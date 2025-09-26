<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'address', 'logo', 'plan', 
        'is_active', 'max_users', 'max_storage_mb', 
        'subscription_expires_at', 'subscription_amount'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'subscription_expires_at' => 'datetime',
        'subscription_amount' => 'decimal:2',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function admins(): HasMany
    {
        return $this->hasMany(Admin::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function designations(): HasMany
    {
        return $this->hasMany(Designation::class);
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    public function isPaid(): bool
    {
        return $this->plan === 'paid' && 
               $this->is_active && 
               ($this->subscription_expires_at === null || $this->subscription_expires_at->isFuture());
    }

    public function canAddUser(): bool
    {
        if ($this->isPaid()) {
            return true;
        }
        return $this->users()->count() < $this->max_users;
    }

    public function getRemainingUsers(): int
    {
        if ($this->isPaid()) {
            return PHP_INT_MAX;
        }
        return max(0, $this->max_users - $this->users()->count());
    }
}