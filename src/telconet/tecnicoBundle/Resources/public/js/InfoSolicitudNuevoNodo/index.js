var esContactoNuevo=false;

Ext.onReady(function()
{
    Ext.tip.QuickTipManager.init();
    
    Ext.util.Format.thousandSeparator = '.';
    Ext.util.Format.decimalSeparator = ',';


    store = new Ext.data.Store
        ({
            pageSize: 10,
            total: 'total',
            proxy:
                {
                    timeout: 400000,
                    type: 'ajax',
                    url: url_grid,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        },
                    extraParams:
                        {
                            nombreElemento: '',
                            estadoSolicitud: 'Todos'
                        }
                },
            fields:
                [
                    {name: 'idElemento', mapping: 'idElemento'},
                    {name: 'nombreElemento', mapping: 'nombreElemento'},
                    {name: 'direccion', mapping: 'direccion'},
                    {name: 'estado', mapping: 'estado'},
                    {name: 'estadoSolicitud', mapping: 'estadoSolicitud'},
                    {name: 'idSolicitud', mapping: 'idSolicitud'},
                    {name: 'feEjecucionSol', mapping: 'feEjecucionSol'},
                    {name: 'motivo', mapping: 'motivo'},
                    {name: 'observacion', mapping: 'observacion'},
                    {name: 'nombreCanton', mapping: 'nombreCanton'},
                    {name: 'nombreProvincia', mapping: 'nombreProvincia'},
                    {name: 'valor', mapping: 'valor'},
                    {name: 'idProvincia', mapping: 'idProvincia'},
                    {name: 'contratoActivo', mapping: 'contratoActivo'},
                    {name: 'tieneRenovacion', mapping: 'tieneRenovacion'}

                ]
        });

    var pluginExpanded = true;

    grid = Ext.create('Ext.grid.Panel',
        {
            width: '99%',
            height: 350,
            store: store,
            loadMask: true,
            frame: false,
            viewConfig: {enableTextSelection: true},
            iconCls: 'icon-grid',
            columns:
                [
                    {
                        header: 'Nombre Nodo',
                        dataIndex: 'nombreElemento',
                        width: '15%',
                        sortable: true
                    },
                    {
                        header: 'Direccion',
                        dataIndex: 'direccion',
                        width: '10%',
                        sortable: true
                    },
                    {
                        header: 'Estado Nodo',
                        dataIndex: 'estado',
                        width: '7%',
                        sortable: true
                    },
                    {
                        header: 'Estado Solicitud',
                        dataIndex: 'estadoSolicitud',
                        width: '10%',
                        sortable: true
                    },
                    {
                        header: 'No. Solicitud',
                        dataIndex: 'idSolicitud',
                        width: '6%',
                        sortable: true
                    },
                    {
                        header: 'Fecha Solicitud',
                        dataIndex: 'feEjecucionSol',
                        width: '10%',
                        sortable: true
                    },
                    {
                        header: 'Observaciones',
                        dataIndex: 'observacion',
                        width: '15%',
                        sortable: true
                    },
                    {
                        header: 'Motivo',
                        dataIndex: 'motivo',
                        width: '11%',
                        sortable: true
                    },
                    {
                        xtype: 'actioncolumn',
                        header: 'Acciones',
                        width: '15%',
                        items:
                            [        
                                //Ver información general del Nodo
                                {
                                    getClass: function(v, meta, rec)
                                    {
                                        var permiso = $("#ROLE_280-2361");
                                        var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);                                        
                                        if (!boolPermiso)
                                        {
                                            return 'button-grid-invisible';
                                        }
                                        else
                                        {                                            
                                            return 'button-ver-resumen-nodo';                                            
                                        }
                                    },
                                    tooltip:'Ver Resumen Nodo',
                                    handler: function(grid, rowIndex, colIndex)
                                    {
                                        verResumenNodo(grid.getStore().getAt(rowIndex).data);                                                                             
                                    }
                                },
                                 //Ver informacion de contrato
                                {
                                    getClass: function(v, meta, rec)
                                    {
                                        var permiso = $("#ROLE_280-2361");
                                        var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);                                        
                                        
                                        if (!boolPermiso)
                                        {
                                            return 'button-grid-invisible';
                                        }
                                        else
                                        {
                                            if ((rec.get('estadoSolicitud') == 'AutorizadaLegal' || rec.get('estadoSolicitud') == 'FirmadoContrato')
                                                || (rec.get('estadoSolicitud') == 'Finalizada' && rec.get('contratoActivo') == 1))
                                            {
                                                return 'button-grid-show';
                                            }                                            
                                            else
                                            {
                                                return 'button-grid-invisible';
                                            }
                                        }
                                    },
                                    tooltip:'Ver Contratos',
                                    handler: function(grid, rowIndex, colIndex)
                                    {
                                        verContratos(grid.getStore().getAt(rowIndex).data);                                                                             
                                    }
                                },
                                //Autorizaciones TEC y Legal de Nodo
                                {
                                    getClass: function(v,meta,rec){
                                        var permisoTec = $("#ROLE_280-2357");                                        
                                        var boolPermisoTec = (typeof permisoTec === 'undefined') ? false : (permisoTec.val() == 1 ? true : false);
                                        
                                        var permisoLeg = $("#ROLE_280-2358");
                                        var boolPermisoLeg = (typeof permisoLeg === 'undefined') ? false : (permisoLeg.val() == 1 ? true : false);                                        
                                        
                                        if (!boolPermisoTec && !boolPermisoLeg)
                                        {
                                            return 'button-grid-invisible';
                                        }
                                        else
                                        {                                            
                                            if (rec.get('estadoSolicitud') == 'Pendiente' && boolPermisoTec)
                                            {
                                                return 'button-grid-Check';
                                            }
                                            else if (rec.get('estadoSolicitud') == 'AutorizadaTecnico' && boolPermisoLeg)
                                            {
                                                return 'button-grid-Check';
                                            }
                                            else
                                            {
                                                return 'button-grid-invisible';
                                            }
                                        }
                                    },
                                    tooltip:'Autorizar TEC o Legal',
                                    handler: function(grid, rowIndex, colIndex)
                                    {
                                        estado = grid.getStore().getAt(rowIndex).data.estadoSolicitud;

                                        if (estado == 'Pendiente')
                                        {
                                            aprobacionTecnicaSolicitud(grid.getStore().getAt(rowIndex).data);
                                        }
                                        else if (estado == 'AutorizadaTecnico')
                                        {
                                            aprobacionLegalSolicitud(grid.getStore().getAt(rowIndex).data);
                                        }

                                    }
                                },
                                //rechazar
                                {
                                    getClass: function(v, meta, rec)
                                    {
                                        var permiso = $("#ROLE_280-2359");
                                        var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

                                        if (!boolPermiso)
                                        {
                                            return 'button-grid-invisible';

                                        }
                                        else
                                        {
                                            if (rec.get('estadoSolicitud') == 'Pendiente')
                                            {
                                                return 'button-grid-BigDelete';
                                            }
                                            else if (rec.get('estadoSolicitud') == 'AutorizadaTecnico')
                                            {
                                                return 'button-grid-BigDelete';
                                            }
                                            else
                                            {
                                                return 'button-grid-invisible';
                                            }
                                        }
                                    },
                                    tooltip:'Rechazar TEC o Legal',
                                    handler: function(grid, rowIndex, colIndex)
                                    {
                                        estado = grid.getStore().getAt(rowIndex).data.estadoSolicitud;

                                        if (estado == 'Pendiente')
                                        {
                                            tipo = 'TEC';
                                        }
                                        else if (estado == 'AutorizadaTecnico')
                                        {
                                            tipo = 'Legal';
                                        }

                                        rechazarReversar(grid.getStore().getAt(rowIndex).data, tipo, 'rechazar');
                                    }
                                },
                                //Descargar PDF de contrato
                                 {
                                    getClass: function(v, meta, rec)
                                    {
                                        var permiso = $("#ROLE_280-2361");
                                        var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);                                        
                                        if (!boolPermiso)
                                        {
                                            return 'button-grid-invisible';
                                        }
                                        else
                                        {
                                            if (rec.get('estadoSolicitud') == 'AutorizadaLegal' && rec.get('contratoActivo')=='0')
                                            {
                                                return 'button-bajarContrato';
                                            }                                            
                                            else
                                            {
                                                return 'button-grid-invisible';
                                            }
                                        }
                                    },
                                    tooltip:'Descargar Contrato',
                                    handler: function(grid, rowIndex, colIndex)
                                    {                                        
                                        window.location = grid.getStore().getAt(rowIndex).data.idSolicitud+"/generarContratoPDF"; 
                                    }
                                },
                                //Subir contrato firmado
                                 {
                                    getClass: function(v, meta, rec)
                                    {
                                        var permiso = $("#ROLE_280-2361");
                                        var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                        if (!boolPermiso)
                                        {
                                            return 'button-grid-invisible';
                                        }
                                        else
                                        {
                                            if (rec.get('estadoSolicitud') == 'AutorizadaLegal' && rec.get('contratoActivo')==0)
                                            {
                                                return 'button-grid-uploadContrato';
                                            }                                            
                                            else
                                            {
                                                return 'button-grid-invisible';
                                            }
                                        }
                                    },
                                    tooltip:'Subir Contrato Firmado',
                                    handler: function(grid, rowIndex, colIndex)
                                    {                                        
                                        subirContrato(grid.getStore().getAt(rowIndex).data);
                                    }
                                },
                                //Habilitar Nodo
                                 {
                                    getClass: function(v, meta, rec)
                                    {
                                        var permiso = $("#ROLE_280-2362");
                                        var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                        if (!boolPermiso)
                                        {
                                            return 'button-grid-invisible';
                                        }
                                        else
                                        {
                                            if (rec.get('estadoSolicitud') === 'FirmadoContrato' && rec.get('tieneRenovacion') === 'N')
                                            {
                                                return 'button-grid-habilitar';
                                            }                                            
                                            else
                                            {
                                                return 'button-grid-invisible';
                                            }
                                        }
                                    },
                                    tooltip:'Habilitar Nodo',
                                    handler: function(grid, rowIndex, colIndex)
                                    {                                        
                                        habilitarNodo(grid.getStore().getAt(rowIndex).data);
                                    }
                                },
                                //Descargar contrato final
                                 {
                                    getClass: function(v, meta, rec)
                                    {
                                        var permiso = $("#ROLE_280-2361");
                                        var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                        if (!boolPermiso)
                                        {
                                            return 'button-grid-invisible';
                                        }
                                        else
                                        {
                                            if ((rec.get('estadoSolicitud') == 'FirmadoContrato' || rec.get('estadoSolicitud') == 'Finalizada')
                                                && rec.get('contratoActivo') == 1)
                                            {
                                                return 'button-grid-descargar';
                                            }                                            
                                            else
                                            {
                                                return 'button-grid-invisible';
                                            }
                                        }
                                    },
                                    tooltip:'Descargar Contrato Final',
                                    handler: function(grid, rowIndex, colIndex)
                                    {                                                                                
                                        window.location = grid.getStore().getAt(rowIndex).data.idSolicitud+"/ajaxDescargarContratoFinal";
                                    }
                                },
                                //Historial de la solicitud
                                {
                                    getClass: function(v, meta, rec)
                                    {                                        
                                        return 'button-grid-verLogs';
                                    },
                                    tooltip:'Ver Historial Solicitud',
                                    handler: function(grid, rowIndex, colIndex)
                                    {
                                        verHistorialSolicitud(grid.getStore().getAt(rowIndex).data);
                                    }
                                },
                                //Reversar accion en la solicitud
                                {
                                    getClass: function(v, meta, rec)
                                    {
                                        var permiso = $("#ROLE_280-2360");
                                        var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                        if (!boolPermiso)
                                        {
                                            return 'button-grid-invisible';
                                        }
                                        else
                                        {
                                            if (rec.get('estadoSolicitud') == 'AutorizadaTecnico')
                                            {
                                                return 'button-reversar';
                                            }
                                            else if (rec.get('estadoSolicitud') == 'AutorizadaLegal')
                                            {
                                                return 'button-reversar';
                                            }
                                            else
                                            {
                                                return 'button-grid-invisible';
                                            }
                                        }
                                    },  
                                    tooltip:'Reversar TEC o Legal',
                                    handler: function(grid, rowIndex, colIndex)
                                    {
                                        estado = grid.getStore().getAt(rowIndex).data.estadoSolicitud;

                                        if (estado == 'AutorizadaTecnico')
                                        {
                                            tipo = 'TEC';
                                        }
                                        else if (estado == 'AutorizadaLegal')
                                        {
                                            tipo = 'Legal';
                                        }
                                        rechazarReversar(grid.getStore().getAt(rowIndex).data, tipo, 'reversar');
                                    }
                                },
                                //Reversar a Autorizacion Tecnica para renovar contrato generado
                                {
                                    getClass: function(v, meta, rec)
                                    {
                                        var permiso = $("#ROLE_280-2358");                                        
                                        var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                        if (!boolPermiso)
                                        {
                                            return 'button-grid-invisible';
                                        }
                                        else
                                        {                                            
                                            if (rec.get('estadoSolicitud') === 'Finalizada' && rec.get('contratoActivo') != 0)
                                            {                                                                                               
                                                return 'button-grid-renovarContrato';
                                            }                                            
                                            else
                                            {
                                                return 'button-grid-invisible';
                                            }
                                        }
                                    },                    
                                    tooltip:'Renovacion Contrato',
                                    handler: function(grid, rowIndex, colIndex)
                                    {                                                                               
                                        renovarContrato(grid.getStore().getAt(rowIndex).data);
                                    }
                                }
                                
                            ]
                    }
                ],
            bbar: Ext.create('Ext.PagingToolbar',
                {
                    store: store,
                    displayInfo: true,
                    displayMsg: 'Mostrando {0} - {1} de {2}',
                    emptyMsg: "No hay datos que mostrar."
                }),
            renderTo: 'grid'
        });

    var filterPanel = Ext.create('Ext.panel.Panel',
        {
            bodyPadding: 7, // Don't want content to crunch against the borders
            //bodyBorder: false,
            border: false,
            //border: '1,1,0,1',
            buttonAlign: 'center',
            layout: {
                type: 'table',
                columns: 5,
                align: 'stretch'
            },
            bodyStyle:
                {
                    background: '#fff'
                },
            collapsible: true,
            collapsed: false,
            width: '99%',
            title: 'Criterios de busqueda',
            buttons:
                [
                    {
                        text: 'Buscar',
                        iconCls: "icon_search",
                        handler: function() {
                            buscar();
                        }
                    },
                    {
                        text: 'Limpiar',
                        iconCls: "icon_limpiar",
                        handler: function() {
                            limpiar();
                        }
                    }
                ],
            items:
                [
                    {
                        xtype: 'textfield',
                        id: 'nombreNodoTxt',
                        fieldLabel: 'Nombre Nodo',
                        value: '',
                        width: '30%'
                    },
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Estado solicitud',
                        id: 'estadoCmb',
                        value: '',
                        store: [
                            ['Pendiente', 'Pendiente'],
                            ['AutorizadaTecnico', 'AutorizadaTecnico'],
                            ['AutorizadaLegal', 'AutorizadaLegal'],
                            ['FirmadoContrato', 'FirmadoContrato'],                            
                            ['Finalizada', 'Finalizada'],
                            ['Anulada', 'Anulada'],
                            ['Eliminada', 'Eliminada'],
                            ['RechazadaTecnico', 'RechazadaTecnico'],
                            ['RechazadaLegal', 'RechazadaLegal']                            
                        ],
                        width: '30%'
                    }
                ],
            renderTo: 'filtro'
        });

    store.load();


});


function aprobacionTecnicaSolicitud(data)
{
    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function(con, opt) {
                    Ext.get(document.body).mask('Autorizando TEC...');
                },
                scope: this
            },
            'requestcomplete': {
                fn: function(con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function(con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    });

    var formPanel = Ext.create('Ext.form.Panel',
        {
            bodyPadding: 2,
            waitMsgTarget: true,
            fieldDefaults:
                {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget: 'side'
                },
            items:
                [
                    {
                        xtype: 'fieldset',
                        autoHeight: true,
                        width: 380,
                        items:
                            [
                                {
                                    xtype: 'displayfield',
                                    fieldLabel: 'Nodo:',
                                    id: 'nombreNodoTec',
                                    value: data.nombreElemento
                                },
                                {
                                    xtype: 'displayfield',
                                    fieldLabel: 'Direccion:',
                                    id: 'direccionTec',
                                    value: data.direccion
                                },
                                {
                                    xtype: 'displayfield',
                                    fieldLabel: 'Provincia:',
                                    id: 'provinciaTec',
                                    value: data.nombreProvincia
                                },
                                {
                                    xtype: 'displayfield',
                                    fieldLabel: 'Canton:',
                                    id: 'cantonTec',
                                    value: data.nombreCanton
                                },
                                {
                                    xtype: 'fieldset',
                                    title: 'Observación',
                                    defaultType: 'textfield',
                                    defaults:
                                        {
                                            width: '95%'
                                        },
                                    items:
                                        [
                                            {
                                                xtype: 'container',
                                                layout:
                                                    {
                                                        type: 'table',
                                                        columns: 4,
                                                        align: 'stretch'
                                                    },
                                                items:
                                                    [
                                                        {
                                                            xtype: 'textareafield',
                                                            id: 'observacionTec',
                                                            width: '100%'
                                                        }
                                                    ]
                                            }
                                        ]
                                }
                            ]
                    }
                ],
            buttons:
                [
                    {
                        text: 'Autorizar',
                        handler: function()
                        {
                            var observacion = Ext.getCmp('observacionTec').getValue();

                            conn.request
                                ({
                                    url: url_autorizarTec,
                                    method: 'post',
                                    timeout: 300000,
                                    params:
                                        {
                                            idSolicitud: data.idSolicitud,
                                            observacion: observacion
                                        },
                                    success: function(response)
                                    {
                                        var json = Ext.JSON.decode(response.responseText);
                                        Ext.Msg.alert('Mensaje', json.respuesta, function(btn)
                                        {
                                            if (btn === 'ok')
                                            {
                                                store.load();
                                                win.destroy();
                                            }
                                        });
                                    },
                                    failure: function(result)
                                    {
                                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                    }
                                });
                        }
                    },
                    {
                        text: 'Cancelar',
                        handler: function()
                        {
                            win.destroy();
                        }
                    }
                ]
        });

    var win = Ext.create('Ext.window.Window',
        {
            title: 'Autorizacion Solicitud de Nodo - TEC',
            modal: true,
            width: 400,
            closable: true,
            layout: 'fit',
            items: [formPanel]
        }).show();

}//fin de funcion aprobar solicitud

function aprobacionLegalSolicitud(data)
{    
    storeTipoPago = new Ext.data.Store({
        total: 'total', 
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url: url_formaPago,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'                
            }      ,
            extraParams: {
                estado: 'Activo'
            }
        },
        fields:
            [
                {name: 'id_forma_pago', mapping: 'id_forma_pago'},
                {name: 'nombre_forma_pago', mapping: 'descripcion_forma_pago'}
            ]
    });
 
    storeTipoCuenta = new Ext.data.Store({
        total: 'total', 
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url: url_tipoCuenta,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'                
            }      ,
            extraParams: {
                estado: 'Activo'
            }
        },
        fields:
            [
                {name: 'id_tipo_cuenta', mapping: 'id_tipo_cuenta'},
                {name: 'descripcion_tipo_cuenta', mapping: 'descripcion_tipo_cuenta'}
            ]
    });
    
    storeBanco = new Ext.data.Store({
        total: 'total',         
        proxy: {
            type: 'ajax',
            url: url_banco,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'                
            }      ,
            extraParams: {
                estado: 'Activo'
            }
        },
        fields:
            [
                {name: 'idBanco', mapping: 'idBanco'},
                {name: 'nombreBanco', mapping: 'nombreBanco'}
            ]
    });        
    
    storeRepLegal = new Ext.data.Store({
        total: 'total', 
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url: url_repLegal,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'                
            }      ,
            extraParams: {               
                tipoRol: 'Representante Legal'
            }
        },
        fields:
            [
                {name: 'login', mapping: 'login'},
                {name: 'nombres', mapping: 'nombres'}
            ]
    });

    var conn = requestMask('Cargando Informacion Contrato...');
    conn.request({
        method: 'POST',
        url: url_infoContrato,
        params:
        {
            idProvincia: data.idProvincia,
            idElemento:  data.idElemento
        },
        success: function(response)
        {
            var json = Ext.JSON.decode(response.responseText);

            //Si no es renovacion procede a crear contrato por primera vez
            if (json.success && !json.esRenovacion)
            {                
                var formPanel = Ext.create('Ext.form.Panel',
                    {
                        bodyPadding: 2,
                        waitMsgTarget: true,
                        fieldDefaults:
                            {
                                labelAlign: 'left',
                                labelWidth: 85,
                                msgTarget: 'side'
                            },
                        items:
                            [
                                {
                                    xtype: 'fieldset',
                                    autoHeight: true,
                                    width: 380,
                                    items:
                                        [
                                            {
                                                xtype: 'displayfield',
                                                fieldLabel: 'Nodo:',
                                                id: 'nombreNodoLegal',
                                                labelStyle:'font-weight:bold',
                                                value: data.nombreElemento
                                            },
                                            {
                                                xtype: 'displayfield',
                                                fieldLabel: 'Direccion:',
                                                id: 'direccionLegal',
                                                labelStyle:'font-weight:bold',
                                                value: data.direccion
                                            },
                                            {
                                                xtype: 'displayfield',
                                                fieldLabel: 'Provincia:',
                                                id: 'provinciaLegal',
                                                labelStyle:'font-weight:bold',
                                                value: data.nombreProvincia
                                            },
                                            {
                                                xtype: 'displayfield',
                                                fieldLabel: 'Canton:',
                                                id: 'cantonLegal',
                                                labelStyle:'font-weight:bold',
                                                value: data.nombreCanton
                                            },
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'Valor:',
                                                id: 'valorLegal',
                                                labelStyle:'font-weight:bold',
                                                value: data.valor,
                                                width:300,
                                                readOnly:true
                                            },
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'Contacto Legal:',
                                                id: 'contactoLegal',
                                                value: json.contactoLegal,
                                                readOnly:true,
                                                labelStyle:'font-weight:bold',     
                                                width:300
                                            },
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'No. Contrato:',
                                                id: 'numeroContrato',
                                                value: json.numeroContrato,
                                                readOnly:false,
                                                labelStyle:'font-weight:bold',     
                                                width:300
                                            },
                                            {
                                                xtype: 'datefield',
                                                fieldLabel: 'Fecha Inicio Contrato:',
                                                id: 'feInicioContrato',
                                                name: 'feInicioContrato',
                                                editable: false,
                                                format: 'Y-m-d',
                                                value: json.fechaActual,                                                
                                                labelStyle:'font-weight:bold',     
                                                width:300                                                
                                            },
                                            {
                                                xtype: 'combobox',
                                                fieldLabel: 'Dura Contrato:',
                                                id: 'cmbDuraContrato',
                                                name: 'cmbDuraContrato',
                                                store: [
                                                    ['1', '1'],
                                                    ['2', '2'],
                                                    ['3', '3'],
                                                    ['4', '4'],
                                                    ['5', '5']
                                                ],                                               
                                                emptyText: '',
                                                editable:false,
                                                labelStyle:'font-weight:bold',     
                                                width:300,
                                                listeners: 
                                                {
                                                    select: function(combo)
                                                    {                                                                                                    
                                                        var feIniContrato = new Date(document.getElementById('feInicioContrato-inputEl').value); 
                                                        var mesActual = feIniContrato.getMonth();           
                                                        var feFinContrato = new Date(feIniContrato.setMonth(mesActual + combo.getValue()*12)); 
                                                        var dia = feFinContrato.getDate();
                                                        var mes = (feFinContrato.getMonth()+1).toString();
                                                        var anio= feFinContrato.getFullYear(); 
                                                        
                                                        if((""+mes.length) < 2)
                                                        {
                                                            mes = "0"+ mes;
                                                        }
                                                        
                                                        var fechaFinContratoFormat = anio + "-" + mes + "-" + dia;
                                                        Ext.getCmp('feFinContrato').setValue(fechaFinContratoFormat); 
                                                    }
                                                }
                                            },
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'Fecha Fin Contrato:',
                                                id: 'feFinContrato',
                                                name: 'feFinContrato',
                                                readOnly:true,
                                                labelStyle:'font-weight:bold',     
                                                width:300,
                                                value:''
                                            },
                                            {
                                                xtype: 'combobox',
                                                fieldLabel: 'Tipo Pago:',
                                                id: 'cmbTipoPago',
                                                name: 'cmbTipoPago',
                                                store:storeTipoPago,
                                                displayField: 'nombre_forma_pago',
                                                valueField: 'id_forma_pago',
                                                queryMode: "remote",
                                                emptyText: '',
                                                labelStyle:'font-weight:bold',     
                                                width:300,
                                                listeners: {
                                                    select: function(combo){	
                                                        
                                                        tipo = combo.getRawValue();
                                                        
                                                        if(tipo === 'EFECTIVO')
                                                        {
                                                            Ext.getCmp('cmbTipoCuenta').setDisabled(true);
                                                            Ext.getCmp('numeroCuenta').setDisabled(true);
                                                            Ext.getCmp('cmbBanco').setDisabled(true);
                                                            Ext.getCmp('cmbBanco').value = "";
                                                            Ext.getCmp('cmbBanco').setRawValue(""); 
                                                            Ext.getCmp('cmbTipoCuenta').value = "";
                                                            Ext.getCmp('cmbTipoCuenta').setRawValue(""); 
                                                            Ext.getCmp('numeroCuenta').value = "";
                                                            Ext.getCmp('numeroCuenta').setRawValue(""); 
                                                        }
                                                        else
                                                        {
                                                            Ext.getCmp('cmbTipoCuenta').setDisabled(false);
                                                            Ext.getCmp('numeroCuenta').setDisabled(false);
                                                        }
                                                    }
                                                }
                                            },
                                            {
                                                xtype: 'combobox',
                                                fieldLabel: 'Tipo Cuenta:',
                                                id: 'cmbTipoCuenta',
                                                name: 'cmbTipoCuenta',
                                                store:storeTipoCuenta,
                                                displayField: 'descripcion_tipo_cuenta',
                                                valueField: 'id_tipo_cuenta',
                                                queryMode: "remote",
                                                emptyText: '',
                                                labelStyle:'font-weight:bold',     
                                                width:300,
                                                value:'',
                                                listeners: {
                                                    select: function(combo){							

                                                        Ext.getCmp('cmbBanco').reset();
                                                        Ext.getCmp('cmbBanco').value = "";
                                                        Ext.getCmp('cmbBanco').setDisabled(false);                               
                                                        Ext.getCmp('cmbBanco').setRawValue("");  
                                                        
                                                        presentarBancosPorTipoCuenta(combo.getValue());
                                                    }
                                                }
                                            },
                                            {
                                                xtype: 'combobox',
                                                fieldLabel: 'Banco:',
                                                id: 'cmbBanco',
                                                name: 'cmbBanco',
                                                store:storeBanco,
                                                displayField: 'nombreBanco',
                                                valueField: 'idBanco',
                                                queryMode: "remote",
                                                emptyText: '',
                                                labelStyle:'font-weight:bold',     
                                                width:300,
                                                value:'',
                                                disabled:true
                                            },                                           
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'Numero Cuenta:',
                                                id: 'numeroCuenta',
                                                labelStyle:'font-weight:bold',     
                                                width:300,
                                                value:''
                                            },
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'Valor Garantia:',
                                                id: 'valorGarantia',
                                                labelStyle:'font-weight:bold',     
                                                width:300,
                                                value:''
                                            },
                                            {
                                                xtype: 'combobox',
                                                fieldLabel: 'Oficina:',
                                                id: 'cmbOficinaRepLegal',
                                                name: 'cmbOficinaRepLegal',
                                                store: [
                                                    ['Guayaquil', 'Guayaquil'],
                                                    ['Quito', 'Quito'],
                                                    ['Panamá', 'Panamá'],
                                                ],                                                                                               
                                                emptyText: '',
                                                labelStyle:'font-weight:bold',     
                                                width:300    ,
                                                value:''
                                            },
                                            {
                                                xtype: 'combobox',
                                                fieldLabel: 'Rep. Telconet:',
                                                id: 'cmbRepTelconet',
                                                name: 'cmbRepTelconet',
                                                store:storeRepLegal,
                                                displayField: 'nombres',
                                                valueField: 'login',
                                                queryMode: "remote",
                                                emptyText: '',
                                                labelStyle:'font-weight:bold',     
                                                width:300,
                                                value:''
                                            }
                                        ]
                                }
                            ],
                        buttons:
                            [
                                {
                                    text: 'Autorizar',
                                    handler: function()
                                    {                                                                                
                                        //Se valida mini formulario para crear contratos
                                        
                                        contactoLegal    = Ext.getCmp('contactoLegal').value;
                                        numeroContrato   = Ext.getCmp('numeroContrato').value;                                        
                                        fechaFinContrato = Ext.getCmp('feFinContrato').value;
                                        fechaIniContrato = Ext.getCmp('feInicioContrato').value;
                                        cmbTipoPago      = Ext.getCmp('cmbTipoPago').value;
                                        cmbBanco         = Ext.getCmp('cmbBanco').value;
                                        cmbTipoCuenta    = Ext.getCmp('cmbTipoCuenta').value;
                                        numeroCuenta     = Ext.getCmp('numeroCuenta').value;
                                        valorGarantia    = Ext.getCmp('valorGarantia').value;
                                        oficinaRepLegal  = Ext.getCmp('cmbOficinaRepLegal').value;
                                        cmbRepTelconet   = Ext.getCmp('cmbRepTelconet').value;
                                        provincia        = data.idProvincia;    
                                        valor            = Ext.getCmp('valorLegal').value;
                                        idSolicitud      = data.idSolicitud;
                                                                                                                        
                                        if(fechaFinContrato == "")
                                        {
                                            Ext.Msg.alert('Advertencia', 'No se ha establecido fecha de Fin de contrato');
                                        }                                        
                                        else if(cmbTipoPago == null)
                                        {
                                            Ext.Msg.alert('Advertencia', 'Debe escoger el tipo de Pago');
                                        }
                                        else if(oficinaRepLegal == "")
                                        {
                                            Ext.Msg.alert('Advertencia', 'Debe escoger la oficina de generacion de Contrato');
                                        } 
                                        else if(cmbRepTelconet == "")
                                        {
                                            Ext.Msg.alert('Advertencia', 'Debe escoger el representante Legal');
                                        }
                                        else
                                        {                                            
                                            var conn = requestMask('Autorizando Solicitud Legal...');
                                            conn.request
                                                ({
                                                    url: url_autorizarLegal,
                                                    method: 'post',
                                                    timeout: 300000,
                                                    params:
                                                        {
                                                            contactoLegal    : contactoLegal,
                                                            numeroContrato   : numeroContrato,                                                            
                                                            fechaFinContrato : fechaFinContrato,
                                                            fechaIniContrato : fechaIniContrato,
                                                            formaPago        : cmbTipoPago,
                                                            banco            : cmbBanco,
                                                            tipoCuenta       : cmbTipoCuenta,
                                                            numeroCuenta     : numeroCuenta,
                                                            valorGarantia    : valorGarantia,
                                                            ofiRepLegal      : oficinaRepLegal,
                                                            repLegal         : cmbRepTelconet,
                                                            provincia        : provincia,
                                                            valor            : valor,
                                                            idSolicitud      : idSolicitud
                                                        },
                                                    success: function(response)
                                                    {
                                                        Ext.get(formPanel.getId()).unmask();
                                                        var json = Ext.JSON.decode(response.responseText);
                                                        Ext.Msg.alert('Mensaje', json.respuesta, function(btn)
                                                        {
                                                            if (json.success)
                                                            {
                                                                store.load();
                                                                win.destroy();
                                                            }
                                                            else
                                                            {
                                                                store.load();
                                                            }
                                                        });
                                                    },
                                                    failure: function(result)
                                                    {
                                                        Ext.get(formPanel.getId()).unmask();
                                                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);                                                        
                                                    }
                                                });
                                        }
                                    
                                    }
                                },
                                {
                                    text: 'Cancelar',
                                    handler: function()
                                    {
                                        Ext.get(document.body).unmask();
                                        win.destroy();
                                    }
                                }
                            ]
                    });

                var win = Ext.create('Ext.window.Window',
                    {
                        title: 'Autorizacion Solicitud de Nodo - Legal',
                        modal: true,
                        width: 400,
                        closable: true,
                        layout: 'fit',
                        items: [formPanel]
                    }).show();
            }
            else if(json.success && json.esRenovacion) //Es renovacion
            {
                showRenovacion(json,data.idElemento,data.idProvincia); 
            }
            else 
            {
                Ext.Msg.alert('Alerta ', json.error);
            }
        }
    });   

}//fin de funcion aprobar solici

function rechazarReversar(data, tipo, accion)
{
    if (accion === 'rechazar')
    {
        mask = 'Rechazando Autorizacion ' + tipo;
        textButtom = 'Rechazar ' + tipo;
        title = 'Rechazar Solicitud de Nodo - ' + tipo;
    }
    else // reversar
    {
        mask = 'Reversando Autorizacion ' + tipo;
        textButtom = 'Reversar ' + tipo;
        title = 'Reversar Solicitud de Nodo - ' + tipo;
    }
    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function(con, opt) {
                    Ext.get(document.body).mask(mask);
                },
                scope: this
            },
            'requestcomplete': {
                fn: function(con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function(con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    });


    var formPanel = Ext.create('Ext.form.Panel',
        {
            bodyPadding: 2,
            waitMsgTarget: true,
            fieldDefaults:
                {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget: 'side'
                },
            items:
                [
                    {
                        xtype: 'fieldset',
                        autoHeight: true,
                        width: 380,
                        items:
                            [
                                {
                                    xtype: 'displayfield',
                                    fieldLabel: 'Nodo:',
                                    id: 'nombreNodo',
                                    value: data.nombreElemento
                                },
                                {
                                    xtype: 'displayfield',
                                    fieldLabel: 'Direccion:',
                                    id: 'direccion',
                                    value: data.direccion
                                },
                                {
                                    xtype: 'displayfield',
                                    fieldLabel: 'Provincia:',
                                    id: 'provincia',
                                    value: data.nombreProvincia
                                },
                                {
                                    xtype: 'displayfield',
                                    fieldLabel: 'Canton:',
                                    id: 'canton',
                                    value: data.nombreCanton
                                },
                                {
                                    xtype: 'fieldset',
                                    title: 'Observación',
                                    defaultType: 'textfield',
                                    defaults:
                                        {
                                            width: '95%'
                                        },
                                    items:
                                        [
                                            {
                                                xtype: 'container',
                                                layout:
                                                    {
                                                        type: 'table',
                                                        columns: 4,
                                                        align: 'stretch'
                                                    },
                                                items:
                                                    [
                                                        {
                                                            xtype: 'textareafield',
                                                            id: 'observacionReversoRechazo',
                                                            width: '100%'
                                                        }
                                                    ]
                                            }
                                        ]
                                }
                            ]
                    }
                ],
            buttons:
                [
                    {
                        text: textButtom,
                        handler: function()
                        {
                            var observacion = Ext.getCmp('observacionReversoRechazo').getValue();

                            conn.request
                                ({
                                    url: url_reversarRechazar,
                                    method: 'post',
                                    timeout: 300000,
                                    params:
                                        {
                                            idSolicitud: data.idSolicitud,
                                            tipoRechazoReverso: tipo,
                                            tipoAccion: accion,
                                            observacion: observacion,
                                            esRenovacion : false
                                        },
                                    success: function(response)
                                    {
                                        var json = Ext.JSON.decode(response.responseText);
                                        Ext.Msg.alert('Mensaje', json.respuesta, function(btn)
                                        {
                                            if (btn === 'ok')
                                            {
                                                store.load();
                                                win.destroy();
                                            }
                                        });
                                    },
                                    failure: function(result)
                                    {
                                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                    }
                                });
                        }
                    },
                    {
                        text: 'Cancelar',
                        handler: function()
                        {
                            win.destroy();
                        }
                    }
                ]
        });

    var win = Ext.create('Ext.window.Window',
        {
            title: title,
            modal: true,
            width: 400,
            closable: true,
            layout: 'fit',
            items: [formPanel]
        }).show();

}

function habilitarNodo(data)
{
    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function(con, opt) {
                    Ext.get(document.body).mask('Habilitando Nodo');
                },
                scope: this
            },
            'requestcomplete': {
                fn: function(con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function(con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    });

    var formPanel = Ext.create('Ext.form.Panel',
        {
            bodyPadding: 2,
            waitMsgTarget: true,
            fieldDefaults:
                {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget: 'side'
                },
            items:
                [
                    {
                        xtype: 'fieldset',
                        autoHeight: true,
                        width: 380,
                        items:
                            [
                                {
                                    xtype: 'displayfield',
                                    fieldLabel: 'Nodo:',
                                    id: 'nombreNodoTec',
                                    value: data.nombreElemento
                                },
                                {
                                    xtype: 'displayfield',
                                    fieldLabel: 'Direccion:',
                                    id: 'direccionTec',
                                    value: data.direccion
                                },
                                {
                                    xtype: 'displayfield',
                                    fieldLabel: 'Provincia:',
                                    id: 'provinciaTec',
                                    value: data.nombreProvincia
                                },
                                {
                                    xtype: 'displayfield',
                                    fieldLabel: 'Canton:',
                                    id: 'cantonTec',
                                    value: data.nombreCanton
                                },
                                {
                                    xtype: 'fieldset',
                                    title: 'Observación',
                                    defaultType: 'textfield',
                                    defaults:
                                        {
                                            width: '95%'
                                        },
                                    items:
                                        [
                                            {
                                                xtype: 'container',
                                                layout:
                                                    {
                                                        type: 'table',
                                                        columns: 4,
                                                        align: 'stretch'
                                                    },
                                                items:
                                                    [
                                                        {
                                                            xtype: 'textareafield',
                                                            id: 'observacionHab',
                                                            width: '100%'
                                                        }
                                                    ]
                                            }
                                        ]
                                }
                            ]
                    }
                ],
            buttons:
                [
                    {
                        text: 'Habilitar Nodo',
                        handler: function()
                        {
                            var observacion = Ext.getCmp('observacionHab').getValue();

                            conn.request
                                ({
                                    url: url_habilitarNodo,
                                    method: 'post',
                                    timeout: 300000,
                                    params:
                                        {
                                            idSolicitud: data.idSolicitud,
                                            observacion: observacion
                                        },
                                    success: function(response)
                                    {
                                        var json = Ext.JSON.decode(response.responseText);
                                        Ext.Msg.alert('Mensaje', json.respuesta, function(btn)
                                        {
                                            if (btn === 'ok')
                                            {
                                                store.load();
                                                win.destroy();
                                            }
                                        });
                                    },
                                    failure: function(result)
                                    {
                                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                    }
                                });
                        }
                    },
                    {
                        text: 'Cancelar',
                        handler: function()
                        {
                            win.destroy();
                        }
                    }
                ]
        });

    var win = Ext.create('Ext.window.Window',
        {
            title: 'Habilitar Nodo',
            modal: true,
            width: 400,
            closable: true,
            layout: 'fit',
            items: [formPanel]
        }).show();

}//fin de funcion aprobar solicitud

function verHistorialSolicitud(data)
{
    storeHistorialSolicitud = new Ext.data.Store
        ({
            pageSize: 50,
            autoLoad: true,
            proxy:
                {
                    type: 'ajax',
                    url: url_historialSol,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        },
                    extraParams:
                        {
                            idSolicitud: data.idSolicitud
                        }
                },
            fields:
                [
                    {name: 'observacion', mapping: 'observacion'},
                    {name: 'fechaCrea', mapping: 'fechaCrea'},
                    {name: 'usuarioCrea', mapping: 'usuarioCrea'},
                    {name: 'estado', mapping: 'estado'}
                ]
        });

    gridHistorico = Ext.create('Ext.grid.Panel',
        {
            id: 'gridHistorico',
            store: storeHistorialSolicitud,
            columnLines: true,
            columns:
                [
                    {
                        header: 'Observación',
                        dataIndex: 'observacion',
                        width: 200,
                        sortable: true
                    },
                    {
                        header: 'Fecha',
                        dataIndex: 'fechaCrea',
                        width: 120,
                        sortable: true
                    },
                    {
                        header: 'Usuario',
                        dataIndex: 'usuarioCrea',
                        width: 80
                    },
                    {
                        header: 'Estado',
                        dataIndex: 'estado',
                        width: 120
                    }
                ],
            viewConfig:
                {
                    stripeRows: true
                },
            frame: true,
            height: 200
        });

    var formPanel = Ext.create('Ext.form.Panel',
        {
            bodyPadding: 2,
            waitMsgTarget: true,
            fieldDefaults:
                {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget: 'side'
                },
            items:
                [
                    {
                        xtype: 'fieldset',
                        title: '',
                        defaultType: 'textfield',
                        defaults:
                            {
                                width: 550
                            },
                        items:
                            [
                                gridHistorico
                            ]
                    }
                ],
            buttons:
                [
                    {
                        text: 'Cerrar',
                        handler: function()
                        {
                            win.destroy();
                        }
                    }
                ]
        });

    var win = Ext.create('Ext.window.Window',
        {
            title: 'Historial de la Solicitud',
            modal: true,
            width: 600,
            closable: true,
            layout: 'fit',
            items: [formPanel]
        }).show();
}//fin de funcion de historial de solicitud

function subirContrato(data) 
{     
    var formPanel = Ext.create('Ext.form.Panel',
        {
            width: 500,
            frame: true,
            bodyPadding: '10 10 0',
            defaults: {
                anchor: '100%',
                allowBlank: false,
                msgTarget: 'side',
                labelWidth: 50
            },
            items: [                
                {
                    xtype: 'filefield',
                    id: 'form-file',
                    name: 'archivo',
                    fieldLabel: 'Archivo:',                    
                    emptyText: 'Seleccione una Archivo',
                    buttonText: 'Browse',
                    buttonConfig: {
                        iconCls: 'upload-icon'
                    }
                }
            ],
            buttons: [{
                    text: 'Subir Contrato',
                    handler: function() {
                        var form = this.up('form').getForm();
                        if (form.isValid())
                        {                                                        
                            form.submit({
                                url: url_subirContrato,
                                params: {
                                    idSolicitud:      data.idSolicitud,
                                    tieneRenovacion : data.tieneRenovacion
                                },
                                waitMsg: 'Subiendo Contrato...',
                                success: function(fp, o)
                                {
                                    Ext.Msg.alert("Mensaje", o.result.respuesta, function(btn) {
                                        if (btn == 'ok')
                                        {
                                            store.load();
                                            win.destroy();                                           
                                        }
                                    });
                                },
                                failure: function(fp, o) 
                                {
                                    Ext.Msg.alert("Alerta", o.result.respuesta);
                                }
                            });                          
                        }
                    }
                }, {
                    text: 'Cancelar',
                    handler: function() {
                        this.up('form').getForm().reset();                        
                        win.destroy();
                    }
                }]
        });

    var win = Ext.create('Ext.window.Window', {
        title: 'Subir Contrato',
        modal: true,
        width: 500,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function verContratos(data)
{            
    Ext.define('ListaDetalleModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idContrato', type: 'idContrato'},
            {name: 'numeroContrato', type: 'numeroContrato'},
            {name: 'valor', type: 'valor'},
            {name: 'garantia', type: 'garantia'},
            {name: 'estado', type: 'estado'},
            {name: 'feFinContrato', type: 'feFinContrato'},
            {name: 'formaPago', type: 'formaPago'}
        ]
    });


    storeInfoContrato = Ext.create('Ext.data.JsonStore', {
        model: 'ListaDetalleModel',
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: url_contratos,
            reader: {
                type: 'json',
                root: 'encontrados',
                totalProperty: 'total'
            },
            extraParams:
            {
                idSolicitud: data.idSolicitud                
            }
        }        
    });
    
    storeInfoContrato.load();  

    var listView = Ext.create('Ext.grid.Panel', {
        width: 900,
        height: 275,
        collapsible: false,        
        dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items: [                    
                    {xtype: 'tbfill'}
                ]}],       
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeInfoContrato,
            displayInfo: true,
            displayMsg: 'Mostrando prospectos {0} - {1} of {2}',
            emptyMsg: "No hay datos para mostrar"
        }),
        store: storeInfoContrato,
        multiSelect: false,
        viewConfig: {
            emptyText: 'No hay datos para mostrar'
        },
        listeners: {
            itemdblclick: function(view, record, item, index, eventobj, obj) {
                var position = view.getPositionByEvent(eventobj),
                    data = record.data,
                    value = data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title: 'Copiar texto?',
                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    buttons: Ext.Msg.OK,
                    icon: Ext.Msg.INFORMATION
                });
            }
        },
        columns: [new Ext.grid.RowNumberer(),
            {
                text: 'Numero de contrato',
                width: 110,
                dataIndex: 'numeroContrato'
            }, {
                text: 'Valor',
                dataIndex: 'valor',
                align: 'right',
                width: 70
            }, {
                text: 'Garantia',
                dataIndex: 'garantia',
                align: 'right',
                width: 70
            }, 
            {
                text: 'Fecha fin contrato',
                dataIndex: 'feFinContrato',
                align: 'right',
                flex: 90               
            },
             {
                text: 'Forma de Pago',
                dataIndex: 'formaPago',
                align: 'right',
                flex: 80               
            },{
                text: 'Estado',
                dataIndex: 'estado',
                align: 'right',
                width: 100
            }          
        ]
    });         
                
    var win = Ext.create('Ext.window.Window', {
        title: 'Historial de Contrato',
        modal: true,
        width: 700,
        closable: true,
        layout: 'fit',
        items: [listView]
    }).show();

}

function renovarContrato(data)
{             
    var conn = requestMask('Reversando a AutorizacionTecnica para RENOVACION DE CONTRATO');    
    conn.request
        ({
            url: url_reversarRechazar,
            method: 'post',
            timeout: 300000,
            params:
                {
                    idSolicitud: data.idSolicitud,
                    tipoRechazoReverso: 'Legal',
                    tipoAccion: 'reversar',
                    observacion: 'Se reversa a AutorizadaTecnica por RENOVACION DE CONTRATO',
                    esRenovacion : true
                },
            success: function(response)
            {
                var json = Ext.JSON.decode(response.responseText);
                Ext.Msg.alert('Mensaje', json.respuesta, function(btn)
                {
                    if (btn === 'ok')
                    {                       
                        store.load({                            
                            callback: function(records, operation, success) {
                                if (success) 
                                {
                                    conn = requestMask('Obteniendo dato de Contrato Anterior');                        
                                    conn.request({
                                        method: 'POST',
                                        url: url_infoContrato,
                                        params:
                                            {
                                                idProvincia: data.idProvincia,
                                                idElemento: data.idElemento,
                                                tipoAccion: 'renovarContrato'
                                            },
                                        success: function(response)
                                        {
                                            var json = Ext.JSON.decode(response.responseText);                                                   
                                            showRenovacion(json,data.idElemento,data.idProvincia);         
                                        }
                                    });
                                } 
                                else 
                                {
                                    Ext.Msg.alert('Error ', 'Error al cargar la data de solicitudes');
                                }
                            }
                        });                                                
                    }
                });
            },
            failure: function(result)
            {
                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
            }
        });    
}

function showRenovacion(data,idElemento,idProvincia)
{    
    storeTipoPago = new Ext.data.Store({
        total: 'total', 
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url: url_formaPago,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'                
            }      ,
            extraParams: {
                estado: 'Activo'
            }
        },
        fields:
            [
                {name: 'idFormaPago', mapping: 'id_forma_pago'},
                {name: 'nombreFormaPago', mapping: 'descripcion_forma_pago'}
            ]
    });
 
    storeBanco = new Ext.data.Store({
        total: 'total', 
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url: url_banco,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'                
            }      ,
            extraParams: {
                estado: 'Activo'
            }
        },
        fields:
            [
                {name: 'idBanco', mapping: 'idBanco'},
                {name: 'nombreBanco', mapping: 'nombreBanco'}
            ]
    });
    
    storeTipoCuenta = new Ext.data.Store({
        total: 'total', 
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url: url_tipoCuenta,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'                
            }      ,
            extraParams: {
                estado: 'Activo'
            }
        },
        fields:
            [
                {name: 'idTipoCuenta', mapping: 'id_tipo_cuenta'},
                {name: 'descripcionTipoCuenta', mapping: 'descripcion_tipo_cuenta'}
            ]
    });
    
    storeRepLegal = new Ext.data.Store({
        total: 'total', 
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url: url_repLegal,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'                
            }      ,
            extraParams: {               
                tipoRol: 'Representante Legal'
            }
        },
        fields:
            [
                {name: 'login', mapping: 'login'},
                {name: 'nombres', mapping: 'nombres'}
            ]
    });       
    
    var formPanelRenov = Ext.create('Ext.form.Panel',
        {
            bodyPadding: 5,
            waitMsgTarget: true,
            fieldDefaults:
                {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget: 'side'
                },
            items:
                [
                    {
                        xtype: 'fieldset',
                        autoHeight: true,
                        width: 380,
                        items:
                            [
                                {
                                    xtype: 'displayfield',
                                    fieldLabel: 'Nodo:',
                                    id: 'nombreNodoLegal',
                                    labelStyle: 'font-weight:bold',
                                    value: data.nodo
                                },
                                {
                                    xtype: 'displayfield',
                                    fieldLabel: 'Direccion:',
                                    id: 'direccionLegal',
                                    labelStyle: 'font-weight:bold',
                                    value: data.direccion
                                },
                                {
                                    xtype: 'displayfield',
                                    fieldLabel: 'Provincia:',
                                    id: 'provinciaLegal',
                                    labelStyle: 'font-weight:bold',
                                    value: data.provincia
                                },
                                {
                                    xtype: 'displayfield',
                                    fieldLabel: 'Canton:',
                                    id: 'cantonLegal',
                                    labelStyle: 'font-weight:bold',
                                    value: data.canton
                                },
                                {
                                    xtype: 'numberfield',
                                    fieldLabel: 'Valor:',
                                    id: 'valorLegal',
                                    labelStyle: 'font-weight:bold',
                                    value: data.valor,
                                    width: 300,
                                    hideTrigger: true,
                                    useThousandSeparator: true                                    
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Contacto Legal:',
                                    id: 'contactoLegal',
                                    value: data.contactoNodo,
                                    labelStyle: 'font-weight:bold',
                                    width: 300,
                                    readOnly: true                                    
                                },
                                {
                                    xtype: 'displayfield',                                                                        
                                    value: '<div onclick="editarContactoNodo('+data.idPersona+')" \n\
                                                style="cursor:pointer;position:relative;left:15%;color:blue" \n\
                                                    align="center">\n\
                                            <img src="/public/images/editar_direccion.png"/>\n\
                                            <b>Cambiar Contacto de Nodo</b><div/>',                                    
                                    width: 300                                                                        
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'No. Contrato Nuevo:',
                                    id: 'numeroContrato',
                                    value: data.numeroContrato,
                                    readOnly: false,
                                    labelStyle: 'font-weight:bold',
                                    width: 300
                                },
                                {
                                    xtype: 'datefield',
                                    fieldLabel: 'Fecha Inicio Contrato:',
                                    id: 'feInicioContrato',
                                    name: 'feInicioContrato',
                                    editable: false,
                                    format: 'Y-m-d',
                                    value: data.fechaInicio,                                    
                                    labelStyle: 'font-weight:bold',
                                    width: 300
                                },
                                {
                                    xtype: 'combobox',
                                    fieldLabel: 'Dura Contrato:',
                                    id: 'cmbDuraContrato',
                                    name: 'cmbDuraContrato',                                    
                                    store: [
                                        ['1', '1'],
                                        ['2', '2'],
                                        ['3', '3'],
                                        ['4', '4'],
                                        ['5', '5']
                                    ],
                                    emptyText: '',
                                    editable: false,
                                    labelStyle: 'font-weight:bold',
                                    width: 300,
                                    listeners:
                                        {
                                            beforerender: function(combo) 
                                            {
                                                combo.setValue(data.duracion);
                                            },
                                            select: function(combo)
                                            {
                                                var feIniContrato = new Date(document.getElementById('feInicioContrato-inputEl').value);
                                                var mesActual = feIniContrato.getMonth();
                                                var feFinContrato = new Date(feIniContrato.setMonth(mesActual + combo.getValue() * 12));
                                                var dia = feFinContrato.getDate();
                                                var mes = (feFinContrato.getMonth()+1).toString();
                                                var anio= feFinContrato.getFullYear(); 

                                                if((""+mes.length) < 2)
                                                {
                                                    mes = "0"+ mes;
                                                }

                                                var fechaFinContratoFormat = anio + "-" + mes + "-" + dia;
                                                Ext.getCmp('feFinContrato').setValue(fechaFinContratoFormat);
                                            }
                                        }
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Fecha Fin Contrato:',
                                    id: 'feFinContrato',
                                    name: 'feFinContrato',
                                    value: data.fechaFin,
                                    minValue: data.fechaFin,
                                    readOnly: true,
                                    labelStyle: 'font-weight:bold',
                                    width: 300
                                },
                                {
                                    xtype: 'combobox',
                                    fieldLabel: 'Tipo Pago:',
                                    id: 'cmbTipoPago',
                                    name: 'cmbTipoPago',
                                    store: storeTipoPago,
                                    displayField: 'nombreFormaPago',
                                    valueField: 'idFormaPago',
                                    queryMode: "remote",
                                    emptyText: '',
                                    labelStyle: 'font-weight:bold',
                                    width: 300,
                                    listeners:
                                        {
                                            beforerender: function(combo) 
                                            {
                                                combo.setValue(data.formaPago);
                                            }                                           
                                        }
                                },                                
                                {
                                    xtype: 'combobox',
                                    fieldLabel: 'Tipo Cuenta:',
                                    id: 'cmbTipoCuenta',
                                    name: 'cmbTipoCuenta',
                                    store: storeTipoCuenta,
                                    displayField: 'descripcionTipoCuenta',
                                    valueField: 'idTipoCuenta',
                                    queryMode: "remote",
                                    emptyText: '',
                                    labelStyle: 'font-weight:bold',
                                    width: 300,                                    
                                    listeners:
                                        {
                                            beforerender: function(combo) 
                                            {
                                                combo.setValue(data.tipoCuenta);
                                                presentarBancosPorTipoCuenta(combo.getValue()); 
                                            },
                                            select: function(combo) {

                                                Ext.getCmp('cmbBanco').reset();
                                                Ext.getCmp('cmbBanco').value = "";   
                                                Ext.getCmp('cmbBanco').setDisabled(false); 
                                                Ext.getCmp('cmbBanco').setRawValue("");
                                                
                                                presentarBancosPorTipoCuenta(combo.getValue());                                                
                                            }
                                        }
                                },
                                {
                                    xtype: 'combobox',
                                    fieldLabel: 'Banco:',
                                    id: 'cmbBanco',
                                    name: 'cmbBanco',
                                    store: storeBanco,
                                    displayField: 'nombreBanco',
                                    valueField: 'idBanco',
                                    queryMode: "remote",
                                    emptyText: '',
                                    labelStyle: 'font-weight:bold',
                                    disabled:data.banco===null?true:false,
                                    width: 300,
                                    listeners:
                                        {
                                            beforerender: function(combo) 
                                            {
                                                combo.setValue(data.banco);
                                            }                                           
                                        }
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Numero Cuenta:',
                                    id: 'numeroCuenta',
                                    labelStyle: 'font-weight:bold',
                                    width: 300,
                                    value: data.numeroPago
                                },
                                {
                                    xtype: 'numberfield',
                                    fieldLabel: 'Valor Garantia:',
                                    id: 'valorGarantia',
                                    labelStyle: 'font-weight:bold',
                                    width: 300,
                                    value: data.garantia,
                                    hideTrigger: true,
                                    useThousandSeparator: true
                                },
                                {
                                    xtype: 'combobox',
                                    fieldLabel: 'Oficina:',
                                    id: 'cmbOficinaRepLegal',
                                    name: 'cmbOficinaRepLegal',
                                    store: [
                                        ['Guayaquil', 'Guayaquil'],
                                        ['Quito', 'Quito']
                                    ],
                                    emptyText: '',
                                    labelStyle: 'font-weight:bold',
                                    width: 300,
                                    listeners:
                                        {
                                            beforerender: function(combo) 
                                            {
                                                combo.setValue(data.oficina);
                                            }                                           
                                        }
                                },
                                {
                                    xtype: 'combobox',
                                    fieldLabel: 'Rep. Telconet:',
                                    id: 'cmbRepTelconet',
                                    name: 'cmbRepTelconet',
                                    store: storeRepLegal,
                                    displayField: 'nombres',
                                    valueField: 'login',
                                    queryMode: "remote",
                                    emptyText: '',
                                    labelStyle: 'font-weight:bold',
                                    width: 300,
                                    listeners:
                                        {
                                            beforerender: function(combo) 
                                            {
                                                combo.setValue(data.login);                                                
                                            }                                           
                                        }
                                }
                            ]
                    }
                ],
            buttons:
                [
                    {
                        text: 'Renovar Contrato',
                        handler: function()
                        {
                            //Se valida mini formulario para crear contratos
                                                        
                            numeroContrato   = Ext.getCmp('numeroContrato').value;
                            fechaFinContrato = Ext.getCmp('feFinContrato').value;
                            fechaIniContrato = Ext.getCmp('feInicioContrato').value;
                            cmbTipoPago      = Ext.getCmp('cmbTipoPago').value;
                            cmbBanco         = Ext.getCmp('cmbBanco').value;
                            cmbTipoCuenta    = Ext.getCmp('cmbTipoCuenta').value;
                            numeroCuenta     = Ext.getCmp('numeroCuenta').value;
                            valorGarantia    = Ext.getCmp('valorGarantia').value;
                            oficinaRepLegal  = Ext.getCmp('cmbOficinaRepLegal').value;
                            cmbRepTelconet   = Ext.getCmp('cmbRepTelconet').value;                            
                            valor            = Ext.getCmp('valorLegal').value;                                                                      
                                                                            
                            if (fechaFinContrato == "")
                            {
                                Ext.Msg.alert('Advertencia', 'No se ha establecido fecha de Fin de contrato');
                            }
                            else if (cmbTipoPago == "" || cmbTipoPago == null)
                            {
                                Ext.Msg.alert('Advertencia', 'Debe escoger el tipo de Pago');
                            }                           
                            else if (oficinaRepLegal == "")
                            {
                                Ext.Msg.alert('Advertencia', 'Debe escoger la oficina de generacion de Contrato');
                            }
                            else if (cmbRepTelconet == "")
                            {
                                Ext.Msg.alert('Advertencia', 'Debe escoger el representante Legal');
                            }
                            else
                            {                                
                                var conn = requestMask('Autorizando Renovacion Solicitud Legal...');                                
                                conn.request
                                    ({
                                        url: url_renovarContrato,
                                        method: 'post',
                                        timeout: 300000,
                                        params:
                                            {                                      
                                                idElemento      : idElemento,
                                                numeroContrato  : numeroContrato,
                                                fechaFinContrato: fechaFinContrato,
                                                fechaIniContrato: fechaIniContrato,
                                                formaPago       : cmbTipoPago,
                                                banco           : cmbBanco,
                                                tipoCuenta      : cmbTipoCuenta,
                                                numeroCuenta    : numeroCuenta,
                                                valorGarantia   : valorGarantia,
                                                ofiRepLegal     : oficinaRepLegal,
                                                repLegal        : cmbRepTelconet,
                                                provincia       : idProvincia,
                                                valor           : valor,
                                                idSolicitud     : data.solicitud,
                                                idContratoAnt   : data.idContrato,
                                                contratoAnterior: data.numeroContAnt,
                                                yaexiste        : $("#hdExiste").val(),
                                                yaexisteRol     : $("#hdContactoTieneRol").val(),
                                                cambioContacto  : $("#hdCambioContacto").val(),
                                                idPersona       : $("#hdIdPersona").val()==="0"?data.idPersona:$("#hdIdPersona").val(),
                                                tipoContacto    : $("#hdTipoContacto").val(),
                                                tipoIdentificacion:$("#hdTipoIdentificacion").val(),
                                                identificacion  : $("#hdIdentificacion").val(),
                                                tipoTributario  : $("#hdTipoTributario").val(),
                                                tipoGenero      : $("#hdGenero").val(),
                                                tipoNacionalidad: $("#hdNacionalidad").val(),
                                                tipoTitulo      : $("#hdTitulo").val(),
                                                nombres         : $("#hdNombres").val(),
                                                apellidos       : $("#hdApellidos").val(),
                                                razonSocial     : $("#hdRazonSocial").val()==="N/A"?data.contactoNodo:$("#hdRazonSocial").val(),
                                                formasContacto  : $("#hdFormasDeContacto").val()
                                            },
                                        success: function(response)
                                        {
                                            Ext.get(formPanelRenov.getId()).unmask();
                                            var json = Ext.JSON.decode(response.responseText);
                                            Ext.Msg.alert('Mensaje', json.respuesta, function(btn)
                                            {
                                                if (btn === 'ok')
                                                {
                                                    store.load();
                                                    winRenov.destroy();
                                                }
                                            });
                                        },
                                        failure: function(result)
                                        {
                                            Ext.get(formPanelRenov.getId()).unmask();
                                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                        }
                                    });
                            }

                        }
                    },
                    {
                        text: 'Cancelar',
                        handler: function()
                        {
                            Ext.get(document.body).unmask();
                            winRenov.destroy();
                        }
                    }
                ]
        });

    var winRenov = Ext.create('Ext.window.Window',
        {
            title: 'Renovacion Contrato de Nodo - Legal',
            modal: true,
            width: 400,
            closable: true,
            layout: 'fit',
            items: [formPanelRenov]
        }).show();
}

function buscar()
{
    store.getProxy().extraParams.estadoSolicitud = Ext.getCmp('estadoCmb').value;
    store.getProxy().extraParams.nombreElemento = Ext.getCmp('nombreNodoTxt').value;
    store.load();
}

function limpiar()
{
    Ext.getCmp('estadoCmb').value = "Pendiente";
    Ext.getCmp('estadoCmb').setRawValue("Pendiente");

    Ext.getCmp('nombreNodoTxt').value = "";
    Ext.getCmp('nombreNodoTxt').setRawValue("");

    store.load({params: {
            nombreElemento: '',
            estadoSolicitud: 'Pendiente',
        }});

}

function requestMask(msg)
{
    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function(con, opt) {
                    Ext.getBody().mask(msg);
                },
                scope: this
            },
            'requestcomplete': {
                fn: function(con, res, opt) {
                    Ext.getBody().unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function(con, res, opt) {
                    Ext.getBody().unmask();
                },
                scope: this
            }
        }
    });
    
    return conn;
}

function presentarBancosPorTipoCuenta(tipoCuentaId) 
{
    storeBanco.proxy.extraParams = {tipoCuentaId: tipoCuentaId};
    storeBanco.load();
}

function presentarBancos(tipoCuentaId, nombreBanco){
    storeBanco.proxy.extraParams = {tipoCuentaId: tipoCuentaId, nombreBanco:nombreBanco};
    storeBanco.load();
}

function editarContactoNodo(idPersona)
{
    storeTipoContactoNodo = new Ext.data.Store({
        total: 'total', 
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url: url_infoEditContacto,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'                
            }      ,
            extraParams: {
                tipoInfo: 'rol'
            }
        },
        fields:
            [
                {name: 'idRol', mapping: 'idRol'},
                {name: 'nombreRol', mapping: 'nombreRol'}
            ]
    });
    
    storeTitulo = new Ext.data.Store({
        total: 'total', 
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url: url_infoEditContacto,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'                
            }      ,
            extraParams: {
                tipoInfo: 'titulo'
            }
        },
        fields:
            [
                {name: 'idTitulo', mapping: 'idTitulo'},
                {name: 'titulo', mapping: 'titulo'}
            ]
    });
    
    Ext.define('PersonaFormasContactoModel', {
        extend: 'Ext.data.Model',
        fields: [
            // the 'name' below matches the tag name to read, except 'availDate'
            // which is mapped to the tag 'availability'
            {name: 'idPersonaFormaContacto', type: 'int'},
            {name: 'formaContacto'},
            {name: 'valor', type: 'string'}
        ]
    });
    
    Ext.define('FormasContactoModel', {
        extend: 'Ext.data.Model',
        fields: [           
            {name: 'id', type: 'int'},
            {name: 'descripcion', type: 'string'}
        ]
    });
        
    storeContactos = Ext.create('Ext.data.Store', {        
        autoDestroy: true,
        model: 'PersonaFormasContactoModel',
        proxy: {
            type: 'ajax',            
            url: url_formas_contacto_persona,
            reader: {
                type: 'json',
                root: 'personaFormasContacto',                
                totalProperty: 'total'
            },
            extraParams: {personaid: ''},
            simpleSortMode: true
        }
    });
    
    var storeFormasContacto = Ext.create('Ext.data.Store', {        
        autoDestroy: true,
        model: 'FormasContactoModel',
        proxy: {
            type: 'ajax',            
            url: url_formas_contacto,
            reader: {
                type: 'json',
                root: 'formasContacto'
            }
        }
    });

    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 1
    });
    
    gridContactoNodo = Ext.create('Ext.grid.Panel', {
        id:'gridContactoNodo',
        store: storeContactos,
        columns: [{
                text: 'Forma Contacto',
                header: 'Forma Contacto',
                dataIndex: 'formaContacto',
                width: 150,
                editor: new Ext.form.field.ComboBox({
                    typeAhead: true,
                    triggerAction: 'all',
                    selectOnTab: true,
                    id: 'id',
                    name: 'formaContacto',
                    valueField: 'descripcion',
                    displayField: 'descripcion',
                    store: storeFormasContacto,
                    lazyRender: true,
                    listClass: 'x-combo-list-small'
                })
            }, {
                text: 'Valor',                
                dataIndex: 'valor',
                width: 200,
                align: 'right',
                editor: {
                    width: '80%',
                    xtype: 'textfield',
                    allowBlank: false
                }
            }, {
                xtype: 'actioncolumn',
                width: 35,
                sortable: false,
                items: [{
                        iconCls: "button-grid-delete",
                        tooltip: 'Borrar Forma Contacto',
                        handler: function(grid, rowIndex, colIndex) 
                        {                            
                            storeContactos.removeAt(rowIndex);                            
                        }
                    }]
            }],
        selModel: {
            selType: 'cellmodel'
        },        
        width: 400,
        height: 200,
        title: '',        
        tbar: [{
                text: 'Agregar',
                handler: function() 
                {                       
                    var r = Ext.create('PersonaFormasContactoModel', {
                        idPersonaFormaContacto: '',
                        formaContacto: '',
                        valor: ''
                    });
                    storeContactos.insert(0, r);
                    cellEditing.startEditByPosition({row: 0, column: 0});                    
                }
            }],
        plugins: [cellEditing]
    }); 
    
    var formPanelContacto = Ext.create('Ext.form.Panel',
        {
            bodyPadding: 5,
            waitMsgTarget: true,
            fieldDefaults:
                {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget: 'side'
                },
            items:
                [
                    {
                        xtype: 'fieldset',
                        autoHeight: true,
                        width: 480,
                        items:
                            [
                                {
                                    xtype: 'combobox',
                                    fieldLabel: 'Tipo Contacto:',
                                    id: 'cmbContactoNodo',
                                    name: 'cmbContactoNodo',
                                    store: storeTipoContactoNodo,
                                    displayField: 'nombreRol',
                                    valueField: 'idRol',
                                    queryMode: "remote",
                                    emptyText: '',
                                    labelStyle: 'font-weight:bold',
                                    width: 300                                    
                                },
                                {
                                    xtype: 'combobox',
                                    fieldLabel: 'Tipo Identificacion:',
                                    id: 'cmbTipoIdentificacion',
                                    name: 'cmbTipoIdentificacion',                                    
                                    store: [
                                        ['CED', 'Cedula'],
                                        ['RUC', 'Ruc'],
                                        ['PAS', 'Pasaporte']                                        
                                    ],                                   
                                    editable: false,
                                    labelStyle: 'font-weight:bold',
                                    width: 300,
                                    listeners:
                                        {
                                            select: function(combo) 
                                            {
                                                Ext.getCmp('identificacion').setDisabled(false); 
                                                Ext.getCmp('identificacion').value = "";
                                                Ext.getCmp('identificacion').setRawValue("");
                                                Ext.getCmp('razonSocial').setDisabled(true);
                                                switch(combo.getValue())
                                                {
                                                    case 'CED':
                                                        $('#identificacion-inputEl').attr('maxlength','10');
                                                        break;
                                                    case 'RUC':
                                                        $('#identificacion-inputEl').attr('maxlength','13');                    
                                                        break;
                                                    case 'PAS':
                                                        $('#identificacion-inputEl').attr('maxlength','20');
                                                        break;
                                                }                                                
                                            }                                           
                                        }
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Identificacion:',
                                    id: 'identificacion',
                                    labelStyle: 'font-weight:bold',                                    
                                    width: 300,
                                    disabled:true                                    
                                },
                                {
                                    xtype: 'displayfield',                                                                        
                                    value: '<div onclick="validarIdentificacion('+idPersona+')" \n\
                                                    style="cursor:pointer;position:relative;left:15%;color:blue" \n\
                                                 align="center">\n\
                                            <img src="/public/images/search.png"/>\n\
                                            <b>Verificar Identificacion existe</b><div/>',                                    
                                    width: 300                                                                        
                                },
                                {
                                    xtype: 'combobox',
                                    fieldLabel: 'Tipo Tributario:',
                                    id: 'cmbTipoTributario',
                                    name: 'cmbTipoTributario',                                    
                                    store: [
                                        ['NAT', 'Natural'],
                                        ['JUR', 'Juridico']                                        
                                    ],                                   
                                    editable: false,
                                    labelStyle: 'font-weight:bold',
                                    width: 300,
                                    disabled:true
                                },
                                {
                                    xtype: 'combobox',
                                    fieldLabel: 'Genero:',
                                    id: 'cmbGenero',
                                    name: 'cmbGenero',                                    
                                    store: [
                                        ['M', 'Masculino'],
                                        ['F', 'Femenino']                                        
                                    ],                                   
                                    editable: false,
                                    labelStyle: 'font-weight:bold',
                                    width: 300,
                                    disabled:true
                                },
                                {
                                    xtype: 'combobox',
                                    fieldLabel: 'Nacionalidad:',
                                    id: 'cmbNacionalidad',
                                    name: 'cmbNacionalidad',                                    
                                    store: [
                                        ['NAC', 'Nacional'],
                                        ['EXT', 'Extranjera']                                        
                                    ],                                   
                                    editable: false,
                                    labelStyle: 'font-weight:bold',
                                    width: 300,
                                    disabled:true
                                },
                                {
                                    xtype: 'combobox',
                                    fieldLabel: 'Titulo:',
                                    id: 'cmbTitulo',
                                    name: 'cmbTitulo',
                                    store: storeTitulo,
                                    displayField: 'titulo',
                                    valueField: 'idTitulo',
                                    queryMode: "remote",
                                    emptyText: '',
                                    labelStyle: 'font-weight:bold',
                                    width: 300,
                                    disabled:true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Nombres:',
                                    id: 'nombres',
                                    labelStyle: 'font-weight:bold',                                    
                                    width: 300,
                                    disabled:true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Apellidos:',
                                    id: 'apellidos',
                                    labelStyle: 'font-weight:bold',                                    
                                    width: 300,
                                    disabled:true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Razon Social:',
                                    id: 'razonSocial',
                                    labelStyle: 'font-weight:bold',                                    
                                    width: 300,
                                    disabled:true
                                },
                                gridContactoNodo
                            ]
                    }
                ],
            buttons:
                [
                    {
                        text: 'Guardar',
                        handler: function()
                        {                                                                                                          
                            if (!validarDatosContactoNodo())
                            {
                                return;
                            }
                            else
                            {
                                //Se envia a inputs tipo hidden los valores obtenidos para luego ser procesados en la actualizacion del contrato
                                infoContacto = obtenerInformacionGridInformacionContacto();
                                if (infoContacto)
                                {
                                    document.getElementById('hdFormasDeContacto').value = infoContacto;
                                }
                                else
                                {
                                    return;
                                }
                                document.getElementById('hdTipoContacto').value       = Ext.getCmp('cmbContactoNodo').value;
                                document.getElementById('hdTipoIdentificacion').value = Ext.getCmp('cmbTipoIdentificacion').value;
                                document.getElementById('hdIdentificacion').value     = Ext.getCmp('identificacion').value;
                                document.getElementById('hdTipoTributario').value     = Ext.getCmp('cmbTipoTributario').value;
                                document.getElementById('hdGenero').value             = Ext.getCmp('cmbGenero').value;
                                document.getElementById('hdNacionalidad').value       = Ext.getCmp('cmbNacionalidad').value;
                                document.getElementById('hdTitulo').value             = Ext.getCmp('cmbTitulo').value;
                                document.getElementById('hdNombres').value            = Ext.getCmp('nombres').value;
                                document.getElementById('hdApellidos').value          = Ext.getCmp('apellidos').value;
                                document.getElementById('hdRazonSocial').value        = Ext.getCmp('razonSocial').value;
                                
                                var nombreAMostrar = Ext.getCmp('razonSocial').value!==''?Ext.getCmp('razonSocial').value:
                                                     Ext.getCmp('nombres').value+" "+Ext.getCmp('apellidos').value;
                                
                                Ext.getCmp("contactoLegal").setRawValue(nombreAMostrar);
                                //Si la persona que carga es diferente al contacto inicial significa que cambio de contacto
                                if (idPersona === $("#hdIdPersona").val())
                                {
                                    document.getElementById('hdCambioContacto').value = 'N';
                                }
                                else
                                {
                                    document.getElementById('hdCambioContacto').value = 'S';
                                }

                                winContactoNodo.destroy();
                            }
                        }
                    },
                    {
                        text: 'Cancelar',
                        handler: function()
                        {                            
                            winContactoNodo.destroy();
                        }
                    }
                ]
        });
    var winContactoNodo = Ext.create('Ext.window.Window',
        {
            title: 'Actualizar Contacto del Nodo',
            modal: true,
            width: 450,            
            closable: true,
            layout: 'fit',
            items: [formPanelContacto]
        }).show();
}

function validarIdentificacion(idPersona)
{
    var identificacion     = Ext.getCmp('identificacion').value;
    var tipoIdentificacion = Ext.getCmp('cmbTipoIdentificacion').value;
    var identificacionEsCorrecta = false;    
    var rol = 'Contacto Nodo';
    
    Ext.getCmp('cmbTipoTributario').setDisabled(true);
    Ext.getCmp('cmbTitulo').setDisabled(true);
    Ext.getCmp('cmbNacionalidad').setDisabled(true);
    Ext.getCmp('cmbGenero').setDisabled(true);
    Ext.getCmp('nombres').setDisabled(true);
    Ext.getCmp('apellidos').setDisabled(true);
    Ext.getCmp('razonSocial').setDisabled(true);
    
    Ext.ComponentQuery.query('#cmbTipoTributario')[0].setValue("");
    Ext.ComponentQuery.query('#cmbTitulo')[0].setValue("");
    Ext.ComponentQuery.query('#cmbNacionalidad')[0].setValue("");                             
    Ext.ComponentQuery.query('#cmbGenero')[0].setValue("");    
    Ext.ComponentQuery.query('#nombres')[0].setValue(""); 
    Ext.ComponentQuery.query('#apellidos')[0].setValue(""); 
    Ext.ComponentQuery.query('#razonSocial')[0].setValue(""); 
    storeContactos.removeAll();
    
    if(tipoIdentificacion === null)
    {
        Ext.Msg.alert('Advertencia', 'Debe escoger el tipo de identificacion');
        return;
    }
    else if(identificacion==="")
    {
        Ext.Msg.alert('Advertencia', 'Debe escribir la identificacion del contacto');
        return;
    }    
    //Se verifica formato
    if (/^[\w]+$/.test(identificacion) && (tipoIdentificacion === 'PAS')) 
    {
        identificacionEsCorrecta = true;
    }
    if (/^\d+$/.test(identificacion) && (tipoIdentificacion === 'RUC' || tipoIdentificacion === 'CED'))
    {
        identificacionEsCorrecta = true;
    }
    
    if(identificacionEsCorrecta)
    {
        var conn = requestMask('Verificando Identificacion');        
        conn.request({
            method: 'POST',
            url: url_validar_identificacion_tipo,
            params:
                {
                    identificacion: identificacion,   
                    tipo          : tipoIdentificacion
                },
            success: function(response)
            {
                var response = response.responseText;            
                if(response !== "")
                {
                    Ext.Msg.alert('Error', response);
                    return;
                }
                else
                {
                    conn.request({
                        method: 'POST',
                        url: url_valida_identificacion,
                        params:
                            {
                                identificacion: identificacion                    
                            },
                        success: function(response)
                        {
                            var response = response.responseText;                           

                            if(response !== "no")
                            {
                                var data = Ext.JSON.decode(response)[0];   
                                                                
                                arrayRoles = data.roles.split("|");
                                
                                tieneRol = false;
                                
                                for (var i = 0; i < arrayRoles.length; i++) 
                                {                                                                            
                                    if (rol === arrayRoles[i]) 
                                    {
                                        tieneRol = true;
                                        break;
                                    }
                                }
                                
                                if(tieneRol)
                                {
                                    $("#hdContactoTieneRol").val('S');                                    
                                }
                                else
                                {
                                    $("#hdContactoTieneRol").val('N');                                    
                                }                            
                                                               
                                Ext.ComponentQuery.query('#cmbTipoTributario')[0].setValue(data.tipoTributario);
                                Ext.ComponentQuery.query('#cmbTitulo')[0].setValue(data.tituloId);
                                Ext.ComponentQuery.query('#cmbNacionalidad')[0].setValue(data.nacionalidad);
                                Ext.ComponentQuery.query('#cmbGenero')[0].setValue(data.genero);
                                Ext.ComponentQuery.query('#nombres')[0].setValue(data.nombres);
                                Ext.ComponentQuery.query('#apellidos')[0].setValue(data.apellidos);
                                Ext.ComponentQuery.query('#razonSocial')[0].setValue(data.razonSocial !== null ?
                                    data.razonSocial : data.nombres + " " + data.apellidos); 
                                
                                $("#hdIdPersona").val(data.id); 
                                $("#hdExiste").val('S'); 
                                
                                if(idPersona === data.id)
                                {                                    
                                    esContactoNuevo = false;
                                }                                
                                            
                                if(tipoIdentificacion === 'RUC')
                                {
                                    Ext.getCmp('razonSocial').setDisabled(true);
                                }       
                                else
                                {
                                    Ext.getCmp('nombres').setDisabled(true);
                                    Ext.getCmp('apellidos').setDisabled(true);
                                }
                                //Cargando informacion de formas de contacto
                                storeContactos.removeAll();
                                storeContactos.load({params: {personaid: data.id}});                                                                
                            }  
                            else
                            {
                                Ext.Msg.alert('Info', 'Identificacion Correcta, ingrese nuevo contacto');  
                                if(tipoIdentificacion === 'RUC')
                                {
                                    Ext.getCmp('razonSocial').setDisabled(false);
                                }
                                //Se carga la informacion del cliente
                                Ext.getCmp('cmbTipoTributario').setDisabled(false);
                                Ext.getCmp('cmbGenero').setDisabled(false);
                                Ext.getCmp('cmbNacionalidad').setDisabled(false);
                                Ext.getCmp('cmbTitulo').setDisabled(false);
                                Ext.getCmp('nombres').setDisabled(false);
                                Ext.getCmp('apellidos').setDisabled(false);
                                Ext.getCmp('razonSocial').setDisabled(false);
                                $("#hdExiste").val('N'); 
                                esContactoNuevo = true;
                            }                                                        
                        }
                    });
                }                                
            }
        });        
    }
    else
    {
        Ext.Msg.alert('Advertencia', 'Identificacion es incorrecta por favor vuelva a ingresarla, no se permite caracteres especiales');
        return;
    }
}

function obtenerInformacionGridInformacionContacto()
{       
    var array = new Object();
    
    var grid = gridContactoNodo;
    
    array['total'] =  grid.getStore().getCount();
    array['data']  = new Array();
    
    if(grid.getStore().getCount()!==0)
    {    
        var array_data = new Array();
        
        for (var i = 0; i < grid.getStore().getCount(); i++)
        {                                                
            if(grid.getStore().getAt(i).data.formaContacto === null || grid.getStore().getAt(i).data.formaContacto === "" )
            {
                Ext.Msg.alert("Advertencia","Debe elegir una forma de contacto del Nodo");
                return false;
            }
            if(grid.getStore().getAt(i).data.valor === "")
            {
                Ext.Msg.alert("Advertencia","Existen valores de forma de contacto sin llenar");
                return false;
            }            
            else
            {
               array_data.push(grid.getStore().getAt(i).data);
            }
        }
        array['data'] = array_data;          

        return Ext.JSON.encode(array);     
    }
    else
    {
        Ext.Msg.alert("Advertencia","No ha ingresado la Informacion de Contacto del Nodo");
        return false;
    }

}

function validarDatosContactoNodo()
{    
    tipoTributario = Ext.getCmp('cmbTipoTributario').value;
    
    if(Ext.getCmp('cmbContactoNodo').value===null)
    {
        Ext.Msg.alert('Advertencia', 'Debe escoger el Tipo de Contacto');
        return false;
    }
    if (Ext.getCmp('cmbTipoIdentificacion').value === null)
    {
        Ext.Msg.alert('Advertencia', 'Debe escoger el Tipo de Identificacion');
        return false;
    }
    if (Ext.getCmp('identificacion').value === "")
    {
        Ext.Msg.alert('Advertencia', 'Debe ingresar la Identificacion del Contacto');
        return false;
    }
    if (Ext.getCmp('cmbTipoTributario').value === "")
    {
        Ext.Msg.alert('Advertencia', 'Debe escoger el Tipo Tributario del Contacto');
        return false;
    }
    
    if(tipoTributario !== 'JUR')
    {                
        if (Ext.getCmp('cmbGenero').value === "")
        {
            Ext.Msg.alert('Advertencia', 'Debe escoger el Genero del Contacto');
            return false;
        }
        if (Ext.getCmp('cmbNacionalidad').value === "")
        {
            Ext.Msg.alert('Advertencia', 'Debe escoger la Nacionalidad del Contacto');
            return false;
        }
        if (Ext.getCmp('cmbTitulo').value === "")
        {
            Ext.Msg.alert('Advertencia', 'Debe escoger el Título del Contacto');
            return false;
        }
        if (Ext.getCmp('nombres').value === "")
        {
            Ext.Msg.alert('Advertencia', 'Debe ingresar los Nombres del Contacto');
            return false;
        }
        if (Ext.getCmp('apellidos').value === "")
        {
            Ext.Msg.alert('Advertencia', 'Debe ingresar los Apellidos del Contacto');
            return false;
        }                 
    }
    else
    {
        if (Ext.getCmp('razonSocial').value === "")
        {
            Ext.Msg.alert('Advertencia', 'Debe ingresar la Razón Social del Contacto');
            return false;
        } 
    }
    
    if(!validaFormasContacto())
    {    
        return false;        
    }
    
    return true;
}

function validaFormasContacto() 
{
    var array_telefonos = new Array();
    var array_correos   = new Array();
        
    var telefonosOk = false;
    var correosOk   = false;
    
    var existenCorreosFallidos = false;
    var existenFonosFallidos   = false;    
    var existeFormaContacto    = false;   
    
    for (var i = 0; i < gridContactoNodo.getStore().getCount(); i++)
    {
        var variable = gridContactoNodo.getStore().getAt(i).data;  

        if (variable.formaContacto.toUpperCase().match(/^TELEFONO.*$/)) 
        {                    
            array_telefonos.push(variable.valor);       
            existeFormaContacto = true;
        }
        if (variable.formaContacto.toUpperCase().match(/^CORREO.*$/)) 
        { 
            array_correos.push(variable.valor);                              
            existeFormaContacto = true;
        }               
    }
        
    var valorErroneo = '';
    //Valida que exista al menos una forma de contacto
    if (existeFormaContacto) 
    {
        //Verificar si existen correos con errores
        for (i = 0; i < array_correos.length; i++) 
        {              
            correosOk = validaCorreo(array_correos[i].toLowerCase());                        
            //Si existe al menos un correo erroneo no deja continuar
            if(!correosOk)
            {                
                valorErroneo = array_correos[i];
                existenCorreosFallidos = true;
                break;
            }
        }
                
        if(existenCorreosFallidos)
        {
            if(valorErroneo!=='')
            {
                Ext.Msg.alert("Error", "El correo <b>"+valorErroneo+"</b> contiene errores o está mal formado, por favor corregir.");
            }
            else
            {
                Ext.Msg.alert("Error", "Ingresar el valor del correo a agregar");
            }
            return false;
        }
        
        valorErroneo = '';
        
        //Verificar si existen telefonos con errores
        for (i = 0; i < array_telefonos.length; i++) 
        {                
            telefonosOk = validaTelefono(array_telefonos[i]);

            if(!telefonosOk)
            {
                valorErroneo = array_telefonos[i];
                existenFonosFallidos = true;
                break;
            }
        }           
       
        if(existenFonosFallidos)
        {
            if(valorErroneo!=='')
            {
                Ext.Msg.alert("Error", "El Teléfono <b>"+valorErroneo+"</b> está mal formado, por favor corregir.");
            }
            else
            {
                Ext.Msg.alert("Error", "Ingresar el valor del Teléfono a agregar");
            }            
            return false;
        }
        
        return true;
    }
    else
    {
        Ext.Msg.alert("Error", "Debe Ingresar al menos una Forma de Contacto");
        return false;
    }			
}

function validaTelefono(telefono) {
    var RegExPattern = Utils.REGEX_FONE_MIN8MAX10;
    if(telefono.indexOf("593") === 0)
    {
        telefono = telefono.replace("593", "0");
    }
    if ((telefono.match(RegExPattern)) && (telefono.value != '') && (telefono.length > 8)) {
        return true;
    } else {
        return false;
    }
}

function validaCorreo(correo) {
    var RegExPattern = Utils.REGEX_MAIL;
    if ((correo.match(RegExPattern)) && (correo.value != '')) {
        return true;
    } else {
        return false;
    }
}

function verResumenNodo(data)
{
        
    //Contactos de nodo
    var storeContactoNodo = new Ext.data.Store({
        proxy: {
            type: 'ajax',
            url: url_infoContactoNodo,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                idNodo: data.idElemento
            }
        },
        fields:
            [
                {name: 'descripcionRol', mapping: 'descripcionRol'},
                {name: 'tipoIdentificacion', mapping: 'tipoIdentificacion'},
                {name: 'identificacionCliente', mapping: 'identificacionCliente'},
                {name: 'nombres',  mapping: 'nombres'},
                {name: 'apellidos',  mapping: 'apellidos'},
                {name: 'idPersona',  mapping: 'idPersona'},                
                {name: 'razonSocial',  mapping: 'razonSocial'},
                {name: 'genero',  mapping: 'genero'},
                {name: 'tipoTributario',  mapping: 'tipoTributario'}
                
            ],
         autoLoad: true
    });
    
     var gridContacto = Ext.create('Ext.grid.Panel', {
        id: 'gridContacto',
        store: storeContactoNodo,
        loadMask: true,
        frame: false,
        columns: [
            {
                id: 'idPersona',
                header: 'idPersona',
                dataIndex: 'idPersona',
                hidden: true,
                hideable: false
            },
            {
                id: 'descripcionRol',
                header: 'Tipo Contacto',
                dataIndex: 'descripcionRol',
                width: 100,
                sortable: true
            },
            {
                id: 'tipoIdentificacion',
                header: 'Tipo Identificación',
                dataIndex: 'tipoIdentificacion',
                width: 100 ,    
                sortable: true
            }, {
                id: 'identificacionCliente',
                header: 'Identificación',
                dataIndex: 'identificacionCliente',
                width: 80                
            },
            {
                id: 'nombres',
                header: 'Nombres',
                dataIndex: 'nombres',
                width: 150                
            }, 
            {
                id: 'apellidos',
                header: 'Apellidos',
                dataIndex: 'apellidos',
                width: 150                
            },
            {
                id: 'razonSocial',
                header: 'Razón Social',
                dataIndex: 'razonSocial',
                width: 220                               
            } ,            
            {
                id: 'tipoTributario',
                header: 'Tipo Tributario',
                dataIndex: 'tipoTributario',
                width: 80                               
            }            
        ],
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeContactoNodo,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        viewConfig: {
            stripeRows: true
        },
        width: 820,
        height: 180,
        title: 'Informacion de Contacto'        
    });             
    
    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function(con, opt) {
                    Ext.get(document.body).mask("Cargando Información del Nodo...");
                },
                scope: this
            },
            'requestcomplete': {
                fn: function(con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function(con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    });

    conn.request
        ({
            url: url_getResumenNodo,
            method: 'post',
            timeout: 300000,
            params:
                {
                    idElemento: data.idElemento                   
                },
            success: function(response)
            {
                Ext.get(document.body).unmask();
                var datosNodo = Ext.JSON.decode(response.responseText);                                
                
                //Se carga la información del Nodo
                var formPanel = Ext.create('Ext.form.Panel', {
                    bodyPadding: 2,
                    waitMsgTarget: true,
                    fieldDefaults: {
                        labelAlign: 'left',
                        labelWidth: 85,
                        msgTarget: 'side'
                    },
                    items: [{
                            xtype: 'fieldset',
                            defaultType: 'textfield',
                            defaults: {
                                width: 855
                            },
                            items: [
                                //informacion del cliente
                                {
                                    xtype: 'fieldset',
                                    title: '<b>Información General del Nodo</b>',
                                    defaultType: 'textfield',
                                    items: [
                                        {
                                            xtype: 'container',
                                            layout: {
                                                type: 'table',
                                                columns: 7,
                                                align: 'stretch'
                                            },
                                            items: [
                                                {width: 10, border: false},
                                                {
                                                    xtype: 'displayfield',
                                                    fieldLabel: 'Nodo:',                                                    
                                                    labelStyle: 'font-weight:bold',
                                                    width:300,
                                                    fieldStyle: 'font-weight:bold;color:green;',
                                                    value: datosNodo.nombreElemento
                                                },          
                                                {width: 5, border: false},
                                                {
                                                    xtype: 'displayfield',
                                                    fieldLabel: 'Valor:',                                                    
                                                    labelStyle: 'font-weight:bold',
                                                    fieldStyle: 'font-weight:bold;color:green;',
                                                    width:300,
                                                    value: '$ '+datosNodo.valor
                                                },
                                                {width: 5, border: false},
                                                {width: 5, border: false},
                                                {width: 10, border: false},
                                                
                                                //-------------------------------------
                                                   
                                                {width: 10, border: false},                                                    
                                                {
                                                    xtype: 'displayfield',
                                                    fieldLabel: 'Motivo:',                                                    
                                                    labelStyle: 'font-weight:bold',
                                                    width:300,
                                                    value: datosNodo.nombreMotivo
                                                },
                                                {width: 5, border: false},
                                                {
                                                    xtype: 'displayfield',
                                                    fieldLabel: 'Factible Torre:',                                                    
                                                    labelStyle: 'font-weight:bold',
                                                    width:300,
                                                    value: datosNodo.torre
                                                },
                                                {width: 5, border: false},
                                                {
                                                    xtype: 'displayfield',
                                                    fieldLabel: 'Altura Torre:',                                                    
                                                    labelStyle: 'font-weight:bold',
                                                    width:300,
                                                    value: datosNodo.alturaTorre+" m"
                                                },
                                                {width: 10, border: false},
                                                
                                                //--------------------------------------
                                                
                                                {width: 10, border: false},
                                                {
                                                    xtype: 'displayfield',
                                                    fieldLabel: '# Medidor:',                                                    
                                                    labelStyle: 'font-weight:bold;',
                                                    width:300,
                                                    value: datosNodo.numeroMedidor
                                                },
                                                {width: 5, border: false},                                               
                                                {
                                                    xtype: 'displayfield',
                                                    fieldLabel: 'Clase Medidor:',                                                    
                                                    labelStyle: 'font-weight:bold',
                                                    width:300,
                                                    value: datosNodo.nombreClaseMedidor
                                                },
                                                {width: 5, border: false},
                                                {
                                                    xtype: 'displayfield',
                                                    fieldLabel: 'Tipo Medidor:',                                                    
                                                    labelStyle: 'font-weight:bold',
                                                    width:300,
                                                    value: datosNodo.nombreTipoMedidor
                                                },
                                                {width: 10, border: false},
                                                
                                                //--------------------------------------
                                                
                                                {width: 10, border: false},
                                                {
                                                    xtype: 'displayfield',
                                                    fieldLabel: 'Clase Nodo:',                                                    
                                                    labelStyle: 'font-weight:bold;',
                                                    width:300,
                                                    value: datosNodo.clase
                                                },
                                                {width: 5, border: false},                                               
                                                {
                                                    xtype: 'displayfield',
                                                    fieldLabel: 'Tipo Nodo:',                                                    
                                                    labelStyle: 'font-weight:bold',
                                                    width:300,
                                                    value: datosNodo.tipoMedio
                                                },
                                                {width: 5, border: false},
                                                {
                                                    xtype: 'displayfield',
                                                    fieldLabel: 'Medidor Eléctrico:',                                                    
                                                    labelStyle: 'font-weight:bold',
                                                    width:300,
                                                    value: datosNodo.medidorElectrico
                                                },
                                                {width: 10, border: false},
                                                
                                                //--------------------------------------
                                                
                                                {width: 10, border: false},
                                                {
                                                    xtype: 'displayfield',
                                                    fieldLabel: 'Observación:',                                                    
                                                    labelStyle: 'font-weight:bold;',
                                                    width:300,
                                                    value: datosNodo.observacion
                                                }
                                            ]
                                        }

                                    ]
                                }, //cierre de la informacion del cliente                               
                                {
                                    xtype: 'fieldset',
                                    title: '<b>Información de Localidad del Nodo<b>',
                                    defaultType: 'textfield',
                                    style: "font-weight:bold; margin-bottom: 5px;",
                                    layout: {
                                        tdAttrs: {style: 'padding: 5px;'},
                                        type: 'table',
                                        columns: 2,
                                        pack: 'center'
                                    },
                                    items: [
                                                                               
                                        {
                                            xtype: 'displayfield',
                                            fieldLabel: 'Región:',
                                            labelStyle: 'font-weight:bold',
                                            width: 300,
                                            value: datosNodo.nombreRegion
                                        },
                                        {
                                            xtype: 'displayfield',
                                            fieldLabel: 'Provincia:',
                                            labelStyle: 'font-weight:bold',
                                            width: 300,
                                            value: datosNodo.nombreProvincia
                                        },
                                        {
                                            xtype: 'displayfield',
                                            fieldLabel: 'Cantón:',
                                            labelStyle: 'font-weight:bold',
                                            width: 300,
                                            value: datosNodo.nombreCanton
                                        },                                       
                                        {
                                            xtype: 'displayfield',
                                            fieldLabel: 'Parroquia:',
                                            labelStyle: 'font-weight:bold;',
                                            width: 300,
                                            value: datosNodo.nombreParroquia
                                        },                                      
                                        {
                                            xtype: 'displayfield',
                                            fieldLabel: 'Latitud:',
                                            labelStyle: 'font-weight:bold',
                                            width: 300,
                                            value: datosNodo.latitudUbicacion
                                        },                                        
                                        {
                                            xtype: 'displayfield',
                                            fieldLabel: 'Longitud:',
                                            labelStyle: 'font-weight:bold',
                                            width: 300,
                                            value: datosNodo.longitudUbicacion
                                        },                                      
                                        {
                                            xtype: 'displayfield',
                                            fieldLabel: 'Dirección:',
                                            labelStyle: 'font-weight:bold;',
                                            width: 300,
                                            value: datosNodo.direccionUbicacion
                                        },                
                                        {
                                            xtype: 'displayfield',
                                            fieldLabel: 'Descripción:',
                                            labelStyle: 'font-weight:bold;',
                                            width: 300,
                                            value: datosNodo.descripcion
                                        },
                                        {
                                            xtype: 'displayfield',
                                            fieldLabel: 'Altura Snm:',
                                            labelStyle: 'font-weight:bold',
                                            width: 300,
                                            value: datosNodo.alturaSnm+" m"
                                        },                                        
                                        {
                                            xtype: 'displayfield',
                                            fieldLabel: 'Es 24x7:',
                                            labelStyle: 'font-weight:bold',
                                            width: 300,
                                            value: datosNodo.accesoPermanente==='S'?'SI':'NO'
                                        }                                        
                                    ]
                                },
                                {
                                    xtype: 'fieldset',
                                    title: '<b>Datos de Contacto de Nodo<b>',
                                    defaultType: 'textfield',
                                    style: "font-weight:bold; margin-bottom: 5px;",
                                    layout: {
                                        tdAttrs: {style: 'padding: 5px;'},
                                        type: 'table',
                                        columns: 2,
                                        pack: 'center'
                                    },
                                    items: [
                                        gridContacto
                                    ]
                                }

                            ]
                        }],
                    buttons: [
                        {
                            text: 'Cerrar',
                            handler: function() {
                                win.destroy();
                            }
                        }]
                });

                var win = Ext.create('Ext.window.Window', {
                    title: 'Resumen de Nodo Creado',
                    modal: true,
                    width: 900,
                    closable: true,
                    layout: 'fit',
                    items: [formPanel]
                }).show();

            },
            failure: function(result)
            {
                Ext.get(document.body).unmask();
                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
            }
        });        
}