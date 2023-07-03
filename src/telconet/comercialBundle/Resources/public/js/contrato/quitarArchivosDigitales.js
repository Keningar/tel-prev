  Ext.require([
                '*',
                'Ext.tip.QuickTipManager',
                    'Ext.window.MessageBox'
            ]);

            var itemsPerPage = 10;
            var store='';            

            Ext.onReady(function(){                         
			Ext.define('ListaDetalleModel', {
				extend: 'Ext.data.Model',                                                                				                                    
                                fields: [{name:'id', type: 'string'},                                      
                                        {name:'ubicacionLogicaDocumento', type: 'string'},
                                        {name:'tipoDocumentoGeneral', type: 'string'},
                                        {name:'feCreacion', type: 'string'},
                                        {name:'usrCreacion', type: 'string'},
                                        {name:'linkVerDocumento', type: 'string'},                   
                                        {name:'linkEliminarDocumento', type: 'string'}                   
					]
			}); 


			store = Ext.create('Ext.data.JsonStore', {
				model: 'ListaDetalleModel',
						pageSize: itemsPerPage,
				proxy: {
					type: 'ajax',
					url: 'showDocumentosContrato',
					reader: {
						type: 'json',
						root: 'logs',
						totalProperty: 'total'
					},					
					simpleSortMode: true
				},
				listeners: {					
					load: function(store){
						store.each(function(record) {							
						});
					}
				}
			});

			store.load();    

			sm = new Ext.selection.CheckboxModel( {
				listeners:{
				selectionchange: function(selectionModel, selected, options){
					arregloSeleccionados= new Array();
					Ext.each(selected, function(record){						
					});							
				}
				}
			});


			var listView = Ext.create('Ext.grid.Panel', {
				width:1000,
				height:275,
				collapsible:false,
				title: '',
				selModel: sm,
				dockedItems: [ {
								xtype: 'toolbar',
								dock: 'top',
								align: '->',
								items: [
									//tbfill -> alinea los items siguientes a la derecha
									{ xtype: 'tbfill' }, {
									iconCls: 'icon_delete',
									text: 'Eliminar',
									disabled: false,
									itemId: 'delete',
									scope: this,
									handler: function(){eliminarAlgunos();}
								}]}],                    
				renderTo: Ext.get('listado'),
				// paging bar on the bottom
				bbar: Ext.create('Ext.PagingToolbar', {
					store: store,
					displayInfo: true,
					displayMsg: 'Mostrando planes {0} - {1} of {2}',
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
					text: 'Id',
					width: 80,
					dataIndex: 'id'
				},{
					text: 'Archivo Digital',
					width: 300,
					dataIndex: 'ubicacionLogicaDocumento'
				},{
					text: 'Tipo Documento',
					dataIndex: 'tipoDocumentoGeneral',
					width: 150			
				},{
					text: 'Fecha de Creacion',
					dataIndex: 'feCreacion',
					flex: 160,
				},{
					text: 'Creado por',
					dataIndex: 'usrCreacion',
					flex: 80
				},{
					text: 'Acciones',
					width: 80,
					renderer: renderAcciones,
				}]
			});            


           function renderAcciones(value, p, record) {
                    var iconos='';
                    iconos=iconos+'<b><a href="'+record.data.linkVerDocumento+'" onClick="" title="Ver Archivo Digital" class="button-grid-show"></a></b>';	                    
                    iconos=iconos+'<b><a href="#" onClick="eliminar(\''+record.data.linkEliminarDocumento+'\')" title="Eliminar Archivo Digital" class="button-grid-delete"></a></b>';	                    
                    return Ext.String.format(
                                    iconos,
                        value,
                        '1',
                                    'nada'
                    );
        }            
	});
	

	function eliminar(direccion)
	{		
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

    function eliminarAlgunos()
    {
		var param = '';
		if(sm.getSelection().length > 0)
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
		  if(estado == 0)
		  {
			Ext.Msg.confirm('Alerta','Se eliminaran los registros. Desea continuar?', function(btn){
				if(btn=='yes'){
					Ext.Ajax.request({
						url: url_eliminar,
						method: 'post',
						params: { param : param},
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
			alert('Por lo menos uno de las registro se encuentra en estado ELIMINADO');
		  }
		}
		else
		{
		  alert('Seleccione por lo menos un registro de la lista');
		}
	}
	
	
