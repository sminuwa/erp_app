<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Http\Requests\ChartOfAccounts\Index;
use App\Http\Requests\ChartOfAccounts\Show;
use App\Http\Requests\ChartOfAccounts\Create;
use App\Http\Requests\ChartOfAccounts\Store;
use App\Http\Requests\ChartOfAccounts\Edit;
use App\Http\Requests\ChartOfAccounts\Update;
use App\Http\Requests\ChartOfAccounts\Destroy;
use App\Imports\ChartOfAccountImport;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Description of ChartOfAccountController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */

class ChartOfAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Index  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Index $request)
    {
        return view('pages.chart_of_accounts.index', ['records' => ChartOfAccount::orderby('class')->orderBy('description')->get()]);
    } /**
      * Display the specified resource.
      *
      * @param  Show  $request
      * @param  ChartOfAccount  $chartofaccount
      * @return \Illuminate\Http\Response
      */
    public function show(Show $request, ChartOfAccount $chartofaccount)
    {
        return view('pages.chart_of_accounts.show', [
            'record' => $chartofaccount,
        ]);

    } /**
      * Show the form for creating a new resource.
      *
      * @param  Create  $request
      * @return \Illuminate\Http\Response
      */
    public function create(Create $request)
    {

        return view('pages.chart_of_accounts.create', [
            'model' => new ChartOfAccount,

        ]);
    } /**
      * Store a newly created resource in storage.
      *
      * @param  Store  $request
      * @return \Illuminate\Http\Response
      */
    public function store(Store $request)
    {
        $model = new ChartOfAccount;
        $model->fill($request->all());

        if ($model->save()) {
            AuditLog::auditLog(auth()->user()->id, "Added  Chart Of Account $model->class");
            session()->flash('app_message', 'Chart of Account saved successfully');
            return redirect()->route('chart_of_accounts.index');
        } else {
            session()->flash('app_message', 'Something is wrong while saving ChartOfAccount');
        }
        return redirect()->back();
    } /**
      * Show the form for editing the specified resource.
      *
      * @param  Edit  $request
      * @param  ChartOfAccount  $chartofaccount
      * @return \Illuminate\Http\Response
      */
    public function edit(Edit $request, ChartOfAccount $chartofaccount)
    {

        return view('pages.chart_of_accounts.edit', [
            'model' => $chartofaccount,

        ]);
    } /**
      * Update a existing resource in storage.
      *
      * @param  Update  $request
      * @param  ChartOfAccount  $chartofaccount
      * @return \Illuminate\Http\Response
      */
    public function update(Update $request, ChartOfAccount $chartofaccount)
    {
        $chartofaccount->fill($request->all());

        if ($chartofaccount->save()) {
            AuditLog::auditLog(auth()->user()->id, "Modified  Chart Of Account $chartofaccount->class");
            session()->flash('app_message', 'Chart of Account successfully updated');
            return redirect()->route('chart_of_accounts.index');
        } else {
            session()->flash('app_error', 'Something is wrong while updating ChartOfAccount');
        }
        return redirect()->back();
    } /**
      * Delete a  resource from  storage.
      *
      * @param  Destroy  $request
      * @param  ChartOfAccount  $chartofaccount
      * @return \Illuminate\Http\Response
      * @throws \Exception
      */
    public function destroy(Destroy $request, ChartOfAccount $chartofaccount)
    {
        $deleted = $chartofaccount->class;
        if ($chartofaccount->delete()) {
            AuditLog::auditLog(auth()->user()->id, "Deleted  Chart Of Account $deleted");
            session()->flash('app_message', 'Chart of Account successfully deleted');
        } else {
            session()->flash('app_error', 'Error occurred while deleting ChartOfAccount');
        }

        return redirect()->back();
    }
    public function importForm()
    {
        return view('pages.chart_of_accounts.import');
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx',
        ]);

        $file = $request->file('file');
        $import = new ChartOfAccountImport();
        $rows = Excel::toCollection($import, $file)->first();
        $user_branch = User::userBranchAction();
        $count = 0;
        try {
            foreach ($rows as $row) {


                ChartOfAccount::updateOrInsert(
                    ['class' => $row['class'], 'prefix' => $row['prefix']],
                    [
                        'prefix' => $row['prefix'],
                        'class' => $row['class'],
                        'description' => $row['description'],
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