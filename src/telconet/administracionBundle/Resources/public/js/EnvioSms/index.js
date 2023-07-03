Ext.require([
	'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

var connEsperaAccion = new Ext.data.Connection
({
	listeners:
        {
            'beforerequest': 
            {
                fn: function (con, opt)
                {						
                    Ext.MessageBox.show
                    ({
                       msg: 'Grabando los datos, Por favor espere!!',
                       progressText: 'Saving...',
                       width:300,
                       wait:true,
                       waitConfig: {interval:200}
                    });
                },
                scope: this
            },
            'requestcomplete':
            {
                fn: function (con, res, opt)
                {
                    Ext.MessageBox.hide();
                },
                scope: this
            },
            'requestexception': 
            {
                fn: function (con, res, opt)
                {
                    Ext.MessageBox.hide();
                },
                scope: this
            }
	}
});
	
Ext.onReady(function() { 
    Ext.tip.QuickTipManager.init();
         
    Ext.define('ModelStore', {
        extend: 'Ext.data.Model',
        fields:
		[				
			{name:'idParametroDetEnvioSMS', mapping:'idParametroDetEnvioSMS'},
			{name:'descripcionProducto',    mapping:'descripcionProducto'},
			{name:'nombreTecnicoProducto',  mapping:'nombreTecnicoProducto'},
			{name:'permiteEnvioSMS',        mapping:'permiteEnvioSMS'},
			{name:'activaEnvioSMS',         mapping:'activaEnvioSMS'},
			{name:'desactivaEnvioSMS',      mapping:'desactivaEnvioSMS'}
		],
        idProperty: 'idParametroDetEnvioSMS'
    });
	
    store = new Ext.data.Store({ 
        pageSize: 10,
        model: 'ModelStore',
        total: 'total',
        proxy: {
            type: 'ajax',
			timeout: 600000,
            url : strUrlGridEnvioSMSPorProducto,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        autoLoad: true
    });
   
    grid = Ext.create('Ext.grid.Panel', {
        id : 'grid',
        title: 'Listado de productos',
        width: 800,
        height: 350,
        store: store,
        plugins: [{ptype : 'pagingselectpersist'}],
        viewConfig: {
			enableTextSelection: true,
            id: 'gv',
            trackOver: true,
            stripeRows: true,
            loadMask: true
        },
        columns:
        [
                {
                  id: 'descripcionProducto',
                  header: 'Descripción',
                  dataIndex: 'descripcionProducto',
                  width: 300,
                  sortable: true
                },
                {
                  id: 'nombreTecnicoProducto',
                  header: 'Nombre técnico',
                  dataIndex: 'nombreTecnicoProducto',
                  width: 150,
                  sortable: true
                },
                {
                  header: 'Permite Envío SMS',
                  dataIndex: 'permiteEnvioSMS',
                  width: 150,
                  sortable: true
                },
                {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: 120,
                    items: [
                        {
                            getClass: function(v, meta, rec) 
                            {
                                var strClassButton = 'button-grid-activarEnvioSms';
                                if(rec.get('activaEnvioSMS') === 'SI')
                                {
                                    this.items[0].tooltip = 'Activar Envío SMS';
                                }
                                else
                                {
                                    strClassButton          = "";
                                    this.items[0].tooltip   = '';
                                }
                                return strClassButton;
                            },
                            tooltip: 'Activar Envío SMS',
                            handler: function(grid, rowIndex, colIndex) 
                            {
                                var rec = store.getAt(rowIndex);
                                var arrayParametros                 = [];
                                arrayParametros['descripcionProducto']          = rec.get('descripcionProducto');
                                arrayParametros['nombreTecnicoProducto']        = rec.get('nombreTecnicoProducto');
                                arrayParametros['observacionAccionAEjecutar']   = 'activará';
                                arrayParametros['observacionAccionEjecutada']   = 'activó';
                                arrayParametros['accionAEjecutar']              = 'activar';
                                actualizaEnvioSMSPorProducto(arrayParametros);
                            }
                        },
						{
                            getClass: function(v, meta, rec) 
                            {
                                var strClassButton = 'button-grid-desactivarEnvioSms';
                                if(rec.get('desactivaEnvioSMS') === 'SI')
                                {
                                    this.items[0].tooltip = 'Desactivar Envío SMS';
                                }
                                else
                                {
                                    strClassButton          = "";
                                    this.items[0].tooltip   = '';
                                }
                                return strClassButton;
                            },
                            tooltip: 'Desactivar Envío SMS',
                            handler: function(grid, rowIndex, colIndex) 
                            {
                                var rec = store.getAt(rowIndex);
                                var arrayParametros                 = [];
                                arrayParametros['descripcionProducto']          = rec.get('descripcionProducto');
                                arrayParametros['nombreTecnicoProducto']        = rec.get('nombreTecnicoProducto');
                                arrayParametros['observacionAccionAEjecutar']   = 'desactivará';
                                arrayParametros['observacionAccionEjecutada']   = 'desactivó';
                                arrayParametros['accionAEjecutar']              = 'desactivar';
                                actualizaEnvioSMSPorProducto(arrayParametros);
                            }
                        }
                    ]
                }
            ],
            bbar: Ext.create('Ext.PagingToolbar', {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'grid'
    });
    
});


function actualizaEnvioSMSPorProducto(arrayParametros)
{
    Ext.Msg.confirm('Alerta','Se '+arrayParametros['observacionAccionAEjecutar']+' el envío de SMS para el producto '
                             +arrayParametros['descripcionProducto']+'. Desea continuar?', function(btn)
    {
        if(btn=='yes')
        {
            connEsperaAccion.request
            ({
                url: strUrlActualizaEnvioSMSPorProducto,
                method: 'post',
                dataType: 'json',
                params:
                { 
                    nombreTecnicoProducto   : arrayParametros['nombreTecnicoProducto'],
                    accionAEjecutar         : arrayParametros['accionAEjecutar']
                },
                success: function(result)
                {
                    if( "OK" == result.responseText )
                    {
                        Ext.Msg.alert('Información', 'Se '+arrayParametros['observacionAccionEjecutada']+' el envío de SMS del producto '
                        + arrayParametros['descripcionProducto']+ ' de manera correcta');
                    }
                    else
                    {
                        Ext.Msg.alert('Error ', result.responseText);
                    }
                    store.load();
                },
                failure: function(result)
                {
                    Ext.Msg.alert('Error ','Error: ' + result.statusText);
                }
            });
        }
    });
}