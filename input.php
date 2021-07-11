<?php

include "koneksi.php";
$api_key_value = "muchamadrifan";
$api_key = $pakan_ikan = $cek_pakan = "";

//Get current date and time
date_default_timezone_set('Asia/Jakarta');
$date = date("Y-m-d");
//echo " Date:".$d."<BR>";
$time = date("H:i");


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $api_key = test_input($_GET["api_key"]);
    if ($api_key_value == $api_key) {

        // GET IP USING IPINFO
        $headers = array(
            "User-Agent: curl/7.68.0",
            "Authorization: Bearer 926f21323c76e5",
        );

        $url = "https://ipinfo.io";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($curl);
        $data = json_decode($output, true);
        $ip = $data['ip'];


        $pakan_ikan = test_input($_GET["pakan_ikan"]);
        $cek_pakan = test_input($_GET["cek_pakan"]);



        if ($conn->connect_error) {
            die("Connection failed: ");
        }

        $sql = "INSERT INTO pakan_ikan (`id`, `pakan_ikan`, `cek_pakan`, `timestamp`, `ip`)  VALUES 
      (NULL, '$pakan_ikan', '$cek_pakan', current_timestamp(), '$ip')";

        if ($conn->query($sql) === TRUE) {
            echo "success";
        } else {
            echo "Error";
        }

        $conn->close();
    } else {
        // echo $test_text;
        echo "Wrong API";
    }
} else {
    echo "No data";
}

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
