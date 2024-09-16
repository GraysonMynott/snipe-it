<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Actionlog;
use App\Models\Asset;
use App\Models\AssetModel;
use App\Models\Category;
use App\Models\CustomField;
use App\Models\License;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use \Illuminate\Contracts\View\View;
use League\Csv\Reader;
use Symfony\Component\HttpFoundation\StreamedResponse;
use League\Csv\EscapeFormula;
use App\Http\Requests\CustomAssetReportRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;

/**
 * This controller handles all actions related to Reports for
 * the Snipe-IT Asset Management application.
 *
 * @version    v1.0
 */
class ReportsController extends Controller
{
    /**
     * Checks for correct permissions
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Exports the assets to CSV
     *
     * @deprecated Server-side exports have been replaced by datatables export since v2.
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v1.0]
     */
    public function exportAssetReport() : Response
    {
        $this->authorize('reports.view');
        // Grab all the assets
        $assets = Asset::with('model', 'assignedTo', 'assetstatus', 'defaultLoc', 'assetlog')
            ->orderBy('created_at', 'DESC')->get();

        $csv = \League\Csv\Writer::createFromFileObject(new \SplTempFileObject());
        $csv->setOutputBOM(Reader::BOM_UTF16_BE);

        $rows = [];

        // Create the header row
        $header = [
            trans('admin/assets/table.asset_tag'),
            trans('admin/assets/table.title'),
            trans('admin/assets/table.serial'),
            trans('admin/assets/table.location'),
        ];

        //we insert the CSV header
        $csv->insertOne($header);

        // Create a row per asset
        foreach ($assets as $asset) {
            $row = [];
            $row[] = e($asset->asset_tag);
            $row[] = e($asset->name);
            $row[] = e($asset->serial);

            if (($asset->assigned_to > 0) && ($location = $asset->location)) {
                if ($location->city) {
                    $row[] = e($location->city).', '.e($location->state);
                } elseif ($location->name) {
                    $row[] = e($location->name);
                } else {
                    $row[] = '';
                }
            } else {
                $row[] = '';  // Empty string if location is not set
            }

            $csv->insertOne($row);
        }

        $csv->output('asset-report-'.date('Y-m-d').'.csv');
        die;
    }

    /**
     * Displays patch report.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v4.0]
     */
    public function getPatchReport() : View
    {
        $this->authorize('reports.view');
        return view('reports/patch');
    }

    /**
     * Displays activity report.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v1.0]
     */
    public function getActivityReport() : View
    {
        $this->authorize('reports.view');

        return view('reports/activity');
    }

    /**
     * Exports the activity report to CSV
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v5.0.7]
     */
    public function postActivityReport(Request $request) : StreamedResponse
    {
        ini_set('max_execution_time', 12000);
        $this->authorize('reports.view');

        \Debugbar::disable();
        $response = new StreamedResponse(function () {
            Log::debug('Starting streamed response');

            // Open output stream
            $handle = fopen('php://output', 'w');
            stream_set_timeout($handle, 2000);

            $header = [
                trans('general.date'),
                trans('general.admin'),
                trans('general.action'),
                trans('general.type'),
                trans('general.item'),
                trans('general.license_serial'),
                trans('general.model_name'),
                trans('general.model_no'),
                'To',
                trans('general.notes'),
                trans('admin/settings/general.login_ip'),
                trans('admin/settings/general.login_user_agent'),
                trans('general.action_source'),
                'Changed',

            ];
            $executionTime = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
            Log::debug('Starting headers: '.$executionTime);
            fputcsv($handle, $header);
            $executionTime = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
            Log::debug('Added headers: '.$executionTime);

            $actionlogs = Actionlog::with('item', 'user', 'target', 'location')
                ->orderBy('created_at', 'DESC')
                ->chunk(20, function ($actionlogs) use ($handle) {
                    $executionTime = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
                Log::debug('Walking results: '.$executionTime);
                $count = 0;

                foreach ($actionlogs as $actionlog) {
                    $count++;
                    $target_name = '';

                    if ($actionlog->target) {
                            if ($actionlog->targetType() == 'user') {
                                $target_name = $actionlog->target->getFullNameAttribute();
                        } else {
                            $target_name = $actionlog->target->getDisplayNameAttribute();
                        }
                    }

                    if($actionlog->item){
                        $item_name = e($actionlog->item->getDisplayNameAttribute());
                    } else {
                        $item_name = '';
                    }

                    $row = [
                        $actionlog->created_at,
                        ($actionlog->admin) ? e($actionlog->admin->getFullNameAttribute()) : '',
                        $actionlog->present()->actionType(),
                        e($actionlog->itemType()),
                        ($actionlog->itemType() == 'user') ? $actionlog->filename : $item_name,
                        ($actionlog->item) ? $actionlog->item->serial : null,
                        (($actionlog->item) && ($actionlog->item->model)) ? htmlspecialchars($actionlog->item->model->name, ENT_NOQUOTES) : null,
                        (($actionlog->item) && ($actionlog->item->model))  ? $actionlog->item->model->model_number : null,
                        $target_name,
                        ($actionlog->note) ? e($actionlog->note) : '',
                        $actionlog->log_meta,
                        $actionlog->remote_ip,
                        $actionlog->user_agent,
                        $actionlog->action_source,
                    ];
                    fputcsv($handle, $row);
                }
            });

            // Close the output stream
            fclose($handle);
            $executionTime = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
            Log::debug('-- SCRIPT COMPLETED IN '.$executionTime);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="activity-report-'.date('Y-m-d-his').'.csv"',
        ]);


        return $response;
    }

    /**
     * Displays license report
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v1.0]
     */
    public function getLicenseReport() : View
    {
        $this->authorize('reports.view');
        $licenses = License::orderBy('created_at', 'DESC')
            ->with('company')
            ->get();

        return view('reports/licenses', compact('licenses'));
    }

    /**
     * Exports the licenses to CSV
     *
     * @deprecated Server-side exports have been replaced by datatables export since v2.
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v1.0]
     */
    public function exportLicenseReport() : Response
    {
        $this->authorize('reports.view');
        $licenses = License::orderBy('created_at', 'DESC')->get();

        $rows = [];
        $header = [
            trans('admin/licenses/table.title'),
            trans('admin/licenses/table.serial'),
            trans('admin/licenses/form.seats'),
            trans('admin/licenses/form.remaining_seats'),
            trans('admin/licenses/form.expiration'),
        ];

        $header = array_map('trim', $header);
        $rows[] = implode(', ', $header);

        // Row per license
        foreach ($licenses as $license) {
            $row = [];
            $row[] = e($license->name);
            $row[] = e($license->serial);
            $row[] = e($license->seats);
            $row[] = $license->remaincount();
            $row[] = $license->expiration_date;

            $rows[] = implode(',', $row);
        }


        $csv      = implode("\n", $rows);
        $response = response()->make($csv, 200);
        $response->header('Content-Type', 'text/csv');
        $response->header('Content-disposition', 'attachment;filename=report.csv');

        return $response;
    }

    /**
     * Returns a form that allows the user to generate a custom CSV report.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @see ReportsController::postCustomReport() method that generates the CSV
     * @since [v1.0]
     */
    public function getCustomReport() : View
    {
        $this->authorize('reports.view');
        $customfields = CustomField::get();

        return view('reports/custom')->with('customfields', $customfields);
    }

    /**
     * Exports the custom report to CSV
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @see ReportsController::getCustomReport() method that generates form view
     * @since [v1.0]
     */
    public function postCustom(CustomAssetReportRequest $request) : StreamedResponse
    {
        ini_set('max_execution_time', env('REPORT_TIME_LIMIT', 12000)); //12000 seconds = 200 minutes
        $this->authorize('reports.view');


        \Debugbar::disable();
        $customfields = CustomField::get();
        $response = new StreamedResponse(function () use ($customfields, $request) {
            Log::debug('Starting streamed response');
            Log::debug('CSV escaping is set to: '.config('app.escape_formulas'));

            // Open output stream
            $handle = fopen('php://output', 'w');
            stream_set_timeout($handle, 2000);
            
            if ($request->filled('use_bom')) {
                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            }

            $header = [];

            if ($request->filled('id')) {
                $header[] = trans('general.id');
            }

            if ($request->filled('company')) {
                $header[] = trans('general.company');
            }

            if ($request->filled('asset_name')) {
                $header[] = trans('admin/assets/form.name');
            }

            if ($request->filled('asset_tag')) {
                $header[] = trans('admin/assets/table.asset_tag');
            }

            if ($request->filled('model')) {
                $header[] = trans('admin/assets/form.model');
                $header[] = trans('general.model_no');
            }

            if ($request->filled('category')) {
                $header[] = trans('general.category');
            }

            if ($request->filled('manufacturer')) {
                $header[] = trans('admin/assets/form.manufacturer');
            }

            if ($request->filled('serial')) {
                $header[] = trans('admin/assets/table.serial');
            }

            if ($request->filled('eol')) {
                $header[] = trans('admin/assets/table.eol');
            }

            if ($request->filled('order')) {
                $header[] = trans('admin/assets/form.order');
            }

            if ($request->filled('location')) {
                $header[] = trans('admin/assets/table.location');
            }
            if ($request->filled('location_address')) {
                $header[] = trans('general.address');
                $header[] = trans('general.address');
                $header[] = trans('general.city');
                $header[] = trans('general.state');
                $header[] = trans('general.country');
                $header[] = trans('general.zip');
            }

            if ($request->filled('rtd_location')) {
                $header[] = trans('admin/assets/form.default_location');
            }
            
            if ($request->filled('rtd_location_address')) {
                $header[] = trans('general.address');
                $header[] = trans('general.address');
                $header[] = trans('general.city');
                $header[] = trans('general.state');
                $header[] = trans('general.country');
                $header[] = trans('general.zip');
            }

            if ($request->filled('username')) {
                $header[] = 'Username';
            }

            if ($request->filled('employee_num')) {
                $header[] = 'Employee No.';
            }

            if ($request->filled('manager')) {
                $header[] = trans('admin/users/table.manager');
            }

            if ($request->filled('title')) {
                $header[] = trans('admin/users/table.title');
            }

            if ($request->filled('phone')) {
                $header[] = trans('admin/users/table.phone');
            }

            if ($request->filled('user_address')) {
                $header[] = trans('admin/reports/general.custom_export.user_address');
            }

            if ($request->filled('user_city')) {
                $header[] = trans('admin/reports/general.custom_export.user_city');
            }

            if ($request->filled('user_state')) {
                $header[] = trans('admin/reports/general.custom_export.user_state');
            }

            if ($request->filled('user_country')) {
                $header[] = trans('admin/reports/general.custom_export.user_country');
            }

            if ($request->filled('user_zip')) {
                $header[] = trans('admin/reports/general.custom_export.user_zip');
            }

            if ($request->filled('status')) {
                $header[] = trans('general.status');
            }

            if ($request->filled('created_at')) {
                $header[] = trans('general.created_at');
            }

            if ($request->filled('updated_at')) {
                $header[] = trans('general.updated_at');
            }

            if ($request->filled('deleted_at')) {
                $header[] = trans('general.deleted');
            }

            if ($request->filled('last_patch_date')) {
                $header[] = trans('general.last_patch');
            }

            if ($request->filled('next_patch_date')) {
                $header[] = trans('general.next_patch_date');
            }

            if ($request->filled('notes')) {
                $header[] = trans('general.notes');
            }

            if ($request->filled('url')) {
                $header[] = trans('general.url');
            }


            foreach ($customfields as $customfield) {
                if ($request->input($customfield->db_column_name()) == '1') {
                    $header[] = $customfield->name;
                }
            }

            $executionTime = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
            Log::debug('Starting headers: '.$executionTime);
            fputcsv($handle, $header);
            $executionTime = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
            Log::debug('Added headers: '.$executionTime);

            $assets = Asset::select('assets.*')->with(
                'location', 'assetstatus', 'company', 'defaultLoc', 'assignedTo',
                'model.category', 'model.manufacturer');
            
            if ($request->filled('by_location_id')) {
                $assets->whereIn('assets.location_id', $request->input('by_location_id'));
            }

            if ($request->filled('by_rtd_location_id')) {
                $assets->whereIn('assets.rtd_location_id', $request->input('by_rtd_location_id'));
            }

            if ($request->filled('by_company_id')) {
                $assets->whereIn('assets.company_id', $request->input('by_company_id'));
            }

            if ($request->filled('by_model_id')) {
                $assets->whereIn('assets.model_id', $request->input('by_model_id'));
            }

            if ($request->filled('by_category_id')) {
                $assets->InCategory($request->input('by_category_id'));
            }

            if ($request->filled('by_manufacturer_id')) {
                $assets->ByManufacturer($request->input('by_manufacturer_id'));
            }

            if ($request->filled('by_status_id')) {
                $assets->whereIn('assets.status_id', $request->input('by_status_id'));
            }

            if (($request->filled('purchase_start')) && ($request->filled('purchase_end'))) {
                $assets->whereBetween('assets.purchase_date', [$request->input('purchase_start'), $request->input('purchase_end')]);
            }

            if (($request->filled('created_start')) && ($request->filled('created_end'))) {
                $created_start = Carbon::parse($request->input('created_start'))->startOfDay();
                $created_end = Carbon::parse($request->input('created_end'))->endOfDay();

                $assets->whereBetween('assets.created_at', [$created_start, $created_end]);
            }

            if (($request->filled('checkout_date_start')) && ($request->filled('checkout_date_end'))) {
                $checkout_start = Carbon::parse($request->input('checkout_date_start'))->startOfDay();
                $checkout_end = Carbon::parse($request->input('checkout_date_end',now()))->endOfDay();

                $actionlogassets = Actionlog::where('action_type','=', 'checkout')
                                              ->where('item_type', 'LIKE', '%Asset%',)
                                              ->whereBetween('action_date',[$checkout_start, $checkout_end])
                                                  ->pluck('item_id');

                $assets->whereIn('assets.id',$actionlogassets);
            }

            if (($request->filled('checkin_date_start'))) {
                $checkin_start = Carbon::parse($request->input('checkin_date_start'))->startOfDay();
                        // use today's date is `checkin_date_end` is not provided
                $checkin_end = Carbon::parse($request->input('checkin_date_end', now()))->endOfDay();

                $assets->whereBetween('assets.last_checkin', [$checkin_start, $checkin_end ]);
            }
            //last checkin is exporting, but currently is a date and not a datetime in the custom report ONLY.

            if (($request->filled('expected_checkin_start')) && ($request->filled('expected_checkin_end'))) {
                    $assets->whereBetween('assets.expected_checkin', [$request->input('expected_checkin_start'), $request->input('expected_checkin_end')]);
            }

            if (($request->filled('last_patch_start')) && ($request->filled('last_patch_end'))) {
                    $last_patch_start = Carbon::parse($request->input('last_patch_start'))->startOfDay();
                    $last_patch_end = Carbon::parse($request->input('last_patch_end'))->endOfDay();

                    $assets->whereBetween('assets.last_patch_date', [$last_patch_start, $last_patch_end]);
            }

            if (($request->filled('next_patch_start')) && ($request->filled('next_patch_end'))) {
                $assets->whereBetween('assets.next_patch_date', [$request->input('next_patch_start'), $request->input('next_patch_end')]);
            }
            if ($request->filled('exclude_archived')) {
                $assets->notArchived();
            }
            if ($request->input('deleted_assets') == 'include_deleted') {
                $assets->withTrashed();
            }
            if ($request->input('deleted_assets') == 'only_deleted') {
                $assets->onlyTrashed();
            }

            Log::debug($assets->toSql());
            $assets->orderBy('assets.id', 'ASC')->chunk(20, function ($assets) use ($handle, $customfields, $request) {
            
                $executionTime = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
                Log::debug('Walking results: '.$executionTime);
                $count = 0;

                $formatter = new EscapeFormula("`");

                foreach ($assets as $asset) {
                    $count++;
                    $row = [];

                    if ($request->filled('id')) {
                        $row[] = ($asset->id) ? $asset->id : '';
                    }

                    if ($request->filled('company')) {
                        $row[] = ($asset->company) ? $asset->company->name : '';
                    }

                    if ($request->filled('asset_name')) {
                        $row[] = ($asset->name) ? $asset->name : '';
                    }

                    if ($request->filled('asset_tag')) {
                        $row[] = ($asset->asset_tag) ? $asset->asset_tag : '';
                    }

                    if ($request->filled('model')) {
                        $row[] = ($asset->model) ? $asset->model->name : '';
                        $row[] = ($asset->model) ? $asset->model->model_number : '';
                    }

                    if ($request->filled('category')) {
                        $row[] = (($asset->model) && ($asset->model->category)) ? $asset->model->category->name : '';
                    }

                    if ($request->filled('manufacturer')) {
                        $row[] = ($asset->model && $asset->model->manufacturer) ? $asset->model->manufacturer->name : '';
                    }

                    if ($request->filled('serial')) {
                        $row[] = ($asset->serial) ? $asset->serial : '';
                    }

                    if ($request->filled('eol')) {
                            $row[] = ($asset->asset_eol_date) ? $asset->asset_eol_date : '';
                    }
                    
                    if ($request->filled('location')) {
                        $row[] = ($asset->location) ? $asset->location->present()->name() : '';
                    }

                    if ($request->filled('location_address')) {
                        $row[] = ($asset->location) ? $asset->location->address : '';
                        $row[] = ($asset->location) ? $asset->location->address2 : '';
                        $row[] = ($asset->location) ? $asset->location->city : '';
                        $row[] = ($asset->location) ? $asset->location->state : '';
                        $row[] = ($asset->location) ? $asset->location->country : '';
                        $row[] = ($asset->location) ? $asset->location->zip : '';
                    }

                    if ($request->filled('rtd_location')) {
                        $row[] = ($asset->defaultLoc) ? $asset->defaultLoc->present()->name() : '';
                    }

                    if ($request->filled('rtd_location_address')) {
                        $row[] = ($asset->defaultLoc) ? $asset->defaultLoc->address : '';
                        $row[] = ($asset->defaultLoc) ? $asset->defaultLoc->address2 : '';
                        $row[] = ($asset->defaultLoc) ? $asset->defaultLoc->city : '';
                        $row[] = ($asset->defaultLoc) ? $asset->defaultLoc->state : '';
                        $row[] = ($asset->defaultLoc) ? $asset->defaultLoc->country : '';
                        $row[] = ($asset->defaultLoc) ? $asset->defaultLoc->zip : '';
                    }

                    if ($request->filled('username')) {
                        $row[] = ''; // Empty string if unassigned
                    }

                    if ($request->filled('employee_num')) {
                        $row[] = ''; // Empty string if unassigned
                    }

                    if ($request->filled('manager')) {
                        $row[] = ''; // Empty string if unassigned
                    }

                    if ($request->filled('title')) {
                        $row[] = ''; // Empty string if unassigned
                    }

                    if ($request->filled('phone')) {
                        $row[] = ''; // Empty string if unassigned
                    }

                    if ($request->filled('user_address')) {
                        $row[] = ''; // Empty string if unassigned
                    }

                    if ($request->filled('user_city')) {
                        $row[] = ''; // Empty string if unassigned
                    }

                    if ($request->filled('user_state')) {
                        $row[] = ''; // Empty string if unassigned
                    }

                    if ($request->filled('user_country')) {
                        $row[] = ''; // Empty string if unassigned
                    }

                    if ($request->filled('user_zip')) {
                        $row[] = ''; // Empty string if unassigned
                    }

                    if ($request->filled('status')) {
                        $row[] = ($asset->assetstatus) ? $asset->assetstatus->name.' ('.$asset->present()->statusMeta.')' : '';
                    }

                    if ($request->filled('checkout_date')) {
                        $row[] = ($asset->last_checkout) ? $asset->last_checkout : '';
                    }

                    if ($request->filled('checkin_date')) {
                        $row[] = ($asset->last_checkin)
                            ? Carbon::parse($asset->last_checkin)->format('Y-m-d')
                            : '';
                    }

                    if ($request->filled('expected_checkin')) {
                        $row[] = ($asset->expected_checkin) ? $asset->expected_checkin : '';
                    }

                    if ($request->filled('created_at')) {
                        $row[] = ($asset->created_at) ? $asset->created_at : '';
                    }

                    if ($request->filled('updated_at')) {
                        $row[] = ($asset->updated_at) ? $asset->updated_at : '';
                    }

                    if ($request->filled('deleted_at')) {
                        $row[] = ($asset->deleted_at) ? $asset->deleted_at : '';
                    }

                    if ($request->filled('last_patch_date')) {
                        $row[] = ($asset->last_patch_date) ? $asset->last_patch_date : '';
                    }

                    if ($request->filled('next_patch_date')) {
                        $row[] = ($asset->next_patch_date) ? $asset->next_patch_date : '';
                    }

                    if ($request->filled('notes')) {
                        $row[] = ($asset->notes) ? $asset->notes : '';
                    }

                    if ($request->filled('url')) {
                        $row[] = config('app.url').'/hardware/'.$asset->id ;
                    }

                    foreach ($customfields as $customfield) {
                        $column_name = $customfield->db_column_name();
                        if ($request->filled($customfield->db_column_name())) {
                            $row[] = $asset->$column_name;
                        }
                    }

                    
                    // CSV_ESCAPE_FORMULAS is set to false in the .env
                    if (config('app.escape_formulas') === false) {
                        fputcsv($handle, $row);

                   // CSV_ESCAPE_FORMULAS is set to true or is not set in the .env
                    } else {
                        fputcsv($handle, $formatter->escapeRecord($row));
                    }

                    $executionTime = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
                    Log::debug('-- Record '.$count.' Asset ID:'.$asset->id.' in '.$executionTime);
                }
            });

            // Close the output stream
            fclose($handle);
            $executionTime = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
            Log::debug('-- SCRIPT COMPLETED IN '.$executionTime);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="custom-assets-report-'.date('Y-m-d-his').'.csv"',
        ]);

        return $response;
    }
}
