<?php

// use App\Models\Auth\Role;

use App\Models\Masters\Company;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
function checkActive($path, $active = 'active')
{
    if (is_string($path)) {
        return request()->is($path) ? $active : '';
    }
    foreach ($path as $str) {
        if (checkActive($str) == $active) {
            return $active;
        }
    }
}

function checked($name, $value, $checked = 'checked')
{
    if (app('form')->getValueAttribute($name) == $value) {
        return $checked;
    }
    return '';
}

function getRandomNo($length = 32)
{
    return Str::random($length);
}

function reply($msg, $data = [], $status_code = 200, $headers = [])
{
    $data += ['app-status' => 'success', 'uid' => getRandomNo()];
    return response()
        ->json(['success' => $msg] + $data, $status_code, $headers);
}


function setDateAttribute($date)
{
    if (strlen($date) > 0) {
        return Carbon::createFromFormat('d-m-Y', $date);
    }
    return null;
}

function getDateAttribute($date)
{
    if ($date && $date != '0000-00-00' && $date != 'null') {
        if (is_object($date)) {
            if (get_class($date) == 'Carbon') {
                return $date->parse($date)->format('d-m-Y');
            } else {
                return $date;
            }
        }
        return Carbon::parse($date)->format('d-m-Y');
    }
    return '';
}
function getTimeAttribute($time)
{
    if ($time && $time != '0000-00-00' && $time != 'null') {
        if (is_object($time)) {
            if (get_class($time) == 'Carbon') {
                return $time->parse($time)->format('H:i');
            } else {
                return $time;
            }
        }
        return Carbon::parse($time)->format('H:i');
    }
    return '';
}

function getDateTimeAttribute($date)
{
    if ($date && $date != '0000-00-00' && $date != 'null') {
        if (is_object($date)) {
            if (get_class($date) == 'Carbon') {
                return $date->parse($date)->format('d-m-Y h:i:s');
            } else {
                return $date;
            }
        }
        return Carbon::parse($date)->format('d-m-Y h:i:s');
    }
    return '';
}

function setAmountAttribute($amount)
{
    if (strlen($amount) > 0) {
        return $amount;
    }
    return 0;
}

function setStringAttribute($string,$str=""){
    if (strlen($string) > 0) {
        return $string;
    }
    return $str;
}

function addString($str, $addstr, $gap = ",")
{
    if ($addstr == '') {
        return $str;
    } elseif ($str == '') {
        return $addstr;
    } else {
        return $str . $gap . $addstr;
    }
}


function getNowTime()
{
    return Carbon::now()->format('H:i');
}
function getToday($format = 'd-m-Y')
{
    return Carbon::today()->format($format);
}

function yesterday($format = 'd-m-Y')
{
    return Carbon::yesterday()->format($format);
}

function tomorrow($format = 'd-m-Y')
{
    return Carbon::tomorrow()->format($format);
}

function getDateAdd($dt1, $period, $interval = 'D',$format ='d-m-Y')
{
    $dt1 =  Carbon::createFromFormat($format, $dt1);
    if (strtoupper($interval) == 'D') {
        $dt1->addDays($period);
    } elseif (strtoupper($interval) == 'M') {
        $dt1->addMonths($period);
    } else {
        $dt1->addYears($period);
    }
    return $dt1->format($format);
}

function getDateObj($date, $format)
{
    if ($format == "dmy") {
        return Carbon::createFromFormat('d-m-Y', $date);
    } else {
        return Carbon::createFromFormat('Y-m-d', $date);
    }
}


function getDateSub($date, $dmy, $monthname = 'N')
{
    $dt = getDateObj($date, 'dmy');
    if (strtoupper($dmy) == 'D') {
        return $dt->day;
    } elseif (strtoupper($dmy) == 'M') {
        if (strtoupper($monthname) == 'Y') {
            return $dt->format('F');
        } else {
            return $dt->month;
        }
    } else {
        return $dt->year;
    }
}


function getDefaultConn()
{
    return 'mysql';
}


function getCompDbConn($set = false)
{
    if ($set) {
        $comp = Company::find(session()->get('company_id', 1));
        $db = config('database.connections.mysql_comp');
        $db['database'] = config('database.data_name') . $comp->id . '_' . 'company';
        // Log::info($db);
        config(['database.connections.company_db' => $db]);
    }
    return 'company_db';
}

function getYearlyDbConn($set = false)
{
    if ($set) {
        $db = config('database.connections.mysql_yr');
        $db['database'] = config('database.data_name') . session()->get('company_id', 1) . '_' . getFY();
        // Log::info($db);
        config(['database.connections.yearly_db' => $db]);
    }
    // dd('here came');
    return 'yearly_db';
}

function getPrvYearDbConn($set = false)
{
    $db = config('database.connections.mysql');
    $db['database'] = config('database.data_name') . getFY(true);
    config(['database.connections.prv_year_db' => $db]);
    return 'prv_year_db';
}

function getCompanyDb()
{
    $conn = getCompDbConn();
    return config("database.connections.$conn.database");
}

function getYearlyDb()
{
    $conn = getYearlyDbConn();
    return config("database.connections.$conn.database");
}

function getSharedDb()
{
    return config('database.data_name') . "shared";
    return '';
}

function getPrvYearDb()
{
    $conn = getPrvYearDbConn();
    return config("database.connections.$conn.database");
    return '';
}

// Normal Format or MySql Format
function getFYDate($startend = "S", $format = 'dmy')
{
    if ($format == 'dmy') {
        if ($startend == "S") {
            $date1 = "01-04-" . substr(getFY(), 0, 4);
        } else {
            $date1 = "31-03-" . substr(getFY(), 4, 4);
        }
    } else {
        if ($startend == "S") {
            $date1 = substr(getFY(), 0, 4) . "-04-01";
        } else {
            $date1 = substr(getFY(), 4, 4) . "-03-31";
        }
    }
    return $date1;
}

function getCurrentDate()
{
    if (getDateComp(getToday(), getFYDate('E')) == 'G') {
        return getFYDate('E');
    } elseif (getDateComp(getToday(), getFYDate('S')) == 'S') {
        return getFYDate('S');
    } else {
        return getToday();
    }
}


function getFY($prvYr = false)
{
    if ($prvYr) {
        $fy = session()->get('fy', '20222023');
        if ($fy != '') {
            $fy = (intval(substr($fy, -4)) - 2) . substr($fy, 0, 4);
        }
        return $fy;
    } else {
        return session()->get('fy', '20222023');
    }
}

function getFYStartDate()
{
    return '01-04-' . substr(getFY(), 0, 4);
}

function getFYEndDate()
{
    return '31-03-' . (substr(getFY(), 0, 4)+1);
}
function setNameAttribute($item_name)
{
    return trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $item_name)));
}

function getResultModel($ids, $modelToEff, $fillable)
{
    $id = array_splice($ids, 0, 1);
    if (count($id) > 0) {
        $id1 = $id[array_keys($id)[0]];
        $model = $modelToEff::findOrFail($id1);
    } else {
        $model = new $modelToEff();
    }
    $model->fill($fillable);
    // dd($model);
    return [$model, $ids];
}

function getResultModelFind($ids, $find_data, $modelToEff, $fillable)
{
    $model = $modelToEff::firstOrNew($find_data);
    if (intval($model->id) > 0) {
        unset($ids[$model->id]);
    }
    $model->fill($fillable);
    // dd($model);
    return [$model, $ids];
}


function getDateFormat($date, $formatreq)
{
    if ($formatreq == "ymd") {
        if (strlen($date) > 0) {
            return Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
        } else {
            return null;
        }
    } else {
        if (strlen($date) > 0) {
            return Carbon::createFromFormat('Y-m-d', $date)->format('d-m-Y');
        } else {
            return null;
        }
    }
}

function getFormattedDate($date, $curformat, $formatreq)
{
    if (strlen($date) > 0) {
        return Carbon::createFromFormat($curformat, $date)->format($formatreq);
    } else {
        return null;
    }
}


function getDates($type, $format)
{
    switch ($type) {
        case 'today': {
                $dt1 = getToday();
                $dt2 = $dt1;
                break;
            }
        case 'yesterday': {
                $dt1 = yesterday();
                $dt2 = $dt1;
                break;
            }
        case 'this_week': {
                $dt2 = getToday();
                $dt1 = getDateAdd($dt2, -6);
                break;
            }
        case 'last_7_days': {
                $dt2 = getToday();
                $dt1 = getDateAdd($dt2, -6);
                break;
            }
        case 'last_week': {
                $dt2 = getToday();
                $dt1 = getDateAdd($dt2, -13);
                $dt2 = getDateAdd($dt2, -7);
                break;
            }
        case 'this_month': {
                $dt2 = getToday();
                $dt1 = getDateAdd($dt2, -30);
                break;
            }
        case 'last_month': {
                $dt2 = getToday();
                $dt1 = getDateAdd($dt2, -31);
                $dt2 = getDateAdd($dt2, -60);
                break;
            }
        case 'this_year': {
                $dt2 = getToday();
                $dt1 = getDateAdd($dt2, -365);
                break;
            }
        case 'last_year': {
                $dt2 = getToday();
                $dt1 = getDateAdd($dt2, -366);
                $dt2 = getDateAdd($dt2, -730);
                break;
            }
    }
    return ['from_date' => getFormattedDate($dt1, 'd-m-Y', $format), 'to_date' => getFormattedDate($dt2, 'd-m-Y', $format)];
}
// it check $dt1 having format format1 is between $dt2 and $dt3 or not
function isBetween($dt1, $format1, $dt2, $dt3, $format)
{
    $dt = Carbon::createFromFormat($format1, $dt1)->setTime(0, 0, 0);
    $dtf = Carbon::createFromFormat($format, $dt2)->setTime(0, 0, 0);
    $dtt = Carbon::createFromFormat($format, $dt3)->setTime(0, 0, 0);
    return $dt->between($dtf, $dtt);
}

function deny($redirect = '')
{
    Session::flash('message', 'You dont have access to this resource!!');
    if (strlen($redirect) > 0) {
        return redirect($redirect);
    }
    //  abort(403);
    // Session::flash('message', 'You dont have access to this resource!!');
    // flash()->warning("You don't have access to this resource!!");
    return redirect('/');
}

// function getS3TempUrl($path, $minutes = 60)
// {
//     if (Storage::disk('s3')->exists($path)) {
//         return Storage::disk('s3')->temporaryUrl(
//             $path,
//             now()->addMinutes($minutes),
//             [
//                 'ResponseContentType' => 'application/octet-stream',
//                 'Access-Control-Allow-Origin' => '*'
//             ]
//         );
//     }
//     return $path;
// }

// function getFileResponse($path)
// {
//     $s3Disk = Storage::disk('s3');
//     // $media_name = Arr::last(explode("/", $path));
//     $stream = $s3Disk->readStream($path);

//     return response()->stream(function () use ($stream, $s3Disk) {
//         fpassthru($stream);
//     }, 200, [
//         "Content-Type" => $s3Disk->mimeType($path),
//         "Content-Length" => $s3Disk->size($path),
//         "Content-disposition" => "attachment; filename=\"" . basename($path) . "\"",
//     ]);
// }

function getYearlyDbConnFromDb($database)
{
    $db = config('database.connections.mysql_yr');
    $db['database'] = $database;
    $conn = 'yearly_db_' . $database;
    config(["database.connections.{$conn}" => $db]);
    return $conn;
}
function getCompanyDbConnFromDb($database)
{
    $db = config('database.connections.mysql_comp');
    $db['database'] = $database;
    $conn = 'company_db_' . $database;
    config(["database.connections.{$conn}" => $db]);
    return $conn;
}




function getDrCrRev($drcr)
{
    if (strtoupper($drcr) == 'D') {
        return 'C';
    } else {
        return 'D';
    }
}

function figToWordOman($number)
{
    $number1 = intval($number);
    $number2 = ($number - intval($number)) * 1000;
    $only = 'N';
    if ($number2 == 0) {
        $only = 'Y';
    }
    $word = trim(figToWord($number1, $only));
    if (strlen($word) > 0) {
        $word = 'Rial ' . $word;
    }
    if ($number2 != 0) {
        $word1 = trim(figToWord($number2, 'N'));
        if (strlen($word1) > 0) {
            $word1 .= ' Baizas Only';
        }
        if (strlen($word) > 0) {
            $word .= " and " . $word1;
        } else {
            $word = $word1;
        }
    }
    return $word;
}

function figToWord($number, $only = 'Y')
{
    $array = array('01' => 'One', '02' => 'Two', '03' => 'Three', '04' => 'Four', '05' => 'Five', '06' => 'Six', '07' => 'Seven', '08' => 'Eight', '09' => 'Nine', '10' => 'Ten', '11' => 'Eleven', '12' => 'Twelve', '13' => 'Thirteen', '14' => 'Fourteen', '15' => 'Fifteen', '16' => 'Sixteen', '17' => 'Seventeen', '18' => 'Eighteen', '19' => 'Nineteen', '20' => 'Twenty', '30' => 'Thirty', '40' => 'Fourty', '50' => 'Fifty', '60' => 'Sixty', '70' => 'Seventy', '80' => 'Eighty', '90' => 'Ninety', '00' => '');
    $fig1 = GetNumToString($number, 13, 2);
    $count = 13 - strlen(trim($fig1));
    $word2 = "";
    while ($count <= 11) {
        if ($count == '1' || $count == '3' || $count == '5' || $count == '8' || $count == '11') {
            $fig2 = trim(substr($fig1, $count - 1, 2));
            if (isset($array[$fig2]) && $array[$fig2] != '') {
                $word1 = trim($array[$fig2]);
            } else {
                $fig2 = trim(substr($fig1, $count - 1, 1));
                $fig2 = $fig2 . "0";
                if ($fig2 == '00' && $count != 8) {
                    $count = $count + 2;
                    continue;
                }
                $word1 = trim($array[$fig2]);
                $fig3 = trim(substr($fig1, ($count + 1 - 1), 1));
                $fig3 = '0' . $fig3;
                $word1 = trim($word1) . ' ' . trim($array[$fig3]);
            }
            $count = $count + 2;
        } else {
            $fig2 = trim(substr($fig1, $count - 1, 1));
            $fig2 = '0' . $fig2;
            $count = $count + 1;
            if (!isset($array[$fig2])) {
                $array[$fig2] = '';
            }
            $word1 = trim($array[$fig2]);
            if ($fig2 == '00') {
                continue;
            }
        }

        switch ($count) {
            case '3':
                $word2 = trim($word2) . ' ' . trim($word1) . ' ' . 'Crore';
                break;
            case '5':
                $word2 = trim($word2) . ' ' . trim($word1) . ' ' . 'Lac';
                break;
            case '7':
                $word2 = trim($word2) . ' ' . trim($word1) . ' ' . 'Thousand';
                break;
            case '8':
                $word2 = trim($word2) . ' ' . trim($word1) . ' ' . 'Hundred';
                break;
            case '10':
                $count = $count + 1;
                if (trim(substr($fig1, $count - 1, 2)) == '00') {
                    $word2 = trim($word2) . ' ' . trim($word1);
                    if ($only == 'Y') {
                        $word2 .=  ' ' . 'Only';
                    }
                    $count = $count + 1;
                } else {
                    $word2 = trim($word2) . ' ' . trim($word1) . ' ' . 'and';
                }
                break;
            case '13':
                $word2 = trim($word2) . ' ' . trim($word1) . ' ' . 'Paise Only';
                break;
        }
    }
    return $word2;
}

function GetNumToString($varNumeric, $length, $decimal = 0)
{
    $varNumeric = number_format(round($varNumeric, $decimal), $decimal, '.', '');
    $varNumeric = str_pad($varNumeric, $length - strlen(settype($varNumeric, 'string')), ' ', STR_PAD_LEFT);
    return $varNumeric;
}

function myRound($amount,$decimals=2) {
    return number_format($amount,$decimals, '.', '');
}


function canadianStates()
{
    return
        $gfg_array = array(
            array(
                'code' => 'AB',
                'text' => 'Alberta',
                'id' => 'Alberta',

            ),
            array(
                'code' => 'BC',
                'id' => 'British Columbia',       'text' => 'British Columbia',

            ),
            array(
                'code' => 'MB',
                'text' => 'Manitoba',
                'id' => 'Manitoba',

            ),
            array(
                'code' => 'NB',
                'id' => 'New Brunswick',      'text' => 'New Brunswick',

            ),
            array(
                'code' => 'NL',
                'text' => 'Newfoundland',
                'id' => 'Newfoundland',

            ),
            array(
                'code' => 'NT',
                'id' => 'Northwest Territories',      'text' => 'Northwest Territories',

            ),
            array(
                'code' => 'NS',
                'id' => 'Nova Scotia',        'text' => 'Nova Scotia',

            ),
            array(
                'code' => 'NU',
                'text' => 'Nunavut',
                'id' => 'Nunavut',

            ),
            array(
                'code' => 'ON',
                'text' => 'Ontario',
                'id' => 'Ontario',

            ),
            array(
                'code' => 'PE',
                'id' => "Prince Edward Island",
                'text' => "Prince Edward Island",

            ),
            array(
                'code' => 'QC',
                'text' => "Quebec",
                'id' => "Quebec",

            ),
            array(
                'code' => 'SK',
                'text' => "Saskatchewan",
                'id' => "Saskatchewan",

            ),
            array(
                'code' => 'YT',
                'text' => "Yukon",
                'id' => "Yukon",

            ),
        );
}

function lotNumbers()
{
    return
        $gfg_array = array(
            array('id' => '01', 'text' => '01 ALGOMA'),
            array('id' => '02', 'text' => '02 BRANT'),
            array('id' => '03', 'text' => '03 BRUCE'),
            array('id' => '04', 'text' => '04 OTTAWA-CARLETON'),
            array('id' => '06', 'text' => '06 COCHRANE'),
            array('id' => '07', 'text' => '07 DUFFERIN'),
            array('id' => '08', 'text' => '08 DUNDAS'),
            array('id' => '11', 'text' => '11 ELGIN'),
            array('id' => '12', 'text' => '12 ESSEX'),
            array('id' => '13', 'text' => '13 FRONTENAC'),
            array('id' => '14', 'text' => '14 GLENGARRY'),
            array('id' => '15', 'text' => '15 GLENVILLE'),
            array('id' => '16', 'text' => '16 GREY'),
            array('id' => '18', 'text' => '18 HALDIMAND'),
            array('id' => '19', 'text' => '19 HALIBURTON'),
            array('id' => '20', 'text' => '20 HALTON COUNTY'),
            array('id' => '21', 'text' => '21 HASTINGS'),
            array('id' => '22', 'text' => '22 HURON'),
            array('id' => '23', 'text' => '23 KENORA'),
            array('id' => '24', 'text' => '24 KENT COUNTY'),
            array('id' => '25', 'text' => '25 LAMBTON'),
            array('id' => '27', 'text' => '27 LANARK'),
            array('id' => '28', 'text' => '28 LEEDS'),
            array('id' => '29', 'text' => '29 LENNOX'),
            array('id' => '30', 'text' => '30 NIAGARA NORTH/ NIAGARA'),
            array('id' => '31', 'text' => '31 MANITOULIN'),
            array('id' => '33', 'text' => '33 MIDDLESEX COUNTY'),
            array('id' => '35', 'text' => '35 MUSKOKA'),
            array('id' => '36', 'text' => '36 NIPISSING'),
            array('id' => '37', 'text' => '37 NORFOLK'),
            array('id' => '39', 'text' => '39 NORTHUMBERLAND'),
            array('id' => '40', 'text' => '40 DURHAM'),
            array('id' => '41', 'text' => '41 OXFORD COUNTY'),
            array('id' => '42', 'text' => '42 PARRY SOUND'),
            array('id' => '43', 'text' => '43 PEEL COUNTY'),
            array('id' => '44', 'text' => '44 PERTH'),
            array('id' => '45', 'text' => '45 PETERBOROUGH'),
            array('id' => '46', 'text' => '46 PRESCOTT'),
            array('id' => '47', 'text' => '47 PRINCE EDWARD'),
            array('id' => '48', 'text' => '48 RAINY RIVER'),
            array('id' => '49', 'text' => '49 RENFREW'),
            array('id' => '50', 'text' => '50 RUSSELL'),
            array('id' => '51', 'text' => '51 SIMCOE'),
            array('id' => '52', 'text' => '52 STORMONT'),
            array('id' => '53', 'text' => '53 SUDBURY'),
            array('id' => '54', 'text' => '54 TIMISKAMING'),
            array('id' => '55', 'text' => '55 THUNDER BAY'),
            array('id' => '57', 'text' => '57 VICTORIA'),
            array('id' => '58', 'text' => '58 WATERLOO'),
            array('id' => '59', 'text' => '59 NIAGARA SOUTH/NIAGARA 30'),
            array('id' => '61', 'text' => '61 WELLINGTON'),
            array('id' => '62', 'text' => '62 HAMILTON WENTWORTH'),
            array('id' => '65', 'text' => '65 YORK REGION'),
            array('id' => '80', 'text' => '80 METRO TORONTO 66 & 64'),
        );
}


