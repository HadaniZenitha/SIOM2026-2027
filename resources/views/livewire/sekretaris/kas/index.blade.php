<div class="siom-admin-page siom-cash-page">
    <style>
        .siom-cash-page .siom-admin-table {
            min-width: 0;
            width: 100%;
        }
        .siom-cash-page .siom-admin-table th,
        .siom-cash-page .siom-admin-table td {
            white-space: normal;
            vertical-align: top;
        }
        .siom-cash-page .siom-admin-actions {
            flex-wrap: wrap;
        }
        .siom-cash-page .siom-cash-balance {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        .siom-cash-page .siom-cash-balance strong {
            font-family: Fredoka, 'Plus Jakarta Sans', sans-serif;
            font-size: 1.35rem;
        }
        .siom-cash-page .siom-cash-import {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 1rem;
        }
        .siom-cash-page .siom-cash-import .siom-admin-field {
            width: min(100%, 460px);
            margin: 0;
        }
        .siom-cash-page .siom-cash-import-row {
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .siom-cash-page .siom-cash-import-row input {
            min-width: 0;
        }
        .siom-cash-page .siom-cash-cards {
            display: none;
            gap: .75rem;
            padding: 1rem;
        }
        .siom-cash-page .siom-cash-card {
            display: grid;
            gap: .35rem;
            padding: .9rem;
            border: 1.5px solid #0f172a;
            border-radius: 14px;
            background: #f8fbff;
        }
        .siom-cash-page .siom-cash-card strong,
        .siom-cash-page .siom-cash-card small {
            overflow-wrap: anywhere;
        }
        .siom-cash-page .is-income { color: #166534; }
        .siom-cash-page .is-expense { color: #991b1b; }
        @media (max-width: 900px) {
            .siom-cash-page .siom-cash-table { display: none; }
            .siom-cash-page .siom-cash-cards { display: grid; }
            .siom-cash-page .siom-cash-balance,
            .siom-cash-page .siom-cash-import,
            .siom-cash-page .siom-cash-import-row {
                align-items: stretch;
                flex-direction: column;
            }
            .siom-cash-page .siom-cash-import .siom-admin-field,
            .siom-cash-page .siom-cash-import-row .siom-admin-btn,
            .siom-cash-page .siom-cash-cards .siom-admin-btn {
                width: 100%;
            }
        }
    </style>

    <div class="siom-admin-heading">
        <div>
            <h1>Kas ORMAWA</h1>
            <p>Saldo periode {{ $account?->period?->name ?? '-' }}.</p>
        </div>
        <a class="siom-admin-btn siom-admin-btn-primary" href="{{ route('sekretaris.kas.create') }}">
            <i class="bi bi-plus-lg"></i> Tambah Transaksi
        </a>
    </div>

    @if (session('success'))
        <div class="siom-admin-alert siom-admin-alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="siom-admin-alert siom-admin-alert-error">{{ session('error') }}</div>
    @endif

    @if ($account)
        <div class="siom-admin-card">
            <div class="siom-admin-card-body">
                <div class="siom-cash-balance">
                    <div>
                        <p class="mb-1">Saldo saat ini</p>
                        <strong>Rp {{ number_format((float) $account->current_balance, 0, ',', '.') }}</strong>
                    </div>
                    <span class="siom-admin-badge siom-admin-badge-finished">{{ $account->period?->name ?? 'Periode aktif' }}</span>
                </div>
            </div>
        </div>
    @endif

    <section class="siom-admin-card">
        <div class="siom-admin-card-body">
            <div class="siom-cash-import">
                <form wire:submit="importExcel" class="siom-admin-field">
                    <label for="cash-excel">Impor Excel (.xlsx)</label>
                    <div class="siom-cash-import-row">
                        <input id="cash-excel" type="file" accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" wire:model="importFile">
                        <button class="siom-admin-btn siom-admin-btn-primary" type="submit" wire:loading.attr="disabled" wire:target="importExcel,importFile">
                            <span wire:loading.remove wire:target="importExcel">Impor</span>
                            <span wire:loading wire:target="importExcel,importFile">Memproses...</span>
                        </button>
                    </div>
                    @error('importFile') <span class="siom-admin-error">{{ $message }}</span> @enderror
                </form>
                <div class="siom-admin-actions mb-0">
                    <button class="siom-admin-btn siom-admin-btn-sm" type="button" wire:click="downloadImportTemplate">Template</button>
                    <button class="siom-admin-btn siom-admin-btn-sm" type="button" wire:click="exportExcel">Ekspor</button>
                </div>
            </div>
            <p class="siom-admin-help mb-0 mt-2">Jenis income atau expense, jumlah positif, tanggal transaksi. Impor ditolak jika saldo menjadi minus.</p>
        </div>
    </section>

    <div class="siom-admin-card">
        <div class="siom-admin-table-wrap siom-cash-table">
            <table class="siom-admin-table">
                <thead>
                    <tr>
                        <th style="width: 120px;">Tanggal</th>
                        <th style="width: 130px;">Jenis</th>
                        <th>Kategori</th>
                        <th>Keterangan</th>
                        <th style="width: 150px;">Jumlah</th>
                        <th style="width: 180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions ?? [] as $transaction)
                        <tr wire:key="cash-{{ $transaction->id }}">
                            <td>{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                            <td class="{{ $transaction->type === 'income' ? 'is-income' : 'is-expense' }}">
                                {{ $transaction->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                            </td>
                            <td>{{ $transaction->category }}</td>
                            <td>{{ $transaction->description }}</td>
                            <td>Rp {{ number_format((float) $transaction->amount, 0, ',', '.') }}</td>
                            <td>
                                <div class="siom-admin-actions">
                                    <a class="siom-admin-btn siom-admin-btn-sm" href="{{ route('sekretaris.kas.edit', $transaction) }}">Edit</a>
                                    <button type="button" class="siom-admin-btn siom-admin-btn-sm siom-admin-btn-danger" wire:click="delete({{ $transaction->id }})" wire:confirm="Hapus transaksi ini?">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="siom-admin-empty">Belum ada transaksi kas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="siom-cash-cards">
            @forelse ($transactions ?? [] as $transaction)
                <article class="siom-cash-card" wire:key="cash-card-{{ $transaction->id }}">
                    <strong class="{{ $transaction->type === 'income' ? 'is-income' : 'is-expense' }}">
                        {{ $transaction->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }} · Rp {{ number_format((float) $transaction->amount, 0, ',', '.') }}
                    </strong>
                    <span>{{ $transaction->category }}</span>
                    <small class="text-muted">{{ $transaction->transaction_date->format('d/m/Y') }} · {{ $transaction->description }}</small>
                    <div class="siom-admin-actions">
                        <a class="siom-admin-btn siom-admin-btn-sm" href="{{ route('sekretaris.kas.edit', $transaction) }}">Edit</a>
                        <button type="button" class="siom-admin-btn siom-admin-btn-sm siom-admin-btn-danger" wire:click="delete({{ $transaction->id }})" wire:confirm="Hapus transaksi ini?">Hapus</button>
                    </div>
                </article>
            @empty
                <p class="siom-admin-empty">Belum ada transaksi kas.</p>
            @endforelse
        </div>

        @if ($transactions?->hasPages())
            <div class="siom-admin-pagination p-3">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>