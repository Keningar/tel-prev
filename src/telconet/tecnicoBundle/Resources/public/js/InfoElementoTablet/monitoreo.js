$("[data-mask]").inputmask();
var arrayIdTablet               = [];
var arrayImeiTablet             = [];
var arrayResponsable            = [];
var arrayNombreDepartamentoPer  = [];
var arrayNombreCuadrilla        = [];
var arrayNombreDepCuadrilla     = [];
var arrayNombreZonaCuadrilla    = [];
var arrayTurnoHoraInicio        = [];
var arrayTurnoHoraFin           = [];
var arrayLatitud                = [];
var arrayLongitud               = [];
var arrayEstado                 = [];
var arrayFechaUltimoIntento     = [];
var arrayFechaUltimoPunto       = [];

arrayXhrPool                    = [];

var mapa;
var arrayInfoWindow             = [];
var arrayMarkerPto              = [];
var openInfoWindow              = null;
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
                if(!boolLoadFirstTime)
                {
                    arrayXhrPool.forEach(function(jqXHR) {
                        jqXHR.abort();
                        
                    });
                    arrayXhrPool = [];
                    Ext.Ajax.abortAll();
                }
                Ext.MessageBox.show
                ({
                   msg: 'Cargando la información, por favor espere!!',
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
function cargarCriteriosBusquedaAvanzada()
{
    var cmbFiltroRegionPerBusqAvanzada = new Ext.form.ComboBox
    ({
        id: 'busqCmbRegionPerBusqAvanzada',
        name: 'busqCmbRegionPerBusqAvanzada',
        fieldLabel: 'Región',
        anchor: '100%',
        queryMode: 'local',
        width: '100%',
        emptyText: 'Seleccione Región',
        store: [
            ['','Todas'],
            ['R1','R1'],
            ['R2','R2']
        ]
    });

    var cmbDepartamentosPerBusqAvanzada = new Ext.form.ComboBox
    ({
        id: 'busqCmbDepartamentoPerBusqAvanzada',
        name: 'busqCmbDepartamentoPerBusqAvanzada',
        fieldLabel: 'Departamento',
        anchor: '100%',
        queryMode: 'remote',
        width: '100%',
        emptyText: 'Seleccione Departamento',
        store: storeDepartamentosPerBusqAvanzada,
        minChars : 3,
        displayField: 'strNombreDepartamento',
        valueField: 'intIdDepartamento',
        forceSelection: true
    });
    
    var cmbFiltroCantonPerBusqAvanzada = new Ext.form.ComboBox
    ({
        id: 'busqCmbCantonPerBusqAvanzada',
        name: 'busqCmbCantonPerBusqAvanzada',
        fieldLabel: 'Cantón',
        anchor: '100%',
        queryMode: 'remote',
        width: '100%',
        emptyText: 'Seleccione Departamento',
        store: storeCantonesPerBusqAvanzada,
        displayField: 'strNombreCanton',
        valueField: 'intIdCanton',
        forceSelection: true
    });
    
    var cmbDepartamentosCuadrillaBusqAvanzada = new Ext.form.ComboBox
    ({
        id: 'busqCmbDepartamentoCuadrillaBusqAvanzada',
        name: 'busqCmbDepartamentoCuadrillaBusqAvanzada',
        fieldLabel: '<b>Departamento</b>',
        anchor: '100%',
        queryMode: 'remote',
        width: '100%',
        emptyText: 'Seleccione Departamento',
        store: storeDepartamentosCuadrillaBusqAvanzada,
        minChars : 3,
        displayField: 'strNombreDepartamento',
        valueField: 'intIdDepartamento',
        forceSelection: true
    });
    
    var cmbZonasCuadrillaBusqAvanzada = new Ext.form.ComboBox
    ({
        id: 'busqCmbZonaCuadrillaBusqAvanzada',
        name: 'busqCmbZonaCuadrillaBusqAvanzada',
        fieldLabel: '<b>Zona</b>',
        anchor: '100%',
        queryMode: 'remote',
        width: '100%',
        emptyText: 'Seleccione Zona',
        store: storeZonasCuadrillaBusqAvanzada,
        displayField: 'strNombreZona',
        valueField: 'intIdZona',
        forceSelection: true
    });
    
    var cmbModelosBusqAvanzada = new Ext.form.ComboBox
    ({
        id: 'busqCmbModeloBusqAvanzada',
        name: 'busqCmbModeloBusqAvanzada',
        fieldLabel: '<b>Modelo de Tablet</b>',
        anchor: '100%',
        queryMode: 'remote',
        width: '100%',
        emptyText: 'Seleccione Modelo',
        store: storeModelosTabletsBusqAvanzada,
        displayField: 'strNombreModeloElemento',
        valueField: 'intIdModeloElemento',
        forceSelection: true
    });


    var cmbFiltrarMisCuadrillas = new Ext.form.ComboBox
    ({
        id: 'busqCmbFiltrarMisCuadrillasBusqAvanzada',
        name: 'busqCmbFiltrarMisCuadrillasBusqAvanzada',
        fieldLabel: '<b>Filtrar Mis Cuadrillas</b>',
        anchor: '100%',
        queryMode: 'local',
        width: '100%',
        store: [
            ['SI','SI'],
            ['NO','NO']
        ],
        value: 'NO',
        editable: false,
        forceSelection: true
    });
    
    var cmbFiltrarPorHorarioBusqAvanzada = new Ext.form.ComboBox
    ({
        id: 'busqCmbFiltrarPorHorarioBusqAvanzada',
        name: 'busqCmbFiltrarPorHorarioBusqAvanzada',
        fieldLabel: '<b>Filtrar por Horario</b>',
        anchor: '100%',
        queryMode: 'local',
        width: '100%',
        store: [
            ['SI','SI'],
            ['NO','NO']
        ],
        value: 'NO',
        editable: false,
        forceSelection: true
    });
    
    

    var formPanelFilterMonitoreoBusqAvanzada = Ext.create('Ext.form.Panel', {
        bodyPadding: 5,
        waitMsgTarget: true,
        id:"panelFiltrosBusqAvanzada",
        fieldDefaults: {
           labelAlign: 'left',
           msgTarget: 'side'
        },
        defaults: {
           margins: '0 0 0 0'
        },
        layout:
        {
            type:'table',
            columns: 3,
            align: 'left'
        },
        buttons: 
        [                   
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function()
                {
                    boolBusquedaGlobal= true;
                    generarBusquedaAvanzada();
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function()
                { 
                    limpiarBusquedaAvanzadaYGeneral();
                }
            }
        ],
        items: [
           {
                xtype: 'fieldset',
                title: '',
                defaultType: 'textfield',
                width: '100%',
                items:
                [
                    {
                        xtype: 'fieldset',
                        title: 'Criterios del Usuario',                       
                        width: '100%',
                        items: 
                        [
                            {
                                 layout: 'table',
                                 border: false,
                                 items: 
                                 [
                                     {
                                         width: 280,
                                         layout: 'form',
                                         border: false,
                                         labelWidth:50,
                                         items: 
                                         [
                                             cmbFiltroRegionPerBusqAvanzada,
                                             cmbFiltroCantonPerBusqAvanzada
                                         ]
                                     },
                                     {
                                         width: 60,
                                         layout: 'form',
                                         border: false,
                                         items: 
                                         [
                                             {
                                                 xtype: 'displayfield'
                                             }
                                         ]
                                     },
                                     {
                                         width: 280,
                                         layout: 'form',
                                         border: false,
                                         items: 
                                         [
                                             cmbDepartamentosPerBusqAvanzada
                                         ]
                                     }
                                 ]
                             }

                        ]
                    },
                    {
                        xtype: 'fieldset',
                        title: 'Criterios de la Cuadrilla',
                        width: '100%',
                        items: 
                        [
                            {
                                layout: 'table',
                                border: false,
                                items: 
                                [
                                    {
                                        width: 280,
                                        layout: 'form',
                                        border: false,
                                        labelWidth:50,
                                        items: 
                                        [
                                            cmbDepartamentosCuadrillaBusqAvanzada,
                                            cmbFiltrarMisCuadrillas
                                        ]
                                    },
                                    {
                                        width: 60,
                                        layout: 'form',
                                        border: false,
                                        items: 
                                        [
                                            {
                                                xtype: 'displayfield'
                                            },
                                            {
                                                xtype: 'displayfield'
                                            }
                                        ]
                                    },
                                    {
                                        width: 280,
                                        layout: 'form',
                                        border: false,
                                        labelWidth:50,
                                        items: 
                                        [
                                            cmbZonasCuadrillaBusqAvanzada,
                                            cmbFiltrarPorHorarioBusqAvanzada
                                        ]
                                    }
                                ]
                            }
                        ]
                    },
                    {
                        xtype: 'fieldset',
                        title: 'Criterios de la Tablet',
                        width: '100%',
                        items: 
                        [
                            {
                                layout: 'table',
                                border: false,
                                items: 
                                [
                                    {
                                        width: 280,
                                        layout: 'form',
                                        border: false,
                                        labelWidth:50,
                                        items: 
                                        [
                                            cmbModelosBusqAvanzada
                                        ]
                                    },
                                    {
                                        width: 60,
                                        layout: 'form',
                                        border: false,
                                        items: 
                                        [
                                            {
                                                xtype: 'displayfield'
                                            },
                                            {
                                                xtype: 'displayfield'
                                            }
                                        ]
                                    },
                                    {
                                        width: 280,
                                        layout: 'form',
                                        border: false,
                                        labelWidth:50,
                                        items: 
                                        [
                                            
                                        ]
                                    }
                                ]
                            }
                        ]
                   }
               ]
           }
        ]
    });
    
    winPanelFilterMonitoreoBusqAvanzada = Ext.create('Ext.window.Window',
    {
      title: 'Criterios de Búsqueda Avanzada',
      modal: true,
      width: 700,
      closable: true,
      layout: 'fit',
      floating: true,
      shadow: true,
      shadowOffset:20,
      resizable:true,
      items: [formPanelFilterMonitoreoBusqAvanzada]
    }).show();
    
}



function limpiarBusquedaAvanzadaYGeneral()
{
    intIndexEstadoMonitoreo                                                             = 0;
    document.getElementById('valueBusqEnCampoIMEI').value                               = "";
    document.getElementById('valueBusqEnCampoResponsable').value                        = "";

    document.getElementById('valueBusqMantenimientoLibreIMEI').value                    = "";
    document.getElementById('valueBusqMantenimientoLibreResponsable').value             = "";
    
    document.getElementById('valueBusqCuadrillasLibresIMEI').value                    = "";
    document.getElementById('valueBusqCuadrillasLibresResponsable').value             = "";

    document.getElementById('valueBusqCON_UBICACION_ACTUALIZADAIMEI').value             = "";
    document.getElementById('valueBusqCON_UBICACION_ACTUALIZADAResponsable').value      = "";

    document.getElementById('valueBusqCON_UBICACION_DESACTUALIZADAIMEI').value          = "";
    document.getElementById('valueBusqCON_UBICACION_DESACTUALIZADAResponsable').value   = "";

    document.getElementById('valueBusqSIN_UBICACIONIMEI').value                         = "";
    document.getElementById('valueBusqSIN_UBICACIONResponsable').value                  = "";

    document.getElementById('valueBusqSIN_INFORMACIONIMEI').value                       = "";
    document.getElementById('valueBusqSIN_INFORMACIONResponsable').value                = "";

    document.getElementById('strRegionPerBusqAvanzada').value                           = "";
    document.getElementById('intIdCantonPerBusqAvanzada').value                         = "";
    document.getElementById('strCantonPerBusqAvanzada').value                           = "";
    document.getElementById('intIdDepartamentoPerBusqAvanzada').value                   = "";
    document.getElementById('strDepartamentoPerBusqAvanzada').value                     = "";

    document.getElementById('intIdDepartamentoCuadrillaBusqAvanzada').value             = "";
    document.getElementById('strDepartamentoCuadrillaBusqAvanzada').value               = "";
    document.getElementById('intIdZonaCuadrillaBusqAvanzada').value                     = "";
    document.getElementById('strZonaCuadrillaBusqAvanzada').value                       = "";
    document.getElementById('intIdModeloBusqAvanzada').value                            = "";
    document.getElementById('strModeloBusqAvanzada').value                              = "";
    document.getElementById('strEstadoMonitoreoBusqAvanzada').value                     = "";
    document.getElementById('strFiltrarMisCuadrillasBusqAvanzada').value                = "";
    document.getElementById('strFiltrarPorHorarioBusqAvanzada').value                   = "";

    if ( Ext.getCmp('busqCmbDepartamentoCuadrillaBusqAvanzada') )
    {
        Ext.getCmp('busqCmbRegionPerBusqAvanzada').setValue(null);
        Ext.getCmp('busqCmbRegionPerBusqAvanzada').setRawValue(null);

        Ext.getCmp('busqCmbCantonPerBusqAvanzada').setValue(null);
        Ext.getCmp('busqCmbCantonPerBusqAvanzada').setRawValue(null);

        Ext.getCmp('busqCmbDepartamentoPerBusqAvanzada').setValue(null);
        Ext.getCmp('busqCmbDepartamentoPerBusqAvanzada').setRawValue(null);

        Ext.getCmp('busqCmbDepartamentoCuadrillaBusqAvanzada').setValue(null);
        Ext.getCmp('busqCmbDepartamentoCuadrillaBusqAvanzada').setRawValue(null);

        Ext.getCmp('busqCmbZonaCuadrillaBusqAvanzada').setValue(null);
        Ext.getCmp('busqCmbZonaCuadrillaBusqAvanzada').setRawValue(null);

        Ext.getCmp('busqCmbModeloBusqAvanzada').setValue(null);
        Ext.getCmp('busqCmbModeloBusqAvanzada').setRawValue(null);

        Ext.getCmp('busqCmbFiltrarMisCuadrillasBusqAvanzada').setValue('NO');
        Ext.getCmp('busqCmbFiltrarMisCuadrillasBusqAvanzada').setRawValue('TODAS');

        Ext.getCmp('busqCmbFiltrarPorHorarioBusqAvanzada').setValue('NO');
        Ext.getCmp('busqCmbFiltrarPorHorarioBusqAvanzada').setRawValue('NO');

        winPanelFilterMonitoreoBusqAvanzada.destroy();
    }
    
    intInicioEnCampo                        = intValorInicial;
    intLimiteEnCampo                        = intValorLimite;

    intInicioMantenimientoLibre             = intValorInicial;
    intLimiteMantenimientoLibre             = intValorLimite ;
    
    intInicioConUbicacionActualizada        = intValorInicial;
    intLimiteConUbicacionActualizada        = intValorLimite;
        
    intInicioConUbicacionDesactualizada     = intValorInicial;
    intLimiteConUbicacionDesactualizada     = intValorLimite;

    intInicioSinUbicacion                   = intValorInicial;
    intLimiteSinUbicacion                   = intValorLimite;

    intInicioSinInformacion                 = intValorInicial;
    intLimiteSinInformacion                 = intValorLimite;
    
    limpiarYGenerarCriteriosBusqueda();
    boolBusquedaGlobal = true;
    generarResumenPrincipal();
}


function limpiarYGenerarCriteriosBusqueda()
{
    document.getElementById("criteriosSeleccionadosPer").innerHTML          = "";
    document.getElementById("criteriosSeleccionadosCuadrilla").innerHTML    = "";
    document.getElementById("criteriosSeleccionadosTablet").innerHTML       = "";
    
    var htmlCriteriosSeleccionadosPer   = "";
    var arrayCriteriosseleccionadosPer  = [];
    arrayCriteriosseleccionadosPer.push($("#strRegionPerBusqAvanzada").val());
    arrayCriteriosseleccionadosPer.push($("#strCantonPerBusqAvanzada").val());
    arrayCriteriosseleccionadosPer.push($("#strDepartamentoPerBusqAvanzada").val());
            
    for(var i=0; i < arrayCriteriosseleccionadosPer.length ; i++)
    {
        if(arrayCriteriosseleccionadosPer[i].trim())
        {
            htmlCriteriosSeleccionadosPer += "<p class='criteriosSession'>"+arrayCriteriosseleccionadosPer[i].trim()+"</p>"
        }
    }
    
    var htmlCriteriosSeleccionadosCuadrilla   = "";
    var arrayCriteriosseleccionadosCuadrilla  = [];
        arrayCriteriosseleccionadosCuadrilla.push($("#strDepartamentoCuadrillaBusqAvanzada").val());
        arrayCriteriosseleccionadosCuadrilla.push($("#strZonaCuadrillaBusqAvanzada").val());
        
    if($("#strFiltrarMisCuadrillasBusqAvanzada").val()=="SI")
    {
        arrayCriteriosseleccionadosCuadrilla.push("MIS CUADRILLAS");
    }
    if($("#strFiltrarPorHorarioBusqAvanzada").val()=="SI")
    {
        arrayCriteriosseleccionadosCuadrilla.push("POR HORARIO");
    }
            
    for(var i=0; i < arrayCriteriosseleccionadosCuadrilla.length ; i++)
    {
        if(arrayCriteriosseleccionadosCuadrilla[i].trim())
        {
            htmlCriteriosSeleccionadosCuadrilla += "<p class='criteriosSession'>"+arrayCriteriosseleccionadosCuadrilla[i].trim()+"</p>"
        }
    }
    
    var htmlCriteriosSeleccionadosTablet   = "";
    var arrayCriteriosseleccionadosTablet  = [];
        arrayCriteriosseleccionadosTablet.push($("#strModeloBusqAvanzada").val());
  
    for(var i=0; i < arrayCriteriosseleccionadosTablet.length ; i++)
    {
        if(arrayCriteriosseleccionadosTablet[i].trim())
        {
            htmlCriteriosSeleccionadosTablet += "<p class='criteriosSession'>"+arrayCriteriosseleccionadosTablet[i].trim()+"</p>"
        }
    }
    
    document.getElementById("criteriosSeleccionadosPer").innerHTML          = htmlCriteriosSeleccionadosPer;
    document.getElementById("criteriosSeleccionadosCuadrilla").innerHTML    = htmlCriteriosSeleccionadosCuadrilla;
    document.getElementById("criteriosSeleccionadosTablet").innerHTML       = htmlCriteriosSeleccionadosTablet;
}

function generarBusquedaAvanzada()
{
    intIndexEstadoMonitoreo = 0;
    if(Ext.getCmp('busqCmbDepartamentoCuadrillaBusqAvanzada'))
    {
        document.getElementById('strRegionPerBusqAvanzada').value               = Ext.getCmp('busqCmbRegionPerBusqAvanzada').value;
        document.getElementById('intIdCantonPerBusqAvanzada').value             = Ext.getCmp('busqCmbCantonPerBusqAvanzada').value;
        document.getElementById('strCantonPerBusqAvanzada').value               = Ext.getCmp('busqCmbCantonPerBusqAvanzada').getRawValue();
        document.getElementById('intIdDepartamentoPerBusqAvanzada').value       = Ext.getCmp('busqCmbDepartamentoPerBusqAvanzada').value;
        document.getElementById('strDepartamentoPerBusqAvanzada').value         = Ext.getCmp('busqCmbDepartamentoPerBusqAvanzada').getRawValue();
        document.getElementById('intIdDepartamentoCuadrillaBusqAvanzada').value = Ext.getCmp('busqCmbDepartamentoCuadrillaBusqAvanzada').value;
        document.getElementById('strDepartamentoCuadrillaBusqAvanzada').value   = Ext.getCmp('busqCmbDepartamentoCuadrillaBusqAvanzada').getRawValue();
        document.getElementById('intIdZonaCuadrillaBusqAvanzada').value         = Ext.getCmp('busqCmbZonaCuadrillaBusqAvanzada').value;
        document.getElementById('strZonaCuadrillaBusqAvanzada').value           = Ext.getCmp('busqCmbZonaCuadrillaBusqAvanzada').getRawValue();
        document.getElementById('intIdModeloBusqAvanzada').value                = Ext.getCmp('busqCmbModeloBusqAvanzada').value;
        document.getElementById('strModeloBusqAvanzada').value                  = Ext.getCmp('busqCmbModeloBusqAvanzada').getRawValue();
        document.getElementById('strFiltrarMisCuadrillasBusqAvanzada').value    = Ext.getCmp('busqCmbFiltrarMisCuadrillasBusqAvanzada').value;
        document.getElementById('strFiltrarPorHorarioBusqAvanzada').value       = Ext.getCmp('busqCmbFiltrarPorHorarioBusqAvanzada').value;
    }
    
    if ( typeof winPanelFilterMonitoreoBusqAvanzada != 'undefined' && winPanelFilterMonitoreoBusqAvanzada != null )
    {
        winPanelFilterMonitoreoBusqAvanzada.destroy();
    }
    
    if ( typeof winPanelReportesMonitoreo != 'undefined' && winPanelReportesMonitoreo != null )
    {
        winPanelReportesMonitoreo.destroy();
    }
    
    limpiarYGenerarCriteriosBusqueda();
    
    tabPanel.setActiveTab(0);
    generarResumenPrincipal();
}

function buscarUbicacionesTablets()
{
    arrayInfoWindow = [];
    arrayMarkerPto  = [];
    openInfoWindow  = null;
    Ext.Ajax.request({
        url: strUrlBuscarTablets,
        method: 'post',
        dataType: 'json',
        timeout: 60000,
        params:
        { 
            strTipoReporte:                         strTipoReporteTotalEnCampo,
            strImeiTablet:                          $('#valueBusqEnCampoIMEI').val(),
            strSerieLogicaTablet:                   $('#valueBusqEnCampoPUBLISH_ID').val(),
            strResponsableTablet:                   $('#valueBusqEnCampoResponsable').val(),
            strRegionPerBusqAvanzada:               $("#strRegionPerBusqAvanzada").val(),
            intIdCantonPerBusqAvanzada:             $("#intIdCantonPerBusqAvanzada").val(),
            intIdDepartamentoPerBusqAvanzada:       $("#intIdDepartamentoPerBusqAvanzada").val(),
            intIdDepartamentoCuadrillaBusqAvanzada: $('#intIdDepartamentoCuadrillaBusqAvanzada').val(),
            intIdZonaCuadrillaBusqAvanzada:         $('#intIdZonaCuadrillaBusqAvanzada').val(),
            intIdModeloBusqAvanzada:                $('#intIdModeloBusqAvanzada').val(),
            strFiltrarMisCuadrillasBusqAvanzada:    $('#strFiltrarMisCuadrillasBusqAvanzada').val(),
            strFiltrarPorHorarioBusqAvanzada:       $('#strFiltrarPorHorarioBusqAvanzada').val()
        },
        success: function(result)
        {
            if(boolBusquedaGlobal)
            {
                buscarTabletsGeneral(intValorInicial, intValorLimite, strTipoReporteMantenimientoLibre);
            }
            
            
            var objData                 = Ext.JSON.decode(result.responseText);
            var arrayTablets            = objData.data;

            arrayIdTablet               = [];
            arrayImeiTablet             = [];
            arrayResponsable            = [];
            arrayNombreDepartamentoPer  = [];
            arrayNombreCuadrilla        = [];
            arrayNombreDepCuadrilla     = [];
            arrayNombreZonaCuadrilla    = [];
            arrayTurnoHoraInicio        = [];
            arrayTurnoHoraFin           = [];
            arrayLatitud                = [];
            arrayLongitud               = [];
            arrayEstado                 = [];
            arrayFechaUltimoIntento     = [];
            arrayFechaUltimoPunto       = [];

            for(var i=0; i < arrayTablets.length ; i++)
            {
               
                var arrayTablet = arrayTablets[i];
                arrayIdTablet.push(arrayTablet['intIdTablet']);
                arrayImeiTablet.push(arrayTablet['strImeiTablet']);
                arrayResponsable.push(arrayTablet['strResponsable']);
                arrayNombreDepartamentoPer.push(arrayTablet['strNombreDepartamentoPer']);
                arrayNombreCuadrilla.push(arrayTablet['strNombreCuadrilla']);
                arrayNombreDepCuadrilla.push(arrayTablet['strNombreDepartamento']);
                arrayNombreZonaCuadrilla.push(arrayTablet['strNombreZona']);
                arrayTurnoHoraInicio.push(arrayTablet['strTurnoHoraInicio']);
                arrayTurnoHoraFin.push(arrayTablet['strTurnoHoraFin']);
                arrayLatitud.push(arrayTablet['strLatitud']);
                arrayLongitud.push(arrayTablet['strLongitud']);
                arrayEstado.push(arrayTablet['strEstadoMonitoreoTablet']);
                arrayFechaUltimoIntento.push(arrayTablet['strFechaUltIntento']);
                arrayFechaUltimoPunto.push(arrayTablet['strFechaUltPunto']);
                
            }
            mostrarMapaUbicacionesTablets();
        },
        failure: function(result)
        {
            Ext.Msg.alert('Error ','Error: ' + result.statusText);
        }
    });
}

function mostrarMapaUbicacionesTablets()
{
    var center = new google.maps.LatLng(-1.766963, -77.973673);

    var myOptions = 
    {
        zoom: 7,
        center: center,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    
    mapa = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    if( arrayLatitud.length > 0)
    {
        for (i = 0; i < arrayLatitud.length; i++) 
        {
            var latlng = new google.maps.LatLng(arrayLatitud[i], arrayLongitud[i]);
            var strHtmlMarkerPto = '<table class="table table-hover" style="margin-bottom: 0px; font-size: 11px;">'+
                                    '<tr>'+
                                        '<td>'+
                                            '<dl class="dl-horizontal" style="margin-bottom: 0px;">'+
                                                '<dt>Responsable</dt>'+
                                                '<dd>'+arrayResponsable[i]+'</dd>'+
                                                '<dt>Departamento</dt>'+
                                                '<dd>'+arrayNombreDepartamentoPer[i]+'</dd>'+
                                                '<dt>Cuadrilla</dt>'+
                                                '<dd>'+arrayNombreCuadrilla[i]+'</dd>';
            if(arrayNombreCuadrilla[i]!="N/A")
            {
                strHtmlMarkerPto += '<dt>Departamento Cuadrilla</dt>'+
                                    '<dd>'+arrayNombreDepCuadrilla[i]+'</dd>'+
                                    '<dt>Zona Cuadrilla</dt>'+
                                    '<dd>'+arrayNombreZonaCuadrilla[i]+'</dd>'+
                                    '<dt>Hora Inicio Cuadrilla</dt>'+
                                    '<dd>'+arrayTurnoHoraInicio[i]+'</dd>'+
                                    '<dt>Hora Fin Cuadrilla</dt>'+
                                    '<dd>'+arrayTurnoHoraFin[i]+'</dd>';
            }
            strHtmlMarkerPto +=             '<dt>Latitud</dt>'+
                                            '<dd>'+arrayLatitud[i]+'</dd>'+
                                            '<dt>Longitud</dt>'+
                                            '<dd>'+arrayLongitud[i]+'</dd>'+
                                            '<dt>Fecha Último Intento</dt>'+
                                            '<dd>'+arrayFechaUltimoIntento[i]+'</dd>'+
                                            '<dt>Fecha Último Punto</dt>'+
                                            '<dd>'+arrayFechaUltimoPunto[i]+'</dd>';
            
            strHtmlMarkerPto +=             '</dl>'+
                                        '</td>'+
                                    '</tr>'+
                                '</table>';
                            
            arrayInfoWindow[arrayIdTablet[i]]   = new google.maps.InfoWindow({
                    content: strHtmlMarkerPto
            });
            
            arrayMarkerPto[arrayIdTablet[i]] = new google.maps.Marker
            ({
                icon: '/public/images/monitoreo-tablets/marker/'+arrayEstado[i].replace(/ /g, "_")+'.png',
                position: latlng, 
                map: mapa,
                title: arrayImeiTablet[i]            
            });
            
            
            google.maps.event.addListener(arrayMarkerPto[arrayIdTablet[i]], 'click', function(innerKey) {
                return function()
                {
                    if(openInfoWindow)
                    {
                        openInfoWindow.close();
                    }
                    openInfoWindow = arrayInfoWindow[innerKey];
                    arrayInfoWindow[innerKey].open(mapa, arrayMarkerPto[innerKey]);
                }
            }(arrayIdTablet[i]));  
        }
    }
}

function ocultarMapaUbicacionesTablets()
{
    $('#divAOcultar').toggle();

    //Primero se oculta o se hace visible el panel del mapa
    if( $('#divAOcultar').is(":visible") )
    {
        document.getElementById("tabsMonitoreo").style.width = "52%";
        $('#tabsMonitoreo').removeClass("col-xs-11").addClass("col-xs-offset-0-5 col-xs-6 ");
        $('#divAOcultar').addClass("col-xs-6 well");
        
        mostrarMapaUbicacionesTablets();
    }
    else
    {
        document.getElementById("tabsMonitoreo").style.width = "93%";
        $('#divAOcultar').removeClass("col-xs-6 well");
        $('#tabsMonitoreo').removeClass("col-xs-offset-0-5 col-xs-6").addClass("col-xs-11");
        $('#tableTabletsEnCampo').DataTable().columns.adjust();
        $('#tableTabletsMantenimientoLibre').DataTable().columns.adjust();
        
    }
    tabPanel.doLayout();
}

Ext.onReady(function()
{
    generarResumenPrincipal();
    tabPanel = new Ext.TabPanel(
    {
        minHeight: 600,
        renderTo: 'tabsMonitoreo',
        activeTab: 0,
        plain: true,
        autoRender: true,
        autoShow: true,
        items: [
            {
                contentEl: 'tab1', title: 'En Campo', 
                listeners:
                {
                    activate: function(tab)
                    {
                        $('#tableTabletsEnCampo').DataTable().columns.adjust();
                    }

                }
            },
            {
                contentEl: 'tab2', title: 'Mantenimiento',
                listeners:
                {
                    activate: function(tab)
                    {
                        $('#tableTabletsMantenimientoLibre').DataTable().columns.adjust();
                    }

                }
            },
            {
                contentEl: 'tab3', title: 'Libres',
                listeners:
                {
                    activate: function(tab)
                    {
                        $('#tableTabletsCuadrillasLibres').DataTable().columns.adjust();
                    }

                }
            },
            {
                contentEl: 'tab4', title: 'Con Ubicación Actualizada',
                listeners:
                {
                    activate: function(tab)
                    {
                        $('#tableTabletsCON_UBICACION_ACTUALIZADA').DataTable().columns.adjust();
                    }

                }
            },
            {
                contentEl: 'tab5', title: 'Con Ubicación Desactualizada',
                listeners:
                {
                    activate: function(tab)
                    {
                        $('#tableTabletsCON_UBICACION_DESACTUALIZADA').DataTable().columns.adjust();
                    }

                }
            },
            {
                contentEl: 'tab6', title: 'Sin Ubicación',
                listeners:
                {
                    activate: function(tab)
                    {
                        $('#tableTabletsSIN_UBICACION').DataTable().columns.adjust();
                    }

                }
            },
            {
                contentEl: 'tab7', title: 'Sin Información',
                listeners:
                {
                    activate: function(tab)
                    {
                        $('#tableTabletsSIN_INFORMACION').DataTable().columns.adjust();
                    }

                }
            }
        ]
    });

    storeCantonesPerBusqAvanzada = new Ext.data.Store
    ({
        total: 'total',
        pageSize: 200,
        proxy:
        {
            type: 'ajax',
            method: 'post',
            url: strUrlGetCantonesBusqAvanzada,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
        [
            {name: 'intIdCanton',     mapping: 'id_canton'},
            {name: 'strNombreCanton', mapping: 'nombre_canton'}
        ],
        listeners: 
        {
            load: function(store, records)
            {
                 store.insert(0, [{ strNombre: 'Todos', strValue: '' } ]);
            }      
        }
    });
    
    storeDepartamentosPerBusqAvanzada = new Ext.data.Store
    ({
        total: 'total',
        pageSize: 200,
        proxy:
        {
            type: 'ajax',
            method: 'post',
            url: strUrlGetDepartamentosBusqAvanzada,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
        [
            {name: 'intIdDepartamento',     mapping: 'id_departamento'},
            {name: 'strNombreDepartamento', mapping: 'nombre_departamento'}
        ],
        listeners: 
        {
            load: function(store, records)
            {
                 store.insert(0, [{ strNombre: 'Todos', strValue: '' } ]);
            }      
        }
    });
    
    storeDepartamentosCuadrillaBusqAvanzada = new Ext.data.Store
    ({
        total: 'total',
        pageSize: 200,
        proxy:
        {
            type: 'ajax',
            method: 'post',
            url: strUrlGetDepartamentosBusqAvanzada,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
        [
            {name: 'intIdDepartamento',     mapping: 'id_departamento'},
            {name: 'strNombreDepartamento', mapping: 'nombre_departamento'}
        ],
        listeners: 
        {
            load: function(store, records)
            {
                 store.insert(0, [{ strNombre: 'Todos', strValue: '' } ]);
            }      
        }
    });
    
    storeZonasCuadrillaBusqAvanzada = new Ext.data.Store
    ({
        total: 'total',
        pageSize: 200,
        proxy:
        {
            type: 'ajax',
            method: 'post',
            url: strUrlGetZonasBusqAvanzada,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
        [
            {name: 'intIdZona',     mapping: 'strValue'},
            {name: 'strNombreZona', mapping: 'strNombre'}
        ],
        listeners: 
        {
            load: function(store, records)
            {
                 store.insert(0, [{ strNombre: 'Todos', strValue: '' } ]);
            }      
        }
    });
    
    storeModelosTabletsBusqAvanzada = new Ext.data.Store
    ({
        total: 'total',
        proxy: 
        {
            type: 'ajax',
            url: strUrlGetModelosBusqAvanzada,
            timeout: 60000,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams:
            {
                strTipoElemento : 'TABLET',
                strEstadoModelo : 'Activo'
            }
        },
        fields:
        [
            {name: 'intIdModeloElemento',     mapping: 'id'},
            {name: 'strNombreModeloElemento', mapping: 'descripcion'}
        ]
    });
    
});

function buscarTabletsGeneral(inicio, limite,strTipoReporte)
{
    var strTipoReporteBusq      = "";
    var intInicioBusq           = "";
    var intLimiteBusq           = "";
    
    if(strTipoReporte == strTipoReporteEnCampo)
    {
        strTipoReporteBusq = strTipoReporteEnCampo;
        intInicioEnCampo    = inicio;
        intLimiteEnCampo    = limite;
        intInicioBusq       = intInicioEnCampo;
        intLimiteBusq       = intLimiteEnCampo;

    }
    else if(strTipoReporte == strTipoReporteMantenimientoLibre)
    {
        strTipoReporteBusq          = strTipoReporteMantenimientoLibre;
        intInicioMantenimientoLibre = inicio;
        intLimiteMantenimientoLibre = limite;
        intInicioBusq               = intInicioMantenimientoLibre;
        intLimiteBusq               = intLimiteMantenimientoLibre;
    }
    else if(strTipoReporte == strTipoReporteCuadrillasLibres)
    {
        strTipoReporteBusq          = strTipoReporteCuadrillasLibres;
        intInicioCuadrillasLibres   = inicio;
        intLimiteCuadrillasLibres   = limite;
        intInicioBusq               = intInicioCuadrillasLibres;
        intLimiteBusq               = intLimiteCuadrillasLibres;
    }
    iniciarDatatableTabletsGeneral(intInicioBusq, intLimiteBusq, strTipoReporteBusq);
}


function buscarTabletsGeneralMonitoreadas(inicio, limite,strTipoReporte)
{
    var strTipoReporteBusq      = "";
    var intInicioBusq           = "";
    var intLimiteBusq           = "";
        
    if(strTipoReporte == strTipoReporteConUbicacionActualizada)
    {
        strTipoReporteBusq                  = strTipoReporteConUbicacionActualizada;
        intInicioConUbicacionActualizada    = inicio;
        intLimiteConUbicacionActualizada    = limite;
        intInicioBusq                       = intInicioConUbicacionActualizada;
        intLimiteBusq                       = intLimiteConUbicacionActualizada;
    }
    else if(strTipoReporte == strTipoReporteConUbicacionDesactualizada)
    {
        strTipoReporteBusq                  = strTipoReporteConUbicacionDesactualizada;
        intInicioConUbicacionDesactualizada = inicio;
        intLimiteConUbicacionDesactualizada = limite;
        intInicioBusq                       = intInicioConUbicacionDesactualizada;
        intLimiteBusq                       = intLimiteConUbicacionDesactualizada;
    }
    else if(strTipoReporte == strTipoReporteSinUbicacion)
    {
        strTipoReporteBusq                  = strTipoReporteSinUbicacion;
        intInicioSinUbicacion               = inicio;
        intLimiteSinUbicacion               = limite;
        intInicioBusq                       = intInicioSinUbicacion;
        intLimiteBusq                       = intLimiteSinUbicacion;
    }
    else if(strTipoReporte == strTipoReporteSinInformacion)
    {
        strTipoReporteBusq                  = strTipoReporteSinInformacion;
        intInicioSinInformacion             = inicio;
        intLimiteSinInformacion             = limite;
        intInicioBusq                       = intInicioSinInformacion;
        intLimiteBusq                       = intLimiteSinInformacion;
    }
    
    iniciarDatatableTabletsGeneralMonitoreadas(intInicioBusq, intLimiteBusq, strTipoReporteBusq);
}


function iniciarDatatableTabletsGeneral(inicio, limite, strTipoReporte)
{
    if(strTipoReporte == strTipoReporteEnCampo)
    {
        $('#tableTabletsEnCampo thead th div').each( function (i) 
        {
            var title = $('#tableTabletsEnCampo thead th').eq(i).text();
            $(this).html( '<input id="valueBusqEnCampo'+title+'" type="text" placeholder="'+title+'" data-index="'+i+'" />' );

        } );

        tableTabletsEnCampo = $("#tableTabletsEnCampo").DataTable
        ({
            oLanguage: {
                "sProcessing": "Procesando...",
                "sEmptyTable": "No hay datos disponibles para su búsqueda",
                "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando 0 a 0 de 0 registros",
                "oPaginate": {
                    "sPrevious": "Anterior",
                    "sNext": "Siguiente"
                }
            },
            "processing": true,
            "bLengthChange": false,
            "serverSide": true,
            "iDisplayLength": limite,
            "iDisplayStart": inicio,
            "bFilter": false,
            "bSort": false,
            "scrollX": 400,
            "scrollY": 400,
            "destroy": true,
            "dom": '<"top"iflp<"clear">>rt<"bottom"flp<"clear">>',
            "fnDrawCallback": function( oSettings )
            {
                intLimiteEnCampo = oSettings._iDisplayLength;
                intInicioEnCampo = oSettings._iDisplayStart;
                
                if(boolLoadFirstTime)
                {
                    tableTabletsEnCampo.columns().every( function () 
                    {
                        $( 'input', this.header() ).on( 'keydown', function (ev) 
                        {
                            // La búsqueda sólo se realizará al presiona enter
                            if (ev.keyCode == 13) 
                            {
                                buscarTabletsGeneral(intValorInicial, intValorLimite, strTipoReporteEnCampo);
                            }
                        });
                    });
                }
                
                if(boolBusquedaGlobal)
                {
                    buscarUbicacionesTablets();
                }
            },
            "ajax": 
            {
                "method": "post",
                "url": strUrlBuscarTablets,
                "dataType": "json",
                'beforeSend': function (jqXHR) {
                    arrayXhrPool.push(jqXHR);

                },
                'complete': function (jqXHR) {
                    var indexXhrPool = arrayXhrPool.indexOf(jqXHR);   //obtener el indice de la reciente conexión completada
                    if (indexXhrPool > -1)
                    {
                        arrayXhrPool.splice(indexXhrPool, 1); //eliminar de la lista por el índice
                    }
                },
                "data": function ( d ) {
                    d.strTipoReporte                            = strTipoReporte;
                    d.strImeiTablet                             = $('#valueBusqEnCampoIMEI').val();
                    d.strSerieLogicaTablet                      = $('#valueBusqEnCampoPUBLISH_ID').val();
                    d.strResponsableTablet                      = $('#valueBusqEnCampoResponsable').val();
                    d.strRegionPerBusqAvanzada                  = $("#strRegionPerBusqAvanzada").val();
                    d.intIdCantonPerBusqAvanzada                = $("#intIdCantonPerBusqAvanzada").val();
                    d.intIdDepartamentoPerBusqAvanzada          = $("#intIdDepartamentoPerBusqAvanzada").val();
                    d.intIdDepartamentoCuadrillaBusqAvanzada    = $('#intIdDepartamentoCuadrillaBusqAvanzada').val();
                    d.intIdZonaCuadrillaBusqAvanzada            = $('#intIdZonaCuadrillaBusqAvanzada').val();
                    d.intIdModeloBusqAvanzada                   = $('#intIdModeloBusqAvanzada').val();
                    d.strEstadoMonitoreoBusqAvanzada            = $('#strEstadoMonitoreoBusqAvanzada').val();
                    d.strFiltrarMisCuadrillasBusqAvanzada       = $('#strFiltrarMisCuadrillasBusqAvanzada').val();
                    d.strFiltrarPorHorarioBusqAvanzada          = $('#strFiltrarPorHorarioBusqAvanzada').val();
                }
            },
            "createdRow": function ( row, data, index ) 
            {
                var htmlLinkMarcadorMapa = "<a onclick='acercarMarcadorMapa("+data.intIdTablet+","+data.strLatitud+","+data.strLongitud+");'"
                                            +" style='cursor:pointer;'>"+data.strImeiTablet+"</a>";
                $('td', row).eq(1).html(htmlLinkMarcadorMapa);

                if(data.strSerieLogicaTablet != "N/A" && data.strSerieLogicaTablet != null)
                {
                    var htmlLinkMarcadorSerieLogicaMapa = "<a onclick='acercarMarcadorMapa("+data.intIdTablet+","+data.strLatitud+","+data.strLongitud+");'"
                                            +" style='cursor:pointer;'>"+data.strSerieLogicaTablet+"</a>";
                    $('td', row).eq(2).html(htmlLinkMarcadorSerieLogicaMapa);
                }
            },
            columns: 
            [
                { data: 'strResponsable'},
                { data: 'strImeiTablet'},
                { data: 'strSerieLogicaTablet'},
                { data: 'strNombreDepartamentoPer'},
                { data: 'strFechaUltIntento'},
                { data: 'strFechaUltPunto'}
            ]
        });
        
        $('#tableTabletsEnCampo').DataTable().columns.adjust();
    }
    else if(strTipoReporte == strTipoReporteMantenimientoLibre)
    {
        $('#tableTabletsMantenimientoLibre thead th div').each( function (i) {
            var title = $('#tableTabletsMantenimientoLibre thead th').eq(i).text();
            $(this).html( '<input id="valueBusqMantenimientoLibre'+title+'" type="text" placeholder="'+title+'" data-index="'+i+'" />' );
        } );
        
        

        tableTabletsMantenimientoLibre = $("#tableTabletsMantenimientoLibre").DataTable
        ({
            oLanguage: {
                "sProcessing": "Procesando...",
                "sEmptyTable": "No hay datos disponibles para su búsqueda",
                "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando 0 a 0 de 0 registros",
                "oPaginate": {
                    "sPrevious": "Anterior",
                    "sNext": "Siguiente"
                }
            },
            "processing": true,
            "bLengthChange": false,
            "serverSide": true,
            "iDisplayLength": limite,
            "iDisplayStart": inicio,
            "bFilter": false,
            "scrollX": 400,
            "scrollY": 400,
            "destroy": true,
            "dom": '<"top"iflp<"clear">>rt<"bottom"flp<"clear">>',
            "bSort": false,
            "fnDrawCallback": function( oSettings )
            {
                intLimiteMantenimientoLibre = oSettings._iDisplayLength;
                intInicioMantenimientoLibre = oSettings._iDisplayStart;
                
                if(boolLoadFirstTime)
                {
                    tableTabletsMantenimientoLibre.columns().every( function () 
                    {
                        $( 'input', this.header() ).on( 'keydown', function (ev) 
                        {
                            // La búsqueda sólo se realizará al presiona enter
                            if (ev.keyCode == 13) 
                            { 
                                buscarTabletsGeneral(intValorInicial, intValorLimite, strTipoReporteMantenimientoLibre);
                            }
                        });
                    });
                }
                if(boolBusquedaGlobal)
                {
                    buscarTabletsGeneral(intValorInicial, intValorLimite, strTipoReporteCuadrillasLibres);
                }
            },
            "ajax": 
            {
                "method": "post",
                "url": strUrlBuscarTablets,
                "dataType": "json",
                'beforeSend': function (jqXHR) {
                    arrayXhrPool.push(jqXHR);

                },
                'complete': function (jqXHR) {
                    var indexXhrPool = arrayXhrPool.indexOf(jqXHR);   //obtener el indice de la reciente conexión completada
                    if (indexXhrPool > -1)
                    {
                        arrayXhrPool.splice(indexXhrPool, 1); //eliminar de la lista por el índice
                    }
                },
                "data": function ( d ) {
                    d.strTipoReporte                            = strTipoReporte;
                    d.strImeiTablet                             = $('#valueBusqMantenimientoLibreIMEI').val();
                    d.strSerieLogicaTablet                      = $('#valueBusqMantenimientoLibrePUBLISH_ID').val();
                    d.strResponsableTablet                      = $('#valueBusqMantenimientoLibreResponsable').val();
                    d.strRegionPerBusqAvanzada                  = $("#strRegionPerBusqAvanzada").val();
                    d.intIdCantonPerBusqAvanzada                = $("#intIdCantonPerBusqAvanzada").val();
                    d.intIdDepartamentoPerBusqAvanzada          = $("#intIdDepartamentoPerBusqAvanzada").val();
                    d.intIdDepartamentoCuadrillaBusqAvanzada    = $('#intIdDepartamentoCuadrillaBusqAvanzada').val();
                    d.intIdZonaCuadrillaBusqAvanzada            = $('#intIdZonaCuadrillaBusqAvanzada').val();
                    d.intIdModeloBusqAvanzada                   = $('#intIdModeloBusqAvanzada').val();
                    d.strEstadoMonitoreoBusqAvanzada            = $('#strEstadoMonitoreoBusqAvanzada').val();
                    d.strFiltrarMisCuadrillasBusqAvanzada       = $('#strFiltrarMisCuadrillasBusqAvanzada').val();
                    d.strFiltrarPorHorarioBusqAvanzada          = $('#strFiltrarPorHorarioBusqAvanzada').val();
                }
            },
            columns: 
            [
                { data: 'strResponsable'},
                { data: 'strImeiTablet' },
                { data: 'strSerieLogicaTablet'},
                { data: 'strNombreDepartamentoPer'}
            ]
        });
        
        $('#tableTabletsMantenimientoLibre').DataTable().columns.adjust();
    }
    else if(strTipoReporte == strTipoReporteCuadrillasLibres)
    {
        $('#tableTabletsCuadrillasLibres thead th div').each( function (i) {
            var title = $('#tableTabletsCuadrillasLibres thead th').eq(i).text();
            $(this).html( '<input id="valueBusqCuadrillasLibres'+title+'" type="text" placeholder="'+title+'" data-index="'+i+'" />' );
        } );
        
        

        tableTabletsCuadrillasLibres = $("#tableTabletsCuadrillasLibres").DataTable
        ({
            oLanguage: {
                "sProcessing": "Procesando...",
                "sEmptyTable": "No hay datos disponibles para su búsqueda",
                "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando 0 a 0 de 0 registros",
                "oPaginate": {
                    "sPrevious": "Anterior",
                    "sNext": "Siguiente"
                }
            },
            "processing": true,
            "bLengthChange": false,
            "serverSide": true,
            "iDisplayLength": limite,
            "iDisplayStart": inicio,
            "bFilter": false,
            "scrollX": 400,
            "scrollY": 400,
            "destroy": true,
            "dom": '<"top"iflp<"clear">>rt<"bottom"flp<"clear">>',
            "bSort": false,
            "fnDrawCallback": function( oSettings )
            {
                intLimiteCuadrillasLibres = oSettings._iDisplayLength;
                intInicioCuadrillasLibres = oSettings._iDisplayStart;
                
                if(boolLoadFirstTime)
                {
                    tableTabletsCuadrillasLibres.columns().every( function () 
                    {
                        $( 'input', this.header() ).on( 'keydown', function (ev) 
                        {
                            // La búsqueda sólo se realizará al presiona enter
                            if (ev.keyCode == 13) 
                            { 
                                buscarTabletsGeneral(intValorInicial, intValorLimite, strTipoReporteCuadrillasLibres);
                            }
                        });
                    });
                }
                if(boolBusquedaGlobal)
                {
                    intIndexEstadoMonitoreo = 0;
                    buscarTabletsGeneralMonitoreadas(intValorInicial, intValorLimite, arrayEstadosMonitoreadas[intIndexEstadoMonitoreo]);
                }
            },
            "ajax": 
            {
                "method": "post",
                "url": strUrlBuscarTablets,
                "dataType": "json",
                'beforeSend': function (jqXHR) {
                    arrayXhrPool.push(jqXHR);

                },
                'complete': function (jqXHR) {
                    var indexXhrPool = arrayXhrPool.indexOf(jqXHR);   //obtener el indice de la reciente conexión completada
                    if (indexXhrPool > -1)
                    {
                        arrayXhrPool.splice(indexXhrPool, 1); //eliminar de la lista por el índice
                    }
                },
                "data": function ( d ) {
                    d.strTipoReporte                            = strTipoReporte;
                    d.strImeiTablet                             = $('#valueBusqCuadrillasLibresIMEI').val();
                    d.strSerieLogicaTablet                      = $('#valueBusqCuadrillasLibresPUBLISH_ID').val();
                    d.strResponsableTablet                      = $('#valueBusqCuadrillasLibresResponsable').val();
                    d.strRegionPerBusqAvanzada                  = $("#strRegionPerBusqAvanzada").val();
                    d.intIdCantonPerBusqAvanzada                = $("#intIdCantonPerBusqAvanzada").val();
                    d.intIdDepartamentoPerBusqAvanzada          = $("#intIdDepartamentoPerBusqAvanzada").val();
                    d.intIdDepartamentoCuadrillaBusqAvanzada    = $('#intIdDepartamentoCuadrillaBusqAvanzada').val();
                    d.intIdZonaCuadrillaBusqAvanzada            = $('#intIdZonaCuadrillaBusqAvanzada').val();
                    d.intIdModeloBusqAvanzada                   = $('#intIdModeloBusqAvanzada').val();
                    d.strEstadoMonitoreoBusqAvanzada            = $('#strEstadoMonitoreoBusqAvanzada').val();
                    d.strFiltrarMisCuadrillasBusqAvanzada       = $('#strFiltrarMisCuadrillasBusqAvanzada').val();
                    d.strFiltrarPorHorarioBusqAvanzada          = $('#strFiltrarPorHorarioBusqAvanzada').val();
                }
            },
            columns: 
            [
                { data: 'strResponsable'},
                { data: 'strImeiTablet' },
                { data: 'strSerieLogicaTablet'},
                { data: 'strNombreDepartamentoPer'}
            ]
        });
        
        $('#tableTabletsCuadrillasLibres').DataTable().columns.adjust();
    }
}

setInterval(function(){ refrescarReportes(); }, 300000);

function refrescarReportes()
{
    boolBusquedaGlobal = true;
    
    if ( typeof winPanelFilterMonitoreoBusqAvanzada != 'undefined' && winPanelFilterMonitoreoBusqAvanzada != null )
    {
        winPanelFilterMonitoreoBusqAvanzada.destroy();
    }
    
    if ( typeof winPanelReportesMonitoreo != 'undefined' && winPanelReportesMonitoreo != null )
    {
        winPanelReportesMonitoreo.destroy();
    }
    
    limpiarYGenerarCriteriosBusqueda();
    
    tabPanel.setActiveTab(0);
    generarResumenPrincipal();
}

function cargarTiposReportes()
{
    generarReporteGeneral();
    
    var formPanelReportesMonitoreo = Ext.create('Ext.form.Panel', {
        bodyPadding: 5,
        waitMsgTarget: true,
        id: 'formPanelResumenNacional',
        fieldDefaults: {
           labelAlign: 'left',
           msgTarget: 'side'
        },
        defaults: {
           margins: '0 0 10 0'
        },
        items: [
           {
               xtype: 'fieldset',
               title: '',
               defaultType: 'textfield',
               width: '100%',
               items:
               [
                   {
                       xtype: 'fieldset',
                       title: '<b>RESUMEN NACIONAL</b>',                       
                       width: '100%',
                       anchor:'100%',
                       items: 
                       [
                           {
                               id: 'divResumen',
                               name: 'divResumen',
                               height: 135,
                               html:    "<form id='formExportarResumenDetallado' name='formExportarResumenDetallado' "
                                            +"action='"+strUrlExportarResumenesMonitoreo+"' method='post'>"
                                            +"<input type='hidden' id='strTipoExportarResumenDetallado' name='strTipoExportarResumenDetallado' "
                                            +"value='' />"
                                            +"<input type='hidden' id='strRegionExportarResumenDetallado' name='strRegionExportarResumenDetallado' "
                                            +"value='' />"
                                        +"</form>"
                                        +"<div class='row'>"
                                            +"<div class='col-xs-12'>"
                                            +"<div style='text-align:center;' class='col-xs-6' id='contTabletsTotal'></div>"
                                                +"<div style='text-align:center;' class='col-xs-6' id='contTabletsActualizadas'></div>"

                                            +"</div>"
                                            +"<div class='col-xs-12'>"
                                                +"<div style='text-align:center;' class='col-xs-6' id='contTabletsEnCampo'></div>"
                                                +"<div style='text-align:center;' class='col-xs-6' id='contTabletsDesactualizadas'></div>"
                                            +"</div>"
                                            +"<div class='col-xs-12'>"
                                                +"<div style='text-align:center;' class='col-xs-6' id='contTabletsMantLibre'></div>"
                                                +"<div style='text-align:center;' class='col-xs-6' id='contTabletsProblemasConGPS'></div>"
                                            +"</div>"
                                            +"<div class='col-xs-12'>"
                                                +"<div style='text-align:center;' class='col-xs-6' id='contTabletsCuadrillasLibres'></div>"
                                                +"<div style='text-align:center;' class='col-xs-6' id='contTabletsNoMonitoreadas'></div>"
                                            +"</div>"
                                        +"</div>"
                                        +"<div id='divExportarResumenNacional' style='display:none;text-align: center;margin-top: 10px;' class='row'>"
                                            +"<input style='margin-top: 5px;' type='button' class='button-crud' "
                                            +"value='Exportar Detallado' onclick='exportarResumenDetallado(\"RESUMEN_NACIONAL\",\"\");' />"
                                        +"</div>"
                            }
                            
                        ]
                   },
                   {
                       xtype: 'fieldset',
                       title: '<b>RESUMEN REGIONAL</b>',                       
                       width: '100%',
                       anchor:'100%',
                       items: 
                       [
                            {
                                layout:
                                {
                                    type:'table',
                                    columns: 2,
                                    tableAttrs: 
                                    {
                                        style: 
                                        {
                                            width: '100%'
                                        }
                                    }
                                },
                                border: false,
                                items: 
                                [
                                    {
                                        xtype: 'fieldset',
                                        title: '<b>R1</b>',
                                        border: false,
                                        labelWidth:50,
                                        items: 
                                        [
                                            {
                                               id: 'divResumenRegionalR1',
                                               name: 'divResumenRegionalR1',
                                               height: 150,
                                               html: "<div class='row'>"
                                                        +"<div class='col-xs-12'>"
                                                           +"<div style='text-align:center;' class='col-xs-6' id='contTabletsTotalR1'></div>"
                                                           +"<div style='text-align:center;' class='divsResumenFilaDatos col-xs-6' "
                                                           +"id='contTabletsActualizadasR1'></div>"
                                                        +"</div>"
                                                        +"<div class='col-xs-12'>"
                                                           +"<div style='text-align:center;' class='col-xs-6' id='contTabletsEnCampoR1'></div>"
                                                           +"<div style='text-align:center;' class='divsResumenFilaDatos col-xs-6' "
                                                           +"id='contTabletsDesactualizadasR1'></div>"
                                                        +"</div>"
                                                        +"<div class='col-xs-12'>"
                                                           +"<div style='text-align:center;' class='col-xs-6' id='contTabletsMantLibreR1'></div>"
                                                           +"<div style='text-align:center;' class='divsResumenFilaDatos col-xs-6' "
                                                           +"id='contTabletsProblemasConGPSR1'></div>"
                                                        +"</div>"
                                                        +"<div class='col-xs-12'>"
                                                            +"<div style='text-align:center;' class='col-xs-6' id='contTabletsCuadrillasLibresR1'>"
                                                            +"</div>"
                                                            +"<div style='text-align:center;' class='divsResumenFilaDatos col-xs-6' "
                                                            +"id='contTabletsNoMonitoreadasR1'></div>"
                                                        +"</div>"
                                                    +"</div>"
                                                    +"<div id='divExportarResumenRegionalR1' style='display:none;text-align: center;margin-top: 5px;'"
                                                        +" class='row'>"
                                                        +"<input style='margin-top: 25px;' type='button' "
                                                        +"class='button-crud' value='Exportar Detallado' "
                                                        +"onclick='exportarResumenDetallado(\"RESUMEN_REGIONAL\",\"R1\");' />"
                                                    +"</div>"
                                            }
                                        ]
                                    },

                                    {
                                        xtype: 'fieldset',
                                        title: '<b>R2</b>',
                                        border: false,
                                        items: 
                                        [
                                            {
                                               id: 'divResumenRegionalR2',
                                               name: 'divResumenRegionalR2',
                                               height: 150,
                                               html: "<div class='row'>"
                                                        +"<div class='col-xs-12'>"
                                                           +"<div style='text-align:center;' class='col-xs-6' id='contTabletsTotalR2'></div>"
                                                           +"<div style='text-align:center;' class='divsResumenFilaDatos col-xs-6' "
                                                           +"id='contTabletsActualizadasR2'></div>"
                                                        +"</div>"
                                                        +"<div class='col-xs-12'>"
                                                           +"<div style='text-align:center;' class='col-xs-6' id='contTabletsEnCampoR2'></div>"
                                                           +"<div style='text-align:center;' class='divsResumenFilaDatos col-xs-6' "
                                                           +"id='contTabletsDesactualizadasR2'></div>"
                                                        +"</div>"
                                                        +"<div class='col-xs-12'>"
                                                           +"<div style='text-align:center;' class='col-xs-6' id='contTabletsMantLibreR2'></div>"
                                                           +"<div style='text-align:center;' class='divsResumenFilaDatos col-xs-6' "
                                                           +"id='contTabletsProblemasConGPSR2'></div>"
                                                        +"</div>"
                                                        +"<div class='col-xs-12'>"
                                                           +"<div style='text-align:center;' class='col-xs-6' id='contTabletsCuadrillasLibresR2'>"
                                                           +"</div>"
                                                           +"<div style='text-align:center;' class='divsResumenFilaDatos col-xs-6' "
                                                           +"id='contTabletsNoMonitoreadasR2'></div>"
                                                        +"</div>"
                                                    +"</div>"
                                                    +"<div id='divExportarResumenRegionalR2' style='display:none;text-align: center;margin-top: 5px;'"
                                                        +" class='row'>"
                                                        +"<input style='margin-top: 25px;' type='button' "
                                                        +"class='button-crud' value='Exportar Detallado' "
                                                        +"onclick='exportarResumenDetallado(\"RESUMEN_REGIONAL\",\"R2\");' />"
                                                    +"</div>"
                                            }
                                        ]
                                    }
                                ]
                            }
                        ]
                    }

                ]
            }

        ]
    });
    
    winPanelReportesMonitoreo = Ext.create('Ext.window.Window',
    {
      title: 'RESUMEN MONITOREO',
      modal: true,
      width: '75%',
      closable: true,
      layout: 'fit',
      floating: true,
      shadow: true,
      shadowOffset:20,
      resizable:true,
      items: [formPanelReportesMonitoreo]
    }).show();
    
}


function generarResumenPrincipal()
{
    connEsperaAccion.request
    ({
        url: strUrlGenerarReporteGeneral,
        method: 'post',
        dataType: 'json',
        timeout: 60000,
        params:
        { 
            strTipoReporteMonitoreo:                'RESUMEN_GENERAL',
            strRegionPerBusqAvanzada:               $("#strRegionPerBusqAvanzada").val(),
            intIdCantonPerBusqAvanzada:             $("#intIdCantonPerBusqAvanzada").val(),
            intIdDepartamentoPerBusqAvanzada:       $("#intIdDepartamentoPerBusqAvanzada").val(),
            intIdDepartamentoCuadrillaBusqAvanzada: $('#intIdDepartamentoCuadrillaBusqAvanzada').val(),
            intIdZonaCuadrillaBusqAvanzada:         $('#intIdZonaCuadrillaBusqAvanzada').val(),
            strFiltrarMisCuadrillasBusqAvanzada:    $('#strFiltrarMisCuadrillasBusqAvanzada').val(),
            strFiltrarPorHorarioBusqAvanzada:       $('#strFiltrarPorHorarioBusqAvanzada').val(),
            intIdModeloBusqAvanzada:                $('#intIdModeloBusqAvanzada').val()
        },
        success: function(result)
        {
            var objData                 = Ext.JSON.decode(result.responseText);
            var arrayResumenGeneral     = objData.encontrados;
            
            if(arrayResumenGeneral.length > 0)
            {
                document.getElementById("intNumTotalResumenPrincipal").innerHTML            = arrayResumenGeneral[0]['intNumTabletsTotal'];
                document.getElementById("intNumEnCampoResumenPrincipal").innerHTML          = arrayResumenGeneral[0]['intNumTabletsEnCampo'];
                document.getElementById("intNumActualizadasResumenPrincipal").innerHTML     = arrayResumenGeneral[0]['intNumTabletsActualizadas'];
                document.getElementById("intNumDesactualizadasResumenPrincipal").innerHTML  = arrayResumenGeneral[0]['intNumTabletsDesactualizadas'];
                document.getElementById("intNumProblemaGPSResumenPrincipal").innerHTML      = arrayResumenGeneral[0]['intNumTabletsProblGPS'];
                document.getElementById("intNumNoMonitoreadasResumenPrincipal").innerHTML   = arrayResumenGeneral[0]['intNumTabletsNoMonitoreadas'];
                document.getElementById("intNumMantLibreResumenPrincipal").innerHTML        = arrayResumenGeneral[0]['intNumTabletsMantLibre'];
                document.getElementById("intNumCuadrillasLibresResumenPrincipal").innerHTML = arrayResumenGeneral[0]['intNumTabletsCuadrillasLibres'];
            }
            else
            {
                document.getElementById("intNumTotalResumenPrincipal").innerHTML            = 0;
                document.getElementById("intNumEnCampoResumenPrincipal").innerHTML          = 0;
                document.getElementById("intNumActualizadasResumenPrincipal").innerHTML     = 0;
                document.getElementById("intNumDesactualizadasResumenPrincipal").innerHTML  = 0;
                document.getElementById("intNumProblemaGPSResumenPrincipal").innerHTML      = 0;
                document.getElementById("intNumNoMonitoreadasResumenPrincipal").innerHTML   = 0;
                document.getElementById("intNumMantLibreResumenPrincipal").innerHTML        = 0;
                document.getElementById("intNumCuadrillasLibresResumenPrincipal").innerHTML = 0;
            }
            
            buscarTabletsGeneral(intValorInicial, intValorLimite, strTipoReporteEnCampo);

        },
        failure: function(result)
        {
            boolLoadFirstTime  = false;
            boolBusquedaGlobal = false;
            Ext.Msg.alert('Error ','Error: ' + result.statusText);
        }
    });
}

function generarReporteGeneral()
{
    Ext.Ajax.request
    ({
        url: strUrlGenerarReporteGeneral,
        method: 'post',
        params:
        { 
            strTipoReporteMonitoreo:    'RESUMEN_GENERAL',
        },
        success: function(result)
        {   
            var objData                 = Ext.JSON.decode(result.responseText);
            var arrayResumenNacional    = objData.encontrados;
            if(arrayResumenNacional.length > 0)
            {
                document.getElementById("contTabletsTotal").innerHTML               = "<b>TOTAL: "
                                                                                      +arrayResumenNacional[0]['intNumTabletsTotal']+"</b>";
                document.getElementById("contTabletsMantLibre").innerHTML           = "<b>MANTENIMIENTO: "
                                                                                      +arrayResumenNacional[0]['intNumTabletsMantLibre']+"</b>";
                                                                              
                document.getElementById("contTabletsEnCampo").innerHTML             = "<b>EN CAMPO: "
                                                                                      +arrayResumenNacional[0]['intNumTabletsEnCampo']+"</b>";
                                                                              
                document.getElementById("contTabletsCuadrillasLibres").innerHTML    = "<b>LIBRES: "
                                                                                      +arrayResumenNacional[0]['intNumTabletsCuadrillasLibres']
                                                                                      +"</b>";
                
                document.getElementById("contTabletsActualizadas").innerHTML    = "<p style='font-size:10px;margin:0px;color:#4cae4c'>"
                                                                                  +"<span style='padding-right:3px; padding-top: 3px;"
                                                                                  +"display:inline-block;'>"
                                                                                  +"<img class='imgResumenPrincipal' "
                                                                                  +"src='/public/images/monitoreo-tablets/marker/"
                                                                                  +"CON_UBICACION_ACTUALIZADA.png'/></span>"
                                                                                  +"CON UBICACIÓN ACTUALIZADA: "
                                                                                  +arrayResumenNacional[0]['intNumTabletsActualizadas']+"</p>";
                document.getElementById("contTabletsDesactualizadas").innerHTML = "<p style='font-size:10px;margin:0px;color:#46b8da'>"
                                                                                  +"<span style='padding-right:3px; padding-top: 3px;"
                                                                                  +"display:inline-block;'>"
                                                                                  +"<img class='imgResumenPrincipal' "
                                                                                  +"src='/public/images/monitoreo-tablets/marker/"
                                                                                  +"CON_UBICACION_DESACTUALIZADA.png'/></span>"
                                                                                  +"CON UBICACIÓN DESACTUALIZADA: "
                                                                                  +arrayResumenNacional[0]['intNumTabletsDesactualizadas']+"</p>";
                document.getElementById("contTabletsProblemasConGPS").innerHTML = "<p style='font-size:10px;margin:0px;color:#eea236'>"
                                                                                  +"<span style='padding-right:3px; padding-top: 3px;"
                                                                                  +"display:inline-block;'>"
                                                                                  +"<img class='imgResumenPrincipal' "
                                                                                  +"src='/public/images/monitoreo-tablets/sin-ubicacion.png'"
                                                                                  +"/></span>SIN UBICACIÓN: "
                                                                                  +arrayResumenNacional[0]['intNumTabletsProblGPS']+"</p>";
                document.getElementById("contTabletsNoMonitoreadas").innerHTML  = "<p style='font-size:10px;margin:0px;color:#d43f3a'> "
                                                                                  +"<span style='padding-right:3px; padding-top: 3px;"
                                                                                  +"display:inline-block;'>"
                                                                                  +"<img class='imgResumenPrincipal' "
                                                                                  +"src='/public/images/monitoreo-tablets/sin-informacion.png'"
                                                                                  +"/></span>SIN INFORMACIÓN: "
                                                                                  +arrayResumenNacional[0]['intNumTabletsNoMonitoreadas']+"</p>";
                                                                              
                
                /*Realizar Búsquedas de resúmenes regionales de manera secuencial, es decir luego de generar la consulta de resumen nacional*/
                Ext.Ajax.request
                ({
                    url: strUrlGenerarReporteGeneral,
                    method: 'post',
                    params:
                    { 
                        strTipoReporteMonitoreo:    'RESUMEN_GENERAL',
                        strRegionPerBusqAvanzada:   'R1'
                    },
                    success: function(result)
                    {   
                        var objDataR1       = Ext.JSON.decode(result.responseText);
                        var arrayResumenR1  = objDataR1.encontrados;
                        if(arrayResumenR1.length > 0)
                        {
                            document.getElementById("contTabletsTotalR1").innerHTML             = "<b>TOTAL: "
                                                                                                  +arrayResumenR1[0]['intNumTabletsTotal']+"</b>";
                            document.getElementById("contTabletsMantLibreR1").innerHTML         = "<b>MANTENIMIENTO: "
                                                                                                  +arrayResumenR1[0]['intNumTabletsMantLibre']+"</b>";

                            document.getElementById("contTabletsEnCampoR1").innerHTML           = "<b>EN CAMPO: "
                                                                                                  +arrayResumenR1[0]['intNumTabletsEnCampo']+"</b>";

                            document.getElementById("contTabletsCuadrillasLibresR1").innerHTML  = "<b>LIBRES: "
                                                                                                  +arrayResumenR1[0]['intNumTabletsCuadrillasLibres']
                                                                                                  +"</b>";

                            document.getElementById("contTabletsActualizadasR1").innerHTML    = "<p style='font-size:10px;margin:0px;color:#4cae4c'>"
                                                                                                +"<span style='padding-right:3px; padding-top: 3px;"
                                                                                                +"display:inline-block;'>"
                                                                                                +"<img class='imgResumenPrincipal' "
                                                                                                +"src='/public/images/monitoreo-tablets/marker/"
                                                                                                +"CON_UBICACION_ACTUALIZADA.png'/></span>"
                                                                                                +"CON UBICACIÓN ACTUALIZADA: "
                                                                                                +arrayResumenR1[0]['intNumTabletsActualizadas']
                                                                                                +"</p>";
                            document.getElementById("contTabletsDesactualizadasR1").innerHTML = "<p style='font-size:10px;margin:0px;color:#46b8da'>"
                                                                                                +"<span style='padding-right:3px; padding-top: 3px;"
                                                                                                +"display:inline-block;'>"
                                                                                                +"<img class='imgResumenPrincipal' "
                                                                                                +"src='/public/images/monitoreo-tablets/marker/"
                                                                                                +"CON_UBICACION_DESACTUALIZADA.png'/></span>"
                                                                                                +"CON UBICACIÓN DESACTUALIZADA: "
                                                                                                +arrayResumenR1[0]['intNumTabletsDesactualizadas']
                                                                                                +"</p>";
                            document.getElementById("contTabletsProblemasConGPSR1").innerHTML = "<p style='font-size:10px;margin:0px;color:#eea236'>"
                                                                                                +"<span style='padding-right:3px; padding-top: 3px;"
                                                                                                +"display:inline-block;'>"
                                                                                                +"<img class='imgResumenPrincipal' "
                                                                                                +"src='/public/images/monitoreo-tablets/"
                                                                                                +"sin-ubicacion.png'/></span>SIN UBICACIÓN: "
                                                                                                +arrayResumenR1[0]['intNumTabletsProblGPS']+"</p>";
                            document.getElementById("contTabletsNoMonitoreadasR1").innerHTML  = "<p style='font-size:10px;margin:0px;color:#d43f3a'>"
                                                                                                +"<span style='padding-right:3px; padding-top: 3px;"
                                                                                                +"display:inline-block;'>"
                                                                                                +"<img class='imgResumenPrincipal' "
                                                                                                +"src='/public/images/monitoreo-tablets/"
                                                                                                +"sin-informacion.png'/></span>SIN INFORMACIÓN: "
                                                                                                +arrayResumenR1[0]['intNumTabletsNoMonitoreadas']
                                                                                                +"</p>";
                                                                                            
                            
                            Ext.Ajax.request
                            ({
                                url: strUrlGenerarReporteGeneral,
                                method: 'post',
                                params:
                                { 
                                    strTipoReporteMonitoreo:    'RESUMEN_GENERAL',
                                    strRegionPerBusqAvanzada:   'R2'
                                },
                                success: function(result)
                                {   
                                    var objDataR2 = Ext.JSON.decode(result.responseText);
                                    var arrayResumenR2 = objDataR2.encontrados;
                                    if(arrayResumenR2.length > 0)
                                    {
                                        document.getElementById("contTabletsTotalR2").innerHTML     = "<b>TOTAL: "
                                                                                                      +arrayResumenR2[0]['intNumTabletsTotal']
                                                                                                      +"</b>";
                                        document.getElementById("contTabletsMantLibreR2").innerHTML = "<b>MANTENIMIENTO: "
                                                                                                      +arrayResumenR2[0]['intNumTabletsMantLibre']
                                                                                                      +"</b>";

                                        document.getElementById("contTabletsEnCampoR2").innerHTML           = "<b>EN CAMPO: "
                                                                                                              +arrayResumenR2[0]
                                                                                                              ['intNumTabletsEnCampo']
                                                                                                              +"</b>";

                                        document.getElementById("contTabletsCuadrillasLibresR2").innerHTML  = "<b>LIBRES: "
                                                                                                              +arrayResumenR2[0]
                                                                                                              ['intNumTabletsCuadrillasLibres']
                                                                                                              +"</b>";

                                        document.getElementById("contTabletsActualizadasR2").innerHTML    = "<p style='font-size:10px;margin:0px;"
                                                                                                            +"color:#4cae4c'>"
                                                                                                            +"<span style='padding-right:3px;"
                                                                                                            +"padding-top:3px;display:inline-block;'>"
                                                                                                            +"<img class='imgResumenPrincipal' "
                                                                                                            +"src='/public/images/monitoreo-tablets/"
                                                                                                            +"marker/CON_UBICACION_ACTUALIZADA.png'/>"
                                                                                                            +"</span>CON UBICACIÓN ACTUALIZADA: "
                                                                                                            +arrayResumenR2[0]
                                                                                                            ['intNumTabletsActualizadas']
                                                                                                            +"</p>";
                                        document.getElementById("contTabletsDesactualizadasR2").innerHTML = "<p style='font-size:10px;margin:0px;"
                                                                                                            +"color:#46b8da'>"
                                                                                                            +"<span style='padding-right:3px;"
                                                                                                            +"padding-top:3px;display:inline-block;'>"
                                                                                                            +"<img class='imgResumenPrincipal' "
                                                                                                            +"src='/public/images/monitoreo-tablets/"
                                                                                                            +"marker/CON_UBICACION_DESACTUALIZADA.png"
                                                                                                            +"'/></span>CON UBICACIÓN DESACTUALIZADA:"
                                                                                                            +" "+arrayResumenR2[0]
                                                                                                            ['intNumTabletsDesactualizadas']
                                                                                                            +"</p>";
                                        document.getElementById("contTabletsProblemasConGPSR2").innerHTML = "<p style='font-size:10px;margin:0px;"
                                                                                                            +"color:#eea236'>"
                                                                                                            +"<span style='padding-right:3px; "
                                                                                                            +"padding-top:3px;display:inline-block;'>"
                                                                                                            +"<img class='imgResumenPrincipal' "
                                                                                                            +"src='/public/images/monitoreo-tablets/"
                                                                                                            +"sin-ubicacion.png'/></span>"
                                                                                                            +"SIN UBICACIÓN: "+arrayResumenR2[0]
                                                                                                            ['intNumTabletsProblGPS']+"</p>";
                                        document.getElementById("contTabletsNoMonitoreadasR2").innerHTML  = "<p style='font-size:10px;margin:0px;"
                                                                                                            +"color:#d43f3a'>"
                                                                                                            +"<span style='padding-right:3px; "
                                                                                                            +"padding-top:3px;display:inline-block;'>"
                                                                                                            +"<img class='imgResumenPrincipal' "
                                                                                                            +"src='/public/images/monitoreo-tablets/"
                                                                                                            +"sin-informacion.png'/></span>"
                                                                                                            +"SIN INFORMACIÓN: "+arrayResumenR2[0]
                                                                                                            ['intNumTabletsNoMonitoreadas']
                                                                                                            +"</p>";
                                        document.getElementById("divExportarResumenNacional").style.display     = "block";
                                        document.getElementById("divExportarResumenRegionalR1").style.display   = "block";
                                        document.getElementById("divExportarResumenRegionalR2").style.display   = "block";

                                    }
                                    else
                                    {
                                        Ext.MessageBox.hide();
                                        Ext.Msg.alert('Error', 'Ha ocurrido un error al obtener el Resumen en R2, Por favor Notificar a Sistemas!'); 
                                    }


                                },
                                failure: function(result)
                                {
                                    Ext.MessageBox.hide();
                                    Ext.Msg.alert('Error',result.responseText); 
                                }
                            });

                        }
                        else
                        {
                            Ext.MessageBox.hide();
                            Ext.Msg.alert('Error', 'Ha ocurrido un error al obtener el Resumen en R2, Por favor Notificar a Sistemas!'); 
                        }


                    },
                    failure: function(result)
                    {
                        Ext.MessageBox.hide();
                        Ext.Msg.alert('Error',result.responseText); 
                    }
                });
                                                                              
                                                                              
                
            }
            else
            {
                Ext.MessageBox.hide();
                Ext.Msg.alert('Error', 'Ha ocurrido un error al obtener el Resumen Nacional, Por favor Notificar a Sistemas!'); 
            }
            
            
        },
        failure: function(result)
        {
            Ext.MessageBox.hide();
            Ext.Msg.alert('Error',result.responseText); 
        }
    });
}


function iniciarDatatableTabletsGeneralMonitoreadas(inicio, limite, strTipoReporte)
{
    $('#tableTablets'+strTipoReporte+' thead th div').each( function (i) 
    {
        var title = $('#tableTablets'+strTipoReporte+' thead th').eq(i).text();
        $(this).html( '<input id="valueBusq'+strTipoReporte+title+'" type="text" placeholder="'+title+'" data-index="'+i+'" />' );
    } );

    $("#tableTablets"+strTipoReporte).DataTable
    ({
        oLanguage: {
            "sProcessing": "Procesando...",
            "sEmptyTable": "No hay datos disponibles para su búsqueda",
            "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando 0 a 0 de 0 registros",
            "oPaginate": {
                "sPrevious": "Anterior",
                "sNext": "Siguiente"
            }
        },
        "processing": true,
        "bLengthChange": false,
        "serverSide": true,
        "iDisplayLength": limite,
        "iDisplayStart": inicio,
        "bFilter": false,
        "bSort": false,
        "scrollX": 400,
        "scrollY": 400,
        "destroy": true,
        "dom": '<"top"iflp<"clear">>rt<"bottom"flp<"clear">>',
        "fnDrawCallback": function( oSettings )
        {
            
            if(strTipoReporte==strTipoReporteConUbicacionActualizada)
            {
                intLimiteConUbicacionActualizada = oSettings._iDisplayLength;
                intInicioConUbicacionActualizada = oSettings._iDisplayStart;
            }
            else if(strTipoReporte==strTipoReporteConUbicacionDesactualizada)
            {
                intLimiteConUbicacionDesactualizada = oSettings._iDisplayLength;
                intInicioConUbicacionDesactualizada = oSettings._iDisplayStart;
            }
            else if(strTipoReporte==strTipoReporteSinUbicacion)
            {
                intLimiteConUbicacionDesactualizada = oSettings._iDisplayLength;
                intInicioConUbicacionDesactualizada = oSettings._iDisplayStart;
            }
            else if(strTipoReporte==strTipoReporteSinInformacion)
            {
                intLimiteSinInformacion = oSettings._iDisplayLength;
                intInicioSinInformacion = oSettings._iDisplayStart;
            }
            
            if(boolLoadFirstTime)
            {
                $('#tableTablets'+strTipoReporte).DataTable().columns().every( function () 
                {
                    $( 'input', this.header() ).on( 'keydown', function (ev) 
                    {
                        // La búsqueda sólo se realizará al presiona enter
                        if (ev.keyCode == 13) 
                        { 
                            buscarTabletsGeneralMonitoreadas(intValorInicial, intValorLimite, strTipoReporte);

                        }
                    });
                });
            }
            if(boolBusquedaGlobal)
            {
                intIndexEstadoMonitoreo++;
                if(intIndexEstadoMonitoreo<intLengthEstadoMonitoreo)
                {
                    buscarTabletsGeneralMonitoreadas(intValorInicial, intValorLimite, arrayEstadosMonitoreadas[intIndexEstadoMonitoreo]);
                }
                else
                {
                    boolLoadFirstTime   = false;
                    boolBusquedaGlobal  = false;
                }
            }
        },
        "ajax": 
        {
            "method": "post",
            "url": strUrlBuscarTablets,
            "dataType": "json",
            'beforeSend': function (jqXHR) {
                arrayXhrPool.push(jqXHR); 
            },
            'complete': function (jqXHR) {
                var indexXhrPool = arrayXhrPool.indexOf(jqXHR);   //obtener el indice de la reciente conexión completada
                if (indexXhrPool > -1)
                {
                    arrayXhrPool.splice(indexXhrPool, 1); //eliminar de la lista por el índice
                }
            },
            "data": function ( d ) {
                d.strTipoReporte                            = "EN_CAMPO";
                d.strImeiTablet                             = $('#valueBusq'+strTipoReporte+'IMEI').val();
                d.strSerieLogicaTablet                      = $('#valueBusq'+strTipoReporte+'PUBLISH_ID').val();
                d.strResponsableTablet                      = $('#valueBusq'+strTipoReporte+'Responsable').val();
                d.strDepartamentoPer                        = $('#valueBusq'+strTipoReporte+'Departamento').val();
                d.strRegionPerBusqAvanzada                  = $("#strRegionPerBusqAvanzada").val();
                d.intIdCantonPerBusqAvanzada                = $("#intIdCantonPerBusqAvanzada").val();
                d.intIdDepartamentoPerBusqAvanzada          = $("#intIdDepartamentoPerBusqAvanzada").val();
                d.intIdDepartamentoCuadrillaBusqAvanzada    = $('#intIdDepartamentoCuadrillaBusqAvanzada').val();
                d.intIdZonaCuadrillaBusqAvanzada            = $('#intIdZonaCuadrillaBusqAvanzada').val();
                d.intIdModeloBusqAvanzada                   = $('#intIdModeloBusqAvanzada').val();
                d.strEstadoMonitoreoBusqAvanzada            = strTipoReporte.replace(/_/g, " ");
                d.strFiltrarMisCuadrillasBusqAvanzada       = $('#strFiltrarMisCuadrillasBusqAvanzada').val();
                d.strFiltrarPorHorarioBusqAvanzada          = $('#strFiltrarPorHorarioBusqAvanzada').val();
            }
        },
        "createdRow": function ( row, data, index ) 
        {
            if((strTipoReporte==strTipoReporteConUbicacionActualizada) || (strTipoReporte==strTipoReporteConUbicacionDesactualizada))
            {
                var htmlLinkMarcadorMapa = "<a onclick='acercarMarcadorMapa("+data.intIdTablet+","+data.strLatitud+","+data.strLongitud+");'"
                                           +" style='cursor:pointer;'>"+data.strImeiTablet+"</a>";
                $('td', row).eq(1).html(htmlLinkMarcadorMapa);

                if(data.strSerieLogicaTablet != "N/A" && data.strSerieLogicaTablet != null)
                {
                    var htmlLinkMarcadorSerieLogicaMapa = "<a onclick='acercarMarcadorMapa("+data.intIdTablet+","+data.strLatitud+","+data.strLongitud+");'"
                                           +" style='cursor:pointer;'>"+data.strSerieLogicaTablet+"</a>";
                    $('td', row).eq(2).html(htmlLinkMarcadorSerieLogicaMapa);
                }
            }
        },
        columns: 
        [
            { data: 'strResponsable'},
            { data: 'strImeiTablet'},
            { data: 'strSerieLogicaTablet'},
            { data: 'strNombreDepartamentoPer'},
            { data: 'strFechaUltIntento'},
            { data: 'strFechaUltPunto'}
        ]
    });
    
    $('#tableTablets'+strTipoReporte).DataTable().columns.adjust();

    
}


function acercarMarcadorMapa(intIdTablet, strLatitud, strLongitud) 
{
    if(openInfoWindow)
    {
        openInfoWindow.close();
    }
    mapa.setZoom(13);
    mapa.setCenter(new google.maps.LatLng(strLatitud, strLongitud));
    arrayInfoWindow[intIdTablet].open(mapa, arrayMarkerPto[intIdTablet]);
    openInfoWindow = arrayInfoWindow[intIdTablet];
}

function exportarResumenDetallado(strTipoReporte,strRegionReporte)
{
    document.getElementById("strTipoExportarResumenDetallado").value    = strTipoReporte;
    document.getElementById("strRegionExportarResumenDetallado").value  = strRegionReporte;
    document.getElementById("formExportarResumenDetallado").submit();
}