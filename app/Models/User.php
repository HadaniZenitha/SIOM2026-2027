<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role_id', 'ormawa_id', 'program_studi_id', 'phone', 'nuptk', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function ormawa()
    {
        return $this->belongsTo(Ormawa::class);
    }

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    public function hasRole(string $role): bool
    {
        return $this->role?->name === $role;
    }

    /**
     * Mengecek apakah user memiliki salah satu dari beberapa role.
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role?->name, $roles);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'submitted_by');
    }

    public function approvalSteps()
    {
        return $this->hasMany(ApprovalStep::class, 'approved_by');
    }

    public function cashTransactions()
    {
        return $this->hasMany(CashTransaction::class, 'created_by');
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class, 'created_by');
    }

    public function lpjs()
    {
        return $this->hasMany(Lpj::class, 'created_by');
    }
}
