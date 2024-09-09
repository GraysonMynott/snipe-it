<?php

namespace Database\Seeders;

use App\Models\Manufacturer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ManufacturerSeeder extends Seeder
{
    public function run()
    {
        Manufacturer::truncate();

        $admin = User::where('permissions->superuser', '1')->first() ?? User::factory()->firstAdmin()->create();

        Manufacturer::factory()->count(1)->apple()->create(['user_id' => $admin->id]);
        Manufacturer::factory()->count(1)->hp()->create(['user_id' => $admin->id]);
        Manufacturer::factory()->count(1)->checkpoint()->create(['user_id' => $admin->id]);
        Manufacturer::factory()->count(1)->aruba()->create(['user_id' => $admin->id]);
        Manufacturer::factory()->count(1)->cisco()->create(['user_id' => $admin->id]);
        Manufacturer::factory()->count(1)->citrix()->create(['user_id' => $admin->id]);

        $src = public_path('/img/demo/manufacturers/');
        $dst = 'manufacturers'.'/';
        $del_files = Storage::files($dst);

        foreach ($del_files as $del_file) { // iterate files
            $file_to_delete = str_replace($src, '', $del_file);
            Log::debug('Deleting: '.$file_to_delete);
            try {
                Storage::disk('public')->delete($dst.$del_file);
            } catch (\Exception $e) {
                Log::debug($e);
            }
        }

        $add_files = glob($src.'/*.*');
        foreach ($add_files as $add_file) {
            $file_to_copy = str_replace($src, '', $add_file);
            Log::debug('Copying: '.$file_to_copy);
            try {
                Storage::disk('public')->put($dst.$file_to_copy, file_get_contents($src.$file_to_copy));
            } catch (\Exception $e) {
                Log::debug($e);
            }
        }
    }
}
