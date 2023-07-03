Ext.require([
    '*'
]);

Ext.onReady(function(){
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
        height: 200,
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
        height: 250,
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
            for(var i=0; i < grid.getStore().getCount(); i++){ 
                variable=grid.getStore().getAt(i).data;
                for(var key in variable) {
                    var valor = variable[key];
					if (key=='valor' && valor==''){
						valoresVacios=true;
					}else{
						array_data.push(valor);
					}
                } 
            }
            $(campo).val(array_data); 
            if (($(campo).val()=='0,,') || ($(campo).val()=='')) {
                alert('No hay formas de contacto aun ingresadas.');
                $(campo).val('');
            }else{
				if(valoresVacios==true){
					alert('Hay formas de contacto que tienen valor vacio, por favor corregir.');
					$(campo).val('');
				}else{
                    if(document.forms[0].contactotype_cliente.value=='')
                        {
                            alert('Debe Seleccionar cliente');
                        }				
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