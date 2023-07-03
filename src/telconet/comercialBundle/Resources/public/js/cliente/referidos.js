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
                    //anchor : '65%',
                    //layout: 'anchor'
            });
            DTFechaHasta = new Ext.form.DateField({
                    id: 'fechaHasta',
                    fieldLabel: 'Hasta',
                    labelAlign : 'left',
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    width:325
                    //anchor : '65%',
                    //layout: 'anchor'
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
		        url : url_cliente_lista_estados,
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

            TFNombre = new Ext.form.TextField({
                    id: 'nombre',
                    fieldLabel: 'Nombre',
                    xtype: 'textfield'
            });
            TFApellido = new Ext.form.TextField({
                    id: 'apellido',
                    fieldLabel: 'Apellido',
                    xtype: 'textfield'
            });			
            TFRazonSocial = new Ext.form.TextField({
                    id: 'razonSocial',
                    fieldLabel:'Razon Social',
                    xtype: 'textfield'
            });			
			
                Ext.define('ListaDetalleModel', {
                    extend: 'Ext.data.Model',
                    fields: [{name:'idPersona', type: 'int'},
                            {name:'Nombre', type: 'string'},
                            {name:'Direccion', type: 'string'},
                            {name:'fechaCreacion', type: 'string'},
                            {name:'usuarioCreacion', type: 'string'},
                            {name:'estado', type: 'string'},
                            {name:'linkVer', type: 'string'},
                            {name:'referido', type: 'string'},
                            {name:'pagos', type: 'string'},
                            {name:'valorDescuento', type: 'string'},
                            {name:'feEmisionFactura', type: 'string'},
                            {name:'numeroFactura', type: 'string'},
                            {name:'estadoFactura', type: 'string'}
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
                            root: 'clientes',
                            totalProperty: 'total'
                        },
                        extraParams:{fechaDesde:'',fechaHasta:'', estado:'',nombre:'',apellido:'',razonSocial:''},
                        simpleSortMode: true
                    },
                    listeners: {
                        beforeload: function(store){
								store.getProxy().extraParams.fechaDesde= Ext.getCmp('fechaDesde').getValue();
								store.getProxy().extraParams.fechaHasta= Ext.getCmp('fechaHasta').getValue();   
                                store.getProxy().extraParams.estado= Ext.getCmp('idestado').getValue();   
                                store.getProxy().extraParams.nombre= Ext.getCmp('nombre').getValue();   
								store.getProxy().extraParams.apellido= Ext.getCmp('apellido').getValue();   
								store.getProxy().extraParams.razonSocial= Ext.getCmp('razonSocial').getValue();   
                        },
                        load: function(store){
                            store.each(function(record) {
                                //idClienteSucursalSesion = record.data.idClienteSucursalSesion;
                            });
                        }
                    }
                });

                store.load({params: {start: 0, limit: 10}});    

                var listView = Ext.create('Ext.grid.Panel', {
                    width:950,
                    height:275,
                    collapsible:false,
                    title: '',
                    dockedItems: [ {
                                    xtype: 'toolbar',
                                    dock: 'top',
                                    align: '->',
                                    items: [
                                    //tbfill -> alinea los items siguientes a la derecha
                                    { xtype: 'tbfill' },
                                    {
                                        iconCls: 'icon_excel_green',
                                        text: 'Exportar',
                                        disabled: false,
                                        itemId: 'exportar',
                                        scope: this,
                                        handler: function(){exportar()}
                                    }
										]}],                    
                    renderTo: Ext.get('lista_clientes'),
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
                        text: 'Cliente',
                        width: 110,
                        dataIndex: 'Nombre'
                    },{
                        text: 'Nuevo Cliente',
                        width: 110,
                        dataIndex: 'referido'
                    },{
                        text: 'Pagos',
                        width: 40,
                        dataIndex: 'pagos'
                    },{
                        text: 'Direccion',
                        dataIndex: 'Direccion',
                        align: 'right',
                        width: 110,
                        renderer: function(value,metaData,record,colIndex,store,view) {
                            metaData.tdAttr = 'data-qtip="' + value+'"';
                            return value;
                        }			
                    },{
                        text: 'Fecha Creacion',
                        dataIndex: 'fechaCreacion',
                        align: 'right',
                        flex: 80,
                                    renderer: function(value,metaData,record,colIndex,store,view) {
                                    metaData.tdAttr = 'data-qtip="' + value+'"';
                                    return value;
                                    }			
                    },{
                        text: 'Descuento',
                        dataIndex: 'valorDescuento',
                        align: 'right',
                        flex: 50
                    },{
                        text: 'Factura',
                        dataIndex: 'numeroFactura',
                        align: 'right',
                        flex: 70
                    },{
                        text: 'Fecha Emision',
                        dataIndex: 'feEmisionFactura',
                        align: 'right',
                        flex: 70
                    },{
                        text: 'Estado Fact',
                        dataIndex: 'estadoFactura',
                        align: 'right',
                        flex: 50
                    },{
                        text: 'Estado Cliente',
                        dataIndex: 'estado',
                        align: 'right',
                        flex: 50
                    },{
                        text: 'Acciones',
                        width: 50,
                        renderer: renderAcciones
                    }
                    
                    ]
                });            

            
            function renderAcciones(value, p, record) {
                    var iconos='';
                    iconos=iconos+'<b><a href="'+record.data.linkVer+'" onClick="" title="Ver Referido" class="button-grid-show"></a></b>';
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
                    columns: 4,
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
                width: 950,
                title: 'Criterios de busqueda',
                
                    buttons: [
                        
                        {
                            text: 'Buscar',
                            //xtype: 'button',
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
                                DTFechaDesde,
                                {html:"&nbsp;",border:false,width:50},
                                DTFechaHasta,
                                {html:"&nbsp;",border:false,width:50},
                                TFNombre,
                                {html:"&nbsp;",border:false,width:50},
                                TFApellido,
                                {html:"&nbsp;",border:false,width:50},
                                TFRazonSocial,
                                {html:"&nbsp;",border:false,width:50},								
                                estado_cmb,                               
                                {html:"&nbsp;",border:false,width:50},
                                ],	
                renderTo: 'filtro_clientes'
            }); 
      

	function Buscar(){
		if  (( Ext.getCmp('fechaDesde').getValue()!=null)&&(Ext.getCmp('fechaHasta').getValue()!=null) )
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
            Ext.getCmp('nombre').setValue('');
            Ext.getCmp('apellido').setValue('');
            Ext.getCmp('razonSocial').setValue('');			
        }
        
        function exportar(){    
                                   //     console.log('fecha1:'+Ext.getCmp('fechaDesde').getRawValue());
                            //console.log('fecha2:'+Ext.getCmp('fechaHasta').getRawValue());
		if  (( Ext.getCmp('fechaDesde').getValue()!=null)&&(Ext.getCmp('fechaHasta').getValue()!=null) )
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

                            window.location.href=url_excel+'?fechaDesde='+Ext.getCmp('fechaDesde').getRawValue()+
                            '&fechaHasta='+Ext.getCmp('fechaHasta').getRawValue()+
                            '&nombre='+Ext.getCmp('nombre').getValue()+
                            '&apellido='+Ext.getCmp('apellido').getValue()+
                            '&razonSocial='+Ext.getCmp('razonSocial').getValue()+
                            '&estado='+Ext.getCmp('idestado').getValue();
			}
		}
		else
		{
                    window.location.href=url_excel+'?nombre='+Ext.getCmp('nombre').getValue()+
                    '&apellido='+Ext.getCmp('apellido').getValue()+
                    '&razonSocial='+Ext.getCmp('razonSocial').getValue()+
                    '&estado='+Ext.getCmp('idestado').getValue();
		}            

        }


});
