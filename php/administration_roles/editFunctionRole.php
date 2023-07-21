<?php 
    $token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIwODA0MzIxMzcwIiwiZXhwIjoxNjkzMDg3NTcwfQ.-nw49HQwSP1FyMZbYf_r5S2eID4f10FKmGDfDyfChpE";
    $registro = "";
    $funciones = "";
    $tabla_completa = "<center>
    <table class='tablaDetalles' width='95%'>
      <tr class='columCabezera'>
        <td class='columOne colum'><strong>Funcion</strong></td>
        <td class='columOne colum'><strong></strong></td>
      </tr>";
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST['role_func_id'])) {
            $role_func_id = $_POST["role_func_id"];
            $rol_id = $_POST["role_id"];

            $url = "http://20.163.192.189:8080/api/role_function/".$role_func_id;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $token));
     
            $response = curl_exec($ch);
            $http_code_in = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            if ($http_code_in == 200) {
            $function_rol = json_decode($response, true);
            $url = "http://20.163.192.189:8080/api/role_function/role/".$rol_id;
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $token));

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);
            $registro = "";
            if ($http_code == 200) {
                $funtions_role = json_decode($response, true);
                foreach ($funtions_role['tb_role_function'] as $function) {
                $url = "http://20.163.192.189:8080/api/function/".$function['rol_func_function'];
                $ch = curl_init($url);

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $token));

                $response = curl_exec($ch);
                $http_code_in = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                curl_close($ch);
                $funciones = $funciones."j_y".$function['rol_func_function'];
                if ($http_code_in == 200) {
                    $function_role = json_decode($response, true);
                    $registro = $registro."
                    <tr class='rowOne'>
                        <td class='columOne'><center>".$function_role['func_name']."</center></td>
                        <td class='columOne'>
                        <center>
                            <button class='btnNormal btnDelete'type='button' onclick='enableFunction(".$function['rol_func_id'].", ".$rol_id.")'>✕</button>
                        </center>
                        </td>
                    </tr>
                    ";
                }
                }
            } else {
                $registro = "
                <tr>
                    <td colspan='2'>
                        El rol no cuenta con funciones asignadas
                    </td>
                </tr>";
            }
        } else {
            $registro = "
            <tr>
                <td colspan='2'>
                    Por favor proporcione el ID de la funcion que desea eliminar
                </td>
            </tr>";
        }
    }
} else {
    $registro = "
    <tr>
        <td colspan='2'>
            No intente acceder con otro metodo que no sea POST. !Loquitop¡
        </td>
    </tr>";
}
$tabla_completa = $tabla_completa.$registro."
    </table>
    </center>".$funciones;
echo $tabla_completa;
?>