<?php

namespace App\Classes\Manufacturing;

use App\Models\BranchProductPrice;
use App\Models\StockCard;
use App\Models\Store;
use App\Models\StoreProduct;
use Illuminate\Support\Facades\DB;

class ManufacturingCostPrice
{
    const TRANSACTION_TYPE_MANUFACTURING_OUT = 8;
    const TRANSACTION_TYPE_MANUFACTURING_IN = 9;
    const TRANSACTION_TYPE_MANUFACTURING_RETURN = 10;
    const TRANSACTION_TYPE_MANUFACTURING_REWORK = 11;

    /**
     * Recalculate finished goods cost using weighted average method
     * Used after manufacturing to add finished goods to inventory
     *
     * @param int $product_id
     * @param int $store_id
     * @param float $quantity
     * @param float $total_cost
     * @param int $branch_id
     * @param string $batch_no
     * @param string $date
     * @return array
     */
    public static function addFinishedGoods($product_id, $store_id, $quantity, $total_cost, $branch_id, $batch_no, $date)
    {
        $user = auth()->user();

        // Ensure product exists in store
        $storeProduct = StoreProduct::where(['product_id' => $product_id, 'store_id' => $store_id])->first();
        if (!$storeProduct) {
            $storeProduct = StoreProduct::create([
                'product_id' => $product_id,
                'store_id' => $store_id,
                'qty_available' => 0,
            ]);
        }

        // Get existing cost price
        $branchPrice = BranchProductPrice::where(['product_id' => $product_id, 'branch_id' => $branch_id])->first();
        $existing_cost = $branchPrice ? $branchPrice->cost_price : 0;

        // Calculate total existing value
        $store_ids = Store::where('branch_id', $branch_id)->pluck('id')->toArray();
        $existing_qty = StoreProduct::where('product_id', $product_id)
            ->whereIn('store_id', $store_ids)
            ->sum('qty_available');

        $existing_total = $existing_qty * $existing_cost;

        // Calculate new weighted average cost
        $new_total_qty = $existing_qty + $quantity;
        $new_total_value = $existing_total + $total_cost;
        $new_cost = $new_total_qty > 0 ? $new_total_value / $new_total_qty : 0;

        DB::beginTransaction();

        try {
            // Update store product quantity
            $storeProduct->qty_available += $quantity;
            $storeProduct->save();

            // Update branch product price
            BranchProductPrice::updateOrInsert(
                ['branch_id' => $branch_id, 'product_id' => $product_id],
                ['cost_price' => $new_cost, 'updated_by' => $user->id, 'updated_at' => now()]
            );

            // Create stock card entry
            $stock_card_param = [[
                'store_id' => $store_id,
                'product_id' => $product_id,
                'quantity' => $quantity,
                'cost' => $new_cost,
                'operation' => 'in',
            ]];

            StockCard::createBatchRecord($stock_card_param, $batch_no, $date, self::TRANSACTION_TYPE_MANUFACTURING_IN);

            DB::commit();
            return ['status' => true, 'message' => 'success', 'new_cost' => $new_cost];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Deduct raw materials from inventory
     *
     * @param array $materials Array of ['product_id' => ..., 'store_id' => ..., 'quantity' => ..., 'unit_cost' => ...]
     * @param string $batch_no
     * @param string $date
     * @param int $branch_id
     * @return array
     */
    public static function deductRawMaterials($materials, $batch_no, $date, $branch_id)
    {
        $user = auth()->user();

        DB::beginTransaction();

        try {
            $stock_card_param = [];

            foreach ($materials as $material) {
                $product_id = $material['product_id'];
                $store_id = $material['store_id'];
                $quantity = $material['quantity'];
                $unit_cost = $material['unit_cost'];

                // Get store product
                $storeProduct = StoreProduct::where(['product_id' => $product_id, 'store_id' => $store_id])->first();

                if (!$storeProduct) {
                    throw new \Exception("Product {$product_id} not found in store {$store_id}");
                }

                if ($storeProduct->qty_available < $quantity) {
                    throw new \Exception("Insufficient quantity for product {$product_id} in store {$store_id}");
                }

                // Deduct quantity
                $storeProduct->qty_available -= $quantity;
                $storeProduct->save();

                $stock_card_param[] = [
                    'store_id' => $store_id,
                    'product_id' => $product_id,
                    'quantity' => $quantity,
                    'cost' => $unit_cost,
                    'operation' => 'out',
                ];
            }

            // Create stock card entries
            StockCard::createBatchRecord($stock_card_param, $batch_no, $date, self::TRANSACTION_TYPE_MANUFACTURING_OUT);

            DB::commit();
            return ['status' => true, 'message' => 'success'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Add additional cost to existing finished goods inventory
     * This recalculates the average cost by adding cost to existing value
     *
     * @param int $product_id
     * @param float $additional_cost
     * @param int $branch_id
     * @return array
     */
    public static function addCostToExistingProduct($product_id, $additional_cost, $branch_id)
    {
        $user = auth()->user();

        // Get existing cost price
        $branchPrice = BranchProductPrice::where(['product_id' => $product_id, 'branch_id' => $branch_id])->first();
        $existing_cost = $branchPrice ? $branchPrice->cost_price : 0;

        // Get total quantity in branch
        $store_ids = Store::where('branch_id', $branch_id)->pluck('id')->toArray();
        $total_qty = StoreProduct::where('product_id', $product_id)
            ->whereIn('store_id', $store_ids)
            ->sum('qty_available');

        if ($total_qty <= 0) {
            return ['status' => false, 'message' => 'No inventory available to add cost to.'];
        }

        // Calculate new cost
        $existing_total = $total_qty * $existing_cost;
        $new_total = $existing_total + $additional_cost;
        $new_cost = $new_total / $total_qty;

        DB::beginTransaction();

        try {
            // Update branch product price
            BranchProductPrice::updateOrInsert(
                ['branch_id' => $branch_id, 'product_id' => $product_id],
                ['cost_price' => $new_cost, 'updated_by' => $user->id, 'updated_at' => now()]
            );

            DB::commit();
            return ['status' => true, 'message' => 'success', 'new_cost' => $new_cost];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Return finished goods (reduce inventory and credit back materials)
     *
     * @param int $product_id
     * @param int $store_id
     * @param float $quantity
     * @param float $total_cost
     * @param int $branch_id
     * @param string $batch_no
     * @param string $date
     * @return array
     */
    public static function returnFinishedGoods($product_id, $store_id, $quantity, $total_cost, $branch_id, $batch_no, $date)
    {
        $user = auth()->user();

        $storeProduct = StoreProduct::where(['product_id' => $product_id, 'store_id' => $store_id])->first();

        if (!$storeProduct || $storeProduct->qty_available < $quantity) {
            return ['status' => false, 'message' => 'Insufficient quantity for return.'];
        }

        // Get existing cost price
        $branchPrice = BranchProductPrice::where(['product_id' => $product_id, 'branch_id' => $branch_id])->first();
        $existing_cost = $branchPrice ? $branchPrice->cost_price : 0;

        // Calculate total existing value
        $store_ids = Store::where('branch_id', $branch_id)->pluck('id')->toArray();
        $existing_qty = StoreProduct::where('product_id', $product_id)
            ->whereIn('store_id', $store_ids)
            ->sum('qty_available');

        $existing_total = $existing_qty * $existing_cost;

        // Calculate new weighted average cost after return
        $new_total_qty = $existing_qty - $quantity;
        $new_total_value = $existing_total - $total_cost;
        $new_cost = $new_total_qty > 0 ? $new_total_value / $new_total_qty : 0;

        DB::beginTransaction();

        try {
            // Reduce store product quantity
            $storeProduct->qty_available -= $quantity;
            $storeProduct->save();

            // Update branch product price
            if ($new_total_qty > 0) {
                BranchProductPrice::updateOrInsert(
                    ['branch_id' => $branch_id, 'product_id' => $product_id],
                    ['cost_price' => $new_cost, 'updated_by' => $user->id, 'updated_at' => now()]
                );
            }

            // Create stock card entry
            $stock_card_param = [[
                'store_id' => $store_id,
                'product_id' => $product_id,
                'quantity' => $quantity,
                'cost' => $existing_cost,
                'operation' => 'out',
            ]];

            StockCard::createBatchRecord($stock_card_param, $batch_no, $date, self::TRANSACTION_TYPE_MANUFACTURING_RETURN);

            DB::commit();
            return ['status' => true, 'message' => 'success', 'new_cost' => $new_cost];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Credit back raw materials to inventory (for returns)
     *
     * @param array $materials Array of ['product_id' => ..., 'store_id' => ..., 'quantity' => ..., 'unit_cost' => ...]
     * @param string $batch_no
     * @param string $date
     * @param int $branch_id
     * @return array
     */
    public static function creditRawMaterials($materials, $batch_no, $date, $branch_id)
    {
        $user = auth()->user();

        DB::beginTransaction();

        try {
            $stock_card_param = [];

            foreach ($materials as $material) {
                $product_id = $material['product_id'];
                $store_id = $material['store_id'];
                $quantity = $material['quantity'];
                $unit_cost = $material['unit_cost'];

                // Get or create store product
                $storeProduct = StoreProduct::where(['product_id' => $product_id, 'store_id' => $store_id])->first();
                if (!$storeProduct) {
                    $storeProduct = StoreProduct::create([
                        'product_id' => $product_id,
                        'store_id' => $store_id,
                        'qty_available' => 0,
                    ]);
                }

                // Get existing cost
                $branchPrice = BranchProductPrice::where(['product_id' => $product_id, 'branch_id' => $branch_id])->first();
                $existing_cost = $branchPrice ? $branchPrice->cost_price : 0;

                // Get total qty in branch
                $store_ids = Store::where('branch_id', $branch_id)->pluck('id')->toArray();
                $existing_qty = StoreProduct::where('product_id', $product_id)
                    ->whereIn('store_id', $store_ids)
                    ->sum('qty_available');

                // Calculate new cost
                $existing_total = $existing_qty * $existing_cost;
                $new_total = $existing_total + ($quantity * $unit_cost);
                $new_qty = $existing_qty + $quantity;
                $new_cost = $new_qty > 0 ? $new_total / $new_qty : $unit_cost;

                // Add quantity
                $storeProduct->qty_available += $quantity;
                $storeProduct->save();

                // Update cost
                BranchProductPrice::updateOrInsert(
                    ['branch_id' => $branch_id, 'product_id' => $product_id],
                    ['cost_price' => $new_cost, 'updated_by' => $user->id, 'updated_at' => now()]
                );

                $stock_card_param[] = [
                    'store_id' => $store_id,
                    'product_id' => $product_id,
                    'quantity' => $quantity,
                    'cost' => $new_cost,
                    'operation' => 'in',
                ];
            }

            // Create stock card entries
            StockCard::createBatchRecord($stock_card_param, $batch_no, $date, self::TRANSACTION_TYPE_MANUFACTURING_RETURN);

            DB::commit();
            return ['status' => true, 'message' => 'success'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get current cost price for a product in a branch
     *
     * @param int $product_id
     * @param int $branch_id
     * @return float
     */
    public static function getCurrentCostPrice($product_id, $branch_id)
    {
        $branchPrice = BranchProductPrice::where(['product_id' => $product_id, 'branch_id' => $branch_id])->first();
        return $branchPrice ? $branchPrice->cost_price : 0;
    }
}
