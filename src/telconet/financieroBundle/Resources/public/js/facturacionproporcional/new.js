/* 
 * Se modifica la logica para editar, para el campo descripcion 
 * al momento de dar clip en el campo del grid.
 * 
 * @author      Jesus Banchen <jbanchen@telconet.ec>
 * @version     1.1     20-08-2019
 * 
 */

var boolFacturacionAgrupada         = false;
var permisoSeleccionarImpuesto      = null;
var boolPermisoSeleccionarImpuesto  = null;
var permisoPagaIce                  = null;
var boolPagaIce                     = null;
var strEsCompensado                 = null;
var permisoPuedeCompensar           = null;
var boolPuedeCompensar              = null;
var storeOficinasFacturacion        = null;
var strTipoFactura                  = '';


Ext.onReady(function ()
{
    permisoSeleccionarImpuesto      = $("#ROLE_69-4277");
    boolPermisoSeleccionarImpuesto  = (typeof permisoSeleccionarImpuesto === 'undefined') ? false 
                                      : (permisoSeleccionarImpuesto.val() == 1 ? true : false);
    permisoPagaIce                  = $("#ROLE_69-4297");
    boolPagaIce                     = (typeof permisoPagaIce === 'undefined') ? false : (permisoPagaIce.val() == 1 ? true : false);
    permisoPuedeCompensar           = $("#ROLE_67-4777");
    boolPuedeCompensar              = (typeof permisoPuedeCompensar === 'undefined') ? false : (permisoPuedeCompensar.val() == 1 ? true : false);
    permisoEditarPrecioFactDet      = $("#ROLE_69-5357");
    boolEditarPrecioFactDet         = (typeof permisoEditarPrecioFactDet === 'undefined') ? false 
                                      : (permisoEditarPrecioFactDet.val() == 1 ? true : false);     
    
    var storeNumeracionFacturacion = new Ext.data.Store
            ({
                total: 'total',
                autoLoad: true,
                proxy:
                        {
                            type: 'ajax',
                            method: 'post',
                            url: strUrlGetNumeracionesFacturacion,
                            timeout: 9000000,
                            reader:
                                    {
                                        type: 'json',
                                        totalProperty: 'total',
                                        root: 'encontrados'
                                    },
                            extraParams:
                                    {
                                        oficina: intIdOficina
                                    }
                        },
                fields:
                        [
                            {name: 'intIdNumeracion', mapping: 'intIdNumeracion'},
                            {name: 'strNumeracion', mapping: 'strNumeracion'}
                        ],
                listeners:
                        {
                            load: function(store, records)
                            {
                                Ext.getCmp('cmbNumeracionFacturacion').setValue(records[0].get('intIdNumeracion'));
                                getNumeroFactura(records[0].get('intIdNumeracion'));
                            }
                        }
            });
    storeOficinasFacturacion = Ext.create('Ext.data.Store',
            {
                storeId: "storeIdOficinaFact",
                autoLoad: true,
                proxy:
                        {
                            type: 'ajax',
                            method: 'post',
                            url: strUrlGetOficinasFacturacion,
                            timeout: 900000,
                            reader:
                                    {
                                        type: 'json',
                                        root: 'encontrados'
                                    }
                        },
                fields:
                        [
                            {name: 'intIdOficina', mapping: 'intIdOficina'},
                            {name: 'strNombreOficina', mapping: 'strNombreOficina'},
                            {name: 'strEsCompensado', mapping: 'strEsCompensado'}
                        ],
                listeners:
                        {
                            load: function(store, records)
                            {
                                Ext.each(records, function(record)
                                {
                                    if (record.get('intIdOficina') == intIdOficina)
                                    {
                                        Ext.getCmp('cmbOficinaFacturacion').setValue(record.get('intIdOficina'));
                                    }
                                });
                            }
                        }
            });
    var cmbNumeracionFacturacion = new Ext.form.ComboBox
            ({
                labelAlign: 'left',
                store: storeNumeracionFacturacion,
                id: 'cmbNumeracionFacturacion',
                name: 'cmbNumeracionFacturacion',
                valueField: 'intIdNumeracion',
                displayField: 'strNumeracion',
                fieldLabel: '',
                emptyText: 'Seleccione',
                width: 100,
                labelWidth: 110,
                labelPad: 10,
                queryMode: "local",
                listConfig:
                        {
                            listeners:
                                    {
                                        itemclick: function(list, record)
                                        {
                                            getNumeroFactura(record.get('intIdNumeracion'));
                                        }
                                    }
                        },
                renderTo: 'numeracionFacturacion'
            });

    var cmbOficinaFacturacion = new Ext.form.ComboBox
            ({
                xtype: 'combobox',
                store: storeOficinasFacturacion,
                labelAlign: 'left',
                id: 'cmbOficinaFacturacion',
                name: 'cmbOficinaFacturacion',
                valueField: 'intIdOficina',
                displayField: 'strNombreOficina',
                fieldLabel: '',
                width: 290,
                allowBlank: false,
                emptyText: 'Seleccione Oficina',
                disabled: boolDisableComboOficina,
                renderTo: 'oficinaFacturacion',
                editable: false,
                listeners:
                        {
                            select:
                                    {
                                        fn: function(comp, record, index)
                                        {
                                            document.getElementById("numFactura").innerHTML = '';
                                            document.getElementById("strNumeroFacturacion").value = '';

                                            Ext.getCmp('cmbNumeracionFacturacion').setValue(null);

                                            if (comp.getRawValue() === "" || comp.getRawValue() === "Seleccione oficina")
                                            {
                                                comp.setValue(null);
                                                Ext.getCmp('cmbNumeracionFacturacion').setDisabled(true);
                                            }
                                            else
                                            {
                                                Ext.getCmp('cmbNumeracionFacturacion').setDisabled(false);

                                                var objExtraParams = storeNumeracionFacturacion.proxy.extraParams;
                                                objExtraParams.oficina = Ext.getCmp('cmbOficinaFacturacion').getValue();

                                                storeNumeracionFacturacion.load({params: {}});
                                            }

                                            verificarSiOficinaEImpuestoCompensa();
                                        }
                                    },
                            click:
                                    {
                                        element: 'el',
                                        fn: function()
                                        {
                                        }
                                    }
                        }
            });

    function getNumeroFactura(intIdNumeracion)
    {
        Ext.MessageBox.wait("Obteniendo número de factura...");

        $.ajax
                ({
                    type: "POST",
                    data: {"intIdNumeracion": intIdNumeracion},
                    url: strUrlGetNumeroFactura,
                    dataType: 'json',
                    success: function(resultado)
                    {
                        Ext.MessageBox.hide();

                        document.getElementById("numFactura").innerHTML = '';
                        document.getElementById("strNumeroFacturacion").value = '';

                        if (resultado.error)
                        {
                            Ext.Msg.alert('Atención', resultado.mensaje);
                        }
                        else
                        {
                            document.getElementById("numFactura").innerHTML = resultado.numeroFactura;
                            document.getElementById("strNumeroFacturacion").value = resultado.numeroFactura;
                        }
                    },
                    failure: function()
                    {
                        Ext.MessageBox.hide();

                        document.getElementById("numFactura").innerHTML = '';
                        document.getElementById("strNumeroFacturacion").value = '';

                        Ext.Msg.alert('Error', 'Error al traer el numero de facturacion de la oficina seleccionada');
                    }
                });
    }
    //Modelo Impuestos
    Ext.define('modelImpuestos', 
    {
        extend: 'Ext.data.Model',
        fields: 
        [
            {name: 'intIdImpuesto',          type: 'int'},
            {name: 'strDescripcionImpuesto', type: 'string'}
        ]
    });
    
    var impuestosStore = Ext.create('Ext.data.Store', 
    {
        autoLoad: true,
        model: "modelImpuestos",
        proxy: 
        {
            type: 'ajax',
            url : strUrlGetImpuestos,
            timeout: 9000000,
            reader: 
            {
                type: 'json',
                root: 'encontrados'
            }
        },
        listeners:
        {
            load: function(store, records)
            {        
                if( boolPermisoSeleccionarImpuesto )
                {
                    Ext.each(records, function(record)
                    {
                        if( record.get('intIdImpuesto') == intIdImpuestoIvaActivo )
                        {
                            Ext.getCmp('cmbImpuesto').setValue(record.get('intIdImpuesto'));
                        }
                    });
                }//( boolPermisoSeleccionarImpuesto )
            }
        }
    });	

    if( boolPermisoSeleccionarImpuesto )
    {
        //Combo Impuestos
        cmbImpuestos = new Ext.form.ComboBox
        ({
            xtype: 'combobox',
            store: impuestosStore,
            labelAlign: 'left',
            name: 'cmbImpuesto',
            id: 'cmbImpuesto',
            valueField: 'intIdImpuesto',
            displayField: 'strDescripcionImpuesto',
            fieldLabel: '',
            width: 85,
            triggerAction: 'all',
            queryMode: 'local',
            allowBlank: true,
            renderTo: 'divImpuestoIva',
            editable: false,
            listeners: 
            {
                select:
                {
                    fn:function(comp, record, index)
                    {
                        verificarSiOficinaEImpuestoCompensa();
                        verificarRadioButtonChecked();
                    }
                },
                click: 
                {
                    element: 'el',
                    fn: function(){}
                }      
            }
        });
    }

    desde_date = new Ext.form.DateField({
        name: 'feDesdeFacturaPost',
        allowBlank: false,
        format: 'Y-m-d',
        renderTo: 'feDesdeFactura',
        id: 'feDesdeFacturaPost',
    });

    hasta_date = new Ext.form.DateField({
        name: 'feHastaFacturaPost',
        allowBlank: false,
        format: 'Y-m-d',
        renderTo: 'feHastaFactura',
        id: 'feHastaFacturaPost',
    });

    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 1,
        listeners: {
            beforeedit: function(editor, e) 
            {
                var strCampoEditado = e.field;
                if(boolEditarPrecioFactDet)
                {
                    if (strCampoEditado === 'descripcion' && strPrefijoEmpresa === 'TN')
                    {
                        strCampoPrincipal = 'editDescripcion';
                    } else {
                        if (strCampoEditado === 'precio_uni')
                        {
                            strCampoPrincipal = 'precioUnitarioFact';
                        } else {
                            strCampoPrincipal = 'precioDetFact';
                        }
                    }
                    
                    Ext.getCmp(strCampoPrincipal).setDisabled(false);
                }
            },
            edit: function(editor, e) 
            {
                record = e.record;
                store  = e.grid.getStore();
                var strCampoEditado = e.field;
                var strMensajeCampo = strCampoEditado == 'precio' ? 'precio' : 'PVP';
                if(boolEditarPrecioFactDet)
                {                
                    if(strCampoEditado == 'precio' || strCampoEditado == 'precio_uni') 
                    {
                        strPagaIce                 = 'NO';
                        acum_subtotal              = 0;
                        acum_impuesto              = 0;
                        acum_impuestoIce           = 0;
                        acum_subtotal_ice          = 0;
                        acum_impuestoIva           = 0;
                        acum_impuestoOtros         = 0;
                        acum_total                 = 0;
                        acum_descuento             = 0;
                        floatCompensacionSolidaria = 0;
                        impuestoIvaIndividual      = 0;
                        impuestoIceIndividual      = 0;
                        impuestoIvaUnitario        = 0;
                        impuestoIceUnitario        = 0;                        
                        
                        if(boolPagaIce)
                        {
                            if(document.getElementById('aplicaIce').checked)
                            {
                                strPagaIce = 'SI';
                            }
                        }               


                        if(e.value <= 0)
                        {                     
                          record.set(strCampoEditado, e.originalValue);
                          Ext.Msg.alert('Error ', 'Valor de ' + strMensajeCampo + ' no permitido. ');
                          return false;
                        }                   

                        if(!(Utils.REGEX_PRECIO.test(e.value)))
                        {                     
                          record.set(strCampoEditado, e.originalValue);
                          Ext.Msg.alert('Error ', 'Formato de ' + strMensajeCampo  + ' no v\u00e1lido, el valor ingresado debe tener hasta 2 decimales Ej: (2.50)');
                          return false;
                        }                       

                        //Si se modifica el precio unitario (PVP), se realiza el cálculo del valor proporcional.
                        if(strCampoEditado == 'precio_uni')
                        {
                            strFechaDesde       = Ext.getCmp('feDesdeFacturaPost').getValue();
                            strFechaHasta       = Ext.getCmp('feHastaFacturaPost').getValue();
                            intProporcionalDias = 0;
                            intTotalPorMesDias  = 0;

                            Ext.MessageBox.wait('Calculado el valor proprocional...');
                            Ext.Ajax.request({
                                async: false,
                                url: strUrlGetDiasRestantes,
                                method: 'POST',
                                params: {fechaDesde: strFechaDesde, fechaHasta: strFechaHasta},
                                success: function(response) {
                                    Ext.MessageBox.hide();
                                    objJsonResponse     = JSON.parse(response.responseText);
                                    if (objJsonResponse.status != 'OK')
                                    {
                                        Ext.Msg.alert('Error ', 'Error: ' + objJsonResponse.message);
                                        return false;
                                    }
                                    intProporcionalDias = parseFloat(objJsonResponse.data.intProporcionalDias);
                                    intTotalPorMesDias  = parseFloat(objJsonResponse.data.intTotalPorMesDias);
                                },
                                failure: function(result)
                                {
                                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                }
                            });

                            valorProporcionalNuevo = parseFloat(record.data.precio_uni) * intProporcionalDias / intTotalPorMesDias;
                            record.set('precio', Math.round(valorProporcionalNuevo * 100)/100);
                        }

                        store.each(function (record) 
                        {
                            var intPorcentajeImp  =  0;
                            
                            if(record.data.descuento != null)
                            {
                                acum_descuento += record.data.descuento;
                            }
                            
                            acum_subtotal              += (record.data.precio * record.data.cantidad);
                            
                            if(record.data.impuestoIce > 0)
                            {      
                                impuestoIceIndividual = (((record.data.precio * record.data.cantidad) - record.data.descuento) 
                                                        *(record.data.porcentajeImpuestoIce/100) );
                                                    
                                impuestoIceUnitario = ((record.data.precio - (record.data.descuento/record.data.cantidad)) 
                                                        *(record.data.porcentajeImpuestoIce/100) );                                                    
                                                   
                                acum_impuestoIce += impuestoIceIndividual;                  
                            }
                            else
                            {
                                impuestoIceIndividual = 0;
                                
                                impuestoIceUnitario   = 0;
                                
                                acum_impuestoIce += impuestoIceIndividual;
                            } 
                            
                            
                            
                            acum_impuesto              += (record.data.impuesto * record.data.cantidad);
                            
                            if( boolPermisoSeleccionarImpuesto )
                            {
                                intImpuesto = Ext.getCmp('cmbImpuesto').getValue();

                                if( intImpuesto > 0 && intImpuesto != null )
                                {
                                    var strImpuesto = Ext.getCmp('cmbImpuesto').getRawValue();
                                    strImpuesto = strImpuesto.replace('IVA ','');
                                    strImpuesto = strImpuesto.replace('ICE ','');
                                    strImpuesto = strImpuesto.replace('ITBMS ','');
                                    strImpuesto = strImpuesto.replace('% ','');
                                    strImpuesto = strImpuesto.trim();

                                    intPorcentajeImp = parseInt(strImpuesto);
                                }
                            }
                            else
                            {
                               intPorcentajeImp = record.data.porcentajeImpuesto;
                            }
                            
                            if(record.data.impuestoIce > 0 || ('SI' === strPagaIce && record.data.impuestoIce > 0))
                            {
                                if('S'=== strPagaIva && record.data.impuesto > 0)
                                {
                                    impuestoIvaIndividual = parseFloat((((record.data.precio * record.data.cantidad) - record.data.descuento) + 
                                                            (((record.data.precio * record.data.cantidad) - record.data.descuento) 
                                                            * (record.data.porcentajeImpuestoIce/100)))* (intPorcentajeImp/100) );

                                    impuestoIvaUnitario   = parseFloat(((record.data.precio - (record.data.descuento/record.data.cantidad)) + 
                                                            ((record.data.precio - (record.data.descuento/record.data.cantidad)) 
                                                            * (record.data.porcentajeImpuestoIce/100)))* (intPorcentajeImp/100) );                                                                   

                                    acum_impuestoIva      += impuestoIvaIndividual;
                                }
                            }
                            else
                            {
                                if('S'=== strPagaIva && record.data.impuesto > 0)
                                {
                                    impuestoIvaIndividual = parseFloat(((record.data.precio* record.data.cantidad) - record.data.descuento)  
                                                                          * (intPorcentajeImp/100) ); 

                                    impuestoIvaUnitario   = parseFloat((record.data.precio - (record.data.descuento/record.data.cantidad))  
                                                                          * (intPorcentajeImp/100) );                                                                   
                                    acum_impuestoIva      += impuestoIvaIndividual; 
                                }
                            }
                            
                            record.set('precio', record.data.precio);
                            record.set('impuestoIce', impuestoIceIndividual);
                            record.set('impuestoIva', impuestoIvaIndividual);
                            
                            acum_impuestoOtros         += (record.data.impuestoOtros * record.data.cantidad);
                            floatCompensacionSolidaria += (record.data.compensacionSolidaria * record.data.cantidad);
                        });

                        redondearDetalleVisualizacion();

                        if(boolFacturacionAgrupada)
                        {
                            var objItemFirst = store.first();

                            store.removeAll();

                            objItemFirst.data.precio                = acum_subtotal;
                            objItemFirst.data.cantidad              = 1;
                            objItemFirst.data.descuento             = acum_descuento;
                            objItemFirst.data.impuesto              = acum_impuestoIva + acum_impuestoIce + acum_impuestoOtros;
                            objItemFirst.data.impuestoIva           = acum_impuestoIva;
                            objItemFirst.data.impuestoIce           = acum_impuestoIce;
                            objItemFirst.data.impuestoOtros         = acum_impuestoOtros;
                            objItemFirst.data.compensacionSolidaria = floatCompensacionSolidaria;
                            objItemFirst.data.tipoOrden             = "PAGR";

                            store.add(objItemFirst);
                        }
                    }
                }
            } 
        }        
    });

    Ext.define('ListadoDetalleOrden', {
        extend: 'Ext.data.Model',
        fields: 
        [
            {name: 'codigo',                type: 'string'},
            {name: 'informacion',           type: 'string'},
            {name: 'precio',                type: 'float'},
            {name: 'cantidad',              type: 'float'},
            {name: 'descuento',             type: 'float'},
            {name: 'tipo',                  type: 'string'},
            {name: 'tipoOrden',             type: 'string'},
            {name: 'fechaActivacion',       type: 'string'},
            {name: 'puntoId',               type: 'string'},
            {name: 'fechaDesde',            type: 'string'},
            {name: 'fechaHasta',            type: 'string'},
            {name: 'precio_uni',            type: 'string'},
            {name: 'tieneImpuesto',         type: 'string'},
            {name: 'descripcion',           type: 'string'},
            {name: 'impuesto',              type: 'float'},
            {name: 'impuestoIva',           type: 'float'},
            {name: 'impuestoIce',           type: 'float'},
            {name: 'impuestoOtros',         type: 'float'},
            {name: 'compensacionSolidaria', type: 'float'},
            {name: 'porcentajeImpuesto',    type: 'float'},
            {name: 'porcentajeImpuestoIce', type: 'float'},
            {name: 'idServicio',            type: 'int'},
            {name: 'login',                 type: 'string'},

        ]
    });

    store = Ext.create('Ext.data.Store',
    {
        // destroy the store if the grid is destroyed
        autoDestroy: true,
        model: 'ListadoDetalleOrden',
        proxy: 
        {
            type: 'ajax',
            url: url_listar_informacion_existente,
            timeout: 9000000,
            reader: 
            {
                type: 'json',
                root: 'listadoInformacion'
            },
            extraParams:
            {
                puntoid: '', 
                informacionGrid: '', 
                fechaDesde: '', 
                fechaHasta: '', 
                strPagaIva: strPagaIva,
                strOpcionPrecargadaSinFrecuencia: strPrecargadaSinFrecuencia,
                strTipoFacturacion: 'proporcional',
                strIdFactura: idFactura
            },
            simpleSortMode: true
        },
        listeners: 
        {
            beforeload: function (store) 
            {
                var intImpuestoId = 0;
                var strPagaIce    = document.getElementById("strPagaIce").value;
    
                if(boolPagaIce)
                {
                    if(document.getElementById('aplicaIce').checked)
                    {
                        strPagaIce = 'SI';
                    } 
                    else 
                    {
                        strPagaIce = 'NO';
                    }
                }
                
                if( boolPermisoSeleccionarImpuesto )
                {
                    intImpuestoId = Ext.getCmp('cmbImpuesto').getValue();
                }
                
                verificarSiClienteEsCompensado();
                
                store.getProxy().extraParams.puntoid         = punto_id;
                store.getProxy().extraParams.intImpuestoId   = intImpuestoId;
                store.getProxy().extraParams.strPagaIce      = strPagaIce;
                store.getProxy().extraParams.strEsCompensado = strEsCompensado;
                store.getProxy().extraParams.boolFacturacionAgrupada = boolFacturacionAgrupada;
                
                var array_data_caract = {};
                var j = 0;
                var informacion = [];
                for (var i = 0; i < grid.getStore().getCount(); i++)
                {
                    variable = grid.getStore().getAt(i).data;
                    
                    for (var key in variable)
                    {
                        var valor = variable[key];
                        if (j == 0)
                            array_data_caract['codigo'] = valor;
                        if (j == 1)
                            array_data_caract['informacion'] = valor;
                        if (j == 2)
                            array_data_caract['precio'] = valor;
                        if (j == 3)
                            array_data_caract['cantidad'] = valor;
                        if (j == 4)
                            array_data_caract['descuento'] = valor;
                        if (j == 5)
                            array_data_caract['tipo'] = valor;
                        if (j == 6)
                            array_data_caract['tipoOrden'] = valor;
                        if (j == 7)
                            array_data_caract['fechaActivacion'] = valor;
                        if (j == 8)
                            array_data_caract['puntoId'] = valor;
                        if (j == 9)
                            array_data_caract['fechaDesde'] = valor;
                        if (j == 10)
                            array_data_caract['fechaHasta'] = valor;
                        j++;
                    }
                    
                    informacion.push(array_data_caract);
                    array_data_caract = {};
                    j = 0;
                }

                store.getProxy().extraParams.fechaDesde = Ext.getCmp('feDesdeFacturaPost').getValue();
                store.getProxy().extraParams.fechaHasta = Ext.getCmp('feHastaFacturaPost').getValue();
                store.getProxy().extraParams.informacionGrid = JSON.stringify(informacion);
            },
            load: function (store)
            {
                acumPrecioUnitario         = 0;
                acum_subtotal              = 0;
                acum_subtotal_ice          = 0;
                acum_impuesto              = 0;
                acum_impuestoIce           = 0;
                acum_impuestoIva           = 0;
                acum_impuestoOtros         = 0;
                acum_total                 = 0;
                acum_descuento             = 0;
                floatCompensacionSolidaria = 0;

                store.each(function (record)
                {
                    if(record.data.descuento != null)
                    {
                        acum_descuento += record.data.descuento;
                    }
                    
                    acumPrecioUnitario         += parseFloat(record.data.precio_uni * record.data.cantidad);
                    acum_subtotal              += parseFloat(record.data.precio * record.data.cantidad);
                    acum_impuesto              += parseFloat(record.data.impuesto * record.data.cantidad);
                    acum_impuestoIce           += parseFloat(record.data.impuestoIce * record.data.cantidad);
                    acum_impuestoIva           += parseFloat(record.data.impuestoIva * record.data.cantidad);
                    acum_impuestoOtros         += parseFloat(record.data.impuestoOtros * record.data.cantidad);
                    floatCompensacionSolidaria += parseFloat(record.data.compensacionSolidaria * record.data.cantidad);
                });
                
                redondearDetalleVisualizacion();
                             
            }
        }
    });


    grid = Ext.create('Ext.grid.Panel',
    {
        store: store,
        columns: 
        [
            {
                text: 'Id',
                dataIndex: 'idServicio',
                hidden: true
            },        
            {
                text: 'Tipo',
                dataIndex: 'tipo',
                hidden: true
            }, 
            {
                text: 'TipoOrden',
                dataIndex: 'tipoOrden',
                hidden: true
            }, 
            {
                text: 'PuntoId',
                dataIndex: 'puntoId',
                hidden: true
            }, 
            {
                text: 'Codigo',
                dataIndex: 'codigo',
                hidden: true
            }, 
            {
                text: 'Producto/Plan',
                width: 110,
                dataIndex: 'informacion',
            },
            {
                text: 'Login',
                width: 100,
                dataIndex: 'login'
            },
            {
                text: 'Descripcion',
                width: 180,
                dataIndex: 'descripcion',
                editor: {
                    allowBlank: false,
                    disabled: true,
                    id: 'editDescripcion'
                }
            }, 
            {
                text: 'Fe. Desde',
                width: 80,
                dataIndex: 'fechaDesde'
            },
            {
                text: 'Fe. Hasta',
                width: 80,
                dataIndex: 'fechaHasta'
            },
            {
                text: 'PVP',
                width: 90,
                align: 'right',
                dataIndex: 'precio_uni',
                editor: {
                    allowBlank: false,
                    disabled: true,
                    id: 'precioUnitarioFact'
                }
            },
            {
                text: 'Precio proporcional',
                width: 95,
                align: 'right',
                dataIndex: 'precio',
                editor: {
                    allowBlank: false,
                    disabled: true,
                    id: 'precioDetFact'
                }                
            },
            {
                text: 'Cantidad',
                dataIndex: 'cantidad',
                align: 'right',
                width: 70
            },
            {
                text: 'Descuento',
                dataIndex: 'descuento',
                align: 'right',
                width: 70
            },
            {
                header: 'Acciones',
                xtype: 'actioncolumn',
                width: 70,
                sortable: false,
                items: 
                [
                    {
                        iconCls: 'button-grid-delete',
                        tooltip: 'Eliminar',
                        handler: function (grid, rowIndex, colIndex)
                        {
                            var precio            = 0;
                            var cantidad          = 0;
                            var subtotal          = 0;
                            var impuesto          = 0;
                            var impuestoIva       = 0;
                            var impuestoIce       = 0;
                            var impuestoOtros     = 0;
                            var descuento         = 0; 
                            var floatCompensacion = 0;

                            if(grid.getStore().getAt(rowIndex).data.descuento != null)
                            {
                                descuento = grid.getStore().getAt(rowIndex).data.descuento;
                            }
                            
                            precio        = grid.getStore().getAt(rowIndex).data.precio;
                            cantidad      = grid.getStore().getAt(rowIndex).data.cantidad;
                            subtotal      = (precio * cantidad);
                            
                            if( grid.getStore().getAt(rowIndex).data.tipoOrden === "REP")
                            {
                                subtotal = precio;
                            }
                            
                            if( grid.getStore().getAt(rowIndex).data.tipoOrden === "MAN" || 
                                grid.getStore().getAt(rowIndex).data.tipoOrden === "REP")
                            {
                                impuesto          = grid.getStore().getAt(rowIndex).data.impuesto;
                                impuestoIva       = grid.getStore().getAt(rowIndex).data.impuestoIva;
                                impuestoIce       = grid.getStore().getAt(rowIndex).data.impuestoIce;
                                impuestoOtros     = grid.getStore().getAt(rowIndex).data.impuestoOtros;
                                floatCompensacion = grid.getStore().getAt(rowIndex).data.compensacionSolidaria;

                            }
                            else
                            {
                                impuesto          = grid.getStore().getAt(rowIndex).data.impuesto * cantidad;
                                impuestoIva       = grid.getStore().getAt(rowIndex).data.impuestoIva * cantidad;
                                impuestoIce       = grid.getStore().getAt(rowIndex).data.impuestoIce * cantidad;
                                impuestoOtros     = grid.getStore().getAt(rowIndex).data.impuestoOtros * cantidad;
                                floatCompensacion = grid.getStore().getAt(rowIndex).data.compensacionSolidaria * cantidad;
                            }       
                            
                            //Resto este valor a los acumuladores
                            acum_subtotal              -= subtotal;
                            acum_descuento             -= descuento;
                            acum_impuesto              -= impuesto;
                            acum_impuestoIce           -= impuestoIce;
                            acum_impuestoIva           -= impuestoIva;
                            acum_impuestoOtros         -= impuestoOtros;
                            floatCompensacionSolidaria -= floatCompensacion;

                            redondearDetalleVisualizacion();

                            store.removeAt(rowIndex);
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
                        grid.cellIndex   = cellIndex;
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
                                        columnDataIndex = view.getHeaderByCell(trigger).dataIndex;

                                    if( view.getRecord(parent).get(columnDataIndex) != null )
                                    {
                                        var columnText      = view.getRecord(parent).get(columnDataIndex).toString();
                                        
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
                
                grid.tip.on('show', function()
                {
                    var timeout;

                    grid.tip.getEl().on('mouseout', function()
                    {
                        timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                    });

                    grid.tip.getEl().on('mouseover', function(){window.clearTimeout(timeout);});

                    Ext.get(view.el).on('mouseover', function(){window.clearTimeout(timeout);});

                    Ext.get(view.el).on('mouseout', function()
                    {
                        timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                    });
                });
            }
        },
        dockedItems: 
        [
            {
                dock: 'top',
                xtype: 'toolbar',
                items: 
                [
                    {xtype: 'tbfill'},
                    {
                        xtype: 'button',
                        itemId: 'grid-excel-button',
                        iconCls: 'x-btn-icon icon_exportar',
                        //hidden : true,
                        text: 'Exportar',
                        handler: function()
                        {
                            var vExportContent = grid.getExcelXml();
                            document.location = 'data:application/vnd.ms-excel;base64,' + Base64.encode(vExportContent);
                        }

                    }
                ]
            }
        ],
        selModel:
        {
            selType: 'cellmodel'
        },
        renderTo: 'lista_informacion_pre_cargada',
        width: 950,
        height: 200,
        title: 'Listado de servicios',
        frame: true,
        plugins: [cellEditing]
    });

    if (typeof strClonarFactura !== 'undefined') 
    {
        if(strClonarFactura == "S")
        {
            clonacion();
        }
    }
});


function clonacion()
{
    
    document.getElementById("strClonarFactura").value = strClonarFactura;
    document.getElementById("strIdFactura").value = idFactura;
    document.getElementById("observacion").value = strObservacion;
    if(typeof diaInicial !== 'undefined' )
    {
        if(diaInicial!==null)
        {
            Ext.getCmp("feDesdeFacturaPost").setValue(diaInicial);
        }
    }
    
    if(typeof diaFinal !== 'undefined' )
    {
        if(diaFinal!==null)
        {
            Ext.getCmp("feHastaFacturaPost").setValue(diaFinal);    
        }
    }
    
    verificarCheck('Clonar');
    
    
}
function enviarInformacion()
{
    if( parseFloat(acum_total).toFixed(2) > 0 )
    {
        var array_data_caract = {};
        var j                 = 0;
        var informacion       = [];


        for (var i = 0; i < grid.getStore().getCount(); i++)
        {
            variable = grid.getStore().getAt(i).data;

            for (var key in variable)
            {
                var valor = variable[key];

                if (j == 0)
                {
                    array_data_caract['codigo'] = valor;
                }

                if (j == 1)
                {
                    array_data_caract['informacion'] = valor;
                }

                if (j == 2)
                {
                    array_data_caract['precio'] = valor;
                }

                if (j == 3)
                {
                    array_data_caract['cantidad'] = valor;
                }

                if (j == 4)
                {
                    array_data_caract['descuento'] = valor;
                }

                if (j == 5)
                {
                    array_data_caract['tipo'] = valor;
                }

                if (j == 6)
                {
                    array_data_caract['tipoOrden'] = valor;
                }

                if (j == 7)
                {
                    array_data_caract['fechaActivacion'] = valor;
                }

                if (j == 8)
                {
                    array_data_caract['puntoId'] = valor;
                }

                if (j == 9)
                {
                    array_data_caract['fechaDesde'] = valor;
                }

                if (j == 10)
                {
                    array_data_caract['fechaHasta'] = valor;
                }

                if (j == 13)
                {
                    array_data_caract['descripcion'] = valor;
                }

                if (j == 14)
                {
                    array_data_caract['impuesto'] = valor;
                }

                if (j == 18)
                {
                    array_data_caract['compensacionSolidaria'] = valor;
                }
                
                if (j === 21)
                {
                    array_data_caract['idServicio'] = valor;
                }                        

                j++;
            }

            informacion.push(array_data_caract);

            array_data_caract = {};
            j                 = 0;
        }

        if (informacion.length > 0)
        {
            var boolContinuar = true;
            var intImpuestoId = 0;
            var strPagaIce    = document.getElementById("strPagaIce").value;
            var intIdOficinaFacturacion    = Ext.getCmp('cmbOficinaFacturacion').getValue();
            var intIdNumeracionFacturacion = Ext.getCmp('cmbNumeracionFacturacion').getValue();

            if( !(intIdOficinaFacturacion > 0) )
            {
                Ext.Msg.alert('Atención', 'Debe seleccionar una oficina de Facturación');
                return false;
            }
            if(boolPagaIce)
            {
                if(document.getElementById('aplicaIce').checked)
                {
                    strPagaIce = 'SI';
                } 
                else 
                {
                    strPagaIce = 'NO';
                }
            }

            if( boolPermisoSeleccionarImpuesto )
            {
                intImpuestoId = Ext.getCmp('cmbImpuesto').getValue();
            }

            verificarSiClienteEsCompensado();

            if(boolContinuar)
            {
                loadMask('myLoading', true, 'Procesando');

                document.getElementById("listado_informacion").value = JSON.stringify(informacion);
                document.getElementById("feDesdeFacturaE").value     = document.getElementById('formulario').feDesdeFacturaPost.value;
                document.getElementById("feHastaFacturaE").value     = document.getElementById('formulario').feHastaFacturaPost.value;
                document.getElementById("intTxtIdImpuesto").value    = intImpuestoId;
                document.getElementById('strPagaIce').value          = strPagaIce;
                document.getElementById('strEsCompensado').value     = strEsCompensado;
                document.getElementById("intIdOficina").value        = intIdOficinaFacturacion;
                document.getElementById("intIdNumeracion").value     = intIdNumeracionFacturacion;

                if(strClonarFactura=="S")
                {
                    var feDesde = Ext.getCmp('feDesdeFacturaPost').getValue();
                    var feHasta = Ext.getCmp('feHastaFacturaPost').getValue();
                    if(feDesde!=null && feHasta !=null)
                    {
                        document.formulario.submit();
                    }
                    else
                    {
                        Ext.Msg.alert("Atención", "Debe llenar el Mes y el Año de consumo");
                        loadMask('myLoading', false);
                    }
                }
                else
                {
                    document.formulario.submit();
                }
                
            }
        }
        else
        {
            Ext.Msg.alert("Atención", "Ingrese detalles a la factura");
        }
    }//( parseFloat(acum_total).toFixed(2) > 0 )
    else
    {
        Ext.Msg.alert('Atención', 'No se pueden crear facturas proporcionales con valor total en cero');
    }
}

function verificarCheck(info)
{
    strTipoFactura = info;
    $('#formulario_tipo').addClass('campo-oculto');
    $('#formulario_portafolio').addClass('campo-oculto');
    $('#formulario_catalogo').addClass('campo-oculto');

    if (info == 'Clonar')
    {
        boolFacturacionAgrupada = false;
        store.removeAll();
        store.getProxy().extraParams.clonar = 'S';
        store.load();
        store.getProxy().extraParams.clonar = 'N';
    }

    if (info == 'Orden' || info == 'Agrupada' || info == 'SinFrencuencia' || info == 'SinFrencuenciaOrden' || info == 'AgrupadaSinFrencuenciaOrden')
    {
        //debo verificar si existe informacion en el grid
        //$("#lista_informacion_pre_cargada").removeClass("campo-oculto");
        if ((Ext.getCmp('feDesdeFacturaPost').getValue() != null) && (Ext.getCmp('feHastaFacturaPost').getValue() != null))
        {
            if (Ext.getCmp('feDesdeFacturaPost').getValue() > Ext.getCmp('feHastaFacturaPost').getValue())
            {
                Ext.Msg.show({
                    title: 'Error en Busqueda',
                    msg: 'La Fecha Desde debe ser menor a Fecha Hasta.',
                    buttons: Ext.Msg.OK,
                    animEl: 'elId',
                    icon: Ext.MessageBox.ERROR
                });

            }
            else
            {
                if( info == 'Agrupada' ||  info == 'AgrupadaSinFrencuenciaOrden'  )
                {
                    boolFacturacionAgrupada = true;
                }
                else
                {
                    boolFacturacionAgrupada = false;
                }
                
                store.removeAll();
                
                if( info == 'SinFrencuencia' )
                {
                    store.getProxy().extraParams.strSinFrecuencia = 'S';
                }
                else
                {
                    if ( info == 'SinFrencuenciaOrden' ||  info == 'AgrupadaSinFrencuenciaOrden' )
                    {
                        store.getProxy().extraParams.strSinFrecuencia = 'SNEW';
                    } else {
                        store.getProxy().extraParams.strSinFrecuencia = 'N';
                    }
                }
                
                store.load();
            }
        }
        else
        {

            Ext.Msg.show({
                title: 'Error en Busqueda',
                msg: 'Por Favor Ingrese las fecha.',
                buttons: Ext.Msg.OK,
                animEl: 'elId',
                icon: Ext.MessageBox.ERROR
            });
        }
    }

    if (info == 'Manual')
    {
        if ((Ext.getCmp('feDesdeFacturaPost').getValue() != null) && (Ext.getCmp('feHastaFacturaPost').getValue() != null))
        {
            if (Ext.getCmp('feDesdeFacturaPost').getValue() > Ext.getCmp('feHastaFacturaPost').getValue())
            {
                Ext.Msg.show({
                    title: 'Error en Busqueda',
                    msg: 'La Fecha Desde debe ser menor a Fecha Hasta.',
                    buttons: Ext.Msg.OK,
                    animEl: 'elId',
                    icon: Ext.MessageBox.ERROR
                });
            }
            else
            {
                var fechaDesde = document.getElementById('formulario').feDesdeFacturaPost.value;
                var fechaHasta = document.getElementById('formulario').feHastaFacturaPost.value;

                if ( strPrefijoEmpresa == 'MD' || strPrefijoEmpresa == 'EN' )
                {
                    var tipo = "portafolio";
                    
                    $.ajax({
                        type: "POST",
                        data: "tipo="+tipo,
                        url: url_info_portafolio,
                        beforeSend: function() 
                        {
                            if (tipo == 'portafolio')
                            {
                                tipo = "Paquete";
                            }
                            Ext.MessageBox.wait("Cargando Datos de " + tipo + "s");
                            $('#formulario_portafolio').addClass('campo-oculto');
                        },
                        complete: function() 
                        {
                            Ext.MessageBox.hide();
                        },
                        success: function (msg) 
                        {
                            if (msg.msg == 'ok')
                            {
                                if (msg.info == 'portafolio')
                                {
                                    $('#formulario_portafolio').removeClass('campo-oculto');
                                    document.getElementById("planes").innerHTML = msg.div;
                                }
                            }
                        }
                    });
                }// ( strPrefijoEmpresa == 'MD' )
                else
                {
                    var fechaDesde = document.getElementById('formulario').feDesdeFacturaPost.value;
                    var fechaHasta = document.getElementById('formulario').feHastaFacturaPost.value;
                
                    if ( strPrefijoEmpresa == 'TN' )
                    {
                        var tipo = "catalogo";
                        $.ajax({
                            type: "POST",
                            data: "tipo="+tipo+"&modulo=Financiero",
                            url: url_info_portafolio,
                            beforeSend: function() 
                            {
                                if (tipo == 'catalogo')
                                {
                                    tipo = "Producto";
                                }
                                Ext.MessageBox.wait("Cargando Datos de " + tipo + "s");
                                $('#formulario_portafolio').addClass('campo-oculto');
                                $('#formulario_catalogo').addClass('campo-oculto');
                                $('#contenido').addClass('campo-oculto');
                            },
                            complete: function() 
                            {
                                Ext.MessageBox.hide();
                            },
                            success: function(msg)
                            {				
                                if (msg.msg == 'ok')
                                {
                                    if(msg.info=='catalogo')
                                    {
                                        document.getElementById("cantidad_plan").value="";
                                        document.getElementById("precio").value="";
                                        $('#formulario_catalogo').removeClass('campo-oculto');
                                        $('#formulario_portafolio').addClass('campo-oculto');
                                        document.getElementById("producto").innerHTML=msg.div;
                                    }
                                }
                                else
                                {
                                    $('#formulario_portafolio').addClass('campo-oculto');
                                    $('#formulario_catalogo').addClass('campo-oculto');
                                }
                            }
                        });
                    }//  ( strPrefijoEmpresa == 'TN' )                    
                    else
                    {
                        if (strPrefijoEmpresa == 'TNP')
                        {
                            $('#formulario_tipo').removeClass('campo-oculto');
                            $('#formulario_portafolio').addClass('campo-oculto');
                            $('#formulario_catalogo').addClass('campo-oculto');   
                        }// ( strPrefijoEmpresa == 'TNP' )
                    }                    
                }               
            }
            
            $('#planes').change(function ()
            {
                var info_plan = document.getElementById('planes').value;
                var plan      = info_plan.split("-");

                $.ajax
                ({
                    type: "POST",
                    data: "plan=" + plan[0],
                    url: url_info_plan,
                    beforeSend: function() 
                    {
                        Ext.MessageBox.wait("Cargando precio del plan: " + plan[1]);
                    },
                    complete: function() 
                    {
                        Ext.MessageBox.hide();
                    },
                    success: function (msg) {
                        //Inicializacion
                        document.getElementById("contenido_plan").innerHTML = "";
                        
                        if (msg.msg == 'ok')
                        {
                            document.getElementById("precio").value = msg.precio;
                            document.getElementById("descuento_plan").value = msg.descuento;
                            document.getElementById("tipoOrden").value = msg.tipoOrden;
                            document.getElementById("tieneImpuesto").value = msg.tieneImpuesto;
                            document.getElementById("seProrratea").value = msg.prorratea;
                        }
                        else
                        {
                            document.getElementById("contenido_plan").innerHTML = msg.msg;
                        }
                    }
                });
            });
            
            
            $('#producto').change(function()
            {
                var info_producto=document.getElementById('producto').value;
                var producto=info_producto.split("-");                

                $.ajax({
                    type: "POST",
                    data: "producto=" + producto[0]+"&idPunto="+punto_id,
                    url:url_listar_caracteristicas,
                    beforeSend: function() 
                    {
                        Ext.MessageBox.wait("Cargando Características del producto");
                        $('#contenido').addClass('campo-oculto');
                    },
                    complete: function() 
                    {
                        Ext.MessageBox.hide();
                    },
                    success: function(msg)
                    {
                        if (msg.msg == 'ok')
                        {
                            var strTabla = "<table id='tbProducto' class='formulario' width='100%'>";
                                strTabla += msg.div;
                                strTabla += "</table>";
                                
                            document.getElementById("contenido").innerHTML = strTabla;
                            $('#contenido').removeClass('info-error');
                            $('#contenido').removeClass('campo-oculto');
                        }
                        else
                        {
                            document.getElementById("contenido").innerHTML = msg.msg;
                            $('#contenido').addClass('info-error');
                            $('#mensaje').addClass('campo-oculto');
                        }
                    }
                });
            });
            
        }
        else
        {
            Ext.Msg.show({
                title: 'Error en Busqueda',
                msg: 'Por Favor Ingrese las fecha.',
                buttons: Ext.Msg.OK,
                animEl: 'elId',
                icon: Ext.MessageBox.ERROR
            });
        }
    }
}

function verificarTipoCheck(info)
{    
    if (info === 'catalogo' || info == 'portafolio')
    {        
        $.ajax({
            type: "POST",
            data: "tipo=" + info + "&modulo=Financiero",           
            url: url_info_portafolio,
            beforeSend: function ()
            {                
                if (info == 'portafolio')
                {
                    info = "Paquete";
                } else
                {
                    info = "Producto";
                }

                Ext.MessageBox.wait("Cargando Datos de " + info + "s");

                $('#formulario_portafolio').addClass('campo-oculto');
                $('#formulario_catalogo').addClass('campo-oculto');
            },
            complete: function ()
            {
                Ext.MessageBox.hide();
            },
            success: function (msg)
            {
                if (msg.msg == 'ok')
                {
                    if (msg.info == 'portafolio')
                    {
                        document.getElementById("planes").innerHTML = msg.div;
                        $('#formulario_portafolio').removeClass('campo-oculto');
                        $('#formulario_catalogo').addClass('campo-oculto');
                    } else if (msg.info == 'catalogo')
                    {
                        document.getElementById("cantidad_plan").value = "";
                        document.getElementById("precio").value = "";
                        document.getElementById("producto").innerHTML = msg.div;
                        $('#formulario_portafolio').addClass('campo-oculto');
                        $('#formulario_catalogo').removeClass('campo-oculto');
                    }
                } else
                {
                    $('#formulario_portafolio').addClass('campo-oculto');
                    $('#formulario_catalogo').addClass('campo-oculto');
                }
            }
        });      
    }
}                  

function replaceAll( text, busca, reemplaza )
{
		while (text.toString().indexOf(busca) != -1)
		text = text.toString().replace(busca,reemplaza);
		return text;
}

function agregar_detalle_catalogo()
{
    var strNombreTecnico            = document.getElementById("strNombreTecnico").value;
    var valor_caract                = new Array();
    var nombre_caract               = new Array();
    var text                        = "";
    var descuento                   = 0;
    var precio_unitario             = 0;
    var precio_total                = 0;
    var cantidad                    = formulario.cantidad.value;
    var info_producto               = formulario.producto.value;
    var producto                    = info_producto.split("-");
    var puntoId                     = formulario.punto_id.value;
    var x                           = 0;
    var tipo                        = 'PR';
    var tipoOrden                   = 'MAN';
    var precio_proporcional         = 0;
    var caracteristicas             = "formulario.caracteristicas_";
    var caracteristica_nombre       = "formulario.caracteristica_nombre_";
    var cantidad_caracteristicas    = formulario.cantidad_caracteristicas.value;
    var caracteristicas_n           = "";
    var caracteristica_nombre_n     = "";
    var floatimpuestoProductoSelec  = 0;
    var intImpuesto                 = 0;
    var intPorcentajeImpIce         = 0;
    var strPagaIce                  = document.getElementById("strPagaIce").value;
    
    if( cantidad != null && cantidad != '' && cantidad>0 )
    {
        if(boolPagaIce)
        {
            if(document.getElementById('aplicaIce').checked)
            {
                strPagaIce = 'SI';
            } 
            else 
            {
                strPagaIce = 'NO';
            }
        }

        if( boolPermisoSeleccionarImpuesto )
        {
            intImpuesto = Ext.getCmp('cmbImpuesto').getValue();

            if( intImpuesto > 0 && intImpuesto != null )
            {
                var strImpuesto = Ext.getCmp('cmbImpuesto').getRawValue();
                strImpuesto = strImpuesto.replace('IVA ','');
                strImpuesto = strImpuesto.replace('ICE ','');
                strImpuesto = strImpuesto.replace('ITBMS ','');
                strImpuesto = strImpuesto.replace('% ','');
                strImpuesto = strImpuesto.trim();

                intImpuesto = parseInt(strImpuesto);
            }
        }
        
        verificarSiClienteEsCompensado();


        for (var x = 0; x < cantidad_caracteristicas; x++)
        {
            caracteristicas_n         = caracteristicas + x;
            caracteristica_nombre_n   = caracteristica_nombre + x;
            valor_caract[x]           = eval(caracteristicas_n).value;
            nombre_caract[x]          = eval(caracteristica_nombre_n).value;
        }


        if( strNombreTecnico == "FINANCIERO" )
        {
            var funcion_precio = formulario.precio_unitario.value;
        }
        else
        {
            var funcion_precio = formulario.funcion_precio.value;
        }

        if( funcion_precio != null )
        {
            if( strNombreTecnico == "FINANCIERO" )
            {
                precio_unitario = parseFloat(funcion_precio);
            }
            else
            {
                text = funcion_precio;

                for (var x = 0; x < nombre_caract.length; x++)
                {
                    text = replaceAll(text, nombre_caract[x], valor_caract[x]); 
                }

                precio_unitario = eval(text);
            }

            if( parseFloat(precio_unitario.toFixed(2)) > 0 )
            {
                var f_inicio = document.getElementById('formulario').feDesdeFacturaPost.value;
                var f_fin = document.getElementById('formulario').feHastaFacturaPost.value;
                //Fecha inicio
                var mesInicio = parseInt(f_inicio.substring(5, 7));
                var anoInicio = f_inicio.substring(0, 4);
                //Fecha fin
                var mesFin = parseInt(f_fin.substring(5, 7));
                var anoFin = f_fin.substring(0, 4);

                var f_inicio_mod = f_inicio.substring(8, 10) + "-" + f_inicio.substring(5, 7) + "-" + f_inicio.substring(0, 4);
                var f_fin_mod = f_fin.substring(8, 10) + "-" + f_fin.substring(5, 7) + "-" + f_fin.substring(0, 4);

                //Dias proporcional
                var diasP = mostrarDias(f_inicio_mod, f_fin_mod);

                //Total del dias
                var diasT = 0;


                Ext.Ajax.request
                ({
                    url: strUrlGetFechasDiasPeriodoAjax,
                    method: 'post',
                    timeout: 999999999,
                    params:
                    { 
                        strFechaActivacion: f_inicio
                    },
                    success: function(response)
                    {
                        var objRespuesta = Ext.JSON.decode(response.responseText);
                            diasT        = objRespuesta.intTotalDiasMes;

                        //Prorrateo del producto, buscar carateristica si prorratea **proximamente
                        //Precio proporcional
                        precio_proporcional = (precio_unitario * diasP) / ( diasT );

                        if( parseFloat(precio_proporcional.toFixed(2)) > 0 )
                        {
                            //Precio total
                            precio_total = (precio_proporcional*cantidad) - floatimpuestoProductoSelec;

                            //Subtotal para el twig
                            acum_subtotal += precio_total;

                            var floatImpuestoIva    = 0;
                            var floatImpuestoIce    = 0;
                            var floatImpuestoOtros  = 0;
                            var floatImpuestoTotal  = 0;
                            var floatCompensacion   = 0;

                            for( var i = 2; i < producto.length; i++ )
                            {
                                floatimpuestoProductoSelec = 0;

                                if( producto[i] != null && producto[i] != '' )
                                {
                                    var arrayImpuesto    = producto[i].split(":");
                                    var intValorImpuesto = arrayImpuesto[1];

                                    if( arrayImpuesto[0] === 'ICE' || arrayImpuesto[0] === 'IEC' )
                                    {
                                        
                                        if(strPagaIce=='SI')
                                        {
                                            intPorcentajeImpIce         = intValorImpuesto;
                                            floatimpuestoProductoSelec  = (precio_total * intValorImpuesto) / 100;
                                            floatImpuestoIce            += floatimpuestoProductoSelec;
                                            acum_impuestoIce            += floatimpuestoProductoSelec;
                                        }
                                    }
                                    else if( arrayImpuesto[0] === 'IVA' || arrayImpuesto[0] === 'ITBMS' )
                                    {
                                        if( intImpuesto > 0 )
                                        {
                                            intValorImpuesto = intImpuesto;
                                        }

                                        if(strPagaIva=='S')
                                        {
                                            floatimpuestoProductoSelec  = ( (precio_total + floatImpuestoIce) * intValorImpuesto) / 100;
                                            acum_impuestoIva            += floatimpuestoProductoSelec;
                                            floatImpuestoIva            += floatimpuestoProductoSelec;

                                            if( 'SI' == strEsCompensado )
                                            {
                                                floatCompensacion          =  ( (precio_total + floatImpuestoIce)
                                                                              * floatPorcentajeCompensacion)/100;
                                                floatCompensacionSolidaria += floatCompensacion;
                                            }
                                        }
                                    }
                                    else
                                    {
                                        floatimpuestoProductoSelec  = ( (precio_total + floatImpuestoIce) * intValorImpuesto) / 100;
                                        floatImpuestoOtros          += floatimpuestoProductoSelec;
                                        acum_impuestoOtros          += floatimpuestoProductoSelec;
                                    }

                                    floatImpuestoTotal += floatimpuestoProductoSelec;
                                }
                            }

                            acum_impuesto += floatImpuestoTotal;

                            redondearDetalleVisualizacion();

                            var rec = new ListadoDetalleOrden({
                                                                 'codigo':                producto[0], 
                                                                 'informacion':           producto[1], 
                                                                 'precio_uni':            precio_unitario.toFixed(2), 
                                                                 'precio':                precio_proporcional.toFixed(2), 
                                                                 'cantidad':              cantidad, 
                                                                 'descuento':             descuento, 
                                                                 'tipo':                  tipo, 
                                                                 'tipoOrden':             tipoOrden, 
                                                                 'fechaActivacion':       '', 
                                                                 'fechaDesde':            f_inicio, 
                                                                 'fechaHasta':            f_fin, 
                                                                 'puntoId':               puntoId, 
                                                                 'descripcion':           producto[1], 
                                                                 'tieneImpuesto':         '', 
                                                                 'impuesto':              floatImpuestoTotal,
                                                                 'impuestoIva':           floatImpuestoIva,
                                                                 'impuestoIce':           floatImpuestoIce,
                                                                 'impuestoOtros':         floatImpuestoOtros,
                                                                 'compensacionSolidaria': floatCompensacion,
                                                                 'porcentajeImpuesto':    intValorImpuesto,
                                                                 'porcentajeImpuestoIce': intPorcentajeImpIce});
                            store.add(rec);

                            limpiar_detalle_catalogo();
                        }
                        else
                        {
                            Ext.Msg.alert('Atención', 'El precio proporcional debe ser mayor a cero');
                        }//( parseFloat(precio_proporcional.toFixed(2)) > 0 )
                    },
                    failure: function(response)
                    {
                        Ext.Msg.alert('Error ','Error: ' + response.statusText);
                    }
                });
            }//( parseFloat(precio_unitario.toFixed(2)) > 0 )
            else
            {
                Ext.Msg.alert('Atención', 'El producto debe tener un precio mayor a cero');
            }
        }
        else
        {
            $('#mensaje').removeClass('campo-oculto').html("No existe funcion precio, no se puede agregar el producto");
        }
    }
    else
    {
        Ext.Msg.alert('Atención', 'Debe escribir una cantidad mayor a cero');
    }
}
/**
 * Documentación para agregar_detalle_portafolio
 * Función que sirve para agregar un detalle a la factura 
 * 
 * @author Edgar Holguin <eholguin@telconet.ec>
 * @version 1.1 03-03-2017 Se modifica para agregar detalle de cargo por reproceso de débito.
 * 
 * @author  telcos
 * @version 1.0
 * 
 */
function agregar_detalle_portafolio()
{
    //Obtener informacion del formulario
    var info_producto   = formulario.planes.value;
    var producto        = info_producto.split("-");
    var cantidad        = formulario.cantidad_plan.value;
    var tipoOrden       = formulario.tipoOrden.value;
    var puntoId         = formulario.punto_id.value;
    var precio_unitario = 0;
    var precio_total    = 0;
    var descuento       = 0;
    var prorrateo       = formulario.seProrratea.value;
    var tieneImpuesto   = formulario.tieneImpuesto.value;
    var intImpuestoId   = 0;
    var strPagaIce      = document.getElementById("strPagaIce").value;
    
    var strTieneDetalleRep = 'N';
    
    verificarSiClienteEsCompensado();

    if( parseFloat(cantidad) > 0 )
    {
        precio_unitario = formulario.precio.value;
        descuento       = formulario.descuento_plan.value;
        
        if( parseFloat(precio_unitario) > 0 )
        {
            if(boolPagaIce)
            {
                if(document.getElementById('aplicaIce').checked)
                {
                    strPagaIce = 'SI';
                } 
                else 
                {
                    strPagaIce = 'NO';
                }
            }

            if( boolPermisoSeleccionarImpuesto )
            {
                intImpuestoId = Ext.getCmp('cmbImpuesto').getValue();

                if( intImpuestoId > 0 && intImpuestoId != null )
                {
                    var strImpuesto = Ext.getCmp('cmbImpuesto').getRawValue();
                    strImpuesto = strImpuesto.replace('IVA ','');
                    strImpuesto = strImpuesto.replace('ICE ','');
                    strImpuesto = strImpuesto.replace('ITBMS ','');
                    strImpuesto = strImpuesto.replace('% ','');
                    strImpuesto = strImpuesto.trim();

                    tieneImpuesto = parseInt(strImpuesto);
                }
            }

            /*Pasos:
             * Calculo la cantidad de dias del proporcional
             * Saco los dias totales del mes
             * Obtengo el precio nuevo
             * Verificar la variable de prorrateo, segun eso se calcula o no*/

            var f_inicio = document.getElementById('formulario').feDesdeFacturaPost.value;
            var f_fin    = document.getElementById('formulario').feHastaFacturaPost.value;
            //Fecha inicio
            var mesInicio = parseInt(f_inicio.substring(5, 7));
            var anoInicio = f_inicio.substring(0, 4);
            //Fecha fin
            var mesFin = parseInt(f_fin.substring(5, 7));
            var anoFin = f_fin.substring(0, 4);

            var f_inicio_mod = f_inicio.substring(8, 10) + "-" + f_inicio.substring(5, 7) + "-" + f_inicio.substring(0, 4);
            var f_fin_mod    = f_fin.substring(8, 10) + "-" + f_fin.substring(5, 7) + "-" + f_fin.substring(0, 4);

            //Dias proporcional
            var diasP = mostrarDias(f_inicio_mod, f_fin_mod);
            
            //Total del dias
            var diasT = 0;

            Ext.Ajax.request
            ({
                url: strUrlGetFechasDiasPeriodoAjax,
                method: 'post',
                timeout: 999999999,
                params:
                { 
                    strFechaActivacion: f_inicio
                },
                success: function(response)
                {
                    var objRespuesta                = Ext.JSON.decode(response.responseText);
                        diasT                       = objRespuesta.intTotalDiasMes;
                    var floatimpuestoProductoSelec  = 0;//Prorrateo del producto, buscar carateristica si prorratea **proximamente
                    var precio_proporcional         = (precio_unitario * diasP) / diasT;
                        precio_proporcional         = (Math.round(precio_proporcional * 100)/100);
                    var floatCompensacion           = 0;

                    //Precio total
                    precio_total = (precio_unitario * cantidad);

                    if (prorrateo == 'S')
                    {
                        precio_total = (precio_proporcional * cantidad);
                    }

                    if( parseFloat(precio_proporcional.toFixed(2)) > 0 )
                    {
                        acum_subtotal     += precio_total;
                        acum_descuento    += parseFloat(descuento);

                        if(strPagaIva=='S')
                        {
                            floatimpuestoProductoSelec = (( (precio_total - parseFloat(descuento)) * tieneImpuesto) / 100);
                            acum_impuesto              += floatimpuestoProductoSelec;
                            acum_impuestoIva           += floatimpuestoProductoSelec;

                            if( 'SI' == strEsCompensado )
                            {
                                floatCompensacion          =  (((precio_total - parseFloat(descuento)) * floatPorcentajeCompensacion)/100)/cantidad;
                                floatCompensacionSolidaria += floatCompensacion * cantidad;
                            }
                        }
                        else
                        {
                            acum_impuesto += 0;
                        }

                        redondearDetalleVisualizacion();

                        //Transformacion y redondeo
                        precio_total = parseFloat(Math.round(precio_total * 100) / 100).toFixed(2);

                        var rec = new ListadoDetalleOrden({
                                                                'codigo':               producto[0], 
                                                                'informacion':          producto[1],
                                                                'precio_uni':           precio_unitario,
                                                                'precio':               precio_proporcional.toFixed(2),
                                                                'cantidad':             cantidad,
                                                                'descuento':            descuento,
                                                                'tipo':                 "PL",
                                                                'tipoOrden':            tipoOrden,
                                                                'fechaActivacion':      '',
                                                                'descripcion':          'Factura proporcional desde: '+f_inicio+' hasta: '+f_fin,
                                                                'fechaDesde':            f_inicio,
                                                                'fechaHasta':            f_fin,
                                                                'puntoId':               puntoId,
                                                                'tieneImpuesto':         tieneImpuesto,
                                                                'impuesto':              floatimpuestoProductoSelec,
                                                                'impuestoIva':           floatimpuestoProductoSelec,
                                                                'impuestoIce':           0,
                                                                'impuestoOtros':         0,
                                                                'compensacionSolidaria': floatCompensacion,
                                                                'porcentajeImpuesto'   : tieneImpuesto
                                                        });
                        store.add(rec);


                        // Verifico si existe detalle por cargo de reproceso
                        for (var i = 0; i < store.getCount(); i++)
                        {
                            arrayDetalle = store.getAt(i).data; 
                            if(arrayDetalle['informacion']=='Cargo por Gestion de Cobranza')
                            {
                              strTieneDetalleRep  = 'S';
                            }
                        }             

                        if('N'=== strTieneDetalleRep && !boolFacturacionAgrupada )
                        {
                          // Se agrega detalle de solicitud por cargo de reproceso          

                          Ext.Ajax.request({
                              url: getSolicitudReprocesoAjax,
                              method: 'post',
                              timeout: 99999,
                              success: function(response){

                                var objRespuesta = Ext.JSON.decode(response.responseText);

                                if(objRespuesta.intCantidadSolicitudes > 0)
                                {                        
                                    var floatPrecioTotal = (objRespuesta.floatPrecioUnitario * objRespuesta.intCantidadSolicitudes);

                                    var floatImpuestoProductoRep = ( floatPrecioTotal * tieneImpuesto) / 100;

                                    acum_subtotal    += floatPrecioTotal;                                 
                                    acum_impuesto    += floatImpuestoProductoRep;
                                    acum_impuestoIva += floatImpuestoProductoRep; 

                                    redondearDetalleVisualizacion();

                                    var rec = new ListadoDetalleOrden({ 
                                                                       'codigo':                objRespuesta.intProductoId, 
                                                                       'informacion':           objRespuesta.strDescripcionProd,
                                                                       'precio_uni':            objRespuesta.floatPrecioUnitario,
                                                                       'precio':                floatPrecioTotal, 
                                                                       'cantidad':              objRespuesta.intCantidadSolicitudes, 
                                                                       'descuento':             0, 
                                                                       'tipo':                  'PR', 
                                                                       'tipoOrden':             'REP', 
                                                                       'fechaActivacion':       '',
                                                                       'puntoId':               puntoId,
                                                                       'descripcion':           objRespuesta.strDescripcionProd,
                                                                       'tieneImpuesto':         tieneImpuesto,
                                                                       'impuesto':              0,
                                                                       'impuestoIva':           floatImpuestoProductoRep,
                                                                       'impuestoIce':           0,
                                                                       'impuestoOtros':         0,
                                                                       'compensacionSolidaria': 0,
                                                                       'porcentajeImpuesto'   : tieneImpuesto
                                                                   });                                                     
                                    store.add(rec);  
                                }                    

                              },
                              failure: function(response)
                              {
                                  Ext.Msg.alert('Error ','Error: ' + response.statusText);
                              }
                          });
                        }                
                        limpiar_detalle_portafolio();
                    }
                    else
                    {
                        Ext.Msg.alert("Atención", "El precio proporcional debe ser mayor a cero");
                    }//( parseFloat(precio_proporcional.toFixed(2)) > 0 )
                },
                failure: function(response)
                {
                    Ext.Msg.alert('Error ','Error: ' + response.statusText);
                }
            });
        }//( parseFloat(precio_unitario) > 0 )
        else
        {
            Ext.Msg.alert("Atención", "Debe ingresar un valor de precio mayor a cero");
        }
    }//( parseFloat(cantidad) > 0 )
    else
    {
        Ext.Msg.alert("Atención", "Debe ingresar una cantidad mayor a cero");
    }
}

function limpiar_detalle_portafolio()
{
    if (formulario.cantidad_plan)
    {
        formulario.cantidad_plan.value = "";
    }

    if (formulario.descuento_plan)
    {
        formulario.descuento_plan.value = "";
    }

    if (formulario.precio)
    {
        formulario.precio.value = "";
    }

    if (formulario.tipoOrden)
    {
        formulario.tipoOrden.value = "";
    }

    if (formulario.planes)
    {
        formulario.planes.options[0].selected = true;
    }
}

function limpiar_detalle_catalogo()
{    
    selectTags = formulario.getElementsByTagName("select");

    for(var i = 0; i < selectTags.length; i++)
    {
        selectTags[i].selectedIndex =0;
    }  

    document.getElementById("contenido").innerHTML = "";
}

function  limpiar_detalle()
{
    limpiar_detalle_catalogo();
}

function mostrarDias(f_inicio, f_fin)
{
    var fechaInicio = f_inicio;
    var fechaFin = f_fin;
    var diasTotal = 0;

    if (fechaInicio.length != 10 || fechaFin.length != 10)
    {
        document.getElementById("diasDisfrutados").value = 0;
    }
    else
    {
        //Separamos las fechas en dias, meses y años
        var diaInicio = fechaInicio.substring(0, 2);
        var mesInicio = fechaInicio.substring(3, 5);
        var anoInicio = fechaInicio.substring(6, 10);

        var diaFin = fechaFin.substring(0, 2);
        var mesFin = fechaFin.substring(3, 5);
        var anoFin = fechaFin.substring(6, 10);

        //Los meses empiezan en 0 por lo que le restamos 1
        mesFin = mesFin - 1;
        mesInicio = mesInicio - 1;

        //Creamos una fecha con los valores que hemos sacado
        var fInicio = new Date(anoInicio, mesInicio, diaInicio);
        var fFin = new Date(anoFin, mesFin, diaFin);

        if (fFin > fInicio) {

            //Para sumarle 365 días tienen que haber 2 años de diferencia
            //Si no solamente sumo los días entre meses
            anoInicio++;
            while (anoFin > anoInicio) {
                //alert("Entro aquí si hay dos años de diferencia");

                if (esBisiesto(anoFin))
                    dias_e_anio = 366;
                else
                    dias_e_anio = 365;
                diasTotal = diasTotal + dias_e_anio;
                anoFin--;
            }

            //Para sumarle los días de un mes completo, tengo que ver que haya diferencia de 2 meses
            mesInicio++;
            while (mesFin > mesInicio)
            {
                dias_e_mes = getDays(mesFin - 1, anoFin);
                diasTotal = diasTotal + dias_e_mes;
                mesFin--;
            }

            //Solamente falta sumar los días 
            mesInicio--;
            if (mesInicio == mesFin) {
                diasTotal = diaFin - diaInicio + 1;
            }
            else
            {
                //Saco los días desde el mesInicio hasta fin de mes
                dias_e_mes = getDays(mesInicio, anoInicio);
                diasTotal = diasTotal + (dias_e_mes - diaInicio) + 1;
                //ahora saco los días desde el principio de mesFin hasta el día
                diasTotal = diasTotal + parseInt(diaFin);
            }
        }
        //Si la fechaFin es mayor
        else if (fechaFin < fechaInicio) {
            alert("La fecha de fin no puede ser mayor que la fecha de inicio");
            diasTotal = 0;
        }
        //Si las fechas son iguales
        else {
            diasTotal = 1;
        }
    }
    return diasTotal;
}

function esBisiesto(ano) {
    if (ano % 4 == 0)
        return true
    /* else */
    return false
}

function getDays(month, year) {

    var ar = new Array(12)
    ar[0] = 31 // Enero
    if (esBisiesto(year))
        ar[1] = 29
    else
        ar[1] = 28

    ar[2] = 31 // Marzo
    ar[3] = 30 // Abril
    ar[4] = 31 // Mayo
    ar[5] = 30 // Junio
    ar[6] = 31 // Julio
    ar[7] = 31 // Agosto
    ar[8] = 30 // Septiembre
    ar[9] = 31 // Octubre
    ar[10] = 30 // Noviembre
    ar[11] = 31 // Diciembre
    return ar[month];
}


function validaNumerosConDecimales(e, field) 
{
    var key = e.keyCode ? e.keyCode : e.which

    if (key == 8) return true;

    if (key > 47 && key < 58)
    {
        if (field.value == "") return true;
        
        var existePto = (/[.]/).test(field.value);
        if (existePto === false)
        {
            regexp = /.[0-9]{10}$/;
        }
        else 
        {
            regexp = /.[0-9]{2}$/;
        }
        
        return !(regexp.test(field.value));
    }

    if (key == 46)
    {
        if (field.value == "") return false;
        var regexp = /^[0-9]+$/;
        return regexp.test(field.value);
    }

    return false;
}

function verificarSiClienteEsCompensado()
{
    var intIdImpuestoSelected = intIdImpuestoIvaActivo;
    
    if( boolPermisoSeleccionarImpuesto )
    {
        intIdImpuestoSelected = Ext.getCmp('cmbImpuesto').getValue();
    }
    
    if( boolPuedeCompensar )
    {
        if( intIdImpuestoSelected == intIdImpuestoIvaActivo && (strOficinaEsCompensado == "S" || strClienteEsCompensado == "S") )
        {
            /**
             * Si tengo el rol para poder marcar como compensado al cliente se debe verificar siempre que el checkbox de compensación este 
             * marcado y que la oficina sea una oficina de compensación
             */
            if( document.getElementById('esCompensado').checked )
            {
                document.getElementById("msgCompensacion").style.display = "";
                strEsCompensado = 'SI';
            } 
            else 
            {
                document.getElementById("msgCompensacion").style.display = "none";
                strEsCompensado = 'NO';
            }
        }//( strOficinaEsCompensado == "S" )
    }
    else
    {
        verificarSiOficinaEImpuestoCompensa();
    }
}

function verificarSiOficinaEImpuestoCompensa()
{
    var intIdImpuestoSelected   = intIdImpuestoIvaActivo;
    var strTmpClienteCompensado = "S";
    
    if( boolPermisoSeleccionarImpuesto )
    {
        intIdImpuestoSelected = Ext.getCmp('cmbImpuesto').getValue();
    }
    
                        
    if( (intIdImpuestoSelected == intIdImpuestoIvaActivo && strOficinaEsCompensado == "S") 
         || (strClienteEsCompensado == "S" && intIdImpuestoSelected == intIdImpuestoIvaActivo) )
    {
        strTmpClienteCompensado = "S";
        strEsCompensado         = "SI";
        
        document.getElementById("msgCompensacion").style.display = "";

        if( boolPuedeCompensar )
        {
            if( document.getElementById('esCompensado') != null )
            {
                document.getElementById('esCompensado').disabled = false;
                document.getElementById('esCompensado').checked  = true;
            }
        }
    }/*( (intIdImpuestoSelected == intIdImpuestoIvaActivo && strOficinaEsCompensado == "S") 
         || (strClienteEsCompensado == "S" && intIdImpuestoSelected == intIdImpuestoIvaActivo) )*/
        
    else
    {
        strTmpClienteCompensado = "N";
        strEsCompensado         = "NO";
        
        document.getElementById("msgCompensacion").style.display = "none";

        if( boolPuedeCompensar )
        {
            if( strOficinaEsCompensado == "S" || strClienteEsCompensado == "S" )
            {
                if( document.getElementById('esCompensado') != null )
                {
                    document.getElementById('esCompensado').disabled = true;
                    document.getElementById('esCompensado').checked  = false;
                }
            }
        }
    }
    
    return strTmpClienteCompensado;
}


function verificarRadioButtonChecked()
{
    var radios = document.getElementsByName('info');

    for (var i = 0, length = radios.length; i < length; i++)
    {
        if (radios[i].checked)
        {
            store.loadData([],false);
            store.load();
            
            break;
        }
    }
}


function redondearDetalleVisualizacion()
{
    //RENDODEOS
    acum_subtotal              = Math.round(acum_subtotal * 100)/100;
    acum_impuestoIce           = Math.round(acum_impuestoIce * 100)/100;
    acum_descuento             = Math.round(acum_descuento * 100)/100;
    acum_impuestoIva           = Math.round(acum_impuestoIva * 100)/100;
    acum_impuestoOtros         = Math.round(acum_impuestoOtros * 100)/100;
    floatCompensacionSolidaria = Math.round(floatCompensacionSolidaria * 100)/100;
    acum_subtotal_ice          = acum_subtotal + acum_impuestoIce - acum_descuento;
    acum_total                 = (Math.round(acum_subtotal_ice * 100)/100) + (Math.round(acum_impuestoIva * 100)/100)
                                  + (Math.round(acum_impuestoOtros * 100)/100) - (Math.round(floatCompensacionSolidaria * 100)/100);
    acum_total                 = Math.round(acum_total * 100)/100;

    //VISUALIZACION
    document.getElementById("subtotalDetalle").innerHTML       = acum_subtotal.toFixed(2);
    document.getElementById("descuentoDetalle").innerHTML      = acum_descuento.toFixed(2);
    document.getElementById("iceDetalle").innerHTML            = acum_impuestoIce.toFixed(2);
    document.getElementById("ivaDetalle").innerHTML            = acum_impuestoIva.toFixed(2);
    document.getElementById("otrosImpDetalle").innerHTML       = acum_impuestoOtros.toFixed(2);
    document.getElementById("compensacionSolidaria").innerHTML = floatCompensacionSolidaria.toFixed(2);
    document.getElementById("totalDetalle").innerHTML          = acum_total.toFixed(2);
}

function actualizaDescripcion(textInput) 
{
    var cantidad                 = $("#cantidad").val();
    var funcion_precio           = $("#funcion_precio").val();
    var cantidad_caracteristicas = $("#cantidad_caracteristicas").val();
    var caracteristicas          = 'caracteristicas_';
    var caracteristica_nombre    = 'caracteristica_nombre_';
    var descripcion_producto     = $("producto").val();//LO SACO DEL COMBO

    var esIntenetLite            = (descripcion_producto === "INTERNET SMALL BUSINESS" ?  true : false);
    
    var precio_unitario             = 0;
    var precio_total                = 0;      
    var caracteristicas_n           = "";
    var caracteristica_nombre_n     = "";
    var valor_caract                = new Array();
    var nombre_caract               = new Array();
    
    //escenario solo para pool de recursos de cloud IAAS
    
    if(textInput || cantidad_caracteristicas>=1)
    {
        for (var x = 0; x < cantidad_caracteristicas; x++)
        { 
            var muestraGrupoNegocioDescProd = true;
            caracteristicas_n         = caracteristicas + x;            
            caracteristica_nombre_n   = caracteristica_nombre + x;
            valor_caract[x]           = eval(caracteristicas_n).value;                          
            if(valor_caract[x]==null || valor_caract[x]=='')
            {                
                return false;
            }            
            nombre_caract[x]          = eval(caracteristica_nombre_n).value;

            if(esIntenetLite && nombre_caract[x] == '[Grupo Negocio]')
            {
                muestraGrupoNegocioDescProd = false;
            }

            if(muestraGrupoNegocioDescProd)
            {
                descripcion_producto      += ' '+valor_caract[x];
            }

        } 

        for (var y = 0; y < nombre_caract.length; y++)
        {
            funcion_precio = replaceAll(funcion_precio, nombre_caract[y], valor_caract[y]);            
        }

        try
        {
            precio_unitario = eval(funcion_precio);
            if(isNaN(precio_unitario))
            {
                throw null;
            }
        }
        catch (err)
        {
            Ext.Msg.alert('Función precio mal definida, No se puede procesar este servicio');
        }
    }    
    
    if(!isNaN(precio_unitario))
    {
        precio_total  = (precio_unitario * cantidad);
    }
    else
    {
        precio_unitario = "";
        
        Ext.Msg.alert('Atención', 'Los valores ingresados no cumplen la función precio, favor verificar');
    }

    if(document.getElementById('precio_venta'))
    {
        document.getElementById('precio_venta').value = precio_unitario;
    }

    if(document.getElementById('precio_unitario'))
    {
        document.getElementById('precio_unitario').value = precio_unitario;
    }

    if(document.getElementById('precio_total'))
    {
        document.getElementById('precio_total').value = precio_total;
    }

    if(document.getElementById('precio_venta'))
    {
        document.getElementById('precio_venta').disabled = false;
    }
}

//Se duplica la actualizaTotal de editarServio por motivo errores en consola
function actualizaTotal () 
{
    var precioNegociacion = $("#precio_venta").val();
    var cantidad          = $("#cantidad").val(); 
    document.getElementById('precio_total').value         =  precioNegociacion * cantidad;
}


//Funcion que valida que el evento sea solo numerico
function validaSoloNumeros(e) 
{
     tecla = (document.all) ? e.keyCode : e.which;
    //Tecla de retroceso para borrar, siempre la permite
  
    if (tecla==8)
    {
        return true;
    }       
    // Patron de entrada, en este caso solo acepta numeros
    patron =/[0-9\.]/;
    tecla_final = String.fromCharCode(tecla);
    return patron.test(tecla_final); 
}