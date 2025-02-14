<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlatModel extends Model
{
    use HasFactory;

    protected $table = 'alat'; 
    protected $primaryKey = 'alat_id'; 

    protected $fillable = [
        'alat_nama',
        'alat_deskripsi',
        'alat_hargaperhari',
        'alat_stok',
        'alat_kategori_id',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriModel::class, 'alat_kategori_id', 'kategori_id');
    }

    public static function getAlat()
    {
        return self::all();
    }

    public static function getAlatById(int $alat_id)
    {
        return self::find($alat_id);
    }

    public static function createAlat(array $data)
    {
        return self::create($data);
    }

    public static function updateAlat(int $alat_id, array $data)
    {
        $alat = self::find($alat_id);
        if ($alat) {
            $alat->update($data);
        }
        return $alat;
    }

    public static function deleteAlat(int $alat_id)
    {
        $alat = self::find($alat_id);
        if ($alat) {
            $alat->delete();
        }
        return $alat;
    }
}