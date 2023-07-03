/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();
    Ext.require([
        'Ext.ux.CheckColumn'
    ]);

    //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		
    DTFechaDesdeAsig = new Ext.form.DateField({
        id: 'fechaDesdeAsig',
        fieldLabel: 'Desde',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 325,
        editable: false
    });
    DTFechaHastaAsig = new Ext.form.DateField({
        id: 'fechaHastaAsig',
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
            url: 'grid',
            timeout: 120000,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                estado: 'Todos'
            }
        },
        fields:
            [
                {name: 'id_factibilidad',           mapping: 'id_factibilidad'},
                {name: 'id_servicio',               mapping: 'id_servicio'},
                {name: 'id_punto',                  mapping: 'id_punto'},
                {name: 'cliente',                   mapping: 'cliente'},
                {name: 'fe_activacion_servicio',    mapping: 'fe_activacion_servicio'},
                {name: 'buscar_cpe_naf',            mapping: 'buscar_cpe_naf'},
                {name: 'tercerizadora',             mapping: 'tercerizadora'},
                {name: 'esRecontratacion',          mapping: 'esRecontratacion'},
                {name: 'vendedor',                  mapping: 'vendedor'},
                {name: 'login2',                    mapping: 'login2'},
                {name: 'producto',                  mapping: 'producto'},
                {name: 'coordenadas',               mapping: 'coordenadas'},
                {name: 'direccion',                 mapping: 'direccion'},
                {name: 'ciudad',                    mapping: 'ciudad'},
                {name: 'tipo_orden',                mapping: 'tipo_orden'},
                {name: 'nombreSector',              mapping: 'nombreSector'},
                {name: 'fechaAsignadaReal',         mapping: 'fechaAsignadaReal'},
                {name: 'rutaCroquis',               mapping: 'rutaCroquis'},
                {name: 'latitud',                   mapping: 'latitud'},
                {name: 'longitud',                  mapping: 'longitud'},
                {name: 'responsable',               mapping: 'responsable'},
                {name: 'idResponsable',             mapping: 'idResponsable'},
                {name: 'url_responsable',           mapping: 'url_responsable'},
                {name: 'fieldIdResponsable',        mapping: 'fieldIdResponsable'},
                {name: 'fieldValueResponsable',     mapping: 'fieldValueResponsable'},
                {name: 'observacion_excedente',     mapping: 'observacion_excedente'},
                //se agrega parametro para validar si es un servicio migrado
                {name: 'esMigracionMd',             mapping: 'esMigracionMd'},
                {name: 'estado',                    mapping: 'estado'},
                {name: 'action1',                   mapping: 'action1'},
                {name: 'action2',                   mapping: 'action2'},
                {name: 'action3',                   mapping: 'action3'}
            ]
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
        dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items: [
                    {xtype: 'tbfill'}
                ]}
        ],
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
                id: 'tercerizadora',
                header: 'tercerizadora',
                dataIndex: 'tercerizadora',
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
                id: 'responsable',
                header: 'responsable',
                dataIndex: 'responsable',
                hidden: true,
                hideable: false
            },
            {
                id: 'idResponsable',
                header: 'idResponsable',
                dataIndex: 'idResponsable',
                hidden: true,
                hideable: false
            },
            {
                id: 'url_responsable',
                header: 'url_responsable',
                dataIndex: 'url_responsable',
                hidden: true,
                hideable: false
            },
            {
                id: 'fieldIdResponsable',
                header: 'fieldIdResponsable',
                dataIndex: 'fieldIdResponsable',
                hidden: true,
                hideable: false
            },
            {
                id: 'fieldValueResponsable',
                header: 'fieldValueResponsable',
                dataIndex: 'fieldValueResponsable',
                hidden: true,
                hideable: false
            },
            {
                id: 'observacion_excedente',
                header: 'observacionExcedente',
                dataIndex: 'observacion_excedente',
                hidden: true,
                hideable: false
            },
            {
                id: 'cliente',
                header: 'Cliente',
                dataIndex: 'cliente',
                width: 170,
                sortable: true
            },
            {
                id: 'esRecontratacion',
                header: 'esRecontratacion',
                dataIndex: 'esRecontratacion',
                hidden: true,
                hideable: false
            },
            {
                id: 'vendedor',
                header: 'Vendedor',
                dataIndex: 'vendedor',
                width: 180,
                sortable: true
            },
            {
                id: 'login2',
                header: 'Login',
                dataIndex: 'login2',
                width: 140,
                sortable: true
            },
            {
                id: 'producto',
                header: 'Servicio',
                dataIndex: 'producto',
                width: 160,
                sortable: true
            },
            {
                id: 'ciudad',
                header: 'Ciudad',
                dataIndex: 'ciudad',
                width: 80,
                sortable: true
            },
            {
                id: 'direccion',
                header: 'Direccion',
                dataIndex: 'direccion',
                width: 180,
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
                id: 'fechaAsignadaReal',
                header: 'Fecha Asignada',
                dataIndex: 'fechaAsignadaReal',
                width: 110,
                sortable: true
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 120,
                items: 
                [
                    {
                        getClass: function(v, meta, rec) {
                            var permiso1 = $("#ROLE_217-1");
                            var boolPermiso1 = (typeof permiso1 === 'undefined') ? false : (permiso1.val() == 1 ? true : false);

                            if (!boolPermiso1) {
                                rec.data.action3 = "icon-invisible";
                            }

                            if (rec.get('action3') == "icon-invisible")
                                this.items[0].tooltip = '';
                            else
                                this.items[0].tooltip = 'Finalizar Retiro Equipo';

                            return rec.get('action3')
                        },
                        tooltip: 'Finalizar Retiro Equipo',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);

                            var permiso1 = $("#ROLE_217-1");
                            var boolPermiso1 = (typeof permiso1 === 'undefined') ? false : (permiso1.val() == 1 ? true : false);

                            if (!boolPermiso1) {
                                rec.data.action3 = "icon-invisible";
                            }

                            if (rec.get('action3') != "icon-invisible")
                                showRetiroEquipo(rec);
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

    /* ******************************************* */
    /* FILTROS DE BUSQUEDA */
    /* ******************************************* */
    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,
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
                {html: "Fecha Asignacion:", border: false, width: 325},
                {html: "&nbsp;", border: false, width: 150},
                {html: "&nbsp;", border: false, width: 325},
                {html: "&nbsp;", border: false, width: 200},
                {html: "&nbsp;", border: false, width: 200},
                DTFechaDesdeAsig,
                {html: "&nbsp;", border: false, width: 150},
                DTFechaHastaAsig,
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
                //se agregan filtros nuevos
                {html: "&nbsp;", border: false, width: 150},
                {
                    xtype: 'textfield',
                    id: 'txtNombre',
                    fieldLabel: 'Nombres',
                    value: '',
                    width: '325'
                },
                {html: "&nbsp;", border: false, width: 200},
                {html: "&nbsp;", border: false, width: 200},
                {
                    xtype: 'textfield',
                    id: 'txtApellido',
                    fieldLabel: 'Apellidos',
                    value: '',
                    width: '325'
                },
                {html: "&nbsp;", border: false, width: 150},
                {
                    xtype: 'textfield',
                    id: 'txtRazonSocial',
                    fieldLabel: 'Razon Social',
                    value: '',
                    width: '325'
                },
                {html: "&nbsp;", border: false, width: 200},
                {html: "&nbsp;", border: false, width: 200},
                {
                    xtype: 'textfield',
                    id: 'txtIdentificacion',
                    fieldLabel: 'Identificacion',
                    value: '',
                    width: '325'
                }

            ],
        renderTo: 'filtro'
    });
});

function buscar() {
    var boolError = false;
    if (Ext.getCmp('txtLogin').getValue() == "" && Ext.getCmp('txtIdentificacion').getValue() == "")
    {
        Ext.Msg.show({
            title: 'Error en Busqueda',
            msg: 'Por Favor realizar la busqueda, al menos con login o identificacion',
            buttons: Ext.Msg.OK,
            icon: Ext.MessageBox.ERROR
        });
    }
    else
    {
        if ((Ext.getCmp('fechaDesdeAsig').getValue() != null) && (Ext.getCmp('fechaHastaAsig').getValue() != null))
        {
            if (Ext.getCmp('fechaDesdeAsig').getValue() > Ext.getCmp('fechaHastaAsig').getValue())
            {
                boolError = true;

                Ext.Msg.show({
                    title: 'Error en Busqueda',
                    msg: 'Por Favor para realizar la busqueda Fecha Desde Asigicacion debe ser fecha menor a Fecha Hasta Asigicacion.',
                    buttons: Ext.Msg.OK,
                    animEl: 'elId',
                    icon: Ext.MessageBox.ERROR
                });
            }

            //Se agrega validacion para filtrar que solo se pueda consultar maximo entre 30 dias en las fechas de planificacion
            var desdeA = Ext.getCmp('fechaDesdeAsig').getValue();
            var hastaA = Ext.getCmp('fechaHastaAsig').getValue();

            var fechaDesdeA = desdeA.getTime();
            var fechaHastaA = hastaA.getTime();


            var differenceA = Math.abs(fechaDesdeA - fechaHastaA)

            //Convierto de milisegundos a dias
            var diasA = differenceA / 86400000;

            if (diasA > 30) {
                boolError = true;
                Ext.Msg.show({
                    title: 'Error en Busqueda',
                    msg: 'Por Favor solo se puede realizar busquedas de hasta 30 dias de diferencia entre la Fecha Desde y Hasta',
                    buttons: Ext.Msg.OK,
                    animEl: 'elId',
                    icon: Ext.MessageBox.ERROR
                });
            }
        }

        if (!boolError)
        {
            store.getProxy().extraParams.fechaDesdeAsig     = Ext.getCmp('fechaDesdeAsig').value;
            store.getProxy().extraParams.fechaHastaAsig     = Ext.getCmp('fechaHastaAsig').value;
            store.getProxy().extraParams.login2             = Ext.getCmp('txtLogin').value;
            store.getProxy().extraParams.descripcionPunto   = Ext.getCmp('txtDescripcionPunto').value;
            store.getProxy().extraParams.vendedor           = Ext.getCmp('txtVendedor').value;
            store.getProxy().extraParams.ciudad             = Ext.getCmp('txtCiudad').value;
            store.getProxy().extraParams.numOrdenServicio   = Ext.getCmp('txtNumOrdenServicio').value;
            //se agregan seteos de filtros nuevos
            store.getProxy().extraParams.nombre             = Ext.getCmp('txtNombre').value;
            store.getProxy().extraParams.apellido           = Ext.getCmp('txtApellido').value;
            store.getProxy().extraParams.razonSocial        = Ext.getCmp('txtRazonSocial').value;
            store.getProxy().extraParams.identificacion     = Ext.getCmp('txtIdentificacion').value;
            store.load();
        }
    }
}

function limpiar() {
    Ext.getCmp('fechaDesdeAsig').setRawValue("");
    Ext.getCmp('fechaHastaAsig').setRawValue("");

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
    //se agregan seteos a nulo de filtros nuevos
    Ext.getCmp('txtNombre').value = "";
    Ext.getCmp('txtNombre').setRawValue("");

    Ext.getCmp('txtApellido').value = "";
    Ext.getCmp('txtApellido').setRawValue("");

    Ext.getCmp('txtRazonSocial').value = "";
    Ext.getCmp('txtRazonSocial').setRawValue("");

    Ext.getCmp('txtIdentificacion').value = "";
    Ext.getCmp('txtIdentificacion').setRawValue("");

    store.getProxy().extraParams.fechaDesdeAsig     = Ext.getCmp('fechaDesdeAsig').value;
    store.getProxy().extraParams.fechaHastaAsig     = Ext.getCmp('fechaHastaAsig').value;
    store.getProxy().extraParams.login2             = Ext.getCmp('txtLogin').value;
    store.getProxy().extraParams.descripcionPunto   = Ext.getCmp('txtDescripcionPunto').value;
    store.getProxy().extraParams.vendedor           = Ext.getCmp('txtVendedor').value;
    store.getProxy().extraParams.ciudad             = Ext.getCmp('txtCiudad').value;
    store.getProxy().extraParams.numOrdenServicio   = Ext.getCmp('txtNumOrdenServicio').value;
    //se agregan seteos a nulo de filtros nuevos
    store.getProxy().extraParams.nombre             = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.apellido           = Ext.getCmp('txtApellido').value;
    store.getProxy().extraParams.razonSocial        = Ext.getCmp('txtRazonSocial').value;
    store.getProxy().extraParams.identificacion     = Ext.getCmp('txtIdentificacion').value;
}
