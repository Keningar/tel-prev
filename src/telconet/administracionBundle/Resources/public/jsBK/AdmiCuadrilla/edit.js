/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function obtenerIntegrantes()
{
	Ext.get('empleados_integrantes').dom.value = "";
  
	var array_integrantes = new Object();
	array_integrantes['total'] =  gridIntegrantes.getStore().getCount();
	array_integrantes['encontrados'] = new Array();
  
	var array_data = new Array();
	for(var i=0; i < gridIntegrantes.getStore().getCount(); i++)
	{
		array_data.push(gridIntegrantes.getStore().getAt(i).data);
	}
	
	array_integrantes['encontrados'] = array_data;
	Ext.get('empleados_integrantes').dom.value = Ext.JSON.encode(array_integrantes);
}

function validarFormulario()
{
    var integrantes = gridIntegrantes.getStore().getCount();    
    if(integrantes == 0)
    {
        alert("No se han registrado las integrantes");
        return false;
    }
    
    obtenerIntegrantes();
    return true;
}

function agregarEmpleadoCuadrilla()
{ 
    var empleadoId = Ext.getCmp('cmb_empleado').getValue();
    var empleadoNombre = Ext.getCmp('cmb_empleado').getRawValue();
	
	if(empleadoId != "" && empleadoId != 0 && empleadoNombre != "")
	{
		var r = Ext.create('Integrantes', {
			integrante_id: '',
			id_integrante: empleadoId,
			nombre_integrante: empleadoNombre
		});   
		
		if(!existeIntegrante(r, gridIntegrantes))
		{
			storeIntegrantes.insert(0, r);   			
		}
	}
	else
	{
		alert('Debe seleccionar el empleado a integrar la cuadrilla.');
	}
}

function existeIntegrante(myRecord, grid)
{
	var existe=false;
	var num=grid.getStore().getCount();  
	for(var i=0; i < num ; i++)
	{
	    var integrante = grid.getStore().getAt(i).get('id_integrante');    
	    if(integrante == myRecord.get('id_integrante'))
	    { 
			existe=true;
	        alert('Ya fue escogido esta persona.');
	        break;
	    }
	}
	return existe;	
}

function eliminarSeleccion(datosSelect)
{
	for(var i = 0; i < datosSelect.getSelectionModel().getCount(); i++)
	{
		datosSelect.getStore().remove(datosSelect.getSelectionModel().getSelection()[i]);
	}
}

Ext.onReady(function() {
    
    /* ****************** EMPLEADOS  ************************ */
    Ext.define('EmpleadosList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_empleado', type:'int'},
            {name:'nombre_empleado', type:'string'}
        ]
    });
    storeEmpleados = Ext.create('Ext.data.Store', {
		pageSize: 200,
		model: 'EmpleadosList',
		proxy: {
			type: 'ajax',
			url : '../getEmpleados',
			reader: {
				type: 'json',
				totalProperty: 'total',
				root: 'encontrados'
			}
		}
    });
    
    combo_empleados = new Ext.form.ComboBox({
		id: 'cmb_empleado',
		name: 'cmb_empleado',
		fieldLabel: false,
		anchor: '100%',
		queryMode:'remote',
		width: 400,
		emptyText: 'Seleccione Empleado',
		store:storeEmpleados,
		displayField: 'nombre_empleado',
		valueField: 'id_empleado',
		renderTo: 'combo_empleado',
		listeners:{
			select:{fn:function(combo, value) {
				//combo.getValue()}
			}}
		}
    });
   
    
    
        
    Ext.define('Integrantes', {
        extend: 'Ext.data.Model',
        fields: [
                    {name:'id_integrante', mapping:'id_integrante'},
                    {name:'nombre_integrante', mapping:'nombre_integrante'}
                ]
    });
    storeIntegrantes = new Ext.data.Store({ 
        total: 'total',
		autoload: true,
        proxy: {
            type: 'ajax',
            url : '../gridIntegrantes',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
		[
			{name:'id_integrante', mapping:'id_integrante'},
			{name:'nombre_integrante', mapping:'nombre_integrante'}
		]
    });
	
    /////////// Asiganaciones /////////////////////
    sm2 = Ext.create('Ext.selection.CheckboxModel', {
            checkOnly: true,
            listeners: {
                selectionchange: function(sm, selections) {
                    gridIntegrantes.down('#removeButton').setDisabled(selections.length == 0);
                }
            }
    })

    gridIntegrantes = Ext.create('Ext.grid.Panel', {
        width: 320,
        height: 800,
        store: storeIntegrantes,
        loadMask: true,
        selModel: sm2,
        iconCls: 'icon-grid',
        // grid columns
        columns:[
                {              
                    header: 'IntegranteId',
                    dataIndex: 'id_integrante',
                    hidden: true,
                    hideable: false
                },
                {
                    header: 'Nombre Integrante',
                    dataIndex: 'nombre_integrante',
                    width: 250,
                    sortable: true
                }
            ],
            dockedItems: [{
            xtype: 'toolbar',
            items: [{
                itemId: 'removeButton',
                text:'Eliminar',
                tooltip:'Elimina el integrante seleccionado',
                iconCls:'remove',
                disabled: true,
                handler : function(){eliminarSeleccion(gridIntegrantes);}
            }]
        }],
        title: 'Integrantes seleccionados a la Cuadrilla',
        frame: true ,
        renderTo: 'gridIntegrantes'
    });
    

});