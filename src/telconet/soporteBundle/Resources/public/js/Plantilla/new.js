/*
 * This example features a window with a DataView from which the user can select images to add to a <div> on the page.
 * To create the example we create simple subclasses of Window, DataView and Panel. When the user selects an image
 * we just add it to the page using the insertSelectedImage function below.
 * 
 * Our subclasses all sit under the Ext.chooser namespace so the first thing we do is tell Ext's class loader that it
 * can find those classes in this directory (InfoPanel.js, IconBrowser.js and Window.js). Then we just need to require
 * those files and pass in an onReady callback that will be called as soon as everything is loaded.
 */
Ext.QuickTips.init();
Ext.onReady(function(){
   
    Ext.tip.QuickTipManager.init();  // enable tooltips       
	
    new Ext.panel.Panel({
        title: 'Plantilla',
        renderTo: "plantilla_mail",
        width: 1000,
        height: 850,
        frame: true,
        layout: 'fit',
	rbar: [
	    {
		  xtype: 'label',
		  html: 'Variables de Texto'
	    },
	    {
		  xtype: 'toolbar',		
		  items: 
		  [                    		     
		      {			
			  text: 'Cliente',
			  itemId: 'cliente',
			  width:150,
			  scope: this,
			  handler: function(){ insertarVariableTexto('%cliente%');}
		      }		   
		  ]
		  
	    },
	    {
		  xtype: 'toolbar',		
		  items: 
		  [                    		      
		      {			 
			  text: 'Saldo',
			  itemId: 'saldo',
			  scope: this,
			  width:150,
			  handler: function(){ insertarVariableTexto('%saldo%'); }
		      }		   
		  ]
		  
	    },
	    {
		  xtype: 'toolbar',		
		  items: 
		  [                    		      
		      {			 
			  text: 'Servicio',
			  itemId: 'servicio',
			  scope: this,
			  width:150,
			  handler: function(){insertarVariableTexto('%servicio%'); }
		      }		   
		  ]
		  
	    }	
	], 
        items: 
        [
	      {
		    xtype: 'htmleditor',
		    //xtype: 'textarea',
		    id: 'plantillaPanel',
		    enableColors: true,
		    enableAlignments: true,                     
	      }
        ]
    });
    /****************************************************************/
    new Ext.panel.Panel({
        title: 'Plantilla',
        renderTo: "plantilla_sms",
        width: 1000,
        height: 200,
        frame: true,
        layout: 'fit',
	rbar: [	
	    {
		  xtype: 'label',
		  html: 'Variables de Texto'
	    },
	    {
		  xtype: 'toolbar',		
		  items: 
		  [                    		     
		      {			
			  text: 'Cliente',
			  itemId: 'cliente',
			  width:150,
			  scope: this,
			  handler: function(){ insertarVariableTexto('%cliente%');}
		      }		   
		  ]
		  
	    },
	    {
		  xtype: 'toolbar',		
		  items: 
		  [                    		      
		      {			 
			  text: 'Saldo',
			  itemId: 'saldo',
			  scope: this,
			  width:150,
			  handler: function(){ insertarVariableTexto('%saldo%'); }
		      }		   
		  ]
		  
	    },
	    {
		  xtype: 'toolbar',		
		  items: 
		  [                    		      
		      {			 
			  text: 'Servicio',
			  itemId: 'servicio',
			  scope: this,
			  width:150,
			  handler: function(){insertarVariableTexto('%servicio%'); }
		      }		   
		  ]
		  
	    }	
	],
        items: {
            xtype: 'textarea',
            id: 'plantillaPanelsms',	   
	    maxLength:160,	            
        }
    });
    /****************************************************************/    
    var msg = function(title, msg) {
        Ext.Msg.show({
            title: title,
            msg: msg,
            minWidth: 200,
            modal: true,
            icon: Ext.Msg.INFO,
            buttons: Ext.Msg.OK
        });
    };


    var tpl = new Ext.XTemplate(
        'File processed on the server.<br />',
        'Name: {fileName}<br />',
        'Size: {fileSize:fileSize}'
    );
    Ext.create('Ext.form.Panel', {
        renderTo: 'fi-form',
        width: 500,
        frame: true,
        title: 'Formulario Subir Imagen',
        bodyPadding: '10 10 0',

        defaults: {
            anchor: '100%',
            allowBlank: false,
            msgTarget: 'side',
            labelWidth: 50
        },

        items: [{
            xtype: 'filefield',
            id: 'form-file',
            name: 'archivo',
            emptyText: 'Seleccione una imagen',
            buttonText: 'Browse',
            buttonConfig: {
                iconCls: 'upload-icon'
            }
        }],
		
        buttons: [{
            text: 'Subir',
            handler: function(){
                var form = this.up('form').getForm();
                if(form.isValid()){
                    form.submit({
                        url: url_fileUpload,
                        waitMsg: 'Subiendo la imagen...',
                        success: function(fp, o) {                           
                            msg('Success', 'Imagen "' + o.result.fileName + '" procesada exitosamente');
                        },
                        failure: function() {
                            Ext.Msg.alert("Error", Ext.JSON.decode(this.response.responseText).message);
                        }
                    });
                }
            }
        },{
            text: 'Resetear',
            handler: function() {
                this.up('form').getForm().reset();
            }
        }]
    });
		
    /*
     * This button just opens the window. We render it into the 'buttons' div and set its
     * handler to simply show the window
     */
    insertButton = Ext.create('Ext.button.Button', {
        text: "Seleccione Imagen",
        renderTo: 'buttons2',
        handler : function() {
			showImages();
        }
    });
	
    
    $('.smsWidgets').hide();//Esconder Panel con textarea para redactar SMS
});

function showImages()
{
	Ext.Loader.setConfig({enabled: true});
	Ext.Loader.setPath('Ext.chooser', '../../../bundles/soporte/js/Plantilla');
	Ext.Loader.setPath('Ext.ux', '../../../public/js/ext-4.1.1/src/ux');

	Ext.require([
		'Ext.button.Button',
		'Ext.data.proxy.Ajax',
		'Ext.chooser.z_InfoPanel',
		'Ext.chooser.z_IconBrowser',
		'Ext.chooser.z_Window',
		'Ext.ux.DataView.Animated',
		'Ext.toolbar.Spacer'
	]);

    /*
     * Here is where we create the window from which the user can select images to insert into the 'images' div.
     * This window is a simple subclass of Ext.window.Window, and you can see its source code in Window.js.
     * All we do here is attach a listener for when the 'selected' event is fired - when this happens it means
     * the user has double clicked an image in the window so we call our insertSelectedImage function to add it
     * to the DOM (see below).
     */
    win = Ext.create('Ext.chooser.z_Window', {
        animateTarget: insertButton.getEl(),
        listeners: {
            selected: insertSelectedImage
        }
    });
    
	win.show();
}


/*
 * This function is called whenever the user double-clicks an image inside the window. It creates
 * a new <img> tag inside the 'images' div and immediately hides it. We then call the show() function
 * with a duration of 500ms to fade the image in. At the end we call .frame() to give a visual cue
 * to the user that the image has been inserted
 */
function insertSelectedImage(image) {
	//(!Ext.isIE6? '<img src="/public/uploads/imagesPlantilla/{thumb}" width="50" height="50" />' : 
	
	var htmlImagen = '<center><img src="'+ image.get('url') + '" width="730" height="850" /></center>';
        var editorPlantilla = Ext.getCmp("plantillaPanel");
	var before = editorPlantilla.getValue();
	editorPlantilla.insertAtCursor(htmlImagen);
	var after = editorPlantilla.getValue();
	if (before==after) {       
		editorPlantilla.setValue(before+htmlImagen);
	}
}
	

function validarFormulario(){   
  
    tipoPId = document.getElementById("telconet_schemabundle_plantillaNotificacionExternatype_tipo").value;        
    
    /*
     * 		VALIDACION PARA NOMBRE DE LA PLANTILLA
     */
    if(document.getElementById("telconet_schemabundle_plantillaNotificacionExternatype_nombrePlantilla").value=="")
    {
        Ext.Msg.alert("Alerta","Debe ingresar un nombre de la plantilla.");
        return false;   
    }  
    
    /*
     * 		VALIDACION PARA PLANTILLAS DE CORREO : 1->Correo (Clase Documento)
     */
    
    if(tipoPId == 1){
     
	  if(Ext.getCmp("plantillaPanel").getValue()=="" )
	  {
	      Ext.Msg.alert("Alerta","Debe ingresar la plantilla Correo.");
	      return false;
	      
	  }else 
	      document.getElementById("telconet_schemabundle_plantillaNotificacionExternatype_plantilla_mail").value = Ext.getCmp("plantillaPanel").getValue();            
      
    }else{
      
	    if(Ext.getCmp("plantillaPanelsms").getValue()=="")
	    {
		Ext.Msg.alert("Alerta","Debe ingresar la plantilla SMS.");
		return false;   
		
	    }else if(Ext.getCmp("plantillaPanelsms").getValue().length>=160){
	      
		Ext.Msg.alert("Alerta","Solo se aceptan 160 caracteres para escribir SMS.");
		return false; 
		
	    }else
		 document.getElementById("telconet_schemabundle_plantillaNotificacionExternatype_plantilla_sms").value = Ext.getCmp("plantillaPanelsms").getValue();
	        	    	    	    
    }
    
    return true;
    
    
    
    
}

function buscar(){
    
    eventStore.removeAll();
    eventStore.getProxy().extraParams.startDate = globalStartDate;
    eventStore.getProxy().extraParams.endDate = globalEndDate;
    eventStore.getProxy().extraParams.estado= Ext.getCmp('cmb_estado').getValue();
    eventStore.load();
}

function limpiar(){
    Ext.getCmp('estado').setRawValue("");
    
    eventStore.removeAll();
    eventStore.getProxy().extraParams.startDate = globalStartDate;
    eventStore.getProxy().extraParams.endDate = globalEndDate;
    eventStore.getProxy().extraParams.estado= Ext.getCmp('cmb_estado').getValue();
    eventStore.load();
}

/***************************************************************/
function verPlantillaTipo(){
   
   if(document.getElementById("telconet_schemabundle_plantillaNotificacionExternatype_tipo").value==1){//1 -> Plantilla Correo : AdmiClaseDocumento
      $('.emailWidgets').show();
      $('.smsWidgets').hide();
   }else{
      $('.smsWidgets').show();
      $('.emailWidgets').hide();
   }
}

function insertarVariableTexto(token)
{      
    cuerpo = Ext.getCmp("plantillaPanel").getValue();  
    cuerpoSMS = Ext.getCmp("plantillaPanelsms").getValue();  
            
    if(cuerpo!='')
    {
	  num = cuerpo.lastIndexOf('<br>');
	  cuerpo = setCharAt(cuerpo,num,'','<br>');
	  cuerpo =cuerpo + token;
	  Ext.getCmp("plantillaPanel").setValue(cuerpo);
    }
    
    if(cuerpoSMS!='')
    {
	   cuerpoSMS =cuerpoSMS + token;
	   Ext.getCmp("plantillaPanelsms").setValue(cuerpoSMS);   
    }
        
}

function setCharAt(str,index,chr,strToDelete) {
    if(index > str.length-1) return str;
    return str.substr(0,index) + chr + str.substr(index+strToDelete.length);
}