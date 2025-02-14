<?php

namespace App\Http\Controllers;

use App\Models\PelangganDataModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PelangganDataController extends Controller
{
    public function index()
    {
        try {
            $pelangganData = PelangganDataModel::with('pelanggan')->get();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil mendapatkan data pelanggan_data.',
                'data' => $pelangganData
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
            $pelangganData = PelangganDataModel::with('pelanggan')->find($id);
            if (!$pelangganData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data pelanggan_data tidak ditemukan.',
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => 'Berhasil mendapatkan data pelanggan_data.',
                'data' => $pelangganData
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
                'pelanggan_data_pelanggan_id' => 'required|exists:pelanggan,pelanggan_id',
                'pelanggan_data_jenis' => 'required|in:KTP,SIM',
                'pelanggan_data_file' => 'required|file|mimes:jpg,jpeg,png|max:10240', 
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan data pelanggan! Periksa kembali input Anda.',
                    'data' => null,
                    'errors' => $validator->errors()
                ], 422);
            }

            $file = $request->file('pelanggan_data_file');
            if (!$file->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak valid!',
                    'data' => null,
                    'errors' => 'File upload gagal.',
                ], 422);
            }

            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/pelanggan_data'), $filename);

            $fileUrl = url('uploads/pelanggan_data/' . $filename);

            $data = PelangganDataModel::create([
                'pelanggan_data_pelanggan_id' => $request->pelanggan_data_pelanggan_id,
                'pelanggan_data_jenis' => $request->pelanggan_data_jenis,
                'pelanggan_data_file' => $fileUrl, 
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data pelanggan berhasil ditambahkan!',
                'data' => $data,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server!',
                'data' => null,
                'errors' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function update(Request $request, int $pelanggan_data_id)
    {
        $pelangganData = PelangganDataModel::find($pelanggan_data_id);

        if (!$pelangganData) {
            return response()->json([
                'success' => false,
                'message' => 'pelangganData not found.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'pelanggan_data_pelanggan_id' => 'sometimes|exists:pelanggan,pelanggan_id',
            'pelanggan_data_jenis' => 'sometimes|in:KTP,SIM',
            'pelanggan_data_file' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:10240'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 400);
        }

        if ($request->has('pelanggan_data_pelanggan_id')) {
            $pelangganData->pelanggan_data_pelanggan_id = $request->pelanggan_data_pelanggan_id;
        }
        if ($request->has('pelanggan_data_jenis')) {
            $pelangganData->pelanggan_data_jenis = $request->pelanggan_data_jenis;
        }
        if ($request->hasFile('pelanggan_data_file')) {
            $file = $request->file('pelanggan_data_file');
            if ($file->isValid()) {
                if ($pelangganData->pelanggan_data_file) {
                    $oldFilePath = public_path(str_replace(url('/'), '', $pelangganData->pelanggan_data_file));
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }

                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/pelanggan_data'), $filename);
                $pelangganData->pelanggan_data_file = url('uploads/pelanggan_data/' . $filename);
            }
        }

        $pelangganData->save();

        return response()->json([
            'success' => true,
            'message' => 'pelangganData updated successfully.',
            'data' => $pelangganData,
        ], 200);
    }

    public function destroy($id)
    {
        try {
            $pelangganData = PelangganDataModel::find($id);
            if (!$pelangganData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data pelanggan_data tidak ditemukan.',
                ], 404);
            }

            $pelangganData->delete();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil menghapus data pelanggan_data.',
                'data' => $pelangganData
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