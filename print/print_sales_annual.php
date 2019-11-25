<?php 
require_once "../support/fpdf.php";
require_once "../support/ja_config.php";

$test = $conn->query("select Year,Sum(Price) as Income,ServiceType from (
    select TransactionNumber,YEAR([date/time]) as [Year],ServiceType, SUM(ServicePrice) as Price  from dashboard_View
    group by TransactionNumber,YEAR([date/time]),ServiceType) b
    group by ServiceType,Year")->fetchAll(PDO::FETCH_ASSOC);

    $initData = array();
    $total = 0;
    
  
    foreach($test as $key => $val){
        $initData["{$val['Year']}"][$val["ServiceType"]] = $val["Income"];
        $total += floatval($val["Income"]);
    
        if($val === end($test)){
            $initData["{$val['Year']}"]["Total"] = $total;
        }
    }

//    print_pre($initData);
//    die;

$categ = array("Hair","Face","Nail","Body");

$pdf = new FPDF("P", "mm", "Letter");
$pdf->addPage();
$pdf->SetTitle('Annual Report');

$pdf->Image("BG_Circle.png",-110,-20, 300, 150);
$pdf->FooterName = "Carl Dennis Alignalan";
$pdf->SetFont('Arial','',12);
$pdf->Cell('100','5','J&A Inventory and Records Management System',0,1,'l');
$pdf->SetFont('Arial','',15);
$pdf->Cell('195','10',"Annual Sales Report",0,1,'C');
$pdf->SetFont('Arial','',10);
$pdf->Cell(48.75,'5',"Year",1,0,'C');
$pdf->Cell(48.75,'5',"Categories",1,0,'C');
$pdf->Cell(48.75,'5',"Total Per Category",1,0,'C');
$pdf->Cell(48.75,'5',"Total",1,1,'C');




$finalTotal = 0;
foreach($initData as $key => $val){
    
    foreach($categ as $key1 => $val1){
        
        $income =  array_key_exists($val1,$val) ? "P ".number_format(floatval($val[$val1]),2,".",",") : "0.00";

        if($key1 === 0){
            $pdf->Cell(48.75,5,$key,1,0,"C");
            $pdf->Cell(48.75,5,$val1,1,0,"C");
            $pdf->Cell(48.75,5,$income,1,0,"C");
            $pdf->Cell(48.75,5,"",1,1,"C");
        }else{
            $pdf->Cell(48.75,5,"",1,0,"C");
            $pdf->Cell(48.75,5,$val1,1,0,"C");
            $pdf->Cell(48.75,5,$income,1,0,"C");
            $pdf->Cell(48.75,5,"",1,1,"C");
        }
    }
    $pdf->Cell(48.75,5,"",1,0,"C");
    $pdf->Cell(48.75,5,"",1,0,"C");
    $pdf->Cell(48.75,5,"",1,0,"C");
    $pdf->Cell(48.75,5,"P ".number_format(floatval($val["Total"]),2,".",","),1,1,"C");
    $finalTotal += floatval($val["Total"]);
    
}
    $pdf->Cell(48.75,5,"",0,0,"C");
    $pdf->Cell(48.75,5,"",0,0,"C");
    $pdf->Cell(48.75,5,"",0,0,"C");
    $pdf->Cell(48.75,5,"P ".number_format($finalTotal,2,".",","),"B",1,"C");



$pdf->Output("I", "annual_report.pdf");