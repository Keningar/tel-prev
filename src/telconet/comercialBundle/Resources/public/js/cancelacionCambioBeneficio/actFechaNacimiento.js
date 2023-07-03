$(document).ready(function () {

    /**
     * Inicializa calendario
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 08-03-2021
     * @since 1.0
     */
    $('#fecha_nacimiento').datepicker({
        uiLibrary: 'bootstrap4',
        locale: 'es-es',
        dateFormat: 'yy-mm-dd'
    });

    $('#datetimepickerFechaNacimiento').datetimepicker({
        format: 'YYYY-MM-DD',
        icons: {
            time: 'fa fa-clock-o',
            date: 'fa fa-calendar',
            up: 'fa fa-chevron-up',
            down: 'fa fa-chevron-down',
            previous: 'fa fa-chevron-left',
            next: 'fa fa-chevron-right',
            today: 'fa fa-crosshairs',
            clear: 'fa fa-trash-o',
            close: 'fa fa-times'
        }
    });
    $('#identificacion_b').val(strIdentificacion);
    $('#infocontratoextratype_personaEmpresaRolId').val(intIdPersonaRol);
    $('#nombre_cliente').val(strNombreCliente);
    $('#fecha_nacimiento').val(strFechaNacimiento);
    $('#edad_cliente').val(strEdadCliente);
    $('#identificacion_cliente').val(strIdentificacion);
    $('#tipo_tributario_cliente').val(strTipoTributario);
    $('#tipo_identificacion_cliente').val(strTipoIdentificacion);

    if(strMsjValidaTipoTributario!='')
    {
        Ext.Msg.alert('Error', '' + strMsjValidaTipoTributario + '');
        $("#btConfirmarFeNacimiento").attr('disabled','disabled');
        $("#btActualizarFeNacimiento").attr('disabled','disabled');
    }
    
    $('#nombre_cliente').prop('disabled', 'disabled');
    $('#edad_cliente').prop('disabled', 'disabled');
    $('#identificacion_cliente').prop('disabled', 'disabled');
    $('#tipo_tributario_cliente').prop('disabled', 'disabled');
    $('#tipo_identificacion_cliente').prop('disabled', 'disabled');

    $(document).on('click', '.button-addon1', function (event) {
        var fechaActual = $.datepicker.formatDate('yy-mm-dd', new Date());
        var fechaNacimiento = $("#fecha_nacimiento").val();
        var fecha1 = moment(fechaNacimiento);
        var fecha2 = moment(fechaActual);
        var edadCalculada = fecha2.diff(fecha1, 'year');
        $("#edad_cliente").val(edadCalculada);
    });

    $("#datetimepickerFechaNacimiento").on("dp.change", function (e) {
        var fechaActual = $.datepicker.formatDate('yy-mm-dd', new Date());
        var fechaNacimiento = $("#fecha_nacimiento").val();
        var fecha1 = moment(fechaNacimiento);
        var fecha2 = moment(fechaActual);
        var edadCalculada = fecha2.diff(fecha1, 'year');
        $("#edad_cliente").val(edadCalculada);
    });

    $("#limpiar_formCliente").click(function () {
        limpiarFormCliente();
    });

    function limpiarFormCliente()
    {
        $('#identificacion_b').val("");
        $('#nombre_cliente').val("");
        $('#fecha_nacimiento').val("");
        $('#edad_cliente').val("");
        $('#identificacion_cliente').val("");
        $('#tipo_tributario_cliente').val("");
        $('#tipo_identificacion_cliente').val("");
    }

    $("#buscar_cliente").click(function () {
        Ext.MessageBox.wait('Cargando Datos del Cliente....');
        Ext.Ajax.request({
            url: url_buscarCliente,
            method: 'post',
            params: {strIdentificacion: $('#identificacion_b').val()},
            success: function (response) {
                Ext.MessageBox.hide();
                var objData = Ext.JSON.decode(response.responseText);
                var strStatus = objData.strStatus;
                var strMensaje = objData.strMensaje;
                var arraySolicitud = objData.arraySolicitud;
                if (strStatus != 'OK')
                {
                    Ext.Msg.alert('Error', '' + strMensaje + '');
                    $("#btConfirmarFeNacimiento").attr('disabled','disabled');
                    $("#btActualizarFeNacimiento").attr('disabled','disabled');
                    $('#nombre_cliente').val("");
                    $('#fecha_nacimiento').val("");
                    $('#edad_cliente').val("");
                    $('#identificacion_cliente').val("");
                    $('#tipo_tributario_cliente').val("");
                    $('#tipo_identificacion_cliente').val("");
                    $('#infocontratoextratype_personaEmpresaRolId').val("");
                } else
                {                    
                    $("#btConfirmarFeNacimiento").removeAttr("disabled");
                    $("#btActualizarFeNacimiento").removeAttr("disabled");
                    $('#nombre_cliente').val(arraySolicitud.nombreCliente);
                    $('#fecha_nacimiento').val(arraySolicitud.fechaNacimiento);
                    $('#edad_cliente').val(arraySolicitud.edad);
                    $('#identificacion_cliente').val(arraySolicitud.identificacion);
                    $('#tipo_tributario_cliente').val(arraySolicitud.tipoTributario);
                    $('#tipo_identificacion_cliente').val(arraySolicitud.tipoIdentificacion);
                    $('#infocontratoextratype_personaEmpresaRolId').val(arraySolicitud.idPersonaRol);
                }
            },
            failure: function (response)
            {
                Ext.Msg.alert('Error ', 'Error: ' + response.responseText);
            }
        });
    });

    $("#btActualizarFeNacimiento").unbind();
    $("#btActualizarFeNacimiento").click(function () {
        $('#identificacion_cliente').prop('disabled', false);          
        Ext.MessageBox.wait('Actualizando Datos del Cliente....');
        
        var formData = new FormData(document.getElementById("formulario"));
        
        $.ajax({           
            url:  url_actualizarFeNacimiento,
            type: 'post',
            data: formData,
            async:false,
            cache: false,
            contentType: false,
            processData: false,
            success: function (response) {
                Ext.MessageBox.hide();
                if (response.strStatus != 'OK')
                {
                    Ext.Msg.alert('Error', '' + response.strMensaje + '');             
                }
                else
                {
                    Ext.Msg.alert('Alert', '' +response.strMensaje);              
                    $('#modalActFechaNacimiento').modal("hide");   
                    if (strOpcion != 'Otro'){
                        location.reload();
                    }
                }
            },
            failure: function (response)
            {
                alert('Error: ' + response.responseText);
            }
        });
    });
    
    $("#btConfirmarFeNacimiento").unbind();
    $("#btConfirmarFeNacimiento").click(function () {
        $('#identificacion_cliente').prop('disabled', false);          
        Ext.MessageBox.wait('Confirmando fecha de nacimiento del cliente....');
        Ext.Ajax.request({
            url: url_confirmarFeNacimiento,
            method: 'post',
            params: {strIdentificacion: $('#identificacion_cliente').val(),
                     strFechaNacimiento: $('#fecha_nacimiento').val()},           
            success: function (response) {
                Ext.MessageBox.hide();
                var objData    = Ext.JSON.decode(response.responseText);
                var strStatus  = objData.strStatus;
                var strMensaje = objData.strMensaje;                
                if (strStatus != 'OK')
                {
                    Ext.Msg.alert('Error', '' + strMensaje + '');                   
                } else
                {
                    Ext.Msg.alert('Alert', '' + strMensaje + '');                   
                }
            },
            failure: function (response)
            {
                Ext.Msg.alert('Error ', 'Error: ' + response.responseText);
            }
        });
    });

});