<?php

date_default_timezone_set('America/Argentina/Buenos_Aires');

$serverName = "";
$connectionInfo = array("Database" => "");
$conn = sqlsrv_connect($serverName, $connectionInfo);