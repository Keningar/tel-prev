Ext.require([
    '*'
]);

var personaid = '';

Ext.onReady(function(){
        
    //Verificacion de error
    if(pageError !== '')
    {
        Ext.Msg.alert('Error ','Error: ' + pageError);
        Ext.MessageBox.show({
                  title: 'Error',
                  msg: text,                  
                  buttons: Ext.MessageBox.OK,
                  icon: Ext.MessageBox.INFO                    
                }); 
    }
    
    
    var tabs = new Ext.TabPanel({
        height: 700,        
        renderTo: 'nodos-tabs',
        activeTab: 0,
        plain:true,
        autoRender:true,
        autoShow:true,
        items:[
             {contentEl:'tab1', title:'Datos Generales'},
             {contentEl:'tab2', title:'Datos Local',listeners:{
                  activate: function(tab){
                          gridInformacionEspacio.view.refresh();                                
                  }
                                
              }},
             {contentEl:'tab3', title:'Datos Contactos',listeners:{
                  activate: function(tab){
                          gridContactoNodo.view.refresh();
                                
                  }
                                
              }},
             {contentEl:'tab4', title:'Galería de Fotos'}
        ]            
    }); 
    
    var storeTipoUbicacion = new Ext.data.Store({              
            proxy: {
                type: 'ajax',
                url : url_admitipoespacio,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    estado: 'Activo'				
                }
            },
            fields:
                      [
                        {name:'idTipoEspacio', mapping:'idTipoEspacio'},
                        {name:'nombreTipoEspacio', mapping:'nombreTipoEspacio'}
                      ]
        }); 
    
    Ext.define('UbicacionModelo', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'tipoEspacioId', mapping:'tipoEspacioId'},
            {name:'tipoEspacioNombre', mapping:'tipoEspacioNombre'},
            {name:'largo', mapping:'largo'},
            {name:'ancho', mapping:'ancho'},
            {name:'alto', mapping:'alto'},      
            {name:'valor', mapping:'valor'}
        ]
    });        
           
    storeInformacionEspacio = Ext.create('Ext.data.Store', 
    {        
        autoDestroy: true,
        autoLoad: false,
        model: 'UbicacionModelo',        
        proxy: {
            type: 'ajax',            
            url: '',            
            reader: {
                type: 'json',
                totalProperty: 'total',                
                root: 'encontrados'
            },
            extraParams: {
				estado: 'Activo'				
			}
        }
    });
    
    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 1,
        listeners: {
            edit: function(){                
                gridInformacionEspacio.getView().refresh();
            }
        }
    });
    
    var selEspacioModelo = Ext.create('Ext.selection.CheckboxModel', {
        listeners: {
            selectionchange: function(sm, selections) {
                gridInformacionEspacio.down('#removeButton').setDisabled(selections.length == 0);
            }
        }
    });
    
    gridInformacionEspacio = Ext.create('Ext.grid.Panel', {
        id: 'gridInformacionEspacio',
        store: storeInformacionEspacio,
        columns: [
            {
                id: 'tipoEspacioId',
                header: 'tipoEspacioId',
                dataIndex: 'tipoEspacioId',
                hidden: true,
                hideable: false
            },
            {
                id: 'tipoEspacioNombre',
                header: 'Tipo Espacio',
                dataIndex: 'tipoEspacioNombre',
                width: 200,
                sortable: true,
                renderer: function(value, metadata, record, rowIndex, colIndex, store) 
                {                                                                               
                    record.data.tipoEspacioId = record.data.tipoEspacioNombre;
                    
                    for (var i = 0; i < storeTipoUbicacion.data.items.length; i++)
                    {
                        if (storeTipoUbicacion.data.items[i].data.idTipoEspacio === record.data.tipoEspacioId)
                        {
                            record.data.tipoEspacioNombre = storeTipoUbicacion.data.items[i].data.nombreTipoEspacio;
                            break;
                        }
                    }

                    return record.data.tipoEspacioNombre;
                },
                editor: {
                    id: 'searchTipoEspacio_cmp',
                    xtype: 'combobox',
                    typeAhead: true,
                    editable:false,
                    displayField: 'nombreTipoEspacio',
                    valueField: 'idTipoEspacio',
                    triggerAction: 'all',
                    selectOnFocus: true,
                    loadingText: 'Buscando ...',
                    hideTrigger: false,
                    store: storeTipoUbicacion,
                    lazyRender: true,
                    listClass: 'x-combo-list-small',                    
                    listeners: {
                        select: function(combo) {                                                                                    
                            var r = Ext.create('UbicacionModelo', {
                                tipoEspacioId: combo.rawValue,
                                tipoEspacioNombre: combo.rawValue,
                                largo: '',
                                ancho: '',
                                alto: '',
                                valor:''
                            });
                            
                            if(existeRecordRelacion(r, gridInformacionEspacio))                        
                            {     
                                //Determinar no repetidos
                                Ext.Msg.alert("Advertencia","Ya ingreso informacio de "+r.get('tipoEspacioNombre'));
                                eliminarSeleccion(gridInformacionEspacio);
                            }
                        }
                    }
                }
            },
        {
                id: 'largo',
                header: 'Largo',
                dataIndex: 'largo',
                width: 150,
                editor: {
                    allowBlank: false,
                    enableKeyEvents: true,
                    listeners:
                        {
                            keypress: function(me, e)
                            {
                                validarSoloNumeros(me, e);
                            }
                        }
                }
            }, {
                id: 'ancho',
                header: 'Ancho',
                dataIndex: 'ancho',
                width: 150,
                editor: {
                    allowBlank: false,
                    enableKeyEvents: true,
                    listeners:
                        {
                            keypress: function(me, e)
                            {
                                validarSoloNumeros(me, e);
                            }
                        }

                }
            },
            {
                id: 'alto',
                header: 'Alto',
                dataIndex: 'alto',
                width: 150,
                editor: {
                    allowBlank: false,
                    enableKeyEvents: true,
                    listeners:
                        {
                            keypress: function(me, e)
                            {
                                validarSoloNumeros(me, e);
                            }
                        }
                }
            }, {
                id: 'valor',
                header: 'Valor',
                dataIndex: 'valor',
                width: 150,
                editor: {
                    allowBlank: false,
                    enableKeyEvents: true,
                    listeners:
                        {
                            keypress: function(me, e)
                            {
                                validarSoloNumeros(me, e);
                            }
                        }
                }
            }
        ],
        selModel: selEspacioModelo,       
        viewConfig: {
            stripeRows: true
        },
        tbar: [{
                xtype: 'toolbar',
                items: [{
                        itemId: 'removeButton',
                        text: 'Eliminar',
                        tooltip: 'Elimina el item seleccionado',
                        iconCls: 'remove',
                        disabled: true,
                        handler: function() {
                            eliminarSeleccion(gridInformacionEspacio);
                        }
                    }, '-', {
                        text: 'Agregar',
                        tooltip: 'Agrega un item a la lista',
                        iconCls: 'add',
                        handler: function() {

                            var r = Ext.create('UbicacionModelo', {
                                tipoEspacioId: '',
                                nombreEspacioId: '',
                                largo: '0',
                                ancho: '0',
                                alto: '0',
                                valor: '0'
                            });

                            storeInformacionEspacio.insert(0, r);
                            cellEditing.startEditByPosition({row: 0, column: 1});
                        }
                    }]
            }],
        width: 850,
        height: 200,
        title: 'Agregue Información de Espacio',
        renderTo: Ext.get('informacionEspacio'),
        plugins: [cellEditing]
    });       
    
    
    /*****************************************************************************
     * 
     *                             FORMAS DE CONTACTO
     *  
     *****************************************************************************/
    
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
            {name: 'id', type: 'int'},
            {name: 'descripcion', type: 'string'}
        ]
    });
        
    store = Ext.create('Ext.data.Store', {        
        autoDestroy: true,
        model: 'PersonaFormasContactoModel',
        proxy: {
            type: 'ajax',            
            url: url_formas_contacto_persona,
            reader: {
                type: 'json',
                root: 'personaFormasContacto',                
                totalProperty: 'total'
            },
            extraParams: {personaid: ''},
            simpleSortMode: true
        },
        listeners: {
            beforeload: function(store) {
                store.getProxy().extraParams.personaid = personaid;
            }
        }
    });
    
    var storeFormasContacto = Ext.create('Ext.data.Store', {        
        autoDestroy: true,
        model: 'FormasContactoModel',
        proxy: {
            type: 'ajax',            
            url: url_formas_contacto,
            reader: {
                type: 'json',
                root: 'formasContacto'
            }
        }
    });

    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 1
    });
    
    gridContactoNodo = Ext.create('Ext.grid.Panel', {
        id:'gridContactoNodo',
        store: store,
        columns: [{
                text: 'Forma Contacto',
                header: 'Forma Contacto',
                dataIndex: 'formaContacto',
                width: 150,
                editor: new Ext.form.field.ComboBox({
                    typeAhead: true,
                    triggerAction: 'all',
                    selectOnTab: true,
                    id: 'id',
                    name: 'formaContacto',
                    valueField: 'descripcion',
                    displayField: 'descripcion',
                    store: storeFormasContacto,
                    lazyRender: true,
                    listClass: 'x-combo-list-small'
                })
            }, {
                text: 'Valor',                
                dataIndex: 'valor',
                width: 400,
                align: 'right',
                editor: {
                    width: '80%',
                    xtype: 'textfield',
                    allowBlank: false
                }
            }, {
                xtype: 'actioncolumn',
                width: 45,
                sortable: false,
                items: [{
                        iconCls: "button-grid-delete",
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
        height: 250,
        title: '',        
        tbar: [{
                text: 'Agregar',
                handler: function() {                    
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
    
            
    
    $('#preclientetype_identificacionCliente').keypress(
        function(){
            if($('#preclientetype_tipoIdentificacion').val()==='Seleccione...' || $('#preclientetype_tipoIdentificacion').val()===''){
                mostrarDiv('dividentificacion');
                $("#dividentificacion").html("Antes de ingresar identificacion seleccione tipo de identificacion");  
                $('#preclientetype_identificacionCliente').attr('readonly','readonly');       
            }
            else{
                ocultarDiv('dividentificacion');
            }
        }
    );
    
    $('#preclientetype_identificacionCliente').attr('readonly','readonly');   
    
    $('#preclientetype_tipoIdentificacion').change(function()
    {
        $('#preclientetype_identificacionCliente').removeAttr('readonly');  
        ocultarDiv('dividentificacion');
        $('#preclientetype_identificacionCliente').removeAttr('maxlength');  
        $('#preclientetype_identificacionCliente').val("");
        if($('#preclientetype_tipoIdentificacion').val()==='RUC'){
            $('#preclientetype_identificacionCliente').attr('maxlength','13');                
        }
        else{
            if($('#preclientetype_tipoIdentificacion').val()==='CED'){
                $('#preclientetype_identificacionCliente').attr('maxlength','10');                
            }
            else{
                if($('#preclientetype_tipoIdentificacion').val()==='PAS'){
                    $('#preclientetype_identificacionCliente').attr('maxlength','20');                
                } 
                else{
                    if($('#preclientetype_tipoIdentificacion').val()==='Seleccione...' || $('#preclientetype_tipoIdentificacion').val()===''){
                        mostrarDiv('dividentificacion');   
                        $("#dividentificacion").html("Antes de ingresar identificacion seleccione tipo de identificacion");                          
                        $('#preclientetype_identificacionCliente').attr('maxlength','10');  
              
			          $('#preclientetype_identificacionCliente').attr('readonly','readonly'); 
                    }                     
                }
            }
        }

    }); 
    
    $(".altMax").hide();
    $("#div_razon_social").hide();
});

//Funcion que muestra el campo de llenado de razon social segun el tipo tributario del contacto
function esTipoNatural()
{
    tipo = $("#preclientetype_tipoTributario").val();
    if(tipo === 'JUR')
    {
        $("#div_razon_social").show();        
        $('#preclientetype_nombres').hide();
        $('#preclientetype_apellidos').hide();
        $('label[for=preclientetype_nombres]').hide();
        $('label[for=preclientetype_apellidos]').hide();
        $('#preclientetype_genero').hide();
        $('#preclientetype_tituloId').hide();
        $('label[for=preclientetype_genero]').hide();
        $('label[for=preclientetype_tituloId]').hide();
    }
    else
    {
        $("#div_razon_social").hide(); 
        $('#preclientetype_nombres').show();
        $('#preclientetype_apellidos').show();
        $('label[for=preclientetype_nombres]').show();
        $('label[for=preclientetype_apellidos]').show();
        $('#preclientetype_genero').show();
        $('#preclientetype_tituloId').show();
        $('label[for=preclientetype_genero]').show();
        $('label[for=preclientetype_tituloId]').show();
    }
}

function validador(e, tipo) {

    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();

    console.log(key);
    if (tipo == 'numeros') {
        letras = "0123456789";
        especiales = [45, 46];
    } else if (tipo == 'letras') {
        letras = "abcdefghijklmnopqrstuvwxyz";
        especiales = [8, 36, 35, 45, 47, 40, 41, 46, 32, 37, 39];
    }
    else if (tipo == 'ip') {
        letras = "0123456789";
        especiales = [8, 46];
    }
    else {
        letras = "abcdefghijklmnopqrstuvwxyz0123456789";
        especiales = [8, 36, 35, 45, 47, 40, 41, 46];
    }

    tecla_especial = false
    for (var i in especiales) {
        if (key == especiales[i]) {
            tecla_especial = true;
            break;
        }
    }


    if (letras.indexOf(tecla) == -1 && !tecla_especial)
        return false;
}