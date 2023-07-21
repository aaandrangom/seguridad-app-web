<?php 
    $token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIwODA0MzIxMzcwIiwiZXhwIjoxNjkzMDg3NTcwfQ.-nw49HQwSP1FyMZbYf_r5S2eID4f10FKmGDfDyfChpE";
    $registro = "";
    $registroTwo = "";
    $funciones = "";
    $tabla_completa = "<center>
    <table class='tablaDetalles' width='95%'>
      <tr class='columCabezera'>
        <td class='columOne colum'><strong>Funcion</strong></td>
        <td class='columOne colum'><strong></strong></td>
      </tr>";
    $tabla_completa_funciones = "<center>
    <table class='tablaDetalles' width='95%'>
      <tr class='columCabezera'>
        <td class='columOne colum'><strong>Secuencial</strong></td>
        <td class='columOne colum'><strong>Funcion</strong></td>
        <td class='columOne colum'><strong></strong></td>
      </tr>";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST['function_id']) and isset($_POST['role_id'])) {
            $function_id = $_POST["function_id"]; 
            $role_id = $_POST["role_id"];

            $data = array(
                "rol_func_role" => $role_id,
                "rol_func_function" => $function_id,
                "rol_func_state" => "A"
            );
            $json_data = json_encode($data); 

            $headers = array(
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json'
            );

            $url = "http://20.163.192.189:8080/api/role_function";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);
            $http_code_in = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            if ($http_code_in == 200) {
                $function_rol = json_decode($response, true);
                $url = "http://20.163.192.189:8080/api/role_function/role/".$role_id;
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
                                <button class='btnNormal btnDelete'type='button' onclick='enableFunction(".$function['rol_func_id'].", ".$role_id.")'>✕</button>
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

                $registroTwo = "";
                $url = "http://20.163.192.189:8080/api/function";
                $ch = curl_init($url);

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $token));

                $response = curl_exec($ch);
                $http_code_in = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                curl_close($ch);

                if ($http_code_in == 200) {
                    $functions = json_decode($response, true);
                    foreach ($functions['tb_function'] as $function) {
                    $registroTwo = $registroTwo . "
                    <tr class='rowOne'>
                        <td class='columOne'>".$function['func_id']."</td>
                        <td class='columOne'>".$function['func_name']."</td>
                        <td class='columOne'>
                        <button class='btnNormal btnEdit'type='button' onclick='AddFunction(".$function['func_id'].", ".$role_id.")'>✓</button>
                        </td>
                    </tr>
                    ";
                    }
                } else {
                    $registroTwo = "
                    <tr>
                        <td colspan='3'>
                            No existen funciones
                        </td>
                    </tr>";
                }
            } else {
                $registro = "
                <tr>
                    <td colspan='2'>
                        No se pudo añadir la funcion 
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
        $registroTwo = "
        <tr>
            <td colspan='3'>
                No intente acceder con otro metodo que no sea POST. !Loquitop¡
            </td>
        </tr>";
    }
$tabla_completa = $tabla_completa.$registro."
    </table>
    </center>";
    
$tabla_completa_funciones = $tabla_completa_funciones.$registroTwo."
</table>
</center>";

echo $tabla_completa."j_y".$tabla_completa_funciones.$funciones;
?>