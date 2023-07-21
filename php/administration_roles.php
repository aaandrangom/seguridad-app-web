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
$url = "http://20.163.192.189:8080/api/role";
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

$roles = json_decode($response, true);

?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Administracion de Roles</title>
<link rel="stylesheet" type="text/css" href="..\css\principal.css">
<link rel="stylesheet" type="text/css" href="..\css\cabezera_principal.css">
<link rel="stylesheet" type="text/css" href="..\css\menu_principal.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/sweetalert2@9.5.3/dist/sweetalert2.all.min.js"></script>
</head>

<body>
<div class="cabezera">
		<strong>ADMINISTRACION DE ROLES</strong>
	</div>	
	<header>
		<?php echo $_SESSION['menu_completo'];?>
	</header>
	<div>
		<center>
			<table class="tabla">
				<tr class="columCabezera">
					<td class="columOne colum">
						<strong>Rol</strong>
					</td>
					<td class="columOne colum">
						<strong>Acciones</strong>
					</td>
				</tr>
				<?php foreach ($roles['role'] as $role) { ?>
					<tr class="rowOne">
						<td class="columOne">
							<?php echo $role['rol_role']; ?>
						</td>
						<td class="columOne">
							<div class="btnGroup">
								<button type="button" class="btnNormal btnInfo" onclick="abriModal('<?php echo $role['rol_id']; ?>')">Informacion</button>
								<form method="POST" action="/php/edit_role.php">
									<input type="hidden" name="rol_id" value="<?php echo $role['rol_id']; ?>">
									<button type="submit" class="btnNormal btnEdit">Editar</button>
								</form>
								<button type="button" class="btnNormal btnDelete" onclick="abriModalConfirmacion('<?php echo $role['rol_id']; ?>')">Eliminiar</button>
							</div>
						</td>
					</tr>
				<?php } ?>
			</table>
		</center>

		<!--MODAL DE DATOS-->
		<div class="modal" data-animation="slideInOutLeft">			
			<div class="modal-content">
				<header class="modal-header">
					<button class="close-modal">
						✕  
					</button>
				</header>
				<div>
					<center>
						<table class="tablaModal" with="98%">
							<tr>
								<td class=" colorOne">Rol</td>
								<td class=" colorOne" >Descripción</td>
								
							</tr>
							<tr>
								<td class=" emptyColum" id="rol"></td>
								<td class=" emptyColum" id="descripcion"></td>
							</tr>							
							<tr>
								<td class=" colorOne">Usuarios Habilitados</td>
								<td class=" colorOne">Estado</td>
								
							</tr>
							<tr>
								<td class=" emptyColum" id="usr_habilitados"></td>
								<td class=" emptyColum" id="estado"></td>
							</tr>
						</table>
					</center>
				</div>
					
			</div>
		</div>

		<!--SCRIP MODAL -->
		<script>

			function abriModal(rolId){
				var parametros = {'rolId': rolId};
				$.ajax({
					type: 'POST',
					url:   'administration_roles/getInfoRole.php', //archivo que recibe la peticion
					data: parametros,
					success: function(resultado) {
						var user = resultado.split("-");
						var modalInfo = document.querySelector(".modal");
						modalInfo.classList.add("is-visible");			
						document.getElementById ("rol").innerHTML = user[1];
						document.getElementById ("descripcion").innerHTML = user[2];
						document.getElementById ("usr_habilitados").innerHTML = user[3];
						document.getElementById ("estado").innerHTML = user[4];
					},
					error: function(resultado) {
						var error = resultado.split("-");
						console.log("Error: " + error);
					}
				});
			}

			function abriModalConfirmacion(rolId){
				Swal
					.fire({
						title: "Usuario "+rolId,
						text: "¿Desea Eliminar?",
						icon: 'warning',
						showCancelButton: true,
						confirmButtonText: "Sí, eliminar",
						cancelButtonText: "Cancelar",
					})
					.then(resultado => {
						if (resultado.value) {
							var parametros = {'rolId': rolId};
							$.ajax({
								type: 'POST',
								url:   'administration_roles/deleteRole.php', //archivo que recibe la peticion
								data: parametros,
								success: function(resultado) {
									window.location.href = "administration_roles.php";
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
			btnCloseModal.addEventListener('click', cerrarModal);

			function cerrarModal() {
				var modalInfo = document.querySelector(".modal.is-visible");
				modalInfo.classList.remove("is-visible");
			}

			document.addEventListener("keyup", e => {
				if (e.key == "Escape" && document.querySelector(".modal.is-visible")) {
				document.querySelector(".modal.is-visible").classList.remove("is-visible");
				}
			});
		</script>
	</div>
</body>
</html>
