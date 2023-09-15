<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProductExpireSetting;
use App\Models\Product;
use App\Models\User;
use App\Http\Requests\ProductExpireSettings\Index;
use App\Http\Requests\ProductExpireSettings\Show;
use App\Http\Requests\ProductExpireSettings\Create;
use App\Http\Requests\ProductExpireSettings\Store;
use App\Http\Requests\ProductExpireSettings\Edit;
use App\Http\Requests\ProductExpireSettings\Update;
use App\Http\Requests\ProductExpireSettings\Destroy;


/**
 * Description of ProductExpireSettingController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */

class ProductExpireSettingController extends Controller
{
       /**
     * Display a listing of the resource.
     *
     * @param  Index  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Index $request)
    {
        return view('pages.product_expire_settings.index', ['records' => ProductExpireSetting::latest()->get()]);
    }    /**
     * Display the specified resource.
     *
     * @param  Show  $request
     * @param  ProductExpireSetting  $productexpiresetting
     * @return \Illuminate\Http\Response
     */
    public function show(Show $request, ProductExpireSetting $productexpiresetting)
    {
        return view('pages.product_expire_settings.show', [
                'record' =>$productexpiresetting,
        ]);

    }    /**
     * Show the form for creating a new resource.
     *
     * @param  Create  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Create $request)
    {
		$products = Product::all(['id','name']);
		$users = User::all(['id','name']);

        return view('pages.product_expire_settings.create', [
            'model' => new ProductExpireSetting,
			"products" => $products,
			"users" => $users,

        ]);
    }    /**
     * Store a newly created resource in storage.
     *
     * @param  Store  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Store $request)
    {
        $model=new ProductExpireSetting;
        $model->fill($request->all());

        if ($model->save()) {
            
            session()->flash('app_message', 'Product expire setting saved successfully');
            return redirect()->route('product_expire_settings.index');
            } else {
                session()->flash('app_message', 'Something is wrong while saving ProductExpireSetting');
            }
        return redirect()->back();
    } /**
     * Show the form for editing the specified resource.
     *
     * @param  Edit  $request
     * @param  ProductExpireSetting  $productexpiresetting
     * @return \Illuminate\Http\Response
     */
    public function edit(Edit $request, ProductExpireSetting $productexpiresetting)
    {
		$products = Product::all(['id','name']);
		$users = User::all(['id','name']);

        return view('pages.product_expire_settings.edit', [
            'model' => $productexpiresetting,
			"products" => $products,
			"users" => $users,

            ]);
    }    /**
     * Update a existing resource in storage.
     *
     * @param  Update  $request
     * @param  ProductExpireSetting  $productexpiresetting
     * @return \Illuminate\Http\Response
     */
    public function update(Update $request,ProductExpireSetting $productexpiresetting)
    {
        $productexpiresetting->fill($request->all());

        if ($productexpiresetting->save()) {
            
            session()->flash('app_message', 'Product expire setting successfully updated');
            return redirect()->route('product_expire_settings.index');
            } else {
                session()->flash('app_error', 'Something is wrong while updating ProductExpireSetting');
            }
        return redirect()->back();
    }    /**
     * Delete a  resource from  storage.
     *
     * @param  Destroy  $request
     * @param  ProductExpireSetting  $productexpiresetting
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Destroy $request, ProductExpireSetting $productexpiresetting)
    {
        if ($productexpiresetting->delete()) {
                session()->flash('app_message', 'Product expiration setting successfully deleted');
            } else {
                session()->flash('app_error', 'Error occurred while deleting ProductExpireSetting');
            }

        return redirect()->back();
    }
}
