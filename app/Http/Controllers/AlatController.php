<?php

namespace App\Http\Controllers;

use App\Models\AlatModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Storage;

class AlatController extends Controller
{
    public function index()
    {
        try {
            $alat = AlatModel::with('kategori')->get()->map(function ($item) {
                $item->alat_gambar = $item->alat_gambar ? asset('storage/' . $item->alat_gambar) : null;
                return $item;
            });

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

            $alat->alat_gambar = $alat->alat_gambar ? asset('storage/' . $alat->alat_gambar) : null;

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
                'alat_gambar' => 'nullable|image|mimes:jpg,png,jpeg|max:2048', 
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan data alat! Periksa kembali input Anda.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $gambarPath = null;
            if ($request->hasFile('alat_gambar')) {
                $gambarPath = $request->file('alat_gambar')->store('images', 'public');
            }

            $alat = AlatModel::create([
                'alat_kategori_id' => $request->alat_kategori_id,
                'alat_nama' => $request->alat_nama,
                'alat_deskripsi' => $request->alat_deskripsi,
                'alat_hargaperhari' => $request->alat_hargaperhari,
                'alat_stok' => $request->alat_stok,
                'alat_gambar' => $gambarPath,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data alat berhasil ditambahkan!',
                'data' => $alat,
            ], 201);
        } catch (Exception $error) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server!',
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
                'alat_stok' => 'integer|min:0',
                'alat_gambar' => 'nullable|image|mimes:jpg,png,jpeg|max:2048', 
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update alat. Please check your input.',
                    'errors' => $validator->errors()
                ], 400);
            }

            if ($request->hasFile('alat_gambar')) {
                if ($alat->alat_gambar) {
                    Storage::disk('public')->delete($alat->alat_gambar);
                }
                $alat->alat_gambar = $request->file('alat_gambar')->store('images', 'public');
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
