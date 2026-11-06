@extends('admin.layouts.app')

@section('title', 'Masukan Lapangan')
@section('page-title', 'Masukan & Catatan Lapangan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
    <li class="breadcrumb-item active">Masukan Lapangan</li>
@endsection

@section('content')

<!-- Filter Bar -->
<div class="card mb-3">
    <div class="card-body p-3">
        <form method="GET" action="/admin/feedbacks" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari judul, isi catatan, klien, atau proyek...">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="diproses" {{ request('status') === 'diproses' ? 'selected' : '' }}>Diproses</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="/admin/feedbacks" class="btn btn-sm btn-outline-secondary" title="Reset">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <span class="fw-bold"><i class="fas fa-comment-dots me-2 text-primary"></i>Catatan & Permintaan dari Klien Lapangan</span>
            <span class="badge bg-secondary-subtle text-secondary border font-monospace">{{ $feedbacks->total() }} Data</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead>
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th>Klien</th>
                        <th>Proyek</th>
                        <th>Judul Masukan</th>
                        <th>Isi Catatan</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th class="text-end" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($feedbacks as $index => $item)
                        <tr>
                            <td class="text-muted">{{ $feedbacks->firstItem() + $index }}</td>
                            <td>
                                <div class="fw-semibold">{{ $item->user->name ?? '-' }}</div>
                                <small class="text-muted">{{ $item->user->email ?? '' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $item->project->nama_proyek ?? 'Umum' }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold">{{ $item->title ?? 'Catatan' }}</span>
                            </td>
                            <td>
                                <small class="text-muted text-truncate d-inline-block" style="max-width: 250px;">
                                    {{ strip_tags($item->content) }}
                                </small>
                            </td>
                            <td>
                                @if($item->status === 'selesai')
                                    <span class="badge bg-success">Selesai</span>
                                @elseif($item->status === 'diproses')
                                    <span class="badge bg-info text-dark">Diproses</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $item->created_at->format('d/m/Y H:i') }}</small>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2" data-bs-toggle="modal" data-bs-target="#fbDetail{{ $item->id }}" title="Baca Lengkap">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <form action="/admin/feedbacks/{{ $item->id }}/status" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    @if($item->status === 'pending')
                                        <input type="hidden" name="status" value="diproses">
                                        <button type="submit" class="btn btn-xs btn-outline-info py-1 px-2" title="Tandai Sedang Diproses">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    @elseif($item->status === 'diproses')
                                        <input type="hidden" name="status" value="selesai">
                                        <button type="submit" class="btn btn-xs btn-outline-success py-1 px-2" title="Tandai Selesai">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @else
                                        <input type="hidden" name="status" value="pending">
                                        <button type="submit" class="btn btn-xs btn-outline-secondary py-1 px-2" title="Kembalikan ke Pending">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    @endif
                                </form>
                                <form action="/admin/feedbacks/{{ $item->id }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus catatan masukan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-outline-danger py-1 px-2" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>

                                <!-- Modal Detail Feedback -->
                                <div class="modal fade" id="fbDetail{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered text-start">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-bold"><i class="fas fa-comment-dots me-2 text-primary"></i>Masukan Lapangan: {{ $item->title ?? 'Tanpa Judul' }}</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <dl class="row mb-0 small">
                                                    <dt class="col-sm-3 text-muted">Klien</dt>
                                                    <dd class="col-sm-9 fw-semibold">{{ $item->user->name ?? '-' }} ({{ $item->user->email ?? '-' }})</dd>

                                                    <dt class="col-sm-3 text-muted">Proyek</dt>
                                                    <dd class="col-sm-9">{{ $item->project->nama_proyek ?? 'Umum' }}</dd>

                                                    <dt class="col-sm-3 text-muted">Status</dt>
                                                    <dd class="col-sm-9">
                                                        @if($item->status === 'selesai')
                                                            <span class="badge bg-success">Selesai</span>
                                                        @elseif($item->status === 'diproses')
                                                            <span class="badge bg-info text-dark">Diproses</span>
                                                        @else
                                                            <span class="badge bg-warning text-dark">Pending</span>
                                                        @endif
                                                    </dd>

                                                    <dt class="col-sm-3 text-muted">Tanggal</dt>
                                                    <dd class="col-sm-9">{{ $item->created_at->format('d F Y, H:i') }} WIB</dd>

                                                    <dt class="col-sm-3 text-muted">Isi Catatan</dt>
                                                    <dd class="col-sm-9">
                                                        <div class="p-2 bg-light rounded border text-secondary" style="white-space: pre-wrap;">{{ $item->content }}</div>
                                                    </dd>
                                                </dl>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <div class="mb-2"><i class="fas fa-comment-dots fs-3 text-secondary"></i></div>
                                <div>Belum ada masukan lapangan yang masuk.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($feedbacks->hasPages())
        <div class="card-footer py-2">
            {{ $feedbacks->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@endsection
