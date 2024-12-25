<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\GuestEntry;
use App\Requests\CustomRequestHandler;
use App\Response\CustomResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;
use Respect\Validation\Validator as v;
use App\Validation\Validator;
use UtilHelper;
// use DbConnect;
use PDO;
use DbHandler;
// $settings = require_once  __DIR__ . "/../../config/settings.php";



class RegisLoginController
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
		$this->dbHandler = new DbHandler();
		$this->utilHelper = new UtilHelper();

		date_default_timezone_set('Asia/Jakarta');
	}

	public function regis(Request $request, Response $response, array $parm)
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

	public function LoginSocialMedia(Request $request, Response $response)
	{

		$arr1 = null;

		$this->validator->validate($request, [
			"id_user" => v::notEmpty(),
			"email" => v::notEmpty()->email(),
			"first_name" => v::notEmpty(),
			"id_tokenfire_user" => v::notEmpty(),
			"tokenfire_user" => v::notEmpty(),
			"api_key_user" => v::notEmpty(),
			"id_device_log" => v::notEmpty(),
			"platform" => v::notEmpty(),
			"uuid" => v::notEmpty(),
			"id_auth" => v::notEmpty(),
			"type_regis" => v::notEmpty(),
		]);

		if ($this->validator->failed()) {
			$responseMessage = $this->validator->errors;
			return $this->customResponse->is400Response($response, $responseMessage);
		}

		$created_at = date('Y-m-d H:i:s');
		$action="register";

		$id_user = substr(CustomRequestHandler::getParam($request, "id_user"), 0, 19);
		$email = CustomRequestHandler::getParam($request, "email");
		$password = $this->utilHelper->hashPassword(CustomRequestHandler::getParam($request, "password"));

		$first_name = CustomRequestHandler::getParam($request, "first_name");
		$last_name = CustomRequestHandler::getParam($request, "last_name");
		$phone = CustomRequestHandler::getParam($request, "phone");

		$id_tokenfire_user = CustomRequestHandler::getParam($request, "id_tokenfire_user");
		$tokenfire_user = CustomRequestHandler::getParam($request, "tokenfire_user");
		$api_key_user = CustomRequestHandler::getParam($request, "api_key_user");


		$android_id = CustomRequestHandler::getParam($request, "android_id");
		$serial_device = CustomRequestHandler::getParam($request, "serial_device");
		$ios_version = CustomRequestHandler::getParam($request, "ios_version");
		$ios_serial_device = CustomRequestHandler::getParam($request, "ios_serial_device");

		$id_device_log = CustomRequestHandler::getParam($request, "id_device_log");
		$info_device = CustomRequestHandler::getParam($request, "info_device");
		$wlan0 = CustomRequestHandler::getParam($request, "wlan0");
		$eth0 = CustomRequestHandler::getParam($request, "eth0");
		$ipv4 = CustomRequestHandler::getParam($request, "ipv4");
		$ipv6 = CustomRequestHandler::getParam($request, "ipv6");
		$wifi = CustomRequestHandler::getParam($request, "wifi");
		$platform = CustomRequestHandler::getParam($request, "platform");
		$imei = CustomRequestHandler::getParam($request, "imei");
		$imsi = CustomRequestHandler::getParam($request, "imsi");
		$uuid = CustomRequestHandler::getParam($request, "uuid");
		$id_auth = CustomRequestHandler::getParam($request, "id_auth");
		$type_regis = CustomRequestHandler::getParam($request, "type_regis");
		$profile_url = CustomRequestHandler::getParam($request, "profile_url");


		// if ($this->EmailExist(CustomRequestHandler::getParam($request, "email"))) {
		//     $responseMessage = "this email already exists";
		//     return $this->customResponse->is400Response($response, $responseMessage);
		// }

		$sql = "SELECT * FROM tbl_users where email='$email'
        ";

		$result = $this->dbHandler->getDataAll($sql);
		if (($result->rowCount()) > 0) {
			$data_row = $result->fetch(PDO::FETCH_ASSOC);
			$status = $data_row['status'];
			$id_user = $data_row['id'];

			$sqlCheck = "SELECT * FROM tbl_user_tokenfire_user where id_user='$id_user' AND tokenfire_user='$tokenfire_user'";
			$resultCheck = $this->dbHandler->getDataAll($sqlCheck);

			if (($resultCheck->rowCount()) > 0) {

				$sqlUpdate = "
				-- UPDATE tbl_user_tokenfire_user SET
				-- `status` = '0',
				-- `updated_at` = '$created_at'
				-- WHERE id_user='$id_user'
				-- AND tokenfire_user NOT IN('$tokenfire_user');

				UPDATE tbl_user_tokenfire_user SET
				`status` = '1',
				`updated_at` = '$created_at'
				WHERE id_user='$id_user'
				AND tokenfire_user='$tokenfire_user';

				";
				$this->dbHandler->updateDataAll($sqlUpdate);
			} else {

				$sqlInsert = "INSERT INTO tbl_user_tokenfire_user set
				id='$id_tokenfire_user',
				id_user='$id_user', tokenfire_user='$tokenfire_user',
				android_id='$android_id',
				serial_device='$serial_device',
				ios_version='$ios_version',
				ios_serial_device='$ios_serial_device',
				imei='$imei',
				imsi='$imsi',
				uuid='$uuid',
				platform='$platform',
				created_at='$created_at',
				status='1';
				";
				$this->dbHandler->insertDataAll($sqlInsert);
			}

			$arr1 = array(
				'id_user' => "" . $data_row['id'] . "",
				'email' => "" . $data_row['email'] . "",
				'first_name' => "" . $data_row['first_name'] . "",
				'last_name' => "" . $data_row['last_name'] . "",
				'password' => "" . $data_row['password'] . "",
				'phone' => "" . $data_row['phone'] . "",
				'verified_phone' => "" . $data_row['verified_phone'] . "",
				'verified_email' => "" . $data_row['verified_email'] . "",
				'photo_profile' => img_user_profile($data_row["photo"]),
				'api_key_user' => "" . $data_row['api_key_user'] . "",
				'created_at' => "" . $data_row['created_at'] . "",
			);

			return $this->customResponse->is200Response($response, $arr1);

		} else {

			$sql1 = "SELECT * FROM tbl_users where id='$id_user'";
			$result1 = $this->dbHandler->getDataAll($sql1);
			if (($result1->rowCount()) == 0) {

				$sqlInsert = "INSERT INTO tbl_users set
				id='$id_user',
				email='$email',

				first_name='$first_name',
				last_name='$last_name',
				phone='$phone',
				`role`='basic',
				created_at='$created_at',
				api_key_user='$api_key_user',
				type_platform='$platform',
				type_regis='$type_regis',
				id_auth='$id_auth',
				photo='$profile_url',
				verified_email='1',
				`status`='1';

				INSERT INTO tbl_user_tokenfire_user set
				id='$id_tokenfire_user',
				id_user='$id_user', tokenfire_user='$tokenfire_user',
				android_id='$android_id',
				serial_device='$serial_device',
				ios_version='$ios_version',
				ios_serial_device='$ios_serial_device',
				imei='$imei',
				imsi='$imsi',
				uuid='$uuid',
				platform='$platform',
				created_at='$created_at',
				`status`='1';
				";

				$result = $this->dbHandler->insertDataAll($sqlInsert);
				if ($result) {

					// if($info_device !=null || $info_device !="" || (!empty($info_device))){
					// 	$sql_check_log = "SELECT id AS id_device_log_regislog_android FROM tbl_user_device_log_regislog_android where id='$id_device_log'";

					// 	$result_check_log = $this->dbHandler->getDataAll($sql_check_log);

					// 	if (($result_check_log->rowCount())<=0) {

						

					// 		$data_data=olah_data_json_login_regislog($info_device,$id_device_log,$id_tokenfire_user,$wlan0,$eth0,$ipv4,$ipv6,$wifi,$action,$created_at,$platform);

					// 		$sqlkirim2 = "INSERT INTO tbl_user_device_log_regislog_android

					// 		(id, id_tokenfire_user, wlan0, eth0, ipv4, ipv6, wifi,`action`, created_at,platform,

					// 		board, brand, device_country_code,

					// 		device_language, device_time_zone, display,fingerprint,

					// 		hardware, host, id_device,imei,

					// 		imsi, manufacturer, model,product,

					// 		`serial`, uuid, version_incremental, version_sdk

					// 		)

					// 		VALUES

					// 		$data_data";

					// 		$result = $this->dbHandler->insertDataAll($sqlkirim2);

					// 	}

					// }

					$arr1 = array(
						'id_user' => "" . $id_user . "",
						'email' => "" . $email . "",
						'first_name' => "" . $first_name . "",
						'last_name' => "" . $last_name . "",
						'password' => "",
						'phone' => "" . $phone . "",
						'verified_phone' => "0",
						'verified_email' => "1",
						'photo_profile' => "" . $profile_url,
						'api_key_user' => "" . $api_key_user . "",
						'created_at' => "" . $created_at . "",
					);

					return $this->customResponse->is200Response($response, $arr1);
				} else {
					return $this->customResponse->is404Response($response, "oops! Coba lagi!");
				}
			} else {
				$data_row1 = $result1->fetch(PDO::FETCH_ASSOC);

				$arr1 = array(
					'id_user' => "" . $data_row1['id'] . "",
					'email' => "" . $data_row1['email'] . "",
					'first_name' => "" . $data_row1['first_name'] . "",
					'last_name' => "" . $data_row1['last_name'] . "",
					'password' => "" . $data_row1['password'] . "",
					'phone' => "" . $data_row1['phone'] . "",
					'verified_phone' => "" . $data_row1['verified_phone'] . "",
					'verified_email' => "" . $data_row1['verified_email'] . "",
					'photo_profile' => img_user_profile($data_row1["photo"]),
					'api_key_user' => "" . $data_row1['api_key_user'] . "",
					'created_at' => "" . $data_row1['created_at'] . "",
				);

				return $this->customResponse->is200Response($response, $arr1);
			}
		}
	}

	public function signUp(Request $request, Response $response)
	{

		$arr1 = null;

		$this->validator->validate($request, [
			"id_user" => v::notEmpty(),
			"email" => v::notEmpty()->email(),
			"first_name" => v::notEmpty(),
			"password" => v::notEmpty(),
			"phone" => v::notEmpty(),
			"id_tokenfire_user" => v::notEmpty(),
			"tokenfire_user" => v::notEmpty(),
			"api_key_user" => v::notEmpty(),
			"id_device_log" => v::notEmpty(),
			"platform" => v::notEmpty(),
			"uuid" => v::notEmpty(),
			"type_regis" => v::notEmpty(),
		]);

		if ($this->validator->failed()) {
			$responseMessage = $this->validator->errors;
			return $this->customResponse->is400Response($response, $responseMessage);
		}

		$created_at = date('Y-m-d H:i:s');
		$action="register";

		$id_user = substr(CustomRequestHandler::getParam($request, "id_user"), 0, 19);
		$email = CustomRequestHandler::getParam($request, "email");
		$password = $this->utilHelper->hashPassword(CustomRequestHandler::getParam($request, "password"));

		$first_name = CustomRequestHandler::getParam($request, "first_name");
		$last_name = CustomRequestHandler::getParam($request, "last_name");
		$phone = CustomRequestHandler::getParam($request, "phone");

		$id_tokenfire_user = CustomRequestHandler::getParam($request, "id_tokenfire_user");
		$tokenfire_user = CustomRequestHandler::getParam($request, "tokenfire_user");
		$api_key_user = CustomRequestHandler::getParam($request, "api_key_user");


		$android_id = CustomRequestHandler::getParam($request, "android_id");
		$serial_device = CustomRequestHandler::getParam($request, "serial_device");
		$ios_version = CustomRequestHandler::getParam($request, "ios_version");
		$ios_serial_device = CustomRequestHandler::getParam($request, "ios_serial_device");

		$id_device_log = CustomRequestHandler::getParam($request, "id_device_log");
		// $info_device=$dataApp['info_device'];
		$info_device = CustomRequestHandler::getParam($request, "info_device");
		$wlan0 = CustomRequestHandler::getParam($request, "wlan0");
		$eth0 = CustomRequestHandler::getParam($request, "eth0");
		$ipv4 = CustomRequestHandler::getParam($request, "ipv4");
		$ipv6 = CustomRequestHandler::getParam($request, "ipv6");
		$wifi = CustomRequestHandler::getParam($request, "wifi");
		$platform = CustomRequestHandler::getParam($request, "platform");
		$imei = CustomRequestHandler::getParam($request, "imei");
		$imsi = CustomRequestHandler::getParam($request, "imsi");
		$uuid = CustomRequestHandler::getParam($request, "uuid");
		$type_regis = CustomRequestHandler::getParam($request, "type_regis");
		$profile_url = CustomRequestHandler::getParam($request, "profile_url");


		// if ($this->EmailExist(CustomRequestHandler::getParam($request, "email"))) {
		//     $responseMessage = "this email already exists";
		//     return $this->customResponse->is400Response($response, $responseMessage);
		// }

		$sql = "SELECT * FROM tbl_users where email='$email'
        ";

		$result = $this->dbHandler->getDataAll($sql);
		if (($result->rowCount()) > 0) {

			$arr1 = null;
			$arrError = array(
				'user' => array(
					'message' => "User already exists."
				)
			);

			return $this->customResponse->is409Response($response, $arrError);

		} else {

			$sql1 = "SELECT * FROM tbl_users where id='$id_user'";
			$result1 = $this->dbHandler->getDataAll($sql1);
			if (($result1->rowCount()) == 0) {

				$sqlInsert = "INSERT INTO tbl_users set
				id='$id_user',
				email='$email',

				first_name='$first_name',
				last_name='$last_name',
				`password`='$password',
				phone='$phone',
				`role`='basic',
				created_at='$created_at',
				api_key_user='$api_key_user',
				type_platform='$platform',
				type_regis='$type_regis',
				photo='$profile_url',
				verified_email='0',
				`status`='1';

				INSERT INTO tbl_user_tokenfire_user set
				id='$id_tokenfire_user',
				id_user='$id_user', tokenfire_user='$tokenfire_user',
				android_id='$android_id',
				serial_device='$serial_device',
				ios_version='$ios_version',
				ios_serial_device='$ios_serial_device',
				imei='$imei',
				imsi='$imsi',
				uuid='$uuid',
				platform='$platform',
				created_at='$created_at',
				`status`='1';
				";

				$result = $this->dbHandler->insertDataAll($sqlInsert);
				if ($result) {

					// if($info_device !=null){
					// 	$sql_check_log = "SELECT id AS id_device_log_regislog_android FROM tbl_user_device_log_regislog_android where id='$id_device_log'";

					// 	$result_check_log = $this->dbHandler->getDataAll($sql_check_log);

					// 	if (($result_check_log->rowCount())<=0) {
							
					// 		$data_data=olah_data_json_login_regislog($info_device,$id_device_log,$id_tokenfire_user,$wlan0,$eth0,$ipv4,$ipv6,$wifi,$action,$created_at,$platform);

					// 		$sqlkirim2 = "INSERT INTO tbl_user_device_log_regislog_android

					// 		(id, id_tokenfire_user, wlan0, eth0, ipv4, ipv6, wifi,`action`, created_at,platform,

					// 		board, brand, device_country_code,

					// 		device_language, device_time_zone, display,fingerprint,

					// 		hardware, host, id_device,imei,

					// 		imsi, manufacturer, model,product,

					// 		`serial`, uuid, version_incremental, version_sdk

					// 		)

					// 		VALUES

					// 		$data_data";

					// 		$result = $this->dbHandler->insertDataAll($sqlkirim2);

					// 	}

					// }

					$arr1 = array(
						'id_user' => "" . $id_user . "",
						'email' => "" . $email . "",
						'first_name' => "" . $first_name . "",
						'last_name' => "" . $last_name . "",
						'password' => "",
						'phone' => "" . $phone . "",
						'verified_phone' => "0",
						'verified_email' => "1",
						'photo_profile' => "" . $profile_url,
						'api_key_user' => "" . $api_key_user . "",
						'created_at' => "" . $created_at . "",
					);

					return $this->customResponse->is200Response($response, $arr1);
				} else {
					return $this->customResponse->is404Response($response, "oops! Coba lagi!");
				}
			} else {
				$data_row1 = $result1->fetch(PDO::FETCH_ASSOC);

				$arr1 = array(
					'id_user' => "" . $data_row1['id'] . "",
					'email' => "" . $data_row1['email'] . "",
					'first_name' => "" . $data_row1['first_name'] . "",
					'last_name' => "" . $data_row1['last_name'] . "",
					'password' => "" . $data_row1['password'] . "",
					'phone' => "" . $data_row1['phone'] . "",
					'verified_phone' => "" . $data_row1['verified_phone'] . "",
					'verified_email' => "" . $data_row1['verified_email'] . "",
					'photo_profile' => img_user_profile($data_row1["photo"]),
					'api_key_user' => "" . $data_row1['api_key_user'] . "",
					'created_at' => "" . $data_row1['created_at'] . "",
				);

				return $this->customResponse->is200Response($response, $arr1);
			}
		}
	}

	public function signIn(Request $request, Response $response)
	{

		$arr1 = null;

		$this->validator->validate($request, [
			"email" => v::notEmpty()->email(),
			"password" => v::notEmpty()->notEmpty(),
			"id_tokenfire_user" => v::notEmpty(),
			"tokenfire_user" => v::notEmpty(),
			"id_device_log" => v::notEmpty(),
			"platform" => v::notEmpty(),
			"uuid" => v::notEmpty(),
		]);

		if ($this->validator->failed()) {
			$responseMessage = $this->validator->errors;
			return $this->customResponse->is400Response($response, $responseMessage);
		}

		$created_at = date('Y-m-d H:i:s');
		$action="login";

		$email = CustomRequestHandler::getParam($request, "email");
		$password = CustomRequestHandler::getParam($request, "password");

		$id_tokenfire_user = CustomRequestHandler::getParam($request, "id_tokenfire_user");
		$tokenfire_user = CustomRequestHandler::getParam($request, "tokenfire_user");

		$android_id = CustomRequestHandler::getParam($request, "android_id");
		$serial_device = CustomRequestHandler::getParam($request, "serial_device");
		$ios_version = CustomRequestHandler::getParam($request, "ios_version");
		$ios_serial_device = CustomRequestHandler::getParam($request, "ios_serial_device");

		$id_device_log = CustomRequestHandler::getParam($request, "id_device_log");
		// $info_device=$dataApp['info_device'];
		$info_device = CustomRequestHandler::getParam($request, "info_device");
		$wlan0 = CustomRequestHandler::getParam($request, "wlan0");
		$eth0 = CustomRequestHandler::getParam($request, "eth0");
		$ipv4 = CustomRequestHandler::getParam($request, "ipv4");
		$ipv6 = CustomRequestHandler::getParam($request, "ipv6");
		$wifi = CustomRequestHandler::getParam($request, "wifi");
		$platform = CustomRequestHandler::getParam($request, "platform");
		$imei = CustomRequestHandler::getParam($request, "imei");
		$imsi = CustomRequestHandler::getParam($request, "imsi");
		$uuid = CustomRequestHandler::getParam($request, "uuid");


		$sql = "SELECT * FROM tbl_users where email='$email'
        ";

		$result = $this->dbHandler->getDataAll($sql);
		if (($result->rowCount()) > 0) {
			$data_row = $result->fetch(PDO::FETCH_ASSOC);
			$status = $data_row['status'];
			$id_user = $data_row['id'];
			$hashPassword = $data_row['password'];

			$verify = password_verify($password, $hashPassword);

			if ($verify == false) {
				$arr1 = null;
				$arrError = array(
					'user' => array(
						'message' => "User not found."
					)
				);

				return $this->customResponse->is404Response($response, $arrError);
				// return false;
			} else {

				$sqlCheck = "SELECT * FROM tbl_user_tokenfire_user where id_user='$id_user' AND tokenfire_user='$tokenfire_user'";
				$resultCheck = $this->dbHandler->getDataAll($sqlCheck);

				if (($resultCheck->rowCount()) > 0) {

					$sqlUpdate = "
					-- UPDATE tbl_user_tokenfire_user SET
					-- `status` = '0',
					-- `updated_at` = '$created_at'
					-- WHERE id_user='$id_user'
					-- AND tokenfire_user NOT IN('$tokenfire_user');

					UPDATE tbl_user_tokenfire_user SET
					`status` = '1',
					`updated_at` = '$created_at'
					WHERE id_user='$id_user'
					AND tokenfire_user='$tokenfire_user';

					";
					$this->dbHandler->updateDataAll($sqlUpdate);
				} else {

					$sqlInsert = "INSERT INTO tbl_user_tokenfire_user set
					id='$id_tokenfire_user',
					id_user='$id_user', tokenfire_user='$tokenfire_user',
					android_id='$android_id',
					serial_device='$serial_device',
					ios_version='$ios_version',
					ios_serial_device='$ios_serial_device',
					imei='$imei',
					imsi='$imsi',
					uuid='$uuid',
					platform='$platform',
					created_at='$created_at',
					`status`='1';
					";
					$this->dbHandler->insertDataAll($sqlInsert);
				}

				// if($info_device !=null){
				// 	$sql_check_log = "SELECT id AS id_device_log_regislog_android FROM tbl_user_device_log_regislog_android where id='$id_device_log'";

				// 	$result_check_log = $this->dbHandler->getDataAll($sql_check_log);

				// 	if (($result_check_log->rowCount())<=0) {
						
				// 		$data_data=olah_data_json_login_regislog($info_device,$id_device_log,$id_tokenfire_user,$wlan0,$eth0,$ipv4,$ipv6,$wifi,$action,$created_at,$platform);

				// 		$sqlkirim2 = "INSERT INTO tbl_user_device_log_regislog_android

				// 		(id, id_tokenfire_user, wlan0, eth0, ipv4, ipv6, wifi,`action`, created_at,platform,

				// 		board, brand, device_country_code,

				// 		device_language, device_time_zone, display,fingerprint,

				// 		hardware, host, id_device,imei,

				// 		imsi, manufacturer, model,product,

				// 		`serial`, uuid, version_incremental, version_sdk

				// 		)

				// 		VALUES

				// 		$data_data";

				// 		$result = $this->dbHandler->insertDataAll($sqlkirim2);

				// 	}

				// }

				$arr1 = array(
					'id_user' => "" . $data_row['id'] . "",
					'email' => "" . $data_row['email'] . "",
					'first_name' => "" . $data_row['first_name'] . "",
					'last_name' => "" . $data_row['last_name'] . "",
					'password' => "" . $data_row['password'] . "",
					'phone' => "" . $data_row['phone'] . "",
					'verified_phone' => "" . $data_row['verified_phone'] . "",
					'verified_email' => "" . $data_row['verified_email'] . "",
					'photo_profile' => img_user_profile($data_row["photo"]),
					'api_key_user' => "" . $data_row['api_key_user'] . "",
					'created_at' => "" . $data_row['created_at'] . "",
				);

				return $this->customResponse->is200Response($response, $arr1);

			}


		} else {

			$arr1 = null;
			$arrError = array(
				'user' => array(
					'message' => "User not found."
				)
			);

			return $this->customResponse->is404Response($response, $arrError);
		}
	}

}
