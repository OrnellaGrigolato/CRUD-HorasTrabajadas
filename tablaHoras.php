<?php
include 'conexion_mssql.php';
?>

<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Alertify -->
    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>
    <!-- include the style Alertify-->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/alertify.min.css" />
    <!-- include a theme Alertify-->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/themes/default.min.css" />
    <!-- Font Awesome (icons) -->
    <script src="https://kit.fontawesome.com/c154323b6c.js" crossorigin="anonymous"></script>
    <!-- Select2 (select component) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js " crossorigin="anonymous">
    </script>

    <title>Horas</title>

    <style>
    .modiv-footer {
        display: flex;
        justify-content: end;
        gap: 1rem;
        margin: .5rem;
    }

    .col-6 {
        display: flex;
        flex-direction: row;
        gap: 2rem;

    }

    .col-6 .input-group {
        flex-wrap: nowrap;
    }

    .empty {
        color: #aaaaaa;
    }

    option {
        color: black
    }

    #tipo {
        border-radius: 4px;
        padding: 3px 7px;
        appearance: none;
        background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23131313%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E");
        background-repeat: no-repeat;
        background-position: right 0.7rem top 50%;
        background-size: 0.45rem auto;
        outline-width: 1px;
        outline-color: #aaaaaa
    }

    #tipo:focus {
        outline-color: #aaaaaa !important;
    }

    /* Styles for select modal component (have to use !important to override them) */
    .modal-body .select2-selection__rendered {
        line-height: 33px !important;
    }

    .modal-body .select2-container .select2-selection--single {
        height: 37px !important;
    }

    .modal-body .select2-selection__arrow {
        height: 36px !important;
    }
    </style>
</head>

<body>
    <!-- Tabla Horas -->
    <table class="table table-head-fixed text-nowrap table-hover" id="tablaHoras">
        <thead>
            <th>Cliente</th>
            <th>Tipo</th>
            <th>Inicio</th>
            <th class="columnaCentrada">Horas</th>
            <th>Comentario</th>
        </thead>
        <tbody>
            <?php
            $filtroFechaDesde = trim($_POST['filtroDesde']);
            $filtroFechaHasta = trim($_POST['filtroHasta']);
            $filtroCliente = trim($_POST['filtroCliente']);
            $totalHoras = 0;
            // Reemplazar con el id del usuario traído del login
            $idUsuario = 2;

            $fechaFormateada = null;

            $query = "SELECT 
            h.ID_Hora,
            h.ID_Vendedor,
            h.ID_Cliente,
            c.RazonSocial AS NombreCliente,
            h.Horas,
            h.Comentario,
            h.ID_TipoHora,
            h.FechaInicio
            FROM 
                CRM_Horas h
            JOIN 
                CRM_CLIENTES c ON h.ID_Cliente = c.ID_Cliente
            WHERE 
                h.ID_Vendedor = ?";

            $params = [$idUsuario];

            if ($filtroFechaDesde != "" && $filtroFechaHasta != "") {
                $fechaDesde = DateTime::createFromFormat('Y-m-d', $filtroFechaDesde)->format('Y-m-d');
                $fechaHasta = DateTime::createFromFormat('Y-m-d', $filtroFechaHasta)->format('Y-m-d');
                $query .= " AND CAST(h.FechaInicio AS DATE) BETWEEN ? AND ?";
                $params[] = $fechaDesde;
                $params[] = $fechaHasta;
            } elseif ($filtroFechaDesde != "") {
                $fechaDesde = DateTime::createFromFormat('Y-m-d', $filtroFechaDesde)->format('Y-m-d');
                $query .= " AND CAST(h.FechaInicio AS DATE) >= ?";
                $params[] = $fechaDesde;
            } elseif ($filtroFechaHasta != "") {
                $fechaHasta = DateTime::createFromFormat('Y-m-d', $filtroFechaHasta)->format('Y-m-d');
                $query .= " AND CAST(h.FechaInicio AS DATE) <= ?";
                $params[] = $fechaHasta;
            } else {
                $fechaDeHoy = date("Y-m-d");
                $query .= " AND CAST(h.FechaInicio AS DATE) = ?";
                $params[] = $fechaDeHoy;
            }

            if ($filtroCliente != "") {
                $query .= " AND h.ID_Cliente = ?";
                $params[] = $filtroCliente;
            }

            $stmt = sqlsrv_prepare($conn, $query, $params);

            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            }

            $ressqlHoras = sqlsrv_execute($stmt);

            if ($ressqlHoras === false) {
                die(print_r(sqlsrv_errors(), true));
            }
            if (!sqlsrv_has_rows($stmt)) {

                if ($filtroFechaDesde === "" && $filtroFechaHasta === "" && $filtroCliente === "") {
                    ?>

            <tr>
                <td colspan="7" class="text-center">
                    Aún no has cargado ninguna hora trabajada en el día de la fecha.
                </td>
            </tr>
            <?php
                } else if ($filtroCliente === "" && ($filtroFechaDesde != "" || $filtroFechaHasta != "")) {
                    $hastaEsMenorQueDesde = DateTime::createFromFormat('Y-m-d', $filtroFechaHasta) < DateTime::createFromFormat('Y-m-d', $filtroFechaDesde);
                    $mayoresAHoy = DateTime::createFromFormat('Y-m-d', $filtroFechaDesde) > DateTime::createFromFormat('Y-m-d', date("Y-m-d"));
                    if ($mayoresAHoy) {
                        ?>
            <tr>
                <td colspan="7" class="text-center">
                    El valor del filtro 'desde' no puede ser mayor a la fecha de hoy.
                </td>
            </tr>

            <?php
                    } else if ($hastaEsMenorQueDesde) {
                        ?>
            <tr>
                <td colspan="7" class="text-center">
                    El valor del filtro 'hasta' no puede ser anterior al del filtro 'desde'.
                </td>
            </tr>

            <?php
                    } else {
                        ?>
            <tr>
                <td colspan="7" class="text-center">
                    No se encontraron horas trabajadas para el rango de fechas seleccionado.
                </td>
            </tr>

            <?php
                    }
                } else if ($filtroCliente !== "" && $filtroFechaDesde === "" && $filtroFechaHasta === "") {
                    ?>
            <tr>
                <td colspan="7" class="text-center">
                    No se encontraron horas trabajadas para el día de la fecha para el cliente seleccionado.
                </td>
            </tr>
            <?php
                } else {
                    ?>
            <tr>
                <td colspan="7" class="text-center">
                    No se encontraron horas trabajadas para el rango de fechas seleccionado y el cliente
                    seleccionado.
                </td>
            </tr>
            <?php
                }
            }
            while ($listaHoras = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $totalHoras += $listaHoras['Horas'];
                ?>
            <tr>
                <td><?php echo $listaHoras['NombreCliente']; ?></td>
                <td><?php echo $listaHoras['ID_TipoHora'] === "FAC" ? "Facturable" : "Abonado"; ?></td>
                <td><?php echo date_format($listaHoras['FechaInicio'], "Y/m/d H:i:s"); ?></td>
                <td class="columnaCentrada"><?php echo $listaHoras['Horas']; ?></td>
                <td style="width: 60%;text-overflow:ellipsis;overflow:hidden; max-width: 400px; ">
                    <?php echo $listaHoras['Comentario']; ?>
                </td>
                <?php
                    $onclickEdit = sprintf(
                        "modalParaEditarHora('%s','%s','%s','%s','%s','%s')",
                        $listaHoras['ID_Hora'],
                        $listaHoras['Horas'],
                        $listaHoras['Comentario'],
                        $listaHoras['ID_TipoHora'],
                        $listaHoras['NombreCliente'],
                        date_format($listaHoras['FechaInicio'], 'Y-m-d H:i:s')
                    );
                    $onclickDelete = sprintf(
                        "borrarHora('%s')",
                        $listaHoras['ID_Hora']
                    );
                    ?>
                <td>
                    <a href="#" class="fas fa-edit" onclick="<?php echo $onclickEdit; ?>"></a>
                </td>
                <td>
                    <a href="#" class="fa-solid fa-trash-can" onclick="<?php echo $onclickDelete; ?>"></a>
                </td>
            </tr>
            <?php
            }
            ?>
        <tfoot>
            <tr>
                <td colspan="7" class="text-center">
                    <a href="#" class="fa-solid fa-plus" style="color: #bababa;text-decoration: none;"
                        onclick="modalParaAgregarHora()"></a>
                </td>
            </tr>
        </tfoot>
        </tbody>
    </table>
    <input type="hidden" id="totalHoras" value="<?php echo $totalHoras; ?>">
    <!-- Modal -->
    <div class="modal fade" role="dialog" id="infoHora" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="modal-title">Actualizar Información de la Hora</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <input type="text" class="form-control" id="infoIdHora" hidden>
                        <div class="row">
                            <div class="col">
                                <div class="input-group mb-3" style="display: flex;flex-wrap:nowrap">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text font-weight-bold">Cliente</span>
                                    </div>

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

                                    <select id="cliente" class="select" style="heigth:181%;">
                                        <option id=0 value='' selected="true">Seleccionar un Cliente</option>
                                        <?php foreach ($list as $row): ?>
                                        <option id=<?= $row["id_cliente"] ?>><?= $row["RazonSocial"] ?></option>
                                        <?php endforeach ?>
                                    </select>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text font-weight-bold" style="width:56px">Tipo
                                        </span>
                                    </div>
                                    <select id="tipo" style="width: calc(100% - 56px);">
                                        <option value="" selected disabled hidden>Seleccionar un tipo
                                        </option>
                                        <option value="ABO">Abonado</option>
                                        <option value="FAC">Facturable</option>
                                    </select>
                                </div>

                            </div>
                        </div>
                        <div class="col">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-weight-bold">Fecha y Hora de Inicio</span>
                                </div>
                                <input type="datetime-local" class="form-control" id="fechaYHora">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-weight-bold">Cantidad de
                                        Horas
                                    </span>
                                </div>
                                <input type="number" class="form-control" id="cantidad" style="width: 50px;"
                                    placeholder="1">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-weight-bold">Comentario</span>
                                </div>
                                <textarea type="text" class="form-control" id="comentario"
                                    placeholder="Se realizó el ticket #123" style="field-sizing: content;"></textarea>
                            </div>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="modiv-footer">
                    <button type="button" class="btn btn-secondary" id="btnCerrarModal"
                        data-dismiss="modal">Cerrar</button>
                    <button id="btnConfirmarEdicion" type="button" data-dismiss="modal"
                        class="btn btn-primary">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    function modalParaAgregarHora() {
        resetValues()
        const valorFiltroCliente = $('.selectorFiltroCliente option:selected').attr("id") != 0 ? $(
            '.selectorFiltroCliente option:selected').attr("id") : ""
        $(document).on("click", "#btnConfirmarEdicion", function() {
            const idHora = $('#infoIdHora').val();
            const cantidad = $('#cantidad').val();
            const comentario = $('#comentario').val();
            const fechaDeInicio = formatDateToSQL(new Date($('#fechaYHora').val().replace("T", " ") + ":00"))
            const tipo = $('#tipo').val();
            const cliente = $('#cliente option:selected').attr('id');
            const fechaCarga = formatDateToSQL(new Date())
            const hayDatosInvalidos = validarDatos(cantidad, $('#fechaYHora').val().replace("T", " ") + ":00",
                $('#cliente')
                .val(), tipo,
                comentario)
            if (!hayDatosInvalidos) {
                alertify.confirm('Confirmar Acción',
                    'Está Seguro de Agregar este Nuevo Registro de Horas Trabajadas?',
                    function() {
                        $.ajax({
                            async: true,
                            type: "POST",
                            data: {
                                idHora,
                                cantidad,
                                comentario,
                                fechaDeInicio,
                                tipo,
                                cliente,
                                fechaCarga
                            },
                            url: "./services/nuevaHora.php",
                            success: function(r) {
                                var dataResult = JSON.parse(r);
                                if (dataResult.status == 200) {
                                    resetValues();
                                    actualizarTablaUsuario(document.getElementById("desde")
                                        .value, document.getElementById("hasta").value,
                                        valorFiltroCliente);
                                } else {
                                    if (dataResult.status == 201) {
                                        alertify.set('notifier', 'position', 'top-center');
                                        alertify.error(
                                            'Hubo un error al agregar el nuevo registro: ' +
                                            dataResult
                                            .error[0][2]);
                                    }
                                }
                            },
                        });
                    },
                    function() {}).set('labels', {
                    ok: 'Confirmar',
                    cancel: 'Cancelar'
                });
            } else {
                hayDatosInvalidos.forEach(error => {
                    alertify.alert("Error", error);
                });
            }
        })
        document.getElementById('modal-title').innerHTML = "Crear Nuevo Registro de Horas Trabajadas";
        console.log(document.getElementById("desde").value);
        if (document.getElementById("desde").value != "" && document
            .getElementById("hasta").value != "") {
            $(".modal-body #fechaYHora").val(document
                .getElementById("hasta").value + " " + "00:00")
        } else if (document.getElementById("desde").value != "" && document
            .getElementById("hasta").value === "") {
            $(".modal-body #fechaYHora").val(document
                .getElementById("desde").value + " " + "00:00")
        } else if (document.getElementById("desde").value === "" && document
            .getElementById("hasta").value != "") {
            $(".modal-body #fechaYHora").val(document
                .getElementById("hasta").value + " " + "00:00")
        } else {
            const arrayDeHoy = new Date().toLocaleString("en-US", {
                timeZone: "America/Argentina/Buenos_Aires"
            }).replace(",", "").replaceAll("/", "-").replace(" PM", "").split(" ")[0].split("-")


            const fecha = arrayDeHoy[1] + "-" + arrayDeHoy[0] + "-" + arrayDeHoy[2]

            const time = new Date().toLocaleString("en-US", {
                timeZone: "America/Argentina/Buenos_Aires"
            }).replace(",", "").replaceAll("/", "-").replace(" PM", "").split(" ")[1]


            $(".modal-body #fechaYHora").val(formatDate(fecha.split("-").reverse()
                .join(
                    "-") + " " + time));
        }
        $('#infoHora').modal('show');
    }

    function modalParaEditarHora(a, b, c, d, e, f, g) {
        const valorFiltroCliente = $('.selectorFiltroCliente option:selected').attr("id") != 0 ? $(
            '.selectorFiltroCliente option:selected').attr("id") : ""
        resetValues()
        const horaId = a;
        const cantidadDeHoras = b;
        const comentario = c;
        const tipo = d;
        const cliente = e;
        const fechaInicio = f;

        $(".modal-body #infoIdHora").val(horaId);
        $(".modal-body #fechaYHora").val(fechaInicio);
        $(".modal-body #cantidad").val(cantidadDeHoras);
        $(".modal-body #cantidad")[0].placeholder = cantidadDeHoras
        $(".modal-body #comentario").val(comentario);
        $(".modal-body .select").val(cliente).trigger('change')
        $(".modal-body #tipo").val(tipo);
        document.getElementById('modal-title').innerHTML = "Actualizar Información de la Hora"
        $('#infoHora').modal('show');
        $("#tipo").removeClass("empty");
        $(document).on("click", "#btnConfirmarEdicion", function() {
            const idHora = $('#infoIdHora').val();
            const cantidad = $('#cantidad').val();
            const comentario = $('#comentario').val();
            const fechaDeInicio = formatDateToSQL(new Date($('#fechaYHora').val().replace("T", " ") + ":00"))
            const tipo = $('#tipo').val();
            const cliente = $('#cliente option:selected').attr('id');
            const hayDatosInvalidos = validarDatos(cantidad, fechaDeInicio, $('#cliente').val(), tipo,
                comentario)
            if (!hayDatosInvalidos) {
                alertify.confirm('Confirmar Acción',
                    'Está Seguro de Modificar este registro de Horas Trabajadas?',
                    function() {
                        $.ajax({
                            async: true,
                            type: "POST",
                            data: {
                                idHora,
                                cantidad,
                                comentario,
                                fechaDeInicio,
                                tipo,
                                cliente
                            },
                            url: "./services/modificarHora.php",
                            success: function(r) {
                                var dataResult = JSON.parse(r);
                                if (dataResult.status == 200) {
                                    resetValues();
                                    actualizarTablaUsuario(document.getElementById("desde")
                                        .value, document.getElementById("hasta").value,
                                        valorFiltroCliente);
                                    $("#tipo").addClass("empty");
                                } else {
                                    if (dataResult.status == 201) {
                                        alertify.set('notifier', 'position', 'top-center');
                                        alertify.error('Hubo un error en el UPDATE : ' +
                                            dataResult
                                            .error[0][2]);
                                    }
                                }
                            },
                        });
                    },
                    function() {}).set('labels', {
                    ok: 'Confirmar',
                    cancel: 'Cancelar'
                });
            } else {
                hayDatosInvalidos.forEach(error => {
                    alertify.alert("Error", error);
                });
            }
        })

    }

    function borrarHora(horaId) {
        const valorFiltroCliente = $('.selectorFiltroCliente option:selected').attr("id") != 0 ? $(
            '.selectorFiltroCliente option:selected').attr("id") : ""
        alertify.confirm('Confirmar Acción', 'Está Seguro de Borrar este registro de Horas Trabajadas?',
            function() {
                $.ajax({
                    async: true,
                    type: "POST",
                    data: {
                        horaId
                    },
                    url: "./services/borrarHora.php",
                    success: function(r) {
                        var dataResult = JSON.parse(r);
                        if (dataResult.status == 200) {
                            actualizarTablaUsuario(document.getElementById("desde").value, document
                                .getElementById("hasta").value, valorFiltroCliente);
                        } else {
                            if (dataResult.status == 201) {
                                alertify.set('notifier', 'position', 'top-center');
                                alertify.error('Hubo un error al BORRAR el registro: ' + dataResult
                                    .error[0][2]);
                            } else {
                                alertify.set('notifier', 'position', 'top-center');
                                alertify.warning('El registro no existe');
                            }
                        }
                    },
                });
            },
            function() {}).set('labels', {
            ok: 'Confirmar',
            cancel: 'Cancelar'
        });

    }

    $(document).on("click", "#btnCerrarModal", function() {
        $("#infoHora").modal("hide");
    })

    function resetValues() {
        $("#infoHora").modal("hide");
        $(".modal-body #infoIdHora").val();
        $(".modal-body #fechaYHora").val("");
        $(".modal-body #cantidad").val(0);
        $(".modal-body #cantidad")[0].placeholder = "1"
        $(".modal-body #comentario").val("");
        $(".modal-body #cliente").val("");
        $(".select").val('').trigger('change')
        $(".modal-body #tipo").val("");
    }

    function validarDatos(cantidad, fechaDeInicio, cliente, tipo, comentario) {
        const errores = [];
        if (cliente == "" || comentario == "" || tipo == null) {
            errores.push("Hay campos vacíos.");
        }

        if (Number.parseFloat(cantidad) <= 0 || isNaN(Number.parseFloat(cantidad))) {
            errores.push("La cantidad debe ser un número válido y mayor que cero.");
        }

        const fechaInicio = new Date(fechaDeInicio);
        if (fechaInicio == "Invalid Date" || fechaInicio > new Date()) {
            errores.push("La fecha de inicio no es válida o es posterior a la fecha actual.");
        }

        return errores.length > 0 ? errores : null;
    }

    function padZero(number) {
        return number.toString().padStart(2, '0');
    }

    function formatDate(dateString) {
        const [datePart, timePart] = dateString.split(' ');
        const [year, month, day] = datePart.split('-');
        const [hour, minute, second] = timePart.split(':');

        const formattedDate = `${year}-${padZero(month)}-${padZero(day)}`;
        const formattedTime = `${padZero(hour)}:${padZero(minute)}:${padZero(second)}`;

        return `${formattedDate} ${formattedTime}`;
    }
    $(document).ready(function() {
        $(".select").select2({
            dropdownParent: $('.modal-body'),
            language: {
                noResults: function() {
                    return "No se encontraron resultados";
                }
            },
            placeholder: 'Seleccionar un cliente',
            width: '100%',
            dropdownCssClass: "custom-dropdown2",
            selectionCssClass: "custom-selection2"
        });
    });

    $(document).ready(function() {
        if ($("#tipo").val() === null) {
            $("#tipo").addClass("empty");
        } else {
            $("#tipo").removeClass("empty");
        }
        $("#tipo").change(function() {
            if ($(this).val() === null) {
                $(this).addClass("empty");
            } else {
                $(this).removeClass("empty");
            }
        });
        $("#tipo").trigger("change");
    });


    function formatDateToSQL(date) {
        // Asegúrate de que la fecha sea un objeto Date
        if (!(date instanceof Date)) {
            date = new Date(date);
        }

        // Obtener los componentes de la fecha
        const year = date.getFullYear();
        const month = ('0' + (date.getMonth() + 1)).slice(-2); // Los meses comienzan en 0
        const day = ('0' + date.getDate()).slice(-2);
        const hours = ('0' + date.getHours()).slice(-2);
        const minutes = ('0' + date.getMinutes()).slice(-2);
        const seconds = ('0' + date.getSeconds()).slice(-2);

        // Formatear la fecha en el formato SQL
        const sqlDate = `${year}-${month}-${day}T${hours}:${minutes}:${seconds}`;
        return sqlDate;
    }
    </script>

</body>

</html>