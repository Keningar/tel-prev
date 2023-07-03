           function esEmpresa(){
                if ($('#convertirtype_tipoEmpresa').val()=='Publica' || $('#convertirtype_tipoEmpresa').val()=='Privada'){
                    ocultarDiv('div_nombres');
                    mostrarDiv('div_razon_social');
                    $('#convertirtype_razonSocial').attr('required','required');
                    $('#convertirtype_representanteLegal').attr('required','required');
                    $('label[for=convertirtype_representanteLegal]').html('* Representante Legal:');
                    $('label[for=convertirtype_representanteLegal]').addClass('campo-obligatorio');
                    $('#convertirtype_nombres').removeAttr('required');
                    $('#convertirtype_apellidos').removeAttr('required');
                    $('#convertirtype_genero').removeAttr('required');
                    $('#convertirtype_estadoCivil').removeAttr('required');
                    $('#convertirtype_fechaNacimiento_year').removeAttr('required');
                    $('#convertirtype_fechaNacimiento_month').removeAttr('required');
                    $('#convertirtype_fechaNacimiento_day').removeAttr('required');
                    $('#convertirtype_tituloId').removeAttr('required');
                    $('#convertirtype_nombres').val('');
                    $('#convertirtype_apellidos').val('');
                    $('#convertirtype_genero').val('');
                    $('#convertirtype_estadoCivil').val('');
                    $('#convertirtype_fechaNacimiento_year').val('');
                    $('#convertirtype_fechaNacimiento_month').val('');
                    $('#convertirtype_fechaNacimiento_day').val('');
                    $('#convertirtype_tituloId').val('');
                }
                else
                {
                    mostrarDiv('div_nombres');
                    ocultarDiv('div_razon_social');
                    $('#convertirtype_razonSocial').removeAttr('required');
                    $('label[for=convertirtype_representanteLegal]').removeClass('campo-obligatorio');
                    $('label[for=convertirtype_representanteLegal]').html('Representante Legal:'); 
                    $('#convertirtype_representanteLegal').removeAttr('required');
                    $('#convertirtype_nombres').attr('required','required');                    
                    $('#convertirtype_apellidos').attr('required','required');                    
                    $('#convertirtype_genero').attr('required','required');                    
                    $('#convertirtype_estadoCivil').attr('required','required');                    
                    $('#convertirtype_fechaNacimiento_year').attr('required','required');
                    $('#convertirtype_fechaNacimiento_month').attr('required','required');
                    $('#convertirtype_fechaNacimiento_day').attr('required','required');                    
                    $('#convertirtype_tituloId').attr('required','required');
                    $('#convertirtype_razonSocial').val('');					
				}
				
				/*if($('#convertirextratype_direccionTributaria').val()!='')
				{$('#convertirextratype_direccionTributaria').attr('readonly','readonly');}*/

				$('#convertirtype_tipoEmpresa').attr('disabled','disabled');

				//if($('#convertirtype_tipoIdentificacion').val()!='')					
				//{
                                    $('#convertirtype_tipoIdentificacion').attr('disabled','disabled');
                               // }					
				//if($('#convertirtype_tipoTributario').val()!='')					
				//{$('#convertirtype_tipoTributario').attr('disabled','disabled');}
				//if($('#convertirtype_nacionalidad').val()!='')
				//{$('#convertirtype_nacionalidad').attr('disabled','disabled');}
				if($('#convertirtype_identificacionCliente').val()!='')
				{
					flagIdentificacionCorrecta = 1;
					$('#convertirtype_identificacionCliente').attr('readonly','readonly');
				}
				//if($('#convertirtype_genero').val()!='')
				//{$('#convertirtype_genero').attr('disabled','disabled');}
				//if($('#convertirtype_tituloId').val()!='')
				//{$('#convertirtype_tituloId').attr('disabled','disabled');}
				//if($('#convertirtype_estadoCivil').val()!='')
				//{$('#convertirtype_estadoCivil').attr('disabled','disabled');}	
				//if($('#convertirtype_fechaNacimiento_month').val()!='')
				//{$('#convertirtype_fechaNacimiento_month').attr('disabled','disabled');}					
				//if($('#convertirtype_fechaNacimiento_day').val()!='')
				//{$('#convertirtype_fechaNacimiento_day').attr('disabled','disabled');}					
				//if($('#convertirtype_fechaNacimiento_year').val()!='')
				//{$('#convertirtype_fechaNacimiento_year').attr('disabled','disabled');}						
                
            } 

                function esRuc(){
                    if ($('#convertirtype_tipoIdentificacion').val()=='RUC'){
                        $('#convertirtype_identificacionCliente').removeAttr('maxlength');
                        $('#convertirtype_identificacionCliente').attr('maxlength','13');
                        //$('#preclientetype_identificacionCliente').val('');
                    }else
                    {
                        $('#convertirtype_identificacionCliente').removeAttr('maxlength');
                        $('#convertirtype_identificacionCliente').attr('maxlength','10');
                        //$('#preclientetype_identificacionCliente').val('');
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
 
 function validaIdentificacion(isValidarIdentificacionTipo){
    currenIdentificacion=$(input).val();
	$.ajax({
			type: "POST",
			data: "identificacion=" + currenIdentificacion,
			url: url_valida_identificacion,
			beforeSend: function(){
				$('#img-valida-identificacion').attr("src",url_img_loader);
			},
			success: function(msg){
				if (msg != ''){
					if(msg=="no"){
						flagIdentificacionCorrecta = 1;
						$('#img-valida-identificacion').attr("title","Identificacion disponible");
						$('#img-valida-identificacion').attr("src",url_img_check);
					}
					if(msg=="si"){
						flagIdentificacionCorrecta = 0;
						$('#img-valida-identificacion').attr("title","identificacion ya existe");
						$('#img-valida-identificacion').attr("src",url_img_delete);
						$(input).focus();
						alert("Identificacion ya existente. Favor Corregir");
					}
				   
				}
                else
                {
                    alert("Error: No se pudo validar la identificacion ingresada.");
                }
                if (isValidarIdentificacionTipo && typeof validarIdentificacionTipo == typeof Function)
                {
                    validarIdentificacionTipo();
                }
            }
    });
}
 
 
 function validaIdentificacionCorrecta(){
	if(flagIdentificacionCorrecta==1){
		return true;
	}else{
		alert("Identificacion ya existente. Favor Corregir para poder ingresar el Nuevo Cliente");
		$(input).focus();
		return false;
	}
}
 
$(document).ready(function(){
    //$('#preconvertirtype_tipoEmpresa').attr('disabled',true);
    //$('#preconvertirtype_tipoIdentificacion').attr('disabled',true);
    //$('#preconvertirtype_identificacionCliente').attr('readonly','readonly');
    //$('#convertirtype_razonSocial').attr('readonly','readonly');
    //$('#convertirtype_nombres').attr('readonly','readonly');
    //$('#convertirtype_apellidos').attr('readonly','readonly');
    esEmpresa();
	//validaIdentificacion();
});

Ext.require([
    '*'
]);

/*if (window.location.search.indexOf('scopecss') !== -1) {
    // We are using ext-all-scoped.css, so all rendered ExtJS Components must have a
    // reset wrapper round them to provide localized CSS resetting.
    Ext.scopeResetCSS = true;
}*/

Ext.onReady(function(){
    //Ext.QuickTips.init();



    //tabs.setActiveTab(1);

    /*function formatDate(value){
        return value ? Ext.Date.dateFormat(value, 'M d, Y') : '';
    }*/

    Ext.define('PersonaFormasContactoModel', {
        extend: 'Ext.data.Model',
        fields: [
            // the 'name' below matches the tag name to read, except 'availDate'
            // which is mapped to the tag 'availability'
            {name: 'idPersonaFormaContacto', type: 'int'},
            {name: 'formaContacto'},
            {name: 'valor', type: 'string'}
        ]
    });

    Ext.define('FormasContactoModel', {
        extend: 'Ext.data.Model',
        fields: [
            // the 'name' below matches the tag name to read, except 'availDate'
            // which is mapped to the tag 'availability'
            {name: 'id', type: 'int'},
            {name: 'descripcion', type: 'string'}
        ]
    });
    
    // create the Data Store
    var store = Ext.create('Ext.data.Store', {
        // destroy the store if the grid is destroyed
        autoDestroy: true,
        model: 'PersonaFormasContactoModel',
        proxy: {
            type: 'ajax',
            // load remote data using HTTP
            url: url_formas_contacto_persona,
            reader: {
                type: 'json',
                root: 'personaFormasContacto',
                // records will have a 'plant' tag
                totalProperty: 'total'
            },
            extraParams:{personaid:''},
            simpleSortMode: true               
        },
        listeners: {
                        beforeload: function(store){
				store.getProxy().extraParams.personaid= personaid; 
                        }
                }
    });

    // create the Data Store
    var storeFormasContacto = Ext.create('Ext.data.Store', {
        // destroy the store if the grid is destroyed
        autoDestroy: true,
        model: 'FormasContactoModel',
        proxy: {
            type: 'ajax',
            // load remote data using HTTP
            url: url_formas_contacto,
            reader: {
                type: 'json',
                root: 'formasContacto'
            }
        }
    });
    
    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 2
    });

    // create the grid and specify what field you want
    // to use for the editor at each header.
    grid = Ext.create('Ext.grid.Panel', {
        store: store,
        columns: [ {
            text:'Forma Contacto',    
            header: 'Forma Contacto',
            dataIndex: 'formaContacto',
            width: 150,
            editor: new Ext.form.field.ComboBox({
                
                typeAhead: true,
                triggerAction: 'all',
                selectOnTab: true,
                id:'id',
                name: 'formaContacto',
		valueField:'descripcion',
                displayField:'descripcion',                
                store: storeFormasContacto,
                lazyRender: true,
                listClass: 'x-combo-list-small'
            })
        }, {
            text: 'Valor',
            //header: 'Valor',
            dataIndex: 'valor',
            width: 400,
            align: 'right',
            editor: {
                width:'80%',
                xtype: 'textfield',
                allowBlank: false
            }
        },{
            xtype: 'actioncolumn',
            width:45,
            sortable: false,
            items: [{
                iconCls:"button-grid-delete",
                tooltip: 'Borrar Forma Contacto',
                handler: function(grid, rowIndex, colIndex) {
                    store.removeAt(rowIndex); 
                }
            }]
        }],
        selModel: {
            selType: 'cellmodel'
        },
        renderTo: Ext.get('lista_formas_contacto_grid'),
        width: 600,
        height: 300,
        title: '',
        //frame: true,
        tbar: [{
            text: 'Agregar',
            handler : function(){
                // Create a model instance
                var r = Ext.create('PersonaFormasContactoModel', {
                    idPersonaFormaContacto: '',
                    formaContacto: '',
                    valor: ''
                });
                store.insert(0, r);
                cellEditing.startEditByPosition({row: 0, column: 0});
                
            }
        }],
        plugins: [cellEditing]
    });

    // manually trigger the data store load
    store.load();
    var tabs = new Ext.TabPanel({
        height: 450,
        renderTo: 'my-tabs',
        activeTab: 0,
        plain:true,
        autoRender:true,
        autoShow:true,
        items:[
             {contentEl:'tab1', title:'Datos Principales'},
             {contentEl:'tab2', title:'Formas de contacto',listeners:{
                  activate: function(tab){
                          grid.view.refresh()
                                
                  }
                                
              }}
        ]            
    }); 
    
    
  

});

    function grabar(campo){
            var array_data = new Array();
            var variable='';
			var valoresVacios=false;
			var datos='';
            for(var i=0; i < grid.getStore().getCount(); i++){ 
                variable=grid.getStore().getAt(i).data;
                for(var key in variable) {
					console.log('variable:'+variable[key]+' key:'+key);
                    var valor = variable[key];
					if (key=='valor' && valor==''){
						valoresVacios=true;
					}else{
						array_data.push(valor);
					}
                } 
                //console.log(array_data);
            }
			datos=array_data;
            
//$(campo).val('');
            if ((datos=='0,,') || (datos=='')) {
                alert('No hay formas de contacto aun ingresadas.');
 
            }else{
				if(valoresVacios==true){
					alert('Hay formas de contacto que tienen valor vacio, por favor corregir.');

				}
				else{
					$(campo).val(array_data); 
					$('#convertirtype_tipoEmpresa').removeAttr('disabled');				
					$('#convertirtype_tipoTributario').removeAttr('disabled');				
					$('#convertirtype_nacionalidad').removeAttr('disabled');
					$('#convertirtype_tipoIdentificacion').removeAttr('disabled');
					$('#convertirtype_genero').removeAttr('disabled');
					$('#convertirtype_estadoCivil').removeAttr('disabled');
					$('#convertirtype_tituloId').removeAttr('disabled');
					$('#convertirtype_fechaNacimiento_month').removeAttr('disabled');
					$('#convertirtype_fechaNacimiento_day').removeAttr('disabled');
					$('#convertirtype_fechaNacimiento_year').removeAttr('disabled');				
				}
			}	
    }


function validaFormasContacto(){
			var array_telefonos = new Array();
			var array_correos = new Array(); var i=0;
            var variable=''; var formaContacto='';var hayTelefono=false;var hayCorreo=false; 
			var esTelefono=false; var esCorreo=false; var telefonosOk=false;var correosOk=false;
            for(var i=0; i < grid.getStore().getCount(); i++){ 
                variable=grid.getStore().getAt(i).data;
				esTelefono=false;esCorreo=false;
                for(var key in variable) {
					var valor = variable[key];
					if (key=='formaContacto'){
						formaContacto = variable[key];
						formaContacto=formaContacto.toUpperCase();
						if(formaContacto.match(/^TELEFONO.*$/)){
							hayTelefono=true;
							esTelefono=true;
						}
						if(formaContacto.match(/^CORREO.*$/)){
							hayCorreo=true;
							esCorreo=true;
						}						
					}
					if(esTelefono){array_telefonos.push(valor);}
					if(esCorreo){array_correos.push(valor);}
                }
				//console.log(array_telefonos);
				//console.log(array_correos);
            }			
			if(hayCorreo){
				for(i=0;i<array_correos.length;i++){
					if (i%2!=0){correosOk=validaCorreo(array_correos[i]);}
				}
				if(correosOk){
					if(hayTelefono){
						for(i=0;i<array_telefonos.length;i++){
							 if (i%2!=0){telefonosOk=validaTelefono(array_telefonos[i]);}
						}
						if(telefonosOk)
						{
							return true;
						}else{
							alert('Hay numeros de telefono que tienen errores, por favor corregir.');
						}
					}
					else{
						return true;
					}
				}
				else{
					alert('Hay correos que tienen errores, por favor corregir.');
					return false;
				}
			}
			else
			{
				alert('Debe Ingresar al menos 1 Correo');
				return false;
			}			
}
function validaTelefono(telefono){
    var RegExPattern = /^[0-9]{8,10}$/;
    if ((telefono.match(RegExPattern)) && (telefono.value!='')) {
		return true;
    } else {
		return false;
    } 
}

function validaCorreo(correo){
    var RegExPattern = /[\w-\.]{3,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/;
    if ((correo.match(RegExPattern)) && (correo.value!='')) {
        return true;
    } else {
		return false;
    } 
}

function validacionesForm(){
	if(validaFormasContacto() &&  flagIdentificacionCorrecta == 1){
		return true;
	}else
	{
		return false;
	}
}
	
	