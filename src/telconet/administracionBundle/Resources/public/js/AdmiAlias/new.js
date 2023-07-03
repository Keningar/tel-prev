

Ext.onReady(function() { 
  
  
      var storeEmpresas = new Ext.data.Store({ 
	      pageSize: 10,
	      model: 'ModelStore',
	      total: 'total',
	      proxy: {
		  type: 'ajax',
		  timeout: 600000,
		  url : 'getEmpresas',
		  reader: {
		      type: 'json',
		      totalProperty: 'total',
		      root: 'encontrados'
		  }		 		  				
	      },
	      fields:
			    [
			        {name:'id_empresa', mapping:'id_empresa'},
				{name:'nombre_empresa', mapping:'nombre_empresa'}				
			    ],	     
	  }); 
      
      
        combo_empresas = new Ext.form.ComboBox({
			  id:'empresas_cmb',
			  name: 'empresas_cmb',
			  displayField:'nombre_empresa',
			  valueField: 'id_empresa',
			  store: storeEmpresas,
			  loadingText: 'Buscando ...',			  
			  fieldLabel: false,	
			  queryMode: "remote",
			  emptyText: '',
			  listClass: 'x-combo-list-small',
			  renderTo:'empresas',
			  width:250,
			  listeners: {
				select: function(combo){							
				  
// 					Ext.getCmp('jurisdiccion_cmb').reset();																											
// 					Ext.getCmp('jurisdiccion_cmb').setDisabled(false);
					
					Ext.getCmp('ciudad_cmb').reset();																											
					Ext.getCmp('ciudad_cmb').setDisabled(false);
					
					Ext.getCmp('departamento_cmb').reset();																											
					Ext.getCmp('departamento_cmb').setDisabled(true);
					
// 					storeJurisdiccion.proxy.extraParams = { idEmpresa:combo.getValue()};
// 					storeJurisdiccion.load();
					
					storeCiudadEmpresa.proxy.extraParams = { empresa:combo.getValue()};
					storeCiudadEmpresa.load();
				}
			},
			forceSelection: true
		  });	
  
//        var storeJurisdiccion  = new Ext.data.Store({ 
// 	      pageSize: 10,
// 	      model: 'ModelStore',
// 	      total: 'total',
// 	      proxy: {
// 		  type: 'ajax',
// 		  timeout: 600000,
// 		  url : 'getJurisdiccionXEmpresa',
// 		  reader: {
// 		      type: 'json',
// 		      totalProperty: 'total',
// 		      root: 'encontrados'
// 		  }		  		  				
// 	      },
// 	      fields:
// 			    [
// 			        {name:'idJurisdiccion', mapping:'idJurisdiccion'},
// 				{name:'nombreJurisdiccion', mapping:'nombreJurisdiccion'}				
// 			    ],	     
// 	  }); 
// 
// 	
// 	combo_jurisdiccion = new Ext.form.ComboBox({
// 		    id:'jurisdiccion_cmb',
// 		    name: 'jurisdiccion_cmb',
// 		    displayField:'nombreJurisdiccion',
// 		    valueField: 'idJurisdiccion',
// 		    store: storeJurisdiccion,
// 		    loadingText: 'Buscando ...',			  
// 		    fieldLabel: false,	
// 		    queryMode: "local",
// 		    emptyText: '',
// 		    listClass: 'x-combo-list-small',
// 		    renderTo:'jurisdicciones',
// 		    width:250,
// 		    disabled:true
// 	    });	
	
	var storeCiudadEmpresa  = new Ext.data.Store({ 
	      pageSize: 10,
	      model: 'ModelStore',
	      total: 'total',
	      proxy: {
		  type: 'ajax',
		  timeout: 600000,
		  url : 'getCiudadesPorEmpresa',
		  reader: {
		      type: 'json',
		      totalProperty: 'total',
		      root: 'encontrados'
		  }		  		  				
	      },
	      fields:
			    [
			        {name:'id_canton', mapping:'id_canton'},
				{name:'nombre_canton', mapping:'nombre_canton'}				
			    ],	     
	  }); 

	
	combo_ciudad = new Ext.form.ComboBox({
		    id:'ciudad_cmb',
		    name: 'ciudad_cmb',
		    displayField:'nombre_canton',
		    valueField: 'id_canton',
		    store: storeCiudadEmpresa,
		    loadingText: 'Buscando ...',			  
		    fieldLabel: false,	
		    queryMode: "local",
		    emptyText: '',
		    listClass: 'x-combo-list-small',
		    renderTo:'ciudades',
		    width:250,
		    disabled:true,
		    listeners:{
			  select:function(combo){
			    
				Ext.getCmp('departamento_cmb').reset();																											
				Ext.getCmp('departamento_cmb').setDisabled(false);
				
				empresa = Ext.getCmp('empresas_cmb').value;																											
				
				storeDepartamento.proxy.extraParams = { id_canton:combo.getValue(), empresa:empresa};
				storeDepartamento.load();
								    
			  }
		      
		    }
	    });	
	
	
	var storeDepartamento  = new Ext.data.Store({ 
	      pageSize: 10,
	      model: 'ModelStore',
	      total: 'total',
	      proxy: {
		  type: 'ajax',
		  timeout: 600000,
		  url : 'getDepartamentosPorEmpresaYCiudad',
		  reader: {
		      type: 'json',
		      totalProperty: 'total',
		      root: 'encontrados'
		  }		  		  				
	      },
	      fields:
			    [
			        {name:'id_departamento', mapping:'id_departamento'},
				{name:'nombre_departamento', mapping:'nombre_departamento'}				
			    ],	     
	  }); 

	
	combo_departamento = new Ext.form.ComboBox({
		    id:'departamento_cmb',
		    name: 'departamento_cmb',
		    displayField:'nombre_departamento',
		    valueField: 'id_departamento',
		    store: storeDepartamento,
		    loadingText: 'Buscando ...',			  
		    fieldLabel: false,	
		    queryMode: "local",
		    emptyText: '',
		    listClass: 'x-combo-list-small',
		    renderTo:'departamentos',
		    width:250,
		    disabled:true
	    });	
	
	
  
  
  
  
});

function guardar(){                   		  
	
	    valor = document.getElementById("telconet_schemabundle_admialiastype_valor").value;
	  
	    empresa      = Ext.getCmp('empresas_cmb').value;
	    ciudad = Ext.getCmp('ciudad_cmb').value;	    	    
	    departamento = Ext.getCmp('departamento_cmb').value;	    	    
	    
	   
	    if(empresa==null){ 
		    Ext.Msg.alert('Alerta ','Debe escoger la empresa a asignar el alias de correo');
	    }
	    
	    else if(valor==''){
		    Ext.Msg.alert('Alerta ','Debe ingresar el correo del alias');
	    }
	    
	    if(valor!='' && empresa!=null)	    
	    {	      	   
	      
		    if(validarEmail(valor)){
		      
			  var conn = new Ext.data.Connection({
			      listeners: {
				  'beforerequest': {
				      fn: function (con, opt) {
					  Ext.get(document.body).mask('Guardando...');
				      },
				      scope: this
				  },
				  'requestcomplete': {
				      fn: function (con, res, opt) {
					  Ext.get(document.body).unmask();
				      },
				      scope: this
				  },
				  'requestexception': {
				      fn: function (con, res, opt) {
					  Ext.get(document.body).unmask();
				      },
				      scope: this
				  }
			      }
			  });
			  
			   conn.request({
				method: 'POST',	    
				params :{					  
					  valor : valor,
					  empresa : empresa,
					  ciudad : ciudad,
					  departamento:departamento
				},
				url: 'create',
				success: function(response){
							      
					  var json = Ext.JSON.decode(response.responseText);
					  
					  if(json.success == true)
					  {						      
						  window.location = json.id+"/show";
					  }
					  else
					  {
						  Ext.Msg.alert('Alerta ',json.mensaje);						
					  }
				},
				failure: function(response) {
					 Ext.Msg.alert('Alerta ','Error al realizar la accion');
				}
			  });	
	    
		    
		    }
	    
	    }
      
  
}

function validarEmail( email ) {
    expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if ( !expr.test(email) ){
        Ext.Msg.alert("Error","La dirección de correo " + email + " es incorrecta.");
	return false;
    }else return true;
}