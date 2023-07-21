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

if (isset($_POST['rol_id'])) {
  $role_id = htmlspecialchars($_POST['rol_id']);

  // Token de autenticación
  $token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIwODA0MzIxMzcwIiwiZXhwIjoxNjk0OTI4ODIxfQ.zOJEUMiDj4vNHkv-EoCNazOp5Os6axj_-PGyByqQXsg";

  // Realizar una solicitud a la API para obtener los datos del usuario por su cédula
  $url = "http://20.163.192.189:8080/api/role/" . $role_id;
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $token));

  $response = curl_exec($ch);
  $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

  curl_close($ch);

  if ($http_code == 200) {

    $role = json_decode($response, true);
    $rol_id = $role['rol_id'];
    $rol_role = $role['rol_role'];
    $rol_description = $role['rol_description'];
    $rol_allowed_users = $role['rol_allowed_users'];
    $rol_state = $role['rol_state'];

  } else {
    echo "Error al obtener los datos del rol. Código de respuesta HTTP: " . $http_code;
  }

  $url = "http://20.163.192.189:8080/api/role_function/role/" . $rol_id;
  $ch = curl_init($url);

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $token));

  $response = curl_exec($ch);
  $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

  curl_close($ch);
  $registro = "";
  ?>
  <script>var ids_roles_functions = [];</script>
  <?php
  if ($http_code == 200) {
    $funtions_role = json_decode($response, true);
    foreach ($funtions_role['tb_role_function'] as $function) {
      $url = "http://20.163.192.189:8080/api/function/".$function['rol_func_function'];
      $ch = curl_init($url);

      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $token));

      $response = curl_exec($ch);
      $http_code_in = curl_getinfo($ch, CURLINFO_HTTP_CODE);

      curl_close($ch);
      ?>
      <script>ids_roles_functions.push(<?php echo $function['rol_func_function']; ?>);</script>
      <?php
      if ($http_code_in == 200) {
        $function_role = json_decode($response, true);
        $registro = $registro."
        <tr class='rowOne'>
            <td class='columOne'><center>".$function_role['func_name']."</center></td>
            <td class='columOne'>
            <center>
                <button class='btnNormal btnDelete'type='button' onclick='enableFunction(".$function['rol_func_id'].", ".$rol_id.")'>✕</button>
            </center>
            </td>
        </tr>
        ";
      }
    }
  } else {
    $registro = "
      <tr>
          <td colspan='2'>
              El rol no cuenta con funciones asignadas
          </td>
      </tr>";
  }
  $registroTwo = "";
  $url = "http://20.163.192.189:8080/api/function";
  $ch = curl_init($url);

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $token));

  $response = curl_exec($ch);
  $http_code_in = curl_getinfo($ch, CURLINFO_HTTP_CODE);

  curl_close($ch);

  if ($http_code_in == 200) {
    $functions = json_decode($response, true);
    foreach ($functions['tb_function'] as $function) {
      $registroTwo = $registroTwo . "
      <tr class='rowOne'>
        <td class='columOne'>".$function['func_id']."</td>
        <td class='columOne'>".$function['func_name']."</td>
        <td class='columOne'>
          <button class='btnNormal btnEdit'type='button' onclick='AddFunction(".$function['func_id'].", ".$role_id.")'>✓</button>
        </td>
      </tr>
    ";
    }
  } else {
    $registroTwo = "
      <tr>
          <td colspan='3'>
              No existen funciones
          </td>
      </tr>";
  }
} else {
  ?>
  <script>
    Swal.fire({
      title: "Error: !Sin rol¡",
      text: "Error 1206: " + <?php echo $http_code ?>,
      icon: "error",
    }).then(resultado => {
      if (resultado.value) {
        window.location.href = "administration_roles.php";
      }
    });;
  </script>
  <?php
}
?>

<!DOCTYPE html>
<html>

<head>
  <title>Editar Rol</title>
  <link rel="stylesheet" type="text/css" href="../css/cabezera_principal.css">
  <link rel="stylesheet" type="text/css" href="../css/menu_principal.css">
  <link rel="stylesheet" type="text/css" href="..\css\principal.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://unpkg.com/sweetalert2@9.5.3/dist/sweetalert2.all.min.js"></script>
</head>

<body>

  <div class="cabezera">
    <strong>MODULO DE SEGURIDAD - EDITAR ROL</strong>
  </div>
  <header>
    <?php echo $_SESSION['menu_completo']; ?>
  </header>
  <div>

    <form id="form_edit_rol" method="POST" action="/php/save_role.php">
      <center>
        <table width="90%">
          <tr>
            <td colspan="2">
              <h2>
                <center>Editar Rol</center>
              </h2>
            </td>
          </tr>
          <tr>
            <td width="50%" rowspan="2">
              <input type="hidden" name="_method" value="PUT">
              <input type="hidden" name="rol_id_current" value="<?php echo $rol_id; ?>">
              <center>
                <table class="tablaTwo">
                  <tr>
                    <td class=" colorOne"><strong>Id</strong></td>
                    <td class=" colorOne"><strong>Rol</strong></td>
                  </tr>
                  <tr>
                    <td class="emptyColumTwo">
                      <input class="inputTexto" type="text" name="rol_id" id="rol_id" value="<?php echo $rol_id; ?>">
                    </td>
                    <td class="emptyColumTwo">
                      <input class="inputTexto" type="text" name="rol_role" id="rol_role"
                        value="<?php echo $rol_role; ?>">
                    </td>
                  </tr>
                  <tr>
                    <td class=" colorOne"><strong>Descripcion</strong></td>
                    <td class=" colorOne"><strong>Cant. maxima de Usuarios</strong></td>
                  </tr>
                  <tr>
                    <td class="emptyColumTwo">
                      <input class="inputTexto" type="text" name="rol_description" id="rol_description"
                        value="<?php echo $rol_description; ?>">
                    </td>
                    <td class="emptyColumTwo">
                      <input class="inputTexto" type="text" name="rol_allowed_users" id="rol_allowed_users"
                        value="<?php echo $rol_allowed_users; ?>">
                    </td>
                  </tr>
                  <tr>
                    <td class=" colorOne"><strong>Estado</strong></td>
                    <td class=" colorOne"></td>
                  </tr>
                  <tr>
                    <td>
                      <div style="position: relative; width: 100%;">
                        <select name="rol_state" id="rol_state" class="form-control" required
                          style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; background-color: #fff; font-size: 14px; appearance: none; -moz-appearance: none; -webkit-appearance: none;">
                          <option value="A" <?php if ($rol_state === 'A')
                            echo 'selected'; ?>>Habilitado</option>
                          <option value="I" <?php if ($rol_state === 'I')
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
              <div id="reloadFuncionesRol" style="width: 100%; height: 175px; overflow-y: scroll;">
                <center>
                  <table class="tablaDetalles" width="95%">
                    <tr class="columCabezera">
                      <td class="columOne colum"><strong>Funcion</strong></td>
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
              <div id="reloadFunciones" style="width: 100%; height: 175px; overflow-y: scroll;">
                <center>
                  <table class="tablaDetalles" width="95%">
                    <tr class="columCabezera">
                      <td class="columOne colum"><strong>Secuencial</strong></td>
                      <td class="columOne colum"><strong>Funcion</strong></td>
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
                <button type="button" class="btnNormal btnEdit" onclick="volverAtras()">Volver</button>
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
  function enableFunction(rol_func_id, role_id) {
    Swal
      .fire({
        title: "Funcion del rol",
        text: "¿Desea quitar esta funcion del rol",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: "Sí, quitar",
        cancelButtonText: "Cancelar",
      })
      .then(resultado => {
        if (resultado.value) {
          var parametros = {
            'role_func_id': rol_func_id,
            'role_id': role_id
          };
          $.ajax({
            type: 'POST',
            url: 'administration_roles/editFunctionRole.php',
            data: parametros,
            success: function (resultado) {
              var funciones = resultado.split("j_y");
              document.getElementById("reloadFuncionesRol").innerHTML = funciones[0];
              if (funciones.length > 1) {
                ids_roles_functions.length = 0;
                for (var i = 1; i < funciones.length; i++) {
                    ids_roles_functions.push(funciones[i]);
                }
              }
            },
            error: function (resultado) {
              Swal.fire({
                title: "Funcion no quitada",
                text: "Error 0612: " + resultado,
                icon: "error",
              });
            }
          });
        }
      });
  }

  function AddFunction(function_id, role_id) {
    for (var i = 0; i < ids_roles_functions.length; i++) {
      if (ids_roles_functions[i] == function_id) {
        Swal.fire({
          title: "Error: Funcion no asignada",
          text: "Intenta asignar una funcion que ya esta asignada",
          icon: "error",
        });
        return;
      }
    }
    Swal
      .fire({
        title: "Funcion del rol",
        text: "¿Desea agregar esta funcion de rol?",
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: "Sí, añadir",
        cancelButtonText: "Cancelar",
      })
      .then(resultado => {
        if (resultado.value) {
          var parametros = {
            'function_id': function_id,
            'role_id': role_id
          };
          $.ajax({
            type: 'POST',
            url: 'administration_roles/addFunctionRole.php',
            data: parametros,
            success: function (resultado) {
              var tablas = resultado.split("j_y");
              if (tablas.length > 1) {
                document.getElementById("reloadFuncionesRol").innerHTML = tablas[0];
                document.getElementById("reloadFunciones").innerHTML = tablas[1];
                if (tablas.length > 2) {
                    ids_roles_functions.length = 0;
                  for (var i = 2; i < tablas.length; i++) {
                    ids_roles_functions.push(tablas[i]);
                  }
                }
              }
            },
            error: function (resultado) {
              Swal.fire({
                title: "Funcion no quitada",
                text: "Error 0612: " + resultado,
                icon: "error",
              });
            }
          });
        }
      });
  }

  $("#form_edit_rol").submit(function(event) {
          event.preventDefault();
          let current_html_form = $(this);
          let action_url = current_html_form.attr('action');
          let form_data = new FormData(document.getElementById('form_edit_rol'));

          $.ajax({
              type: "POST",
              url: action_url,
              data: form_data,
              processData: false,
              contentType: false,
              success: function (data) {
              data = data.split("-");
              if (data.length == 3){
                Swal.fire({
                  title: data[0],
                  text: data[1],
                  icon: data[2],
                });
              } else {
                Swal.fire({
                  title: "Operacion inconlusa",
                  text: "Por favor revisa los registros nuevamente",
                  icon: "warning",
              });
              }
            },
            error: function (data) {
              Swal.fire({
                  title: "Error al enviar los datos",
                  text: "Por favor revisa los registros nuevamente",
                  icon: "warning",
              });
            },
          });
      });

      function volverAtras(){
        window.location.href="/php/administration_roles.php";
      }
</script>

</html>