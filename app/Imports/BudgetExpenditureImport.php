<?php

namespace App\Imports;

use App\Models\BudgetExpenditure;
use App\Models\Branch;
use App\Models\GeneralAccount;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BudgetExpenditureImport implements ToCollection, WithHeadingRow, WithMultipleSheets
{
    public $inserted = 0;
    public $updated = 0;
    public $skipped = 0;
    public $errors = [];

    public function sheets(): array
    {
        return [
            0 => $this,
        ];
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $branch = Branch::where('code', $row['branch_code'] ?? null)->first();
            $account = GeneralAccount::where('number', $row['account_code'] ?? null)->first();

            if (!$branch || !$account || empty($row['budget_year'])) {
                $this->skipped++;
                $this->errors[] = [
                    'row' => $row->toArray(),
                    'reason' => 'Missing required branch, account, or budget year'
                ];
                continue;
            }

            $amount = is_numeric($row['amount']) ? $row['amount'] : null;
            if ($amount === null) {
                $this->skipped++;
                $this->errors[] = [
                    'row' => $row->toArray(),
                    'reason' => 'Invalid or empty amount'
                ];
                continue;
            }

            $budget = BudgetExpenditure::updateOrCreate(
                [
                    'branch_id' => $branch->id,
                    'general_account_id' => $account->id,
                    'budget_year' => $row['budget_year']
                ],
                [
                    'proposed_budget' => $amount
                ]
            );

            $budget->wasRecentlyCreated ? $this->inserted++ : $this->updated++;
        }
    }
}
