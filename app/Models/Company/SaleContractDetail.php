<?php

namespace App\Models\Company;

use App\Models\Items\Item;
use App\Models\Items\ItemUnit;
use App\Models\Yearly\SaleOrder;
use App\Models\Yearly\SaleOrderDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits;

class SaleContractDetail extends Model
{
    use Traits\UserAutoUpdate;
    protected $table = "sales_contract_dets";
    protected $connection = 'company_db';
    protected $fillable = [
        'sale_contract_id',
        'item_id',
        'qty',
        'unit_id',
        'rate',
        'bargain_price_diff',
        'packed_loose',
        'tolerance_per',
        'remarks',
        'close_qty'

    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
    public function item_unit()
    {
        return $this->belongsTo(ItemUnit::class, 'unit_id');
    }

}
