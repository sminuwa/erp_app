<?php

namespace App\Http\Controllers;

use App\Classes\CostPrice;
use App\Classes\Transaction;
use App\Models\Branch;
use App\Models\IntersiteTransfer;
use App\Models\IntersiteTransferProduct;
use App\Models\IntersiteTransferReceive;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TransferProduct;
use App\Http\Requests\TransferProducts\Index;
use App\Http\Requests\TransferProducts\Show;
use App\Http\Requests\TransferProducts\Create;
use App\Http\Requests\TransferProducts\StoreRequest;
use App\Http\Requests\TransferProducts\Edit;
use App\Http\Requests\TransferProducts\Update;
use App\Http\Requests\TransferProducts\Destroy;
use App\Models\Product;
use App\Models\Category;
use App\Models\Store;
use Illuminate\Support\Facades\DB;
use App\Models\StoreProduct;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;
use App\Models\User;

/**
 * Description of InterStoreTransferController
 *
 * @author Tuhin Bepari <digitaldreams40@gmail.com>
 */

class InterSiteTransferController extends Controller
{

    public function index(Index $request)
    {
        \Cart::clear();
        $posted = IntersiteTransfer::where('destination_branch_id', User::userBranchAction())
            ->posted()->count();
        $records = IntersiteTransfer::where('source_branch_id', User::userBranchAction())
            ->orderBy('id', 'desc')
            ->get();
        return view('pages.inventories.transfers.inter_site.index', compact('records', 'posted'));
    }

    public function received(Request $request)
    {
        $this->authorize('intersite.received');
        \Cart::clear();
        $pending = IntersiteTransfer::where('source_branch_id', User::userBranchAction())
            ->pending()->count();
        $records = IntersiteTransfer::where('destination_branch_id', User::userBranchAction())
            ->notPending()
            ->orderBy('id', 'desc')
            ->get();
        return view('pages.inventories.transfers.inter_site.received', compact('records', 'pending'));
    }

    public function post(IntersiteTransfer $intersite)
    {
        if ($intersite->status == 0) {
            DB::beginTransaction();
            $this->authorize('intersite.post');
            $user = auth()->user();
            $intersite->status = 1;
            $intersite->posted_by = $user->id;

            if ($intersite->save()) {
                $items = $intersite->products;
                /*return $items;*/
                foreach ($items as $item) {
                    StoreProduct::where(['store_id' => $item->store_id, 'product_id' => $item->product_id])->decrement('qty_available', $item->quantity);
                }
                if (Transaction::intersite_post($intersite->id)['status']) {
                    $action = "Posted intersite transfer of product from " . $user->branch->name . " to  branch" . Branch::find($intersite->destination_branch_id)->name;
                    AuditLog::auditLog(Auth::id(), $action);
                    session()->flash('app_message', 'Intersite transfer posted successfully');
                    DB::commit();
                } else {
                    DB::rollBack();
                }
            }
        }
        return back();
    }

    public function receive(IntersiteTransfer $intersite)
    {
        $this->authorize('intersite.receive');
        $user = auth()->user();
        $intersite->status = 2;
        $intersite->received_by = $user->id;
        DB::beginTransaction();
        if ($intersite->save()) {
            if (Transaction::intersite_receive($intersite->id)['status']) {
                $action = "Received intersite transfer of product from " . $user->branch->name . " to  branch" . Branch::find($intersite->destination_branch_id)->name;
                AuditLog::auditLog(Auth::id(), $action);
                session()->flash('app_message', 'Intersite transfer saved successfully');
                DB::commit();
            } else {
                DB::rollBack();
            }
        }
        return back();
    }
    public function search(Index $request)
    {
        $refno = $request->refno;
        $records = TransferProduct::where(['nature' => 'Transfer', 'stock_in_out' => 'out', 'transfer_products.status' => 1])
            ->where('stores.branch_id', 'LIKE', User::userBranchAction())
            ->where('transfer_products.type', 'intersite')
            ->where('reference', 'LIKE', "%$refno%")
            ->join('stores', 'stores.id', 'transfer_products.source_store_id')
            ->groupBy(['refno', 'source_store_id', 'product_id'])->orderBy('transfer_products.created_at', 'DESC')->get();
        return view('pages.inventories.transfers.inter_site.index', ['records' => $records]);
    }

    public function show(Show $request, IntersiteTransfer $intersiteTransfer)
    {
        return view('pages.inventories.transfers.inter_site.show', [
            'record' => $intersiteTransfer,
            'stores' => Store::where('branch_id', auth()->user()->branch->id)->orderBy('code', 'asc')->get(),
        ]);

    }

    public function addToStore(Request $request)
    {
        try {
            $user = auth()->user();
            $intersite_transfer_id = $request->intersite_transfer_id;
            $store_id = $request->store_id;
            $intersite = IntersiteTransfer::find($intersite_transfer_id);
            $products = $intersite->products;
            DB::beginTransaction();
            foreach ($products as $product) {
                //            $record = IntersiteTransferProduct::where(['intersite_transfer_id' => $intersite_transfer_id, 'store_id' => $product->store_id, 'product_id' => $product->product_id])->first();
                $new_cost_price = [];
                $receive = IntersiteTransferReceive::where(
                    [
                        'intersite_transfer_id' => $intersite_transfer_id,
                        'store_id' => $store_id,
                        'product_id' => $product->product_id,
                        'quantity' => $product->quantity
                    ]
                )->first();
                if (!$receive)
                    $receive = new IntersiteTransferReceive();
                $receive->intersite_transfer_id = $intersite->id;
                $receive->source_store_id = $product->store_id;
                $receive->product_id = $product->product_id;
                $receive->store_id = $store_id;
                $receive->quantity = $product->quantity;
                $receive->cost_price = $product->cost_price;
                $receive->status = 1;
                $receive->save();
                $new_cost_price[$product->product_id] = [
                    'quantity' => $product->quantity,
                    'price' => $product->cost_price,
                    'store_id' => $store_id,
                    'expiry_date' => '',
                ];

                /*return CostPrice::newCostPrice(
                    $new_cost_price,
                    $intersite->reference,
                    $user->branch->id,
                    $intersite->date,
                    TRANSACTION_TYPE_INTERSITE
                );*/
                CostPrice::newCostPrice(
                    $new_cost_price,
                    $intersite->reference,
                    $user->branch->id,
                    $intersite->date,
                    TRANSACTION_TYPE_INTERSITE
                );
            }
            $action = "Added products to store from intersite with reference : " . $intersite->reference;
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
            session()->flash('app_message', 'Product added to store successfully.');
            return back();
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('app_error', 'Allocation failed. ' . $e->getMessage());
            return back();
        }
        /*if (
            CostPrice::newCostPrice(
                $new_cost_price,
                $intersite->reference,
                $user->branch->id,
                $intersite->date,
                TRANSACTION_TYPE_INTERSITE
            )['status']
        ) {
            $action = "Added products to store from intersite with reference : " . $intersite->reference;
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
        } else
            DB::rollback();
        session()->flash('app_message', 'Product added to store successfully.');
        return back();*/
    }

    public function create(Create $request)
    {
        //\Cart::session('_token')->clear();

        $products = Product::select('products.id', 'products.name', 'products.code', 'qty_available')
            ->join('store_products', 'store_products.product_id', 'products.id')
            ->join('stores', 'stores.id', 'store_products.store_id')
            ->join('branches', 'branches.id', 'stores.branch_id')
            ->where('stores.branch_id', User::userBranchAction())
            ->where('products.status', 1)
            ->where('qty_available', '>', 0)->get();
        $categories = Category::all(['id', 'name']);
        $stores = Store::where('branch_id', 'LIKE', User::userBranchAction())->get();
        $cartItems = \Cart::getContent(); //\Cart::getContent();
        $branches = Branch::where('id', '<>', User::userBranchAction())->orderBy('name')->get();

        return view('pages.inventories.transfers.inter_site.create', [
            'model' => new TransferProduct,
            'products' => $products,
            'categories' => $categories,
            'stores' => $stores,
            'transfer_products' => $cartItems,
            'branches' => $branches,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('intersite.create');
        $user = auth()->user();
        $intersite_id = $request->intersite_id;
        $source_branch_id = $user->branch->id;
        $destination_branch_id = $request->transfer_branch_id;
        $vehicle_no = $request->vehicle_no;
        $description = $request->description ?? '';
        $date = $request->date ?? date('Y');
        $intersite = IntersiteTransfer::find($intersite_id);
        DB::beginTransaction();
        try {
            if (!$intersite) {
                $intersite = new IntersiteTransfer();
                $intersite->reference = IntersiteTransfer::generateNewNumber();
                $intersite->created_by = auth()->id();
                $intersite->status = 0;
            }
            $intersite->source_branch_id = $source_branch_id;
            $intersite->destination_branch_id = $destination_branch_id;
            $intersite->vehicle_no = $vehicle_no;
            $intersite->description = $description;
            $intersite->date = $date;
            if ($intersite->save()) {
                $items = \Cart::getContent();

                if (count($items) < 1) {
                    session()->flash('app_error', 'There is no product in the cart.');
                    return back()->withInput();
                }
                IntersiteTransferProduct::where('intersite_transfer_id', $intersite->id)->delete();
                foreach ($items as $item) {
                    $attribute = $item->attributes;
                    $store_id = $attribute['store_id'];
                    $product_id = $attribute['product_id'];
                    $product = IntersiteTransferProduct::where(['intersite_transfer_id' => $intersite->id, 'store_id' => $store_id, 'product_id' => $product_id])->first();
                    if (!$product)
                        $product = new IntersiteTransferProduct();
                    $product->intersite_transfer_id = $intersite->id;
                    $product->store_id = $store_id;
                    $product->product_id = $product_id;
                    $product->quantity = $item->quantity;
                    $product->cost_price = $item->price;
                    $product->save();
                }
                $action = "Made intersite transfer of product from " . $user->branch->name . " to  branch" . Branch::find($destination_branch_id)->name;
                AuditLog::auditLog(Auth::id(), $action);
                session()->flash('app_message', 'Intersite transfer saved successfully');
                DB::commit();
                \Cart::clear();
                return redirect()->route('intersite.show', $intersite->id);
            } else {
                DB::rollBack();
                session()->flash('app_message', 'Something went wrong.');
            }
        } catch (\Exception $ex) {
            DB::rollBack();
            session()->flash('app_message', 'Something went wrong. ' . $ex->getMessage());
            throw $ex;
        }
        return redirect()->back();
    }

    public function edit(Edit $request, IntersiteTransfer $intersite)
    {
        //\Cart::session('_token')->clear();
        $items = $intersite->products;
        $products = Product::where('status', 1)->get();
        $categories = Category::all(['id', 'name', 'code']);
        $stores = Store::where('branch_id', 'LIKE', User::userBranchAction())->get();
        /*if(!\Cart::isEmpty()){
            if(\Cart::getContent()->attributes['intersite_id'] == $intersite->id)
                \Cart::clear();
        }*/
        if (\Cart::isEmpty())
            $this->loadToCart($items);
        $cartItems = \Cart::getContent();

        $branches = Branch::where('id', '<>', User::userBranchAction())->orderBy('name')->get();
        return view('pages.inventories.transfers.inter_site.create', [
            'intersite' => $intersite,
            'model' => $intersite,
            'products' => $products,
            'categories' => $categories,
            'stores' => $stores,
            'transfer_products' => $cartItems,
            'branches' => $branches,
        ]);
    }


    public function delete(Destroy $request, IntersiteTransfer $intersite)
    {
        $this->authorize('intersite.delete');
        DB::beginTransaction();
        try {
            $reference_no = $intersite->reference;
            DB::table('intersite_transfer_products')->where('intersite_transfer_id', $intersite->id)->delete();
            DB::table('intersite_transfers')->where('id', $intersite->id)->delete();
            session()->flash('app_message', 'Stock transfer cancelled successfully');
            $action = "Deleted stock transfer request with reference no " . $reference_no;
            AuditLog::auditLog(Auth::id(), $action);
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            session()->flash('app_error', 'Something is wrong while cancelling stock transfer');
            throw $ex;
        }
        \Cart::clear();
        return redirect()->back();
    }
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required',
            'source_store_id' => 'required',
            'quantity_requested' => 'required',
        ]);
        $product = Product::find($request->product_id);
        $add = \Cart::add([
            'id' => generateRandomString(),
            'name' => $product->name,
            'price' => $request->cost_price ?? 1,
            'quantity' => $request->quantity_requested,
            'attributes' => array(
                'store_id' => $request->store_id,
                'product_id' => $request->product_id,
                'code' => $product->code,
            ),
        ]);

        if ($add) {
            session()->flash('app_message', 'Product is Added to Cart Successfully !');
            return redirect()->back()->withInput();

        } else {

            session()->flash('app_error', 'Product not added to cart');
            return redirect()->back()->withInput();
        }
    }
    public function removeCart(Request $request, $id)
    {
        \Cart::remove($request->id);
        session()->flash('app_message', 'Item Cart Remove Successfully !');

        return redirect()->back();
    }

    public function clearAllCart()
    {
        \Cart::clear();

        session()->flash('app_message', 'All Item Cart Clear Successfully !');

        return redirect()->back();
    }
    public function loadToCart($items)
    {
        foreach ($items as $data) {
            if ($data != null) {
                $product = Product::find($data->product_id);
                \Cart::add([
                    'id' => generateRandomString(),
                    'name' => $product->name,
                    'price' => $data->cost_price,
                    'quantity' => $data->quantity,
                    'attributes' => array(
                        'intersite_id' => $data->intersite->id ?? '',
                        'store_id' => $data->store_id ?? 1,
                        'product_id' => $data->product_id ?? 1,
                        'code' => $product->code ?? '',
                    ),
                ]);
            }
        }
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
    public function getNextTransferID()
    {
        $no = DB::table('transfer_products')->select(DB::raw('MAX(transfer_id) as max'))->where('nature', 'transfer')->first();
        return $no->max + 1;
    }
    public function generateRefNo()
    {
        $no = $this->getNextTransferID();
        return "TR" . date('y') . '' . date('m') . str_pad(($no), 4, "0", STR_PAD_LEFT);
    }
    public function printStockTransfer(IntersiteTransfer $intersite)
    {
        $this->authorize('intersite.print');

        return view('pages.inventories.transfers.inter_site.print')->with(['intersite' => $intersite]);
    }

}
