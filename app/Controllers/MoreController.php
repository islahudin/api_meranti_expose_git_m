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

class MoreController
{

    protected  $customResponse;


    protected  $validator;
    protected  $conn;
    protected  $dbHandler;
    protected  $utilHelper;

    public function  __construct()
    {
        $this->customResponse = new CustomResponse();

        $this->validator = new Validator();
        $this->dbHandler = new DbHandler();
        $this->utilHelper = new UtilHelper();
        date_default_timezone_set('Asia/Jakarta');
    }

    public function getBlog(Request $request, Response $response)
    {
        $url = "http://apiblog.kreen.id/api2/v1/list_article2";
        $data = $this->utilHelper->curlConnect($url);

        $data_api = array();

        if ($data != null) {
            // code...
            $json_api = json_decode($data, true);
            if (!empty($json_api['success'])) {
                $data_api = $json_api['data'];

                return $this->customResponse->is200Response($response, $data_api);
            } else {
                return $this->customResponse->is404Response($response, "faild");
            }
        } else {
            return $this->customResponse->is404Response($response, "faild");
        }
    }

    public function getNews(Request $request, Response $response)
    {

        $endpoint = 'https://api-berita-indonesia.vercel.app/';

        $transaction = $this->utilHelper->curlConnect($endpoint, "");
        // printf($transaction);

        $data_api = array();

        if ($transaction != null) {

            $json_api = json_decode($transaction, true);
            if (!empty($json_api['endpoints'])) {
                // printf($transaction);

                $data_api = $json_api['endpoints'];
                return $this->customResponse->is200Response($response, $data_api);
            } else {
                return $this->customResponse->is404Response($response, "faild");
            }
        } else {
            return $this->customResponse->is404Response($response, "faild");
        }
    }
}
