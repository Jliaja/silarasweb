<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Atribut yang bisa diisi secara Mass Assignment.
     * Sudah dibersihkan dari spasi gaib brok!
     */
    protected $fillable = [
        'name',
        'nik',
        'email',
        'no_hp',
        'alamat',
        'tempat_lahir',
        'tgl_lahir',
        'jenis_kelamin',
        'agama',
        'pekerjaan',
        'password',
        'role',
    ];

    /**
     * Atribut yang disembunyikan dari serialisasi JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * 💡 FIX UTAMA: Casting tipe data field agar sinkron otomatis ke MariaDB
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'tgl_lahir'         => 'date', // Paksa tipe teks dari form HTML diconvert jadi format DATE MariaDB asli!
        ];
    }
}