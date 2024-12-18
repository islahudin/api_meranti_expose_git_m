<?php

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;

class UtilHelper
{
    protected $writerqr;
    public function __construct()
    {
        require_once __DIR__ . "/Helper.php";
        //Create an instance; passing `true` enables exceptions
        $this->writerqr = new PngWriter();
    }

    public function generateQrCode(string $str, int $size = 300, string $qrName = "")
    {

        // Create QR code
        // $qrCode = QrCode::create($str)
        //     ->setEncoding(new Encoding('UTF-8'))
        //     ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
        //     ->setSize($size)
        //     ->setMargin(10)
        //     ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
        //     ->setForegroundColor(new Color(0, 0, 0))
        //     ->setBackgroundColor(new Color(255, 255, 255));

        // Create QR code
        $qrCode = new QrCode(
            data: 'Life is too short to be generating QR codes',
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Low,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255)
        );

        // // Create generic logo
        // $logo = Logo::create(__DIR__.'/assets/symfony.png')
        // ->setResizeToWidth(50);

        // // Create generic label
        // $label = Label::create('Label')
        // ->setTextColor(new Color(255, 0, 0));

        // $result = $writer->write($qrCode, $logo, $label);

        $result = $this->writerqr->write($qrCode);

        // // Save it to a file
        // $result->saveToFile(__DIR__.'/'.$qrName.'.png');

        // // Generate a data URI to include image data inline (i.e. inside an <img> tag)
        // $dataUri = $result->getDataUri();
        // echo $dataUri;

        $tempdir = __DIR__ . "./../images/img_qrcode/"; //Nama folder tempat menyimpan file qrcode
        if (!file_exists($tempdir)) { //Buat folder bername temp
            mkdir($tempdir);
        }

        // Save it to a file
        // $result->saveToFile(__DIR__.$tempdir.$qrName.'.png');
        $result->saveToFile($tempdir . $qrName . '.png');

        // Generate a data URI to include image data inline (i.e. inside an <img> tag)
        $dataUri = $result->getDataUri();
        // echo $dataUri;
        // echo '
        // <br /><img src="'.$dataUri.'" alt="'.$qrName.'" />

        // ';

        return $dataUri;
    }

    /* -------------CURL method ------------------ */
    public function curlConnect($end_point, $post = "", $additional_headers = array())
    {

        // $authentication = base64_encode("$this->server_key:");

        $curl = curl_init($end_point);
        //INSTALL CERTIFICAT
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

        $default_headers = array(
            // "Authorization: Basic $authentication",
            "Content-Type: application/json",
            "Accept: application/json"
        );

        $headers = array_merge($default_headers, $additional_headers);

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0);
        if (!empty($post)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, ($post));
        }
        // curl_setopt($curl, CURLOPT_POSTFIELDS, ($post));
        // $response = curl_exec($curl);
        // var_export($response);

        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        $result = curl_exec($curl);
        if (curl_errno($curl) != 0 && empty($result)) {
            $result = false;
        }
        curl_close($curl);
        return $result;
    }

    function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}
