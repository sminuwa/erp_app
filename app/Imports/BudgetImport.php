<?php
namespace App\Imports;

use App\Models\Budget;
use App\Models\Branch;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class BudgetImport implements ToCollection
{
    protected $branchName;
    protected $quarter;
    protected $budgetYear;

    public function collection(Collection $rows)
    {
        // Extract Branch Name, Quarter, and Budget Year from the first row (row index 0)
        $metadataRow = $rows[0];

        $this->branchName = trim($metadataRow[1]); // Extract Branch Name
        $this->quarter = trim($metadataRow[3]); // Extract Quarter
        $this->budgetYear = trim($metadataRow[5]); // Extract Budget Year

        // Ensure the metadata exists
        if (!$this->branchName || !$this->quarter || !$this->budgetYear) {
            throw new \Exception("Missing Branch Name, Quarter, or Budget Year in the first row.");
        }

        // Find or create the branch
        $branch = Branch::firstOrCreate(['name' => $this->branchName]);

        // Loop through the budget rows (starting from index 2, skipping metadata and headers)
        foreach ($rows->skip(2) as $row) {
            if (!isset($row[0]) || empty(trim($row[0]))) {
                continue; // Skip empty rows
            }

            // Find or create the category
            $category = Category::firstOrCreate(['name' => trim($row[0])]);

            // Insert or update budget data
            Budget::updateOrCreate(
                [
                    'branch_id' => $branch->id,
                    'category_id' => $category->id,
                    'budget_year' => $this->budgetYear,
                    'quarter' => $this->quarter
                ],
                [
                    'proposed_budget' => is_numeric($row[1]) ? $row[1] : 0,
                    'month1' => is_numeric($row[2]) ? $row[2] : 0,
                    'month2' => is_numeric($row[3]) ? $row[3] : 0,
                    'month3' => is_numeric($row[4]) ? $row[4] : 0,
                    'total' => is_numeric($row[5]) ? $row[5] : 0,
                ]
            );
        }
    }
}
