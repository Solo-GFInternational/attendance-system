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
        $validated = $this->validateSite($request);

        $site = Site::create($validated);

        return redirect()
            ->route('sites.index')
            ->with('success', 'Site added successfully. Tablet link: ' . route('attendance.site', $site->code));
    }

    /**
     * The code becomes the tablet link (/attendance/CODE), so it is saved in
     * capitals and limited to characters that are safe in a web address.
     */
    private function validateSite(Request $request, ?Site $site = null): array
    {
        if (is_string($request->input('code'))) {
            $request->merge(['code' => strtoupper(trim($request->input('code')))]);
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('sites', 'name')->ignore($site)],
            'code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Z0-9]+(-[A-Z0-9]+)*$/',
                Rule::notIn(Site::RESERVED_CODES),
                Rule::unique('sites', 'code')->ignore($site),
            ],
            'active' => 'required|boolean',
        ], [
            'code.regex' => 'The site code can only use letters, numbers and dashes (no spaces), because it becomes the tablet link.',
            'code.not_in' => 'That site code is used by the system. Please choose a different code.',
        ]);
    }

    public function edit(Site $site)
    {
        return view('sites.edit', compact('site'));
    }

    public function update(Request $request, Site $site)
    {
        $site->update($this->validateSite($request, $site));

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