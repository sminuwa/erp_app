<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
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
//        $order = Customer::where('type', $customer->type)->get();
//        return $order;
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


    public function loadCartItem(){
        return view('components.cart');
    }

    public function addCartItem(Request $request)
    {
        $product = Product::find($request->product_id);
        $qty = $request->qty_supplied;
        $qty_available = $request->qty_available;
        $add = \Cart::add([
            'id' => $request->product_id,
            'name' => $product->name,
            'price' => $request->unit_price == 0 ? 1 : $request->unit_price ,
            'quantity' => $qty == 0 ? 1 : $qty,
            'attributes' => array(
                'cost_price' => $request->unit_price ?? '',
                'code'=> $product->code,
                'selling_price' => $selling_price ?? '',
                'qty_available' => $qty_available ?? '',
                'discount' => 0,
                'store'=> $request->store ?? '',
                'unit' =>$product->unit ?? ''
            ),
        ]);
        return view('components.cart');
    }

    public function updateCartItem(Request $request, $id)
    {
//        return $request;
        $sold_price = $request->sold_price;
        if ($request->has('percent')) {
            $percent = $request->percent;
            $sold_price = ceil($request->cost_price + ($request->cost_price / 100) * $percent);
        }

        \Cart::update(
            $request->store_product_id,
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
                )
            ]
        );
        if ($request->ajax()) {
            return \Cart::getTotal();
        }
        return view('components.cart');
    }

    public function deleteCartItem($id){
        \Cart::remove($id);
        return view('components.cart');
    }

}
