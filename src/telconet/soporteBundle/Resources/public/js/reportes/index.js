function validar_fecha_mayor(fechaInicial,fechaFinal)
{
    valuesStart=fechaInicial.split("-");
    valuesEnd=fechaFinal.split("-");

    // Verificamos que la fecha no sea posterior a la actual
    var dateStart=new Date(valuesStart[2],(valuesStart[1]-1),valuesStart[0]);
    var dateEnd=new Date(valuesEnd[2],(valuesEnd[1]-1),valuesEnd[0]);
    if(dateStart>dateEnd)
    {
        return 1;
    }
    return 0;
}


function validar_dias_entre_meses(fechaInicial,fechaFinal)
{
    valuesStart=fechaInicial.split("-");
    valuesEnd=fechaFinal.split("-");

    // Verificamos que los dias entre las fechas no sea mayor a 30 dias
    var dateStart=new Date(valuesStart[2],(valuesStart[1]-1),valuesStart[0]);
    var dateEnd=new Date(valuesEnd[2],(valuesEnd[1]-1),valuesEnd[0]);

    var fechaDesdeP = dateStart.getTime();
    var fechaHastaP = dateEnd.getTime();


    var differenceP = Math.abs(fechaDesdeP - fechaHastaP)

    //Convierto de milisegundos a dias
    var diasP = differenceP/86400000;

    if(diasP >30)
    {
        return 1;
    }

    return 0;
}


var connGenerarReporteTareas = new Ext.data.Connection({
    listeners: {
        'beforerequest': {
            fn: function(con, opt) {
                Ext.MessageBox.show({
                    msg: 'Generando el archivo csv',
                    progressText: 'Guardando...',
                    width: 300,
                    wait: true,
                    waitConfig: {interval: 200}
                });
            },
            scope: this
        },
        'requestcomplete': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
            },
            scope: this
        },
        'requestexception': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
            },
            scope: this
        }
    }
});


function generarReporteTareasTrace(fechaInicial,fechaFinal)
{

    connGenerarReporteTareas.request({
        url: urlGenerarReporteTareasTrace,
        method: 'post',
        timeout: 7200000,
        params:
            {
                fechaInicio : fechaInicial,
                fechaFin    : fechaFinal
            },
        success: function(response){

            var text = Ext.decode(response.responseText);
            Ext.Msg.show({
                title: 'Información',
                msg: text.respuesta,
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.INFO
            });

        },
        failure: function(result) {
            Ext.Msg.show({
                title: 'Error',
                msg: result.statusText,
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR
            });
        }
    });
}

function validarFormulario()
{
    var fechaInicio  = Ext.getCmp('fe_inicio').getValues().fe_inicio_value;
    var fechaFin     = Ext.getCmp('fe_fin').getValues().fe_fin_value;
    var esMayor      = 0;
    var maximoDias   = 0;

    if(fechaInicio != "" && fechaFin != "")
    {
        //Validar que la Fecha Inicio sea mayor que la Fecha Fin
        esMayor = validar_fecha_mayor(fechaInicio,fechaFin);

        if(esMayor == 1)
        {
            Ext.Msg.show({
                title  :'Error en Búsqueda',
                msg    : 'Por Favor la Fecha Inicio no puede ser mayor que la Fecha Fin',
                buttons: Ext.Msg.OK,
                icon   : Ext.MessageBox.ERROR
            });
            return false;
        }

        //validar dias entre fechas
        maximoDias = validar_dias_entre_meses(fechaInicio,fechaFin);

        if(maximoDias == 1)
        {
            Ext.Msg.show({
            title  :'Error en Búsqueda',
            msg    : 'Por Favor solo se puede generar reportes de hasta 31 dias de diferencia entre la Fecha Inicio y Fin',
            buttons: Ext.Msg.OK,
            icon   : Ext.MessageBox.ERROR
            });

            return false;
        }

        generarReporteTareasTrace(fechaInicio,fechaFin);

    }
    else
    {
        Ext.Msg.show({
            title  :'Error en Búsqueda',
            msg    : 'Por Favor seleccionar una Fecha Inicio y Fecha Fin',
            buttons: Ext.Msg.OK,
            icon   : Ext.MessageBox.ERROR
        });

        return false;
    }
}


Ext.onReady(function()
{
    fecha = Ext.create('Ext.form.Panel',
    {
        renderTo: 'div_fe_inicio',
        id: 'fe_inicio',
        name: 'fe_inicio',
        width: 144,
        frame: false,
        bodyPadding: 0,
        height: 30,
        border: 0,
        margin: 0,
        items:
        [{
            xtype: 'datefield',
            id: 'fe_inicio_value',
            name: 'fe_inicio_value',
            editable: false,
            anchor: '100%',
            format: 'd-m-Y',
            value: '',
            minValue: ''
        }]
    });

    fecha = Ext.create('Ext.form.Panel',
    {
        renderTo: 'div_fe_fin',
        id: 'fe_fin',
        name: 'fe_fin',
        width: 144,
        frame: false,
        bodyPadding: 0,
        height: 30,
        border: 0,
        margin: 0,
        items: 
        [{
            xtype: 'datefield',
            id: 'fe_fin_value',
            name: 'fe_fin_value',
            editable: false,
            anchor: '100%',
            format: 'd-m-Y',
            value: '',
            minValue: ''
        }]
    });

});