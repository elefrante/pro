<?php
// reportes.php
require_once "../APIAdmin/config.php"; // conexión a la BD

// -- Procesar publicación --
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'publicar') {
    $titulo = mysqli_real_escape_string($conn, trim($_POST['titulo'] ?? ''));
    $descripcion = mysqli_real_escape_string($conn, trim($_POST['descripcion'] ?? ''));
    $id_admin = intval($_POST['adminId'] ?? 0);
    $fecha = date('Y-m-d');

    $nombre_arch = null;
    if (!empty($_FILES['archivo']) && $_FILES['archivo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['archivo'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            $uploadsDir = __DIR__ . '/../APICooperativa/reportes/';
            if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0755, true);

            // Crear nombre único seguro
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $safeBase = preg_replace('/[^A-Za-z0-9_\-]/', '_', pathinfo($file['name'], PATHINFO_FILENAME));
            $nombre_arch = $safeBase . '_' . time() . ($ext ? '.' . $ext : '');
            $dest = $uploadsDir . $nombre_arch;

            if (!move_uploaded_file($file['tmp_name'], $dest)) {
                $errorMsg = "Error al guardar el archivo.";
            }
        } else {
            $errorMsg = "Error en la subida del archivo (code {$file['error']}).";
        }
    }

    // Insertar en BD (si no hay error)
    if (!isset($errorMsg)) {
        $sql = "INSERT INTO reporte (titulo, nombre_arch, fecha, descripcion, id_admin)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssi", $titulo, $nombre_arch, $fecha, $descripcion, $id_admin);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: reportes.php");
        exit;
    } else {
        $flash_error = $errorMsg;
    }
}

// -- Obtener historial de reportes (join admin para mostrar nombre) --
$query = "
    SELECT r.*, a.nombre_apellidos
    FROM reporte r
    LEFT JOIN administrativo a ON a.id_admin = r.id_admin
    ORDER BY r.fecha DESC, r.id_reporte DESC
";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>CRS - Administrador Reportes</title>
  <link rel="stylesheet" href="css/estilos.css">
  <style>
    /* Layout específico para reportes (respeta estilos.css) */
    .reportes-grid {
      display: grid;
      grid-template-columns: 1fr 360px;
      gap: 18px;
      max-width: 1200px;
      margin: 0 auto;
    }

    .panel-form {
      background:#121212;
      padding:20px;
      border-radius:12px;
      color:#fff;
      box-shadow:0 6px 18px rgba(0,0,0,0.45);
    }
    .panel-form h3 { margin-bottom:12px; color:#fff; }
    .form-row { margin-bottom:12px; display:flex; flex-direction:column; gap:6px; }
    .form-row label { color:#ddd; font-weight:600; }
    .form-row input[type="text"], .form-row textarea {
      padding:10px; border-radius:8px; border:1px solid #2b2b2b; background:#eee; color:#000;
      font-size:14px;
    }
    .form-row textarea { min-height:120px; resize:vertical; }

    .file-row { display:flex; align-items:center; gap:12px; margin-bottom:14px; }
    .file-btn {
      background:#2ebd4d; color:#fff; border:none; padding:8px 14px; border-radius:16px; cursor:pointer; font-weight:700;
      box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }
    .publicar-btn {
      background:transparent; color:#fff; border:2px solid #2ebd4d; padding:10px 22px; border-radius:20px; cursor:pointer;
      font-weight:700; margin-left:auto;
    }
    .file-name { color:#ddd; font-size:14px; }

    /* Panel historial */
    .panel-hist {
      background:#121212;
      padding:10px;
      border-radius:12px;
      color:#fff;
      box-shadow:0 6px 18px rgba(0,0,0,0.45);
      height: calc(100vh - 120px);
      overflow-y:auto;
    }
    .hist-title { background:#2a2a2a; padding:10px; border-radius:8px; font-weight:700; margin-bottom:10px; color:#fff; }
    .hist-item { padding:12px; border-radius:8px; background:#161616; margin-bottom:10px; color:#fff; display:flex; flex-direction:column; gap:6px; }
    .hist-item .meta { font-size:13px; color:#cfcfcf; }
    .hist-item a.download { color:#2ebd4d; text-decoration:none; font-weight:700; }

    /* Responsive */
    @media (max-width: 900px) {
      .reportes-grid { grid-template-columns: 1fr; }
      .panel-hist { height:auto; max-height:400px; }
    }
  </style>
</head>
<body>
  <header>
    <div class="header-content">
      <a class="logo" href="../LandingYLogin/landing.html">
        <img style="height: 60px; width: 60px;" src="../imgs/Logo1.png" alt="Logo">
        <h1 style="padding-left: 5px; color:#fff;">CRS</h1>
      </a>
      <h2 id="saludo" style="color:#fff">Cargando...</h2>
    </div>
  </header>

  <div class="admin-container">
    <aside class="sidebar">
      <h3 style="color:#fff">ADMINISTRACIÓN</h3>
      <ul>
        <li><a href="../admin/inicio.php">Inicio</a></li>
        <li><a href="../admin/solicitudes.html">Solicitudes</a></li>
        <li><a href="../admin/gestiónHoras.php">Gestión de Horas</a></li>
        <li><a href="../admin/comprobantes.php">Comprobantes</a></li>
        <li><a href="../admin/exoneraciones.php">Exoneraciones</a></li>
        <li class="active"><a href="../admin/reportes.php">Reportes</a></li>
      </ul>
      <a href="#" class="logout-bottom" onclick="cerrarSesion()">Cerrar sesión</a>
    </aside>

    <main class="main-content">
      <div class="reportes-grid">
        <!-- FORM -->
        <div class="panel-form">
          <h3>Nuevo Aviso/Reporte</h3>

          <?php if (!empty($flash_error)): ?>
            <div style="background:#5a1a1a;color:#fff;padding:8px;border-radius:6px;margin-bottom:10px;">
              <?php echo htmlspecialchars($flash_error); ?>
            </div>
          <?php endif; ?>

          <form id="formReporte" method="post" enctype="multipart/form-data">
            <input type="hidden" name="accion" value="publicar">
            <input type="hidden" name="adminId" id="adminIdInput" value="0">

            <div class="form-row">
              <label for="titulo">Titulo</label>
              <input id="titulo" name="titulo" type="text" placeholder="Titulo del aviso" required>
            </div>

            <div class="form-row">
              <label for="descripcion">Descripción:</label>
              <textarea id="descripcion" name="descripcion" placeholder="Escriba aquí la descripción..." required></textarea>
            </div>

            <div class="file-row">
              <label class="file-btn" for="archivo">Subir documento</label>
              <input id="archivo" name="archivo" type="file" style="display:none">
              <div class="file-name" id="fileName">Ningún archivo seleccionado</div>
            </div>

            <div style="display:flex; align-items:center; gap:12px;">
              <button type="submit" class="publicar-btn">Publicar</button>
            </div>
          </form>
        </div>

        <!-- HISTORIAL -->
        <aside class="panel-hist">
          <div class="hist-title">Historial</div>

          <?php while ($r = mysqli_fetch_assoc($result)): ?>
            <div class="hist-item">
              <div style="display:flex;justify-content:space-between;align-items:center">
                <div style="font-weight:700;"><?php echo htmlspecialchars($r['titulo']); ?></div>
                <div style="font-size:13px;color:#bdbdbd;"><?php echo date("d/m/Y", strtotime($r['fecha'])); ?></div>
              </div>

              <div class="meta">
                <?php echo htmlspecialchars($r['descripcion']); ?>
              </div>

              <div class="meta">
                Autor: <?php echo htmlspecialchars($r['nombre_apellidos'] ?? '---'); ?>
                <?php if (!empty($r['nombre_arch'])): ?>
                  &nbsp;•&nbsp; <a class="download" href="<?php echo '../APICooperativa/reportes/' . rawurlencode($r['nombre_arch']); ?>" target="_blank">Descargar</a>
                <?php endif; ?>
              </div>
            </div>
          <?php endwhile; ?>

        </aside>
      </div>
    </main>
  </div>

<script>
  // Cargar nombre admin en header (igual que en otras pantallas)
  const adminId = localStorage.getItem('adminId');
  if (!adminId) {
    document.getElementById('saludo').innerText = "No has iniciado sesión.";
    window.location.href = "login.html";
  } else {
    fetch(`../APIAdmin/api.php/administrativos/${adminId}`)
      .then(res => res.json())
      .then(admin => {
        document.getElementById('saludo').innerText = admin.nombre_apellidos ?? "Administrador";
      }).catch(()=> { document.getElementById('saludo').innerText = "Administrador"; });
    // Rellenar hidden adminId para el POST
    document.getElementById('adminIdInput').value = adminId;
  }

  // Mostrar nombre de archivo seleccionado
  const archivoInput = document.getElementById('archivo');
  const fileName = document.getElementById('fileName');
  archivoInput.addEventListener('change', () => {
    if (archivoInput.files.length > 0) fileName.innerText = archivoInput.files[0].name;
    else fileName.innerText = 'Ningún archivo seleccionado';
  });


  function cerrarSesion() {
  // Borrar sesión del admin
  localStorage.removeItem('adminId');

  // Redirigir al login
  window.location.href = "login.html";
}
</script>
</body>
</html>
