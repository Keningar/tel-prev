            Ext.require([
                '*',
                'Ext.tip.QuickTipManager',
                    'Ext.window.MessageBox'
            ]);

            var itemsPerPage = 999999;
            var store='';
            var motivo_id='';
            var relacion_sistema_id='';
            var tipo_solicitud_id='';
            var area_id='';
            var login_id='';
            var tipo_asignacion='';
            var pto_sucursal='';
            var idClienteSucursalSesion;

            Ext.onReady(function(){

	Ext.form.VTypes["valorVtypeVal"] =/(^\d{1,4}\.\d{1,2}$)|(^\d{1,4}$)/;		
	Ext.form.VTypes["valorVtype"]=function(v){
		return Ext.form.VTypes["valorVtypeVal"].test(v);
	}
	Ext.form.VTypes["valorVtypeText"]="Puede ingresar hasta 4 enteros y al menos 1 decimal o puede ingresar hasta 4 enteros sin decimales";
	Ext.form.VTypes["valorVtypeMask"]=/[\d\.]/;

	Ext.form.VTypes["porcentajeVtypeVal"] =/(^\d{1,3}\.\d{1,2}$)|(^\d{1,3}$)/;		
	Ext.form.VTypes["porcentajeVtype"]=function(v){
		return Ext.form.VTypes["porcentajeVtypeVal"].test(v);
	}
	Ext.form.VTypes["porcentajeVtypeText"]="Puede ingresar hasta 3 enteros y al menos 1 decimal o puede ingresar hasta 3 enteros sin decimales";
	Ext.form.VTypes["porcentajeVtypeMask"]=/[\d\.]/;
            function solicitarCancelacion(){
                var param = '';
                if(sm.getSelection().length > 0)
                {
                  var estado = 0;
                  for(var i=0 ;  i < sm.getSelection().length ; ++i)
                  {
                    param = param + sm.getSelection()[i].data.idServicio;
                    if(i < (sm.getSelection().length -1))
                    {
                      param = param + '|';
                    }
                  }      
                  if(motivo_id)
                  {
  
                        if(Ext.getCmp('fechaCancelacion').getValue() ){
                            
                            if(TFObservacion.getValue()){
                                ejecutaEnvioSolicitud(param);
                            }
                            else
                                Ext.Msg.alert('Alerta ','Por favor ingresar la observacion');
                            
                        }    
                        else
                            Ext.Msg.alert('Alerta ','Por favor ingresar fecha de suspension');


                  }
                  else
                  {
                    alert('Seleccione el Motivo de la solicitud');
                  }
                }
                else
                {
                  alert('Seleccione por lo menos un registro de la lista');
                }
            }            
 
            function ejecutaEnvioSolicitud(param){
   
                        Ext.Msg.confirm('Alerta','Se solicitara suspension temporal para los registros seleccionados. Desea continuar?', function(btn){
                            if(btn=='yes'){
                                //alert(motivo_id);
                                Ext.Ajax.request({
                                    url: url_solicitar_cancelacion_ajax,
                                    method: 'post',
                                    params: { param : param, motivoId:motivo_id, rs: relacion_sistema_id, ts:tipo_solicitud_id, fecha:DTFechaCancelacion.getValue(), obs:TFObservacion.getValue()},
                                    success: function(response){
                                        var text = response.responseText;
                                        Ext.Msg.alert('Ok ',response.responseText);
                                        store.load();
                                    },
                                    failure: function(response)
                                    {
                                        Ext.Msg.alert('Error ','Error: ' + response.responseText);
                                    }
                                });
                            }
                        });                
                
            }

            //CREAMOS DATA STORE PARA EMPLEADOS
            Ext.define('modelMotivo', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'idMotivo', type: 'string'},
                    {name: 'descripcion',  type: 'string'},
                    {name: 'idRelacionSistema',  type: 'string'},
                    {name: 'idTipoSolicitud',  type: 'string'}                    
                ]
            });	
            var ajaxProxyCancelacion = new Ext.data.proxy.Ajax({
		        type: 'ajax',
		        url : url_lista_motivos,
		        reader: {
		            type: 'json',
		            root: 'motivos'
                        }
            });           
            motivo_store = Ext.create('Ext.data.Store', {
		    autoLoad: false,
		    model: "modelMotivo",
		    proxy: ajaxProxyCancelacion
            });	
            var motivo_cmb = new Ext.form.ComboBox({
                xtype: 'combobox',
                store: motivo_store,
                labelAlign : 'left',
                id:'idMotivo',
                name: 'idMotivo',
		valueField:'idMotivo',
                displayField:'descripcion',
                fieldLabel: 'Motivo',
                labelAlign:'right',
		width: 280,
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
                        tipo_solicitud_id=e.displayTplData[0].idTipoSolicitud;
                    },
                    click: {
                        element: 'el', //bind to the underlying el property on the panel
                        fn: function(){ 
                            motivo_id='';
                            relacion_sistema_id='';
                            tipo_solicitud_id='';
                            motivo_store.removeAll();
                            motivo_store.load();
                        }
                    }			
		}
            });


            
            DTFechaCancelacion = new Ext.form.DateField({
                    id: 'fechaCancelacion',
                    fieldLabel: 'Fecha Suspension',
                    labelAlign : 'left',
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    width:250
                    //anchor : '65%',
                    //layout: 'anchor'
            });

           
            TFObservacion = new Ext.form.field.TextArea({
                    xtype     : 'textareafield',
                    //grow      : true,
                    name      : 'observacion',
                    fieldLabel: 'Observacion',
                    //width    : '400px',
                    cols     : 80,
                    rows     : 2,
                    maxLength: 200
                }); 

            
                Ext.define('ListaDetalleModel', {
                    extend: 'Ext.data.Model',
                    fields: [{name:'idServicio', type: 'int'},
                            {name:'tipo', type: 'string'},
                            {name:'idPunto', type: 'string'},
                            {name:'descripcionPunto', type: 'string'},
                            {name:'idProducto', type: 'string'},
                            {name:'descripcionProducto', type: 'string'},
                            {name:'cantidad', type: 'string'},
                            {name:'fechaCreacion', type: 'string'},
                            {name:'precioVenta', type: 'string'},                            
                            {name:'estado', type: 'string'},
                            {name:'yaFueSolicitada', type: 'string'}
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
                            root: 'servicios',
                            totalProperty: 'total'
                        },
                        extraParams:{fechaDesde:'',fechaHasta:'', estado:'',nombre:''},
                        simpleSortMode: true
                    },
                    listeners: {
                        beforeload: function(store,id_punto_cliente){
                                store.getProxy().extraParams.idPuntoCliente= id_punto_cliente;
                        },
                        load: function(store){
                            store.each(function(record) {
                                //idClienteSucursalSesion = record.data.idClienteSucursalSesion;
                            });
                        }
                    }
                });

                store.load({params: {start: 0, limit: 999999}});    

                 var sm = new Ext.selection.CheckboxModel({
                    listeners:{
                       select: function( selectionModel, record, index, eOpts ){
                           //console.log('selected:'+index);
                           if(record.data.yaFueSolicitada == 'S'){
                                sm.deselect(index);
                                Ext.Msg.alert('Alerta','Ya fue solicitada cancelacion para el servicio: '+record.data.descripcionProducto);
                            }
                            
                       } 
                    }
                });


                var opcionesPanel = Ext.create('Ext.panel.Panel', {
                    bodyPadding: 0,
                    border:true,
                    buttonAlign: 'center',
                    bodyStyle: {
                                background: '#fff'
                    },                     
                    defaults: {
                        bodyStyle: 'padding:10px'
                    },
                    //collapsible : true,
                    //collapsed: true,
                    width: 900,
                    title: 'Opciones',
                    items: [
{
                                    xtype: 'toolbar',
                                    dock: 'top',
                                    align: '->',
                                    items: [
                                        //tbfill -> alinea los items siguientes a la derecha,
                                        DTFechaCancelacion,
                                        { xtype: 'tbfill' },
                                        motivo_cmb,
                                        {
                                        iconCls: 'icon_solicitud',
                                        text: 'solicitar',
                                        disabled: false,
                                        itemId: 'delete',
                                        scope: this,
                                        handler: function(){ solicitarCancelacion();}
                                        }
                                        
                                  ]}
                    ],	
                    renderTo: 'filtro_servicios'
                });
                var observacionPanel = Ext.create('Ext.panel.Panel', {
                    bodyPadding: 7,
                    border:true,
                    //buttonAlign: 'center',
                    bodyStyle: {
                                background: '#fff'
                    },                     
                    defaults: {
                        bodyStyle: 'padding:10px'
                    },
                    //collapsible : true,
                    //collapsed: true,
                    width: 900,
                    title: '',
                    items: [
                      TFObservacion
                    ],	
                    renderTo: 'panel_observacion'
                });


                var listView = Ext.create('Ext.grid.Panel', {
                    width:900,
                    height:350,
                    collapsible:false,
                    title: 'Servicios del punto cliente',
                    selModel: sm,                    
                    renderTo: Ext.get('lista_servicios'),
                    // paging bar on the bottom
                    bbar: Ext.create('Ext.PagingToolbar', {
                        store: store,
                        displayInfo: true,
                        displayMsg: 'Mostrando servicios {0} - {1} of {2}',
                        emptyMsg: "No hay datos para mostrar"
                    }),	
                    store: store,
                    multiSelect: false,
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
                    viewConfig: {
                          getRowClass: function(record, index) {
                              var c = record.get('yaFueSolicitada');
                              //console.log(c);
                              if (c == 'S') {
                                  return 'grisTextGrid';
                              } else{
                                  return 'blackTextGrid';
                              }
                          },
                          emptyText: 'No hay datos para mostrar'
                    } ,
                    columns: [new Ext.grid.RowNumberer(),  
                            {
                        text: 'Descripcion',
                        width: 150,
                        dataIndex: 'descripcionProducto'
                    },{
                        text: 'Cantidad',
                        width: 115,
                        dataIndex: 'cantidad'
                    },{
                        text: 'Precio Venta',
                        dataIndex: 'precioVenta',
                        align: 'right',
                        width: 135			
                    },{
                        text: 'Fecha Creacion',
                        dataIndex: 'fechaCreacion',
                        align: 'right',
                        flex: 60			
                    },{
                        text: 'Estado',
                        dataIndex: 'estado',
                        align: 'right',
                        flex: 40
                    }
                    
                    ]
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
				store.load({params: {start: 0, limit: 999999}});
			}
		}
		else
		{

			   Ext.Msg.show({
			   title:'Error en Busqueda',
			   msg: 'Por Favor Ingrese criterios de fecha.',
			   buttons: Ext.Msg.OK,
			   animEl: 'elId',
			   icon: Ext.MessageBox.ERROR
				});
		}	
	}
        
        function Limpiar(){
            
            Ext.getCmp('fechaDesde').setValue('');
            Ext.getCmp('fechaHasta').setValue('');
            Ext.getCmp('idestado').setValue('');
            Ext.getCmp('nombre').setValue('');
        }


});
