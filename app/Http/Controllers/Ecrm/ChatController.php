<?php

namespace App\Http\Controllers\Ecrm;

use App\Http\Controllers\Controller;
use App\Models\Ecrm\ChatMessage;
use App\Models\Ecrm\ChatSession;
use App\Models\Ecrm\Order;
use App\Models\Ecrm\QuickReply;
use App\Services\ChatLoadBalancerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    protected $loadBalancer;

    public function __construct(ChatLoadBalancerService $loadBalancer)
    {
        $this->loadBalancer = $loadBalancer;
    }

    public function index(Order $order, Request $request)
    {
        // Check access
        if (Auth::user()->role === 'client' && $order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['client', 'user']);
        
        // Auto-assign agent to customer if customer opens chat for the first time
        if (Auth::user()->role === 'client') {
            $assignedAgent = $this->loadBalancer->assignAgentToCustomer($order->id, Auth::id());
            
            // If no agent assigned, try to get existing assignment
            if (!$assignedAgent) {
                $assignedAgent = $this->loadBalancer->getAssignedAgent($order->id, Auth::id());
            }
        }

        $messages = ChatMessage::where('order_id', $order->id)
            ->with(['user', 'quickReply'])
            ->latest()
            ->paginate(50);

        $quickReplies = QuickReply::where('aktif', true)
            ->orderBy('order')
            ->get();

        // Mark messages as read
        ChatMessage::where('order_id', $order->id)
            ->where('user_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Check for new messages (AJAX request)
        if ($request->has('check_new') && $request->check_new) {
            $lastId = $request->get('last_id', 0);
            $newMessages = ChatMessage::where('order_id', $order->id)
                ->where('id', '>', $lastId)
                ->where('user_id', '!=', Auth::id())
                ->exists();
            
            return response()->json([
                'has_new' => $newMessages,
                'messages' => $newMessages ? ChatMessage::where('order_id', $order->id)
                    ->where('id', '>', $lastId)
                    ->with(['user', 'quickReply'])
                    ->latest()
                    ->get()
                    ->map(function($msg) {
                        return [
                            'id' => $msg->id,
                            'user' => $msg->user->name,
                            'role' => $msg->user->role,
                            'pesan' => $msg->pesan,
                            'created_at' => $msg->created_at->format('d M Y, H:i'),
                            'is_ai_generated' => $msg->is_ai_generated,
                            'quick_reply' => $msg->quickReply ? $msg->quickReply->pertanyaan : null,
                        ];
                    }) : []
            ]);
        }

        // Get assigned agent info for display
        $assignedAgent = null;
        if (Auth::user()->role === 'client') {
            $session = ChatSession::where('order_id', $order->id)
                ->where('customer_id', Auth::id())
                ->whereIn('status', ['waiting', 'active'])
                ->with('agent')
                ->first();
            
            $assignedAgent = $session?->agent;
        }

        return view('ecrm.chat.index', compact('order', 'messages', 'quickReplies', 'assignedAgent'));
    }

    public function send(Request $request, Order $order)
    {
        // Check access
        if (Auth::user()->role === 'client' && $order->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'pesan' => 'required|string',
        ]);

        ChatMessage::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'pesan' => $validated['pesan'],
            'is_read' => false,
        ]);

        return redirect()->back()
            ->with('success', 'Pesan berhasil dikirim');
    }

    public function quickReply(Request $request, Order $order)
    {
        // Check access
        if (Auth::user()->role === 'client' && $order->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'quick_reply_id' => 'required|exists:ecrm_quick_replies,id',
        ]);

        $quickReply = QuickReply::findOrFail($validated['quick_reply_id']);

        $message = ChatMessage::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'pesan' => $quickReply->jawaban,
            'quick_reply_id' => $quickReply->id,
            'is_read' => false,
        ]);

        return redirect()->back()
            ->with('success', 'Pesan berhasil dikirim');
    }

    public function aiAnswer(Request $request, Order $order)
    {
        // Check access
        if (Auth::user()->role === 'client' && $order->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'pertanyaan' => 'required|string',
        ]);

        $aiResponse = $this->generateAIResponse($validated['pertanyaan'], $order);

        ChatMessage::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'pesan' => $aiResponse,
            'is_ai_generated' => true,
            'is_read' => false,
        ]);

        return redirect()->back()
            ->with('success', 'Jawaban AI berhasil dikirim');
    }

    private function generateAIResponse(string $pertanyaan, Order $order): string
    {
        try {
            // Setup Gemini API
            $apiKey = env('GEMINI_API_KEY');
            
            if (!$apiKey) {
                return "Maaf, fitur AI belum dikonfigurasi. Silakan hubungi admin.";
            }

            // Context tentang order
            $context = "Anda adalah asisten untuk jasa desain. ";
            $context .= "Client memesan: {$order->jenis_desain}. ";
            $context .= "Deskripsi: {$order->deskripsi}. ";
            if ($order->kebutuhan) {
                $context .= "Kebutuhan: {$order->kebutuhan}. ";
            }
            $context .= "Jawab pertanyaan dengan ramah dan profesional dalam bahasa Indonesia.";

            // Call Gemini API
            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $context . "\n\nPertanyaan: " . $pertanyaan
                            ]
                        ]
                    ]
                ]
            ]);

            $data = $response->json();
            
            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                return $data['candidates'][0]['content']['parts'][0]['text'];
            }

            return "Maaf, terjadi kesalahan saat memproses pertanyaan. Silakan coba lagi.";
        } catch (\Exception $e) {
            \Log::error('Gemini API Error: ' . $e->getMessage());
            return "Maaf, terjadi kesalahan saat menghubungi AI. Silakan coba lagi atau hubungi admin.";
        }
    }

    public function markRead(ChatMessage $message)
    {
        if ($message->user_id === Auth::id()) {
            abort(403);
        }

        $message->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Show all chats for customer
     */
    public function myChats(Request $request)
    {
        // Only for client role
        if (Auth::user()->role !== 'client') {
            abort(403);
        }

        // Build query
        $query = Order::where('user_id', Auth::id())
            ->with(['client'])
            ->withCount(['chatMessages as unread_count' => function($q) {
                $q->where('user_id', '!=', Auth::id())
                  ->where('is_read', false);
            }])
            ->withCount(['chatMessages as total_messages']);

        // Filter by search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_order', 'like', '%' . $search . '%')
                  ->orWhere('jenis_desain', 'like', '%' . $search . '%')
                  ->orWhereHas('client', function($q) use ($search) {
                      $q->where('nama', 'like', '%' . $search . '%');
                  });
            });
        }

        $orders = $query->latest()->get();

        // Load last message for each order
        foreach ($orders as $order) {
            $order->last_message = ChatMessage::where('order_id', $order->id)
                ->with('user')
                ->latest()
                ->first();
        }

        // Calculate statistics (before filtering)
        $allOrders = Order::where('user_id', Auth::id())->get();
        $stats = [
            'total_chats' => $allOrders->count(),
            'unread_messages' => ChatMessage::whereHas('order', function($q) {
                $q->where('user_id', Auth::id());
            })
            ->where('user_id', '!=', Auth::id())
            ->where('is_read', false)
            ->count(),
            'total_messages' => ChatMessage::whereHas('order', function($q) {
                $q->where('user_id', Auth::id());
            })->count(),
        ];

        return view('ecrm.chat.my-chats', compact('orders', 'stats'));
    }
}

