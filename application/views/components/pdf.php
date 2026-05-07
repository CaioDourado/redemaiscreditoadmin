<?php
    require_once __DIR__ . '/../../../vendor/autoload.php';
    date_default_timezone_set('America/Sao_Paulo');

    if(isset($orientation)){
        if($orientation=='L') $mpdf=new \Mpdf\Mpdf('c','A4-L');
        else $mpdf=new mPDF();
    }else{
        $mpdf=new \Mpdf\Mpdf();
    }
    $mpdf->SetDisplayMode('fullpage');

    //$css = file_get_contents(base_url()."assets/css/pdf.css");

    // Doideira Total

    $css_url = base_url()."assets/css/pdf.css";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $css_url);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 40000); //timeout in seconds
    //curl_setopt($ch, CURLOPT_NOBODY, TRUE); // remove body
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $return = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Fim

    $css = $return;

    $mpdf->WriteHTML($css,1);
    if(isset($titulo)) $mpdf->SetTitle($titulo); else $mpdf->SetTitle('Domento Rede Mais Credito');
    $mpdf->WriteHTML($conteudo);
    if(isset($senha)) $mpdf->SetProtection(array(),$senha,'d41d8cd98f00b204e9800998ecf8427e');
    $nome_arquivo = isset($titulo) ? $titulo.'.pdf' : 'documento_redemaiscredito.pdf';
    $nome_arquivo = preg_replace('/[^A-Za-z0-9_. -]/', '', $nome_arquivo);

    if (!headers_sent()) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="'.$nome_arquivo.'"');
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
    }

    $mpdf->Output($nome_arquivo,'I');
    exit;
