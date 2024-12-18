<?php

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailerHelper{
	private $mailer = NULL;
	public function __construct() {
    //Create an instance; passing `true` enables exceptions
    $this->mailer = new PHPMailer(true);
    }

	public function sendEmail($subject,$from, $title_from,$to,$content_email,$cc="",$bcc="",$replay="",$atc_file="",$atc_file_name=""){

    try {
        //Server settings
        // $this->mailer->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $this->mailer->isSMTP();                                            //Send using SMTP
        $this->mailer->Host       = 'bijakin.com';                     //Set the SMTP server to send through
        $this->mailer->SMTPAuth   = true;                                   //Enable SMTP authentication
        $this->mailer->Username   = 'dev@bijakin.com';                     //SMTP username
        $this->mailer->Password   = 'L1ghtMOONSTER12';                               //SMTP password
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $this->mailer->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        #//Recipients
        $this->mailer->setFrom($from, $title_from);
        // $this->mailer->addAddress('islahudin.soft01engineer@gmail.com', 'Agan islah');     //Add a recipient
        $this->mailer->clearAddresses(); // ADD cleanEmailAddress :)
        $this->mailer->addAddress($to);     //Add a recipient
        // $this->mailer->addAddress('ellen@example.com');               //Name is optional
        // $this->mailer->addReplyTo('info@example.com', 'Information');
        // $this->mailer->addCC('cc@example.com');
        // $this->mailer->addBCC('bcc@example.com');

        #//Attachments
        // $this->mailer->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        // $this->mailer->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

        #//Content
        // $this->mailer->isHTML(true);                                  //Set email format to HTML
        // $this->mailer->Subject = 'Here is the subject';
        // $this->mailer->Body    = 'This is the HTML message body <b>in bold!</b>';
        // $this->mailer->AltBody = 'This is the body in plain text for non-HTML mail clients';

        if(!empty($replay)){
    		$exp=explode(",",$replay);
    		foreach($exp as $replay){
    			$this->mailer->addReplyTo($replay); // replay email to...
    		}
    		}
    		if(!empty($cc)){
    		$this->mailer->ClearCCs(); // ADD ClearCCs :)
    		$exp=explode(",",$cc);
    		foreach($exp as $cc){
    			$this->mailer->addCC($cc); // Cc Email to...
    		}
    		}
    		if(!empty($bcc)){
    		$this->mailer->ClearBCCs(); // ADD ClearBCCs :)
    		$exp=explode(",",$bcc);
    		foreach($exp as $bcc){
    			$this->mailer->addBCC($bcc); // BCC Email to...
    		}
    		}
    		if(!empty($atc_file)){
    			if(!empty($atc_file_name)){
    				$this->mailer->clearAttachments(); // ADD cleanStringAttachment :)
    				$this->mailer->addStringAttachment($atc_file, $atc_file_name);
    			}

    			// $this->mailer->addStringAttachment($atc_file, $atc_file, 'base64', 'application/pdf');
    		/*$exp=explode(",",$atc_file);
    		foreach($exp as $atc_file){
    			$this->mailer->addStringAttachment($atc_file, 'doc.pdf', 'base64', 'application/pdf'); // jika attachments file
    		}*/
    		}
    		$this->mailer->Subject = $subject; // Subjec Email...
    		$this->mailer->isHTML(true); // Set email format to HTML
    		$this->mailer->Body = $content_email; // Email body content (isi email)
    		// Send email
    		if(!$this->mailer->send()) {
    			$mail_sukses = "0"; // gagal kiirm Email
    		}
    		// Email berhasil di kirm
    		else {
    			$mail_sukses = "1";
    		}
    		return $mail_sukses;


        // $this->mailer->send();
        // echo 'Message has been sent';
    } catch (Exception $e) {
        // echo "Message could not be sent. Mailer Error: {$this->mailer->ErrorInfo}";
        return $mail_sukses="0";
    }


	}
}
$emails=new MailerHelper;
/*
$subject = "Send Email via SMTP using PHPMailer";
$content_email = "<h1>Send HTML Email using SMTP in PHP</h1>
			<p>This is a test email sending using SMTP mail server with PHPMailer.</p>";
$kirim = $emails->sendEmail($subject,$from="bowo@smartcomputerindo.com", $title_from="Bowo Susanto",$to="susanto.wibowoo@gmail.com",$isi_email);
echo $kirim;*/
