/**
* 'verHistorial'.
*
* Obtiene el historial de un punto del cliente y arma el grid
* @return grid con historial del punto.
*
* @author John Vera <javera@telconet.ec>
* @version 1.0 07-10-2014
*/


function verHistorialPunto(idPunto){
    var storeHistorial = new Ext.data.Store({  
        pageSize: 50,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url : url_ver_historial_puntos,
            reader: {
                type: 'json',
                observacionProperty: 'observacion',
                root: 'data'
            },
            extraParams: {
                idPunto: idPunto
            }
        },
        fields:
            [
              {name:'accion', mapping:'accion'},
              {name:'valor', mapping:'valor'},
              {name:'user', mapping:'user'},
              {name:'fecha', mapping:'fecha'},
              {name:'ip', mapping:'ip'}
            ]
    });

    gridHistorial = Ext.create('Ext.grid.Panel', {
        id:'gridHistorialServicio',
        store: storeHistorial,
        columnLines: true,
        columns: [
        {
            header: 'Acción',
            dataIndex: 'accion',
            width: '15%',
            sortable: true
        },
        {
            header: 'Valor',
            dataIndex: 'valor',
            width: '50%'
        },
        {
            header: 'Usuario',
            dataIndex: 'user',
            width: '9%'
        },
        {
            header: 'Fecha',
            dataIndex: 'fecha',
            width: '15%'
        },
        {
            header: 'Ip',
            dataIndex: 'ip',
            width: '10%'
        }
        ],
        viewConfig:{
            stripeRows:true
        },

        frame: true,
        height: 200

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

                gridHistorial

            ]
        }
        ],
        buttons: [{
            text: 'Cerrar',
            handler: function(){
                win.destroy();
            }
        }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Historial del punto',
        modal: true,
        width: 950,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}