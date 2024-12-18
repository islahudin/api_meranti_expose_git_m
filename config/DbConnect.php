<?php

/**
 * Handling database connection
 *
 * @author islahudin
 * @link islahudin.soft01engineer@gmail.com
 */

use App\Response\CustomResponse;

class DbConnect
{

    private $conn;
    private $customResponse;

    function __construct()
    {

        // $conn->close();
        $conn = null;
        $this->customResponse = new CustomResponse();
    }

    /**
     * Establishing database connection
     * @return database connection handler
     */
    function connect()
    {

        require __DIR__ . '/Config.php';

        try {
            // buat koneksi dengan database
            $conn = new PDO(DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USERNAME, DB_PASSWORD);

            // set error mode
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // returing connection resource
            return $conn;

            //Kill the connection with a KILL Statement.
            $conn->query('KILL CONNECTION_ID()');
            // hapus koneksi
            $conn = null;
        } catch (PDOException $e) {
            // tampilkan pesan kesalahan jika koneksi gagal
            // print "Koneksi atau query bermasalah: " . $e->getMessage() . "<br/>";
            $response["success"] = false;
            $response["code"] = 500;
            $response["status"] = "SERVER_ERROR";
            $response["message"] = "An unexpected error occured. Our team has been notified and will troubleshoot the issue";
            // echoRespnse(500, $response);
            // echo $response;
            return $this->customResponse->is400Response($response, "Faild insert");
            die();
        }
        $conn = null;
    }
}
