var storeEmpleadosDepartamento = null;
var storeEmpleadosAsignados    = null;
var modelStoreEmpDepartamento  = null;
var modelStoreEmpAsignados     = null;
var gridEmpleadosDepartamento  = null;
var gridEmpleadosAsignaciones  = null;

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
    if (boolDepConfigHE == true)
    {
        
        DTFechaDesde = new Ext.form.DateField({
            xtype: 'datefield',
            id: 'txtFechaInicio',
            name: 'txtFechaInicio',
            fieldLabel: '<b>Fecha Inicio</b>',
            editable: false,
            format: 'd-m-Y',
            value:'',
            emptyText: "Seleccione",
            labelWidth: 70,
            renderTo:'divFechaInicio',
            minValue:new Date(),
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
            value:'',
            emptyText: "Seleccione",
            labelWidth: 70,
            renderTo:'divFechaFin',
            minValue:new Date(),
            listeners: {
                select: function(cmp, newValue, oldValue) {
                    validarHoras(cmp);
                }
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
        value:'',
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
        value:'',
        renderTo:'divHoraFinTurno',
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
        width: 480,
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
                width: 250,
                sortable: true
            },
            {
                header: 'Cargo',
                dataIndex: 'strCargo',
                width: 120,
                sortable: true
            },
            {
                header: 'Estado',
                dataIndex: 'strEstadoEmpleado',
                width: 94,
                sortable: true
            }
        ],
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
                name: 'strEstadoEmpleado',
                mapping: 'strEstadoEmpleado',
                type: 'string', 
            }
        ],
        idProperty: 'intIdPersonaEmpresaRol'
    });
    
    storeEmpleadosAsignados = new Ext.data.Store
    ({
        model: 'ListaEmpleadosAsignadosModel'
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
                width: 250,
                sortable: true
            },
            {
                header: 'Cargo',
                dataIndex: 'strCargo',
                width: 120,
                sortable: true
            },
            {
                header: 'Estado',
                dataIndex: 'strEstadoEmpleado',
                width: 94,
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
                            eliminarSeleccion(gridEmpleadosAsignaciones);
                        }
                    }
                ]
            }
        ],
        title: 'Empleados Asignados a la Cuadrilla',
        renderTo: 'gridEmpleadosAsignaciones'
    });
    
    Ext.define('modelDiaSemana', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idDia', type: 'string'},
            {name: 'nombreDia', type: 'string'}
        ]
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
    
    
    //combo dias de la semana labora cuadrilla
    comboDiasSemana = new Ext.form.ComboBox({
        xtype        : 'combobox',
        store        :  storeDiaSemana,
        id           : 'comboDiaSemana',
        name         : 'comboDiaSemana',
        displayField : 'nombreDia',
        valueField   : 'idDia',
        fieldLabel   : '<b>Dias Semana </b>',
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
            var strEstadoEmpleado      = RowSel.get('strEstadoEmpleado');

            var r = Ext.create('ListaEmpleadosAsignadosModel', 
            {
                intIdPersonaEmpresaRol: intIdPersonaEmpresaRol,
                strEmpleado: strEmpleado,
                strCargo: strCargo,
                strEstadoEmpleado: strEstadoEmpleado
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
            
            Ext.Msg.alert('Error', 'Ya fue escogido esta persona '+myRecord.get('strEmpleado'));
        }
    }
    
    return existe;	
}


function eliminarSeleccion(datosSelect)
{
    var xRowSelMod      = datosSelect.getSelectionModel().getCount();
    var intValorInicial = xRowSelMod - 1;
 
    for(var i = intValorInicial; i >= 0; i--)
    {
        datosSelect.getStore().remove(datosSelect.getSelectionModel().getSelection()[i]);
    }
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
    Ext.MessageBox.wait("Guardando los datos...");

    var fieldHoraDesdeTurno = Ext.getCmp('horaInicioTurno');
    var valueHoraDesdeTurno = fieldHoraDesdeTurno.getValue();
    var formattedValueHoraDesdeTurno = Ext.Date.format(valueHoraDesdeTurno, 'H:i');
    var fieldHoraHastaTurno = Ext.getCmp('horaFinTurno');
    var valueHoraHastaTurno = fieldHoraHastaTurno.getValue();
    var formattedValueHoraHastaTurno = Ext.Date.format(valueHoraHastaTurno, 'H:i');
    obtenerIntegrantes();
    if (boolDepConfigHE)
    {
        var fieldFechaDesdeTurno = Ext.getCmp('txtFechaInicio');
        var valueFechaDesdeTurno = fieldFechaDesdeTurno.getValue();
        var formattedValueFechaDesdeTurno = Ext.Date.format(valueFechaDesdeTurno, 'd-m-Y');
        var fieldFechaHastaTurno = Ext.getCmp('txtFechaFin');
        var valueFechaHastaTurno = fieldFechaHastaTurno.getValue();
        var formattedValueFechaHastaTurno = Ext.Date.format(valueFechaHastaTurno, 'd-m-Y');
        var strMensaje='';
        var fieldDiasSemana = Ext.getCmp('comboDiaSemana');
        var valueDiasSemana = fieldDiasSemana.getValue().filter(function(valor) {
            return valor !== '';
          });

    }
    else{
        if(!valueHoraDesdeTurno)
        {
            strMensaje='La Hora Inicio no puede estar vacía';
            Ext.Msg.alert('Atenci\xf3n', strMensaje);
            return false;
        }
        else if(!valueHoraHastaTurno)
        {
            strMensaje='La Hora Fin no puede estar vacía';
            Ext.Msg.alert('Atenci\xf3n', strMensaje);
            return false;
        }
        else if(valueHoraDesdeTurno && valueHoraHastaTurno)
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
        }
    }

    $("#fechaInicioTurnoCuadrilla").val(formattedValueFechaDesdeTurno);
    $("#fechaFinTurnoCuadrilla").val(formattedValueFechaHastaTurno);
    $("#horaInicioTurnoCuadrilla").val(formattedValueHoraDesdeTurno);
    $("#horaFinTurnoCuadrilla").val(formattedValueHoraHastaTurno);
    $("#diasSemana").val(JSON.stringify({valueDiasSemana}));
    var integrantes = gridEmpleadosAsignaciones.getStore().getCount();
    
    if(integrantes == 0)
    {
        Ext.MessageBox.hide();
        Ext.Msg.alert('Error', 'No se han registrado los integrantes');
        
        return false;
    }
    
    return true;
}

function validarHoras(cmp)
{
    var fieldHoraDesdeAsignacion = Ext.getCmp('horaInicioTurno');
    var valueHoraDesdeAsignacion = fieldHoraDesdeAsignacion.getValue();
    var formattedValueHoraDesdeAsignacion = Ext.Date.format(valueHoraDesdeAsignacion, 'H:i');

    var fieldHoraHastaAsignacion = Ext.getCmp('horaFinTurno');
    var valueHoraHastaAsignacion = fieldHoraHastaAsignacion.getValue();
    var formattedValueHoraHastaAsignacion = Ext.Date.format(valueHoraHastaAsignacion, 'H:i');

    var fieldFechaDesdeAsignacion = Ext.getCmp('txtFechaInicio');
    var valueFechaDesdeAsignacion = fieldFechaDesdeAsignacion.getValue();
    var formattedValueFechaDesdeAsignacion = Ext.Date.format(valueFechaDesdeAsignacion, 'd-m-Y');

    var fieldFechaHastaAsignacion = Ext.getCmp('txtFechaFin');
    var valueFechaHastaAsignacion = fieldFechaHastaAsignacion.getValue();
    var formattedValueFechaHastaAsignacion = Ext.Date.format(valueFechaHastaAsignacion, 'd-m-Y');
    var boolOKHoras = true;
    var boolOKFechas = true;
    var boolCamposLLenos=false;

    var strMensaje  = '';

    if (boolDepConfigHE)
    {
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


