<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'organization',
        'user_type',
        'is_verified',
        'two_factor_enabled',
        'account_balance',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

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
            'two_factor_enabled' => 'boolean',
            'is_verified' => 'boolean',
            'account_balance' => 'decimal:2',
            'two_factor_recovery_codes' => 'array',
        ];
    }

    /**
     * Get the documents for the user.
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get the invoices for the user.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the audit logs for the user.
     */
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Check if user has sufficient balance for processing.
     */
    public function hasSufficientBalance(float $amount): bool
    {
        return $this->account_balance >= $amount;
    }

    /**
     * Deduct amount from user balance.
     */
    public function deductBalance(float $amount): bool
    {
        if ($this->hasSufficientBalance($amount)) {
            $this->decrement('account_balance', $amount);
            return true;
        }
        return false;
    }

    /**
     * Add amount to user balance.
     */
    public function addBalance(float $amount): void
    {
        $this->increment('account_balance', $amount);
    }
}
