<div class="siom-admin-page siom-approval-show">
    <style>
        .siom-approval-show .siom-back {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            margin-bottom: .7rem;
            color: #1d4ed8;
            font-size: .85rem;
            font-weight: 700;
            text-decoration: none;
        }
        .siom-approval-show .siom-doc-list,
        .siom-approval-show .siom-step-cols {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: .45rem;
        }
        .siom-approval-show .siom-step-col {
            display: grid;
            grid-template-columns: 2rem minmax(140px, 1fr) 140px minmax(0, 1.4fr);
            gap: .35rem .75rem;
            align-items: center;
            width: min(100%, 760px);
            padding: .6rem .75rem;
            border: 1.5px solid currentColor;
            border-radius: 12px;
        }
        .siom-approval-show .siom-step-col strong,
        .siom-approval-show .siom-step-col small,
        .siom-approval-show .siom-step-col span { overflow-wrap: anywhere; }
        .siom-approval-show .siom-step-col small { color: inherit; font-size: .78rem; opacity: .85; }
        .siom-approval-show .is-approved { background: #dcfce7; color: #166534; }
        .siom-approval-show .is-pending { background: #fef9c3; color: #854d0e; }
        .siom-approval-show .is-waiting { background: #f1f5f9; color: #475569; }
        .siom-approval-show .is-revision { background: #e0f2fe; color: #075985; }
        .siom-approval-show .is-rejected { background: #fee2e2; color: #991b1b; }
        .siom-approval-show .siom-admin-actions { flex-wrap: wrap; justify-content: flex-end; }
        @media (max-width: 760px) {
            .siom-approval-show .siom-step-col { grid-template-columns: 2rem 1fr; width: 100%; }
            .siom-approval-show .siom-step-col small { grid-column: 2; }
            .siom-approval-show .siom-admin-actions { align-items: stretch; flex-direction: column; }
            .siom-approval-show .siom-admin-actions .siom-admin-btn,
            .siom-approval-show .siom-doc-list .siom-admin-btn { width: 100%; }
        }
    </style>

    <a class="siom-back" href="{{ route('approval.index') }}">
        <i class="bi bi-arrow-left"></i> Kembali ke persetujuan
    </a>

    <div class="siom-admin-heading">
        <div>
            <h1>{{ $lpj->title }}</h1>
            <p>{{ $lpj->ormawa?->name }} · {{ $lpj->period?->name }} · Proposal {{ $lpj->submission?->submission_number }}</p>
        </div>
    </div>

    <section class="siom-admin-card">
        <div class="siom-admin-card-body">
            <h2 class="h5 mb-2">Ringkasan</h2>
            <p class="mb-3">{{ $lpj->description }}</p>
            <div class="siom-doc-list">
                @foreach ($lpj->documents as $document)
                    <button type="button" class="siom-admin-btn siom-admin-btn-sm" wire:click="download({{ $document->id }})">
                        <i class="bi bi-download"></i> {{ $document->file_name }}
                    </button>
                @endforeach
            </div>
        </div>
    </section>

    <section class="siom-admin-card">
        <div class="siom-admin-card-body">
            <h2 class="h5 mb-3">Alur persetujuan</h2>
            <div class="siom-step-cols">
                @foreach ($lpj->approvalSteps as $step)
                    @php $stepStatus = $step->status instanceof \BackedEnum ? $step->status->value : (string) $step->status; @endphp
                    <div class="siom-step-col is-{{ $stepStatus }}">
                        <strong>{{ $step->sequence }}.</strong>
                        <span>{{ $step->role?->display_name ?? 'Tahap' }}</span>
                        <strong>{{ ucfirst(str_replace('_', ' ', $stepStatus)) }}</strong>
                        <small>{{ $step->note }}</small>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @if ($canDecide)
        <form class="siom-admin-card" wire:submit.prevent>
            <div class="siom-admin-card-body">
                <div class="siom-admin-field">
                    <label for="approval-note">Catatan keputusan</label>
                    <textarea id="approval-note" rows="4" wire:model="note"></textarea>
                    @error('note') <span class="siom-admin-error">{{ $message }}</span> @enderror
                </div>
                @error('approval')
                    <div class="siom-admin-alert siom-admin-alert-error">{{ $message }}</div>
                @enderror
                <div class="siom-admin-actions">
                    <button type="button" class="siom-admin-btn siom-admin-btn-danger" wire:click="reject" wire:confirm="Tolak LPJ ini?">Tolak</button>
                    <button type="button" class="siom-admin-btn" wire:click="requestRevision">Minta Revisi</button>
                    <button type="button" class="siom-admin-btn siom-admin-btn-primary" wire:click="approve">Setujui</button>
                </div>
            </div>
        </form>
    @endif
</div>