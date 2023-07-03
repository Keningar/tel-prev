Ext.require([
    '*'
]);

Ext.onReady(function(){    
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
                url : url_lista_bancos,
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
    combo_bancos = new Ext.form.ComboBox({
            id: 'cmb_bancos',
            name: 'cmb_bancos',
            fieldLabel: false,
            anchor: '100%',
            queryMode:'local',
            width: 200,
            emptyText: 'Seleccione Banco',
            store:storeBancos,
            displayField: 'descripcion_banco',
            valueField: 'id_banco',
            renderTo: 'combo_banco',
            listeners:{
                select:{fn:function(combo, value) {
                    Ext.getCmp('cmb_tipocuenta').reset();  
                    //Ext.getCmp('cmb_accion').reset();  
                    
                    storeTipoCuenta.proxy.extraParams = {id_banco: combo.getValue(),es_tarjeta:'N'};
                    storeTipoCuenta.load({params: {}});

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
            valueField: 'id_cuenta',
            renderTo: 'combo_tipo_cuenta',
            listeners:{
                select:{fn:function(combo, value) {  
                }}
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
            valueField: 'id_banco',
            renderTo: 'combo_banco2',
            listeners:{
                select:{fn:function(combo, value) {
                    Ext.getCmp('cmb_tipotarjeta').reset();  
                    //Ext.getCmp('cmb_accion').reset();  
                    
                    storeTipoTarjeta.proxy.extraParams = {id_banco: combo.getValue(),es_tarjeta:'S'};
                    storeTipoTarjeta.load({params: {}});

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
            valueField: 'id_cuenta',
            renderTo: 'combo_tipo_tarjeta',
            listeners:{
                select:{fn:function(combo, value) {  
                }}
            }
    });
    
});


function limpia(){
    $('#infopagodettype_valor').val('');
    $('#infopagodettype_factura').val('');
    $('#infopagodettype_forma_pago').val('');
    $('#infopagodettype_numero_cuenta').val('');
    $('#infopagodettype_numero_tarjeta').val('');
    $('#infopagodettype_comentario').val('');
    $('#infopagodettype_numero_retencion').val('');
    ocultarDiv('div_datos_factura');
    presentaFormaPago();   
}

    
function mostrarDiv(div){
    capa = document.getElementById(div);
    capa.style.display = 'block';    
}
function ocultarDiv(div){
    capa = document.getElementById(div);
    capa.style.display = 'none';    
}



function presentaFormaPago()
{
    
    obtieneDatosContrato();
    var textFormaDePago=$("select[id='infopagodettype_forma_pago'] option:selected").text();
    if (textFormaDePago.toUpperCase()=='DEBITO BANCARIO')
    {                    
        mostrarDiv('div_ctaBancaria');
        ocultarDiv('div_tCredito');
        ocultarDiv('div_cheque');
        ocultarDiv('div_retencion');
        storeBancos.load({params: {es_tarjeta: 'N'}});
        resetCombos();
    }
    if(textFormaDePago.toUpperCase()=='TARJETA DE CREDITO')
    {
        ocultarDiv('div_ctaBancaria');
        ocultarDiv('div_cheque');
        mostrarDiv('div_tCredito');
        ocultarDiv('div_retencion');
        storeBancos1.load({params: {es_tarjeta: 'S'}});
        resetCombos();                                       
    }
    if(textFormaDePago.toUpperCase()=='EFECTIVO')
    {
        ocultarDiv('div_ctaBancaria');
        ocultarDiv('div_cheque');
        ocultarDiv('div_tCredito');
        ocultarDiv('div_retencion');
        resetCombos(); 
        
    }
    if(textFormaDePago.toUpperCase()=='CHEQUE')
    {
        storeBancosCheque.load({params: {es_tarjeta: 'N'}});    
        ocultarDiv('div_ctaBancaria');
        mostrarDiv('div_cheque');
        ocultarDiv('div_tCredito'); 
        ocultarDiv('div_retencion'); 
        resetCombos();   
    }  
    if(
    textFormaDePago.toUpperCase()=='RETENCION FUENTE 2%'
    ||textFormaDePago.toUpperCase()=='RETENCION FUENTE 8%'
    ||textFormaDePago.toUpperCase()=='RETENCION IVA 70%'
    ||textFormaDePago.toUpperCase()=='RETENCION IVA 100%')
    { 
        ocultarDiv('div_ctaBancaria');
        mostrarDiv('div_retencion');
        ocultarDiv('div_tCredito'); 
        ocultarDiv('div_cheque'); 
        resetCombos();   
    }      
    if(!$("#infopagodettype_forma_pago").val()){
        ocultarDiv('div_ctaBancaria');
        ocultarDiv('div_tCredito');
        ocultarDiv('div_cheque');
        ocultarDiv('div_retencion');
        resetCombos();
    }

}

function resetCombos(){  
        combo_bancos.reset();
        combo_bancos2.reset();
        combo_bancos_cheque.reset();
        combo_tipocuenta.reset();
        combo_tipotarjeta.reset();    
}


