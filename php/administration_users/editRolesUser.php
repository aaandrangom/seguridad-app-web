<?php 
    $token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIwODA0MzIxMzcwIiwiZXhwIjoxNjkzMDg3NTcwfQ.-nw49HQwSP1FyMZbYf_r5S2eID4f10FKmGDfDyfChpE";
    $registro = "";
    $roles = "";
    $tabla_completa = "<center>
    <table class='tablaDetalles' width='95%'>
        <tr class='columCabezera'>
        <td class='columOne colum'><strong>Rol</strong></td>
        <td class='columOne colum'><strong>Descripcion</strong></td>
        <td class='columOne colum'><strong></strong></td>
        </tr>";
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST['role_user_id'])) {
            $role_user_id = $_POST["role_user_id"];
            $user_cedula = $_POST["user_cedula"];

            $url = "http://20.163.192.189:8080/api/role_user/".$role_user_id;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $token));
     
            $response = curl_exec($ch);
            $http_code_in = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            if ($http_code_in == 200) {
            $rol_user = json_decode($response, true);
            $url = "http://20.163.192.189:8080/api/role_user/user/".$user_cedula;
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $token));

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);
            if ($http_code == 200) {
                $roles_user = json_decode($response, true);
                foreach ($roles_user['tb_role_user_user'] as $rol){
                    $url = "http://20.163.192.189:8080/api/role/".$rol['rol_usr_role'];
                    $ch = curl_init($url);

                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $token));

                    $response = curl_exec($ch);
                    $http_code_in = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                    curl_close($ch);
                    $roles = $roles."j_y".$rol['rol_usr_role'];
                    if ($http_code_in == 200) {
                        $rol_user = json_decode($response, true);
                        $registro = $registro."
                        <tr class='rowOne'>
                        <td class='columOne'>".$rol_user['rol_role']."</td>
                        <td class='columOne'>".$rol_user['rol_description']."</td>
                        <td class='columOne'>
                            <button class='btnNormal btnDelete'type='button' onclick='enableRole(".$rol['rol_usr_id'].", ".  '"'.$rol['rol_usr_user'].'"'  .")'>✕</button>
                        </td>
                        </tr>
                    ";
                    } else {
                        $registro = "
                        <tr>
                            <td colspan='3'>
                                No se pudo extraer la informacion del rol nro: ".$rol['rol_usr_role']."
                            </td>
                        </tr>";
                    }
                }
            } else {
                $registro = "
                <tr>
                    <td colspan='3'>
                        El usuario no cuenta con roles asignados
                    </td>
                </tr>";
            }
        } else {
            $registro = "
            <tr>
                <td colspan='3'>
                    Por favor proporcione el ID del rol que desea eliminar
                </td>
            </tr>";
        }
    }
} else {
    $registro = "
    <tr>
        <td colspan='3'>
            No intente acceder con otro metodo que no sea POST. !Loquitop¡
        </td>
    </tr>";
}
$tabla_completa = $tabla_completa.$registro."
    </table>
    </center>".$roles;
echo $tabla_completa;
?>