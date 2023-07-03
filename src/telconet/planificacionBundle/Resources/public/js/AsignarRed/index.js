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
            //anchor : '65%',
            //layout: 'anchor'
    });
    DTFechaHastaPlanif = new Ext.form.DateField({
        id: 'fechaHastaPlanif',
        fieldLabel: 'Hasta',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 325,
        editable: false
            //anchor : '65%',
            //layout: 'anchor'
    });

    // **************** EMPLEADOS ******************

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
                
                {name: 'InfoMigracionSDWAN', mapping: 'InfoMigracionSDWAN'},
                {name: 'ultimaMilla', mapping: 'ultimaMilla'},
                {name: 'radio', mapping: 'radio'},
                {name: 'idPop', mapping: 'idPop'},
                {name: 'pop', mapping: 'pop'},
                {name: 'intElemento', mapping: 'intElemento'},
                {name: 'caja', mapping: 'caja'},
                {name: 'idCaja', mapping: 'idCaja'},
                {name: 'splitter', mapping: 'splitter'},
                {name: 'idSplitter', mapping: 'idSplitter'},
                {name: 'dslam', mapping: 'dslam'},
                {name: 'tipo_orden', mapping: 'tipo_orden'},
                {name: 'mismosRecursos', mapping: 'mismosRecursos'},
                {name: 'diferenteTecnologia', mapping: 'diferenteTecnologia'},
                {name: 'esRecontratacion', mapping: 'esRecontratacion'},
                {name: 'elementoId', mapping: 'elementoId'},
                {name: 'id_factibilidad', mapping: 'id_factibilidad'},
                {name: 'id_servicio', mapping: 'id_servicio'},
                {name: 'cantidad', mapping: 'cantidad'},
                {name: 'tieneIp', mapping: 'tieneIp'},
                {name: 'cantidadIp', mapping: 'cantidadIp'},
                {name: 'nombreTecnico', mapping: 'nombreTecnico'},
                {name: 'id_servicio_trasladado', mapping: 'id_servicio_trasladado'},
                {name: 'id_punto', mapping: 'id_punto'},
                {name: 'tercerizadora', mapping: 'tercerizadora'},
                {name: 'cliente', mapping: 'cliente'},
                {name: 'vendedor', mapping: 'vendedor'},
                {name: 'login2', mapping: 'login2'},
                {name: 'producto', mapping: 'producto'},
                {name: 'esPlan', mapping: 'esPlan'},
                {name: 'idPlan', mapping: 'idPlan'},
                {name: 'coordenadas', mapping: 'coordenadas'},
                {name: 'direccion', mapping: 'direccion'},
                {name: 'ciudad', mapping: 'ciudad'},
                {name: 'jurisdiccion', mapping: 'jurisdiccion'},
                {name: 'nombreSector', mapping: 'nombreSector'},
                {name: 'fechaPlanificacionReal', mapping: 'fechaPlanificacionReal'},
                {name: 'fePlanificada', mapping: 'fePlanificada'},
                {name: 'HoraIniPlanificada', mapping: 'HoraIniPlanificada'},
                {name: 'HoraFinPlanificada', mapping: 'HoraFinPlanificada'},
                {name: 'rutaCroquis', mapping: 'rutaCroquis'},
                {name: 'latitud', mapping: 'latitud'},
                {name: 'longitud', mapping: 'longitud'},
                {name: 'estado', mapping: 'estado'},
                {name: 'action1', mapping: 'action1'},
                {name: 'action2', mapping: 'action2'},
                {name: 'action3', mapping: 'action3'},
                {name: 'action4', mapping: 'action4'},
                {name: 'interfaceSplitter', mapping: 'interfaceSplitter'},
                {name: 'idInterfaceConector', mapping: 'idInterfaceConector'},
                {name: 'strTipoRed', mapping: 'strTipoRed'},
                {name: 'booleanTipoRedGpon', mapping: 'booleanTipoRedGpon'},
                {name: 'strVrfCamaraGpon', mapping: 'strVrfCamaraGpon'},
                {name: 'strVlanCamaraGpon', mapping: 'strVlanCamaraGpon'},
                {name: 'strVrfAdminGpon', mapping: 'strVrfAdminGpon'},
                {name: 'strVlanAdminGpon', mapping: 'strVlanAdminGpon'},
                {name: 'booleanWifiSafeCity', mapping: 'booleanWifiSafeCity'},
                {name: 'booleanCamVpnSafeCity', mapping: 'booleanCamVpnSafeCity', defaultValue: false},
                {name: 'capacidad1', mapping: 'capacidad1'},
                {name: 'capacidad2', mapping: 'capacidad2'},
                {name: 'descripcionSolicitud', mapping: 'descripcionSolicitud'},
                {name: 'action5', mapping: 'action5'},
                {name: 'intElementoInterface', mapping: 'intElementoInterface'},
                {name: 'marcaOlt', mapping: 'marcaOlt'},
                {name: 'intCantidadIpsReservadas', mapping: 'intCantidadIpsReservadas'},
                {name: 'id_persona_empresa_rol', mapping: 'id_persona_empresa_rol'},
                {name: 'tipo_enlace', mapping: 'tipo_enlace'},
                {name: 'um', mapping: 'um'},
                {name: 'esPseudoPe', mapping: 'esPseudoPe'},
                {name: 'esAdminstradoPor', mapping: 'esAdminstradoPor'},
                {name: 'strDescripcion', mapping: 'strDescripcion'},
                {name: 'strTipoIp', mapping: 'strTipoIp'},
                {name: 'strTipoDeRed', mapping: 'strTipoDeRed'},
                {name: 'flujoZeroTouch', mapping: 'flujoZeroTouch'},
                {name: 'booleanClearChannelPaP', mapping: 'booleanClearChannelPaP'},
                {name: 'tipoRedServicio' , mapping: 'tipoRedServicio'}
            ]
    });

    var pluginExpanded = true;

    //****************  DOCKED ITEMS - BOTONES PARTE SUPERIOR ************************
    var permiso = $("#ROLE_139-111");
    var boolPermiso1 = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

    var permiso = $("#ROLE_139-112");
    var boolPermiso2 = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

    var asignarGlobalBtn = "";
    var asignarIndividualBtn = "";
    sm = "";
    if (boolPermiso1 && boolPermiso2)
    {
        sm = Ext.create('Ext.selection.CheckboxModel', {
            checkOnly: true
        })
    }
    if (boolPermiso1)
    {
        asignarGlobalBtn = Ext.create('Ext.button.Button', {
            iconCls: 'icon_delete',
            text: 'Asignar',
            itemId: 'asignar',
            scope: this,
            handler: function() {
                asignarResponsable('local', '0');
            }
        });
    }
    if (boolPermiso2)
    {
        asignarIndividualBtn = Ext.create('Ext.button.Button', {
            iconCls: 'icon_delete',
            text: 'Asignacion Individual',
            itemId: 'asignacion_individual',
            scope: this,
            handler: function() {
                showAsignacionIndividual('local', '0', false);
            }
        });
    }

    var toolbar = Ext.create('Ext.toolbar.Toolbar', {
        dock: 'top',
        align: '->',
        items: ['->', asignarGlobalBtn, asignarIndividualBtn]
    });


    grid = Ext.create('Ext.grid.Panel', {
        width: 1230,
        height: 500,
        store: store,
        loadMask: true,
        frame: false,
        columns: [
            {
                id: 'id_persona_empresa_rol',
                header: 'id_persona_empresa_rol',
                dataIndex: 'id_persona_empresa_rol',
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
                id: 'radio',
                header: 'radio',
                dataIndex: 'radio',
                hidden: true,
                hideable: false
            },
            {
                id: 'idPop',
                header: 'idPop',
                dataIndex: 'idPop',
                hidden: true,
                hideable: false
            },
            {
                id: 'pop',
                header: 'pop',
                dataIndex: 'pop',
                hidden: true,
                hideable: false
            },
            {
                id: 'dslam',
                header: 'dslam',
                dataIndex: 'dslam',
                hidden: true,
                hideable: false
            },
            {
                id: 'elementoId',
                header: 'elementoId',
                dataIndex: 'elementoId',
                hidden: true,
                hideable: false
            },
            {
                id: 'intElemento',
                header: 'intElemento',
                dataIndex: 'intElemento',
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
                id: 'splitter',
                header: 'splitter',
                dataIndex: 'splitter',
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
                id: 'tieneIp',
                header: 'tieneIp',
                dataIndex: 'tieneIp',
                hidden: true,
                hideable: false
            },
            {
                id: 'intCantidadIpsReservadas',
                header: 'intCantidadIpsReservadas',
                dataIndex: 'intCantidadIpsReservadas',
                hidden: true,
                hideable: false
            },
            {
                id: 'cantidadIp',
                header: 'cantidadIp',
                dataIndex: 'cantidadIp',
                hidden: true,
                hideable: false
            },
            {
                id: 'nombreTecnico',
                header: 'nombreTecnico',
                dataIndex: 'nombreTecnico',
                hidden: true,
                hideable: false
            },
            {
                id: 'cantidad',
                header: 'cantidad',
                dataIndex: 'cantidad',
                hidden: true,
                hideable: false
            },
            {
                id: 'id_servicio_trasladado',
                header: 'idServicioTrasladado',
                dataIndex: 'id_servicio_trasladado',
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
                id: 'tipo_orden',
                header: 'tipo_orden',
                dataIndex: 'tipo_orden',
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
                id: 'cliente',
                header: 'Cliente',
                dataIndex: 'cliente',
                width: 150,
                sortable: true
            },
            {
                id: 'vendedor',
                header: 'Vendedor',
                dataIndex: 'vendedor',
                width: 110,
                sortable: true
            },
            {
                id: 'login2',
                header: 'Login',
                dataIndex: 'login2',
                width: 120,
                sortable: true
            },
            {
                id: 'producto',
                header: 'Servicio',
                dataIndex: 'producto',
                width: 140,
                sortable: true
            },
            {
                id: 'um',
                header: 'UM',
                dataIndex: 'um',
                width: 50,
                sortable: true
            },
            {
                id: 'jurisdiccion',
                header: 'Jurisdiccion',
                dataIndex: 'jurisdiccion',
                width: 95,
                sortable: true
            },
            {
                id: 'coordenadas',
                header: 'Coordenadas',
                dataIndex: 'coordenadas',
                width: 120,
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
                id: 'descripcionSolicitud',
                header: 'Tipo Solicitud',
                dataIndex: 'descripcionSolicitud',
                width: 200,
                sortable: true
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 100,
                items: [
                    {
                        getClass: function(v, meta, rec) {
                           
                            this.items[0].tooltip = 'Asignar Recursos De Red';

                            return rec.get('action3')
                        },
                        tooltip: 'Asignar Recursos De Red',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);

                            if (rec.data.descripcionSolicitud == 'SOLICITUD MIGRACION')
                            {
                                rec.data.action3 = "icon-invisible";
                            }

                            if (rec.data.flujoZeroTouch == 'S')
                            {
                                rec.data.action3 = "icon-invisible";
                            }

                            if (rec.get('action3') != "icon-invisible")
                            {
                                if(prefijoEmpresa === 'TN')
                                {
                                    if(rec.get('nombreTecnico') === 'TUNELIP')
                                    {
                                        showRecursoRedTunelIp(rec);
                                    }
                                    else if((rec.get('nombreTecnico') === 'INTERNET' || rec.get('nombreTecnico') === 'INTERNET SDWAN') &&
                                        (rec.get('ultimaMilla') !== 'FTTx'))
                                    {   
                                        if(rec.get('booleanClearChannelPaP'))
                                        {
                                            showRecursoRedClearChannel(rec);
                                        }
                                        else
                                        {
                                            showRecursoRedInternetDedicado(rec);
                                        }
                                    }
                                    else if(rec.get('nombreTecnico') === 'INTERNETDC' || rec.get('nombreTecnico') === 'INTERNET DC SDWAN')
                                    {
                                        showRecursosRedInternetDC(rec);
                                    }
                                    else if(rec.get('nombreTecnico') === 'DATOSDC' || rec.get('nombreTecnico') === 'DATOS DC SDWAN')
                                    {
                                        showRecursosRedDatosDC(rec);
                                    }
                                    else if( (rec.get('nombreTecnico') === 'SAFECITYDATOS' || rec.get('nombreTecnico') === 'SAFECITYWIFI'
                                              || rec.get('booleanCamVpnSafeCity'))
                                             && rec.get('ultimaMilla') === 'FTTx')
                                    {
                                        showRecursoRedServiciosSafeCity(rec);
                                    }
                                    else if(rec.get('nombreTecnico') === 'L3MPLS' || rec.get('nombreTecnico') === 'L3MPLS SDWAN')
                                    {
                                        if(rec.get('esPseudoPe') === 'S' && rec.get('esAdminstradoPor') === 'CLIENTE')
                                        {
                                            if(rec.get('ultimaMilla') === 'SATELITAL')
                                            {
                                                showRecursosRedSatelital(rec);
                                            }
                                            else
                                            {
                                                showRecursoRedLPseudoPe(rec);
                                            }
                                        } else
                                        {
                                            if (rec.get('ultimaMilla') === 'FTTx')
                                            {
                                                showRecursosRedL3mplsFttx(rec);
                                            } else
                                            {
                                                if(typeof rec.get('InfoMigracionSDWAN') !== "undefined"
                                                 && rec.get('InfoMigracionSDWAN').EsMigracionSDWAN === true
                                                 && rec.get('InfoMigracionSDWAN').EsMigracionSDWAN === true)
                                                {
                                                    showRecursoRedL3mplsMigracionSDWAN(rec);
                                                }
                                                else
                                                {
                                                    showRecursoRedL3mpls(rec);
                                                }
                                            }
                                        }
                                    }
                                    else if(rec.get('nombreTecnico') === 'DATOS FWA')
                                    {
                                        showRecursoRedFWA(rec);
                                    }
                                    else if(rec.get('nombreTecnico') === 'DATOS SAFECITY' && rec.get('ultimaMilla') === 'FTTx')
                                    {
                                        showRecursoDeRedInternetLite(rec);
                                    }

                                    if(rec.get('nombreTecnico') === 'INTMPLS' || ((rec.get('ultimaMilla') === 'FTTx') &&
                                        (rec.get('nombreTecnico') === 'INTERNET')))
                                    {
                                        showRecursoRedInternetMPLS(rec);
                                    }
                                    if(rec.get('nombreTecnico') === 'INTERNET WIFI')
                                    {
                                        showRecursoDeRedWifi(rec, rec.get('id_factibilidad'), "asignarResponsable");
                                    }
                                    if(rec.get('nombreTecnico') === 'CONCINTER')
                                    {
                                        showRecursoRedL3mpls(rec);
                                    }
                                    if(rec.get('nombreTecnico') === 'INTERNET SMALL BUSINESS' || rec.get('nombreTecnico') === 'IPSB'
                                        || rec.get('nombreTecnico') === 'TELCOHOME')
                                    {
                                        if(rec.get('ultimaMilla') === 'FTTx')
                                        {
                                            showRecursoDeRedInternetLite(rec);
                                        }
                                        else
                                        {
                                            Ext.MessageBox.show({
                                                title: 'Error',
                                                msg: 'No existe el flujo para el producto '+rec.get('producto')
                                                     +' con la última milla '+rec.get('ultimaMilla'),
                                                buttons: Ext.MessageBox.OK,
                                                icon: Ext.MessageBox.ERROR
                                            });
                                        }
                                    }
                                }
                                else if(prefijoEmpresa === 'TNP')
                                {
                                    if(rec.get('ultimaMilla') === 'Fibra Optica')
                                    {
                                        if(rec.get('esPlan') === 'si')
                                        {
                                            showRecursoDeRedInternetResidencial(rec);
                                        }
                                        else
                                        {
                                            showRecursoDeRedInternetResidencial(rec);
                                        }
                                    }
                                    if(rec.get('nombreTecnico') === 'INTERNET SMALL BUSINESS' || rec.get('nombreTecnico') === 'IPSB')
                                    {
                                        if(rec.get('ultimaMilla') === 'FTTx')
                                        {
                                            showRecursoDeRedInternetLite(rec);
                                        }
                                        else
                                        {
                                            Ext.MessageBox.show({
                                                title: 'Error',
                                                msg: 'No existe el flujo para el producto '+rec.get('producto')
                                                     +' con la última milla '+rec.get('ultimaMilla'),
                                                buttons: Ext.MessageBox.OK,
                                                icon: Ext.MessageBox.ERROR
                                            });
                                        }
                                    }
                                }
                                else
                                {
                                    showRecursoDeRed(rec, rec.get('id_factibilidad'), "asignarResponsable");
                                }
                            }
                            else
                            {
                                if (rec.data.flujoZeroTouch == 'S')
                                {
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: 'No tiene permisos para realizar esta acción. Luego que el técnico de OPU realice las pruebas ZeroTouch puede intentar nuevamente.',
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
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
                                
                        }
                    },
                     {
                        getClass: function(v, meta, rec) {
                            var permiso = $("#ROLE_135-94");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                rec.data.action5 = "icon-invisible";
                            }
                            if (rec.data.descripcionSolicitud != 'SOLICITUD MIGRACION')
                            {
                                rec.data.action5 = "icon-invisible";
                            }

                            if (rec.get('action5') == "icon-invisible")
                                this.items[0].tooltip = '';
                            else
                                this.items[0].tooltip = 'Migrar Recursos De Red';

                            return rec.get('action5')
                        },
                        tooltip: 'Migrar Recursos De Red',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);

                            var permiso = $("#ROLE_135-95");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                rec.data.action5 = "icon-invisible";
                            }
                            
                            if (rec.data.descripcionSolicitud != 'SOLICITUD MIGRACION')
                            {
                                rec.data.action5 = "icon-invisible";
                            }

                            if (rec.get('action5') != "icon-invisible")
                            {
                                if(prefijoEmpresa === 'TN')
                                {
                                    showRecursoDeRedMigracionProdsTn(rec);
                                }
                                else
                                {
                                    showRecursoDeRedMigracion(rec, rec.get('id_factibilidad'), "asignarResponsable");
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
                            var permiso = $("#ROLE_135-94");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                rec.data.action4 = "icon-invisible";
                            }
                            
                            if (rec.data.descripcionSolicitud == 'SOLICITUD MIGRACION')
                            {
                                rec.data.action4 = "icon-invisible";
                            }

                            if (rec.get('action4') == "icon-invisible")
                                this.items[0].tooltip = '';
                            else
                                this.items[0].tooltip = 'Trasladar Recursos de Red';

                            return rec.get('action4')
                        },
                        tooltip: 'Trasladar Recursos De Red',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);

                            var permiso = $("#ROLE_135-95");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                rec.data.action4 = "icon-invisible";
                            }
                            
                            if (rec.data.descripcionSolicitud == 'SOLICITUD MIGRACION')
                            {
                                rec.data.action4 = "icon-invisible";
                            }

                            if (rec.get('action4') != "icon-invisible")
                            {      
                                showTrasladarRecursosDeRed(rec, rec.get('id_factibilidad'), "asignarResponsable");           
                            }
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
    
    if(prefijoEmpresa == "TN")
    {
        grid.headerCt.insert(
                                    27,
                                    {
                                        text: 'T. Enlace',
                                        width: 60,
                                        dataIndex: 'tipo_enlace',
                                        sortable: true
                                    }
                                );
    }
    if(prefijoEmpresa == "MD")
    {
        grid.headerCt.insert(
                                    31,
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
        collapsible: false,
        collapsed: false,
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
                {html: "Fecha Planificacion:", border: false, width: 325},
                {html: "&nbsp;", border: false, width: 150},
                {html: "&nbsp;", border: false, width: 325},
                {html: "&nbsp;", border: false, width: 200},
                {html: "&nbsp;", border: false, width: 200},
                DTFechaDesdePlanif,
                {html: "&nbsp;", border: false, width: 150},
                DTFechaHastaPlanif,
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
                {
						xtype: 'combobox',
						id: 'filtro_tipo_solicitud',
						name: 'filtro_tipo_solicitud',
						fieldLabel: 'Tipo Solicitud',
						typeAhead: true,
						triggerAction: 'all',
						displayField:'tipo_solicitud',
						valueField: 'id_tipo_solicitud',
						selectOnTab: true,
						store: [
						    ['SOLICITUD PLANIFICACION','SOLICITUD PLANIFICACION'],
						    ['SOLICITUD INFO TECNICA','SOLICITUD INFO TECNICA'],
                            ['SOLICITUD MIGRACION','SOLICITUD MIGRACION'],
                            ['SOLICITUD CAMBIO ULTIMA MILLA','SOLICITUD CAMBIO ULTIMA MILLA']
						], 
						lazyRender: true,
						queryMode: "local",
						listClass: 'x-combo-list-small',
						width: 325,
					},
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

    if (!boolError)
    {
        store.getProxy().extraParams.fechaDesdePlanif = Ext.getCmp('fechaDesdePlanif').value;
        store.getProxy().extraParams.fechaHastaPlanif = Ext.getCmp('fechaHastaPlanif').value;
        store.getProxy().extraParams.login2 = Ext.getCmp('txtLogin').value;
        store.getProxy().extraParams.descripcionPunto = Ext.getCmp('txtDescripcionPunto').value;
        store.getProxy().extraParams.vendedor = Ext.getCmp('txtVendedor').value;
        store.getProxy().extraParams.ciudad = Ext.getCmp('txtCiudad').value;
        store.getProxy().extraParams.numOrdenServicio = Ext.getCmp('txtNumOrdenServicio').value;
        store.getProxy().extraParams.tipoSolicitud = Ext.getCmp('filtro_tipo_solicitud').value;
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

    Ext.getCmp('txtNumOrdenServicio').value = "";
    Ext.getCmp('txtNumOrdenServicio').setRawValue("");

    store.getProxy().extraParams.fechaDesdePlanif = Ext.getCmp('fechaDesdePlanif').value;
    store.getProxy().extraParams.fechaHastaPlanif = Ext.getCmp('fechaHastaPlanif').value;
    store.getProxy().extraParams.login2 = Ext.getCmp('txtLogin').value;
    store.getProxy().extraParams.descripcionPunto = Ext.getCmp('txtDescripcionPunto').value;
    store.getProxy().extraParams.vendedor = Ext.getCmp('txtVendedor').value;
    store.getProxy().extraParams.ciudad = Ext.getCmp('txtCiudad').value;
    store.getProxy().extraParams.numOrdenServicio = Ext.getCmp('txtNumOrdenServicio').value;
    store.load();
}
