/**
 * confirmarServicioNetfiber
 * 
 * Funcion que sirve para confirmar los servicios Netfiber de MD
 * 
 * @author Lizbeth Cruz <mlcruz@telconet.ec>
 * @version 1.0 02-10-2018
 * @param data
 * @param idAccion
 */
function confirmarServicioNetfiber(data, idAccion)
{
    var connValidaSerie = new Ext.data.Connection({
                                        listeners: {
                                            'beforerequest': {
                                                fn: function() {
                                                    Ext.MessageBox.show({
                                                        msg: 'Verificando Serie, Por favor espere!!',
                                                        progressText: 'verificando...',
                                                        width: 300,
                                                        wait: true,
                                                        waitConfig: {interval: 200}
                                                    });
                                                },
                                                scope: this
                                            },
                                            'requestcomplete': {
                                                fn: function() {
                                                    Ext.MessageBox.hide();
                                                },
                                                scope: this
                                            },
                                            'requestexception': {
                                                fn: function() {
                                                    Ext.MessageBox.hide();
                                                },
                                                scope: this
                                            }
                                        }
                                    });
                                    
    Ext.define('ListaParametrosDetModel', {
        extend: 'Ext.data.Model',
        fields: [{name: 'intIdParametroDet', type: 'int'},
            {name: 'intIdParametroCab', type: 'int'},
            {name: 'strDescripcionDet', type: 'string'},
            {name: 'strValor1', type: 'string'},
            {name: 'strValor2', type: 'string'},
            {name: 'strValor3', type: 'string'},
            {name: 'strValor4', type: 'string'},
            {name: 'strEstado', type: 'string'},
            {name: 'strUsrCreacion', type: 'string'},
            {name: 'strFeCreacion', type: 'string'}
        ]
    });
    
    var storeModelosElementos = new Ext.data.Store({  
                            pageSize: 2000,
                            proxy: {
                                type: 'ajax',
                                url : getModelosElemento,
                                extraParams: {
                                    tipo:   '',
                                    forma:  'Empieza con',
                                    estado: "Activo"
                                },
                                reader: {
                                    type: 'json',
                                    totalProperty: 'total',
                                    root: 'encontrados'
                                }
                            },
                            fields:
                                [
                                  {name:'modelo', mapping:'modelo'},
                                  {name:'codigo', mapping:'codigo'}
                                ]
                        });
    
    var storeElementosNetFiber = new Ext.data.Store({
                                    pageSize: 100,
                                    autoLoad: false,
                                    proxy: {
                                        type: 'ajax',
                                        url: getElementosNetFiber,
                                        reader: {
                                            type: 'json',
                                            totalProperty: 'total',
                                            root: 'encontrados'
                                        },
                                        actionMethods: {
                                            create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                                        },
                                        extraParams: {
                                            idServicio: data.idServicio
                                        }
                                    },
                                    fields:
                                        [
                                            {name: 'tipoElemento',        mapping: 'tipoElemento'},
                                            {name: 'serieElemento',       mapping: 'serieElemento'},
                                            {name: 'modeloElemento',      mapping: 'modeloElemento'},
                                            {name: 'descripcionElemento', mapping: 'descripcionElemento'}
                                        ]
                                });
                        
    storeElementosNetFiber.load({
                    callback:function(){        
                            var intCantidadRegistrosStore = gridElementosNetFiber.getStore().getCount() ;
                            if (intCantidadRegistrosStore == 0 )
                            {
                                Ext.getCmp('elementosTransceiver').hide()
                            }
                    }
                });
                                
    var cellEditingNh = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 2,
        listeners: {
            edit: function(editor, object) {
                var rowIdx = object.rowIdx;
                var column = object.field;
                var recordElemento = gridElementosNetFiber.getStore().getAt(rowIdx);
                if (typeof recordElemento != 'undefined' && column == "serieElemento")
                {
                    var serie = recordElemento.get('serieElemento');

                    if (serie != "")
                    {
                        var booleanExisteRecord = existeRecordNetFiber(serie, gridElementosNetFiber);
                        if (!booleanExisteRecord)
                        {
                            connValidaSerie.request({
                                    url: buscarCpeNaf,
                                    timeout: 120000,
                                    method: 'post',
                                    params: { 
                                        serieCpe:                serie,
                                        modeloElemento:          '',
                                        estado:                  'PI',
                                        bandera:                 'ActivarServicio',
                                        permiteReutilizarEquipo: 'SI'
                                    },
                                    success: function(response){
                                        var respuesta      = response.responseText.split("|");
                                        var status         = respuesta[0];
                                        var mensaje        = respuesta[1].split(",");
                                        var descripcion    = mensaje[0];
                                        var modeloElemento = mensaje[2];
                                        if(status=="OK")
                                        {
                                            if(storeModelosElementos.find('modelo',modeloElemento)==-1)
                                            {
                                                recordElemento.set('descripcionElemento','');
                                                recordElemento.set('modeloElemento','');
                                                var strMsj = 'El Elemento con: <br>'+
                                                'Modelo: <b>'+modeloElemento+' </b><br>'+
                                                'Descripcion: <b>'+descripcion+' </b><br>'+
                                                'no existe Telcos, debe ser registrado previamente para continuar con el proceso, Favor Revisar <br>';
                                                var objAlertMsj = Ext.Msg.alert('Advertencia', strMsj);
                                                Ext.defer(function () {
                                                    objAlertMsj.toFront();
                                                }, 5);
                                            }
                                            else
                                            {
                                                recordElemento.set('descripcionElemento',descripcion);
                                                recordElemento.set('modeloElemento',modeloElemento);
                                            }
                                        }
                                        else
                                        {
                                            recordElemento.set('descripcionElemento','');
                                            recordElemento.set('modeloElemento','');
                                            var objAlertMsjError = Ext.Msg.alert('Mensaje ', mensaje);
                                            Ext.defer(function () {
                                                objAlertMsjError.toFront();
                                            }, 5);
                                        }
                                    },
                                    failure: function(result)
                                    {
                                        Ext.get(formPanel.getId()).unmask();
                                        var mm = Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                        Ext.defer(function () {
                                            mm.toFront();
                                        }, 5);
                                    }
                                });
                        }
                        else
                        {
                            var mm = Ext.Msg.alert('Mensaje ', 
                                                   'La serie '+ serie +
                                                   ' ya existe en otro registro del grid, por favor ingrese otra serie.');
                            Ext.defer(function () {
                                mm.toFront();
                            }, 5);
                        }
                    }
                    else
                    {
                        var mensaje = "Por favor ingrese la serie correspondiente."
                        recordElemento.set('descripcionElemento','');
                        recordElemento.set('modeloElemento','');
                        var objAlertMsjError = Ext.Msg.alert('Mensaje ', mensaje);
                        Ext.defer(function () {
                            objAlertMsjError.toFront();
                        }, 5);
                    }
                }
            }
        }
    });
                                
    var gridElementosNetFiber = Ext.create('Ext.grid.Panel', {
        width: 600,
        height:150,
        title: 'Equipos a Registrar',
        store: storeElementosNetFiber,
        columnLines: true,
        plugins: [cellEditingNh],
        listeners: 
        {
            viewready: function (grid)
            {
                var view = grid.view;
                grid.mon(view,
                {
                    uievent: function (type, view, cell, recordIndex, cellIndex)
                    {
                        grid.cellIndex   = cellIndex;
                        grid.recordIndex = recordIndex;
                    }
                });
                grid.tip = Ext.create('Ext.tip.ToolTip',
                {
                    target: view.el,
                    delegate: '.x-grid-cell',
                    trackMouse: true,
                    autoHide: false,
                    renderTo: Ext.getBody(),
                    listeners:
                    {
                        beforeshow: function(tip)
                        {
                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1)
                            {
                                var header = grid.headerCt.getGridColumns()[grid.cellIndex];

                                if( header.dataIndex != null )
                                {
                                    var trigger         = tip.triggerElement,
                                        parent          = tip.triggerElement.parentElement,
                                        columnDataIndex = view.getHeaderByCell(trigger).dataIndex;

                                    if( view.getRecord(parent).get(columnDataIndex) != null )
                                    {
                                        var columnText      = view.getRecord(parent).get(columnDataIndex).toString();

                                        if (columnText)
                                        {
                                            tip.update(columnText);
                                        }
                                        else
                                        {
                                            return false;
                                        }
                                    }
                                    else
                                    {
                                        return false;
                                    }
                                }
                                else
                                {
                                    return false;
                                }
                            }     
                        }
                    }
                });
                grid.tip.on('show', function()
                {
                    var timeout;
                    grid.tip.getEl().on('mouseout', function()
                    {
                        timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                    });
                    grid.tip.getEl().on('mouseover', function(){window.clearTimeout(timeout);});
                    Ext.get(view.el).on('mouseover', function(){window.clearTimeout(timeout);});
                    Ext.get(view.el).on('mouseout', function()
                    {
                        timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                    });
                });
            }
        },
        columns: [
            {
                width: 25,
                text : 'N°',
                dataIndex: 'rowIndex',
                sortable : false,
                // other config you need..
                renderer : function(value, metaData, record, rowIndex)
                {
                    return rowIndex+1;
                }
            },
            {
                header: 'Tipo Elemento',
                dataIndex: 'tipoElemento',
                width: 170                  
            },
            {
                header: 'Serie',
                dataIndex: 'serieElemento',
                width: 120,
                editor: {
                    xtype: 'textfield',
                    valueField: ''
                }
            },
            {
                header: 'Modelo',
                dataIndex: 'modeloElemento',
                width: 115
            },
            {
                header: 'Descripcion',
                dataIndex: 'descripcionElemento',
                width: 158
            }
            ]

    });
    
    var confirmarFormPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side',
            bodyStyle: 'padding:20px'
        },
        layout: {
            type: 'table',
            columns: 1
        },
        defaults: {
            bodyStyle: 'padding:20px'
        },
        items: [
            {
                xtype: 'fieldset',
                title: 'Datos del Servicio',
                defaultType: 'textfield',
                defaults: {
                    width: 600
                },
                items: [
                    {
                        xtype: 'container',
                        layout: {
                            type: 'table',
                            columns: 5,
                            align: 'stretch'
                        },
                        items: [
                            {width: 25, border: false},
                            {
                                xtype: 'textfield',
                                name: 'login',
                                fieldLabel: 'Login',
                                value: data.login,
                                readOnly: true,
                                width: 280
                            },
                            {width: 50, border: false},
                            {
                                xtype: 'textfield',
                                name: 'estado',
                                fieldLabel: 'Estado',
                                value: data.estado,
                                readOnly: true,
                                width: 200
                            },
                            {width: 25, border: false},
                            {width: 25, border: false},
                            {
                                xtype: 'textfield',
                                name: 'producto',
                                fieldLabel: 'Producto',
                                value: data.nombreProducto,
                                readOnly: true,
                                width: 280
                            },
                            {width: 50, border: false},
                            {
                                xtype: 'textfield',
                                name: 'ultimaMillaServicio',
                                fieldLabel: 'Última Milla',
                                value: data.ultimaMilla,
                                readOnly: true,
                                width: 200
                            },
                            {width: 25, border: false},
                            {width: 25, border: false},
                            {
                                id : 'observacionActivarServicio',
                                xtype: 'textarea',
                                name: 'observacionActivarServicio',
                                fieldLabel: '* Observación',
                                value: "",
                                readOnly: false,
                                required : true,
                                width: 280
                            },
                            {width: 50, border: false},
                            {width: 200, border: false},
                            {width: 25, border: false}
                        ]
                    }

                ]
            },
            {
                xtype: 'fieldset',
                title: 'Información de Fibra Invisible - NetFiber',
                defaultType: 'textfield',
                defaults: {
                    width: 600
                },
                items: [
                    {
                        xtype: 'container',
                        layout: {
                            type: 'table',
                            columns: 5,
                            align: 'stretch'
                        },
                        items: [
                            {width: 25, border: false},
                            {
                                xtype: 'textfield',
                                id: 'serieNetfiber',
                                name: 'serieNetfiber',
                                fieldLabel: 'Serie',
                                displayField: "",
                                value: "",
                                width: 280,
                                listeners: {
                                    blur: function() {
                                        if (!Ext.isEmpty(Ext.getCmp('serieNetfiber').getValue()))
                                        {
                                            Ext.getCmp('btnConfirmar').setDisabled(true);
                                            var booleanExisteRecord = existeRecordNetFiber(Ext.getCmp('serieNetfiber').getValue(), gridElementosNetFiber);
                                            if (booleanExisteRecord)
                                            {
                                                Ext.getCmp('btnConfirmar').setDisabled(false);
                                                Ext.Msg.alert('Mensaje ', 'La serie ya se encuentra utilizada, por favor ingrese una serie diferente.');
                                                Ext.getCmp('descripcionNetfiber').setValue = "";
                                                Ext.getCmp('descripcionNetfiber').setRawValue("");
                                            }
                                            else
                                            {
                                                Ext.Ajax.request({
                                                    url: buscarCpeNaf,
                                                    method: 'post',
                                                    params: {
                                                        serieCpe: Ext.getCmp('serieNetfiber').getValue(),
                                                        estado: 'PI',
                                                        bandera: 'ActivarServicio'
                                                    },
                                                    success: function(response) 
                                                    {
                                                        Ext.getCmp('btnConfirmar').setDisabled(false);
                                                        var respuesta = response.responseText.split("|");
                                                        var status = respuesta[0];
                                                        var mensaje = respuesta[1];

                                                        if (status == "OK")
                                                        {
                                                            Ext.getCmp('descripcionNetfiber').setValue = mensaje;
                                                            Ext.getCmp('descripcionNetfiber').setRawValue(mensaje);
                                                        }
                                                        else
                                                        {
                                                            Ext.Msg.alert('Mensaje ', mensaje);
                                                            Ext.getCmp('descripcionNetfiber').setValue = status;
                                                            Ext.getCmp('descripcionNetfiber').setRawValue(status);
                                                        }
                                                    },
                                                    failure: function(result)
                                                    {
                                                        Ext.getCmp('btnConfirmar').setDisabled(false);
                                                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                                    }
                                                });
                                            }
                                        }
                                    }
                                }
                            },
                            {width: 50, border: false},
                            {
                                xtype: 'textfield',
                                id: 'descripcionNetfiber',
                                name: 'descripcionNetfiber',
                                fieldLabel: 'Descripción',
                                displayField: "",
                                value: "",
                                readOnly: true,
                                width: 225
                            },
                            {width: 25, border: false}
                        ]
                    }
                ]
            },
            {
                xtype: 'fieldset',
                id: 'elementosTransceiver',
                title: 'Informacion de los Elementos Transceiver',
                defaultType: 'textfield',
                defaults: {
                    width: 600
                },
                items: [
                    {
                        xtype: 'container',
                        layout: {
                            type: 'table',
                            columns: 1,
                            align: 'stretch'
                        },
                        items: [
                                gridElementosNetFiber
                        ]
                    }
                ]
            }
        ],
        buttons: [{
                id: 'btnConfirmar',
                text: 'Confirmar',
                formBind: true,
                handler: function() {

                    var strObservacion          = Ext.getCmp('observacionActivarServicio').getValue();
                    var strSerieNetfiber        = Ext.getCmp('serieNetfiber').getValue();
                    var strDescripcioNetfiber   = Ext.getCmp('descripcionNetfiber').getValue();
                    var booleanValidacion  = true;
                    var intBanderaErroflag     = 0;
                    
                    if (Ext.isEmpty(strObservacion))
                    {
                        booleanValidacion  = false;
                        intBanderaErroflag = 1;
                    }
                    else if( strDescripcioNetfiber == "ELEMENTO ESTADO INCORRECTO" || 
                        strDescripcioNetfiber == "ELEMENTO CON SALDO CERO"    || 
                        strDescripcioNetfiber == "NO EXISTE ELEMENTO" ||
                        Ext.isEmpty(strDescripcioNetfiber))
                    {
                        booleanValidacion  = false;
                        intBanderaErroflag = 2;
                    }
                    else if(Ext.isEmpty(strSerieNetfiber))
                    {
                        booleanValidacion = false;
                        intBanderaErroflag = 3;
                    }
                    if (booleanValidacion)
                    {
                        booleanValidacion = validarInformacionGrid(gridElementosNetFiber);
                    }
                    if (booleanValidacion) 
                    {
                        Ext.get(confirmarFormPanel.getId()).mask('Ejecutando...');
                        var jsonDatosElementos = getInfoElementos(gridElementosNetFiber);
                        Ext.Ajax.request({
                            url: strUrlConfirmarServicioNetfiberBoton,
                            method: 'post',
                            timeout: 400000,
                            params: {
                                idServicio             : data.idServicio,
                                idProducto             : data.productoId,
                                jsonDatosElementos     : jsonDatosElementos,
                                strObservacionServicio : strObservacion,
                                idAccion               : idAccion,
                                strSerieNetfiber       : strSerieNetfiber,
                                intIdServicioInternet  : data.idServicioRefIpFija,
                                intIdSolicitudServicio : data.tieneSolicitudPlanificacion
                            },
                            success: function(response) {
                                Ext.get(confirmarFormPanel.getId()).unmask();
                                var objData   = Ext.JSON.decode(response.responseText);
                                var strStatus = objData.strStatus;
                                if (strStatus == "OK") {
                                    win.destroy();
                                    store.load();
                                    Ext.Msg.alert('Mensaje', 'Se confirmó el Servicio: ' + data.login, function(btn) {
                                        if (btn == 'ok') {
                                        }
                                    });
                                }
                                else {
                                    Ext.Msg.alert('Mensaje ', 'Error:' + strStatus);
                                }
                            },
                            failure: function(result)
                            {
                                Ext.get(confirmarFormPanel.getId()).unmask();
                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                            }
                        });
                    }
                    else 
                    {
                        if( intBanderaErroflag == 1 )
                        {
                            Ext.Msg.alert("Validación","Por favor ingrese la observación correspondiente!");
                        }
                        else if( intBanderaErroflag == 2 )
                        {
                            Ext.Msg.alert("Validación","Datos incorrectos, favor revisar!");
                        }
                        else if( intBanderaErroflag == 3 )
                        {
                            Ext.Msg.alert("Validación","Por favor ingrese la serie correspondiente!");
                        }
                        else
                        {
                            Ext.Msg.alert("Failed", "Existen campos vacíos. Por favor revisar.");
                        }
                    }
                }
            }, 
            {
                text: 'Cancelar',
                handler: function() {
                    win.destroy();
                }
            }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Confirmar Servicio',
        modal: true,
        width: 650,
        closable: true,
        layout: 'fit',
        items: [confirmarFormPanel]
    }).show();
    
    storeModelosElementos.load();
}

function existeRecordNetFiber(serieRecord, grid)
{
    var serieFibraInvisible = Ext.getCmp('serieNetfiber').getValue();
    var existe = false;        
    var cont   = 0;
    var num    = grid.getStore().getCount(); 
    
    if(!Ext.isEmpty(serieFibraInvisible) && serieFibraInvisible === serieRecord)
    {
        cont = cont +1;
    }

    for (var i = 0; i < num; i++)
    {
        var serieElemento = grid.getStore().getAt(i).data.serieElemento;                   
        if (serieElemento === serieRecord)
        {
            cont = cont +1;
        }
    }
    if (cont > 1)
    {
        existe = true;
    }
    return existe;
}

function validarInformacionGrid(gridElementosNetFiber)
{
    var camposCompletos     = true;        
    var num                 = gridElementosNetFiber.getStore().getCount();        
    var serieElemento       = '';
    var modeloElemento      = '';
    for (var i = 0; i < num; i++)
    {
        serieElemento       = gridElementosNetFiber.getStore().getAt(i).data.serieElemento;                  
        modeloElemento      = gridElementosNetFiber.getStore().getAt(i).data.modeloElemento;
        if (Ext.isEmpty(serieElemento) || Ext.isEmpty(modeloElemento) 
           )
        {
            camposCompletos = false;
            break;
        }
    }
    return camposCompletos;
}

function getInfoElementos(gridElementosNetFiber)
{
    var arrayElemento = [];
    for (var i = 0; i < gridElementosNetFiber.getStore().getCount(); i++)
    {
        arrayElemento.push(gridElementosNetFiber.getStore().getAt(i).data);
    }
    return Ext.JSON.encode(arrayElemento);
}