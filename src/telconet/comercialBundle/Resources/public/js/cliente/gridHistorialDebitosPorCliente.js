Ext.require([
    'Ext.grid.*',
    'Ext.data.*',
    'Ext.form.field.Number',
    'Ext.form.field.Date',
    'Ext.tip.QuickTipManager'
]);
function verDebitosPorCliente(idPer){
    var storeHistorial = new Ext.data.Store({ 
        storeId: 'storeHistorial',
        groupField: 'id',
        pageSize: 10,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url : url_historial_debitos_cliente,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'pagos'
            },
            extraParams: {
                idPer: idPer
            }
        },
        fields:
            [
              {name:'id', mapping:'id'},
              {name:'usuarioCreacion', mapping:'usuarioCreacion'},
              {name:'fechaCreacion', mapping:'fechaCreacion'},
              {name:'total', mapping:'total'},
              {name:'estado', mapping:'estado'},
              {name:'observacionRechazo', mapping:'observacionRechazo'},
              {name:'banco', mapping:'banco'},
              {name:'fechaProceso', mapping:'fechaProceso'}
            ],
        listeners: { 
            groupchange: function(store, groupers) {
                var grouped = storeHistorial.isGrouped(),
                    groupBy = groupers.items[0] ? groupers.items[0].property : '',
                    toggleMenuItems, len, i = 0;

                gridHistorialDebitos.down('[text=Desagrupar Debitos]').setDisabled(!grouped);
                
            }
        }
    });
    
    var groupingFeature = Ext.create('Ext.grid.feature.Grouping', {
            groupHeaderTpl: '{columnName}: {name} ({rows.length} Item{[values.rows.length > 1 ? "s" : ""]})',
            hideGroupedHeader: true,
            startCollapsed: true,
            id: 'debitosGrouping'
        }),
        groups = storeHistorial.getGroups(),
        len = groups.length, i = 0,
        toggleMenu = [],
        toggleGroup = function(item) {
            var groupName = item.text;
            if (item.checked) {
                groupingFeature.expand(groupName, true);
            } else {
                groupingFeature.collapse(groupName, true);
            }
        };
        
    for (; i < len; i++) {
        toggleMenu[i] = {
            xtype: 'menucheckitem',
            text: groups[i].id,
            handler: toggleGroup
        }
    }    
    
    //grid de usuarios
    gridHistorialDebitos = Ext.create('Ext.grid.Panel', {
        id:'gridHistorialServicio',
        collapsible: true,
        iconCls: 'icon-grid',
        frame: true,
        store: storeHistorial,
        width: 900,
        height: 450,
        title: 'Historial Debitos',
        resizable: true,
        features: [groupingFeature],      
        columnLines: true,
        
        columns: [
                      {
            header: 'Id Debito General',
            dataIndex: 'id',
            width: 100
        },
            {
            header: 'Fecha Proceso',
            dataIndex: 'fechaProceso',
            width: 100
        },
            {
            header: 'Usuario Proceso',
            dataIndex: 'usuarioCreacion',
            width: 90,
            sortable: true
        },
        {
            header: 'Valor',
            dataIndex: 'total',
            width: 90
        },
        {
            header: 'Estado',
            dataIndex: 'estado',
            width: 80
        },
        {
            header: 'Banco',
            dataIndex: 'banco',
            width: 190
        },
        {
            header: 'Motivo Rechazo',
            dataIndex: 'observacionRechazo',
            width: 235
        }],
      
        fbar  : ['->', {
            text:'Desagrupar Debitos',
            iconCls: 'icon-clear-group',
            handler : function() {
                groupingFeature.disable();
            }
        }],
          bbar: Ext.create('Ext.PagingToolbar', {
          store: storeHistorial,
          displayInfo: true,
          displayMsg: 'Mostrando {0} - {1} de {2}'
        }) 
    });
    
    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side'
        },
        items: [
        
        {
            xtype: 'fieldset',
            title: '',
            defaultType: 'textfield',
            defaults: {
                width: 900
            },
            items: [

                gridHistorialDebitos

            ]
        }//cierre interfaces cpe
        ],
        buttons: [{
            text: 'Cerrar',
            handler: function(){
                win.destroy();
            }
        }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: '',
        modal: true,
        width: 950,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}


