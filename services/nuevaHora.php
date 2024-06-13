<?php

include '../conexion_mssql.php';

$cantidad = trim($_POST['cantidad']);
$tipo = trim($_POST['tipo']);
$cliente = trim($_POST['cliente']);
$comentario = trim($_POST['comentario']);
$fechaDeInicio = trim($_POST['fechaDeInicio']);
$horaDeInicio = trim($_POST['horaDeInicio']);
// Reemplazar con el id del usuario traído del login
$idUsuario = 1;

$sql = "INSERT INTO  CRM_HORAS_TRABAJADAS (cliente,tipo,horaDeInicio,cantidad,comentario, ID_Usuario, fechaDeInicio) VALUES ('$cliente','$tipo','$horaDeInicio','$cantidad','$comentario','$idUsuario', '$fechaDeInicio');";

$ressql = sqlsrv_query($conn, $sql);

if ($ressql === false) {
    die(json_encode(array('status' => '201', 'error' => sqlsrv_errors())));
} else {
    die(json_encode(array('status' => '200', 'error' => '')));
}