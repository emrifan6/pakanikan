<?php

include "koneksi.php";

$api_key_value = "muchamadrifan";
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $api_key = test_input($_GET["api_key"]);
    if ($api_key_value == $api_key) {
        $sqlpompa = "SELECT gpio, status FROM pakan_ikan_control WHERE gpio = 'pompa' ORDER BY id DESC LIMIT 1";
        $sqlpakan = "SELECT gpio, status FROM pakan_ikan_control WHERE gpio = 'pakan' ORDER BY id DESC LIMIT 1";
        $query_pompa = $conn->query($sqlpompa);
        $query_pakan = $conn->query($sqlpakan);
        $row_pompa = $query_pompa->fetch_assoc();
        $row_pakan = $query_pakan->fetch_assoc();
        $data = array();
        array_push($data, $row_pompa, $row_pakan);
        if ($row_pakan['status'] != '0') {
            $sql = "INSERT INTO pakan_ikan_control (`id`, `gpio`, `status`, `timestamp`)  VALUES 
        (NULL, 'pakan', '0', current_timestamp())";
            $result = $conn->query($sql);
        }
        echo  json_encode($data) . "\n";
        $conn->close();
    } else {
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
