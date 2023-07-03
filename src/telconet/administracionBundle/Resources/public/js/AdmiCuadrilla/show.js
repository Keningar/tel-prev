    var storeEmpleadosDepartamento = null;
var storeEmpleadosAsignados    = null;
var modelStoreEmpDepartamento  = null;
var modelStoreEmpAsignados     = null;
var gridEmpleadosDepartamento  = null;
var gridEmpleadosAsignaciones  = null;
var win                        = null;

Ext.require
([
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);


Ext.onReady(function()
{
    Ext.tip.QuickTipManager.init();
        
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
                name: 'strCargoTelcos',
                type: 'string', 
                mapping: 'strCargoTelcos'
            },
            {   
                name: 'strEstadoEmpleado',
                type: 'string', 
                mapping: 'strEstadoEmpleado'
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
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'usuarios'
            },
            extraParams:
            {
                strExceptoUsr: intIdJefeSeleccionado,
                strNombreArea: strNombreArea,
                intIdCuadrilla: intIdCuadrilla
            }
        },
        fields:
        [
            {name: 'intIdPersonaEmpresaRol', mapping: 'intIdPersonaEmpresaRol'},
            {name: 'strEmpleado',            mapping: 'strEmpleado'},
            {name: 'strCargo',               mapping: 'strCargo'},
            {name: 'strCargoTelcos',         mapping: 'strCargoTelcos'},
            {name: 'strEstadoEmpleado',      mapping: 'strEstadoEmpleado'}
            
        ],
        autoLoad: true
    });

    gridEmpleadosAsignaciones = Ext.create('Ext.grid.Panel',
    {
        id: 'gridEmpleadosAsignados',
        name: 'gridEmpleadosAsignados',
        width: 600,
        height: 250,
        store: storeEmpleadosAsignados,
        loadMask: true,
        iconCls: 'icon-grid',
        plugins:[{ ptype : 'pagingselectpersist' }],
        viewConfig: 
        {
            enableTextSelection: true,
            id: 'gvEmpleadosAsignaciones',
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
                header: 'Integrante',
                dataIndex: 'strEmpleado',
                width: 230,
                sortable: true
            },
            {
                header: 'Cargo NAF',
                dataIndex: 'strCargo',
                width: 150,
                sortable: true
            },
            {
                header: 'Cargo<br>Telcos',
                dataIndex: 'strCargoTelcos',
                width: 90,
                sortable: true
            },
            {
                header: 'Estado',
                dataIndex: 'strEstadoEmpleado',
                width: 128,
                sortable: true
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
                        grid.cellIndex = cellIndex;
                        grid.recordIndex = recordIndex;
                    }
                });

                grid.tip = Ext.create('Ext.tip.ToolTip',
                {
                    target: view.el,
                    delegate: '.x-grid-cell',
                    trackMouse: true,
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
                                        columnDataIndex = view.getHeaderByCell(trigger).dataIndex,
                                        columnText      = view.getRecord(parent).get(columnDataIndex).toString();

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
                        }
                    }
                });
            }
        },
        title: 'Integrantes de la Cuadrilla',
        renderTo: 'gridIntegrantes'
    });
});