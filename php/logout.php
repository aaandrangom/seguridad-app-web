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
	$token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIwODA0MzIxMzcwIiwiZXhwIjoxNjkzMDg3NTcwfQ.-nw49HQwSP1FyMZbYf_r5S2eID4f10FKmGDfDyfChpE";
	$url = "http://20.163.192.189:8080/api/auditoria/cerrar_sesion";
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
	session_destroy();
	header("Location: /index.php");
	exit();
}
?>