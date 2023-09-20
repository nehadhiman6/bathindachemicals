<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MonthlyReport
 *
 * @author Ani
 */

namespace App\Printings;

use Illuminate\Support\Facades\Auth;

class VoucherPrint extends PrintPdf
{
    protected $testpdf = null;
    protected $pageno = 0;
    protected $nextpage = '';
    protected $secHeight = 0;
    protected $totlAmount = 0;
    protected $purchase = null;
    protected $hight = 0;
    protected $footerHeight = 0;
    protected $detailHeight = 0;
    protected $company = null;
    protected $border_gap = 0;
    protected $total_amount = 0;
    protected $notEnter = 0;
    protected $cash_books = [];
    protected $vo = null;
    protected $dr_cr_type = '';

    public function makepdf($vo)
    {
        $this->vo = $vo;
        $this->vo->load('voucher_details.account', 'account_tds', 'account_other','user_created','cashbooks.account');
        $this->company = getCom();
        $this->cash_books = $this->vo->cashbooks;
        foreach($this->cash_books as $cb) {
            if($this->dr_cr_type == '') {
                $this->dr_cr_type = $cb->dr_cr;
            } else if($this->dr_cr_type != $cb->dr_cr) {
                $this->dr_cr_type = 'B';
            }
        }
        // $this->company = $this->company->load('city.state');
        // dd($this->company);
        $this->testpdf = new TestPdf();
        $this->testpdf->makePdf("P", "A4");
        $this->main();
        return $this->pdf;
    }

    private function setCopy($copyno)
    {
        $pdf = $this->pdf;
        $this->copyno = $copyno;
        $this->header();
        $this->details();
        $this->footer("E");
    }


    private function main($title = '')
    {
        $pdf = $this->pdf;
        $this->pageno = 0;
        $this->footerHeight = 45;
        do {
            $this->pageno++;
            $this->nextpage = "N";
            $this->addNewPage("L", "A5");
            $this->pdf->SetAutoPageBreak(true, 5);
            $this->setCopy(1);
        } while ($this->nextpage == "Y");
    }

    private function header()
    {


        $pdf = $this->pdf;
        $vo = $this->vo;
        $this->lineHeight = 5;
        $leftSideYPos = 0;
        $this->pdf->Line($this->lm, $this->pdf->GetY(), $this->pageWidth + $this->lm, $this->pdf->GetY());
        $this->GetYPos(true);
        $width = 0;
        $width = 0;
        $this->addCell($width, 0, $this->company->company_name, 0, 'C', 1, '', 15, 'B', false, false, true);
        $this->addHLine();
        $this->addCell($width, $this->lineHeight, $this->company->office_address, 0, 'C', 1, 'T', 10, 'T', false, false, true);
        $this->GetYPos(true);
        $this->addCols([25, 100, 40]);
        if ($vo->voucher_type == 'R' && $vo->tr_type == 'C') {
            $this->addCell($this->getColW(1,2), $this->lineHeight, 'Cash Receipt Voucher', 0, 'R', 0, 'T', 12, 'B');
        } else if ($vo->voucher_type == 'R' && $vo->tr_type == 'B') {
            $this->addCell($this->getColW(1,2), $this->lineHeight, 'Bank Receipt Voucher', 0, 'R', 0, 'T', 12, 'B');
        } elseif ($vo->voucher_type == 'P' && $vo->tr_type == 'C') {
            $this->addCell($this->getColW(1,2), $this->lineHeight, 'Cash Payment Voucher', 0, 'R', 0, 'T', 12, 'B');
        } elseif ($vo->voucher_type == 'P' && $vo->tr_type == 'B') {
            $this->addCell($this->getColW(1,2), $this->lineHeight, 'Bank Payment Voucher', 0, 'R', 0, 'T', 12, 'B');
        } elseif ($vo->voucher_type == 'I') {
            $this->addCell($this->getColW(1,2), $this->lineHeight, 'TDS Voucher', 0, 'R', 0, 'T', 12, 'B');
        } elseif ($vo->voucher_type == 'T') {
            $this->addCell($this->getColW(1,2), $this->lineHeight, 'Transfer Voucher', 0, 'R', 0, 'T', 12, 'B');
        } elseif ($vo->voucher_type == 'J') {
            $this->addCell($this->getColW(1,2), $this->lineHeight, 'Journal Voucher', 0, 'R', 0, 'T', 12, 'B');
        }
        $this->addCell($this->getColW(3,4), $this->lineHeight, "", 0, 'R', 1, 'T', 10, 'B');
        $this->pdf->ln(2);

        // if ($this->dr_cr_type != 'B' && ($vo->voucher_type == 'P' || $vo->voucher_type == 'R')){
        //     if ($vo->tr_type == 'B') {
        //         $this->addCell($this->getColW(1), $this->lineHeight, "Bank:", 0, 'L', 0, 'T', 10, 'B');
        //         $this->addCell($this->getColW(2), $this->lineHeight, $vo->account_other ? $vo->account_other->name : '', 0, 'L', 0, 'T', 10, '');
        //     }
        // }
        // else{
            $this->addCell($this->getColW(1,2), $this->lineHeight, "Voucher No. : ".$vo->voucher_no, 0, 'L', 0, 'T', 10, 'B');
            $this->addCell($this->getColW(3,4), $this->lineHeight, "Date : ".$vo->voucher_date, 0, 'R', 1, 'T', 10, 'B');
        // }
        // $this->GetYPos(true);
        // $this->addCols([24, 60, 20, 40]);


        $this->addCols([24, 60, 20, 40]);
        // $this->pdf->ln(2);
        $this->pdf->Line($this->lm, $this->pdf->GetY(), $this->pageWidth + $this->lm, $this->pdf->GetY());
        $this->GetYPos(true);
        $this->addHLine();
        $this->GetYPos(true);
        $this->addCols([10, 50,20, 95]);
        $this->pdf->ln(1);
        $this->addCell($this->getColW(1), $this->lineHeight, "SNo", 0, 'L', 0, 'L', 0, 'B');
        $this->addCell($this->getColW(2), $this->lineHeight, "Head Of Account", 0, 'L', 0, 'L', 0, 'B');
        $this->addCell($this->getColW(3), $this->lineHeight, "Weight", 0, 'C', 0, 'L', 0, 'B');
        $this->addCell($this->getColW(4), $this->lineHeight, "Particulars", 0, 'L', 0, 'L', 0, 'B');
        $this->addCell($this->getColW(5), $this->lineHeight, "Amount", 0, 'C', 1, 'L', 0, 'B');
        // $this->pdf->ln(2);
        $this->addHLine();

        $this->hight = $this->getNetHeight();
        $this->detailHeight = $this->hight - $pdf->getY() - $this->footerHeight;
        $this->secHeight = 0;
        $this->addVLine($this->lm + 160);
    }


    private function details($title = '')
    {
        $this->total_amount = 0;
        $vo = $this->vo;
        $this->i = 1;
        $this->notEnter = 0;
        $debitTransactions = [];
        $creditTransactions=[];
        // foreach ($vo->voucher_details as $entry) {
        //     if (isset($entry['drcr']) && $entry['drcr'] === 'D') {
        //         $debitTransactions[] = $entry;
        //     }

        //     if (isset($entry['drcr']) && $entry['drcr'] === 'C') {
        //         $creditTransactions[] = $entry;
        //     }
        // }
        $bank_dr_cr = '';
        $bank_amt = 0;
        if(in_array($vo->voucher_type,['R','P']) && $vo->tr_type == 'B') {
            $bank_dr_cr = $vo->voucher_type == 'R' ? 'D':'C';
        }
        foreach ($this->cash_books as $entry) {
            if ($entry['dr_cr'] === 'D') {
                $debitTransactions[] = $entry;
            }

            if ($entry['dr_cr'] === 'C') {
                $creditTransactions[] = $entry;
            }
            if($entry['dr_cr'] == $bank_dr_cr && $entry->ac_id == $vo->acid_other) {
                $bank_amt += $entry->amount;
            }
        }
        if($bank_amt != 0) {
            $set = false;
            if($bank_dr_cr == 'D') {
                foreach($debitTransactions as $val) {
                    if($val->ac_id == $vo->acid_other) {
                        if(!$set) {
                            $set = true;
                            $val->amount = $bank_amt;
                            $val->part = '';
                        } else {
                            $val->amount = 0;
                        }
                    }
                }
             } else {
                foreach($creditTransactions as $val) {
                    if($val->ac_id == $vo->acid_other) {
                        if(!$set) {
                            $set = true;
                            $val->amount = $bank_amt;
                            $val->part = '';
                        } else {
                            $val->amount = 0;
                        }
                    }
                }
            }
        }
        $data = array_merge($debitTransactions,$creditTransactions);
        foreach ($data as $key=>$det) {
            if($det->amount != 0) {
                $this->addDetail($key,$det);
            }
        }
    }

    private function addDetail($key,$det)
    {
        $vo = $this->vo;
        $this->detHeight = 0;
        $this->testpdf->addMCell($this->getColW(2), $this->lineHeight, $det->account->name, 0, 'L', 0, 'T', 0, '');
        if ($this->secHeight + $this->testpdf->testHeight > $this->detailHeight) {
            $this->startNewPage();
        }
        if($vo->voucher_type == 'J' || $this->dr_cr_type == 'B'){
            if($det['dr_cr'] == 'D' && $key == 0){
                $this->addMCell($this->getColW(1,5), $this->lineHeight, 'Debit', 1, 'C', 1, 'L', 0, 'B',1);
            }
            else if($det['dr_cr'] == 'C' && $this->notEnter == 0){
                $this->addHLine();
                $this->addMCell($this->getColW(1,3), $this->lineHeight, figToWord($this->total_amount), 0, 'L', 0, 'T', 10, 'B');
                $this->addCell($this->getColW(4), $this->lineHeight, "Total:", 0, 'R', 0, 'T', 10, 'B');
                $this->addCell($this->getColW(5), $this->lineHeight, number_format($this->total_amount, 2, ".", ''), 0, 'R', 1, 'T', 10, '');
                $this->addMCell($this->getColW(1,5), $this->lineHeight, 'Credit', 1, 'C', 1, 'L', 0, 'B',1);
                $this->total_amount = 0;
                $this->notEnter = 1;
            }
        }
        $this->addMCell($this->getColW(1), $this->lineHeight, $this->i, 0, 'L', 0, 'L', 0, '');
        $this->addMCell($this->getColW(2), $this->lineHeight, $det->account->name, 0, 'L', 0, 'T', 0, '');
        $this->addMCell($this->getColW(3), $this->lineHeight, $det->weight, 0, 'L', 0, 'T', 0, '');
        $this->addMCell($this->getColW(4), $this->lineHeight, $det->part, 0, 'L', 0, 'T', 0, '');
        $this->addMCell($this->getColW(5), $this->lineHeight, number_format($det->amount,2,'.',''), 0, 'R', 0, 'T', 0, '');
        $this->pdf->ln($this->detHeight);
        $this->secHeight += $this->detHeight;
        $this->total_amount = $this->total_amount + $det->amount;
        // $this->addVLine($this->lm + 160,40);
        $this->i++;
    }

    private function footer($type = "R")
    {
        $vo = $this->vo;
        if ($this->secHeight > $this->detailHeight) {
            $this->drawBox($this->pdf->getY(), false);
            $this->pdf->Line($this->lm, $this->pdf->GetY(), $this->pageWidth + $this->lm, $this->pdf->GetY());
        }
        // if ($this->detailHeight - $this->secHeight > 0) {
        //     $this->pdf->ln($this->detailHeight - $this->secHeight);
        // }
        // $this->drawBox($this->pdf->getY(), false);
        $this->addHLine();

        if ($type == "E") {
            $this->GetYPos(true);
            $this->addMCell($this->getColW(1,3), $this->lineHeight, figToWord($this->total_amount), 0, 'L', 0, 'T', 10, 'B');
            $this->addCell($this->getColW(4), $this->lineHeight, "Total:", 0, 'R', 0, 'T', 10, 'B');
            $this->addCell($this->getColW(5), $this->lineHeight, number_format($this->total_amount, 2, ".", ''), 0, 'R', 1, 'T', 10, '');
            $this->addVLine($this->lm + 160);
        } else {
            $this->addCols([170]);
            $this->addCell($this->getColW(1), $this->lineHeight, "Continued.....", 0, 'R', 1, 'T', 10, '');
        }
        // $this->addVLine($this->lm + 160);

        $this->addHLine();
        $this->addCols([40,40,40,40]);
        $this->pdf->ln(15);
        $this->addCell($this->getColW(1), $this->lineHeight, "________________________", 0, 'C', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(2), $this->lineHeight, "________________________", 0, 'C', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(3), $this->lineHeight, "________________________", 0, 'C', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(4), $this->lineHeight, "________________________", 0, 'C',0, 'T', 10, 'B');
        $this->addCell($this->getColW(5), $this->lineHeight, "________________________", 0, 'C',1, 'T', 10, 'B');
        $this->pdf->ln(2);
        $this->addCell($this->getColW(1), $this->lineHeight, "Entered By", 0, 'C', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(2), $this->lineHeight, "Checked By", 0, 'C', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(3), $this->lineHeight, "Auditor", 0, 'C', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(4), $this->lineHeight, "VP(F".'$'."A)", 0, 'C', 0, 'T', 10, 'B');
        $this->addCell($this->getColW(5), $this->lineHeight, "Director", 0, 'C',1, 'T', 10, 'B');
        $this->drawBorder($this->border_gap);
    }




    private function startNewPage($footer = 'Y')
    {
        // $this->footerHeight = 20;
        if ($footer == 'Y')
            $this->footer();
        $this->secHeight = 0;
        $this->pageno++;
        $this->addNewPage("L", "A5");
        $this->header();
    }
}
