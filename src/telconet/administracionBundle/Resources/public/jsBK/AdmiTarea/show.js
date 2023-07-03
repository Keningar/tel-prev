Ext.onReady(function(){
    Ext.tip.QuickTipManager.init();
    
    var storeTareaInterfaceModeloTramoScript = new Ext.data.Store({ 
        total: 'total',
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : 'getDatosTareaInterfaceModeloTramo',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'id'},
                {name:'opcion', mapping:'opcion'},
                {name:'comboId', mapping:'comboId'},
                {name:'nombreCombo', mapping:'nombreCombo'},
                {name:'interfaceModeloId', mapping:'interfaceModeloId'},
                {name:'tipoInterfaceNombre', mapping:'tipoInterfaceNombre'},
                {name:'script', mapping:'script'}
              ]
    });
    
    
    // create the grid and specify what field you want
    // to use for the editor at each header.
    gridTareaInterfaceModeloTramo = Ext.create('Ext.grid.Panel', {
        id:'gridTareaInterfaceModeloTramo',
        store: storeTareaInterfaceModeloTramoScript,
        columnLines: true,
        columns: [Ext.create('Ext.grid.RowNumberer'),
        {
            id: 'opcion',
            header: 'Opcion',
            dataIndex: 'opcion',
            width: 100,
            sortable: true
        },{
            id: 'idCombo',
            header: 'idCombo',
            dataIndex: 'idCombo',
            hidden: true,
            hideable: false
        }, {
            id: 'nombreCombo',
            header: 'Elemento/Tramo',
            dataIndex: 'nombreCombo',
            width: 220,
            sortable: true
        },{
            id: 'interfaceModeloId',
            header: 'interfaceModeloId',
            dataIndex: 'interfaceModeloId',
            hidden: true,
            hideable: false
        }, {
            id: 'tipoInterfaceNombre',
            header: 'Nombre Interface',
            dataIndex: 'tipoInterfaceNombre',
            width: 220,
            sortable: true
        }, {
            id: 'script',
            header: 'script',
            dataIndex: 'script',
            hidden: true,
            hideable: false
        },{
            xtype: 'actioncolumn',
            header: 'Acciones',
            width: 120,
            items: [{
                getClass: function(v, meta, rec) {return 'button-grid-show'},
                tooltip: 'Ver Script',
                handler: function(grid, rowIndex, colIndex) {
                            verScript(grid.getStore().getAt(rowIndex).data);
                        }
                }
            ]
        }],        
        viewConfig:{
            stripeRows:true
        },
        width: 700,
        height: 200,
        frame: true,
        title: 'Detalle Tareas',
        renderTo: 'grid'
    });
                  
    /**************************************************/
    
});

function verScript(data){

    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side'
        },
        items: [{
            xtype: 'fieldset',
            title: 'Ver Scripting',
            defaultType: 'textfield',
            defaults: {
                width: 650
            },
            items: [
                
                {
                    xtype: 'container',
                    layout: {
                        type: 'hbox',
                        pack: 'left'
                    },
                    items: [{
                    xtype: 'textareafield',
                    id:'scripting',
                    name: 'scripting',
                    fieldLabel: 'Script',
                    value: data.script,
                    cols: 80,
                    rows: 10,
                    anchor: '100%',
                    readOnly:true
                    }]
                },

            ]
        }],
        buttons: [{
            text: 'Cerrar',
            handler: function(){
                win.destroy();
            }
        }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Ver Detalle de Scripting',
        modal: true,
        width: 730,
        closable: false,
        layout: 'fit',
        items: [formPanel]
    }).show();
            

}