<?php
// Iniciar sesión para poder acceder a las variables de sesión
session_start();

// Verificar si el usuario está autenticado (debes implementar la lógica de autenticación)
if (!isset($_SESSION['matricula'])) {
    die('No se ha iniciado sesión o no se encontró la matrícula del alumno.');
}

$matricula_alumno = $_SESSION['matricula'];

$conexion = mysqli_connect("localhost", "root", "", "login_register_db");
require('./fpdf.php');

class PDF extends FPDF
{
    private $alumno_id; // Variable para almacenar el ID del alumno

    // Constructor que recibe la matrícula del alumno
    function __construct($matricula)
    {
        parent::__construct();
        global $conexion;

        // Consulta para obtener el ID del alumno según la matrícula
        $consulta_id_alumno = $conexion->query("SELECT id_alumno FROM alumnos WHERE matricula = '{$matricula}' LIMIT 1");
        if ($consulta_id_alumno->num_rows > 0) {
            $alumno = $consulta_id_alumno->fetch_assoc();
            $this->alumno_id = $alumno['id_alumno'];
        } else {
            die('No se encontró ningún alumno con la matrícula proporcionada.');
        }
    }

    // Cabecera de página
    function Header()
    {
        // Logo e información de la institución
        $this->Image('logo.png', 10, 10, 40);
        $this->Image('sistemass.jpeg', 150, 5, 20);
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor(0);

        // Ajustar la posición Y para el título
        $this->SetY(30);  // Ajustar este valor según sea necesario
        $this->Cell(0, 10, utf8_decode("TECNOLÓGICO DE ESTUDIOS SUPERIORES DE IXTAPALUCA"), 0, 1, 'C');
        
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, utf8_decode("REPORTE DE CALIFICACIONES"), 0, 1, 'C');

        $this->Ln(5);

        // Datos del alumno
        global $conexion;
        // Consulta para obtener los datos del alumno
        $consulta_alumno = $conexion->query("SELECT a.*, g.nombre_grupo, p.periodo 
                                             FROM alumnos a 
                                             JOIN calificaciones c ON a.id_alumno = c.id_alumno
                                             JOIN grupos g ON c.id_grupo = g.id_grupo
                                             JOIN periodos p ON c.id_periodo = p.id_periodo
                                             WHERE a.id_alumno = {$this->alumno_id}
                                             LIMIT 1");
        $alumno = $consulta_alumno->fetch_assoc();

        // Mostrar datos del alumno
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, utf8_decode("Nombre del alumno: {$alumno['nombre']} {$alumno['ap_paterno']} {$alumno['ap_materno']}"), 0, 1);
        $this->Cell(0, 10, utf8_decode("Matrícula: {$alumno['matricula']}"), 0, 1);
        $this->Cell(0, 10, utf8_decode("Carrera: Ing. Sistemas Computacionales"), 0, 1);
        $this->Cell(0, 10, utf8_decode("Periodo: {$alumno['periodo']}"), 0, 1);
        $this->Cell(0, 10, utf8_decode("Grupo: {$alumno['nombre_grupo']}"), 0, 1);

        $this->Ln(5);
    }

    // Tabla de calificaciones
    function TablaCalificaciones()
    {
        global $conexion;

        // Consulta para obtener las calificaciones del alumno según su ID
        $consulta_calificaciones = $conexion->query("SELECT m.nombre AS materia, m.creditos, c.* 
                                                     FROM calificaciones c 
                                                     INNER JOIN materias m ON c.id_materia = m.id_materia 
                                                     WHERE c.id_alumno = {$this->alumno_id}");

        // Encabezados de la tabla
        $this->SetFillColor(200, 220, 255);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(60, 10, 'Materia', 1, 0, 'C', true);
        $this->Cell(25, 10, 'Creditos', 1, 0, 'C', true);
        $this->Cell(25, 10, 'Parcial 1', 1, 0, 'C', true);
        $this->Cell(25, 10, 'Parcial 2', 1, 0, 'C', true);
        $this->Cell(25, 10, 'Parcial 3', 1, 0, 'C', true);
        $this->Cell(30, 10, 'Calificacion Final', 1, 1, 'C', true);

        // Datos de calificaciones
        $this->SetFont('Arial', '', 8); // Reducir el tamaño de letra para las materias
        while ($fila = $consulta_calificaciones->fetch_assoc()) {
            $this->Cell(60, 10, utf8_decode($fila['materia']), 1, 0, 'C');
            $this->Cell(25, 10, $fila['creditos'], 1, 0, 'C');
            $this->Cell(25, 10, $fila['parcial_1'], 1, 0, 'C');
            $this->Cell(25, 10, $fila['parcial_2'], 1, 0, 'C');
            $this->Cell(25, 10, $fila['parcial_3'], 1, 0, 'C');
            $this->Cell(30, 10, $fila['calif_final'], 1, 1, 'C');
        }
    }

    // Pie de página
    function Footer()
    {
        $this->SetY(-20);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');

        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 8, utf8_decode('ESTE DOCUMENTO NO TIENE VALIDEZ PARA TRÁMITES ACADÉMICOS'), 0, 0, 'C');

        $this->SetY(-10);
        $this->SetFont('Arial', 'I', 8);
        $hoy = date('d/m/Y');
        $this->Cell(0, 10, utf8_decode($hoy), 0, 0, 'C');
    }
}

$pdf = new PDF($matricula_alumno);
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Arial', '', 12);
$pdf->TablaCalificaciones();

$pdf->Output('ReporteCalificaciones.pdf', 'I');
?>
