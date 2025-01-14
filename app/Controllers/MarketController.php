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
// $settings = require_once  __DIR__ . "/../../config/settings.php";



class MarketController
{

    protected  $customResponse;


    protected  $validator;
    protected  $conn;
    protected  $dbHandler;

    public function  __construct()
    {
        $this->customResponse = new CustomResponse();

        $this->validator = new Validator();
        $this->dbHandler = new DbHandler();
        date_default_timezone_set('Asia/Jakarta');
    }

    public function getMarketPg(Request $request, Response $response)
    {
        $total = 0;
        $page = 0;
        $pages = 0;

        $arr1 = array();

        $page = CustomRequestHandler::getParam($request, "page", 1);
        $per_page = CustomRequestHandler::getParam($request, "per_page", 1);
        $lat = CustomRequestHandler::getParam($request, "lat", '');
        $lng = CustomRequestHandler::getParam($request, "lng", '');
        $district = CustomRequestHandler::getParam($request, "district", '');
        $q = CustomRequestHandler::getParam($request, "q", '');
        
        if (stringContains($district, ',')) {
            // code...
            $_data = $district;
            // $arr_data = explode (",",$_data);
            // $imploded_data = implode("','",$arr_data);

            $new_arr = array_map('trim', explode(',', $_data));
            $imploded_data = implode("','", $new_arr);
            $district_filter = " AND tbl_adm_district.`name` IN ('$imploded_data')";
        } else if (!empty($district)){
            // code...
            $district_filter = "AND tbl_adm_district.`name` = '$district'";
        }


        if (!empty($q)) {
            $q_filter = " AND ((tbl_market.name LIKE '%$q%') OR (tbl_adm_district.`name` LIKE '%$q%'))";
        } else {
            $q_filter = "";
        }

        $per_page = min(max($per_page, 5), 20);

        // Calculate the offset
        $offset = ($page - 1) * $per_page;

        // Fetch the total records and paginated results
        $sql = "SELECT tbl_market.id, COUNT(*) OVER() AS total_records, tbl_market.name AS title, tbl_market.lat, tbl_market.lng, tbl_market.address,tbl_market.phone, tbl_market.rating_set,
        tbl_adm_country.`name` AS country, tbl_adm_province.`name` AS province, tbl_adm_regency.`nickname` AS regency, tbl_adm_district.`name` AS district,
        COALESCE((SELECT img FROM tbl_market_image WHERE id_market=tbl_market.id AND `status`='1' ORDER BY tbl_market_image.sort ASC LIMIT 1),'') AS img, 
        COALESCE((SELECT COUNT(id) FROM tbl_like_all WHERE liked='1' AND id_ref=tbl_market.id),0 )AS count_liked, 
        COALESCE(round(SQRT(
        POW(111.111 * (tbl_market.lat - ('$lat')), 2) +
        POW(111.111 * (('$lng') - tbl_market.lng) *
        COS(tbl_market.lat / 57.3), 2)), 1),'0') as distance_in_km
    
        FROM tbl_market
    
        JOIN tbl_adm_country ON tbl_market.id_country=tbl_adm_country.id
        JOIN tbl_adm_province ON tbl_market.id_province=tbl_adm_province.id
        JOIN tbl_adm_regency ON tbl_market.id_regency=tbl_adm_regency.id
        JOIN tbl_adm_district ON tbl_market.id_district=tbl_adm_district.id
        WHERE 1
        AND tbl_market.`status`='1'
        AND tbl_market.id_regency='1410' 

        $q_filter
        $district_filter
        
        GROUP BY tbl_market.id
        LIMIT $per_page OFFSET $offset
        ";

        $result = $this->dbHandler->getDataAll($sql);
        if ($result === false) {

            $rPagination["total"] = 0;
            $rPagination["page"] = (int)$page;
            $rPagination["pages"] = 0;
            $rPagination["per_page"] = (int)$per_page;
            return $this->customResponse->is500Response($response, "Query query error.");
        } else {
            $total = 0;
            while ($data_row = $result->fetch(PDO::FETCH_ASSOC)) {
                $total = (int)$data_row['total_records'];

                if (($lat != "") || ($lng != "")) {
                    $distance = $data_row["distance_in_km"];
                } else {
                    // code...
                    $distance = "0";
                }

                $id = $data_row["id"];
                $banner = img_banner_expen($data_row["img"]);
                $file_name = pathinfo($data_row["img"], PATHINFO_FILENAME);
                $filepath = "./../images/img_temp/" . $file_name . ".jpg";
                // $commp = compress_img($banner, $filepath, 50);
                // if ($commp == $banner) { //
                //     // code...
                //     $img = $commp;
                // } else {
                //     // code...
                //     $img = get_root_uri() . "images/img_temp/" . $commp;
                // }
                $img = "$banner";
                $img = $data_row["img"];

                $arr1[] = array(
                    'id' => "" . $data_row["id"] . "",
                    'title' => "" . clean_special_characters($data_row["title"]) . "",
                    'distance' => "" . $distance . "",
                    'count_liked' => (int) $data_row["count_liked"],
                    'subtitle' => $data_row["subtitle"],
                    'rating_set' => (float)$data_row["rating_set"],
                    'address' => $data_row["address"],
                    'country' => "" . $data_row["country"] . "",
                    'province' => "" . $data_row["province"] . "",
                    'regency' => "" . $data_row["regency"] . "",
                    'district' => "" . $data_row["district"] . "",
                    'lat' => (float)$data_row["lat"],
                    'lng' => (float)$data_row["lng"],
                    'img' => "" . $img,
                );
            }

            $pages = (int)ceil($total / $per_page);
        }

        $rPagination["total"] = $total;
        $rPagination["page"] = (int)$page;
        $rPagination["pages"] = $pages;
        $rPagination["per_page"] = (int)$per_page;

        return $this->customResponse->is200Response2($response, $arr1, rPagination: $rPagination);
    }

    public function viewMarket(Request $request, Response $response, array $parm)
    {

        $id_user = CustomRequestHandler::getParam($request, "id_user",'');

        // $api_gmaps = constant('API_GMAPS');
        $api_gmaps = API_GMAPS;
        $token_mapbox = TOKEN_MAPBOX;
        $current_day = date("l");

        $arr1 = null;

        $sql = "SELECT tmarket.id AS id_market, tmarket.name AS title,
        tmarket.address,tmarket.lat,tmarket.lng, tmarket.rating_set,
				tmarket.phone,
        tmarket.min,tmarket.sen,tmarket.sel,tmarket.rab,tmarket.kam,tmarket.jum,tmarket.sab,
				
        (SELECT COALESCE(round((SUM(tbl_tour_review.rating)/COUNT(tbl_tour_review.id)),1),0)
        FROM tbl_tour_review
        JOIN tbl_tour ON tbl_tour_review.id_tour=tbl_tour.id
        WHERE 1
        AND tbl_tour_review.id_tour=tmarket.id
        AND tbl_tour_review.`status`='1') AS rating,

        (SELECT COALESCE(COUNT(tbl_tour_review.id))
        FROM tbl_tour_review
        JOIN tbl_tour ON tbl_tour_review.id_tour=tbl_tour.id
        WHERE 1
        AND tbl_tour_review.id_tour=tmarket.id
        AND tbl_tour_review.`status`='1') AS total_review,

        COALESCE((SELECT tbl_like_all.liked FROM tbl_like_all WHERE id_user='$id_user' AND id_ref=tmarket.id),0 )AS liked,
        COALESCE((SELECT COUNT(*) FROM tbl_like_all WHERE liked='1' AND id_ref=tmarket.id),0 )AS count_liked,
        COALESCE((SELECT COUNT(tbl_tour_review_image.id)
        FROM tbl_tour_review
        JOIN tbl_tour_review_image ON tbl_tour_review_image.id_tour_review=tbl_tour_review.id
        JOIN tbl_tour ON tbl_tour_review.id_tour=tbl_tour.id
        WHERE 1
        AND tbl_tour_review.id_tour=tmarket.id ),0 )AS total_gallery,
        tbl_adm_country.`name` AS country, tbl_adm_country.`name` AS country_slug,
        tbl_adm_province.`name` AS province, tbl_adm_province.`name` AS province_slug,
        tbl_adm_regency.`nickname` AS regency, tbl_adm_regency.`nickname` AS regency_slug,
        tbl_adm_district.`name` AS district, tbl_adm_district.`name` AS district_slug

        FROM tbl_market tmarket
        JOIN tbl_adm_country ON tmarket.id_country=tbl_adm_country.id
        JOIN tbl_adm_province ON tmarket.id_province=tbl_adm_province.id
        JOIN tbl_adm_regency ON tmarket.id_regency=tbl_adm_regency.id
        JOIN tbl_adm_district ON tmarket.id_district=tbl_adm_district.id
        WHERE 1
        AND tmarket.`status`='1'
        AND (tmarket.id='$parm[slug]')
        
        GROUP BY tmarket.id
        ";

        $result = $this->dbHandler->getDataAll($sql);
        if (($result->rowCount()) > 0
        ) {

            $data_row = $result->fetch(PDO::FETCH_ASSOC);
            $id_market = $data_row["id_market"];

            $sql2 = "SELECT * FROM tbl_market_image WHERE id_market='$id_market' AND `status`='1' ORDER BY sort ASC";

            $result2 = $this->dbHandler->getDataAll($sql2);
            if (($result2->rowCount()) > 0) {

                while ($data_row2 = $result2->fetch(PDO::FETCH_ASSOC)) {

                    $_id = $data_row2["id"];
                    $banner = img_banner_expen($data_row2["img"]);
                    $file_name = pathinfo($data_row2["img"], PATHINFO_FILENAME);
                    $filepath = "./../images/img_temp/" . $file_name . ".jpg";
                    // $commp = compress_img($banner, $filepath, 50);
                    // if ($commp == $banner) {
                    //     // code...
                    //     $img = $commp;
                    // } else {
                    //     // code...
                    //     $img = get_root_uri() . "images/img_temp/" . $commp;
                    // }

                    $img = $banner;

                    $arr2[] = array(
                        'id' => "" . $data_row2["id"] . "",
                        'title' => "" . $data_row2["title"] . "",
                        'img' => "" . $img . "",
                        // 'img' => "" . $banner. "",
                    );
                }
            }

            // Full week schedule mapped to each day
            $week_schedule = [
                "minggu" => $data_row["min"],
                "senin" => $data_row["sen"],
                "selasa" => $data_row["sel"],
                "rabu" => $data_row["rab"],
                "kamis" => $data_row["kam"],
                "jumat" => $data_row["jum"],
                "sabtu" => $data_row["sab"]
            ];

        
            $opening_hours = getOpeningHours($week_schedule);

            // $maps_img=maps_static($data_row["lat"], $data_row["lng"], $api_gmaps);
            $maps_img = maps_static_mapbox($data_row["lat"], $data_row["lng"], $token_mapbox);

            $arr1 = array(
                'id' => "" . $data_row["id_market"] . "",
                'title' => "" . $data_row["title"] . "",
                'subtitle' => "Supermarket",
                'phone' => "" . $data_row["phone"] . "",
                'country' => "" . $data_row["country"] . "",
                'country_slug' => "" . $data_row["country_slug"] . "",
                'province' => "" . $data_row["province_slug"] . "",
                'regency' => "" . $data_row["regency"] . "",
                'regency_slug' => "" . $data_row["regency_slug"] . "",
                'district' => "" . $data_row["district"] . "",
                'district_slug' => "" . $data_row["district_slug"] . "",
                'address' => "" . $data_row["address"] . "",
                'lat' => (float)$data_row["lat"],
                'lng' => (float)$data_row["lng"],
                'opening_hours' => $opening_hours,
                'maps_img' => $maps_img,
                // 'link' => url_link_expen()."" . $data_row["slug"]. "",
                'rating_set' => (float) $data_row["rating_set"],
                'link' => "",
                'liked' => (int) $data_row["liked"],
                'count_liked' => (int) $data_row["count_liked"],
                'rating' => "" . $data_row["rating"] . "",
                'total_review' => (int) $data_row["total_review"],

                'banner' => $arr2,
                'features' => null,


            );

            return $this->customResponse->is200Response($response, $arr1);
        } else {
            // code...
            return $this->customResponse->is404Response($response, "faild");
        }
    }
}
