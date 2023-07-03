var intAcumuladoTotal           = 0;
var intAcumuladoSubtotal        = 0;
var intAcumuladoSubtotalIce     = 0;
var intAcumuladoImpuesto        = 0;
var intAcumuladoDescuento       = 0;
var intAcumuladoImpuestoIce     = 0;
var intAcumuladoImpuestoIva     = 0;
var intAcumuladoImpuestoOtros   = 0;
var modelListadoDetalleOrden    = null;
var gridDetalleFactura          = null;
var winDetallesFactura          = null;
var floatCompensacionSolidaria  = 0;

Ext.require([ 'Ext.ux.grid.plugin.PagingSelectionPersistence' ]);

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.onReady(function(){
	
var desde_date = new Ext.form.DateField({
                    name: 'feDesdeFacturaPost',
                    allowBlank: false,
                    format: 'Y-m-d',
                    renderTo: 'feDesdeFactura',
                });
                
var hasta_date = new Ext.form.DateField({
                    name: 'feHastaFacturaPost',
                    allowBlank: false,
                    format: 'Y-m-d',
                    renderTo: 'feHastaFactura',
                });

var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
    clicksToEdit: 1
});

modelListadoDetalleOrden = Ext.define('ListadoDetalleOrden',
{
    extend: 'Ext.data.Model',
    fields:
    [               
        {name: 'codigo',                     type: 'string'},
        {name: 'informacion',                type: 'string'},
        {name: 'valor',                      type: 'float'},
        {name: 'cantidad',                   type: 'float'},
        {name: 'descuento',                  type: 'float'},
        {name: 'tipo',                       type: 'string'},
        {name: 'intPuntoId',                 type: 'string'},
        {name: 'impuesto',                   type: 'float'},
        {name: 'subtotal',                   type: 'float'},
        {name: 'impuestoIva',                type: 'float'},
        {name: 'impuestoIce',                type: 'float'},
        {name: 'impuestoOtros',              type: 'float'},
        {name: 'intIdDetalleFactura',        type: 'int'},
        {name: 'floatCompensacionSolidaria', type: 'float'},
        {name: 'idServicio',                 type: 'int'}
    ],
    idProperty: 'intIdDetalleFactura'
}); 

 store = Ext.create('Ext.data.Store', {
    // destroy the store if the grid is destroyed
    autoDestroy: true,
    model: modelListadoDetalleOrden,
    proxy: {
        type: 'ajax',
        // load remote data using HTTP
        url: url_info_por_dias,
        timeout: 9000000,
        reader: {
            type: 'json',
            root: 'listadoInformacion'
            // records will have a 'plant' tag
        },
        extraParams:
        {
            fechaDesde: '',
            fechaHasta: '',
            idFactura: '',
            porcentaje: '',
            tipo: '',
            strEsCompensado: strEsCompensado
        },
        simpleSortMode: true               
    },
    listeners: {
            
            load: function (store)
            {
                intAcumuladoTotal           = 0;
                intAcumuladoSubtotal        = 0;
                intAcumuladoDescuento       = 0;
                intAcumuladoImpuesto        = 0;
                intAcumuladoImpuestoIce     = 0;
                intAcumuladoImpuestoIva     = 0;
                intAcumuladoImpuestoOtros   = 0;
                intAcumuladoSubtotalIce     = 0;
                floatCompensacionSolidaria  = 0;

                store.each(function (record) 
                {
                    intAcumuladoSubtotal        += record.data.subtotal;
                    intAcumuladoDescuento       += record.data.descuento;
                    intAcumuladoImpuesto        += record.data.impuesto;
                    intAcumuladoImpuestoIce     += record.data.impuestoIce;
                    intAcumuladoImpuestoIva     += record.data.impuestoIva;
                    intAcumuladoImpuestoOtros   += record.data.impuestoOtros;
                    floatCompensacionSolidaria  += record.data.floatCompensacionSolidaria;
                });
                
                intAcumuladoSubtotalIce = intAcumuladoSubtotal + intAcumuladoImpuestoIce - intAcumuladoDescuento;
                intAcumuladoTotal       = parseFloat(intAcumuladoSubtotalIce) + parseFloat(intAcumuladoImpuestoIva)
                                          - parseFloat(floatCompensacionSolidaria);
                
                redondearDetalleVisualizacion();
            }
    }
});

// create the grid and specify what field you want
// to use for the editor at each header.
grid = Ext.create('Ext.grid.Panel', {
        store:store,
        columns: [new Ext.grid.RowNumberer(),
        {
            text: 'Id',
            dataIndex: 'idServicio',
            hidden: true
        },            
        {
            text: 'Codigo',
            dataIndex: 'codigo',
            hidden:true
		},{
            text: 'PuntoId',
            dataIndex: 'intPuntoId',
            hidden:true
        },{
            text: 'Producto/Plan',
            dataIndex: 'informacion',
            hidden:false,
            width: 350,
        },{
            text: 'Valor',
            width: 130,
            dataIndex: 'valor'
        },{
            text: 'Cantidad',
            dataIndex: 'cantidad',
            hidden:false
        },{
            text: 'Descuento $',
            dataIndex: 'descuento',
            hidden:false
        },{
            text: 'Tipo',
            dataIndex: 'tipo',
            hidden:true
        },{
            header: 'Acciones',
            xtype: 'actioncolumn',
            width: 84,
            align: 'center',
            sortable: false,
            items: 
                [
                    {
                        iconCls: 'button-grid-delete',
                        tooltip: 'Eliminar',
                        handler: function (grid, rowIndex, colIndex)
                        {
                            var precio          = 0;
                            var cantidad        = 0;
                            var subtotal        = 0;
                            var impuesto        = 0;
                            var impuestoIce     = 0;
                            var impuestoIva     = 0;
                            var impuestoOtros   = 0;
                            var descuento       = 0;
                            
                            var floatTmpCompensacionSolidaria = 0;

                            precio          = grid.getStore().getAt(rowIndex).data.valor;
                            cantidad        = grid.getStore().getAt(rowIndex).data.cantidad;
                            descuento       = grid.getStore().getAt(rowIndex).data.descuento;
                            subtotal        = (precio * cantidad);
                            impuesto        = grid.getStore().getAt(rowIndex).data.impuesto;
                            impuestoIce     = grid.getStore().getAt(rowIndex).data.impuestoIce;
                            impuestoIva     = grid.getStore().getAt(rowIndex).data.impuestoIva;
                            impuestoOtros   = grid.getStore().getAt(rowIndex).data.impuestoOtros;
                            
                            floatTmpCompensacionSolidaria = grid.getStore().getAt(rowIndex).data.floatCompensacionSolidaria;

                            //Resto este valor a los acumuladores
                            intAcumuladoSubtotal        -= subtotal;
                            intAcumuladoDescuento       -= descuento;
                            intAcumuladoImpuesto        -= impuesto;
                            intAcumuladoImpuestoIce     -= impuestoIce;
                            intAcumuladoImpuestoIva     -= impuestoIva;
                            intAcumuladoImpuestoOtros   -= impuestoOtros;
                            floatCompensacionSolidaria  -= floatTmpCompensacionSolidaria;
                
                            intAcumuladoSubtotalIce = intAcumuladoSubtotal + intAcumuladoImpuestoIce - intAcumuladoDescuento;
                            intAcumuladoTotal       = parseFloat(intAcumuladoSubtotalIce)+parseFloat(intAcumuladoImpuestoIva) 
                                                      - parseFloat(floatCompensacionSolidaria);
                                                  
                            redondearDetalleVisualizacion();
                            
                            store.removeAt(rowIndex);
                        }
                    }
                ]
		}],
        selModel: {
            selType: 'cellmodel'
        },
        renderTo: 'lista_informacion_pre_cargada',
        width: 800,
        height: 200,
        title: 'Detalle de NC',
        frame: true,
        plugins: [cellEditing]
    });
});

var tipoNC = "";

function enviarInformacion()
{
    var array_data_caract = {};
    var j                 = 0;
    var informacion       = [];
    var strMotivo         = document.getElementById('motivos').value;
    
    for (var i = 0; i < grid.getStore().getCount(); i++)
    {
        variable = grid.getStore().getAt(i).data;
        
        for (var key in variable)
        {
            array_data_caract[key]            = variable[key];
            array_data_caract['tipoNC']       = tipoNC;
            array_data_caract['porcentajeNc'] = document.getElementById('porcentajeFactura').value;
            array_data_caract['fechaDesdeNc'] = document.getElementById('formulario').feDesdeFacturaPost.value;
            array_data_caract['fechaHastaNc'] = document.getElementById('formulario').feHastaFacturaPost.value;
			j++;
		}
        
		informacion.push(array_data_caract);
        
		array_data_caract = {};
		j                 = 0;
	}
    
	document.getElementById("listado_informacion").value = JSON.stringify(informacion);
        
    if( strMotivo == null || strMotivo == "" || strMotivo == "Seleccione..." )
    {
        Ext.Msg.alert("Atención", "Debe seleccionar el motivo de la Nota de Crédito");
        return false;
    }
        
    if (informacion.length > 0)
    {
        loadMask('myLoading', true, 'Procesando');
        document.getElementById("listado_informacion").value = JSON.stringify(informacion);
        document.formulario.submit();
    }
    else
    {
        Ext.Msg.alert("Atención", "Ingrese detalles a la nota de crédito");
        return false;
    }
}

function verificarCheck(info)
{
    tipoNC=info;
    if(info=='PorDias')
    {
		$('#formulario_por_dias').removeClass('campo-oculto');
		$('#formulario_por_porcentaje').addClass('campo-oculto');
		$('#formulario_valor_original').addClass('campo-oculto');
    }
    
    if(info=='PorServicio')
    {
        $('#formulario_por_dias').addClass('campo-oculto');
		$('#formulario_por_porcentaje').removeClass('campo-oculto');
		$('#formulario_valor_original').addClass('campo-oculto');
    }
    
    if(info=='ValorOriginal')
    {
        $('#formulario_por_dias').addClass('campo-oculto');
		$('#formulario_por_porcentaje').addClass('campo-oculto');
    }
    
    $('#info_check').val(info);
    store.loadData([],false);
        
    if( info == 'ValorPorDetalle' )
    {
        getWindowDetalle();
    }
}

function generarDetalle()
{
	var clickeado=$('#info_check').val();
	
	if(clickeado=='PorDias')
    {
		//llamo al ajax por dias
		//envio los valores de las fechas
		var fechaDesde=document.getElementById('formulario').feDesdeFacturaPost.value;
		var fechaHasta=document.getElementById('formulario').feHastaFacturaPost.value;
        
        if(!Ext.isEmpty(fechaDesde) && !Ext.isEmpty(fechaHasta))
        {
            store.getProxy().extraParams.fechaDesde = fechaDesde;
            store.getProxy().extraParams.fechaHasta = fechaHasta;        
            store.getProxy().extraParams.tipo       = "PD";  
        }
        else
        {
            Ext.Msg.alert("Atención", "Debe ingresar las fechas correspondientes para el cálculo de días.");
        }  
    }
    
	if(clickeado=='PorServicio')
    {
		//llamo al ajax por servicio
		//envio el valor del porcentaje
		var porcentajeFactura=document.getElementById('porcentajeFactura').value;
        
        if(porcentajeFactura > 0)
        {
            store.getProxy().extraParams.porcentaje = porcentajeFactura;        
        store.getProxy().extraParams.tipo           = "PS";    
        }
        else
        {
            Ext.Msg.alert("Atención", "Debe ingresar un valor de porcentaje mayor a cero.");
        }
    }
	
	if(clickeado=='ValorOriginal')
    {
		//llamo al ajax por valor original
		//se muestra el detalle de la factura exactamente igual        
        store.getProxy().extraParams.tipo= "VO";       
        
    }
    
    if (clickeado != 'ValorPorDetalle')
    {
        store.getProxy().extraParams.strPagaIva = strPagaIva;
        store.getProxy().extraParams.idFactura  = idFactura;
        store.load();
    }
}

function mostrarDiv(div){
    capa = document.getElementById(div);
    capa.style.display = 'block';    
}
function ocultarDiv(div){
    capa = document.getElementById(div);
    capa.style.display = 'none';    
}



function anadirDetalle()
{
    var param                  = '';
    var intSeleccionados       = 0;
    var intSeleccionadoCeros   = 0;
    var selection              = gridDetalleFactura.getPlugin('pagingSelectionPersistence').getPersistedSelection();
    var arrayOpciones          = new Object();
        arrayOpciones['items'] = new Array();

    if(selection.length > 0)
    {
        for(var i=0 ;  i < selection.length ; i++)
        {
            if( selection[i]['data'].valor > 0 )
            {
                arrayOpciones['items'].push(selection[i]['data']);
                        
                intSeleccionados++;
            }
            else
            {
                intSeleccionadoCeros ++;
            }
        }
        
        if( intSeleccionados > 0 && intSeleccionadoCeros == 0 )
        {
            Ext.MessageBox.wait("Añadiendo detalles...");
            
            Ext.Ajax.request
            ({
                url: url_info_por_dias,
                method: 'post',
                timeout: 9000000,
                params: 
                {
                    fechaDesde: '', 
                    fechaHasta: '', 
                    idFactura: idFactura, 
                    porcentaje: '', 
                    tipo: 'VPD', 
                    boolWithoutValues: 'S',
                    strPagaIva: strPagaIva,
                    listado_informacion: Ext.JSON.encode(arrayOpciones),
                    strEsCompensado: strEsCompensado
                },
                success: function(response)
                {         
                    intAcumuladoSubtotal        = 0;
                    intAcumuladoSubtotalIce     = 0;
                    intAcumuladoDescuento       = 0;
                    intAcumuladoImpuesto        = 0;
                    intAcumuladoImpuestoIce     = 0;
                    intAcumuladoImpuestoIva     = 0;
                    intAcumuladoImpuestoOtros   = 0;
                    intAcumuladoTotal           = 0;
                    floatCompensacionSolidaria  = 0;
                    
                    var jsonResponse                  = response.responseText;
                    var arrayItems                    = Ext.JSON.decode(jsonResponse);
                    var floatTmpCompensacionSolidaria = 0;
                    var precio                        = 0;
                    var cantidad                      = 0;
                    var subtotal                      = 0;
                    var impuesto                      = 0;
                    var impuestoIce                   = 0;
                    var impuestoIva                   = 0;
                    var impuestoOtros                 = 0;
                    var descuento                     = 0;
                    var total                         = 0;
                    
                    if( arrayItems['listadoInformacion'].length > 0 )
                    {
                        for(var i=0; i<arrayItems['listadoInformacion'].length; i++)
                        {
                            store.insert(0, arrayItems['listadoInformacion'][i]);
                            
                            precio          = arrayItems['listadoInformacion'][i]['valor'];
                            cantidad        = arrayItems['listadoInformacion'][i]['cantidad'];
                            descuento       = arrayItems['listadoInformacion'][i]['descuento'];
                            subtotal        = (precio * cantidad);
                            impuesto        = arrayItems['listadoInformacion'][i]['impuesto'];
                            impuestoIce     = arrayItems['listadoInformacion'][i]['impuestoIce'];
                            impuestoIva     = arrayItems['listadoInformacion'][i]['impuestoIva'];
                            impuestoOtros   = arrayItems['listadoInformacion'][i]['impuestoOtros'];
                            
                            floatTmpCompensacionSolidaria = arrayItems['listadoInformacion'][i]['floatCompensacionSolidaria'];

                            //Sumo este valor a los acumuladores
                            intAcumuladoSubtotal        += subtotal;
                            intAcumuladoDescuento       += descuento;
                            intAcumuladoImpuesto        += impuesto;
                            intAcumuladoImpuestoIce     += impuestoIce;
                            intAcumuladoImpuestoIva     += impuestoIva;
                            intAcumuladoImpuestoOtros   += impuestoOtros;
                            floatCompensacionSolidaria  += floatTmpCompensacionSolidaria;
                
                            intAcumuladoSubtotalIce = intAcumuladoSubtotal + intAcumuladoImpuestoIce - intAcumuladoDescuento;
                            intAcumuladoTotal       = parseFloat(intAcumuladoSubtotalIce) + parseFloat(intAcumuladoImpuestoIva) 
                                                      - parseFloat(floatCompensacionSolidaria);

                            redondearDetalleVisualizacion();
                        }
                        
                        winDetallesFactura.close();
                        Ext.MessageBox.hide();
                    }
                    else
                    {
                        Ext.MessageBox.hide();
                        Ext.Msg.alert('Error', 'No se pudo agregar detalles a la nota de crédito, por favor vuelva a intentarlo');
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
            Ext.Msg.alert('Error ','Debe seleccionar registros con valores mayor que cero');
        }
    }
    else
    {
        Ext.Msg.alert('Error ','Seleccione por lo menos un detalle de la lista');
    }
}

function getWindowDetalle()
{   
    var btnAnadirDetalle = Ext.create('Ext.button.Button', 
    {
        iconCls: 'icon_aprobar',
        text: 'Añadir Detalle',
        itemId: 'anadirDetalleAjax',
        scope: this,
        disabled: true,
        handler: function()
        { 
            anadirDetalle();
        }
    });
    
    var toolbar = Ext.create('Ext.toolbar.Toolbar', 
    {
        dock: 'top',
        align: '->',
        items   : 
        [
            { xtype: 'tbfill' },
            btnAnadirDetalle
        ]
    });
        
    var modelGridDetalle = Ext.create('Ext.selection.CheckboxModel',
    {
        checkOnly: true,
        listeners: 
        {
            selectionchange: function(sm, selections)
            {
                gridDetalleFactura.down('#anadirDetalleAjax').setDisabled( selections.length == 0 );
            }
        }
    });
    
    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing',
    { 
        clicksToEdit: 1,
        listeners:
        {
            beforeedit: function(e, editor)
            {
                gridDetalleFactura.getSelectionModel().deselect(editor.rowIdx);
            },
            afteredit: function(e, editor)
            {
                gridDetalleFactura.getSelectionModel().deselect(editor.rowIdx);
            }
        }
    });
    
    var storeDetalle = Ext.create('Ext.data.Store', 
    {
        autoLoad: true,
        model: modelListadoDetalleOrden,
        proxy: 
        {
            type: 'ajax',
            url: url_info_por_dias,
            timeout: 9000000,
            reader: 
            {
                type: 'json',
                root: 'listadoInformacion'
            },
            extraParams:
            {
                fechaDesde: '', 
                fechaHasta: '', 
                idFactura: idFactura, 
                porcentaje: '', 
                tipo: 'VO', 
                boolWithoutValues: 'S',
                strPagaIva: strPagaIva,
                strEsCompensado: strEsCompensado
            },
            simpleSortMode: true
        }
    });
    
    gridDetalleFactura = Ext.create('Ext.grid.Panel',
    {
        width: 400,
        height: 350,
        store: storeDetalle,
        loadMask: true,
        selModel: modelGridDetalle,
        iconCls: 'icon-grid',
        columns: 
        [
            {
                text: 'Producto/Plan',
                dataIndex: 'informacion',
                hidden: false,
                width: 300
            }, 
            {
                text: 'Valor',
                width: 120,
                dataIndex: 'valor',
                field: 
                {
                    xtype: 'numberfield',
                    allowBlank: false,
                    minValue: 0
                }
            }, 
        ],
        header: false,
        title: false,
        plugins: [cellEditing, {ptype : 'pagingselectpersist'}],
        dockedItems: [ toolbar ],
        listeners:
        {
            rowclick : function (in_this, rowIndex, e) 
            {
                var record = in_this.getStore().getAt(rowIndex);
                if (isDisabled(record))
                {
                    in_this.getSelectionModel().deselectRow(rowIndex);
                }
            }
        }
    });

    winDetallesFactura = Ext.create('Ext.window.Window',
    {
        title: 'Detalle de la Factura',
        modal: true,
        width: 490,
        closable: true,
        layout: 'fit',
        floating: true,
        shadow: true,
        shadowOffset:20,
        items: [gridDetalleFactura]
    }).show();
}


function redondearDetalleVisualizacion()
{
    //REDONDEOS
    intAcumuladoSubtotal       = Math.round(intAcumuladoSubtotal * 100)/100;
    intAcumuladoImpuestoIva    = Math.round(intAcumuladoImpuestoIva * 100)/100;
    intAcumuladoDescuento      = Math.round(intAcumuladoDescuento * 100)/100;
    intAcumuladoImpuestoIce    = Math.round(intAcumuladoImpuestoIce * 100)/100;
    intAcumuladoImpuestoOtros  = Math.round(intAcumuladoImpuestoOtros * 100)/100;
    floatCompensacionSolidaria = Math.round(floatCompensacionSolidaria * 100)/100;
    intAcumuladoTotal          = intAcumuladoSubtotal + intAcumuladoImpuestoIva + intAcumuladoImpuestoIce - floatCompensacionSolidaria
                                 - intAcumuladoDescuento;
    intAcumuladoTotal          = Math.round(intAcumuladoTotal * 100)/100;
    
    //VISUALIZACION
    document.getElementById("subtotalDetalle").innerHTML        = intAcumuladoSubtotal.toFixed(2);
    document.getElementById("ivaDetalle").innerHTML             = intAcumuladoImpuestoIva.toFixed(2);
    document.getElementById("descuentoDetalle").innerHTML       = intAcumuladoDescuento.toFixed(2);
    document.getElementById("totalDetalle").innerHTML           = intAcumuladoTotal.toFixed(2);
    document.getElementById("iceDetalle").innerHTML             = intAcumuladoImpuestoIce.toFixed(2);
    document.getElementById("otrosImpDetalle").innerHTML        = intAcumuladoImpuestoOtros.toFixed(2);
    document.getElementById("compensacionSolidaria").innerHTML  = floatCompensacionSolidaria.toFixed(2);
}
