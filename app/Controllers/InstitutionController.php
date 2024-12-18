<?php

namespace App\Controllers;

use App\Models\GuestEntry;
use App\Requests\CustomRequestHandler;
use App\Response\CustomResponse;
use Psr\Http\Message\ResponseInterface as Response;
// use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Validator as v;
use App\Validation\Validator;
// use DbConnect;
use PDO;
use DbHandler;
use UtilHelper;
// $settings = require_once  __DIR__ . "/../../config/settings.php";
use voku\helper\HtmlDomParser;



class InstitutionController
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
    $this->utilHelper = new UtilHelper();
    $this->dbHandler = new DbHandler();
    date_default_timezone_set('Asia/Jakarta');
  }

  public function getInstitutionDetail(Request $request, Response $response, array $parm)
  {

    $arr1 = array();
    $arr2 = array();
    $arr1 = array();

    // $id_regency = (empty(CustomRequestHandler::getParam($request, "id_regency"))) ? '' : CustomRequestHandler::getParam($request, "id_regency");
    $id=$parm["id"];

    $sql = "SELECT * FROM tbl_institution WHERE `status`='1' AND id='$id'
        
    ";

    $result = $this->dbHandler->getDataAll($sql);
    if ($result->rowCount() > 0) {
      // code...

      $result = $this->dbHandler->getDataAll($sql);
      $data_row = $result->fetch(PDO::FETCH_ASSOC);
      $institution_id = $data_row["id"];

      $sqlSymbol = "SELECT * FROM tbl_institution_symbol WHERE institution_id='$institution_id' AND `status`='1'
        
      ";

      $resultSymbol = $this->dbHandler->getDataAll($sqlSymbol);
      if ($resultSymbol->rowCount() > 0) {
        // code...

        while ($data_rowSymbol = $resultSymbol->fetch(PDO::FETCH_ASSOC)) {

          $symbol = img_institution($data_rowSymbol["symbol"]);

          $arr2[] = array(
            'id' => "" . $data_rowSymbol["id"] . "",
            'title' => "" . clean_special_characters($data_rowSymbol["title"]) . "",
            'description' => "" . clean_special_characters($data_rowSymbol["description"]) . "",
            'img' => $symbol,
          );
        }

        
      }

      $output=null;

      $sqlLeadership = "SELECT * FROM tbl_institution_leadership WHERE institution_id='$institution_id' AND `status`='1'
      ORDER BY `year` DESC
        
      ";

      $resultLeadership = $this->dbHandler->getDataAll($sqlLeadership);
      if ($resultLeadership->rowCount() > 0) {
        // code...

        while ($data_rowLeadership = $resultLeadership->fetch(PDO::FETCH_ASSOC)) {

          $img = img_leadership($data_rowLeadership["img"]);

          $arr3[] = array(
            'id' => "" . $data_rowLeadership["id"] . "",
            'name' => "" . clean_special_characters($data_rowLeadership["name"]) . "",
            'title' => "" . clean_special_characters($data_rowLeadership["title"]) . "",
            'year' => "" . clean_special_characters($data_rowLeadership["year"]) . "",
            'period' => "" . clean_special_characters($data_rowLeadership["period"]) . "",
            'biography' => "" . clean_special_characters($data_rowLeadership["biography"]) . "",
            'img' => $img,
          );
        }

        // // Grup data berdasarkan year
        // $result = [];
        // foreach ($arr3 as $item) {
        //   $year = $item['year'];
        //   unset($item['year']); // Hilangkan key 'year' untuk data akhir

        //   $result[$year]['year'] = $year; // Set year di grup
        //   $result[$year]['list'][] = $item; // Tambahkan data ke list
        // }

        // // Konversi hasil menjadi array numerik
        // $output = array_values($result);

        // Grup data berdasarkan year dan period
        $result = [];
        foreach ($arr3 as $item) {
            $year = $item['year'];
            $period = $item['period'];

            // Hilangkan 'year' dan 'period' dari tiap item
            unset($item['year'], $item['period']);

            // Buat grup berdasarkan year dan period
            $key = $year . '-' . $period; // Kunci unik untuk grup
            if (!isset($result[$key])) {
                $result[$key] = [
                    'year' => $year,
                    'period' => $period,
                    'list' => []
                ];
            }

            $result[$key]['list'][] = $item;
        }

        // Konversi hasil menjadi array numerik
        $output = array_values($result);

        
      }

      $img = img_institution($data_row["logo"]);
      $img_poster = img_institution($data_row["poster"]);

      $arr1 = array(
        'id' => "" . $data_row["id"] . "",
        'name' => "" . clean_special_characters($data_row["name"]) . "",
        'vision' => clean_special_characters($data_row["vision"]),
        'mission' => json_decode($data_row["mission"]),
        'description' => "" . clean_special_characters($data_row["description"]) . "",
        'logo' => $img,
        'img_poster' => $img_poster,
        'symbol' => $arr2,
        'leadership' => $output,
      );

      
    }

    return $this->customResponse->is200Response($response, $arr1);
  }

}
