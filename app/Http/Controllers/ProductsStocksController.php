<?php

namespace App\Http\Controllers;

use App\Models\ProductsStocks;
use Illuminate\Http\Request;

class ProductsStocksController extends Controller
{
    /**
     * Display a listing of the resource.
     * @OA\Get(
     *     path="/api/products_stocks",
     *     tags={"ProductsStocks"},
     *     summary="List all products stocks",
     *     @OA\Response(
     *         response=200,
     *         description="List all products stocks"
     *     )
     * )
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ProductsStocks::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     * @OA\POST(
     *     path="/api/products_stocks",
     *     tags={"ProductsStocks"},
     *     summary="Create a new product stock",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id", "quantity"},
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="quantity", type="integer", example=100)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product stock created successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0',
        ]);

        $productStock = ProductsStocks::create($validated);

        return response()->json($productStock, 201);
    }

    /**
     * Display the specified resource.
     * @OA\GET(
     *     path="/api/products_stocks/{id}",
     *     tags={"ProductsStocks"},
     *     summary="Show the product stock by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the product stock to retrieve",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product stock retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product stock not found"
     *     )
     * )
     *
     * @param  \App\Models\ProductsStocks  $productsStock
     * @return \Illuminate\Http\Response
     */
    public function show(ProductsStocks $productsStock)
    {
        return response()->json($productsStock);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProductsStocks  $productsStocks
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductsStocks $productsStocks)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * 
     * @OA\Put(
     *     path="/api/products_stocks/{id}",
     *     tags={"ProductsStocks"},
     *     summary="Update the product stock",
     *     description="Update product stock by ID. You can send only the fields you want to update.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the product stock to update",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="quantity", type="integer", example=100)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product stock updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product stock not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductsStocks  $productsStock
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, ProductsStocks $productsStock)
    {
        $validated = $request->validate([
            'product_id' => 'sometimes|required|exists:products,id',
            'quantity' => 'sometimes|required|integer|min:0',
        ]);

        $productsStock->update($validated);

        return response()->json($productsStock);
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @OA\Delete(
     *     path="/api/products_stocks/{id}",
     *     tags={"ProductsStocks"},
     *     summary="Delete the product stock",
     *     description="Deletes a product stock by their ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the product stock to delete",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Product stock deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product stock not found"
     *     )
     * )
     *
     * @param  \App\Models\ProductsStocks  $productsStock
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductsStocks $productsStock)
    {
        $productsStock->delete();

        return response()->json(null, 204);
    }
}
