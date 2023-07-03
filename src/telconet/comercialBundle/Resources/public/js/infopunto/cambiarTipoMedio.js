var jsonCaracteristicas;
var precioUnitario;
var precioNegociacion;
var precioVenta;
var descripcion;
var idProducto;

function cambiarTipoMedio(idServicio, backup, ultimaMillaPrincipal, tipoMedio, tipoEnlace, coordenadas, cliente)
{
    Ext.MessageBox.wait('Consultando datos. Favor espere..');
    Ext.Ajax.request({
        url: urlGetInformacionBackup,
        method: 'post',
        timeout: 400000,
        params:
            {
                idServicio: idServicio,
                idServicioBackup : backup
            },
        success: function(response)
        {
            Ext.MessageBox.hide();
            
            var datos = Ext.JSON.decode(response.responseText);
            
            var boolEsConcentrador = (datos.esConcentrador==='SI'?true:false);
            idProducto = datos.idProducto;
            
            var titulo    = '<b><label>Información del Servicio </label><b>';
            var tituloSeleccionar = '<b><label>Seleccione el servicio </label><b>';
                        
            storeUltimaMilla = new Ext.data.Store({
                total: 'total',
                autoLoad:true,
                proxy: {
                    type: 'ajax',
                    url: getUltimaMillaCambioMedio,
                    extraParams:
                    {
                        idTipoMedio: ultimaMillaPrincipal
                    },
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    }
                },
                fields:
                    [
                        {name: 'idTipoMedio',     mapping: 'idTipoMedio'},
                        {name: 'nombreTipoMedio', mapping: 'nombreTipoMedio'}
                    ]
            });

            //----------------------------------------------
            cbFacturaTipoMedio = new Ext.form.Checkbox({
                id: 'cbFacturaTipoMedio',
                name: 'cbFacturaTipoMedio',
                boxLabel: 'Facturable',
                checked: false,
                listeners: {
                    change: function() {
                        if(Ext.getCmp('cbFacturaTipoMedio').value)
                        {
                            Ext.getCmp('lblCostoTipoMedio').enable();
                        }
                        else
                        {
                            Ext.getCmp('lblCostoTipoMedio').disable();
                            Ext.getCmp('lblCostoTipoMedio').reset();
                        }

                    }
                }
            });
            //----------------------------------------------
            var chkPrincipal = new Ext.form.Radio({
                boxLabel: 'Principal',
                id: 'chkPrincipal',
                name: 'chkTipoMedio',
                checked: true,
                inputValue: idServicio
            });
            //----------------------------------------------
            var chkBackup = new Ext.form.Radio({
                boxLabel: 'Backup',
                id: 'chkBackup',
                name: 'chkTipoMedio',
                inputValue: backup
            });
            //----------------------------------------------
            var rdTipoMedio = new Ext.form.RadioGroup({
                style: 'font-weight:bold;',
                colspan: 5,
                columns: 2,
                width: 450,
                items: [chkPrincipal, chkBackup]
            });
            
            //----------------------------------------------
            var objContainerServicio   = Ext.create('Ext.form.FieldSet', {
                title: tituloSeleccionar,
                hidden: !datos.boolCambioMedio,
                layout: {
                    type: 'table',
                    columns: 5,
                    align: 'stretch'
                },
                items: [
                    rdTipoMedio,
                ]
            });
            //----------------------------------------------
            var formPanel = Ext.create('Ext.form.Panel', {
                bodyPadding: 10,
                buttonAlign: 'center',
                waitMsgTarget: true,
                fieldDefaults: {
                    labelAlign: 'center',
                    labelWidth: 85,
                    msgTarget: 'side'
                },
                items: [
                    //informacion del Enlace
                    {
                        xtype: 'container',
                        defaultType: 'textfield',
                        layout: {
                            tdAttrs: {style: 'padding: 5px;'},
                            type: 'table',
                            columns: 1,
                            pack: 'center'
                        },
                        items: [
                            objContainerServicio,    
                            {
                                xtype: 'fieldset',
                                title: titulo,
                                layout: {
                                    type: 'table',
                                    columns: 5,
                                    align: 'stretch'
                                },
                                items: [
                                    //---------------------------------------------
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'cliente',
                                        fieldLabel: 'Cliente',
                                        fieldStyle: "font-weight: bold",
                                        displayField: cliente,
                                        value: cliente,
                                        readOnly: true,
                                        width: 280
                                    },
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'producto',
                                        fieldLabel: 'Producto',
                                        displayField: datos.descripcion,
                                        value: datos.descripcion,
                                        readOnly: true,
                                        width: 280
                                    },
                                    { width: 10, border: false},
                                    //---------------------------------------------
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id:'padreFacturacion',
                                        name: 'padreFacturacion',
                                        fieldLabel: 'Padre Facturación',
                                        displayField: datos.loginPadreFact,
                                        value: datos.loginPadreFact,
                                        readOnly: true,
                                        fieldStyle: "color: gray",
                                        width: 280
                                    },
                                    { width: 10, border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'coordenadas',
                                        fieldLabel: 'Coordenadas:',
                                        displayField: coordenadas,
                                        value: coordenadas,
                                        readOnly: true,
                                        width: 280
                                    },
                                    { width: 10, border: false},
                                    //---------------------------------------------
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'tipoMedio',
                                        fieldLabel: 'Tipo Medio:',
                                        displayField: tipoMedio,
                                        value: tipoMedio,
                                        readOnly: true,
                                        width: 280
                                    },
                                    { width: '15%', border: false},
                                    { width: '15%', border: false},
                                    { width: '10%', border: false},
                                    //-------------------------------------------
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'combobox',
                                        id:'comboUltimaMilla',
                                        name: 'comboUltimaMilla',
                                        store: storeUltimaMilla,
                                        fieldLabel: 'Tipo Medio a trasladarse',
                                        displayField: 'nombreTipoMedio',
                                        valueField: 'idTipoMedio',
                                        queryMode: 'local',
                                        width: 280
                                    },
                                    { width: '15%', border: false},
                                    { width: '15%', border: false},
                                    { width: '10%', border: false},
                                    //-------------------------------------------
                                    { width: '10%', border: false},
                                    cbFacturaTipoMedio,
                                    { width: '15%', border: false},
                                    { width: '15%', border: false},
                                    { width: '10%', border: false},
                                    //-------------------------------------------
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'numberfield',
                                        hideTrigger: true,
                                        id: 'lblCostoTipoMedio',
                                        disabled: true,
                                        maxLength: 6,
                                        name: 'tipoMedio',
                                        fieldLabel: 'Costo:',
                                        value: ''
                                    },
                                    { width: '15%', border: false},
                                    { width: '15%', border: false},
                                    { width: '10%', border: false}
                                ]
                            },

                        ]
                    }       
                ],
                buttons: [
                    {
                        text: 'Guardar',
                        disabled: false,
                        handler: function() 
                        {
                            var form = formPanel.getForm();
                            if (form.isValid())
                            {
                                var idServicioCambioMedio = idServicio;
                                if(objContainerServicio.isVisible())
                                {
                                    idServicioCambioMedio = rdTipoMedio.getValue().chkTipoMedio;
                                }
                                var ultimaMilla              = Ext.getCmp('comboUltimaMilla').value;
                                var facturableTipoMedio      = Ext.getCmp('lblCostoTipoMedio').value;

                                if(Ext.isEmpty(ultimaMilla))
                                {
                                    Ext.Msg.alert('Alerta', 'Debe escoger el tipo medio de traslado del servicio');
                                    return;
                                }

                                if(Ext.getCmp('cbFacturaTipoMedio').checked)
                                {
                                    if(Ext.isEmpty(facturableTipoMedio))
                                    {
                                        Ext.Msg.alert('Alerta', 'Se requiere el ingreso de un valor a facturar');
                                        return;
                                    }
                                    else if(facturableTipoMedio == 0)
                                    {
                                        Ext.Msg.alert('Alerta', 'Se requiere ingresar un valor diferente de 0 para facturar');
                                        return;
                                    }
                                    else if(facturableTipoMedio < 0)
                                    {
                                        Ext.Msg.alert('Alerta', 'No debe ingresar valores negativos para la facturación');
                                        return;
                                    }
                                    else if(facturableTipoMedio.length > 6)
                                    {
                                        Ext.Msg.alert('Alerta', 'La longitud del costo debe ser menor a 6 dígitos');
                                        return;
                                    }
                                }

                                Ext.Msg.show({
                                    title: 'Confirmar',
                                    msg: 'Está seguro de realizar el cambio de tipo medio ?',
                                    buttons: Ext.Msg.YESNO,
                                    icon: Ext.MessageBox.QUESTION,
                                    buttonText: {
                                        yes: 'si', no: 'no'
                                    },
                                    fn: function(btn) {
                                        if (btn == 'yes') {
                                            Ext.MessageBox.wait('Guardando datos...');
                                            Ext.Ajax.request({
                                                timeout: 900000,
                                                url: urlCrearServicioBackup,
                                                method: 'post',
                                                params: 
                                                {
                                                    servicio                 : idServicioCambioMedio,
                                                    punto                    : datos.idPunto,
                                                    descripcion              : descripcion,
                                                    precioVenta              : precioVenta,
                                                    precioNegociacion        : precioNegociacion,
                                                    precioUnitario           : precioUnitario,
                                                    ultimaMilla              : ultimaMilla,
                                                    loginVendedor            : datos.login,
                                                    codigo                   : datos.idProducto,
                                                    frecuencia               : datos.frecuencia,
                                                    caracteristicas          : jsonCaracteristicas,
                                                    facturable               : facturableTipoMedio,
                                                    cantidad                 : 1,
                                                    info                     : 'C',
                                                    tipoOrden                : 'C',
                                                    idPadreFacturacion       : datos.idPadreFact,
                                                    loginAux                 : datos.loginAux,
                                                    tipoEnlace               : tipoEnlace,
                                                    precioInstalacionFormula : datos.precioInstalacion,
                                                    precioInstalacionPactado : datos.precioInstalacion,
                                                    tipoMedio                :'S'
                                                },
                                                success: function(response) {
                                                    Ext.MessageBox.hide();
                                                    win.destroy();
                                                    var datos = Ext.JSON.decode(response.responseText);
                                                    if(datos.status === 'OK' && datos.intIdServicio > 0) {

                                                        if(datos.intTotalServicios >= 1)
                                                        {
                                                            Ext.Msg.confirm('Alerta', 'Desea Utilizar la misma Ultima Milla?', function(btn) {
                                                                if (btn == 'yes')
                                                                {
                                                                    usarMismaUM(datos.intIdServicio);
                                                                }
                                                                else
                                                                {
                                                                    connFact.request({
                                                                       url: "../../solicitarFactibilidadAjax",
                                                                       method: 'post',
                                                                       timeout: 400000,
                                                                       params: {id: datos.intIdServicio, idProducto: idProducto},
                                                                       success: function(response) {
                                                                           var text = response.responseText;

                                                                           Ext.Msg.alert('Mensaje', text, function(btn) {
                                                                               if (btn == 'ok') {
                                                                                   store.load();
                                                                               }
                                                                           });
                                                                       },
                                                                       failure: function(result) {
                                                                           Ext.Msg.alert('Alerta', result.responseText);
                                                                           store.load();
                                                                       }
                                                                   });
                                                                }
                                                            });
                                                        }
                                                        else
                                                        {
                                                            connFact.request({
                                                            url: "../../solicitarFactibilidadAjax",
                                                            method: 'post',
                                                            timeout: 400000,
                                                            params: {id: datos.intIdServicio, idProducto: idProducto},
                                                            success: function(response) {
                                                                var text = response.responseText;

                                                                Ext.Msg.alert('Mensaje', text, function(btn) {
                                                                    if (btn == 'ok') {
                                                                        store.load();
                                                                    }
                                                                });
                                                            },
                                                            failure: function(result) {
                                                                    Ext.Msg.alert('Alerta', result.responseText);
                                                                    store.load();
                                                                }
                                                            });
                                                        }
                                                    }
                                                    else
                                                    {
                                                         Ext.Msg.alert('Alerta', 'No se pudo crear el servicio.');
                                                    }
                                                },
                                                failure: function(response)
                                                {
                                                    Ext.MessageBox.hide();
                                                    win.destroy();
                                                    Ext.MessageBox.show({
                                                        title: 'Error',
                                                        msg: response.responseText,
                                                        buttons: Ext.MessageBox.OK,
                                                        icon: Ext.MessageBox.ERROR
                                                    });
                                                }
                                            });
                                        }
                                    }});
                            }
                            else
                            {
                                Ext.Msg.alert('Alerta', 'La longitud del costo debe ser menor a 6 dígitos');
                                return;
                            }
                        }
                    },
                    {
                        text: 'Cerrar',
                        handler: function(){
                            win.destroy();
                        }
                    }
                ]
            });

            var win = Ext.create('Ext.window.Window', {
                title: 'Cambio Tipo Medio',
                y : 150,
                modal: true,
                closable: true,
                layout: 'fit',
                items: [formPanel]
            }).show();
            
            calcularValoresProd(datos);
        },
        failure:function()
        {
            Ext.MessageBox.hide();
        }
    });
}

function calcularValoresProd(data)
{
    var capacidadUno    = data.capacidadUno;
    var capacidadDos    = data.capacidadDos;
    var funcionPrecio   = data.funcionPrecio;
    var caracteristicas = data.caracteristicas;
    var grupoNegocio    = '';
    
    if(!Ext.isEmpty(capacidadUno) && !Ext.isEmpty(capacidadDos))
    {
        caracteristicas["[CAPACIDAD1]"] = capacidadUno;
        caracteristicas["[CAPACIDAD2]"] = capacidadDos;
        
        jsonCaracteristicas = generarJsonCaracteristicas(caracteristicas,data.refCaracteristicas);
        
        for (var caracteristica in caracteristicas)
        {
            if(caracteristica === '[Grupo Negocio]')
            {
                grupoNegocio = caracteristicas[caracteristica];
            }
            funcionPrecio = replaceAll(funcionPrecio, caracteristica, caracteristicas[caracteristica]);
        }
        
        //Obtener el valor del precio del producto
        var precioProducto = eval(funcionPrecio);
        
        precioVenta = precioProducto;
        precioUnitario = precioProducto;
        precioNegociacion = precioProducto;
        descripcion = data.descripcion+" "+grupoNegocio+" "+capacidadUno+" "+capacidadDos+" "+data.zona;
    }
}

function generarJsonCaracteristicas(caracteristicas , refCaracteristicas)
{
    var arrayCaracteristicas = [];
        
    for(var refCaract in refCaracteristicas)
    {
        var jsonData = {};

        jsonData['idCaracteristica'] = refCaracteristicas[refCaract].idCaracteristica;

        for (var caracteristica in caracteristicas)
        {
            if(caracteristica.indexOf(refCaracteristicas[refCaract].caracteristica)!== -1)
            {
                jsonData['valor']       = caracteristicas[caracteristica];
                jsonData['descripcion'] = caracteristica;
            }
        }

        arrayCaracteristicas.push(jsonData);            
    }

    jsonCaracteristicas = Ext.JSON.encode(arrayCaracteristicas);
    
    return jsonCaracteristicas;
}

function replaceAll( text, busca, reemplaza )
{
    while (text.toString().indexOf(busca) !== -1)
    text = text.toString().replace(busca,reemplaza);
    return text;
}

var connFact = new Ext.data.Connection({
    listeners: {
        'beforerequest': {
            fn: function(con, opt) {
                Ext.MessageBox.show({
                    msg: 'Grabando los datos, Por favor espere!!',
                    progressText: 'Saving...',
                    width: 300,
                    wait: true,
                    waitConfig: {interval: 200}
                });
                //Ext.get(document.body).mask('Loading...');
            },
            scope: this
        },
        'requestcomplete': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
                //Ext.get(document.body).unmask();
            },
            scope: this
        },
        'requestexception': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
                //Ext.get(document.body).unmask();
            },
            scope: this
        }
    }
});