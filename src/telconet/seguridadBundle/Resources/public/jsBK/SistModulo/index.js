/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


Ext.onReady(function(){
    /*******************Creacion Grid******************/
    ////////////////Grid  Encontrados////////////////
    Ext.define('Encontrado', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_modulo', mapping:'id_modulo'},
            {name:'nombre_modulo', mapping:'nombre_modulo'},
            {name:'estado', mapping:'estado'},
            {name:'action1', mapping:'action1'},
            {name:'qtip1', mapping:'qtip1'},
            {name:'action2', mapping:'action2'},
            {name:'qtip2', mapping:'qtip2'},
            {name:'action3', mapping:'action3'},
            {name:'qtip3', mapping:'qtip3'}
        ]
    });

    // create the Data Store
    store = Ext.create('Ext.data.Store', {
        autoLoad: true,
        pageSize: 10,        
        model: 'Encontrado',
        proxy: {
            type: 'ajax',
            // load remote data using HTTP
            url: 'grid',
            // specify a XmlReader (coincides with the XML format of the returned data)
            reader: {
                type: 'json',
                totalProperty: 'total',
                // records will have a 'plant' tag
                root: 'encontrados'
                },
            extraParams: {
                nombre: '',
                estado: 'Todos'
            }
        }
    });
    
    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    });
    
    gridEncontrados = Ext.create('Ext.grid.Panel', {
        width: 800,
        height: 294,
        store: store,
        loadMask: true,       
        selModel: sm,
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
                        itemId: 'delete',
                        scope: this,
                        handler: function(){ eliminarAlgunos();}
                    }
                ]}
        ],
        columns:[{
            id: 'id_modulo',
            header: 'IdModulo',
            dataIndex: 'id_modulo',
            hidden: true,
            hideable: false
        }, {
            id: 'nombre_modulo1',
            header: 'Nombre Modulo',
            dataIndex: 'nombre_modulo',
            width: 520
        }, {
            header: 'Estado',
            dataIndex: 'estado',
            width: 100,
            sortable: true
        }, {
            xtype: 'actioncolumn',
            header: 'Acciones',
            width: 120,
            items: [{
                getClass: function(v, meta, rec) {return rec.get('action1')},
                tooltip: 'Ver',
                handler: function(grid, rowIndex, colIndex) {
                    var rec = store.getAt(rowIndex);
                    window.location = rec.get('id_modulo')+"/show";                        
                }
            }, {
                getClass: function(v, meta, rec) {
                    if (rec.get('action2') == "icon-invisible") 
                        this.items[1].tooltip = '';
                    else 
                        this.items[1].tooltip = 'Editar';
                        return rec.get('action2')
                    },
                handler: function(grid, rowIndex, colIndex) {
                    var rec = store.getAt(rowIndex);
                    if(rec.get('action2')!="icon-invisible")
                        window.location = rec.get('id_modulo')+"/edit";
                }
            }, {
                getClass: function(v, meta, rec) {
                    if (rec.get('action3') == "icon-invisible") 
                        this.items[2].tooltip = '';
                    else 
                        this.items[2].tooltip = 'Eliminar';
                        return rec.get('action3')
                },
                handler: function(grid, rowIndex, colIndex) {
                    var rec = store.getAt(rowIndex);
                    if(rec.get('action3')!="icon-invisible")
                        Ext.Msg.confirm('Alerta','Se eliminara el registro. Desea continuar?', function(btn){
                                    if(btn=='yes'){
                                        Ext.Ajax.request({
                                            url: "deleteAjax",
                                            method: 'post',
                                            params: { param : rec.get('id_modulo')},
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
            }]
        }],
        // paging bar on the bottom
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
            type: 'hbox',
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
                        { width: '10%',border:false},
                        {
                            xtype: 'textfield',
                            id: 'txtNombre',
                            fieldLabel: 'Nombre',
                            value: '',
                            width: '200'
                        },
                        { width: '20%',border:false},
                        ,{
                            xtype: 'combobox',
                            fieldLabel: 'Estado',
                            id: 'sltEstado',
                            value:'Todos',
                            store: [
                                ['Todos','Todos'],
                                ['Activo','Activo'],
                                ['Modificado','Modificado'],
                                ['Eliminado','Eliminado']
                            ],
                            width: '30%'
                        }
                        ],	
        renderTo: 'filtro'
    });     
});
function buscar(){
    store.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
    store.load();
}
function limpiar(){
    Ext.getCmp('txtNombre').value="";
    Ext.getCmp('txtNombre').setRawValue("");
    Ext.getCmp('sltEstado').value="Todos";
    Ext.getCmp('sltEstado').setRawValue("Todos");
    
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
        param = param + sm.getSelection()[i].data.id_modulo;
        
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
      alert('Seleccione por lo menos un registro de la lista');
    }
}