<?php

namespace App\Http\Controllers;

use App\Imports\ProductUnitMeasureImport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProductUnitMeasure;
use App\Models\Product;
use Maatwebsite\Excel\Facades\Excel;


/**
 * Description of ProductUnitMeasureController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */

class ProductUnitMeasureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('pages.product_unit_measures.index', ['records' => ProductUnitMeasure::orderBy('value')->get()]);
    } /**
      * Display the specified resource.
      *
      * @param  Request  $request
      * @param  ProductUnitMeasure  $productunitmeasure
      * @return \Illuminate\Http\Response
      */
    public function show(Request $request, ProductUnitMeasure $productunitmeasure)
    {
        return view('pages.product_unit_measures.show', [
            'record' => $productunitmeasure,
        ]);

    } /**
      * Show the form for creating a new resource.
      *
      * @param  Request  $request
      * @return \Illuminate\Http\Response
      */
    public function create(Request $request)
    {
        $products = Product::all(['id', 'name', 'code']);

        return view('pages.product_unit_measures.create', [
            'model' => new ProductUnitMeasure,
            "products" => $products,

        ]);
    } /**
      * Store a newly created resource in storage.
      *
      * @param  Request  $request
      * @return \Illuminate\Http\Response
      */
    public function store(Request $request)
    {
        $model = new ProductUnitMeasure;
        $model->fill($request->all());

        if ($model->save()) {

            session()->flash('app_message', 'product unit measure saved successfully');
            return redirect()->route('product_unit_measures.index');
        } else {
            session()->flash('app_message', 'Something is wrong while saving product unit of measure');
        }
        return redirect()->back();
    } /**
      * Show the form for editing the specified resource.
      *
      * @param  Request  $request
      * @param  ProductUnitMeasure  $productunitmeasure
      * @return \Illuminate\Http\Response
      */
    public function edit(Request $request, ProductUnitMeasure $productunitmeasure)
    {
        $products = Product::all(['id', 'name', 'code']);

        return view('pages.product_unit_measures.edit', [
            'model' => $productunitmeasure,
            "products" => $products,

        ]);
    } /**
      * Update a existing resource in storage.
      *
      * @param  Request  $request
      * @param  ProductUnitMeasure  $productunitmeasure
      * @return \Illuminate\Http\Response
      */
    public function update(Request $request, ProductUnitMeasure $productunitmeasure)
    {
        $productunitmeasure->fill($request->all());

        if ($productunitmeasure->save()) {

            session()->flash('app_message', 'Product unit of measure successfully updated');
            return redirect()->route('product_unit_measures.index');
        } else {
            session()->flash('app_error', 'Something is wrong while updating product unit of measure');
        }
        return redirect()->back();
    } /**
      * Delete a  resource from  storage.
      *
      * @param  Request  $request
      * @param  ProductUnitMeasure  $productunitmeasure
      * @return \Illuminate\Http\Response
      * @throws \Exception
      */
    public function destroy(Request $request, ProductUnitMeasure $productunitmeasure)
    {
        if ($productunitmeasure->delete()) {
            session()->flash('app_message', 'Produc uUnit measure successfully deleted');
        } else {
            session()->flash('app_error', 'Error occurred while deleting product unit of measure');
        }

        return redirect()->back();
    }
    public function importForm()
    {
        return view('pages.product_unit_measures.import');
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx',
        ]);

        $file = $request->file('file');
        $import = new ProductUnitMeasureImport();
        $rows = Excel::toCollection($import, $file)->first();
        $faileds = [];
        $count = 0;
        $data = array();
        try {
            foreach ($rows as $row) {
                $product_id = Product::where('code', trim($row['product_code']))->first()->id ?? 0;
                ProductUnitMeasure::updateOrInsert(
                    ['code' => $row['unit_code'], 'product_id' => $product_id],
                    [
                        'product_id' => $product_id,
                        'type' => $row['type'],
                        'value' => $row['value'],
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
        return view('pages.product_unit_measures.import', ['count' => $count]);
    }
}
