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
              
            var objCliente  = new Cliente();  
            var objClienteA = new Cliente(); 
            var objClienteB = new Cliente();  

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

            var mes_store = Ext.create('Ext.data.Store', {
                fields: ['valor', 'signo'],
                data : [
                    {"valor":"01", "signo":"01"},
                    {"valor":"02", "signo":"02"},
                    {"valor":"03", "signo":"03"},  
                    {"valor":"04", "signo":"04"},
                    {"valor":"05", "signo":"05"},
                    {"valor":"06", "signo":"06"},
                    {"valor":"07", "signo":"07"},
                    {"valor":"08", "signo":"08"},
                    {"valor":"09", "signo":"09"},
                    {"valor":"10", "signo":"10"},
                    {"valor":"11", "signo":"11"},
                    {"valor":"12", "signo":"12"}
                    //...
                ]
            });

            var mes_cmb = new Ext.form.ComboBox({
                xtype: 'combobox',
                labelAlign : 'left', 
                store: mes_store,
                id:'idmes',
                name: 'idmes',
		valueField:'valor',
                displayField:'signo',
                fieldLabel: 'Mes',
		width: 160,
		mode: 'local',
		allowBlank: true,
    listeners : {
    render : function(combobox) {
        /// code to convert GMT String to date object
        combobox.setValue((new Date).getMonth()+1);
                }
        }                
            });

                
            var anio_store = Ext.create('Ext.data.Store', {
                fields: ['valor', 'signo'],
                data : [
                    {"valor":"1998", "signo":"1998"},
                    {"valor":"1999", "signo":"1999"},
                    {"valor":"2000", "signo":"2000"},  
                    {"valor":"2001", "signo":"2001"},
                    {"valor":"2002", "signo":"2002"},
                    {"valor":"2003", "signo":"2003"},
                    {"valor":"2004", "signo":"2004"},
                    {"valor":"2005", "signo":"2005"},
                    {"valor":"2006", "signo":"2006"},
                    {"valor":"2007", "signo":"2007"},
                    {"valor":"2008", "signo":"2008"},
                    {"valor":"2009", "signo":"2009"},
                    {"valor":"2010", "signo":"2010"},
                    {"valor":"2011", "signo":"2011"},
                    {"valor":"2012", "signo":"2012"},
                    {"valor":"2013", "signo":"2013"},
                    {"valor":"2014", "signo":"2014"},
                    {"valor":"2015", "signo":"2015"},
                    {"valor":"2016", "signo":"2016"},
                    {"valor":"2017", "signo":"2017"},
                    {"valor":"2018", "signo":"2018"},
                    {"valor":"2019", "signo":"2019"},
                    {"valor":"2020", "signo":"2020"}
                    //...
                ]
            });

            var anio_cmb = new Ext.form.ComboBox({
                xtype: 'combobox',
                labelAlign : 'left', 
                store: anio_store,
                id:'idanio',
                name: 'idanio',
                valueField:'valor',
                displayField:'signo',
                fieldLabel: 'A\u00f1o',
                width: 160,
                mode: 'local',
                allowBlank: true,
    listeners : {
    render : function(combobox) {
        /// code to convert GMT String to date object
        combobox.setValue((new Date).getFullYear());
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
            var objScope = 
                {
                  extraParams: 
                  {
                      strAppendDatos: 'Todos',
                      strDisponiblesPersona: ''
                  }
                };

             
            var objStoreOficinasVendedor        = objCliente.objStoreOficinas(objScope);
            objStoreOficinasVendedor.objStore   = objStoreOficinasVendedor;
            objStoreOficinasVendedor.strIdObj   = 'cbxIdOficina';
            objStoreOficinasVendedor.intWidth   = 450;

            var cbxOficinas     = objCliente.objComboMultiSelectDatos(objStoreOficinasVendedor,'Oficina Vendedor');
            cbxOficinas.colspan = 3;
            cbxOficinas.setWidth(450);
            
            
            var objStoreOficinasPtoCobertura        = objClienteA.objStoreOficinas(objScope);
            objStoreOficinasPtoCobertura.objStore   = objStoreOficinasPtoCobertura;
            objStoreOficinasPtoCobertura.strIdObj   = 'cbxIdOficinaPtoCobertura';
            objStoreOficinasPtoCobertura.intWidth   = 450;

            var cbxOficinasPtoCobertura     = objClienteA.objComboMultiSelectDatos(objStoreOficinasPtoCobertura,'Oficina Pto:Cobertura');
            cbxOficinasPtoCobertura.colspan = 3;
            cbxOficinasPtoCobertura.setWidth(450);     
            
            
            var objStorePlanes        = objClienteB.objStorePlanes(objScope);
            objStorePlanes.objStore   = objStorePlanes;
            objStorePlanes.strIdObj   = 'cbxIdPlan';
            objStorePlanes.intWidth   = 450;

            var cbxPlanes     = objClienteB.objComboMultiSelectDatos(objStorePlanes,'Plan Tarifario');
            cbxPlanes.colspan = 3;
            cbxPlanes.setWidth(450);            
            
    
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
            
            TFIdentificacion = new Ext.form.TextField({
                id: 'identificacion',
                fieldLabel: 'Identificaci\u00F3n',
                xtype: 'textfield'
            });            
            
            DOCFechaActivacionDesde = new Ext.form.DateField({
                id: 'fechaActivacionDesde',
                fieldLabel: 'Fecha Activaci\u00f3n Desde',
                labelAlign : 'left',
                xtype: 'datefield',
                format: 'Y-m-d',
                width:360,
                editable: false
            });
            DOCFechaActivacionHasta = new Ext.form.DateField({
                id: 'fechaActivacionHasta',
                fieldLabel: 'Fecha Activaci\u00f3n Hasta',
                labelAlign : 'left',
                xtype: 'datefield',
                format: 'Y-m-d',
                width:360,
                editable: false
            });	   
            

            DOCFechaPrePlanificacionDesde = new Ext.form.DateField({
                id: 'fechaPrePlanificacionDesde',
                fieldLabel: 'Fecha Pre-Planificaci\u00f3n Desde',
                labelAlign : 'left',
                xtype: 'datefield',
                format: 'Y-m-d',
                width:360,
                editable: false
            });
            DOCFechaPrePlanificacionHasta = new Ext.form.DateField({
                id: 'fechaPrePlanificacionHasta',
                fieldLabel: 'Fecha Pre-Planificaci\u00f3n Hasta',
                labelAlign : 'left',
                xtype: 'datefield',
                format: 'Y-m-d',
                width:360,
                editable: false
            });	               
			
                Ext.define('ListaDetalleModel', {
                    extend: 'Ext.data.Model',
                    fields: [{name:'idPersona', type: 'int'},
                            {name:'Nombre', type: 'string'},
                            {name:'Direccion', type: 'string'},
                            {name:'fechaCreacion', type: 'string'},
                            {name:'usuarioCreacion', type: 'string'},
                            {name:'login', type: 'string'},
                            {name:'vendedor', type: 'string'},
                            {name:'servicio', type: 'string'},
                            {name:'feActivacion', type:'string'},
                            {name:'fePrePlanificacion', type:'string'},                            
                            {name:'estado', type: 'string'},
                            {name:'linkVer', type: 'string'},
                            {name:'pagos', type: 'string'},
                            {name:'feEmisionFactura', type: 'string'},
                            {name:'numeroFactura', type: 'string'},
                            {name:'estadoFactura', type: 'string'},
                            {name:'identificacion', type: 'string'},
                            {name:'numeroEmpPub', type: 'string'},
                            {name:'ofiVendedor', type: 'string'},
                            {name:'ofiPtoCobertura', type: 'string'},
                            {name:'usuarioAprobacion', type: 'string'},
                            {name:'fechaAprobacion', type: 'string'},
                            {name:'estadoContrato', type: 'string'},
                            {name:'descripcionCuenta', type: 'string'}
                            ]
                }); 


                store = Ext.create('Ext.data.JsonStore', {
                    model: 'ListaDetalleModel',
                            pageSize: itemsPerPage,
                    proxy: {
                        timeout: 999999,
                        type: 'ajax',
                        url: url_grid,
                        reader: {
                            type: 'json',
                            root: 'clientes',
                            totalProperty: 'total'
                        },
                        extraParams:{ 
                                      estado:'',
                                      nombre:'',
                                      apellido:'',
                                      razonSocial:'',
                                      mes:'',
                                      anio:'',
                                      fechaActivacionDesde:'',
                                      fechaActivacionHasta:'',
                                      fechaPrePlanificacionDesde:'',
                                      fechaPrePlanificacionHasta:'',                                      
                                      cbxIdOficina:'',
                                      identificacion:''
                                    },
                        simpleSortMode: true
                    },
                    listeners: {
                        beforeload: function(store){
                            store.getProxy().extraParams.mes                         = Ext.getCmp('idmes').getValue();
                            store.getProxy().extraParams.anio                        = Ext.getCmp('idanio').getValue();   
                            store.getProxy().extraParams.estado                      = Ext.getCmp('idestado').getValue();   
                            store.getProxy().extraParams.nombre                      = Ext.getCmp('nombre').getValue();   
                            store.getProxy().extraParams.apellido                    = Ext.getCmp('apellido').getValue();   
                            store.getProxy().extraParams.razonSocial                 = Ext.getCmp('razonSocial').getValue(); 
                            store.getProxy().extraParams.identificacion              = Ext.getCmp('identificacion').getValue(); 
                            store.getProxy().extraParams.fechaActivacionDesde        = Ext.getCmp('fechaActivacionDesde').getValue();   
                            store.getProxy().extraParams.fechaActivacionHasta        = Ext.getCmp('fechaActivacionHasta').getValue(); 
                            store.getProxy().extraParams.fechaPrePlanificacionDesde  = Ext.getCmp('fechaPrePlanificacionDesde').getValue();   
                            store.getProxy().extraParams.fechaPrePlanificacionHasta  = Ext.getCmp('fechaPrePlanificacionHasta').getValue();                             
                            store.getProxy().extraParams.cbxIdOficina                = Ext.getCmp('cbxIdOficina').getValue().toString(); 
                            store.getProxy().extraParams.cbxIdOficinaPtoCobertura    = Ext.getCmp('cbxIdOficinaPtoCobertura').getValue().toString(); 
                            store.getProxy().extraParams.cbxIdPlan                   = Ext.getCmp('cbxIdPlan').getValue().toString(); 
                        },
                        load: function(store){
                            store.each(function(record) {
                                //idClienteSucursalSesion = record.data.idClienteSucursalSesion;
                            });
                        }
                    }
                });
 

                var listView = Ext.create('Ext.grid.Panel', {
                    width:1200,
                    height:300,
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
                                        iconCls: 'icon_exportar',
                                        text: 'Generar-Enviar CSV',
                                        disabled: false,
                                        itemId: 'exportar',
                                        scope: this,
                                        handler: function(){generarReporteComercial()}
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
                        width: 250,
                        dataIndex: 'Nombre'
                    },{
                        text: 'Identificaci\u00f3n',
                        dataIndex: 'identificacion',
                        align: 'left',
                        width: 100			
                    },{
                        text: 'Fecha Creacion Cli',
                        dataIndex: 'fechaCreacion',
                        align: 'left',
                        width: 150			
                    },{
                        text: 'Estado Cliente',
                        dataIndex: 'estado',
                        align: 'left',
                        width: 85
                    },{
                        text: 'Login',
                        dataIndex: 'login',
                        align: 'left',
                        width: 150
                    },{
                        text: 'Ofi.Pto.Cobertura',
                        dataIndex: 'ofiPtoCobertura',
                        align: 'left',
                        width: 250
                    },{
                        text: 'Vendedor',
                        dataIndex: 'vendedor',
                        align: 'left',
                        width: 150
                    },{
                        text: 'Oficina Vendedor',
                        dataIndex: 'ofiVendedor',
                        align: 'left',
                        width: 250
                    },{
                        text: 'Usr Aprobaci\u00f3n',
                        dataIndex: 'usuarioAprobacion',
                        align: 'left',
                        width: 150
                    },{
                        text: 'Fecha Aprobaci\u00f3n',
                        dataIndex: 'fechaAprobacion',
                        align: 'left',
                        width: 150			
                    },{
                        text: 'Estado Contrato',
                        dataIndex: 'estadoContrato',
                        align: 'left',
                        width: 85
                    },{
                        text: 'Num Emp. P\u00fablica',
                        dataIndex: 'numeroEmpPub',
                        align: 'left',
                        width: 150
                    },{
                        text: 'Servicio',
                        dataIndex: 'servicio',
                        align: 'left',
                        width: 250
                    },{
                        text: 'Fecha Pre-Planificaci\u00f3n ',
                        dataIndex: 'fePrePlanificacion',
                        align: 'left',
                        width: 150
                    },{
                        text: 'Fecha activaci\u00f3n Servicio',
                        dataIndex: 'feActivacion',
                        align: 'left',
                        width: 150
                    },{
                        text: 'Factura',
                        dataIndex: 'numeroFactura',
                        align: 'left',
                        width: 150
                    },{
                        text: 'Fecha Emision',
                        dataIndex: 'feEmisionFactura',
                        align: 'left',
                        width: 150
                    },{
                        text: 'Estado Fact',
                        dataIndex: 'estadoFactura',
                        align: 'left',
                        width: 85
                    },{
                        text: 'Pagos',
                        width: 40,
                        dataIndex: 'pagos'
                    },{
                        text: 'Descripcion Cta.',
                        dataIndex: 'descripcionCuenta',
                        align: 'left',
                        width: 150
                    },{
                        text: 'Acciones',
                        width: 45,
                        renderer: renderAcciones
                    }
                    
                    ]
                });            

            
            function renderAcciones(value, p, record) {
                    var iconos='';
                    iconos=iconos+'<b><a href="'+record.data.linkVer+'" onClick="" title="Ver Cliente" class="button-grid-show"></a></b>';
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
                width: 1200,
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
                                mes_cmb,
                                {html:"&nbsp;",border:false,width:100},
                                anio_cmb,
                                {html:"&nbsp;",border:false,width:100},
                                DOCFechaActivacionDesde,
                                {html:"&nbsp;",border:false,width:100},
                                DOCFechaActivacionHasta,                                  
                                {html:"&nbsp;",border:false,width:100},
                                DOCFechaPrePlanificacionDesde,
                                {html:"&nbsp;",border:false,width:100},
                                DOCFechaPrePlanificacionHasta,                                  
                                {html:"&nbsp;",border:false,width:100},                                
                                TFNombre,
                                {html:"&nbsp;",border:false,width:100},
                                TFApellido,
                                {html:"&nbsp;",border:false,width:100},
                                TFRazonSocial,
                                {html:"&nbsp;",border:false,width:100},
                                TFIdentificacion,
                                {html:"&nbsp;",border:false,width:100},
                                cbxPlanes,   
                                {html:"&nbsp;",border:false,width:100},
                                cbxOficinas,
                                {html:"&nbsp;",border:false,width:100},                                
                                cbxOficinasPtoCobertura
                                
                                ],	
                renderTo: 'filtro_clientes'
            }); 
      

        function Buscar()
        {

            var strIdentificacion = Ext.getCmp('identificacion').getValue();

            if (strIdentificacion!== null && strIdentificacion!=='')
            {  
              if (!(/^[0-9a-zA-Z]+$/.test(strIdentificacion)))
              {
                  Ext.Msg.alert('Alerta ', 'Identificaci\u00F3n no v\u00E1lida.');
                  return false;
              }                       
            }   
          
            if (Ext.getCmp('fechaActivacionDesde').getValue() > Ext.getCmp('fechaActivacionHasta').getValue())
            {
                Ext.Msg.show({
                    title: 'Error en B\u00fasqueda',
                    msg: 'Por Favor para realizar la b\u00fasqueda Fecha Activaci\u00F3n Desde debe ser fecha menor a Fecha Activaci\u00F3n Hasta.',
                    buttons: Ext.Msg.OK,
                    animEl: 'elId',
                    icon: Ext.MessageBox.ERROR
                });
                return false;
            } 
            
            if (Ext.getCmp('fechaPrePlanificacionDesde').getValue() > Ext.getCmp('fechaPrePlanificacionHasta').getValue())
            {
                Ext.Msg.show({
                    title: 'Error en B\u00fasqueda',
                    msg: 'Por Favor para realizar la b\u00fasqueda Fecha Pre-Planificaci\u00F3n Desde debe ser fecha menor a \n\
                          Fecha Pre-Planificaci\u00F3n Hasta.',
                    buttons: Ext.Msg.OK,
                    animEl: 'elId',
                    icon: Ext.MessageBox.ERROR
                });
                return false;
            }              

          store.load({params: {start: 0, limit: 10}});
        }
        
        function Limpiar(){
            
            Ext.getCmp('fechaDesde').setValue('');
            Ext.getCmp('fechaHasta').setValue('');
            Ext.getCmp('idestado').setValue('');
            Ext.getCmp('nombre').setValue('');
            Ext.getCmp('apellido').setValue('');
            Ext.getCmp('razonSocial').setValue('');	
            Ext.getCmp('fechaActivacionDesde').setValue('');
            Ext.getCmp('fechaActivacionHasta').setValue(''); 
            Ext.getCmp('fechaPrePlanificacionDesde').setValue('');
            Ext.getCmp('fechaPrePlanificacionHasta').setValue('');             
            Ext.getCmp('identificacion').setValue('');
            Ext.getCmp('cbxIdPlan').setValue('');
            Ext.getCmp('cbxIdOficinaPtoCobertura').setValue('');
            Ext.getCmp('cbxIdOficina').setValue('');            
        }
        
        function exportar(){    


                            window.location.href=url_excel+'?mes='+Ext.getCmp('idmes').getValue()+
                            '&anio='+Ext.getCmp('idanio').getValue()+
                            '&nombre='+Ext.getCmp('nombre').getValue()+
                            '&apellido='+Ext.getCmp('apellido').getValue()+
                            '&razonSocial='+Ext.getCmp('razonSocial').getValue()+
                            '&estado='+Ext.getCmp('idestado').getValue();

        }    
              
              
     /**
     * Función que envia los parametros necesarios para la generación del reporte de clientes - facturas
     *
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.1 16-12-2016 
     */  
    function generarReporteComercial()
    {
      
      var strIdentificacion = Ext.getCmp('identificacion').getValue();

      if (strIdentificacion!== null && strIdentificacion!=='')
      {  
        if (!(/^[0-9a-zA-Z]+$/.test(strIdentificacion)))
        {
            Ext.Msg.alert('Alerta ', 'Identificaci\u00F3n no v\u00E1lida.');
            return false;
        }                       
      }   

      if (Ext.getCmp('fechaActivacionDesde').getValue() > Ext.getCmp('fechaActivacionHasta').getValue())
      {
          Ext.Msg.show({
              title: 'Error en B\u00fasqueda',
              msg: 'Por Favor para realizar la b\u00fasqueda Fecha Activaci\u00F3n Desde debe ser fecha menor a Fecha Activaci\u00F3n Hasta.',
              buttons: Ext.Msg.OK,
              animEl: 'elId',
              icon: Ext.MessageBox.ERROR
          });
          return false;
      }  

      if (Ext.getCmp('fechaPrePlanificacionDesde').getValue() > Ext.getCmp('fechaPrePlanificacionHasta').getValue())
      {
          Ext.Msg.show({
              title: 'Error en B\u00fasqueda',
              msg: 'Por Favor para realizar la b\u00fasqueda Fecha Pre-Planificaci\u00F3n Desde debe ser fecha menor a \n\
                    Fecha Pre-Planificaci\u00F3n Hasta.',
              buttons: Ext.Msg.OK,
              animEl: 'elId',
              icon: Ext.MessageBox.ERROR
          });
          return false;
      }  
      
      Ext.MessageBox.wait('Generando Reporte. Favor espere..');
      Ext.Ajax.request(
          {
              timeout: 999999,
              url: urlGenerarRptComercial,
              type: 'json' ,       
              params:
                  {                                     
                      mes: Ext.getCmp('idmes').getValue(),
                      anio: Ext.getCmp('idanio').getValue(),
                      estado: Ext.getCmp('idestado').getValue(),
                      nombre: Ext.getCmp('nombre').getValue(),
                      apellido: Ext.getCmp('apellido').getValue(),
                      razonSocial: Ext.getCmp('razonSocial').getValue(),
                      identificacion: Ext.getCmp('identificacion').getValue(),
                      fechaActivacionDesde: Ext.getCmp('fechaActivacionDesde').getValue(),
                      fechaActivacionHasta: Ext.getCmp('fechaActivacionHasta').getValue(),
                      fechaPrePlanificacionDesde: Ext.getCmp('fechaPrePlanificacionDesde').getValue(),
                      fechaPrePlanificacionHasta: Ext.getCmp('fechaPrePlanificacionHasta').getValue(),                      
                      cbxIdOficina: Ext.getCmp('cbxIdOficina').getValue().toString(),
                      cbxIdOficinaPtoCobertura: Ext.getCmp('cbxIdOficinaPtoCobertura').getValue().toString(),
                      cbxIdPlan: Ext.getCmp('cbxIdPlan').getValue().toString()
                  },
              method: 'post',

              success: function(response) 
              {
                  Ext.Msg.alert('Mensaje',response.responseText);
              },
              failure: function(response)
              {
                  Ext.Msg.alert('Error ', response.responseText);
              }
          });        

      }

});
