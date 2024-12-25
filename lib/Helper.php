<?php

function get_root_uri()
{
  $expiryPeriod = constant('BASE_URL') . constant('ENDPOINT');
  return $expiryPeriod;
  // return '';
}

function img_banner_tour($img)
{
  // Http response code
  $img_s = substr($img, 0, 4);
  if ($img_s == "http") {
    // code...
    $img_banner_tour = $img;
  } else {
    $img_banner_tour = get_root_uri() . "images/img_tour/" . $img;
  }
  return $img_banner_tour;
}

function img_user_profile($img)
{
  // Http response code
  $img_s = substr($img, 0, 4);
  if ($img_s == "") {
    // code...
    $img_user_profile = "";
  } else if ($img_s == "http") {
    // code...
    $img_user_profile = $img;
  } else {
    // $img_user_profile="https://www.kreen.id/public/image/".$img;
    $img_user_profile = "https://www.kreen.id/public/user/" . $img;
  }
  return $img_user_profile;
}

function url_link_event()
{
  $url_link_event = "https://kreen.id/webinar/";
  return $url_link_event;
}

function img_banner_expen($img)
{
  $img_s = substr($img, 0, 4);
  if ($img_s == "http") {
    // code...
    $img_banner_expen = $img;
  } else {
    $img_banner_expen = get_root_uri() . "images/img_tour/" . $img;
  }
  return $img_banner_expen;
}

function img_main_menu($img)
{
  $img_s = substr($img, 0, 4);
  if ($img_s == "http") {
    // code...
    $img_banner_expen = $img;
  } else {
    $img_banner_expen = get_root_uri() . "images/img_menu/" . $img;
  }
  return $img_banner_expen;
}

function img_banner_event($img)
{
  $img_s = substr($img, 0, 4);
  if ($img_s == "http") {
    // code...
    $img_banner_expen = $img;
  } else {
    $img_banner_expen = get_root_uri() . "images/img_event/" . $img;
  }
  return $img_banner_expen;
}

function img_banner_main($img)
{
  $img_s = substr($img, 0, 4);
  if ($img_s == "http") {
    // code...
    $img_banner_expen = $img;
  } else {
    $img_banner_expen = get_root_uri() . "images/img_banner/" . $img;
  }
  return $img_banner_expen;
}

function img_institution($img)
{
  $img_s = substr($img, 0, 4);
  if ($img_s == "http") {
    // code...
    $img_banner_expen = $img;
  } else {
    $img_banner_expen = get_root_uri() . "images/img_institution/" . $img;
  }
  return $img_banner_expen;
}

function img_leadership($img)
{
  $img_s = substr($img, 0, 4);
  if ($img_s == "http") {
    // code...
    $img_banner_expen = $img;
  } else {
    $img_banner_expen = get_root_uri() . "images/img_leadership/" . $img;
  }
  return $img_banner_expen;
}

function img_logo_merchant($img)
{

  $img_s = substr($img, 0, 4);
  if ($img_s == "http") {
    // code...
    $img_logo_merchant = $img;
  } else {
    $img_logo_merchant = "https://kreen.id/public/event/" . $img;
  }
  return $img_logo_merchant;
}

function maps_static($lat, $lng, $key)
{
  $maps_static = "https://maps.google.com/maps/api/staticmap?center=" . $lat . "," . $lng . "&zoom=15&markers=" . $lat . "," . $lng . "&size=400x250&sensor=false&key=" . $key;
  // $maps_static="https://maps.google.com/maps/api/staticmap?center=".$lat.",".$lng."&zoom=15&markers=".$lat.",".$lng."&size=400x250&sensor=false&style=feature:poi|element:labels|visibility:offkey=".$key;
  return $maps_static;
}

function maps_static_mapbox($lat, $lng, $key)
{
  $maps_static = "https://api.mapbox.com/styles/v1/mapbox/streets-v11/static/" . $lng . "," . $lat . ",7.59,0/300x200?access_token=" . $key;
  // https://api.mapbox.com/styles/v1/mapbox/streets-v11/static/-121.0242,37.5953,7.59,0/300x200?access_token=pk.eyJ1IjoicW9yZGluYXRlLXRlY2hubyIsImEiOiJjbGlpdWozcncwMXJnM2VwYmVxMzBzc3hvIn0.QfE3x1awLDfaYLoNYjRdxg
  return $maps_static;
}


function acak_all_string($panjang)
{
  $karakter = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789';
  $string = '';
  for ($i = 0; $i < $panjang; $i++) {
    $pos = rand(0, strlen($karakter) - 1);
    $string .= $karakter[$pos];
  }
  return $string;
}


function currencyRp($angka)
{
  $rupiah = "Rp. " . number_format($angka, 0, ',', '.');
  return $rupiah;
}

function currency($angka)
{
  $rupiah = "" . number_format($angka, 0, ',', '.');
  return $rupiah;
}

function format_waktu($timestamp)
{
  $selisih = time() - strtotime($timestamp);

  $detik = $selisih;
  $menit = round($selisih / 60);
  $jam = round($selisih / 3600);
  $hari = round($selisih / 86400);
  $minggu = round($selisih / 604800);
  $bulan = round($selisih / 2419200);
  $tahun = round($selisih / 29030400);

  if ($detik <= 60) {
    $waktu = '1 detik yang lalu';
  } else if ($menit <= 60) {
    $waktu = $menit . ' menit';
  } else if ($jam <= 24) {
    $waktu = $jam . ' jam';
  } else if ($hari <= 7) {
    $waktu = $hari . ' hari';
  }
  // else if ($minggu <= 4) {
  //     $waktu = $minggu.' minggu yang lalu';
  // } else if ($bulan <= 12) {
  //     $waktu = $bulan.' bulan yang lalu';
  // } else {
  //     $waktu = $tahun.' tahun yang lalu';
  // }
  else {
    // $waktu = strftime( "%d %b %y", time());
    // $waktu = strftime("%d %B %Y", strtotime($timestamp));
    $waktu = date('d M Y', strtotime($timestamp));
    // $waktu=date_format(date_create($timestamp), "d-M-y");
    // $waktu = date("d-m-y", $timestamp);
  }

  return $waktu;
}

function tanggal_indo($tanggal)
{
  $ambil = substr($tanggal, 0, 10);
  $ambil2 = substr($tanggal, 2, 2);
  $ambil3 = substr($tanggal, 10, 6);

  $bulan = array(
    1 =>   'Jan',
    'Feb',
    'Mar',
    'Apr',
    'Mei',
    'Jun',
    'Jul',
    'Agus',
    'Sep',
    'Okt',
    'Nov',
    'Des'
  );
  $split = explode('-', $ambil);
  return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $ambil2 . ',' . $ambil3;
}

function get_date($tanggal, $bhs = "")
{
  $ambil = substr($tanggal, 0, 10);
  $ambil2 = substr($tanggal, 0, 4);
  $ambil3 = substr($tanggal, 10, 6);

  if ($bhs == 'IDN') {
    // code...
    $bulan = array(
      1 =>   'Januari',
      'Februari',
      'Maret',
      'April',
      'Mei',
      'Juni',
      'Juli',
      'Agustus',
      'September',
      'Oktober',
      'November',
      'Desember'
    );
  } else {
    // code...
    $bulan = array(
      1 =>   'January',
      'February',
      'March',
      'April',
      'May',
      'June',
      'July',
      'August',
      'September',
      'October',
      'November',
      'December'
    );
  }


  $split = explode('-', $ambil);
  return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $ambil2 . ',' . $ambil3;
}

function getday($tgl, $sep, $bhs = "")
{
  $tgl = date('Y-m-d', strtotime($tgl));
  $sepparator = '-';
  $parts = explode($sepparator, $tgl);
  $d = date("l", mktime(0, 0, 0, $parts[1], $parts[2], $parts[0]));


  if ($d == 'Monday') {
    if ($bhs == 'IND') {
      return 'Senin';
    } else {
      return $d;
    }
  } elseif ($d == 'Tuesday') {
    if ($bhs == 'IND') {
      return 'Selasa';
    } else {
      return $d;
    }
  } elseif ($d == 'Wednesday') {
    if ($bhs == 'IND') {
      return 'Rabu';
    } else {
      return $d;
    }
  } elseif ($d == 'Thursday') {
    if ($bhs == 'IND') {
      return 'Kamis';
    } else {
      return $d;
    }
  } elseif ($d == 'Friday') {
    if ($bhs == 'IND') {
      return 'Jumat';
    } else {
      return $d;
    }
  } elseif ($d == 'Saturday') {
    if ($bhs == 'IND') {
      return 'Sabtu';
    } else {
      return $d;
    }
  } elseif ($d == 'Sunday') {
    if ($bhs == 'IND') {
      return 'Minggu';
    } else {
      return $d;
    }
  } else {
    return 'ERROR!';
  }
}

function img_article()
{
  // Http response code
  $img_article = "https://blog.kreen.id/articleCover/";
  return $img_article;
}

function stringContains($string, $needle)
{
  if (strpos($string, $needle) !== false) {
    return true;
  }
  return false;
}


function clean_special_characters($string)
{
  // $string = str_replace(' ', ' ', $string); // Replaces all spaces with hyphens.
  $string = preg_replace('/[^a-zA-Z0-9_ -<>=]/s', ' ', $string); // Removes special chars.

  return preg_replace('!\s+!', ' ', $string);
}

function validImage($url)
{
  if (@getimagesize($url)) {
    // echo 'image exists';
    return true;
  } else {
    // echo 'image does not exist';
    return false;
  }
}

function compress_img($source, $destination, $quality)
{

  if (!file_exists($destination)) {
    // echo "file tidak ada";

    $image = "";

    $source = str_replace(' ', "%20", $source);
    if (validImage($source)) {
      // code...
      $info = getimagesize($source);

      if ($info['mime'] == 'image/jpeg') {
        $image = @imagecreatefromjpeg($source);
      } elseif ($info['mime'] == 'image/gif') {
        $image = @imagecreatefromgif($source);
      } elseif ($info['mime'] == 'image/png') {
        $image = @imagecreatefrompng($source);
      }

      if ($image) {
        // code...
        imagejpeg($image, $destination, $quality);

        $path_parts = pathinfo($destination);

        // return $destination;
        return $path_parts['basename'];
      } else {
        // code...
        return $source;
      }
    } else {
      return $source;
    }
  } else {
    // echo "file sudah ada";

    $path_parts = pathinfo($destination);
    return $path_parts['basename'];
  }
}

function fomrat_time($time)
{ //format time 12
  // $s_time=date('H:i', strtotime($time));
  $s_time = date('h:i A', strtotime($time));
  return $s_time;
}

function olah_data_detail_review($data_json, $id, $created_at)
{

  $str = '';
  foreach (json_decode($data_json, 1) as $a) {
    $str .= '(';
    $str .= "'$id',";
    $str .= "'$created_at',";
    $count = 1;
    foreach ($a as $b) {
      $b = addslashes($b);

      if ($count == 1) {
        $str .= "'$b'";
      } else if (end($a) == $b) {
        $str .= ",'$b'";
      } else {
        $str .= ",'$b'";
      }
      $count = $count + 1;
    }
    $str .= "),\n";
  }

  $str = preg_replace('[,$]s', '', $str);
  return $str;
}

function img_profile_school($level)
{
  $img = "";
  if (strtolower($level) == strtolower("sd")
  || strtolower($level) == strtolower("SPK SD")
  || strtolower($level) == strtolower("SDTK")
  ) {
    $img = "logo_sd.png";
  } else if (strtolower($level) == strtolower("SMP")
  || strtolower($level) == strtolower("SMPTK")
  || strtolower($level) == strtolower("SPK SMP")
  ) {
    $img = "logo_smp.png";
  } else if (strtolower($level) == strtolower("sma")
  || strtolower($level) == strtolower("SMTK")
  || strtolower($level) == strtolower("SPK SMA")
  || strtolower($level) == strtolower("SMAK")
  ) {
    $img = "logo_sma.png";
  } else if (strtolower($level) == strtolower("smk")) {
    $img = "logo_smk.png";
  } else if (strtolower($level) == strtolower("mi") 
  || strtolower($level) == strtolower("RA")
  || strtolower($level) == strtolower("PAUDQ")
  
  ) {
    $img = "logo_mi.png";
  } else if (strtolower($level) == strtolower("mts")) {
    $img = "logo_mts.png";
  } else if (strtolower($level) == strtolower("ma")) {
    $img = "logo_ma.png";
  } else if (strtolower($level) == strtolower("paud")) {
    $img = "logo_paud.png";
  } else if (strtolower($level) == strtolower("Pondok Pesantren")) {
    $img = "logo_ponpes.png";
  } else if (strtolower($level) == strtolower("TK")
  || strtolower($level) == strtolower("KB")
  || strtolower($level) == strtolower("TPA")
  || strtolower($level) == strtolower("SPS")
  || strtolower($level) == strtolower("Nava Dhammasekha")
  ) {
    $img = "logo_tk.png";
  } else{
    $img = "logo_tutwuri.png";
  }
  $img_profile_school = get_root_uri() . "images/img_school/" . $img;
  return $img_profile_school;
}

function img_school_level($img)
{
  
  $img_profile_school = get_root_uri() . "images/img_school/img_level/" . $img;
  return $img_profile_school;
}



function connectCURL($end_point, $post = "")
{

  $curl = curl_init($end_point);
  //INSTALL CERTIFICAT
  //       curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
  //       curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
  //       curl_setopt ($curl, CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem");

  $headers = array();
  $headers[] = 'Content-Type: application/json';

  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  // curl_setopt($curl, CURLOPT_POST, 1);
  curl_setopt($curl, CURLOPT_HEADER, 0);
  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
  if (is_array($post)) {
    $post_data = json_encode($post);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
  }
  //       curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
  $result = curl_exec($curl);
  if (curl_errno($curl) != 0 && empty($result)) {
    $result = false;
  }
  curl_close($curl);
  return $result;
}


/**
 * $sTgl = "Senin, 30 September 2024 14:24 WIB";
 * echo convertToDatetime($sTgl) . "\n"; // Output: 2024-09-30 14:24:00
 */

 function convertToDatetime($inputDate) {
  // Set timezone ke Asia/Jakarta
  date_default_timezone_set('Asia/Jakarta');

  // Hapus nama hari dan "WIB" agar dapat diolah
  $cleanedDate = preg_replace('/^[a-zA-Z]+, /u', '', $inputDate); // Hapus nama hari
  $cleanedDate = str_replace('WIB', '', $cleanedDate); // Hapus "WIB"
  $cleanedDate = trim($cleanedDate); // Hapus spasi ekstra

  // Tambahkan dukungan untuk bulan dalam bahasa Indonesia
  $bulanIndonesia = [
      'Januari' => 'January',
      'Februari' => 'February',
      'Maret' => 'March',
      'April' => 'April',
      'Mei' => 'May',
      'Juni' => 'June',
      'Juli' => 'July',
      'Agustus' => 'August',
      'September' => 'September',
      'Oktober' => 'October',
      'November' => 'November',
      'Desember' => 'December'
  ];

  // Ganti nama bulan Indonesia ke bahasa Inggris
  $cleanedDate = str_ireplace(array_keys($bulanIndonesia), array_values($bulanIndonesia), $cleanedDate);

  // Konversi ke format "Y-m-d H:i:s"
  $timestamp = strtotime($cleanedDate);
  if ($timestamp === false) {
      return "Error: Tidak dapat mengonversi tanggal.";
  }

  return date('Y-m-d H:i:s', $timestamp);
}

function extractYear($dateString) {
  return substr($dateString, 0, 4); // Mengambil 4 karakter pertama
}

function getOpeningHours($week_schedule) {

  // Check if all values in $week_schedule are empty or null
  if (!array_filter($week_schedule)) {
      // If all values are null or empty, set $opening_hours to null
      return null;
  }

  // Get today's index and name
  $days_in_order = array_keys($week_schedule);
  $current_day_index = date('w'); // 0 (Sunday) to 6 (Saturday)
  $today = $days_in_order[$current_day_index];
  $current_time = date('H:i');

  // Rearrange the days to start from today
  $ordered_days = array_merge(
      array_slice($days_in_order, $current_day_index),
      array_slice($days_in_order, 0, $current_day_index)
  );

  // Build the opening_hours array with status only for today
  return array_map(function ($day) use ($week_schedule, $today, $current_time) {
      $open_time = $week_schedule[$day];
      $status = "tutup"; // Default to "tutup"

      // If the value is a time range (HH:mm-HH:mm)
      if (strpos($open_time, '-') !== false) {
          list($start, $end) = explode('-', $open_time);
          // If today, check if the current time is within the opening time range
          if ($day === $today && $current_time >= $start && $current_time <= $end) {
              $status = "buka";
          }
      } else {
          // For other cases (like "Buka 24 jam", "Tutup", "Buka")
          if (stripos($open_time, 'buka') !== false) {
              $status = "buka";
          } elseif (stripos($open_time, 'tutup') !== false) {
              $status = "tutup";
          }
      }

      return ["day" => $day, "open_time" => $open_time, "status" => $status];
  }, $ordered_days);
}

function olah_data_json_login_regislog($data_json, $id_device_log, $id_tokenfire_user, $wlan0, $eth0, $ipv4, $ipv6, $wifi, $action, $tgl_input,$platform)
{

    $str = '';
    foreach (json_decode($data_json, 1) as $a) {
        $str .= '(';
        $str .= "'$id_device_log',";
        $str .= "'$id_tokenfire_user',";
        $str .= "'$wlan0',";
        $str .= "'$eth0',";
        $str .= "'$ipv4',";
        $str .= "'$ipv6',";
        $str .= "'$wifi',";
        $str .= "'$action',";
        $str .= "'$tgl_input',";
        $str .= "'$platform',";
        $count = 1;
        foreach ($a as $b) {

            if ($count == 1) {
                $str .= "'$b'";
            } else if (end($a) == $b) {
                $str .= ",'$b'";
            } else {
                $str .= ",'$b'";
            }
            $count = $count + 1;
        }
        $str .= "),\n";
    }

    $str = preg_replace('[,$]s', '', $str);
    return $str;
}


