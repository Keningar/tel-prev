Ext.require([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);

var intLimitePagina = 15;
var objStore        = '';
var intIdMotivo     = '';
var strUrl          = '';
var strAncho        = '100%';
var strEstadoFiltro = '';

Ext.onReady(function () 
{
    var objPermisoApRe     = $("#ROLE_443-7017");
    var boolPermisoApRe    = (typeof objPermisoApRe === 'undefined') ? false : (objPermisoApRe.val() == 1 ? true : false);
    var objPermisoApReDoc  = $("#ROLE_443-7037");
    var boolPermisoApReDoc = (typeof objPermisoApReDoc === 'undefined') ? false : (objPermisoApReDoc.val() == 1 ? true : false);
    Ext.define('modelDescuento', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'descripcion', type: 'string'},
            {name: 'descripTecnica', type: 'string'},
            {name: 'empresa', type: 'string'}
        ]
    });

    var objStoreMotivoDto = Ext.create('Ext.data.Store', {
        autoLoad : true,
        id       : 'idStoremodelDescuento',
        model    : "modelDescuento",
        proxy    :
        {
            type    : 'ajax',
            url     : url_lista_descuentos,
            timeout : 3000000,
            reader  :
            {
                type: 'json',
                root: 'motivos'
            }
        }
    });

    var objComboMotivoDto = new Ext.form.ComboBox({
        xtype             : 'combobox',
        store             : objStoreMotivoDto,
        labelAlign        : 'left',
        id                : 'objComboMotivoDto',
        name              : 'objComboMotivoDto',
        valueField        : 'idMotivoDes',
        displayField      : 'descripcion',
        fieldLabel        : 'Tipo de autorización',
        width             : 325,
        triggerAction     : 'all',
        selectOnFocus     : true,
        lastQuery         : '',
        mode              : 'local',
        allowBlank        : true,
        value             : 'Autorización Descuento',
        listeners         : 
        {
            select:
                function (e) 
                {
                    strDescripTecnica = e.displayTplData[0].descripTecnica;
                    if (strDescripTecnica != undefined && strDescripTecnica == 'AUTORIZACION_DESCUENTO')
                    {
                        $('#getPanelDescuento').empty();
                        $('#getPanelObservacion').empty();
                        getPanelDescuento();
                    }
                    else if (strDescripTecnica != undefined && strDescripTecnica == 'AUTORIZACION_INSTALACION')
                    {
                        $('#getPanelDescuento').empty();
                        $('#getPanelObservacion').empty();
                        getPanelInstalacion();
                    }
                    else if (strDescripTecnica != undefined && strDescripTecnica == 'AUTORIZACION_CAMBIO_DOCUMENTO')
                    {
                        $('#getPanelObservacion').empty();
                        $('#getPanelDescuento').empty();
                        getPanelDocumentos();
                    }
                },
            click: 
            {
                element: 'el',
                fn: function () 
                {
                    objStoreMotivoDto.load();
                }
            }
        }
    });

    /* ******************************************* */
            /*[INI] FILTROS DE BUSQUEDA */
    /* ******************************************* */

    Ext.define('modelEstado', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'strDescripcionCargo', type: 'string'},
            {name: 'strEstado', type: 'string'}
        ]
    });

    var objStoreEstado = Ext.create('Ext.data.Store', {
        autoLoad : true,
        model    : "modelEstado",
        proxy    :
        {
            type   : 'ajax',
            url    : url_lista_estado,
            reader : 
            {
                type: 'json',
                root: 'estado'
            }
        }
    });

    objComboEstado   = new Ext.form.ComboBox({
        xtype        : 'combobox',
        store        : objStoreEstado,
        labelAlign   : 'left',
        id           : 'strEstado',
        name         : 'strEstado',
        valueField   : 'strEstado',
        displayField : 'strEstado',
        fieldLabel   : 'Estado',
        width        : 325,
        triggerAction: 'all',
        selectOnFocus: true,
        lastQuery    : '',
        mode         : 'local',
        allowBlank   : true,
        value        : strEstadoCargo
    });

    DTFechaDesde = new Ext.form.DateField(
    {
        id         : 'fechaDesde',
        fieldLabel : 'Fecha de creación desde',
        labelAlign : 'left',
        xtype      : 'datefield',
        format     : 'Y-m-d',
        width      : 325,
    });
    DTFechaHasta = new Ext.form.DateField(
    {
        id         : 'fechaHasta',
        fieldLabel : 'Fecha de creación hasta',
        labelAlign : 'left',
        xtype      : 'datefield',
        format     : 'Y-m-d',
        width      : 325,
    });
    TFNombre = new Ext.form.TextField(
    {
        id         : 'nombre',
        fieldLabel : 'Nombre del cliente',
        width      : 325,
        xtype      : 'textfield'
    });
    TFApellido = new Ext.form.TextField(
    {
        id         : 'apellido',
        fieldLabel : 'Apellido del cliente',
        width      : 325,
        xtype      : 'textfield'
    });
    TFRazonSocial = new Ext.form.TextField(
    {
        id         : 'razonSocial',
        fieldLabel : 'Razón social',
        width      : 325,
        xtype      : 'textfield'
    });
    TFUsuarioCreacion = new Ext.form.TextField(
    {
        id         : 'usuarioCreacion',
        fieldLabel : 'Usuario creación',
        width      : 325,
        xtype      : 'textfield'
    });
    TFLogin = new Ext.form.TextField(
    {
        id         : 'login',
        fieldLabel : 'Login',
        width      : 325,
        xtype      : 'textfield'
    });
    TFPendienteApro = new Ext.form.TextField(
    {
        id         : 'pendienteAprobar',
        fieldLabel : 'Pendiente Aprobar',
        width      : 325,
        xtype      : 'textfield'
    });
    TFObservacion = new Ext.form.field.TextArea({
        xtype     : 'textareafield',
        name      : 'observacion',
        id        : 'observacion',
        fieldLabel: 'Observación de rechazo',
        cols      : 80,
        rows      : 2,
        maxLength : 200
    });
    /* ******************************************* */
            /*[FIN] FILTROS DE BUSQUEDA */
    /* ******************************************* */
    getPanelDescuento();

    /**
     * Documentación para la función 'getMostrarDiv'.
     *
     * Función que muestra elementos del html.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 11-12-2019
     *
     */
    function getMostrarDiv()
    {
        div               = document.getElementById('getCortesia');
        div.style.display = '';
        div               = document.getElementById('getObservacion');
        div.style.display = '';
    }

    /**
     * Documentación para la función 'getOcultarDiv'.
     *
     * Función que oculta elementos del html.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 11-12-2019
     *
     */
    function getOcultarDiv() 
    {
        div               = document.getElementById('getCortesia');
        div.style.display = 'none';
        div               = document.getElementById('getObservacion');
        div.style.display = 'none';
    }

    /**
     * Documentación para la función 'getMotivoRechazo'.
     *
     * Función encargada de llenar el combo de motivos.
     *
     * @param String $strUrl url de rechazo del documento.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 11-12-2019
     *
     */
    function getMotivoRechazo(strUrl) 
    {
        Ext.define('modelMotivo', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'idMotivo', type: 'string'},
                {name: 'descripcion', type: 'string'},
                {name: 'idRelacionSistema', type: 'string'}
            ]
        });

        var objStoreMotivoRechazo = Ext.create('Ext.data.Store', {
            autoLoad : false,
            model    : "modelMotivo",
            proxy    :
            {
                type   : 'ajax',
                url    : strUrl,
                reader : 
                {
                    type: 'json',
                    root: 'motivos'
                }
            }
        });

        objComboMotivo   = new Ext.form.ComboBox({
            xtype        : 'combobox',
            store        : objStoreMotivoRechazo,
            labelAlign   : 'left',
            id           : 'idMotivo',
            name         : 'idMotivo',
            valueField   : 'idMotivo',
            displayField : 'descripcion',
            fieldLabel   : 'Motivo de rechazo',
            width        : 400,
            triggerAction: 'all',
            selectOnFocus: true,
            lastQuery    : '',
            mode         : 'local',
            allowBlank   : true,
            listeners: 
            {
                select:
                    function (e) {
                        intIdMotivo         = Ext.getCmp('idMotivo').getValue();
                        relacion_sistema_id = e.displayTplData[0].idRelacionSistema;
                    },
                click: 
                {
                    element: 'el',
                    fn: function ()
                    {
                        intIdMotivo         = '';
                        relacion_sistema_id = '';
                        objStoreMotivoRechazo.load();
                    }
                }
            }
        });
    }

    /**
     * Documentación para la función 'getPanelDescuento'.
     *
     * Función encargada de crear el grid 'Autorización Descuento'.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 11-12-2019
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.1 16-06-2021 - Se modifica la función agregando las capacidades de los productos
     *                           cuando este sea Internet dedicado y L3MPLS
     *
     */
    function getPanelDescuento()
    {
        getOcultarDiv();
        strUrl         = url_lista_motivos;
        strUrlAprobar  = url_aprobar;
        strUrlRechazar = url_rechazar;
        getMotivoRechazo(strUrl);
        $('#strEstado').show();
        Ext.define('ListaDetalleModel', {
            extend: 'Ext.data.Model',
            fields: [{name: 'id', type: 'string'},
                {name: 'cliente', type: 'string'},
                {name: 'servicio', type: 'string'},
                {name: 'login', type: 'string'},
                {name: 'motivo', type: 'string'},
                {name:'asesor', type: 'string'},
                {name:'vOriginal', type: 'string'},
                {name:'vFinal', type: 'string'},
                {name: 'descuento', type: 'string'},
                {name: 'observacion', type: 'string'},
                {name: 'feCreacion', type: 'string'},
                {name: 'usrCreacion', type: 'string'},
                {name: 'linkVer', type: 'string'},
                {name: 'arrayCargos', type: 'string'},
                {name: 'strCargoActual', type: 'string'},
                {name: 'intCantCargos', type: 'string'},
                {name: 'estadoClt', type: 'string'},
                {name: 'estadoSolicitud', type: 'string'},
                {name: 'boolAprobar', type: 'boolean'},
                {name: 'boolVelocidad', type: 'boolean'},
                {name: 'strVelocidadUp', type: 'string'},
                {name: 'strVelocidadDown', type: 'string'}
            ]
        });

        objStore = Ext.create('Ext.data.JsonStore', {
            model   : 'ListaDetalleModel',
            pageSize: intLimitePagina,
            proxy   :
            {
                type   : 'ajax',
                url    : url_store,
                reader : 
                {
                    type          : 'json',
                    root          : 'solicitudes',
                    totalProperty : 'total'
                },
                extraParams: 
                {
                    fechaDesde      : '',
                    fechaHasta      : '',
                    nombre          : '',
                    apellido        : '',
                    razonSocial     : '',
                    usuarioCreacion : '',
                    strEstadoFiltro : '',
                    login           : ''
                },
                simpleSortMode: true
            },
            listeners:
            {
                beforeload: function (objStore)
                {
                    objStore.getProxy().extraParams.fechaDesde      = Ext.getCmp('fechaDesde').getValue();
                    objStore.getProxy().extraParams.fechaHasta      = Ext.getCmp('fechaHasta').getValue();
                    objStore.getProxy().extraParams.nombre          = Ext.getCmp('nombre').getValue();
                    objStore.getProxy().extraParams.apellido        = Ext.getCmp('apellido').getValue();
                    objStore.getProxy().extraParams.razonSocial     = Ext.getCmp('razonSocial').getValue();
                    objStore.getProxy().extraParams.usuarioCreacion = Ext.getCmp('usuarioCreacion').getValue();
                    objStore.getProxy().extraParams.strEstadoFiltro = Ext.getCmp('strEstado').getValue();
                    objStore.getProxy().extraParams.login           = Ext.getCmp('login').getValue();
                },
                load: function (objStore,records)
                {
                    if( objStore.proxy.reader.rawData.mensajeCargo ){
                        Ext.Msg.alert('Alerta',objStore.proxy.reader.rawData.mensajeCargo);
                    }
                    objStore.each(function (record)
                    {
                        if(Ext.isEmpty(record.data.cliente))
                        {
                            objStore.remove(record);
                        }
                    });
                }
            }
        });

        objStore.load({params: {start: 0, limit: 15}});

        sm = new Ext.selection.CheckboxModel({
            listeners:
            {
                selectionchange: function (selectionModel, selected, options)
                {
                    arregloSeleccionados = new Array();
                    Ext.each(selected, function (record) {});
                }
            }
        });

        Ext.create('Ext.grid.Panel', {
            width       : strAncho,
            autoHeight  : true,
            collapsible : false,
            title       : 'Autorización Descuento',
            selModel    : sm,
            dockedItems : [{
                xtype: 'toolbar',
                dock : 'top',
                align: '->',
                items: [
                    objComboMotivo,
                    {xtype: 'tbfill'},
                    , {
                        iconCls  : 'icon_aprobar',
                        text     : 'Aprobar',
                        disabled : false,
                        itemId   : 'aprobar',
                        scope    : this,
                        hidden   : !boolPermisoApRe,
                        handler  : function () 
                        {
                            getAprobarSeleccionados(strUrlAprobar)
                        }
                    }, {
                        iconCls  : 'icon_delete',
                        text     : 'Rechazar',
                        disabled : false,
                        itemId   : 'rechazar',
                        scope    : this,
                        hidden   : !boolPermisoApRe,
                        handler: function () 
                        {
                            getRechazarSeleccionados(strUrlRechazar)
                        }
                    }
                ]}],
            renderTo: Ext.get('getPanelDescuento'),
                bbar: Ext.create('Ext.PagingToolbar', {
                    store       : objStore,
                    displayInfo : true,
                    displayMsg  : 'Mostrando {0} - {1} de {2}',
                    emptyMsg    : "No hay datos para mostrar"
                }),
            store       : objStore,
            multiSelect : false,
            viewConfig  : 
            {
                emptyText : 'No hay solicitudes pendientes por aprobar'
            },
            listeners:
            {
                viewready: function (grid)
                {
                    var view = grid.view;
                    grid.mon(view,
                    {
                        uievent: function (type, view, cell, recordIndex, cellIndex, e)
                        {
                            grid.cellIndex   = cellIndex;
                            grid.recordIndex = recordIndex;
                        }
                    });

                    grid.tip = Ext.create('Ext.tip.ToolTip',
                    {
                        target     : view.el,
                        delegate   : '.x-grid-cell',
                        trackMouse : true,
                        autoHide   : false,
                        renderTo   : Ext.getBody(),
                        listeners:
                        {
                            beforeshow: function (tip)
                            {
                                if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1)
                                {
                                    header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                    if (header.dataIndex != null)
                                    {
                                        var trigger = tip.triggerElement,
                                        parent = tip.triggerElement.parentElement,
                                        columnDataIndex = view.getHeaderByCell(trigger).dataIndex;

                                        if (view.getRecord(parent).get(columnDataIndex) != null)
                                        {
                                            var columnText = view.getRecord(parent).get(columnDataIndex).toString();
                                            if (columnText)
                                            {
                                                tip.update(columnText);
                                            }
                                            else
                                            {
                                                return false;
                                            }
                                        }
                                        else
                                        {
                                            return false;
                                        }
                                    }
                                    else
                                    {
                                        return false;
                                    }
                                }
                            }
                        }
                    });

                    grid.tip.on('show', function ()
                    {
                        var timeout;
                        grid.tip.getEl().on('mouseout', function ()
                        {
                            timeout = window.setTimeout(function () {
                                grid.tip.hide();
                            }, 500);
                        });

                        grid.tip.getEl().on('mouseover', function () {
                            window.clearTimeout(timeout);
                        });

                        Ext.get(view.el).on('mouseover', function () {
                            window.clearTimeout(timeout);
                        });

                        Ext.get(view.el).on('mouseout', function ()
                        {
                            timeout = window.setTimeout(function () {
                                grid.tip.hide();
                            }, 500);
                        });
                    });
                },
                itemdblclick: function(view, record, item, index, eventobj, obj) 
                {
                    var position = view.getPositionByEvent(eventobj),
                        data = record.data,
                        value = data[this.columns[position.column].dataIndex];
                    Ext.Msg.show(
                    {
                        title: '¿Desea copiar el contenido?',
                        msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> "+
                        "<i class='fa fa-arrow-right' aria-hidden='true'></i> <b>" + value + "</b>",
                        buttons: Ext.Msg.OK,
                        icon: Ext.Msg.INFORMATION
                    });
                }
            },
            columns: [new Ext.grid.RowNumberer(),
            {
                text: 'Fecha de creación',
                dataIndex: 'feCreacion',
                align: 'center',
                width: 100
            },{
                text: 'Cliente',
                width: 200,
                align: 'center',
                dataIndex: 'cliente'
            }, {
                text: 'Login',
                width: 130,
                align: 'center',
                dataIndex: 'login'
            },{
                text: 'Vendedor',
                width: 180,
                align: 'center',
                dataIndex: 'asesor'
            },
            {
                text: 'Servicio',
                width: 100,
                align: 'center',
                dataIndex: 'servicio'
            }, {
                text: 'Motivo',
                width: 130,
                align: 'center',
                dataIndex: 'motivo'
            },{
                text: 'Valor original',
                dataIndex: 'vOriginal',
                align: 'center',
                width: 86

            }, {
                text: 'Descuento',
                dataIndex: 'descuento',
                align: 'center',
                width: 65
            }, {
                text: 'Valor Final',
                dataIndex: 'vFinal',
                align: 'center',
                width: 86
            }, {
                text: 'Observación de la solicitud',
                dataIndex: 'observacion',
                align: 'center',
                width: 275
            }, {
                text: 'Usuario Creación',
                dataIndex: 'usrCreacion',
                align: 'center',
                width: 95
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 80,
                items:
                [
                    {
                        getClass: function(v, meta, rec)
                        {
                            var objPermiso = $("#ROLE_443-1");
                            var boolPermiso = (typeof objPermiso === 'undefined') ? false : (objPermiso.val() == 1 ? true : false);
                            if (boolPermiso) 
                            {
                                tooltip = 'Ver Solicitud';
                                return 'button-grid-show';
                            }
                        },
                        tooltip: 'Ver Solicitud',
                        handler  : function(grid, rowIndex, colIndex) 
                        {
                            var objDatos = objStore.getAt(rowIndex);
                            getVentanaDetalle(objDatos,strUrl,strUrlAprobar,strUrlRechazar);
                        }
                    }
                ]
            }]
        });
        if(boolPermisoApRe)
        {
            $('#idMotivo').show();
        }
        else
        {
            $('#idMotivo').hide();
        }
    }

    /**
     * Documentación para la función 'getPanelInstalacion'.
     *
     * Función encargada de crear el grid 'Autorización de instalaciones'.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 11-12-2019
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.1 16-06-2021 - Se modifica la función agregando las capacidades de los productos
     *                           cuando este sea Internet dedicado y L3MPLS.
     *
     */
    function getPanelInstalacion()
    {
        getOcultarDiv();
        strUrl         = url_lista_motivos_insta;
        strUrlAprobar  = url_aprobar_insta;
        strUrlRechazar = url_rechazar_insta;
        getMotivoRechazo(strUrl);
        $('#strEstado').show();
        Ext.define('ListaDetalleModel', {
            extend: 'Ext.data.Model',
            fields: [{name: 'id', type: 'string'},
                {name: 'cliente', type: 'string'},
                {name: 'servicio', type: 'string'},
                {name: 'login', type: 'string'},
                {name: 'motivo', type: 'string'},
                {name:'asesor', type: 'string'},
                {name:'vInstalacion', type: 'string'},
                {name:'vFinal', type: 'string'},
                {name: 'descuento', type: 'string'},
                {name: 'observacion', type: 'string'},
                {name: 'feCreacion', type: 'string'},
                {name: 'usrCreacion', type: 'string'},
                {name: 'linkVer', type: 'string'},
                {name: 'arrayCargos', type: 'string'},
                {name: 'strCargoActual', type: 'string'},
                {name: 'intCantCargos', type: 'string'},
                {name: 'estadoClt', type: 'string'},
                {name: 'estadoSolicitud', type: 'string'},
                {name: 'boolAprobar', type: 'boolean'},
                {name: 'boolVelocidad', type: 'boolean'},
                {name: 'strVelocidadUp', type: 'string'},
                {name: 'strVelocidadDown', type: 'string'}
            ]
        });

        objStore = Ext.create('Ext.data.JsonStore', {
            model    : 'ListaDetalleModel',
            pageSize : intLimitePagina,
            proxy:
            {
                type:  'ajax',
                url    : url_store_insta,
                reader :
                {
                    type          : 'json',
                    root          : 'solicitudes',
                    totalProperty : 'total'
                },
                extraParams:
                {
                    fechaDesde      : '',
                    fechaHasta      : '',
                    nombre          : '',
                    apellido        : '',
                    razonSocial     : '',
                    usuarioCreacion : '',
                    login           : ''
                },
                simpleSortMode: true
            },
            listeners: 
            {
                beforeload: function (objStore) 
                {
                    objStore.getProxy().extraParams.fechaDesde      = Ext.getCmp('fechaDesde').getValue();
                    objStore.getProxy().extraParams.fechaHasta      = Ext.getCmp('fechaHasta').getValue();
                    objStore.getProxy().extraParams.nombre          = Ext.getCmp('nombre').getValue();
                    objStore.getProxy().extraParams.apellido        = Ext.getCmp('apellido').getValue();
                    objStore.getProxy().extraParams.razonSocial     = Ext.getCmp('razonSocial').getValue();
                    objStore.getProxy().extraParams.usuarioCreacion = Ext.getCmp('usuarioCreacion').getValue();
                    objStore.getProxy().extraParams.strEstadoFiltro = Ext.getCmp('strEstado').getValue();
                    objStore.getProxy().extraParams.login           = Ext.getCmp('login').getValue();
                },
                load: function (objStore,records) 
                {
                    objStore.each(function (record) 
                    {
                        if (Ext.isEmpty(record.data.cliente))
                        {
                            objStore.remove(record);
                        }
                    });
                }
            }
        });

        objStore.load({params: {start: 0, limit: 15}});

        sm = new Ext.selection.CheckboxModel({
            listeners: 
            {
                selectionchange: function (selectionModel, selected, options)
                {
                    arregloSeleccionados = new Array();
                    Ext.each(selected, function (record) {
                    });
                }
            }
        });

        Ext.get('getPanelDescuento').innerHTML = "";
        Ext.create('Ext.grid.Panel', {
            width       : strAncho,
            autoHeight  : true,
            collapsible : false,
            title       : 'Autorización de instalaciones',
            selModel    : sm,
            dockedItems: [{
                xtype : 'toolbar',
                dock  : 'top',
                align : '->',
                items : [
                    objComboMotivo,
                    {xtype: 'tbfill'},
                    , {
                        iconCls  : 'icon_aprobar',
                        text     : 'Aprobar',
                        disabled : false,
                        itemId   : 'aprobar',
                        scope    : this,
                        hidden   : !boolPermisoApRe,
                        handler  : function () 
                        {
                            getAprobarSeleccionados(strUrlAprobar)
                        }
                    }, {
                        iconCls  : 'icon_delete',
                        text     : 'Rechazar',
                        disabled : false,
                        itemId   : 'rechazar',
                        scope    : this,
                        hidden   : !boolPermisoApRe,
                        handler  : function () 
                        {
                            getRechazarSeleccionados(strUrlRechazar)
                        }
                    }
                ]}],
                renderTo: Ext.get('getPanelDescuento'),
                bbar: Ext.create('Ext.PagingToolbar', {
                    store: objStore,
                    displayInfo: true,
                    displayMsg: 'Mostrando {0} - {1} de {2}',
                    emptyMsg: "No hay datos para mostrar"
                }),
            store: objStore,
            multiSelect: false,
            viewConfig: {
                emptyText: 'No hay solicitudes pendientes por aprobar'
            }, listeners:
                {
                    viewready: function (grid)
                    {
                        var view = grid.view;
                        grid.mon(view,
                        {
                            uievent: function (type, view, cell, recordIndex, cellIndex, e)
                            {
                                grid.cellIndex  = cellIndex;
                                grid.recordIndex = recordIndex;
                            }
                        });

                        grid.tip = Ext.create('Ext.tip.ToolTip',
                        {
                            target     : view.el,
                            delegate   : '.x-grid-cell',
                            trackMouse : true,
                            autoHide   : false,
                            renderTo   : Ext.getBody(),
                            listeners:
                            {
                                beforeshow: function (tip)
                                {
                                    if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1)
                                    {
                                        header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                        if (header.dataIndex != null)
                                        {
                                            var trigger = tip.triggerElement,
                                            parent = tip.triggerElement.parentElement,
                                            columnDataIndex = view.getHeaderByCell(trigger).dataIndex;

                                            if (view.getRecord(parent).get(columnDataIndex) != null)
                                            {
                                                var columnText = view.getRecord(parent).get(columnDataIndex).toString();
                                                if (columnText)
                                                {
                                                    tip.update(columnText);
                                                }
                                                else
                                                {
                                                    return false;
                                                }
                                            }
                                            else
                                            {
                                                return false;
                                            }
                                        }
                                        else
                                        {
                                            return false;
                                        }
                                    }
                                }
                            }
                        });

                        grid.tip.on('show', function ()
                        {
                            var timeout;
                            grid.tip.getEl().on('mouseout', function ()
                            {
                                timeout = window.setTimeout(function () {
                                    grid.tip.hide();
                                }, 500);
                            });

                            grid.tip.getEl().on('mouseover', function () {
                                window.clearTimeout(timeout);
                            });

                            Ext.get(view.el).on('mouseover', function () {
                                window.clearTimeout(timeout);
                            });

                            Ext.get(view.el).on('mouseout', function ()
                            {
                                timeout = window.setTimeout(function () {
                                    grid.tip.hide();
                                }, 500);
                            });
                        });
                    },
                    itemdblclick: function(view, record, item, index, eventobj, obj) 
                    {
                        var position = view.getPositionByEvent(eventobj),
                            data = record.data,
                            value = data[this.columns[position.column].dataIndex];
                        Ext.Msg.show(
                        {
                            title: '¿Desea copiar el contenido?',
                            msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br>"+
                            "<i class='fa fa-arrow-right' aria-hidden='true'></i> <b>" + value + "</b>",
                            buttons: Ext.Msg.OK,
                            icon: Ext.Msg.INFORMATION
                        });
                    }
                },
            columns: [new Ext.grid.RowNumberer(),
            {
                text: 'Fecha de creación',
                dataIndex: 'feCreacion',
                align: 'center',
                width: 100
            },{
                text: 'Cliente',
                width: 200,
                align: 'center',
                dataIndex: 'cliente'
            },{
                text: 'Login',
                width: 130,
                align: 'center',
                dataIndex: 'login'
            },{
                text: 'Vendedor',
                width: 180,
                align: 'center',
                dataIndex: 'asesor'
            },{
                text: 'Servicio',
                width: 100,
                align: 'center',
                dataIndex: 'servicio'
            },{
                text: 'Motivo',
                width: 130,
                align: 'center',
                dataIndex: 'motivo'
            },{
                text: 'Valor instalación',
                dataIndex: 'vInstalacion',
                align: 'center',
                width: 86
            },{
                text: 'Descuento',
                dataIndex: 'descuento',
                align: 'center',
                width: 65
            },{
                text: 'Observación de la solicitud',
                dataIndex: 'observacion',
                align: 'center',
                width: 275
            },{
                text: 'Usuario Creación',
                dataIndex: 'usrCreacion',
                align: 'center',
                width: 95
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 80,
                items:
                [
                    {
                        getClass: function(v, meta, rec)
                        {
                            var objPermiso = $("#ROLE_443-1");
                            var boolPermiso = (typeof objPermiso === 'undefined') ? false : (objPermiso.val() == 1 ? true : false);
                            if (boolPermiso) 
                            {
                                tooltip = 'Ver Solicitud';
                                return 'button-grid-show';
                            }
                        },
                        tooltip: 'Ver Solicitud',
                        handler  : function(grid, rowIndex, colIndex) 
                        {
                            var objDatos = objStore.getAt(rowIndex);
                            getVentanaDetalle(objDatos,strUrl,strUrlAprobar,strUrlRechazar);
                        }
                    }
                ]
            }]
        });
        if(boolPermisoApRe)
        {
            $('#idMotivo').show();
        }
        else
        {
            $('#idMotivo').hide();
        }
    }

    /**
     * Documentación para la función 'getPanelDocumentos'.
     *
     * Función encargada de crear el grid 'Autorización de cambio de documento'.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 11-12-2019
     *
     */
    function getPanelDocumentos()
    {
        getMostrarDiv();
        strUrl         = url_lista_motivos_doc;
        strUrlAprobar  = url_aprobar_doc;
        strUrlRechazar = url_rechazar_doc;
        getMotivoRechazo(strUrl);
        $('#strEstado').hide();
        Ext.define('ListaDetalleModel', {
            extend: 'Ext.data.Model',
            fields: [{name: 'id', type: 'string'},
                {name: 'cliente', type: 'string'},
                {name: 'asesor', type: 'string'},
                {name: 'servicio', type: 'string'},
                {name: 'login', type: 'string'},
                {name: 'motivo', type: 'string'},
                {name: 'descuento', type: 'string'},
                {name: 'observacion', type: 'string'},
                {name: 'feCreacion', type: 'string'},
                {name: 'usrCreacion', type: 'string'},
                {name: 'valor', type: 'string'},
                {name: 'linkVer', type: 'string'}
            ]
        });

        objStore = Ext.create('Ext.data.JsonStore', {
            model   : 'ListaDetalleModel',
            pageSize: intLimitePagina,
            proxy: {
                type: 'ajax',
                url: url_store_doc,
                reader: {
                    type: 'json',
                    root: 'solicitudes',
                    totalProperty: 'total'
                },
                extraParams: {
                    fechaDesde     : '',
                    fechaHasta     : '',
                    nombre         : '',
                    apellido       : '',
                    razonSocial    : '',
                    usuarioCreacion: '',
                    login          : ''
                },
                simpleSortMode: true
            },
            listeners: {
                beforeload: function (objStore) {
                    objStore.getProxy().extraParams.fechaDesde      = Ext.getCmp('fechaDesde').getValue();
                    objStore.getProxy().extraParams.fechaHasta      = Ext.getCmp('fechaHasta').getValue();
                    objStore.getProxy().extraParams.nombre          = Ext.getCmp('nombre').getValue();
                    objStore.getProxy().extraParams.apellido        = Ext.getCmp('apellido').getValue();
                    objStore.getProxy().extraParams.razonSocial     = Ext.getCmp('razonSocial').getValue();
                    objStore.getProxy().extraParams.usuarioCreacion = Ext.getCmp('usuarioCreacion').getValue();
                    objStore.getProxy().extraParams.login           = Ext.getCmp('login').getValue();
                },
                load: function (objStore,records) {
                    objStore.each(function (record) {
                        if (Ext.isEmpty(record.data.cliente)){
                            objStore.remove(record);
                        }
                    });
                }
            }
        });

        objStore.load({params: {start: 0, limit: 15}});

        sm = new Ext.selection.CheckboxModel({
            listeners: {
                selectionchange: function (selectionModel, selected, options) {
                    arregloSeleccionados = new Array();
                    Ext.each(selected, function (record) {
                    });
                }
            }
        });

        Ext.create('Ext.grid.Panel', {
            width      : strAncho,
            autoHeight : true,
            collapsible: false,
            title      : '',
            selModel   : sm,
            dockedItems: [{
                    xtype: 'toolbar',
                    dock: 'top',
                    align: '->',
                    items: [
                        objComboMotivo,
                        {xtype: 'tbfill'},
                        , {
                            iconCls : 'icon_aprobar',
                            text     : 'Aprobar',
                            disabled : false,
                            itemId   : 'aprobar',
                            scope    : this,
                            hidden   : !boolPermisoApReDoc,
                            handler: function () {
                                getAprobarSeleccionados(strUrlAprobar)
                            }
                        }, {
                            iconCls : 'icon_delete',
                            text     : 'Rechazar',
                            disabled : false,
                            itemId   : 'rechazar',
                            scope    : this,
                            hidden   : !boolPermisoApReDoc,
                            handler: function () {
                                getRechazarSeleccionados(strUrlRechazar)
                            }
                        }]}],
            renderTo: Ext.get('getPanelDescuento'),
            bbar: Ext.create('Ext.PagingToolbar', {
                store: objStore,
                displayInfo: true,
                displayMsg: 'Mostrando {0} - {1} de {2}',
                emptyMsg: "No hay datos para mostrar"
            }),
            store: objStore,
            multiSelect: false,
            viewConfig: {
                emptyText: 'No hay solicitudes pendientes por aprobar'
            }, listeners:
                {
                    viewready: function (grid)
                    {
                        var view = grid.view;
                        grid.mon(view,
                            {
                                uievent: function (type, view, cell, recordIndex, cellIndex, e)
                                {
                                    grid.cellIndex = cellIndex;
                                    grid.recordIndex = recordIndex;
                                }
                            });

                        grid.tip = Ext.create('Ext.tip.ToolTip',
                        {
                            target    : view.el,
                            delegate  : '.x-grid-cell',
                            trackMouse: true,
                            autoHide  : false,
                            renderTo  : Ext.getBody(),
                            listeners:
                            {
                                beforeshow: function (tip)
                                {
                                    if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1)
                                    {
                                        header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                        if (header.dataIndex != null)
                                        {
                                            var trigger = tip.triggerElement,
                                                parent = tip.triggerElement.parentElement,
                                                columnDataIndex = view.getHeaderByCell(trigger).dataIndex;

                                            if (view.getRecord(parent).get(columnDataIndex) != null)
                                            {
                                                var columnText = view.getRecord(parent).get(columnDataIndex).toString();
                                                if (columnText)
                                                {
                                                    tip.update(columnText);
                                                }
                                                else
                                                {
                                                    return false;
                                                }
                                            }
                                            else
                                            {
                                                return false;
                                            }
                                        }
                                        else
                                        {
                                            return false;
                                        }
                                    }
                                }
                            }
                        });

                        grid.tip.on('show', function ()
                        {
                            var timeout;

                            grid.tip.getEl().on('mouseout', function ()
                            {
                                timeout = window.setTimeout(function () {
                                    grid.tip.hide();
                                }, 500);
                            });

                            grid.tip.getEl().on('mouseover', function () {
                                window.clearTimeout(timeout);
                            });

                            Ext.get(view.el).on('mouseover', function () {
                                window.clearTimeout(timeout);
                            });

                            Ext.get(view.el).on('mouseout', function ()
                            {
                                timeout = window.setTimeout(function () {
                                    grid.tip.hide();
                                }, 500);
                            });
                        });
                    },
                    itemdblclick: function(view, record, item, index, eventobj, obj) 
                    {
                        var position = view.getPositionByEvent(eventobj),
                            data = record.data,
                            value = data[this.columns[position.column].dataIndex];
                        Ext.Msg.show(
                        {
                            title: '¿Desea copiar el contenido?',
                            msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> "+
                            "<i class='fa fa-arrow-right' aria-hidden='true'></i> <b>" + value + "</b>",
                            buttons: Ext.Msg.OK,
                            icon: Ext.Msg.INFORMATION
                        });
                    }
                },
            columns: [new Ext.grid.RowNumberer(),
            {
                text: 'Fecha de creación',
                dataIndex: 'feCreacion',
                align: 'center',
                width: 100
            },{
                text: 'Cliente',
                width: 200,
                align: 'center',
                dataIndex: 'cliente'
            },{
                text: 'Login',
                width: 130,
                align: 'center',
                dataIndex: 'login'
            },{
                text: 'Vendedor',
                width: 180,
                align: 'center',
                dataIndex: 'asesor'
            },{
                text: 'Servicio',
                width: 100,
                align: 'center',
                dataIndex: 'servicio'
            },{
                text: 'Motivo',
                width: 130,
                align: 'center',
                dataIndex: 'motivo'
            },{
                text: 'Valor',
                align: 'center',
                width: 86,
                dataIndex: 'valor'
            },{
                text: 'Tipo doc.',
                dataIndex: 'descuento',
                align: 'center',
                width: 65
            },{
                text: 'Observación de la solicitud',
                dataIndex: 'observacion',
                align: 'center',
                width: 275
            },{
                text: 'Usuario Creación',
                dataIndex: 'usrCreacion',
                align: 'center',
                width: 95
            }]
        });

        Ext.create('Ext.panel.Panel', {
            bodyPadding : 7,
            border      : true,
            hidden   : !boolPermisoApReDoc,
            bodyStyle   : {
                background: '#fff'
            },
            defaults:
            {
                bodyStyle: 'padding:10px'
            },
            width: strAncho,
            title: 'Autorización de cambio de documento',
            items: [
                TFObservacion
            ],
            renderTo: 'getPanelObservacion'
        });
        if(boolPermisoApReDoc)
        {
            $('#idMotivo').show();
        }
        else
        {
            $('#idMotivo').hide();
        }
    }

    Ext.create('Ext.panel.Panel', {
        bodyPadding : 7,
        border      : false,
        buttonAlign : 'center',
        layout      : 
        {
            type    : 'table',
            columns : 4,
            align   : 'center',
            border :true
        },
        bodyStyle: 
        {
            background: '#fff'
        },
        defaults:
            {
                bodyStyle: 'padding:10px'
            },
        collapsible: true,
        collapsed  : true,
        width      : strAncho,
        title      : 'Criterios de búsquedas',
        buttons: [
            {
                text    : 'Buscar',
                iconCls : "icon_search",
                handler : Buscar,
            },
            {
                text    : 'Limpiar',
                iconCls : "icon_limpiar",
                handler : function () 
                {
                    limpiar();
                }
            }

        ],
        items: [objComboMotivoDto,
            {html: "&nbsp;", border: false, width: 50},
            TFLogin,
            {html: "&nbsp;", border: false, width: 50},
            DTFechaDesde,
            {html: "&nbsp;", border: false, width: 50},
            DTFechaHasta,
            {html: "&nbsp;", border: false, width: 50},
            TFNombre,
            {html: "&nbsp;", border: false, width: 50},
            TFApellido,
            {html: "&nbsp;", border: false, width: 50},
            TFRazonSocial,
            {html: "&nbsp;", border: false, width: 50},
            objComboEstado,
            {html: "&nbsp;", border: false, width: 50},
        ],
        renderTo: 'getPanelFiltro'
    });
});

    /**
     * Documentación para la función 'Buscar'.
     *
     * Función que realiza la búsqueda con los campos ingresados por el usuario.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 11-12-2019
     *
     */
    function Buscar()
    {
        var arrayParametros = new Array();
        var strRazonSocial  = Ext.getCmp('razonSocial').getValue();
        var strNombre       = Ext.getCmp('nombre').getValue();
        var strApellido     = Ext.getCmp('apellido').getValue();
        var boolContinuar   = true;
        var strMensaje      = 'Para realizar la búsqueda, <b>mínimo debe ingresar 4 letras</b>.';

        if(strRazonSocial.length > 0 && strRazonSocial.length < 4)
        {
            arrayParametros.push(Ext.get('razonSocial'));
            boolContinuar = false;
        }
        if(strNombre.length > 0 && strNombre.length < 4)
        {
            arrayParametros.push(Ext.get('nombre'));
            boolContinuar = false;
        }
        if(strApellido.length > 0 && strApellido.length < 4)
        {
            arrayParametros.push(Ext.get('apellido'));
            boolContinuar = false;
        }
        if(boolContinuar)
        {
            objStore.currentPage = 1;
            objStore.load({params: {start: 0, limit: 15}});
        }
        else
        {
            mostrarAlertaFormulario(strMensaje,arrayParametros);
        }
    }
    /**
     * Documentación para la función 'mostrarAlertaFormulario'.
     *
     * Función que muestra una alerta en el campo donde el usuario debe corregir.
     *
     * @param string $strMensaje      => String que contenga el mensaje general a mostrar.
     * @param array  $arrayParametros => Array que contenga los identificadores del elemento HTML.
     *
     * @return string Texto a mostrar en pantalla.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 04-01-2020
     *
     */
    function mostrarAlertaFormulario(strMensaje, arrayParametros)
    {
        Ext.Msg.alert("Advertencia", `${strMensaje}`, function (btn) 
        {
            if (btn == 'ok')
            {
                for(i=0;i<arrayParametros.length;i++)
                {
                    let objCampo = Ext.get(arrayParametros[i]);
                    objCampo.addCls('animated tada');
                    objCampo.frame('red', 1, {
                        duration: 1000
                    });
                    objCampo.focus();
                    objCampo.setStyle({borderColor: 'red'});
                    setTimeout(
                        function () {
                            objCampo.removeCls('animated tada');
                        },
                        2000
                    );
                }
            }
        });
    }
    /**
     * Documentación para la función 'limpiar'.
     *
     * Función que limpia los campos ingresados por el usuario.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 11-12-2019
     *
     */
    function limpiar()
    {
        Ext.getCmp('fechaDesde').setRawValue("");
        Ext.getCmp('fechaHasta').setRawValue("");
        Ext.getCmp('nombre').setValue("");
        Ext.getCmp('apellido').setValue("");
        Ext.getCmp('razonSocial').setValue("");
        Ext.getCmp('usuarioCreacion').setValue("");
        Ext.getCmp('login').setValue("");
        Ext.getCmp('strEstado').setValue("Todos");
    }
    /**
     * Documentación para la función 'getAprobarSeleccionados'.
     *
     * Función que aprueba los documentos seleccionados por el usuario.
     *
     * @param String $strUrlAprobar url de aprobación del documento.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 11-12-2019
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.1 16-06-2021 - Se agregan validaciones al momento de querer aprobar/rechazar solicitudes.
     *
     */
    function getAprobarSeleccionados(strUrlAprobar)
    {
        var strEstadoSeleccionado = Ext.getCmp('strEstado').getValue();
        if(strEstadoCargo != strEstadoSeleccionado)
        {
            Ext.Msg.alert('Alerta','La solicitud por aprobar no corresponde al estado que usted tiene permitido aprobar.');
            return;
        }
        var strParametro  = '';
        var strTipoDoc    = '';
        var boolContinuar = false;

        if(Ext.getCmp('idMotivo').getValue() != '')
        {
            Ext.getCmp('idMotivo').setRawValue("");
        }
        if(Ext.getCmp('observacion').getValue() != '')
        {
            Ext.getCmp('observacion').setRawValue("");
        }

        if(strUrlAprobar == url_aprobar_doc)
        {
            boolContinuar = true;
        }
        if (sm.getSelection().length > 0)
        {
            var intContEstado = 0;
            for (var i = 0; i < sm.getSelection().length; ++i)
            {
                strParametro = strParametro + sm.getSelection()[i].data.id;
                if(boolContinuar)
                {
                    strTipoDoc = strTipoDoc + sm.getSelection()[i].data.descuento;  
                }
                if (sm.getSelection()[i].data.estado == 'Eliminado')
                {
                    intContEstado = intContEstado + 1;
                }
                if (i < (sm.getSelection().length - 1))
                {
                    strParametro = strParametro + '|';
                    if(boolContinuar)
                    {
                        strTipoDoc = strTipoDoc + '|';
                    }
                }
            }
            if (intContEstado == 0)
            {
                Ext.Msg.confirm('Alerta', 'Se aprobarán las solicitudes seleccionadas.<br> ¿Desea continuar?', function (btn) {
                    if (btn == 'yes') 
                    {
                        Ext.MessageBox.wait("Procesando aprobación...");
                        Ext.Ajax.request({
                            url   : strUrlAprobar,
                            method: 'post',
                            params: {param: strParametro,tipoDoc : strTipoDoc},
                            success: function (response)
                            {
                                Ext.MessageBox.hide();
                                objStore.load();
                                Ext.Msg.alert('Alerta','Transacción exitosa!');
                            },
                            failure: function (result)
                            {
                                objStore.load();
                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                            }
                        });
                    }
                });
            }
            else
            {
                Ext.Msg.alert('Alerta','Uno de los registros se encuentra en estado ELIMINADO.');
            }
        }
        else
        {
            Ext.Msg.alert('Alerta','Seleccione por lo menos un registro del listado.');
        }
    }
    /**
     * Documentación para la función 'getRechazarSeleccionados'.
     *
     * Función que rechaza los documentos seleccionados por el usuario.
     *
     * @param String $strUrlRechazar url de rechazo del documento.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 11-12-2019
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.1 16-06-2021 - Se agregan validaciones al momento de querer aprobar/rechazar solicitudes.
     *
     */
    function getRechazarSeleccionados(strUrlRechazar)
    {
        var strEstadoSeleccionado = Ext.getCmp('strEstado').getValue();
        if(strEstadoCargo != strEstadoSeleccionado)
        {
            Ext.Msg.alert('Alerta','La solicitud por rechazar no corresponde al estado que usted tiene permitido rechazar.');
            return;
        }
        var strParametro   = '';
        var strObservacion = '';
        if (sm.getSelection().length > 0)
        {
            var intContEstado = 0;
            for (var i = 0; i < sm.getSelection().length; ++i)
            {
                strParametro = strParametro + sm.getSelection()[i].data.id;
                if (sm.getSelection()[i].data.estado == 'Eliminado')
                {
                    intContEstado = intContEstado + 1;
                }
                if (i < (sm.getSelection().length - 1))
                {
                    strParametro = strParametro + '|';
                }
                if(strUrlRechazar == url_rechazar_doc)
                {
                    strObservacion = TFObservacion.getValue();
                }
            }
            if (intContEstado == 0)
            {
                if(Ext.getCmp('idMotivo').getValue())
                {
                    intIdMotivo = Ext.getCmp('idMotivo').getValue();
                    Ext.Msg.confirm('Alerta', 'Se rechazarán las solicitudes seleccionadas.<br> ¿Desea continuar?', function (btn) {
                        if (btn == 'yes')
                        {
                            Ext.MessageBox.wait("Procesando...");
                            Ext.Ajax.request({
                                url: strUrlRechazar,
                                method: 'post',
                                params: {param: strParametro, motivoId: intIdMotivo, obs:strObservacion},
                                success: function (response) 
                                {
                                    Ext.MessageBox.hide();
                                    objStore.load();
                                    Ext.Msg.alert('Alerta','Transacción exitosa!');
                                    if(Ext.getCmp('idMotivo').getValue() != '')
                                    {
                                        Ext.getCmp('idMotivo').setRawValue("");
                                    }
                                    if(Ext.getCmp('observacion').getValue() != '')
                                    {
                                        Ext.getCmp('observacion').setRawValue("");
                                    }
                                },
                                failure: function (result)
                                {
                                    objStore.load();
                                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                }
                            });
                        }
                    });
                }
                else
                {
                    var strMensaje      = 'Debe seleccionar un motivo para poder rechazar.';
                    var arrayParametros = new Array();
                    arrayParametros.push(Ext.get('idMotivo'));
                    mostrarAlertaFormulario(strMensaje,arrayParametros);
                }
            }
            else
            {
                Ext.Msg.alert('Alerta','Uno de los registros se encuentra en estado ELIMINADO.');
            }
        }
        else
        {
            Ext.Msg.alert('Alerta','Seleccione por lo menos un registro del listado.');
        }
    }

    /**
     * Documentación para la función 'getVentanaDetalle'.
     *
     * Función que muestra una ventana emergente con los datos del cliente, 
     * de la solicitud y un gráfico indicando quién falta de aprobar.
     *
     * @param Object $objDatos       objeto que contiene los datos de la solicitud.
     * @param String $strUrl         url de la lista de motivo de rechazo.
     * @param String $strUrlAprobar  url de aprobación del documento.
     * @param String $strUrlRechazar url de rechazo del documento.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 16-06-2021
     *
     */
    function getVentanaDetalle(objDatos,strUrl,strUrlAprobar,strUrlRechazar)
    {
        var objPermisoApRe  = $("#ROLE_443-7017");
        var boolPermisoApRe = (typeof objPermisoApRe === 'undefined') ? false : (objPermisoApRe.val() == 1 ? true : false);
        var strCargos      = objDatos.data['arrayCargos'];
        var arrayCargos    = strCargos.split('|');
        var strVendedor    = objDatos.data['asesor'];
        var strCliente     = objDatos.data['cliente'];
        var strLogin       = objDatos.data['login'];
        var strServicio    = objDatos.data['servicio'];
        var strEstado      = objDatos.data['estadoClt'];
        var strCargoActual = objDatos.data['strCargoActual'];
        var intCantCargos  = objDatos.data['intCantCargos'];
        var strObservacion = objDatos.data['observacion'];
        var strVOriginal   = objDatos.data['vOriginal'];
        var strDescuento   = objDatos.data['descuento'];
        var strVFinal      = objDatos.data['vFinal'];
        var strMotivo      = objDatos.data['motivo'];
        var boolVelocidad  = objDatos.data['boolVelocidad'];
        var strVelocidadUp = objDatos.data['strVelocidadUp'];
        var strVelocidadDown= objDatos.data['strVelocidadDown'];
        var strTextoValor  = 'Valor Original';
        var boolAprobar    = objDatos.data['boolAprobar'];
        var boolAprobarAll = (boolAprobar == true  && boolPermisoApRe == true) ? true:false;
        if(strVOriginal == undefined && strVOriginal == null)
        {
            strVOriginal   = objDatos.data['vInstalacion'];
            strTextoValor  = 'Valor Instalación';
        }
        for(var i=arrayCargos.length; i>intCantCargos; i--)
        {
            arrayCargos.pop();
        }
        var objPanel = Ext.create('Ext.form.Panel',
        {
            id           : 'objPanel',
            bodyPadding  : 10,
            waitMsgTarget: true,
            buttonAlign  : 'center',
            items: 
            [
                {
                    xtype : 'fieldset',
                    title : 'Flujo de autorización',
                    items:
                    [
                        {
                            xtype : 'label',
                            html  : '<br/><br/><div class="progress-bar-wrapper"></div><br/><br/>'
                        }
                    ]
                },
                {
                    xtype : 'fieldset',
                    title : 'Información general',
                    layout: {
                        type    : 'table',
                        columns : 2,
                        align   : 'stretch'
                    },
                    items:
                    [
                        {
                            xtype      : 'fieldset',
                            title      : 'Datos del cliente',
                            width      : 377,
                            height     : 215,
                            defaultType: 'textfield',
                            items:
                            [
                                {
                                    xtype     : 'displayfield',
                                    fieldLabel: 'Cliente',
                                    value     : strCliente
                                },
                                {
                                    xtype     : 'displayfield',
                                    fieldLabel: 'Login',
                                    value     : strLogin
                                },
                                {
                                    xtype     : 'displayfield',
                                    fieldLabel: 'Estado',
                                    value     : strEstado
                                },
                                {
                                    xtype     : 'displayfield',
                                    fieldLabel: 'Vendedor',
                                    value     : strVendedor
                                },
                                {
                                    xtype     : 'displayfield',
                                    fieldLabel: 'Servicio',
                                    value     : strServicio
                                }
                            ]
                        },
                        {
                            xtype      : 'fieldset',
                            title      : 'Datos de la solicitud',
                            width      : 377,
                            height     : 215,
                            defaultType: 'textfield',
                            items:
                            [
                                {
                                    xtype     : 'displayfield',
                                    fieldLabel: strTextoValor,
                                    value     : strVOriginal
                                },
                                {
                                    xtype     : 'displayfield',
                                    fieldLabel: 'Descuento',
                                    value     : strDescuento
                                },
                                {
                                    xtype     : 'displayfield',
                                    fieldLabel: 'Valor final',
                                    value     : strVFinal
                                },
                                {
                                    xtype     : 'displayfield',
                                    fieldLabel: 'Motivo',
                                    value     : strMotivo
                                },
                                {
                                    xtype     : 'displayfield',
                                    fieldLabel: 'Velocidad Up',
                                    value     : strVelocidadUp,
                                    hidden: boolVelocidad
                                },
                                {
                                    xtype     : 'displayfield',
                                    fieldLabel: 'Velocidad Down',
                                    value     : strVelocidadDown,
                                    hidden: boolVelocidad
                                },
                                {
                                    xtype     : 'displayfield',
                                    fieldLabel: 'Observación',
                                    value     : strObservacion
                                }
                            ]
                        }
                    ]
                }
            ],
            buttons:
            [
                {
                    text    : 'Aprobar',
                    iconCls : 'icon_aprobar',
                    formBind: true,
                    hidden  : !boolAprobarAll,
                    handler : function()
                    {
                        getAprobarModal(objDatos,strUrlAprobar);
                        objVentanaDescuento.destroy();
                    }
                },
                {
                    text    : 'Rechazar',
                    iconCls : 'icon_delete',
                    formBind: true,
                    hidden  : !boolAprobarAll,
                    handler : function()
                    {
                        getPanelMotivo(objDatos,strUrl,strUrlRechazar);
                        objVentanaDescuento.destroy();
                    }
                }
            ]
        });

        objVentanaDescuento = Ext.create('Ext.window.Window', {
                                title      :'Información de la solicitud',
                                modal      : true,
                                width      : 810,
                                height     : 500,
                                resizable  : false,
                                layout     : 'fit',
                                items      : [objPanel],
                                buttonAlign: 'center',
                            }).show();
        ProgressBar.init(arrayCargos,
                         strCargoActual,
                         'progress-bar-wrapper');
    }

    /**
     * Documentación para la función 'getPanelMotivo'.
     *
     * Función que muestra una ventana emergente con una lista de los motivos de rechazo.
     *
     * @param Object $objDatos       objeto que contiene los datos de la solicitud.
     * @param String $strUrl         url de la lista de motivo de rechazo.
     * @param String $strUrlRechazar url de rechazo del documento.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 16-06-2021
     *
     */
    function getPanelMotivo(objDatos,strUrl,strUrlRechazar)
    {
        Ext.define('modelMotivo', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'idMotivo', type: 'string'},
                {name: 'descripcion', type: 'string'},
                {name: 'idRelacionSistema', type: 'string'}
            ]
        });
        var objStoreMotivoRechazoModal = Ext.create('Ext.data.Store', {
            autoLoad : false,
            model    : "modelMotivo",
            proxy    :
            {
                type   : 'ajax',
                url    : strUrl,
                reader : 
                {
                    type: 'json',
                    root: 'motivos'
                }
            }
        });
        var objPanelMotivo = Ext.create('Ext.form.Panel',
        {
            id           : 'objPanelMotivo',
            bodyPadding  : 5,
            waitMsgTarget: true,
            fieldDefaults: 
            {
                labelAlign: 'left',
                labelWidth: 85,
                msgTarget : 'side'
            },
            items: 
            [
                {
                    xtype          : 'combobox',
                    fieldLabel     : 'Motivo de rechazo',
                    id             : 'idMotivoModal',
                    name           : 'idMotivoModal',
                    store          : objStoreMotivoRechazoModal,
                    valueField     : 'idMotivo',
                    displayField   : 'descripcion',
                    queryMode      : 'remote',
                    emptyText      : 'Seleccione',
                    forceSelection : true
                }
            ],
            buttons:
            [
                {
                    id      : 'btnguardar',
                    text    : 'Guardar cambios',
                    handler : function()
                    {
                        getRechazarModal(objDatos,strUrlRechazar);
                    }
                },
                {
                    text: 'Cerrar',
                    handler: function()
                    {
                        objVentanaMotivo.destroy();
                    }
                }
            ]
        });

        objVentanaMotivo = Ext.create('Ext.window.Window',
              {
                   title      : 'Motivo de rechazo',
                   modal      : true,
                   width      : 270,
                   resizable  : false,
                   layout     : 'fit',
                   buttonAlign: 'center',
                   items: [objPanelMotivo]
              }).show();
    }

    /**
     * Documentación para la función 'getRechazarModal'.
     *
     * Función que rechaza los documentos seleccionados por el usuario.
     *
     * @param Object $objDatos       objeto que contiene los datos de la solicitud.
     * @param String $strUrlRechazar url de rechazo del documento.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 16-06-2021
     *
     */
    function getRechazarModal(objDatos,strUrlRechazar)
    {
        var intIdSolicitud = objDatos.data['id'];
        var intIdMotivo    = Ext.getCmp('idMotivoModal').getValue();
        if(typeof(intIdMotivo) != 'undefined' && intIdMotivo != null)
        {
            Ext.Msg.confirm('Alerta', 'Se rechazará la solicitud.<br> ¿Desea continuar?', function (btn) {
                if (btn == 'yes')
                {
                    Ext.MessageBox.wait("Procesando...");
                    Ext.Ajax.request({
                        url: strUrlRechazar,
                        method: 'post',
                        params: {param: intIdSolicitud, motivoId: intIdMotivo},
                        success: function (response) 
                        {
                            Ext.MessageBox.hide();
                            objVentanaMotivo.destroy();
                            objStore.load();
                            Ext.Msg.alert('Alerta','Transacción exitosa!');
                        },
                        failure: function (result)
                        {
                            objStore.load();
                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                        }
                    });
                }
            });
        }
        else
        {
            var strMensaje      = 'Debe seleccionar un motivo para poder rechazar.';
            var arrayParametros = new Array();
            arrayParametros.push(Ext.get('idMotivoModal'));
            mostrarAlertaFormulario(strMensaje,arrayParametros);
        }
    }

    /**
     * Documentación para la función 'getAprobarSeleccionados'.
     *
     * Función que aprueba los documentos seleccionados por el usuario.
     *
     * @param String $strUrlAprobar url de aprobación del documento.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 16-06-2021
     *
     */
    function getAprobarModal(objDatos,strUrlAprobar)
    {
        var intIdSolicitud = objDatos.data['id'];
        Ext.Msg.confirm('Alerta', 'Se aprobará la solicitud.<br> ¿Desea continuar?', function (btn) {
            if (btn == 'yes') 
            {
                Ext.MessageBox.wait("Procesando aprobación...");
                Ext.Ajax.request({
                    url   : strUrlAprobar,
                    method: 'post',
                    params: {param: intIdSolicitud},
                    success: function (response)
                    {
                        Ext.MessageBox.hide();
                        objStore.load();
                        Ext.Msg.alert('Alerta','Transacción exitosa!');
                    },
                    failure: function (result)
                    {
                        objStore.load();
                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                    }
                });
            }
        });
    }