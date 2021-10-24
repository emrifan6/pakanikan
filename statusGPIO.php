<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
include "koneksi.php";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['toggle_update'])) {
        $tombol = $_POST['tombol'];
        $status = $_POST['status'];
        $sql = "INSERT INTO pakan_ikan_control (`id`, `gpio`, `status`, `timestamp`)  VALUES 
        (NULL, '$tombol', '$status', current_timestamp())";
        $result = $conn->query($sql);
        echo ($result);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['toggle_select'])) {
        $sql = "SELECT gpio, status FROM pakan_ikan_control WHERE gpio = 'pompa' ORDER BY id DESC LIMIT 1";
        $sql1 = "SELECT gpio, status FROM pakan_ikan_control WHERE gpio = 'pakan' ORDER BY id DESC LIMIT 1";
        $pompa = $conn->query($sql);
        $pakan = $conn->query($sql1);
        $data = $pompa->fetch_assoc();
        $data1 = $pakan->fetch_assoc();
        $output = array("pompa" => $data['status'], "pakan" => $data1['status']);
        echo json_encode($output);
    } elseif (isset($_GET['toggle_updated'])) {
        $sql = "SELECT timestamp FROM pakan_ikan_control WHERE gpio = 'pompa' ORDER BY id DESC LIMIT 1";
        $sql1 = "SELECT timestamp FROM pakan_ikan_control WHERE gpio = 'pakan' ORDER BY id DESC LIMIT 1";
        $updated = $conn->query($sql);
        $updated1 = $conn->query($sql1);
        $data = $updated->fetch_assoc();
        $data1 = $updated1->fetch_assoc();
        $output1 = array("pompa" => $data['timestamp'], "pakan" => $data1['timestamp']);
        echo json_encode($output1);
    }
} else {
    echo json_encode(array());
}
