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
    $css = file_get_contents(base_url()."assets/css/pdf.css");
    $mpdf->WriteHTML($css,1);
    if(isset($titulo)) $mpdf->SetTitle($titulo); else $mpdf->SetTitle('Documento Rede Mais Credito');
    $mpdf->WriteHTML($conteudo);
    if(isset($senha)) $mpdf->SetProtection(array(),$senha,'d41d8cd98f00b204e9800998ecf8427e');
    $tmp_path = FCPATH.'tmp'.DIRECTORY_SEPARATOR;
    if(!is_dir($tmp_path)){
        mkdir($tmp_path, 0755, true);
    }

    if(isset($nome_arquivo)){
        $mpdf->Output($tmp_path.$nome_arquivo.'.pdf','F');
    }else{
        $mpdf->Output($tmp_path.'documento_redemaiscredito.pdf','F');
    }
    //exit;
