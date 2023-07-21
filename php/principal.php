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
$funciones = json_encode($_SESSION['funciones']);
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title>Inicio</title>
	<link rel="stylesheet" type="text/css" href="/css/cabezera_principal.css">
	<link rel="stylesheet" type="text/css" href="/css/principal.css">
	<link rel="stylesheet" type="text/css" href="/css/menu_principal.css">
	<link rel="stylesheet" type="text/css" href="/css/carrusel.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css">
</head>

<body>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
	<div class="cabezera">
		<strong>PRINCIPAL MODULO DE SEGURIDAD</strong>
	</div>
	<header id='menu'>
		<nav id='navegacion'>
			<ul id='opciones_menu' class='ul_menu'>
				<li class='li_menu'>
					<form method="post" action="/php/logout.php">
						<input class="btnCerrar" type="submit" name="logout" value="Cerrar sesión">
					</form>
				</li>
				<li id='Reportes' class='li_menu'><a href='/php/reporte.php'>Reportes</a></li>
				<li id='Usuarios' class='li_menu'><a href='/php/administration_users.php'>Usuarios</a>
					<ul class='ul_submenu'>
						<li class='li_submenu'><a href='/html/register.php'>Crear</a></li>
					</ul>
				</li>
				<li id='Modulos' class='li_menu'><a href='/php/administration_modules.php'>Modulos</a></li>
				<li id='Roles' class='li_menu'><a href='/php/administration_roles.php'>Roles</a>
					<ul class='ul_submenu'>
						<li class='li_submenu'><a href='/html/register_role.php'>Crear</a></li>
					</ul>
				</li>
				<li class='li_menu'><a href='/php/principal.php'>Inicio</a></li>
			</ul>
		</nav>
	</header>

	<!-- Carrusel de imágenes -->
	<div class="carrusel">
		<div><img src="https://www.cyberpower.com/mx/es/File/GetImageByDocId/BLG20211221001" alt="Imagen 1"></div>
		<div><img src="https://sentrio.io/wp-content/uploads/duotone-26.jpg"
				alt="Imagen 2"></div>
		<div><img
				src="https://www.itsitio.com/wp-content/uploads/2021/11/4b117d276fec117bc8820e5c4f0be830_XL-780x470.jpg"
				alt="Imagen 3"></div>
	<!-- Agrega más imágenes según sea necesario -->
	</div>
</body>

</html>


<script>
	$(document).ready(function () {
		$('.carrusel').slick({
			dots: true,
			infinite: true,
			speed: 500,
			slidesToShow: 1,
			slidesToScroll: 1,
			centerMode: true,
			centerPadding: "0",
			prevArrow: "<button type='button' class='slick-prev'>&#8592;</button>",
			nextArrow: "<button type='button' class='slick-next'>&#8594;</button>",
		});
	});

	var menu = document.getElementById("opciones_menu");
	var opciones_menu = menu.querySelectorAll('li');
	var funciones = <?php echo $funciones; ?>;
	console.log(opciones_menu.length);
	for (var opcion of opciones_menu) {
		if (opcion.id != "") {
			if (funciones.includes(opcion.id)) {
				console.log("Si incluye " + opcion.id);
			} else {
				console.log("No incluye " + opcion.id);
				var funcion = document.getElementById(opcion.id);
				funcion.remove();
			}
		}
	}
	var menu_final = document.getElementById("menu").innerHTML;
	console.log("Desde la principal: " + menu_final);
	$.ajax({
		type: 'POST',
		url: 'distribucion_menu.php',
		data: { "menu": menu_final }
	});
</script>