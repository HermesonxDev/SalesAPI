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
            'product_quantity' => 'required|numeric',
            'description'      => 'nullable|string',
            'value'            => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error on create order.',
                'errors'  => $validator->errors()
            ], 422, [], JSON_UNESCAPED_SLASHES);
        }

        try {

            Order::create([
                'customer_id'      => $request->customer_id,
                'product_id'       => $request->product_id,
                'product_quantity' => $request->product_quantity,
                'description'      => $request->description,
                'value'            => $request->value
            ]);

            return response()->json([
                'message' => 'Order created has successfully!'
            ], 201, [], JSON_UNESCAPED_SLASHES);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error on create order.',
                'errors'  => $e->getMessage()
            ], 500, [], JSON_UNESCAPED_SLASHES);
        }
    }

    public function orders(Request $request) {
        try {
            $id = (int) $request->query('id');
            $customer_id = (int) $request->query('customer_id');
            $product_id = (int) $request->query('product_id');
            $value = (float) $request->query('value'); 
            $finished = filter_var($request->query('finished'), FILTER_VALIDATE_BOOLEAN);
            $canceled = filter_var($request->query('canceled'), FILTER_VALIDATE_BOOLEAN);
            $limit = $request->query('limit', null);

            $query = Order::query();

            if ($id) {
                $query->where('id', $id);

                $result = $query->where('deleted', '<>', true)->first();

                if (!$result) {
                    return response()->json([
                        'message' => 'Error on listing orders.',
                        'errors'  => 'Order not found.'
                    ], 404, [], JSON_UNESCAPED_SLASHES);
                }
            }

            if ($customer_id) {
                $query->where('customer_id', $customer_id);

                $result = $query->where('deleted', '<>', true)->get();

                if ($result->isEmpty()) {
                    return response()->json([
                        'message' => 'Error on listing orders.',
                        'errors'  => 'Orders not found.'
                    ], 404, [], JSON_UNESCAPED_SLASHES);
                }
            }

            if ($product_id) {
                $query->where('product_id', $product_id);

                $result = $query->where('deleted', '<>', true)->get();

                if ($result->isEmpty()) {
                    return response()->json([
                        'message' => 'Error on listing orders.',
                        'errors'  => 'Orders not found.'
                    ], 404, [], JSON_UNESCAPED_SLASHES);
                }
            }

            if ($value) {
                $query->where('value', $value);

                $result = $query->where('deleted', '<>', true)->get();

                if ($result->isEmpty()) {
                    return response()->json([
                        'message' => 'Error on listing orders.',
                        'errors'  => 'Orders not found.'
                    ], 404, [], JSON_UNESCAPED_SLASHES);
                }
            }

            if ($finished) {
                $query->where('finished', $finished);

                $result = $query->where('deleted', '<>', true)->get();

                if ($result->isEmpty()) {
                    return response()->json([
                        'message' => 'Error on listing orders.',
                        'errors'  => 'Orders not found.'
                    ], 404, [], JSON_UNESCAPED_SLASHES);
                }
            }

            if ($canceled) {
                $query->where('canceled', $canceled);

                $result = $query->where('deleted', '<>', true)->get();

                if ($result->isEmpty()) {
                    return response()->json([
                        'message' => 'Error on listing orders.',
                        'errors'  => 'Orders not found.'
                    ], 404, [], JSON_UNESCAPED_SLASHES);
                }
            }

            if (!is_null($limit)) {
                $query->limit($limit);
            }

            $orders = $query->where('deleted', '<>', true)->get();

            return response()->json([
                'limit' => $limit,
                'total' => count($orders),
                'data'  => $orders
            ], 200, [], JSON_UNESCAPED_SLASHES);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error on listing orders.',
                'errors'  => $e->getMessage()
            ], 500, [], JSON_UNESCAPED_SLASHES);
        }
    }

    public function edit(Request $request, int $id) {
        $validator = Validator::make($request->all(), [
            'customer_id'      => 'nullable|integer',
            'product_id'       => 'nullable|integer',
            'product_quantity' => 'nullable|numeric',
            'description'      => 'nullable|string',
            'value'            => 'nullable|numeric',
            'finished'         => 'nullable|boolean',
            'canceled'         => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error on editing order.',
                'errors'  => $validator->errors()
            ], 422, [], JSON_UNESCAPED_SLASHES);
        }

        try {

            $order = Order::where('id', $id)->where('deleted', '<>', true)->first();

            if (!$order) {
                return response()->json([
                    'message' => 'Error on editing order.',
                    'errors'  => 'Order not found.'
                ], 404, [], JSON_UNESCAPED_SLASHES);
            }

            $order->customer_id = $request->customer_id;
            $order->product_id = $request->product_id;
            $order->product_quantity = $request->product_quantity;
            $order->description = $request->description;
            $order->value = $request->value;
            $order->finished = $request->finished;
            $order->canceled = $request->canceled;
            $order->save();

            return response()->json([
                'message' => 'Order edited has successfully!'
            ], 200, [], JSON_UNESCAPED_SLASHES);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error on editing order.',
                'errors'  => $e->getMessage()
            ], 500, [], JSON_UNESCAPED_SLASHES);
        }
    }

    public function delete(Request $request, int $id) {
        try {
            $order = Order::where('id', $id)->where('deleted', '<>', true)->first();

            if (!$order) {
                return response()->json([
                    'message' => 'Error on deleting order.',
                    'errors'  => 'Order not found.'
                ], 404, [], JSON_UNESCAPED_SLASHES);
            }

            $order->deleted = true;
            $order->save();

            return response()->json([
                'message' => 'Order deleted has successfully!'
            ], 200, [], JSON_UNESCAPED_SLASHES);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error on deleting order.',
                'errors'  => $e->getMessage()
            ], 500, [], JSON_UNESCAPED_SLASHES);
        }
    }
}
