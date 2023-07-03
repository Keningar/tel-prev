Ext.require([
    '*'
]);

Ext.onReady(function(){
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

    grid = Ext.create('Ext.grid.Panel', {
        store: storeDetalle,
        columns: [ {
            text: 'Forma Pago',
            dataIndex: 'formaPagoText',
            width: 160,
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
            width: 120,
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
            width: 120,
            align: 'right'
        },{
            xtype: 'actioncolumn',
            width:45,
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
        },
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
                select:{fn:function(combo, value) 
                {
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
  
});	

function verifRetencionesDuplicadas()
{
    $.ajax({
        type: "POST",
        data: "idPer=" + id_persona+" &numDoc=" + $('#infopagodettype_numero_retencion').val()+" &idFormaPago="+$('#infopagodettype_forma_pago').val()+"&codEmpresa="+codEmpresa,
        url: url_verifica_retencion,
        success: function(msg){
            if (msg != ''){
                if(msg=="no"){
                        agregaDetalle();
                }
                if(msg=="si"){
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


/**
 * Documentacion para funcion agregaDetalle()
 * funcion que permite agregar valores al grid del detalle del pago
 * segun la forma de pago seleccionada al ingresar el pago.
 * 
 * @version 1.2 27/06/2016 amontero@telconet.ec
 * Actualizacion: Se corrige el error en la validacion de retenciones
 * ya que al agregar detalles asumia que forma de pago CANJE era una retencion
 * 
 * @version 1.1 05/05/2016 amontero@telconet.ec
 * Actualizacion: se agrega la nueva forma de pago RETENCION FUENTE 1%  
 * 
 * @since 11/02/2015
 * @author amontero@telconet.ec
 * 
 * @author Edson Franco <efranco@telconet.ec>
 * @version 1.2 16-03-2017 - Se valida la fecha de proceso seleccionada por el usuario para validar si es permitido el ingreso del pago.
 */
function agregaDetalle()
{
    var valorDetalle = $('#infopagodettype_valor').val();
    
	if( valorDetalle <= 0 || valorDetalle == '' ) 
    {
		Ext.Msg.alert('Alerta ','El valor ingresado debe ser mayor a 0');
		return false;
	}
    var banco                  = ''; 
    var bancoText              = ''; 
    var tipoCuenta             = '';
    var tipoCuentaText         = ''; 
    var numeroCuenta           = ''; 
    var codigoDebito           = '';
    var rec                    = '';
    var ctaBancariaEmpresa     = '';
    var ctaBancariaEmpresaText = '';
    var numeroDocumento        = '';
    var intFormaPago           = $('#infopagodettype_forma_pago').val();
    var textFormaDePago        = $("select[id='infopagodettype_forma_pago'] option:selected").text();
    var idFormaDePago          = $("select[id='infopagodettype_forma_pago'] option:selected").val();
    var idInputFormaPago       = idFormaDePago+"-"+textFormaDePago;
    var strTipoFormaPago       = document.getElementById(idInputFormaPago).value;
        strTipoFormaPago       = strTipoFormaPago.trim().toUpperCase();
    
    //Obtiene los valores segun tipo de forma de pago
    if( strTipoFormaPago == 'TARJETA_CREDITO' )
    {           
        banco                  = Ext.getCmp('cmb_bancos2').getValue();
        tipoCuenta             = Ext.getCmp('cmb_tipotarjeta').getValue();
        bancoText              = Ext.getCmp('cmb_bancos2').getRawValue();
        tipoCuentaText         = Ext.getCmp('cmb_tipotarjeta').getRawValue();         
        numeroCuenta           = $('#infopagodettype_numero_tarjeta').val();
        numeroDocumento        = $('#infopagodettype_numero_voucher').val();
        ctaBancariaEmpresa     = Ext.getCmp('cmb_ctas_bancarias_empresa_tc').getValue();
        ctaBancariaEmpresaText = Ext.getCmp('cmb_ctas_bancarias_empresa_tc').getRawValue();  
        feProceso              = Ext.Date.format(Ext.getCmp('fechaVoucherTc').getValue(),'Y-m-d');            
    }
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
              ctaBancariaEmpresa     = Ext.getCmp('cmb_ctas_bancarias_empresa_deb').getValue();
              ctaBancariaEmpresaText = Ext.getCmp('cmb_ctas_bancarias_empresa_deb').getRawValue(); 
         }
         else
         {           
             if ( strTipoFormaPago == 'CHEQUE' )
             { 
                  banco           = Ext.getCmp('cmb_bancos_cheque').getValue();
                  bancoText       = Ext.getCmp('cmb_bancos_cheque').getRawValue();         
                  numeroDocumento = $('#infopagodettype_numero_cheque').val();
                  tipoCuenta      = $('#infopagodettype_id_tipo_cuenta_cheque').val();
                  tipoCuentaText  = $('#infopagodettype_tipo_cuenta_cheque').val();
             }
             else
             {
                if( strTipoFormaPago == "RETENCION" )
                 {        
                        numeroDocumento = $('#infopagodettype_numero_retencion').val();
                        feProceso    = Ext.Date.format(Ext.getCmp('fechaRetencion').getValue(),'Y-m-d');
                 }
                 else
                 {
                     if( strTipoFormaPago == "DEPOSITO" )
                     {
                         numeroDocumento        = $('#infopagodettype_numero_papeleta').val();
                         feProceso              = Ext.Date.format(Ext.getCmp('fechaDeposito').getValue(),'Y-m-d');
                         ctaBancariaEmpresa     = Ext.getCmp('cmb_ctas_bancarias_empresa').getValue();
                         ctaBancariaEmpresaText = Ext.getCmp('cmb_ctas_bancarias_empresa').getRawValue();
                     }				   
                 }                 
             }
        }
    }
    
    //##Valida campos segun tipo de forma de pago
    if( strTipoFormaPago == "DEPOSITO" && intFormaPago && numeroDocumento && valorDetalle && feProceso && ctaBancariaEmpresa )
    {
        rec = new InfoPagoDetModel({ 'formaPago': intFormaPago,
                                     'formaPagoText':textFormaDePago,
                                     'banco': '',
                                     'bancoText': '',
                                     'tipoCuenta': '',
                                     'tipoCuentaText': '',
                                     'numeroCta':'',
                                     'valor':valorDetalle,
                                     'comentario':$('#infopagodettype_comentario').val(),
                                     'feProceso':feProceso,
                                     'codigoDebito':'',
                                     'ctaBancariaEmpresa':ctaBancariaEmpresa,
                                     'ctaBancariaEmpresaText':ctaBancariaEmpresaText,
                                     'numeroDocumento':numeroDocumento,
                                     'strTipoFormaPago': strTipoFormaPago });
        
        validarFechaPagoIngresada(feProceso, storeDetalle, rec);
    }
    else
    {
        //Si es OTROS
        if( strTipoFormaPago == "OTROS" && intFormaPago && valorDetalle )
        {
            rec = new InfoPagoDetModel({'formaPago':intFormaPago,
                                        'formaPagoText':textFormaDePago,
                                        'banco':'',
                                        'bancoText':'',
                                        'tipoCuenta':'',
                                        'tipoCuentaText':'',
                                        'numeroCta':'',
                                        'valor':valorDetalle,
                                        'comentario':$('#infopagodettype_comentario').val(),
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
            if( strTipoFormaPago == 'CHEQUE' && intFormaPago && valorDetalle && banco )
            {
                rec = new InfoPagoDetModel({'formaPago':intFormaPago,
                                            'formaPagoText':textFormaDePago,
                                            'banco':banco,
                                            'bancoText':bancoText,
                                            'tipoCuenta':tipoCuenta,
                                            'tipoCuentaText':tipoCuentaText,
                                            'numeroCta':numeroCuenta,
                                            'valor':valorDetalle,
                                            'comentario':$('#infopagodettype_comentario').val(),
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
                //Si es RETENCION
                if( strTipoFormaPago == "RETENCION" && intFormaPago && valorDetalle && numeroDocumento && feProceso )
                {
                    rec = new InfoPagoDetModel({'formaPago':intFormaPago,
                                                'formaPagoText':textFormaDePago,
                                                'banco':'',
                                                'bancoText':'',
                                                'tipoCuenta':'',
                                                'tipoCuentaText':'',
                                                'numeroCta':numeroCuenta,
                                                'valor':valorDetalle,
                                                'comentario':$('#infopagodettype_comentario').val(),
                                                'feProceso':feProceso,
                                                'codigoDebito':'',
                                                'ctaBancariaEmpresa':'',
                                                'ctaBancariaEmpresaText':'',
                                                'numeroDocumento':numeroDocumento,
                                                'strTipoFormaPago': strTipoFormaPago });

                    validarFechaPagoIngresada(feProceso, storeDetalle, rec);                    
                }
                else
                {
                    if( strTipoFormaPago ==='TARJETA_CREDITO' && (strPrefijoEmpresa==='TN' || strPrefijoEmpresa==='TNP') && intFormaPago && banco && tipoCuenta && numeroCuenta 
                        && numeroDocumento && valorDetalle && feProceso && ctaBancariaEmpresa )
                    {
                        validacionOk=true;
                        rec = new InfoPagoDetModel({'formaPago':intFormaPago,
                                                    'formaPagoText':textFormaDePago,
                                                    'factura':$('#infopagodettype_factura').val(),
                                                    'facturaText':$("select[id='infopagodettype_factura'] option:selected").text(),
                                                    'banco':banco,
                                                    'bancoText':bancoText,
                                                    'tipoCuenta':tipoCuenta,
                                                    'tipoCuentaText':tipoCuentaText,
                                                    'numeroCta':numeroCuenta,
                                                    'valor':valorDetalle,
                                                    'comentario':$('#infopagodettype_comentario').val(),
                                                    'codigoDebito':'',
                                                    'feProceso':feProceso,
                                                    'numeroDocumento':numeroDocumento,
                                                    'ctaBancariaEmpresa':ctaBancariaEmpresa,
                                                    'ctaBancariaEmpresaText':ctaBancariaEmpresaText,
                                                    'strTipoFormaPago': strTipoFormaPago });

                        validarFechaPagoIngresada(feProceso, storeDetalle, rec);
                    }
                    else
                    {
                        if( strTipoFormaPago === 'TARJETA_CREDITO' && (strPrefijoEmpresa==='MD' || strPrefijoEmpresa==='EN') && intFormaPago && banco && tipoCuenta && numeroCuenta
                            && valorDetalle )
                        {
                            validacionOk=true;
                            rec = new InfoPagoDetModel({'formaPago':intFormaPago,
                                                        'formaPagoText':textFormaDePago,
                                                        'factura':$('#infopagodettype_factura').val(),
                                                        'facturaText':$("select[id='infopagodettype_factura'] option:selected").text(),
                                                        'banco':banco,
                                                        'bancoText':bancoText,
                                                        'tipoCuenta':tipoCuenta,
                                                        'tipoCuentaText':tipoCuentaText,
                                                        'numeroCta':numeroCuenta,
                                                        'valor':valorDetalle,
                                                        'comentario':$('#infopagodettype_comentario').val(),
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
                            if( strTipoFormaPago === 'DEBITO' && (strPrefijoEmpresa==='TN' || strPrefijoEmpresa==='TNP') && intFormaPago && banco && tipoCuenta && numeroCuenta 
                                && codigoDebito && valorDetalle && feProceso && ctaBancariaEmpresa )
                            {
                                validacionOk=true;
                                rec = new InfoPagoDetModel({'formaPago':intFormaPago,
                                                            'formaPagoText':textFormaDePago,
                                                            'factura': '',
                                                            'facturaText':'',
                                                            'banco':banco,
                                                            'bancoText':bancoText,
                                                            'tipoCuenta':tipoCuenta,
                                                            'tipoCuentaText':tipoCuentaText,
                                                            'numeroCta':numeroCuenta,
                                                            'valor':valorDetalle,
                                                            'comentario':$('#infopagodettype_comentario').val(),
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
                                if( strTipoFormaPago === 'DEBITO' && (strPrefijoEmpresa==='MD' || strPrefijoEmpresa==='EN') && intFormaPago && banco && tipoCuenta && numeroCuenta
                                    && codigoDebito && valorDetalle && feProceso )
                                {
                                    validacionOk=true;
                                    rec = new InfoPagoDetModel({'formaPago':intFormaPago,
                                                                'formaPagoText':textFormaDePago,
                                                                'factura':$('#infopagodettype_factura').val(),
                                                                'facturaText':$("select[id='infopagodettype_factura'] option:selected").text(),
                                                                'banco':banco,
                                                                'bancoText':bancoText,
                                                                'tipoCuenta':tipoCuenta,
                                                                'tipoCuentaText':tipoCuentaText,
                                                                'numeroCta':numeroCuenta,
                                                                'valor':valorDetalle,
                                                                'comentario':$('#infopagodettype_comentario').val(),
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
    presentaFormaPago();   
}


function limpiaText(){
    $('#infopagodettype_valor').val('');
    $('#infopagodettype_numero_cuenta').val('');
    $('#infopagodettype_numero_tarjeta').val('');
    $('#infopagodettype_comentario').val('');
    $('#infopagodettype_numero_retencion').val('');
    $('#infopagodettype_numero_voucher').val('');
    $('#infopagodettype_numero_papeleta').val('');
    $('#infopagodettype_codigo_debito').val('');
    $('#infopagodettype_numero_cuenta_debito').val('');      
}


function grabar(){
    var array_data = new Array();
    var variable='';
    for(var i=0; i < grid.getStore().getCount(); i++){ 
        variable=grid.getStore().getAt(i).data;
        for(var key in variable) {
            var valor = variable[key];
            array_data.push(valor);
        }
        array_data.push('|');
        //console.log(array_data);
    }
    $('#infopagodettype_detalles').val(array_data); 

    if (($('#infopagodettype_detalles').val()=='0,,') || ($('#infopagodettype_detalles').val()=='')) {
        alert('Debe ingresar al menos 1 detalle');
        $('#infopagodettype_detalle').val('');

    }
    else
    {
        Ext.MessageBox.wait('Validando el anticipo a generar...');
        
        Ext.Ajax.request
        ({
            timeout: 9000000,
            url: strUrlValidarCreacionAnticipo,
            params:
            {
                strTipoDocumento: 'ANT',
                strDatosFormaPagoDet: $('#infopagodettype_detalles').val()
            },
            method: 'get',
            success: function(response) 
            {
                Ext.MessageBox.hide();
                var mensajeRespuesta = response.responseText;

                if( "OK" == mensajeRespuesta )
                {
                    Ext.MessageBox.show
                    ({
                        msg: 'Grabando datos, por favor espere...',
                        progressText: 'Grabando...',
                        width:300,
                        wait:true,
                        waitConfig: {interval:200}
                    });        
                    
                    document.form_cab.submit();
                }
                else
                {
                    Ext.Msg.alert('Atención', mensajeRespuesta);
                }
            },
            failure: function(result)
            {
                Ext.Msg.alert('Error ', 'Error al validar la creación del anticipo.');
            }
        });
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
    var textFormaDePago  = $("select[id='infopagodettype_forma_pago'] option:selected").text();
    var idFormaDePago    = $("select[id='infopagodettype_forma_pago'] option:selected").val();
    var idInputFormaPago = idFormaDePago+"-"+textFormaDePago;
    var strTipoFormaPago = "";

    if( document.getElementById(idInputFormaPago) != null )
    {
        strTipoFormaPago = document.getElementById(idInputFormaPago).value;
        strTipoFormaPago = strTipoFormaPago.trim().toUpperCase();
    }
    
    if( strTipoFormaPago != "" )
    {
        if( strTipoFormaPago == 'TARJETA_CREDITO' )
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
        }//( strTipoFormaPago == 'TARJETA_CREDITO' )
        
        if( strTipoFormaPago === 'DEBITO' )
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
        }//( strTipoFormaPago === 'DEBITO' )
        
        if( strTipoFormaPago === 'OTROS' )
        {
            ocultarDiv('div_cheque');
            ocultarDiv('div_tCredito');
            ocultarDiv('div_debito');        
            ocultarDiv('div_retencion');
            ocultarDiv('div_deposito');	                
            resetCombos(); 
            limpiaText();
        }//( strTipoFormaPago === 'OTROS' )
        
        if( strTipoFormaPago === 'CHEQUE' )
        {
            storeBancosCheque.load({params: {es_tarjeta: 'N', visibleEn:'PAG-CHEQ'}});    
            mostrarDiv('div_cheque');
            ocultarDiv('div_tCredito');
            ocultarDiv('div_debito');        
            ocultarDiv('div_retencion');
            ocultarDiv('div_deposito');	                
            resetCombos();  
            limpiaText();
        }//( strTipoFormaPago === 'CHEQUE' )
        
        if( strTipoFormaPago === 'RETENCION' )
        { 
            mostrarDiv('div_retencion');
            ocultarDiv('div_tCredito');
            ocultarDiv('div_debito');        
            ocultarDiv('div_cheque'); 
            ocultarDiv('div_deposito');        
            resetCombos(); 
            limpiaText();
        }//( strTipoFormaPago === 'RETENCION' )
        
        if( strTipoFormaPago === 'DEPOSITO' )
        {   
            ocultarDiv('div_retencion');
            ocultarDiv('div_tCredito');
            ocultarDiv('div_debito');        
            ocultarDiv('div_cheque'); 
            mostrarDiv('div_deposito');	
            storeCtasBancariasEmpresa.load({params: {idFormaPago: idFormaDePago}});
            resetCombos();
            limpiaText();
        }//( strTipoFormaPago === 'DEPOSITO' )
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


//PARA LA EMPRESA TELCONET SE VALIDA QUE EL NUMERO DE RETENCION 
//SEA EN EL FORMATO: 15 numeros relleno con ceros a la izquierda. Ej: 000000123456789'
if (strPrefijoEmpresa==='TN' || strPrefijoEmpresa==='TNP' )
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
            var textFormaDePago  = $("select[id='infopagodettype_forma_pago'] option:selected").text();
            var idFormaDePago    = $("select[id='infopagodettype_forma_pago'] option:selected").val();
            var idInputFormaPago = idFormaDePago+"-"+textFormaDePago;
            var strTipoFormaPago = "";

            if( document.getElementById(idInputFormaPago) != null )
            {
                strTipoFormaPago = document.getElementById(idInputFormaPago).value;
                strTipoFormaPago = strTipoFormaPago.trim().toUpperCase();
            }
            
            //SOLO SI LA FORMA DE PAGO ES RETENCION REALIZA VALIDACION DE NUMERO DE RETENCION
            if( strTipoFormaPago === "RETENCION" )
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
function validaFormatoNumeroRetencion()
{
    return /^[\d+]{15,15}$/.test($("#infopagodettype_numero_retencion").val());
}

function validarFechaPagoIngresada(strFeProceso, store, record)
{
    Ext.MessageBox.wait('Validando fecha del anticipo ingresado...');
    
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
                Ext.Msg.alert('Error ', 'Error al validar la fecha ingresada del anticipo.');
            }
        });
    }
    else
    {
        Ext.Msg.alert('Atención', 'Debe ingresar una fecha para agregar el detalle del anticipo');
    }
}

//Se bloquea la tecla F5 para evitar el refresco de pagina por medio del teclado
shortcut.add("F5",function() {});
   