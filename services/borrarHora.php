<?php

include '../conexion_mssql.php';

$horaId = trim($_POST['horaId']);

$sqlCtrlI = "SELECT ID_Hora FROM CRM_Horas WHERE ID_Hora='$horaId'";
$ressqlCtrlI = sqlsrv_query($conn, $sqlCtrlI);
$regCtrlI = sqlsrv_fetch_array($ressqlCtrlI);

if (sqlsrv_has_rows($ressqlCtrlI) === true) {

    $sql = "DELETE FROM CRM_Horas WHERE ID_Hora='$horaId'";
    $ressql = sqlsrv_query($conn, $sql);

    if ($ressql === false) {
        die(json_encode(array('status' => '201', 'error' => sqlsrv_errors())));
    } else {
        die(json_encode(array('status' => '200', 'error' => '')));
    }

}