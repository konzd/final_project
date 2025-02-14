<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PelangganDataModel extends Model
{
    use HasFactory;

    protected $table = 'pelanggan_data';
    protected $primaryKey = 'pelanggan_data_id';

    protected $fillable = [
        'pelanggan_data_pelanggan_id',
        'pelanggan_data_jenis',
        'pelanggan_data_file',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(PelangganModel::class, 'pelanggan_data_pelanggan_id', 'pelanggan_id');
    }

    public static function getPelangganData()
    {
        return self::all();
    }

    public static function getPelangganDataById(int $id)
    {
        return self::find($id);
    }

    public static function createPelangganData($data)
    {
        return self::create($data);
    }

    public static function updatePelangganData(int $id, $data)
    {
        $pelangganData = self::find($id);
        if ($pelangganData) {
            $pelangganData->update($data);
        }
        return $pelangganData;
    }

    public static function deletePelangganData(int $id)
    {
        $pelangganData = self::find($id);
        if ($pelangganData) {
            $pelangganData->delete();
        }
        return $pelangganData;
    }
}
