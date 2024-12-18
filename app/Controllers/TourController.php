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



class TourController
{

    protected $customResponse;


    protected $validator;
    protected $conn;
    protected $dbHandler;

    public function __construct()
    {
        $this->customResponse = new CustomResponse();

        $this->validator = new Validator();
        $this->dbHandler = new DbHandler();
        date_default_timezone_set('Asia/Jakarta');
    }

    public function getTourPg(Request $request, Response $response)
    {
        $total = 0;
        $page = 0;
        $pages = 0;

        $arr1 = array();

        $page = (int) (CustomRequestHandler::getParam($request, "page", 1));
        $per_page = (int) (CustomRequestHandler::getParam($request, "per_page", 1));
        $sort = (CustomRequestHandler::getParam($request, "sort", ''));
        $lat = (CustomRequestHandler::getParam($request, "lat", ''));
        $lng = (CustomRequestHandler::getParam($request, "lng", ''));
        $subcategory = (CustomRequestHandler::getParam($request, "subcategory", ''));
        $district = (CustomRequestHandler::getParam($request, "district", ''));
        $q = (CustomRequestHandler::getParam($request, "q", ''));

        if ($sort == "recommended") {
            // code...
            $addSqlSort = "ORDER BY count_liked DESC";
            // $addSqlSort="ORDER BY COALESCE((SELECT COUNT(id) FROM tbl_like_all WHERE liked='1' AND id_ref=tbl_event.id),0 ) DESC";
        } else if ($sort == "populer") {
            // code...
            $addSqlSort = "ORDER BY count_order DESC";
        } else {
            $addSqlSort = "ORDER BY tbl_tour.rating DESC";
        }

        if (stringContains($subcategory, ',')) {
            // code...
            $_data = $subcategory;
            // $arr_data = explode (",",$_data);
            // $imploded_data = implode("','",$arr_data);

            $new_arr = array_map('trim', explode(',', $_data));
            $imploded_data = implode("','", $new_arr);
            $subcategory_filter = " AND tbl_tour_subcategory.slug IN ('$imploded_data')";
        } else if (!empty($subcategory)) {
            // code...
            $subcategory_filter = "AND tbl_tour_subcategory.slug = '$subcategory'";
        }

        if (stringContains($district, ',')) {
            // code...
            $_data = $district;
            // $arr_data = explode (",",$_data);
            // $imploded_data = implode("','",$arr_data);

            $new_arr = array_map('trim', explode(',', $_data));
            $imploded_data = implode("','", $new_arr);
            $district_filter = " AND tbl_adm_district.`name` IN ('$imploded_data')";
        } else if (!empty($district)) {
            // code...
            $district_filter = "AND tbl_adm_district.`name` = '$district'";
        }


        if (!empty($q)) {
            $q_filter = " AND ((tbl_tour.title LIKE '%$q%') OR (tbl_adm_district.`name` LIKE '%$q%'))";
        } else {
            $q_filter = "";
        }

        $per_page = min(max($per_page, 5), 20);

        // Calculate the offset
        $offset = ($page - 1) * $per_page;

        // Fetch the total records and paginated results
        $sql = "SELECT tbl_tour.id AS id_tour, COUNT(*) OVER() AS total_records, tbl_tour.title, tbl_tour.slug, tbl_tour.lat, tbl_tour.lng, tbl_tour.rating, tbl_tour.detail_address AS address,
        tbl_adm_country.`name` AS country, tbl_adm_province.`name` AS province, tbl_adm_regency.`nickname` AS regency, tbl_adm_district.`name` AS district,
        COALESCE((SELECT img FROM tbl_tour_image WHERE id_tour=tbl_tour.id AND `status` ='1' ORDER BY tbl_tour_image.sort ASC LIMIT 1),'') AS img, 
        COALESCE((SELECT COUNT(id) FROM tbl_like_all WHERE liked='1' AND id_ref=tbl_tour.id),0 )AS count_liked, 
        COALESCE(round(SQRT(
        POW(111.111 * (tbl_tour.lat - ('$lat')), 2) +
        POW(111.111 * (('$lng') - tbl_tour.lng) *
        COS(tbl_tour.lat / 57.3), 2)), 1),'0') as distance_in_km
    
        FROM tbl_tour
    
        JOIN tbl_tour_subcategory ON  tbl_tour.id_tour_subcategory=tbl_tour_subcategory.id
        JOIN tbl_tour_category ON tbl_tour_subcategory.id_tour_category=tbl_tour_category.id
        JOIN tbl_adm_country ON tbl_tour.id_country=tbl_adm_country.id
        JOIN tbl_adm_province ON tbl_tour.id_province=tbl_adm_province.id
        JOIN tbl_adm_regency ON tbl_tour.id_regency=tbl_adm_regency.id
        JOIN tbl_adm_district ON tbl_tour.id_district=tbl_adm_district.id
        WHERE 1
        AND tbl_tour.`status`='1'
        AND tbl_tour.id_regency='1410' 
        $q_filter
        $subcategory_filter
        $district_filter
        
        GROUP BY tbl_tour.id
        $addSqlSort
        LIMIT $per_page OFFSET $offset
        ";

        $result = $this->dbHandler->getDataAll($sql);
        if ($result === false) {

            $rPagination["total"] = 0;
            $rPagination["page"] = (int) $page;
            $rPagination["pages"] = 0;
            $rPagination["per_page"] = (int) $per_page;
            return $this->customResponse->is500Response($response, "Query query error.");
        } else {
            $total = 0;
            while ($data_row = $result->fetch(PDO::FETCH_ASSOC)) {
                $total = (int) $data_row['total_records'];

                if (($lat != "") || ($lng != "")) {
                    $distance = $data_row["distance_in_km"];
                } else {
                    // code...
                    $distance = "0";
                }

                $id_tour = $data_row["id_tour"];
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
                $img = $banner;

                $arr1[] = array(
                    'id_tour' => "" . $data_row["id_tour"] . "",
                    'title' => "" . clean_special_characters($data_row["title"]) . "",
                    'distance' => (float) $distance,
                    'count_liked' => (int) $data_row["count_liked"],
                    'subtitle' => "",
                    'rating' => (float) $data_row["rating"],
                    'slug' => "" . $data_row["slug"] . "",
                    'link' => url_link_event() . "" . $data_row["slug"] . "",
                    'address' => $data_row["address"],
                    'country' => "" . $data_row["country"] . "",
                    'province' => "" . $data_row["province"] . "",
                    'regency' => "" . $data_row["regency"] . "",
                    'district' => "" . $data_row["district"] . "",
                    'lat' => (float) $data_row["lat"],
                    'lng' => (float) $data_row["lng"],
                    'img' => "" . $img,
                );
            }

            $pages = (int) ceil($total / $per_page);
        }

        $rPagination["total"] = $total;
        $rPagination["page"] = (int) $page;
        $rPagination["pages"] = $pages;
        $rPagination["per_page"] = (int) $per_page;

        return $this->customResponse->is200Response2($response, $arr1, rPagination: $rPagination);
    }

    public function viewTour(Request $request, Response $response, array $parm)
    {

        $id_user = CustomRequestHandler::getParam($request, "id_user", '');

        // $api_gmaps = constant('API_GMAPS');
        $api_gmaps = API_GMAPS;
        $token_mapbox = TOKEN_MAPBOX;
        $current_day = date("l");

        $arr1 = null;

        $sql = "SELECT ttour.id AS id_tour, ttour.title,
        ttour.highlight,ttour.description,
        ttour.location_guide,ttour.lat,ttour.lng,
        ttour.detail_address,ttour.location_map,ttour.slug,
        ttour.email_expen AS email,ttour.phone_expen AS phone,ttour.whatsapp_expen AS whatsapp, ttour.rating AS rating_set,
        ttour.link_ig, ttour.link_fb,
        tbl_tour_category.name AS category, tbl_tour_category.slug AS category_slug,
        tbl_tour_subcategory.name AS subcategory, tbl_tour_subcategory.slug AS subcategory_slug,

        tbl_tour_merchant.id AS id_merchant, tbl_tour_merchant.`name` AS merchant_name,
        tbl_tour_merchant.img AS merchant_img,
        (SELECT COALESCE(round((SUM(tbl_tour_review.rating)/COUNT(tbl_tour_review.id)),1),0)
        FROM tbl_tour_review
        JOIN tbl_tour ON tbl_tour_review.id_tour=tbl_tour.id
        WHERE 1
        AND tbl_tour_review.id_tour=ttour.id
        AND tbl_tour_review.`status`='1') AS rating,

        (SELECT COALESCE(COUNT(tbl_tour_review.id))
        FROM tbl_tour_review
        JOIN tbl_tour ON tbl_tour_review.id_tour=tbl_tour.id
        WHERE 1
        AND tbl_tour_review.id_tour=ttour.id
        AND tbl_tour_review.`status`='1') AS total_review,

        COALESCE((SELECT tbl_like_all.liked FROM tbl_like_all WHERE id_user='$id_user' AND id_ref=ttour.id),0 )AS liked,
        COALESCE((SELECT COUNT(*) FROM tbl_like_all WHERE liked='1' AND id_ref=ttour.id),0 )AS count_liked,
        -- COALESCE((SELECT COUNT(tbl_tour_review_image.id)
        -- FROM tbl_tour_review 
        -- JOIN tbl_tour_review_image ON tbl_tour_review_image.id_tour_review=tbl_tour_review.id
        -- JOIN tbl_tour ON tbl_tour_review.id_tour=tbl_tour.id
        -- WHERE 1
        -- AND tbl_tour_review.id_tour=ttour.id ),0 )AS total_gallery,

        COALESCE((SELECT COUNT(tbl_tour_image.id)
        FROM tbl_tour_image
        WHERE 1
        AND tbl_tour_image.id_tour=ttour.id ),0 )AS total_gallery,
        tbl_adm_country.`name` AS country, tbl_adm_country.`name` AS country_slug,
        tbl_adm_province.`name` AS province, tbl_adm_province.`name` AS province_slug,
        tbl_adm_regency.`nickname` AS regency, tbl_adm_regency.`nickname` AS regency_slug,
        tbl_adm_district.`name` AS district, tbl_adm_district.`name` AS district_slug

        FROM tbl_tour ttour
        JOIN tbl_tour_subcategory ON ttour.id_tour_subcategory=tbl_tour_subcategory.id
        JOIN tbl_tour_category ON tbl_tour_subcategory.id_tour_category=tbl_tour_category.id
        JOIN tbl_tour_merchant ON ttour.id_merchant=tbl_tour_merchant.id
        JOIN tbl_adm_country ON ttour.id_country=tbl_adm_country.id
        JOIN tbl_adm_province ON ttour.id_province=tbl_adm_province.id
        JOIN tbl_adm_regency ON ttour.id_regency=tbl_adm_regency.id
        JOIN tbl_adm_district ON ttour.id_district=tbl_adm_district.id
        WHERE 1
        AND ttour.`status`='1'
        AND tbl_tour_merchant.`status`='1'
        AND (ttour.slug='$parm[slug]')
        GROUP BY ttour.id
        ";

        $result = $this->dbHandler->getDataAll($sql);
        if (
            ($result->rowCount()) > 0
        ) {

            $data_row = $result->fetch(PDO::FETCH_ASSOC);
            $id_tour = $data_row["id_tour"];

            $sql2 = "SELECT * FROM tbl_tour_image WHERE id_tour='$id_tour' AND `status` ='1' ORDER BY sort ASC";

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

            ################
            $sql2 = "SELECT CONCAT(tbl_users.first_name, ' ', tbl_users.last_name) AS fullname,
            tbl_users.photo AS photo_profile,
            tbl_tour_review.id, tbl_tour_review.rating,
            tbl_tour_review.review,
            tbl_tour_review.created_at,
            COALESCE((SELECT tbl_tour_review_helpful.helpful FROM tbl_tour_review_helpful WHERE id_user='$id_user' AND id_tour_review=tbl_tour_review.id),0 )AS helpful,
            COALESCE((SELECT COUNT(*) FROM tbl_tour_review_helpful WHERE helpful='1' AND id_tour_review=tbl_tour_review.id),0 )AS count_helpful

            FROM tbl_tour_review
            JOIN tbl_tour ON tbl_tour_review.id_tour=tbl_tour.id
            JOIN tbl_users ON tbl_tour_review.id_user=tbl_users.id
            WHERE 1
            AND tbl_tour_review.`status`='1'
            AND tbl_tour.id='$id_tour'
            AND tbl_users.id <> '$id_user'
            ORDER BY tbl_tour_review.created_at DESC
            LIMIT 5
            ";

            $result2 = $this->dbHandler->getDataAll($sql2);
            if (($result2->rowCount()) > 0) {

                while ($data_row2 = $result2->fetch(PDO::FETCH_ASSOC)) {
                    $id_rating = $data_row2["id"];

                    $arr31 = null;
                    $sql2_ = "SELECT * FROM tbl_tour_review_image
                    WHERE id_tour_review='$id_rating' AND `status`='1'
                    ORDER BY created_at DESC
                    ";

                    $result2_ = $this->dbHandler->getDataAll($sql2_);
                    if (($result2_->rowCount()) > 0) {

                        while ($data_row2_ = $result2_->fetch(PDO::FETCH_ASSOC)) {
                            $arr31[] = array(
                                'id' => "" . $data_row2_["id"] . "",
                                'img' => "" . $data_row2_["img"] . "",
                            );
                        }
                    }

                    $arrRevDetail = null;
                    $sqlRevDetail = "SELECT  
                    tbl_tour_review_detail.id, tbl_tour_review_detail.id_tour_review_type, tbl_tour_review_detail.rating,  
                    tbl_tour_review_type.`name` 
                    FROM tbl_tour_review_detail
                    JOIN tbl_tour_review_type ON tbl_tour_review_type.id = tbl_tour_review_detail.id_tour_review_type
                    AND tbl_tour_review_detail.id_tour_review='$id_rating'
                    ";

                    $resultRevDetail = $this->dbHandler->getDataAll($sqlRevDetail);
                    if (($resultRevDetail->rowCount()) > 0) {

                        while ($data_rowRevDetail = $resultRevDetail->fetch(PDO::FETCH_ASSOC)) {
                            $arrRevDetail[] = array(
                                'id' => "" . $data_rowRevDetail["id"] . "",
                                'id_tour_review_type' => "" . $data_rowRevDetail["id_tour_review_type"] . "",
                                'rating' => (float) $data_rowRevDetail["rating"],
                                'name' => "" . $data_rowRevDetail["name"] . "",
                            );
                        }
                    }

                    $helpful = $data_row2["helpful"] == 1 ? true : false;

                    $arr3[] = array(
                        'id' => "" . $data_row2["id"] . "",
                        'fullname' => "" . $data_row2["fullname"] . "",
                        'photo_profile' => "" . $data_row2["photo_profile"] . "",
                        'rating' => "" . $data_row2["rating"] . "",
                        'review' => "" . $data_row2["review"] . "",
                        'date' => "" . $data_row2["created_at"] . "",
                        'is_helpful' => $helpful,
                        'count_helpful' => (int) $data_row2["count_helpful"],
                        'img_list' => $arr31,
                        'review_detail' => $arrRevDetail,

                    );
                }
            }

            #############
            $arr3_2 = null;
            $sql2_2 = "SELECT CONCAT(tbl_users.first_name, ' ', tbl_users.last_name) AS fullname,
            tbl_users.photo AS photo_profile,
            tbl_tour_review.id, tbl_tour_review.rating, tbl_tour_review.id_tour_visit_type AS id_visit_type,
            tbl_tour_review.review,
            tbl_tour_review.created_at,
            COALESCE((SELECT tbl_tour_review_helpful.helpful FROM tbl_tour_review_helpful WHERE id_user='$id_user' AND id_tour_review=tbl_tour_review.id),0 )AS helpful,
            COALESCE((SELECT COUNT(*) FROM tbl_tour_review_helpful WHERE helpful='1' AND id_tour_review=tbl_tour_review.id),0 )AS count_helpful

            FROM tbl_tour_review
            JOIN tbl_tour ON tbl_tour_review.id_tour=tbl_tour.id
            JOIN tbl_users ON tbl_tour_review.id_user=tbl_users.id
            WHERE 1
            AND tbl_tour_review.`status`='1'
            AND tbl_tour.id='$id_tour'
            AND tbl_users.id='$id_user'
            ORDER BY tbl_tour_review.created_at DESC
            ";

            $result2_2 = $this->dbHandler->getDataAll($sql2_2);
            if (($result2_2->rowCount()) > 0) {

                $data_row2_2 = $result2_2->fetch(PDO::FETCH_ASSOC);
                $id_rating = $data_row2_2["id"];

                $arr31_2 = null;
                $sql2_ = "SELECT * FROM tbl_tour_review_image
                WHERE id_tour_review='$id_rating' AND `status`='1'
                ORDER BY created_at DESC
                ";

                $result2_ = $this->dbHandler->getDataAll($sql2_);
                if (($result2_->rowCount()) > 0) {

                    while ($data_row2_ = $result2_->fetch(PDO::FETCH_ASSOC)) {
                        $arr31_2[] = array(
                            'id' => "" . $data_row2_["id"] . "",
                            'title' => "" . $data_row2_["id"] . "",
                            'img' => "" . $data_row2_["img"] . "",
                        );
                    }
                }

                //
                $arrRevDetail = null;
                $sqlRevDetail = "SELECT  
                tbl_tour_review_detail.id, tbl_tour_review_detail.id_tour_review_type, tbl_tour_review_detail.rating,  
                tbl_tour_review_type.`name` 
                FROM tbl_tour_review_detail
                JOIN tbl_tour_review_type ON tbl_tour_review_type.id = tbl_tour_review_detail.id_tour_review_type
                AND tbl_tour_review_detail.id_tour_review='$id_rating'
                ";

                $resultRevDetail = $this->dbHandler->getDataAll($sqlRevDetail);
                if (($resultRevDetail->rowCount()) > 0) {

                    while ($data_rowRevDetail = $resultRevDetail->fetch(PDO::FETCH_ASSOC)) {
                        $arrRevDetail[] = array(
                            'id' => "" . $data_rowRevDetail["id"] . "",
                            'id_tour_review_type' => "" . $data_rowRevDetail["id_tour_review_type"] . "",
                            'rating' => (float) $data_rowRevDetail["rating"],
                            'name' => "" . $data_rowRevDetail["name"] . "",
                        );
                    }
                }

                $helpful = $data_row2_2["helpful"] == 1 ? true : false;

                $arr3_2 = array(
                    'id' => "" . $data_row2_2["id"] . "",
                    'id_visit_type' => "" . $data_row2_2["id_visit_type"] . "",
                    'fullname' => "" . $data_row2_2["fullname"] . "",
                    'photo_profile' => "" . $data_row2_2["photo_profile"] . "",
                    'rating' => "" . $data_row2_2["rating"] . "",
                    'review' => "" . $data_row2_2["review"] . "",
                    'date' => "" . $data_row2_2["created_at"] . "",
                    'is_helpful' => $helpful,
                    'count_helpful' => (int) $data_row2_2["count_helpful"],
                    'img_list' => $arr31_2,
                    'review_detail' => $arrRevDetail,

                );
            }



            $sql4 = "SELECT tbl_master_day.day,
            tbl_tour_open_hours.*
            FROM tbl_tour_open_hours
            JOIN tbl_master_day ON tbl_tour_open_hours.id_day=tbl_master_day.id
            WHERE 1
            AND tbl_tour_open_hours.id_tour='$id_tour'
            AND tbl_tour_open_hours.`status`='1'
            GROUP BY tbl_master_day.id
            ORDER BY tbl_master_day.sort ASC
            ";

            $result4 = $this->dbHandler->getDataAll($sql4);
            if (($result4->rowCount()) > 0) {

                while ($data_row4 = $result4->fetch(PDO::FETCH_ASSOC)) {
                    if ($data_row4["day_en"] == $current_day) {
                        // code...
                        $today = "1";
                    } else {
                        // code...
                        $today = "0";
                    }

                    $arr4[] = array(
                        'id' => "" . $data_row4["id"] . "",
                        'day' => "" . $data_row4["day"] . "",
                        'start_time' => "" . fomrat_time($data_row4["start_time"]) . "",
                        'end_time' => "" . fomrat_time($data_row4["end_time"]) . "",
                        'open' => "" . $data_row4["open"] . "",
                        'today' => "" . $today . "",
                    );
                }
            }

            // RATING_TYPE
            ################
            $arr6 = null;
            $sql6 = "SELECT tbl_tour_review.id, tbl_tour_review_type.`name` AS title,
            (SELECT COALESCE(round((SUM(tbl_tour_review_detail.rating)/COUNT(tbl_tour_review_detail.id)),1),0)
            FROM tbl_tour_review_detail
            JOIN tbl_tour_review ON tbl_tour_review_detail.id_tour_review=tbl_tour_review.id
            WHERE 1
            AND tbl_tour_review_detail.id_tour_review_type=tbl_tour_review_type.id
            AND tbl_tour_review_detail.`status`='1') AS rating

            FROM tbl_tour_review
            JOIN tbl_tour ON tbl_tour_review.id_tour=tbl_tour.id
            JOIN tbl_tour_review_detail ON tbl_tour_review_detail.id_tour_review=tbl_tour_review.id
            JOIN tbl_tour_review_type ON tbl_tour_review_detail.id_tour_review_type=tbl_tour_review_type.id
            WHERE 1
            AND tbl_tour.id='$id_tour'
            AND tbl_tour_review.`status`='1'
            GROUP BY tbl_tour_review_type.id
            ";

            $result6 = $this->dbHandler->getDataAll($sql6);
            if (($result6->rowCount()) > 0) {

                while ($data_row6 = $result6->fetch(PDO::FETCH_ASSOC)) {
                    $arr6[] = array(
                        'id' => "" . $data_row6["id"] . "",
                        'title' => "" . $data_row6["title"] . "",
                        'rating' => "" . $data_row6["rating"] . "",

                    );
                }
            }

            // SHORT_GALLERY
            ################
            $arr1_ = null;
            $total_gallery = $data_row["total_gallery"];
            if ($total_gallery > 0) {
                // code...
                // $sql7 = "SELECT tbl_tour_review_image.*, tbl_tour_review.review AS title
                // FROM tbl_tour_review
                // JOIN tbl_tour_review_image ON tbl_tour_review_image.id_tour_review=tbl_tour_review.id
                // JOIN tbl_tour ON tbl_tour_review.id_tour=tbl_tour.id
                // WHERE 1
                // AND tbl_tour.id='$id_tour'
                // AND tbl_tour_review_image.status='1'
                // ORDER BY created_at DESC
                // LIMIT 6
                // ";

                $sql7 = "SELECT * FROM tbl_tour_image 
                WHERE 1
                AND id_tour='$id_tour' 
                AND `status` ='1' 
                ORDER BY sort ASC
                LIMIT 6
                ";

                $result7 = $this->dbHandler->getDataAll($sql7);
                if (($result7->rowCount()) > 0) {

                    while ($data_row7 = $result7->fetch(PDO::FETCH_ASSOC)) {

                        $_id = $data_row7["id"];
                        $banner = img_banner_expen($data_row7["img"]);
                        $file_name = pathinfo($data_row7["img"], PATHINFO_FILENAME);
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

                        $arr7[] = array(
                            'id' => "" . $data_row7["id"] . "",
                            'title' => "" . $data_row7["title"] . "",
                            'img' => "" . $img,
                            // 'img' => "".$banner,
                        );
                    }
                }

                $arr1_ = array(
                    'total_img' => (int) $total_gallery,
                    'img_list' => $arr7

                );
            }

            $arr2_ = null;
            $arr2_ = array(
                'email' => "" . $data_row["email"] . "",
                'phone' => "" . $data_row["phone"] . "",
                'whatsapp' => "" . $data_row["whatsapp"] . ""

            );
            $arr3_ = null;
            $arr3_ = array(
                'link_ig' => "" . $data_row["link_ig"] . "",
                'link_fb' => "" . $data_row["link_fb"] . "",

            );

            // FACILITY
            ################
            $arr8 = null;
            $sql8 = "SELECT *
            FROM tbl_tour_facility
            WHERE id_tour='$id_tour'
            AND `status`='1'
            ";

            $result8 = $this->dbHandler->getDataAll($sql8);
            if (($result8->rowCount()) > 0) {

                while ($data_row8 = $result8->fetch(PDO::FETCH_ASSOC)) {
                    $arr8[] = array(
                        'id' => "" . $data_row8["id"] . "",
                        'name' => "" . $data_row8["name"] . "",

                    );
                }
            }

            // $maps_img=maps_static($data_row["lat"], $data_row["lng"], $api_gmaps);
            $maps_img = maps_static_mapbox($data_row["lat"], $data_row["lng"], $token_mapbox);

            $arr1 = array(
                'id' => "" . $data_row["id_tour"] . "",
                'title' => "" . $data_row["title"] . "",
                'slug' => "" . $data_row["slug"] . "",
                'id_merchant' => "" . $data_row["id_merchant"] . "",
                'category' => "" . $data_row["category"] . "",
                'category_slug' => "" . $data_row["category_slug"] . "",
                'subcategory' => "" . $data_row["subcategory"] . "",
                'subcategory_slug' => "" . $data_row["subcategory_slug"] . "",
                'country' => "" . $data_row["country"] . "",
                'country_slug' => "" . $data_row["country_slug"] . "",
                'province' => "" . $data_row["province_slug"] . "",
                'regency' => "" . $data_row["regency"] . "",
                'regency_slug' => "" . $data_row["regency_slug"] . "",
                'district' => "" . $data_row["district"] . "",
                'district_slug' => "" . $data_row["district_slug"] . "",
                'merchant_name' => "" . clean_special_characters($data_row["merchant_name"]) . "",
                'merchant_img' => img_logo_merchant($data_row["merchant_img"]),
                'merchant_email' => "" . $data_row["email"] . "",
                'highlight' => "" . clean_special_characters($data_row["highlight"]) . "",
                'description' => "" . clean_special_characters($data_row["description"]) . "",
                'location_guide' => "" . $data_row["location_guide"] . "",
                'lat' => (float) $data_row["lat"],
                'lng' => (float) $data_row["lng"],
                'maps_img' => $maps_img,
                'detail_address' => "" . $data_row["detail_address"] . "",
                'location_map' => "" . $data_row["location_map"] . "",
                // 'link' => url_link_expen()."" . $data_row["slug"]. "",
                'rating_set' => (float) $data_row["rating_set"],
                'link' => "",
                'liked' => (int) $data_row["liked"],
                'count_liked' => (int) $data_row["count_liked"],
                'rating' => (float) $data_row["rating"],
                'total_review' => (int) $data_row["total_review"],

                'banner' => $arr2,
                'review' => $arr3,
                'my_review' => $arr3_2,
                'open_hours' => $arr4,
                'rating_type' => $arr6,
                'short_gallery' => $arr1_,
                'contact' => $arr2_,
                'social_media' => $arr3_,
                'facility' => $arr8,


            );

            return $this->customResponse->is200Response($response, $arr1);
        } else {
            // code...
            return $this->customResponse->is404Response($response, "faild");
        }
    }

    public function getTourVisitType(Request $request, Response $response, array $parm)
    {

        $arr1 = array();

        // $id_regency = (empty(CustomRequestHandler::getParam($request, "id_regency"))) ? '' : CustomRequestHandler::getParam($request, "id_regency");

        $sql = "SELECT * FROM tbl_tour_visit_type
        WHERE `status`='1'
        
        ";

        $result = $this->dbHandler->getDataAll($sql);
        $total = $result->rowCount();
        if ($total > 0) {
            // code...

            $result = $this->dbHandler->getDataAll($sql);

            while ($data_row = $result->fetch(PDO::FETCH_ASSOC)) {

                $arr1[] = array(
                    'id' => "" . $data_row["id"] . "",
                    'title' => "" . $data_row["title"] . "",
                    'slug' => "" . $data_row["slug"] . "",

                );
            }
        }



        return $this->customResponse->is200Response($response, $arr1);
    }

    public function getTourReviewType(Request $request, Response $response, array $parm)
    {

        $arr1 = array();

        // $id_regency = (empty(CustomRequestHandler::getParam($request, "id_regency"))) ? '' : CustomRequestHandler::getParam($request, "id_regency");

        $sql = "SELECT * FROM tbl_tour_review_type
        WHERE `status`='1'
        
        ";

        $result = $this->dbHandler->getDataAll($sql);
        $total = $result->rowCount();
        if ($total > 0) {
            // code...

            $result = $this->dbHandler->getDataAll($sql);

            while ($data_row = $result->fetch(PDO::FETCH_ASSOC)) {

                $arr1[] = array(
                    'id' => "" . $data_row["id"] . "",
                    'title' => "" . $data_row["name"] . "",
                    'slug' => "" . $data_row["slug"] . "",

                );
            }
        }



        return $this->customResponse->is200Response($response, $arr1);
    }

    public function getTourReviewResource(Request $request, Response $response, array $parm)
    {

        $arr1 = null;
        $arr_1 = null;
        $arr_2 = null;

        $sql = "SELECT * FROM tbl_tour_review_type WHERE `status`='1'
        
        ";

        $result = $this->dbHandler->getDataAll($sql);
        if (($result->rowCount()) > 0) {

            while ($data_row = $result->fetch(PDO::FETCH_ASSOC)) {

                $arr_1[] = array(
                    'id' => "" . $data_row["id"] . "",
                    'title' => "" . $data_row["name"] . "",
                    'slug' => "" . $data_row["slug"] . "",

                );
            }

            $result = $this->dbHandler->getDataAll($sql);
            $total = $result->rowCount();
            if ($total > 0) {
                // code...

                $sql2 = "SELECT * FROM tbl_tour_visit_type
                WHERE `status`='1'
                
                ";

                $result2 = $this->dbHandler->getDataAll($sql2);
                if (($result2->rowCount()) > 0) {
                    // code...

                    while ($data_row2 = $result2->fetch(PDO::FETCH_ASSOC)) {

                        $arr_2[] = array(
                            'id' => "" . $data_row2["id"] . "",
                            'title' => "" . $data_row2["name"] . "",
                            'slug' => "" . $data_row2["slug"] . "",

                        );
                    }
                }
            }

            $arr1 = array(
                'review_type' => $arr_1,
                'visit_type' => $arr_2,

            );
        }



        return $this->customResponse->is200Response($response, $arr1);
    }

    public function tourReviewSave(Request $request, Response $response)
    {

        $arr1 = null;

        $this->validator->validate($request, [
            "id_user" => v::notEmpty(),
            "id_review" => v::notEmpty(),
            "id_ref" => v::notEmpty(),
            "rating" => v::notEmpty(),
            "review" => v::notEmpty(),
            "id_visit_type" => v::notEmpty(),
            "data_rating" => v::notEmpty(),


        ]);

        if ($this->validator->failed()) {
            $responseMessage = $this->validator->errors;
            return $this->customResponse->is400Response($response, $responseMessage);
        }

        $created_at = date('Y-m-d H:i:s');

        $id_user = CustomRequestHandler::getParam($request, "id_user");
        $id_review = CustomRequestHandler::getParam($request, "id_review");
        $id_ref = CustomRequestHandler::getParam($request, "id_ref");
        $rating = CustomRequestHandler::getParam($request, "rating");
        $review = CustomRequestHandler::getParam($request, "review");
        $id_visit_type = CustomRequestHandler::getParam($request, "id_visit_type");
        $data_rating = CustomRequestHandler::getParam($request, "data_rating");
        $img_review = CustomRequestHandler::getParam($request, "img_review");

        if ((json_decode($data_rating)) > 0) {

            $sql = "SELECT *
            FROM tbl_tour
            WHERE tbl_tour.`status`='1'
            AND tbl_tour.`id`='$id_ref'
            ";

            $result = $this->dbHandler->getDataAll($sql);
            if (($result->rowCount()) > 0) {

                $sql2 = "SELECT *
                FROM tbl_tour_review
                WHERE id='$id_review'
            
                ";

                $result2 = $this->dbHandler->getDataAll($sql2);
                if (($result2->rowCount()) <= 0) {

                    if ((isset($img_review)) && (!empty($img_review))) {

                        $image_name = "img_exp" . acak_all_string(5) . date("YmdHms") . ".jpg";
                        $filepath = "./../images/img_review/" . $image_name;
                        // $filepath = "foto/".$image_name;
                        file_put_contents($filepath, base64_decode($img_review));

                        $img_review_url = get_root_uri() . "images/img_review/" . $image_name;

                        $sqlInsert = "
                        INSERT INTO tbl_tour_review set
                        id='$id_review',
                        id_tour='$id_ref',
                        id_tour_visit_type='$id_visit_type',
                        id_user='$id_user',
                        rating='$rating',
                        review = '$review',
                        photo='$img_review_url',
                        created_at='$created_at';
                
                        ";
                    } else {
                        // code...

                        $sqlInsert = "
                        INSERT INTO tbl_tour_review set
                        id='$id_review',
                        id_tour='$id_ref',
                        id_tour_visit_type='$id_visit_type',
                        id_user='$id_user',
                        rating='$rating',
                        review = '$review',
                        created_at='$created_at';
                
                        ";
                    }



                    $result = $this->dbHandler->insertDataAll($sqlInsert);
                    if ($result) {

                        $sql = "SELECT * FROM tbl_tour_review_detail WHERE `id_tour_review`='$id_review'
        
                        ";
                        $result = $this->dbHandler->getDataAll($sql);
                        if (($result->rowCount()) > 0) {

                            $arr3_2 = null;
                            $sql2_2 = "SELECT CONCAT(tbl_users.first_name, ' ', tbl_users.last_name) AS fullname,
                            tbl_users.photo AS photo_profile,
                            tbl_tour_review.id, tbl_tour_review.rating, tbl_tour_review.id_tour_visit_type AS id_visit_type,
                            tbl_tour_review.review,
                            tbl_tour_review.created_at,
                            COALESCE((SELECT tbl_tour_review_helpful.helpful FROM tbl_tour_review_helpful WHERE id_user='$id_user' AND id_tour_review=tbl_tour_review.id),0 )AS helpful,
                            COALESCE((SELECT COUNT(*) FROM tbl_tour_review_helpful WHERE helpful='1' AND id_tour_review=tbl_tour_review.id),0 )AS count_helpful

                            FROM tbl_tour_review
                            JOIN tbl_tour ON tbl_tour_review.id_tour=tbl_tour.id
                            JOIN tbl_users ON tbl_tour_review.id_user=tbl_users.id
                            WHERE 1
                            -- AND tbl_tour_review.`status`='1'
                            AND tbl_tour.id='$id_ref'
                            AND tbl_users.id='$id_user'
                            ORDER BY tbl_tour_review.created_at DESC
                            ";

                            $result2_2 = $this->dbHandler->getDataAll($sql2_2);
                            if (($result2_2->rowCount()) > 0) {

                                $data_row2_2 = $result2_2->fetch(PDO::FETCH_ASSOC);
                                $id_rating = $data_row2_2["id"];

                                $arr31_2 = null;
                                $sql2_ = "SELECT * FROM tbl_tour_review_image
                                WHERE id_tour_review='$id_rating' AND `status`='1'
                                ORDER BY created_at DESC
                                ";

                                $result2_ = $this->dbHandler->getDataAll($sql2_);
                                if (($result2_->rowCount()) > 0) {

                                    while ($data_row2_ = $result2_->fetch(PDO::FETCH_ASSOC)) {
                                        $arr31_2[] = array(
                                            'id' => "" . $data_row2_["id"] . "",
                                            'title' => "" . $data_row2_["title"] . "",
                                            'img' => "" . $data_row2_["img"] . "",
                                        );
                                    }
                                }

                                //
                                $arrRevDetail = null;
                                $sqlRevDetail = "SELECT  
                                tbl_tour_review_detail.id, tbl_tour_review_detail.id_tour_review_type, tbl_tour_review_detail.rating,  
                                tbl_tour_review_type.`name` 
                                FROM tbl_tour_review_detail
                                JOIN tbl_tour_review_type ON tbl_tour_review_type.id = tbl_tour_review_detail.id_tour_review_type
                                AND tbl_tour_review_detail.id_tour_review='$id_rating'
                                ";

                                $resultRevDetail = $this->dbHandler->getDataAll($sqlRevDetail);
                                if (($resultRevDetail->rowCount()) > 0) {

                                    while ($data_rowRevDetail = $resultRevDetail->fetch(PDO::FETCH_ASSOC)) {
                                        $arrRevDetail[] = array(
                                            'id' => "" . $data_rowRevDetail["id"] . "",
                                            'id_tour_review_type' => "" . $data_rowRevDetail["id_tour_review_type"] . "",
                                            'rating' => (float) $data_rowRevDetail["rating"],
                                            'name' => "" . $data_rowRevDetail["name"] . "",
                                        );
                                    }
                                }


                                $helpful = $data_row2_2["helpful"] == 1 ? true : false;

                                $arr3_2 = array(
                                    'id' => "" . $data_row2_2["id"] . "",
                                    'id_visit_type' => "" . $data_row2_2["id_visit_type"] . "",
                                    'fullname' => "" . $data_row2_2["fullname"] . "",
                                    'photo_profile' => "" . $data_row2_2["photo_profile"] . "",
                                    'rating' => "" . $data_row2_2["rating"] . "",
                                    'review' => "" . $data_row2_2["review"] . "",
                                    'date' => "" . $data_row2_2["created_at"] . "",
                                    'is_helpful' => $helpful,
                                    'count_helpful' => (int) $data_row2_2["count_helpful"],
                                    'img_list' => $arr31_2,
                                    'review_detail' => $arrRevDetail,

                                );
                            }

                            return $this->customResponse->is200Response($response, $arr3_2);
                        } else {
                            // code...
                            $data_insert = olah_data_detail_review($data_rating, $id_review, $created_at);
                            $sqlkirim2 = "INSERT INTO tbl_tour_review_detail
                              (
                                id_tour_review, created_at,
                                  id, id_tour_review_type, rating
                              )
                              VALUES
                              $data_insert";
                            $result = $this->dbHandler->insertDataAll($sqlkirim2);

                            if ($result) {

                                $arr3_2 = null;
                                $sql2_2 = "SELECT CONCAT(tbl_users.first_name, ' ', tbl_users.last_name) AS fullname,
                                tbl_users.photo AS photo_profile,
                                tbl_tour_review.id, tbl_tour_review.rating, tbl_tour_review.id_tour_visit_type AS id_visit_type,
                                tbl_tour_review.review,
                                tbl_tour_review.created_at,
                                COALESCE((SELECT tbl_tour_review_helpful.helpful FROM tbl_tour_review_helpful WHERE id_user='$id_user' AND id_tour_review=tbl_tour_review.id),0 )AS helpful,
                                COALESCE((SELECT COUNT(*) FROM tbl_tour_review_helpful WHERE helpful='1' AND id_tour_review=tbl_tour_review.id),0 )AS count_helpful

                                FROM tbl_tour_review
                                JOIN tbl_tour ON tbl_tour_review.id_tour=tbl_tour.id
                                JOIN tbl_users ON tbl_tour_review.id_user=tbl_users.id
                                WHERE 1
                                -- AND tbl_tour_review.`status`='1'
                                AND tbl_tour.id='$id_ref'
                                AND tbl_users.id='$id_user'
                                ORDER BY tbl_tour_review.created_at DESC
                                ";

                                $result2_2 = $this->dbHandler->getDataAll($sql2_2);
                                if (($result2_2->rowCount()) > 0) {

                                    $data_row2_2 = $result2_2->fetch(PDO::FETCH_ASSOC);
                                    $id_rating = $data_row2_2["id"];

                                    $arr31_2 = null;
                                    $sql2_ = "SELECT * FROM tbl_tour_review_image
                                    WHERE id_tour_review='$id_rating' AND `status`='1'
                                    ORDER BY created_at DESC
                                    ";

                                    $result2_ = $this->dbHandler->getDataAll($sql2_);
                                    if (($result2_->rowCount()) > 0) {

                                        while ($data_row2_ = $result2_->fetch(PDO::FETCH_ASSOC)) {
                                            $arr31_2[] = array(
                                                'id' => "" . $data_row2_["id"] . "",
                                                'img' => "" . $data_row2_["img"] . "",
                                            );
                                        }
                                    }

                                    //
                                    $arrRevDetail = null;
                                    $sqlRevDetail = "SELECT  
                                    tbl_tour_review_detail.id, tbl_tour_review_detail.id_tour_review_type, tbl_tour_review_detail.rating,  
                                    tbl_tour_review_type.`name` 
                                    FROM tbl_tour_review_detail
                                    JOIN tbl_tour_review_type ON tbl_tour_review_type.id = tbl_tour_review_detail.id_tour_review_type
                                    AND tbl_tour_review_detail.id_tour_review='$id_rating'
                                    ";

                                    $resultRevDetail = $this->dbHandler->getDataAll($sqlRevDetail);
                                    if (($resultRevDetail->rowCount()) > 0) {

                                        while ($data_rowRevDetail = $resultRevDetail->fetch(PDO::FETCH_ASSOC)) {
                                            $arrRevDetail[] = array(
                                                'id' => "" . $data_rowRevDetail["id"] . "",
                                                'id_tour_review_type' => "" . $data_rowRevDetail["id_tour_review_type"] . "",
                                                'rating' => (float) $data_rowRevDetail["rating"],
                                                'name' => "" . $data_rowRevDetail["name"] . "",
                                            );
                                        }
                                    }

                                    $helpful = $data_row2_2["helpful"] == 1 ? true : false;

                                    $arr3_2 = array(
                                        'id' => "" . $data_row2_2["id"] . "",
                                        'id_visit_type' => "" . $data_row2_2["id_visit_type"] . "",
                                        'fullname' => "" . $data_row2_2["fullname"] . "",
                                        'photo_profile' => "" . $data_row2_2["photo_profile"] . "",
                                        'rating' => "" . $data_row2_2["rating"] . "",
                                        'review' => "" . $data_row2_2["review"] . "",
                                        'date' => "" . $data_row2_2["created_at"] . "",
                                        'is_helpful' => $helpful,
                                        'count_helpful' => (int) $data_row2_2["count_helpful"],
                                        'img_list' => $arr31_2,
                                        'review_detail' => $arrRevDetail,

                                    );
                                }

                                return $this->customResponse->is200Response($response, $arr3_2);
                            } else {
                            }
                        }
                    } else {
                        // code...
                        return $this->customResponse->is404Response($response, "Failed review1");
                    }

                } else {
                    // code...
                    $arr3_2 = null;
                    $sql2_2 = "SELECT CONCAT(tbl_users.first_name, ' ', tbl_users.last_name) AS fullname,
                    tbl_users.photo AS photo_profile,
                    tbl_tour_review.id, tbl_tour_review.rating, tbl_tour_review.id_tour_visit_type AS id_visit_type,
                    tbl_tour_review.review,
                    tbl_tour_review.created_at,
                    COALESCE((SELECT tbl_tour_review_helpful.helpful FROM tbl_tour_review_helpful WHERE id_user='$id_user' AND id_tour_review=tbl_tour_review.id),0 )AS helpful,
                    COALESCE((SELECT COUNT(*) FROM tbl_tour_review_helpful WHERE helpful='1' AND id_tour_review=tbl_tour_review.id),0 )AS count_helpful

                    FROM tbl_tour_review
                    JOIN tbl_tour ON tbl_tour_review.id_tour=tbl_tour.id
                    JOIN tbl_users ON tbl_tour_review.id_user=tbl_users.id
                    WHERE 1
                    -- AND tbl_tour_review.`status`='1'
                    AND tbl_tour.id='$id_ref'
                    AND tbl_users.id='$id_user'
                    ORDER BY tbl_tour_review.created_at DESC
                    ";

                    $result2_2 = $this->dbHandler->getDataAll($sql2_2);
                    if (($result2_2->rowCount()) > 0) {

                        $data_row2_2 = $result2_2->fetch(PDO::FETCH_ASSOC);
                        $id_rating = $data_row2_2["id"];

                        $arr31_2 = null;
                        $sql2_ = "SELECT * FROM tbl_tour_review_image
                        WHERE id_tour_review='$id_rating' AND `status`='1'
                        ORDER BY created_at DESC
                        ";

                        $result2_ = $this->dbHandler->getDataAll($sql2_);
                        if (($result2_->rowCount()) > 0) {

                            while ($data_row2_ = $result2_->fetch(PDO::FETCH_ASSOC)) {
                                $arr31_2[] = array(
                                    'id' => "" . $data_row2_["id"] . "",
                                    'img' => "" . $data_row2_["img"] . "",
                                );
                            }
                        }

                        //
                        $arrRevDetail = null;
                        $sqlRevDetail = "SELECT  
                        tbl_tour_review_detail.id, tbl_tour_review_detail.id_tour_review_type, tbl_tour_review_detail.rating,  
                        tbl_tour_review_type.`name` 
                        FROM tbl_tour_review_detail
                        JOIN tbl_tour_review_type ON tbl_tour_review_type.id = tbl_tour_review_detail.id_tour_review_type
                        AND tbl_tour_review_detail.id_tour_review='$id_rating'
                        ";

                        $resultRevDetail = $this->dbHandler->getDataAll($sqlRevDetail);
                        if (($resultRevDetail->rowCount()) > 0) {

                            while ($data_rowRevDetail = $resultRevDetail->fetch(PDO::FETCH_ASSOC)) {
                                $arrRevDetail[] = array(
                                    'id' => "" . $data_rowRevDetail["id"] . "",
                                    'id_tour_review_type' => "" . $data_rowRevDetail["id_tour_review_type"] . "",
                                    'rating' => (float) $data_rowRevDetail["rating"],
                                    'name' => "" . $data_rowRevDetail["name"] . "",
                                );
                            }
                        }

                        $helpful = $data_row2_2["helpful"] == 1 ? true : false;

                        $arr3_2 = array(
                            'id' => "" . $data_row2_2["id"] . "",
                            'id_visit_type' => "" . $data_row2_2["id_visit_type"] . "",
                            'fullname' => "" . $data_row2_2["fullname"] . "",
                            'photo_profile' => "" . $data_row2_2["photo_profile"] . "",
                            'rating' => "" . $data_row2_2["rating"] . "",
                            'review' => "" . $data_row2_2["review"] . "",
                            'date' => "" . $data_row2_2["created_at"] . "",
                            'is_helpful' => $helpful,
                            'count_helpful' => (int) $data_row2_2["count_helpful"],
                            'img_list' => $arr31_2,
                            'review_detail' => $arrRevDetail,

                        );
                    }

                    return $this->customResponse->is200Response($response, $arr3_2);
                }
            } else {
                // code...
                return $this->customResponse->is404Response($response, "Failed review2");
            }
        } else {
            return $this->customResponse->is404Response($response, "data rating tidak ditemukan");
        }
    }

    public function tourReviewUpdate(Request $request, Response $response)
    {

        $arr1 = null;

        $this->validator->validate($request, [
            "id_user" => v::notEmpty(),
            "id_review" => v::notEmpty(),
            "id_ref" => v::notEmpty(),
            "rating" => v::notEmpty(),
            "review" => v::notEmpty(),
            "id_visit_type" => v::notEmpty(),
            "data_rating" => v::notEmpty(),


        ]);

        if ($this->validator->failed()) {
            $responseMessage = $this->validator->errors;
            return $this->customResponse->is400Response($response, $responseMessage);
        }

        $created_at = date('Y-m-d H:i:s');

        $id_user = CustomRequestHandler::getParam($request, "id_user");
        $id_review = CustomRequestHandler::getParam($request, "id_review");
        $id_ref = CustomRequestHandler::getParam($request, "id_ref");
        $rating = CustomRequestHandler::getParam($request, "rating");
        $review = CustomRequestHandler::getParam($request, "review");
        $id_visit_type = CustomRequestHandler::getParam($request, "id_visit_type");
        $data_rating = CustomRequestHandler::getParam($request, "data_rating");
        $img_review = CustomRequestHandler::getParam($request, "img_review");

        if ((json_decode($data_rating)) > 0) {

            $sql = "SELECT *
            FROM tbl_tour
            WHERE tbl_tour.`status`='1'
            AND tbl_tour.`id`='$id_ref'
            ";

            $result = $this->dbHandler->getDataAll($sql);
            if (($result->rowCount()) > 0) {

                $sql2 = "SELECT *
                FROM tbl_tour_review
                WHERE id='$id_review'
            
                ";

                $result2 = $this->dbHandler->getDataAll($sql2);
                if (($result2->rowCount()) > 0) {

                    if ((isset($img_review)) && (!empty($img_review))) {

                        $image_name = "img_exp" . acak_all_string(5) . date("YmdHms") . ".jpg";
                        $filepath = "./../images/img_review/" . $image_name;
                        // $filepath = "foto/".$image_name;
                        file_put_contents($filepath, base64_decode($img_review));

                        $img_review_url = get_root_uri() . "images/img_review/" . $image_name;

                        $sqlInsert = "UPDATE tbl_tour_review SET
                        id_tour='$id_ref',
                        id_tour_visit_type='$id_visit_type',
                        id_user='$id_user',
                        rating='$rating',
                        review = '$review',
                        photo='$img_review_url',
                        updated_at='$created_at'
                        WHERE id='$id_review'
                        ";
                    } else {
                        // code...

                        $sqlInsert = "UPDATE tbl_tour_review SET
                        id_tour='$id_ref',
                        id_tour_visit_type='$id_visit_type',
                        id_user='$id_user',
                        rating='$rating',
                        review = '$review',
                        updated_at='$created_at'
                        WHERE id='$id_review'
                        ";
                    }

                    $result = $this->dbHandler->insertDataAll($sqlInsert);
                    if ($result) {

                        $sql = "DELETE FROM tbl_tour_review_detail WHERE `id_tour_review`='$id_review'";
                        $result = $this->dbHandler->getDataAll($sql);
                        if ($result) {

                            // code...
                            $data_insert = olah_data_detail_review($data_rating, $id_review, $created_at);
                            $sqlkirim2 = "INSERT INTO tbl_tour_review_detail
                              (
                                id_tour_review, created_at,
                                  id, id_tour_review_type, rating
                              )
                              VALUES
                              $data_insert";
                            $result = $this->dbHandler->insertDataAll($sqlkirim2);

                            if ($result) {

                                $arr3_2 = null;
                                $sql2_2 = "SELECT CONCAT(tbl_users.first_name, ' ', tbl_users.last_name) AS fullname,
                                tbl_users.photo AS photo_profile,
                                tbl_tour_review.id, tbl_tour_review.rating, tbl_tour_review.id_tour_visit_type AS id_visit_type,
                                tbl_tour_review.review,
                                tbl_tour_review.created_at,
                                COALESCE((SELECT tbl_tour_review_helpful.helpful FROM tbl_tour_review_helpful WHERE id_user='$id_user' AND id_tour_review=tbl_tour_review.id),0 )AS helpful,
                                COALESCE((SELECT COUNT(*) FROM tbl_tour_review_helpful WHERE helpful='1' AND id_tour_review=tbl_tour_review.id),0 )AS count_helpful

                                FROM tbl_tour_review
                                JOIN tbl_tour ON tbl_tour_review.id_tour=tbl_tour.id
                                JOIN tbl_users ON tbl_tour_review.id_user=tbl_users.id
                                WHERE 1
                                -- AND tbl_tour_review.`status`='1'
                                AND tbl_tour.id='$id_ref'
                                AND tbl_users.id='$id_user'
                                ORDER BY tbl_tour_review.created_at DESC
                                ";

                                $result2_2 = $this->dbHandler->getDataAll($sql2_2);
                                if (($result2_2->rowCount()) > 0) {

                                    $data_row2_2 = $result2_2->fetch(PDO::FETCH_ASSOC);
                                    $id_rating = $data_row2_2["id"];

                                    $arr31_2 = null;
                                    $sql2_ = "SELECT * FROM tbl_tour_review_image
                                    WHERE id_tour_review='$id_rating' AND `status`='1'
                                    ORDER BY created_at DESC
                                    ";

                                    $result2_ = $this->dbHandler->getDataAll($sql2_);
                                    if (($result2_->rowCount()) > 0) {

                                        while ($data_row2_ = $result2_->fetch(PDO::FETCH_ASSOC)) {
                                            $arr31_2[] = array(
                                                'id' => "" . $data_row2_["id"] . "",
                                                'img' => "" . $data_row2_["img"] . "",
                                            );
                                        }
                                    }

                                    //
                                    $arrRevDetail = null;
                                    $sqlRevDetail = "SELECT  
                                    tbl_tour_review_detail.id, tbl_tour_review_detail.id_tour_review_type, tbl_tour_review_detail.rating,  
                                    tbl_tour_review_type.`name` 
                                    FROM tbl_tour_review_detail
                                    JOIN tbl_tour_review_type ON tbl_tour_review_type.id = tbl_tour_review_detail.id_tour_review_type
                                    AND tbl_tour_review_detail.id_tour_review='$id_rating'
                                    ";

                                    $resultRevDetail = $this->dbHandler->getDataAll($sqlRevDetail);
                                    if (($resultRevDetail->rowCount()) > 0) {

                                        while ($data_rowRevDetail = $resultRevDetail->fetch(PDO::FETCH_ASSOC)) {
                                            $arrRevDetail[] = array(
                                                'id' => "" . $data_rowRevDetail["id"] . "",
                                                'id_tour_review_type' => "" . $data_rowRevDetail["id_tour_review_type"] . "",
                                                'rating' => (float) $data_rowRevDetail["rating"],
                                                'name' => "" . $data_rowRevDetail["name"] . "",
                                            );
                                        }
                                    }

                                    $helpful = $data_row2_2["helpful"] == 1 ? true : false;

                                    $arr3_2 = array(
                                        'id' => "" . $data_row2_2["id"] . "",
                                        'id_visit_type' => "" . $data_row2_2["id_visit_type"] . "",
                                        'fullname' => "" . $data_row2_2["fullname"] . "",
                                        'photo_profile' => "" . $data_row2_2["photo_profile"] . "",
                                        'rating' => "" . $data_row2_2["rating"] . "",
                                        'review' => "" . $data_row2_2["review"] . "",
                                        'date' => "" . $data_row2_2["created_at"] . "",
                                        'is_helpful' => $helpful,
                                        'count_helpful' => (int) $data_row2_2["count_helpful"],
                                        'img_list' => $arr31_2,
                                        'review_detail' => $arrRevDetail,

                                    );
                                }

                                return $this->customResponse->is200Response($response, $arr3_2);
                            } else {
                                return $this->customResponse->is404Response($response, "Failed review1");
                            }



                        } else {

                        }
                    } else {
                        // code...
                        return $this->customResponse->is404Response($response, "Failed review1");
                    }
                } else {
                    return $this->customResponse->is404Response($response, "Failed review2");
                }

            } else {
                // code...
                return $this->customResponse->is404Response($response, "Failed review2");
            }
        } else {
            return $this->customResponse->is404Response($response, "data rating tidak ditemukan");
        }
    }

    public function getTourReviewPg(Request $request, Response $response)
    {
        $total = 0;
        $page = 0;
        $pages = 0;

        $arr1 = array();

        $page = (int) CustomRequestHandler::getParam($request, "page", 1);
        $per_page = (int) CustomRequestHandler::getParam($request, "per_page", 1);
        $id_tour = CustomRequestHandler::getParam($request, "id_tour", '');
        $slug = CustomRequestHandler::getParam($request, "slug", '');
        $id_user = CustomRequestHandler::getParam($request, "id_user", '');

        $per_page = min(max($per_page, 5), 20);

        // Calculate the offset
        $offset = ($page - 1) * $per_page;

        // Fetch the total records and paginated results
        // COUNT(*) OVER() AS total_records,
        $sql = "SELECT COUNT(*) OVER() AS total_records, CONCAT(tbl_users.first_name, ' ', tbl_users.last_name) AS fullname, 
        tbl_users.photo AS photo_profile,
        tbl_tour_review.id, tbl_tour_review.rating,
        tbl_tour_review.review,
        tbl_tour_review.created_at,
        COALESCE((SELECT tbl_tour_review_helpful.helpful FROM tbl_tour_review_helpful WHERE id_user='$id_user' AND id_tour_review=tbl_tour_review.id),0 )AS helpful,
        COALESCE((SELECT COUNT(*) FROM tbl_tour_review_helpful WHERE helpful='1' AND id_tour_review=tbl_tour_review.id),0 )AS count_helpful

        FROM tbl_tour_review 
        JOIN tbl_tour ON tbl_tour_review.id_tour=tbl_tour.id
        JOIN tbl_users ON tbl_tour_review.id_user=tbl_users.id
        WHERE 1
        AND tbl_tour_review.`status`='1'
        AND (tbl_tour.id='$id_tour' OR tbl_tour.slug='$slug')
        ORDER BY tbl_tour_review.created_at DESC
        LIMIT $per_page OFFSET $offset
        ";

        $result = $this->dbHandler->getDataAll($sql);
        if ($result === false) {

            $rPagination["total"] = 0;
            $rPagination["page"] = (int) $page;
            $rPagination["pages"] = 0;
            $rPagination["per_page"] = (int) $per_page;
            return $this->customResponse->is500Response($response, "Query query error.");
        } else {
            $total = 0;
            while ($data_row = $result->fetch(PDO::FETCH_ASSOC)) {
                $total = (int) $data_row['total_records'];

                $id_rating = $data_row["id"];

                $arr1_ = null;
                $sql2 = "SELECT * FROM tbl_tour_review_image
                WHERE id_tour_review='$id_rating' AND `status`='1'
                ORDER BY created_at DESC
                ";

                $result2 = $this->dbHandler->getDataAll($sql2);
                if (($result2->rowCount()) > 0) {

                    while ($data_row2 = $result2->fetch(PDO::FETCH_ASSOC)) {
                        $arr1_[] = array(
                            'id' => "" . $data_row2["id"] . "",
                            'img' => "" . $data_row2["img"] . "",
                        );
                    }
                }

                $helpful = $data_row["helpful"] == 1 ? true : false;

                $arr1[] = array(
                    'id' => "" . $data_row["id"] . "",
                    'fullname' => "" . $data_row["fullname"] . "",
                    'photo_profile' => "" . $data_row["photo_profile"] . "",
                    'rating' => (float) $data_row["rating"],
                    'review' => "" . $data_row["review"] . "",
                    'date' => "" . $data_row["created_at"] . "",
                    'is_helpful' => $helpful,
                    'count_helpful' => (int) $data_row["count_helpful"],
                    'img_list' => $arr1_,

                );
            }

            $pages = (int) ceil($total / $per_page);
        }

        $rPagination["total"] = $total;
        $rPagination["page"] = (int) $page;
        $rPagination["pages"] = $pages;
        $rPagination["per_page"] = (int) $per_page;

        return $this->customResponse->is200Response2($response, $arr1, rPagination: $rPagination);
    }

    public function tourReviewHelpfulSave(Request $request, Response $response)
    {

        $arr1 = null;

        $this->validator->validate($request, [
            "id_user" => v::notEmpty(),
            "id_review" => v::notEmpty(),
            "id_helpful" => v::notEmpty(),

        ]);

        if ($this->validator->failed()) {
            $responseMessage = $this->validator->errors;
            return $this->customResponse->is400Response($response, $responseMessage);
        }

        $created_at = date('Y-m-d H:i:s');

        $id_user = CustomRequestHandler::getParam($request, "id_user");
        $id_review = CustomRequestHandler::getParam($request, "id_review");
        $id_helpful = CustomRequestHandler::getParam($request, "id_helpful");

        $sqlCek = "SELECT * FROM tbl_tour_review
        WHERE id='$id_review' AND status='1'
        ";
        $resultCek = $this->dbHandler->getDataAll($sqlCek);
        if (($resultCek->rowCount()) > 0) {

            $sql = "SELECT * FROM tbl_tour_review_helpful
            WHERE id_tour_review='$id_review'
            AND id_user='$id_user'
            ";
            $result = $this->dbHandler->getDataAll($sql);
            if (($result->rowCount()) > 0) {

                $data_row = $result->fetch(PDO::FETCH_ASSOC);

                $helpful = $data_row["helpful"];

                if ($helpful == "0") {
                    // code...
                    $sql = "UPDATE tbl_tour_review_helpful SET helpful='1',
                    updated_at='$created_at'
                    WHERE id_tour_review='$id_review'
                    AND id_user='$id_user'
                    ";
                    $result = $this->dbHandler->updateDataAll($sql);
                    if ($result) {
                        // code...

                        $sqlHelpful = "SELECT COUNT(id) AS helpful_total
                        FROM tbl_tour_review_helpful
                        WHERE `helpful`='1'
                        AND id_tour_review='$id_review'
                        ";
                        $resultHelpful = $this->dbHandler->getDataAll($sqlHelpful);
                        if (($resultHelpful->rowCount()) > 0) {

                            $data_rowHelpful = $resultHelpful->fetch(PDO::FETCH_ASSOC);

                            $helpful_total = $data_rowHelpful["helpful_total"];
                        }

                        $arr1 = array(
                            'is_helpful' => true,
                            'helpful_total' => (int) $helpful_total,
                        );

                        return $this->customResponse->is200Response($response, $arr1);
                    } else {

                        return $this->customResponse->is404Response($response, $arr1, "Gagal diupdate!");
                    }
                } else {
                    // code...
                    $sql = "UPDATE tbl_tour_review_helpful SET helpful='0',
                    updated_at='$created_at'
                    WHERE id_tour_review='$id_review'
                    AND id_user='$id_user'
                    ";
                    $result = $this->dbHandler->updateDataAll($sql);
                    if ($result) {
                        // code...

                        $sqlHelpful = "SELECT COUNT(id) AS helpful_total
                        FROM tbl_tour_review_helpful
                        WHERE `helpful`='1'
                        AND id_tour_review='$id_review'
                        ";
                        $resultHelpful = $this->dbHandler->getDataAll($sqlHelpful);
                        if (($resultHelpful->rowCount()) > 0) {

                            $data_rowHelpful = $resultHelpful->fetch(PDO::FETCH_ASSOC);

                            $helpful_total = $data_rowHelpful["helpful_total"];
                        }

                        $arr1 = array(
                            'is_helpful' => false,
                            'helpful_total' => (int) $helpful_total,
                        );

                        return $this->customResponse->is200Response($response, $arr1);
                    } else {

                        return $this->customResponse->is404Response($response, $arr1, "Gagal diupdate!");
                    }
                }
            } else {

                $sqlInsert = "INSERT INTO tbl_tour_review_helpful SET
                id='$id_helpful',
                id_user='$id_user',
                id_tour_review='$id_review',
                helpful='1',
                created_at='$created_at';
                ";

                $result = $this->dbHandler->insertDataAll($sqlInsert);
                if ($result) {

                    $sqlHelpful = "SELECT COUNT(id) AS helpful_total
                    FROM tbl_tour_review_helpful
                    WHERE `helpful`='1'
                    AND id_tour_review='$id_review'
                    ";
                    $resultHelpful = $this->dbHandler->getDataAll($sqlHelpful);
                    if (($resultHelpful->rowCount()) > 0) {

                        $data_rowHelpful = $resultHelpful->fetch(PDO::FETCH_ASSOC);

                        $helpful_total = $data_rowHelpful["helpful_total"];
                    }

                    $arr1 = array(
                        'is_helpful' => true,
                        'helpful_total' => (int) $helpful_total,
                    );

                    return $this->customResponse->is200Response($response, $arr1);
                } else {
                    // code...

                    return $this->customResponse->is404Response($response, $arr1, "Gagal insert!");
                }
            }
        } else {

            return $this->customResponse->is404Response($response, $arr1, "Layanan tidak ditemukan!");
        }
    }

    public function getTourOfficialPhotosPg(Request $request, Response $response)
    {
        $total = 0;
        $page = 0;
        $pages = 0;

        $arr1 = array();

        $page = CustomRequestHandler::getParam($request, "page", 1);
        $per_page = CustomRequestHandler::getParam($request, "per_page", 1);
        $id_tour = CustomRequestHandler::getParam($request, "id_tour", '');
        // $slug = (empty(CustomRequestHandler::getParam($request, "slug"))) ? '' : CustomRequestHandler::getParam($request, "slug");

        $per_page = min(max($per_page, 5), 20);

        // Calculate the offset
        $offset = ($page - 1) * $per_page;

        // Fetch the total records and paginated results
        // COUNT(*) OVER() AS total_records,
        $sql = "SELECT COUNT(*) OVER() AS total_records, tbl_tour_image.* FROM tbl_tour_image
        WHERE id_tour='$id_tour' AND `status` ='1'
        ORDER BY sort ASC
        LIMIT $per_page OFFSET $offset
        ";

        $result = $this->dbHandler->getDataAll($sql);
        if ($result === false) {

            $rPagination["total"] = 0;
            $rPagination["page"] = (int) $page;
            $rPagination["pages"] = 0;
            $rPagination["per_page"] = (int) $per_page;
            return $this->customResponse->is500Response($response, "Query query error.");
        } else {
            $total = 0;
            while ($data_row = $result->fetch(PDO::FETCH_ASSOC)) {
                $total = (int) $data_row['total_records'];

                $_id = $data_row["id"];
                $banner = img_banner_expen($data_row["img"]);
                $file_name = pathinfo($data_row["img"], PATHINFO_FILENAME);
                $filepath = "./../images/img_temp/" . $file_name . ".jpg";
                // $commp=compress_img($banner,$filepath,50);
                // if ($commp == $banner) {
                // // code...
                // $img=$commp;
                // }else {
                // // code...
                // $img=get_root_uri()."images/img_temp/".$commp;
                // }
                $img = $banner;

                $arr1[] = array(
                    'id' => "" . $data_row["id"] . "",
                    'img' => "" . $img,
                );
            }

            $pages = (int) ceil($total / $per_page);
        }

        $rPagination["total"] = $total;
        $rPagination["page"] = (int) $page;
        $rPagination["pages"] = $pages;
        $rPagination["per_page"] = (int) $per_page;

        return $this->customResponse->is200Response2($response, $arr1, rPagination: $rPagination);
    }
}
