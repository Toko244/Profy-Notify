<?php

namespace App\Http\Controllers;

use App\Http\Requests\SymlinkRequest;
use App\Models\Symlink;
use Illuminate\Support\Str;

class SymlinkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $symlinks = Symlink::latest()->paginate(20);

        return view('pages.symlinks.index', ['symlinks' => $symlinks]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.symlinks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SymlinkRequest $request)
    {
        $original = $request->link;
        $newLink = config('app.url').'/link/'.Str::random(20);
        while (Symlink::where('symlink', $newLink)->exists()) {
            $newLink = config('app.url').'/link/'.Str::random(12);
        }
        $symlink = Symlink::create([
            'title' => $title,
            'original' => $original,
            'symlink' => $newLink,
        ]);

        return redirect()->route('symlinks.edit', ['symlink' => $symlink]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Symlink $symlink)
    {
        return view('pages.symlinks.edit', ['symlink' => $symlink]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SymlinkRequest $request, Symlink $symlink)
    {
        $symlink->update([
            'title' => $title,
            'original' => $request->link,
        ]);

        return redirect()->route('symlinks.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Symlink $symlink)
    {
        $symlink->delete();

        return redirect()->route('symlinks.index');
    }

    /**
     * Redirect to the original link.
     */
    public function redirect()
    {
        $symlink = Symlink::where('symlink', request()->url())->first();
        if ($symlink) {
            $symlink->increment('usage_count');
            return redirect($symlink->original);
        }

        return redirect('https://www.profy.ge/ka');
    }
}
