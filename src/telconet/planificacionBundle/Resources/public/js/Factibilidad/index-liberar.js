/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Ext.require([
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

var connLiberarRecursos = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function(con, opt) {
                    Ext.MessageBox.show({
                        msg: 'Grabando los datos, Por favor espere!!',
                        progressText: 'Saving...',
                        width: 300,
                        wait: true,
                        waitConfig: {interval: 200}
                    });
                },
                scope: this
            },
            'requestcomplete': {
                fn: function(con, res, opt) {
                    Ext.MessageBox.hide();
                },
                scope: this
            },
            'requestexception': {
                fn: function(con, res, opt) {
                    Ext.MessageBox.hide();
                },
                scope: this
            }
        }
    });
    
Ext.onReady(function() {

    


    //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		
    DTFechaDesde = new Ext.form.DateField({
        id: 'fechaDesde',
        fieldLabel: 'Factibilidad Desde',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 300,
        editable: false
            //anchor : '65%',
            //layout: 'anchor'
    });
    DTFechaHasta = new Ext.form.DateField({
        id: 'fechaHasta',
        fieldLabel: 'Factibilidad Hasta',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 300,
        editable: false
            //anchor : '65%',
            //layout: 'anchor'
    });



    Ext.define('ModelStore', {
        extend: 'Ext.data.Model',
        fields:
            [
                {name: 'idServicio', mapping: 'idServicio'},
                {name: 'usrVendedor', mapping: 'usrVendedor'},
                {name: 'ciudad', mapping: 'ciudad'},
                {name: 'cliente', mapping: 'cliente'},
                {name: 'login', mapping: 'login'},
                {name: 'fechaFactibilidad', mapping: 'fechaFactibilidad'},
                {name: 'diasFactibles', mapping: 'diasFactibles'},
                {name: 'automatica', mapping: 'automatica'}
            ],
        idProperty: 'idServicio'
    });
    store = new Ext.data.Store({
        pageSize: 20,
        model: 'ModelStore',
        total: 'total',
        proxy: {
            timeout: 3000000,
            type: 'ajax',
            url: url_ajaxGrid,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            actionMethods: {
                create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
            },
            extraParams: {
                fechaDesdePlanif: '',
                fechaHastaPlanif: '',
                fechaDesdeIngOrd: '',
                fechaHastaIngOrd: '',
                login2: '',
                descripcionPunto: '',
                vendedor: '',
                ciudad: '',
                numOrdenServicio: '',
                estado: 'Todos',
                ultimaMilla: 'Fibra Optica'
            }
        },
        autoLoad: true
    });

    var pluginExpanded = true;
    var eliminarBtn = "";
    sm = "";
    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    })

    eliminarBtn = Ext.create('Ext.button.Button', {
        iconCls: 'icon_delete',
        text: 'Liberar Recursos Masivo',
        itemId: 'deleteAjax',
        scope: this,
        handler: function() {
            eliminarAlgunos();
        }
    });
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
                        //store.load();
                    }
                },
                //tbfill -> alinea los items siguientes a la derecha
                {xtype: 'tbfill'},
                eliminarBtn
            ]
    });

    grid = Ext.create('Ext.grid.Panel', {
        id: 'grid',
        width: 1200,
        height: 520,
        store: store,
        selModel: Ext.create('Ext.selection.CheckboxModel'),
        plugins: [{ptype: 'pagingselectpersist'}],
        viewConfig: {
            enableTextSelection: true,
            id: 'gv',
            trackOver: true,
            stripeRows: true,
            loadMask: true
        },
        dockedItems: [toolbar],
        columns: [
            {
                id: 'idServicio',
                header: 'IdServicio',
                dataIndex: 'idServicio',
                hidden: true,
                hideable: false
            },
            {
                id: 'usrVendedor',
                header: 'USR Vendedor',
                dataIndex: 'usrVendedor',
                hideable: false
            },
            {
                id: 'ciudad',
                header: 'Ciudad',
                width: 300,
                dataIndex: 'ciudad',
                hideable: false
            },
            {
                id: 'cliente',
                header: 'Cliente',
                width: 225,
                dataIndex: 'cliente',
                hideable: false
            },
            {
                id: 'login',
                header: 'Login',
                width: 125,
                dataIndex: 'login',
                hideable: false
            },
            {
                id: 'fechaFactibilidad',
                header: 'Fecha Factibilidad',
                width: 125,
                dataIndex: 'fechaFactibilidad',
                hideable: false
            },
            {
                id: 'diasFactibles',
                header: 'Dias Factible',
                dataIndex: 'diasFactibles',
                hideable: false
            },
            {
                id: 'automatica',
                header: 'Es Automatica',
                dataIndex: 'automatica',
                hideable: false
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 130,
                items: [
                    {
                        getClass: function(v, meta, rec) {
                            var tipoBoton = 'button-grid-verInterfacesPorPuerto';
                            var permiso = $("#ROLE_270-1997");//LIBERACION DE RECURSOS
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                tipoBoton = "icon-invisible";
                            }

                            if (tipoBoton == "icon-invisible")
                                this.items[0].tooltip = '';
                            else
                                this.items[0].tooltip = 'Liberar Recursos';

                            return tipoBoton
                        },
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            var tipoBoton = 'button-grid-verInterfacesPorPuerto';
                            var permiso = $("#ROLE_270-1997");//LIBERACION DE RECURSOS
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                tipoBoton = "icon-invisible";
                            }

                            if (tipoBoton != "icon-invisible")
                                Ext.Msg.confirm('Alerta', 'Se liberaran los Recursos de Red del servicio seleccionado. Desea continuar?', function(btn) {
                                    if (btn == 'yes') {
                                        connLiberarRecursos.request({
                                            url: url_delete_ajax,
                                            params: {param: rec.get('idServicio')},
                                            method: 'post',
                                            success: function(response) {
                                                var text = response.responseText;
                                                Ext.Msg.alert("Mensaje", text);
                                                store.load();
                                            },
                                            failure: function(result)
                                            {
                                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                            }
                                        });
                                    }
                                });
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
            type: 'table',
            columns: 5,
            align: 'left'
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed: true,
        width: 1200,
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
        items:
            [
                {html: "&nbsp;", border: false, width: 250},
                DTFechaDesde,
                {html: "&nbsp;", border: false, width: 50},
                {
                    xtype: 'textfield',
                    id: 'txtLogin',
                    fieldLabel: 'Login',
                    value: '',
                    width: '300'
                },
                {html: "&nbsp;", border: false, width: 5},
                {html: "&nbsp;", border: false, width: 250},
                DTFechaHasta,
                {html: "&nbsp;", border: false, width: 50},
                {
                    xtype: 'combobox',
                    fieldLabel: 'Es Automatica',
                    id: 'esAutomatica',
                    value: 'Todos',
                    store: [
                        ['Todos', 'Todos'],
                        ['SI', 'Si'],
                        ['NO', 'No']
                    ],
                    width: '300'
                },
                {html: "&nbsp;", border: false, width: 5},
                {html: "&nbsp;", border: false, width: 250},
                {
                    xtype: 'numberfield',
                    id: 'txtMayorA',
                    fieldLabel: 'Mayor a',
                    allowDecimals: false,
                    allowNegative: false,
                    minLength: 0,
                    maxLength: 3,
                    maxValue: 999,
                    size: 27,
                    allowBlank: true
                }
            ],
        renderTo: 'filtro'
    });

});


/* ******************************************* */
/*  FUNCIONES  */
/* ******************************************* */

function buscar() {
    var boolError = false;

    if ((Ext.getCmp('fechaDesde').getValue() != null) && (Ext.getCmp('fechaHasta').getValue() != null))
    {
        if (Ext.getCmp('fechaDesde').getValue() > Ext.getCmp('fechaHasta').getValue())
        {
            boolError = true;

            Ext.Msg.show({
                title: 'Error en Busqueda',
                msg: 'Por Favor para realizar la busqueda Factibilidad Desde debe ser fecha menor a Fecha Factibilidad Hasta .',
                buttons: Ext.Msg.OK,
                animEl: 'elId',
                icon: Ext.MessageBox.ERROR
            });
        }
    }
    var mayorA = Ext.getCmp('txtMayorA').isValid();
    if (!mayorA)
    {
        Ext.Msg.show({
            title: 'Error en Busqueda',
            msg: 'Por Favor en el campo MAYOR A se debe de ingresar un numero entre 0 y 999.',
            buttons: Ext.Msg.OK,
            animEl: 'elId',
            icon: Ext.MessageBox.ERROR
        });

        boolError = true;
    }
    if (!boolError)
    {
        store.getProxy().extraParams.fechaDesde = Ext.getCmp('fechaDesde').value;
        store.getProxy().extraParams.fechaHasta = Ext.getCmp('fechaHasta').value;
        store.getProxy().extraParams.automatica = Ext.getCmp('esAutomatica').value;
        store.getProxy().extraParams.login = Ext.getCmp('txtLogin').value;
        store.getProxy().extraParams.mayorA = Ext.getCmp('txtMayorA').value;
        store.load();
    }
}

function limpiar() {
    Ext.getCmp('fechaDesde').setRawValue("");
    Ext.getCmp('fechaDesde').value = "";
    Ext.getCmp('fechaHasta').setRawValue("");
    Ext.getCmp('fechaHasta').value = "";

    Ext.getCmp('esAutomatica').value = "";
    Ext.getCmp('esAutomatica').setRawValue("");

    Ext.getCmp('txtLogin').value = "";
    Ext.getCmp('txtLogin').setRawValue("");

    Ext.getCmp('txtMayorA').value = "";
    Ext.getCmp('txtMayorA').setRawValue("");

    store.getProxy().extraParams.fechaDesde = Ext.getCmp('fechaDesde').value;
    store.getProxy().extraParams.fechaHasta = Ext.getCmp('fechaHasta').value;
    store.getProxy().extraParams.automatica = Ext.getCmp('esAutomatica').value;
    store.getProxy().extraParams.login = Ext.getCmp('txtLogin').value;
    store.getProxy().extraParams.mayorA = Ext.getCmp('txtMayorA').value;
    store.load();
}



function eliminarAlgunos() {
    var param = '';
    var selection = grid.getPlugin('pagingSelectionPersistence').getPersistedSelection();
    if (selection.length > 0)
    {

        for (var i = 0; i < selection.length; ++i)
        {
            param = param + selection[i].getId();

            if (i < (selection.length - 1))
            {
                param = param + '|';
            }
        }

        Ext.Msg.confirm('Alerta', 'Se liberaran los Recursos de Red de los registros seleccionados. Desea continuar?', function(btn) {
            if (btn == 'yes') {
                connLiberarRecursos.request({
                    url: url_delete_ajax,
                    params: {param: param},
                    method: 'post',
                    timeout: 3000000,
                    success: function(response) {
                        var text = response.responseText;
                        Ext.Msg.alert("Mensaje",text);
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
        alert('Seleccione por lo menos un registro de la lista');
    }
}
