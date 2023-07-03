Ext.require
([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);

var itemsPerPage = 10;

Ext.onReady(function () 
{
    //CREAMOS DATA STORE PARA ESTADOS
    Ext.define('modelEstado', 
    {
        extend: 'Ext.data.Model',
        fields: 
        [
            {name: 'idestado',      type: 'string'},
            {name: 'codigo',        type: 'string'},
            {name: 'descripcion',   type: 'string'}
        ]
    });
    
    var estado_store = Ext.create('Ext.data.Store',
    {
        autoLoad: false,
        model: "modelEstado",
        proxy: 
        {
            type: 'ajax',
            url: strUrlGetEstadosDocumentos,
            timeout: 9000000,
            reader:
            {
                type: 'json',
                root: 'estados'
            }
        }
    });
    
    var estado_cmb = new Ext.form.ComboBox
    ({
        xtype: 'combobox',
        store: estado_store,
        id: 'idestado',
        name: 'idestado',
        valueField: 'descripcion',
        displayField: 'descripcion',
        fieldLabel: 'Estado',
        width: 350,
        labelAlign : 'right',
        labelWidth: 110,
        labelPad: 10,
    });


    Ext.define('ListaDetalleModel', 
    {
        extend: 'Ext.data.Model',
        fields: 
        [
            {name: 'Numerofacturasri',      type: 'string'},
            {name: 'Punto',                 type: 'string'},
            {name: 'Cliente',               type: 'string'},
            {name: 'Esautomatica',          type: 'string'},
            {name: 'Feemision',             type: 'string'},
            {name: 'Total',                 type: 'string'},
            {name: 'Fecreacion',            type: 'string'},
            {name: 'strFeAutorizacion',     type: 'string'},
            {name: 'Estado',                type: 'string'},
            {name: 'linkVer',               type: 'string'},
            {name: 'id',                    type: 'int'},
            {name: 'strCodigoDocumento',    type: 'string'},
            {name: 'intIdTipoDocumento',    type: 'int'},
            {name: 'empresa',               type: 'string'},
            {name: 'strEsElectronica',      type: 'string'},
            {name: 'negocio',               type: 'string'}
        ]
    });


    store = Ext.create('Ext.data.JsonStore',
    {
        model: 'ListaDetalleModel',
        pageSize: itemsPerPage,
        proxy: 
        {
            type: 'ajax',
            timeout: 9000000,
            url: strUrlGridDocumentosFinancieros,
            reader: 
            {
                type: 'json',
                root: 'documentos',
                totalProperty: 'total'
            },
            simpleSortMode: true
        },
        listeners: 
        {
            beforeload: function (store) 
            {
                store.getProxy().extraParams.fechaDesdeCreacion = Ext.getCmp('fechaDesdeCreacion').getValue();
                store.getProxy().extraParams.fechaHastaCreacion = Ext.getCmp('fechaHastaCreacion').getValue();
                store.getProxy().extraParams.fechaDesdeEmision  = Ext.getCmp('fechaDesdeEmision').getValue();
                store.getProxy().extraParams.fechaHastaEmision  = Ext.getCmp('fechaHastaEmision').getValue();
                store.getProxy().extraParams.strNumeroDocumento = Ext.getCmp('strNumeroDocumento').getValue();
                store.getProxy().extraParams.strTipoDocumento   = Ext.getCmp('cmbTipoDocumentoFinanciero').getValue();
                store.getProxy().extraParams.estado             = Ext.getCmp('idestado').getValue();
            }
        }
    });
    
    
    Ext.define('ListModelTipoDocumentoFinanciero',
    {
        extend: 'Ext.data.Model',
        fields: 
        [
            {name: 'codigo_tipo_documento', type: 'string'},
            {name: 'nombre_tipo_documento', type: 'string'}
        ]
    });

    storeTipoDocumentoFinanciero = Ext.create('Ext.data.Store',
    {
        model: 'ListModelTipoDocumentoFinanciero',
        autoLoad: true,
        proxy:
        {
            type: 'ajax',
            url: strUrlGetTipoDocumentoFinanciero,
            timeout: 9000000,
            reader:
            {
                type: 'json',
                root: 'encontrados'
            }
        }
    });

    var cmbTipoDocumentoFinanciero = new Ext.form.ComboBox
    ({
        xtype: 'combobox',
        store: storeTipoDocumentoFinanciero,
        id: 'cmbTipoDocumentoFinanciero',
        name: 'cmbTipoDocumentoFinanciero',
        valueField: 'codigo_tipo_documento',
        displayField: 'nombre_tipo_documento',
        fieldLabel: 'Tipo Documento',
        width: 350,
        labelAlign : 'right',
        labelWidth: 110,
        labelPad: 10,
        emptyText: 'Seleccione',
        editable: false
    });
    

    var listView = Ext.create('Ext.grid.Panel', 
    {
        width: 1000,
        height: 275,
        collapsible: false,
        title: '',
        renderTo: Ext.get('gridDocumentosFinancieros'),
        bbar: Ext.create('Ext.PagingToolbar',
        {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando documentos {0} - {1} of {2}',
            emptyMsg: "No hay datos para mostrar"
        }),
        store: store,
        multiSelect: false,
        viewConfig: 
        {
            stripeRows: true,
            enableTextSelection: true,
            emptyText: 'No hay datos para mostrar'
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
        columns: 
        [
            new Ext.grid.RowNumberer(),
            {
                text: 'Id Documento Financiero',
                width: 130,
                dataIndex: 'id',
                hidden: true
            }, 
            {
                text: 'T. Doc',
                width: 50,
                dataIndex: 'strCodigoDocumento'
            },
            {
                text: 'N° Documento SRI',
                width: 110,
                dataIndex: 'Numerofacturasri'
            }, 
            {
                text: 'Pto cliente',
                width: 100,
                dataIndex: 'Punto'
            },
            {
                text: 'Cliente',
                width: 215,
                dataIndex: 'Cliente'
            }, 
            {
                text: 'Auto?',
                dataIndex: 'Esautomatica',
                align: 'right',
                width: 50
            }, 
            {
                text: 'Elec?',
                dataIndex: 'strEsElectronica',
                align: 'right',
                width: 50
            }, 
            {
                text: 'Estado',
                dataIndex: 'Estado',
                align: 'right',
                width: 70
            },
            {
                text: 'F. Emision',
                dataIndex: 'Feemision',
                align: 'right',
                width: 100
            },
            {
                text: 'F. Creacion',
                dataIndex: 'Fecreacion',
                align: 'right',
                width: 100
            }, 
            {
                text: 'F. Autorizacion',
                dataIndex: 'strFeAutorizacion',
                align: 'right',
                width: 100
            },     
            {
                text: 'Total',
                dataIndex: 'Total',
                align: 'right',
                width: 70
            },
            {
                text: 'Acciones',
                width: 60,
                renderer: renderAcciones
            }
        ]
    });


    function renderAcciones(value, p, record)
    {
        var iconos = '';
        iconos     = iconos + '<b><a href="' + record.data.linkVer + '" target="_blank" title="Ver" class="button-grid-show"></a></b>';
        
        return Ext.String.format(iconos, value, '1', 'nada');
    }

    var filterPanel = Ext.create('Ext.panel.Panel', 
    {
        bodyPadding: 7,
        border: false,
        buttonAlign: 'center',
        layout: 
        {
            type: 'table',
            columns: 5,
            align: 'left',
        },
        bodyStyle:
        {
            background: '#fff'
        },
        collapsible: true,
        collapsed: true,
        width: 1000,
        title: 'Criterios de busqueda',
        buttons: 
        [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: Buscar,
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function ()
                {
                    limpiar();
                }
            }
        ],
        items:
        [
            {html: "&nbsp;", border: false, width: 50},
            {
                xtype: 'fieldcontainer',
                fieldLabel: 'Fecha Creación',
                labelWidth: 110,
                labelPad: 10,
                labelAlign : 'right',
                items: 
                [
                    {
                        xtype: 'datefield',
                        width: 230,
                        id: 'fechaDesdeCreacion',
                        name: 'fechaDesdeCreacion',
                        fieldLabel: 'Desde:',
                        labelWidth: 50,
                        labelPad: 10,
                        labelAlign : 'right',
                        format: 'Y-m-d',
                        editable: false
                    },
                    {
                        xtype: 'datefield',
                        width: 230,
                        id: 'fechaHastaCreacion',
                        name: 'fechaHastaCreacion',
                        fieldLabel: 'Hasta:',
                        labelWidth: 50,
                        labelPad: 10,
                        labelAlign : 'right',
                        format: 'Y-m-d',
                        editable: false
                    }
                ]
            },
            {html: "&nbsp;", border: false, width: 50},
            {
                xtype: 'fieldcontainer',
                fieldLabel: 'Fecha Emisión',
                labelWidth: 110,
                labelPad: 10,
                labelAlign : 'right',
                items: 
                [
                    {
                        xtype: 'datefield',
                        width: 230,
                        id: 'fechaDesdeEmision',
                        name: 'fechaDesdeEmision',
                        fieldLabel: 'Desde:',
                        labelWidth: 50,
                        labelPad: 10,
                        labelAlign : 'right',
                        format: 'Y-m-d',
                        editable: false
                    },
                    {
                        xtype: 'datefield',
                        width: 230,
                        id: 'fechaHastaEmision',
                        name: 'fechaHastaEmision',
                        fieldLabel: 'Hasta:',
                        labelWidth: 50,
                        labelPad: 10,
                        labelAlign : 'right',
                        format: 'Y-m-d',
                        editable: false
                    }
                ]
            },
            {html: "&nbsp;", border: false, width: 50},
            {html: "&nbsp;", border: false, width: 50},
            {
                xtype: 'textfield',
                id: 'strNumeroDocumento',
                name: 'strNumeroDocumento',
                fieldLabel: 'N° Documento Financiero',
                value: '',
                labelWidth: 110,
                labelPad: 10,
                labelAlign : 'right',
                width: 350
            },
            {html: "&nbsp;", border: false, width: 50},
            cmbTipoDocumentoFinanciero,
            {html: "&nbsp;", border: false, width: 50},
            {html: "&nbsp;", border: false, width: 50},
            estado_cmb,
            {html: "&nbsp;", border: false, width: 50},
            {html: "&nbsp;", border: false, width: 50},
            {html: "&nbsp;", border: false, width: 50},
        ],
        renderTo: 'filtroDocumentosFinancieros'
    });

    if(intIdPunto)
    {
        store.load({params: {start: 0, limit: 10}});
    }
});

function Buscar()
{
    store.loadData([],false);
    store.currentPage = 1;
    store.load();
}

function limpiar() 
{
    Ext.getCmp('fechaDesdeEmision').setRawValue("");
    Ext.getCmp('fechaHastaEmision').setRawValue("");
    Ext.getCmp('fechaDesdeCreacion').setRawValue("");
    Ext.getCmp('fechaHastaCreacion').setRawValue("");
    Ext.getCmp('strNumeroDocumento').setRawValue("");
    Ext.getCmp('cmbTipoDocumentoFinanciero').setRawValue("");
    Ext.getCmp('idestado').setRawValue("");
    
    Buscar();
}
