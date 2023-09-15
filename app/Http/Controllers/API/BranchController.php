<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function list(Request $request)//api
    {
        try {
            return response()->json([
                'status' => 'success',
                'message' => 'Branch list.',
                "data" => Branch::get()->toArray()
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
