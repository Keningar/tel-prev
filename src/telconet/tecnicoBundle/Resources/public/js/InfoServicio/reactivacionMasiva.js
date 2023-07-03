/*************************
 * @author jlafuente
 * @since 2014-01-01
 * @date 2014-02-11
 */
var valorMontoDeuda;
var idsOficinas;
var fechaCorteDesde;
var fechaCorteHasta;
// ...
var message;
Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();

    // itemsFor = [];
    itemsOfi = [];

    var itemsPerPage = 1000;
    store = new Ext.data.Store({
        pageSize : itemsPerPage,
        total : 'total',
        proxy : {
            type : 'ajax',
            timeout : 400000,
            url : getServiciosAReactivar,
            reader : {
                type : 'json',
                totalProperty : 'total',
                root : 'encontrados'
            },
            extraParams : {
                fechaCorteDesde : '',
                fechaCorteHasta : '',
                valorMontoDeuda : '',
                idsOficinas : '',
                estado : ''
            }
        },listeners: {
            load: function(store, records, success) {
                if (message != null) {
                    Ext.MessageBox.hide();
                }
            }
        },
        fields : [ {
            name : 'idPunto',
            mapping : 'puntoId'
        }, {
            name : 'login',
            mapping : 'login'
        }, {
            name : 'clienteNombre',
            mapping : 'nombrecliente'
        }, {
            name : 'clienteId',
            mapping : 'id'
        }, {
            name : 'oficina',
            mapping : 'nombreOficina'
        }, {
            name : 'cartera',
            mapping : 'saldo'
        }, {
            name : 'formaPago',
            mapping : 'descripcionFormaPago'
        }, {
            name : 'estado',
            mapping : 'estado'
        },{
            name : 'ultimaMilla',
            mapping : 'ultimaMilla'
        }        
        ]
    });
        
        
    //ultima milla 
    var storeUltimaMilla = new Ext.data.Store({ 
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : getUltimaMilla,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'registros'
            }
        },
        fields:
                  [
                      {name:'nombreUltimaMilla', mapping:'nombreUltimaMilla'}
                  ],
        autoLoad: true
    });
    
    Ext.Ajax.request({
        url : "getOficinaGrupoConFormaPagoReactivar",
        method : 'post',
        success : function(response) {

            var variable = response.responseText.split("&");
            var oficinaGrupo = variable[0];
            var formaPago = variable[1];

            var r = Ext.JSON.decode(oficinaGrupo);
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

            // var form = Ext.JSON.decode(formaPago);
            // for (var i = 0; i < form.total; i++) {
            // var forma = form.encontrados[i].descripcionFormaPago;
            // var idForma = form.encontrados[i].idFormaPago;
            //
            // itemsFor[i] = new Ext.form.Checkbox({
            // boxLabel : forma,
            // id : 'idForma_' + i,
            // name : 'forma',
            // inputValue : idForma
            // // width : '20%'
            // });
            //
            // }

            var filterPanel = Ext.create('Ext.panel.Panel', {
                bodyPadding : 7,
                layout : 'anchor',
                buttonAlign : 'center',
                collapsible : true,
                collapsed : false,
                width : '1250px',
                title : 'Criterios de B\xfaqueda',
                buttons : [ {
                    text : 'Buscar',
                    iconCls : "icon_search",
                    handler : function() {
                        buscar();
                    }
                }, {
                    text : 'Limpiar',
                    iconCls : "icon_limpiar",
                    handler : function() {
                        limpiar();
                    }
                }

                ],
                items : [ {
                    xtype : 'numberfield',
                    hideTrigger : true,
                    fieldLabel : 'Minimo Deuda',
                    id : 'montoMinimoDeuda',
                    name : 'montoMinimoDeuda',
                    minValue : 5,
                    maxValue : 10000,
                    emptyText : 'Rango ($5 - $10.000)',
                    labelStyle : 'text-align:left;'
                },
                
                     //**** Adicion Cliente Canal *****
                {
                    xtype: 'combo',
                    fieldLabel: 'Clientes Canal',
                    id: 'clienteCanal',
                    name: 'clienteCanal',
                    labelStyle: 'text-align:left;',
                    multiSelect: false,
                    store: [
                        ['Todos', 'Todos'],
                        ['S', 'SI'],
                        ['N', 'NO']
                    ]
                },
                //**** Adicion Cliente Canal *****
                {
                    xtype : 'combo',
                    fieldLabel : 'Ultima Milla',
                    id : 'ultimaMilla',
                    name : 'ultimaMilla',
                    displayField: 'nombreUltimaMilla',
                    valueField: 'nombreUltimaMilla',
                    emptyText : 'Seleccione...',
                    labelStyle : 'text-align:left;',
                    multiSelect: false,
                    queryMode:'local',
                    store: storeUltimaMilla,                                                        
                },

                {
                    xtype : 'container',
                    layout : 'hbox',
                    items : [ {
                        xtype : 'label',
                        text : 'Fecha de Corte:',
                        margin : '15 0 0 0'
                    }, {
                        xtype : 'component',
                        width : 20
                    }, {

                        xtype : 'container',
                        layout : 'vbox',
                        items : [ {
                            xtype : 'datefield',
                            id : 'dateCorteDesde',
                            name : 'dateCorteDesde',
                            fieldLabel : "Desde",
                            emptyText : 'Ingrese una fecha',
                            labelStyle : 'text-align:left;',
                            allowBlank : false,
                            // value: new Date(),
                            maxValue : new Date()

                        }, {
                            xtype : 'datefield',
                            id : 'dateCorteHasta',
                            name : 'dateCorteHasta',
                            fieldLabel : "Hasta",
                            emptyText : 'Ingrese una fecha',
                            labelStyle : 'text-align:left;',
                            allowBlank : false,
                            // value: new Date(),
                            maxValue : new Date()
                        } ]

                    }, ]
                },                           

                {
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
                // -------------------------------------

                ],
                renderTo : 'filtro'
            });

        },
        failure : function(result) {
            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
        }
    });

    var pluginExpanded = true;

    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly : true
    })

    gridServicios = Ext.create('Ext.grid.Panel', {
    	width : '1250px',
        height : 500,
        store : store,
        loadMask : true,
        frame : false,
        selModel : sm,
        iconCls : 'icon-grid',
        dockedItems : [ {
            xtype : 'toolbar',
            dock : 'top',
            align : '->',
            items : [
            // tbfill -> alinea los items siguientes a la derecha
            {
                xtype : 'tbfill'
            }, {
                iconCls : 'icon_reactivacionMasiva',
                text : 'Reactivar',
                itemId : 'deleteAjax',
                scope : this,
                handler : function() {
                    reactivacionMasivaClientes();
          
                }
            } ]
        } ],
        columns : [ {
            xtype : 'rownumberer',
            width : 40
        }, {
            header : 'Login',
            dataIndex : 'login',
            width : 250,
            sortable : true
        }, {
            dataIndex : 'clienteNombre',
            header : 'Cliente Nombre',
            width : 330,
            sortable : true
        }, {
            dataIndex : 'oficina',
            header : 'Oficina',
            width : 200,
            sortable : true
        }, {
            dataIndex : 'cartera',
            header : 'Cartera',
            width : 120,
            sortable : true
        }, {
            dataIndex : 'formaPago',
            header : 'Forma Pago',
            width : 120,
            sortable : true
        },{
            dataIndex : 'ultimaMilla',
            header : 'Ultima Milla',
            width : 80,
            sortable : true
        },{
            header : 'Estado',
            dataIndex : 'estado',
            width : 80,
            sortable : true
        } ],
         bbar: Ext.create('Ext.PagingToolbar', {
	         store: store,
	         displayInfo: true,
	         displayMsg: 'Mostrando {0} - {1} de {2}',
	         emptyMsg: "No hay datos que mostrar."
         }),
        renderTo : 'grid'
    });

});

function buscar() {
	
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

    	if(idsOficinasSelected!=""){
    		message = Ext.MessageBox.show({
    	        title : 'Favor espere',
    	        msg : 'Procesando...',
    	        closable:false,
    	        progressText: 'Saving...',
    	        width:300,
    	        wait:true,
    	        waitConfig: {interval:200}
    	    });
    	    sm.deselectAll();
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

    	    // ... Variables de filtros para la reactivacion ... //
    	    fechaCorteDesde = Ext.getCmp('dateCorteDesde').value;
    	    fechaCorteHasta = Ext.getCmp('dateCorteHasta').value;
    	    valorMontoDeuda = Ext.getCmp('montoMinimoDeuda').value;
            clienteCanal    = Ext.getCmp('clienteCanal').value;
            //ultima milla
            ultimaMilla = Ext.getCmp('ultimaMilla').value;	
    	    idsOficinas     = idsOficinasSelected;
    	    // ... Recargamos el store del grid con los filtros ... //
    	    store.getProxy().extraParams.fechaCorteDesde = fechaCorteDesde;
    	    store.getProxy().extraParams.fechaCorteHasta = fechaCorteHasta;
    	    store.getProxy().extraParams.valorMontoDeuda = valorMontoDeuda;
    	    store.getProxy().extraParams.idsOficinas     = idsOficinas;
            store.getProxy().extraParams.clienteCanal    = clienteCanal;
            //Ultima  milla
            store.getProxy().extraParams.ultimaMilla     = ultimaMilla;
    	    store.getProxy().extraParams.estado          = "In-Corte";
    	    // ...
    	    store.removeAll();
    	    store.load();
    	}else{
    		Ext.Msg.alert('Alerta',"Favor, seleccione una oficina");
    	}
    
	
    
}

function limpiar() {
    sm.deselectAll();

    Ext.getCmp('dateCorteDesde').reset();
    Ext.getCmp('dateCorteHasta').reset();
    Ext.getCmp('montoMinimoDeuda').reset();
    Ext.getCmp('clienteCanal').reset();


    for (var i = 0; i < itemsOfi.length; i++) {
        oficinaSeteada = Ext.getCmp('idOficina_' + i).setValue(false);
    }

    store.load({
        params : {
            fechaCorteDesde : "",
            fechaCorteHasta : "",
            valorMontoDeuda : "",
            idsOficinas : "",
            estado : ""
        }
    });
}

function reactivacionMasivaClientes() {      
    var param = '';
    var cantidadPuntos = 0;
    if (sm.getSelection().length > 0) {
        var estado = 0;
        for (var i = 0; i < sm.getSelection().length; ++i) {
            cantidadPuntos = cantidadPuntos + 1;
            param = param + sm.getSelection()[i].data.idPunto;

            if (sm.getSelection()[i].data.estado == 'Activo') {
                estado = estado + 1;
            }
            if (i < (sm.getSelection().length - 1)) {
                param = param + '|';
            }
            
        }

        // alert(param);

        if (estado == 0) {
            Ext.Msg.confirm('Alerta', 'Se procedera a REACTIVAR a los CLIENTES. Desea continuar?', function(btn) {
                if (btn == 'yes') {
                	message = Ext.MessageBox.show({
                        title : 'Favor espere',
                        msg : 'Procesando...',
                        progressText: 'Saving...',
                        width:300,
                        wait:true,
                        closable:false,
                        waitConfig: {interval:200}
                    });
                    Ext.Ajax.request({
                        url : reactivarClientesMasivo,
                        method : 'post',
                        timeout : 400000,
                        params : {
                            idsPuntos : param,
                            fechaCorteDesde : fechaCorteDesde,
                            fechaCorteHasta : fechaCorteHasta,
                            valorMontoDeuda : valorMontoDeuda,
                            idsOficinas : idsOficinas,
                            cantidadPuntos : cantidadPuntos
                        },
                        success : function(response) {
                        	
                            Ext.MessageBox.hide();
//                            alert(response.responseText);
                            Ext.Msg.alert('Alerta',response.responseText, function(btn) {
                                if (btn == 'ok') {
                                	
                                		store.load({
                                            params : {
                                            	fechaCorteDesde : '',
                                                fechaCorteHasta : '',
                                                valorMontoDeuda : '',
                                                idsOficinas : '',
                                                estado : ''
                                            }
                                        });	
                                	
                                }
                            });
                        },
                        failure : function(result) {
                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                        }
                    });
                }
            });

        } else {
            alert('Por lo menos uno de los CLIENTES se encuentra ACTIVO');
        }
    } else {
//        alert('Seleccione por lo menos un CLIENTE de la lista');
        Ext.Msg.alert('Alerta ', 'Seleccione por lo menos un CLIENTE de la lista');
    }
}