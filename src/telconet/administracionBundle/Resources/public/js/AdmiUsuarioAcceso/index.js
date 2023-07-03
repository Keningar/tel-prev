/**
 * Funcion que sirve para cargar los paneles de filtros y el grid
 * 
 * @author Francisco Adum <fadum@telconet.ec>
 * @version 1.0     5-02-2015
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
                nombreUsuarioAcceso: '',
                estado: 'Todos'
            }
        },
        fields:
            [
                {name: 'idUsuarioAcceso',       mapping: 'idUsuarioAcceso'},
                {name: 'nombreUsuarioAcceso',   mapping: 'nombreUsuarioAcceso'},
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
                id: 'idUsuarioAcceso',
                header: 'idUsuarioAcceso',
                dataIndex: 'idUsuarioAcceso',
                hidden: true,
                hideable: false
            },
            {
                id: 'nombreUsuarioAcceso',
                header: 'Nombre Usuario',
                dataIndex: 'nombreUsuarioAcceso',
                width: 400,
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
                width: 200,
                items: [
                    {
                        getClass: function(v, meta, rec) {
                            var permiso = $("#ROLE_132-6");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                rec.data.action4 = "icon-invisible";
                            }

                            if (rec.get('action4') == "icon-invisible")
                                this.items[0].tooltip = '';
                            else
                                this.items[0].tooltip = 'Relacion Usuario - Modelo';

                            return rec.get('action4')
                        },
                        tooltip: 'Relacion Usuario - Modelo',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            var permiso = $("#ROLE_132-6");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                rec.data.action4 = "icon-invisible";
                            }

                            if (rec.get('action4') != "icon-invisible")
                                verRelacionUsuarioModelo(rec.get('idUsuarioAcceso'));
                            else
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                        }
                    },
                    {
                        getClass: function(v, meta, rec) {
                            var permiso = $("#ROLE_132-6");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                rec.data.action1 = "icon-invisible";
                            }

                            if (rec.get('action1') == "icon-invisible")
                                this.items[0].tooltip = '';
                            else
                                this.items[0].tooltip = 'Ver';

                            return rec.get('action1')
                        },
                        tooltip: 'Ver',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            var permiso = $("#ROLE_132-6");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                rec.data.action1 = "icon-invisible";
                            }

                            if (rec.get('action1') != "icon-invisible")
                                window.location = rec.get('idUsuarioAcceso') + "/show";
                            else
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                        }
                    },
                    {
                        getClass: function(v, meta, rec) {
                            var permiso = $("#ROLE_132-4");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                rec.data.action2 = "icon-invisible";
                            }

                            if (rec.get('action2') == "icon-invisible")
                                this.items[1].tooltip = '';
                            else
                                this.items[1].tooltip = 'Editar';

                            return rec.get('action2')
                        },
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            var permiso = $("#ROLE_132-4");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                rec.data.action2 = "icon-invisible";
                            }

                            if (rec.get('action2') != "icon-invisible")
                                window.location = rec.get('idUsuarioAcceso') + "/edit";
                            else
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                        }
                    },
                    {
                        getClass: function(v, meta, rec) {
                            var permiso = $("#ROLE_132-8");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                rec.data.action3 = "icon-invisible";
                            }

                            var permiso = $("#ROLE_132-9");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                rec.data.action3 = "icon-invisible";
                            }

                            if (rec.get('action3') == "icon-invisible")
                                this.items[2].tooltip = '';
                            else
                                this.items[2].tooltip = 'Eliminar';

                            return rec.get('action3')
                        },
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            var permiso = $("#ROLE_132-8");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                rec.data.action3 = "icon-invisible";
                            }

                            var permiso = $("#ROLE_132-9");
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
                                            params: {param: rec.get('idUsuarioAcceso')},
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
        bodyPadding: 7, // Don't want content to crunch against the borders
        //bodyBorder: false,
        border: false,
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
                id: 'txtNombre',
                fieldLabel: 'Nombre',
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
 * Funcion que sirve para cargar los datos del filtro en las 
 * variables que se envian al controlador para que busque en la base
 * 
 * @author Francisco Adum <fadum@telconet.ec>
 * @version 1.0     5-02-2015
 * */
function buscar() {
    store.getProxy().extraParams.nombreUsuarioAcceso    = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.estado                 = Ext.getCmp('sltEstado').value;
    store.load();
}

/**
 * Funcion que sirve para limpiar los campos del filtro
 * 
 * @author Francisco Adum <fadum@telconet.ec>
 * @version 1.0     5-02-2015
 * */
function limpiar() {
    Ext.getCmp('txtNombre').value = "";
    Ext.getCmp('txtNombre').setRawValue("");

    Ext.getCmp('sltEstado').value = "Todos";
    Ext.getCmp('sltEstado').setRawValue("Todos");
}

/**
 * Funcion que sirve para mostrar un pop up con la relacion
 * entre el usuario y el modelo del elemento.
 * 
 * @author Francisco Adum <fadum@telconet.ec>
 * @version 1.0     5-02-2015
 * */
function verRelacionUsuarioModelo(idUsuarioAcceso) {
    var informacionGrid = new Ext.data.Store({
        total: 'total',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: getRelacionUsuarioModelo,
            extraParams: {idUsuario: idUsuarioAcceso},
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'nombreUsuarioAcceso',   mapping: 'nombreUsuarioAcceso'},
                {name: 'nombreModeloElemento',  mapping: 'nombreModeloElemento'},
                {name: 'nombreTipoElemento',    mapping: 'nombreTipoElemento'}
            ]
    });

    gridRelacionUsuarioModelo = Ext.create('Ext.grid.Panel', {
        id: 'gridRelacionUsuarioModelo',
        store: informacionGrid,
        columnLines: true,
        columns: [
            {
                id: 'nombreUsuarioAcceso',
                header: 'Usuario',
                dataIndex: 'nombreUsuarioAcceso',
                width: 120
            },
            {
                id: 'nombreModeloElemento',
                header: 'Modelo Elemento',
                dataIndex: 'nombreModeloElemento',
                width: 120
            },
            {
                id: 'nombreTipoElemento',
                header: 'Tipo Elemento',
                dataIndex: 'nombreTipoElemento',
                width: 120
            }
        ],
        viewConfig: {
            stripeRows: true
        },
        width: 380,
        height: 350,
        frame: true
    });

    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side',
            bodyStyle: 'padding:20px'
        },
        layout: {
            type: 'table',
            // The total column count must be specified here
            columns: 2
        },
        defaults: {
            // applied to each contained panel
            bodyStyle: 'padding:20px'
        },
        items: [
            gridRelacionUsuarioModelo
        ],
        buttons: [{
                text: 'Cerrar',
                handler: function() {
                    win.destroy();
                }
            }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Relacion Usuario - Modelo',
        modal: true,
        width: 400,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}