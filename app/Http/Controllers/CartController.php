<?php

namespace App\Http\Controllers;

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

        $validated = $request->validate([
            'id' => 'required',
            'name' => 'required',
            'code' => 'required',
            'sold_price' => 'required',
            'qty' => 'required',
            'cost_price' => 'required'
        ]);
        $qty = $request->qty;
        $selling_price = $request->selling_price;
        $cost_price = $request->cost_price;
        $qty_available = $request->qty_available;
        $store = $request->store;
        $add = \Cart::add([
            'id' => $request->id,
            'name' => $request->name,
            'price' => $request->sold_price,
            'quantity' => $qty == 0 ? 1 : $qty,
            'attributes' => array('cost_price' => $cost_price, 'code'=>$request->code,'selling_price' => $selling_price, 'qty_available' => $qty_available, 'discount' => 0,'store'=>$store),
        ]);
        //dd(\Cart::getContent());
        if ($add) {
            session()->flash('success', 'Product is Added to Cart Successfully !');
            return redirect()->back();

        } else {

            session()->flash('Product not added to cart');
            return redirect()->back();
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
                'attributes' => array('cost_price' => $request->cost_price, 'selling_price' => $request->selling_price, 'code'=>$request->code, 'discount' => $request->selling_price - $request->sold_price, 'qty_available' => $request->qty_available,'store'=>$request->store)
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

}
