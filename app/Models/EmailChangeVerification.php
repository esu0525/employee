<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailChangeVerification extends Model
{
    protected $table = 'email_change_verifications';

    protected $fillable = [
        'user_id',
        'old_email',
        'new_email',
        'token',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired()
    {
        return $this->expires_at->isPast();
    }
}
