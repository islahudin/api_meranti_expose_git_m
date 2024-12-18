<?php

namespace App\Controllers;

use App\Models\GuestEntry;
use App\Requests\CustomRequestHandler;
use App\Response\CustomResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;
use Respect\Validation\Validator as v;
use App\Validation\Validator;
// use DbConnect;
use PDO;
use DbHandler;
use HtmlTemplateHelper;
use MailerHelper;
use Mpdf\Mpdf as _mpdf;



class MailTestController
{

    protected  $customResponse;
    protected  $validator;

    protected  $emails;
    protected  $htmlTemplateHelper;
    protected  $mpdf;

    public function  __construct()
    {
        $this->customResponse = new CustomResponse();
        $this->validator = new Validator();

        $this->emails = new MailerHelper();
        $this->htmlTemplateHelper = new HtmlTemplateHelper();
        $this->mpdf = new _mpdf;

        date_default_timezone_set('Asia/Jakarta');
    }

    public function sendEmailTest(Request $request, Response $response)
    {

        $this->validator->validate($request, [
            "subject" => v::notEmpty(),
            "from_email" => v::notEmpty()->email(),
            "title_from" => v::notEmpty(),
            "to_email" => v::notEmpty()->email(),
            "content_email" => v::notEmpty()
        ]);

        if ($this->validator->failed()) {
            $responseMessage = $this->validator->errors;
            return $this->customResponse->is400Response($response, $responseMessage);
        }

        $subject = CustomRequestHandler::getParam($request, "subject");
        $from_email = CustomRequestHandler::getParam($request, "from_email");
        $title_from = CustomRequestHandler::getParam($request, "title_from");
        $to_email = CustomRequestHandler::getParam($request, "to_email");
        $content_email = CustomRequestHandler::getParam($request, "content_email");

        ##sample param
        // $subject="subjct kirim email";
        // $from_email="cs@bijakin.com";
        // $title_from="cs Bijakin";
        // $to_email="islahudin.soft01engineer@gmail.com";
        // $content_email = "<h1>Send HTML Email using SMTP in PHP</h1>
        // <p>This is a test email sending using SMTP mail server with PHPMailer.</p>";

        $send_email = $this->emails->sendEmail($subject, $from_email, $title_from, $to_email, $content_email);

        if ($send_email) {
            return $this->customResponse->is200Response($response, null);
        } else {
            return $this->customResponse->is400Response($response, "Faild send email");
        }
    }

    public function sendEmailTest2(Request $request, Response $response)
    {

        ##sample param
        $subject = "subjct kirim email";
        $from_email = "cs@bijakin.com";
        $title_from = "cs Bijakin";
        $to_email = "islahudin.soft01engineer@gmail.com";
        // $content_email = "<h1>Send HTML Email using SMTP in PHP</h1>
        // <p>This is a test email sending using SMTP mail server with PHPMailer.</p>";

        $content_email = $this->htmlTemplateHelper->EmailTemplate("islahudin", "12345", "ord001");
        $content_pdf = $this->htmlTemplateHelper->EmailPdf("islahudin", "12345", "ord001", "online");

        echo $content_email;

        $this->mpdf->WriteHTML($content_pdf);
        // $this->mpdf->Output();

        ob_start();

        $atc_file = $this->mpdf->Output('', 'S');
        ob_end_clean();

        $file_name = "myticket.pdf";
        $send_email = $this->emails->sendEmail($subject, $from_email, $title_from, $to_email, $content_email, "", "", "", $atc_file, $file_name);

        if ($send_email) {
            return $this->customResponse->is200Response($response, null);
        } else {
            return $this->customResponse->is400Response($response, "Faild send email");
        }
    }
}
