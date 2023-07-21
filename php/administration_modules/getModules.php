<?php
$token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIwODA0MzIxMzcwIiwiZXhwIjoxNjkzMDg3NTcwfQ.-nw49HQwSP1FyMZbYf_r5S2eID4f10FKmGDfDyfChpE";
$url = "http://20.163.192.189:8080/api/module";
$headers = array(
  "Authorization: Bearer $token",
  "Content-Type: application/json"
);

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($curl);
$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);
$registro = "";
if ($http_code == 200) {
    $modules = json_decode($response, true);
    foreach ($modules['tb_module'] as $module) {    
        $registro = $registro."<tr class='rowOne'>
            <td class='columOne'>
                ".$module['mod_name']."
            </td>
            <td class='columOne'>
                <div class='btnGroup'>
                    <button type='button' class='btnNormal btnInfo' onclick='abriModal(".$module['mod_id'].")'>Informacion</button>
                    <form method='POST' action='editInfoModule.php'>
                        <input type='hidden' name='mod_id' value=".$module['mod_id'].">
                        <button type='submit' class='btnNormal btnEdit'>Editar</button>
                    </form>
                    <button type='button' class='btnNormal btnDelete' onclick='abriModalConfirmacion(".$module['mod_id'].")'>Eliminiar</button>
                </div>
            </td>
        </tr>";
    }
  } else {
    $registro = "<tr class='rowOne'>
    <td class='columOne'colspan='2'>
        No hay modulos
    </td>";
  }

echo $registro;
?>