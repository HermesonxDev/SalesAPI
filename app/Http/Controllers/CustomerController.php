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
            'telephone'  => 'nullable|string',
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
                'telephone'  => $request->telephone,
                'cpf_cnpj'   => $request->cpf_cnpj,
                'address'    => $request->address
            ]);

            return response()->json([
                'message' => 'Customer created has successfully!'
            ], 200, [], JSON_UNESCAPED_SLASHES);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error on create customer.',
                'errors'  => $e->getMessage()
            ], 500, [], JSON_UNESCAPED_SLASHES);
        }
    }

    public function list(Request $request) {
        try {
            $filters = [
                'id'         => $request->query('id'),
                'first_name' => $request->query('first_name'),
                'last_name'  => $request->query('last_name'),
                'cpf_cnpj'   => $request->query('cpf_cnpj'),
                'active'     => $request->query('active'),
                'limit'      => $request->query('limit')
            ];

            $query = Customer::query()->where('deleted', '<>', true);

            foreach ($filters as $filter => $value) {
                if (!is_null($value)) {
                    switch ($filter) {
                        case 'id':
                            $query->where($filter, (int) $value);
                            break;
                        case 'first_name':
                        case 'last_name':
                        case 'cpf_cnpj':
                            $query->where($filter, 'like', "%{$value}%");
                            break;
                        case 'active':
                            $query->where($filter, filter_var($value, FILTER_VALIDATE_BOOLEAN));
                            break;
                        case 'limit':
                            $query->limit((int) $value);
                            break;
                    }
                }
            }

            $customers = $query->get();

            if ($customers->isEmpty()) {
                return response()->json([
                    'message' => 'Error on listing customers.',
                    'errors'  => 'Customers not found.'
                ], 404, [], JSON_UNESCAPED_SLASHES);
            }

            return response()->json([
                'limit' => $filters['limit'],
                'total' => count($customers),
                'data'  => $customers
            ], 200, [], JSON_UNESCAPED_SLASHES);

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
            'telephone'  => 'nullable|string',
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
            $customer = Customer::where('id', $id)->where('deleted', '<>', true)->first();

            if (!$customer) {
                return response()->json([
                    'message' => 'Error on editing customer.',
                    'errors'  => 'Customer not found.'
                ], 404, [], JSON_UNESCAPED_SLASHES);
            }

            $customer->first_name = $request->first_name;
            $customer->last_name = $request->last_name;
            $customer->telephone = $request->telephone;
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
            $customer = Customer::where('id', $id)->where('deleted', '<>', true)->first();

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
