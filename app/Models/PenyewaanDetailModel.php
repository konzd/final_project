<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenyewaanDetailModel extends Model
{
    use HasFactory;

    protected $table = 'penyewaan_detail';
    protected $primaryKey = 'penyewaan_detail_id';

    protected $fillable = [
        'penyewaan_detail_penyewaan_id',
        'penyewaan_detail_alat_id',
        'penyewaan_detail_jumlah',
        'penyewaan_detail_subharga',
    ];

    public function penyewaan()
    {
        return $this->belongsTo(PenyewaanModel::class, 'penyewaan_detail_penyewaan_id', 'penyewaan_id');
    }

    public function alat()
    {
        return $this->belongsTo(AlatModel::class, 'penyewaan_detail_alat_id', 'alat_id');
    }

    public static function getPenyewaanDetail()
    {
        return self::all();
    }

    public static function getPenyewaanDetailById(int $penyewaan_detail_id)
    {
        return self::find($penyewaan_detail_id);
    }

    public static function createPenyewaanDetail($data)
    {
        return self::create($data);
    }

    public static function updatePenyewaanDetail(int $penyewaan_detail_id, $data)
    {
        $penyewaanDetail = self::find($penyewaan_detail_id);
        if ($penyewaanDetail) {
            $penyewaanDetail->update($data);
        }
        return $penyewaanDetail;
    }

    public static function deletePenyewaanDetail(int $penyewaan_detail_id)
    {
        $penyewaanDetail = self::find($penyewaan_detail_id);
        if ($penyewaanDetail) {
            $penyewaanDetail->delete();
        }
        return $penyewaanDetail;
    }
}
