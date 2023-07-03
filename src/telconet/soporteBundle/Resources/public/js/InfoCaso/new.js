/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var criterios=0;

Ext.onReady(function(){   
    Ext.tip.QuickTipManager.init();      
	//  25-06-2014
	//  <arsuarez@telconet.ec>
	//Si mensaje existe volver a ejecutar el submit por cada excepcion que devuelva ORACLE de violacion de UNIQUE CONSTRAINT    
    if (devuelveError) {
        if (devuelveError == 'ORA-ERROR')            
            document.forms[0].submit();	//Realiza el submit nuevamente ya que retorna error de ORACLE ( UNIQUE CONSTRAINT ESTABLECIDO )
        else
            Ext.Msg.alert('Alerta ', devuelveError);
    }    
    
    if (strMsgCliente == 'Error')
    {
        Ext.Msg.alert('Alerta ', 'La sesión del cliente ha caducado');
    }
    
    //Se setea la afectacion por defecto para todos los casos como CAIDA
    document.getElementById("telconet_schemabundle_infocasotype_tipoAfectacion").value = "CAIDA";
    
    fecha = Ext.create('Ext.form.Panel', {
        renderTo: 'div_fe_apertura',
        id: 'fe_apertura',
        name:'fe_apertura',
        width: 144,
        frame:false,
        bodyPadding: 0,
        height:30,
        border:0,
        margin:0,
        items: [{
            xtype: 'datefield',
            id: 'fe_apertura_value',
            name:'fe_apertura_value',
            editable: false,
            anchor: '100%',
            format: 'Y-m-d',
            value:new Date(),
            maxValue: new Date()  // limited to the current date or prior
        }]
    });
    hora = Ext.create('Ext.form.Panel', {
        width: 144,        
        frame:false,
        height:30,
        id: 'ho_apertura',
        name:'ho_apertura',
        border:0,
        margin:0,
        renderTo: 'div_hora_apertura',
        items: [{
            xtype: 'timefield',
            format: 'H:i',
            id: 'ho_apertura_value',
            name: 'ho_apertura_value',
            minValue: '00:01 AM',
            maxValue: '23:59 PM',
            increment: 1,
            value:new Date(),
            anchor: '100%'
        }]
    });
    
    document.getElementById("telconet_schemabundle_infocasotype_tipoCasoId").value = "Escoja una opcion";
		    
	storeSintomas = new Ext.data.Store({ 
		pageSize: 10,
		total: 'total',
		proxy: {
			type: 'ajax',
			url : 'getSintomasXCaso',
			reader: {
				type: 'json',
				totalProperty: 'total',
				root: 'encontrados'
			},
			extraParams: {
				id: '',
				nombre: '',
				estado: 'Todos',
				boolCriteriosAfectados: ''
			}
		},
		fields:
		[
			{name:'id_sintoma', mapping:'id_sintoma'},
			{name:'nombre_sintoma', mapping:'nombre_sintoma'},
			{name:'criterios_sintoma', mapping:'criterios_sintoma'},
			{name:'afectados_sintoma', mapping:'afectados_sintoma'}
		]
	});
	selModelSintomas = Ext.create('Ext.selection.CheckboxModel', {
	   listeners: {
			selectionchange: function(sm, selections) {
				gridSintomas.down('#removeButton').setDisabled(selections.length == 0);
			}
		}
	});
    
    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 1,
        listeners: {
            edit: function(editor, object) {
                var rowIdx = object.rowIdx;
                var column = object.field;
                var currentIp = object.value;
                var store = gridSintomas.getStore().getAt(rowIdx);
            }
        }
    });

	comboSintomaStore = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url: url_sintomaGrid,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
                estado: 'Activo',
                tipoCaso: ''
            }
        },
        fields:
            [
                {name: 'id_sintoma', mapping: 'id_sintoma'},
                {name: 'nombre_sintoma', mapping: 'nombre_sintoma'}
            ],
        listeners: {

            load: function(store) {
                if(store.getCount() == 0)
                {
                    Ext.Msg.alert('Alerta ', 'No hay sintomas asociados a este tipo de caso');
                };
            }
        }
    });

	// Create the combo box, attached to the states data store
	comboSintoma = Ext.create('Ext.form.ComboBox', {
		id:'comboSintoma',
		store: comboSintomaStore,
		displayField: 'nombre_sintoma',
		valueField: 'id_sintoma',
		height:30,
		border:0,
		margin:0,
		fieldLabel: false,	
		queryMode: "remote",
		emptyText: ''
	});
	Ext.define('Sintoma', {
		extend: 'Ext.data.Model',
		fields: [
			{name: 'id_sintoma',  type: 'string'},
			{name: 'nombre_sintoma',  type: 'string'}
		]
	});

	var permiso = '{{ is_granted("ROLE_78-32") }}';
	var boolPermisoSintoma = (typeof permiso === 'undefined') ? false : (permiso ? true : false);	
	if(boolPermisoSintoma)
	{
		gridSintomas = Ext.create('Ext.grid.Panel', 
        {
            id: 'gridSintomas',
            store: storeSintomas,
            viewConfig: {enableTextSelection: true, stripeRows: true},
            columnLines: true,
            columns: [
                {
                    id: 'id_sintoma',
                    header: 'SintomaId',
                    dataIndex: 'id_sintoma',
                    hidden: true,
                    hideable: false
                }, {
                    id: 'nombre_sintoma',
                    header: 'Sintoma',
                    dataIndex: 'nombre_sintoma',
                    width: 420,
                    sortable: true,
                    renderer: function(value, metadata, record, rowIndex, colIndex, store)
                    {
                        for (var i = 0; i < comboSintomaStore.data.items.length; i++) {

                            if ((comboSintomaStore.data.items[i].data.nombre_sintoma == value) ||
                                (comboSintomaStore.data.items[i].data.id_sintoma == value))
                            {
                                record.data.id_sintoma = comboSintomaStore.data.items[i].data.id_sintoma;
                                record.data.nombre_sintoma = comboSintomaStore.data.items[i].data.nombre_sintoma;

                                break;
                            }
                            if (i == (comboSintomaStore.data.items.length - 1))
                            {
                                record.data.nombre_sintoma = '';
                            }
                        }

                        return record.data.nombre_sintoma;
                    },
                    editor: {
                        id: 'searchSintoma_cmp',
                        xtype: 'combobox',
                        displayField: 'nombre_sintoma',
                        valueField: 'id_sintoma',
                        loadingText: 'Buscando ...',
                        store: comboSintomaStore,
                        fieldLabel: false,
                        queryMode: "remote",
                        emptyText: '',
                        listClass: 'x-combo-list-small'
                    }
			},{
				id: 'criterios_sintoma',
				header: 'criterios_sintoma',
				dataIndex: 'criterios_sintoma',
				hidden: true,
				hideable: false
			},{
				id: 'afectado_sintoma',
				header: 'afectado_sintoma',
				dataIndex: 'afectado_sintoma',
				hidden: true,
				hideable: false
			},
{
                header: 'Afectados',
                xtype: 'actioncolumn',
                width: 230,                
                sortable: false,
                items: [
                    {
                        getClass: function(v, meta, rec) {

                            this.items[0].tooltip = arrayTituloPaneles[arrayPaneles.indexOf("PanelElementos")]; 
                                                        
                            if (arrayPaneles.indexOf("PanelElementos") !== -1 && tipoCaso === 'Backbone')
                                return 'button-grid-afectados';
                            if (arrayPaneles.indexOf("PanelElementos") !== -1 && tipoCaso === 'Movilizacion')
                                return 'button-grid-afectados-movilizacion';
                            if (arrayPaneles.indexOf("PanelElementos") !== -1 && tipoCaso === 'Seguridad')
                                return 'button-grid-afectados-seguridad-monitoreo';
                            if (arrayPaneles.indexOf("PanelElementos") !== -1 && tipoCaso === 'Monitoreo')
                                return 'button-grid-afectados-seguridad-monitoreo';                            
                            else
                                return '';
                        },
                        handler: function(grid, rowIndex, colIndex) {
                            agregarAfectadosXSintoma(grid.getStore().getAt(rowIndex).data.nombre_sintoma, "PanelElementos");
                        }
                    },
                    {
                        getClass: function(v, meta, rec) {

                            if (arrayPaneles.indexOf("PanelServicios") !== -1 && (tipoCaso === 'Tecnico' || tipoCaso === 'Arcotel')&&
                                $('#cliente').val() !== 'no cliente')
                            {
                                this.items[1].tooltip = arrayTituloPaneles[arrayPaneles.indexOf("PanelServicios")];
                                return 'button-grid-afectados-cliente-empleado';
                            }
                            if (arrayPaneles.indexOf("PanelEmpleados") !== -1 && tipoCaso === 'Seguridad' && empresa === 'TN')
                            {
                                this.items[1].tooltip = arrayTituloPaneles[arrayPaneles.indexOf("PanelEmpleados")];
                                return 'button-grid-afectados-cliente-empleado';
                            }                            
                            else
                            {
                                return '';
                            }
                        },
                        handler: function(grid, rowIndex, colIndex) {
                            var panel;
                            if (arrayPaneles.indexOf("PanelServicios") !== -1)
                            {
                                panel = "PanelServicios";
                            }
                            if (arrayPaneles.indexOf("PanelEmpleados") !== -1)
                            {
                                panel = "PanelEmpleados";
                            }                          
                            
                            idPuntoCliente = $('#cliente').val();
	
                            if (idPuntoCliente === 'no cliente' && panel === "PanelServicios")
                            {                                
                                Ext.Msg.alert("Alerta", "Para agregar servicios afectados debe tener un cliente en Sesion");
                                return false;		                            
                            }                                                        
                            
                            agregarAfectadosXSintoma(grid.getStore().getAt(rowIndex).data.nombre_sintoma, panel);
                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {                                                                              
                            if (arrayPaneles.indexOf("PanelProveedores") !== -1 && tipoCaso === 'Backbone' && empresa === 'TN')
                            {
                                this.items[2].tooltip = arrayTituloPaneles[arrayPaneles.indexOf("PanelProveedores")];
                                return 'button-grid-afectados-proveedores';
                            }
                            else
                            {
                                return '';
                            }
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {                                               
                            agregarAfectadosXSintoma(grid.getStore().getAt(rowIndex).data.nombre_sintoma, "PanelProveedores");
                        }
                    }
                ]
            }],
			selModel: selModelSintomas,
			// inline buttons
			dockedItems: [{
				xtype: 'toolbar',
				items: [{
					itemId: 'removeButton',
					text:'Eliminar',
					tooltip:'Elimina el item seleccionado',
					disabled: true,
					handler : function(){eliminarSeleccion(gridSintomas);}
				}, '-', {
					text:'Agregar',
					tooltip:'Agrega un item a la lista',
					handler : function(){
                    if(seleccionarTipoCaso == "1") // Validacion para identificar que se ha seleccionado un Tipo de Caso
                    {
						//RONALD .------ VALIDAR AQUI QUE SE HAYA ESCOGIDO UN SINTOMA ANTERIOR... ANTES DE CREAR OTRO..
						var storeValida = Ext.getCmp("gridSintomas").getStore();
						var boolSigue = false;
						var boolSigue2 = false;
						
						if(storeValida.getCount() > 0)
						{
							var boolSigue_vacio = true;
							var boolSigue_igual = true;
							for(var i = 0; i < storeValida.getCount(); i++)
							{
								var id_sintoma = storeValida.getAt(i).data.id_sintoma;
								var nombre_sintoma = storeValida.getAt(i).data.nombre_sintoma;                                                                
								
								if(id_sintoma != "" && nombre_sintoma != ""){ /*NADA*/  }
								else {  boolSigue_vacio = false; }
								
								if(i>0)
								{
									for(var j = 0; j < i; j++)
									{
										var id_sintoma_valida = storeValida.getAt(j).data.id_sintoma;
										var nombre_sintoma_valida = storeValida.getAt(j).data.nombre_sintoma;
										
										if(id_sintoma_valida == id_sintoma || nombre_sintoma_valida == nombre_sintoma)
										{
											boolSigue_igual = false;	
										}
									}
								}
							} 
							
							if(boolSigue_vacio) { boolSigue = true; }	
							if(boolSigue_igual) { boolSigue2 = true; }					
						}
						else
						{
							boolSigue = true;
							boolSigue2 = true;
						}
						
						if(boolSigue && boolSigue2)
						{
							// Create a model instance
							var r = Ext.create('Sintoma', {
								id_sintoma: '',
								nombre_sintoma: '',
								criterios_sintoma: '',
								afectados_sintoma: ''
							});
							storeSintomas.insert(0, r);
						}
						else if(!boolSigue)
						{
							Ext.Msg.alert('Alerta ',"Debe completar datos de los sintomas a ingresar, antes de solicitar un nuevo sintoma");
						}
						else if(!boolSigue2)
						{
							Ext.Msg.alert('Alerta ',"No puede ingresar el mismo sintoma! Debe modificar el registro repetido, antes de solicitar un nuevo sintoma");
						}
						else
						{
							Ext.Msg.alert('Alerta ',"Debe completar datos de los sintomas a ingresar, antes de solicitar un nuevo sintoma");
						}
                    }
                    else
                    {
                        Ext.Msg.alert('Alerta','Debe seleccionar un Tipo de Caso');
                        return;
                    }}
				}]
			}],

			width: 750,
			height:  240,
			renderTo: 'div_sintomas',
			frame: true,
			title: 'Agregar Sintomas',
			plugins: [cellEditing]
		});
	}	
});

function validarFormulario(){     
  
   if(document.getElementById("telconet_schemabundle_infocasotype_tipoCasoId").value=="Escoja una opcion"){
        Ext.Msg.alert("Alerta","El campo tipo de caso es requerido.");
        return false;
    }
    if(document.getElementById("telconet_schemabundle_infocasotype_tipoNotificacionId").value==""){
        Ext.Msg.alert("Alerta","El campo forma de contacto es requerido.");
        return false;
    } 
    if(document.getElementById("telconet_schemabundle_infocasotype_nivelCriticidadId").value=="Escoja una opcion"){
        Ext.Msg.alert("Alerta","El campo nivel de criticidad es requerido.");
        return false;
    }
    fecha = document.getElementById("fecha_apertura");
    fecha.value =  Ext.getCmp('fe_apertura').getValues().fe_apertura_value;
    if(fecha.value==""){
        Ext.Msg.alert("Alerta","El campo fecha de apertura es requerido.");
        return false;
    }
	
    hora = document.getElementById("hora_apertura");
    hora.value =  Ext.getCmp('ho_apertura').getValues().ho_apertura_value;
    if(hora.value==""){
        Ext.Msg.alert("Alerta","El campo hora de apertura es requerido.");
        return false;
    }
    if(document.getElementById("telconet_schemabundle_infocasotype_tituloIni").value==""){
        Ext.Msg.alert("Alerta","El campo titulo inicial es requerido.");
        return false;
    }
    if(document.getElementById("telconet_schemabundle_infocasotype_versionIni").value==""){
        Ext.Msg.alert("Alerta","El campo version inicial es requerido.");
        return false;
    }              
   
     var pto_cliente_id = $('#cliente').val();	        
	
	if (pto_cliente_id != 'no cliente')
	{
		var storeValida = Ext.getCmp("gridSintomas").getStore(); 
		if(storeValida.getCount() <= 0)
		{
		    Ext.Msg.alert("Alerta", "Debe ingresar al menos un sintoma para crear el Caso");
		    return false;		
		}
	}
	
	var valorBool = validarSintomas();		        
	
	if(valorBool)
	{
		json_sintomas = obtenerSintomas();
		
		sintoma = Ext.JSON.decode(json_sintomas);
		
		if(sintoma.total == 0){
			 Ext.Msg.alert("Alerta", "Debe ingresar al menos un sintoma para crear un caso.");
			 return false;
		}else $('#sintomas_escogidos').val(json_sintomas);
		
	}	
	else
	{
		return false;
	}		
	
	if (pto_cliente_id == 'no cliente' && criterios == 0)
    {
        Ext.Msg.alert("Alerta", "El Caso requiere tener al menos un Afectado : <br>- Punto/Cliente en Sesion <b>ó</b>\n\
                        <br>-Afectaciones de Backbone");
        return false;
    }
    
    if(tipoCaso === 'Backbone' && criterios == 0)
    {
        Ext.Msg.alert("Alerta", "Debe ingresar al menos un afectado para crear el <b>Tipo de Caso Backbone</b>.");
        return false;
    }
    
    return true;

}

/**
 * 
 * Función que valida datos del formulario. Es llamado cuando se hace click en boton guardar
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 07-11-2018
 * @since 1.0
 */
function validarDatosFormulario(){
    //Pide confirmación de grabar caso sin asociar con una asignación
    if( document.getElementById("asignacionSolicitud") !== null && document.getElementById("asignacionSolicitud").value === ""){
        Ext.Msg.confirm('Alerta','Tiene asignaciones pendientes y no ha asociado ninguna al caso, '+
                                 '¿está seguro(a) de crear el caso sin asignación?',function(btn)
        {
            if (btn == "no"){
                return false;
            }
            else
            {
                if (validarFormulario()) {
                                      Ext.MessageBox.wait("Grabando Datos...", "Por favor espere");
                                      document.forms[0].submit();
                }
            }
        });
    }
    else
    {
        if (validarFormulario()) {
                                      Ext.MessageBox.wait("Grabando Datos...", "Por favor espere");
                                      document.forms[0].submit();
        }
    }
}

function validador(e,tipo) {      
        
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
       
    
    
    if(tipo=='numeros'){    
      letras = "0123456789";
      especiales = [8, 37,36,9];
    }else if(tipo=='letras'){
      letras = "abcdefghijklmnopqrstuvwxyz";
      especiales = [8, 37, 32,46,36,38,40,44,9];
    }
    else{ 
      letras = "abcdefghijklmnopqrstuvwxyz0123456789";
      especiales = [8, 37, 32,46,36,38,40,44,9];
    }
    
    //46 => .    
    //32 => ' ' espacio
    //8 => backspace       
    //37 => direccional izq
    //39 => direccional der y '
    //44 => ,

    tecla_especial = false
    for(var i in especiales) {
        if(key == especiales[i]) {
            tecla_especial = true;
            break;
        }
    }
                

    if(letras.indexOf(tecla) == -1 && !tecla_especial)   
        return false;
}

function eliminarSeleccion(datosSelect)
{	
	
	for(var i = 0; i < datosSelect.getSelectionModel().getCount(); i++)
	{		
		datosSelect.getStore().remove(datosSelect.getSelectionModel().getSelection()[i]);
	}
	
	criterios=0;
}