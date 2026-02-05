<?php

namespace App\Classes\Manufacturing;

use App\Models\BranchProductPrice;
use App\Models\ManufacturingBom;
use App\Models\StoreProduct;

class ProductionCalculator
{
    /**
     * Calculate materials required for production based on BOM
     *
     * @param int $bom_id
     * @param float $quantity Number of units to produce (for single) or batches (for batch)
     * @return array ['materials' => array, 'total_cost' => float]
     */
    public static function calculateMaterials($bom_id, $quantity, $branch_id = null)
    {
        $bom = ManufacturingBom::with('materials.product')->find($bom_id);

        if (!$bom) {
            return ['materials' => [], 'total_cost' => 0];
        }

        if (!$branch_id) {
            $branch_id = $bom->branch_id;
        }

        $materials = [];
        $total_cost = 0;

        foreach ($bom->materials as $bomMaterial) {
            // Calculate required quantity
            $required_qty = $bomMaterial->quantity * $quantity;

            // Get current unit cost
            $unit_cost = self::getCurrentCostPrice($bomMaterial->product_id, $branch_id);

            $material_cost = $required_qty * $unit_cost;
            $total_cost += $material_cost;

            $materials[] = [
                'product_id' => $bomMaterial->product_id,
                'product_name' => $bomMaterial->product->name,
                'product_code' => $bomMaterial->product->code,
                'store_id' => $bomMaterial->source_store_id,
                'bom_qty' => $bomMaterial->quantity,
                'quantity' => $required_qty,
                'unit_cost' => $unit_cost,
                'total_cost' => $material_cost,
            ];
        }

        return [
            'materials' => $materials,
            'total_cost' => $total_cost
        ];
    }

    /**
     * Calculate production cost including other costs
     *
     * @param int $bom_id
     * @param float $quantity
     * @param int|null $branch_id
     * @return array
     */
    public static function calculateProductionCost($bom_id, $quantity, $branch_id = null)
    {
        $bom = ManufacturingBom::find($bom_id);

        if (!$bom) {
            return [
                'material_cost' => 0,
                'labor_cost' => 0,
                'power_cost' => 0,
                'other_cost' => 0,
                'total_other_cost' => 0,
                'total_cost' => 0,
                'unit_cost' => 0,
                'expected_output' => 0
            ];
        }

        $materialsData = self::calculateMaterials($bom_id, $quantity, $branch_id);

        // Scale other costs by quantity
        $labor_cost = $bom->labor_cost * $quantity;
        $power_cost = $bom->power_cost * $quantity;
        $other_cost = $bom->other_cost * $quantity;
        $total_other_cost = $labor_cost + $power_cost + $other_cost;

        $total_cost = $materialsData['total_cost'] + $total_other_cost;

        // Calculate expected output
        $expected_output = $bom->isBatch()
            ? $quantity * $bom->actual_output
            : $quantity;

        $unit_cost = $expected_output > 0 ? $total_cost / $expected_output : 0;

        return [
            'material_cost' => $materialsData['total_cost'],
            'labor_cost' => $labor_cost,
            'power_cost' => $power_cost,
            'other_cost' => $other_cost,
            'total_other_cost' => $total_other_cost,
            'total_cost' => $total_cost,
            'unit_cost' => $unit_cost,
            'expected_output' => $expected_output,
            'materials' => $materialsData['materials']
        ];
    }

    /**
     * Validate inventory availability for production
     *
     * @param array $materials Array from calculateMaterials
     * @param bool $check_reservations Whether to consider existing reservations
     * @return array ['status' => bool, 'message' => string, 'insufficient' => array]
     */
    public static function validateInventoryAvailability($materials, $check_reservations = true)
    {
        $insufficient = [];

        foreach ($materials as $material) {
            if ($check_reservations) {
                $available = InventoryReservationService::getAvailableQty(
                    $material['product_id'],
                    $material['store_id']
                );
            } else {
                $storeProduct = StoreProduct::where([
                    'product_id' => $material['product_id'],
                    'store_id' => $material['store_id']
                ])->first();
                $available = $storeProduct ? $storeProduct->qty_available : 0;
            }

            if ($available < $material['quantity']) {
                $insufficient[] = [
                    'product_id' => $material['product_id'],
                    'product_name' => $material['product_name'] ?? 'Unknown',
                    'store_id' => $material['store_id'],
                    'required_qty' => $material['quantity'],
                    'available_qty' => $available,
                    'shortage' => $material['quantity'] - $available
                ];
            }
        }

        if (count($insufficient) > 0) {
            return [
                'status' => false,
                'message' => 'Insufficient inventory for ' . count($insufficient) . ' material(s)',
                'insufficient' => $insufficient
            ];
        }

        return [
            'status' => true,
            'message' => 'All materials have sufficient inventory',
            'insufficient' => []
        ];
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
        $branchPrice = BranchProductPrice::where([
            'product_id' => $product_id,
            'branch_id' => $branch_id
        ])->first();

        return $branchPrice ? (float) $branchPrice->cost_price : 0;
    }

    /**
     * Calculate materials for a production order (multiple BOMs)
     *
     * @param array $orderItems Array of ['bom_id' => ..., 'quantity' => ...]
     * @param int $branch_id
     * @return array
     */
    public static function calculateOrderMaterials($orderItems, $branch_id)
    {
        $all_materials = [];
        $total_cost = 0;
        $aggregated = [];

        foreach ($orderItems as $item) {
            $materialsData = self::calculateMaterials($item['bom_id'], $item['quantity'], $branch_id);

            foreach ($materialsData['materials'] as $material) {
                $key = $material['product_id'] . '_' . $material['store_id'];

                if (!isset($aggregated[$key])) {
                    $aggregated[$key] = $material;
                } else {
                    $aggregated[$key]['quantity'] += $material['quantity'];
                    $aggregated[$key]['total_cost'] += $material['total_cost'];
                }
            }

            $total_cost += $materialsData['total_cost'];
        }

        return [
            'materials' => array_values($aggregated),
            'total_cost' => $total_cost
        ];
    }

    /**
     * Get BOM details with current costs
     *
     * @param int $bom_id
     * @param int|null $branch_id
     * @return array|null
     */
    public static function getBomDetails($bom_id, $branch_id = null)
    {
        $bom = ManufacturingBom::with(['materials.product', 'finishProduct', 'outputStore', 'mainRawMaterial'])
            ->find($bom_id);

        if (!$bom) {
            return null;
        }

        if (!$branch_id) {
            $branch_id = $bom->branch_id;
        }

        $materials = [];
        $total_material_cost = 0;

        foreach ($bom->materials as $material) {
            $unit_cost = self::getCurrentCostPrice($material->product_id, $branch_id);
            $total_cost = $material->quantity * $unit_cost;
            $total_material_cost += $total_cost;

            $materials[] = [
                'id' => $material->id,
                'product_id' => $material->product_id,
                'product_code' => $material->product->code,
                'product_name' => $material->product->name,
                'quantity' => $material->quantity,
                'store_id' => $material->source_store_id,
                'unit_cost' => $unit_cost,
                'total_cost' => $total_cost
            ];
        }

        $total_other_cost = $bom->labor_cost + $bom->power_cost + $bom->other_cost;
        $total_cost = $total_material_cost + $total_other_cost;
        $unit_cost = $bom->actual_output > 0 ? $total_cost / $bom->actual_output : 0;

        return [
            'id' => $bom->id,
            'reference' => $bom->reference,
            'description' => $bom->description,
            'bom_type' => $bom->bom_type,
            'finish_product' => [
                'id' => $bom->finishProduct->id,
                'code' => $bom->finishProduct->code,
                'name' => $bom->finishProduct->name
            ],
            'output_store' => [
                'id' => $bom->outputStore->id,
                'name' => $bom->outputStore->name
            ],
            'actual_output' => $bom->actual_output,
            'accepted_excess' => $bom->accepted_excess,
            'accepted_shortage' => $bom->accepted_shortage,
            'materials' => $materials,
            'labor_cost' => $bom->labor_cost,
            'power_cost' => $bom->power_cost,
            'other_cost' => $bom->other_cost,
            'total_material_cost' => $total_material_cost,
            'total_other_cost' => $total_other_cost,
            'total_cost' => $total_cost,
            'unit_cost' => $unit_cost
        ];
    }
}
