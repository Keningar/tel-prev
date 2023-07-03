Ext.require([
	'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);
Ext.onReady(function(){   	  	 

	  var storeInterfaces = new Ext.data.Store({  
			pageSize: 50,
			autoLoad: true,
			proxy: {
			    type: 'ajax',
			    url : 'getInterfaceElementos',
			    reader: {
				type: 'json',
				totalProperty: 'total',
				root: 'encontrados'
			    },
			    extraParams: {
				//idServicio: rec.data.id_servicio_trasladado
			    }
			},
			fields:
			    [
				{name:'idInterfaceElemento', mapping:'id_interface_elemento'},
				{name:'idDetalleInterface', mapping:'id_detalle_interface'},
			        {name:'puertos', mapping:'puertos'},
				{name:'detalleModulo', mapping:'detalle_nombre'},
				{name:'valorModulo', mapping:'detalle_valor'}	
			    ]
		  });
	    
		
		  Ext.define('Interfaces', {
		      extend: 'Ext.data.Model',
		      fields: [
			    {name:'idInterfaceElemento', mapping:'id_interface_elemento'},
			    {name:'idDetalleInterface', mapping:'id_detalle_interface'},
			    {name:'puertos', mapping:'puertos'},
			    {name:'detalleModulo', mapping:'detalle_nombre'},
			    {name:'valorModulo', mapping:'detalle_valor'}			    
		      ]
		  });
		  
		  
		var cellEditingInterfaces = Ext.create('Ext.grid.plugin.CellEditing', {
			clicksToEdit: 1,
			listeners: {
			    edit: function(editor,object){
			      var rowIdx = object.rowIdx ;
			      var column= object.field;
			      var currentIp= object.value;
			      var storeInterfaces = gridInterfaces.getStore().getAt(rowIdx);
			      
			  }	
			}
		    });
		    
		    var selInterfaces = Ext.create('Ext.selection.CheckboxModel', {
			listeners: {
			    selectionchange: function(sm, selections) {
				gridInterfaces.down('#removeButton').setDisabled(selections.length == 0);
			    }
			}
		    });
		    		    
		    gridInterfaces = Ext.create('Ext.grid.Panel', {
			id:'gridInterfaces',
			store: storeInterfaces,
			columnLines: true,
			columns: [	
			{
			    id: 'idInterfaceElemento',
			    header: 'idInterfaceElemento',
			    dataIndex: 'idInterfaceElemento',
			    hidden: true,
			    hideable: false			    		    
			},
			{
			    id: 'idDetalleInterface',
			    header: 'idDetalleInterface',
			    dataIndex: 'idDetalleInterface',			    
			    hidden: true,
			    hideable: false			   
			},
			{
			    header: 'Interfaz (Puertos)',
			    dataIndex: 'puertos',
			    width: 200,
			    editor: {
				id:'puertos',
				name:'puertos',
				xtype: 'textfield',
				valueField: ''
			    }
			},
			{			    
			    header: 'Modulo',
			    dataIndex: 'detalleModulo',
			    width: 100,
			    sortable: true,
			    editor: {				
				queryMode: 'local',
				xtype: 'combobox',
				displayField:'detalleModulo',
				valueField: 'detalleModulo',							
				store: [
					['module1','module1'],
					['module2','module2'],					
				],
			    }
			},
			{
			    header: 'Valor',
			    dataIndex: 'valorModulo',
			    width: 150,
			    editor: {
				id:'valorModulo',
				name:'valorModulo',
				xtype: 'textfield',
				valueField: ''
			    }
			}],
			selModel: selInterfaces,
			viewConfig:{
			    stripeRows:true
			},

			// inline buttons
			dockedItems: [{
			    xtype: 'toolbar',
			    items: [
			    {
				itemId: 'removeButton',
				text:'Eliminar',
				tooltpuertos:'Elimina el item seleccionado',
				iconCls:'remove',
				disabled: true,
				handler : function(){eliminarSeleccion(gridInterfaces);}
			    },
			    '-',
			    {
				text:'Agregar',
				tooltpuertos:'Agrega un item a la lista',
				iconCls:'add',
				disabled:false,
				handler : function(){
				  
				    // Create a model instance
				    var r = Ext.create('Interfaces', { 
					puertos: '',
					detalleModulo: '',
					valorModulo: ''					
				    });
				    		
				    storeInterfaces.insert(0, r);
				    cellEditingInterfaces.startEditByPosition({row: 0, column: 1});
				}
			    }
			      
			    ]
			}],
			frame: true,
			width:450,
			height: 300,
			renderTo: 'interfacesElementos',
			title: 'Ingreso de Interfaces',
			plugins: [cellEditingInterfaces]
		    });  	
});
	
function eliminarSeleccion(datosSelect)
{
	for(var i = 0; i < datosSelect.getSelectionModel().getCount(); i++)
	{
		  datosSelect.getStore().remove(datosSelect.getSelectionModel().getSelection()[i]);
	}
}
function validateForm(){
  
    var array = obtenerInterfaces(gridInterfaces);
    
    if(array!=""){
	  Ext.Msg.alert("Mensaje","Interfaces Agregadas Correctamente");
	  $('#interfaces_escogidas').val(array);					    
    }
      
    if(obtenerInterfaces(gridInterfaces)==""){
      Ext.Msg.alert("Alerta","Debe agregar al menos una Interfaz");
      return false;
    }else return true;
  
}
function guardar(){
  
      var array = obtenerInterfaces(gridInterfaces);
      
      //console.log(array);
      
      marcarId = document.getElementById("telconet_schemabundle_elementogatewaytype_nombreMarcaElemento").value;
   
      if(array!="" || marcarId!="Escoja una opcion"){
	    
	    $('#interfaces_escogidas').val(array);
	    
	    id = document.getElementById("id_elemento").value;
	    marcarId = document.getElementById("telconet_schemabundle_elementogatewaytype_nombreMarcaElemento").value;
	    nombreGw = document.getElementById("telconet_schemabundle_elementogatewaytype_nombreElemento").value;
	    descriGw = document.getElementById("telconet_schemabundle_elementogatewaytype_descripcionElemento").value;
	    ip = document.getElementById("telconet_schemabundle_infoiptype_ip").value;
	    subred = document.getElementById("telconet_schemabundle_infoiptype_mascara").value;
	    gateway = document.getElementById("telconet_schemabundle_infoiptype_gateway").value;
	    //usuario = document.getElementById("telconet_schemabundle_admiusuarioaccesotype_nombreUsuarioAcceso").value;
	    password = document.getElementById("telconet_schemabundle_infocontrasenatype_contrasena").value;
	    empresa = document.getElementById("telconet_schemabundle_empresastype_nombreEmpresa").value;
	    interfaces = document.getElementById("interfaces_escogidas").value;
	    
	    //console.log(id+" "+marcarId+" "+nombreGw+" "+descriGw+" "+ip+" "+subred+" "+gateway+" "+password+" "+interfaces);
	    
	    Ext.Ajax.request({
				      url: "update",
				      method: 'post',
				      params: { 
					      id : id,
					      marcarId : marcarId, 
					      nombreGw: nombreGw, 
					      descriGw: descriGw, 
					      ip: ip, 
					      subred: subred,
					      gateway: gateway,
					     // usuario: usuario,
					      password: password, 				  
					      interfaces: interfaces,
					      empresa:empresa
				      },
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
				      failure: function() {					
					      Ext.Msg.alert('Alerta ','Error al realizar la accion');
				      }
			      });
      
      }else{
  
	    
      }
  
}
function obtenerInterfaces(gridInterfaces){
      if(gridInterfaces.getStore().getCount()>=1){
	
	      var array_relaciones = new Object();	      	      	      	      
	      
	      array_relaciones['total'] =  gridInterfaces.getStore().getCount();
	      array_relaciones['caracteristicas'] = new Array();
	      var array_data = new Array();
	      for(var i=0; i < gridInterfaces.getStore().getCount(); i++)
	      {		    		    
		    puertos = gridInterfaces.getStore().getAt(i).data['puertos'];
		    detalle = gridInterfaces.getStore().getAt(i).data['detalleModulo'];
		    valor   = gridInterfaces.getStore().getAt(i).data['valorModulo'];
		    
		    if(puertos==null || puertos==""){
			array_data =  new Array();
			Ext.Msg.alert("Alerta","Debe ingresar el puerto");
			return "";
		    }
		    if(detalle==null || detalle==""){
			array_data =  new Array();
			Ext.Msg.alert("Alerta","Debe ingresar el nombre del Modulo");
			return "";
		    }
		    if(valor==null || valor==""){
			array_data =  new Array();
			Ext.Msg.alert("Alerta","Debe ingresar el valor del Modulo");
			return "";
		    }
	      
		    array_data.push(gridInterfaces.getStore().getAt(i).data);
	      }	      	      
	      
	      array_relaciones['caracteristicas'] = array_data;
	      return Ext.JSON.encode(array_relaciones);
    }else{
	    Ext.Msg.alert("Alerta","Debe agregar al menos una Interfaz");
	    return "";
    }	
  
}