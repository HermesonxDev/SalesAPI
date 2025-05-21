<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller {
    
    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name'  => 'required|string',
            'cpf_cnpj'   => 'required|string',
            'address'    => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error on create customer.',
                'errors'  => $validator->errors()
            ], 422, [], JSON_UNESCAPED_SLASHES);
        }

        try {
            Customer::create([
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
                'cpf_cnpj'   => $request->cpf_cnpj,
                'address'    => $request->address
            ]);

            return response()->json([
                'message' => 'Customer created has successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error on create customer.',
                'errors'  => $e->getMessage()
            ], 500, [], JSON_UNESCAPED_SLASHES);
        }
    }

    public function customers(Request $request) {
        try {
            $id = (int) $request->query('id');
            $first_name = $request->query('first_name');
            $last_name = $request->query('last_name');
            $cpf_cnpj = $request->query('cpf_cnpj');
            $active = filter_var($request->query('active'), FILTER_VALIDATE_BOOLEAN);
            $limit = $request->query('limit', null);

            $customers = Customer::query();

            if ($id) {
                $query->where('id', $id);

                $result = $query->where('deleted', '<>', true)->first();

                if (!$result) {
                    return response()->json([
                        'message' => 'Error on listing customers.',
                        'errors'  => 'Customer not found.'
                    ], 404, [], JSON_UNESCAPED_SLASHES);
                }
            }

            if ($first_name) {
                $query->where('first_name', 'like', "%{$first_name}%");

                $result = $query->where('deleted', '<>', true)->get();

                if ($result->isEmpty()) {
                    return response()->json([
                        'message' => 'Error on listing customers.',
                        'errors'  => 'Customers not found.'
                    ], 404, [], JSON_UNESCAPED_SLASHES);
                }
            }

            if ($last_name) {
                $query->where('last_name', 'like', "%{$last_name}%");

                $result = $query->where('deleted', '<>', true)->get();

                if ($result->isEmpty()) {
                    return response()->json([
                        'message' => 'Error on listing customers.',
                        'errors'  => 'Customers not found.'
                    ], 404, [], JSON_UNESCAPED_SLASHES);
                }
            }

            if ($cpf_cnpj) {
                $query->where('cpf_cnpj', 'like', "%{$cpf_cnpj}%");

                $result = $query->where('deleted', '<>', true)->get();

                if ($result->isEmpty()) {
                    return response()->json([
                        'message' => 'Error on listing customers.',
                        'errors'  => 'Customers not found.'
                    ], 404, [], JSON_UNESCAPED_SLASHES)
                }
            }

            if ($active) {
                $query->where('active', $active);

                $result = $query->where('deleted', '<>', true)->get();

                if ($result->isEmpty()) {
                    return response()->json([
                        'message' => 'Error on listing customers.',
                        'errors'  => 'Customers not found.'
                    ], 404, [], JSON_UNESCAPED_SLASHES);
                }
            }

            if (!is_null($limit)) {
                $query->limit($limit);
            }

            $customers->where('deleted', '<>', true)->get();

            return response()->json([
                'limit' => $limit,
                'total' => count($customers),
                'data'  => $customers
            ], 404, [], JSON_UNESCAPED_SLASHES);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error on listing customers.',
                'errors'  => $e->getMessage()
            ], 500, [], JSON_UNESCAPED_SLASHES);
        }
    }

    public function edit(Request $request, int $id) {
        $validator = Validator::make($request->all(), [
            'first_name' => 'nullable|string',
            'last_name'  => 'nullable|string',
            'cpf_cnpj'   => 'nullable|string',
            'address'    => 'nullable|string',
            'active'     => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error on editing customer.',
                'errors'  => $validator->errors()
            ], 422, [], JSON_UNESCAPED_SLASHES);
        }

        try {
            $customer = Customer::where('id', $id)->first();

            if (!$customer) {
                return response()->json([
                    'message' => 'Error on editing customer.',
                    'errors'  => 'Customer not found.'
                ], 404, [], JSON_UNESCAPED_SLASHES);
            }

            $customer->first_name = $request->first_name;
            $customer->last_name = $request->last_name;
            $customer->cpf_cnpj = $request->cpf_cnpj;
            $customer->address = $request->address;
            $customer->active = filter_var($request->active, FILTER_VALIDATE_BOOLEAN);
            $customer->save();

            return response()->json([
                'message' => 'Customer created has successfully!'
            ], 200, [], JOSN_UNESCAPED_SLASHES);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error on editing customer.',
                'errors'  => $e->getMessage()
            ], 500, [], JSON_UNESCAPED_SLASHES);
        }
    }

    public function delete(Request $request, int $id) {
        try {
            $customer = Customer::where('id', $id)->first();

            if (!$customer) {
                return response()->json([
                    'message' => 'Error on deleting customer.',
                    'errors'  => 'Customer not found.'
                ], 404, [], JSON_UNESCAPED_SLASHES);
            }

            $customer->deleted = true;
            $customer->save();

            return response()->json([
                'message' => 'Customer deleted has successfully!'
            ], 200, [], JSON_UNESCAPED_SLASHES);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error on deleting customer.',
                'errors'  => $e->getMessage()
            ], 500, [], JSON_UNESCAPED_SLASHES);
        }
    }
}
