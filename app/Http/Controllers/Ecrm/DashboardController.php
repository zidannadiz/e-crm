<?php

namespace App\Http\Controllers\Ecrm;

use App\Http\Controllers\Controller;
use App\Models\Ecrm\Client;
use App\Models\Ecrm\Order;
use App\Models\Ecrm\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'client') {
            // Client Dashboard
            $stats = [
                'total_orders' => Order::where('user_id', $user->id)->count(),
                'pending_orders' => Order::where('user_id', $user->id)->where('status', 'pending')->count(),
                'active_orders' => Order::where('user_id', $user->id)->whereIn('status', ['approved', 'in_progress', 'review'])->count(),
                'completed_orders' => Order::where('user_id', $user->id)->where('status', 'completed')->count(),
            ];

            $recent_orders = Order::where('user_id', $user->id)
                ->with('client')
                ->latest()
                ->limit(5)
                ->get();

            $unread_messages = \App\Models\Ecrm\ChatMessage::whereHas('order', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->where('user_id', '!=', $user->id)
            ->where('is_read', false)
            ->count();

            return view('ecrm.dashboard.client', compact('stats', 'recent_orders', 'unread_messages'));
        } elseif ($user->role === 'cs') {
            // Customer Service Dashboard
            $stats = [
                'unread_messages' => \App\Models\Ecrm\ChatMessage::where('user_id', '!=', $user->id)
                    ->where('is_read', false)
                    ->count(),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'active_orders' => Order::whereIn('status', ['approved', 'in_progress', 'review'])->count(),
                'today_orders' => Order::whereDate('created_at', today())->count(),
            ];

            $recent_chats = \App\Models\Ecrm\ChatMessage::with(['order.client', 'user'])
                ->where('is_read', false)
                ->where('user_id', '!=', $user->id)
                ->latest()
                ->limit(10)
                ->get();

            $pending_orders = Order::with('client', 'user')
                ->where('status', 'pending')
                ->latest()
                ->limit(5)
                ->get();

            return view('ecrm.dashboard.cs', compact('stats', 'recent_chats', 'pending_orders'));
        } else {
            // Admin Dashboard
            $stats = [
                'total_clients' => Client::count(),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'active_orders' => Order::whereIn('status', ['approved', 'in_progress', 'review'])->count(),
                'total_revenue' => Invoice::where('status', 'paid')->sum('total') ?? 0,
            ];

            $recent_orders = Order::with('client', 'user')
                ->latest()
                ->limit(5)
                ->get();

            $recent_invoices = Invoice::with('client', 'order')
                ->latest()
                ->limit(5)
                ->get();

            return view('ecrm.dashboard.index', compact('stats', 'recent_orders', 'recent_invoices'));
        }
    }
}

