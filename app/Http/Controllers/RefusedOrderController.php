<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\RefusedOrder;

class RefusedOrderController extends Controller {
    
    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|integer',
            'order_id'    => 'required|integer',
            'reason'      => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error on create refused order.',
                'errors'  => $validator->errors()
            ], 422, [], JSON_UNESCAPED_SLASHES);
        }

        try {

            RefusedOrder::create([
                'customer_id' => $request->customer_id,
                'order_id'    => $request->order_id,
                'reason'      => $request->reason
             ]);

            return response()->json([
                'message' => 'Refused order created has successfully!'
            ], 201, [], JSON_UNESCAPED_SLASHES);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error on created refused order.',
                'errors'  => $e->getMessage()
            ], 500, [], JSON_UNESCAPED_SLASHES);
        }
    }

    public function list(Request $request) {
        try {
            $filters = [
                'id'          => $request->query('id'),
                'customer_id' => $request->query('customer_id'),
                'order_id'    => $request->query('order_id'),
                'canceled'    => $request->query('canceled'),
                'limit'       => $request->query('limit')
            ];

            $query = RefusedOrder::query()->where('deleted', '<>', true);

            foreach ($filters as $filter => $value) {
                if (!is_null($value)) {
                    switch ($filter) {
                        case 'id':
                        case 'customer_id':
                        case 'order_id':
                            $query->where($filter, (int) $value);
                            break;
                        case 'canceled':
                            $query->where($filter, filter_var($value, FILTER_VALIDATE_BOOLEAN));
                            break;
                        case 'limit':
                            $query->limit((int) $value);
                            break;
                    }
                }
            }

            $refused_orders = $query->get();

            if ($refused_orders->isEmpty()) {
                return response()->json([
                    'message' => 'Error on listing refused orders.',
                    'errors'  => 'Refused orders not found.'
                ], 404, [], JSON_UNESCAPED_SLASHES);
            }

            return response()->json([
                'limit' => $filters['limit'],
                'total' => count($refused_orders),
                'data'  => $refused_orders
            ], 200, [], JSON_UNESCAPED_SLASHES);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error on listing refused orders.',
                'errors'  => $e->getMessage()
            ], 500, [], JSON_UNESCAPED_SLASHES);
        }
    }

    public function edit(Request $request, int $id) {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'nullable|integer',
            'order_id'    => 'nullable|integer',
            'reason'      => 'nullable|string',
            'canceled'    => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error on editing refused order.',
                'errors'  => $validator->errors()
            ], 422, [], JSON_UNESCAPED_SLASHES);
        }

        try {

            $refused_order = RefusedOrder::where('id', $id)->where('deleted', '<>', true)->first();

            if (!$refused_order) {
                return response()->json([
                    'message' => 'Error on editing refused order.',
                    'errors'  => 'Refused order not found.'
                ], 404, [], JSON_UNESCAPED_JSON);
            }

            $refused_order->customer_id = $request->customer_id;
            $refused_order->order_id = $request->order_id;
            $refused_order->reason = $request->reason;
            $refused_order->canceled = $request->canceled;
            $refused_order->save();

            return response()->json([
                'message' => 'Refused order edited has successfully!'
            ], 200, [], JSON_UNESCAPED_SLASHES);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error on editing refused order.',
                'errors'  => $e->getMessage()
            ], 500, [], JSON_UNESCAPED_SLASHES);
        }
    }

    public function delete(Request $request, int $id) {
        try {

            $refused_order = RefusedOrder::where('id', $id)->where('deleted', '<>', true)->first();

            if (!$refused_order) {
                return response()->json([
                    'message' => 'Error on deleting refused order.',
                    'errors'  => 'Refused order not found.'
                ], 404, [], JSON_UNESCAPED_SLASHES);
            }

            $refused_order->deleted = true;
            $refused_order->save();

            return response()->json([
                'message' => 'Refused order deleted has successfully!'
            ], 200, [], JSON_UNESCAPED_SLASHES);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error on deleting refused order.',
                'errors'  => $e->getMessage()
            ], 500, [], JSON_UNESCAPED_SLASHES);
        }
    }
}
