<?php

namespace App\Imports;

use App\Models\RoBudget;
use App\Models\Branch;
use App\Models\Category;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RoBudgetImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Find the staff, branch, and category
        $staff = User::where('user_code', $row['staff_code'])->first();
        $branch = Branch::where('id', $row['branch_code'])->first();
        $category = Category::where('id', $row['category'])->first();

        // Validate data
        if (!$staff || !$branch || !$category) {
            return null; // Skip row if any required reference is missing
        }

        $total = $row['month1'] + $row['month2'] + $row['month3'];

        // Ensure the total does not exceed 100
        if ($total > 100) {
            return null; // Skip this record if it exceeds the allowed limit
        }

        return new RoBudget([
            'staff_id' => $staff->id,
            'branch_id' => $branch->id,
            'category_id' => $category->id,
            'budget_year' => $row['budget_year'],
            'quarter' => $row['quarter'],
            'month1' => $row['month1'],
            'month2' => $row['month2'],
            'month3' => $row['month3'],
            'total' => $total,
        ]);
    }
}
