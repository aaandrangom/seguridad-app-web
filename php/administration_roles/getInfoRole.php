<?php
if (isset($_POST['rolId'])) {
$rol_id = htmlspecialchars($_POST['rolId']);

// Token de autenticación
$token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIwODA0MzIxMzcwIiwiZXhwIjoxNjkzMDg3NTcwfQ.-nw49HQwSP1FyMZbYf_r5S2eID4f10FKmGDfDyfChpE";

// Realizar una solicitud a la API para obtener los datos del usuario por su cédula
$url = "http://20.163.192.189:8080/api/role/" . $rol_id;
$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $token));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

if ($http_code == 200) {
    $role = json_decode($response, true);
    
    echo $role['rol_id']."-".
    $role['rol_role']."-".
    $role['rol_description']."-".
    $role['rol_allowed_users']."-".
    $role['rol_state'];

} else {
    echo "Error al obtener los datos del rol. Código de respuesta HTTP: ".$http_code."-";
  }
} else {
    echo "No se proporcionó la ID del rol";
  }
?>