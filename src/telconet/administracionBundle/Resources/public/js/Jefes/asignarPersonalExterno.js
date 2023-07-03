var storeEmpleadosDepartamento = null;
var storeEmpleadosAsignados    = null;
var modelStoreEmpDepartamento  = null;
var modelStoreEmpAsignados     = null;
var boolOcultarColumna         = false;
var boolOcultarCargo           = true;

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
    
    if( !Ext.isEmpty(strPrefijoEmpresa) && strPrefijoEmpresa == "TN" )
    {
        boolOcultarColumna = true;
        boolOcultarCargo   = false;
        intWidthNombres    = 295;
        intHeightAsignados = 561;
    }
    
    var storeCargosVisibles = new Ext.data.Store
    ({
        total: 'total',
        pageSize: 200,
        proxy:
        {
            type: 'ajax',
            method: 'post',
            url: strUrlGetCargos,
            timeout: 900000,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams:
            {
                strNombreArea: strNombreArea,
                strCargosJefes: 'PERSONAL_EXTERNO'
            }
        },
        fields:
        [
            {name: 'intIdCargo',     mapping: 'intIdCargo'},
            {name: 'strNombreCargo', mapping: 'strNombreCargo'}
        ]
    });

    var cmbCargosEmpleados = new Ext.form.ComboBox
    ({
        id: 'cmbCargosEmpleados',
        name: 'cmbCargosEmpleados',
        fieldLabel: 'Cargo Asignar',
        anchor: '100%',
        queryMode: 'remote',
        width: 250,
        emptyText: 'Seleccione Cargo',
        store: storeCargosVisibles,
        displayField: 'strNombreCargo',
        valueField: 'intIdCargo',
        renderTo: 'divCmbCargosEmpleados',
        editable: false,
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
                strNombreArea: strNombreArea,
                strExceptoFreelanceComisionista: 'S',
                strSoloPersonalExterno: 'S'
            }
        },
        fields:
        [
            {name: 'intIdPersonaEmpresaRol',   mapping: 'intIdPersonaEmpresaRol'},
            {name: 'strEmpleado',              mapping: 'strEmpleado'},
            {name: 'strCargo',                 mapping: 'strCargo'},
            {name: 'strGrupoProductoAsociado', mapping: 'strGrupoProductoAsociado'}
        ],
        autoLoad: true
    });

    var gridEmpleadosDepartamento = Ext.create('Ext.grid.Panel',
    {
        width: 540,
        height: 510,
        store: storeEmpleadosDepartamento,
        loadMask: true,
        selModel: modelStoreEmpDepartamento,
        iconCls: 'icon-grid',
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
                strNombreArea: strNombreArea,
                strSoloFreelanceComisionista: 'S',
                strSoloPersonalExterno: 'S'
            },
            simpleSortMode: true
        },
        autoLoad: true
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
                        text: 'Borrar Selección',
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
                            eliminarSeleccion(gridEmpleadosAsignaciones);
                        }
                    },
                    {
                        itemId: 'asignarMetaButton',
                        text: 'Asignar Meta',
                        scope: this,
                        tooltip: 'Asigna meta del empleado seleccionado',
                        iconCls: 'btn-asignar-meta',
                        hidden: boolOcultarColumna,
                        disabled: true,
                        handler: function()
                        {
                            asignarMetaMasivaSeleccion(gridEmpleadosAsignaciones);
                        }
                    }
                ]
            }
        ],
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
    if (tipo == 'empleadosDepartamento')
    {
        if( Ext.getCmp('txtNombre').value == '' )
        {
            Ext.Msg.alert('Error', 'No se puede realizar la búsqueda porque el campo Nombre está vacío.');
        }
        else
        {
            storeEmpleadosDepartamento.loadData([],false);
            storeEmpleadosDepartamento.currentPage = 1;
            storeEmpleadosDepartamento.getProxy().extraParams.query = Ext.getCmp('txtNombre').value;
            storeEmpleadosDepartamento.load();
        }
    }
    else
    {
        if( Ext.getCmp('txtNombreAsignacion').value == '' )
        {
            Ext.Msg.alert('Error', 'No se puede realizar la búsqueda porque el campo Nombre está vacío.');
        }
        else
        {
            storeEmpleadosAsignados.loadData([],false);
            storeEmpleadosAsignados.currentPage = 1;
            storeEmpleadosAsignados.getProxy().extraParams.query = Ext.getCmp('txtNombreAsignacion').value;
            storeEmpleadosAsignados.load();
        }
    }
}


function limpiar(tipo)
{
    if (tipo == 'empleadosDepartamento')
    {
        Ext.getCmp('txtNombre').value = "";
        Ext.getCmp('txtNombre').setRawValue("");

        storeEmpleadosDepartamento.loadData([],false);
        storeEmpleadosDepartamento.currentPage = 1;
        storeEmpleadosDepartamento.getProxy().extraParams.query = Ext.getCmp('txtNombre').value;
        storeEmpleadosDepartamento.load();
    }
    else
    {
        Ext.getCmp('txtNombreAsignacion').value = "";
        Ext.getCmp('txtNombreAsignacion').setRawValue("");

        storeEmpleadosAsignados.loadData([],false);
        storeEmpleadosAsignados.currentPage = 1;
        storeEmpleadosAsignados.getProxy().extraParams.query = Ext.getCmp('txtNombreAsignacion').value;
        storeEmpleadosAsignados.load();
    }
}


function asignarEmpleado()
{
    var strIdPersonasEmpresaRol = '';
    var intIdCargoSeleccionado  = Ext.getCmp('cmbCargosEmpleados').value;;
    var boolContinuar           = true;
    
    if( modelStoreEmpDepartamento.getSelection().length > 0 )
    {
        if( Ext.isEmpty(intIdCargoSeleccionado) )
        {
            boolContinuar = false;
            Ext.Msg.alert('Atención', 'No se puede proceder asignar los empleados puesto que no se ha seleccionado un cargo respectivo');
        }//( Ext.isEmpty(intIdCargoSeleccionado) )
        
        if( boolContinuar )
        {
            Ext.Msg.confirm('Alerta', 'Se asignaran los empleados. Desea continuar?', function(btn) 
            {
                if (btn == 'yes')
                {
                    for( var i = 0; i < modelStoreEmpDepartamento.getSelection().length; ++i )
                    {
                        strIdPersonasEmpresaRol = strIdPersonasEmpresaRol 
                                                  + modelStoreEmpDepartamento.getSelection()[i].get('intIdPersonaEmpresaRol');

                        if( i < (modelStoreEmpDepartamento.getSelection().length - 1) )
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
                            strValor: intIdCargoSeleccionado,
                            strAccion: 'Guardar',
                            strCaracteristica: strCaracteristicaCargo,
                            intIdPersonaEmpresaRol: strIdPersonasEmpresaRol
                        },
                        success: function(response)
                        {
                            if( response.responseText == 'OK')
                            {
                                Ext.Msg.alert('Información', 'Se han asignado los empleados con éxito');
                            }
                            else
                            {
                                Ext.Msg.alert('Error', 'Hubo un problema al asignar los empleados'); 
                            }

                            storeEmpleadosAsignados.load();
                            storeEmpleadosDepartamento.load();
                        },
                        failure: function(result)
                        {
                            Ext.Msg.alert('Error ', 'Se presentaron errores al asignar los empleados, favor notificar a sistemas.');

                        }
                    });
                }
            });
        }//( boolContinuar )
    }
    else
    {
        Ext.Msg.alert('Atención', 'Seleccione por lo menos un empleado de la lista');
    }//( modelStoreEmpDepartamento.getSelection().length > 0 )
}


function eliminarSeleccion(grid)
{
    var strIdPersonasEmpresaRol = '';
    
    Ext.Msg.confirm('Alerta', 'Se eliminaran los empleados asignados que han sido seleccionados. Desea continuar?', function(btn)
    {
        if (btn == 'yes')
        {
            var xRowSelMod = grid.getSelectionModel().getSelection();
            
            for (var i = 0; i < xRowSelMod.length; i++)
            {
                var RowSel = xRowSelMod[i];
                
                strIdPersonasEmpresaRol = strIdPersonasEmpresaRol + RowSel.get('intIdPersonaEmpresaRol');
                
                if(i < (xRowSelMod.length -1))
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
                    strValor: null,
                    strAccion: 'Eliminar',
                    strCaracteristica: strCaracteristicaCargo,
                    intIdPersonaEmpresaRol: strIdPersonasEmpresaRol
                },
                success: function(response)
                {
                    if( response.responseText == 'OK')
                    {
                        Ext.Msg.alert('Información', 'Se han eliminado los empleados asignados que fueron seleccionados');
                    }
                    else
                    {
                        Ext.Msg.alert('Error', 'Hubo un problema al eliminar los empleados asignados'); 
                    }
                    
                    storeEmpleadosAsignados.load();
                    storeEmpleadosDepartamento.load();
                },
                failure: function(result)
                {
                    Ext.Msg.alert('Error ', 'Se presentaron errores al eliminar, favor notificar a sistemas.');
                }
            });
        }
    });
}