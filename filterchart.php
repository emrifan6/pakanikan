<?php
include "koneksi.php";
$awalchart = $_POST['awalchart'];
$akhirchart = $_POST['akhirchart'];
// var_dump($awalchart);
// var_dump($akhirchart);

// $sql = "SELECT dht11, ds18b, DATE_FORMAT(timestamp, '%H:%i') AS time FROM pakan_ikan WHERE timestamp BETWEEN '2021-10-18' AND '2021-10-19'";
$sql = "SELECT dht11, ds18b, DATE_FORMAT(timestamp, '%H:%i') AS time, timestamp FROM pakan_ikan WHERE timestamp BETWEEN '" . $awalchart . "' AND '" . $akhirchart . "'";
$result = $conn->query($sql);
$result1 = $conn->query($sql);
$result2 = $conn->query($sql);
$result3 = $conn->query($sql);

$data_dht11 = array();
while ($a = mysqli_fetch_array($result)) {
    array_push($data_dht11, $a['dht11']);
}
$data_ds18b = array();
while ($a = mysqli_fetch_array($result1)) {
    array_push($data_ds18b, $a['ds18b']);
}
$data_time = array();
while ($a = mysqli_fetch_array($result2)) {
    array_push($data_time, $a['time']);
}
$data_timestamp = array();
while ($a = mysqli_fetch_array($result2)) {
    array_push($data_time, $a['timestamp']);
}

$reverse_dht11 = array_reverse($data_dht11);
$reverse_ds18b = array_reverse($data_ds18b);
$reverse_time = array_reverse($data_time);
$reverse_timestamp = array_reverse($data_timestamp);

$data_array = [
    "dht11" => $reverse_dht11,
    "ds18b" => $reverse_ds18b,
    "time" => $reverse_time,
    "timestamp" => $reverse_timestamp
];

// var_dump($data_dht11);
echo json_encode($data_array, true);
