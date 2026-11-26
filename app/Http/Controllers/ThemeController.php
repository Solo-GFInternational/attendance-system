<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Support\FestiveTheme;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Admin only: choose the festive theme shown on every page and tablet.
 */
class ThemeController extends Controller
{
    public function edit()
    {
        $setting = rescue(fn () => Setting::with('updatedBy:id,name')->find(FestiveTheme::SETTING), null, false);

        return view('theme.edit', [
            'themes' => FestiveTheme::all(),
            'currentKey' => FestiveTheme::currentKey(),
            'setting' => $setting,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'theme' => ['required', 'string', Rule::in(FestiveTheme::keys())],
        ], [
            'theme.*' => 'Choose one of the themes shown.',
        ]);

        FestiveTheme::save($validated['theme'], $request->user());

        $name = FestiveTheme::all()[$validated['theme']]['name'];

        return redirect()
            ->route('theme.edit')
            ->with('success', "{$name} theme is now showing on every page and tablet.");
    }
}
