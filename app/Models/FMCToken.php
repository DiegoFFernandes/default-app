<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FMCToken extends Model
{
    use HasFactory;
    protected $table = 'fcm_tokens';
    protected $fillable = [
        'user_id',
        'token',
        'platform',
    ];

    public function updateOrCreateToken($userId, $token, $platform = null)
    {
        return self::updateOrCreate(
            ['token' => $token], // chave única
            [
                'user_id' => $userId,
                'platform' => $platform,
                'last_used_at' => now(),
            ]
        );
    }

    
}
