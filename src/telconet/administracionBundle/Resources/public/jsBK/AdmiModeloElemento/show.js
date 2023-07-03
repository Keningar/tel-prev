/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


Ext.onReady(function(){
    Ext.tip.QuickTipManager.init(); 

    var storeInterfaceModelo = new Ext.data.Store({ 
        total: 'total',
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : 'getInterfacesModelo',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'idInterfaceModelo', mapping:'idInterfaceModelo'},
                {name:'tipoInterfaceId', mapping:'tipoInterfaceId'},
                {name:'nombreTipoInterface', mapping:'nombreTipoInterface'},
                {name:'claseInterface', mapping:'claseInterface'},
                {name:'cantidadInterface', mapping:'cantidadInterface'},
                {name:'formatoInterface', mapping:'formatoInterface'},
                {name:'caracteristicasInterface', mapping:'caracteristicasInterface'}
              ]
    });
    
    gridInterfacesModelo = Ext.create('Ext.grid.Panel', {
        id:'gridInterfacesModelo',
        store: storeInterfaceModelo,
        columnLines: true,
        columns: [Ext.create('Ext.grid.RowNumberer'),
        {
            id: 'idInterfaceModelo',
            header: 'idInterfaceModelo',
            dataIndex: 'idInterfaceModelo',
            hidden: true,
            hideable: false
        },{
            id: 'tipoInterfaceId',
            header: 'tipoInterfaceId',
            dataIndex: 'tipoInterfaceId',
            hidden: true,
            hideable: false
        }, {
            id: 'nombreTipoInterface',
            header: 'Tipo Interface',
            dataIndex: 'nombreTipoInterface',
            width: 150,
            sortable: true
        }, {
            id: 'claseInterface',
            header: 'Clase Interface',
            dataIndex: 'claseInterface',
            width: 120,
            sortable: true
        },{
            id: 'cantidadInterface',
            header: 'Cantidad Interface',
            dataIndex: 'cantidadInterface',
            width: 120,
            sortable: true
        },{
            id: 'formatoInterface',
            header: 'Formato Interface',
            dataIndex: 'formatoInterface',
            width: 150,
            sortable: true
        }, {
            id: 'caracteristicasInterface',
            header: 'caracteristicasInterface',
            dataIndex: 'caracteristicasInterface',
            hidden: true,
            hideable: false
        },{
            xtype: 'actioncolumn',
            header: 'Acciones',
            width: 120,
            items: [{
                getClass: function(v, meta, rec) {return 'button-grid-show'},
                tooltip: 'Ver Caracteristicas',
                handler: function(grid, rowIndex, colIndex) {
                            verCaracteristicas(grid.getStore().getAt(rowIndex).data);
                        }
                }
            ]
        }],        
        viewConfig:{
            stripeRows:true
        },
        width: 850,
        height: 200,
        frame: true,
        title: 'Detalle Tareas',
        renderTo: 'grid'
    });
    
    //-------------------------------------------------------------------------------
    var storeModeloUsuarioAcceso = new Ext.data.Store({ 
        total: 'total',
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : 'getModeloUsuariosAcceso',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'idUsuarioAcceso', mapping:'idUsuarioAcceso'},
                {name:'nombreUsuarioAcceso', mapping:'nombreUsuarioAcceso'},
                {name:'esPreferenciaUsuario', mapping:'esPreferenciaUsuario'}
              ]
    });
    
    gridModeloUsuariosAcceso = Ext.create('Ext.grid.Panel', {
        id:'gridModeloUsuariosAcceso',
        store: storeModeloUsuarioAcceso,
        columnLines: true,
        columns: [Ext.create('Ext.grid.RowNumberer'),
        {
            id: 'idUsuarioAcceso',
            header: 'idUsuarioAcceso',
            dataIndex: 'idUsuarioAcceso',
            hidden: true,
            hideable: false
        },{
            id: 'nombreUsuarioAcceso',
            header: 'Usuario',
            dataIndex: 'nombreUsuarioAcceso',
            width: 100,
            sortable: true
        }, {
            id: 'esPreferenciaUsuario',
            header: 'Es Preferencia',
            dataIndex: 'esPreferenciaUsuario',
            width: 100,
            sortable: true
        }],        
        viewConfig:{
            stripeRows:true
        },
        width: 425,
        height: 200,
        frame: true,
        title: 'Usuarios',
        renderTo: 'gridUsuarios'
    });
    
    //-------------------------------------------------------------------------------
    var storeModeloProtocolos = new Ext.data.Store({ 
        total: 'total',
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : 'getModeloProtocolos',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'idProtocolo', mapping:'idProtocolo'},
                {name:'nombreProtocolo', mapping:'nombreProtocolo'},
                {name:'esPreferenciaProtocolo', mapping:'esPreferenciaProtocolo'}
              ]
    });
    
    gridModeloProtocolos = Ext.create('Ext.grid.Panel', {
        id:'gridModeloProtocolos',
        store: storeModeloProtocolos,
        columnLines: true,
        columns: [Ext.create('Ext.grid.RowNumberer'),
        {
            id: 'idProtocolo',
            header: 'idProtocolo',
            dataIndex: 'idProtocolo',
            hidden: true,
            hideable: false
        },{
            id: 'nombreProtocolo',
            header: 'Protocolo',
            dataIndex: 'nombreProtocolo',
            width: 100,
            sortable: true
        }, {
            id: 'esPreferenciaProtocolo',
            header: 'Es Preferencia',
            dataIndex: 'esPreferenciaProtocolo',
            width: 100,
            sortable: true
        }],        
        viewConfig:{
            stripeRows:true
        },
        width: 425,
        height: 200,
        frame: true,
        title: 'Protocolos',
        renderTo: 'gridProtocolos'
    });
    
    //-------------------------------------------------------------------------------
    var storeModeloTecnologias = new Ext.data.Store({ 
        total: 'total',
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : 'getModeloTecnologias',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'idTecnologia', mapping:'idTecnologia'},
                {name:'nombreTecnologia', mapping:'nombreTecnologia'}
              ]
    });
    
    gridModeloTecnologias = Ext.create('Ext.grid.Panel', {
        id:'gridModeloTecnologias',
        store: storeModeloTecnologias,
        columnLines: true,
        columns: [Ext.create('Ext.grid.RowNumberer'),
        {
            id: 'idTecnologia',
            header: 'idTecnologia',
            dataIndex: 'idTecnologia',
            hidden: true,
            hideable: false
        },{
            id: 'nombreTecnologia',
            header: 'Tecnologia',
            dataIndex: 'nombreTecnologia',
            width: 100,
            sortable: true
        }],        
        viewConfig:{
            stripeRows:true
        },
        width: 425,
        height: 200,
        frame: true,
        title: 'Tecnologias',
        renderTo: 'gridTecnologias'
    });
    
    //-------------------------------------------------------------------------------
    var storeDetallesModelo = new Ext.data.Store({ 
        total: 'total',
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : 'getDetallesModelo',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'idDetalleModelo', mapping:'idDetalleModelo'},
                {name:'idDetalle', mapping:'idDetalle'},
                {name:'nombreDetalle', mapping:'nombreDetalle'}
              ]
    });
    
    gridDetallesModelo = Ext.create('Ext.grid.Panel', {
        id:'gridDetallesModelo',
        store: storeDetallesModelo,
        columnLines: true,
        columns: [Ext.create('Ext.grid.RowNumberer'),
        {
            id: 'idDetalle',
            header: 'idDetalle',
            dataIndex: 'idDetalle',
            hidden: true,
            hideable: false
        },{
            id: 'nombreDetalle',
            header: 'Caracteristica',
            dataIndex: 'nombreDetalle',
            width: 250,
            sortable: true
        }],        
        viewConfig:{
            stripeRows:true
        },
        width: 425,
        height: 200,
        frame: true,
        title: 'Caracteristicas Modelo',
        renderTo: 'gridDetallesModelo'
    });
    
});

function verCaracteristicas(data){
    Ext.define('Detalle', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'idDetalle', mapping:'idDetalle'},
            {name:'nombreDetalle', mapping:'nombreDetalle'}
        ]
    });

    storeDetalles = Ext.create('Ext.data.Store', {
        // destroy the store if the grid is destroyed
        pageSize: 100,
        autoLoad: true,
        model: 'Detalle',
        data: Ext.JSON.decode(data.caracteristicasInterface),
        proxy: {
            type: 'memory',

            // specify a XmlReader (coincides with the XML format of the returned data)
            reader: {
                type: 'json',
                totalProperty: 'total',
                // records will have a 'plant' tag
                root: 'detalles'
            }
        }
    });

    gridDetalles = Ext.create('Ext.grid.Panel', {
        id:'gridDetalles',
        store: storeDetalles,
        columnLines: true,
        columns: [Ext.create('Ext.grid.RowNumberer'),
        {
            id: 'idDetalle',
            header: 'idDetalle',
            dataIndex: 'idDetalle',
            hidden: true,
            hideable: false
        },{
            id: 'nombreDetalle',
            header: 'Caracteristica',
            dataIndex: 'nombreDetalle',
            width: 150,
            sortable: true
        }],   
        buttons: [{
            text: 'Cerrar',
            handler: function(){
                win.destroy();
            }
        }],
        viewConfig:{
            stripeRows:true
        },
        width: 700,
        height: 200,
        frame: true,
        title: 'Detalle Caracteristicas',
        renderTo: 'grid'
    });
    
    var win = Ext.create('Ext.window.Window', {
        title: 'Ver Caracteristicas',
        modal: true,
        width: 260,
        closable: false,
        layout: 'fit',
        items: [gridDetalles]
    }).show();
}