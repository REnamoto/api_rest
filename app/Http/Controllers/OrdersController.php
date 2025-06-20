<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use App\Models\ProductsStocks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     * @OA\Get(
     *     path="/api/orders",
     *     tags={"Orders"},
     *     summary="List all orders",
     *     @OA\Response(
     *         response=200,
     *         description="List all orders"
     *     )
     * )
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Orders::all();
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
     *     path="/api/orders",
     *     tags={"Orders"},
     *     summary="Create a new order",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"client_id", "product_id"},
     *             @OA\Property(property="client_id", type="integer", example=1),
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="quantity", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Order created successfully"
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
            'client_id' => 'required|exists:clients,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Verify if there is enough stock
        $stock = ProductsStocks::where('product_id', $validated['product_id'])->first();

        if (!$stock) {
            return response()->json(['message' => 'Stock not found for the product.'], 404);
        }

        if ($stock->stock < $validated['quantity']) {
            return response()->json(['message' => 'Not enough stock available.'], 422);
        }

        // Create the order
        $order = Orders::create($validated);

        // Update the stock
        $stock->stock -= $validated['quantity'];
        $stock->save();

        return response()->json($order, 201);
    }

    /**
     * Display the specified resource.
     * @OA\GET(
     *     path="/api/orders/{id}",
     *     tags={"Orders"},
     *     summary="Show the order by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the order to retrieve",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     )
     * )
     * 
     * @param  \App\Models\Orders  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Orders $order)
    {
        return response()->json($order);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Orders  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Orders $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * 
     * @OA\Put(
     *     path="/api/orders/{id}",
     *     tags={"Orders"},
     *     summary="Update the order",
     *     description="Update order by ID. You can send only the fields you want to update.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the order to update",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="client_id", type="integer", example=1),
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="quantity", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Orders  $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Orders $order)
    {
        $validated = $request->validate([
            'client_id' => 'sometimes|required|exists:clients,id',
            'product_id' => 'sometimes|required|exists:products,id',
            'quantity' => 'sometimes|required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            // Quantity update requires inventory control
            if (isset($validated['quantity'])) {
                $oldQuantity = $order->quantity;
                $newQuantity = $validated['quantity'];

                // Verifies if quantity has changed
                if ($oldQuantity !== $newQuantity) {
                    // Get the productId  
                    $productId = $validated['product_id'] ?? $order->product_id;

                    // Search the product stock
                    $stock = ProductsStocks::where('product_id', $productId)->first();

                    if (!$stock) {
                        return response()->json(['message' => 'Stock not found for the product.'], 404);
                    }

                    $difference = $newQuantity - $oldQuantity;

                    // If increasing the order quantity, check if there is enough stock
                    if ($difference > 0 && $stock->stock < $difference) {
                        return response()->json(['message' => 'Not enough stock available.'], 422);
                    }

                    // Apply the difference to the stock
                    $stock->stock -= $difference;
                    $stock->save();
                }
            }

            $order->update($validated);

            DB::commit();

            return response()->json($order);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @OA\Delete(
     *     path="/api/orders/{id}",
     *     tags={"Orders"},
     *     summary="Delete the order",
     *     description="Deletes an order by their ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the order to delete",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Order deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     )
     * )
     * 
     * @param  \App\Models\Orders  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Orders $order)
    {
        DB::beginTransaction();

        try {
            $productId = $order->product_id;
            $quantity = $order->quantity;

            // Search the product stock
            $stock = ProductsStocks::where('product_id', $productId)->first();

            if (!$stock) {
                return response()->json(['message' => 'Stock not found for the product.'], 404);
            }

            // Returns the order quantity to the stock
            $stock->stock += $quantity;
            $stock->save();

            // Deletes the order
            $order->delete();

            DB::commit();

            return response()->json(null, 204);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
