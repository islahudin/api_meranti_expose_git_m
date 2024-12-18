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



class UserController
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

    public function checkAccount(Request $request, Response $response, array $parm)
    {

        $arr1 = null;

        $this->validator->validate($request, [
            "id_user" => v::notEmpty(),
            "tokenfire_user" => v::notEmpty(),
            "platform" => v::notEmpty(),
            "id_device_log" => v::notEmpty(),
            "uuid" => v::notEmpty(),
        ]);

        if ($this->validator->failed()) {
            $responseMessage = $this->validator->errors;
            return $this->customResponse->is400Response($response, $responseMessage);
        }

        $created_at = date('Y-m-d H:i:s');
        $time_server = date('H:i');
        $date_server = date('Y-m-d');
        $otpStatus = constant('OTP');

        $id_user = CustomRequestHandler::getParam($request, "id_user");
        $tokenfire_user = CustomRequestHandler::getParam($request, "tokenfire_user");
        $platform = CustomRequestHandler::getParam($request, "platform");
        // $info_device=CustomRequestHandler::getParam($request, "info_device");
        $id_device_log = CustomRequestHandler::getParam($request, "id_device_log");
        $android_id = CustomRequestHandler::getParam($request, "android_id");
        $serial_device = CustomRequestHandler::getParam($request, "serial_device");
        $ios_version = CustomRequestHandler::getParam($request, "ios_version");
        $ios_serial_device = CustomRequestHandler::getParam($request, "ios_serial_device");
        $uuid = CustomRequestHandler::getParam($request, "uuid");

        if ($platform == "android") {

            $sqlCheck = "SELECT version_code, version_name, platform, status_update, IF(`required`='on',1,0) AS mandatory
			FROM tbl_update_version_apps WHERE type_app='user' AND platform='android'
            ";

            $resultCheck = $this->dbHandler->getDataAll($sqlCheck);
            if (($resultCheck->rowCount()) > 0) {

                $data_rowCheck = $resultCheck->fetch(PDO::FETCH_ASSOC);

                $arr1_ = array(
                    'version_code' => "" . $data_rowCheck["version_code"] . "",
                    'version_name' => "" . $data_rowCheck["version_name"] . "",
                    'platform' => "" . $data_rowCheck["platform"] . "",
                    'status_update' => "" . $data_rowCheck["status_update"] . "",
                    'mandatory' => "" . $data_rowCheck["mandatory"] . "",
                    'date_server' => "" . $date_server . "",
                );
            }

            $sql = "
			UPDATE tbl_user_tokenfire_user SET
			platform = '$platform',
			android_id = '$android_id',
			serial_device = '$serial_device',
			uuid = '$uuid'
			WHERE id_user='$id_user'
			and tokenfire_user='$tokenfire_user';

			UPDATE tbl_user_tokenfire_user
			SET `status` = '0'
			WHERE id_user='$id_user'
			AND tokenfire_user NOT IN('$tokenfire_user');

			";
            $result = $this->dbHandler->updateDataAll($sql);


            $sql2 = "SELECT tbl_user_device_info_android.id
			from tbl_user_tokenfire_user, tbl_user_device_info_android
			WHERE
			tbl_user_tokenfire_user.id=tbl_user_device_info_android.id_tokenfire_user
			AND tbl_user_tokenfire_user.tokenfire_user='$tokenfire_user'
			and tbl_user_tokenfire_user.id_user='$id_user'
			";
            $result2 = $this->dbHandler->getDataAll($sql2);
            if (($result2->rowCount()) > 0) {
            } else {
                // $data_data=olah_data_json($info_device,$tgl_input,$id_device_log,$id_tokenfire_user);
                // $sqlkirim2 = "INSERT INTO tbl_user_device_info_android
                // (id, created_at, id_tokenfire_user, board, brand, device_country_code,
                // device_language, device_time_zone, display,fingerprint,
                // hardware, host, id_device,imei,
                // imsi, manufacturer, model,product,
                // serial, uuid, version_incremental, version_sdk
                // )
                // VALUES
                // $data_data";
                // $result = $this->dbHandler->insertDataAll($sqlkirim2);


            }
        } else {

            $sqlCheck = "SELECT version_code, version_name, platform, status_update, IF(wajib='on',1,0) AS mandatory
			FROM tbl_update_version_apps WHERE type_app='user' AND platform='android'
            ";

            $resultCheck = $this->dbHandler->getDataAll($sqlCheck);
            if (($resultCheck->rowCount()) > 0) {

                $data_rowCheck = $resultCheck->fetch(PDO::FETCH_ASSOC);

                $arr1_ = array(
                    'version_code' => "" . $data_rowCheck["version_code"] . "",
                    'version_name' => "" . $data_rowCheck["version_name"] . "",
                    'platform' => "" . $data_rowCheck["platform"] . "",
                    'status_update' => "" . $data_rowCheck["status_update"] . "",
                    'mandatory' => "" . $data_rowCheck["mandatory"] . "",
                    'date_server' => "" . $date_server . "",
                );
            }

            $sql = "
			UPDATE tbl_user_tokenfire_user SET
			platform = '$platform',
			android_id = '$android_id',
			serial_device = '$serial_device'
			WHERE id_user='$id_user'
			and tokenfire_user='$tokenfire_user';

			UPDATE tbl_user_tokenfire_user
			SET `status` = 'NonAktif'
			WHERE id_user='$id_user'
			AND tokenfire_user NOT IN('$tokenfire_user');

			";
            $result = $this->dbHandler->updateDataAll($sql);


            $sql2 = "SELECT tbl_user_device_info_ios.id_device_info_ios
			from tbl_user_tokenfire_user, tbl_user_device_info_ios
			WHERE
			tbl_user_tokenfire_user.id_tokenfire_user=tbl_user_device_info_ios.id_tokenfire_user
			AND tbl_user_tokenfire_user.tokenfire_user='$tokenfire_user'
			and tbl_user_tokenfire_user.id_user='$id_user'
			";
            $result2 = $this->dbHandler->getDataAll($sql2);
            if (($result2->rowCount()) > 0) {
            } else {

                // $data_data=olah_data_json($info_device,$tgl_input,$id_device_log,$id_tokenfire_user);
                // $sqlkirim2 = "INSERT INTO tbl_user_device_info_ios
                // (id, created_at, id_tokenfire_user,
                // device_name, identifier_forvendor, localize_model,
                // model, system_name, system_version,user_interface_idiom
                // )
                // VALUES
                // $data_data";
                // $result = $this->dbHandler->insertDataAll($sqlkirim2);


            }
        }

        ############################################

        $data = [
            ":id_user" => $id_user
        ];

        $sql = "SELECT tbl_users.*
        FROM tbl_users
        WHERE `status`='1'
            AND id='$id_user'

        ";
        $result = $this->dbHandler->getDataAll($sql);
        if (($result->rowCount()) > 0) {
            $user = $result->fetch(PDO::FETCH_ASSOC);

            // if (($user["status"]=="aktif")||($user["status"]!=="aktif")) {
            if (($user["status"] == "1")) {
                $login = true;
            } else {
                $login = false;
            }

            $arr1 = array(
                'id_user' => "" . $user['id'] . "",
                'email' => "" . $user['email'] . "",
                'first_name' => "" . $user['first_name'] . "",
                'last_name' => "" . $user['last_name'] . "",
                'password' => "" . $user['password'] . "",
                'phone' => "" . $user['phone'] . "",
                'verified_phone' => "" . $user['verified_phone'] . "",
                'verified_email' => "" . $user['verified_email'] . "",
                'img_user_profile' => "" . img_user_profile($user['photo']) . "",
                'api_key_user' => "" . $user['api_key_user'] . "",
                'created_at' => "" . $user['created_at'] . "",
                'update_app' => $arr1_,
            );

            $arrAdd = array(
                'otp' => $otpStatus,
                'status_login' => $login,
            );

            return $this->customResponse->is200Response($response, $arr1, $arrAdd);
        } else {
            return $this->customResponse->is404Response($response, "account not found");
        }

        // }else{
        //
        // 	$response["success"] = true;
        // 	$response["status_login"] = false;
        // 	$response["data"] = $arr1;
        // 	$response["message"] = "Akun tidak ditemukan2!";
        // 	echoRespnse(201, $response);
        //
        // }


    }

    public function getNotificationPg(Request $request, Response $response)
    {
        $total = 0;
        $page = 0;
        $pages = 0;

        $arr1 = array();

        $page = CustomRequestHandler::getParam($request, "page");
        $per_page = CustomRequestHandler::getParam($request, "per_page");
        $id_user = (empty(CustomRequestHandler::getParam($request, "id_user"))) ? '' : CustomRequestHandler::getParam($request, "id_user");

        $page = (int)(($page < 0) || empty(CustomRequestHandler::getParam($request, "page"))) ? '1' : CustomRequestHandler::getParam($request, "page");
        $per_page = (int)(($per_page < 0) || empty(CustomRequestHandler::getParam($request, "per_page"))) ? '1' : CustomRequestHandler::getParam($request, "per_page");


        $page = ($page < 1) ? '1' : $page;

        if ($per_page <= 5) {
            // code...
            $raw_per_page = "5";
        } else if ($per_page >= 20) {
            // code...
            $raw_per_page = "20";
        } else {
            // code...
            $raw_per_page = $per_page;
        }


        $sql = "SELECT * FROM tbl_notification WHERE id_user='$id_user' AND status='1'
        ORDER BY created_at DESC
      
        ";

        $result = $this->dbHandler->getDataAll($sql);
        $total = $result->rowCount();
        if ($total > 0) {
            // code...
            $pages = ceil($total / $raw_per_page);

            $page = ($page > $pages) ? $pages : $page;
            $begin = ($page * $raw_per_page) - $raw_per_page;

            $sql .= "LIMIT {$begin},{$raw_per_page}";
            $result = $this->dbHandler->getDataAll($sql);

            while ($data_row = $result->fetch(PDO::FETCH_ASSOC)) {

                $arr1[] = array(
                    'id' => "" . $data_row["id"] . "",
                    'id_user' => "" . $data_row["id_user"] . "",
                    'id_ref' => "" . $data_row["id_ref1"] . "",
                    'title' => "" . $data_row["title"] . "",
                    'description' => "" . $data_row["description"] . "",
                    'type' => "" . $data_row["type"] . "",
                    'type_service' => "" . $data_row["type_service"] . "",
                    'is_read' => "" . $data_row["is_read"] . "",
                    'created_at' => "" . $data_row["created_at"] . "",
                );
            }
        }

        $rPagination["total"] = $total;
        $rPagination["page"] = (int)$page;
        $rPagination["pages"] = $pages;
        $rPagination["per_page"] = (int)$raw_per_page;

        return $this->customResponse->is200Response($response, $arr1, rPagination: $rPagination);
    }
}
