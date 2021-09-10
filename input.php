<?php

include "koneksi.php";
$api_key_value = "muchamadrifan";
$api_key = $pakan_ikan = $cek_pakan = $dht11 = $ds18b = "";

//Get current date and time
date_default_timezone_set('Asia/Jakarta');
$date = date("Y-m-d");
//echo " Date:".$d."<BR>";
$time = date("H:i");

$js_odns = '
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
        <script type="text/javascript">
        var settings = {
          "url": "https://updates.opendns.com/nic/update?hostname=gasem",
          "method": "GET",
          "timeout": 0,
          "headers": {
          "Access-Control-Allow-Origin":  "*",
            "Authorization": "Basic Qm94YmlnNDlAZ21haWwuY29tOkdzbV93dWx1bmcxMjM="
          },
        };
    $.ajax(settings).done(function (response) {
      console.log(response);
    });
    </script>
';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $api_key = test_input($_GET["api_key"]);
    if ($api_key_value == $api_key) {
        
        $pakan_ikan = test_input($_GET["pakan_ikan"]);
        $cek_pakan = test_input($_GET["cek_pakan"]);
        $dht11 = test_input($_GET["dht11"]);
        $ds18b = test_input($_GET["ds18b"]);
        $ip = $_SERVER['REMOTE_ADDR'];
        
       if ($conn->connect_error) {
            die("Connection failed: ");
        }

      $sql = "INSERT INTO pakan_ikan (`id`, `pakan_ikan`, `cek_pakan`, `dht11`, `ds18b`, `timestamp`, `ip`)  VALUES 
    (NULL, '$pakan_ikan', '$cek_pakan', '$dht11', '$ds18b', current_timestamp(), '$ip')";

      if ($conn->query($sql) === TRUE) {
        echo "success";
        echo $js_odns;
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

