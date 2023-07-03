/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Ext.onReady(function(){
    Ext.tip.QuickTipManager.init();
    
    var comboCantonStore = new Ext.data.Store({  
            pageSize: 1000,
            proxy: {
                type: 'ajax',
                url : '../../general/admi_canton/getCantones',
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            fields:
                      [
                        {name:'id_canton', mapping:'id_canton'},
                        {name:'nombre_canton', mapping:'nombre_canton'}
                      ]
        });
      
    /*******************Creacion Grid******************/
    ////////////////Grid  Relaciones////////////////
    Ext.define('Relacion', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'canton_id', mapping:'canton_id'},
            {name:'canton_nombre'},
            {name:'mail_tecnico', mapping:'mail_tecnico'},
            {name:'ip_reserva', mappgin:'ip_reserva'}
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
            id: 'canton_id',
            header: 'CantonId',
            dataIndex: 'canton_id',
            hidden: true,
            hideable: false
        }, {
            id: 'canton_nombre',
            header: 'Canton',
            dataIndex: 'canton_nombre',
            width: 320,
            sortable: true,
            renderer: function (value, metadata, record, rowIndex, colIndex, store){
                if (typeof(record.data.canton_nombre) == "number")
                {
                    record.data.canton_id = record.data.canton_nombre;
                    for (var i = 0;i< comboCantonStore.data.items.length;i++)
                    {
                        if (comboCantonStore.data.items[i].data.id_canton == record.data.canton_id)
                        {
                            record.data.canton_nombre = comboCantonStore.data.items[i].data.nombre_canton;
                            break;
                        }
                    }
                }
                return record.data.canton_nombre;
            },
            editor: {
                id:'searchCanton_cmp',
                xtype: 'combobox',
                typeAhead: true,
                displayField:'nombre_canton',
                valueField: 'id_canton',
                triggerAction: 'all',
                selectOnFocus: true,
                loadingText: 'Buscando ...',
                hideTrigger: false,
                store: comboCantonStore,
                lazyRender: true,
                listClass: 'x-combo-list-small',
                listeners: {
                    select: function(combo){
                        var r = Ext.create('Relacion', {
                            canton_id: combo.getValue(),
                            canton_nombre: combo.lastSelectionText,
                            mail_tecnico: '',
                            ip_reserva:''
                        });
                        if(!existeRecordRelacion(r, gridRelaciones))
                        {
                            Ext.get('searchCanton_cmp').dom.value='';
                            if(r.canton_id != 'null')
                            {
                                Ext.get('searchCanton_cmp').dom.value=r.get('canton_nombre');
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
        },{
            id: 'mail_tecnico',
            header: 'Mail Tecnico',
            dataIndex: 'mail_tecnico',
            width: 150,
            editor: {
                allowBlank: false
            }
        },{
            id: 'ip_reserva',
            header: 'Ip Reserva',
            dataIndex: 'ip_reserva',
            width: 150,
            editor: {
                allowBlank: false
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
                iconCls:'remove',
                disabled: true,
                handler : function(){eliminarSeleccion(gridRelaciones);}
            }, '-', {
                text:'Agregar',
                tooltip:'Agrega un item a la lista',
                iconCls:'add',
                handler : function(){
                    // Create a model instance
                    var r = Ext.create('Relacion', {
                        canton_id: '',
                        canton_nombre: '',
                        mail_tecnico: '',
                        ip_reserva:''
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
        height: 200,
        frame: true,
        title: 'Agregar Cantones',
        renderTo: 'grid',
        plugins: [cellEditing]
    });
    
    /**************************************************/
    // manually trigger the data store load
    
});

function eliminarSeleccion(datosSelect)
{
  for(var i = 0; i < datosSelect.getSelectionModel().getCount(); i++)
  {
	datosSelect.getStore().remove(datosSelect.getSelectionModel().getSelection()[i]);
  }
}

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

function validarFormulario()
{
  obtenerRelaciones();

  var relaciones=gridRelaciones.getStore().getCount();

  var canton_id = 0;  
  for(var i=0; i < gridRelaciones.getStore().getCount(); i++)
  {  	
  	if(!gridRelaciones.getStore().getAt(i).data.canton_id)
  	{
  		canton_id = canton_id + 1;
  	}
  }  

  if(document.getElementById('telconet_administracionBundle_admijurisdicciontype_nombreJurisdiccion').value=="")
  {
    alert("Se debe registrar el Nombre");
    return false;
  }
  
  else if(relaciones == 0)
  {
    alert("No se han registrado las relaciones");
    return false;
  }
  else if(canton_id>0)
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
    var canton=grid.getStore().getAt(i).get('canton_id');

    if((canton == myRecord.get('canton_id') ) || canton == myRecord.get('canton_id'))
    {
      existe=true;
      break;
    }
  }
  return existe;
}