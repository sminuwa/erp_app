<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\DosageForm;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Requests\Products\Index;
use App\Http\Requests\Products\Show;
use App\Http\Requests\Products\Create;
use App\Http\Requests\Products\Store;
use App\Http\Requests\Products\Edit;
use App\Http\Requests\Products\Update;
use App\Http\Requests\Products\Destroy;
use GrahamCampbell\ResultType\Success;
use App\Models\Category;
use App\Models\BranchProductPrice;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;


/**
 * Description of ProductController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Index  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Index $request)
    {
        return view('pages.products.index', ['records' => Product::orderBy('created_at', 'desc')->limit(10)->get()]);
    } /**
      * Display the specified resource.
      *
      * @param  Show  $request
      * @param  Product  $product
      * @return \Illuminate\Http\Response
      */
    public function show(Show $request, Product $product)
    {
        return view('pages.products.show', [
            'record' => $product,
        ]);

    } /**
      * Show the form for creating a new resource.
      *
      * @param  Create  $request
      * @return \Illuminate\Http\Response
      */
    public function create(Create $request)
    {
        $categories = Category::orderBy('name')->get();
        $companies = Company::orderBy('name')->get();
        return view('pages.products.create', [
            'model' => new Product,
            'categories' => $categories,
            'companies' => $companies,
        ]);
    }

    public function store(Store $request)
    {


//        $model = new Product;
//        $model->fill($request->except(['shortcut']));
        if ($model = Product::createRecord($request->comany_id,$request->category_id, $request->name, $request->barcode, $request->expiry_status, $request->status)) {
//            $model->addStoreProduct();
            $action = "Added a new product: " . $model->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Product saved successfully');
            if ($request->has('shortcut'))
                return redirect()->back();
            return redirect()->route('products.index');
        } else {
            session()->flash('app_error', 'Something is wrong while saving Product');
        }
        return redirect()->back();
    } /**
      * Show the form for editing the specified resource.
      *
      * @param  Edit  $request
      * @param  Product  $product
      * @return \Illuminate\Http\Response
      */
    public function edit(Edit $request, Product $product)
    {
        $companies = Company::orderBy('name')->get();
        return view('pages.products.edit', [
            'model' => $product,
            'companies' => $companies,

        ]);
    } /**
      * Update a existing resource in storage.
      *
      * @param  Update  $request
      * @param  Product  $product
      * @return \Illuminate\Http\Response
      */
    public function update(Update $request, Product $product)
    {
        $product->fill($request->all());

        if ($product->save()) {
            $action = "Updated a product: " . $product->name;
            AuditLog::auditLog(Auth::id(), $action);
            session()->flash('app_message', 'Product successfully updated');
            return redirect()->route('products.index');
        } else {
            session()->flash('app_error', 'Something is wrong while updating Product');
        }
        return redirect()->back();
    } /**
      * Delete a  resource from  storage.
      *
      * @param  Destroy  $request
      * @param  Product  $product
      * @return \Illuminate\Http\Response
      * @throws \Exception
      */
    public function destroy(Destroy $request, Product $product)
    {
        if ($product->storeProducts()->count() > 0) {
            session()->flash('app_error', 'This product cannot be deleted because it once sold to someone');
        } else {
            if ($product->delete()) {
                $action = "Deleted a product: " . $product->name;
                AuditLog::auditLog(Auth::id(), $action);
                session()->flash('app_message', 'Product successfully deleted');
            } else {
                session()->flash('app_error', 'Error occurred while deleting Product');
            }
        }


        return redirect()->back();
    }

    public function purchasePrice(Request $request){
        $method = $request->method();
        switch($method){
            case 'GET':
                return view('pages.purchase_prices.create');
            case 'POST':
                $product_id = $request->purchase_price;
                $purchase_price = $request->purchase_price;
                $product = Product::find($product_id);
                if (($product->purchase_price = $purchase_price) && $product->save()) {
                    $action = "Updated product purchase price: " . $product->name;
                    AuditLog::auditLog(Auth::id(), $action);
                    session()->flash('app_message', 'Purchase price updated successfully');
                } else {
                    session()->flash('app_error', 'Error occurred while updating purchase price');
                }
                return back();

            default: return back();
        }
    }
}
