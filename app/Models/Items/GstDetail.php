<?php

namespace App\Models\Items;

use App\Models\Accounts\Account;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class GstDetail extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "gst_details";
    protected $connection = 'mysql';
    protected $fillable = [
        'gst_id', 'gst_type_id', 'name', 'rate', 'rate_on', 'acid_input', 'acid_output'
    ];

    public function account_input()
    {
        return $this->belongsTo(Account::class, 'acid_input', 'id');
    }

    public function account_output()
    {
        return $this->belongsTo(Account::class, 'acid_output', 'id');
    }
}
