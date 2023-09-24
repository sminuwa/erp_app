<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\GeneralAccount;
use App\Http\Requests\GeneralAccounts\Index;
use App\Http\Requests\GeneralAccounts\Show;
use App\Http\Requests\GeneralAccounts\Create;
use App\Http\Requests\GeneralAccounts\Store;
use App\Http\Requests\GeneralAccounts\Edit;
use App\Http\Requests\GeneralAccounts\Update;
use App\Http\Requests\GeneralAccounts\Destroy;
use App\Imports\GeneralAccountImport;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Description of GeneralAccountController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */

class GeneralAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Index  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Index $request)
    {
        $user_branch = User::userBranchAction();
        return view('pages.general_accounts.index', ['records' => GeneralAccount::where('branch_id', $user_branch)->orderby('number')->get()]);
    } /**
      * Display the specified resource.
      *
      * @param  Show  $request
      * @param  GeneralAccount  $generalaccount
      * @return \Illuminate\Http\Response
      */
    public function show(Show $request, GeneralAccount $generalaccount)
    {
        return view('pages.general_accounts.show', [
            'record' => $generalaccount,
        ]);

    } /**
      * Show the form for creating a new resource.
      *
      * @param  Create  $request
      * @return \Illuminate\Http\Response
      */
    public function create(Create $request)
    {
        $user_branch = User::userBranchAction();
        $branches = Branch::where('id', $user_branch)->orderBy('name')->get();
        return view('pages.general_accounts.create', [
            'model' => new GeneralAccount,
            'branches' => $branches,


        ]);
    } /**
      * Store a newly created resource in storage.
      *
      * @param  Store  $request
      * @return \Illuminate\Http\Response
      */
    public function store(Store $request)
    {
        $model = new GeneralAccount;
        $model->fill($request->all());

        if ($model->save()) {
            AuditLog::auditLog(auth()->user()->id, "Added General account $model->number");
            session()->flash('app_message', 'GeneralAccount saved successfully');
            return redirect()->route('general_accounts.index');
        } else {
            session()->flash('app_message', 'Something is wrong while saving GeneralAccount');
        }
        return redirect()->back();
    } /**
      * Show the form for editing the specified resource.
      *
      * @param  Edit  $request
      * @param  GeneralAccount  $generalaccount
      * @return \Illuminate\Http\Response
      */
    public function edit(Edit $request, GeneralAccount $generalaccount)
    {
        $user_branch = User::userBranchAction();
        $branches = Branch::where('id', $user_branch)->orderBy('name')->get();
        return view('pages.general_accounts.edit', [
            'model' => $generalaccount,
            'branches' => $branches,

        ]);
    } /**
      * Update a existing resource in storage.
      *
      * @param  Update  $request
      * @param  GeneralAccount  $generalaccount
      * @return \Illuminate\Http\Response
      */
    public function update(Update $request, GeneralAccount $generalaccount)
    {
        $generalaccount->fill($request->all());

        if ($generalaccount->save()) {
            AuditLog::auditLog(auth()->user()->id, "Modified General account $generalaccount->number");
            session()->flash('app_message', 'GeneralAccount successfully updated');
            return redirect()->route('general_accounts.index');
        } else {
            session()->flash('app_error', 'Something is wrong while updating GeneralAccount');
        }
        return redirect()->back();
    } /**
      * Delete a  resource from  storage.
      *
      * @param  Destroy  $request
      * @param  GeneralAccount  $generalaccount
      * @return \Illuminate\Http\Response
      * @throws \Exception
      */
    public function destroy(Destroy $request, GeneralAccount $generalaccount)
    {
        $deleted = $generalaccount->number;
        if ($generalaccount->delete()) {
            AuditLog::auditLog(auth()->user()->id, "Deleted General account $deleted");
            session()->flash('app_message', 'GeneralAccount successfully deleted');
        } else {
            session()->flash('app_error', 'Error occurred while deleting GeneralAccount');
        }

        return redirect()->back();
    }
    public function importForm()
    {
        return view('pages.general_accounts.import');
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx',
        ]);

        $file = $request->file('file');
        $import = new GeneralAccountImport();
        $rows = Excel::toCollection($import, $file)->first();
        $user_branch = User::userBranchAction();
        try {
            foreach ($rows as $row) {
                GeneralAccount::updateOrInsert(
                    ['branch_id' => $user_branch, 'class' => $row['class'], 'number' => $row['number']],
                    [
                        'branch_id' => $user_branch,
                        'class' => $row['class'],
                        'number' => $row['number'],
                        'description' => $row['description'],
                        'is_control' => $row['is_control'],
                        'updated_at' => Carbon::now(),
                    ]
                );
                $count++;

            }
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
        //dd($faileds);
        session()->flash('app_message', 'File imported and records updated/inserted successfully!');
        return redirect()->back();
    }
}