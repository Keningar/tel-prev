Ext.require([
    '*'
]);


Ext.onReady(function(){

        Ext.define('BancosList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_banco', type:'int'},
            {name:'descripcion_banco', type:'string'}
        ]
    });
    storeBancos = Ext.create('Ext.data.Store', {
            model: 'BancosList',
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url : url_lista_bancos,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            }
    });
    combo_bancos = new Ext.form.ComboBox({
            id: 'cmb_bancos',
            name: 'cmb_bancos',
            fieldLabel: false,
            anchor: '100%',
            width: 200,
            emptyText: 'Seleccione Banco',
            store:storeBancos,
            displayField: 'descripcion_banco',
            valueField: 'id_banco',
            renderTo: 'combo_banco',
            listeners:{
                select:{fn:function(combo, value) {
                    Ext.getCmp('cmb_tipocuenta').reset();  
                    //Ext.getCmp('cmb_accion').reset();  
                    $('#admiformatodebitoextratype_bancoId').val(combo.getValue());
                    storeTipoCuenta.proxy.extraParams = {id_banco: combo.getValue()};
                    storeTipoCuenta.load({params: {}});

                }}
            }
    });    



     Ext.define('TiposCuentaList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_cuenta', type:'int'},
            {name:'descripcion_cuenta', type:'string'}
        ]
    });
    
     storeTipoCuenta = Ext.create('Ext.data.Store', {
            model: 'TiposCuentaList',
            proxy: {
                type: 'ajax',
                url : url_lista_tipos_cuenta,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            }
    });
    
    combo_tipocuenta = new Ext.form.ComboBox({
            id: 'cmb_tipocuenta',
            name: 'cmb_tipocuenta',
            fieldLabel: false,
            anchor: '100%',
            queryMode:'local',
            width: 200,
            emptyText: 'Seleccione tipo cuenta',
            store: storeTipoCuenta,
            displayField: 'descripcion_cuenta',
            valueField: 'id_cuenta',
            renderTo: 'combo_tipo_cuenta',
            listeners:{
                select:{fn:function(combo, value) {
                        $('#admiformatodebitoextratype_tipoCuentaId').val(combo.getValue());
                }}
            }
    });    



    
    Ext.define('DetModel', {
        extend: 'Ext.data.Model',
        fields: [
            // the 'name' below matches the tag name to read, except 'availDate'
            // which is mapped to the tag 'availability'
            {name: 'descripcion', type: 'string'},
            {name: 'longitud', type: 'string'},
            {name: 'caracterRelleno', type:'string'},
            {name: 'tipoCampo', type:'string'},
			{name: 'tipoCampoId', type:'string'},
            {name: 'contenido', type: 'string'},
            {name: 'orientacionCaracter', type: 'string'},
            {name: 'variable', type: 'string'},
			{name: 'variableId', type: 'string'},
			{name: 'orientacionCaracterId', type: 'string'},
			{name: 'requiereValidacion', type: 'string'},
			{name: 'posicion', type: 'int'},
			{name: 'tipoDato', type: 'string'},
			{name: 'tipoDatoId', type: 'string'},
			{name: 'caracterRellenoId', type:'string'}			
        ]
    });
    
    // create the Data Store
    storeDetalle = Ext.create('Ext.data.Store', {
        // destroy the store if the grid is destroyed
        autoDestroy: true,
        model: 'DetModel',
        proxy: {
            type: 'memory',
            reader: {
                type: 'json',
                root: 'personaFormasContacto',
                totalProperty: 'total'
            }             
        },
		sortOnLoad : true,
		sorters : {
			property : 'posicion',
			direction : 'ASC'
		}       
    });


    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 2
    });

    // create the grid and specify what field you want
    // to use for the editor at each header.
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
            width: 70,
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
            width: 50,
            align: 'right'
        }, {
            text: 'Contenido',
            dataIndex: 'contenido',
            width: 120,
            align: 'right'
        }, {
            text: 'Variable',
            dataIndex: 'variable',
            width: 105,
            align: 'right'
        }, {
            text: 'Alineado',
            dataIndex: 'orientacionCaracter',
            width: 95,
            align: 'right'
        }, {
            text: 'Validacion',
            dataIndex: 'requiereValidacion',
            width: 65,
            align: 'right'
        },{
            xtype: 'actioncolumn',
            width:40,
            sortable: false,
            items: [{
                iconCls:"button-grid-delete",
                tooltip: 'Borrar Linea',
                handler: function(grid, rowIndex, colIndex) {
                    storeDetalle.removeAt(rowIndex); 
                    //calculaTotal();
                }
            }]
        }
		
		],
        selModel: {
            selType: 'cellmodel'
        },
        renderTo: Ext.get('lista_grid'),
        width: 850,
        height: 500,
        title: '',
        plugins: [cellEditing]
    });

	function renderAcciones(value, p, record) {
			var iconos='';
			if(record.variable)
				iconos=iconos+'<b><a href="#" onClick="agregarValidaciones()" title="Ver" class="button-grid-show"></a></b>';
			return Ext.String.format(
							iconos,
				value
			);
	}		
});



