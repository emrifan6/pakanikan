<?php
echo $_SERVER['REMOTE_ADDR'];
include "koneksi.php";
$ip = "";
if ($_SERVER["REQUEST_METHOD"] == "GET") {
       $ip = $_GET["ip"];
       if ($conn->connect_error) {
            die("Connection failed: ");
        }
	      $sql = "UPDATE pakan_ikan SET ip = '$ip' WHERE ( id = (SELECT MAX(id) FROM pakan_ikan LIMIT 1) ) AND ip IS NULL";
	      if ($conn->query($sql) === TRUE) {
	          echo "success";
	          $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://updates.opendns.com/nic/update?hostname=gasem&myip=' + $ip,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'GET',
              CURLOPT_HTTPHEADER => array(
                'Authorization: Basic Qm94YmlnNDlAZ21haWwuY29tOkdzbV93dWx1bmcxMjM='
              ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
	      } else {
	          echo "Error";
	      }
	      $conn->close();
 } 


