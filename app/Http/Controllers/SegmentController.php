<?php

namespace App\Http\Controllers;

use App\Imports\SegmentImport;
use App\Models\Customer;
use App\Models\Segment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class SegmentController extends Controller
{
    public function index()
    {
        $segments = Segment::when(request()->has('search'), function ($query) {
            $query->where('name', 'LIKE', '%' . request('search') . '%')
                ->orWhere('description', 'like', '%' . request('search') . '%');
        })
            ->latest()
            ->paginate(20);
        return view('pages.segments.index', [
            'segments' => $segments
        ]);
    }

    public function create()
    {
        return view('pages.segments.create');
    }

    public function store(Request $request)
    {
        $importData = Excel::toArray(new SegmentImport, request()->file('file'));
        $customerEmails = [];
        foreach ($importData[0] as $row) {
            $customerEmails[] = $row[0];
        }
        $segment = Segment::create($request->only('name', 'description'));
        Customer::whereIn('email', $customerEmails)->select('id')->get()->each(function ($customer) use ($segment) {
            DB::table('segment_customers')->insert([
                'segment_id' => $segment->id,
                'customer_id' => $customer->id
            ]);
        });


        return redirect()->route('segments.index')->with('success', 'Segment created');
    }

    public function delete(Segment $segment)
    {
        $segment->delete();
        return redirect()->route('segments.index')->with('success', 'Segment deleted');
    }
}
