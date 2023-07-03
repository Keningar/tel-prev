Ext.onReady(function() {    
    Ext.tip.QuickTipManager.init();

    var storeMarca = new Ext.data.Store({ 
        total: 'total',
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : '../../tecnico/admi_marca_elemento/getMarcasElementos',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'nombreMarcaElemento', mapping:'nombreMarcaElemento'},
                {name:'idMarcaElemento', mapping:'idMarcaElemento'}
              ]
    });
    
    var storeTipo = new Ext.data.Store({ 
        total: 'total',
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : '../../tecnico/admi_tipo_elemento/getTiposElementos',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'nombreTipoElemento', mapping:'nombreTipoElemento'},
                {name:'idTipoElemento', mapping:'idTipoElemento'}
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
                nombre: '',
                marcaElemento: '',
                tipoElemento: '',
                estado: 'Todos'
            }
        },
        fields:
                  [
                    {name:'idModeloElemento', mapping:'idModeloElemento'},
                    {name:'nombreModeloElemento', mapping:'nombreModeloElemento'},
                    {name:'marcaElemento', mapping:'marcaElemento'},
                    {name:'tipoElemento', mapping:'tipoElemento'},
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
        width: 860,
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
                  id: 'idModeloElemento',
                  header: 'idModeloElemento',
                  dataIndex: 'idModeloElemento',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'nombreModeloElemento',
                  header: 'Nombre Modelo',
                  dataIndex: 'nombreModeloElemento',
                  width: 200,
                  sortable: true
                },
                {
                  id: 'marcaElemento',
                  header: 'Marca Elemento',
                  dataIndex: 'marcaElemento',
                  width: 150,
                  sortable: true
                },
                {
                  id: 'tipoElemento',
                  header: 'Tipo Elemento',
                  dataIndex: 'tipoElemento',
                  width: 200,
                  sortable: true
                },
                {
                  header: 'Estado',
                  dataIndex: 'estado',
                  width: 100,
                  sortable: true
                },
                {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: 160,
                    items: [
                        {
                        getClass: function(v, meta, rec) {return 'button-grid-show'},
                        tooltip: 'Ver',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            window.location = ""+rec.get('idModeloElemento')+"/show";
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
                                    window.location = ""+rec.get('idModeloElemento')+"/edit";
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
                                                params: { param : rec.get('idModeloElemento')},
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
        width: 860,
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
                        { width: '10%',border:false},
                        {
                            xtype: 'textfield',
                            id: 'txtNombre',
                            fieldLabel: 'Nombre',
                            value: '',
                            width: '200px'
                        },
                        { width: '20%',border:false},
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
                        { width: '10%',border:false},
                    
                        { width: '10%',border:false}, //inicio
                        {
                            xtype: 'combo',
                            id: 'sltMarcaElemento',
                            fieldLabel: 'Marca Elemento',
                            store: storeMarca,
                            displayField: 'nombreMarcaElemento',
                            valueField: 'idMarcaElemento',
                            width: '30%'
                        },
                        { width: '20%',border:false}, //medio
                        {
                            xtype: 'combo',
                            id: 'sltTipoElemento',
                            fieldLabel: 'Tipo Elemento',
                            store: storeTipo,
                            displayField: 'nombreTipoElemento',
                            valueField: 'idTipoElemento',
                            width: '30%'
                        },
                        { width: '10%',border:false}, //final
                        
                        
                        ],	
        renderTo: 'filtro'
    });
    
});

function buscar(){
    store.getProxy().extraParams.marcaElemento = Ext.getCmp('sltMarcaElemento').value;
    store.getProxy().extraParams.tipoElemento = Ext.getCmp('sltTipoElemento').value;
    store.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
    store.load();
}

function limpiar(){
    Ext.getCmp('txtNombre').value="";
    Ext.getCmp('txtNombre').setRawValue("");
    
    Ext.getCmp('sltTipoElemento').value="";
    Ext.getCmp('sltTipoElemento').setRawValue("");
    
    Ext.getCmp('sltMarcaElemento').value="";
    Ext.getCmp('sltMarcaElemento').setRawValue("");
    
    Ext.getCmp('sltEstado').value="Todos";
    Ext.getCmp('sltEstado').setRawValue("Todos");
    
    store.getProxy().extraParams.marcaElemento = Ext.getCmp('sltMarcaElemento').value;
    store.getProxy().extraParams.tipoElemento = Ext.getCmp('sltTipoElemento').value;
    store.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
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
        param = param + sm.getSelection()[i].data.idModeloElemento;
        
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