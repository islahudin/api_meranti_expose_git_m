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



class PublicPlaceController
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

    public function getPublicPlacePg(Request $request, Response $response, array $parm)
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
        $type = CustomRequestHandler::getParam($request, "type", '');
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

        if (stringContains($type, ',')) {
            // code...
            $_data = $type;
            // $arr_data = explode (",",$_data);
            // $imploded_data = implode("','",$arr_data);

            $new_arr = array_map('trim', explode(',', $_data));
            $imploded_data = implode("','", $new_arr);
            $district_filter = " AND tbl_public_place.type IN ('$imploded_data')";
        } else if (!empty($type)){
            // code...
            $type_filter = "AND tbl_public_place.type = '$type'";
        }


        if (!empty($q)) {
            $q_filter = " AND ((tbl_public_place.name LIKE '%$q%') OR (tbl_adm_district.`name` LIKE '%$q%'))";
        } else {
            $q_filter = "";
        }

        $per_page = min(max($per_page, 5), 20);

        // Calculate the offset
        $offset = ($page - 1) * $per_page;

        // Fetch the total records and paginated results
        $sql = "SELECT tbl_public_place.id, COUNT(*) OVER() AS total_records, tbl_public_place.name AS title, tbl_public_place.slug, tbl_public_place.lat, tbl_public_place.lng, tbl_public_place.subtitle, tbl_public_place.phone, tbl_public_place.rating_set, 
        tbl_public_place.service_options, tbl_public_place.accessibility, tbl_public_place.offer, tbl_public_place.food_choices, tbl_public_place.facility, tbl_public_place.atmosphere, tbl_public_place.type_visitor, 
        tbl_public_place.planning, tbl_public_place.payment, tbl_public_place.address, tbl_public_place.type,
        tbl_adm_country.`name` AS country, tbl_adm_province.`name` AS province, tbl_adm_regency.`nickname` AS regency, tbl_adm_district.`name` AS district,
        COALESCE((SELECT img FROM tbl_public_place_image 
        WHERE id_ref=tbl_public_place.place_id 
        AND `status` ='1' ORDER BY tbl_public_place_image.sort ASC LIMIT 1),'') AS img, 
        COALESCE((SELECT COUNT(id) FROM tbl_like_all WHERE liked='1' AND id_ref=tbl_public_place.id),0 )AS count_liked, 
        COALESCE(round(SQRT(
        POW(111.111 * (tbl_public_place.lat - ('$lat')), 2) +
        POW(111.111 * (('$lng') - tbl_public_place.lng) *
        COS(tbl_public_place.lat / 57.3), 2)), 1),'0') as distance_in_km
    
        FROM tbl_public_place
    
        JOIN tbl_adm_country ON tbl_public_place.id_country=tbl_adm_country.id
        JOIN tbl_adm_province ON tbl_public_place.id_province=tbl_adm_province.id
        JOIN tbl_adm_regency ON tbl_public_place.id_regency=tbl_adm_regency.id
        JOIN tbl_adm_district ON tbl_public_place.id_district=tbl_adm_district.id
        WHERE 1
        AND tbl_public_place.`status`='1'
        AND tbl_public_place.id_regency='1410' 

        $q_filter
        $district_filter
        $type_filter
        
        GROUP BY tbl_public_place.id
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
                    'type' => $data_row["type"],
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

    public function viewPublicPlace(Request $request, Response $response, array $parm)
    {

        $id_user = CustomRequestHandler::getParam($request, "id_user",'');

        // $api_gmaps = constant('API_GMAPS');
        $api_gmaps = API_GMAPS;
        $token_mapbox = TOKEN_MAPBOX;
        $current_day = date("l");

        $arr1 = null;

        $sql = "SELECT tplace.id, tplace.place_id, tplace.name AS title,
        tplace.subtitle, tplace.type,
        tplace.address,tplace.lat,tplace.lng, tplace.slug, tplace.rating_set,
        tplace.phone_number AS phone, 
        tplace.service_options, tplace.accessibility, tplace.offer,
        tplace.food_choices, tplace.facility, tplace.atmosphere, tplace.type_visitor,
        tplace.planning, tplace.payment,
        tplace.min,tplace.sen,tplace.sel,tplace.rab,tplace.kam,tplace.jum,tplace.sab,
				
        (SELECT COALESCE(round((SUM(tbl_tour_review.rating)/COUNT(tbl_tour_review.id)),1),0)
        FROM tbl_tour_review
        JOIN tbl_tour ON tbl_tour_review.id_tour=tbl_tour.id
        WHERE 1
        AND tbl_tour_review.id_tour=tplace.id
        AND tbl_tour_review.`status`='1') AS rating,

        (SELECT COALESCE(COUNT(tbl_tour_review.id))
        FROM tbl_tour_review
        JOIN tbl_tour ON tbl_tour_review.id_tour=tbl_tour.id
        WHERE 1
        AND tbl_tour_review.id_tour=tplace.id
        AND tbl_tour_review.`status`='1') AS total_review,

        COALESCE((SELECT tbl_like_all.liked FROM tbl_like_all WHERE id_user='$id_user' AND id_ref=tplace.id),0 )AS liked,
        COALESCE((SELECT COUNT(*) FROM tbl_like_all WHERE liked='1' AND id_ref=tplace.id),0 )AS count_liked,
        COALESCE((SELECT COUNT(tbl_tour_review_image.id)
        FROM tbl_tour_review
        JOIN tbl_tour_review_image ON tbl_tour_review_image.id_tour_review=tbl_tour_review.id
        JOIN tbl_tour ON tbl_tour_review.id_tour=tbl_tour.id
        WHERE 1
        AND tbl_tour_review.id_tour=tplace.id ),0 )AS total_gallery,
        tbl_adm_country.`name` AS country, tbl_adm_country.`name` AS country_slug,
        tbl_adm_province.`name` AS province, tbl_adm_province.`name` AS province_slug,
        tbl_adm_regency.`nickname` AS regency, tbl_adm_regency.`nickname` AS regency_slug,
        tbl_adm_district.`name` AS district, tbl_adm_district.`name` AS district_slug

        FROM tbl_public_place tplace
        JOIN tbl_adm_country ON tplace.id_country=tbl_adm_country.id
        JOIN tbl_adm_province ON tplace.id_province=tbl_adm_province.id
        JOIN tbl_adm_regency ON tplace.id_regency=tbl_adm_regency.id
        JOIN tbl_adm_district ON tplace.id_district=tbl_adm_district.id
        WHERE 1
        AND tplace.`status`='1'
        AND ((tplace.slug='$parm[slug]') || (tplace.id='$parm[slug]'))
        GROUP BY tplace.id
        ";

        $result = $this->dbHandler->getDataAll($sql);
        if (($result->rowCount()) > 0
        ) {

            $data_row = $result->fetch(PDO::FETCH_ASSOC);
            $place_id = $data_row["place_id"];

            $sql2 = "SELECT * FROM tbl_public_place_image WHERE id_ref='$place_id' AND `status` ='1' ORDER BY sort ASC";

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

            $arrReview=null;
            // REVIEW
            $sqlReview = "SELECT * FROM tbl_public_place_review WHERE place_id='$place_id' AND `status` ='1' ORDER BY `time` DESC";

            $resultReview = $this->dbHandler->getDataAll($sqlReview);
            if (($resultReview->rowCount()) > 0) {

                while ($data_rowReview = $resultReview->fetch(PDO::FETCH_ASSOC)) {

                    $arrReview[] = array(
                        'id' => "" . $data_rowReview["id"] . "",
                        'author_name' => "" . $data_rowReview["author_name"] . "",
                        'profile_photo_url' => "" . $data_rowReview["profile_photo_url"] . "",
                        'rating' => (float)$data_rowReview["rating"],
                        'relative_time_description' => "" . $data_rowReview["relative_time_description"] . "",
                        'text' => "" . $data_rowReview["text"] . "",
                        'time' => (int)$data_rowReview["time"],
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
                'id' => "" . $data_row["id"] . "",
                'title' => "" . $data_row["title"] . "",
                'subtitle' => "" . $data_row["subtitle"] . "",
                'slug' => "" . $data_row["slug"] . "",
                'type' => "" . $data_row["type"] . "",
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
                'review' => $arrReview,


            );

            return $this->customResponse->is200Response($response, $arr1);
        } else {
            // code...
            return $this->customResponse->is404Response($response, "faild");
        }
    }

}
