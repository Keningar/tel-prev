var storeEmpleadosDepartamento = null;
var storeEmpleadosAsignados    = null;
var modelStoreEmpDepartamento  = null;
var modelStoreEmpAsignados     = null;
var boolOcultarColumna         = false;
var boolOcultarCargo           = true;
var intWidthNombres            = 290;
var intHeightAsignados         = 510;
var intWidthCargos             = 170;
var boolOcultarTiempo          = true;
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
    var strUrlCargar          = strUrlEmpleadosDepartamento;
    var strTituloDpto         = 'Empleados del Departamento';
    var strTituloEmp          = 'Empleados';
    var strTituloEmpAsignados = 'Empleados Asignados';

    Ext.tip.QuickTipManager.init();
    
    if( !Ext.isEmpty(strPrefijoEmpresa) && strPrefijoEmpresa == "TN" )
    {
        boolOcultarColumna = true;
        boolOcultarCargo   = false;
        intWidthNombres    = 295;
        intHeightAsignados = 525;
    }

    if( strEsAsistente == "S" && !Ext.isEmpty(strPrefijoEmpresa) && strPrefijoEmpresa == "TN")
    {
        strUrlCargar          = strUrlVendedoresDepartamento;
        strTituloDpto         = 'Vendedores del Departamento';
        strTituloEmp          = 'Vendedores';
        strTituloEmpAsignados = 'Vendedores Asignados';
        intWidthNombres       = 200;
        boolOcultarTiempo     = false;
        intWidthCargos        = 150
    }    
    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing',
    {
        clicksToEdit: 1
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
            url: strUrlCargar,
            timeout: 9000000,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'usuarios'
            },
            extraParams:
            {
                strExceptoUsr: intIdJefeSeleccionado,
                strNoAsignados: 'S',
                strCargo: strCargo,
                strNombreArea: strNombreArea,
                strExceptoChoferes:'S',
                intIdCargoTelcos: intIdCargoTelcos,
                strEsAsistente: strEsAsistente
            }
        },
        fields:
        [
            {name: 'intIdPersonaEmpresaRol', mapping: 'intIdPersonaEmpresaRol'},
            {name: 'strEmpleado',            mapping: 'strEmpleado'}
        ],
        autoLoad: true
    });

    var gridEmpleadosDepartamento = Ext.create('Ext.grid.Panel',
    {
        width: 370,
        height: 525,
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
                header: strTituloEmp,
                dataIndex: 'strEmpleado',
                width: 325,
                sortable: true
            }
        ],
        title: strTituloDpto,
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
        width: 370,
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
                name: 'strMetaBruta',
                type: 'string', 
                mapping: 'strMetaBruta'
            },
            {
                name: 'strMetaActiva',
                type: 'string', 
                mapping: 'strMetaActiva'
            },
            {
                name: 'intMetaActivaValor',
                type: 'string', 
                mapping: 'intMetaActivaValor'
            },
            {
                name: 'strCargo',
                type: 'string', 
                mapping: 'strCargo'
            },
            {
                name: 'strTiempoLimite',
                type: 'string', 
                mapping: 'strTiempoLimite'
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
            url: strUrlCargar,
            timeout: 9000000,
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
                strEsAsistente: strEsAsistente
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
                
                if( !Ext.isEmpty(Ext.getCmp('asignarMetaButton')) )
                {
                    gridEmpleadosAsignaciones.down('#asignarMetaButton').setDisabled(selections.length == 0);
                }
            }
        }
    });

    gridEmpleadosAsignaciones = Ext.create('Ext.grid.Panel',
    {
        id: 'gridEmpleadosAsignados',
        name: 'gridEmpleadosAsignados',
        width: 510,
        height: intHeightAsignados,
        store: storeEmpleadosAsignados,
        loadMask: true,
        selModel: modelStoreEmpAsignados,
        iconCls: 'icon-grid',
        plugins:[{ ptype : 'pagingselectpersist' },cellEditing],
        columns: 
        [
            {
                header: 'intIdPersonaEmpresaRol',
                dataIndex: 'intIdPersonaEmpresaRol',
                hidden: true,
                hideable: false
            },
            {
                header: strTituloEmp,
                dataIndex: 'strEmpleado',
                width: intWidthNombres,
                sortable: true
            },
            {
                header: 'Meta Bruta',
                dataIndex: 'strMetaBruta',
                width: 65,
                sortable: true,
                align: 'center',
                hidden: boolOcultarColumna
            },
            {
                header: 'Meta Activa',
                dataIndex: 'intMetaActivaValor',
                width: 65,
                sortable: true,
                align: 'center',
                hidden: boolOcultarColumna
            },
            {
                header: 'Tiempo N° de dias',
                dataIndex: 'strTiempoLimite',
                editor: 'textfield',
                width: 115,
                sortable: true,
                align: 'center',
                hidden: boolOcultarTiempo
            },
            {
                header: 'Cargo Telcos',
                dataIndex: 'strCargo',
                width: intWidthCargos,
                sortable: true,
                align: 'center',
                hidden: boolOcultarCargo
            },
            {
                header: 'Acciones',
                xtype: 'actioncolumn',
                width: 60,
                sortable: false,
                hidden: boolOcultarColumna,
                items:
                [
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var strClassA = "btn-acciones btn-asignar-meta";
                            
                            if (strClassA == "icon-invisible")
                            {
                                this.items[0].tooltip = '';
                            }
                            else
                            {
                                this.items[0].tooltip = 'Asignar Meta';
                            }
                            
                            return strClassA;
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec                    = storeEmpleadosAsignados.getAt(rowIndex);
                            var strClassA              = "btn-acciones btn-asignar-meta";
                            var strMetaBruta           = rec.data.strMetaBruta;
                            var strMetaActiva          = rec.data.strMetaActiva;
                            var intIdPersonaEmpresaRol = rec.data.intIdPersonaEmpresaRol;
                            
                            if (strClassA != "icon-invisible")
                            {
                                var arrayParametros                             = [];
                                    arrayParametros['intIdPersonalEmpresaRol']  = intIdPersonaEmpresaRol;
                                    arrayParametros['strMetaBruta']             = strMetaBruta;
                                    arrayParametros['strMetaActiva']            = strMetaActiva;
                                    arrayParametros['accion']                   = 'Guardar';
                                    arrayParametros['store']                    = storeEmpleadosAsignados;
                                    
                                asignarMeta( arrayParametros );
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
                        itemId: 'asignarTiempoButton',
                        text: 'Asignar Tiempo',
                        scope: this,
                        tooltip: 'Asigna el tiempo del vendedor',
                        hidden: boolOcultarTiempo,
                        handler: function()
                        {
                            asignarVendedor("asignar_vendedor_tiempo");
                        }
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
                            if( strEsAsistente == "S" && !Ext.isEmpty(strPrefijoEmpresa) && strPrefijoEmpresa == "TN")
                            {
                                eliminarSeleccionVendedor(gridEmpleadosAsignaciones);
                            }
                            else
                            {
                                eliminarSeleccion(gridEmpleadosAsignaciones);
                            }
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
        title: strTituloEmpAsignados,
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
        width: 510,
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
    var boolContinuar           = true;
    
    if( modelStoreEmpDepartamento.getSelection().length > 0 )
    {
        if( strNombreArea == 'Comercial' && strPrefijoEmpresa == "TN" )
        {
            if( Ext.isEmpty(intIdCargoSeleccionado) )
            {
                boolContinuar = false;
                Ext.Msg.alert('Atención', 'No se puede proceder asignar los empleados puesto que no se encuentra el cargo de vendedor para ser ' + 
                              'asignado');
            }//( Ext.isEmpty(intIdCargoSeleccionado) )
        }//( strNombreArea == 'Comercial' && strPrefijoEmpresa == "TN" )
        
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
                        url: strUrlCambioJefe,
                        method: 'post',
                        params:
                        {
                            intIdJefe: intIdJefeSeleccionado,
                            strIdPersonaEmpresaRol: strIdPersonasEmpresaRol,
                            intIdCargoSeleccionado: intIdCargoSeleccionado,
                            strNombreArea: strNombreArea
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
        }//( boolContinuar )
    }
    else
    {
        Ext.Msg.alert('Atención', 'Seleccione por lo menos un empleado de la lista');
    }//( modelStoreEmpDepartamento.getSelection().length > 0 )
}

function asignarVendedor(strAccion)
{
    var strVendTemp             = '';
    var bollContinuar           = true;
    var objExpRegular           = /^[0-9]+$/;
    var intCantidadVendTemp     = gridEmpleadosAsignaciones.getStore().getCount();
    var strIdPersonasEmpresaRol = '';

    if( modelStoreEmpDepartamento.getSelection().length > 0 && strAccion == 'asignar_vendedor' )
    {
        if( strNombreArea == 'Comercial' && strPrefijoEmpresa == "TN" )
        {
            Ext.Msg.confirm('Alerta', 'Se asignaran los Vendedores. Desea continuar?', function(btn) 
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
                        url: strUrlAsignacionVendedor,
                        method: 'post',
                        timeout: 9000000,
                        params:
                        {
                            intIdJefe: intIdJefeSeleccionado,
                            strIdPersonaEmpresaRol: strIdPersonasEmpresaRol,
                            strNombreArea: strNombreArea,
                            strAccion: strAccion
                        },
                        success: function(response)
                        {
                            if( response.responseText == 'OK')
                            {
                                Ext.Msg.alert('Información', 'Se ha asignado los vendedores con éxito');
                            }
                            else
                            {
                                Ext.Msg.alert('Error', 'Hubo un problema al asignar los vendedores'); 
                            }

                            storeEmpleadosAsignados.load();
                            storeEmpleadosDepartamento.load();
                        },
                        failure: function(result)
                        {
                            Ext.Msg.alert('Error ', 'Se presentaron errores al asignar los vendedores, favor notificar a sistemas.');

                        }
                    });
                }
            });
        }
    }
    else if( strAccion == "asignar_vendedor_tiempo" && intCantidadVendTemp > 0 )
    {
        for(var intContador=0; intContador < intCantidadVendTemp ; ++intContador) 
        {
            var strTiempoLimite = gridEmpleadosAsignaciones.getStore().getAt(intContador).get('strTiempoLimite');
            if (strTiempoLimite != null && strTiempoLimite != '')
            {
                intIdVend = gridEmpleadosAsignaciones.getStore().getAt(intContador).get('intIdPersonaEmpresaRol');
                strVendTemp = strVendTemp +strTiempoLimite+':'+intIdVend;

                if( objExpRegular.test(strTiempoLimite) && strVendTemp != null && strVendTemp !='' && parseInt(strTiempoLimite) > 0)
                {
                    strVendTemp = strVendTemp + '|';
                }
                else
                {
                    bollContinuar = false;
                    break ;
                }
            }
        }    
        if( bollContinuar )
        {
            connEsperaAccion.request
            ({
                url: strUrlAsignacionVendedor,
                method: 'post',
                params:
                {
                    intIdJefe: intIdJefeSeleccionado,
                    strVendTemp: strVendTemp,
                    strNombreArea: strNombreArea,
                    strAccion: strAccion
                },
                success: function(response)
                {
                    if( response.responseText == 'OK')
                    {
                        Ext.Msg.alert('Información', 'Se ha asignado los tiempos a los vendedores con éxito');
                    }
                    else
                    {
                        Ext.Msg.alert('Error', 'Hubo un problema al asignar los tiempos a los vendedores'); 
                    }
                    storeEmpleadosAsignados.load();
                    storeEmpleadosDepartamento.load();
                },
                failure: function(result)
                {
                    Ext.Msg.alert('Error ', 'Se presentaron errores al asignar los tiempos a los vendedores, favor notificar al departamento de sistemas.');
                }
            });
        }
        else
        {
            Ext.Msg.alert('Error', 'Por favor ingresar solo números enteros mayor a cero'); 
        }
    }
    else
    {
        Ext.Msg.alert('Atención', 'Seleccione por lo menos un empleado de la lista');
    }
}


function eliminarSeleccionVendedor(grid)
{
    var strIdPersonasEmpresaRol = '';

    Ext.Msg.confirm('Alerta', 'Se eliminaran los vendedores asignados que han sido seleccionados. Desea continuar?', function(btn)
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
                url: strUrlCambioVendedor,
                timeout: 9000000,
                method: 'post',
                params:
                {
                    intIdAsist: intIdJefeSeleccionado,
                    strIdPersonaEmpresaRol: strIdPersonasEmpresaRol,
                    strNombreArea: strNombreArea
                },
                success: function(response)
                {
                    if( response.responseText == 'OK')
                    {
                        Ext.Msg.alert('Información', 'Se han eliminado los vendedores asignados que fueron seleccionados');
                    }
                    else
                    {
                        Ext.Msg.alert('Error', 'Hubo un problema al eliminar los vendedores asignados que fueron seleccionados'); 
                    }
                    
                    storeEmpleadosAsignados.load();
                    storeEmpleadosDepartamento.load();
                },
                failure: function(result)
                {
                    Ext.Msg.alert('Error ', 'Se presentaron errores al eliminar, favor notificar al departamento de sistemas.');
                }
            });
        }
    });
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


function asignarMetaMasivaSeleccion(grid)
{
    var formPanel = Ext.create('Ext.form.Panel',
        {
            id: 'formAsignarMeta',
            bodyPadding: 2,
            waitMsgTarget: true,
            fieldDefaults: 
            {
                labelAlign: 'left',
                labelWidth: 125,
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
                        width: 700
                    },
                    layout:
                    {
                        type: 'table',
                        columns: 4,
                        align: 'left'
                    },
                    items:
                    [
                        {
                            xtype: 'numberfield',
                            fieldLabel: 'Meta Bruta (Ventas) *',
                            width: '600',
                            name: 'strMetaBruta',
                            id: 'strMetaBruta',
                            colspan: 4,
                            hideTrigger:true,
                            listeners: 
                            {
                                keyup:
                                {
                                    element: 'el',
                                    fn: function(event,target)
                                    { 
                                        getValorMetaActiva();
                                    }
                                },  
                            }
                        },
                        {
                            xtype: 'numberfield',
                            fieldLabel: 'Meta Activa (%) *',
                            width: 200,
                            id: 'strMetaActiva',
                            name: 'strMetaActiva',
                            colspan: 2,
                            hideTrigger:true,
                            style: 
                            {
                                width: '10%',
                            },
                            listeners: 
                            {
                                keyup:
                                {
                                    element: 'el',
                                    fn: function(event,target)
                                    { 
                                        getValorMetaActiva();
                                    }
                                },  
                            }
                        },
                        {
                            xtype: 'displayfield',
                            value: '=',
                            width: 10,
                            style: 
                            {
                                marginRight: '5px',
                                marginLeft: '5px'
                            }
                        },
                        {
                            xtype: 'displayfield',
                            id: 'strMetaActivaValor',
                            name: 'strMetaActivaValor',
                            value: '0'
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
                        var form = Ext.getCmp('formAsignarMeta').getForm();

                        if( form.isValid() )
                        {
                            var strMetaBruta  = Ext.getCmp('strMetaBruta').getValue();
                            var strMetaActiva = Ext.getCmp('strMetaActiva').getValue();

                            if ( strMetaBruta != '0' && strMetaBruta != null && strMetaBruta != '' 
                                 && strMetaActiva != null && strMetaActiva != '' && strMetaActiva != '0' )
                            {               
                                var strIdPersonasEmpresaRol = '';
                                
                                Ext.Msg.confirm('Alerta', 'Se agregará Meta Masiva a los empleados que han sido seleccionados.\n\
                                                           Desea continuar?',
                                function(btn)
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
                                        
                                        var arrayParametros                             = [];
                                            arrayParametros['intIdPersonalEmpresaRol']  = strIdPersonasEmpresaRol;
                                            arrayParametros['valor']                    = strMetaBruta+'|'+strMetaActiva;
                                            arrayParametros['caracteristica']           = strCaracteristicaMetaBruta+'|'
                                                                                          +strCaracteristicaMetaActiva;
                                            arrayParametros['accion']                   = 'Guardar';
                                            arrayParametros['store']                    = storeEmpleadosAsignados;
                                        
                                        ajaxAsignarCaracteristica(arrayParametros);
                                    }
                                });
                            }
                            else
                            {
                                Ext.Msg.alert('Atenci\xf3n', 'Todos los valores son requeridos');
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
               title: 'Asignar Meta',
               modal: true,
               width: 350,
               closable: true,
               layout: 'fit',
               items: [formPanel]
          }).show();
}