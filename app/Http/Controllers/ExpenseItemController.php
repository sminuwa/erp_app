<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ExpenseItem;
use App\Http\Requests\ExpenseItems\Index;
use App\Http\Requests\ExpenseItems\Show;
use App\Http\Requests\ExpenseItems\Create;
use App\Http\Requests\ExpenseItems\Store;
use App\Http\Requests\ExpenseItems\Edit;
use App\Http\Requests\ExpenseItems\Update;
use App\Http\Requests\ExpenseItems\Destroy;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;


/**
 * Description of ExpenseItemController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */

class ExpenseItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Index  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Index $request)
    {
        return view('pages.expense_items.index', ['records' => ExpenseItem::orderBy('code')->get()]);
    } /**
  * Display the specified resource.
  *
  * @param  Show  $request
  * @param  ExpenseItem  $expenseitem
  * @return \Illuminate\Http\Response
  */
    public function show(Show $request, ExpenseItem $expenseitem)
    {
        return view('pages.expense_items.show', [
            'record' => $expenseitem,
        ]);

    } /**
  * Show the form for creating a new resource.
  *
  * @param  Create  $request
  * @return \Illuminate\Http\Response
  */
    public function create(Create $request)
    {

        return view('pages.expense_items.create', [
            'model' => new ExpenseItem,
            'code'=>str_pad((ExpenseItem::max('id')+1),3,"0",STR_PAD_LEFT),

        ]);
    } /**
  * Store a newly created resource in storage.
  *
  * @param  Store  $request
  * @return \Illuminate\Http\Response
  */
    public function store(Store $request)
    {
        $model = new ExpenseItem;
        $model->fill($request->all());

        if ($model->save()) {
            $action = "Modified expense item: " . $model->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'ExpenseItem saved successfully');
            return redirect()->route('expense_items.index');
        }
        else {
            session()->flash('app_message', 'Something is wrong while saving ExpenseItem');
        }
        return redirect()->back();
    } /**
  * Show the form for editing the specified resource.
  *
  * @param  Edit  $request
  * @param  ExpenseItem  $expenseitem
  * @return \Illuminate\Http\Response
  */
    public function edit(Edit $request, ExpenseItem $expenseitem)
    {

        return view('pages.expense_items.edit', [
            'model' => $expenseitem,

        ]);
    } /**
  * Update a existing resource in storage.
  *
  * @param  Update  $request
  * @param  ExpenseItem  $expenseitem
  * @return \Illuminate\Http\Response
  */
    public function update(Update $request, ExpenseItem $expenseitem)
    {
        $expenseitem->fill($request->all());

        if ($expenseitem->save()) {
            $action = "Updated expense item: " . $expenseitem->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'ExpenseItem successfully updated');
            return redirect()->route('expense_items.index');
        }
        else {
            session()->flash('app_error', 'Something is wrong while updating ExpenseItem');
        }
        return redirect()->back();
    } /**
  * Delete a  resource from  storage.
  *
  * @param  Destroy  $request
  * @param  ExpenseItem  $expenseitem
  * @return \Illuminate\Http\Response
  * @throws \Exception
  */
    public function destroy(Destroy $request, ExpenseItem $expenseitem)
    {
        if ($expenseitem->delete()) {
            $action = "Deleted expense item: " . $expenseitem->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'ExpenseItem successfully deleted');
        }
        else {
            session()->flash('app_error', 'Error occurred while deleting ExpenseItem');
        }

        return redirect()->back();
    }
}
