<?php

namespace App\Livewire\Admin\TemplateDokumen;

use App\Models\DocumentTemplate;
use App\Models\Period;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithFileUploads;

    public $name = '';
    public $type = 'proposal';
    public $ormawa_category = '';
    public $period_id = '';
    public $file;
    public $showForm = false;

    protected $rules = [
        'name'            => 'required|string|max:255',
        'type'            => 'required|in:proposal,lpj',
        'ormawa_category' => 'nullable|in:hima,bem',
        'period_id'       => 'nullable|exists:periods,id',
        'file'            => 'required|file|mimes:docx|max:10240',
    ];

    protected $messages = [
        'name.required' => 'Nama template wajib diisi.',
        'file.required' => 'File template .docx wajib diunggah.',
        'file.mimes' => 'Format file harus ber-ekstensi .docx (Microsoft Word).',
        'file.max' => 'Ukuran file maksimal 10 MB.',
    ];

    public function save()
    {
        $this->validate();

        // Simpan file ke storage public
        $path = $this->file->store('document-templates', 'public');

        DocumentTemplate::create([
            'period_id'       => $this->period_id ?: null,
            'created_by'      => Auth::id(),
            'name'            => $this->name,
            'type'            => $this->type,
            'ormawa_category' => $this->ormawa_category ?: null,
            'file_path'       => $path,
            'file_name'       => $this->file->getClientOriginalName(),
            'version'         => 1,
            'is_active'       => true,
        ]);

        $this->reset(['name', 'period_id', 'ormawa_category', 'file', 'showForm']);
        $this->type = 'proposal';

        session()->flash('success', 'Template dokumen berhasil disimpan.');
    }

    public function toggleStatus($id)
    {
        $template = DocumentTemplate::findOrFail($id);
        $template->update([
            'is_active' => !$template->is_active,
        ]);
        
        session()->flash('success', 'Status template berhasil diperbarui.');
    }

    public function updateCategory($id, $category)
    {
        $template = DocumentTemplate::findOrFail($id);
        $category = in_array($category, ['hima', 'bem'], true) ? $category : null;
        $template->update(['ormawa_category' => $category]);

        session()->flash('success', 'Kategori ormawa template berhasil diperbarui.');
    }

    public function download($id)
    {
        $template = DocumentTemplate::findOrFail($id);

        if (Storage::disk('public')->exists($template->file_path)) {
            return response()->download(
                Storage::disk('public')->path($template->file_path),
                $template->file_name
            );
        }

        session()->flash('error', 'File template tidak ditemukan.');
    }

    public function delete($id)
    {
        $template = DocumentTemplate::findOrFail($id);

        if ($template->file_path && Storage::disk('public')->exists($template->file_path)) {
            Storage::disk('public')->delete($template->file_path);
        }

        $template->delete();

        session()->flash('success', 'Template berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.admin.template-dokumen.index', [
            'templates' => DocumentTemplate::with(['period', 'creator'])
                ->latest()
                ->get(),
            'periods' => Period::orderByDesc('start_date')->get(),
        ]);
    }
}