/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function validarFormulario()
{
  obtenerRelaciones();

  var relaciones=gridRelaciones.getStore().getCount();

  var accion_id = 0;  
  for(var i=0; i < gridRelaciones.getStore().getCount(); i++)
  {  	
  	if(!gridRelaciones.getStore().getAt(i).data.accion_id)
  	{
  		accion_id = accion_id + 1;
  	}
  }  

  if(document.getElementById('telconet_seguridadBundle_sistmodulotype_nombreModulo').value=="")
  {
    alert("Se debe registrar el Modulo");
    return false;
  }
  
  else if(relaciones == 0)
  {
    alert("No se han registrado las relaciones");
    return false;
  }
  else if(accion_id>0)
  {
    alert("Por lo menos una accion se encuentra vacia");
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
    var accion=grid.getStore().getAt(i).get('accion_id');
    var item_menu=grid.getStore().getAt(i).get('item_menu_id');

    if(accion!=""){
        if((accion == myRecord.get('accion_id') && item_menu == myRecord.get('item_menu_id')) || accion == myRecord.get('accion_id'))
        {
          existe=true;
          break;
        }
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

Ext.onReady(function(){
    Ext.tip.QuickTipManager.init();
    
    var comboAccionStore = new Ext.data.Store({  
            pageSize: 1000,
            proxy: {
                type: 'ajax',
                url : '../sist_accion/grid',
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    estado: "Activo"
                }
            },
            fields:
                      [
                        {name:'id_accion', mapping:'id_accion'},
                        {name:'nombre_accion', mapping:'nombre_accion'}
                      ]
        });
        
    comboItemMenuStore = new Ext.data.Store({
            pageSize: 1000,
            proxy: {
                type: 'ajax',
                url : '../sist_item_menu/grid',
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
                        {name:'id_item_menu', mapping:'id_item_menu'},
                        {name:'nombre_item_menu', mapping:'nombre_item_menu'}
                      ]
        });
        
    comboTareaStore = new Ext.data.Store({
            pageSize: 1000,
            proxy: {
                type: 'ajax',
                url : 'getTareas',
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
                        {name:'id_tarea', mapping:'id_tarea'},
                        {name:'nombre_tarea', mapping:'nombre_tarea'}
                      ]
        });
    
    /*******************Creacion Grid******************/
    ////////////////Grid  Relaciones////////////////
    Ext.define('Relacion', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'modulo_id', mapping:'modulo_id'},
            {name:'accion_id', mapping:'accion_id'},
            {name:'accion_nombre'},
            {name:'item_menu_id', mapping:'item_menu_id'},
            {name:'item_menu_nombre'},
            {name:'tarea_id', mapping:'tarea_id'},
            {name:'tarea_nombre'}
        ]
    });

    
    storeRelaciones = Ext.create('Ext.data.Store', {
        // destroy the store if the grid is destroyed
        autoDestroy: true,
        autoLoad: false,
        model: 'Relacion',        
        proxy: {
            type: 'ajax',
            // load remote data using HTTP
            url: 'gridRelacioness',
            // specify a XmlReader (coincides with the XML format of the returned data)
            reader: {
                type: 'json',
                totalProperty: 'total',
                // records will have a 'plant' tag
                root: 'relaciones'
            }
        }
    });
        
    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 2,
        listeners: {
            edit: function(){
                // refresh summaries
                gridRelaciones.getView().refresh();
            }
        }
    });
    
    var selModelRelaciones = Ext.create('Ext.selection.CheckboxModel', {
        listeners: {
            selectionchange: function(sm, selections) {
                gridRelaciones.down('#removeButton').setDisabled(selections.length == 0);
            }
        }
    });
    
    // create the grid and specify what field you want
    // to use for the editor at each header.
    gridRelaciones = Ext.create('Ext.grid.Panel', {
        id:'gridRelaciones',
        store: storeRelaciones,
        columnLines: true,
        columns: [{
            id: 'modulo_id',
            header: 'ModuloId',
            dataIndex: 'modulo_id',
            hidden: true,
            hideable: false
        }, {
            id: 'accion_id',
            header: 'AccionId',
            dataIndex: 'accion_id',
            hidden: true,
            hideable: false
        }, {
            id: 'accion_nombre',
            header: 'Accion',
            dataIndex: 'accion_nombre',
            width: 220,
            sortable: true,
            renderer: function (value, metadata, record, rowIndex, colIndex, store){
                if (typeof(record.data.accion_nombre) == "number")
                {
                    record.data.accion_id = record.data.accion_nombre;
                    for (var i = 0;i< comboAccionStore.data.items.length;i++)
                    {
                        if (comboAccionStore.data.items[i].data.id_accion == record.data.accion_id)
                        {
                            record.data.accion_nombre = comboAccionStore.data.items[i].data.nombre_accion;
                            break;
                        }
                    }
                }
                return record.data.accion_nombre;
            },
            editor: {
                id:'searchAccion_cmp',
                xtype: 'combobox',
                typeAhead: true,
                displayField:'nombre_accion',
                valueField: 'id_accion',
                triggerAction: 'all',
                selectOnFocus: true,
                loadingText: 'Buscando ...',
                hideTrigger: false,
                store: comboAccionStore,
                lazyRender: true,
                listClass: 'x-combo-list-small',
                listeners: {
                    select: function(combo){
                        var r = Ext.create('Relacion', {
                            modulo_id: '',
                            accion_id: combo.getValue(),
                            accion_nombre: combo.lastSelectionText,
                            item_menu_id: '',
                            item_menu_nombre: '',
                            tarea_id: '',
                            tarea_nombre: ''
                        });
                        if(!existeRecordRelacion(r, gridRelaciones))
                        {
                            Ext.get('searchAccion_cmp').dom.value='';
                            if(r.accion_id != 'null')
                            {
                                Ext.get('searchAccion_cmp').dom.value=r.get('accion_nombre');
                                this.collapse();
                            }
                        }
                        else
                        {
                            alert('Ya existe');
                            eliminarSeleccion(gridRelaciones);
                        }
                    }
                }
            }
        }, {
            id: 'item_menu_id',
            header: 'ItemMenuId',
            dataIndex: 'item_menu_id',
            hidden: true,
            hideable: false
        }, {
            id: 'item_menu_nombre',
            header: 'Item Menu',
            dataIndex: 'item_menu_nombre',
            width: 220,
            sortable: true,
            renderer: function (value, metadata, record, rowIndex, colIndex, store){
                if (typeof(record.data.item_menu_nombre) == "number")
                {
                    record.data.item_menu_id = record.data.item_menu_nombre;
                    for (var i = 0;i< comboItemMenuStore.data.items.length;i++)
                    {
                        if (comboItemMenuStore.data.items[i].data.id_item_menu == record.data.item_menu_id)
                        {
                            record.data.item_menu_nombre = comboItemMenuStore.data.items[i].data.nombre_item_menu;
                            break;
                        }
                    }
                }
                return record.data.item_menu_nombre;
            },
            editor: {
                id:'searchItemMenu_cmp',
                xtype: 'combobox',
                typeAhead: true,
                displayField:'nombre_item_menu',
                valueField: 'id_item_menu',
                triggerAction: 'all',
                selectOnFocus: true,                
                loadingText: 'Buscando ...',
                hideTrigger: false,
                store: comboItemMenuStore,
                lazyRender: true,
                listClass: 'x-combo-list-small',
                listeners: {
                    select: function(combo) {
                        
                        var r = Ext.create('Relacion', {
                            modulo_id: '',
                            accion_id: '',
                            accion_nombre: '',
                            item_menu_id: combo.getValue(),
                            item_menu_nombre: combo.lastSelectionText,
                            tarea_id: '',
                            tarea_nombre: ''
                        });                            
                        if(!existeRecordRelacion(r, gridRelaciones))
                        {
                            
                            Ext.get('searchItemMenu_cmp').dom.value='';
                            if(r.item_menu_id != 'null')
                            {
                                Ext.get('searchItemMenu_cmp').dom.value=r.get('item_menu_nombre');
                                this.collapse();
                            }
                        }
                        else
                        {
                            alert('Ya existe');
                            eliminarSeleccion(gridRelaciones);
                        }
                    }
                }
            }
        }, {
            id: 'tarea_id',
            header: 'Tarea',
            dataIndex: 'tarea_id',
            hidden: true,
            hideable: false
        }, {
            id: 'tarea_nombre',
            header: 'Tarea/Modelo',
            dataIndex: 'tarea_nombre',
            width: 220,
            sortable: true,
            renderer: function (value, metadata, record, rowIndex, colIndex, store){
                if (typeof(record.data.tarea_nombre) == "number")
                {
                    record.data.tarea_id = record.data.tarea_nombre;
                    for (var i = 0;i< comboTareaStore.data.items.length;i++)
                    {
                        if (comboTareaStore.data.items[i].data.id_tarea == record.data.tarea_id)
                        {
                            record.data.tarea_nombre = comboTareaStore.data.items[i].data.nombre_tarea;
                            break;
                        }
                    }
                }
                return record.data.tarea_nombre;
            },
            editor: {
                id:'searchTarea_cmp',
                xtype: 'combobox',
                typeAhead: true,
                displayField:'nombre_tarea',
                valueField: 'id_tarea',
                triggerAction: 'all',
                selectOnFocus: true,                
                loadingText: 'Buscando ...',
                hideTrigger: false,
                store: comboTareaStore,
                lazyRender: true,
                listClass: 'x-combo-list-small',
                listeners: {
                    select: function(combo) {
                        
                        var r = Ext.create('Relacion', {
                            modulo_id: '',
                            accion_id: '',
                            accion_nombre: '',
                            item_menu_id: '',
                            item_menu_nombre: '',
                            tarea_id: combo.getValue(),
                            tarea_nombre: combo.lastSelectionText
                        });                            
                        if(!existeRecordRelacion(r, gridRelaciones))
                        {
                            
                            Ext.get('searchTarea_cmp').dom.value='';
                            if(r.tarea_id != 'null')
                            {
                                Ext.get('searchTarea_cmp').dom.value=r.get('tarea_nombre');
                                this.collapse();
                            }
                        }
                        else
                        {
                            alert('Ya existe');
                            eliminarSeleccion(gridRelaciones);
                        }
                    }
                }
            }
        }],
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
                tooltip:'Elimina el item seleccionado',
                disabled: true,
                handler : function(){eliminarSeleccion(gridRelaciones);}
            }, '-', {
                text:'Agregar',
                tooltip:'Agrega un item a la lista',
                handler : function(){
                    // Create a model instance
                    var r = Ext.create('Relacion', {
                        modulo_id: '',
                        accion_id: '',
                        accion_nombre: '',
                        item_menu_id: '',
                        item_menu_nombre: ''
                    });
                    if(!existeRecordRelacion(r, gridRelaciones))
                    {
                        storeRelaciones.insert(0, r);
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
        height: 400,
        frame: true,
        title: 'Agregar Informacion de relacion',
        renderTo: 'grid',
        plugins: [cellEditing]
    });
    
    /**************************************************/
    // manually trigger the data store load
    
});
function obtenerRelaciones()
{
  var array_relaciones = new Object();
  array_relaciones['total'] =  gridRelaciones.getStore().getCount();
  array_relaciones['relaciones'] = new Array();
  var array_data = new Array();
  for(var i=0; i < gridRelaciones.getStore().getCount(); i++)
  {
  	array_data.push(gridRelaciones.getStore().getAt(i).data);
  }
  array_relaciones['relaciones'] = array_data;
  Ext.get('relaciones').dom.value = Ext.JSON.encode(array_relaciones);
}