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
                ultimaMilla: 'Fibra Optica,UTP',
                strFiltrarJurisdiccion: 'SI',
                id_jurisdiccion:'',
                limite: true
            }
        },
        fields:
            [
                {name: 'id_factibilidad', mapping: 'id_factibilidad'},
                {name: 'estadoFactibilidad', mapping: 'estadoFactibilidad'},
                {name: 'intIdPersona', mapping: 'intIdPersona'},
                {name: 'intIdPersonaEmpresaRol', mapping: 'intIdPersonaEmpresaRol'},
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
                {name: 'id_jurisdiccion', mapping: 'id_jurisdiccion'},
                {name: 'jurisdiccion', mapping: 'jurisdiccion'},
                {name: 'nombreSector', mapping: 'nombreSector'},
                {name: 'fePlanificacion', mapping: 'fePlanificacion'},
                {name: 'rutaCroquis', mapping: 'rutaCroquis'},
                {name: 'latitud', mapping: 'latitud'},
                {name: 'longitud', mapping: 'longitud'},
                {name: 'strPrefijoEmpresa', mapping: 'strPrefijoEmpresa'},
                {name: 'strEsEdificio', mapping: 'strEsEdificio'},
                {name: 'strDependeDeEdificio', mapping: 'strDependeDeEdificio'},
                {name: 'strNombreEdificio', mapping: 'strNombreEdificio'},
                {name: 'strTipoEnlace', mapping: 'strTipoEnlace'},
                {name: 'strTipoRed', mapping: 'strTipoRed'},
                {name: 'booleanTipoRedGpon', mapping: 'booleanTipoRedGpon'},
                {name: 'intIdElemento', mapping: 'intIdElemento'},
                {name: 'intIdUltimaMilla', mapping: 'intIdUltimaMilla'},
                {name: 'strCodigoTipoMedio', mapping: 'strCodigoTipoMedio'},
                {name: 'strNombreTipoMedio', mapping: 'strNombreTipoMedio'},
                {name: 'strObraCivil', mapping: 'strObraCivil'},
                {name: 'strPermisosRegeneracion', mapping: 'strPermisosRegeneracion'},
                {name: 'strObservacionPermiRegeneracion', mapping: 'strObservacionPermiRegeneracion'},
                {name: 'grupo', mapping: 'grupo'},
                {name: 'nombreTecnico', mapping: 'nombreTecnico'},
                {name: 'continuaFlujoDC', mapping: 'continuaFlujoDC'},
                {name: 'productosCoreAsociados', mapping: 'productosCoreAsociados'},
                {name: 'idServicioAlqEspacioDC', mapping: 'idServicioAlqEspacioDC'},
                {name: 'contieneAlquilerServidor', mapping: 'contieneAlquilerServidor'},
                {name: 'esSolucion', mapping: 'esSolucion'},
                {name: 'capacidad1', mapping: 'capacidad1'},
                {name: 'capacidad2', mapping: 'capacidad2'},
                {name: 'estado', mapping: 'estado'},
                {name: 'action1', mapping: 'action1'},
                {name: 'action2', mapping: 'action2'},
                {name: 'action3', mapping: 'action3'},
                {name: 'action4', mapping: 'action4'},
                {name: 'action5', mapping: 'action5'},
                {name: 'action6', mapping: 'action6'}
            ],
        autoLoad: true
    });

    storeJurisdicciones = new Ext.data.Store({
        total: 'total',
        proxy: {
            timeout: 3000000,
            type: 'ajax',
            url: url_comboJurisdicciones,
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
                ultimaMilla: 'Fibra Optica,UTP',
                strFiltrarJurisdiccion: 'SI',
                limite: false
            }
        },
        fields:
            [
                {name: 'id_jurisdiccion', mapping: 'id_jurisdiccion'},
                {name: 'jurisdiccion', mapping: 'jurisdiccion'}
                
            ],
        listeners: {
            load: function(store) {
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
        width: 1280,
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
                width: 125,
                sortable: true
            },
            {
                id: 'vendedor',
                header: 'Vendedor',
                dataIndex: 'vendedor',
                width: 105,
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
                width: 100,
                sortable: true
            },
            {
                id: 'ultimaMilla',
                header: 'Ultima Milla',
                dataIndex: 'ultimaMilla',
                width: 60,
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
                width: 105,
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
                width: 165,
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
                            {
                                if((rec.get("strCodigoTipoMedio")=='RAD'||rec.get("strCodigoTipoMedio")=='CO'))
                                {
                                    showRechazarOrden_FactibilidadRadio(rec);
                                }
                                else
                                {
                                    showRechazarOrden_Factibilidad(rec);
                                }
                                
                                
                            }
                            else
                            {
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: 'No tiene permisos para realizar esta accion',
                                    buttons: Ext.MessageBox.OK,
                                    icon: Ext.MessageBox.ERROR
                                });
                            }
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
                            {
                                if((rec.get("strCodigoTipoMedio")=='RAD'||rec.get("strCodigoTipoMedio")=='CO'))
                                {
                                    if ("TN" === rec.get("strPrefijoEmpresa")) 
                                    {
                                        showPreFactibilidadRadio(rec);
                                    }
                                    else
                                    {
                                        showFactibilidadRadioMd(rec);
                                    }
                                }
                                else
                                {
                                    showPreFactibilidad(rec);
                                }
                            }
                            else
                            {
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: 'No tiene permisos para realizar esta accion',
                                    buttons: Ext.MessageBox.OK,
                                    icon: Ext.MessageBox.ERROR
                                });
                            }
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
                            {                                
                                if((rec.get("strCodigoTipoMedio")=='RAD'||rec.get("strCodigoTipoMedio")=='CO'))
                                {
                                    if ("TN" === rec.get("strPrefijoEmpresa")) 
                                    {
                                        showFactibilidadRadioTn(rec);
                                    }
                                    else
                                    {
                                        showFactibilidadMaterialesRadio(rec);
                                    }
                                }
                                else
                                {
                                    if(rec.get("grupo").includes('DATACENTER'))
                                    {
                                        showIngresoFactibilidadDC(rec);
                                    }
                                    else
                                    {
                                        showIngresoFactibilidad(rec);
                                    }
                                }
                            }
                            else
                            {
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: 'No tiene permisos para realizar esta accion',
                                    buttons: Ext.MessageBox.OK,
                                    icon: Ext.MessageBox.ERROR
                                });
                            }
                        }
                    },
                    {
                        getClass: function(v, meta, rec) {
                            var permiso1 = $("#ROLE_267-5477");
                            var boolPermiso1 = (typeof permiso1 === 'undefined') ? false : (permiso1.val() == 1 ? true : false);
                            if (!boolPermiso1) {
                                rec.data.action6 = "icon-invisible";
                            }

                            if (rec.get('action6') == "icon-invisible")
                                this.items[5].tooltip = '';
                            else
                            {
                                if ("TN" === rec.get("strPrefijoEmpresa"))
                                {
                                    this.items[5].tooltip = 'Factibilidad Anticipada';
                                }
                            }

                            return rec.get('action6')
                        },
                        tooltip: 'Factibilidad Anticipada',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);

                            var permiso1 = $("#ROLE_267-5477");
                            var boolPermiso1 = (typeof permiso1 === 'undefined') ? false : (permiso1.val() == 1 ? true : false);
                            if (!boolPermiso1) {
                                rec.data.action6 = "icon-invisible";
                            }

                            if (rec.get('action6') != "icon-invisible")
                            {
                                if ("TN" === rec.get("strPrefijoEmpresa"))
                                {
                                    showFactibilidadAnticipadaRadioTn(rec);
                                }
                            }
                            else
                            {
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: 'No tiene permisos para realizar esta accion',
                                    buttons: Ext.MessageBox.OK,
                                    icon: Ext.MessageBox.ERROR
                                });
                            }
                        }
                    },
                    {
                        getClass: function(v, meta, rec) {
                             var permiso = $("#ROLE_135-5377");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                                if ((rec.get('estadoFactibilidad')=='PreFactibilidad' || rec.get('estadoFactibilidad')=='FactibilidadEnProceso') 
                                     && (rec.get('strPrefijoEmpresa') == "TN")
                                     && (rec.get('producto')=='L3MPLS' || rec.get('producto')=='Internet Dedicado')  )
                                {
                                    if(boolPermiso){ 
                                        return 'button-grid-cambiarEstado';
                                    }
                                    else{
                                        return 'icon-invisible';
                                    } 
                                }
                            
                        },
                        tooltip: 'Cambiar Tipo Medio',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            cambioTipoMedio(rec);
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
    
    if(prefijoEmpresa == "TN")
    {
        grid.headerCt.insert(
                                    8,
                                    {
                                        text: 'T. Enlace',
                                        width: 60,
                                        dataIndex: 'strTipoEnlace',
                                        sortable: true
                                    }
                                );
    }
    if(prefijoEmpresa == "MD")
    {
        grid.headerCt.insert(
                                    15,
                                    {
                                        id: 'nombreSector',
                                        header: 'Sector',
                                        dataIndex: 'nombreSector',
                                        width: 80,
                                        sortable: true
                                    }
                                );
    }

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
        width: 1280,
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

function cambioTipoMedio(rec)
{
    winCambioTipoMedio = "";
    formPanelRechazarOrden_Factibilidad = "";

    if (!winCambioTipoMedio)
    {
        storeMotivos = new Ext.data.Store({
            total: 'total',
            pageSize: 10000,
            proxy: {
                type: 'ajax',
                url: urlMotivosRechazo,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    nombre: '',
                    estado: 'ACTIVE'
                }
            },
            fields:
                [
                    {name: 'id_motivo', mapping: 'id_motivo'},
                    {name: 'nombre_motivo', mapping: 'nombre_motivo'}
                ],
            autoLoad: true
        });
        
        storeTipoMedio = new Ext.data.Store({
            total: 'total',
            pageSize: 10000,
            proxy: {
                type: 'ajax',
                url: urlObtenerTipoMedio,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    strCodigoTipoMedio: rec.get("strCodigoTipoMedio")
                }
            },
            fields:
                [
                    {name: 'id', mapping: 'id'},
                    {name: 'nombre', mapping: 'nombre'}
                ],
            autoLoad: true
        });        
        //******** html campos requeridos...
        var iniHtmlCamposRequeridos = '<p style="text-align: right; font-weight: bold; border: 0 !important;">* Campos requeridos</p>';
        CamposRequeridos = Ext.create('Ext.Component', {
            html: iniHtmlCamposRequeridos,
            padding: 1,
            layout: 'anchor',
            style: { textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
        });

        formPanelCambioTipoMedio = Ext.create('Ext.form.Panel', {
            buttonAlign: 'center',
            BodyPadding: 10,
            bodyStyle: "background: white; padding:10px; border: 0px none;",
            frame: true,
            items: [
                CamposRequeridos,
                {
                    xtype: 'fieldset',
                    title: 'Datos del Cambio',
                    defaultType: 'textfield',
                    style: "font-weight:bold; margin-bottom: 15px;",
                    defaults: {
                        width: '350px'
                    },
                    items: [
                        {
                            xtype: 'combobox',
                            id: 'cmbTipoMedio',
                            fieldLabel: '* Tipo Medio',
                            typeAhead: true,
                            triggerAction: 'all',
                            displayField: 'nombre',
                            valueField: 'id',
                            selectOnTab: true,
                            store: storeTipoMedio,
                            lazyRender: true,
                            queryMode: "local"
                        }, 
                        {
                            xtype: 'combobox',
                            id: 'cmbMotivo',
                            fieldLabel: '* Motivo',
                            typeAhead: true,
                            triggerAction: 'all',
                            displayField: 'nombre_motivo',
                            valueField: 'id_motivo',
                            selectOnTab: true,
                            store: storeMotivos,
                            lazyRender: true,
                            queryMode: "local"
                        },                         
                        {
                            xtype: 'textarea',
                            fieldLabel: '* Observacion',
                            name: 'info_observacion',
                            id: 'info_observacion',
                            allowBlank: false
                        }
                    ]
                }
            ],
            buttons: [
                {
                    text: 'Cambiar',
                    handler: function() {
                        var txtObservacion = Ext.getCmp('info_observacion').value;
                        var cmbMotivo = Ext.getCmp('cmbMotivo').value;
                        var id_factibilidad = rec.get("id_factibilidad");
                        var idTipoMedio = Ext.getCmp('cmbTipoMedio').value;

                        var boolError = false;
                        var mensajeError = "";
                        if (!id_factibilidad || id_factibilidad == "" || id_factibilidad == 0)
                        {
                            boolError = true;
                            mensajeError += "El id del Detalle Solicitud no existe.\n";
                        }
                        if (!cmbMotivo || cmbMotivo == "" || cmbMotivo == 0)
                        {
                            boolError = true;
                            mensajeError += "El motivo no fue escogido, por favor seleccione.\n";
                        }
                        if (!txtObservacion || txtObservacion == "" || txtObservacion == 0)
                        {
                            boolError = true;
                            mensajeError += "La observacion no fue ingresada, por favor ingrese.\n";
                        }
                        
                        if (!idTipoMedio || idTipoMedio == "" || idTipoMedio == 0)
                        {
                            boolError = true;
                            mensajeError += "Seleccione el tipo de medio.\n";
                        }                        

                        if (!boolError)
                        {
                            connFactibilidad.request({
                                method: 'POST',
                                timeout: 120000,
                                params: {idSolicitud: id_factibilidad, 
                                        idTipomedio: idTipoMedio, 
                                        idMotivo: cmbMotivo, 
                                        observacion: txtObservacion},
                                url: urlCambiarTipoMedio,
                                success: function(response) {
                                    var text = response.responseText;
                                    
                                    winCambioTipoMedio.close();
                                    winCambioTipoMedio.destroy();                                    
                                    
                                    if (text == "OK")
                                    {
                                        Ext.Msg.alert('Mensaje', 'Transacción Exitosa.', function(btn) {
                                            if (btn == 'ok') {
                                                store.load();
                                            }
                                        });
                                    }
                                    else {
                                        Ext.MessageBox.show({
                                            title: 'Error',
                                            msg: text,
                                            buttons: Ext.MessageBox.OK,
                                            icon: Ext.MessageBox.ERROR
                                        });
                                    }
                                },
                                failure: function(result) {
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: result.responseText,
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                }
                            });
                        }
                        else {
                            Ext.MessageBox.show({
                                title: 'Error',
                                msg: mensajeError,
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.ERROR
                            });
                        }
                    }
                },
                {
                    text: 'Cerrar',
                    handler: function() {
                        winCambioTipoMedio.close();
                        winCambioTipoMedio.destroy();

                    }
                }
            ]
        });

        winCambioTipoMedio = Ext.widget('window', {
            title: 'Cambio de Tipo Medio',
            layout: 'fit',
            resizable: false,
            modal: true,
            closable: false,
            items: [formPanelCambioTipoMedio]
        });
    }
    winCambioTipoMedio.show();
}



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
        store.removeAll();
        store.currentPage = 1;
        store.getProxy().extraParams.fechaDesdePlanif = Ext.getCmp('fechaDesdePlanif').value;
        store.getProxy().extraParams.fechaHastaPlanif = Ext.getCmp('fechaHastaPlanif').value;
        store.getProxy().extraParams.fechaDesdeIngOrd = Ext.getCmp('fechaDesdeIngOrd').value;
        store.getProxy().extraParams.fechaHastaIngOrd = Ext.getCmp('fechaHastaIngOrd').value;
        store.getProxy().extraParams.login2 = Ext.getCmp('txtLogin').value;
        store.getProxy().extraParams.descripcionPunto = Ext.getCmp('txtDescripcionPunto').value;
        store.getProxy().extraParams.vendedor = Ext.getCmp('txtVendedor').value;
        store.getProxy().extraParams.ciudad = Ext.getCmp('txtCiudad').value;
        store.getProxy().extraParams.numOrdenServicio = Ext.getCmp('txtNumOrdenServicio').value;
        store.getProxy().extraParams.ultimaMilla = 'Fibra Optica,UTP';
        store.getProxy().extraParams.id_jurisdiccion = Ext.getCmp('comboJurisdiccion').value.toString();
        if (store.getProxy().extraParams.id_jurisdiccion == '-1')
        {
            limpiar();
        }
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

    Ext.getCmp('comboJurisdiccion').value = "";
    Ext.getCmp('comboJurisdiccion').setRawValue("");

    store.getProxy().extraParams.fechaDesdePlanif = Ext.getCmp('fechaDesdePlanif').value;
    store.getProxy().extraParams.fechaHastaPlanif = Ext.getCmp('fechaHastaPlanif').value;
    store.getProxy().extraParams.fechaDesdeIngOrd = Ext.getCmp('fechaDesdeIngOrd').value;
    store.getProxy().extraParams.fechaHastaIngOrd = Ext.getCmp('fechaHastaIngOrd').value;
    store.getProxy().extraParams.login2 = Ext.getCmp('txtLogin').value;
    store.getProxy().extraParams.descripcionPunto = Ext.getCmp('txtDescripcionPunto').value;
    store.getProxy().extraParams.vendedor = Ext.getCmp('txtVendedor').value;
    store.getProxy().extraParams.ciudad = Ext.getCmp('txtCiudad').value;
    store.getProxy().extraParams.numOrdenServicio = Ext.getCmp('txtNumOrdenServicio').value;
    store.getProxy().extraParams.id_jurisdiccion = Ext.getCmp('comboJurisdiccion').value.toString();
    store.load();
}
