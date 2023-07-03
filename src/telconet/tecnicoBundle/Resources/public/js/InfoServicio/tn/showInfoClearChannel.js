function showInfoclearChannel(data,grid){
    Ext.get(grid.getId()).mask('Consultando Datos...');

    storeClearChannelWan = new Ext.data.Store({  
        pageSize: 5,
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url : morstrarClearChannelWan,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                idServicio: data.idServicio
            }
        },
        fields:
            [
              {name:'ip',           mapping:'strIp'},
              {name:'feCreacion',           mapping:'srtFechaCreacion'},
              {name:'subred',       mapping:'strSubred'},
              {name:'mascara',      mapping:'strMascara'},
              {name:'ipInicial',         mapping:'strIpInicial'},
              {name:'ipFinal',   mapping:'strIpFinal'},
              {name:'tipoIp',   mapping:'strTipoIp'},
              {name:'nombrePe',   mapping:'strNombrePe'},
              {name:'vlan',   mapping:'strVlan'}
            ]
    });

    storeClearChannelLan = new Ext.data.Store({  
        pageSize: 5,
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url : morstrarClearChannelLan,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                idServicio: data.idServicio
            }
        },
        fields:
            [
              {name:'ip',           mapping:'strIp'},
              {name:'feCreacion',           mapping:'srtFechaCreacion'},
              {name:'subred',       mapping:'strSubred'},
              {name:'mascara',      mapping:'strMascara'},
              {name:'ipInicial',         mapping:'strIpInicial'},
              {name:'ipFinal',   mapping:'strIpFinal'},
              {name:'tipoIp',   mapping:'strTipoIp'},
              {name:'gateway',   mapping:'strGateway'}
            ]
    });
    

    //grid de IPS WAN 
    gridClearChannelWan = Ext.create('Ext.grid.Panel', {
        id:             'gridClearChannelWan',
        title: 'Ip Wan',
        store:          storeClearChannelWan,
        columnLines:    true,
        frame:          true,
        autoHeight:     true,
        autoScroll:     true,
        columns: [
           {
                header: 'IP',
                dataIndex: 'ip',
                width: 100
            }, {
                header: 'Fecha Creación',
                dataIndex: 'feCreacion',
                width: 150
            }, {
                header: 'Subred',
                dataIndex: 'subred',
                width: 150
            }, {
                header: 'Mascara', 
                dataIndex: 'mascara',
                width: 150
            }, {
                header: 'Tipo',
                dataIndex: 'tipoIp',
                width: 60
            }, {
                header: 'Ip Inicial',
                dataIndex: 'ipInicial',
                width: 100
            }, {
                header: 'Ip Final',
                dataIndex: 'ipFinal',
                width: 100
            }, {
                header: 'Nombre Pe',
                dataIndex: 'nombrePe',
                width: 175
            }, {
                header: 'VLAN',
                dataIndex: 'vlan',
                width: 50
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar', {
                store: storeClearChannelWan,
                displayInfo: true,
                displayMsg: 'Mostrando {0} - {1} de {2}',
                emptyMsg: "No hay datos que mostrar."
            }),
        viewConfig: {
            stripeRows: true
        }
    });

    gridClearChannelLan = Ext.create('Ext.grid.Panel', {
        id:             'gridClearChannelLan',
        title: 'Ip Lan',
        store:          storeClearChannelLan,
        columnLines:    true,
        frame:          true,
        autoHeight:     true,
        autoScroll:     true,
        columns: [
           {
                header: 'IP Wan',
                dataIndex: 'ip',
                width: 120
            }, {
                header: 'Fecha Creación',
                dataIndex: 'feCreacion',
                width: 125
            }, {
                header: 'Subred',
                dataIndex: 'subred',
                width: 120
            }, {
                header: 'Mascara',
                dataIndex: 'mascara',
                width: 100
            },  {
                header: 'Gateway', 
                dataIndex: 'gateway',
                width: 120
            },{
                header: 'Tipo',
                dataIndex: 'tipoIp',
                width: 100
            }, {
                header: 'Ip Inicial',
                dataIndex: 'ipInicial',
                width: 120
            }, {
                header: 'Ip Final',
                dataIndex: 'ipFinal',
                width: 120
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar', {
                store: storeClearChannelLan,
                displayInfo: true,
                displayMsg: 'Mostrando {0} - {1} de {2}',
                emptyMsg: "No hay datos que mostrar."
            }),
        viewConfig: {
            stripeRows: true
        }
    });

    
    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        autoHeight:true,
        autoScroll: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side'
        },
        items: [
            gridClearChannelWan,
            gridClearChannelLan
        ],
        buttons: [{
            text: 'Cerrar',
            handler: function(){
                win.destroy();
                store.load();
            }
        }]
    });

    var win = Ext.create('Ext.window.Window', {
        title:          'Listado de IPs',
        modal:          true,
        layout :        'fit',
        closable:       true,
        autoWidth:      true,        
        autoHeight :    true,
        autoScroll :    true,
        items:          [ formPanel ]
    });
    
    storeClearChannelWan.load({
        callback: function() {
            Ext.get(grid.getId()).unmask();
            win.show();
            win.center();
        }
    });


    storeClearChannelLan.load({
        callback: function() {
            Ext.get(grid.getId()).unmask();
            win.show();
            win.center();
        }
    });
    
}