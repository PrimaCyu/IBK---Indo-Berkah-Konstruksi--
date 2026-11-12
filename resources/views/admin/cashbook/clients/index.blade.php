@extends('admin.layouts.app')

@section('title', 'Buku Kontak Klien - PT Indo Berkah Konstruksi')
@section('page-title', 'Buku Kontak Klien')

@section('breadcrumb')
    <li class="breadcrumb-item text-muted">Manajemen Keuangan</li>
    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Kontak Klien</li>
@endsection

@section('content')
<div class="container-fluid px-0">
    {{-- Header Card --}}
    <div class="card border-0 shadow-sm mb-3" style="border-radius: 10px;">
        <div class="card-body p-3.5 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 38px; height: 38px; background-color: #eff6ff; color: #2563eb;">
                        <i class="fas fa-address-book fs-6"></i>
                    </div>
                    <div>
                        <h1 class="h5 fw-bold text-dark mb-0">Buku Kontak Klien</h1>
                        <p class="text-muted small mb-0">Direktori kontak & WhatsApp pemilik proyek untuk keperluan penagihan termin dan kuitansi.</p>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1.5 shadow-sm" data-bs-toggle="modal" data-bs-target="#addClientModal" style="border-radius: 6px; padding: 6px 14px; font-weight: 600;">
                    <i class="fas fa-user-plus"></i>
                    <span>Tambah Kontak Klien</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Filter & Search Card --}}
    <div class="card border-0 shadow-sm mb-3" style="border-radius: 10px;">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.cashbook.clients.index') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-6 col-lg-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm border-start-0" placeholder="Cari nama, email, atau nomor telepon klien...">
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-outline-primary px-3">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    @if(request()->filled('search'))
                        <a href="{{ route('admin.cashbook.clients.index') }}" class="btn btn-sm btn-outline-secondary ms-1" title="Reset filter">
                            <i class="fas fa-rotate-left"></i> Reset
                        </a>
                    @endif
                </div>
                <div class="col-auto ms-md-auto text-muted small">
                    Total: <strong class="text-dark">{{ $totalClientsCount }}</strong> klien terdaftar
                </div>
            </form>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="card border-0 shadow-sm" style="border-radius: 10px;">
        <div class="card-header bg-white border-bottom py-2.5 px-3.5 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-users text-primary"></i>
                <h2 class="h6 fw-bold mb-0 text-dark">Daftar Klien Proyek</h2>
                <span class="badge rounded-pill bg-light text-dark border font-monospace" style="font-size: 0.72rem;">{{ $clients->total() }} Data</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" tabindex="0" role="region" aria-label="Tabel daftar kontak klien">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 45px;" class="text-center">NO</th>
                            <th scope="col" style="min-width: 200px;">NAMA KLIEN</th>
                            <th scope="col" style="min-width: 180px;">KONTAK & WHATSAPP</th>
                            <th scope="col" style="min-width: 220px;">PROYEK TERKAIT</th>
                            <th scope="col" class="text-center" style="width: 110px;">JML PROYEK</th>
                            <th scope="col" class="text-end" style="width: 140px;">AKSI</th>
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
                                <td>
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle text-white fw-bold shadow-xs" 
                                             style="width: 36px; height: 36px; background-color: #3b82f6; font-size: 0.8rem; flex-shrink: 0;"
                                             title="{{ $client->name }}">
                                            {{ strtoupper($clientInitials) }}
                                        </div>
                                        <div class="text-truncate" style="max-width: 220px;">
                                            <div class="fw-bold text-dark text-truncate" style="font-size: 0.875rem;">{{ $client->name }}</div>
                                            <small class="text-muted d-block">Terdaftar: {{ $client->created_at ? $client->created_at->format('d M Y') : '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        @if($rawPhone)
                                            <div class="d-flex align-items-center gap-1.5">
                                                <i class="fab fa-whatsapp text-success fs-6"></i>
                                                <span class="font-monospace small fw-semibold text-dark">{{ $rawPhone }}</span>
                                            </div>
                                        @else
                                            <span class="text-muted small italic"><i class="fas fa-phone-slash me-1 opacity-50"></i>No. HP belum diisi</span>
                                        @endif

                                        <div class="text-muted small text-truncate" style="max-width: 200px;">
                                            <i class="fas fa-envelope text-secondary me-1 opacity-75"></i>{{ $client->email }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($client->projects->isNotEmpty())
                                        <div class="d-flex flex-column gap-1">
                                            @foreach($client->projects->take(2) as $proj)
                                                <div class="d-flex align-items-center gap-1.5">
                                                    <span class="badge {{ $proj->status === 'selesai' ? 'bg-success-subtle text-success border border-success-subtle' : ($proj->status === 'aktif' ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-secondary-subtle text-secondary border border-secondary-subtle') }}" style="font-size: 0.68rem; padding: 2px 6px;">
                                                        {{ ucfirst($proj->status) }}
                                                    </span>
                                                    <a href="{{ route('admin.cashbook.show', $proj) }}" class="text-decoration-none text-dark small fw-medium text-truncate" style="max-width: 180px;" title="{{ $proj->nama_proyek }}">
                                                        {{ $proj->nama_proyek }}
                                                    </a>
                                                </div>
                                            @endforeach
                                            @if($client->projects->count() > 2)
                                                <span class="text-muted" style="font-size: 0.72rem;">+{{ $client->projects->count() - 2 }} proyek lainnya</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted small">Belum ada proyek</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-light text-dark border font-monospace">
                                        {{ $client->projects_count }} Proyek
                                    </span>
                                </td>
                                <td class="text-end text-nowrap">
                                    <div class="d-inline-flex gap-1.5 align-items-center">
                                        @if($cleanWa)
                                            <a href="https://wa.me/{{ $cleanWa }}?text={{ urlencode('Halo ' . $client->name . ', kami dari Bagian Keuangan PT Indo Berkah Konstruksi...') }}" 
                                               target="_blank" 
                                               class="btn btn-xs btn-success d-inline-flex align-items-center gap-1 shadow-none px-2 py-1"
                                               style="font-size: 0.75rem; border-radius: 6px;"
                                               title="Chat via WhatsApp">
                                                <i class="fab fa-whatsapp"></i> Chat WA
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.cashbook.index') }}?search={{ urlencode($client->name) }}" 
                                           class="btn btn-xs btn-outline-primary d-inline-flex align-items-center gap-1 shadow-none px-2 py-1"
                                           style="font-size: 0.75rem; border-radius: 6px;"
                                           title="Lihat Buku Kas Proyek Klien">
                                            <i class="fas fa-wallet"></i> Buku Kas
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
            <div class="card-footer bg-white border-top py-2 px-3">
                {{ $clients->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Modal Tambah Kontak Klien --}}
<div class="modal fade" id="addClientModal" tabindex="-1" aria-labelledby="addClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom py-2.5 px-3.5">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-user-plus text-primary"></i>
                    <h5 class="modal-title h6 fw-bold text-dark mb-0" id="addClientModalLabel">Tambah Kontak Klien Baru</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form action="{{ route('admin.cashbook.clients.store') }}" method="POST">
                @csrf
                <div class="modal-body p-3.5">
                    <div class="mb-3">
                        <label for="client-name" class="form-label small fw-bold text-dark">Nama Lengkap Klien <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="client-name" name="name" placeholder="Contoh: Bpk. H. Ahmad Fauzi" required value="{{ old('name') }}">
                    </div>

                    <div class="mb-3">
                        <label for="client-phone" class="form-label small fw-bold text-dark">Nomor WhatsApp / HP</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light"><i class="fab fa-whatsapp text-success"></i></span>
                            <input type="text" class="form-control form-control-sm" id="client-phone" name="phone" placeholder="Contoh: 081234567890" value="{{ old('phone') }}">
                        </div>
                        <div class="form-text" style="font-size: 0.72rem;">Digunakan untuk konfirmasi kuitansi dan penagihan termin via WhatsApp.</div>
                    </div>

                    <div class="mb-3">
                        <label for="client-email" class="form-label small fw-bold text-dark">Alamat Email <span class="text-danger">*</span></label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light"><i class="fas fa-envelope text-muted"></i></span>
                            <input type="email" class="form-control form-control-sm" id="client-email" name="email" placeholder="Contoh: ahmad.fauzi@gmail.com" required value="{{ old('email') }}">
                        </div>
                        <div class="form-text" style="font-size: 0.72rem;">Email digunakan sebagai identitas akun portal klien IBK.</div>
                    </div>

                    <div class="mb-2">
                        <label for="client-password" class="form-label small fw-bold text-dark">Password Akun Klien (Opsional)</label>
                        <input type="password" class="form-control form-control-sm" id="client-password" name="password" placeholder="Dikosongkan jika ingin password default: ibk12345">
                        <div class="form-text" style="font-size: 0.72rem;">Bila dikosongkan, password otomatis disetel ke: <code>ibk12345</code></div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2 px-3.5">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary px-3">
                        <i class="fas fa-save me-1"></i> Simpan Kontak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
