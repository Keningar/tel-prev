/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Ext.onReady(function() { 
    Ext.tip.QuickTipManager.init();
              
    store = new Ext.data.Store({ 
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'grid',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombreBanco: '',
                nombreTipoCuenta: '',
                estado: 'Todos'
            }
        },
        fields:
                [
                    {name:'id_banco_tipo_cuenta', mapping:'id_banco_tipo_cuenta'},
                    {name:'id_banco', mapping:'id_banco'},
                    {name:'id_tipo_cuenta', mapping:'id_tipo_cuenta'},
                    {name:'descripcion_banco', mapping:'descripcion_banco'},
                    {name:'descripcion_tipo_cuenta', mapping:'descripcion_tipo_cuenta'},
                    {name:'total_caracteres', mapping:'total_caracteres'},
                    {name:'total_codseguridad', mapping:'total_codseguridad'},
                    {name:'caracter_empieza', mapping:'caracter_empieza'},
                    {name:'es_tarjeta', mapping:'es_tarjeta'},
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
        width: 850,
        height: 400,
        store: store,
        loadMask: true,
        frame: false,
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
        columns:
        [
                {
                  id: 'id_banco_tipo_cuenta',
                  header: 'IdBancoTipoCuenta',
                  dataIndex: 'id_banco_tipo_cuenta',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'id_banco',
                  header: 'IdBanco',
                  dataIndex: 'id_banco',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'id_tipo_cuenta',
                  header: 'IdTipoCuenta',
                  dataIndex: 'id_tipo_cuenta',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'descripcion_banco',
                  header: 'Descripcion Banco',
                  dataIndex: 'descripcion_banco',
                  width: 300,
                  sortable: true
                },
                {
                  id: 'descripcion_tipo_cuenta',
                  header: 'Descripcion Tipo Cuenta',
                  dataIndex: 'descripcion_tipo_cuenta',
                  width: 300,
                  sortable: true
                },
                {
                  id: 'total_caracteres',
                  header: 'Total Caracteres',
                  dataIndex: 'total_caracteres',
                  width: 100,
                  sortable: true
                },
                {
                  id: 'total_codseguridad',
                  header: 'Total Codseguridad',
                  dataIndex: 'total_codseguridad',
                  width: 100,
                  sortable: true
                },
                {
                  id: 'caracter_empieza',
                  header: 'Caracter Empieza',
                  dataIndex: 'total_codcaracter_empiezaseguridad',
                  width: 100,
                  sortable: true
                },
                {
                  id: 'es_tarjeta',
                  header: 'Es Tarjeta',
                  dataIndex: 'es_tarjeta',
                  width: 80,
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
                    width: 120,
                    items: [{
                        getClass: function(v, meta, rec) {return rec.get('action1')},
                        tooltip: 'Ver',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            window.location = rec.get('id_banco_tipo_cuenta')+"/show";
                            }
                        },
                        {
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
                                window.location = rec.get('id_banco_tipo_cuenta')+"/edit";
                            }
                        },
                        {
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
                                            params: { param : rec.get('id_banco_tipo_cuenta')},
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
    
    /* ******************************************* */
                /* FILTROS DE BUSQUEDA */
    /* ******************************************* */
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
        width: 850,
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
                        { width: '5%',border:false},
                        {
                            xtype: 'textfield',
                            id: 'txtNombreBanco',
                            fieldLabel: 'Banco',
                            value: '',
                            width: '250'
                        },
                        { width: '15%',border:false},
                        {
                            xtype: 'textfield',
                            id: 'txtNombreTipoCuenta',
                            fieldLabel: 'Tipo Cuenta',
                            value: '',
                            width: '250'
                        },
                        
                        
                        { width: '5%',border:false},
                        ,{
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
                            width: '250'
                        },
                        { width: '15%',border:false},
                        { width: '250',border:false}
                    ],	
        renderTo: 'filtro'
    }); 
    
});


/* ******************************************* */
            /*  FUNCIONES  */
/* ******************************************* */

function buscar(){
    store.getProxy().extraParams.nombre_banco = Ext.getCmp('txtNombreBanco').value;
    store.getProxy().extraParams.nombre_tipo_cuenta = Ext.getCmp('txtNombreTipoCuenta').value;
    store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
    store.load();
}
function limpiar(){
    Ext.getCmp('txtNombreBanco').value="";
    Ext.getCmp('txtNombreBanco').setRawValue("");
    Ext.getCmp('txtNombreTipoCuenta').value="";
    Ext.getCmp('txtNombreTipoCuenta').setRawValue("");
    Ext.getCmp('sltEstado').value="Todos";
    Ext.getCmp('sltEstado').setRawValue("Todos");
    
    store.getProxy().extraParams.nombre_banco = Ext.getCmp('txtNombreBanco').value;
    store.getProxy().extraParams.nombre_tipo_cuenta = Ext.getCmp('txtNombreTipoCuenta').value;
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
        param = param + sm.getSelection()[i].data.id_banco_tipo_cuenta;

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