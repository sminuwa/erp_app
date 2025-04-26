<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RoBudget;
use App\Models\Branch;
use App\Models\Category;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\RoBudgetImport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
class RoBudgetController extends Controller
{
    public function index()
    {
        $budgets = RoBudget::with(['staff', 'branch', 'category'])->latest('budget_year')->get();
        return view('pages.budgets.ro.index', compact('budgets'));
    }

    public function create()
    {
        $branches = Branch::all();
        $categories = Category::all();
        $staffs = User::all();
        return view('pages.budgets.ro.create', compact('branches', 'categories', 'staffs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:users,id',
            'branch_id' => 'nullable|exists:branches,id',
            'category_id' => 'nullable|exists:categories,id',
            'budget_year' => 'required|digits:4',
            'quarter' => 'required|in:Q1,Q2,Q3,Q4',
            'month1' => 'required|numeric',
            'month2' => 'required|numeric',
            'month3' => 'required|numeric',
        ]);

        $total = $request->month1 + $request->month2 + $request->month3;
        if ($total > 100) {
            return back()->withErrors(['total' => 'Total budget allocation cannot exceed 100.'])->withInput();
        }

        RoBudget::create(array_merge($request->all(), ['total' => $total]));
        return redirect()->route('ro_budgets.index')->with('success', 'Budget added successfully.');
    }

    public function edit(RoBudget $roBudget)
    {
        $branches = Branch::all();
        $categories = Category::all();
        $staffs = User::all();
        return view('pages.budgets.ro.create', compact('roBudget', 'branches', 'categories', 'staffs'));
    }

    public function update(Request $request, RoBudget $roBudget)
    {
        $request->validate([
            'staff_id' => 'required|exists:users,id',
            'branch_id' => 'nullable|exists:branches,id',
            'category_id' => 'nullable|exists:categories,id',
            'budget_year' => 'required|digits:4',
            'quarter' => 'required|in:Q1,Q2,Q3,Q4',
            'month1' => 'required|numeric',
            'month2' => 'required|numeric',
            'month3' => 'required|numeric',
        ]);

        $total = $request->month1 + $request->month2 + $request->month3;
        if ($total > 100) {
            return back()->withErrors(['total' => 'Total budget allocation cannot exceed 100.'])->withInput();
        }

        $roBudget->update(array_merge($request->all(), ['total' => $total]));
        return redirect()->route('ro_budgets.index')->with('success', 'Budget updated successfully.');
    }

    public function destroy(RoBudget $roBudget)
    {
        $roBudget->delete();
        session()->flash('app_message', 'Budget deleted successfully');
        return redirect()->route('ro_budgets.index');
    }

    public function importStore(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,csv']);

        $import = new RoBudgetImport();
        Excel::import($import, $request->file('file'));

        $message = "Budget data import completed: ";
        $message .= "Inserted: " . $import->inserted . ", ";
        $message .= "Updated: " . $import->updated . ", ";
        $message .= "Skipped: " . $import->skipped . ".";

        if (!empty($import->errors)) {
            $message .= " Errors: " . implode(' | ', $import->errors);
            session()->flash('app_error', $message);
            return redirect()->route('ro_budgets.index')->withErrors(array('message' => $message));
        }
        session()->flash('app_message', $message);
        return redirect()->route('ro_budgets.index')->withErrors(array('message' => $message));
    }

    public function import(Request $request)
    {

        return view('pages.budgets.ro.import');
    }
    public function downloadTemplate()
    {
        // Fetch all staff, branches, and categories
        $staffs = User::select('user_code', 'firstname', 'surname', 'branch_id')
            ->where('is_sale_representative', 1)
            ->with('branch:id,code')->get();
        $categories = Category::select('id', 'code', 'name')->get();

        // Create a new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('RO Budget Template');

        // Set headers
        $headers = ['Staff Code', 'Staff Name', 'Branch Code', 'Category Code', 'Category Name', 'Budget Year', 'Quarter', 'Month 1', 'Month 2', 'Month 3'];
        $sheet->fromArray([$headers], null, 'A1');

        // Populate data
        $rowNumber = 2;
        foreach ($staffs as $staff) {
            foreach ($categories as $category) {
                $sheet->setCellValue('A' . $rowNumber, $staff->user_code);
                $sheet->setCellValue('B' . $rowNumber, $staff->firstname . ' ' . $staff->surname);
                $sheet->setCellValue('C' . $rowNumber, $staff->branch->code ?? 'N/A');
                $sheet->setCellValue('D' . $rowNumber, $category->code);
                $sheet->setCellValue('E' . $rowNumber, $category->name);
                $sheet->setCellValue('F' . $rowNumber, date('Y'));
                $sheet->setCellValue('G' . $rowNumber, 'Q1'); // Default Quarter
                $sheet->setCellValue('H' . $rowNumber, ''); // Month 1 Placeholder
                $sheet->setCellValue('I' . $rowNumber, ''); // Month 2 Placeholder
                $sheet->setCellValue('J' . $rowNumber, ''); // Month 3 Placeholder
                $rowNumber++;
            }
        }

        // Prepare response for download
        $fileName = "ro_budget_import_template.xlsx";
        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

}
