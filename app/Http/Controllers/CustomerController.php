<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::when(request()->has('search'), function($query) {
            $query->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', '%'. request('search') . '%')
                ->orWhere('email', 'like', '%' . request('search') . '%')
                ->orWhere('phone', 'like', '%' . request('search') . '%');
            })
            ->latest()
            ->paginate(20);
        return view('pages.customers.index', [
            'customers' => $customers
        ]);
    }
}
