var storeEmpleadosDepartamento = null;
var storeEmpleadosAsignados    = null;
var modelStoreEmpDepartamento  = null;
var modelStoreEmpAsignados     = null;

Ext.require
([
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

var connEsperaAccion = new Ext.data.Connection
    ({
	listeners:
        {
            'beforerequest': 
            {
                fn: function (con, opt)
                {						
                    Ext.MessageBox.show
                    ({
                       msg: 'Grabando los datos, Por favor espere!!',
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
    
    Ext.define('modelGrupoProducto',
    {
        extend: 'Ext.data.Model',
        fields:
        [
            {name: 'nombre', type: 'string'}
        ]
    });

    var storeGruposProducto = Ext.create('Ext.data.Store',
    {
        autoLoad: true,
        model: "modelGrupoProducto",
        proxy:
        {
            type: 'ajax',
            url: strUrlGetGruposProducto,
            timeout: 9000000,
            reader:
            {
                type: 'json',
                root: 'nombres'
            }
        }
    });
            
    var cmbGrupoProducto = new Ext.form.ComboBox
    ({
        xtype: 'combobox',
        store: storeGruposProducto,
        labelAlign : 'left',
        name: 'cmbGrupoProducto',
        id: 'cmbGrupoProducto',
        valueField:'nombre',
        displayField:'nombre',
        fieldLabel: 'Grupo del Producto',
        width: 350,
        allowBlank: false,	
        emptyText: 'Seleccione Grupo',
        disabled: false,
        renderTo: 'divCmbProductos',
        editable: false,
        listeners: 
        {
            select: 
            {
                fn: function(combo, value) 
                {
                    if( !Ext.isEmpty(combo.getValue()) )
                    {
                        loadStoreEmpleadosDepartamento(null, combo.getValue());
                        loadStoreEmpleadosAsignados(null, combo.getValue());
                    }
                }
            }
        },
        forceSelection: true
    });
        
    modelStoreEmpDepartamento = Ext.create('Ext.selection.CheckboxModel',
    {
        checkOnly: true
    });
    
    storeEmpleadosDepartamento = new Ext.data.Store
    ({
        pageSize: 20,
        total: 'total',
        proxy: 
        {
            type: 'ajax',
            url: strUrlEmpleadosDepartamento,
            timeout: 9000000,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'usuarios'
            },
            extraParams:
            {
                strNombreArea: strNombreArea
            },
            simpleSortMode: true
        },
        fields:
        [
            {name: 'intIdPersonaEmpresaRol',   mapping: 'intIdPersonaEmpresaRol'},
            {name: 'strEmpleado',              mapping: 'strEmpleado'},
            {name: 'strCargo',                 mapping: 'strCargo'},
            {name: 'strGrupoProductoAsociado', mapping: 'strGrupoProductoAsociado'}
        ]
    });

    var gridEmpleadosDepartamento = Ext.create('Ext.grid.Panel',
    {
        width: 540,
        height: 510,
        store: storeEmpleadosDepartamento,
        loadMask: true,
        selModel: modelStoreEmpDepartamento,
        iconCls: 'icon-grid',
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
            {
                header: 'intIdPersonaEmpresaRol',
                dataIndex: 'intIdPersonaEmpresaRol',
                hidden: true,
                hideable: false
            },
            {
                header: 'Empleado',
                dataIndex: 'strEmpleado',
                width: 214,
                sortable: true
            },
            {
                header: 'Cargo Telcos',
                dataIndex: 'strCargo',
                width: 150,
                sortable: true,
                align: 'center'
            },
            {
                header: 'Grupo Producto',
                dataIndex: 'strGrupoProductoAsociado',
                width: 135,
                sortable: true,
                align: 'center'
            }
        ],
        title: 'Empleados del Departamento',
        bbar: Ext.create('Ext.PagingToolbar',
        {
            store: storeEmpleadosDepartamento,
            displayInfo: true,
            displayMsg: 'Desde {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'gridEmpleadosDepartamento'
    });
    
    
    var filterPanel = Ext.create('Ext.panel.Panel', 
    {
        bodyPadding: 7,
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
        width: 540,
        title: 'Criterios de busqueda',
        buttons: 
        [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function() 
                {
                    buscar('empleadosDepartamento');
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function() 
                {
                    limpiar('empleadosDepartamento');
                }
            }
        ],
        items: 
        [
            {width: '1%', border: false},
            {
                xtype: 'textfield',
                id: 'txtNombre',
                fieldLabel: 'Nombre',
                value: '',
                labelWidth: '7',
                width: '80%',
                style: 'margin: 5px'
            }
        ],
        renderTo: 'filtroEmpleadosDepartamento'
    });
    
    
    Ext.define('ListaEmpleadosAsignadosModel', 
    {
        extend: 'Ext.data.Model',
        fields: 
        [
            {
                name: 'intIdPersonaEmpresaRol', 
                type: 'string', 
                mapping: 'intIdPersonaEmpresaRol'
            },
            {
                name: 'strEmpleado',
                type: 'string', 
                mapping: 'strEmpleado'
            },
            {
                name: 'strCargo',
                type: 'string', 
                mapping: 'strCargo'
            },
            {
                name: 'strGrupoProductoAsociado',
                type: 'string', 
                mapping: 'strGrupoProductoAsociado'
            }
        ],
        idProperty: 'intIdPersonaEmpresaRol'
    });
    
    storeEmpleadosAsignados = new Ext.data.Store
    ({
        model: 'ListaEmpleadosAsignadosModel',
        pageSize: 20,
        total: 'total',
        proxy: 
        {
            type: 'ajax',
            url: strUrlEmpleadosDepartamento,
            timeout: 9000000,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'usuarios'
            },
            extraParams:
            {
                strNombreArea: strNombreArea
            },
            simpleSortMode: true
        }
    });

    modelStoreEmpAsignados = Ext.create('Ext.selection.CheckboxModel', 
    {
        checkOnly: true,
        listeners: 
        {
            selectionchange: function(sm, selections)
            {
                gridEmpleadosAsignaciones.down('#removeButton').setDisabled(selections.length == 0);
            }
        }
    });

    gridEmpleadosAsignaciones = Ext.create('Ext.grid.Panel',
    {
        id: 'gridEmpleadosAsignados',
        name: 'gridEmpleadosAsignados',
        width: 540,
        height: 565,
        store: storeEmpleadosAsignados,
        loadMask: true,
        selModel: modelStoreEmpAsignados,
        iconCls: 'icon-grid',
        plugins:[{ ptype : 'pagingselectpersist' }],
        columns: 
        [
            {
                header: 'intIdPersonaEmpresaRol',
                dataIndex: 'intIdPersonaEmpresaRol',
                hidden: true,
                hideable: false
            },
            {
                header: 'Empleado Asignado',
                dataIndex: 'strEmpleado',
                width: 214,
                sortable: true
            },
            {
                header: 'Cargo Telcos',
                dataIndex: 'strCargo',
                width: 150,
                sortable: true,
                align: 'center'
            },
            {
                header: 'Grupo Producto',
                dataIndex: 'strGrupoProductoAsociado',
                width: 135,
                sortable: true,
                align: 'center'
            }
        ],
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
                            Ext.getCmp('gridEmpleadosAsignados').getPlugin('pagingSelectionPersistence').selectAll();
                        }
                    },
                    {
                        iconCls: 'icon_limpiar',
                        text: 'Borrar Seleccion',
                        itemId: 'clear',
                        scope: this,
                        handler: function()
                        { 
                            Ext.getCmp('gridEmpleadosAsignados').getPlugin('pagingSelectionPersistence').clearPersistedSelection();
                        }
                    },
                    {
                        xtype: 'tbfill'
                    },
                    {
                        itemId: 'removeButton',
                        text: 'Eliminar',
                        tooltip: 'Elimina el empleado seleccionado',
                        iconCls: 'remove',
                        scope: this,
                        disabled: true,
                        handler: function()
                        {
                            asignarEliminarEmpleadoSeleccionados('eliminarGerenteProducto');
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
        title: 'Empleados Asignados',
        bbar: Ext.create('Ext.PagingToolbar',
        {
            store: storeEmpleadosAsignados,
            displayInfo: true,
            displayMsg: 'Desde {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'gridEmpleadosAsignaciones'
    });
    
    var filterPanelAsignaciones = Ext.create('Ext.panel.Panel',
    {
        bodyPadding: 7,
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
        width: 540,
        title: 'Criterios de busqueda',
        buttons: 
        [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function()
                {
                    buscar('empleadosAsignados');
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function()
                {
                    limpiar('empleadosAsignados');
                }
            }
        ],
        items: 
        [
            {width: '1%', border: false},
            {
                xtype: 'textfield',
                id: 'txtNombreAsignacion',
                fieldLabel: 'Nombre',
                value: '',
                labelWidth: '7',
                width: '80%',
                style: 'margin: 5px'
            }
        ],
        renderTo: 'filtroEmpleadosAsignados'
    });

});


function buscar(tipo)
{
    var strGrupoProducto  = Ext.getCmp('cmbGrupoProducto').value;
    var strNombreEmpleado = '';
    
    if( !Ext.isEmpty(strGrupoProducto) )
    {
        if( tipo == 'empleadosDepartamento' )
        {
            strNombreEmpleado = Ext.getCmp('txtNombre').value;
            
            if( Ext.isEmpty(strNombreEmpleado) )
            {
                Ext.Msg.alert('Error', 'No se puede realizar la búsqueda porque el campo Nombre está vacío.');
            }
            else
            {
                loadStoreEmpleadosDepartamento(strNombreEmpleado, strGrupoProducto);
            }
        }
        else
        {
            strNombreEmpleado = Ext.getCmp('txtNombreAsignacion').value;
            
            if( Ext.isEmpty(strNombreEmpleado) )
            {
                Ext.Msg.alert('Error', 'No se puede realizar la búsqueda porque el campo Nombre está vacío.');
            }
            else
            {
                loadStoreEmpleadosAsignados(strNombreEmpleado, strGrupoProducto);
            }
        }
    }
    else
    {
        Ext.Msg.alert('Atención', 'Debe seleccionar el grupo del producto para realizar la búsqueda correspondiente');
    }
}


function limpiar(tipo)
{
    var strGrupoProducto = Ext.getCmp('cmbGrupoProducto').value;
    
    if( !Ext.isEmpty(strGrupoProducto) )
    {
        if (tipo == 'empleadosDepartamento')
        {
            Ext.getCmp('txtNombre').value = "";
            Ext.getCmp('txtNombre').setRawValue("");

            loadStoreEmpleadosDepartamento(null, strGrupoProducto);
        }
        else
        {
            Ext.getCmp('txtNombreAsignacion').value = "";
            Ext.getCmp('txtNombreAsignacion').setRawValue("");

            loadStoreEmpleadosAsignados(null, strGrupoProducto);
        }
    }
    else
    {
        Ext.Msg.alert('Atención', 'Debe seleccionar el grupo del producto para realizar la acción correspondiente');
    }
}


function asignarEliminarEmpleadoSeleccionados(strAccion)
{
    var strIdPersonasEmpresaRol = '';
    var strGrupoProducto        = 0;
    var boolContinuar           = true;
    var modelStore              = null;
    var strMensaje              = '';
    
    if( !Ext.isEmpty(strAccion) )
    {
        if( strAccion == 'asignarGerenteProducto' )
        {
            modelStore = modelStoreEmpDepartamento;
            strMensaje = 'asignarán';
        }
        else if( strAccion == 'eliminarGerenteProducto' )
        {
            modelStore = modelStoreEmpAsignados;
            strMensaje = 'eliminarán';
        }
        else
        {
            boolContinuar = false;
            Ext.Msg.alert('Atención', 'No se ha enviado una acción válida para realizar la petición del usuario.');
        }
    }
    else
    {
         boolContinuar = false;
         Ext.Msg.alert('Atención', 'No se ha enviado una acción válida para realizar la petición del usuario.');
    }

    if( modelStore.getSelection().length > 0 )
    {
        strGrupoProducto = Ext.getCmp('cmbGrupoProducto').value;

        if( Ext.isEmpty(strGrupoProducto) )
        {
            boolContinuar = false;
            Ext.Msg.alert('Atención', 'Debe seleccionar el grupo del producto para realizar la acción requerida.');
        }//( Ext.isEmpty(strGrupoProducto) )
        
        if( Ext.isEmpty(intIdCargoGerenteProducto) )
        {
            boolContinuar = false;
            Ext.Msg.alert('Atención', 'No se ha encontrado cargo de Gerente de Producto. Por favor contactarse con Sistemas para revisar el '
                                      + 'inconveniente presentado.');
        }//( Ext.isEmpty(intIdCargoGerenteProducto) )
        
        if( boolContinuar )
        {
            Ext.Msg.confirm('Alerta', 'Se ' + strMensaje + ' los empleados. Desea continuar?', function(btn) 
            {
                if (btn == 'yes')
                {
                    for( var i = 0; i < modelStore.getSelection().length; ++i )
                    {
                        strIdPersonasEmpresaRol = strIdPersonasEmpresaRol + modelStore.getSelection()[i].get('intIdPersonaEmpresaRol');

                        if( i < (modelStore.getSelection().length - 1) )
                        {
                            strIdPersonasEmpresaRol = strIdPersonasEmpresaRol + '|';
                        }
                    }

                    connEsperaAccion.request
                    ({
                        url: strAsignarCaracteristica,
                        method: 'post',
                        params:
                        {
                            strNombreArea: strNombreArea,
                            strGrupoProducto: strGrupoProducto,
                            strValor: intIdCargoGerenteProducto,
                            strAccion: strAccion,
                            strCaracteristica: strCaracteristicaCargo,
                            intIdPersonaEmpresaRol: strIdPersonasEmpresaRol,
                            strCaracteristicaCargoProducto: strCaracteristicaCargoProducto
                        },
                        success: function(response)
                        {
                            if( response.responseText == 'OK')
                            {
                                if( strAccion == "asignarGerenteProducto" )
                                {
                                    Ext.Msg.alert('Información', 'Se han asignado los Gerentes de Producto con éxito');
                                }
                                else
                                {
                                    Ext.Msg.alert('Información', 'Se han eliminado los Gerentes de Producto que fueron seleccionados');
                                }
                            }
                            else
                            {
                                if( strAccion == "asignarGerenteProducto" )
                                {
                                    Ext.Msg.alert('Error', 'Hubo un problema al asignar los Gerentes de Producto');
                                }
                                else
                                {
                                    Ext.Msg.alert('Error', 'Hubo un problema al eliminar los Gerentes de Producto seleccionados');
                                }
                            }

                            loadStoreEmpleadosDepartamento(null, strGrupoProducto);
                            loadStoreEmpleadosAsignados(null, strGrupoProducto);
                        },
                        failure: function(result)
                        {
                            Ext.Msg.alert('Error ', 'Se presentaron errores al realizar la acción solicitada. Favor notificar al departamento de '+
                                          'sistemas.');

                        }
                    });
                }
            });
        }//( boolContinuar )
    }
    else
    {
        Ext.Msg.alert('Atención', 'Seleccione por lo menos un empleado de la lista');
    }//( modelStore.getSelection().length > 0 )
}

function loadStoreEmpleadosDepartamento(strNombre, strNombreProducto)
{
    storeEmpleadosDepartamento.loadData([],false);
    storeEmpleadosDepartamento.currentPage = 1;
    storeEmpleadosDepartamento.getProxy().extraParams.strNoAsignadosProducto = strNombreProducto;
    storeEmpleadosDepartamento.getProxy().extraParams.query                  = strNombre;
    storeEmpleadosDepartamento.load();
}

function loadStoreEmpleadosAsignados(strNombre, strNombreProducto)
{
    storeEmpleadosAsignados.loadData([],false);
    storeEmpleadosAsignados.currentPage = 1;
    storeEmpleadosAsignados.getProxy().extraParams.strAsignadosProducto = strNombreProducto;
    storeEmpleadosAsignados.getProxy().extraParams.query                = strNombre;
    storeEmpleadosAsignados.load();
}