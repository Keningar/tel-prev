var idsOficinas;
var idNuevoPlan;
var valorReferido;
var message;
Ext.onReady(function() {    

    itemsOfi = [];
    
    storePlanes = new Ext.data.Store({ 
        pageSize: 10,
        total: 'total',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url : getPlanesPorEstado,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                estado: 'Activo',
                limit: '100'
            }
        },
        fields:
                  [
                    {name:'idPlan', mapping:'idPlan'},
                    {name:'nombrePlan', mapping:'nombrePlan'},
                    {name:'total', mapping:'total'}
                  ]
    });

//    var storeOficinas = new Ext.data.Store({ 
//        pageSize: 10,
//        total: 'total',
//        proxy: {
//            type: 'ajax',
//            url : 'getOficinaGrupoPorEmpresa',
//            reader: {
//                type: 'json',
//                totalProperty: 'total',
//                root: 'encontrados'
//            },
//            extraParams: {
//                plan: '',
//                estado: 'Activo'
//            }
//        },
//        fields:
//                  [
//                    {name:'idOficina', mapping:'idOficina'},
//                    {name:'nombreOficina', mapping:'nombreOficina'}
//                  ],
//        autoLoad: true,
//        listeners : {
//            load : function(t, records, options) {
//                var i = 0;
//                if (records[0].data.nombreOficina != "") {
//                    for (var i = 0; i < records.length; i++) {
//                        var cb = Ext.create('Ext.form.field.Checkbox', {
//                            boxLabel : records[i].data.nombreOficina,
//                            inputValue : records[i].data.idOficina,
//                            id : 'idOficina_' + i,
//                            name : 'oficina'
//                        });
//                        itemsOfi[i] = cb;
//                    }
//                }
//            }
//        }
//    });
//
//    var multiCombo = Ext.create('Ext.form.field.ComboBox', {
//        id: 'sltOficinas',
//        fieldLabel: 'Oficinas',
//        multiSelect: true,
//        displayField: 'nombreOficina',
//        valueField: 'idOficina',
//        width: 450,
//        labelWidth: 130,
//        store: storeOficinas,
//        queryMode: 'local'
//    });
    
    
    Ext.Ajax.request({
        url : "getOficinaGrupoPorEmpresa",
        method : 'post',
        success : function(response) {
            var variable = response.responseText;
            var r = Ext.JSON.decode(variable);
            for (var i = 0; i < r.total; i++) {
                var linea = r.encontrados[i].nombreOficina; 
                var idLinea = r.encontrados[i].idOficina; 
                itemsOfi[i] = new Ext.form.Checkbox({
                    boxLabel : linea,
                    id : 'idOficina_' + i,
                    name : 'oficina',
                    inputValue : idLinea
                });
            }
            
            var filterPanel = Ext.create('Ext.panel.Panel', {
           	 bodyPadding : 7,
                layout : 'anchor',
                buttonAlign : 'center',
                collapsible : true,
                collapsed : false,
                width : '1200px',
                title : 'Criterios de B\xfaqueda',
                   buttons: [
                       {
                           text: 'Buscar',
                           iconCls: "icon_search",
                           handler: function(){ buscar();}
                       },
                       {
                           text: 'Limpiar',
                           iconCls: "icon_limpiar",
                           handler: function(){ limpiar();}
                       }

                       ],                
                       items: [  {
                    	   xtype: 'hidden',
                    	   id: 'txtIdPlan',
                    	   value: '',
                    	   readOnly: true
                    	   },{
                                   xtype : 'fieldset',
                                   title : 'Plan Actual',
                                   width : 1010,
                                   style : 'text-align:left;',
                                   collapsible : false,
                                   collapsed : false,
                                   items : [ 
											{
											  border:false,
											  html: '<div style="width:950px;" >\n\
											               <label class="x-field x-form-item x-field-default x-table-form-item" cellpadding="0" \n\
											                      style="border-width: 0px; width: 30%; table-layout: fixed; padding:0 3% 0 0;" for="txtPlan" >Plan:</label>\n\
											               <input type="text" id="txtPlan" class="x-form-field x-form-text" style=" -moz-user-select: text; width:300px;" readonly>\n\
											               <a  href="#" onclick="buscarPlanPanel()">\n\
											                    <img src="/public/images/search.png" />\n\
											               </a>\n\
											         </div>',
											}                                            ]
                               },{
                                   xtype : 'fieldset',
                                   title : 'Oficinas',
                                   width : 1010,
                                   style : 'text-align:left;',
                                   collapsible : false,
                                   collapsed : false,
                                   items : [ {
                                       xtype : 'checkboxgroup',
                                       columns : 3,
                                       vertical : true,
                                       items : itemsOfi
                                   }, {
                                       xtype : 'panel',
                                       buttonAlign : 'right',
                                       bbar : [ {
                                           text : 'Select All',
                                           handler : function() {
                                               for (var i = 0; i < itemsOfi.length; i++) {
                                                   Ext.getCmp('idOficina_' + i).setValue(true);
                                               }
                                           }
                                       }, '-', {
                                           text : 'Deselect All',
                                           handler : function() {
                                               for (var i = 0; i < itemsOfi.length; i++) {
                                                   Ext.getCmp('idOficina_' + i).setValue(false);
                                               }
                                           }
                                       } ]
                                   } ]
                               }
                               
                               ],	
               renderTo: 'filtro'
               });
        },
        failure : function(result) {
            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
        }
    });

     
    
    
     store = new Ext.data.Store({ 
        pageSize: 1000,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'getServiciosCambioPlan',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                oficinaGrupo: '',
                plan: '',
                estado: ''
            }
        },
        fields:
                  [
                    {name:'idServicio', mapping:'servicioId'},
                    {name:'login', mapping:'login'},
                    {name:'clienteNombre', mapping:'nombrecliente'},
                    {name:'clienteId', mapping:'personaId'},
                    {name:'plan', mapping:'nombrePlan'},
                    {name:'precioVenta', mapping:'precioVenta'},
                    {name:'estado', mapping:'estadoServicio'}
                  ]
    });
    
    var pluginExpanded = true;
    
    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    })
    
    var comboCambioPlan = new Ext.form.ComboBox({
                
        xtype: 'combobox',
        id: 'sltPlanCambio',
        name: 'sltPlanCambio',
        fieldLabel: 'Plan',
        store: storePlanes,
        width: 450,
        displayField: 'nombrePlan',
        valueField: 'idPlan',
        loadingText: 'Buscando ...',
        listClass: 'x-combo-list-small',
        queryMode: 'local',
        listeners: {
            select: function(combo){
                for (var i = 0;i< storePlanes.data.items.length;i++)
                {
                    if (storePlanes.data.items[i].data.idPlan == combo.getValue())
                    {
                        Ext.getCmp('totalPlanNuevo').setValue = storePlanes.data.items[i].data.total;
                        Ext.getCmp('totalPlanNuevo').setRawValue(storePlanes.data.items[i].data.total);

                        break;
                    }
                }
            }
        }

    });
        
    gridServicios = Ext.create('Ext.grid.Panel', {
        width: 1220,
        height: 500,
        store: store,
        loadMask: true,
        frame: false,
        selModel: sm,
        iconCls: 'icon-grid',
        dockedItems: [ {
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items: [
                    //tbfill -> alinea los items siguientes a la derecha
                    { xtype: 'tbfill' },
                    comboCambioPlan,
                    {
                        xtype: 'textfield',
                        id:'totalPlanNuevo',
                        name: 'totalPlanNuevo',
                        fieldLabel: 'Precio Ref',
                        displayField: "",
                        value: "",
                        readOnly: true,
                        width: '15%'
                    },
                    {
                        iconCls: 'icon_edit',
                        text: 'Cambio Plan',
                        itemId: 'cambioPlan',
                        scope: this,
                        handler: function(){ cambioPlan();}
                    }
                ]}
        ],                  
        columns:[
                {
                  id: 'idServicio',
                  header: 'idServicio',
                  dataIndex: 'idServicio',
                  hidden: true,
                  hideable: false
                },
                {
                  header: 'Login',
                  dataIndex: 'login',
                  width: 230,
                  sortable: true
                },
                {
                  dataIndex: 'clienteNombre',
                  header: 'Cliente Nombre',
                  width: 280,
                  sortable: true
                },
                {
                  header: 'Plan',
                  dataIndex: 'plan',
                  width: 250,
                  sortable: true
                },
                {
                  header: 'Precio Venta',
                  dataIndex: 'precioVenta',
                  width: 150,
                  sortable: true
                },
                {
                  header: 'Estado',
                  dataIndex: 'estado',
                  width: 70,
                  sortable: true
                }
            ],
            bbar: Ext.create('Ext.PagingToolbar', {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'grid'
    });

});

function buscar(){  
	store.removeAll();
	$planActual = document.getElementById('txtPlan').value;
	if( $planActual != ""){
		var idsOficinasSelected = "";
	    for (var i = 0; i < itemsOfi.length; i++) {
	        oficinaSeteada = Ext.getCmp('idOficina_' + i).value;
	        if (oficinaSeteada == true) {
	            if (idsOficinasSelected != null && idsOficinasSelected == "") {
	                idsOficinasSelected = idsOficinasSelected + Ext.getCmp('idOficina_' + i).inputValue;
	            } else {
	                idsOficinasSelected = idsOficinasSelected + ",";
	                idsOficinasSelected = idsOficinasSelected + Ext.getCmp('idOficina_' + i).inputValue;
	            }
	        }
	    }
	    //...
	    idsOficinas=idsOficinasSelected;
	    
	    idPlanActual = Ext.getCmp('txtIdPlan').value;;

	    //...
	    
	    store.getProxy().extraParams.oficinaGrupo = idsOficinas;
	    store.getProxy().extraParams.plan = idPlanActual;
	    store.load();
	}else{
		 Ext.Msg.alert('Alerta','Seleccione el Plan Actual, que desea cambiar.');
	}
}

function limpiar(){
	store.removeAll();
	for (var i = 0; i < itemsOfi.length; i++) {
	        oficinaSeteada = Ext.getCmp('idOficina_' + i).setValue(false);
	    }
	
    document.getElementById('txtPlan').value = "";
    Ext.getCmp('txtIdPlan').value="";
    Ext.getCmp('totalPlanNuevo').value="";
    //...
    Ext.getCmp('sltPlanCambio').setRawValue("");
    Ext.getCmp('totalPlanNuevo').setRawValue("");
}

function cambioPlan(){
    var param = '';
    var cantidadServicios = 0;
    for(var i=0 ;  i < sm.getSelection().length ; ++i)
    {
    	cantidadServicios = cantidadServicios + 1;
      param = param + sm.getSelection()[i].data.idServicio;
      if(i < (sm.getSelection().length -1))
      {
        param = param + '|';
      }
    } 
    
    Ext.Msg.confirm('Alerta','Se procedera a CAMBIAR DE PLAN a los SERVICIOS. Desea continuar?', function(btn){
        if(btn=='yes'){
            Ext.get(gridServicios.getId()).mask('Cambiando de Plan a los Servicios...');
            Ext.Ajax.request({
                url: cambioPlanMasivo,
                method: 'post',
                timeout: 1000000,
                params: { 
                    idsServicios : param,
                    cantidadServicios: cantidadServicios,
                    idsOficinas : idsOficinas,
                    planNuevo : Ext.getCmp('sltPlanCambio').value,
                    valorPlan : Ext.getCmp('totalPlanNuevo').value    
                },
                success: function(response){
                    Ext.get(gridServicios.getId()).unmask();
                    
                    Ext.Msg.alert('Alerta',response.responseText, function(btn) {
                        if (btn == 'ok') {
                        	store.removeAll();
                        }
                    });
                },
                failure: function(result)
                {
                	 Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                }
            });
        }
    });
}

function buscarPlanPanel(){
    storeCambioPlanes = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            url : getPlanesPorEstado,
            timeout : 400000,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
                  [
                    {name:'idPlan', mapping:'idPlan'},
                    {name:'nombrePlan', mapping:'nombrePlan'},
                    {name:'valorCapacidad1', mapping:'valorCapacidad1'},
                    {name:'valorCapacidad2', mapping:'valorCapacidad2'},
                    {name:'valorCapacidad3', mapping:'valorCapacidad3'},
                    {name:'valorCapacidad4', mapping:'valorCapacidad4'},
                    {name:'estado', mapping:'estado'},
                    {name:'total', mapping:'total'}
                  ]
    });
    
    gridPlanesBusq = Ext.create('Ext.grid.Panel', {
        width: 800,
        height: 294,
        store: storeCambioPlanes,
        loadMask: true,
        frame: false,
        iconCls: 'icon-grid',                  
        columns:[
                {
                  id: 'idPlan',
                  header: 'idPlan',
                  dataIndex: 'idPlan',
                  hidden: true,
                  hideable: false
                },
                {
                  header: 'Nombre Plan',
                  dataIndex: 'nombrePlan',
                  width: 360,
                  sortable: true
                },
                {
                  header: 'Cap 1',
                  dataIndex: 'valorCapacidad1',
                  width: 50,
                  sortable: true
                },
                {
                  header: 'Cap 2',
                  dataIndex: 'valorCapacidad2',
                  width: 50,
                  sortable: true
                },
                {
                  header: 'Prom/Int 1',
                  dataIndex: 'valorCapacidad3',
                  width: 70,
                  sortable: true
                },
                {
                  header: 'Prom/Int 2',
                  dataIndex: 'valorCapacidad4',
                  width: 70,
                  sortable: true
                },
                {
                  header: 'Estado',
                  dataIndex: 'estado',
                  width: 70,
                  sortable: true
                },
                {
                    xtype: 'actioncolumn',
                    header: 'Accion',
                    width: 50,
                    items: [
                        {
                            getClass: function(v, meta, rec) {
                                  return 'button-grid-seleccionar';
                            },
                            tooltip: 'Seleccionar',
                            handler: function(grid, rowIndex, colIndex) {
                                Ext.getCmp('txtIdPlan').setValue(grid.getStore().getAt(rowIndex).data.idPlan);
                                document.getElementById('txtPlan').value = grid.getStore().getAt(rowIndex).data.nombrePlan;
                                
                                //...
                                store.removeAll();
                                //...
                                
                                win.destroy();
                            }
                        }
                    ]
                }
            ],
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeCambioPlanes,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        })
    });
    
    
    filterPanelPlanesBusq = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,  // Don't want content to crunch against the borders
        //bodyBorder: false,
        border:false,
        //border: '1,1,0,1',
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 5,
            align: 'stretch'
        },
        bodyStyle: {
            background: '#fff'
        },                     

        collapsible : true,
        collapsed: false,
        width: 800,
        title: 'Criterios de busqueda',
            buttons: [
                {
                    text: 'Buscar',
                    iconCls: "icon_search",
                    handler: function(){ buscarPlan();}
                }

            ],  //cierre buttons              
            items: [

                { width: '10%',border:false}, //inicio
                {
                    xtype: 'textfield',
                    id: 'txtNombrePlan',
                    fieldLabel: 'Nombre',
                    value: '',
                    width: '400px'
                },
                { width: '20%',border:false}, //medio
                { width: '10%',border:false},
                { width: '10%',border:false}, //final

                //-------------------------------------


            ]//cierre items
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
//                checkboxToggle: true,
//                collapsed: true,
            defaults: {
                width: 530
            },
            items: [
                filterPanelPlanesBusq,
                gridPlanesBusq
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
        title: 'Elementos',
        modal: true,
        width: 850,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function buscarPlan(){
	var nombrePlan=Ext.getCmp('txtNombrePlan').value;
	if(nombrePlan != ""){		
	    storeCambioPlanes.getProxy().extraParams.plan = Ext.getCmp('txtNombrePlan').value;
	    storeCambioPlanes.getProxy().extraParams.estado = "Todos";
	    storeCambioPlanes.load();
	}else{
		Ext.Msg.alert('Alerta',"Favor, ingrese el nombre del plan que desea buscar.");
	}
    
}