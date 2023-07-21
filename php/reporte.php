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

// Función para exportar a PDF
function exportarPDF($auditoriaData)
{
  // Recuperar la variable de sesión con los datos de auditoría
  if (!empty($auditoriaData)) {
    require("../library/mpdf/autoload.php");

    // Generar el contenido del reporte en HTML
    $contenido = '
    <!DOCTYPE html>
    <html>
    <head>
      <title>Reporte</title>
      <style>
        body {
          font-family: Arial, sans-serif;
          margin: 40px;
        }
        
        .encabezado {
          text-align: center;
          margin-bottom: 20px;
        }
        
        .encabezado img {
          width: 150px;
          height: auto;
          margin-bottom: 10px;
        }
        
        .titulo {
          margin: 0;
          font-size: 24px;
          font-weight: bold;
        }
        
        .subtitulo {
          margin: 5px 0;
          font-size: 18px;
        }
        
        .info {
          margin-bottom: 10px;
          font-size: 14px;
        }
        
        .tabla-auditoria {
          width: 100%;
          border-collapse: collapse;
          margin-top: 20px;
        }
        
        .tabla-auditoria th, .tabla-auditoria td {
          padding: 8px;
          border: 1px solid #ccc;
        }
        
        .tabla-auditoria th {
          background-color: #f2f2f2;
        }
        
        .tabla-auditoria tbody tr:nth-child(even) {
          background-color: #f9f9f9;
        }
        
        .tabla-auditoria tbody tr:hover {
          background-color: #e5e5e5;
        }
      </style>
    </head>
    <body>
      <div class="encabezado">
        <img src="https://upload.wikimedia.org/wikipedia/commons/6/60/Universidad_T%C3%A9cnica_del_Norte_Logo.jpg" height="120px">
        <h1 class="titulo">Reporte de Pista de Auditoría</h1>
        <p class="subtitulo">Módulos de Seguridad, Compras, Inventario, Cuentas por Cobrar y Facturación</p>
        <p class="info">Fecha: ' . date('d/m/Y') . '</p>
        <p class="info">Ciudad: Ibarra, Imbabura</p>
      </div>
      <table class="tabla-auditoria">
        <thead>
          <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Fecha</th>
            <th>Acción</th>
            <th>Módulo</th>
            <th>Funcionalidad</th>
            <th>Observación</th>
          </tr>
        </thead>
        <tbody>';

    // Agregar los datos de auditoría a la tabla
    foreach ($auditoriaData as $fila) {
      $contenido .= '
          <tr>
            <td>' . $fila['aud_id'] . '</td>
            <td>' . $fila['aud_usuario'] . '</td>
            <td>' . $fila['aud_fecha'] . '</td>
            <td>' . $fila['aud_accion'] . '</td>
            <td>' . $fila['aud_modulo'] . '</td>
            <td>' . $fila['aud_funcionalidad'] . '</td>
            <td>' . $fila['aud_observacion'] . '</td>
          </tr>';
    }

    $contenido .= '
        </tbody>
      </table>
    </body>
    </html>';

    // Crear un nuevo documento PDF
    $mpdf = new \Mpdf\Mpdf([
      'mode' => 'utf-8',
      'default_font_size' => 9,
      'orientation' => 'P',
      'margin_left' => 25,
      'margin_right' => 25,
      'margin_top' => 15,
      'margin_bottom' => 15,
    ]);

    // Agregar el contenido HTML al documento PDF
    $mpdf->WriteHTML($contenido);

    // Generar el nombre del archivo PDF
    $nombreArchivo = 'reporte_' . date('d-m-Y_H_i_s') . '.pdf';

    // Descargar el archivo PDF
    $mpdf->Output($nombreArchivo, 'D');
  } else {
    // Si no hay datos almacenados en la sesión, mostrar un mensaje de error o redirigir a una página de error
    die("Error: No se encontraron datos de auditoría para exportar al PDF.");
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['exportar-pdf'])) {
    // Exportar el PDF con los datos almacenados en la sesión
    if (!empty($_SESSION['auditoriaData'])) {
      exportarPDF($_SESSION['auditoriaData']);
    } else {
      die("Error: No se encontraron datos de auditoría para exportar al PDF.");
    }
  } else {
    $fechaInicio = $_POST['fecha-inicio'];
    $fechaFin = $_POST['fecha-fin'];

    if ($fechaInicio > $fechaFin) {
      $temp = $fechaInicio;
      $fechaInicio = $fechaFin;
      $fechaFin = $temp;
    }

    // Formatear las fechas
    $fechaInicioFormateada = date('Y-m-d', strtotime($fechaInicio));
    $fechaFinFormateada = date('Y-m-d', strtotime($fechaFin));

    $auditoriaData = array();

    if (isset($_POST['seguridad'])) {
      $token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIwODA0MzIxMzcwIiwiZXhwIjoxNjkzMDg3NTcwfQ.-nw49HQwSP1FyMZbYf_r5S2eID4f10FKmGDfDyfChpE";
      $urlSeguridad = "http://20.163.192.189:8080/api/reporte-pista-auditoria/?fecha_inicio=" . urlencode($fechaInicioFormateada) . "&fecha_fin=" . urlencode($fechaFinFormateada);

      $headers = array(
        "Authorization: Bearer $token",
        "Content-Type: application/json"
      );

      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, $urlSeguridad);
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

      $response = curl_exec($curl);
      curl_close($curl);

      $dataSeguridad = json_decode($response, true);
      if ($dataSeguridad && isset($dataSeguridad['tb_auditoria'])) {
        $auditoriaData = array_merge($auditoriaData, $dataSeguridad['tb_auditoria']);
      }
    }

    if (isset($_POST['cuentas'])) {
      // Fecha de inicio y fin para la consulta
      $fechaInicioFormateada = "2023-07-01";
      $fechaFinFormateada = "2023-07-21";

      // URL de la API con los parámetros de fecha
      $urlCuentas = "https://appsdistrivuidas.azurewebsites.net/api/auditoria?startDate=" . urlencode($fechaInicioFormateada) . "&endDate=" . urlencode($fechaFinFormateada);

      // Realizamos la solicitud GET a la API
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, $urlCuentas);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      $response = curl_exec($curl);
      curl_close($curl);

      if ($response) {
        $data = json_decode($response, true);

        // Agregar los datos del módulo "Cuentas por Cobrar" al arreglo $auditoriaData
        foreach ($data as $item) {
          if ($item['aud_modulo'] === "Cuenta Bancaria") {
            $auditoriaData[] = array(
              'aud_id' => $item['aud_id'],
              'aud_usuario' => "1004192371",
              'aud_fecha' => date('d/m/Y', strtotime($item['aud_fecha'])),
              'aud_accion' => $item['aud_accion'],
              'aud_modulo' => $item['aud_modulo'],
              'aud_funcionalidad' => $item['aud_funcionalidad'],
              'aud_observacion' => $item['aud_observacion']
            );
          }
        }
      }
    }

    if (isset($_POST['inventario'])) {

      $data = array(
        "fecha_inicio" => $fechaInicioFormateada,
        "fecha_fin" => $fechaFinFormateada
      );

      $urlInventario = "https://inventarioproductos.onrender.com/auditoriafecha";

      $tokenInventario = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VybmFtZSI6Ik1hdGVpdG8iLCJpYXQiOjE2ODk4MDkzMTMsImV4cCI6MTY4OTg5NTcxM30.7-kRKEczO3GL4ZHCpa3fFinCP3njItj3a3qTQ68dfe4"; // Reemplaza "tu_token_aqui" por tu token real

      $headers = array(
        "Authorization: $tokenInventario",
        "Content-Type: application/json"
      );

      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, $urlInventario);
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
      curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

      $responseInventario = curl_exec($curl);
      curl_close($curl);

      if ($responseInventario) {
        $dataInventario = json_decode($responseInventario, true);
        if (isset($dataInventario['auditorias'])) {
          foreach ($dataInventario['auditorias'] as $filaInventario) {
            $auditoriaData[] = array(
              'aud_id' => $filaInventario['aud_id'],
              'aud_usuario' => $filaInventario['usu_id'],
              'aud_fecha' => date('d/m/Y', strtotime($filaInventario['aud_fecha'])),
              'aud_accion' => $filaInventario['aud_accion'],
              'aud_modulo' => $filaInventario['aud_modulo'],
              'aud_funcionalidad' => $filaInventario['aud_funcionalidad'],
              'aud_observacion' => $filaInventario['aud_observacion']
            );
          }
        } else {
          echo "Respuesta de la API de Inventario Productos: " . $responseInventario;
        }
      } else {
        echo "Error al obtener datos de la API de Inventario Productos: " . $urlInventario;
      }
    }



    if (isset($_POST['compras'])) {
      $urlCompras = "https://gr2compras.000webhostapp.com/auditoriafecha/?fecha_inicio=" . urlencode($fechaInicioFormateada) . "&fecha_fin=" . urlencode($fechaFinFormateada);

      $response = file_get_contents($urlCompras);

      $dataCompras = json_decode($response, true);
      if ($dataCompras && isset($dataCompras['data'])) {
        foreach ($dataCompras['data'] as $filaCompras) {
          $auditoriaData[] = array(
            'aud_id' => $filaCompras['id'],
            'aud_usuario' => $filaCompras['aud_usuario'],
            'aud_fecha' => $filaCompras['aud_fecha'],
            'aud_accion' => $filaCompras['aud_accion'],
            'aud_modulo' => $filaCompras['aud_modulo'],
            'aud_funcionalidad' => $filaCompras['aud_funcionalidad'],
            'aud_observacion' => $filaCompras['aud_observacion']
          );
        }
      }
    }

    // Almacenar los datos de auditoría en la sesión
    $_SESSION['auditoriaData'] = $auditoriaData;
    $_SESSION['auditoria_formulario'] = $_POST;
  }
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Pista de Auditoría</title>
  <link rel="stylesheet" type="text/css" href="/css/reporte.css">
  <link rel="stylesheet" type="text/css" href="/css/cabezera_principal.css">
  <link rel="stylesheet" type="text/css" href="/css/menu_principal.css">

  <style>
    /* Estilos para el formulario y la tabla */
    body {
      font-family: Arial, sans-serif;
    }

    .formulario {
      max-width: 800px;
      margin: 5% auto;
    }

    .campo-fecha {
      display: flex;
      justify-content: space-between;
      margin-bottom: 20px;
    }

    .campo {
      flex: 1;
      margin-right: 10px;
    }

    .campo label {
      display: block;
      font-weight: bold;
      margin-bottom: 5px;
    }

    .campo input[type="date"] {
      padding: 6px;
      border: 1px solid #ccc;
      border-radius: 4px;
      width: 100%;
    }

    .checkbox-grupo {
      display: flex;
      flex-wrap: wrap;
      margin-bottom: 20px;
    }

    .checkbox-grupo label {
      display: block;
      margin-right: 10px;
      margin-bottom: 5px;
    }

    .checkbox-grupo input[type="checkbox"] {
      margin-right: 5px;
    }

    .boton {
      background-color: #4CAF50;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    .boton:hover {
      background-color: #45a049;
    }

    .tabla-contenedor {
      max-width: 800px;
      margin: 20px auto;
    }

    .tabla-auditoria {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      table-layout: fixed;
    }

    .tabla-auditoria th,
    .tabla-auditoria td {
      padding: 8px;
      border: 1px solid #ccc;
      text-align: center;
      overflow-wrap: break-word;
    }

    .tabla-auditoria th {
      background-color: #f2f2f2;
    }

    .tabla-auditoria tbody tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    .tabla-auditoria tbody tr:hover {
      background-color: #e5e5e5;
    }

    /* Estilos para la paginación */
    .pagination {
      display: flex;
      justify-content: center;
      margin-top: 20px;
    }

    .pagination button {
      background-color: #4CAF50;
      color: white;
      padding: 8px 12px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      margin: 0 4px;
    }

    .pagination button:hover {
      background-color: #45a049;
    }
  </style>
</head>

<body>
  <div class="cabezera">
    <strong>MODULO DE SEGURIDAD - AUDITORÍA</strong>
  </div>
  <header>
    <?php echo $_SESSION['menu_completo']; ?>
  </header>
  <div class="formulario">
    <form method="POST" action="reporte.php">
      <div class="campo-fecha">
        <div class="campo">
          <label for="fecha-inicio">Fecha de inicio:</label>
          <input type="date" id="fecha-inicio" name="fecha-inicio" value="<?php echo date('Y-m-d'); ?>" required>
        </div>
        <div class="campo">
          <label for="fecha-fin">Fecha de fin:</label>
          <input type="date" id="fecha-fin" name="fecha-fin" value="<?php echo date('Y-m-d'); ?>" required>
        </div>
      </div>

      <div class="checkbox-grupo">
        <label><strong>Módulos:</strong></label>
        <div class="columna">
          <label for="compras"><input type="checkbox" id="compras" name="compras"> Compras </label>
        </div>
        <div class="columna">
          <label for="cuentas"><input type="checkbox" id="cuentas" name="cuentas"> Cuentas por cobrar </label>
        </div>
        <div class="columna">
          <label for="facturacion"><input type="checkbox" id="facturacion" name="facturacion"> Facturación </label>
        </div>
        <div class="columna">
          <label for="inventario"><input type="checkbox" id="inventario" name="inventario"> Inventario </label>
        </div>
        <div class="columna">
          <label for="seguridad"><input type="checkbox" id="seguridad" name="seguridad"> Seguridad </label>
        </div>
      </div>

      <button class="boton" type="submit" name="generar">Generar</button>
    </form>

    <?php if (!empty($_SESSION['auditoriaData'])): ?>
      <h2>Tabla de Auditoría</h2>
      <div class="tabla-contenedor">
        <table class="tabla-auditoria" id="auditoria-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Usuario</th>
              <th>Fecha</th>
              <th>Acción</th>
              <th>Módulo</th>
              <th>Funcionalidad</th>
              <th>Observación</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $totalFilas = count($_SESSION['auditoriaData']);
            $filasPorPagina = 5;
            $totalPaginas = ceil($totalFilas / $filasPorPagina);
            $paginaActual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
            $inicio = ($paginaActual - 1) * $filasPorPagina;
            $fin = min($inicio + $filasPorPagina, $totalFilas);

            for ($i = $inicio; $i < $fin; $i++) {
              $fila = $_SESSION['auditoriaData'][$i];
              ?>
              <tr>
                <td>
                  <?php echo $fila['aud_id']; ?>
                </td>
                <td>
                  <?php echo $fila['aud_usuario']; ?>
                </td>
                <td>
                  <?php echo $fila['aud_fecha']; ?>
                </td>
                <td>
                  <?php echo $fila['aud_accion']; ?>
                </td>
                <td>
                  <?php echo $fila['aud_modulo']; ?>
                </td>
                <td>
                  <?php echo $fila['aud_funcionalidad']; ?>
                </td>
                <td>
                  <?php echo $fila['aud_observacion']; ?>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>

      <!-- Paginación -->
      <div class="pagination">
        <?php
        $maxEnlaces = 5; // Número máximo de enlaces a mostrar
        $enlaceMedio = ceil($maxEnlaces / 2);
        $inicioEnlaces = max(1, $paginaActual - $enlaceMedio + 1);
        $finEnlaces = min($inicioEnlaces + $maxEnlaces - 1, $totalPaginas);

        if ($paginaActual > 1) {
          echo '<a href="?pagina=1#auditoria-table"><button>Primera</button></a>';
          $paginaAnterior = $paginaActual - 1;
          echo '<a href="?pagina=' . $paginaAnterior . '#auditoria-table"><button>&laquo;</button></a>';
        }

        for ($i = $inicioEnlaces; $i <= $finEnlaces; $i++) {
          if ($i === $paginaActual) {
            echo '<button class="active">' . $i . '</button>';
          } else {
            echo '<a href="?pagina=' . $i . '#auditoria-table"><button>' . $i . '</button></a>';
          }
        }

        if ($paginaActual < $totalPaginas) {
          $paginaSiguiente = $paginaActual + 1;
          echo '<a href="?pagina=' . $paginaSiguiente . '#auditoria-table"><button>&raquo;</button></a>';
          echo '<a href="?pagina=' . $totalPaginas . '#auditoria-table"><button>Última</button></a>';
        }
        ?>
      </div>

      <form method="POST" action="reporte.php">
        <input type="hidden" name="fecha-inicio" value="<?php echo $_SESSION['auditoria_formulario']['fecha-inicio']; ?>">
        <input type="hidden" name="fecha-fin" value="<?php echo $_SESSION['auditoria_formulario']['fecha-fin']; ?>">
        <?php foreach ($_SESSION['auditoriaData'] as $fila): ?>
          <input type="hidden" name="auditoriaData[]" value="<?php echo htmlspecialchars(json_encode($fila)); ?>">
        <?php endforeach; ?>
        <button class="boton" type="submit" name="exportar-pdf">Exportar a PDF</button>
      </form>
    <?php endif; ?>
  </div>
</body>

</html>