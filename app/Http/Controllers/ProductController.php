<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller {
    
    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string',
            'description' => 'nullable|string',
            'quantity'    => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error on product creation.',
                'errors'  => $validator->errors()
            ], 422, [], JSON_UNESCAPED_SLASHES);
        }

        try {
            Product::create([
                'name'        => $request->name,
                'description' => $request->description,
                'quantity'    => $request->quantity
            ]);

            return response()->json([
                'message' => 'Product created has successfully!'
            ], 201, [], JSON_UNESCAPED_SLASHES);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error on product creation.',
                'errors'  => $e->getMessage()
            ], 500, [], JSON_UNESCAPED_SLASHES)
        }
    }


    public function products(Request $request) {
        try {
            $id = (int) $request->query('id');
            $name = $request->query('name');
            $active = filter_var($request->query('active'), FILTER_VALIDATE_BOOLEAN);
            $limit = $request->query('limit', null);

            $query = Product::query();

            if ($id) {
                $query->where('id', $id);

                $result = $query->where('deleted', '<>', true)->first();

                if (!$result) {
                    return response()->json([
                        'message' => 'Error on listing products.',
                        'errors'  => 'Product not found.'
                    ], 404, [], JSON_UNESCAPED_SLASHES);
                }
            }

            if ($name) {
                $query->where('name', 'like', "%{$name}%");

                $result = $query->where('deleted', '<>', true)->get();

                if ($result->isEmpty()) {
                    return response()->json([
                        'message' => 'Error on listing products.',
                        'errors'  => 'Products not found.'
                    ], 404, [], JSON_UNESCAPED_SLASHES);
                }
            }

            if ($active) {
                $query->where('active', $active);

                $result = $query->where('deleted', '<>', true)->get();

                if ($result->isEmpty()) {
                    return response()->json([
                        'message' => 'Error on listing products.',
                        'errors'  => 'Products not found.'
                    ], 404, [], JSON_UNESCAPED_SLASHES);
                }
            }

            if (!is_null($limit)) {
                $query->limit($limit);
            }

            $products = $query->where('deleted', '<>', true)->get();

            return response()->json([
                'limit' => $limit,
                'total' => count($products),
                'data'  => $products
            ], 200, [], JSON_UNESCAPED_SLASHES);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error on listing products.',
                'errors'  => $e->getMessage()
            ], 500, [], JSON_UNESCAPED_SLASHES);
        }
    }


    public function edit(Request $request, int $id) {
        $validator = Validator::make($request->all(), [
            'name'        => 'nullable|string',
            'description' => 'nullable|string',
            'quantity'    => 'nullable|numeric',
            'active'      => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error on editing product.',
                'errors'  => $validator->errors()
            ], 422, [], JSON_UNESCAPED_SLASHES);
        }

        try {
            $product = Product::where('id', $id)->where('deleted', '<>', true)->first();

            if (!$product) {
                return response()->json([
                    'message' => 'Error on editing product.',
                    'errors'  => 'Product not found.'
                ], 404, [], JSON_UNESCAPED_SLASHES);
            }

            $product->name = $request->name;
            $product->description = $request->description;
            $product->quantity = $request->quantity;
            $product->active = filter_var($request->active, FILTER_VALIDATE_BOOLEAN);
            $product->save();

            return response()->json([
                'message' => 'Product edited has successfully!'
            ], 200, [], JSON_UNESCAPED_SLASHES);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error on editing product.',
                'errors'  => $e->getMessage()
            ], 500, [], JSON_UNESCAPED_SLASHES);
        }
    }


    public function delete(Request $request, int $id) {
        try {
            $product = Product::where('id', $id)->where('deleted', '<>', true)->first();

            if (!$product) {
                return response()->json([
                    'message' => 'Error on deleting product.',
                    'errors'  => 'Product not found.'
                ], 404, [], JSON_UNESCAPED_SLASHES);
            }

            $product->deleted = true;
            $product->save();

            return response()->json([
                'message' => 'Product deleted has successfully!'
            ], 200, [], JSON_UNESCAPED_SLASHES);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error on deleting product.',
                'errors'  => $e->getMessage()
            ], 500, [], JSON_UNESCAPED_SLASHES);
        }
    }
}
