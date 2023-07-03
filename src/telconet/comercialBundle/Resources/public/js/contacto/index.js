Ext.QuickTips.init();
Ext.onReady(function() {

    var objContacto = new Contacto();
    var intHeightForm = 400;
    var intWidthForm = 1060;
    var intWidthFilterPanel = 1060;
    function GrigContainer() {
        var objContacto = new Contacto();
        this.showGridContacto = function(objScope, strTipoInsert, intIdPersonaEmpresaRol, intIdPunto) {

            return new Ext.create('Ext.grid.Panel', {
                title: objScope.title,
                store: objScope.objStore,
                id: objScope.strIdObj,
                columns: [
                    {header: 'Nombres', dataIndex: 'strNombres', align: 'left', width: 170},
                    {header: 'Apellidos', dataIndex: 'strApellidos', align: 'left', width: 170},
                    {header: 'Titulo', dataIndex: 'strTitulo', align: 'center', width: 145},
                    {header: 'Identificacion', dataIndex: 'strIdentificacionCliente', align: 'center', width: 150},
                    {header: 'Fecha Creacion', dataIndex: 'dateFeCreacion', align: 'center', width: 120},
                    {header: 'Usuario Creacion', dataIndex: 'strUsuarioCreacion', align: 'center', width: 110},
                    {
                        xtype: 'actioncolumn',
                        header: 'Acciones',
                        align: 'center',
                        width: 170,
                        items: [
                            {
                                tooltip: 'Ver informacion completa de contacto',
                                getClass: function(v, meta, rec) {
                                    if ("Eliminado" !== rec.get("strEstado")) {
                                        return 'button-lookBook button-point';
                                    }
                                    return 'none';
                                },
                                handler: function(grid, rowIndex, colIndex) {
                                    window.location.assign(grid.store.data.items[rowIndex].data.strUrlShow);
                                }
                            },
                            {
                                tooltip: 'Ver/Editar informacion de contacto',
                                getClass: function(v, meta, rec) {
                                    if ("Eliminado" !== rec.get("strEstado")) {
                                        return 'button-editBook button-point';
                                    }
                                    return 'none';
                                },
                                handler: function(grid, rowIndex, colIndex) {
                                    var objContacto = new Contacto();
                                    var objScope = {
                                        intIdPersona: grid.store.data.items[rowIndex].data.intIdPersona,
                                        strNombre: grid.store.data.items[rowIndex].data.strNombres,
                                        strApellido: grid.store.data.items[rowIndex].data.strApellidos,
                                        intIdTitulo: grid.store.data.items[rowIndex].data.intIdTitulo,
                                        strTitulo: grid.store.data.items[rowIndex].data.strTitulo,
                                        strIdentificacion: grid.store.data.items[rowIndex].data.strIdentificacionCliente,
                                        tipoIdentificacion: grid.store.data.items[rowIndex].data.tipoIdentificacion,
                                        strTipoInsert: strTipoInsert,
                                        intIdPersonaEmpresaRol : intIdPersonaEmpresaRol,
                                        intIdPunto: intIdPunto
                                    };
                                    objContacto.verEditarInformacionContacto(objScope);
                                }
                            },
                            {
                                tooltip: 'Ver tipo de contacto',
                                getClass: function(v, meta, rec) {
                                    return 'button-agenda button-point';
                                },
                                handler: function(grid, rowIndex, colIndex) {
                                    var strNombres = grid.store.data.items[rowIndex].data.strNombres + ' ' +
                                        grid.store.data.items[rowIndex].data.strApellidos;
                                    var objContacto = new Contacto();
                                    var strTitulo = grid.store.data.items[rowIndex].data.strTitulo;
                                    objContacto.ingresaTipoContacto(grid.store.data.items[rowIndex].data.intIdPersona, grid.store, strNombres, strTitulo, strTipoInsert, intIdPersonaEmpresaRol, intIdPunto);
                                }
                            },
                            {
                                tooltip: 'Duplicar contacto',
                                getClass: function(v, meta, rec) {
                                    if ("Eliminado" !== rec.get("strEstado")) {
                                        return 'button-duplicarcontacto button-point';
                                    }
                                    return 'none';
                                },
                                handler: function(grid, rowIndex, colIndex) {
                                    if (objModalPanelListaPuntos) {
                                        objModalPanelListaPuntos.customParams.tipoRol = strTipoInsert;
                                        objModalPanelListaPuntos.customParams.infoContacto = grid.getStore().getAt(rowIndex).data;
                                        objModalPanelListaPuntos.setTitle('Seleccionar puntos en donde se duplicará el contacto ' +
                                            objModalPanelListaPuntos.customParams.infoContacto.strNombres + ' ' +
                                            objModalPanelListaPuntos.customParams.infoContacto.strApellidos);

                                        if (strTipoInsert == 'PERSONA_ROL') {
                                            Ext.getCmp('idChkDuplicarANivelCliente').setValue(false);
                                        }

                                        if (objStorePuntosSeleccionados) {
                                            if (objStorePuntosSeleccionados.getCount() > 0 ||
                                                Ext.getCmp('idChkDuplicarANivelCliente').checked ||
                                                Ext.getCmp('idChkTodosPuntos').checked) {
                                                Ext.getCmp('idBtnGuardarDuplicar').enable();
                                            } else {
                                                Ext.getCmp('idBtnGuardarDuplicar').disable();
                                            }
                                        }
                                        objModalPanelListaPuntos.show();
                                    }
                                }
                            },
                            {
                                tooltip: 'Eliminar contacto',
                                getClass: function(v, meta, rec) {
                                    if ("Eliminado" !== rec.get("strEstado")) {
                                        return 'button-deleteBook button-point';
                                    }
                                    return 'none';
                                },
                                handler: function(grid, rowIndex, colIndex) {
                                    var objContacto = new Contacto();
                                    objContacto.eliminarContacto(grid.store.data.items[rowIndex].data.intIdPersona, strTipoInsert, intIdPersonaEmpresaRol, intIdPunto, grid.store);
                                }
                            }
                        ]
                    }
                ],
                height: objContacto.intHeightGrid,
                width: objContacto.intWidthGrid,
                bbar: Ext.create('Ext.PagingToolbar', {
                    displayInfo: true,
                    store: objScope.objStore,
                    displayMsg: 'Mostrando {0} - {1} de {2}',
                    emptyMsg: "No hay datos que mostrar."
                })
            });
        };
    }

    var objComboTitulo = objContacto.objComboTitulo();
   // var objComboEstado = objContacto.objComboEstado();
    var grigContainer = new GrigContainer();
    var objStoreCliente = objContacto.objStoreCliente();
    var objScopeFields = {
        title: 'Contactos Cliente',
        objStore: objStoreCliente,
        strIdObj: 'gridCliente'
    };
    var gridContactoCliente = grigContainer.showGridContacto(objScopeFields, 'PERSONA_ROL', intIdPersonaEmpresaRol, intIdPunto);
    var objStorePunto = objContacto.objStorePunto();
    objScopeFields.title = 'Contactos Punto';
    objScopeFields.strIdObj = 'gridPunto';
    objScopeFields.objStore = objStorePunto;
    var gridContactoPunto = grigContainer.showGridContacto(objScopeFields, 'PUNTO', intIdPersonaEmpresaRol, intIdPunto);
    objContacto.btnBuscar.on({
        click: function() {
            var objExtraParams = {
                //strEstado: objComboEstado.getValue(),
                strNombres: objContacto.objTxtNombres.getValue(),
                strApellidos: objContacto.objTxtApellidos.getValue(),
                strUsrCreacion: objContacto.objTxtUsrCreacion.getValue(),
                intIdTitulo: objComboTitulo.getValue(),
                dateFechaDesde: objContacto.objDateDesde.getValue(),
                dateFechaHasta: objContacto.objDateHasta.getValue(),
                intIdPersonaEmpresaRol: intIdPersonaEmpresaRol,
                intIdPunto: intIdPunto,
                strJoinPunto: '',
                strDescripcionTipoRol: 'Contacto',
                strGroupBy: 'GROUP'
            };
            objStoreCliente.proxy.extraParams = objExtraParams;
            objStoreCliente.load();
            objExtraParams.strJoinPunto = 'BUSCA_POR_PUNTO';
            objStorePunto.proxy.extraParams = objExtraParams;
            objStorePunto.load();
        }
    });
    var objFilterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 10,
        border: false,
        width: intWidthFilterPanel,
        buttonAlign: 'center',
        renderTo: 'filterContacto',
        layout: {
            type: 'table',
            columns: 2,
            align: 'left',
            tableAttrs: {
                style: {
                    width: '100%',
                    height: '100%'
                }
            },
            tdAttrs: {
                align: 'center',
                valign: 'middle'
            }
        },
        bodyStyle: 'margin: 10px; padding: 5px 3px;',
        collapsible: true,
        collapsed: false,
        title: 'Criterios de busqueda',
        buttons: [
            objContacto.btnBuscar,
            objContacto.btnLimpiar
        ],
        items: [
            objContacto.objDateDesde,
            objContacto.objDateHasta,
            objContacto.objTxtNombres,
            objContacto.objTxtApellidos,
            objComboTitulo,
          // objComboEstado,
            objContacto.objTxtUsrCreacion
        ]
    });
    var formMuestraContactos = Ext.create('Ext.form.Panel', {
        id: 'frmMuestraContacto',
        height: intHeightForm,
        width: intWidthForm,
        renderTo: 'frmContacto',
        bodyStyle: 'padding:10px 10px 0; background:#FFFFFF;',
        bodyPadding: 10,
        autoScroll: false,
        layout: {
            type: 'table',
            columns: 1,
            tableAttrs: {
                style: {
                    width: '100%',
                    height: '100%'
                }
            },
            tdAttrs: {
                align: 'center',
                valign: 'middle'
            }
        },
        items: [
            {
                xtype: 'tabpanel',
                activeTab: 0,
                autoScroll: false,
                layoutOnTabChange: true,
                items: [gridContactoCliente, gridContactoPunto]
            }
        ]
    });

    /* Inicio de interfaz para duplicación masiva */
    var registrosPorPagina = 10;

    //Modelos
    var modelEstadoPunto = Ext.define('modelEstadoPunto', {
        extend: 'Ext.data.Model',
        fields: [
            {
                name: 'estado_punto',
                type: 'string'
            }
        ]
    });

    var modelPunto = Ext.define('modelPunto', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idPto', type: 'int'},
            {name: 'cliente', type: 'string'},
            {name: 'login', type: 'string'},
            {name: 'nombrePunto', type: 'string'},
            {name: 'direccion', type: 'string'},
            {name: 'descripcionPunto', type: 'string'},
            {name: 'estado', type: 'string'},
            {name: 'linkVer', type: 'string'},
            {name: 'linkEditar', type: 'string'},
            {name: 'linkEliminar', type: 'string'},
            {name: 'permiteAnularPunto', type: 'string'}
        ]
    });

    //Stores
    var objStoreEstadoPunto = Ext.create('Ext.data.Store',{
        autoLoad: false,
        model: modelEstadoPunto,
        proxy: {
            type: 'ajax',
            url: url_puntos_lista_estados,
            reader: {
                type: 'json',
                root: 'encontrados'
            }
        }
    });

    var objStorePuntosLista = Ext.create('Ext.data.Store', {
        id: 'idStorePuntosListaDuplicar',
        autoLoad: false,
        model: modelPunto,
        pageSize: registrosPorPagina,
        proxy: {
            type: 'ajax',
            url: url_gridPtos,
            reader: {
                type: 'json',
                root: 'ptos',
                totalProperty: 'total'
            },
            extraParams: {
                txtFechaDesde: '',
                txtFechaHasta: '',
                txtLogin: '',
                txtNombrePunto: '',
                txtDireccion: '',
                estado_punto: ''
            },
            simpleSortMode: true
        },
        listeners: {
            beforeload: function(store) {
                store.getProxy().extraParams.txtFechaDesde  = Ext.getCmp('txtFechaDesde').getValue();
                store.getProxy().extraParams.txtFechaHasta  = Ext.getCmp('txtFechaHasta').getValue();
                store.getProxy().extraParams.txtLogin       = Ext.getCmp('txtLogin').getValue();
                store.getProxy().extraParams.txtNombrePunto = Ext.getCmp('txtNombrePunto').getValue();
                store.getProxy().extraParams.txtDireccion   = Ext.getCmp('txtDireccion').getValue();
                store.getProxy().extraParams.estado_punto   = Ext.getCmp('estado_punto').getValue();
            }
        }
    });
    var objStorePuntosSeleccionados = Ext.create(Ext.data.Store, {
        id: 'idStorePuntosSeleccionadosDuplicar',
        model: modelPunto,
        pageSize: registrosPorPagina,
        proxy: {
            type: 'memory'
        }
    });

    // Campos de búsqueda
    var objDateFechaDesde = new Ext.form.DateField({
        id: 'txtFechaDesde',
        fieldLabel: 'Creaci&oacuten Desde',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 325
    });
    var objDateFechaHasta = new Ext.form.DateField({
        id: 'txtFechaHasta',
        fieldLabel: 'Creaci&oacuten Hasta',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 325
    });
    var objTextLogin = new Ext.form.TextField({
        id: 'txtLogin',
        fieldLabel: 'Login',
        xtype: 'textfield',
        width: 325
    });
    var objTextNombrePunto = new Ext.form.TextField({
        id: 'txtNombrePunto',
        fieldLabel: 'Nombre Punto',
        xtype: 'textfield',
        width: 315
    });
    var objTextDireccion = new Ext.form.TextField({
        id: 'txtDireccion',
        fieldLabel: 'Direccion Punto',
        xtype: 'textfield',
        width: 315
    });
    var objCbEstadoPunto = new Ext.form.ComboBox({
        xtype: 'combobox',
        store: objStoreEstadoPunto,
        labelAlign: 'left',
        id: 'estado_punto',
        name: 'estado_punto',
        valueField: 'estado_punto',
        displayField: 'estado_punto',
        fieldLabel: 'Estado',
        width: 325,
        triggerAction: 'all',
        selectOnFocus: true,
        lastQuery: '',
        mode: 'local',
        allowBlank: true,
        listeners: {
            select:
                function(e) {
                    estado_id = Ext.getCmp('estado_punto').getValue();
                },
            click: {
                element: 'el',
                fn: function() {
                    estado_id = '';
                    objStoreEstadoPunto.removeAll();
                    objStoreEstadoPunto.load();
                }
            }
        }
    });

    //Formulario de búsqueda
    var objFormFiltroPuntos = Ext.create('Ext.panel.Panel', {
        flex: 0.5,
        border: false,
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 2,
            align: 'left',
            tableAttrs: {
                style: {
                    width: '100%',
                    height: '100%'
                }
            },
            tdAttrs: {
                align: 'left',
                valign: 'middle'
            }
        },
        bodyStyle: {
            padding: '10px 10px 10px 150px',
        },
        collapsible : false,
        collapsed: false,
        title: 'Criterios de b&uacutesqueda',
        dockedItems: [{
            xtype: 'toolbar',
            dock: 'bottom',
            ui: 'footer',
            margin: '5',
            layout: {
                type: 'hbox',
                pack: 'center',
                align: 'middle',
            },
            items: [
                {
                    text: 'Buscar',
                    iconCls: "icon_search",
                    handler: function(){
                        var boolError = false;
                        if ((Ext.getCmp('txtFechaDesde').getValue() != null) && (Ext.getCmp('txtFechaHasta').getValue() != null))
                        {
                            if (Ext.getCmp('txtFechaDesde').getValue() > Ext.getCmp('txtFechaHasta').getValue())
                            {
                                boolError = true;
                                Ext.Msg.show({
                                    title: 'Error en Busqueda',
                                    msg: 'Por Favor para realizar la busqueda Fecha Desde debe ser fecha menor a Fecha Hasta.',
                                    buttons: Ext.Msg.OK,
                                    animEl: 'elId',
                                    icon: Ext.MessageBox.ERROR
                                });

                            }
                        }
                        else
                        {
                            if ((Ext.getCmp('txtFechaDesde').getValue() == null) && (Ext.getCmp('txtFechaHasta').getValue() != null)
                                || (Ext.getCmp('txtFechaDesde').getValue() != null) && (Ext.getCmp('txtFechaHasta').getValue() == null))
                            {
                                Ext.Msg.show({
                                    title: 'Error en B&uacutesqueda',
                                    msg: 'Por favor ingrese criterios de fecha correctamente.',
                                    buttons: Ext.Msg.OK,
                                    animEl: 'elId',
                                    icon: Ext.MessageBox.ERROR
                                });
                            }
                        }

                        if (!boolError){
                            objStorePuntosLista.load({
                                params: {
                                    start: 0,
                                    limit: registrosPorPagina
                                }
                            });
                        }
                    },
                },
                {
                    text: 'Limpiar',
                    iconCls: "icon_limpiar",
                    handler: function() {
                        Ext.getCmp('txtFechaDesde').setValue('');
                        Ext.getCmp('txtFechaHasta').setValue('');
                        Ext.getCmp('txtLogin').setValue('');
                        Ext.getCmp('txtNombrePunto').setValue('');
                        Ext.getCmp('txtDireccion').setValue('');
                        Ext.getCmp('estado_punto').setValue('');
                        objStorePuntosLista.load({
                            params: {
                                start: 0,
                                limit: registrosPorPagina
                            }
                        });
                    }
                }
            ]
        }],
        items: [
            objDateFechaDesde,
            objDateFechaHasta,
            objTextLogin,
            objTextNombrePunto,
            objTextDireccion,
            objCbEstadoPunto,
        ],
    });

    //Grid de lista de puntos
    var objGridPuntosListado = Ext.create('Ext.grid.Panel', {
        id: 'idGridPuntosListadoDuplicar',
        width: 'auto',
        layout: 'fit',
        flex: 1,
        store: objStorePuntosLista,
        multiSelect: false,
        style: 'vertical-align: middle;',
        title: 'Listado de Puntos',
        header: true,
        bbar: Ext.create('Ext.PagingToolbar', {
            store: objStorePuntosLista,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos para mostrar"
        }),
        viewConfig: {
            emptyText: 'No hay datos para mostrar'
        },
        listeners: {
            viewready: function (grid) {
                objGridTooltip(grid);
            },
        },
        columns: [
            new Ext.grid.RowNumberer({
                header: '#',
                flex: 0,
                align: 'center',
                dataIndex: 'index'
            }),
            {
                text: 'Login',
                flex: 1,
                dataIndex: 'login'
            },
            {
                text: 'Nombre Punto',
                dataIndex: 'nombrePunto',
                flex: 1,
            },
            {
                text: 'Direcci&oacuten',
                dataIndex: 'direccion',
                flex: 1,
            },
            {
                text: 'Estado',
                dataIndex: 'estado',
                flex: 0.4,
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                dataIndex: 'acciones',
                flex: 0,
                items: [
                    {
                        tooltip: '<b>Agregar punto</b>',
                        getClass: function(obj, metadata, record, rowIndex, colIndex) {
                            if (record.get('estado') !== 'Eliminado') {
                                if (objModalPanelListaPuntos.customParams.tipoRol === 'PUNTO') {
                                    if (typeof intIdPunto !== 'undefined' && intIdPunto !== null && intIdPunto > 0) {
                                        if (objStorePuntosLista.getAt(rowIndex).get('idPto') !== intIdPunto) {
                                            return 'x-btn button-seleccion-puntos-agregar';
                                        }
                                    }
                                    return 'none';
                                }
                                return 'x-btn button-seleccion-puntos-agregar';
                            }
                            return 'none';
                        },
                        handler: function(view, rowIndex, colIndex) {

                            var index = objStorePuntosSeleccionados.find('idPto', objStorePuntosLista.getAt(rowIndex).get('idPto'));

                            if(index <= -1) { //Indice no encontrado
                                var objGridItem = objStorePuntosLista.getAt(rowIndex);

                                objGridItem.index = objStorePuntosSeleccionados.getCount();
                                objStorePuntosSeleccionados.add(objGridItem);
                            } else {
                                Ext.getCmp('idGridPuntosSeleccionadosDuplicar').getSelectionModel().select(index);
                            }

                            if(objStorePuntosSeleccionados.getCount() > 0){
                                Ext.getCmp('idBtnGuardarDuplicar').enable();
                            }
                        }
                    }]
            }
        ]
    });

    //Grid de lista de puntos seleccionados
    var objGridPuntosSeleccionados = Ext.create('Ext.grid.Panel', {
        id: 'idGridPuntosSeleccionadosDuplicar',
        width: 'auto',
        layout: 'fit',
        flex: 0.3,
        title: 'Puntos selecionados',
        store: objStorePuntosSeleccionados,
        multiSelect: false,
        viewConfig: {
            emptyText: 'No hay datos para mostrar'
        },
        listeners: {
            viewready: function (grid) {
                objGridTooltip(grid);
            },
        },
        columns: [
            new Ext.grid.RowNumberer({
                header: '#',
                flex: 0,
                align: 'center',
            }),
            {
                text: 'Login',
                flex: 1,
                dataIndex: 'login'
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                dataIndex: 'acciones',
                flex: 0,
                items: [
                    {
                        tooltip: '<b>Quitar punto</b>',
                        iconCls: 'x-btn btn-acciones button-grid-delete',
                        handler: function(view, rowIndex, colIndex) {
                            objStorePuntosSeleccionados.removeAt(rowIndex);

                            if(objStorePuntosSeleccionados.getCount() <= 0 &&
                                !Ext.getCmp('idChkDuplicarANivelCliente').checked) {
                                Ext.getCmp('idBtnGuardarDuplicar').disable();
                            }
                        }
                    }]
            }
        ]
    });

    //Ventana modal para creación masiva
    var objModalPanelListaPuntos = Ext.create('Ext.window.Window', {
        title: 'Seleccionar puntos en donde se duplicará el contacto',
        id: 'idModalPanelListaPuntosDuplicar',
        floating: true,
        border: false,
        frame: false,
        height: 550,
        width: 1200,
        modal: true,
        resizable: false,
        closeAction : 'hide',
        bodyStyle: 'background-color: #FFFFFF',
        layout: {
            type: 'vbox',
            align: 'stretch',
        },
        customParams: {
            tipoRol: 'PERSONA_ROL',
            firstTime: true,
            infoContacto: {}
        },
        items: [objFormFiltroPuntos,
            {
                xtype: 'container',
                flex: 1,
                layout: {
                    type: 'hbox',
                    align: 'stretch',
                    columns: 2,
                    rows: 1,
                },
                items: [objGridPuntosListado, objGridPuntosSeleccionados]
            }],
        dockedItems: [{
            xtype: 'toolbar',
            dock: 'bottom',
            ui: 'footer',
            items: [
                {
                    xtype: 'container',
                    flex: 1,
                    layout: {
                        type: 'hbox',
                        pack: 'end',
                        align: 'middle',
                    },
                    items: [
                        {
                            xtype: 'checkbox',
                            id: 'idChkTodosPuntos',
                            boxLabel: 'Duplicar en todos los puntos',
                            name: 'chkTodosPuntos',
                            inputValue: 'chkTodosPuntos',
                            margin: '5 25 5 5',
                            checked: false,
                            listeners: {
                                change: function(field, newValue, oldValue, eOpts){
                                    if(newValue == true){
                                        objGridPuntosListado.disable();
                                        objGridPuntosSeleccionados.disable();
                                        Ext.getCmp('idBtnGuardarDuplicar').enable();
                                    } else {
                                        objGridPuntosListado.enable();
                                        objGridPuntosSeleccionados.enable();

                                        if(objGridPuntosSeleccionados.getCount() > 0) {
                                            Ext.getCmp('idBtnGuardarDuplicar').enable();
                                        } else {
                                            Ext.getCmp('idBtnGuardarDuplicar').disable();
                                        }
                                    }
                                }
                            }
                        },
                        {
                            xtype: 'checkbox',
                            id: 'idChkDuplicarANivelCliente',
                            boxLabel: 'Duplicar a nivel cliente',
                            name: 'chkDuplicarANivelCliente',
                            inputValue: 'chkDuplicarANivelCliente',
                            margin: '5 25 5 5',
                            checked: false,
                            listeners: {
                                change: function(field, newValue, oldValue, eOpts){
                                    if(newValue == false && objStorePuntosSeleccionados.getCount() <= 0) {
                                        if(!Ext.getCmp('idChkTodosPuntos').checked) {
                                            Ext.getCmp('idBtnGuardarDuplicar').disable();
                                            return;
                                        }
                                    }
                                    Ext.getCmp('idBtnGuardarDuplicar').enable();
                                }
                            }
                        },
                        {
                            xtype: 'button',
                            id: 'idBtnGuardarDuplicar',
                            text: 'Guardar y Cerrar',
                            align: 'center',
                            disabled: true,
                            iconCls: 'x-btn button-seleccion-puntos-cerrar',
                            handler: function () {
                                var arrayIdPuntosAsignacionMasiva = [];

                                objStorePuntosSeleccionados.each(function(record) {
                                    arrayIdPuntosAsignacionMasiva.push(record.get('idPto'));
                                });

                                var objMsgBoxWait = Ext.MessageBox.show({
                                    msg: 'Este proceso podría tardar varios minutos.<br/>Espere por favor.',
                                    title: 'Duplicando contacto',
                                    progressText: '',
                                    progress: true,
                                    closable: false,
                                    width: 300,
                                    wait: true,
                                    waitConfig: {interval: 200}
                                });

                                Ext.Ajax.request({
                                    url: urlDuplicarContacto,
                                    method: 'POST',
                                    async: true,
                                    timeout: 1200000,
                                    params: {
                                        strTipoRol : objModalPanelListaPuntos.customParams.tipoRol,
                                        intIdPersona : objModalPanelListaPuntos.customParams.infoContacto.intIdPersona,
                                        jsonInfoContacto: Ext.JSON.encode(objModalPanelListaPuntos.customParams.infoContacto),
                                        booleanTodosLosPuntos: Ext.getCmp('idChkTodosPuntos').getValue(),
                                        booleanIncluirNivelCliente: Ext.getCmp('idChkDuplicarANivelCliente').getValue(),
                                        strArrayIdPuntos: Ext.getCmp('idChkTodosPuntos').getValue()
                                            ? new Array()
                                            : Ext.JSON.encode(arrayIdPuntosAsignacionMasiva),
                                    },
                                    success: function(response) {
                                        objModalPanelListaPuntos.close();
                                        objMsgBoxWait.close();
                                        var text = Ext.decode(response.responseText);
                                        Ext.Msg.alert(Utils.arrayTituloMensajeBox[text.strStatus], text.strMessageStatus);
                                    },
                                    failure: function(result) {
                                        objMsgBoxWait.close();
                                        if(!Ext.isEmpty(result) && !Ext.isEmpty(result.statusText)) {
                                            Ext.Msg.alert('Error: ', result.statusText);
                                        } else {
                                            Ext.Msg.alert('Error: ', 'Vuelva a intentar.');
                                        }
                                    }
                                });
                            }
                        },]
                }
            ]
        }],
        listeners: {
            show: function (window, options) {
                if(window.customParams.firstTime){
                    objStorePuntosLista.load({
                        params: {
                            start: 0,
                            limit: registrosPorPagina
                        }
                    });
                }
                this.customParams.tipoRol == 'PUNTO'
                    ? Ext.getCmp('idChkDuplicarANivelCliente').enable()
                    : Ext.getCmp('idChkDuplicarANivelCliente').disable();
            }
        }
    });

    //Generación de tooltip del grid
    var objGridTooltip = function(grid){
        var view = grid.view;

        Ext.create('Ext.tip.ToolTip', {
            target: view.el,
            delegate: view.cellSelector,
            trackMouse: true,
            renderTo: Ext.getBody(),
            listeners: {
                beforeshow: function(tip) {
                    var columnDataIndex = view.getHeaderByCell(tip.triggerElement).dataIndex;

                    if(!Ext.isEmpty(columnDataIndex) && columnDataIndex !== 'acciones') {
                        var columnText = view.getRecord(tip.triggerElement.parentElement).get(columnDataIndex).toString();

                        if (!Ext.isEmpty(columnText)) {
                            tip.update("<b>" + columnText + "</b>");
                        } else {
                            return false;
                        }
                    } else {
                        return false;
                    }
                }
            }
        });
    };
});

