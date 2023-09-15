<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Http\Requests\Companies\Index;
use App\Http\Requests\Companies\Show;
use App\Http\Requests\Companies\Create;
use App\Http\Requests\Companies\Store;
use App\Http\Requests\Companies\Edit;
use App\Http\Requests\Companies\Update;
use App\Http\Requests\Companies\Destroy;


/**
 * Description of CompanyController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Index  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Index $request)
    {
        return view('pages.companies.index', ['records' => Company::orderBy('name')->get()]);
    } /**
      * Display the specified resource.
      *
      * @param  Show  $request
      * @param  Company  $company
      * @return \Illuminate\Http\Response
      */
    public function show(Show $request, Company $company)
    {
        return view('pages.companies.show', [
            'record' => $company,
        ]);

    } /**
      * Show the form for creating a new resource.
      *
      * @param  Create  $request
      * @return \Illuminate\Http\Response
      */
    public function create(Create $request)
    {

        return view('pages.companies.create', [
            'model' => new Company,

        ]);
    } /**
      * Store a newly created resource in storage.
      *
      * @param  Store  $request
      * @return \Illuminate\Http\Response
      */
    public function store(Store $request)
    {
        $model = new Company;
        $model->fill($request->all());

        if ($model->save()) {

            session()->flash('app_message', 'Company saved successfully');
            return redirect()->route('companies.index');
        } else {
            session()->flash('app_message', 'Something is wrong while saving Company');
        }
        return redirect()->back();
    } /**
      * Show the form for editing the specified resource.
      *
      * @param  Edit  $request
      * @param  Company  $company
      * @return \Illuminate\Http\Response
      */
    public function edit(Edit $request, Company $company)
    {

        return view('pages.companies.edit', [
            'model' => $company,

        ]);
    } /**
      * Update a existing resource in storage.
      *
      * @param  Update  $request
      * @param  Company  $company
      * @return \Illuminate\Http\Response
      */
    public function update(Update $request, Company $company)
    {
        $company->fill($request->all());

        if ($company->save()) {

            session()->flash('app_message', 'Company successfully updated');
            return redirect()->route('companies.index');
        } else {
            session()->flash('app_error', 'Something is wrong while updating Company');
        }
        return redirect()->back();
    } /**
      * Delete a  resource from  storage.
      *
      * @param  Destroy  $request
      * @param  Company  $company
      * @return \Illuminate\Http\Response
      * @throws \Exception
      */
    public function destroy(Destroy $request, Company $company)
    {
        if ($company->delete()) {
            session()->flash('app_message', 'Company successfully deleted');
        } else {
            session()->flash('app_error', 'Error occurred while deleting Company');
        }

        return redirect()->back();
    }
}