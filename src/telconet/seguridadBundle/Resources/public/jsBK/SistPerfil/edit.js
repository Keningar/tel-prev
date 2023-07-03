/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


function obtenerAsignaciones()
{
  Ext.get('segu_asignaciones').dom.value = "";
  var array_asignaciones = new Object();
  array_asignaciones['total'] =  gridAsignaciones.getStore().getCount();
  array_asignaciones['asignaciones'] = new Array();
  var array_data = new Array();
  for(var i=0; i < gridAsignaciones.getStore().getCount(); i++)
  {
    
    array_data.push(gridAsignaciones.getStore().getAt(i).data);
  }
  
  array_asignaciones['asignaciones'] = array_data;
  Ext.get('segu_asignaciones').dom.value = Ext.JSON.encode(array_asignaciones);
  
}

function validarFormulario()
{
  var asignaciones=gridAsignaciones.getStore().getCount();
  
  if(asignaciones == 0)
  {
    alert("No se han registrado las asignaciones");
    return false;
  }
  obtenerAsignaciones();
  return true;
}
function ingresarAsignacion()
{ 
  if(sm.getSelection().length > 0)
  {  
    for(var i=0 ;  i < sm.getSelection().length ; ++i)
    {
        var r = Ext.create('Asignacion', {
                            perfil_id: '',
                            modulo_id: sm.getSelection()[i].get('modulo_id'),
                            modulo_nombre: sm.getSelection()[i].get('modulo_nombre'),
                            accion_id: sm.getSelection()[i].get('accion_id'),
                            accion_nombre: sm.getSelection()[i].get('accion_nombre')
                        });          
       if(!existeAsignacion(r, gridAsignaciones))
        storeAsignaciones.insert(0, r);         
    }
  }
  else
  {
    alert('Seleccione por lo menos una accion de la lista');
  }
}

function existeAsignacion(myRecord, grid)
{
  var existe=false;
  var num=grid.getStore().getCount();  
  for(var i=0; i < num ; i++)
  {
    var modulo=grid.getStore().getAt(i).get('modulo_id');
    var accion=grid.getStore().getAt(i).get('accion_id');     
    if(modulo == myRecord.get('modulo_id') && accion == myRecord.get('accion_id'))
    { 
        existe=true;
        alert('Ya existe una asignacion similar');
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

function presentarAcciones(obj)
{ 
    storeAcciones.proxy.extraParams={id_modulo: obj.value};
    storeAcciones.load({params: {id_modulo: obj.value}}); 

}

Ext.onReady(function() { 
    
        Ext.define('Asignacion', {
            extend: 'Ext.data.Model',
            fields: [
                {name:'perfil_id', mapping:'perfil_id'},
                {name:'modulo_id', mapping:'modulo_id'},
                {name:'modulo_nombre', mapping:'modulo_nombre'},
                {name:'accion_id', mapping:'accion_id'},
                {name:'accion_nombre', mapping:'accion_nombre'}
            ]
        });
    
    
        storeAsignaciones = new Ext.data.Store({ 
                autoLoad: true,
                total: 'total',
                proxy: {
                    type: 'ajax',
                    url : 'gridAsignaciones',
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'asignaciones'
                    }
                },
                fields:
                      [
                        {name:'perfil_id', mapping:'perfil_id'},
                        {name:'modulo_id', mapping:'modulo_id'},
                        {name:'modulo_nombre', mapping:'modulo_nombre'},
                        {name:'accion_id', mapping:'accion_id'},
                        {name:'accion_nombre', mapping:'accion_nombre'}
                      ]
            });
        storeAcciones = new Ext.data.Store({ 
                total: 'total',
                proxy: {
                    type: 'ajax',
                    url : '../gridAcciones',
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    }
                },
                fields:
                      [
                        {name:'modulo_id', mapping:'modulo_id'},
                        {name:'modulo_nombre', mapping:'modulo_nombre'},
                        {name:'accion_id', mapping:'accion_id'},
                        {name:'accion_nombre', mapping:'accion_nombre'}
                      ]
            });
        
        sm = Ext.create('Ext.selection.CheckboxModel', {
                checkOnly: true
            })

        grid = Ext.create('Ext.grid.Panel', {
        width: 300,
        height: 500,
        store: storeAcciones,
        loadMask: true,
        selModel: sm,
        iconCls: 'icon-grid',
        // grid columns
        columns:[
                {
              header: 'ModuloId',
              dataIndex: 'modulo_id',
              hidden: true,
              hideable: false
            },
            {
              header: 'Modulo',
              dataIndex: 'modulo_nombre',
              hidden: true,
              hideable: false
            },
            {
              header: 'AccionId',
              dataIndex: 'accion_id',
              hidden: true,
              hideable: false
            },
            {
              header: 'Acciones',
              dataIndex: 'accion_nombre',
              width: 260,
              sortable: true
            }
            ],
        title: 'Acciones del modulo',
        renderTo: 'gridAcciones'
    });
    
    /////////// Asiganaciones /////////////////////
    

        sm2 = Ext.create('Ext.selection.CheckboxModel', {
                checkOnly: true,
                listeners: {
                   selectionchange: function(sm, selections) {
                        gridAsignaciones.down('#removeButton').setDisabled(selections.length == 0);
                    }
                }
        })
        
        gridAsignaciones = Ext.create('Ext.grid.Panel', {
        width: 400,
        height: 500,
        store: storeAsignaciones,
        loadMask: true,
        selModel: sm2,
        iconCls: 'icon-grid',
        // grid columns
        columns:[
                {
              id: 'perfil_id',
              header: 'PerfilId',
              dataIndex: 'perfil_id',
              hidden: true,
              hideable: false
            },
            {
              id: 'modulo_id',
              header: 'ModuloId',
              dataIndex: 'modulo_id',
              hidden: true,
              hideable: false
            },
            {
              id: 'modulo_nombre',
              header: 'Modulo',
              width: 110,
              dataIndex: 'modulo_nombre',
              sortable: true
            },
            {
              id: 'accion_id',
              header: 'AccionId',
              dataIndex: 'accion_id',
              hidden: true,
              hideable: false
            },
            {
              id: 'accion_nombre',
              header: 'Acciones',
              width: 240,
              dataIndex: 'accion_nombre',
              sortable: true
            }
            ],
            dockedItems: [{
            xtype: 'toolbar',
            items: [{
                itemId: 'removeButton',
                text:'Eliminar',
                tooltip:'Elimina el item seleccionado',
                iconCls:'remove',
                disabled: true,
                handler : function(){eliminarSeleccion(gridAsignaciones);}
            }]
        }],
        title: 'Permisos asignados al perfil',
        frame: true ,
        renderTo: 'gridAsignaciones'
    });
    
});
