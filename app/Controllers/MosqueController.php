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



class MosqueController
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

    public function getMosquePg(Request $request, Response $response)
    {
        $total = 0;
        $page = 0;
        $pages = 0;

        $arr1 = array();

        $page = CustomRequestHandler::getParam($request, "page", 1);
        $per_page = CustomRequestHandler::getParam($request, "per_page", 1);
        $lat = CustomRequestHandler::getParam($request, "lat", '');
        $lng = CustomRequestHandler::getParam($request, "lng", '');
        $district = CustomRequestHandler::getParam($request, "district",'');
        $q = CustomRequestHandler::getParam($request, "q",'');

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
            $q_filter = " AND ((tbl_mosque.name LIKE '%$q%') OR (tbl_adm_district.`name` LIKE '%$q%'))";
        } else {
            $q_filter = "";
        }

        if (!empty($lat) && !empty($lng)) {
            $nearby_filter = " HAVING distance_in_km <> 0 AND distance_in_km <= 5
            ORDER BY 
            distance_in_km ASC";
        } else {
            $nearby_filter = "";
        }

        $per_page = min(max($per_page, 5), 20);

        // Calculate the offset
        $offset = ($page - 1) * $per_page;

        // Fetch the total records and paginated results
        $sql = "SELECT tbl_mosque.id, COUNT(*) OVER() AS total_records, tbl_mosque.name AS title, tbl_mosque.lat, tbl_mosque.lng, tbl_mosque.address, tbl_mosque.address_detail,
        tbl_mosque.image AS img_profile, tbl_mosque.no_id, tbl_mosque.typology, tbl_mosque.since, tbl_mosque.number_administrators, tbl_mosque.number_imam, tbl_mosque.number_khatib, tbl_mosque.number_muadzin,
        tbl_mosque.number_mosque_youth, tbl_mosque.surface_area, tbl_mosque.land_status, tbl_mosque.building_area, tbl_mosque.capacity, tbl_mosque.historical, tbl_mosque.phone, tbl_mosque.email,
        tbl_mosque.web, tbl_mosque.public_facilities, tbl_mosque.activity, tbl_mosque.child_friendly, tbl_mosque.disability,
        tbl_adm_country.`name` AS country, tbl_adm_province.`name` AS province, tbl_adm_regency.`nickname` AS regency, tbl_adm_district.`name` AS district,
        COALESCE((SELECT img FROM tbl_mosque_image WHERE id_mosque=tbl_mosque.id ORDER BY tbl_mosque_image.sort ASC LIMIT 1),'') AS img, 
        COALESCE((SELECT COUNT(id) FROM tbl_like_all WHERE liked='1' AND id_ref=tbl_mosque.id),0 )AS count_liked, 
        COALESCE(round(SQRT(
        POW(111.111 * (tbl_mosque.lat - ('$lat')), 2) +
        POW(111.111 * (('$lng') - tbl_mosque.lng) *
        COS(tbl_mosque.lat / 57.3), 2)), 1),'0') as distance_in_km
    
        FROM tbl_mosque
    
        JOIN tbl_adm_country ON tbl_mosque.id_country=tbl_adm_country.id
        JOIN tbl_adm_province ON tbl_mosque.id_province=tbl_adm_province.id
        JOIN tbl_adm_regency ON tbl_mosque.id_regency=tbl_adm_regency.id
        JOIN tbl_adm_district ON tbl_mosque.id_district=tbl_adm_district.id
        WHERE 1
        AND tbl_mosque.`status`='1'
        AND tbl_mosque.id_regency='1410' 

        $q_filter
        $nearby_filter
        $district_filter
        
        -- GROUP BY tbl_mosque.id
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
                // $img = "$banner";
                $img = $data_row["img_profile"];

                $arr1[] = array(
                    'id' => "" . $data_row["id"] . "",
                    'title' => "" . clean_special_characters($data_row["title"]) . "",
                    'distance' => "" . $distance . "",
                    'count_liked' => (int) $data_row["count_liked"],
                    'address' => $data_row["address"],
                    'address_detail' => $data_row["address_detail"],
                    'no_id' => $data_row["no_id"],
                    'typology' => $data_row["typology"],
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

    public function viewMosque(Request $request, Response $response, array $parm)
    {

        $id_user = CustomRequestHandler::getParam($request, "id_user",'');

        // $api_gmaps = constant('API_GMAPS');
        $api_gmaps = API_GMAPS;
        $token_mapbox = TOKEN_MAPBOX;
        $current_day = date("l");

        $arr1 = null;

        $sql = "SELECT tmosque.id AS id_mosque, tmosque.name AS title,
        tmosque.no_id, tmosque.typology, tmosque.since, tmosque.number_administrators, tmosque.number_imam, tmosque.number_khatib,
        tmosque.number_muadzin, tmosque.number_mosque_youth, tmosque.surface_area, tmosque.land_status, tmosque.building_area, tmosque.capacity,
        tmosque.historical, tmosque.email, tmosque.web, tmosque.public_facilities, tmosque.activity, tmosque.child_friendly, tmosque.disability,
        tmosque.address,tmosque.lat,tmosque.lng,
        tmosque.phone, tmosque.image, tmosque.address_detail, 
				
        (SELECT COALESCE(round((SUM(tbl_tour_review.rating)/COUNT(tbl_tour_review.id)),1),0)
        FROM tbl_tour_review
        JOIN tbl_tour ON tbl_tour_review.id_tour=tbl_tour.id
        WHERE 1
        AND tbl_tour_review.id_tour=tmosque.id
        AND tbl_tour_review.`status`='1') AS rating,

        (SELECT COALESCE(COUNT(tbl_tour_review.id))
        FROM tbl_tour_review
        JOIN tbl_tour ON tbl_tour_review.id_tour=tbl_tour.id
        WHERE 1
        AND tbl_tour_review.id_tour=tmosque.id
        AND tbl_tour_review.`status`='1') AS total_review,

        COALESCE((SELECT tbl_like_all.liked FROM tbl_like_all WHERE id_user='$id_user' AND id_ref=tmosque.id),0 )AS liked,
        COALESCE((SELECT COUNT(*) FROM tbl_like_all WHERE liked='1' AND id_ref=tmosque.id),0 )AS count_liked,
        COALESCE((SELECT COUNT(tbl_tour_review_image.id)
        FROM tbl_tour_review
        JOIN tbl_tour_review_image ON tbl_tour_review_image.id_tour_review=tbl_tour_review.id
        JOIN tbl_tour ON tbl_tour_review.id_tour=tbl_tour.id
        WHERE 1
        AND tbl_tour_review.id_tour=tmosque.id ),0 )AS total_gallery,
        tbl_adm_country.`name` AS country, tbl_adm_country.`name` AS country_slug,
        tbl_adm_province.`name` AS province, tbl_adm_province.`name` AS province_slug,
        tbl_adm_regency.`nickname` AS regency, tbl_adm_regency.`nickname` AS regency_slug,
        tbl_adm_district.`name` AS district, tbl_adm_district.`name` AS district_slug

        FROM tbl_mosque tmosque
        JOIN tbl_adm_country ON tmosque.id_country=tbl_adm_country.id
        JOIN tbl_adm_province ON tmosque.id_province=tbl_adm_province.id
        JOIN tbl_adm_regency ON tmosque.id_regency=tbl_adm_regency.id
        JOIN tbl_adm_district ON tmosque.id_district=tbl_adm_district.id
        WHERE 1
        AND tmosque.`status`='1'
        AND ((tmosque.id='$parm[slug]'))
        GROUP BY tmosque.id
        ";

        $result = $this->dbHandler->getDataAll($sql);
        if (($result->rowCount()) > 0
        ) {

            $data_row = $result->fetch(PDO::FETCH_ASSOC);
            $id_mosque = $data_row["id_mosque"];

            $sql2 = "SELECT * FROM tbl_mosque_image WHERE id_mosque='$id_mosque' ORDER BY sort ASC";

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

            // Define the attributes to check
            $attributes = [
                "public_facilities" => $data_row["public_facilities"],
                "activity" => $data_row["activity"],
                "child_friendly" => $data_row["child_friendly"],
                "disability" => $data_row["disability"],
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
                'id' => "" . $data_row["id_mosque"] . "",
                'title' => "" . $data_row["title"] . "",
                'slug' => "",
                'image' => "" . $data_row["image"] . "",
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
                'maps_img' => $maps_img,
                // 'link' => url_link_expen()."" . $data_row["slug"]. "",
                'rating_set' => (float) $data_row["rating_set"],
                'link' => "",
                'liked' => (int) $data_row["liked"],
                'count_liked' => (int) $data_row["count_liked"],
                'rating' => "" . $data_row["rating"] . "",
                'total_review' => (int) $data_row["total_review"],

                'banner' => $arr2,
                'address_detail' => "" . $data_row["address_detail"] . "",
                'no_id' => "" . $data_row["no_id"] . "",
                'typology' => "" . $data_row["typology"] . "",
                'since' => "" . $data_row["since"] . "",
                'number_administrators' => (int) $data_row["number_administrators"],
                'number_imam' => (int) $data_row["number_imam"],
                'number_khatib' => (int) $data_row["number_khatib"],
                'number_muadzin' => (int) $data_row["number_muadzin"],
                'number_mosque_youth' => (int) $data_row["number_mosque_youth"],
                'surface_area' => "" . $data_row["surface_area"] . "",
                'land_status' => "" . $data_row["land_status"] . "",
                'building_area' => "" . $data_row["building_area"] . "",
                'capacity' => "" . $data_row["capacity"] . "",
                'historical' => "" . $data_row["historical"] . "",
                'phone' => "" . $data_row["phone"] . "",
                'email' => "" . $data_row["email"] . "",
                'web' => "" . $data_row["web"] . "",
                // 'public_facilities' => json_decode($data_row["public_facilities"]),
                // 'activity' => json_decode($data_row["activity"]),
                // 'child_friendly' => json_decode($data_row["child_friendly"]),
                // 'disability' => json_decode($data_row["disability"]),
                'features' => $formattedOutput,


            );

            return $this->customResponse->is200Response($response, $arr1);
        } else {
            // code...
            return $this->customResponse->is404Response($response, "faild");
        }
    }
}
