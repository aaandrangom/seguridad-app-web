<?php 
session_start();

$token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIwODA0MzIxMzcwIiwiZXhwIjoxNjk0OTg1NDgyfQ.GljEqO4wDKT_x94OIQ76k2AraJUY4YKAwBFrfs-ZsMQ";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Obtener los valores enviados desde el formulario
  $user_name = $_POST["username"];
  $user_password = $_POST["password"];
  $mod_name = $_POST["module"];

  // URL de tu API y parámetros para la solicitud de inicio de sesión
  $url = "http://20.163.192.189:8080/api/login";

  $queryParams = "?user_username=". urlencode($user_name) . "&user_password=" . urlencode($user_password) . "&mod_name=" . urlencode($mod_name);
  $url .= $queryParams;
  $headers = array(
    "Authorization: Bearer $token",
    "Content-Type: application/json"
  );
 
  // Configurar opciones para la solicitud HTTP
  $options = array(
    'http' => array(
      'header' => implode("\r\n", $headers),
      'method' => 'GET',
      'ignore_errors' => true
    )
  );

  // Realizar solicitud GET a la API
  $context = stream_context_create($options);
  $response = file_get_contents($url, false, $context);

  // Verificar si se obtuvo una respuesta válida
  if ($response !== false) {
    $data = json_decode($response);

    if ($data) {
      $_SESSION['usuario'] = $user_name;
      $_SESSION['funciones'] = array();
      foreach ($data as $funciones) {
        foreach ($funciones as $funcion) {
          array_push($_SESSION['funciones'], $funcion);
        }
      }

      // Redireccionar al archivo principal.php
      header("Location: /php/principal.php");
      exit();
    } else {
      // Mostrar mensaje de error
      echo "</br><center><strong><a style='color:red'>Credenciales erróneas</a></strong></center>";
    }
  } else {
    // Mostrar mensaje de error
    echo "</br><center><strong><a style='color:red'>Error al realizar la solicitud a la API</a></strong></center>";
  }
}
?>

<!DOCTYPE html>
<html>  
<head>
  <title>Formulario de Inicio de Sesión</title>
  <link rel="stylesheet" type="text/css" href="/css/EstiloLogin.css">
  <meta charset="utf8">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
  <div class="container">
    <h2>Inicio de Sesión</h2>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
      <label for="username">Usuario:</label>
      <input type="text" id="username" name="username" placeholder="Ingrese su nombre de usuario" required>
      
      <label for="password">Contraseña:</label>
      <input type="password" id="password" name="password" placeholder="Ingrese su contraseña" required>
      
      <label for="module">Módulo</label>
      <input type="text" id="module" name="module" placeholder="Ingrese su módulo" required readonly="readonly" value="Seguridad">

      <input type="submit" value="Iniciar Sesión" class="submit">
    </form>
  </div>
</body>
</html>
