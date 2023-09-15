<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DosageForm;
use App\Http\Requests\DosageForms\Index;
use App\Http\Requests\DosageForms\Show;
use App\Http\Requests\DosageForms\Create;
use App\Http\Requests\DosageForms\Store;
use App\Http\Requests\DosageForms\Edit;
use App\Http\Requests\DosageForms\Update;
use App\Http\Requests\DosageForms\Destroy;


/**
 * Description of DosageFormController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */

class DosageFormController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Index  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Index $request)
    {
        return view('pages.dosage_forms.index', ['records' => DosageForm::orderBy('name')->get()]);
    } /**
      * Display the specified resource.
      *
      * @param  Show  $request
      * @param  DosageForm  $dosageform
      * @return \Illuminate\Http\Response
      */
    public function show(Show $request, DosageForm $dosageform)
    {
        return view('pages.dosage_forms.show', [
            'record' => $dosageform,
        ]);

    } /**
      * Show the form for creating a new resource.
      *
      * @param  Create  $request
      * @return \Illuminate\Http\Response
      */
    public function create(Create $request)
    {

        return view('pages.dosage_forms.create', [
            'model' => new DosageForm,

        ]);
    } /**
      * Store a newly created resource in storage.
      *
      * @param  Store  $request
      * @return \Illuminate\Http\Response
      */
    public function store(Store $request)
    {
        $model = new DosageForm;
        $model->fill($request->all());

        if ($model->save()) {

            session()->flash('app_message', 'DosageForm saved successfully');
            return redirect()->route('dosage_forms.index');
        } else {
            session()->flash('app_message', 'Something is wrong while saving DosageForm');
        }
        return redirect()->back();
    } /**
      * Show the form for editing the specified resource.
      *
      * @param  Edit  $request
      * @param  DosageForm  $dosageform
      * @return \Illuminate\Http\Response
      */
    public function edit(Edit $request, DosageForm $dosageform)
    {

        return view('pages.dosage_forms.edit', [
            'model' => $dosageform,

        ]);
    } /**
      * Update a existing resource in storage.
      *
      * @param  Update  $request
      * @param  DosageForm  $dosageform
      * @return \Illuminate\Http\Response
      */
    public function update(Update $request, DosageForm $dosageform)
    {
        $dosageform->fill($request->all());

        if ($dosageform->save()) {

            session()->flash('app_message', 'DosageForm successfully updated');
            return redirect()->route('dosage_forms.index');
        } else {
            session()->flash('app_error', 'Something is wrong while updating DosageForm');
        }
        return redirect()->back();
    } /**
      * Delete a  resource from  storage.
      *
      * @param  Destroy  $request
      * @param  DosageForm  $dosageform
      * @return \Illuminate\Http\Response
      * @throws \Exception
      */
    public function destroy(Destroy $request, DosageForm $dosageform)
    {
        if ($dosageform->delete()) {
            session()->flash('app_message', 'DosageForm successfully deleted');
        } else {
            session()->flash('app_error', 'Error occurred while deleting DosageForm');
        }

        return redirect()->back();
    }
}