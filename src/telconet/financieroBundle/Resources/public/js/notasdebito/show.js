/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

    
function editarnotadebito(id_debito,motivo,numeroPago,cantidad,valorTotal,observacion) {
   
    winDetalleDebito="";
    
   
       if(!winDetalleDebito) {		

        var form0 = Ext.widget('form', {
            layout: {
                type: 'vbox',
                align: 'stretch'
            },
            border: false,
            bodyPadding: 10,

            fieldDefaults: {
                labelAlign: 'top',
                labelWidth: 100,
                labelStyle: 'font-weight:bold'
            },
            defaults: {
                margins: '0 0 10 0'
            },
            url: url_editarnotadebito,
            items: [
             {
                xtype: 'hiddenfield',
                name: 'iddebito',
                value: id_debito
            },  
            {
                xtype: 'textfield',
                fieldLabel: 'Motivo',
                labelAlign : 'left',
                name: 'motivo',
                value:motivo,
                readOnly:true,
                width:100,
                anchor: '100%'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Cantidad',
                labelAlign : 'left',
                name: 'cantidad',
                value:cantidad,
                readOnly:true,
                width:100,
                anchor: '100%'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Valor',
                labelAlign : 'left',
                name: 'valor',
                value:valorTotal,
                readOnly:true,
                width:100,
                anchor: '100%'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Observacion',
                labelAlign : 'left',
                name: 'observacion',
                value:observacion,
                //readOnly:true,
                width:100,
                anchor: '100%'
            }
            
            ],
            buttons: [{
                text: 'Cancel',
                handler: function() {
                    this.up('form').getForm().reset();
                    this.up('window').hide();
                }
            }, {
                text: 'Grabar',
                handler: function() {
                var form1 = this.up('form').getForm();
                   // if (form1.isValid()) {
                    form1.submit({
                        waitMsg: "Procesando",
                        success: function(form1, action) {
                            
                            Ext.Msg.alert('Success', 'Se actualizaron los datos con exito');	
                            form1.reset();
                            if (store){
                                    store.load();
                            }
                        },
                        failure: function(form1, action) {
                            Ext.Msg.alert('Mensaje', 'No se actualizaron los datos');
                        }
                    });
                    this.up('window').hide();
                   //}
                }	
            }]
        });

        winDetalleDebito = Ext.widget('window', {
            title: 'Editar Deposito',
            closeAction: 'hide',
            width: 350,
            height: 300,
            minHeight: 200,
            layout: 'fit',
            resizable: true,
            modal: true,
            items: form0
        });

    }

    winDetalleDebito.show();

}


function gridNuevo(){
  // console.log(grid.headerCt.getGridColumns()[0].dataIndex);
    //console.log(grid.getGridColumns()[0]);
    
   
}

Ext.onReady(function(){     

var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
    clicksToEdit: 1
});










Ext.define('ListadoDetalleOrden', {
    extend: 'Ext.data.Model',
    fields: [
             {name:'id', type: 'string'},
	     {name:'motivo', type: 'string'},
             {name:'observacion', type: 'string'},
             {name:'valor', type: 'string'},
             {name:'valor_total', type: 'string'},
             {name:'numero_pago', type: 'string'},
            ]
}); 

 store = Ext.create('Ext.data.Store', {
    // destroy the store if the grid is destroyed
    autoDestroy: true,
    model: 'ListadoDetalleOrden',
    proxy: {
        type: 'ajax',
        // load remote data using HTTP
        url: url_listar_informacion_existente,
        reader: {
            type: 'json',
            root: 'listadoInformacion'
            // records will have a 'plant' tag
        },
        extraParams:{facturaid:factura_id},
        simpleSortMode: true               
    },
});

store.load();








// create the grid and specify what field you want
// to use for the editor at each header.
 
grid = Ext.create('Ext.grid.Panel', {
        store:store,
        columns: [new Ext.grid.RowNumberer(), 
            
        {
            text: 'id',
            name:'id',
            width: 200,
            dataIndex: 'id',
            hidden: true
        },    
        {
            text: 'Motivo',
            name:'motivo',
            width: 200,
            dataIndex: 'motivo'
        },{
            text: 'No. Pago Aplicado',
             name:'numero_pago',
            dataIndex: 'numero_pago',
            width: 150			
        },{
            text: 'Observacion',
            id: 'observacion',
            name:'observacion',
            dataIndex: 'observacion',
            width: 280
        },{
            text: 'Cantidad',
              name:'valor',
            dataIndex: 'valor',
            align: 'right',
            width: 70			
        },{
            text: 'Valor',
            dataIndex: 'valor_total',
            align: 'right',
            width: 70			
        }],
        selModel: {
            selType: 'cellmodel'
        },
        renderTo: 'listado_detalle_nota_debito',
        width: 850,
        height: 200,
        title: 'Detalle de nota de debito',
        frame: true,
        plugins: [cellEditing]
        
    });
    


    
    function renderAcciones(value, p, record) {
         var iconos='';
        var bandera=(typeof record.data.id === 'undefined') ? false :(record.data.id != 'undefined' ? true : false);
       // if(record.data.id != "undefined" ) { 
        if(bandera ) { 
             var permiso = $("#ROLE_71-1067");
              var boolPermiso1 = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);	
               if(boolPermiso1){
          iconos=iconos+'<b><a href="#" onClick="editarnotadebito('+record.data.id+',\''+record.data.motivo+'\',\''+record.data.numero_pago+'\',\''+record.data.valor+'\',\''+record.data.valor_total+'\',\''+record.data.observacion+'\')" \n\title="Editar ND" class="button-grid-edit"></a></b>'; 
                }
        }
         return Ext.String.format(
               iconos,
                value
          );
              
    }
    

    
    
    
    
    
    
});
