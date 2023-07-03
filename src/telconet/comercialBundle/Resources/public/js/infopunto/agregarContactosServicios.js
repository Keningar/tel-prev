var winListaAddContactos;

function showAgregarContactos(idServicio,descripcionServicio){
//alert(idServicio);
arregloSeleccionados= new Array();
winListaAddContactos="";

if (!winListaAddContactos)
{
    Ext.define('ListaDetalleModel', {
        extend: 'Ext.data.Model',
        fields: [{name:'idPersonaContacto', type: 'int'},
                 {name:'idPersona', type: 'int'},
                 {name:'nombres', type: 'string'},
                 {name:'apellidos', type: 'string'},
                 {name:'estado', type: 'string'},
                 {name:'ingresado', type: 'boolean'}
		]
    });
    var storeServicios = Ext.create('Ext.data.JsonStore', {
        model: 'ListaDetalleModel',
        proxy: {
            type: 'ajax',
            url: url_contactos_cliente,
            reader: {
                type: 'json',
                root: 'contactos',
                totalProperty: 'total'
            }
        }
    });
    storeServicios.load({params: {idserv: idServicio}});    

                 var sm = new Ext.selection.CheckboxModel( {
                    listeners:{
                        selectionchange: function(selectionModel, selected, options){
                            
                            Ext.each(selected, function(record){
                                    arregloSeleccionados.push(record.data.idPersonaContacto);
                    });			
                            //console.log('A selection change has occurred');
                            //console.log(arregloSeleccionados);

                        },
                       select: function( selectionModel, record, index, eOpts ){
                           //console.log('selected:'+index);
                           if(record.data.ingresado){
                                sm.deselect(index);
                                //Ext.Msg.alert('Alerta','Este contacto ya fue asignado al servicio '+descripcionServicio);
                            }
                            
                       } 
                    }
                });

    var listView = Ext.create('Ext.grid.Panel', {
        width:500,
        height:300,
        collapsible:false,
        title: '',
        selModel: sm,
        renderTo: Ext.getBody(),
        store: storeServicios,
        multiSelect: false,
        viewConfig: {
            emptyText: 'No hay datos para mostrar'
        },

        columns: [new Ext.grid.RowNumberer(),  
                    {
                        text: 'Nombres',
                        width: 160,
                        dataIndex: 'nombres'
                    },{
                        text: 'Apellidos',
                        width: 160,
                        dataIndex: 'apellidos'
                    },{
                        text: 'Estado',
                        dataIndex: 'estado',
                        align: 'right',
                        width: 105
                    }                 
		],

listeners:{

    },
  viewConfig: {
        getRowClass: function(record, index) {
            var c = record.get('ingresado');
            //console.log(c);
            if (c) {
                return 'greenTextGrid';
            } else{
                return 'blackTextGrid';
            }
        }
	  }    
                
                
    });

            var formContactos = Ext.widget('form', {
                layout: {
                    type: 'vbox',
                    align: 'stretch'
                },
                border: false,
                bodyPadding: 10,
                url: url_grabaContactos,
                fieldDefaults: {
                    labelAlign: 'top',
                    labelWidth: 100,
                    labelStyle: 'font-weight:bold'
                },
                defaults: {
                    margins: '0 0 10 0'
                },
                items: [			
			listView,			    
			{
			xtype: 'hiddenfield',
			id: 'contactos',
                        name: 'contactos',
                        value: ''
			},
			{
			xtype: 'hiddenfield',
			id: 'servicio',
                        name: 'servicio',
                        value: idServicio
			}			
		],
                buttons: [{
                    text: 'Cancel',
                    handler: function() {
                        this.up('form').getForm().reset();
                        this.up('window').hide();
                    }
                }, {
                    text: 'Grabar',
                    handler: function() {

			var form1 = this.up('form').getForm();
                        if (form1.isValid()) {
			//form1.url=url_grabaServicios+"?lista="+js_array_to_php_array(arregloSeleccionados);
                        Ext.getCmp('servicio').setValue(idServicio);
			Ext.getCmp('contactos').setValue(arregloSeleccionados);
                        if (Ext.getCmp('contactos').getValue())
                        {    
                            form1.submit({
                                        waitMsg: "Procesando",
                                        success: function(form1, action) {	

                                                                    Ext.Msg.alert('Success', 'Los datos fueron ingresados con exito');	
                                                                    //HAGO LOAD DE LA CONSULTA DE TICKETS PARA REFRESCAR DESHABILITAR LOS ICONOS
                                                                    store.load({params: {start: 0, limit: 10}});
                                        },
                                        failure: function(form1, action) {
                                            Ext.Msg.alert('Failed', 'Error al ingresar los datos, por favor comunicarse con el departamento de Sistemas');
                                        }
                            });	
                        }
                        else{
                            Ext.Msg.alert('Failed', 'Seleccione al menos 1 contacto para asignar.');
                        }			
			this.up('window').hide();
                        }
                    }	
                }]
            });


	winListaAddContactos = Ext.widget('window', {
                title: 'Contactos',
                closeAction: 'hide',
                width: 510,
                height:380,
                minHeight: 380,
                layout: 'fit',
                resizable: false,
                modal: true,
		closabled: false,
                items: [formContactos]
    });
	
}
winListaAddContactos.show();

function cierraVentana(){
    winListaAddContactos.close();
    
}
}

