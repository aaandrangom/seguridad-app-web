<?php
if (isset($_POST['mod_id'])) {
$mod_id = $_POST['mod_id'];

// Token de autenticación
$token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIwODA0MzIxMzcwIiwiZXhwIjoxNjkzMDg3NTcwfQ.-nw49HQwSP1FyMZbYf_r5S2eID4f10FKmGDfDyfChpE";

// Realizar una solicitud a la API para obtener los datos del usuario por su cédula
$url = "http://20.163.192.189:8080/api/module/" . $mod_id;
$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $token));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

  if ($http_code == 200) {
      $module = json_decode($response, true);
      
      $url = "http://20.163.192.189:8080/api/user/" . $module['mod_admin'];
      $ch = curl_init($url);

      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $token));

      $response = curl_exec($ch);
      $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

      curl_close($ch);
      if ($http_code == 200) {
        $user = json_decode($response, true);
        $user_full_name = $user['usr_full_name'];
      } else {
        $user_full_name = $module['mod_admin'];
      }

      echo $module['mod_id']."-".
      $module['mod_name']."-".
      $user_full_name."-".
      $module['mod_state']."-";
  } else {
      echo "Error al obtener los datos del modulo. Código de respuesta HTTP: ".$http_code."-";
  }
} else {
    echo "No se proporcionó el id del modulo-";
  }
?>