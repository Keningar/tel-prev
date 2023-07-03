var storeEmpleadosDepartamento = null;
var storeEmpleadosAsignados    = null;
var modelStoreEmpDepartamento  = null;
var modelStoreEmpAsignados     = null;
var gridEmpleadosDepartamento  = null;
var gridEmpleadosAsignaciones  = null;
var win                        = null;
var arrayDiasSemanasId         = [];

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
    if(boolDepConfigHE)
    {
        var storeDiasSemanaCuadrilla = new Ext.data.Store({
            total: 'total',
            pageSize: 200,
            proxy: {
                type: 'ajax',
                method: 'post',
                url: urlDiasSemanaCuadrilla,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams:{
                    intIdCuadrilla: intIdCuadrilla
                }
            },
            fields: [
                {name: 'idDia', type: 'string', mapping: 'idDia'},
                {name: 'nombreDia', type: 'string', mapping: 'nombreDia'},
            ],
            autoLoad: true
        });
    
        storeDiasSemanaCuadrilla.load(
            {
                callback: function() {
    
                    var objDiasSemana = storeDiasSemanaCuadrilla.data;
                    for (let indice = 0; indice < storeDiasSemanaCuadrilla.getCount(); indice++) {
                        const diaId = objDiasSemana.items[indice].data.idDia;
                        arrayDiasSemanasId.push(diaId);
                    }
    
                    //combo dias de la semana labora cuadrilla
                    comboDiasSemana = new Ext.form.ComboBox({
                        xtype        : 'combobox',
                        store        :  storeDiaSemana,
                        id           : 'comboDiaSemana',
                        name         : 'comboDiaSemana',
                        displayField : 'nombreDia',
                        valueField   : 'idDia',
                        value        : arrayDiasSemanasId,
                        fieldLabel   : '<b>Dias Semana</b>',
                        width        :  500,
                        queryMode    : "local",
                        plugins      : ['selectedCount'],
                        disabled     : false,
                        editable     : false,
                        emptyText    : "Seleccione",
                        multiSelect  : true,
                        displayTpl   : '<tpl for="."> {nombreDia} <tpl if="xindex < xcount">, </tpl> </tpl>',
                        listConfig   : {
                            itemTpl: '{nombreDia} <div class="uncheckedChkbox"></div>'
                        },
    
                        renderTo: 'divComboDiaSemana'
                    });
    
                }
            }
        );
    
        DTFechaDesde = new Ext.form.DateField({
            xtype: 'datefield',
            id: 'txtFechaInicio',
            name: 'txtFechaInicio',
            fieldLabel: '<b>Fecha Inicio</b>',
            editable: false,
            format: 'd-m-Y',
            value:turnoFechaInicio,
            emptyText: "Seleccione",
            labelWidth: 70,
            renderTo:'divFechaInicio',
            //minValue:new Date(),
            listeners: {
                select: function(cmp, newValue, oldValue) {
                    validarHoras(cmp);
                }
             
            }
        });
        DTFechaHasta = new Ext.form.DateField({
            xtype: 'datefield',
            id: 'txtFechaFin',
            name: 'txtFechaFin',
            fieldLabel: '<b>Fecha Fin</b>',
            editable: false,
            format: 'd-m-Y',
            value:turnoFechaFin,
            emptyText: "Seleccione",
            labelWidth: 70,
            renderTo:'divFechaFin',
            //minValue:new Date(),
            listeners: {
                select: function(cmp, newValue, oldValue) {
                    validarHoras(cmp);
                }
            }
        });
    
        var storeDiaSemana = new Ext.data.Store ({
            total: 'total',
            pageSize: 200,
            fields: [
                {name: 'idDia', type: 'string', mapping: 'idDia'},
                {name: 'nombreDia', type: 'string', mapping: 'nombreDia'},
            ],
            sorters: [{
                property : 'idDia',
                direction: 'ASC'
            }],
            proxy:
            {
                type: 'ajax',
                method: 'post',
                url: urlDiasSemana,
                reader:
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
            },
            listeners: 
            {
                load: function(store, records)
                {
                     store.insert(0, 
                     [
                        {
                            nombreDia: 'Todos',
                            idDia:     '',
                        }
                     ]);
                }      
            },
            autoLoad: true
        });
    
        Ext.define('comboSelectedCount', {
            alias: 'plugin.selectedCount',
            init: function (combo) {
                combo.on({
                    select: function (me, records) {
                        var store = combo.getStore(),
                            diff = records.length != store.count,
                            newAll = false,
                            all = false,
                            newRecords = [];
                        Ext.each(records, function (obj, i, recordsItself) {
                            if (records[i].data.nombreDia === 'Todos') {
                                allRecord = records[i];
                                if (!combo.allSelected) {
                                    combo.select(store.getRange());
                                    combo.allSelected = true;
                                    all = true;
                                    newAll = true;
                                } else {
                                    all = true;
                                }
                            } else {
                                if (diff && !newAll)
                                    newRecords.push(records[i]);
                            }
                        });
                        if (combo.allSelected && !all) {
                            combo.clearValue();
                            combo.allSelected = false;
                        } else  if (diff && !newAll) {
                            combo.select(newRecords);
                            combo.allSelected = false;
                        }
                    }
                })
            }
        });
    }

    DTHoraDesde = new Ext.form.TimeField({
        xtype: 'timefield',
        id: 'horaInicioTurno',
        name:'horaInicioTurno',
        fieldLabel: '<b>Hora Inicio</b>',
        editable: false,
        minValue: '00:00',
        maxValue: '24:00',
        format: 'H:i',
        value:turnoHoraInicio,
        emptyText: "Seleccione",
        increment: 15,
        labelWidth: 70,
        renderTo:'divHoraInicioTurno',
        listeners: {
            select: function(cmp, newValue, oldValue) {
                validarHoras(cmp);
            }

        }
    });
    DTHoraHasta = new Ext.form.TimeField({
        xtype: 'timefield',
        id: 'horaFinTurno',
        name:'horaFinTurno',
        fieldLabel: '<b>Hora Fin</b>',
        editable: false,
        minValue: '00:00',
        maxValue: '24:00',
        format: 'H:i',
        emptyText: "Seleccione",
        increment: 15,
        labelWidth: 70,
        value:turnoHoraFin,
        renderTo:'divHoraFinTurno',
        listeners: {
            select: function(cmp, newValue, oldValue) {
                validarHoras(cmp);
            }
        }
    });

    
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
                root: 'usuarios'
            },
            extraParams:
            {
                strExceptoUsr: intIdJefeSeleccionado,
                strsignadosA: intIdJefeSeleccionado,
                strNombreArea: strNombreArea,
                strSinCuadrilla: 'S',
                strExceptoChoferes:'S'
            },
        },
        fields:
        [
            {name: 'intIdPersonaEmpresaRol', mapping: 'intIdPersonaEmpresaRol'},
            {name: 'strEmpleado',            mapping: 'strEmpleado'},
            {name: 'strCargo',               mapping: 'strCargo'},
            {name: 'strEstadoEmpleado',      mapping: 'strEstadoEmpleado'},
        ],
        autoLoad: true
    });

    gridEmpleadosDepartamento = Ext.create('Ext.grid.Panel',
    {
        width: 410,
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
        viewConfig: 
        {
            enableTextSelection: true,
            id: 'gvEmpleadosDepartamento',
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
                width: 220,
                sortable: true
            },
            {
                header: 'Cargo NAF',
                dataIndex: 'strCargo',
                width: 140,
                sortable: true
            },
            {
                header: 'Estado',
                dataIndex: 'strEstadoEmpleado',
                width: 94,
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
        title: 'Empleados Asignados al Coordinador',
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
        width: 410,
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
            },
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
                name: 'strCargoTelcos',
                type: 'string', 
                mapping: 'strCargoTelcos'
            },
            {
                name: 'boolTabletAsignada',
                type: 'string', 
                mapping: 'boolTabletAsignada'
            },
            {
                name: 'intTabletAsignada',
                type: 'string', 
                mapping: 'intTabletAsignada'
            },
            {
                name: 'strTabletAsignada',
                type: 'string', 
                mapping: 'strTabletAsignada'
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
                strsignadosA: intIdJefeSeleccionado,
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
            {name: 'strTabletAsignada',      mapping: 'strTabletAsignada'},
            {name: 'strEstadoEmpleado',      mapping: 'strEstadoEmpleado'}
        ],
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
        width: 580,
        height: 510,
        store: storeEmpleadosAsignados,
        loadMask: true,
        selModel: modelStoreEmpAsignados,
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
                header: 'Empleado Asignado',
                dataIndex: 'strEmpleado',
                width: 219,
                sortable: true
            },
            {
                header: 'Cargo NAF',
                dataIndex: 'strCargo',
                width: 100,
                sortable: true
            },
            {
                header: 'Cargo<br>Telcos',
                dataIndex: 'strCargoTelcos',
                width: 85,
                sortable: true
            },
            {
                header: 'Tablet Asignada',
                dataIndex: 'strTabletAsignada',
                width: 110,
                sortable: true
            },
            {
                header: 'Estado',
                dataIndex: 'strEstadoEmpleado',
                width: 90,
                sortable: true
            },
            {
                header: 'Acciones',
                xtype: 'actioncolumn',
                width:89,
                sortable: false,
                items:
                [
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var strClassA   = "btn-acciones btn-habilitar-jefe";

                            if( boolPermisoCambiarCargo === false )
                            {
                                strClassA = 'icon-invisible';
                            }

                            if (strClassA == "icon-invisible")
                            {
                                this.items[0].tooltip = '';
                            }
                            else
                            {
                                this.items[0].tooltip = 'Cambiar Cargo de Telcos';
                            }

                            return strClassA;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec         = storeEmpleadosAsignados.getAt(rowIndex);
                            var strClassA   = "btn-acciones btn-habilitar-jefe";

                            if( boolPermisoCambiarCargo === false )
                            {
                                strClassA = 'icon-invisible';
                            }

                            if (strClassA != "icon-invisible")
                            {
                                cambiarCargoEnTelcos(rec.data.intIdPersonaEmpresaRol, rec.data.strCargoTelcos, intIdCuadrilla);
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
                            eliminarSeleccion(gridEmpleadosAsignaciones);
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
        title: 'Empleados Asignados a la Cuadrilla',
        renderTo: 'gridEmpleadosAsignaciones'
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
            storeEmpleadosDepartamento.loadData([],false);
            storeEmpleadosDepartamento.currentPage = 1;
            storeEmpleadosDepartamento.getProxy().extraParams.query          = Ext.getCmp('txtNombre').value;

            if(Ext.getCmp('cmbCargoNoAsignados').getValue() == 'Todos')
            {
                storeEmpleadosDepartamento.getProxy().extraParams.strFiltroCargo = '';
            }
            else
            {
                storeEmpleadosDepartamento.getProxy().extraParams.strFiltroCargo = Ext.getCmp('cmbCargoNoAsignados').value;
            }

            storeEmpleadosDepartamento.load();
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
}


function agregarEmpleadoCuadrilla()
{ 
    var xRowSelMod = gridEmpleadosDepartamento.getSelectionModel().getSelection();

    if(xRowSelMod.length > 0)
    {
        for (var i = 0; i < xRowSelMod.length; i++)
        {
            var RowSel                 = xRowSelMod[i];
            var strCargo               = RowSel.get('strCargo');
            var strEmpleado            = RowSel.get('strEmpleado');
            var intIdPersonaEmpresaRol = RowSel.get('intIdPersonaEmpresaRol');

            var r = Ext.create('ListaEmpleadosAsignadosModel', 
            {
                intIdPersonaEmpresaRol: intIdPersonaEmpresaRol,
                strEmpleado: strEmpleado,
                strCargo: strCargo
            });   

            if(!existeIntegrante(r, gridEmpleadosAsignaciones))
            {
                storeEmpleadosAsignados.insert(0, r);   			
            }
        }
    }
    else
    {
        Ext.Msg.alert('Error', 'Debe seleccionar al menos un empleado para integrarlo a la cuadrilla.');
    }
}


function existeIntegrante(myRecord, grid)
{
    var existe = false;
    var num    = grid.getStore().getCount(); 

    for(var i=0; i < num ; i++)
    {
        var integrante = grid.getStore().getAt(i).get('intIdPersonaEmpresaRol');

        if(integrante == myRecord.get('intIdPersonaEmpresaRol'))
        { 
            existe = true;

            Ext.Msg.alert('Error', 'Ya fue seleccionada esta persona '+myRecord.get('strEmpleado'));
        }
    }

    return existe;	
}


function eliminarSeleccion(datosSelect)
{
    var storeMotivos = new Ext.data.Store
    ({
        total: 'total',
        pageSize: 200,
        proxy:
        {
            type: 'ajax',
            method: 'post',
            url: strUrlMotivosCuadrillas,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams:
            {
                strAccion: 'delete', 
                strModulo: 'admi_cuadrilla',
                strItemMenu: 'Cuadrillas'
            }
        },
        fields:
        [
            {name: 'intIdMotivo', mapping: 'intIdMotivo'},
            {name: 'strMotivo',   mapping: 'strMotivo'}
        ],
        listeners: 
        {
            load: function(store, records)
            {
                 store.insert(0, 
                 [
                    {
                        strMotivo: 'Seleccione',
                        intIdMotivo: ''
                    }
                 ]);
            }      
        }
    });

    var formPanel = Ext.create('Ext.form.Panel',
    {
        id: 'formEliminarEmpleados',
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: 
        {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side'
        },
        items: 
        [
            {
                xtype: 'fieldset',
                title: '',
                defaultType: 'textfield',
                defaults:
                {
                    width: 300
                },
                items:
                [
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Motivo:',
                        id: 'cmbMotivo',
                        name: 'cmbMotivo',
                        store: storeMotivos,
                        displayField: 'strMotivo',
                        valueField: 'intIdMotivo',
                        queryMode: 'remote',
                        emptyText: 'Seleccione',
                        forceSelection: true
                    }
                ]
            }
        ],
        buttons:
        [
            {
                text: 'Aceptar',
                type: 'submit',
                handler: function()
                {
                    var form = Ext.getCmp('formEliminarEmpleados').getForm();

                    if( form.isValid() )
                    {
                        var intIdMotivo = Ext.getCmp('cmbMotivo').getValue();

                        if ( intIdMotivo != null && intIdMotivo != '' )
                        {
                            var arrayParametros           = new Array();
                                arrayParametros['grid']   = datosSelect;
                                arrayParametros['motivo'] = intIdMotivo;
                                arrayParametros['accion'] = 'Eliminar';
                                arrayParametros['store']  = storeEmpleadosAsignados;
                            if (boolDepConfigHE)
                            {
                                verificarPlanificacion(arrayParametros);
                            }
                            else
                            {
                                cambiarEstadosEmpleados(arrayParametros);
                            }
                            
                        }
                        else
                        {
                            Ext.Msg.alert('Atenci\xf3n', 'Debe seleccionar un motivo');
                        }
                    }
                }
            },
            {
                text: 'Cerrar',
                handler: function()
                {
                    win.destroy();
                }
            }
        ]
    });

win = Ext.create('Ext.window.Window',
      {
           title: 'Eliminar Empleados',
           modal: true,
           width: 350,
           closable: true,
           layout: 'fit',
           items: [formPanel]
      }).show();
}


function obtenerIntegrantes()
{
    Ext.get('empleados_integrantes').dom.value = "";

    var array_integrantes                = new Object();
        array_integrantes['total']       = gridEmpleadosAsignaciones.getStore().getCount();
        array_integrantes['encontrados'] = new Array();

    var array_data = new Array();

    for(var i=0; i < gridEmpleadosAsignaciones.getStore().getCount(); i++)
    {
        array_data.push(gridEmpleadosAsignaciones.getStore().getAt(i).data);
    }

    array_integrantes['encontrados'] = array_data;

    Ext.get('empleados_integrantes').dom.value = Ext.JSON.encode(array_integrantes);
}


function validarFormulario()
{
    Ext.MessageBox.wait("Actualizando los datos...");

    var fieldHoraDesdeTurno = Ext.getCmp('horaInicioTurno');
    var valueHoraDesdeTurno = fieldHoraDesdeTurno.getValue();
    var formattedValueHoraDesdeTurno = Ext.Date.format(valueHoraDesdeTurno, 'H:i');
    var fieldHoraHastaTurno = Ext.getCmp('horaFinTurno');
    var valueHoraHastaTurno = fieldHoraHastaTurno.getValue();
    var formattedValueHoraHastaTurno = Ext.Date.format(valueHoraHastaTurno, 'H:i');
    var boolValida = true;

    /*var validarNumChoferes=existenChoferes();
    if(validarNumChoferes)
    {
        Ext.MessageBox.hide();
        Ext.Msg.alert('Error', 'Existen choferes asignados a la cuadrilla. Usted no está autorizado para asignar choferes. Por favor elimínelos');
        return false;
    }*/

    
    if(!validacionCampoLlenos())
    {
        boolValida = false;
    }
    else 
    {
        obtenerIntegrantes();
        if(boolDepConfigHE)
        {
            var fieldFechaDesdeTurno = Ext.getCmp('txtFechaInicio');
            var valueFechaDesdeTurno = fieldFechaDesdeTurno.getValue();
            var formattedValueFechaDesdeTurno = Ext.Date.format(valueFechaDesdeTurno, 'd-m-Y');
            var fieldFechaHastaTurno = Ext.getCmp('txtFechaFin');
            var valueFechaHastaTurno = fieldFechaHastaTurno.getValue();
            var formattedValueFechaHastaTurno = Ext.Date.format(valueFechaHastaTurno, 'd-m-Y');
            var fieldDiasSemana = Ext.getCmp('comboDiaSemana');
            var valueDiasSemana = fieldDiasSemana.getValue().filter(function(valor) {
                return valor !== '';
              });
            var arrayParametros           = new Array();
            arrayParametros['motivo'] = '';
            arrayParametros['accion'] = 'Agregar';
            arrayParametros['tipoHorarioId']  = '1';
            arrayParametros['grid']   = gridEmpleadosDepartamento;
            arrayParametros['store']  = storeEmpleadosDepartamento;
            arrayParametros['fechaInicio']  = formattedValueFechaDesdeTurno;
            arrayParametros['fechaFin']  = formattedValueFechaHastaTurno;
            arrayParametros['horaInicio']  = formattedValueHoraDesdeTurno;
            arrayParametros['horaFin']  = formattedValueHoraHastaTurno;
            arrayParametros['diasSemana']  = JSON.stringify({dias:valueDiasSemana});
            arrayParametros['diasSemana1']  = JSON.stringify({valueDiasSemana});
            arrayParametros["cambio_estado_emple"] = 'NO';
            
            enviarTramaPaquete(arrayParametros);
            boolValida = false;
        }
        else
        {
            Ext.Ajax.request
            ({
                url: strUrlVerificarVehiculoCuadrilla,
                method: 'post',
                params: 
                { 
                    strHoraDesdeTurno      : formattedValueHoraDesdeTurno,
                    strHoraHastaTurno      : formattedValueHoraHastaTurno,
                },
                success: function(response)
                {
                    var text = response.responseText;
        
                    if(text === "OK")
                    {
                        $("#horaInicioTurnoCuadrilla").val(formattedValueHoraDesdeTurno);
                        $("#horaFinTurnoCuadrilla").val(formattedValueHoraHastaTurno);
                        document.getElementById("form_new_proceso").submit();

                    }
                    else
                    {
                        Ext.MessageBox.hide();
                        Ext.Msg.alert('Error', text); 
                    }
                },
                failure: function(result)
                {
                    Ext.MessageBox.hide();
                    Ext.Msg.alert('Error',result.responseText);
                }
            });
        
            boolValida = false;
        }
    }

    return boolValida;

}



function cambiarCargoEnTelcos(intIdPersonaEmpresaRol, strCargo, intCuadrilla)
{
    storeCargosVisibles = new Ext.data.Store
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
                strEsVisible: 'SI',
                strNombreArea: strNombreArea,
                strCargo: strCargo,
                intIdCuadrilla: intCuadrilla,
                strCargoChoferNoVisible:'S'
            },
        },
        fields:
        [
            {name: 'intIdCargo',     mapping: 'intIdCargo'},
            {name: 'strNombreCargo', mapping: 'strNombreCargo'}
        ]
    });

    var formPanel = Ext.create('Ext.form.Panel',
        {
            id: 'formCambiarCargo',
            bodyPadding: 2,
            waitMsgTarget: true,
            fieldDefaults: 
            {
                labelAlign: 'left',
                labelWidth: 85,
                msgTarget: 'side'
            },
            items: 
            [
                {
                    xtype: 'fieldset',
                    title: '',
                    defaultType: 'textfield',
                    defaults:
                    {
                        width: 300
                    },
                    items:
                    [
                        {
                            xtype: 'displayfield',
                            fieldLabel: 'Cargo Actual:',
                            name: 'cargoActual',
                            value: strCargo
                        },
                        {
                            xtype: 'combobox',
                            fieldLabel: 'Cargos:',
                            id: 'comboCargos',
                            name: 'comboCargos',
                            store: storeCargosVisibles,
                            displayField: 'strNombreCargo',
                            valueField: 'strNombreCargo',
                            queryMode: 'remote',
                            emptyText: 'Seleccione',
                            forceSelection: true
                        }
                    ]
                }
            ],
            buttons:
            [
                {
                    text: 'Asignar',
                    type: 'submit',
                    handler: function()
                    { 
                        var form = Ext.getCmp('formCambiarCargo').getForm();

                        if( form.isValid() )
                        {
                            var strNombreCargo = Ext.getCmp('comboCargos').getValue();

                            if ( strNombreCargo != null && strNombreCargo != '' )
                            {
                                if(validarLiderCuadrillaExistente(strNombreCargo, intIdPersonaEmpresaRol))
                                {
                                    var arrayDataEmpleadosAsignados = obtenerIntegrantesCuadrilla();

                                    var arrayParametros                             = [];
                                    arrayParametros['intIdPersonalEmpresaRol']  = intIdPersonaEmpresaRol;
                                    arrayParametros['valor']                    = strNombreCargo;
                                    arrayParametros['caracteristica']           = 'CARGO';
                                    arrayParametros['accion']                   = 'Guardar';
                                    arrayParametros['store']                    = storeEmpleadosAsignados;
                                    arrayParametros['dataEmpleadosAsignados']   = arrayDataEmpleadosAsignados;

                                    ajaxAsignarCaracteristica(arrayParametros);
                                }
                                else
                                {
                                    Ext.Msg.alert('Atenci\xf3n', 'Debe existir un lider en la cuadrilla.');
                                }
                            }
                            else
                            {
                                Ext.Msg.alert('Atenci\xf3n', 'Debe seleccionar un Cargo');
                            }
                        }
                    }
                },
                {
                    text: 'Cerrar',
                    handler: function()
                    {
                        win.destroy();
                    }
                }
            ]
        });

    win = Ext.create('Ext.window.Window',
          {
               title: 'Cambiar cargo de Telcos',
               modal: true,
               width: 350,
               closable: true,
               layout: 'fit',
               items: [formPanel]
          }).show();
}


function agregarSeleccion()
{
    var xRowSelMod = gridEmpleadosDepartamento.getSelectionModel().getSelection();

    if( xRowSelMod.length > 0 )
    {   
        var storeTipoHorario = new Ext.data.Store({
            total: 'total',
            pageSize: 200,
            autoLoad:false,
            proxy: {
                type: 'ajax',
                method: 'post',
                url: urlConsultarTipoHorario,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    consulta: 'list_tipoHorarios'
                    }
            },
            fields:
                [
                    {name: 'idTipoHorario', mapping: 'idTipoHorario'},
                    {name: 'nombreTipoHorario', mapping: 'nombreTipoHorario'}
                ]
        });
        var storeDiaSemana = new Ext.data.Store ({
            total: 'total',
            pageSize: 200,
            fields: [
                {name: 'idDia', type: 'string', mapping: 'idDia'},
                {name: 'nombreDia', type: 'string', mapping: 'nombreDia'},
            ],
            sorters: [{
                property : 'idDia',
                direction: 'ASC'
            }],
            proxy:
            {
                type: 'ajax',
                method: 'post',
                url: urlDiasSemana,
                reader:
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
            },
            listeners: 
            {
                load: function(store, records)
                {
                     store.insert(0, 
                     [
                        {
                            nombreDia: 'Todos',
                            idDia:     '',
                        }
                     ]);
                }      
            },
            autoLoad: true
        });
    
        Ext.define('comboSelectedCount1', {
            alias: 'plugin.selectedCount1',
            init: function (combo) {
                combo.on({
                    select: function (me, records) {
                        var store = combo.getStore(),
                            diff = records.length != store.count,
                            newAll = false,
                            all = false,
                            newRecords = [];
                        Ext.each(records, function (obj, i, recordsItself) {
                            if (records[i].data.nombreDia === 'Todos') {
                                allRecord = records[i];
                                if (!combo.allSelected) {
                                    combo.select(store.getRange());
                                    combo.allSelected = true;
                                    all = true;
                                    newAll = true;
                                } else {
                                    all = true;
                                }
                            } else {
                                if (diff && !newAll)
                                    newRecords.push(records[i]);
                            }
                        });
                        if (combo.allSelected && !all) {
                            combo.clearValue();
                            combo.allSelected = false;
                        } else  if (diff && !newAll) {
                            combo.select(newRecords);
                            combo.allSelected = false;
                        }
                    }
                })
            }
        });
        if(boolDepConfigHE)
        {
            var formPanel = Ext.create('Ext.form.Panel',
            {
                id: 'formAgregarEmpleados',
                bodyPadding: 2,
                waitMsgTarget: true,
                fieldDefaults: 
                {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget: 'side'
                },
                items: 
                [
                    {
                        xtype: 'fieldset',
                        title: '',
                        defaultType: 'textfield',
                        defaults:
                        {
                            width: 300
                        },
                        items:
                        [
                            {
                                xtype: 'checkbox',
                                fieldLabel : '¿Asignar mismo horario de cuadrilla?',
                                id : 'checkAsignarAhora',
                                name : 'checkAsignarAhora',
                                anchor: '100%',
                                checked: false,
                                hidden: false,
                                listeners: {
                                    change: function(field, newValue, oldValue, eOpts){
    
                                        if(newValue == true)
                                        {
                                            Ext.getCmp('horaInicio').disable();
                                            Ext.getCmp('horaFin').disable();
                                     
                                            Ext.getCmp('horaInicio').setRawValue(Ext.Date.format(Ext.getCmp('horaInicioTurno').getValue(), 'H:i'));
                                            Ext.getCmp('horaFin').setRawValue(Ext.Date.format(Ext.getCmp('horaFinTurno').getValue(), 'H:i'));
                                        }
                                        else
                                        {
                                            Ext.getCmp('horaInicio').enable(); 
                                            Ext.getCmp('horaFin').enable(); 
        
                                            Ext.getCmp('horaInicio').setRawValue("");
                                            Ext.getCmp('horaFin').setRawValue("");
                                        }
                                    }
                                }
                            },
                            {
                                displayField:'nombreTipoHorario',
                                valueField  : 'idTipoHorario',
                                xtype       : 'combobox',
                                editable    : false,
                                fieldLabel  : '<b>*Tipo horario</b>',
                                id          : 'cmbTipoHorario',
                                name        : 'cmbTipoHorario',
                                emptyText   : "Seleccione",
                                anchor      : '80%',
                                store       : storeTipoHorario,
                                listeners: {
                                    select: function(cmp, newValue, oldValue) {
                                        if (newValue.length > 0) 
                                        {
                                            var esTipoLineaBase = newValue[0].data.nombreTipoHorario.toUpperCase() == 'LINEA BASE' ? true: false;
    
                                            if(esTipoLineaBase)
                                            {
                                                /*Ext.getCmp('checkAsignarAhora').disable();
                                                Ext.getCmp('FechaInicio').disable();
                                                Ext.getCmp('FechaFin').disable();
                                                Ext.getCmp('horaInicio').disable();
                                                Ext.getCmp('horaFin').disable();
                                                Ext.getCmp('comboDiaSemana1').disable();*/
                                                var fechaActual = new Date();
                                                var fechaActual1 = new Date();
                                                var tempFechaDesdeAsigHoras = Ext.getCmp('txtFechaInicio').getValue();
                                                var fieldHoraDesdeAsignacion = Ext.getCmp('horaInicioTurno').getValue();
                                    
                                                fechaActual1.setHours(fieldHoraDesdeAsignacion.getHours());          
                                                fechaActual1.setMinutes(fieldHoraDesdeAsignacion.getMinutes());
                                                if(Ext.getCmp('txtFechaFin').getValue() < fechaActual)
                                                {
                                                    strMensaje='La Fechas de planificación son menores a la fecha Actual asegurese de actualizar la planificación de la cuadrilla';
                                                    Ext.Msg.alert('Atenci\xf3n', strMensaje);
                                                    winAgregar.destroy();
                                                    return false;
                                                } 
                                                if (tempFechaDesdeAsigHoras < fechaActual)
                                                {
                                                    if(fechaActual1 < fechaActual)
                                                    {
                                                        fechaActual.setDate(fechaActual.getDate() + 1);
                                                        Ext.getCmp('FechaInicio').setRawValue(Ext.Date.format(fechaActual, 'd-m-Y'));
                                                    }
                                                    else
                                                    {
                                                        fechaActual.setDate(fechaActual.getDate());
                                                        Ext.getCmp('FechaInicio').setRawValue(Ext.Date.format(fechaActual, 'd-m-Y'));
                                                    }

                                                }
                                                else
                                                {
                                                    Ext.getCmp('FechaInicio').setRawValue(Ext.Date.format(Ext.getCmp('txtFechaInicio').getValue(), 'd-m-Y'));
                                                }
                                                
                                                Ext.getCmp('FechaFin').setRawValue(Ext.Date.format(Ext.getCmp('txtFechaFin').getValue(), 'd-m-Y'));
                                                Ext.getCmp('checkAsignarAhora').setValue(true);
                                                Ext.getCmp('comboDiaSemana1').setValue(Ext.getCmp('comboDiaSemana').getValue());
                                                
                                            }
                                            else 
                                            {
                                                habilitarCamposAgregar();
                                                Ext.getCmp('checkAsignarAhora').enable();
    
                                                Ext.getCmp('FechaInicio').setRawValue("");
                                                Ext.getCmp('FechaFin').setRawValue("");
                                                Ext.getCmp('horaInicio').setRawValue("");
                                                Ext.getCmp('horaFin').setRawValue("");
                                                Ext.getCmp('comboDiaSemana1').setValue(null);
                                                Ext.getCmp('checkAsignarAhora').setValue(false);
                                            }
    
                                        }
                                    }
                                }
                            },
                            {
                                xtype: 'datefield',
                                id: 'FechaInicio',
                                name: 'FechaInicio',
                                fieldLabel: '<b>*Fecha Inicio</b>',
                                editable: false,
                                format: 'd-m-Y',
                                value:'',
                                emptyText: "Seleccione",
                                //labelWidth: 70,
                                queryMode: 'remote',
                                anchor: '80%',
                                //renderTo:'divFechaInicio',
                                //minValue:new Date(),
                                listeners: {
                                    select: function(cmp, newValue, oldValue) {
                                        validarFechas(cmp);
                                    }
                                 
                                },
                            },
                            {
                                xtype: 'datefield',
                                id: 'FechaFin',
                                name: 'FechaFin',
                                fieldLabel: '<b>*Fecha Fin</b>',
                                editable: false,
                                format: 'd-m-Y',
                                value:'',
                                emptyText: "Seleccione",
                                queryMode: 'remote',
                                anchor: '80%',
                                /*labelWidth: 70,
                                renderTo:'divFechaFin',*/
                                //minValue:new Date(),
                                listeners: {
                                    select: function(cmp, newValue, oldValue) {
                                       validarFechas(cmp);
                                    }
                                }
                            },
                            {
                                xtype: 'timefield',
                                id: 'horaInicio',
                                name:'horaInicio',
                                fieldLabel: '<b>*Hora Inicio</b>',
                                editable: false,
                                minValue: '00:00',
                                maxValue: '24:00',
                                format: 'H:i',
                                value:'',
                                emptyText: "Seleccione",
                                increment: 15,
                                queryMode: 'remote',
                                forceSelection: true,
                                anchor: '80%',
                                listeners: {
                                    select: function(cmp, newValue, oldValue) {
                                        //validarHoras(cmp);
                                    }
                        
                                }
                            },
                            {
                                xtype: 'timefield',
                                id: 'horaFin',
                                name:'horaFin',
                                fieldLabel: '<b>*Hora Fin</b>',
                                editable: false,
                                minValue: '00:00',
                                maxValue: '24:00',
                                format: 'H:i',
                                emptyText: "Seleccione",
                                increment: 15,
                                queryMode: 'remote',
                                anchor: '80%',
                                value:'',
                                listeners: {
                                    select: function(cmp, newValue, oldValue) {
                                        //validarHoras(cmp);
                                    }
                                }
                            },
                            {
                                xtype        : 'combobox',
                                store        :  storeDiaSemana,
                                id           : 'comboDiaSemana1',
                                name         : 'comboDiaSemana1',
                                displayField : 'nombreDia',
                                valueField   : 'idDia',
                                fieldLabel   : '<b>*Dias Semana</b>',
                                anchor       : '80%',
                                queryMode    : "local",
                                plugins      : ['selectedCount1'],
                                disabled     : false,
                                editable     : false,
                                emptyText    : "Seleccione",
                                multiSelect  : true,
                                displayTpl   : '<tpl for="."> {nombreDia} <tpl if="xindex < xcount">, </tpl> </tpl>',
                                listConfig   : {
                                    itemTpl: '{nombreDia} <div class="uncheckedChkbox"></div>'
                                },
                                listeners: {
                                    select: function(cmp, newValue, oldValue) {
                                        
                                    }
                                }
            
                            },
                            
                        ]
                    }
                ],
                buttons:
                [
                    {
                        text: 'Agregar',
                        type: 'submit',
                        handler: function()
                        { 
                            var form = Ext.getCmp('formAgregarEmpleados').getForm();
    
                            if( form.isValid() )
                            {
                                var strNombreTipo  = Ext.getCmp('cmbTipoHorario').getValue();
                                var strFechaInicio = Ext.getCmp('FechaInicio').getValue();
                                var strFechaFin    = Ext.getCmp('FechaFin').getValue();
                                var strhoraInicio  = Ext.getCmp('horaInicio').getValue();
                                var strhoraFin     = Ext.getCmp('horaFin').getValue();
                                var valuedDiasSemana = Ext.getCmp('comboDiaSemana1').getValue().filter(function(valor) {
                                    return valor !== '';
                                  });
                                var boolErrorDatos  = false;
                                
                                if(strNombreTipo == 1)
                                {
                                    if (valuedDiasSemana == "" || valuedDiasSemana == null)
                                    {
                                        boolErrorDatos = true;
                                        Ext.Msg.alert('Error', 'Debe seleccionar un dia de Semana');
                                    }
                                    var checkBox = document.getElementById("checkAsignarAhora");
                                    if (checkBox.checked != true || tareaDepartamento != 'undefined'){
                                        if (strhoraInicio == "" || strhoraInicio == null)
                                        {
                                            boolErrorDatos = true;
                                            Ext.Msg.alert('Error', 'Debe seleccionar Hora de Inicio');
                                        }
                                        if (strhoraFin == "" || strhoraFin == null)
                                        {
                                            boolErrorDatos = true;
                                            Ext.Msg.alert('Error', 'Debe seleccionar Hora Fin');
                                        }
                                    }
                                    if (strFechaFin == "" || strFechaFin == null)
                                    {
                                        boolErrorDatos = true;
                                        Ext.Msg.alert('Error', 'Debe seleccionar Fecha Fin');
                                    }
                                    if (strFechaInicio == "" || strFechaInicio == null)
                                    {
                                        boolErrorDatos = true;
                                        Ext.Msg.alert('Error', 'Debe seleccionar Fecha Inicio');
                                    }
                                    if (strNombreTipo == "" || strNombreTipo == null)
                                    {
                                        boolErrorDatos = true;
                                        Ext.Msg.alert('Error', 'Debe seleccionar un Horario');
                                    }
                                }
                                var formattedValueFechaInicio = Ext.Date.format(strFechaInicio, 'd-m-Y');
                                var formattedValueFechaFin    = Ext.Date.format(strFechaFin, 'd-m-Y');
                                var formattedValueHoraInicio = Ext.Date.format(strhoraInicio, 'H:i');
                                var formattedValueHoraFin    = Ext.Date.format(strhoraFin, 'H:i');
    
                                if ( !boolErrorDatos )
                                {
                                    /*
                                    if(validarLiderCuadrillaExistente(strNombreCargo, intIdPersonaEmpresaRol))
                                    {
                                        var arrayDataEmpleadosAsignados = obtenerIntegrantesCuadrilla();
    
                                        var arrayParametros                             = [];
                                        arrayParametros['intIdPersonalEmpresaRol']  = intIdPersonaEmpresaRol;
                                        arrayParametros['valor']                    = strNombreCargo;
                                        arrayParametros['caracteristica']           = 'CARGO';
                                        arrayParametros['accion']                   = 'Guardar';
                                        arrayParametros['store']                    = storeEmpleadosAsignados;
                                        arrayParametros['dataEmpleadosAsignados']   = arrayDataEmpleadosAsignados;
    
                                        ajaxAsignarCaracteristica(arrayParametros);
                                    }
                                    else
                                    {
                                        Ext.Msg.alert('Atenci\xf3n', 'Debe existir un lider en la cuadrilla.');
                                    }*/
    
                                    var arrayParametros           = new Array();
                                    arrayParametros['motivo'] = '';
                                    arrayParametros['accion'] = 'Agregar';
                                    arrayParametros['grid']   = gridEmpleadosDepartamento;
                                    arrayParametros['store']  = storeEmpleadosDepartamento;
                                    arrayParametros['tipoHorarioId']  = strNombreTipo;
                                    arrayParametros['fechaInicio']  = formattedValueFechaInicio;
                                    arrayParametros['fechaFin']  = formattedValueFechaFin;
                                    arrayParametros['horaInicio']  = formattedValueHoraInicio;
                                    arrayParametros['horaFin']  = formattedValueHoraFin;
                                    arrayParametros['diasSemana']  = JSON.stringify({dias:valuedDiasSemana});
                                    arrayParametros["cambio_estado_emple"] = 'SI';
                        
                                    Ext.Msg.confirm('Alerta','Se agregaran los empleados seleccionados. Desea continuar?', function(btn)
                                    {
                                        if(btn=='yes')
                                        {
                                            var boolTieneLider = false;
                            
                                            for (iteracion = 0; iteracion < xRowSelMod.length; iteracion++) 
                                            {
                                                var strCargoEmpleado = xRowSelMod[iteracion].raw.strCargoTelcos;
                            
                                                if(strCargoEmpleado == 'Lider')
                                                {
                                                    boolTieneLider = true;
                                                    break;
                                                }
                                            }
                            
                                            if(boolTieneLider && validarLiderCuadrillaExistente('Todo', 0))
                                            {
                                                Ext.Msg.alert('Error', 'Esta cuadrilla ya tiene asignado un lider');
                                            }
                                            else
                                            {
                                                if(boolDepConfigHE)
                                                {
                                                    enviarTramaPaquete(arrayParametros);
                                                }
                                                else
                                                {
                                                    cambiarEstadosEmpleados(arrayParametros);
                                                }
                                            }
                                        }
                                    });
                                }
                                /*else
                                {
                                    Ext.Msg.alert('Atenci\xf3n', 'Debe seleccionar un Tipo Horario');
                                }*/
                            }
                        }
                    },
                    {
                        text: 'Cerrar',
                        handler: function()
                        {
                            winAgregar.destroy();
                        }
                    }
                ]
            });
    
            winAgregar = Ext.create('Ext.window.Window',
              {
                   title: 'Agregar empleados',
                   modal: true,
                   width: 350,
                   closable: true,
                   layout: 'fit',
                   items: [formPanel]
              }).show();
        }
        else
        {
            var arrayParametros           = new Array();
            arrayParametros['motivo'] = '';
            arrayParametros['accion'] = 'Agregar';
            arrayParametros['grid']   = gridEmpleadosDepartamento;
            arrayParametros['store']  = storeEmpleadosDepartamento;

            Ext.Msg.confirm('Alerta','Se agregaran los empleados seleccionados. Desea continuar?', function(btn)
            {
                if(btn=='yes')
                {
                    var boolTieneLider = false;

                    for (iteracion = 0; iteracion < xRowSelMod.length; iteracion++) 
                    {
                        var strCargoEmpleado = xRowSelMod[iteracion].raw.strCargoTelcos;

                        if(strCargoEmpleado == 'Lider')
                        {
                            boolTieneLider = true;
                            break;
                        }
                    }

                    if(boolTieneLider && validarLiderCuadrillaExistente('Todo', 0))
                    {
                        Ext.Msg.alert('Error', 'Esta cuadrilla ya tiene asignado un lider');
                    }
                    else
                    {
                        cambiarEstadosEmpleados(arrayParametros);
                    }
                }
            });

        }

    }
    else
    {
        Ext.Msg.alert('Error', 'Debe seleccionar empleados para que sean agregados');
    }
}

function cambiarEstadosEmpleados(arrayParametros)
{
    var xRowSelMod    = arrayParametros['grid'].getSelectionModel().getSelection();
    var intTotal      = 0;
    var boolContinuar = true;

    if( arrayParametros['accion'] == 'Eliminar')
    {
        intTotal = arrayParametros['store'].getCount();

        if( xRowSelMod.length >= intTotal )
        {
            boolContinuar = false;
        }
    }

    if( boolContinuar )
    {
        if(validarEliminarLiderCuadrilla(xRowSelMod))
        {
            var array_integrantes                = new Object();
            array_integrantes['total']       = xRowSelMod.length;
            array_integrantes['encontrados'] = new Array();

            Ext.get('empleados_integrantes').dom.value = "";

            var array_data = new Array();

            for (var i = 0; i < xRowSelMod.length; i++)
            {
                var RowSel = xRowSelMod[i];

                array_data.push({ 'intIdPersonaEmpresaRol': RowSel.get('intIdPersonaEmpresaRol')});
            }

            array_integrantes['encontrados'] = array_data;

            Ext.get('empleados_integrantes').dom.value = Ext.JSON.encode(array_integrantes);

            connEsperaAccion.request
            ({
                url: strUrlCambioEstadoEmpleados,
                method: 'post',
                dataType: 'json',
                params:
                { 
                    intIdCuadrilla: intIdCuadrilla,
                    strEmpleados: Ext.get('empleados_integrantes').dom.value,
                    intIdMotivo: arrayParametros['motivo'],
                    strAccion: arrayParametros['accion']
                },
                success: function(response)
                {
                    if ( typeof win != 'undefined' && win != null )
                    {
                        win.destroy();
                    }

                    if( "OK" == response.responseText )
                    {
                        Ext.Msg.alert('Información', 'Se han realizado los cambios respectivos en la cuadrilla');

                        storeEmpleadosAsignados.load();
                        limpiar('empleadosDepartamento');
                    }
                    else
                    {
                        Ext.Msg.alert('Error', response.responseText);
                    }
                },
                failure: function(result)
                {
                    if ( typeof win != 'undefined' && win != null )
                    {
                        win.destroy();
                    }

                    Ext.Msg.alert('Error',result.responseText); 
                }
            });

            Ext.get('empleados_integrantes').dom.value = "";
        }
        else
        {
            Ext.Msg.alert('Atenci\xf3n', 'Debe existir un lider en la cuadrilla.');
        }
    }
    else
    {
        if( arrayParametros['accion'] == 'Eliminar')
        {
            Ext.Msg.alert('Error', 'La cuadrilla no puede quedar vacía'); 
        }
    }
}

function cambiarEstadosEmpleados1(arrayParametros)
{
    var xRowSelMod    = arrayParametros['grid'].getSelectionModel().getSelection();
    var intTotal      = 0;
    var boolContinuar = true;

    if( arrayParametros['accion'] == 'Eliminar')
    {
        intTotal = arrayParametros['store'].getCount();

        if( xRowSelMod.length >= intTotal )
        {
            boolContinuar = false;
        }
    }
    if (typeof winAgregar != 'undefined' && winAgregar != null)
    {
        winAgregar.destroy();
    }

    if( boolContinuar )
    {
        if(validarEliminarLiderCuadrilla(xRowSelMod))
        {
            var array_integrantes                = new Object();
            array_integrantes['total']       = xRowSelMod.length;
            array_integrantes['encontrados'] = new Array();

            Ext.get('empleados_integrantes').dom.value = "";

            var array_data = new Array();

            for (var i = 0; i < xRowSelMod.length; i++)
            {
                var RowSel = xRowSelMod[i];

                array_data.push({ 'intIdPersonaEmpresaRol': RowSel.get('intIdPersonaEmpresaRol')});
            }

            array_integrantes['encontrados'] = array_data;

            Ext.get('empleados_integrantes').dom.value = Ext.JSON.encode(array_integrantes);
            arrayParametros['Empleados'] = Ext.get('empleados_integrantes').dom.value;
            
            connEsperaAccion.request({
                url: strUrlCambioEstadoEmpleados,
                method: 'post',
                dataType: 'json',
                params:
                { 
                    intIdCuadrilla: intIdCuadrilla,
                    strEmpleados: Ext.get('empleados_integrantes').dom.value,
                    intIdMotivo: arrayParametros['motivo'],
                    strAccion: arrayParametros['accion'],
                    strTipoHorarioId: arrayParametros['tipoHorarioId'],
                    strFechaInicio: arrayParametros['fechaInicio'],
                    strFechaFin: arrayParametros['fechaFin'],
                    strhoraInicio: arrayParametros['horaInicio'],
                    strhoraFin: arrayParametros['horaFin'],
                    arrayDiaSemana: arrayParametros['diasSemana']
                },
                success: function(response)
                {
                    if ( typeof win != 'undefined' && win != null )
                    {
                        win.destroy();
                    }

                    if( "OK" == response.responseText )
                    {
                        Ext.Msg.alert('Información', 'Se han realizado los cambios respectivos en la cuadrilla');

                        storeEmpleadosAsignados.load();
                        limpiar('empleadosDepartamento');
                    }
                    else
                    {
                        Ext.Msg.alert('Error', response.responseText);
                    }
                },
                failure: function(result)
                {
                    if ( typeof win != 'undefined' && win != null )
                    {
                        win.destroy();
                    }

                    Ext.Msg.alert('Error',result.responseText); 
                }
            });

            Ext.get('empleados_integrantes').dom.value = "";
        }
        else
        {
            Ext.Msg.alert('Atenci\xf3n', 'Debe existir un lider en la cuadrilla.');
        }
    }
    else
    {
        if( arrayParametros['accion'] == 'Eliminar')
        {
            Ext.Msg.alert('Error', 'La cuadrilla no puede quedar vacía'); 
        }
    }
}

function validarHoras(cmp)
{
    var fieldHoraDesdeAsignacion = Ext.getCmp('horaInicioTurno');
    var valueHoraDesdeAsignacion = fieldHoraDesdeAsignacion.getValue();
    var formattedValueHoraDesdeAsignacion = Ext.Date.format(valueHoraDesdeAsignacion, 'H:i');

    var fieldHoraHastaAsignacion = Ext.getCmp('horaFinTurno');
    var valueHoraHastaAsignacion = fieldHoraHastaAsignacion.getValue();
    var formattedValueHoraHastaAsignacion = Ext.Date.format(valueHoraHastaAsignacion, 'H:i');

    var boolOKHoras = true;
    var boolOKFechas = true;
    var boolCamposLLenos=false;
    var strMensaje  = '';

    if (boolDepConfigHE)
    {
        var fieldFechaDesdeAsignacion = Ext.getCmp('txtFechaInicio');
        var valueFechaDesdeAsignacion = fieldFechaDesdeAsignacion.getValue();
        var formattedValueFechaDesdeAsignacion = Ext.Date.format(valueFechaDesdeAsignacion, 'd-m-Y');

        var fieldFechaHastaAsignacion = Ext.getCmp('txtFechaFin');
        var valueFechaHastaAsignacion = fieldFechaHastaAsignacion.getValue();
        var formattedValueFechaHastaAsignacion = Ext.Date.format(valueFechaHastaAsignacion, 'd-m-Y');
        if(valueFechaDesdeAsignacion && valueFechaHastaAsignacion)
        {
            if(formattedValueFechaDesdeAsignacion>formattedValueFechaHastaAsignacion)
            {
                boolOKFechas=false;
                strMensaje='La Fecha Inicio '+ formattedValueHoraDesdeAsignacion +' no puede ser mayor a la Fecha Fin '+formattedValueHoraHastaAsignacion;
                Ext.Msg.alert('Atenci\xf3n', strMensaje);
            }
        }
        if(valueHoraDesdeAsignacion && valueHoraHastaAsignacion && valueFechaDesdeAsignacion && valueFechaHastaAsignacion)
        {   
            boolCamposLLenos=true;
            var tempFechaDesdeAsigHoras = Ext.getCmp('txtFechaInicio').getValue();
            var tempFechaHastaAsigHoras = Ext.getCmp('txtFechaInicio').getValue();

            tempFechaDesdeAsigHoras.setHours(valueHoraDesdeAsignacion.getHours());          
            tempFechaDesdeAsigHoras.setMinutes(valueHoraDesdeAsignacion.getMinutes()); 
            tempFechaHastaAsigHoras.setHours(valueHoraHastaAsignacion.getHours());          
            tempFechaHastaAsigHoras.setMinutes(valueHoraHastaAsignacion.getMinutes()); 
            if(tempFechaHastaAsigHoras <= tempFechaDesdeAsigHoras)
            {
                tempFechaHastaAsigHoras.setDate(tempFechaDesdeAsigHoras.getDate() + 1);  
            }
            var arrHrsMin = calcularHoras(tempFechaDesdeAsigHoras, tempFechaHastaAsigHoras);
            var boolEsMayorHorasLimit = arrHrsMin[0] >= 8 && (arrHrsMin[0] < 9 || (arrHrsMin[0] == 9 && arrHrsMin[1] == 0))? false:true;
            if(boolEsMayorHorasLimit)
            {
                boolOKFechas=false;
                strMensaje='El rango de horas que intenta ingresar no es correcto';
                Ext.Msg.alert('Atenci\xf3n', strMensaje);
            }
        }
        
    }
    else
    {
        if(valueHoraDesdeAsignacion && valueHoraHastaAsignacion)
        {
            boolCamposLLenos=true;
        }
        if(valueHoraDesdeAsignacion && valueHoraHastaAsignacion)
        {
            if(formattedValueHoraDesdeAsignacion==formattedValueHoraHastaAsignacion)
            {
                boolOKHoras=false;
                strMensaje='La Hora Inicio '+ formattedValueHoraDesdeAsignacion +' no puede ser igual a la Hora Fin '+formattedValueHoraHastaAsignacion;
                Ext.Msg.alert('Atenci\xf3n', strMensaje);
            }
            else if(formattedValueHoraDesdeAsignacion>formattedValueHoraHastaAsignacion)
            {
                boolOKHoras=false;
                strMensaje='La Hora Inicio '+ formattedValueHoraDesdeAsignacion +' no puede ser mayor a la Hora Fin '+formattedValueHoraHastaAsignacion;
                Ext.Msg.alert('Atenci\xf3n', strMensaje);
            }
        }
    }

    if( boolCamposLLenos && ((boolOKHoras && !boolDepConfigHE) || (boolOKFechas && boolDepConfigHE)) )
    {
        return true;
    }
    else
    {
        if(cmp && boolCamposLLenos)
        {
            cmp.value = "";
            cmp.setRawValue("");
        }
        
        return false;
    }
}

function calcularHoras(fechaIni, fechaFin) 
{
    
    var diferenciaHoras = (fechaFin - fechaIni);
    var difHrs = Math.floor((diferenciaHoras % 86400000) / 3600000); // hours
    var difMins = Math.round(((diferenciaHoras % 86400000) % 3600000) / 60000); // minutes
    return [difHrs, difMins];
}

function validarFechas(cmp)
{
    var fieldFechaDesdeAsignacion = Ext.getCmp('FechaInicio');
    var valueFechaDesdeAsignacion = fieldFechaDesdeAsignacion.getValue();
    var formattedValueFechaDesdeAsignacion = Ext.Date.format(valueFechaDesdeAsignacion, 'd-m-Y');

    var fieldFechaHastaAsignacion = Ext.getCmp('FechaFin');
    var valueFechaHastaAsignacion = fieldFechaHastaAsignacion.getValue();
    var formattedValueFechaHastaAsignacion = Ext.Date.format(valueFechaHastaAsignacion, 'd-m-Y');
    var boolOKFechas = true;
    var boolCamposLLenos=false;
    var strMensaje  = '';

    if( valueFechaDesdeAsignacion && valueFechaHastaAsignacion)
    {
        boolCamposLLenos=true;
    }

    if(valueFechaDesdeAsignacion && valueFechaHastaAsignacion)
    {
        if(formattedValueFechaDesdeAsignacion>formattedValueFechaHastaAsignacion)
        {
            boolOKFechas=false;
            strMensaje='La Fecha Inicio '+ formattedValueHoraDesdeAsignacion +' no puede ser mayor a la Fecha Fin '+formattedValueHoraHastaAsignacion;
            Ext.Msg.alert('Atenci\xf3n', strMensaje);
        }
        else if (formattedValueFechaDesdeAsignacion == formattedValueFechaHastaAsignacion)
        {
            Ext.getCmp('comboDiaSemana1').disable();
            Ext.getCmp('comboDiaSemana1').setValue((valueFechaDesdeAsignacion.getDay()+1).toString());
        }
        else
        {
            habilitarCamposAgregar();

            /*Ext.getCmp('FechaInicio').setRawValue("");
            Ext.getCmp('FechaFin').setRawValue("");*/
            Ext.getCmp('comboDiaSemana1').setValue(null);
        }
        
    }

    if( boolCamposLLenos && boolOKFechas)
    {
        return true;
    }
    else
    {
        if(cmp && boolCamposLLenos)
        {
            cmp.value = "";
            cmp.setRawValue("");
        }

        return false;
    }
}

function asignarTablet(intIdPersonaEmpresaRol)
{        
    var storeModelosTablet = new Ext.data.Store
        ({
            total: 'total',
            pageSize: 200,
            proxy:
            {
                type: 'ajax',
                method: 'post',
                url: strUrlGetModelosTablet,
                timeout: 9000000,
                reader:
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            fields:
            [
                {name: 'strIdentificacion', mapping: 'strIdentificacion'},
                {name: 'strDescripcion',    mapping: 'strDescripcion'}
            ],
            listeners: 
            {
                load: function(store, records)
                {
                     store.insert(0,[{ strIdentificacion: '', strDescripcion: 'Seleccione' }]);
                }      
            }
        });

    var storeTablets = new Ext.data.Store
        ({
            total: 'total',
            pageSize: 200,
            proxy:
            {
                type: 'ajax',
                method: 'post',
                url: strUrlGetTablets,
                timeout: 9000000,
                reader:
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams:
                {
                    strCategoria: strCategoriaTablet,
                    strMostrarElementosAsignados: 'N'
                }
            },
            fields:
            [
                {name: 'intIdElemento',     mapping: 'intIdElemento'},
                {name: 'strNombreElemento', mapping: 'strNombreElemento'}
            ],
            listeners: 
            {
                load: function(store, records)
                {
                     store.insert(0,[{ strNombreElemento: 'Sin asignación', intIdElemento: '' }]);
                }      
            }
        });

    var cmbModelosTablet = Ext.create('Ext.form.ComboBox',
        {
            id: 'cmbModeloTablet',
            name: 'cmbModeloTablet',
            fieldLabel: '<b>Modelo Tablet</b>',
            emptyText: "Seleccione",
            triggerAction: 'all',
            selectOnTab: true,
            store: storeModelosTablet,
            displayField: 'strDescripcion',
            valueField: 'strIdentificacion',             
            lazyRender: true,
            queryMode: "remote",
            listClass: 'x-combo-list-small',
            listeners:
            {
                select:
                {
                    fn:function(comp, record, index)
                    {
                        if (comp.getValue() === "" || comp.getRawValue() === "&nbsp;")
                        {
                            comp.setValue(null);

                            Ext.getCmp('cmbTablet').reset();
                            Ext.getCmp('cmbTablet').setDisabled(true);
                        }
                        else
                        {
                            Ext.getCmp('cmbTablet').reset();
                            Ext.getCmp('cmbTablet').setDisabled(false);

                            var objExtraParams = storeTablets.proxy.extraParams;

                            objExtraParams.strModeloElemento = comp.getValue();

                            storeTablets.load([], false);
                        }
                    }
                }
            }
        });

    var cmbTablets = Ext.create('Ext.form.ComboBox',
        {
            fieldLabel: '<b>Tablet Nueva<b/>',
            id: 'cmbTablet',
            name: 'cmbTablet',
            triggerAction: 'all',
            selectOnTab: true,
            store: storeTablets,
            displayField: 'strNombreElemento',
            valueField: 'intIdElemento',             
            lazyRender: true,
            queryMode: 'remote',
            emptyText: 'Seleccione',
            listClass: 'x-combo-list-small',
            disabled: true,
            forceSelection:true
        });

    var formPanel = Ext.create('Ext.form.Panel',
    {
        id: 'formAsignarTablet',
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: 
        {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side'
        },
        items: 
        [
            {
                xtype: 'fieldset',
                title: '',
                defaultType: 'textfield',
                defaults:
                {
                    width: 300
                },
                items:
                [
                    cmbModelosTablet,
                    cmbTablets
                ]
            }
        ],
        buttons:
        [
            {
                text: 'Asignar',
                type: 'submit',
                handler: function()
                {
                    var form = Ext.getCmp('formAsignarTablet').getForm();

                    if( form.isValid() )
                    {
                        var intIdTablet = Ext.getCmp('cmbTablet').getValue();

                        if ( intIdTablet != null )
                        {
                            var tabletSeleccionada = intIdTablet;

                            Ext.Msg.confirm('Alerta','Está seguro que desea asignar la tablet seleccionada. Desea continuar?', function(btn)
                            {
                                if(btn=='yes')
                                {
                                    connEsperaAccion.request
                                    ({
                                        url: strUrlAsignarTablet,
                                        method: 'post',
                                        dataType: 'json',
                                        params:
                                        { 
                                            idAsociado : intIdPersonaEmpresaRol,
                                            elemento: tabletSeleccionada,
                                            strCategoria: strCategoriaTablet

                                        },
                                        success: function(result)
                                        {
                                            if ( typeof win != 'undefined' && win != null )
                                            {
                                                win.destroy();
                                            }

                                            if( "OK" == result.responseText  )
                                            {
                                                Ext.Msg.alert('Información ', 'Se asigna una nueva tablet con éxito');

                                                storeEmpleadosAsignados.load();
                                            }
                                            else
                                            {
                                                Ext.Msg.alert('Error ', result.responseText);
                                            }
                                        },
                                        failure: function(result)
                                        {
                                            Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                        }
                                    });
                                }
                            });
                        }
                        else
                        {
                            Ext.Msg.alert('Atenci\xf3n', 'Debe seleccionar una tablet');
                        }
                    }
                }
            },
            {
                text: 'Cerrar',
                handler: function()
                {
                    win.destroy();
                }
            }
        ]
    });

    win = Ext.create('Ext.window.Window',
          {
               title: 'Asignar Tablet',
               modal: true,
               width: 350,
               closable: true,
               layout: 'fit',
               items: [formPanel]
          }).show();
}

function existeYaUnChofer()
{
    var existeUnChofer = false;
    var num = gridEmpleadosAsignaciones.getStore().getCount();
    for(var i=0; i < num ; i++)
    {
        var strCargoTelcos  = gridEmpleadosAsignaciones.getStore().getAt(i).get('strCargo');
        var strCargoNaF     = gridEmpleadosAsignaciones.getStore().getAt(i).get('strCargoTelcos');
        if(strCargoTelcos=='Chofer' || strCargoNaF=='Chofer')
        {
            existeUnChofer=true;
        }
    }


    return existeUnChofer;	
}

function existenChoferes()
{
    var existenChoferes = false;
    var num = gridEmpleadosAsignaciones.getStore().getCount();
    var contChoferes=0;
    for(var i=0; i < num ; i++)
    {
        var strCargoTelcos  = gridEmpleadosAsignaciones.getStore().getAt(i).get('strCargo');
        var strCargoNaF     = gridEmpleadosAsignaciones.getStore().getAt(i).get('strCargoTelcos');
        if(strCargoTelcos=='Chofer' || strCargoNaF=='Chofer')
        {
            contChoferes++;
        }
    }
    
    if(contChoferes>0)
    {
        existenChoferes=true;
    }


    return existenChoferes;	
}

function validarLiderCuadrillaExistente(strNombreCargo, intIdPersonaEmpresaRol)
{
    var boolResponse = false

    if(strNombreCargo != 'Lider')
    {
        for (iteracion = 0; iteracion < storeEmpleadosAsignados.data.length; iteracion++) {
            var strCargoEmpleado                = storeEmpleadosAsignados.data.items[iteracion].data.strCargoTelcos;
            var intIdPersonaEmpresaRolEmpleado  = storeEmpleadosAsignados.data.items[iteracion].data.intIdPersonaEmpresaRol;

            if(strCargoEmpleado == 'Lider' && (intIdPersonaEmpresaRolEmpleado != intIdPersonaEmpresaRol || intIdPersonaEmpresaRol == 0))
            {
                boolResponse = true;
                break;
            }
        }
    }
    else
    {
        boolResponse = true;
    }

    return boolResponse;
}

function validarEliminarLiderCuadrilla(arrayEmpleadosSeleccionados)
{
    var boolResponse = true

    for (iteracion = 0; iteracion < arrayEmpleadosSeleccionados.length; iteracion++) {
        var strCargoEmpleado = arrayEmpleadosSeleccionados[iteracion].data.strCargoTelcos;
        if(strCargoEmpleado == 'Lider')
        {
            boolResponse = false;
            break;
        }
    }
    
    return boolResponse;
}

function obtenerIntegrantesCuadrilla()
{
    var dataEmpleadosAsignados = [];

    for (iteracion = 0; iteracion < storeEmpleadosAsignados.data.length; iteracion++) 
    {
        var data = {
            strCargoTelcos: storeEmpleadosAsignados.data.items[iteracion].data.strCargoTelcos, 
            intIdPersonaEmpresaRol: storeEmpleadosAsignados.data.items[iteracion].data.intIdPersonaEmpresaRol
        }; 

        dataEmpleadosAsignados.push(data);  
    }

    return JSON.stringify(dataEmpleadosAsignados);
}

function enviarTramaPaquete(arrayParametros)
{
    Ext.MessageBox.wait("Verificando datos...");
    var xRowSelMod    = arrayParametros['grid'].getSelectionModel().getSelection();
    var intTotal      = 0;
    var boolContinuar = true;

    if( arrayParametros['accion'] == 'Eliminar')
    {
        intTotal = arrayParametros['store'].getCount();

        if( xRowSelMod.length >= intTotal )
        {
            boolContinuar = false;
        }
    }
    if (typeof winAgregar != 'undefined' && winAgregar != null)
    {
        winAgregar.destroy();
    }

    if( boolContinuar )
    {
        if(validarEliminarLiderCuadrilla(xRowSelMod))
        {
            var array_integrantes                = new Object();
            array_integrantes['total']       = xRowSelMod.length;
            array_integrantes['encontrados'] = new Array();

            Ext.get('empleados_integrantes').dom.value = "";

            var array_data = new Array();

            for (var i = 0; i < xRowSelMod.length; i++)
            {
                var RowSel = xRowSelMod[i];

                array_data.push({ 'intIdPersonaEmpresaRol': RowSel.get('intIdPersonaEmpresaRol')});
            }

            array_integrantes['encontrados'] = array_data;
            if(arrayParametros["cambio_estado_emple"] == 'NO')
            {
                array_integrantes['encontrados'] = JSON.parse(obtenerIntegrantesCuadrilla());
                array_integrantes['total']       = array_integrantes['encontrados'].length;
                arrayParametros['Empleados']     = JSON.stringify(array_integrantes);
                Ext.get('empleados_integrantes').dom.value = Ext.JSON.encode(array_integrantes);
            }
            else
            {
                Ext.get('empleados_integrantes').dom.value = Ext.JSON.encode(array_integrantes);
                arrayParametros['Empleados'] = Ext.get('empleados_integrantes').dom.value;
            }

            Ext.Ajax.request
            ({
                url: '../ajaxPlanificacionCuadrilla',
                method: 'post',
                params: 
                { 
                    intIdCuadrilla   : intIdCuadrilla,
                    strEmpleados     : arrayParametros['Empleados'] ,
                    accion           : arrayParametros['accion'],
                    strFechaInicio   : arrayParametros['fechaInicio'],
                    strFechaFin      : arrayParametros['fechaFin'],
                    strHoraInicio    : arrayParametros['horaInicio'],
                    strHoraFin       : arrayParametros['horaFin'],
                    cmbTipoHorario1  : arrayParametros['tipoHorarioId'],
                    comboDiaSemana1  : arrayParametros["diasSemana"],
                },
                success: function(response)
                {
                    var text = JSON.parse(response.responseText);
                    if(text.status === "OK")
                    {   
                        if(arrayParametros["cambio_estado_emple"] == 'SI')
                        {
                            cambiarEstadosEmpleados1(arrayParametros);
                        }
                        else
                        {
                            Ext.Ajax.request
                            ({
                                url: strUrlVerificarVehiculoCuadrilla,
                                method: 'post',
                                params: 
                                { 
                                    strHoraDesdeTurno      : arrayParametros['horaInicio'],
                                    strHoraHastaTurno      : arrayParametros['horaFin'],
                                    strFechaInicioTurno    : arrayParametros['fechaInicio'],
                                    strFechaFinTurno       : arrayParametros['fechaFin']
                                },
                                success: function(response)
                                {
                                    var text = response.responseText;
                        
                                    if(text === "OK")
                                    {
                                        $("#horaInicioTurnoCuadrilla").val(arrayParametros['horaInicio']);
                                        $("#horaFinTurnoCuadrilla").val(arrayParametros['horaFin']);
                                        $("#fechaInicioTurnoCuadrilla").val(arrayParametros['fechaInicio']);
                                        $("#fechaFinTurnoCuadrilla").val(arrayParametros['fechaFin']);
                                        $("#diasSemana").val(arrayParametros['diasSemana1']);
                                        document.getElementById("form_new_proceso").submit();
                                    }
                                    else
                                    {
                                        Ext.MessageBox.hide();
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

                    }
                    else
                    {
                        if ( typeof win != 'undefined' && win != null )
                        {
                            win.destroy();
                        }
                        
                        Ext.MessageBox.hide();
                        Ext.Msg.alert('Error', text.mensaje); 
                    }
                },
                failure: function(result)
                {
                    if ( typeof win != 'undefined' && win != null )
                    {
                        win.destroy();
                    }
                    
                    Ext.MessageBox.hide();
                    Ext.Msg.alert('Error',result.responseText);
                }
            });

            //Ext.get('empleados_integrantes').dom.value = "";
        }
        else
        {
            Ext.Msg.alert('Atenci\xf3n', 'Debe existir un lider en la cuadrilla.');
        }
    }
    else
    {
        if( arrayParametros['accion'] == 'Eliminar')
        {
            Ext.Msg.alert('Error', 'La cuadrilla no puede quedar vacía'); 
        }
    }
    
    
    return false;
}

function verificarPlanificacion(arrayParametros)
{
    var xRowSelMod    = arrayParametros['grid'].getSelectionModel().getSelection();
    var intTotal      = 0;
    var boolContinuar = true;

    if( arrayParametros['accion'] == 'Eliminar')
    {
        intTotal = arrayParametros['store'].getCount();

        if( xRowSelMod.length >= intTotal )
        {
            boolContinuar = false;
        }
    } 

    if( boolContinuar )
    {
        if(validarEliminarLiderCuadrilla(xRowSelMod))
        {
            var array_integrantes                = new Object();
            array_integrantes['total']       = xRowSelMod.length;
            array_integrantes['encontrados'] = new Array();

            Ext.get('empleados_integrantes').dom.value = "";

            var array_data = new Array();

            for (var i = 0; i < xRowSelMod.length; i++)
            {
                var RowSel = xRowSelMod[i];

                array_data.push({ 'intIdPersonaEmpresaRol': RowSel.get('intIdPersonaEmpresaRol')});
            }

            array_integrantes['encontrados'] = array_data;

            Ext.get('empleados_integrantes').dom.value = Ext.JSON.encode(array_integrantes);
            arrayParametros['Empleados'] = Ext.get('empleados_integrantes').dom.value;

            connEsperaAccion.request
            ({
                url: '../ajaxVerificarPlanificacion',
                method: 'post',
                dataType: 'json',
                params:
                { 
                    intIdCuadrilla   : intIdCuadrilla,
                    strEmpleados     : arrayParametros['Empleados'] ,
                    accion           : arrayParametros['accion']
                },
                success: function(response)
                {
                    var text = JSON.parse(response.responseText);
                    if(text.status === "OK")
                    {
                        //cambioEstadoCuadrilla(arrayParametros);
                        cambiarEstadosEmpleados1(arrayParametros);
                    }
                    else
                    {
                        if ( typeof win != 'undefined' && win != null )
                        {
                            win.destroy();
                        }
                        Ext.MessageBox.hide();
                        Ext.Msg.alert('Error', text.mensaje); 
                    }
                },
                failure: function(result)
                {
                    if ( typeof win != 'undefined' && win != null )
                    {
                        win.destroy();
                    }
                    
                    Ext.MessageBox.hide();
                    Ext.Msg.alert('Error',result.responseText);
                }
            });

            Ext.get('empleados_integrantes').dom.value = "";
        }
        else
        {
            Ext.Msg.alert('Atenci\xf3n', 'Debe existir un lider en la cuadrilla.');
        }
    }
    else
    {
        if( arrayParametros['accion'] == 'Eliminar')
        {
            Ext.Msg.alert('Error', 'La cuadrilla no puede quedar vacía'); 
        }
    }

    return false;
}

function habilitarCamposAgregar()
{
    Ext.getCmp('FechaInicio').enable();
    Ext.getCmp('FechaFin').enable();
    Ext.getCmp('horaInicio').enable(); 
    Ext.getCmp('horaFin').enable(); 
    Ext.getCmp('comboDiaSemana1').enable();
}

function validacionCampoLlenos() 
{
    var fieldHoraDesdeTurno = Ext.getCmp('horaInicioTurno');
    var valueHoraDesdeTurno = fieldHoraDesdeTurno.getValue();
    var formattedValueHoraDesdeTurno = Ext.Date.format(valueHoraDesdeTurno, 'H:i');
    var fieldHoraHastaTurno = Ext.getCmp('horaFinTurno');
    var valueHoraHastaTurno = fieldHoraHastaTurno.getValue();
    var formattedValueHoraHastaTurno = Ext.Date.format(valueHoraHastaTurno, 'H:i');
    var integrantes = gridEmpleadosAsignaciones.getStore().getCount();
    var strMensaje='';
    if (boolDepConfigHE)
    {
        var fieldFechaDesdeTurno = Ext.getCmp('txtFechaInicio');
        var valueFechaDesdeTurno = fieldFechaDesdeTurno.getValue();
    
        var fieldFechaHastaTurno = Ext.getCmp('txtFechaFin');
        var valueFechaHastaTurno = fieldFechaHastaTurno.getValue();
        var fieldDiasSemana = Ext.getCmp('comboDiaSemana');
        var valueDiasSemana = fieldDiasSemana.getValue();
        
        if(valueDiasSemana.length == 0 && boolDepConfigHE)
        {
            strMensaje='Los días de la semana no puede estar vacía';
            Ext.Msg.alert('Atenci\xf3n', strMensaje);
            //return {respuesta:false, mensaje: strMensaje};
            return false;
        }
    }

    if(!valueHoraDesdeTurno)
    {
        strMensaje='La Hora Inicio no puede estar vacía';
        Ext.Msg.alert('Atenci\xf3n', strMensaje);
        //return {respuesta:false, mensaje: strMensaje};
        return false;
    }
    else if(!valueHoraHastaTurno)
    {
        strMensaje='La Hora Fin no puede estar vacía';
        Ext.Msg.alert('Atenci\xf3n', strMensaje);
        //return {respuesta:false, mensaje: strMensaje};
        return false
    }
    else if(!valueFechaDesdeTurno && boolDepConfigHE)
    {
        strMensaje='La Fecha Inicio no puede estar vacía';
        Ext.Msg.alert('Atenci\xf3n', strMensaje);
        //return {respuesta:false, mensaje: strMensaje};
        return false;
    }
    else if(!valueFechaHastaTurno && boolDepConfigHE)
    {
        strMensaje='La Fecha Fin no puede estar vacía';
        Ext.Msg.alert('Atenci\xf3n', strMensaje);
        //return {respuesta:false, mensaje: strMensaje};
        return false;
    }
    else if(valueHoraDesdeTurno && valueHoraHastaTurno && !boolDepConfigHE)
    {
        if(formattedValueHoraDesdeTurno==formattedValueHoraHastaTurno)
        {
            strMensaje='La Hora Inicio '+ formattedValueHoraDesdeTurno +' no puede ser igual a la Hora Fin '+formattedValueHoraHastaTurno;
            Ext.Msg.alert('Atenci\xf3n', strMensaje);
            return false;
        }
        else if(formattedValueHoraDesdeTurno>formattedValueHoraHastaTurno)
        {
            strMensaje='La Hora Inicio '+ formattedValueHoraDesdeTurno +' no puede ser mayor a la Hora Fin '+formattedValueHoraHastaTurno;
            Ext.Msg.alert('Atenci\xf3n', strMensaje);
            return false;
        }
        else
        {
            return true;
        }
    }
    else if(integrantes == 0)
    {
        Ext.MessageBox.hide();
        Ext.Msg.alert('Error', 'No se han registrado los integrantes');
        return false;
    }
    else if(!validarLiderCuadrillaExistente('Todo', 0))
    {
        Ext.MessageBox.hide();
        Ext.Msg.alert('Atenci\xf3n', 'Debe existir un lider en la cuadrilla.');
        return false;
    }
    else
    {
        //return {respuesta:true, mensaje: ''};
        return true;
    }
   
}
