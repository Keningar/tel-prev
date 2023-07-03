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
		width: 325,
		triggerAction: 'all',
		selectOnFocus:true,
		lastQuery: '',
		mode: 'local',
		allowBlank: true,	
					
		listeners: {
                    select:
                    function(e) {
                        //alert(Ext.getCmp('idestado').getValue());
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

                Ext.define('ListaDetalleModel', {
                    extend: 'Ext.data.Model',
                    fields: [{name:'id', type: 'int'},
                        {name:'tipo', type: 'string'},
                            {name:'numero', type: 'string'},
							{name:'cliente', type: 'string'},
                            {name:'punto', type: 'string'},
                            {name:'total', type: 'string'},
                            {name:'fechaCreacion', type: 'string'},
                            {name:'usuarioCreacion', type: 'string'},
                            {name:'estado', type: 'string'},
                            {name:'comentario', type: 'string'},
                            {name:'linkVer', type: 'string'}
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
                        extraParams:{fechaDesde:'',fechaHasta:'', estado:'',recaudacionId:''},
                        simpleSortMode: true
                    },
                    listeners: {
                        beforeload: function(store){
						store.getProxy().extraParams.fechaDesde= Ext.getCmp('fechaDesde').getValue();
						store.getProxy().extraParams.fechaHasta= Ext.getCmp('fechaHasta').getValue();   
                        store.getProxy().extraParams.estado= Ext.getCmp('idestado').getValue();
						store.getProxy().extraParams.recaudacionId=recaudacionId;	
                        },
                        load: function(store){
                            store.each(function(record) {
                                //idClienteSucursalSesion = record.data.idClienteSucursalSesion;
                            });
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
                    width:860,
                    height:275,
                    collapsible:false,
                    title: '',
                    //selModel: sm,
                    dockedItems: [ {
                                    xtype: 'toolbar',
                                    dock: 'top',
                                    align: '->',
                                    items: [
                                        //tbfill -> alinea los items siguientes a la derecha
                                        { xtype: 'tbfill' }
									]}],                    
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
                    viewConfig: {
                        emptyText: 'No hay datos para mostrar'
                    },
                    columns: [new Ext.grid.RowNumberer(),  				
                    {
                        text: 'Tipo',
                        width: 70,
                        dataIndex: 'tipo'
                    },                        
                            {
                        text: 'Numero',
                        width: 120,
                        dataIndex: 'numero'
                    },{
                        text: 'Cliente',
                        width: 115,
                        dataIndex: 'cliente'
                    },{
                        text: 'Punto',
                        width: 110,
                        dataIndex: 'punto'
                    },{
                        text: 'Total',
                        dataIndex: 'total',
                        align: 'right',
                        width: 40		
                    },{
                        text: 'Fecha Creacion',
                        dataIndex: 'fechaCreacion',
                        align: 'right',
                        flex: 85			
                    },{
                        text: 'Observacion',
                        dataIndex: 'comentario',
                        align: 'right',
                        flex: 100
                    },{
                        text: 'Estado',
                        dataIndex: 'estado',
                        align: 'right',
                        flex: 50
                    }/*,{
                        text: 'Acciones',
                        width: 40,
                        renderer: renderAcciones
                    }*/
                    
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



            var filterPanel = Ext.create('Ext.panel.Panel', {
                bodyPadding: 7,  // Don't want content to crunch against the borders
                //bodyBorder: false,
                border:false,
                //border: '1,1,0,1',
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
                    // applied to each contained panel
                    bodyStyle: 'padding:10px'
                },
                collapsible : true,
                collapsed: true,
                width: 860,
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
