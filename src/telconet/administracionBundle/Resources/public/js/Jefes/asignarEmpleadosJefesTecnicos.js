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
        
    modelStoreEmpDepartamento = Ext.create('Ext.selection.CheckboxModel',
    {
        checkOnly: true
    });
    
    storeCargosNoVisibles = new Ext.data.Store
    ({
        total: 'total',
        pageSize: 200,
        proxy:
        {
            type: 'ajax',
            method: 'post',
            url: strUrlGetCargos,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams:
            {
                strNombreArea: strNombreArea
            }
        },
        fields:
        [
            {name: 'intIdCargo',     mapping: 'intIdCargo'},
            {name: 'strNombreCargo', mapping: 'strNombreCargo'}
        ],
        listeners: 
        {
            load: function(store, records)
            {
                 store.insert(0, 
                 [
                    {
                        strNombreCargo: 'Todos',
                        intIdCargo:     ''
                    }
                 ]);
            }      
        },
        autoLoad: true
    });
    
    storeEmpleadosDepartamento = new Ext.data.Store
    ({
        pageSize: 20,
        total: 'total',
        proxy: 
        {
            type: 'ajax',
            url: strUrlEmpleadosDepartamento,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                timeout: 900000,
                root: 'usuarios'
            },
            extraParams:
            {
                strExceptoUsr: intIdJefeSeleccionado,
                strNoAsignados: 'S',
                strCargo: strCargo,
                strNombreArea: strNombreArea,
                strExceptoChoferes:'S'
            }
        },
        fields:
        [
            {name: 'intIdPersonaEmpresaRol', mapping: 'intIdPersonaEmpresaRol'},
            {name: 'strEmpleado',            mapping: 'strEmpleado'},
            {name: 'strCargo',               mapping: 'strCargo'}
        ],
        autoLoad: true
    });

    var gridEmpleadosDepartamento = Ext.create('Ext.grid.Panel',
    {
        width: 480,
        height: 510,
        store: storeEmpleadosDepartamento,
        loadMask: true,
        selModel: modelStoreEmpDepartamento,
        iconCls: 'icon-grid',
        viewConfig: 
        {
            emptyText: 'No hay datos para mostrar',
            enableTextSelection: true,
            trackOver: true,
            stripeRows: true,
            loadMask: true
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
                width: 280,
                sortable: true
            },
            {
                header: 'Cargo',
                dataIndex: 'strCargo',
                width: 194,
                sortable: true
            }
        ],
        title: 'Empleados No Asignados',
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
            type: 'table',
            columns: 2,
            align: 'left'
        },
        bodyStyle: 
        {
            background: '#fff'
        },
        collapsible: true,
        collapsed: true,
        width: 480,
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
            },
            {width: '1%', border: false},
            {
                xtype: 'combobox',
                fieldLabel: 'Cargos:',
                labelWidth: '7',
                id: 'cmbCargoNoAsignados',
                name: 'cmbCargoNoAsignados',
                store: storeCargosNoVisibles,
                displayField: 'strNombreCargo',
                valueField: 'strNombreCargo',
                queryMode: 'remote',
                emptyText: 'Seleccione',
                width: '80%',
                forceSelection: true
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
            timeout: 900000,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'usuarios'
            },
            extraParams:
            {
                strExceptoUsr: intIdJefeSeleccionado,
                strsignadosA: intIdJefeSeleccionado,
                strNombreArea: strNombreArea
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
        width: 480,
        height: 510,
        store: storeEmpleadosAsignados,
        loadMask: true,
        selModel: modelStoreEmpAsignados,
        iconCls: 'icon-grid',
        plugins:[{ ptype : 'pagingselectpersist' }],
        viewConfig: 
        {
            emptyText: 'No hay datos para mostrar',
            enableTextSelection: true,
            trackOver: true,
            stripeRows: true,
            loadMask: true
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
                width: 280,
                sortable: true
            },
            {
                header: 'Cargo',
                dataIndex: 'strCargo',
                width: 194,
                sortable: true
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
                            var arrayParametros           = [];
                                arrayParametros['grid']   = gridEmpleadosAsignaciones;
                                arrayParametros['accion'] = 'eliminar';
                           
                            verificarEmpleadosAEliminar(arrayParametros);
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
            type: 'table',
            columns: 2,
            align: 'left'
        },
        bodyStyle:
        {
            background: '#fff'
        },
        collapsible: true,
        collapsed: true,
        width: 480,
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
            },
            {width: '1%', border: false},
            {
                xtype: 'combobox',
                fieldLabel: 'Cargos:',
                labelWidth: '7',
                id: 'cmbCargoAsignados',
                name: 'cmbCargoAsignados',
                store: storeCargosNoVisibles,
                displayField: 'strNombreCargo',
                valueField: 'strNombreCargo',
                queryMode: 'remote',
                emptyText: 'Seleccione',
                width: '80%',
                forceSelection: true
            },
        ],
        renderTo: 'filtroEmpleadosAsignados'
    });

});


function buscar(tipo)
{
    if (tipo == 'empleadosDepartamento')
    {
        if( Ext.getCmp('txtNombre').value == '' && Ext.getCmp('cmbCargoNoAsignados').getValue() == null )
        {
            Ext.Msg.alert('Error', 'No se puede realizar la búsqueda porque el campo Nombre y el campo Cargo están vacíos.');
        }
        else
        {
            var strCargo = Ext.getCmp('cmbCargoNoAsignados').getValue();
            
            if( Ext.getCmp('cmbCargoNoAsignados').getValue() == 'Todos')
            {
                strCargo = '';
            }
            
            storeEmpleadosDepartamento.loadData([],false);
            storeEmpleadosDepartamento.currentPage = 1;
            storeEmpleadosDepartamento.getProxy().extraParams.query          = Ext.getCmp('txtNombre').value;
            storeEmpleadosDepartamento.getProxy().extraParams.strFiltroCargo = strCargo;
            storeEmpleadosDepartamento.load();
        }
    }
    else
    {
        if( Ext.getCmp('txtNombreAsignacion').value == '' && Ext.getCmp('cmbCargoAsignados').getValue() == null )
        {
            Ext.Msg.alert('Error', 'No se puede realizar la búsqueda porque el campo Nombre y el campo Cargo están vacíos.');
        }
        else
        {
            var strCargo = Ext.getCmp('cmbCargoNoAsignados').getValue();
            
            if( Ext.getCmp('cmbCargoNoAsignados').getValue() == 'Todos')
            {
                strCargo = '';
            }
            
            storeEmpleadosAsignados.loadData([],false);
            storeEmpleadosAsignados.currentPage = 1;
            storeEmpleadosAsignados.getProxy().extraParams.query          = Ext.getCmp('txtNombreAsignacion').value;
            storeEmpleadosAsignados.getProxy().extraParams.strFiltroCargo = strCargo;
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
        Ext.getCmp('cmbCargoNoAsignados').setValue(null);

        storeEmpleadosDepartamento.loadData([],false);
        storeEmpleadosDepartamento.currentPage = 1;
        storeEmpleadosDepartamento.getProxy().extraParams.query          = Ext.getCmp('txtNombre').value;
        storeEmpleadosDepartamento.getProxy().extraParams.strFiltroCargo = Ext.getCmp('cmbCargoNoAsignados').value;
        storeEmpleadosDepartamento.load();
    }
    else
    {
        Ext.getCmp('txtNombreAsignacion').value = "";
        Ext.getCmp('txtNombreAsignacion').setRawValue("");
        Ext.getCmp('cmbCargoAsignados').setValue(null);

        storeEmpleadosAsignados.loadData([],false);
        storeEmpleadosAsignados.currentPage = 1;
        storeEmpleadosAsignados.getProxy().extraParams.query          = Ext.getCmp('txtNombreAsignacion').value;
        storeEmpleadosAsignados.getProxy().extraParams.strFiltroCargo = Ext.getCmp('cmbCargoAsignados').value;
        storeEmpleadosAsignados.load();
    }
}


function asignarEmpleado()
{
    var strIdPersonasEmpresaRol = '';
    
    if (modelStoreEmpDepartamento.getSelection().length > 0)
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
                    url: strUrlCambioJefe,
                    method: 'post',
                    params:
                    {
                        intIdJefe: intIdJefeSeleccionado,
                        strIdPersonaEmpresaRol: strIdPersonasEmpresaRol
                    },
                    success: function(response)
                    {
                        if( response.responseText == 'OK')
                        {
                            Ext.Msg.alert('Información', 'Se ha asignado los empleados con éxito');
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
    }
    else
    {
        alert('Seleccione por lo menos un empleado de la lista');
    }
}


function verificarEmpleadosAEliminar(grid)
{
    var strIdPersonasEmpresaRol = '';
    var xRowSelMod              = grid.getSelectionModel().getSelection();
            
    for (var i = 0; i < xRowSelMod.length; i++)
    {
        var RowSel = xRowSelMod[i];

        strIdPersonasEmpresaRol = strIdPersonasEmpresaRol + RowSel.get('intIdPersonaEmpresaRol');

        if(i < (xRowSelMod.length -1))
        {
            strIdPersonasEmpresaRol = strIdPersonasEmpresaRol + '|';
        }
    }
            
    Ext.MessageBox.wait("Verificando datos...");										
                                        
    Ext.Ajax.request
    ({
        url: strUrlVerificarEmpleadosAEliminar,
        method: 'post',
        timeout: 900000,
        params: 
        { 
            strIdPersonaEmpresaRol: strIdPersonasEmpresaRol
        },
        success: function(response)
        {
            var text = response.responseText;
            
            Ext.MessageBox.hide();
            
            if(text === "OK")
            {
                eliminarSeleccion(strIdPersonasEmpresaRol);
            }
            else
            {
                Ext.Msg.alert('Error', text); 
            }
        },
        failure: function(result)
        {
            Ext.MessageBox.hide();
            Ext.Msg.alert('Error',result.responseText);
        }
    });
}


function eliminarSeleccion(strIdPersonasEmpresaRol)
{
    Ext.Msg.confirm('Alerta', 'Se eliminaran los empleados asignados que han sido seleccionados. Desea continuar?', function(btn)
    {
        if (btn == 'yes')
        {
            connEsperaAccion.request
            ({
                url: strUrlCambioJefe,
                method: 'post',
                params:
                {
                    intIdJefe: null,
                    strIdPersonaEmpresaRol: strIdPersonasEmpresaRol,
                    strAccion: 'Eliminar',
                    strNombreArea: strNombreArea
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