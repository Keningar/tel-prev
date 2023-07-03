Ext.require([
    '*'
]);


function winCampos(idVariable,idFormato) {

winDetalle="";
if(!winDetalle) {

    Ext.define('detallesModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idFormato', type: 'int'},
            {name: 'variable'},
            {name: 'idVariable', type: 'string'},
            {name: 'valor', type: 'string'}
        ]
    });

    Ext.define('camposModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id', type: 'string'},
            {name: 'descripcion', type: 'string'}
        ]
    });
   
    // create the Data Store
    var store = Ext.create('Ext.data.Store', {
        autoDestroy: true,
        model: 'detallesModel',
        proxy: {
            type: 'memory',
            reader: {
                type: 'json',
                root: 'validaciones'
            }
        }
    });

    // create the Data Store
    var storeCampos = Ext.create('Ext.data.Store', {
        autoDestroy: true,
        model: 'camposModel',
        proxy: {
            type: 'ajax',
            url: url_campos,
            reader: {
                type: 'json',
                root: 'campos'
            },
            extraParams:{variable:idVariable}
        }
    });
    
    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 2
    });

    gridValidaciones = Ext.create('Ext.grid.Panel', {
        store: store,
        columns: [ {
            text:'Variable',    
            dataIndex: 'variable',
            width: 150,
            editor: new Ext.form.field.ComboBox({   
                typeAhead: true,
                triggerAction: 'all',
                selectOnTab: true,
                id:'id',
                name: 'variable',
				valueField:'descripcion',
                displayField:'descripcion',                
                store: storeCampos,
                lazyRender: true,
                listClass: 'x-combo-list-small',
				listeners:{
					select: function(combo, rec, idx){
						//console.log(rec[0].data.id);
						store.first().data.idFormato=idFormato;
						store.first().data.idVariable=rec[0].data.id;
						//console.log(store.first().data);
					}
				}
            })
        }, {
            text: 'Valor',
            dataIndex: 'valor',
            width: 400,
            align: 'right',
            editor: {
                width:'80%',
                xtype: 'textfield',
                allowBlank: false
            }
        },{
            xtype: 'actioncolumn',
            width:45,
            sortable: false,
            items: [{
                iconCls:"button-grid-delete",
                tooltip: 'Borrar Forma Contacto',
                handler: function(grid, rowIndex, colIndex) {
                    store.removeAt(rowIndex); 
                }
            }]
        }],
        selModel: {
            selType: 'cellmodel'
        },
        width: 600,
        height: 300,
        title: '',
        tbar: [{
            text: 'Agregar',
            handler : function(){
                // Create a model instance
                var r = Ext.create('detallesModel', {
                    idFormato: '',
                    variable: '',
					idVariable: '',
                    valor: ''
                });
                store.insert(0, r);
                cellEditing.startEditByPosition({row: 0, column: 0});
                
            }
        }],
        plugins: [cellEditing]
    });



        var form = Ext.widget('form', {
            layout: {
                type: 'vbox',
                align: 'stretch'
            },
            border: false,
            bodyPadding: 10,

            fieldDefaults: {
                labelAlign: 'top',
                labelWidth: 130,
                labelStyle: 'font-weight:bold'
            },
            defaults: {
                margins: '0 0 10 0'
            },
            url: url_grabar,
            items: [
			{
				xtype: 'hiddenfield',
				id: 'detalles',
				name: 'detalles',
				value: '',
				
				},	
				gridValidaciones
            ],
            buttons: [{
                text: 'Cancel',
                handler: function() {
                    this.up('form').getForm().reset();
                    this.up('window').destroy();
                }
            }, {
                text: 'Grabar',
                name: 'grabar',
                handler: function() {
                var form1 = this.up('form').getForm();
					//verifica si existe validaciones ingresadas
                    if (getValidaciones()) {
					
                    form1.submit({
                        waitMsg: "Procesando",
                        success: function(form1, action) {
                            Ext.Msg.alert('Success', 'Los datos fueron ingresados con exito');	
                            if (storeDetalle){
                                    storeDetalle.load();
                            }
                            form1.reset();
                            form1.destroy();
                            winDetalle.destroy();
                        },
                        failure: function(form1, action) {
                            console.log(action.result.errors.error);                            
                            Ext.Msg.alert('Failed', 'Error al ingresar los datos, por favor comunicarse con el departamento de Sistemas');
                            form1.reset();
                            form1.destroy();
                            winDetalle.destroy();
                        }
                    });
                   }
                   else{
                       Ext.Msg.alert('Failed', 'Falta ingresar datos.');
                   }
                }	
            }]
        });	
	
	
	
}


winDetalle = Ext.widget('window', {
	title: 'Ingresar Datos de Validacion',
	closeAction: 'hide',
	closable: false,
	width: 700,
	height: 380,
	minHeight: 200,
	layout: 'fit',
	resizable: true,
	modal: true,
	items: form
});

winDetalle.show();

}


function getValidaciones(){
            var array_data = new Array();
            var variable='';
            for(var i=0; i < gridValidaciones.getStore().getCount(); i++){ 
                variable=gridValidaciones.getStore().getAt(i).data;
                for(var key in variable) {
                    var valor = variable[key];
                    array_data.push(valor);
                } 
                //console.log(array_data);
            } 
			Ext.getCmp('detalles').setValue(array_data);

            if ((Ext.getCmp('detalles').getValue()=='0,,') || (Ext.getCmp('detalles').getValue()=='')) {
                alert('Debe ingresar al menos 1 validacion');
                Ext.getCmp('detalles').setValue('');
				return false;
            }
			else
			{
				return true;
			}
}