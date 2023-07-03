Ext.require
([
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

var store           = '';
var intItemsPerPage = 10;
var win             = null;
var storeCargos     = null;
var storeJefes      = null;

Ext.onReady(function()
{		
    Ext.tip.QuickTipManager.init();
    
    var strNombre = new Ext.form.TextField(
    {
        id: 'nombre',
        fieldLabel: 'Nombre',
        xtype: 'textfield'
    });
    
    var strApellido = new Ext.form.TextField(
    {
        id: 'apellido',
        fieldLabel: 'Apellido',
        xtype: 'textfield'
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
            timeout: 900000,
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
        }
    });
    
    storeCargosVisibles = new Ext.data.Store
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
                strEsVisible: 'SI',
                strNombreArea: strNombreArea
            }
        },
        fields:
        [
            {name: 'intIdCargo',     mapping: 'intIdCargo'},
            {name: 'strNombreCargo', mapping: 'strNombreCargo'}
        ]
    });
    
    Ext.define('ListaDetalleModel', 
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
                name: 'strReportaA',
                type: 'string', 
                mapping: 'strReportaA'
            },
            {
                name: 'strIdReportaA',
                type: 'string', 
                mapping: 'strIdReportaA'
            },
            {
                name: 'strCargo',
                type: 'string', 
                mapping: 'strCargo'
            },
            {
                name: 'intTotalEmpleadosAsignados',
                type: 'string',
                mapping: 'intTotalEmpleadosAsignados'
            },
            {
                name: 'boolEsJefe',
                type: 'string',
                mapping: 'boolEsJefe'
            },
            {
                name: 'strFuncionaComoJefe',
                type: 'string',
                mapping: 'strFuncionaComoJefe'
            },
            {
                name: 'intCuadrillasPrestadas',
                type: 'integer',
                mapping: 'intCuadrillasPrestadas'
            },
            {
                name: 'intCuadrillasActivas',
                type: 'integer',
                mapping: 'intCuadrillasActivas'
            },
            {
                name: 'intCuadrillasEsPrestamo',
                type: 'integer',
                mapping: 'intCuadrillasEsPrestamo'
            },
            {
                name: 'strTieneTurno',
                type: 'string',
                mapping: 'strTieneTurno'
            },
            {
                name: 'intIdTurno',
                type: 'integer',
                mapping: 'intIdTurno'
            }
        ],
        idProperty: 'login'
    });

    store = Ext.create('Ext.data.JsonStore', 
    {
        model: 'ListaDetalleModel',
        pageSize: intItemsPerPage,
        proxy:
        {
            type: 'ajax',
            url: strUrlGrid,
            timeout: 900000,
            reader:
            {
                type: 'json',
                root: 'usuarios',
                totalProperty: 'total'
            },
            extraParams:
            {
                nombre: '', 
                apellido: '',
                strNombreArea: strNombreArea
            },
            simpleSortMode: true
        },
        listeners:
        {
            beforeload: function(store) 
            {
                store.getProxy().extraParams.nombre   = Ext.getCmp('nombre').getValue();
                store.getProxy().extraParams.apellido = Ext.getCmp('apellido').getValue();
                
                var strCargo =  Ext.getCmp('cmbCargo').getValue();
        
                if(strCargo == 'Todos')
                {
                    store.getProxy().extraParams.cargo = '';
                }
                else
                {
                    store.getProxy().extraParams.cargo = strCargo;
                }
            },
            load: function(store) 
            {
                store.each(function(record) {});
            }
        },
        autoLoad: true
    });

    listView = Ext.create('Ext.grid.Panel', 
    {
        id: 'grid',
        width: 1150,
        height: 365,
        renderTo: Ext.get('jefes'),
        // para el pagineo
        bbar: Ext.create('Ext.PagingToolbar',
        {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando usuarios {0} - {1} of {2}',
            emptyMsg: "No hay datos para mostrar"
        }),
        store: store,
        viewConfig: 
        {
            emptyText: 'No hay datos para mostrar',
            enableTextSelection: true,
            id: 'gv',
            trackOver: true,
            stripeRows: true,
            loadMask: true
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
        columns: 
        [
            new Ext.grid.RowNumberer(),
            {
                id: 'intIdPersonaEmpresaRol',
                header: 'intIdPersonaEmpresaRol',
                dataIndex: 'intIdPersonaEmpresaRol',
                hidden: true,
                hideable: false
            }, 
            {
                text: 'Nombres y Apellidos',
                width: 236,
                dataIndex: 'strEmpleado'
            },
            {
                text: 'Reporta A',
                width: 236,
                dataIndex: 'strReportaA'
            },
            {
                text: 'Cargo',
                width: 140,
                dataIndex: 'strCargo'
            },
            {
                text: '# Empleados<br>Asignados',
                width: 95,
                align: 'center',
                dataIndex: 'intTotalEmpleadosAsignados'
            },
            {
                text: '# Cuadrillas<br>Activas',
                width: 95,
                align: 'center',
                dataIndex: 'intCuadrillasActivas'
            },
            {
                text: '# Cuadrillas<br>Prestadas',
                width: 95,
                align: 'center',
                dataIndex: 'intCuadrillasPrestadas'
            },
            {
                text: '# Cuadrillas<br>Son Préstamo',
                width: 100,
                align: 'center',
                dataIndex: 'intCuadrillasEsPrestamo'
            },
            {
                header: 'Acciones',
                align: 'center',
                xtype: 'actioncolumn',
                width: 150,
                sortable: false,
                items:
                [
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var strClassA = "btn-acciones btn-asignar-jefe";
                            
                            if (strClassA == "icon-invisible")
                            {
                                this.items[0].tooltip = '';
                            }
                            else
                            {
                                this.items[0].tooltip = 'Asignar Responsable';
                            }
                            
                            return strClassA;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec       = store.getAt(rowIndex);
                            var strClassA = "btn-acciones btn-asignar-jefe";
                            var strCargo  = rec.data.strCargo;
                            
                            if (strClassA != "icon-invisible")
                            {
                                var intIdPersonaEmpresaRol = rec.data.intIdPersonaEmpresaRol;
                                var strIdReportaA          = rec.data.strIdReportaA;
                                
                                var arrayParametros                           = [];
                                    arrayParametros['intIdPersonaEmpresaRol'] = intIdPersonaEmpresaRol;
                                    arrayParametros['strIdReportaA']          = strIdReportaA;
                                    arrayParametros['strCargo']               = strCargo;
                                    
                                asignarJefe( arrayParametros );
                            }
                            else
                            {
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var strClassA                  = "btn-acciones btn-cambiar-asignacion-jefe";
                            var strCargo                   = rec.data.strCargo;
                            var boolEsJefe                 = rec.data.boolEsJefe;
                            var strFuncionaComoJefe        = rec.data.strFuncionaComoJefe;
                            var intCuadrillasPrestadas     = rec.data.intCuadrillasPrestadas;
                            var intTotalEmpleadosAsignados = rec.data.intTotalEmpleadosAsignados;
                            
                            if( (boolEsJefe == 'N' && strFuncionaComoJefe=='N') || intCuadrillasPrestadas > 0 || intTotalEmpleadosAsignados == 0 )
                            {
                                strClassA = 'icon-invisible';
                            }
                            
                            if (strClassA == "icon-invisible")
                            {
                                this.items[1].tooltip = '';
                            }
                            else
                            {
                                this.items[1].tooltip = 'Cambiar de Responsable<br>a los empleados asignados';
                            }
                            
                            return strClassA;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec                        = store.getAt(rowIndex);
                            var strClassA                  = "btn-acciones btn-cambiar-asignacion-jefe";
                            var strCargo                   = rec.data.strCargo;
                            var boolEsJefe                 = rec.data.boolEsJefe;
                            var strFuncionaComoJefe        = rec.data.strFuncionaComoJefe;
                            var intCuadrillasPrestadas     = rec.data.intCuadrillasPrestadas;
                            var intTotalEmpleadosAsignados = rec.data.intTotalEmpleadosAsignados;
                            
                            if( (boolEsJefe == 'N' && strFuncionaComoJefe=='N') || intCuadrillasPrestadas > 0 || intTotalEmpleadosAsignados == 0 )
                            {
                                strClassA = 'icon-invisible';
                            }
                            
                            if (strClassA != "icon-invisible")
                            {
                                var intIdPersonaEmpresaRol = rec.data.intIdPersonaEmpresaRol;
                                var strEmpleado            = rec.data.strEmpleado;
                                
                                var arrayParametros                           = [];
                                    arrayParametros['intIdPersonaEmpresaRol'] = intIdPersonaEmpresaRol;
                                    arrayParametros['strIdReportaA']          = intIdPersonaEmpresaRol;
                                    arrayParametros['strCargo']               = strCargo;
                                    arrayParametros['jefeActual']             = strEmpleado;
                                
                                cambiarJefeAsignado( arrayParametros );
                            }
                            else
                            {
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var strClassA               = "btn-acciones btn-asignar-empleados";
                            var boolEsJefe              = rec.data.boolEsJefe;
                            var strFuncionaComoJefe     = rec.data.strFuncionaComoJefe;
                            
                            if( boolEsJefe == 'N' && strFuncionaComoJefe == 'N')
                            {
                                strClassA = 'icon-invisible';
                            }
                            
                            if (strClassA == "icon-invisible")
                            {
                                this.items[2].tooltip = '';
                            }
                            else
                            {
                                this.items[2].tooltip = 'Asignar Empleados';
                            }
                            
                            return strClassA;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec                     = store.getAt(rowIndex);
                            var strClassA               = "btn-acciones btn-asignar-empleados";
                            var boolEsJefe              = rec.data.boolEsJefe;
                            var strFuncionaComoJefe     = rec.data.strFuncionaComoJefe;
                            
                            if( boolEsJefe == 'N' && strFuncionaComoJefe == 'N' )
                            {
                                strClassA = 'icon-invisible';
                            }
                            
                            if (strClassA != "icon-invisible")
                            {
                                mostrarVistaAsignarEmpleados(rec);
                            }
                            else
                            {
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var arrayCargos = ['Coordinador', 'Ayudante Coordinador'];
                            var strCargo  = rec.data.strCargo;
                            var strClassA = "btn-acciones btn-asignar-coordinador-general";
                            var strTieneTurno = rec.data.strTieneTurno;
                            
                            if (!arrayCargos.includes(strCargo) || !strTieneTurno ||
                                (arrayCargos.includes(strCargo) && strTieneTurno == 'SI'))
                            {
                                strClassA = "icon-invisible";
                                this.items[3].tooltip = '';
                            }
                            else
                            {
                                this.items[3].tooltip = 'Asignar Coordinador General';
                            }
                            
                            return strClassA;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec         = store.getAt(rowIndex);
                            var strClassA   = "btn-acciones btn-asignar-jefe";

                            if (strClassA != "icon-invisible")
                            {
                                var intIdPersonaEmpresaRol = rec.data.intIdPersonaEmpresaRol;
                                asignarCoordinadorGeneral(intIdPersonaEmpresaRol);
                            }
                            else
                            {
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var arrayCargos = ['Coordinador', 'Ayudante Coordinador'];
                            var strCargo  = rec.data.strCargo;
                            var strClassA = "btn-acciones btn-ver-asignacion-coordinador-general";
                            var strTieneTurno = rec.data.strTieneTurno;
                            
                            if (!arrayCargos.includes(strCargo) || !strTieneTurno || 
                                (arrayCargos.includes(strCargo) && strTieneTurno == 'NO'))
                            {
                                strClassA = "icon-invisible";
                                this.items[4].tooltip = '';
                            }
                            else
                            {
                                this.items[4].tooltip = 'Ver Asignación';
                            }
                            
                            return strClassA;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);
                            verAsignancion(rec);
                        }
                    },
                ]
            }
        ]
    });


    function asignarCoordinadorGeneral( intIdPersonaEmpresaRol )
    {

        var formPanel = Ext.create('Ext.form.Panel',
            {
                id: 'formAsignarCoordinadorGeneral',
                bodyPadding: 2,
                waitMsgTarget: true,
                fieldDefaults: 
                {
                    labelAlign: 'left',
                    labelWidth: 100,
                    msgTarget: 'side'
                },
                items: 
                [
                    {
                        xtype: 'checkbox',
                        fieldLabel : '¿Asignar ahora?',
                        id : 'checkAsignarAhora',
                        name : 'checkAsignarAhora',
                        width: 290,
                        checked: false,
                        hidden: false,
                        listeners: {
                            change: function(field, newValue, oldValue, eOpts){
                                if(newValue == true)
                                {
                                    Ext.getCmp('txtFechaInicio').disable();
                                    Ext.getCmp('horaInicioTurno').disable();

                                    Ext.getCmp('txtFechaInicio').setValue(Ext.Date.format(new Date(), 'Y-m-d'));
                                    Ext.getCmp('horaInicioTurno').setValue(Ext.Date.format(new Date(), 'H:i'));
                                }
                                else
                                {
                                    Ext.getCmp('txtFechaInicio').enable();
                                    Ext.getCmp('horaInicioTurno').enable(); 

                                    Ext.getCmp('txtFechaInicio').setRawValue("");
                                    Ext.getCmp('horaInicioTurno').setRawValue("");
                                }
                            }
                        }
                    },
                    {
                        xtype: 'datefield',
                        width: 290,
                        id: 'txtFechaInicio',
                        name: 'txtFechaInicio',
                        fieldLabel: 'Fecha Inicio:',
                        format: 'Y-m-d',
                        editable: false,
                        minValue:new Date(),
                        allowBlank: false,
                        emptyText: "Seleccione",
                        listeners: {
                            select: function(cmp, newValue, oldValue) {
                                validarFechasHoras(cmp);
                            }
                        }
                    },
                    {
                        xtype: 'timefield',
                        width: 290,
                        id: 'horaInicioTurno',
                        name:'horaInicioTurno',
                        fieldLabel: '<b>Hora Inicio</b>',
                        editable: false,
                        minValue: '00:00',
                        maxValue: '24:00',
                        format: 'H:i',
                        increment: 60,
                        allowBlank: false,
                        emptyText: "Seleccione",
                        listeners: {
                            select: function(cmp, newValue, oldValue) {
                                validarFechasHoras(cmp);
                            }
                        }
                    },

                    {
                        xtype: 'datefield',
                        width: 290,
                        id: 'txtFechaFin',
                        name: 'txtFechaFin',
                        fieldLabel: 'Fecha Fin:',
                        format: 'Y-m-d',
                        editable: false,
                        minValue:new Date(),
                        allowBlank: false,
                        emptyText: "Seleccione",
                        listeners: {
                            select: function(cmp, newValue, oldValue) {
                                validarFechasHoras(cmp);
                            }
                        }
                    },

                    {
                        xtype: 'timefield',
                        width: 290,
                        id: 'horaFinTurno',
                        name:'horaFinTurno',
                        fieldLabel: '<b>Hora Fin</b>',
                        editable: false,
                        minValue: '00:00',
                        maxValue: '24:00',
                        format: 'H:i',
                        increment: 60,
                        allowBlank: false,
                        emptyText: "Seleccione",
                        listeners: {
                            select: function(cmp, newValue, oldValue) {
                                validarFechasHoras(cmp);
                            }
                        }
                    },
                    
                ],
                buttons:
                [
                    {
                        text: 'Asignar',
                        type: 'submit',
                        handler: function()
                        {
                            var form = Ext.getCmp('formAsignarCoordinadorGeneral').getForm();  
                            if ( form.isValid() )
                            {
                                var formattedValueFechaInicio = formatearFecha('txtFechaInicio');
                                var formattedValueHoraInicio = formatearHora('horaInicioTurno');

                                var formattedFechaFin = formatearFecha('txtFechaFin');
                                var formattedValueHoraFin = formatearHora('horaFinTurno');

                                boolAsignarAhora = Ext.getCmp('checkAsignarAhora').value;

                                var arrayParametros                             = [];
                                    arrayParametros['intIdPersonalEmpresaRol']  = intIdPersonaEmpresaRol;
                                    arrayParametros['strFechaInicio']           = formattedValueFechaInicio;
                                    arrayParametros['strHoraInicio']            = formattedValueHoraInicio;
                                    arrayParametros['strFechaFin']              = formattedFechaFin;
                                    arrayParametros['strHoraFin']               = formattedValueHoraFin;
                                    arrayParametros['boolAsignarAhora']         = boolAsignarAhora;
                                    arrayParametros['store']                    = store;

                                var formattedValueHoraActual    = Ext.Date.format(new Date(), 'H:i');
                                var formattedValueFechaActual   = Ext.Date.format(new Date(), 'Y-m-d');
                                boolAsignarAhora                = Ext.getCmp('checkAsignarAhora').value;

                                if (formattedValueFechaInicio == formattedValueFechaActual &&
                                    formattedValueHoraActual >= formattedValueHoraInicio &&
                                    !boolAsignarAhora)
                                {
                                    var strMensaje = 'La hora de inicio no puede ser igual a la hora actual.';
                                    Ext.Msg.alert('Atenci\xf3n', strMensaje);
                                }
                                else
                                {
                                    ajaxAsignarCoordinadorGeneral(arrayParametros);
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
                   title: 'Generar asignación de turno.',
                   modal: true,
                   width: 350,
                   closable: true,
                   layout: 'fit',
                   items: [formPanel]
              }).show();
    }

    function formatearFecha( strIdField )
    {
        var fieldFecha = Ext.getCmp(strIdField);
        var valueFecha = fieldFecha.getValue();
        var formattedValueFecha = Ext.Date.format(valueFecha, 'Y-m-d');

        return formattedValueFecha;
    }

    function formatearHora( strIdField )
    {
        var fieldHora = Ext.getCmp(strIdField);
        var valueHora = fieldHora.getValue();
        var formattedValueHora = Ext.Date.format(valueHora, 'H:i');

        return formattedValueHora;
    }

    function validarStrValue( strValue )
    {
        if (strValue === null || strValue.trim() === "")
        {
            return false;
        }
        
        return true;
    }

    function validarFechasHoras( cmp )
    {
        var formattedValueFechaInicio = formatearFecha('txtFechaInicio');
        var formattedValueFechaFin = formatearFecha('txtFechaFin');

        var formattedValueHoraInicio= formatearHora('horaInicioTurno');
        var formattedValueHoraFin = formatearHora('horaFinTurno');

        var strMensaje          = '';
        var boolAsignarAhora    = Ext.getCmp('checkAsignarAhora').value;

        var valueFechaActual = new Date();
        var formattedValueHoraActual = Ext.Date.format(valueFechaActual, 'H:i');
        var formattedValueFechaActual = Ext.Date.format(valueFechaActual, 'Y-m-d');

        if (validarStrValue(formattedValueFechaInicio) &&
            validarStrValue(formattedValueHoraInicio) &&
            !boolAsignarAhora)
        {   
            if ((formattedValueFechaInicio == formattedValueFechaActual) && 
                formattedValueHoraInicio < formattedValueHoraActual)
            {
                strMensaje = 'La hora de inicio no puede ser inferior a la hora actual.'
                mostrarMesajeError(strMensaje, cmp);
            }
        }

        if (validarStrValue(formattedValueFechaInicio) && validarStrValue(formattedValueFechaFin))
        {
            if (formattedValueFechaFin < formattedValueFechaInicio)
            {
                strMensaje = 'La fecha fin no puede ser inferior a la fecha inicio.'
                mostrarMesajeError(strMensaje, cmp);
            }
        }

        if (validarStrValue(formattedValueFechaInicio) && validarStrValue(formattedValueHoraInicio) && 
            validarStrValue(formattedValueHoraFin) && validarStrValue(formattedValueHoraFin))
        {
            
            if ((formattedValueFechaFin == formattedValueFechaActual ||
                formattedValueFechaInicio == formattedValueFechaFin) &&
                formattedValueHoraFin <= formattedValueHoraInicio &&
                !boolAsignarAhora)
            {
                strMensaje = 'La hora de fin no puede ser inferior o igual a la hora de inicio.'
                mostrarMesajeError(strMensaje, cmp);
            }

            var valueDateActual = new Date();
            valueDateActual.setHours(valueDateActual.getHours() + 1);
            var formattedValueHoraMinima = Ext.Date.format(valueDateActual, 'H');

            if ((formattedValueFechaFin == formattedValueFechaActual ||
                formattedValueFechaInicio == formattedValueFechaFin) &&
                formattedValueHoraFin < formattedValueHoraMinima &&
                boolAsignarAhora)
            {
                strMensaje = 'La asignación de turno no puede terminar dentro de la hora actual.'
                mostrarMesajeError(strMensaje, cmp);
            }

        }

    }

    function mostrarMesajeError( strMensaje, cmp )
    {
        Ext.Msg.alert('Atenci\xf3n', strMensaje);
        cmp.value = "";
        cmp.setRawValue("");
    }

    function verAsignancion(rec)
    {
        var strEmpleado = rec.data.strEmpleado;
        var intIdTurno = rec.data.intIdTurno;
        
        
        new Ext.data.Connection({
            listeners: {
                'beforerequest': {
                    fn: function (con, opt) {
                        Ext.get(document.body).mask('Loading...');
                    },
                    scope: this
                },
                'requestcomplete': {
                    fn: function (con, res, opt) {
                        Ext.get(document.body).unmask();
                    },
                    scope: this
                },
                'requestexception': {
                    fn: function (con, res, opt) {
                        Ext.get(document.body).unmask();
                    },
                    scope: this
                }
            }
        });

        
            Ext.Ajax.request({
                url    : strUrlGetTurno,
                method : 'post',
                params : {
                    intIdTurno: intIdTurno
                },
                success: function (objResponse) 
                {
                    var objTurno = JSON.parse(objResponse.responseText).turno;
                    Ext.getCmp('txtEstadoEdit').setValue(objTurno.strEstado);
                    Ext.getCmp('txtFechaInicioEdit').setValue(objTurno.strFechaInicio);
                    Ext.getCmp('txtFechaInicioEdit').setValue(objTurno.strFechaInicio);
                    Ext.getCmp('txtFechaFinEdit').setValue(objTurno.strFechaFin);
                    Ext.getCmp('horaInicioTurnoEdit').setValue(objTurno.strHoraInicio);
                    Ext.getCmp('horaFinTurnoEdit').setValue(objTurno.strHoraFin);
                }
            });

        var formCoordinadorTurno = Ext.create('Ext.form.Panel', {
            bodyPadding   : 5,
            waitMsgTarget : true,
            fieldDefaults: 
            {
                labelAlign : 'left',
                labelWidth : 150,
                msgTarget  : 'side'
            },
            items: 
            [
                {
                    xtype    : 'fieldset',
                    title    : '&nbsp;<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b style="color:blue">Coordinador</b>',
                    defaults : { width: 450 },
                    items :
                    [
                        {
                            xtype      : 'displayfield',
                            fieldLabel : '<b>Nombres y Apellidos</b>',
                            value      : strEmpleado,
                            id         :'strCoordinadorTurno',
                            readOnly   : true
                        }
                    ]
                },
                {
                    xtype    : 'fieldset',
                    title    : '&nbsp;<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b style="color:blue">Detalles</b>',
                    defaults : { width: 450 },
                    items: 
                    [
                        {
                            xtype      : 'displayfield',
                            fieldLabel : '<b>Estado</b>',
                            value      : '',
                            id         :'txtEstadoEdit',
                            readOnly   : true
                        },
                        {
                            xtype: 'datefield',
                            width: 290,
                            id: 'txtFechaInicioEdit',
                            name: 'txtFechaInicioEdit',
                            fieldLabel: 'Fecha Inicio:',
                            format: 'Y-m-d',
                            editable: false,
                            readOnly   : true,
                            //minValue:new Date(),
                            allowBlank: false,
                            emptyText: "Seleccione",
                            listeners: {
                                select: function(cmp, newValue, oldValue) {
                                    validarFechasHoras(cmp);
                                }
                            }
                        },
                        {
                            xtype: 'timefield',
                            width: 290,
                            id: 'horaInicioTurnoEdit',
                            name:'horaInicioTurnoEdit',
                            fieldLabel: '<b>Hora Inicio</b>',
                            editable: false,
                            readOnly   : true,
                            minValue: '00:00',
                            maxValue: '24:00',
                            format: 'H:i',
                            increment: 60,
                            allowBlank: false,
                            emptyText: "Seleccione",
                            listeners: {
                                select: function(cmp, newValue, oldValue) {
                                    validarFechasHoras(cmp);
                                }
                            }
                        },
    
                        {
                            xtype: 'datefield',
                            width: 290,
                            id: 'txtFechaFinEdit',
                            name: 'txtFechaFinEdit',
                            fieldLabel: 'Fecha Fin:',
                            format: 'Y-m-d',
                            editable: false,
                            readOnly   : true,
                            //minValue:new Date(),
                            allowBlank: false,
                            emptyText: "Seleccione",
                            listeners: {
                                select: function(cmp, newValue, oldValue) {
                                    validarFechasHoras(cmp);
                                }
                            }
                        },
    
                        {
                            xtype: 'timefield',
                            width: 290,
                            id: 'horaFinTurnoEdit',
                            name:'horaFinTurnoEdit',
                            fieldLabel: '<b>Hora Fin</b>',
                            editable: false,
                            readOnly   : true,
                            minValue: '00:00',
                            maxValue: '24:00',
                            format: 'H:i',
                            increment: 60,
                            allowBlank: false,
                            emptyText: "Seleccione",
                            listeners: {
                                select: function(cmp, newValue, oldValue) {
                                    validarFechasHoras(cmp);
                                }
                            }
                        },
                    ]
                }
            ],
            buttonAlign : 'center',
            buttons     :
            [
                {
                    text     : 'Eliminar',
                    formBind : true,
                    handler  : function() {
                        Ext.Msg.confirm("Alerta", "Se eliminara el turno asignado. Desea continuar?", function (btn) {
                            if (btn == "yes") {
                                Ext.Ajax.request({
                                    url: strUrlEliminarTurno,
                                    method: 'post',
                                    params: { 
                                        intIdTurno: intIdTurno
                                    },
                                    success: function(response){
                                        var text = response.responseText;

                                        if( text == 'OK')
                                        {
                                            Ext.Msg.alert('Información', 'Se elimino con éxito');
                                        }
                                        else
                                        {
                                            Ext.Msg.alert('Error', 'Hubo un problema al eliminar la asignación'); 
                                        }
                                        winCoordinadorTurno.destroy();
                                        store.load();
                                    },
                                    failure: function(result)
                                    {
                                        Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                    }
                                });
                        }
                    });
                    }
                },
                {
                    text: 'Cerrar',
                    handler: function() {
                        winCoordinadorTurno.destroy();
                    }
                }
            ]
        });

        winCoordinadorTurno = Ext.create('Ext.window.Window', {
            title    : 'Turno Asignado',
            modal    : true,
            closable : false,
            width    : 530,
            layout   : 'fit',
            items    : [formCoordinadorTurno]
        });
    
        winCoordinadorTurno.show();
    }
    
    var filterPanel = Ext.create('Ext.panel.Panel', 
    {
        bodyPadding: 7,
        border: false,
        buttonAlign: 'center',
        layout: 
        {
            type: 'table',
            columns: 6,
            align: 'left'
        },
        bodyStyle: 
        {
            background: '#fff'
        },
        defaults: 
        {
            bodyStyle: 'padding:10px'
        },
        collapsible: true,
        collapsed: true,
        width: 1150,
        title: 'Criterios de busqueda',
        buttons: 
        [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: Buscar
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: Limpiar
            }

        ],
        items: 
        [
            strNombre,
            {
                html: "&nbsp;",
                border: false,
                width: 50
            },
            strApellido,
            {
                html: "&nbsp;",
                border: false,
                width: 50
            },
            {
                xtype: 'combobox',
                fieldLabel: 'Cargos:',
                id: 'cmbCargo',
                name: 'cmbCargo',
                store: storeCargosNoVisibles,
                displayField: 'strNombreCargo',
                valueField: 'strNombreCargo',
                queryMode: 'remote',
                emptyText: 'Seleccione',
                forceSelection: true
            },
            {
                html: "&nbsp;",
                border: false,
                width: 50
            },
        ],
        renderTo: 'criteriosBusqueda'
    });
    
    
    var myMask = new Ext.LoadMask
        (
           Ext.getCmp('grid').el,
           {
               msg:"Cargando..."
           }
        );

    Ext.Ajax.on('beforerequest', myMask.show, myMask);
    Ext.Ajax.on('requestcomplete', myMask.hide, myMask);
    Ext.Ajax.on('requestexception', myMask.hide, myMask);


    function Buscar() 
    {
        if( Ext.getCmp('nombre').getValue() == '' && Ext.getCmp('apellido').getValue() == '' && Ext.getCmp('cmbCargo').getValue() == null )
        {
            Ext.Msg.alert('Error', 'No se puede realizar la búsqueda porque los campos Nombres, Apellidos y Cargos están vacíos.');
        }
        else
        {
            store.loadData([],false);
            store.currentPage = 1;
            store.load();
        }
    }

    function Limpiar()
    {
        Ext.getCmp('nombre').setValue('');
        Ext.getCmp('apellido').setValue('');
        Ext.getCmp('cmbCargo').setValue(null);
        
        store.loadData([],false);
        store.currentPage = 1;
        store.load();
    }
    
    function habilitarJefe(intIdPersonaEmpresaRol, strCargo)
    {
        var formPanel = Ext.create('Ext.form.Panel',
            {
                id: 'formHabilitarJefe',
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
                        text: 'Habilitar',
                        type: 'submit',
                        handler: function()
                        {
                            var form = Ext.getCmp('formHabilitarJefe').getForm();

                            if( form.isValid() )
                            {
                                var strNombreCargo = Ext.getCmp('comboCargos').getValue();

                                if ( strNombreCargo != null && strNombreCargo != '' )
                                {
                                    var arrayParametros                             = [];
                                        arrayParametros['intIdPersonalEmpresaRol']  = intIdPersonaEmpresaRol;
                                        arrayParametros['valor']                    = strNombreCargo;
                                        arrayParametros['caracteristica']           = strCaracteristicaCargo;
                                        arrayParametros['accion']                   = 'Guardar';
                                        arrayParametros['store']                    = store;
                                        
                                    ajaxAsignarCaracteristica(arrayParametros);
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
                   title: 'Habilitar como Responsable',
                   modal: true,
                   width: 350,
                   closable: true,
                   layout: 'fit',
                   items: [formPanel]
              }).show();
    }
    
    
    function crearStoreJefes( arrayParametros )
    {
        storeJefes = new Ext.data.Store
        ({
            total: 'total',
            pageSize: 200,
            proxy:
            {
                type: 'ajax',
                method: 'post',
                url: strUrlGetJefes,
                timeout: 900000,
                reader:
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'usuarios'
                },
                extraParams:
                {
                    strSoloJefes: 'S', 
                    strExceptoUsr: arrayParametros['intIdPersonaEmpresaRol']+'|'+arrayParametros['strIdReportaA'],
                    strCargo: arrayParametros['strCargo'],
                    strNombreArea: strNombreArea
                }
            },
            fields:
            [
                {name: 'intIdPersonaEmpresaRol', mapping: 'intIdPersonaEmpresaRol'},
                {name: 'strEmpleado',            mapping: 'strEmpleado'}
            ],
            listeners: 
            {
                load: function(store, records)
                {
                     store.insert(0, 
                     [
                        {
                            strEmpleado: 'Seleccione',
                            intIdPersonaEmpresaRol: ''
                        }
                     ]);
                }      
            }
        });
    }
    
    
    function asignarJefe( arrayParametros )
    {
        crearStoreJefes(arrayParametros);
            
        var formPanel = Ext.create('Ext.form.Panel',
            {
                id: 'formAsignarJefe',
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
                                fieldLabel: 'Responsable:',
                                id: 'comboJefe',
                                name: 'comboJefe',
                                store: storeJefes,
                                displayField: 'strEmpleado',
                                valueField: 'intIdPersonaEmpresaRol',
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
                            var form = Ext.getCmp('formAsignarJefe').getForm();

                            if( form.isValid() )
                            {
                                var intIdJefe              = Ext.getCmp('comboJefe').getValue();
                                var strIdPersonaEmpresaRol = arrayParametros['intIdPersonaEmpresaRol'];

                                if ( intIdJefe != null && intIdJefe != '' )
                                {                   
                                    var arrayTmpParametros                           = [];
                                        arrayTmpParametros['accion']                 = 'asignar_responsable';
                                        arrayTmpParametros['intIdJefe']              = intIdJefe;
                                        arrayTmpParametros['strIdPersonaEmpresaRol'] = strIdPersonaEmpresaRol;

                                    verificarEmpleadosAEliminar(arrayTmpParametros);
                                }
                                else
                                {
                                    Ext.Msg.alert('Atenci\xf3n', 'Debe seleccionar un Responsable');
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
                   title: 'Asignar Responsable',
                   modal: true,
                   width: 350,
                   closable: true,
                   layout: 'fit',
                   items: [formPanel]
              }).show();
    }
    
    
    function cambiarJefeAsignado( arrayParametros )
    {
        crearStoreJefes( arrayParametros );
        
        var formPanel = Ext.create('Ext.form.Panel',
            {
                id: 'formAsignarJefe',
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
                                id: 'strJefeActual',
                                name: 'strJefeActual',
                                fieldLabel: 'Responsable Actual',
                                value: arrayParametros['jefeActual']
                            },
                            {
                                xtype: 'combobox',
                                fieldLabel: 'Responsable Nuevo',
                                id: 'comboJefe',
                                name: 'comboJefe',
                                store: storeJefes,
                                displayField: 'strEmpleado',
                                valueField: 'intIdPersonaEmpresaRol',
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
                            var form = Ext.getCmp('formAsignarJefe').getForm();

                            if( form.isValid() )
                            {
                                var intIdJefe              = Ext.getCmp('comboJefe').getValue();
                                var strIdPersonaEmpresaRol = arrayParametros['intIdPersonaEmpresaRol'];

                                if ( intIdJefe != null && intIdJefe != '' )
                                {
                                    if( intIdJefe == 'sa' )
                                    {
                                        intIdJefe = null;
                                    }
                                    
                                    Ext.Ajax.request
                                    ({
                                        url: strUrlCambioJefe,
                                        method: 'post',
                                        dataType: 'json',
                                        params:
                                        { 
                                            intIdJefe: intIdJefe,
                                            strIdPersonaEmpresaRol: strIdPersonaEmpresaRol,
                                            strAccion: 'cambioJefeEmpleadosAsignados',
                                            strNombreArea: strNombreArea
                                        },
                                        success: function(response)
                                        {
                                            if( response.responseText == 'OK')
                                            {
                                                Ext.Msg.alert('Información', 'Se ha asignado a un nuevo Responsable con éxito');
                                                win.destroy();
                                            }
                                            else
                                            {
                                                Ext.Msg.alert('Error', 'Hubo un problema al asignar el Responsable'); 
                                            }
                                            
                                            store.load();
                                        },
                                        failure: function(result)
                                        {
                                            Ext.Msg.alert('Error',result.responseText); 
                                        }
                                    });
                                }
                                else
                                {
                                    Ext.Msg.alert('Atenci\xf3n', 'Debe seleccionar un Responsable');
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
                   title: 'Asignar Responsable',
                   modal: true,
                   width: 350,
                   closable: true,
                   layout: 'fit',
                   items: [formPanel]
              }).show();
    }
    
    
    function deshabilitarComoJefe( arrayParametros )
    {
        Ext.Msg.confirm('Alerta', 'Está seguro que desea deshabilitar como '+arrayParametros["cargoEmpleado"]+'?', function(btn) 
        {
            if (btn == 'yes')
            {
                ajaxAsignarCaracteristica(arrayParametros);
            }
        });
    }
    
    
    function mostrarVistaAsignarEmpleados(rec)
    {
        $('#itemIntIdPersonaEmpresaRol').val( rec.data.intIdPersonaEmpresaRol );
        $('#itemStrCargo').val( rec.data.strCargo );

        document.forms[0].submit();		
    }
});


function cambiarJefeAjax( arrayParametros )
{
    Ext.Ajax.request
    ({
        url: strUrlCambioJefe,
        method: 'post',
        dataType: 'json',
        params:
        { 
            intIdJefe: arrayParametros['intIdJefe'],
            strIdPersonaEmpresaRol: arrayParametros['strIdPersonaEmpresaRol'],
            strNombreArea: strNombreArea
        },
        success: function(response)
        {
            if( response.responseText == 'OK')
            {
                Ext.Msg.alert('Información', 'Se ha asignado a un nuevo Responsable con éxito');
                win.destroy();
            }
            else
            {
                Ext.Msg.alert('Error', 'Hubo un problema al asignar el responsable'); 
            }

            store.load();
        },
        failure: function(result)
        {
            Ext.Msg.alert('Error',result.responseText); 
        }
    });
}