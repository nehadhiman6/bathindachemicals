<?php

namespace App\Http\Controllers\Attachment;

use App\Http\Controllers\Controller;
use App\Models\Attachment\YearlyAttachment;
use App\Models\Masters\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class YearlyAttachmentController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, [
            'filepond' => 'required|file|max:2048'
        ]);
        $file = $request->file('filepond');
        $attachment = new YearlyAttachment();
        if ($file) {
            $attachment->file_ext = $file->getClientOriginalExtension();
            $attachment->file_name = $file->getClientOriginalName();
            $attachment->mime_type = $file->getMimeType();
            DB::beginTransaction();
            $attachment->save();
            if (config('bcl.upload_disk') == 's3') {
                $folderPath = config('bcl.aws_folder_soft1').'/' . yearlyAttachmentPath() . '/';
                $fileName =  $attachment->id . '.' . $attachment->file_ext;
                Storage::disk('s3')->putFileAs($folderPath, $file, $fileName);
            } else {
                $file->move(storage_path('app/' . yearlyAttachmentPath(). '/'), $attachment->id . '.' . $attachment->file_ext);
            }
            DB::commit();
            return reply(true, ['attachment' => $attachment]);
        }
    }

    public function show(Request $request, $id)
    {
        $attachment = YearlyAttachment::findOrFail($id);
        if ($attachment) {
            if (config('bcl.upload_disk') == 's3') {
                $file_path = '/' . config('bcl.aws_folder_soft1') . "/".yearlyAttachmentPath()."/" . $id . '.' . $attachment->file_ext;
            } else {
                $file_path = '/'.yearlyAttachmentPath().'/' . $attachment->id . '.' . $attachment->file_ext;
            }
            return showFile($file_path, $attachment);
        }
    }

    public function getAttachmentThumbnail(Request $request, $id)
    {

        $attachment = YearlyAttachment::findOrFail($id);
        if ($attachment) {
            if (config('bcl.upload_disk') == 's3') {
                $file_path = '/' . config('bcl.aws_folder_soft1') . "/".yearlyAttachmentPath()."/" . $id . '.' . $attachment->file_ext;
            } else {
                $file_path = '/'.yearlyAttachmentPath().'/' . $attachment->id . '.' . $attachment->file_ext;
            }

            return showFile($file_path, $attachment, true);
        }
    }

    // direct from file
    public function deleteAttachment(Request $request)
    {
        $file_id = $request->getContent();
        if ($file_id > 0) {
            DB::beginTransaction();
            $attachment =  YearlyAttachment::findOrFail($file_id);
            if ($attachment) {
                $image_path = storage_path('app/'.yearlyAttachmentPath().'/') . $attachment->id . '.' . $attachment->file_ext;
                // Value is not URL but directory file path
                if (File::exists($image_path)) {
                    File::delete($image_path);
                }
            }
            $attachment->delete();
            DB::commit();
            return reply(true, ['attachment' => $attachment]);
        }
    }
}
