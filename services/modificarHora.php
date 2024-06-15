<?php

include '../conexion_mssql.php';

$idHora = trim($_POST['idHora']);
$cantidad = trim($_POST['cantidad']);
$tipo = trim($_POST['tipo']);
$cliente = trim($_POST['cliente']);
$comentario = trim($_POST['comentario']);
$fechaDeInicio = trim($_POST['fechaDeInicio']);
$sqlCtrlI = "SELECT ID_Hora FROM CRM_Horas WHERE ID_Hora='$idHora'";
$ressqlCtrlI = sqlsrv_query($conn, $sqlCtrlI);
$regCtrlI = sqlsrv_fetch_array($ressqlCtrlI);

if (sqlsrv_has_rows($ressqlCtrlI) === true) {

    $sql = "UPDATE CRM_Horas SET Horas='$cantidad', ID_TipoHora='$tipo', ID_Cliente='$cliente', Comentario='$comentario', FechaInicio='$fechaDeInicio' WHERE ID_Hora='$idHora'";
    $ressql = sqlsrv_query($conn, $sql);
    if ($ressql === false) {
        die(json_encode(array('status' => '201', 'error' => sqlsrv_errors())));
    } else {
        die(json_encode(array('status' => '200', 'error' => '')));
    }

}