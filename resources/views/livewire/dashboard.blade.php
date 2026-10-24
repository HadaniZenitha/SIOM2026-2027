<div>

    <div class="mb-4">

        <h1 class="h3 fw-bold">
            Dashboard
        </h1>

        <p class="text-muted mb-0">
            Selamat datang di Sistem Pengajuan Anggaran ORMAWA.
        </p>

    </div>

    <div class="row g-4">

        <div class="col-md-4">

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <small class="text-muted">
                        Nama Pengguna
                    </small>

                    <h5 class="fw-bold mt-2">
                        {{ auth()->user()->name }}
                    </h5>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <small class="text-muted">
                        Role
                    </small>

                    <h5 class="fw-bold mt-2">
                        {{ auth()->user()->role->display_name }}
                    </h5>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <small class="text-muted">
                        Periode Aktif
                    </small>

                    <h5 class="fw-bold mt-2">
                        2026/2027
                    </h5>

                </div>

            </div>

        </div>

    </div>

</div>