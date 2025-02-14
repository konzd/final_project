<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PelangganModel extends Model
{
    use HasFactory;

    protected $table = 'pelanggan';
    protected $primaryKey = 'pelanggan_id';

    protected $fillable = [
        'pelanggan_nama',
        'pelanggan_email',
        'pelanggan_notelp',
        'pelanggan_alamat'
    ];

    public function pelangganData()
    {
        return $this->hasMany(PelangganDataModel::class, 'pelanggan_data_pelanggan_id', 'pelanggan_id');
    }

    public static function getPelanggan()
    {
        return self::all();
    }

    public static function getPelangganById(int $pelanggan_id)
    {
        return self::find($pelanggan_id);
    }

    public static function createPelanggan($data)
    {
        return self::create($data);
    }

    public static function updatePelanggan(int $pelanggan_id, $data)
    {
        $pelanggan = self::find($pelanggan_id);
        if ($pelanggan) {
            $pelanggan->update($data);
        }
        return $pelanggan;
    }

    public static function deletePelanggan(int $pelanggan_id)
    {
        $pelanggan = self::find($pelanggan_id);
        if ($pelanggan) {
            $pelanggan->delete();
        }
        return $pelanggan;
    }
}
