<?php

namespace App\Console\Commands;

use App\Models\Asset;
use App\Models\Recipients\AlertRecipient;
use App\Models\Setting;
use App\Notifications\SendUpcomingPatchNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;

class SendUpcomingPatchReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'snipeit:upcoming-patches';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email/slack notifications for upcoming asset patches.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $settings = Setting::getSettings();
        $interval = $settings->patch_warning_days ?? 0;
        $today = Carbon::now();
        $interval_date = $today->copy()->addDays($interval);

        $assets = Asset::whereNull('deleted_at')->DueOrOverdueForPatch($settings)->orderBy('assets.next_patch_date', 'desc')->get();
        $this->info($assets->count().' assets must be patched in on or before '.$interval_date.' is deadline');


        if (($assets) && ($assets->count() > 0) && ($settings->alert_email != '')) {
            // Send a rollup to the admin, if settings dictate
            $recipients = collect(explode(',', $settings->alert_email))->map(function ($item) {
                return new AlertRecipient($item);
            });

            $this->info('Sending Admin SendUpcomingPatchNotification to: '.$settings->alert_email);
            \Notification::send($recipients, new SendUpcomingPatchNotification($assets, $settings->patch_warning_days));

        }

    }
}
