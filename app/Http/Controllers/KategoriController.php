<?php

namespace App\Http\Controllers;

use App\Models\KategoriModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KategoriController extends Controller
{
    public function index()
    {
        try {
            $kategori = KategoriModel::all();
            return response()->json([
                'success' => true,
                'message' => 'Successfully retrieved kategori data.',
                'data' => $kategori
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error occurred while fetching kategori data.',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function show(int $kategori_id)
    {
        try {
            $kategori = KategoriModel::find($kategori_id);
            if (!$kategori) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori not found.',
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => 'Successfully retrieved kategori data.',
                'data' => $kategori
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error occurred while fetching kategori data.',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori_nama' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $kategori = KategoriModel::createKategori($validator->validated());
            return response()->json([
                'success' => true,
                'message' => 'Successfully created kategori.',
                'data' => $kategori
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error occurred while creating kategori.',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, int $kategori_id)
    {
        try {
            $kategori = KategoriModel::find($kategori_id);
            if (!$kategori) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori not found.',
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'kategori_nama' => 'sometimes|string|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error.',
                    'errors' => $validator->errors()
                ], 400);
            }

            $kategori->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Successfully patched kategori.',
                'data' => $kategori
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error occurred while patching kategori.',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(int $kategori_id)
    {
        try {
            $kategori = KategoriModel::find($kategori_id);
            if (!$kategori) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori not found.',
                ], 404);
            }

            $kategori->delete();
            return response()->json([
                'success' => true,
                'message' => 'Successfully deleted kategori.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error occurred while deleting kategori.',
                'errors' => $e->getMessage()
            ], 500);
        }
    }
}
