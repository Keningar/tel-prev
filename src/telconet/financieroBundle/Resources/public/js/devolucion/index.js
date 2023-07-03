/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
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
                    width:325,
                    //anchor : '65%',
                    //layout: 'anchor'
            });
            DTFechaHasta = new Ext.form.DateField({
                    id: 'fechaHasta',
                    fieldLabel: 'Hasta',
                    labelAlign : 'left',
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    width:325,
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
		        url : url_store_estados,
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
				allowBlank: false,	
					
				listeners: {
							select:
							function(e) {
								estado_id = Ext.getCmp('idestado').getValue();
							},
							click: {
								element: 'el', //bind to the underlying el property on the panel
								fn: function(){ 
									estado_store.removeAll();
									estado_store.load();
								}
							}			
				}
            });


			Ext.define('ListaDetalleModel', {
				extend: 'Ext.data.Model',
				fields: [{name:'Numerofacturasri', type: 'string'},
						{name:'Punto', type: 'string'},
						{name:'Cliente', type: 'string'},
						{name:'Esautomatica', type: 'string'},
						{name:'Fecreacion', type: 'string'},
						{name:'Estado', type: 'string'},
						{name:'linkVer', type: 'string'},
						{name:'linkEliminar', type: 'string'},
						{name:'id', type: 'int'}
						]
			}); 


			store = Ext.create('Ext.data.JsonStore', {
				model: 'ListaDetalleModel',
						pageSize: itemsPerPage,
				proxy: {
					type: 'ajax',
					url: url_store_grid,
					reader: {
						type: 'json',
						root: 'documentos',
						totalProperty: 'total'
					},
					extraParams:{fechaDesde:'',fechaHasta:'', estado:''},
					simpleSortMode: true
				},
				listeners: {
					beforeload: function(store){
						store.getProxy().extraParams.fechaDesde= Ext.getCmp('fechaDesde').getValue();
						store.getProxy().extraParams.fechaHasta= Ext.getCmp('fechaHasta').getValue();   
						store.getProxy().extraParams.estado= Ext.getCmp('idestado').getValue();
					},
					load: function(store){
						store.each(function(record) {
							//idClienteSucursalSesion = record.data.idClienteSucursalSesion;
						});
					}
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
				width:800,
				height:275,
				collapsible:false,
				title: '',
				selModel: sm,
				dockedItems: [{
					xtype: 'toolbar',
					dock: 'top',
					align: '->',
					items: [
						//tbfill -> alinea los items siguientes a la derecha
						{ xtype: 'tbfill' },
					  {
						iconCls: 'icon_delete',
						text: 'Eliminar',
						disabled: false,
						itemId: 'delete',
						scope: this,
						handler: function(){ eliminarAlgunos(); }
				}]}],                    
				renderTo: Ext.get('listar'),
				// paging bar on the bottom
				bbar: Ext.create('Ext.PagingToolbar', {
					store: store,
					displayInfo: true,
					displayMsg: 'Mostrando prospectos {0} - {1} of {2}',
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
					text: 'Id Factura',
					width: 130,
					dataIndex: 'id',
					hidden:true
				},{
					text: 'No. Devolucion',
					width: 130,
					dataIndex: 'Numerofacturasri'
				},{
					text: 'Pto cliente',
					width: 130,
					dataIndex: 'Punto'
				},{
					text: 'Cliente',
					width: 130,
					dataIndex: 'Cliente'
				},{
					text: 'Es automatica',
					dataIndex: 'Esautomatica',
					align: 'right',
					width: 80			
				},{
					text: 'Estado',
					dataIndex: 'Estado',
					align: 'right',
					width: 70			
				},{
					text: 'F. Creacion',
					dataIndex: 'Fecreacion',
					align: 'right',
					width: 100			
				},{
					text: 'Acciones',
					width: 100,
					renderer: renderAcciones,
				}]
			});            


            function renderAcciones(value, p, record) {
                    var iconos='';
                    var estadoIncidencia=true;
                    iconos=iconos+'<b><a href="'+record.data.linkVer+'" onClick="" title="Ver" class="button-grid-show"></a></b>';			
                    if(record.data.Estado!='Activo')
						iconos=iconos+'<b><a href="#" onClick="showProcesar('+record.data.id+')" title="Anular" class="button-grid-delete"></a></b>';
					else
						iconos=iconos+'<b></b>';
						
                    return Ext.String.format(
						iconos,
                        value,
                        '1',
						'nada'
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
                    align: 'left',
                },
                bodyStyle: {
					background: '#fff'
				},                     
                collapsible : true,
                collapsed: true,
                width: 800,
                title: 'Criterios de busqueda',
				buttons: [
					{
						text: 'Buscar',
						//xtype: 'button',
						iconCls: "icon_search",
						handler: Buscar,
					},
					{
						text: 'Limpiar',
						iconCls: "icon_limpiar",
						handler: function(){ limpiar();}
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
						{html:"&nbsp;",border:false,width:325},
						{html:"&nbsp;",border:false,width:50}
				],	
                renderTo: 'filtro'
            }); 
            
			Ext.define('modelMotivos', {
				extend: 'Ext.data.Model',
				fields: [
					{name: 'id', type: 'string'},
					{name: 'descripcion',  type: 'string'}                    
				]
			});	
    
			store_motivos = Ext.create('Ext.data.Store', {
				autoLoad: false,
				model: "modelMotivos",
				proxy: {
					type: 'ajax',
					url : url_listar_motivos,
					reader: {
						type: 'json',
						root: 'documentos'
							}
						}
			});	

            
		});

		function Buscar()
		{
			store.load({params: {start: 0, limit: 10}});
		}

		function eliminar(direccion)
		{
			//alert(direccion);
			Ext.Msg.confirm('Alerta','Se eliminara el registro. Desea continuar?', function(btn){
				if(btn=='yes'){
					Ext.Ajax.request({
						url: direccion,
						method: 'post',
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

		function limpiar(){
			Ext.getCmp('fechaDesde').setRawValue("");
			Ext.getCmp('fechaHasta').setRawValue("");
			Ext.getCmp('idestado').setRawValue("");
		}
