<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleMenuSetting extends Model
{
    protected $fillable = [
        'role_id',
        'user_id',
        'access_scope',
        'menu_key',
        'enabled',
        'auto_enable_at',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'auto_enable_at' => 'datetime',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
