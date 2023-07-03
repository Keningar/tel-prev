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

    store = new Ext.data.Store({
        autoLoad: true,
        pageSize: 14,
        total: 'total',
        proxy: {
            timeout: 900000,
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
                login2: '',
                descripcionPunto: '',
                vendedor: '',
                ciudad: '',
                numOrdenServicio: '',
                estado: 'Todos'
            }
        },
        fields:
            [
                {name: 'id_factibilidad', mapping: 'id_factibilidad'},
                {name: 'estadoFactibilidad', mapping: 'estadoFactibilidad'},
                {name: 'id_servicio', mapping: 'id_servicio'},
                {name: 'tipo_orden', mapping: 'tipo_orden'},
                {name: 'idOlt', mapping: 'idOlt'},
                {name: 'olt', mapping: 'olt'},
                {name: 'idLinea', mapping: 'idLinea'},
                {name: 'id_punto', mapping: 'id_punto'},
                {name: 'linea', mapping: 'linea'},
                {name: 'idCaja', mapping: 'idCaja'},
                {name: 'caja', mapping: 'caja'},
                {name: 'idSplitter', mapping: 'idSplitter'},
                {name: 'splitter', mapping: 'splitter'},
                {name: 'id_orden_trabajo', mapping: 'id_orden_trabajo'},
                {name: 'intIdPersonaEmpresaRol', mapping: 'intIdPersonaEmpresaRol'},
                {name: 'strPrefijoEmpresa', mapping: 'strPrefijoEmpresa'},
                {name: 'strEsEdificio', mapping: 'strEsEdificio'},
                {name: 'strDependeDeEdificio', mapping: 'strDependeDeEdificio'},
                {name: 'strNombreEdificio', mapping: 'strNombreEdificio'},
                {name: 'intIdElemento', mapping: 'intIdElemento'},
                {name: 'intIdPersona', mapping: 'intIdPersona'},
                {name: 'intIdUltimaMilla', mapping: 'intIdUltimaMilla'},
                {name: 'strCodigoTipoMedio', mapping: 'strCodigoTipoMedio'},
                {name: 'strNombreTipoMedio', mapping: 'strNombreTipoMedio'},
                {name: 'strTipoEnlace', mapping: 'strTipoEnlace'},
                {name: 'strTipoRed', mapping: 'strTipoRed'},
                {name: 'booleanTipoRedGpon', mapping: 'booleanTipoRedGpon'},
                {name: 'strNombreTipoElementoPadre', mapping: 'strNombreTipoElementoPadre'},
                {name: 'strNombreMarcaElementoPadre', mapping: 'strNombreMarcaElementoPadre'},
                {name: 'strNombreModeloElementoPadre', mapping: 'strNombreModeloElementoPadre'},
                {name: 'strNombreTipoElementoDist', mapping: 'strNombreTipoElementoDist'},
                {name: 'strNombreMarcaElementoDist', mapping: 'strNombreMarcaElementoDist'},
                {name: 'strNombreModeloElementoDist', mapping: 'strNombreModeloElementoDist'},
                {name: 'strNombreInfoInterfaceElementoDist', mapping: 'strNombreInfoInterfaceElementoDist'},
                {name: 'strMetraje', mapping: 'strMetraje'},
                {name: 'strObraCivil', mapping: 'strObraCivil'},
                {name: 'strPermisosRegeneracion', mapping: 'strPermisosRegeneracion'},
                {name: 'strObservacionPermiRegeneracion', mapping: 'strObservacionPermiRegeneracion'},
                {name: 'intSplitter', mapping: 'intSplitter'},
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
                {name: 'intPuntoCobertura', mapping:'intPuntoCobertura'},
                {name: 'intCanton' , mapping:'intCanton'},
                {name: 'intParroquia', mapping:'intParroquia'},
                {name: 'intSector', mapping:'intSector'},
                {name: 'intIdProducto', mapping:'intIdProducto'},
                {name: 'action1', mapping: 'action1'},
            ]
    });

    /* ******************************************* */
    /* FILTROS DE BUSQUEDA */
    /* ******************************************* */
    Ext.create('Ext.panel.Panel', {
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
                    buscar();
                }
            }

        ],
        items:
            [
                {html: "&nbsp;", border: false, width: 200},
                {html: "Fecha Solicita Planificacion:", border: false, width: 325},
                {html: "&nbsp;", border: false, width: 150},
                {html: "&nbsp;", border: false, width: 325},
                {html: "&nbsp;", border: false, width: 200},
                {html: "&nbsp;", border: false, width: 200},
                DTFechaDesdePlanif,
                {html: "&nbsp;", border: false, width: 150},
                {
                    xtype: 'textfield',
                    id: 'txtLogin',
                    fieldLabel: 'Login',
                    value: '',
                    width: '325'
                },
                {html: "&nbsp;", border: false, width: 200},
                {html: "&nbsp;", border: false, width: 200},
                DTFechaHastaPlanif,
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
                {html: "&nbsp;", border: false, width: 325},
                {html: "&nbsp;", border: false, width: 150},
                {html: "&nbsp;", border: false, width: 325},
                {html: "&nbsp;", border: false, width: 200}

            ],
        renderTo: 'filtro'
    });

    /* ******************************************* */
    /* GRID DE SERVICIOS */
    /* ******************************************* */
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
                id: 'idOlt',
                header: 'idOlt',
                dataIndex: 'idOlt',
                hidden: true,
                hideable: false
            },
            {
                id: 'olt',
                header: 'olt',
                dataIndex: 'olt',
                hidden: true,
                hideable: false
            },
            {
                id: 'idLinea',
                header: 'idLinea',
                dataIndex: 'idLinea',
                hidden: true,
                hideable: false
            },
            {
                id: 'linea',
                header: 'linea',
                dataIndex: 'linea',
                hidden: true,
                hideable: false
            },
            {
                id: 'idCaja',
                header: 'idCaja',
                dataIndex: 'idCaja',
                hidden: true,
                hideable: false
            },
            {
                id: 'caja',
                header: 'caja',
                dataIndex: 'caja',
                hidden: true,
                hideable: false
            },
            {
                id: 'idSplitter',
                header: 'idSplitter',
                dataIndex: 'idSplitter',
                hidden: true,
                hideable: false
            },
            {
                id: 'splitter',
                header: 'splitter',
                dataIndex: 'splitter',
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
                width: 135,
                sortable: true
            },
            {
                id: 'observacion',
                header: 'Observacion',
                dataIndex: 'observacion',
                width: 70,
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
                header: 'F. Sol. Planifica',
                dataIndex: 'fePlanificacion',
                width: 95,
                sortable: true
            },
            {
                id: 'estadoFactibilidad',
                header: 'Estado',
                dataIndex: 'estadoFactibilidad',
                width: 75,
                sortable: true
            },
            {
                id: 'intIdPersonaEmpresaRol',
                header: 'intIdPersonaEmpresaRol',
                dataIndex: 'intIdPersonaEmpresaRol',
                hidden: true,
                hideable: false
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
                        tooltip: 'Editar Edificio',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);

                            if (rec.get("latitud") != 0 && rec.get("longitud") != 0){
                                showDatosEdificio(rec);
                            }
                            else
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: 'Las coordenadas del punto son incorrectas',
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

    if (!boolError)
    {
        store.removeAll();
        store.currentPage = 1;

        store.getProxy().extraParams.fechaDesdePlanif  = Ext.getCmp('fechaDesdePlanif').value;
        store.getProxy().extraParams.fechaHastaPlanif  = Ext.getCmp('fechaHastaPlanif').value;
        store.getProxy().extraParams.login2            = Ext.getCmp('txtLogin').value;
        store.getProxy().extraParams.descripcionPunto  = Ext.getCmp('txtDescripcionPunto').value;
        store.getProxy().extraParams.vendedor          = Ext.getCmp('txtVendedor').value;
        store.getProxy().extraParams.ciudad            = Ext.getCmp('txtCiudad').value;
        
        store.load();
    }
}

function limpiar() {
    Ext.getCmp('fechaDesdePlanif').setRawValue("");
    Ext.getCmp('fechaHastaPlanif').setRawValue("");

    Ext.getCmp('txtLogin').value = "";
    Ext.getCmp('txtLogin').setRawValue("");

    Ext.getCmp('txtDescripcionPunto').value = "";
    Ext.getCmp('txtDescripcionPunto').setRawValue("");

    Ext.getCmp('txtVendedor').value = "";
    Ext.getCmp('txtVendedor').setRawValue("");

    Ext.getCmp('txtCiudad').value = "";
    Ext.getCmp('txtCiudad').setRawValue("");

    store.getProxy().extraParams.fechaDesdePlanif = Ext.getCmp('fechaDesdePlanif').value;
    store.getProxy().extraParams.fechaHastaPlanif = Ext.getCmp('fechaHastaPlanif').value;
    store.getProxy().extraParams.login2 = Ext.getCmp('txtLogin').value;
    store.getProxy().extraParams.descripcionPunto = Ext.getCmp('txtDescripcionPunto').value;
    store.getProxy().extraParams.vendedor = Ext.getCmp('txtVendedor').value;
    store.getProxy().extraParams.ciudad = Ext.getCmp('txtCiudad').value;
    store.load();
}