Ext.onReady(function() {    
    Ext.tip.QuickTipManager.init();

    var storeModelo = new Ext.data.Store({ 
        total: 'total',
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : '../../tecnico/admi_modelo_elemento/getModelosElementos',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'nombreModeloElemento', mapping:'nombreModeloElemento'},
                {name:'idModeloElemento', mapping:'idModeloElemento'}
              ]
    });
    
    var storeTipoInterface = new Ext.data.Store({ 
        total: 'total',
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : '../../tecnico/admi_tipo_interface/getTiposInterfaces',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'nombreTipoInterface', mapping:'nombreTipoInterface'},
                {name:'idTipoInterface', mapping:'idTipoInterface'}
              ]
    });

    store = new Ext.data.Store({ 
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'getEncontrados',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                modeloElemento: '',
                tipoInterface: '',
                estado: 'Todos'
            }
        },
        fields:
                  [
                    {name:'idInterfaceModelo', mapping:'idInterfaceModelo'},
                    {name:'modeloElemento', mapping:'modeloElemento'},
                    {name:'tipoInterface', mapping:'tipoInterface'},
                    {name:'estado', mapping:'estado'},
                    {name:'action1', mapping:'action1'},
                    {name:'action2', mapping:'action2'},
                    {name:'action3', mapping:'action3'}
                  ],
        autoLoad: true
    });
   
    var pluginExpanded = true;
    
    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    })

    
    grid = Ext.create('Ext.grid.Panel', {
        width: 800,
        height: 294,
        store: store,
        loadMask: true,
        frame: false,
        selModel: sm,
        iconCls: 'icon-grid',
        dockedItems: [ {
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items: [
                    //tbfill -> alinea los items siguientes a la derecha
                    { xtype: 'tbfill' },
                    {
                        iconCls: 'icon_delete',
                        text: 'Eliminar',
                        itemId: 'deleteAjax',
                        scope: this,
                        handler: function(){ eliminarAlgunos();}
                    }
                ]}
        ],                  
        columns:[
                {
                  id: 'idInterfaceModelo',
                  header: 'idInterfaceModelo',
                  dataIndex: 'idInterfaceModelo',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'modeloElemento',
                  header: 'Modelo Elemento',
                  dataIndex: 'modeloElemento',
                  width: 200,
                  sortable: true
                },
                {
                  id: 'tipoInterface',
                  header: 'Tipo Interface',
                  dataIndex: 'tipoInterface',
                  width: 200,
                  sortable: true
                },
                {
                  header: 'Estado',
                  dataIndex: 'estado',
                  width: 200,
                  sortable: true
                },
                {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: 120,
                    items: [{
                        getClass: function(v, meta, rec) {return 'button-grid-show'},
                        tooltip: 'Ver',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            window.location = ""+rec.get('idInterfaceModelo')+"/show";
                            }
                        },
                        {
                        getClass: function(v, meta, rec) {
                            if (rec.get('action2') == "button-grid-invisible") 
                                this.items[1].tooltip = '';
                            else 
                                this.items[1].tooltip = 'Editar';
                            
                            return rec.get('action2')
                        },
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            if(rec.get('action2')!="button-grid-invisible")
                                window.location = ""+rec.get('idInterfaceModelo')+"/edit";
                                //alert(rec.get('nombre'));
                            }
                        },
                        {
                        getClass: function(v, meta, rec) {
                            if (rec.get('action3') == "button-grid-invisible") 
                                this.items[2].tooltip = '';
                            else 
                                this.items[2].tooltip = 'Eliminar';
                            
                            return rec.get('action3')
                        },
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            if(rec.get('action3')!="button-grid-invisible")
                                Ext.Msg.confirm('Alerta','Se eliminara el registro. Desea continuar?', function(btn){
                                    if(btn=='yes'){
                                        Ext.Ajax.request({
                                            url: "delete",
                                            method: 'post',
                                            params: { param : rec.get('idInterfaceModelo')},
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
        renderTo: 'grid'
    });
    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,  // Don't want content to crunch against the borders
        //bodyBorder: false,
        border:false,
        //border: '1,1,0,1',
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 5,
            align: 'stretch'
        },
        bodyStyle: {
                    background: '#fff'
                },                     

        collapsible : true,
        collapsed: true,
        width: 800,
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
                items: [
                        { width: '10%',border:false}, //inicio
                        {
                            xtype: 'combo',
                            id: 'sltModeloElemento',
                            fieldLabel: 'Modelo Elemento',
                            store: storeModelo,
                            displayField: 'nombreModeloElemento',
                            valueField: 'idModeloElemento',
                            width: '30%'
                        },
                        { width: '20%',border:false}, //medio
                        {
                            xtype: 'combo',
                            id: 'sltTipoInterface',
                            fieldLabel: 'Tipo Interface',
                            store: storeTipoInterface,
                            displayField: 'nombreTipoInterface',
                            valueField: 'idTipoInterface',
                            width: '30%'
                        },
                        { width: '10%',border:false}, //final
                        
                        { width: '10%',border:false},
                        {
                            xtype: 'combobox',
                            fieldLabel: 'Estado',
                            id: 'sltEstado',
                            value:'Todos',
                            store: [
                                ['Todos','Todos'],
                                ['ACTIVO','Activo'],
                                ['MODIFICADO','Modificado'],
                                ['ELIMINADO','Eliminado']
                            ],
                            width: '30%'
                        },
                        { width: '20%',border:false},
                        { width: '30%',border:false},
                        { width: '10%',border:false},
                        ],	
        renderTo: 'filtro'
    }); 
    
});

function buscar(){
    store.getProxy().extraParams.tipoInterface = Ext.getCmp('sltTipoInterface').value;
    store.getProxy().extraParams.modeloElemento = Ext.getCmp('sltModeloElemento').value;
    store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
    store.load();
}

function limpiar(){
    Ext.getCmp('sltTipoInterface').value="";
    Ext.getCmp('sltTipoInterface').setRawValue("");
    
    Ext.getCmp('sltModeloElemento').value="";
    Ext.getCmp('sltModeloElemento').setRawValue("");
    
    Ext.getCmp('sltEstado').value="Todos";
    Ext.getCmp('sltEstado').setRawValue("Todos");
    
    store.getProxy().extraParams.tipoInterface = Ext.getCmp('sltTipoInterface').value;
    store.getProxy().extraParams.modeloElemento = Ext.getCmp('sltModeloElemento').value;
    store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
    store.load();
}

function eliminarAlgunos(){
    var param = '';
    if(sm.getSelection().length > 0)
    {
      var estado = 0;
      for(var i=0 ;  i < sm.getSelection().length ; ++i)
      {
        param = param + sm.getSelection()[i].data.idInterfaceModelo;
        
        if(sm.getSelection()[i].data.estado == 'Eliminado')
        {
          estado = estado + 1;
        }
        if(i < (sm.getSelection().length -1))
        {
          param = param + '|';
        }
        alert(param);
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
      alert('Seleccione por lo menos un registro de la lista');
    }
}   