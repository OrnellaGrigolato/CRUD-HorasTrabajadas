<?php

include '../conexion_mssql.php';

$idHora = trim($_POST['idHora']);
$cantidad = trim($_POST['cantidad']);
$tipo = trim($_POST['tipo']);
$cliente = trim($_POST['cliente']);
$comentario = trim($_POST['comentario']);
$fechaDeInicio = trim($_POST['fechaDeInicio']);
$horaDeInicio = trim($_POST['horaDeInicio']);

$sqlCtrlI = "SELECT id FROM CRM_HORAS_TRABAJADAS WHERE id='$idHora'";

$ressqlCtrlI = sqlsrv_query($conn, $sqlCtrlI);
$regCtrlI = sqlsrv_fetch_array($ressqlCtrlI);

if (sqlsrv_has_rows($ressqlCtrlI) === true) {

    $sql = "UPDATE CRM_HORAS_TRABAJADAS SET cantidad='$cantidad',  tipo='$tipo', cliente='$cliente', comentario='$comentario', fechaDeInicio='$fechaDeInicio', horaDeInicio='$horaDeInicio' WHERE id='$idHora';";
    $ressql = sqlsrv_query($conn, $sql);
    if ($ressql === false) {
        die(json_encode(array('status' => '201', 'error' => sqlsrv_errors())));
    } else {
        die(json_encode(array('status' => '200', 'error' => '')));
    }

}