<?php

namespace App\Controllers;

use App\Models\GuestEntry;
use App\Requests\CustomRequestHandler;
use App\Response\CustomResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;
use Respect\Validation\Validator as v;
use App\Validation\Validator;
use voku\helper\HtmlDomParser;
// use DbConnect;
use PDO;
use DbHandler;
use UtilHelper;

class NewsController
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
        $this->htmlDom = new HtmlDomParser();
        date_default_timezone_set('Asia/Jakarta');
    }


    public function getNewsMedia(Request $request, Response $response)
    {

        $endpoint = 'https://api-berita-indonesia.vercel.app/';

        $transaction = $this->utilHelper->curlConnect($endpoint, "");
        // printf($transaction);

        $data_api = array();

        if ($transaction != null) {

            $json_api = json_decode($transaction, true);
            if (!empty($json_api['endpoints'])) {
                // printf($transaction);

                $data_api = $json_api['endpoints'];
                return $this->customResponse->is200Response($response, $data_api);
            } else {
                return $this->customResponse->is404Response($response, "faild");
            }
        } else {
            return $this->customResponse->is404Response($response, "faild");
        }
    }

    public function getNews(Request $request, Response $response, array $parm)
    {

        $category = $parm['category'];

        $endpoint = "https://api-berita-indonesia.vercel.app/tempo/$category";

        $transaction = $this->utilHelper->curlConnect($endpoint, "");
        // printf($transaction);

        $data_api = array();

        if ($transaction != null) {

            $json_api = json_decode($transaction, true);
            if (!empty($json_api['data'])) {
                // printf($transaction);

                $data_api = $json_api['data'];
                return $this->customResponse->is200Response($response, $data_api);
            } else {
                return $this->customResponse->is404Response($response, "faild1");
            }
        } else {
            return $this->customResponse->is404Response($response, "faild2");
        }
    }


    public function getNewsLocalPg(Request $request, Response $response, array $parm)
    {
        $total = 0;
        $page = 0;
        $pages = 0;

        $arr1 = array();

        $page = CustomRequestHandler::getParam($request, "page", 1);
        $per_page = CustomRequestHandler::getParam($request, "per_page", 1);
        $q = CustomRequestHandler::getParam($request, "q",'');

        if (!empty($q)) {
            $q_filter = " AND tbl_news.title LIKE '%$q%'";
        } else {
            $q_filter = "";
        }

        $per_page = min(max($per_page, 5), 20);

        // Calculate the offset
        $offset = ($page - 1) * $per_page;

        // Fetch the total records and paginated results
        $sql = "SELECT COUNT(*) OVER() AS total_records, id, title, link, thumbnail, `date`, `image` AS img ,image_desc AS img_desc, content, author
        FROM tbl_news
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
                
                $thumbnail = $data_row["thumbnail"];
                $img = str_replace('-thumb', '', $thumbnail);

                $arr1[] = array(
                    'id' => "" . $data_row["id"] . "",
                    'title' => "" . clean_special_characters($data_row["title"]) . "",
                    'link' => "" . clean_special_characters($data_row["link"]) . "",
                    'thumbnail' => "" . clean_special_characters($data_row["thumbnail"]) . "",
                    'date' => "" . clean_special_characters($data_row["date"]) . "",
                    'img_desc' => "" . clean_special_characters($data_row["img_desc"]) . "",
                    'content' => "" . clean_special_characters($data_row["content"]) . "",
                    'author' => "" . clean_special_characters($data_row["author"]) . "",
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

    public function viewNewsLocal(Request $request, Response $response, array $parm)
    {

        $id_user = CustomRequestHandler::getParam($request, "id_user",'');

        $arr1 = null;

        $sql = "SELECT id, title, link, thumbnail, `date`, `image` AS img ,image_desc AS img_desc, content, author
        FROM tbl_news
        WHERE 1
        AND `status` ='1'
        AND ((tbl_news.id='$parm[slug]'))
        ";

        $result = $this->dbHandler->getDataAll($sql);
        if (($result->rowCount()) > 0
        ) {

            $data_row = $result->fetch(PDO::FETCH_ASSOC);
            $thumbnail = $data_row["thumbnail"];
            $img = str_replace('-thumb', '', $thumbnail);

            $arr1 = array(
                'id' => "" . $data_row["id"] . "",
                'title' => "" . clean_special_characters($data_row["title"]) . "",
                'link' => "" . clean_special_characters($data_row["link"]) . "",
                'thumbnail' => "" . clean_special_characters($data_row["thumbnail"]) . "",
                'date' => "" . clean_special_characters($data_row["date"]) . "",
                'img_desc' => "" . clean_special_characters($data_row["img_desc"]) . "",
                'content' => "" . clean_special_characters($data_row["content"]) . "",
                'author' => "" . clean_special_characters($data_row["author"]) . "",
                'img' => "" . $img,
            );

            return $this->customResponse->is200Response($response, $arr1);
        } else {
            // code...
            return $this->customResponse->is404Response($response, "faild");
        }
    }


    public function getNewsLocal(Request $request, Response $response, array $parm)
    {

        $page = CustomRequestHandler::getParam($request, "page", 1);

        $transaction= $this->domNewsLocal($page);

        $data_api = array();

        if ($transaction != null) {

            $json_api = json_decode($transaction, true);
            if (!empty($json_api['data'])) {
                // printf($transaction);

                $data_api = $json_api['data'];
                // return $this->customResponse->is200Response($response, $data_api);

                // $rPagination["total"] = $total;
                $rPagination["page"] = (int)$json_api["page"];
                $rPagination["pages"] = (int)$json_api["totalPage"];

                return $this->customResponse->is200Response2($response, $data_api, rPagination: $rPagination);
            } else {
                return $this->customResponse->is404Response($response, "faild1");
            }
        } else {
            return $this->customResponse->is404Response($response, "faild2");
        }

        
        

        
    }

    function domNewsLocal(Int $page = 1)
    {
        $url = "https://www.goriau.com/berita/kep-meranti_$page.html";
        $html = HtmlDomParser::file_get_html($url);
    

        if ($html) {

            // $article = $html->findOne('.postbox');

            // var_dump($article->outerHtml); // Debugging

            // Array untuk menyimpan hasil scraping
            $posts = [];

            // Mencari semua artikel berdasarkan class post
            foreach ($html->find('div.post') as $article) {
                $post = [];
                // Ambil link gambar (img)
                $img = $article->find('.post-thumb img', 0);
                $img = $img ? ($img->getAttribute('src') ?: $img->getAttribute('data-cfsrc') ?: '') : '';

                // Ambil judul dan link
                $titleElement = $article->find('.post-title a', 0);
                if ($titleElement) {
                    $link = 'https://www.goriau.com' . $titleElement->href;
                    $title = trim($titleElement->plaintext);
                } else {
                    $link = '';
                    $title = '';
                }

                // Ambil atribut waktu
                $time = $article->find('.post-attr', 0);
                $time = $time ? trim($time->plaintext) : '';


                $post['link'] = $link;
                $post['title'] = $title;
                $post['pubDate'] = $time;
                $post['description'] = '';
                $post['thumbnail'] = $img;

                // Tambahkan data post ke array hasil
                $posts[] = $post;
            }

            // Menentukan halaman aktif dan jumlah total halaman
            $pagination = $html->find('.paginate .pg');
            $activePage = 1;
            $totalPages = 10;

            // Mencari halaman aktif
            foreach ($pagination as $page) {
                if (strpos($page->class, 'disabled') !== false) {
                    $activePage = (int) $page->plaintext;
                    break;
                }
            }

            // Menentukan jumlah total halaman berdasarkan pagination
            foreach ($pagination as $page) {
                if (is_numeric(trim($page->plaintext))) {
                    $totalPages = max($totalPages, (int) $page->plaintext);
                }
            }

            // Membentuk array hasil output dengan format yang diinginkan
            $result = [
                'page' => $activePage,
                'totalPage' => $totalPages,
                'data' => $posts
            ];

            // Output hasil scraping dalam format JSON
            header('Content-Type: application/json');
            // echo json_encode($result, JSON_PRETTY_PRINT);
            return json_encode($result, JSON_PRETTY_PRINT);

        }else{
            return null;
        }
    }
}
