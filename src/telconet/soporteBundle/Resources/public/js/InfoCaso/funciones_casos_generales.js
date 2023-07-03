function verCasosAperturados(data)
{
    //Store que obtiene los casos aperturados
    var storeCasosAperturados = new Ext.data.Store(
    {
        pageSize : 1000,
        total    : 'total',
        async    : false,
        autoLoad : true,
        fields   : [
            {name: 'numeroCaso'      , mapping: 'numeroCaso'},
            {name: 'clienteAfectado' , mapping: 'clienteAfectado'},
            {name: 'loginAfectado'   , mapping: 'loginAfectado'},
            {name: 'fechaApertura'   , mapping: 'fechaApertura'},
            {name: 'estado'          , mapping: 'estado'},
            {name: 'nivelCriticidad' , mapping: 'nivelCriticidad'},
            {name: 'tipoCaso'        , mapping: 'tipoCaso'}
        ],
        proxy : {
            type   : 'ajax',
            url    : url_getCasosAperturados,
            reader : {
                type          : 'json',
                totalProperty : 'total',
                root          : 'casos'
            },
            extraParams: {
                idCaso : data.idCaso
            }
        }
    });

    //Grid Casos Aperturados
    var gridCasosAperturados = Ext.create('Ext.grid.Panel',
    {
        id          : 'gridCasosAperturados',
        width       : 900,
        height      : 240,
        store       : storeCasosAperturados,
        collapsible : false,
        loadMask    : true,
        frame       : true,
        forceFit    : true,
        autoRender  : true,
        enableColumnResize :false,
        listeners: {
            itemdblclick: function(view,record,item,index,eventobj,obj){
                var position = view.getPositionByEvent(eventobj),
                data = record.data,
                value = data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title:'Copiar texto?',
                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    buttons: Ext.Msg.OK,
                    icon: Ext.Msg.INFORMATION
                });
            },
            viewready: function(grid) {
                var view = grid.view;
                grid.mon(view, {
                    uievent: function(type, view, cell, recordIndex, cellIndex, e) {
                        grid.cellIndex   = cellIndex;
                        grid.recordIndex = recordIndex;
                    }
                });

                grid.tip = Ext.create('Ext.tip.ToolTip', {
                    target: view.el,
                    delegate: '.x-grid-cell',
                    trackMouse: true,
                    renderTo: Ext.getBody(),
                    listeners: {
                        beforeshow: function(tip) {
                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1) {
                                header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                tip.update(grid.getStore().getAt(grid.recordIndex).get(header.dataIndex));
                            }
                        }
                    }
                });
            }
        },
        viewConfig: {
            enableTextSelection : true,
            stripeRows          : true,
            emptyText           : 'Sin Casos Aperturados'
        },
        columnLines: true,
        columns:
            [
                new Ext.grid.RowNumberer(),
                {
                    id        : 'numeroCaso',
                    dataIndex : 'numeroCaso',
                    header    : "<b>Número de Caso</b>",
                    align     : 'center',
                    width     : 100
                },
                {
                    id        : 'clienteAfectado',
                    dataIndex : 'clienteAfectado',
                    header    : '<b>Cliente</b>',
                    align     : 'center',
                    width     : 140
                },
                {
                    id        : 'loginAfectado',
                    dataIndex : 'loginAfectado',
                    header    : '<b>Login</b>',
                    align     : 'center',
                    width     : 120
                },
                {
                    id        : 'fechaApertura',
                    dataIndex : 'fechaApertura',
                    header    : '<b>Fecha de Apertura</b>',
                    align     : 'center',
                    width     : 110
                },
                {
                    id        : 'estado',
                    dataIndex : 'estado',
                    header    : '<b>Estado</b>',
                    align     : 'center',
                    width     : 80
                },
                {
                    id        : 'nivelCriticidad',
                    dataIndex : 'nivelCriticidad',
                    header    : '<b>Nivel de Criticidad</b>',
                    align     : 'center',
                    width     : 100
                },
                {
                    id        : 'tipoCaso',
                    dataIndex : 'tipoCaso',
                    header    : '<b>Tipo de Caso</b>',
                    align     : 'center',
                    width     : 95
                }
            ]
    });

    //Panel de Casos Aperturados
    var formPanelCasosAperturados = Ext.create('Ext.form.Panel',
    {
        bodyPadding   : 5,
        waitMsgTarget : true,
        fieldDefaults : {
                labelAlign: 'left',
                labelWidth: 200,
                msgTarget: 'side'
        },
        items:
        [
            {
                xtype : 'fieldset',
                title : '&nbsp;<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b style="color:blue";>Casos Aperturados</b>',
                items : [
                    gridCasosAperturados
                ]
            },
        ],
        buttons:
        [
            {
                text: 'Cerrar',
                handler: function() {
                    winCasosAperturados.destroy();
                }
            }
        ]
    });

    var winCasosAperturados = Ext.create('Ext.window.Window',{
        modal    : true,
        closable : false,
        width    : 950,
        layout   : 'fit',
        items    : [formPanelCasosAperturados]
    }).show();
}
