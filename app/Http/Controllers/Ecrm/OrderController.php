<?php

namespace App\Http\Controllers\Ecrm;

use App\Http\Controllers\Controller;
use App\Models\Ecrm\Client;
use App\Models\Ecrm\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['client', 'user']);

        if (Auth::user()->role === 'client') {
            $query->where('user_id', Auth::id());
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_order', 'like', '%' . $search . '%')
                  ->orWhere('deskripsi', 'like', '%' . $search . '%')
                  ->orWhereHas('client', function($q) use ($search) {
                      $q->where('nama', 'like', '%' . $search . '%');
                  });
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('jenis_desain')) {
            $query->where('jenis_desain', $request->jenis_desain);
        }

        $orders = $query->latest()->paginate(15);
        $clients = Client::where('status', 'aktif')->get();

        // For CS, add statistics and use CS view
        if (Auth::user()->role === 'cs') {
            $stats = [
                'pending' => Order::where('status', 'pending')->count(),
                'in_progress' => Order::where('status', 'in_progress')->count(),
                'completed' => Order::where('status', 'completed')->count(),
                'total' => Order::count(),
            ];
            
            return view('ecrm.orders.cs-index', compact('orders', 'clients', 'stats'));
        }

        return view('ecrm.orders.index', compact('orders', 'clients'));
    }

    public function create()
    {
        $user = Auth::user();
        
        // Ensure user has role, default to 'client' if null
        if (!$user->role) {
            $user->update(['role' => 'client']);
            $user->refresh();
        }
        
        // Check if user is client (middleware should handle this, but double check)
        if ($user->role !== 'client') {
            abort(403, 'Hanya client yang dapat membuat pesanan. Role Anda: ' . ($user->role ?? 'none'));
        }

        $client = $user->client;
        
        // If client doesn't exist, create one automatically (use firstOrCreate to prevent duplicate email)
        if (!$client) {
            $client = Client::firstOrCreate(
                ['email' => $user->email],
                [
                    'nama' => $user->name,
                    'tipe' => 'individu',
                    'status' => 'aktif',
                ]
            );
            
            // Link client to user
            $user->update(['client_id' => $client->id]);
        }

        return view('ecrm.orders.create', compact('client'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Ensure user has role, default to 'client' if null
        if (!$user->role) {
            $user->update(['role' => 'client']);
            $user->refresh();
        }
        
        if ($user->role !== 'client') {
            abort(403, 'Hanya client yang dapat membuat pesanan. Role Anda: ' . ($user->role ?? 'none'));
        }

        $client = $user->client;
        
        // If client doesn't exist, create one automatically (use firstOrCreate to prevent duplicate email)
        if (!$client) {
            $client = Client::firstOrCreate(
                ['email' => $user->email],
                [
                    'nama' => $user->name,
                    'tipe' => 'individu',
                    'status' => 'aktif',
                ]
            );
            
            // Link client to user
            $user->update(['client_id' => $client->id]);
        }

        $validated = $request->validate([
            'jenis_desain' => 'required|in:logo,branding,web_design,ui_ux,print_design,packaging,social_media,seminar,lainnya',
            'deskripsi' => 'required|string',
            'kebutuhan' => 'nullable|string',
            'deadline' => 'nullable|date',
        ]);

        $validated['client_id'] = $client->id;
        $validated['user_id'] = $user->id;
        $validated['status'] = 'pending';
        $validated['produk_status'] = 'pending';

        $order = Order::create($validated);

        return redirect()->route('ecrm.orders.show', $order)
            ->with('success', 'Pesanan berhasil dibuat. Admin akan meninjau pesanan Anda.');
    }

    public function show(Order $order)
    {
        $user = Auth::user();
        
        // Check access - client can only see their own orders, admin can see all
        if ($user->role === 'client' && $order->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke order ini');
        }
        
        // Admin can access all orders, no restriction needed

        $order->load(['client', 'user', 'chatMessages.user', 'invoices']);
        $unreadCount = $order->chatMessages()
            ->where('user_id', '!=', Auth::id())
            ->where('is_read', false)
            ->count();

        return view('ecrm.orders.show', compact('order', 'unreadCount'));
    }

    public function myOrders()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['client'])
            ->latest()
            ->paginate(15);

        return view('ecrm.orders.my-orders', compact('orders'));
    }

    public function approve(Order $order)
    {
        $order->update([
            'status' => 'approved',
            'catatan_admin' => request('catatan') ?? $order->catatan_admin,
        ]);

        return redirect()->back()
            ->with('success', 'Pesanan berhasil disetujui');
    }

    public function reject(Order $order)
    {
        $order->update([
            'status' => 'cancelled',
            'catatan_admin' => request('catatan') ?? $order->catatan_admin,
        ]);

        return redirect()->back()
            ->with('success', 'Pesanan ditolak');
    }

    public function edit(Order $order)
    {
        $user = Auth::user();
        
        // Client can only edit their own orders
        if ($user->role === 'client' && $order->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke order ini');
        }
        
        // Client can only edit if status is pending
        if ($user->role === 'client' && $order->status !== 'pending') {
            return redirect()->route('ecrm.orders.show', $order)
                ->with('error', 'Pesanan hanya dapat diedit jika status masih pending');
        }
        
        $client = $order->client;
        
        return view('ecrm.orders.edit', compact('order', 'client'));
    }

    public function update(Request $request, Order $order)
    {
        $user = Auth::user();
        
        // Client can only update their own orders
        if ($user->role === 'client') {
            if ($order->user_id !== $user->id) {
                abort(403, 'Anda tidak memiliki akses ke order ini');
            }
            
            // Client can only update if status is pending
            if ($order->status !== 'pending') {
                return redirect()->back()
                    ->with('error', 'Pesanan hanya dapat diubah jika status masih pending');
            }
            
            // Client can only update certain fields
            $validated = $request->validate([
                'jenis_desain' => 'required|in:logo,branding,web_design,ui_ux,print_design,packaging,social_media,seminar,lainnya',
                'deskripsi' => 'required|string',
                'kebutuhan' => 'nullable|string',
                'deadline' => 'nullable|date',
            ]);
        } else {
            // Admin can update all fields
            $validated = $request->validate([
                'produk_status' => 'required|in:pending,proses,selesai',
                'budget' => 'nullable|numeric|min:0',
                'deadline' => 'nullable|date',
                'catatan_admin' => 'nullable|string',
                'desain_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,zip,rar|max:10240', // Max 10MB
            ]);
            
            // Handle file upload
            if ($request->hasFile('desain_file')) {
                // Delete old file if exists
                if ($order->desain_file) {
                    $oldFilePath = public_path('storage/desain/' . $order->desain_file);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
                
                $file = $request->file('desain_file');
                $fileName = time() . '_' . $order->nomor_order . '_' . $file->getClientOriginalName();
                
                // Create directory if not exists
                $uploadPath = public_path('storage/desain');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                $file->move($uploadPath, $fileName);
                $validated['desain_file'] = $fileName;
            }
        }

        $order->update($validated);

        return redirect()->route('ecrm.orders.show', $order)
            ->with('success', 'Pesanan berhasil diperbarui');
    }

    public function uploadDesain(Request $request, Order $order)
    {
        // Only admin can upload desain
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Hanya admin yang dapat mengupload desain');
        }

        $validated = $request->validate([
            'desain_file' => 'required|file|mimes:jpg,jpeg,png,pdf,zip,rar|max:10240', // Max 10MB
        ]);

        // Delete old file if exists
        if ($order->desain_file) {
            $oldFilePath = public_path('storage/desain/' . $order->desain_file);
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }
        }

        // Upload new file
        $file = $request->file('desain_file');
        $fileName = time() . '_' . $order->nomor_order . '_' . $file->getClientOriginalName();
        
        // Create directory if not exists
        $uploadPath = public_path('storage/desain');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        
        $file->move($uploadPath, $fileName);
        
        $order->update([
            'desain_file' => $fileName
        ]);

        return redirect()->back()
            ->with('success', 'Desain berhasil diupload');
    }

    public function updateStatus(Request $request, Order $order)
    {
        // Only admin and CS can update status
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'cs') {
            abort(403, 'Anda tidak memiliki akses untuk mengupdate status pesanan');
        }

        $validated = $request->validate([
            'produk_status' => 'required|in:pending,proses,selesai',
            'catatan_admin' => 'nullable|string',
        ]);

        $order->update($validated);

        return redirect()->route('ecrm.orders.show', $order)
            ->with('success', 'Status produk berhasil diperbarui');
    }

    public function destroy(Order $order)
    {
        $user = Auth::user();
        
        // Client can only delete their own orders
        if ($user->role === 'client') {
            if ($order->user_id !== $user->id) {
                abort(403, 'Anda tidak memiliki akses ke order ini');
            }
            
            // Client can only delete if status is pending
            if ($order->status !== 'pending') {
                return redirect()->back()
                    ->with('error', 'Pesanan hanya dapat dihapus jika status masih pending');
            }
            
            // Client cannot delete if order has invoices
            if ($order->invoices()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Pesanan tidak dapat dihapus karena sudah memiliki invoice');
            }
        }
        
        // Admin can delete any order, including completed orders with invoices
        // Delete related invoices and payments first if needed
        if ($user->role === 'admin') {
            // Load invoices with payments
            $order->load('invoices.payments');
            
            // Delete related payments first
            foreach ($order->invoices as $invoice) {
                $invoice->payments()->delete();
            }
            // Delete related invoices
            $order->invoices()->delete();
            // Delete related chat messages
            $order->chatMessages()->delete();
        } else {
            // For non-admin, check if order has invoices
            if ($order->invoices()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Pesanan tidak dapat dihapus karena sudah memiliki invoice');
            }
        }
        
        $order->delete();

        // Redirect based on user role
        if ($user->role === 'admin') {
            return redirect()->route('ecrm.orders.index')
                ->with('success', 'Pesanan berhasil dihapus');
        }

        return redirect()->route('ecrm.orders.my')
            ->with('success', 'Pesanan berhasil dihapus');
    }
}


