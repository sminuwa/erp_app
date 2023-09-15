<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PaymentMode;
use App\Http\Requests\PaymentModes\Index;
use App\Http\Requests\PaymentModes\Show;
use App\Http\Requests\PaymentModes\Create;
use App\Http\Requests\PaymentModes\Store;
use App\Http\Requests\PaymentModes\Edit;
use App\Http\Requests\PaymentModes\Update;
use App\Http\Requests\PaymentModes\Destroy;


/**
 * Description of PaymentModeController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */

class PaymentModeController extends Controller
{
       /**
     * Display a listing of the resource.
     *
     * @param  Index  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Index $request)
    {
        return view('pages.payment_modes.index', ['records' => PaymentMode::paginate(10)]);
    }    /**
     * Display the specified resource.
     *
     * @param  Show  $request
     * @param  PaymentMode  $paymentmode
     * @return \Illuminate\Http\Response
     */
    public function show(Show $request, PaymentMode $paymentmode)
    {
        return view('pages.payment_modes.show', [
                'record' =>$paymentmode,
        ]);

    }    /**
     * Show the form for creating a new resource.
     *
     * @param  Create  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Create $request)
    {

        return view('pages.payment_modes.create', [
            'model' => new PaymentMode,

        ]);
    }    /**
     * Store a newly created resource in storage.
     *
     * @param  Store  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Store $request)
    {
        $model=new PaymentMode;
        $model->fill($request->all());

        if ($model->save()) {
            
            session()->flash('app_message', 'PaymentMode saved successfully');
            return redirect()->route('payment_modes.index');
            } else {
                session()->flash('app_error', 'Something is wrong while saving PaymentMode');
            }
        return redirect()->back();
    } /**
     * Show the form for editing the specified resource.
     *
     * @param  Edit  $request
     * @param  PaymentMode  $paymentmode
     * @return \Illuminate\Http\Response
     */
    public function edit(Edit $request, PaymentMode $paymentmode)
    {

        return view('pages.payment_modes.edit', [
            'model' => $paymentmode,

            ]);
    }    /**
     * Update a existing resource in storage.
     *
     * @param  Update  $request
     * @param  PaymentMode  $paymentmode
     * @return \Illuminate\Http\Response
     */
    public function update(Update $request,PaymentMode $paymentmode)
    {
        $paymentmode->fill($request->all());

        if ($paymentmode->save()) {
            
            session()->flash('app_message', 'PaymentMode successfully updated');
            return redirect()->route('payment_modes.index');
            } else {
                session()->flash('app_error', 'Something is wrong while updating PaymentMode');
            }
        return redirect()->back();
    }    /**
     * Delete a  resource from  storage.
     *
     * @param  Destroy  $request
     * @param  PaymentMode  $paymentmode
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Destroy $request, PaymentMode $paymentmode)
    {
        if ($paymentmode->delete()) {
                session()->flash('app_message', 'PaymentMode successfully deleted');
            } else {
                session()->flash('app_error', 'Error occurred while deleting PaymentMode');
            }

        return redirect()->back();
    }
}
