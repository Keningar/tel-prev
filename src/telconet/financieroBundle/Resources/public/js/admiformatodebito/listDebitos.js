            Ext.require([
                '*',
                'Ext.tip.QuickTipManager',
                    'Ext.window.MessageBox'
            ]);

            var itemsPerPage = 10;
            var store='';
            var estado_id='';
            var area_id='';
            var login_id='';
            var tipo_asignacion='';
            var pto_sucursal='';
            var idClienteSucursalSesion;


            Ext.onReady(function(){
			
            TFNumeroCuenta = new Ext.form.TextField({
                    id: 'numeroCuenta',
                    name: 'numerocuenta',
                    labelAlign:'right',
                    fieldLabel: 'Numero Cta/Tarj',
                    xtype: 'textfield',
                    width: '170px'
            });			

            //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		
            DTFechaDesde = new Ext.form.DateField({
                    id: 'fechaDesde',
                    fieldLabel: 'Desde',
                    labelAlign : 'left',
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    width:325
            });
            DTFechaHasta = new Ext.form.DateField({
                    id: 'fechaHasta',
                    fieldLabel: 'Hasta',
                    labelAlign : 'left',
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    width:325
            });

            //CREAMOS DATA STORE PARA EMPLEADOS
            Ext.define('modelBancos', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'id_banco', type: 'int'},
                    {name: 'descripcion_banco',  type: 'string'}                    
                ]
            });			
            var tipo_cuenta_store = Ext.create('Ext.data.Store', {
		    autoLoad: false,
		    model: "modelBancos",
		    proxy: {
		        type: 'ajax',
		        url : url_lista_bco_tipo_cta,
		        reader: {
		            type: 'json',
		            root: 'encontrados'
                        }
                    }
            });				
			
            var tipo_cuenta_cmb = new Ext.form.ComboBox({
                xtype: 'combobox',
                store: tipo_cuenta_store,
                labelAlign : 'left',
                id:'idtipocuenta',
                name: 'idtipocuenta',
		valueField:'id_banco',
                displayField:'descripcion_banco',
                fieldLabel: 'Banco',
		width: 325,
		triggerAction: 'all',
		selectOnFocus:true,
		lastQuery: '',
		mode: 'local',
		allowBlank: true,	
					
		listeners: {
                    select:
                    function(e) {
                        bcotc_id = Ext.getCmp('idtipocuenta').getValue();
                    },
                    click: {
                        element: 'el', //bind to the underlying el property on the panel
                        fn: function(){ 
                            bcotc_id='';
                            tipo_cuenta_store.removeAll();
                            tipo_cuenta_store.load();
                        }
                    }			
		}
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
		    autoLoad: false,
		    model: "modelEstado",
		    proxy: {
		        type: 'ajax',
		        url : url_lista_estados,
		        reader: {
		            type: 'json',
		            root: 'estados'
                        }
                    }
            });	
            var estado_cmb = new Ext.form.ComboBox({
                xtype: 'combobox',
                store: estado_store,
                labelAlign : 'left',
                id:'idestado',
                name: 'idestado',
		valueField:'descripcion',
                displayField:'descripcion',
                fieldLabel: 'Estado',
		width: 280,
		triggerAction: 'all',
		selectOnFocus:true,
		lastQuery: '',
		mode: 'local',
		allowBlank: true,	
					
		listeners: {
                    select:
                    function(e) {
                        estado_id = Ext.getCmp('idestado').getValue();
                    },
                    click: {
                        element: 'el', //bind to the underlying el property on the panel
                        fn: function(){ 
                            estado_id='';
                            estado_store.removeAll();
                            estado_store.load();
                        }
                    }			
		}
            });

    Ext.define('ListaDetalleModel', 
    {
        extend: 'Ext.data.Model',
        fields: 
        [
            {name:'id', type: 'int'},
            {name:'banco', type: 'string'},
            {name:'numeroCuenta', type: 'string'},
            {name:'cliente', type: 'string'},
            {name:'total', type: 'string'},
            {name:'debitado', type: 'string'},
            {name:'fechaCreacion', type: 'string'},
            {name:'usuarioCreacion', type: 'string'},
            {name:'estado', type: 'string'},
            {name:'observacionRechazo', type: 'string'},
            {name:'referencia', type: 'string'},            
            {name:'linkVer', type: 'string'},
            {name:'identificacion', type: 'string'}
        ]
    }); 


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
                        extraParams:{fechaDesde:'',fechaHasta:'', estado:'',debitoGeneralId:'',banco:''},
                        simpleSortMode: true
                    },
                    listeners: {
                        beforeload: function(store){
						store.getProxy().extraParams.fechaDesde= Ext.getCmp('fechaDesde').getValue();
						store.getProxy().extraParams.fechaHasta= Ext.getCmp('fechaHasta').getValue();   
                        store.getProxy().extraParams.estado= Ext.getCmp('idestado').getValue();
						store.getProxy().extraParams.banco= Ext.getCmp('idtipocuenta').getValue();
						store.getProxy().extraParams.numeroCuenta= Ext.getCmp('numeroCuenta').getValue();
						store.getProxy().extraParams.debitoGeneralId=debitoGenId;	
                        }
                    },
					sortOnLoad : true,
					sorters : {
						property : 'banco',
						direction : 'ASC'
					}
                });

                store.load({params: {start: 0, limit: 10}});    



                 var sm = new Ext.selection.CheckboxModel( {
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


    var listView = Ext.create('Ext.grid.Panel', {
        width:1000,
        height:275,
        collapsible:false,
        title: '',
        dockedItems: 
        [{
            xtype: 'toolbar',
            dock: 'top',
            align: '->',
            items: 
            [
                //tbfill -> alinea los items siguientes a la derecha
                { xtype: 'tbfill' }
            ]
        }],                    
        renderTo: Ext.get('lista_pagos'),
        // paging bar on the bottom
        bbar: Ext.create('Ext.PagingToolbar', {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando clientes {0} - {1} of {2}',
            emptyMsg: "No hay datos para mostrar"
        }),	
        store: store,
        multiSelect: false,
        viewConfig: 
        {
            emptyText: 'No hay datos para mostrar'
        },
        listeners:
        {
            itemdblclick: function( view, record, item, index, eventobj, obj )
            {
                var position = view.getPositionByEvent(eventobj),
                    data     = record.data,
                    value    = data[this.columns[position.column].dataIndex];
                    Ext.Msg.show(
                    {
                        title   : 'Copiar texto?',
                        msg     : "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                        buttons : Ext.Msg.OK,
                        icon    : Ext.MessageBox.INFO
                    });
            },
            viewready: function (grid) 
            {
                var view = grid.view;
                grid.mon(view, 
                {
                    uievent: function (type, view, cell, recordIndex, cellIndex, e) 
                    {
                        grid.cellIndex   = cellIndex;
                        grid.recordIndex = recordIndex;
                    }
                });

                grid.tip = Ext.create('Ext.tip.ToolTip', 
                {
                    target     : view.el,
                    delegate   : '.x-grid-cell',
                    trackMouse : true,
                    renderTo   : Ext.getBody(),
                    listeners  : 
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
        },
        columns: 
        [
            new Ext.grid.RowNumberer(),  
            {
                text: 'Banco',
                width: 150,
                dataIndex: 'banco'
            },
            {
                text: 'Numero Cta/Tarj',
                width: 100,
                dataIndex: 'numeroCuenta'
            },
            {
                text: 'Cliente',
                width: 115,
                dataIndex: 'cliente'
            },
            {
                text: 'Ced/Ruc',
                width: 90,
                dataIndex: 'identificacion'
            },
            {
                text: '$ Enviado',
                dataIndex: 'total',
                align: 'right',
                width: 60			
            },
            {
                text: '$ Procesado',
                dataIndex: 'debitado',
                align: 'right',
                width: 65			
            },
            {
                text: 'Fecha Creacion',
                dataIndex: 'fechaCreacion',
                align: 'right',
                flex: 85			
            },
            {
                text: 'Estado',
                dataIndex: 'estado',
                align: 'right',
                flex: 60
            },
            {
                text: 'Observacion Rechazo',
                dataIndex: 'observacionRechazo',
                align: 'right',
                flex: 110
            },
            {
                text: 'Referencia Debito',
                dataIndex: 'referencia',
                align: 'right',
                flex: 110
            }            
        ]
    });            

            
            function renderAcciones(value, p, record) {
                    var iconos='';
                    iconos=iconos+'<b><a href="'+record.data.linkVer+'" onClick="" title="Ver" class="button-grid-show"></a></b>';
                    if((record.data.tipo=='Anticipo') && (record.data.estado=='Pendiente'))
                        iconos=iconos+'<b><a href="#" onClick="" title="Cruzar Anticipo" class="button-grid-cruzar"></a></b>';
                    return Ext.String.format(
                                    iconos,
                        value
                    );
            }



    var filterPanel = Ext.create('Ext.panel.Panel', 
    {
        bodyPadding: 7,  // Don't want content to crunch against the borders
        border:false,
        buttonAlign: 'center',
        layout:{
            type:'table',
            columns: 5,
            align: 'left'
        },
        bodyStyle: {background: '#fff'},                     
        defaults: {bodyStyle: 'padding:10px'},
        collapsible : true,
        collapsed: true,
        width: 1000,
        title: 'Criterios de busqueda',
        buttons: 
        [
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
        items: 
        [
            {html:"&nbsp;",border:false,width:50},
            tipo_cuenta_cmb,
            {html:"&nbsp;",border:false,width:50},
            TFNumeroCuenta,
            {html:"&nbsp;",border:false,width:50},
            {html:"&nbsp;",border:false,width:50},
            estado_cmb,                               
            {html:"&nbsp;",border:false,width:50}
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
				store.load({params: {start: 0, limit: 10}});
			}
		}
		else
		{
                    store.load({params: {start: 0, limit: 10}});
		}	
	}
        
        function Limpiar(){   
            Ext.getCmp('fechaDesde').setValue('');
            Ext.getCmp('fechaHasta').setValue('');
            Ext.getCmp('idestado').setValue('');
        }


});
