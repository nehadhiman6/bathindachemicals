<?php

namespace App\Models\Masters;

use App\Models\Items\Item;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;
class PackingFormula extends Model
{
    use HasFactory;
    use Traits\UserAutoUpdate;
    protected $table = "packing_formulas";
    protected $connection = 'mysql';
    protected $fillable = [
        'item_id',
        'party_cat_id',
        'wef_date',
        'packing_id',
        'date_applicable_on',
        'uid'
    ];

    public function  packing_formula_details(){
        return $this->hasMany(PackingFormulaDetail::class,'pack_formula_id');
    }
    public function setWefDateAttribute($date)
    {
        $this->attributes['wef_date'] = setDateAttribute($date);
    }

    public function getWefDateAttribute($date)
    {
        return getDateAttribute($date);
    }

    public function party_category(){
        return $this->belongsTo(PartyCategory::class,'party_cat_id');
    }

    public function item(){
        return $this->belongsTo(Item::class,'item_id');
    }

    public function packing(){
        return $this->belongsTo(Packing::class,'packing_id');
    }
}
