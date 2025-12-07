<?php

namespace App\Http\Controllers\Ecrm;

use App\Http\Controllers\Controller;
use App\Services\ChatLoadBalancerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatLoadBalancerController extends Controller
{
    protected $loadBalancer;

    public function __construct(ChatLoadBalancerService $loadBalancer)
    {
        $this->loadBalancer = $loadBalancer;
    }

    /**
     * Get load statistics for all agents
     * Only accessible by admin
     */
    public function getLoadStatistics()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Hanya admin yang dapat melihat statistik load balancing');
        }

        $statistics = $this->loadBalancer->getLoadStatistics();

        return response()->json([
            'success' => true,
            'data' => $statistics,
            'summary' => [
                'total_agents' => count($statistics),
                'total_active_customers' => array_sum(array_column($statistics, 'active_customers')),
                'average_load' => count($statistics) > 0 
                    ? round(array_sum(array_column($statistics, 'active_customers')) / count($statistics), 2)
                    : 0,
                'min_load' => count($statistics) > 0 ? min(array_column($statistics, 'active_customers')) : 0,
                'max_load' => count($statistics) > 0 ? max(array_column($statistics, 'active_customers')) : 0,
            ]
        ]);
    }

    /**
     * End chat session
     * Accessible by admin, cs, and customer (for their own sessions)
     */
    public function endSession(Request $request, $orderId)
    {
        $customerId = $request->input('customer_id', Auth::id());

        // Check access
        if (Auth::user()->role === 'client' && $customerId !== Auth::id()) {
            abort(403, 'Anda tidak dapat mengakhiri sesi chat orang lain');
        }

        $result = $this->loadBalancer->endChatSession($orderId, $customerId);

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Sesi chat berhasil diakhiri'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengakhiri sesi chat'
        ], 400);
    }

    /**
     * Reassign agent to customer
     * Only accessible by admin
     */
    public function reassignAgent(Request $request, $orderId)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Hanya admin yang dapat melakukan reassign agent');
        }

        $customerId = $request->input('customer_id');
        
        if (!$customerId) {
            return response()->json([
                'success' => false,
                'message' => 'Customer ID diperlukan'
            ], 400);
        }

        $agent = $this->loadBalancer->reassignAgent($orderId, $customerId);

        if ($agent) {
            return response()->json([
                'success' => true,
                'message' => 'Agent berhasil di-reassign',
                'agent' => [
                    'id' => $agent->id,
                    'name' => $agent->name,
                    'email' => $agent->email,
                    'role' => $agent->role,
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal melakukan reassign agent'
        ], 400);
    }
}

