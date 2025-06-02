<?php

namespace App\Http\Controllers;

use App\Models\BudgetExpenditure;
use App\Models\Branch;
use App\Models\GeneralAccount;
use App\Models\User;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BudgetExpenditureController extends Controller
{
    public function index()
    {
        $budgets = BudgetExpenditure::with(['branch', 'account'])
            ->latest('budget_year')
            //->where('branch_id', 'LIKE', User::userBranchAction())
            ->get();
        return view('pages.budgets.expenditures.index', compact('budgets'));
    }

    public function create()
    {
        $branches = Branch::all();
        $accounts = GeneralAccount::whereBetween('class',['C51','C63'])
        ->orderBy('number')->get();
        return view('pages.budgets.expenditures.create', compact('branches', 'accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'general_account_id' => 'required|exists:general_accounts,id',
            'budget_year' => 'required|digits:4',
            'proposed_budget' => 'nullable|numeric',
        ]);

        BudgetExpenditure::create($request->all());
        return redirect()->route('budget_expenditures.index')->with('success', 'Budget saved successfully.');
    }

    public function edit(BudgetExpenditure $budgetExpenditure)
    {
        $branches = Branch::all();
        $accounts = GeneralAccount::all();
        return view('pages.budgets.expenditures.create', compact('budgetExpenditure', 'branches', 'accounts'));
    }

    public function update(Request $request, BudgetExpenditure $budgetExpenditure)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'general_account_id' => 'required|exists:general_accounts,id',
            'budget_year' => 'required|digits:4',
            'proposed_budget' => 'nullable|numeric',
        ]);

        $budgetExpenditure->update($request->all());
        session()->flash('app_message', 'Budget updated successfully');
        return redirect()->route('budget_expenditures.index');
    }

    public function destroy(BudgetExpenditure $budgetExpenditure)
    {
        $budgetExpenditure->delete();
        session()->flash('app_message', 'Budget deleted successfully.');
        return redirect()->route('budget_expenditures.index');
    }

    public function export()
    {
        $budgets = BudgetExpenditure::with(['branch', 'account'])->get();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([
            ['Branch Name', 'Branch Code', 'Account Name', 'Account Code', 'Budget Year', 'Proposed Budget']
        ], null, 'A1');

        $row = 2;
        foreach ($budgets as $budget) {
            $sheet->fromArray([
                $budget->branch->name ?? '',
                $budget->branch->code ?? '',
                $budget->account->description ?? '',
                $budget->account->number ?? '',
                $budget->budget_year,
                $budget->proposed_budget
            ], null, 'A' . $row++);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'budget_expenditures_export.xlsx';

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
        ]);
    }


    public function downloadTemplate()
    {
        $branches = Branch::whereNotNull('code')->get();
        $accounts = GeneralAccount::where('status', 1)
        ->whereBetween('class',['C51','C63'])->get();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        foreach ($branches as $branch) {
            if (!$branch->code || $accounts->isEmpty()) {
                continue; // skip if no code or no accounts
            }

            $sheet = new Worksheet($spreadsheet, substr($branch->code, 0, 31));
            $spreadsheet->addSheet($sheet);
            $sheet->fromArray(
                ['Branch Name', 'Branch Code', 'Account Name', 'Account Code', 'Budget Year', 'Amount'],
                null,
                'A1'
            );

            $row = 2;
            $year = date('Y'); // default year or dynamically passed
            foreach ($accounts as $account) {
                $sheet->setCellValue("A$row", $branch->name);
                $sheet->setCellValue("B$row", $branch->code);
                $sheet->setCellValue("C$row", $account->description);
                $sheet->setCellValue("D$row", $account->number);
                $sheet->setCellValue("E$row", $year);
                $sheet->setCellValue("F$row", ''); // user enters this
                $row++;
            }
        }

        // fallback if no sheets added
        if ($spreadsheet->getSheetCount() === 0) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle('Template');
            $sheet->setCellValue('A1', 'No data available.');
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'budget_expenditures_template.xlsx';

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    public function importForm()
    {
        return view('pages.budgets.expenditures.import');
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        $import = new \App\Imports\BudgetExpenditureImport();
        \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file'));

        $message = "Import completed: Inserted: {$import->inserted}, Updated: {$import->updated}, Skipped: {$import->skipped}.";
        if (!empty($import->errors)) {
            $message .= " Some rows were skipped due to errors.";
            // Optionally log the errors or store in session
            session()->flash('import_errors', $import->errors);
        }
        session()->flash('app_message', $message);
        return redirect()->route('budget_expenditures.index');
    }

}
