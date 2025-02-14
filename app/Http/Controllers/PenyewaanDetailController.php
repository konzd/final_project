<?php

namespace App\Http\Controllers;

use App\Models\PenyewaanDetailModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PenyewaanDetailController extends Controller
{
    public function index()
    {
        try {
            $penyewaanDetail = PenyewaanDetailModel::with(['penyewaan', 'alat'])->get();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil mendapatkan data penyewaan_detail.',
                'data' => $penyewaanDetail
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server.',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $penyewaanDetail = PenyewaanDetailModel::with(['penyewaan', 'alat'])->find($id);
            if (!$penyewaanDetail) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data penyewaan_detail tidak ditemukan.',
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => 'Berhasil mendapatkan data penyewaan_detail.',
                'data' => $penyewaanDetail
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server.',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'penyewaan_detail_penyewaan_id' => 'required|exists:penyewaan,penyewaan_id',
                'penyewaan_detail_alat_id' => 'required|exists:alat,alat_id',
                'penyewaan_detail_jumlah' => 'required|integer|min:1',
                'penyewaan_detail_subharga' => 'required|integer|min:1', 
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan detail penyewaan!',
                    'data' => null,
                    'errors' => $validator->errors(),
                ], 422); 
            }

            $penyewaanDetail = PenyewaanDetailModel::create($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Detail penyewaan berhasil ditambahkan!',
                'data' => $penyewaanDetail,
            ], 201);

        } catch (\Exception $error) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server!',
                'data' => null,
                'errors' => $error->getMessage(),
            ], 500);
        }
}

    public function update(Request $request, $id)
    {
        try {
            $penyewaanDetail = PenyewaanDetailModel::find($id);
            if (!$penyewaanDetail) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data penyewaan_detail tidak ditemukan.',
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'penyewaan_detail_penyewaan_id' => 'required|exists:penyewaan,penyewaan_id',
                'penyewaan_detail_alat_id' => 'required|exists:alat,alat_id',
                'penyewaan_detail_jumlah' => 'required|integer|min:1',
                'penyewaan_detail_subharga' => 'required|integer|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi data gagal.',
                    'errors' => $validator->errors(),
                ], 400);
            }

            $penyewaanDetail->update($validator->validated());
            return response()->json([
                'success' => true,
                'message' => 'Berhasil memperbarui data penyewaan_detail.',
                'data' => $penyewaanDetail
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server.',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $penyewaanDetail = PenyewaanDetailModel::find($id);
            if (!$penyewaanDetail) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data penyewaan_detail tidak ditemukan.',
                ], 404);
            }

            $penyewaanDetail->delete();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil menghapus data penyewaan_detail.',
                'data' => $penyewaanDetail
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server.',
                'errors' => $e->getMessage()
            ], 500);
        }
    }
}
