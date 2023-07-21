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

if (isset($_POST['usr_id'])) {
  $user_cedula = htmlspecialchars($_POST['usr_id']);

  // Token de autenticación
  $token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIwODA0MzIxMzcwIiwiZXhwIjoxNjk0OTI4ODIxfQ.zOJEUMiDj4vNHkv-EoCNazOp5Os6axj_-PGyByqQXsg";

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
    $usr_id = $user['usr_id'];
    $usr_first_name = $user['usr_first_name'];
    $usr_second_name = $user['usr_second_name'];
    $usr_first_lastname = $user['usr_first_lastname'];
    $usr_second_lastname = $user['usr_second_lastname'];
    $usr_user = $user['usr_user'];
    $usr_email = $user['usr_email'];
    $usr_password = $user['usr_password'];
    $usr_state = $user['usr_state'];

  } else {
    echo "Error al obtener los datos del usuario. Código de respuesta HTTP: " . $http_code;
  }

  $url = "http://20.163.192.189:8080/api/role_user/user/" . $user_cedula;
  $ch = curl_init($url);

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $token));

  $response = curl_exec($ch);
  $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

  curl_close($ch);
  $registro = "";
  ?>
  <script>var ids_roles_user = [];</script>
  <?php
  if ($http_code == 200) {
    $roles_user = json_decode($response, true);
    foreach ($roles_user['tb_role_user_user'] as $rol) {
      $url = "http://20.163.192.189:8080/api/role/" . $rol['rol_usr_role'];
      $ch = curl_init($url);

      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $token));

      $response = curl_exec($ch);
      $http_code_in = curl_getinfo($ch, CURLINFO_HTTP_CODE);

      curl_close($ch);
      ?>
      <script>ids_roles_user.push(<?php echo $rol['rol_usr_role']; ?>);</script>
      <?php
      if ($http_code_in == 200) {
        $rol_user = json_decode($response, true);
        $registro = $registro . "
        <tr class='rowOne'>
        <td class='columOne'>" . $rol_user['rol_role'] . "</td>
        <td class='columOne'>" . $rol_user['rol_description'] . "</td>
        <td class='columOne'>
            <button class='btnNormal btnDelete'type='button' onclick='enableRole(" . $rol['rol_usr_id'] . ", " . '"' . $rol['rol_usr_user'] . '"' . ")'>✕</button>
        </td>
        </tr>
    ";
      }
    }
  } else {
    $registro = "
      <tr>
          <td colspan='3'>
              El usuario no cuenta con roles asignados
          </td>
      </tr>";
  }
  $registroTwo = "";
  $url = "http://20.163.192.189:8080/api/role";
  $ch = curl_init($url);

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $token));

  $response = curl_exec($ch);
  $http_code_in = curl_getinfo($ch, CURLINFO_HTTP_CODE);

  curl_close($ch);

  if ($http_code_in == 200) {
    $roles = json_decode($response, true);
    foreach ($roles['role'] as $rol_) {
      $registroTwo = $registroTwo . "
      <tr class='rowOne'>
        <td class='columOne'>" . $rol_['rol_id'] . "</td>
        <td class='columOne'>" . $rol_['rol_role'] . "</td>
        <td class='columOne'>" . $rol_['rol_description'] . "</td>
        <td class='columOne'>
          <button class='btnNormal btnEdit'type='button' onclick='AddRole(" . $rol_['rol_id'] . ", " . '"' . $user_cedula . '"' . ")'>✓</button>
        </td>
      </tr>
    ";
    }
  } else {
    $registroTwo = "
      <tr>
          <td colspan='4'>
              No existen Roles
          </td>
      </tr>";
  }
} else {
  ?>
  <script>
    Swal.fire({
      title: "Error: !Sin usuario¡",
      text: "Error 1206: " + <?php echo $http_code ?>,
      icon: "error",
    }).then(resultado => {
      if (resultado.value) {
        window.location.href = "administration_users.php";
      }
    });;
  </script>
  <?php
}
?>

<!DOCTYPE html>
<html>

<head>
  <title>Editar Usuario</title>
  <link rel="stylesheet" type="text/css" href="../css/cabezera_principal.css">
  <link rel="stylesheet" type="text/css" href="../css/menu_principal.css">
  <link rel="stylesheet" type="text/css" href="..\css\principal.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://unpkg.com/sweetalert2@9.5.3/dist/sweetalert2.all.min.js"></script>
</head>

<body>

  <div class="cabezera">
    <strong>MODULO DE SEGURIDAD - EDITAR USUARIO</strong>
  </div>
  <header>
    <?php echo $_SESSION['menu_completo']; ?>
  </header>
  <div>

    <form method="POST" action="/php/save_user.php">
      <center>
        <table width="90%">
          <tr>
            <td colspan="2">
              <h2>
                <center>Editar Usuario</center>
              </h2>
            </td>
          </tr>
          <tr>
            <td width="50%" rowspan="2">
              <input type="hidden" name="_method" value="PUT">
              <input type="hidden" name="usr_id_current" value="<?php echo $usr_id; ?>">
              <center>
                <table class="tablaTwo">
                  <tr>
                    <td class=" colorOne"><strong>Cedula</strong></td>
                    <td class=" colorOne"><strong>Nombre de Usuario</strong></td>
                  </tr>
                  <tr>
                    <td class="emptyColumTwo" id="cedula">
                      <input class="inputTexto" type="text" name="usr_id" id="usr_id" value="<?php echo $usr_id; ?>">
                    </td>
                    <td class="emptyColumTwo" id="usuario">
                      <input class="inputTexto" type="text" name="usr_user" id="usr_user"
                        value="<?php echo $usr_user; ?>">
                    </td>
                  </tr>
                  <tr>
                    <td class=" colorOne"><strong>Primer Nombre</strong></td>
                    <td class=" colorOne"><strong>Segundo Nombre</strong></td>
                  </tr>
                  <tr>
                    <td class="emptyColumTwo" id="primer_nombre">
                      <input class="inputTexto" type="text" name="usr_first_name" id="usr_first_name"
                        value="<?php echo $usr_first_name; ?>">
                    </td>
                    <td class="emptyColumTwo" id="segundo_nombre">
                      <input class="inputTexto" type="text" name="usr_second_name" id="usr_second_name"
                        value="<?php echo $usr_second_name; ?>">
                    </td>
                  </tr>
                  <tr>
                    <td class=" colorOne"><strong>Primer Apellido</strong></td>
                    <td class=" colorOne"><strong>Segundo Apellido</strong></td>
                  </tr>
                  <tr>
                    <td class="emptyColumTwo" id="primer_apellido">
                      <input class="inputTexto" type="text" name="usr_first_lastname" id="usr_first_lastname"
                        value="<?php echo $usr_first_lastname; ?>">
                    </td>
                    <td class="emptyColumTwo" id="segundo_apellido">
                      <input class="inputTexto" type="text" name="usr_second_lastname" id="usr_second_lastname"
                        value="<?php echo $usr_second_lastname; ?>">
                    </td>
                  </tr>
                  <tr>
                    <td class=" colorOne"><strong>Correo Electrónico</strong></td>
                    <td class=" colorOne"><strong>Contraseña</strong></td>
                  </tr>
                  <tr>
                    <td class="emptyColumTwo" id="email">
                      <input class="inputTexto" type="email" name="usr_email" id="usr_email"
                        value="<?php echo $usr_email; ?>">
                    </td>
                    <td class="emptyColumTwo" id="contraseña">
                      <input class="inputTexto" type="password" name="usr_password" id="usr_password"
                        value="<?php echo $usr_password; ?>">
                    </td>
                  </tr>
                  <tr>
                    <td class=" colorOne"><strong>Estado</strong></td>
                    <td class=" colorOne"></td>
                  </tr>
                  <tr>
                    <td>
                      <div style="position: relative; width: 100%;">
                        <select name="usr_state" id="usr_state" class="form-control" required
                          style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; background-color: #fff; font-size: 14px; appearance: none; -moz-appearance: none; -webkit-appearance: none;">
                          <option value="A" <?php if ($usr_state === 'A')
                            echo 'selected'; ?>>Habilitado</option>
                          <option value="I" <?php if ($usr_state === 'I')
                            echo 'selected'; ?>>Deshabilitado</option>
                        </select>
                      </div>
                    </td>
                    <td class="emptyColumTwo"></td>
                  </tr>
                </table>
              </center>
            </td>
            <td>
              <div id="reloadRolesUser" style="width: 100%; height: 175px; overflow-y: scroll;">
                <center>
                  <table class="tablaDetalles" width="95%">
                    <tr class="columCabezera">
                      <td class="columOne colum"><strong>Rol</strong></td>
                      <td class="columOne colum"><strong>Descripcion</strong></td>
                      <td class="columOne colum"><strong></strong></td>
                    </tr>
                    <?php echo $registro ?>
                  </table>
                </center>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <div id="reloadRoles" style="width: 100%; height: 175px; overflow-y: scroll;">
                <center>
                  <table class="tablaDetalles" width="95%">
                    <tr class="columCabezera">
                      <td class="columOne colum"><strong>Secuencial</strong></td>
                      <td class="columOne colum"><strong>Rol</strong></td>
                      <td class="columOne colum"><strong>Descripcion</strong></td>
                      <td class="columOne colum"><strong></strong></td>
                    </tr>
                    <?php echo $registroTwo ?>
                  </table>
                </center>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <center>
                <button type="button" class="btnNormal btnEdit" onclick="history.back()">Volver</button>
              </center>
            </td>
            <td>
              <center>
                <button type="submit" class="btnNormal btnEdit">Guardar cambios</button>
              </center>
            </td>
          </tr>
        </table>
      </center>
    </form>
</body>
<script>
  function enableRole(id_role_user, user) {
    Swal
      .fire({
        title: "Rol del usuario",
        text: "¿Desea quitar este rol de usuario?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: "Sí, quitar",
        cancelButtonText: "Cancelar",
      })
      .then(resultado => {
        if (resultado.value) {
          var parametros = {
            'role_user_id': id_role_user,
            'user_cedula': user
          };
          $.ajax({
            type: 'POST',
            url: 'administration_users/editRolesUser.php',
            data: parametros,
            success: function (resultado) {
              var roles = resultado.split("j_y");
              document.getElementById("reloadRolesUser").innerHTML = roles[0];
              if (roles.length > 1) {
                ids_roles_user.length = 0;
                for (var i = 1; i < roles.length; i++) {
                  ids_roles_user.push(roles[i]);
                }
              }
            },
            error: function (resultado) {
              Swal.fire({
                title: "Rol no quitado",
                text: "Error 0612: " + resultado,
                icon: "error",
              });
            }
          });
        }
      });
  }

  function AddRole(role_id, user) {
    for (var i = 0; i < ids_roles_user.length; i++) {
      if (ids_roles_user[i] == role_id) {
        Swal.fire({
          title: "Error: Rol no asignado",
          text: "Intenta asignar un rol que ya esta asignado",
          icon: "error",
        });
        return;
      }
    }
    Swal
      .fire({
        title: "Rol del usuario",
        text: "¿Desea agregar este rol de usuario?",
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: "Sí, añadir",
        cancelButtonText: "Cancelar",
      })
      .then(resultado => {
        if (resultado.value) {
          var parametros = {
            'role_id': role_id,
            'user_cedula': user
          };
          $.ajax({
            type: 'POST',
            url: 'administration_users/addRoleUser.php',
            data: parametros,
            success: function (resultado) {
              var tablas = resultado.split("j_y");
              if (tablas.length > 1) {
                document.getElementById("reloadRolesUser").innerHTML = tablas[0];
                document.getElementById("reloadRoles").innerHTML = tablas[1];
                if (tablas.length > 2) {
                  ids_roles_user.length = 0;
                  for (var i = 2; i < tablas.length; i++) {
                    ids_roles_user.push(tablas[i]);
                  }
                }

              }
            },
            error: function (resultado) {
              Swal.fire({
                title: "Rol no quitado",
                text: "Error 0612: " + resultado,
                icon: "error",
              });
            }
          });
        }
      });
  }

</script>

</html>