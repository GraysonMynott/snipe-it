<?php

namespace Database\Seeders;

use App\Models\AssetModel;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AssetModelSeeder extends Seeder
{
    public function run()
    {
        AssetModel::truncate();

        $admin = User::where('permissions->superuser', '1')->first() ?? User::factory()->firstAdmin()->create();

        // Firewalls
        AssetModel::factory()->count(1)->ckp1550Model()->create(['user_id' => $admin->id]);
        AssetModel::factory()->count(1)->ckp1555Model()->create(['user_id' => $admin->id]);
        AssetModel::factory()->count(1)->ckp1570Model()->create(['user_id' => $admin->id]);
        AssetModel::factory()->count(1)->ckp1590Model()->create(['user_id' => $admin->id]);
        AssetModel::factory()->count(1)->ckpCloudguardModel()->create(['user_id' => $admin->id]);


        $src = public_path('/img/demo/models/');
        $dst = 'models'.'/';
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
