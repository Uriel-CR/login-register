
function validarInput(input, errorId) {
    var valor = parseInt(input.value);
    var errorElement = document.getElementById(errorId);
    // Verificar si el valor ingresado es menor a 70
    if (valor < 70) {
        errorElement.textContent = "La calificación debe ser de 70 a 100 o N/A";
    } else {
        errorElement.textContent = "";
    }
}

function convertirMayusculas(input) {
    input.value = input.value.toUpperCase();
}

function validarCalificaciones() {
    var parcial1 = parseInt(document.forms["calificaciones"]["parcial_1"].value);
    var parcial2 = parseInt(document.forms["calificaciones"]["parcial_2"].value);
    var parcial3 = parseInt(document.forms["calificaciones"]["parcial_3"].value);

    // Verificar si los valores son números y mayores a 70
    if (parcial1 < 70 || parcial2 < 70 || parcial3 < 70) {
        alert("La calificación debe ser mayor a 70 en todos los parciales");
        return false;
    }
    return true;
}

function bloquearCelda(input) {
    var celda = input.parentNode;
    input.disabled = true;
}

function redirectPanel() {
    window.location.href = "../assets/php/alumnos.php";
}

function calcularPromedio() {
    var parcial1 = document.getElementsByName("parcial_1")[0].value.trim();
    var parcial2 = document.getElementsByName("parcial_2")[0].value.trim();
    var parcial3 = document.getElementsByName("parcial_3")[0].value.trim();
    
    if (parcial1 === "N/A" || parcial2 === "N/A" || parcial3 === "N/A") {
        document.getElementsByName("promedio")[0].value = "N/A";
        document.getElementsByName("calif_final")[0].value = "N/A";
        return;
    }
    
    parcial1 = parseFloat(parcial1);
    parcial2 = parseFloat(parcial2);
    parcial3 = parseFloat(parcial3);
    
    var promedio = (parcial1 + parcial2 + parcial3) / 3;
    var calificacionFinal = promedio; // Suponiendo que la calificación final es igual al promedio
    
    document.getElementsByName("promedio")[0].value = promedio.toFixed(2);
    document.getElementsByName("calif_final")[0].value = calificacionFinal.toFixed(2);
}

 // Función para mostrar las calificaciones
 function mostrarCalificaciones() {
    document.forms[0].submit();
}

// Obtener referencia al botón "Mostrar" y a la ventana modal
var showModalButtons = document.querySelectorAll('.show-modal');
var modal = document.getElementById('myModal');
var modalContent = document.getElementById('modalContent');

// Agregar evento clic a cada botón "Mostrar"
showModalButtons.forEach(function(button) {
  button.addEventListener('click', function() {
    // Obtener los datos del turno del atributo de datos del botón
    var claveMateria = this.getAttribute('data-clave');
    var nombreMateria = this.getAttribute('data-nombre');
    
    // Mostrar los detalles del turno en la ventana modal
    modalContent.innerHTML = "Clave de materia: " + claveMateria + "<br>Nombre de materia: " + nombreMateria;
    
    // Mostrar la ventana modal
    modal.style.display = 'block';
  });
});

// Obtener referencia al botón de cierre y cerrar la ventana modal al hacer clic en él
var closeButton = document.getElementsByClassName('close')[0];
closeButton.addEventListener('click', function() {
  modal.style.display = 'none';
});

// Cerrar la ventana modal si se hace clic fuera de ella
window.addEventListener('click', function(event) {
  if (event.target == modal) {
    modal.style.display = 'none';
  }
});




