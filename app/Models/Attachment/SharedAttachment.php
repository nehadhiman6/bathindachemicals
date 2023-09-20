<?php

namespace App\Models\Attachment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;

class SharedAttachment extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = 'attachments';
    protected $fillable = [
        'file_name',
        'file_ext',
        'mime_type',
    ];
    protected $appends = ['pdf_url'];

    public function getPdfUrlAttribute()
    {
        $pdf_url = '';
        if (config('bcl.upload_disk') == 's3') {
            $comp_id = session()->get('company_id');
            $file_path = '/' . config('bcl.aws_folder_soft1') . "/shared-files/$comp_id/" . $this->id . '.' . $this->file_ext;
            $pdf_url = getFileUrl($file_path, $this);
        }
        return $pdf_url;
    }
}
