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

$token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIwODA0MzIxMzcwIiwiZXhwIjoxNjk0OTI4ODIxfQ.zOJEUMiDj4vNHkv-EoCNazOp5Os6axj_-PGyByqQXsg";
$url = "http://20.163.192.189:8080/api/user";
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

$users = json_decode($response, true);

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
		<strong>ADMINISTRACION DE USUARIOS</strong>
	</div>	
	<header>
		<?php echo $_SESSION['menu_completo'];?>
	</header>
	<div>
		<center>
			<table class="tabla">
				<tr class="columCabezera">
					<td class="columOne colum">
						<strong>Usuario</strong>
					</td>
					<td class="columOne colum">
						<strong>Acciones</strong>
					</td>
				</tr>
				<?php foreach ($users['tb_user'] as $user) { ?>
					<tr class="rowOne">
						<td class="columOne">
							<?php echo $user['usr_full_name']; ?>
						</td>
						<td class="columOne">
							<div class="btnGroup">
								<button type="button" class="btnNormal btnInfo" onclick="abriModal('<?php echo $user['usr_id']; ?>')">Informacion</button>
								<form method="POST" action="/php/edit_user.php">
									<input type="hidden" name="usr_id" value="<?php echo $user['usr_id']; ?>">
									<button type="submit" class="btnNormal btnEdit">Editar</button>
								</form>
								<button type="button" class="btnNormal btnDelete" onclick="abriModalConfirmacion('<?php echo $user['usr_id']; ?>')">Eliminiar</button>
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
								<td class=" colorOne">Cedula</td>
								<td class=" colorOne" >Estado</td>
								
							</tr>
							<tr>
								<td class=" emptyColum" id="cedula"></td>
								<td class=" emptyColum" id="estado"></td>
							</tr>							
							<tr>
								<td class=" colorOne">Primer Nombre</td>
								<td class=" colorOne">Segundo Nombre</td>
								
							</tr>
							<tr>
								<td class=" emptyColum" id="primer_nombre"></td>
								<td class=" emptyColum" id="segundo_nombre"></td>
							</tr>
							<tr>
								<td class=" colorOne">Primer Apellido</td>
								<td class=" colorOne">Segundo Apellido</td>
							</tr>
							<tr>
								<td class=" emptyColum" id="primer_apellido"></td>
								<td class=" emptyColum" id="segundo_apellido"></td>
							</tr>
							<tr>
								<td class=" colorOne">Nombre de Usuario</td>
								<td class=" colorOne">Correo Electrónico</td>
							</tr>
							<tr>
								<td class=" emptyColum" id="usuario"></td>
								<td class=" emptyColum" id="email"></td>
							</tr>
						</table>
					</center>
				</div>
					
			</div>
		</div>

		<!--SCRIP MODAL -->
		<script>

			function abriModal(ced){
				var parametros = {'cedula': cedula};
				$.ajax({
					type: 'POST',
					url:   'administration_users/getInfoUser.php', //archivo que recibe la peticion
					data: {cedula: ced},
					success: function(resultado) {
						var user = resultado.split("-");
						var modalInfo = document.querySelector(".modal");
						modalInfo.classList.add("is-visible");
						document.getElementById ("cedula").innerHTML = user[0];				
						document.getElementById ("primer_nombre").innerHTML = user[1];
						document.getElementById ("segundo_nombre").innerHTML = user[2];
						document.getElementById ("primer_apellido").innerHTML = user[3];
						document.getElementById ("segundo_apellido").innerHTML = user[4];
						document.getElementById ("usuario").innerHTML = user[6];
						document.getElementById ("email").innerHTML = user[7];
						document.getElementById ("estado").innerHTML = user[8];
					},
					error: function(resultado) {
						var error = resultado.split("-");
						console.log("Error: " + error);
					}
				});
			}

			function abriModalConfirmacion(ced){
				Swal
					.fire({
						title: "Usuario "+ced,
						text: "¿Desea Eliminar?",
						icon: 'warning',
						showCancelButton: true,
						confirmButtonText: "Sí, eliminar",
						cancelButtonText: "Cancelar",
					})
					.then(resultado => {
						if (resultado.value) {
							var parametros = {'cedula': cedula};
							$.ajax({
								type: 'POST',
								url:   'administration_users/deleteUser.php', //archivo que recibe la peticion
								data: {cedula: ced},
								success: function(resultado) {
									window.location.href = "administration_users.php";
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
