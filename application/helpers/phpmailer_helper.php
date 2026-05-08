<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'config/env.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__.'/../third_party/phpmailer/src/Exception.php';
require __DIR__.'/../third_party/phpmailer/src/PHPMailer.php';
require __DIR__.'/../third_party/phpmailer/src/SMTP.php';

function enviar_email($from,$to,$assunto,$mensagem,$nome=null,$cc=NULL,$anexo=null){
    $mail = new PHPMailer(true);
    try {
        $host = adm_env('MAIL_HOST');
        $username = adm_env('MAIL_USERNAME');
        $password = adm_env('MAIL_PASSWORD');
        $port = (int)adm_env('MAIL_PORT', 465);
        $secure = adm_env('MAIL_ENCRYPTION', 'ssl');

        if(empty($host) || empty($username) || empty($password)){
            return array('status'=>'erro', 'retorno'=>'Configuracao de e-mail incompleta.');
        }

        if(empty($to)){
            return array('status'=>'erro', 'retorno'=>'Destinatario de e-mail nao informado.');
        }

        $mail->isSMTP();
        $mail->Host = $host;
        $mail->SMTPAuth = true;
        $mail->Username = $username;
        $mail->Password = $password;
        $mail->SMTPSecure = strtolower($secure)==='tls' ? 'tls' : 'ssl';
        $mail->Port = $port;
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
