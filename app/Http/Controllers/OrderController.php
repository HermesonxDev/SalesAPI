<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller {
    
    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'customer_id'      => 'required|integer',
            'product_id'       => 'required|integer',
            'product_quantity' => 'required|integer',
            'description'      => 'nullable|string',
            'value'            => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error on order creation',
                'errors'  => $validator->errors()
            ], 422, [], JSON_UNESCAPED_SLASHES);
        }

        try {

            Order::create([
                'customer_id' => 
            ])
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error on order creation',
                'errors'  => $e->getMessage()
            ], 500, [], JSON_UNESCAPED_SLASHES);
        }
    }

    public function orders(Request $request) {

    }

    public function edit(Request $request) {

    }

    public function delete(Request $request) {

    }
}
