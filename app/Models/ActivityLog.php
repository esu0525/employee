<?php

namespace App\Models;

// v1.1
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'module',
        'description',
        'ip_address',
        'user_agent'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function log($action, $module, $description = null)
    {
        // Exclude "view" actions from being logged as per user request
        if ($action === 'view') {
            return null;
        }

        // Use standard auth ID if session is missing
        $userId = session('auth_user_id') ?: (auth()->check() ? auth()->id() : null);

        return self::create([
            'user_id' => $userId,
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }
}

