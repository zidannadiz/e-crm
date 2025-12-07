<?php

namespace App\Http\Controllers\Ecrm;

use App\Http\Controllers\Controller;
use App\Models\Ecrm\Invoice;
use App\Models\Ecrm\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['invoice.client', 'invoice.order', 'verifiedBy']);

        if ($request->has('invoice_id')) {
            $query->where('invoice_id', $request->invoice_id);
        }

        $payments = $query->latest('tanggal_pembayaran')->paginate(15);
        $invoices = Invoice::where('status', '!=', 'paid')->get();

        return view('ecrm.payments.index', compact('payments', 'invoices'));
    }

    public function create(Request $request)
    {
        $invoices = Invoice::where('status', '!=', 'cancelled')
            ->with(['client', 'order'])
            ->get();
        $invoiceId = $request->invoice_id;

        return view('ecrm.payments.create', compact('invoices', 'invoiceId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:ecrm_invoices,id',
            'jumlah' => 'required|numeric|min:0',
            'tanggal_pembayaran' => 'required|date',
            'metode_pembayaran' => 'required|in:transfer,cash,kartu_kredit,e_wallet,lainnya',
            'bukti_pembayaran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'catatan' => 'nullable|string',
        ]);

        if ($request->hasFile('bukti_pembayaran')) {
            $validated['bukti_pembayaran'] = $request->file('bukti_pembayaran')->store('payments', 'public');
        }

        $validated['status'] = 'pending';

        $payment = Payment::create($validated);

        // Update invoice status if fully paid
        $invoice = Invoice::find($validated['invoice_id']);
        if ($invoice->remaining_amount <= 0) {
            $invoice->update(['status' => 'paid']);
        }

        return redirect()->route('ecrm.payments.show', $payment)
            ->with('success', 'Pembayaran berhasil ditambahkan');
    }

    public function show(Payment $payment)
    {
        $payment->load(['invoice.client', 'invoice.order', 'verifiedBy']);
        return view('ecrm.payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        $invoices = Invoice::where('status', '!=', 'cancelled')
            ->with(['client', 'order'])
            ->get();

        return view('ecrm.payments.edit', compact('payment', 'invoices'));
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:ecrm_invoices,id',
            'jumlah' => 'required|numeric|min:0',
            'tanggal_pembayaran' => 'required|date',
            'metode_pembayaran' => 'required|in:transfer,cash,kartu_kredit,e_wallet,lainnya',
            'bukti_pembayaran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'catatan' => 'nullable|string',
        ]);

        if ($request->hasFile('bukti_pembayaran')) {
            // Delete old file
            if ($payment->bukti_pembayaran) {
                Storage::disk('public')->delete($payment->bukti_pembayaran);
            }
            $validated['bukti_pembayaran'] = $request->file('bukti_pembayaran')->store('payments', 'public');
        }

        $payment->update($validated);

        return redirect()->route('ecrm.payments.show', $payment)
            ->with('success', 'Pembayaran berhasil diperbarui');
    }

    public function destroy(Payment $payment)
    {
        // Delete file if exists
        if ($payment->bukti_pembayaran) {
            Storage::disk('public')->delete($payment->bukti_pembayaran);
        }

        $payment->delete();

        return redirect()->route('ecrm.payments.index')
            ->with('success', 'Pembayaran berhasil dihapus');
    }

    public function verify(Payment $payment)
    {
        $payment->update([
            'status' => 'verified',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        // Update invoice status
        $invoice = $payment->invoice;
        if ($invoice->remaining_amount <= 0) {
            $invoice->update(['status' => 'paid']);
        }

        return redirect()->back()
            ->with('success', 'Pembayaran berhasil diverifikasi');
    }

    public function reject(Payment $payment)
    {
        $payment->update([
            'status' => 'rejected',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Pembayaran ditolak');
    }
}

