<?php

namespace App\Http\Controllers;

use App\Classes\CostPrice;
use App\Models\InterstoreTransfer;
use App\Models\InterstoreTransferDetail;
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

class InterStoreTransferController extends Controller
{

    public function index(Index $request)
    {
        \Cart::clear();
        $user = auth()->user();
        $branch = $user->branch;
        $records = InterstoreTransfer::where(['branch_id'=>$branch->id])->orderBy('reference', 'desc')->get();
        return view('pages.inventories.transfers.inter_store.index', ['records' => $records]);
    }

    public function search(Index $request)
    {
        $reference = $request->refno;
        $records = InterstoreTransfer::where('reference', 'LIKE', "%$reference%")
            ->orderBy('created_at', 'DESC')->get();
        return view('pages.inventories.transfers.inter_store.index', ['records' => $records]);
    }

    public function show(Show $request, TransferProduct $transferproduct)
    {
        return view('pages.inventories.transfers.inter_store.show', [
            'record' => $transferproduct,
        ]);

    }

    public function create(Create $request)
    {
        $products = Product::select('products.id', 'products.name', 'products.code')
            ->join('store_products', 'store_products.product_id', 'products.id')
            ->where('qty_available', '>', 0)->get();
        $categories = Category::all(['id', 'name']);
        $stores = Store::where('branch_id', 'LIKE', User::userBranchAction())->get();
        $cartItems = \Cart::session('_token')->getContent(); //\Cart::getContent();

        return view('pages.inventories.transfers.inter_store.create', [
            'model' => new TransferProduct,
            'products' => $products,
            'categories' => $categories,
            'stores' => $stores,
            'transfer_products' => $cartItems,
        ]);
    }

    public function store(StoreRequest $request)
    {
        $user = auth()->user();
        $branch = $user->branch;
        try {
            $interstore = new InterstoreTransfer();
            $interstore->date = $request->date;
            $interstore->branch_id = $branch->id;
            $interstore->reference = InterstoreTransfer::generateNewNumber();
            $interstore->created_by = $user->id;
            DB::beginTransaction();
            if($interstore->save()) {
                $items = \Cart::getContent();
                if (count($items) > 0) {
                    $transfer_id = $this->getNextTransferID();
                    $new_cost_price = [];
                    foreach ($items as $item) {
                        $new_cost_price[$item->attributes['product_id']] = [
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'source_store_id' => $item->attributes['source_store_id'],
                            'destination_store_id' => $item->attributes['destination_store_id'],
                            'expiry_date' => $item->attributes['expiry_date'] ?? '',
                        ];
                        $detail = new InterstoreTransferDetail();
                        $detail->interstore_transfer_id = $interstore->id;
                        $detail->product_id = $item->attributes['product_id'];
                        $detail->source_store_id = $item->attributes['source_store_id'];
                        $detail->destination_store_id = $item->attributes['destination_store_id'];
                        $detail->quantity = $item->quantity;
                        $detail->expiry_date = $item->attributes['expiry_date'] ?? '';
                        $detail->save();
                    }
                    if (
                        CostPrice::interstore(
                            $new_cost_price,
                            $interstore->reference,
                            $interstore->branch_id,
                            $interstore->date)['status']
                    ) {
                        $action = "Created interstore transfer with reference : " . $interstore->reference;
                        AuditLog::auditLog(Auth::id(), $action);
                        session()->flash('app_message', 'Transfer created successfully');
                        DB::commit();
                    }
                }
            }

        } catch (\Exception $ex) {
            DB::rollBack();
            session()->flash('app_error', 'Something is wrong while creating interstore transfer');
            throw $ex;
        }
        \Cart::clear();
        return redirect()->route('interstore.index');
    }

    public function edit(Edit $request, TransferProduct $transferproduct)
    {
        //\Cart::session('_token')->clear();
        $products = Product::all(['id', 'name']);
        $categories = Category::all(['id', 'name']);
        $stores = Store::where('branch_id', 'LIKE', User::userBranchAction())->get();
        $this->loadToCart($transferproduct);
        $cartItems = \Cart::session('_token')->getContent();
        return view('pages.inventories.transfers.inter_store.edit', [
            'model' => $transferproduct,
            'products' => $products,
            'categories' => $categories,
            'stores' => $stores,
            'transfer_products' => $cartItems,
        ]);
    }

    public function print(InterstoreTransfer $interstoreTransfer)
    {
        return view('pages.inventories.transfers.inter_store.print', compact('interstoreTransfer'));
    }
}
