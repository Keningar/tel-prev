            Ext.require([
                '*',
                'Ext.tip.QuickTipManager',
                'Ext.window.MessageBox',
                'Ext.ux.form.field.BoxSelect'    
            ]);

            var itemsPerPage = 30;
            var store='';
            var estado_id='';
            var area_id='';
            var login_id='';
            var tipo_asignacion='';
            var pto_sucursal='';
            var idClienteSucursalSesion;


            Ext.onReady(function(){
                 
        Ext.define('BancosList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_banco', type:'int'},
            {name:'descripcion_banco', type:'string'}
        ]
    });		
        storeBancos = Ext.create('Ext.data.Store', {
                model: 'BancosList',
                autoLoad: false,
                proxy: {
                    type: 'ajax',
                    url : url_lista_bancos_contables,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    }
                }
        });
        storeBancos.load();

        Ext.define('CuentasList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'idCuenta', type:'int'},
            {name:'descripcion', type:'string'}
        ]
        });
     Ext.define('TiposCuentaList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_cuenta', type:'int'},
            {name:'descripcion_cuenta', type:'string'}
        ]
    });
     storeCuentas = Ext.create('Ext.data.Store', {
            model: 'TiposCuentaList',
            proxy: {
                type: 'ajax',
                url : url_lista_cuentas_bancos_contables,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams:{id_banco:'',es_tarjeta:''}
            }
    }); 	
     
        var combo_bancos_naf = new Ext.form.ComboBox({
            id: 'cmb_bancos_naf',
            name: 'cmb_bancos_naf',
            fieldLabel: false,
            anchor: '100%',
            queryMode:'local',
            width: 200,
            emptyText: 'Seleccione Banco',
            store:storeBancos,
            displayField: 'descripcion_banco',
            valueField: 'id_banco',
            listeners:{
                select:{fn:function(combo, value) {
                    Ext.getCmp('cmb_cuentas_naf').reset();
                    storeCuentas.proxy.extraParams = {id_banco: combo.getValue(),es_tarjeta:'N'};
                    storeCuentas.load({params: {}});

                }}
            }
    });


        var combo_cuentas_naf = new Ext.form.ComboBox({
            id: 'cmb_cuentas_naf',
            name: 'cmb_cuentas_naf',
            fieldLabel: false,
            anchor: '100%',
            queryMode:'local',
            width: 200,
            emptyText: 'Seleccione Cuenta',
            store:storeCuentas,
            displayField: 'descripcion_cuenta',
            valueField: 'id_cuenta',
            listeners:{
                select:{fn:function(combo, value) {
                }}
            }
    });
	
            //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		
            DTFechaDesde = new Ext.form.DateField({
                    id: 'fechaDesde',
                    fieldLabel: 'Desde',
                    labelAlign : 'left',
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    width:325
            });
            DTFechaHasta = new Ext.form.DateField({
                    id: 'fechaHasta',
                    fieldLabel: 'Hasta',
                    labelAlign : 'left',
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    width:325
            });


            //CREAMOS DATA STORE PARA EMPLEADOS
            Ext.define('modelFormas', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'idformapago', type: 'string'},
                    {name: 'codigo',  type: 'string'},
                    {name: 'descripcion',  type: 'string'}                    
                ]
            });			
            var formaspago_store = Ext.create('Ext.data.Store', {
		    autoLoad: false,
		    model: "modelFormas",
		    proxy: {
		        type: 'ajax',
		        url : url_lista_formaspago,
		        reader: {
		            type: 'json',
		            root: 'formas'
                        }
                    }
            });	



    var baseExampleConfig = {
        id:'idformasmultiselect',
        name:'idformasmultiselect',
        fieldLabel: 'formas',
        displayField: 'descripcion',
        valueField: 'idformapago',
        width: 325,
        labelWidth: 130,
        emptyText: 'Seleccione',
        store: formaspago_store,
        mode: 'local'
    };


    var formaspagomultiselect_cmb = Ext.create('Ext.ux.form.field.BoxSelect', baseExampleConfig);
    


                Ext.define('ListaDetalleModel', {
                    extend: 'Ext.data.Model',
                    fields: [{name:'id', type: 'int'},
                            {name:'numero', type: 'string'},
                            {name:'punto', type: 'string'},
                            {name:'valor', type: 'string'},
                            {name:'formaPago', type: 'string'},
                            {name:'comentario', type: 'string'},
                            {name:'fechaCreacion', type: 'string'},
                            {name:'usuarioCreacion', type: 'string'},
                            {name:'estado', type: 'string'},
                            {name:'linkVer', type: 'string'}
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
                            root: 'pagos',
                            totalProperty: 'total'
                        },
                        extraParams:{fechaDesde:'',fechaHasta:'', banco:'', cuenta:''},
                        simpleSortMode: true
                    },
                    listeners: {
                        beforeload: function(store){
				store.getProxy().extraParams.fechaDesde= Ext.getCmp('fechaDesde').getValue();
				store.getProxy().extraParams.fechaHasta= Ext.getCmp('fechaHasta').getValue();
                                //console.log(Ext.getCmp('idformasmultiselect').lastValue);
                                store.getProxy().extraParams.formapago= Ext.getCmp('idformasmultiselect').lastValue;     
                        },
                        load: function(store){
                            store.each(function(record) {
                                //idClienteSucursalSesion = record.data.idClienteSucursalSesion;
                            });
                        }
                    }
                });

                store.load({params: {start: 0, limit: 30}});    



                 var sm = new Ext.selection.CheckboxModel( {
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



            function depositarAlgunos(){
                var param = '';
                if(sm.getSelection().length > 0)
                {
                  var estado = 0;
                  for(var i=0 ;  i < sm.getSelection().length ; ++i)
                  {
                    param = param + sm.getSelection()[i].data.id;

                    if(sm.getSelection()[i].data.estado == 'Eliminado')
                    {
                      estado = estado + 1;
                    }
                    if(i < (sm.getSelection().length -1))
                    {
                      param = param + '|';
                    }
                  }      
                  if(estado == 0)
                  {
                   if ((Ext.getCmp('cmb_bancos_naf').getValue())&&(Ext.getCmp('cmb_bancos_naf').getValue()))
                   { 
                       Ext.Msg.confirm('Alerta','Se marcaran como depositados los pagos seleccionados. Desea continuar?', function(btn){
                        if(btn=='yes'){
                            Ext.Ajax.request({
                                url: url_marcar_depositado_ajax,
                                method: 'post',
                                params: { param : param, banco:Ext.getCmp('cmb_bancos_naf').getValue(), idcuenta:Ext.getCmp('cmb_cuentas_naf').getValue(), cuenta:Ext.getCmp('cmb_cuentas_naf').rawValue},
                                success: function(response){
                                    var text = response.responseText;
                                    store.load();
                                },
                                failure: function(result)
                                {
                                    Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                }
                             });
                            }
                        });
                    }
                    else
                    {
                        Ext.Msg.alert('Error','Debe seleccionar el banco y la cuenta donde se realiza el deposito');
                    }
                  }
                  else
                  {
                    alert('Por lo menos uno de las registro se encuentra en estado ELIMINADO');
                  }
                }
                else
                {
                  alert('Seleccione por lo menos un registro de la lista');
                }
            }


                var listView = Ext.create('Ext.grid.Panel', {
                    width:800,
                    height:275,
                    collapsible:false,
                    title: '',
                    selModel: sm,
                    dockedItems: [ {
                                    xtype: 'toolbar',
                                    dock: 'top',
                                    align: '->',
                                    items: [
                                        //tbfill -> alinea los items siguientes a la derecha
                                        combo_bancos_naf,
                                        combo_cuentas_naf,
                                        { xtype: 'tbfill' },
                                        /*{
                                        iconCls: 'icon_add',
                                        text: 'Add',    
                                        scope: this,
                                        handler: function(){}
                                    },*/ {
                                        iconCls: 'icon_bank',
                                        text: 'Generar',
                                        disabled: false,
                                        itemId: 'depositar',
                                        scope: this,
                                        handler: function(){ depositarAlgunos();}
                                    }]}],                    
                    renderTo: Ext.get('lista_pagos'),
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
                    listeners:{
                            itemdblclick: function( view, record, item, index, eventobj, obj ){
                                var position = view.getPositionByEvent(eventobj),
                                data = record.data,
                                value = data[this.columns[position.column].dataIndex];
                                Ext.Msg.show({
                                    title:'Copiar texto?',
                                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                                    buttons: Ext.Msg.OK,
                                    icon: Ext.Msg.INFORMATION
                                });
                            }
                    },
                    columns: [new Ext.grid.RowNumberer(),  
                            {
                        text: 'Numero',
                        width: 100,
                        dataIndex: 'numero'
                    },{
                        text: 'Punto',
                        width: 115,
                        dataIndex: 'punto'
                    },{
                        text: 'Forma Pago',
                        dataIndex: 'formaPago',
                        align: 'right',
                        width: 120			
                    },{
                        text: 'Valor',
                        dataIndex: 'valor',
                        align: 'right',
                        width: 70			
                    },{
                        text: 'Comentario',
                        dataIndex: 'comentario',
                        align: 'right',
                        flex: 100			
                    },{
                        text: 'Fecha Creacion',
                        dataIndex: 'fechaCreacion',
                        align: 'right',
                        flex: 170			
                    },{
                        text: 'Estado',
                        dataIndex: 'estado',
                        align: 'right',
                        flex: 110
                    },{
                        text: 'Acciones',
                        width: 90,
                        renderer: renderAcciones
                    }
                    
                    ]
                });            

            
            function renderAcciones(value, p, record) {
                    var iconos='';
                    iconos=iconos+'<b><a href="'+record.data.linkVer+'" onClick="" title="Ver" class="button-grid-show"></a></b>';
                    return Ext.String.format(
                                    iconos,
                        value
                    );
            }



            var filterPanel = Ext.create('Ext.panel.Panel', {
                bodyPadding: 7,  // Don't want content to crunch against the borders
                //bodyBorder: false,
                border:false,
                //border: '1,1,0,1',
                buttonAlign: 'center',
                layout:{
                    type:'table',
                    columns: 5,
                    align: 'left'
                },
                bodyStyle: {
                            background: '#fff'
                },                     
                defaults: {
                    // applied to each contained panel
                    bodyStyle: 'padding:10px'
                },
                collapsible : true,
                collapsed: true,
                width: 800,
                title: 'Criterios de busqueda',
                buttons: [
                        {
                            text: 'Buscar',
                            iconCls: "icon_search",
                            handler: Buscar
                        },
                        {
                            text: 'Limpiar',
                            iconCls: "icon_limpiar",
                            handler: Limpiar
                        }
                ],                
                items: [
                                {html:"&nbsp;",border:false,width:50},
                                DTFechaDesde,
                                {html:"&nbsp;",border:false,width:50},
                                DTFechaHasta,
                                {html:"&nbsp;",border:false,width:50},
                                {html:"&nbsp;",border:false,width:50},
                                formaspagomultiselect_cmb,                               
                                {html:"&nbsp;",border:false,width:50},
                                                                
                ],	
                renderTo: 'filtro_pagos'
            }); 
      

	function Buscar(){
		if  (( Ext.getCmp('fechaDesde').getValue())&&(Ext.getCmp('fechaHasta').getValue()) )
		{
			if (Ext.getCmp('fechaDesde').getValue() > Ext.getCmp('fechaHasta').getValue())
			{
			   Ext.Msg.show({
			   title:'Error en Busqueda',
			   msg: 'Por Favor para realizar la busqueda Fecha Desde debe ser fecha menor a Fecha Hasta.',
			   buttons: Ext.Msg.OK,
			   animEl: 'elId',
			   icon: Ext.MessageBox.ERROR
				});		 

			}
			else
			{
				store.load({params: {start: 0, limit: 30}});
			}
		}
		else
		{
                    store.load({params: {start: 0, limit: 30}});
		}	
	}
        
        function Limpiar(){   
            Ext.getCmp('fechaDesde').setValue('');
            Ext.getCmp('fechaHasta').setValue('');
            Ext.getCmp('idformasmultiselect').setValue('');
        }


});
