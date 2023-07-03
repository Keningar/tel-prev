var objConneccion = new Ext.data.Connection
({
    listeners:
    {
        'beforerequest':
        {
            fn: function (con, opt)
            {
                Ext.get(document.body).mask('Cargando la información...');
            },
            scope: this
        },
        'requestcomplete':+
        {
            fn: function (con, res, opt)
            {
                Ext.get(document.body).unmask();
            },
            scope: this
        },
        'requestexception':
        {
            fn: function (con, res, opt)
            {
                Ext.get(document.body).unmask();
            },
            scope: this
        }
    }
});

Ext.onReady(function() 
{
    $('#divCalendario').monthpicker({dateFormat: 'mm-yyyy'});
    iniciarGraficosDashboard();
});


function iniciarGraficosDashboard()
{
    cargarGraficosPastel('ventasTotales', '', 'Ventas Totales');

    /*if( !Ext.isEmpty(strTipoVendedor) )
    {
        cargarGraficosBarChart(strTipoVendedor, strTipoVendedor);
    }
    else
    {
        cargarGraficosBarChart('PYMES', 'Pymes');
    }*/
}

function cargarInformacionDashboard()
{
    var strFechaSelected = document.getElementById("divCalendario").value;
    var strFechaInicio   = "";
    var strFechaFin      = "";

    if ( !Ext.isEmpty(strFechaSelected) )
    {
        if( typeof strFechaSelected === 'string')
        {
            var arrayFechaSeleccionada = strFechaSelected.split(', ');
            strFechaInicio             = "01-" + Utils.arrayNombreMesesIngles[arrayFechaSeleccionada[0]]+"-"+arrayFechaSeleccionada[1];

            //SE SUMA UN MES A LA FECHA DE INICIO
            strFechaSelected = Ext.Date.parse("01-" + Utils.arrayNombreMesesIngles[arrayFechaSeleccionada[0]] + "-"+arrayFechaSeleccionada[1],'d-M-Y');
            strFechaSelected.setMonth(strFechaSelected.getMonth() + 1);

            var strMesFin = "";
            var strDiaFin = strFechaSelected.getDate();
            var intNuevoMes = parseInt(strFechaSelected.getMonth()) + 1;
                strMesFin = intNuevoMes;
                
            if ( parseInt(intNuevoMes) < 10 )
            {
                strMesFin = "0"+intNuevoMes;
            }
            
            if ( parseInt(strDiaFin) < 10 )
            {
                strDiaFin = "0"+strDiaFin;
            }
                
            strFechaFin = strDiaFin + "-" + Utils.arrayNumeroMesesEnIngles[strMesFin] + "-" + strFechaSelected.getFullYear();
        }
    }

    document.getElementById('strFechaInicio').value = strFechaInicio;
    document.getElementById('strFechaFin').value    = strFechaFin;

    iniciarGraficosDashboard();
}

function cargarGraficosPastel(strIdVentasCategoria, strCategoria, strNombreSerie)
{
    document.getElementById('strCategoriaSelected').value = strCategoria;
    document.getElementById('strGrupoSelected').value     = '';

    Ext.MessageBox.wait("Cargando la información comercial...");
    
    Ext.Ajax.request
    ({
        method: 'post',
        dataType: 'json',
        timeout: 9000000,
        params :
        {
            strCategoria : strCategoria,
            strFechaInicio: document.getElementById('strFechaInicio').value,
            strFechaFin: document.getElementById('strFechaFin').value
        },
        url: strGetInformacionGraficosDashboardComercial,
        success: function(response)
        {
            Ext.MessageBox.hide();
            
            var objJson = Ext.JSON.decode(response.responseText);
            
            if(objJson.boolSuccess == true)
            {
                cargarInformacionDivsCorrespondientes(objJson, strCategoria, null);
                
                if( !Ext.isEmpty(objJson.arrayDataComercial) )
                {
                    var strTituloGrafico           = "";//objJson.strTituloGrafico;
                    var arrayDataComercial         = objJson.arrayDataComercial;
                    var arrayDataDrilldown         = objJson.arrayDataDrilldown;
                    var chartSeriesData            = [];
                    var chartDrilldownData         = [];

                    $.each(arrayDataComercial, function()
                    {
                        var objSerie           = new Object();
                            objSerie.name      = this.name;
                            objSerie.y         = parseFloat(this.y);
                            objSerie.drilldown = this.drilldown;

                        chartSeriesData.push(objSerie);
                    });

                    $.each(arrayDataDrilldown, function()
                    {
                        var objSerie               = new Object();
                            objSerie.name          = this.name;
                            objSerie.id            = this.id;
                            objSerie.data          = new Array();
                        var arrayItemDataDrilldown = this.arrayItemDataDrilldown;

                        $.each(arrayItemDataDrilldown, function()
                        {
                            var arraySerieDrilldownData = 
                            [
                                this.strSubgrupo,
                                parseFloat(this.floatSubgrupo)
                            ];

                            objSerie.data.push(arraySerieDrilldownData);
                        });

                        chartDrilldownData.push(objSerie);
                    });

                    Highcharts.chart(strIdVentasCategoria,
                    {
                        chart:
                        {
                            type: 'pie',
                            events:
                            {
                                drillup: function (e)
                                {
                                    cargarInformacionSubgrupos(strCategoria, null, 'grupos');
                                },
                                drilldown: function (e)
                                {
                                    if( e.seriesOptions )
                                    {
                                        cargarInformacionSubgrupos(null, e.point.name, 'subgrupos');
                                    }//( e.seriesOptions )
                                }//drilldown: function (e)
                            }//events:
                        },
                        title:
                        {
                            text: strTituloGrafico
                        },
                        legend:
                        {
                            align: 'right',
                            layout: 'vertical',
                            verticalAlign: 'middle',
                            x: -60,
                            y: 10,
                            width: 200,
                            useHTML: true,
                            labelFormatter: function()
                            {
                                return '<div style="width:300px">' +
                                       '    <span style="float:left; width:100px">' +
                                                this.name + 
                                       '    </span> ' +
                                       '</div>';
                            }
                        },
                        plotOptions:
                        {
                            series:
                            {
                                dataLabels:
                                {
                                    enabled: true,
                                    format: '<span style="color:{point.color}"><b>{point.name}:</b></span><br/>' +
                                            '<b>$ {point.y:,.2f}</b><br/>'+
                                            '<b>{point.percentage:,.2f} %</b>'
                                }
                            },
                            pie:
                            {
                                allowPointSelect: true,
                                cursor: 'pointer',
                                dataLabels:
                                {
                                    enabled: false
                                },
                                showInLegend: true
                            }
                        },
                        tooltip:
                        {
                            headerFormat: '<span style="font-size:11px">{series.name}</span><br/>',
                            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>$ {point.y:,.2f}</b><br/>'
                        },
                        series: 
                        [
                            {
                                name: strNombreSerie,
                                colorByPoint: true,
                                data: chartSeriesData
                            }
                        ],
                        drilldown:
                        {
                            series: chartDrilldownData,
                            drillUpButton:
                            {
                                relativeTo: 'spacingBox',
                                position:
                                {
                                    y: 0,
                                    x: 0
                                }
                            }
                        }
                    });
                }//( !Ext.isEmpty(objJson.arrayDataComercial) )
                else
                {
                    document.getElementById(strIdVentasCategoria).innerHTML = "";
                    Ext.Msg.alert('Alerta', 'No se ha encontrado información para mostrar de la opción seleccionada. (' + strNombreSerie + ').');
                }
                    
            }
            else
            {
                Ext.MessageBox.hide();
                Ext.Msg.alert('Alerta', objJson.strMensajeError);						
            }
        },
        failure: function(response)
        {
            Ext.MessageBox.hide();
            Ext.Msg.alert('Alerta', 'No se pudo consultar la información requerida.');
        }
    });
}//cargarGraficosPastel(strIdVentasCategoria, strCategoria)


function cargarInformacionSubgrupos(strCategoria, strGrupo, strInfoCargar)
{
    document.getElementById('strCategoriaSelected').value = strCategoria;
    document.getElementById('strGrupoSelected').value     = strGrupo;

    Ext.MessageBox.wait("Cargando la información de los " + strInfoCargar + "...");
    Ext.Ajax.request
    ({
        method: 'post',
        dataType: 'json',
        timeout: 9000000,
        params :
        {
            strCategoria: strCategoria,
            strGrupo : strGrupo,
            strFechaInicio: document.getElementById('strFechaInicio').value,
            strFechaFin: document.getElementById('strFechaFin').value
        },
        url: strGetInformacionGraficosDashboardComercial,
        success: function(response)
        {
            Ext.MessageBox.hide();

            var objJson = Ext.JSON.decode(response.responseText);

            if(objJson.boolSuccess == true)
            {
                cargarInformacionDivsCorrespondientes(objJson, strCategoria, strGrupo);
            }
            else
            {
                Ext.MessageBox.hide();
                Ext.Msg.alert('Alerta', objJson.strMensajeError);						
            }
        },
        failure: function(response)
        {
            Ext.MessageBox.hide();
            Ext.Msg.alert('Alerta', 'No se pudo consultar la información requerida.');
        }
    });
}


/**
 * cargarInformacionDivsCorrespondientes
 *
 * Función encargada de asignar información en los divs correspondientes
 *
 * @author Edson Franco <efranco@telconet.ec>
 * @version 1.0 01-03-2017
 *
 * @param string objJson      - objeto json que contiene la información
 * @param string strCategoria - categoria de los productos
 * @param string strGrupo     - grupo de los productos
 * 
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.1 27-03-2018
 * Se actualiza para que pueda llenar información de divs para facturación MRC y NRC
 * 
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.2 02-04-2018
 * Se corrige formateo de valores de Facturas y NC que llegan en 0
 *
 */
function cargarInformacionDivsCorrespondientes(objJson, strCategoria, strGrupo)
{
    var strMesActual                 = objJson.strMesActual;
    var strAnioActual                = objJson.strAnioActual;
    var intTotalVentasNoConcretadas  = objJson.intTotalVentasNoConcretadas;
    var floatVentasNoConcretadas     = objJson.floatVentasNoConcretadas;
    var floatMalasVentas             = objJson.floatMalasVentas;
    var floatBuenasVentas            = objJson.floatBuenasVentas;
    var intTotalBuenasVentas         = objJson.intTotalBuenasVentas;
    var intTotalMalasVentas          = objJson.intTotalMalasVentas;
    var floatFacturacionUnica        = objJson.floatFacturacionUnica;
    var floatFacturacionMensual      = objJson.floatFacturacionMensual;
    var floatFacturacionNoMensual    = objJson.floatFacturacionNoMensual;
    var arrayProductosDestacados     = objJson.arrayProductosDestacados;
    var arrayVendedoresDestacados    = objJson.arrayVendedoresDestacados;
    var arrayFacturacionAsesor       = objJson.arrayCarteraAsesor;
    var arrayFacturacionAsesorTri    = objJson.arrayCarteraAsesorTrimes;
    var arrayClientesNuevos          = objJson.clientesNuevos;
    var arrayClientesCancel          = objJson.clientesCancel;
    var arrayClientesFact            = objJson.clientesFact;    
    var floatTotalPresupuestoMRC     = objJson.totalPresupuestoMrc;
    var floatTotalPresupuestoMRCID   = objJson.totalPresupuestoMrcID;
    var floatTotalPresupuestoMRCBS   = objJson.totalPresupuestoMrcBS;
    var floatTotalPresupuestoNRC     = objJson.totalPresupuestoNrc;    
    var intPorcentajeMRC             = parseFloat(objJson.totalPorcentajeMrc.replace(/\,/g,''));
    var intPorcentajeMRCID           = parseFloat(objJson.totalPorcentajeMrcID.replace(/\,/g,''));
    var intPorcentajeMRCBS           = parseFloat(objJson.totalPorcentajeMrcBS.replace(/\,/g,''));
    var intPorcentajeNRC             = parseFloat(objJson.totalPorcentajeNrc.replace(/\,/g,''));
    var floatTotalMrc                = 0;
    var floatTotalMrcID              = 0;
    var floatTotalMrcBS              = 0;
    var floatTotalNrc                = 0;
    var floatTotalNrcTri             = 0;
    var totalMrc                     = 0;
    var totalNrc                     = 0;
    var totalNrcTri                  = 0;
    var intCantidadClientesNuevos    = 0;
    var floatTotalClientesNuevos     = 0;
    var intCantidadClientesCancel    = 0;
    var floatTotalClientesCancel     = 0;
    var intCantidadClientesFact      = 0;
    var floatTotalFact               = 0;
    var strDivSemaforoMRC            = "";
    var strDivSemaforoMRCID          = "";
    var strDivSemaforoMRCBS          = "";
    var strDivSemaforoNRC            = "";    
    
    document.getElementById('divCalendario').value                    = strMesActual + ", " + strAnioActual;
    document.getElementById('strVentasFechaActual').innerHTML         = "del Mes - " + strMesActual + " " + strAnioActual;
    document.getElementById('intTotalVentasNoConcretadas').innerHTML  = intTotalVentasNoConcretadas;
    document.getElementById('floatVentasNoConcretadas').innerHTML     = floatVentasNoConcretadas;
    document.getElementById('floatMalasVentas').innerHTML             = floatMalasVentas;
    document.getElementById('floatBuenasVentas').innerHTML            = floatBuenasVentas;
    document.getElementById('intTotalBuenasVentas').innerHTML         = intTotalBuenasVentas;
    document.getElementById('intTotalMalasVentas').innerHTML          = intTotalMalasVentas;
    document.getElementById('floatFacturacionUnica').innerHTML        = '$ ' + floatFacturacionUnica;
    document.getElementById('floatFacturacionMensual').innerHTML      = '$ ' + floatFacturacionMensual;
    document.getElementById('floatFacturacionNoMensual').innerHTML    = '$ ' + floatFacturacionNoMensual;    
    document.getElementById('strMesAnioActualClientesCancelados').innerHTML = strMesActual + " " + strAnioActual;

    var strDivProductosDestacados  = "";
    var strDivVendedoresDestacados = "";
    
    if( Ext.isEmpty(strCategoria) )
    {
        strCategoria = '';
    }
    
    if( Ext.isEmpty(strGrupo) )
    {
        strGrupo = '';
    }

    $.each(arrayVendedoresDestacados, function()
    {
        strDivVendedoresDestacados = strDivVendedoresDestacados + '<div class="col-lg-6">' +
                                     '   <span class="progress-description" style="margin-top: 5px;">' +
                                             this.strVendedor +
                                     '   </span>' +
                                     '</div>' +
                                     '<div class="col-lg-5">' +
                                         '<span class="info-box-number text-right">$ ' + this.floatVenta + '</span>' +
                                     '</div>';
    });
    strDivVendedoresDestacados = strDivVendedoresDestacados 
                                 + '<div class="col-lg-6"><span class="info-box-text">&nbsp;</span></div>'
                                 + '<div class="col-lg-6"><span class="info-box-text">&nbsp;</span></div>';
    if( !Ext.isEmpty(strDivVendedoresDestacados) )
    {
        strDivVendedoresDestacados = strDivVendedoresDestacados + '<div class="small-box col-lg-11" style="width: 98%; margin-top:13px;">'+
                                     '<div class="icon"></div>' +
                                     '<a href="#" onclick="verMas(\'Vendedores\');" data-toggle="modal" data-target="#modalVerMas" ' + 
                                     'class="small-box-footer"> '+
                                     '  Ver m&aacute;s<i class="fa fa-arrow-circle-right" style="margin-left: 5px;"></i></a>' +
                                     '</div>';
    }//( !Ext.isEmpty(strDivVendedoresDestacados) )
    else
    {
        strDivVendedoresDestacados = '<div class="col-lg-1">&nbsp;</div>' +
                                     '<div class="col-lg-10">' +
                                     '   <span class="progress-description" style="margin-top: 5px;">' +
                                     '      No se ha encontrado el listado de Vendedores Destacados' +
                                     '   </span>' +
                                     '</div>' +
                                     '<div class="col-lg-1">&nbsp;</div>';
    }

    document.getElementById('divArrayVendedoresDestacados').innerHTML = strDivVendedoresDestacados;

    $.each(arrayProductosDestacados, function()
    {
        strDivProductosDestacados = strDivProductosDestacados + '<div class="col-lg-6">' +
                                    '   <span class="progress-description" style="margin-top: 5px;">' +
                                            this.strProducto +
                                    '   </span>' +
                                    '</div>' +
                                    '<div class="col-lg-5">' +
                                        '<span class="info-box-number text-right">$ ' + this.floatVenta + '</span>' +
                                    '</div>';
    });
    strDivProductosDestacados = strDivProductosDestacados 
                                + '<div class="col-lg-6"><span class="info-box-text">&nbsp;</span></div>'
                                + '<div class="col-lg-6"><span class="info-box-text">&nbsp;</span></div>';
    if( !Ext.isEmpty(strDivProductosDestacados) )
    {
        strDivProductosDestacados = strDivProductosDestacados + '<div class="small-box col-lg-11" style="width: 98%; margin-top:12px;">'+
                                     '<div class="icon"></div>' +
                                     '<a href="#" onclick="verMas(\'Productos\');" data-toggle="modal" data-target="#modalVerMas" ' +
                                     'class="small-box-footer"> '+
                                     '  Ver m&aacute;s<i class="fa fa-arrow-circle-right" style="margin-left: 5px;"></i></a>' +
                                     '</div>';
    }//( !Ext.isEmpty(strDivVendedoresDestacados) )
    else
    {
        strDivProductosDestacados = '<div class="col-lg-1">&nbsp;</div>' +
                                     '<div class="col-lg-10">' +
                                     '   <span class="progress-description" style="margin-top: 5px;">' +
                                     '      No se ha encontrado el listado de Productos Destacados' +
                                     '   </span>' +
                                     '</div>' +
                                     '<div class="col-lg-1">&nbsp;</div>';
    }

    document.getElementById('divArrayProductosDestacados').innerHTML = strDivProductosDestacados;

    var mesActualCorto         = "";
    //
    $.each(arrayFacturacionAsesor, function()
    {
        mesActualCorto = this.strMes.substring(0,3);
        if ((mesActualCorto.toUpperCase() === strMesActual.toUpperCase()) || (this.strMes.toUpperCase() === strMesActual.toUpperCase())) 
        {
            if (this.floatFacMrc!==0)
            {
                floatTotalMrc = floatTotalMrc + parseFloat(this.floatFacMrc.replace(/\,/g,''));
            }
            if (this.floatNcMrc!==0)
            {
                floatTotalMrc = floatTotalMrc + parseFloat(this.floatNcMrc.replace(/\,/g,''));
            }
            if (this.floatFacNrc!==0)
            {
                floatTotalNrc = floatTotalNrc + parseFloat(this.floatFacNrc.replace(/\,/g,''));
            }
            if (this.floatNcNrc!==0)
            {
                floatTotalNrc = floatTotalNrc + parseFloat(this.floatNcNrc.replace(/\,/g,''));
            }
            if (this.intClientesMrc!==0)
            {
                totalMrc = totalMrc + parseFloat(this.intClientesMrc.replace(/\,/g,''));
            }
            if (this.intClientesNrc!==0)
            {
                totalNrc = totalNrc + parseFloat(this.intClientesNrc.replace(/\,/g,''));
            }
            if (this.floatFacMrcID!==0)
            {
                floatTotalMrcID = floatTotalMrcID + parseFloat(this.floatFacMrcID.replace(/\,/g,''));
            }
            if (this.floatNcMrcID!==0)
            {
                floatTotalMrcID = floatTotalMrcID + parseFloat(this.floatNcMrcID.replace(/\,/g,''));
            }
            if (this.floatFacMrcBS!==0)
            {
                floatTotalMrcBS = floatTotalMrcBS + parseFloat(this.floatFacMrcBS.replace(/\,/g,''));
            }
            if (this.floatNcMrcBS!==0)
            {
                floatTotalMrcBS = floatTotalMrcBS + parseFloat(this.floatNcMrcBS.replace(/\,/g,''));
            }
        }
    });

//cambio mdleon
    $.each(arrayFacturacionAsesorTri, function()
    {
        if (this.floatFacNrcTri!==0)
        {
            floatTotalNrcTri = floatTotalNrcTri + parseFloat(this.floatFacNrcTri.replace(/\,/g,''));
        }
        if (this.floatNcNrcTri!==0)
        {
            floatTotalNrcTri = floatTotalNrcTri + parseFloat(this.floatNcNrcTri.replace(/\,/g,''));
        }
        if (this.intClientesNrcTri!==0)
        {
            totalNrcTri = totalNrcTri + this.intClientesNrcTri;
        }
    });
//fin cambio mdleon
    document.getElementById('floatMrc').innerHTML               = formatearValor(floatTotalMrc, 2);
    document.getElementById('intInternetDatos').innerHTML       = formatearValor(floatTotalMrcID, 2);
    document.getElementById('Business').innerHTML               = formatearValor(floatTotalMrcBS, 2);
    //document.getElementById('OtrosMrc').innerHTML               = formatearValor(floatTotalMrc-floatTotalMrcID-floatTotalMrcBS, 2);
    document.getElementById('floatNrc').innerHTML               = formatearValor(floatTotalNrc, 2);
    document.getElementById('floatNrc3').innerHTML               = formatearValor(floatTotalNrcTri, 2);

    document.getElementById('intTotalMrc').innerHTML            = totalMrc + ' Clientes';
    document.getElementById('intTotalNrc').innerHTML            = totalNrc + ' Clientes';
    document.getElementById('intTotalNrcTri').innerHTML         = totalNrcTri + ' Clientes';
    
    $.each(arrayClientesNuevos, function()    
    {
        intCantidadClientesNuevos++;
        floatTotalClientesNuevos=floatTotalClientesNuevos+parseFloat(this.TOTAL);
    });
    $.each(arrayClientesCancel, function()    
    {
        intCantidadClientesCancel++;
        floatTotalClientesCancel=floatTotalClientesCancel+parseFloat(this.TOTAL);
    }); 
    $.each(arrayClientesFact, function()    
    {
        intCantidadClientesFact++;
        floatTotalFact=floatTotalFact+parseFloat(this.TOTAL);
    });     
                        
    if( intPorcentajeMRC <= 0 || (intPorcentajeMRC > 0 && intPorcentajeMRC <= 30) )
    {
        strDivSemaforoMRC     = '<div style="line-height: 1!important; font-size: 40px "class="text-danger"><strong>'+intPorcentajeMRC+'%</strong></div>'+
                                '<div class="progress" style="height: 5px;">'+
                                    '<div class="progress-bar progress-bar-danger" role="progressbar" style="width: '+intPorcentajeMRC+'%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>'+
                                '</div>';
    }
    else if(intPorcentajeMRC > 30 && intPorcentajeMRC <= 60)
    {
        strDivSemaforoMRC     = '<div style="line-height: 1!important; font-size: 40px "class="text-warning"><strong>'+intPorcentajeMRC+'%</strong></div>'+
                                '<div class="progress" style="height: 5px;">'+
                                    '<div class="progress-bar progress-bar-warning" role="progressbar" style="width: '+intPorcentajeMRC+'%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>'+
                                '</div>';
                            
    }
    else if(intPorcentajeMRC > 60 && intPorcentajeMRC < 100)
    {
        strDivSemaforoMRC     = '<div style="line-height: 1!important; font-size: 40px "class="text-info"><strong>'+intPorcentajeMRC+'%</strong></div>'+
                                '<div class="progress" style="height: 5px;">'+
                                    '<div class="progress-bar progress-bar-info" role="progressbar" style="width: '+intPorcentajeMRC+'%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>'+
                                '</div>';
                            
    }
    else if(intPorcentajeMRC >= 100)
    {
        strDivSemaforoMRC     = '<div style="line-height: 1!important; font-size: 40px "class="text-success"><strong>'+intPorcentajeMRC+'%</strong></div>'+
                                '<div class="progress" style="height: 5px;">'+
                                    '<div class="progress-bar progress-bar-success" role="progressbar" style="width: 100%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>'+
                                '</div>';        
    }
    
    //Inicio
    if( intPorcentajeMRCID <= 0 || (intPorcentajeMRCID > 0 && intPorcentajeMRCID <= 30) )
    {
        strDivSemaforoMRCID     = '<div style="line-height: 1!important; font-size: 15px "class="text-danger"><strong>'+intPorcentajeMRCID+'% Internet/Datos</strong></div>'+
                                '<div class="progress" style="height: 5px;">'+
                                    '<div class="progress-bar progress-bar-danger" role="progressbar" style="width: '+intPorcentajeMRCID+'%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>'+
                                '</div>';
    }
    else if(intPorcentajeMRCID > 30 && intPorcentajeMRCID <= 60)
    {
        strDivSemaforoMRCID     = '<div style="line-height: 1!important; font-size: 15px "class="text-warning"><strong>'+intPorcentajeMRCID+'% Internet/Datos</strong></div>'+
                                '<div class="progress" style="height: 5px;">'+
                                    '<div class="progress-bar progress-bar-warning" role="progressbar" style="width: '+intPorcentajeMRCID+'%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>'+
                                '</div>';
                            
    }
    else if(intPorcentajeMRCID > 60 && intPorcentajeMRCID < 100)
    {
        strDivSemaforoMRCID     = '<div style="line-height: 1!important; font-size: 15px "class="text-info"><strong>'+intPorcentajeMRCID+'% Internet/Datos</strong></div>'+
                                '<div class="progress" style="height: 5px;">'+
                                    '<div class="progress-bar progress-bar-info" role="progressbar" style="width: '+intPorcentajeMRCID+'%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>'+
                                '</div>';
                            
    }
    else if(intPorcentajeMRCID >= 100)
    {
        strDivSemaforoMRCID     = '<div style="line-height: 1!important; font-size: 15px "class="text-success"><strong>'+intPorcentajeMRCID+'% Internet/Datos</strong></div>'+
                                '<div class="progress" style="height: 5px;">'+
                                    '<div class="progress-bar progress-bar-success" role="progressbar" style="width: 100%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>'+
                                '</div>';        
    }
    if( intPorcentajeMRCBS <= 0 || (intPorcentajeMRCBS > 0 && intPorcentajeMRCBS <= 30) )
    {
        strDivSemaforoMRCBS     = '<div style="line-height: 1!important; font-size: 15px "class="text-danger"><strong>'+intPorcentajeMRCBS+'% Business Solution</strong></div>'+
                                '<div class="progress" style="height: 5px;">'+
                                    '<div class="progress-bar progress-bar-danger" role="progressbar" style="width: '+intPorcentajeMRCBS+'%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>'+
                                '</div>';
    }
    else if(intPorcentajeMRCBS > 30 && intPorcentajeMRCBS <= 60)
    {
        strDivSemaforoMRCBS     = '<div style="line-height: 1!important; font-size: 15px "class="text-warning"><strong>'+intPorcentajeMRCBS+'% Business Solution</strong></div>'+
                                '<div class="progress" style="height: 5px;">'+
                                    '<div class="progress-bar progress-bar-warning" role="progressbar" style="width: '+intPorcentajeMRCBS+'%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>'+
                                '</div>';
                            
    }
    else if(intPorcentajeMRCBS > 60 && intPorcentajeMRCBS < 100)
    {
        strDivSemaforoMRCBS     = '<div style="line-height: 1!important; font-size: 15px "class="text-info"><strong>'+intPorcentajeMRCBS+'% Business Solution</strong></div>'+
                                '<div class="progress" style="height: 5px;">'+
                                    '<div class="progress-bar progress-bar-info" role="progressbar" style="width: '+intPorcentajeMRCBS+'%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>'+
                                '</div>';
                            
    }
    else if(intPorcentajeMRCBS >= 100)
    {
        strDivSemaforoMRCBS     = '<div style="line-height: 1!important; font-size: 15px "class="text-success"><strong>'+intPorcentajeMRCBS+'% Business Solution</strong></div>'+
                                '<div class="progress" style="height: 5px;">'+
                                    '<div class="progress-bar progress-bar-success" role="progressbar" style="width: 100%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>'+
                                '</div>';        
    }
    //FIn

    if( intPorcentajeNRC <= 0 || (intPorcentajeNRC > 0 && intPorcentajeNRC <= 30) )
    {
        strDivSemaforoNRC     = '<div style="line-height: 1!important; font-size: 40px "class="text-danger"><strong>'+intPorcentajeNRC+'%</strong></div>'+
                                '<div class="progress" style="height: 5px;">'+
                                    '<div class="progress-bar progress-bar-danger" role="progressbar" style="width: '+intPorcentajeNRC+'%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>'+
                                '</div>';
    }
    else if(intPorcentajeNRC > 30 && intPorcentajeNRC <= 60)
    {
        strDivSemaforoNRC     = '<div style="line-height: 1!important; font-size: 40px "class="text-warning"><strong>'+intPorcentajeNRC+'%</strong></div>'+
                                '<div class="progress" style="height: 5px;">'+
                                    '<div class="progress-bar progress-bar-warning" role="progressbar" style="width: '+intPorcentajeNRC+'%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>'+
                                '</div>';
                            
    }
    else if(intPorcentajeNRC > 60 && intPorcentajeNRC < 100)
    {
        strDivSemaforoNRC     = '<div style="line-height: 1!important; font-size: 40px "class="text-info"><strong>'+intPorcentajeNRC+'%</strong></div>'+
                                '<div class="progress" style="height: 5px;">'+
                                    '<div class="progress-bar progress-bar-info" role="progressbar" style="width: '+intPorcentajeNRC+'%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>'+
                                '</div>';
                            
    }
    else if(intPorcentajeNRC >= 100)
    {
        strDivSemaforoNRC     = '<div style="line-height: 1!important; font-size: 40px "class="text-success"><strong>'+intPorcentajeNRC+'%</strong></div>'+
                                '<div class="progress" style="height: 5px;">'+
                                    '<div class="progress-bar progress-bar-success" role="progressbar" style="width: 100%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>'+
                                '</div>';        
    }
    
    document.getElementById('intCantidadClientesNuevos').innerHTML  = intCantidadClientesNuevos + ' Clt. nuevos(mes vigente)';
    document.getElementById('floatTotalClientesNuevos').innerHTML   = '$ ' + formatearValor(floatTotalClientesNuevos, 2);   
    document.getElementById('intCantidadClientesCancel').innerHTML  = intCantidadClientesCancel + ' Clt. cancelados(mes anterior)';
    document.getElementById('floatTotalClientesCancel').innerHTML   = '$ ' + formatearValor(floatTotalClientesCancel, 2);   
    document.getElementById('intCantidadClientesFact').innerHTML    = intCantidadClientesFact + ' Clt. por facturar(mes vigente)';
    document.getElementById('floatTotalClientesFact').innerHTML     = '$ ' + formatearValor(floatTotalFact, 2);    
    document.getElementById('TotalPresupuestoMRC').innerHTML        = formatearValor(floatTotalPresupuestoMRC, 2);  
    document.getElementById('TotalPresupuestoMRCID').innerHTML        = formatearValor(floatTotalPresupuestoMRCID, 2);
    document.getElementById('TotalPresupuestoMRCBS').innerHTML        = formatearValor(floatTotalPresupuestoMRCBS, 2);
    document.getElementById('TotalPresupuestoNRC').innerHTML        = formatearValor(floatTotalPresupuestoNRC, 2);   
    document.getElementById('SemaforoMRC').innerHTML                = strDivSemaforoMRC;
    document.getElementById('SemaforoMRCID').innerHTML              = strDivSemaforoMRCID;
    document.getElementById('SemaforoMRCBS').innerHTML              = strDivSemaforoMRCBS;
    document.getElementById('SemaforoNRC').innerHTML                = strDivSemaforoNRC;
}

/**
 * formatearValor
 *
 * Función encargada de mostrar popup con el detalle de facturación MRC o NRC según el parametro recibido.
 *
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 27-03-2018
 *
 * @param string amount    - valor a formatear
 * @param string decimales - cantidad de decimales
 *
 *
 */
function formatearValor(amount, decimales) {

    var sign  = (amount.toString().substring(0, 1) == "-");
    amount   += ''; // por si pasan un numero en vez de un string
    amount    = parseFloat(amount.replace(/[^0-9\.]/g, '')); // elimino cualquier cosa que no sea numero o punto
    decimales = decimales || 0; // por si la variable no fue fue pasada

    // si no es un numero o es igual a cero retorno el mismo cero
    if (isNaN(amount) || amount === 0) 
        return parseFloat(0).toFixed(decimales);

    // si es mayor o menor que cero retorno el valor formateado como numero
    amount = '' + amount.toFixed(decimales);

    var amount_parts = amount.split('.'),
        regexp = /(\d+)(\d{3})/;

    while (regexp.test(amount_parts[0]))
        amount_parts[0] = amount_parts[0].replace(regexp, '$1' + ',' + '$2');

    return sign ? '-' + amount_parts.join('.') : amount_parts.join('.');
}

function verMas(strTipo)
{
    var strModalTitle     = '';
    var strUrlCargar      = '';
    var strCategoria      = document.getElementById('strCategoriaSelected').value;
    var strGrupo          = document.getElementById('strGrupoSelected').value;
    var strClassModal     = '';
    var strClassBodyModal = '';
    var strClassDialog    = '';
    var strFechaInicio    = document.getElementById('strFechaInicio').value;
    var strFechaFin       = document.getElementById('strFechaFin').value;

    $("#modalVerMas").removeClass("modal-info");
    $("#modalVerMas").removeClass("modal-danger");
    $("#modalVerMas").removeClass("modal-success");
    $("#modalVerMas").removeClass("modal-primary");
    $("#modalVerMas").removeClass("modal-warning");
    $("#divModalTitle").removeClass("width-700px");
    $("#strModalBody").removeClass("width-700px");
    $("#strModalBody").removeClass("height-250px");
    
    if( strTipo == "Vendedores" )
    {
        strClassModal = "modal-warning";
        strModalTitle = 'Vendedores Destacados';
        strUrlCargar  = strUrlInformacionDestacados;
    }
    else if( strTipo == "Productos" )
    {
        strClassModal = "modal-primary";
        strModalTitle = 'Productos Destacados';
        strUrlCargar  = strUrlInformacionDestacados;
    }
    else if( strTipo == "ORDENES_ACTIVAS" )
    {
        strModalTitle     = 'Detalle de las Ventas Activas';
        strUrlCargar      = strUrlDetalladoVentas;
        strClassModal     = "modal-info";
        strClassBodyModal = "width-700px";
        strClassDialog    = "height-250px";
    }
    else if( strTipo == "ORDENES_CANCELADAS" )
    {
        strModalTitle               = 'Detalle de las &Oacute;rdenes Canceladas';
        strUrlCargar                = strUrlDetalladoVentas;
        strClassModal               = "modal-danger";
        strClassBodyModal           = "width-700px";
        strClassDialog              = "height-250px";
        strFechaInicio              = document.getElementById('strFechaInicio').value;
        strFechaFin                 = document.getElementById('strFechaFin').value;
    }
    else if( strTipo == "ORDENES_NO_CONCRETADAS" )
    {
        strModalTitle     = 'Detalle de las Ventas Por Implementar';
        strUrlCargar      = strUrlDetalladoVentas;
        strClassModal     = "modal-info";
        strClassBodyModal = "width-700px";
        strClassDialog    = "height-250px";
    }
    else if( strTipo == "CLIENTES" )
    {
        strModalTitle     = 'Detalle de Clientes';
        strUrlCargar      = strUrlGetDetalleResultadosClientes;
        strClassModal     = "modal-warning";
        strClassBodyModal = "width-700px";
        strClassDialog    = "height-250px";
    }    
    $("#modalVerMas").addClass(strClassModal);
    $("#divModalTitle").addClass(strClassBodyModal);
    $("#strModalBody").addClass(strClassBodyModal);
    $("#strModalBody").addClass(strClassDialog);
    
    document.getElementById('strModalTitle').innerHTML = strModalTitle;
    document.getElementById('strModalBody').innerHTML  = '';

    Ext.MessageBox.wait("Cargando la información...");
    Ext.Ajax.request
    ({
        method: 'post',
        dataType: 'json',
        timeout: 9000000,
        params :
        {
            strCategoria: strCategoria,
            strGrupo : strGrupo,
            strTipo: strTipo,
            strFechaInicio: strFechaInicio,
            strFechaFin: strFechaFin
        },
        url: strUrlCargar,
        success: function(response)
        {
            Ext.MessageBox.hide();

            var objJson = Ext.JSON.decode(response.responseText);

            if(objJson.boolSuccess == false)
            {
                Ext.MessageBox.hide();
                Ext.Msg.alert('Alerta', objJson.strMensajeRespuesta);
            }

            document.getElementById('strModalBody').innerHTML = objJson.strBodyModal;
        },
        failure: function(response)
        {
            Ext.MessageBox.hide();
            Ext.Msg.alert('Alerta', 'No se pudo consultar la información requerida.');

            document.getElementById('strModalBody').innerHTML = '<div class="row">' +
                                                                '  <div class="col-lg-2">&nbsp;</div>' +
                                                                '  <div class="col-lg-8">No se ha encontrado ' + strTipo + '.</div>' +
                                                                '  <div class="col-lg-2">&nbsp;</div>' +
                                                                '</div>';
        }
    });
}

/**
 * verDetalleFact
 *
 * Función encargada de mostrar popup con el detalle de facturación MRC o NRC según el parametro recibido.
 *
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 27-03-2018
 *
 * @param string $strTipo - Tipo de facturación
 *
 *
 */
function verDetalleFact(strTipo,strDetalle)
{
    var strModalTitle     = '';
    var strUrlCargar      = '';
    var strClassModal     = '';
    var strClassBodyModal = '';
    var strClassDialog    = '';
    var strFechaInicio    = document.getElementById('strFechaInicio').value;
    var strFechaFin       = document.getElementById('strFechaFin').value;

    $("#modalVerMas").removeClass("modal-info");
    $("#modalVerMas").removeClass("modal-danger");
    $("#modalVerMas").removeClass("modal-success");
    $("#modalVerMas").removeClass("modal-primary");
    $("#modalVerMas").removeClass("modal-warning");
    $("#divModalTitle").removeClass("width-700px");
    $("#strModalBody").removeClass("width-700px");
    $("#strModalBody").removeClass("height-250px");
    if( strTipo == "MRC" )
    {
        strUrlCargar   = strUrlDetalladoFacturacion;
        strClassModal = "modal-success";
        strModalTitle = 'Facturaci&oacute;n MRC';
    }
    else if( strTipo == "NRC" )
    {
        strUrlCargar   = strUrlDetalladoFacturacion;
        strClassModal = "modal-success";
        strModalTitle = 'Facturaci&oacute;n NRC';
    }
    else if( strTipo == "ORDENES_MRC" )
    {
        strUrlCargar   = strUrlDetalladoFacturacion;
        strClassModal = "modal-success";                
        strModalTitle = 'Detalle de las &Oacute;rdenes';
    }
    $("#modalVerMas").addClass(strClassModal);
    $("#divModalTitle").addClass(strClassBodyModal);
    $("#strModalBody").addClass(strClassBodyModal);
    $("#strModalBody").addClass(strClassDialog);
    $("#modalVerMas").css({"padding-right":"400px"});
    document.getElementById('strModalTitle').innerHTML = strModalTitle;
    document.getElementById('strModalBody').innerHTML  = '';
    Ext.MessageBox.wait("Cargando la información...");
    Ext.Ajax.request
    ({
        method: 'post',
        dataType: 'json',
        timeout: 9000000,
        params :
        {
            strTipo: strTipo,
            strTipoConsulta: strDetalle,
            strFechaInicio: strFechaInicio,
            strFechaFin: strFechaFin
        },
        url: strUrlCargar,
        success: function(response)
        {
            Ext.MessageBox.hide();
            var objJson = Ext.JSON.decode(response.responseText);
            if(objJson.boolSuccess == false)
            {
                Ext.MessageBox.hide();
                Ext.Msg.alert('Alerta', objJson.strMensajeError);
            }
            document.getElementById('strModalBody').innerHTML = objJson.strBodyModal;
        },
        failure: function(response)
        {
            Ext.MessageBox.hide();
            Ext.Msg.alert('Alerta', 'No se pudo consultar la información requerida.');
            document.getElementById('strModalBody').innerHTML = '<div class="row">' +
                                                                '  <div class="col-lg-2">&nbsp;</div>' +
                                                                '  <div class="col-lg-8">No se ha encontrado ' + strTipo + '.</div>' +
                                                                '  <div class="col-lg-2">&nbsp;</div>' +
                                                                '</div>';
        }
    });
}

/**
 * verDetalleResultadosVentas
 *
 * Función encargada de mostrar popup con el detalle de facturación MRC o NRC según el parametro recibido.
 *
 * @author Kevin Baque <kbaque@telconet.ec>
 * @version 1.0 17-08-2018
 *
 * @param string $strTipo - Tipo de facturación
 *
 *
 */
function verDetalleResultadosVentas(strTipo,strGrupo)
{
    var strModalTitle     = '';
    var strUrlCargar      = '';
    var strClassModal     = '';
    var strClassBodyModal = '';
    var strClassDialog    = '';
    var strTipoConsulta   = '';
    var strFechaInicio    = document.getElementById('strFechaInicio').value;
    var strFechaFin       = document.getElementById('strFechaFin').value;

    $("#modalVerMas").removeClass("modal-info");
    $("#modalVerMas").removeClass("modal-danger");
    $("#modalVerMas").removeClass("modal-success");
    $("#modalVerMas").removeClass("modal-primary");
    $("#modalVerMas").removeClass("modal-warning");
    $("#divModalTitle").removeClass("width-700px");
    $("#strModalBody").removeClass("width-700px");
    $("#strModalBody").removeClass("height-250px");
    if( strTipo == "MRC" )
    {
        strTipoConsulta ='CUMPLIMIENTO_MRC';
        strUrlCargar   = strUrlDetalladoResultadosVentas;
        strClassModal = "modal-primary";
        strModalTitle = 'Cumplimiento de presupuesto  MRC';
    }
    else if( strTipo == "NRC" )
    {
        strTipoConsulta ='CUMPLIMIENTO_NRC';        
        strUrlCargar   = strUrlDetalladoResultadosVentas;
        strClassModal = "modal-primary";
        strModalTitle = 'Cumplimiento de presupuesto  NRC';
    }
    
    $("#modalVerMas").addClass(strClassModal);
    $("#divModalTitle").addClass(strClassBodyModal);
    $("#strModalBody").addClass(strClassBodyModal);
    $("#strModalBody").addClass(strClassDialog);
    $("#modalVerMas").css({"padding-right":"400px"});
    document.getElementById('strModalTitle').innerHTML = strModalTitle;
    document.getElementById('strModalBody').innerHTML  = '';
    Ext.MessageBox.wait("Cargando la información...");
    Ext.Ajax.request
    ({
        method: 'post',
        dataType: 'json',
        timeout: 9000000,
        params :
        {
            strTipo: strTipo,
            strGrupo: strGrupo,
            strTipoConsulta: strTipoConsulta,
            strFechaInicio: strFechaInicio,
            strFechaFin: strFechaFin
        },
        url: strUrlCargar,
        success: function(response)
        {
            Ext.MessageBox.hide();
            var objJson = Ext.JSON.decode(response.responseText);
            if(objJson.boolSuccess === false)
            {
                Ext.MessageBox.hide();
                Ext.Msg.alert('Alerta', objJson.strMensajeError);
            }
            document.getElementById('strModalBody').innerHTML = objJson.strBodyModal;
        },
        failure: function(response)
        {            
            Ext.MessageBox.hide();
            Ext.Msg.alert('Alerta', 'No se pudo consultar la información requerida.');
            document.getElementById('strModalBody').innerHTML = '<div class="row">' +
                                                                '  <div class="col-lg-2">&nbsp;</div>' +
                                                                '  <div class="col-lg-8">No se ha encontrado ' + strTipo + '.</div>' +
                                                                '  <div class="col-lg-2">&nbsp;</div>' +
                                                                '</div>';
        }
    });
}
/**
 * verDetalleFact
 *
 * Función encargada de exportar a excel el detalle de facturación MRC o NRC
 *
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 24-04-2018
 *
 * @param string $strTipo - Tipo de facturación
 *
 *
 */
function exportarDetalleFact(strTipo)
{
    var strUrlCargar   = strUrlDetalladoFacturacion;
    var strFechaInicio = document.getElementById('strFechaInicio').value;
    var strFechaFin    = document.getElementById('strFechaFin').value;
    var strConsulta    = "";
    if(strTipo == 'MRC')
    {
        strConsulta = 'DETALLADO_EXCEL';
    }else if(strTipo == 'NRC')
    {
        strConsulta = 'DETALLADO_EXCEL';
    }else if(strTipo == 'NRC TRIMESTRAL')
    {
        strConsulta = 'DETALLADO_EXCELT';
    }

    Ext.MessageBox.wait("Generando el reporte de ventas...");
    Ext.Ajax.request
    ({
        method: 'post',
        dataType: 'json',
        timeout: 9000000,
        params :
        {
            strTipo: strTipo,
            strTipoConsulta: strConsulta,
            strFechaInicio: strFechaInicio,
            strFechaFin: strFechaFin
        },
        url: strUrlCargar,
        success: function(response)
        {
            Ext.MessageBox.hide();

            var objJson = Ext.JSON.decode(response.responseText);

            //Ext.Msg.alert('Alerta', objJson.strMensajeRespuesta);
            if(objJson.boolSuccess == false)
            {
                Ext.MessageBox.hide();
                Ext.Msg.alert('Alerta', objJson.strMensajeError);
            }
            else
            {
                Ext.MessageBox.hide();
                Ext.Msg.alert('Alerta', 'Se genero el reporte solicitado y fue enviado a su correo');
            }
            //document.getElementById('strModalBody').innerHTML = objJson.strBodyModal;
        },
        failure: function(response)
        {
            Ext.MessageBox.hide();
            Ext.Msg.alert('Alerta', 'No se pudo generar el reporte solicitado.');
        }
    });
}

/**
 * verDetalleFact
 *
 * Función encargada de exportar a excel el detalle de facturación MRC o NRC
 *
 * @author Kevin Baque Puya <kbaque@telconet.ec>
 * @version 1.0 17-10-2018
 *
 * @param string $strTipo         - Tipo de facturación
 * @param string $strTipoConsulta - Tipo de consulta 
 *
 */
function exportarCumplimiento(strTipo,strTipoConsulta)
{
    var strUrlCargar   = strUrlDetalladoResultadosVentas;
    var strFechaInicio = document.getElementById('strFechaInicio').value;
    var strFechaFin    = document.getElementById('strFechaFin').value;

    Ext.MessageBox.wait("Generando el reporte de cumplimiento y el detallado de facturacion...");
    Ext.Ajax.request
    ({
        method: 'post',
        dataType: 'json',
        timeout: 9000000,
        params :
        {
            strTipo: strTipo,
            strTipoConsulta: strTipoConsulta,
            strFechaInicio: strFechaInicio,
            strFechaFin: strFechaFin
        },
        url: strUrlCargar,
        success: function(response)
        {
            Ext.MessageBox.hide();

            var objJson = Ext.JSON.decode(response.responseText);

            if(!objJson.boolSuccess)
            {
                Ext.MessageBox.hide();
                Ext.Msg.alert('Alerta', objJson.strMensajeError);
            }
            else
            {
                Ext.MessageBox.hide();
                Ext.Msg.alert('Alerta', 'Se genero el reporte solicitado y fue enviado a su correo');
            }
        },
        failure: function(response)
        {
            Ext.MessageBox.hide();
            Ext.Msg.alert('Alerta', 'No se pudo generar el reporte solicitado.');
        }
    });
}

function exportarDetalleVentas(strTipo)
{
    var strUrlCargar   = '';
    var strAccion      = 'EXPORTAR';
    var strCategoria   = document.getElementById('strCategoriaSelected').value;
    var strGrupo       = document.getElementById('strGrupoSelected').value;
    var strFechaInicio = document.getElementById('strFechaInicio').value;
    var strFechaFin    = document.getElementById('strFechaFin').value;
    
    if( strTipo == "ORDENES_ACTIVAS" )
    {
        strUrlCargar = strUrlDetalladoVentas;
        strTipo      = 'VENTAS_ACTIVAS';
    }
    else if( strTipo == "ORDENES_CANCELADAS" )
    {
        var strFechaInicioSelected  = strFechaInicio.replace(/-/g, "/") + " 00:00:00";
        var dateFechaInicioSelected = new Date(strFechaInicioSelected);
        strFechaInicio              = "01-JAN-" + dateFechaInicioSelected.getFullYear();
        strFechaFin                 = "01-JAN-" + ( dateFechaInicioSelected.getFullYear() + 1 );
        strUrlCargar                = strUrlDetalladoVentas;
        strTipo                     = 'CLIENTES_CANCELADOS';
    }
    else
    {
        strUrlCargar = strUrlDetalladoVentas;
        strTipo      = 'VENTAS_NO_CONCRETADAS';
    }
                
    Ext.MessageBox.wait("Generando el reporte de ventas...");
    Ext.Ajax.request
    ({
        method: 'post',
        dataType: 'json',
        timeout: 9000000,
        params :
        {
            strCategoria: strCategoria,
            strGrupo : strGrupo,
            strTipo: strTipo,
            strFechaInicio: strFechaInicio,
            strFechaFin: strFechaFin,
            strAccion: strAccion
        },
        url: strUrlCargar,
        success: function(response)
        {
            Ext.MessageBox.hide();

            var objJson = Ext.JSON.decode(response.responseText);

            Ext.Msg.alert('Alerta', objJson.strMensajeRespuesta);
        },
        failure: function(response)
        {
            Ext.MessageBox.hide();
            Ext.Msg.alert('Alerta', 'No se pudo generar el reporte solicitado.');
        }
    });
}


function cargarGraficosBarChart(strIdVentasEmpleado, strTituloGrafico)
{
    Ext.MessageBox.wait("Cargando la información...");
    Ext.Ajax.request
    ({
        method: 'post',
        dataType: 'json',
        timeout: 9000000,
        params :
        {
            strTipoVendedor: strIdVentasEmpleado,
            strFechaInicio: document.getElementById('strFechaInicio').value,
            strFechaFin: document.getElementById('strFechaFin').value
        },
        url: strUrlGetInformacionVendedores,
        success: function(response)
        {
            Ext.MessageBox.hide();

            var objJson = Ext.JSON.decode(response.responseText);

            if(objJson.boolSuccess == true)
            {
                var arrayCategories            = [];
                var jsonData                   = null;
                var arrayDataMeta              = [];
                var arrayDataVendido           = [];
                var arrayVendedores            = objJson.arrayVendedores;
                var arrayVendido               = objJson.arrayVendido;
                var arrayMetas                 = objJson.arrayMetas;
                var arrayDataVendidoCategoria1 = [];
                var arrayDataMetasCategoria1   = [];
                var arrayDataVendidoCategoria2 = [];
                var arrayDataMetasCategoria2   = [];
                var arrayDataVendidoCategoria3 = [];
                var arrayDataMetasCategoria3   = [];
                var arrayVentasCat             = objJson.arrayVentasCat;
                
                $.each(arrayVendedores, function(key, value)
                {
                    arrayCategories.push(value);
                });
                
                $.each(arrayVendido, function(key, value)
                {
                    arrayDataVendido.push(value);
                });
                
                $.each(arrayMetas, function(key, value)
                {
                    arrayDataMeta.push(value);
                });
                
                $.each(arrayVentasCat, function(key, value)
                {
                    arrayCategories.push(key);
                    
                    var arrayValues = value;
                    
                    $.each(arrayValues, function(key, value)
                    {
                        var arrayValuesCategoria = value;
                        
                        if( key == "CATEGORIA_1" )
                        {
                            $.each(arrayValuesCategoria, function(key, value)
                            {
                                if( key == 'floatVendido' )
                                {
                                    arrayDataVendidoCategoria1.push(value);
                                }
                                else if( key == 'floatMeta' )
                                {
                                    arrayDataMetasCategoria1.push(value);
                                }
                            });
                        }
                        else if( key == "CATEGORIA_2" )
                        {
                            $.each(arrayValuesCategoria, function(key, value)
                            {
                                if( key == 'floatVendido' )
                                {
                                    arrayDataVendidoCategoria2.push(value);
                                }
                                else if( key == 'floatMeta' )
                                {
                                    arrayDataMetasCategoria2.push(value);
                                }
                            });
                        }
                        else
                        {
                            $.each(arrayValuesCategoria, function(key, value)
                            {
                                if( key == 'floatVendido' )
                                {
                                    arrayDataVendidoCategoria3.push(value);
                                }
                                else if( key == 'floatMeta' )
                                {
                                    arrayDataMetasCategoria3.push(value);
                                }
                            });
                        }
                    });
                });
                
                if( strIdVentasEmpleado == "PROVINCIAS" && !Ext.isEmpty(arrayDataMeta) && !Ext.isEmpty(arrayDataVendido) )
                {
                    jsonData =
                    [
                        {
                            name: 'Meta',
                            color: 'rgba(248,161,63,1)',
                            data: arrayDataMeta,
                            tooltip:
                            {
                                valuePrefix: '$ '
                            },
                            pointPadding: 0.3,
                            pointPlacement: 0,
                            yAxis: 1
                        },
                        {
                            name: 'Vendido',
                            color: 'rgba(186,60,61,.9)',
                            data: arrayDataVendido,
                            tooltip:
                            {
                                valuePrefix: '$ '
                            },
                            pointPadding: 0.4,
                            pointPlacement: 0,
                            yAxis: 1
                        }
                    ];
                }//( strIdVentasEmpleado == "PROVINCIAS" && !Ext.isEmpty(objDataMeta) && !Ext.isEmpty(objDataVendido) )
                else if( strIdVentasEmpleado !== "PROVINCIAS" )
                {
                    jsonData =
                    [
                        {
                            name: 'Meta Categoría 1',
                            color: 'rgba(165,170,217,1)',
                            data: arrayDataMetasCategoria1,
                            pointPadding: 0.3,
                            pointPlacement: -0.3,
                            tooltip:
                            {
                                valuePrefix: '$ ',
                            }
                        },
                        {
                            name: 'Vendido Categoría 1',
                            color: 'rgba(126,86,134,.9)',
                            data: arrayDataVendidoCategoria1,
                            pointPadding: 0.4,
                            pointPlacement: -0.3,
                            tooltip:
                            {
                                valuePrefix: '$ ',
                            }
                        },
                        {
                            name: 'Meta Categoria 2',
                            color: 'rgba(248,161,63,1)',
                            data: arrayDataMetasCategoria2,
                            tooltip:
                            {
                                valuePrefix: '$ '
                            },
                            pointPadding: 0.3,
                            pointPlacement: 0,
                            yAxis: 1
                        },
                        {
                            name: 'Vendido Categoría 2',
                            color: 'rgba(186,60,61,.9)',
                            data: arrayDataVendidoCategoria2,
                            tooltip:
                            {
                                valuePrefix: '$ '
                            },
                            pointPadding: 0.4,
                            pointPlacement: 0,
                            yAxis: 1
                        },
                        {
                            name: 'Meta Categoria 3',
                            color: 'rgba(169, 245, 169, 1)',
                            data: arrayDataMetasCategoria3,
                            tooltip:
                            {
                                valuePrefix: '$ '
                            },
                            pointPadding: 0.3,
                            pointPlacement: 0.3
                        },
                        {
                            name: 'Vendido Categoría 3',
                            color: 'rgba(243, 247, 129, 9)',
                            data: arrayDataVendidoCategoria3,
                            tooltip:
                            {
                                valuePrefix: '$ '
                            },
                            pointPadding: 0.4,
                            pointPlacement: 0.3
                        }
                    ];
                }

                Highcharts.chart(strIdVentasEmpleado,
                {
                    chart:
                    {
                        type: 'column'
                    },
                    title:
                    {
                        text: 'Categoría: ' + strTituloGrafico
                    },
                    xAxis:
                    {
                        categories: arrayCategories
                    },
                    yAxis: 
                    [
                        {
                            min: 0,
                            title:
                            {
                                text: 'Ventas ($)'
                            }
                        },
                        {
                            title:
                            {
                                text: 'Ventas ($)'
                            },
                            opposite: true
                        }
                    ],
                    legend:
                    {
                        shadow: false
                    },
                    tooltip:
                    {
                        shared: true
                    },
                    plotOptions:
                    {
                        column:
                        {
                            grouping: false,
                            shadow: false,
                            borderWidth: 0
                        }
                    },
                    series: jsonData
                });
            }
            else
            {
                Ext.MessageBox.hide();
                Ext.Msg.alert('Alerta', objJson.strMensajeError);

                document.getElementById(strIdVentasEmpleado).innerHTML = '';
            }
        },
        failure: function(response)
        {
            Ext.MessageBox.hide();
            Ext.Msg.alert('Alerta', 'No se pudo consultar la información requerida.');

            document.getElementById(strIdVentasEmpleado).innerHTML = '';
        }
    });
}

function modalNrc(strTipo)
{
    strTiTuloM = strTipo+" Mensual";
    strTiTuloT = strTipo+" Trimestral";

    var formPanel = Ext.create('Ext.form.Panel', {
            bodyPadding: 3,
            waitMsgTarget: true,
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 80,
                msgTarget: 'side'
            },
            items: [
            
            {
                xtype: 'fieldset',
                title: '',
                defaultType: 'textfield',
                items: [
                    {
                        xtype: 'fieldset',
                        title: 'Selecione el Tipo',
                        defaultType: 'textfield',
                        defaults: {
                            width: 135,
                            align: 'center',
                            pack:'center',
                            bodyStyle:'padding: 10px',
                        },
                        items: [
                                {
                                    xtype:'button',
                                    text: strTiTuloM,
                                    id: 'mensual',
                                    handler: function(){ 
                                        if(strTipo=='Detalle'){
                                            verDetalleFact('NRC','DETALLADO'); 
                                            $("#modalVerMas").modal('show');
                                            win.destroy();
                                        }
                                        else
                                        {
                                            exportarDetalleFact('NRC');
                                            win.destroy();
                                        }
                                        
                                    }
                                },
                                {
                                    xtype:'button',
                                    text: strTiTuloT,
                                    handler: function(){ 
                                        if(strTipo=='Detalle'){
                                            verDetalleFact('NRC','DETALLADO_TRIMESTRAL'); 
                                            $("#modalVerMas").modal('show');
                                            win.destroy();
                                        }
                                        else
                                        {
                                            exportarDetalleFact('NRC TRIMESTRAL');
                                            win.destroy();
                                        }
                                        
                                    }
                                }

                                ]
                    }

                ]
            }
            ],
            buttons: [
                {
                    text: 'Cerrar',
                    handler: function(){
                        win.destroy();
                    }
                }]
        });
    var win = Ext.create('Ext.window.Window', {
            title: 'Seleccione la Forma',
            modal: true,
            closable: true,
            layout: 'fit',
            width: 340,
            items: [formPanel]
        }).show();
        
        win.center();

}

function modalCumplimientoMrc(strTipo)
{
    var strTiTulo   = "Total";
    var strTiTuloID = "Internet/Datos";
    var strTiTuloBS = "Business Solution";

    var formPanelCump = Ext.create('Ext.form.Panel', {
            bodyPadding: 3,
            waitMsgTarget: true,
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 80,
                msgTarget: 'side'
            },
            items: [
            
            {
                xtype: 'fieldset',
                title: '',
                defaultType: 'textfield',
                items: [
                    {
                        xtype: 'fieldset',
                        title: 'Selecione el '+strTipo,
                        defaultType: 'textfield',
                        defaults: {
                            width: 135,
                            align: 'center',
                            pack:'center',
                            bodyStyle:'padding: 10px',
                        },
                        items: [
                                {
                                    xtype:'button',
                                    text: strTiTulo,
                                    id: 'mensual',
                                    handler: function(){ 
                                        if(strTipo=='Detalle'){
                                            verDetalleResultadosVentas('MRC','OTROS'); 
                                            $("#modalVerMas").modal('show');
                                            win2.destroy();
                                        }
                                        else
                                        {
                                            exportarCumplimiento('MRC','EXPORTAR_MRC');
                                        }
                                        win2.destroy();
                                    }
                                },
                                {
                                    xtype:'button',
                                    text: strTiTuloID,
                                    handler: function(){ 
                                        if(strTipo=='Detalle'){
                                            verDetalleResultadosVentas('MRC','ID'); 
                                            $("#modalVerMas").modal('show');
                                            win2.destroy();
                                        }
                                        else
                                        {
                                            exportarCumplimiento('MRC','EXPORTAR_MRCID');
                                        }
                                        win2.destroy();
                                    }
                                },
                                {
                                    xtype:'button',
                                    text: strTiTuloBS,
                                    handler: function(){ 
                                        if(strTipo=='Detalle'){
                                            verDetalleResultadosVentas('MRC','BS'); 
                                            $("#modalVerMas").modal('show');
                                            win2.destroy();
                                        }
                                        else
                                        {
                                            exportarCumplimiento('MRC','EXPORTAR_MRCBS');
                                        }
                                        win2.destroy();
                                    }
                                }

                                ]
                    }

                ]
            }
            ],
            buttons: [
                {
                    text: 'Cerrar',
                    handler: function(){
                        win2.destroy();
                    }
                }]
        });
    var win2 = Ext.create('Ext.window.Window', {
            title: 'Seleccione la Forma',
            modal: true,
            closable: true,
            layout: 'fit',
            width: 340,
            items: [formPanelCump]
        }).show();
        
        win2.center();

}