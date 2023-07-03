Ext.require
([
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

var gridServicios      = null;
var storeGridServicios = null;
var connEsperaAccion   = new Ext.data.Connection
({
    listeners:
    {
        'beforerequest': 
        {
            fn: function (con, opt)
            {						
                Ext.MessageBox.show
                ({
                   msg: 'Validando la información...',
                   progressText: 'Saving...',
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

Ext.onReady(function()
{
    Ext.tip.QuickTipManager.init();
    
    Ext.define('ListModel',
        {
            extend: 'Ext.data.Model',
            fields:
                [
                    {name: 'id', type: 'int'},
                    {name: 'nombre', type: 'string'}
                ]
        });

    Ext.define('ListModelVendedor',
        {
            extend: 'Ext.data.Model',
            fields:
                [
                    {name: 'login', type: 'string'},
                    {name: 'nombre', type: 'string'}
                ]
        });

    storeVendedores = Ext.create('Ext.data.Store',
        {
            model: 'ListModelVendedor',
            autoLoad: true,
            proxy:
                {
                    type: 'ajax',
                    url: url_vendedores,
                    reader:
                        {
                            type: 'json',
                            root: 'registros'
                        }
                },
            listeners:
                {
                    load: function(store)
                    {
                        var combo1 = Ext.getCmp("idvendedor");
                        if (combo1)
                        {
                            store.each(function(record)
                            {
                                if (record.data.login == vendedor_default)
                                {
                                    combo1.setValue(record.data.login);
                                }
                            });
                        }
                    }
                }
        });

    new Ext.form.ComboBox(
        {
            xtype: 'combobox',
            store: storeVendedores,
            labelAlign: 'left',
            name: 'idvendedor',
            id: 'idvendedor',
            valueField: 'login',
            displayField: 'nombre',
            fieldLabel: '',
            width: 300,
            allowBlank: false,
            emptyText: 'Seleccione Vendedor',
            disabled: false,
            renderTo: 'combo_vendedor',
            matchFieldWidth: false,
            editable: false,
            listeners:
                {
                    select:
                        {
                            fn: function(combo)
                            {
                                $('#infopuntoextratype_loginVendedor').val(combo.getValue());
                            }
                        },
                    click:
                        {
                            element: 'el'
                        }
                }
        }
    );

    if( strVendedorPorServicio == "S" )
    {
        modelStoreServicios = Ext.create('Ext.selection.CheckboxModel',
        {
            checkOnly: true
        });

        Ext.define('ListaDetalleModel',
        {
            extend: 'Ext.data.Model',
            fields: 
            [
                {name: 'idServicio',                 type: 'int'},
                {name: 'tipo',                       type: 'string'},
                {name: 'descripcionPunto',           type: 'string'},
                {name: 'descripcionPresentaFactura', type: 'string'},
                {name: 'loginPadreFact',             type: 'string'},
                {name: 'descripcionProducto',        type: 'string'},
                {name: 'nombreTecnicoProducto',      type: 'string'},
                {name: 'cantidad',                   type: 'string'},
                {name: 'fechaCreacion',              type: 'string'},
                {name: 'estado',                     type: 'string'},
                {name: 'tipoOrden',                  type: 'string'},
                {name: 'esVenta',                    type: 'string'},
                {name: 'ultimaMilla',                type: 'string'},
                {name: 'frecuenciaProducto',         type: 'string'},
                {name: 'mesesRestantes',             type: 'string'},
                {name: 'loginAux',                   type: 'string'},
                {name: 'tipoEnlace',                 type: 'string'},
                {name: 'precioTotal',                type: 'string'},
                {name: 'anexoTecnico',               type: 'string'},
                {name: 'backup',                     type: 'string'},
                {name: 'nombre_vendedor',            type: 'string'},
                {name: 'boolGeneraSolicitudVendedor',type: 'boolean'}
            ],
            idProperty: 'idServicio'
        });

        storeGridServicios = Ext.create('Ext.data.JsonStore', 
        {
            model: 'ListaDetalleModel',
            pageSize: '15',
            proxy: 
            {
                type: 'ajax',
                url: strUrlGridServicios,
                timeout: 900000,
                reader: 
                {
                    type: 'json',
                    root: 'servicios',
                    totalProperty: 'total'
                },
                extraParams:
                {
                    strOpcion: 'CAMBIO_VENDEDOR'
                },
                simpleSortMode: true
            }
        });

        gridServicios = Ext.create('Ext.grid.Panel', 
        {
            id: 'gridPanelServicios',
            name: 'gridPanelServicios',
            width: 1100,
            height: 490,
            collapsible: false,
            title: '',
            layout:'fit',
            renderTo: Ext.get('gridServicios'),
            selModel: modelStoreServicios,
            bbar: Ext.create('Ext.PagingToolbar',
            {
                store: storeGridServicios,
                displayInfo: true,
                displayMsg: 'Mostrando servicios {0} - {1} of {2}',
                emptyMsg: "No hay datos para mostrar"
            }),
            plugins:[{ ptype : 'pagingselectpersist' }],
            store: storeGridServicios,
            multiSelect: false,
            viewConfig: 
            {
                emptyText: 'No hay datos para mostrar',
                stripeRows: true,
                enableTextSelection: true
            },
            dockedItems:
            [
                {
                    xtype: 'toolbar',
                    dock: 'top',
                    align: '->',
                    items:
                    [
                        {
                            iconCls: 'icon_add',
                            text: 'Seleccionar Todos',
                            itemId: 'select',
                            scope: this,
                            handler: function()
                            { 
                                Ext.getCmp('gridPanelServicios').getPlugin('pagingSelectionPersistence').selectAll();
                            }
                        },
                        {
                            iconCls: 'icon_limpiar',
                            text: 'Quitar selección Todos',
                            itemId: 'clear',
                            scope: this,
                            handler: function()
                            { 
                                Ext.getCmp('gridPanelServicios').getPlugin('pagingSelectionPersistence').clearPersistedSelection();
                            }
                        }
                    ]
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
            columns: 
            [
                new Ext.grid.RowNumberer(),
                {
                    text: 'Orden',
                    width: 45,
                    dataIndex: 'tipoOrden'
                },
                {
                    text: 'UM',
                    width: 40,
                    dataIndex: 'ultimaMilla',
                    align: 'center'
                },
                {
                    text: 'Vendedor',
                    width: 130,
                    dataIndex: 'nombre_vendedor',
                    align: 'center'
                },
                {
                    text: 'Cant.',
                    width: 35,
                    dataIndex: 'cantidad',
                    align: 'center'
                },
                {
                    text: 'Producto / Plan',
                    width: 140,
                    dataIndex: 'descripcionProducto'
                },
                {
                    text: 'P. Total',
                    dataIndex: 'precioTotal',
                    align: 'right',
                    width: 70
                },
                {
                    text: 'Venta',
                    dataIndex: 'esVenta',
                    align: 'center',
                    width: 40
                },
                {
                    text: 'Frec.',
                    dataIndex: 'frecuenciaProducto',
                    align: 'center',
                    width: 35
                },
                {
                    text: 'Creación',
                    dataIndex: 'fechaCreacion',
                    align: 'right',
                    width: 67,
                    renderer: function(value, metaData, record, colIndex, store, view) 
                    {
                        metaData.tdAttr = 'data-qtip="' + value + '"';
                        return value;
                    }
                },
                {
                    text: 'Estado',
                    dataIndex: 'estado',
                    align: 'center',
                    width: 83
                }
            ]
        });


        if( strPrefijoEmpresa == "TN" )
        {
            gridServicios.headerCt.insert(5,
            {
                text: 'Descripción Factura',
                width: 110,
                dataIndex: 'descripcionPresentaFactura'
            });

            gridServicios.headerCt.insert(6,
            {
                text: 'Login Aux',
                width: 60,
                dataIndex: 'loginAux'
            });

            gridServicios.headerCt.insert(7,
            {
                text: 'Padre Fact.',
                width: 70,
                dataIndex: 'loginPadreFact'
            });

            gridServicios.headerCt.insert(8,
            {
                text: 'Tipo Enlace',
                width: 70,
                dataIndex: 'tipoEnlace'
            });

            gridServicios.headerCt.insert(12,
            {
                text: 'Meses',
                width: 43,
                dataIndex: 'mesesRestantes'
            });

            gridServicios.getView().refresh();
        }
        else
        {
            gridServicios.headerCt.items.items[4].setWidth(250);
        }
        
        var filterPanelAsignaciones = Ext.create('Ext.panel.Panel',
        {
            bodyPadding: 10,
            border: false,
            buttonAlign: 'center',
            layout:
            {
                type: 'hbox',
                align: 'stretch'
            },
            bodyStyle:
            {
                background: '#fff'
            },
            collapsible: true,
            collapsed: true,
            width: 1100,
            title: 'Criterios de búsqueda',
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
            items: 
            [
                {width: '1%', border: false},
                {
                    xtype: 'combobox',
                    store: storeVendedores,
                    labelAlign: 'left',
                    name: 'cmbVendedorBusqueda',
                    id: 'cmbVendedorBusqueda',
                    valueField: 'login',
                    displayField: 'nombre',
                    fieldLabel: 'Vendedor a consultar',
                    width: 300,
                    allowBlank: false,
                    emptyText: 'Seleccione Vendedor',
                    disabled: false,
                    matchFieldWidth: false,
                    editable: false
                }
            ],
            renderTo: 'filtroServicios'
        });
    }//( strVendedorPorServicio == "S" )
});


function mostrarGridServicios()
{
    if( $('#strAsignarServicios').attr('checked') )
    {
        $('#gridServicios').removeClass('campo-oculto');
        $('#filtroServicios').removeClass('campo-oculto');
        gridServicios.getView().refresh();
        
        storeGridServicios.loadData([],false);
        storeGridServicios.currentPage = 1;
        storeGridServicios.load();
    }
    else
    {
        $('#gridServicios').addClass('campo-oculto');
        $('#filtroServicios').addClass('campo-oculto');
    }
}


function buscar()
{
    if( !Ext.isEmpty(Ext.getCmp('cmbVendedorBusqueda').value) )
    {
        storeGridServicios.loadData([],false);
        storeGridServicios.currentPage = 1;
        storeGridServicios.getProxy().extraParams.strLoginVendedor = Ext.getCmp('cmbVendedorBusqueda').value;
        storeGridServicios.load();
    }
    else
    {
        Ext.Msg.alert('Atención', 'Debe seleccionar un vendedor para realizar la búsqueda respectiva');
    } 
}


function limpiar()
{
    Ext.getCmp('cmbVendedorBusqueda').value = "";
    Ext.getCmp('cmbVendedorBusqueda').setRawValue("");

    storeGridServicios.loadData([],false);
    storeGridServicios.currentPage = 1;
    storeGridServicios.getProxy().extraParams.strLoginVendedor = Ext.getCmp('cmbVendedorBusqueda').value;
    storeGridServicios.load();
}


function validarFormulario()
{    
    if( $('#strAsignarServicios').attr('checked') )
    {
        var strServiciosSelected     = null;
        var intServiciosConSolicitud = 0;
        var strServiciosConSolicitud = '';
        var arrayGridServicios       = gridServicios.getSelectionModel().getSelection();
        var strMensajeAlerta         = 'Se actualizará el vendedor en los servicios seleccionados. ';
        
        if( Ext.isEmpty(Ext.getCmp('idvendedor').value) )
        {
            Ext.Msg.alert('Atención', 'Por favor seleccionar un vendedor para realizar la acción correspondiente.');

            return false;
        }//( Ext.isEmpty(Ext.getCmp('idvendedor').value) )

        for( var i = 0; i < arrayGridServicios.length; i++ )
        {
            var objServicioSelected = arrayGridServicios[i];

            strServiciosSelected = strServiciosSelected + objServicioSelected.get('idServicio');
            
            if( objServicioSelected.get('boolGeneraSolicitudVendedor') )
            {
                intServiciosConSolicitud++;
                strServiciosConSolicitud += '<b>Servicio #' + intServiciosConSolicitud + '</b><br/>'
                                            + 'Descripción Servicio: <b>' + objServicioSelected.get('descripcionPresentaFactura') + '</b><br/><br/>';
            }

            if( i < (arrayGridServicios.length -1) )
            {
                strServiciosSelected = strServiciosSelected + '|';
            }//( i < (arrayGridServicios.length -1) )
        }//for( var i = 0; i < arrayGridServicios.length; i++ )
        
        if( !Ext.isEmpty(strServiciosSelected) )
        {
            connEsperaAccion.request
            ({
                url: strUrlValidarSolicitudes,
                method: 'post',
                params:
                {
                    strServiciosSelected: strServiciosSelected,
                    strNombreSolicitud: 'SOLICITUD CAMBIO PERSONAL PLANTILLA',
                    strCaracteristicaSolicitud: 'CAMBIO_VENDEDOR'
                },
                success: function(response)
                {
                    if( response.responseText == "OK" )
                    {
                        if( intServiciosConSolicitud > 0 )
                        {
                            strMensajeAlerta = 'Se crearán las correspondientes solicitudes para la aprobación del cambio de vendedor a los ' +
                                               'siguientes servicios: <br/><br/>' + strServiciosConSolicitud;
                        }//( intServiciosConSolicitud > 0 )
                        
                        Ext.Msg.confirm('Alerta', strMensajeAlerta + 'Desea continuar?', function(btn)
                        {
                            if( btn == 'yes' )
                            {
                                $('#infopuntoextratype_strServiciosSelected').val(strServiciosSelected);

                                document.forms[0].submit();
                            }//( btn == 'yes' )
                        });//Ext.Msg.confirm...
                    }
                    else
                    {
                        Ext.Msg.alert('Atención', response.responseText);
                    }//( response.responseText == "OK" )
                },
                failure: function(result)
                {
                    Ext.Msg.alert('Error ', 'Se presentaron errores al validar las solicitudes creadas del servicio seleccionado.');
                }
            });
            
            return false;
        }
        else
        {
            Ext.Msg.alert('Atención', 'No ha seleccionado ningún servicio para actualizar la información del vendedor.');
            
            return false;
        }//( !Ext.isEmpty(strServiciosSelected) )
    }//( $('#strAsignarServicios').attr('checked') )
    else
    {
        return true;
    }
}

function validacionesForm()
{
    if ($('#infopuntoextratype_sectorId').val() == '')
    {
        mostrarDiv('div_errorsector');
        return false;
    }
    if ($('#infopuntoextratype_loginVendedor').val() == '')
    {
        mostrarDiv('div_errorvendedor');
        return false;
    }
    return true;
}

function setLogin(combo)
{
    $('#infopuntoextratype_loginVendedor').val(combo.getValue());
}