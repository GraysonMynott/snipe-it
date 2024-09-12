<?php

namespace App\Http\Middleware;

use App\Models\Asset;
use Closure;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
class AssetCountForSidebar
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /**
         * This needs to be set for the /setup process, since the tables might not exist yet
         */
        $total_assets = 0;
        $total_due_for_checkin = 0;
        $total_overdue_for_checkin = 0;
        $total_due_for_patch = 0;
        $total_overdue_for_patch = 0;

        try {
            $settings = Setting::getSettings();
            view()->share('settings', $settings);
        } catch (\Exception $e) {
            Log::debug($e);
        }

        try {
            $total_assets = Asset::count();
            if ($settings->show_archived_in_list != '1') {
                $total_assets -= Asset::Archived()->count();
            }
            view()->share('total_assets', $total_assets);
        } catch (\Exception $e) {
            Log::debug($e);
        }

        try {
            $total_rtd_sidebar = Asset::RTD()->count();
            view()->share('total_rtd_sidebar', $total_rtd_sidebar);
        } catch (\Exception $e) {
            Log::debug($e);
        }

        try {
            $total_deployed_sidebar = Asset::Deployed()->count();
            view()->share('total_deployed_sidebar', $total_deployed_sidebar);
        } catch (\Exception $e) {
            Log::debug($e);
        }

        try {
            $total_archived_sidebar = Asset::Archived()->count();
            view()->share('total_archived_sidebar', $total_archived_sidebar);
        } catch (\Exception $e) {
            Log::debug($e);
        }

        try {
            $total_pending_sidebar = Asset::Pending()->count();
            view()->share('total_pending_sidebar', $total_pending_sidebar);
        } catch (\Exception $e) {
            Log::debug($e);
        }

        try {
            $total_undeployable_sidebar = Asset::Undeployable()->count();
            view()->share('total_undeployable_sidebar', $total_undeployable_sidebar);
        } catch (\Exception $e) {
            Log::debug($e);
        }

        try {
            $total_due_for_patch = Asset::DueForPatch($settings)->count();
            view()->share('total_due_for_patch', $total_due_for_patch);
        } catch (\Exception $e) {
            Log::debug($e);
        }

        try {
            $total_overdue_for_patch = Asset::OverdueForPatch()->count();
            view()->share('total_overdue_for_patch', $total_overdue_for_patch);
        } catch (\Exception $e) {
            Log::debug($e);
        }

        try {
            $total_due_for_checkin = Asset::DueForCheckin($settings)->count();
            view()->share('total_due_for_checkin', $total_due_for_checkin);
        } catch (\Exception $e) {
            Log::debug($e);
        }

        try {
            $total_overdue_for_checkin = Asset::OverdueForCheckin()->count();
            view()->share('total_overdue_for_checkin', $total_overdue_for_checkin);
        } catch (\Exception $e) {
            Log::debug($e);
        }

        view()->share('total_due_and_overdue_for_checkin', ($total_due_for_checkin + $total_overdue_for_checkin));
        view()->share('total_due_and_overdue_for_patch', ($total_due_for_patch + $total_overdue_for_patch));

        return $next($request);
    }
}
