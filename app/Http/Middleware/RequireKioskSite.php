<?php

namespace App\Http\Middleware;

use App\Models\Employee;
use App\Models\Visitor;
use Closure;
use Illuminate\Http\Request;

/**
 * Tablet pages: a site must be set, and only that site's employees and
 * visitors can be seen or acted on. Someone logged in on a device without a
 * site (an office PC) may see all sites.
 */
class RequireKioskSite
{
    public function handle(Request $request, Closure $next)
    {
        $siteId = $request->session()->get('attendance_site_id');

        if (! $siteId) {
            return $request->user()
                ? $next($request)
                : $this->deny($request, 'attendance.index', 'This tablet is not set to a site. Please open your site link.');
        }

        foreach ($request->route()->parameters() as $model) {
            if (($model instanceof Employee || $model instanceof Visitor)
                && (int) $model->site_id !== (int) $siteId) {
                return $this->deny($request, 'attendance.index', 'That person is not registered at this site.');
            }
        }

        // A missing or unknown employee is left to the page's own validation.
        $employeeId = $request->input('employee_id');

        if (is_numeric($employeeId)) {
            $employee = Employee::find((int) $employeeId);

            if ($employee && (int) $employee->site_id !== (int) $siteId) {
                return $this->deny($request, 'attendance.employee', 'That person is not registered at this site.');
            }
        }

        return $next($request);
    }

    private function deny(Request $request, string $route, string $message)
    {
        if ($request->expectsJson() || $request->routeIs('attendance.status')) {
            return response()->json(['matched' => false, 'success' => false, 'message' => $message], 403);
        }

        return redirect()->route($route)->with('error', $message);
    }
}
