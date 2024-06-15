<?php

include '../conexion_mssql.php';

$horas = trim($_POST['cantidad']);
$ID_TipoHora = trim($_POST['tipo']); //FAC o ABO
$ID_Cliente = ($_POST['cliente']);
$comentario = trim($_POST['comentario']);
$fechaInicio = trim($_POST['fechaDeInicio']);
$fechaCarga = trim($_POST['fechaCarga']);
// Reemplazar con el id del usuario traído del login
$ID_Vendedor = 2;
$sql = "INSERT INTO CRM_Horas (ID_Cliente,horas,ID_TipoHora, comentario,ID_Vendedor, fechaInicio,fechaCarga) VALUES ('$ID_Cliente','$horas','$ID_TipoHora','$comentario','$ID_Vendedor','$fechaInicio','$fechaCarga');";

$ressql = sqlsrv_query($conn, $sql);

if ($ressql === false) {
    die(json_encode(array('status' => '201', 'error' => sqlsrv_errors())));
} else {
    die(json_encode(array('status' => '200', 'error' => '')));
}