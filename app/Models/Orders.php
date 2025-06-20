<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\ProductsStocks;

class Orders extends Model
{
    protected $fillable = ['client_id', 'product_id'];
    
    use HasFactory;

    public static function storeWithStockControl(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Get the product stock
            $stock = ProductsStocks::where('product_id', $data['product_id'])->firstOrFail();

            // Check if there is stock for the product
            if (!$stock) {
                throw new \Exception("Stock not found for the product.");
            }

            // Check if there is enough stock for the order
            if ($stock->stock < $data['quantity']) {
                throw new \Exception("Insufficient stock");
            }

            $stock->stock -= $data['quantity'];
            $stock->save();

            return self::create($data);
        });
    }

    public function updateWithStockControl(array $data): self
    {
        return DB::transaction(function () use ($data) {
            $oldQuantity = $this->quantity;
            $newQuantity = $data['quantity'] ?? $oldQuantity;

            // Verifies if the quantity has changed
            if ($newQuantity !== $oldQuantity) {
                $productId = $data['product_id'] ?? $this->product_id;

                $stock = ProductsStocks::where('product_id', $productId)->first();

                if (!$stock) {
                    throw new \Exception('Stock not found for the product.');
                }

                $difference = $newQuantity - $oldQuantity;

                // If increasing the order quantity, check if there is enough stock
                if ($difference > 0 && $stock->stock < $difference) {
                    throw new \Exception('Not enough stock available.');
                }

                // Apply the difference to the stock
                $stock->stock -= $difference;
                $stock->save();
            }

            $this->update($data);

            return $this;
        });
    }

    public function deleteAndRestoreStock(): void
    {
        DB::transaction(function () {
            $productId = $this->product_id;
            $quantity = $this->quantity;

            $stock = ProductsStocks::where('product_id', $productId)->first();

            if (!$stock) {
                throw new \Exception('Stock not found for the product.');
            }

            $stock->stock += $quantity;
            $stock->save();

            $this->delete();
        });
    }
}
