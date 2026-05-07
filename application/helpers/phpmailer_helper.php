<?php defined('BASEPATH') OR exit('No direct script access allowed');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__.'/../third_party/phpmailer/src/Exception.php';
require __DIR__.'/../third_party/phpmailer/src/PHPMailer.php';
require __DIR__.'/../third_party/phpmailer/src/SMTP.php';

function enviar_email($from,$to,$assunto,$mensagem,$nome=null,$cc=NULL,$anexo=null){
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'redemaiscreditoboletos@gmail.com';
        $mail->Password = 'sosabfdstjtrdisj';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->setFrom($from, $nome);
        $mail->addAddress($to);

        if($cc!=null&&is_array($cc)){
            foreach($cc as $index => $item):
                $mail->addBCC($item);
            endforeach;
        }

        if($anexo!=null&&is_array($anexo)){
            foreach ($anexo as $index => $item):
                $mail->AddAttachment($item['caminho'], $name = $item['nome'],  $encoding = 'base64', $type = 'application/pdf');
            endforeach;
        }

        $mail->isHTML(true);
        $mail->Subject = $assunto;
        $mail->Body = $mensagem;

        if ($mail->send()) {
            return array('status' => 'ok', 'retorno' => array('remetente' => $from, 'destinatario' => $to, 'assunto' => $assunto, 'mensagem' => $mensagem, 'nome' => $nome));
        } else {
            return array('status' => 'erro', 'retorno' => $mail->ErrorInfo);
        }
    }catch (Exception $e) {
        return array('status' => 'erro', 'retorno' => $mail->ErrorInfo);
    }
}
