function verificarPlanificacion(arrayParametros)
{
    Ext.MessageBox.wait("Verificando planificación...");										
                                        
    Ext.Ajax.request
    ({
        url: strUrlVerificarPlanifiacion,
        method: 'post',
        params: 
        { 
            cuadrilla: arrayParametros['cuadrillas'],
        },
        success: function(response)
        {
            var text = response.responseText;

            if(text === "OK")
            {
                verificarIntegrantesCuadrilla(arrayParametros);
            }
            else
            {
                if ( typeof win != 'undefined' && win != null )
                {
                    win.destroy();
                }
                
                Ext.MessageBox.hide();
                Ext.Msg.alert('Error', text); 
            }
        },
        failure: function(result)
        {
            if ( typeof win != 'undefined' && win != null )
            {
                win.destroy();
            }
            
            Ext.MessageBox.hide();
            Ext.Msg.alert('Error',result.responseText);
        }
    });
    
    return false; 
}

function verificarIntegrantesCuadrilla(arrayParametros)
{
    Ext.MessageBox.wait("Verificando datos...");										
                                        
    Ext.Ajax.request
    ({
        url: strUrlVerificarIntegrantesCuadrilla,
        method: 'post',
        params: 
        { 
            cuadrilla: arrayParametros['cuadrillas'],
            accion: arrayParametros['accion']
        },
        success: function(response)
        {
            var text = response.responseText;

            if(text === "OK")
            {
                cambioEstadoCuadrilla(arrayParametros);
            }
            else
            {
                if ( typeof win != 'undefined' && win != null )
                {
                    win.destroy();
                }
                
                Ext.MessageBox.hide();
                Ext.Msg.alert('Error', text); 
            }
        },
        failure: function(result)
        {
            if ( typeof win != 'undefined' && win != null )
            {
                win.destroy();
            }
            
            Ext.MessageBox.hide();
            Ext.Msg.alert('Error',result.responseText);
        }
    });
    
    return false;
}

function cambioEstadoCuadrilla(arrayParametros)
{
    Ext.Msg.confirm('Alerta','Está seguro que desea '+arrayParametros['accion']+' la cuadrilla seleccionada. Desea continuar?', function(btn)
    {
        if(btn=='yes')
        {
            connEsperaAccion.request
            ({
                url: strUrlCambioEstado,
                method: 'post',
                dataType: 'json',
                params:
                { 
                    cuadrillas : arrayParametros['cuadrillas'],
                    accion : arrayParametros['accion'],
                    coordinadorPrestado: arrayParametros['coordinadorPrestado'],
                    strFechaInicio     : arrayParametros["strFechaInicio"],
                    strFechaFin        : arrayParametros["strFechaFin"],
                    strHoraInicio      : arrayParametros["strHoraInicio"],
                    strHoraFin         : arrayParametros["strHoraFin"],
                    cmbTipoHorario1    : arrayParametros["cmbTipoHorario1"],
                    arrayDiaSemana     : JSON.stringify({dias:arrayParametros["comboDiaSemana1"]})
                },
                success: function(result)
                {
                    if( "OK" == result.responseText  )
                    {
                        if( arrayParametros['accion'] == "prestar" )
                        {
                            Ext.Msg.alert('Información', 'Se prestó la cuadrilla con éxito');
                        }
                        else if( arrayParametros['accion'] == "devolver" )
                        {
                            Ext.Msg.alert('Información', 'Se devolvió la cuadrilla con éxito');
                        }
                        else if(arrayParametros['accion'] == "recuperar" )
                        {
                            Ext.Msg.alert('Información', 'Se recupero la cuadrilla con éxito');
                        }
                        else
                        {
                            Ext.Msg.alert('Información', 'Se cambio el estado de la cuadrilla con éxito');
                        }
                        
                        if ( typeof win != 'undefined' && win != null )
                        {
                            win.destroy();
                        }

                        store.load();
                    }
                    else
                    {
                        Ext.Msg.alert('Error ', result.responseText);
                    }
                },
                failure: function(result)
                {
                    Ext.Msg.alert('Error ','Error: ' + result.statusText);
                }
            });
        }
    });
}

function enviarTramaPaquete(arrayParametros)
{
    Ext.MessageBox.wait("Verificando datos...");										

    console.log(JSON.stringify({dias:arrayParametros["comboDiaSemana1"]}));
    Ext.Ajax.request
    ({
        url: '../admi_cuadrilla/ajaxPlanificacionCuadrilla',
        method: 'post',
        params: 
        { 
            cuadrillas         : arrayParametros['cuadrillas'],
            accion             : arrayParametros['accion'],
            strFechaInicio   : arrayParametros['strFechaInicio'],
            strFechaFin      : arrayParametros['strFechaFin'],
            strHoraInicio    : arrayParametros['strHoraInicio'],
            strHoraFin       : arrayParametros['strHoraFin'],
            cmbTipoHorario1  : arrayParametros['cmbTipoHorario1'],
            comboDiaSemana1  : JSON.stringify({dias:arrayParametros["comboDiaSemana1"]}),
        },
        success: function(response)
        {
            var text = JSON.parse(response.responseText);

            if(text.status === "OK")
            {

                verificarIntegrantesCuadrilla(arrayParametros);
            }
            else
            {
                if ( typeof win != 'undefined' && win != null )
                {
                    win.destroy();
                }

                Ext.MessageBox.hide();
                Ext.Msg.alert('Error', text.mensaje);
            }
        },
        failure: function(result)
        {
            if ( typeof win != 'undefined' && win != null )
            {
                win.destroy();
            }
            
            Ext.MessageBox.hide();
            Ext.Msg.alert('Error',result.responseText);console.log('failure');
        }
    });
    
    return false;
}


function asignarHal()
{
    if (selModel.getSelection().length > 0)
    {
        presentarVentanaHal();
    }
    else
    {
        Ext.Msg.alert('Error ', 'Seleccione por lo menos un registro de la lista');
    }
}

function asignarSatelite() {
    if (selModel.getSelection().length > 0) {
        presentarVentanaSatelite();
    } else {
        Ext.Msg.alert('Error ', 'Seleccione por lo menos un registro de la lista');
    }
}

function presentarVentanaHal()
{
        btnguardar = Ext.create('Ext.Button', {
        text: 'Aceptar',
        cls: 'x-btn-rigth',
        handler: function()
        {
            var strValorHal         = Ext.getCmp('comboValorHal').value;
            win.destroy();
            procesarCuadrillaHal(strValorHal);
        }
        });

        btncancelar = Ext.create('Ext.Button', {
                    text: 'Cerrar',
                    cls: 'x-btn-rigth',
                    handler: function() {
                        win.destroy();
                    }
            });
    
        formPanel = Ext.create('Ext.form.Panel', {
            bodyPadding: 5,
            waitMsgTarget: true,
            layout: 'column',
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 100,
                msgTarget: 'side'
            },
            items:
            [
                {
                    xtype: 'fieldset',
                    title: '',
                    style: 'border: none;padding:0px',
                    autoHeight: true,
                    width: 300,
                    items:
                    [
                        {
                            xtype: 'fieldset',
                            title: 'Datos Hal',
                            width: '100%',
                            height: '100%',
                            margin: 0,
                            items:[
                                {
                                    displayField: 'valor_hal',
                                    valueField : 'valor_hal',
                                    xtype      : 'combobox',
                                    fieldLabel : 'Valor',
                                    id         : 'comboValorHal',
                                    width      :  200,
                                    name       : 'comboValorHal',
                                    value      : 'N',
                                    store      : [['N', 'No'],['S', 'Si']]
                                }
                            ]
                        }
                    ]
                }
            ]
        });

        win = Ext.create('Ext.window.Window', {
            title: "Asignar a HAL",
            closable: false,
            modal: true,
            width: 330,
            height: 200,
            resizable: false,
            layout: 'fit',
            items: [formPanel],
            buttonAlign: 'center',
            buttons:[btnguardar,btncancelar]
        }).show();
}

function presentarVentanaSatelite()
{
    btnguardar = Ext.create('Ext.Button', {
        text: 'Aceptar',
        cls: 'x-btn-rigth',
        handler: function()
        {
            var strValorSatelite         = Ext.getCmp('comboValorSatelite').value;
            win.destroy();
            procesarCuadrillaSatelite(strValorSatelite);
        }
    });

    btncancelar = Ext.create('Ext.Button', {
        text: 'Cerrar',
        cls: 'x-btn-rigth',
        handler: function() {
            win.destroy();
        }
    });

    formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 5,
        waitMsgTarget: true,
        layout: 'column',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 100,
            msgTarget: 'side'
        },
        items:
            [
                {
                    xtype: 'fieldset',
                    title: '',
                    style: 'border: none;padding:0px',
                    autoHeight: true,
                    width: 300,
                    items:
                        [
                            {
                                xtype: 'fieldset',
                                title: 'Datos Satélite',
                                width: '100%',
                                height: '100%',
                                margin: 0,
                                items:[
                                    {
                                        displayField: 'valor_satelite',
                                        valueField : 'valor_satelite',
                                        xtype      : 'combobox',
                                        fieldLabel : 'Valor',
                                        id         : 'comboValorSatelite',
                                        width      :  200,
                                        name       : 'comboValorSatelite',
                                        value      : 'N',
                                        store      : [['N', 'No'],['S', 'Si']]
                                    }
                                ]
                            }
                        ]
                }
            ]
    });

    win = Ext.create('Ext.window.Window', {
        title: "Asignar a Cuadrilla Satélite",
        closable: false,
        modal: true,
        width: 330,
        height: 200,
        resizable: false,
        layout: 'fit',
        items: [formPanel],
        buttonAlign: 'center',
        buttons:[btnguardar,btncancelar]
    }).show();
}


function procesarCuadrillaHal(strValorHal)
{
    var tramaCuadrillas = '';

    for (var i = 0; i < selModel.getSelection().length; ++i)
    {
        tramaCuadrillas = tramaCuadrillas + selModel.getSelection()[i].data.intIdCuadrilla;

        if (i < (selModel.getSelection().length - 1))
        {
            tramaCuadrillas = tramaCuadrillas + '|';
        }
    }

    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.get(document.body).mask('Loading...');
                },
                scope: this
            },
            'requestcomplete': {
                fn: function (con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function (con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    });
    
    var strMensajeAlert = "Se asignarán las cuadrillas a HAL.";
    if (strValorHal == 'N')
    {
        strMensajeAlert = "Se quitarán las cuadrillas de HAL.";
    }

    Ext.Msg.confirm('Alerta', strMensajeAlert+' Desea continuar?', function(btn) {
        if (btn == 'yes') {
            conn.request({
                url: url_AsignaAHal,
                method: 'post',
                params: {tramaCruadrillas: tramaCuadrillas,valorHal: strValorHal},
                success: function(response) {
                    var json = Ext.JSON.decode(response.responseText);
                    if (json.estado == "Ok") {
                        Ext.Msg.alert('Alerta', json.mensaje);
                        store.load();
                    }
                    else {
                        Ext.Msg.alert('Error ', json.mensaje);
                        store.load();
                    }
                },
                failure: function(result)
                {
                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                    store.load();
                }
            });
        }
    });
}

function procesarCuadrillaSatelite(strValorSatelite) {
    var tramaCuadrillas = '';

    for (var i = 0; i < selModel.getSelection().length; ++i) {
        tramaCuadrillas = tramaCuadrillas + selModel.getSelection()[i].data.intIdCuadrilla;

        if (i < (selModel.getSelection().length - 1)) {
            tramaCuadrillas = tramaCuadrillas + '|';
        }
    }

    var strMensajeAlert = "Se asignarán las cuadrillas a Cuadrillas Satélites.";
    if (strValorSatelite == 'N') {
        strMensajeAlert = "Se quitarán las cuadrillas de Cuadrillas Satélites.";
    }

    Ext.Msg.confirm('Alerta', strMensajeAlert + ' Desea continuar?', function (btn) {
        if (btn == 'yes') {
            connEsperaAccion.request({
                url: url_AsignaASatelite,
                method: 'post',
                params: {tramaCruadrillas: tramaCuadrillas, valorSatelite: strValorSatelite},
                success: function (response) {
                    var json = Ext.JSON.decode(response.responseText);
                    if (json.estado == "Ok") {
                        Ext.Msg.alert('Alerta', json.mensaje);
                        store.load();
                    }
                    else {
                        Ext.Msg.alert('Error ', json.mensaje);
                        store.load();
                    }
                },
                failure: function (result) {
                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                    store.load();
                }
            });
        }
    });
}

function administrarCuadrillaLibre(arrayParametros)
{                           
    var strMensaje      =  'Está seguro que desea dejar '+arrayParametros['strEstadoAccionARealizarCuadrilla'];
        strMensaje      += ' a la cuadrilla '+arrayParametros['strNombreCuadrilla']+'?';
        
    var strUrlAEjecutar = '';
    if(arrayParametros['strEstadoAccionARealizarCuadrilla']=="LIBRE")
    {
        strUrlAEjecutar = strUrlLiberarCuadrilla;
    }
    else
    {
        strUrlAEjecutar = strUrlReactivarCuadrillaLibre;
    }
    Ext.Msg.confirm('Alerta',strMensaje, function(btn)
    {
        if(btn=='yes')
        {
            connEsperaAccion.request
            ({
                url: strUrlAEjecutar,
                method: 'post',
                dataType: 'json',
                params:
                { 
                    intIdCuadrilla: arrayParametros["intIdCuadrilla"]
                },
                success: function(result)
                {
                    var strResult = result.responseText;
                    
                    Ext.Msg.alert('Información',result.responseText);
                    if ( typeof win != 'undefined' && win != null )
                    {
                        win.destroy();
                    }

                    if( strResult=="OK" )
                    {
                        var strMensajeAccionAEjecutar = 'Se dejó '+arrayParametros["strEstadoAccionARealizarCuadrilla"]+' a la cuadrilla ';
                            strMensajeAccionAEjecutar+= arrayParametros["strNombreCuadrilla"]+' ';

                       Ext.Msg.alert('Información ', strMensajeAccionAEjecutar);
                       store.load();
                    }
                    else
                    {
                        var strMensajeError='';
                        strMensajeError+=strResult;
                        Ext.Msg.alert('Error ', strMensajeError);

                    }
                },
                failure: function(result)
                {
                    Ext.Msg.alert('Error ','Error: ' + result.statusText);
                }
            });
        }
    });
    
}
