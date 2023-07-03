/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.onReady(function() {  
    Ext.tip.QuickTipManager.init();
            
    //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		
    DTFechaDesdePlanif = new Ext.form.DateField({
        id: 'fechaDesdePlanif',
        fieldLabel: 'Desde',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:325,
        editable: false
        //anchor : '65%',
        //layout: 'anchor'
    });
    DTFechaHastaPlanif = new Ext.form.DateField({
        id: 'fechaHastaPlanif',
        fieldLabel: 'Hasta',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:325,
        editable: false
        //anchor : '65%',
        //layout: 'anchor'
    });	
    DTFechaDesdeIngOrd = new Ext.form.DateField({
        id: 'fechaDesdeIngOrd',
        fieldLabel: 'Desde',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:325,
        editable: false
        //anchor : '65%',
        //layout: 'anchor'
    });
    DTFechaHastaIngOrd = new Ext.form.DateField({
        id: 'fechaHastaIngOrd',
        fieldLabel: 'Hasta',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:325,
        editable: false
        //anchor : '65%',
        //layout: 'anchor'
    });
    
    store = new Ext.data.Store({ 
        pageSize: 13,
        total: 'total',
        proxy: {
            timeout: 60000,
            type: 'ajax',
            url : 'grid',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                fechaDesdePlanif: '',
                fechaHastaPlanif: '',
                fechaDesdeIngOrd: '',
                fechaHastaIngOrd: '',
                login2: '',
                descripcionPunto: '',
                vendedor: '',
                ciudad: '',
                numOrdenServicio: '',
                estado: 'Todos'
            }
        },
        fields:
                [
                    {name:'id_factibilidad', mapping:'id_factibilidad'},
                    {name:'id_servicio', mapping:'id_servicio'},
                    {name:'id_punto', mapping:'id_punto'},
                    {name:'id_orden_trabajo', mapping:'id_orden_trabajo'},
                    {name:'cliente', mapping:'cliente'},
		            {name:'esRecontratacion', mapping:'esRecontratacion'},
                    {name:'vendedor', mapping:'vendedor'},
                    {name:'valor', mapping:'valor'},
                    {name:'descripcionFormaContacto', mapping:'descripcionFormaContacto'},
                    {name:'usr_vendedor', mapping:'usr_vendedor'},

                    {name:'nombreAsistente', mapping:'nombreAsistente'},
                    {name:'valorAsistente', mapping:'valorAsistente'},
                    {name:'descripcionFormaContactoAsistente', mapping:'descripcionFormaContactoAsistente'},
                    {name:'usr_asistente', mapping:'usr_asistente'},

                    {name:'login2', mapping:'login2'},
					{name:'tercerizadora', mapping:'tercerizadora'},
                    {name:'producto', mapping:'producto'},
		            {name:'tipo_orden', mapping:'tipo_orden'},
                    {name:'coordenadas', mapping:'coordenadas'},
                    {name:'direccion', mapping:'direccion'},
                    {name:'ciudad', mapping:'ciudad'},
                    {name:'jurisdiccion', mapping:'jurisdiccion'},
                    {name:'valorMetraje', mapping:'valorMetraje'},
                    {name:'valorObraCivil', mapping:'valorObraCivil'},
                    {name:'valorOtrosMate', mapping:'valorOtrosMate'},
                    {name:'valorAsumeEmpresa', mapping:'valorAsumeEmpresa'},
                    {name:'valorAsumeCliente', mapping:'valorAsumeCliente'},
                    {name:'MRC', mapping:'MRC'},
                    {name:'NRC', mapping:'NRC'},
                    {name:'nombreSector', mapping:'nombreSector'},
                    {name:'fePlanificacion', mapping:'fePlanificacion'},
                    {name:'rutaCroquis', mapping:'rutaCroquis'},
                    {name:'latitud', mapping:'latitud'},
                    {name:'longitud', mapping:'longitud'},
                    {name:'estado', mapping:'estado'},
                    // {name:'action1', mapping:'action1'},
                    // {name:'action2', mapping:'action2'},
                    {name:'action3', mapping:'action3'},
                    {name:'action4', mapping:'action4'},
                    {name:'action5', mapping:'action5'},
                    {name:'action6', mapping:'action6'},
                    {name:'action7', mapping:'action8'},
                    {name:'action8', mapping:'action9'},
                    {name:'action9', mapping:'action7'},
                    {name:'action10', mapping:'action10'}
                ],
        autoLoad: true
    });

    var pluginExpanded = true;
    
    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    })

    
    grid = Ext.create('Ext.grid.Panel', {
        width: 1600,
        height: 500,
        store: store,
        loadMask: true,
        frame: false,       
        columns:[
                {
                  id: 'id_factibilidad',
                  header: 'IdFactibilidad',
                  dataIndex: 'id_factibilidad',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'id_servicio',
                  header: 'IdServicio',
                  dataIndex: 'id_servicio',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'id_punto',
                  header: 'IdPunto',
                  dataIndex: 'id_punto',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'id_orden_trabajo',
                  header: 'IdOrdenTrabajo',
                  dataIndex: 'id_orden_trabajo',
                  hidden: true,
                  hideable: false
                },
				{
                  id: 'tercerizadora',
                  header: 'tercerizadora',
                  dataIndex: 'tercerizadora',
                  hidden: true,
                  hideable: false
                },
                {
				  id: 'tipo_orden',
                  header: 'tipo_orden',
                  dataIndex: 'tipo_orden',
                  hidden: true,
                  hideable: false
                },                
                {
                  id: 'cliente',
                  header: 'Cliente',
                  dataIndex: 'cliente',
                  width: 150,
                  sortable: true
                },
                {
                  id: 'esRecontratacion',
                  header: 'esRecontratacion',
                  dataIndex: 'esRecontratacion',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'vendedor',
                  header: 'Vendedor',
                  dataIndex: 'vendedor',
                  width: 170,
                  sortable: true
                },
                {
                  id: 'login2',
                  header: 'Login',
                  dataIndex: 'login2',
                  width: 120,
                  sortable: true
                },
                {
                  id: 'producto',
                  header: 'Producto',
                  dataIndex: 'producto',
                  width: 140,
                  sortable: true
                },  
                {
                  id: 'jurisdiccion',
                  header: 'Jurisdiccion',
                  dataIndex: 'jurisdiccion',
                  width: 100,
                  sortable: true
                },   
                 {
                    id: 'MRC',
                    header: 'MRC',
                    dataIndex: 'MRC',                    
                    hidden : prefEmpresa == "TN" ? false : true,
                    width: 60,
                    sortable: true
                 },   
                  {
                    id: 'NRC',
                    header: 'NRC',
                    dataIndex: 'NRC',
                    hidden : prefEmpresa == "TN" ? false : true,
                    width: 60,
                    sortable: true
                  },   
                {
                    id: 'valorMetraje',
                    header: 'Precio Metraje',
                    dataIndex: 'valorMetraje',                    
                    hidden : prefEmpresa == "TN" ? false : true,
                    width: 80,
                    sortable: true
                 },   
                {
                    id: 'valorObraCivil',
                    header: 'Precio Obra Civil',
                    dataIndex: 'valorObraCivil',
                    hidden : prefEmpresa == "TN" ? false : true,
                    width: 100,
                    sortable: true
                },  
                {
                    id: 'valorOtrosMate',
                    header: 'Precio Otros Materiales',
                    dataIndex: 'valorOtrosMate',
                    hidden : prefEmpresa == "TN" ? false : true,
                    width: 130,
                    sortable: true
                },    
                {
                    id: 'valorAsumeEmpresa',
                    header: 'Precio Asume Empresa',
                    dataIndex: 'valorAsumeEmpresa',
                    hidden : prefEmpresa == "TN" ? false : true,
                    width: 140,
                    sortable: true
                },
                {
                  id: 'coordenadas',
                  header: 'Coordenadas',
                  dataIndex: 'coordenadas',
                  width: 120,
                  hidden: prefEmpresa == "TN" ? true : false,
                  sortable: true
                },
                {
                  id: 'direccion',
                  header: 'Direccion',
                  dataIndex: 'direccion',
                  width: 130,
                  sortable: true
                },   
                {
                  id: 'nombreSector',
                  header: 'Sector',
                  dataIndex: 'nombreSector',
                  hidden: prefEmpresa == "TN" ? true : false,                
                  width: 90,
                  sortable: true
                },  
                {
                  id: 'fePlanificacion',
                  header: 'Fecha Solicita Aprobacion',
                  dataIndex: 'fePlanificacion',
                  width: 140,
                  sortable: true
                },
                {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: 200,
                    items: [                       
                        {
                            getClass: function(v, meta, rec) {return rec.get('action4')},
                            tooltip: 'Rechazar Solicitud',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                showRechazarOrden_Factibilidad(rec);
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {return rec.get('action5')},
                            tooltip: 'Aprobar Solicitud',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                showFactibilidadMateriales(rec);
                            }
                        },
                        // Ver Seguimiento
                        {
                        getClass: function(v, meta, rec)
                        {
                                this.items[2].tooltip = 'Ver Seguimiento';

                            return rec.get('action6');
                        },
                        handler: function(grid, rowIndex, colIndex)
                        {
                            var rec = store.getAt(rowIndex);

                                verSeguimientoTarea(rec.data.id_factibilidad, rec.data.id_punto, rec.data.login2, rec.data.usr_vendedor, rec.data.producto, prefEmpresa);

                        }
                        },
                        // Cargar Archivo
                        {
                        getClass: function(v, meta, rec) {
                                this.items[3].tooltip = 'Cargar Archivo';

                            return rec.get('action7');
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);
                        
                            subirMultipleAdjuntosMateriales(rec.data.id_factibilidad,
                                rec.data.id_servicio);
                        }
                        },
                        // Ver Archivos
                        {
                            getClass: function(v, meta, rec)
                            {
                                this.items[4].tooltip =  'Ver Archivos'; 
                        
                                return rec.get('action8')
                            },
                            handler: function(grid, rowIndex, colIndex)
                            {
                            var rec = store.getAt(rowIndex);

                            presentarDocumentosMaterialesExcedentes(rec);
                               
                            }
                        }
                    ]
                }
            ],
            bbar: Ext.create('Ext.PagingToolbar', {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'grid',
         listeners: 
        {
            itemdblclick: function(view, record, item, index, eventobj, obj) {
                var position = view.getPositionByEvent(eventobj),
                    data = record.data,
                    value = data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title: 'Copiar texto?',
                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    buttons: Ext.Msg.OK,
                    icon: Ext.Msg.INFORMATION
                });
            },
            viewready: function (grid)
            {
                var view = grid.view;

                grid.mon(view,
                {
                    uievent: function (type, view, cell, recordIndex, cellIndex, e)
                    {
                        grid.cellIndex   = cellIndex;
                        grid.recordIndex = recordIndex;
                    }
                });

                grid.tip = Ext.create('Ext.tip.ToolTip',
                {
                    target: view.el,
                    delegate: '.x-grid-cell',
                    trackMouse: true,
                    autoHide: false,
                    renderTo: Ext.getBody(),
                    listeners:
                    {
                        beforeshow: function(tip)
                        {
                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1)
                            {
                                header = grid.headerCt.getGridColumns()[grid.cellIndex];

                                if( header.dataIndex != null )
                                {
                                    var trigger         = tip.triggerElement,
                                        parent          = tip.triggerElement.parentElement,                                        
                                        columnDataIndex = view.getHeaderByCell(trigger).dataIndex;

                                    if( view.getRecord(parent).get(columnDataIndex) != null )
                                    {
                                        var columnText      = view.getRecord(parent).get(columnDataIndex).toString();

                                        if (columnText)
                                        {
                                            tip.update(columnText);
                                        }
                                        else
                                        {
                                            return false;
                                        }
                                    }
                                    else
                                    {
                                        return false;
                                    }
                                }
                                else
                                {
                                    return false;
                                }
                            }     
                        }
                    }
                });

                grid.tip.on('show', function()
                {
                    var timeout;
                    
                    grid.tip.getEl().on('mouseout', function()
                    {
                        timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                    });

                    grid.tip.getEl().on('mouseover', function(){window.clearTimeout(timeout);});

                    Ext.get(view.el).on('mouseover', function(){window.clearTimeout(timeout);});

                    Ext.get(view.el).on('mouseout', function()
                    {
                        timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                    });
                });
            }
        }
    });
    
    /* ******************************************* */
                /* FILTROS DE BUSQUEDA */
    /* ******************************************* */
    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,  // Don't want content to crunch against the borders        
        border:false,       
        buttonAlign: 'center',
        layout:{
            type:'table',
            columns: 5,
            align: 'left'
        },
        bodyStyle: {
                    background: '#fff'
                },                     

        collapsible : true,
        collapsed: true,
        width: 1600,
        title: 'Criterios de busqueda',
            buttons: [
                {
                    text: 'Buscar',
                    iconCls: "icon_search",
                    handler: function(){ buscar();}
                },
                {
                    text: 'Limpiar',
                    iconCls: "icon_limpiar",
                    handler: function(){ limpiar();}
                }

                ],                
                items: 
                [              
                    {html:"&nbsp;",border:false,width:200},
                    {html:"Fecha Solicita Planificacion:",border:false,width:325},
                    {html:"&nbsp;",border:false,width:150},
                    {html:"&nbsp;",border:false,width:200},

                    {html:"&nbsp;",border:false,width:200},
                    
                   
                    {html:"&nbsp;",border:false,width:200},
                    DTFechaDesdePlanif,
                    {html:"&nbsp;",border:false,width:150},
                    {html:"&nbsp;",border:false,width:200},
                    {html:"&nbsp;",border:false,width:200},
                    
                    {html:"&nbsp;",border:false,width:200},
                    DTFechaHastaPlanif,
                    {html:"&nbsp;",border:false,width:150},
                    {html:"&nbsp;",border:false,width:150},
                    {html:"&nbsp;",border:false,width:200},
                    
                
                    {html:"&nbsp;",border:false,width:200},
                    {
                        xtype: 'textfield',
                        id: 'txtLogin',
                        fieldLabel: 'Login',
                        value: '',
                        width: '325'
                    },
                    {html:"&nbsp;",border:false,width:150},
                    {
                        xtype: 'textfield',
                        id: 'txtDescripcionPunto',
                        fieldLabel: 'Descripcion Punto',
                        value: '',
                        width: '325'
                    },
                    {html:"&nbsp;",border:false,width:200},
                    
                    {html:"&nbsp;",border:false,width:200},
                    {
                        xtype: 'textfield',
                        id: 'txtVendedor',
                        fieldLabel: 'Vendedor',
                        value: '',
                        width: '325'
                    },
                    {html:"&nbsp;",border:false,width:150},
                    {
                        xtype: 'textfield',
                        id: 'txtCiudad',
                        fieldLabel: 'Ciudad',
                        value: '',
                        width: '325'
                    },
                    {html:"&nbsp;",border:false,width:200},
                    
                    {html:"&nbsp;",border:false,width:200},                   
                    {html:"&nbsp;",border:false,width:150},
                    {html:"&nbsp;",border:false,width:525},
                    {html:"&nbsp;",border:false,width:200}
                    
                ],	
        renderTo: 'filtro'
    }); 
    
});


/* ******************************************* */
            /*  FUNCIONES  */
/* ******************************************* */

function buscar(){
    var boolError = false;
    
    if(( Ext.getCmp('fechaDesdePlanif').getValue()!=null)&&(Ext.getCmp('fechaHastaPlanif').getValue()!=null) )
    {
        if(Ext.getCmp('fechaDesdePlanif').getValue() > Ext.getCmp('fechaHastaPlanif').getValue())
        {
            boolError = true;
            
            Ext.Msg.show({
                title:'Error en Busqueda',
                msg: 'Por Favor para realizar la busqueda Fecha Desde Planificacion debe ser fecha menor a Fecha Hasta Planificacion.',
                buttons: Ext.Msg.OK,
                animEl: 'elId',
                icon: Ext.MessageBox.ERROR
            });	
        }
    }        
    
    if(!boolError)
    {
        store.getProxy().extraParams.fechaDesdePlanif = Ext.getCmp('fechaDesdePlanif').value;
        store.getProxy().extraParams.fechaHastaPlanif = Ext.getCmp('fechaHastaPlanif').value;       
        store.getProxy().extraParams.login2 = Ext.getCmp('txtLogin').value;
        store.getProxy().extraParams.descripcionPunto = Ext.getCmp('txtDescripcionPunto').value;
        store.getProxy().extraParams.vendedor = Ext.getCmp('txtVendedor').value;
        store.getProxy().extraParams.ciudad = Ext.getCmp('txtCiudad').value;       
        store.load();
    }          
}

function limpiar(){
    Ext.getCmp('fechaDesdePlanif').setRawValue("");
    Ext.getCmp('fechaHastaPlanif').setRawValue("");    
    
    Ext.getCmp('fechaDesdePlanif').setValue("");
    Ext.getCmp('fechaHastaPlanif').setValue("");    
    
    Ext.getCmp('txtLogin').value="";
    Ext.getCmp('txtLogin').setRawValue("");
    
    Ext.getCmp('txtDescripcionPunto').value="";
    Ext.getCmp('txtDescripcionPunto').setRawValue("");
    
    Ext.getCmp('txtVendedor').value="";
    Ext.getCmp('txtVendedor').setRawValue("");
    
    Ext.getCmp('txtCiudad').value="";
    Ext.getCmp('txtCiudad').setRawValue("");        
    
    store.getProxy().extraParams.fechaDesdePlanif = Ext.getCmp('fechaDesdePlanif').value;
    store.getProxy().extraParams.fechaHastaPlanif = Ext.getCmp('fechaHastaPlanif').value;
    store.getProxy().extraParams.login2 = Ext.getCmp('txtLogin').value;
    store.getProxy().extraParams.descripcionPunto = Ext.getCmp('txtDescripcionPunto').value;
    store.getProxy().extraParams.vendedor = Ext.getCmp('txtVendedor').value;
    store.getProxy().extraParams.ciudad = Ext.getCmp('txtCiudad').value;    
    store.load();
}

function verSeguimientoTarea(idDetalleSolicitud, IdPunto, Login, Vendedor, Producto, Empresa){ //IdFactibilidad, IdPunto, Login, Vendedor, Producto, Empresa
  
  
    var conn = new Ext.data.Connection({
      listeners: {
          'beforerequest': {
              fn: function (con, opt) {
                  Ext.get(document.body).mask('Loading...');
              },
              scope: this
          },
          'requestcomplete': {
              fn: function (con, res, opt) {
                  Ext.get(document.body).unmask();
              },
              scope: this
          },
          'requestexception': {
              fn: function (con, res, opt) {
                  Ext.get(document.body).unmask();
              },
              scope: this
          }
      }
  });
 
  btncancelar = Ext.create('Ext.Button', {
          text: 'Cerrar',
          cls: 'x-btn-rigth',
          handler: function() {
            winSeguimientoTarea.destroy();													
          }
  });
  
  storeSeguimientoTarea = new Ext.data.Store({ 
      total: 'total',
      async: false,
      autoLoad: true,
      proxy: {
          type: 'ajax',
          url : strUrlGetSeguimientoMaterialesExcedentes,
          reader: {
              type: 'json'              
          },
          extraParams: {
            idDetalleSolicitud: idDetalleSolicitud,
            pantallaDe: 'Autorizaciones'			
        }
      },
      fields:
      [
            {name:'id', mapping:'id'},
            {name:'observacion', mapping:'observacion'},
            {name:'estado', mapping:'estado'},
            {name:'usrCreacion', mapping:'usrCreacion'},
            {name:'feCreacion', mapping:'feCreacion'}					
      ]
  });
  gridSeguimiento = Ext.create('Ext.grid.Panel', {
      id:'gridSeguimiento',
      store: storeSeguimientoTarea,		
      columnLines: true,
      columns: [
          {
                id: 'observacion',
                header: 'Observacion',
                dataIndex: 'observacion',
                width:400,
                sortable: true						 
          },
            {
                id: 'estado',
                header: 'Estado',
                dataIndex: 'estado',
                width:80,
                sortable: true						 
          },
            {
                id: 'usrCreacion',
                header: 'User Creación',
                dataIndex: 'usrCreacion',
                width:80,
                sortable: true						 
          },
            {
                id: 'feCreacion',
                header: 'Fecha Creación',
                dataIndex: 'feCreacion',
                width:120,
                sortable: true						 
          }

      ],
      width: 700,
      height: 175,
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
                          },
                          viewready: function (grid) {
                              var view = grid.view;

                              // record the current cellIndex
                              grid.mon(view, {
                                  uievent: function (type, view, cell, recordIndex, cellIndex, e) {
                                      grid.cellIndex = cellIndex;
                                      grid.recordIndex = recordIndex;
                                  }
                              });

                              grid.tip = Ext.create('Ext.tip.ToolTip', {
                                  target: view.el,
                                  delegate: '.x-grid-cell',
                                  trackMouse: true,
                                  renderTo: Ext.getBody(),
                                  listeners: {
                                      beforeshow: function updateTipBody(tip) {
                                          if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1) {
                                              header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                              tip.update(grid.getStore().getAt(grid.recordIndex).get(header.dataIndex));
                                          }
                                     }
                                  }
                              });

                          }                                    
                  }
       

  });

  
  btnguardar3 = Ext.create('Ext.Button', {        
          text: 'Guardar',
          cls: 'x-btn-rigth',
          handler: function() {
              var valorSeguimiento = Ext.getCmp('seguimiento').value;
              winSeguimientoTarea.destroy();
              conn.request({
                  method: 'POST',
                  params :{
                      id_factibilidad: idDetalleSolicitud,
                      id_punto: IdPunto,
                      login: Login,
                      vendedor: Vendedor,
                      producto: Producto,
                      empresa: Empresa,
                      pantallaDe: 'Autorizaciones',
                      seguimiento: valorSeguimiento
                  },
                  url: strUrlIngresarSeguimientoMaterialesExcedentes,
                  success: function(response){
                      var json = Ext.JSON.decode(response.responseText);
                      if(json.mensaje != "cerrada")
                      {
                          Ext.Msg.alert('Mensaje','Se ingreso el seguimiento.', function(btn){
                              if(btn=='ok'){
                                  return;
                              }
                          });
                      }
                      else
                      {
                          Ext.Msg.alert('Alerta ',"La tarea se encuentra Cerrada, por favor consultela nuevamente");
                      }
                  },
                  failure: function(rec, op) {
                      var json = Ext.JSON.decode(op.response.responseText);
                      Ext.Msg.alert('Alerta ',json.mensaje);
                  }
          });
          }
  });
  

  btnaprobar = Ext.create('Ext.Button', {        
    text: 'Aprobar',
    cls: 'x-btn-rigth',
    handler: function() {
        var valorSeguimiento = Ext.getCmp('seguimiento').value;
        winSeguimientoTarea.destroy();
        conn.request({
            method: 'POST',
            params :{
                id: idDetalleSolicitud ,
                observacion: valorSeguimiento
            },
            url: strUrlAprobarSeguimientoMaterialesExcedentes,
            success: function(response){
                var json = Ext.JSON.decode(response.responseText);
                if(json.mensaje != "cerrada")
                {
                    Ext.Msg.alert('Mensaje','Se aprobó el seguimiento.', function(btn){
                        if(btn=='ok'){
                            return;
                        }
                    });
                }
                else
                {
                    Ext.Msg.alert('Alerta ',"El seguimiento se encuentra Cerrado, por favor consultela nuevamente");
                }
            },
            failure: function(rec, op) {
                var json = Ext.JSON.decode(op.response.responseText);
                Ext.Msg.alert('Alerta ',json.mensaje);
            }
        });
    }
});


btnrechazar = Ext.create('Ext.Button', {        
    text: 'Rechazar',
    cls: 'x-btn-rigth',
    handler: function() {
        var valorSeguimiento = Ext.getCmp('seguimiento').value;
        winSeguimientoTarea.destroy();
        conn.request({
            method: 'POST',
            params :{
                id: idDetalleSolicitud ,
                observacion: valorSeguimiento
            },
            url: strUrlRechazarSeguimientoMaterialesExcedentes,
            success: function(response){
                var json = Ext.JSON.decode(response.responseText);
                if(json.mensaje != "cerrada")
                {
                    Ext.Msg.alert('Mensaje','Se rechazó el seguimiento.', function(btn){
                        if(btn=='ok'){
                            return;
                        }
                    });
                }
                else
                {
                    Ext.Msg.alert('Alerta ',"El seguimiento se encuentra Cerrado, por favor consultela nuevamente");
                }
            },
            failure: function(rec, op) {
                var json = Ext.JSON.decode(op.response.responseText);
                Ext.Msg.alert('Alerta ',json.mensaje);
            }
    });
    }
});




  btncancelar3 = Ext.create('Ext.Button', {
          text: 'Cerrar',
          cls: 'x-btn-rigth',
          handler: function() {
              winSeguimientoTarea.destroy();
          }
  });

  formPanel3 = Ext.create('Ext.form.Panel', {
    waitMsgTarget: true,
    height: 140,
    width: 700,
    layout: 'fit',
    fieldDefaults: {
        labelAlign: 'left',
        labelWidth: 140,
        msgTarget: 'side'
    },

    items: [{
        xtype: 'fieldset',
        title: 'Información',
        defaultType: 'textfield',
        items: [
            {
                xtype: 'textarea',
                fieldLabel: 'Seguimiento:',
                id: 'seguimiento',
                name: 'seguimiento',
                rows: 4,
                cols: 70
            }
        ]
    }]
 });


  formPanelSeguimiento = Ext.create('Ext.form.Panel', {
          waitMsgTarget: true,
          height: 200,
          width:700,
          layout: 'fit',
          fieldDefaults: {
              labelAlign: 'left',
              msgTarget: 'side'
          },

          items: [{
              xtype: 'fieldset',				
              defaultType: 'textfield',
              items: [					
                  gridSeguimiento, formPanel3
              ]
          }]
  });


  winSeguimientoTarea = Ext.create('Ext.window.Window', {
          title: 'Historial Solicitud',
          modal: true,
          width: 750,
          height: 400,
          resizable: true,
          layout: 'fit',
          items: [formPanelSeguimiento],
          buttonAlign: 'center',
          buttons:[btnguardar3,btncancelar3]
  }).show();       
}



function subirMultipleAdjuntosMateriales(idDetalleSolicitud, idServicio)
{
    var id_tarea = idDetalleSolicitud;
    var id_servicio = idServicio;
    
    var panelMultiupload = Ext.create('widget.multiupload',{ fileslist: [] });
    var formPanel = Ext.create('Ext.form.Panel',
     {
        width: 500,
        frame: true,
        bodyPadding: '10 10 0',
        defaults: {
            anchor: '100%',
            allowBlank: false,
            msgTarget: 'side',
            labelWidth: 50
        },
        items: [panelMultiupload],
        buttons: [{
            text: 'Subir',
            handler: function()
            {
                var form = this.up('form').getForm();
                if(form.isValid())
                {
                    if(numArchivosSubidos>0)
                    {
                        form.submit({
                            url: url_multipleFileUpload,
                            params :{
                              idSolicitud    : id_tarea,
                              servicio     : id_servicio,
                              origenMateriales: 'S'
                            },
                            waitMsg: 'Procesando Archivo...',
                            success: function(fp, o)
                            {
                                Ext.Msg.alert("Mensaje", o.result.respuesta, function(btn){
                                    if(btn=='ok')
                                    {
                                        numArchivosSubidos=0;
                                        win.destroy();
                                          
                                    }
                                });
                            },
                            failure: function(fp, o) {
                              Ext.Msg.alert("Alerta",o.result.respuesta);
                            }
                        });
                    }
                    else
                    {
                        Ext.Msg.alert("Mensaje", "No existen archivos para subir", function(btn){
                            if(btn=='ok')
                            {
                                numArchivosSubidos=0;
                                win.destroy();
                            }
                        });
                    }
                    
                }
            }
        },
        {
            text: 'Cancelar',
            handler: function() {
                numArchivosSubidos=0;
                win.destroy();
            }
        }]
    });
    
    var win = Ext.create('Ext.window.Window', {
        title: 'Subir Archivos Tarea',
        modal: true,
        width: 500,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
    
}


function presentarDocumentosMaterialesExcedentes(rec)
{
    var id_factibilidad           = rec.data.id_factibilidad;
    var id_servicio           = rec.data.id_servicio;      
	
   storeDocumentosMateriales = new Ext.data.Store({ 
        pageSize: 1000,
        proxy: {
            type: 'ajax',
            url : url_documentosMaterialesExced,
            reader: {
                type : 'json'
            },
            extraParams: {
                idFactibilidad             : id_factibilidad,
                idServicio                 : id_servicio
            }
        },
        fields:
		[
            {name:'idDocumento',            mapping:'idDocumento'},
            {name:'ubicacionLogica',        mapping:'ubicacionLogica'},
            {name:'feCreacion',             mapping:'feCreacion'},
            {name:'usrCreacion',            mapping:'usrCreacion'},
            {name:'linkVerDocumento',       mapping:'linkVerDocumento'},
            {name:'boolEliminarDocumento',  mapping:'boolEliminarDocumento'}
		],
        autoLoad: true,
		listeners: {
			beforeload: function(sender, options )
			{
				Ext.MessageBox.show({
				   msg: 'Cargando los datos, Por favor espere!!',
				   progressText: 'Saving...',
				   width:300,
				   wait:true,
				   waitConfig: {interval:200}
				});
			},
			load: function(sender, node, records) {
				gridDocumentosCaso = "";
				
				if(storeDocumentosMateriales.getCount()>0){
					
                    //grid de documentos por Caso
                    gridDocumentosCaso = Ext.create('Ext.grid.Panel', {
                        id:'gridMaterialesPunto',
                        store: storeDocumentosMateriales,
                        columnLines: true,
                        columns: [{
                            header   : 'Nombre Archivo',
                            dataIndex: 'ubicacionLogica',
                            width    : 260
                        },
                        {
                            header   : 'Usr. Creación',
                            dataIndex: 'usrCreacion',
                            width    : 80
                        },
                        {
                            header   : 'Fecha de Carga',
                            dataIndex: 'feCreacion',
                            width    : 120
                        },
                        {
                            xtype: 'actioncolumn',
                            header: 'Acciones',
                            width: 90,
                            items:
                            [
                                {
                                    iconCls: 'button-grid-show',
                                    tooltip: 'Ver Archivo Digital',
                                    handler: function(grid, rowIndex, colIndex) {
                                        var rec         = storeDocumentosMateriales.getAt(rowIndex);
                                        verArchivoDigital(rec);
                                    }
                                },
                                {
                                    getClass: function(v, meta, rec) 
                                    {
                                        var strClassButton  = 'button-grid-delete';
                                        if(!rec.get('boolEliminarDocumento'))
                                        {
                                            strClassButton = ""; 
                                        }

                                        if (strClassButton == "")
                                        {
                                            this.items[0].tooltip = ''; 
                                        }   
                                        else
                                        {
                                            this.items[0].tooltip = 'Eliminar Archivo Digital';
                                        }
                                        return strClassButton;

                                    },
                                    tooltip: 'Eliminar Archivo Digital',
                                    handler: function(grid, rowIndex, colIndex) 
                                    {
                                        var rec                 = storeDocumentosMateriales.getAt(rowIndex);
                                        var idDocumento         = rec.get('idDocumento');
                                        var strClassButton      = 'button-grid-delete';
                                        if(!rec.get('boolEliminarDocumento'))
                                        {
                                            strClassButton = ""; 
                                        }

                                        if (strClassButton != "" )
                                        {
                                            eliminarAdjunto(storeDocumentosMateriales,idDocumento);
                                                
                                        } 
                                    }
                                }
                            ]
                        }
                    ],
                        viewConfig:{
                            stripeRows:true,
                            enableTextSelection: true
                        },
                        frame : true,
                        height: 200
                    });


                function verArchivoDigital(rec)
                {
                    var rutaFisica = rec.get('linkVerDocumento');
                    var posicion = rutaFisica.indexOf('/public')
                    window.open(rutaFisica.substring(posicion,rutaFisica.length));
                }

                var formPanel = Ext.create('Ext.form.Panel', {
                    bodyPadding  : 2,
                    waitMsgTarget: true,
                    fieldDefaults: {
                        labelAlign: 'left',
                        labelWidth: 85,
                        msgTarget : 'side'
                    },
                    items: [

                    {
                        xtype      : 'fieldset',
                        title      : '',
                        defaultType: 'textfield',

                        defaults: {
                            width: 550
                        },
                        items: [

                            gridDocumentosCaso

                        ]
                    }
                    ],
                    buttons: [{
                        text: 'Cerrar',
                        handler: function(){
                            win.destroy();
                        }
                    }]
                });

                var win = Ext.create('Ext.window.Window', {
                    title   : 'Documentos Cargados',
                    modal   : true,
                    width   : 580,
                    closable: true,
                    layout  : 'fit',
                    items   : [formPanel]
                }).show();                    
                
                Ext.MessageBox.hide();
				}//FIN IF TIENE DATA
				else
				{	
                    Ext.Msg.show({
                        title  :'Mensaje',
                        msg    : 'La tarea seleccionada no posee archivos adjuntos.',
                        buttons: Ext.Msg.OK,
                        animEl : 'elId',
                    });

					//$('#tr_error').css("display", "table-row");
					//$('#busqueda_error').html("Alerta: No existen registros para esta busqueda");
					//mostrarOcultarBusqueada(true);
				}
				
				//Ext.MessageBox.hide();
			}
		}
    });

}

function eliminarAdjunto(storeDocumentosCaso,idDocumento)
{
    Ext.Msg.confirm('Alerta','Se eliminará el archivo. Desea continuar?', function(btn)
    {
        if(btn=='yes')
        {
              Ext.MessageBox.wait("Eliminando Archivo...", 'Por favor espere'); 
              Ext.Ajax.request({
                url: url_eliminar_adjunto,
                method: 'post',
                params: { id:idDocumento },
                success: function(response)
                {
                    Ext.MessageBox.hide();
                    var json = Ext.JSON.decode(response.responseText);                                                                                                

                    if (json.status=="OK")
                    {
                        Ext.MessageBox.show({
                            title: "Información",
                            cls: 'msg_floating',
                            msg: json.message,
                            icon: Ext.MessageBox.INFO,
                            buttons: Ext.Msg.OK,
                            fn: function(buttonId) 
                            {
                                if (buttonId === "ok")
                                {
                                    storeDocumentosCaso.load();
                                }
                            }
                        });
                    }
                    else
                    {
                        Ext.Msg.show(
                        {
                           title: 'Error',
                           width: 300,
                           cls: 'msg_floating',
                           icon: Ext.MessageBox.ERROR,
                           msg: json.message
                        });
                    }
                  },
                  failure: function(response)
                  {
                    Ext.MessageBox.hide();
                    var json = Ext.JSON.decode(response.responseText);
                    Ext.Msg.show(
                    {
                       title: 'Error',
                       width: 300,
                       cls: 'msg_floating',
                       icon: Ext.MessageBox.ERROR,
                       msg: json.message
                    });
                  }
              });
        }
    });
}