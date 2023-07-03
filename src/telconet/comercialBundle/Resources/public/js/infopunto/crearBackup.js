
var jsonCaracteristicas;

function crearServicioBackup(idServicio, ultimaMillaPrincipal)
{

    Ext.MessageBox.wait('Consultando datos. Favor espere..');
    Ext.Ajax.request({
        url: urlGetInformacionBackup,
        method: 'post',
        timeout: 400000,
        params:
            {
                idServicio: idServicio
            },
        success: function(response)
        {
            Ext.MessageBox.hide();
            
            var datos = Ext.JSON.decode(response.responseText);
            var boolEsClearChannel = (datos.descripcion==='CLEAR CHANNEL PUNTO A PUNTO'?false:true);
            var boolEsConcentrador = (datos.esConcentrador==='SI'?true:false);
            
            var titulo    = '<b><label>Información Servicio '+(boolEsConcentrador?'Concentrador ':'')+'Principal</label><b>';
            var tituloBck = '<b><label>Información Servicio '+(boolEsConcentrador?'Concentrador ':'')+'Backup</label><b>';
            
            storeTipoRed = new Ext.data.Store({
                total: 'total',
                autoLoad: true,
                proxy: {
                    type: 'ajax',
                    url: getTipoRedPorProducto,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    },
                    extraParams:
                    {
                        intIdProducto: datos.idProducto
                    }
                },
                fields:
                    [
                        {name: 'id',    mapping: 'id'},
                        {name: 'name',  mapping: 'name'}
                    ]
            });

            storeUltimaMilla = new Ext.data.Store({
                total: 'total',
                autoLoad:true,
                proxy: {
                    type: 'ajax',
                    url: getUltimaMilla,
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
            
            storeVendedores = new Ext.data.Store({
                autoLoad:true,
                proxy: {
                    type: 'ajax',
                    url: urlVendedores,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'registros'
                    }
                },
                fields:
                    [
                        {name: 'login',  type: 'string'},
                        {name: 'nombre', type: 'string'}
                    ]
            });
            
            
            
            var comboVendedores = new Ext.form.ComboBox(
                {
                    xtype: 'combobox',
                    store: storeVendedores,
                    labelAlign: 'left',
                    name: 'comboVendedores',
                    id: 'comboVendedores',
                    valueField: 'login',
                    displayField: 'nombre',
                    fieldLabel: 'Vendedor',
                    width: 280,
                    emptyText: 'Seleccione Vendedor',
                    disabled: false,
                    listeners:
                        {
                            beforerender: function(combo)
                            {
                                combo.setValue(datos.login);
                            }
                        }
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

                            {
                                xtype: 'fieldset',
                                title: titulo,
                                layout: {
                                    type: 'table',
                                    columns: 5,
                                    align: 'stretch'
                                },
                                items: [
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'producto',
                                        fieldLabel: 'Producto',
                                        fieldStyle: "font-weight: bold",
                                        displayField: datos.descripcion,
                                        value: datos.descripcion,
                                        readOnly: true,
                                        width: 280
                                    },
                                    { width: 10, border: false},
                                    { width: 15, border: false},                                    
                                    { width: 10, border: false},
                                    //---------------------------------------------
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'umPrincipal',
                                        fieldLabel: 'Ultima Milla Principal',
                                        displayField: ultimaMillaPrincipal,
                                        value: ultimaMillaPrincipal,
                                        readOnly: true,
                                        width: 280
                                    },
                                    { width: 10, border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'esConcentrador',
                                        fieldLabel: 'Es Concentrador',
                                        displayField: datos.esConcentrador,
                                        value: datos.esConcentrador,
                                        readOnly: true,
                                        width: 280
                                    },
                                    { width: 10, border: false},
                                    //---------------------------------------------
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
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'zona',
                                        fieldLabel: 'Zona',
                                        displayField: datos.zona,
                                        value: datos.zona,
                                        readOnly: true,
                                        width: 280
                                    },
                                    { width: '10%', border: false}
                                ]
                            },
                            //Informacion de nuevo Servicio Backup
                            {
                                xtype: 'fieldset',
                                title: tituloBck,
                                layout: {
                                    type: 'table',
                                    columns: 5,
                                    align: 'stretch'
                                },
                                items: [
                                    {width: '10%', border: false},
                                    comboVendedores,
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'combobox',
                                        fieldLabel: 'Tipo de Red',
                                        id: 'comboTipoRed',
                                        name: 'comboTipoRed',
                                        queryMode: 'local',
                                        store: storeTipoRed,
                                        valueField: 'id',
                                        displayField: 'name',
                                        width: 280,
                                        listeners: {
                                            change: function(combo) {
                                                storeUltimaMilla.getProxy().extraParams = {
                                                    strTipoRed: combo.getValue(),
                                                    strNombreTecnico: datos.nombreTecnico
                                                };
                                                storeUltimaMilla.load();
                                            }
                                        }
                                    },
                                    { width: '10%', border: false},
                                    //-------------------------------------------
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'combobox',
                                        id:'comboUltimaMilla',
                                        name: 'comboUltimaMilla',
                                        store: storeUltimaMilla,
                                        fieldLabel: 'Ultima Milla Backup',
                                        displayField: 'nombreTipoMedio',
                                        valueField: 'idTipoMedio',
                                        queryMode: 'local',
                                        width: 280
                                    },
                                    //-------------------------------------------
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
                                    { width: '10%', border: false},
                                    //---------------------------------------------
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'numberfield',
                                        id:'capacidadUnoBackup',
                                        name: 'capacidadUnoBackup',
                                        fieldLabel: 'Capacidad Uno',
                                        displayField: datos.capacidadUno,
                                        value: datos.capacidadUno,
                                        hideTrigger: true,
                                        fieldStyle: "color: gray",
                                        readOnly: true,
                                        width: 280
                                    },
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'numberfield',
                                        id:'capacidadDosBackup',
                                        name: 'capacidadDosBackup',
                                        fieldLabel: 'Capacidad Dos',
                                        displayField: datos.capacidadDos,
                                        value: datos.capacidadDos,
                                        hideTrigger: true,
                                        fieldStyle: "color: gray",
                                        readOnly: true,
                                        width: 280
                                    },
                                    { width: '10%', border: false},
                                    //---------------------------------------------
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'numberfield',
                                        id:'precioUnitarioBackup',
                                        name: 'precioUnitarioBackup',
                                        fieldLabel: 'Precio Unitario (Fórmula)',
                                        displayField: '',
                                        value: '',
                                        readOnly: true,
                                        hideTrigger: true,
                                        fieldStyle: "color: gray",
                                        width: 280
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'numberfield',
                                        id:'precioNegociacionBackup',
                                        name: 'precioNegociacionBackup',
                                        fieldLabel: 'Precio Negociación',
                                        displayField: '',
                                        value: '',
                                        readOnly: false,
                                        hideTrigger: true,
                                        fieldStyle: "color: blue;font-weight:bold;",
                                        width: 280,
                                        listeners : {
                                            blur : function (f, e)
                                            {
                                                actualizarTotal(datos);
                                            }
                                        }
                                    },
                                    { width: '10%', border: false},
                                    //---------------------------------------------
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'numberfield',
                                        id:'precioInstalacionFormulaBackup',
                                        name: 'precioInstalacionFormulaBackup',
                                        fieldLabel: 'Precio Instalación (Fórmula)',
                                        displayField: datos.instalacion,
                                        value: datos.instalacion,
                                        readOnly: true,
                                        hideTrigger: true,
                                        fieldStyle: "color: gray",
                                        width: 280
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'numberfield',
                                        id:'precioInstalacionPactadoBackup',
                                        name: 'precioInstalacionPactadoBackup',
                                        fieldLabel: 'Precio Instalación (Pactado)',
                                        displayField: datos.instalacion,
                                        value: datos.instalacion,
                                        readOnly: false,
                                        hideTrigger: true,
                                        fieldStyle: "color: gray",
                                        width: 280
                                    },
                                    { width: '10%', border: false},
                                    //---------------------------------------------
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'numberfield',
                                        id:'precioVentaBackup',
                                        name: 'precioVentaBackup',
                                        fieldLabel: 'Precio Venta',
                                        displayField: '',
                                        value: '',
                                        readOnly: true,
                                        hideTrigger: true,
                                        fieldStyle: "color: green;font-weight:bold;",
                                        width: 280
                                    },
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id:'descripcionBackup',
                                        name: 'descripcionBackup',
                                        fieldLabel: 'Descripcion',
                                        displayField: '',
                                        value: '',
                                        hideTrigger: true,
                                        width: 280
                                    },
                                    { width: '10%', border: false},
                                    //-----------------------------
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'combobox',
                                        id:'comboModeloBackUp',
                                        name: 'comboModeloBackUp',
                                        store: [
                                            ['BGP','BGP'],
                                            ['Última Milla','Última Milla']
                                        ],
                                        hidden: boolEsClearChannel,
                                        fieldLabel: 'Modelo BackUp',
                                        displayField: '',
                                        valueField: '',
                                        queryMode: 'local',
                                        editable: false,
                                        width: 280,
                                        listeners: {
                                            select: function(combo){
                                                if(Ext.getCmp('comboModeloBackUp').getValue() == 'BGP')
                                                {
                                                    Ext.getCmp('comboConmutadorOptico').setValue('NO');
                                                }
                                                else
                                                {
                                                    Ext.getCmp('comboConmutadorOptico').setValue('SI');
                                                }
                                            }
                                        }
                                    },
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'combobox',
                                        id:'comboConmutadorOptico',
                                        name: 'comboConmutadorOptico',
                                        store: [
                                            ['SI','SI'],
                                            ['NO','NO']
                                        ],
                                        disabled: true,
                                        hidden: boolEsClearChannel,
                                        fieldLabel: 'Conmutador Óptico',
                                        displayField: '',
                                        valueField: '',
                                        queryMode: 'local',
                                        width: 280
                                    },
                                    { width: '10%', border: false},
                                ]
                            }

                        ]
                    }       
                ],
                buttons: [
                    {
                        text: 'Crear Backup',
                        disabled: false,
                        handler: function() 
                        {
                            var precioUnitario           = '';
                            var precioNegociacion        = '';
                            var precioInstalacionFormula = '';
                            var precioInstalacionPactado = '';
                            var precioVenta              = '';
                            var descripcion              = '';
                            var ultimaMilla              = Ext.getCmp('comboUltimaMilla').value;
                            var loginVendedor            = Ext.getCmp('comboVendedores').value;
                            var strTipoRed               = Ext.getCmp('comboTipoRed').value;
                            var strConmutadorOptico      = Ext.getCmp('comboConmutadorOptico').value;
                            var strModeloBackUp           = Ext.getCmp('comboModeloBackUp').value;

                            if(Ext.isEmpty(ultimaMilla))
                            {
                                Ext.Msg.alert('Alerta', 'Debe seleccionar la Ultima Milla del nuevo Servicio Backup');
                                return;
                            }

                            if(Ext.isEmpty(strTipoRed))
                            {
                                Ext.Msg.alert('Alerta', 'Debe seleccionar el Tipo de Red');
                                return;
                            }

                            if(!boolEsClearChannel && Ext.isEmpty(strModeloBackUp))
                            {
                                Ext.Msg.alert('Alerta', 'Debe seleccionar el Modelo BackUp');
                                return;
                            }

                            if(!boolEsClearChannel && Ext.isEmpty(strConmutadorOptico))
                            {
                                Ext.Msg.alert('Alerta', 'Debe seleccionar si requiere Conmutador Óptico');
                                return;
                            }

                            //Toma los valores del servicio principal para replicar
                            descripcion              = Ext.getCmp('descripcionBackup').value;
                            precioVenta              = Ext.getCmp('precioVentaBackup').value;
                            precioInstalacionFormula = Ext.getCmp('precioInstalacionFormulaBackup').value;
                            precioInstalacionPactado = Ext.getCmp('precioInstalacionPactadoBackup').value;
                            precioNegociacion        = Ext.getCmp('precioNegociacionBackup').value;
                            precioUnitario           = Ext.getCmp('precioUnitarioBackup').value;

                            //Validar que las capacidades del servicio backup no sea menor a las capacidades del principal
                            if(datos.capacidadUno !== Ext.getCmp('capacidadUnoBackup').value && 
                               datos.capacidadDos !== Ext.getCmp('capacidadDosBackup').value)
                            {
                                Ext.Msg.alert('Alerta', 'Las Capacidades de Servicio Backup no pueden ser diferentes a \n\
                                                        las capacidades del Servicio Principal');
                                return;
                            }
                            
                            Ext.Msg.show({
                                title: 'Confirmar',
                                msg: 'Está seguro de crear el Servicio Backup ?',
                                buttons: Ext.Msg.YESNOCANCEL,
                                icon: Ext.MessageBox.QUESTION,
                                buttonText: {
                                    yes: 'si', no: 'no', cancel: 'cancelar'
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
                                                servicio                 : idServicio,
                                                punto                    : datos.idPunto,
                                                descripcion              : descripcion,
                                                precioVenta              : precioVenta,
                                                precioInstalacionFormula : precioInstalacionFormula,
                                                precioInstalacionPactado : precioInstalacionPactado,
                                                precioNegociacion        : precioNegociacion,
                                                precioUnitario           : precioUnitario,
                                                ultimaMilla              : ultimaMilla,
                                                tipoRed                  : strTipoRed,
                                                loginVendedor            : loginVendedor,
                                                codigo                   : datos.idProducto,
                                                frecuencia               : datos.frecuencia,
                                                caracteristicas          : jsonCaracteristicas,
                                                cantidad                 : 1,
                                                info                     : 'C',
                                                tipoOrden                : datos.tipoOrden,
                                                idPadreFacturacion       : datos.idPadreFact,
                                                conmutadorOptico         : strConmutadorOptico,
                                                modeloBackUp             : strModeloBackUp
                                            },
                                            success: function(response) {
                                                Ext.MessageBox.hide();
                                                win.destroy();
                                                var datos = Ext.JSON.decode(response.responseText);
                                                Ext.Msg.alert('Mensaje', datos.mensaje, function(btn) {
                                                    if (btn === 'ok') {
                                                        store.load();
                                                    }
                                                });
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
                title: 'Crear Servicio Backup',
                y : 150,
                modal: true,
                closable: true,
                layout: 'fit',
                items: [formPanel]
            }).show();
            calcularValoresProducto(datos);
        },
        failure:function()
        {
            Ext.MessageBox.hide();
        }
    });
}

function calcularValoresProducto(data)
{
    var capacidadUno    = Ext.getCmp('capacidadUnoBackup').value;
    var capacidadDos    = Ext.getCmp('capacidadDosBackup').value;
    var funcionPrecio   = data.funcionPrecio;
    var caracteristicas     = data.caracteristicas;
    var grupoNegocio    = '';
    var precioProducto  = '';
    
    if(!Ext.isEmpty(capacidadUno) && !Ext.isEmpty(capacidadDos))
    {
        caracteristicas["[CAPACIDAD1]"] = capacidadUno;
        caracteristicas["[CAPACIDAD2]"] = capacidadDos;
        if(data.descripcion==='CLEAR CHANNEL PUNTO A PUNTO')
        {
            precioProducto = data.precioFormula;
        }
        else
        {
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
        precioProducto = eval(funcionPrecio);

        }

        
        
        Ext.getCmp('precioVentaBackup').setRawValue(precioProducto);
        Ext.getCmp('precioUnitarioBackup').setRawValue(precioProducto);
        Ext.getCmp('precioNegociacionBackup').setRawValue(precioProducto);
        Ext.getCmp('descripcionBackup').setRawValue(data.descripcion+" "+grupoNegocio+" "+capacidadUno+" "+capacidadDos+" "+data.zona);
        Ext.getCmp('precioVentaBackup').setValue(precioProducto);
        Ext.getCmp('precioUnitarioBackup').setValue(precioProducto);
        Ext.getCmp('precioNegociacionBackup').setValue(precioProducto);
        Ext.getCmp('descripcionBackup').setValue(data.descripcion+" "+grupoNegocio+" "+capacidadUno+" "+capacidadDos+" "+data.zona);
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

function actualizarTotal()
{
    Ext.getCmp('precioVentaBackup').setRawValue(Ext.getCmp('precioNegociacionBackup').value);
}

function replaceAll( text, busca, reemplaza )
{
    while (text.toString().indexOf(busca) !== -1)
    text = text.toString().replace(busca,reemplaza);
    return text;
}