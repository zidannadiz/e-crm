<?php

namespace App\Http\Controllers\Ecrm;

use App\Http\Controllers\Controller;
use App\Models\Ecrm\QuickReply;
use Illuminate\Http\Request;

class QuickReplyController extends Controller
{
    public function index()
    {
        $quickReplies = QuickReply::orderBy('order')->latest()->paginate(15);
        return view('ecrm.quick-replies.index', compact('quickReplies'));
    }

    public function create()
    {
        return view('ecrm.quick-replies.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pertanyaan' => 'required|string|max:255',
            'jawaban' => 'required|string',
            'kategori' => 'nullable|string|max:255',
            'use_ai' => 'boolean',
            'order' => 'nullable|integer',
            'aktif' => 'boolean',
        ]);

        $validated['use_ai'] = $request->has('use_ai');
        $validated['aktif'] = $request->has('aktif') ? true : ($request->has('aktif') === false ? false : true);

        QuickReply::create($validated);

        return redirect()->route('ecrm.quick-replies.index')
            ->with('success', 'Quick Reply berhasil ditambahkan');
    }

    public function edit(QuickReply $quickReply)
    {
        return view('ecrm.quick-replies.edit', compact('quickReply'));
    }

    public function update(Request $request, QuickReply $quickReply)
    {
        $validated = $request->validate([
            'pertanyaan' => 'required|string|max:255',
            'jawaban' => 'required|string',
            'kategori' => 'nullable|string|max:255',
            'use_ai' => 'boolean',
            'order' => 'nullable|integer',
            'aktif' => 'boolean',
        ]);

        $validated['use_ai'] = $request->has('use_ai');
        $validated['aktif'] = $request->has('aktif');

        $quickReply->update($validated);

        return redirect()->route('ecrm.quick-replies.index')
            ->with('success', 'Quick Reply berhasil diperbarui');
    }

    public function destroy(QuickReply $quickReply)
    {
        $quickReply->delete();

        return redirect()->route('ecrm.quick-replies.index')
            ->with('success', 'Quick Reply berhasil dihapus');
    }
}

