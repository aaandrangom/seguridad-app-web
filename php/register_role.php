<?php

$rol_role = $_POST['rol_role'];
$rol_description = $_POST['rol_description'];
$rol_allowed_users = $_POST['rol_allowed_users'];
$rol_state = $_POST['rol_state'];

$data = array(
  "rol_role" => $rol_role,
  "rol_description" => $rol_description,
  "rol_allowed_users" => $rol_allowed_users,
  "rol_state" => $rol_state
);

$json_data = json_encode($data);

$url = "http://20.163.192.189:8080/api/role";

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
  header('Location: /php/administration_roles.php');
  exit;
} else {
  echo '<script>alert("Error al registrar el rol."); window.location.href = "/php/users.php";</script>';
  exit;
}

?>