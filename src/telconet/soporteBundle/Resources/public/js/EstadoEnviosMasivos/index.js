Ext.require([
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();
    var storePlantillasCorreo = Ext.create('Ext.data.Store', {
        id: 'storePlantillas',
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: strUrlGetPlantillasEnvioMasivo,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'idPlantilla', mapping: 'id_plantilla'},
                {name: 'nombrePlantilla', mapping: 'nombre_plantilla'}
            ]
    });

    comboPlantillasCorreo = new Ext.form.ComboBox({
        id: 'cmbPlantillaEmail',
        name: 'cmbPlantillaEmail',
        fieldLabel: "Plantillas Correo",
        anchor: '100%',
        queryMode: 'remote',
        width: 350,
        padding: 4,
        emptyText: 'Seleccione Plantilla Correo',
        store: storePlantillasCorreo,
        displayField: 'nombrePlantilla',
        valueField: 'idPlantilla',
        layout: 'anchor',
        disabled: false,
        forceSelection: true,
        allowBlank: false
    });

    itemsBusqueda = [
        {html: "&nbsp;", border: false, width: 150},
        comboPlantillasCorreo,
        {html: "&nbsp;", border: false, width: 250},
        {
            xtype: 'combobox',
            fieldLabel: 'Tipo de Envío',
            id: 'cmbTipoEnvio',
            name: 'cmbTipoEnvio',
            store: [
                ['INMEDIATO', 'Inmediato'],
                ['PROGRAMADO', 'Programado'],
                ['RECURRENTE', 'Recurrente']
            ]
        },
        {html: "&nbsp;", border: false, width: 150},
        {html: "&nbsp;", border: false, width: 150},
        {
            xtype: 'combobox',
            fieldLabel: 'Estado',
            id: 'cmbEstado',
            name: 'cmbEstado',
            store: [
                ['Pendiente', 'Pendiente'],
                ['Configurado', 'Configurado'],
                ['Finalizado', 'Finalizado']
            ]
        },
        {html: "&nbsp;", border: false, width: 250},
        {html: "&nbsp;", border: false, width: 150},
        {html: "&nbsp;", border: false, width: 150}
    ];


    var filtrosBusqueda = Ext.create('Ext.panel.Panel', {
        id: 'filtrosBusqueda',
        bodyPadding: 5,
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
        collapsed: false,
        width: 1190,
        title: 'Criterios de búsqueda',
        buttons:
            [
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
        items: itemsBusqueda,
        renderTo: 'divFiltrosBusqueda'
    });
    Ext.define('ModelStore',
        {
            extend: 'Ext.data.Model',
            fields:
                [
                    {name: 'intIdNotifMasiva', mapping: 'intIdNotifMasiva'},
                    {name: 'strNombrePlantilla', mapping: 'strNombrePlantilla'},
                    {name: 'strTipoEnvio', mapping: 'strTipoEnvio'},
                    {name: 'strTiposContactos', mapping: 'strTiposContactos'},
                    {name: 'strInfoGeneral', mapping: 'strInfoGeneral'},
                    {name: 'strUsrCreacion', mapping: 'strUsrCreacion'},
                    {name: 'strEstado', mapping: 'strEstado'},
                    {name: 'strAccionEliminar', mapping: 'strAccionEliminar'}
                ],
            idProperty: 'intIdNotifMasiva'
        });

    storePrincipal = new Ext.data.Store
        ({
            pageSize: 10,
            model: 'ModelStore',
            total: 'intTotal',
            proxy: {
                type: 'ajax',
                timeout: 600000,
                url: strUrlGridNotifMasivas,
                reader:
                    {
                        type: 'json',
                        totalProperty: 'intTotal',
                        root: 'arrayResultado'
                    }
            },
            autoLoad: true
        });


    gridEstadosEnvioMasivo = Ext.create('Ext.grid.Panel', {
        width: '1190px',
        height: 450,
        id: 'gridEstadosEnvioMasivo',
        store: storePrincipal,
        frame: false,
        setVisible: false,
        viewConfig: {enableTextSelection: true},
        plugins: [{ptype: 'pagingselectpersist'}],
        viewConfig: {
            id: 'gv3',
            trackOver: true,
            stripeRows: true,
            loadMask: true
        },
        iconCls: 'icon-grid',
        columns: [
            {
                xtype: 'rownumberer',
                width: 50
            },
            {
                id: 'intIdNotifMasiva',
                header: 'intIdNotifMasiva',
                dataIndex: 'intIdNotifMasiva',
                hidden: true,
                hideable: false
            },
            {
                dataIndex: 'strNombrePlantilla',
                header: 'Nombre Notificacion',
                width: 250,
                sortable: true
            },
            {
                dataIndex: 'strTipoEnvio',
                header: 'Tipo Envio',
                width: 100,
                sortable: true
            },
            {
                dataIndex: 'strTiposContactos',
                header: 'Tipo Contacto',
                width: 200,
                sortable: true
            },
            {
                dataIndex: 'strInfoGeneral',
                header: 'Configuración Envío',
                width: 290,
                sortable: true
            },
            {
                dataIndex: 'strUsrCreacion',
                header: 'Usr. Creación',
                width: 100,
                sortable: true
            },
            {
                dataIndex: 'strEstado',
                header: 'Estado',
                width: 100,
                sortable: true
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 80,
                items:
                    [
                        {
                            getClass: function(v, meta, rec) {
                                var permiso = '{{ is_granted("ROLE_398-6") }}';
                                var strClassButton = 'btn-acciones button-grid-show';
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
                                if (!boolPermiso) {
                                    strClassButton = "icon-invisible";
                                }

                                return strClassButton;
                            },
                            tooltip: 'Ver Parámetros y Logs de Ejecución',
                            handler: function(grid, rowIndex, colIndex) {

                                var rec = storePrincipal.getAt(rowIndex);
                                var strClassButton = 'btn-acciones button-grid-show';
                                var permiso = '{{ is_granted("ROLE_398-6") }}';
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
                                if (!boolPermiso) {
                                    strClassButton = "icon-invisible";
                                }

                                if (strClassButton != "icon-invisible")
                                {
                                    window.location = rec.get('intIdNotifMasiva') + "/showInfoEnvioMasivo";
                                    console.log(rec.get('intIdNotifMasiva') + "/showInfoEnvioMasivo");
                                }
                                    
                                else
                                {
                                    Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                                }
                                    
                            }
                        },
                        {
                            getClass: function(v, meta, rec) {
                                var permiso = '{{ is_granted("ROLE_398-8") }}';
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
                                if (!boolPermiso) {
                                    rec.data.strAccionEliminar = "icon-invisible";
                                }
                                return rec.get('strAccionEliminar');
                            },
                            tooltip: 'Eliminar Envío Masivo',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = storePrincipal.getAt(rowIndex);
								var permiso = '{{ is_granted("ROLE_398-8") }}';
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
                                if (!boolPermiso) {
                                    rec.data.strAccionEliminar = "icon-invisible";
                                }
                                
                                if (rec.get('strAccionEliminar') != "icon-invisible")
                                {
                                    eliminarEnvioMasivo(rec.get('intIdNotifMasiva'));
                                }  
                                else
                                {
                                    Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                                }
                                    
                            }
                        }
                    ]
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storePrincipal,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'grid',
        listeners: {
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
            },
            viewready: function(grid) {
                var view = grid.view;

                grid.mon(view, {
                    uievent: function(type, view, cell, recordIndex, cellIndex, e) {
                        grid.cellIndex = cellIndex;
                        grid.recordIndex = recordIndex;
                    }
                });

                grid.tip = Ext.create('Ext.tip.ToolTip', {
                    target: view.el,
                    delegate: '.x-grid-cell',
                    trackMouse: true,
                    renderTo: Ext.getBody(),
                    listeners: {
                        beforeshow: function updateTipBody(tip) {
                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1) {
                                header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                tip.update(grid.getStore().getAt(grid.recordIndex).get(header.dataIndex));
                            }
                        }
                    }
        });

            }
        }
    });

});

function buscar() {
    extraParamsBusqueda = {
        intIdPlantilla: Ext.getCmp('cmbPlantillaEmail').value,
        strTipoEnvio: Ext.getCmp('cmbTipoEnvio').value,
        strEstado: Ext.getCmp('cmbEstado').value
    };
    storePrincipal.loadData([], false);
    storePrincipal.currentPage = 1;
    storePrincipal.getProxy().extraParams = extraParamsBusqueda;
    storePrincipal.load();
}


function limpiar() {
    extraParamsBusqueda = null;
    Ext.getCmp('cmbPlantillaEmail').value = "";
    Ext.getCmp('cmbPlantillaEmail').setRawValue("");
    Ext.getCmp('cmbTipoEnvio').value = "";
    Ext.getCmp('cmbTipoEnvio').setRawValue("");
    Ext.getCmp('cmbEstado').value = "";
    Ext.getCmp('cmbEstado').setRawValue("");

    storePrincipal.loadData([], false);
    storePrincipal.currentPage = 1;
}

function eliminarEnvioMasivo(intIdNotifMasiva)
{
    Ext.Ajax.request({
        url: strUrlEliminarEnvioMasivo,
        method: 'post',
        params: {
            intIdNotifMasiva: intIdNotifMasiva
        },
        success: function(response) {
            var json = Ext.JSON.decode(response.responseText);

            if (json.strStatus === "OK")
            {
                Ext.Msg.alert('Mensaje ', json.strMensaje);
                buscar();
            }
            else
            {
                Ext.Msg.alert('Error ', json.strMensaje);
            }
        }
    });
}
