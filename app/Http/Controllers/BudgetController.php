<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\Branch;
use App\Models\Category;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BudgetImport; // This will be created for Excel import

class BudgetController extends Controller
{
    public function index()
    {
        $budgets = Budget::with(['branch', 'category'])->paginate(10); // Pagination
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
}
