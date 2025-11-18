/* ===============================
       SISTEMA DE TRADUCCIÓN
================================ */

let idioma = localStorage.getItem("idioma") || "es";

const traducciones = {
    es: {
        asociate: "¡Asociate!",
        iniciar_sesion: "Iniciar Sesión",
        inicio: "Inicio",
        ingresar:"Ingresa tus credenciales para acceder a tu cuenta.",

        hero_titulo: "Construí con nosotros el hogar donde tus sueños crecen.",
        hero_desc: "En CRS (Cooperativa Richie Silver), creemos que un hogar se construye con más que ladrillos: se construye con solidaridad, esfuerzo compartido y sueños comunes. Somos una cooperativa de vivienda de ayuda mutua que impulsa la autogestión y el trabajo colectivo para hacer posible el acceso a una vivienda digna. Nuestro símbolo (un apretón de manos bajo un techo de colores) representa lo que somos: unión, compromiso y diversidad bajo un mismo objetivo.",

        vision_titulo: "Nuestra Visión y Compromiso:",
        vision_desc: "En CRS creemos que la vivienda es un derecho y un proyecto colectivo. Nos comprometemos a fomentar la autogestión, la solidaridad y el trabajo compartido para que cada familia acceda a un hogar digno, construido con esfuerzo común.",

        como_titulo: "Cómo funciona la Cooperativa:",
        como_desc: "Nos organizamos bajo ayuda mutua y autogestión. Cada asociado participa en las decisiones, aporta cuotas mensuales y realiza horas de trabajo comunitario. Las asambleas garantizan que todos podamos opinar y construir juntos nuestro futuro.",

        historia_titulo: "Nuestra Historia:",
        historia_desc: "La CRS nació por la iniciativa de Richie Silver y un grupo de familias. Inspirados por la solidaridad, comenzaron a reunirse y a proyectar sus viviendas. Desde entonces, trabajamos con esfuerzo colectivo y compromiso para construir un hogar digno para todos.",

        faq_titulo: "Preguntas frecuentes",

        faq_p1: "¿Cómo asociarse?",
        faq_r1: "Podés completar el formulario de inscripción o acercarte a nuestras reuniones abiertas.",

        noticias_titulo: "Noticias",

        login_titulo: "Iniciar Sesión",
        login_desc: "Ingresa tus credenciales para acceder a tu cuenta",

        registro_titulo: "Registro de Nuevo Socio",
        registro_desc: "¡Complete el siguiente formulario para mandar una solicitud para asociarte!",
        registro_foto: "Foto de perfil",
        registro_nombre: "Nombre",
        registro_apellido: "Apellido",
        registro_cedula: "Cédula",
        registro_fechaNac: "Fecha de Nacimiento",
        registro_telefono: "Teléfono",
        registro_correo: "Correo Electronico",
        registro_contra: "Contraseña",

        registrar: "Registrarse",
        ya_tienes: "¿Ya tienes una cuenta? ",
        inicia_aqui: "Inicia sesión aquí",

        no_tienes:"¿No tienes una cuenta?",
        registra_aqui: "Regístrate aquí"
    },

    en: {
        asociate: "Join Us!",
        iniciar_sesion: "Login",
        inicio: "Home",
        ingresar: "Enter your credentials to access your account.",

        hero_titulo: "Build with us the home where your dreams grow.",
        hero_desc: "At CRS (Richie Silver Cooperative), we believe that a home is built with more than bricks: it's built with solidarity, shared effort, and common dreams. We are a mutual-aid housing cooperative that promotes self-management and collective work to make access to decent housing possible. Our symbol (a handshake under a colorful roof) represents who we are: unity, commitment, and diversity united by a common goal.", 

        vision_titulo: "Our Vision and Commitment:",
        vision_desc: "At CRS, we believe that housing is a right and a collective project. We are committed to promoting self-management, solidarity, and shared work so that every family can access a decent home, built through collective effort.",

        como_titulo: "How the Cooperative Works:",
        como_desc: "We are organized through mutual aid and self-management. Each member participates in decisions, contributes monthly dues, and performs community service hours. Assemblies ensure that everyone can express their opinions and build our future together.",

        historia_titulo: "Our History:",
        historia_desc: "We are organized through mutual aid and self-management. Each member participates in decisions, contributes monthly dues, and performs community service hours. Assemblies ensure that everyone can express their opinions and build our future together.",

        faq_titulo: "Frequently Asked Questions",

        faq_p1: "How to join?",
        faq_r1: "You can complete the registration form or come to our open meetings.",

        noticias_titulo: "News",

        login_titulo: "Login",
        login_desc: "Enter your credentials to access your account",

        registro_titulo: "New Member Registration",
        registro_desc: "Complete the form below to send your membership request!",
        registro_foto: "Profile Photo",
        registro_nombre: "First Name",
        registro_apellido: "Last Name",
        registro_cedula: "ID Number",
        registro_fechaNac: "Date of Birth",
        registro_telefono: "Phone Number",
        registro_correo: "Email",
        registro_contra: "Password",

        registrar: "Register",
        ya_tienes: "Already have an account? ",
        inicia_aqui: "Login here",

        no_tienes:"Don't you have an account?",
        registra_aqui: "Register here"
    }
};

function aplicarIdioma() {
    document.querySelectorAll("[traduc]").forEach(el => {
        const clave = el.getAttribute("traduc");
        if (traducciones[idioma][clave]) {
            el.textContent = traducciones[idioma][clave];
        }
    });
}

window.cambiarIdioma = function () {
    idioma = idioma === "es" ? "en" : "es";
    localStorage.setItem("idioma", idioma);
    aplicarIdioma();
};

document.addEventListener("DOMContentLoaded", aplicarIdioma);

/* ===============================
              FAQS
================================ */

const faqs = [
  {
    pregunta: { es: "¿Cómo asociarse?", en: "How to join?" },
    respuesta: { es: "Podés completar el formulario de inscripción o acercarte a nuestras reuniones abiertas.",
                 en: "You can complete the registration form or attend our open meetings." }
  },
  {
    pregunta: { es: "¿Qué beneficios obtengo?", en: "What benefits do I get?" },
    respuesta: { es: "Acceso a vivienda digna, participación activa, apoyo mutuo y desarrollo comunitario.",
                 en: "Access to decent housing, active participation, mutual support, and community development." }
  },
  {
    pregunta: { es: "¿Qué servicios brinda la cooperativa?", en: "What services does the cooperative offer?" },
    respuesta: { es: "Asesoramiento, acompañamiento técnico, espacios de participación y formación.",
                 en: "Guidance, technical support, participation spaces, and training." }
  },
  {
    pregunta: { es: "¿Quiénes pueden participar?", en: "Who can participate?" },
    respuesta: { es: "Cualquier persona mayor de edad con voluntad de comprometerse con el proyecto colectivo.",
                 en: "Any adult willing to commit to the collective project." }
  }
];

let index = 0;
let intervalId;

const questionEl = document.getElementById("faq-question");
const answerEl = document.getElementById("faq-answer");
const faqBox = document.getElementById("faq-box");

function updateFAQ(newIndex) {
    if (!questionEl || !answerEl) return;

    questionEl.style.opacity = 0;
    answerEl.style.opacity = 0;

    setTimeout(() => {
        questionEl.textContent = faqs[newIndex].pregunta[idioma];
        answerEl.textContent = faqs[newIndex].respuesta[idioma];

        questionEl.style.opacity = 1;
        answerEl.style.opacity = 1;
    }, 300);
}

function nextFAQ() {
    index = (index + 1) % faqs.length;
    updateFAQ(index);
}

function prevFAQ() {
    index = (index - 1 + faqs.length) % faqs.length;
    updateFAQ(index);
}

function startAutoRotate() {
    intervalId = setInterval(nextFAQ, 4000);
}

function stopAutoRotate() {
    clearInterval(intervalId);
}

/* Activación FAQ */
if (faqBox) {
    faqBox.addEventListener("mouseenter", stopAutoRotate);
    faqBox.addEventListener("mouseleave", startAutoRotate);
    updateFAQ(index);
    startAutoRotate();
}

/* ===============================
             NOTICIAS
================================ */

const noticias = [
  {
    titulo: { es: "Fiesta de Integración", en: "Integration Event" },
    texto: { es: "El 10 de agosto celebraremos una jornada de integración con juegos para niños, música y merienda compartida. Un espacio para fortalecer vínculos y celebrar nuestros avances.",
             en: "On August 10th, we will celebrate an integration day with games for children, music, and a shared snack. It will be an opportunity to strengthen bonds and celebrate our progress." },
    imagen: "../imgs/nenes.avif"
  },
  {
    titulo: { es: "Proyecto de Huerta Comunitaria", en: "Community Garden Project" },
    texto: { es: "Estamos iniciando el proyecto de huerta colectiva en el predio común. Si querés sumarte al equipo organizador, escribí al correo de la cooperativa o acercate los miércoles de tarde al salón.",
             en: "We're starting a community garden project on our shared land. If you'd like to join the organizing team, email the cooperative or come to the community center on Wednesday afternoons." },
    imagen: "../imgs/huerta.webp"
  },
  {
    titulo: { es: "Nuevo Convenio con Mutual de Ahorro", en: "New Agreement with Savings Mutual" },
    texto: { es: "Firmamos un acuerdo con la Mutual Solidaria que permitirá acceder a microcréditos para mejoras habitacionales. Pronto compartiremos información detallada en la asamblea.",
             en: "We signed an agreement with Mutual Solidaria that will provide access to microloans for home improvements. We will share detailed information at the assembly soon." },
    imagen: "../imgs/firma.jpg"
  },
  {
    titulo: { es: "Taller de Autoconstrucción", en: "Self-Construction Workshop" },
    texto: { es: "El sábado 27 de julio a las 10:00 h invitamos a todos los socios al taller práctico de autoconstrucción. Aprenderemos técnicas básicas de albañilería y pintura. Actividad gratuita con cupos limitados.",
             en: "On Saturday, July 27th at 10:00 AM, we invite all members to a hands-on self-construction workshop. We will learn basic masonry and painting techniques. This is a free activity with limited spaces available." },
    imagen: "../imgs/taller.jpg"
  }
];

let noticiaIndex = 0;
let noticiaIntervalId;

const noticia = document.getElementById("noti");
const noticia1 = document.getElementById("noti1");
const noticiaImg = document.getElementById("noti-img");
const noticiaBox = document.getElementById("noti-box");

function updateNoti(newIndex) {
    if (!noticia || !noticia1 || !noticiaImg) return;

    noticia.style.opacity = 0;
    noticia1.style.opacity = 0;
    noticiaImg.style.opacity = 0;

    setTimeout(() => {
        noticia.textContent = noticias[newIndex].titulo[idioma];
        noticia1.textContent = noticias[newIndex].texto[idioma];
        noticiaImg.src = noticias[newIndex].imagen;

        noticia.style.opacity = 1;
        noticia1.style.opacity = 1;
        noticiaImg.style.opacity = 1;
    }, 300);
}

function nextNoti() {
    noticiaIndex = (noticiaIndex + 1) % noticias.length;
    updateNoti(noticiaIndex);
}

function prevNoti() {
    noticiaIndex = (noticiaIndex - 1 + noticias.length) % noticias.length;
    updateNoti(noticiaIndex);
}

function startNoticiaAutoRotate() {
    noticiaIntervalId = setInterval(nextNoti, 5000);
}

function stopNoticiaAutoRotate() {
    clearInterval(noticiaIntervalId);
}

/* Activación Noticias */
if (noticiaBox) {
    noticiaBox.addEventListener("mouseenter", stopNoticiaAutoRotate);
    noticiaBox.addEventListener("mouseleave", startNoticiaAutoRotate);
    updateNoti(noticiaIndex);
    startNoticiaAutoRotate();
}
