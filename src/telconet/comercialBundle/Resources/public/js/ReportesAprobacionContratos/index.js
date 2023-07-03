            Ext.require([
                '*',
                'Ext.tip.QuickTipManager',
                    'Ext.window.MessageBox'
            ]);

            var itemsPerPage = 30;
            var store='';

            Ext.onReady(function(){
            
            var objScope = 
            {
               extraParams: 
               {
                  strAppendDatos: 'Todos',
                  strDisponiblesPersona: ''
               }
            };

            objStorePtosCobertura = function(objScope) {
            modelPtosCobertura    = Ext.define('modelPtosCoberturaByEmpresa', {
                     extend: 'Ext.data.Model',
                     fields: [
                              {name: 'intIdObj', type: 'int'},
                              {name: 'strDescripcionObj', type: 'string'}
                             ]
                });
                return new Ext.create('Ext.data.Store', {
                id: objScope.id,
                model: modelPtosCobertura,
                autoLoad: true,
                proxy: {
                      type: 'ajax',
                      url: urlGetPtosCoberturaByEmpresa,
                      timeout: 99999,
                      reader: {
                                type: 'json',
                                root: 'registros'
                              },
                      extraParams: objScope.extraParams,
                      simpleSortMode: true
                   }
                });
            };
            var objClienteA = new Cliente();
            var objStorePtosCoberturaByEmpresa        = objStorePtosCobertura(objScope);
            objStorePtosCoberturaByEmpresa.objStore   = objStorePtosCoberturaByEmpresa;
            objStorePtosCoberturaByEmpresa.strIdObj   = 'cbxIdPtoCobertura';
            objStorePtosCoberturaByEmpresa.intWidth   = 450;

            var cbxPtoCobertura     = objClienteA.objComboMultiSelectDatos(objStorePtosCoberturaByEmpresa,'Ptos. Cobertura');
            cbxPtoCobertura.colspan = 4;
            cbxPtoCobertura.setWidth(400);
            
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
                    fields: [{name:'intIdServicio', type: 'int'},
                            {name:'strLogin', type: 'string'},
                            {name:'strFePrePlanificacion', type: 'string'},
                            {name:'strUltEstadoSolPlanific', type: 'string'},
                            {name:'strNumContratoEmpPub', type: 'string'},
                            {name:'strNumContratoSistema', type: 'string'},
                            {name:'strIdentificacion', type: 'string'},
                            {name:'strNombreCliente', type: 'string'},
                            {name:'strPtoCobertura', type:'string'},
                            {name:'strUsrAprobacion', type:'string'},
                            {name:'strVendedor', type: 'string'},
                            {name:'strFeCreacionProspecto', type: 'string'},
                            {name:'strFeCreacionPto', type: 'string'},
                            {name:'strFeCreacionServicio', type: 'string'},
                            {name:'strFeFactible', type: 'string'},
                            {name:'strCanalVenta', type: 'string'},
                            {name:'strPuntoVenta', type: 'string'},
                            {name:'strFormaPago', type: 'string'},
                            {name:'strDescripcionBanco', type: 'string'},
                            {name:'strDescripcionCuenta', type: 'string'},
                            {name:'strEstadoContrato', type: 'string'},
                            {name:'fltCostoInstalacion', type: 'string'},
                            {name:'strCortesia', type: 'string'},
                            {name:'strNumeroFactura', type: 'string'},
                            {name:'strEstadoFactura', type: 'string'},
                            {name:'strNumeroPago', type: 'string'},
                            {name:'strFeCreacionPago', type: 'string'},
                            {name:'strObservacionPago', type: 'string'},
                            {name:'strUltimaMilla', type: 'string'},
                            {name:'strSegmento', type: 'string'},
                            {name:'strTipoContrato', type: 'string'},
                            {name:'strPlanProducto', type: 'string'}
                            ]
                }); 

                store = Ext.create('Ext.data.JsonStore', {
                    model: 'ListaDetalleModel',
                            pageSize: itemsPerPage,
                    proxy: {
                        timeout: 9999999999,
                        type: 'ajax',
                        url: urlGridRptAprobContratos,
                        reader: {
                            type: 'json',
                            root: 'clientes',
                            totalProperty: 'total'
                        },
                        extraParams:{
                                      fechaPrePlanificacionDesde:'',
                                      fechaPrePlanificacionHasta:'',
                                      cbxIdPtoCobertura:''
                                    },
                        simpleSortMode: true
                    },
                    listeners: {
                        beforeload: function(store){
                           store.getProxy().extraParams.fechaPrePlanificacionDesde  = Ext.getCmp('fechaPrePlanificacionDesde').getValue();
                           store.getProxy().extraParams.fechaPrePlanificacionHasta  = Ext.getCmp('fechaPrePlanificacionHasta').getValue();
                           store.getProxy().extraParams.cbxIdPtoCobertura           = Ext.getCmp('cbxIdPtoCobertura').getValue().toString();
                        },
                        load: function(store){
                            store.each(function(record) {
                            });
                        }
                    }
                });
 
                var listView = Ext.create('Ext.grid.Panel', {
                    width:1200,
                    height:600,
                    collapsible:false,
                    title: '',
                    dockedItems: [ {
                                    xtype: 'toolbar',
                                    dock: 'top',
                                    align: '->',
                                    items: [
                                    { xtype: 'tbfill' },
                                    {
                                        iconCls: 'icon_exportar',
                                        text: 'Generar-Enviar CSV',
                                        disabled: false,
                                        itemId: 'exportar',
                                        scope: this,
                                        handler: function(){generarRptAprobacionContratos()}
                                    }
                                    ]}],
                    renderTo: Ext.get('lista_clientes'),
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
                        text: 'Login',
                        width: 100,
                        dataIndex: 'strLogin'
                    },{
                        text: 'Fe. PrePlanificaci\u00f3n',
                        dataIndex: 'strFePrePlanificacion',
                        align: 'left',
                        width: 120
                    },{
                        text: 'Ult. Estado PrePlanif.',
                        dataIndex: 'strUltEstadoSolPlanific',
                        align: 'left',
                        width: 120
                    },{
                        text: 'No. Emp. P\u00fablica',
                        dataIndex: 'strNumContratoEmpPub',
                        align: 'left',
                        width: 100
                    },{
                        text: 'No. Contrato Sistema',
                        dataIndex: 'strNumContratoSistema',
                        align: 'left',
                        width: 120
                    },{
                        text: 'Identificaci\u00f3n',
                        dataIndex: 'strIdentificacion',
                        align: 'left',
                        width: 100
                    },{
                        text: 'Cliente',
                        dataIndex: 'strNombreCliente',
                        align: 'left',
                        width: 150
                    },{
                        text: 'Pto. Cobertura',
                        dataIndex: 'strPtoCobertura',
                        align: 'left',
                        width: 120
                    },{
                        text: 'Usr. Aprobaci\u00f3n',
                        dataIndex: 'strUsrAprobacion',
                        align: 'left',
                        width: 120
                    },{
                        text: 'Vendedor',
                        dataIndex: 'strVendedor',
                        align: 'left',
                        width: 120    
                    },{
                        text: 'Fe. Creaci\u00f3n Prospecto',
                        dataIndex: 'strFeCreacionProspecto',
                        align: 'left',
                        width: 120
                    },{
                        text: 'Fe. Creaci\u00f3n Punto',
                        dataIndex: 'strFeCreacionPto',
                        align: 'left',
                        width: 150
                    },{
                        text: 'Fe. Creaci\u00f3n Servicio',
                        dataIndex: 'strFeCreacionServicio',
                        align: 'left',
                        width: 120
                    },{
                        text: 'Fe. Factible',
                        dataIndex: 'strFeFactible',
                        align: 'left',
                        width: 100
                    },{
                        text: 'Canal Venta',
                        dataIndex: 'strCanalVenta',
                        align: 'left',
                        width: 100
                    },{
                        text: 'Punto Venta',
                        dataIndex: 'strPuntoVenta',
                        align: 'left',
                        width: 100
                    },{
                        text: 'Forma Pago',
                        dataIndex: 'strFormaPago',
                        align: 'left',
                        width: 100
                    },{
                        text: 'Desc. Banco',
                        dataIndex: 'strDescripcionBanco',
                        align: 'left',
                        width: 100
                    },{
                        text: 'Desc. Cuenta',
                        dataIndex: 'strDescripcionCuenta',
                        align: 'left',
                        width: 100
                    },{
                        text: 'Estado Contrato',
                        dataIndex: 'strEstadoContrato',
                        align: 'left',
                        width: 100
                    },{
                        text: 'Costo Instalaci\u00f3n',
                        dataIndex: 'fltCostoInstalacion',
                        align: 'left',
                        width: 150
                    },{
                        text: 'Cortes\u00eda',
                        dataIndex: 'strCortesia',
                        align: 'left',
                        width: 80
                    },{
                        text: 'Num. Factura',
                        dataIndex: 'strNumeroFactura',
                        align: 'left',
                        width: 100
                    },{
                        text: 'Estado Factura',
                        dataIndex: 'strEstadoFactura',
                        align: 'left',
                        width: 100
                    },{
                        text: 'Num. Pago',
                        dataIndex: 'strNumeroPago',
                        align: 'left',
                        width: 100
                    },{
                        text: 'Fe. Creaci\u00f3n Pago',
                        dataIndex: 'strFeCreacionPago',
                        align: 'left',
                        width: 100
                    },{
                        text: 'Obs. Pago',
                        dataIndex: 'strObservacionPago',
                        align: 'left',
                        width: 150
                    },{
                        text: 'Ultima Milla',
                        dataIndex: 'strUltimaMilla',
                        align: 'left',
                        width: 100
                    },{
                        text: 'Segmento',
                        dataIndex: 'strSegmento',
                        align: 'left',
                        width: 100
                    },{
                        text: 'Tipo Contrato',
                        dataIndex: 'strTipoContrato',
                        align: 'left',
                        width: 100
                    },{
                        text: 'Plan/Producto',
                        dataIndex: 'strPlanProducto',
                        align: 'left',
                        width: 150
                    }
                    ]
                });
           
            var filterPanel = Ext.create('Ext.panel.Panel', {
                bodyPadding: 7,
                border:false,
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
                            bodyStyle: 'padding:10px'
                          },
                collapsible : true,
                collapsed: true,
                width: 1200,
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
                                DOCFechaPrePlanificacionDesde,
                                {html:"&nbsp;&nbsp;",border:false,width:200},
                                DOCFechaPrePlanificacionHasta,
                                {html:"&nbsp;&nbsp;",border:false,width:200},
                                cbxPtoCobertura
                                ],
                renderTo: 'filtro_clientes'
            }); 

       function transformarFecha(dateSinFormat)
       {
           var d = new Date(dateSinFormat);
           var curr_date = d.getDate();
           var curr_month = d.getMonth();
           curr_month++;
           var curr_year = d.getFullYear();
  
           var dateFormato = curr_date + "-" + curr_month + "-" + curr_year;

           return dateFormato;
        }

        function Buscar()
        {
            var fechaPrePlanificacionDesde     = '';
            var fechaPrePlanificacionHasta     = '';
            var boolFechaPrePlanificacionDesde = false;
            var boolFechaPrePlanificacionHasta = false;
            
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
            
            if(Ext.getCmp('fechaPrePlanificacionDesde').getValue()!=null)
            {
                fechaPrePlanificacionDesde       = Ext.getCmp('fechaPrePlanificacionDesde').getValue();
                fechaPrePlanificacionDesde       = transformarFecha(fechaPrePlanificacionDesde);  
                boolFechaPrePlanificacionDesde   = true;
            }

            if(Ext.getCmp('fechaPrePlanificacionHasta').getValue()!=null)
            {
               fechaPrePlanificacionHasta = Ext.getCmp('fechaPrePlanificacionHasta').getValue();
               fechaPrePlanificacionHasta = transformarFecha(fechaPrePlanificacionHasta);
               boolFechaPrePlanificacionHasta   = true;
            }
            
            if(boolFechaPrePlanificacionDesde || boolFechaPrePlanificacionHasta)
            {
                if(boolFechaPrePlanificacionDesde && boolFechaPrePlanificacionHasta)
                {
                    var aFecha1 = fechaPrePlanificacionDesde.split('-'); 
                    var aFecha2 = fechaPrePlanificacionHasta.split('-'); 
                    var fFecha1 = aFecha1[0]+'-'+(aFecha1[1]-1)+'-'+aFecha1[2]; 
                    var fFecha2 = aFecha2[0]+'-'+(aFecha2[1]-1)+'-'+aFecha2[2];             
                    var rangoFechaCreacion = Utils.restaFechas(fFecha1,fFecha2);

                    if(rangoFechaCreacion>31)
                    {                        
                        Ext.Msg.show({
                        title: 'Error en B\u00fasqueda',
                        msg: 'Rango de fechas excede el  limite permitido (31 dias)  ',
                        buttons: Ext.Msg.OK,
                        animEl: 'elId',
                        icon: Ext.MessageBox.ERROR
                        });
                        return false;            
                    }
                }
                else
                {                    
                    Ext.Msg.show({
                        title: 'Error en B\u00fasqueda',
                        msg: 'Debe elegir un rango de Fechas de Creación válido ',
                        buttons: Ext.Msg.OK,
                        animEl: 'elId',
                        icon: Ext.MessageBox.ERROR
                        });
                    return false;
                }
            }
            store.load({params: {start: 0, limit: 30}});
        }
        
        function Limpiar(){
            
            Ext.getCmp('fechaPrePlanificacionDesde').setValue('');
            Ext.getCmp('fechaPrePlanificacionHasta').setValue('');
            Ext.getCmp('cbxIdPtoCobertura').setValue('');
        }

     /**
     * Función que envia los parametros necesarios para la generación del Reporte de Aprobacion de Contratos
     *
     * @author Anabelle Pehaherrera <apenaherrera@telconet.ec>
     * @version 1.0 10-07-2017
     */
    function generarRptAprobacionContratos()
    {
      var fechaPrePlanificacionDesde     = '';
      var fechaPrePlanificacionHasta     = '';
      var boolFechaPrePlanificacionDesde = false;
      var boolFechaPrePlanificacionHasta = false;
            
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
      if (Ext.getCmp('fechaPrePlanificacionDesde').getValue() != null)
      {
          fechaPrePlanificacionDesde = Ext.getCmp('fechaPrePlanificacionDesde').getValue();
          fechaPrePlanificacionDesde = transformarFecha(fechaPrePlanificacionDesde);
          boolFechaPrePlanificacionDesde = true;
      }

      if (Ext.getCmp('fechaPrePlanificacionHasta').getValue() != null)
      {
          fechaPrePlanificacionHasta = Ext.getCmp('fechaPrePlanificacionHasta').getValue();
          fechaPrePlanificacionHasta = transformarFecha(fechaPrePlanificacionHasta);
          boolFechaPrePlanificacionHasta = true;
      }

      if (boolFechaPrePlanificacionDesde || boolFechaPrePlanificacionHasta)
      {
          if (boolFechaPrePlanificacionDesde && boolFechaPrePlanificacionHasta)
          {
              var aFecha1 = fechaPrePlanificacionDesde.split('-');
              var aFecha2 = fechaPrePlanificacionHasta.split('-');
              var fFecha1 = aFecha1[0] + '-' + (aFecha1[1] - 1) + '-' + aFecha1[2];
              var fFecha2 = aFecha2[0] + '-' + (aFecha2[1] - 1) + '-' + aFecha2[2];
              var rangoFechaCreacion = Utils.restaFechas(fFecha1, fFecha2);
              if (rangoFechaCreacion > 31)
              {
                 Ext.Msg.show({
                        title: 'Error en B\u00fasqueda',
                        msg: 'Rango de fechas excede el  limite permitido (31 dias)  ',
                        buttons: Ext.Msg.OK,
                        animEl: 'elId',
                        icon: Ext.MessageBox.ERROR
                        });
                  return false;
              }
          }
          else
          {
              Ext.Msg.show({
                        title: 'Error en B\u00fasqueda',
                        msg: 'Debe elegir un rango de Fechas de Creación válido ',
                        buttons: Ext.Msg.OK,
                        animEl: 'elId',
                        icon: Ext.MessageBox.ERROR
                        });
              return false;
          }
      }
      Ext.MessageBox.wait('Generando Reporte. Favor espere..');
      Ext.Ajax.request(
          {
              timeout: 9999999999,
              url: urlGenerarRptAprobContratos,
              type: 'json' ,
              params:
                  {
                      fechaPrePlanificacionDesde: Ext.getCmp('fechaPrePlanificacionDesde').getValue(),
                      fechaPrePlanificacionHasta: Ext.getCmp('fechaPrePlanificacionHasta').getValue(),
                      cbxIdPtoCobertura: Ext.getCmp('cbxIdPtoCobertura').getValue().toString()
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
