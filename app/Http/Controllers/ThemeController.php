<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\CustomThemePhoto;
use App\Support\FestiveTheme;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Admin only: choose the festive theme shown on every page and tablet,
 * and set up the Custom theme (photo, greeting, colour).
 */
class ThemeController extends Controller
{
    public function edit()
    {
        $setting = rescue(fn () => Setting::with('updatedBy:id,name')->find(FestiveTheme::SETTING), null, false);

        return view('theme.edit', [
            'themes' => FestiveTheme::all(),
            'currentKey' => FestiveTheme::currentKey(),
            'custom' => FestiveTheme::customSettings(),
            'setting' => $setting,
        ]);
    }

    public function update(Request $request, CustomThemePhoto $photos)
    {
        $validated = $request->validate([
            'theme' => ['required', 'string', Rule::in(FestiveTheme::keys())],
            'custom_greeting' => ['nullable', 'string', 'max:80', Rule::requiredIf($request->input('theme') === FestiveTheme::CUSTOM)],
            'custom_color' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'custom_photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_photo' => ['nullable', 'boolean'],
        ], [
            'theme.*' => 'Choose one of the themes shown.',
            'custom_greeting.required' => 'Enter the greeting text for the Custom theme.',
            'custom_greeting.max' => 'Keep the greeting to 80 characters or fewer.',
            'custom_color.*' => 'Choose a colour for the Custom theme.',
            'custom_photo.mimes' => 'The photo must be a JPG, PNG or WEBP picture.',
            'custom_photo.max' => 'The photo must be 2 MB or smaller.',
            'custom_photo.uploaded' => 'The photo must be 2 MB or smaller.',
            'custom_photo.*' => 'That photo could not be uploaded. Please try another picture.',
        ]);

        $this->saveCustom($request, $validated, $photos);

        FestiveTheme::save($validated['theme'], $request->user());

        $name = FestiveTheme::all()[$validated['theme']]['name'];

        return redirect()
            ->route('theme.edit')
            ->with('success', "{$name} theme is now showing on every page and tablet.");
    }

    /**
     * Public (tablets are not logged in). Only names in our own photo format
     * that exist on the private disk are served.
     */
    public function photo(string $file, CustomThemePhoto $photos)
    {
        $path = $photos->path($file);

        abort_unless($path, 404);

        return response()->file($path, [
            'Content-Type' => 'image/jpeg',
            // The name changes with every upload, so it can be cached for good.
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function saveCustom(Request $request, array $validated, CustomThemePhoto $photos): void
    {
        $custom = FestiveTheme::customSettings();
        $changed = false;

        foreach (['greeting' => 'custom_greeting', 'color' => 'custom_color'] as $key => $field) {
            $value = trim((string) ($validated[$field] ?? ''));

            if ($value !== '' && $value !== $custom[$key]) {
                $custom[$key] = $key === 'color' ? strtolower($value) : $value;
                $changed = true;
            }
        }

        $oldPhoto = $custom['photo'];

        if ($request->hasFile('custom_photo')) {
            $custom['photo'] = $photos->store($request->file('custom_photo'));
            $changed = true;
        } elseif (! empty($validated['remove_photo']) && $oldPhoto) {
            $custom['photo'] = null;
            $changed = true;
        }

        if (! $changed) {
            return;
        }

        FestiveTheme::saveCustom($custom, $request->user());

        if ($oldPhoto && $oldPhoto !== $custom['photo']) {
            $photos->delete($oldPhoto);
        }
    }
}
