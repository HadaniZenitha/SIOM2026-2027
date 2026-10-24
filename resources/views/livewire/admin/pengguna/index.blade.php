<div class="siom-admin-users">
<style>
    @import url('https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    .siom-admin-users {
        --users-bg: #eff6ff;
        --users-card: #fff;
        --users-ink: #0f172a;
        --users-muted: #475569;
        --users-blue: #2563eb;
        --users-blue-dark: #1d4ed8;
        --users-blue-light: #f0f7ff;
        --users-blue-ice: #e0f2fe;
        --users-border: 2px solid var(--users-ink);
        --users-shadow: 4px 4px 0 var(--users-ink);
        min-height: 100%;
        padding: clamp(1rem, 3vw, 2.5rem);
        background:
            radial-gradient(circle at 92% 2%, rgba(56, 189, 248, .12), transparent 24rem),
            var(--users-bg);
        color: var(--users-ink);
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .siom-admin-users *,
    .siom-admin-users *::before,
    .siom-admin-users *::after {
        box-sizing: border-box;
    }

    .siom-admin-users .d-flex { display: flex; }
    .siom-admin-users .flex-wrap { flex-wrap: wrap; }
    .siom-admin-users .justify-content-between { justify-content: space-between; }
    .siom-admin-users .align-items-center { align-items: center; }
    .siom-admin-users .text-end { text-align: right; }
    .siom-admin-users .text-center { text-align: center; }
    .siom-admin-users .d-block { display: block; }
    .siom-admin-users .w-100 { width: 100%; }
    .siom-admin-users .mb-4 { margin-bottom: 1.5rem; }
    .siom-admin-users .mb-3 { margin-bottom: 1rem; }
    .siom-admin-users .mb-1 { margin-bottom: .25rem; }
    .siom-admin-users .mb-0 { margin-bottom: 0; }
    .siom-admin-users .me-1 { margin-right: .25rem; }
    .siom-admin-users .me-2 { margin-right: .5rem; }
    .siom-admin-users .me-3 { margin-right: 1rem; }
    .siom-admin-users .ps-4 { padding-left: 1.5rem; }
    .siom-admin-users .pe-4 { padding-right: 1.5rem; }
    .siom-admin-users .p-0 { padding: 0; }
    .siom-admin-users .py-5 { padding-top: 3rem; padding-bottom: 3rem; }
    .siom-admin-users .fs-1 { font-size: 2.5rem; }
    .siom-admin-users .small { font-size: .875rem; }
    .siom-admin-users .fw-bold { font-weight: 800; }
    .siom-admin-users .fw-semibold { font-weight: 700; }
    .siom-admin-users .fw-normal { font-weight: 500; }
    .siom-admin-users .text-secondary { color: #64748b; }

    .siom-admin-users h4,
    .siom-admin-users h5,
    .siom-admin-users h6,
    .siom-admin-users p {
        margin-top: 0;
    }

    .siom-admin-users h4,
    .siom-admin-users h5 {
        color: var(--users-ink);
        font-family: Fredoka, 'Plus Jakarta Sans', sans-serif;
        letter-spacing: .01em;
    }

    .siom-admin-users h4 { font-size: clamp(1.65rem, 3vw, 2.1rem); }
    .siom-admin-users h5 { font-size: 1.45rem; }
    .siom-admin-users h6 { font-size: 1rem; }
    .siom-admin-users .text-muted { color: var(--users-muted) !important; }
    .siom-admin-users .text-danger { color: #dc2626 !important; }
    .siom-admin-users .text-success { color: #15803d !important; }
    .siom-admin-users .text-primary { color: var(--users-blue) !important; }

    .siom-admin-users .row {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: 1rem;
        margin: 0;
    }

    .siom-admin-users .col-md-6 { grid-column: span 6; }
    .siom-admin-users .col-md-3 { grid-column: span 3; }
    .siom-admin-users .col-12 { grid-column: 1 / -1; }

    .siom-admin-users .card {
        overflow: hidden;
        border: var(--users-border) !important;
        border-radius: 18px;
        background: var(--users-card);
        box-shadow: var(--users-shadow) !important;
    }

    .siom-admin-users .card-body { padding: 1.25rem; }
    .siom-admin-users .card-body.p-0 { padding: 0; }
    .siom-admin-users .card-footer {
        padding: 1rem 1.25rem;
        border-top: 1px solid #dbeafe !important;
        background: #fff;
    }

    .siom-admin-users .btn {
        display: inline-flex;
        min-height: 2.65rem;
        align-items: center;
        justify-content: center;
        gap: .35rem;
        padding: .6rem 1.15rem;
        border: var(--users-border);
        border-radius: 9999px;
        background: #fff;
        color: var(--users-ink);
        font-family: Fredoka, 'Plus Jakarta Sans', sans-serif;
        font-size: .9rem;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        box-shadow: 2px 2px 0 var(--users-ink);
        transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
    }

    .siom-admin-users .btn:hover {
        transform: translate(-1px, -1px);
        box-shadow: 3px 3px 0 var(--users-ink);
    }

    .siom-admin-users .btn-primary {
        border-color: var(--users-ink);
        background: var(--users-blue);
        color: #fff;
    }

    .siom-admin-users .btn-primary:hover { background: var(--users-blue-dark); color: #fff; }
    .siom-admin-users .btn-light { background: #f8fafc; }
    .siom-admin-users .btn-sm { min-height: 2.25rem; padding: .4rem .75rem; font-size: .85rem; }
    .siom-admin-users .btn-group { display: inline-flex; gap: .45rem; }

    .siom-admin-users .alert {
        display: flex;
        align-items: center;
        gap: .5rem;
        margin-bottom: 1rem;
        padding: .9rem 1.1rem;
        border: var(--users-border);
        border-radius: 14px;
        background: #fff;
        box-shadow: 3px 3px 0 var(--users-ink);
        font-weight: 600;
    }

    .siom-admin-users .alert-success { border-left: 8px solid #16a34a; }
    .siom-admin-users .alert-danger { border-left: 8px solid #dc2626; }
    .siom-admin-users .btn-close {
        position: relative;
        width: 1.5rem;
        height: 1.5rem;
        margin-left: auto;
        border: 0;
        background: transparent;
        color: inherit;
        font-size: 0;
        cursor: pointer;
    }

    .siom-admin-users .btn-close::before {
        content: '×';
        font-size: 1.7rem;
        line-height: 1;
    }

    .siom-admin-users .form-label {
        display: block;
        margin-bottom: .4rem;
        color: var(--users-ink);
        font-size: .88rem;
        font-weight: 700;
    }

    .siom-admin-users .form-control,
    .siom-admin-users .form-select {
        display: block;
        width: 100%;
        min-height: 2.8rem;
        padding: .65rem .85rem;
        border: 1.5px solid #94a3b8;
        border-radius: 10px;
        background-color: #f8fafc;
        color: var(--users-ink);
        font: 500 .9rem 'Plus Jakarta Sans', sans-serif;
        transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
    }

    .siom-admin-users .form-control:focus,
    .siom-admin-users .form-select:focus {
        border-color: var(--users-blue);
        outline: 0;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .16);
    }

    .siom-admin-users .form-control.is-invalid,
    .siom-admin-users .form-select.is-invalid { border-color: #dc2626; }
    .siom-admin-users .invalid-feedback { display: block; margin-top: .3rem; color: #b91c1c; font-size: .8rem; }
    .siom-admin-users .input-group { display: flex; }
    .siom-admin-users .input-group .form-control { border-radius: 0 10px 10px 0; }
    .siom-admin-users .input-group-text {
        display: inline-flex;
        align-items: center;
        padding: 0 .9rem;
        border: 1.5px solid #94a3b8;
        border-right: 0;
        border-radius: 10px 0 0 10px;
        background: var(--users-blue-ice);
    }

    .siom-admin-users .table-responsive { width: 100%; overflow-x: auto; }
    .siom-admin-users .table {
        width: 100%;
        min-width: 850px;
        border-collapse: collapse;
        color: var(--users-ink);
        font-size: .88rem;
    }

    .siom-admin-users .table th {
        padding: .95rem .75rem;
        border-bottom: var(--users-border);
        background: var(--users-blue-ice);
        color: var(--users-ink);
        font-family: Fredoka, 'Plus Jakarta Sans', sans-serif;
        font-size: .82rem;
        font-weight: 700;
        text-align: left;
        white-space: nowrap;
    }

    .siom-admin-users .table td {
        padding: .85rem .75rem;
        border-bottom: 1px solid #e2e8f0;
        vertical-align: middle;
    }

    .siom-admin-users .table tbody tr:last-child td { border-bottom: 0; }
    .siom-admin-users .table-hover tbody tr:hover { background: var(--users-blue-light); }
    .siom-admin-users .badge {
        display: inline-flex;
        align-items: center;
        padding: .38rem .7rem;
        border: 1px solid currentColor;
        border-radius: 9999px;
        font-size: .75rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .siom-admin-users .bg-primary-subtle { background: var(--users-blue-ice); }
    .siom-admin-users .bg-success-subtle { background: #dcfce7; }
    .siom-admin-users .bg-secondary-subtle { background: #f1f5f9; }
    .siom-admin-users .bg-white { background: #fff; }
    .siom-admin-users .bg-primary { background: var(--users-blue); }
    .siom-admin-users .text-white { color: #fff !important; }
    .siom-admin-users .rounded-circle { border-radius: 50%; }

    .siom-admin-users .pagination {
        display: flex;
        flex-wrap: wrap;
        gap: .4rem;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .siom-admin-users .page-link {
        display: inline-flex;
        min-width: 2.35rem;
        min-height: 2.35rem;
        align-items: center;
        justify-content: center;
        padding: .4rem .7rem;
        border: 1.5px solid var(--users-ink);
        border-radius: 9px;
        background: #fff;
        color: var(--users-ink);
        font-weight: 700;
        text-decoration: none;
    }

    .siom-admin-users .page-item.active .page-link { background: var(--users-blue); color: #fff; }
    .siom-admin-users .page-item.disabled .page-link { opacity: .45; }

    .siom-admin-users .modal {
        position: fixed;
        z-index: 1100;
        inset: 0;
        display: grid;
        place-items: center;
        overflow-y: auto;
        padding: 1rem;
        background: rgba(15, 23, 42, .68) !important;
        backdrop-filter: blur(4px);
    }

    .siom-admin-users .modal-dialog { width: min(100%, 760px); margin: auto; }
    .siom-admin-users .modal-content {
        overflow: hidden;
        border: 2px solid var(--users-ink) !important;
        border-radius: 20px;
        background: #fff;
        box-shadow: 7px 7px 0 var(--users-ink) !important;
    }

    .siom-admin-users .modal-header,
    .siom-admin-users .modal-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.15rem 1.4rem;
        border-bottom: 1px solid #dbeafe;
    }

    .siom-admin-users .modal-body { max-height: min(68vh, 700px); overflow-y: auto; padding: 1.4rem; }
    .siom-admin-users .modal-footer { justify-content: flex-end; border-top: 1px solid #dbeafe; border-bottom: 0; }
    .siom-admin-users .form-check { display: flex; align-items: center; gap: .6rem; }
    .siom-admin-users .form-check-input { width: 1.1rem; height: 1.1rem; accent-color: var(--users-blue); }
    .siom-admin-users .form-check-label { font-weight: 700; }
    .siom-admin-users .modal-body > .row > .mb-3 { grid-column: 1 / -1; }

    @media (max-width: 767px) {
        .siom-admin-users { padding: 1rem; }
        .siom-admin-users .row { grid-template-columns: 1fr; }
        .siom-admin-users .col-md-6,
        .siom-admin-users .col-md-3,
        .siom-admin-users .col-12 { grid-column: 1 / -1; }
        .siom-admin-users .modal { align-items: start; }
        .siom-admin-users .modal-dialog { margin: auto; }
        .siom-admin-users .modal-body { max-height: none; }
    }

    @media (max-width: 480px) {
        .siom-admin-users > .d-flex { align-items: stretch; gap: 1rem; }
        .siom-admin-users > .d-flex > .btn { width: 100%; }
        .siom-admin-users .card-body { padding: 1rem; }
        .siom-admin-users .modal { padding: .5rem; }
        .siom-admin-users .modal-header,
        .siom-admin-users .modal-body,
        .siom-admin-users .modal-footer { padding: 1rem; }
    }
</style>

    {{-- HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Pengguna</h4>
            <p class="text-muted mb-0">
                Kelola akun dan hak akses pengguna SIOM.
            </p>
        </div>

        <button
            type="button"
            class="btn btn-primary"
            wire:click="create"
        >
            <i class="bi bi-person-plus me-1"></i>
            Tambah Pengguna
        </button>
    </div>


    {{-- FLASH MESSAGE --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-circle me-2"></i>
            {{ session('error') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>
        </div>
    @endif


    {{-- FILTER --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Cari Pengguna
                    </label>

                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-search"></i>
                        </span>

                        <input
                            type="text"
                            class="form-control"
                            wire:model.live.debounce.400ms="search"
                            placeholder="Cari nama atau email..."
                        >
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        Role
                    </label>

                    <select
                        class="form-select"
                        wire:model.live="roleFilter"
                    >
                        <option value="">Semua Role</option>

                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}">
                                {{ $role->display_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        Status
                    </label>

                    <select
                        class="form-select"
                        wire:model.live="statusFilter"
                    >
                        <option value="">Semua Status</option>
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>

            </div>

        </div>
    </div>


    {{-- TABLE --}}
    <div class="card border-0 shadow-sm">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>
                            <th class="ps-4" width="60">No</th>
                            <th>Pengguna</th>
                            <th>Role</th>
                            <th>ORMAWA</th>
                            <th>Program Studi</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($users as $index => $user)

                            <tr>

                                <td class="ps-4">
                                    {{ $users->firstItem() + $index }}
                                </td>

                                <td>

                                    <div class="d-flex align-items-center">

                                        <div
                                            class="rounded-circle bg-primary-subtle text-primary
                                                   d-flex align-items-center justify-content-center
                                                   me-3"
                                            style="width:42px;height:42px;"
                                        >
                                            <i class="bi bi-person"></i>
                                        </div>

                                        <div>
                                            <div class="fw-semibold">
                                                {{ $user->name }}
                                            </div>

                                            <small class="text-muted">
                                                {{ $user->email }}
                                            </small>
                                        </div>

                                    </div>

                                </td>

                                <td>
                                    @if ($user->role)
                                        <span class="badge bg-primary-subtle text-primary">
                                            {{ $user->role->display_name }}
                                        </span>
                                    @else
                                        <span class="text-muted">
                                            -
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    {{ $user->ormawa?->name ?? '-' }}
                                </td>

                                <td>
                                    {{ $user->programStudi?->name ?? '-' }}
                                </td>

                                <td>

                                    @if ($user->is_active)

                                        <span class="badge bg-success-subtle text-success">
                                            <i class="bi bi-circle-fill me-1"
                                               style="font-size:7px;"></i>
                                            Aktif
                                        </span>

                                    @else

                                        <span class="badge bg-secondary-subtle text-secondary">
                                            <i class="bi bi-circle-fill me-1"
                                               style="font-size:7px;"></i>
                                            Nonaktif
                                        </span>

                                    @endif

                                </td>

                                <td class="text-end pe-4">

                                    <div class="btn-group">

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-light"
                                            wire:click="edit({{ $user->id }})"
                                            title="Edit"
                                        >
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        @if ($user->id !== auth()->id())

                                            <button
                                                type="button"
                                                class="btn btn-sm btn-light"
                                                wire:click="toggleStatus({{ $user->id }})"
                                                title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                            >
                                                @if ($user->is_active)
                                                    <i class="bi bi-person-slash text-danger"></i>
                                                @else
                                                    <i class="bi bi-person-check text-success"></i>
                                                @endif
                                            </button>

                                            <button
                                                type="button"
                                                class="btn btn-sm btn-light text-danger"
                                                wire:click="deleteUser({{ $user->id }})"
                                                wire:confirm="Hapus akun {{ $user->name }}? Akun dengan riwayat kegiatan tidak dapat dihapus."
                                                title="Hapus pengguna"
                                            >
                                                <i class="bi bi-trash"></i>
                                            </button>

                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="7" class="text-center py-5">

                                    <div class="text-muted">

                                        <i
                                            class="bi bi-people fs-1 d-block mb-3"
                                        ></i>

                                        <h6 class="fw-semibold">
                                            Belum ada pengguna
                                        </h6>

                                        <p class="mb-0">
                                            Silakan tambahkan pengguna baru.
                                        </p>

                                    </div>

                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        @if ($users->hasPages())

            <div class="card-footer bg-white border-0">
                {{ $users->links() }}
            </div>

        @endif

    </div>


    {{-- MODAL TAMBAH / EDIT --}}
    @if ($showModal)

        <div
            class="modal fade show d-block"
            tabindex="-1"
            style="background: rgba(0,0,0,.5);"
        >

            <div class="modal-dialog modal-lg modal-dialog-centered">

                <div class="modal-content border-0 shadow-lg">

                    <div class="modal-header">

                        <div>
                            <h5 class="modal-title fw-bold">
                                {{ $isEdit ? 'Edit Pengguna' : 'Tambah Pengguna' }}
                            </h5>

                            <small class="text-muted">
                                {{ $isEdit
                                    ? 'Perbarui data akun pengguna.'
                                    : 'Buat akun pengguna baru untuk SIOM.'
                                }}
                            </small>
                        </div>

                        <button
                            type="button"
                            class="btn-close"
                            wire:click="closeModal"
                        ></button>

                    </div>


                    <form wire:submit="save">

                        <div class="modal-body">

                            <div class="row g-3">

                                {{-- NAMA --}}
                                <div class="col-md-6">

                                    <label class="form-label fw-semibold">
                                        Nama Lengkap
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control @error('name') is-invalid @enderror"
                                        wire:model="name"
                                        placeholder="Masukkan nama lengkap"
                                    >

                                    @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>


                                {{-- EMAIL --}}
                                <div class="col-md-6">

                                    <label class="form-label fw-semibold">
                                        Email
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        type="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        wire:model="email"
                                        placeholder="nama@kampus.ac.id"
                                    >

                                    @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>


                                {{-- ROLE --}}
                                <div class="col-md-6">

                                    <label class="form-label fw-semibold">
                                        Role
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        class="form-select @error('role_id') is-invalid @enderror"
                                        wire:model="role_id"
                                    >

                                        <option value="">
                                            Pilih Role
                                        </option>

                                        @foreach ($roles as $role)

                                            <option value="{{ $role->id }}">
                                                {{ $role->display_name }}
                                            </option>

                                        @endforeach

                                    </select>

                                    @error('role_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>


                                {{-- ORMAWA --}}
                                <div class="col-md-6">

                                    <label class="form-label fw-semibold">
                                        ORMAWA
                                    </label>

                                    <select
                                        class="form-select @error('ormawa_id') is-invalid @enderror"
                                        wire:model="ormawa_id"
                                    >

                                        <option value="">
                                            Tidak ada / pilih ORMAWA
                                        </option>

                                        @foreach ($ormawas as $ormawa)

                                            <option value="{{ $ormawa->id }}">
                                                {{ $ormawa->name }}
                                            </option>

                                        @endforeach

                                    </select>

                                    @error('ormawa_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>


                                {{-- PROGRAM STUDI --}}
                                <div
                                    class="col-md-6"
                                    x-data="{ rolesRequiringProdi: @js($roles->whereIn('name', ['sekretaris_ormawa', 'kaprodi'])->pluck('id')->map(fn ($id) => (string) $id)->values()->all()) }"
                                >

                                    <label class="form-label fw-semibold">
                                        Program Studi
                                    </label>

                                    <select
                                        class="form-select @error('program_studi_id') is-invalid @enderror"
                                        wire:model.live="program_studi_id"
                                        x-show="rolesRequiringProdi.includes(String($wire.role_id ?? ''))"
                                    >

                                        <option value="">
                                            Tidak ada / pilih Program Studi
                                        </option>

                                        @foreach ($programStudis as $prodi)

                                            <option value="{{ $prodi->id }}">
                                                {{ $prodi->name }}
                                            </option>

                                        @endforeach

                                    </select>

                                    @error('program_studi_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                    <small
                                        class="text-muted"
                                        x-show="!rolesRequiringProdi.includes(String($wire.role_id ?? ''))"
                                    >
                                        Program Studi hanya berlaku untuk Sekretaris ORMAWA dan KAPRODI.
                                    </small>

                                </div>

                                {{-- NUPTK --}}

                                <div class="mb-3">
                                    <label class="form-label">
                                        NUPTK
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        wire:model="nuptk"
                                        placeholder="Masukkan NUPTK jika pejabat/dosen"
                                    >

                                    @error('nuptk')
                                        <div class="text-danger small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>


                                {{-- TELEPON --}}
                                <div class="col-md-6">

                                    <label class="form-label fw-semibold">
                                        Nomor Telepon
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        wire:model="phone"
                                        placeholder="08xxxxxxxxxx"
                                    >

                                </div>


                                {{-- PASSWORD --}}
                                <div class="col-md-6">

                                    <label class="form-label fw-semibold">
                                        Password

                                        @if ($isEdit)
                                            <small class="text-muted fw-normal">
                                                (kosongkan jika tidak diubah)
                                            </small>
                                        @else
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>

                                    <input
                                        type="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        wire:model="password"
                                        placeholder="Minimal 8 karakter"
                                    >

                                    @error('password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>


                                {{-- KONFIRMASI PASSWORD --}}
                                <div class="col-md-6">

                                    <label class="form-label fw-semibold">
                                        Konfirmasi Password
                                    </label>

                                    <input
                                        type="password"
                                        class="form-control"
                                        wire:model="password_confirmation"
                                        placeholder="Ulangi password"
                                    >

                                </div>


                                {{-- STATUS --}}
                                <div class="col-12">

                                    <div class="form-check form-switch">

                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            wire:model="is_active"
                                            id="isActive"
                                        >

                                        <label
                                            class="form-check-label fw-semibold"
                                            for="isActive"
                                        >
                                            Akun Aktif
                                        </label>

                                    </div>

                                    <small class="text-muted">
                                        Pengguna hanya dapat login jika akun aktif.
                                    </small>

                                </div>

                            </div>

                        </div>


                        <div class="modal-footer">

                            <button
                                type="button"
                                class="btn btn-light"
                                wire:click="closeModal"
                            >
                                Batal
                            </button>

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                <i class="bi bi-check-lg me-1"></i>

                                {{ $isEdit
                                    ? 'Simpan Perubahan'
                                    : 'Simpan Pengguna'
                                }}
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    @endif

</div>