/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.QuickTips.init();
Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();
    store = new Ext.data.Store({
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
            url: url_grid,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados',
            },
            extraParams: {
                nombre: '',
                estado: 'Todos',
                tipo: 'Notificacion Externa' //Se crea este parametro para poder filtrar la busqueda
            }
        },
        fields:
            [
                {name: 'id_documento', mapping: 'id_documento'},
                {name: 'nombre', mapping: 'nombre'},
                {name: 'usuario', mapping: 'usuario'},
                {name: 'estado', mapping: 'estado'},
                {name: 'tipo', mapping: 'tipo'},
                {name: 'action1', mapping: 'action1'},
                {name: 'action2', mapping: 'action2'},
                {name: 'action3', mapping: 'action3'},
                {name: 'fechaPublicacionDesde', mapping: 'fechaPublicacionDesde'},
                {name: 'fechaPublicacionHasta', mapping: 'fechaPublicacionHasta'}
            ],
        autoLoad: true
    });

    var pluginExpanded = true;

    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    })


    grid = Ext.create('Ext.grid.Panel', {
        width: 950,
        height: 400,
        store: store,
        viewConfig: {enableTextSelection: true},
        loadMask: true,
        frame: false,
        columns: [
            {
                id: 'id_documento',
                header: 'No',
                dataIndex: 'id_documento',
                hideable: false,
                hidden: true,
                width: 100
            },
            {
                id: 'nombre',
                header: 'Nombre',
                dataIndex: 'nombre',
                width: 340
            },
            {
                id: 'usuario',
                header: 'Creador',
                dataIndex: 'usuario',
                width: 100
            },
            {
                header: 'Fecha Desde',
                dataIndex: 'fechaPublicacionDesde',
                width: 165,
                sortable: true
            },
            {
                header: 'Fecha Hasta',
                dataIndex: 'fechaPublicacionHasta',
                width: 165,
                sortable: true
            },
            {
                header: 'Estado',
                dataIndex: 'estado',
                width: 80,
                sortable: true
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 110,
                items: [{
                        getClass: function(v, meta, rec) {
                            return rec.get('action1')
                        },
                        tooltip: 'Ver Noticia',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            window.location = rec.get('id_documento') + "/show";
                        }
                    },
                    {
                        getClass: function(v, meta, rec) {
                            //se agrega rol para administracion de noticias
                            var permiso1 = $("#ROLE_259-1857");
                            var boolPermiso1 = (typeof permiso1 === 'undefined') ? false : (permiso1.val() == 1 ? true : false);

                            if (!boolPermiso1) {
                                rec.data.action2 = "icon-invisible";
                            }
                            if (rec.get('action2') == "icon-invisible")
                                this.items[1].tooltip = '';
                            else
                                this.items[1].tooltip = 'Editar';

                            return rec.get('action2');
                        },
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            if (rec.get('action2') != "icon-invisible")
                                window.location = rec.get('id_documento') + "/edit";
                        }
                    },
                    {
                        getClass: function(v, meta, rec) {
                            //se agrega rol para administracion de noticias
                            var permiso1 = $("#ROLE_259-1857");
                            var boolPermiso1 = (typeof permiso1 === 'undefined') ? false : (permiso1.val() == 1 ? true : false);

                            if (!boolPermiso1) {
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
                            if (rec.get('action3') != "icon-invisible")
                                Ext.Msg.confirm('Alerta', 'Se eliminara el registro. Desea continuar?', function(btn) {
                                    if (btn == 'yes') {
                                        Ext.Ajax.request({
                                            url: url_delete_ajax,
                                            method: 'post',
                                            params: {param: rec.get('id_documento')},
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

    /************************************************************************************/
    //
    //			criterio de busqueda de plantillas creadas
    //
    /************************************************************************************/
    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7, // Don't want content to crunch against the borders
        border: false,
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 5
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed: true,
        width: 950,
        title: 'Criterios de búsqueda',
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function() {
                    buscar();
                }
            },
        ],
        items: [
            {html: "&nbsp;", border: false, width: 50},
            {
                xtype: 'textfield',
                id: 'txtNombre',
                name: 'txtNombre',
                fieldLabel: 'Nombre Noticia',
                value: '',
                width: 290
            },
            {html: "&nbsp;", border: false, width: 20},
            , {
                xtype: 'combobox',
                fieldLabel: 'Estado',
                id: 'sltEstado',
                value: 'Todos',
                name: 'sltEstado',
                store: [
                    ['Todos', 'Todos'],
                    ['Activo', 'Activo'],
                    ['Modificado', 'Modificado'],
                    ['Eliminado', 'Eliminado']
                ],
                width: '30%'
            },
            {html: "&nbsp;", border: false, width: 50}
        ],
        renderTo: 'filtro'
    });

});

function eliminarAlgunos() {
    var param = '';
    if (sm.getSelection().length > 0)
    {
        var estado = 0;
        for (var i = 0; i < sm.getSelection().length; ++i)
        {
            param = param + sm.getSelection()[i].data.id_accion;

            if (sm.getSelection()[i].data.estado == 'Eliminado')
            {
                estado = estado + 1;
            }
            if (i < (sm.getSelection().length - 1))
            {
                param = param + '|';
            }
        }
        if (estado == 0)
        {
            Ext.Msg.confirm('Alerta', 'Se eliminaran los registros. Desea continuar?', function(btn) {
                if (btn == 'yes') {
                    Ext.Ajax.request({
                        url: url_delete_ajax,
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
            alert('Por lo menos uno de los registros se encuentra en estado ELIMINADO');
        }
    }
    else
    {
        alert('Seleccione por lo menos un registro de la lista.');
    }
}

function exportarExcel() {
    window.open("exportarConsulta");
}

function buscar() {

    tipo = 'Notificacion Externa';
    store.proxy.extraParams = {
        tipo: tipo,
        estado: Ext.getCmp('sltEstado').value,
        //se agrega parametro nombre para filtro de noticias
        nombre: Ext.getCmp('txtNombre').value
    };

    store.load();
}