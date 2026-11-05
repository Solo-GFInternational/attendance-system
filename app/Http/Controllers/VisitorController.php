<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VisitorController extends Controller
{
    public function index(Request $request)
{
    $siteId = $request->session()->get('attendance_site_id');

    $visitors = Visitor::where(function ($query) {

            $query->whereNull('sign_out')
                ->orWhereDate(
                    'sign_out',
                    Carbon::today()
                );

        })
        ->when($siteId, function ($query) use ($siteId) {

            $query->where('site_id', $siteId);

        })
        ->orderByDesc('sign_in')
        ->get();

    return view(
        'visitors.index',
        compact('visitors')
    );
}


    public function signIn(Request $request)
{
    $validated = $request->validate([

        'name' =>
            'required|string|max:255',

        'company' =>
            'nullable|string|max:255',

        'purpose' =>
            'required|string|max:255',

    ]);

    $siteId = $request->session()->get('attendance_site_id');

    Visitor::create([

        'visit_date' =>
            Carbon::today(),

        'name' =>
            $validated['name'],

        'company' =>
            $validated['company'] ?? null,

        'purpose' =>
            $validated['purpose'],

        'sign_in' =>
            now(),

        'site_id' =>
            $siteId,

    ]);

    return redirect()
        ->route('visitors.index')
        ->with(
            'success',
            'Thank you. You have been signed in at ' .
            now()->format('h:i A') .
            '.'
        );
}


    public function signOut(Visitor $visitor)
    {
        /*
         * Do not allow a visitor to be signed out twice.
         */

        if ($visitor->sign_out) {

            return redirect()

                ->route('visitors.index')

                ->with(
                    'error',

                    $visitor->name .
                    ' has already signed out.'
                );
        }


        /*
         * This works regardless of when the visitor
         * originally signed in.
         *
         * For example:
         *
         * Sign in:
         * 15 September 2026
         *
         * Sign out:
         * 21 September 2026
         *
         * The database will record the actual
         * sign-out date and time.
         */

        $visitor->update([

            'sign_out' =>
                now(),

        ]);


        return redirect()

            ->route('visitors.index')

            ->with(
                'success',

                $visitor->name .
                ' signed out at ' .
                now()->format('h:i A') .
                '.'
            );
    }
}