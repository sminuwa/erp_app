<?php

namespace App\Http\Controllers;

use App\Imports\CategoryImport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Requests\Categories\Index;
use App\Http\Requests\Categories\Show;
use App\Http\Requests\Categories\Create;
use App\Http\Requests\Categories\Store;
use App\Http\Requests\Categories\Edit;
use App\Http\Requests\Categories\Update;
use App\Http\Requests\Categories\Destroy;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;


/**
 * Description of CategoryController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Index  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Index $request)
    {

        return view('pages.categories.index', ['records' => Category::orderBy('name')->get()]);
    } /**
  * Display the specified resource.
  *
  * @param  Show  $request
  * @param  Category  $category
  * @return \Illuminate\Http\Response
  */
    public function show(Show $request, Category $category)
    {
        return view('pages.categories.show', [
            'record' => $category,
        ]);

    } /**
  * Show the form for creating a new resource.
  *
  * @param  Create  $request
  * @return \Illuminate\Http\Response
  */
    public function create(Create $request)
    {

        return view('pages.categories.create', [
            'model' => new Category,

        ]);
    }

    public function store(Store $request)
    {

        /*$model = new Category;
        $model->fill($request->all());*/
        try{
            if ($model = Category::createRecord($request->code, $request->name)) {
                $action = "Added a new item group: " . $model->name;
                AuditLog::auditLog(Auth::id(), $action);
                session()->flash('app_message', 'Category saved successfully');
                if ($request->has('shortcut'))
                    return redirect()->back();
                return redirect()->route('categories.index');
            }
            else {
                session()->flash('app_error', 'Something is wrong while saving Category');
            }
            return redirect()->back();
        }catch (\Exception $e){
            return redirect()->back()->with('app_error', 'Something is wrong while saving Category.');
        }
    } /**
  * Show the form for editing the specified resource.
  *
  * @param  Edit  $request
  * @param  Category  $category
  * @return \Illuminate\Http\Response
  */
    public function edit(Edit $request, Category $category)
    {

        return view('pages.categories.edit', [
            'model' => $category,

        ]);
    } /**
  * Update a existing resource in storage.
  *
  * @param  Update  $request
  * @param  Category  $category
  * @return \Illuminate\Http\Response
  */
    public function update(Update $request, Category $category)
    {
        $category->fill($request->all());

        if ($category->save()) {
            $action = "Updated an item group: " . $category->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Category successfully updated');
            return redirect()->route('categories.index');
        }
        else {
            session()->flash('app_error', 'Something is wrong while updating Category');
        }
        return redirect()->back();
    } /**
  * Delete a  resource from  storage.
  *
  * @param  Destroy  $request
  * @param  Category  $category
  * @return \Illuminate\Http\Response
  * @throws \Exception
  */
    public function destroy(Destroy $request, Category $category)
    {
        if (!$category->has_record()) {
            if($category->delete() && $category->deleteAccounts()) {
                $action = "Deleted an item group: " . $category->name;
                AuditLog::auditLog(Auth::id(), $action);
                session()->flash('app_message', 'Category successfully deleted');
            }
        }
        else {
            session()->flash('app_error', 'Cannot delete this record.');
        }

        return redirect()->back();
    }
    public function importForm()
    {
        $this->authorize('categories.import.form');
        return view('pages.categories.import');
    }
    public function import(Request $request)
    {
        $this->authorize('categories.import');
        $request->validate([
            'file' => 'required|file|mimes:xlsx',
        ]);

        $file = $request->file('file');
        $import = new CategoryImport();
        $rows = Excel::toCollection($import, $file)->first();
        $user_branch = User::userBranchAction();
        $faileds = [];
        $count = 0;
        $data = array();
        try {
            foreach ($rows as $row) {
                Category::updateOrInsert(
                    ['code' => $row['code']],
                    [
                        'name' => trim($row['name']),
                        'asset_account' => $row['asset_account'],
                        'cost_account' => $row['cost_account'],
                        'revenue_account' => $row['revenue_account'],
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
        return view('pages.categories.import', ['count' => $count]);
    }
}
