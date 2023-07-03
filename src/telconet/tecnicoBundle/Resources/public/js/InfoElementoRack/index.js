Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();

    var storeMarcas = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url: url_getMarcasElementosTipo,
            extraParams: {
                idMarca: '',
                tipoElemento: 'RACK'
            },
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'nombreMarcaElemento', mapping: 'nombreMarcaElemento'},
                {name: 'idMarcaElemento', mapping: 'idMarcaElemento'}
            ]
    });

    storeModelos = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url: url_getModelosElementosPorMarca,
            extraParams: {
                idMarca: '',
                tipoElemento: 'RACK'
            },
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'nombreModeloElemento', mapping: 'nombreModeloElemento'},
                {name: 'idModeloElemento', mapping: 'idModeloElemento'}
            ]
    });

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

    var storeNodo = new Ext.data.Store({
        total: 'total',
        proxy: {
            timeout: 400000,
            type: 'ajax',
            url: url_getEncontradosNodo,
            extraParams: {
                nombreElemento: '',
                modeloElemento: '',
                marcaElemento: '',
                canton: '',
                jurisdiccion: '',
                estado: 'Todos'
            },
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
                {name: 'idElemento', mapping: 'idElemento'},
                {name: 'nombreElemento', mapping: 'nombreElemento'}
            ]
    });

    store = new Ext.data.Store({
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
            url: url_getEncontradosRack,
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
                estado: 'Todos'
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
                {name: 'action3', mapping: 'action3'}
            ],
    });

    var pluginExpanded = true;

    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    })

    function administrarUnidades(data)
    {
        Ext.define('estados', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'opcion', type: 'string'},
                {name: 'valor', type: 'string'}
            ]
        });

        var storeUnidades = new Ext.data.Store({
            total: 'total',
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: url_getUnidadesElemento,
                extraParams: {idElemento: data.idElemento},
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                limitParam: undefined,
                startParam: undefined
            },
            fields:
                [
                    {name: 'idElemento', mapping: 'idElemento'},
                    {name: 'nombreElemento', mapping: 'nombreElemento'},
                    {name: 'estado', mapping: 'estado'},
                    {name: 'nombreElementoUnidad', mapping: 'nombreElementoUnidad'}
                ]
        });

        gridAdministracionUnidades = Ext.create('Ext.grid.Panel', {
            id: 'gridAdministracionPuertos',
            store: storeUnidades,
            columnLines: true,
            columns: [{
                    id: 'idElemento',
                    header: 'idElemento',
                    dataIndex: 'idElemento',
                    hidden: true,
                    hideable: false
                }, {
                    id: 'nombreElemento',
                    header: 'Unidad Elemento',
                    dataIndex: 'nombreElemento',
                    width: 170,
                    hidden: false,
                    hideable: false
                }, {
                    id: 'estado',
                    header: 'Estado',
                    dataIndex: 'estado',
                    width: 130,
                    sortable: true
                },
                {
                    id: 'nombreElementoUnidad',
                    header: 'Nombre de Elemento',
                    dataIndex: 'nombreElementoUnidad',
                    width: 450,
                    hidden: false,
                    hideable: false
                }
            ],
            viewConfig: {
                stripeRows: true,
                enableTextSelection: true
            }
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
                columns: 1
            },
            defaults: {
                // applied to each contained panel
                bodyStyle: 'padding:20px'
            },
            items: [
                //hidden json
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
                    title: 'Informacion del Elemento',
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
                    title: 'Unidades',
                    defaultType: 'textfield',
                    defaults: {
                        width: 500,
                        height: 200
                    },
                    items: [
                        gridAdministracionUnidades

                    ]
                }, //cierre interfaces cpe
            ], //cierre items
            buttons: [{
                    text: 'Salir',
                    handler: function() {
                        win.destroy();
                    }
                }]
        });

        var win = Ext.create('Ext.window.Window', {
            title: 'Administracion de Unidades',
            modal: true,
            width: 550,
            closable: true,
            layout: 'fit',
            items: [formPanel]
        }).show();
    }

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
                header: 'Rack',
                xtype: 'templatecolumn',
                width: 290,
                tpl: '<span class="box-detalle">{nombreElemento}</span>\n\
                      <span class="bold">Jurisdiccion:</span><span>{jurisdiccionNombre}</span></br>\n\
                      <span class="bold">Canton:</span><span>{cantonNombre}</span></br>\n\\n\
                      <span class="bold">Nodo:</span><span>{nombreElementoNodo}</span></br>\n\
                      <tpl if="switchTelconet!=\'N/A\'">\n\
                      <!--<span class="bold">Switch:</span>{switchTelconet}</br>--> \n\
                      <!--<span class="bold">Puerto:</span>{puertoSwitch}-->\n\
                      </tpl>'

            },
            {
                header: 'Marca',
                dataIndex: 'marcaElemento',
                width: 145,
                sortable: true
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
                    //VER RACK
                    {
                        getClass: function(v, meta, rec) {
                            var permiso = $("#ROLE_273-6");
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
                            var permiso = $("#ROLE_273-6");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                rec.data.action1 = "icon-invisible";
                            }
                            if (rec.get('action1') != "icon-invisible")
                                window.location = "" + rec.get('idElemento') + "/showRack";
                            else
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                        }
                    },
                    //EDITAR RACK
                    {
                        getClass: function(v, meta, rec) {
                            var permiso = $("#ROLE_273-4");
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
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            if (rec.get('action2') != "button-grid-invisible")
                                window.location = "" + rec.get('idElemento') + "/editRack";
                        }
                    },
                    //ELIMINAR RACK
                    {
                        getClass: function(v, meta, rec) {
                            var permiso = $("#ROLE_273-8");
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
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            if (rec.get('action3') != "button-grid-invisible")
                                Ext.Msg.confirm('Alerta', 'Se eliminara el registro. Desea continuar?', function(btn) {
                                    if (btn == 'yes') {
                                        Ext.Ajax.request({
                                            url: url_deleteRack,
                                            method: 'post',
                                            params: {param: rec.get('idElemento')},
                                            success: function(response) {

                                                var text = response.responseText;
                                                if (text == "UNIDADES OCUPADAS") {
                                                    Ext.Msg.alert('Mensaje', 'NO SE PUEDE ELIMINAR EL ELEMENTO PORQUE AUN EXISTEN UNIDADES OCUPADAS, FAVOR REVISAR!', function(btn) {
                                                        if (btn == 'ok') {
                                                            ;
                                                            store.load();
                                                        }
                                                    });
                                                }
                                                else
                                                {
                                                    if (text == "PROBLEMAS TRANSACCION")
                                                    {
                                                        Ext.Msg.alert('Mensaje', 'Existieron problemas al procesar la transaccion, favor notificar a Sistemas.', function(btn) {
                                                            if (btn == 'ok') {
                                                                ;
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
                                            failure: function(result)
                                            {
                                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                            }
                                        });
                                    }
                                });
                        }
                    },
                    {
                        getClass: function(v, meta, rec) {
                            if (rec.get('estado') != "Eliminado") {
                                var permiso = $("#ROLE_273-2137");
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
                        tooltip: 'Administrar Unidades',
                        handler: function(grid, rowIndex, colIndex) {
                            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                            if (grid.getStore().getAt(rowIndex).data.estado != "Eliminado") {
                                administrarUnidades(grid.getStore().getAt(rowIndex).data);
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
            //-------------------------------------

            {width: '10%', border: false}, //inicio
            {
                id: 'sltMarca',
                fieldLabel: 'Marca',
                xtype: 'combobox',
                store: storeMarcas,
                displayField: 'nombreMarcaElemento',
                valueField: 'idMarcaElemento',
                loadingText: 'Buscando ...',
                queryMode: 'local',
                listClass: 'x-combo-list-small',
                listeners: {
                    select: function(combo) {
                        cargarModelos(combo.getValue());
                    }
                }, //cierre listener
                width: '30%'
            },
            {width: '20%', border: false}, //medio
            {
                xtype: 'combobox',
                id: 'sltModelo',
                fieldLabel: 'Modelo',
                store: storeModelos,
                displayField: 'nombreModeloElemento',
                valueField: 'idModeloElemento',
                loadingText: 'Buscando ...',
                listClass: 'x-combo-list-small',
                queryMode: 'local',
                width: '30%'
            },
            {width: '10%', border: false}, //final

            //-------------------------------------

            {width: '10%', border: false}, //inicio
            {
                xtype: 'combobox',
                id: 'sltCanton',
                fieldLabel: 'Canton',
                displayField: 'nombre_canton',
                valueField: 'id_canton',
                loadingText: 'Buscando ...',
                store: storeCantones,
                listClass: 'x-combo-list-small',
                queryMode: 'local',
                width: '30%'
            },
            {width: '20%', border: false}, //medio
            {
                xtype: 'combobox',
                id: 'sltJurisdiccion',
                fieldLabel: 'Jurisidiccion',
                store: storeJurisdicciones,
                displayField: 'nombreJurisdiccion',
                valueField: 'idJurisdiccion',
                loadingText: 'Buscando ...',
                listClass: 'x-combo-list-small',
                queryMode: 'local',
                width: '30%'
            },
            {width: '10%', border: false}, //final

            //-------------------------------------

            {width: '10%', border: false}, //inicio
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
            {width: '20%', border: false}, //medio
            {
                xtype: 'combobox',
                id: 'sltNodo',
                fieldLabel: 'Nodo',
                store: storeNodo,
                displayField: 'nombreElemento',
                valueField: 'idElemento',
                loadingText: 'Buscando ...',
                listClass: 'x-combo-list-small',
                queryMode: 'local',
                width: '30%'
            },
            {width: '10%', border: false}, //final


        ],
        renderTo: 'filtro'
    });

    store.load({
        callback: function() {
            storeMarcas.load({
                // store loading is asynchronous, use a load listener or callback to handle results
                callback: function() {
                    storeModelos.load({
                        callback: function() {
                            storeCantones.load({
                                callback: function() {
                                    storeJurisdicciones.load({
                                        callback: function() {
                                            storeNodo.load({ 
                                            });
                                        }
                                    });
                                }
                            });
                        }
                    });
                }
            });
        }
    });

});

function cargarModelos(idParam) {
    storeModelos.proxy.extraParams = {idMarca: idParam, tipoElemento: 'RACK', limite: 100};
    storeModelos.load({params: {}});
}

function buscar() {
    store.getProxy().extraParams.nombreElemento = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.marcaElemento = Ext.getCmp('sltMarca').value;
    store.getProxy().extraParams.modeloElemento = Ext.getCmp('sltModelo').value;
    store.getProxy().extraParams.canton = Ext.getCmp('sltCanton').value;
    store.getProxy().extraParams.jurisdiccion = Ext.getCmp('sltJurisdiccion').value;
    store.getProxy().extraParams.popElemento = Ext.getCmp('sltNodo').value;
    store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
    store.load();

}

function limpiar() {
    Ext.getCmp('txtNombre').value = "";
    Ext.getCmp('txtNombre').setRawValue("");

    Ext.getCmp('sltMarca').value = "";
    Ext.getCmp('sltMarca').setRawValue("");

    Ext.getCmp('sltModelo').value = "";
    Ext.getCmp('sltModelo').setRawValue("");

    Ext.getCmp('sltCanton').value = "";
    Ext.getCmp('sltCanton').setRawValue("");

    Ext.getCmp('sltJurisdiccion').value = "";
    Ext.getCmp('sltJurisdiccion').setRawValue("");

    Ext.getCmp('sltNodo').value = "";
    Ext.getCmp('sltNodo').setRawValue("");

    Ext.getCmp('sltEstado').value = "Todos";
    Ext.getCmp('sltEstado').setRawValue("Todos");
    store.load({params: {
            nombreElemento: Ext.getCmp('txtNombre').value,
            marcaElemento: Ext.getCmp('sltMarca').value,
            modeloElemento: Ext.getCmp('sltModelo').value,
            canton: Ext.getCmp('sltCanton').value,
            jurisdiccion: Ext.getCmp('sltJurisdiccion').value,
            popElemento: Ext.getCmp('sltNodo').value,
            estado: Ext.getCmp('sltEstado').value
        }});
}