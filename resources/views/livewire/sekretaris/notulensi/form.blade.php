<div class="siom-admin-page siom-notes-form">
    <style>
        .siom-notes-form .siom-notes-back {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            margin-bottom: .6rem;
            color: #1d4ed8;
            font-size: .85rem;
            font-weight: 700;
            text-decoration: none;
        }
        .siom-notes-form .siom-attendance-sheet {
            width: 100%;
        }
        .siom-notes-form .siom-attendance {
            display: grid;
            grid-template-columns: minmax(180px, 1.2fr) 140px minmax(180px, 1.4fr);
            gap: .75rem;
            align-items: center;
            min-width: 640px;
            padding: .8rem 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .siom-notes-form .siom-attendance strong,
        .siom-notes-form .siom-attendance small {
            display: block;
            overflow-wrap: anywhere;
        }
        .siom-notes-form .siom-admin-actions {
            justify-content: flex-end;
            flex-wrap: wrap;
            margin-top: 1.25rem;
        }
        @media (max-width: 760px) {
            .siom-notes-form .siom-attendance-sheet {
                overflow-x: auto;
                padding-bottom: .35rem;
            }
            .siom-notes-form .siom-admin-actions {
                justify-content: stretch;
            }
            .siom-notes-form .siom-admin-actions .siom-admin-btn {
                width: 100%;
            }
        }
    </style>

    <a class="siom-notes-back" href="{{ route('sekretaris.notulensi') }}">
        <i class="bi bi-arrow-left"></i> Kembali ke notulensi
    </a>

    <div class="siom-admin-heading">
        <div>
            <h1>{{ $meetingId ? 'Edit Notulensi' : 'Buat Notulensi Rapat' }}</h1>
            <p>Catat informasi rapat dan kehadiran anggota dalam satu formulir.</p>
        </div>
    </div>

    <form class="siom-admin-card" wire:submit="save">
        <div class="siom-admin-card-body">
            <div class="siom-admin-grid">
                <div class="siom-admin-span-12 siom-admin-field">
                    <label for="meeting-title">Judul rapat</label>
                    <input id="meeting-title" type="text" wire:model="title" placeholder="Contoh: Rapat Kerja ORMAWA">
                    @error('title') <span class="siom-admin-error">{{ $message }}</span> @enderror
                </div>

                <div class="siom-admin-span-6 siom-admin-field">
                    <label for="meeting-date">Tanggal rapat</label>
                    <input id="meeting-date" type="date" wire:model="meeting_date">
                    @error('meeting_date') <span class="siom-admin-error">{{ $message }}</span> @enderror
                </div>

                <div class="siom-admin-span-3 siom-admin-field">
                    <label for="meeting-start">Waktu mulai</label>
                    <input id="meeting-start" type="time" wire:model="start_time">
                    @error('start_time') <span class="siom-admin-error">{{ $message }}</span> @enderror
                </div>

                <div class="siom-admin-span-3 siom-admin-field">
                    <label for="meeting-end">Waktu selesai</label>
                    <input id="meeting-end" type="time" wire:model="end_time">
                    @error('end_time') <span class="siom-admin-error">{{ $message }}</span> @enderror
                </div>

                <div class="siom-admin-span-12 siom-admin-field">
                    <label for="meeting-location">Lokasi</label>
                    <input id="meeting-location" type="text" wire:model="location" placeholder="Lokasi rapat">
                    @error('location') <span class="siom-admin-error">{{ $message }}</span> @enderror
                </div>

                <div class="siom-admin-span-12 siom-admin-field">
                    <label for="meeting-minutes">Isi notulensi</label>
                    <textarea id="meeting-minutes" rows="8" wire:model="minutes_text" placeholder="Tuliskan pembahasan, keputusan, dan tindak lanjut rapat"></textarea>
                    @error('minutes_text') <span class="siom-admin-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-4">
                <h2 class="h5 mb-1">Daftar hadir</h2>
                <p class="small text-muted mb-2">Atur status kehadiran dan catatan bila diperlukan. Geser ke samping pada layar kecil.</p>
                @error('attendances')
                    <div class="siom-admin-alert siom-admin-alert-error">{{ $message }}</div>
                @enderror

                <div class="siom-attendance-sheet">
                    @forelse ($members as $member)
                        <div class="siom-attendance" wire:key="attendance-{{ $member->id }}">
                            <div>
                                <strong>{{ $member->name }}</strong>
                                <small class="text-muted">{{ $member->nim }}</small>
                            </div>
                            <div class="siom-admin-field mb-0">
                                <label class="visually-hidden" for="attendance-status-{{ $member->id }}">Status</label>
                                <select id="attendance-status-{{ $member->id }}" wire:model="attendances.{{ $member->id }}.status">
                                    <option value="hadir">Hadir</option>
                                    <option value="izin">Izin</option>
                                    <option value="sakit">Sakit</option>
                                    <option value="alpa">Alpa</option>
                                </select>
                            </div>
                            <div class="siom-admin-field mb-0">
                                <label class="visually-hidden" for="attendance-note-{{ $member->id }}">Catatan {{ $member->name }}</label>
                                <input id="attendance-note-{{ $member->id }}" type="text" placeholder="Catatan (opsional)" wire:model="attendances.{{ $member->id }}.note">
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Belum ada anggota pada periode aktif.</p>
                    @endforelse
                </div>
            </div>

            <div class="siom-admin-actions">
                <a class="siom-admin-btn" href="{{ route('sekretaris.notulensi') }}">Batal</a>
                <button type="submit" class="siom-admin-btn siom-admin-btn-primary" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">Simpan Notulensi</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            </div>
        </div>
    </form>
</div>