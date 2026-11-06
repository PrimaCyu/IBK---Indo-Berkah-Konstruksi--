@extends('admin.layouts.app')

@section('title', 'Penawaran Masuk (RFQ)')
@section('page-title', 'Daftar Penawaran Masuk (Leads)')
@section('page-subtitle', 'Kelola permohonan RAB dan penawaran harga dari calon klien')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
    <li class="breadcrumb-item active">Penawaran</li>
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold">
            <i class="bi bi-inbox-fill text-primary me-2"></i> Semua Permintaan Penawaran ({{ $penawarans->total() }})
        </h5>
        <div>
            <span class="badge badge-gold px-3 py-2 rounded-pill">Total: {{ $penawarans->total() }} Data</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-modern align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Foto Lokasi</th>
                        <th>Nama & Kontak</th>
                        <th>Estimasi Budget</th>
                        <th>Alamat Proyek</th>
                        <th>Rincian Kebutuhan</th>
                        <th>Waktu Masuk</th>
                        <th class="text-end" style="width: 180px;">Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penawarans as $index => $item)
                        @php
                            $phone = preg_replace('/[^0-9]/', '', $item->telepon);
                            if (str_starts_with($phone, '0')) {
                                $phone = '62' . substr($phone, 1);
                            }
                            $waText = urlencode("Halo Bapak/Ibu {$item->nama}, kami dari PT Indo Berkah Konstruksi ingin menindaklanjuti permintaan penawaran pembangunan Anda.");
                        @endphp
                        <tr>
                            <td class="text-muted">{{ $penawarans->firstItem() + $index }}</td>
                            <td>
                                @if($item->foto)
                                    <a href="{{ asset('storage/' . $item->foto) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $item->foto) }}" alt="Foto" class="rounded shadow-sm object-fit-cover" style="width: 60px; height: 60px;">
                                    </a>
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted border" style="width: 60px; height: 60px;">
                                        <i class="bi bi-image fs-4"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $item->nama }}</div>
                                <div class="small text-muted mb-1"><i class="bi bi-telephone text-primary"></i> {{ $item->telepon }}</div>
                                @if($item->email)
                                    <div class="small text-muted"><i class="bi bi-envelope"></i> {{ $item->email }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 fw-bold fs-7">
                                    {{ $item->budget }}
                                </span>
                            </td>
                            <td>
                                <span class="small text-secondary">{{ $item->alamat ?? '-' }}</span>
                            </td>
                            <td>
                                <div class="small text-dark text-truncate" style="max-width: 250px;" title="{{ $item->deskripsi }}">
                                    {{ $item->deskripsi }}
                                </div>
                            </td>
                            <td>
                                <small class="text-muted d-block">{{ $item->created_at->format('d M Y') }}</small>
                                <small class="text-muted" style="font-size: 0.75rem;">{{ $item->created_at->format('H:i') }} WIB</small>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="https://wa.me/{{ $phone }}?text={{ $waText }}" target="_blank" class="btn btn-sm btn-whatsapp rounded-pill px-3 shadow-sm" title="Chat WhatsApp">
                                        <i class="bi bi-whatsapp"></i> Chat WA
                                    </a>
                                    <form action="/admin/penawaran/{{ $item->id }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus penawaran ini?')">
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
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                Belum ada penawaran biaya yang masuk saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($penawarans->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $penawarans->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

