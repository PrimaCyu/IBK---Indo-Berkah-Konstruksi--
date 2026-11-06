@extends('admin.layouts.app')

@section('title', 'Masukan Lapangan Klien')
@section('page-title', 'Catatan & Masukan Lapangan')
@section('page-subtitle', 'Kelola feedback dan catatan yang dikirim oleh klien melalui portal dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
    <li class="breadcrumb-item active">Masukan Lapangan</li>
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold">
            <i class="bi bi-chat-left-dots-fill text-warning me-2"></i> Daftar Masukan & Catatan Klien ({{ $feedbacks->total() }})
        </h5>
        <span class="badge badge-gold px-3 py-2 rounded-pill">Total: {{ $feedbacks->total() }} Catatan</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-modern align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Klien</th>
                        <th>Proyek Terkait</th>
                        <th>Judul Masukan</th>
                        <th>Isi Catatan</th>
                        <th>Status</th>
                        <th>Waktu Kirim</th>
                        <th class="text-end" style="width: 160px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($feedbacks as $index => $item)
                        <tr>
                            <td class="text-muted">{{ $feedbacks->firstItem() + $index }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $item->user->name ?? 'Klien' }}</div>
                                <small class="text-muted">{{ $item->user->email ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $item->project->nama_proyek ?? 'Proyek Umum' }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $item->title ?? 'Catatan Lapangan' }}</span>
                            </td>
                            <td>
                                <div class="small text-secondary" style="max-width: 300px; max-height: 60px; overflow: hidden;">
                                    {!! $item->content !!}
                                </div>
                            </td>
                            <td>
                                @if($item->status === 'selesai')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">Selesai</span>
                                @elseif($item->status === 'diproses')
                                    <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3 py-1">Diproses</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1">Pending</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <form action="/admin/feedbacks/{{ $item->id }}/status" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        @if($item->status === 'pending')
                                            <input type="hidden" name="status" value="diproses">
                                            <button type="submit" class="btn btn-sm btn-outline-info rounded-pill px-2" title="Tandai Sedang Diproses">
                                                <i class="bi bi-clock-history"></i> Proses
                                            </button>
                                        @elseif($item->status === 'diproses')
                                            <input type="hidden" name="status" value="selesai">
                                            <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-2" title="Tandai Selesai">
                                                <i class="bi bi-check2-circle"></i> Selesai
                                            </button>
                                        @else
                                            <input type="hidden" name="status" value="pending">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-2" title="Kembalikan ke Pending">
                                                <i class="bi bi-arrow-counterclockwise"></i> Reset
                                            </button>
                                        @endif
                                    </form>

                                    <form action="/admin/feedbacks/{{ $item->id }}" method="POST" onsubmit="return confirm('Hapus catatan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-1" style="width: 32px; height: 32px;" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-chat-left-dots fs-1 d-block mb-2 text-secondary"></i>
                                Belum ada masukan atau catatan dari klien.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($feedbacks->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $feedbacks->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

