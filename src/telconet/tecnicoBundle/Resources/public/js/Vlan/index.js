Ext.require([
	'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

Ext.tip.QuickTipManager.init();

Ext.onReady(function() {
    
    store = new Ext.data.Store({
        autoLoad : true,
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
            url: urlAjaxGrid,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'data'
            },
            extraParams: {
                nombre: '',
                vlan: ''
            },
            actionMethods: {
                create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
            }
        },
        fields:
            [
                {name: 'id', mapping: 'id'},
                {name: 'vlan', mapping: 'vlan'},
                {name: 'id_elemento', mapping: 'id_elemento'},
                {name: 'elemento', mapping: 'elemento'},
                {name: 'fe_creacion', mapping: 'fe_creacion'},
                {name: 'usr_creacion', mapping: 'usr_creacion'}
            ]
    });

    grid = Ext.create('Ext.grid.Panel', {
        height: 230,
        title: 'Reservadas Por Pe',
        store: store,
        loadMask: true,
        frame: false,
        viewConfig: {enableTextSelection: true},
        columns: [
            {
                id: 'id',
                dataIndex: 'id',
                hidden: true
            },
            {
                id: 'id_elemento',
                dataIndex: 'id_elemento',
                hidden: true
            },
            {
                header: 'Pe',
                dataIndex: 'elemento',
                sortable: true,
                width: 230
            },
            {
                header: 'Vlan',
                dataIndex: 'vlan',
                sortable: true,
                width: 230
            },
            {
                header: 'Usr Creacion',
                dataIndex: 'usr_creacion',
                sortable: true,
                width: 180
            },
            {
                header: 'Fe Creacion',
                dataIndex: 'fe_creacion',
                sortable: true,
                width: 180
            },
            
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 175,
                items: [
                    {
                        getClass: function(v, meta, rec) {
                            
                            if (!puedeEliminarVlan) {
                                return "icon-invisible";
                            }
                            
                            return "button-grid-BigDelete";
                        },
                        tooltip: 'Eliminar',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            
                            if(puedeEliminarVlan)
                            {
                                Ext.Msg.confirm('Alerta','Se eliminara la Vlan <b>'+
                                                rec.get('vlan')+
                                                '</b> del Pe <b>' + rec.get('elemento')+
                                                '</b>. Desea continuar?', function(btn){
                                    if(btn=='yes'){
                                        Ext.MessageBox.wait('Eliminando vlan...');
                                        Ext.Ajax.request({
                                            url: urlAjaxDelete,
                                            method: 'post',
                                            params: { id : rec.get('id')},
                                            success: function(response){
                                                Ext.MessageBox.hide();
                                                Ext.Msg.alert('Mensaje', response.responseText, function(btn) {
                                                    if (btn == 'ok') {
                                                        store.load();
                                                    }
                                                });
                                            },
                                            failure: function(response)
                                            {
                                                Ext.MessageBox.hide();
                                                Ext.Msg.alert('Error', response.responseText, function(btn) {
                                                    if (btn == 'ok') {
                                                        store.load();
                                                    }
                                                });
                                            }
                                        });
                                    }
                                });
                            } 
                            else
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: 'No tiene permisos para realizar esta accion',
                                    buttons: Ext.MessageBox.OK,
                                    icon: Ext.MessageBox.ERROR
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
        bodyPadding: 7, // Don't want content to crunch against the borders
        border: false,
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 5,
            align: 'stretch'
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed: false,
        title: 'Criterios de busqueda',
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function() {
                    buscar();
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function() {
                    limpiar();
                }
            }

        ],
        items: [
            {width: '10%', border: false},
            {
                xtype: 'textfield',
                id: 'txtNombre',
                fieldLabel: 'Pe',
                value: '',
                width: '200px'
            },
            {
                xtype: 'textfield',
                id: 'txtNumeroVlan',
                fieldLabel: 'Vlan',
                value: '',
                width: '200px'
            }
        ],
        renderTo: 'filtro'
    });

});


function buscar() {
    store.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.vlan   = Ext.getCmp('txtNumeroVlan').value;
    store.load();

}

function limpiar() {
    Ext.getCmp('txtNombre').value = "";
    Ext.getCmp('txtNombre').setRawValue("");
    Ext.getCmp('txtNumeroVlan').value = "";
    Ext.getCmp('txtNumeroVlan').setRawValue("");
    store.load({params: {
        nombre: Ext.getCmp('txtNombre').value,
        vlan: Ext.getCmp('txtNumeroVlan').value
    }});
}