<div class="siom-admin-page siom-cash-form">
    <style>
        .siom-cash-form .siom-cash-back {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            margin-bottom: .6rem;
            color: #1d4ed8;
            font-size: .85rem;
            font-weight: 700;
            text-decoration: none;
        }
        .siom-cash-form .siom-admin-card {
            width: 100%;
        }
        .siom-cash-form .siom-admin-actions {
            justify-content: flex-end;
            flex-wrap: wrap;
            margin-top: 1.25rem;
        }
        @media (max-width: 760px) {
            .siom-cash-form .siom-admin-actions { justify-content: stretch; }
            .siom-cash-form .siom-admin-actions .siom-admin-btn { width: 100%; }
        }
    </style>

    <a class="siom-cash-back" href="{{ route('sekretaris.kas') }}">
        <i class="bi bi-arrow-left"></i> Kembali ke kas ORMAWA
    </a>

    <div class="siom-admin-heading">
        <div>
            <h1>{{ $transactionId ? 'Edit Transaksi Kas' : 'Tambah Transaksi Kas' }}</h1>
            <p>Isi detail transaksi dengan lengkap agar saldo kas tercatat dengan benar.</p>
        </div>
    </div>

    <form class="siom-admin-card" wire:submit="save">
        <div class="siom-admin-card-body">
            <div class="siom-admin-grid">
                <div class="siom-admin-span-6 siom-admin-field">
                    <label for="cash-type">Jenis transaksi</label>
                    <select id="cash-type" wire:model="type">
                        <option value="income">Pemasukan</option>
                        <option value="expense">Pengeluaran</option>
                    </select>
                    @error('type') <span class="siom-admin-error">{{ $message }}</span> @enderror
                </div>

                <div class="siom-admin-span-6 siom-admin-field">
                    <label for="cash-category">Kategori</label>
                    <input id="cash-category" type="text" wire:model="category" placeholder="Contoh: Konsumsi, iuran anggota">
                    @error('category') <span class="siom-admin-error">{{ $message }}</span> @enderror
                </div>

                <div class="siom-admin-span-6 siom-admin-field">
                    <label for="cash-amount">Jumlah (Rp)</label>
                    <input id="cash-amount" type="number" min="0.01" step="0.01" wire:model="amount" placeholder="0">
                    @error('amount') <span class="siom-admin-error">{{ $message }}</span> @enderror
                </div>

                <div class="siom-admin-span-6 siom-admin-field">
                    <label for="cash-date">Tanggal transaksi</label>
                    <input id="cash-date" type="date" wire:model="transaction_date">
                    @error('transaction_date') <span class="siom-admin-error">{{ $message }}</span> @enderror
                </div>

                <div class="siom-admin-span-12 siom-admin-field">
                    <label for="cash-description">Keterangan</label>
                    <textarea id="cash-description" rows="4" wire:model="description" placeholder="Tambahkan catatan transaksi (opsional)"></textarea>
                    @error('description') <span class="siom-admin-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="siom-admin-actions">
                <a class="siom-admin-btn" href="{{ route('sekretaris.kas') }}">Batal</a>
                <button type="submit" class="siom-admin-btn siom-admin-btn-primary" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">Simpan Transaksi</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            </div>
        </div>
    </form>
</div>