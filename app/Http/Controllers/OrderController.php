<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;

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

            $customer = Customer::where('id', $request->customer_id)->first();
            $product = Product::where('id', $request->product_id)->first();

            if (!$customer && !$product) {
                return response()->json([
                    'message' => 'Error on create order.',
                    'errors'  => 'No products or customers found.'
                ], 404, [], JSON_UNESCAPED_SLASHES);
            }

            if ($product->quantity <= 0) {
                return response()->json([
                    'message' => 'Error on create order.',
                    'errors'  => 'Product out of stock.'
                ], 422, [], JSON_UNESCAPED_SLASHES);
            }

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

    public function list(Request $request) {
        try {
            $filters = [
                'id'          => $request->query('id'),
                'customer_id' => $request->query('customer_id'),
                'product_id'  => $request->query('product_id'),
                'value'       => $request->query('value'),
                'finished'    => $request->query('finished'),
                'canceled'    => $request->query('canceled'),
                'limit'       => $request->query('limit')
            ];

            $query = Order::query()->where('deleted', '<>', true);

            foreach ($filters as $filter => $value) {
                if (!is_null($value)) {
                    switch ($filter) {
                        case 'id':
                        case 'customer_id':
                        case 'product_id':
                            $query->where($filter, (int) $value);
                            break;
                        case 'value':
                            $query->where($filter, (float) $value);
                            break;
                        case 'finished':
                        case 'canceled':
                            $query->where($filter, filter_var($value, FILTER_VALIDATE_BOOLEAN));
                            break;
                        case 'limit':
                            $query->limit((int) $value);
                            break;
                    }
                }
            }

            $orders = $query->get();

            if ($orders->isEmpty()) {
                return response()->json([
                    'message' => 'Error on listing orders.',
                    'errors'  => 'Orders not found.'
                ], 404, [], JSON_UNESCAPED_SLASHES);
            }

            return response()->json([
                'limit' => $filters['limit'],
                'total' => $orders->count(),
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
