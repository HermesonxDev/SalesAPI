<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Payment;

class PaymentController extends Controller {
    
    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|integer',
            'order_id'    => 'required|integer',
            'description' => 'nullable|string',
            'value'       => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error on create payment.',
                'errors'  => $validator->errors()
            ], 422, [], JSON_UNESCAPED_SLASHES);
        }

        try {

            Payment::create([
                'customer_id' => $request->customer_id,
                'order_id'    => $request->order_id,
                'description' => $request->description,
                'value'       => $request->value
            ]);

            return response()->json([
                'message' => 'Payment created has sucessfully.'
            ], 201, [], JSON_UNESCAPED_SLASHES);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error on create payment.',
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
                'value'       => $request->query('value'),
                'finished'    => $request->query('finished'),
                'canceled'    => $request->query('canceled'),
                'limit'       => $request->query('limit')
            ];

            $query = Payment::query()->where('deleted', '<>', true);

            foreach ($filters as $filter => $value) {
                if(!is_null($value)) {
                    switch ($filter) {
                        case 'id':
                        case 'customer_id':
                        case 'order_id':
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

            $payments = $query->get();

            if ($payments->isEmpty()) {
                return response()->json([
                    'message' => 'Error on listing payments.',
                    'errors'  => 'Payments not found.'
                ], 404, [], JSON_UNESCAPED_SLASHES);
            }

            return response()->json([
                'limit' => $filters['limit'],
                'total' => count($payments),
                'data'  => $payments
            ], 200, [], JSON_UNESCAPED_SLASHES);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error on listing payments.',
                'errors'  => $e->getMessage()
            ], 500, [], JSON_UNESCAPED_SLASHES);
        }
    }

    public function edit(Request $request, int $id) {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|integer',
            'order_id'    => 'required|integer',
            'description' => 'nullable|string',
            'value'       => 'nullable|numeric',
            'finished'    => 'nullable|boolean',
            'canceled'    => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error on editing payment.',
                'errors'  => $validator->errors()
            ], 422, [], JSON_UNESCAPED_SLASHES);
        }

        try {

            $payment = Payment::where('id', $id)->where('deleted', '<>', true)->first();

            if (!$payment) {
                return response()->json([
                    'message' => 'Error on editing payment.',
                    'errors'  => 'Payment not found.'
                ], 404, [], JSON_UNESCAPED_SLASHES);
            }

            $payment->customer_id = $request->customer_id;
            $payment->order_id = $request->order_id;
            $payment->description = $request->description;
            $payment->value = $request->value;
            $payment->finished = $request->finished;
            $payment->canceled = $request->canceled;
            $payment->save();

            return response()->json([
                'message' => 'Payment edited has successfully!'
            ], 200, [], JSON_UNESCAPED_SLASHES);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error on editing payment.',
                'errors'  => $e->getMessage()
            ], 500, [], JSON_UNESCAPED_SLASHES);
        }
    }

    public function delete(Request $request, int $id) {
        try {
            $payment = Payment::where('id', $id)->where('deleted', '<>', true)->first();

            if (!$payment) {
                return response()->json([
                    'message' => 'Error on deleting payment.',
                    'errors'  => 'Payment not found'
                ], 404, [], JSON_UNESCAPED_SLASHES);
            }

            $payment->deleted = true;
            $payment->save();

            return response()->json([
                'message' => 'Payment deleted has successfully!'
            ], 200, [], JSON_UNESCAPED_SLASHES);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error on deleting payment.',
                'errors'  => $e->getMessage()
            ], 500, [], JSON_UNESCAPED_SLASHES);
        }
    }
}
