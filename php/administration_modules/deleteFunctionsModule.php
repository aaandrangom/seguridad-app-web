<?php
$registro = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['function_module_id'])) {
        $function_id = $_POST['function_module_id'];
        $mod_id = $_POST['mod_id'];
        // Token de autenticación
        $token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIwODA0MzIxMzcwIiwiZXhwIjoxNjkzMDg3NTcwfQ.-nw49HQwSP1FyMZbYf_r5S2eID4f10FKmGDfDyfChpE";

        // Realizar la eliminación del usuario
        $url = "http://20.163.192.189:8080/api/function/" . $function_id ;
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $token));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);
        if ($http_code == 200) {
            $url = "http://20.163.192.189:8080/api/function_mod/".$mod_id;
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $token));

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($http_code == 200) {
                $functions_module = json_decode($response, true);
                foreach ($functions_module['tb_function'] as $function) {
                    $registro = $registro."
                    <tr class='rowOne'>
                        <td class='columOne'><center>".$function['func_name']."</center></td>
                        <td class='columOne'>
                        <center>
                            <button class='btnNormal btnDelete'type='button' onclick='enableFunction(".$function['func_id'].")'>✕</button>
                        </center>
                        </td>
                     </tr>
                    ";
                }
            } else {
                $registro = "
                <tr class='rowOne'>
                    <td class='columOne' colspan='2'>No se pudo extraer las funciones actualizadas de modulo.</td>
                </tr>";
            }
        } else {
            $registro = "
            <tr class='rowOne'>
                <td class='columOne' colspan='2'>Error al eliminar la funcion. Código de respuesta HTTP: ".$http_code."</td>
            </tr>";
        }
    } else {
        $registro = "
        <tr class='rowOne'>
            <td class='columOne' colspan='2'>No se proporcionó el ID de la funcion.</td>
        </tr>";
    }    
} else {
    $registro = "
    <tr class='rowOne'>
        <td class='columOne' colspan='2'>Método no permitido. Se requiere una solicitud POST.</td>
    </tr>";
}
echo $registro;
?>