Ext.require([
    '*'
]);

Ext.onReady(function(){
    
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

    // create the grid and specify what field you want
    // to use for the editor at each header.
    grid = Ext.create('Ext.grid.Panel', {
        store: storeDetalle,
        columns: [ {
            text: 'Forma Pago',
            //header: 'Valor',
            dataIndex: 'formaPagoText',
            width: 140,
            align: 'right'
        }, {
            text: 'Factura',
            //header: 'Valor',
            dataIndex: 'facturaText',
            width: 100,
            align: 'right'
        }, {
            text: 'Banco',
            //header: 'Valor',
            dataIndex: 'bancoText',
            width: 130,
            align: 'right'
        }, {
            text: 'Tipo Cuenta',
            //header: 'Valor',
            dataIndex: 'tipoCuentaText',
            width: 130,
            align: 'right'
        }, {
            text: 'Numero',
            //header: 'Valor',
            dataIndex: 'numeroCta',
            width: 100,
            align: 'right'
        }, {
            text: 'Valor',
            //header: 'Valor',
            dataIndex: 'valor',
            width: 70,
            align: 'right'
        }, {
            text: 'Comentario',
            //header: 'Valor',
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
                    Ext.getCmp('cmb_tipocuenta_deposito').reset();  
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
            store:storeBancosContables,
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
   Ext.define('valoresFacturaModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'totalPagos', type:'string'},
            {name:'valorFactura', type:'string'}
        ]
    });
    storeValoresFact = Ext.create('Ext.data.Store', {
            model: 'valoresFacturaModel',
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url : url_valores_fact,
                reader: {
                    type: 'json',
                    root: 'datosFactura'
                }
            },
            listeners: {
                            load: function(store){
                                store.each(function(record) {
                                    //console.log('Pagos:'+record.data.totalPagos);
                                    //console.log('Factura:'+record.data.valorFactura);
                                    mostrarDiv('div_datos_factura');
                                    $('#div_datos_factura').html('Saldo: $'+(record.data.valorFactura-record.data.totalPagos));
                                    //idClienteSucursalSesion = record.data.idClienteSucursalSesion;
                                });
                    }
            } 
    });
    
    

    
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

function agregaDetalle(){
var banco=''; var bancoText=''; var tipoCuenta='';var tipoCuentaText=''; var numeroCuenta='';var rec='';
var valorPago=$('#infopagodettype_valor').val(); var validacionOk=false;
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
                if((textFormaDePago.toUpperCase()=='RETENCION FUENTE 2%'
                ||textFormaDePago.toUpperCase()=='RETENCION FUENTE 8%')) {        
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
        (textFormaDePago.toUpperCase()=='TARJETA DE CREDITO' ||
		textFormaDePago.toUpperCase()=='DEPOSITO' ||
		textFormaDePago.toUpperCase()=='TRANSFERENCIA')
        && $('#infopagodettype_forma_pago').val() && $('#infopagodettype_factura').val() 
        && banco && tipoCuenta && numeroCuenta && $('#infopagodettype_valor').val())
    {
        validacionOk=true;
        rec = new InfoPagoDetModel({'formaPago':$('#infopagodettype_forma_pago').val(),
            'formaPagoText':$("select[id='infopagodettype_forma_pago'] option:selected").text(),
            'factura':$('#infopagodettype_factura').val(),
            'facturaText':$("select[id='infopagodettype_factura'] option:selected").text(),
            'banco':banco,'bancoText':bancoText,'tipoCuenta':tipoCuenta,'tipoCuentaText':tipoCuentaText,
            'numeroCta':numeroCuenta,'valor':$('#infopagodettype_valor').val(),'comentario':$('#infopagodettype_comentario').val()});
        storeDetalle.add(rec);
        calculaTotal();
        limpia();
    }
    else
    {
        //console.log($("select[id='infopagodettype_forma_pago'] option:selected").text());
        //console.log($('#infopagodettype_forma_pago').val());
        //console.log($('#infopagodettype_factura').val());
        //console.log($('#infopagodettype_valor').val());
		//console.log(numeroCuenta);
		//console.log(banco);
		//console.log(tipoCuenta);
        //Si es efectivo
        if(textFormaDePago.toUpperCase()=='EFECTIVO'
        && $('#infopagodettype_forma_pago').val() && $('#infopagodettype_factura').val()  
        && $('#infopagodettype_valor').val())
        {
            validacionOk=true;
            rec = new InfoPagoDetModel({'formaPago':$('#infopagodettype_forma_pago').val(),
                'formaPagoText':$("select[id='infopagodettype_forma_pago'] option:selected").text(),
                'factura':$('#infopagodettype_factura').val(),
                'facturaText':$("select[id='infopagodettype_factura'] option:selected").text(),
                'banco':'','bancoText':'','tipoCuenta':'','tipoCuentaText':'',
                'numeroCta':'','valor':$('#infopagodettype_valor').val(),'comentario':$('#infopagodettype_comentario').val()});
            storeDetalle.add(rec);
            calculaTotal();
            limpia();
        }
        else{
                //Si es Cheque
                if(textFormaDePago.toUpperCase()=='CHEQUE'
                && $('#infopagodettype_forma_pago').val() && $('#infopagodettype_factura').val() 
                && $('#infopagodettype_valor').val()&& banco)
                {
                    validacionOk=true;
                    rec = new InfoPagoDetModel({'formaPago':$('#infopagodettype_forma_pago').val(),
                        'formaPagoText':$("select[id='infopagodettype_forma_pago'] option:selected").text(),
                        'factura':$('#infopagodettype_factura').val(),
                        'facturaText':$("select[id='infopagodettype_factura'] option:selected").text(),
                        'banco':banco,'bancoText':bancoText,'tipoCuenta':tipoCuenta,'tipoCuentaText':tipoCuentaText,
                        'numeroCta':numeroCuenta,'valor':$('#infopagodettype_valor').val(),'comentario':$('#infopagodettype_comentario').val()});
                    storeDetalle.add(rec);
                    calculaTotal();
                    limpia();
                }
                else{
                        //Si es Retencion 2% o 8%
                        if(
                        (textFormaDePago.toUpperCase()=='RETENCION FUENTE 2%' 
                        || textFormaDePago.toUpperCase()=='RETENCION FUENTE 8%')                    
                        && $('#infopagodettype_forma_pago').val() && $('#infopagodettype_factura').val() 
                        && $('#infopagodettype_valor').val() )
                        {
                            validacionOk=true;
                            rec = new InfoPagoDetModel({'formaPago':$('#infopagodettype_forma_pago').val(),
                            'formaPagoText':$("select[id='infopagodettype_forma_pago'] option:selected").text(),
                            'factura':$('#infopagodettype_factura').val(),
                            'facturaText':$("select[id='infopagodettype_factura'] option:selected").text(),
                            'banco':'','bancoText':'','tipoCuenta':'','tipoCuentaText':'',
                            'numeroCta':numeroCuenta,'valor':$('#infopagodettype_valor').val(),
                            'comentario':$('#infopagodettype_comentario').val()});

                           if (existeRetencionEnGrid($('#infopagodettype_factura').val())=='S'){
                               alert("Solo se puede ingresar 1 retencion por factura. Favor Corregir");
                               limpia();
                           }
                           else{
                                verificaRetencion($('#infopagodettype_factura').val(),rec);                             
                           }
                        }
                        else{
                            //console.log('faltan datos');
                            Ext.Msg.alert('Alerta ','Faltan campos por ingresar');
                        }
                }
        }
    }
    //Verifica si el pago excede el saldo de la factura
    if(validacionOk){
        var valor=storeValoresFact.getAt(0).data.valorFactura-storeValoresFact.getAt(0).data.totalPagos-valorPago;
        if(valor<0){
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
	//CREA PAGO
	//------------------------
	$.ajax(
	{
		type: "POST",
		data: "detalles=" + $('#infopagodettype_detalles').val(),
		url: url_graba_pago,
        beforeSend: function(){
			Ext.MessageBox.show({
			   msg: 'Saving your data, please wait...',
			   progressText: 'Saving...',
			   width:300,
			   wait:true,
			   waitConfig: {interval:200},
			   //icon:'ext-mb-download', //custom class in msg-box.html
			   //animateTarget: 'mb7'
		   });
        },		
		success: function(resp){
			
			var obj=JSON.parse(resp);
			//console.log(obj);
			var msg=obj.msg;
			if (msg != ''){
				if(msg=="error"){
					Ext.MessageBox.show({
						icon: Ext.Msg.ERROR,
						width:500,
						height: 300,
						title:'Mensaje del Sistema',
						msg: 'No se pudo procesar el pago, por favor consulte con el administrador.',
						buttonText: {yes: "Ok"},
						fn: function(btn){
							if(btn=='yes')
								window.top.location.href=obj.link;
						}
					});				
				}else{
					if(msg=="cerrar-conservicios"){
						Ext.MessageBox.show({
							icon: Ext.Msg.INFO,
						    width:500,
							height: 300,
							title:'Mensaje del Sistema',
							msg: 'Se proceso el pago y el cliente ya no tiene saldos adeudados. Se procedera a realizar la reactivacion.',
							buttonText: {yes: "Ok"},
							fn: function(btn){
								if(btn=='yes'){
									//REALIZA REACTIVACION MASIVA
									//-----------------------------
								   $.ajax({
											type: "POST",
											data: "param=" + obj.servicios,
											url: url_reactivacion_masiva,
											success: function(msg){
												if (msg != ''){
													if(msg=="OK"){
														Ext.MessageBox.show({
															icon: Ext.Msg.INFO,
															width:500,
															height: 300,
															title:'Mensaje del Sistema',
															msg: 'Se proceso la reactivacion masiva de los servicios con exito.',
															buttonText: {yes: "Ok"},
															fn: function(btn){
																if(btn=='yes')
																	window.top.location.href=obj.link;
															}
														});
													}else{
														Ext.MessageBox.show({
															icon: Ext.Msg.ERROR,
															width:500,
															height: 300,
															title:'Mensaje del Sistema',
															msg: 'No se proceso la reactivacion de los servicios, por favor consultar con el administrador.',
															buttonText: {yes: "Ok"},
															fn: function(btn){
																if(btn=='yes')
																	window.top.location.href=obj.link;
															}
														});
													}					
											   }
											   else
											   {
														Ext.MessageBox.show({
															icon: Ext.Msg.ERROR,
															width:500,
															height: 300,
															title:'Mensaje del Sistema',
															msg: 'No se pudo procesar la reactivacion de los servicios, por favor consultar con el administrador.',
															buttonText: {yes: "Ok"},
															fn: function(btn){
																if(btn=='yes')
																	window.top.location.href=obj.link;
															}
														});
											   }
											}          
									});	
								}
							}
						});					

					}else{
						if(msg=='nocerrar'){
							Ext.MessageBox.show({
								icon: Ext.Msg.INFO,
								width:500,
								height: 300,
								title:'Mensaje del Sistema',
								msg: 'Se registro el pago con exito pero el cliente aun tiene saldos adeudados.',
								buttonText: {yes: "Ok"},
								fn: function(btn){
									if(btn=='yes')
									//console.debug('you clicked: ',btn); //you clicked:  yes
									window.top.location.href=obj.link;
								}
							});							
						}else if(msg=='nocerrar-inaudit'){
                            Ext.MessageBox.show({
								icon: Ext.Msg.INFO,
								width:500,
								height: 300,
								title:'Mensaje del Sistema',
								msg: 'Se procesó el pago y el cliente no tiene saldos adeudados. Reactivación detenida por Proceso Posible Abusador.',
								buttonText: {yes: "Ok"},
								fn: function(btn){
									if(btn=='yes')
                                    {
                                        //console.debug('you clicked: ',btn); //you clicked:  yes
									    window.top.location.href=obj.link;
                                    }
								}
							});		
                        }else{
							if(msg=='cerrar-sinservicios'){
								Ext.MessageBox.show({
									icon: Ext.Msg.INFO,
									width:500,
									height: 300,
									title:'Mensaje del Sistema',
									msg: 'Se registro el pago con exito y el cliente ya no tiene saldos adeudados. No se encontro servicios para reactivar, por favor consultar con el administrador.',
									buttonText: {yes: "Ok"},
									fn: function(btn){
										if(btn=='yes')
										//console.debug('you clicked: ',btn); //you clicked:  yes
										window.top.location.href=obj.link;
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
				Ext.MessageBox.show({
					icon: Ext.Msg.ERROR,
					width:500,
					height: 300,
					title:'Mensaje del Sistema',
					msg: 'No se pudo procesar el pago, por favor consulte con el administrador.',
					buttonText: {yes: "Ok"},
					fn: function(btn){
						if(btn=='yes')
							window.top.location.href=obj.link;
					}
				});
		   }
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



function presentaFormaPago(){
    
    obtieneDatosContrato();
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
    if(
    textFormaDePago.toUpperCase()=='RETENCION FUENTE 2%'
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

function verificaRetencion(fact,rec){
   $.ajax({
			type: "POST",
			data: "fact=" + fact,
			url: url_verifica_retencion,
			success: function(msg){
				if (msg != ''){
					if(msg=="no"){
                                                //console.log(flagCorrecto);
                                                storeDetalle.add(rec);
                                                calculaTotal();
                                                limpia();
					}
					if(msg=="si"){
						alert("Ya existe retencion ingresada para esta factura. Favor Corregir");
                                                   limpia();
                                                //console.log(flagCorrecto);
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
        console.log('newFact:'+fact+''+'factura:'+grid.getStore().getAt(i).data.factura)
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
