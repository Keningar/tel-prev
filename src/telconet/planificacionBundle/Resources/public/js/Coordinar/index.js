/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();

    //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA


    DTFechaDesdePlanif = Ext.create('Ext.data.fecha', {
        id: 'fechaDesdePlanif',
        name: 'fechaDesdePlanif',
        fieldLabel: 'Desde'
    });

    DTFechaHastaPlanif = Ext.create('Ext.data.fecha', {
        id: 'fechaHastaPlanif',
        name: 'fechaHastaPlanif',
        fieldLabel: 'Hasta'
    });

    DTFechaDesdeIngOrd = Ext.create('Ext.data.fecha', {
        id: 'fechaDesdeIngOrd',
        name: 'fechaDesdeIngOrd',
        fieldLabel: 'Desde'
    });
    DTFechaHastaIngOrd = Ext.create('Ext.data.fecha', {
        id: 'fechaHastaIngOrd',
        name: 'fechaHastaIngOrd',
        fieldLabel: 'Hasta'
    });

    cmbSector = Ext.create('Ext.data.comboSectores', {
        id: 'cmbSector',
        name: 'cmbSector'});

    cmbEstadoPunto = Ext.create('Ext.data.comboEstadoPunto', {
        id: 'cmbEstadoPunto',
        name: 'cmbEstadoPunto'});

    storeEstadoPunto.on('load', function(store, records, successful, eOpts) {
        store.insert(0, [{estado_punto_busqueda: 'Todos'}]);
        cmbEstadoPunto.select('Todos', true);
    });

    storeTipoSolicitud = getTiposPlanificacion();
    cmbTipoSolicitud = Ext.create('Ext.data.comboGenericoList', {store: storeTipoSolicitud,
        fieldLabel: "Tipo Solicitud",
        id: 'filtro_tipo_solicitud',
        name: 'filtro_tipo_solicitud'});


    cmbUltimaMilla = Ext.create('Ext.data.comboUltimaMilla', {fieldLabel: "Ultima Milla",
        id: 'cmbUltimaMilla',
        name: 'cmbUltimaMilla'});



    Ext.define('cboSelectedCountSector', {
        alias: 'plugin.selectedCount',
        init: function(cboCountSector) {
            cboCountSector.on({
                select: function(me, objRecords) {
                    intNumeroRegistros = objRecords.length,
                        storeCboCountSector = cboCountSector.getStore(),
                        boolDiffRowCbo = objRecords.length != storeCboCountSector.count,
                        boolNewAll = false,
                        boolSelectedAll = false,
                        objNewRecords = [];
                    Ext.each(objRecords, function(obj, i, objRecordsItself) {
                        //Pregunta si el registro seleccionado es 0 entonces seleccionado todo
                        if (objRecords[i].data.intIdCanalPagoLinea === 0) {
                            boolSelectedAll = true;
                            //Si no esta todo seleccionado, permite seleccionar todo nuevamente
                            if (!cboCountSector.boolCboSelectedAll) {
                                intNumeroRegistros = storeCboCountSector.getCount();
                                cboCountSector.select(storeCboCountSector.getRange());
                                cboCountSector.boolCboSelectedAll = true;
                                boolNewAll = true;
                            }
                        } else {
                            if (boolDiffRowCbo && !boolNewAll)
                                objNewRecords.push(objRecords[i]);
                        }

                    });
                    //Validacion que realiza el uncheck del combo
                    if (cboCountSector.boolCboSelectedAll && !boolSelectedAll) {
                        cboCountSector.clearValue();
                        cboCountSector.boolCboSelectedAll = false;
                    } else if (boolDiffRowCbo && !boolNewAll) {
                        cboCountSector.select(objNewRecords);
                        cboCountSector.boolCboSelectedAll = false;
                    }
                }
            });
        }
    });



    var storeCiudades = new Ext.data.Store
        ({
            total: 'total',
            autoLoad: true,
            proxy:
                {
                    type: 'ajax',
                    url: strUrlGetCiudades,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        }
                },
            fields:
                [
                    {name: 'id_canton', mapping: 'id_canton'},
                    {name: 'nombre_canton', mapping: 'nombre_canton'}
                ]
        });

    var cmbCiudades = new Ext.form.ComboBox
        ({
            id: 'cmbCiudades',
            name: 'cmbCiudades',
            fieldLabel: 'Ciudad',
            plugins: ['selectedCount'],
            //editable: false,
            //disabled: true,
            anchor: '100%',
            queryMode: 'local',
            width: '325',
            emptyText: 'Seleccione Ciudad',
            store: storeCiudades,
            displayField: 'nombre_canton',
            valueField: 'nombre_canton',
            multiSelect: true,

            displayTpl: '<tpl for="."> {nombre_canton} <tpl if="xindex < xcount">, </tpl> </tpl>',
            listConfig: {
                itemTpl: '{nombre_canton} <div class="uncheckedChkbox"></div>'
            },

            //renderTo: 'divCiudad'
        });




    //store =  Ext.create('Ext.data.storeCoordinar',{mName:''});
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
                fechaDesdePlanif: '',
                fechaHastaPlanif: '',
                fechaDesdeIngOrd: '',
                fechaHastaIngOrd: '',
                login: '',
                descripcionPunto: '',
                vendedor: '',
                ciudad: '',
                numOrdenServicio: '',
                ultimaMilla: '',
                estado: 'Todos'
            }
        },
        model: Ext.create('Ext.data.modelCoordinar', {mName: 'modelCoordinar'})
            //autoLoad: true
    });

    var permiso = $("#ROLE_145-37");
    var boolPermiso1 = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

    var exportarBtn = "";
    if (boolPermiso1)
    {
        exportarBtn = Ext.create('Ext.button.Button', {
            iconCls: 'icon_exportar',
            itemId: 'exportar',
            text: 'Exportar',
            scope: this,
            handler: function() {
                exportarExcel();
            }
        });
    }
    else
    {
        exportarBtn = Ext.create('Ext.Component', {
            html: '',
            height: 5,
            padding: 0,
            layout: 'anchor',
            style: {color: '#000000'}
        });
    }

    var toolbar = Ext.create('Ext.toolbar.Toolbar', {
        dock: 'top',
        align: '->',
        items: ['->', exportarBtn],
        fields:
            [
                {name: 'id_factibilidad',           mapping: 'id_factibilidad'},
                {name: 'id_servicio',               mapping: 'id_servicio'},
                {name: 'tipo_orden',                mapping: 'tipo_orden'},
                {name: 'id_punto',                  mapping: 'id_punto'},
                {name: 'estado_punto',              mapping: 'estado_punto'},
                {name: 'caja',                      mapping: 'caja'},
                {name: 'tercerizadora',             mapping: 'tercerizadora'},
                {name: 'id_orden_trabajo',          mapping: 'id_orden_trabajo'},
                {name: 'cliente',                   mapping: 'cliente'},
                {name: 'descripcionSolicitud',      mapping: 'descripcionSolicitud'},
                {name: 'vendedor',                  mapping: 'vendedor'},
                {name: 'login2',                    mapping: 'login2'},
                {name: 'esRecontratacion',          mapping: 'esRecontratacion'},
                {name: 'producto',                  mapping: 'producto'},
                {name: 'coordenadas',               mapping: 'coordenadas'},
                {name: 'direccion',                 mapping: 'direccion'},
                {name: 'observacion',               mapping: 'observacion'},
                {name: 'telefonos',                 mapping: 'telefonos'},
                {name: 'ciudad',                    mapping: 'ciudad'},
                {name: 'jurisdiccion',              mapping: 'jurisdiccion'},
                {name: 'nombreSector',              mapping: 'nombreSector'},
                {name: 'ultimaMilla',               mapping: 'ultimaMilla'},
                {name: 'strMetraje',                mapping: 'strMetraje'},
                {name: 'strPrefijoEmpresa',         mapping: 'strPrefijoEmpresa'},
                {name: 'feSolicitaPlanificacion',   mapping: 'feSolicitaPlanificacion'},
                {name: 'fePlanificada',             mapping: 'fePlanificada'},
                {name: 'HoraIniPlanificada',        mapping: 'HoraIniPlanificada'},
                {name: 'HoraFinPlanificada',        mapping: 'HoraFinPlanificada'},
                {name: 'latitud',                   mapping: 'latitud'},
                {name: 'longitud',                  mapping: 'longitud'},
                {name: 'strTipoEnlace',             mapping: 'strTipoEnlace'},
                {name: 'estado',                    mapping: 'estado'},
                {name: 'tituloCoordinar',           mapping: 'tituloCoordinar'},
                {name: 'esSolucion',                mapping: 'esSolucion'},
                {name: 'precioFibra',               mapping: 'precioFibra'},
                {name: 'metrosDeDistancia',         mapping: 'metrosDeDistancia'},
                {name: 'nombreTecnico',             mapping: 'nombreTecnico'},
                {name: 'tipo_esquema',              mapping: 'tipo_esquema'},
                {name: 'idIntWifiSim',              mapping: 'idIntWifiSim'},
                {name: 'idIntCouSim',               mapping: 'idIntCouSim'},
                {name: 'arraySimultaneos',          mapping: 'arraySimultaneos'},
                {name: 'strTipoRed',                mapping: 'strTipoRed'},
                {name: 'action1',                   mapping: 'action1'},
                {name: 'action2',                   mapping: 'action2'},
                {name: 'action3',                   mapping: 'action3'},
                {name: 'action4',                   mapping: 'action4'},
                {name: 'action5',                   mapping: 'action5'},
                {name: 'boolMarcarRechazo',         mapping: 'boolMarcarRechazo'}
            ]
    });

    var pluginExpanded = true;

    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    });

    if (prefijoEmpresa == "MD")
    {
        grid = Ext.create('Ext.grid.Panel', {
            width: 1230,
            height: 500,
            store: store,
            dockedItems: [toolbar],
            loadMask: true,
            frame: false,
            columns:
                [
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
                        id: 'observacion',
                        header: 'observacion',
                        dataIndex: 'observacion',
                        hidden: true,
                        hideable: false
                    },
                    {
                        id: 'telefonos',
                        header: 'telefonos',
                        dataIndex: 'telefonos',
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
                        id: 'tercerizadora',
                        header: 'tercerizadora',
                        dataIndex: 'tercerizadora',
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
                        id: 'tipo_orden',
                        header: 'TipoOrden',
                        dataIndex: 'tipo_orden',
                        hidden: true,
                        hideable: false
                    },
                    {
                        id: 'descripcionSolicitud',
                        header: 'Tipo Solicitud',
                        dataIndex: 'descripcionSolicitud',
                        width: 130,
                        sortable: true,
                        renderer: function(value, metaData) {
                            metaData.style="white-space: inherit";
                            return value;
                        }
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
                        width: 80,
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
                        id: 'estado_punto',
                        header: 'Estado del Punto',
                        dataIndex: 'estado_punto',
                        width: 100
                    },
                    {
                        id: 'producto',
                        header: 'Producto',
                        dataIndex: 'producto',
                        width: 160,
                        sortable: true,
                        renderer: function(value, metaData) {
                            metaData.style="white-space: inherit";
                            return value;
                        }
                    },
                    {
                        id: 'caja',
                        header: 'Caja',
                        dataIndex: 'caja',
                        width: 75,
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
                        width: 130,
                        sortable: true
                    },
                    {
                        id: 'nombreSector',
                        header: 'Sector',
                        dataIndex: 'nombreSector',
                        width: 90,
                        sortable: true
                    },
                    {
                        id: 'feSolicitaPlanificacion',
                        header: 'Fecha Sol. Planificacion',
                        dataIndex: 'feSolicitaPlanificacion',
                        width: 90,
                        sortable: true
                    },
                    {
                        id: 'fePlanificada',
                        header: 'Fecha Planificacion',
                        dataIndex: 'fePlanificada',
                        width: 150,
                        renderer: function(value, p, r) {
                            return r.data['fePlanificada'] + ' ' + r.data['HoraIniPlanificada'];
                        }
                    },
                    {
                        id: 'estado',
                        header: 'Estado',
                        dataIndex: 'estado',
                        width: 80,
                        sortable: true,
                        renderer: function(value, p, r) {
                            if(r.data['boolMarcarRechazo']){
                                return '<p style="color:red;">value</p>'
                            }
                            return value;
                        }
                    },
                    {
                        id: 'idComunicacion',
                        dataIndex: 'intIdComunicacion',
                        hidden: true,
                        hideable: false
    
                    },
                    {
                        id: 'idDetalle',
                        dataIndex: 'intIdDetalle',
                        hidden: true,
                        hideable: false
                    },
                    {
                        id: 'tareaEsHal',
                        dataIndex: 'strTareaEsHal',
                        hidden: true,
                        hideable: false
                    },
                    {
                        xtype: 'actioncolumn',
                        header: 'Acciones',
                        width: 225,
                        items: [
                            {
                                getClass: function(v, meta, rec) {
                                    var permiso = $("#ROLE_137-103");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                    if (!boolPermiso) {
                                        rec.data.action1 = "icon-invisible";
                                    }

                                    if (rec.get('action1') == "icon-invisible")
                                        this.items[0].tooltip = '';
                                    else
                                        this.items[0].tooltip = 'Programar';

                                    return rec.get('action1')
                                },
                                tooltip: 'Programar',
                                handler: function(grid, rowIndex, colIndex) {
                                    var rec = store.getAt(rowIndex);

                                    var permiso = $("#ROLE_137-103");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                    if (!boolPermiso) {
                                        rec.data.action1 = "icon-invisible";
                                    }

                                    if (rec.get('action1') != "icon-invisible")
                                    {
                                        showProgramar(rec, 'local', 0);
                                        //store.load();

                                    } else
                                        Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                                }
                            },
                            {
                                getClass: function(v, meta, rec) {
                                    var permiso = $("#ROLE_137-104");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                    if (!boolPermiso) {
                                        rec.data.action2 = "icon-invisible";
                                    }

                                    if (rec.get('action2') == "icon-invisible")
                                        this.items[1].tooltip = '';
                                    else
                                        this.items[1].tooltip = 'Replanificar';

                                    return rec.get('action2');
                                },
                                tooltip: 'Replanificar',
                                handler: function(grid, rowIndex, colIndex, ) {
                                    var rec = store.getAt(rowIndex);

                                    var permiso = $("#ROLE_137-103");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                    if (!boolPermiso) {
                                        rec.data.action2 = "icon-invisible";
                                    }

                                    if (rec.get('action2') != "icon-invisible")
                                    {
                                        var permiso = $("#ROLE_137-9711");
                                        var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                        showRePlanificar(rec, 'local', boolPermiso);

                                    } else
                                        Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                                }
                            },
                            {
                                getClass: function(v, meta, rec) {
                                    var permiso = $("#ROLE_137-106");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                    if (!boolPermiso) {
                                        rec.data.action3 = "icon-invisible";
                                    }
                                    if (rec.data.descripcionSolicitud == 'Solicitud Migracion')
                                    {
                                        rec.data.action3 = "icon-invisible";
                                    }
                                    if (rec.get('action3') == "icon-invisible")
                                        this.items[2].tooltip = '';
                                    else
                                        this.items[2].tooltip = 'Detener';

                                    return rec.get('action3')
                                },
                                tooltip: 'Detener',
                                handler: function(grid, rowIndex, colIndex) {
                                    var rec = store.getAt(rowIndex);

                                    var permiso = $("#ROLE_137-103");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                    if (!boolPermiso) {
                                        rec.data.action3 = "icon-invisible";
                                    }
                                    if (rec.data.descripcionSolicitud == 'Solicitud Migracion')
                                    {
                                        rec.data.action3 = "icon-invisible";
                                    }

                                    if (rec.get('action3') != "icon-invisible")
                                        showDetener_Coordinar(rec, 'local');
                                    else
                                        Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                                }
                            },
                            {
                                getClass: function(v, meta, rec) {
                                    var permiso = $("#ROLE_137-225");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                    if (!boolPermiso) {
                                        rec.data.action4 = "icon-invisible";
                                    }
                                    if (rec.data.descripcionSolicitud == 'Solicitud Migracion')
                                    {
                                        rec.data.action4 = "icon-invisible";
                                    }
                                    if (rec.get('action4') == "icon-invisible")
                                        this.items[3].tooltip = '';
                                    else
                                        this.items[3].tooltip = 'Anular Orden';

                                    return rec.get('action4')
                                },
                                tooltip: 'Anular Orden',
                                handler: function(grid, rowIndex, colIndex) {
                                    var rec = store.getAt(rowIndex);

                                    var permiso = $("#ROLE_137-103");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                    if (!boolPermiso) {
                                        rec.data.action4 = "icon-invisible";
                                    }
                                    if (rec.data.descripcionSolicitud == 'Solicitud Migracion')
                                    {
                                        rec.data.action4 = "icon-invisible";
                                    }
                                    if (rec.get('action4') != "icon-invisible")
                                        showAnularOrden_Coordinar(rec);
                                    else
                                        Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                                }
                            },
                            {
                                getClass: function(v, meta, rec) {
                                    var permiso = $("#ROLE_137-105");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                    if (!boolPermiso) {
                                        rec.data.action5 = "icon-invisible";
                                    }

                                    if (rec.get('action5') == "icon-invisible")
                                        this.items[4].tooltip = '';
                                    else
                                        this.items[4].tooltip = 'Rechazar Orden';

                                    return rec.get('action5')
                                },
                                tooltip: 'Rechazar Orden',
                                handler: function(grid, rowIndex, colIndex) {
                                    var rec = store.getAt(rowIndex);

                                    var permiso = $("#ROLE_137-103");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                    if (!boolPermiso) {
                                        rec.data.action5 = "icon-invisible";
                                    }

                                    if (rec.get('action5') != "icon-invisible")
                                        showRechazarOrden_Coordinar(rec);
                                    else
                                        Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                                }
                            },
                            {
                                getClass: function(v, meta, rec) {
                                    return rec.get('action10')
                                },
                                tooltip: 'Ingresar Seguimiento',
                                handler: function(grid, rowIndex, colIndex) {
                                    var rec = store.getAt(rowIndex);

                                    if (rec.get('action10') != "icon-invisible")
                                    {
                                        agregarSeguimiento(rec);
                                    }
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
                                    return rec.get('action6')
                                },
                                tooltip: 'Ver Mapa',
                                handler: function(grid, rowIndex, colIndex) {
                                    var rec = store.getAt(rowIndex);

                                    if (rec.get("latitud") != 0 && rec.get("longitud") != 0)
                                        showVerMapa(rec);
                                    else
                                        Ext.Msg.alert('Error ', 'Las coordenadas son incorrectas');
                                }
                            },
                            {
                                getClass: function(v, meta, rec) {
                                    return rec.get('action7')
                                },
                                tooltip: 'Ver Croquis',
                                handler: function(grid, rowIndex, colIndex) {
                                    var rec = store.getAt(rowIndex);

                                    if (rec.get("id_factibilidad") != "" && rec.get("rutaCroquis") != "")
                                        showVerCroquis(rec.get('id_factibilidad'), rec.get('rutaCroquis'));
                                    else
                                        Ext.Msg.alert('Error ', 'Las ruta no existe');
                                }
                            },

                            {
                                getClass: function(v, meta, rec) {
                                    var permiso = $("#ROLE_137-103");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                    /*if (!boolPermiso) {
                                     rec.data.action8 = "icon-invisible";
                                     }*/

                                    if (rec.get('action8') == "icon-invisible")
                                        this.items[2].tooltip = '';
                                    else
                                        this.items[2].tooltip = 'Asignar Responsable';

                                    return rec.get('action8')
                                },
                                tooltip: 'Asignar Responsable',
                                handler: function(grid, rowIndex, colIndex) {
                                    var rec = store.getAt(rowIndex);

                                    var permiso = $("#ROLE_137-103");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                    /*if (!boolPermiso) {
                                     rec.data.action8 = "icon-invisible";
                                     }*/

                                    if (rec.get('action8') != "icon-invisible")
                                    {
                                        showProgramar(rec, 'local', 1);
                                    }
                                    //showAsignacionIndividual(rec, 'local', '0', false);
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
                                    var permiso = $("#ROLE_137-103");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                    /*if (!boolPermiso) {
                                     rec.data.action8 = "icon-invisible";
                                     }*/

                                    if (rec.get('action9') == "icon-invisible")
                                        this.items[2].tooltip = '';
                                    else
                                        this.items[2].tooltip = 'Asignar Responsable';

                                    return rec.get('action9')
                                },
                                tooltip: 'Asignar Responsable',
                                handler: function(grid, rowIndex, colIndex) {
                                    var rec = store.getAt(rowIndex);

                                    var permiso = $("#ROLE_137-103");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                    /*if (!boolPermiso) {
                                     rec.data.action8 = "icon-invisible";
                                     }*/

                                    if (rec.get('action9') != "icon-invisible")
                                    {
                                        showProgramar(rec, 'local', 1);
                                    }
                                    //showAsignacionIndividual(rec, 'local', '0', false);
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
            renderTo: 'grid',
            listeners: 
            {
                itemdblclick: function(view, record, item, index, eventobj, obj) {
                    var position = view.getPositionByEvent(eventobj),
                        data = record.data,
                        value = data[this.columns[position.column].dataIndex];
                    Ext.Msg.show({
                        title: 'Copiar texto?',
                        msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                        buttons: Ext.Msg.OK,
                        icon: Ext.Msg.INFORMATION
                    });
                }
            }
        });

    } else {
        grid = Ext.create('Ext.grid.Panel', {
            width: 1230,
            height: 500,
            store: store,
            dockedItems: [toolbar],
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
                    id: 'observacion',
                    header: 'observacion',
                    dataIndex: 'observacion',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'telefonos',
                    header: 'telefonos',
                    dataIndex: 'telefonos',
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
                    id: 'tercerizadora',
                    header: 'tercerizadora',
                    dataIndex: 'tercerizadora',
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
                    id: 'tipo_orden',
                    header: 'TipoOrden',
                    dataIndex: 'tipo_orden',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'descripcionSolicitud',
                    header: 'Tipo Solicitud',
                    dataIndex: 'descripcionSolicitud',
                    width: 130,
                    sortable: true
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
                    width: 80,
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
                    id: 'tipo_esquema',
                    header: 'Esquema',
                    dataIndex: 'tipo_esquema',
                    width: 60,
                    sortable: true,
                    align: 'center'
                },
                {
                    id: 'estado_punto',
                    header: 'Estado del Punto',
                    dataIndex: 'estado_punto',
                    width: 90
                },
                /*{
                 id: 'strTipoEnlace',
                 header: 'T. Enlace',
                 dataIndex: 'strTipoEnlace',
                 width: 60,
                 sortable: true
                 },*/
                {
                    id: 'producto',
                    header: 'Producto',
                    dataIndex: 'producto',
                    width: 125,
                    sortable: true
                },
                {
                    id: 'jurisdiccion',
                    header: 'Jurisdiccion',
                    dataIndex: 'jurisdiccion',
                    width: 75,
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
                    width: 130,
                    sortable: true
                },
                {
                    id: 'ultimaMilla',
                    header: 'Ultima Milla',
                    dataIndex: 'ultimaMilla',
                    width: 70,
                    sortable: true
                },
                {
                    id: 'feSolicitaPlanificacion',
                    header: 'F. Sol. Planifica',
                    dataIndex: 'feSolicitaPlanificacion',
                    width: 90,
                    sortable: true

                },
                {
                    id: 'fePlanificada',
                    header: 'Fecha Planificacion',
                    dataIndex: 'fePlanificada',
                    width: 150,
                    renderer: function(value, p, r) {
                        return r.data['fePlanificada'] + ' ' + r.data['HoraIniPlanificada'];
                    }
                },
                {
                    id: 'estado',
                    header: 'Estado',
                    dataIndex: 'estado',
                    width: 80,
                    sortable: true
                },
                {
                    id: 'idComunicacion',
                    dataIndex: 'intIdComunicacion',
                    hidden: true,
                    hideable: false

                },
                {
                    id: 'idDetalle',
                    dataIndex: 'intIdDetalle',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'tareaEsHal',
                    dataIndex: 'strTareaEsHal',
                    hidden: true,
                    hideable: true
                },
                {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: 350,
                    items: [
                        {
                            getClass: function(v, meta, rec) {
                                var permiso = $("#ROLE_137-103");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                if (!boolPermiso) {
                                    rec.data.action1 = "icon-invisible";
                                }

                                if (rec.get('action1') == "icon-invisible")
                                    this.items[0].tooltip = '';
                                else
                                    this.items[0].tooltip = 'Programar';

                                return rec.get('action1');
                            },
                            tooltip: 'Programar',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);

                                var permiso = $("#ROLE_137-103");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                if (!boolPermiso) {
                                    rec.data.action1 = "icon-invisible";
                                }

                                if (rec.get('action1') != "icon-invisible")
                                {
                                    showProgramar(rec, 'local', 0);
                                } else
                                    Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                var permiso = $("#ROLE_137-104");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                if (!boolPermiso) {
                                    rec.data.action2 = "icon-invisible";
                                }

                                if (rec.get('action2') == "icon-invisible")
                                    this.items[1].tooltip = '';
                                else
                                    this.items[1].tooltip = 'Replanificar';

                                return rec.get('action2')
                            },
                            tooltip: 'Replanificar',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);

                                var permiso = $("#ROLE_137-103");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                if (!boolPermiso) {
                                    rec.data.action2 = "icon-invisible";
                                }

                                if (rec.get('action2') != "icon-invisible")
                                {
                                    var permiso = $("#ROLE_137-9711");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

                                    showRePlanificar(rec, 'local', boolPermiso);
                                } else
                                    Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                var permiso = $("#ROLE_137-106");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                if (!boolPermiso) {
                                    rec.data.action3 = "icon-invisible";
                                }

                                if (rec.get('action3') == "icon-invisible")
                                    this.items[2].tooltip = '';
                                else
                                    this.items[2].tooltip = 'Detener';

                                return rec.get('action3')
                            },
                            tooltip: 'Detener',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);

                                var permiso = $("#ROLE_137-103");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                if (!boolPermiso) {
                                    rec.data.action3 = "icon-invisible";
                                }

                                if (rec.get('action3') != "icon-invisible")
                                    showDetener_Coordinar(rec, 'local');
                                else
                                    Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                var permiso = $("#ROLE_137-225");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                if (!boolPermiso) {
                                    rec.data.action4 = "icon-invisible";
                                }

                                if (rec.get('action4') == "icon-invisible")
                                    this.items[3].tooltip = '';
                                else
                                    this.items[3].tooltip = 'Anular Orden';

                                return rec.get('action4')
                            },
                            tooltip: 'Anular Orden',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);

                                var permiso = $("#ROLE_137-103");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                if (!boolPermiso) {
                                    rec.data.action4 = "icon-invisible";
                                }

                                if (rec.get('action4') != "icon-invisible")
                                    showAnularOrden_Coordinar(rec);
                                else
                                    Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                var permiso = $("#ROLE_137-105");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                if (!boolPermiso) {
                                    rec.data.action5 = "icon-invisible";
                                }

                                if (rec.get('action5') == "icon-invisible")
                                    this.items[4].tooltip = '';
                                else
                                    this.items[4].tooltip = 'Rechazar Orden';

                                return rec.get('action5')
                            },
                            tooltip: 'Rechazar Orden',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);

                                var permiso = $("#ROLE_137-103");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                if (!boolPermiso) {
                                    rec.data.action5 = "icon-invisible";
                                }

                                if (rec.get('action5') != "icon-invisible")
                                    showRechazarOrden_Coordinar(rec);
                                else
                                    Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                return rec.get('action10')
                            },
                            tooltip: 'Ingresar Seguimiento',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);

                                if (rec.get('action10') != "icon-invisible")
                                {
                                    agregarSeguimiento(rec);
                                }
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
                                return rec.get('action6')
                            },
                            tooltip: 'Ver Mapa',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);

                                if (rec.get("latitud") != 0 && rec.get("longitud") != 0)
                                    showVerMapa(rec);
                                else
                                    Ext.Msg.alert('Error ', 'Las coordenadas son incorrectas');
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                return rec.get('action7')
                            },
                            tooltip: 'Ver Croquis',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);

                                if (rec.get("id_factibilidad") != "" && rec.get("rutaCroquis") != "")
                                    showVerCroquis(rec.get('id_factibilidad'), rec.get('rutaCroquis'));
                                else
                                    Ext.Msg.alert('Error ', 'Las ruta no existe');
                            }
                        },

                        {
                            getClass: function(v, meta, rec) {
                                var permiso = $("#ROLE_137-103");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                /*if (!boolPermiso) {
                                 rec.data.action8 = "icon-invisible";
                                 }*/

                                if (rec.get('action8') == "icon-invisible")
                                    this.items[2].tooltip = '';
                                else
                                    this.items[2].tooltip = 'Asignar Responsable';

                                return rec.get('action8')
                            },
                            tooltip: 'Asignar Responsable',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);

                                var permiso = $("#ROLE_137-103");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                /*if (!boolPermiso) {
                                 rec.data.action8 = "icon-invisible";
                                 }*/

                                if (rec.get('action8') != "icon-invisible")
                                {
                                    showProgramar(rec, 'local', 1);
                                }
                                //showAsignacionIndividual(rec, 'local', '0', false);
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
                                var permiso = $("#ROLE_137-103");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                /*if (!boolPermiso) {
                                 rec.data.action8 = "icon-invisible";
                                 }*/

                                if (rec.get('action9') == "icon-invisible")
                                    this.items[2].tooltip = '';
                                else
                                    this.items[2].tooltip = 'Asignar Responsable';

                                return rec.get('action9')
                            },
                            tooltip: 'Asignar Responsable',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);

                                var permiso = $("#ROLE_137-103");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                /*if (!boolPermiso) {
                                 rec.data.action8 = "icon-invisible";
                                 }*/

                                if (rec.get('action9') != "icon-invisible")
                                {
                                    showProgramar(rec, 'local', 1);
                                    store.load();
                                }
                                //showAsignacionIndividual(rec, 'local', '0', false);
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
                                if (!rec.get('seguimiento'))
                                {
                                    this.items[10].tooltip = '';
                                }
                                else
                                {
                                    this.items[10].tooltip = 'Seguimiento';
                                    return rec.get('action13');
                                }
                            },
                            tooltip: 'Seguimiento',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                showSeguimiento(rec, 'local', 0);
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                return rec.get('action11')
                            },
                            tooltip: 'Planificar FWA',
                            handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                                    planificarServicio(rec);
                                    store.load();
                            }
                        },
                        {
                            getClass: function (v, meta, rec) {
                                return rec.get('action12')
                            },
                            tooltip: 'Ver Tareas del cliente',
                            handler: function (grid, rowIndex, colIndex) 
                            {
                                var rec = store.getAt(rowIndex);
                                verTareasClientes(rec.get('login2'));
                                
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                if (!rec.get('pedidos'))
                                {
                                    this.items[13].tooltip = '';
                                }
                                else
                                {
                                    this.items[13].tooltip = 'Pedidos';
                                    return rec.get('action14');
                                }
                            },
                            tooltip: 'Pedidos',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                showPedidos(rec, 'local', 0);
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                return rec.raw.action15
                            },
                            tooltip: 'Validador de Excedente de Material',
                            handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            validadorExcedenteMaterial(rec);
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                return rec.raw.action16
                            },

                            tooltip: 'Consulta Archivos de Evidencia para Excedente de Materiales',
                            handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            verDocumento(rec);
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                return rec.raw.action17
                            },
                            handler: function(grid, rowIndex, colIndex) {
                            var rec         = store.getAt(rowIndex);
                            var estado      = rec.get('estado');
                            var tipoGestion = 'gestionar';
                            if (estado=='PrePlanificada')
                            {
                                tipoGestion = 'planificar';
                            }
                            showProgramarInspecciones(rec.get('id_factibilidad'), rec.get('login2'), rec.get('cliente'),tipoGestion,estado);
                            },
                            tooltip: 'Gestionar Inspecciones'
                        },
                        {
                            getClass: function(v, meta, rec) {
                                var permiso = $("#verDocumentosSolicitudInspeccion");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                if (!boolPermiso) {
                                    rec.raw.action18 = "icon-invisible";
                                }
                                return rec.raw.action18
                            },
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                var permiso = $("#verDocumentosSolicitudInspeccion");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                if (!boolPermiso) {
                                    rec.data.action18 = "icon-invisible";
                                }
                                if (rec.get('action18') != "icon-invisible")
                                    showDocumentosSolicitudInspeccion(rec);
                                else
                                    Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta acción');


                            },
                            tooltip: 'Ver Documentos Solicitud Inspección'
                        },
                        {
                            getClass: function(v, meta, rec) {
                                var permiso = $("#rechazarSolicitudInspeccion");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                if (!boolPermiso) {
                                    rec.raw.action19 = "icon-invisible";
                                }
                                return rec.raw.action19
                            },
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                var permiso = $("#rechazarSolicitudInspeccion");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                if (!boolPermiso) {
                                    rec.data.action19 = "icon-invisible";
                                }
                                if (rec.get('action19') != "icon-invisible")
                                    showRechazarSolicitudInspeccion(rec);
                                else
                                    Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta acción');
                            },
                            tooltip: 'Rechazar Solicitud Inspección'
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


    }
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
                cmbTipoSolicitud,
                {html: "&nbsp;", border: false, width: 150},
                {
                    xtype: 'combobox',
                    fieldLabel: 'Estado',
                    id: 'sltEstado',
                    value: 'Todos',
                    store: [
                        ['Todos', 'Todos'],
                        ['PrePlanificada', 'PrePlanificada'],
                        ['Planificada', 'Planificada'],
                        ['Replanificada', 'Replanificada'],
                        ['Rechazada', 'Rechazada'],
                        ['Detenido', 'Detenido'],
                        ['AsignadoTarea', 'AsignadoTarea'],
                        ['Asignada', 'Asignada']
                    ],
                    width: '525'
                },
                {html: "&nbsp;", border: false, width: 200},
                {html: "&nbsp;", border: false, width: 200},
                /*{
                 xtype: 'textfield',
                 id: 'txtCiudad',
                 fieldLabel: 'Ciudad',
                 value: '',
                 width: '325'
                 }*/
                cmbCiudades,
                {html: "&nbsp;", border: false, width: 200},

                cmbSector,

                {html: "&nbsp;", border: false, width: 200},
                {html: "&nbsp;", border: false, width: 200},
                {
                    xtype: 'textfield',
                    id: 'txtIdentificacion',
                    fieldLabel: 'Identificación',
                    value: '',
                    width: '325'
                },
                {html: "&nbsp;", border: false, width: 150},
                {
                    xtype: 'textfield',
                    id: 'txtVendedor',
                    fieldLabel: 'Vendedor',
                    value: '',
                    width: '325'
                },
                {html: "&nbsp;", border: false, width: 200},
                {html: "&nbsp;", border: false, width: 200},
                {
                    xtype: 'textfield',
                    id: 'txtNombres',
                    fieldLabel: 'Nombres',
                    value: '',
                    width: '375'
                },
                {html: "&nbsp;", border: false, width: 150},
                {
                    xtype: 'textfield',
                    id: 'txtApellidos',
                    fieldLabel: 'Apellidos',
                    value: '',
                    width: '325'
                },
                {html: "&nbsp;", border: false, width: 200},
                {html: "&nbsp;", border: false, width: 200},
                {
                    xtype: 'textfield',
                    id: 'txtLogin',
                    fieldLabel: 'Login',
                    value: '',
                    width: '325',
                    listeners: {
                        specialkey: function (field, event) {
                            if (event.getKey() == event.ENTER && field.inputEl.dom.value) {
                                buscar();
                            }
                        }
                    }
                },
                {html: "&nbsp;", border: false, width: 150},
                {
                    xtype: 'textfield',
                    id: 'txtDescripcionPunto',
                    fieldLabel: 'Descripcion Punto',
                    value: '',
                    width: '325'
                },

                //-------------------------------------

                {html: "&nbsp;", border: false, width: 200},
                {html: "&nbsp;", border: false, width: 200},
                //inicio
                cmbUltimaMilla,
                //------------------------------------
                {html: "&nbsp;", border: false, width: 200},
                cmbEstadoPunto,
                {html: "&nbsp;", border: false, width: 200}
            ],
        renderTo: 'filtro'
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
            store.getProxy().extraParams.tipoSolicitud = Ext.getCmp('filtro_tipo_solicitud').value;
            store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
            store.getProxy().extraParams.ciudad = Ext.getCmp('cmbCiudades').getValue().length > 0 ? Ext.getCmp('cmbCiudades').getValue().toString() : '';
            store.getProxy().extraParams.sector = Ext.getCmp('cmbSector').value;
            store.getProxy().extraParams.identificacion = Ext.getCmp('txtIdentificacion').value;
            store.getProxy().extraParams.vendedor = Ext.getCmp('txtVendedor').value;
            store.getProxy().extraParams.nombres = Ext.getCmp('txtNombres').value;
            store.getProxy().extraParams.apellidos = Ext.getCmp('txtApellidos').value;
            store.getProxy().extraParams.login = Ext.getCmp('txtLogin').value;
            store.getProxy().extraParams.descripcionPunto = Ext.getCmp('txtDescripcionPunto').value;
            store.getProxy().extraParams.ultimaMilla = Ext.getCmp('cmbUltimaMilla').value;
            store.getProxy().extraParams.estadoPunto = Ext.getCmp('cmbEstadoPunto').value;

            store.load();
        }
    }

    function limpiar() {
        Ext.getCmp('fechaDesdePlanif').setRawValue("");
        Ext.getCmp('fechaHastaPlanif').setRawValue("");
        Ext.getCmp('fechaDesdeIngOrd').setRawValue("");
        Ext.getCmp('fechaHastaIngOrd').setRawValue("");

        Ext.getCmp('fechaDesdePlanif').setValue("");
        Ext.getCmp('fechaHastaPlanif').setValue("");
        Ext.getCmp('fechaDesdeIngOrd').setValue("");
        Ext.getCmp('fechaHastaIngOrd').setValue("");

        Ext.getCmp('filtro_tipo_solicitud').value = "";
        Ext.getCmp('filtro_tipo_solicitud').setRawValue("");

        Ext.getCmp('sltEstado').value = "Todos";
        Ext.getCmp('sltEstado').setRawValue("Todos");

        Ext.getCmp('cmbCiudades').value = "";
        Ext.getCmp('cmbCiudades').setRawValue("");

        Ext.getCmp('cmbSector').value = "";
        Ext.getCmp('cmbSector').setRawValue("");

        Ext.getCmp('txtIdentificacion').value = "";
        Ext.getCmp('txtIdentificacion').setRawValue("");

        Ext.getCmp('txtVendedor').value = "";
        Ext.getCmp('txtVendedor').setRawValue("");

        Ext.getCmp('txtNombres').value = "";
        Ext.getCmp('txtNombres').setRawValue("");

        Ext.getCmp('txtApellidos').value = "";
        Ext.getCmp('txtApellidos').setRawValue("");

        Ext.getCmp('txtLogin').value = "";
        Ext.getCmp('txtLogin').setRawValue("");

        Ext.getCmp('txtDescripcionPunto').value = "";
        Ext.getCmp('txtDescripcionPunto').setRawValue("");

        Ext.getCmp('cmbUltimaMilla').value = "";
        Ext.getCmp('cmbUltimaMilla').setRawValue("");

        Ext.getCmp('cmbEstadoPunto').value = "Todos";
        Ext.getCmp('cmbEstadoPunto').setRawValue("Todos");

        store.getProxy().extraParams.fechaDesdePlanif = Ext.getCmp('fechaDesdePlanif').value;
        store.getProxy().extraParams.fechaHastaPlanif = Ext.getCmp('fechaHastaPlanif').value;
        store.getProxy().extraParams.fechaDesdeIngOrd = Ext.getCmp('fechaDesdeIngOrd').value;
        store.getProxy().extraParams.fechaHastaIngOrd = Ext.getCmp('fechaHastaIngOrd').value;
        store.getProxy().extraParams.tipoSolicitud = Ext.getCmp('filtro_tipo_solicitud').value;
        store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
        store.getProxy().extraParams.ciudad = Ext.getCmp('cmbCiudades').getValue().length > 0 ? Ext.getCmp('cmbCiudades').getValue().toString() : '';
        store.getProxy().extraParams.sector = Ext.getCmp('cmbSector').value;
        store.getProxy().extraParams.identificacion = Ext.getCmp('txtIdentificacion').value;
        store.getProxy().extraParams.vendedor = Ext.getCmp('txtVendedor').value;
        store.getProxy().extraParams.nombres = Ext.getCmp('txtNombres').value;
        store.getProxy().extraParams.apellidos = Ext.getCmp('txtApellidos').value;
        store.getProxy().extraParams.login = Ext.getCmp('txtLogin').value;
        store.getProxy().extraParams.descripcionPunto = Ext.getCmp('txtDescripcionPunto').value;
        store.getProxy().extraParams.descripcionPunto = Ext.getCmp('cmbUltimaMilla').value;
        store.getProxy().extraParams.sector = Ext.getCmp('cmbSector').value;
        store.getProxy().extraParams.estadoPunto = Ext.getCmp('cmbEstadoPunto').value;

        store.load();
    }

    function exportarExcel() {
        var url = "exportarGrid";
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
            url = url + "?fechaDesdePlanif=" + ((Ext.getCmp('fechaDesdePlanif').getRawValue()) ? Ext.getCmp('fechaDesdePlanif').getRawValue() : '');
            url = url + "&fechaHastaPlanif=" + ((Ext.getCmp('fechaHastaPlanif').getRawValue()) ? Ext.getCmp('fechaHastaPlanif').getRawValue() : '');
            url = url + "&fechaDesdeIngOrd=" + ((Ext.getCmp('fechaDesdeIngOrd').getRawValue()) ? Ext.getCmp('fechaDesdeIngOrd').getRawValue() : '');
            url = url + "&fechaHastaIngOrd=" + ((Ext.getCmp('fechaHastaIngOrd').getRawValue()) ? Ext.getCmp('fechaHastaIngOrd').getRawValue() : '');
            url = url + "&login=" + Ext.getCmp('txtLogin').value;
            url = url + "&descripcionPunto=" + Ext.getCmp('txtDescripcionPunto').value;
            url = url + "&vendedor=" + Ext.getCmp('txtVendedor').value;
            url = url + "&ciudad=" + (Ext.getCmp('cmbCiudades').getValue().length > 0 ? Ext.getCmp('cmbCiudades').getValue().toString() : '');
            url = url + "&sector=" + ((Ext.getCmp('cmbSector').getRawValue()) ? Ext.getCmp('cmbSector').getRawValue() : '');
            url = url + "&ultimaMilla=" + ((Ext.getCmp('cmbUltimaMilla').getRawValue()) ? Ext.getCmp('cmbUltimaMilla').getRawValue() : '');
            url = url + "&tipoSolicitud=" + ((Ext.getCmp('filtro_tipo_solicitud').value) ? Ext.getCmp('filtro_tipo_solicitud').value : '');
            url = url + "&empresaCod=" + ($('#codigoEmpresa').val() ? $('#codigoEmpresa').val() : '');
            url = url + "&estado=" + Ext.getCmp('sltEstado').value;
            url = url + "&identificacion=" + Ext.getCmp('txtIdentificacion').value;
            url = url + "&nombres=" + Ext.getCmp('txtNombres').value;
            url = url + "&apellidos=" + Ext.getCmp('txtApellidos').value;
            url = url + "&estadoPunto=" + Ext.getCmp('cmbEstadoPunto').value;
            window.open(url);
        }
    }

});
