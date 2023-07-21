<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cedula'])) {
        $usr_id = $_POST['cedula'];
        // Token de autenticación
        $token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIwODA0MzIxMzcwIiwiZXhwIjoxNjkzMDg3NTcwfQ.-nw49HQwSP1FyMZbYf_r5S2eID4f10FKmGDfDyfChpE";

        // Realizar la eliminación del usuario
        $url = "http://20.163.192.189:8080/api/user/" . $usr_id;
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $token));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);
        if ($http_code == 200) {
            echo "true";
        } else {
            echo "Error al eliminar el usuario. Código de respuesta HTTP: " . $http_code;
        }
    } else {
        echo "No se proporcionó la cédula del usuario.";
    }    
} else {
    echo "Método no permitido. Se requiere una solicitud POST.";
}
?>