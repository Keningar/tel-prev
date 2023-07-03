var permisoSeleccionarImpuesto      = null;
var boolPermisoSeleccionarImpuesto  = null;
var permisoPagaIce                  = null;
var boolPagaIce                     = null;
var permisoPuedeCompensar           = null;
var boolPuedeCompensar              = null;
var strEsCompensado                 = null;
var storeOficinasFacturacion        = null;
var permisoEditarPrecioFactDet      = null; 
var boolEditarPrecioFactDet         = null;
var strTipoFactura                  = '';

Ext.onReady(function () 
{
    permisoSeleccionarImpuesto      = $("#ROLE_67-4277");
    boolPermisoSeleccionarImpuesto  = (typeof permisoSeleccionarImpuesto === 'undefined') ? false 
                                      : (permisoSeleccionarImpuesto.val() == 1 ? true : false);
    permisoPagaIce                  = $("#ROLE_67-4297");
    boolPagaIce                     = (typeof permisoPagaIce === 'undefined') ? false : (permisoPagaIce.val() == 1 ? true : false);
    permisoPuedeCompensar           = $("#ROLE_67-4777");
    boolPuedeCompensar              = (typeof permisoPuedeCompensar === 'undefined') ? false : (permisoPuedeCompensar.val() == 1 ? true : false);
    permisoEditarPrecioFactDet      = $("#ROLE_67-5357");
    boolEditarPrecioFactDet         = (typeof permisoEditarPrecioFactDet === 'undefined') ? false 
                                      : (permisoEditarPrecioFactDet.val() == 1 ? true : false);    
                                        
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
    
    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 1,
        listeners: {
            beforeedit: function(editor, e) 
            {
                if(boolEditarPrecioFactDet)
                {
                    if(undefined != Ext.getCmp('precioDetFact'))
                    {
                        Ext.getCmp('precioDetFact').setDisabled(false); 
                    }
                }
            },
            edit: function(editor, e) 
            { 
                record = e.record;
                store  = e.grid.getStore(); 
                if(boolEditarPrecioFactDet)
                {
                
                    if(e.field == 'precio') 
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
                          record.set('precio', e.originalValue);
                          Ext.Msg.alert('Error ', 'Valor de precio no permitido. ');
                          return false;
                        }                   

                        if(!(Utils.REGEX_PRECIO.test(e.value)))
                        {                     
                          record.set('precio', e.originalValue);
                          Ext.Msg.alert('Error ', 'Formato de precio no v\u00e1lido, el valor ingresado debe tener hasta 2 decimales Ej: (2.50)');
                          return false;
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
                            
                            if(record.data.impuestoIce > 0 || ('SI' === strPagaIce   && record.data.impuestoIce > 0))
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
                            objItemFirst.data.impuesto              = acum_impuesto;
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

    var objAnio = Ext.create('Ext.form.field.Text', {
        fieldLabel: 'Año:',
        name: 'idTxtAnio',
        itemId: 'idTxtAnio',
        id: 'idTxtAnio',
        autofocus: true,
        enableKeyEvents: true,
        width: 150,
        labelAlign : 'right',
        labelWidth: 40,
        labelPad: 10,
        renderTo: 'textFieldAnio',
        maskRe: /[0-9]/,
        listeners: {
            render: function (field) {
                var objDate = new Date();
                var intYear = objDate.getFullYear();
                Ext.getCmp('idTxtAnio').setValue(intYear);
            }
        }
    });

    var objMesStore = Ext.create('Ext.data.Store', {
        fields: ['valor', 'signo'],
        data: [
            {"valor": "1", "signo": "ENERO"},
            {"valor": "2", "signo": "FEBRERO"},
            {"valor": "3", "signo": "MARZO"},
            {"valor": "4", "signo": "ABRIL"},
            {"valor": "5", "signo": "MAYO"},
            {"valor": "6", "signo": "JUNIO"},
            {"valor": "7", "signo": "JULIO"},
            {"valor": "8", "signo": "AGOSTO"},
            {"valor": "9", "signo": "SEPTIEMBRE"},
            {"valor": "10", "signo": "OCTUBRE"},
            {"valor": "11", "signo": "NOVIEMBRE"},
            {"valor": "12", "signo": "DICIEMBRE"}
        ]
    });

    var month = new Array();
    month[1] = "ENERO";
    month[2] = "FEBRERO";
    month[3] = "MARZO";
    month[4] = "ABRIL";
    month[5] = "MAYO";
    month[6] = "JUNIO";
    month[7] = "JULIO";
    month[8] = "AGOSTO";
    month[9] = "SEPTIEMBRE";
    month[10] = "OCTUBRE";
    month[11] = "NOVIEMBRE";
    month[12] = "DICIEMBRE";

    var cmbMes = new Ext.form.ComboBox({
        xtype: 'combobox',
        store: objMesStore,
        id: 'intIdMes',
        name: 'intIdMes',
        valueField: 'valor',
        displayField: 'signo',
        fieldLabel: 'Mes',
        width: 170,
        labelAlign : 'right',
        labelWidth: 40,
        labelPad: 10,
        mode: 'local',
        allowBlank: true,
        editable: false,
        listeners: {
            select: function (combobox) {
                var strMes = month[combobox.value];
                combobox.setRawValue(strMes);
            },
            render: function (combobox) {
                var objDate = new Date();
                var intMonth = objDate.getMonth();
                intMonth = intMonth + 1;
                var strMes = month[intMonth];
                combobox.setRawValue(strMes);
            }
        },
        renderTo: 'strFechaConsumo'
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
            {name: 'descripcion',           type: 'string'},
            {name: 'login',                 type: 'string'},
            {name: 'tieneImpuesto',         type: 'string'},
            {name: 'impuesto',              type: 'float'},
            {name: 'impuestoIva',           type: 'float'},
            {name: 'impuestoIce',           type: 'float'},
            {name: 'impuestoOtros',         type: 'float'},
            {name: 'compensacionSolidaria', type: 'float'},
            {name: 'porcentajeImpuesto',    type: 'float'},
            {name: 'porcentajeImpuestoIce', type: 'float'},
            {name: 'idServicio',            type: 'int'}            
        ]
    });

    store = Ext.create('Ext.data.Store',
    {
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
                strPagaIva: strPagaIva,
                strOpcionPrecargadaSinFrecuencia: strPrecargadaSinFrecuencia,
                strIdFactura: strIdFactura
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
                store.getProxy().extraParams.strPagaIce      = strPagaIce;
                store.getProxy().extraParams.strEsCompensado = strEsCompensado;
                store.getProxy().extraParams.intImpuestoId   = intImpuestoId;
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
                            array_data_caract['descripcion'] = valor;
                        j++;
                    }
                    
                    informacion.push(array_data_caract);
                    array_data_caract = {};
                    j = 0;
                }
                store.getProxy().extraParams.informacionGrid = JSON.stringify(informacion);
            },
            load: function (store) 
            {
                acum_subtotal              = 0;
                acum_impuesto              = 0;
                acum_impuestoIce           = 0;
                acum_subtotal_ice          = 0;
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
                    
                    acum_subtotal              += (record.data.precio * record.data.cantidad);
                    acum_impuestoIce           += (record.data.impuestoIce * record.data.cantidad);
                    acum_impuesto              += (record.data.impuesto * record.data.cantidad);
                    acum_impuestoIva           += (record.data.impuestoIva * record.data.cantidad);
                    acum_impuestoOtros         += (record.data.impuestoOtros * record.data.cantidad);
                    floatCompensacionSolidaria += (record.data.compensacionSolidaria * record.data.cantidad);
                   
                });
                
                redondearDetalleVisualizacion();
             
            }
        }
    });

    grid = Ext.create('Ext.grid.Panel', 
    {
        store: store,
        id:'gridDetallesFact',
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
                width: 160,
                dataIndex: 'informacion'
            },
            {
                text: 'Login',
                width: 160,
                dataIndex: 'login'
            }, 
            {
                text: 'Descripcion',
                width: 180,
                dataIndex: 'descripcion',
                field: 
                {
                    xtype: 'textfield',
                    allowBlank: false,
                }
            }, 
            {
                text: 'Fe. Activacion',
                width: 130,
                dataIndex: 'fechaActivacion'
            }, 
            {
                text: 'Precio',
                width: 70,
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
                text: '$ Descuento',
                dataIndex: 'descuento',
                align: 'right',
                width: 70
            },
            {
                header: 'Acciones',
                xtype: 'actioncolumn',
                width: 70,
                align: 'center',
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
            }, 
            {
                text: 'Impuesto',
                dataIndex: 'tieneImpuesto',
                hidden: true
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
        selModel: 
        {
            selType: 'cellmodel'
        },
        dockedItems: 
        [{
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
                    handler: function() {
                        var vExportContent = grid.getExcelXml();
                        document.location = 'data:application/vnd.ms-excel;base64,' + Base64.encode(vExportContent);
                    }

                }
            ]
        }],
        renderTo: 'lista_informacion_pre_cargada',
        width: 950,
        height: 200,
        title: 'Listado de servicios',
        frame: true,
        plugins: [cellEditing]
    });
    
    storeOficinasFacturacion = Ext.create('Ext.data.Store', 
    {
        storeId: "storeIdOficinaFact",
        autoLoad: true,
        proxy: 
        {
            type: 'ajax',
            method: 'post',
            url : strUrlGetOficinasFacturacion,
            timeout: 900000,
            reader: 
            {
                type: 'json',
                root: 'encontrados'
            }
        },
        fields:
        [
            {name: 'intIdOficina',     mapping: 'intIdOficina'},
            {name: 'strNombreOficina', mapping: 'strNombreOficina'},
            {name: 'strEsCompensado',  mapping: 'strEsCompensado'}
        ],
        listeners:
        {
            load: function(store, records)
            {                
                Ext.each(records, function(record)
                {
                    if( record.get('intIdOficina') == intIdOficina )
                    {
                        Ext.getCmp('cmbOficinaFacturacion').setValue(record.get('intIdOficina'));
                    }
                });
            }
        }
    });

    var cmbOficinaFacturacion = new Ext.form.ComboBox
    ({
        xtype: 'combobox',
        store: storeOficinasFacturacion,
        labelAlign : 'left',
        id: 'cmbOficinaFacturacion',
        name: 'cmbOficinaFacturacion',
        valueField:'intIdOficina',
        displayField:'strNombreOficina',
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
                fn:function(comp, record, index)
                {
                    document.getElementById("numFactura").innerHTML       = '';
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
                    if(strClonarFactura == "N")
                    {
                        verificarRadioButtonChecked();
                    }                    
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
            {name: 'strNumeracion',   mapping: 'strNumeracion'}
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
    
    if( strOpcionesFechaConsumo === "S" )
    {
        var dateFechaDiaInicio = new Ext.form.DateField
        ({
            name: 'dateDiaInicio',
            allowBlank: false,
            format: 'Y-m-d',
            renderTo: 'dateFechaDiaInicio',
            id: 'dateDiaInicio',
            editable: false
        });

        var dateFechaDiaFin = new Ext.form.DateField
        ({
            name: 'dateDiaFin',
            allowBlank: false,
            format: 'Y-m-d',
            renderTo: 'dateFechaDiaFin',
            id: 'dateDiaFin',
            editable: false
        });
    
        var dateFechaMesInicio = new Ext.form.TextField
        ({
            xtype: 'textfield',
            fieldLabel: '',
            labelAlign: 'left',                
            name: 'dateMesInicio',
            id: 'dateMesInicio',
            renderTo: 'dateFechaMesInicio',
            width: '130px',
            readOnly: true,
            editable: false
        });
        
        var dateFechaMesFin = new Ext.form.TextField
        ({
            xtype: 'textfield',
            fieldLabel: '',
            labelAlign: 'left',                
            name: 'dateMesFin',
            id: 'dateMesFin',
            renderTo: 'dateFechaMesFin',
            width: '130px',
            readOnly: true,
            editable: false
        });

        $('#dateMesInicio-inputEl').monthpicker({dateFormat: 'mm-yyyy'});
        $('#dateMesFin-inputEl').monthpicker({dateFormat: 'mm-yyyy'});
    }

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
    if (typeof strClonarFactura !== 'undefined') 
    {
        if(strClonarFactura == "S")
        {
            clonacion();
        }
    }
     
});


function getNumeroFactura(intIdNumeracion)
{
    Ext.MessageBox.wait("Obteniendo número de factura...");
    
    $.ajax
    ({
        type: "POST",
        data:{ "intIdNumeracion": intIdNumeracion },
        url: strUrlGetNumeroFactura,
        dataType: 'json',
        success: function (resultado) 
        {
            Ext.MessageBox.hide();
            
            document.getElementById("numFactura").innerHTML       = '';
            document.getElementById("strNumeroFacturacion").value = '';
            
            if(resultado.error)
            {
                Ext.Msg.alert('Atención', resultado.mensaje);
            }
            else
            {               
                document.getElementById("numFactura").innerHTML       = resultado.numeroFactura;
                document.getElementById("strNumeroFacturacion").value = resultado.numeroFactura;
            }
        },
        failure: function()
        {
            Ext.MessageBox.hide();
            
            document.getElementById("numFactura").innerHTML       = '';
            document.getElementById("strNumeroFacturacion").value = '';
            
            Ext.Msg.alert('Error', 'Error al traer el numero de facturacion de la oficina seleccionada');
        }
    });
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
                    array_data_caract['descripcion'] = valor;
                }

                if (j == 10)
                {
                    array_data_caract['login'] = valor;
                }

                if (j == 11)
                {
                    array_data_caract['tieneImpuesto'] = valor;
                }

                if (j == 13)
                {
                    array_data_caract['impuesto'] = valor;
                }

                if (j == 16)
                {
                    array_data_caract['compensacionSolidaria'] = valor;
                }
                if (j === 19)
                {
                    array_data_caract['idServicio'] = valor;
                }              
                
                j++;
            }

            informacion.push(array_data_caract);

            array_data_caract = {};
            j                 = 0;
        }

        var intIdOficinaFacturacion    = Ext.getCmp('cmbOficinaFacturacion').getValue();
        var intIdNumeracionFacturacion = Ext.getCmp('cmbNumeracionFacturacion').getValue();
        var strNumeroFacturacion;
       
        strNumeroFacturacion       = document.getElementById("strNumeroFacturacion").value;
       
      
        
        
        var intImpuestoId              = 0;
        var strPagaIce                 = document.getElementById("strPagaIce").value;

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

        if( intIdOficinaFacturacion > 0 )
        {
            if( strNumeroFacturacion != '' && strNumeroFacturacion != null )
            {
                if (informacion.length > 0)
                {
                    var boolContinuar = true;

                    if ( strOpcionesFechaConsumo == "S" )
                    {
                        var strValorFeConsumoSelected  = document.getElementById("opcFeConsumoSelected").value;

                        if(strValorFeConsumoSelected == "feConsumo")
                        {
                            if( Ext.isEmpty(Ext.getCmp('intIdMes').getValue()) || Ext.isEmpty(Ext.getCmp('idTxtAnio').getValue()) )
                            {
                                Ext.Msg.alert("Atención", "Debe llenar el Mes y el Año de consumo");
                                boolContinuar = false;
                            }
                        }
                        else
                        {
                            var strValorRangoConsumoSeleccionado  = document.getElementById("opcRangoConsumoSelected").value;

                            if( strValorRangoConsumoSeleccionado == "consumoDias" )
                            {
                                if (Ext.getCmp('dateDiaInicio').getValue() > Ext.getCmp('dateDiaFin').getValue())
                                {
                                    Ext.Msg.alert("Atención", "La fecha inicial debe ser menor que la fecha final");
                                    boolContinuar = false;
                                }
                            }//( strValorRangoConsumoSeleccionado == "consumoDias" )
                            else
                            {
                                var dateMesInicio = parsearStringAFechaExtJs('dateMesInicio');
                                var dateMesFinal  = parsearStringAFechaExtJs('dateMesFin');

                                document.getElementById("txtFechaMesInicio").value = dateMesInicio;
                                document.getElementById("txtFechaMesFin").value    = dateMesFinal;

                                if( Ext.Date.parse(dateMesInicio , 'd-m-Y') > Ext.Date.parse(dateMesFinal , 'd-m-Y') )
                                {
                                    Ext.Msg.alert("Atención", "El mes inicial debe ser menor que el mes final");
                                    boolContinuar = false;
                                }//( dateMesInicio > dateMesFinal )
                            }//( strValorRangoConsumoSeleccionado == "consumoMeses" )
                        }//(strValorFeConsumoSelected == "feConsumo")
                    }// ( strOpcionesFechaConsumo == "S" )
                    
                    if ( strFechaConsumo === "S" && ( Ext.isEmpty(Ext.getCmp('intIdMes').getValue()) 
                                                      || Ext.isEmpty(Ext.getCmp('idTxtAnio').getValue()) ) )
                    {
                        Ext.Msg.alert("Atención", "Debe llenar el Mes y el Año de consumo");
                        boolContinuar = false;
                    }


                    if(boolContinuar)
                    {
                        loadMask('myLoading', true, 'Procesando');

                        document.getElementById("listado_informacion").value    = JSON.stringify(informacion);
                        document.getElementById("txtMes").value                 = Ext.getCmp('intIdMes').getValue();
                        document.getElementById("intIdOficina").value           = intIdOficinaFacturacion;
                        document.getElementById("intIdNumeracion").value        = intIdNumeracionFacturacion;
                        document.getElementById("intTxtIdImpuesto").value       = intImpuestoId;
                        document.getElementById('strPagaIce').value             = strPagaIce;
                        document.getElementById('strEsCompensado').value        = strEsCompensado;

                        if(boolFacturacionAgrupada)
                        {
                            document.getElementById("strFacturacionAgrupada").value = 'S';
                        }

                        document.formulario.submit();
                    }
                }
                else
                {
                    Ext.Msg.alert('Atención', "Ingrese detalles a la factura");
                }
            }
            else
            {
                Ext.Msg.alert('Atención', 'Debe seleccionar una oficina que contenga numeración para facturar');
            }
        }
        else
        {
            Ext.Msg.alert('Atención', 'Debe seleccionar una oficina de Facturación');
        }
    }//( parseFloat(acum_total).toFixed(2) > 0 )
    else
    {
        Ext.Msg.alert('Atención', 'No se pueden crear facturas con valor total en cero');
    }
}


function parsearStringAFechaExtJs(strIdFecha)
{
    var dateFechaResultante = null;
    var fechaSeleccionada   = document.getElementById(strIdFecha+'-inputEl').value;
    
    if( !Ext.isEmpty(fechaSeleccionada) )
    {
        if( typeof fechaSeleccionada === 'string')
        {
            var arrayFechaSeleccionada = fechaSeleccionada.split(', ');

            dateFechaResultante = "01-" + Utils.arrayMes[arrayFechaSeleccionada[0]] + "-" + arrayFechaSeleccionada[1];
        }
    }
    
    return dateFechaResultante;
}

    /**
    * 
    * @author  telcos
    * @version 1.0
    * 
    * @author Edgar Holguin <eholguin@telconet.ec>
    * @version 1.1 07/03/2017 Se modifica icono mostrado en mensaje informativo, se omite seteo de año actual para TN.
    * 
    * @author Jesus Banchen <jbanchen@telconet.ec>
    * @version 1.2 21/08/2019 Se modifica para agregar una nueva condicion (info == 'SinFrencuenciaOrden')
    * esto permitira que el nuevo boton realice el proceso respectivo. 
    * 
    * @author Jesus Banchen <jbanchen@telconet.ec>
    * @version 1.3 02/09/2019 Se modifica para agregar una nueva condicion (info == 'AgrupadaSinFrencuenciaOrden')
    * esto permitira que el nuevo boton realice el proceso respectivo. 
    * 
    */
function verificarCheck(info)
{
    strTipoFactura = info;
    $('#formulario_tipo').addClass('campo-oculto');
    $('#formulario_portafolio').addClass('campo-oculto');
    $('#formulario_catalogo').addClass('campo-oculto');
    if(info !== 'feConsumo' && info !== 'rangoConsumo')
    {
        var objDate = new Date();
        var intYear = objDate.getFullYear();
        
       
        if (typeof strClonarFactura == 'undefined') 
        {
            if (Ext.getCmp('idTxtAnio').getValue() < intYear)
            {
                Ext.Msg.show
                ({
                    title: 'Advertencia en campo Año',
                    msg: 'Est\u00e1 ingresando un a\u00f1o menor al actual',
                    buttons: Ext.Msg.OK,
                    animEl: 'elId',
                    icon: Ext.MessageBox.WARNING
                });   
                document.getElementById("txtMes").value = Ext.getCmp('intIdMes').getValue();
            }                
        }    
    }
    
    if (info == 'Clonar')
    {
        boolFacturacionAgrupada = false;
        store.removeAll();
        store.getProxy().extraParams.clonar = 'S';
        store.load();
        store.getProxy().extraParams.clonar = 'N';
    }
    
    if (info == 'Orden')
    {
        boolFacturacionAgrupada = false;
        store.removeAll();
        store.getProxy().extraParams.strSinFrecuencia = 'N';
        store.load();
    }
    
    if (info == 'SinFrencuencia')
    {
        boolFacturacionAgrupada = false;
        store.removeAll();
        store.getProxy().extraParams.strSinFrecuencia = 'S';
        store.load();
    }
    
    if (info == 'SinFrencuenciaOrden')
    {
        boolFacturacionAgrupada = false;
        store.removeAll();
        store.getProxy().extraParams.strSinFrecuencia = 'SNEW';
        store.load();
    }
    
    if (info == 'Agrupada')
    {   
        boolFacturacionAgrupada = true;
        store.removeAll();
        store.getProxy().extraParams.strSinFrecuencia = 'N';
        store.load();
    }
    
    if (info == 'AgrupadaSinFrencuenciaOrden')
    {   
        boolFacturacionAgrupada = true;
        store.removeAll();
        store.getProxy().extraParams.strSinFrecuencia = 'SNEW';
        store.load();
    }

    if (info == 'Manual')
    {        
        if (strPrefijoEmpresa=='MD' || strPrefijoEmpresa == 'EN')
        {
            var tipo = "portafolio";
            
            $.ajax
            ({
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
        }
        else
        {
            if ( strPrefijoEmpresa == 'TN' )
            {
                var tipo = "catalogo";
                
                $.ajax
                ({
                    type: "POST",
                    data: "tipo="+tipo+"&nombre="+nombre_tipo_negocio+"&nombre_no="+nombre_tipo_negocio_no+"&modulo=Financiero",
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
            }// ( strPrefijoEmpresa == 'TN' )
            else
            {
                if ( strPrefijoEmpresa == 'TNP' || strPrefijoEmpresa == 'TNG' )
                {                 
                    $('#formulario_tipo').removeClass('campo-oculto');
                    $('#formulario_portafolio').addClass('campo-oculto');
                    $('#formulario_catalogo').addClass('campo-oculto');                                                        
                }// ( strPrefijoEmpresa == 'TNP' || strPrefijoEmpresa == 'TNG' )
            }
        }         
        
        $('#planes').change(function ()
        {
            var info_plan   = document.getElementById('planes').value;
            var plan        = info_plan.split("-");
            var tipo        = '';
            
            if (strPrefijoEmpresa=='MD' || strPrefijoEmpresa=='TNP' || strPrefijoEmpresa == 'EN')
            {
                tipo='portafolio';
            }
            else if ( strPrefijoEmpresa != 'TTCO' )
            {
                tipo='catalogo';
            }
            
            $.ajax
            ({
                type: "POST",
                data: "plan=" + plan[0]+"&"+"tipo="+tipo,
                url: url_info_plan,
                beforeSend: function() 
                {
                    Ext.MessageBox.wait("Cargando precio del plan: "+ plan[1]);
                },
                complete: function() 
                {
                    Ext.MessageBox.hide();
                },
                success: function (msg)
                {
                    document.getElementById("contenido_plan").innerHTML = "";
                    
                    if (msg.msg == 'ok')
                    {
                        document.getElementById("precio").value         = msg.precio;
                        document.getElementById("descuento_plan").value = msg.descuento;
                        document.getElementById("tipoOrden").value      = msg.tipoOrden;
                        document.getElementById("tipo").value           = msg.tipo;
                        document.getElementById("tieneImpuesto").value  = msg.tieneImpuesto;
                    }
                    else
                        document.getElementById("contenido_plan").innerHTML = msg.msg;
                }
            });
        });
        
        
        $('#producto').change(function()
        {
            var info_producto = document.getElementById('producto').value;
            var producto      = info_producto.split("-");
            
            $.ajax
            ({
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
    
    
    if(info == 'feConsumo')
    {
        document.getElementById("div_feConsumo").style.display    = "";
        document.getElementById("div_rangoConsumo").style.display = "none";
        document.getElementById("opcFeConsumoSelected").value     = "feConsumo";
    }
    
    if(info == 'rangoConsumo')
    {
        document.getElementById("div_feConsumo").style.display    = "none";
        document.getElementById("div_rangoConsumo").style.display = "";
        document.getElementById("opcFeConsumoSelected").value     = "rangoConsumo";
    }
    
    if(info == 'consumoDias')
    {
        document.getElementById("divPorDias").style.display      = "";
        document.getElementById("divPorMeses").style.display     = "none";
        document.getElementById("opcRangoConsumoSelected").value = "consumoDias";
    }
    
    if(info == 'consumoMeses')
    {
        document.getElementById("divPorDias").style.display      = "none";
        document.getElementById("divPorMeses").style.display     = "";
        document.getElementById("opcRangoConsumoSelected").value = "consumoMeses";
    }
}

function verificarTipoCheck(info)
{   
    if (info === 'catalogo' || info == 'portafolio')
    {
        $.ajax({
            type: "POST",
            data: "tipo=" + info + "&nombre=" + nombre_tipo_negocio +
                "&nombre_no=" + nombre_tipo_negocio_no + "&modulo=Financiero",
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
    var tipo                        ='PR';
    var tipoOrden                   ='MAN';
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
            else
            {
                intImpuesto = 0;
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


        if(funcion_precio!=null)
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
                precio_total  = (precio_unitario * cantidad);

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
                                    floatCompensacion           = ( (precio_total + floatImpuestoIce) * floatPorcentajeCompensacion) / 100;
                                    floatCompensacionSolidaria  += floatCompensacion;
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

                var rec = new ListadoDetalleOrden({ 'codigo':                producto[0], 
                                                    'informacion':           producto[1], 
                                                    'precio':                precio_unitario.toFixed(2), 
                                                    'cantidad':              cantidad,
                                                    'descuento':             descuento, 
                                                    'tipo':                  tipo,
                                                    'tipoOrden':             tipoOrden, 
                                                    'fechaActivacion':       '', 
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
            }//( parseFloat(precio_unitario) > 0 )
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
 */
function agregar_detalle_portafolio()
{
    //Obtener informacion del formulario
    var info_producto    = formulario.planes.value;
    var producto         = info_producto.split("-");
    var cantidad         = formulario.cantidad_plan.value;
    var tipoOrden        = formulario.tipoOrden.value;
    var tipo             = formulario.tipo.value;
    var puntoId          = formulario.punto_id.value;
    var tieneImpuesto    = formulario.tieneImpuesto.value;
    var precio_unitario  = 0;
    var precio_total     = 0;
    var descuento        = 0;
    var descripcion_plan = formulario.descripcion_plan.value;
    precio_unitario      = formulario.precio.value;
    descuento            = formulario.descuento_plan.value;
    precio_total         = precio_unitario;
    var intImpuestoId    = 0;
    var strPagaIce       = document.getElementById("strPagaIce").value;
    
    
    var strTieneDetalleRep = 'N';

    verificarSiClienteEsCompensado();
    
    if( parseFloat(precio_unitario) > 0 )
    {
        if( parseFloat(cantidad) > 0 )
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

            var floatimpuestoProductoSelec = 0;
            var floatCompensacion          = 0;

            //Subtotal para el twig
            precio_total      = precio_unitario * cantidad;
            acum_subtotal     += precio_total;
            acum_descuento    += parseFloat(descuento);

            if(strPagaIva=='S')
            {
                floatimpuestoProductoSelec = (( (precio_total - parseFloat(descuento)) * tieneImpuesto) / 100);
                acum_impuesto              += floatimpuestoProductoSelec;
                acum_impuestoIva           += floatimpuestoProductoSelec;

                if( 'SI' == strEsCompensado )
                {
                    floatCompensacion           =  ( ( (precio_total - parseFloat(descuento)) * floatPorcentajeCompensacion) / 100 ) / cantidad;
                    floatCompensacionSolidaria  += ( floatCompensacion * cantidad );
                }
            }
            else
            {
                acum_impuesto += 0;
            }

            redondearDetalleVisualizacion();

            var rec = new ListadoDetalleOrden({ 
                                                'codigo':                producto[0], 
                                                'informacion':           producto[1], 
                                                'precio':                precio_unitario, 
                                                'cantidad':              cantidad, 
                                                'descuento':             descuento, 
                                                'tipo':                  tipo, 
                                                'tipoOrden':             tipoOrden, 
                                                'fechaActivacion':       '',
                                                'puntoId':               puntoId,
                                                'descripcion':           descripcion_plan,
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


                        acum_subtotal      += floatPrecioTotal;                                 
                        acum_impuesto      += floatImpuestoProductoRep;
                        acum_impuestoIva   += floatImpuestoProductoRep; 

                        redondearDetalleVisualizacion();

                        var rec = new ListadoDetalleOrden({ 
                                                           'codigo':                objRespuesta.intProductoId, 
                                                           'informacion':           objRespuesta.strDescripcionProd, 
                                                           'precio':                objRespuesta.floatPrecioUnitario, 
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
        }//( parseFloat(cantidad) > 0 )
        else
        {
            Ext.Msg.alert("Atención", "Debe ingresar una cantidad mayor a cero");
        }
    }//( parseFloat(precio_unitario) > 0 )
    else
    {
        Ext.Msg.alert("Atención", "Debe ingresar un valor de precio mayor a cero");
    }
}



function clonacion()
{
    if(strClonarFactura == "S")
    {
        document.getElementById("strClonarFactura").value = strClonarFactura;
        if (typeof dia_inicial != 'undefined')
        {
            document.getElementById("rangoConsumo").checked = "true";
            $('#rangoConsumo').checked=true;
            verificarCheck("rangoConsumo");
            document.getElementById("opcRangoConsumoDias").selected = "true";
            document.getElementById("opcFeConsumoSelected").value = "rangoConsumo";
            Ext.getCmp("dateDiaInicio").setValue(dia_inicial);
            if(dia_final!=0)
                Ext.getCmp("dateDiaFin").setValue(dia_final);
        }
        
        else if (typeof mes_inicial != 'undefined')
        {
            document.getElementById("rangoConsumo").checked = "true";
            $('#rangoConsumo').checked=true;
            document.getElementById("opcRangoConsumoMeses").checked = "true";
            verificarCheck("rangoConsumo");
            verificarCheck("consumoMeses");
            document.getElementById("opcRangoConsumoMeses").selected = "true";
            document.getElementById("opcFeConsumoSelected").value = "rangoConsumo";
            document.getElementById("rangoConsumo").checked = "true";
            document.getElementById("opcRangoConsumoMeses").selected = "true";
            Ext.getCmp("dateMesInicio").setValue(mes_inicial);
            if(mes_final!=0)
                Ext.getCmp("dateMesFin").setValue(mes_final);
        }
        else
        {
            document.getElementById("feConsumo").checked = "true";
            $('#feConsumo').checked=true;
            verificarCheck("feConsumo");
        }
        if (typeof mes_consumo != 'undefined')
        {   
            Ext.getCmp("intIdMes").setValue(mes_consumo);
        }
        if (typeof anio_consumo != 'undefined')
        {
            Ext.getCmp("idTxtAnio").setValue(anio_consumo);
        }
                        
        document.getElementById("observacion").value = strObservacion;

        verificarCheck("Clonar");
        
    }
}

function limpiar_detalle_portafolio()
{
    if (formulario.cantidad_plan)
        formulario.cantidad_plan.value = "";

    if (formulario.descuento_plan)
        formulario.descuento_plan.value = "";

    if (formulario.precio)
        formulario.precio.value = "";

    if (formulario.tipoOrden)
        formulario.tipoOrden.value = "";

    if (formulario.planes)
        formulario.planes.options[0].selected = true;

    if (formulario.descripcion_plan)
        formulario.descripcion_plan.value = "";
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
    if( boolPuedeCompensar )
    {
        /**
         * Si tengo el rol para poder marcar como compensado al cliente se debe verificar siempre que el checkbox de compensación este marcado y
         * que la oficina sea una oficina de compensación
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
    }
    else
    {
        verificarSiOficinaEImpuestoCompensa();
    }
}


function verificarSiOficinaEImpuestoCompensa()
{
    var intIdOficinaSelected    = Ext.getCmp('cmbOficinaFacturacion').getValue();
    var intIdImpuestoSelected   = intIdImpuestoIvaActivo;
    var strOficinaEsCompensado  = "N";
    var strTmpClienteCompensado = "S";
    
    if( boolPermisoSeleccionarImpuesto )
    {
        intIdImpuestoSelected = Ext.getCmp('cmbImpuesto').getValue();
    }

    if( !Ext.isEmpty(intIdOficinaSelected) )
    {
        var indexStoreOficina = storeOficinasFacturacion.findExact('intIdOficina', intIdOficinaSelected);

        if( !Ext.isEmpty(indexStoreOficina) )
        {
            if( indexStoreOficina > 0 )
            {
                var recordStoreOficina = storeOficinasFacturacion.getAt(indexStoreOficina);

                if( !Ext.isEmpty(recordStoreOficina) )
                {
                    strOficinaEsCompensado = recordStoreOficina.get("strEsCompensado");
                }//( !Ext.empty(recordStoreOficina) )
            }//( indexStoreOficina > 0 )
        }//( !Ext.empty(indexStoreOficina) )
    }//( !Ext.empty(intIdOficinaSelected) )
    
                        
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
            if( document.getElementById('esCompensado') != null )
            {
                document.getElementById('esCompensado').disabled = true;
                document.getElementById('esCompensado').checked  = false;
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

//Se duplica la actualizaDescripcion de editarServio por motivo errores en consola
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

        for (var y= 0; y < nombre_caract.length; y++)
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
