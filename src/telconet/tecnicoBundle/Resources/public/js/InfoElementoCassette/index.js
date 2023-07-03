Ext.onReady(function () {
    Ext.tip.QuickTipManager.init();

    var storeCantones = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url: url_getCantones,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'nombre_canton', mapping: 'nombre_canton'},
                {name: 'id_canton', mapping: 'id_canton'}
            ]
    });

    var storeJurisdicciones = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url: url_getJurisdicciones,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            limitParam: undefined,
            startParam: undefined,
        },
        fields:
            [
                {name: 'nombreJurisdiccion', mapping: 'nombreJurisdiccion'},
                {name: 'idJurisdiccion', mapping: 'idJurisdiccion'}
            ]
    });

    storeElementosCaja = new Ext.data.Store({
        pageSize: 100,
        listeners: {
            load: function () {

            }
        },
        proxy: {
            type: 'ajax',
            url: url_buscarElementoContenedor,
            timeout: 800000,
            extraParams: {
                nombreElemento: this.nombre_elemento,
                tipoElemento: 'CAJA DISPERSION'
            },
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'id_elemento', mapping: 'id_elemento'},
                {name: 'nombre_elemento', mapping: 'nombre_elemento'}
            ]
    });

    store = new Ext.data.Store({
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
            url: url_getEncontradosCassette,
            timeout: 800000,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombreElemento: '',
                ipElemento: '',
                marcaElemento: '',
                modeloElemento: '',
                canton: '',
                jurisdiccion: '',
                popElemento: '',
                estado: ''
            }
        },
        fields:
            [
                {name: 'idElemento', mapping: 'idElemento'},
                {name: 'nombreElemento', mapping: 'nombreElemento'},
                {name: 'nombreElementoNodo', mapping: 'nombreElementoNodo'},
                {name: 'cantonNombre', mapping: 'cantonNombre'},
                {name: 'jurisdiccionNombre', mapping: 'jurisdiccionNombre'},
                {name: 'switchTelconet', mapping: 'switchTelconet'},
                {name: 'puertoSwitch', mapping: 'puertoSwitch'},
                {name: 'marcaElemento', mapping: 'marcaElemento'},
                {name: 'modeloElemento', mapping: 'modeloElemento'},
                {name: 'estado', mapping: 'estado'},
                {name: 'action1', mapping: 'action1'},
                {name: 'action2', mapping: 'action2'},
                {name: 'action3', mapping: 'action3'},
                {name: 'nombreTipo', mapping: 'nombreTipo'}
            ],
    });

    var pluginExpanded = true;

    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    })


    grid = Ext.create('Ext.grid.Panel', {
        width: 930,
        height: 350,
        store: store,
        loadMask: true,
        frame: false,
        viewConfig: {enableTextSelection: true},
        columns: [
            {
                id: 'idElemento',
                header: 'idElemento',
                dataIndex: 'idElemento',
                hidden: true,
                hideable: false
            },
            {
                id: 'ipElemento',
                header: 'Cassette',
                xtype: 'templatecolumn',
                width: 290,
                tpl: '<span class="box-detalle">{nombreElemento}</span>\n\
                        <span class="bold">Jurisdiccion:</span><span>{jurisdiccionNombre}</span></br>\n\
                        <span class="bold">Canton:</span><span>{cantonNombre}</span></br>\n\\n\
                        <span class="bold">Caja:</span><span>{nombreElementoNodo}</span></br>\n\
                        '

            },
            {
                header: 'Modelo',
                dataIndex: 'modeloElemento',
                width: 120,
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
                width: 275,
                items: [
                    //VER CASSETTE
                    {
                        getClass: function (v, meta, rec) {
                            var permiso = $("#ROLE_321-6");
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
                        handler: function (grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            var permiso = $("#ROLE_321-6");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                rec.data.action1 = "icon-invisible";
                            }
                            if (rec.get('action1') != "icon-invisible")
                                window.location = "" + rec.get('idElemento') + "/showCassette";
                            else
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                        }
                    },
                    //EDITAR CASSETTE
                    {
                        getClass: function (v, meta, rec) {
                            var permiso = $("#ROLE_321-4");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                return 'button-grid-invisible';
                            } 
                            else {
                                if (rec.get('action2') == "button-grid-invisible")
                                    this.items[1].tooltip = '';
                                else
                                    this.items[1].tooltip = 'Editar';
                            }


                            return rec.get('action2')
                        },
                        handler: function (grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            if (rec.get('action2') != "button-grid-invisible")
                                window.location = "" + rec.get('idElemento') + "/editCassette";
                        }
                    },
                    //ELIMINAR CASSETTE
                    {
                        getClass: function (v, meta, rec) {
                            var permiso = $("#ROLE_321-8");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                return 'button-grid-invisible';
                            }
                            else {
                                if (rec.get('action3') == "button-grid-invisible")
                                    this.items[2].tooltip = '';
                                else
                                    this.items[2].tooltip = 'Eliminar';
                            }


                            return rec.get('action3')
                        },
                        handler: function (grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            if (rec.get('action3') != "button-grid-invisible")
                                Ext.Msg.confirm('Alerta', 'Se eliminara el registro. Desea continuar?', function (btn) {
                                    if (btn == 'yes') {
                                        Ext.Ajax.request({
                                            url: url_deleteCassette,
                                            method: 'post',
                                            params: {param: rec.get('idElemento')},
                                            success: function (response) {

                                                var text = response.responseText;
                                                if (text == "INTERFACES EN USO") {
                                                    Ext.Msg.alert(
                                                        'Mensaje',
                                                        'NO SE PUEDE ELIMINAR EL ELEMENTO PORQUE AUN EXISTEN INTERFACES EN USO, FAVOR REVISAR!',
                                                        function (btn) {
                                                            if (btn == 'ok') {
                                                                store.load();
                                                            }
                                                        });
                                                } 
                                                else
                                                {
                                                    if (text == "PROBLEMAS TRANSACCION")
                                                    {
                                                        Ext.Msg.alert(
                                                            'Mensaje',
                                                            'Existieron problemas al procesar la transaccion, favor notificar a Sistemas.',
                                                            function (btn) {
                                                                if (btn == 'ok') {
                                                                    store.load();
                                                                }
                                                            });
                                                    }
                                                    else
                                                    {
                                                        store.load();
                                                    }
                                                }


                                            },
                                            failure: function (result)
                                            {
                                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                            }
                                        });
                                    }
                                });
                        }
                    },
                    //PUERTOS CASSETE
                    {
                        getClass: function (v, meta, rec) {
                            if (rec.get('estado') != "Eliminado") {
                                var permiso = $("#ROLE_321-3317");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                if (!boolPermiso) {
                                    return 'button-grid-invisible';
                                }
                                else {
                                    return 'button-grid-administrarPuertos';
                                }

                            }
                            else {
                                return 'button-grid-invisible';
                            }

                        },
                        tooltip: 'Administrar Puertos',
                        handler: function (grid, rowIndex, colIndex) {
                            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                            if (grid.getStore().getAt(rowIndex).data.estado != "Eliminado") {
                                administrarPuertos(grid.getStore().getAt(rowIndex).data);
                            }
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
        width: 930,
        title: 'Criterios de busqueda',
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function () {
                    buscar();
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function () {
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
                width: '100%'
            },
            {width: '10%', border: false},
            {
                xtype: 'combobox',
                id: 'sltCanton',
                fieldLabel: 'Cantón',
                displayField: 'nombre_canton',
                valueField: 'id_canton',
                loadingText: 'Buscando ...',
                store: storeCantones,
                listClass: 'x-combo-list-small',
                queryMode: 'local',
                width: '40%'
            },
            {width: '5%', border: false},
            {width: '5%', border: false},
            {
                xtype: 'combobox',
                id: 'sltJurisdiccion',
                fieldLabel: 'Jurisdicción',
                store: storeJurisdicciones,
                displayField: 'nombreJurisdiccion',
                valueField: 'idJurisdiccion',
                loadingText: 'Buscando ...',
                listClass: 'x-combo-list-small',
                queryMode: 'local',
                listeners: {
                    select: function (combo) {
                        cargarCajas(combo.getValue());
                    }
                }, //cierre listener
                width: '100%'
            },
            {width: '10%', border: false},
            {
                xtype: 'combobox',
                fieldLabel: 'Estado',
                id: 'sltEstado',
                value: 'Seleccione',
                store: [
                    ['Seleccione', '--Seleccione--'],
                    ['Todos', 'Todos'],
                    ['Activo', 'Activo'],
                    ['Modificado', 'Modificado'],
                    ['Eliminado', 'Eliminado']
                ],
                width: '40%'
            },
            {width: '5%', border: false},
            {width: '5%', border: false},
            {
                xtype: 'combobox',
                id: 'sltNodo',
                fieldLabel: 'Caja',
                store: storeElementosCaja,
                displayField: 'nombre_elemento',
                valueField: 'id_elemento',
                loadingText: 'Buscando ...',
                listClass: 'x-combo-list-small',
                queryMode: 'remote',
                width: '100%'
            },
            {width: '10%', border: false},
        ],
        renderTo: 'filtro'
    });

    store.load({
        callback: function () {
            storeCantones.load({
                // store loading is asynchronous, use a load listener or callback to handle results
                callback: function () {
                    storeJurisdicciones.load({
                        callback: function () {
                            storeElementosCaja.load({
                                callback: function () {
                                }
                            });
                        }
                    });
                }
            });
        }
    });

    function administrarPuertos(data) {

        var storeInterfaces = new Ext.data.Store({
            total: 'total',
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: url_interfaceCassette,
                extraParams: {idElemento: data.idElemento, tipo: 'CASSETTE'},
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            fields:
                [
                    {name: 'idInterfaceElemento', mapping: 'idInterfaceElemento'},
                    {name: 'nombreInterfaceElemento', mapping: 'nombreInterfaceElemento'},
                    {name: 'estado', mapping: 'estado'},
                    {name: 'login', mapping: 'login'},
                    {name: 'colorHilo', mapping: 'colorHilo'}
                ]
        });


        var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 2,
            listeners: {
                edit: function () {
                    // refresh summaries
                    gridAdministracionPuertos.getView().refresh();
                }
            }
        });

        gridAdministracionPuertos = Ext.create('Ext.grid.Panel', {
            id: 'gridAdministracionPuertos',
            store: storeInterfaces,
            columnLines: true,
            columns: [{
                    id: 'idInterfaceElemento',
                    header: 'idInterfaceElemento',
                    dataIndex: 'idInterfaceElemento',
                    hidden: true,
                    hideable: false
                }, {
                    id: 'nombreInterfaceElemento',
                    header: 'Interface Elemento',
                    dataIndex: 'nombreInterfaceElemento',
                    width: 150,
                    hidden: false,
                    hideable: false
                }, {
                    id: 'estado',
                    header: 'Estado',
                    dataIndex: 'estado',
                    width: 150,
                    sortable: true,
                }, {
                    id: 'login',
                    header: 'Login',
                    dataIndex: 'login',
                    width: 250,
                    hidden: false,
                    hideable: false
                }
            ],
            width: 550,
            height: 250,
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
                columns: 1
            },
            defaults: {
                bodyStyle: 'padding:20px'
            },
            items: [
                {
                    xtype: 'hidden',
                    id: 'jsonInterfaces',
                    name: 'jsonInterfaces',
                    fieldLabel: '',
                    displayField: '',
                    value: '',
                    readOnly: true,
                    width: '30%'

                }, //cierre hidden

                //elemento
                {
                    xtype: 'fieldset',
                    title: 'Información del Elemento',
                    defaultType: 'textfield',
                    defaults: {
                        width: 300,
                        height: 20
                    },
                    items: [
                        {
                            xtype: 'container',
                            layout: {
                                type: 'table',
                                columns: 1,
                                align: 'stretch'
                            },
                            items: [
                                {
                                    xtype: 'textfield',
                                    id: 'elemento',
                                    name: 'elemento',
                                    fieldLabel: 'Elemento',
                                    displayField: data.nombreElemento,
                                    value: data.nombreElemento,
                                    readOnly: true,
                                    width: '200%'
                                }

                                //---------------------------------------

                            ]//cierre del container table
                        }


                    ]//cierre del fieldset
                }, //cierre informacion ont

                {
                    xtype: 'fieldset',
                    title: 'Puertos',
                    defaultType: 'textfield',
                    defaults: {
                        width: 500,
                        height: 200
                    },
                    items: [
                        gridAdministracionPuertos

                    ]
                }, //cierre interfaces cpe
            ], //cierre items
            buttons: [{
                    text: 'Cancelar',
                    handler: function () {
                        win.destroy();
                    }
                }]
        });

        var win = Ext.create('Ext.window.Window', {
            title: 'Administración de Puertos',
            modal: true,
            width: 600,
            closable: true,
            layout: 'fit',
            resizable: false,
            items: [formPanel]
        }).show();

    }


});

function buscar() {
    store.getProxy().extraParams.nombreElemento = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.canton = Ext.getCmp('sltCanton').value;
    store.getProxy().extraParams.jurisdiccion = Ext.getCmp('sltJurisdiccion').value;
    store.getProxy().extraParams.popElemento = Ext.getCmp('sltNodo').value;
    store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
    store.load();

}

function limpiar() {
    Ext.getCmp('txtNombre').value = "";
    Ext.getCmp('txtNombre').setRawValue("");

    Ext.getCmp('sltCanton').value = "";
    Ext.getCmp('sltCanton').setRawValue("");

    Ext.getCmp('sltJurisdiccion').value = "";
    Ext.getCmp('sltJurisdiccion').setRawValue("");

    Ext.getCmp('sltNodo').value = "";
    Ext.getCmp('sltNodo').setRawValue("");

    Ext.getCmp('sltEstado').value = "--Seleccione--";
    Ext.getCmp('sltEstado').setRawValue("--Seleccione--");
    store.load({params: {
            nombreElemento: Ext.getCmp('txtNombre').value,
            canton: Ext.getCmp('sltCanton').value,
            jurisdiccion: Ext.getCmp('sltJurisdiccion').value,
            popElemento: Ext.getCmp('sltNodo').value,
            estado: Ext.getCmp('sltEstado').value
        }});
}

function cargarCajas(idParams) {
    Ext.getCmp('sltNodo').reset();

    storeElementosCaja.proxy.extraParams = {
        nombreElemento: this.nombre_elemento,
        tipoElemento: 'CAJA DISPERSION',
        estado: 'Activo',
        jurisdiccion: Ext.getCmp('sltJurisdiccion').value
    };
    storeElementosCaja.load({
        callback: function () {
        }
    });
}