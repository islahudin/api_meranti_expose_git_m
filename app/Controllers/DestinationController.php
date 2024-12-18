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



class DestinationController
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

	public function getDestinationLikePg(Request $request, Response $response)
	{
		$total = 0;
		$page = 0;
		$pages = 0;

		$arr1 = array();

		$id_user = CustomRequestHandler::getParam($request, "id_user", "");
		$filter = CustomRequestHandler::getParam($request, "filter", "");
		$lat = CustomRequestHandler::getParam($request, "lat", "");
		$lng = CustomRequestHandler::getParam($request, "lng", "");

		$page = (int) CustomRequestHandler::getParam($request, "page", 1);
		$per_page = (int) CustomRequestHandler::getParam($request, "per_page", 1);

		if (!empty($filter)) {
			// code...
			if (stringContains($filter, ',')) {
				// code...
				$_data = $filter;
				$new_arr = array_map('trim', explode(',', $_data));
				$imploded_data = implode("','", $new_arr);

				$filter_filter = "AND type IN ('$imploded_data')";
			} else {
				// code...
				$filter_filter = "AND type LIKE '%$filter%'";
			}
		} else {
			// code...
			$filter_filter = "";
		}

		$per_page = min(max($per_page, 5), 20);

		// Calculate the offset
		$offset = ($page - 1) * $per_page;

		// Fetch the total records and paginated results
		$sql = "SELECT COUNT(*) OVER() AS total_records, tbl_like_all.* FROM tbl_like_all 
		WHERE 1
		AND id_user='$id_user'
        AND `liked`='1'
        -- AND `type`='tour'
        $filter_filter
        ORDER BY created_at DESC
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

				$id_ref = $data_row["id_ref"];
				$type = $data_row["type"];
				$created_at = $data_row["created_at"];

				if ($type == "tour") {

					$sql2 = "SELECT tbl_tour.id, tbl_tour.title, tbl_tour.slug, tbl_tour.lat, tbl_tour.lng, tbl_tour.rating, 
                    tbl_adm_country.`name` AS country, tbl_adm_province.`name` AS province, tbl_adm_regency.`nickname` AS regency, tbl_adm_district.`name` AS district,
                    COALESCE((SELECT img FROM tbl_tour_image WHERE id_tour=tbl_tour.id ORDER BY tbl_tour_image.sort ASC LIMIT 1),'') AS img, 
                    COALESCE((SELECT COUNT(id) FROM tbl_like_all WHERE liked='1' AND id_ref=tbl_tour.id),0 )AS count_liked, 
                    COALESCE(round(SQRT(
                    POW(111.111 * (tbl_tour.lat - ('$lat')), 2) +
                    POW(111.111 * (('$lng') - tbl_tour.lng) *
                    COS(tbl_tour.lat / 57.3), 2)), 1),'0') as distance_in_km
                
                    FROM tbl_tour, tbl_adm_country, tbl_adm_province,tbl_adm_regency,tbl_adm_district, tbl_tour_category, tbl_tour_subcategory
                
                    WHERE  tbl_tour.id_tour_subcategory=tbl_tour_subcategory.id
                    AND tbl_tour_subcategory.id_tour_category=tbl_tour_category.id
                    AND tbl_tour.id_country=tbl_adm_country.id
                    AND tbl_tour.id_province=tbl_adm_province.id
                    AND tbl_tour.id_regency=tbl_adm_regency.id
                    AND tbl_tour.id_district=tbl_adm_district.id
                    AND tbl_tour.`status`='1'
                    AND tbl_tour.id_regency='1410' 
                    AND tbl_tour.id='$id_ref'
                    
                    GROUP BY tbl_tour.id

                    ";

					$result2 = $this->dbHandler->getDataAll($sql2);
					if (($result2->rowCount()) > 0) {

						while ($data_row2 = $result2->fetch(PDO::FETCH_ASSOC)) {

							if (($lat != "") || ($lng != "")) {
								$distance = $data_row2["distance_in_km"];
							} else {
								// code...
								$distance = "0";
							}

							$banner = img_banner_expen($data_row2["img"]);
							$file_name = pathinfo($data_row2["img"], PATHINFO_FILENAME);
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
								'id_ref' => "" . $id_ref . "",
								'title' => "" . clean_special_characters($data_row2["title"]) . "",
								'distance' => "" . $distance . "",
								'count_liked' => (int) $data_row2["count_liked"],
								'rating' => (float) $data_row2["rating"],
								'slug' => "" . $data_row2["slug"] . "",
								'link' => url_link_event() . "" . $data_row2["slug"] . "",
								'country' => "" . $data_row2["country"] . "",
								'province' => "" . $data_row2["province"] . "",
								'regency' => "" . $data_row2["regency"] . "",
								'district' => "" . $data_row2["district"] . "",
								'type' => "" . $type . "",
								'created_at' => "" . $created_at . "",
								'img' => "" . $img,
							);
						}
					}
				} else if ($type == "hotel") {

					$sql2 = "SELECT tbl_hotel.id, tbl_hotel.`name` AS title, tbl_hotel.slug, tbl_hotel.lat, tbl_hotel.lng, tbl_hotel.rating_set AS rating, 
                    tbl_adm_country.`name` AS country, tbl_adm_province.`name` AS province, tbl_adm_regency.`nickname` AS regency, tbl_adm_district.`name` AS district,
                    COALESCE((SELECT img FROM tbl_hotel_image WHERE id_hotel=tbl_hotel.id ORDER BY tbl_hotel_image.sort ASC LIMIT 1),'') AS img, 
                    COALESCE((SELECT COUNT(id) FROM tbl_like_all WHERE liked='1' AND id_ref=tbl_hotel.id),0 )AS count_liked, 
                    COALESCE(round(SQRT(
                    POW(111.111 * (tbl_hotel.lat - ('$lat')), 2) +
                    POW(111.111 * (('$lng') - tbl_hotel.lng) *
                    COS(tbl_hotel.lat / 57.3), 2)), 1),'0') as distance_in_km
                
                    FROM tbl_hotel, tbl_adm_country, tbl_adm_province,tbl_adm_regency,tbl_adm_district
                
                    WHERE 1
                    AND tbl_hotel.id_country=tbl_adm_country.id
                    AND tbl_hotel.id_province=tbl_adm_province.id
                    AND tbl_hotel.id_regency=tbl_adm_regency.id
                    AND tbl_hotel.id_district=tbl_adm_district.id
                    AND tbl_hotel.`status`='1'
                    AND tbl_hotel.id_regency='1410' 
                    AND tbl_hotel.id='$id_ref'
                    
                    GROUP BY tbl_hotel.id

                    ";

					$result2 = $this->dbHandler->getDataAll($sql2);
					if (($result2->rowCount()) > 0) {

						while ($data_row2 = $result2->fetch(PDO::FETCH_ASSOC)) {

							if (($lat != "") || ($lng != "")) {
								$distance = $data_row2["distance_in_km"];
							} else {
								// code...
								$distance = "0";
							}

							$banner = img_banner_expen($data_row2["img"]);
							$file_name = pathinfo($data_row2["img"], PATHINFO_FILENAME);
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
								'id_ref' => "" . $id_ref . "",
								'title' => "" . clean_special_characters($data_row2["title"]) . "",
								'distance' => "" . $distance . "",
								'count_liked' => (int) $data_row2["count_liked"],
								'rating' => (float) $data_row2["rating"],
								'slug' => "" . $data_row2["slug"] . "",
								'link' => url_link_event() . "" . $data_row2["slug"] . "",
								'country' => "" . $data_row2["country"] . "",
								'province' => "" . $data_row2["province"] . "",
								'regency' => "" . $data_row2["regency"] . "",
								'district' => "" . $data_row2["district"] . "",
								'type' => "" . $type . "",
								'created_at' => "" . $created_at . "",
								'img' => "" . $img,
							);
						}
					}
				} else if ($type == "resto") {

					$sql2 = "SELECT tbl_resto.id, tbl_resto.`name` AS title, tbl_resto.slug, tbl_resto.lat, tbl_resto.lng, tbl_resto.rating_set AS rating, 
                    tbl_adm_country.`name` AS country, tbl_adm_province.`name` AS province, tbl_adm_regency.`nickname` AS regency, tbl_adm_district.`name` AS district,
                    COALESCE((SELECT img FROM tbl_resto_image WHERE id_resto=tbl_resto.id ORDER BY tbl_resto_image.sort ASC LIMIT 1),'') AS img, 
                    COALESCE((SELECT COUNT(id) FROM tbl_like_all WHERE liked='1' AND id_ref=tbl_resto.id),0 )AS count_liked, 
                    COALESCE(round(SQRT(
                    POW(111.111 * (tbl_resto.lat - ('$lat')), 2) +
                    POW(111.111 * (('$lng') - tbl_resto.lng) *
                    COS(tbl_resto.lat / 57.3), 2)), 1),'0') as distance_in_km
                
                    FROM tbl_resto, tbl_adm_country, tbl_adm_province,tbl_adm_regency,tbl_adm_district
                
                    WHERE 1
                    AND tbl_resto.id_country=tbl_adm_country.id
                    AND tbl_resto.id_province=tbl_adm_province.id
                    AND tbl_resto.id_regency=tbl_adm_regency.id
                    AND tbl_resto.id_district=tbl_adm_district.id
                    AND tbl_resto.`status`='1'
                    AND tbl_resto.id_regency='1410' 
                    AND tbl_resto.id='$id_ref'
                    
                    GROUP BY tbl_resto.id

                    ";

					$result2 = $this->dbHandler->getDataAll($sql2);
					if (($result2->rowCount()) > 0) {

						while ($data_row2 = $result2->fetch(PDO::FETCH_ASSOC)) {

							if (($lat != "") || ($lng != "")) {
								$distance = $data_row2["distance_in_km"];
							} else {
								// code...
								$distance = "0";
							}

							$banner = img_banner_expen($data_row2["img"]);
							$file_name = pathinfo($data_row2["img"], PATHINFO_FILENAME);
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
								'id_ref' => "" . $id_ref . "",
								'title' => "" . clean_special_characters($data_row2["title"]) . "",
								'distance' => "" . $distance . "",
								'count_liked' => (int) $data_row2["count_liked"],
								'rating' => (float) $data_row2["rating"],
								'slug' => "" . $data_row2["slug"] . "",
								'link' => url_link_event() . "" . $data_row2["slug"] . "",
								'country' => "" . $data_row2["country"] . "",
								'province' => "" . $data_row2["province"] . "",
								'regency' => "" . $data_row2["regency"] . "",
								'district' => "" . $data_row2["district"] . "",
								'type' => "" . $type . "",
								'created_at' => "" . $created_at . "",
								'img' => "" . $img,
							);
						}
					}
				} else if ($type == "market") {

					$sql2 = "SELECT tbl_market.id, tbl_market.`name` AS title, tbl_market.id AS slug, tbl_market.lat, tbl_market.lng, tbl_market.rating_set AS rating, 
                    tbl_adm_country.`name` AS country, tbl_adm_province.`name` AS province, tbl_adm_regency.`nickname` AS regency, tbl_adm_district.`name` AS district,
                    COALESCE((SELECT img FROM tbl_market_image WHERE id_market=tbl_market.id ORDER BY tbl_market_image.sort ASC LIMIT 1),'') AS img, 
                    COALESCE((SELECT COUNT(id) FROM tbl_like_all WHERE liked='1' AND id_ref=tbl_market.id),0 )AS count_liked, 
                    COALESCE(round(SQRT(
                    POW(111.111 * (tbl_market.lat - ('$lat')), 2) +
                    POW(111.111 * (('$lng') - tbl_market.lng) *
                    COS(tbl_market.lat / 57.3), 2)), 1),'0') as distance_in_km
                
                    FROM tbl_market, tbl_adm_country, tbl_adm_province,tbl_adm_regency,tbl_adm_district
                
                    WHERE 1
                    AND tbl_market.id_country=tbl_adm_country.id
                    AND tbl_market.id_province=tbl_adm_province.id
                    AND tbl_market.id_regency=tbl_adm_regency.id
                    AND tbl_market.id_district=tbl_adm_district.id
                    AND tbl_market.`status`='1'
                    AND tbl_market.id_regency='1410' 
                    AND tbl_market.id='$id_ref'
                    
                    GROUP BY tbl_market.id

                    ";

					$result2 = $this->dbHandler->getDataAll($sql2);
					if (($result2->rowCount()) > 0) {

						while ($data_row2 = $result2->fetch(PDO::FETCH_ASSOC)) {

							if (($lat != "") || ($lng != "")) {
								$distance = $data_row2["distance_in_km"];
							} else {
								// code...
								$distance = "0";
							}

							$banner = img_banner_expen($data_row2["img"]);
							$file_name = pathinfo($data_row2["img"], PATHINFO_FILENAME);
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
								'id_ref' => "" . $id_ref . "",
								'title' => "" . clean_special_characters($data_row2["title"]) . "",
								'distance' => "" . $distance . "",
								'count_liked' => (int) $data_row2["count_liked"],
								'rating' => (float) $data_row2["rating"],
								'slug' => "" . $data_row2["slug"] . "",
								'link' => url_link_event() . "" . $data_row2["slug"] . "",
								'country' => "" . $data_row2["country"] . "",
								'province' => "" . $data_row2["province"] . "",
								'regency' => "" . $data_row2["regency"] . "",
								'district' => "" . $data_row2["district"] . "",
								'type' => "" . $type . "",
								'created_at' => "" . $created_at . "",
								'img' => "" . $img,
							);
						}
					}
				} else if ($type == "mosque") {

					$sql2 = "SELECT tbl_mosque.id, tbl_mosque.`name` AS title, tbl_mosque.id AS slug, tbl_mosque.lat, tbl_mosque.lng, tbl_mosque.rating_set AS rating, tbl_mosque.image AS img,
                    tbl_adm_country.`name` AS country, tbl_adm_province.`name` AS province, tbl_adm_regency.`nickname` AS regency, tbl_adm_district.`name` AS district,
                    COALESCE((SELECT COUNT(id) FROM tbl_like_all WHERE liked='1' AND id_ref=tbl_mosque.id),0 )AS count_liked, 
                    COALESCE(round(SQRT(
                    POW(111.111 * (tbl_mosque.lat - ('$lat')), 2) +
                    POW(111.111 * (('$lng') - tbl_mosque.lng) *
                    COS(tbl_mosque.lat / 57.3), 2)), 1),'0') as distance_in_km
                
                    FROM tbl_mosque, tbl_adm_country, tbl_adm_province,tbl_adm_regency,tbl_adm_district
                
                    WHERE 1
                    AND tbl_mosque.id_country=tbl_adm_country.id
                    AND tbl_mosque.id_province=tbl_adm_province.id
                    AND tbl_mosque.id_regency=tbl_adm_regency.id
                    AND tbl_mosque.id_district=tbl_adm_district.id
                    AND tbl_mosque.`status`='1'
                    AND tbl_mosque.id_regency='1410' 
                    AND tbl_mosque.id='$id_ref'
                    
                    GROUP BY tbl_mosque.id

                    ";

					$result2 = $this->dbHandler->getDataAll($sql2);
					if (($result2->rowCount()) > 0) {

						while ($data_row2 = $result2->fetch(PDO::FETCH_ASSOC)) {

							if (($lat != "") || ($lng != "")) {
								$distance = $data_row2["distance_in_km"];
							} else {
								// code...
								$distance = "0";
							}

							$banner = img_banner_expen($data_row2["img"]);
							$file_name = pathinfo($data_row2["img"], PATHINFO_FILENAME);
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
								'id_ref' => "" . $id_ref . "",
								'title' => "" . clean_special_characters($data_row2["title"]) . "",
								'distance' => "" . $distance . "",
								'count_liked' => (int) $data_row2["count_liked"],
								'rating' => (float) $data_row2["rating"],
								'slug' => "" . $data_row2["slug"] . "",
								'link' => url_link_event() . "" . $data_row2["slug"] . "",
								'country' => "" . $data_row2["country"] . "",
								'province' => "" . $data_row2["province"] . "",
								'regency' => "" . $data_row2["regency"] . "",
								'district' => "" . $data_row2["district"] . "",
								'type' => "" . $type . "",
								'created_at' => "" . $created_at . "",
								'img' => "" . $img,
							);
						}
					}
				} else if ($type == "school") {

					$sql2 = "SELECT tbl_school3.id, tbl_school3.`name` AS title, tbl_school3.id AS slug, tbl_school3.lat, tbl_school3.lng, tbl_school3.rating_set AS rating, tbl_school3.`level` AS img,
                    tbl_adm_country.`name` AS country, tbl_adm_province.`name` AS province, tbl_adm_regency.`nickname` AS regency, tbl_adm_district.`name` AS district,
                    COALESCE((SELECT COUNT(id) FROM tbl_like_all WHERE liked='1' AND id_ref=tbl_school3.id),0 )AS count_liked, 
                    COALESCE(round(SQRT(
                    POW(111.111 * (tbl_school3.lat - ('$lat')), 2) +
                    POW(111.111 * (('$lng') - tbl_school3.lng) *
                    COS(tbl_school3.lat / 57.3), 2)), 1),'0') as distance_in_km
                
                    FROM tbl_school3, tbl_adm_country, tbl_adm_province,tbl_adm_regency,tbl_adm_district
                
                    WHERE 1
                    AND tbl_school3.id_country=tbl_adm_country.id
                    AND tbl_school3.id_province=tbl_adm_province.id
                    AND tbl_school3.id_regency=tbl_adm_regency.id
                    AND tbl_school3.id_district=tbl_adm_district.id
                    AND tbl_school3.`status`='1'
                    AND tbl_school3.id_regency='1410' 
                    AND tbl_school3.id='$id_ref'
                    
                    GROUP BY tbl_school3.id

                    ";

					$result2 = $this->dbHandler->getDataAll($sql2);
					if (($result2->rowCount()) > 0) {

						while ($data_row2 = $result2->fetch(PDO::FETCH_ASSOC)) {

							if (($lat != "") || ($lng != "")) {
								$distance = $data_row2["distance_in_km"];
							} else {
								// code...
								$distance = "0";
							}

							$banner = img_banner_expen($data_row2["img"]);
							$file_name = pathinfo($data_row2["img"], PATHINFO_FILENAME);
							$filepath = "./../images/img_temp/" . $file_name . ".jpg";
							// $commp = compress_img($banner, $filepath, 50);
							// if ($commp == $banner) { //
							//     // code...
							//     $img = $commp;
							// } else {
							//     // code...
							//     $img = get_root_uri() . "images/img_temp/" . $commp;
							// }
							// $img = $banner;
							$img = img_profile_school($data_row["img"]);

							$arr1[] = array(
								'id_ref' => "" . $id_ref . "",
								'title' => "" . clean_special_characters($data_row2["title"]) . "",
								'distance' => "" . $distance . "",
								'count_liked' => (int) $data_row2["count_liked"],
								'rating' => (float) $data_row2["rating"],
								'slug' => "" . $data_row2["slug"] . "",
								'link' => url_link_event() . "" . $data_row2["slug"] . "",
								'country' => "" . $data_row2["country"] . "",
								'province' => "" . $data_row2["province"] . "",
								'regency' => "" . $data_row2["regency"] . "",
								'district' => "" . $data_row2["district"] . "",
								'type' => "" . $type . "",
								'created_at' => "" . $created_at . "",
								'img' => "" . $img,
							);
						}
					}
				}else {

					$sql2 = "SELECT tbl_public_place.id, tbl_public_place.`name` AS title, tbl_public_place.slug, tbl_public_place.lat, tbl_public_place.lng, tbl_public_place.rating_set AS rating, 
                    tbl_adm_country.`name` AS country, tbl_adm_province.`name` AS province, tbl_adm_regency.`nickname` AS regency, tbl_adm_district.`name` AS district,
                    COALESCE((SELECT img FROM tbl_public_place_image WHERE id_ref=tbl_public_place.place_id ORDER BY tbl_public_place_image.sort ASC LIMIT 1),'') AS img, 
                    COALESCE((SELECT COUNT(id) FROM tbl_like_all WHERE liked='1' AND id_ref=tbl_public_place.id),0 )AS count_liked, 
                    COALESCE(round(SQRT(
                    POW(111.111 * (tbl_public_place.lat - ('$lat')), 2) +
                    POW(111.111 * (('$lng') - tbl_public_place.lng) *
                    COS(tbl_public_place.lat / 57.3), 2)), 1),'0') as distance_in_km
                
                    FROM tbl_public_place, tbl_adm_country, tbl_adm_province,tbl_adm_regency,tbl_adm_district
                
                    WHERE 1
                    AND tbl_public_place.id_country=tbl_adm_country.id
                    AND tbl_public_place.id_province=tbl_adm_province.id
                    AND tbl_public_place.id_regency=tbl_adm_regency.id
                    AND tbl_public_place.id_district=tbl_adm_district.id
                    AND tbl_public_place.`status`='1'
                    AND tbl_public_place.id_regency='1410' 
                    AND tbl_public_place.id='$id_ref'
                    
                    GROUP BY tbl_public_place.id

                    ";

					$result2 = $this->dbHandler->getDataAll($sql2);
					if (($result2->rowCount()) > 0) {

						while ($data_row2 = $result2->fetch(PDO::FETCH_ASSOC)) {

							if (($lat != "") || ($lng != "")) {
								$distance = $data_row2["distance_in_km"];
							} else {
								// code...
								$distance = "0";
							}

							$img = $data_row2["img"];

							$arr1[] = array(
								'id_ref' => "" . $id_ref . "",
								'title' => "" . clean_special_characters($data_row2["title"]) . "",
								'distance' => "" . $distance . "",
								'count_liked' => (int) $data_row2["count_liked"],
								'rating' => (float) $data_row2["rating"],
								'slug' => "" . $data_row2["slug"] . "",
								'link' => url_link_event() . "" . $data_row2["slug"] . "",
								'country' => "" . $data_row2["country"] . "",
								'province' => "" . $data_row2["province"] . "",
								'regency' => "" . $data_row2["regency"] . "",
								'district' => "" . $data_row2["district"] . "",
								'type' => "" . $type . "",
								'created_at' => "" . $created_at . "",
								'img' => "" . $img,
							);
						}
					}
				}
			}

			$pages = (int) ceil($total / $per_page);
		}

		$rPagination["total"] = $total;
		$rPagination["page"] = (int) $page;
		$rPagination["pages"] = $pages;
		$rPagination["per_page"] = (int) $per_page;

		return $this->customResponse->is200Response2($response, $arr1, rPagination: $rPagination);
	}

	public function DestinationLikeSave(Request $request, Response $response)
	{

		$arr1 = null;

		$this->validator->validate($request, [
			"id_ref" => v::notEmpty(),
			"id_user" => v::notEmpty(),
			"id_like" => v::notEmpty(),
			"type" => v::notEmpty(),
		]);

		if ($this->validator->failed()) {
			$responseMessage = $this->validator->errors;
			return $this->customResponse->is400Response($response, $responseMessage);
		}

		$created_at = date('Y-m-d H:i:s');

		$id_ref = CustomRequestHandler::getParam($request, "id_ref");
		$id_user = CustomRequestHandler::getParam($request, "id_user");
		$id_like = CustomRequestHandler::getParam($request, "id_like");
		$type = CustomRequestHandler::getParam($request, "type");

		$sqlCek = null;
		if ($type == "tour") {
			$sqlCek = "SELECT * FROM tbl_tour
			WHERE id='$id_ref' AND status='1'
			";
		} else if ($type == "mosque") {
			$sqlCek = "SELECT * FROM tbl_mosque
			WHERE id='$id_ref' AND status='1'
			";
		} else if ($type == "market") {
			$sqlCek = "SELECT * FROM tbl_market
			WHERE id='$id_ref' AND status='1'
			";
		} else if ($type == "resto") {
			$sqlCek = "SELECT * FROM tbl_resto
			WHERE id='$id_ref' AND status='1'
			";
		} else if ($type == "school") {
			$sqlCek = "SELECT * FROM tbl_school3
			WHERE id='$id_ref' AND status='1'
			";
		} else if ($type == "hotel") {
			$sqlCek = "SELECT * FROM tbl_hotel
			WHERE id='$id_ref' AND status='1'
			";
		} else {
			$sqlCek = "SELECT * FROM tbl_public_place
			WHERE id='$id_ref' AND status='1'
			AND type ='$type'
			";
		}

		if ($sqlCek != null) {
			$resultCek = $this->dbHandler->getDataAll($sqlCek);
			if (($resultCek->rowCount()) > 0) {

				$sql = "
				SELECT * FROM tbl_like_all
				WHERE id_ref='$id_ref'
				AND id_user='$id_user'
				AND type='$type'
				";
				$result = $this->dbHandler->getDataAll($sql);
				if (($result->rowCount()) > 0) {

					$data_row = $result->fetch(PDO::FETCH_ASSOC);

					$liked = $data_row["liked"];

					if ($liked == "0") {
						// code...
						$sql = "
						UPDATE tbl_like_all
						SET liked='1',
						updated_at='$created_at'
						WHERE id_ref='$id_ref'
						AND id_user='$id_user'
						";
						$result = $this->dbHandler->updateDataAll($sql);
						if ($result) {
							// code...
							$arr1 = array(
								'liked' => "1",

							);

							return $this->customResponse->is200Response($response, $arr1);
						} else {
							return $this->customResponse->is404Response($response, "Gagal diupdate!");
						}
					} else {
						// code...
						$sql = "
						UPDATE tbl_like_all
						SET liked='0',
						updated_at='$created_at'
						WHERE id_ref='$id_ref'
						AND id_user='$id_user'
						";
						$result = $this->dbHandler->updateDataAll($sql);
						if ($result) {
							// code...
							$arr1 = array(
								'liked' => "0",

							);

							return $this->customResponse->is200Response($response, $arr1);
						} else {
							return $this->customResponse->is404Response($response, "Gagal diupdate!");
						}
					}
				} else {

					$sqlInsert = "
					insert into tbl_like_all set
					id='$id_like',
					id_user='$id_user',
					id_ref='$id_ref',
					liked='1',
					type='$type',
					created_at='$created_at';
					";

					$result = $this->dbHandler->insertDataAll($sqlInsert);
					if ($result) {
						$arr1 = array(
							'liked' => "1",
						);

						return $this->customResponse->is200Response($response, $arr1);
					} else {
						// code...
						return $this->customResponse->is404Response($response, "Faild insert!");
					}
				}
			} else {
				return $this->customResponse->is404Response($response, "service not found!");
			}

		} else {
			return $this->customResponse->is404Response($response, "type service not found!");
		}


	}

	public function getDestinationFind(Request $request, Response $response)
	{

		$arr1 = array();
		$per_page = (int) (CustomRequestHandler::getParam($request, "per_page", 1));
		$sort = (CustomRequestHandler::getParam($request, "sort", ''));
		$lat = (CustomRequestHandler::getParam($request, "lat", ''));
		$lng = (CustomRequestHandler::getParam($request, "lng", ''));
		$subcategory = (CustomRequestHandler::getParam($request, "subcategory", ''));
		$district = (CustomRequestHandler::getParam($request, "district", ''));
		$q = (CustomRequestHandler::getParam($request, "q", ''));

		// Define the attributes to check
		$attributes = [
			"tour" => $this->getTour($per_page,$q,$subcategory,$district,$lat, $lng, $sort),
			"resto" => $this->getResto($per_page,$q,$subcategory,$district,$lat, $lng, $sort),
			"hotel" => $this->getHotel($per_page,$q,$subcategory,$district,$lat, $lng, $sort),
			"market" => $this->getMarket($per_page,$q,$subcategory,$district,$lat, $lng, $sort),
			"mosque" => $this->getMosque($per_page,$q,$subcategory,$district,$lat, $lng, $sort),
			"school" => $this->getSchool($per_page,$q,$subcategory,$district,$lat, $lng, $sort),
		];

		// Filter attributes to exclude null or empty values
		$filteredAttributes = array_filter($attributes, function ($value) {
			return !empty($value); // Keep values that are not null or empty
		});

		// Format the result as required
		$result = [];
		foreach ($filteredAttributes as $key => $value) {
			$result[] = [
				"name" => strtolower($key), // Convert key to lowercase
				"data_list" => $value    // Wrap the value in an array
			];
		}


		return $this->customResponse->is200Response($response, $result);
	}


	private function getTour($per_page = "", $q = "", $subcategory = "", $district = "", $lat = "", $lng = "", $sort = "")
	{
		$arr1 = array();

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

		// Fetch the total records and paginated results
		$sql = "SELECT tbl_tour.id AS id, COUNT(*) OVER() AS total_records, tbl_tour.title, tbl_tour.slug, tbl_tour.lat, tbl_tour.lng, tbl_tour.rating, tbl_tour.detail_address AS address,
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
        LIMIT $per_page
        ";

		$result = $this->dbHandler->getDataAll($sql);
		if ($result) {

			while ($data_row = $result->fetch(PDO::FETCH_ASSOC)) {

				if (($lat != "") || ($lng != "")) {
					$distance = $data_row["distance_in_km"];
				} else {
					// code...
					$distance = "0";
				}

				$banner = img_banner_expen($data_row["img"]);

				$img = $banner;

				$arr1[] = array(
					'id' => "" . $data_row["id"] . "",
					'title' => "" . clean_special_characters($data_row["title"]) . "",
					'distance' => (float) $distance,
					'count_liked' => (int) $data_row["count_liked"],
					'subtitle' => "",
					'rating' => (float) $data_row["rating"],
					'slug' => "" . $data_row["slug"] . "",
					'link' => url_link_event() . "" . $data_row["slug"] . "",
					'label' => "",
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
		}

		return $arr1;
	}

	private function getResto($per_page = "", $q = "", $subcategory = "", $district = "", $lat = "", $lng = "", $sort = "")
	{
		$arr1 = array();

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
			$q_filter = " AND ((tbl_resto.name LIKE '%$q%') OR (tbl_adm_district.`name` LIKE '%$q%'))";
		} else {
			$q_filter = "";
		}

		$per_page = min(max($per_page, 5), 20);

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
        LIMIT $per_page
        ";

		$result = $this->dbHandler->getDataAll($sql);
		if ($result) {

			while ($data_row = $result->fetch(PDO::FETCH_ASSOC)) {

				if (($lat != "") || ($lng != "")) {
					$distance = $data_row["distance_in_km"];
				} else {
					// code...
					$distance = "0";
				}

				$banner = img_banner_expen($data_row["img"]);

				$img = $banner;

				$arr1[] = array(
					'id' => "" . $data_row["id"] . "",
					'title' => "" . clean_special_characters($data_row["title"]) . "",
					'distance' => (float) $distance,
					'count_liked' => (int) $data_row["count_liked"],
					'subtitle' => "",
					'rating' => (float) $data_row["rating"],
					'slug' => "" . $data_row["slug"] . "",
					'link' => url_link_event() . "" . $data_row["slug"] . "",
					'label' => "",
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
		}

		return $arr1;
	}

	private function getHotel($per_page = "", $q = "", $subcategory = "", $district = "", $lat = "", $lng = "", $sort = "")
	{
		$arr1 = array();

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
            $q_filter = " AND ((tbl_hotel.name LIKE '%$q%') OR (tbl_adm_district.`name` LIKE '%$q%'))";
        } else {
            $q_filter = "";
        }

        $per_page = min(max($per_page, 5), 20);

        // Fetch the total records and paginated results
        $sql = "SELECT tbl_hotel.id, COUNT(*) OVER() AS total_records, tbl_hotel.name AS title, tbl_hotel.lat, tbl_hotel.slug, tbl_hotel.lng, tbl_hotel.phone, tbl_hotel.rating_set, 
        tbl_hotel.`level`, tbl_hotel.check_in, tbl_hotel.check_out, tbl_hotel.facility, tbl_hotel.address, 
        tbl_adm_country.`name` AS country, tbl_adm_province.`name` AS province, tbl_adm_regency.`nickname` AS regency, tbl_adm_district.`name` AS district,
        COALESCE((SELECT img FROM tbl_hotel_image WHERE id_hotel=tbl_hotel.id AND `status` ='1' ORDER BY tbl_hotel_image.sort ASC LIMIT 1),'') AS img, 
        COALESCE((SELECT COUNT(id) FROM tbl_like_all WHERE liked='1' AND id_ref=tbl_hotel.id),0 )AS count_liked, 
        COALESCE(round(SQRT(
        POW(111.111 * (tbl_hotel.lat - ('$lat')), 2) +
        POW(111.111 * (('$lng') - tbl_hotel.lng) *
        COS(tbl_hotel.lat / 57.3), 2)), 1),'0') as distance_in_km
    
        FROM tbl_hotel
    
        JOIN tbl_adm_country ON tbl_hotel.id_country=tbl_adm_country.id
        JOIN tbl_adm_province ON tbl_hotel.id_province=tbl_adm_province.id
        JOIN tbl_adm_regency ON tbl_hotel.id_regency=tbl_adm_regency.id
        JOIN tbl_adm_district ON tbl_hotel.id_district=tbl_adm_district.id
        WHERE 1
        AND tbl_hotel.`status`='1'
        AND tbl_hotel.id_regency='1410' 

        $q_filter
        $district_filter
        
        GROUP BY tbl_hotel.id
        LIMIT $per_page
        ";

		$result = $this->dbHandler->getDataAll($sql);
		if ($result) {

			while ($data_row = $result->fetch(PDO::FETCH_ASSOC)) {

				if (($lat != "") || ($lng != "")) {
					$distance = $data_row["distance_in_km"];
				} else {
					// code...
					$distance = "0";
				}

				$banner = img_banner_expen($data_row["img"]);

				$img = $banner;

				$arr1[] = array(
					'id' => "" . $data_row["id"] . "",
					'title' => "" . clean_special_characters($data_row["title"]) . "",
					'distance' => (float) $distance,
					'count_liked' => (int) $data_row["count_liked"],
					'subtitle' => "",
					'rating' => (float) $data_row["rating"],
					'slug' => "" . $data_row["slug"] . "",
					'link' => url_link_event() . "" . $data_row["slug"] . "",
					'label' => "",
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
		}

		return $arr1;
	}

	private function getMarket($per_page = "", $q = "", $subcategory = "", $district = "", $lat = "", $lng = "", $sort = "")
	{
		$arr1 = array();

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
        LIMIT $per_page
        ";

		$result = $this->dbHandler->getDataAll($sql);
		if ($result) {

			while ($data_row = $result->fetch(PDO::FETCH_ASSOC)) {

				if (($lat != "") || ($lng != "")) {
					$distance = $data_row["distance_in_km"];
				} else {
					// code...
					$distance = "0";
				}

				$banner = img_banner_expen($data_row["img"]);

				$img = $banner;

				$arr1[] = array(
					'id' => "" . $data_row["id"] . "",
					'title' => "" . clean_special_characters($data_row["title"]) . "",
					'distance' => (float) $distance,
					'count_liked' => (int) $data_row["count_liked"],
					'subtitle' => "",
					'rating' => (float) $data_row["rating"],
					'slug' => "" . $data_row["slug"] . "",
					'link' => url_link_event() . "" . $data_row["slug"] . "",
					'label' => "",
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
		}

		return $arr1;
	}
	private function getMosque($per_page = "", $q = "", $subcategory = "", $district = "", $lat = "", $lng = "", $sort = "")
	{
		$arr1 = array();

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
        LIMIT $per_page
        ";

		$result = $this->dbHandler->getDataAll($sql);
		if ($result) {

			while ($data_row = $result->fetch(PDO::FETCH_ASSOC)) {

				if (($lat != "") || ($lng != "")) {
					$distance = $data_row["distance_in_km"];
				} else {
					// code...
					$distance = "0";
				}

				// $banner = img_banner_expen($data_row["img"]);

				$img = $data_row["img_profile"];

				$arr1[] = array(
					'id' => "" . $data_row["id"] . "",
					'title' => "" . clean_special_characters($data_row["title"]) . "",
					'distance' => (float) $distance,
					'count_liked' => (int) $data_row["count_liked"],
					'subtitle' => "",
					'rating' => (float) 0,
					'slug' => "",
					'link' => "",
					'label' => $data_row["typology"],
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
		}

		return $arr1;
	}

	private function getSchool($per_page = "", $q = "", $subcategory = "", $district = "", $lat = "", $lng = "", $sort = "")
	{
		$arr1 = array();

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

        $nearby_filter
        
        -- GROUP BY tbl_school3.id
        LIMIT $per_page
        ";

		$result = $this->dbHandler->getDataAll($sql);
		if ($result) {

			while ($data_row = $result->fetch(PDO::FETCH_ASSOC)) {

				if (($lat != "") || ($lng != "")) {
					$distance = $data_row["distance_in_km"];
				} else {
					// code...
					$distance = "0";
				}

				// $banner = img_banner_expen($data_row["img"]);

				$img = img_profile_school($data_row["level"]);

				$arr1[] = array(
					'id' => "" . $data_row["id"] . "",
					'title' => "" . clean_special_characters($data_row["title"]) . "",
					'distance' => (float) $distance,
					'count_liked' => (int) $data_row["count_liked"],
					'subtitle' => "",
					'rating' => (float) 0,
					'slug' => "",
					'link' => "",
					'label' => $data_row["level"],
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
		}

		return $arr1;
	}
}
