<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Ormawa extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function members()
    {
        return $this->hasMany(OrganizationMember::class);
    }

    public function divisions()
    {
        return $this->hasMany(OrganizationDivision::class);
    }

    public function budgets()
    {
        return $this->hasMany(OrganizationBudget::class);
    }

    public function cashAccounts()
    {
        return $this->hasMany(CashAccount::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }

    public function lpjs()
    {
        return $this->hasMany(Lpj::class);
    }

    /**
     * @return array<string, string>
     */
    public function memberPositionOptions(): array
    {
        $options = [
            'staff_muda' => 'Staff Muda',
            'staff_ahli' => 'Staff Ahli',
            'kepala_divisi_komite' => 'Kepala Divisi/Komite',
            'sekretaris' => 'Sekretaris',
            'bendahara' => 'Bendahara',
            $this->leadershipPosition() => $this->leadershipTitle(),
        ];

        $deputyPosition = $this->deputyLeadershipPosition();
        if ($deputyPosition !== null) {
            $options[$deputyPosition] = $this->deputyLeadershipTitle() ?? '';
        }

        return $options;
    }

    public function leadershipPosition(): string
    {
        if ($this->isBem()) {
            return 'presiden_mahasiswa';
        }

        if ($this->isDpm()) {
            return 'ketua_dewan';
        }

        return 'ketua_himpunan';
    }

    public function leadershipTitle(): string
    {
        return match ($this->leadershipPosition()) {
            'presiden_mahasiswa' => 'Presiden Mahasiswa',
            'ketua_dewan' => 'Ketua Dewan',
            default => 'Ketua Himpunan',
        };
    }

    public function deputyLeadershipPosition(): ?string
    {
        if ($this->isDpm()) {
            return null;
        }

        return $this->isBem()
            ? 'wakil_presiden_mahasiswa'
            : 'wakil_ketua_himpunan';
    }

    public function deputyLeadershipTitle(): ?string
    {
        if ($this->isDpm()) {
            return null;
        }

        return $this->isBem()
            ? 'Wakil Presiden Mahasiswa'
            : 'Wakil Ketua Himpunan';
    }

    public function formatMemberPosition(?string $position, ?int $positionNumber = null): string
    {
        $title = $this->memberPositionOptions()[$position] ?? $position ?? '-';

        if ($positionNumber === null || ! in_array($position, [
            'wakil_ketua_himpunan',
            'wakil_presiden_mahasiswa',
        ], true)) {
            return $title;
        }

        return $title.' '.$this->toRomanNumeral($positionNumber);
    }

    private function isBem(): bool
    {
        return mb_strtoupper(trim($this->code)) === 'BEM'
            || Str::contains(Str::lower($this->name), 'badan eksekutif mahasiswa');
    }

    private function isDpm(): bool
    {
        return mb_strtoupper(trim($this->code)) === 'DPM'
            || Str::contains(Str::lower($this->name), 'dewan perwakilan mahasiswa');
    }

    private function toRomanNumeral(int $number): string
    {
        $numerals = [
            1000 => 'M',
            900 => 'CM',
            500 => 'D',
            400 => 'CD',
            100 => 'C',
            90 => 'XC',
            50 => 'L',
            40 => 'XL',
            10 => 'X',
            9 => 'IX',
            5 => 'V',
            4 => 'IV',
            1 => 'I',
        ];
        $result = '';

        foreach ($numerals as $value => $numeral) {
            while ($number >= $value) {
                $result .= $numeral;
                $number -= $value;
            }
        }

        return $result;
    }
}
