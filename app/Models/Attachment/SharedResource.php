<?php

namespace App\Models\Attachment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class SharedResource extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = 'resources';
    protected $fillable = [
        'resourceable_type',
        'resourceable_id',
        'attachment_id',
        'doc_type',
        'doc_description',
        'year'
    ];

    public function attachment(){
        return $this->belongsTo(SharedAttachment::class,'attachment_id');
    }
}
