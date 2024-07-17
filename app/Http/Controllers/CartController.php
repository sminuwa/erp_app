<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchProductPrice;
use App\Models\Customer;
use App\Models\GeneralAccount;
use App\Models\Product;
use App\Models\ProductUnitMeasure;
use App\Models\Store;
use App\Models\StoreProduct;
use App\Models\Supplier;
use App\Models\User;
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
            'price' => $request->sold_price == 0 ? 1 : $request->sold_price,
            'quantity' => $qty == 0 ? 1 : $qty,
            'attributes' => array(
                'cost_price' => $cost_price,
                'code' => $request->code,
                'selling_price' => $selling_price,
                'qty_available' => $qty_available,
                'discount' => 0,
                'store' => $store,
                'unit' => $request->unit ?? ''
            ),
        ]);
        //        return \Cart::getContent();
        if ($add) {
            session()->flash('success', 'Product is Added to Cart Successfully !');
            return back()->with(['customer' => $customer]);
        } else {
            session()->flash('success', 'Product not added to cart');
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
                    'code' => $request->code,
                    'discount' => $request->selling_price - $request->sold_price,
                    'qty_available' => $request->qty_available,
                    'store' => $request->store,
                    'unit' => $request->unit,
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


    public function loadCartItem(Request $request)
    {
        $type = $request->type;
        return view('components.cart', compact('type'));
    }

    public function addCartItem(Request $request)
    {
        $type = $request->type;
        if ($type == 'interstore') {
            $product = Product::find($request->product_id);
            $add = \Cart::add([
                'id' => $request->source_store_id.$request->product_id,
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
        if ($type == 'intersite') {
            $store_id = $request->store_id;
            $product_id = $request->product_id;
            $quantity = $request->quantity;
            $product = Product::find($request->product_id);
            $cost_price = BranchProductPrice::where(['product_id' => $request->product_id, 'branch_id' => auth()->user()->branch->id])->first();
            $store_product = StoreProduct::where(['store_id'=> $store_id,'product_id'=>$product_id])->first();
            if($store_product->qty_available < $quantity)
                $quantity = $store_product->qty_available;
            $items = \Cart::getContent();
            foreach($items as $item){
//                $store_id = $item->attributes['store_id'];
//                $product_id = $item->attributes['product_id'];
                $sp = StoreProduct::where(['store_id'=> $store_id,'product_id'=>$product_id])->first();
                if(($item->quantity + $quantity) > $sp->qty_available){
                    \Cart::remove($store_id.$product_id);
                }
            }
            $add = \Cart::add([
                'id' => $store_id.$product_id,
                'name' => $product->name,
                'price' => str_replace(',', '', $cost_price->cost_price) ?? 1,
                'quantity' => $quantity,
                'attributes' => array(
                    'store_id' => $store_id,
                    'product_id' => $product_id,
                    'code' => $product->code,
                ),
            ]);

//            return \Cart::getContent();

        }
        if ($type == 'grn') {
            $product = Product::find($request->product_id);
            $store = Store::find($request->store_id);
            $qty = $request->qty_supplied;
            $qty_available = $request->qty_available;
            $selling_price = str_replace(',', '', $request->seller_price);
            $unit_price = str_replace(',', '', $request->unit_price);
            $add = \Cart::add([
                'id' => $request->product_id,
                'name' => $product->name,
                'price' => $unit_price == 0 ? 1 : $unit_price,
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
        if ($type == 'request') {
            $product = Product::find($request->product_id);
            $store = Store::find($request->store_id);
            $qty = $request->qty_supplied;
            $qty_available = $request->qty_available;
            $unit_price = str_replace(',', '', $request->unit_price);
            $add = \Cart::add([
                'id' => $request->product_id,
                'name' => $product->name,
                'price' => $unit_price == 0 ? 1 : $unit_price,
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

        if ($type == 'adjustment') {

            $product = Product::find($request->product_id);
            // get the unit cost
            $query = BranchProductPrice::where(['branch_id' => User::userBranchAction(), 'product_id' => $request->product_id])->first();
            $cost_price = str_replace(',', '', $query->cost_price ?? 0);
            if ($request->cost_price != null) {
                $cost_price = $request->cost_price;
            }

            $add = \Cart::add([
                'id' => $request->store_id.$request->product_id,
                'name' => $product->name,
                'price' => $cost_price, //This is not applicable here
                'quantity' => abs($request->quantity),
                'attributes' => array(
                    'store_id' => $request->store_id,
                    'product_id' => $request->product_id,
                    'available_qty' => $request->available_qty,
                    'expiry_date' => $request->expiry_date ?? '',
                    'code' => $product->code,
                ),
            ]);
        }
        if ($type == 'order' || $type == 'proforma' || $type == 'invoice') {
            $customer = Customer::find($request->customer);
            //return $request;
            $cost_price = str_replace(',', '', $request->cost_price);
            if ($type == 'proforma' || $type == 'order') {
                $product_id = $request->product_id;
                $product = Product::find($product_id);
                $name = $product->name ?? '';
                $code = $product->code ?? '';
                $qty_available = 0;
                $id = $request->product_id;
                $unit = $product->unit;


            } else {
                $name = $request->name;
                $code = $request->code;
                $qty_available = $request->qty_available;
                $store_product = StoreProduct::find($request->id);
                $product_id = $store_product->product_id;
                $id = $request->id;
                $unit = $request->unit;

            }
            $prices = BranchProductPrice::where(['product_id' => $product_id, 'branch_id' => auth()->user()->branch->id])->first();
            $selling_price = $cost_price;// Price for Order and Profoma but change below if it is sales invoice
            if ($prices) {
                if ($customer->type == 'Retail')
                    $selling_price = str_replace(',', '', $prices->retail_selling_price);
                if ($customer->type == 'Wholesale')
                    $selling_price = str_replace(',', '', $prices->whole_selling_price);
                $cost_price = str_replace(',', '', $prices->cost_price);
            }
            $qty = $request->qty;

            $store = $request->store;
            $add = \Cart::add([
                'id' => $id,
                'name' => $name,
                'price' => $type != 'invoice' ? $cost_price : $selling_price,
                'quantity' => $qty == 0 ? 1 : $qty,
                'attributes' => array(
                    'cost_price' => $cost_price,
                    'code' => $code,
                    'selling_price' => $type != 'invoice' ? $cost_price : $selling_price,
                    'qty_available' => $qty_available,
                    'discount' => 0,
                    'store' => $store,
                    'unit' => $unit,
                ),
            ]);
        }

        if ($request->has('credit')) {
            $branch_id = $request->branch_id;
            $payer_id = $request->payer_id;
            $account_type = $request->account_type;
            $credit = $request->credit;
            $debit = $request->debit;
            $request->description;
            $branch = Branch::find($branch_id ?? auth()->user()->branch->id);
            $payer = null;
            $name = "";
            $code = "";
            if ($account_type == 'Customer') {
                $payer = Customer::find($payer_id);
                $name = $payer->name;
                $code = $payer->code;
            }
            if ($account_type == 'Supplier') {
                $payer = Supplier::find($payer_id);
                $name = $payer->name;
                $code = $payer->code;
            }
            if ($account_type == 'GeneralAccount') {
                $payer = GeneralAccount::find($payer_id);
                $name = $payer->description;
                $code = $payer->number;
            }

            $add = \Cart::add([
                'id' => $payer_id.$branch->id,
                'name' => $name,
                'price' => str_replace(',', '', $credit) ?? 1,
                'quantity' => 1,
                'attributes' => array(
                    'credit' => str_replace(',', '', $credit) ?? 1,
                    'debit' => str_replace(',', '', $debit) ?? 1,
                    'description' => $request->description,
                    'code' => $code,
                    'account_type' => $account_type,
                    'payer_id' => $payer_id,
                    'branch_id' => $branch->id,
                    'branch_code' => $branch->code,
                ),
            ]);
            $type = 'journal';
        }

        return view('components.cart', compact('type'));
    }

    public function updateCartItem(Request $request, $id)
    {
        $type = $request->type;
        if ($type == 'grn') {
            $sold_price = $request->sold_price;
            if ($request->has('percent')) {
                $percent = $request->percent;
                $cost_price = floatval(str_replace(',', '', $request->cost_price));
                $sold_price = ceil($cost_price + ($cost_price / 100) * $percent);
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
                        'code' => $request->code,
                        'discount' => $request->selling_price - $request->sold_price ?? '',
                        'qty_available' => $request->qty_available,
                        'store' => $request->store,
                        'unit' => $request->unit,
                        'store_id' => $request->store_id ?? '',
                        'store_code' => $store->code ?? '',
                    )
                ]
            );
        }
        if ($type == 'request') {
            $sold_price = $request->sold_price;
            if ($request->has('percent')) {
                $percent = $request->percent;
                $cost_price = floatval(str_replace(',', '', $request->cost_price));
                $sold_price = ceil($cost_price + ($cost_price / 100) * $percent);
            }
            $store = Store::find($request->store_id);
            \Cart::update(
                $request->id,
                [
                    'quantity' => [
                        'relative' => false,
                        'value' => $request->quantity
                    ],
                    'price' => str_replace(',', '', $request->price),
                    'attributes' => array(
                        'cost_price' => str_replace(',', '', $request->price),
                        'selling_price' => str_replace(',', '', $request->selling_price) ?? '',
                        'code' => $request->code,
                        'discount' => str_replace(',', '', $request->selling_price - $request->sold_price) ?? '',
                        'qty_available' => $request->qty_available,
                        'store' => $request->store,
                        'unit' => $request->unit,
                        'store_id' => $request->store_id ?? '',
                        'store_code' => $store->code ?? '',
                    )
                ]
            );
        }
        if ($type == 'adjustment') {
            $product = Product::find($request->product_id);
            \Cart::update(
                $request->id,
                [
                    'quantity' => [
                        'relative' => false,
                        'value' => $request->quantity
                    ],
                    'name' => $product->name,
                    'price' => 1, //Thos is not applicable here
                    'attributes' => array(
                        'store_id' => $request->store_id,
                        'product_id' => $request->product_id,
                        'available_qty' => $request->available_qty,
                        'code' => $product->code,
                        'expiry_date' => $request->expiry_date ?? '',
                    ),
                ]
            );
        }
        if ($type == 'intersite') {
            $product = Product::find($request->product_id);
            $store_id = $request->store_id;
            $product_id = $request->product_id;
            $quantity = $request->quantity;
            $store_product = StoreProduct::where(['store_id'=> $store_id,'product_id'=>$product_id])->first();
            if($store_product->qty_available < $quantity)
                $quantity = $store_product->qty_available;
            $items = \Cart::getContent();
            foreach($items as $item){
//                $store_id = $item->attributes['store_id'];
//                $product_id = $item->attributes['product_id'];
                $sp = StoreProduct::where(['store_id'=> $store_id,'product_id'=>$product_id])->first();
                if(($item->quantity + $quantity) > $sp->qty_available){
                    \Cart::remove($store_id.$product_id);
                }
            }
            \Cart::update(
                $request->id,
                [
                    'quantity' => [
                        'relative' => false,
                        'value' => $quantity
                    ],
                    'name' => $product->name,
                    'price' => str_replace(',', '', $request->cost_price), //This is not applicable here
                    'attributes' => array(
                        'store_id' => $store_id,
                        'product_id' => $product_id,
                        'code' => $request->code,
                    ),
                ]
            );
        }
        if ($type == 'order' || $type == 'proforma' || $type == 'invoice') {

            $unit = $request->unit;
            $store_product_id = $request->id;
            $selling_price = str_replace(',', '', $request->selling_price);
            $cost_price = str_replace(',', '', $request->cost_price);
            $sold_price = str_replace(',', '', $request->sold_price);
            $store_product = StoreProduct::find($store_product_id);
            $product_id = $store_product->product_id;
            $store_id = $store_product->store_id;
            $unit_measure = ProductUnitMeasure::where(['product_id' => $product_id, 'code' => $unit])->first();
            if ($unit_measure && $unit_measure->value > 1) {
                if ($unit_measure->type == 'division') {
                    $sold_price = roundDown(($selling_price / ($unit_measure->value ?? 1)), 50);
                    $cost_price = ($cost_price / ($unit_measure->value ?? 1));
                }
                if ($unit_measure->type == 'multiple') {
                    $sold_price = roundDown(($selling_price * ($unit_measure->value ?? 1)), 50);
                    $cost_price = ($cost_price * ($unit_measure->value ?? 1));
                }
            }

            // else {
            //     return $sold_price = $selling_price;
            // }
            $discount = ($selling_price > 0 ? $selling_price : 0) - ($sold_price > 0 ? $sold_price : 0);

            \Cart::update(
                $request->id,
                [
                    'quantity' => [
                        'relative' => false,
                        'value' => $request->quantity ?? 1
                    ],
                    'price' => $sold_price,
                    'attributes' => array(
                        'cost_price' => $cost_price,
                        'selling_price' => $selling_price,
                        'discount' => $discount > 0 ? $discount : 0,
                        'qty_available' => $request->qty_available,
                        'code' => $request->code,
                        'store' => $request->store,
                        'unit' => $request->unit,
                    )
                ]
            );
        }
        if ($request->type == 'journal') {
            $branch_id = $request->branch_id;
            $payer_id = $request->payer_id;
            $account_type = $request->account_type;
            $credit = $request->credit;
            $debit = $request->debit;
            $request->description;

            $branch = Branch::find($branch_id ?? auth()->user()->branch->id);
            $payer = null;
            $name = "";
            $code = "";
            if ($account_type == 'Customer') {
                $payer = Customer::find($payer_id);
                $name = $payer->name;
                $code = $payer->code;
            }
            if ($account_type == 'Supplier') {
                $payer = Supplier::find($payer_id);
                $name = $payer->name;
                $code = $payer->code;
            }
            if ($account_type == 'GeneralAccount') {
                $payer = GeneralAccount::find($payer_id);
                $name = $payer->description;
                $code = $payer->number;
            }
            $add = \Cart::update(
                $request->id,
                [
                    'name' => $name,
                    'price' => str_replace(',', '', $credit) ?? 1,
                    'quantity' => 1,
                    'attributes' => array(
                        'credit' => str_replace(',', '', $credit) ?? 1,
                        'debit' => str_replace(',', '', $debit) ?? 1,
                        'description' => $request->description,
                        'code' => $code,
                        'account_type' => $account_type,
                        'payer_id' => $payer_id,
                        'branch_id' => $branch->id,
                        'branch_code' => $branch->code,
                    ),
                ]
            );
            $type = 'journal';
        }

        if ($request->type == "returndebit") {

            $cost_price = $request->unit_price;
            $quantity = $request->quantity;
            \Cart::update(
                $request->id,
                [
                    'quantity' => [
                        'relative' => false,
                        'value' => $request->quantity
                    ],
                    'price' => str_replace(',', '', $cost_price),
                    'attributes' => array(
                        'cost_price' => $request->unit_price,
                        'product_id' => $request->product_id,
                        'store_id' => $request->store_id,
                        'code' => $request->code,
                        'store' => $request->store
                    ),
                ]
            );
            $type = 'returndebit';
        }


        if ($request->ajax()) {
            return \Cart::getTotal();
        }
        $type = $request->type;
        return view('components.cart', compact('type'));
    }

    public function deleteCartItem(Request $request, $id)
    {
        \Cart::remove($id);
        $type = $request->type;
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
