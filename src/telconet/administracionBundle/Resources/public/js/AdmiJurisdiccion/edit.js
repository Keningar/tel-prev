/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


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

  if(document.getElementById('telconet_schemabundle_admijurisdicciontype_nombreJurisdiccion').value=="")
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
//    alert(canton);
    if((canton == myRecord.get('canton_id')))
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

Ext.onReady(function(){
    Ext.tip.QuickTipManager.init();
    
    var comboCantonStore = new Ext.data.Store({  
            pageSize: 1000,
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url : '../../../general/admi_canton/getCantones',
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
    
    var storeCantonJurisdiccion = new Ext.data.Store({ 
        total: 'total',
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : 'getCantonesJurisdicciones',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'canton_id', mapping:'canton_id'},
                {name:'nombreCanton', mapping:'nombreCanton'},
                {name:'mailTecnico', mapping:'mailTecnico'},
                {name:'ipReserva', mapping:'ipReserva'},
                {name:'idCantonJurisdiccion', mapping:'idCantonJurisdiccion'}
              ]
    });
    
    /*******************Creacion Grid******************/
    ////////////////Grid  Relaciones////////////////
    Ext.define('CantonJurisdiccion', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'idCantonJurisdiccion', mapping:'idCantonJurisdiccion'},
            {name:'canton_id', mapping:'canton_id'},
            {name:'nombreCanton', mapping:'nombreCanton'},
            {name:'mailTecnico', mapping:'mailTecnico'},
            {name:'ipReserva', mapping:'ipReserva'}
        ]
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
        store: storeCantonJurisdiccion,
        columnLines: true,
        columns: [{
            id: 'idCantonJurisdiccion',
            header: 'idCantonJurisdiccion',
            dataIndex: 'idCantonJurisdiccion',
            hidden: true,
            hideable: false
        }, {
            id: 'canton_id',
            header: 'CantonId',
            dataIndex: 'canton_id',
            hidden: true,
            hideable: false
        }, {
            id: 'nombreCanton',
            header: 'Canton',
            dataIndex: 'nombreCanton',
            width: 320,
            sortable: true,
            renderer: function (value, metadata, record, rowIndex, colIndex, store){
                if (typeof(record.data.nombreCanton) == "number")
                {
                    
                    record.data.canton_id = record.data.nombreCanton;
                    for (var i = 0;i< comboCantonStore.data.items.length;i++)
                    {
                        if (comboCantonStore.data.items[i].data.id_canton == record.data.canton_id)
                        {
                            record.data.nombreCanton = comboCantonStore.data.items[i].data.nombre_canton;
                            break;
                        }
                    }
                }
                return record.data.nombreCanton;
            },
            editor: {
                id:'searchCanton_cmp',
                xtype: 'combobox',
                typeAhead: true,
                displayField:'nombre_canton',
                valueField: 'id_canton',
                //triggerAction: 'all',
                selectOnFocus: true,
                loadingText: 'Buscando ...',
                //hideTrigger: false,
                store: comboCantonStore,
                //lazyRender: true,
                queryMode: 'local',
                listClass: 'x-combo-list-small',
                listeners: {
                    select: function(combo){
                        var r = Ext.create('CantonJurisdiccion', {
                            idCantonJurisdiccion: '',
                            canton_id: combo.getValue(),
                            nombreCanton: combo.lastSelectionText,
                            mailTecnico: '',
                            ipTeserva:''
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
            id: 'mailTecnico',
            header: 'Mail Tecnico',
            dataIndex: 'mailTecnico',
            width: 150,
            editor: {
                allowBlank: false
            }
        },{
            id: 'ipReserva',
            header: 'Ip Reserva',
            dataIndex: 'ipReserva',
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
                    var r = Ext.create('CantonJurisdiccion', {
                        idCantonJurisdiccion: '',
                        canton_id: '',
                        nombreCanton: '',
                        mailTecnico: '',
                        ipReserva: ''
                    });
                    if(!existeRecordRelacion(r, gridRelaciones))
                    {
                        storeCantonJurisdiccion.insert(0, r);
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