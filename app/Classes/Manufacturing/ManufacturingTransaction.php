<?php

namespace App\Classes\Manufacturing;

use App\Models\BatchConversion;
use App\Models\BatchProduction;
use App\Models\GeneralAccount;
use App\Models\GeneralAccountControl;
use App\Models\GeneralAccountLedger;
use App\Models\ManufacturingAdditionalCost;
use App\Models\ManufacturingPenalty;
use App\Models\ManufacturingReturn;
use App\Models\ManufacturingRework;
use App\Models\ManufacturingTeam;
use App\Models\SingleProductManufacturing;

class ManufacturingTransaction
{
    /**
     * Post ledger entries for Single Product Manufacturing
     * - Debit raw material category asset accounts (materials consumed)
     * - Credit finish goods category asset account (goods produced)
     */
    public static function singleProductManufacturing($manufacturing_id)
    {
        $user = auth()->user();
        $branch = $user->branch;

        $manufacturing = SingleProductManufacturing::with(['materials.product.category', 'finishProduct.category'])
            ->find($manufacturing_id);

        if (!$manufacturing) {
            return ['status' => false, 'message' => 'Manufacturing record not found.'];
        }

        $raw_material_entries = [];
        $categories = [];

        // Group materials by category for raw material debit
        foreach ($manufacturing->materials as $material) {
            $category = $material->product->category;
            $category_id = $category->id;

            if (!isset($categories[$category_id])) {
                $categories[$category_id] = [
                    'category_id' => $category_id,
                    'asset_account_id' => $category->asset_account,
                    'amount' => 0,
                ];
            }
            $categories[$category_id]['amount'] += $material->total_cost;
        }

        // Create credit entries for raw material asset accounts (materials consumed)
        foreach ($categories as $cat) {
            $raw_material_entries[] = [
                'model_id' => $cat['asset_account_id'],
                'model_name' => 'GeneralAccount',
                'branch_id' => $branch->id,
                'description' => 'Raw materials consumed - ' . $manufacturing->reference,
                'reference' => $manufacturing->reference,
                'credit' => $cat['amount'],
                'debit' => 0,
                'date' => $manufacturing->manufacturing_date,
                'user_id' => $user->id,
                'receipt_no' => 'MFG_RAW_' . $manufacturing->reference
            ];
        }

        // Create debit entry for finished goods asset account
        $finish_product_category = $manufacturing->finishProduct->category;
        $finish_goods_entry = [
            'model_id' => $finish_product_category->asset_account,
            'model_name' => 'GeneralAccount',
            'branch_id' => $branch->id,
            'description' => 'Finished goods produced - ' . $manufacturing->reference,
            'reference' => $manufacturing->reference,
            'credit' => 0,
            'debit' => $manufacturing->total_cost,
            'date' => $manufacturing->manufacturing_date,
            'user_id' => $user->id,
            'receipt_no' => 'MFG_FG_' . $manufacturing->reference
        ];

        $general_account_ledger = array_merge($raw_material_entries, [$finish_goods_entry]);

        if (GeneralAccountLedger::upsert($general_account_ledger, ['model_id', 'model_name', 'branch_id', 'receipt_no'])) {
            return ['status' => true, 'message' => 'success'];
        }

        return ['status' => false, 'message' => 'Something went wrong.'];
    }

    /**
     * Post ledger entries for Batch Production
     * - Debit raw material category asset accounts (materials consumed)
     * - Credit Work-in-Progress account
     */
    public static function batchProduction($batch_id)
    {
        $user = auth()->user();
        $branch = $user->branch;

        $batch = BatchProduction::with(['materials.product.category', 'bom'])
            ->find($batch_id);

        if (!$batch) {
            return ['status' => false, 'message' => 'Batch production record not found.'];
        }

        // Get WIP control account
        $wip_control = GeneralAccountControl::where('code', 'manufacturing_wip')->first();
        if (!$wip_control || !$wip_control->general_account_id) {
            return ['status' => false, 'message' => 'Manufacturing WIP account not configured.'];
        }

        $raw_material_entries = [];
        $categories = [];
        $total_material_cost = 0;

        // Group materials by category for raw material debit
        foreach ($batch->materials as $material) {
            $category = $material->product->category;
            $category_id = $category->id;

            if (!isset($categories[$category_id])) {
                $categories[$category_id] = [
                    'category_id' => $category_id,
                    'asset_account_id' => $category->asset_account,
                    'amount' => 0,
                ];
            }
            $categories[$category_id]['amount'] += $material->total_cost;
            $total_material_cost += $material->total_cost;
        }

        // Create credit entries for raw material asset accounts (materials consumed)
        foreach ($categories as $cat) {
            $raw_material_entries[] = [
                'model_id' => $cat['asset_account_id'],
                'model_name' => 'GeneralAccount',
                'branch_id' => $branch->id,
                'description' => 'Raw materials consumed - Batch ' . $batch->reference,
                'reference' => $batch->reference,
                'credit' => $cat['amount'],
                'debit' => 0,
                'date' => $batch->production_date,
                'user_id' => $user->id,
                'receipt_no' => 'BPC_RAW_' . $batch->reference
            ];
        }

        // Create debit entry for WIP account
        $wip_entry = [
            'model_id' => $wip_control->general_account_id,
            'model_name' => 'GeneralAccount',
            'branch_id' => $branch->id,
            'description' => 'Work in Progress - Batch ' . $batch->reference,
            'reference' => $batch->reference,
            'credit' => 0,
            'debit' => $total_material_cost,
            'date' => $batch->production_date,
            'user_id' => $user->id,
            'receipt_no' => 'BPC_WIP_' . $batch->reference
        ];

        $general_account_ledger = array_merge($raw_material_entries, [$wip_entry]);

        if (GeneralAccountLedger::upsert($general_account_ledger, ['model_id', 'model_name', 'branch_id', 'receipt_no'])) {
            return ['status' => true, 'message' => 'success'];
        }

        return ['status' => false, 'message' => 'Something went wrong.'];
    }

    /**
     * Post ledger entries for Batch Conversion
     * - Debit WIP account (WIP cost released)
     * - Credit finish goods category asset account (goods produced)
     */
    public static function batchConversion($conversion_id)
    {
        $user = auth()->user();
        $branch = $user->branch;

        $conversion = BatchConversion::with(['finishProduct.category', 'batchProduction'])
            ->find($conversion_id);

        if (!$conversion) {
            return ['status' => false, 'message' => 'Batch conversion record not found.'];
        }

        // Get WIP control account
        $wip_control = GeneralAccountControl::where('code', 'manufacturing_wip')->first();
        if (!$wip_control || !$wip_control->general_account_id) {
            return ['status' => false, 'message' => 'Manufacturing WIP account not configured.'];
        }

        // Credit WIP account (reduce WIP)
        $wip_entry = [
            'model_id' => $wip_control->general_account_id,
            'model_name' => 'GeneralAccount',
            'branch_id' => $branch->id,
            'description' => 'WIP converted to finished goods - ' . $conversion->reference,
            'reference' => $conversion->reference,
            'credit' => $conversion->wip_cost_deducted,
            'debit' => 0,
            'date' => $conversion->conversion_date,
            'user_id' => $user->id,
            'receipt_no' => 'BCN_WIP_' . $conversion->reference
        ];

        // Debit finished goods asset account
        $finish_product_category = $conversion->finishProduct->category;
        $finish_goods_entry = [
            'model_id' => $finish_product_category->asset_account,
            'model_name' => 'GeneralAccount',
            'branch_id' => $branch->id,
            'description' => 'Finished goods from batch conversion - ' . $conversion->reference,
            'reference' => $conversion->reference,
            'credit' => 0,
            'debit' => $conversion->total_cost,
            'date' => $conversion->conversion_date,
            'user_id' => $user->id,
            'receipt_no' => 'BCN_FG_' . $conversion->reference
        ];

        $general_account_ledger = [$wip_entry, $finish_goods_entry];

        if (GeneralAccountLedger::upsert($general_account_ledger, ['model_id', 'model_name', 'branch_id', 'receipt_no'])) {
            return ['status' => true, 'message' => 'success'];
        }

        return ['status' => false, 'message' => 'Something went wrong.'];
    }

    /**
     * Post ledger entries for Additional Cost
     * - Debit expense account
     * - Credit finished goods asset account (adding cost to inventory)
     */
    public static function additionalCost($additional_cost_id)
    {
        $user = auth()->user();
        $branch = $user->branch;

        $cost = ManufacturingAdditionalCost::with('account')->find($additional_cost_id);

        if (!$cost) {
            return ['status' => false, 'message' => 'Additional cost record not found.'];
        }

        $production = $cost->getProduction();
        if (!$production) {
            return ['status' => false, 'message' => 'Production record not found.'];
        }

        // Get finished product category
        $finish_product = $cost->production_type === 'single_product'
            ? $production->finishProduct
            : $production->finishProduct;

        if (!$finish_product || !$finish_product->category) {
            return ['status' => false, 'message' => 'Finished product category not found.'];
        }

        // Credit expense account
        $expense_entry = [
            'model_id' => $cost->account_id,
            'model_name' => 'GeneralAccount',
            'branch_id' => $branch->id,
            'description' => 'Additional manufacturing cost - ' . $cost->reference,
            'reference' => $cost->reference,
            'credit' => $cost->amount,
            'debit' => 0,
            'date' => $cost->cost_date,
            'user_id' => $user->id,
            'receipt_no' => 'MAC_EXP_' . $cost->reference
        ];

        // Debit finished goods asset account
        $fg_entry = [
            'model_id' => $finish_product->category->asset_account,
            'model_name' => 'GeneralAccount',
            'branch_id' => $branch->id,
            'description' => 'Additional cost added to finished goods - ' . $cost->reference,
            'reference' => $cost->reference,
            'credit' => 0,
            'debit' => $cost->amount,
            'date' => $cost->cost_date,
            'user_id' => $user->id,
            'receipt_no' => 'MAC_FG_' . $cost->reference
        ];

        $general_account_ledger = [$expense_entry, $fg_entry];

        if (GeneralAccountLedger::upsert($general_account_ledger, ['model_id', 'model_name', 'branch_id', 'receipt_no'])) {
            return ['status' => true, 'message' => 'success'];
        }

        return ['status' => false, 'message' => 'Something went wrong.'];
    }

    /**
     * Post penalty to team ledger balance
     */
    public static function penalty($penalty_id)
    {
        $penalty = ManufacturingPenalty::find($penalty_id);

        if (!$penalty) {
            return ['status' => false, 'message' => 'Penalty record not found.'];
        }

        if ($penalty->isTeamPenalty() && $penalty->team) {
            $penalty->team->addPenalty($penalty->amount_charged);
            return ['status' => true, 'message' => 'success'];
        }

        return ['status' => true, 'message' => 'Penalty posted (staff penalty - no ledger update).'];
    }

    /**
     * Post ledger entries for Manufacturing Return (reversal)
     * - Credit finished goods asset account (reduce inventory)
     * - Debit raw material asset accounts (return materials to stock)
     */
    public static function manufacturingReturn($return_id)
    {
        $user = auth()->user();
        $branch = $user->branch;

        $return = ManufacturingReturn::with('materials.product.category')->find($return_id);

        if (!$return) {
            return ['status' => false, 'message' => 'Return record not found.'];
        }

        $production = $return->getProduction();
        if (!$production) {
            return ['status' => false, 'message' => 'Production record not found.'];
        }

        $finish_product = $return->production_type === 'single_product'
            ? $production->finishProduct
            : $production->finishProduct;

        // Credit finished goods asset (reduce inventory)
        $fg_entry = [
            'model_id' => $finish_product->category->asset_account,
            'model_name' => 'GeneralAccount',
            'branch_id' => $branch->id,
            'description' => 'Manufacturing return - ' . $return->reference,
            'reference' => $return->reference,
            'credit' => $return->total_cost_returned,
            'debit' => 0,
            'date' => $return->return_date,
            'user_id' => $user->id,
            'receipt_no' => 'MRT_FG_' . $return->reference
        ];

        $raw_material_entries = [];
        $categories = [];

        // Group materials by category for raw material credit (return to stock)
        foreach ($return->materials as $material) {
            $category = $material->product->category;
            $category_id = $category->id;

            if (!isset($categories[$category_id])) {
                $categories[$category_id] = [
                    'category_id' => $category_id,
                    'asset_account_id' => $category->asset_account,
                    'amount' => 0,
                ];
            }
            $categories[$category_id]['amount'] += $material->total_cost;
        }

        // Debit raw material asset accounts (return to stock)
        foreach ($categories as $cat) {
            $raw_material_entries[] = [
                'model_id' => $cat['asset_account_id'],
                'model_name' => 'GeneralAccount',
                'branch_id' => $branch->id,
                'description' => 'Raw materials returned - ' . $return->reference,
                'reference' => $return->reference,
                'credit' => 0,
                'debit' => $cat['amount'],
                'date' => $return->return_date,
                'user_id' => $user->id,
                'receipt_no' => 'MRT_RAW_' . $return->reference
            ];
        }

        $general_account_ledger = array_merge([$fg_entry], $raw_material_entries);

        if (GeneralAccountLedger::upsert($general_account_ledger, ['model_id', 'model_name', 'branch_id', 'receipt_no'])) {
            return ['status' => true, 'message' => 'success'];
        }

        return ['status' => false, 'message' => 'Something went wrong.'];
    }

    /**
     * Post ledger entries for Manufacturing Rework
     * - Credit raw material asset accounts (additional materials consumed)
     * - Debit finished goods asset account (adding cost to existing inventory)
     */
    public static function rework($rework_id)
    {
        $user = auth()->user();
        $branch = $user->branch;

        $rework = ManufacturingRework::with('materials.product.category')->find($rework_id);

        if (!$rework) {
            return ['status' => false, 'message' => 'Rework record not found.'];
        }

        $production = $rework->getProduction();
        if (!$production) {
            return ['status' => false, 'message' => 'Production record not found.'];
        }

        $finish_product = $rework->production_type === 'single_product'
            ? $production->finishProduct
            : $production->finishProduct;

        $raw_material_entries = [];
        $categories = [];

        // Group materials by category for raw material credit
        foreach ($rework->materials as $material) {
            $category = $material->product->category;
            $category_id = $category->id;

            if (!isset($categories[$category_id])) {
                $categories[$category_id] = [
                    'category_id' => $category_id,
                    'asset_account_id' => $category->asset_account,
                    'amount' => 0,
                ];
            }
            $categories[$category_id]['amount'] += $material->total_cost;
        }

        // Credit raw material asset accounts (materials consumed)
        foreach ($categories as $cat) {
            $raw_material_entries[] = [
                'model_id' => $cat['asset_account_id'],
                'model_name' => 'GeneralAccount',
                'branch_id' => $branch->id,
                'description' => 'Rework materials consumed - ' . $rework->reference,
                'reference' => $rework->reference,
                'credit' => $cat['amount'],
                'debit' => 0,
                'date' => $rework->rework_date,
                'user_id' => $user->id,
                'receipt_no' => 'MRW_RAW_' . $rework->reference
            ];
        }

        // Debit finished goods asset account (add cost)
        $fg_entry = [
            'model_id' => $finish_product->category->asset_account,
            'model_name' => 'GeneralAccount',
            'branch_id' => $branch->id,
            'description' => 'Rework cost added to finished goods - ' . $rework->reference,
            'reference' => $rework->reference,
            'credit' => 0,
            'debit' => $rework->total_cost,
            'date' => $rework->rework_date,
            'user_id' => $user->id,
            'receipt_no' => 'MRW_FG_' . $rework->reference
        ];

        $general_account_ledger = array_merge($raw_material_entries, [$fg_entry]);

        if (GeneralAccountLedger::upsert($general_account_ledger, ['model_id', 'model_name', 'branch_id', 'receipt_no'])) {
            return ['status' => true, 'message' => 'success'];
        }

        return ['status' => false, 'message' => 'Something went wrong.'];
    }
}
