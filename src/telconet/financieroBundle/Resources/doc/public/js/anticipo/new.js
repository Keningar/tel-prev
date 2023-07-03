Ext.require([
    '*'
]);

Ext.onReady(function(){
    Ext.define('InfoPagoDetModel', {
        extend: 'Ext.data.Model',
        fields: [
            // the 'name' below matches the tag name to read, except 'availDate'
            // which is mapped to the tag 'availability'
            {name: 'formaPago', type: 'string'},
            {name: 'formaPagoText', type: 'string'},
            {name: 'factura', type:'string'},
            {name: 'facturaText', type:'string'},
            {name: 'banco', type: 'string'},
            {name: 'bancoText', type: 'string'},
            {name: 'tipoCuenta', type: 'string'},
            {name: 'tipoCuentaText', type: 'string'},
            {name: 'numeroCta', type: 'string'},
            {name: 'valor', type: 'float'},
            {name: 'comentario', type: 'string'}
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
            width: 140,
            align: 'right'
        }, {
            text: 'Banco',
            dataIndex: 'bancoText',
            width: 130,
            align: 'right'
        }, {
            text: 'Tipo Cuenta',
            dataIndex: 'tipoCuentaText',
            width: 130,
            align: 'right'
        }, {
            text: 'Numero',
            dataIndex: 'numeroCta',
            width: 100,
            align: 'right'
        }, {
            text: 'Valor',
            dataIndex: 'valor',
            width: 70,
            align: 'right'
        }, {
            text: 'Comentario',
            dataIndex: 'comentario',
            width: 150,
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
        selModel: {
            selType: 'cellmodel'
        },
        renderTo: Ext.get('lista_grid'),
        width: 850,
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
    combo_bancos_deposito = new Ext.form.ComboBox({
            id: 'cmb_bancos_deposito',
            name: 'cmb_bancos_deposito',
            fieldLabel: false,
            anchor: '100%',
            queryMode:'local',
            width: 200,
            emptyText: 'Seleccione Banco',
            store: storeBancosContables,
            displayField: 'descripcion_banco',
            valueField: 'id_banco',
            renderTo: 'combo_banco_deposito',
            listeners:{
                select:{fn:function(combo, value) {
                    //Ext.getCmp('cmb_tipocuenta_deposito').reset();  
                    //Ext.getCmp('cmb_accion').reset();  
                    
                    storeCuentasBancosContables.proxy.extraParams = {id_banco: combo.getValue(),es_tarjeta:'N'};
                    storeCuentasBancosContables.load({params: {}});

                }}
            }				
    });
    combo_bancos_transferencia = new Ext.form.ComboBox({
            id: 'cmb_bancos_transferencia',
            name: 'cmb_bancos_transferencia',
            fieldLabel: false,
            anchor: '100%',
            queryMode:'local',
            width: 200,
            emptyText: 'Seleccione Banco',
            store:storeBancos,
            displayField: 'descripcion_banco',
            valueField: 'id_banco',
            renderTo: 'combo_banco_transferencia',
            listeners:{
                select:{fn:function(combo, value) {
                    Ext.getCmp('cmb_tipocuenta_transferencia').reset();  
                    //Ext.getCmp('cmb_accion').reset();  
                    
                    storeCuentasBancosContables.proxy.extraParams = {id_banco: combo.getValue(),es_tarjeta:'N'};
                    storeCuentasBancosContables.load({params: {}});

                }}
            }				
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

    combo_tipocuenta_deposito = new Ext.form.ComboBox({
            id: 'cmb_tipocuenta_deposito',
            name: 'cmb_tipocuenta_deposito',
            fieldLabel: false,
            anchor: '100%',
            queryMode:'local',
            width: 200,
            emptyText: 'Seleccione tipo cuenta',
            store: storeCuentasBancosContables,
            displayField: 'descripcion_cuenta',
            valueField: 'id_cuenta',
            renderTo: 'combo_tipo_cuenta_deposito',
            listeners:{
                select:{fn:function(combo, value) {  
                }}
            }
    });
    combo_tipocuenta_transferencia = new Ext.form.ComboBox({
            id: 'cmb_tipocuenta_transferencia',
            name: 'cmb_tipocuenta_transferencia',
            fieldLabel: false,
            anchor: '100%',
            queryMode:'local',
            width: 200,
            emptyText: 'Seleccione tipo cuenta',
            store: storeCuentasBancosContables,
            displayField: 'descripcion_cuenta',
            valueField: 'id_cuenta',
            renderTo: 'combo_tipo_cuenta_transferencia',
            listeners:{
                select:{fn:function(combo, value) {  
                }}
            }
    });
});	
function agregaDetalle(){
var banco=''; var bancoText=''; var tipoCuenta='';var tipoCuentaText=''; var numeroCuenta='';var rec='';
var textFormaDePago=$("select[id='infopagodettype_forma_pago'] option:selected").text();
//##obtiene valores segun forma de pago
       if (textFormaDePago.toUpperCase()=='TARJETA DE CREDITO'){ 
         banco=Ext.getCmp('cmb_bancos2').getValue();
         tipoCuenta=Ext.getCmp('cmb_tipotarjeta').getValue();
         bancoText=Ext.getCmp('cmb_bancos2').getRawValue();
         tipoCuentaText=Ext.getCmp('cmb_tipotarjeta').getRawValue();         
         numeroCuenta=$('#infopagodettype_numero_tarjeta').val();
       }
       else
       {
            if (textFormaDePago.toUpperCase()=='CHEQUE'){ 
              banco=Ext.getCmp('cmb_bancos_cheque').getValue();
              bancoText=Ext.getCmp('cmb_bancos_cheque').getRawValue();         
              numeroCuenta=$('#infopagodettype_numero_cheque').val();
              tipoCuenta=$('#infopagodettype_id_tipo_cuenta_cheque').val();
              tipoCuentaText=$('#infopagodettype_tipo_cuenta_cheque').val();
            }
            else
            {
                if(textFormaDePago.toUpperCase()=='RETENCION FUENTE 2%'
                ||textFormaDePago.toUpperCase()=='RETENCION FUENTE 8%')        
				{        
                 numeroCuenta=$('#infopagodettype_numero_retencion').val();
				}else{
				   if (textFormaDePago.toUpperCase()=='DEPOSITO'){           
					 banco=Ext.getCmp('cmb_bancos_deposito').getValue();
					 tipoCuenta=Ext.getCmp('cmb_tipocuenta_deposito').getValue();
					 bancoText=Ext.getCmp('cmb_bancos_deposito').getRawValue();
					 tipoCuentaText=Ext.getCmp('cmb_tipocuenta_deposito').getRawValue();         
					 numeroCuenta=$('#infopagodettype_numero_papeleta').val();
				   }else{
					   if (textFormaDePago.toUpperCase()=='TRANSFERENCIA'){           
						 banco=Ext.getCmp('cmb_bancos_transferencia').getValue();
						 tipoCuenta=Ext.getCmp('cmb_tipocuenta_transferencia').getValue();
						 bancoText=Ext.getCmp('cmb_bancos_transferencia').getRawValue();
						 tipoCuentaText=Ext.getCmp('cmb_tipocuenta_transferencia').getRawValue();         
						 numeroCuenta=$('#infopagodettype_numero_transferencia').val();
					   }				   
				   }
			   
			   }                 
            }
       }
    
//##Valida campos segun forma de pago
    //si es tarjeta de credito o cuenta bancaria
    if(
        (
        textFormaDePago.toUpperCase()=='TARJETA DE CREDITO' ||
		textFormaDePago.toUpperCase()=='DEPOSITO' ||
		textFormaDePago.toUpperCase()=='TRANSFERENCIA')
        && $('#infopagodettype_forma_pago').val()
        && banco && tipoCuenta && numeroCuenta && $('#infopagodettype_valor').val())
    {
        rec = new InfoPagoDetModel({'formaPago':$('#infopagodettype_forma_pago').val(),
            'formaPagoText':$("select[id='infopagodettype_forma_pago'] option:selected").text(),
            'banco':banco,'bancoText':bancoText,'tipoCuenta':tipoCuenta,'tipoCuentaText':tipoCuentaText,
            'numeroCta':numeroCuenta,'valor':$('#infopagodettype_valor').val(),'comentario':$('#infopagodettype_comentario').val()});
        storeDetalle.add(rec);
        calculaTotal();
        limpia();
    }
    else
    {
        //Si es efectivo
        if(
        textFormaDePago.toUpperCase()=='EFECTIVO' 
        && $('#infopagodettype_forma_pago').val()
        && $('#infopagodettype_valor').val())
        {
            rec = new InfoPagoDetModel({'formaPago':$('#infopagodettype_forma_pago').val(),
                'formaPagoText':$("select[id='infopagodettype_forma_pago'] option:selected").text(),
                'banco':'','bancoText':'','tipoCuenta':'','tipoCuentaText':'',
                'numeroCta':'','valor':$('#infopagodettype_valor').val(),'comentario':$('#infopagodettype_comentario').val()});
            storeDetalle.add(rec);
            calculaTotal();
            limpia();
        }
        else{
                //Si es Cheque
                if(
                textFormaDePago.toUpperCase()=='CHEQUE' 
                && $('#infopagodettype_forma_pago').val()
                && $('#infopagodettype_valor').val()&& banco)
                {
                    rec = new InfoPagoDetModel({'formaPago':$('#infopagodettype_forma_pago').val(),
                        'formaPagoText':$("select[id='infopagodettype_forma_pago'] option:selected").text(),
                        'banco':banco,'bancoText':bancoText,'tipoCuenta':tipoCuenta,'tipoCuentaText':tipoCuentaText,
                        'numeroCta':numeroCuenta,'valor':$('#infopagodettype_valor').val(),'comentario':$('#infopagodettype_comentario').val()});
                    storeDetalle.add(rec);
                    calculaTotal();
                    limpia();
                }
                else{
                        //Si es Retencion 2% o 8%
                        if(
                        textFormaDePago.toUpperCase()=='RETENCION FUENTE 2%'
                        ||textFormaDePago.toUpperCase()=='RETENCION FUENTE 8%'                   
                        && $('#infopagodettype_forma_pago').val()
                        && $('#infopagodettype_valor').val() )
                        {
                            rec = new InfoPagoDetModel({'formaPago':$('#infopagodettype_forma_pago').val(),
                            'formaPagoText':$("select[id='infopagodettype_forma_pago'] option:selected").text(),
                            'banco':'','bancoText':'','tipoCuenta':'','tipoCuentaText':'',
                            'numeroCta':numeroCuenta,'valor':$('#infopagodettype_valor').val(),
                            'comentario':$('#infopagodettype_comentario').val()});
                        }
                        else{
                            Ext.Msg.alert('Alerta ','Faltan campos por ingresar');
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
    $('#infopagodettype_valor').val('');
    $('#infopagodettype_forma_pago').val('');
    $('#infopagodettype_numero_cuenta').val('');
    $('#infopagodettype_numero_tarjeta').val('');
    $('#infopagodettype_comentario').val('');
    $('#infopagodettype_numero_retencion').val('');
	$('#infopagodettype_numero_papeleta').val('');
	$('#infopagodettype_numero_transferencia').val('');	
    presentaFormaPago();   
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
}
    


function mostrarDiv(div){
    capa = document.getElementById(div);
    capa.style.display = 'block';    
}
function ocultarDiv(div){
    capa = document.getElementById(div);
    capa.style.display = 'none';    
}



function presentaFormaPago(){
    var textFormaDePago=$("select[id='infopagodettype_forma_pago'] option:selected").text();
    if(textFormaDePago.toUpperCase()=='TARJETA DE CREDITO')
    {
        ocultarDiv('div_cheque');
        mostrarDiv('div_tCredito');
        ocultarDiv('div_retencion');
        ocultarDiv('div_deposito');
        ocultarDiv('div_transferencia');		
        storeBancos1.load({params: {es_tarjeta: 'S'}});
        resetCombos();                                       
    }
    if(textFormaDePago.toUpperCase()=='EFECTIVO')
    {
        ocultarDiv('div_cheque');
        ocultarDiv('div_tCredito');
        ocultarDiv('div_retencion');
        ocultarDiv('div_deposito');
        ocultarDiv('div_transferencia');		
        resetCombos(); 
        
    }
    if(textFormaDePago.toUpperCase()=='CHEQUE')
    {
        storeBancosCheque.load({params: {es_tarjeta: 'N'}});    
        mostrarDiv('div_cheque');
        ocultarDiv('div_tCredito'); 
        ocultarDiv('div_retencion');
        ocultarDiv('div_deposito');
        ocultarDiv('div_transferencia');		
        resetCombos();   
    }  
    if(textFormaDePago.toUpperCase()=='RETENCION FUENTE 2%'
    ||textFormaDePago.toUpperCase()=='RETENCION FUENTE 8%')
    { 
        mostrarDiv('div_retencion');
        ocultarDiv('div_tCredito'); 
        ocultarDiv('div_cheque'); 
        ocultarDiv('div_deposito');
        ocultarDiv('div_transferencia');		
        resetCombos();   
    }      
    if(textFormaDePago.toUpperCase()=='DEPOSITO')
    { 
        ocultarDiv('div_retencion');
        ocultarDiv('div_tCredito'); 
        ocultarDiv('div_cheque'); 
        mostrarDiv('div_deposito');
        ocultarDiv('div_transferencia');
        storeBancosContables.load({params: {es_tarjeta: 'N'}});		
        resetCombos();   
    } 	
    if(textFormaDePago.toUpperCase()=='TRANSFERENCIA')
    { 
        ocultarDiv('div_retencion');
        ocultarDiv('div_tCredito'); 
        ocultarDiv('div_cheque'); 
        ocultarDiv('div_deposito');
        mostrarDiv('div_transferencia');	
        storeBancosContables.load({params: {es_tarjeta: 'N'}});		
        resetCombos();   
    } 		
    if(!$("#infopagodettype_forma_pago").val()){
        ocultarDiv('div_tCredito');
        ocultarDiv('div_cheque');
        ocultarDiv('div_retencion');
        ocultarDiv('div_deposito');
        ocultarDiv('div_transferencia');		
        resetCombos();
    }

}

function resetCombos(){  
        combo_bancos2.reset();
        combo_bancos_cheque.reset();
        combo_bancos_deposito.reset();	
        combo_bancos_transferencia.reset();			
        combo_tipotarjeta.reset();    
combo_tipocuenta_deposito.reset();
combo_tipocuenta_transferencia.reset();			
}
