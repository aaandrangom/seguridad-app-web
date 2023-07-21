<?php
session_start();
if (!array_key_exists('usuario', $_SESSION)) {
  ?>
  <script>
    alert('Acceso denegado: Usuario no identificado');
    window.location.href = "http://20.163.192.189/";
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
  <title>Registro de Roles</title>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="../css/cabezera_principal.css">
  <link rel="stylesheet" type="text/css" href="../css/menu_principal.css">
</head>

<body>
  <div class="cabezera">
    <strong>MODULO DE SEGURIDAD - REGISTRAR ROL</strong>
  </div>
  <header>
    <?php echo $_SESSION['menu_completo'];?>
  </header>
  <div class="container">
    <div class="row">
      <div class="col-md-6 col-md-offset-3">
        <h2 class="text-center">Formulario de Nuevo Rol</h2>
        <form action="/php/register_role.php" method="POST">
          <div class="row">
            <div class="col-md-6">

              <div class="form-group">
                <label for="rol_role">Nombre del Rol:</label>
                <input type="text" name="rol_role" id="rol_role" class="form-control" required>
              </div>

              <div class="form-group">
                <label for="rol_allowed_users">Número de Usuarios:</label>
                <input type="number" min="1" name="rol_allowed_users" id="rol_allowed_users" class="form-control"
                  required>
              </div>

            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label for="rol_description">Descripción del Rol:</label>
                <textarea rows="4" cols="50" name="rol_description" id="rol_description" class="form-control" required>
                </textarea>
              </div>

              <div class="form-group">
                <label for="rol_state">Estado del Rol:</label>
                <select name="rol_state" id="rol_state" class="form-control" required>
                  <option value="A">Habilitado</option>
                  <option value="I">Deshabilitado</option>
                </select>
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