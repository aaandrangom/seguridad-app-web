<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mod_id']) 
    and isset($_POST['mod_name']) 
    and isset($_POST['mod_admin']) 
    and isset($_POST['mod_state'])
    and isset($_POST['mod_id_current'])){
        $mod_id = $_POST['mod_id'];
        $mod_name = $_POST['mod_name'];
        $mod_admin = $_POST['mod_admin'];
        $mod_state = $_POST['mod_state'];
        $mod_id_current = $_POST['mod_id_current'];

        $updated_module = [
            'mod_id' => $mod_id,
            'mod_name' => $mod_name,
            'mod_admin' => $mod_admin,
            'mod_state' => $mod_state
        ];
        $query_params = http_build_query($updated_module);
        $url = "http://20.163.192.189:8080/api/module/".$mod_id_current."?".$query_params;
        $access_token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIwODA0MzIxMzcwIiwiZXhwIjoxNjk0OTI4ODIxfQ.zOJEUMiDj4vNHkv-EoCNazOp5Os6axj_-PGyByqQXsg"; // Reemplaza con tu token de acceso real
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
            $text = "Modulo actualizado con exito";
            $icon = "success";
        } else {
            $title = "Error al Actualizar";
            $text = "Error al guardar los cambios. Código de respuesta HTTP: ".$http_code;
            $icon = "error";
        }
    } else {
        $title = "Error al Actualizar";
        $text = "Modulo NO actualizado. No se han enviado los datos correctamente";
        $icon = "error";
    }
 
} else {
    $title = "Error al Actualizar";
    $text = "Método no permitido. Se esperaba una solicitud PUT.";
    $icon = "error";
}
echo $title."-".$text."-".$icon;
?>
