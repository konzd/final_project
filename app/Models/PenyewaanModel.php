<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenyewaanModel extends Model
{
    use HasFactory;

    protected $table = 'penyewaan';

    protected $primaryKey = 'penyewaan_id';

    protected $fillable = array(
        'penyewaan_pelanggan_id',
        'penyewaan_tglsewa',
        'penyewaan_tglkembali',
        'penyewaan_sttspembayaran',
        'penyewaan_sttskembali',
        'penyewaan_totalharga',
    );

    public function pelanggan()
    {
        return $this->belongsTo(PelangganModel::class, 'penyewaan_pelanggan_id');
    }

    public function penyewaanDetail()
    {
        return $this->hasMany(PenyewaanDetailModel::class, 'penyewaan_detail_penyewaan_id');
    }

    public static function getPenyewaan()
    {
        return self::all();
    }

    public static function getPenyewaanById(int $penyewaan_id)
    {
        return self::find($penyewaan_id);
    }

    public static function createPenyewaan($data)
    {
        return self::create($data);
    }

    public static function updatePenyewaan(int $penyewaan_id, $data)
    {
        $penyewaan = self::find($penyewaan_id);
        if ($penyewaan) {
            $penyewaan->update($data);
        }
        return $penyewaan;
    }

    public static function deletePenyewaan(int $penyewaan_id)
    {
        $penyewaan = self::find($penyewaan_id);
        if ($penyewaan) {
            $penyewaan->delete();
        }
        return $penyewaan;
    }
}
