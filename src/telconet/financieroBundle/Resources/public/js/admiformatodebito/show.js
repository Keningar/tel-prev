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
            {name: 'tipoCampo', type: 'string'},
            {name: 'contenido', type: 'string'},
            {name: 'orientacionCaracter', type: 'string'},
	     {name: 'variable', type: 'string'},
		 {name: 'variableId', type: 'string'},
	     {name: 'tieneValidacion', type: 'string'},
		 {name: 'posicion', type: 'int'},
		 {name: 'requiereValidacion', type: 'string'},
		 {name: 'tipoDato', type: 'string'},
		 {name: 'tipoDatoId', type: 'string'}		 
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
            width: 27,
            align: 'right'
        },
		{
            text: 'Descripcion',
            dataIndex: 'descripcion',
            width: 130,
            align: 'right'
        }, {
            text: 'Dato',
            dataIndex: 'tipoDato',
            width: 75,
            align: 'right'
        }, {
            text: 'Long',
            dataIndex: 'longitud',
            width: 40,
            align: 'right'
        }, {
            text: 'Relleno',
            dataIndex: 'caracterRelleno',
            width: 105,
            align: 'right'
        }, {
            text: 'Tipo',
            dataIndex: 'tipoCampo',
            width: 65,
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
            text: 'Alineado',
            dataIndex: 'orientacionCaracter',
            width: 60,
            align: 'right'
        }, {
            text: 'Validacion',
            dataIndex: 'requiereValidacion',
            width: 61,
            align: 'right'
        },{
                        text: 'Acciones',
                        width: 46,
                        renderer: renderAcciones
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
            function renderAcciones(value, p, record) {
                    var iconos='';
                    if ((record.data.requiereValidacion=='S')&&(record.data.variable)&&(record.data.tieneValidacion == 'N')){
                        iconos=iconos+'<b><a href="#" onClick="winCampos('+record.data.variableId+','+record.data.id+')" title="Ingresar Validaciones"  class="button-grid-edit"></a></b>';
                    }
		      else
		      {
			if((record.data.requiereValidacion=='S')&&(record.data.variable)&&(record.data.tieneValidacion == 'S'))
			   iconos=iconos+'<b><a href="#" onClick="" title="Ver Validaciones"  class="button-grid-show"></a></b>';
		      }
                    return Ext.String.format(
                                    iconos,
                        value
                    );
            }
});