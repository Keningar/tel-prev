function cancelacionAnticipada(data) {
    var subtotal = 0, subtotalpromo = 0, subtotalgeneral = 0, subtotalgeneralnc = 0;
    var descuento = 0, equipos = 0, instalacion = 0;
    var valorProductoAdicional = 0, subtotalnc = 0;
    var valorDescto = 0, subtotalNDI = 0;
    var porcentDescto = 0, valorInstalacion = 0, porcentInstalacion = 0;
    var descuentoAdicional = 0, caracteristicas = '', equiposSeleccionados='';
    var strFacturaCancelacion = 'S';
    var activarActaCancelacion     = '';
    var activarActaCancelInternet = '';
    var activarActaCancelProdAdicional = '';
    var codigoPlantillaCancelacion = '';
    var codigoPlantillaCancelInternet = '';
    var codigoPlantillaCancelProdAdicional = '';
    var productoCancelar           = '';
    var arrayProdLigadosCliente    = new Array();
    var idServicioDescuento        = 0;
    var arrayIdServPorcentajeDesc  = new Array();
    var idServicioPorcentaje       = 0;
    var boolRealizaCalculoGeneral  = false;
    var strFacturaValoresCV        = 'N';
    var SumTotalEquipos            = 0;
    var SumDescPromo               = 0;
    var SumDescAdicionalPromo      = 0;
    var SumInstalacion             = 0;
    var arrayProductosFacturar     = new Array();
    var arrayGeneralProdFacturar   = new Array();
    var arrayDescuentosFacturar    = new Array();
    var arrayGeneralDescuentos     = new Array();
    var descPromo                  = 0;
    var descPromoAdicional         = 0;
    var porDescInstNC              = 0;
    var porDescPromoNC             = 0;
    var strCreaNC                  = 'N';
    var strGeneralCaract           = '';
    var strGeneralEquiposSelect    = '';
    var idServicioClienteRG;
    var objRespuestaValores;
    var boolErrorValoresFact       = false;
    var arrayIdServicioDesc;
    var tipoSubtotalGeneral;
    var counterFactNc;
    var idServicioClienteSG;
    var idServicioEquipo;

    /**
     * Obtiene Parametros de solicitud de acta de cancelacion
     * @author Ivan Romero <icromero@telconet.ec>
     * @version 1.0 30-12-2022
     * @since 1.0
     */
    Ext.Ajax.request({
        url: urlGetParametrosCancelacion,
        method: 'post',
        timeout: 99999,
        async: false,
        success: function (response) {
            var objParametros = Ext.JSON.decode(response.responseText).encontrados;
            for (var i = 0; i < objParametros.length; i++)
            {
                if (objParametros[i].descripcion == "ActivarActaCancelacion")
                {
                    activarActaCancelInternet = objParametros[i].valor1;
                }
                if (objParametros[i].descripcion == "codigoPlantillaCancelacion")
                {
                    codigoPlantillaCancelInternet = objParametros[i].valor1;
                }
                if (objParametros[i].descripcion == "ActivarActaCancelacionProdAdicional")
                {
                    activarActaCancelProdAdicional = objParametros[i].valor1;
                }
                if (objParametros[i].descripcion == "codigoPlantillaCancelacionProdAdicional")
                {
                    codigoPlantillaCancelProdAdicional = objParametros[i].valor1;
                }
            }
        },
        failure: function (response)
        {
            Ext.Msg.alert('Error ', 'Error: ' + response.statusText);
        }
    });
    

    /**
     * Obtiene Subtotal de Nota de Credito enviando como parametro el idServicio
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 02-09-2022
     * @since 1.0
     */
    function calcularSubtotalNc(idServicioPorcentaje)
    {
        valorDescto        = 0;
        porcentDescto      = 0;
        valorInstalacion   = 0;
        porcentInstalacion = 0;

        if (Ext.getCmp('tpdescuentos' + idServicioPorcentaje).getValue() && Ext.getCmp('tpdescuentos' + idServicioPorcentaje).getValue() > 0)
        {
            porcentDescto = Ext.getCmp('tpdescuentos' + idServicioPorcentaje).getValue();
            valorDescto   = Ext.getCmp('tvdescuentos' + idServicioPorcentaje).getValue();
        }

        if (Ext.getCmp('tpinstalacion' + idServicioPorcentaje).getValue() && Ext.getCmp('tpinstalacion' + idServicioPorcentaje).getValue() > 0)
        {
            valorInstalacion   = Ext.getCmp('tvinstalacion' + idServicioPorcentaje).getValue();
            porcentInstalacion = Ext.getCmp('tpinstalacion' + idServicioPorcentaje).getValue();
        }

        subtotalnc = (((valorDescto * porcentDescto) / 100) + ((valorInstalacion * porcentInstalacion) / 100));
        subtotalnc = Math.round(subtotalnc * 100) / 100;
        subtotalnc = parseFloat(subtotalnc.toFixed(2));

        Ext.getCmp('subtotalnc' + idServicioPorcentaje).setValue(subtotalnc);
        Ext.getCmp('lbsubtotalnc' + idServicioPorcentaje).setText(subtotalnc.toFixed(2));
    }

    function calcularSubtotalGeneralFactNc(tipoSubtotalGeneral)
    {

        if (tipoSubtotalGeneral === "FACT")
        {
            subtotalgeneral = 0;
        }
        else
        {
            subtotalgeneralnc = 0;
        }

        for (counterFactNc = 0; counterFactNc < arrayProdLigadosCliente.length; counterFactNc++)
        {
            idServicioClienteSG = arrayProdLigadosCliente[counterFactNc].idServicio;

            if (tipoSubtotalGeneral === "FACT")
            {
                subtotalgeneral = subtotalgeneral + parseFloat(Ext.getCmp('subtotal' + idServicioClienteSG).getValue());
            }
            else
            {
                subtotalgeneralnc = subtotalgeneralnc + parseFloat(Ext.getCmp('subtotalnc' + idServicioClienteSG).getValue());
            }
        }

        Ext.getCmp('subtotalgeneral').setValue(subtotalgeneral.toFixed(2));
        Ext.getCmp('lbsubtotalgeneral').setText(subtotalgeneral.toFixed(2));

        Ext.getCmp('subtotalgeneralnc').setValue(subtotalgeneralnc.toFixed(2));
        Ext.getCmp('lbsubtotalgeneralnc').setText(subtotalgeneralnc.toFixed(2));

    }


    var rdFactCancelSi = new Ext.form.Radio({
        boxLabel: 'Si',
        name: 'rgFacturaCancel',
        width: '5px',
        inputValue: 'S',
        checked: true,
        padding: '5 10 5 2'
    });
    var rdFactCancelNo = new Ext.form.Radio({
        boxLabel: 'No',
        name: 'rgFacturaCancel',
        inputValue: 'N',
        checked: false,
        padding: '5 5 5 10',
    });
    var rdFacturaCancelacion = new Ext.form.RadioGroup({
        fieldLabel: '¿Se debe facturar?',
        labelWidth: '130px',
        padding: '5 5 5 15',
        columns: 3,
        items: [rdFactCancelSi, rdFactCancelNo],
        listeners: {
            change: function (field, newValue) {
                switch (newValue['rgFacturaCancel']) {
                    case 'S':
                        for (var counterRG_S = 0; counterRG_S < arrayProdLigadosCliente.length; counterRG_S++)
                        {
                            idServicioClienteRG = arrayProdLigadosCliente[counterRG_S].idServicio;
                            strFacturaCancelacion = 'S';

                            equipos                = parseFloat(Ext.getCmp('equipos' + idServicioClienteRG).getValue());
                            descuento              = parseFloat(Ext.getCmp('descuento' + idServicioClienteRG).getValue());
                            valorProductoAdicional = parseFloat(Ext.getCmp('valorProductoAdicional' + idServicioClienteRG).getValue());
                            instalacion            = parseFloat(Ext.getCmp('instalacion' + idServicioClienteRG).getValue());
                            descuentoAdicional     = parseFloat(Ext.getCmp('descuentoAdicional' + idServicioClienteRG).getValue());

                            subtotal = equipos + descuento + valorProductoAdicional + instalacion + descuentoAdicional;
                            subtotal = Math.round(subtotal * 100) / 100;

                            subtotalpromo = descuento + descuentoAdicional;
                            subtotalpromo = Math.round(subtotalpromo * 100) / 100;

                            Ext.getCmp('gridEquiposFacturar' + idServicioClienteRG).enable();
                            if (Ext.getCmp('instalacion' + idServicioClienteRG).getValue() > 0)
                            {
                                Ext.getCmp('ncinstalacion' + idServicioClienteRG).enable();
                            }

                            if (Ext.getCmp('descuento' + idServicioClienteRG).getValue() > 0)
                            {
                                Ext.getCmp('ncdescuentos' + idServicioClienteRG).enable();
                            }


                            if (strFacturaValoresCV === 'N' && arrayProdLigadosCliente[counterRG_S].nombreProducto.toUpperCase() !== 'INTERNET')
                            {
                                Ext.getCmp('tvdcto.adicional' + idServicioClienteRG).disable();

                            }
                            else
                            {
                                Ext.getCmp('tvdcto.adicional' + idServicioClienteRG).enable();
                            }

                            Ext.getCmp('motivo').disable();
                            Ext.getCmp('observacion').disable();
                            Ext.getCmp('observacion').setValue('');
                            Ext.getCmp('motivo').setValue('');
                            Ext.getCmp('subtotal' + idServicioClienteRG).setValue(subtotal);
                            Ext.getCmp('subtotalpromo' + idServicioClienteRG).setValue(subtotalpromo);
                            Ext.getCmp('tvdescuentos' + idServicioClienteRG).setValue(
                                                        Ext.getCmp('descuento' + idServicioClienteRG).getValue());
                            Ext.getCmp('tvdcto.adicional' + idServicioClienteRG).setValue(
                                                            Ext.getCmp('descuentoAdicional' + idServicioClienteRG).getValue());
                            Ext.getCmp('tvinstalacion' + idServicioClienteRG).setValue(
                                                         Ext.getCmp('instalacion' + idServicioClienteRG).getValue());

                            if (Ext.getCmp('lbpromonetlifecam' + idServicioClienteRG) !== undefined)
                            {
                                Ext.getCmp('lbpromonetlifecam' + idServicioClienteRG).setText(
                                                                 Ext.getCmp('valorProductoAdicional' + idServicioClienteRG).getValue());
                                Ext.getCmp('tvpromonetlifecam' + idServicioClienteRG).setValue(valorProductoAdicional);
                            } 
                            else if (Ext.getCmp('lbnetlifeassistance' + idServicioClienteRG) !== undefined)
                            {
                                Ext.getCmp('lbnetlifeassistance' + idServicioClienteRG).setText(
                                                                   Ext.getCmp('valorProductoAdicional' + idServicioClienteRG).getValue());
                                Ext.getCmp('tvnetlifeassistance' + idServicioClienteRG).setValue(valorProductoAdicional);
                            }
                            else if (Ext.getCmp('lbnetlifecloud' + idServicioClienteRG) !== undefined)
                            {
                                Ext.getCmp('lbnetlifecloud' + idServicioClienteRG).setText(
                                                              Ext.getCmp('valorProductoAdicional' + idServicioClienteRG).getValue());
                                Ext.getCmp('tvnetlifecloud' + idServicioClienteRG).setValue(valorProductoAdicional);
                            }
                            else if (Ext.getCmp('lbelcanaldelfutbol' + idServicioClienteRG) !== undefined)
                            {
                                Ext.getCmp('lbelcanaldelfutbol' + idServicioClienteRG).setText(
                                                                  Ext.getCmp('valorProductoAdicional' + idServicioClienteRG).getValue());
                                Ext.getCmp('tvelcanaldelfutbol' + idServicioClienteRG).setValue(valorProductoAdicional);
                            }

                            Ext.getCmp('lbinstalacion' + idServicioClienteRG).setText(Ext.getCmp('instalacion' + idServicioClienteRG).getValue());
                            Ext.getCmp('lbdescuentos' + idServicioClienteRG).setText(Ext.getCmp('descuento' + idServicioClienteRG).getValue());
                            Ext.getCmp('lbsubtotal' + idServicioClienteRG).setText(subtotal.toFixed(2));
                            Ext.getCmp('lbsubtotalpromo' + idServicioClienteRG).setText(subtotalpromo.toFixed(2));

                            calcularSubtotalGeneralFactNc('FACT');
                            calcularSubtotalGeneralFactNc('NC');
                        }

                        break;
                    case 'N':
                        for (var counterRG_N = 0; counterRG_N < arrayProdLigadosCliente.length; counterRG_N++)
                        {
                            idServicioClienteRG = arrayProdLigadosCliente[counterRG_N].idServicio;
                            strFacturaCancelacion = 'N';
                            subtotal = 0;
                            equipos = 0;
                            Ext.getCmp('gridEquiposFacturar' + idServicioClienteRG).disable();
                            Ext.getCmp('gridEquiposFacturar' + idServicioClienteRG).getSelectionModel().deselectAll();
                            Ext.getCmp('tpdescuentos' + idServicioClienteRG).setValue(0);
                            Ext.getCmp('tpinstalacion' + idServicioClienteRG).setValue(0);
                            Ext.getCmp('motivo').enable();
                            Ext.getCmp('observacion').enable();
                            Ext.getCmp('tpdescuentos' + idServicioClienteRG).disable();
                            Ext.getCmp('tpinstalacion' + idServicioClienteRG).disable();
                            Ext.getCmp('ncdescuentos' + idServicioClienteRG).disable();
                            Ext.getCmp('ncinstalacion' + idServicioClienteRG).disable();
                            Ext.getCmp('tvequipos' + idServicioClienteRG).setValue(0);
                            Ext.getCmp('subtotal' + idServicioClienteRG).setValue(0);
                            Ext.getCmp('subtotalpromo' + idServicioClienteRG).setValue(0);
                            Ext.getCmp('subtotalgeneral').setValue(0);
                            Ext.getCmp('subtotalgeneralnc').setValue(0);
                            Ext.getCmp('tvdescuentos' + idServicioClienteRG).setValue(0);
                            Ext.getCmp('tvinstalacion' + idServicioClienteRG).setValue(0);

                            if (Ext.getCmp('lbpromonetlifecam' + idServicioClienteRG) !== undefined)
                            {
                                Ext.getCmp('lbpromonetlifecam' + idServicioClienteRG).setText('0.00');
                                Ext.getCmp('tvpromonetlifecam' + idServicioClienteRG).setValue(0);
                            } 
                            else if (Ext.getCmp('lbnetlifeassistance' + idServicioClienteRG) !== undefined)
                            {
                                Ext.getCmp('lbnetlifeassistance' + idServicioClienteRG).setText('0.00');
                                Ext.getCmp('tvnetlifeassistance' + idServicioClienteRG).setValue(0);
                            }
                            else if (Ext.getCmp('lbnetlifecloud' + idServicioClienteRG) !== undefined)
                            {
                                Ext.getCmp('lbnetlifecloud' + idServicioClienteRG).setText('0.00');
                                Ext.getCmp('tvnetlifecloud' + idServicioClienteRG).setValue(0);
                            }
                            else if (Ext.getCmp('lbelcanaldelfutbol' + idServicioClienteRG) !== undefined)
                            {
                                Ext.getCmp('lbelcanaldelfutbol' + idServicioClienteRG).setText('0.00');
                                Ext.getCmp('tvelcanaldelfutbol' + idServicioClienteRG).setValue(0);
                            }

                            Ext.getCmp('tvequipos' + idServicioClienteRG).setValue(0);
                            Ext.getCmp('tvdcto.adicional' + idServicioClienteRG).setValue(0);
                            Ext.getCmp('tvdcto.adicional' + idServicioClienteRG).disable();
                            Ext.getCmp('caracteristicas' + idServicioClienteRG).setValue('');
                            Ext.getCmp('equiposSeleccionados' + idServicioClienteRG).setValue('');
                            Ext.getCmp('lbequipos' + idServicioClienteRG).setText('0.00');
                            Ext.getCmp('equipos' + idServicioClienteRG).setValue(0);
                            Ext.getCmp('lbinstalacion' + idServicioClienteRG).setText('0.00');
                            Ext.getCmp('lbdescuentos' + idServicioClienteRG).setText('0.00');
                            Ext.getCmp('lbsubtotal' + idServicioClienteRG).setText('0.00');
                            Ext.getCmp('lbsubtotalpromo' + idServicioClienteRG).setText('0.00');
                            Ext.getCmp('lbsubtotalgeneral').setText('0.00');
                            Ext.getCmp('lbsubtotalgeneralnc').setText('0.00');
                            Ext.getCmp('lbsubtotalnc' + idServicioClienteRG).setText('0.00');
                        }

                        break;
                    default:
                        strFacturaCancelacion = 'S';
                }
            }
        }
    });

    /**
     * Obtiene los productos ligados al cliente y la fecha de activacion.
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 22-07-2022
     * @since 1.0
     */
    Ext.Ajax.request({
        url: urlGetPlanProductosClienteCV,
        method: 'post',
        timeout: 99999,
        async: true,
        params: {
            idServicio: data.idServicio,
            descripcionProducto: data.descripcionProducto
        },
        success: function (data) {

            var arrayPlanProdCliente = Ext.JSON.decode(data.responseText).planProductosClienteCV;

            for (var i = 0; i < arrayPlanProdCliente.length; i++)
            {
                
                var idServicioCliente = arrayPlanProdCliente[i].idServicio;

                Ext.create('Ext.form.Text',
                    {
                        xtype: 'textfield',
                        hidden: true,
                        fieldLabel: 'Caracteristicas',
                        name: 'caracteristicas' + arrayPlanProdCliente[i].idServicio,
                        id: 'caracteristicas' + arrayPlanProdCliente[i].idServicio,
                        value: caracteristicas,
                        allowBlank: false,
                        readOnly: true
                    });
                    
                Ext.create('Ext.form.Text',
                    {
                        xtype: 'textfield',
                        hidden: true,
                        fieldLabel: 'EquiposSeleccionados',
                        name: 'equiposSeleccionados' + arrayPlanProdCliente[i].idServicio,
                        id: 'equiposSeleccionados' + arrayPlanProdCliente[i].idServicio,
                        value: equiposSeleccionados,
                        allowBlank: false,
                        readOnly: true
                    });

                Ext.create('Ext.form.Text',
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Vigencia ' + arrayPlanProdCliente[i].nombreProducto.toUpperCase(),
                        name: 'feActivacion' + arrayPlanProdCliente[i].idServicio,
                        id: 'feActivacion' + arrayPlanProdCliente[i].idServicio,
                        value: arrayPlanProdCliente[i].fechaActivacion,
                        fieldStyle: 'border:none 0px',
                        allowBlank: false,
                        readOnly: true,
                        labelWidth: '160px',
                        width: '80px',
                        padding: '5 5 5 5',
                        labelAlign: 'left'
                    });

                containerFechaVigencia.add(Ext.getCmp('feActivacion' + idServicioCliente));

                Ext.define('EquiposFacturar', {
                    extend: 'Ext.data.Model',
                    fields: [
                        {name: 'id', mapping: 'id'},
                        {name: 'descripcion', mapping: 'descripcion'},
                        {name: 'tecnologia', mapping: 'tecnologia'},
                        {name: 'precio', type: 'float'},
                        {name: 'cantidad', type: 'int'},
                        {name: 'idServicio', type: 'idServicio'}
                    ]
                });

                new Ext.data.Store({
                    pageSize: 50,
                    autoLoad: true,
                    id: 'storeEquipos' + idServicioCliente,
                    proxy: {
                        type: 'ajax',
                        url: urlGetEquiposFacturar,
                        reader: {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'equipos'
                        },
                        extraParams: {
                            idServicio: idServicioCliente,
                            descripcionProducto: arrayPlanProdCliente[i].nombreProducto.toUpperCase()
                        }
                    },
                    fields:
                        [
                            {name: 'id', mapping: 'id'},
                            {name: 'descripcion', mapping: 'descripcion'},
                            {name: 'tecnologia', mapping: 'tecnologia'},
                            {name: 'precio', type: 'precio'},
                            {name: 'cantidad', type: 'cantidad'},
                            {name: 'idServicio', type: 'idServicio'}
                        ]
                });

                Ext.create('Ext.grid.Panel',
                    {
                        id: 'gridEquiposFacturar' + idServicioCliente,
                        store: 'storeEquipos' + idServicioCliente,
                        columnLines: true,
                        multiSelect: true,
                        viewConfig: {
                            emptyText: 'No existen equipos asociados al Producto',
                            deferEmptyText: false 
                        },
                        selModel: Ext.create('Ext.selection.CheckboxModel', {
                            checkOnly: 'true',
                            allowDeselect: true,
                            listeners: {
                                selectionchange: function (selectionModel, selected) {

                                    var arrayCaracteristicas = new Array();
                                    var arrayEquiposSeleccionados = new Array();
                                    tipoSubtotalGeneral = "FACT";
                                    caracteristicas = '';
                                    equiposSeleccionados = '';
                                    subtotal = 0;
                                    equipos = 0;
                                    

                                    if (selected.length > 0)
                                    {
                                        Ext.each(selected, function (record) {

                                            if (record.data.idServicio !== undefined || record.data.idServicio !== '')
                                            {
                                                idServicioEquipo = record.data.idServicio;
                                            }

                                            arrayCaracteristicas = new Array();
                                            equipos += parseFloat(record.data.precio * record.data.cantidad);
                                            arrayCaracteristicas.push(parseInt(record.data.id)+'/'+record.data.cantidad+'/'+record.data.precio);
                                            Ext.getCmp('equipos' + idServicioEquipo).setValue(equipos);
                                            Ext.getCmp('tvequipos' + idServicioEquipo).setValue(equipos);
                                            arrayCaracteristicas.forEach(function (caracteristica) {
                                                caracteristicas += caracteristica + '-';
                                            });
                                            
                                            arrayEquiposSeleccionados = new Array();
                                            arrayEquiposSeleccionados.push(record.data.descripcion);
                                            arrayEquiposSeleccionados.forEach(function (equipoSeleccionado) {
                                                equiposSeleccionados += equipoSeleccionado + ',';
                                            });

                                        });

                                        equipos = Math.round(equipos * 100) / 100;
                                        equipos = parseFloat(equipos.toFixed(2));
                                    }
                                    else
                                    {
                                        var arrayIdServicioSelect = (selectionModel.store.storeId).split("storeEquipos");
                                        idServicioEquipo      = arrayIdServicioSelect[1];
                                        equipos = 0;

                                    }

                                    caracteristicas = caracteristicas.substring(0, caracteristicas.length - 1);
                                    Ext.getCmp('caracteristicas' + idServicioEquipo).setValue(caracteristicas);
                                    
                                    equiposSeleccionados = equiposSeleccionados.substring(0, equiposSeleccionados.length - 1);
                                    Ext.getCmp('equiposSeleccionados' + idServicioEquipo).setValue(equiposSeleccionados);

                                    Ext.getCmp('equipos' + idServicioEquipo).setValue(equipos);
                                    Ext.getCmp('tvequipos' + idServicioEquipo).setValue(equipos);

                                    descuento = parseFloat(Ext.getCmp('descuento' + idServicioEquipo).getValue());
                                    valorProductoAdicional = parseFloat(Ext.getCmp('valorProductoAdicional' + idServicioEquipo).getValue());
                                    instalacion = parseFloat(Ext.getCmp('instalacion' + idServicioEquipo).getValue());
                                    descuentoAdicional = parseFloat(Ext.getCmp('tvdcto.adicional' + idServicioEquipo).getValue());
                                    if (isNaN(descuentoAdicional) || !descuentoAdicional)
                                    {
                                        descuentoAdicional = 0;
                                    }

                                    subtotal = equipos + descuento + valorProductoAdicional + instalacion + descuentoAdicional;
                                    subtotal = Math.round(subtotal * 100) / 100;

                                    subtotalpromo = descuento + descuentoAdicional;
                                    subtotalpromo = Math.round(subtotalpromo * 100) / 100;

                                    Ext.getCmp('subtotal' + idServicioEquipo).setValue(subtotal);
                                    Ext.getCmp('subtotalpromo' + idServicioEquipo).setValue(subtotalpromo);
                                    Ext.getCmp('lbequipos' + idServicioEquipo).setText(equipos.toFixed(2));
                                    Ext.getCmp('lbsubtotal' + idServicioEquipo).setText(subtotal.toFixed(2));
                                    Ext.getCmp('lbsubtotalpromo' + idServicioEquipo).setText(subtotalpromo.toFixed(2));

                                    if (boolRealizaCalculoGeneral === true)
                                    {
                                        calcularSubtotalGeneralFactNc(tipoSubtotalGeneral);
                                    }
                                }
                            }
                        }),
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
                                            autoHide: false,
                                            renderTo: Ext.getBody(),
                                            listeners:
                                                {
                                                    beforeshow: function (tip)
                                                    {
                                                        if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1)
                                                        {
                                                            header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                                            if (header.dataIndex != null)
                                                            {
                                                                var trigger = tip.triggerElement,
                                                                    parent = tip.triggerElement.parentElement,
                                                                    columnDataIndex = view.getHeaderByCell(trigger).dataIndex;
                                                                if (view.getRecord(parent).get(columnDataIndex) != null)
                                                                {
                                                                    var columnText = view.getRecord(parent).get(columnDataIndex).toString();
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
                                                            else
                                                            {
                                                                return false;
                                                            }
                                                        }
                                                    }
                                                }
                                        });
                                    grid.tip.on('show', function ()
                                    {
                                        var timeout;

                                        grid.tip.getEl().on('mouseout', function ()
                                        {
                                            timeout = window.setTimeout(function () {
                                                grid.tip.hide();
                                            }, 500);
                                        });

                                        grid.tip.getEl().on('mouseover', function () {
                                            window.clearTimeout(timeout);
                                        });

                                        Ext.get(view.el).on('mouseover', function () {
                                            window.clearTimeout(timeout);
                                        });

                                        Ext.get(view.el).on('mouseout', function ()
                                        {
                                            timeout = window.setTimeout(function () {
                                                grid.tip.hide();
                                            }, 500);
                                        });
                                    });
                                }
                            },
                        columns:
                            [

                                {
                                    text: 'Id',
                                    width: 10,
                                    dataIndex: 'id',
                                    hidden: true
                                }, {
                                    text: 'Descripcion.',
                                    width: 220,
                                    dataIndex: 'descripcion'
                                }, {
                                    text: 'Tecnologia',
                                    width: 100,
                                    dataIndex: 'tecnologia'
                                }, {
                                    text: 'Precio Unitario',
                                    width: 100,
                                    dataIndex: 'precio'
                                }, {
                                    text: 'Cantidad',
                                    width: 100,
                                    dataIndex: 'cantidad',
                                    editor: {
                                        allowBlank: false, disabled: true, id: 'cantidadEquipo'
                                    }
                                }, {
                                    text: 'IdServicio',
                                    width: 10,
                                    dataIndex: 'idServicio',
                                    hidden: true
                                }
                            ],
                        plugins: [Ext.create('Ext.grid.plugin.CellEditing', {
                                clicksToEdit: 1,
                                listeners: {
                                    beforeedit: function ()
                                    {
                                        if (undefined !== Ext.getCmp('cantidadEquipo'))
                                        {
                                            Ext.getCmp('cantidadEquipo').setDisabled(false);
                                        }
                                    },
                                    edit: function (editor, e)
                                    {
                                        if (e.field === 'cantidad')
                                        {
                                            equipos = 0;
                                        }
                                    }
                                }
                            })],
                    });
                    
                var strTitlePanelEquipo = '';

                if (arrayPlanProdCliente[i].nombreProducto == 'INTERNET')
                {
                    strTitlePanelEquipo = 'El cliente tiene contratado el servicio de ' +
                                               arrayPlanProdCliente[i].nombreProducto + '. Revisar entrega de equipos. ';
                } 
                else
                {
                    strTitlePanelEquipo = 'El cliente tiene contratado ' + 
                                               arrayPlanProdCliente[i].nombreProducto + ' como producto adicional. Revisar entrega de equipos. ';
                }

                new Ext.Panel({title: strTitlePanelEquipo, padding: '0 0 5 0', id: 'panelTitle' + idServicioCliente});

                formPanelEquiposFacturar.add(Ext.getCmp('panelTitle' + idServicioCliente));
                formPanelEquiposFacturar.add(Ext.getCmp('gridEquiposFacturar' + idServicioCliente));

            }
        },
        error: function () {
            alert('Existe un error. Comuniquese con el Deparamento de Sistemas');
        }
    });


    var containerFechaVigencia = Ext.create("Ext.container.Container", {
        autoheight: true,
        columns: 2,
        padding: 5,
        layout: {
            type: 'table',
            columns: 2,
            tableAttrs: {
                style: {
                    width: '100%'
                }
            }
        }
    });


    Ext.define('modelMotivos', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id', type: 'string'},
            {name: 'descripcion', type: 'string'}
        ]
    });

    var storeMotivosNc = Ext.create('Ext.data.Store', {
        autoLoad: false,
        model: "modelMotivos",
        proxy: {
            type: 'ajax',
            url: urlGetMotivosNc,
            reader: {
                type: 'json',
                root: 'documentos'
            }
        }
    });

    var cmbMotivosNc = Ext.create('Ext.form.ComboBox', {
        xtype: 'combo',
        id: 'motivo',
        name: 'motivo',
        fieldLabel: 'Motivo',
        labelWidth: '100px',
        hiddenName: 'motivos',
        emptyText: 'Seleccione el motivo...',
        store: storeMotivosNc,
        displayField: 'descripcion',
        valueField: 'id',
        width: 300,
        padding: '10 5 10 5',
        disabled: true,
    });

    var txtAreaObs = Ext.create('Ext.form.TextArea', {
        id: 'observacion',
        name: 'observacion',
        fieldLabel: 'Observaci\u00f3n',
        labelWidth: '100px',
        width: 400,
        height: 40,
        padding: '10 5 5 5',
        disabled: true
    });

    Ext.Ajax.request({
        url: urlGetParamFactDetallada,
        method: 'post',
        timeout: 99999,
        success: function (response) {

            var objRespuesta = Ext.JSON.decode(response.responseText);
            Ext.getCmp('myform').disable();
            var alert = Ext.Msg.alert('Cancelaci\u00f3n Voluntaria ',
                                      'Estimado usuario, la ventana se habilitara cuando se cargue completamente la informaci\u00f3n! ');

            Ext.Ajax.request({
                url: urlGetPlanProductosClienteCV,
                method: 'post',
                timeout: 99999,
                async: true,
                params: {
                    idServicio: data.idServicio,
                    descripcionProducto: data.descripcionProducto
                },
                success: function (data) {

                    new Ext.form.Label(
                        {
                            xtype: 'label',
                            id: 'lbtsubtotalgeneral',
                            name: 'lbtsubtotalgeneral',
                            text: 'Subtotal General Fact',
                            width: 180,
                            padding: '10 13 10 15',
                            height: 50,
                            left: 5,
                            style: 'font-weight:bold;',
                            border: true
                        });

                    new Ext.form.Label(
                        {
                            xtype: 'label',
                            id: 'lbsubtotalgeneral',
                            name: 'lbsubtotalgeneral',
                            text: '',
                            width: 52.5,
                            padding: '10 10 10 13',
                            height: 35,
                            left: 190,
                            border: true,
                            style: {
                                color: 'black',
                                textAlign: 'left'
                            }
                        });

                    var txtSubtotalGeneral = Ext.create('Ext.form.Text',
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Subtotal General Fact',
                            name: 'subtotalgeneral',
                            id: 'subtotalgeneral',
                            value: subtotalgeneral,
                            allowBlank: false,
                            readOnly: true,
                            hidden: true,
                            width: '200px',
                            labelWidth: '100px',
                            bodyStyle: 'margin: 20px;',
                            labelStyle: 'font-weight:bold;',
                            padding: '15 10 15 6',
                            labelAlign: 'left'
                        });

                    new Ext.form.Label(
                        {
                            xtype: 'label',
                            id: 'lbtsubtotalgeneralnc',
                            name: 'lbtsubtotalgeneralnc',
                            text: 'Subtotal General NC',
                            width: 180,
                            padding: '10 13 10 15',
                            height: 50,
                            left: 5,
                            style: 'font-weight:bold;',
                            border: true
                        });

                    new Ext.form.Label(
                        {
                            xtype: 'label',
                            id: 'lbsubtotalgeneralnc',
                            name: 'lbsubtotalgeneralnc',
                            text: '',
                            width: 52.5,
                            padding: '10 10 10 13',
                            height: 35,
                            left: 190,
                            border: true,
                            style: {
                                color: 'black',
                                textAlign: 'left'
                            }
                        });

                    var txtSubtotalGeneralNC = Ext.create('Ext.form.Text',
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Subtotal General NC',
                            name: 'subtotalgeneralnc',
                            id: 'subtotalgeneralnc',
                            value: subtotalgeneralnc,
                            allowBlank: false,
                            readOnly: true,
                            hidden: true,
                            width: '200px',
                            labelWidth: '100px',
                            bodyStyle: 'margin: 20px;',
                            labelStyle: 'font-weight:bold;',
                            padding: '15 10 15 6',
                            labelAlign: 'left'
                        });
                        
                    var strTitleValoresFacturar = "";    
                    arrayProdLigadosCliente = Ext.JSON.decode(data.responseText).planProductosClienteCV;

                    for (var counterProd = 0; counterProd < arrayProdLigadosCliente.length; counterProd++)
                    {
                        var idServicioCliente = arrayProdLigadosCliente[counterProd].idServicio;
                        var nombreProducto = arrayProdLigadosCliente[counterProd].nombreProducto.toUpperCase();

                        if (nombreProducto == 'INTERNET')
                        {
                            strTitleValoresFacturar = 'Valores para facturar del Servicio ' + nombreProducto;
                        }
                        else
                        {
                            strTitleValoresFacturar = 'Valores para facturar del Producto ' + nombreProducto;
                        }

                        Ext.Ajax.request({
                            url: urlGetSaldoPorVencerNDI,
                            method: 'post',
                            timeout: 99999,
                            async: false,
                            params: {
                                idServicio: idServicioCliente
                            },
                            success: function (response) {
                                var objRespuesta = Ext.JSON.decode(response.responseText);
                                subtotalNDI = parseFloat(objRespuesta.totalNDI);
                            },
                            failure: function (response)
                            {
                                Ext.Msg.alert('Error ', 'Error: ' + response.statusText);
                            }
                        });

                        Ext.Ajax.request({
                            url: urlGetVigenciaPromocion,
                            method: 'post',
                            timeout: 99999,
                            async: false,
                            params: {
                                idServicio: idServicioCliente
                            },
                            success: function (response) {
                                var objRespuesta = Ext.JSON.decode(response.responseText);
                                strFacturaValoresCV = objRespuesta.strFacturaValoresCV;     
                            },
                            failure: function (response)
                            {   
                                Ext.Msg.alert('Error ', 'Error: ' + response.statusText);
                            }
                        });


                        Ext.Ajax.request({
                            url: urlGetValoresFacturar,
                            method: 'post',
                            timeout: 99999,
                            async: false,
                            params: {
                                idServicio: idServicioCliente,
                                descripcionProducto: nombreProducto
                            },
                            success: function (response) 
                            {
                                objRespuestaValores = Ext.JSON.decode(response.responseText);
                                boolErrorValoresFact = false;
                            },
                            failure: function (response)
                            {      
                                Ext.Msg.alert('Error ', 'Error: ' + response.statusText);
                                boolErrorValoresFact = true;
                            }
                        });
                        
                        if(!boolErrorValoresFact)
                        {    
                            valorProductoAdicional = parseFloat(objRespuestaValores.totalValorProducto);
                            instalacion = parseFloat(objRespuestaValores.totalInstalacion);

                            if (strFacturaValoresCV === 'N' && nombreProducto !== 'INTERNET')
                            {
                                descuentoAdicional = 0;
                                descuento = 0;
                            }
                            else
                            {
                                descuentoAdicional = parseFloat(objRespuestaValores.totalDctoAdicional);
                                descuento = parseFloat(objRespuestaValores.totalDctos);
                            }

                            subtotal = equipos + descuento + valorProductoAdicional + instalacion + descuentoAdicional;
                            subtotal = Math.round(subtotal * 100) / 100;

                            subtotalpromo = descuento + descuentoAdicional;
                            subtotalpromo = Math.round(subtotalpromo * 100) / 100;
                            productoCancelar = objRespuestaValores.productoCancelar;

                            Ext.create('Ext.form.Text',
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Equipos',
                                    name: 'equipos' + idServicioCliente,
                                    id: 'equipos' + idServicioCliente,
                                    value: '0',
                                    hidden: true,
                                    readOnly: true
                                });

                            Ext.create('Ext.form.Text',
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Descuento Adicional',
                                    name: 'descuentoAdicional' + idServicioCliente,
                                    id: 'descuentoAdicional' + idServicioCliente,
                                    value: descuentoAdicional,
                                    hidden: true,
                                    readOnly: true
                                });

                            Ext.create('Ext.form.Text',
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Descuento',
                                    name: 'descuento' + idServicioCliente,
                                    id: 'descuento' + idServicioCliente,
                                    value: descuento,
                                    hidden: true,
                                    readOnly: true
                                });

                            Ext.create('Ext.form.Text',
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Instalacion',
                                    name: 'instalacion' + idServicioCliente,
                                    id: 'instalacion' + idServicioCliente,
                                    value: instalacion,
                                    hidden: true,
                                    readOnly: true
                                });

                            Ext.create('Ext.form.Text',
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Valor Producto Adicional',
                                    name: 'valorProductoAdicional' + idServicioCliente,
                                    id: 'valorProductoAdicional' + idServicioCliente,
                                    value: valorProductoAdicional,
                                    hidden: true,
                                    readOnly: true
                                });

                            Ext.create('Ext.form.Text',
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Subtotal Fact',
                                    name: 'subtotal' + idServicioCliente,
                                    id: 'subtotal' + idServicioCliente,
                                    value: subtotal,
                                    allowBlank: false,
                                    readOnly: true,
                                    hidden: true,
                                    width: '200px',
                                    labelWidth: '100px',
                                    bodyStyle: 'margin: 20px;',
                                    labelStyle: 'font-weight:bold;',
                                    padding: '15 10 15 6',
                                    labelAlign: 'left'
                                });

                            Ext.create('Ext.form.Text',
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Subtotal Promo ' + nombreProducto,
                                    name: 'subtotalpromo' + idServicioCliente,
                                    id: 'subtotalpromo' + idServicioCliente,
                                    value: subtotalpromo,
                                    allowBlank: false,
                                    readOnly: true,
                                    hidden: true,
                                    width: '200px',
                                    labelWidth: '100px',
                                    bodyStyle: 'margin: 20px;',
                                    labelStyle: 'font-weight:bold;',
                                    padding: '15 10 15 6',
                                    labelAlign: 'left'
                                });

                            Ext.create('Ext.form.Text',
                                {
                                    fieldLabel: 'Valor IdServicio',
                                    name: 'idServicio_ncdescuentos' + idServicioCliente,
                                    id: 'idServicio_ncdescuentos' + idServicioCliente,
                                    value: idServicioCliente,
                                    hidden: true,
                                    disabled: true,
                                    width: '200px',
                                    labelWidth: '95px',
                                    bodyStyle: 'margin: 20px;',
                                    labelStyle: 'font-weight:bold;',
                                    padding: '15 20 15 3',
                                    labelAlign: 'left'
                                });

                            Ext.create('Ext.form.Text',
                                {
                                    fieldLabel: 'Valor IdServicio',
                                    name: 'idServicio_ncinstalacion' + idServicioCliente,
                                    id: 'idServicio_ncinstalacion' + idServicioCliente,
                                    value: idServicioCliente,
                                    hidden: true,
                                    disabled: true,
                                    width: '200px',
                                    labelWidth: '95px',
                                    bodyStyle: 'margin: 20px;',
                                    labelStyle: 'font-weight:bold;',
                                    padding: '15 20 15 3',
                                    labelAlign: 'left'
                                });

                            Ext.create('Ext.form.Text',
                                {
                                    fieldLabel: 'Valor IdServicio Desc.',
                                    name: 'idServicio_tvdcto.adicional' + idServicioCliente,
                                    id: 'idServicio_tvdcto.adicional' + idServicioCliente,
                                    value: idServicioCliente,
                                    hidden: true,
                                    disabled: true,
                                    width: '200px',
                                    labelWidth: '95px',
                                    bodyStyle: 'margin: 20px;',
                                    labelStyle: 'font-weight:bold;',
                                    padding: '15 20 15 3',
                                    labelAlign: 'left'
                                });

                            Ext.create('Ext.form.Panel', {
                                id: 'formPanelValoresFacturar' + idServicioCliente,
                                name: 'formPanelValoresFacturar' + idServicioCliente,
                                bodyPadding: 4,
                                height: 350,
                                items: [
                                    {
                                        xtype: 'fieldset',
                                        title: strTitleValoresFacturar,
                                        padding: '5 5 10 5',
                                        width: 530,
                                        defaults: {height: 35}

                                    }
                                ]
                            });

                            Ext.create('Ext.form.Text',
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'CUOTAS POR VENCER',
                                    name: 'subtotalNDI' + idServicioCliente,
                                    id: 'subtotalNDI' + idServicioCliente,
                                    value: subtotalNDI,
                                    allowBlank: false,
                                    readOnly: true,
                                    hidden: true,
                                    width: '180px',
                                    labelWidth: '95px',
                                    bodyStyle: 'margin: 20px;',
                                    labelStyle: 'font-weight:bold;',
                                    padding: '15 20 15 3',
                                    labelAlign: 'left'
                                });

                            new Ext.form.Label(
                                {
                                    xtype: 'label',
                                    id: 'lbtsubtotalNDI' + idServicioCliente,
                                    name: 'lbtsubtotalNDI' + idServicioCliente,
                                    text: 'CUOTAS POR VENCER',
                                    width: 180,
                                    padding: '5 13 10 15',
                                    height: 30,
                                    style: 'font-weight:bold;',
                                    border: true,
                                    hidden: true
                                });

                            new Ext.form.Label(
                                {
                                    xtype: 'label',
                                    id: 'lbsubtotalNDI' + idServicioCliente,
                                    name: 'lbsubtotalNDI' + idServicioCliente,
                                    text: '',
                                    width: 100,
                                    padding: '5 10 10 13',
                                    height: 30,
                                    border: true,
                                    hidden: true,
                                    style: {
                                        color: 'black',
                                        textAlign: 'left'
                                    }
                                });

                            new Ext.container.Container({
                                layout: 'hbox',
                                id: 'contSubtotalNDI' + idServicioCliente,
                                height: 30,
                                padding: '5 10 5 10',
                                items: [Ext.getCmp('subtotalNDI' + idServicioCliente),
                                        Ext.getCmp('lbtsubtotalNDI' + idServicioCliente),
                                        Ext.getCmp('lbsubtotalNDI' + idServicioCliente)]
                            });

                            if (subtotalNDI == 0)
                            {
                                Ext.getCmp('lbsubtotalNDI' + idServicioCliente).hidden = false;
                                Ext.getCmp('lbtsubtotalNDI' + idServicioCliente).hidden = false;
                            }

                            Ext.create('Ext.form.Text',
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Subtotal NC',
                                    name: 'subtotalnc' + idServicioCliente,
                                    id: 'subtotalnc' + idServicioCliente,
                                    value: subtotalnc,
                                    allowBlank: false,
                                    readOnly: true,
                                    hidden: true,
                                    width: '200px',
                                    labelWidth: '95px',
                                    bodyStyle: 'margin: 20px;',
                                    labelStyle: 'font-weight:bold;',
                                    padding: '15 20 15 3',
                                    labelAlign: 'left'
                                });

                            new Ext.form.Label(
                                {
                                    xtype: 'label',
                                    id: 'lbtsubtotal' + idServicioCliente,
                                    name: 'lbtsubtotal' + idServicioCliente,
                                    text: 'Subtotal Fact',
                                    width: 180,
                                    padding: '0 13 10 15',
                                    height: 35,
                                    style: 'font-weight:bold;',
                                    border: true
                                });

                            new Ext.form.Label(
                                {
                                    xtype: 'label',
                                    id: 'lbsubtotal' + idServicioCliente,
                                    name: 'lbsubtotal' + idServicioCliente,
                                    text: '',
                                    width: 52.5,
                                    padding: '0 10 10 13',
                                    height: 35,
                                    left: 180,
                                    border: true,
                                    style: {
                                        color: 'black',
                                        textAlign: 'left'
                                    }
                                });

                            new Ext.form.Label(
                                {
                                    xtype: 'label',
                                    id: 'lbtsubtotalpromo' + idServicioCliente,
                                    name: 'lbtsubtotalpromo' + idServicioCliente,
                                    text: 'Subtotal Promo ' + nombreProducto,
                                    width: 180,
                                    padding: '10 13 10 15',
                                    height: 50,
                                    style: 'font-weight:bold;',
                                    border: true
                                });

                            new Ext.form.Label(
                                {
                                    xtype: 'label',
                                    id: 'lbsubtotalpromo' + idServicioCliente,
                                    name: 'lbsubtotalpromo' + idServicioCliente,
                                    text: '',
                                    width: 52.5,
                                    padding: '10 10 10 13',
                                    height: 35,
                                    left: 180,
                                    border: true,
                                    style: {
                                        color: 'black',
                                        textAlign: 'left'
                                    }
                                });

                            new Ext.form.Label(
                                {
                                    xtype: 'label',
                                    id: 'lbtsubtotalnc' + idServicioCliente,
                                    name: 'lbtsubtotalnc' + idServicioCliente,
                                    text: 'Subtotal NC',
                                    width: 100,
                                    padding: '10 5 10 20',
                                    height: 30,
                                    left: 310,
                                    border: true,
                                    style: 'font-weight:bold;'
                                });

                            new Ext.form.Label(
                                {
                                    xtype: 'label',
                                    id: 'lbsubtotalnc' + idServicioCliente,
                                    name: 'lbsubtotalnc' + idServicioCliente,
                                    text: '',
                                    width: 52.5,
                                    padding: '10 10 10 20',
                                    height: 35,
                                    left: 395,
                                    border: true,
                                    style: {
                                        color: 'black',
                                        textAlign: 'left'
                                    }
                                });

                            new Ext.container.Container({
                                layout: 'hbox',
                                id: 'contSubtotalPromo' + idServicioCliente,
                                height: 55,
                                padding: '5 10 5 10',
                                items: [Ext.getCmp('subtotalpromo' + idServicioCliente), 
                                        Ext.getCmp('lbtsubtotalpromo' + idServicioCliente), 
                                        Ext.getCmp('lbsubtotalpromo' + idServicioCliente), 
                                        Ext.getCmp('subtotalnc' + idServicioCliente), 
                                        Ext.getCmp('lbtsubtotalnc' + idServicioCliente), 
                                        Ext.getCmp('lbsubtotalnc' + idServicioCliente),
                                        Ext.getCmp('idServicio_tvdcto.adicional' + idServicioCliente)]
                            });

                            new Ext.container.Container({
                                layout: 'hbox',
                                id: 'contSubtotal' + idServicioCliente,
                                height: 35,
                                padding: '5 10 5 10',
                                items: [Ext.getCmp('subtotal' + idServicioCliente),
                                        Ext.getCmp('lbtsubtotal' + idServicioCliente),
                                        Ext.getCmp('lbsubtotal' + idServicioCliente)]
                            });

                            for (var i = 0; i < objRespuesta.parametros.length; i++)
                            {
                                var strDescripcion = objRespuesta.parametros[i].strDescripcion;
                                var strId = strDescripcion.toLowerCase();
                                var strElemento = strId.replace(/ /g, '');
                                var strGeneraNc = objRespuesta.parametros[i].strGeneraNc;
                                var strEditable = objRespuesta.parametros[i].strEditable;

                                if (strDescripcion === 'Dcto. Adicional')
                                {
                                    strDescripcion = 'Promo. Servicio Proporcional';
                                }
                                if (strDescripcion === 'Descuentos')
                                {
                                    strDescripcion = 'Promo.Servicio';
                                }

                                Ext.create('Ext.form.Text',
                                    {
                                        fieldLabel: strDescripcion,
                                        name: 'tv' + strElemento + idServicioCliente,
                                        id: 'tv' + strElemento + idServicioCliente,
                                        value: 0,
                                        hidden: true,
                                        width: '260px',
                                        labelWidth: '110px',
                                        labelStyle: 'white-space: nowrap;',
                                        bodyStyle: 'margin: 30px;',
                                        padding: '10 10 10 20',
                                        labelAlign: 'rigth',
                                        maskRe: /[0-9.]/,
                                        regex: /^[0-9]+(?:\.[0-9]+)?$/,
                                        regexText: 'Solo numeros',
                                        style: 'font-weight:bold;'
                                    });
                                if (strDescripcion === 'Promo. Servicio Proporcional')
                                {
                                    Ext.getCmp('tvdcto.adicional' + idServicioCliente).setValue(descuentoAdicional);
                                }

                                new Ext.form.Label(
                                    {
                                        xtype: 'label',
                                        id: 'lbt' + strElemento + idServicioCliente,
                                        name: 'lbt' + strElemento + idServicioCliente,
                                        text: strDescripcion,
                                        width: 110,
                                        padding: '20 20 20 20',
                                        height: 30,
                                        border: true,
                                        style: 'font-weight:bold;'
                                    });

                                new Ext.form.Label(
                                    {
                                        xtype: 'label',
                                        id: 'lb' + strElemento + idServicioCliente,
                                        name: 'lb' + strElemento + idServicioCliente,
                                        text: '',
                                        width: 52.5,
                                        padding: '20 20 20 20',
                                        height: 35,
                                        border: true,
                                        style: {
                                            labelStyle: 'font-weight:bold;',
                                            color: 'black',
                                            textAlign: 'left'
                                        }
                                    });

                                if (strGeneraNc === 'S') 
                                {

                                    Ext.create('Ext.form.Text',
                                        {
                                            id: 'tp' + strElemento + idServicioCliente,
                                            name: 'tp' + strElemento + idServicioCliente,
                                            fieldLabel: '%',
                                            width: '21%',
                                            bodyStyle: 'margin: 20px;',
                                            padding: '5 5 5 5',
                                            labelWidth: '20px',
                                            disabled: true,
                                            minValue: 1,
                                            maxValue: 50,
                                            maskRe: /[0-9.]/,
                                            regex: /^[0-9]+(?:\.[0-9]+)?$/,
                                            regexText: 'Solo numeros',
                                            validator: function (val) {
                                                var errMsg = "Porcentaje max es 50";
                                                return (val > 0 && val <= 50) ? true : errMsg;
                                            }
                                            
                                        });

                                    new Ext.form.Checkbox({
                                        fieldLabel: 'Nota de Cr\u00e9dito',
                                        id: 'nc' + strElemento + idServicioCliente,
                                        name: 'nc' + strElemento + idServicioCliente,
                                        bodyStyle: 'margin: 5px;',
                                        labelWidth: '110px',
                                        style: 'font-weight:bold;',
                                        padding: '10 20 10 30',
                                        labelAlign: 'left',
                                        listeners: {
                                            change: function (checkbox, newValue) {
                                                if (newValue)
                                                {
                                                    if (this.name === 'ncdescuentos' + Ext.getCmp('idServicio_' + this.name).getValue())
                                                    {
                                                        Ext.getCmp('tpdescuentos' + Ext.getCmp('idServicio_' + this.name).getValue()).enable();

                                                    }
                                                    else
                                                    {
                                                        Ext.getCmp('tpinstalacion' + Ext.getCmp('idServicio_' + this.name).getValue()).enable();
                                                    }
                                                } 
                                                else
                                                {
                                                    if (this.name === 'ncdescuentos' + Ext.getCmp('idServicio_' + this.name).getValue())
                                                    {
                                                        Ext.getCmp('tpdescuentos' +Ext.getCmp('idServicio_' + this.name).getValue()).disable();
                                                        Ext.getCmp('tpdescuentos' +Ext.getCmp('idServicio_' + this.name).getValue()).setValue(0);

                                                    }
                                                    else
                                                    {
                                                        Ext.getCmp('tpinstalacion'+Ext.getCmp('idServicio_' + this.name).getValue()).disable();
                                                        Ext.getCmp('tpinstalacion'+Ext.getCmp('idServicio_' + this.name).getValue()).setValue(0);
                                                    }
                                                }
                                            }
                                        }
                                    });
                                    if (strEditable === 'N')
                                    {
                                        new Ext.container.Container({
                                            layout: 'hbox',
                                            id: 'cont' + strElemento + idServicioCliente,
                                            height: 35,
                                            padding: '2 5 2 5',
                                            items: [Ext.getCmp('tv' + strElemento + idServicioCliente),
                                                    Ext.getCmp('lbt' + strElemento + idServicioCliente),
                                                    Ext.getCmp('lb' + strElemento + idServicioCliente),
                                                    Ext.getCmp('nc' + strElemento + idServicioCliente),
                                                    Ext.getCmp('tp' + strElemento + idServicioCliente),
                                                    Ext.getCmp('idServicio_ncdescuentos' + idServicioCliente)]

                                        });
                                    }
                                    else
                                    {
                                        Ext.getCmp('tv' + strElemento + idServicioCliente).hidden = false;

                                        new Ext.container.Container({
                                            layout: 'hbox',
                                            id: 'cont' + strElemento + idServicioCliente,
                                            height: 35,
                                            padding: '2 5 2 5',
                                            items: [Ext.getCmp('tv' + strElemento + idServicioCliente), 
                                                    Ext.getCmp('nc' + strElemento + idServicioCliente),
                                                    Ext.getCmp('tp' + strElemento + idServicioCliente)]
                                        });

                                    }
                                }
                                else
                                {
                                    if (strEditable === 'N')
                                    {
                                        new Ext.container.Container({
                                            layout: 'hbox',
                                            id: 'cont' + strElemento + idServicioCliente,
                                            height: 35,
                                            padding: '2 5 2 5',
                                            items: [Ext.getCmp('lbt' + strElemento + idServicioCliente),
                                                    Ext.getCmp('lb' + strElemento + idServicioCliente)]
                                        });

                                    } 
                                    else
                                    {
                                        Ext.getCmp('tv' + strElemento + idServicioCliente).hidden = false;
                                        new Ext.container.Container({
                                            layout: 'hbox',
                                            id: 'cont' + strElemento + idServicioCliente,
                                            height: 35,
                                            padding: '2 5 2 5',
                                            items: [Ext.getCmp('tv' + strElemento + idServicioCliente)]
                                        });
                                    }
                                }
                                if (((strElemento === 'netlifecloud' && productoCancelar.toUpperCase() === strElemento.toUpperCase()) ||
                                   (strElemento === 'netlifeassistance' && productoCancelar.toUpperCase() === strElemento.toUpperCase()) ||
                                   (strElemento === 'promonetlifecam' &&('promo' + productoCancelar).toUpperCase() === strElemento.toUpperCase()) || 
                                   (strElemento === 'elcanaldelfutbol' && productoCancelar.toUpperCase() === 'ECDF') ||
                                   (strElemento === 'i.protegidomultipaid' && productoCancelar.toUpperCase() === strElemento.toUpperCase()) ||
                                   (strElemento === 'ipfijaadicionalpyme' && productoCancelar.toUpperCase() === strElemento.toUpperCase()) ||
                                   (strElemento === 'ipfija' && productoCancelar.toUpperCase() === strElemento.toUpperCase()) ||
                                   (strElemento === 'cableadoethernet' && productoCancelar.toUpperCase() === strElemento.toUpperCase()) ||
                                   (strElemento === 'ecommercebasic' && productoCancelar.toUpperCase() === strElemento.toUpperCase()) ||
                                   (strElemento === 'netlifeassistancepro' && productoCancelar.toUpperCase() === strElemento.toUpperCase())||
                                   (strElemento === 'paramount+' && productoCancelar.toUpperCase() === strElemento.toUpperCase()))
                                   && (nombreProducto !== 'INTERNET'))
                                {

                                    Ext.getCmp('formPanelValoresFacturar'+idServicioCliente).add(Ext.getCmp('cont'+strElemento+idServicioCliente));

                                }
                                if (strElemento !== 'netlifecloud' &&  strElemento !== 'netlifeassistance' && 
                                    strElemento !== 'promonetlifecam' &&  strElemento !== 'elcanaldelfutbol' && 
                                    strElemento !== 'i.protegidomultipaid' &&  strElemento !== 'ipfijaadicionalpyme'&& 
                                    strElemento !== 'ipfija' &&  strElemento !== 'cableadoethernet' && 
                                    strElemento !== 'ecommercebasic' &&  strElemento !== 'netlifeassistancepro' &&
                                    strElemento !== 'paramount+')
                                {

                                    Ext.getCmp('formPanelValoresFacturar'+idServicioCliente).add(Ext.getCmp('cont'+strElemento+idServicioCliente));

                                }

                            }

                            Ext.getCmp('formPanelValoresFacturar' + idServicioCliente).add(Ext.getCmp('contSubtotalPromo' + idServicioCliente));
                            Ext.getCmp('formPanelValoresFacturar' + idServicioCliente).add(Ext.getCmp('contSubtotal' + idServicioCliente));

                            if (subtotalNDI == 0 && nombreProducto === 'INTERNET')
                            {
                                Ext.getCmp('formPanelValoresFacturar'+idServicioCliente).add(Ext.getCmp('contSubtotalNDI'+idServicioCliente));
                            }

                            Ext.getCmp('subtotal' + idServicioCliente).setValue(subtotal);
                            Ext.getCmp('subtotalpromo' + idServicioCliente).setValue(subtotalpromo);
                            Ext.getCmp('tvdescuentos' + idServicioCliente).setValue(descuento);
                            Ext.getCmp('lbdescuentos' + idServicioCliente).setText(descuento.toFixed(2));

                            Ext.getCmp('tvinstalacion' + idServicioCliente).setValue(instalacion);
                            Ext.getCmp('lbinstalacion' + idServicioCliente).setText(instalacion.toFixed(2));

                            Ext.getCmp('tvequipos' + idServicioCliente).setValue(equipos);
                            Ext.getCmp('lbequipos' + idServicioCliente).setText(equipos.toFixed(2));

                            if (('promo' + productoCancelar).toUpperCase() === 'PROMONETLIFECAM')
                            {
                                Ext.getCmp('lbpromonetlifecam' + idServicioCliente).setText(valorProductoAdicional.toFixed(2));
                                Ext.getCmp('tvpromonetlifecam' + idServicioCliente).setValue(valorProductoAdicional);

                                arrayProductosFacturar = {'idServicio': idServicioCliente, 
                                                          'nombreProducto': 'PROMONETLIFECAM', 
                                                          'valorFacturar': valorProductoAdicional};
                                arrayGeneralProdFacturar.push(arrayProductosFacturar);

                            } 
                            else if (productoCancelar.toUpperCase() === 'NETLIFEASSISTANCE')
                            {
                                Ext.getCmp('lbnetlifeassistance' + idServicioCliente).setText(valorProductoAdicional.toFixed(2));
                                Ext.getCmp('tvnetlifeassistance' + idServicioCliente).setValue(valorProductoAdicional);

                                arrayProductosFacturar = {'idServicio': idServicioCliente, 
                                                          'nombreProducto': 'NETLIFEASSISTANCE',
                                                          'valorFacturar': valorProductoAdicional};
                                arrayGeneralProdFacturar.push(arrayProductosFacturar);

                            } 
                            else if (productoCancelar.toUpperCase() === 'NETLIFECLOUD')
                            {
                                Ext.getCmp('lbnetlifecloud' + idServicioCliente).setText(valorProductoAdicional.toFixed(2));
                                Ext.getCmp('tvnetlifecloud' + idServicioCliente).setValue(valorProductoAdicional);

                                arrayProductosFacturar = {'idServicio': idServicioCliente,
                                                          'nombreProducto': 'NETLIFECLOUD',
                                                          'valorFacturar': valorProductoAdicional};
                                arrayGeneralProdFacturar.push(arrayProductosFacturar);

                            } 
                            else if (productoCancelar.toUpperCase() === 'ECDF')
                            {
                                Ext.getCmp('lbelcanaldelfutbol' + idServicioCliente).setText(valorProductoAdicional.toFixed(2));
                                Ext.getCmp('tvelcanaldelfutbol' + idServicioCliente).setValue(valorProductoAdicional);

                                arrayProductosFacturar = {'idServicio': idServicioCliente,
                                                          'nombreProducto': 'ECDF', 
                                                          'valorFacturar': valorProductoAdicional};
                                arrayGeneralProdFacturar.push(arrayProductosFacturar);

                            }

                            Ext.getCmp('lbsubtotal' + idServicioCliente).setText(subtotal.toFixed(2));
                            Ext.getCmp('lbsubtotalpromo' + idServicioCliente).setText(subtotalpromo.toFixed(2));
                            Ext.getCmp('lbsubtotalnc' + idServicioCliente).setText(subtotalnc.toFixed(2));
                            Ext.getCmp('lbsubtotalNDI' + idServicioCliente).setText(subtotalNDI.toFixed(2));

                            if (instalacion === 0)
                            {
                                Ext.getCmp('ncinstalacion' + idServicioCliente).disable();
                            }

                            if (descuento === 0)
                            {
                                Ext.getCmp('ncdescuentos' + idServicioCliente).disable();
                            }

                            if (productoCancelar.toUpperCase() === 'NETLIFECLOUD')
                            {
                                Ext.getCmp('ncinstalacion' + idServicioCliente).disable();
                                Ext.getCmp('ncdescuentos' + idServicioCliente).disable();
                            }

                            if (strFacturaValoresCV === 'N' && nombreProducto !== 'INTERNET')
                            {
                                Ext.getCmp('tvdcto.adicional' + idServicioCliente).disable();
                            } 
                            else
                            {
                                Ext.getCmp('tvdcto.adicional' + idServicioCliente).enable();
                            }

                            formPanelValoresFacturar.add(Ext.getCmp('formPanelValoresFacturar' + idServicioCliente));
                                
                        }


                        if (counterProd + 1 === arrayProdLigadosCliente.length) {
                            Ext.getCmp('myform').enable();
                            alert.close();
                            boolRealizaCalculoGeneral = true;

                        }

                        subtotalgeneral = subtotalgeneral + parseFloat(Ext.getCmp('subtotal' + idServicioCliente).getValue());
                        subtotalgeneralnc = subtotalgeneralnc + parseFloat(Ext.getCmp('subtotalnc' + idServicioCliente).getValue());

                    }

                    new Ext.container.Container({
                        layout: 'hbox',
                        id: 'contSubtotalGeneral',
                        height: 35,
                        padding: '5 10 5 10',
                        items: [txtSubtotalGeneral,
                                Ext.getCmp('lbtsubtotalgeneral'), 
                                Ext.getCmp('lbsubtotalgeneral'),
                                txtSubtotalGeneralNC,
                                Ext.getCmp('lbtsubtotalgeneralnc'),
                                Ext.getCmp('lbsubtotalgeneralnc')]
                    });


                    Ext.getCmp('subtotalgeneral').setValue(subtotalgeneral.toFixed(2));
                    Ext.getCmp('lbsubtotalgeneral').setText(subtotalgeneral.toFixed(2));

                    Ext.getCmp('subtotalgeneralnc').setValue(subtotalgeneralnc.toFixed(2));
                    Ext.getCmp('lbsubtotalgeneralnc').setText(subtotalgeneralnc.toFixed(2));

                    formPanelValoresFacturar.add(Ext.getCmp('contSubtotalGeneral'));
                    
                    for (var counterServ= 0; counterServ < arrayProdLigadosCliente.length; counterServ++)
                    {
                        var idServPorcentaje = arrayProdLigadosCliente[counterServ].idServicio;

                        Ext.getCmp('tpinstalacion'+idServPorcentaje).on('change', function(btn, e, eOpts) 
                        {
                            arrayIdServPorcentajeDesc = (this.name).split("tpinstalacion");
                            tipoSubtotalGeneral = "NC";

                            if (arrayIdServPorcentajeDesc[1] !== 0 && arrayIdServPorcentajeDesc[1] !== undefined)
                            {
                                idServicioPorcentaje = arrayIdServPorcentajeDesc[1];
                                calcularSubtotalNc(idServicioPorcentaje);
                            } 

                            if (boolRealizaCalculoGeneral === true)
                            {
                                calcularSubtotalGeneralFactNc(tipoSubtotalGeneral);
                            }

                        });

                        Ext.getCmp('tpdescuentos'+idServPorcentaje).on('change', function(btn, e, eOpts)  
                        {
                            arrayIdServPorcentajeDesc = (this.name).split("tpdescuentos");
                            tipoSubtotalGeneral = "NC";

                            if (arrayIdServPorcentajeDesc[1] !== 0 && arrayIdServPorcentajeDesc[1] !== undefined)
                            {
                                idServicioPorcentaje = arrayIdServPorcentajeDesc[1];
                                calcularSubtotalNc(idServicioPorcentaje);
                            } 

                            if (boolRealizaCalculoGeneral === true)
                            {
                                calcularSubtotalGeneralFactNc(tipoSubtotalGeneral);
                            }
                        });     
                        
                        
                        
                        Ext.getCmp('tvdcto.adicional'+idServPorcentaje).on('change', function(btn, e, eOpts)  
                        {                            
                            descuentoAdicional = 0;
                            arrayIdServicioDesc = (this.name).split("tvdcto.adicional");
                            tipoSubtotalGeneral = "FACT";

                            idServicioDescuento = arrayIdServicioDesc[1];

                            if (idServicioDescuento !== 0 && idServicioDescuento !== undefined)
                            {
                                descuentoAdicional = parseFloat(Ext.getCmp('tvdcto.adicional' +  idServicioDescuento).getValue());
                                if (isNaN(descuentoAdicional) || !descuentoAdicional)
                                {
                                    descuentoAdicional = 0;
                                }

                                equipos = parseFloat(Ext.getCmp('equipos' + idServicioDescuento).getValue());
                                descuento = parseFloat(Ext.getCmp('descuento' + idServicioDescuento).getValue());
                                valorProductoAdicional = parseFloat(Ext.getCmp('valorProductoAdicional' + idServicioDescuento).getValue());
                                instalacion = parseFloat(Ext.getCmp('instalacion' + idServicioDescuento).getValue());

                                subtotal = equipos +descuento +valorProductoAdicional +instalacion +descuentoAdicional;
                                subtotal = Math.round(subtotal * 100) / 100;

                                subtotalpromo = descuento + descuentoAdicional;
                                subtotalpromo = Math.round(subtotalpromo * 100) / 100;

                                Ext.getCmp('subtotal' + idServicioDescuento).setValue(subtotal);
                                Ext.getCmp('lbsubtotal' + idServicioDescuento).setText(subtotal.toFixed(2));

                                Ext.getCmp('subtotalpromo' + idServicioDescuento).setValue(subtotal);
                                Ext.getCmp('lbsubtotalpromo' + idServicioDescuento).setText(subtotalpromo.toFixed(2));

                                if (boolRealizaCalculoGeneral === true)
                                {
                                    calcularSubtotalGeneralFactNc(tipoSubtotalGeneral);
                                }

                            }
                                
                        });
                        
                    }

                },
                error: function () {
                    alert('Existe un error. Comuniquese con el Deparamento de Sistemas');
                }
            });
        },
        failure: function (response)
        {
            Ext.Msg.alert('Error ', 'Error: ' + response.statusText);
        }
    });


    var formPanelEquiposFacturar = Ext.create('Ext.form.Panel', {
        id: 'panelEquiposFacturar',
        bodyPadding: 4,
        autoHeight: true,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 50,
            msgTarget: 'side'
        }
    });

    var formPanelValoresFacturar = Ext.create('Ext.form.Panel', {
        id: 'myform2',
        bodyPadding: 4,
        autoHeight: true,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 50,
            msgTarget: 'side'
        }
    });

    var formPanel = Ext.create('Ext.form.Panel', {
        id: 'myform',
        width: 550,
        autoHeight: true,
        bodyPadding: 4,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 50,
            msgTarget: 'side'
        },
        layout: {
            type: 'accordion',
            titleCollapse: false,
            animate: false,
            activeOnTop: false
        },
        items: [
            {
                title: 'Información No Genera Factura',
                items: [

                    {width: '10%', border: false},
                    rdFacturaCancelacion,
                    {width: '10%', border: false},
                    {width: '10%', border: false},
                    containerFechaVigencia,
                    {width: '15%', border: false},
                    {width: '30%', border: false},
                    {width: '10%', border: false},

                    {width: '10%', border: false},
                    cmbMotivosNc,
                    {width: '10%', border: false},
                    {width: '10%', border: false},
                    txtAreaObs,
                    {width: '15%', border: false},
                    {width: '30%', border: false},
                    {width: '10%', border: false},
                ]
            },
            {
                title: 'Equipos',
                items: [formPanelEquiposFacturar]
            },
            {
                title: 'Valores a Facturar',
                items: [
                    formPanelValoresFacturar,
                ]
            }],
        buttons: [{
                text: 'Siguiente ->',
                handler: function () {
                    var boolContinuaFlujo = true, strMsj = '';
                    if (strFacturaCancelacion === 'N' && (cmbMotivosNc.getValue() === null || txtAreaObs.getValue() === ''))
                    {
                        boolContinuaFlujo = false;
                        if (cmbMotivosNc.getValue() === null) {
                            strMsj = 'Favor Escoja un Motivo';
                        } else if (txtAreaObs.getValue() === '') {
                            strMsj = 'Ingrese Observaci\u00f3n';
                        }
                        Ext.Msg.alert("Advertencia", strMsj, function () {
                            return false;
                        });
                    }
                    if (boolContinuaFlujo) {

                        for (var counterFlujo = 0; counterFlujo < arrayProdLigadosCliente.length; counterFlujo++)
                        {
                            var idServicioParam = arrayProdLigadosCliente[counterFlujo].idServicio;
                            var idProducto = arrayProdLigadosCliente[counterFlujo].idProducto;
                            var nombreProdParam = arrayProdLigadosCliente[counterFlujo].nombreProducto.toUpperCase();
                            var fechaActivacionProd = arrayProdLigadosCliente[counterFlujo].fechaActivacion;

                            SumTotalEquipos = SumTotalEquipos + parseFloat(Ext.getCmp('tvequipos' + idServicioParam).getValue());
                            SumDescPromo = SumDescPromo + parseFloat(Ext.getCmp('tvdescuentos' + idServicioParam).getValue());
                            SumDescAdicionalPromo = SumDescAdicionalPromo + parseFloat(Ext.getCmp('tvdcto.adicional' + idServicioParam).getValue());
                            SumInstalacion = SumInstalacion + parseFloat(Ext.getCmp('tvinstalacion' + idServicioParam).getValue());


                            if (nombreProdParam == 'INTERNET')
                            {

                                descPromo          = parseFloat(Ext.getCmp('tvdescuentos' + idServicioParam).getValue());
                                descPromoAdicional = Ext.getCmp('tvdcto.adicional' + idServicioParam).getValue() != "" ? 
                                                     parseFloat(Ext.getCmp('tvdcto.adicional' + idServicioParam).getValue()) : 0;
                                porDescInstNC      = Ext.getCmp('tpinstalacion' + idServicioParam).getValue() != "" ? 
                                                     parseFloat(Ext.getCmp('tpinstalacion' + idServicioParam).getValue()) : 0;
                                porDescPromoNC     = Ext.getCmp('tpdescuentos' + idServicioParam).getValue() != "" ?
                                                     parseFloat(Ext.getCmp('tpdescuentos' + idServicioParam).getValue()) : 0;

                            } 
                            else
                            {
                                descPromo          = parseFloat(Ext.getCmp('tvdescuentos' + idServicioParam).getValue());
                                descPromoAdicional = Ext.getCmp('tvdcto.adicional' + idServicioParam).getValue() != "" ? 
                                                     parseFloat(Ext.getCmp('tvdcto.adicional' + idServicioParam).getValue()) : 0;
                                porDescInstNC      = 0;
                                porDescPromoNC     = Ext.getCmp('tpdescuentos' + idServicioParam).getValue() != "" ?
                                                     parseFloat(Ext.getCmp('tpdescuentos' + idServicioParam).getValue()) : 0;
                            }

                            var subtotalNcGeneral   = Ext.getCmp('subtotalgeneralnc').getValue();
                            var subtotalFactGeneral = Ext.getCmp('subtotalgeneral').getValue();


                            arrayDescuentosFacturar = {"idServicio": idServicioParam,
                                                       "idProducto": idProducto, 
                                                       "nombreProducto": nombreProdParam,
                                                       "descPromo": descPromo,
                                                       "descPromoAdicional": descPromoAdicional,
                                                       "porDescInstNC": porDescInstNC, 
                                                       "porDescPromoNC": porDescPromoNC,
                                                       "fechaActivacionProd":fechaActivacionProd};

                            arrayGeneralDescuentos.push(arrayDescuentosFacturar);

                            strGeneralCaract = strGeneralCaract + '-' + Ext.getCmp('caracteristicas' + idServicioParam).getValue();
                            
                            strGeneralEquiposSelect = strGeneralEquiposSelect + ','+ Ext.getCmp('equiposSeleccionados' + idServicioParam).getValue();

                            if (((parseFloat(Ext.getCmp('tvinstalacion' + idServicioParam).getValue()) > 0 && porDescInstNC > 0) ||
                                 (descPromo > 0 && porDescPromoNC > 0) && strCreaNC === 'N'))
                            {
                                strCreaNC = 'S';
                            }
                        }

                        strGeneralCaract = strGeneralCaract.substring(1, strGeneralCaract.length);
                        strGeneralEquiposSelect = strGeneralEquiposSelect.substring(1, strGeneralEquiposSelect.length);
                        
                        // Se invoca pantalla de cancelación correspondiente                      
                        if (data.descripcionProducto === 'INTERNET')
                        {
                            activarActaCancelacion = activarActaCancelInternet;
                            codigoPlantillaCancelacion = codigoPlantillaCancelInternet;
                        } 
                        else
                        {
                            activarActaCancelacion = activarActaCancelProdAdicional;
                            codigoPlantillaCancelacion = codigoPlantillaCancelProdAdicional;
                        }

                        Ext.Ajax.request({
                            url: urlEjecutarCancelacionVoluntaria,
                            method: 'post',
                            timeout: 400000,
                            params: {},
                            success: function () {
                                win.destroy();
                                var motivo = cmbMotivosNc.getValue();
                                var observacion = txtAreaObs.getValue();
                                var arrayParametros = {
                                    "data": data,
                                    "subtotal": subtotalFactGeneral,
                                    "equipos": SumTotalEquipos,
                                    "arrayGeneralDescuentos": arrayGeneralDescuentos,
                                    "instalacion": SumInstalacion,
                                    "subtotalnc": subtotalNcGeneral,
                                    "strCreaNC": strCreaNC,
                                    "arrayGeneralProdFacturar": arrayGeneralProdFacturar,
                                    "caracteristicas": strGeneralCaract,
                                    "strFacturaCancelacion": strFacturaCancelacion,
                                    "motivoCancelacion": motivo,
                                    "observacion": observacion,
                                    "accion": '313',
                                    "subtotalNDI": 0,
                                    "activarActaCancelacion": activarActaCancelacion,
                                    "codigoPlantillaCancelacion": codigoPlantillaCancelacion,
                                    "equiposSeleccionados": strGeneralEquiposSelect
                                };

                                // Se invoca pantalla de cancelación correspondiente                      
                                if (data.descripcionProducto === 'INTERNET')
                                {
                                    cancelarServicioMdFacturacion(arrayParametros);
                                } 
                                else
                                {
                                    cancelarServicioProdAdicional(arrayParametros);
                                }
                            },
                            failure: function (result)
                            {
                                Ext.get(formPanel.getId()).unmask();
                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                            }
                        });
                    }

                },
            }, {
                text: 'Cerrar',
                handler: function () {
                    win.destroy();
                }
            }]
    });
    var win = Ext.create('Ext.window.Window', {
        title: 'Facturaci\u00f3n por Cancelaci\u00f3n Voluntaria',
        modal: true,
        width: 600,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

