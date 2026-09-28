<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Staff extends Model
{
    protected $table = 'staff';

    protected $fillable = [
        'username',
        'full_name',
        'email',
        'department',
        'position',
        'contact',
        'fax',
        'branch',
        'employee_no',
        'employee_code',
        'role',
        'login_status',
        'suspend_login',
        'activation_date',
        'disable_date',
        'maximum_attempts',
        'last_login',
        'last_logout',
        'password',
        'group_ids',
        'widget_ids',
        'delegate_ids',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'activation_date' => 'date:Y-m-d',
            'disable_date' => 'date:Y-m-d',
            'group_ids' => 'array',
            'widget_ids' => 'array',
            'delegate_ids' => 'array',
        ];
    }

    public function areas(): BelongsToMany
    {
        return $this->belongsToMany(Area::class);
    }

    public function toDirectory(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'full_name' => $this->full_name,
            'department' => $this->department ?? '',
            'position' => $this->position ?? '',
            'email' => $this->email,
            'contact' => $this->contact ?? '',
            'fax' => $this->fax ?? '',
            'branch' => $this->branch ?? '',
            'employee_no' => $this->employee_no ?? '',
            'employee_code' => $this->employee_code ?? '',
            'user_type' => $this->role,
            'login_status' => $this->login_status,
            'activation_date' => optional($this->activation_date)->toDateString() ?? '',
            'disable_date' => optional($this->disable_date)->toDateString() ?? '',
            'maximum_attempts' => $this->maximum_attempts,
            'suspend_login' => $this->suspend_login,
            'last_login' => $this->last_login ?? '',
            'last_logout' => $this->last_logout ?? '',
            'group_ids' => $this->group_ids ?? [],
            'workspace_ids' => $this->areas->pluck('id')->all(),
            'widget_ids' => $this->widget_ids ?? [],
            'delegate_ids' => $this->delegate_ids ?? [],
        ];
    }
}
