<?php

namespace App\Controllers;

use App\Models\GuestEntry;
use App\Requests\CustomRequestHandler;
use App\Response\CustomResponse;
use Psr\Http\Message\ResponseInterface as Response;
// use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Validator as v;
use App\Validation\Validator;
// use DbConnect;
use PDO;
use DbHandler;
use UtilHelper;
// $settings = require_once  __DIR__ . "/../../config/settings.php";
use voku\helper\HtmlDomParser;



class MydomController
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
    $this->utilHelper = new UtilHelper();
    $this->dbHandler = new DbHandler();
    date_default_timezone_set('Asia/Jakarta');
  }


  public function domNews2(Request $request, Response $response, array $parm)
  {
    $created_at = date('Y-m-d H:i:s');

    $page = 1;

    $transaction = $this->domNewsLocal($page);

    $data_api = array();

    if ($transaction != null) {

      $json_api = json_decode($transaction, true);
      if (!empty($json_api['data'])) {
        // printf($transaction);

        $data_api = $json_api['data'];

        $todo_no = 1;
        foreach ($data_api as $article) {

          $link = addslashes($article["link"]);
          $title = addslashes($article["title"]);
          $pubDate = addslashes($article["pubDate"]);
          $description = addslashes($article["description"]);
          $thumbnail = addslashes($article["thumbnail"]);

          // echo $link;

          $transaction2 = $this->domNewsDetailLocal($link);

          // print_r($transaction);

          $data_api = array();

          if ($transaction2 != null) {

            $json_api2 = json_decode($transaction2, true);
            if (!empty($json_api2['title'])) {
              // printf($transaction);

              $title = addslashes($json_api2["title"]);
              $date = convertToDatetime(addslashes($json_api2["date"]));
              $image = addslashes($json_api2["image"]);
              $imageDesc = addslashes($json_api2["imageDesc"]);
              $content = addslashes($json_api2["content"]);
              $author = addslashes($json_api2["author"]);

              echo $link."</br>";

              $sql2 = "SELECT * FROM tbl_news WHERE `link`='$link'";
              $result2 = $this->dbHandler->getDataAll($sql2);
              // // echo "($todo_no) inserted ID: " . $idTodo . " slug= ". $slug."\n";

              if (($result2->rowCount()) == 0) {
                $sqlInsert = "INSERT INTO tbl_news SET
                    `title`='$title',
                    `link`='$link',
                    `description`='$description',
                    `thumbnail`='$thumbnail',
                    `pub_date`='$pubDate',
                    `page`='$page',
                    `date` = '$date', 
                    `image` = '$image',
                    `image_desc` = '$imageDesc',
                    `author` = '$author',
                    `content` = '$content',
                    `status` = '1',
                    created_at='$created_at';
                    ";

                $result = $this->dbHandler->insertDataAll($sqlInsert);

                if ($result) {

                  echo "($todo_no) inserted ID: " . $title . "</br>";
                  // echo "Url: " . $url . "<br>";

                } else {
                  echo "Error insert";
                }
              } else {
                echo "($todo_no) Already ID: " . $title . "</br>";
                // echo "Url: " . $url . "<br>";
              }

            } else {
              echo "error1";
            }
          } else {
            echo "error2";
          }

        }

      } else {
        echo "error1";
      }
    } else {
      echo "error2";


    }


  }


  public function domNews(Request $request, Response $response, array $parm)
  {
    $created_at = date('Y-m-d H:i:s');

    $page = 1;

    $transaction = $this->domNewsLocal($page);

    $data_api = array();

    if ($transaction != null) {

      $json_api = json_decode($transaction, true);
      if (!empty($json_api['data'])) {
        // printf($transaction);

        $data_api = $json_api['data'];

        $todo_no = 1;
        foreach ($data_api as $article) {

          $link = addslashes($article["link"]);
          $title = addslashes($article["title"]);
          $pubDate = addslashes($article["pubDate"]);
          $description = addslashes($article["description"]);
          $thumbnail = addslashes($article["thumbnail"]);

          // echo $link;

          $sql2 = "SELECT * FROM tbl_news WHERE `link`='$link'";
          $result2 = $this->dbHandler->getDataAll($sql2);
          // echo "($todo_no) inserted ID: " . $idTodo . " slug= ". $slug."\n";

          if (($result2->rowCount()) == 0) {
            $sqlInsert = "INSERT INTO tbl_news SET
            `title`='$title',
            `link`='$link',
            `description`='$description',
            `thumbnail`='$thumbnail',
            `pub_date`='$pubDate',
            `page`='$page',
            `status`='0',
            created_at='$created_at';
            ";

            $result = $this->dbHandler->insertDataAll($sqlInsert);

            if ($result) {

              echo "($todo_no) inserted ID: " . $title . "</br>";
              // echo "Url: " . $url . "<br>";

            } else {
              echo "Error insert";
            }
          } else {
            echo "($todo_no) Already ID: " . $title . "</br>";
            // echo "Url: " . $url . "<br>";
          }

        }

      } else {
        echo "error1";
      }
    } else {
      echo "error2";


    }


  }


  public function domNewsDetail(Request $request, Response $response, array $parm)
  {
    $created_at = date('Y-m-d H:i:s');

    $nno = 1;

    $sqlCheck = "SELECT * FROM tbl_news  WHERE `date` is null
    ORDER BY `id`
    -- LIMIT 1
    ";
    $resultCheck = $this->dbHandler->getDataAll($sqlCheck);
    // echo "($todo_no) inserted ID: " . $idTodo . " slug= ". $slug."\n";

    if (($resultCheck->rowCount()) > 0) {

      while ($data_rowCheck = $resultCheck->fetch(PDO::FETCH_ASSOC)) {
        $nno++;
        $url = $data_rowCheck["link"];

        // $url = "https://www.goriau.com/berita/baca/gandeng-bank-riau-kepri-syariah-bkpsdm-kepulauan-meranti-taja-sosialisasi-pensiun-bagi-pns.html";
        $transaction = $this->domNewsDetailLocal($url);

        // print_r($transaction);

        $data_api = array();

        if ($transaction != null) {

          $json_api = json_decode($transaction, true);
          if (!empty($json_api['title'])) {
            // printf($transaction);

            $title = addslashes($json_api["title"]);
            $date = convertToDatetime(addslashes($json_api["date"]));
            $image = addslashes($json_api["image"]);
            $imageDesc = addslashes($json_api["imageDesc"]);
            $content = addslashes($json_api["content"]);
            $author = addslashes($json_api["author"]);

            // echo $content;

            $sql2 = "SELECT * FROM tbl_news WHERE `link`='$url'";
            $result2 = $this->dbHandler->getDataAll($sql2);
            // echo "($todo_no) inserted ID: " . $idTodo . " slug= ". $slug."\n";

            if (($result2->rowCount()) > 0) {
              $sqlInsert = "UPDATE tbl_news SET 
              `date` = '$date', 
              `image` = '$image',
              `image_desc` = '$imageDesc',
              `author` = '$author',
              `content` = '$content',
              `status`='1',
              updated_at='$created_at'
              WHERE `link`='$url';
              ";

              $result = $this->dbHandler->insertDataAll($sqlInsert);

              if ($result) {

                echo "($nno) Update ID: " . $title . "</br>";
                // echo "Url: " . $url . "<br>";

              } else {
                echo "Error insert";
              }
            } else {
              echo "($nno) NotAny ID: " . $title . "</br>";
              // echo "Url: " . $url . "<br>";
            }



          } else {
            echo "error1";
          }
        } else {
          echo "error2";
        }

      }


    }


  }





  function domNewsLocal(int $page = 1)
  {
    
    $url = "https://www.goriau.com/berita/kep-meranti_$page.html";
    
    $htmlContent = $this->myCurl($url);

    // Parse HTML using HtmlDomParser
    $html = HtmlDomParser::str_get_html($htmlContent);


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

      // // Menentukan halaman aktif dan jumlah total halaman
      // $pagination = $html->find('.paginate .pg');
      // $activePage = 1;
      // $totalPages = 10;

      // // Mencari halaman aktif
      // foreach ($pagination as $page) {
      //     if (strpos($page->class, 'disabled') !== false) {
      //         $activePage = (int) $page->plaintext;
      //         break;
      //     }
      // }

      // // Menentukan jumlah total halaman berdasarkan pagination
      // foreach ($pagination as $page) {
      //     if (is_numeric(trim($page->plaintext))) {
      //         $totalPages = max($totalPages, (int) $page->plaintext);
      //     }
      // }

      // Mencari informasi tentang pagination
      $pagination = $html->find('.paginate', 0);
      $totalPages = 1;  // Default jika tidak ada pagination
      $currentPage = 1;

      // Ambil halaman aktif dan total halaman
      if ($pagination) {
        // Mencari elemen halaman aktif
        $activePage = $pagination->find('a.pg.disabled', 0);
        if ($activePage) {
          // Mengambil nomor halaman aktif dari elemen yang punya class 'disabled'
          preg_match('/_(\d+)\.html/', $activePage->href, $matches);
          $currentPage = isset($matches[1]) ? (int) $matches[1] : 1;
        }

        // Menghitung jumlah halaman total berdasarkan elemen-elemen 'a.pg'
        $pages = $pagination->find('a.pg');
        $totalPages = count($pages);
      }

      // Membentuk array hasil output dengan format yang diinginkan
      $result = [
        'page' => $currentPage, //$activePage
        'totalPage' => $totalPages,
        'data' => $posts
      ];

      // Output hasil scraping dalam format JSON
      header('Content-Type: application/json');
      // echo json_encode($result, JSON_PRETTY_PRINT);
      return json_encode($result, JSON_PRETTY_PRINT);

    } else {
      return null;
    }
  }


  function domNewsDetailLocal(string $url)
  {

    $htmlContent = $this->myCurl($url);

    // Parse HTML using HtmlDomParser
    $html = HtmlDomParser::str_get_html($htmlContent);

    if ($html) {

      $posts = [];

      // Array untuk menyimpan paragraf hasil filter
      $content = [];

      // var_dump($html); // Lihat isi variabel
      // var_dump($html->outerHtml);
      // var_dump($html->find('.postdetail'));


      $date = $html->find('.post-date', 0)->plaintext ?? 'Tanggal tidak ditemukan';
      $title = $html->find('.post-title h1', 0)->plaintext ?? 'Judul tidak ditemukan';
      $author = $html->find('.post-author span', 0)->plaintext ?? 'Penulis tidak ditemukan';
      $image = $html->find('.post-thumb img', 0);
      $image = $image ? ($image->getAttribute('src') ?: $image->getAttribute('data-cfsrc') ?: '') : '';
      $imageDesc = $html->find('.post-thumb-desc', 0)->plaintext ?? 'Deskripsi gambar tidak ditemukan';

      // Menelusuri semua elemen <p> dalam konten
      foreach ($html->find('p') as $paragraph) {
        // Periksa apakah elemen parent mengandung elemen iklan (<ins> atau div dengan atribut iklan)
        $parent = $paragraph->parent;
        // var_dump($paragraph); // Debug untuk melihat tipe dan isi variabel

        if (is_object($parent)) {
          // Proceed if $parent is an object
          $hasAds = $parent->find('ins, [class*=adsbygoogle], [data-ad-client]');
        } else {
          // Log an error or handle the case where $parent is not an object
          // error_log('Error: $parent is not an object. It is of type ' . gettype($parent));
          $hasAds = null; // Or handle appropriately
        }

        // $hasAds = $parent->find('ins, [class*=adsbygoogle], [data-ad-client]');

        // Jika tidak ada elemen iklan dalam parent, tambahkan konten ke array
        if (empty($hasAds)) {
          $content[] = $paragraph->outertext; // Mengambil tag <p> beserta isi dan atributnya
        }
      }

      // Gabungkan hasil menjadi string
      $filteredContent = implode("\n", $content);

      $post['title'] = $title;
      $post['date'] = $date;
      $post['image'] = $image;
      $post['imageDesc'] = $imageDesc;
      $post['content'] = $filteredContent;
      $post['author'] = $author;

      // Tambahkan data post ke array hasil
      $posts = $post;

      // Output hasil scraping dalam format JSON
      // header('Content-Type: application/json');
      // echo json_encode($result, JSON_PRETTY_PRINT);
      return json_encode($posts, JSON_PRETTY_PRINT);

    } else {
      return null;
    }
  }
  
  
  function myCurl($url){
      // Load HTML using cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Handle redirects
    $htmlContent = curl_exec($ch);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return $this->customResponse->is400Response($response, "Error fetching URL: $error");
    }

    curl_close($ch);
    
    return $htmlContent;
  }

}
