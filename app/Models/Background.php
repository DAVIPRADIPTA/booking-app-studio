<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Background extends Model
{
    use HasFactory;

    /**
     * Kolom-kolom yang dapat diisi melalui mass assignment.
     * Ini penting untuk keamanan (mencegah Mass Assignment Vulnerability).
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'category',
        'image',
        'is_active',
    ];

    /**
     * Kita menentukan bahwa kolom 'is_active' harus secara otomatis dikonversi ke tipe boolean.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Konstanta untuk menyimpan daftar kategori yang sama dengan yang ada di ENUM database.
     * Ini membuat kode lebih rapi dan konsisten, karena kita hanya perlu mengupdate di satu tempat.
     *
     * @var array
     */
    public const CATEGORIES = [
        'pre-wedding',
        'family',
        'graduation',
        'maternity'
    ];
}