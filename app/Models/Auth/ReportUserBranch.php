<?php

namespace App\Models\Auth;

use App\Models\Masters\Branch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class ReportUserBranch extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "report_branch_users";
    protected $connection = 'mysql';
    protected $fillable = [
        'branch_id', 'user_id'
    ];
    public function branch()
    {
        return  $this->belongsTo(Branch::class,'branch_id');
    }
    public function user()
    {
        return  $this->belongsTo(Branch::class,'user_id');
    }
}
