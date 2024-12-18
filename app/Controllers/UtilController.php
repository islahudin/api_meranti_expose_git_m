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



class UtilController
{

  protected $customResponse;


  protected $validator;
  protected $conn;
  protected $dbHandler;
  protected $utilHelper;

  public function __construct()
  {
    $this->customResponse = new CustomResponse();

    $this->validator = new Validator();
    $this->utilHelper = new UtilHelper();
    $this->dbHandler = new DbHandler();
    date_default_timezone_set('Asia/Jakarta');
  }

  public function getBanner(Request $request, Response $response, array $parm)
  {

    $arr1 = array();

    // $id_regency = (empty(CustomRequestHandler::getParam($request, "id_regency"))) ? '' : CustomRequestHandler::getParam($request, "id_regency");

    $sql = "SELECT * FROM tbl_banner WHERE `status`='1'
        
        ";

    $result = $this->dbHandler->getDataAll($sql);
    $total = $result->rowCount();
    if ($total > 0) {
      // code...

      $result = $this->dbHandler->getDataAll($sql);

      while ($data_row = $result->fetch(PDO::FETCH_ASSOC)) {

        $banner = img_banner_main($data_row["img"]);

        $arr1[] = array(
          'id' => "" . $data_row["id"] . "",
          'title' => "" . clean_special_characters($data_row["title"]) . "",
          'img' => $banner,
        );
      }
    }

    return $this->customResponse->is200Response($response, $arr1);
  }

  public function getDistrict(Request $request, Response $response, array $parm)
  {

    $arr1 = array();

    // $id_regency = (empty(CustomRequestHandler::getParam($request, "id_regency"))) ? '' : CustomRequestHandler::getParam($request, "id_regency");

    $sql = "SELECT * FROM tbl_adm_district WHERE id_regency='$parm[id_regency]'
        
        ";

    $result = $this->dbHandler->getDataAll($sql);
    $total = $result->rowCount();
    if ($total > 0) {
      // code...

      $result = $this->dbHandler->getDataAll($sql);

      while ($data_row = $result->fetch(PDO::FETCH_ASSOC)) {

        $arr1[] = array(
          'id' => "" . $data_row["id"] . "",
          'title' => "" . clean_special_characters($data_row["name"]) . "",
          'slug' => "",
        );
      }
    }

    return $this->customResponse->is200Response($response, $arr1);
  }

  public function getMainMenu(Request $request, Response $response, array $parm)
  {

    $arrMainMenu = null;

    $language = CustomRequestHandler::getParam($request, "language", "id");

    if ($language == "en") {
      $sqlMainMenu = "SELECT id, title_en AS title, slug, subtitle, img FROM tbl_main_menu WHERE `status` ='1' ORDER BY `sort` ASC";
    } else {
      $sqlMainMenu = "SELECT id, title AS title, slug, subtitle, img FROM tbl_main_menu WHERE `status` ='1' ORDER BY `sort` ASC";
    }

    $resultMainMenu = $this->dbHandler->getDataAll($sqlMainMenu);
    if (($resultMainMenu->rowCount()) > 0) {

      while ($data_rowMainMenu = $resultMainMenu->fetch(PDO::FETCH_ASSOC)) {

        $img = img_main_menu($data_rowMainMenu["img"]);

        $arrMainMenu[] = array(
          'id' => "" . $data_rowMainMenu["id"] . "",
          'title' => "" . $data_rowMainMenu["title"] . "",
          'slug' => "" . $data_rowMainMenu["slug"] . "",
          'subtitle' => "" . $data_rowMainMenu["subtitle"] . "",
          'img' => $img,
        );
      }
    }

    return $this->customResponse->is200Response($response, $arrMainMenu);
  }

  public function getApi(Request $request, Response $response, array $parm)
  {

    $arr1 = array();

    $url = "http://apiblog.kreen.id/api2/v1/list_article2";
    $data = connectCURL($url);
    if ($data != null) {
      // code...
      $json_api = json_decode($data, true);
      if (!empty($json_api['success'])) {
        $arr1 = $json_api['data'];
        return $this->customResponse->is200Response($response, $arr1);
      } else {
        // code...
        return $this->customResponse->is404Response($response, "Data tidak ditemukan");
      }
    } else {
      // code...

      return $this->customResponse->is404Response($response, "Data tidak ditemukan");
    }
  }

  public function getApiSchool(Request $request, Response $response, array $parm)
  {

    $arr1 = array();
    $created_at = date('Y-m-d H:i:s');

    // $url = "http://apiblog.kreen.id/api2/v1/list_article2";
    $url = "https://api-sekolah-indonesia.vercel.app/sekolah?kab_kota=091500&page=1&perPage=1000";
    $data = connectCURL($url);
    if ($data != null) {
      // code...
      $json_api = json_decode($data, true);
      if (!empty($json_api['status'])) {
        $arr1 = $json_api['dataSekolah'];
        //   return $this->customResponse->is200ResponsePg($response, $arr1);
        // echo"".$arr1["kode_prop"];

        foreach ($arr1 as $index => $value) {

          $sqlInsert = "INSERT INTO tbl_school SET
                id='" . $value["id"] . "',
                npsn='" . $value["npsn"] . "',
                sekolah='" . $value["sekolah"] . "',
                bentuk='" . $value["bentuk"] . "',
                status='" . $value["status"] . "',
                alamat_jalan='" . $value["alamat_jalan"] . "',
                lintang='" . $value["lintang"] . "',
                bujur='" . $value["bujur"] . "',
                kode_prop='" . $value["kode_prop"] . "',
                propinsi='" . $value["propinsi"] . "',
                kode_kab_kota='" . $value["kode_kab_kota"] . "',
                kabupaten_kota='" . $value["kabupaten_kota"] . "',
                kode_kec='" . $value["kode_kec"] . "',
                kecamatan='" . $value["kecamatan"] . "',
                created_at='$created_at';
                ";

          $result = $this->dbHandler->insertDataAll($sqlInsert);
          if ($result) {

            echo $index . "= " . $value['sekolah'] . "<br>";
          }
        }
        // print_r($arr1);
      } else {
        // code...
        return $this->customResponse->is404Response($response, "Data tidak ditemukan1");
      }
    } else {
      // code...

      return $this->customResponse->is404Response($response, "Data tidak 2");
    }
  }

  public function scripingMasjid(Request $request, Response $response, array $parm)
  {

    $arr1 = array();

    $this->validator->validate($request, [
      "page" => v::notEmpty(),
    ]);

    if ($this->validator->failed()) {
      $responseMessage = $this->validator->errors;
      return $this->customResponse->is400Response($response, $responseMessage);
    }

    $page = CustomRequestHandler::getParam($request, "page");

    $created_at = date('Y-m-d H:i:s');

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "https://simas.kemenag.go.id/page/search/masjid/4/85/0/0/?p=$page");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    $html = curl_exec($curl);
    curl_close($curl);

    // initialize HtmlDomParser 
    $htmlDomParser = HtmlDomParser::str_get_html($html);
    // print_r($htmlDomParser);

    ######################

    // retrieve the HTML pagination elements with 
    // the ".page-numbers a" CSS selector 
    $paginationElements = $htmlDomParser->find(".pagination a");
    $paginationLinks = [];
    foreach ($paginationElements as $paginationElement) {
      // populate the paginationLinks set with the URL 
      // extracted from the href attribute of the HTML pagination element 
      $paginationLink = $paginationElement->getAttribute("href");
      // avoid duplicates in the list of URLs 
      if (!in_array($paginationLink, $paginationLinks)) {
        $paginationLinks[] = $paginationLink;
      }
    }

    // print the paginationLinks array 
    // print_r($paginationLinks);

    ######################

    // remove all non-numeric characters in the last element of 
    // the $paginationLinks array to retrieve the highest pagination number 
    $highestPaginationNumber = preg_replace("/\D/", "", end($paginationLinks));

    ######################

    $productDataLit = array();

    // retrieve the list of products on the page 
    $productElements = $htmlDomParser->find(".search-result-item");
    foreach ($productElements as $productElement) {
      // extract the product data 
      // $url = $productElement->findOne("a")->getAttribute("href"); 
      // $image = $productElement->findOne("img")->getAttribute("src"); 
      // $name = $productElement->findOne("h2")->text; 
      // $name = $productElement->findOne("h2")->text; 
      // $price = $productElement->findOne(".price span")->text; 

      // $price = $productElement->findOne(".search-result-item span")->text; 
      $image = $productElement->findOne("img")->getAttribute("src");
      $name = clean_special_characters($productElement->findOne("h4")->text);
      $address = $productElement->findOne("p")->text;
      $url = $productElement->findOne("a")->getAttribute("href");
      $url2 = $productElement->findOne("a")->getAttribute("href");
      $mapLink = $productElement->find('.btn-secondary', 0)->href;

      $url1 = $url;
      $path = parse_url($url1, PHP_URL_PATH);
      $segments = explode('/', $path);
      $id_masjid = end($segments);

      $url2 = $mapLink;
      parse_str(parse_url($url2, PHP_URL_QUERY), $params);

      $latitude = $params['query'];
      $longitude = explode(',', $latitude)[1];
      $latitude = explode(',', $latitude)[0];

      // transform the product data into an associative array 
      $productData = array(
        "url" => $url,
        "image" => $image,
        "name" => $name,
        "address" => $address,
        "mapLink" => $mapLink,
        "id_masjid" => $id_masjid,
        "latitude" => $latitude,
        "longitude" => $longitude,
        // "price" => $price 
      );

      $productDataList[] = $productData;

      $sqlHelpful = "SELECT *
          FROM tbl_mosque
          WHERE id='$id_masjid'
          ";
      $resultHelpful = $this->dbHandler->getDataAll($sqlHelpful);
      if (($resultHelpful->rowCount()) == 0) {

        $sqlInsert = "INSERT INTO tbl_mosque SET
            id='$id_masjid',
            `name`='" . addslashes($name) . "',
            `address`='" . addslashes($address) . "',
            `url`='" . addslashes($url) . "',
            `mapLink`='" . addslashes($mapLink) . "',
            `image`='" . addslashes($image) . "',
            `lat`='" . addslashes($latitude) . "',
            `lng`='" . addslashes($longitude) . "',
            created_at='$created_at';
            ";

        $result = $this->dbHandler->insertDataAll($sqlInsert);
        if ($result) {
        }
      }
    }
    // print_r($productDataList);

    // Transformasikan $productDataList menjadi JSON
    $jsonData = json_encode($productDataList, JSON_PRETTY_PRINT);

    // Tampilkan JSON
    echo $jsonData;

    ######################

    // // iterate over all "/shop/page/X" pages and retrieve all product data 
    // for ($paginationNumber = 1; $paginationNumber <=$highestPaginationNumber; $paginationNumber++) { 
    // 	$curl = curl_init(); 
    // 	curl_setopt($curl, CURLOPT_URL, "https://simas.kemenag.go.id/page/search/masjid/4/85/0/0/?p=$paginationNumber"); 
    // 	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
    // 	$pageHtml = curl_exec($curl); 
    // 	curl_close($curl); 

    // 	$paginationHtmlDomParser = HtmlDomParser::str_get_html($pageHtml); 

    // 	// scraping logic... 
    // }
    // print_r($paginationHtmlDomParser);

    // echo json_encode($kegiatanList, JSON_PRETTY_PRINT);


  }

  public function scripingMasjidDetail(Request $request, Response $response, array $parm)
  {

    $arr1 = array();

    $created_at = date('Y-m-d H:i:s');

    $sqlCheck = "SELECT *
        FROM tbl_mosque
        -- WHERE xd IS NULL
        WHERE xd ='1'
        AND id='46167'
        LIMIT 10
        ";
    $resultCheck = $this->dbHandler->getDataAll($sqlCheck);
    if (($resultCheck->rowCount()) > 0) {

      while ($data_rowCheck = $resultCheck->fetch(PDO::FETCH_ASSOC)) {

        // $id_masjid="65021";
        $id_masjid = $data_rowCheck["id"];

        $curl = curl_init();
        $durl = "https://simas.kemenag.go.id//profil//masjid//$id_masjid";
        // $durl="https://simas.kemenag.go.id/profil/masjid/3912";
        // $durl="https://simas.kemenag.go.id/profil/masjid/12751";
        curl_setopt($curl, CURLOPT_URL, $durl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        $html = curl_exec($curl);
        curl_close($curl);

        // initialize HtmlDomParser 
        $htmlDomParser = HtmlDomParser::str_get_html($html);
        // print_r($htmlDomParser);

        ######################


        // Cari elemen dengan class "search-result-item"
        $results = $htmlDomParser->find('.masjid-title');
        $name = $htmlDomParser->find('h1', 0)->plaintext;
        $tipologi = $htmlDomParser->find('.badge-info', 0)->plaintext;
        $no_id = $htmlDomParser->find('.masjid-card', 0)->find('a', 0)->plaintext;
        $didirikan = $htmlDomParser->find('.masjid-alamat-calendar', 0)->plaintext;
        $address = $htmlDomParser->find('.masjid-alamat-location', 0)->find('p', 0)->plaintext;
        $url_peta = $htmlDomParser->find('.masjid-alamat-location', 0)->find('a', 0)->href;
        $url_googlemap = $htmlDomParser->find('.masjid-alamat-location', 0)->find('a', 1)->href;
        $jlh_pengurus = $htmlDomParser->find('.summary-item', 0)->find('p', 0)->plaintext;
        $jlh_imam = $htmlDomParser->find('.summary-item', 1)->find('p', 0)->plaintext;
        $jlh_khatib = $htmlDomParser->find('.summary-item', 2)->find('p', 0)->plaintext;
        $jlh_muazin = $htmlDomParser->find('.summary-item', 3)->find('p', 0)->plaintext;
        $jlh_remaja_masjid = $htmlDomParser->find('.summary-item', 4)->find('p', 0)->plaintext;
        $luas_tanah = $htmlDomParser->find('.section-content-info-wrapper', 0)->find('.row', 1)->find('.row', 0)->find('.label', 1)->plaintext;
        $status_tanah = $htmlDomParser->find('.section-content-info-wrapper', 0)->find('.row', 2)->find('.row', 0)->find('.label', 1)->plaintext;
        $luas_bangunan = $htmlDomParser->find('.section-content-info-wrapper', 0)->find('.row', 3)->find('.row', 0)->find('.label', 1)->plaintext;
        $daya_tampung_jamaah = $htmlDomParser->find('.section-content-info-wrapper', 0)->find('.row', 4)->find('.row', 0)->find('.label', 1)->plaintext;
        $sejarah_masjid = $htmlDomParser->find('.masjid-sejarah', 0)->innertext;
        $phone = $htmlDomParser->find('.masjid-alamat-phone', 0)->find('p', 0)->plaintext;
        $email = $htmlDomParser->find('.masjid-alamat-phone', 1)->find('p', 0)->plaintext;
        $web = $htmlDomParser->find('.masjid-alamat-phone', 2)->find('p', 0)->plaintext;



        // Array untuk menyimpan hasil iterasi
        $fasilitasUmumList = array();
        $kegiatanList = array();
        $ramahAnakList = array();
        $disabilitasList = array();

        // Iterasi elemen Fasilitas Umum
        $fasilitasUmumElements = $htmlDomParser->find('.section-content-info-wrapper h4:contains("Fasilitas Umum") + div.row div.masjid-item');
        foreach ($fasilitasUmumElements as $element) {
          $fasilitasUmumList[] = $element->find('p', 0)->text;
        }

        // Iterasi elemen Kegiatan
        $kegiatanElements = $htmlDomParser->find('.section-content-info-wrapper h4:contains("Kegiatan") + div.row div.masjid-item');
        foreach ($kegiatanElements as $element) {
          $kegiatanList[] = $element->find('p', 0)->text;
        }

        // Iterasi elemen ramah anak
        $ramahAnakElements = $htmlDomParser->find('.section-content-info-wrapper h4:contains("Fasilitas Ramah Anak") + div.row div.masjid-item');
        foreach ($ramahAnakElements as $element) {
          $ramahAnakList[] = $element->find('p', 0)->text;
        }

        // Iterasi elemen Disabilitas
        $disabilitasElements = $htmlDomParser->find('.section-content-info-wrapper h4:contains("Fasilitas Disabilitas") + div.row div.masjid-item');
        foreach ($disabilitasElements as $element) {
          $disabilitasList[] = $element->find('p', 0)->text;
        }

        $sqlHelpful = "SELECT *
          FROM tbl_mosque
          WHERE id='$id_masjid'
          ";
        $resultHelpful = $this->dbHandler->getDataAll($sqlHelpful);
        if (($resultHelpful->rowCount()) > 0) {

          echo $no_id;

          // $sqlInsert = "UPDATE tbl_mosque SET 
          // no_id = '".addslashes($no_id)."',
          // address = '".addslashes($address)."',
          // typology = '".addslashes($tipologi)."',
          // since = '".addslashes($didirikan)."',
          // number_administrators = '".addslashes($jlh_pengurus)."',
          // number_imam = '".addslashes($jlh_imam)."',
          // number_khatib = '".addslashes($jlh_khatib)."',
          // number_muadzin = '".addslashes($jlh_muazin)."',
          // number_mosque_youth = '".addslashes($jlh_remaja_masjid)."',
          // surface_area = '".addslashes($luas_tanah)."',
          // land_status = '".addslashes($status_tanah)."',
          // building_area = '".addslashes($luas_bangunan)."',
          // capacity = '".addslashes($daya_tampung_jamaah)."',
          // historical = '".addslashes($sejarah_masjid)."',
          // phone = '".addslashes($phone)."',
          // email = '".addslashes($email)."',
          // web = '".addslashes($web)."',
          // public_facilities = '".json_encode($fasilitasUmumList)."',
          // activity = '".json_encode($kegiatanList)."',
          // child_friendly = '".json_encode($ramahAnakList)."',
          // disability = '".json_encode($disabilitasList)."',
          // xd = '2',
          // updated_at_='$created_at'
          // WHERE id='$id_masjid';
          // ";

          $sqlInsert = "UPDATE tbl_mosque SET 
            -- address2 = '" . addslashes($address) . "',
            address2 = '$address',
            
            xd = '2',
            updated_at_='$created_at'
            WHERE id='$id_masjid';
            ";

          $result = $this->dbHandler->insertDataAll($sqlInsert);
          if ($result) {
            echo "succes update $address";
          }
        }
      }
    }
  }

  public function scripingMasjidDetail2(Request $request, Response $response, array $parm)
  {

    $arr1 = array();

    $created_at = date('Y-m-d H:i:s');

    $sqlCheck = "SELECT *
        FROM tbl_mosque
        -- WHERE xd IS NULL
        WHERE xd ='1'
        -- AND id='46167'
        LIMIT 10
        ";
    $resultCheck = $this->dbHandler->getDataAll($sqlCheck);
    if (($resultCheck->rowCount()) > 0) {

      while ($data_rowCheck = $resultCheck->fetch(PDO::FETCH_ASSOC)) {

        // $id_masjid="65021";
        $id_masjid = $data_rowCheck["id"];

        $curl = curl_init();
        $durl = "https://simas.kemenag.go.id//profil//masjid//$id_masjid";
        // $durl="https://simas.kemenag.go.id/profil/masjid/3912";
        // $durl="https://simas.kemenag.go.id/profil/masjid/12751";
        curl_setopt($curl, CURLOPT_URL, $durl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        $html = curl_exec($curl);
        curl_close($curl);

        // initialize HtmlDomParser 
        $htmlDomParser = HtmlDomParser::str_get_html($html);
        // print_r($htmlDomParser);

        ######################


        // Cari elemen dengan class "search-result-item"
        $results = $htmlDomParser->find('.masjid-title');
        $name = $htmlDomParser->find('h1', 0)->plaintext;
        $tipologi = $htmlDomParser->find('.badge-info', 0)->plaintext;
        $no_id = $htmlDomParser->find('.masjid-card', 0)->find('a', 0)->plaintext;
        $didirikan = $htmlDomParser->find('.masjid-alamat-calendar', 0)->plaintext;
        $address = $htmlDomParser->find('.masjid-alamat-location', 0)->find('p', 0)->plaintext;
        $url_peta = $htmlDomParser->find('.masjid-alamat-location', 0)->find('a', 0)->href;
        $url_googlemap = $htmlDomParser->find('.masjid-alamat-location', 0)->find('a', 1)->href;
        $jlh_pengurus = $htmlDomParser->find('.summary-item', 0)->find('p', 0)->plaintext;
        $jlh_imam = $htmlDomParser->find('.summary-item', 1)->find('p', 0)->plaintext;
        $jlh_khatib = $htmlDomParser->find('.summary-item', 2)->find('p', 0)->plaintext;
        $jlh_muazin = $htmlDomParser->find('.summary-item', 3)->find('p', 0)->plaintext;
        $jlh_remaja_masjid = $htmlDomParser->find('.summary-item', 4)->find('p', 0)->plaintext;
        $luas_tanah = $htmlDomParser->find('.section-content-info-wrapper', 0)->find('.row', 1)->find('.row', 0)->find('.label', 1)->plaintext;
        $status_tanah = $htmlDomParser->find('.section-content-info-wrapper', 0)->find('.row', 2)->find('.row', 0)->find('.label', 1)->plaintext;
        $luas_bangunan = $htmlDomParser->find('.section-content-info-wrapper', 0)->find('.row', 3)->find('.row', 0)->find('.label', 1)->plaintext;
        $daya_tampung_jamaah = $htmlDomParser->find('.section-content-info-wrapper', 0)->find('.row', 4)->find('.row', 0)->find('.label', 1)->plaintext;
        $sejarah_masjid = $htmlDomParser->find('.masjid-sejarah', 0)->innertext;
        $phone = $htmlDomParser->find('.masjid-alamat-phone', 0)->find('p', 0)->plaintext;
        $email = $htmlDomParser->find('.masjid-alamat-phone', 1)->find('p', 0)->plaintext;
        $web = $htmlDomParser->find('.masjid-alamat-phone', 2)->find('p', 0)->plaintext;



        // Array untuk menyimpan hasil iterasi
        $fasilitasUmumList = array();
        $kegiatanList = array();
        $ramahAnakList = array();
        $disabilitasList = array();

        // Iterasi elemen Fasilitas Umum
        $fasilitasUmumElements = $htmlDomParser->find('.section-content-info-wrapper h4:contains("Fasilitas Umum") + div.row div.masjid-item');
        foreach ($fasilitasUmumElements as $element) {
          $fasilitasUmumList[] = $element->find('p', 0)->text;
        }

        // Iterasi elemen Kegiatan
        $kegiatanElements = $htmlDomParser->find('.section-content-info-wrapper h4:contains("Kegiatan") + div.row div.masjid-item');
        foreach ($kegiatanElements as $element) {
          $kegiatanList[] = $element->find('p', 0)->text;
        }

        // Iterasi elemen ramah anak
        $ramahAnakElements = $htmlDomParser->find('.section-content-info-wrapper h4:contains("Fasilitas Ramah Anak") + div.row div.masjid-item');
        foreach ($ramahAnakElements as $element) {
          $ramahAnakList[] = $element->find('p', 0)->text;
        }

        // Iterasi elemen Disabilitas
        $disabilitasElements = $htmlDomParser->find('.section-content-info-wrapper h4:contains("Fasilitas Disabilitas") + div.row div.masjid-item');
        foreach ($disabilitasElements as $element) {
          $disabilitasList[] = $element->find('p', 0)->text;
        }

        $sqlHelpful = "SELECT *
          FROM tbl_mosque
          WHERE id='$id_masjid'
          ";
        $resultHelpful = $this->dbHandler->getDataAll($sqlHelpful);
        if (($resultHelpful->rowCount()) > 0) {

          echo $no_id;

          // $sqlInsert = "UPDATE tbl_mosque SET 
          // no_id = '".addslashes($no_id)."',
          // address = '".addslashes($address)."',
          // typology = '".addslashes($tipologi)."',
          // since = '".addslashes($didirikan)."',
          // number_administrators = '".addslashes($jlh_pengurus)."',
          // number_imam = '".addslashes($jlh_imam)."',
          // number_khatib = '".addslashes($jlh_khatib)."',
          // number_muadzin = '".addslashes($jlh_muazin)."',
          // number_mosque_youth = '".addslashes($jlh_remaja_masjid)."',
          // surface_area = '".addslashes($luas_tanah)."',
          // land_status = '".addslashes($status_tanah)."',
          // building_area = '".addslashes($luas_bangunan)."',
          // capacity = '".addslashes($daya_tampung_jamaah)."',
          // historical = '".addslashes($sejarah_masjid)."',
          // phone = '".addslashes($phone)."',
          // email = '".addslashes($email)."',
          // web = '".addslashes($web)."',
          // public_facilities = '".json_encode($fasilitasUmumList)."',
          // activity = '".json_encode($kegiatanList)."',
          // child_friendly = '".json_encode($ramahAnakList)."',
          // disability = '".json_encode($disabilitasList)."',
          // xd = '2',
          // updated_at_='$created_at'
          // WHERE id='$id_masjid';
          // ";

          $sqlInsert = "UPDATE tbl_mosque SET 
            address2 = '" . addslashes($address) . "',
            
            
            xd = '2',
            updated_at_='$created_at'
            WHERE id='$id_masjid';
            ";

          $result = $this->dbHandler->insertDataAll($sqlInsert);
          if ($result) {
            echo "succes update $address";
          }
        }
      }
    }
  }

  public function getCurrency(Request $request, Response $response, array $parm)
  {

    $arr1 = array();
    $APIKEY = "XeZ6FKgb1U9JJDDUgAJohOvKK5zMTOrpleN0UDI3";

    $curl = curl_init();
    $durl = "https://api.freecurrencyapi.com/v1/latest?apikey=$APIKEY";
    // $durl="https://simas.kemenag.go.id/profil/masjid/3912";
    // $durl="https://simas.kemenag.go.id/profil/masjid/12751";
    curl_setopt($curl, CURLOPT_URL, $durl);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    $resp = curl_exec($curl);
    curl_close($curl);

    // var_dump($resp);

    $data = json_decode($resp, true);
    foreach ($data['data'] as $currency => $value) {
      echo "Currency: $currency, Value: $value\n";
    }
  }

  public function getCurrency_(Request $request, Response $response)
  {

    // $APIKEY="XeZ6FKgb1U9JJDDUgAJohOvKK5zMTOrpleN0UDI3";

    // $endpoint = "https://api.freecurrencyapi.com/v1/latest?apikey=$APIKEY";

    // $transaction = $this->utilHelper->curlConnect($endpoint, "");
    // printf($transaction);

    // $data_api = array();


    // if ($transaction != null) {

    //     $json_api = json_decode($transaction, true);
    //     if (!empty($json_api['data'])) {
    //         // printf($transaction);

    //         $data_api = $json_api['data'];
    //         return $this->customResponse->is200Response($response, $data_api);
    //     } else {
    //         return $this->customResponse->is404Response($response, $data_api);
    //     }
    // } else {
    //     return $this->customResponse->is404Response($response, $data_api);
    // }

    $transaction = '
        {
          "data": {
              "AUD": 1.454759,
              "BGN": 1.787058,
              "BRL": 4.813655,
              "CAD": 1.320052,
              "CHF": 0.894401,
              "CNY": 7.126611,
              "CZK": 21.77803,
              "DKK": 6.80951,
              "EUR": 0.914052,
              "GBP": 0.779451,
              "HKD": 7.821454,
              "HRK": 6.886923,
              "HUF": 341.31039,
              "IDR": 14935.018058,
              "ILS": 3.557772,
              "INR": 81.915149,
              "ISK": 136.470223,
              "JPY": 141.872199,
              "KRW": 1277.311425,
              "MXN": 17.081922,
              "MYR": 4.614505,
              "NOK": 10.568011,
              "NZD": 1.605396,
              "PHP": 55.715066,
              "PLN": 4.077052,
              "RON": 4.532109,
              "RUB": 83.50015,
              "SEK": 10.644712,
              "SGD": 1.337702,
              "THB": 34.690046,
              "TRY": 23.553538,
              "USD": 1,
              "ZAR": 18.214624
          }
        }
        ';

    // $APIKEY="XeZ6FKgb1U9JJDDUgAJohOvKK5zMTOrpleN0UDI3";

    // $endpoint = "https://api.freecurrencyapi.com/v1/latest?apikey=$APIKEY";

    // $transaction = $this->utilHelper->curlConnect($endpoint, "");

    $currency = "IDR";
    $currency = "USD";
    $currency = "MYR";

    $json_api = json_decode($transaction, true);
    if (!empty($json_api['data'][$currency]) && !empty($json_api['data']['IDR'])) {
      $data = $json_api['data'];
      echo "ada";
      $value = $data['IDR'] / $data[$currency];
      echo $value;
      echo "</br>";
      // echo round(14935.014058,2);
      echo round($value, 2);
      echo "</br>";
      echo (float) round($value, 2);
    } else {
      echo "tdak ada";
    }
    // $idrValue = $json_api['data']['IDR'];

    // // printf($jjs);
    // echo $idrValue;

  }

  public function getMe(Request $request, Response $response)
  {

    $user_id = $request->getAttribute('user_id');
    // return $response->withJson(['user_id' => $user_id]);

    // $user_id="";

    // return $user_id;
    return $this->customResponse->is200Response($response, $user_id);
  }
}
