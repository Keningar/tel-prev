    $(document).ready(function ()
    {
        var boolPermisoConsultar  = (typeof boolConsultar === 'undefined')  ? false : (boolConsultar ? true : false);
        if(boolPermisoConsultar)
        {
            var objListado = $('#tabla').DataTable({
                "ajax": {
                    "url": url_grid,
                    "type": "POST",
                    beforeSend: function()
                    {
                        Ext.get(document.body).mask('Cargando Información.');
                    },
                    complete: function() 
                    {
                        Ext.get(document.body).unmask();
                    },
                    "data": function (param) {
                        param.strIdentificacion = $("#strIdentificacion_buscar").val();
                        param.strRazonSocial    = $("#strRazonSocial_buscar").val();
                    }
                },
                "language": {
                    "oPaginate": {
                        "sPrevious": "Anterior",
                        "sNext": "Siguiente"
                    },
                    "sProcessing": "Procesando...",
                    "lengthMenu": "Muestra _MENU_ filas por página",
                    "zeroRecords": "No hay información disponible",
                    "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "infoEmpty": "No hay información disponible",
                    "infoFiltered": "(filtrado de _MAX_ total filas)",
                    "search": "Buscar:",
                    "processing": true,
                    "loadingRecords": "Cargando datos..."
                },
                "columns": [
                        {"data": "intIdPersona"},
                        {"data": "strIdentificacion"},
                        {"data": "strRazonSocial"},
                        {"data": "strUsrVendedor"},
                        {"data": "strDireccion"},
                        {"data": "strFechaCreacion"},
                        {"data": "strFechaUltEmision"},
                        {"data": "strSaldoPendiente"},
                        {"data": "strEstado"},
                        {"data": "strRol"},
                        {"data": "strPropuestaCRM"}
                ],
                'columnDefs': [{
                    'targets': 0,
                    'searchable': false,
                    'orderable': false,
                    'render': function (data) {
                        return '<input type="checkbox" name="id[]" value="' + $('<div/>').text(data).html() + '">';
                    }
                }]
            });
        }

        $('#objListado-select-all').on('click', function () {
            var rows = objListado.rows({'search': 'applied'}).nodes();
            $('input[type="checkbox"]', rows).prop('checked', this.checked);
        });

        $('#tabla tbody').on('change', 'input[type="checkbox"]', function () {
            if (!this.checked) 
            {
                var el = $('#objListado-select-all').get(0);
                if (el && el.checked && ('indeterminate' in el)) 
                {
                    el.indeterminate = true;
                }
            }
        });


        $("#buscar").click(function () {
            $('#tabla').DataTable().ajax.reload();
        });

        $("#limpiar").click(function () {
            limpiarFormBuscar();
        });

        /**
         * Documentación para la función 'limpiarFormBuscar'.
         *
         * Función encargada de limpiar los campos.
         *
         * @author Kevin Baque Puya <kbaque@telconet.ec>
         * @version 1.0 11-05-2021
         *
         */
        function limpiarFormBuscar()
        {
            $('#strIdentificacion_buscar').val("");
            $('#strRazonSocial_buscar').val("");
        }

        $('form').keypress(function (e) {
            if (e === 13) {
                return false;
            }
        });

        $('input').keypress(function (e) {
            if (e.which === 13) {
                return false;
            }
        });
    });