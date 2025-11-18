<?php
require_once "../APIUsuarios/config.php";

$query = "SELECT cedula, nombre, apellido, perfil FROM usuario WHERE aceptado = 1 ORDER BY apellido, nombre";
$result = mysqli_query($conn, $query);

function getSemana($fecha) {
    return date("W", strtotime($fecha));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>CRS - Gestión de Horas</title>
<link rel="stylesheet" href="css/estilos.css">
<style>
body{background:#1e1e1e;color:white;}
.socios-list{display:flex;flex-direction:column;gap:14px;margin-top:18px;}
.socio-card{display:flex;justify-content:space-between;align-items:center;background:#2a2a2a;padding:14px;border-radius:10px;}
.btn-horas{background:#2f6bfd;padding:8px 14px;border:none;border-radius:8px;color:white;cursor:pointer;}
.det-horas{display:none;background:#1c1c1c;padding:12px;margin-top:8px;border-radius:10px;}
.semana-card{background:#333;padding:10px;border-radius:8px;margin-top:8px;}
.modal {display: none;position: fixed;top: 0; left: 0; width: 100%; height: 100%;background: rgba(0,0,0,0.7);justify-content: center;align-items: center;z-index: 999;}
.modal-content {background: #2a2a2a;padding: 20px;border-radius: 10px;color: white;max-width: 450px;text-align: center;}
.close-modal {background: #ff4d4d;border: none;padding: 8px 14px;margin-top: 15px;border-radius: 6px;cursor: pointer;color: white;}
.btn-ver-motivo {background: #ffb347;border: none;padding: 6px 10px;border-radius: 6px;cursor: pointer;margin-top: 5px;}
</style>
</head>

<body>

<header>
    <div class="header-content">
        <a class="logo" href="../LandingYLogin/landing.html">
            <img style="height:60px;width:60px;" src="../imgs/Logo1.png">
            <h1 style="padding-left:6px;">CRS</h1>
        </a>
        <h2 id="saludo">Cargando...</h2>
    </div>
</header>

<div class="admin-container">
<aside class="sidebar">
    <h3>ADMINISTRACIÓN</h3>
    <ul>
        <li><a href="inicio.php">Inicio</a></li>
        <li><a href="solicitudes.html">Solicitudes</a></li>
        <li class="active"><a href="gestiónHoras.php">Gestión de Horas</a></li>
        <li><a href="comprobantes.php">Comprobantes</a></li>
        <li><a href="exoneraciones.php">Exoneraciones</a></li>
        <li><a href="reportes.php">Reportes</a></li>
    </ul>
    <a href="#" class="logout-bottom" onclick="cerrarSesion()">Cerrar sesión</a>
</aside>

<main class="main-content">
<h1 style="color: #1a1a1a">Gestión de horas por socio</h1>

<section class="socios-list">
<?php while($u = mysqli_fetch_assoc($result)): ?>
    <div>
        <div class="socio-card">
            <div>
                <strong><?= $u['nombre'] . " " . $u['apellido']; ?></strong> - <?= $u['cedula']; ?>
            </div>
            <button class="btn-horas" onclick="verHoras('<?= $u['cedula']; ?>')">Ver horas</button>
        </div>
        <div class="det-horas" id="horas-<?= $u['cedula']; ?>"></div>
    </div>
<?php endwhile; ?>
</section>

</main>
</div>

<script>
async function verHoras(cedula){
    const cont = document.getElementById("horas-" + cedula);

    if(cont.style.display === "block"){
        cont.style.display = "none";
        return;
    }

    const res = await fetch(`../APICooperativa/apiHoras.php?cedula=${cedula}`);
    let semanas = await res.json();

    cont.innerHTML = "";

    // 1) Ordenar por fecha de más vieja a más nueva
    semanas.sort((a, b) => new Date(a.fecha) - new Date(b.fecha));

    // 2) Numerar semanas según orden
    semanas = semanas.map((s, index) => ({
        ...s,
        numeroSemana: index + 1
    }));

    // 3) Mostrar de la más nueva a la más vieja
    semanas.reverse();

    semanas.forEach(s => {
        cont.innerHTML += `
        <div class="semana-card">
            <p><strong>Semana ${s.numeroSemana} - (${formatearRangoFechas(s.fecha)})</strong></p>
            <p><strong>Horas:</strong> ${s.cantidad_hs} / 21 hrs</p>
            <p><strong>Estado:</strong> ${s.estado}</p>
            ${s.solicitud == 1 ? `<button class="btn-ver-motivo" onclick="openMotivoModal('${s.motivo.replace(/'/g, "\\'")}')">Ver motivo</button>` : ""}
        </div>`;
    });

    cont.style.display = "block";
}


// Calcula inicio y fin de semana según la fecha recibida
function formatearRangoFechas(fecha){
    const f = new Date(fecha);
    const inicio = new Date(f);
    const fin = new Date(f);
    inicio.setDate(f.getDate() - f.getDay() + 1);
    fin.setDate(inicio.getDate() + 6);
    return `${inicio.toLocaleDateString()} - ${fin.toLocaleDateString()}`;
}

const adminId = localStorage.getItem('adminId');
if(adminId){
    fetch(`../APIAdmin/api.php/administrativos/${adminId}`)
    .then(res=>res.json())
    .then(a=>{ document.getElementById("saludo").innerText = a.nombre_apellidos; });
} else {
    document.getElementById("saludo").innerText = "No has iniciado sesión.";
    window.location.href = "login.html";
}

function openMotivoModal(motivo) {
  const modal = document.getElementById("motivoModal");
  const texto = document.getElementById("motivoTexto");

  texto.innerText = motivo && motivo.trim() !== "" ? motivo : "No se especificó un motivo.";
  modal.style.display = "flex";
}


function closeMotivoModal() {
  document.getElementById("motivoModal").style.display = "none";
}

window.onclick = function(event) {
  const modal = document.getElementById("motivoModal");
  if (event.target === modal) {
    closeMotivoModal();
  }
};

function cerrarSesion() {
  // Borrar sesión del admin
  localStorage.removeItem('adminId');

  // Redirigir al login
  window.location.href = "login.html";
}
</script>
<div id="motivoModal" class="modal">
  <div class="modal-content">
      <h3>Motivo de la exoneración</h3>
      <p id="motivoTexto"></p>
      <button class="close-modal" onclick="closeMotivoModal()">Cerrar</button>
  </div>
</div>

</body>
</html>
