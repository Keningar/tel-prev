Ext.require([
	'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);
	
Ext.onReady(function() { 
    Ext.tip.QuickTipManager.init();
    
    Ext.define('CargoGestionadoModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'intIdRol',              mapping: 'intIdRol'},
            {name: 'intIdParametroDet',     mapping: 'intIdParametroDet'},
            {name: 'strDescripcionRol',     mapping: 'strDescripcionRol'},
            {name: 'strEsJefe',             mapping: 'strEsJefe'},
            {name: 'strFuncionRol',         mapping: 'strFuncionRol'},
            {name: 'strFuncionaComoJefe',   mapping: 'strFuncionaComoJefe'},
            {name: 'strAccion'}
        ]
    });


    smCargosExistentes = Ext.create('Ext.selection.CheckboxModel', 
    {
        checkOnly: true,
        listeners: {
            selectionchange: function(sm, selections) 
            {
                gridCargosExistentes.down('#agregarCargosCuadrillaCreadoButton').setDisabled(selections.length === 0);
            }
        }
    });
    
    var filtroPanelCargosExistentes = Ext.create('Ext.panel.Panel', {
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
        width: 600,
        title: 'Filtros',
        buttons: 
        [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function() {
                    buscarCargoExistente();
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function() {
                    limpiarCargoExistente();
                }
            }
        ],
        items: 
        [
            {width: '1%', border: false},
            {
                xtype: 'textfield',
                id: 'txtBusqCargoExistente',
                fieldLabel: 'Cargo',
                value: '',
                labelWidth: '7',
                width: '80%',
                style: 'margin: 5px'
            }
        ],
        renderTo: 'filtroPanelCargosExistentes'
    });
    
    
    storeCargosExistentes = new Ext.data.Store({
        total: 'total',
        proxy: 
        {
            type: 'ajax',
            timeout: 600000,
            url: strUrlGetCargos,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'arrayResultado'
            }
        },
        fields:
        [
            {name: 'intIdRol',          mapping: 'intIdRol'},
            {name: 'intIdParametroDet'},
            {name: 'strDescripcionRol', mapping: 'strDescripcionRol'},
            {name: 'strEsJefe',         mapping: 'strEsJefe'}
            
        ],
        autoLoad: true
    });
    
    gridCargosExistentes = Ext.create('Ext.grid.Panel', {
        id: 'gridCargosExistentes',
        width: 600,
        height: 330,
        store: storeCargosExistentes,
        selModel: smCargosExistentes,
        title: 'Cargos Existentes',
        viewConfig:
        {
            enableTextSelection: true,
            id: 'gvCargosExistentes',
            trackOver: true,
            stripeRows: true,
            loadMask: true
        },
        columns: 
        [
            {
                id: 'intIdRolExistente',
                header: 'intIdRolExistente',
                dataIndex: 'intIdRol',
                hidden: true,
                hideable: false
            },
            {
                id: 'strDescripcionRolExistente',
                header: 'Cargo',
                dataIndex: 'strDescripcionRol',
                width: 200,
                sortable: true
            },
            {
                id: 'strEsJefeRolExistente',
                header: 'Es Jefe',
                dataIndex: 'strEsJefe',
                width: 200,
                sortable: true
            }
        ],
        dockedItems: 
        [{
            xtype: 'toolbar',
            items: 
            [{
                itemId: 'agregarCargosCuadrillaCreadoButton',
                text: 'Agregar',
                tooltip: 'Agregar el cargo seleccionado',
                iconCls: 'icon_add',
                disabled: true,
                handler: function() {
                    agregarCargoCuadrilla();
                }
            }]
        }],
        renderTo: "gridCargosExistentes"
    });

    smCargosCuadrillaActuales = Ext.create('Ext.selection.CheckboxModel', {
            checkOnly: true,
            listeners: {
                selectionchange: function(sm, selections) {
                    gridCargosCuadrillaActuales.down('#removeCargoCuadrillaActualButton').setDisabled(selections.length === 0);
                }
            }
    });
    
    var filtroPanelCargosCuadrillaActuales = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,
        border: false,
        buttonAlign: 'center',
        layout: {
            type: 'hbox',
            align: 'stretch'
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed: true,
        width: 600,
        title: 'Filtros',
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function() {
                    buscarCargoCuadrillaActual();
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function() {
                    limpiarCargoCuadrillaActual();
                }
            }
        ],
        items: [
            {width: '1%', border: false},
            {
                xtype: 'textfield',
                id: 'txtBusqCargoCuadrillaActual',
                fieldLabel: 'Cargo',
                value: '',
                labelWidth: '7',
                width: '80%',
                style: 'margin: 5px'
            }
        ],
        renderTo: 'filtroPanelCargosCuadrillaActuales'
    });
                    
    storeCargosCuadrillaActuales = new Ext.data.Store({
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
            url: strUrlGetCargosCuadrilla,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'arrayResultado'
            }
        },
        fields:
        [				
            {name: 'intIdRol',              mapping: 'intIdRol'},
            {name: 'intIdParametroDet',     mapping: 'intIdParametroDet'},
            {name: 'strDescripcionRol',     mapping: 'strDescripcionRol'},
            {name: 'strEsJefe',             mapping: 'strEsJefe'},
            {name: 'strFuncionRol',         mapping: 'strFuncionRol'},
            {name: 'strFuncionaComoJefe',   mapping: 'strFuncionaComoJefe'},
            {name: 'strAccion'}
        ],
        autoLoad: true
    });
    
    
    var cellEditingCargosCuadrillaActuales = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 1
    });
    
    gridCargosCuadrillaActuales = Ext.create('Ext.grid.Panel', {
        id: 'gridCargosCuadrillaActuales',
        width: 600,
        height: 330,
        store: storeCargosCuadrillaActuales,
        selModel: smCargosCuadrillaActuales,
        plugins: [cellEditingCargosCuadrillaActuales],
        columns: 
        [
            {
                id: 'intIdRolCuadrillaActual',
                header: 'intIdRolCuadrillaActual',
                dataIndex: 'intIdRol',
                hidden: true,
                hideable: false
            },
            {
                id: 'intIdParametroDet',
                header: 'intIdParametroDet',
                dataIndex: 'intIdParametroDet',
                hidden: true,
                hideable: false
            },
            {
                id: 'strDescripcionRolCuadrillaActual',
                header: 'Cargo',
                dataIndex: 'strDescripcionRol',
                width: 200,
                sortable: true
            },
            {
                id: 'strEsJefeRolCuadrillaActual',
                header: 'Es Jefe',
                dataIndex: 'strEsJefe',
                width: 60,
                sortable: true
            },
            {
                id: 'strFuncionRolCuadrillaActual',
                header: 'Función de Cargo',
                dataIndex: 'strFuncionRol',
                width: 150,
                sortable: true
            },
            {
                id: 'strFuncionaComoJefeCuadrillaActual',
                header: 'Coordina Cuadrillas?',
                dataIndex: 'strFuncionaComoJefe',
                width: 120,
                editor: new Ext.form.field.ComboBox({
                    typeAhead: true,
                    triggerAction: 'all',
                    selectOnTab: true,
                    store: [
                        ['SI', 'SI'],
                        ['NO', 'NO']
                    ],
                    lazyRender: true,
                    listClass: 'x-combo-list-small'
                })
            },
            {
                header: 'Acción',
                hidden: true,
                hideable: false,
                dataIndex: 'strAccion'
            }
        ],
        viewConfig:
        {
            enableTextSelection: true,
            id: 'gvCargosCuadrillaActuales',
            trackOver: true,
            stripeRows: true,
            loadMask: true,
            getRowClass: function(record, index) 
            {
                if (record.get("strAccion") == "Insertar")
                {
                    return "x-grid-row-news";
                }
            }
        },
        dockedItems:
        [
            {
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items:
                    [
                        {xtype: 'tbfill'},
                        {
                            itemId: 'removeCargoCuadrillaActualButton',
                            text: 'Eliminar',
                            tooltip: 'Elimina el cargo seleccionado',
                            iconCls: 'icon_delete',
                            scope: this,
                            disabled: true,
                            handler: function()
                            {
                                eliminarCargoCuadrilla(gridCargosCuadrillaActuales);
                            }
                        }
                    ]
            }
        ],
        title: 'Cargos considerados en Cuadrillas',
        frame: true,
        renderTo: 'gridCargosCuadrillaActuales'
    });
});

function existeAsignacionCargo(myRecord, grid)
{
    var boolExiste      = false;
    var intCountGrid    = grid.getStore().getCount();
    for (var i = 0; i < intCountGrid; i++)
    {
        var intIdRol = grid.getStore().getAt(i).get('intIdRol');
        if (intIdRol === myRecord.get('intIdRol'))
        {
            boolExiste = true;
            break;
        }
    }
    return boolExiste;
}




function buscarCargoExistente()
{
    storeCargosExistentes.getProxy().extraParams.strDescripcionRol = Ext.getCmp('txtBusqCargoExistente').value;
    storeCargosExistentes.load();
}
function limpiarCargoExistente()
{
    Ext.getCmp('txtBusqCargoExistente').value = "";
    Ext.getCmp('txtBusqCargoExistente').setRawValue("");

    storeCargosExistentes.getProxy().extraParams.strDescripcionRol = Ext.getCmp('txtBusqCargoExistente').value;
    storeCargosExistentes.load();    
}

function buscarCargoCuadrillaActual()
{
    storeCargosCuadrillaActuales.getProxy().extraParams.strDescripcionRol = Ext.getCmp('txtBusqCargoCuadrillaActual').value;
    storeCargosCuadrillaActuales.load();
}
function limpiarCargoCuadrillaActual()
{
    Ext.getCmp('txtBusqCargoCuadrillaActual').value = "";
    Ext.getCmp('txtBusqCargoCuadrillaActual').setRawValue("");

    storeCargosCuadrillaActuales.getProxy().extraParams.strDescripcionRol = Ext.getCmp('txtBusqCargoCuadrillaActual').value;
    storeCargosCuadrillaActuales.load();    
}



function agregarCargoCuadrilla()
{
    var arrayFuncionRol = ["Jefes", "Personal Tecnico"];
    
    if (smCargosExistentes.getSelection().length > 0)
    {
        for (var i = 0; i < smCargosExistentes.getSelection().length; ++i)
        {
            
            var r = Ext.create('CargoGestionadoModel', {
                intIdRol:               smCargosExistentes.getSelection()[i].get('intIdRol'),
                intIdParametroDet:      '',
                strDescripcionRol:      smCargosExistentes.getSelection()[i].get('strDescripcionRol'),
                strEsJefe:              smCargosExistentes.getSelection()[i].get('strEsJefe'),
                strFuncionRol:          smCargosExistentes.getSelection()[i].get('strEsJefe')=="SI" ? arrayFuncionRol[0] : arrayFuncionRol[1],
                strFuncionaComoJefe:    'NO',
                strAccion:              'Insertar'
                
            });
            if (!existeAsignacionCargo(r, gridCargosCuadrillaActuales))
            {
                storeCargosCuadrillaActuales.insert(0, r);
            }
            else
            {
                Ext.Msg.alert('Alerta ', 'Algún o algunos cargos escogidos ya se encuentran en el panel de Cargos considerados en cuandrillas!');
            }
        }
    }
    else
    {
        Ext.Msg.alert('Alerta ', 'Seleccione por lo menos un cargo de los cargos existentes!')
    }
}


function eliminarCargoCuadrilla(datosSelect)
{
    var xRowSelMod = datosSelect.getSelectionModel().getSelection();
    for (var i = 0; i < xRowSelMod.length; i++)
    {
        var RowSel = xRowSelMod[i];
        datosSelect.getStore().remove(RowSel);
    }
}



function obtenerCargosCuadrillaGestionados()
{
    var arrayCargosCuadrillaGestionados                 = new Object();
    arrayCargosCuadrillaGestionados['arrayRegistros']   = new Array();
    var arrayData                                       = new Array();
    var intTotalGestionados                             = 0;

    if(storeCargosCuadrillaActuales.getNewRecords().length > 0)
    {
        var registrosNuevos=storeCargosCuadrillaActuales.getNewRecords();
        Ext.each(registrosNuevos,function(record,index)
        {
            arrayData.push(record.data);
        });
        intTotalGestionados=intTotalGestionados+storeCargosCuadrillaActuales.getNewRecords().length;
    }

    if(storeCargosCuadrillaActuales.getUpdatedRecords().length > 0)
    {
        var registrosActualizados=storeCargosCuadrillaActuales.getUpdatedRecords();
        Ext.each(registrosActualizados,function(record,index)
        {
            record.set('strAccion', 'Editar');
            arrayData.push(record.data);
        });
        intTotalGestionados=intTotalGestionados+storeCargosCuadrillaActuales.getUpdatedRecords().length;
    }

    if(storeCargosCuadrillaActuales.getRemovedRecords().length > 0)
    {
        var registrosEliminados=storeCargosCuadrillaActuales.getRemovedRecords();
        Ext.each(registrosEliminados,function(record,index)
        {
            record.set('strAccion', 'Eliminar');
            arrayData.push(record.data);
        });
        intTotalGestionados=intTotalGestionados+storeCargosCuadrillaActuales.getRemovedRecords().length;
    }
    arrayCargosCuadrillaGestionados['intTotal']         = intTotalGestionados;
    arrayCargosCuadrillaGestionados['arrayRegistros']   = arrayData;

    param = Ext.JSON.encode(arrayCargosCuadrillaGestionados);
    return param;
}



function guardarCargosCuadrillas()
{
    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function(con, opt) {
                    Ext.get(document.body).mask('Guardando...');
                },
                scope: this
            },
            'requestcomplete': {
                fn: function(con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function(con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    });

    conn.request({
        method: 'POST',
        params: {
            strCargosCuadrilla: obtenerCargosCuadrillaGestionados()
        },
        url: strUrlUpdateCargosCuadrilla,
        success: function(response) {

            var json = Ext.JSON.decode(response.responseText);

            if (json.success === true)
            {
                Ext.Msg.alert('Informacion', json.mensaje, function(){
                    window.location = strUrlIndexCargosCuadrillas;
               });
            }
            else
            {
                Ext.Msg.alert('Error ', json.mensaje);
            }
        },
        failure: function(response) {
            Ext.Msg.alert('Alerta ', 'Error al realizar la acción');
        }
    });

}














