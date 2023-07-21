<?php
if (isset($_POST['cedula'])) {
$user_cedula = htmlspecialchars($_POST['cedula']);

// Token de autenticación
$token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIwODA0MzIxMzcwIiwiZXhwIjoxNjkzMDg3NTcwfQ.-nw49HQwSP1FyMZbYf_r5S2eID4f10FKmGDfDyfChpE";

// Realizar una solicitud a la API para obtener los datos del usuario por su cédula
$url = "http://20.163.192.189:8080/api/user/" . $user_cedula;
$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $token));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

if ($http_code == 200) {
    $user = json_decode($response, true);
    
    echo $user['usr_id']."-".
    $user['usr_first_name']."-".
    $user['usr_second_name']."-".
    $user['usr_first_lastname']."-".
    $user['usr_second_lastname']."-".
    $user['usr_full_name']."-".
    $user['usr_user']."-".
    $user['usr_email']."-".
    $user['usr_state'];

} else {
    echo "Error al obtener los datos del usuario. Código de respuesta HTTP: ".$http_code."-";
  }
} else {
    echo "No se proporcionó la cédula del usuario-";
  }
?>