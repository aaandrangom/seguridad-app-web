<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['rol_id']) 
    and isset($_POST['rol_role']) 
    and isset($_POST['rol_description']) 
    and isset($_POST['rol_allowed_users'])
    and isset($_POST['rol_state'])
    and isset($_POST['rol_id_current'])){
        $rol_id = $_POST['rol_id'];
        $rol_role = $_POST['rol_role'];
        $rol_description = $_POST['rol_description'];
        $rol_allowed_users = $_POST['rol_allowed_users'];
        $rol_state = $_POST['rol_state'];
        $rol_id_current = $_POST['rol_id_current'];

        $updated_role = [
            'rol_id' =>  $rol_id,
            'rol_role' => $rol_role,
            'rol_description' => $rol_description,
            'rol_allowed_users' => $rol_allowed_users,
            'rol_state' => $rol_state
        ];
        $query_params = http_build_query($updated_role);
        $url = "http://20.163.192.189:8080/api/role/". $rol_id_current."?".$query_params;
        $access_token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIwODA0MzIxMzcwIiwiZXhwIjoxNjkzMDg3NTcwfQ.-nw49HQwSP1FyMZbYf_r5S2eID4f10FKmGDfDyfChpE"; // Reemplaza con tu token de acceso real
        $headers = [
        'Authorization: Bearer '.$access_token,
        'Content-Type: application/json'
        ];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code == 200) {
            $title = "Actualizacion Completa";
            $text = "Rol actualizado con exito";
            $icon = "success";
        } else {
            $title = "Error al Actualizar";
            $text = "Error al guardar los cambios. Código de respuesta HTTP: ".$http_code;
            $icon = "error";
        }
    } else {
        $title = "Error al Actualizar";
        $text = "Rol NO actualizado. No se han enviado los datos correctamente";
        $icon = "error";
    }
 
} else {
    $title = "Error al Actualizar";
    $text = "Método no permitido. Se esperaba una solicitud PUT.";
    $icon = "error";
}
echo $title."-".$text."-".$icon;
?>
