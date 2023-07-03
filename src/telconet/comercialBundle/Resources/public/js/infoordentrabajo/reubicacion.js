Ext.require([
                '*',
                'Ext.tip.QuickTipManager',
                    'Ext.window.MessageBox'
            ]);

            var itemsPerPage = 10;
            var store='';
            var area_id='';
            var login_id='';
            var tipo_asignacion='';
            var pto_sucursal='';
            var idClienteSucursalSesion;

            Ext.onReady(function(){
                
            //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		
            DTFechaDesde = new Ext.form.DateField({
                    id: 'fechaDesde',
                    fieldLabel: 'Desde',
                    labelAlign : 'left',
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    width:325,
                    //anchor : '65%',
                    //layout: 'anchor'
            });
            DTFechaHasta = new Ext.form.DateField({
                    id: 'fechaHasta',
                    fieldLabel: 'Hasta',
                    labelAlign : 'left',
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    width:325,
                    //anchor : '65%',
                    //layout: 'anchor'
            });
                
                Ext.define('ListaDetalleModel', {
                    extend: 'Ext.data.Model',
                    fields: [{name:'id', type: 'string'}, 
							{name:'descripcion', type: 'string'},
                            {name:'cantidad', type: 'string'},
                            {name:'estado', type: 'string'},
                            {name:'precio', type: 'string'},
                            ]
                }); 


                store = Ext.create('Ext.data.JsonStore', {
                    model: 'ListaDetalleModel',
                            pageSize: itemsPerPage,
                    proxy: {
                        type: 'ajax',
                        url: url_store,
                        reader: {
                            type: 'json',
                            root: 'listado',
                            totalProperty: 'total'
                        },
                        extraParams:{fechaDesde:'',fechaHasta:'',estado:'Activo'},
                        simpleSortMode: true
                    },
                    listeners: {
                        beforeload: function(store){
				store.getProxy().extraParams.fechaDesde= Ext.getCmp('fechaDesde').getValue();
				store.getProxy().extraParams.fechaHasta= Ext.getCmp('fechaHasta').getValue();
                        },
                        load: function(store){
                            store.each(function(record) {
                                //idClienteSucursalSesion = record.data.idClienteSucursalSesion;
                            });
                        }
                    }
                });

                store.load({params: {start: 0, limit: 10}});    



                sm = new Ext.selection.CheckboxModel( {
                    listeners:{
                        selectionchange: function(selectionModel, selected, options){
                            arregloSeleccionados= new Array();
                            Ext.each(selected, function(record){
                                    //arregloSeleccionados.push(record.data.idOsDet);
                    });			
                            //console.log(arregloSeleccionados);

                        }
                    }
                });


                listView = Ext.create('Ext.grid.Panel', {
                    // width:400,
                    // height:275,
                    collapsible:false,
                    title: '',
					hidden: true,
                    selModel: sm,
                    dockedItems: [ {
                                    xtype: 'toolbar',
                                    dock: 'top',
                                    align: '->',
                                    items: [
                                        //tbfill -> alinea los items siguientes a la derecha
                                        { xtype: 'tbfill' },
					, {
                                        iconCls: 'icon_crear',
                                        text: 'Guardar OT',
                                        disabled: false,
                                        itemId: 'crear',
                                        scope: this,
                                        handler: function(){aprobarAlgunos()}
                                    }]}],                    
                    renderTo: Ext.get('lista_servicios'),
                    // paging bar on the bottom
                    bbar: Ext.create('Ext.PagingToolbar', {
                        store: store,
                        displayInfo: true,
                        displayMsg: 'Mostrando prospectos {0} - {1} of {2}',
                        emptyMsg: "No hay datos para mostrar"
                    }),	
                    store: store,
                    multiSelect: false,
                    viewConfig: {
                        emptyText: 'No hay datos para mostrar'
                    },
                    columns: [new Ext.grid.RowNumberer(),  
                            {
                        text: 'Servicio',
                        width: 650,
                        dataIndex: 'descripcion',
						align: 'center',
                    // },{
                        // text: 'Cantidad',
                        // width: 110,
                        // dataIndex: 'cantidad'
                    // },{
                        // text: 'Precio',
                        // width: 130,
                        // dataIndex: 'precio'
                    },{
                        text: 'Estado',
                        dataIndex: 'estado',
                        align: 'center',
                        width: 173			
                    }]
                });            
            
});

function Buscar(){

				store.load({params: {start: 0, limit: 10}});
	
	}



function aprobarAlgunos(){
var param = '';
if(sm.getSelection().length > 0)
{
  var estado = 0;
  var estadosActivos = 0;
  for(var i=0 ;  i < sm.getSelection().length ; ++i)
  {
    param = param + sm.getSelection()[i].data.id;

    if(sm.getSelection()[i].data.estado == 'Eliminado')
    {
      estado = estado + 1;
    }
    if(sm.getSelection()[i].data.estado == 'Activo')
    {
      estadosActivos = estadosActivos + 1;
    }
    if(i < (sm.getSelection().length -1))
    {
      param = param + '|';
    }
  }      
  if(estado == 0 && estadosActivos>0)
  {

	Ext.MessageBox.show({
		icon: Ext.Msg.INFO,
		// width:500,
		// height: 300,
		title:'Mensaje del Sistema',
		msg: 'Esta Seguro(a) de Crear la Orden de Trabajo?',
		 buttons    : Ext.MessageBox.YESNO,
		// buttonText: {yes: "Ok"},
		fn: function(btn){
			if(btn=='yes'){ 
				Ext.MessageBox.wait("Creando Orden de Trabajo", 'Mensaje');
				Ext.Ajax.request({
					url: url_crear,
					method: 'post',
					params: { servicios : param},
					success: function(response){
						 Ext.MessageBox.hide();
						var text = response.responseText;
						Ext.Msg.alert('Mensaje', text, function(btn){
							if(btn=='ok'){
								location.reload(); 
							}
						});
					},
					failure: function(result)
					{
						Ext.Msg.alert('Error ','Error: ' + result.statusText);
					}
				});
			}
		}
    });

  }
  else
  {
	if(estado > 0){
	    alert('Por lo menos uno de las registro se encuentra en estado ELIMINADO');
	}
	if(estadosActivos == 0){
	    alert('Para Realizar la Reubicacion debe tener por lo menos un Servicio Activo');
	}
  }
}
else
{
  alert('Seleccione por lo menos un Servicio de la lista');
}
}

function limpiar(){
    Ext.getCmp('fechaDesde').setRawValue("");
    Ext.getCmp('fechaHasta').setRawValue("");  
}

function presentarOcultarServicios(obj){
	if(obj.value=='R'){
		document.getElementById('orden_nueva').style.display = 'none';
		document.getElementById('telconet_schemabundle_infoordentrabajotype_ultimaMillaId').parentNode.style.display = "none";
		listView.setVisible(true);
	}else{
		document.getElementById('orden_nueva').removeAttribute('style');
		document.getElementById('telconet_schemabundle_infoordentrabajotype_ultimaMillaId').parentNode.removeAttribute('style');
		listView.setVisible(false);
	}
}
