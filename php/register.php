<?php

$usr_id = $_POST['usr_id'];
$usr_first_name = $_POST['usr_first_name'];
$usr_second_name = $_POST['usr_second_name'];
$usr_first_lastname = $_POST['usr_first_lastname'];
$usr_second_lastname = $_POST['usr_second_lastname'];
$usr_user = $_POST['usr_user'];
$usr_email = $_POST['usr_email'];
$usr_password = $_POST['usr_password'];

$data = array(
  "usr_id" => $usr_id,
  "usr_first_name" => $usr_first_name,
  "usr_second_name" => $usr_second_name,
  "usr_first_lastname" => $usr_first_lastname,
  "usr_second_lastname" => $usr_second_lastname,
  "usr_user" => $usr_user,
  "usr_email" => $usr_email,
  "usr_password" => $usr_password
);

$json_data = json_encode($data);

$url = "http://20.163.192.189:8080/api/user";

// Establecer los encabezados de autenticación
$access_token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIwODA0MzIxMzcwIiwiZXhwIjoxNjkzMDg3NTcwfQ.-nw49HQwSP1FyMZbYf_r5S2eID4f10FKmGDfDyfChpE"; // Reemplaza con tu token de acceso real
$headers = array(
  'Authorization: Bearer ' . $access_token,
  'Content-Type: application/json'
);

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

if ($http_code == 200) {
  header('Location: /php/administration_users.php');
  exit;
} else {
  echo '<script>alert("Error al registrar el usuario."); window.location.href = "/php/users.php";</script>';
  exit;
}

?>