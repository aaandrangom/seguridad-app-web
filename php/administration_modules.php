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

$token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIwODA0MzIxMzcwIiwiZXhwIjoxNjkzMDg3NTcwfQ.-nw49HQwSP1FyMZbYf_r5S2eID4f10FKmGDfDyfChpE";
$url = "http://20.163.192.189:8080/api/module";
$headers = array(
  "Authorization: Bearer $token",
  "Content-Type: application/json"
);

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($curl);
curl_close($curl);

$modules = json_decode($response, true);

?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Administracion de Usuarios</title>
<link rel="stylesheet" type="text/css" href="..\css\principal.css">
<link rel="stylesheet" type="text/css" href="..\css\cabezera_principal.css">
<link rel="stylesheet" type="text/css" href="..\css\menu_principal.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/sweetalert2@9.5.3/dist/sweetalert2.all.min.js"></script>
</head>

<body>
<div class="cabezera">
		<strong>ADMINISTRACION DE MODULOS</strong>
	</div>	
	<header>
		<?php echo $_SESSION['menu_completo'];?>
	</header>
	<div>
		<center>
			<table class="tabla">
				<tr>
					<td class="columOne colum" colspan="2">
						<center>
						<button type="button" class="btnNormal btnEdit" onclick="open_create_module()">Crear Modulo</button>
						</center>
					</td>
				</tr>
				<tr class="columCabezera">
					<td class="columOne colum">
						<strong>Modulo</strong>
					</td>
					<td class="columOne colum">
						<strong>Acciones</strong>
					</td>
				</tr>
				<tbody id="body_table">
					<?php foreach ($modules['tb_module'] as $module) { ?>
						<tr class="rowOne">
							<td class="columOne">
								<?php echo $module['mod_name']; ?>
							</td>
							<td class="columOne">
								<div class="btnGroup">
									<button type="button" class="btnNormal btnInfo" onclick="abriModal(<?php echo $module['mod_id']; ?>)">Informacion</button>
									<form method="POST" action="edit_module.php">
										<input type="hidden" name="mod_id" value=<?php echo $module['mod_id']; ?>>
										<button type="submit" class="btnNormal btnEdit">Editar</button>
									</form>
									<button type="button" class="btnNormal btnDelete" onclick="abriModalConfirmacion(<?php echo $module['mod_id']; ?>)">Eliminiar</button>
								</div>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</center>

		<!--MODAL DE DATOS-->
		<div class="modal" data-animation="slideInOutLeft">			
			<div class="modal-content-Pequeño">
				<header class="modal-header">
					<button class="close-modal">
						✕  
					</button>
				</header>
				<div>
					<center>
						<table class="tablaModal">
							<tr>
								<td class=" colorOne">iD</td>							
							</tr>
							<tr>
								<td class=" emptyColum" id="mod_id"></td>
							</tr>							
							<tr>
								<td class=" colorOne">Nombre del modulo</td>								
							</tr>
							<tr>
								<td class=" emptyColum" id="mod_name"></td>
							</tr>
							<tr>
								<td class=" colorOne">Administrador</td>												
							</tr>
							<tr>
								<td class=" emptyColum" id="mod_admin"></td>
							</tr>							
							<tr>
								<td class=" colorOne">Estado</td>
							</tr>
							<tr>
								<td class=" emptyColum" id="mod_state"></td>
							</tr>
						</table>
					</center>
				</div>				
			</div>
		</div>

		<!--MODAL DE CREACION DE MODULO-->
		<div id ="div_create" class="modal" data-animation="slideInOutLeft">			
			<div class="modal-content-Pequeño">
				<header class="modal-header">
					<button id="close_modal_create" class="close-modal">
						✕  
					</button>
				</header>
				<div>
				<form id="form_create_mod" method="POST" action="administration_modules/createModule.php">
					<center>
						<table class="tablaModal">							
							<tr>
								<td class="colorOne">Nombre del modulo</td>								
							</tr>
							<tr>
								<td class="emptyColumTwo" id="mod_name">
								<input class="inputTexto" type="text" name="mod_name" id="mod_name"
								required required title="Por favor, ingresa el NOMBRE del modulo">
								</td>
							</tr>
							<tr>
								<td class="colorOne">Administrador</td>												
							</tr>
							<tr>
								<td class="emptyColumTwo" id="mod_admin">
								<input class="inputTexto" type="text" name="mod_admin" id="mod_admin"
								required required title="Por favor, ingresa el ADMIN del modulo">
								</td>
							</tr>							
							<tr>
								<td class="colorOne">Estado</td>
							</tr>
							<tr>
								<td class="emptyColumTwo" id="mod_state">
								<input class="inputTexto" type="text" name="mod_state" id="mod_state"
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

		<!--SCRIP MODAL -->
		<script>
			$("#form_create_mod").submit(function(event) {
				cerrarModal();
				event.preventDefault();
				let current_html_form = $(this);
				let action_url = current_html_form.attr('action');
				let form_data = new FormData(document.getElementById('form_create_mod'));

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
							window.location.href="administration_modules.php";
						});
					} else {
						Swal.fire({
						title: "Operacion inconlusa",
						text: "Por favor revisa los registros nuevamente",
						icon: "warning",
						}).then(()=>{
							window.location.href="administration_modules.php";
						});
					}
					},
					error: function (data) {
						Swal.fire({
							title: "Error al enviar los datos",
							text: "Por favor revisa los registros nuevamente",
							icon: "warning",
						}).then(()=>{
							window.location.href="administration_modules.php";
						});
					},
				});
				
			});

			function open_create_module(){
				var modalCreate = document.getElementById("div_create");
				modalCreate.classList.add("is-visible");
			}

			function abriModal(mod_id){
				var parametros = {'mod_id': mod_id};
				$.ajax({
					type: 'POST',
					url:   'administration_modules/getInfoModule.php', //archivo que recibe la peticion
					data: parametros,
					success: function(resultado) {
						var module_ = resultado.split("-");
						var modalInfo = document.querySelector(".modal");
						modalInfo.classList.add("is-visible");
						document.getElementById ("mod_id").innerHTML = module_[0];				
						document.getElementById ("mod_name").innerHTML = module_[1];
						document.getElementById ("mod_admin").innerHTML = module_[2];
						document.getElementById ("mod_state").innerHTML = module_[3];
					},
					error: function(resultado) {
						var error = resultado.split("-");
						console.log("Error: " + error);
					}
				});
			}

			function abriModalConfirmacion(mod_id){
				Swal
					.fire({
						title: "Modulo #"+mod_id,
						text: "¿Desea Eliminar?",
						icon: 'warning',
						showCancelButton: true,
						confirmButtonText: "Sí, eliminar",
						cancelButtonText: "Cancelar",
					})
					.then(resultado => {
						if (resultado.value) {
							var parametros = {'mod_id': mod_id};
							$.ajax({
								type: 'POST',
								url:   'administration_modules/deleteModule.php', //archivo que recibe la peticion
								data: parametros,
								success: function(resultado) {
									window.location.href = "administration_modules.php";
								},
								error: function(resultado) {
									Swal.fire({
										title: "Eliminacion no realizada",
										text: "Error 0612: "+resultado,
										icon: "error",
									});
								}
							});
						} else {
							
						}
					});
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

			window.setInterval(actualizacionModules, 10000);
			function actualizacionModules() {
				$.ajax({
					type: 'POST',
					url:   'administration_modules/getModules.php', //archivo que recibe la peticion
					data: {},
					success: function(resultado) {
						document.getElementById("body_table").innerHTML=resultado;
					},
					error: function(resultado) {
						window.location.href = "administration_modules.php";
					}
				});
			}
		</script>
	</div>
</body>
</html>
