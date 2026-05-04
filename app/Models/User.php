<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
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
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /**
     * Get notifikasi for this user.
     */
    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'user_id')->orderBy('created_at', 'desc');
    }

    /**
     * Get pengumuman that user created.
     */
    public function pengumuman()
    {
        return $this->hasMany(Pengumuman::class, 'user_id');
    }

    /**
     * Get guru profile if user is a teacher.
     */
    public function guru()
    {
        return $this->hasOne(Guru::class, 'user_id');
    }

    public function siswa()
    {
        return $this->hasOne(Siswa::class, 'user_id');
    }

    /**
     * Get teacher salary records created by this user.
     */
    public function gajiGuruDicatat()
    {
        return $this->hasMany(GajiGuru::class, 'dibayar_oleh_user_id');
    }

    /**
     * Get unread notifications count.
     */
    public function getUnreadNotificationCount()
    {
        return $this->notifikasi()->where('is_read', false)->count();
    }
}
