    Ext.require([
        '*',
        'Ext.tip.QuickTipManager',
            'Ext.window.MessageBox'
    ]);

    var itemsPerPage = 10;
    var motivo_id='';
    var relacion_sistema_id='';
    var tipo_solicitud_id='';
    var area_id='';
    var login_id='';
    var tipo_asignacion='';
    var pto_sucursal='';
    var idClienteSucursalSesion;
    var sumaInstalacion="";

    Ext.onReady(function(){

        Ext.form.VTypes["valorVtypeVal"] =/(^\d{1,4}\.\d{1,2}$)|(^\d{1,4}$)/;		
        Ext.form.VTypes["valorVtype"]=function(v){
            return Ext.form.VTypes["valorVtypeVal"].test(v);
        };
        Ext.form.VTypes["valorVtypeText"]="Puede ingresar hasta 4 enteros y al menos 1 decimal o puede ingresar hasta 4 enteros sin decimales";
        Ext.form.VTypes["valorVtypeMask"]=/[\d\.]/;

        Ext.form.VTypes["porcentajeVtypeVal"] =/(^\d{1,3}\.\d{1,2}$)|(^\d{1,3}$)/;		
        Ext.form.VTypes["porcentajeVtype"]=function(v){
            return Ext.form.VTypes["porcentajeVtypeVal"].test(v);
        };
        Ext.form.VTypes["porcentajeVtypeText"]="Puede ingresar hasta 3 enteros y al menos 1 decimal o puede ingresar hasta 3 enteros sin decimales";
        Ext.form.VTypes["porcentajeVtypeMask"]=/[\d\.]/;

        /* Se agrega combo tipo de descuento para la generacion de la
        * solicitud de descuento unico.
        * 
        * @author rcoello@telconet.ec
        * @version 1.1 04-05-2017
        *
        */
        function solicitarDescuento(){
            var param                       = '';
            var strDescuentoSeleccionado    = '';

            if(sm.getSelection().length > 0)
            {
              strDescuentoSeleccionado  = Ext.getCmp('cboTipoDescuento').getValue();
              var estado                = 0;
              for(var i=0 ;  i < sm.getSelection().length ; ++i)
              {
                param = param + sm.getSelection()[i].data.idServicio +"-"+sm.getSelection()[i].data.precioVenta+"-"+sm.getSelection()[i].data.descuentoFijo+"-"+sm.getSelection()[i].data.precioDescuentoFijo;
                if(i < (sm.getSelection().length -1))
                {
                  param = param + '|';
                }
              }      

              if(strDescuentoSeleccionado)
              {
                    if(motivo_id)
                    { 
                      if((Ext.getCmp('valorPrecio').getValue())&&(Ext.getCmp('valorPrecio').isValid()))
                              ejecutaEnvioSolicitud(param);
                          else
                        Ext.Msg.alert('Alerta ','Por favor ingresar el Valor o verifique que este ingresado correctamente');                        

                    }
                    else
                    {
                      alert('Seleccione el Motivo de la solicitud');
                    }

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


        /**
        * Documentación para funcion 'ejecutaEnvioSolicitud'.
        * Metodo que envia a generar la solicitud  de descuento unico
        * 
        * Se agrega nuevo parametro 'tipoDescuento' a la peticion ajax
        * 
        * @author rcoello@telconet.ec
        * @version 1.1 05-05-2017
        *
        */   
        function ejecutaEnvioSolicitud(param){
                    var tipoValor           = '';
                    var valor               = '';
                    var strTipoDescuento    ='';
                    
                   if(Ext.getCmp('valorPrecio').getValue()){
                       tipoValor    =   'valor';
                       valor        =   TFPrecio.getValue();
                       valor        =   parseFloat(valor).toFixed(2);
                   }

                   strTipoDescuento = Ext.getCmp('cboTipoDescuento').getValue();

                    Ext.Msg.confirm('Alerta','Se solicitara descuento para los registros seleccionados. Desea continuar?', function(btn){
                        if(btn=='yes'){
                            Ext.Ajax.request({
                                url:    url_solicitar_descuento_ajax,
                                method: 'post',
                                params: { param : param, motivoId:motivo_id, rs: relacion_sistema_id, ts:tipo_solicitud_id, tValor:tipoValor, 
                                          v:valor, obs:TFObservacion.getValue(), tipoDescuento: strTipoDescuento},
                                success: function(response)
                                {
                                    var text = response.responseText;
                                    Ext.Msg.alert('Ok ',response.responseText);

                                   storeServiciosDescuento.currentPage = 1;
                                   storeServiciosDescuento.reload();


                                    TFPrecio.setValue("");
                                },
                                failure: function(response)
                                {
                                    Ext.Msg.alert('Error ','Error: ' + response.responseText);
                                }
                            });
                        }
                    });                

        }

        var strTipoDescuentoPorDefault = null;

        //Creamos Store para Tipo de Descuento
        Ext.define('modelTipoDescuento', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'strDisplayTipoDescuento', type: 'string'},
                {name: 'strValueTipoDescuento',   type: 'string'},
                {name: 'strValueSelected',        type: 'string'}
            ]
        });

        storeTipoDescuento = Ext.create('Ext.data.Store', {
            id: 'idStoreTipoDescuento',
            autoLoad: true,
            model: "modelTipoDescuento",
            proxy: {
                type: 'ajax',
                url: urlGetTipoDescuento,
                reader: {
                    type: 'json',
                    root: 'arrayTipoDescuento'
                },
                extraParams:
                {
                    strTipoSolicitud: 'SOL_DCTO_UNICO'
                }
            },
            listeners: {

                load: function(storeCbxTipoDescuento, records, success) {
                    if(storeCbxTipoDescuento.getCount() == 0)
                    {
                        Ext.Msg.alert('Alerta ', 'No hay descuento relacionado a la solicitud.');
                    }
                    else
                    {
                        for (var i = 0; i < storeCbxTipoDescuento.data.items.length; i++)
                        {
                            if (!Ext.isEmpty(storeCbxTipoDescuento.data.items[i].data.strValueSelected))
                            {
                                Ext.getCmp('cboTipoDescuento').setValue( storeCbxTipoDescuento.data.items[i].data.strValueSelected );
                                break;
                            }
                        }
                    }
                }
            }
        });

        cboTipoDescuento = new Ext.form.ComboBox({
            id: 'cboTipoDescuento',
            name: 'cboTipoDescuento',
            xtype: 'combobox',
            editable: false,
            queryMode: 'local',
            store: storeTipoDescuento,
            labelAlign: 'left',
            valueField: 'strValueTipoDescuento',
            displayField: 'strDisplayTipoDescuento',
            fieldLabel: 'Tipo Descuento',
            width: 250,
            height: 30
        });    

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
            id:'idMotivo',
            name: 'idMotivo',
            valueField:'idMotivo',
            displayField:'descripcion',
            fieldLabel: 'Motivo',
            labelAlign:'right',
            width: 325,
            triggerAction: 'all',
            selectOnFocus:true,
            lastQuery: '',
            mode: 'local',
            allowBlank: true,	

            listeners: {
                        select:
                        function(e) {
                            motivo_id           = Ext.getCmp('idMotivo').getValue();
                            relacion_sistema_id = e.displayTplData[0].idRelacionSistema;
                            tipo_solicitud_id   = e.displayTplData[0].idTipoSolicitud;
                        },
                        click: {
                            element: 'el',
                            fn: function(){ 
                                motivo_id           = '';
                                relacion_sistema_id = '';
                                tipo_solicitud_id   = '';
                                motivo_store.removeAll();
                                motivo_store.load();
                            }
                        }			
            }
        });

        TFPrecio = new Ext.form.TextField({
                id: 'valorPrecio',
                name: 'valorPrecio',
                labelAlign:'right',
                fieldLabel: 'Valor Porcentaje',

                xtype: 'textfield',
                width: '170px',
                vtype: 'valorVtype'
        });

        TFObservacion = new Ext.form.field.TextArea({
                xtype     : 'textareafield',
                name      : 'observacion',
                fieldLabel: 'Observación',
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
                        {name:'precioVenta', type: 'double'}, 
                        {name:'descuentoFijo', type: 'string'},
                        {name:'precioDescuentoFijo', type: 'double'},
                        {name:'estado', type: 'string'},
                        {name:'yaFueSolicitada', type: 'string'},
                        {name:'strNombreProducto', type: 'string'}
                        ]
            }); 

            storeServiciosDescuento = Ext.create('Ext.data.JsonStore',
            {
                model: 'ListaDetalleModel',
                pageSize: itemsPerPage,
                autoLoad: true,
                proxy:
                    {
                        type: 'ajax',
                        url: url_grid,
                        simpleSortMode: true,
                        reader:
                            {
                                type: 'json',
                                root: 'servicios',
                                totalProperty: 'total'
                            },
                        extraParams:
                            {
                                tipo: 'servicios'
                            }
                    },
                listeners:
                    {
                        beforeload:
                            function(store, id_punto_cliente)
                            {
                                store.getProxy().extraParams.idPuntoCliente = id_punto_cliente;
                            }
                    }
            });

            var sm = new Ext.selection.CheckboxModel(
            {
                listeners:
                    {
                        select: function(selectionModel, record, index, eOpts)
                        {
                            if (record.data.yaFueSolicitada == 'S')
                            {
                                sm.deselect(index);
                                Ext.Msg.alert('Alerta', 'Ya fue solicitado descuento para el servicio: ' + record.data.descripcionProducto);
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
                width: 800,
                title: 'Opciones',
                items: [
                        {
                        xtype: 'toolbar',
                        dock: 'top',
                        align: '->',
                        items: [
                                cboTipoDescuento
                                ]
                       },{
                                xtype: 'toolbar',
                                dock: 'top',
                                align: '->',
                                items: [
                                    TFPrecio,
                                    { xtype: 'tbfill' },
                                    motivo_cmb,
                                    {
                                    iconCls: 'icon_solicitud',
                                    text: 'Solicitar',
                                    disabled: false,
                                    itemId: 'delete',
                                    scope: this,
                                    handler: function(){ solicitarDescuento();}
                                    }

                              ]}
                ],	
                renderTo: 'filtro_servicios'
            });
            
            var observacionPanel = Ext.create('Ext.panel.Panel', {
                    bodyPadding: 7,
                    border:true,
                    bodyStyle: {
                                background: '#fff'
                    },                     
                    defaults: {
                        bodyStyle: 'padding:10px'
                    },
                    width: 800,
                    title: '',
                    items: [
                      TFObservacion
                    ],	
                    renderTo: 'panel_observacion'
                });


            var listView = Ext.create('Ext.grid.Panel', {
                width:800,
                height:350,
                collapsible:false,
                title: 'Servicios del punto cliente',
                selModel: sm,                    
                renderTo: Ext.get('lista_servicios'),
                bbar: Ext.create('Ext.PagingToolbar', {
                    store: storeServiciosDescuento,
                    displayInfo: true,
                    displayMsg: 'Mostrando Servicios: {0} - {1} de {2}',
                    emptyMsg: "No hay datos para mostrar"
                }),	
                store: storeServiciosDescuento,
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
                    text: 'Producto/Plan',
                    width: 150,
                    dataIndex: 'strNombreProducto'
                },{
                    text: 'Descripción',
                    width: 150,
                    dataIndex: 'descripcionProducto'
                },{
                    text: 'Cantidad',
                    width: 60,
                     align: 'right',
                    dataIndex: 'cantidad'
                },{
                    text: 'Precio Venta',
                    dataIndex: 'precioVenta',
                    align: 'right',
                    width: 80			
                },{
                    text: 'Desct Fijo',
                    dataIndex: 'descuentoFijo',
                    align: 'right',
                    width: 60			
                },
                {
                    text: 'Precio Desct Fijo',
                    dataIndex: 'precioDescuentoFijo',
                    align: 'right',
                    width: 110			
                },
                {
                    text: 'Fecha Creación',
                    dataIndex: 'fechaCreacion',
                    align: 'right',
                    flex: 60			
                },{
                    text: 'Estado',
                    dataIndex: 'estado',
                    align: 'right',
                    flex: 30
                }

                ]
            });

            function Buscar()
            {
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
                        storeServiciosDescuento.currentPage = 1;
                        storeServiciosDescuento.load({params: {start: 0, limit: itemsPerPage}});
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
