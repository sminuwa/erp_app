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

        $manufacturing = SingleProductManufacturing::with(['materials.product.category', 'finishProduct.category', 'bom'])
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

        // Create credit entries for overhead costs (Labour, Power, Other)
        $overhead_entries = [];

        $labourControl = GeneralAccountControl::where('code', 'manufacturing_labour')->first();
        $powerControl = GeneralAccountControl::where('code', 'manufacturing_power')->first();
        $otherControl = GeneralAccountControl::where('code', 'manufacturing_other')->first();

        // Validate overhead accounts are configured before posting — a missing account with a
        // non-zero cost would leave the FG debit without a matching overhead credit (GL imbalance).
        if ($manufacturing->labor_cost > 0 && (!$labourControl || !$labourControl->general_account_id)) {
            return ['status' => false, 'message' => 'Manufacturing Labour GL account is not configured but labour cost is ' . $manufacturing->labor_cost . '. Please set it up under GL Control Accounts before posting.'];
        }
        if ($manufacturing->power_cost > 0 && (!$powerControl || !$powerControl->general_account_id)) {
            return ['status' => false, 'message' => 'Manufacturing Power GL account is not configured but power cost is ' . $manufacturing->power_cost . '. Please set it up under GL Control Accounts before posting.'];
        }
        if ($manufacturing->other_cost > 0 && (!$otherControl || !$otherControl->general_account_id)) {
            return ['status' => false, 'message' => 'Manufacturing Other GL account is not configured but other cost is ' . $manufacturing->other_cost . '. Please set it up under GL Control Accounts before posting.'];
        }

        if ($labourControl && $labourControl->general_account_id && $manufacturing->labor_cost > 0) {
            $overhead_entries[] = [
                'model_id' => $labourControl->general_account_id,
                'model_name' => 'GeneralAccount',
                'branch_id' => $branch->id,
                'description' => 'Manufacturing labour cost - ' . $manufacturing->reference,
                'reference' => $manufacturing->reference,
                'credit' => $manufacturing->labor_cost,
                'debit' => 0,
                'date' => $manufacturing->manufacturing_date,
                'user_id' => $user->id,
                'receipt_no' => 'MFG_LAB_' . $manufacturing->reference
            ];
        }

        if ($powerControl && $powerControl->general_account_id && $manufacturing->power_cost > 0) {
            $overhead_entries[] = [
                'model_id' => $powerControl->general_account_id,
                'model_name' => 'GeneralAccount',
                'branch_id' => $branch->id,
                'description' => 'Manufacturing power cost - ' . $manufacturing->reference,
                'reference' => $manufacturing->reference,
                'credit' => $manufacturing->power_cost,
                'debit' => 0,
                'date' => $manufacturing->manufacturing_date,
                'user_id' => $user->id,
                'receipt_no' => 'MFG_PWR_' . $manufacturing->reference
            ];
        }

        if ($otherControl && $otherControl->general_account_id && $manufacturing->other_cost > 0) {
            $overhead_entries[] = [
                'model_id' => $otherControl->general_account_id,
                'model_name' => 'GeneralAccount',
                'branch_id' => $branch->id,
                'description' => 'Manufacturing other cost - ' . $manufacturing->reference,
                'reference' => $manufacturing->reference,
                'credit' => $manufacturing->other_cost,
                'debit' => 0,
                'date' => $manufacturing->manufacturing_date,
                'user_id' => $user->id,
                'receipt_no' => 'MFG_OTH_' . $manufacturing->reference
            ];
        }

        // Margin entry (credit the BOM's margin GL account)
        $margin_entry = [];
        $bom = $manufacturing->bom;
        $marginPerPiece = (float) ($bom->margin_per_piece ?? 0);
        if ($marginPerPiece > 0 && $bom->margin_gl_account_id) {
            $outputQty = $manufacturing->quantity * ($bom->actual_output ?? 1);
            $totalMargin = $marginPerPiece * $outputQty;
            $margin_entry[] = [
                'model_id' => $bom->margin_gl_account_id,
                'model_name' => 'GeneralAccount',
                'branch_id' => $branch->id,
                'description' => 'Manufacturing margin - ' . $manufacturing->reference,
                'reference' => $manufacturing->reference,
                'credit' => $totalMargin,
                'debit' => 0,
                'date' => $manufacturing->manufacturing_date,
                'user_id' => $user->id,
                'receipt_no' => 'MFG_MGN_' . $manufacturing->reference
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

        $general_account_ledger = array_merge($raw_material_entries, $overhead_entries, $margin_entry, [$finish_goods_entry]);

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
        // Costs are fetched live from branch_product_prices (not stored on materials)
        foreach ($batch->materials as $material) {
            $category = $material->product->category;
            $category_id = $category->id;

            $unitCost  = ProductionCalculator::getCurrentCostPrice($material->product_id, $batch->branch_id);
            $totalCost = $material->quantity * $unitCost;

            if (!isset($categories[$category_id])) {
                $categories[$category_id] = [
                    'category_id' => $category_id,
                    'asset_account_id' => $category->asset_account,
                    'amount' => 0,
                ];
            }
            $categories[$category_id]['amount'] += $totalCost;
            $total_material_cost += $totalCost;
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

        // Create credit entries for overhead costs (Labour, Power, Other)
        $overhead_entries = [];

        $labourControl = GeneralAccountControl::where('code', 'manufacturing_labour')->first();
        $powerControl = GeneralAccountControl::where('code', 'manufacturing_power')->first();
        $otherControl = GeneralAccountControl::where('code', 'manufacturing_other')->first();

        // Validate overhead accounts are configured before posting — a missing account with a
        // non-zero cost would leave the WIP debit without a matching overhead credit (GL imbalance).
        if ($batch->labor_cost > 0 && (!$labourControl || !$labourControl->general_account_id)) {
            return ['status' => false, 'message' => 'Manufacturing Labour GL account is not configured but labour cost is ' . $batch->labor_cost . '. Please set it up under GL Control Accounts before posting.'];
        }
        if ($batch->power_cost > 0 && (!$powerControl || !$powerControl->general_account_id)) {
            return ['status' => false, 'message' => 'Manufacturing Power GL account is not configured but power cost is ' . $batch->power_cost . '. Please set it up under GL Control Accounts before posting.'];
        }
        if ($batch->other_cost > 0 && (!$otherControl || !$otherControl->general_account_id)) {
            return ['status' => false, 'message' => 'Manufacturing Other GL account is not configured but other cost is ' . $batch->other_cost . '. Please set it up under GL Control Accounts before posting.'];
        }

        if ($labourControl && $labourControl->general_account_id && $batch->labor_cost > 0) {
            $overhead_entries[] = [
                'model_id' => $labourControl->general_account_id,
                'model_name' => 'GeneralAccount',
                'branch_id' => $branch->id,
                'description' => 'Batch production labour cost - ' . $batch->reference,
                'reference' => $batch->reference,
                'credit' => $batch->labor_cost,
                'debit' => 0,
                'date' => $batch->production_date,
                'user_id' => $user->id,
                'receipt_no' => 'BPC_LAB_' . $batch->reference
            ];
        }

        if ($powerControl && $powerControl->general_account_id && $batch->power_cost > 0) {
            $overhead_entries[] = [
                'model_id' => $powerControl->general_account_id,
                'model_name' => 'GeneralAccount',
                'branch_id' => $branch->id,
                'description' => 'Batch production power cost - ' . $batch->reference,
                'reference' => $batch->reference,
                'credit' => $batch->power_cost,
                'debit' => 0,
                'date' => $batch->production_date,
                'user_id' => $user->id,
                'receipt_no' => 'BPC_PWR_' . $batch->reference
            ];
        }

        if ($otherControl && $otherControl->general_account_id && $batch->other_cost > 0) {
            $overhead_entries[] = [
                'model_id' => $otherControl->general_account_id,
                'model_name' => 'GeneralAccount',
                'branch_id' => $branch->id,
                'description' => 'Batch production other cost - ' . $batch->reference,
                'reference' => $batch->reference,
                'credit' => $batch->other_cost,
                'debit' => 0,
                'date' => $batch->production_date,
                'user_id' => $user->id,
                'receipt_no' => 'BPC_OTH_' . $batch->reference
            ];
        }

        // Create debit entry for WIP account (full WIP value including overhead)
        $wip_entry = [
            'model_id' => $wip_control->general_account_id,
            'model_name' => 'GeneralAccount',
            'branch_id' => $branch->id,
            'description' => 'Work in Progress - Batch ' . $batch->reference,
            'reference' => $batch->reference,
            'credit' => 0,
            'debit' => $batch->wip_value,
            'date' => $batch->production_date,
            'user_id' => $user->id,
            'receipt_no' => 'BPC_WIP_' . $batch->reference
        ];

        $general_account_ledger = array_merge($raw_material_entries, $overhead_entries, [$wip_entry]);

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

        $conversion = BatchConversion::with(['finishProduct.category', 'batchProduction.bom', 'materialProduct.category'])
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

        // Credit raw material asset account (packaging material)
        $material_entry = [];
        if ($conversion->material_product_id && $conversion->material_cost > 0) {
            $materialCategory = $conversion->materialProduct->category ?? null;
            if ($materialCategory && $materialCategory->asset_account) {
                $material_entry[] = [
                    'model_id' => $materialCategory->asset_account,
                    'model_name' => 'GeneralAccount',
                    'branch_id' => $branch->id,
                    'description' => 'Packaging material deducted - ' . $conversion->reference,
                    'reference' => $conversion->reference,
                    'credit' => $conversion->material_cost,
                    'debit' => 0,
                    'date' => $conversion->conversion_date,
                    'user_id' => $user->id,
                    'receipt_no' => 'BCN_RM_' . $conversion->reference
                ];
            } else {
                return ['status' => false, 'message' => 'Packaging material product category asset account not configured.'];
            }
        }

        // Margin entry (credit the BOM's margin GL account)
        $margin_entry = [];
        $bom = $conversion->batchProduction->bom ?? null;
        if ($bom) {
            $marginPerPiece = (float) ($bom->margin_per_piece ?? 0);
            if ($marginPerPiece > 0 && $bom->margin_gl_account_id) {
                $totalMargin = $marginPerPiece * $conversion->produced_qty;
                $margin_entry[] = [
                    'model_id' => $bom->margin_gl_account_id,
                    'model_name' => 'GeneralAccount',
                    'branch_id' => $branch->id,
                    'description' => 'Batch conversion margin - ' . $conversion->reference,
                    'reference' => $conversion->reference,
                    'credit' => $totalMargin,
                    'debit' => 0,
                    'date' => $conversion->conversion_date,
                    'user_id' => $user->id,
                    'receipt_no' => 'BCN_MGN_' . $conversion->reference
                ];
            }
        }

        // Debit finished goods asset account
        $finish_product_category = $conversion->finishProduct->category;
        if (!$finish_product_category || !$finish_product_category->asset_account) {
            return ['status' => false, 'message' => 'Finish product category asset account not configured.'];
        }

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

        $general_account_ledger = array_merge([$wip_entry], $material_entry, $margin_entry, [$finish_goods_entry]);

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
            'description' => ($cost->description ? $cost->description . ' - ' : '') . 'Additional cost - ' . $cost->reference,
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
            'description' => ($cost->description ? $cost->description . ' - ' : '') . 'Additional cost to FG - ' . $cost->reference,
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
     * Reverse ledger entries for Additional Cost
     * Swaps debit/credit from the original additionalCost posting
     */
    public static function reverseAdditionalCost($cost_id)
    {
        $user = auth()->user();
        $branch = $user->branch;

        $cost = ManufacturingAdditionalCost::find($cost_id);

        if (!$cost) {
            return ['status' => false, 'message' => 'Additional cost record not found.'];
        }

        $production = $cost->getProduction();
        if (!$production) {
            return ['status' => false, 'message' => 'Production record not found.'];
        }

        $finish_product = $cost->production_type === 'single_product'
            ? $production->finishProduct
            : $production->finishProduct;

        if (!$finish_product || !$finish_product->category) {
            return ['status' => false, 'message' => 'Finished product category not found.'];
        }

        // Debit expense account (reverse the original credit)
        $expense_entry = [
            'model_id' => $cost->account_id,
            'model_name' => 'GeneralAccount',
            'branch_id' => $branch->id,
            'description' => 'Reversal: ' . ($cost->description ? $cost->description . ' - ' : '') . $cost->reference,
            'reference' => $cost->reference,
            'credit' => 0,
            'debit' => $cost->amount,
            'date' => now()->format('Y-m-d'),
            'user_id' => $user->id,
            'receipt_no' => 'MAC_REV_EXP_' . $cost->reference
        ];

        // Credit finished goods asset account (reverse the original debit)
        $fg_entry = [
            'model_id' => $finish_product->category->asset_account,
            'model_name' => 'GeneralAccount',
            'branch_id' => $branch->id,
            'description' => 'Reversal: Additional cost from FG - ' . $cost->reference,
            'reference' => $cost->reference,
            'credit' => $cost->amount,
            'debit' => 0,
            'date' => now()->format('Y-m-d'),
            'user_id' => $user->id,
            'receipt_no' => 'MAC_REV_FG_' . $cost->reference
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

        if (!$finish_product || !$finish_product->category || !$finish_product->category->asset_account) {
            return ['status' => false, 'message' => 'Finished product GL asset account not configured for this return.'];
        }

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
            $category = $material->product->category ?? null;
            if (!$category || !$category->asset_account) continue;
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

        // Reconcile overhead: total_cost_returned includes overhead (labour/power/other) that was
        // absorbed into the unit cost, but the raw material debits only cover material costs.
        // The difference is debited to the WIP account to keep the GL balanced.
        $total_material_debit = array_sum(array_column($categories, 'amount'));
        $overhead_remainder   = round($return->total_cost_returned - $total_material_debit, 2);
        $overhead_entry = [];

        if ($overhead_remainder > 0) {
            $wip_control = GeneralAccountControl::where('code', 'manufacturing_wip')->first();
            if ($wip_control && $wip_control->general_account_id) {
                $overhead_entry[] = [
                    'model_id'    => $wip_control->general_account_id,
                    'model_name'  => 'GeneralAccount',
                    'branch_id'   => $branch->id,
                    'description' => 'Manufacturing return overhead adjustment - ' . $return->reference,
                    'reference'   => $return->reference,
                    'credit'      => 0,
                    'debit'       => $overhead_remainder,
                    'date'        => $return->return_date,
                    'user_id'     => $user->id,
                    'receipt_no'  => 'MRT_WIP_' . $return->reference
                ];
            }
        }

        $general_account_ledger = array_merge([$fg_entry], $raw_material_entries, $overhead_entry);

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
