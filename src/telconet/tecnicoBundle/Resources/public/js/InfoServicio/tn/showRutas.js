function showRutas(data,grid){
    
    Ext.get(grid.getId()).mask('Consultando Datos...');
    
    storeRutas = new Ext.data.Store({  
        pageSize: 5,
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url : mostrarRutas,
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
              {name:'id',           mapping:'idRutaElemento'},
              {name:'nombreRuta',   mapping:'nombreRutaElemento'},
              {name:'ip',           mapping:'ip'},
              {name:'subred',       mapping:'subred'},
              {name:'mascara',      mapping:'mascara'},
              {name:'tipo',         mapping:'tipo'},
              {name:'feCreacion',   mapping:'feCreacion'}
            ]
    });
    
    Ext.define('Rutas', {
        extend: 'Ext.data.Model',
        fields: [
              {name:'id',           mapping:'idRutaElemento'},
              {name:'nombreRuta',   mapping:'nombreRutaElemento'},
              {name:'ip',           mapping:'ip'},
              {name:'subred',       mapping:'subred'},
              {name:'mascara',      mapping:'mascara'},
              {name:'tipo',         mapping:'tipo'},
              {name:'feCreacion',   mapping:'feCreacion'}
        ]
    });
    
    //grid de usuarios
    gridRutas = Ext.create('Ext.grid.Panel', {
        id:             'gridRutas',
        store:          storeRutas,
        columnLines:    true,
        frame:          true,
        autoHeight:     true,
        autoScroll:     true,
        columns: [
            {
                header: 'Nombre Ruta',
                dataIndex: 'nombreRuta',
                width: 150
            },{
                header: 'IP',
                dataIndex: 'ip',
                width: 100
            }, {
                header: 'Subred',
                dataIndex: 'subred',
                width: 100
            }, {
                header: 'Mascara',
                dataIndex: 'mascara',
                width: 100
            }, {
                header: 'Tipo',
                dataIndex: 'tipo',
                width: 100
            }, {
                header: 'Fecha Creación',
                dataIndex: 'feCreacion',
                width: 150
            },
            {
                xtype: 'actioncolumn',
                header: 'Accion',
                width: 50,
                items: [
                    {
                        getClass: function(v, meta, rec) {
                            if (rec.get('tipo') == "Ruta Dinámica")
                            {
                                return 'button-grid-invisible';  
                            }
                            else
                            {
                                return 'button-grid-delete';
                            }
                        },
                        tooltip: 'Eliminar Ruta',
                        handler: function(grid, rowIndex, colIndex) {
                            Ext.Msg.show({
                                title: 'Confirmar',
                                msg: 'Está seguro de eliminar la ruta?',
                                buttons: Ext.Msg.YESNO,
                                icon: Ext.MessageBox.QUESTION,
                                buttonText: {
                                    yes: 'si', no: 'no'
                                },
                                fn: function(btn) {
                                    if (btn === 'yes') {
                                        Ext.MessageBox.wait('Eliminando Ruta...');

                                        Ext.Ajax.request({
                                            url: eliminarRuta,
                                            method: 'post',
                                            timeout: 400000,
                                            params: {
                                                idServicio:     data.idServicio,
                                                idProducto:     data.productoId,
                                                idElemento:         data.elementoId,
                                                vrf:            data.vrf,
                                                idRuta:         storeRutas.getAt(rowIndex).data.id
                                            },
                                            success: function(response) {
                                                Ext.MessageBox.hide();
                                                win.destroy();

                                                Ext.Msg.alert('Mensaje', response.responseText, function(btn) {
                                                    if (btn === 'ok') {
                                                        store.load();
                                                        win.destroy();
                                                    }
                                                });
                                            },
                                            failure: function(response)
                                            {
                                                Ext.MessageBox.show({
                                                    title: 'Error',
                                                    msg: response.responseText,
                                                    buttons: Ext.MessageBox.OK,
                                                    icon: Ext.MessageBox.ERROR
                                                });
                                            }
                                        });
                                    }
                                }
                            });
                        }
                    }
                ]
            }],
        bbar: Ext.create('Ext.PagingToolbar', {
                store: storeRutas,
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
            gridRutas
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
        title:          'Listado de Enrutamiento',
        modal:          true,
        layout :        'fit',
        closable:       true,
        autoWidth:      true,        
        autoHeight :    true,
        autoScroll :    true,
        items:          [ formPanel ]
    });
    
    storeRutas.load({
        callback: function() {
            Ext.get(grid.getId()).unmask();
            win.show();
            win.center();
        }
    });
    
}