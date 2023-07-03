 
Ext.onReady(function() { 
    var store = new Ext.data.Store({ 
           id:'verDocumentosCaducadosDigitalesStore',
           total: 'total',
           pageSize: 10,
           autoLoad: true,
           proxy: {
               type: 'ajax',                
               url: url_showDocumentosCaducadosPersonaEmpleado,               
               reader: {
                   type: 'json', 
                   totalProperty: 'total', 
                   root: 'docs'
               }
           },
           fields:
                 [
                   {name:'id', mapping:'id'},                                      
                   {name:'ubicacionLogicaDocumento', mapping:'ubicacionLogicaDocumento'},
                   {name:'tipoDocumentoGeneral', mapping:'tipoDocumentoGeneral'},
                   {name:'estadoCaducidad', mapping:'estadoCaducidad'},
                   {name:'feCaducidad', mapping:'feCaducidad'},
                   {name:'usrCreacion', mapping:'usrCreacion'},
                   {name:'linkVerDocumento', mapping: 'linkVerDocumento'}
                 ]
        });
        

        store.on('load', function () {
        var countDocsCaducados = store.getCount();
        //console.log(count);
          
            if(countDocsCaducados>0)
            {
                var gridDocumentosDigitalesCaducadosPersonaEmpleado = Ext.create('Ext.grid.Panel', {
                              id: 'gridDocumentosDigitalesCaducadosPersonaEmpleado',
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
                                        header: 'Archivo Digital',
                                        dataIndex: 'ubicacionLogicaDocumento',
                                        width: 300
                                      },
                                      {
                                        header: 'Tipo Documento',
                                        dataIndex: 'tipoDocumentoGeneral',
                                        width: 150
                                      },
                                      {
                                        header: 'Caducidad',
                                        dataIndex: 'estadoCaducidad',
                                        width: 160,
                                        sortable: true
                                      },
                                      {
                                        header: 'Fecha de Caducidad',
                                        dataIndex: 'feCaducidad',
                                        width: 160,
                                        sortable: true
                                      },
                                      {
                                        header: 'Creado por',
                                        dataIndex: 'usrCreacion',
                                        width: 80,
                                        sortable: true
                                      },
                                      {
                                text: 'Acciones',
                                width: 80,
                                renderer: renderAcciones,
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
                            title: 'Archivos Digitales Caducados',
                            height: 400,
                            width: 800,
                            modal: true,
                            layout:{
                                type:'fit',
                                align:'stretch',
                                pack:'start'
                            },
                            floating: true,
                            shadow: true,
                            shadowOffset:20,
                            items: [gridDocumentosDigitalesCaducadosPersonaEmpleado] 
                        });
                    pop.show();
            }
    });
});


function renderAcciones(value, p, record) 
{
            var iconos='';
            iconos=iconos+'<b><a href="'+record.data.linkVerDocumento+'" onClick="" title="Ver Archivo Digital" class="button-grid-show"></a></b>';	                                       
            return Ext.String.format(
                            iconos,
                value,
                '1',
                            'nada'
            );
}
