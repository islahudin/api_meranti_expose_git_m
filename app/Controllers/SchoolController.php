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



class SchoolController
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

    public function getSchoolPg(Request $request, Response $response)
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
        $level = CustomRequestHandler::getParam($request, "level", '');
        $status = CustomRequestHandler::getParam($request, "status", '');
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

        if (stringContains($level, ',')) {
            // code...
            $_data = $level;
            // $arr_data = explode (",",$_data);
            // $imploded_data = implode("','",$arr_data);

            $new_arr = array_map('trim', explode(',', $_data));
            $imploded_data = implode("','", $new_arr);
            $level_filter = " AND tbl_school3.level IN ('$imploded_data')";
        } else if (!empty($level)){
            // code...
            $level_filter = "AND tbl_school3.level = '$level'";
        }

        if (stringContains($status, ',')) {
            // code...
            $_data = $status;
            // $arr_data = explode (",",$_data);
            // $imploded_data = implode("','",$arr_data);

            $new_arr = array_map('trim', explode(',', $_data));
            $imploded_data = implode("','", $new_arr);
            $status_filter = " AND tbl_school3.s_status IN ('$imploded_data')";
        } else if (!empty($status)){
            // code...
            $status_filter = "AND tbl_school3.s_status = '$status'";
        }


        if (!empty($q)) {
            $q_filter = " AND ((tbl_school3.name LIKE '%$q%') OR (tbl_adm_district.`name` LIKE '%$q%'))";
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
        $sql = "SELECT tbl_school3.id,  COUNT(*) OVER() AS total_records, tbl_school3.name AS title, tbl_school3.lat, tbl_school3.lng, tbl_school3.s_status, tbl_school3.level,
        tbl_adm_country.`name` AS country, tbl_adm_province.`name` AS province, tbl_adm_regency.`nickname` AS regency, tbl_adm_district.`name` AS district,
        COALESCE((SELECT img FROM tbl_school_image WHERE id_school=tbl_school3.id ORDER BY tbl_school_image.sort ASC LIMIT 1),'') AS img, 
        COALESCE((SELECT COUNT(id) FROM tbl_like_all WHERE liked='1' AND id_ref=tbl_school3.id),0 )AS count_liked, 
        COALESCE(round(SQRT(
        POW(111.111 * (tbl_school3.lat - ('$lat')), 2) +
        POW(111.111 * (('$lng') - tbl_school3.lng) *
        COS(tbl_school3.lat / 57.3), 2)), 1),'0') as distance_in_km
    
        FROM tbl_school3
    
        JOIN tbl_adm_country ON tbl_school3.id_country=tbl_adm_country.id
        JOIN tbl_adm_province ON tbl_school3.id_province=tbl_adm_province.id
        JOIN tbl_adm_regency ON tbl_school3.id_regency=tbl_adm_regency.id
        JOIN tbl_adm_district ON tbl_school3.id_district=tbl_adm_district.id
        WHERE 1
        AND tbl_school3.`status`='1'
        AND tbl_school3.id_regency='1410' 
        AND lat <>''

        $q_filter
        $district_filter
        $level_filter
        $status_filter

        $nearby_filter
        
        -- GROUP BY tbl_school3.id
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
                $img = img_profile_school($data_row["level"]);

                $arr1[] = array(
                    'id' => "" . $data_row["id"] . "",
                    'title' => "" . clean_special_characters($data_row["title"]) . "",
                    'distance' => "" . $distance . "",
                    'count_liked' => (int) $data_row["count_liked"],
                    's_status' => $data_row["s_status"],
                    'level' => $data_row["level"],
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

    public function viewSchool(Request $request, Response $response, array $parm)
    {

        $id_user = CustomRequestHandler::getParam($request, "id_user", '');
        $lat = CustomRequestHandler::getParam($request, "lat", '');
        $lng = CustomRequestHandler::getParam($request, "lng", '');

        $arr1 = null;

        $sql = "SELECT tbl_school3.id,  COUNT(*) OVER() AS total_records, tbl_school3.name AS title, tbl_school3.lat, tbl_school3.lng, tbl_school3.s_status, tbl_school3.level, tbl_school3.address,
        tbl_school3.address_detail, tbl_school3.npsn, tbl_school3.shade, tbl_school3.founding_date, tbl_school3.sk_foundment_no, tbl_school3.operational_date,
        tbl_school3.sk_operational_no, tbl_school3.accreditation, tbl_school3.accreditation_date, tbl_school3.sk_accreditation_no, tbl_school3.certification,
        tbl_school3.phone, tbl_school3.fax, tbl_school3.email, tbl_school3.website, tbl_school3.headmaster, tbl_school3.operator,
        tbl_school3.village,
        tbl_adm_country.`name` AS country, tbl_adm_province.`name` AS province, tbl_adm_regency.`nickname` AS regency, tbl_adm_district.`name` AS district,
        COALESCE((SELECT img FROM tbl_school_image WHERE id_school=tbl_school3.id ORDER BY tbl_school_image.sort ASC LIMIT 1),'') AS img, 
        COALESCE((SELECT tbl_like_all.liked FROM tbl_like_all WHERE id_user='$id_user' AND id_ref=tbl_school3.id),0 )AS liked,
        COALESCE((SELECT COUNT(id) FROM tbl_like_all WHERE liked='1' AND id_ref=tbl_school3.id),0 )AS count_liked, 
        COALESCE(round(SQRT(
        POW(111.111 * (tbl_school3.lat - ('$lat')), 2) +
        POW(111.111 * (('$lng') - tbl_school3.lng) *
        COS(tbl_school3.lat / 57.3), 2)), 1),'0') as distance_in_km
    
        FROM tbl_school3
    
        JOIN tbl_adm_country ON tbl_school3.id_country=tbl_adm_country.id
        JOIN tbl_adm_province ON tbl_school3.id_province=tbl_adm_province.id
        JOIN tbl_adm_regency ON tbl_school3.id_regency=tbl_adm_regency.id
        JOIN tbl_adm_district ON tbl_school3.id_district=tbl_adm_district.id
        WHERE 1
        AND tbl_school3.`status`='1'
        AND tbl_school3.id='$parm[slug]'
        ";

        $result = $this->dbHandler->getDataAll($sql);
        if (
            ($result->rowCount()) > 0
        ) {

            $data_row = $result->fetch(PDO::FETCH_ASSOC);

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
            $img = img_profile_school($data_row["level"]);

            $arr1 = array(
                'id' => "" . $data_row["id"] . "",
                'title' => "" . clean_special_characters($data_row["title"]) . "",
                'distance' => "" . $distance . "",
                'liked' => (int) $data_row["liked"],
                'count_liked' => (int) $data_row["count_liked"],
                's_status' => $data_row["s_status"],
                'level' => $data_row["level"],
                'country' => "" . $data_row["country"] . "",
                'province' => "" . $data_row["province"] . "",
                'regency' => "" . $data_row["regency"] . "",
                'district' => "" . $data_row["district"] . "",
                'village' => "" . $data_row["village"] . "",
                'address' => "" . $data_row["address"] . "",
                'address_detail' => "" . $data_row["address_detail"] . "",
                'npsn' => "" . $data_row["npsn"] . "",
                'shade' => "" . $data_row["shade"] . "",
                'founding_date' => "" . $data_row["founding_date"] . "",
                'sk_foundment_no' => "" . $data_row["sk_foundment_no"] . "",
                'operational_date' => "" . $data_row["operational_date"] . "",
                'sk_operational_no' => "" . $data_row["sk_operational_no"] . "",
                'accreditation' => "" . $data_row["accreditation"] . "",
                'accreditation_date' => "" . $data_row["accreditation_date"] . "",
                'sk_accreditation_no' => "" . $data_row["sk_accreditation_no"] . "",
                'certification' => "" . $data_row["certification"] . "",
                'phone' => "" . $data_row["phone"] . "",
                'fax' => "" . $data_row["fax"] . "",
                'email' => "" . $data_row["email"] . "",
                'website' => "" . $data_row["website"] . "",
                'headmaster' => "" . $data_row["headmaster"] . "",
                'operator' => "" . $data_row["operator"] . "",
                'lat' => (float) $data_row["lat"],
                'lng' => (float) $data_row["lng"],
                'img' => "" . $img,
            );

            return $this->customResponse->is200Response($response, $arr1);
        } else {
            // code...
            return $this->customResponse->is404Response($response, "faild");
        }
    }

    public function getSchoolLevel(Request $request, Response $response, array $parm)
    {

        $arr1 = array();

        // $id_regency = (empty(CustomRequestHandler::getParam($request, "id_regency"))) ? '' : CustomRequestHandler::getParam($request, "id_regency");

        $sql = "SELECT `level` FROM tbl_school3 
        WHERE 1
        -- AND id_regency='$parm[id_regency]'
        AND tbl_school3.`status`='1'
        AND tbl_school3.id_regency='1410' 
        AND lat <>''
        GROUP BY `level`  
        ";

        $result = $this->dbHandler->getDataAll($sql);
        $total = $result->rowCount();
        if ($total > 0) {
            // code...

            $result = $this->dbHandler->getDataAll($sql);

            while ($data_row = $result->fetch(PDO::FETCH_ASSOC)) {

                $arr1[] = array(
                    'id' => "" . $data_row["level"] . "",
                    'title' => "" . clean_special_characters($data_row["level"]) . "",
                    'slug' => "" . clean_special_characters($data_row["level"]) . "",
                );
            }
        }

        return $this->customResponse->is200Response($response, $arr1);
    }

    public function getSchoolStatus(Request $request, Response $response, array $parm)
    {

        $arr1 = array();

        // $id_regency = (empty(CustomRequestHandler::getParam($request, "id_regency"))) ? '' : CustomRequestHandler::getParam($request, "id_regency");

        $sql = "SELECT `s_status` FROM tbl_school3 
        WHERE 1
        -- AND id_regency='$parm[id_regency]'
        GROUP BY `s_status`  
        ";

        $result = $this->dbHandler->getDataAll($sql);
        $total = $result->rowCount();
        if ($total > 0) {
            // code...

            $result = $this->dbHandler->getDataAll($sql);

            while ($data_row = $result->fetch(PDO::FETCH_ASSOC)) {

                $arr1[] = array(
                    'id' => "" . $data_row["s_status"] . "",
                    'title' => "" . clean_special_characters($data_row["s_status"]) . "",
                    'slug' => "" . clean_special_characters($data_row["s_status"]) . "",
                );
            }
        }

        return $this->customResponse->is200Response($response, $arr1);
    }
}
