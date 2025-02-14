<?php

namespace App\Http\Controllers;

use App\Models\PelangganModel;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;

class PelangganController extends Controller
{
    public function index()
    {
        try {
            $pelanggan = PelangganModel::with('pelangganData')->get();

            return response()->json([
                'success' => true,
                'message' => 'Successfully retrieved pelanggan data.',
                'data' => $pelanggan
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, there is an error in the internal server',
                'data' => null,
                'errors' => $error->getMessage()
            ], 500);
        }
    }

    public function show(int $pelanggan_id)
    {
        try {
            $pelanggan = PelangganModel::with('pelangganData')->find($pelanggan_id);

            if (!$pelanggan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelanggan not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Successfully retrieved pelanggan data.',
                'data' => $pelanggan,
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, there is an error in the internal server',
                'data' => null,
                'errors' => $error->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'pelanggan_nama'   => 'required|string|max:100',
                'pelanggan_email'  => 'required|email|unique:pelanggan,pelanggan_email|max:100',
                'pelanggan_notelp'=> 'required|string|max:15',
                'pelanggan_alamat' => 'required|string|max:200',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create pelanggan. Please check your input.',
                    'data' => null,
                    'errors' => $validator->errors()
                ], 400);
            }

            $pelanggan = PelangganModel::create($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Successfully created pelanggan.',
                'data' => $pelanggan,
            ], 201);
        } catch (Exception $error) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, there is an error in the internal server',
                'data' => null,
                'errors' => $error->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, int $pelanggan_id)
    {
        try {
            $pelanggan = PelangganModel::find($pelanggan_id);

            if (!$pelanggan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelanggan not found',
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'pelanggan_nama'   => 'sometimes|string|max:100',
                'pelanggan_email'  => 'sometimes|email|max:100|unique:pelanggan,pelanggan_email,' . $pelanggan_id . ',pelanggan_id',
                'pelanggan_notelp' => 'sometimes|string|max:15',
                'pelanggan_alamat' => 'sometimes|string|max:200', 
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error.',
                    'errors' => $validator->errors(),
                ], 400);
            }

            $pelanggan->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Successfully patched pelanggan.',
                'data' => $pelanggan,
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, there is an error in the internal server',
                'errors' => $error->getMessage(),
            ], 500);
        }
    }

    public function destroy(int $pelanggan_id)
    {
        try {
            $pelanggan = PelangganModel::find($pelanggan_id);

            if (!$pelanggan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelanggan not found',
                ], 404);
            }

            $pelanggan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Successfully deleted pelanggan.',
                'data' => $pelanggan,
            ], 200);
        } catch (Exception $error) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, there is an error in the internal server',
                'data' => null,
                'errors' => $error->getMessage()
            ], 500);
        }
    }
}
