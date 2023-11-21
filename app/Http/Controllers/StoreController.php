<?php

namespace App\Http\Controllers;

use App\Imports\StoreImport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Http\Requests\Stores\Index;
use App\Http\Requests\Stores\Show;
use App\Http\Requests\Stores\Create;
use App\Http\Requests\Stores\StoreRequest;
use App\Http\Requests\Stores\Edit;
use App\Http\Requests\Stores\Update;
use App\Http\Requests\Stores\Destroy;
use App\Models\Branch;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;


/**
 * Description of StoreController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Index  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Index $request)
    {
        return view('pages.stores.index', ['records' => Store::orderBy('code')->get()]);
    } /**
      * Display the specified resource.
      *
      * @param  Show  $request
      * @param  Store  $store
      * @return \Illuminate\Http\Response
      */
    public function show(Show $request, Store $store)
    {
        return view('pages.stores.show', [
            'record' => $store,
        ]);

    } /**
      * Show the form for creating a new resource.
      *
      * @param  Create  $request
      * @return \Illuminate\Http\Response
      */
    public function create(Create $request)
    {
        $branches = Branch::where('id', 'LIKE', User::userBranchAction())->orderBy('name')->get();
        return view('pages.stores.create', [
            'model' => new Store,
            'branches' => $branches,

        ]);
    } /**
      * Store a newly created resource in storage.
      *
      * @param  Store  $request
      * @return \Illuminate\Http\Response
      */
    public function store(StoreRequest $request)
    {
        $model = new Store;
        $model->fill($request->all());

        if ($model->save()) {
            $action = "Added a new store : " . $model->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Store saved successfully');
            return redirect()->route('stores.index');
        } else {
            session()->flash('app_message', 'Something is wrong while saving Store');
        }
        return redirect()->back();
    } /**
      * Show the form for editing the specified resource.
      *
      * @param  Edit  $request
      * @param  Store  $store
      * @return \Illuminate\Http\Response
      */
    public function edit(Edit $request, Store $store)
    {
        $branches = Branch::where('id', 'LIKE', User::userBranchAction())->orderBy('name')->get();
        return view('pages.stores.edit', [
            'model' => $store,
            'branches' => $branches,

        ]);
    } /**
      * Update a existing resource in storage.
      *
      * @param  Update  $request
      * @param  Store  $store
      * @return \Illuminate\Http\Response
      */
    public function update(Update $request, Store $store)
    {
        $store->fill($request->all());

        if ($store->save()) {
            $action = "Updated a store : " . $store->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Store successfully updated');
            return redirect()->route('stores.index');
        } else {
            session()->flash('app_error', 'Something is wrong while updating Store');
        }
        return redirect()->back();
    } /**
      * Delete a  resource from  storage.
      *
      * @param  Destroy  $request
      * @param  Store  $store
      * @return \Illuminate\Http\Response
      * @throws \Exception
      */
    public function destroy(Destroy $request, Store $store)
    {
        if ($store->storeProducts()->count() > 0) {
            session()->flash('app_error', 'Store cannot be deleted because it has some products in it');
        } else {
            if ($store->delete()) {
                $action = "Deleted a store : " . $store->name;
                AuditLog::auditLog(Auth::id(), $action);
                session()->flash('app_message', 'Store successfully deleted');
            } else {
                session()->flash('app_error', 'Error occurred while deleting Store');
            }
        }
        return redirect()->back();
    }
    public function importForm()
    {
        return view('pages.stores.import');
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx',
        ]);

        $file = $request->file('file');
        $import = new StoreImport();
        $rows = Excel::toCollection($import, $file)->first();
        $user_branch = User::userBranchAction();
        $faileds = [];
        $count = 0;
        $data = array();
        try {
            foreach ($rows as $row) {
                Store::updateOrInsert(
                    ['code' => $row['code']],
                    [
                        'name' => trim($row['name']),
                        'branch_id' => Branch::where('code', trim($row['branch_code']))->first()->id ?? 0,
                        'created_at' => Carbon::now(),
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
        return view('pages.stores.import', ['count' => $count]);
    }
}
