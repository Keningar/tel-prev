var storeGetTipoSolicitudesClientes = null;
var storeGetSolicitudesClientes     = null;
var gridDetalleSolicitudes          = null;
var strDescripcionSolicitudMasivo   = 'SOLICITUD CAMBIO MASIVO CLIENTES VENDEDOR';
var strDescSolicitudVendedorOrigen  = 'SOLICITUD_CAMBIO_MASIVO_VENDEDOR_ORIGEN';

var connEsperaAccion = new Ext.data.Connection
({
    timeout: 9000000,
    listeners:
    {
        'beforerequest': 
        {
            fn: function (con, opt)
            {						
                Ext.MessageBox.show
                ({
                   msg: 'Grabando la información...',
                   progressText: 'Guardando...',
                   width:300,
                   wait:true,
                   waitConfig: {interval:200}
                });
            },
            scope: this
        },
        'requestcomplete':
        {
            fn: function (con, res, opt)
            {
                Ext.MessageBox.hide();
            },
            scope: this
        },
        'requestexception': 
        {
            fn: function (con, res, opt)
            {
                Ext.MessageBox.hide();
            },
            scope: this
        }
    }
});

Ext.require
([
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

Ext.onReady(function()
{
    Ext.tip.QuickTipManager.init();

    /* ******************************************* */
    /* ************* GRID DE BUSQUEDA ************ */
    /* ******************************************* */
    var selectionDetalleSolicitudesClientes = Ext.create('Ext.selection.CheckboxModel',
    {
        checkOnly: true,
        listeners:
        {
            selectionchange: function(sm, selections)
            {
                gridDetalleSolicitudes.down('#btnRechazar').setDisabled(selections.length == 0);
                gridDetalleSolicitudes.down('#btnAprobar').setDisabled(selections.length == 0);
            }
        }
    });
    
    Ext.define('modelDetalleSolicitud', 
    {
        extend: 'Ext.data.Model',
        fields: 
        [
            {name: 'id',                     mapping: 'id'},
            {name: 'estado',                 mapping: 'estado'},
            {name: 'observacion',            mapping: 'observacion'},
            {name: 'feCreacion',             mapping: 'feCreacion'},
            {name: 'usrCreacion',            mapping: 'usrCreacion'},
            {name: 'precioDescuento',        mapping: 'precioDescuento'},
            {name: 'porcentajeDescuento',    mapping: 'porcentajeDescuento'},
            {name: 'descripcionSolicitud',   mapping: 'descripcionSolicitud'},
            {name: 'login',                  mapping: 'login'},
            {name: 'loginVendedor',          mapping: 'loginVendedor'},
            {name: 'intIdServicio',          mapping: 'intIdServicio'},
            {name: 'strDescripcionServicio', mapping: 'strDescripcionServicio'}
        ],
        idProperty: 'id'
    });
    
    storeGetSolicitudesClientes = new Ext.data.Store
    ({
        model: 'modelDetalleSolicitud',
        pageSize: 20,
        total: 'total',
        proxy: 
        {
            type: 'ajax',
            url: strUrlGridSolicitudesClientes,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'registros'
            },
            extraParams: {
                strEstadoSolicitud : 'Pendiente',
                intTipoSolicitudCliente: 0,
                strCambioMasivoVendedor: 'N',
                strCaracteristicaSolicitud: ''
            }
        },
        listeners: {
            beforeload: function (store, operation, eOpts) {
                store.proxy.extraParams.intTipoSolicitudCliente = Ext.getCmp('cmbTipoSolicitudCliente').getValue();
                store.proxy.extraParams.strCambioMasivoVendedor = Ext.String.trim(Ext.getCmp('cmbTipoSolicitudCliente').
                    getRawValue()).toUpperCase().localeCompare(strDescripcionSolicitudMasivo) == 0
                        ? 'S'
                        : 'N';
                store.proxy.extraParams.strCaracteristicaSolicitud = Ext.String.trim(Ext.getCmp('cmbTipoSolicitudCliente').
                    getRawValue()).toUpperCase().localeCompare(strDescripcionSolicitudMasivo) == 0
                        ? strDescSolicitudVendedorOrigen
                        : '';
            },
            load: function (store, records, successful, eOpts) {
                Ext.Array.each(gridDetalleSolicitudes.columns, function(column, index){
                    switch (column.dataIndex) {
                        case 'login':
                        case 'strDescripcionServicio':
                        case 'precioDescuento':
                        case 'porcentajeDescuento':
                            column.setVisible(store.proxy.extraParams.strCambioMasivoVendedor != 'S')
                            break;
                        case 'acciones':
                            column.setVisible(store.proxy.extraParams.strCambioMasivoVendedor == 'S')
                            break;
                        case 'observacion':
                            column.flex = (store.proxy.extraParams.strCambioMasivoVendedor == 'S') ? 5 : 2;
                            break;
                        default:
                            break;
                    }
                });
                gridDetalleSolicitudes.doLayout();
            }
        }
    });
    
    Ext.define('modelMotivoRechazo',
    {
        extend: 'Ext.data.Model',
        fields:
        [
            {name: 'intIdMotivo', type: 'integer'},
            {name: 'strMotivo',   type: 'string'}
        ]
    });

    var storeMotivoRechazo = Ext.create('Ext.data.Store',
    {
        autoLoad: true,
        model: "modelMotivoRechazo",
        proxy:
        {
            type: 'ajax',
            url: strUrlGetMotivosRechazo,
            reader:
            {
                type: 'json',
                root: 'encontrados'
            }
        }
    });
    
    var cmbMotivoRechazo = new Ext.form.ComboBox
    ({
        xtype: 'combobox',
        store: storeMotivoRechazo,
        labelAlign: 'left',
        id: 'cmbMotivoRechazo',
        name: 'cmbMotivoRechazo',
        valueField: 'intIdMotivo',
        displayField: 'strMotivo',
        fieldLabel: 'Motivo Rechazo',
        width: 400,
        margin: 4,
        triggerAction: 'all',
        selectOnFocus: true,
        lastQuery: '',
        mode: 'local',
        allowBlank: true
    });

    Ext.define('ModelRazonSocial', {
        extend: 'Ext.data.Model',
        fields: [
            { name: 'intIdPersona', type: 'integer', mapping: 'intIdPersona' },
            { name: 'intIdPersonaEmpresaRol', type: 'integer', mapping: 'intIdPersonaEmpresaRol' },
            { name: 'strRazonSocial', type: 'string', mapping: 'strRazonSocial' },
            { name: 'strIdentificacion', type: 'string', mapping: 'strIdentificacion' }]
    });

    var objStoreRazonSocial = Ext.create('Ext.data.Store', {
        model: 'ModelRazonSocial',
        id: 'idStoreRazonSocial',
        pageSize: 10000,
        autoLoad: false,
        proxy:
        {
            type: 'ajax',
            url: strUrlClientesPorSolicitud,
            timeout: 60000,
            reader:
            {
                type: 'json',
                root: 'registros',
                totalProperty: 'total'
            },
            extraParams:
            {
                intIdSolicitud: 0,
            },
            simpleSortMode: true,
        }
    });

    gridDetalleSolicitudes = Ext.create('Ext.grid.Panel',
    {
        id: 'gridDetalleSolicitudes',
        width: 'auto',
        height: 500,
        store: storeGetSolicitudesClientes,
        loadMask: true,
        selModel: selectionDetalleSolicitudesClientes,
        iconCls: 'icon-grid',
        layout: 'fit',
        dockedItems:
        [
            {
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items:
                [
                    cmbMotivoRechazo,
                    {
                        xtype: 'tbfill'
                    },
                    {
                        itemId: 'btnRechazar',
                        text: 'Rechazar',
                        tooltip: 'Rechazar las solicitudes',
                        iconCls: 'icon_rechazar',
                        scope: this,
                        disabled: true,
                        handler: function()
                        {
                            aprobarRechazarSolicitudes('rechazar');
                        }
                    },
                    {
                        itemId: 'btnAprobar',
                        text: 'Aprobar',
                        scope: this,
                        tooltip: 'Aprobar las solicitudes',
                        iconCls: 'icon_aprobar',
                        disabled: true,
                        handler: function()
                        {
                            aprobarRechazarSolicitudes('aprobar');
                        }
                    }
                ]
            }
        ],
        columns: 
        [
            new Ext.grid.RowNumberer(
            {
                align: 'center',
            }),
            {
                header: 'id',
                dataIndex: 'id',
                hidden: true,
                hideable: false,
                align: 'center',
                flex: 0.4,

            },
            {
                header: 'Observación',
                dataIndex: 'observacion',
                sortable: true,
                hidden: false,
                flex: 2,
            },
            {
                header: 'Login',
                dataIndex: 'login',
                sortable: true,
                hidden: false,
                flex: 1,
            },
            {
                header: 'Servicio',
                dataIndex: 'strDescripcionServicio',
                sortable: true,
                hidden: false,
                flex: 2,
            },
            {
                header: 'Descuento',
                dataIndex: 'precioDescuento',
                sortable: true,
                hidden: false,
                flex: 1,
            },
            {
                header: 'Porcentaje<br/>Descuento',
                dataIndex: 'porcentajeDescuento',
                sortable: true,
                hidden: false,
                flex: 1,
            },
            {
                header: 'Fecha<br/>Creación',
                dataIndex: 'feCreacion',
                sortable: true,
                hidden: false,
                flex: 1,
            },
            {
                header: 'Usuario<br/>Creación',
                dataIndex: 'usrCreacion',
                sortable: true,
                hidden: false,
                flex: 1,
            },
            {
                header: 'Estado',
                dataIndex: 'estado',
                sortable: true,
                hidden: false,
                flex: 1,
            },
            {
                xtype: 'actioncolumn',
                dataIndex: 'acciones',
                header: 'Acciones',
                align: 'center',
                hidden: true,
                flex: 0.5,
                items: [
                {
                    tooltip: 'Ver clientes',
                    getClass: function(v, meta, rec) {
                        if (storeGetSolicitudesClientes.proxy.extraParams.strCambioMasivoVendedor == 'S') {
                            return 'x-btn button-grid-show button-point';
                        }
                        return 'none';
                    },
                    handler: function(grid, rowIndex, colIndex) {
                        if (storeGetSolicitudesClientes.proxy.extraParams.strCambioMasivoVendedor == 'S') {
                            if(!objModalPanelListaClientes.isVisible()){
                                objStoreRazonSocial.removeAll();
                                objModalPanelListaClientes.show();

                                Ext.data.StoreManager.get('idStoreRazonSocial').proxy.extraParams.intIdSolicitud =
                                    !Ext.isEmpty(storeGetSolicitudesClientes.getAt(rowIndex))
                                        ? storeGetSolicitudesClientes.getAt(rowIndex).get('id')
                                        : 0;
                                Ext.data.StoreManager.get('idStoreRazonSocial').load();
                            }
                        }
                    }
                }]
            }
        ],
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
                    target: view.el,
                    delegate: '.x-grid-cell',
                    trackMouse: true,
                    autoHide: false,
                    renderTo: Ext.getBody(),
                    listeners:
                    {
                        beforeshow: function(tip)
                        {
                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1)
                            {
                                header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                
                                if( header.dataIndex != null )
                                {
                                    var trigger         = tip.triggerElement,
                                        parent          = tip.triggerElement.parentElement,
                                        columnTitle     = view.getHeaderByCell(trigger).text,
                                        columnDataIndex = view.getHeaderByCell(trigger).dataIndex;

                                    if( view.getRecord(parent).get(columnDataIndex) != null )
                                    {
                                        var columnText      = view.getRecord(parent).get(columnDataIndex).toString();
                                        
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
                
                grid.tip.on('show', function()
                {
                    var timeout;

                    grid.tip.getEl().on('mouseout', function()
                    {
                        timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                    });

                    grid.tip.getEl().on('mouseover', function(){window.clearTimeout(timeout);});

                    Ext.get(view.el).on('mouseover', function(){window.clearTimeout(timeout);});

                    Ext.get(view.el).on('mouseout', function()
                    {
                        timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                    });
                });
            }
        },
        title: 'Solicitudes del Cliente',
        bbar: Ext.create('Ext.PagingToolbar',
        {
            store: storeGetSolicitudesClientes,
            displayInfo: true,
            displayMsg: 'Desde {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'grid'
    });

    var objGridListaClientes = Ext.create('Ext.grid.Panel', {
        id: 'idGridListaClientes',
        width: 'auto',
        layout: 'fit',
        flex: 1,
        store: objStoreRazonSocial,
        bbar: Ext.create('Ext.PagingToolbar', {
            store: objStoreRazonSocial,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos para mostrar"
        }),
        viewConfig: {
            emptyText: 'No hay datos para mostrar'
        },
        columns: [
            new Ext.grid.RowNumberer({
                header: '#',
                flex: 0.25,
                align: 'center',
            }),
            {
                header: 'Razón Social',
                flex: 3,
                dataIndex: 'strRazonSocial'
            },
            {
                header: 'Identificación',
                flex: 1,
                dataIndex: 'strIdentificacion'
            }
        ]
    });

    var objModalPanelListaClientes = Ext.create('Ext.window.Window', {
        title: 'Clientes a reasignar',
        id: 'idModalPanelListaClientes',
        floating: true,
        border: false,
        frame: false,
        height: 400,
        width: 700,
        modal: true,
        resizable: false,
        closeAction : 'hide',
        bodyStyle: 'background-color: #FFFFFF',
        layout: {
            type: 'vbox',
            align: 'stretch',
        },
        items: [objGridListaClientes]
    });




    /* ******************************************* */
    /* *********** FILTROS DE BUSQUEDA *********** */
    /* ******************************************* */
    var dateFechaCreacionDesde = new Ext.form.DateField
    ({
        id: 'dateFechaCreacionDesde',
        fieldLabel: 'Fecha Creación Desde',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 400,
        labelWidth: 125,
        editable: false
    });

    var dateFechaCreacionHasta = new Ext.form.DateField
    ({
        id: 'dateFechaCreacionHasta',
        fieldLabel: 'Fecha Creación Hasta',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 400,
        labelWidth: 125,
        editable: false
    });

    storeGetTipoSolicitudesClientes  = new Ext.data.Store
    ({
        total: 'total',
        autoLoad: true,
        proxy:
        {
            type: 'ajax',
            url : strUrlGetTipoSolicitudesClientes,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
        [
            {name:'valor2',      mapping:'valor2'},//Corresponde al código del tipo de solicitud del cliente
            {name:'descripcion', mapping:'descripcion'}//Corresponde al nombre del tipo de solicitud
        ],
        listeners: {
            load: function(store, operation, eOpts){
                Ext.getCmp('cmbTipoSolicitudCliente').setValue(store.first())
                storeGetSolicitudesClientes.proxy.extraParams.intTipoSolicitudCliente = Ext.getCmp('cmbTipoSolicitudCliente').getValue();
                storeGetSolicitudesClientes.load();
            }
        }
    });
    
    var filterPanel = Ext.create('Ext.panel.Panel',
    {
        bodyPadding: 7,
        buttonAlign: 'center',
        layout: 'fit',
        border: true,
        bodyStyle:
        {
            background: '#fff'
        },
        buttons:
        [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function()
                { 
                    buscar();
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function()
                { 
                    limpiar();
                }
            }

        ],   
        width: 'auto',
        title: 'Criterios de Búsqueda',
        collapsible : true,
        collapsed: false,
        items: 
        [
            {
                xtype:'fieldset',
                width: '80%',
                title: 'General',
                collapsible: false,
                layout:
                {
                    type: 'table',
                    columns: 3,
                    align: 'left',
                    tdAttrs: {
                    },
                },
                items:
                [
                    {
                        xtype: 'combobox',
                        id: 'cmbTipoSolicitudCliente',
                        fieldLabel: 'Tipo de Solicitud',
                        typeAhead: true,
                        triggerAction: 'all',
                        displayField:'descripcion',
                        valueField: 'valor2',
                        selectOnTab: true,
                        editable: false,
                        queryMode: 'local',
                        store: storeGetTipoSolicitudesClientes,
                        width: 400,
                        colspan: 3,
                        labelWidth: 125,
                        blankText: 'Cargando...',
                        listeners: {
                            select: function(combobox, records, eOpts)
                            {
                                storeGetSolicitudesClientes.load();
                            }
                        }

                    },
                    dateFechaCreacionDesde,
                    {
                        xtype: 'tbspacer',
                        width: 100,
                        colspan: 1
                    },
                    dateFechaCreacionHasta,
                ]
            }
        ],
        renderTo: 'filtroBusqueda'
    }); 
 
});

function aprobarRechazarSolicitudes(strAccion)
{
    var strSolicitudesSeleccionadas = null;
    var arrayGridSolicitudes        = gridDetalleSolicitudes.getSelectionModel().getSelection();
    var intIdMotivoRechazo          = null;
    var boolContinuar               = true;
    
    if( strAccion == "rechazar" )
    {
        if( Ext.isEmpty(Ext.getCmp('cmbMotivoRechazo').value) )
        {
            Ext.Msg.alert('Atención', 'Por favor seleccionar un motivo de rechazo para la solicitudes.');

            boolContinuar = false;
        }//( Ext.isEmpty(Ext.getCmp('idvendedor').value) )
        else
        {
            intIdMotivoRechazo = Ext.getCmp('cmbMotivoRechazo').value;
        }
    }//( strAccion == "rechazar" )
    
    if( boolContinuar )
    {
        for( var i = 0; i < arrayGridSolicitudes.length; i++ )
        {
            var objSolicitudSelected = arrayGridSolicitudes[i];

            strSolicitudesSeleccionadas = strSolicitudesSeleccionadas + objSolicitudSelected.get('id');

            if( i < (arrayGridSolicitudes.length -1) )
            {
                strSolicitudesSeleccionadas = strSolicitudesSeleccionadas + '|';
            }//( i < (arrayGridSolicitudes.length -1) )
        }//for( var i = 0; i < arrayGridSolicitudes.length; i++ )
        
        if( !Ext.isEmpty(strSolicitudesSeleccionadas) )
        {
            Ext.Msg.confirm('Alerta', 'Se ' + strAccion + 'án las solicitudes seleccionadas. Desea continuar?', function(btn)
            {
                if( btn == 'yes' )
                {
                    connEsperaAccion.request
                    ({
                        url: (Ext.String.trim(Ext.getCmp('cmbTipoSolicitudCliente').getRawValue()).toUpperCase()
                            .localeCompare(strDescripcionSolicitudMasivo) == 0)
                                ? strUrlAprobarRechazarSolicitudesClientesMasivo
                                : strUrlAprobarRechazarSolicitudesClientes,
                        method: 'post',
                        params:
                        {
                            strAccion: strAccion,
                            strSolicitudesSeleccionadas: strSolicitudesSeleccionadas,
                            intIdMotivoRechazo: intIdMotivoRechazo
                        },
                        success: function(response)
                        {
                            var objJsonResponse = Ext.JSON.decode(response.responseText);

                            if( !Ext.isEmpty(objJsonResponse.strMensajeConfirmacion) )
                            {
                                Ext.Msg.alert('Información', objJsonResponse.strMensajeConfirmacion);
                                
                                storeGetSolicitudesClientes.loadData([],false);
                                storeGetSolicitudesClientes.currentPage = 1;
                                storeGetSolicitudesClientes.load();
                            }
                            else
                            {
                                Ext.Msg.alert('Atención', objJsonResponse.strMensajeError);
                            }
                        },
                        failure: function(result)
                        {
                            Ext.Msg.alert('Error ', 'Se presentaron errores al realizar acciones sobre las solicitudes seleccionadas.');
                        }
                    });
                }//( btn == 'yes' )
            });//Ext.Msg.confirm('Alerta', 'Se ' + strAccion + 'án las solicitudes seleccionadas. Desea continuar?', function(btn)
        }
        else
        {
            Ext.Msg.alert('Atención', 'No ha seleccionado ninguna solicitud para ' + strAccion + '.');
        }//( !Ext.isEmpty(strSolicitudesSeleccionadas) )
    }//( boolContinuar )
}


function buscar()
{
    var intTipoSolicitudCliente = Ext.getCmp('cmbTipoSolicitudCliente').getValue();
    var strFechaCreacionDesde   = "";
    var strFechaCreacionHasta   = "";

    if( Ext.isEmpty(intTipoSolicitudCliente) )
    {
        Ext.Msg.alert('Alerta','Debe seleccionar un tipo de solicitud de cliente a buscar');
        return false;
    }
    
    if( !Ext.isEmpty(Ext.getCmp('dateFechaCreacionDesde').getValue()) )
    {
        strFechaCreacionDesde  = Ext.util.Format.date(Ext.getCmp('dateFechaCreacionDesde').getValue(), 'd-m-Y');
    }

    if( !Ext.isEmpty(Ext.getCmp('dateFechaCreacionHasta').getValue()) )
    {
        strFechaCreacionHasta  = Ext.util.Format.date(Ext.getCmp('dateFechaCreacionHasta').getValue(), 'd-m-Y');
    }

    storeGetSolicitudesClientes.loadData([],false);
    storeGetSolicitudesClientes.currentPage = 1;
    storeGetSolicitudesClientes.getProxy().extraParams.intTipoSolicitudCliente = intTipoSolicitudCliente;
    storeGetSolicitudesClientes.getProxy().extraParams.strFechaCreacionDesde   = strFechaCreacionDesde;
    storeGetSolicitudesClientes.getProxy().extraParams.strFechaCreacionHasta   = strFechaCreacionHasta;
    storeGetSolicitudesClientes.load();
}


function limpiar()
{
    var intTipoSolicitudCliente = Ext.getCmp('cmbTipoSolicitudCliente').getValue();
    
    if( Ext.isEmpty(intTipoSolicitudCliente) )
    {
        Ext.Msg.alert('Alerta','Debe seleccionar un tipo de solicitud de cliente');
        return false;
    }
    
    Ext.getCmp('dateFechaCreacionDesde').value="";
    Ext.getCmp('dateFechaCreacionDesde').setRawValue("");
    Ext.getCmp('dateFechaCreacionHasta').value="";
    Ext.getCmp('dateFechaCreacionHasta').setRawValue("");

    storeGetSolicitudesClientes.loadData([],false);
    storeGetSolicitudesClientes.currentPage = 1;
    storeGetSolicitudesClientes.getProxy().extraParams.intTipoSolicitudCliente = intTipoSolicitudCliente;
    storeGetSolicitudesClientes.getProxy().extraParams.strFechaCreacionDesde   = Ext.getCmp('dateFechaCreacionDesde').value;
    storeGetSolicitudesClientes.getProxy().extraParams.strFechaCreacionHasta   = Ext.getCmp('dateFechaCreacionHasta').value;
    storeGetSolicitudesClientes.load();
}
 
