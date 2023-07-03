function verTareaPlan(data){
    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side'
        },
        items: [{
            xtype: 'fieldset',
            title: 'Datos de la Tarea',
            defaultType: 'displayfield',
            defaults: {
                width: 650
            },
            items: [
                {
                    xtype: 'container',
                    layout: {
                        type: 'hbox',
                        pack: 'left'
                    },
                    items: [{
                    xtype: 'displayfield',
                    id:'disptxtNombreTarea',
                    name: 'disptxtNombreTarea',
                    fieldLabel: 'Nombre',
                    value: data.nombreTarea,
                    width: '100%'
                    }]
                },
                {html:"&nbsp;",border:false,width:'100%'},
                {
                    xtype: 'container',
                    layout: {
                        type: 'hbox',
                        pack: 'left'
                    },
                    items: [{
                    xtype: 'displayfield',
                    id:'disptxtDescripcionTarea',
                    name: 'disptxtDescripcionTarea',
                    fieldLabel: 'Descripcion',
                    value: data.descripcionTarea,
                    width: '100%'
                    }]
                }

            ]
        }],
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Ver Tarea',
        modal: true,
        width: 730,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
    win.query('fieldset')[0].setHeight('auto');

}





function agregarGridTareasMantenimiento(indice)
{ 
    //var storeTareasIndice = modelFactory(rec.get('Reference'), rec.get('ResultFields'));
    var storeTareasIndice = new Ext.data.Store({ 
        pageSize: 10,
        total: 'total',
        fields:
        [
            {name:'idTarea',            mapping:'idTarea'},
            {name:'nombreTarea',        mapping:'nombreTarea'}
        ]
    });
    
    

	// Create the combo box, attached to the states data store
	var comboTareas = Ext.create('Ext.form.ComboBox', {
		id:'comboTareas',
		store: comboTareasMantenimientoStore,
		displayField: 'nombreTareaCombo',
		valueField: 'idTareaCombo',
		height:30,
		border:0,
		margin:0,
		fieldLabel: false,	
		queryMode: "remote",
		emptyText: ''
	});

    Ext.define('Tarea', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idTarea',   type: 'string'},
            {name: 'nombreTarea',   type: 'string'}
        ]
    });
    
    
    var selModelTareas = Ext.create('Ext.selection.CheckboxModel', {
	   listeners: {
			selectionchange: function(sm, selections) {
                var gridTareasMantenimiento = Ext.getCmp("gridTareas_"+indice);
                gridTareasMantenimiento.down('#removeButton').setDisabled(selections.length == 0);
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
                var store = Ext.getCmp("gridTareas_"+indice).getStore().getAt(rowIdx);
            }
        }
    });
    
    

    var gridTareas = Ext.create('Ext.grid.Panel', 
    {
        id: 'gridTareas_'+indice,
        store: storeTareasIndice,
        viewConfig: {enableTextSelection: true, stripeRows: true},
        columnLines: true,
        selModel: selModelTareas,
        columns: 
        [
            {
                id: 'idTarea'+indice,
                header: 'idTarea',
                dataIndex: 'idTarea',
                hidden: true,
                hideable: false
            },

            {
                id: 'nombreTarea'+indice,
                header: 'Tarea',
                dataIndex: 'nombreTarea',
                width: 420,
                
                renderer: function(value, metadata, record, rowIndex, colIndex, store)
                {
                    
                    for (var i = 0; i < comboTareasMantenimientoStore.data.items.length; i++) 
                    {
                        if ((comboTareasMantenimientoStore.data.items[i].data.nombreTareaCombo == value) ||
                            (comboTareasMantenimientoStore.data.items[i].data.idTareaCombo == value))
                        {
                            record.data.idTarea     = comboTareasMantenimientoStore.data.items[i].data.idTareaCombo;
                            record.data.nombreTarea = comboTareasMantenimientoStore.data.items[i].data.nombreTareaCombo;
                            
                            break;
                        }
                        if (i == (comboTareasMantenimientoStore.data.items.length - 1))
                        {
                            record.data.nombreTarea = '';
                        }
                    }
                    return record.data.nombreTarea;
                },
                editor: {
                    id: 'searchTarea_cmp'+indice,
                    xtype: 'combobox',
                    displayField: 'nombreTareaCombo',
                    valueField: 'idTareaCombo',
                    loadingText: 'Buscando ...',
                    store: comboTareasMantenimientoStore,
                    fieldLabel: false,
                    queryMode: "remote",
                    emptyText: '',
                    listClass: 'x-combo-list-small'
                }
                
            }
        ],
        
        dockedItems: 
        [
            {
                xtype: 'toolbar',
                items: 
                [
                    {
                        itemId: 'removeButton',
                        text:'Eliminar',
                        tooltip:'Elimina el item seleccionado',
                        disabled: true,
                        handler : function(){eliminarSeleccion(gridTareas);}
                    },
                    '-', 
                    {
                        text:'Agregar',
                        tooltip:'Agrega un item a la lista',
                        handler : function(){
                            var strMensaje='';
                            var storeValida = Ext.getCmp("gridTareas_"+indice).getStore();
                            var storeTareasMantenimiento= Ext.getCmp("gridTareas_"+indice).getStore();

                            var bool_OK = false;

                            if(storeValida.getCount() > 0)
                            {
                                var bool_tiene_registros_vacios     = false;
                                var bool_tiene_registros_repetidos  = false;

                                //Recorre las tareas dentro del grid
                                for(var i = 0; i < storeValida.getCount(); i++)
                                {
                                    var id_tarea = storeValida.getAt(i).data.idTarea;
                                    var nombre_tarea = storeValida.getAt(i).data.nombreTarea;     

                                    if(id_tarea != "" && nombre_tarea != ""){}
                                    else 
                                    {  
                                        bool_tiene_registros_vacios = true;
                                        break;
                                    }

                                    if(i>0)
                                    {
                                        for(var j = 0; j < i; j++)
                                        {
                                            var id_tarea_valida = storeValida.getAt(j).data.idTarea;
                                            var nombre_tarea_valida = storeValida.getAt(j).data.nombreTarea;

                                            if(id_tarea_valida == id_tarea || nombre_tarea_valida == nombre_tarea)
                                            {
                                                bool_tiene_registros_repetidos = true;
                                                break;
                                            }
                                        }
                                    }
                                }

                                if(!bool_tiene_registros_vacios && !bool_tiene_registros_repetidos)
                                {
                                    bool_OK=true;
                                }
                                else if(bool_tiene_registros_vacios)
                                {
                                    strMensaje+='Debe completar datos de las tareas a ingresar, antes de solicitar una nueva tarea';
                                    Ext.Msg.alert('Alerta ',strMensaje);
                                    return false;
                                }
                                else if(bool_tiene_registros_repetidos)
                                {
                                    strMensaje+='No puede ingresar tareas repetidas en el Mantenimiento .Debe modificar el registro repetido ';
                                    Ext.Msg.alert('Alerta ',strMensaje);
                                    return false;
                                }
                            }
                            else
                            {
                                bool_OK = true;
                            }
                            
                            if(bool_OK)
                            {
                                var tarea = Ext.create('Tarea', {
                                    idTarea     : '',
                                    nombreTarea : ''
                                });
                                storeTareasMantenimiento.insert(0, tarea);
                            }
                        }
                    }
                ]
            }
        ],
        width: 750,
        height:  240,
        renderTo: 'div_tareas_'+indice,
        frame: true,
        title: 'Agregar Tareas',
        plugins: [cellEditing]
    });
}

function cambiarAMayusculas(componente)
{
    componente.onkeyup = function()
    {
        componente.value = componente.value.toUpperCase();
    };
}