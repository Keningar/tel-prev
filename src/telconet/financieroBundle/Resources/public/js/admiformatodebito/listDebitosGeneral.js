            Ext.require([
                '*',
                'Ext.tip.QuickTipManager',
                    'Ext.window.MessageBox',

            ]);

            var itemsPerPage = 20;
            var store='';
            var estado_id='';
            var area_id='';
            var login_id='';
            var tipo_asignacion='';
            var pto_sucursal='';
            var idClienteSucursalSesion;
            var intWidthGridDebitos =1400;
            var intIdDebitoGeneral = null;
            //parametro para recuperar valor de parametro FlujoDebitoPlanificado
            var strFlujoPlanificado;

            Ext.onReady(function(){
                //se invoca el metodo para recuperar desde la base los parametros de "DEBITOS_PLANIFICADOS" y recuperar el FlujoDebitoPlanificado
                Ext.Ajax.request
                ({
                    url: strUrlGetParametroFlujoPlanificado,
                    method: 'get',
                    datatype: 'json',
                    timeout: 9000000,
                    reader: 
                        {
                            type: 'json',
                            root: 'encontrados'
                        },
                    params:
                    { 
                        strNombreParametro: "DEBITOS_PLANIFICADOS"
                    },
                    success: function(response)
                    {
                        strFlujoPlanificado = response.responseText;
                       
                
        

                        //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		
                        DTFechaDesde = new Ext.form.DateField({
                                id: 'fechaDesde',
                                fieldLabel: 'Desde',
                                labelAlign : 'left',
                                xtype: 'datefield',
                                format: 'Y-m-d',
                                emptyText: 'YYYY-MM-DD',
                                width:325
                        });
                        DTFechaHasta = new Ext.form.DateField({
                                id: 'fechaHasta',
                                fieldLabel: 'Hasta',
                                labelAlign : 'left',
                                xtype: 'datefield',
                                format: 'Y-m-d',
                                emptyText: 'YYYY-MM-DD',
                                width:325
                        });


                        //CREAMOS DATA STORE PARA EMPLEADOS
                        Ext.define('modelEstado', {
                            extend: 'Ext.data.Model',
                            fields: [
                                {name: 'idestado', type: 'string'},
                                {name: 'codigo',  type: 'string'},
                                {name: 'descripcion',  type: 'string'}                    
                            ]
                        });			
                var estado_store = Ext.create('Ext.data.Store', {
                    model: "modelEstado",
                    proxy: {
                        type: 'ajax',
                        url: url_lista_estados,
                        reader: {
                            type: 'json',
                            root: 'estados'
                        }
                    }
                });
                var estado_cmb = new Ext.form.ComboBox({
                    xtype: 'combobox',
                    store: estado_store,
                    labelAlign: 'left',
                    id: 'idestado',
                    name: 'idestado',
                    valueField: 'descripcion',
                    displayField: 'descripcion',
                    fieldLabel: 'Estado',
                    width: 325,
                    triggerAction: 'all',
                    emptyText: 'Seleccione...',
                    mode: 'local',
                    allowBlank: true,
                });

                //SE CREA EL COMBOBOX PARA EL CICLO DEPENDIENDO SI LA EMPRESA APLICA CICLOS DE FACTURACIÓN
                var cboCiclosFacturacion   = null;
                var booleanAplicaCiclosFac = false;
                if (strAplicaCiclosFacturacion === 'S') {
                    booleanAplicaCiclosFac = true;
                    var storeCiclos = new Ext.data.Store({
                        pageSize: 10,
                        total: 'intTotal',
                        proxy: {
                            type: 'ajax',
                            url: strUrlObtieneCiclos,
                            reader: {
                                type: 'json',
                                totalProperty: 'intTotal',
                                root: 'arrayRegistros'
                            }
                        },
                        fields:
                            [
                                {name: 'intIdCiclo', mapping: 'intIdCiclo'},
                                {name: 'strNombreCiclo', mapping: 'strNombreCiclo'}
                            ],
                        autoLoad: true,
                        listeners: {
                            load: function () {
                                storeCiclos.add({
                                    strNombreCiclo: 'Todos',
                                    intIdCiclo: null
                                });
                            }
                        },
                        sorters: {
                            property: 'intIdCiclo',
                            direction: 'ASC'
                        }
                    });


                    cboCiclosFacturacion = Ext.create('Ext.form.ComboBox', {
                        xtype: 'combo',
                        fieldLabel: 'Ciclo Facturacio&#769;n',
                        id: 'cmbCicloFacturacionId',
                        name: 'cmbCicloFacturacion',
                        displayField: 'strNombreCiclo',
                        valueField: 'intIdCiclo',
                        emptyText: 'Seleccione...',
                        labelStyle: 'text-align:left;',
                        multiSelect: false,
                        width: 325,
                        queryMode: 'local',
                        store: storeCiclos,
                        hidden: !booleanAplicaCiclosFac
                    });
                }
                /**
                 * Se crean los botones y el row para Generar Debitos y respuesta debitos.
                 */
                var btnGenerarDebitos = Ext.create('Ext.button.Button', {
                    text: 'Generar De&#769;bitos',
                    width: 160,
                    iconCls: 'button-grid-crearSolicitud-without-border',
                    handler: function() {
                        $(location).attr('href', strUrlGenerarDebitos);
                    }
                });
                var btnSubirRespuesta = Ext.create('Ext.button.Button', {
                    text: 'Subir Respuesta De&#769;bitos',
                    width: 160,
                    iconCls: 'button-grid-uploadContrato',
                    handler: function () {
                        if (intIdDebitoGeneral != null) {
                            Ext.MessageBox.wait("Validando las cabeceras del de&#769;bito...");
                            Ext.Ajax.request({
                                url: strUrlCuentaCabeceras,
                                method: 'POST',
                                params: {
                                    intDebitoGeneralId: intIdDebitoGeneral
                                },
                                success: function (response, request) {
                                    intTotal = parseInt(response.responseText);
                                    if (intTotal > 0) {
                                        $('#idDebitoGeneral').val(intIdDebitoGeneral);
                                        $('#formSubmit').submit();
                                        Ext.MessageBox.close();
                                    }
                                    else{
                                        Ext.MessageBox.show({
                                        modal: true,
                                        title: 'Información',
                                        msg: 'El de&#769;bito no tiene cabeceras pendientes o tiene un cierre final manual',
                                        width: 300,
                                        icon: Ext.MessageBox.INFO
                                    });
                                    }

                                },
                                failure: function () {
                                    Ext.MessageBox.show({
                                        modal: true,
                                        title: 'Error',
                                        msg: 'Error al validar el de&#769;bito general.',
                                        width: 300,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                }
                            });
                        }
                        else
                        {
                            Ext.Msg.alert('Atencio&#769;n', 'Debe seleccionar un de&#769;bito para subir su(s) archivo(s) de respuesta.');
                        }
                    }
                });
                
                var toolbarAcciones = Ext.create('Ext.toolbar.Toolbar', {
                    dock: 'top',
                    align: '->',
                    items:
                        [{xtype: 'tbfill'},
                            btnGenerarDebitos,
                            btnSubirRespuesta
                        ]
                });
                /**
                * FIN
                */
                    if(getParametroDebitoPlanificado(strFlujoPlanificado,'FlujoDebitoPlanificado') == 'SI'){
                        Ext.define('ListaDetalleModel',
                        {
                            extend: 'Ext.data.Model',
                            fields:
                            [
                                {name:'id',                    type: 'int'},
                                {name:'fechaCreacion',         type: 'string'},
                                {name:'usuarioCreacion',       type: 'string'},
                                {name:'bancos',                type: 'string'},
                                {name:'totalRegistros',        type: 'string'},
                                {name:'pendientes',            type: 'string'},
                                {name:'procesados',            type: 'string'},
                                {name:'rechazados',            type: 'string'},
                                {name:'estado',                type: 'string'},
                                {name:'planificado',           type: 'string'},
                                {name:'linkArchivo',           type: 'string'},
                                {name:'linkArchivoExcel',      type: 'string'},
                                {name:'intCaracteristica',     type: 'int'   },
                                {name:'linkVer',               type: 'string'},
                                {name:'linkRespuestas',        type: 'string'},
                                {name:'linkCierreFinalManual', type: 'string'},
                                {name:'linkPagos',             type: 'string'},
                                {name:'ejecutando',            type: 'string'},
                                {name:'ejecutandoCierre',      type: 'string'},
                                {name:'valorTotal',            type: 'string'},
                                {name:'valorPendientes',       type: 'string'},
                                {name:'valorProcesados',       type: 'string'},
                                {name:'valorRechazados',       type: 'string'},
                                {name:'oficinaClientes',       type: 'string'},
                                {name:'descripcionImpuesto',   type: 'string'},
                                {name:'nombreCiclo',           type: 'string'},
                                {name:'linkArchivoDebNfs',     type: 'string'}
                            ]
                        }); 
                    }else{
                        Ext.define('ListaDetalleModel',
                            {
                                extend: 'Ext.data.Model',
                                fields:
                                [
                                    {name:'id',                    type: 'int'},
                                    {name:'fechaCreacion',         type: 'string'},
                                    {name:'usuarioCreacion',       type: 'string'},
                                    {name:'bancos',                type: 'string'},
                                    {name:'totalRegistros',        type: 'string'},
                                    {name:'pendientes',            type: 'string'},
                                    {name:'procesados',            type: 'string'},
                                    {name:'rechazados',            type: 'string'},
                                    {name:'estado',                type: 'string'},
                                    {name:'linkArchivo',           type: 'string'},
                                    {name:'linkArchivoExcel',      type: 'string'},
                                    {name:'intCaracteristica',     type: 'int'   },
                                    {name:'linkVer',               type: 'string'},
                                    {name:'linkRespuestas',        type: 'string'},
                                    {name:'linkCierreFinalManual', type: 'string'},
                                    {name:'linkPagos',             type: 'string'},
                                    {name:'ejecutando',            type: 'string'},
                                    {name:'ejecutandoCierre',      type: 'string'},
                                    {name:'valorTotal',            type: 'string'},
                                    {name:'valorPendientes',       type: 'string'},
                                    {name:'valorProcesados',       type: 'string'},
                                    {name:'valorRechazados',       type: 'string'},
                                    {name:'oficinaClientes',       type: 'string'},
                                    {name:'descripcionImpuesto',   type: 'string'},
                                    {name:'nombreCiclo',           type: 'string'},
                                    {name:'linkArchivoDebNfs',     type: 'string'}
                                ]
                            }); 
                    }
                            


                            store = Ext.create('Ext.data.JsonStore', {
                                model: 'ListaDetalleModel',
                                        pageSize: itemsPerPage,
                                proxy: {
                                    type: 'ajax',
                                    url: url_grid,
                                    reader: {
                                        type: 'json',
                                        root: 'pagos',
                                        totalProperty: 'total'
                                    },
                                    extraParams:{fechaDesde:'',fechaHasta:'', estado:''},
                                    simpleSortMode: true
                                },
                                listeners: {
                                    beforeload: function (store) {
                                        store.getProxy().extraParams.fechaDesde = Ext.getCmp('fechaDesde').getValue();
                                        store.getProxy().extraParams.fechaHasta = Ext.getCmp('fechaHasta').getValue();
                                        store.getProxy().extraParams.estado = Ext.getCmp('idestado').getValue();
                                        if (strAplicaCiclosFacturacion === 'S') {
                                            store.getProxy().extraParams.cicloId = Ext.getCmp('cmbCicloFacturacionId').getValue();
                                        }
                                    }
                                },
                                sortOnLoad : true,
                                sorters : {
                                    property : 'banco',
                                    direction : 'ASC'
                                }
                            });

                            store.load({params: {start: 0, limit: 20}});    



                            var ObjSelectionMode = new Ext.selection.CheckboxModel( {
                                listeners:{
                                    selectionchange: function(selectionModel, selected, options){
                                        arregloSeleccionados= new Array();
                                        Ext.each(selected, function(record){
                                                //arregloSeleccionados.push(record.data.idOsDet);
                                });			
                                        //console.log(arregloSeleccionados);

                                    }
                                }
                            });


                ObjSelectionMode = new Ext.selection.CheckboxModel({
                    mode: 'SINGLE',
                    allowDeselect: true,
                    listeners: {
                        selectionchange: function (selectionModel, selected, options) {
                            if (selected[0] == null) {
                                intIdDebitoGeneral = null;
                            } else {
                                intIdDebitoGeneral = selected[0].get('id');
                            }
                        }
                    }
                });
                if(getParametroDebitoPlanificado(strFlujoPlanificado,'FlujoDebitoPlanificado') == 'SI'){
                    listView = Ext.create('Ext.grid.Panel', {
                        width:intWidthGridDebitos,
                        height: 320,
                        collapsible:false,
                        title: '',
                        selModel: ObjSelectionMode,
                        dockedItems: [ {
                                        xtype: 'toolbar',
                                        dock: 'top',
                                        align: '->',
                                        items: [
                                            { xtype: 'tbfill' }                                        
                                        ]}, toolbarAcciones],
                        renderTo: Ext.get('lista_pagos'),
                        // paging bar on the bottom
                        bbar: Ext.create('Ext.PagingToolbar', {
                            store: store,
                            displayInfo: true,
                            displayMsg: 'Mostrando debitos {0} - {1} of {2}',
                            emptyMsg: "No hay datos para mostrar"
                        }),	
                        store: store,
                        multiSelect: false,
                        viewConfig: {
                            emptyText: 'No hay datos para mostrar'
                        },
                        columns: 
                        [
                            new Ext.grid.RowNumberer(),  
                            {
                                text: 'Fecha Creacion',
                                dataIndex: 'fechaCreacion',
                                align: 'right',
                                flex: 150			
                            },
                            {
                                text: 'Ciclo',
                                dataIndex: 'nombreCiclo',
                                align: 'left',
                                flex: 100,
                                hidden: !booleanAplicaCiclosFac
                            },
                            {
                                text: 'Planificado',
                                dataIndex: 'planificado',
                                align: 'left',
                                flex: 130
                            },
                            {
                                text: 'Oficina',
                                dataIndex: 'oficinaClientes',
                                align: 'right',
                                flex: 100			
                            },
                            {
                                text: 'Bancos / Tarjetas',
                                dataIndex: 'bancos',
                                align: 'left',
                                flex: 250			
                            },
                            {
                                text: 'IVA',
                                dataIndex: 'descripcionImpuesto',
                                align: 'center',
                                flex: 6			
                            },
                            {
                                text: 'Total',
                                dataIndex: 'totalRegistros',
                                align: 'left',
                                width: 50			
                            },
                            {
                                text: 'Total($)',
                                columns:
                                [
                                    {
                                        text: 'Enviado',
                                        dataIndex: 'valorTotal',
                                        align: 'left',
                                        width: 70
                                    },
                                    {
                                        text: 'Procesado',
                                        dataIndex: 'valorProcesados',
                                        align: 'left',
                                        width: 70
                                    }
                                ]
                            },
                            {
                            text: 'Pendientes',    
                            columns:
                            [
                                {
                                    text: 'Cant',
                                    dataIndex: 'pendientes',
                                    align: 'left',
                                    width: 45			
                                },
                                {
                                    text: '$',
                                    dataIndex: 'valorPendientes',
                                    align: 'left',
                                    width: 70			
                                }
                            ]},
                            {
                                text: 'Procesados',   
                                columns:
                                [
                                    {
                                        text: 'Cant',
                                        dataIndex: 'procesados',
                                        align: 'left',
                                        width: 40			
                                    },
                                    {
                                        text: '$',
                                        dataIndex: 'valorProcesados',
                                        align: 'left',
                                        width: 70			
                                    }                    
                                ]
                            },
                            {
                                text: 'Rechazados',   
                                columns:
                                [
                                    {
                                        text: 'Cant',
                                        dataIndex: 'rechazados',
                                        align: 'left',
                                        width: 40			
                                    },
                                    {
                                        text: '$',
                                        dataIndex: 'valorRechazados',
                                        align: 'left',
                                        width: 70			
                                    }                    
                                ]
                            },
                            {
                                text: 'Usuario',
                                dataIndex: 'usuarioCreacion',
                                align: 'right',
                                width: 70
                            },
                            {
                                text: 'I',
                                dataIndex: 'estado',
                                align: 'right',
                                width: 20,
                                renderer: renderAccionEjecutando
                            },
                            {
                                text: 'C',
                                dataIndex: 'estado cierre',
                                align: 'right',
                                width: 20,
                                renderer: renderAccionEjecutandoCierre
                            },
                            {
                                text: 'Acciones',
                                width: 200,
                                renderer: renderAcciones
                            }
                        ],
                        listeners:
                        {
                            viewready: function (grid) 
                            {
                                var view = grid.view;
                                // record the current cellIndex
                                grid.mon(view, 
                                {
                                    uievent: function (type, view, cell, recordIndex, cellIndex, e) {
                                        grid.cellIndex = cellIndex;
                                        grid.recordIndex = recordIndex;
                                    }
                                });

                                grid.tip = Ext.create('Ext.tip.ToolTip', 
                                {
                                    target: view.el,
                                    delegate: '.x-grid-cell',
                                    trackMouse: true,
                                    renderTo: Ext.getBody(),
                                    listeners: 
                                    {
                                        beforeshow: function updateTipBody(tip) 
                                        {
                                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1) 
                                            {
                                                header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                                tip.update(grid.getStore().getAt(grid.recordIndex).get(header.dataIndex));
                                            }
                                        }
                                    }
                                });
                    }
                        }                    
                    });   
                }else{
                    listView = Ext.create('Ext.grid.Panel', {
                        width:intWidthGridDebitos,
                        height: 320,
                        collapsible:false,
                        title: '',
                        selModel: ObjSelectionMode,
                        dockedItems: [ {
                                        xtype: 'toolbar',
                                        dock: 'top',
                                        align: '->',
                                        items: [
                                            { xtype: 'tbfill' }                                        
                                        ]}, toolbarAcciones],
                        renderTo: Ext.get('lista_pagos'),
                        // paging bar on the bottom
                        bbar: Ext.create('Ext.PagingToolbar', {
                            store: store,
                            displayInfo: true,
                            displayMsg: 'Mostrando debitos {0} - {1} of {2}',
                            emptyMsg: "No hay datos para mostrar"
                        }),	
                        store: store,
                        multiSelect: false,
                        viewConfig: {
                            emptyText: 'No hay datos para mostrar'
                        },
                        columns: 
                        [
                            new Ext.grid.RowNumberer(),  
                            {
                                text: 'Fecha Creacion',
                                dataIndex: 'fechaCreacion',
                                align: 'right',
                                flex: 150			
                            },
                            {
                                text: 'Ciclo',
                                dataIndex: 'nombreCiclo',
                                align: 'left',
                                flex: 100,
                                hidden: !booleanAplicaCiclosFac
                            },
                            {
                                text: 'Oficina',
                                dataIndex: 'oficinaClientes',
                                align: 'right',
                                flex: 100			
                            },
                            {
                                text: 'Bancos / Tarjetas',
                                dataIndex: 'bancos',
                                align: 'left',
                                flex: 250			
                            },
                            {
                                text: 'IVA',
                                dataIndex: 'descripcionImpuesto',
                                align: 'center',
                                flex: 6			
                            },
                            {
                                text: 'Total',
                                dataIndex: 'totalRegistros',
                                align: 'left',
                                width: 50			
                            },
                            {
                                text: 'Total($)',
                                columns:
                                [
                                    {
                                        text: 'Enviado',
                                        dataIndex: 'valorTotal',
                                        align: 'left',
                                        width: 70
                                    },
                                    {
                                        text: 'Procesado',
                                        dataIndex: 'valorProcesados',
                                        align: 'left',
                                        width: 70
                                    }
                                ]
                            },
                            {
                            text: 'Pendientes',    
                            columns:
                            [
                                {
                                    text: 'Cant',
                                    dataIndex: 'pendientes',
                                    align: 'left',
                                    width: 45			
                                },
                                {
                                    text: '$',
                                    dataIndex: 'valorPendientes',
                                    align: 'left',
                                    width: 70			
                                }
                            ]},
                            {
                                text: 'Procesados',   
                                columns:
                                [
                                    {
                                        text: 'Cant',
                                        dataIndex: 'procesados',
                                        align: 'left',
                                        width: 40			
                                    },
                                    {
                                        text: '$',
                                        dataIndex: 'valorProcesados',
                                        align: 'left',
                                        width: 70			
                                    }                    
                                ]
                            },
                            {
                                text: 'Rechazados',   
                                columns:
                                [
                                    {
                                        text: 'Cant',
                                        dataIndex: 'rechazados',
                                        align: 'left',
                                        width: 40			
                                    },
                                    {
                                        text: '$',
                                        dataIndex: 'valorRechazados',
                                        align: 'left',
                                        width: 70			
                                    }                    
                                ]
                            },
                            {
                                text: 'Usuario',
                                dataIndex: 'usuarioCreacion',
                                align: 'right',
                                width: 70
                            },
                            {
                                text: 'I',
                                dataIndex: 'estado',
                                align: 'right',
                                width: 20,
                                renderer: renderAccionEjecutando
                            },
                            {
                                text: 'C',
                                dataIndex: 'estado cierre',
                                align: 'right',
                                width: 20,
                                renderer: renderAccionEjecutandoCierre
                            },
                            {
                                text: 'Acciones',
                                width: 200,
                                renderer: renderAcciones
                            }
                        ],
                        listeners:
                        {
                            viewready: function (grid) 
                            {
                                var view = grid.view;
                                // record the current cellIndex
                                grid.mon(view, 
                                {
                                    uievent: function (type, view, cell, recordIndex, cellIndex, e) {
                                        grid.cellIndex = cellIndex;
                                        grid.recordIndex = recordIndex;
                                    }
                                });

                                grid.tip = Ext.create('Ext.tip.ToolTip', 
                                {
                                    target: view.el,
                                    delegate: '.x-grid-cell',
                                    trackMouse: true,
                                    renderTo: Ext.getBody(),
                                    listeners: 
                                    {
                                        beforeshow: function updateTipBody(tip) 
                                        {
                                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1) 
                                            {
                                                header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                                tip.update(grid.getStore().getAt(grid.recordIndex).get(header.dataIndex));
                                            }
                                        }
                                    }
                                });
                    }
                        }                    
                    });   
                }
                                     

                        function renderAccionEjecutando(value, p, record) {
                                var iconos='';
                                if(record.data.ejecutando=='S')
                                    iconos=iconos+iconoEjecutando;                    
                                return Ext.String.format(
                                                iconos,
                                    value
                                );
                        }
                        
                        
                        function renderAccionEjecutandoCierre(value, p, record) {
                                var iconos='';
                                if(record.data.ejecutandoCierre=='S')
                                    iconos=iconos+iconoEjecutando;                    
                                return Ext.String.format(
                                                iconos,
                                    value
                                );
                        }
                        
                        /**
                        * Documentación para funcion 'renderAcciones'.
                        * muestra las acciones que se pueden realizar sobre los debitos
                        * @param value  : valor enviado
                        * @param p      : p
                        * @param record : registro que viene de la base de datos
                        * @author <amontero@telconet.ec>
                        * @since 06/01/2015
                        * @return String
                        * @author Ricardo Robles <rrobles@telconet.ec>
                        * @version 1.1 20-03-2019 - Se agrega link que redirecciona a la opción de Cierre Final Manual(linkCierreFinalManual).
                        */             
                        function renderAcciones(value, p, record) 
                        {   
                            var iconos = '';
                            iconos = iconos+'<b><a href="'+record.data.linkVer+'" onClick="" title="Ver Detalle Debitos" class="button-grid-show"></a></b>';
                            if(record.data.intCaracteristica === 1 && record.data.ejecutandoCierre!=='S')
                            {
                                iconos = iconos+'<b><a href="'+record.data.linkCierreFinalManual+'" onClick="" title="Cierre Final Manual" class="button-grid-Tuerca-2"></a></b>';
                            }

                            if(record.data.linkArchivo)
                            {
                                iconos=iconos+'<b><a href="'+record.data.linkArchivo+
                                    '" onClick="" title="Ver Archivo debito Generado" class="button-grid-zip"></a></b>';
                                if(puedeVerExcelDebito)
                                {    
                                    iconos=iconos+'<b><a href="'+record.data.linkArchivoExcel+
                                        '" onClick="" title="Ver Archivo Excel debito Generado" class="button-grid-excel"></a></b>';
                                }
                            }
                            if(puedeVerExcelPagosDebito)
                            {
                                iconos=iconos+'<b><a href="'+record.data.linkPagos+'" onClick="" title="Ver Pagos" class="button-grid-excel-green"></a></b>';
                            }
                            if(record.data.linkRespuestas)
                            {
                                iconos=iconos+'<b><a href="#" onClick="descargar(\''+record.data.linkRespuestas+
                                    '\')" title="Descargar Resultado subida" class="button-grid-excel-red"></a></b>';
                            }
                            //Validación para obtener el link y poder descargar el archivo excel de clientes del nfs, en el caso
                            //de haber sido subido por el flujo de la opción de débito. 
                            if(record.data.linkArchivoDebNfs) 
                            {
                                iconos=iconos+'<b><a href="'+record.data.linkArchivoDebNfs+
                                        '" onClick="" title="Ver Archivo Excel NFS Clientes Debito" class="button-grid-excel-debitoNfs"></a></b>';
                            }
                            if(puedeAnularCabeceraDebito)
                            {
                                iconos=iconos+'<b><a href="#" onClick="accionEnDebitos(\''+record.data.id+
                                    '\',\'Anular\')" title="Anular Debitos" class="button-grid-eliminar"></a></b>';
                            }
                            if(puedeReabrirCabeceraDebito)
                            {
                                iconos=iconos+'<b><a href="#" onClick="accionEnDebitos(\''+record.data.id+
                                    '\',\'Reabrir\')" title="Reabrir Debitos" class="button-grid-cambioVelocidad"></a></b>';
                            }
                            return Ext.String.format(iconos,value);
                        }



                        var filterPanel = Ext.create('Ext.panel.Panel', {
                            bodyPadding: 7,  // Don't want content to crunch against the borders
                            border:false,
                            buttonAlign: 'center',
                            layout:{
                                type:'table',
                                columns: 5,
                                align: 'left'
                            },
                            bodyStyle: {
                                        background: '#fff'
                            },                     
                            defaults: {
                                bodyStyle: 'padding:10px'
                            },
                            collapsible : true,
                            collapsed: true,
                            width: intWidthGridDebitos,
                            title: 'Criterios de busqueda',
                            buttons: [
                                    {
                                        text: 'Buscar',
                                        iconCls: "icon_search",
                                        handler: Buscar
                                    },
                                    {
                                        text: 'Limpiar',
                                        iconCls: "icon_limpiar",
                                        handler: Limpiar
                                    }
                            ],                
                            items: [
                                            {html:"&nbsp;",border:false,width:50},
                                            DTFechaDesde,
                                            {html:"&nbsp;",border:false,width:50},
                                            DTFechaHasta,
                                            {html:"&nbsp;",border:false,width:50},
                                            {html:"&nbsp;",border:false,width:50},
                                            estado_cmb,                               
                                            {html:"&nbsp;",border:false,width:50},
                                            cboCiclosFacturacion,
                                            {html:"&nbsp;",border:false,width:50},
                            ],	
                            renderTo: 'filtro_pagos'
                        }); 
                

                function Buscar(){
                    if  (( Ext.getCmp('fechaDesde').getValue())&&(Ext.getCmp('fechaHasta').getValue()) )
                    {
                        if (Ext.getCmp('fechaDesde').getValue() > Ext.getCmp('fechaHasta').getValue())
                        {
                        Ext.Msg.show({
                        title:'Error en Busqueda',
                        msg: 'Por Favor para realizar la busqueda Fecha Desde debe ser fecha menor a Fecha Hasta.',
                        buttons: Ext.Msg.OK,
                        animEl: 'elId',
                        icon: Ext.MessageBox.ERROR
                            });		 

                        }
                        else
                        {
                            store.load({params: {start: 0, limit: 20}});
                        }
                    }
                    else
                    {
                                store.load({params: {start: 0, limit: 20}});
                    }	
                }
                    
                    function Limpiar(){   
                        Ext.getCmp('fechaDesde').setValue('');
                        Ext.getCmp('fechaHasta').setValue('');
                        Ext.getCmp('idestado').setValue('');
                    }

        },
        failure: function(result)
        {
            Ext.Msg.alert('Error',result.responseText); 
        }
    }); 
});
/**
* Documentación para funcion 'getParametroDebitoPlanificado'.
* se obtiene el mensaje parametrizados para las validaciones
* @param archivos : archivos a presentar para descargarlos
* @author <icromero@telconet.ec>
* @since 05/06/2021
* @version 1.1
* @return String
*/
function getParametroDebitoPlanificado(strMensajesDebitos,strDescripcion)
{       var json = Ext.JSON.decode(strMensajesDebitos);
        var mensaje ='null';
        json.encontrados.forEach(element => {
            if(element.strDescripcion === strDescripcion){
                mensaje = element.strValor;
            }
        });
        return mensaje;
}
            /**
            * Documentación para funcion 'descargar'.
            * muestra los archivos de respuesta subidos en el debito escogido
            * @param archivos : archivos a presentar para descargarlos
            * @author <amontero@telconet.ec>
            * @since 03/09/2015
            * @version 1.1
            * @return String
            */
            function descargar(archivos)
            {
                var files = archivos.split("|"); 
                var datos = '<table width="80%" id="rounded-corner">';
                for(i = 0; i < files.length ; i++) 
                { // listando los elementos del array
                    if(files[i]!='')
                    {
                        var files01 = files[i].split("*");
                        datos       = datos+'<tr>\n\<td><a href="'+files01[0]+
                            '" class="button-grid-excel"></a><a href="'+files01[0]+'">'+files01[1]+'</a></td></tr>';
                    }
                }
                datos             = datos+'</table>';
                var panelarchivos = Ext.create('Ext.panel.Panel', {
                    title: '',
                    width: 100,
                    html: datos,
                    renderTo: Ext.getBody()
                });
                winDetalle = Ext.widget('window', 
                {
                    title: 'Archivos de debitos no encontrados',
                    closeAction: 'hide',
                    closable: true,
                    width: 550,
                    height: 200,
                    minHeight: 200,
                    layout: 'fit',
                    resizable: true,
                    modal: true,
                    items: panelarchivos
                });

                winDetalle.show();
            }
        
function ejecutarCadaTiempo(){

    store.load({params: {start: 0, limit: 20}});
}
        
setInterval('ejecutarCadaTiempo()',150000);

            
function accionEnDebitos(idDebitoGeneral,accion){
    var nombreBotonGuardar='';
    var tituloVentana='';
    var urlAccion='';
    if (accion==='Reabrir'){
        nombreBotonGuardar='Reabrir';
        tituloVentana='Reabrir Debitos';
        urlAccion=url_reabre_cabecera_debitos;
    }        
    else
    {
        if(accion==='Anular')
            nombreBotonGuardar='Anular';
            tituloVentana='Anular Debitos';
            urlAccion=url_anula_cabecera_debitos;
    }    
    var chkBxGrp= new Ext.form.CheckboxGroup(        
    {        
        xtype: 'checkboxgroup',
        fieldLabel: '',
        name: 'CheckBoxGrp',
        labelSeparator: '',
        itemCls: 'x-check-group-alt',
        columns: 1,
        vertical: true
    });        

    panelDebitos=Ext.create('Ext.form.Panel', {
        title: '',
        renderTo: Ext.getBody(),
        bodyPadding: 5,
        width: 350,
        items: [
            {
                xtype: 'displayfield',
                fieldLabel: '',
                name: 'home_score',
                value: 'Seleccione banco(s) que desea '+accion+'.'
            },
            chkBxGrp
        ],
        buttons: 
        [
            {
                text: nombreBotonGuardar,
                name: 'guardarBtn',
                disabled: false,
                handler: function() {
                    var arrayBancos=chkBxGrp.getChecked();
                    var strIdsDebitosCab="";
                    for(var i=0;i<arrayBancos.length;i++){
                        strIdsDebitosCab=strIdsDebitosCab+arrayBancos[i].inputValue+"|";
                    }  
                    //console.log(url_anula_cabecera_debitos);
                    var form1 = this.up('form').getForm();
                    if (form1.isValid() && strIdsDebitosCab!=='') {

                        Ext.MessageBox.show({
                            msg: 'Guardando datos...',
                            title: 'Procesando',
                            progressText: 'Mensaje',
                            progress: true,
                            closable: false,
                            width: 300,
                            wait: true,
                            waitConfig: {interval: 200}
                        });

                        Ext.Ajax.request({
                            url: urlAccion,
                            method: 'POST',
                            params: {
                                accion: accion,
                                debitosCabId: strIdsDebitosCab
                             },
                            success: function(response, request) {
                                Ext.MessageBox.hide();
                                var obj = Ext.decode(response.responseText);
                                if (obj.success) {
                                    //Ext.getCmp('listView').getStore().load();
                                    listView.getStore().load();
                                    Ext.MessageBox.show({
                                        modal: true,
                                        title: 'Información',
                                        msg: 'Guardado correctamente.',
                                        width: 300,
                                        icon: Ext.MessageBox.INFO,
                                        buttons: Ext.Msg.OK
                                    });
                                    form1.reset();
                                    winDebitos.destroy();
                                } else {
                                    Ext.MessageBox.show({
                                        modal: true,
                                        title: 'Error',
                                        msg: 'Error al guardar.',
                                        width: 300,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                }

                            },
                            failure: function() {
                                Ext.MessageBox.show({
                                    modal: true,
                                    title: 'Error',
                                    msg: 'Error al guardar.',
                                    width: 300,
                                    icon: Ext.MessageBox.ERROR
                                });
                            }
                        });

                    } 
                    else {
                            Ext.MessageBox.show({
                                modal: true,
                                title: 'Información',
                                msg: 'Por favor seleccione al menos un Banco.',
                                width: 300,
                                icon: Ext.MessageBox.ERROR
                            });
                    }
                }
            },
            {
                text: 'Cancelar',
                handler: function() {
                    this.up('form').getForm().reset();
                    this.up('window').destroy();
                }
            }
        ]
    });


    Ext.define('modelCabeceraDebitos', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idDebitoGeneral',  type: 'string'},
            {name: 'nombreGrupo',  type: 'string'},
            {name: 'estado',  type: 'string'},
            {name: 'debitosPendientes',  type: 'string'},
            {name: 'debitosProcesados',  type: 'string'},
            {name: 'debitosRechazados',  type: 'string'},
            {name: 'totalCabeceras',  type: 'string'},
            {name: 'totalCabecerasPendientes',  type: 'string'}
        ]
    });	
    var storeCabeceraDebitos = Ext.create('Ext.data.JsonStore', {
        model: 'modelCabeceraDebitos',
        pageSize: 999,
        proxy: {
            type: 'ajax',
            url: url_get_grupo_debitos,
            reader: {
                type: 'json',
                root: 'debitosCab'
            },
            extraParams:{debitoGeneralId:idDebitoGeneral,accion:accion},
            simpleSortMode: true
        },
        listeners: {
            load: function(store){
                var indice=0;
                store.each(function(record) {
                    var objPendientes               = new Number((record.data.debitosPendientes)+'').toFixed(parseInt(2));
                    var objProcesados               = new Number((record.data.debitosProcesados)+'').toFixed(parseInt(2));
                    var objRechazados               = new Number((record.data.debitosRechazados)+'').toFixed(parseInt(2));  
                    var objTotalCabeceras           = new Number((record.data.totalCabeceras)+'').toFixed(parseInt(2));  
                    var objTotalCabecerasPendientes = new Number((record.data.totalCabecerasPendientes)+'').toFixed(parseInt(2));  
                    var totalDebitos                = parseFloat(objPendientes)+parseFloat(objProcesados)+parseFloat(objRechazados);
                    var nombreBanco                 = record.data.nombreGrupo;
                    var tempObj="";
                    if(accion==='Anular' && (parseFloat(objProcesados)>0 || parseFloat(objRechazados)>0)){
                        tempObj = new Ext.form.Checkbox({
                        checked:false, boxLabel: nombreBanco+"<br> [no se puede anular porque ya tiene debitos procesados o anulados]", 
                        name: record.data.idDebitoGeneral+"_"+indice, inputValue: record.data.idDebitoGeneral, disabled:false,
                        listeners: {
                            click: 
                            {
                                element: 'el', //bind to the underlying el property on the panel
                                fn: function()
                                { 
                                    alert(nombreBanco+" no se puede anular porque tiene ya debitos procesados o rechazados."); 
                                    tempObj.reset();
                                }
                            }
                        }                        
                        });
                    }
                    else{
                        if(accion==='Anular' && parseFloat(objPendientes)===totalDebitos){
                            tempObj = new Ext.form.Checkbox({
                            checked:false, boxLabel: nombreBanco, name: record.data.idDebitoGeneral+"_"+indice, 
                            inputValue: record.data.idDebitoGeneral
                            });
                        }
                        else{                            
                            if(accion==='Reabrir' && parseFloat(objTotalCabeceras)===parseFloat(objTotalCabecerasPendientes)){
                                tempObj = new Ext.form.Checkbox({
                                checked:false, boxLabel: nombreBanco+"<br> [Debito ya esta Abierto y se puede procesar]", 
                                name: record.data.idDebitoGeneral+"_"+indice, inputValue: record.data.idDebitoGeneral,
                                listeners: {
                                    click: {
                                        element: 'el', //bind to the underlying el property on the panel
                                        fn: function()
                                        { 
                                            alert(nombreBanco+" ya esta Abierto y se puede procesar."); tempObj.reset();
                                        }
                                    }
                                }
                                });
                            }
                            else{
                                if(accion==='Reabrir'&& parseFloat(objPendientes) > 0)
                                {
                                    tempObj = new Ext.form.Checkbox({
                                    checked:true, boxLabel: nombreBanco, name: record.data.idDebitoGeneral+"_"+indice, 
                                    inputValue: record.data.idDebitoGeneral
                                    });
                                }                                   
                            }
                                
                        }
                    }
                    chkBxGrp.items.add(tempObj);
                    chkBxGrp.doLayout();
                    indice++;
                });
            }                
        },
        sortOnLoad : true,
        sorters : {
            property : 'banco',
            direction : 'ASC'
        }
    });

    storeCabeceraDebitos.load(); 

    winDebitos = Ext.widget('window', {
        title: tituloVentana,
        closeAction: 'hide',
        closable: true,
        width: 400,
        height: 250,
        minHeight: 300,
        autoScroll:true,
        layout: 'fit',
        resizable: true,
        modal: true,
        items: panelDebitos
    });

    winDebitos.show();
}        