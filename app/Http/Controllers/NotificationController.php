<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Store;
use App\Models\Category;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;
use App\Models\User;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function notify()
    {
        $user_branch = User::userBranchAction();

        $products = Product::all(['id', 'name']);
        $stores = Store::select('id', 'name')->where('branch_id', 'LIKE', $user_branch)->get();
        $categories = Category::all(['id', 'name']);
        $branches = Branch::select('id', 'name')->where('id', 'LIKE', $user_branch)->get();
        $customers = Customer::where('type', 'Credit')->where('branch_id', 'LIKE', $user_branch)->orderBy('name')->get();
        return view('pages.notification', [
            'categories' => $categories,
            'products' => $products,
            'stores' => $stores,
            'branches' => $branches,
            'customers' => $customers,
        ]);
    }
    public function send(Request $request)
    {
        $text_message = $request->text;
        if ($request->has('phone')) {
            $phone = $request->phone;
            $msg = "Dear Customer, %0a $text_message";
            $url = "http://portal.nigeriabulksms.com/api/";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "$url");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,
                "username=engrabusadik@gmail.com&password=Aisha123&message=$msg&sender=".User::UserBranchName()->name."&mobiles=$phone");

            // Receive server response
            $server_output = curl_exec($ch);
            curl_close($ch);
        }
        else {
            if ($request->customer_id == "all") {
                $customers = Customer::where('type', 'Credit')->where('branch_id', 'LIKE', User::userBranchAction())->orderBy('name')->get();
                foreach ($customers as $customer) {
                    $customer_phone = $customer->phone;
                    $msg = "Dear $customer->name, %0a $text_message";
                    $url = "http://portal.nigeriabulksms.com/api/";
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "$url");
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS,
                        "username=engrabusadik@gmail.com&password=Aisha123&message=$msg&sender=".User::UserBranchName()->name."&mobiles=$customer_phone");

                    // Receive server response
                    $server_output = curl_exec($ch);
                    curl_close($ch);
                }
            }
            else {
                $customer = Customer::find($request->customer_id);
                $customer_phone = $customer->phoe;
                $msg = "Dear $customer->name, %0a $text_message";
                $url = "http://portal.nigeriabulksms.com/api/";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "$url");
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS,
                    "username=engrabusadik@gmail.com&password=Aisha123&message=$msg&sender=".User::UserBranchName()->name."&mobiles=$customer_phone");

                // Receive server response
                $server_output = curl_exec($ch);
                curl_close($ch);
            }
        }

        session()->flash('app_message', 'SMS successfully sent');
        return redirect()->back();
    }
}
