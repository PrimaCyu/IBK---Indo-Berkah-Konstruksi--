<?php

namespace App\Http\Controllers;

use App\Models\SuratPerjanjian;
use Illuminate\Http\Request;

class SuratPerjanjianController extends Controller
{
    public function index()
    {
        return view('frontend.surat-perjanjian');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'        => 'required|string|max:255',
            'email'       => 'nullable|email|max:255',
            'telepon'     => 'nullable|string|max:20',
            'keterangan'  => 'nullable|string',
            'file_surat'  => 'required|file|mimes:pdf,doc,docx|max:2048',
        ], [
            'file_surat.required' => 'File surat perjanjian wajib diunggah.',
            'file_surat.mimes'    => 'Format file harus PDF, DOC, atau DOCX.',
            'file_surat.max'      => 'Ukuran file maksimal 2 MB.',
        ]);

        $path = $request->file('file_surat')->store('surat-perjanjian', 'public');

        SuratPerjanjian::create([
            'nama'       => $request->nama,
            'email'      => $request->email,
            'telepon'    => $request->telepon,
            'keterangan' => $request->keterangan,
            'file_surat' => $path,
            'status'     => 'pending',
        ]);

        return back()->with('success', 'Surat perjanjian berhasil dikirim. Tim kami akan segera meninjau dokumen Anda.');
    }
}
