<?php
require('fpdf/mc_table.php');

function GenerateWord()
{
    //Get a random word
    $nb=rand(3,10);
    $w='';
    for($i=1;$i<=$nb;$i++)
        $w.=chr(rand(ord('a'),ord('z')));
    return $w;
}

function GenerateSentence()
{
    //Get a random sentence
    $nb=rand(1,10);
    $s='';
    for($i=1;$i<=$nb;$i++)
        $s.=GenerateWord().' ';
    return substr($s,0,-1);
}
class MyPDF extends PDF_MC_Table
{
// Page header
function Header()
{
 $this->Image('cetak/kop-surat.png',0,0);
}
}

$pdf=new MyPDF();
$pdf->SetMargins(30,40,30);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',10);
//Table with 20 rows and 4 columns
$pdf->SetWidths(array(30,50,30,40));
srand(microtime()*1000000);
for($i=0;$i<20;$i++)
    $pdf->Row(array(GenerateSentence(),GenerateSentence(),GenerateSentence(),GenerateSentence()));
// $pdf->Output('cetak/tes2.pdf','F'); 
$pdf->Output();
//coba aja ini kagi kok ok ok
?>
