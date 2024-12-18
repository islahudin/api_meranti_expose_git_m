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



class EducationController
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

    public function getEBookPg(Request $request, Response $response, array $parm)
    {
        $total = 0;
        $page = 0;
        $pages = 0;

        $arr1 = array();

        $page = CustomRequestHandler::getParam($request, "page", 1);
        $per_page = CustomRequestHandler::getParam($request, "per_page", 1);
        $q = CustomRequestHandler::getParam($request, "q",'');

        $slug= $parm["slug"] ?? '';
        if (!empty($slug)) {
            $slug_filter = " AND tbl_ebook.subtitle = '$slug'";
        } else {
            $slug_filter = "";
        }

        if (!empty($q)) {
            $q_filter = " AND ((tbl_ebook.title LIKE '%$q%') OR (tbl_ebook.subtitle LIKE '%$q%'))";
        } else {
            $q_filter = "";
        }

        $per_page = min(max($per_page, 5), 20);

        // Calculate the offset
        $offset = ($page - 1) * $per_page;

        // Fetch the total records and paginated results
        $sql = "SELECT COUNT(*) OVER() AS total_records, id, title, subtitle, img, pdf_link, class,author, reviewer, editor, designer, illustrator, publisher, `year`
        FROM tbl_ebook
        WHERE 1
        AND `status` ='1'
        
        $slug_filter
        $q_filter
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


                $id = $data_row["id"];
                // $banner = img_banner_expen($data_row["img"]);
                // $file_name = pathinfo($data_row["img"], PATHINFO_FILENAME);
                // $filepath = "./../images/img_temp/" . $file_name . ".jpg";

                // $commp = compress_img($banner, $filepath, 50);
                // if ($commp == $banner) { //
                //     // code...
                //     $img = $commp;
                // } else {
                //     // code...
                //     $img = get_root_uri() . "images/img_temp/" . $commp;
                // }

                // $img = "$banner";
                $img = $data_row["img"];

                $arr1[] = array(
                    'id' => "" . $data_row["id"] . "",
                    'title' => "" . clean_special_characters($data_row["title"]) . "",
                    'subtitle' => "" . clean_special_characters($data_row["subtitle"]) . "",
                    'class' => "" . clean_special_characters($data_row["class"]) . "",
                    'link' => "" . clean_special_characters($data_row["pdf_link"]) . "",
                    'author' => "" . clean_special_characters($data_row["author"]) . "",
                    'publisher' => "" . clean_special_characters($data_row["publisher"]) . "",
                    'year' => "" . clean_special_characters($data_row["year"]) . "",
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

    public function viewEBook(Request $request, Response $response, array $parm)
    {

        $arr1 = null;

        $sql = "SELECT id, title, subtitle, img, pdf_link, class,author, reviewer, editor, designer, illustrator, publisher, `year`
        FROM tbl_ebook
        WHERE 1
        AND `status` ='1'
        AND (tbl_ebook.id='$parm[slug]')
        ";

        $result = $this->dbHandler->getDataAll($sql);
        if (
            ($result->rowCount()) > 0
        ) {

            $data_row = $result->fetch(PDO::FETCH_ASSOC);

            $img = $data_row["img"];

            $arr1[] = array(
                'id' => "" . $data_row["id"] . "",
                'title' => "" . clean_special_characters($data_row["title"]) . "",
                'subtitle' => "" . clean_special_characters($data_row["subtitle"]) . "",
                'link' => "" . clean_special_characters($data_row["pdf_link"]) . "",
                'class' => "" . clean_special_characters($data_row["class"]) . "",
                'author' => "" . clean_special_characters($data_row["author"]) . "",
                'reviewer' => "" . clean_special_characters($data_row["reviewer"]) . "",
                'editor' => "" . clean_special_characters($data_row["editor"]) . "",
                'designer' => "" . clean_special_characters($data_row["designer"]) . "",
                'illustrator' => "" . clean_special_characters($data_row["illustrator"]) . "",
                'publisher' => "" . clean_special_characters($data_row["publisher"]) . "",
                'year' => "" . clean_special_characters($data_row["year"]) . "",
                'img' => "" . $img,
            );


            return $this->customResponse->is200Response($response, $arr1);
        } else {
            // code...
            return $this->customResponse->is404Response($response, "faild");
        }
    }

    public function getClass(Request $request, Response $response, array $parm)
    {

        $arr1 = array();

        // $id_regency = (empty(CustomRequestHandler::getParam($request, "id_regency"))) ? '' : CustomRequestHandler::getParam($request, "id_regency");

        // $sql = "SELECT `level` FROM tbl_ebook WHERE `status`='1' GROUP BY `level`
        // ORDER BY CAST(class AS UNSIGNED) ASC
        
        // ";
        $sql = "SELECT * FROM tbl_class_level WHERE `status`='1'
        ORDER BY id ASC
        
        ";

        $result = $this->dbHandler->getDataAll($sql);
        if (($result->rowCount()) > 0) {
            // code...

            while ($data_row = $result->fetch(PDO::FETCH_ASSOC)) {

                $id = $data_row["id"];
                $name = $data_row["name"];
                $bg = json_decode($data_row["bg"]);
                $img = img_school_level($data_row["img"]);

                $sql2 = "SELECT eb1.class_label AS class, eb1.subtitle AS slug,
                COALESCE((SELECT COUNT(id) AS class FROM tbl_ebook WHERE `class`=eb1.class),0) AS total_ebook
                FROM tbl_ebook eb1 WHERE eb1.`class_level_id`='$id' GROUP BY class
                ORDER BY CAST(class AS UNSIGNED) ASC
                ";

                $arr2=array();
                $result2 = $this->dbHandler->getDataAll($sql2);
                if (($result2->rowCount()) > 0) {

                    while ($data_row2 = $result2->fetch(PDO::FETCH_ASSOC)) {

                        $class = $data_row2["class"];
                        $slug = $data_row2["slug"];
                        $total_ebook = $data_row2["total_ebook"];

                        $arr2[] = array(
                            'name' =>  $class,
                            'slug' =>  $slug,
                            'total_ebook' =>  (int)$total_ebook,
                        );

                    }
                    
                }

                $arr1[] = array(
                    'level' =>  $name,
                    'bg' =>  $bg,
                    'img' =>  $img,
                    'class' =>  $arr2,
                );
            }
        }

        return $this->customResponse->is200Response($response, $arr1);
    }
}
