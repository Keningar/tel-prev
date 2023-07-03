Ext.require([
    '*'
]);
 
Ext.onReady(function()
{    
    DTFechaDebito = new Ext.form.DateField({
            id: 'fechaDebito',
            fieldLabel: '',
            labelAlign : 'left',
            xtype: 'datefield',
            format: 'Y-m-d',
            renderTo: 'fecha_debito',
            width:250
    }); 
    DTFechaDeposito = new Ext.form.DateField({
            id: 'fechaDeposito',
            fieldLabel: '',
            labelAlign : 'left',
            xtype: 'datefield',
            format: 'Y-m-d',
            renderTo: 'fecha_deposito',
            width:250
    });

    DTFechaRetencion = new Ext.form.DateField({
            id: 'fechaRetencion',
            fieldLabel: '',
            labelAlign : 'left',
            xtype: 'datefield',
            format: 'Y-m-d',
            renderTo: 'fecha_retencion',
            width:250
    }); 
    DTFechaVoucherTc = new Ext.form.DateField({
            id: 'fechaVoucherTc',
            fieldLabel: '',
            labelAlign : 'left',
            xtype: 'datefield',
            format: 'Y-m-d',
            renderTo: 'fecha_voucher_tc',
            width:250
    });     
    Ext.define('datosContratoModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'numero', type: 'string'},
            {name: 'formaPago', type: 'string'}
        ]
    });
    
    storeDatosContrato = Ext.create('Ext.data.Store', {
            model: 'datosContratoModel',
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url : url_datos_contrato,
                reader: {
                    type: 'json',
                    root: 'contrato'
                }
            },
            listeners: {
                        load: function(store){
                            store.each(function(record) {
                                //console.log($('#infopagodettype_forma_pago').val());
                                //console.log(record.data.formaPago);
                                if($("select[id='infopagodettype_forma_pago'] option:selected").text()!=record.data.formaPago){
                                    mostrarDiv('div_mensaje_contrato');
                                    $('#div_mensaje_contrato').html('Tomar en cuenta que la forma de pago que escogio es diferente a la forma de pago ingresada en el contrato: '+record.data.formaPago);                                    
                                }
                                else
                                {
                                    ocultarDiv('div_mensaje_contrato');
                                }    
                            });
                    }
            } 
    });   
    
    
    Ext.define('InfoPagoDetModel',
    {
        extend: 'Ext.data.Model',
        fields: 
        [
            {name: 'formaPago',             type: 'string'},
            {name: 'formaPagoText',         type: 'string'},
            {name: 'factura',               type: 'string'},
            {name: 'facturaText',           type: 'string'},
            {name: 'banco',                 type: 'string'},
            {name: 'bancoText',             type: 'string'},
            {name: 'tipoCuenta',            type: 'string'},
            {name: 'tipoCuentaText',        type: 'string'},
            {name: 'numeroCta',             type: 'string'},
            {name: 'valor',                 type: 'float'},
            {name: 'comentario',            type: 'string'},
            {name: 'feProceso',             type: 'string'},
            {name: 'codigoDebito',          type: 'string'},
            {name: 'ctaBancariaEmpresaText',type: 'string'},
            {name: 'ctaBancariaEmpresa',    type: 'string'},
            {name: 'numeroDocumento',       type: 'string'},
            {name: 'strTipoFormaPago',      type: 'string'}
        ]
    });
    
    // create the Data Store
    storeDetalle = Ext.create('Ext.data.Store', {
        // destroy the store if the grid is destroyed
        autoDestroy: true,
        model: 'InfoPagoDetModel',
        proxy: {
            type: 'memory',
            reader: {
                type: 'json',
                root: 'personaFormasContacto',
                totalProperty: 'total'
            }             
        }       
    });


    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 2
    });

    // create the grid and specify what field you want
    // to use for the editor at each header.
    grid = Ext.create('Ext.grid.Panel', {
        store: storeDetalle,
        columns: [ {
            text: 'Forma Pago',
            dataIndex: 'formaPagoText',
            width: 150,
            align: 'right'
        }, {
            text: 'Factura',
            dataIndex: 'facturaText',
            width: 110,
            align: 'right'
        }, {
            text: 'Banco',
            dataIndex: 'bancoText',
            width: 120,
            align: 'right'
        }, {
            text: 'Tipo Cuenta',
            dataIndex: 'tipoCuentaText',
            width: 100,
            align: 'right'
        }, {
            text: '#Cta',
            dataIndex: 'numeroCta',
            width: 70,
            align: 'right'
        }, {
            text: '#Doc/CodDeb',
            dataIndex: 'numeroDocumento',
            width: 80,
            align: 'right'
        }, {
            text: 'Cta Empresa',
            dataIndex: 'ctaBancariaEmpresaText',
            width: 100,
            align: 'right'
        }, {
            text: 'Valor',
            dataIndex: 'valor',
            width: 70,
            align: 'right'
        }, {
            text: 'Fe. Proceso',
            dataIndex: 'feProceso',
            width: 70,
            align: 'right'
        }, {
            text: 'Comentario',
            dataIndex: 'comentario',
            width: 100,
            align: 'right'
        },{
            xtype: 'actioncolumn',
            width:30,
            sortable: false,
            items: [{
                iconCls:"button-grid-delete",
                tooltip: 'Borrar Forma Contacto',
                handler: function(grid, rowIndex, colIndex) {
                    storeDetalle.removeAt(rowIndex); 
                    calculaTotal();
                }
            }]
        }],
        listeners:
        {
            viewready: function (grid) 
            {
                var view = grid.view;
                // record the current cellIndex
                grid.mon(view, 
                {
                    uievent: function (type, view, cell, recordIndex, cellIndex, e) {
                        grid.cellIndex = cellIndex;
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
                        beforeshow: function updateTipBody(tip) 
                        {
                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1) 
                            {
                                header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                tip.update(grid.getStore().getAt(grid.recordIndex).get(header.dataIndex));
                            }
                        }
                    }
                });
            }                             
        }    
        ,
        selModel: {
            selType: 'cellmodel'
        },
        
        renderTo: Ext.get('lista_grid'),
        width: 1020,
        height: 250,
        title: '',
        plugins: [cellEditing]
    });
    
    
    
        Ext.define('BancosList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_banco', type:'int'},
            {name:'descripcion_banco', type:'string'}
        ]
    });
    storeBancos = Ext.create('Ext.data.Store', {
            model: 'BancosList',
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url : url_lista_bancos,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            }
    });
    storeBancosContables = Ext.create('Ext.data.Store', {
            model: 'BancosList',
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url : url_lista_bancos_contables,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            }
    });	
     storeBancos1 = Ext.create('Ext.data.Store', {
            model: 'BancosList',
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url : url_lista_bancos_tarjeta,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            }
    });
      storeBancosCheque = Ext.create('Ext.data.Store', {
            model: 'BancosList',
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url : url_lista_bancos,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            }
    });   
     Ext.define('TiposCuentaList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_cuenta', type:'int'},
            {name:'descripcion_cuenta', type:'string'}
        ]
    });

     storeCuentasBancosContables = Ext.create('Ext.data.Store', {
            model: 'TiposCuentaList',
            proxy: {
                type: 'ajax',
                url : url_lista_cuentas_bancos_contables,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            }
    });    
     storeTipoCuenta = Ext.create('Ext.data.Store', {
            model: 'TiposCuentaList',
            proxy: {
                type: 'ajax',
                url : url_lista_tipos_cuenta,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            }
    });
      storeTipoTarjeta = Ext.create('Ext.data.Store', {
            model: 'TiposCuentaList',
            proxy: {
                type: 'ajax',
                url : url_lista_tipos_cuenta,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            }
    });   
 	

    storeCtasBancariasEmpresa = Ext.create('Ext.data.Store', 
    {
            model: 'TiposCuentaList',
            proxy: {
                type: 'ajax',
                url : url_lista_ctas_bancarias_empresa,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            }
    }); 

    
    combo_bancos2 = new Ext.form.ComboBox({
            id: 'cmb_bancos2',
            name: 'cmb_bancos2',
            fieldLabel: false,
            anchor: '100%',
            queryMode:'local',
            width: 200,
            emptyText: 'Seleccione Banco',
            store:storeBancos1,
            displayField: 'descripcion_banco',
            editable    : false,             
            valueField: 'id_banco',
            renderTo: 'combo_banco2',
            listeners:{
                select:{fn:function(combo, value) {
                    Ext.getCmp('cmb_tipotarjeta').reset();  
                    storeTipoTarjeta.proxy.extraParams = {id_banco: combo.getValue(),es_tarjeta:'S', visibleEn:'PAG-TARJ'};
                    storeTipoTarjeta.load({params: {}});
                }}
            }
    }); 

    combo_bancos_debito = new Ext.form.ComboBox({
            id: 'cmb_bancos_debito',
            name: 'cmb_bancos_debito',
            fieldLabel: false,
            anchor: '100%',
            queryMode:'local',
            width: 200,
            emptyText: 'Seleccione Banco',
            store:storeBancos,
            displayField: 'descripcion_banco',
            editable    : false,             
            valueField: 'id_banco',
            renderTo: 'combo_banco_debito',
            listeners:{
                select:{fn:function(combo, value) 
                {
                    Ext.getCmp('cmb_tipotarjeta').reset();
                    storeTipoCuenta.proxy.extraParams = {id_banco: combo.getValue(),es_tarjeta:'', visibleEn:'PAG-DEB'};
                    storeTipoCuenta.load({params: {}});

                }}
            }
    });

    combo_bancos_cheque = new Ext.form.ComboBox({
            id: 'cmb_bancos_cheque',
            name: 'cmb_bancos_cheque',
            fieldLabel: false,
            anchor: '100%',
            queryMode:'local',
            width: 200,
            emptyText: 'Seleccione Banco',
            store:storeBancosCheque,
            displayField: 'descripcion_banco',
            editable    : false,             
            valueField: 'id_banco',
            renderTo: 'combo_banco_cheque'            
    });
    	    	
    
    combo_tipotarjeta = new Ext.form.ComboBox({
            id: 'cmb_tipotarjeta',
            name: 'cmb_tipotarjeta',
            fieldLabel: false,
            anchor: '100%',
            queryMode:'local',
            width: 200,
            emptyText: 'Seleccione tipo tarjeta',
            store: storeTipoTarjeta,
            displayField: 'descripcion_cuenta',
            editable    : false,             
            valueField: 'id_cuenta',
            renderTo: 'combo_tipo_tarjeta',
            listeners:{
                select:{fn:function(combo, value) {  
                }}
            }
    });
    combo_tipocuenta = new Ext.form.ComboBox({
            id: 'cmb_tipocuenta',
            name: 'cmb_tipocuenta',
            fieldLabel: false,
            anchor: '100%',
            queryMode:'local',
            width: 200,
            emptyText: 'Seleccione tipo cuenta',
            store: storeTipoCuenta,
            displayField: 'descripcion_cuenta',
            editable    : false,             
            valueField: 'id_cuenta',
            renderTo: 'combo_tipo_cuenta_debito',
            listeners:{
                select:{fn:function(combo, value) {  
                }}
            }
    });    
    
    
    //Combo que muestra las cuentas bancarias de la empresa para las formas de pago de depositos
    combo_ctas_bancarias_empresa = new Ext.form.ComboBox
    ({
            id           : 'cmb_ctas_bancarias_empresa',
            name         : 'cmb_ctas_bancarias_empresa',
            fieldLabel   : false,
            anchor       : '100%',
            queryMode    : 'local',
            width        : 350,
            emptyText    : 'Seleccione cuenta bancaria empresa',
            store        : storeCtasBancariasEmpresa,
            displayField : 'descripcion_cuenta',
            editable     : false,
            valueField   : 'id_cuenta',
            renderTo     : 'combo_ctas_bancarias_empresa',
            listeners:
            {
                select:{fn:function(combo, value) {  }}
            }
    });
    

    //Combo que muestra las cuentas bancarias de la empresa para forma de pago Tarjeta de credito
    combo_ctas_bancarias_empresa_tc = new Ext.form.ComboBox
    ({
            id           : 'cmb_ctas_bancarias_empresa_tc',
            name         : 'cmb_ctas_bancarias_empresa_tc',
            fieldLabel   : false,
            anchor       : '100%',
            queryMode    : 'local',
            width        : 350,
            emptyText    : 'Seleccione cuenta bancaria empresa',
            store        : storeCtasBancariasEmpresa,
            displayField : 'descripcion_cuenta',
            editable     : false,            
            valueField   : 'id_cuenta',
            renderTo     : 'combo_ctas_bancarias_empresa_tc',
            listeners:
            {
                select:{fn:function(combo, value) {  }}
            }
    });   
    
    
    //Combo que muestra las cuentas bancarias de la empresa para forma de pago Tarjeta de credito
    combo_ctas_bancarias_empresa_deb = new Ext.form.ComboBox
    ({
            id           : 'cmb_ctas_bancarias_empresa_deb',
            name         : 'cmb_ctas_bancarias_empresa_deb',
            fieldLabel   : false,
            anchor       : '100%',
            queryMode    : 'local',
            width        : 350,
            emptyText    : 'Seleccione cuenta bancaria empresa',
            store        : storeCtasBancariasEmpresa,
            displayField : 'descripcion_cuenta',
            editable     : false,            
            valueField   : 'id_cuenta',
            renderTo     : 'combo_ctas_bancarias_empresa_deb',
            listeners:
            {
                select:{fn:function(combo, value) {  }}
            }
    }); 
    
    Ext.define('valoresFacturaModel', 
    {
        extend: 'Ext.data.Model',
        fields: 
        [
            {name:'totalPagos'  , type:'float'},
            {name:'valorFactura', type:'float'},
            {name:'saldo'       , type:'float'}
        ]
    });
    
    storeValoresFact = Ext.create('Ext.data.Store', 
    {
        model: 'valoresFacturaModel',
        autoLoad: false,
        proxy: 
        {
            timeout:9000000,
            type: 'ajax',
            url : url_valores_fact,
            reader: 
            {
                type: 'json',
                root: 'datosFactura'
            }
        },
        listeners: 
        {
            load: function(store)
            {
                store.each(function(record) 
                {
                    mostrarDiv('div_datos_factura');
                    var newnumber = new Number(record.data.saldo+'').toFixed(parseInt(2));
                    $('#div_datos_factura').html('Saldo: $'+parseFloat(newnumber));
                });
            }
        } 
    });
    
    
function roundNumber(number, decimals) { // Arguments: number to round, number of decimal places
	var newnumber = new Number(number+'').toFixed(parseInt(decimals));
	document.roundform.roundedfield.value =  parseFloat(newnumber);
}
    
});

function obtieneDatosContrato(){
if ($('#infopagodettype_forma_pago').val())
        storeDatosContrato.load();
else
    ocultarDiv('div_mensaje_contrato');

}

function obtieneDatosFactura(){
    var factura=$('#infopagodettype_factura').val();
    if(factura)
        storeValoresFact.load({params: {fact:factura}});
    else
        ocultarDiv('div_datos_factura');
}

/**
 * Documentacion para funcion agregaDetalle()
 * funcion que permite agregar valores al grid del detalle del pago
 * segun la forma de pago seleccionada al ingresar el pago.
 * Actualizacion: Se incluye campos para ingresar al detalle del pago la 
 * cuenta bancaria de la empresa para poder obtener la cuenta contable
 * @version 1.1
 * @since 11/02/2015
 * @author amontero@telconet.ec
 * @version 1.1 05/05/2016 amontero@telconet.ec
 * @author Edson Franco <efranco@telconet.ec>
 * @version 1.2 16-03-2017 - Se valida la fecha de proceso seleccionada por el usuario para validar si es permitido el ingreso del pago.
 */
function agregaDetalle()
{
    var feProceso              = '';
    var banco                  = ''; 
    var bancoText              = ''; 
    var tipoCuenta             = '';
    var tipoCuentaText         = ''; 
    var numeroCuenta           = ''; 
    var codigoDebito           = ''; 
    var rec                    = '';
    var valorPago              = $('#infopagodettype_valor').val(); 
    var validacionOk           = false;
    var pagos                  = 0;
    var pagosFacturaGrid       = 0;
    var intFormaPago           = $('#infopagodettype_forma_pago').val();
    var textFormaDePago        = $("select[id='infopagodettype_forma_pago'] option:selected").text();
    var idFormaDePago          = $("select[id='infopagodettype_forma_pago'] option:selected").val();
    var intIdFactura           = $('#infopagodettype_factura').val();
    var strNumeroFactura       = $("select[id='infopagodettype_factura'] option:selected").text();
    var idInputFormaPago       = idFormaDePago+"-"+textFormaDePago;
    var strTipoFormaPago       = document.getElementById(idInputFormaPago).value;
        strTipoFormaPago       = strTipoFormaPago.trim().toUpperCase();
    var comentario             = $('#infopagodettype_comentario').val();
    var ctaBancariaEmpresa     = '';
    var ctaBancariaEmpresaText = '';
    var numeroDocumento        = '';    
    comentario                 = comentario.replace(","," ");
    $('#infopagodettype_comentario').val(comentario);
    
    if( intIdFactura && valorPago )
    {
        pagosFacturaGrid=new Number(parseFloat( obtienePagosPorFacturaEnGrid($('#infopagodettype_factura').val())  ));
        pagos=new Number(
            parseFloat(storeValoresFact.getAt(0).data.totalPagos) + parseFloat(valorPago)).toFixed(parseInt(2));
        var valor=0;
        if(storeValoresFact.getAt(0).data.valorFactura)
            valor=new Number(
            parseFloat(storeValoresFact.getAt(0).data.valorFactura) - pagos - pagosFacturaGrid).toFixed(parseInt(2));    
        //##obtiene valores segun forma de pago
        if( strTipoFormaPago == 'TARJETA_CREDITO' )
        {
            banco                  = Ext.getCmp('cmb_bancos2').getValue();
            tipoCuenta             = Ext.getCmp('cmb_tipotarjeta').getValue();
            bancoText              = Ext.getCmp('cmb_bancos2').getRawValue();
            tipoCuentaText         = Ext.getCmp('cmb_tipotarjeta').getRawValue();         
            numeroCuenta           = $('#infopagodettype_numero_tarjeta').val();
            if(strPrefijoEmpresa==='TN' || strPrefijoEmpresa==='TNP')
            {    
                numeroDocumento        = $('#infopagodettype_numero_voucher').val();
                ctaBancariaEmpresa     = Ext.getCmp('cmb_ctas_bancarias_empresa_tc').getValue();
                ctaBancariaEmpresaText = Ext.getCmp('cmb_ctas_bancarias_empresa_tc').getRawValue();  
                feProceso              = Ext.Date.format(Ext.getCmp('fechaVoucherTc').getValue(),'Y-m-d');            
            }    
        }//( strTipoFormaPago == 'TARJETA_CREDITO' )
        else
        {
            if( strTipoFormaPago == 'DEBITO' )
            {           
                banco                  = Ext.getCmp('cmb_bancos_debito').getValue();
                tipoCuenta             = Ext.getCmp('cmb_tipocuenta').getValue();
                bancoText              = Ext.getCmp('cmb_bancos_debito').getRawValue();
                tipoCuentaText         = Ext.getCmp('cmb_tipocuenta').getRawValue();         
                numeroCuenta           = $('#infopagodettype_numero_cuenta_debito').val();
                codigoDebito           = $('#infopagodettype_codigo_debito').val();              
                numeroDocumento        = $('#infopagodettype_codigo_debito').val();                    
                feProceso              = Ext.Date.format(Ext.getCmp('fechaDebito').getValue(),'Y-m-d');
                if(strPrefijoEmpresa==='TN' || strPrefijoEmpresa==='TNP')
                {               
                    ctaBancariaEmpresa     = Ext.getCmp('cmb_ctas_bancarias_empresa_deb').getValue();
                    ctaBancariaEmpresaText = Ext.getCmp('cmb_ctas_bancarias_empresa_deb').getRawValue();              
                }
            }//( strTipoFormaPago == 'DEBITO' )
            else
            {
                if( strTipoFormaPago == 'CHEQUE' )
                { 
                    banco           = Ext.getCmp('cmb_bancos_cheque').getValue();
                    bancoText       = Ext.getCmp('cmb_bancos_cheque').getRawValue();         
                    numeroDocumento = $('#infopagodettype_numero_cheque').val();
                    tipoCuenta      = $('#infopagodettype_id_tipo_cuenta_cheque').val();
                    tipoCuentaText  = $('#infopagodettype_tipo_cuenta_cheque').val();
                }//( strTipoFormaPago == 'CHEQUE' )
                else
                {
                    if( strTipoFormaPago == 'RETENCION' )
                    {   
                        numeroDocumento = $('#infopagodettype_numero_retencion').val();
                        feProceso    = Ext.Date.format(Ext.getCmp('fechaRetencion').getValue(),'Y-m-d');
                    }//( strTipoFormaPago == 'RETENCION' )
                    else
                    {
                        if( strTipoFormaPago == 'DEPOSITO' )
                        {
                              numeroDocumento        = $('#infopagodettype_numero_papeleta').val();
                              feProceso              = Ext.Date.format(Ext.getCmp('fechaDeposito').getValue(),'Y-m-d');
                              ctaBancariaEmpresa     = Ext.getCmp('cmb_ctas_bancarias_empresa').getValue();
                              ctaBancariaEmpresaText = Ext.getCmp('cmb_ctas_bancarias_empresa').getRawValue();
                        }//( strTipoFormaPago == 'DEPOSITO' )
                    }               
                }
            }
        }
        
        //##Valida campos segun forma de pago
        //si es tarjeta de credito o cuenta bancaria
        if( strTipoFormaPago == 'DEPOSITO' && $('#infopagodettype_forma_pago').val() && $('#infopagodettype_factura').val() && numeroDocumento 
            && valorPago && feProceso && ctaBancariaEmpresa )
        {
            validacionOk = true;
            rec          = new InfoPagoDetModel({ 'formaPago': intFormaPago,
                                                  'formaPagoText': textFormaDePago,
                                                  'factura': intIdFactura,
                                                  'facturaText': strNumeroFactura,
                                                  'banco': '',
                                                  'bancoText': '',
                                                  'tipoCuenta': '',
                                                  'tipoCuentaText': '',
                                                  'numeroCta': '',
                                                  'valor': valorPago,
                                                  'comentario': comentario,
                                                  'feProceso': feProceso,
                                                  'codigoDebito': '',
                                                  'ctaBancariaEmpresa':ctaBancariaEmpresa,
                                                  'ctaBancariaEmpresaText':ctaBancariaEmpresaText,
                                                  'numeroDocumento':numeroDocumento,
                                                  'strTipoFormaPago': strTipoFormaPago });

            validarFechaPagoIngresada(feProceso, storeDetalle, rec);
        }
        else
        {
            //Si es OTROS el tipo de forma de pago
            if( strTipoFormaPago == 'OTROS' && $('#infopagodettype_forma_pago').val() && $('#infopagodettype_factura').val()  
                && valorPago )
            {
                validacionOk = true;
                rec          = new InfoPagoDetModel({ 'formaPago': intFormaPago,
                                                      'formaPagoText': textFormaDePago,
                                                      'factura': intIdFactura,
                                                      'facturaText': strNumeroFactura,
                                                      'banco':'',
                                                      'bancoText':'',
                                                      'tipoCuenta':'',
                                                      'tipoCuentaText':'',
                                                      'numeroCta':'',
                                                      'valor':valorPago,
                                                      'comentario':comentario,
                                                      'feProceso':'',
                                                      'codigoDebito':'',
                                                      'ctaBancariaEmpresa':'',
                                                      'ctaBancariaEmpresaText':'',
                                                      'numeroDocumento':'',
                                                      'strTipoFormaPago': strTipoFormaPago });

                storeDetalle.add(rec);
                calculaTotal();
                limpia();
            }
            else
            {
                //Si es Cheque
                if( strTipoFormaPago.trim().toUpperCase()=='CHEQUE' && $('#infopagodettype_forma_pago').val() && $('#infopagodettype_factura').val() 
                    && valorPago&& banco && numeroDocumento )
                {
                    validacionOk = true;
                    rec          = new InfoPagoDetModel({ 'formaPago': intFormaPago,
                                                          'formaPagoText': textFormaDePago,
                                                          'factura': intIdFactura,
                                                          'facturaText': strNumeroFactura,
                                                          'banco':banco,
                                                          'bancoText':bancoText,
                                                          'tipoCuenta':tipoCuenta,
                                                          'tipoCuentaText':tipoCuentaText,
                                                          'numeroCta':'',
                                                          'valor':valorPago,
                                                          'comentario':comentario,
                                                          'feProceso':'',
                                                          'codigoDebito':'',
                                                          'ctaBancariaEmpresa':'',
                                                          'ctaBancariaEmpresaText':'',
                                                          'numeroDocumento':numeroDocumento,
                                                          'strTipoFormaPago': strTipoFormaPago });

                    storeDetalle.add(rec);
                    calculaTotal();
                    limpia();
                }
                else
                {
                    //Si el tipo de forma de pago es Retencion
                    if( strTipoFormaPago.trim().toUpperCase() == 'RETENCION' && $('#infopagodettype_forma_pago').val() 
                        && $('#infopagodettype_factura').val() && valorPago && numeroDocumento && feProceso )
                    {
                        validacionOk = true;
                        rec          = new InfoPagoDetModel({ 'formaPago': intFormaPago,
                                                              'formaPagoText': textFormaDePago,
                                                              'factura': intIdFactura,
                                                              'facturaText': strNumeroFactura,
                                                              'banco':'',
                                                              'bancoText':'',
                                                              'tipoCuenta':'',
                                                              'tipoCuentaText':'',
                                                              'numeroCta':'',
                                                              'valor':valorPago,
                                                              'comentario':comentario,
                                                              'feProceso':feProceso,
                                                              'codigoDebito':'',
                                                              'ctaBancariaEmpresa':'',
                                                              'ctaBancariaEmpresaText':'',
                                                              'numeroDocumento':numeroDocumento,
                                                              'strTipoFormaPago': strTipoFormaPago });

                        //SOLO SI LA FORMA DE PAGO ES RETENCION VALIDARA 
                        //SI YA EXISTE UNA RETENCION INGRESADA EN LA FACTURA SELECCIONADA
                        if(textFormaDePago.toUpperCase().match(/^RETENCION.*$/))
                        {    
                            if (existeRetencionEnGrid($('#infopagodettype_factura').val())=='S')
                            {
                                alert("Solo se puede ingresar 1 retencion por factura. Favor Corregir");
                                limpia();
                            }
                            else
                            {
                                 verificaRetencion($('#infopagodettype_factura').val(),rec);                                                 
                            }
                        }
                        else
                        {
                            validarFechaPagoIngresada(feProceso, storeDetalle, rec);
                        }          
                    }
                    else
                    {
                        if((strTipoFormaPago.trim().toUpperCase()==='TARJETA_CREDITO') && (strPrefijoEmpresa==='TN' || strPrefijoEmpresa==='TNP')
                           && $('#infopagodettype_forma_pago').val() && $('#infopagodettype_factura').val() 
                           && banco && tipoCuenta && numeroCuenta && numeroDocumento && valorPago 
                           && feProceso && ctaBancariaEmpresa)
                        {
                            validacionOk = true;
                            rec          = new InfoPagoDetModel({ 'formaPago': intFormaPago,
                                                                  'formaPagoText': textFormaDePago,
                                                                  'factura': intIdFactura,
                                                                  'facturaText': strNumeroFactura,
                                                                  'banco':banco,
                                                                  'bancoText':bancoText,
                                                                  'tipoCuenta':tipoCuenta,
                                                                  'tipoCuentaText':tipoCuentaText,
                                                                  'numeroCta':numeroCuenta,
                                                                  'valor':valorPago,
                                                                  'comentario':comentario,
                                                                  'codigoDebito':'',
                                                                  'feProceso':feProceso,
                                                                  'numeroDocumento':numeroDocumento,
                                                                  'ctaBancariaEmpresa':ctaBancariaEmpresa ,
                                                                  'ctaBancariaEmpresaText':ctaBancariaEmpresaText,
                                                                  'strTipoFormaPago': strTipoFormaPago });

                            validarFechaPagoIngresada(feProceso, storeDetalle, rec);
                        }
                        else
                        { 
                            if((strTipoFormaPago.trim().toUpperCase()==='TARJETA_CREDITO') && (strPrefijoEmpresa==='MD' || strPrefijoEmpresa==='EN')
                               && $('#infopagodettype_forma_pago').val() && $('#infopagodettype_factura').val() 
                               && banco && tipoCuenta && numeroCuenta && valorPago)
                            {
                                validacionOk = true;
                                rec          = new InfoPagoDetModel({ 'formaPago': intFormaPago,
                                                                      'formaPagoText': textFormaDePago,
                                                                      'factura': intIdFactura,
                                                                      'facturaText': strNumeroFactura,
                                                                      'banco':banco,
                                                                      'bancoText':bancoText,
                                                                      'tipoCuenta':tipoCuenta,
                                                                      'tipoCuentaText':tipoCuentaText,
                                                                      'numeroCta':numeroCuenta,
                                                                      'valor':valorPago,
                                                                      'comentario':comentario,
                                                                      'feProceso':'',
                                                                      'numeroDocumento':'',
                                                                      'codigoDebito':'',
                                                                      'strTipoFormaPago': strTipoFormaPago });

                                storeDetalle.add(rec);
                                calculaTotal();
                                limpia();
                            }                            
                            else
                            {
                                if((strTipoFormaPago.trim().toUpperCase()==='DEBITO')  && (strPrefijoEmpresa==='TN' || strPrefijoEmpresa==='TNP')
                                   && $('#infopagodettype_forma_pago').val() && $('#infopagodettype_factura').val() 
                                   && banco && tipoCuenta && numeroCuenta && codigoDebito && valorPago 
                                   && feProceso && ctaBancariaEmpresa)
                                {
                                    validacionOk = true;
                                    rec          = new InfoPagoDetModel({ 'formaPago': intFormaPago,
                                                                          'formaPagoText': textFormaDePago,
                                                                          'factura': intIdFactura,
                                                                          'facturaText': strNumeroFactura,
                                                                          'banco':banco,
                                                                          'bancoText':bancoText,
                                                                          'tipoCuenta':tipoCuenta,
                                                                          'tipoCuentaText':tipoCuentaText,
                                                                          'numeroCta':numeroCuenta,
                                                                          'valor':valorPago,
                                                                          'comentario':comentario,
                                                                          'feProceso':feProceso,
                                                                          'codigoDebito':codigoDebito,
                                                                          'ctaBancariaEmpresa':ctaBancariaEmpresa ,
                                                                          'ctaBancariaEmpresaText':ctaBancariaEmpresaText,
                                                                          'numeroDocumento':numeroDocumento,
                                                                          'strTipoFormaPago': strTipoFormaPago });

                                    validarFechaPagoIngresada(feProceso, storeDetalle, rec);
                                }
                                else
                                {  
                                    if((strTipoFormaPago.trim().toUpperCase()==='DEBITO')  && (strPrefijoEmpresa==='MD' || strPrefijoEmpresa==='EN')
                                       && $('#infopagodettype_forma_pago').val() && $('#infopagodettype_factura').val() 
                                       && banco && tipoCuenta && numeroCuenta && codigoDebito && valorPago && feProceso)
                                    {
                                        validacionOk = true;
                                        rec          = new InfoPagoDetModel({ 'formaPago': intFormaPago,
                                                                              'formaPagoText': textFormaDePago,
                                                                              'factura': intIdFactura,
                                                                              'facturaText': strNumeroFactura,
                                                                              'banco':banco,
                                                                              'bancoText':bancoText,
                                                                              'tipoCuenta':tipoCuenta,
                                                                              'tipoCuentaText':tipoCuentaText,
                                                                              'numeroCta':numeroCuenta,
                                                                              'valor':valorPago,
                                                                              'comentario':comentario,
                                                                              'feProceso':feProceso,
                                                                              'codigoDebito':codigoDebito,
                                                                              'numeroDocumento':numeroDocumento,
                                                                              'strTipoFormaPago': strTipoFormaPago });

                                        validarFechaPagoIngresada(feProceso, storeDetalle, rec);
                                    } 
                                    else
                                    {    
                                        Ext.Msg.alert('Alerta ','Faltan campos por ingresar');
                                    }   
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    else
    {
        Ext.Msg.alert('Alerta ','Por favor debe escoger una factura y Agregar el valor para poder ingresar el detalle del pago.');            

    }
    //Verifica si el pago excede el saldo de la factura
    if(validacionOk)
    {
        if(valor<0)
        {
            Ext.Msg.alert('Alerta ','El pago excede el saldo de la factura. Se generara un anticipo por el valor excedente.');
        }
    }
}

function calculaTotal(){
                var total=0;
                for(var i=0; i < grid.getStore().getCount(); i++){ 
                    //console.log(grid.getStore().getAt(i).data.valor);
                    total=total + grid.getStore().getAt(i).data.valor;
                    //console.log(array_data);
                } 
                $('#infopagocabtype_valorTotal').removeAttr('readonly');
                $('#infopagocabtype_valorTotal').val(total);
                $('#infopagocabtype_valorTotal').attr('readonly','readonly');
    
}

function limpia(){
    $('#infopagodettype_forma_pago').val('');
    limpiaText();
    ocultarDiv('div_datos_factura');
    presentaFormaPago();   
}


function limpiaText(){
    $('#infopagodettype_valor').val('');
    $('#infopagodettype_factura').val('');
    $('#infopagodettype_numero_cuenta').val('');
    $('#infopagodettype_numero_tarjeta').val('');
    $('#infopagodettype_comentario').val('');
    $('#infopagodettype_numero_retencion').val('');
    $('#infopagodettype_numero_voucher').val('');
    $('#infopagodettype_numero_papeleta').val('');
    $('#infopagodettype_codigo_debito').val('');
    $('#infopagodettype_numero_cuenta_debito').val('');    
    ocultarDiv('div_datos_factura');  
}



function grabar(){
    var array_data = new Array();
    var variable='';
    for(var i=0; i < grid.getStore().getCount(); i++)
    { 
        variable=grid.getStore().getAt(i).data;
        for(var key in variable) 
        {
            var valor = variable[key];
            array_data.push(valor);
        }
        array_data.push('|');
    }
    $('#infopagodettype_detalles').val(array_data); 

    if (($('#infopagodettype_detalles').val()=='0,,') || ($('#infopagodettype_detalles').val()=='')) 
    {
        alert('Debe ingresar al menos 1 detalle');
        $('#infopagodettype_detalle').val('');
    }
    else
    {
        Ext.MessageBox.wait('Validando el pago a generar...');
        
        Ext.Ajax.request
        ({
            timeout: 9000000,
            url: strUrlValidarCreacionAnticipo,
            params:
            {
                strTipoDocumento: 'PAG',
                strDatosFormaPagoDet: $('#infopagodettype_detalles').val()
            },
            method: 'get',
            success: function(response) 
            {
                Ext.MessageBox.hide();
                var mensajeRespuesta = response.responseText;
                    
                if( "OK" == mensajeRespuesta )
                {
                    //CREA PAGO
                    //------------------------
                    $.ajax(
                    {
                        type: "POST",
                        data: "detalles=" + $('#infopagodettype_detalles').val() + "&idpunto="+id_punto,
                        url: url_graba_pago,
                        beforeSend: function()
                        {
                            Ext.MessageBox.show({
                               msg: 'Grabando datos, por favor espere...',
                               progressText: 'Grabando...',
                               width:300,
                               wait:true,
                               waitConfig: {interval:200}
                           });
                        },		
                        success: function(resp)
                        {
                            var obj = JSON.parse(resp);
                            var msg = obj.msg;
                            
                            if (msg != '')
                            {
                                if(msg=="error")
                                {
                                    Ext.MessageBox.show
                                    ({
                                        icon: Ext.Msg.ERROR,
                                        width:500,						
                                        height: 300,
                                        title:'Mensaje del Sistema',
                                        msg: 'No se pudo procesar el pago, por favor consulte con el administrador.',
                                        buttonText: {yes: "Ok"},
                                        fn: function(btn)
                                        {
                                            if(btn=='yes')
                                            {
                                                redirigirPantalla(obj.link);
                                            }
                                        }
                                    });				
                                }
                                else
                                {
                                    if(msg=="cerrar-conservicios")
                                    {
                                        Ext.MessageBox.show
                                        ({
                                            icon: Ext.Msg.INFO,
                                            width:500,
                                            height: 300,
                                            title:'Mensaje del Sistema',
                                            msg: 'Se proceso el pago y el cliente ya no tiene saldos adeudados. Se procedio a realizar la '+
                                                 'reactivacion.',
                                            buttonText: {yes: "Ok"},
                                            fn: function(btn)
                                            {
                                                if(btn=='yes')
                                                {
                                                    redirigirPantalla(obj.link);
                                                }
                                            }
                                        });
                                    }
                                    else
                                    {
                                        if(msg=='nocerrar')
                                        {
                                            Ext.MessageBox.show
                                            ({
                                                icon: Ext.Msg.INFO,
                                                width:500,
                                                height: 300,
                                                title:'Mensaje del Sistema',
                                                msg: 'Se registro el pago con exito pero el cliente aun tiene saldos adeudados. ',
                                                buttonText: {yes: "Ok"},
                                                fn: function(btn)
                                                {
                                                    if(btn=='yes')
                                                    {
                                                        redirigirPantalla(obj.link);
                                                    }
                                                }
                                            });							
                                        }
                                        else if(msg=='nocerrar-inaudit')
                                        {
                                            Ext.MessageBox.show
                                            ({
                                                icon: Ext.Msg.INFO,
                                                width:500,
                                                height: 300,
                                                title:'Mensaje del Sistema',
                                                msg: 'Se procesó el pago y el cliente no tiene saldos adeudados. Reactivación detenida por Proceso Posible Abusador. ',
                                                buttonText: {yes: "Ok"},
                                                fn: function(btn)
                                                {
                                                    if(btn=='yes')
                                                    {
                                                        redirigirPantalla(obj.link);
                                                    }
                                                }
                                            });	        
                                        }
                                        else
                                        {
                                            if(msg=='cerrar-sinservicios')
                                            {
                                                Ext.MessageBox.show
                                                ({
                                                    icon: Ext.Msg.INFO,
                                                    width:500,
                                                    height: 300,
                                                    title:'Mensaje del Sistema',
                                                    msg: 'Se registro el pago con exito y el cliente ya no tiene saldos adeudados. '+
                                                         'No se encontro servicios para reactivar, por favor consultar con el administrador.',
                                                    buttonText: {yes: "Ok"},
                                                    fn: function(btn)
                                                    {
                                                        if(btn=='yes')
                                                        {
                                                            redirigirPantalla(obj.link);
                                                        }
                                                    }
                                                });	
                                            }    
                                        }
                                    }
                                }					
                            }
                            else
                            {
                                console.log(obj.msgerror);

                                Ext.MessageBox.show
                                ({
                                    icon: Ext.Msg.ERROR,
                                    width:500,
                                    height: 300,
                                    title:'Mensaje del Sistema',
                                    msg: 'No se pudo procesar el pago, por favor consulte con el administrador.',
                                    buttonText: {yes: "Ok"},
                                    fn: function(btn)
                                    {
                                        if(btn=='yes')
                                        {
                                            redirigirPantalla(obj.link);
                                        }
                                    }
                                });
                            }
                        }
                    });
                }
                else
                {
                    Ext.Msg.alert('Atención', mensajeRespuesta);
                }
            },
            failure: function(result)
            {
                Ext.Msg.alert('Error ', 'Error al validar la creación del pago.');
            }
        });
    }
}


function redirigirPantalla(strUrl)
{
    Ext.MessageBox.show
    ({
        msg: 'Mostrando el pago generado, por favor espere...',
        progressText: 'Mostrando...',
        width:300,
        wait:true,
        waitConfig: {interval:200}
    });
    
    window.top.location.href = strUrl;
}


function mostrarDiv(div){
    capa = document.getElementById(div);
    capa.style.display = 'block';    
}
function ocultarDiv(div){
    capa = document.getElementById(div);
    capa.style.display = 'none';    
}


/**
 * Documentacion para funcion presentaFormaPago()
 * Opcion que permite presentar el grupo de campos que se necesitan ingresar
 * segun la forma de pago seleccionada al ingresar el pago.
 * Actualizacion: se agrega la nueva forma de pago RETENCION FUENTE 1%   
 * @since 11/02/2015
 * @author amontero@telconet.ec
 * @version 1.1 05/05/2016 amontero@telconet.ec
 */
function presentaFormaPago()
{
    obtieneDatosContrato();
    
    var textFormaDePago  = $("select[id='infopagodettype_forma_pago'] option:selected").text();
    var idFormaDePago    = $("select[id='infopagodettype_forma_pago'] option:selected").val();
    var idInputFormaPago = idFormaDePago+"-"+textFormaDePago;
    var strTipoFormaPago = "";

    if( document.getElementById(idInputFormaPago) != null )
    {
        strTipoFormaPago = document.getElementById(idInputFormaPago).value;
    }
    
    if( strTipoFormaPago.trim() != "" )
    {
        if(strTipoFormaPago.toUpperCase()=='TARJETA_CREDITO')
        {
            ocultarDiv('div_cheque');
            mostrarDiv('div_tCredito');
            ocultarDiv('div_debito');
            ocultarDiv('div_retencion');
            ocultarDiv('div_deposito');
            if (strPrefijoEmpresa==='TN' || strPrefijoEmpresa==='TNP')
            {
                mostrarDiv('div_tCredito_numero_documento');
                mostrarDiv('div_tCredito_fecha_documento');
                mostrarDiv('div_tCredito_cuenta_empresa');
            }         
            storeBancos1.load({params: {es_tarjeta: 'S', visibleEn:'PAG-TARJ'}});
            storeCtasBancariasEmpresa.load();        
            resetCombos();  
            limpiaText();
        }//(strTipoFormaPago.toUpperCase()=='TARJETA_CREDITO')

        if(strTipoFormaPago.toUpperCase()==='DEBITO')
        {
            ocultarDiv('div_cheque');
            ocultarDiv('div_tCredito');
            mostrarDiv('div_debito');
            ocultarDiv('div_retencion');
            ocultarDiv('div_deposito');	        
            if (strPrefijoEmpresa==='TN' || strPrefijoEmpresa==='TNP')
            {
                mostrarDiv('div_debito_cuenta_empresa');  
            }         
            storeBancos.load({params: {es_tarjeta: '', visibleEn:'PAG-DEB'}});
            storeCtasBancariasEmpresa.load();
            resetCombos(); 
            limpiaText();
        }//(strTipoFormaPago.toUpperCase()==='DEBITO')

        if(strTipoFormaPago.toUpperCase()==='OTROS')
        {
            ocultarDiv('div_cheque');
            ocultarDiv('div_tCredito');
            ocultarDiv('div_debito');        
            ocultarDiv('div_retencion');
            ocultarDiv('div_deposito');	                
            resetCombos(); 
            limpiaText();
        }//(strTipoFormaPago.toUpperCase()==='OTROS')

        if(strTipoFormaPago.toUpperCase()=='CHEQUE')
        {
            storeBancosCheque.load({params: {es_tarjeta: 'N', visibleEn:'PAG-CHEQ'}});    
            mostrarDiv('div_cheque');
            ocultarDiv('div_tCredito');
            ocultarDiv('div_debito');        
            ocultarDiv('div_retencion');
            ocultarDiv('div_deposito');	                
            resetCombos();  
            limpiaText();
        }//(strTipoFormaPago.toUpperCase()=='CHEQUE')

        if( strTipoFormaPago.toUpperCase()=='RETENCION' )
        { 
            mostrarDiv('div_retencion');
            ocultarDiv('div_tCredito');
            ocultarDiv('div_debito');        
            ocultarDiv('div_cheque'); 
            ocultarDiv('div_deposito');        
            resetCombos(); 
            limpiaText();
        }//( strTipoFormaPago.toUpperCase()=='RETENCION' )

        if( strTipoFormaPago.toUpperCase()=='DEPOSITO' )
        {   
            ocultarDiv('div_retencion');
            ocultarDiv('div_tCredito');
            ocultarDiv('div_debito');        
            ocultarDiv('div_cheque'); 
            mostrarDiv('div_deposito');	
            storeCtasBancariasEmpresa.load({params: {idFormaPago: idFormaDePago}});
            resetCombos();
            limpiaText();
        }//( strTipoFormaPago.toUpperCase()=='DEPOSITO' )
    }//( strTipoFormaPago.trim() != "" )
    
    if(!$("#infopagodettype_forma_pago").val()){
        ocultarDiv('div_tCredito');
        ocultarDiv('div_debito');        
        ocultarDiv('div_cheque');
        ocultarDiv('div_retencion');
        ocultarDiv('div_deposito');		                
        resetCombos();
        limpiaText();
    }

}

function resetCombos(){  
    combo_bancos2.reset();
    combo_bancos_debito.reset();    
    combo_bancos_cheque.reset();		
    combo_tipotarjeta.reset();
    combo_tipocuenta.reset();	
    combo_ctas_bancarias_empresa.reset();
    combo_ctas_bancarias_empresa_tc.reset();    
    combo_ctas_bancarias_empresa_deb.reset();     
}

async function verificaRetencion(fact,rec){
   $.ajax({
			type: "POST",
			data: "fact=" + fact+"&idPer=" + id_persona+" &numDoc=" + $('#infopagodettype_numero_retencion').val()+" &idFormaPago="+$('#infopagodettype_forma_pago').val()+"&codEmpresa="+codEmpresa,
			url: url_verifica_retencion,
			success: function(msg){
				if (msg != ''){
					if(msg=="no")
                    {
                        //console.log(flagCorrecto);
                        storeDetalle.add(rec);
                        calculaTotal();
                        //limpia();
					}
					if(msg=="si"){
						alert("Ya existe retencion ingresada para esta factura. Favor Corregir");
                        limpia();
                                                //console.log(flagCorrecto);
					}
                    if(msg=="ret")
                    {
                        alert("Retención ya registrada para este cliente.");
                        limpia();
                    }
			   }
			   else
			   {
				   alert("Error: No se pudo validar si existe retencion.");
			   }
			}
                      
	});      
}

function existeRetencionEnGrid(fact){
    var variable='';var respuesta='N';
    for(var i=0; i < grid.getStore().getCount(); i++){ 
        variable=grid.getStore().getAt(i).data;
        //console.log('newFact:'+fact+''+'factura:'+grid.getStore().getAt(i).data.factura)
        for(var key in variable) {
            if (key=='formaPagoText'){
                if ((variable[key]==variable[key].match(/^Retencion.*$/) )
                   &&(fact==grid.getStore().getAt(i).data.factura))
                    respuesta='S';
            }
        }
    }    
    //console.log(respuesta);
    return respuesta;
}


function obtienePagosPorFacturaEnGrid(fact){
    var respuesta=0;
    for(var i=0; i < grid.getStore().getCount(); i++){ 


                if (fact==grid.getStore().getAt(i).data.factura){
                    console.log(grid.getStore().getAt(i).data.factura);
                    respuesta=respuesta+grid.getStore().getAt(i).data.valor;
                }
    }    
    return respuesta;
}
//PARA LA EMPRESA TELCONET SE VALIDA QUE EL NUMERO DE RETENCION 
//SEA EN EL FORMATO: 15 numeros relleno con ceros a la izquierda. Ej: 000000123456789'
if (strPrefijoEmpresa==='TN' || strPrefijoEmpresa==='TNP')
{       
    $(function()
    { 
        $("#infopagodettype_numero_retencion").keydown(
            function(event)
            {
                if(!isNumeric(event)) return false;
            }
        );
        $("#infopagodettype_numero_retencion").blur(function()
        {
            //SOLO SI LA FORMA DE PAGO ES RETENCION REALIZA VALIDACION DE NUMERO DE RETENCION
            if($("select[id='infopagodettype_forma_pago'] option:selected").text().toUpperCase().match(/^RETENCION.*$/))
            {    
                if (parseFloat($("#infopagodettype_numero_retencion").val())>0)
                {    
                    $("#infopagodettype_numero_retencion").val(("000000000000000" + $("#infopagodettype_numero_retencion").val()).slice(-15)); 

                    if (validaFormatoNumeroRetencion())
                    {   
                        ocultarDiv('div_numero_retencion');
                        return true;
                    }
                    else
                    {
                        mostrarDiv('div_numero_retencion');
                        $('#div_numero_retencion').html('Numero de retencion debe ser de 15 numeros rellenado con ceros a la izquierda');     
                        $("#infopagodettype_numero_retencion").val("");  
                        return false;
                    }    
                }
                else
                {
                    mostrarDiv('div_numero_retencion');
                    $('#div_numero_retencion').html('Numero de retencion debe ser de 15 numeros rellenado con ceros a la izquierda');     
                    $("#infopagodettype_numero_retencion").val("");
                    return false;
                }   
            }
        });    
    });
}


function validarFechaPagoIngresada(strFeProceso, store, record)
{
    Ext.MessageBox.wait('Validando fecha del pago ingresado...');
    
    if( !Ext.isEmpty(strFeProceso) )
    {
        Ext.Ajax.request
        ({
            timeout: 9000000,
            url: strUrlValidarFechaPago,
            params:
            {
                strFechaValidar: strFeProceso,
                strParametroValidar: 'CREACION_PAG_ANT'
            },
            method: 'get',
            success: function(response) 
            {                
                var mensajeRespuesta = response.responseText;

                if( "S" == mensajeRespuesta )
                {
                    Ext.MessageBox.hide();
                    store.add(record);
                    calculaTotal();
                    limpia();
                }
                else
                {
                    Ext.Msg.alert('Atención', mensajeRespuesta);
                }
            },
            failure: function(result)
            {
                Ext.Msg.alert('Error ', 'Error al validar la fecha ingresada del pago.');
            }
        });
    }
    else
    {
        Ext.Msg.alert('Atención', 'Debe ingresar una fecha para agregar el detalle del pago');
    }
}


function validaFormatoNumeroRetencion()
{
    return /^[\d+]{15,15}$/.test($("#infopagodettype_numero_retencion").val());
}

//Se bloquea la tecla F5 para evitar el refresco de pagina por medio del teclado
shortcut.add("F5",function() {});
