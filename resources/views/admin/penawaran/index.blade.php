@extends('admin.layouts.app')

@section('title', 'Penawaran Masuk (RFQ)')
@section('page-title', 'Penawaran Masuk (RFQ)')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
    <li class="breadcrumb-item active">Penawaran</li>
@endsection

@section('content')

<!-- Filter Bar -->
<div class="card mb-3">
    <div class="card-body p-3">
        <form method="GET" action="/admin/penawaran" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nama pemohon, kontak, alamat, atau estimasi budget...">
                </div>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="fas fa-search me-1"></i> Cari
                </button>
                @if(request()->filled('search'))
                    <a href="/admin/penawaran" class="btn btn-sm btn-outline-secondary" title="Reset">
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
            <span class="fw-bold"><i class="fas fa-file-invoice-dollar me-2 text-primary"></i>Daftar Permintaan Penawaran Biaya (RFQ)</span>
            <span class="badge bg-secondary-subtle text-secondary border font-monospace">{{ $penawarans->total() }} Data</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead>
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th>Foto</th>
                        <th>Pengirim</th>
                        <th>Estimasi Budget</th>
                        <th>Lokasi</th>
                        <th>Deskripsi Kebutuhan</th>
                        <th>Tanggal Masuk</th>
                        <th class="text-end" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penawarans as $index => $item)
                        @php
                            $phone = preg_replace('/[^0-9]/', '', $item->telepon);
                            if (str_starts_with($phone, '0')) {
                                $phone = '62' . substr($phone, 1);
                            }
                            $waText = urlencode("Halo Bapak/Ibu {$item->nama}, kami dari PT Indo Berkah Konstruksi ingin menindaklanjuti permohonan penawaran yang Anda ajukan melalui website kami.");
                        @endphp
                        <tr>
                            <td class="text-muted">{{ $penawarans->firstItem() + $index }}</td>
                            <td style="width: 55px;">
                                @if($item->foto)
                                    <a href="{{ asset('storage/' . $item->foto) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $item->foto) }}" alt="Foto" class="rounded border object-fit-cover" style="width: 45px; height: 35px;">
                                    </a>
                                @else
                                    <div class="rounded bg-light border text-muted d-flex align-items-center justify-content-center" style="width: 45px; height: 35px; font-size: 0.75rem;">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $item->nama }}</div>
                                <small class="text-muted d-block">{{ $item->telepon }}</small>
                                @if($item->email)
                                    <small class="text-muted d-block">{{ $item->email }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $item->budget }}</span>
                            </td>
                            <td>
                                <small class="text-muted text-truncate d-inline-block" style="max-width: 150px;">
                                    {{ $item->alamat ?? '-' }}
                                </small>
                            </td>
                            <td>
                                <small class="text-muted text-truncate d-inline-block" style="max-width: 220px;" title="{{ $item->deskripsi }}">
                                    {{ $item->deskripsi }}
                                </small>
                            </td>
                            <td>
                                <small class="text-muted">{{ $item->created_at->format('d/m/Y H:i') }}</small>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2" data-bs-toggle="modal" data-bs-target="#detailModal{{ $item->id }}" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="https://wa.me/{{ $phone }}?text={{ $waText }}" target="_blank" class="btn btn-xs btn-wa py-1 px-2" title="Hubungi via WhatsApp">
                                    <i class="fab fa-whatsapp me-1"></i> WA
                                </a>
                                <form action="/admin/penawaran/{{ $item->id }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data penawaran ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-outline-danger py-1 px-2" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>

                                <!-- Modal Detail Penawaran -->
                                <div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered text-start">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-bold"><i class="fas fa-file-invoice-dollar me-2 text-primary"></i>Detail Penawaran: {{ $item->nama }}</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <dl class="row mb-0 small">
                                                    <dt class="col-sm-4 text-muted">Nama Pengirim</dt>
                                                    <dd class="col-sm-8 fw-semibold">{{ $item->nama }}</dd>

                                                    <dt class="col-sm-4 text-muted">Nomor WhatsApp</dt>
                                                    <dd class="col-sm-8">{{ $item->telepon }}</dd>

                                                    <dt class="col-sm-4 text-muted">Email</dt>
                                                    <dd class="col-sm-8">{{ $item->email ?? '-' }}</dd>

                                                    <dt class="col-sm-4 text-muted">Estimasi Budget</dt>
                                                    <dd class="col-sm-8"><span class="badge bg-light text-dark border">{{ $item->budget }}</span></dd>

                                                    <dt class="col-sm-4 text-muted">Alamat / Lokasi</dt>
                                                    <dd class="col-sm-8">{{ $item->alamat ?? '-' }}</dd>

                                                    <dt class="col-sm-4 text-muted">Tanggal Masuk</dt>
                                                    <dd class="col-sm-8">{{ $item->created_at->format('d F Y, H:i') }} WIB</dd>

                                                    <dt class="col-sm-4 text-muted">Uraian Kebutuhan</dt>
                                                    <dd class="col-sm-8">{{ $item->deskripsi ?? '-' }}</dd>

                                                    @if($item->foto)
                                                        <dt class="col-sm-4 text-muted">Lampiran Denah/Foto</dt>
                                                        <dd class="col-sm-8">
                                                            <a href="{{ asset('storage/' . $item->foto) }}" target="_blank">
                                                                <img src="{{ asset('storage/' . $item->foto) }}" alt="Lampiran" class="img-fluid rounded border mt-1" style="max-height: 180px;">
                                                            </a>
                                                        </dd>
                                                    @endif
                                                </dl>
                                            </div>
                                            <div class="modal-footer d-flex justify-content-between">
                                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                <a href="https://wa.me/{{ $phone }}?text={{ $waText }}" target="_blank" class="btn btn-sm btn-wa">
                                                    <i class="fab fa-whatsapp me-1"></i> Balas via WhatsApp
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <div class="mb-2"><i class="fas fa-inbox fs-3 text-secondary"></i></div>
                                <div>Belum ada data penawaran yang diterima.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($penawarans->hasPages())
        <div class="card-footer py-2">
            {{ $penawarans->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@endsection
