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

            //CREAMOS DATA STORE PARA EMPLEADOS
            Ext.define('modelPadres', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'idpadre', type: 'string'},
                    {name: 'login',  type: 'string'}                
                ]
            });			
            var padres_store = Ext.create('Ext.data.Store', {
		    autoLoad: false,
		    model: "modelPadres",
		    proxy: {
		        type: 'ajax',
		        url : url_padres,
		        reader: {
		            type: 'json',
		            root: 'padres'
                        }
                    }
            });	
            var padres_cmb = new Ext.form.ComboBox({
                xtype: 'combobox',
                store: padres_store,
                labelAlign : 'left',
                id:'idpadre',
                name: 'idpadre',
		valueField:'idpadre',
                displayField:'login',
                fieldLabel: 'Pto. Facturacion',
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
                        //estado_id = Ext.getCmp('idestado').getValue();
                    },
                    click: {
                        element: 'el', //bind to the underlying el property on the panel
                        fn: function(){ 
                            //estado_id='';
                            padres_store.removeAll();
                            padres_store.load();
                        }
                    }			
		}
            });


            TFNombre = new Ext.form.TextField({
                    id: 'nombre',
                    fieldLabel: 'Login',
                    xtype: 'textfield'
            });


            Ext.define('serviciosModel', {
                extend: 'Ext.data.Model',
                idProperty: 'idServicio',
                fields: [
                    {name: 'idServicio', type: 'string'},
                    {name: 'idPunto', type: 'string'},
                    {name: 'descripcionPunto', type: 'string'},
                    {name: 'loginPunto', type: 'string'},
                    {name: 'idProducto', type: 'string'},
                    {name: 'descripcionProducto', type: 'string'},
                    {name: 'cantidad', type: 'string'},
                    {name: 'precioVenta', type: 'float'},
                    {name: 'estado', type: 'string'},
                    {name: 'fechaCreacion', type: 'string'},
                    {name: 'padre', type: 'string'},
                    {name: 'loginPadre', type: 'string'}
                ]
            });


            var store = Ext.create('Ext.data.JsonStore', {
                model: 'serviciosModel',
                pageSize: 30,
                proxy: {
                    type: 'ajax',
                    url: url_servicios,
                    reader: {
                        type: 'json',
                        root: 'servicios'
                    }
                },
                listeners: {
                    beforeload: function(store){  
                            store.getProxy().extraParams.nombre= Ext.getCmp('nombre').getValue();   
                    },
                    load: function(store){
                        store.each(function(record) {
                            //idClienteSucursalSesion = record.data.idClienteSucursalSesion;
                        });
                    }
                }
            });

            store.load({params: {start: 0, limit: 30}});    



                 var sm = new Ext.selection.CheckboxModel( {
                    listeners:{
                       select: function( selectionModel, record, index, eOpts ){
                           //console.log('selected:'+index);
                           if(record.data.padre){
                                sm.deselect(index);
                                Ext.Msg.alert('Alerta','Este Punto ya fue asignado a un Padre');
                            }
                            
                       
                       }
                    }
                });





            function asignarVarios(){
                var param = '';
                if(sm.getSelection().length > 0)
                {
                  var estado = 0;
                  for(var i=0 ;  i < sm.getSelection().length ; ++i)
                  {
                    param = param + sm.getSelection()[i].data.idServicio;

                    if(sm.getSelection()[i].data.padre)
                    {
                      estado = estado + 1;
                    }
                    if(i < (sm.getSelection().length -1))
                    {
                      param = param + '|';
                    }
                  }      
                  if((estado == 0)&&padres_cmb.getValue())
                  {
                    Ext.Msg.confirm('Alerta','El punto de facturacion seleccionado se asignara a los servicios seleccionados. Desea continuar?', function(btn){
                        if(btn=='yes'){
                            Ext.Ajax.request({
                                url: url_asignar_ajax,
                                method: 'post',
                                params: { param : param, padre:padres_cmb.getValue()},
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
                    Ext.Msg.alert('Error','Por lo menos uno de los registro ya tienen padre o falta escoger el punto padre.');
                  }
                }
                else
                {
                  alert('Seleccione por lo menos un registro de la lista');
                }
            }

                var listView = Ext.create('Ext.grid.Panel', {
                    width:850,
                    height:400,
                    collapsible:false,
                    title: 'Listado de Servicios',
                    selModel: sm,
                    dockedItems: [ {
                        xtype: 'toolbar',
                        dock: 'top',
                        align: '->',
                        items: [padres_cmb,
                            //tbfill -> alinea los items siguientes a la derecha
                            { xtype: 'tbfill' },
                            {
                            iconCls: 'icon_add',
                            text: 'Asignar',
                            disabled: false,
                            itemId: 'asignarpadre',
                            scope: this,
                            handler: function(){ asignarVarios();}
                            }]
                    }],                    
                    renderTo: Ext.get('lista_servicios'),
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
                    viewConfig: {
                          getRowClass: function(record, index) {
                              var c = record.get('padre');
                              //console.log(c);
                              if (c) {
                                  return 'redTextGrid';
                              } else{
                                  return 'blackTextGrid';
                              }
                          }
                    },                    
                    columns: [new Ext.grid.RowNumberer(),  
                    {
                        text: 'Punto',
                        width: 150,
                        dataIndex: 'loginPunto'
                    },{
                        text: 'Servicio',
                        dataIndex: 'descripcionProducto',
                        align: 'right',
                        width: 200			
                    },{
                        text: 'Precio Venta',
                        dataIndex: 'precioVenta',
                        align: 'right',
                        width: 80			
                    },{
                        text: 'Padre',
                        dataIndex: 'loginPadre',
                        align: 'right',
                        width: 150			
                    },{
                        text: 'Estado',
                        dataIndex: 'estado',
                        align: 'right',
                        flex: 100
                    },{
                        text: 'Acciones',
                        width: 130,
                        renderer: renderAcciones
                    }
                    
                    ]
                });            

            
            function renderAcciones(value, p, record) {
                    var iconos='';
                    iconos=iconos+'<b><a href="'+record.data.linkVer+'" onClick="" title="Ver" class="button-grid-show"></a></b>';
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
                width: 850,
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
                                {html:"&nbsp;",border:false,width:50},
                                {html:"&nbsp;",border:false,width:50},
                                TFNombre,
                                {html:"&nbsp;",border:false,width:50}
                                ],	
                renderTo: 'filtro_servicios'
            }); 
      

	function Buscar(){

		store.load({params: {start: 0, limit: 30}});
			
			
	}
        
        function Limpiar(){
            
            Ext.getCmp('fechaDesde').setValue('');
            Ext.getCmp('fechaHasta').setValue('');
            Ext.getCmp('idestado').setValue('');
            Ext.getCmp('nombre').setValue('');
        }


});