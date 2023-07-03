Ext.require
([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);

var itemsPerPage    = 3000;
var store           = null;
var estado_id       = '';
var area_id         = '';
var login_id        = '';
var tipo_asignacion = '';
var pto_sucursal    = '';
var gridTotalizado  = null;
var idClienteSucursalSesion;

var boolPermisoAprobar  = false;
var boolPermisoRechazar = false;
var boolPermisoExportar = false;

Ext.onReady(function()
{
    var permisoComboOficina     = $("#ROLE_185-165");
    var boolPermisoComboOficina = (typeof permisoComboOficina === 'undefined') ? false : (permisoComboOficina.val() == 1 ? true : false);
    var boolOcultarComboOficina = boolPermisoComboOficina ? false: true;
    
    var permisoAprobar = $("#ROLE_185-4737");
    boolPermisoAprobar = (typeof permisoAprobar === 'undefined') ? false : (permisoAprobar.val() == 1 ? true : false);
    
    var permisoRechazar = $("#ROLE_185-4738");
    boolPermisoRechazar = (typeof permisoRechazar === 'undefined') ? false : (permisoRechazar.val() == 1 ? true : false);
    
    var permisoExportar = $("#ROLE_185-4757");
    boolPermisoExportar = (typeof permisoExportar === 'undefined') ? false : (permisoExportar.val() == 1 ? true : false);
    
    /**
     * COMBO OFICINAS
     */
    Ext.define('modelOficina',
    {
        extend: 'Ext.data.Model',
        fields:
        [
            {name: 'id_oficina_grupo', type: 'int'},
            {name: 'nombre_oficina',   type: 'string'}                    
        ]
    });	

    var storeOficinas = Ext.create('Ext.data.JsonStore',
    {
        autoLoad: boolPermisoComboOficina,
        model: "modelOficina",
        proxy: 
        {
            type: 'ajax',
            method: 'post',
            url : strUrlGetOficinas,
            timeout: 9000000,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams:
            {
                estado: 'Activo'
            }
        },
        listeners: 
        {
            load: function(store, records)
            {
                var intContador = 0;

                store.insert(0, [{ id_oficina_grupo: '0', nombre_oficina: 'TODAS' } ]);

                store.each(function(record,id)
                {
                    if( record.get('id_oficina_grupo') == intIdOficina )
                    {
                        cmbOficinas.setValue(store.getAt(intContador).get('id_oficina_grupo'));
                    }

                    intContador ++;
                });
            }      
        }
    });	


    var cmbOficinas = {html:"&nbsp;",border:false,width:10};

    if( !boolOcultarComboOficina )
    {
        cmbOficinas = new Ext.form.ComboBox
        ({
            xtype: 'combobox',
            store: storeOficinas,
            labelAlign : 'left',
            id:'cmbOficina',
            name: 'cmbOficina',
            valueField:'id_oficina_grupo',
            displayField:'nombre_oficina',
            fieldLabel: 'Oficinas',
            width: 300,
            allowBlank: false,	
            minChars:4,
            loadingText: 'Buscando...',
            editable: false,
            queryMode: 'local',
            hidden: boolOcultarComboOficina,	  
            listeners:
            {
                select: 
                {
                    fn:function(e)
                    {
                        intIdOficina = Ext.getCmp('cmbOficina').getValue();
                    }
                }	      
            }
        });
    }
    /**
     * FIN COMBO OFICINAS
     */
                
                
            //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		
            DTFechaDesde = new Ext.form.DateField({
                    id: 'fechaDesde',
                    fieldLabel: 'Desde',
                    labelAlign : 'left',
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    width:200,
            });
            
            DTFechaHasta = new Ext.form.DateField({
                    id: 'fechaHasta',
                    fieldLabel: 'Hasta',
                    labelAlign : 'left',
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    width:200,
            });
			
			DTFechaEmision = new Ext.form.DateField({
                    id: 'fechaEmision',
                    fieldLabel: 'Fecha emision',
                    labelAlign : 'left',
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    width:200,
            });
            
            //CREAMOS DATA STORE PARA CLIENTES
            Ext.define('modelCliente', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'idcliente', type: 'int'},
                    {name: 'descripcion',  type: 'string'}                    
                ]
            });	
            		
            var estado_clientes = Ext.create('Ext.data.Store', {
		    autoLoad: false,
		    model: "modelCliente",
		    proxy: {
		        type: 'ajax',
		        url : url_store_clientes,
		        reader: {
		            type: 'json',
		            root: 'clientes'
                        }
                    }
            });	
            
            clientes_cmb = new Ext.form.ComboBox({
                xtype: 'combobox',
                store: estado_clientes,
                labelAlign : 'left',
                id:'idCliente',
                name: 'idCliente',
				valueField:'idcliente',
                displayField:'descripcion',
                fieldLabel: 'Clientes',
				width: 300,
				allowBlank: false,	
                minChars:4,
                loadingText: 'Searching...',
			});
            
            
            //CREAMOS DATA STORE PARA FILTRO DE USR CREACION
            Ext.define('modelUsrCreacion', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'id', type: 'int'},
                    {name: 'valor2',  type: 'string'}
                ]
            });
           
            var storeUsrCreacion = Ext.create('Ext.data.Store', {
            autoLoad: false,
            model: "modelUsrCreacion",
            proxy: {
                type: 'ajax',
                url : url_store_users,
                reader: {
                    type: 'json',
                    root: 'encontrados'
                        }
                    }
            });
            
            users_cmb = new Ext.form.ComboBox({
                xtype: 'combobox',
                store: storeUsrCreacion,
                labelAlign : 'left',
                id:'usrCreacion',
                name: 'usrCreacion',
                valueField:'valor2',
                displayField:'valor2',
                fieldLabel: 'Usuario Creaci\u00f3n',
                width: 300,
                allowBlank: false,	
                minChars:4,
                loadingText: 'Buscando...',
            });
			
			Ext.define('ListadoPtosClientes', 
            {
				extend: 'Ext.data.Model',
				fields: [
					{name:'idPtoCliente', type:'int'},
					{name:'descripcionPto', type:'string'}
				]
			});
				
            listado_ptos_clientes = Ext.create('Ext.data.Store', 
            {
                autoLoad: false,
                model: 'ListadoPtosClientes',
                proxy: 
                {
                    type: 'ajax',
                    url : url_store_pto_clientes,
                    timeout: 9000000,
                    reader: 
                    {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'listado'
                    },
                    extraParams:
                    {
                        idCliente:''
                    },
                    simpleSortMode: true
                },
                listeners:
                {
                    beforeload: function(store)
                    {
                        listado_ptos_clientes.getProxy().extraParams.idCliente= Ext.getCmp('idCliente').getValue();
                    }
                }
            });
    
			clientes_ptos_cmb = new Ext.form.ComboBox({
                xtype: 'combobox',
                store: listado_ptos_clientes,
                labelAlign : 'left',
                id:'idPtoCliente',
                name: 'idPtoCliente',
				valueField:'idPtoCliente',
                displayField:'descripcionPto',
                fieldLabel: 'Ptos Clientes',
				width: 300,
				triggerAction: 'all',
				selectOnFocus:true,
				lastQuery: '',
				mode: 'local',
				allowBlank: false,	
			});


                Ext.define('ListaDetalleModel', {
                    extend: 'Ext.data.Model',
                    fields: 
                        [
                            {name:'id',                  type: 'string'},
                            {name:'codigoTipoDocumento', type: 'string'},
                            {name:'documento',           type: 'string'},
                            {name:'oficina',             type: 'string'},
                            {name:'cliente',             type: 'string'},
                            {name:'punto',               type: 'string'},
                            {name:'subtotal',            type: 'string'},
                            {name:'impuestos',           type: 'string'},
                            {name:'descuento',           type: 'string'},
                            {name:'total',               type: 'string'},
                            {name:'feCreacion',          type: 'string'},
                            {name:'strLinkShow',         type: 'string'},
                            {name:'strLinkClone',         type: 'string'},
                            {name:'vendedor',            type: 'string'},
                            {name:'observacion',         type: 'string'},
                            {name:'usrCreacion',         type: 'string'}
                         ]
                });
                

                store = Ext.create('Ext.data.JsonStore',
                {
                    autoLoad: true,
                    model: 'ListaDetalleModel',
                    pageSize: itemsPerPage,
                    proxy: 
                    {
                        type: 'ajax',
                        url: url_store_grid,
                        timeout: 9000000,
                        reader: 
                        {
                            type: 'json',
                            root: 'documentos',
                            totalProperty: 'total'
                        },
                        extraParams:
                        {
                            fechaDesde:     '',
                            fechaHasta:     '', 
                            idCliente:      '', 
                            idPtoCliente:   '',
                            fechaEmision:   '',
                            usrCReacion:   '',
                            intIdOficina:   0
                        },
                        simpleSortMode: true
                    },
                    listeners: 
                    {
                        beforeload: function(store)
                        {
                            store.getProxy().extraParams.fechaDesde     = Ext.getCmp('fechaDesde').getValue();
                            store.getProxy().extraParams.fechaHasta     = Ext.getCmp('fechaHasta').getValue();   
                            store.getProxy().extraParams.idCliente      = Ext.getCmp('idCliente').getValue();
                            store.getProxy().extraParams.idPtoCliente   = Ext.getCmp('idPtoCliente').getValue();
                            store.getProxy().extraParams.fechaEmision   = Ext.getCmp('fechaEmision').getValue();
                            store.getProxy().extraParams.usrCreacion    = Ext.getCmp('usrCreacion').getValue();
                            store.getProxy().extraParams.intIdOficina   = intIdOficina;  
                        },
                        load: function(store)
                        {
                            if( gridTotalizado != null )
                            {
                                var itemTotalizado           = store.getProxy().getReader().jsonData.totalizados[0];
                                var floatSubtotal            = parseFloat(itemTotalizado.subtotal).toFixed(2);
                                var floatSubtotalConImpuesto = parseFloat(itemTotalizado.subtotalConImpuesto).toFixed(2);
                                var floatSubtotalDescuento   = parseFloat(itemTotalizado.subtotalDescuento).toFixed(2);
                                var floatValorTotal          = parseFloat(itemTotalizado.valorTotal).toFixed(2);

                                //Verificacion de variables si no poseen valor
                                if(isNaN(floatSubtotal))
                                {
                                    floatSubtotal = 0;
                                }

                                if(isNaN(floatSubtotalConImpuesto))
                                {
                                    floatSubtotalConImpuesto = 0;
                                }

                                if(isNaN(floatSubtotalDescuento))
                                {
                                    floatSubtotalDescuento = 0;
                                }

                                if(isNaN(floatValorTotal))
                                {
                                    floatValorTotal = 0;
                                }
                                
                                document.getElementById('floatSubtotal-body').innerHTML            = floatSubtotal;
                                document.getElementById('floatSubtotalConImpuesto-body').innerHTML = floatSubtotalConImpuesto;
                                document.getElementById('floatSubtotalDescuento-body').innerHTML   = floatSubtotalDescuento;
                                document.getElementById('floatValorTotal-body').innerHTML          = floatValorTotal;
                            }
                        }
                    }
                });

                sm = new Ext.selection.CheckboxModel( {
                    listeners:
                        {
                            selectionchange: function(selectionModel, selected, options)
                            {
                                arregloSeleccionados= new Array();
                                Ext.each(selected, function(record)
                                {
                                        //arregloSeleccionados.push(record.data.idOsDet);
                                });			

                            }
                       }
                });

				Ext.define('Ext.grid.RowNumberer', {
					extend: 'Ext.grid.column.Column',
					alias: 'widget.rownumberer',
					text: "&#160",
					width: 40,
					align: 'right',
					constructor : function(config){
						this.callParent(arguments);
						if (this.rowspan) {
							this.renderer = Ext.Function.bind(this.renderer, this);
						}
					},
					// private
					resizable: false,
					hideable: false,
					menuDisabled: true,
					dataIndex: '',
					cls: Ext.baseCSSPrefix + 'row-numberer',
					rowspan: undefined,
					renderer: function(value, metaData, record, rowIdx, colIdx, store) {
						if (this.rowspan){
							metaData.cellAttr = 'rowspan="'+this.rowspan+'"';
						}

						metaData.tdCls = Ext.baseCSSPrefix + 'grid-cell-special';
						return store.indexOfTotal(record) + 1;
					}
				});

                
                var listView = Ext.create('Ext.grid.Panel', {
                    width:1350,
                    height:1300,
                    collapsible:false,
                    title: '',
                    selModel: sm,
                    dockedItems: [ 
                        {
                            xtype: 'toolbar',
                            dock: 'top',
                            align: '->',
                            items: [
                                { xtype: 'tbfill' },
                                {
                                    iconCls: 'icon_aprobar',
                                    text: 'Aprobar',
                                    itemId: 'aprobar',
                                    scope: this,
                                    disabled: !boolPermisoAprobar,
                                    handler: function(){Procesar()}
                                },{
                                    iconCls: 'icon_delete',
                                    text: 'Rechazar',
                                    itemId: 'rechazar',
                                    scope: this,
                                    disabled: !boolPermisoRechazar,
                                    handler: function(){Rechazar()}
                                },{
                                    iconCls: 'x-btn-icon icon_exportar',
                                    text: 'Exportar',
                                    itemId: 'exportar',
                                    scope: this,
                                    disabled: !boolPermisoExportar,
                                    handler: function(){Exportar()}
                                }
                            ]
                        }
                    ],                 
                    renderTo: Ext.get('lista_facturas'),
                    bbar: Ext.create('Ext.PagingToolbar', {
                        store: store,
                        displayInfo: true,
                        displayMsg: 'Mostrando facturas {0} - {1} of {2}',
                        emptyMsg: "No hay datos para mostrar"
                    }),	
                    store: store,
                    multiSelect: false,
                    viewConfig: {
                        emptyText: 'No hay datos para mostrar',
                         listeners: {
                            viewready: function(view) {
                                myFunction();
                            }
                        }
                    },
                    listeners:{
                            itemdblclick: function( view, record, item, index, eventobj, obj ){
                                var position = view.getPositionByEvent(eventobj),
                                data = record.data,
                                value = data[this.columns[position.column].dataIndex];
                                Ext.Msg.show({
                                    title:'Copiar texto?',
                                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                                    buttons: Ext.Msg.OK,
                                    icon: Ext.Msg.INFORMATION
                                });
                            }
                    },
                    columns: [new Ext.grid.RowNumberer(),
                    {
                        text: 'F. Creacion',
                        dataIndex: 'feCreacion',
                        align: 'right',
                        width: 80			
                    },{
                        text: 'Oficina',
                        width: 130,
                        dataIndex: 'oficina'
                    },{
                        text: 'Tipo. Doc.',
                        width: 65,
                        dataIndex: 'codigoTipoDocumento',
                        align: 'center'
                    },{
                        text: 'No. documento',
                        width: 90,
                        dataIndex: 'documento',
                        align: 'center'
                    },{
                        text: 'Observación',
                        width: 140,
                        dataIndex: 'observacion'
                    },{
                        text: 'Usuario Creaci\u00f3n',
                        width: 100,
                        dataIndex: 'usrCreacion'
                    },{
                        text: 'Cliente',
                        width: 110,
                        dataIndex: 'cliente'
                    },{
                        text: 'Pto cliente',
                        width: 100,
                        dataIndex: 'punto'
                    },{
                        text: 'Vendedor',
                        width: 110,
                        dataIndex: 'vendedor'
                    },{
                        text: 'Subtotal',
                        width: 70,
                        align: 'right',
                        dataIndex: 'subtotal',
                        renderer: function(value)
                        {
                            return Ext.util.Format.number(value, '0,000.00');
                        }
                    },{
                        text: 'Impuesto',
                        width: 60,
                        align: 'right',
                        dataIndex: 'impuestos',
                        renderer: function(value)
                        {
                            return Ext.util.Format.number(value, '0,000.00');
                        }
                    },{
                        text: 'Descuento',
                        dataIndex: 'descuento',
                        align: 'right',
                        width: 70,
                        renderer: function(value)
                        {
                            return Ext.util.Format.number(value, '0,000.00');
                        }
                    },{
                        text: 'Total',
                        dataIndex: 'total',
                        align: 'right',
                        width: 85,
                        renderer: function(value)
                        {
                            return Ext.util.Format.number(value, '0,000.00');
                        }
                    },{
                        text: 'Acciones',
                        width: 60,
                        renderer: renderAcciones,
                    }
                ]
                });


            function renderAcciones(value, p, record) {
                    var iconos='';
                    iconos=iconos+'<b><a target="_blank" href="' + record.data.strLinkShow + '" onClick="" title="Ver" class="button-grid-show x-action-col-icon x-action-col-0"></a></b>';			
                    if(record.data.strLinkClone!=null && record.data.strLinkClone!="")
                    {
                        iconos=iconos+'<b><a target="_blank" href="' + record.data.strLinkClone + '" onClick="" title="Clonar Prefactura" class="x-action-col-icon x-action-col-0 button-grid-clonar-factura"></a></b>';
                    }
                    
                    return Ext.String.format(
                                    iconos,
                                    value,
                                    '1',
                                    'nada'
                    );
            }

            var filterPanel = Ext.create('Ext.panel.Panel', {
                bodyPadding: 7, 
                border:false,
                buttonAlign: 'center',
                layout:{
                    type:'table',
                    columns: 5,
                    align: 'left',
                },
                bodyStyle: {
                            background: '#fff'
                        },                     

                collapsible : true,
                collapsed: false,
                width: 1350,
                title: 'Criterios de busqueda',
                
                    buttons: [
                        
                        {
                            text: 'Buscar',
                            iconCls: "icon_search",
                            handler: Buscar,
                        },
                        {
                            text: 'Limpiar',
                            iconCls: "icon_limpiar",
                            handler: function(){ limpiar();}
                        }
                        
                        ],                

                        items: 
                        [
                            DTFechaDesde,
                            {html:"&nbsp;",border:false,width:10},
                            DTFechaHasta,
                            {html:"&nbsp;",border:false,width:10},
                            cmbOficinas,
                            clientes_cmb,
                            {html:"&nbsp;",border:false,width:10},
                            clientes_ptos_cmb,
                            {html:"&nbsp;",border:false,width:10},
                            users_cmb                            
                        ],	
                renderTo: 'filtro_facturas'
            }); 
			
            var filterPanelProcesar = Ext.create('Ext.panel.Panel', {
                bodyPadding: 7,  // Don't want content to crunch against the borders
                border:false,
                buttonAlign: 'center',
                layout:{
                    type:'table',
                    columns: 5,
                    align: 'left',
                },
                bodyStyle: {
                            background: '#fff'
                        },                     

                collapsible : true,
                collapsed: false,
                width: 1350,
                title: 'Procesar facturas seleccionadas',                
                        items: [
                                DTFechaEmision,
                                {html:"&nbsp;",border:false,width:10},
                                {html:"&nbsp;",border:false,width:50},
                                ],	                
                renderTo: 'filtro_procesar'
            }); 
            
            verificarCmbSesion();
            
    });

    function Buscar()
    {
        store.load({params: {start: 0, limit: 3000}});
    }
    
    function Procesar()
    {
        var strIdDocumentos = '';
        var fechaEmision    = Ext.getCmp('fechaEmision').getValue();
        
        if (!boolPermisoAprobar)
        {
            Ext.Msg.show(
                {
                    title: 'Error',
                    msg: 'No tiene permisos para realizar esta acción',
                    buttons: Ext.Msg.OK,
                    icon: Ext.MessageBox.ERROR
                }
            );
        }
        else if (sm.getSelection().length == 0)
        {
            Ext.Msg.alert('Alerta', 'Seleccione por lo menos un registro de la lista');
        }
        else
        {
            for(var i=0; i<sm.getSelection().length; ++i)
            {
                strIdDocumentos = strIdDocumentos + sm.getSelection()[i].data.id;

                if(i < (sm.getSelection().length -1))
                {
                    strIdDocumentos = strIdDocumentos + '|';
                }
            }
            
            if( !Ext.isEmpty(fechaEmision) )
            {
                Ext.Msg.confirm('Atención','Se procesaran los registros seleccionados. Desea continuar?', function(btn)
                {
                    if(btn=='yes')
                    {
                        Ext.MessageBox.wait("Procesando las facturas...");
                        
                        Ext.Ajax.request
                        ({
                            url: direccion,
                            method: 'post',
                            timeout: 9000000,
                            params:
                            {
                                fechaEmision:    fechaEmision,
                                strTipoDoc:      strTipoDoc,
                                strIdDocumentos: strIdDocumentos
                            },
                            success: function(response)
                            {
                                Ext.MessageBox.hide();
                                
                                if( "OK" === response.responseText)
                                {
                                    Ext.MessageBox.wait("Se procesaron los documentos seleccionados con éxito.<br/><br/>"+
                                                        "Actualizando la información de las facturas...");
                                                    
                                    window.location.assign(url_procesadas);
                                }
                                else
                                {
                                    Ext.Msg.alert('Error', response.responseText);
                                }
                            },
                            failure: function(result)
                            {
                                Ext.MessageBox.hide();
                                Ext.Msg.alert('Error ','Error: ' + result.statusText);
                            }
                        });
                    }
                });
            }
            else
            {
                Ext.Msg.alert('Alerta','Ingrese Fecha de Emisión de facturas a procesar');
            }
        }
    }
    
    function Rechazar()
    {
        var param = '';
        
        if (!boolPermisoRechazar)
        {
            Ext.Msg.show(
                {
                    title: 'Error',
                    msg: 'No tiene permisos para realizar esta acción',
                    buttons: Ext.Msg.OK,
                    icon: Ext.MessageBox.ERROR
                }
            );
        }
        else if(sm.getSelection().length > 0)
        {
            var estado = 0;
            for(var i=0 ;  i < sm.getSelection().length ; ++i)
            {
                param = param + sm.getSelection()[i].data.id;

                if(sm.getSelection()[i].data.estado == 'Eliminado')
                {
                    estado = estado + 1;
                }
                if(i < (sm.getSelection().length -1))
                {
                    param = param + '|';
                }
            }
            Ext.Msg.confirm('Alerta','Se rechazarán los registros seleccionadas. Desea continuar?', 
            function(btn){
				if(btn=='yes'){
					Ext.Ajax.request({
						url: direccion_rechazo,
						timeout:600000,
						method: 'post',
						params: { 
							param : param, 
						},
						success: function(response){
							var text = response.responseText;
							store.load();
						},
						failure: function(result)
						{
							Ext.Msg.alert('Error ','Error: ' + result.statusText);
						}
					});
				}
			});
		}
		else
		{
		  alert('Seleccione por lo menos un registro de la lista');
		}
    }
    
    function Exportar()
    {
        
        
        if (!boolPermisoExportar)
        {
            Ext.Msg.show(
                {
                    title: 'Error',
                    msg: 'No tiene permisos para realizar esta acción',
                    buttons: Ext.Msg.OK,
                    icon: Ext.MessageBox.ERROR
                }
            );
        }
        else 
        {
            var fechaHasta   = '';
            var fechaDesde   = ''; 
            var idCliente    = '';
            var idPtoCliente = '';
            var usrCreacion  = '';

            if(Ext.getCmp('fechaHasta').getValue()) 
            {
                fechaHasta = Ext.getCmp('fechaHasta').getValue();
            }

            if(Ext.getCmp('fechaDesde').getValue())
            {
                fechaDesde = Ext.getCmp('fechaDesde').getValue();
            }

            if(Ext.getCmp('idCliente').getValue())
            {
                idCliente  = Ext.getCmp('idCliente').getValue();
            }

            if(Ext.getCmp('idPtoCliente').getValue())
            {
                idPtoCliente = Ext.getCmp('idPtoCliente').getValue();
            }
            
            if(Ext.getCmp('usrCreacion').getValue()) 
            {
                usrCreacion = Ext.getCmp('usrCreacion').getValue();
            }                        


            Ext.MessageBox.confirm(
               'Exportar Excel',
               '¿ Generar reporte?',
               function(btn) 
               {
                   if (btn === 'yes') 
                   {
                    if(fechaDesde)
                    {
                            fechaDesde = new Date(fechaDesde).toISOString();
                    }
                    if(fechaHasta)
                    {
                        fechaHasta = new Date(fechaHasta).toISOString();
                    }

                     window.location =  url_exportar_excel + '?fechaDesde='+fechaDesde
                                                             + '&fechaHasta='+fechaHasta
                                                             + '&idCliente='+idCliente
                                                             + '&idPtoCliente='+idPtoCliente
                                                             + '&intIdOficina='+intIdOficina
                                                             + '&usrCreacion='+usrCreacion
                                                             + '&strTipoDoc='+strTipoDoc;
                   }  
               });
        }
    }

    function limpiar(){
        Ext.getCmp('fechaDesde').setValue('');
        Ext.getCmp('fechaHasta').setValue('');
        Ext.getCmp('idCliente').setValue('');
        Ext.getCmp('idPtoCliente').setValue('');
        Ext.getCmp('fechaEmision').setValue('');
        Ext.getCmp('usrCreacion').setValue('');
        //Recarga el store
        store.load();
    }
    
    function verificarCmbSesion()
    {
        if(cliente=="S")
        {
            clientes_cmb.setVisible(false);
            clientes_ptos_cmb.setVisible(false);
        }
        else
        {
            clientes_cmb.setVisible(true);
            clientes_ptos_cmb.setVisible(true);
        }
	}
    
    function myFunction() 
    {
        var floatSubtotal            = 0;
        var floatSubtotalConImpuesto = 0;
        var floatSubtotalDescuento   = 0;
        var floatValorTotal          = 0;
        
        if( store != null )
        { 
            if( !isNaN(store.getProxy().getReader().jsonData) )
            {
                var jsonData             = store.getProxy().getReader().jsonData.totalizados[0];
                floatSubtotal            = parseFloat(jsonData.subtotal).toFixed(2);
                floatSubtotalConImpuesto = parseFloat(jsonData.subtotalConImpuesto).toFixed(2);
                floatSubtotalDescuento   = parseFloat(jsonData.subtotalDescuento).toFixed(2);
                floatValorTotal          = parseFloat(jsonData.valorTotal).toFixed(2);
            }//( !isNaN(store.getProxy().getReader().jsonData) )
        }//( store != null )

        //Verificacion de variables si no poseen valor
        if(isNaN(floatSubtotal))
        {
            floatSubtotal=0;
        }
        
        if(isNaN(floatSubtotalConImpuesto))
        {
            floatSubtotalConImpuesto=0;
        }
        
        if(isNaN(floatSubtotalDescuento))
        {
            floatSubtotalDescuento=0;
        }
        
        if(isNaN(floatValorTotal))
        {
            floatValorTotal=0;
        }
        
        gridTotalizado = Ext.create('Ext.panel.Panel', 
        {
            title: 'Totalizado de facturación',
            width: 1350,
            height: 100,
            layout: 
            {
                type: 'table',
                columns: 4
            },
            defaults: 
            {
                bodyStyle:'padding:10px'
            },
            items: 
            [
                {
                    title: 'Subtotal',
                    id: 'floatSubtotal',
                    name: 'floatSubtotal',
                    width: 375,
                    height: 55,
                    html: floatSubtotal
                },
                {
                    title: 'Descuento',
                    width: 375,
                    height: 55,
                    id: 'floatSubtotalDescuento',
                    name: 'floatSubtotalDescuento',
                    html: floatSubtotalDescuento
                },
                {
                    title: 'Impuestos',
                    width: 375,
                    height: 55,
                    id: 'floatSubtotalConImpuesto',
                    name: 'floatSubtotalConImpuesto',
                    html: floatSubtotalConImpuesto
                },
                {
                    title: 'Total',
                    width: 375,
                    height: 55,
                    id: 'floatValorTotal',
                    name: 'floatValorTotal',
                    html: floatValorTotal
                }
            ],
            renderTo: totales_facturado
        });

    }
    
    Ext.override(Ext.MessageBox, {
        buttonText: { yes: "Sí", no: "No", cancel: "Cancelar" }
    });

