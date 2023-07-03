/**
* Se crea "storeJurisdiccionPe" para obtener los parametros desde la BBDD 
* y llenarlos en el campo "cmb_jurisdiccionPe"
* @author David Valdivieso <dvaldiviezon@telconet.ec>
* @version 1.0 18/04/2023
*/

var items;
var itemF;
var tipoFiltroModulo = 'Financiero';
var isMasivo = false;
var tipoEnvioMSG = '';

itemsFor = [];
itemsOfi = [];
itemsBancosTarjetas = [];
itemPanelTipoPago = [];

var numFacturasAbiertas;
var fechaEmisionFactura;
var valorMontoDeuda;
var idFormaPago;
var idsBancosTarjetas;
var idsOficinas;
// ...
var idsOficinasGlobal;
var idsFormasPagoGlobal;
var chkBoxPuntoServicio = Ext.create('Ext.selection.CheckboxModel');
var chkBoxFinancieroPuntoServicio = Ext.create('Ext.selection.CheckboxModel');
Ext.require([
	'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);
	
Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();
    
    /*************************************************************/
    //              GRID PARTE FINANCIERA - INICIO
    /**************************************************************/
    
     storeTipoNegocio = new Ext.data.Store({
		total : 'total',
		autoLoad:true,
		proxy : {
			type : 'ajax',
			url : '/tecnico/procesomasivo/corte/getTipoNegocioPorEmpresa',
			reader : {
				type : 'json',
				totalProperty : 'total',
				root : 'registros'
			}
		},
		fields : [ {
			name : 'idTipoNegocio',
			mapping : 'idTipoNegocio'
		}, {
			name : 'nombreTipoNegocio',
			mapping : 'nombreTipoNegocio'
		} ]
	});	
     
      var frameBancosTarjetas = new Ext.form.CheckboxGroup({
				id : 'frameBancosTarjetas',
				flex : 4,
				vertical : true,
				align : 'left',
				columns : 2
			});
  
	 
      storeBancosTarjetas = new Ext.data.Store({
	      total : 'total',
	      proxy : {
		      type : 'ajax',
		      url : '/tecnico/procesomasivo/corte/getBancosTarjetas',
		      reader : {
			      type : 'json',
			      totalProperty : 'total',
			      root : 'registros'
		      }
	      },
	      fields : [ {
		      name : 'id',
		      mapping : 'id'
	      }, {
		      name : 'nombre',
		      mapping : 'nombre'
	      } ],
	      listeners : {
		      load : function(t, records, options) {
			      frameBancosTarjetas.removeAll();
			      var i = 0;
			      Ext.getCmp('panelBancosTarjetas').setVisible(false);
			      if (records[0].data.nombre != "") {
				      for (var i = 0; i < records.length; i++) {
					      var cb = Ext.create('Ext.form.field.Checkbox',
							      {
								      boxLabel : records[i].data.nombre,
								      inputValue : records[i].data.id,
								      id : 'idBancoTarjeta_' + i,
								      name : 'bancoTarjeta'
							      });					      
					      frameBancosTarjetas.add(cb);
					      itemsBancosTarjetas[i] = cb;
					      Ext.getCmp('panelBancosTarjetas').setVisible(
							      true);
				      }
			      }
			      Ext.MessageBox.hide();
		      }
	      }
      });
      
    //Ciclo de Facturacion
    var storeCiclos = new Ext.data.Store({
                pageSize: 10,
                total: 'intTotal',
                proxy: {
                    type: 'ajax',
                    url : getCiclos,
                    reader: {
                        type: 'json',
                        totalProperty: 'intTotal',
                        root: 'arrayRegistros'
                    }
                },
                fields:
                          [
                      {name:'intIdCiclo', mapping:'intIdCiclo'},
                      {name:'strNombreCiclo', mapping:'strNombreCiclo'}
                          ],
                autoLoad: true
            });

    var cboCiclosFacturacion = Ext.create('Ext.form.ComboBox', {
    xtype : 'combo',
    fieldLabel : 'Ciclo de Facturacion',
    id : 'cmbCicloFacturacion',
    name : 'cmbCicloFacturacion',
    displayField: 'strNombreCiclo',
    valueField: 'intIdCiclo',
    emptyText : 'Seleccione...',
    labelStyle : 'text-align:left;',
    multiSelect: false,
    queryMode:'local',
    store: storeCiclos
    });

    Ext.Ajax.request({
		url : "/tecnico/procesomasivo/corte/getOficinaGrupoConFormaPago",
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

		      var form = Ext.JSON.decode(formaPago);
		      for (var i = 0; i < form.total; i++) {
			      var forma = form.encontrados[i].descripcionFormaPago;
			      var idForma = form.encontrados[i].idFormaPago;

			      itemsFor[i] = new Ext.form.Radio({
				      boxLabel : forma,
				      id : 'idForma_' + i,
				      name : 'forma',
				      inputValue : idForma
			      });
		      }		      		     
		  	
		    itemF = [	
    
			{html:"&nbsp;",border:false,width:150},		
			{
				xtype : 'numberfield',
				fieldLabel : 'Docs. Abiertos',
				id : 'facturasAbiertas',
				name : 'facturasAbiertas',
				minValue : 1,
				maxValue : 10,
				allowDecimals : false,
				decimalPrecision : 2,
				step : 1,
				emptyText : 'Rango (1-10)',
				labelStyle : 'text-align:left;'
			},
			{html:"&nbsp;",border:false,width:150},	
			{html:"&nbsp;",border:false,width:250},	
			{html:"&nbsp;",border:false,width:250},			
 			{html:"&nbsp;",border:false,width:150},
			{
				xtype : 'numberfield',
				hideTrigger : true,
				fieldLabel : 'Monto Cartera',
				id : 'montoCartera',
				name : 'montoCartera',
				minValue : 5,
				maxValue : 10000,
				emptyText : 'Rango ($5 - $10.000)',
				labelStyle : 'text-align:left;'
			},
			{html:"&nbsp;",border:false,width:150},	
			{html:"&nbsp;",border:false,width:250},	
			{html:"&nbsp;",border:false,width:250},			
 			{html:"&nbsp;",border:false,width:150},
			{
				xtype : 'combo',
				fieldLabel : 'Tipo Negocio',
				id : 'tipoNegocio',
				name : 'tipoNegocio',
				displayField: 'nombreTipoNegocio',
				valueField: 'idTipoNegocio',
				emptyText : 'Seleccione...',
				labelStyle : 'text-align:left;',
				multiSelect: false,
				queryMode:'local',
				store: storeTipoNegocio				
			},
			{html:"&nbsp;",border:false,width:150},	
			{html:"&nbsp;",border:false,width:250},	
			 {html:"&nbsp;",border:false,width:250},			
 			 {html:"&nbsp;",border:false,width:150},			
			{
				xtype: 'combobox',
				fieldLabel: 'Estado Servicio',
				id: 'estado',
			        name: 'estado',				
				store: [
					['Activo','Activo'],
					['In-Corte','In-Corte'],					
					['Cancel','Cancel']
				],				
			},
			{html:"&nbsp;",border:false,width:150},	
			{html:"&nbsp;",border:false,width:250},	
			{html:"&nbsp;",border:false,width:250},			
 			{html:"&nbsp;",border:false,width:150},
			{
				xtype: 'datefield',				
				id: 'feActivacion',
				name: 'feActivacion',
				fieldLabel: 'Activacion Servicio Hasta:',   
				format: 'Y-m-d',
				editable: false	,
				disabled: empresa!='TTCO'?false:true
			},
			{html:"&nbsp;",border:false,width:150},	
			{html:"&nbsp;",border:false,width:250},	
			{html:"&nbsp;",border:false,width:250},			
 			{html:"&nbsp;",border:false,width:150},
                        boolPermisoEmpresas?cboCiclosFacturacion:{html:"&nbsp;",border:false,width:50},
                        {html:"&nbsp;",border:false,width:150},
                        {html:"&nbsp;",border:false,width:250},
                        {html:"&nbsp;",border:false,width:250},
                        {html:"&nbsp;",border:false,width:150},
			{
				xtype : 'fieldset',
				title : 'Oficinas',
				width : 1010,
				style : 'text-align:left;',
				collapsible : false,
				collapsed : false,
				items : [
					{
						xtype : 'checkboxgroup',
						columns : 3,
						vertical : true,
						items : itemsOfi
					},
					{
						xtype : 'panel',
						buttonAlign : 'right',
						bbar : [
							{
								text : 'Select All',
								handler : function() {
									for (var i = 0; i < itemsOfi.length; i++) {
										Ext.getCmp('idOficina_'+i).setValue(true);
									}
								}
							},
							'-',
							{
								text : 'Deselect All',
								handler : function() {
									for (var i = 0; i < itemsOfi.length; i++) {
										Ext.getCmp('idOficina_'+i).setValue(false);
									}
								}
							} ]
					} ]
			},
			{html:"&nbsp;",border:false,width:150},	
			{html:"&nbsp;",border:false,width:250},	
			 {html:"&nbsp;",border:false,width:250},			
 			 {html:"&nbsp;",border:false,width:150},
			{
				xtype : 'container',
				layout : 'hbox',
				items : [
						{
							xtype : 'fieldset',
							width : 400,
							title : 'Forma Pago',
							collapsible : false,
							collapsed : false,
							items : [ {
								xtype : 'radiogroup',
								columns : 1,
								vertical : true,
								align : 'left',
								items : itemsFor,
								listeners : {
									change : function(field,newValue,oldValue) {
										storeBancosTarjetas.getProxy().extraParams.idFormaPagoSelected = newValue.forma;
										storeBancosTarjetas.removeAll();
										storeBancosTarjetas.load();
										itemsBancosTarjetas = [];
										message = Ext.MessageBox
											.show({
												title : 'Favor espere',
												msg : 'Procesando...',
												closable : false,
												progressText : 'Saving...',
												width : 300,
												wait : true,
												waitConfig : {
													interval : 200
												}
											});
									}
								}
							} ]
						},
						{
							xtype : 'component',
							width : 10
						},
						{
							id : 'panelBancosTarjetas',
							name : 'panelBancosTarjetas',
							xtype : 'fieldset',
							title : 'Bancos / Tarjetas',
							width : 600,
							collapsible : false,
							collapsed : false,
							items : [
								frameBancosTarjetas,
								{
									xtype : 'panel',
									buttonAlign : 'right',
									bbar : [
										  {
											  text : 'Select All',
											  handler : function() {
												  for (var i = 0; i < itemsBancosTarjetas.length; i++) {
													  Ext.getCmp('idBancoTarjeta_'+ i).setValue(true);
												  }
											  }
										  },
										  '-',
										  {
											  text : 'Deselect All',
											  handler : function() {
												  for (var i = 0; i < itemsBancosTarjetas.length; i++) {
													  Ext.getCmp('idBancoTarjeta_'+ i).setValue(false);
												  }
											  }
										  } 
									      ]
								} ]
						} ]
			}
    

		      ];
		      
		       var filterPanelFinanciero = Ext.create('Ext.panel.Panel', {
				  id:'filtroFinanciero',
				  bodyPadding: 5,         
				  border:false,        
				  buttonAlign: 'center',
				  layout:{
				      type:'table',
				      columns: 5,
				      align: 'left'
				  },
				  bodyStyle: {
						  background: '#fff'
					  },             
				  collapsible : true,
				  collapsed: false,
				  width: 1190,
				  title: 'Criterios de busqueda',
				  buttons: 
				  [
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
				  items: itemF,		
				  renderTo: 'filtroFinanciero'
			}); 
	
		      
		        
		}
		
	});			      	
    
    /***********************************************************************************/
    
    	
    itemPanelTipoPago = 
			  {
				xtype : 'fieldset',
				title : 'Oficinas',
				width : 1010,
				style : 'text-align:left;',
				collapsible : false,
				collapsed : false,
				items : [
					{
						xtype : 'checkboxgroup',
						columns : 3,
						vertical : true,
						items : itemsOfi
					},
					{
						xtype : 'panel',
						buttonAlign : 'right',
						bbar : [
							{
								text : 'Select All',
								handler : function() {
									for (var i = 0; i < itemsOfi.length; i++) {
										Ext.getCmp('idOficina_'+i).setValue(true);
									}
								}
							},
							'-',
							{
								text : 'Deselect All',
								handler : function() {
									for (var i = 0; i < itemsOfi.length; i++) {
										Ext.getCmp('idOficina_'+i).setValue(false);
									}
								}
							} ]
					} ]
			}
    
    ;
    
    /*************************************************************/
    //              GRID PARTE FINANCIERA - FIN
    /**************************************************************/
    
    /*********************************************************************************/
    //				TIPOS DE FILTROS A BUSCAR
    /*********************************************************************************/
    
     var filterPanelTipoModulos = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,
        border: false,
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 5,
            align: 'left'
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: false,
        collapsed: false,
        width: 1190,
        title: 'Filtros por Modulos',
        items:
            [
                {html: "&nbsp;", border: false, width: 150},
                {
                    xtype: 'combobox',
                    fieldLabel: 'Modulos',
                    id: 'sltModulos',
                    value: 'Financiero',
                    store: [
                        ['Tecnico', 'Tecnico'],
                        ['Financiero', 'Financiero'],
                        ['Comercial', 'Comercial'],
                        ['Soporte', 'Soporte']
                    ],
                    width: 425,
                    listeners: {
                        select: {
                            fn: function(e) {
                                mostrarFiltros();
                                limpiar();
                            }
                        }
                    }
                },
                {html: "&nbsp;", border: false, width: 150},
            ],
        renderTo: 'tipo'
    }); 

    /******************************* MODELO DE LA TABLA ****************************************/
    /* obtiene los registros que vienen desde el servidor */
    Ext.define('ModelStore', {
        extend: 'Ext.data.Model',
        fields:
		[				
			{name:'id_servicio', mapping:'id_servicio'},
			{name:'id_punto', mapping:'id_punto'},
			{name:'id_persona', mapping:'id_persona'},
			{name:'id_persona_empresa_rol', mapping:'id_persona_empresa_rol'},			
			{name:'cliente', mapping:'cliente'},
			{name:'direccion_cliente', mapping:'direccion_cliente'},
			{name:'nombre_oficina', mapping:'nombre_oficina'},
			{name:'login2', mapping:'login2'},
			{name:'ciudad_punto', mapping:'ciudad_punto'},
			{name:'direccion_punto', mapping:'direccion_punto'},
			{name:'estado_cliente', mapping:'estado_cliente'},			
			{name:'estado_servicio', mapping:'estado_servicio'},
			{name:'servicio', mapping:'servicio'},		
			{name:'action1', mapping:'action1'}               
		],
        idProperty: 'id_punto'
    });
	    
    chkBoxModelPuntoServicio = Ext.create('Ext.selection.CheckboxModel', {
				checkOnly : true
			});
    
    /*************************************************************************************/
    
    itemsSuperiorGrid =
        [
            {
                iconCls: 'icon_check',
                text: 'Envio por Seleccion',
                itemId: 'enviar',
                scope: this,
                handler: function() 
                {
                    var boolEjecutaNoMasivo = true;
                    
                    if (tipoFiltroModulo != 'Financiero') 
                    {
                        if (tipoFiltroModulo == 'Comercial' || tipoFiltroModulo == 'Tecnico')
                        {
                            if (gridC.getStore().data.items.length == 0)
                            {                                
                                boolEjecutaNoMasivo = false;
                            }
                        }
                        else if (tipoFiltroModulo == 'Soporte')
                        {
                            if (gridSoporte.getStore().data.items.length == 0)
                            {                               
                                boolEjecutaNoMasivo = false;
                            }
                        }
                        if(boolEjecutaNoMasivo) 
                        {
                            isMasivo = false;
                            showEnviarPlantilla();
                            tipoEnvioMSG = 'no masivo';
                        }
                        else
                        {
                            Ext.Msg.alert('Alerta ', 'Debe realizar su busqueda primero');
                        }
                    } 
                    else 
                    {
                        if (gridServicios.getStore().data.items.length == 0)
                        {
                            Ext.Msg.alert('Alerta ', 'Debe realizar su busqueda primero');
                        }
                        else 
                        {
                            isMasivo = false;
                            showEnviarPlantilla();
                            tipoEnvioMSG = 'no masivo';
                        }

                    }
                }
            },
            {
                iconCls: 'icon_check',
                text: 'Envio Masivo',
                itemId: 'enviarMasivo',
                scope: this,
                handler: function() {
                    showEnviarMasivo();
                    isMasivo = true;
                    tipoEnvioMSG = 'masivo';
                }
            },
            {xtype: 'tbfill'},
            {
                iconCls: 'icon_limpiar',
                text: 'Borrar Todos',
                itemId: 'clear',
                scope: this,
                handler: function() {
                    Ext.getCmp('gridC').getPlugin('pagingSelectionPersistence').clearPersistedSelection();
                    Ext.getCmp('gridServicios').getPlugin('pagingSelectionPersistence').clearPersistedSelection();
                    Ext.getCmp('gridSoporte').getPlugin('pagingSelectionPersistence').clearPersistedSelection();
                }
            },
            {
                iconCls: 'icon_exportar',
                text: 'Exportar',
                itemId: 'exportar',
                scope: this,
                handler: function() {
                    exportarExcel();
                }
            }
        ];    
    /**************************************************************/
    //              GRID PARTE SPORTE - INICIO
    //**************************************************************
    storeSoporte = new Ext.data.Store({
        pageSize: 25,        
        total: 'total',
        proxy: {
            type: 'ajax',
            timeout: 1000000,
            url: 'getPuntosANotificarPorSoporte',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                estadoCaso:'Asignado'
            }
        },
        fields:
            [
                {
                    name: 'idPunto',
                    mapping: 'idPunto'
                }, {
                    name: 'login',
                    mapping: 'login'
                }, {
                    name: 'cliente',
                    mapping: 'cliente'
                }, {
                    name: 'numeroCaso',
                    mapping: 'numeroCaso'
                }, {
                    name: 'estadoCaso',
                    mapping: 'estadoCaso'
                }, {
                    name: 'oficina',
                    mapping: 'oficina'
                }, {
                    name: 'departamento',
                    mapping: 'departamento'
                }
            ]
    });       
    
    gridSoporte = Ext.create('Ext.grid.Panel', {        
        width: 1190,
        height:650,
        id:'gridSoporte',
        store: storeSoporte,		        
        selModel: Ext.create('Ext.selection.CheckboxModel'),
        plugins: [{ptype : 'pagingselectpersist'}],
        viewConfig: {
            id: 'gv1',
            trackOver: true,
            stripeRows: true,
            loadMask: true,
            enableTextSelection: true
        },
        dockedItems: 
		[ 
			{
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items: itemsSuperiorGrid,
			}
        ], 
        columns:
		[
			{
				xtype : 'rownumberer',
				width : 40
			},		
			{
			  id: 'id_punto',
			  header: 'IdPunto',
			  dataIndex: 'id_punto',
			  hidden: true,
			  hideable: false
			},												
			{
			  id: 'login',
			  header: 'Login',
			  dataIndex: 'login',
			  width: 180,
			  sortable: true
			},
			{
			  id: 'cliente',
			  header: 'Cliente',
			  dataIndex: 'cliente',
			  width: 300,
			  sortable: true
			},
			{
			  id: 'numeroCaso',
			  header: 'Caso',
			  dataIndex: 'numeroCaso',
			  width: 180,
			  sortable: true
			},
			{
			  id: 'estadoCaso',
			  header: 'Estado Caso',
			  dataIndex: 'estadoCaso',
			  width: 100,
			  sortable: true
			},    
            {
			  id: 'oficina',
			  header: 'Oficina',
			  dataIndex: 'oficina',
			  width: 150,
			  sortable: true
			},
			{
			  id: 'departamento',
			  header: 'Departamento Asignado',
			  dataIndex: 'departamento',
			  width: 180,
			  sortable: true
			}					
		],
	    bbar: Ext.create('Ext.PagingToolbar', {
            displayInfo: true,
            store: storeSoporte,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'gridSoporte'
    });    
    
    /*************************************************************/
    //              GRID PARTE SOPORTE - FIN
    /**************************************************************/
    
		
    /*************************************************************/
    //              GRID PARTE TECNICA/COMERCIAL - INICIO
    /**************************************************************/

    store = new Ext.data.Store({
        pageSize: 100,
        model: 'ModelStore', //obtiene los campos desde el servidor , mapeo
        total: 'total',
        proxy: {
            type: 'ajax',
            timeout: 1000000,
            url: 'grid',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados',
            },
            extraParams: {
                login2: '',
                ciudad_punto: '',
                direccion_punto: '',
                oficina: '',
                estado_servicio: '-',
                estado_punto: '-',
                tipoFiltroModulo: 'Tecnico'
            }
        }
    });       
    
    gridC = Ext.create('Ext.grid.Panel', {        
        width: 1190,
        height:600,
        id:'gridC',
        store: store,		        
        selModel: chkBoxPuntoServicio,
        plugins: [{ptype : 'pagingselectpersist'}],
        viewConfig: {
            id: 'gv2',
            trackOver: true,
            stripeRows: true,
            loadMask: true,
            enableTextSelection: true
        },
        dockedItems: 
		[ 
			{
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items: itemsSuperiorGrid
			}
        ], 
        columns:
		[
			{
				xtype : 'rownumberer',
				width : 40
			},
			{
			  id: 'id_servicio',
			  header: 'IdServicio',
			  dataIndex: 'id_servicio',
			  hidden: true,
			  hideable: false
			},
			{
			  id: 'id_punto',
			  header: 'IdPunto',
			  dataIndex: 'id_punto',
			  hidden: true,
			  hideable: false
			},
			{
			  id: 'id_persona',
			  header: 'IdPersona',
			  dataIndex: 'id_persona',
			  hidden: true,
			  hideable: false
			},
			{
			  id: 'id_persona_empresa_rol',
			  header: 'IdPersonaEmpresaRol',
			  dataIndex: 'id_persona_empresa_rol',
			  hidden: true,
			  hideable: false
			},							
			{
			  id: 'login2',
			  header: 'Login',
			  dataIndex: 'login2',
			  width: 180,
			  sortable: true
			},
			{
			  id: 'cliente',
			  header: 'Cliente',
			  dataIndex: 'cliente',
			  width: 250,
			  sortable: true
			},
			{
			  id: 'nombre_oficina',
			  header: 'Oficina',
			  dataIndex: 'nombre_oficina',
			  width: 180,
			  sortable: true
			},
			{
			  id: 'servicio',
			  header: 'Servicio',
			  dataIndex: 'servicio',
			  width: 180,
			  sortable: true
			},     
			{
			  id: 'ciudad_punto',
			  header: 'Ciudad Punto',
			  dataIndex: 'ciudad_punto',
			  width: 180,
			  sortable: true
			},	   						
			{
			  id: 'estado_servicio',
			  header: 'Estado Servicio',
			  dataIndex: 'estado_servicio',
			  width: 115,
			  sortable: true
			},  			
		],
	    bbar: Ext.create('Ext.PagingToolbar', {            
            displayInfo: true,
	    store:store,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'grid'
    });    
    
    /*************************************************************/
    //              GRID PARTE TECNICA/COMERCIAL - FIN
    /**************************************************************/
    
    /*************************************************************/
    //              GRID PARTE FINANCIERA - INICIO
    /**************************************************************/
    
    storeFinanciero = new Ext.data.Store({
				pageSize : 100,
				total : 'total',
				proxy : {
					type : 'ajax',
					timeout : 400000,
					//url : '/tecnico/procesomasivo/corte/getPuntosACortar',
					url : '/soporte/notificaciones/envio_plantilla/getPuntosANotificar',
					reader : {
						type : 'json',
						totalProperty : 'total',
						root : 'encontrados'
					},
					extraParams : {
						numFacturasAbiertas : '',						
						valorMontoDeuda : '',
						idFormaPago : '',
						idsBancosTarjetas : '',
						idsOficinas : '',
                                                estado : '',
                                                ciclosFacturacion : ''
					}
				},
				fields : 
				[ 
				      {
					      name : 'idPunto',
					      mapping : 'idPunto'
				      }, {
					      name : 'login',
					      mapping : 'login'
				      }, {
					      name : 'clienteNombre',
					      mapping : 'clienteNombre'
				      }, {
					      name : 'clienteId',
					      mapping : 'clienteId'
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
					      name : 'nombreNegocio',
					      mapping : 'nombreTipoNegocio'
				      },
				      {
					      name : 'feActivacion',
					      mapping : 'feActivacion'
				      },{
					      name : 'estado',
					      mapping : 'estado'
				      } 
				]
			});    
    
    gridServicios = Ext.create('Ext.grid.Panel', {
        width: '1190px',
        height: 500,
        id: 'gridServicios',
        store: storeFinanciero,
        frame: false,
        setVisible: false,
        selModel: chkBoxFinancieroPuntoServicio,
        viewConfig: {enableTextSelection: true},
        plugins: [{ptype: 'pagingselectpersist'}],
        viewConfig: {
            id: 'gv3',
            trackOver: true,
            stripeRows: true,
            loadMask: true
        },
        iconCls: 'icon-grid',
        dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items: itemsSuperiorGrid
            }],
        columns: [
            {
                xtype: 'rownumberer',
                width: 25
            },
            {
                header: 'Login',
                dataIndex: 'login',
                width: 180,
                sortable: true
            },
            {
                dataIndex: 'clienteNombre',
                header: 'Cliente Nombre',
                width: 250,
                sortable: true
            },
            {
                dataIndex: 'oficina',
                header: 'Oficina',
                width: 180,
                sortable: true
            },
            {
                dataIndex: 'cartera',
                header: 'Cartera',
                width: 80,
                sortable: true
            },
            {
                dataIndex: 'formaPago',
                header: 'Forma Pago',
                width: 150,
                sortable: true
            },
            {
                dataIndex: 'nombreNegocio',
                header: 'Tipo Negocio',
                width: 110,
                sortable: true
            },
            {
                dataIndex: 'feActivacion',
                header: 'Fecha Activacion',
                width: 100,
                sortable: true
            },
            {
                dataIndex: 'estado',
                header: 'Estado Servicio',
                width: 100,
                sortable: true
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeFinanciero,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'gridFinanciero',
        listeners: {
            itemdblclick: function(view, record, item, index, eventobj, obj) {
                var position = view.getPositionByEvent(eventobj),
                    data = record.data,
                    value = data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title: 'Copiar texto?',
                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    buttons: Ext.Msg.OK,
                    icon: Ext.Msg.INFORMATION
                });
            },
            viewready: function(grid) {
                var view = grid.view;

                // record the current cellIndex
                grid.mon(view, {
                    uievent: function(type, view, cell, recordIndex, cellIndex, e) {
                        grid.cellIndex = cellIndex;
                        grid.recordIndex = recordIndex;
                    }
                });

                grid.tip = Ext.create('Ext.tip.ToolTip', {
                    target: view.el,
                    delegate: '.x-grid-cell',
                    trackMouse: true,
                    renderTo: Ext.getBody(),
                    listeners: {
                        beforeshow: function updateTipBody(tip) {
                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1) {
                                header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                tip.update(grid.getStore().getAt(grid.recordIndex).get(header.dataIndex));
                            }
                        }
                    }
                });

            }
        }
    });
    
    /*************************************************************/
    //              GRID PARTE FINANCIERA - FIN
    /**************************************************************/
    
    
    Ext.getCmp('gridServicios').show();
    Ext.getCmp('gridC').hide();
    Ext.getCmp('gridSoporte').hide();
    
    $('#filtroFinanciero').show();
    $('#filtro').hide();
		     
    mostrarFiltros();
    
   
});

/* ******************************************* */
            /*  FUNCIONES  */
/* ******************************************* */

function mostrarFiltros() {

    tipoItem = Ext.getCmp('sltModulos').value;

    $('#filtro').empty();

    if (tipoItem !== 'Financiero')
    {

        Ext.getCmp('gridC').show();
        Ext.getCmp('gridServicios').hide();
        Ext.getCmp('gridSoporte').hide();
        
        $('#filtroFinanciero').hide();
        $('#filtro').show();

        if (tipoItem === 'Tecnico') 
        {
            items = getFiltrosTecnicos();
            tipoFiltroModulo = 'Tecnico';
        }
        else if (tipoItem === 'Comercial')
        {
            items = getFiltroComercial();
            tipoFiltroModulo = 'Comercial';
        }
        else
        {
            items = getFiltroSoporte();
            tipoFiltroModulo = 'Soporte';
            Ext.getCmp('gridSoporte').show();
            Ext.getCmp('gridC').hide();
        }

        var filterPanel = Ext.create('Ext.panel.Panel', {
            bodyPadding: 5,
            border: false,
            buttonAlign: 'center',
            layout: {
                type: 'table',
                columns: 5,
                align: 'left'
            },
            bodyStyle: {
                background: '#fff'
            },
            collapsible: true,
            collapsed: false,
            width: 1190,
            title: 'Criterios de busqueda',
            buttons:
                [
                    {
                        text: 'Buscar',
                        iconCls: "icon_search",
                        handler: function() {
                            buscar();
                        }
                    },
                    {
                        text: 'Limpiar',
                        iconCls: "icon_limpiar",
                        handler: function() {
                            limpiar();
                        }
                    }
                ],
            items: items,
            renderTo: 'filtro'
        });

    } 
    else 
    {
        tipoFiltroModulo = 'Financiero';
        Ext.getCmp('gridServicios').show();
        Ext.getCmp('gridSoporte').hide();
        Ext.getCmp('gridC').hide();
        $('#filtroFinanciero').show();
        $('#filtro').hide();
    }
    
}
function getFiltrosTecnicos(){
  
    	/*********************************************************************************/
			    // **************** OFICINAS ******************
	/*********************************************************************************/
	Ext.define('OficinasList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_oficina_grupo', type:'int'},
            {name:'nombre_oficina', type:'string'}
        ]
    });     
      
    
    /*************************************************************************************/
	
    eval("var storeOficinas = Ext.create('Ext.data.Store', { "+
        "  id: 'storeOficinas', "+
        "  model: 'OficinasList', "+
        "  autoLoad: false, "+
        " proxy: { "+
            "   type: 'ajax',"+
        "    url : 'getOficinas',"+
            "   reader: {"+
        "        type: 'json',"+
            "       totalProperty: 'total',"+
        "        root: 'encontrados'"+
            "  }"+
        "  }"+
    " });    ");
    
    combo_oficinas = new Ext.form.ComboBox({
        id: 'cmb_oficina',
        name: 'cmb_oficina',
        fieldLabel: "Oficinas",
        emptyText: 'Seleccione Oficina',
        store: eval("storeOficinas"),
        displayField: 'nombre_oficina',
        valueField: 'id_oficina_grupo',
        height:30,
	width: 425,
        border:0,
        margin:0,
	queryMode: "remote",
	emptyText: ''
    });

	/*********************************************************************************/
			    //ELEMENTOS PADRE
	/*********************************************************************************/	
	
    var param = empresa=='TTCO'?'POP':'NODO';
    var labelTipo = empresa=='TTCO'?'POP':'NODO';
    var labelElemento = empresa=='TTCO'?'DSLAM':'OLT';
    var boolCombo = true;

    if(empresa == "TN")
    {
        boolCombo = false;
    }

    storeElementosPadre = new Ext.data.Store({ 
		total: 'total',
		pageSize: 10000,
		proxy: {
			type: 'ajax',
			url : 'getElementos',
			reader: {
				type: 'json',
				totalProperty: 'total',
				root: 'encontrados'
			},
			extraParams: {
				nombre: this.nombreElemento,
				estado: 'ACTIVE',
				elemento: param
			}
		},
		fields:
		[
			{name:'idElemento', mapping:'idElemento'},
			{name:'nombreElemento', mapping:'nombreElemento'}
		]
	});

    storeProductos = new Ext.data.Store({
		total: 'total',
		pageSize: 10000,
		proxy: {
			type: 'ajax',
			url : getProductos,
			reader: {
				type: 'json',
				totalProperty: 'total',
				root: 'encontrados'
			},
			extraParams: {
			}
		},
		fields:
		[
			{name:'id_producto', mapping:'id_producto'},
			{name:'nombre_producto', mapping:'nombre_producto'}
		]
	});

    storeCiudades = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: ajaxCiudad,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
                estado: 'Activo'
            }
        },
        fields:
            [
                {name: 'id_canton', mapping: 'id_canton'},
                {name: 'nombre_canton', mapping: 'nombre_canton'}
            ]
    });

    storePe = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: urlGetPeByCiudad,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
                estado: 'Activo'
            }
        },
        fields:
            [
                {name: 'nombre_pe', mapping: 'nombre_pe'}
            ]
    });
	
    cmb_elementosPadre = new Ext.form.ComboBox({
        id: 'cmb_elementosPadre',
        name: 'cmb_elementosPadre',
        fieldLabel: labelTipo,
        emptyText: '',
        store: storeElementosPadre,
        displayField: 'nombreElemento',
        valueField: 'idElemento',
        height:30,
	width: 425,
        border:0,
        margin:0,
	queryMode: "remote",
	emptyText: '',
	listeners: {
            select: function(){                
                
                Ext.getCmp('cmb_elementos').setDisabled(false);
                Ext.getCmp('cmb_elementos').setRawValue('');
		Ext.getCmp('cmb_elementos').reset();
              
                storeElementos.proxy.extraParams = {
                    tipoElemento: Ext.getCmp("cmb_elementosPadre").getValue()
                };
                storeElementos.load();                
            }
        }
	
    });

    storeJurisdiccionPe = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: urlGetJurisdiccionesPe,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
                estado: 'Activo'
            }
        },
        fields:
            [
                {name: 'jurisdiccion_pe', mapping: 'jurisdiccion_pe'}
            ]
    });

    cmb_jurisdiccionPe = new Ext.form.ComboBox({
        id: 'cmb_jurisdiccionPe',
        name: 'cmb_jurisdiccionPe',
        fieldLabel: 'Jurisdiccion PE',
        emptyText: '',
        store: storeJurisdiccionPe,
        displayField: 'jurisdiccion_pe',
        valueField: 'jurisdiccion_pe',
        height:30,
        width: 425,
        hidden: boolCombo,
        border:0,
        margin:0,
        queryMode: "remote",
        listeners:{
              select:{
                  fn:function(comp, record, index) {
                        Ext.getCmp('cmb_protocolo').value = "";
                        Ext.getCmp('cmb_protocolo').setRawValue("");
                        Ext.getCmp('cmb_servicio').value = "";
                        Ext.getCmp('cmb_servicio').setRawValue("");
                        Ext.getCmp('cmb_ciudad').value = "";
                        Ext.getCmp('cmb_ciudad').setRawValue("");
                        Ext.getCmp('cmb_pe').value = "";
                        Ext.getCmp('cmb_pe').setRawValue("");
                        storePe.removeAll();
                        storePe.proxy.extraParams = { jurisdiccion: comp.getValue() };
                        storePe.load();
                  }
              }
         }
    });

    cmb_ciudad = new Ext.form.ComboBox({
        id: 'cmb_ciudad',
        name: 'cmb_ciudad',
        fieldLabel: 'Ciudad',
        emptyText: '',
        store: storeCiudades,
        displayField: 'nombre_canton',
        valueField: 'id_canton',
        height:30,
        width: 425,
        hidden: boolCombo,
        border:0,
        margin:0,
        queryMode: "remote",
        listeners: {
            select: function(){

            }
        }

    });

    cmb_pe = new Ext.form.ComboBox({
        id: 'cmb_pe',
        name: 'cmb_pe',
        fieldLabel: 'PE',
        emptyText: '',
        store: storePe,
        displayField: 'nombre_pe',
        valueField: 'nombre_pe',
        height:30,
        width: 425,
        hidden: boolCombo,
        border:0,
        margin:0,
        queryMode: "remote",
        listeners: {
            select: function(){

            }
        }
    });

    cmb_protocolo = new Ext.form.ComboBox({
        id: 'cmb_protocolo',
        name: 'cmb_protocolo',
        fieldLabel: 'Protocolo',
        emptyText: '',
        store: [
            ['STANDARD','STANDARD'],
            ['BGP','BGP'],
            ['EIGRP','EIGRP'],
            ['OSPF','OSPF']
        ],
        displayField: 'nombreProtocolo',
        valueField: 'idProtocolo',
        height:30,
        width: 425,
        hidden: boolCombo,
        border:0,
        margin:0,
        queryMode: "remote",
        listeners: {
            select: function(){

            }
        }
    });

    cmb_servicio = new Ext.form.ComboBox({
        id: 'cmb_servicio',
        name: 'cmb_servicio',
        fieldLabel: 'Servicio',
        emptyText: '',
        store: storeProductos,
        displayField: 'nombre_producto',
        valueField: 'id_producto',
        height:30,
        width: 425,
        hidden: boolCombo,
        border:0,
        margin:0,
        queryMode: "remote",
        listeners: {
            select: function(){

            }
        }
    });
	/*********************************************************************************/
			    // **************** DSLAM ******************
	/*********************************************************************************/
	storeElementos = new Ext.data.Store({ 
		total: 'total',
		pageSize: 10000,
		proxy: {
			type: 'ajax',
			url : 'getElementosPorTipo',
			reader: {
				type: 'json',
				totalProperty: 'total',
				root: 'encontrados'
			},
			extraParams: {
				nombre: this.nombreElemento,
				estado: 'ACTIVE',				
			}
		},
		fields:
		[
			{name:'idElemento', mapping:'idElemento'},
			{name:'nombreElemento', mapping:'nombreElemento'}
		]
	});
	
    cmb_elementos = new Ext.form.ComboBox({
        id: 'cmb_elementos',
        name: 'cmb_elementos',
        fieldLabel: labelElemento,
        emptyText: '',
        store: storeElementos,
        displayField: 'nombreElemento',
        valueField: 'idElemento',
        height:30,
	width: 425,
        border:0,
        margin:0,
	queryMode: "remote",
	emptyText: ''
    });
	
   /* ********************************************************* */
                /* FILTROS DE BUSQUEDA TECNICO*/
    /* ********************************************************* */                 
  
    var itemsTecnicos = [

			{html:"&nbsp;",border:false,width:150,hidden:boolCombo},
			cmb_jurisdiccionPe,
			{html:"&nbsp;",border:false,width:150,hidden:boolCombo},
			cmb_pe,
			{html:"&nbsp;",border:false,width:250,hidden:boolCombo},
			{html:"&nbsp;",border:false,width:150,hidden:boolCombo},
			cmb_protocolo,
			{html:"&nbsp;",border:false,width:150,hidden:boolCombo},
			cmb_servicio,
			{html:"&nbsp;",border:false,width:250,hidden:boolCombo},
			{html:"&nbsp;",border:false,width:150,hidden:boolCombo},
			cmb_ciudad,
			{html:"&nbsp;",border:false,width:150,hidden:boolCombo},
			{html:"&nbsp;",border:false,width:150,hidden:boolCombo},
			{html:"&nbsp;",border:false,width:250,hidden:boolCombo},
			{html:"&nbsp;",border:false,width:150,hidden:boolCombo},
			cmb_elementosPadre,
			{html:"&nbsp;",border:false,width:150},
			cmb_elementos,			
			{html:"&nbsp;",border:false,width:250},			
			{html:"&nbsp;",border:false,width:150},
			combo_oficinas,			
			{html:"&nbsp;",border:false,width:150},
			{
				xtype: 'textfield',
				id: 'txtLogin',
				fieldLabel: 'Login Pto',
				value: '',
				width: 425
			},
			{html:"&nbsp;",border:false,width:250},			
			{html:"&nbsp;",border:false,width:150},
			{
				xtype: 'textfield',
				id: 'txtCiudadPto',
				fieldLabel: 'Ciudad Pto',
				value: '',
				width: 425
			},
			{html:"&nbsp;",border:false,width:150},
			{
				xtype: 'textfield',
				id: 'txtDireccionPto',
				fieldLabel: 'Direccion Pto',
				value: '',
				width: 425
			},
			{html:"&nbsp;",border:false,width:250},			
			{html:"&nbsp;",border:false,width:150},
			{
				xtype: 'combobox',
				fieldLabel: 'Estado Servicio',
				id: 'sltEstadoServicio',
				value:'Todos',
				store: [
					['Todos','Todos'],
					['Activo','Activo'],
					['In-Corte','In-Corte'],
					['Cancel','Cancelado']
				],
				width: 425
			},
			{html:"&nbsp;",border:false,width:150},
			{
				xtype: 'combobox',
				fieldLabel: 'Estado Punto',
				id: 'sltEstadoPunto',
				value:'Todos',
				store: [
					['Todos','Todos'],
					['Pendiente','Pendiente'],
					['Activo','Activo'],
					['Inactivo','Inactivo']
				],
				width: 425
			},
			{html:"&nbsp;",border:false,width:250}
	  ];
	return itemsTecnicos;
  
}
function getFiltroFinanciero(){ 
    
  
	var frameBancosTarjetas = new Ext.form.CheckboxGroup({
				id : 'frameBancosTarjetas',
				flex : 4,
				vertical : true,
				align : 'left',
				columns : 2
			});
  
	 
	 
	storeBancosTarjetas = new Ext.data.Store({
				total : 'total',
				proxy : {
					type : 'ajax',
					url : '/tecnico/procesomasivo/corte/getBancosTarjetas',
					reader : {
						type : 'json',
						totalProperty : 'total',
						root : 'registros'
					}
				},
				fields : [ {
					name : 'id',
					mapping : 'id'
				}, {
					name : 'nombre',
					mapping : 'nombre'
				} ],
				listeners : {
					load : function(t, records, options) {
						frameBancosTarjetas.removeAll();
						var i = 0;
						Ext.getCmp('panelBancosTarjetas').setVisible(false);
						if (records[0].data.nombre != "") {
							for (var i = 0; i < records.length; i++) {
								var cb = Ext.create('Ext.form.field.Checkbox',
										{
											boxLabel : records[i].data.nombre,
											inputValue : records[i].data.id,
											id : 'idBancoTarjeta_' + i,
											name : 'bancoTarjeta'
										});								
								frameBancosTarjetas.add(cb);
								itemsBancosTarjetas[i] = cb;
								Ext.getCmp('panelBancosTarjetas').setVisible(
										true);
							}
						}
						Ext.MessageBox.hide();
					}
				}
			});
	 			
	var itemsFinancieros = [	
    
			{html:"&nbsp;",border:false,width:150},		
			{
				xtype : 'numberfield',
				fieldLabel : 'Docs. Abiertos',
				id : 'facturasAbiertas',
				name : 'facturasAbiertas',
				minValue : 1,
				maxValue : 10,
				allowDecimals : false,
				decimalPrecision : 2,
				step : 1,
				emptyText : 'Rango (1-10)',
				labelStyle : 'text-align:left;'
			},
			{html:"&nbsp;",border:false,width:150},	
			 {html:"&nbsp;",border:false,width:250},	
			 {html:"&nbsp;",border:false,width:250},			
 			 {html:"&nbsp;",border:false,width:150},
			{
				xtype : 'numberfield',
				hideTrigger : true,
				fieldLabel : 'Monto Cartera',
				id : 'montoCartera',
				name : 'montoCartera',
				minValue : 5,
				maxValue : 10000,
				emptyText : 'Rango ($5 - $10.000)',
				labelStyle : 'text-align:left;'
			},
			{html:"&nbsp;",border:false,width:150},	
			{html:"&nbsp;",border:false,width:250},	
			 {html:"&nbsp;",border:false,width:250},			
 			 {html:"&nbsp;",border:false,width:150},
			{
				xtype : 'combo',
				fieldLabel : 'Tipo Negocio',
				id : 'tipoNegocio',
				name : 'tipoNegocio',
				displayField: 'nombreTipoNegocio',
				valueField: 'idTipoNegocio',
				emptyText : 'Seleccione...',
				labelStyle : 'text-align:left;',
				multiSelect: false,
				queryMode:'local',
				store: storeTipoNegocio				
			},
			{html:"&nbsp;",border:false,width:150},	
			{html:"&nbsp;",border:false,width:250},	
			 {html:"&nbsp;",border:false,width:250},			
 			 {html:"&nbsp;",border:false,width:150},
			
			{
				xtype : 'fieldset',
				title : 'Oficinas',
				width : 1010,
				style : 'text-align:left;',
				collapsible : false,
				collapsed : false,
				items : [
					{
						xtype : 'checkboxgroup',
						columns : 3,
						vertical : true,
						items : itemsOfi
					},
					{
						xtype : 'panel',
						buttonAlign : 'right',
						bbar : [
							{
								text : 'Select All',
								handler : function() {
									for (var i = 0; i < itemsOfi.length; i++) {
										Ext.getCmp('idOficina_'+i).setValue(true);
									}
								}
							},
							'-',
							{
								text : 'Deselect All',
								handler : function() {
									for (var i = 0; i < itemsOfi.length; i++) {
										Ext.getCmp('idOficina_'+i).setValue(false);
									}
								}
							} ]
					} ]
			},
			{html:"&nbsp;",border:false,width:150},	
			{html:"&nbsp;",border:false,width:250},	
			 {html:"&nbsp;",border:false,width:250},			
 			 {html:"&nbsp;",border:false,width:150},
			{
				xtype : 'container',
				layout : 'hbox',
				items : [
						{
							xtype : 'fieldset',
							width : 400,
							title : 'Forma Pago',
							collapsible : false,
							collapsed : false,
							items : [ {
								xtype : 'radiogroup',
								columns : 1,
								vertical : true,
								align : 'left',
								items : itemsFor,
								listeners : {
									change : function(field,newValue,oldValue) {
										storeBancosTarjetas.getProxy().extraParams.idFormaPagoSelected = newValue.forma;
										storeBancosTarjetas.removeAll();
										storeBancosTarjetas.load();
										itemsBancosTarjetas = [];
										message = Ext.MessageBox
											.show({
												title : 'Favor espere',
												msg : 'Procesando...',
												closable : false,
												progressText : 'Saving...',
												width : 300,
												wait : true,
												waitConfig : {
													interval : 200
												}
											});
									}
								}
							} ]
						},
						{
							xtype : 'component',
							width : 10
						},
						{
							id : 'panelBancosTarjetas',
							name : 'panelBancosTarjetas',
							xtype : 'fieldset',
							title : 'Bancos / Tarjetas',
							width : 600,
							collapsible : false,
							collapsed : false,
							items : [
								frameBancosTarjetas,
								{
									xtype : 'panel',
									buttonAlign : 'right',
									bbar : [
										  {
											  text : 'Select All',
											  handler : function() {
												  for (var i = 0; i < itemsBancosTarjetas.length; i++) {
													  Ext.getCmp('idBancoTarjeta_'+ i).setValue(true);
												  }
											  }
										  },
										  '-',
										  {
											  text : 'Deselect All',
											  handler : function() {
												  for (var i = 0; i < itemsBancosTarjetas.length; i++) {
													  Ext.getCmp('idBancoTarjeta_'+ i).setValue(false);
												  }
											  }
										  } 
									      ]
								} ]
						} ]
			}
    

		      ];
	
	return itemsFinancieros;
  
}
function getFiltroComercial(){
    
    /* ************************************************************ */
                /* FILTROS DE BUSQUEDA COMERCIAL*/
    /* ************************************************************* */
     storeFormasPagosContratoBusquedaAvanzada = new Ext.data.Store({ 
                total: 'total',
                proxy: {
                    timeout: 400000,                
		    type: 'ajax',
                    url : '/search/ajaxGetFormasPagosContrato',
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    }
                },
                fields:
		[
		    {name:'forma_pago', mapping:'forma_pago'},
		    {name:'id_forma_pago', mapping:'id_forma_pago'}
		],
                listeners: {
                    load: function(store, records) {
                         store.insert(0, [{
                             forma_pago: '&nbsp;',
                             id_forma_pago: null
                         }]);
                         Ext.getCmp('forma_pago_comercial').queryMode = 'local';
                    }      
                },                 
     });
     
     //Servicios
     
     Ext.define('modelListadoServiciosBusquedaAvanzada', {
                    extend: 'Ext.data.Model',
                    fields: [
                            {name: 'servicio_por', type: 'string'}
                    ]
            });
            
            storeListadoServiciosPorBusquedaAvanzada = new Ext.data.Store({ 
                    model: 'modelListadoServiciosBusquedaAvanzada',
                    data : [
                            {servicio_por:'&nbsp;' },
                            {servicio_por:'Catalogo' },
                            {servicio_por:'Portafolio' }
                    ]
            });
            
            storeListadoServiciosBusquedaAvanzada = new Ext.data.Store({ 
                total: 'total',
                proxy: {
                    timeout: 400000,                
		    type: 'ajax',
                    url : '/search/ajaxGetListadoServiciosPor',
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    }
                },
                fields:
                [
                    {name:'id_servicio', mapping:'id_servicio'},
                    {name:'servicio', mapping:'servicio'}
                ],
                listeners: {
                    load: function(store, records) {
                         store.insert(0, [{
                             servicio: '&nbsp;',
                             id_servicio: null
                         }]);
                    }      
                },         
            });
            
            storeEstadosServicioBusquedaAvanzada = new Ext.data.Store({ 
                total: 'total',
                proxy: {
                    timeout: 400000,                type: 'ajax',
                    url : '/search/ajaxGetEstadoServicios',
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    }
                },
                fields:
                        [
                            {name:'estado_servicio_comercial', mapping:'estado_servicio'}
                        ],
                 listeners: {
                    load: function(store, records) {
                         store.insert(0, [{                            
			      estado_servicio_comercial: 'Todos',
                         }]);
                         Ext.getCmp('estado_servicio_comercial').queryMode = 'local';
                    }      
                },                
            });
    
    
    var itemsComercial = [			
			{html:"&nbsp;",border:false,width:150},
			{
                                  xtype: 'combobox',
                                  id: 'forma_pago_comercial',
                                  name: 'forma_pago_comercial',
                                  fieldLabel: 'Forma Pago',
                                  emptyText: "Seleccione",
                                  width:350,
                                  triggerAction: 'all',
                                  displayField:'forma_pago',
                                  valueField: 'id_forma_pago',
                                  selectOnTab: true,
                                  store: storeFormasPagosContratoBusquedaAvanzada,              
                                  lazyRender: true,
                                  queryMode: "remote",
                                  listClass: 'x-combo-list-small',				  
                        },
			{html:"&nbsp;",border:false,width:150},
			{
                                  xtype: 'combobox',
                                  id: 'servicios_por_comercial',
                                  name: 'servicios_por_comercial',
                                  fieldLabel: 'Servicios Por:',
                                  emptyText: "Seleccione",
                                  width:350,
                                  editable: false,
                                  triggerAction: 'all',
                                  displayField:'servicio_por',
                                  valueField: 'servicio_por',
                                  selectOnTab: true,
                                  store: storeListadoServiciosPorBusquedaAvanzada,              
                                  lazyRender: true,
                                  queryMode: "local",
                                  listClass: 'x-combo-list-small',
                                  listeners:{
                                        select:{
                                            fn:function(comp, record, index) {
                                                if (comp.getValue() === "" || comp.getValue() === "&nbsp;"){
                                                   comp.setValue(null);
                                                }else{   
                                                    storeListadoServiciosBusquedaAvanzada.proxy.extraParams = { por : comp.getValue() };
                                                    storeListadoServiciosBusquedaAvanzada.load({params: {}});
                                                }
                                            }
                                        }
                                   }
                           },
			    {html:"&nbsp;",border:false,width:250},
			    {html:"&nbsp;",border:false,width:150},
			   {
                                  xtype: 'combobox',
                                  id: 'producto_plan',
                                  name: 'producto_plan',
                                  fieldLabel: 'Listado Servicios',
                                  emptyText: "Seleccione Por",
                                  width:350,
                                  triggerAction: 'all',
                                  displayField:'servicio',
                                  valueField: 'id_servicio',
                                  selectOnTab: true,
                                  store: storeListadoServiciosBusquedaAvanzada,              
                                  lazyRender: true,
                                  queryMode: "local",
                                  listClass: 'x-combo-list-small',
                                  listeners:{
                                        select:{
                                            fn:function(comp, record, index) {
                                                if (comp.getRawValue() === "" || comp.getRawValue() === "&nbsp;") 
                                                   comp.setValue(null);
                                            }
                                        }
                                   }
                            },
			    {html:"&nbsp;",border:false,width:150},
			    {
                                  xtype: 'combobox',
                                  id: 'estado_servicio_comercial',
                                  name: 'estado_servicio_comercial',
                                  fieldLabel: 'Estado Servicio',
                                  emptyText: "Seleccione",
                                  width:350,
                                  triggerAction: 'all',
                                  displayField:'estado_servicio_comercial',
                                  valueField: 'estado_servicio_comercial',
                                  selectOnTab: true,
                                  store: storeEstadosServicioBusquedaAvanzada,              
                                  lazyRender: true,
                                  queryMode: "remote",
                                  listClass: 'x-combo-list-small',
                                  listeners:{
                                        select:{
                                            fn:function(comp, record, index) {
                                                if (comp.getValue() === "" || comp.getValue() === "&nbsp;") 
                                                   comp.setValue(null);
                                            }
                                        }
                                   }
                              },
	  ];

      return itemsComercial;
}
function getFiltroSoporte(){
    var storeEmpresas = Ext.create('Ext.data.Store', {
        fields: ['opcion', 'valor'],
        data:
            [{
                    "opcion": "MEGADATOS",
                    "valor": "MD"
                }, {
                    "opcion": "TRANSTELCO",
                    "valor": "TTCO"
                },
                {
                    "opcion": "TELCONET",
                    "valor": "TN"
                }
            ]
    });
    
    var storeEstadosCaso = Ext.create('Ext.data.Store', {
        fields: ['opcion', 'valor'],
        data:
            [{
                    "opcion": "Asignado",
                    "valor": "Asignado"
                },
                {
                    "opcion": "Cerrado",
                    "valor": "Cerrado"
                }
            ]
    });
        
    storeCiudades = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: ajaxCiudad,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
                estado: 'Activo'
            }
        },
        fields:
            [
                {name: 'id_canton', mapping: 'id_canton'},
                {name: 'nombre_canton', mapping: 'nombre_canton'}
            ]
    });
    
    storeDepartamentosCiudad = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: ajaxDepartamento,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
                estado: 'Activo'
            }
        },
        fields:
            [
                {name: 'id_departamento', mapping: 'id_departamento'},
                {name: 'nombre_departamento', mapping: 'nombre_departamento'}
            ]
    }); 
                  
    var itemsSoporte = [
        {html: "&nbsp;", border: false, width: 150},        
        {
            xtype: 'textfield',
            id: 'txtNumeroCaso',
            fieldLabel: 'Numero de Caso',
            value: '',
            width: 350
        },
        {html: "&nbsp;", border: false, width: 150},
        {
            xtype: 'combobox',
            id: 'cmb_estadoCaso',
            name: 'cmb_estadoCaso',
            fieldLabel: 'Estado Caso:',
            emptyText: "",
            width: 350,
            editable: false,            
            displayField: 'opcion',
            valueField: 'valor',
            selectOnTab: true,
            store: storeEstadosCaso,
            lazyRender: true,
            queryMode: "local",
            listClass: 'x-combo-list-small'           
        },        
        {html: "&nbsp;", border: false, width: 250},        
        {html: "&nbsp;", border: false, width: 150},
        {
            xtype: 'combobox',
            id: 'cmb_empresa',
            name: 'cmb_empresa',
            fieldLabel: 'Empresa Asignada',
            emptyText: "",
            width: 350,
            triggerAction: 'all',
            displayField: 'opcion',
            valueField: 'valor',
            selectOnTab: true,
            store: storeEmpresas,
            lazyRender: true,            
            queryMode: "local",
            listClass: 'x-combo-list-small',
            listeners: {
                select: function(combo) {

                    Ext.getCmp('cmb_ciudad').reset();
                    Ext.getCmp('cmb_departamento').reset();                    

                    Ext.getCmp('cmb_ciudad').setDisabled(false);
                    Ext.getCmp('cmb_departamento').setDisabled(true);                    

                    presentarCiudades(combo.getValue());
                }
            },
        },
        {html: "&nbsp;", border: false, width: 150},
        {
            xtype: 'combobox',
            id: 'cmb_ciudad',
            name: 'cmb_ciudad',
            fieldLabel: 'Ciudad Asignado',
            emptyText: "",
            width: 350,
            triggerAction: 'all',
            displayField: 'nombre_canton',
            valueField: 'id_canton',
            selectOnTab: true,
            store: storeCiudades,
            lazyRender: true,
            disabled: true,
            queryMode: "remote",
            listClass: 'x-combo-list-small',
            listeners: {
                select: function(combo) {
                    Ext.getCmp('cmb_departamento').reset();                    

                    Ext.getCmp('cmb_departamento').setDisabled(false);                    

                    var empresa = Ext.getCmp('cmb_empresa').getValue();

                    presentarDepartamentosPorCiudad(combo.getValue(), empresa);
                }
            }
        },        
        {html: "&nbsp;", border: false, width: 250},
        {html: "&nbsp;", border: false, width: 150},
        {
            xtype: 'combobox',
            id: 'cmb_departamento',
            name: 'cmb_departamento',
            fieldLabel: 'Departamento Asignado',
            emptyText: "",
            width: 350,
            triggerAction: 'all',
            displayField: 'nombre_departamento',
            valueField: 'id_departamento',
            selectOnTab: true,
            store: storeDepartamentosCiudad,
            lazyRender: true,
            disabled: true,
            queryMode: "remote",
            listClass: 'x-combo-list-small'
        }
    ];

    return itemsSoporte;
}

function presentarCiudades(empresa){
      
    storeCiudades.proxy.extraParams = { empresa:empresa};
    storeCiudades.load();    
}
function presentarDepartamentosPorCiudad(id_canton , empresa){
  
    storeDepartamentosCiudad.proxy.extraParams = { id_canton:id_canton,empresa:empresa};
    storeDepartamentosCiudad.load();  
}

function getParamsMasivos(tipoEnvio,cmb_plantilla_email,cmb_plantilla_sms,tiempoEnvio,fechaComunicacion,tipo){
  
    var params = {};

    params['tipoFiltroModulo'] = tipoFiltroModulo;
    params['cmb_plantilla_email'] = cmb_plantilla_email;
    params['cmb_plantilla_sms'] = cmb_plantilla_sms;
    params['tiempoEnvio'] = tiempoEnvio;
    params['tipoEnvio'] = tipoEnvio;
    params['fechaComunicacion'] = fechaComunicacion;
    params['isMasivo'] = true;
    params['tipoNotificacion'] = tipo;


    if (tipoFiltroModulo === 'Tecnico') {

        params['tipoElemento'] = Ext.getCmp('cmb_elementosPadre').value;
        params['elemento'] = Ext.getCmp('cmb_elementos').value;
        params['oficina'] = Ext.getCmp('cmb_oficina').value;
        params['login2'] = Ext.getCmp('txtLogin').value;
        params['ciudad_punto'] = Ext.getCmp('txtCiudadPto').value;
        params['direccion_punto'] = Ext.getCmp('txtDireccionPto').value;
        params['estado_servicio'] = Ext.getCmp('sltEstadoServicio').value;
        params['estado_punto'] = Ext.getCmp('sltEstadoPunto').value;
        params['nombre_pe'] = Ext.getCmp('cmb_pe').value;
        params['protocolo'] = Ext.getCmp('cmb_protocolo').value;
        params['servicio'] = Ext.getCmp('cmb_servicio').value;
        params['ciudad'] = Ext.getCmp('cmb_ciudad').value;
    }
    else if (tipoFiltroModulo === 'Financiero')
    {

        idsOficinasGlobal = "";
        idsFormasPagoGlobal = "";
        // ...
        var idsOficinasSelected = "";
        for (var i = 0; i < itemsOfi.length; i++) {
            oficinaSeteada = Ext.getCmp('idOficina_' + i).value;
            if (oficinaSeteada == true) {
                if (idsOficinasSelected != null && idsOficinasSelected == "") {
                    idsOficinasSelected = idsOficinasSelected
                        + Ext.getCmp('idOficina_' + i).inputValue;
                } else {
                    idsOficinasSelected = idsOficinasSelected + ",";
                    idsOficinasSelected = idsOficinasSelected
                        + Ext.getCmp('idOficina_' + i).inputValue;
                }
            }
        }
        idsOficinasGlobal = idsOficinasSelected;

        if (idsOficinasGlobal != "") {

            numFacturasAbiertas = "";
            valorMontoDeuda = "";
            idFormaPago = "";
            idsBancosTarjetas = "";
            idsOficinas = "";
            idTipoNegocio = "";

            var idFormaPagoSelected = "";
            for (var i = 0; i < itemsFor.length; i++) {
                formaSeteada = Ext.getCmp('idForma_' + i).value;
                if (formaSeteada == true) {
                    if (idFormaPagoSelected != null && idFormaPagoSelected == "") {
                        idFormaPagoSelected = idFormaPagoSelected
                            + Ext.getCmp('idForma_' + i).inputValue;
                    }
                }
            }
            idsFormasPagoGlobal = idFormaPagoSelected;

            var idsBancosTarjetasSelected = "";
            if (itemsBancosTarjetas) {
                for (var i = 0; i < itemsBancosTarjetas.length; i++) {
                    bancosTarjetasSeteada = Ext.getCmp('idBancoTarjeta_' + i).value;
                    if (bancosTarjetasSeteada == true) {
                        if (idsBancosTarjetasSelected != null
                            && idsBancosTarjetasSelected == "") {
                            idsBancosTarjetasSelected = idsBancosTarjetasSelected
                                + Ext.getCmp('idBancoTarjeta_' + i).inputValue;
                        } else {
                            idsBancosTarjetasSelected = idsBancosTarjetasSelected + ",";
                            idsBancosTarjetasSelected = idsBancosTarjetasSelected
                                + Ext.getCmp('idBancoTarjeta_' + i).inputValue;
                        }
                    }
                }
            }

            numFacturasAbiertas = Ext.getCmp('facturasAbiertas').value;
            valorMontoDeuda = Ext.getCmp('montoCartera').value;
            idTipoNegocio = Ext.getCmp('tipoNegocio').value;
            feActivacion = Ext.getCmp('feActivacion').value;
            estado = Ext.getCmp('estado').value;
            idFormaPago = idFormaPagoSelected;
            idsBancosTarjetas = idsBancosTarjetasSelected;
            idsOficinas = idsOficinasSelected;
            cmbCiclos = Ext.getCmp('cmbCicloFacturacion').getValue();

            params['facturas_abiertas'] = numFacturasAbiertas;
            params['saldo'] = valorMontoDeuda;
            params['tipo_negocio'] = idTipoNegocio;
            params['forma_pago'] = idFormaPago;
            params['bancos_tarjetas'] = idsBancosTarjetas;
            params['oficinas'] = idsOficinas;
            params['estado'] = estado;
            params['feActivacion'] = feActivacion;
            params['intCiclo'] = cmbCiclos;


        }

    }
    else if (tipoFiltroModulo === 'Soporte')
    {
        params['numeroCaso']           = Ext.getCmp('txtNumeroCaso').value;
        params['estadoCaso']           = Ext.getCmp('cmb_estadoCaso').value;
        params['empresaAsignado']      = Ext.getCmp('cmb_empresa').value;
        params['ciudadAsignado']       = Ext.getCmp('cmb_ciudad').value;
        params['departamentoAsignado'] = Ext.getCmp('cmb_departamento').value;
    }
    else
    {
        params['forma_pago'] = Ext.getCmp('forma_pago_comercial').value;
        params['servicios_por'] = Ext.getCmp('servicios_por_comercial').value;
        params['producto_plan'] = Ext.getCmp('producto_plan').value;
        params['estado_servicio_comercial'] = Ext.getCmp('estado_servicio_comercial').value;
    }
   
   return params;
  
}
function buscar(){
    var boolError = false;
    
    if(!boolError)
    {
        store.removeAll();
		
	if(tipoFiltroModulo == 'Tecnico'){

	    store.getProxy().extraParams.jurisdiccionPe = Ext.getCmp('cmb_jurisdiccionPe').value;
	    store.getProxy().extraParams.pe = Ext.getCmp('cmb_pe').value;
	    store.getProxy().extraParams.protocolo = Ext.getCmp('cmb_protocolo').value;
	    store.getProxy().extraParams.servicio = Ext.getCmp('cmb_servicio').value;
	    store.getProxy().extraParams.ciudad = Ext.getCmp('cmb_ciudad').value;
	    store.getProxy().extraParams.tipoElemento = Ext.getCmp('cmb_elementosPadre').value;
	    store.getProxy().extraParams.elemento = Ext.getCmp('cmb_elementos').value;
	    store.getProxy().extraParams.oficina = Ext.getCmp('cmb_oficina').value;
	    store.getProxy().extraParams.login2 = Ext.getCmp('txtLogin').value;
	    store.getProxy().extraParams.ciudad_punto = Ext.getCmp('txtCiudadPto').value;
	    store.getProxy().extraParams.direccion_punto = Ext.getCmp('txtDireccionPto').value;
	    store.getProxy().extraParams.estado_servicio= Ext.getCmp('sltEstadoServicio').value;
	    store.getProxy().extraParams.estado_punto = Ext.getCmp('sltEstadoPunto').value;
	    store.getProxy().extraParams.tipoFiltroModulo = tipoFiltroModulo;
	    store.getProxy().extraParams.isMasivo = false;
	    store.load();
	
	}
	else if(tipoFiltroModulo == 'Financiero')
	{	  	  	  	  
	    idsOficinasGlobal = "";
	    idsFormasPagoGlobal = "";

            cmbCiclos = Ext.getCmp('cmbCicloFacturacion').getValue();
            if (boolPermisoEmpresas)
            {
              if (cmbCiclos <= 0)
              {
                  Ext.Msg.alert('Alerta',"Favor, seleccione un Ciclo de Facturacion");
                  return;
              }
            }
	    // ...
	    var idsOficinasSelected = "";
	    for (var i = 0; i < itemsOfi.length; i++) {
		    oficinaSeteada = Ext.getCmp('idOficina_' + i).value;
		    if (oficinaSeteada == true) {
			    if (idsOficinasSelected != null && idsOficinasSelected == "") {
				    idsOficinasSelected = idsOficinasSelected
						    + Ext.getCmp('idOficina_' + i).inputValue;
			    } else {
				    idsOficinasSelected = idsOficinasSelected + ",";
				    idsOficinasSelected = idsOficinasSelected
						    + Ext.getCmp('idOficina_' + i).inputValue;
			    }
		    }
	    }
	    idsOficinasGlobal = idsOficinasSelected;

	    if (idsOficinasGlobal != "") {
		    
		    numFacturasAbiertas = "";		    
		    valorMontoDeuda = "";
		    idFormaPago = "";
		    idsBancosTarjetas = "";
		    idsOficinas = "";
		    idTipoNegocio ="";		    

		    var idFormaPagoSelected = "";
		    for (var i = 0; i < itemsFor.length; i++) {
			    formaSeteada = Ext.getCmp('idForma_' + i).value;
			    if (formaSeteada == true) {
				    if (idFormaPagoSelected != null && idFormaPagoSelected == "") {
					    idFormaPagoSelected = idFormaPagoSelected
							    + Ext.getCmp('idForma_' + i).inputValue;
				    }
			    }
		    }
		    idsFormasPagoGlobal = idFormaPagoSelected;

		    var idsBancosTarjetasSelected = "";
		    if(itemsBancosTarjetas){
		    for (var i = 0; i < itemsBancosTarjetas.length; i++) {
			    bancosTarjetasSeteada = Ext.getCmp('idBancoTarjeta_' + i).value;
			    if (bancosTarjetasSeteada == true) {
				    if (idsBancosTarjetasSelected != null
						    && idsBancosTarjetasSelected == "") {
					    idsBancosTarjetasSelected = idsBancosTarjetasSelected
							    + Ext.getCmp('idBancoTarjeta_' + i).inputValue;
				    } else {
					    idsBancosTarjetasSelected = idsBancosTarjetasSelected + ",";
					    idsBancosTarjetasSelected = idsBancosTarjetasSelected
							    + Ext.getCmp('idBancoTarjeta_' + i).inputValue;
				    }
			    }
		      }
		    }
		      		      		      
		      numFacturasAbiertas = Ext.getCmp('facturasAbiertas').value;		      
		      valorMontoDeuda = Ext.getCmp('montoCartera').value;
		      idTipoNegocio = Ext.getCmp('tipoNegocio').value;
		      feActivacion = Ext.getCmp('feActivacion').value;
		      estado = Ext.getCmp('estado').value;
		      idFormaPago = idFormaPagoSelected;
		      idsBancosTarjetas = idsBancosTarjetasSelected;
		      idsOficinas = idsOficinasSelected;		      		      
		      
		      storeFinanciero.getProxy().extraParams.numFacturasAbiertas = numFacturasAbiertas;	
		      storeFinanciero.getProxy().extraParams.valorMontoDeuda = valorMontoDeuda;
		      storeFinanciero.getProxy().extraParams.idFormaPago = idFormaPago;
		      storeFinanciero.getProxy().extraParams.idsBancosTarjetas = idsBancosTarjetas;
		      storeFinanciero.getProxy().extraParams.idsOficinas = idsOficinas;
		      storeFinanciero.getProxy().extraParams.estado = estado;
		      storeFinanciero.getProxy().extraParams.idTipoNegocio = idTipoNegocio;		
		      storeFinanciero.getProxy().extraParams.feActivacion = feActivacion;	
                      storeFinanciero.getProxy().extraParams.ciclosFacturacion  = cmbCiclos;
		      storeFinanciero.removeAll();
		      storeFinanciero.load();
		      				      		      
	    
	    }else{
		Ext.Msg.alert('Alerta',"Favor, seleccione una oficina");
	    }
	    	  
	  
	}
    else if(tipoFiltroModulo === 'Soporte')
    {        	  	
	    storeSoporte.getProxy().extraParams.numeroCaso           = Ext.getCmp('txtNumeroCaso').value;
	    storeSoporte.getProxy().extraParams.estadoCaso           = Ext.getCmp('cmb_estadoCaso').value;         
	    storeSoporte.getProxy().extraParams.empresaAsignado      = Ext.getCmp('cmb_empresa').value;        
	    storeSoporte.getProxy().extraParams.ciudadAsignado       = Ext.getCmp('cmb_ciudad').value;
	    storeSoporte.getProxy().extraParams.departamentoAsignado = Ext.getCmp('cmb_departamento').value;	
        storeSoporte.getProxy().extraParams.tipoFiltroModulo     = tipoFiltroModulo;
	    storeSoporte.getProxy().extraParams.isMasivo = false;
	    storeSoporte.load();		
    }
    else
	{	  	 	  
	    store.getProxy().extraParams.forma_pago = Ext.getCmp('forma_pago_comercial').value;
	    store.getProxy().extraParams.servicios_por = Ext.getCmp('servicios_por_comercial').value;        
	    store.getProxy().extraParams.producto_plan = Ext.getCmp('producto_plan').value;        
	    store.getProxy().extraParams.estado_servicio_comercial = Ext.getCmp('estado_servicio_comercial').value;	    
	    store.getProxy().extraParams.tipoFiltroModulo = tipoFiltroModulo;
	    store.getProxy().extraParams.isMasivo = false;
	    store.load();
	  
	}
	
	
    }          
}
function limpiar(){    
      
    if(tipoFiltroModulo === 'Tecnico')
    {
        Ext.getCmp('cmb_jurisdiccionPe').value = "";
        Ext.getCmp('cmb_jurisdiccionPe').setRawValue("");
        Ext.getCmp('cmb_pe').value = "";
        Ext.getCmp('cmb_pe').setRawValue("");
        Ext.getCmp('cmb_protocolo').value = "";
        Ext.getCmp('cmb_protocolo').setRawValue("");
        Ext.getCmp('cmb_servicio').value = "";
        Ext.getCmp('cmb_servicio').setRawValue("");
        Ext.getCmp('cmb_ciudad').value = "";
        Ext.getCmp('cmb_ciudad').setRawValue("");
        Ext.getCmp('cmb_elementos').value = "";
        Ext.getCmp('cmb_elementos').setRawValue("");
        Ext.getCmp('cmb_elementosPadre').value = "";
        Ext.getCmp('cmb_elementosPadre').setRawValue("");
        Ext.getCmp('cmb_oficina').value = "";
        Ext.getCmp('cmb_oficina').setRawValue("");
        Ext.getCmp('txtLogin').value = "";
        Ext.getCmp('txtLogin').setRawValue("");
        Ext.getCmp('txtCiudadPto').value = "";
        Ext.getCmp('txtCiudadPto').setRawValue("");
        Ext.getCmp('txtDireccionPto').value = "";
        Ext.getCmp('txtDireccionPto').setRawValue("");
        Ext.getCmp('sltEstadoServicio').value = "Todos";
        Ext.getCmp('sltEstadoServicio').setRawValue("Todos");
        Ext.getCmp('sltEstadoPunto').value = "Todos";
        Ext.getCmp('sltEstadoPunto').setRawValue("Todos");
    }
    
    if(tipoFiltroModulo === 'Soporte')
    {
        Ext.getCmp('txtNumeroCaso').value = "";
        Ext.getCmp('txtNumeroCaso').setRawValue("");
        Ext.getCmp('cmb_estadoCaso').value = "";
        Ext.getCmp('cmb_estadoCaso').setRawValue("");
        Ext.getCmp('cmb_empresa').value = "";
        Ext.getCmp('cmb_empresa').setRawValue("");
        Ext.getCmp('cmb_ciudad').value = "";
        Ext.getCmp('cmb_ciudad').setRawValue("");
        Ext.getCmp('cmb_departamento').value = "";
        Ext.getCmp('cmb_departamento').setRawValue("");
    }
        
    store.removeAll();
    storeSoporte.removeAll();

    if (tipoFiltroModulo === 'Financiero')
    {
        sm.deselectAll();
        Ext.getCmp('tipoNegocio').reset();
        Ext.getCmp('facturasAbiertas').reset();
        Ext.getCmp('montoCartera').reset();
        idsOficinasGlobal = "";
        idsFormasPagoGlobal = "";
        for (var i = 0; i < itemsFor.length; i++) {
            formaSeteada = Ext.getCmp('idForma_' + i).setValue(false);
        }

        for (var i = 0; i < itemsOfi.length; i++) {
            oficinaSeteada = Ext.getCmp('idOficina_' + i).setValue(false);
        }

        Ext.getCmp('feActivacion').value = "";
        Ext.getCmp('feActivacion').setRawValue("");
        Ext.getCmp('cmbCicloFacturacion').reset();

    }

    storeFinanciero.removeAll();    

}

/************************************************************************ */
/*********************** ASIGNACION RESPONSABLE ************************* */
/************************************************************************ */
var winEnviarPlantilla;



function showEnviarMasivo() 
{   
    var boolEjecutar = true;
    if(tipoFiltroModulo != 'Financiero')
    {
        if(tipoFiltroModulo == 'Comercial' || tipoFiltroModulo == 'Tecnico')
        {
            if(gridC.getStore().data.items.length == 0)
            {
                Ext.Msg.alert('Alerta ', 'Debe realizar su busqueda primero');
                boolEjecutar = false;
            }
        }
        else
        {
            if(gridSoporte.getStore().data.items.length == 0)
            {
                Ext.Msg.alert('Alerta ', 'Debe realizar su busqueda primero');
                boolEjecutar = false;
            }
        }
    }    
    else if (gridServicios.getStore().data.items.length == 0 && tipoFiltroModulo == 'Financiero')
    {
        Ext.Msg.alert('Alerta ', 'Debe realizar su busqueda primero');
        boolEjecutar = false;
    }   
    
    if(boolEjecutar)
    {
        Ext.MessageBox.confirm('Enviar Notificacion Masiva', 'Se enviarán las notificaciones a todos los registros consultados, desea continuar?', function(btn) {
            if (btn == 'yes') {
                isMasivo = true;
                showEnviarPlantilla();
            }
            else {
                isMasivo = false;
            }
        });
    }
}

function showEnviarPlantilla()
{
    winEnviarPlantilla="";
    formPanelEnviarPlantilla= "";
    
    if (!winEnviarPlantilla)
    {      
        //******** html vacio...
        var iniHtmlVacio1 = '';           
        Vacio1 =  Ext.create('Ext.Component', {
            html: iniHtmlVacio1,
            width: 600,
            padding: 4,
            layout: 'anchor',
            style: { color: '#000000' }
        });  
           
        var i = 1;   		
                     
        //******** RADIOBUTTONS -- TIPOS DE ENVIO
        var iniHtml =   'Formas de Envio: '+
			'&nbsp;&nbsp;&nbsp;&nbsp;'+
			'<input type="checkbox" id="email" checked="" value="email" name="tipoEnvio" onclick="cambiarTipoPlantilla(this)">&nbsp;EMAIL&nbsp;&nbsp;&nbsp;'+
                        '<input type="checkbox" id="sms" value="sms" name="tipoEnvio" onclick="cambiarTipoPlantilla(this)">&nbsp;SMS&nbsp;&nbsp;&nbsp;&nbsp;'+
			'<input type="checkbox" id="todos" value="todos" name="tipoEnvio" onclick="cambiarTipoPlantilla(this)">&nbsp;Todos&nbsp;';
	if(isMasivo==true)
	iniHtml += '<br /><br />'+
	'Tipo Notificacion :'+	
	'&nbsp;&nbsp;&nbsp;&nbsp;'+
	'&nbsp;&nbsp;&nbsp;<input type="checkbox" checked="" id="masivo" value="masivo" name="masivo" onclick="cambiarEnvioMasivoTipo(this)">&nbsp;Masivo&nbsp;&nbsp;&nbsp;'+
	'<input type="checkbox" id="personalizado" value="personalizado" name="masivo" onclick="cambiarEnvioMasivoTipo(this)">&nbsp;Personalizado&nbsp;&nbsp;&nbsp;<br />';
       
	RadiosTiposEnvio =  Ext.create('Ext.Component', {
            html: iniHtml,
            width: 600,
            padding: 4,
            style: { color: '#000000' }
        });

        // **************** PLANTILLAS  ******************
        Ext.define('PlantillasList', {
            extend: 'Ext.data.Model',
            fields: [
                {name:'id_documento', type:'int'},
                {name:'nombre', type:'string'}		
            ]
        });           
	
	/*******************************************************************************/
	//Obtener TIPO de plantilla
	/*********************************************************************************/		
	var storePlantillasCorreo = Ext.create('Ext.data.Store', { 
              id: 'storePlantillas', 
              model: 'PlantillasList', 
              autoLoad: false, 
              proxy: { 
                   type: 'ajax',
                   url : 'getPlantillas',
               reader: {
                   type: 'json',
                   totalProperty: 'total',
                   root: 'encontrados',           
               },
	       extraParams: { 
                 	tipo: 'Notificacion Externa Correo',           
            	}
              }
         });    
	var storePlantillasSMS = Ext.create('Ext.data.Store', { 
              id: 'storePlantillas', 
              model: 'PlantillasList', 
              autoLoad: false, 
              proxy: { 
                   type: 'ajax',
                   url : 'getPlantillas',
               reader: {
                   type: 'json',
                   totalProperty: 'total',
                   root: 'encontrados',           
               },
	       extraParams: { 
                	tipo: 'Notificacion Externa SMS',           
            	}
              }
         });  
        combo_plantillas_correo = new Ext.form.ComboBox({
            id: 'cmb_plantilla_email',
            name: 'cmb_plantilla_email',
            fieldLabel: "Plantillas Correo",
            anchor: '100%',
            queryMode:'remote',
            width: 300,
            padding: 4,
            emptyText: 'Seleccione Plantilla Correo',
            store: storePlantillasCorreo,
            displayField: 'nombre',
            valueField: 'id_documento',
            layout: 'anchor',
            disabled: false
        });
	
	combo_plantillas_sms = new Ext.form.ComboBox({
            id: 'cmb_plantilla_sms',
            name: 'cmb_plantilla_sms',
            fieldLabel: "Plantillas SMS",
            anchor: '100%',
            queryMode:'remote',
            width: 300,
            padding: 4,
            emptyText: 'Seleccione Plantilla SMS',
            store: storePlantillasSMS,
            displayField: 'nombre',
            valueField: 'id_documento',
            layout: 'anchor',
            disabled: false
        });

		
        //******** RADIOBUTTONS -- URGENCIA ENVIO
        var iniHtml2 =   'Tiempo de Envio: '+
			'&nbsp;&nbsp;&nbsp;&nbsp;'+
			'<input type="radio" checked="" value="inmediato" name="tiempoEnvio" onchange="cambiarTiemposEnvio(this.value);">&nbsp;Envio inmediato' + 
                        '&nbsp;&nbsp;'+
                        '<input type="radio" value="programado" name="tiempoEnvio" onchange="cambiarTiemposEnvio(this.value);">&nbsp;Envio Programado';
        RadiosTiemposEnvio =  Ext.create('Ext.Component', {
            html: iniHtml2,
            width: 600,
            padding: 4,
            style: { color: '#000000' }
        });
		
        DTFechaComunicacion = new Ext.form.DateField({
            id: 'fechaComunicacion',
            fieldLabel: 'Fecha Comunicacion',
            labelAlign : 'left',
            xtype: 'datefield',
            format: 'Y-m-d',
            editable: false,
			disabled: true,
            padding: 4,
			minValue: new Date(),
            value:new Date()
        }); 
		
        //******** html vacio...
        var iniHtmlVacio = '';           
        Vacio =  Ext.create('Ext.Component', {
            html: iniHtmlVacio,
            width: 600,
            padding: 8,
            layout: 'anchor',
            style: { color: '#000000' }
        });
                        
        formPanelEnviarPlantilla = Ext.create('Ext.form.Panel', {
            width:700,
            height:200,
            BodyPadding: 10,
            bodyStyle: "background: white; padding:10px; border: 0px none;",
            frame: true,
            items: [Vacio1, RadiosTiposEnvio, Vacio1, combo_plantillas_correo, Vacio1, combo_plantillas_sms, Vacio1, RadiosTiemposEnvio, Vacio1, DTFechaComunicacion, Vacio],
            buttons:[
                {
                    text: 'Enviar Comunicacion',
                    handler: function(){
                        grabarEnviarPlantilla();                        
                    }
                },
                {
                    text: 'Cancelar',
                    handler: function(){  
                        cierraVentanaEnviarPlantilla();
                    }
                }
            ]
        });  
              
        Ext.getCmp('cmb_plantilla_email').setVisible(true);
	Ext.getCmp('cmb_plantilla_sms').setVisible(false);
        Ext.getCmp('fechaComunicacion').setVisible(false);
               
	winEnviarPlantilla = Ext.widget('window', {
            title: 'Formulario Envio Plantilla',
            width: 740,
            height:250,
            minHeight: 250,
            layout: 'fit',
            resizable: false,
            modal: true,
            closabled: false,
            items: [formPanelEnviarPlantilla]
        });
    }
    
    winEnviarPlantilla.show();    
}

function cambiarTiemposEnvio(valor)
{
    if(valor == "programado")
    {
        Ext.getCmp('fechaComunicacion').setVisible(true);
		Ext.getCmp('fechaComunicacion').setDisabled(false);
	}
	else
	{
		Ext.getCmp('fechaComunicacion').setVisible(false);
		Ext.getCmp('fechaComunicacion').setDisabled(true);
	}
	
}

function cierraVentanaEnviarPlantilla(){
    winEnviarPlantilla.close();
    winEnviarPlantilla.destroy();
}  


function grabarEnviarPlantilla(){       
          
     Ext.MessageBox.show({
	    title: isMasivo==false?'Enviando Comunicacion':'Enviando Comunicacion Masiva',
	    progressText: 'Enviando...',
	    width:300,
	    wait:true,
	    waitConfig: {interval:200},
	    icon:'ext-mb-download', 
	    animEl: 'buttonID',
	    progress:true,
	    closable:false
    });    
    var f = function(){
	  
	  Ext.MessageBox.hide();
	  Ext.MessageBox.show({
	      title: 'Result',
	      width:300,
	      msg: 'Comunicacion Enviada',
	      buttons: Ext.MessageBox.OK,
	    
	  });           
    };
        
    var tipoEnvio = $("input[name='tipoEnvio']:checked").val();		
    var tiempoEnvio = $("input[name='tiempoEnvio']:checked").val();
    
    var cmb_plantilla_email = Ext.getCmp('cmb_plantilla_email').value;
    var cmb_plantilla_sms   = Ext.getCmp('cmb_plantilla_sms').value;
    var fechaComunicacion   = Ext.getCmp('fechaComunicacion').value;	
  
    if(isMasivo==false)
    {
	var param = "";
	if(tipoFiltroModulo != 'Financiero')
        {
	    if (chkBoxPuntoServicio.getSelection().length > 0)
            {
                for (var intForIndex = 0; intForIndex < chkBoxPuntoServicio.getSelection().length; intForIndex++)
                {
                    param = param + chkBoxPuntoServicio.getSelection()[intForIndex].data.idPunto + '-';
                }
            } else
            {
                Ext.Msg.alert('Alerta ', 'Seleccione por lo menos un registro de la lista');
            }
        }
        else{
            if (chkBoxFinancieroPuntoServicio.getSelection().length > 0)
            {
                for (var intForIndex = 0; intForIndex < chkBoxFinancieroPuntoServicio.getSelection().length; intForIndex++)
                {
                    param = param + chkBoxFinancieroPuntoServicio.getSelection()[intForIndex].data.idPunto + '-';
                }
            } else
            {
                Ext.Msg.alert('Alerta ', 'Seleccione por lo menos un registro de la lista');
            }
        }
        param = param.substring(0,(param.length)-1);
        if(validarPlantillasEscogidas(tipoEnvio,cmb_plantilla_email,cmb_plantilla_sms))
        {
            Ext.Ajax.request({
                    url:    "enviarAjax",
                    method: 'post',
                    params: {
                        param : param,
                        tipoEnvio: tipoEnvio,
                        tipoNotificacion:tipoEnvioMSG,
                        tiempoEnvio: tiempoEnvio,
                        cmb_plantilla_email: cmb_plantilla_email,
                        cmb_plantilla_sms: cmb_plantilla_sms,
                        fechaComunicacion: fechaComunicacion
                    },
                    success: function(response){

                            f();

                            var json = Ext.JSON.decode(response.responseText);

                            if(json.success == true)
                            {
                                    Ext.Msg.alert('Mensaje ', json.mensaje);
                                    cierraVentanaEnviarPlantilla();
                            }
                            else
                            {
                                    Ext.Msg.alert('Alerta ',json.mensaje);
                            }
                    }
            });
	  }
	  else
	  {
	      Ext.Msg.alert('Alerta ', 'Seleccione por lo menos un registro de la lista');
	  }
    }else{	   

	   var formaNot = $("input[name='masivo']:checked").val();		   	   
      
	   if(validarPlantillasEscogidas(tipoEnvio,cmb_plantilla_email,cmb_plantilla_sms)){
		  var params = getParamsMasivos(tipoEnvio,cmb_plantilla_email,cmb_plantilla_sms,tiempoEnvio,fechaComunicacion,formaNot == 'masivo'?'masivo':'personalizado');				  
		  
		  Ext.Ajax.request({
			  url:    "enviarMasivosAjax",
			  method: 'post',			
			  params: params,			    			 
			  success: function(response){	
			    
				  f();		
				  
				  var json = Ext.JSON.decode(response.responseText);					    					   
				  if(json.success == true)
				  {
					  Ext.Msg.alert('Mensaje ', json.mensaje);
					  cierraVentanaEnviarPlantilla();
				  }
				  else
				  {
					  Ext.Msg.alert('Alerta ',json.mensaje);							 
				  }
			  }				  
		  });
	
	}else{
	    Ext.Msg.alert('Alerta ', 'Debe escoger la plantilla a utilizar');			  
	}      
    }	
}
function cambiarTipoPlantilla(el){    
    switch(el.id){
      case 'sms':
	  if($("#"+el.id).is(':checked')){	  
	    Ext.getCmp('cmb_plantilla_email').setVisible(false);
	    Ext.getCmp('cmb_plantilla_sms').setVisible(true);
	    $("#email").attr('checked', false);  
	    $("#todos").attr('checked', false);  	
	  }else $("#sms").attr('checked', true);  	    
	break;
      case 'email':
	  if($("#"+el.id).is(':checked')){	  
	    Ext.getCmp('cmb_plantilla_email').setVisible(true);
	    Ext.getCmp('cmb_plantilla_sms').setVisible(false);
	    $("#sms").attr('checked', false);  
	    $("#todos").attr('checked', false);  
	  }else $("#email").attr('checked', true);  	    	
	break;
      case 'todos':
	  if($("#"+el.id).is(':checked')){
	    Ext.getCmp('cmb_plantilla_email').setVisible(true);
	    Ext.getCmp('cmb_plantilla_sms').setVisible(true);
	    $("#sms").attr('checked', false);  
	    $("#email").attr('checked', false);  
	  }else $("#todos").attr('checked', true);  	    	  
	break;      
    }             
}
function cambiarEnvioMasivoTipo(el){
    switch(el.id){
      case 'masivo':
	  if($("#"+el.id).is(':checked')){	  	   
	    $("#personalizado").attr('checked', false); 
	    $("#masivo").attr('checked', true);
	  }	    
	break;
      case 'personalizado':
	 if($("#"+el.id).is(':checked')){	  	   
	    $("#personalizado").attr('checked', true); 
	    $("#masivo").attr('checked', false);
	  }		    	
	break;    
    }             
  
}
function validarPlantillasEscogidas(tipo,email,sms){      
  
    if(tipo=='email' && email==null )return false;
    if(tipo=='sms' && sms==null )return false;
    if(tipo =='todos' &&  (sms==null || email==null) )return false;
    return true;
}


/* ************************************************* */
            /*  FUNCION  DE EXPORTAR  */
/* ************************************************* */
function exportarExcel(){             		
  
            idsOficinasGlobal = "";
	    idsFormasPagoGlobal = "";
	    
	    var idsOficinasSelected = "";
	    for (var i = 0; i < itemsOfi.length; i++) {
		    oficinaSeteada = Ext.getCmp('idOficina_' + i).value;
		    if (oficinaSeteada == true) {
			    if (idsOficinasSelected != null && idsOficinasSelected == "") {
				    idsOficinasSelected = idsOficinasSelected
						    + Ext.getCmp('idOficina_' + i).inputValue;
			    } else {
				    idsOficinasSelected = idsOficinasSelected + ",";
				    idsOficinasSelected = idsOficinasSelected
						    + Ext.getCmp('idOficina_' + i).inputValue;
			    }
		    }
	    }
	    idsOficinasGlobal = idsOficinasSelected;

	    if (idsOficinasGlobal != "") {
		    
		    numFacturasAbiertas = "";		    
		    valorMontoDeuda = "";
		    idFormaPago = "";
		    idsBancosTarjetas = "";
		    idsOficinas = "";
		    idTipoNegocio ="";		    

		    var idFormaPagoSelected = "";
		    for (var i = 0; i < itemsFor.length; i++) 
		    {
			    formaSeteada = Ext.getCmp('idForma_' + i).value;
			    if (formaSeteada == true) 
			    {
				    if (idFormaPagoSelected != null && idFormaPagoSelected == "") 
				    {
					    idFormaPagoSelected = idFormaPagoSelected + Ext.getCmp('idForma_' + i).inputValue;
				    }
			    }
		    }
		    idsFormasPagoGlobal = idFormaPagoSelected;

		    var idsBancosTarjetasSelected = "";
		    if(itemsBancosTarjetas)
		    {
			  for (var i = 0; i < itemsBancosTarjetas.length; i++) 
			  {
				  bancosTarjetasSeteada = Ext.getCmp('idBancoTarjeta_' + i).value;
				  if (bancosTarjetasSeteada == true) 
				  {
					  if (idsBancosTarjetasSelected != null  && idsBancosTarjetasSelected == "") 
					  {
						  idsBancosTarjetasSelected = idsBancosTarjetasSelected + Ext.getCmp('idBancoTarjeta_' + i).inputValue;
					  } 
					  else 
					  {
						  idsBancosTarjetasSelected = idsBancosTarjetasSelected + ",";
						  idsBancosTarjetasSelected = idsBancosTarjetasSelected + Ext.getCmp('idBancoTarjeta_' + i).inputValue;
					  }
				  }
			    }
		    }
		      		      		      
		    numFacturasAbiertas = Ext.getCmp('facturasAbiertas').value;		      
		    valorMontoDeuda = Ext.getCmp('montoCartera').value;
		    idTipoNegocio = Ext.getCmp('tipoNegocio').value;		   
		    estado = Ext.getCmp('estado').value;
		    idFormaPago = idFormaPagoSelected;
		    idsBancosTarjetas = idsBancosTarjetasSelected;
		    idsOficinas = idsOficinasSelected;		    		   
                    if (tipoFiltroModulo === 'Financiero' && boolPermisoEmpresas)
                    {
                        cmbCiclos = Ext.getCmp('cmbCicloFacturacion').getValue();
                    }
                    else
                    {
                        cmbCiclos = "";
                    }
        				      
		    $('#hid_facturas').val(numFacturasAbiertas );
		    $('#hid_cartera').val(valorMontoDeuda);
		    $('#hid_tipo_negocio').val(idTipoNegocio);
		    $('#hid_estado_servicio').val(estado);		    
		    $('#hid_oficinas').val(idsOficinas);     		
		    $('#hid_forma_pago').val(idFormaPago);
		    $('#hid_bancos').val(idsBancosTarjetas);
                    $('#hid_ciclos').val(cmbCiclos);

		    document.forms[0].submit();
      
	    }
}