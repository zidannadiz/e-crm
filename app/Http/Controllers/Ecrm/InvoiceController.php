<?php

namespace App\Http\Controllers\Ecrm;

use App\Http\Controllers\Controller;
use App\Models\Ecrm\Client;
use App\Models\Ecrm\Invoice;
use App\Models\Ecrm\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['client', 'order']);

        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $invoices = $query->latest('tanggal_invoice')->paginate(15);
        $clients = Client::where('status', 'aktif')->get();

        return view('ecrm.invoices.index', compact('invoices', 'clients'));
    }

    public function create(Request $request)
    {
        $orders = Order::whereIn('status', ['approved', 'in_progress', 'review'])
            ->with('client')
            ->get();
        $clients = Client::where('status', 'aktif')->get();
        $orderId = $request->order_id;

        return view('ecrm.invoices.create', compact('orders', 'clients', 'orderId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:ecrm_orders,id',
            'client_id' => 'required|exists:ecrm_clients,id',
            'tanggal_invoice' => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date|after_or_equal:tanggal_invoice',
            'subtotal' => 'required|numeric|min:0',
            'pajak' => 'nullable|numeric|min:0',
            'diskon' => 'nullable|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        // Calculate total
        $subtotal = $validated['subtotal'];
        $pajak = $validated['pajak'] ?? 0;
        $diskon = $validated['diskon'] ?? 0;
        $total = ($subtotal + $pajak) - $diskon;

        $validated['total'] = $total;
        $validated['status'] = 'draft';

        $invoice = Invoice::create($validated);

        return redirect()->route('ecrm.invoices.show', $invoice)
            ->with('success', 'Invoice berhasil dibuat');
    }

    public function show(Invoice $invoice)
    {
        $user = Auth::user();
        
        // Check access - client can only see their own invoices, admin can see all
        if ($user->role === 'client') {
            // Check if invoice belongs to this user's orders
            if ($invoice->order->user_id !== $user->id) {
                abort(403, 'Anda tidak memiliki akses ke invoice ini');
            }
        }
        
        $invoice->load(['client', 'order', 'payments.verifiedBy']);
        return view('ecrm.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $orders = Order::whereIn('status', ['approved', 'in_progress', 'review'])
            ->with('client')
            ->get();
        $clients = Client::where('status', 'aktif')->get();

        return view('ecrm.invoices.edit', compact('invoice', 'orders', 'clients'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:ecrm_orders,id',
            'client_id' => 'required|exists:ecrm_clients,id',
            'tanggal_invoice' => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date|after_or_equal:tanggal_invoice',
            'subtotal' => 'required|numeric|min:0',
            'pajak' => 'nullable|numeric|min:0',
            'diskon' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,sent,paid,overdue,cancelled',
            'deskripsi' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        // Calculate total
        $subtotal = $validated['subtotal'];
        $pajak = $validated['pajak'] ?? 0;
        $diskon = $validated['diskon'] ?? 0;
        $total = ($subtotal + $pajak) - $diskon;

        $validated['total'] = $total;

        $invoice->update($validated);

        return redirect()->route('ecrm.invoices.show', $invoice)
            ->with('success', 'Invoice berhasil diperbarui');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()->route('ecrm.invoices.index')
            ->with('success', 'Invoice berhasil dihapus');
    }

    public function send(Invoice $invoice)
    {
        $invoice->update(['status' => 'sent']);

        return redirect()->back()
            ->with('success', 'Invoice berhasil dikirim');
    }

    public function sendReminder(Invoice $invoice)
    {
        // Only admin and CS can send reminders
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'cs') {
            abort(403, 'Anda tidak memiliki akses untuk mengirim reminder');
        }

        // Update status to sent if still draft
        if ($invoice->status === 'draft') {
            $invoice->update(['status' => 'sent']);
        }

        // TODO: Here you can add email notification to client
        // Mail::to($invoice->client->email)->send(new InvoiceReminderMail($invoice));

        return redirect()->back()
            ->with('success', 'Reminder pembayaran berhasil dikirim ke client');
    }

    public function myInvoices(Request $request)
    {
        $user = Auth::user();
        
        // Get invoices for orders belonging to this user
        $query = Invoice::with(['client', 'order', 'payments'])
            ->whereHas('order', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_invoice', 'like', '%' . $search . '%')
                  ->orWhereHas('order', function($q) use ($search) {
                      $q->where('nomor_order', 'like', '%' . $search . '%')
                        ->orWhere('deskripsi', 'like', '%' . $search . '%');
                  });
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->latest('tanggal_invoice')->paginate(15);

        return view('ecrm.invoices.my-invoices', compact('invoices'));
    }
}

