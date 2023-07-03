/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.onReady(function () {
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

    store = new Ext.data.Store({
        pageSize: 14,
        total: 'total',
        proxy: {
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
                txtNodo: '',
                txtEstado: '',
                txtUser: '',
                id_jurisdiccion: '',
                limite: true
            }
        },
        fields:
            [
                { name: 'idElemento', mapping: 'idElemento' },
                { name: 'idSolicitud', mapping: 'idSolicitud' },
                { name: 'nombreElemento', mapping: 'nombreElemento' },
                { name: 'direccion', mapping: 'direccion' },
                { name: 'latitud', mapping: 'latitud' },
                { name: 'longitud', mapping: 'longitud' },
                { name: 'feCreacion', mapping: 'feCreacion' },
                { name: 'canton', mapping: 'canton' },
                { name: 'idCanton', mapping: 'idCanton' },
                { name: 'modeloElemento', mapping: 'modeloElemento' },
                { name: 'id_jurisdiccion', mapping: 'id_jurisdiccion' },
                { name: 'jurisdiccion', mapping: 'jurisdiccion' },
                { name: 'estado', mapping: 'estado' },
                { name: 'administra', mapping: 'administra' },
                { name: 'usrCreacion', mapping: 'usrCreacion' }
            ],
        autoLoad: true
    });


    storeJurisdicciones = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url: url_ajaxGridCombo,
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
                txtNodo: '',
                txtEstado: '',
                txtUser: '',
                limite: false
            }
        },
        fields:
            [
                { name: 'id_jurisdiccion', mapping: 'id_jurisdiccion' },
                { name: 'jurisdiccion', mapping: 'jurisdiccion' }
            ],
        listeners: {
            load: function (store) {
                // using a map of already used names
                const hits = {}
                store.filterBy(record => {
                    const name = record.get('id_jurisdiccion')
                    if (hits[name]) {
                        return false
                    } else {
                        hits[name] = true
                        return true
                    }
                });
            }
        },

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
                id: 'idSolicitud',
                header: 'idSolicitud',
                dataIndex: 'idSolicitud',
                hidden: true,
                hideable: false
            },
            {
                id: 'nombreElemento',
                header: 'Nombre',
                dataIndex: 'nombreElemento',
                width: 160,
                sortable: true
            },
            {
                id: 'direccion',
                header: 'Direccion',
                dataIndex: 'direccion',
                width: 200,
                sortable: true
            },
            {
                id: 'latitud',
                header: 'Latitud',
                dataIndex: 'latitud',
                width: 80,
                sortable: true
            },
            {
                id: 'longitud',
                header: 'Longitud',
                dataIndex: 'longitud',
                width: 80,
                sortable: true
            },
            {
                id: 'feCreacion',
                header: 'Fecha Creación',
                dataIndex: 'feCreacion',
                width: 120,
                sortable: true
            },
            {
                id: 'canton',
                header: 'Cantón',
                dataIndex: 'canton',
                width: 80,
                sortable: true
            },
            {
                id: 'jurisdiccion',
                header: 'Jurisdiccion',
                dataIndex: 'jurisdiccion',
                width: 100,
                sortable: true
            },
            {
                id: 'modeloElemento',
                header: 'Tipo',
                dataIndex: 'modeloElemento',
                width: 100,
                sortable: true
            },
            {
                id: 'estado',
                header: 'Estado',
                dataIndex: 'estado',
                width: 80,
                sortable: true
            },
            {
                id: 'usrCreacion',
                header: 'User Creación',
                dataIndex: 'usrCreacion',
                width: 80,
                sortable: true
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 130,
                items: [
                    {
                        getClass: function (v, meta, rec) {
                            return 'button-grid-Gmaps';
                        },
                        tooltip: 'Ver Mapa',
                        handler: function (grid, rowIndex, colIndex) {
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
                        getClass: function (v, meta, rec) {
                            var permiso = $("#ROLE_323-3358");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (boolPermiso) {
                                if (rec.get('estado') == 'PreFactibilidad' || rec.get('estado') == 'FactibilidadEnProceso') {
                                    return 'button-grid-BigDelete';
                                }
                                else {
                                    return "icon-invisible";
                                }
                            }
                        },
                        tooltip: 'Rechazar',
                        handler: function (grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            rechazarFactibilidadNodoCliente(rec);
                        }
                    },
                    {
                        getClass: function (v, meta, rec) {
                            var permiso = $("#ROLE_323-3357");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (boolPermiso) {
                                if (rec.get('estado') == 'PreFactibilidad') {
                                    return 'button-grid-Time2';
                                }
                                else {
                                    return "icon-invisible";
                                }
                            }
                        },
                        tooltip: 'Fecha Factibilidad',
                        handler: function (grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            showPreFactibilidad(rec);
                        }
                    },
                    {
                        getClass: function (v, meta, rec) {
                            var permiso = $("#ROLE_323-3359");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (boolPermiso) {
                                if (rec.get('estado') == 'FactibilidadEnProceso') {
                                    return 'button-grid-Check';
                                }
                                else {
                                    return "icon-invisible";
                                }
                            }
                        },
                        tooltip: 'Ingresar Factibilidad',
                        handler: function (grid, rowIndex, colIndex) {
                            var tipoAdministracion;
                            var rec = store.getAt(rowIndex);
                            //valido la empresa
                            if (prefijoEmpresa == 'TN') {
                                Ext.Msg.confirm('Mensaje', 'Es administrado por Telconet?', function (btn) {
                                    if (btn === 'no') {
                                        Ext.MessageBox.show({
                                            title: 'Edificio',
                                            icon: Ext.Msg.QUESTION,
                                            msg: 'Por favor escoja la Administración del Edificio',
                                            buttonText: { yes: "Propia", no: "Tercerizada" },
                                            fn: function (btn) {
                                                if (btn === 'yes') {
                                                    tipoAdministracion = 'PROPIA';
                                                }
                                                else if (btn === 'no') {
                                                    tipoAdministracion = 'TERCERIZADA';
                                                }
                                                else {
                                                    return;
                                                }

                                                if (tipoAdministracion === 'TERCERIZADA') {
                                                    //Si es tercerizadora se pide la misma para relacionarla al edificio
                                                    showTercerizadoras(tipoAdministracion, rec);
                                                }
                                                else {
                                                    //Si es propia se guarda a factibilidad de edificio
                                                    factibilidadPseudoPe(tipoAdministracion,
                                                        rec.get('idSolicitud'),
                                                        rec.get('idElemento'),
                                                        null
                                                    );
                                                }
                                            }
                                        });
                                    }
                                    else {
                                        showIngresoFactibilidad(rec);
                                    }

                                });
                            }
                            else {
                                showIngresoFactibilidad(rec);
                            }
                        }
                    },
                    {
                        getClass: function (v, meta, rec) {
                            var permiso = $("#ROLE_323-4917");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (boolPermiso) {
                                if (rec.get('estado') == 'FactibilidadEquipos') {
                                    return 'button-grid-verInterface';
                                }
                                else {
                                    return "icon-invisible";
                                }
                            }
                        },
                        tooltip: 'Asignar Equipos',
                        handler: function (grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            showIngresoFactibilidadTN(rec);
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
        items:
            [
                { html: "&nbsp;", border: false, width: 200 },
                { html: "Fecha Factibilidad:", border: false, width: 325 },
                { html: "&nbsp;", border: false, width: 150 },
                { html: "", border: false, width: 325 },
                { html: "&nbsp;", border: false, width: 200 },
                { html: "&nbsp;", border: false, width: 200 },
                DTFechaDesdePlanif,
                { html: "&nbsp;", border: false, width: 150 },
                DTFechaHastaPlanif,
                { html: "&nbsp;", border: false, width: 200 },
                { html: "&nbsp;", border: false, width: 200 },
                {
                    xtype: 'textfield',
                    id: 'txtNodo',
                    fieldLabel: 'Nombre',
                    value: '',
                    width: '325'
                },
                { html: "&nbsp;", border: false, width: 150 },
                {
                    xtype: 'combobox',
                    id: 'txtEstado',
                    fieldLabel: 'Estado',
                    store: ['PreFactibilidad', 'FactibilidadEnProceso', 'Finalizada', 'Rechazada'],
                    value: '',
                    width: '325'
                },
                { html: "&nbsp;", border: false, width: 200 },
                { html: "&nbsp;", border: false, width: 200 },
                {
                    xtype: 'textfield',
                    id: 'txtUser',
                    fieldLabel: 'Usuario Crea',
                    value: '',
                    width: '325'
                },
                { html: "&nbsp;", border: false, width: 150 },
                // {html: "&nbsp;", border: false, width: 150},
                // {html: "&nbsp;", border: false, width: 525},
                // {html: "&nbsp;", border: false, width: 200},
                {
                    xtype: 'combobox',
                    fieldLabel: 'Jurisdicción:',
                    id: 'comboJurisdiccion',
                    name: 'comboJurisdiccion',
                    store: storeJurisdicciones,
                    displayField: 'jurisdiccion',
                    valueField: 'id_jurisdiccion',
                    queryMode: "remote",
                    emptyText: '',
                    listeners: {
                    },
                    forceSelection: true
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

    if ((Ext.getCmp('fechaDesdePlanif').getValue() != null) && (Ext.getCmp('fechaHastaPlanif').getValue() != null)) {
        if (Ext.getCmp('fechaDesdePlanif').getValue() > Ext.getCmp('fechaHastaPlanif').getValue()) {
            boolError = true;

            Ext.Msg.show({
                title: 'Error en Busqueda',
                msg: 'Por Favor para realizar la busqueda Fecha Desde debe ser fecha menor a Fecha Hasta .',
                buttons: Ext.Msg.OK,
                animEl: 'elId',
                icon: Ext.MessageBox.ERROR
            });
        }
    }


    if (!boolError) {
        store.getProxy().extraParams.fechaDesdePlanif = Ext.getCmp('fechaDesdePlanif').value;
        store.getProxy().extraParams.fechaHastaPlanif = Ext.getCmp('fechaHastaPlanif').value;
        store.getProxy().extraParams.txtNodo = Ext.getCmp('txtNodo').value;
        store.getProxy().extraParams.txtEstado = Ext.getCmp('txtEstado').value;
        store.getProxy().extraParams.txtUser = Ext.getCmp('txtUser').value;
        store.getProxy().extraParams.id_jurisdiccion = Ext.getCmp('comboJurisdiccion').value.toString();
        if (store.getProxy().extraParams.id_jurisdiccion == '-1') {
            limpiar();
        }
        store.load();
    }
}

function limpiar() {
    Ext.getCmp('fechaDesdePlanif').setRawValue("");
    Ext.getCmp('fechaHastaPlanif').setRawValue("");

    Ext.getCmp('txtNodo').value = "";
    Ext.getCmp('txtNodo').setRawValue("");

    Ext.getCmp('txtEstado').value = "";
    Ext.getCmp('txtEstado').setRawValue("");

    Ext.getCmp('txtUser').value = "";
    Ext.getCmp('txtUser').setRawValue("");

    Ext.getCmp('comboJurisdiccion').value = "";
    Ext.getCmp('comboJurisdiccion').setRawValue("");

    store.getProxy().extraParams.fechaDesdePlanif = Ext.getCmp('fechaDesdePlanif').value;
    store.getProxy().extraParams.fechaHastaPlanif = Ext.getCmp('fechaHastaPlanif').value;
    store.getProxy().extraParams.fechaHastaPlanif = Ext.getCmp('txtNodo').value;
    store.getProxy().extraParams.fechaHastaPlanif = Ext.getCmp('txtEstado').value;
    store.getProxy().extraParams.fechaHastaPlanif = Ext.getCmp('txtUser').value;
    store.getProxy().extraParams.id_jurisdiccion = Ext.getCmp('comboJurisdiccion').value.toString();
    store.load();
}