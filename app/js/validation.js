function validarRegistro() {
    // Obtener elementos del formulario
    let dniElem = document.querySelector("[name='dni']");
    let telefonoElem = document.querySelector("[name='telefono']");
    let nombreElem = document.querySelector("[name='nombre']");
    let apellidosElem = document.querySelector("[name='apellidos']");
    let usuarioElem = document.querySelector("[name='usuario']") || document.querySelector("[name='nombre_usuario']");
    let passwordElem = document.querySelector("[name='password']") || document.querySelector("[name='contrasena']");
    let emailElem = document.querySelector("[name='email']");
    let fechaElem = document.querySelector("[name='fecha']") || document.querySelector("[name='fecha_nacimiento']");
    
    // Verificar que todos los elementos requeridos existen
    if (!dniElem || !telefonoElem || !nombreElem || !apellidosElem || !usuarioElem || !emailElem || !fechaElem) {
        console.error("No se encontraron todos los campos requeridos");
        return false;
    }

    // Obtener valores
    const dni = dniElem.value.trim();
    const telefono = telefonoElem.value.trim();
    const nombre = nombreElem.value.trim();
    const apellidos = apellidosElem.value.trim();
    const usuario = usuarioElem.value.trim();
    const password = passwordElem ? passwordElem.value : "";
    const email = emailElem.value.trim();
  const dniRegex = /^\d{8}-[A-Z]$/;
  const telRegex = /^\d{9}$/;
    const nombreRegex = /^[A-Za-zÀ-ÖØ-öø-ÿ '´`.-]{2,100}$/; // letters, spaces and common name chars
    const usuarioRegex = /^[A-Za-z0-9_\-]{3,50}$/;
    const passwordMinLen = 6;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  if (!dniRegex.test(dni)) {
    alert("Formato DNI incorrecto (11111111-Z)");
    return false;
  }

  const letras = "TRWAGMYFPDXBNJZSQVHLCKE";
  const numero = parseInt(dni.substring(0,8));
  const letra = dni.charAt(9);
  if (letras[numero % 23] !== letra) {
    alert("La letra del DNI no corresponde");
    return false;
  }

  if (!telRegex.test(telefono)) {
    alert("Teléfono debe tener 9 dígitos");
    return false;
  }

    if (!nombreRegex.test(nombre)) {
      alert("Nombre no válido. Solo letras y espacios, 2-100 caracteres.");
      return false;
    }

    if (!nombreRegex.test(apellidos)) {
      alert("Apellidos no válidos. Solo letras y espacios, 2-100 caracteres.");
      return false;
    }

    if (!usuarioRegex.test(usuario)) {
      alert("Nombre de usuario no válido. Solo letras, números, _ o - y 3-50 caracteres.");
      return false;
    }

    // Validación de fecha (obligatoria, formato válido YYYY-MM-DD y no futura)
    const fecha = (fechaElem.value || "").trim();
    if (!fecha) {
      alert("La fecha de nacimiento es obligatoria");
      return false;
    }
    // Comprobar patrón básico AAAA-MM-DD
    const fechaRegex = /^\d{4}-\d{2}-\d{2}$/;
    if (!fechaRegex.test(fecha)) {
      alert("La fecha de nacimiento no es válida (use AAAA-MM-DD)");
      return false;
    }
    // Validar fecha real y que no sea futura
    const fechaDate = new Date(fecha);
    if (isNaN(fechaDate.getTime())) {
      alert("La fecha de nacimiento no es válida");
      return false;
    }
    const hoy = new Date();
    // Normalizar horas para comparar solo fecha
    fechaDate.setHours(0,0,0,0);
    hoy.setHours(0,0,0,0);
    if (fechaDate > hoy) {
      alert("La fecha de nacimiento no puede ser futura");
      return false;
    }

    // Verificar si estamos en el formulario de registro o si se ha introducido una contraseña
    const isRegisterForm = document.getElementById('register_form') !== null;
    if (isRegisterForm || password.length > 0) {
      if (password.length < passwordMinLen) {
        alert("La contraseña debe tener al menos " + passwordMinLen + " caracteres.");
        return false;
      }
    }

    if (!emailRegex.test(email)) {
      alert("Email no válido. Usa el formato ejemplo@servidor.ext");
      return false;
    }

  return true;
}

// make function global so inline scripts can call it
window.validarRegistro = validarRegistro;

