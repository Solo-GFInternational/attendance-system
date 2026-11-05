<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SiteController extends Controller
{
    public function index()
    {
        $sites = Site::withCount('employees')
            ->orderBy('name')
            ->get();

        return view('sites.index', compact('sites'));
    }

    public function create()
    {
        return view('sites.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sites,name',
            'code' => 'required|string|max:50|unique:sites,code',
            'active' => 'required|boolean',
        ]);

        Site::create($validated);

        return redirect()
            ->route('sites.index')
            ->with('success', 'Site added successfully.');
    }

    public function edit(Site $site)
    {
        return view('sites.edit', compact('site'));
    }

    public function update(Request $request, Site $site)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sites', 'name')->ignore($site->id),
            ],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('sites', 'code')->ignore($site->id),
            ],
            'active' => 'required|boolean',
        ]);

        $site->update($validated);

        return redirect()
            ->route('sites.index')
            ->with('success', 'Site updated successfully.');
    }

    public function toggleStatus(Site $site)
    {
        $site->active = !$site->active;
        $site->save();

        return redirect()
            ->route('sites.index')
            ->with(
                'success',
                'Site "' . $site->name . '" is now ' .
                ($site->active ? 'Active.' : 'Inactive.')
            );
    }
}