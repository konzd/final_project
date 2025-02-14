<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriModel extends Model
{
    use HasFactory;

    protected $table = 'kategori';
    protected $primaryKey = 'kategori_id';
    protected $fillable = [
        'kategori_nama',
    ];

    public function alat()
    {
        return $this->hasMany(AlatModel::class, 'alat_kategori_id', 'kategori_id');
    }

    public static function getKategori()
    {
        return self::all();
    }

    public static function getKategoriById(int $kategori_id)
    {
        return self::find($kategori_id);
    }

    public static function createKategori(array $data)
    {
        return self::create($data);
    }

    public static function updateKategori(int $kategori_id, array $data)
    {
        $kategori = self::find($kategori_id);
        if ($kategori) {
            $kategori->update($data);
        }
        return $kategori;
    }

    public static function deleteKategori(int $kategori_id)
    {
        $kategori = self::find($kategori_id);
        if ($kategori) {
            $kategori->delete();
        }
        return $kategori;
    }
}
