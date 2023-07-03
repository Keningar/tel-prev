/**
 * Funcion que sirve para cargar los paneles de filtros y el grid de
 * la pantalla de administracion de contrasenas
 *
 * @author Francisco Adum <fadum@telconet.ec>
 * @version 1.0 5-02-2015
 * */
Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();

    store = new Ext.data.Store({
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
            url: 'getEncontrados',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombreElemento: '',
                estado: 'Todos'
            }
        },
        fields:
            [
                {name: 'idElementoContrasena',  mapping: 'idElementoContrasena'},
                {name: 'nombreElemento',        mapping: 'nombreElemento'},
                {name: 'nombreModeloElemento',  mapping: 'nombreModeloElemento'},
                {name: 'nombreTipoElemento',    mapping: 'nombreTipoElemento'},
                {name: 'contrasena',            mapping: 'contrasena'},
                {name: 'estado',                mapping: 'estado'},
                {name: 'action1',               mapping: 'action1'},
                {name: 'action2',               mapping: 'action2'},
                {name: 'action3',               mapping: 'action3'},
                {name: 'action4',               mapping: 'action4'}
            ],
        autoLoad: true
    });

    pluginExpanded = true;

    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    });

    grid = Ext.create('Ext.grid.Panel', {
        width: 850,
        height: 350,
        store: store,
        loadMask: true,
        frame: false,
        selModel: sm,
        viewConfig: {enableTextSelection: true},
        iconCls: 'icon-grid',
        columns: [
            {
                id: 'idElementoContrasena',
                header: 'idElementoContrasena',
                dataIndex: 'idElementoContrasena',
                hidden: true,
                hideable: false
            },
            {
                id: 'nombreElemento',
                header: 'Nombre Elemento',
                dataIndex: 'nombreElemento',
                width: 200,
                sortable: true
            },
            {
                id: 'nombreModeloElemento',
                header: 'Modelo',
                dataIndex: 'nombreModeloElemento',
                width: 150,
                sortable: true
            },
            {
                id: 'nombreTipoElemento',
                header: 'Tipo',
                dataIndex: 'nombreTipoElemento',
                width: 150,
                sortable: true
            },
            {
                header: 'Estado',
                dataIndex: 'estado',
                width: 150,
                sortable: true
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 150,
                items: [
                    {
                        getClass: function(v, meta, rec) {
                            var permiso = $("#ROLE_271-2077");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);  
                            if (!boolPermiso) {
                                rec.data.action4 = "icon-invisible";
                            }

                            if (rec.get('action4') == "icon-invisible")
                                this.items[0].tooltip = '';
                            else
                                this.items[0].tooltip = 'Ver Contrasena';

                            return rec.get('action4');
                        },
                        tooltip: 'Ver Contrasena',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            var permiso = $("#ROLE_271-2077");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);  
                            if (!boolPermiso) {
                                rec.data.action4 = "icon-invisible";
                            }

                            if (rec.get('action4') != "icon-invisible")
                                verContrasena(rec.get('contrasena'));
                            else
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                        }
                    },
                    {
                        getClass: function(v, meta, rec) {
                            var permiso = $("#ROLE_271-6");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                rec.data.action1 = "icon-invisible";
                            }

                            if (rec.get('action1') == "icon-invisible")
                                this.items[0].tooltip = '';
                            else
                                this.items[0].tooltip = 'Ver';

                            return rec.get('action1');
                        },
                        tooltip: 'Ver',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            var permiso = $("#ROLE_271-6");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                rec.data.action1 = "icon-invisible";
                            }

                            if (rec.get('action1') != "icon-invisible")
                                window.location = rec.get('idElementoContrasena') + "/show";
                            else
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                        }
                    },
                    {
                        getClass: function(v, meta, rec) {
                            var permiso = $("#ROLE_271-8");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                rec.data.action3 = "icon-invisible";
                            }
                            
                            var permiso = $("#ROLE_271-9");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                rec.data.action3 = "icon-invisible";
                            }

                            if (rec.get('action3') == "icon-invisible")
                                this.items[2].tooltip = '';
                            else
                                this.items[2].tooltip = 'Eliminar';

                            return rec.get('action3');
                        },
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            var permiso = $("#ROLE_271-8");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                rec.data.action3 = "icon-invisible";
                            }
                            
                            var permiso = $("#ROLE_271-9");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                rec.data.action3 = "icon-invisible";
                            }

                            if (rec.get('action3') != "icon-invisible")
                            {
                                Ext.Msg.confirm('Alerta', 'Se eliminara el registro. Desea continuar?', function(btn) {
                                    if (btn == 'yes') {
                                        Ext.Ajax.request({
                                            url: "deleteAjax",
                                            method: 'post',
                                            params: {param: rec.get('idElementoContrasena')},
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
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
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

    filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7, 
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
            {width: '10%', border: false},
            {
                xtype: 'textfield',
                id: 'txtNombreElemento',
                fieldLabel: 'Nombre Elemento',
                value: '',
                width: '200px'
            },
            {width: '20%', border: false},
            {
                xtype: 'combobox',
                fieldLabel: 'Estado',
                id: 'sltEstado',
                value: 'Todos',
                store: [
                    ['Todos', 'Todos'],
                    ['Activo', 'Activo'],
                    ['Modificado', 'Modificado'],
                    ['Eliminado', 'Eliminado']
                ],
                width: '30%'
            },
            {width: '10%', border: false}
        ],
        renderTo: 'filtro'
    });

});

/**
 * Funcion que sirve para desencriptar la contraseña de un elemento
 * 
 * @author Francisco Adum <fadum@telconet.ec>
 * @version 1.0 11-02-2015
 * */
function verContrasena(cadena) {
    Ext.get(grid.getId()).mask('Consultando Clave...');

    Ext.Ajax.request({
        url: verContrasenaBoton,
        method: 'post',
        timeout: 400000,
        params: {
            contrasena: cadena
        },
        success: function(response) {
            Ext.get(grid.getId()).unmask();
            var datos = response.responseText;

            var formPanel = Ext.create('Ext.form.Panel', {
                bodyPadding: 2,
                waitMsgTarget: true,
                fieldDefaults: {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget: 'side'
                },
                items: [
                    {
                        xtype: 'fieldset',
                        title: '',
                        defaultType: 'textfield',
                        defaults: {
                            width: 200
                        },
                        items: [
                            {
                                xtype: 'textfield',
                                fieldLabel: 'Clave',
                                displayField: datos,
                                value: datos,
                                readOnly: true,
                                width: '30%'
                            }

                        ]
                    }//cierre 
                ],
                buttons: [{
                        text: 'Cerrar',
                        handler: function() {
                            win.destroy();
                        }
                    }]
            });

            var win = Ext.create('Ext.window.Window', {
                title: 'Clave del Elemento',
                modal: true,
                width: 300,
                closable: true,
                layout: 'fit',
                items: [formPanel]
            }).show();
        }

    });
}

/**
 * Funcion que sirve para enviar datos de los filtros para realizar la busqueda
 * 
 * @author Francisco Adum <fadum@telconet.ec>
 * @version 1.0 11-02-2015
 * */
function buscar(){
    store.getProxy().extraParams.nombreElemento = Ext.getCmp('txtNombreElemento').value;
    store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
    store.load();
}

/**
 * Funcion que sirve para limpiar los filtros para realizar la busqueda
 * 
 * @author Francisco Adum <fadum@telconet.ec>
 * @version 1.0 11-02-2015
 * */
function limpiar(){
    Ext.getCmp('txtNombreElemento').value="";
    Ext.getCmp('txtNombreElemento').setRawValue("");
    
    Ext.getCmp('sltEstado').value="Todos";
    Ext.getCmp('sltEstado').setRawValue("Todos");
}
