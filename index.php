<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">

  <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>

  <!-- Chart.JS -->
  <!-- <script src="node_modules/chart.js/dist/chart.js"></script> -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.3/dist/Chart.min.js" integrity="sha256-R4pqcOYV8lt7snxMQO/HSbVCFRPMdrhAFMH+vr9giYI=" crossorigin="anonymous"></script>

  <style>
    .chart {
      width: 100%;
      height: 250px;
      margin: auto;
      /* border: 3px solid #73AD21; */
    }
  </style>

  <title>Pakan Ikan</title>
</head>



<body>

  <div class="row">
    <div class="col-9">
      <!-- Line Chart JS (suhu) -->
      <div class="container chart">
        <canvas id="linechart"></canvas>
      </div>
    </div>
    <div class="col-3 mt-4">
      <!-- Form code begins -->
      <!-- <form method="post" action="filterchart.php"> -->
      <form action="" id="filterchart" onsubmit="getfilterdata();return false">
        <div class=" row mb-3">
          <label for="awalchart" class="col-sm-2 col-form-label">Awal chart</label>
          <div class="col-sm-10">
            <input type="date" class="form-control" id="awalchart" name="awalchart">
          </div>
        </div>
        <div class="row mb-3">
          <label for="akhirchart" class="col-sm-2 col-form-label">Akhir chart</label>
          <div class="col-sm-10">
            <input type="date" class="form-control" id="akhirchart" name="akhirchart">
          </div>
        </div>
        <div class="form-group">
          <!-- Submit button -->
          <button class="btn btn-primary " name="submit" type="submit">Filter</button>
        </div>
      </form>
      <!-- Form code ends -->
    </div>
  </div>

  <script>
    function getfilterdata() {
      var awalchart = document.getElementById("filterchart").elements["awalchart"].value;
      var akhirchart = document.getElementById("filterchart").elements["akhirchart"].value;
      console.log(awalchart);
      console.log(akhirchart);
      var settings = {
        "url": "filterchart.php",
        "method": "POST",
        "timeout": 0,
        "headers": {
          "Content-Type": "application/x-www-form-urlencoded"
        },
        "data": {
          "awalchart": awalchart,
          "akhirchart": akhirchart
        }
      };

      $.getJSON(settings, function(data) {
        myBarChart.data.labels = data.time;
        myBarChart.data.datasets[0].data = data.dht11;
        myBarChart.data.datasets[1].data = data.ds18b;
        myBarChart.update();
      });
    }
  </script>

  <script>
    function removeData(chart) {
      chart.data.labels.pop();
      chart.data.datasets.forEach((dataset) => {
        dataset.data.pop();
      });
      chart.update();
    }
  </script>




  <div id="data-update">
    <?php
    include "koneksi.php";
    $sql = "SELECT dht11, ds18b, DATE_FORMAT(timestamp, '%H:%i') AS time, timestamp FROM pakan_ikan WHERE dht11 != 0 OR ds18b !=0 ORDER BY timestamp DESC LIMIT 35";
    $dht11 = $conn->query($sql);
    $ds18b = $conn->query($sql);
    $time = $conn->query($sql);
    $timestamp = $conn->query($sql);
    ?>
    <script type="text/javascript">
      var data_dht11 = [<?php while ($a = mysqli_fetch_array($dht11)) {
                          echo '"' . $a['dht11'] . '",';
                        } ?>];
      var data_ds18b = [<?php while ($b = mysqli_fetch_array($ds18b)) {
                          echo '"' . $b['ds18b'] . '",';
                        } ?>];
      var data_time = [<?php while ($c = mysqli_fetch_array($time)) {
                          echo '"' . $c['time'] . '",';
                        } ?>];
      var data_timestamp = [<?php while ($d = mysqli_fetch_array($timestamp)) {
                              echo '"' . $d['timestamp'] . '",';
                            } ?>];
      data_dht11.reverse();
      data_ds18b.reverse();
      data_time.reverse();
      data_timestamp.reverse();
      // console.log(data_time);
    </script>
  </div>

  <!-- AWAL LINE CHART Chart.js -->
  <script type="text/javascript">
    var ctx = document.getElementById("linechart").getContext("2d");
    var data = {
      labels: data_time,
      datasets: [{
          label: "DHT11",
          fill: false,
          lineTension: 0.1,
          backgroundColor: "#E91F63",
          borderColor: "#E91F63",
          pointHoverBackgroundColor: "#E91F63",
          pointHoverBorderColor: "#E91F63",
          data: data_dht11
        },
        {
          label: "DS18B",
          fill: false,
          lineTension: 0.1,
          backgroundColor: "#01BCD6",
          borderColor: "#01BCD6",
          pointHoverBackgroundColor: "#01BCD6",
          pointHoverBorderColor: "#01BCD6",
          data: data_ds18b
        }
      ]
    };

    var myBarChart = new Chart(ctx, {
      type: 'line',
      data: data,
      options: {
        tooltips: {
          callbacks: {
            label: function(tooltipItem, data) {
              let lbdht11 = data.datasets[0].data[tooltipItem.index];
              let lbds18b = data.datasets[1].data[tooltipItem.index];
              let lbtime = data_timestamp[tooltipItem.index];
              return ['DHT11 = ' + lbdht11, 'DS18B       = ' + lbds18b, 'time = ' + lbtime];
              // return ['DHT11 = '];
            }
          }
        },
        elements: {
          point: {
            radius: 3
          }
        },
        legend: {
          display: true
        },
        barValueSpacing: 20,
        scales: {
          // y: {
          //   min: 25,
          //   max: 35,
          // },
          yAxes: [{
            ticks: {
              autoSkip: true,
              min: 24,
            }
          }],
          xAxes: [{
            ticks: {
              /*Precisa autoSkip pra nao aparecer muitas informacoes*/
              autoSkip: true,
              maxRotation: 45,
              minRotation: 45,
              maxTicksLimit: 20,
            },
            gridLines: {
              color: "rgba(0, 0, 0, 0)",
            }
          }]
        },
        maintainAspectRatio: false
      }
    });
  </script>



  <!-- AKHIR LINE CHART -->


  <!--AWAL TOMBOL SWITCH-->
  <div class="row">
    <div class="col">
      <!-- POMPA -->
      <div class="container">
        <div class="row text-center">
          <div class="col-12">
            <form method="post" id="toggleForm">
              <fieldset>
                <legend>POMPA</legend>
                <div class="form-group">
                  <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="customSwitch1" name='machine_state' value="pakan">
                    <label class="custom-control-label" id="statusText" for="customSwitch1"></label>
                  </div>
                </div>
              </fieldset>
            </form>
            <p class="text-center" id="updatedAt">Last updated at: </p>
          </div>
        </div>
      </div>
    </div>
    <div class="col">
      <!-- PAKAN -->
      <div class="container">
        <div class="row text-center">
          <div class="col-12">
            <form method="post" id="toggleForm1">
              <fieldset>
                <legend>PAKAN</legend>
                <div class="form-group">
                  <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="customSwitch2" name='machine_state'>
                    <label class="custom-control-label" id="statusText1" for="customSwitch2"></label>
                  </div>
                </div>
              </fieldset>
            </form>
            <p class="text-center" id="updatedAt1">Last updated at: </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    function putStatus() {
      // console.log('CEK STATUS');
      $.ajax({
        type: "GET",
        url: "statusGPIO.php",
        data: {
          toggle_select: true,
        },
        success: function(result) {
          // console.log(result);
          if (result['pompa'] == 1) {
            $('#customSwitch1').prop('checked', true);
            statusText(1, 'pompa');
          } else {
            $('#customSwitch1').prop('checked', false);
            statusText(0, 'pompa');
          }
          if (result['pakan'] == 1) {
            $('#customSwitch2').prop('checked', true);
            statusText(1, 'pakan');
          } else {
            $('#customSwitch2').prop('checked', false);
            statusText(0, 'pakan');
          }
          lastUpdated();
        }
      });
    }

    function statusText(status_val, tombol) {
      if (tombol == 'pompa') {
        if (status_val == 1) {
          var status_str = "On (1)";
        } else {
          var status_str = "Off (0)";
        }
        document.getElementById("statusText").innerText = status_str;
      }
      if (tombol == 'pakan') {
        if (status_val == 1) {
          var status_str1 = "On (1)";
        } else {
          var status_str1 = "Off (0)";
        }
        document.getElementById("statusText1").innerText = status_str1;
      }
    }

    function onToggle() {
      $('#toggleForm :checkbox').change(function() {
        if (this.checked) {
          //alert('checked');
          updateStatus(1, 'pompa');
          statusText(1, 'pompa');
        } else {
          //alert('NOT checked');
          updateStatus(0, 'pompa');
          statusText(0, 'pompa');
        }
      });

      $('#toggleForm1 :checkbox').change(function() {
        if (this.checked) {
          //alert('checked');
          updateStatus(1, 'pakan');
          statusText(1, 'pakan');
        } else {
          //alert('NOT checked');
          updateStatus(0, 'pakan');
          statusText(0, 'pakan');
        }
      });
    }

    function updateStatus(status_val, tombol) {
      $.ajax({
        type: "POST",
        url: "statusGPIO.php",
        data: {
          toggle_update: true,
          status: status_val,
          tombol: tombol
        },
        success: function(result) {
          // console.log(result);
          lastUpdated();
        }
      });
    }

    function lastUpdated() {
      $.ajax({
        type: "GET",
        url: "statusGPIO.php",
        data: {
          toggle_updated: true
        },
        success: function(result) {
          // console.log("DATA RESULT");
          // console.log(result);
          document.getElementById("updatedAt").innerText = "Last updated at: " + result['pompa'];
          document.getElementById("updatedAt1").innerText = "Last updated at: " + result['pakan'];
        }
      });
    }
    $(document).ready(function() {
      //Called on page load:
      putStatus();
      onToggle();
      statusText();
    });

    const interval = setInterval(function() {
      putStatus();
    }, 10000);
  </script>





  <!--AKHIR TOMBOL SWITCH-->


  <!-- <h1>TABEL PAKAN IKAN</h1> -->

  <?php
  include "koneksi.php";
  $sql = "SELECT * FROM pakan_ikan ORDER BY id DESC LIMIT 50";
  $data = $conn->query($sql);
  $data = mysqli_fetch_all($data);
  // var_dump($data);
  ?>

  <div class="container">
    <?php if (!empty($data)) : ?>
      <h3 class="mt-4 mb-3 text-center">DAFTAR PEMBERIAN PAKAN IKAN</h3>
      <table class="table table-striped table-responsive">
        <thead>
          <tr>
            <th scope="col">No</th>
            <th scope="col">PAKAN IKAN</th>
            <th scope="col">CEK PAKAN</th>
            <th scope="col">SUHU MODUL</th>
            <th scope="col">SUHU KOLAM</th>
            <th scope="col">TIMESTAMP</th>
            <th scope="col">IP</th>
          </tr>
        </thead>
        <tbody>
          <?php $i = 1; ?>
          <?php foreach ($data as $r) : ?>
            <tr>
              <th scope="row"><?= $i++; ?></th>
              <td class="text-uppercase"><?= $r[1]; ?></td>
              <td class="text-uppercase"><?= $r[2]; ?></td>
              <td><?= $r[3]; ?></td>
              <td><?= $r[4]; ?></td>
              <td><?= $r[5]; ?></td>
              <td><?= $r[6]; ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif ?>
  </div>


  <!-- Optional JavaScript; choose one of the two! -->

  <!-- Option 1: jQuery and Bootstrap Bundle (includes Popper) -->
  <!--<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>-->

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>

  <!-- Option 2: Separate Popper and Bootstrap JS -->
  <!--
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js" integrity="sha384-+YQ4JLhjyBLPDQt//I+STsc9iw4uQqACwlvpslubQzn4u2UU2UFM80nGisd026JF" crossorigin="anonymous"></script>
    -->
</body>

</html>