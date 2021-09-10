<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
  
  <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>


  <title>Pakan Ikan</title>
</head>

<body>
  <!-- <h1>PAKAN IKAN</h1> -->

  <?php
  include "koneksi.php";
  $sql = "SELECT * FROM pakan_ikan WHERE pakan_ikan != 'kos_cek' ORDER BY id DESC LIMIT 50";
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