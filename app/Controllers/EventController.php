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



class EventController
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

    public function getEventPg(Request $request, Response $response)
    {
        $total = 0;
        $page = 0;
        $pages = 0;

        $arr1 = array();

        $page = CustomRequestHandler::getParam($request, "page", 1);
        $per_page = CustomRequestHandler::getParam($request, "per_page", 1);
        $q = CustomRequestHandler::getParam($request, "q", '');

        if (!empty($q)) {
            $q_filter = " AND ((tbl_event.title LIKE '%$q%'))";
        } else {
            $q_filter = "";
        }

        $per_page = min(max($per_page, 2), 20);

        // Calculate the offset
        $offset = ($page - 1) * $per_page;

        // Fetch the total records and paginated results
        $sql = "SELECT COUNT(*) OVER() AS total_records, id, title, description, content, img, `date`, `time`, 
        CASE 
            WHEN `date` = CURDATE() THEN true 
            ELSE false 
        END AS isDay,
        CASE 
            WHEN `date` > CURDATE() THEN true 
            ELSE false 
        END AS isComing,
        CASE 
            WHEN `date` = CURDATE() + INTERVAL 1 DAY THEN true 
            ELSE false 
        END AS isTomorrow
        FROM tbl_event 
        WHERE 1
        AND `status` ='1'
        $q_filter
        ORDER BY `date` DESC
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

                $img = img_banner_event($data_row["img"]);

                $arr1[] = array(
                    'id' => "" . $data_row["id"] . "",
                    'title' => "" . clean_special_characters($data_row["title"]) . "",
                    'description' => $data_row["description"],
                    'content' => "" . $data_row["content"] . "",
                    'date' => "" . $data_row["date"] . "",
                    'time' => "" . $data_row["time"] . "",
                    'year' => extractYear($data_row["date"]),
                    'isDay' => (bool) $data_row["isDay"],
                    'isComing' => (bool) $data_row["isComing"],
                    'isTomorrow' => (bool) $data_row["isTomorrow"],
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

    public function viewEvent(Request $request, Response $response, array $parm)
    {

        $arr1 = null;

        $sql = "SELECT COUNT(*) OVER() AS total_records, id, title, description, content, img, `date`, `time`, 
        CASE 
            WHEN `date` = CURDATE() THEN true 
            ELSE false 
        END AS isDay,
        CASE 
            WHEN `date` > CURDATE() THEN true 
            ELSE false 
        END AS isComing,
        CASE 
            WHEN `date` = CURDATE() + INTERVAL 1 DAY THEN true 
            ELSE false 
        END AS isTomorrow
        FROM tbl_event 
        WHERE 1
        AND `status` ='1'
        AND id='$parm[slug]'
        ";

        $result = $this->dbHandler->getDataAll($sql);
        if (
            ($result->rowCount()) > 0
        ) {

            $data_row = $result->fetch(PDO::FETCH_ASSOC);

            $img = img_banner_event($data_row["img"]);

            $arr1 = array(
                'id' => "" . $data_row["id"] . "",
                'title' => "" . clean_special_characters($data_row["title"]) . "",
                'description' => $data_row["description"],
                'content' => "" . $data_row["content"] . "",
                'date' => "" . $data_row["date"] . "",
                'time' => "" . $data_row["time"] . "",
                'year' => extractYear($data_row["date"]),
                'isDay' => (bool) $data_row["isDay"],
                'isComing' => (bool) $data_row["isComing"],
                'isTomorrow' => (bool) $data_row["isTomorrow"],
                'img' => "" . $img,
            );

            return $this->customResponse->is200Response($response, $arr1);
        } else {
            // code...
            return $this->customResponse->is404Response($response, "faild");
        }
    }

    public function getEventGroup(Request $request, Response $response)
    {
        $arr1 = [];

        // Get search query parameter
        $q = CustomRequestHandler::getParam($request, "q", '');

        // Apply filtering if search query exists
        $q_filter = !empty($q) ? " AND tbl_event.title LIKE :q " : "";

        // Query to fetch distinct years
        $sqlCheck = "SELECT YEAR(`date`) AS `year`
        FROM tbl_event 
        WHERE `status` = '1'
        $q_filter
        GROUP BY YEAR(`date`)
        ORDER BY `year` DESC";

        $params = [];
        if (!empty($q)) {
            $params[':q'] = "%$q%";
        }

        $resultCheck = $this->dbHandler->getDataAllParam($sqlCheck, $params);
        if ($resultCheck) {
            foreach ($resultCheck as $data_rowCheck) {
                $year = $data_rowCheck['year'];

                // Query to fetch events for the year
                $sql = "SELECT 
                    COUNT(*) OVER() AS total_records, 
                    id, title, description, content, img, `date`, `time`, 
                    CASE WHEN `date` = CURDATE() THEN true ELSE false END AS isDay,
                    CASE WHEN `date` > CURDATE() THEN true ELSE false END AS isComing,
                    CASE WHEN `date` = CURDATE() + INTERVAL 1 DAY THEN true ELSE false END AS isTomorrow
                FROM tbl_event 
                WHERE `status` = '1'
                AND YEAR(`date`) = :year
                $q_filter
                ORDER BY `date` DESC";

                // Add year to parameters
                $params[':year'] = $year;

                $result = $this->dbHandler->getDataAllParam($sql, $params);
                if ($result) {
                    $arr = [];
                    foreach ($result as $data_row) {
                        $img = img_banner_event($data_row["img"]);

                        $arr[] = [
                            'id' => (string) $data_row["id"],
                            'title' => clean_special_characters($data_row["title"]),
                            'description' => $data_row["description"],
                            'content' => $data_row["content"],
                            'date' => $data_row["date"],
                            'time' => $data_row["time"],
                            'year' => extractYear($data_row["date"]),
                            'isDay' => (bool) $data_row["isDay"],
                            'isComing' => (bool) $data_row["isComing"],
                            'isTomorrow' => (bool) $data_row["isTomorrow"],
                            'img' => $img,
                        ];
                    }

                    $arr1[] = [
                        'year' => (string) $year,
                        'data_list' => $arr,
                    ];
                }
            }
        }

        return $this->customResponse->is200Response($response, $arr1);
    }

}
