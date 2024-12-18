<?php

/**
 * Class Models
 *
 * @author islahudin.soft01engineer@gmail.com
 * @link islahudin.soft01engineer@gmail.com
 */

// use DbConnect;

error_reporting(E_NOTICE);

class DbHandler
{

    private $conn;
    function __construct()
    {
        require_once __DIR__ . "/DbConnect.php";
        require_once __DIR__ . "/FetchResult.php"; // Ensure FetchResult is included
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
        date_default_timezone_set('Asia/Jakarta');
    }

    /* ------------- `users` table method ------------------ */

    /**
     * Validating user api key
     * If the api key is there in db, it is a valid key
     * @return boolean
     */
    public function isValidApiKey($api_key)
    {
        // $sql = "SELECT id AS id_user from tbl_user WHERE api_key =:api_key";
        // $stmt = $this->conn->prepare($sql);
        // $stmt->execute([":api_key" => $api_key]);
        // $stmt->execute(array(":api_key" => $api_key));

        // var_dump(array(":api_key" => $api_key));

        // $num_rows = $stmt->rowCount();
        // return $num_rows > 0;
        // $stmt->bindParam("api_key", $api_key_user, PDO::PARAM_STR);
        // return 0;

        //
        // $stmt = $this->conn->prepare('SELECT id AS id_user FROM tbl_user WHERE api_key =:api_key');
        // $stmt->bindValue(':api_key', $api_key);
        // $stmt->execute();
        // // $article_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // // return 0;
        // $num_rows = $stmt->rowCount();
        // return $num_rows > 0;

        //
        // $sql = "SELECT id AS id_user from tbl_user WHERE api_key ='fghjk'";
        // $api_key = 'fghjk';
        $sql = "SELECT id AS id_user from tbl_user WHERE api_key =:api_key";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":api_key" => $api_key]);
        // $stmt->execute();

        $num_rows = $stmt->rowCount();
        return $num_rows > 0;
        // var_dump($api_key);
        // if ($stmt) {
        //     echo 'oke' . $api_key;
        // } else {
        //     echo 'faild';
        // }

        //

        // $sql = "SELECT id AS id_user FROM `tbl_user` WHERE api_key=:api_key";
        // $stmt = $this->conn->prepare($sql);
        // $stmt->bindParam("api_key", $api_key);
        // $stmt->execute();
        // $mainCount = $stmt->rowCount();
        // return $mainCount > 0;

        // $stmt = $this->conn->prepare("SELECT id AS id_user from tbl_user WHERE api_key = ?");
        // $stmt->bind_param("s", $api_key);
        // $stmt->execute();
        // return 0;
    }

    public function isValidApiKey_($api_key_user)
    {
        $sql = "SELECT id AS id_user from tbl_user WHERE api_key ='$api_key_user'";
        // $stmt = $this->conn->prepare($sql);

        $stmt = $this->conn->prepare($sql);
        // $stmt->execute();
        // $stmt->rowCount();

        // $stmt->execute([":api_key_user" => $api_key_user]);
        // $num_rows = $stmt->rowCount();
        return 0;
    }



    public function isValidApiKeyUser($api_key_user, $id_user)
    {
        $sql = "SELECT id AS id_user from tbl_users WHERE api_key_user =:api_key_user AND id=:id_user";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":api_key_user" => $api_key_user, ":id_user" => $id_user]);
        $num_rows = $stmt->rowCount();
        return $num_rows > 0;
    }

    /**
     * Validating user api key
     * If the api key is there in db, it is a valid key
     * @return boolean
     */
    public function isValidApiKeyApps($api_key_apps)
    {
        $sql = "SELECT id AS id_key_apps from tbl_key_apps WHERE key_apps =:key_apps AND type_app='user' AND `status` ='1'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":key_apps" => $api_key_apps]);
        $num_rows = $stmt->rowCount();
        return $num_rows > 0;
    }

    /**
     * Fetching user id_user by api key
     */
    public function getIdUser($api_key_user)
    {
        $sql = "SELECT id AS id_user FROM tbl_user WHERE api_key =:api_key_user";
        $stmt = $this->conn->prepare($sql);
        if ($stmt->execute([":api_key_user" => $api_key_user])) {
            $stmt = $stmt->fetch(PDO::FETCH_ASSOC);

            return $stmt["id_user"];
        } else {
            return NULL;
        }
    }

    public function updateDataByParm($sql, $parm)
    {

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($parm);

        return $stmt;
    }

    /**
     * Contoh SQL query dengan placeholder
     * $sql = "SELECT * FROM users WHERE username = :username";
     * 
     * Array parameter dengan nilai yang akan di-bind ke placeholder
     * $params = [
     *     ':username' => 'example_user'
     * ];
     * 
     * Memanggil fungsi getDataByParm
     * $result = $this->getDataByParm($sql, $params);
     * 
     * if ($result !== false) {
     *     if (!empty($result)) {
     *         Menampilkan data yang diambil
     *         foreach ($result as $row) {
     *             echo "Username: " . $row['username'] . ", Email: " . $row['email'] . "<br>";
     *         }
     *     } else {
     *         echo "Tidak ada data yang ditemukan.";
     *     }
     * } else {
     *     echo "Terjadi kesalahan saat mengambil data.";
     * }
     */

    public function getDataByParm($sql, $parm)
    {
        try {
            // Persiapan statement
            $stmt = $this->conn->prepare($sql);

            // Eksekusi statement dengan parameter yang diberikan
            $stmt->execute($parm);

            // Mengambil semua hasil sebagai array asosiatif
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Membuat instance dari FetchResult
            $fetchResult = new FetchResult($result);

            // Mengembalikan hasil FetchResult
            return $fetchResult;
        } catch (PDOException $e) {
            // Menangani kesalahan dan mengembalikan pesan error
            // Sebaiknya log error ke file atau sistem logging
            // error_log($e->getMessage());
            // var_dump("PDOException" . $e->getMessage());
            return false;
        }
    }

    /**
     * Contoh SQL query dengan placeholder
     * $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
     * 
     * Array parameter dengan nilai yang akan di-bind ke placeholder
     * $params = [
     *     ':username' => 'example_user',
     *     ':email' => 'example@example.com',
     *     ':password' => password_hash('securepassword', PASSWORD_DEFAULT) // Menggunakan hash untuk password
     * ];
     * 
     * Memanggil fungsi insertDataByParm
     * $result = $this->insertDataByParm($sql, $params);
     * 
     * if ($result !== false) {
     *     echo "Data berhasil dimasukkan. Jumlah baris yang terpengaruh: " . $result;
     * } else {
     *     echo "Terjadi kesalahan saat memasukkan data.";
     * }
     */
    public function insertDataByParm($sql, $parm)
    {
        try {
            // Persiapan statement
            $stmt = $this->conn->prepare($sql);

            // Eksekusi statement dengan parameter yang diberikan
            $stmt->execute($parm);

            // Mengembalikan jumlah baris yang terpengaruh
            return $stmt->rowCount();
        } catch (PDOException $e) {
            // Menangani kesalahan dan mengembalikan pesan error
            // Sebaiknya log error ke file atau sistem logging
            // error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Fetching all user
     */
    public function getDataAll($sql)
    {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Executes a query and returns all results as an array.
     *
     * @param string $query The SQL query to execute.
     * @param array $params An associative array of query parameters.
     * @return array The fetched results as an array of associative arrays.
     */
    public function getDataAllParam(string $query, array $params = []): array
    {
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Handle exceptions (e.g., log them)
            error_log("Database Error: " . $e->getMessage());
            return [];
        }
    }


    public function insertDataAll($sql)
    {

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        if ($stmt) {
            return true;
        } else {
            return false;
        }
    }

    public function updateDataAll($sql)
    {

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        if ($stmt) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteDataAll($sql)
    {

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        if ($stmt) {
            return true;
        } else {
            return false;
        }
    }
}
