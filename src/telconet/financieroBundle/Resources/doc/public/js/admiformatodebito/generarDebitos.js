            Ext.require([
                '*',
                'Ext.tip.QuickTipManager',
                    'Ext.window.MessageBox'
            ]);

            var itemsPerPage = 100;
            var store='';
            var estado_id='';
            var area_id='';
            var login_id='';
            var tipo_asignacion='';
            var pto_sucursal='';
            var idClienteSucursalSesion;

            Ext.onReady(function(){

                Ext.define('ListaDetalleModel', {
                    extend: 'Ext.data.Model',
                    fields: [{name:'id', type: 'int'},
                            {name:'banco', type: 'string'},
                            {name:'tipoCuentaTarjeta', type: 'string'}
                ]
                }); 


                store = Ext.create('Ext.data.JsonStore', {
                    model: 'ListaDetalleModel',
                            pageSize: itemsPerPage,
                    proxy: {
                        type: 'ajax',
                        url: url_grid,
                        reader: {
                            type: 'json',
                            root: 'detalles'
                        },
                        extraParams:{fechaDesde:'',fechaHasta:'', estado:'',nombre:''},
                        simpleSortMode: true
                    },
                    listeners: {
                    }
                });

                store.load({params: {start: 0, limit: 100}});    



                 sm = new Ext.selection.CheckboxModel( {
                    listeners:{
                        selectionchange: function(selectionModel, selected, options){
                            arregloSeleccionados= new Array();
                            Ext.each(selected, function(record){
                                    //arregloSeleccionados.push(record.data.idOsDet);
                    });			
                            //console.log(arregloSeleccionados);

                        }
                    }
                });



                var listView = Ext.create('Ext.grid.Panel', {
                    width:800,
                    height:330,
                    collapsible:false,
                    title: '',
                    selModel: sm,
                    dockedItems: [ {
                                    xtype: 'toolbar',
                                    dock: 'top',
                                    align: '->',
                                    items: []}],                     
                    renderTo: Ext.get('lista'),
                    // paging bar on the bottom
                    bbar: Ext.create('Ext.PagingToolbar', {
                        store: store,
                        displayInfo: true,
                        displayMsg: 'Mostrando clientes {0} - {1} of {2}',
                        emptyMsg: "No hay datos para mostrar"
                    }),	
                    store: store,
                    multiSelect: false,
                    viewConfig: {
                        emptyText: 'No hay datos para mostrar'
                    },
                    columns: [new Ext.grid.RowNumberer(),  
                            {
                        text: 'Banco',
                        width: 250,
                        dataIndex: 'banco'
                    },{
                        text: 'Tipo Cta/Tarjeta',
                        width: 500,
                        dataIndex: 'tipoCuentaTarjeta'
                    }
                    ]
                });            				
});


            function procesar(){
                var param = '';
                if(sm.getSelection().length > 0)
                {
                  var estado = 0;
                  for(var i=0 ;  i < sm.getSelection().length ; ++i)
                  {
                    param = param + sm.getSelection()[i].data.id;

                    if(i < (sm.getSelection().length -1))
                    {
                      param = param + '|';
                    }
                  }      

				Ext.Msg.confirm('Alerta','Se creara los archivos de debito con los bancos seleccionados. Desea continuar?', function(btn){
					if(btn=='yes'){
						$('#debitos').val(param);
						//console.log($('#debitos').val());
						document.forms[0].submit();
					}
				});


                }
                else
                {
                  alert('Seleccione por lo menos un registro de la lista');
                }
            }