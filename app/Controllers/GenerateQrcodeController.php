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
use UtilHelper;

class GenerateQrcodeController
{

    protected  $customResponse;
    protected  $validator;

    protected  $utilHelper;

    public function  __construct()
    {
        $this->customResponse = new CustomResponse();
        $this->validator = new Validator();

        $this->utilHelper = new UtilHelper();

        date_default_timezone_set('Asia/Jakarta');
    }

    public function generateQrcodeTest(Request $request, Response $response)
    {

        $qrcode = $this->utilHelper->generateQrCode("12345", 300, "qrcode12");

        // $arrData= null;
        if ($qrcode) {
            $arrData = array(
                'qrcode' => "" . $qrcode . "",

            );

            return $this->customResponse->is200Response($response, $arrData);
        } else {
            return $this->customResponse->is400Response($response, "Faild generate qrcode");
        }
        // echo '
        // <br /><img src="'.$qrcode.'" alt="" />
        // ';

    }
}
