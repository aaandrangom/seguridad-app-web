<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['_method'] === 'PUT') {
  // Obtener los datos enviados desde el formulario
  $usr_id_current = $_POST['usr_id_current'];
  $usr_id = $_POST['usr_id'];
  $usr_first_name = $_POST['usr_first_name'];
  $usr_second_name = $_POST['usr_second_name'];
  $usr_first_lastname = $_POST['usr_first_lastname'];
  $usr_second_lastname = $_POST['usr_second_lastname'];
  $usr_user = $_POST['usr_user'];
  $usr_email = $_POST['usr_email'];
  $usr_password = $_POST['usr_password'];
  $usr_state = $_POST['usr_state'];

  // Crear un arreglo con los datos actualizados del usuario
  $updated_user = [
    'usr_id_update' => $usr_id,
    'usr_first_name' => $usr_first_name,
    'usr_second_name' => $usr_second_name,
    'usr_first_lastname' => $usr_first_lastname,
    'usr_second_lastname' => $usr_second_lastname,
    'usr_user' => $usr_user,
    'usr_email' => $usr_email,
    'usr_password' => $usr_password,
    'usr_state' => $usr_state
  ];

  // Convertir los datos en formato de consulta de URL
  $query_params = http_build_query($updated_user);

  // Realizar la solicitud PUT a la API
  $url = "http://20.163.192.189:8080/api/user/" . $usr_id_current . '?' . $query_params;

  // Establecer los encabezados de autenticación
  $access_token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIwODA0MzIxMzcwIiwiZXhwIjoxNjk0OTI4ODIxfQ.zOJEUMiDj4vNHkv-EoCNazOp5Os6axj_-PGyByqQXsg"; // Reemplaza con tu token de acceso real
  $headers = [
    'Authorization: Bearer ' . $access_token,
    'Content-Type: application/json'
  ];

  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  $response = curl_exec($ch);
  $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

  curl_close($ch);

  if ($http_code == 200) {
    header("Location: /php/administration_users.php");
  } else {
    echo "Error al guardar los cambios. Código de respuesta HTTP: " . $http_code;
  }
} else {
  echo "Método no permitido. Se esperaba una solicitud PUT.";
}
?>
