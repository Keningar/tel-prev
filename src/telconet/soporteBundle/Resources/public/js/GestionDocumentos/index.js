/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.QuickTips.init();
Ext.onReady(function() {

    store = new Ext.data.Store({
        pageSize : 15,
        total    : 'total',
        proxy:
        {
            timeout : 400000,
            type    : 'ajax',
            url     : 'grid',
            reader:
            {
                totalProperty : 'total',
                type          : 'json',
                root          : 'encontrados'
            },
            extraParams:
            {
                modulo : 'SOPORTE',
                estado : 'Todos'
            }
        },
        fields:
        [
            {name:'id'              , mapping:'id'},
            {name:'nombre'          , mapping:'nombre'},
            {name:'modulo'          , mapping:'modulo'},
            {name:'estado'          , mapping:'estado'},
            {name:'feCreacion'      , mapping:'feCreacion'},
            {name:'extension'       , mapping:'extension'},
            {name:'tipoDocumento'   , mapping:'tipoDocumento'},
            {name:'punto'           , mapping:'punto'},
            {name:'feCreacion'      , mapping:'feCreacion'},
            {name:'tipoElemento'    , mapping:'tipoElemento'},
            {name:'modeloElemento'  , mapping:'modeloElemento'},
            {name:'elemento'        , mapping:'elemento'},
            {name:'ubicacionLogica' , mapping:'ubicacionLogica'},
            {name:'ubicacionFisica' , mapping:'ubicacionFisica'},
            {name:'action1'         , mapping:'action1'},
            {name:'action2'         , mapping:'action2'},
            {name:'action3'         , mapping:'action3'},
            {name:'action4'         , mapping:'action4'}
        ]
    });

    var pluginExpanded = true;

    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    })

    grid = Ext.create('Ext.grid.Panel', {
        width      : 1015,
        height     : 550,
        store      : store,
        selModel   : sm,
        renderTo   : grid,
        loadMask   : true,
        frame      : false,
        viewConfig :
        {
            enableTextSelection : true
        },
        dockedItems:
        [
            {
                xtype : 'toolbar',
                dock  : 'top',
                align : '->',
                items :
                [
                    //tbfill -> alinea los items siguientes a la derecha
                    { xtype: 'tbfill' },
                    {
                        iconCls : 'icon_delete',
                        text    : 'Eliminar',
                        itemId  : 'delete',
                        scope   : this,
                        handler : function(){eliminarAlgunos();}
                    }
                ]
            }
        ],
        columns:
        [
            {
                id        : 'id',
                header    : 'id',
                dataIndex : 'id',
                hidden    :  true,
                hideable  :  false
            },
            {
                id        : 'nombre',
                header    : 'Nombre Documento',
                dataIndex : 'nombre',
                width     :  180,
                sortable  :  true
            },
            {
                id        : 'tipoDocumento',
                header    : 'Tipo Documento',
                dataIndex : 'tipoDocumento',
                width     :  110,
                sortable  :  true
            },
            {
                id        : 'extension',
                header    : 'Extensión',
                dataIndex : 'extension',
                width     :  70,
                sortable  :  true
            },
            {
                id        : 'ubicacionLogica',
                header    : 'Archivo',
                dataIndex : 'ubicacionLogica',
                width     :  150,
                sortable  :  true
            },
            {
                id        : 'punto',
                header    : 'Login',
                dataIndex : 'punto',
                width     :  130,
                sortable  :  true
            },
            {
                id        : 'modulo',
                dataIndex : 'modulo',
                header    : 'Módulo',
                align     : 'center',
                width     :  80,
                sortable  :  true
            },
            {
                id        : 'feCreacion',
                header    : 'Fec. Creación',
                dataIndex : 'feCreacion',
                align     : 'center',
                width     :  90,
                sortable  :  true
            },
            {
                id        : 'estado',
                header    : 'Estado',
                dataIndex : 'estado',
                align     : 'center',
                width     :  70,
                sortable  :  true
            },
            {
                xtype  : 'actioncolumn',
                header : 'Acciones',
                align  : 'center',
                width  :  150,
                items  :
                [
                  {
                    getClass: function(v, meta, rec) 
                    {

                        if (rec.get('action1') == "icon-invisible") 
                        this.items[0].tooltip = '';
                        else 
                        this.items[0].tooltip = 'Ver Documento';

                        return rec.get('action1')
                    },
                    tooltip: 'Ver',
                    handler: function(grid, rowIndex, colIndex) 
                    {
                        var rec = store.getAt(rowIndex);
                        if(rec.get('action1')!="icon-invisible") {
                            window.location = rec.get('id')+"/soporte/show";
                        } else {
                            Ext.Msg.alert('Error ','No tiene permisos para realizar esta acción');
                        }
                    }
                  },
                  {
                    getClass: function(v, meta, rec)
                    {
                        if (rec.get('action2') == "icon-invisible")
                            this.items[1].tooltip = '';
                        else 
                            this.items[1].tooltip = 'Editar';
                        return rec.get('action2')
                    },
                    handler: function(grid, rowIndex, colIndex) 
                    {
                        var rec = store.getAt(rowIndex);
                        if(rec.get('action2')!="icon-invisible")
                            window.location = rec.get('id')+"/edit";
                        else
                            Ext.Msg.alert('Error ','No tiene permisos para realizar esta acción');
                    }
                  },
                  {
                    getClass: function(v, meta, rec) 
                    {
                        if (rec.get('action4') == "icon-invisible") 
                            this.items[2].tooltip = '';
                        else 
                            this.items[2].tooltip = 'Descargar';
                        return rec.get('action4')
                    },
                    handler: function(grid, rowIndex, colIndex) 
                    {
                        var rec = store.getAt(rowIndex);
                        if(rec.get('action4')!="icon-invisible")
                            window.location = rec.get('id')+"/descargarDocumento";
                        else
                            Ext.Msg.alert('Error ','No tiene permisos para realizar esta acción');
                    }
                  },
                  {
                      getClass: function(v, meta, rec) 
                      {
                          if (rec.get('action3') == "icon-invisible")
                              this.items[3].tooltip = '';
                          else
                              this.items[3].tooltip = 'Eliminar';
                          return rec.get('action3')
                      },
                      handler: function(grid, rowIndex, colIndex) 
                      {
                          var rec = store.getAt(rowIndex);

                          if(rec.get('action3')!="icon-invisible") {

                              Ext.Msg.confirm('Alerta','Se eliminara el registro. Desea continuar?', function(btn){
                                  if(btn=='yes') {
                                      Ext.Ajax.request({
                                          url     : "deleteAjax",
                                          method  : 'post',
                                          params  : { param : rec.get('id')},
                                          success : function(response){
                                              var text = response.responseText;
                                              store.load();
                                          },
                                          failure: function(result) {
                                              Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                          }
                                      });
                                  }
                              });
                          }
                          else Ext.Msg.alert('Error ','No tiene permisos para realizar esta acción');
                      }
                  }
              ]
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar', {
            store       : store,
            displayInfo : true,
            displayMsg  : 'Mostrando {0} - {1} de {2}',
            emptyMsg    : "No hay datos que mostrar."
        }),
        listeners:{
            itemdblclick: function(view,record,item,index,eventobj,obj) {
                var position = view.getPositionByEvent(eventobj),
                data  = record.data,
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
                    target     : view.el,
                    delegate   : '.x-grid-cell',
                    trackMouse : true,
                    renderTo   : Ext.getBody(),
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

    //****************************************************************
    //                    Combos para Filtros de Busqueda
    //****************************************************************


    //Campo Fecha Desde General
    var dateFechaDesde = new Ext.form.DateField ({
        xtype          : 'datefield',
        id             : 'dateFechaDesde',
        name           : 'dateFechaDesde',
        fieldLabel     : 'Fecha Desde',
        labelAlign     : 'left',
        format         : 'd-m-Y',
        width          : 400,
        editable       : false,
        maxValue       : new Date(),
        listeners: {
            'select' : {
                fn: function(field, date) {
                    Ext.getCmp('dateFechaHasta').setMinValue(date);
                    if (!Ext.isEmpty( Ext.getCmp('dateFechaHasta').getValue()) &&
                        date.getTime() > Ext.getCmp('dateFechaHasta').getValue().getTime()) {
                        Ext.getCmp('dateFechaHasta').setValue(date);
                    }
                }
            }
        }
    });

    //Campo Fecha Hasta General
    var dateFechaHasta = new Ext.form.DateField ({
        xtype          : 'datefield',
        id             : 'dateFechaHasta',
        name           : 'dateFechaHasta',
        fieldLabel     : 'Fecha Hasta',
        labelAlign     : 'left',
        format         : 'd-m-Y',
        width          : 400,
        editable       : false,
        maxValue       : new Date()
    });

    //-------------- CLIENTES -------------------

    storeClientes = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type : 'ajax',
            url  : '/soporte/tareas/getClientes',
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                    nombre : '',
                    estado : 'Activo'
                }
        },
        fields:
        [
            {name:'id_cliente', mapping:'id_cliente'},
            {name:'cliente'   , mapping:'cliente'}
        ],
        autoLoad: false
    });

    comboCliente = new Ext.form.ComboBox({
        id           : 'cmb_cliente',
        name         : 'cmb_cliente',
        store        :  storeClientes,
        displayField : 'cliente',
        valueField   : 'id_cliente',
        fieldLabel   : 'Login',
        height       : 30,
        width        : 400,
        border       : 0,
        margin       : 0,
        emptyText    : '',
        disabled     : false,
        triggerAction: 'all'
    });

    //-------------- TIPO DOCUMENTO GENERAL -------------------

    storeTipoDocumentoGeneral = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'getTipoDocumentoGeneral',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                    nombre: '',
                    estado: 'Activo'
                }
        },
        fields:
        [
            {name:'idTipo', mapping:'idTipo'},
            {name:'descripcionTipoDocumento', mapping:'descripcionTipoDocumento'}
        ],
        autoLoad: false
    });

    comboTipoDocumentoGeneral = new Ext.form.ComboBox({
        id: 'cmb_tipoDocumentoGeneral',
        name: 'cmb_tipoDocumentoGeneral',        	
        fieldLabel: 'Tipo Documento',
        store: storeTipoDocumentoGeneral,
        displayField: 'descripcionTipoDocumento',
        valueField: 'idTipo',
        height:30,
        width: 400,
        border:0,
        margin:0,
        queryMode: "remote",
        emptyText: ''
    });

     //-------------- TIPO DOCUMENTO ( EXTENSION ) -------------------

    storeTipoDocumento = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'getTipoDocumento',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                    nombre: '',
                    estado: 'Activo'
                }
        },
        fields:
        [
            {name:'idTipo', mapping:'idTipo'},
            {name:'extensionTipoDocumento', mapping:'extensionTipoDocumento'}
        ],
        autoLoad: false
    });

    comboTipoDocumento = new Ext.form.ComboBox({
        id           : 'cmb_tipoDocumento',
        name         : 'cmb_tipoDocumento',
        fieldLabel   : 'Extensión',
        store        : storeTipoDocumento,
        displayField : 'extensionTipoDocumento',
        valueField   : 'idTipo',
        height       : 30,
        width        : 400,
        border       : 0,
        margin       : 0,
        queryMode    : "remote",
        emptyText    : ''
    });

    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding : 7,  // Don't want content to crunch against the borders        
        border      : false,
        buttonAlign : 'center',
        layout:
        {
            type    :'table',
            columns : 5
        },
        bodyStyle:
        {
            background: '#fff'
        },
        collapsible : true,
        collapsed   : false,
        width       : 1015,
        title       : 'Criterios de busqueda',
        buttons:
        [
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
            {html:"&nbsp;",border:false,width:50},
            dateFechaDesde,
            {html:"&nbsp;",border:false,width:80},
            dateFechaHasta,
            {html:"&nbsp;",border:false,width:50},
            {html:"&nbsp;",border:false,width:50},
            {
                xtype: 'textfield',
                id: 'txt_nombre',
                name: 'txt_nombre',
                fieldLabel: 'Nombre',
                value: '',
                width: 400
            },
            {html:"&nbsp;",border:false,width:80},
            comboTipoDocumentoGeneral,
            {html:"&nbsp;",border:false,width:50},

            //----------------------------------//

            {html:"&nbsp;",border:false,width:50},
            comboTipoDocumento,
            {html:"&nbsp;",border:false,width:80},
            {
                xtype      : 'combobox',
                id         : 'cmb_modulo',
                fieldLabel : 'Módulo',
                value      : 'SOPORTE',
                width      :  400,
                store      :
                [
                    ['TODOS'      , 'TODOS'],
                    ['COMERCIAL'  , 'COMERCIAL'],
                    ['TECNICO'    , 'TECNICO'],
                    ['FINANCIERO' , 'FINANCIERO'],
                    ['SOPORTE'    , 'SOPORTE']
                ]
            },
            {html:"&nbsp;",border:false,width:50},

            //----------------------------------//

            {html:"&nbsp;",border:false,width:50},
            comboCliente,
            {html:"&nbsp;",border:false,width:80},
            {
                xtype      : 'combobox',
                fieldLabel : 'Estado',
                id         : 'cmb_estado',
                value      : 'Activo',
                store : [
                    ['Todos'      , 'Todos'],
                    ['Activo'     , 'Activo'],
                    ['Modificado' , 'Modificado'],
                    ['Eliminado'  , 'Eliminado']
                ],
                width: 400
            },
            {html:"&nbsp;",border:false,width:50},	

            //----------------------------------//

            {html:"&nbsp;",border:false,width:50},		
            {
                xtype      : 'textfield',
                id         : 'txt_tareaCaso',
                name       : 'txt_tareaCaso',
                fieldLabel : 'Número Tarea/Caso',
                value      : '',
                width      : 400
            },
            {html:"&nbsp;",border:false,width:80},
            {
                xtype: 'textfield',
                id: 'txt_encuesta',
                name: 'txt_encuesta',
                fieldLabel: 'Codigo Encuesta',
                value: '',				
                width: 400
            },
            {html:"&nbsp;",border:false,width:50},	
        ],	
        renderTo: 'filtro'
    }); 

    Ext.getCmp('txt_encuesta').setDisabled(true);

    });

    function buscar()
    {
    store.proxy.extraParams =
    {
        tipoDocumento: Ext.getCmp('cmb_tipoDocumentoGeneral').value ? Ext.getCmp('cmb_tipoDocumentoGeneral').value : '',
        extensionDoc : Ext.getCmp('cmb_tipoDocumento').value ? Ext.getCmp('cmb_tipoDocumento').value : '',
        nombre       : Ext.getCmp('txt_nombre').value        ? Ext.getCmp('txt_nombre').value        : '',
        casoTarea    : Ext.getCmp('txt_tareaCaso').value     ? Ext.getCmp('txt_tareaCaso').value     : '',
        modulo       : Ext.getCmp('cmb_modulo').value        ? Ext.getCmp('cmb_modulo').value        : '',
        login        : Ext.getCmp('cmb_cliente').value       ? Ext.getCmp('cmb_cliente').value       : '',
        estado       : Ext.getCmp('cmb_estado').value        ? Ext.getCmp('cmb_estado').value        : '',
        encuesta     : Ext.getCmp('txt_encuesta').value      ? Ext.getCmp('txt_encuesta').value      : '',
        fechaDesde   : Ext.getCmp('dateFechaDesde').value    ? Ext.getCmp('dateFechaDesde').value    : '',
        fechaHasta   : Ext.getCmp('dateFechaHasta').value    ? Ext.getCmp('dateFechaHasta').value    : ''
    };
    store.load();
    }

    function limpiar() {
    Ext.getCmp('dateFechaDesde').setValue(null);
    Ext.getCmp('dateFechaHasta').setValue(null);
    Ext.getCmp('dateFechaHasta').setMinValue(null);
    Ext.getCmp('txt_nombre').value = "";
    Ext.getCmp('txt_nombre').setRawValue("");
    Ext.getCmp('txt_tareaCaso').value = "";
    Ext.getCmp('txt_tareaCaso').setRawValue("");
    Ext.getCmp('cmb_tipoDocumentoGeneral').value= "";
    Ext.getCmp('cmb_tipoDocumentoGeneral').setRawValue("");
    Ext.getCmp('cmb_tipoDocumento').value = "";
    Ext.getCmp('cmb_tipoDocumento').setRawValue("");
    Ext.getCmp('cmb_modulo').value = "SOPORTE";
    Ext.getCmp('cmb_modulo').setRawValue("SOPORTE");
    Ext.getCmp('cmb_cliente').value = "";
    Ext.getCmp('cmb_cliente').setRawValue("");
    Ext.getCmp('cmb_estado').value = "Activo";
    Ext.getCmp('cmb_estado').setRawValue("Activo");
    Ext.getCmp('txt_encuesta').value = "";
    Ext.getCmp('txt_encuesta').setRawValue("");
    Ext.getCmp('cmb_cliente').clearValue();
    Ext.getCmp('cmb_cliente').reset();
    storeClientes.removeAll();
    store.removeAll();
    }

    function eliminarAlgunos(){
    var param = '';
    if(sm.getSelection().length > 0)
    {
      var estado = 0;
      for(var i=0 ;  i < sm.getSelection().length ; ++i)
      {
        param = param + sm.getSelection()[i].data.id_comunicacion;

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
                    url: "deleteAjax",
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
        alert('Seleccione por lo menos un registro de la lista.');
    }
}
