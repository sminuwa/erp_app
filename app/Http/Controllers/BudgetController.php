<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\Branch;
use App\Models\Category;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BudgetImport; // This will be created for Excel import
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BudgetController extends Controller
{
    public function index()
    {
        if (!auth()->user()->can('budget.view'))
            return abort('403');
        $budgets = Budget::with(['branch', 'category'])->latest('budget_year')->get(); // Pagination
        return view('pages.budgets.index', compact('budgets'));
    }


    public function create()
    {
        $branches = Branch::all();
        $categories = Category::all();
        return view('pages.budgets.create', compact('branches', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'category_id' => 'required|exists:categories,id',
            'budget_year' => 'required|digits:4',
            'quarter' => 'required|in:Q1,Q2,Q3,Q4',
            'month1' => 'required|numeric',
            'month2' => 'required|numeric',
            'month3' => 'required|numeric',
            'total' => 'required|numeric'
        ]);

        Budget::create($request->all());
        return redirect()->route('budgets.index')->with('success', 'Budget added successfully.');
    }

    public function edit($id)
    {
        $budget = Budget::findOrFail($id);
        $branches = Branch::all();
        $categories = Category::all();
        return view('pages.budgets.create', compact('budget', 'branches', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'category_id' => 'required|exists:categories,id',
            'budget_year' => 'required|digits:4',
            'quarter' => 'required|in:Q1,Q2,Q3,Q4',
            'month1' => 'required|numeric',
            'month2' => 'required|numeric',
            'month3' => 'required|numeric',
            'total' => 'required|numeric'
        ]);

        $budget = Budget::findOrFail($id);
        $budget->update($request->all());

        return redirect()->route('budgets.index')->with('success', 'Budget updated successfully.');
    }
    public function destroy(Budget $budget)
    {
        
        $budget->delete();
        session()->flash('app_message', 'Budget deleted successfully');
        return redirect()->back();
    }
    public function importForm()
    {
        return view('pages.budgets.import');
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);

        Excel::import(new BudgetImport, $request->file('file'));

        return redirect()->route('budgets.index')->with('success', 'Budget data imported successfully.');
    }
    public function generate()
    {
        // Fetch all branches and categories
        $branches = Branch::whereNotNull('code')->where('code', '!=', '')->get();
        $categories = Category::all();

        // Create a new Spreadsheet
        $spreadsheet = new Spreadsheet();

        if ($branches->isEmpty()) {
            return back()->with('error', 'No branches with valid codes found.');
        }

        foreach ($branches as $branchIndex => $branch) {
            // Ensure sheet title does not exceed 31 characters
            $branchCode = substr(preg_replace('/[^A-Za-z0-9]/', '', $branch->code), 0, 31);

            // Skip if the branch code is empty after sanitization
            if (empty($branchCode)) {
                continue;
            }

            // Create the first sheet or additional sheets dynamically
            if ($branchIndex == 0) {
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle($branchCode);
            } else {
                $sheet = $spreadsheet->createSheet();
                $sheet->setTitle($branchCode);
            }

            // Add metadata (Branch Code, Quarter, Budget Year)
            $sheet->setCellValue('A1', 'Branch Name:');
            $sheet->setCellValue('B1', $branch->name);
            $sheet->setCellValue('C1', 'Quarter:');
            $sheet->setCellValue('D1', 'Q1'); // Default, can be changed dynamically
            $sheet->setCellValue('E1', 'Budget Year:');
            $sheet->setCellValue('F1', date('Y')); // Default to current year

            // Headers for budget data
            $sheet->setCellValue('A3', 'CATEGORY');
            $sheet->setCellValue('B3', 'PROPOSED BGT');
            $sheet->setCellValue('C3', 'MONTH 1');
            $sheet->setCellValue('D3', 'MONTH 2');
            $sheet->setCellValue('E3', 'MONTH 3');

            // Populate categories
            $rowNumber = 4;
            foreach ($categories as $category) {
                $sheet->setCellValue('A' . $rowNumber, $category->name);
                $sheet->setCellValue('B' . $rowNumber, ''); // Proposed Budget placeholder
                $sheet->setCellValue('C' . $rowNumber, ''); // Month 1 placeholder
                $sheet->setCellValue('D' . $rowNumber, ''); // Month 2 placeholder
                $sheet->setCellValue('E' . $rowNumber, ''); // Month 3 placeholder
                $sheet->setCellValue('F' . $rowNumber, ''); // Total placeholder
                $rowNumber++;
            }
        }

        // Prepare response for download
        $fileName = "budget_import_template.xlsx";
        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
