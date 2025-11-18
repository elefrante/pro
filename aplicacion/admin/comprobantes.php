<?php
require_once "../APIAdmin/config.php"; // conexión DB

$query = "
    SELECT c.id_comprobante, c.nombre_comprobante, c.fecha_optativa, c.tipo, c.descripcion, c.estado, c.fecha, c.cedula,
           u.nombre, u.apellido,
           a.nombre_apellidos AS gestionado_por
    FROM comprobante c
    JOIN usuario u ON u.cedula = c.cedula
    LEFT JOIN gestiona g ON g.id_comprobante = c.id_comprobante
    LEFT JOIN administrativo a ON a.id_admin = g.id_admin
    ORDER BY c.fecha DESC
";
$result = mysqli_query($conn, $query);

function formatearPago($tipo, $fecha_optativa) {
    $tipo = strtolower(trim($tipo));
    $fecha = strtotime($fecha_optativa);

    // Mapeo manual de meses en español
    $meses = [
        "January" => "Enero",
        "February" => "Febrero",
        "March" => "Marzo",
        "April" => "Abril",
        "May" => "Mayo",
        "June" => "Junio",
        "July" => "Julio",
        "August" => "Agosto",
        "September" => "Septiembre",
        "October" => "Octubre",
        "November" => "Noviembre",
        "December" => "Diciembre"
    ];

    if ($tipo === "mensual") {
        $mesIngles = date("F", $fecha);   // Ej: "March"
        $mesEsp = $meses[$mesIngles];     // Ej: "Marzo"
        $anio = date("Y", $fecha);

        return "Pago mensual: $mesEsp $anio";
    }

    if ($tipo === "inicial") {
        return "Pago Inicial";
    }

    if ($tipo === "compensatorio") {
        $inicioSemana = date("d/m/Y", strtotime("monday this week", $fecha));
        $finSemana = date("d/m/Y", strtotime("sunday this week", $fecha));

        return "Pago semanal: Semana ($inicioSemana - $finSemana)";
    }

    return $tipo;
}



?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRS - Administrador Comprobantes</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Gabarito:wght@400..900&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <script src="../Front/script.js" defer></script>
    <style>
        body { background:#1e1e1e; color:white;}
        article { background:#2a2a2a; padding:15px; border-radius:10px; margin:15px 0; }
        h2 { margin:0; }
        .btn { padding:8px 15px; border:none; border-radius:20px; cursor:pointer; margin-right:10px; }
        .btn-aceptar { background:#1db954; color:white; }
        .btn-invalido { background:#ff4d4d; color:white; }
        .btn-ver { background:#4caf50; color:white; margin-top:10px; display:inline-block; }
        .estado { font-size:14px; color:#ccc; margin-top:10px; }
        .modal { display:none; position:fixed; z-index:999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.7); }
        .modal-content { background:#2a2a2a; padding:20px; border-radius:10px; margin:10% auto; width:50%; }
        .close { color:#fff; float:right; font-size:24px; cursor:pointer; }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <a class="logo" href="../LandingYLogin/landing.html">
                <img style="height: 60px; width: 60px;" src="../imgs/Logo1.png" alt="Logo de la cooperativa" />
                <h1 style="padding-left: 5px;">CRS</h1>
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
            <li class="active"><a href="../admin/comprobantes.php">Comprobantes</a></li>
            <li><a href="../admin/exoneraciones.php">Exoneraciones</a></li>
            <li><a href="../admin/reportes.php">Reportes</a></li>
        </ul>
        <a href="#" class="logout-bottom" onclick="cerrarSesion()">Cerrar sesión</a>
    </aside>
    <main class="main-content">
    <h1 style="color: #1a1a1a;">Lista de Comprobantes</h1>
    <section>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <article>
                <h2><?php echo htmlspecialchars($row['nombre'] . " " . $row['apellido']); ?></h2>
                <p><strong><?php echo formatearPago($row['tipo'], $row['fecha_optativa']); ?></strong></p>
                <p><strong>Fecha:</strong> <?php echo date("d/m/Y", strtotime($row['fecha'])); ?></p>

                <?php
                    $desc = $row['descripcion'];
                    $shortDesc = strlen($desc) > 30 ? substr($desc, 0, 30) . "..." : $desc;
                ?>
                <p><strong>Descripción:</strong> <?php echo htmlspecialchars($shortDesc); ?>
                    <?php if (strlen($desc) > 30): ?>
                        <button class="btn" onclick="openModal('<?php echo htmlspecialchars(addslashes($desc)); ?>')">Ver más</button>
                    <?php endif; ?>
                </p>

                <a href="../APICooperativa/<?php echo htmlspecialchars($row['nombre_comprobante']); ?>" target="_blank" class="btn btn-ver">Ver comprobante</a>

                <?php if ($row['estado'] === "En proceso"): ?>
                    <form method="post" action="procesar_estado.php" style="margin-top:10px;">
                        <input type="hidden" name="id" value="<?php echo $row['id_comprobante']; ?>">
                        <input type="hidden" name="adminId" id="adminIdSend" value="">
                        <button type="submit" name="accion" value="Aceptar" class="btn btn-aceptar">Aceptar</button>
                        <button type="submit" name="accion" value="Invalido" class="btn btn-invalido">Inválido</button>
                    </form>

                <?php else: ?>
                    <p class="estado">Gestionado por: <strong><?php echo $row['gestionado_por']; ?></strong></p>
                    <p class="estado">Estado: <strong><?php echo htmlspecialchars($row['estado']); ?></strong></p>
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

    <script>
        function openModal(desc) {
            document.getElementById("modalText").innerText = desc;
            document.getElementById("modalDesc").style.display = "block";
        }
        function closeModal() {
            document.getElementById("modalDesc").style.display = "none";
        }
        window.onclick = function(event) {
            if (event.target == document.getElementById("modalDesc")) {
                closeModal();
            }
        }

        const adminId = localStorage.getItem('adminId');

if (!adminId) {
    document.getElementById('saludo').innerText = "No has iniciado sesión.";
    window.location.href = "login.html";
} else {
    fetch(`../APIAdmin/api.php/administrativos/${adminId}`)
        .then(res => res.json())
        .then(admin => {
            if (admin && admin.nombre_apellidos) {
                document.getElementById('saludo').innerHTML = 
                    `${admin.nombre_apellidos}`;
            } else {
                document.getElementById('saludo').innerText = "Error al cargar datos.";
            }
        })
        .catch(() => {
            document.getElementById('saludo').innerText = "Error al conectar con la API.";
        });
}
document.addEventListener("DOMContentLoaded", () => {
    const adminId = localStorage.getItem('adminId');
    document.querySelectorAll("#adminIdSend").forEach(input => {
        input.value = adminId;
    });
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
