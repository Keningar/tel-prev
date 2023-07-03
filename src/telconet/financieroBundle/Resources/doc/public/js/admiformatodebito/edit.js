Ext.require([
    '*'
]);

Ext.onReady(function(){
    Ext.define('FormatoDetModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id', type: 'string'},
            {name: 'descripcion', type: 'string'},
            {name: 'longitud', type:'string'},
            {name: 'caracterRelleno', type: 'string'},
            {name: 'tipoCampo', type:'string'},
			{name: 'tipoCampoId', type:'string'},			
            {name: 'contenido', type: 'string'},
            {name: 'orientacionCaracter', type: 'string'},
	     {name: 'variable', type: 'string'},
		 {name: 'variableId', type: 'string'},
	     {name: 'tieneValidacion', type: 'string'},
			{name: 'requiereValidacion', type: 'string'},
			{name: 'posicion', type: 'int'},
			{name: 'tipoDato', type: 'string'},
			{name: 'tipoDatoId', type: 'string'},
			{name: 'caracterRellenoId', type: 'string'}			
        ]
    });
    
    storeDetalle = Ext.create('Ext.data.Store', {
        autoDestroy: true,
        model: 'FormatoDetModel',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: url_grid,
            reader: {
                type: 'json',
                root: 'detalles'
            }             
        },
		sortOnLoad : true,
		sorters : {
			property : 'posicion',
			direction : 'ASC'
		}
    });

    grid = Ext.create('Ext.grid.Panel', {
        store: storeDetalle,
        columns: [  
		{
            text: 'Pos',
            dataIndex: 'posicion',
            width: 30,
            align: 'right'
        },
		{
            text: 'Descripcion',
            dataIndex: 'descripcion',
            width: 130,
            align: 'right'
        },
		{
            text: 'Dato',
            dataIndex: 'tipoDato',
            width: 80,
            align: 'right'
        }, {
            text: 'Long',
            dataIndex: 'longitud',
            width: 35,
            align: 'right'
        }, {
            text: 'Relleno',
            dataIndex: 'caracterRelleno',
            width: 105,
            align: 'right'
        }, {
            text: 'Tipo',
            dataIndex: 'tipoCampo',
            width: 60,
            align: 'right'
        }, {
            text: 'Contenido',
            dataIndex: 'contenido',
            width: 120,
            align: 'right'
        }, {
            text: 'Variable',
            dataIndex: 'variable',
            width: 120,
            align: 'right'
        }, {
            text: 'Alineacion',
            dataIndex: 'orientacionCaracter',
            width: 70,
            align: 'right'
        }, {
            text: 'Validacion',
            dataIndex: 'requiereValidacion',
            width: 58,
            align: 'right'
        },{
            xtype: 'actioncolumn',
            width:40,
            sortable: false,
            items: [{
                iconCls:"button-grid-delete",
                tooltip: 'Borrar detalle de formato',
                handler: function(grid, rowIndex, colIndex) {
                    storeDetalle.removeAt(rowIndex); 
                }
            }]
        }
		],
        selModel: {
            selType: 'cellmodel'
        },
        renderTo: Ext.get('lista_detalles'),
        width: 850,
        height: 500,
        title: ''
    });
});