<?php

namespace App\Http\Controllers\Assets;

use App\Helpers\StorageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\UploadFileRequest;
use App\Models\Actionlog;
use App\Models\Asset;
use \Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use \Illuminate\Contracts\View\View;
use \Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AssetFilesController extends Controller
{
    /**
     * Upload a file to the server.
     *
     * @param UploadFileRequest $request
     * @param int $assetId
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *@since [v1.0]
     * @author [A. Gianotto] [<snipe@snipe.net>]
     */
    public function store(UploadFileRequest $request, $assetId = null) : RedirectResponse
    {
        if (! $asset = Asset::find($assetId)) {
            return redirect()->route('hardware.index')->with('error', trans('admin/assets/message.does_not_exist'));
        }

        $this->authorize('update', $asset);

        if ($request->hasFile('file')) {
            if (! Storage::exists('private_uploads/assets')) {
                Storage::makeDirectory('private_uploads/assets', 775);
            }

            foreach ($request->file('file') as $file) {
                $file_name = $request->handleFile('private_uploads/assets/','hardware-'.$asset->id, $file);
                
                $asset->logUpload($file_name, $request->get('notes'));
            }

            return redirect()->back()->with('success', trans('admin/assets/message.upload.success'));
        }

        return redirect()->back()->with('error', trans('admin/assets/message.upload.nofiles'));
    }

    /**
     * Check for permissions and display the file.
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @param  int $assetId
     * @param  int $fileId
     * @since [v1.0]
     */
    public function show($assetId = null, $fileId = null) : View | RedirectResponse | Response | StreamedResponse | BinaryFileResponse
    {
        $asset = Asset::find($assetId);
        // the asset is valid
        if (isset($asset->id)) {
            $this->authorize('view', $asset);

            if (! $log = Actionlog::whereNotNull('filename')->where('item_id', $asset->id)->find($fileId)) {
                return response('No matching record for that asset/file', 500)
                    ->header('Content-Type', 'text/plain');
            }

            $file = 'private_uploads/assets/'.$log->filename;

            if ($log->action_type == 'patch') {
                $file = 'private_uploads/patches/'.$log->filename;
            }

            if (! Storage::exists($file)) {
                return response('File '.$file.' not found on server', 404)
                    ->header('Content-Type', 'text/plain');
            }

            if (request('inline') == 'true') {

                $headers = [
                    'Content-Disposition' => 'inline',
                ];

                return Storage::download($file, $log->filename, $headers);
            }

            return StorageHelper::downloader($file);
        }
        // Prepare the error message
        $error = trans('admin/assets/message.does_not_exist', ['id' => $fileId]);

        // Redirect to the hardware management page
        return redirect()->route('hardware.index')->with('error', $error);
    }

    /**
     * Delete the associated file
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @param  int $assetId
     * @param  int $fileId
     * @since [v1.0]
     */
    public function destroy($assetId = null, $fileId = null) : RedirectResponse
    {
        $asset = Asset::find($assetId);
        $this->authorize('update', $asset);
        $rel_path = 'private_uploads/assets';

        // the asset is valid
        if (isset($asset->id)) {
            $this->authorize('update', $asset);
            $log = Actionlog::find($fileId);
            if ($log) {
                if (Storage::exists($rel_path.'/'.$log->filename)) {
                    Storage::delete($rel_path.'/'.$log->filename);
                }
                $log->delete();

                return redirect()->back()->with('success', trans('admin/assets/message.deletefile.success'));
            }

            return redirect()->back()
                ->with('success', trans('admin/assets/message.deletefile.success'));
        }

        return redirect()->route('hardware.index')->with('error', trans('admin/assets/message.does_not_exist'));
    }
}
