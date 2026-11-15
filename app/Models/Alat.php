<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alat extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_pemilik',
        'category_id',
        'tipe_id',
        'no_seri',
        'tanggal_tera',
        'masa_berlaku_start',
        'masa_berlaku_end',
        'category_dana_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function categoryDana()
    {
        return $this->belongsTo(CategoryDana::class);
    }

    public function tipe()
    {
        return $this->belongsTo(Tipe::class);
    }
}
