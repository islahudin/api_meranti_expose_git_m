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



class GuestEntryController2
{

    protected  $customResponse;

    protected  $guestEntry;

    protected  $validator;
    protected  $conn;
    protected  $dbHandler;

    public function  __construct()
    {
        $this->customResponse = new CustomResponse();

        $this->guestEntry = new GuestEntry();

        $this->validator = new Validator();
        // $db = new DbConnect();
        // $this->conn = $db->connect();
        $this->dbHandler = new DbHandler();
        date_default_timezone_set('Asia/Jakarta');
    }

    public function createGuest(Request $request, Response $response)
    {

        $this->validator->validate($request, [
            "full_name" => v::notEmpty(),
            "email" => v::notEmpty()->email(),
            "comment" => v::notEmpty()
        ]);

        if ($this->validator->failed()) {
            $responseMessage = $this->validator->errors;
            return $this->customResponse->is400Response($response, $responseMessage);
        }

        $full_name = CustomRequestHandler::getParam($request, "full_name");
        $email = CustomRequestHandler::getParam($request, "email");
        $comment = CustomRequestHandler::getParam($request, "comment");

        $created_at = date('Y-m-d H:i:s');

        $sqlInsert = "INSERT INTO tbl_guest_entry SET
                            full_name='$full_name',
                            email='$email',
                            comment='$comment',
                            created_at='$created_at';
                            ";
        $result = $this->dbHandler->insertDataAll($sqlInsert);
        if ($result) {
            return $this->customResponse->is200Response($response, "Success Insert");
        } else {
            return $this->customResponse->is400Response($response, "Faild insert");
        }
    }

    public function viewGuests(Request $request, Response $response)
    {
        $sql = "SELECT tbl_guest_entry.*, tbl_prodi.name AS prodi_name 
        FROM tbl_guest_entry, tbl_prodi 
        WHERE tbl_guest_entry.id_prodi = tbl_prodi.id 
        
        ";
        $result = $this->dbHandler->getDataAll($sql);

        $arrEvent = array();

        if (($result->rowCount()) > 0
        ) {

            // $data_rowCheckDevice = $stmt->fetchAll(PDO::FETCH_OBJ);
            // $id_device_kiosk = $data_rowCheckDevice['id'];
            // echo "ada $id_device_kiosk";
            while ($data_row = $result->fetch(PDO::FETCH_ASSOC)) {
                $arrEvent[] = array(
                    'id' => "" . $data_row["id"] . "",
                    'full_name' => "" . $data_row["full_name"] . "",
                    'email' => "" . $data_row["email"] . "",
                    'comment' => "" . $data_row["comment"] . "",
                    'created_at' => "" . $data_row["created_at"] . "",
                    'updated_at' => $data_row["updated_at"],
                    'prodi_name' => $data_row["prodi_name"],
                );
            }

            return $this->customResponse->is200Response($response, $arrEvent);
        } else {

            return $this->customResponse->is400Response($response, $arrEvent);
        }
    }

    public function viewGuestsPg(Request $request, Response $response)
    {
        $total = 0;
        $page = 0;
        $pages = 0;

        $arrEvent = array();
        // echo "ikan";
        // exit;
        // echo "ikan2";

        $page = CustomRequestHandler::getParam($request, "page");
        $per_page = CustomRequestHandler::getParam($request, "per_page");
        $q = (empty(CustomRequestHandler::getParam($request, "q"))) ? '' : CustomRequestHandler::getParam($request, "q");
        $sort = (empty(CustomRequestHandler::getParam($request, "sort"))) ? '' : CustomRequestHandler::getParam($request, "sort");
        $prodi = (empty(CustomRequestHandler::getParam($request, "prodi"))) ? '' : CustomRequestHandler::getParam($request, "prodi");

        $page = (int)(($page < 0) || empty(CustomRequestHandler::getParam($request, "page"))) ? '1' : CustomRequestHandler::getParam($request, "page");
        $per_page = (int)(($per_page < 0) || empty(CustomRequestHandler::getParam($request, "per_page"))) ? '1' : CustomRequestHandler::getParam($request, "per_page");

        if ($sort == "asc") {
            // code...
            $addSqlSort = "ORDER BY created_at ASC";
            // $addSqlSort="ORDER BY COALESCE((SELECT COUNT(id) FROM tbl_like_all WHERE liked='1' AND id_ref=tbl_event.id),0 ) DESC";
        } else if ($sort == "desc") {
            // code...
            $addSqlSort = "ORDER BY created_at DESC";
        } else {
            // code...
            $addSqlSort = "ORDER BY created_at ASC";
        }

        if (stringContains($prodi, ',')) {
            // code...
            $_data = $prodi;
            // $arr_data = explode (",",$_data);
            // $imploded_data = implode("','",$arr_data);

            $new_arr = array_map('trim', explode(',', $_data));
            $imploded_data = implode("','", $new_arr);
            $prodi_filter = " AND tbl_prodi.slug IN ('$imploded_data')";
        } else {
            // code...
            $prodi_filter = "AND tbl_prodi.slug LIKE '%$prodi'";
        }


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


        $sql = "SELECT tbl_guest_entry.*, tbl_prodi.name AS prodi_name 
        FROM tbl_guest_entry, tbl_prodi 
        WHERE tbl_guest_entry.id_prodi = tbl_prodi.id 
        AND tbl_guest_entry.full_name LIKE '%$q%'
        $prodi_filter
        $addSqlSort

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
                $arrEvent[] = array(
                    'id' => "" . $data_row["id"] . "",
                    'full_name' => "" . $data_row["full_name"] . "",
                    'email' => "" . $data_row["email"] . "",
                    'comment' => "" . $data_row["comment"] . "",
                    'created_at' => "" . $data_row["created_at"] . "",
                    'updated_at' => $data_row["updated_at"],
                    'prodi_name' => $data_row["prodi_name"],
                    'img' => img_article() . $data_row["full_name"],
                    'register_time' => format_waktu($data_row["created_at"]),
                    'random' => acak_all_string(10),
                );
            }
        }

        $rPagination["total"] = $total;
        $rPagination["page"] = (int)$page;
        $rPagination["pages"] = $pages;
        $rPagination["per_page"] = (int)$raw_per_page;

        return $this->customResponse->is200Response2($response, $arrEvent, $message = "Success", $rPagination);
    }


    public function searchGuest(Request $request, Response $response)
    {

        $q = CustomRequestHandler::getParam($request, "q");
        $id = CustomRequestHandler::getParam($request, "id");

        $sql = "SELECT tbl_guest_entry.*, tbl_prodi.name AS prodi_name 
        FROM tbl_guest_entry, tbl_prodi 
        WHERE tbl_guest_entry.id_prodi = tbl_prodi.id 
        AND tbl_guest_entry.full_name LIKE'%$q%'
        AND tbl_guest_entry.id='$id'
        ";
        $result = $this->dbHandler->getDataAll($sql);

        $arrEvent = array();

        if (($result->rowCount()) > 0
        ) {

            while ($data_row = $result->fetch(PDO::FETCH_ASSOC)) {
                $arrEvent[] = array(
                    'id' => "" . $data_row["id"] . "",
                    'full_name' => "" . $data_row["full_name"] . "",
                    'email' => "" . $data_row["email"] . "",
                    'comment' => "" . $data_row["comment"] . "",
                    'created_at' => "" . $data_row["created_at"] . "",
                    'updated_at' => $data_row["updated_at"],
                    'prodi_name' => $data_row["prodi_name"],
                );
            }

            return $this->customResponse->is200Response($response, $arrEvent);
        } else {

            return $this->customResponse->is400Response($response, $arrEvent);
        }
    }

    public function getSingleGuest(Request $request, Response $response, array $id)
    {
        $sql = "SELECT tbl_guest_entry.*, tbl_prodi.name AS prodi_name 
        FROM tbl_guest_entry, tbl_prodi 
        WHERE tbl_guest_entry.id_prodi = tbl_prodi.id 
        AND tbl_guest_entry.id='$id[id]'
        ";
        $result = $this->dbHandler->getDataAll($sql);

        $arrEvent = null;

        if (($result->rowCount()) > 0
        ) {

            $data_row = $result->fetch(PDO::FETCH_ASSOC);
            $arrEvent = array(
                'id' => "" . $data_row["id"] . "",
                'full_name' => "" . $data_row["full_name"] . "",
                'email' => "" . $data_row["email"] . "",
                'comment' => "" . $data_row["comment"] . "",
                'created_at' => "" . $data_row["created_at"] . "",
                'updated_at' => $data_row["updated_at"],
                'prodi_name' => $data_row["prodi_name"],
            );

            return $this->customResponse->is200Response($response, $arrEvent);
        } else {

            return $this->customResponse->is400Response($response, "data not found");
        }
    }

    public function editGuest(Request $request, Response $response, $id)
    {

        $this->validator->validate($request, [
            "full_name" => v::notEmpty(),
            "email" => v::notEmpty()->email(),
            "comment" => v::notEmpty()
        ]);

        if ($this->validator->failed()) {
            $responseMessage = $this->validator->errors;
            return $this->customResponse->is400Response($response, $responseMessage);
        }

        $full_name = CustomRequestHandler::getParam($request, "full_name");
        $email = CustomRequestHandler::getParam($request, "email");
        $comment = CustomRequestHandler::getParam($request, "comment");

        $created_at = date('Y-m-d H:i:s');

        $sqlInsert = "UPDATE tbl_guest_entry 
        SET
        full_name='$full_name',
        email='$email',
        comment='$comment',
        created_at='$created_at'
        WHERE id='$id[id]';
        ";
        $result = $this->dbHandler->insertDataAll($sqlInsert);
        if ($result) {
            $responseMessage = "guest entry data updated successfully";
            return $this->customResponse->is200Response($response, $responseMessage);
        } else {
            return $this->customResponse->is400Response($response, "Faild insert");
        }
    }

    public function deleteGuest(Request $request, Response $response, $id)
    {

        $sqlInsert = "DELETE FROM tbl_guest_entry 
        WHERE id='$id[id]';
        ";

        $result = $this->dbHandler->deleteDataAll($sqlInsert);
        if ($result) {
            return $this->customResponse->is200Response($response, "Success Delete");
        } else {
            return $this->customResponse->is400Response($response, "Faild Delete");
        }
    }

    public function countGuests(Request $request, Response $response)
    {

        $sql = "SELECT count(id) AS _count FROM tbl_guest_entry 
        ";
        $result = $this->dbHandler->getDataAll($sql);

        $data_rowCheckDevice = $result->fetch(PDO::FETCH_ASSOC);
        $guestsCount = $data_rowCheckDevice['_count'];
        return $this->customResponse->is200Response($response, $guestsCount);
    }

    public function cobaGon(Request $request, Response $response, $id)
    {

        return $this->customResponse->is200Response($response, "Success cobaGon");
    }
}
