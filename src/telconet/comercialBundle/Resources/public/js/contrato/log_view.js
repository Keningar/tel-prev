/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function verHistorialFormaPago(contrato, strPrefijoEmpresa){

    var store = new Ext.data.Store({ 
           id:'historialFormaPagoContratoStore',
           total: 'total',
           pageSize: 10,
           autoLoad: true,
           proxy: {
               type: 'ajax',
               url : 'showLogFormaPagoContrato',
               reader: {
                   type: 'json', 
                   totalProperty: 'total', 
                   root: 'logs'
               }
           },
           fields:
                 [
                   {name:'id', mapping:'id'},
                   {name:'formaPago', mapping:'formaPago'},
                   {name:'titularCuenta', mapping:'titularCuenta'},
                   {name:'bancoTipo', mapping:'bancoTipo'},
                   {name:'bancoTipoCuenta', mapping:'bancoTipoCuenta'},
                   {name:'numeroCtaTarjeta', mapping:'numeroCtaTarjeta'},
                   {name:'feCreacion', mapping:'feCreacion'},
                   {name:'usrCreacion', mapping:'usrCreacion'},
                   {name:'strFormaPagoActual', mapping:'strFormaPagoActual'},
                   {name:'intNumeroActa', mapping:'intNumeroActa'},
                   {name:'strMotivo', mapping:'strMotivo'}, 
                   {name:'strNombreArchivoAbu', mapping:'strNombreArchivoAbu'}                  
                 ]
        });

//    var store_documento = new Ext.data.Store({ 
//           id:'historialDocumentoFormaPagoContratoStore',
//           total: 'total',
//           pageSize: 10,
//           autoLoad: true,
//           extraParams: {
//                idHistorial: 0
//            },
//           proxy: {
//               type: 'ajax',
//               url : 'getDocumentoLogFormaPagoContrato',
//               reader: {
//                   type: 'json', 
//                   totalProperty: 'total', 
//                   root: 'logs'
//               }
//           },
//           fields:
//                 [
//                   {name:'id', mapping:'id'},
//                   {name:'formaPago', mapping:'formaPago'}
//                 ]
//        });
    
    if(strPrefijoEmpresa === 'MD')    
    {   
            gridHistorialFormaPagoContrato = Ext.create('Ext.grid.Panel', {
            id: 'gridHistorialFormaPagoContrato',
            store: store,
            timeout: 60000,
            dockedItems: [ {
                    xtype: 'toolbar',
                    dock: 'top',
                    align: '->',
                    items: [
                        { xtype: 'tbfill' }
                    ]}
            ],                  
            columns:[
                    {
                      id: 'id',
                      header: 'id',
                      dataIndex: 'id',
                      hidden: true,
                      hideable: false
                    },
                    {
                      header: 'Forma de Pago',
                      dataIndex: 'formaPago',
                      width: 120
                    },
                    {
                      header: 'Titular',
                      dataIndex: 'titularCuenta',
                      width: 200
                    },
                    {
                      header: 'Banco',
                      dataIndex: 'bancoTipo',
                      width: 125
                    },
                    {
                      header: 'Tipo',
                      dataIndex: 'bancoTipoCuenta',
                      width: 100
                    },
                    {
                      header: 'Cta/Tarjeta',
                      dataIndex: 'numeroCtaTarjeta',
                      width: 100,
                      sortable: true
                    },
                    {
                      header: 'Forma Pago Actual',
                      dataIndex: 'strFormaPagoActual',
                      width: 150,
                      sortable: true
                    },
                    {
                      header: 'N\u00famero Acta',
                      dataIndex: 'intNumeroActa',
                      width: 100,
                      sortable: true
                    },
                    {
                      header: 'Nombre Archivo',
                      dataIndex: 'strNombreArchivoAbu',
                      width: 100,
                      sortable: true
                    }, 
                    {
                      header: 'Motivo',
                      dataIndex: 'strMotivo',
                      width: 100,
                      sortable: true
                    }, 
                    {
                      header: 'Fecha de modificación',
                      dataIndex: 'feCreacion',
                      width: 120,
                      sortable: true
                    },
                    {
                      header: 'Modificado por',
                      dataIndex: 'usrCreacion',
                      width: 80,
                      sortable: true
                    }
                ],
                bbar: Ext.create('Ext.PagingToolbar', {
                    store: store,
                    displayInfo: true,
                    displayMsg: 'Mostrando {0} - {1} de {2}',
                    emptyMsg: "No hay datos que mostrar." 
                })
        });
    }
    else
    {
            gridHistorialFormaPagoContrato = Ext.create('Ext.grid.Panel', {
            id: 'gridHistorialFormaPagoContrato',
            store: store,
            timeout: 60000,
            dockedItems: [ {
                    xtype: 'toolbar',
                    dock: 'top',
                    align: '->',
                    items: [
                        { xtype: 'tbfill' }
                    ]}
            ],                  
            columns:[
                    {
                      id: 'id',
                      header: 'id',
                      dataIndex: 'id',
                      hidden: true,
                      hideable: false
                    },
                    {
                      header: 'Forma de Pago',
                      dataIndex: 'formaPago',
                      width: 120
                    },
                    {
                      header: 'Titular',
                      dataIndex: 'titularCuenta',
                      width: 200
                    },
                    {
                      header: 'Banco',
                      dataIndex: 'bancoTipo',
                      width: 125
                    },
                    {
                      header: 'Tipo',
                      dataIndex: 'bancoTipoCuenta',
                      width: 100
                    },
                    {
                      header: 'Cta/Tarjeta',
                      dataIndex: 'numeroCtaTarjeta',
                      width: 100,
                      sortable: true
                    },
                    {
                      header: 'Fecha de modificación',
                      dataIndex: 'feCreacion',
                      width: 120,
                      sortable: true
                    },
                    {
                      header: 'Modificado por',
                      dataIndex: 'usrCreacion',
                      width: 80,
                      sortable: true
                    }
                ],
                bbar: Ext.create('Ext.PagingToolbar', {
                    store: store,
                    displayInfo: true,
                    displayMsg: 'Mostrando {0} - {1} de {2}',
                    emptyMsg: "No hay datos que mostrar." 
                })
        });        
    }

    
      
        
        var pop = Ext.create('Ext.window.Window', {
            title: 'Contrato: '+contrato,
            height: 400,
            width: 870,
            modal: true,
            layout:{
                type:'fit',
                align:'stretch',
                pack:'start'
            },
            floating: true,
            shadow: true,
            shadowOffset:20,
            items: [gridHistorialFormaPagoContrato] 
        });
        
        
        pop.show();
}

function verDocumentoHistorialFormaPago(contrato){
    var store = new Ext.data.Store({ 
           id:'historialFormaPagoContratoStore',
           total: 'total',
           pageSize: 10,
           autoLoad: true,
           proxy: {
               type: 'ajax',
               url : 'showLogFormaPagoContrato',
               reader: {
                   type: 'json', 
                   totalProperty: 'total', 
                   root: 'logs'
               }
           },
           fields:
                 [
                   {name:'id', mapping:'id'},
                   {name:'formaPago', mapping:'formaPago'},
                   {name:'titularCuenta', mapping:'titularCuenta'}
                 ]
        });

    
        var gridHistorialFormaPagoContrato = Ext.create('Ext.grid.Panel', {
            id: 'gridHistorialFormaPagoContrato',
            store: store,
            timeout: 60000,
            dockedItems: [ {
                    xtype: 'toolbar',
                    dock: 'top',
                    align: '->',
                    items: [
                        { xtype: 'tbfill' }
                    ]}
            ],                  
            columns:[
                    {
                      id: 'id',
                      header: 'id',
                      dataIndex: 'id',
                      hidden: true,
                      hideable: false
                    },
                    {
                      header: 'Forma de Pago',
                      dataIndex: 'formaPago',
                      width: 120
                    },
                    {
                      header: 'Titular',
                      dataIndex: 'titularCuenta',
                      width: 200
                    }
                ],
                bbar: Ext.create('Ext.PagingToolbar', {
                    store: store,
                    displayInfo: true,
                    displayMsg: 'Mostrando {0} - {1} de {2}',
                    emptyMsg: "No hay datos que mostrar." 
                })
        });

    
        
        var pop = Ext.create('Ext.window.Window', {
            title: 'Contrato: '+contrato,
            height: 400,
            width: 870,
            modal: true,
            layout:{
                type:'fit',
                align:'stretch',
                pack:'start'
            },
            floating: true,
            shadow: true,
            shadowOffset:20,
            items: [gridHistorialFormaPagoContrato] 
        });
        
        
        pop.show();
}