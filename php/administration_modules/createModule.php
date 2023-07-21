<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mod_name']) 
    and isset($_POST['mod_admin']) 
    and isset($_POST['mod_state'])) {
        $mod_name = $_POST['mod_name'];
        $mod_admin = $_POST['mod_admin'];
        $mod_state = $_POST['mod_state'];

        $created_module = [
            'mod_name' => $mod_name,
            'mod_admin' => $mod_admin,
            'mod_state' => $mod_state
        ];

        $json_data = json_encode($created_module);

        $url = "http://20.163.192.189:8080/api/module";

        // Establecer los encabezados de autenticación
        $access_token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIwODA0MzIxMzcwIiwiZXhwIjoxNjkzMDg3NTcwfQ.-nw49HQwSP1FyMZbYf_r5S2eID4f10FKmGDfDyfChpE"; // Reemplaza con tu token de acceso real
        $headers = array(
        'Authorization: Bearer '.$access_token,
        'Content-Type: application/json'
        );

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($http_code == 200) {
            $title = "Creacion Completa";
            $text = "Modulo creado con exito";
            $icon = "success";
        } else {
            $title = "Error al Crear";
            $text = "Error al crear el modulo. Código de respuesta HTTP: ".$http_code;
            $icon = "error";
        }
    } else {
        $title = "Error al Crear";
        $text = "Modulo NO creado. No se han enviado los datos correctamente";
        $icon = "error";
    }    
} else {
    $title = "Error al Crear";
    $text = "Método no permitido. Se esperaba una solicitud POST.";
    $icon = "error";
}
echo $title."-".$text."-".$icon;
?>