<?php

namespace App\Services;

use App\Models\Ecrm\ChatSession;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChatLoadBalancerService
{
    /**
     * Assign agent to customer using load balancing algorithm
     * 
     * @param int $orderId
     * @param int $customerId
     * @return User|null The assigned agent (admin or cs)
     */
    public function assignAgentToCustomer(int $orderId, int $customerId): ?User
    {
        // Use database transaction with locking to prevent race condition
        return DB::transaction(function () use ($orderId, $customerId) {
            // Check if session already exists for this order
            $existingSession = ChatSession::where('order_id', $orderId)
                ->where('customer_id', $customerId)
                ->whereIn('status', ['waiting', 'active'])
                ->lockForUpdate()
                ->first();

            if ($existingSession && $existingSession->agent_id) {
                // Return existing agent
                return User::find($existingSession->agent_id);
            }

            // Get all available agents (admin and cs)
            $availableAgents = User::whereIn('role', ['admin', 'cs'])
                ->whereNotNull('email_verified_at')
                ->lockForUpdate()
                ->get();

            if ($availableAgents->isEmpty()) {
                Log::warning('No available agents for load balancing', [
                    'order_id' => $orderId,
                    'customer_id' => $customerId
                ]);
                return null;
            }

            // Calculate active customer count for each agent
            $agentLoads = $this->calculateAgentLoads($availableAgents->pluck('id')->toArray());

            // Find agent with minimum load
            $selectedAgent = $this->selectAgentWithMinLoad($availableAgents, $agentLoads);

            if (!$selectedAgent) {
                Log::error('Failed to select agent for load balancing', [
                    'order_id' => $orderId,
                    'customer_id' => $customerId
                ]);
                return null;
            }

            // Create or update chat session
            $session = ChatSession::updateOrCreate(
                [
                    'order_id' => $orderId,
                    'customer_id' => $customerId,
                ],
                [
                    'agent_id' => $selectedAgent->id,
                    'status' => 'active',
                    'assigned_at' => now(),
                ]
            );

            Log::info('Agent assigned to customer', [
                'order_id' => $orderId,
                'customer_id' => $customerId,
                'agent_id' => $selectedAgent->id,
                'agent_name' => $selectedAgent->name,
                'agent_load' => $agentLoads[$selectedAgent->id] ?? 0,
            ]);

            return $selectedAgent;
        });
    }

    /**
     * Calculate active customer count for each agent
     * 
     * @param array $agentIds
     * @return array [agent_id => active_customer_count]
     */
    protected function calculateAgentLoads(array $agentIds): array
    {
        // Count active sessions per agent using single optimized query
        $loads = ChatSession::whereIn('agent_id', $agentIds)
            ->where('status', 'active')
            ->select('agent_id', DB::raw('COUNT(*) as active_count'))
            ->groupBy('agent_id')
            ->pluck('active_count', 'agent_id')
            ->toArray();

        // Initialize all agents with 0 if they have no active sessions
        $result = [];
        foreach ($agentIds as $agentId) {
            $result[$agentId] = $loads[$agentId] ?? 0;
        }

        return $result;
    }

    /**
     * Select agent with minimum load
     * If multiple agents have same load, select the one with smallest ID (first registered)
     * 
     * @param \Illuminate\Database\Eloquent\Collection $agents
     * @param array $agentLoads
     * @return User|null
     */
    protected function selectAgentWithMinLoad($agents, array $agentLoads): ?User
    {
        if ($agents->isEmpty()) {
            return null;
        }

        // Find minimum load
        $minLoad = min($agentLoads);

        // Get all agents with minimum load
        $candidates = $agents->filter(function ($agent) use ($agentLoads, $minLoad) {
            return ($agentLoads[$agent->id] ?? 0) === $minLoad;
        });

        if ($candidates->isEmpty()) {
            return null;
        }

        // If multiple candidates, select the one with smallest ID (fallback logic)
        return $candidates->sortBy('id')->first();
    }

    /**
     * End chat session and update agent load
     * 
     * @param int $orderId
     * @param int $customerId
     * @return bool
     */
    public function endChatSession(int $orderId, int $customerId): bool
    {
        return DB::transaction(function () use ($orderId, $customerId) {
            $session = ChatSession::where('order_id', $orderId)
                ->where('customer_id', $customerId)
                ->where('status', 'active')
                ->lockForUpdate()
                ->first();

            if (!$session) {
                return false;
            }

            $session->update([
                'status' => 'ended',
                'ended_at' => now(),
            ]);

            Log::info('Chat session ended', [
                'order_id' => $orderId,
                'customer_id' => $customerId,
                'agent_id' => $session->agent_id,
            ]);

            return true;
        });
    }

    /**
     * Get current load statistics for all agents
     * 
     * @return array
     */
    public function getLoadStatistics(): array
    {
        $agents = User::whereIn('role', ['admin', 'cs'])
            ->whereNotNull('email_verified_at')
            ->get();

        $agentLoads = $this->calculateAgentLoads($agents->pluck('id')->toArray());

        $statistics = [];
        foreach ($agents as $agent) {
            $statistics[] = [
                'agent_id' => $agent->id,
                'agent_name' => $agent->name,
                'agent_email' => $agent->email,
                'agent_role' => $agent->role,
                'active_customers' => $agentLoads[$agent->id] ?? 0,
            ];
        }

        // Sort by active customers (ascending) then by ID (ascending)
        usort($statistics, function ($a, $b) {
            if ($a['active_customers'] === $b['active_customers']) {
                return $a['agent_id'] <=> $b['agent_id'];
            }
            return $a['active_customers'] <=> $b['active_customers'];
        });

        return $statistics;
    }

    /**
     * Reassign customer to a different agent (if needed)
     * 
     * @param int $orderId
     * @param int $customerId
     * @return User|null
     */
    public function reassignAgent(int $orderId, int $customerId): ?User
    {
        // End current session
        $this->endChatSession($orderId, $customerId);

        // Assign to new agent
        return $this->assignAgentToCustomer($orderId, $customerId);
    }

    /**
     * Get agent for existing session
     * 
     * @param int $orderId
     * @param int $customerId
     * @return User|null
     */
    public function getAssignedAgent(int $orderId, int $customerId): ?User
    {
        $session = ChatSession::where('order_id', $orderId)
            ->where('customer_id', $customerId)
            ->whereIn('status', ['waiting', 'active'])
            ->first();

        if (!$session || !$session->agent_id) {
            return null;
        }

        return User::find($session->agent_id);
    }
}

