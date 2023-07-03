
var alias = '';
var borrarAlias = false;

Ext.require([
	'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);
Ext.onReady(function(){   
  
	   var panel = new Ext.panel.Panel({	
	      title: 'Plantilla Notificacion',
	      renderTo: "plantilla_mail",
	      width: 950,
	      height: 600,
	      frame: true,
	      layout: 'fit',	     
	      items: {
		  xtype: 'textarea',
		  id: 'plantillaPanel',
		  value:document.getElementById('plantilla_hd').value
	      },
	       dockedItems: 
	      [		
		    {
		      xtype: 'toolbar',
		      dock: 'top',
		      align: '->',
		      items: 
		      [      
			  {				
				text: 'Escoger Aliases',
				scope: this,
				handler: function(){ verAliases(idPlantilla); }
			  },
			  { xtype: 'tbfill' },
			  {
				xtype: 'textfield',
				id: 'txtCorreo',
				fieldLabel: 'Correo Prueba',
				value: '',
				width: '300',
				height : '50'
                          },
			  {				
				text: 'Enviar Prueba',
				scope: this,
				handler: function(){ ejecutarPrueba();}
			  }
		      ]
		    }
              ]        
	  });
            
});
	

function ejecutarPrueba(){
  
      correo = Ext.getCmp('txtCorreo').value;  
      
      if(validarEmail(correo) && Ext.getCmp('plantillaPanel').value!=''){
  
	    var conn = new Ext.data.Connection({
	      listeners: {
		  'beforerequest': {
		      fn: function (con, opt) {
			  Ext.get(document.body).mask('Enviando Plantilla a '.correo);
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
			  correoPrueba: correo,
			  plantilla: Ext.getCmp('plantillaPanel').value,		    
		  },
		  url: '/administracion/comunicacion/admi_plantilla/envioPrueba',
		  success: function(response){
			  			
			  var json = Ext.JSON.decode(response.responseText);			  
			  Ext.Msg.alert('Mensaje',json.mensaje);
		  },
		  failure: function(response) {
			  var json = Ext.JSON.decode(response.responseText);
			  Ext.Msg.alert('Error ',json.mensaje);
		  }
	  });
      
    }
  
}

	
function validarEmail( email ) {
    expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if ( !expr.test(email) ){
        Ext.Msg.alert("Error","La dirección de correo " + email + " es incorrecta.");
	return false;
    }else return true;
}

/**
*
* Guardar la edición de la Plantilla.
*
* @author Verion Inicial
* @version 1.0 
*
* @author Néstor Naula <nnaulal@telconet.ec>
* @version 1.1 - Bandera para redireccionar a la pantalla de ECUCERT si es su origen
* @since 1.0
*
*/
function guardar(idBanderaEcucert,urlEcucert)
{      	       
    id = document.getElementById("id_plantilla_hd").value;
    nombrePlantilla = document.getElementById("telconet_schemabundle_admiplantillatype_nombrePlantilla").value;
    codigo = document.getElementById("telconet_schemabundle_admiplantillatype_codigo").value;
    modulo = document.getElementById("telconet_schemabundle_admiplantillatype_modulo").value;
    plantilla = Ext.getCmp('plantillaPanel').value;
    
    editaConCambioCorreo = false;

    if (nombrePlantilla !== "" && codigo !== "" && plantilla !== null) {

        var conn = new Ext.data.Connection({
            listeners: {
                'beforerequest': {
                    fn: function(con, opt) {
                        Ext.get(document.body).mask('Guardando...');
                    },
                    scope: this
                },
                'requestcomplete': {
                    fn: function(con, res, opt) {
                        Ext.get(document.body).unmask();
                    },
                    scope: this
                },
                'requestexception': {
                    fn: function(con, res, opt) {
                        Ext.get(document.body).unmask();
                    },
                    scope: this
                }
            }
        });

        conn.request({
            method: 'POST',
            params: {
                id: id,
                nombrePlantilla: nombrePlantilla,
                codigo: codigo,
                plantilla: plantilla,
                correos: aliasGestionados,
                modulo: modulo
            },
            url: urlUpdate,
            success: function(response) {

                var json = Ext.JSON.decode(response.responseText);

                if (json.success === true)
                {
					if(idBanderaEcucert == 1)
					{
						window.location.href = urlEcucert;
					}
					else
					{
                    	window.location = "show";
					}
                }
                else
                {
                    Ext.Msg.alert('Error ', json.mensaje);
                }
            },
            failure: function(response) {
                Ext.Msg.alert('Alerta ', 'Error al realizar la accion');
            }
        });
    } 
    else 
    {
        if (nombrePlantilla === "")
            Ext.Msg.alert("Alerta", "Debe ingresar el nombre de la Plantilla");
        else if (codigo === "")
            Ext.Msg.alert("Alerta", "Debe ingresar el codigo de la Plantilla");
        else if (plantilla === null)
            Ext.Msg.alert("Alerta", "Debe ingresar el cuerpo de la notificacion");
    }

}
