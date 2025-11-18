<?php
require_once "../APIAdmin/config.php"; // conexión a la base de datos

$query = "
    SELECT 
        h.fecha, h.solicitud, h.cantidad_hs, h.motivo, h.estado, h.cedula,
        u.nombre AS nombre_user, u.apellido AS apellido_user,
        a.nombre_apellidos AS admin_nombre
    FROM horas_semanales h
    JOIN usuario u ON u.cedula = h.cedula
    LEFT JOIN exonera e ON e.cedula = h.cedula AND e.fecha = h.fecha
    LEFT JOIN administrativo a ON a.id_admin = e.id_admin
    WHERE h.solicitud = 1
    ORDER BY h.fecha DESC
";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRS - Administrador Exoneraciones</title>
    <link rel="stylesheet" href="css/estilos.css">
    <script src="../Front/script.js" defer></script>
</head>
<body class="body-dark">
<header>
    <div class="header-content">
        <a class="logo" href="../LandingYLogin/landing.html">
            <img style="height:60px;width:60px;" src="../imgs/Logo1.png" alt="Logo">
            <h1 style="padding-left:5px;">CRS</h1>
        </a>
        <h2 id="saludo">Cargando...</h2>
    </div>
</header>

<div class="admin-container">
<aside class="sidebar">
    <h3>ADMINISTRACIÓN</h3>
    <ul>
        <li><a href="../admin/inicio.php">Inicio</a></li>
        <li><a href="../admin/solicitudes.html">Solicitudes</a></li>
        <li><a href="../admin/gestiónHoras.php">Gestión de Horas</a></li>
        <li><a href="../admin/comprobantes.php">Comprobantes</a></li>
        <li class="active"><a href="../admin/exoneraciones.php">Exoneraciones</a></li>
        <li><a href="../admin/reportes.php">Reportes</a></li>
    </ul>
    <a href="#" class="logout-bottom" onclick="cerrarSesion()">Cerrar sesión</a>
</aside>

<main class="main-content">
    <h1>Exoneraciones</h1>
    <section class="exoneraciones-section">
        <?php
            function getSemanaRango($fechaStr) {
                $fecha = new DateTime($fechaStr);
                $dia = $fecha->format('N'); // 1 (Lunes) a 7 (Domingo)
                $inicio = clone $fecha;
                $inicio->modify('-' . ($dia - 1) . ' days');
                $fin = clone $inicio;
                $fin->modify('+6 days');
                return ['inicio' => $inicio, 'fin' => $fin];
            }
        ?>

        <?php
        $primeraSemana = null;
        while ($row = mysqli_fetch_assoc($result)):
            $rango = getSemanaRango($row['fecha']);

            // Calcular número de semana relativo (misma lógica que tenías)
            $semanaActual = $rango['inicio']->format('W');
            $anioActual = $rango['inicio']->format('Y');
            if (!$primeraSemana) {
                $primeraSemana = ['num' => $semanaActual, 'anio' => $anioActual];
            }
            $diffSemanas = ((int)$anioActual - (int)$primeraSemana['anio']) * 52 + ((int)$semanaActual - (int)$primeraSemana['num']);
            $numeroSemanaRelativa = $diffSemanas + 2;
        ?>
        <article class="exoneracion-card">
            <h2>
                <?php echo htmlspecialchars($row['nombre_user'] . " " . $row['apellido_user']); ?> — 
                Semana <?php echo $numeroSemanaRelativa; ?> 
                (<?php echo $rango['inicio']->format('d/m/Y'); ?> - <?php echo $rango['fin']->format('d/m/Y'); ?>)
            </h2>

            <?php
            $motivo = htmlspecialchars($row['motivo']);
            if (mb_strlen($motivo) > 10) {
                $motivoCorto = mb_substr($motivo, 0, 10) . '...';
                echo '<p>Motivo de exoneración: ' . $motivoCorto . 
                     ' <button class="btn ver-mas" type="button" onclick="openModal(\'' . htmlspecialchars(addslashes($row['motivo'])) . '\')">Ver más</button></p>';
            } else {
                echo '<p>Motivo de exoneración: ' . $motivo . '</p>';
            }
            ?>

            <?php if ($row['estado'] === 'En proceso'): ?>
                <form method="post" action="procesar_exoneracion.php" class="form-acciones" onsubmit="return ensureAdminId(this);">
                    <input type="hidden" name="cedula" value="<?php echo $row['cedula']; ?>">
                    <input type="hidden" name="fecha" value="<?php echo $row['fecha']; ?>">
                    <!-- hidden adminId that JS will fill -->
                    <input type="hidden" name="adminId" class="adminIdSend" value="">
                    <button type="submit" name="accion" value="Exonerado" class="btn aceptar">Concedido</button>
                    <button type="submit" name="accion" value="Denegado" class="btn rechazar">Denegado</button>
                </form>
            <?php else: ?>
                <p class="estado-texto">
                    Estado: 
                    <span class="estado-<?php echo strtolower($row['estado']); ?>">
                        <?php echo htmlspecialchars(strtolower($row['estado'])); ?>
                    </span><br>
                    <span class="gestor">Gestionado por 
                        <?php
                        if (!empty($row['admin_nombre'])) {
                            echo htmlspecialchars($row['admin_nombre']);
                        } else {
                            echo "<em>Pendiente de administrador</em>";
                        }
                        ?>
                    </span>
                </p>
            <?php endif; ?>
        </article>
        <?php endwhile; ?>

    </section>

    <!-- Modal -->
    <div id="modalDesc" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <p id="modalText"></p>
        </div>
    </div>
</main>
</div>

<script>
// Modal helpers
function openModal(desc) {
    document.getElementById("modalText").innerText = desc;
    document.getElementById("modalDesc").style.display = "block";
}
function closeModal() {
    document.getElementById("modalDesc").style.display = "none";
}
window.onclick = function(event) {
    if (event.target == document.getElementById("modalDesc")) closeModal();
}

// Cargar nombre del admin en header (igual que tenías)
const adminId = localStorage.getItem('adminId');
if (!adminId) {
    document.getElementById('saludo').innerText = "No has iniciado sesión.";
    window.location.href = "login.html";
} else {
    fetch(`../APIAdmin/api.php/administrativos/${adminId}`)
        .then(res => res.json())
        .then(admin => {
            document.getElementById('saludo').innerText = admin.nombre_apellidos ?? "Error al cargar.";
        })
        .catch(() => {
            document.getElementById('saludo').innerText = "Error de conexión.";
        });
}

// Inyectar adminId en todos los inputs hidden
document.addEventListener("DOMContentLoaded", () => {
    const aid = localStorage.getItem('adminId') ?? "";
    document.querySelectorAll(".adminIdSend").forEach(input => input.value = aid);
});

// prevención: si no hay adminId no permitir enviar formulario (retorna false)
function ensureAdminId(formEl) {
    const adminIdVal = formEl.querySelector(".adminIdSend")?.value;
    if (!adminIdVal) {
        alert("No se detectó un administrador activo. Iniciá sesión para gestionar solicitudes.");
        return false;
    }
    return true;
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
