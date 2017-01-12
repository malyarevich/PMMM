<?php
//Plugin Name: Quiz PDF
//Description: generate and download pdf certificate after passing the quiz
//Version:  1.0
 function get_certificate()
 {
//     wp_redirect('http://wordpress.p350419.webspaceconfig.de/wp-content/plugins/quiz-pdf/test.php');
//     require_once("../../../wp-load.php");
     require_once('fpdf181/fpdf.php');
     $user  = get_user_by('email', $_SESSION['user_course_mail']);

//create a FPDF object
     $pdf=new FPDF();
     $pdf->AddFont('Demo_ConeriaScript','','Demo_ConeriaScript.php');
     $pdf->SetFont('Demo_ConeriaScript','',30);
     $pdf->SetTextColor(50,60,100);

     $pdf->SetTitle('Bacchus Certificate');

////set up a page
     $pdf->AddPage('P');
     $pdf->SetDisplayMode('real','default');

//insert an image and make it a link

     $pdf->SetXY(50,50);
     $pdf->SetDrawColor(50,60,100);
     $pdf->SetTextColor(70,70,70);
     $pdf->Cell(100,10,'Certificate of Completion',0,100,'C',0);
     $pdf->Ln(20);
     $pdf->SetTopMargin(190);
     $pdf->Cell(0,10,'for',0,0,'C',0);
     $pdf->Ln(20);
     $pdf->Cell(190,10,'Online South African Wine Education',0,0,'C',0);
     $pdf->Ln(20);
     $pdf->Cell(0,10,'Course',0,0,'C',0);
     $pdf->Ln(20);
     $pdf->SetFont('Times','B',10);
     $pdf->Cell(0,10,'This certificate is presented to,',0,0,'C',0);
     $pdf->Ln(20);
     $pdf->SetFont('Demo_ConeriaScript','',25);
    //$pdf->Cell(190,10,'123' . ' ' . '456',0,0,'C',0);
     $pdf->Cell(190,10,$user->first_name . ' ' . $user->last_name,0,0,'C',0);
     $pdf->Cell(190,10,$_SESSION['user_course_mail'],0,0,'C',0);
     $pdf->Ln(20);
     $pdf->SetFont('Times','B',10);
     $pdf->Cell('',10,'for successfully completing the Wines of South Africa Online South African Wine Education Course ,',0,0,'C',0);
     $pdf->Ln(20);
     $pdf->SetFont('Demo_ConeriaScript','',25);
     $pdf->Cell('',10,'on '.date("j F Y"),0,0,'C',0);
     $pdf->Ln(20);

     $pdf->Image(plugins_url().'/quiz-pdf/img/signature.png',15,220,0,0,'PNG');
     $pdf->Image(plugins_url().'/quiz-pdf/img/logo.png',110,220,0,0,'PNG');

     $pdf->SetXY (10,50);
     $pdf->SetFontSize(10);

    //Output the document
     $pdf->Output('D', 'Bacchus Certificate.pdf');
 }
add_action('generate_pdf', 'get_certificate');

function rewriteCertificateUrl() {
    $url = array_diff(explode('/', $_SERVER['REQUEST_URI']), array(''));
    switch ($url[1]){
        case "quiz-certificate":
            do_action('generate_pdf');
            die;
            break;
        default:
            break;
    }
}
add_action('init', 'rewriteCertificateUrl');

function generate_quiz_certificate(){
    echo get_site_url().'/quiz-certificate';
}

?>