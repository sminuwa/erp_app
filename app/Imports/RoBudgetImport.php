<?php
namespace App\Imports;

use App\Models\RoBudget;
use App\Models\Branch;
use App\Models\Category;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class RoBudgetImport implements ToCollection
{
    public $inserted = 0;
    public $updated = 0;
    public $skipped = 0;
    public $errors = [];

    protected $headerMap = [
        'Staff Code' => 'staff_code',
        'Branch Code' => 'branch_code',
        'Category Code' => 'category_code',
        'Budget Year' => 'budget_year',
        'Quarter' => 'quarter',
        'Month 1' => 'month1',
        'Month 2' => 'month2',
        'Month 3' => 'month3'
    ];

    public function collection(Collection $rows)
    {
        // Get actual headers from the first row
        $headers = $rows->first()->toArray();
        $mappedHeaders = array_map(function ($header) {
            return $this->headerMap[$header] ?? $header; // Map headers to expected format
        }, $headers);

        foreach ($rows->skip(1) as $row) { // Skip header row
            $row = array_combine($mappedHeaders, $row->toArray()); // Map correct headers

            if (!isset($row['staff_code'], $row['branch_code'], $row['category_code'], $row['budget_year'], $row['quarter'], $row['month1'], $row['month2'], $row['month3'])) {
                $this->skipped++;
                $this->errors[] = "Missing required columns in row: " . json_encode($row);
                continue;
            }

            $staff = User::where('user_code', $row['staff_code'])->first();
            $branch = Branch::where('code', $row['branch_code'])->first();
            $category = Category::where('code', $row['category_code'])->first();

            if (!$staff || !$branch || !$category) {
                $this->skipped++;
                $this->errors[] = "Invalid reference for staff, branch, or category in row: " . json_encode($row);
                continue;
            }

            $total = $row['month1'] + $row['month2'] + $row['month3'];

            if ($total > 100) {
                $this->skipped++;
                $this->errors[] = "Total budget exceeds 100 in row: " . json_encode($row);
                continue;
            }

            $existingBudget = RoBudget::where([
                'staff_id' => $staff->id,
                'branch_id' => $branch->id,
                'category_id' => $category->id,
                'budget_year' => $row['budget_year'],
                'quarter' => $row['quarter']
            ])->first();

            if ($existingBudget) {
                $existingBudget->update([
                    'month1' => $row['month1'],
                    'month2' => $row['month2'],
                    'month3' => $row['month3'],
                    'total' => $total,
                ]);
                $this->updated++;
            } else {
                RoBudget::create([
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
                $this->inserted++;
            }
        }
    }
}
