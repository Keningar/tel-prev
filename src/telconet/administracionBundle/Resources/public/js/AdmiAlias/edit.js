Ext.onReady(function() {           
  
      var storeEmpresas = new Ext.data.Store({ 
	      pageSize: 10,
	      model: 'ModelStore',
	      total: 'total',
	      proxy: {
		  type: 'ajax',
		  timeout: 600000,
		  url : '/administracion/comunicacion/admi_alias/getEmpresas',
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
				  
 					Ext.getCmp('ciudad_cmb').reset();																											
 					Ext.getCmp('ciudad_cmb').setDisabled(false);	
					
					Ext.getCmp('departamento_cmb').reset();																											
					Ext.getCmp('departamento_cmb').setDisabled(true);
					
					document.getElementById('id_jurisdiccion_hd').value = '';
					document.getElementById('id_departamento_hd').value = '';
					
 					storeCiudadEmpresa.proxy.extraParams = { empresa:combo.getValue()};
 					storeCiudadEmpresa.load();
				}
			},
			forceSelection: true
		  });	
  
	
      var storeCiudadEmpresa  = new Ext.data.Store({ 
	      pageSize: 10,
	      model: 'ModelStore',
	      total: 'total',
	      proxy: {
		  type: 'ajax',
		  timeout: 600000,
		  url : '/administracion/comunicacion/admi_alias/getCiudadesPorEmpresa',
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
		  url : '/administracion/comunicacion/admi_alias/getDepartamentosPorEmpresaYCiudad',
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
	
            
      Ext.getCmp('empresas_cmb').setRawValue(document.getElementById('nombre_empresa_hd').value);            
      
      if(document.getElementById('id_jurisdiccion_hd').value!=0){		    
		    
	    Ext.getCmp('ciudad_cmb').setDisabled(false);	    
	    Ext.getCmp('ciudad_cmb').setRawValue(document.getElementById('nombre_jurisdiccion_hd').value);
	    
      }
      
      if(document.getElementById('id_departamento_hd').value!=0){		    
		    
	    Ext.getCmp('departamento_cmb').setDisabled(false);	    
	    Ext.getCmp('departamento_cmb').setRawValue(document.getElementById('nombre_departamento_hd').value);
	    
      }
  
  
  
});
function guardar(){  	  
  
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
        	    
	    id = document.getElementById("id_alias_hd").value;	    
	    valor   = document.getElementById("telconet_schemabundle_admialiastype_valor").value;
	    
	    if(Ext.getCmp('empresas_cmb').value!=null)	    
		empresa = Ext.getCmp('empresas_cmb').value;
	    else empresa = document.getElementById('id_empresa_hd').value;  
	    
	    if( Ext.getCmp('ciudad_cmb').value!=null)
		jurisdiccion = Ext.getCmp('ciudad_cmb').value;
	    else jurisdiccion = document.getElementById('id_jurisdiccion_hd').value;
	    
	     if( Ext.getCmp('departamento_cmb').value!=null)
		departamento = Ext.getCmp('departamento_cmb').value;
	    else departamento = document.getElementById('id_departamento_hd').value;
	    
	    if(valor==''){
		    Ext.Msg.alert('Alerta ','Debe ingresar el correo del alias');
	    }	    	   
	    
	    if(valor!='')	    
	    {	   
	      
		   if(validarEmail(valor)){
		     
			   conn.request({
				method: 'POST',	    
				params :{
					  id : id,
					  valor : valor,
					  empresa : empresa,
					  ciudad : jurisdiccion    ,
					  departamento : departamento
				},
				url: 'update',
				success: function(response){
							      
					 var json = Ext.JSON.decode(response.responseText);
					  
					  if(json.success == true)
					  {						 
					      window.location = "show";						 
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