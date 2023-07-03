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


            function eliminarCliente(id){
                //alert (id);
                 Ext.Msg.confirm('Alerta','Se eliminara el registro. Desea continuar?', function(btn){
                 if(btn=='yes'){
                    Ext.Ajax.request({
                     url: url_cliente_delete_ajax,
                     params: { param : id},                     
                     method: 'get',                     
                     success: function(response){
                           var text = response.responseText;
                           Ext.Msg.alert(text);
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
                Ext.define('ListaDetalleModel', {
                    extend: 'Ext.data.Model',
                    fields: [{name:'idPersona', type: 'int'},
                            {name:'banco', type: 'string'},
                            {name:'tipoCuenta', type: 'string'},
                            {name:'fechaCreacion', type: 'string'},
                            {name:'usuarioCreacion', type: 'string'},
                            {name:'estado', type: 'string'},
                            {name:'linkVer', type: 'string'},
                            {name:'linkEditar', type: 'string'},
                            {name:'linkEliminar', type: 'string'}
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
                        extraParams:{fechaDesde:'',fechaHasta:'', estado:'',nombre:''},
                        simpleSortMode: true
                    },
                    listeners: {
                        beforeload: function(store){
				store.getProxy().extraParams.fechaDesde= Ext.getCmp('fechaDesde').getValue();
				store.getProxy().extraParams.fechaHasta= Ext.getCmp('fechaHasta').getValue();   
                                store.getProxy().extraParams.estado= Ext.getCmp('idestado').getValue();   
                                store.getProxy().extraParams.nombre= Ext.getCmp('nombre').getValue();   
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





            function eliminarAlgunos(){
                var param = '';
                if(sm.getSelection().length > 0)
                {
                  var estado = 0;
                  for(var i=0 ;  i < sm.getSelection().length ; ++i)
                  {
                    param = param + sm.getSelection()[i].data.idPersona;

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
                                url: url_cliente_delete_ajax,
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





                var listView = Ext.create('Ext.grid.Panel', {
                    width:800,
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
                                        { xtype: 'tbfill' },
                                        /*{
                                        iconCls: 'icon_add',
                                        text: 'Add',    
                                        scope: this,
                                        handler: function(){}
                                    },*/ {
                                        iconCls: 'icon_delete',
                                        text: '',
                                        disabled: false,
                                        itemId: 'delete',
                                        scope: this,
                                        handler: function(){ eliminarAlgunos();}
                                    }]}],                    
                    renderTo: Ext.get('lista_tiposcuenta'),
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
                        text: 'Banco',
                        width: 150,
                        dataIndex: 'banco'
                    },{
                        text: 'Tipo Cta/Tarjeta',
                        width: 115,
                        dataIndex: 'tipoCuenta'
                    },{
                        text: 'Fecha Creacion',
                        dataIndex: 'fechaCreacion',
                        align: 'right',
                        flex: 60,
                                    renderer: function(value,metaData,record,colIndex,store,view) {
                                    metaData.tdAttr = 'data-qtip="' + value+'"';
                                    return value;
                                    }			
                    },{
                        text: 'Estado',
                        dataIndex: 'estado',
                        align: 'right',
                        flex: 40
                    },{
                        text: 'Acciones',
                        width: 90,
                        renderer: renderAcciones
                    }
                    
                    ]
                });            

            
            function renderAcciones(value, p, record) {
                    var iconos='';
                    //var estadoIncidencia=true;
                    iconos=iconos+'<b><a href="'+record.data.linkVer+'" onClick="" title="Ver" class="button-grid-show"></a></b>';                    
                   /* if (record.data.estado != 'Inactivo'){
                        iconos=iconos+'<b><a href="'+record.data.linkEditar+'" onClick="" title="Editar"  class="button-grid-edit"></a></b>';
                        iconos=iconos+'<b><a href="#" onClick="eliminarCliente(\''+record.data.idPersona+'\')" title="Eliminar"  class="button-grid-delete"></a></b>';                        
                    }*/
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
                width: 800,
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
                                DTFechaDesde,
                                {html:"&nbsp;",border:false,width:50},
                                DTFechaHasta,
                                {html:"&nbsp;",border:false,width:50},
                                {html:"&nbsp;",border:false,width:50},
                                TFNombre,
                                {html:"&nbsp;",border:false,width:50},
                                estado_cmb,                               
                                {html:"&nbsp;",border:false,width:50},
                                ],	
                renderTo: 'filtro_tiposcuenta'
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
        }


});
