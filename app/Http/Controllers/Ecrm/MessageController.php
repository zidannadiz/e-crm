<?php

namespace App\Http\Controllers\Ecrm;

use App\Http\Controllers\Controller;
use App\Models\Ecrm\ChatMessage;
use App\Models\Ecrm\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Display inbox for CS and Admin
     * Grouped by order (conversation) instead of individual messages
     */
    public function inbox(Request $request)
    {
        // Get all orders that have messages
        $query = Order::with(['client', 'user'])
            ->whereHas('chatMessages', function($q) {
                if (Auth::user()->role === 'cs') {
                    // CS only sees orders with messages not from themselves
                    $q->where('user_id', '!=', Auth::id());
                }
                // Admin can see all orders with messages
            });

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_order', 'like', '%' . $search . '%')
                  ->orWhere('jenis_desain', 'like', '%' . $search . '%')
                  ->orWhereHas('client', function($q) use ($search) {
                      $q->where('nama', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('chatMessages', function($q) use ($search) {
                      $q->where('pesan', 'like', '%' . $search . '%');
                  });
            });
        }

        // Filter by unread status (before getting counts)
        if ($request->has('status')) {
            if ($request->status === 'unread') {
                $query->whereHas('chatMessages', function($q) {
                    if (Auth::user()->role === 'cs') {
                        $q->where('user_id', '!=', Auth::id())
                          ->where('is_read', false);
                    } else {
                        $q->where('user_id', '!=', Auth::id())
                          ->where('is_read', false);
                    }
                });
            } elseif ($request->status === 'read') {
                // For read, we need to check that there are no unread messages
                $query->whereDoesntHave('chatMessages', function($q) {
                    if (Auth::user()->role === 'cs') {
                        $q->where('user_id', '!=', Auth::id())
                          ->where('is_read', false);
                    } else {
                        $q->where('user_id', '!=', Auth::id())
                          ->where('is_read', false);
                    }
                });
            }
        }

        // Get orders with message counts
        $query->withCount(['chatMessages as unread_count' => function($q) {
            if (Auth::user()->role === 'cs') {
                $q->where('user_id', '!=', Auth::id())
                  ->where('is_read', false);
            } else {
                // Admin: count unread messages not from themselves
                $q->where('user_id', '!=', Auth::id())
                  ->where('is_read', false);
            }
        }])
        ->withCount(['chatMessages as total_messages' => function($q) {
            if (Auth::user()->role === 'cs') {
                $q->where('user_id', '!=', Auth::id());
            }
        }]);

        $orders = $query->latest()->get();

        // Load last message for each order
        foreach ($orders as $order) {
            $lastMessageQuery = ChatMessage::where('order_id', $order->id);
            
            if (Auth::user()->role === 'cs') {
                $lastMessageQuery->where('user_id', '!=', Auth::id());
            }
            
            $order->last_message = $lastMessageQuery
                ->with('user')
                ->latest()
                ->first();
        }

        // Filter out orders without last message (shouldn't happen, but safety check)
        $orders = $orders->filter(function($order) {
            return $order->last_message !== null;
        });

        // Statistics
        $statsQuery = ChatMessage::query();
        if (Auth::user()->role === 'cs') {
            $statsQuery->where('user_id', '!=', Auth::id());
        }
        
        $stats = [
            'total' => Order::whereHas('chatMessages', function($q) {
                if (Auth::user()->role === 'cs') {
                    $q->where('user_id', '!=', Auth::id());
                }
            })->count(),
            'unread' => (clone $statsQuery)->where('is_read', false)->where('user_id', '!=', Auth::id())->count(),
            'today' => (clone $statsQuery)->whereDate('created_at', today())->count(),
        ];

        return view('ecrm.messages.inbox', compact('orders', 'stats'));
    }

    /**
     * Mark message as read
     */
    public function markAsRead(ChatMessage $message)
    {
        $message->update(['is_read' => true]);

        return back()->with('success', 'Pesan ditandai sudah dibaca');
    }

    /**
     * Mark all as read
     */
    public function markAllAsRead()
    {
        $query = ChatMessage::where('is_read', false);
        
        if (Auth::user()->role === 'cs') {
            $query->where('user_id', '!=', Auth::id());
        }
        
        $query->update(['is_read' => true]);

        return back()->with('success', 'Semua pesan ditandai sudah dibaca');
    }
}

