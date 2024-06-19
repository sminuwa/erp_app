<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customers\Index;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    //

    public function index(Request $request)
    {
        $search = $request->search ?? null;
        $user_branch = User::userBranchAction();
        $customers = Customer::
            where('branch_id', 'like', $request->branch_id ?? '%')
            ->where('type', 'like', $request->type ?? '%')
            ->where(function($query) use ($request) {
                return $query->where('name', 'like', '%'.$request->keyword.'%')
                    ->orWhere('code', 'like', '%'.$request->keyword.'%');
            })
            ->orderBy('created_at', 'desc')
            ->limit(25)
            ->with('branch')
            ->get();
        return view('pages.customers.ajax.list', compact('customers'));
    }
}
