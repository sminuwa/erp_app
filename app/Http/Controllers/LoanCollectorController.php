<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\LoanCollector;
use App\Http\Requests\LoanCollectors\Index;
use App\Http\Requests\LoanCollectors\Show;
use App\Http\Requests\LoanCollectors\Create;
use App\Http\Requests\LoanCollectors\Store;
use App\Http\Requests\LoanCollectors\Edit;
use App\Http\Requests\LoanCollectors\Update;
use App\Http\Requests\LoanCollectors\Destroy;
use Illuminate\Support\Facades\DB;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


/**
 * Description of LoanCollectorController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */

class LoanCollectorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Index  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Index $request)
    {
        return view('pages.loan_collectors.index', ['records' => LoanCollector::where('branch_id', 'LIKE', User::userBranchAction())
            ->orderBy('name')->take(10)->get()]);
    }
    public function search(Request $request)
    {
        $search_value = $request->refno;
        $records = LoanCollector::where('reg_code', 'LIKE', "%$search_value%")
            ->where('branch_id', 'LIKE', User::userBranchAction())
            ->orWhere('phone', 'LIKE', "%$search_value%")
            ->orWhere('email', 'LIKE', "%$search_value%")
            ->orderBy('name')->get();
        return view('pages.loan_collectors.index', ['records' => $records]);
    }
    /**
     * Display the specified resource.
     *
     * @param  Show  $request
     * @param  LoanCollector  $loancollector
     * @return \Illuminate\Http\Response
     */
    public function show(Show $request, LoanCollector $loancollector)
    {
        return view('pages.loan_collectors.show', [
            'record' => $loancollector,
        ]);

    } /**
  * Show the form for creating a new resource.
  *
  * @param  Create  $request
  * @return \Illuminate\Http\Response
  */
    public function create(Create $request)
    {
        return view('pages.loan_collectors.create', [
            'model' => new LoanCollector,
            'reg_code' => $this->regCode(),

        ]);
    } /**
  * Store a newly created resource in storage.
  *
  * @param  Store  $request
  * @return \Illuminate\Http\Response
  */
    public function store(Store $request)
    {
        $model = new LoanCollector;
        $model->fill($request->all());
        
        if ($model->save()) {
            $model->branch_id = User::userBranchAction();
            $model->save();
            $action = "Registered a new loan collector: " . $model->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Loan collector saved successfully');
            return redirect()->route('loan_collectors.index');
        }
        else {
            session()->flash('app_message', 'Something is wrong while saving Loan collector');
        }
        return redirect()->back();
    } /**
  * Show the form for editing the specified resource.
  *
  * @param  Edit  $request
  * @param  LoanCollector  $loancollector
  * @return \Illuminate\Http\Response
  */
    public function edit(Edit $request, LoanCollector $loancollector)
    {

        return view('pages.loan_collectors.edit', [
            'model' => $loancollector,

        ]);
    } /**
  * Update a existing resource in storage.
  *
  * @param  Update  $request
  * @param  LoanCollector  $loancollector
  * @return \Illuminate\Http\Response
  */
    public function update(Update $request, LoanCollector $loancollector)
    {

        $loancollector->fill($request->all());

        if ($loancollector->save()) {
            $action = "Updated a loan collector: " . $loancollector->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'LoanCollector successfully updated');
            return redirect()->route('loan_collectors.index');
        }
        else {
            session()->flash('app_error', 'Something is wrong while updating LoanCollector');
        }
        return redirect()->back();
    } /**
  * Delete a  resource from  storage.
  *
  * @param  Destroy  $request
  * @param  LoanCollector  $loancollector
  * @return \Illuminate\Http\Response
  * @throws \Exception
  */
    public function destroy(Destroy $request, LoanCollector $loancollector)
    {
        if ($loancollector->loans->count('*') > 0) {
            $action = "Deleted a loan collector: " . $loancollector->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_error', 'Record cannot be deleted because a loan has been given to the person');
            return redirect()->back();
        }

        if ($loancollector->delete()) {
            session()->flash('app_message', 'Loan collector successfully deleted');
        }
        else {
            session()->flash('app_error', 'Error occurred while deleting Loan collector');
        }

        return redirect()->back();
    }
    public function regCode()
    {
        $invoice = DB::table('loan_collectors')->select(DB::raw('MAX(SUBSTR(reg_code,3,5)) as max'))->first();
        return 'LC' . str_pad(($invoice->max + 1), 3, "0", STR_PAD_LEFT);
    }
}
