@extends('admin.layouts.app')

@section('title', 'Buku Kontak Klien - PT Indo Berkah Konstruksi')
@section('page-title', 'Buku Kontak Klien')

@section('breadcrumb')
    <li class="breadcrumb-item text-muted">Manajemen Keuangan</li>
    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Kontak Klien</li>
@endsection

@section('content')
<style>
    .client-avatar-circle {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background-color: #EFF6FF;
        border: 1px solid #DBEAFE;
        color: #2563EB;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.75rem;
        flex-shrink: 0;
    }
    .badge-soft-success {
        background-color: #DCFCE7;
        color: #15803D;
        border: 1px solid #BBF7D0;
    }
    .badge-soft-primary {
        background-color: #DBEAFE;
        color: #1D4ED8;
        border: 1px solid #BFDBFE;
    }
    .badge-soft-neutral {
        background-color: #F1F5F9;
        color: #475569;
        border: 1px solid #E2E8F0;
    }
    .btn-action-wa {
        background-color: #ECFDF5;
        border: 1px solid #A7F3D0;
        color: #047857;
        font-size: 0.75rem;
        font-weight: 600;
        height: 30px;
        padding: 0 10px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        text-decoration: none;
        transition: all 0.15s ease-in-out;
    }
    .btn-action-wa:hover {
        background-color: #10B981;
        border-color: #059669;
        color: #FFFFFF;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
    }
    .btn-action-cashbook {
        background-color: #F8FAFC;
        border: 1px solid #E2E8F0;
        color: #334155;
        font-size: 0.75rem;
        font-weight: 600;
        height: 30px;
        padding: 0 10px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        text-decoration: none;
        transition: all 0.15s ease-in-out;
    }
    .btn-action-cashbook:hover {
        background-color: #2563EB;
        border-color: #1D4ED8;
        color: #FFFFFF;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
    }
    .btn-action-cashbook:hover i {
        color: #FFFFFF !important;
    }
    .project-link:hover {
        color: #2563EB !important;
        text-decoration: underline !important;
    }
</style>

<div class="container-fluid px-0">
    {{-- 1. Page Header: Minimalist & Clean --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
        <div>
            <h1 class="h5 fw-bold text-dark mb-1" style="font-size: 1.15rem; letter-spacing: -0.2px;">Direktori Kontak Klien</h1>
            <p class="text-muted small mb-0" style="font-size: 0.8rem;">Data kontak WhatsApp & email pemilik proyek untuk penagihan termin dan kuitansi.</p>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1.5 shadow-xs" data-bs-toggle="modal" data-bs-target="#addClientModal" style="border-radius: 6px; padding: 6px 14px; font-weight: 600; font-size: 0.8rem;">
                <i class="fas fa-plus" style="font-size: 0.72rem;"></i>
                <span>Tambah Kontak Klien</span>
            </button>
        </div>
    </div>

    {{-- 2. Main Card: Integrated Toolbar & Table --}}
    <div class="card border shadow-xs" style="border-radius: 10px; border-color: #E2E8F0;">
        {{-- Toolbar: Search, Filter, & Total Counter --}}
        <div class="card-header bg-white border-bottom py-2.5 px-3.5">
            <form method="GET" action="{{ route('admin.cashbook.clients.index') }}" class="d-flex align-items-center justify-content-between flex-wrap gap-2.5">
                {{-- Search & Filter Group --}}
                <div class="d-flex align-items-center gap-2 flex-grow-1" style="max-width: 480px;">
                    <div class="input-group input-group-sm flex-grow-1 shadow-none">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-2.5 pe-1.5" style="border-color: #CBD5E1; border-radius: 6px 0 0 6px;">
                            <i class="fas fa-search" style="font-size: 0.75rem;"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm border-start-0 ps-1" placeholder="Cari nama, email, atau no. WhatsApp..." style="border-color: #CBD5E1; border-radius: 0 6px 6px 0; font-size: 0.8rem; height: 34px;">
                    </div>
                    <button type="submit" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1.5 px-3 text-nowrap" style="height: 34px; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">
                        <i class="fas fa-filter" style="font-size: 0.72rem;"></i>
                        <span>Filter</span>
                    </button>
                    @if(request()->filled('search'))
                        <a href="{{ route('admin.cashbook.clients.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center justify-content-center" title="Reset pencarian" style="height: 34px; width: 34px; padding: 0; border-radius: 6px;">
                            <i class="fas fa-rotate-left" style="font-size: 0.75rem;"></i>
                        </a>
                    @endif
                </div>

                {{-- Counter Info --}}
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <span class="badge rounded-pill bg-light text-secondary border px-3 py-1.5 font-monospace" style="font-size: 0.75rem; font-weight: 500;">
                        <i class="fas fa-users me-1 text-primary opacity-75"></i> Total: <strong class="text-dark">{{ $totalClientsCount }}</strong> Klien
                    </span>
                </div>
            </form>
        </div>

        {{-- Table Body --}}
        <div class="card-body p-0">
            <div class="table-responsive" tabindex="0" role="region" aria-label="Tabel daftar kontak klien">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 50px;" class="text-center">NO</th>
                            <th scope="col" style="min-width: 210px;">NAMA KLIEN</th>
                            <th scope="col" style="min-width: 200px;">KONTAK & WHATSAPP</th>
                            <th scope="col" style="min-width: 240px;">PROYEK TERKAIT</th>
                            <th scope="col" class="text-center" style="width: 120px;">TOTAL PROYEK</th>
                            <th scope="col" class="text-end" style="width: 190px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $index => $client)
                            @php
                                $clientInitials = collect(explode(' ', $client->name))->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->implode('');
                                $rawPhone = $client->phone;
                                $cleanWa = null;
                                if ($rawPhone) {
                                    $digits = preg_replace('/[^0-9]/', '', (string)$rawPhone);
                                    if (str_starts_with($digits, '0')) {
                                        $cleanWa = '62' . substr($digits, 1);
                                    } elseif (str_starts_with($digits, '62')) {
                                        $cleanWa = $digits;
                                    } else {
                                        $cleanWa = '62' . $digits;
                                    }
                                }
                            @endphp
                            <tr>
                                <td class="text-center text-muted small">{{ $clients->firstItem() + $index }}</td>
                                
                                {{-- 1. NAMA KLIEN --}}
                                <td>
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="client-avatar-circle" title="{{ $client->name }}">
                                            {{ strtoupper($clientInitials) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="fw-semibold text-dark text-truncate" style="max-width: 200px; font-size: 0.85rem;">{{ $client->name }}</div>
                                            <div class="text-muted text-truncate" style="font-size: 0.72rem;">
                                                <i class="far fa-calendar-alt me-1 opacity-75"></i>{{ $client->created_at ? $client->created_at->format('d M Y') : '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- 2. KONTAK & WHATSAPP --}}
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        @if($rawPhone)
                                            <div class="d-flex align-items-center gap-1.5">
                                                <span class="d-inline-flex align-items-center justify-content-center text-success" style="width: 16px; font-size: 0.85rem;">
                                                    <i class="fab fa-whatsapp"></i>
                                                </span>
                                                <span class="font-monospace fw-semibold text-dark" style="font-size: 0.8rem; letter-spacing: 0.2px;">
                                                    {{ $rawPhone }}
                                                </span>
                                            </div>
                                        @else
                                            <div class="text-muted small" style="font-size: 0.75rem;">
                                                <i class="fas fa-phone-slash me-1 opacity-50"></i>No. HP belum diisi
                                            </div>
                                        @endif

                                        <div class="d-flex align-items-center gap-1.5 text-muted" style="font-size: 0.75rem;">
                                            <span class="d-inline-flex align-items-center justify-content-center text-secondary opacity-75" style="width: 16px; font-size: 0.75rem;">
                                                <i class="far fa-envelope"></i>
                                            </span>
                                            <span class="text-truncate" style="max-width: 180px;" title="{{ $client->email }}">
                                                {{ $client->email }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- 3. PROYEK TERKAIT --}}
                                <td>
                                    @if($client->projects->isNotEmpty())
                                        <div class="d-flex flex-column gap-1.5">
                                            @foreach($client->projects->take(2) as $proj)
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge {{ $proj->status === 'selesai' ? 'badge-soft-success' : ($proj->status === 'aktif' ? 'badge-soft-primary' : 'badge-soft-neutral') }}" style="font-size: 0.68rem; padding: 2px 7px; border-radius: 4px; flex-shrink: 0; font-weight: 500;">
                                                        {{ ucfirst($proj->status) }}
                                                    </span>
                                                    <a href="{{ route('admin.cashbook.show', $proj) }}" class="text-decoration-none text-dark small fw-medium text-truncate project-link" style="max-width: 190px;" title="{{ $proj->nama_proyek }}">
                                                        {{ $proj->nama_proyek }}
                                                    </a>
                                                </div>
                                            @endforeach
                                            @if($client->projects->count() > 2)
                                                <span class="text-muted ps-1" style="font-size: 0.7rem;">+{{ $client->projects->count() - 2 }} proyek lainnya</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted small italic" style="font-size: 0.75rem;">Belum ada proyek</span>
                                    @endif
                                </td>

                                {{-- 4. TOTAL PROYEK --}}
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-light text-secondary border font-monospace px-2.5 py-1" style="font-size: 0.72rem;">
                                        {{ $client->projects_count }} Proyek
                                    </span>
                                </td>

                                {{-- 5. AKSI --}}
                                <td class="text-end text-nowrap">
                                    <div class="d-inline-flex gap-1.5 align-items-center justify-content-end">
                                        @if($cleanWa)
                                            <a href="https://wa.me/{{ $cleanWa }}?text={{ urlencode('Halo ' . $client->name . ', kami dari Bagian Keuangan PT Indo Berkah Konstruksi...') }}" 
                                               target="_blank" 
                                               class="btn-action-wa"
                                               title="Chat via WhatsApp">
                                                <i class="fab fa-whatsapp"></i>
                                                <span>Chat WA</span>
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.cashbook.index') }}?search={{ urlencode($client->name) }}" 
                                           class="btn-action-cashbook"
                                           title="Lihat Buku Kas Proyek Klien">
                                            <i class="fas fa-wallet text-secondary"></i>
                                            <span>Buku Kas</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted mb-2"><i class="fas fa-users-slash fs-3 opacity-50"></i></div>
                                    <p class="fw-semibold mb-1 text-dark">Belum ada data kontak klien yang cocok.</p>
                                    <p class="small text-muted mb-3">Klik tombol "Tambah Kontak Klien" di atas untuk mendaftarkan kontak baru.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($clients->hasPages())
            <div class="card-footer bg-white border-top py-2.5 px-3.5">
                {{ $clients->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Modal Tambah Kontak Klien --}}
<div class="modal fade" id="addClientModal" tabindex="-1" aria-labelledby="addClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-light border-bottom py-2.5 px-3.5">
                <div class="d-flex align-items-center gap-2">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle text-primary" style="width: 28px; height: 28px; background-color: #EFF6FF;">
                        <i class="fas fa-user-plus" style="font-size: 0.75rem;"></i>
                    </div>
                    <h5 class="modal-title h6 fw-bold text-dark mb-0" id="addClientModalLabel">Tambah Kontak Klien Baru</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup" style="font-size: 0.75rem;"></button>
            </div>
            <form action="{{ route('admin.cashbook.clients.store') }}" method="POST">
                @csrf
                <div class="modal-body p-3.5">
                    <div class="mb-3">
                        <label for="client-name" class="form-label small fw-bold text-dark">Nama Lengkap Klien <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="client-name" name="name" placeholder="Contoh: Bpk. H. Ahmad Fauzi" required value="{{ old('name') }}" style="border-radius: 6px; font-size: 0.825rem;">
                    </div>

                    <div class="mb-3">
                        <label for="client-phone" class="form-label small fw-bold text-dark">Nomor WhatsApp / HP</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light text-success border-end-0" style="border-radius: 6px 0 0 6px;"><i class="fab fa-whatsapp"></i></span>
                            <input type="text" class="form-control form-control-sm border-start-0 ps-1" id="client-phone" name="phone" placeholder="Contoh: 081234567890" value="{{ old('phone') }}" style="border-radius: 0 6px 6px 0; font-size: 0.825rem;">
                        </div>
                        <div class="form-text text-muted" style="font-size: 0.72rem;">Digunakan untuk konfirmasi kuitansi dan penagihan termin via WhatsApp.</div>
                    </div>

                    <div class="mb-3">
                        <label for="client-email" class="form-label small fw-bold text-dark">Alamat Email <span class="text-danger">*</span></label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light text-muted border-end-0" style="border-radius: 6px 0 0 6px;"><i class="far fa-envelope"></i></span>
                            <input type="email" class="form-control form-control-sm border-start-0 ps-1" id="client-email" name="email" placeholder="Contoh: ahmad.fauzi@gmail.com" required value="{{ old('email') }}" style="border-radius: 0 6px 6px 0; font-size: 0.825rem;">
                        </div>
                        <div class="form-text text-muted" style="font-size: 0.72rem;">Email digunakan sebagai identitas akun portal klien IBK.</div>
                    </div>

                    <div class="mb-2">
                        <label for="client-password" class="form-label small fw-bold text-dark">Password Akun Klien (Opsional)</label>
                        <input type="password" class="form-control form-control-sm" id="client-password" name="password" placeholder="Dikosongkan jika ingin password default: ibk12345" style="border-radius: 6px; font-size: 0.825rem;">
                        <div class="form-text text-muted" style="font-size: 0.72rem;">Bila dikosongkan, password otomatis disetel ke: <code>ibk12345</code></div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2.5 px-3.5">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 6px; font-size: 0.8rem;">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary px-3" style="border-radius: 6px; font-size: 0.8rem; font-weight: 600;">
                        <i class="fas fa-save me-1"></i> Simpan Kontak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
