<?php

namespace App\Models\Masters;

use App\Models\Accounts\Account;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;

class Packing extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "packings";
    protected $connection = 'mysql';
    protected $guarded = ['form_id'];

}


