<?php
// inicio.php
require_once "../APIUsuarios/config.php"; // conexión a la BD usuarios

// Traer solo usuarios aceptados = 1
$query = "SELECT cedula, nombre, apellido, telefono, email, unidad_habitacional, perfil, fecha_nacimiento
          FROM usuario
          WHERE aceptado = 1
          ORDER BY apellido ASC, nombre ASC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>CRS - Administrador Inicio</title>
  <link rel="stylesheet" href="css/estilos.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    /* Pequeños ajustes locales para legibilidad dentro de la lista */
    .socios-list { display:flex; flex-direction:column; gap:14px; margin-top:18px; background-color: black; padding: 15px; border-radius: 5px;}
    .socio-card{
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap:12px;
      background:#141414;
      color:#fff;              /* texto blanco aquí */
      padding:14px;
      border-radius:12px;
      box-shadow:0 6px 14px rgba(0,0,0,0.5);
    }
    .socio-left { display:flex; gap:12px; align-items:center; }
    .socio-left img{ width:64px; height:64px; border-radius:50%; object-fit:cover; background:#333; }
    .socio-meta p { margin:0; color:#fff; }
    .socio-meta .nombre { font-weight:700; font-size:1.05rem; }
    .socio-actions { display:flex; gap:8px; align-items:center; }
    .btn-datos, .btn-guardar, .btn-baja {
      padding:8px 12px; border-radius:8px; border:none; cursor:pointer; font-weight:600;
    }
    .btn-datos { background:#2f6bfd; color:#fff; }
    .btn-guardar { background:#28a745; color:#fff; }
    .btn-baja { background:#e04b4b; color:#fff; }

    .detalles {
      display:none;
      background:#1b1b1b;
      margin-top:10px;
      padding:12px;
      border-radius:8px;
      color:#fff;
    }
    .det-row { display:flex; gap:12px; align-items:center; margin-bottom:8px; flex-wrap:wrap; }
    .det-row label { min-width:140px; color:#cfcfcf; font-size:0.9rem; }
    .det-row .value { color:#fff; font-weight:600; }
    .det-row input[type="text"], .det-row input[type="email"], .det-row input[type="date"] {
      background:#222; border:1px solid #333; padding:8px; border-radius:6px; color:#fff; min-width:220px;
    }

    /* Responsivo */
    @media (max-width:850px){
      .socio-card { flex-direction:column; align-items:stretch; }
      .socio-actions { justify-content:flex-end; }
      .det-row label { min-width:120px; }
      .det-row input { min-width:160px; }
    }

  </style>
</head>
<body class="body-dark">
  <header>
    <div class="header-content">
      <a class="logo" href="../LandingYLogin/landing.html">
        <img style="height:60px;width:60px;" src="../imgs/Logo1.png" alt="Logo">
        <h1 style="padding-left:6px;color:#fff;">CRS</h1>
      </a>
      <h2 id="adminNombre" style="color:#fff">Cargando...</h2>
    </div>
  </header>

  <div class="admin-container">
    <aside class="sidebar">
      <h3 style="color:#fff">ADMINISTRACIÓN</h3>
      <ul>
        <li class="active"><a href="inicio.php">Inicio</a></li>
        <li><a href="solicitudes.html">Solicitudes</a></li>
        <li><a href="gestiónHoras.php">Gestión de Horas</a></li>
        <li><a href="comprobantes.php">Comprobantes</a></li>
        <li><a href="exoneraciones.php">Exoneraciones</a></li>
        <li><a href="reportes.php">Reportes</a></li>
      </ul>
      <a href="#" class="logout-bottom" onclick="cerrarSesion()">Cerrar sesión</a>
    </aside>

    <main class="main-content">
      <h1 id="saludo" style="color:black; font-size:28px;">Cargando...</h1>

      <section class="socios-list">
        <h2 style="color: #fff;">Informacion Socios</h2>
        <?php while ($row = mysqli_fetch_assoc($result)): 
            // preparar datos
            $ced = htmlspecialchars($row['cedula']);
            $nombreCompleto = htmlspecialchars($row['nombre'] . ' ' . $row['apellido']);
            $perfil = $row['perfil'] ? htmlspecialchars($row['perfil']) : 'default.jpg';
            $telefono = htmlspecialchars($row['telefono']);
            $email = htmlspecialchars($row['email']);
            $unidad = htmlspecialchars($row['unidad_habitacional']);
            $fecha_nac = $row['fecha_nacimiento'] ? date("d/m/Y", strtotime($row['fecha_nacimiento'])) : '';
        ?>
        <div>
          <div class="socio-card" id="card-<?php echo $ced; ?>">
            <div class="socio-left">
              <img src="<?php echo "../APIUsuarios/$perfil"; ?>" alt="perfil-<?php echo $ced; ?>">
              <div class="socio-meta">
                <p class="nombre"><?php echo $nombreCompleto; ?></p>
                <p><?php echo $email; ?></p>
              </div>
            </div>

            <div class="socio-actions">
              <button class="btn-datos" onclick="toggleDetalles('<?php echo $ced; ?>')">Datos</button>
            </div>
          </div>

          <!-- detalles desplegables -->
          <div class="detalles" id="det-<?php echo $ced; ?>">
            <div class="det-row">
              <label>Nombre completo:</label>
              <div class="value"><?php echo $nombreCompleto; ?></div>
            </div>

            <div class="det-row">
              <label>Cédula:</label>
              <div class="value"><?php echo $ced; ?></div>
            </div>

            <div class="det-row">
              <label>Fecha de nacimiento:</label>
              <div class="value"><?php echo $fecha_nac; ?></div>
            </div>

            <div class="det-row">
              <label for="tel-<?php echo $ced; ?>">Teléfono:</label>
              <input id="tel-<?php echo $ced; ?>" type="text" value="<?php echo $telefono; ?>">
            </div>

            <div class="det-row">
              <label for="mail-<?php echo $ced; ?>">Correo:</label>
              <input id="mail-<?php echo $ced; ?>" type="email" value="<?php echo $email; ?>">
            </div>

            <div class="det-row">
              <label for="uni-<?php echo $ced; ?>">Unidad habitacional:</label>
              <input id="uni-<?php echo $ced; ?>" type="text" value="<?php echo $unidad; ?>">
            </div>

            <div style="margin-top:10px; display:flex; gap:8px;">
              <button class="btn-guardar" onclick="guardar('<?php echo $ced; ?>')">Guardar cambios</button>
              <button class="btn-baja" onclick="darDeBaja('<?php echo $ced; ?>')">Dar de baja</button>
            </div>
          </div>
        </div>
        <?php endwhile; ?>
      </section>

    </main>
  </div>

<script>
  // Cargar nombre del admin en header y saludo (igual que estabas haciendo)
  const adminId = localStorage.getItem('adminId');
  if (!adminId) {
    document.getElementById('saludo').innerText = "No has iniciado sesión.";
    document.getElementById('adminNombre').innerText = "No has iniciado sesión.";
    window.location.href = "login.html";
  } else {
    fetch(`../APIAdmin/api.php/administrativos/${adminId}`)
      .then(res => res.json())
      .then(admin => {
        if (admin && admin.nombre_apellidos) {
          document.getElementById('adminNombre').innerText = admin.nombre_apellidos;
          document.getElementById('saludo').innerHTML = `Bienvenido, <strong>${admin.nombre_apellidos}</strong>.`;
        } else {
          document.getElementById('saludo').innerText = "Error al cargar datos.";
          document.getElementById('adminNombre').innerText = "Error";
        }
      })
      .catch(() => {
        document.getElementById('saludo').innerText = "Error al conectar con la API.";
        document.getElementById('adminNombre').innerText = "Error";
      });
  }

  // Toggle detalles
  function toggleDetalles(cedula) {
    const el = document.getElementById('det-' + cedula);
    if (!el) return;
    el.style.display = (el.style.display === 'block') ? 'none' : 'block';
  }

  // Guardar cambios -> PUT a la API de usuarios
  async function guardar(cedula) {
    const telefono = document.getElementById('tel-' + cedula).value.trim();
    const email = document.getElementById('mail-' + cedula).value.trim();
    const unidad = document.getElementById('uni-' + cedula).value.trim();

    try {
      const resp = await fetch(`../APIUsuarios/api.php/usuarios/${encodeURIComponent(cedula)}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          telefono: telefono,
          email: email,
          unidad_habitacional: unidad
        })
      });

      if (!resp.ok) {
        const txt = await resp.text();
        Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo guardar: ' + txt });
        return;
      }

      Swal.fire({ icon: 'success', title: 'Guardado', text: 'Cambios guardados correctamente.' });
    } catch (err) {
      Swal.fire({ icon: 'error', title: 'Error', text: 'Error de conexión al guardar.' });
      console.error(err);
    }
  }

  // Dar de baja -> DELETE a la API de usuarios
  function darDeBaja(cedula) {
    Swal.fire({
      title: '¿Eliminar socio?',
      text: 'Se eliminará el socio de la base de datos.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then(async (res) => {
      if (!res.isConfirmed) return;
      try {
        const resp = await fetch(`../APIUsuarios/api.php/usuarios/${encodeURIComponent(cedula)}`, {
          method: 'DELETE'
        });
        if (!resp.ok) {
          const txt = await resp.text();
          Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo eliminar: ' + txt });
          return;
        }
        Swal.fire({ icon: 'success', title: 'Eliminado', text: 'Socio eliminado.' }).then(() => location.reload());
      } catch (err) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Error de conexión.' });
      }
    });
  }

  function cerrarSesion() {
  // Borrar sesión del admin
  localStorage.removeItem('adminId');

  // Redirigir al login
  window.location.href = "login.html";
}
</script>
</body>
</html>
