/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.require([
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();

    Ext.define('ModelStore', {
        extend: 'Ext.data.Model',
        fields:
            [
                {name: 'id_tipo_cuenta', mapping: 'id_tipo_cuenta'},
                {name: 'descripcion_tipo_cuenta', mapping: 'descripcion_tipo_cuenta'},
                {name: 'estado', mapping: 'estado'},
                {name: 'action1', mapping: 'action1'},
                {name: 'action2', mapping: 'action2'},
                {name: 'action3', mapping: 'action3'}
            ],
        idProperty: 'id_tipo_cuenta'
    });

    store = new Ext.data.Store({
        pageSize: 10,
        model: 'ModelStore',
        total: 'total',
        proxy: {
            type: 'ajax',
            timeout: 600000,
            url: 'grid',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
                estado: 'Todos'
            }
        },
        autoLoad: true
    });

    var pluginExpanded = true;

    //****************  DOCKED ITEMS - BOTONES PARTE SUPERIOR ************************
    var permiso = $("#ROLE_45-8");
    var boolPermiso1 = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

    var permiso = $("#ROLE_45-9");
    var boolPermiso2 = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

    var eliminarBtn = "";
    sm = "";
    if (boolPermiso1 && boolPermiso2)
    {
        sm = Ext.create('Ext.selection.CheckboxModel', {
            checkOnly: true
        })

        eliminarBtn = Ext.create('Ext.button.Button', {
            iconCls: 'icon_delete',
            text: 'Eliminar',
            itemId: 'deleteAjax',
            text    : 'Eliminar',
                scope: this,
            handler: function() {
                eliminarAlgunos();
            }
        });
    }

    var toolbar = Ext.create('Ext.toolbar.Toolbar', {
        dock: 'top',
        align: '->',
        items:
            [
                {
                    iconCls: 'icon_add',
                    text: 'Seleccionar Todos',
                    itemId: 'select',
                    scope: this,
                    handler: function() {
                        Ext.getCmp('grid').getPlugin('pagingSelectionPersistence').selectAll()
                    }
                },
                {
                    iconCls: 'icon_limpiar',
                    text: 'Borrar Todos',
                    itemId: 'clear',
                    scope: this,
                    handler: function() {
                        Ext.getCmp('grid').getPlugin('pagingSelectionPersistence').clearPersistedSelection()
                    }
                },
                //tbfill -> alinea los items siguientes a la derecha
                {xtype: 'tbfill'},
                eliminarBtn
            ]
    });

    grid = Ext.create('Ext.grid.Panel', {
        id: 'grid',
        width: 850,
        height: 400,
        store: store,
        selModel: Ext.create('Ext.selection.CheckboxModel'),
        plugins: [{ptype: 'pagingselectpersist'}],
        viewConfig: {
            enableTextSelection: true,
            id: 'gv',
            trackOver: true,
            stripeRows: true,
            loadMask: false
        },
        dockedItems: [toolbar],
        columns: [
            {
                id: 'id_tipo_cuenta',
                header: 'IdTipoCuenta',
                dataIndex: 'id_tipo_cuenta',
                hidden: true,
                hideable: false
            },
            {
                id: 'descripcion_tipo_cuenta',
                header: 'Descripcion Tipo de Cuenta',
                dataIndex: 'descripcion_tipo_cuenta',
                width: 300,
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
                items: [
                    {
                        /*Ver Tipo Cuenta*/
                        getClass: function(v, meta, rec) {
                            return 'button-grid-show'
                        },
                        tooltip: 'Ver',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            window.location = "" + rec.get('id_tipo_cuenta') + "/show";
                        }
                    },
                    {
                        /*Editar Tipo Cuenta*/
                        getClass: function(grid, rowIndex, rec) {
                            var permiso = $("#ROLE_45-4");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            strEditarTipoCuenta = 'button-grid-invisible';
                            if (boolPermiso && 'Eliminado' !== rec.get('estado')) {
                                strEditarTipoCuenta = 'button-grid-edit';
                            }
                            return strEditarTipoCuenta;
                        },
                        tooltip: 'Editar Tipo Cuenta',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            window.location = "" + rec.get('id_tipo_cuenta') + "/edit";
                        }
                    },
                    {
                        /*Eliminar Tipo Cuenta*/
                        getClass: function(v, meta, rec) {
                            var permiso = $("#ROLE_45-9");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            strEditarTipoCuenta = 'button-grid-invisible';                            
                            if (boolPermiso && 'Eliminado' !== rec.get('estado')) {
                                strEditarTipoCuenta = 'button-grid-delete';
                            }
                            return strEditarTipoCuenta;
                        },
                        tooltip: 'Eliminar Tipo Cuenta',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            Ext.Msg.confirm('Alerta', 'Se eliminara el registro. Desea continuar?', function(btn) {
                                if (btn === 'yes') {
                                    Ext.Ajax.request({
                                        url: urlDeleteAjax,
                                        method: 'post',
                                        params: {param: rec.get('id_tipo_cuenta')},
                                        success: function(response) {
                                            var text = response.responseText;
                                            store.load();
                                        },
                                        failure: function(result)
                                        {
                                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
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
        bodyPadding: 7, // Don't want content to crunch against the borders
        //bodyBorder: false,
        border: false,
        //border: '1,1,0,1',
        buttonAlign: 'center',
        layout: {
            type: 'hbox',
            align: 'stretch'
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed: true,
        width: 850,
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
            {width: '5%', border: false},
            {
                xtype: 'textfield',
                id: 'txtNombre',
                fieldLabel: 'Nombre',
                value: '',
                width: '250'
            },
            {width: '15%', border: false},
            , {
                xtype: 'combobox',
                fieldLabel: 'Estado',
                id: 'sltEstado',
                value: 'Todos',
                store: [
                    ['Todos', 'Todos'],
                    ['ACTIVO', 'Activo'],
                    ['MODIFICADO', 'Modificado'],
                    ['ELIMINADO', 'Eliminado']
                ],
                width: '200'
            }
        ],
        renderTo: 'filtro'
    });

});


/* ******************************************* */
/*  FUNCIONES  */
/* ******************************************* */

function buscar() {
    store.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
    store.load();
}
function limpiar() {
    Ext.getCmp('txtNombre').value = "";
    Ext.getCmp('txtNombre').setRawValue("");
    Ext.getCmp('sltEstado').value = "Todos";
    Ext.getCmp('sltEstado').setRawValue("Todos");

    store.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
    store.load();
}

function eliminarAlgunos() {
    var param = '';
    var selection = grid.getPlugin('pagingSelectionPersistence').getPersistedSelection();

    if (selection.length > 0)
    {
        var estado = 0;
        for (var i = 0; i < selection.length; ++i)
        {
            param = param + selection[i].getId();

            if (i < (selection.length - 1))
            {
                param = param + '|';
            }
        }

        if (estado == 0)
        {
            Ext.Msg.confirm('Alerta', 'Se eliminaran los registros. Desea continuar?', function(btn) {
                if (btn == 'yes') {
                    Ext.Ajax.request({
                        url: "deleteAjax",
                        method: 'post',
                        params: {param: param},
                        success: function(response) {
                            var text = response.responseText;
                            store.load();
                        },
                        failure: function(result)
                        {
                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
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