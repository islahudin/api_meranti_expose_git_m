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



class RestoController
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

    public function getRestoPg(Request $request, Response $response)
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
            $q_filter = " AND ((tbl_resto.name LIKE '%$q%') OR (tbl_adm_district.`name` LIKE '%$q%'))";
        } else {
            $q_filter = "";
        }

        $per_page = min(max($per_page, 5), 20);

        // Calculate the offset
        $offset = ($page - 1) * $per_page;

        // Fetch the total records and paginated results
        $sql = "SELECT tbl_resto.id, COUNT(*) OVER() AS total_records, tbl_resto.name AS title, tbl_resto.slug, tbl_resto.lat, tbl_resto.lng, tbl_resto.subtitle, tbl_resto.phone, tbl_resto.rating_set, 
        tbl_resto.service_options, tbl_resto.accessibility, tbl_resto.offer, tbl_resto.food_choices, tbl_resto.facility, tbl_resto.atmosphere, tbl_resto.type_visitor, 
        tbl_resto.planning, tbl_resto.payment, tbl_resto.address, 
        tbl_adm_country.`name` AS country, tbl_adm_province.`name` AS province, tbl_adm_regency.`nickname` AS regency, tbl_adm_district.`name` AS district,
        COALESCE((SELECT img FROM tbl_resto_image WHERE id_resto=tbl_resto.id AND `status` ='1' ORDER BY tbl_resto_image.sort ASC LIMIT 1),'') AS img, 
        COALESCE((SELECT COUNT(id) FROM tbl_like_all WHERE liked='1' AND id_ref=tbl_resto.id),0 )AS count_liked, 
        COALESCE(round(SQRT(
        POW(111.111 * (tbl_resto.lat - ('$lat')), 2) +
        POW(111.111 * (('$lng') - tbl_resto.lng) *
        COS(tbl_resto.lat / 57.3), 2)), 1),'0') as distance_in_km
    
        FROM tbl_resto
    
        JOIN tbl_adm_country ON tbl_resto.id_country=tbl_adm_country.id
        JOIN tbl_adm_province ON tbl_resto.id_province=tbl_adm_province.id
        JOIN tbl_adm_regency ON tbl_resto.id_regency=tbl_adm_regency.id
        JOIN tbl_adm_district ON tbl_resto.id_district=tbl_adm_district.id
        WHERE 1
        AND tbl_resto.`status`='1'
        AND tbl_resto.id_regency='1410' 

        $q_filter
        $district_filter
        
        GROUP BY tbl_resto.id
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
                    'rating' => (float)$data_row["rating_set"],
                    'slug' => $data_row["slug"],
                    'link' =>  url_link_event() . "" . $data_row["slug"] . "",
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

    public function viewResto(Request $request, Response $response, array $parm)
    {

        $id_user = CustomRequestHandler::getParam($request, "id_user",'');

        // $api_gmaps = constant('API_GMAPS');
        $api_gmaps = API_GMAPS;
        $token_mapbox = TOKEN_MAPBOX;
        $current_day = date("l");

        $arr1 = null;

        $sql = "SELECT tresto.id AS id_resto, tresto.name AS title,
        tresto.subtitle,
        tresto.address,tresto.lat,tresto.lng, tresto.slug, tresto.rating_set,
        tresto.phone, tresto.service_options, tresto.accessibility, tresto.offer,
        tresto.food_choices, tresto.facility, tresto.atmosphere, tresto.type_visitor,
        tresto.planning, tresto.payment,
        tresto.min,tresto.sen,tresto.sel,tresto.rab,tresto.kam,tresto.jum,tresto.sab,
				
        (SELECT COALESCE(round((SUM(tbl_tour_review.rating)/COUNT(tbl_tour_review.id)),1),0)
        FROM tbl_tour_review
        JOIN tbl_tour ON tbl_tour_review.id_tour=tbl_tour.id
        WHERE 1
        AND tbl_tour_review.id_tour=tresto.id
        AND tbl_tour_review.`status`='1') AS rating,

        (SELECT COALESCE(COUNT(tbl_tour_review.id))
        FROM tbl_tour_review
        JOIN tbl_tour ON tbl_tour_review.id_tour=tbl_tour.id
        WHERE 1
        AND tbl_tour_review.id_tour=tresto.id
        AND tbl_tour_review.`status`='1') AS total_review,

        COALESCE((SELECT tbl_like_all.liked FROM tbl_like_all WHERE id_user='$id_user' AND id_ref=tresto.id),0 )AS liked,
        COALESCE((SELECT COUNT(*) FROM tbl_like_all WHERE liked='1' AND id_ref=tresto.id),0 )AS count_liked,
        COALESCE((SELECT COUNT(tbl_tour_review_image.id)
        FROM tbl_tour_review
        JOIN tbl_tour_review_image ON tbl_tour_review_image.id_tour_review=tbl_tour_review.id
        JOIN tbl_tour ON tbl_tour_review.id_tour=tbl_tour.id
        WHERE 1
        AND tbl_tour_review.id_tour=tresto.id ),0 )AS total_gallery,
        tbl_adm_country.`name` AS country, tbl_adm_country.`name` AS country_slug,
        tbl_adm_province.`name` AS province, tbl_adm_province.`name` AS province_slug,
        tbl_adm_regency.`nickname` AS regency, tbl_adm_regency.`nickname` AS regency_slug,
        tbl_adm_district.`name` AS district, tbl_adm_district.`name` AS district_slug

        FROM tbl_resto tresto
        JOIN tbl_adm_country ON tresto.id_country=tbl_adm_country.id
        JOIN tbl_adm_province ON tresto.id_province=tbl_adm_province.id
        JOIN tbl_adm_regency ON tresto.id_regency=tbl_adm_regency.id
        JOIN tbl_adm_district ON tresto.id_district=tbl_adm_district.id
        WHERE 1
        AND tresto.`status`='1'
        AND (tresto.slug='$parm[slug]')
        GROUP BY tresto.id
        ";

        $result = $this->dbHandler->getDataAll($sql);
        if (($result->rowCount()) > 0
        ) {

            $data_row = $result->fetch(PDO::FETCH_ASSOC);
            $id_resto = $data_row["id_resto"];

            $sql2 = "SELECT * FROM tbl_resto_image WHERE id_resto='$id_resto' AND `status` ='1' ORDER BY sort ASC";

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


            // Define the attributes to check
            $attributes = [
                "opsi_layanan" => $data_row["service_options"],
                "aksesibilitas" => $data_row["accessibility"],
                "menawarkan" => $data_row["offer"],
                "pilihan_makanan" => $data_row["food_choices"],
                "fasilitas" => $data_row["facility"],
                "suasana" => $data_row["atmosphere"],
                "type_visitor" => $data_row["type_visitor"],
                "perencanaan" => $data_row["planning"],
                "pembayaran" => $data_row["payment"],
            ];

            $formattedOutput = [];

            // Loop through each attribute, check if it's not empty, and format accordingly
            foreach ($attributes as $key => $value) {
                $content = json_decode($value, true); // Decode JSON string to array

                // Only add to output if content is not empty or an empty array
                if (!empty($content)) {
                    $formattedOutput[] = [
                        "name" => $key,
                        "content" => $content
                    ];
                }
            }

            // $maps_img=maps_static($data_row["lat"], $data_row["lng"], $api_gmaps);
            $maps_img = maps_static_mapbox($data_row["lat"], $data_row["lng"], $token_mapbox);

            $arr1 = array(
                'id' => "" . $data_row["id_resto"] . "",
                'title' => "" . $data_row["title"] . "",
                'subtitle' => "" . $data_row["subtitle"] . "",
                'slug' => "" . $data_row["slug"] . "",
                'country' => "" . $data_row["country"] . "",
                'phone' => "" . $data_row["phone"] . "",
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
                'features' => $formattedOutput,


            );

            return $this->customResponse->is200Response($response, $arr1);
        } else {
            // code...
            return $this->customResponse->is404Response($response, "faild");
        }
    }
}
