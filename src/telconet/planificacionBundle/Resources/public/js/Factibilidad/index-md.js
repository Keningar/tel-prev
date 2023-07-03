/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();

    //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		
    DTFechaDesdePlanif = new Ext.form.DateField({
        id: 'fechaDesdePlanif',
        fieldLabel: 'Desde',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 325,
        editable: false
    });
    DTFechaHastaPlanif = new Ext.form.DateField({
        id: 'fechaHastaPlanif',
        fieldLabel: 'Hasta',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 325,
        editable: false
    });
    DTFechaDesdeIngOrd = new Ext.form.DateField({
        id: 'fechaDesdeIngOrd',
        fieldLabel: 'Desde',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 325,
        editable: false
    });
    DTFechaHastaIngOrd = new Ext.form.DateField({
        id: 'fechaHastaIngOrd',
        fieldLabel: 'Hasta',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 325,
        editable: false
    });

    store = new Ext.data.Store({
        pageSize: 14,
        total: 'total',
        proxy: {
            timeout: 3000000,
            type: 'ajax',
            url: 'ajaxGrid',
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
        fields:
            [
                {name: 'id_factibilidad', mapping: 'id_factibilidad'},
                {name: 'estadoFactibilidad', mapping: 'estadoFactibilidad'},
                {name: 'id_servicio', mapping: 'id_servicio'},
                {name: 'tipo_orden', mapping: 'tipo_orden'},
                {name: 'id_punto', mapping: 'id_punto'},
                {name: 'observacion', mapping: 'observacion'},
                {name: 'id_orden_trabajo', mapping: 'id_orden_trabajo'},
                {name: 'ultimaMilla', mapping: 'ultimaMilla'},
                {name: 'cliente', mapping: 'cliente'},
                {name: 'esRecontratacion', mapping: 'esRecontratacion'},
                {name: 'vendedor', mapping: 'vendedor'},
                {name: 'login2', mapping: 'login2'},
                {name: 'producto', mapping: 'producto'},
                {name: 'coordenadas', mapping: 'coordenadas'},
                {name: 'direccion', mapping: 'direccion'},
                {name: 'ciudad', mapping: 'ciudad'},
                {name: 'jurisdiccion', mapping: 'jurisdiccion'},
                {name: 'nombreSector', mapping: 'nombreSector'},
                {name: 'fePlanificacion', mapping: 'fePlanificacion'},
                {name: 'rutaCroquis', mapping: 'rutaCroquis'},
                {name: 'latitud', mapping: 'latitud'},
                {name: 'longitud', mapping: 'longitud'},
                {name: 'estado', mapping: 'estado'},
                {name: 'action1', mapping: 'action1'},
                {name: 'action2', mapping: 'action2'},
                {name: 'action3', mapping: 'action3'},
                {name: 'action4', mapping: 'action4'},
                {name: 'action5', mapping: 'action5'}
            ],
        autoLoad: true
    });

    var pluginExpanded = true;

    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    })


    grid = Ext.create('Ext.grid.Panel', {
        width: 1230,
        height: 500,
        store: store,
        loadMask: true,
        frame: false,
        columns: [
            {
                id: 'id_factibilidad',
                header: 'IdFactibilidad',
                dataIndex: 'id_factibilidad',
                hidden: true,
                hideable: false
            },
            {
                id: 'id_servicio',
                header: 'IdServicio',
                dataIndex: 'id_servicio',
                hidden: true,
                hideable: false
            },
            {
                id: 'tipo_orden',
                header: 'tipo_orden',
                dataIndex: 'tipo_orden',
                hidden: true,
                hideable: false
            },
            {
                id: 'id_punto',
                header: 'IdPunto',
                dataIndex: 'id_punto',
                hidden: true,
                hideable: false
            },
            {
                id: 'id_orden_trabajo',
                header: 'IdOrdenTrabajo',
                dataIndex: 'id_orden_trabajo',
                hidden: true,
                hideable: false
            },
            {
                id: 'esRecontratacion',
                header: 'esRecontratacion',
                dataIndex: 'esRecontratacion',
                hidden: true,
                hideable: false
            },
            {
                id: 'ultimaMilla',
                header: 'ultimaMilla',
                dataIndex: 'ultimaMilla',
                hidden: true,
                hideable: false
            },
            {
                id: 'cliente',
                header: 'Cliente',
                dataIndex: 'cliente',
                width: 140,
                sortable: true
            },
            {
                id: 'vendedor',
                header: 'Vendedor',
                dataIndex: 'vendedor',
                width: 115,
                sortable: true
            },
            {
                id: 'login2',
                header: 'Login',
                dataIndex: 'login2',
                width: 105,
                sortable: true
            },
            {
                id: 'observacion',
                header: 'Observacion',
                dataIndex: 'observacion',
                width: 80,
                sortable: true
            },
            {
                id: 'producto',
                header: 'Producto',
                dataIndex: 'producto',
                width: 120,
                sortable: true
            },
            {
                id: 'jurisdiccion',
                header: 'Jurisdiccion',
                dataIndex: 'jurisdiccion',
                width: 80,
                sortable: true
            },
            {
                id: 'ciudad',
                header: 'Ciudad',
                dataIndex: 'ciudad',
                width: 65,
                sortable: true
            },
            {
                id: 'direccion',
                header: 'Direccion',
                dataIndex: 'direccion',
                width: 120,
                sortable: true
            },
            {
                id: 'nombreSector',
                header: 'Sector',
                dataIndex: 'nombreSector',
                width: 80,
                sortable: true
            },
            {
                id: 'fePlanificacion',
                header: 'Fecha Solicita Planificacion',
                dataIndex: 'fePlanificacion',
                width: 95,
                sortable: true
            },
            {
                id: 'estadoFactibilidad',
                header: 'Estado',
                dataIndex: 'estadoFactibilidad',
                width: 95,
                sortable: true
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 130,
                items: [
                    {
                        getClass: function(v, meta, rec) {
                            return rec.get('action1')
                        },
                        tooltip: 'Ver Mapa',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);

                            if (rec.get("latitud") != 0 && rec.get("longitud") != 0)
                                showVerMapa(rec);
                            else
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: 'Las coordenadas son incorrectas',
                                    buttons: Ext.MessageBox.OK,
                                    icon: Ext.MessageBox.ERROR
                                });
                        }
                    },
                    {
                        getClass: function(v, meta, rec) {
                            return rec.get('action2')
                        },
                        tooltip: 'Ver Croquis',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);

                            if (rec.get("id_factibilidad") != "" && rec.get("rutaCroquis") != "")
                                showVerCroquis(rec.get('id_factibilidad'), rec.get('rutaCroquis'));
                            else
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: 'Las ruta no existe',
                                    buttons: Ext.MessageBox.OK,
                                    icon: Ext.MessageBox.ERROR
                                });
                        }
                    },
                    {
                        getClass: function(v, meta, rec) {
                            var permiso = $("#ROLE_135-94");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                rec.data.action3 = "icon-invisible";
                            }

                            if (rec.get('action3') == "icon-invisible")
                                this.items[2].tooltip = '';
                            else
                                this.items[2].tooltip = 'Rechazar Orden';

                            return rec.get('action3')
                        },
                        tooltip: 'Rechazar Orden',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);

                            var permiso = $("#ROLE_135-94");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                rec.data.action3 = "icon-invisible";
                            }

                            if (rec.get('action3') != "icon-invisible")
                                showRechazarOrden_Factibilidad(rec);
                            else
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: 'No tiene permisos para realizar esta accion',
                                    buttons: Ext.MessageBox.OK,
                                    icon: Ext.MessageBox.ERROR
                                });
                        }
                    },
                    {
                        getClass: function(v, meta, rec) {
                            var permiso = $("#ROLE_135-95");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                rec.data.action4 = "icon-invisible";
                            }

                            if (rec.get('action4') == "icon-invisible")
                                this.items[3].tooltip = '';
                            else
                                this.items[3].tooltip = 'Fecha Factibilidad';

                            return rec.get('action4')
                        },
                        tooltip: 'Fecha Factibilidad',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);

                            var permiso = $("#ROLE_135-95");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                rec.data.action4 = "icon-invisible";
                            }

                            if (rec.get('action4') != "icon-invisible")
                                showPreFactibilidad(rec);
                            else
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: 'No tiene permisos para realizar esta accion',
                                    buttons: Ext.MessageBox.OK,
                                    icon: Ext.MessageBox.ERROR
                                });
                        }
                    },
                    {
                        getClass: function(v, meta, rec) {
                            var permiso1 = $("#ROLE_135-89");
                            var boolPermiso1 = (typeof permiso1 === 'undefined') ? false : (permiso1.val() == 1 ? true : false);
                            var permiso2 = $("#ROLE_135-96");
                            var boolPermiso2 = (typeof permiso2 === 'undefined') ? false : (permiso2.val() == 1 ? true : false);
                            var permiso3 = $("#ROLE_135-2717");
                            var boolPermiso3 = (typeof permiso3 === 'undefined') ? false : (permiso3.val() == 1 ? true : false);
                            if (!boolPermiso1 || !boolPermiso2 || !boolPermiso3) {
                                rec.data.action5 = "icon-invisible";
                            }

                            if (rec.get('action5') == "icon-invisible")
                                this.items[4].tooltip = '';
                            else
                                this.items[4].tooltip = 'Ingresar Factibilidad';

                            return rec.get('action5')
                        },
                        tooltip: 'Ingresar Factibilidad',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);

                            var permiso1 = $("#ROLE_135-89");
                            var boolPermiso1 = (typeof permiso1 === 'undefined') ? false : (permiso1.val() == 1 ? true : false);
                            var permiso2 = $("#ROLE_135-96");
                            var boolPermiso2 = (typeof permiso2 === 'undefined') ? false : (permiso2.val() == 1 ? true : false);
                            var permiso3 = $("#ROLE_135-2717");
                            var boolPermiso3 = (typeof permiso3 === 'undefined') ? false : (permiso3.val() == 1 ? true : false);
                            if (!boolPermiso1 || !boolPermiso2 || !boolPermiso3) {
                                rec.data.action5 = "icon-invisible";
                            }

                            if (rec.get('action5') != "icon-invisible")
                                showIngresoFactibilidad(rec);
                            else
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: 'No tiene permisos para realizar esta accion',
                                    buttons: Ext.MessageBox.OK,
                                    icon: Ext.MessageBox.ERROR
                                });
                        }
                    },
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
        border: false,
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
        width: 1230,
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
                {html: "&nbsp;", border: false, width: 200},
                {html: "Fecha Solicita Planificacion:", border: false, width: 325},
                {html: "&nbsp;", border: false, width: 150},
                {html: "Fecha Ingreso Orden:", border: false, width: 325},
                {html: "&nbsp;", border: false, width: 200},
                {html: "&nbsp;", border: false, width: 200},
                DTFechaDesdePlanif,
                {html: "&nbsp;", border: false, width: 150},
                DTFechaDesdeIngOrd,
                {html: "&nbsp;", border: false, width: 200},
                {html: "&nbsp;", border: false, width: 200},
                DTFechaHastaPlanif,
                {html: "&nbsp;", border: false, width: 150},
                DTFechaHastaIngOrd,
                {html: "&nbsp;", border: false, width: 200},
                {html: "&nbsp;", border: false, width: 200},
                {
                    xtype: 'textfield',
                    id: 'txtLogin',
                    fieldLabel: 'Login',
                    value: '',
                    width: '325'
                },
                {html: "&nbsp;", border: false, width: 150},
                {
                    xtype: 'textfield',
                    id: 'txtDescripcionPunto',
                    fieldLabel: 'Descripcion Punto',
                    value: '',
                    width: '325'
                },
                {html: "&nbsp;", border: false, width: 200},
                {html: "&nbsp;", border: false, width: 200},
                {
                    xtype: 'textfield',
                    id: 'txtVendedor',
                    fieldLabel: 'Vendedor',
                    value: '',
                    width: '325'
                },
                {html: "&nbsp;", border: false, width: 150},
                {
                    xtype: 'textfield',
                    id: 'txtCiudad',
                    fieldLabel: 'Ciudad',
                    value: '',
                    width: '325'
                },
                {html: "&nbsp;", border: false, width: 200},
                {html: "&nbsp;", border: false, width: 200},
                {
                    xtype: 'textfield',
                    id: 'txtNumOrdenServicio',
                    fieldLabel: 'Número Orden Servicio',
                    value: '',
                    width: '325'
                },
                {html: "&nbsp;", border: false, width: 150},
                {html: "&nbsp;", border: false, width: 525},
                {html: "&nbsp;", border: false, width: 200}

            ],
        renderTo: 'filtro'
    });

});


/* ******************************************* */
/*  FUNCIONES  */
/* ******************************************* */

function buscar() {
    var boolError = false;

    if ((Ext.getCmp('fechaDesdePlanif').getValue() != null) && (Ext.getCmp('fechaHastaPlanif').getValue() != null))
    {
        if (Ext.getCmp('fechaDesdePlanif').getValue() > Ext.getCmp('fechaHastaPlanif').getValue())
        {
            boolError = true;

            Ext.Msg.show({
                title: 'Error en Busqueda',
                msg: 'Por Favor para realizar la busqueda Fecha Desde Planificacion debe ser fecha menor a Fecha Hasta Planificacion.',
                buttons: Ext.Msg.OK,
                animEl: 'elId',
                icon: Ext.MessageBox.ERROR
            });
        }
    }

    if ((Ext.getCmp('fechaDesdeIngOrd').getValue() != null) && (Ext.getCmp('fechaHastaIngOrd').getValue() != null))
    {
        if (Ext.getCmp('fechaDesdeIngOrd').getValue() > Ext.getCmp('fechaHastaIngOrd').getValue())
        {
            boolError = true;

            Ext.Msg.show({
                title: 'Error en Busqueda',
                msg: 'Por Favor para realizar la busqueda Fecha Desde Ingreso Orden debe ser fecha menor a Fecha Hasta Ingreso Orden.',
                buttons: Ext.Msg.OK,
                animEl: 'elId',
                icon: Ext.MessageBox.ERROR
            });
        }
    }

    if (!boolError)
    {
        store.getProxy().extraParams.fechaDesdePlanif = Ext.getCmp('fechaDesdePlanif').value;
        store.getProxy().extraParams.fechaHastaPlanif = Ext.getCmp('fechaHastaPlanif').value;
        store.getProxy().extraParams.fechaDesdeIngOrd = Ext.getCmp('fechaDesdeIngOrd').value;
        store.getProxy().extraParams.fechaHastaIngOrd = Ext.getCmp('fechaHastaIngOrd').value;
        store.getProxy().extraParams.login2 = Ext.getCmp('txtLogin').value;
        store.getProxy().extraParams.descripcionPunto = Ext.getCmp('txtDescripcionPunto').value;
        store.getProxy().extraParams.vendedor = Ext.getCmp('txtVendedor').value;
        store.getProxy().extraParams.ciudad = Ext.getCmp('txtCiudad').value;
        store.getProxy().extraParams.numOrdenServicio = Ext.getCmp('txtNumOrdenServicio').value;
        store.getProxy().extraParams.ultimaMilla = 'Fibra Optica';
        store.load();
    }
}

function limpiar() {
    Ext.getCmp('fechaDesdePlanif').setRawValue("");
    Ext.getCmp('fechaHastaPlanif').setRawValue("");
    Ext.getCmp('fechaDesdeIngOrd').setRawValue("");
    Ext.getCmp('fechaHastaIngOrd').setRawValue("");

    Ext.getCmp('txtLogin').value = "";
    Ext.getCmp('txtLogin').setRawValue("");

    Ext.getCmp('txtDescripcionPunto').value = "";
    Ext.getCmp('txtDescripcionPunto').setRawValue("");

    Ext.getCmp('txtVendedor').value = "";
    Ext.getCmp('txtVendedor').setRawValue("");

    Ext.getCmp('txtCiudad').value = "";
    Ext.getCmp('txtCiudad').setRawValue("");

    Ext.getCmp('txtNumOrdenServicio').value = "";
    Ext.getCmp('txtNumOrdenServicio').setRawValue("");

    store.getProxy().extraParams.fechaDesdePlanif = Ext.getCmp('fechaDesdePlanif').value;
    store.getProxy().extraParams.fechaHastaPlanif = Ext.getCmp('fechaHastaPlanif').value;
    store.getProxy().extraParams.fechaDesdeIngOrd = Ext.getCmp('fechaDesdeIngOrd').value;
    store.getProxy().extraParams.fechaHastaIngOrd = Ext.getCmp('fechaHastaIngOrd').value;
    store.getProxy().extraParams.login2 = Ext.getCmp('txtLogin').value;
    store.getProxy().extraParams.descripcionPunto = Ext.getCmp('txtDescripcionPunto').value;
    store.getProxy().extraParams.vendedor = Ext.getCmp('txtVendedor').value;
    store.getProxy().extraParams.ciudad = Ext.getCmp('txtCiudad').value;
    store.getProxy().extraParams.numOrdenServicio = Ext.getCmp('txtNumOrdenServicio').value;
    store.load();
}
