<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AreaManager;
use App\Models\Branch;
use App\Models\User;

class AreaManagerController extends Controller
{
    public function index()
    {
        $areaManagers = AreaManager::with(['manager', 'branches'])->latest()->get();
        return view('pages.budgets.area_managers.index', compact('areaManagers'));
    }

    public function create()
    {
        $managers = User::orderBy('user_code')->get();
        $branches = Branch::orderBy('code')->get();
        return view('pages.budgets.area_managers.create', compact('managers', 'branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'manager_id' => 'required|exists:users,id',
            'branch_id' => 'required|array',
            'branch_id.*' => 'exists:branches,id',
            'status' => 'required|boolean',
        ]);

        foreach ($request->branch_id as $branchId) {
            AreaManager::create([
                'manager_id' => $request->manager_id,
                'branch_id' => $branchId,
                'status' => $request->status,
            ]);
        }

        return redirect()->route('area_managers.index')->with('success', 'Area Manager assigned successfully.');
    }

    public function edit($id)
    {
        $areaManager = AreaManager::findOrFail($id);
        $managers = User::orderBy('user_code')->get();
        $branches = Branch::orderBy('code')->get();
        return view('pages.budgets.area_managers.create', compact('areaManager', 'managers', 'branches'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'manager_id' => 'required|exists:users,id',
            'branch_id' => 'required|array',
            'branch_id.*' => 'exists:branches,id',
            'status' => 'required|boolean',
        ]);

        AreaManager::where('manager_id', $request->manager_id)->delete();

        foreach ($request->branch_id as $branchId) {
            AreaManager::create([
                'manager_id' => $request->manager_id,
                'branch_id' => $branchId,
                'status' => $request->status,
            ]);
        }

        return redirect()->route('area_managers.index')->with('success', 'Area Manager updated successfully.');
    }

    public function destroy($id)
    {
        $areaManager = AreaManager::findOrFail($id);
        $areaManager->delete();
        return redirect()->route('area_managers.index')->with('success', 'Area Manager removed successfully.');
    }
}
