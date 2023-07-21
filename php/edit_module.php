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

if (isset($_POST['mod_id'])) {
  $mod_id = $_POST['mod_id'];

  // Token de autenticación
  $token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIwODA0MzIxMzcwIiwiZXhwIjoxNjkzMDg3NTcwfQ.-nw49HQwSP1FyMZbYf_r5S2eID4f10FKmGDfDyfChpE";

  // Realizar una solicitud a la API para obtener los datos del usuario por su cédula
  $url = "http://20.163.192.189:8080/api/module/".$mod_id;
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $token));

  $response = curl_exec($ch);
  $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

  curl_close($ch);

  if ($http_code == 200) {

    $module = json_decode($response, true);
    $mod_id = $module['mod_id'];
    $mod_name = $module['mod_name'];
    $mod_admin = $module['mod_admin'];
    $mod_state = $module['mod_state'];

  } else {
    echo "Error al obtener los datos del modulo. Código de respuesta HTTP: " . $http_code;
  }

  $url = "http://20.163.192.189:8080/api/function_mod/".$mod_id;
  $ch = curl_init($url);

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $token));

  $response = curl_exec($ch);
  $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

  curl_close($ch);
  $registro = "";
  ?><script>var ids_functions_module = [];</script><?php
  if ($http_code == 200) {
    $functions_module = json_decode($response, true);
    foreach ($functions_module['tb_function'] as $function_mod){
        $registro = $registro."
        <tr class='rowOne'>
            <td class='columOne'><center>".$function_mod['func_name']."</center></td>
            <td class='columOne'>
            <center>
                <button class='btnNormal btnDelete'type='button' onclick='enableFunction(".$function_mod['func_id'].")'>✕</button>
            </center>
            </td>
        </tr>
        ";
    }
  } else {
    $registro = "
      <tr>
          <td colspan='2'>
              El modulo no cuenta con funciones asignadas
          </td>
      </tr>";
  }
} else {
  ?>
  <script>
    alert("Sin mod");
    window.location.href="administration_modules.php";
  </script>
  <?php
}
?>

<!DOCTYPE html>
    <html>

    <head>
      <title>Editar Modulo</title>
      <link rel="stylesheet" type="text/css" href="../css/cabezera_principal.css">
      <link rel="stylesheet" type="text/css" href="../css/menu_principal.css">
      <link rel="stylesheet" type="text/css" href="..\css\principal.css">
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <script src="https://unpkg.com/sweetalert2@9.5.3/dist/sweetalert2.all.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
     </head>

    <body>

      <div class="cabezera">
        <strong>MODULO DE SEGURIDAD - EDITAR MODULO</strong>
      </div>
      <header>
        <?php echo $_SESSION['menu_completo'];?>
      </header>
      <div>

      <form id="form_edit_mod" method="POST" action="/php/save_module.php">
        <center>
          <table width="90%">
              <tr>
                <td colspan="2">
                  <h2><center>Editar Modulo</center></h2>
                </td>
              </tr>
              <tr>
                <td width="50%" rowspan="2">   
                  <input type="hidden" name="_method" value="PUT">
                  <input type="hidden" name="mod_id_current" value="<?php echo $mod_id; ?>">              
                  <center>
                    <table class="tablaModal">
                      <tr>
                        <td class="colorOne">iD</td>							
                      </tr>
                      <tr>
                        <td class="emptyColumTwo">
                          <input class="inputTexto" type="text" name="mod_id" id="mod_id"
                                value="<?php echo $mod_id; ?>">
                        </td>
                      </tr>							
                      <tr>
                        <td class="colorOne">Nombre del modulo</td>								
                      </tr>
                      <tr>
                        <td class="emptyColumTwo">
                          <input class="inputTexto" type="text" name="mod_name" id="mod_name"
                                value="<?php echo $mod_name; ?>">
                        </td>
                      </tr>
                      <tr>
                        <td class="colorOne">Administrador</td>												
                      </tr>
                      <tr>
                        <td class="emptyColumTwo">
                          <input class="inputTexto" type="text" name="mod_admin" id="mod_admin"
                                value="<?php echo $mod_admin; ?>">
                        </td>
                      </tr>							
                      <tr>
                        <td class="colorOne">Estado</td>
                      </tr>
                      <tr>
                        <td class="emptyColumTwo">
                          <input class="inputTexto" type="text" name="mod_state" id="mod_state"
                                value="<?php echo $mod_state; ?>">
                        </td>
                      </tr>
                    </table>
                  </center>
                </td>
                <td>
                  <div style="width: 100%; height: 175px; overflow-y: scroll;">
                    <center>
                      <table class="tablaDetalles" width="95%">
                        <tr class="columCabezera">
                          <td class="columOne colum"><strong>Funciones</strong></td>
                          <td class="columOne colum"><strong>
                          <button type="button" class="btnCircular btnInfo" onclick="open_create_function()">+</button>
                          </strong></td>
                        </tr>
                        <tbody id="reloadFunctionsModule" >
                          <?php echo $registro ?>
                        </tbody> 
                      </table>
                    </center>
                  </div>
                </td>   
              </tr>
              <tr>
              <tr>
                <td>
                </td>
              </tr>
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
      <form id="form_reload_page" method="POST" action="editInfoModule.php">
        <input type="hidden" name="mod_id" id="mod_id" value="<?php echo $mod_id ?>">
      </form>
      <!--MODAL DE CREACION DE MODULO-->
      <div id ="div_create" class="modal" data-animation="slideInOutLeft">			
        <div class="modal-content-Pequeño">
          <header class="modal-header">
            <button id="close_modal_create" class="close-modal">
              ✕  
            </button>
          </header>
          <div>
          <form id="form_create_func" method="POST" action="administration_modules/createFunction.php">
            <input type="hidden" name="func_module" id="func_module" value="<?php echo $mod_id ?>">
            <center>
              <table class="tablaModal">							
                <tr>
                  <td class="colorOne"><center><h2>Crear Funcion</h2></center></td>								
                </tr>
                <tr>
                  <td class="colorOne">Nombre de la funcion</td>								
                </tr>
                <tr>
                  <td class="emptyColumTwo">
                  <input class="inputTexto" type="text" name="func_name" id="func_name"
                  required required title="Por favor, ingresa el NOMBRE del modulo">
                  </td>
                </tr>					
                <tr>
                  <td class="colorOne">Estado</td>
                </tr>
                <tr>
                  <td class="emptyColumTwo">
                  <input class="inputTexto" type="text" name="func_state" id="func_state"
                  required required title="Por favor, ingresa el ESTADO del modulo"
                  value="A">
                  </td>
                </tr>
                <tr>
                  <td>
                    <center>
                      <button type="submit" class="btnNormal btnEdit">Crear</button>
                    </center>
                  </td>
                </tr>
              </table>
            </center>
          </form>
          </div>				
        </div>
      </div>
    </body>
    <script>
      $("#form_edit_mod").submit(function(event) {
          event.preventDefault();
          let current_html_form = $(this);
          let action_url = current_html_form.attr('action');
          let form_data = new FormData(document.getElementById('form_edit_mod'));

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

      function enableFunction(id_function_module){
        Swal
					.fire({
						title: "Funcion del modulo",
						text: "¿Desea quitar esta funcion del modulo?",
						icon: 'warning',
						showCancelButton: true,
						confirmButtonText: "Sí, quitar",
						cancelButtonText: "Cancelar",
					})
					.then(resultado => {
						if (resultado.value) {
              var parametros = {'function_module_id': id_function_module,
                    'mod_id': <?php echo $mod_id?>};
              $.ajax({
                type: 'POST',
                url:   'administration_modules/deleteFunctionsModule.php',
                data: parametros,
                success: function(resultado) {
                  document.getElementById("reloadFunctionsModule").innerHTML = resultado;                
                },
                error: function(resultado) {
                  Swal.fire({
										title: "Funcion no quitada",
										text: "Error 0612: "+resultado,
										icon: "error",
									});
                }
              });
						}
					});
      }

      $("#form_create_func").submit(function(event) {
				cerrarModal();
				event.preventDefault();
				let current_html_form = $(this);
				let action_url = current_html_form.attr('action');
				let form_data = new FormData(document.getElementById('form_create_func'));

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
						}).then(()=>{
							var reload = document.getElementById('form_reload_page');
              reload.submit();
						});
					} else {
						Swal.fire({
						title: "Operacion inconlusa",
						text: "Por favor revisa los registros nuevamente",
						icon: "warning",
						}).then(()=>{
							var reload = document.getElementById('form_reload_page');
              reload.submit();
						});
					}
					},
					error: function (data) {
						Swal.fire({
							title: "Error al enviar los datos",
							text: "Por favor revisa los registros nuevamente",
							icon: "warning",
						}).then(()=>{
							var reload = document.getElementById('form_reload_page');
              reload.submit();
						});
					},
				});
				
			});

			function open_create_function(){
				var modalCreate = document.getElementById("div_create");
				modalCreate.classList.add("is-visible");
			}

			var btnCloseModal = document.querySelector(".close-modal");
			var btnCloseModalCreate = document.getElementById("close_modal_create");
			btnCloseModal.addEventListener('click', cerrarModal);
			btnCloseModalCreate.addEventListener('click', cerrarModal);

			function cerrarModal() {
				var modalInfo = document.querySelector(".modal.is-visible");
				modalInfo.classList.remove("is-visible");
			}

			document.addEventListener("keyup", e => {
				if (e.key == "Escape" && document.querySelector(".modal.is-visible")) {
				document.querySelector(".modal.is-visible").classList.remove("is-visible");
				}
			});

      function volverAtras(){
        window.location.href="/php/administration_modules.php";
      }
    </script>
    </html>