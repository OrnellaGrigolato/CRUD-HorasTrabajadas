<?php include 'conexion_mssql.php'; ?>

<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- JQuery && JQuery UI -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"
        integrity="sha256-DI6NdAhhFRnO2k51mumYeDShet3I8AKCQf/tf7ARNhI=" crossorigin="anonymous"></script>
    <!-- Alertify -->
    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>
    <!-- include the style Alertify-->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/alertify.min.css" />
    <!-- include a theme Alertify-->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/themes/default.min.css" />
    <!-- Font-->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome && ionic (icons) -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Adminlte (boostrap template) -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <script src="dist/js/adminlte.js"></script>
    <!-- Select2 (select component) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js " crossorigin="anonymous">
    </script>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
        </script>

    <style>
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        .top-container {
            background-color: #f1f1f1;
            padding: 30px;
            text-align: center;
        }

        .header {
            padding: 10px 16px;
            background: #555;
            color: #f1f1f1;
        }

        .content {
            padding: 16px;
        }

        .sticky {
            position: fixed;
            top: 0;
            width: 100%;
        }

        .sticky+.content {
            padding-top: 102px;
        }

        #texto {
            display: inline-block;
            vertical-align: top;
        }

        .card {
            margin-top: 2rem;
        }

        #fechayhora {
            border: 1px solid #aaa;
            padding: 3px 10px;
            border-radius: 4px;
        }

        .card-header {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;

        }

        .footer {
            width: 100%;
            text-align: right;
            margin-top: -1rem;
            padding-right: 1rem;
        }

        /* Styles for select filter component (have to use !important to override them) */
        .custom-selection1 .select2-selection__rendered .select2-selection__placeholder {
            color: black !important;
            font-family: monospace !important;
        }
    </style>

    <title>Whatsapp.Ar Horas Trabajadas</title>
</head>

<body>
    <div class="header" id="myHeader">
        <p id="texto">HORAS TRABAJADAS</p>
    </div>
    <div class="container">
        <div class="card">
            <div class="card-header bg-gradient-teal">
                <h6 class="mb-0">Horas Trabajadas</h6>
                <form
                    style="font-family : monospace; background-color: transparent; font-size : 10pt; color: black;margin: 0; display:flex; align-items: center; gap: 8px">
                    <b style="font-size : 17px;padding: 0px 10px 0px 0px;">Filtrar por Fecha</b>
                    <label for="birthdaytime">Desde</label>
                    <input type="date" id="desde" name="fechayhora" onchange="filtrarDesde(this.value);">
                    <a onclick="limpiarFiltro('Desde')" class="fa fa-times" id="limpiarFiltroDesde"
                        style="color: black;text-decoration: none; margin-right: 7px;visibility: hidden;cursor:pointer"></a>
                    <label for="birthdaytime">Hasta</label>
                    <input type="date" id="hasta" name="fechayhora" onchange="filtrarHasta(this.value);">
                    <a onclick="limpiarFiltro('Hasta')" class="fa fa-times" id="limpiarFiltroHasta"
                        style="color: black;text-decoration: none; margin-right: 17px;visibility: hidden;cursor:pointer"></a>

                    <?php
                    $query = "SELECT ID_Cliente,RazonSocial FROM CRM_CLIENTES";
                    $list = [];
                    if (($result = sqlsrv_query($conn, $query)) !== false) {
                        while ($contact = sqlsrv_fetch_array($result)) {
                            $cliente = [
                                'id_cliente' => (int) $contact['ID_Cliente'],
                                'RazonSocial' => mb_convert_encoding($contact['RazonSocial'], 'UTF-8', 'ISO-8859-1'),
                            ];
                            array_push($list, $cliente);
                        }
                    }
                    ?>

                    <select class="selectorFiltroCliente">
                        <option id=0 value='' selected="true">Filtrar por Cliente</option>
                        <?php foreach ($list as $row): ?>
                            <option id=<?= $row["id_cliente"] ?>><?= $row["RazonSocial"] ?></option>
                        <?php endforeach ?>
                    </select>

                    <a onclick="limpiarFiltro('Cliente')" class="fa fa-times" id="limpiarFiltroCliente"
                        style="color: black;text-decoration: none;cursor:pointer;display:none;"></a>

                </form>
            </div>
            <div class="card-body">
                <div id="tablaHoras"></div>
            </div>

            <p class="footer">Cantidad de Horas: <b id="cantidadHoras">0</b></p>

        </div>
    </div>

    <script>
        function actualizarTablaUsuario(filtroDesde, filtroHasta, filtroCliente) {
            $.ajax({
                async: true,
                type: "POST",
                data: {
                    filtroDesde,
                    filtroHasta,
                    filtroCliente
                },
                url: "tablaHoras.php",
                beforeSend: function () {
                    $("#tablaHoras").html(
                        '<div  style="width: 100%;display:flex; justify-content: center;"><div class="spinner-border text-primary" role="status" style="margin: 4px auto;"><span class="sr-only"></span></div></div>'
                    );
                },
                success: function (datos) {
                    $("#tablaHoras").html(datos);
                    const totalHoras = $('#totalHoras').val();
                    $('#cantidadHoras').text(totalHoras);
                }
            })
        };

        $(document).ready(function () {
            actualizarTablaUsuario("", "", "");
        });

        window.onscroll = function () {
            myFunction()
        };

        var header = document.getElementById("myHeader");
        var sticky = header.offsetTop;

        function myFunction() {
            if (window.pageYOffset > sticky) {
                header.classList.add("sticky");
            } else {
                header.classList.remove("sticky");
            }
        }

        function filtrarDesde(value) {
            document.getElementById("limpiarFiltroDesde").style.visibility = 'visible';
            actualizarTablaUsuario(value, document.getElementById("hasta").value, $(
                '.selectorFiltroCliente option:selected').attr("id") != 0 ?
                $(
                    '.selectorFiltroCliente option:selected').attr("id") : "");
        }

        function filtrarHasta(value) {
            document.getElementById("limpiarFiltroHasta").style.visibility = 'visible';
            actualizarTablaUsuario(document.getElementById("desde").value, value, $(
                '.selectorFiltroCliente option:selected').attr("id") != 0 ?
                $(
                    '.selectorFiltroCliente option:selected').attr("id") : "");
        }

        function limpiarFiltro(tipoDeFiltro) {
            switch (tipoDeFiltro) {
                case "Desde":
                    document.getElementById("desde").value = ""
                    document.getElementById("limpiarFiltroDesde").style.visibility = 'hidden'
                    actualizarTablaUsuario("", document.getElementById("hasta").value, $(
                        '.selectorFiltroCliente option:selected').attr("id") != 0 ? $(
                            '.selectorFiltroCliente option:selected').attr("id") : "")
                    break
                case "Hasta":
                    document.getElementById("hasta").value = ""
                    document.getElementById("limpiarFiltroHasta").style.visibility = 'hidden'
                    actualizarTablaUsuario(document.getElementById("desde").value, "", document.getElementById("hasta")
                        .value, $('.selectorFiltroCliente option:selected').attr("id") != 0 ? $(
                            '.selectorFiltroCliente option:selected').attr("id") : "")
                    break
                case "Cliente":
                    $(".selectorFiltroCliente").val('').trigger('change')
                    document.getElementById("limpiarFiltroCliente").style.display = 'none'
                    actualizarTablaUsuario(document.getElementById("desde").value, document.getElementById("hasta").value, "")
                    break
            }
        }

        $(document).ready(function () {
            $(".selectorFiltroCliente").select2({
                language: {
                    noResults: function () {
                        return "No se encontraron resultados";
                    }
                },
                placeholder: 'Seleccionar un cliente',
                dropdownCssClass: "custom-dropdown1",
                selectionCssClass: "custom-selection1"
            })
            $(".selectorFiltroCliente").on("change", function (e) {
                document.getElementById("limpiarFiltroCliente").style.display = 'inline'
                actualizarTablaUsuario(document.getElementById("desde").value, document.getElementById(
                    "hasta").value, $(this).find(
                        "option:selected").attr("id"))
            });
        });
    </script>
</body>

</html>