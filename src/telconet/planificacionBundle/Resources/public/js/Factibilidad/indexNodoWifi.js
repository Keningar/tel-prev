/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var connFactibilidad = new Ext.data.Connection({
    listeners: {
        'beforerequest': {
            fn: function(con, opt) {
                Ext.MessageBox.show({
                    msg: 'Grabando los datos, Por favor espere!!',
                    progressText: 'Saving...',
                    width: 300,
                    wait: true,
                    waitConfig: {interval: 200}
                });
            },
            scope: this
        },
        'requestcomplete': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
            },
            scope: this
        },
        'requestexception': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
            },
            scope: this
        }
    }
});

Ext.onReady(function()
{
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

    var storeCantones = new Ext.data.Store({
        total    : 'total',
        pageSize : 100,
        autoLoad : true,
        proxy: {
            type : 'ajax',
            url  : url_getCantones,
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

    store = new Ext.data.Store({
        total    : 'total',
        pageSize : 14,
        async    : false,
        autoLoad : true,
        proxy: {
            type    : 'ajax',
            method  : 'post',
            url     : url_ajaxGrid,
            timeout : 600000,
            reader: {
                type          : 'json',
                totalProperty : 'total',
                root          : 'encontrados'
            },
            extraParams: {
                fechaDesdePlanif : '',
                fechaHastaPlanif : '',
                txtNodo          : '',
                txtEstado        : 'PreFactibilidad',
                txtLogin         : ''
            }
        },
        fields:
            [
                {name: 'login', mapping: 'login'},
                {name: 'idServicio', mapping: 'idServicio'},
                {name: 'idSolicitud', mapping: 'idSolicitud'},
                {name: 'direccion', mapping: 'direccion'},
                {name: 'idSector', mapping: 'idSector'},
                {name: 'idCanton', mapping: 'idCanton'},
                {name: 'latitud', mapping: 'latitud'},
                {name: 'longitud', mapping: 'longitud'},
                {name: 'estado', mapping: 'estado'},
                {name: 'nombreCanton', mapping: 'nombreCanton'},
                {name: 'fechaCreacion', mapping: 'fechaCreacion'},
                {name: 'elemento', mapping: 'nombreElemento'},
                {name: 'idElemento', mapping: 'idElemento'},
                {name: 'idParroquia', mapping: 'idParroquia'},
                {name: 'usrCreacion', mapping: 'usrCreacion'},
                {name: 'loginL3', mapping: 'loginL3'},
                {name: 'idPunto', mapping: 'idPunto'},
                {name: 'nombrePunto', mapping: 'nombrePunto'},
                {name: 'tipoOrden', mapping: 'tipoOrden'},
                {name: 'nombreProducto', mapping: 'nombreProducto'},
                {name: 'nombreSector', mapping: 'nombreSector'},
                {name: 'idPersonaEmpresaRol', mapping: 'idPersonaEmpresaRol'},
                {name: 'modeloElementoWifi', mapping: 'modeloElementoWifi'},
                {name: 'tipoEsquema', mapping: 'tipoEsquema'},
                {name: 'boolInstalacionSim', mapping: 'boolInstalacionSim'}
            ]
    });

    var pluginExpanded = true;

    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    });

    grid = Ext.create('Ext.grid.Panel', {
        width    : 1230,
        height   : 500,
        store    : store,
        loadMask : true,
        frame    : false,
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
            },
        columns: [
            {
                id: 'idSolicitud',
                header: 'idSolicitud',
                dataIndex: 'idSolicitud',
                hidden: true,
                hideable: false
            },
            {
                id: 'login',
                header: 'Login',
                dataIndex: 'login',
                width: 160,
                sortable: true
            },
            {
                id: 'esquema',
                header: 'Esquema',
                dataIndex: 'tipoEsquema',
                width: 70,
                sortable: true,
                align: 'center'
            },
            {
                id: 'elemento',
                header: 'Nombre Elemento',
                dataIndex: 'elemento',
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
                id: 'loginL3',
                header: 'Login L3MPLS',
                dataIndex: 'loginL3',
                width: 120,
                sortable: true
            },
            {
                id: 'fechaCreacion',
                header: 'Fecha Creación',
                dataIndex: 'fechaCreacion',
                width: 120,
                sortable: true
            },
            {
                id: 'nombreCanton',
                header: 'Cantón',
                dataIndex: 'nombreCanton',
                width: 80,
                sortable: true
            },
            {
                id: 'estado',
                header: 'Estado',
                dataIndex: 'estado',
                width: 120,
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
                width: 200,
                items: [
                    {
                        getClass: function(v, meta, rec) {
                            return 'button-grid-Gmaps';
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
                            var permiso = $("#ROLE_341-3918");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (boolPermiso) {
                                if (rec.get('estado') == 'PreFactibilidad' || rec.get('estado') == 'FactibilidadEnProceso')
                                {
                                    return 'button-grid-BigDelete';
                                }
                                else
                                {
                                    return "icon-invisible";
                                }
                            }
                        },
                        tooltip: 'Rechazar',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            rechazarFactibilidadNodoCliente(rec);
                        }
                    },
                    {
                        getClass: function(v, meta, rec) {
                            var permiso = $("#ROLE_341-3897");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (boolPermiso) {
                                if (rec.get('estado') == 'PreFactibilidad' && rec.get('idElemento') == null)
                                {
                                    return 'button-grid-solicitarPlanificacion';
                                }
                                else
                                {
                                    return "icon-invisible";
                                }
                            }
                        },
                        tooltip: 'Ingresar Nuevo Nodo Wifi',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            nuevoNodoWifi(rec);
                        }
                    },
                    {
                        getClass: function(v, meta, rec) {
                            var permiso = $("#ROLE_341-3917");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (boolPermiso) {
                                if ((rec.get('estado') == 'PreFactibilidad' ||
                                    rec.get('estado') == 'RelacionElemento') &&
                                    !rec.get('boolInstalacionSim'))
                                {
                                    return 'button-grid-Check';
                                }
                                else
                                {
                                    return "icon-invisible";
                                }
                            }
                        },
                        tooltip: "Asignar Factibilidad Nodo Wifi Existente",
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            showIngresoFactibilidadWifi(rec);
                        }
                    },
                    {
                        getClass: function(v, meta, rec) {
                            var permiso = $("#ROLE_341-4417");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (boolPermiso) {
                                if (rec.get('estado') == 'Factible')
                                {
                                    return 'button-grid-edit';
                                }
                                else
                                {
                                    return "icon-invisible";
                                }
                            }
                        },
                        tooltip: 'Editar Factibilidad',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            showIngresoFactibilidadWifi(rec);
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
                {html: "Fecha Factibilidad:", border: false, width: 325},
                {html: "&nbsp;", border: false, width: 150},
                {html: "", border: false, width: 325},
                {html: "&nbsp;", border: false, width: 200},
                {html: "&nbsp;", border: false, width: 200},
                DTFechaDesdePlanif,
                {html: "&nbsp;", border: false, width: 150},
                DTFechaHastaPlanif,
                {html: "&nbsp;", border: false, width: 200},
                {html: "&nbsp;", border: false, width: 200},
                {
                    xtype: 'textfield',
                    id: 'txtNodo',
                    fieldLabel: 'Nombre Nodo',
                    value: '',
                    width: '325'
                },
                {html: "&nbsp;", border: false, width: 150},
                {
                    xtype: 'combobox',
                    id: 'txtEstado',
                    fieldLabel: 'Estado',
                    store: ['PreFactibilidad', 'FactibilidadEnProceso', 'Rechazada','PendientePunto', 'Factible','Finalizada','Eliminada'],
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
                    id: 'sltCanton',
                    fieldLabel: 'Canton',
                    xtype: 'combobox',
                    typeAhead: true,
                    displayField: 'nombre_canton',
                    valueField: 'id_canton',
                    loadingText: 'Buscando ...',
                    store: storeCantones,
                    listClass: 'x-combo-list-small',
                    queryMode: 'local',
                    width: '325'
                },

                {html: "&nbsp;", border: false, width: 200}

            ],
        renderTo: 'filtro'
    });

});


/* ******************************************* */
/*  FUNCIONES  */
/* ******************************************* */

function buscar()
{
    if (Ext.isEmpty(Ext.getCmp('fechaDesdePlanif').value) &&
        Ext.isEmpty(Ext.getCmp('fechaHastaPlanif').value) &&
        Ext.isEmpty(Ext.getCmp('txtNodo').value) &&
        Ext.isEmpty(Ext.getCmp('txtEstado').value) &&
        Ext.isEmpty(Ext.getCmp('txtLogin').value) &&
        Ext.isEmpty(Ext.getCmp('sltCanton').value))
    {
        Ext.Msg.show({
                title: 'Error',
                msg: 'Por favor ingresar al menos un filtro para realizar la búsqueda.',
                buttons: Ext.Msg.OK,
                animEl: 'elId',
                icon: Ext.MessageBox.ERROR
            });

        return;
    }

    if ( (!Ext.isEmpty(Ext.getCmp('fechaDesdePlanif').value) && Ext.isEmpty(Ext.getCmp('fechaHastaPlanif').value)) ||
         (Ext.isEmpty(Ext.getCmp('fechaDesdePlanif').value)  && !Ext.isEmpty(Ext.getCmp('fechaHastaPlanif').value)))
    {
        Ext.Msg.show({
                title: 'Error',
                msg: 'Por favor completar el rango de fechas.',
                buttons: Ext.Msg.OK,
                animEl: 'elId',
                icon: Ext.MessageBox.ERROR
        });

        return;
    }

    if ((Ext.getCmp('fechaDesdePlanif').getValue() != null) && (Ext.getCmp('fechaHastaPlanif').getValue() != null))
    {
        if (Ext.getCmp('fechaDesdePlanif').getValue() > Ext.getCmp('fechaHastaPlanif').getValue())
        {
            Ext.Msg.show({
                title: 'Error',
                msg: 'La Fecha desde debe ser menor a la fecha hasta.',
                buttons: Ext.Msg.OK,
                animEl: 'elId',
                icon: Ext.MessageBox.ERROR
            });

            return;
        }

        if(getDiferenciaTiempo(Ext.getCmp('fechaDesdePlanif').getValue(),Ext.getCmp('fechaHastaPlanif').getValue()) > 31)
        {
            Ext.Msg.show({
                title: 'Error',
                msg: 'El rango de fechas elegidas no puede superar un máximo de 30 días',
                buttons: Ext.Msg.OK,
                animEl: 'elId',
                icon: Ext.MessageBox.ERROR
            });

            return;
        }
    }

    store.getProxy().extraParams.fechaDesdePlanif = Ext.getCmp('fechaDesdePlanif').value;
    store.getProxy().extraParams.fechaHastaPlanif = Ext.getCmp('fechaHastaPlanif').value;
    store.getProxy().extraParams.txtNodo          = Ext.getCmp('txtNodo').value;
    store.getProxy().extraParams.txtEstado        = Ext.getCmp('txtEstado').value;
    store.getProxy().extraParams.txtLogin         = Ext.getCmp('txtLogin').value;
    store.getProxy().extraParams.idCanton         = Ext.getCmp('sltCanton').value;
    store.load();
}

function getDiferenciaTiempo(fechaIni, fechaFin)
{
    var fechaIniS  = getDate(fechaIni).split("-");
    var fechaFinS  = getDate(fechaFin).split("-");
    var dateInicio = (String)(fechaIniS[0] + "-" + fechaIniS[1] + "-" + fechaIniS[2]);
    var dateFin    = (String)(fechaFinS[0] + "-" + fechaFinS[1] + "-" + fechaFinS[2]);
    var diferencia = new Date(dateFin) - new Date(dateInicio);
    return Math.ceil((((diferencia / 1000) / 60) / 60) / 24);
}

function getDate(date)
{
    var day = date.getDate().toString();

    var month = (date.getMonth()+1).toString();

    if(day.length === 1)
    {
        day = '0'+day;
    }

    if(month.length === 1)
    {
        month = '0'+month;
    }

    return date.getFullYear() +"-"+ month + '-' + day;
}

function showIngresoFactibilidad(rec)
{

    storeElementos = new Ext.data.Store({
        total: 'total',
        autoDestroy: true,
        autoLoad: false,
        listeners: {
            load: function() {

            }
        },
        proxy: {
            type: 'ajax',
            url: url_getElementosSinRelacion,
            timeout: 120000,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            listeners: {
                exception: function(proxy, response, options) {
                    Ext.MessageBox.alert('Error', "Favor ingrese el nombre");
                }
            },
            actionMethods: {
                create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
            },
            extraParams: {
                nombre: this.nombreElemento,
                tipoElemento: 'ROUTER',
                idCanton: rec.get('idCanton')
            }
        },
        fields:
            [
                {name: 'idElemento', mapping: 'idElemento'},
                {name: 'nombreElemento', mapping: 'nombreElemento'},
                {name: 'nombreModeloElemento', mapping: 'nombreModeloElemento'}
            ]
    });

    formPanelFactibilidad = Ext.create('Ext.form.Panel', {
        buttonAlign: 'center',
        BodyPadding: 10,
        bodyStyle: "background: white; padding:10px; border: 0px none;",
        frame: true,
        items: [
            {
                xtype: 'fieldset',
                title: 'Seleccione el router',
                defaultType: 'textfield',
                style: "font-weight:bold; margin-bottom: 15px;",
                defaults: {
                    width: '350px'
                },
                items: [
                    {
                        selectOnFocus: true,
                        loadingText: 'Buscando ...',
                        hideTrigger: false,
                        xtype: 'combobox',
                        name: 'cmbElemento',
                        id: 'cmbElemento',
                        typeAhead: true,
                        triggerAction: 'all',
                        displayField: 'nombreElemento',
                        queryMode: "remote",
                        valueField: 'idElemento',
                        selectOnTab: true,
                        store: storeElementos,
                        width: 350,
                        lazyRender: true,
                        listClass: 'x-combo-list-small',
                        forceSelection: true,
                        emptyText: 'Escriba el nombre...',
                        minChars: 3
                    }
                ]
            }
        ],
        buttons: [
            {
                text: 'Guardar',
                handler: function() {
                    var txtElemento = Ext.getCmp('cmbElemento').value;

                    var boolError = false;
                    var mensajeError = "";
                    if (!txtElemento || txtElemento == "" || txtElemento == 0)
                    {
                        boolError = true;
                        mensajeError += "Seleccionar el elemento.\n";
                    }

                    if (!boolError)
                    {
                        connFactibilidad.request({
                            url: url_relacionElemento,
                            timeout: 120000,
                            method: 'post',
                            params: {
                                idSolicitud: rec.get('idSolicitud'),
                                idElemento: txtElemento
                            },
                            success: function(response) {
                                var text = response.responseText;
                                if (text == "OK")
                                {
                                    Ext.Msg.alert('Mensaje', 'Transacción Exitosa.', function(btn) {
                                        if (btn == 'ok') {
                                            store.load();
                                        }
                                    });
                                    cierraVentanaPreFactibilidad();
                                }
                                else {
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: text,
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                    cierraVentanaPreFactibilidad();
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
                    cierraVentanaPreFactibilidad();
                }
            }
        ]
    });

    winPreFactibilidad = Ext.widget('window', {
        title: 'Relacion Elemento',
        layout: 'fit',
        resizable: false,
        modal: true,
        closable: false,
        items: [formPanelFactibilidad]
    });


    winPreFactibilidad.show();
}

function rechazarFactibilidadNodoCliente(rec)
{
    storeMotivos = new Ext.data.Store({
        total: 'total',
        pageSize: 10000,
        proxy: {
            type: 'ajax',
            url: url_getMotivosRechazo,
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

    formPanelRechazarOrden_Factibilidad = Ext.create('Ext.form.Panel', {
        buttonAlign: 'center',
        BodyPadding: 10,
        bodyStyle: "background: white; padding:10px; border: 0px none;",
        frame: true,
        items: [
            {
                xtype: 'fieldset',
                title: 'Ingreso de información',
                defaultType: 'textfield',
                style: "font-weight:bold; margin-bottom: 15px;",
                defaults: {
                    width: '350px'
                },
                items: [
                    {
                        xtype: 'combobox',
                        id: 'cmbMotivo',
                        fieldLabel: 'Motivo',
                        typeAhead: true,
                        triggerAction: 'all',
                        displayField: 'nombre_motivo',
                        valueField: 'id_motivo',
                        selectOnTab: true,
                        store: storeMotivos,
                        lazyRender: true,
                        queryMode: "local",
                        listClass: 'x-combo-list-small',
                        width: 300
                    }, 
                    {
                        xtype: 'textarea',
                        fieldLabel: 'Observacion',
                        name: 'info_observacion',
                        id: 'info_observacion',
                        allowBlank: false,
                        width: 300
                    }
                ]
            }
        ],
        buttons: [
            {
                text: 'Rechazar',
                handler: function() {
                    var txtObservacion = Ext.getCmp('info_observacion').value;
                    var cmbMotivo = Ext.getCmp('cmbMotivo').value;
                    var id_factibilidad = rec.get("id_factibilidad");

                    var boolError = false;
                    var mensajeError = "";
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

                    if (!boolError)
                    {
                        connFactibilidad.request({
                            method: 'POST',
                            params: {
                                id_motivo: cmbMotivo,
                                observacion: txtObservacion,
                                idSolicitud: rec.get('idSolicitud'),
                                idElemento: rec.get('idElemento')},
                            url: url_rechazar,
                            success: function(response) {
                                var text = response.responseText;
                                cierraVentanaRechazarOrden_Factibilidad();
                                if (text == "OK")
                                {
                                    Ext.Msg.alert('Mensaje', 'Se rechazó correctamente.', function(btn) {
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
                    cierraVentanaRechazarOrden_Factibilidad();
                }
            }
        ]
    });

    winRechazarOrden_Factibilidad = Ext.widget('window', {
        title: 'Rechazo de factibilidad',
        layout: 'fit',
        resizable: false,
        modal: true,
        closable: false,
        items: [formPanelRechazarOrden_Factibilidad]
    });


    winRechazarOrden_Factibilidad.show();
}

function nuevoNodoWifi(rec)
{
    var iniHtmlCamposRequeridos = '<p style="text-align: right; color:red; font-weight: bold; border: 0 !important;">* Campos requeridos</p>';
    CamposRequeridos = Ext.create('Ext.Component', {
        html: iniHtmlCamposRequeridos,
        padding: 1,
        layout: 'anchor',
        style: {color: 'black', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
    });


    var storeTipos = new Ext.data.Store({
        pageSize: 100,
        proxy: {
            type: 'ajax',
            timeout: 400000,
            url: url_getModelosPorTipoElemento,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                tipoElemento: 'NODO WIFI'
            }

        },
        fields:
            [
                {name: 'id', mapping: 'id'},
                {name: 'descripcion', mapping: 'descripcion'}
            ]
    });

    /*Se agrega filtro para que en el combo-box aparezca unicamente la opcion que corresponda al
    esquema del servicio, y en caso de existir un servicio anterior sin esquema le permitirá elegir
    las dos opciones.*/
    storeTipos.filter([
        {filterFn: function(record) {
            if (parseInt(rec.get('tipoEsquema')) == 2){
                return record.get('descripcion') == 'CLIENTE';
            } else if (parseInt(rec.get('tipoEsquema')) == 1)
            {
                return record.get('descripcion') == 'BACKBONE';
            } else
            {
                return record.get('descripcion');
            }
            }
        }
    ]);


    DTFechaProgramacion = new Ext.form.DateField({
        id: 'fechaProgramacion',
        name: 'fechaProgramacion',
        fieldLabel: '* Fecha Programación',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        editable: false,
        minValue: new Date(),
        value: new Date(),
        width: 300

    });

    formPanelFactibilidad = Ext.create('Ext.form.Panel', {
        buttonAlign: 'center',
        BodyPadding: 10,
        bodyStyle: "background: white; padding:10px; border: 0px none;",
        frame: true,
        items: [
            CamposRequeridos,
            {
                xtype: 'fieldset',
                title: 'Ingreso de Información',
                defaultType: 'textfield',
                style: "font-weight:bold; margin-bottom: 15px;",
                defaults: {
                    width: '350px'
                },
                items: [
                    
                    {
                        xtype: 'textfield',
                        fieldLabel: '* Nombre Elemento',
                        name: 'nombreElemento',
                        id: 'nombreElemento',
                        value: 'CPE-WIFI-',
                        emptyText: 'CPE-WIFI-login',
                        allowBlank: false,
                        width: 300
                    },
                    {
                        xtype: 'combobox',
                        id: 'cmbTipo',
                        fieldLabel: '* Tipo',
                        typeAhead: true,
                        triggerAction: 'all',
                        displayField: 'descripcion',
                        valueField: 'id',
                        selectOnTab: true,
                        store: storeTipos,
                        listClass: 'x-combo-list-small',
                        width: 300
                    },
                    {
                        xtype: 'textarea',
                        fieldLabel: '* Descripción',
                        name: 'descripcionElemento',
                        id: 'descripcionElemento',
                        allowBlank: false,
                        width: 300
                    }
                ]
            }
        ],
        buttons: [
            {
                text: 'Guardar',
                handler: function() {
                    var fechaProgramacion = Ext.getCmp('fechaProgramacion').value;
                    var txtNombreElemento = Ext.getCmp('nombreElemento').value;
                    var txtDescripcion = Ext.getCmp('descripcionElemento').value;
                    var cmbTipo = Ext.getCmp('cmbTipo').value;

                    var boolError = false;
                    var mensajeError = "";
                    if (!fechaProgramacion || fechaProgramacion == "" || fechaProgramacion == 0)
                    {
                        boolError = true;
                        mensajeError += "La fecha de creacion no fue seleccionada, por favor seleccione.\n";
                    }

                    if (!txtNombreElemento || txtNombreElemento == "" || txtNombreElemento == 0
                    || !txtNombreElemento.split('-')[2])
                    {
                        boolError = true;
                        mensajeError += " Favor ingrese el nombre. \n";
                    }

                    if (!cmbTipo || cmbTipo == "" || cmbTipo == 0)
                    {
                        boolError = true;
                        mensajeError += " Favor seleccione el tipo.\n";
                    }

                    if (!txtDescripcion || txtDescripcion == "" || txtDescripcion == 0)
                    {
                        boolError = true;
                        mensajeError += " Favor ingrese la descripcion.\n";
                    }


                    if (!boolError)
                    {
                        connFactibilidad.request({
                            url: url_fechaFactibilidad,
                            timeout: 120000,
                            method: 'post',
                            params: {
                                idSolicitud: rec.get('idSolicitud'),
                                fechaProgramacion: fechaProgramacion,
                                nombreElemento: txtNombreElemento,
                                tipoElemento: cmbTipo,
                                descripcion: txtDescripcion,
                                idParroquia: rec.get('idParroquia'),
                                longitud: rec.get('longitud'),
                                latitud: rec.get('latitud'),
                                direccion: rec.get('direccion'),
                                idPunto: rec.get('idPunto'),
                                idServicio: rec.get('idServicio'),
                                cliente: rec.get('nombrePunto'),
                                producto: rec.get('nombreProducto'),
                                feCreacion: rec.get('fechaCreacion'),
                                estado: rec.get('estado'),
                                tipoEsquema: rec.get('tipoEsquema')
                            },
                            success: function(response) {
                                cierraVentanaPreFactibilidad();
                                if (IsJsonString(response.responseText)) 
                                {
                                    var res =   JSON.parse(response.responseText);
                                    if (rec.get('nombreProducto') == 'INTERNET WIFI')
                                    {
                                        if (res.status == "OK")
                                        {
                                            Ext.Msg.alert('Mensaje', `Transacción Exitosa, se generó la tarea: #${res.id} para la creación de un Nodo Wifi`, function(btn) {
                                                if (btn == 'ok') {
                                                    store.load();
                                                }
                                            });
                                        }
                                        else 
                                        {
                                            Ext.MessageBox.show({
                                                title: 'Error',
                                                msg: res.mensaje,
                                                buttons: Ext.MessageBox.OK,
                                                icon: Ext.MessageBox.ERROR
                                            });
                                        }
                                    }
                                } else 
                                {
                                    cierraVentanaPreFactibilidad();
                                    var text = response.responseText;
                                    if (text == "OK") 
                                    {
                                        Ext.Msg.alert('Mensaje', `Transacción Exitosa`, function (btn) {
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
                                }
                            },
                            failure: function(result) {
                                cierraVentanaPreFactibilidad();
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
                    cierraVentanaPreFactibilidad();
                }
            }
        ]
    });

    winPreFactibilidad = Ext.widget('window', {
        title: 'Nuevo Nodo Wifi',
        layout: 'fit',
        resizable: false,
        modal: true,
        closable: false,
        items: [formPanelFactibilidad]
    });


    winPreFactibilidad.show();
}

function showIngresoFactibilidadWifi(rec)
{
    var idElementoWifi = rec.get("idElemento");
    var nombreElementoWifi = rec.get("nombreElemento");

    var arrayParametros = [];
    var interfaceOdf;
    arrayParametros['strPrefijoEmpresa'] = strPrefijoEmpresa;
    arrayParametros['strDescripcionTipoRol'] = 'Contacto';
    arrayParametros['strEstadoTipoRol'] = 'Activo';
    arrayParametros['strDescripcionRol'] = 'Contacto Tecnico';
    arrayParametros['strEstadoRol'] = 'Activo';
    arrayParametros['strEstadoIER'] = 'Activo';
    arrayParametros['strEstadoIPER'] = 'Activo';
    arrayParametros['strEstadoIPC'] = 'Activo';
    arrayParametros['intIdPersonaEmpresaRol'] = rec.get("idPersonaEmpresaRol");
    arrayParametros['intIdPunto'] = rec.get("idPunto");
    arrayParametros['idStore'] = 'storeContactoIngFac';
    arrayParametros['strJoinPunto'] = '';
    strDefaultType = "hiddenfield";
    strXtype = "hiddenfield";
    strNombres = '';
    strApellidos = '';
    strErrorMetraje = '';
    boolCheckObraCivil = false;
    boolCheckObservacionRegeneracion = false;
    storeRolContacto = '';
    storeRolContactoPunto = '';
    if ("TN" === strPrefijoEmpresa) {
        var arrayPersonaContacto = [];
        storeRolContacto = obtienInfoPersonaContacto(arrayParametros);
        arrayParametros['intIdPersonaEmpresaRol'] = '';
        arrayParametros['idStore'] = 'storeContactoPuntoIngFac';
        arrayParametros['strJoinPunto'] = 'PUNTO';
        storeRolContactoPunto = obtienInfoPersonaContacto(arrayParametros);
        strDefaultType = "textfield";
        strXtype = "fieldset";
    }
    var boolEsEdificio = true;
    var boolDependeDeEdificio = true;
    var boolNombreEdificio = true;
    if ("S" === rec.get("strEsEdificio")) {
        boolEsEdificio = false;
    }
    if ("S" === rec.get("strDependeDeEdificio")) {
        boolDependeDeEdificio = false;
    }
    if (false === boolDependeDeEdificio || false === boolEsEdificio) {
        boolNombreEdificio = false;
    }
    var strNombreTipoElemento = "SPLITTER";
    var strNombreElementoPadre = "Olt";
    if ("TN" === strPrefijoEmpresa) {
        strNombreTipoElemento = "CASSETTE";
        strNombreElementoPadre = "Switch";
        if ("true" == rec.get("strObraCivil"))
        {
            boolCheckObraCivil = true;
        }
        if ("true" == rec.get("strPermisosRegeneracion"))
        {
            boolCheckObservacionRegeneracion = true;
        }
    }

    winIngresoFactibilidad = "";
    formPanelInresoFactibilidad = "";
    if (!winIngresoFactibilidad)
    {
        //******** html campos requeridos...
        var iniHtmlCamposRequeridos = '<p style="text-align: right; color:#ff0000; font-weight: bold; border: 0 !important;">* Campos requeridos</p>';
        CamposRequeridos = Ext.create('Ext.Component', {
            html: iniHtmlCamposRequeridos,
            padding: 1,
            layout: 'anchor',
            style: {color: 'red', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
        });

    storeElementosFactibles = new Ext.data.Store
        ({
            total: 'total',
            pageSize: 200,
            proxy:
                {
                    timeout: 60000,
                    type: 'ajax',
                    method: 'post',
                    url: getElementosFactibles,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        },
                    extraParams:
                        {
                            idServicio : rec.get('idServicio')

                        }
                },
            fields:
                [
                    {name: 'idElementoFactible', mapping: 'idElementoFactible'},
                    {name: 'nombreElementoFactible', mapping: 'nombreElementoFactible'}
                ],
            listeners:
                {
                    load: function(store, records)
                    {
                        var mensaje = store.proxy.reader.jsonData.mensaje.trim();
                        let strNotificar = '';
                        const objDept = {
                            radio:  "<b><br/>NOTIFICAR A RADIO</b>",
                            l2:     "<b><br/>NOTIFICAR A IPCCL2</b>"
                        };
                        
                        if (rec.get('tipoEsquema'))
                        {
                            strNotificar = rec.get('tipoEsquema') == 1 ? objDept.radio : objDept.l2;

                            if (mensaje.startsWith('NO EXISTE DISPONIBILIDAD')|| 
                            mensaje.startsWith('NO EXISTEN PUERTOS') || 
                            mensaje.startsWith('EL ELEMENTO NO TIENE'))
                            {
                                mensaje = mensaje + strNotificar;
                            }
                        }

                        if (mensaje != 'OK')
                        {
                            Ext.MessageBox.alert('Notificación', mensaje);
                        }
                    }
                },
        });


    var storeInterfacesElemento = new Ext.data.Store({
        pageSize: 100,
        proxy: {
            type: 'ajax',
            timeout: 400000,
            url: getInterfacesPorElemento,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'idInterface', mapping: 'idInterface'},
                {name: 'nombreInterface', mapping: 'nombreInterface'}
            ]
    });

    storeElementosNodoWifi = new Ext.data.Store({
        total: 'total',
        autoDestroy: true,
        autoLoad: false,
        listeners: {
            load: function() {

            }
        },
        proxy: {
            type: 'ajax',
            url: url_getElementosWifi,
            timeout: 120000,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            listeners: {
                exception: function(proxy, response, options) {
                    Ext.MessageBox.alert('Error', "Favor ingrese el nombre");
                }
            },
            actionMethods: {
                create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
            },
            extraParams: {
                nombre: this.nombreElemento,
                tipoElemento: 'NODO WIFI',
                idCanton: rec.get('idCanton')
            }
        },
        fields:
            [
                {name: 'idElemento', mapping: 'idElemento'},
                {name: 'nombreElemento', mapping: 'nombreElemento'},
                {name: 'modelo', mapping: 'modelo'}
            ],
    });

        DTFechaProgramacion = new Ext.form.DateField({
            id: 'fechaProgramacion',
            name: 'fechaProgramacion',
            fieldLabel: '* Fecha',
            labelAlign: 'left',
            xtype: 'datefield',
            format: 'Y-m-d',
            editable: false,
            minValue: new Date(),
            value: new Date(),
            labelStyle: "color:red;"
        });

        storeElementos = new Ext.data.Store({
            total: 'total',
            listeners: {
                load: function() {
                }
            },
            proxy: {
                type: 'ajax',
                url: urlComboCajas,
                timeout: 120000,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                listeners: {
                    exception: function(proxy, response, options) {
                        Ext.MessageBox.alert('Error', "Favor ingrese un nombre de caja");
                    }
                },
                actionMethods: {
                    create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                },
                extraParams: {
                    nombre: this.strNombreElemento
                }
            },
            fields:
                [
                    {name: 'intIdElemento', mapping: 'intIdElemento'},
                    {name: 'strNombreElemento', mapping: 'strNombreElemento'}
                ]
        });

        storeElementosByPadre = new Ext.data.Store({
            total: 'total',
            pageSize: 10000,
            proxy: {
                type: 'ajax',
                url: urlComboElementosByPadre,
                timeout: 120000,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                actionMethods: {
                    create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                },
                extraParams: {
                    popId: '',
                    elemento: strNombreTipoElemento
                }
            },
            fields:
                [
                    {name: 'idElemento', mapping: 'idElemento'},
                    {name: 'nombreElemento', mapping: 'nombreElemento'}
                ]
        });

        /*modelClaseTipoMedio*/
        Ext.define('modelClaseTipoMedio', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'idClaseTipoMedio', type: 'int'},
                {name: 'nombreClaseTipoMedio', type: 'string'}
            ]
        });

        //Store de storeClaseTipoMedio
        storeClaseTipoMedio = Ext.create('Ext.data.Store', {
            autoLoad: true,
            model: "modelClaseTipoMedio",
            proxy: {
                type: 'ajax',
                url: urlGetClaseTipoMedio,
                reader: {
                    type: 'json',
                    root: 'encontrados'
                },
                extraParams: {
                    tipoMedioId: rec.get('txtIdUltimaMilla'),
                    estado: 'Activo'
                }
            }
        });

        Ext.define('modelInterfaceElemento', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'intIdInterfaceElemento', type: 'int'},
                {name: 'strNombreInterfaceElemento', type: 'string'}
            ]
        });

        storePuertos = Ext.create('Ext.data.Store', {
            model: "modelInterfaceElemento",
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url: urlInterfacesByElemento,
                reader: {
                    type: 'json',
                    root: 'encontrados'
                },
                extraParams: {
                    intIdElemento: '',
                    strEstado: ''
                }
            }
        });

        var storeHilosDisponibles = new Ext.data.Store({
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url: getHilosDisponibles,
                extraParams: {
                    idElemento: '',
                    estadoInterface: 'connected',
                    estadoInterfaceNotConect: 'not connect',
                    estadoInterfaceReserved: 'not connect',
                    strBuscaHilosServicios: 'BUSCA_HILOS_SERVICIOS',
                    intIdPunto: rec.get('idPunto')
                },
                reader: {
                    type: 'json',
                    root: 'encontrados'
                }
            },
            fields:
                [
                    {name: 'idInterfaceElemento', mapping: 'idInterfaceElemento'},
                    {name: 'idInterfaceElementoOut', mapping: 'idInterfaceElementoOut'},
                    {name: 'colorHilo', mapping: 'colorHilo'},
                    {name: 'numeroHilo', mapping: 'numeroHilo'},
                    {name: 'numeroColorHilo', mapping: 'numeroColorHilo'}
                ]
        });

        cbxPuertos = Ext.create('Ext.form.ComboBox', {
            id: 'cbxPuertos',
            name: 'cbxPuertos',
            store: storeHilosDisponibles,
            queryMode: 'local',
            fieldLabel: 'Hilos Disponibles',
            displayField: 'numeroColorHilo',
            valueField: 'idInterfaceElementoOut',
            editable: false,
            hidden: true,
            listeners:
                {
                    select: function(combo)
                    {
                        var objeto = combo.valueModels[0].raw;
                        Ext.Ajax.request({
                            url: ajaxJsonPuertoOdfByHilo,
                            method: 'post',
                            async: false,
                            params: {idInterfaceElementoConector: objeto.idInterfaceElemento},
                            success: function(response)
                            {
                                var json = Ext.JSON.decode(response.responseText);
                                interfaceOdf = json.idInterfaceElemento;
                                intIdInterfaceElemento = json.idInterfaceElemento;
                                var arrayParamDistribucionTN = [];
                                arrayParamDistribucionTN['strUrlInfoCaja'] = urlInfoCaja;
                                arrayParamDistribucionTN['intIdElementoContenedor'] = Ext.getCmp('cbxIdElementoCaja').value;
                                arrayParamDistribucionTN['strIdElementoDistribucion'] = objeto.idInterfaceElemento;
                                arrayParamDistribucionTN['strTipoBusqueda'] = 'INTERFACE';
                                arrayParamDistribucionTN['strNombreElementoPadre'] = strNombreElementoPadre.toUpperCase();
                                arrayParamDistribucionTN['strNombreCaja'] = Ext.getCmp('cbxIdElementoCaja').getRawValue();
                                arrayParamDistribucionTN['strNombreElemento'] = Ext.getCmp('cbxElementoPNivel').getRawValue();
                                arrayParamDistribucionTN['strNombreTipoElemento'] = strNombreTipoElemento;
                                arrayParamDistribucionTN['strNombreTipoMedio'] = rec.get("strNombreTipoMedio");
                                arrayParamDistribucionTN['strPrefijoEmpresa'] = strPrefijoEmpresa;
                                arrayParamDistribucionTN['strUrlDisponibilidadElemento'] = urlDisponibilidadElemento;
                                arrayParamDistribucionTN['strUrlCalculaMetraje'] = urlCalculaMetraje;
                                arrayParamDistribucionTN['intIdPunto'] = rec.get("idPunto");
                                arrayParamDistribucionTN['winIngresoFactibilidad'] = winIngresoFactibilidad;

                            },
                            failure: function(result)
                            {
                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                            }
                        });


                    }
                }
        });

        formPanelIngresoFactibilidad = Ext.create('Ext.form.Panel', {
            buttonAlign: 'center',
            BodyPadding: 10,
            bodyStyle: "background: white; padding: 5px; border: 0px none;",
            frame: true,
            items: [
                CamposRequeridos,
                {
                    xtype: 'fieldset',
                    title: '',
                    layout: {
                        tdAttrs: {style: 'padding: 5px;'},
                        type: 'table',
                        columns: 2,
                        pack: 'center'
                    },
                    items: [
                        {
                            xtype: 'fieldset',
                            title: 'Datos del Cliente',
                            defaultType: 'textfield',
                            style: "font-weight:bold; margin-bottom: 5px;",
                            layout: 'anchor',
                            defaults: {
                                width: '350px'
                            },
                            items: [
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Cliente',
                                    name: 'info_cliente',
                                    id: 'info_cliente',
                                    value: rec.get("nombrePunto"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Login',
                                    name: 'info_login',
                                    id: 'info_login',
                                    value: rec.get("login"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Ciudad',
                                    name: 'info_ciudad',
                                    id: 'info_ciudad',
                                    value: rec.get("nombreCanton"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Direccion',
                                    name: 'info_direccion',
                                    id: 'info_direccion',
                                    value: rec.get("direccion"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Sector',
                                    name: 'info_nombreSector',
                                    id: 'info_nombreSector',
                                    value: rec.get("nombreSector"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Latitud',
                                    name: 'strLatitud',
                                    id: 'intIdLatitud',
                                    value: rec.get("latitud"),
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Longitud',
                                    name: 'strLongitud',
                                    id: 'intIdLongitud',
                                    value: rec.get("longitud"),
                                    readOnly: true
                                },
                                {
                                    xtype: 'checkboxfield',
                                    fieldLabel: 'Es Edificio',
                                    checked: true,
                                    readOnly: true,
                                    hidden: boolEsEdificio
                                },
                                {
                                    xtype: 'checkboxfield',
                                    fieldLabel: 'Depende de Edificio',
                                    readOnly: true,
                                    checked: true,
                                    hidden: boolDependeDeEdificio
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Nombre Edificio',
                                    name: 'strNombreEdificio',
                                    id: 'intIdNombreEdificio',
                                    value: rec.get("strNombreEdificio"),
                                    allowBlank: false,
                                    readOnly: true,
                                    hidden: boolNombreEdificio
                                }
                            ]
                        },
                        {
                            xtype: 'fieldset',
                            title: '',
                            layout: {
                                tdAttrs: {style: 'padding: 5px;'},
                                type: 'table',
                                columns: 1,
                                pack: 'center'
                            },
                            items: [
                                {
                                    xtype: 'fieldset',
                                    title: 'Datos del Servicio',
                                    defaultType: 'textfield',
                                    style: "font-weight:bold; margin-bottom: 5px;",
                                    defaults: {
                                        width: '350px'
                                    },
                                    items: [
                                        {
                                            xtype: 'textfield',
                                            fieldLabel: 'Servicio',
                                            name: 'info_servicio',
                                            id: 'info_servicio',
                                            value: rec.get("nombreProducto"),
                                            allowBlank: false,
                                            readOnly: true
                                        },
                                        {
                                            xtype: 'textfield',
                                            fieldLabel: 'Tipo Orden',
                                            name: 'tipo_orden_servicio',
                                            id: 'tipo_orden_servicio',
                                            value: rec.get("tipoOrden"),
                                            allowBlank: false,
                                            readOnly: true
                                        }
                                    ]
                                },
                                {
                                    xtype: strXtype,
                                    title: 'Datos Contacto Tecnico',
                                    style: "font-weight:bold; margin-bottom: 5px;",
                                    layout: 'anchor',
                                    defaults: {
                                        width: '350px'
                                    },
                                    items: [
                                        {
                                            xtype: 'tabpanel',
                                            activeTab: 0,
                                            autoScroll: false,
                                            layoutOnTabChange: true,
                                            items: [
                                                {
                                                    xtype: 'grid',
                                                    title: 'Contacto nivel cliente',
                                                    store: storeRolContacto,
                                                    id: 'gridRolContacto',
                                                    height: 80,
                                                    columns: [
                                                        {
                                                            header: "Nombres",
                                                            dataIndex: 'strNombres',
                                                            width: 174
                                                        },
                                                        {
                                                            header: "Apellidos",
                                                            dataIndex: 'strApellidos',
                                                            width: 174
                                                        }
                                                    ]
                                                },
                                                {
                                                    xtype: 'grid',
                                                    title: 'Contacto nivel punto',
                                                    store: storeRolContactoPunto,
                                                    id: 'gridRolContactoPunto',
                                                    height: 80,
                                                    columns: [
                                                        {
                                                            header: "Nombres",
                                                            dataIndex: 'strNombres',
                                                            width: 174
                                                        },
                                                        {
                                                            header: "Apellidos",
                                                            dataIndex: 'strApellidos',
                                                            width: 174
                                                        }
                                                    ]
                                                }
                                            ]
                                        }
                                    ]
                                }
                            ]
                        }
                    ]
                },
                {
                    xtype: 'fieldset',
                    title: 'Datos de Factibilidad',
                    defaultType: 'textfield',
                    style: "font-weight:bold; margin-bottom: 5px;",
                    layout: {
                        tdAttrs: {style: 'padding: 5px;'},
                        type: 'table',
                        columns: 2,
                        pack: 'center'
                    },
                    items: [
                        {
                            xtype: 'fieldset',
                            style: "border:0",
                            items: [
                             
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Elemento',
                                    name: 'txtNameElementoPadre',
                                    id: 'txtIdNameElementoPadre',
                                    readOnly: true,
                                    hidden: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Tipo Elemento',
                                    name: 'txtNameTipoElemento',
                                    id: 'txtIdTipoElemento',
                                    readOnly: true,
                                    hidden: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Ultima Milla',
                                    name: 'txtNameUltimaMilla',
                                    id: 'txtIdUltimaMilla',
                                    readOnly: true,
                                    hidden: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Puerto',
                                    name: 'txtPuerto',
                                    id: 'txtIdPuerto',
                                    readOnly: true,
                                    hidden: true
                                },
                                {
                                    xtype: 'combobox',
                                    id: 'idCbxClaseFibra',
                                    fieldLabel: 'Clase Fibra',
                                    store: storeClaseTipoMedio,
                                    triggerAction: 'all',
                                    displayField: 'nombreClaseTipoMedio',
                                    valueField: 'idClaseTipoMedio',
                                    loadingText: 'Seleccione ...',
                                    listClass: 'x-combo-list-small',
                                    queryMode: 'local',
                                    hidden: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'MODELO NODO',
                                    name: 'txtModeloNodoWifi',
                                    id: 'txtModeloNodoWifi',
                                    readOnly: true,
                                    hidden: true
                                },  
                                {
                                    loadingText: 'Buscando ...',
                                    xtype: 'combobox',
                                    name: 'cmbNodoWifi',
                                    id: 'cmbNodoWifi',
                                    fieldLabel: '* NODO WIFI',
                                    labelStyle: "color:red;",
                                    displayField: 'nombreElemento',
                                    queryMode: "remote",
                                    valueField: 'idElemento',
                                    store: storeElementosNodoWifi,
                                    lazyRender: true,
                                    forceSelection: true,
                                    loadingText: 'Buscando...',
                                    width: 300,
                                    minChars: 3,

                                    listeners: {
                                        select: function(combo) {
                                            var objeto = combo.valueModels[0].raw;

                                            storeElementosFactibles.proxy.extraParams = {idElemento: combo.getValue(), idServicio : rec.get('idServicio')};
                                            storeElementosFactibles.load({params: {}});
                                            Ext.getCmp('cmbElementoFactible').setVisible(true);
                                            Ext.getCmp('txtModeloNodoWifi').setValue('');
                                            Ext.getCmp('txtModeloNodoWifi').setVisible(true);
                                            Ext.getCmp('txtModeloNodoWifi').setValue(objeto.nombreModeloElemento);
                                            Ext.getCmp('cbxIdElementoCaja').disable();
                                            
                                            Ext.getCmp('cbxIdElementoCaja').setValue('');
                                            Ext.getCmp('cbxElementoPNivel').setValue('');
                                            Ext.getCmp('txtIdfloatMetraje').setValue("");
                                            Ext.getCmp('cbxPuertos').setValue("");
                                            Ext.getCmp('txtIdPuerto').setValue("");

                                            Ext.getCmp('cbxIdElementoCaja').setVisible(false);
                                            Ext.getCmp('cbxElementoPNivel').setVisible(false);
                                            Ext.getCmp('txtIdfloatMetraje').setVisible(false);
                                            Ext.getCmp('cbxPuertos').setVisible(false);
                                            Ext.getCmp('txtIdPuerto').setVisible(false);                                            
                                            if(objeto.nombreModeloElemento == 'BACKBONE')
                                            {   
                                                Ext.getCmp('cbxIdElementoCaja').setVisible(true);
                                            }
                                        }
                                    }
                                },
                                {
                                    queryMode: 'local',
                                    xtype: 'combobox',
                                    id: 'cmbElementoFactible',
                                    name: 'cmbElementoFactible',
                                    fieldLabel: '* ELEMENTO',
                                    labelStyle: "color:red;",
                                    hidden: true,
                                    displayField: 'nombreElementoFactible',
                                    valueField: 'idElementoFactible',
                                    loadingText: 'Buscando...',
                                    store: storeElementosFactibles,
                                    width: 300,
                                    listeners: {
                                        select: function(combo) {
                                            storeInterfacesElemento.proxy.extraParams = {idElemento: combo.getValue() , estado : 'not connect'};
                                            storeInterfacesElemento.load({params: {}});
                                            Ext.getCmp('cbxIdElementoCaja').enable();
                                            Ext.getCmp('interfaceElementoNuevo').setVisible(true);
                                        }
                                    },

                                },
                                {
                                    queryMode: 'local',
                                    xtype: 'combobox',
                                    id: 'interfaceElementoNuevo',
                                    name: 'interfaceElementoNuevo',
                                    fieldLabel: '* PUERTO ELEMENTO',
                                    labelStyle: "color:red;",
                                    hidden: true,
                                    displayField: 'nombreInterface',
                                    valueField: 'idInterface',
                                    loadingText: 'Buscando...',
                                    store: storeInterfacesElemento,
                                    width: 300,
                                },                                
                                {
                                    xtype: 'combobox',
                                    id: 'cbxIdElementoCaja',
                                    name: 'cbxElementoCaja',
                                    fieldLabel: '* CAJA',
                                    typeAhead: true,
                                    triggerAction: 'all',
                                    displayField: 'strNombreElemento',
                                    queryMode: "remote",
                                    valueField: 'intIdElemento',
                                    selectOnTab: true,
                                    hidden: true,
                                    store: storeElementos,
                                    width: 470,
                                    lazyRender: true,
                                    listClass: 'x-combo-list-small',
                                    labelStyle: "color:red;",
                                    forceSelection: true,
                                    disabled: true,
                                    minChars: 3,
                                    listeners: {
                                        select: {fn: function(combo, value) {
                                                Ext.getCmp('cbxElementoPNivel').setVisible(true);
                                                Ext.getCmp('txtIdInterfacesDisponibles').setValue(0);

                                                Ext.getCmp('txtIdNameElementoPadre').setValue("");
                                                Ext.getCmp('txtIdMarcaElementoPadre').setValue("");
                                                Ext.getCmp('txtIdUltimaMilla').setValue("");
                                                Ext.getCmp('txtIdTipoElemento').setValue("");
                                                Ext.getCmp('txtIdModeloElemento').setValue("");
                                                Ext.getCmp('txtIdfloatMetraje').setValue("");
                                                Ext.getCmp('cbxPuertos').setValue("");
                                                Ext.getCmp('txtIdPuerto').setValue("");

                                                Ext.getCmp('txtIdNameElementoPadre').setVisible(false);
                                                Ext.getCmp('txtIdMarcaElementoPadre').setVisible(false);
                                                Ext.getCmp('txtIdUltimaMilla').setVisible(false);
                                                Ext.getCmp('txtIdTipoElemento').setVisible(false);
                                                Ext.getCmp('txtIdModeloElemento').setVisible(false);
                                                Ext.getCmp('txtIdfloatMetraje').setVisible(false);
                                                Ext.getCmp('cbxPuertos').setVisible(false);
                                                Ext.getCmp('txtIdPuerto').setVisible(false);

                                                storeElementosByPadre.proxy.extraParams = {
                                                    popId: combo.getValue(),
                                                    elemento: strNombreTipoElemento,
                                                    estado: 'Activo'
                                                };
                                                storeElementosByPadre.load({params: {}});
                                            }}
                                    }
                                },
                                {
                                    xtype: 'combobox',
                                    id: 'cbxElementoPNivel',
                                    name: 'cbxElementoPNivel',
                                    fieldLabel: '* ' + strNombreTipoElemento,
                                    hidden: true,
                                    typeAhead: true,
                                    width: 470,
                                    queryMode: "local",
                                    triggerAction: 'all',
                                    displayField: 'nombreElemento',
                                    valueField: 'idElemento',
                                    selectOnTab: true,
                                    store: storeElementosByPadre,
                                    lazyRender: true,
                                    listClass: 'x-combo-list-small',
                                    emptyText: 'Seleccione un ' + strNombreTipoElemento,
                                    labelStyle: "color:red;",
                                    editable: false,
                                    listeners: {
                                        select: {fn: function(combo, value) {

                                                Ext.getCmp('txtIdNameElementoPadre').setValue("");
                                                Ext.getCmp('txtIdMarcaElementoPadre').setValue("");
                                                Ext.getCmp('txtIdUltimaMilla').setValue("");
                                                Ext.getCmp('txtIdTipoElemento').setValue("");
                                                Ext.getCmp('txtIdModeloElemento').setValue("");
                                                Ext.getCmp('txtIdfloatMetraje').setValue("");
                                                Ext.getCmp('cbxPuertos').setValue("");
                                                Ext.getCmp('txtIdPuerto').setValue("");

                                                Ext.getCmp('txtIdNameElementoPadre').setVisible(false);
                                                Ext.getCmp('txtIdMarcaElementoPadre').setVisible(false);
                                                Ext.getCmp('txtIdUltimaMilla').setVisible(false);
                                                Ext.getCmp('txtIdTipoElemento').setVisible(false);
                                                Ext.getCmp('txtIdModeloElemento').setVisible(false);
                                                Ext.getCmp('txtIdfloatMetraje').setVisible(false);
                                                Ext.getCmp('cbxPuertos').setVisible(false);
                                                Ext.getCmp('txtIdPuerto').setVisible(false);

                                                var arrayParamInfoElemDist = [];
                                                arrayParamInfoElemDist['strPrefijoEmpresa'] = strPrefijoEmpresa;
                                                arrayParamInfoElemDist['strIdElementoDistribucion'] = combo.getValue();
                                                arrayParamInfoElemDist['strNombreCaja'] = Ext.getCmp('cbxIdElementoCaja').getRawValue();
                                                arrayParamInfoElemDist['intIdElementoContenedor'] = Ext.getCmp('cbxIdElementoCaja').value;
                                                arrayParamInfoElemDist['strUrlInfoCaja'] = urlInfoCaja;
                                                arrayParamInfoElemDist['strTipoBusqueda'] = 'ELEMENTO';
                                                arrayParamInfoElemDist['strNombreElementoPadre'] = strNombreElementoPadre;
                                                arrayParamInfoElemDist['strNombreElemento'] = combo.getRawValue();
                                                arrayParamInfoElemDist['strNombreTipoElemento'] = strNombreTipoElemento;
                                                arrayParamInfoElemDist['strNombreTipoMedio'] = rec.get("strNombreTipoMedio");
                                                arrayParamInfoElemDist['strUrlDisponibilidadElemento'] = urlDisponibilidadElemento;
                                                arrayParamInfoElemDist['strUrlCalculaMetraje'] = urlCalculaMetraje;
                                                arrayParamInfoElemDist['intIdPunto'] = rec.get("idPunto");
                                                arrayParamInfoElemDist['winIngresoFactibilidad'] = winIngresoFactibilidad;
                                                if ("TN" === strPrefijoEmpresa) {
                                                    arrayParamInfoElemDist['storeHilosDisponibles'] = storeHilosDisponibles;
                                                    var objResponseHiloMetraje = buscaHiloCalculaMetraje(arrayParamInfoElemDist);
                                                        Ext.getCmp('chbxIdObraCivil').setVisible(false);
                                                        Ext.getCmp('chbxIdObservacionRegeneracion').setVisible(false);
                                                    
                                                    if ("100" !== objResponseHiloMetraje.strStatus) {
                                                        strErrorMetraje = objResponseHiloMetraje.strMessageStatus;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Metraje',
                                    name: 'txtNamefloatMetraje',
                                    hidden: true,
                                    id: 'txtIdfloatMetraje',
                                    regex: /^(?:\d*\.\d{1,2}|\d+)$/,
                                    value: 0,
                                    readOnly: false
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Disponibilidad',
                                    name: 'txtInterfacesDisponibles',
                                    hidden: true,
                                    id: 'txtIdInterfacesDisponibles',
                                    maxLength: 3,
                                    value: 0,
                                    readOnly: true
                                },
                                cbxPuertos
                            ]
                        },
                        {
                            xtype: 'fieldset',
                            style: "border:0",
                            items: [
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Modelo Elemento',
                                    name: 'txtNameModeloElemento',
                                    id: 'txtIdModeloElemento',
                                    readOnly: true,
                                    hidden: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Marca Elemento',
                                    name: 'txtMarcaElementoPadre',
                                    id: 'txtIdMarcaElementoPadre',
                                    readOnly: true,
                                    hidden: true
                                },
                                {
                                    xtype: 'checkboxfield',
                                    fieldLabel: 'Obra Civil',
                                    name: 'chbxObraCivil',
                                    checked: boolCheckObraCivil,
                                    id: 'chbxIdObraCivil',
                                    hidden: true
                                },
                                {
                                    xtype: 'checkboxfield',
                                    fieldLabel: 'Requiere permisos regeneración',
                                    name: 'chbxObservacionRegeneracion',
                                    id: 'chbxIdObservacionRegeneracion',
                                    checked: boolCheckObservacionRegeneracion,
                                    hidden: true
                                },
                                {
                                    xtype: 'textarea',
                                    fieldLabel: 'Observacion',
                                    name: 'txtObservacionRegeneracion',
                                    id: 'txtIdObservacionRegeneracion',
                                    value: rec.get("strObservacionPermiRegeneracion"),
                                    hidden: true
                                }
                            ]
                        }
                    ]
                }
            ],
            buttons: [
                {
                    text: 'Guardar',
                    handler: function() {
                        var txtModelo = Ext.getCmp('txtModeloNodoWifi').value;                      
                        
                        
                        var intIdElementoCaja = Ext.getCmp('cbxIdElementoCaja').value;//caja
                        var intElementoPNivel = Ext.getCmp('cbxElementoPNivel').value;//casette
                        var intInterfaceElementoDistribucion = Ext.getCmp('cbxPuertos').value;//interfaceCasette
                        var intPuertosDisponibles = Ext.getCmp('txtIdInterfacesDisponibles').value;
                        var chbxObservacionRegeneracion = Ext.getCmp('chbxIdObservacionRegeneracion').value;
                        var strObservacionRegeneracion = Ext.getCmp('txtIdObservacionRegeneracion').value;
                        var floatMetraje = Ext.getCmp('txtIdfloatMetraje').value;
                        var idSolicitud = rec.get("idSolicitud");
                        var boolError = false;
                        var parametros;
                        var mensajeError = "";

                        var idNodoWifi = Ext.getCmp('cmbNodoWifi').value;
                        var idElementoWifi = Ext.getCmp('cmbElementoFactible').value;
                        var idInterfaceElementoWifi = Ext.getCmp('interfaceElementoNuevo').value;

                        if (!idNodoWifi || idNodoWifi == "" || idNodoWifi == 0)
                        {
                            boolError = true;
                            mensajeError += "Favor ingrese el nodo wifi \n";
                        }

                        if (!idElementoWifi || idElementoWifi == "" || idElementoWifi == 0)
                        {
                            boolError = true;
                            mensajeError += " Favor ingrese el elemento . \n";
                        }

                        if (!idInterfaceElementoWifi || idInterfaceElementoWifi == "" || idInterfaceElementoWifi == 0)
                        {
                            boolError = true;
                            mensajeError += " Favor ingrese el puerto del elemento.\n";
                        }


                        if (txtModelo == 'BACKBONE')
                        {
                            if (interfaceOdf == 0 || interfaceOdf == "")
                            {
                                boolError = true;
                                mensajeError += "El casette no tiene relacionado un ODF, favor corregir.\n";
                            }

                            if (!intIdElementoCaja || intIdElementoCaja == "" || intIdElementoCaja == 0)
                            {
                                boolError = true;
                                mensajeError += "El Elemento Caja no fue escogido, por favor seleccione.\n";
                            }
                            if (!intElementoPNivel || intElementoPNivel == "" || intElementoPNivel == 0)
                            {
                                boolError = true;
                                mensajeError += "El Elemento " + strNombreTipoElemento + " no fue escogido, por favor seleccione.\n";
                            }
                            if (null == intInterfaceElementoDistribucion) {
                                boolError = true;
                                mensajeError += "No ha seleccionado un hilo para el " + strNombreTipoElemento + ". \n";
                            }
                        }
                        

                        parametros = {
                            modeloNodoWifi: txtModelo,
                            idNodoWifi: idNodoWifi,
                            idElementoWifi: idElementoWifi,
                            idInterfaceElementoWifi: idInterfaceElementoWifi,                            
                            intInterfaceOdf: interfaceOdf,
                            idSolicitud: idSolicitud,
                            intIdElementoCaja: intIdElementoCaja,
                            idCasette: intElementoPNivel,
                            idInterfaceCasette: intInterfaceElementoDistribucion,
                            
                            strObservacion: strObservacionRegeneracion,
                            floatMetraje: floatMetraje,
                            strErrorMetraje: strErrorMetraje,
                            strNombreTipoElemento: strNombreTipoElemento
                        };

                        if (!boolError)
                        {
                            connFactibilidad.request({
                                url: url_asignarFactibilidad,
                                method: 'post',
                                timeout: 120000,
                                params: parametros,
                                success: function(response) {                                    
                                    cierraVentanaIngresoFactibilidad(winIngresoFactibilidad);
                                    if (IsJsonString(response.responseText)) 
                                    {
                                        const text = JSON.parse(response.responseText);
                                        if (text.status == "OK") 
                                        {
                                            Ext.Msg.alert('Mensaje', `Transacción Exitosa${text.mensaje}`, function (btn) {
                                                if (btn == 'ok') 
                                                {
                                                    store.load();
                                                }
                                            });
                                        }
                                        else 
                                        {
                                            Ext.MessageBox.show({
                                                title: 'Error',
                                                msg: text.mensaje,
                                                buttons: Ext.MessageBox.OK,
                                                icon: Ext.MessageBox.ERROR
                                            });
                                        }
                                    }else
                                    {
                                        var text = response.responseText;
                                        if (text == "OK") 
                                        {
                                            Ext.Msg.alert('Mensaje', `Transacción Exitosa`, function (btn) {
                                                if (btn == 'ok') 
                                                {
                                                    store.load();
                                                }
                                            });
                                        }
                                        else 
                                        {
                                            Ext.MessageBox.show({
                                                title: 'Error',
                                                msg: text,
                                                buttons: Ext.MessageBox.OK,
                                                icon: Ext.MessageBox.ERROR
                                            });
                                        }
                                    }
                                },
                                failure: function(result) {
                                    cierraVentanaIngresoFactibilidad(winIngresoFactibilidad);
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
                        cierraVentanaIngresoFactibilidad(winIngresoFactibilidad);
                    }
                }
            ]
        });

        winIngresoFactibilidad = Ext.widget('window', {
            title: 'Factibilidad Wifi',
            layout: 'fit',
            resizable: false,
            modal: true,
            closable: false,
            items: [formPanelIngresoFactibilidad]
        });
    }
    
    winIngresoFactibilidad.show();
    
    //valido q si ya tiene un nodo wifi q le aparezca ese mismo

    if (idElementoWifi)
    {
        //asignar valores a un combo
        storeElementosNodoWifi.add({idElemento: idElementoWifi, nombreElemento:rec.get('elemento'), modelo:rec.get('modeloNodo')});
        Ext.getCmp('cmbNodoWifi').setValue(idElementoWifi);
       
        Ext.getCmp('cmbNodoWifi').display = idElementoWifi;
        Ext.getCmp('cmbNodoWifi').value = idElementoWifi;
        Ext.getCmp('cmbNodoWifi').disable();

        storeElementosFactibles.proxy.extraParams = {idElemento: idElementoWifi, idServicio: rec.get('idServicio')};
        storeElementosFactibles.load({params: {}});
        Ext.getCmp('cmbElementoFactible').setVisible(true);
        Ext.getCmp('txtModeloNodoWifi').setValue('');
        Ext.getCmp('txtModeloNodoWifi').setVisible(true);
        Ext.getCmp('txtModeloNodoWifi').setValue(rec.get('modeloElementoWifi'));
        Ext.getCmp('cbxIdElementoCaja').disable();

        Ext.getCmp('cbxIdElementoCaja').setValue('');
        Ext.getCmp('cbxElementoPNivel').setValue('');
        Ext.getCmp('txtIdfloatMetraje').setValue("");
        Ext.getCmp('cbxPuertos').setValue("");
        Ext.getCmp('txtIdPuerto').setValue("");

        Ext.getCmp('cbxIdElementoCaja').setVisible(false);
        Ext.getCmp('cbxElementoPNivel').setVisible(false);
        Ext.getCmp('txtIdfloatMetraje').setVisible(false);
        Ext.getCmp('cbxPuertos').setVisible(false);
        Ext.getCmp('txtIdPuerto').setVisible(false);
        if (rec.get('modeloElementoWifi') == 'BACKBONE')
        {
            Ext.getCmp('cbxIdElementoCaja').setVisible(true);
        }
    }
    
}

function eliminarSeleccion(datosSelect)
{
    for (var i = 0; i < datosSelect.getSelectionModel().getCount(); i++)
    {
        datosSelect.getStore().remove(datosSelect.getSelectionModel().getSelection()[i]);
    }
}

function cierraVentanaIngresoFactibilidad()
{
    winIngresoFactibilidad.close();
    winIngresoFactibilidad.destroy();
}


/**
 * Función para validar un objeto JSON.
 *
 * @author Pablo Pin <ppin@telconet.ec>
 * @version 1.0 09-04-2019 | Versión Inicial.
 * @return {boolean}
 *
 */
function IsJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

function cierraVentanaRechazarOrden_Factibilidad()
{
    winRechazarOrden_Factibilidad.close();
    winRechazarOrden_Factibilidad.destroy();
}

function cierraVentanaPreFactibilidad()
{
    winPreFactibilidad.close();
    winPreFactibilidad.destroy();
}

function limpiar()
{
    Ext.getCmp('fechaDesdePlanif').setRawValue("");
    Ext.getCmp('fechaDesdePlanif').setValue(null);
    Ext.getCmp('fechaHastaPlanif').setRawValue("");
    Ext.getCmp('fechaHastaPlanif').setValue(null);
    Ext.getCmp('txtNodo').value = "";
    Ext.getCmp('txtNodo').setRawValue("");
    Ext.getCmp('txtEstado').value = "";
    Ext.getCmp('txtEstado').setRawValue("");
    Ext.getCmp('txtLogin').value = "";
    Ext.getCmp('txtLogin').setRawValue("");
    Ext.getCmp('sltCanton').value = "";
    Ext.getCmp('sltCanton').setRawValue("");
}
