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



class CompanyController extends Controller
{

    public function index(Index $request)
    {
        return view('pages.companies.index', ['records' => Company::orderBy('name')->get()]);
    }

    public function show(Show $request, Company $company)
    {
        return view('pages.companies.show', [
            'record' => $company,
        ]);

    }

    public function create(Create $request)
    {

        return view('pages.companies.create', [
            'model' => new Company,

        ]);
    }

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
    }

    public function edit(Edit $request, Company $company)
    {

        return view('pages.companies.edit', [
            'model' => $company,

        ]);
    }

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
    }

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
