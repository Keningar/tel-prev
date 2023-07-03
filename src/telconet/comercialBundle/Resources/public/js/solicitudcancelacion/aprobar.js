Ext.require([
                '*',
                'Ext.tip.QuickTipManager',
                    'Ext.window.MessageBox'
            ]);

            var itemsPerPage = 10;
            var store='';
            var motivo_id='';
            var tipo_asignacion='';
            var pto_sucursal='';
            var idClienteSucursalSesion;

            Ext.onReady(function(){

            Ext.define('modelMotivo', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'idMotivo', type: 'string'},
                    {name: 'descripcion',  type: 'string'},
                    {name: 'idRelacionSistema',  type: 'string'}                 
                ]
            });

            var motivo_store = Ext.create('Ext.data.Store', {
		    autoLoad: false,
		    model: "modelMotivo",
		    proxy: {
		        type: 'ajax',
		        url : url_lista_motivos,
		        reader: {
		            type: 'json',
		            root: 'motivos'
                        }
                    }
            });	
            var motivo_cmb = new Ext.form.ComboBox({
                xtype: 'combobox',
                store: motivo_store,
                labelAlign : 'left',
                id:'idMotivo',
                name: 'idMotivo',
				valueField:'idMotivo',
                displayField:'descripcion',
                fieldLabel: 'Motivo Rechazo',
                labelAlign:'right',
				width: 400,
				triggerAction: 'all',
				selectOnFocus:true,
				lastQuery: '',
				mode: 'local',
				allowBlank: true,
				listeners: {
					select:
					function(e) {
						//alert(Ext.getCmp('idestado').getValue());
						motivo_id = Ext.getCmp('idMotivo').getValue();
						relacion_sistema_id=e.displayTplData[0].idRelacionSistema;
					},
					click: {
						element: 'el', //bind to the underlying el property on the panel
						fn: function(){ 
							motivo_id='';
							relacion_sistema_id='';
							motivo_store.removeAll();
							motivo_store.load();
						}
					}			
				}
            });                
                
                
                
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
            
            TFLogin = new Ext.form.TextField({
                    labelAlign : 'left', 
                    id: 'loginPto',
                    fieldLabel: 'Login',
                    xtype: 'textfield'
            });
                Ext.define('ListaDetalleModel', {
                    extend: 'Ext.data.Model',
                    fields: [{name:'id', type: 'string'},
                            {name:'cliente', type: 'string'},
			    {name:'servicio', type: 'string'},
                            {name:'login', type: 'string'},
                            {name:'motivo', type: 'string'},
                            {name:'descuento', type: 'string'},
                            {name:'observacion', type: 'string'},
                            {name:'feCreacion', type: 'string'},
                            {name:'usrCreacion', type: 'string'},
                            {name:'linkVer', type: 'string'}
                            ]
                }); 


                store = Ext.create('Ext.data.JsonStore', {
                    model: 'ListaDetalleModel',
                            pageSize: itemsPerPage,
                    proxy: {
                        type: 'ajax',
                        url: url_store,
                        reader: {
                            type: 'json',
                            root: 'solicitudes',
                            totalProperty: 'total'
                        },
                        extraParams:{fechaDesde:'',fechaHasta:''},
                        simpleSortMode: true
                    },
                    listeners: {
                        beforeload: function(store){
				store.getProxy().extraParams.fechaDesde= Ext.getCmp('fechaDesde').getValue();
				store.getProxy().extraParams.fechaHasta= Ext.getCmp('fechaHasta').getValue();
                                store.getProxy().extraParams.login= Ext.getCmp('loginPto').getValue();
                        },
                        load: function(store){
                            store.each(function(record) {
                                //idClienteSucursalSesion = record.data.idClienteSucursalSesion;
                            });
                        }
                    }
                });

                store.load({params: {start: 0, limit: 30}});    



                sm = new Ext.selection.CheckboxModel( {
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
                    width:1200,
                    height:275,
                    collapsible:false,
                    title: '',
                    selModel: sm,
                    dockedItems: [ {
                                    xtype: 'toolbar',
                                    dock: 'top',
                                    align: '->',
                                    items: [
                                        motivo_cmb,
                                        //tbfill -> alinea los items siguientes a la derecha
                                        { xtype: 'tbfill' },
					,  {
                                        iconCls: 'icon_delete',
                                        text: 'Anular',
                                        disabled: false,
                                        itemId: 'rechazar',
                                        scope: this,
                                        handler: function(){rechazarAlgunos()}
                                    }                                
                                ]}],                    
                    renderTo: Ext.get('lista_prospectos'),
                    // paging bar on the bottom
                    bbar: Ext.create('Ext.PagingToolbar', {
                        store: store,
                        displayInfo: true,
                        displayMsg: 'Mostrando solicitudes de cancelacion {0} - {1} of {2}',
                        emptyMsg: "No hay datos para mostrar"
                    }),	
                    store: store,
                    multiSelect: false,
                    viewConfig: {
                        emptyText: 'No hay datos para mostrar'
                    },
                    columns: [new Ext.grid.RowNumberer(),  
                            {
                        text: 'Cliente',
                        width: 170,
                        dataIndex: 'cliente'
                    },{
                        text: 'Login',
                        width: 100,
                        dataIndex: 'login'
                    },                        
                    {
                        text: 'Servicio',
                        width: 160,
                        dataIndex: 'servicio'
                    },{
                        text: 'Motivo',
                        width: 160,
                        dataIndex: 'motivo'
                    },{
                        text: 'Observacion Solicitud',
                        dataIndex: 'observacion',
                        align: 'right',
                        width: 200		
                    },{
                        text: 'Fecha Creacion',
                        dataIndex: 'feCreacion',
                        align: 'right',
                        width: 130			
                    },{
                        text: 'Usuario Creacion',
                        dataIndex: 'usrCreacion',
                        align: 'right',
                        flex: 50			
                    }]
                });            


            function renderAcciones(value, p, record) {
                    var iconos='';
                    var estadoIncidencia=true;
                    iconos=iconos+'<b><a href="'+record.data.linkVer+'" onClick="" title="Ver" class="button-grid-show"></a></b>';			
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
                    columns: 6,
                    align: 'left',
                },
                bodyStyle: {
                            background: '#fff'
                        },                     

                collapsible : true,
                collapsed: true,
                width: 1200,
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
                                TFLogin
                                ],	
                renderTo: 'filtro_prospectos'
            }); 
            
});

function Buscar(){

				store.load({params: {start: 0, limit: 10}});
	
	}


function rechazarAlgunos(){
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
          
        if (Ext.getCmp('idMotivo').getValue()){  
        Ext.Msg.confirm('Alerta','Se anularan los servicios seleccionados. Desea continuar?', function(btn){
            if(btn=='yes'){
                Ext.Ajax.request({
                    url: url_rechazar,
                    method: 'post',
                    params: { param : param, motivoId:motivo_id },
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
        alert('Debe seleccionar un motivo para poder anular la(s) solicitud(es) de cancelacion de servicios.');                
       } 
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


function limpiar(){
    Ext.getCmp('fechaDesde').setRawValue("");
    Ext.getCmp('fechaHasta').setRawValue("");  
    Ext.getCmp('loginPto').setRawValue("");  
}
