<?php 
session_start();
if(!array_key_exists('usuario', $_SESSION)){
  ?>
  <script> 
    alert('Acceso denegado: Usuario no identificado');
    window.location.href="http://20.163.192.189/";
  </script>
  <?php
}
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: /index.php");
    exit();
}  
?>

<!DOCTYPE html>
<html>

<head>
  <title>Registro de Usuario</title>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="../css/cabezera_principal.css">
  <link rel="stylesheet" type="text/css" href="../css/menu_principal.css">
</head>

<body>
  <div class="cabezera">
    <strong>MODULO DE SEGURIDAD - REGISTRAR USUARIO</strong>
  </div>
  <header>
  <?php echo $_SESSION['menu_completo'];?>
  </header>
  <div class="container">
    <div class="row">
      <div class="col-md-6 col-md-offset-3">
        <h2 class="text-center">Formulario de Usuario</h2>
        <form action="/php/register.php" method="POST">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="usr_id">Cédula:</label>
                <input type="text" name="usr_id" id="usr_id" class="form-control" required>
              </div>

              <div class="form-group">
                <label for="usr_first_name">Nombre:</label>
                <input type="text" name="usr_first_name" id="usr_first_name" class="form-control" required>
              </div>

              <div class="form-group">
                <label for="usr_second_name">Segundo Nombre:</label>
                <input type="text" name="usr_second_name" id="usr_second_name" class="form-control" required>
              </div>

              <div class="form-group">
                <label for="usr_first_lastname">Primer Apellido:</label>
                <input type="text" name="usr_first_lastname" id="usr_first_lastname" class="form-control" required>
              </div>

              <div class="form-group">
                <label for="usr_second_lastname">Segundo Apellido:</label>
                <input type="text" name="usr_second_lastname" id="usr_second_lastname" class="form-control" required>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="usr_user">Nombre de Usuario:</label>
                <input type="text" name="usr_user" id="usr_user" class="form-control" required>
              </div>

              <div class="form-group">
                <label for="usr_email">Correo Electrónico:</label>
                <input type="email" name="usr_email" id="usr_email" class="form-control" required>
              </div>

              <div class="form-group">
                <label for="usr_password">Contraseña:</label>
                <input type="password" name="usr_password" id="usr_password" class="form-control" required>
              </div>
            </div>
          </div>

          <div class="form-group">
            <input type="submit" value="Enviar" class="btn btn-primary">
          </div>
        </form>
      </div>
    </div>
  </div>
</body>

</html>