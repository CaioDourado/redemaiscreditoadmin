<?php defined('BASEPATH') OR exit('No direct script access allowed');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function enviar_email($from,$to,$assunto,$mensagem,$nome=null,$cc=NULL,$anexo=null){
	$mail = new PHPMailer(true);

	try {
		//Server settings
		$mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
		$mail->isSMTP();                                            //Send using SMTP
		$mail->Host       = 'br458.hostgator.com.br';                     //Set the SMTP server to send through
		$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
		$mail->Username   = 'boleto@redemaiscredito.com.br';                     //SMTP username
		//$mail->Password   = 'Rmc*2024';                               //SMTP password
		$mail->Password   = '6:MX5c9?Ciwk';                               //SMTP password
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
		$mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
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
		echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
	}
}
