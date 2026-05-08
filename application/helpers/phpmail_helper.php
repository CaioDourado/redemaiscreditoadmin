<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'config/env.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

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

		//Server settings
		$mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
		$mail->isSMTP();                                            //Send using SMTP
		$mail->Host       = $host;                     //Set the SMTP server to send through
		$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
		$mail->Username   = $username;                     //SMTP username
		$mail->Password   = $password;                               //SMTP password
		$mail->SMTPSecure = strtolower($secure)==='tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
		$mail->Port       = $port;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
		$mail->CharSet 	  = 'UTF-8';

		/*
		//Recipients
		$mail->setFrom('boletos@redemaiscredito.com.br', 'Rede Mais Credito');
		$mail->addAddress('caiof.dourado@gmail.com');               //Name is optional

		//Attachments
		//$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
		//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

		//Content
		$mail->isHTML(true);                                  //Set email format to HTML
		$mail->Subject = 'Titulo do E-mail';
		$mail->Body    = 'Corpo do E-mail';

		$mail->send();
		echo 'Message has been sent';
		*/
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
	} catch (Exception $e) {
		return array('status'=>'erro', 'retorno'=>$mail->ErrorInfo);
	}
}
