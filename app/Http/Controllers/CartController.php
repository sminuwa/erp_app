<?php

namespace App\Http\Controllers;

use App\Models\BranchProductPrice;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;
use Darryldecode\Cart\Cart;
use Brian2694\Toastr\Facades\Toastr;

class CartController extends Controller
{
    public function cartList()
    {
        $cartItems = \Cart::getContent();
        // dd($cartItems);
        return view('cart', compact('cartItems'));
    }


    public function addToCart(Request $request)
    {
//        return \Cart::getContent();
//        return $request;
        $validated = $request->validate([
            'id' => 'required',
            'name' => 'required',
            'code' => 'required',
            'sold_price' => 'required',
            'qty' => 'required',
            'cost_price' => 'required'
        ]);
        $customer = Customer::find($request->customer);
        $qty = $request->qty;
        $selling_price = $request->selling_price;
        $cost_price = $request->cost_price;
        $qty_available = $request->qty_available;
        $store = $request->store;
        $add = \Cart::add([
            'id' => $request->id,
            'name' => $request->name,
            'price' => $request->sold_price == 0 ? 1 :$request->sold_price ,
            'quantity' => $qty == 0 ? 1 : $qty,
            'attributes' => array(
                'cost_price' => $cost_price,
                'code'=>$request->code,
                'selling_price' => $selling_price,
                'qty_available' => $qty_available,
                'discount' => 0,
                'store'=> $store,
                'unit' =>$request->unit ?? ''
            ),
        ]);
//        return \Cart::getContent();
        if ($add) {
            session()->flash('success', 'Product is Added to Cart Successfully !');
            return back()->with(['customer'=> $customer]);
        } else {
            session()->flash('success','Product not added to cart');
            return back()->with('customer', $customer);
        }
    }

    public function updateCart(Request $request)
    {
        $sold_price = $request->sold_price;
        if ($request->has('percent')) {
            $percent = $request->percent;
            $sold_price = ceil($request->cost_price + ($request->cost_price / 100) * $percent);
        }

        \Cart::update(
            $request->id,
            [
                'quantity' => [
                    'relative' => false,
                    'value' => $request->quantity
                ],
                'price' => $sold_price,
                'attributes' => array(
                    'cost_price' => $request->cost_price,
                    'selling_price' => $request->selling_price,
                    'code'=>$request->code,
                    'discount' => $request->selling_price - $request->sold_price,
                    'qty_available' => $request->qty_available,
                    'store'=>$request->store,
                    'unit'=>$request->unit,
                )
            ]
        );

        session()->flash('success', 'Item Cart is Updated Successfully !');
        if ($request->ajax()) {
            return \Cart::getTotal();
        }
        return redirect()->back();
    }

    public function removeCart(Request $request, $id)
    {
        \Cart::remove($request->id);
        session()->flash('success', 'Item Cart Remove Successfully !');

        return redirect()->back();
    }

    public function clearAllCart()
    {
        \Cart::clear();

        session()->flash('success', 'All Item Cart Clear Successfully !');

        return redirect()->back();
    }


    public function loadCartItem(Request $request){
        $type = $request->type;
        return view('components.cart', compact('type'));
    }

    public function addCartItem(Request $request)
    {
        $type =$request->type;
        if($type == 'interstore'){
            $product = Product::find($request->product_id);
            $add = \Cart::add([
                'id' => $this->generateRandomString(),
                'name' => $product->name,
                'price' => 0,
                'quantity' => $request->qty_transfered,
                'attributes' => array(
                    'source_store_id' => $request->source_store_id,
                    'destination_store_id' => $request->destination_store_id,
                    'product_id' => $request->product_id,
                    'code' => $product->code,
                ),
            ]);
        }
        if($type == 'intersite'){
            $product = Product::find($request->product_id);
            $cost_price = BranchProductPrice::where(['product_id'=>$request->product_id, 'branch_id'=>auth()->user()->branch->id])->first();
            $add = \Cart::add([
                'id' => $this->generateRandomString(),
                'name' => $product->name,
                'price' => $cost_price->cost_price ?? 1,
                'quantity' => $request->quantity,
                'attributes' => array(
                    'store_id' => $request->store_id,
                    'product_id' => $request->product_id,
                    'code' => $product->code,
                ),
            ]);
        }
        if($type == 'grn') {
            $product = Product::find($request->product_id);
            $store = Store::find($request->store_id);
            $qty = $request->qty_supplied;
            $qty_available = $request->qty_available;
            $add = \Cart::add([
                'id' => $request->product_id,
                'name' => $product->name,
                'price' => $request->unit_price == 0 ? 1 : $request->unit_price,
                'quantity' => $qty == 0 ? 1 : $qty,
                'attributes' => array(
                    'cost_price' => $request->unit_price ?? '',
                    'code' => $product->code,
                    'selling_price' => $selling_price ?? '',
                    'qty_available' => $qty_available ?? '',
                    'discount' => 0,
                    'store' => $request->store ?? '',
                    'unit' => $product->unit ?? '',
                    'store_id' => $request->store_id ?? '',
                    'store_code' => $store->code ?? '',
                ),
            ]);
        }
        if($type == 'request') {
            $product = Product::find($request->product_id);
            $store = Store::find($request->store_id);
            $qty = $request->qty_supplied;
            $qty_available = $request->qty_available;
            $add = \Cart::add([
                'id' => $request->product_id,
                'name' => $product->name,
                'price' => $request->unit_price == 0 ? 1 : $request->unit_price,
                'quantity' => $qty == 0 ? 1 : $qty,
                'attributes' => array(
                    'cost_price' => $request->unit_price ?? '',
                    'code' => $product->code,
                    'selling_price' => $selling_price ?? '',
                    'qty_available' => $qty_available ?? '',
                    'discount' => 0,
                    'store' => $request->store ?? '',
                    'unit' => $product->unit ?? '',
                    'store_id' => $request->store_id ?? '',
                    'store_code' => $store->code ?? '',
                ),
            ]);
        }
        if($type == 'adjustment') {
            $add = \Cart::add([
                'id' => $this->generateRandomString(),
                'name' => Product::find($request->product_id)->name,
                'price' => 0, //Thos is not applicable here
                'quantity' => abs($request->adjusted_qty),
                'attributes' => array(
                    'store_id' => $request->store_id,
                    'product_id' => $request->product_id,
                    'available_qty' => $request->available_qty,
                ),
            ]);
        }

        if($type == 'order') {
//            return $request;
            $customer = Customer::find($request->customer);
            $qty = $request->qty;
            $selling_price = $request->selling_price;
            $cost_price = $request->cost_price;
            $qty_available = $request->qty_available;
            $store = $request->store;
            $add = \Cart::add([
                'id' => $request->id,
                'name' => $request->name,
                'price' => $request->sold_price == 0 ? 1 : $request->sold_price ,
                'quantity' => $qty == 0 ? 1 : $qty,
                'attributes' => array(
                    'cost_price' => $cost_price,
                    'code'=>$request->code,
                    'selling_price' => $selling_price,
                    'qty_available' => $qty_available,
                    'discount' => 0,
                    'store'=> $store,
                    'unit' =>$request->unit ?? '',
                    'product_id' =>$product->id ?? ''
                ),
            ]);
        }

        if($type == 'invoice') {

        }

        if($type == 'proforma') {

        }

        return view('components.cart', compact('type'));
    }

    public function updateCartItem(Request $request, $id)
    {
        $type =$request->type;
        if($type == 'grn') {
            $sold_price = $request->sold_price;
            if ($request->has('percent')) {
                $percent = $request->percent;
                $sold_price = ceil($request->cost_price + ($request->cost_price / 100) * $percent);
            }
            $store = Store::find($request->store_id);
            \Cart::update(
                $request->id,
                [
                    'quantity' => [
                        'relative' => false,
                        'value' => $request->quantity
                    ],
                    'price' => $request->price,
                    'attributes' => array(
                        'cost_price' => $request->price,
                        'selling_price' => $request->selling_price ?? '',
                        'code'=>$request->code,
                        'discount' => $request->selling_price - $request->sold_price ?? '',
                        'qty_available' => $request->qty_available,
                        'store'=>$request->store,
                        'unit'=>$request->unit,
                        'store_id' =>$request->store_id ?? '',
                        'store_code' =>$store->code ?? '',
                    )
                ]
            );
        }
        if($type == 'request') {
            $sold_price = $request->sold_price;
            if ($request->has('percent')) {
                $percent = $request->percent;
                $sold_price = ceil($request->cost_price + ($request->cost_price / 100) * $percent);
            }
            $store = Store::find($request->store_id);
            \Cart::update(
                $request->id, [
                    'quantity' => [
                        'relative' => false,
                        'value' => $request->quantity
                    ],
                    'price' => $request->price,
                    'attributes' => array(
                        'cost_price' => $request->price,
                        'selling_price' => $request->selling_price ?? '',
                        'code'=>$request->code,
                        'discount' => $request->selling_price - $request->sold_price ?? '',
                        'qty_available' => $request->qty_available,
                        'store'=>$request->store,
                        'unit'=>$request->unit,
                        'store_id' =>$request->store_id ?? '',
                        'store_code' =>$store->code ?? '',
                    )
                ]
            );
        }
        if($type == 'adjustment') {
            $product = Product::find($request->product_id);
            \Cart::update(
                $request->id, [
                'quantity' => [
                    'relative' => false,
                    'value' => $request->quantity
                ],
                'name' => $product->name,
                'price' => 0, //Thos is not applicable here
                'attributes' => array(
                    'store_id' => $request->store_id,
                    'product_id' => $request->product_id,
                    'available_qty' => $request->available_qty,
                ),
            ]);
        }
        if($type == 'intersite') {
            $product = Product::find($request->product_id);
            \Cart::update(
                $request->id, [
                'quantity' => [
                    'relative' => false,
                    'value' => $request->quantity
                ],
                'name' => $product->name,
                'price' => $request->cost_price, //This is not applicable here
                'attributes' => array(
                    'store_id' => $request->store_id,
                    'product_id' => $request->product_id,
                    'code' => $request->code,
                ),
            ]);
        }
        if($type == 'order') {
            return $request;
            $sold_price = $request->sold_price;
            \Cart::update(
                $request->id,
                [
                    'quantity' => [
                        'relative' => false,
                        'value' => $request->quantity
                    ],
                    'price' => $sold_price,
                    'attributes' => array(
                        'cost_price' => $request->cost_price,
                        'selling_price' => $request->selling_price,
                        'code'=>$request->code,
                        'discount' => $request->selling_price - $request->sold_price,
                        'qty_available' => $request->qty_available,
                        'store'=>$request->store,
                        'unit'=>$request->unit,
                    )
                ]
            );
        }

        if($type == 'invoice') {

        }

        if($type == 'proforma') {

        }
        if ($request->ajax()) {
            return \Cart::getTotal();
        }
        $type =$request->type;
        return view('components.cart', compact('type'));
    }

    public function deleteCartItem(Request $request, $id){
        \Cart::remove($id);
        $type =$request->type;
        return view('components.cart', compact('type'));
    }

    public function generateRandomString($length = 5)
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}
