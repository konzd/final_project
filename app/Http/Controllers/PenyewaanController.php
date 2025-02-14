<?php

namespace App\Http\Controllers;

use App\Models\PenyewaanModel;
use App\Models\PelangganModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PenyewaanController extends Controller
{
    public function index()
    {
        try {
            $penyewaan = PenyewaanModel::with('pelanggan')->get();

            $response = array(
                'success' => true,
                'message' => 'Successfully retrieved penyewaan data.',
                'data' => $penyewaan
            );

            return response()->json($response, 200);
        } catch (Exception $error) {
            $response = array(
                'success' => false,
                'message' => 'Sorry, there is an error in the internal server',
                'data' => null,
                'errors' => $error->getMessage()
            );

            return response()->json($response, 500);
        }
    }

    public function show(int $penyewaan_id)
    {
        try {
            $penyewaan = PenyewaanModel::with(['pelanggan', 'penyewaanDetail'])->find($penyewaan_id);

            if (!$penyewaan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Penyewaan not found',
                ], 404);
            }

            $response = [
                'success' => true,
                'message' => 'Successfully retrieved penyewaan data.',
                'data' => $penyewaan,
            ];

            return response()->json($response, 200);
        } catch (Exception $error) {
            $response = [
                'success' => false,
                'message' => 'Sorry, there is an error in the internal server',
                'data' => null,
                'errors' => $error->getMessage()
            ];

            return response()->json($response, 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'penyewaan_pelanggan_id' => 'required|exists:pelanggan,pelanggan_id',
                'penyewaan_tglsewa' => 'required|date',
                'penyewaan_tglkembali' => 'required|date|after_or_equal:penyewaan_tglsewa',
                'penyewaan_sttspembayaran' => 'required|in:Lunas,Belum Dibayar,DP', 
                'penyewaan_sttskembali' => 'required|in:Sudah Kembali,Belum Kembali', 
                'penyewaan_totalharga' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan data penyewaan!',
                    'data' => null,
                    'errors' => $validator->errors(),
                ], 422); 
            }

            $penyewaan = PenyewaanModel::create($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Data penyewaan berhasil ditambahkan!',
                'data' => $penyewaan,
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

    public function update(Request $request, int $penyewaan_id)
    {
        try {
            $penyewaan = PenyewaanModel::find($penyewaan_id);

            if (!$penyewaan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Penyewaan not found',
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'penyewaan_pelanggan_id' => 'sometimes|exists:pelanggan,pelanggan_id',
                'penyewaan_tglsewa' => 'sometimes|date',
                'penyewaan_tglkembali' => 'sometimes|date|after_or_equal:penyewaan_tglsewa',
                'penyewaan_sttspembayaran' => 'sometimes|string|max:100',
                'penyewaan_sttskembali' => 'sometimes|string|max:100',
                'penyewaan_totalharga' => 'sometimes|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error.',
                    'errors' => $validator->errors(),
                ], 400);
            }

            $penyewaan->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Successfully patched penyewaan.',
                'data' => $penyewaan,
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, there is an error in the internal server',
                'errors' => $error->getMessage(),
            ], 500);
        }
    }

    public function destroy(int $penyewaan_id)
    {
        try {
            $penyewaan = PenyewaanModel::find($penyewaan_id);

            if (!$penyewaan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Penyewaan not found',
                ], 404);
            }

            $penyewaan->delete();

            $response = array(
                'success' => true,
                'message' => 'Successfully deleted penyewaan.',
                'data' => $penyewaan,
            );

            return response()->json($response, 200);
        } catch (Exception $error) {
            $response = array(
                'success' => false,
                'message' => 'Sorry, there is an error in the internal server',
                'data' => null,
                'errors' => $error->getMessage()
            );

            return response()->json($response, 500);
        }
    }
}
