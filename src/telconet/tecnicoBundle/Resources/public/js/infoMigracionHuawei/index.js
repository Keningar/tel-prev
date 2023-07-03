Ext.onReady(function() 
{
    Ext.tip.QuickTipManager.init();
    
    store = new Ext.data.Store
    ({
        pageSize: 10,
        total: 'total',
        proxy: 
        {
            timeout: 400000,
            type: 'ajax',
            url : 'getConsulta',
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: 
            {
                login:'',
                estado:'',
                fechaDesde:'',
                fechaHasta:''
            }
        },
        fields:
        [
            {name:'nombreCompleto', mapping:'nombreCompleto'},
            {name:'login', mapping:'login'},
            {name:'plan', mapping:'plan'},
            {name:'estadoServicio', mapping:'estadoServicio'},
            {name:'estadoSolicitud', mapping:'estadoSolicitud'},
            {name:'fechaSolicitud', mapping:'fechaSolicitud'},
            {name:'idServicio', mapping:'idServicio'},       
            {name:'tipoNegocioNombre', mapping:'tipoNegocioNombre'},
            {name:'idSolicitud', mapping:'idSolicitud'},
            {name:'direccion', mapping:'direccion'},
            {name:'perfil', mapping:'perfil'},
            {name:'motivo', mapping:'motivo'},
            {name:'caso', mapping:'caso'}

        ]
    });
    
    var pluginExpanded = true;
    
    grid = Ext.create('Ext.grid.Panel', 
    {
        width: '98%',
        height: 350,
        store: store,
        loadMask: true,
        frame: false,
        viewConfig: { enableTextSelection: true },
        iconCls: 'icon-grid',
        columns:
        [
            {
                header: 'Nombre Completo',
                dataIndex: 'nombreCompleto',
                width: '20%',
                sortable: true
            },
            {
                header: 'Login',
                dataIndex: 'login',
                width: '10%',
                sortable: true
            },
            {
                header: 'Plan',
                dataIndex: 'plan',
                width: '15%',
                sortable: true
            },
            {
                header: 'Estado Servicio',
                dataIndex: 'estadoServicio',
                width: '7%',
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
                dataIndex: 'fechaSolicitud',
                width: '8%',
                sortable: true
            },
            {
                header: 'Estado Solicitud',
                dataIndex: 'estadoSolicitud',
                width: '8%',
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
                width: '19%',
                items: 
                [
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var permiso = $("#ROLE_283-2437");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

                            if(!boolPermiso)
                            { 
                                return 'button-grid-invisible';
                            }
                            else
                            { 
                                if (rec.get('estadoSolicitud')=='' || rec.get('estadoSolicitud')=='PrePlanificada'|| rec.get('estadoSolicitud')=='Anulada')
                                {
                                    return 'button-grid-crearSolicitud';
                                }
                                else
                                {
                                    return 'button-grid-invisible';
                                }
                            }
                        },
                        tooltip: 'Nueva Solicitud',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            crearSolicitudCambio(grid.getStore().getAt(rowIndex).data);
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var permiso = $("#ROLE_283-2577");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

                            if(!boolPermiso)
                            { 
                                return 'button-grid-invisible';
                            }
                            else
                            { 
                                if (rec.get('estadoSolicitud')=='Pendiente'||rec.get('estadoSolicitud')=='PrePlanificada'||
                                    rec.get('estadoSolicitud')=='AsignadoTarea')
                                {
                                    return 'button-grid-BigDelete';
                                }
                                else
                                {
                                    return 'button-grid-invisible';
                                }
                            }
                        },
                        tooltip: 'Anular Solicitud',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            anularSolicitudCambio(grid.getStore().getAt(rowIndex).data);
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            return 'button-grid-verLogs';
                        },
                        tooltip: 'Ver Solicitudes',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            verSolicitudes(grid.getStore().getAt(rowIndex).data);
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
        bodyPadding: 7,  // Don't want content to crunch against the borders
        //bodyBorder: false,
        border:false,
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

        collapsible : true, 
        collapsed: false,
        width: '98%',
        title: 'Criterios de busqueda',
        buttons: 
        [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function(){ buscar(); }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function(){ limpiar(); }
            }
        ],                
        items: 
        [
            {   width: '10%',border:false },
            {
                xtype: 'textfield',
                id: 'login',
                fieldLabel: 'Login',
                value: '',
                width: '30%'
            },
            {   width: '20%',border:false },
            {
                xtype: 'combobox',
                fieldLabel: 'Estado solicitud',
                id: 'estado',
                value:'',
                store: [
                    ['PrePlanificada','PrePlanificada'],
                ],
                width: '30%'
            },
            {   width: '10%',border:false },
            
            //inicio del siguiente bloque
            {   width: '10%',border:false },
            {
                xtype : 'datefield',
                format: 'd/m/Y',
                id : 'fechaDesde',
                name : 'fechaDesde',
                fieldLabel : "Fecha inicio solicitud",

                labelStyle : 'text-align:left;',
                displayField: "",
                value: "",
                maxValue : new Date()
            }, 
            {   width: '20%',border:false },
            {
                xtype : 'datefield',
                format: 'd/m/Y',
                id : 'fechaHasta',
                name : 'fechaHasta',
                fieldLabel : "Fecha fin solicitud",

                labelStyle : 'text-align:left;',
                displayField: "",
                value: "",
                //allowBlank : false,
                maxValue : new Date()
            }, 
            {   width: '10%',border:false }
        ],
        renderTo: 'filtro'
    });

});

/**
* Funcion que realiza la consulta de la informacion
* general del cliente.
* 
* @author Edgar Holguin     <eholguin@telconet.ec>
* @author Francisco Adum    <fdaum@telconet.ec>
* @version 1.0 17-06-2014
* @version 1.1 modificado:25-03-2015
*/
function consultarCliente(data)
{    
    Ext.get(grid.getId()).mask('Consultando Datos...');
    Ext.Ajax.request
    ({
        url: getDatosBackbone,
        method: 'post',
        timeout: 400000,
        params: 
        { 
            idServicio: data.idServicio
        },
        success: function(response)
        {
            Ext.get(grid.getId()).unmask();
            
            var json = Ext.JSON.decode(response.responseText);
            var datos = json.encontrados;
            
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
                        title: 'Información',
                        defaultType: 'textfield',
                        defaults: 
                        {
                            width: 520
                        },
                        items: 
                        [
                            //informacion del cliente
                            {
                                xtype: 'fieldset',
                                title: 'Informacion Cliente',
                                defaultType: 'textfield',
                                defaults: 
                                {
                                    width: 500
                                },
                                items: 
                                [
                                    {
                                        xtype: 'container',
                                        layout: 
                                        {
                                            type: 'table',
                                            columns: 5,
                                            align: 'stretch'
                                        },
                                        items: 
                                        [
                                            {   width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'nombreCompleto',
                                                fieldLabel: 'Cliente',
                                                displayField: data.nombreCompleto,
                                                value: data.nombreCompleto,
                                                readOnly: true,
                                                width: '30%'
                                            },
                                            {   width: '15%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'tipoNegocio',
                                                fieldLabel: 'Tipo Negocio',
                                                displayField: data.tipoNegocioNombre,
                                                value: data.tipoNegocioNombre,
                                                readOnly: true,
                                                width: '30%'
                                            },
                                            {   width: '10%', border: false},

                                            //---------------------------------------------

                                            {   width: '10%', border: false},
                                            {
                                                xtype: 'textareafield',
                                                name: 'direccion',
                                                fieldLabel: 'Direccion',
                                                displayField:  data.direccion,
                                                value:  data.direccion,
                                                readOnly: true,
                                                width: '30%'
                                            },
                                            {   width: '15%', border: false},
                                            {   width: '30%', border: false},
                                            {   width: '10%', border: false},

                                            //---------------------------------------------
                                        ]
                                    }
                                ]
                            },//cierre de la informacion del cliente
                            //informacion del servicio/producto
                            {
                                xtype: 'fieldset',
                                title: 'Informacion Servicio',
                                defaultType: 'textfield',
                                defaults: 
                                {
                                    width: 500,
                                    height: 50
                                },
                                items: 
                                [
                                    {
                                        xtype: 'container',
                                        layout: 
                                        {
                                            type: 'table',
                                            columns: 5,
                                            align: 'stretch'
                                        },
                                        items: 
                                        [
                                            {   width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'plan',
                                                fieldLabel: 'Plan',
                                                displayField: data.plan,
                                                value: data.plan,
                                                readOnly: true,
                                                width: '30%'
                                            },
                                            {   width: '15%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'login',
                                                fieldLabel: 'Login',
                                                displayField: data.login,
                                                value: data.login,
                                                readOnly: true,
                                                width: '30%'
                                            },
                                            {   width: '10%', border: false},
                                            
                                            //---------------------------------------------
                                            
                                            {   width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'perfil',
                                                fieldLabel: 'Perfil',
                                                displayField: data.perfil,
                                                value: data.perfil,
                                                readOnly: true,
                                                width: '30%'
                                            },
                                            {   width: '15%', border: false},
                                        ]
                                    }
                                ]
                            },
                            //información técnica
                            {
                                xtype: 'fieldset',
                                title: 'Información técnica',
                                defaultType: 'textfield',
                                defaults: 
                                {
                                    width: 500,
                                    height: 85
                                },
                                items: 
                                [
                                    {
                                        xtype: 'container',
                                        layout: 
                                        {
                                            type: 'table',
                                            columns: 5,
                                            align: 'stretch'
                                        },
                                        items: 
                                        [
                                            {   width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'olt',
                                                fieldLabel: 'Olt',
                                                displayField: datos[0].nombreOlt,
                                                value: datos[0].nombreOlt,
                                                readOnly: true,
                                                width: '30%'
                                            },
                                            {   width: '15%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'puertoOlt',
                                                fieldLabel: 'Puerto Olt',
                                                displayField: datos[0].nombreInterfaceOlt,
                                                value: datos[0].nombreInterfaceOlt,
                                                readOnly: true,
                                                width: '30%'
                                            },
                                            {   width: '10%', border: false},
                                            
                                            //---------------------------------------------
                                            
                                            {   width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'split',
                                                fieldLabel: 'Split',
                                                displayField: datos[0].nombreSplitter,
                                                value: datos[0].nombreSplitter,
                                                readOnly: true,
                                                width: '30%'
                                            },
                                            {   width: '15%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'puertoSplit',
                                                fieldLabel: 'Puerto Split',
                                                displayField: datos[0].nombrePuertoSplitter,
                                                value: datos[0].nombrePuertoSplitter,
                                                readOnly: true,
                                                width: '30%'
                                            },
                                            {    width: '10%', border: false},
                                            
                                            //---------------------------------------------
                                            
                                            {   width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'caja',
                                                fieldLabel: 'Caja',
                                                displayField: datos[0].nombreCaja,
                                                value: datos[0].nombreCaja,
                                                readOnly: true,
                                                width: '30%'
                                            },
                                            {   width: '15%', border: false},
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
                        text: 'Salir',
                        handler: function()
                        {
                            win.destroy();
                        }
                    }
                ]
            });
            
            var win = Ext.create('Ext.window.Window', 
            {
                title: 'Información General',
                modal: true,
                width: 580,
                closable: true,
                layout: 'fit',
                items: [formPanel]
            }).show();  
        } //cierre response
    });//cierre ajax       
}//fin de funcion de consultar datos

/**
* Funcion que crea la solicitud de migracion
* huawei con estado Pendiente
* 
* @author Edgar Holguin     <javera@telconet.ec>
* @author Francisco Adum    <fdaum@telconet.ec>
* @version 1.0 17-06-2014
* @version 1.1 modificado:25-03-2015
*/
function crearSolicitudCambio(data)
{    
    var storeMotivos = new Ext.data.Store
    ({
        pageSize: 50,
        autoLoad: true,
        proxy: 
        {
            type: 'ajax',
            url : getMotivos,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: 
            {
                accion: "getMotivoMigracionHuawei"
            }
        },
        fields:
        [
            {   name:'idMotivo', mapping:'idMotivo'         },
            {   name:'nombreMotivo', mapping:'nombreMotivo' }
        ]
    });
    
    
    var storeSpliterPrimario = new Ext.data.Store({
        total: 'total',
        proxy: {
            timeout: 400000, type: 'ajax',
            url: getSplitterAnterior,
            extraParams: {
                nivelSplitter: '2'
            },
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'elemento', mapping: 'elemento'},
                {name: 'id_elemento', mapping: 'id_elemento'}
            ],
    }); 
    
   var storeSpliterNuevo= new Ext.data.Store({
        total: 'total',
        proxy: {
            timeout: 400000, type: 'ajax',
            url: getSplitterNuevo,
            extraParams: {
                nivelSplitter: '2'
            },
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'elemento', mapping: 'elemento'},
                {name: 'id_elemento', mapping: 'id_elemento'}
            ],
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
                title: 'Solicitud',
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
                            columns: 2,
                            align: 'stretch'
                        },
                        items: 
                        [
                           
                            //---------------------------------------------
                          {
                                xtype: 'checkboxfield',
                                name: 'chksplitter',
                                boxLabel: 'Splitter Nuevo',
                                inputValue: '',
                                listeners: {
                                    change: function (checkbox, newValue, oldValue, eOpts ) {
                                        if( newValue ) {
                                            Ext.getCmp('splitterNuevo').setDisabled(false);
                                            Ext.getCmp('splitterPrimario').setDisabled(true);
                                            Ext.getCmp('splitterPrimario').setValue('');
                                        }
                                        else {
                                            Ext.getCmp('splitterNuevo').setDisabled(true);
                                            Ext.getCmp('splitterPrimario').setDisabled(false);
                                            Ext.getCmp('splitterNuevo').setValue('');
                                        }                                                                                
                                    }
                                }
                            },                      
                            {   width: '100%', border: false },
                            {
                                xtype: 'combo',
                                id:'motivo',
                                name: 'motivo',
                                store: storeMotivos,
                                fieldLabel: 'Motivo',
                                displayField: 'nombreMotivo',
                                valueField: 'idMotivo',
                                width:350,
                                queryMode: 'local'
                            },
                            //---------------------------------------------
                            {   width: '100%', border: false },
                            {
                                xtype: 'combobox',
                                allowBlank:false,
                                width:350,
                                id: 'splitterPrimario',
                                name: 'splitterPrimario',
                                fieldLabel: 'Splitter Anterior',
                                valueField: 'id_elemento',
                                displayField: 'elemento',
                                loadingText: 'Buscando ...',
                                store: storeSpliterPrimario,
                                listClass: 'x-combo-list-small',
                                queryMode: 'remote'
                            },
                            //---------------------------------------------
                            {   width: '100%', border: false },
                            {
                                xtype: 'combobox',
                                allowBlank:false,
                                width:350,
                                id: 'splitterNuevo',
                                name: 'splitterNuevo',
                                fieldLabel: 'Splitter Nuevo',
                                valueField: 'id_elemento',
                                displayField: 'elemento',
                                loadingText: 'Buscando ...',
                                store: storeSpliterNuevo,
                                listClass: 'x-combo-list-small',
                                disabled: true,
                                queryMode: 'remote'
                            },                            
                            //---------------------------------------------
                            {   width: '100%', border: false },
                            {
                                xtype: 'textareafield',
                                id: 'observacion',
                                name: 'observacion',
                                fieldLabel: 'Observación',
                                displayField: '',
                                value: '',
                                readOnly: false,
                                width:350
                            },
                            
                        ]
                    }
                ]
            }
        ],
 
        buttons: 
        [
            {
                text: 'Crear',
                handler: function()
                {
                    var motivo = Ext.getCmp('motivo').getValue();
                    var splitter_anterior = Ext.getCmp('splitterPrimario').getValue();
                    var splitter_nuevo = Ext.getCmp('splitterNuevo').getValue();
                    var observacion = Ext.getCmp('observacion').getValue();
                    var validacion = false;
                    var mensaje='';

                    if((motivo!=null) && (splitter_anterior!=null || splitter_nuevo!=null))
                    {
                        validacion=true;
                    }
            
                    if (validacion)
                    {
                        Ext.Msg.alert('Mensaje','Esta seguro que desea Crear la Solicitud?', function(btn)
                        {  
                            if(btn=='ok')
                            {
                                Ext.get(grid.getId()).mask('Creando Solicitud...');
                                Ext.Ajax.request({
                                    url: crearSolicitud,
                                    method: 'post',
                                    timeout: 300000, 
                                    params: 
                                    { 
                                        idServicio: data.idServicio,
                                        motivo: motivo,
                                        splitterPrimario: splitter_anterior,
                                        splitterNuevo: splitter_nuevo,
                                        observacion: observacion
                                    },
                                    success: function(response)
                                    {
                                        Ext.get(grid.getId()).unmask();
					
					var json = Ext.JSON.decode(response.responseText);
					
					console.log(json);
					
                                        if(json.status == "OK")
                                        {
                                            Ext.Msg.alert('Mensaje',json.mensaje, function(btn)
                                            {
                                                if(btn=='ok')
                                                {
                                                    store.load();
                                                    win.destroy();
                                                }
                                            });
                                        }  
                                        else if(json.status == "EXISTE")
                                        {
                                            Ext.Msg.alert('Mensaje',json.mensaje, function(btn)
                                            {
                                                if(btn=='ok')
                                                {
                                                    win.destroy();
                                                }
                                            });
                                        }                                         
					else
                                        {
                                            Ext.Msg.alert('Mensaje ',json.mensaje );
                                        }
                                    },
                                    failure: function(result)
                                    {
                                        Ext.get(grid.getId()).unmask();
                                        Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                    }
                                }); 
                            }
                        });        
                    }
                    else
                    {
                        if((motivo==null) )
                        {
                            mensaje='Favor indique un motivo';
                        }
                        else if((splitter_anterior==null || splitter_nuevo==null))
                        {
                            mensaje='Favor indique un splitter';
                        }
                        Ext.Msg.alert("Advertencia",mensaje, function(btn)
                        {
                            if(btn=='ok')
                            { 
                            }
                        });
                    }
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
        title: 'Crear Solicitud de Migracion Huawei',
        modal: true,
        width: 450,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();

}//fin de funcion crear solicitud



/**
* Funcion que muestra el historial del servicio de sus
* solicitudes de migracion huawei.
* 
* @author Edgar Holguin     <eholguin@telconet.ec>
* @author Francisco Adum    <fdaum@telconet.ec>
* @version 1.0 17-06-2014
* @version 1.1 modificado:25-03-2015
*/
function verSolicitudes(data)
{
    storeHistorialSolicitud = new Ext.data.Store
    ({
        pageSize: 50,
        autoLoad: true,
        proxy: 
        {
            type: 'ajax',
            url : getSolicitudes,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: 
            {
                idServicio: data.idServicio
            }
        },
        fields:
        [
            {   name:'idSolicitud', mapping:'idSolicitud'   },
            {   name:'motivo', mapping:'motivo'             },
            {   name:'observacion', mapping:'observacion'   },
            {   name:'fechaCrea', mapping:'fechaCrea'       },
            {   name:'usuarioCrea', mapping:'usuarioCrea'   },
            {   name:'estado', mapping:'estado'             },
            {   name:'splitterAnt', mapping:'splitterAnt'}
        ]
    });

    gridHistorico = Ext.create('Ext.grid.Panel', 
    {
        id:'gridHistorico',
        store: storeHistorialSolicitud,
        columnLines: true,
        columns: 
        [
            {

                header: 'No.',
                dataIndex: 'idSolicitud',
                hidden: false,
                width: 50,
                sortable: true
            },
            {

                header: 'Motivo',
                dataIndex: 'motivo',
                width: 120,
                sortable: true
            },
            {

                header: 'Observación',
                dataIndex: 'observacion',
                width: 120,
                sortable: true
            },
            {
                header: 'Splitter Anterior',
                dataIndex: 'splitterAnt',
                width: 200,
                sortable: true
            },
            {

                header: 'Fecha Creación',
                dataIndex: 'fechaCrea',
                width: 120,
                sortable: true
            },
            {
                header: 'Usuario Crea',
                dataIndex: 'usuarioCrea',
                width: 75
            },        
            {
                header: 'Estado',
                dataIndex: 'estado',
                width: 90
            },
            {
                xtype: 'actioncolumn',
                header: 'Accion',
                width: 60,
                items: 
                [
                    {
                        getClass: function(v, meta, rec) 
                        {
                            return 'button-grid-show';
                        },
                        tooltip: 'Ver Historial',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            verHistorialSolicitud(grid.getStore().getAt(rowIndex).data);
                        }
                    }
                ]
            }
        ],
        viewConfig:
        {
            stripeRows:true
        },

        frame: true,
        height: 200
        //title: 'Historial del Servicio'
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
                    width: 870
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
        title: 'Solicitudes del servicio',
        modal: true,
        width: 920,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}// fin de la funcion de historial de solicitudes

/**
* Funcion que muestra el historial de las
* solicitudes de cambio de linea pon.
* 
* @author Edgar Holguin     <eholguin@telconet.ec>
* @author Francisco Adum    <fdaum@telconet.ec>
* @version 1.0 17-06-2014
* @version 1.1 modificado:25-03-2015
*/
function verHistorialSolicitud(data)
{
    storeHistorialSolicitud = new Ext.data.Store
    ({
        pageSize: 50,
        autoLoad: true,
        proxy: 
        {
            type: 'ajax',
            url : getHistorialSolicitud,
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
            { name:'observacion', mapping:'observacion' },
            { name:'fechaCrea', mapping:'fechaCrea' },
            { name:'usuarioCrea', mapping:'usuarioCrea' },
            { name:'estado', mapping:'estado' }
        ]
    });
    
    gridHistorico = Ext.create('Ext.grid.Panel', 
    {
        id:'gridHistorico',
        store: storeHistorialSolicitud,
        columnLines: true,
        columns: 
        [
            {
                header: 'Observación',
                dataIndex: 'observacion',
                width: 120,
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
                width: 75
            },        
            {
                header: 'Estado',
                dataIndex: 'estado',
                width: 70
            }
        ],
        viewConfig:
        {
            stripeRows:true
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
                    width: 395
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
        width: 435,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}//fin de funcion de historial de solicitud


/**
* Funcion que rechaza la solicitud de cambio de
* linea pon
* 
* @author John Vera         <javera@telconet.ec>
* @version 1.0 17-06-2014
*/
function anularSolicitudCambio(data)
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
                            columns: 5,
                            align: 'stretch'
                        },
                        items: 
                        [
                            {   width: '10%', border: false},
                            {
                                xtype: 'textareafield',
                                id: 'observacion',
                                name: 'observacion',
                                fieldLabel: '',
                                displayField: '',
                                value: '',
                                readOnly: false,
                                width: '100%'
                            },
                            {   width: '10%', border: false},
                        ]
                    }
                ]
            }
        ],

        buttons: 
        [
            {
                text: 'Anular',
                handler: function()
                {
                    var observacion = Ext.getCmp('observacion').getValue();
                    var validacion = false;

                    if(observacion!='')
                    {
                        validacion=true;
                    }

                    if (validacion)
                    {
                        Ext.Msg.alert('Mensaje','Esta seguro que desea Anular la Solicitud?', function(btn)
                        {
                            if(btn=='ok')
                            {
                                Ext.get(grid.getId()).mask('Anulando Solicitud...');
                                Ext.Ajax.request
                                ({
                                    url: anularSolicitud,
                                    method: 'post',
                                    timeout: 300000, 
                                    params: 
                                    { 
                                        idSolicitud: data.idSolicitud,
                                        observacion: observacion,
                                        caso: data.caso
                                    },
                                    success: function(response)
                                    {
                                        Ext.get(grid.getId()).unmask();
                                        if(response.responseText == "OK")
                                        {
                                            Ext.Msg.alert('Mensaje','Se anuló la solicitud correctamente.', function(btn)
                                            {
                                                if(btn=='ok')
                                                {
                                                    store.load();
                                                    win.destroy();
                                                }
                                            });
                                        }
                                        else
                                        {
                                            Ext.Msg.alert('Mensaje ',response.responseText );
                                        }
                                    },
                                    failure: function(result)
                                    {
                                        Ext.get(grid.getId()).unmask();
                                        Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                    }
                                }); 
                            }
                        });        
                    }
                    else
                    {
                        Ext.Msg.alert("Advertencia","Favor ingrese una observación", function(btn)
                        {
                            if(btn=='ok')
                            { 
                            }
                        });
                    }
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
        title: 'Anulación de Solicitud',
        modal: true,
        width: 350,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();

}//fin de funcion anular solicitud


/**
* Funcion que ejecuta la busqueda de los datos
* 
* @author Edgar Holguin     <eholguin@telconet.ec>
* @author Francisco Adum    <fdaum@telconet.ec>
* @version 1.0 17-06-2014
* @version 1.1 modificado:25-03-2015
*/
function buscar()
{
    if(Ext.getCmp('fechaDesde').value > Ext.getCmp('fechaHasta').value)
    {
        alert('Ingrese correctamente las fechas.');
        return;
    }
    
    store.getProxy().extraParams.estado     = Ext.getCmp('estado').value;
    store.getProxy().extraParams.fechaDesde = Ext.getCmp('fechaDesde').value;
    store.getProxy().extraParams.fechaHasta = Ext.getCmp('fechaHasta').value;
    store.getProxy().extraParams.login      = Ext.getCmp('login').value;
    store.load();
}

/**
* Funcion que limpia los controles de los 
* filtros.
* 
* @author Edgar Holguin     <eholguin@telconet.ec>
* @author Francisco Adum    <fdaum@telconet.ec>
* @version 1.0 17-06-2014
* @version 1.1 modificado:25-03-2015
*/
function limpiar()
{
    Ext.getCmp('estado').value="";
    Ext.getCmp('estado').setRawValue("");
    
    Ext.getCmp('fechaDesde').value="";
    Ext.getCmp('fechaDesde').setRawValue("");
    
    Ext.getCmp('fechaHasta').value="";
    Ext.getCmp('fechaHasta').setRawValue("");
    
    Ext.getCmp('login').value="";
    Ext.getCmp('login').setRawValue("");
}
