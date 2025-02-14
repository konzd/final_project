<?php

namespace App\Http\Controllers;

use App\Models\AlatModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class AlatController extends Controller
{
    public function index()
    {
        try {
            $alat = AlatModel::with('kategori')->get();

            return response()->json([
                'success' => true,
                'message' => 'Successfully retrieved all alat data.',
                'data' => $alat
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'success' => false,
                'message' => 'Internal server error.',
                'errors' => $error->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $alat = AlatModel::with('kategori')->find($id);
            if (!$alat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alat not found.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Successfully retrieved alat data.',
                'data' => $alat
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'success' => false,
                'message' => 'Internal server error.',
                'errors' => $error->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'alat_kategori_id' => 'required|exists:kategori,kategori_id',
                'alat_nama' => 'required|string|max:150',
                'alat_deskripsi' => 'nullable|string|max:255',
                'alat_hargaperhari' => 'required|integer|min:1',
                'alat_stok' => 'required|integer|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan data alat! Periksa kembali input Anda.',
                    'data' => null,
                    'errors' => $validator->errors()
                ], 422);
            }

            $alat = AlatModel::create($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Data alat berhasil ditambahkan!',
                'data' => $alat,
            ], 201);
        } catch (Exception $error) {
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
            $alat = AlatModel::find($id);
            if (!$alat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alat not found.',
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'alat_kategori_id' => 'exists:kategori,kategori_id',
                'alat_nama' => 'string|max:150',
                'alat_deskripsi' => 'nullable|string|max:255',
                'alat_hargaperhari' => 'integer|min:1',
                'alat_stok' => 'integer|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update alat. Please check your input.',
                    'errors' => $validator->errors()
                ], 400);
            }

            $alat->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Successfully updated alat.',
                'data' => $alat
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'success' => false,
                'message' => 'Internal server error.',
                'errors' => $error->getMessage()
            ], 500);
        }
    }

    // PATCH (Update sebagian)
    public function patch(Request $request, $id)
    {
        try {
            $alat = AlatModel::find($id);
            if (!$alat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alat not found.',
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'alat_kategori_id' => 'exists:kategori,kategori_id',
                'alat_nama' => 'string|max:150',
                'alat_deskripsi' => 'nullable|string|max:255',
                'alat_hargaperhari' => 'integer|min:1',
                'alat_stok' => 'integer|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update alat partially. Please check your input.',
                    'errors' => $validator->errors()
                ], 400);
            }

            $alat->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Successfully patched alat.',
                'data' => $alat
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'success' => false,
                'message' => 'Internal server error.',
                'errors' => $error->getMessage()
            ], 500);
        }
    }

    // DELETE
    public function destroy($id)
    {
        try {
            $alat = AlatModel::find($id);
            if (!$alat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alat not found.',
                ], 404);
            }

            $alat->delete();

            return response()->json([
                'success' => true,
                'message' => 'Successfully deleted alat.'
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'success' => false,
                'message' => 'Internal server error.',
                'errors' => $error->getMessage()
            ], 500);
        }
    }
}
