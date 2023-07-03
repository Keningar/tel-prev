var grid  = null;
var win   = null;
var storeAsignacionProvisionalChofer=null;
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
                    msg: 'Generando pdf, Por favor espere!!',
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

    var dateActual=new Date();
    var firstDay = new Date(dateActual.getFullYear(), dateActual.getMonth(), 1);
    var lastDay=new Date(dateActual.getFullYear(),dateActual.getMonth()+1,0);   

    DTFechaDesdeAPChofer = new Ext.form.DateField({
            xtype: 'datefield',
            id: 'fechaDesdeAsignacionAPChofer',
            name:'fechaDesdeAsignacionAPChofer',
            format: 'd/m/Y',
            fieldLabel: '<b>Desde</b>',
            editable: false,
            emptyText: "Seleccione",
            labelWidth: 150,
            value: firstDay,
            listeners: {
                select: function(cmp, newValue, oldValue) {
                    validarFechasFiltro(cmp);
                }
            }
     });

    DTFechaHastaAPChofer = new Ext.form.DateField({
        xtype: 'datefield',
        id: 'fechaHastaAsignacionAPChofer',
        name:'fechaHastaAsignacionAPChofer',
        editable: false,
        fieldLabel: '<b>Hasta</b>',
        format: 'd/m/Y',
        emptyText: "Seleccione",
        labelWidth: 150,
        value: lastDay,
        listeners: {
            select: function(cmp, newValue, oldValue) {
                validarFechasFiltro(cmp);
            }
        }
    }); 
    storeAsignacionProvisionalChofer = new Ext.data.Store
    ({ 
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
            timeout: 600000,
            url : strUrlGridHistorialAsignacionProvisionalChofer,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams:
            {
                fechaDesde: Ext.getCmp('fechaDesdeAsignacionAPChofer').getSubmitValue(),
                fechaHasta: Ext.getCmp('fechaHastaAsignacionAPChofer').getSubmitValue(),
                errorFechas: 0
            }
        },
        fields:
        [
            {name:'strDptoZonaAsignacionProvisionalHisto',              mapping:'strDptoZonaAsignacionProvisionalHisto'},
            {name:'strCoordinadorAsignacionProvisionalHisto',           mapping:'strCoordinadorAsignacionProvisionalHisto'},
            {name:'strModeloAsignacionProvisionalHisto',                mapping:'strModeloAsignacionProvisionalHisto'},
            {name:'strPlacaAsignacionProvisionalHisto',                 mapping:'strPlacaAsignacionProvisionalHisto'},
            {name:'strDiscoAsignacionProvisionalHisto',                 mapping:'strDiscoAsignacionProvisionalHisto'},
            {name:'nombreLiderCuadrillaProvisionalHisto',               mapping:'nombreLiderCuadrillaProvisionalHisto'},
            {name:'strConductorAReemplazarAsignacionProvisionalHisto',  mapping:'strConductorAReemplazarAsignacionProvisionalHisto'},
            {name:'strConductorReemplazoAsignacionProvisionalHisto',    mapping:'strConductorReemplazoAsignacionProvisionalHisto'},
            {name:'strMotivoAsignacionProvisionalHisto',                mapping:'strMotivoAsignacionProvisionalHisto'},
            {name:'strObservacionProvisionalHisto',                     mapping:'strObservacionProvisionalHisto'},
            {name:'strFechaHoraAsignacionProvisionalHisto',             mapping:'strFechaHoraAsignacionProvisionalHisto'}
            
        ],
        autoLoad: true
    });

    grid = Ext.create('Ext.grid.Panel', 
    {
        id : 'grid',
        width: '100%',
        height: 400,
        store: storeAsignacionProvisionalChofer,
        plugins: 
        [
            {ptype : 'pagingselectpersist'}
        ],
        viewConfig: 
        {
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
            {
                header: 'DPTO / ZONA',
                dataIndex: 'strDptoZonaAsignacionProvisionalHisto',
                width: 300
            },
            {
                header: 'COORDINADOR',
                dataIndex: 'strCoordinadorAsignacionProvisionalHisto',
                width: 300
            },
            {
                header: 'VEHÍCULO',
                dataIndex: 'strModeloAsignacionProvisionalHisto',
                width: 150
            },
            {
                header: 'PLACA',
                dataIndex: 'strPlacaAsignacionProvisionalHisto',
                width: 75
            },
            {
                header: 'DISCO',
                dataIndex: 'strDiscoAsignacionProvisionalHisto',
                width: 75
            },
            {
                header: 'LÍDER DE CUADRILLA',
                dataIndex: 'nombreLiderCuadrillaProvisionalHisto',
                width: 300
            },
            {
                header: 'CONDUCTOR A REEMPLAZAR',
                dataIndex: 'strConductorAReemplazarAsignacionProvisionalHisto',
                width: 300
            },
            {
                header: 'ASIGNACIÓN PROVISIONAL',
                dataIndex: 'strConductorReemplazoAsignacionProvisionalHisto',
                width: 300
            },
            {
                header: 'MOTIVO',
                dataIndex: 'strMotivoAsignacionProvisionalHisto',
                width: 200
            },
            {
                header: 'OBSERVACIÓN',
                dataIndex: 'strObservacionProvisionalHisto',
                width: 300
            },
            {
                header: 'FECHA / HORA',
                dataIndex: 'strFechaHoraAsignacionProvisionalHisto',
                width: 250
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
                    { xtype: 'tbfill' },
                    {
                        iconCls: 'icon_exportar_pdf',
                        text: 'Exportar',
                        scope: this,
                        handler: function()
                        {
                            var permiso = $("#ROLE_328-3677");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if(!boolPermiso)
                            { 
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }
                            else
                            {
                                document.getElementById("formAsignacionesProvisionales").submit();
                                //exportarPDF();
                            }
                            
                        }
                    }
                ]
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar', 
        {
            store: storeAsignacionProvisionalChofer,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        title: 'Asignación Conductor Provisional',
        renderTo: 'grid'
    });


    /* ******************************************* */
                /* FILTROS DE BUSQUEDA */
    /* ******************************************* */
    var filterPanel = Ext.create('Ext.panel.Panel', 
        {
            bodyPadding: 7, 
            border:false,
            buttonAlign: 'center',
            layout: 
            {
                type:'vbox',
                columns: 5,
                align: 'left'
            },
            bodyStyle: 
            {
                background: '#fff'
            },   
            collapsible : true,
            collapsed: false,
            width: '100%',
            title: 'Criterios de busqueda',
            buttons: 
            [
                {
                    text: 'Buscar',
                    iconCls: "icon_search",
                    handler: function()
                    { 
                        buscarAsignacionProvisionalChofer();
                    }
                },
                {
                    text: 'Limpiar',
                    iconCls: "icon_limpiar",
                    handler: function()
                    { 
                        limpiarAsignacionProvisionalChofer();
                    }
                }
            ],                
            items: 
            [
            
                {
                    layout: 'table',
                    border: false,
                    items: 
                    [
                        {html:"&nbsp;",border:false,width:150},
                        {
                            width: 290,
                            layout: 'form',
                            border: false,
                            labelWidth:50,
                            items: 
                            [
                                DTFechaDesdeAPChofer

                            ]
                        },
                        {html:"&nbsp;",border:false,width:100},
                        {
                            width:10,
                            layout: 'form',
                            border: false,
                            items: 
                            [
                                {
                                    xtype: 'displayfield'
                                }
                            ]
                        },
                        {html:"&nbsp;",border:false,width:150},
                        {
                            width: 290,
                            layout: 'form',
                            border: false,
                            labelWidth:50,
                            items: 
                            [
                                DTFechaHastaAPChofer

                            ]
                        },
                        {html:"&nbsp;",border:false,width:150},
                    ]
                },
                
                
                
                {
                    layout: 'table',
                    border: false,
                    items: 
                    [
                        {html:"&nbsp;",border:false,width:150},
                        {
                            width: 290,
                            layout: 'form',
                            border: false,
                            labelWidth:50,
                            items: 
                            [
                                {
                                    xtype: 'textfield',
                                    id: 'strBuscarXPlaca',
                                    name : 'strBuscarXPlaca',
                                    fieldLabel: '<b>Placa</b>',
                                    labelWidth:150,
                                    value: '',
                                    enableKeyEvents: true,
                                    listeners:
                                    {
                                        keyup: function(form, e)
                                        {
                                            convertirTextoEnMayusculas('strBuscarXPlaca-inputEl');
                                        }
                                    }
                                }

                            ]
                        },
                        {html:"&nbsp;",border:false,width:100},
                        {
                            width:10,
                            layout: 'form',
                            border: false,
                            items: 
                            [
                                {
                                    xtype: 'displayfield'
                                }
                            ]
                        },
                        {html:"&nbsp;",border:false,width:150},
                        {
                            width: 290,
                            layout: 'form',
                            border: false,
                            labelWidth:50,
                            items: 
                            [
                                {
                                    xtype: 'textfield',
                                    id: 'strBuscarXDisco',
                                    name : 'strBuscarXDisco',
                                    fieldLabel: '<b>Disco</b>',
                                    labelWidth:150,
                                    value: '',
                                    enableKeyEvents: true
                                }

                            ]
                        },
                        {html:"&nbsp;",border:false,width:150}
                        
                    ]
                },
                
                
                {
                    layout: 'table',
                    border: false,
                    items: 
                    [
                        {html:"&nbsp;",border:false,width:150},
                        {
                            width: 290,
                            layout: 'form',
                            border: false,
                            labelWidth:50,
                            items: 
                            [
                                {
                                    xtype: 'textfield',
                                    id: 'strBuscarXIdentificacionChoferProvisional',
                                    name : 'strBuscarXIdentificacionChoferProvisional',
                                    fieldLabel: '<b>Identificación Chofer Provisional</b>',
                                    labelWidth:150,
                                    value: ''
                                }

                            ]
                        },
                        {html:"&nbsp;",border:false,width:100},
                        {
                            width:10,
                            layout: 'form',
                            border: false,
                            items: 
                            [
                                {
                                    xtype: 'displayfield'
                                }
                            ]
                        },
                        {html:"&nbsp;",border:false,width:150},
                        {
                            width: 290,
                            layout: 'form',
                            border: false,
                            labelWidth:50,
                            items: 
                            [
                                {
                                    xtype: 'displayfield'
                                }

                            ]
                        },
                        {html:"&nbsp;",border:false,width:150}
                        
                    ]
                },
                
                
                
                
                {
                    layout: 'table',
                    border: false,
                    items: 
                    [
                        {html:"&nbsp;",border:false,width:150},
                        {
                            width: 290,
                            layout: 'form',
                            border: false,
                            labelWidth:50,
                            items: 
                            [
                                {
                                    xtype: 'textfield',
                                    id: 'strBuscarXNombresChoferProvisional',
                                    name : 'strBuscarXNombresChoferProvisional',
                                    fieldLabel: '<b>Nombres Chofer Provisional</b>',
                                    labelWidth:150,
                                    value: ''
                                }

                            ]
                        },
                        {html:"&nbsp;",border:false,width:100},
                        {
                            width:10,
                            layout: 'form',
                            border: false,
                            items: 
                            [
                                {
                                    xtype: 'displayfield'
                                }
                            ]
                        },
                        {html:"&nbsp;",border:false,width:150},
                        {
                            width: 290,
                            layout: 'form',
                            border: false,
                            labelWidth:50,
                            items: 
                            [
                                {
                                    xtype: 'textfield',
                                    id: 'strBuscarXApellidosChoferProvisional',
                                    name : 'strBuscarXApellidosChoferProvisional',
                                    fieldLabel: '<b>Apellidos Chofer Provisional</b>',
                                    labelWidth:150,
                                    value: ''
                                }

                            ]
                        },
                        {html:"&nbsp;",border:false,width:150}
                        
                    ]
                }
            ],
            renderTo: 'filtro'
        }); 

});


/* ******************************************* */
            /*  FUNCIONES  */
/* ******************************************* */


function buscarAsignacionProvisionalChofer()
{
    storeAsignacionProvisionalChofer.loadData([],false);
    storeAsignacionProvisionalChofer.currentPage = 1;
    storeAsignacionProvisionalChofer.getProxy().extraParams.fechaDesde          = Ext.getCmp('fechaDesdeAsignacionAPChofer').getSubmitValue();
    storeAsignacionProvisionalChofer.getProxy().extraParams.fechaHasta          = Ext.getCmp('fechaHastaAsignacionAPChofer').getSubmitValue();
    
    storeAsignacionProvisionalChofer.getProxy().extraParams.identificacionChoferProvisional = Ext.getCmp('strBuscarXIdentificacionChoferProvisional').getValue();
    storeAsignacionProvisionalChofer.getProxy().extraParams.nombresChoferProvisional        = Ext.getCmp('strBuscarXNombresChoferProvisional').getValue();
    storeAsignacionProvisionalChofer.getProxy().extraParams.apellidosChoferProvisional      = Ext.getCmp('strBuscarXApellidosChoferProvisional').getValue();
    
    
    storeAsignacionProvisionalChofer.getProxy().extraParams.placa      = Ext.getCmp('strBuscarXPlaca').getValue();
    storeAsignacionProvisionalChofer.getProxy().extraParams.disco      = Ext.getCmp('strBuscarXDisco').getValue();
    
    storeAsignacionProvisionalChofer.load();
}


function limpiarAsignacionProvisionalChofer()
{
    var dateActual=new Date();
    var firstDay = new Date(dateActual.getFullYear(), dateActual.getMonth(), 1);
    var lastDay=new Date(dateActual.getFullYear(),dateActual.getMonth()+1,0); 

    Ext.getCmp('fechaDesdeAsignacionAPChofer').setValue(firstDay);
    Ext.getCmp('fechaHastaAsignacionAPChofer').setValue(lastDay);

    storeAsignacionProvisionalChofer.loadData([],false);
    storeAsignacionProvisionalChofer.currentPage = 1;
    storeAsignacionProvisionalChofer.getProxy().extraParams.fechaDesde          = Ext.getCmp('fechaDesdeAsignacionAPChofer').getSubmitValue();
    storeAsignacionProvisionalChofer.getProxy().extraParams.fechaHasta          = Ext.getCmp('fechaHastaAsignacionAPChofer').getSubmitValue();
    
    
    Ext.getCmp('strBuscarXPlaca').value="";
    Ext.getCmp('strBuscarXPlaca').setRawValue("");
    
    Ext.getCmp('strBuscarXDisco').value="";
    Ext.getCmp('strBuscarXDisco').setRawValue("");
    
    Ext.getCmp('strBuscarXIdentificacionChoferProvisional').value="";
    Ext.getCmp('strBuscarXIdentificacionChoferProvisional').setRawValue("");
    Ext.getCmp('strBuscarXNombresChoferProvisional').value="";
    Ext.getCmp('strBuscarXNombresChoferProvisional').setRawValue("");
    Ext.getCmp('strBuscarXApellidosChoferProvisional').value="";
    Ext.getCmp('strBuscarXApellidosChoferProvisional').setRawValue("");
    
    storeAsignacionProvisionalChofer.getProxy().extraParams.identificacionChoferProvisional = Ext.getCmp('strBuscarXIdentificacionChoferProvisional').getValue();
    storeAsignacionProvisionalChofer.getProxy().extraParams.nombresChoferProvisional        = Ext.getCmp('strBuscarXNombresChoferProvisional').getValue();
    storeAsignacionProvisionalChofer.getProxy().extraParams.apellidosChoferProvisional      = Ext.getCmp('strBuscarXApellidosChoferProvisional').getValue();
    
    
    storeAsignacionProvisionalChofer.getProxy().extraParams.placa      = Ext.getCmp('strBuscarXPlaca').getValue();
    storeAsignacionProvisionalChofer.getProxy().extraParams.disco      = Ext.getCmp('strBuscarXDisco').getValue();
    storeAsignacionProvisionalChofer.load();

}


function validarFechasFiltro(cmp)
{
    var strTipoAsignacion="Asignaciones Provisionales de Choferes";
    var storeHistorialPrincipal=storeAsignacionProvisionalChofer;
    var fieldFechaDesdeAsignacion=Ext.getCmp('fechaDesdeAsignacionAPChofer');
    var valFechaDesdeAsignacion=fieldFechaDesdeAsignacion.getSubmitValue();

    var fieldFechaHastaAsignacion=Ext.getCmp('fechaHastaAsignacionAPChofer');
    var valFechaHastaAsignacion=fieldFechaHastaAsignacion.getSubmitValue();


    var boolOKFechas= true;
    var boolCamposLLenos=false;
    var strMensaje  = '';

    if(valFechaDesdeAsignacion && valFechaHastaAsignacion)
    {
        var valCompFechaDesdeAsignacion = Ext.Date.parse(valFechaDesdeAsignacion, "d/m/Y");
        var valCompFechaHastaAsignacion = Ext.Date.parse(valFechaHastaAsignacion, "d/m/Y");

        if ((isNaN(fieldFechaDesdeAsignacion.value) || isNaN(fieldFechaHastaAsignacion.value)) || 
            (fieldFechaDesdeAsignacion.value==="" || fieldFechaHastaAsignacion.value==="" ))
        {
            boolOKFechas=false;
            strMensaje= "Los campos de las fechas en las "+strTipoAsignacion+" no pueden estar vacías";
            Ext.Msg.alert('Atenci\xf3n ', strMensaje);
        }
        else if(valCompFechaDesdeAsignacion>valCompFechaHastaAsignacion)
        {
            boolOKFechas=false;
            strMensaje='La Fecha Desde '+ valFechaDesdeAsignacion +' no puede ser mayor a la Fecha Hasta '+valFechaHastaAsignacion+
                        " en las "+strTipoAsignacion;
            Ext.Msg.alert('Atenci\xf3n', strMensaje); 
        }
    }

    if(valFechaDesdeAsignacion && valFechaHastaAsignacion )
    {
        boolCamposLLenos=true;
    }


    if(boolOKFechas && boolCamposLLenos)
    {
        var objExtraParams = storeHistorialPrincipal.proxy.extraParams;
        objExtraParams.errorFechas              = 0;
        objExtraParams.fechaDesde  = valFechaDesdeAsignacion;
        objExtraParams.fechaHasta  = valFechaHastaAsignacion;

    }
    else if(!boolOKFechas )
    {
        cmp.value = "";
        cmp.setRawValue("");
        var objExtraParams = storeHistorialPrincipal.proxy.extraParams;
        objExtraParams.errorFechas=1;
        storeHistorialPrincipal.load();

    }
}

function exportarPDF()
{
    var objExtraParams = storeAsignacionProvisionalChofer.proxy.extraParams;
    var errorFechas = objExtraParams.errorFechas;
    if(errorFechas)
    {
        Ext.Msg.alert('Atenci\xf3n ', "Existe un error en las fechas de búsqueda");
    }
    else
    {
        document.getElementById("formAsignacionesProvisionales").submit();
    }
}

function convertirTextoEnMayusculas(idTexto)
{
    var strTexto      = document.getElementById(idTexto).value;
    var strMayusculas = strTexto.toUpperCase(); 
    
    document.getElementById(idTexto).value = strMayusculas;
}