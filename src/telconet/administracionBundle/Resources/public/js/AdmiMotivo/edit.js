/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function validarFormulario()
{
    obtenerMotivos();
    
    var modulo = Ext.get('escogido_modulo_id').dom.value;
   // var itemmenu = Ext.get('escogido_itemmenu_id').dom.value;
    var accion = Ext.get('escogido_accion_id').dom.value;
    if(modulo=="" || !modulo) {  modulo = 0; }
    //if(itemmenu=="" || !itemmenu) {  itemmenu = 0; }
    if(accion=="" || !accion) {  accion = 0; }
         
    var motivos = gridMotivos.getStore().getCount();
    var motivo_id = 0;  
    for(var i=0; i < gridMotivos.getStore().getCount(); i++)
    {  	
		if(!gridMotivos.getStore().getAt(i).data.motivo_id)
		{
			motivo_id = motivo_id + 1;
		}
    }  
    
    if(modulo==0)
    {
        alert("No se ha escogido el Modulo");
        return false;
    }/*
    else if(itemmenu==0)
    {
        alert("No se ha escogido el Item Menu");
        return false;
    }*/
    else if(accion==0)
    {
        alert("No se ha escogido la Accion");
        return false;
    }
    else if(motivos == 0)
    {
        alert("No se han registrado los motivos");
        return false;
    }
  
    return true;
}

function existeRecordRelacion(myRecord, grid)
{
  var existe=false;
  var num=grid.getStore().getCount();

  for(var i=0; i < num ; i++)
  {
    var nombre_motivo=grid.getStore().getAt(i).get('nombre_motivo');

    if(nombre_motivo == myRecord.get('nombre_motivo') )
    {
      existe=true;
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
    /* ****************** MOTIVOS ************************ */
    Ext.define('Motivos', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_motivo', mapping:'id_motivo'},
            {name:'relacionsistema_id', mapping:'relacionsistema_id'},
            {name:'nombre_motivo', mapping:'nombre_motivo'}
        ]
    });
    storeMotivos = Ext.create('Ext.data.Store', {
        // destroy the store if the grid is destroyed
        autoDestroy: true,
        autoLoad: false,
        model: 'Motivos',        
        proxy: {
            type: 'ajax',
            // load remote data using HTTP
            url: '../getListadoMotivos',
            // specify a XmlReader (coincides with the XML format of the returned data)
            reader: {
                type: 'json',
                totalProperty: 'total',
                // records will have a 'plant' tag
                root: 'encontrados'
            }
        }
    });
       
    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 2,
        listeners: {
            edit: function(){
                // refresh summaries
                gridMotivos.getView().refresh();
            }
        }
    });
    
    var selModelRelaciones = Ext.create('Ext.selection.CheckboxModel', {
        listeners: {
            selectionchange: function(sm, selections) {
                gridMotivos.down('#removeButton').setDisabled(selections.length == 0);
            }
        }
    });
    
    // create the grid and specify what field you want
    // to use for the editor at each header.
    gridMotivos = Ext.create('Ext.grid.Panel', {
        id:'gridMotivos',
        store: storeMotivos,
        columnLines: true,
        columns: [{
            id: 'id_motivo',
            header: 'MotivoId',
            dataIndex: 'id_motivo',
            hidden: true,
            hideable: false
        }, {
            id: 'relacionsistema_id',
            header: 'RelacionSistemaId',
            dataIndex: 'relacionsistema_id',
            hidden: true,
            hideable: false
        }, {
            id: 'nombre_motivo',
            header: 'Motivo',
            dataIndex: 'nombre_motivo',
            width: 320,
            sortable: true,
            editor: {
                id:'searchAccion_cmp',
                xtype: 'textfield',
                typeAhead: true,
                displayField:'nombre_motivo',
                valueField: 'id_motivo',
                size: 300
            } 
        }
        ],
        selModel: selModelRelaciones,
        viewConfig:{
            stripeRows:true
        },

        // inline buttons
        dockedItems: [{
            xtype: 'toolbar',
            items: [{
                itemId: 'removeButton',
                text:'Eliminar',
                tooltip:'Elimina el motivo seleccionado',
                iconCls:'remove',
                disabled: true,
                handler : function(){eliminarSeleccion(gridMotivos);}
            }, '-', {
                itemId: 'addButton',
                text:'Agregar',
                tooltip:'Agrega un motivo a la lista',
                iconCls:'add',
                disabled: true,
                handler : function(){
                    // Create a model instance
                    var r = Ext.create('Motivos', {
                        motivo_id: '',
                        relacionsistema_id: '',
                        motivo_nombre: ''
                    });
                    if(!existeRecordRelacion(r, gridMotivos))
                    {
                        storeMotivos.insert(0, r);
                        cellEditing.startEditByPosition({row: 0, column: 3});
                    }
                    else
                    {
                      alert('Ya existe un registro vacio.');
                    }
                }
            }]
        }],

        width: 700,
        height: 200,
        frame: true,
        title: 'Agregar Informacion de Motivo',
        renderTo: 'grid',
        plugins: [cellEditing]
    });
});

function obtenerMotivos()
{
  var array_motivos = new Object();
  array_motivos['total'] =  gridMotivos.getStore().getCount();
  array_motivos['motivos'] = new Array();
  var array_data = new Array();
  for(var i=0; i < gridMotivos.getStore().getCount(); i++)
  {
  	array_data.push(gridMotivos.getStore().getAt(i).data);
  }
  array_motivos['motivos'] = array_data;
  Ext.get('motivos').dom.value = Ext.JSON.encode(array_motivos);
}