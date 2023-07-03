Ext.onReady(function() {     
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

    gridIntegrantes = Ext.create('Ext.grid.Panel', {
        width: 320,
        height: 800,
        store: storeIntegrantes,
        loadMask: true,
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