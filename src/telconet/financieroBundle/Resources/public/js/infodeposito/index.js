            Ext.require([
                '*',
                'Ext.tip.QuickTipManager',
                'Ext.window.MessageBox',
                'Ext.ux.form.field.BoxSelect'    
            ]);

            var itemsPerPage = 'total';
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
                    timeout:9000000,
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

        Ext.define('OficinaList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_oficina', type:'int'},
            {name:'nombre_oficina', type:'string'}
        ]
    });		
    
        storeOficinas = Ext.create('Ext.data.Store', {
                model: 'OficinaList',
                autoLoad: false,
                proxy: {
                    timeout:900000,
                    type: 'ajax',
                    url : url_lista_oficinas,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    }
                }
        });
        storeOficinas.load();

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
                timeout:900000,
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
    
    //store que recepta las cuentas bancarias de la empresa en sesion
    storeCtasBancariasEmpresa = Ext.create('Ext.data.Store', 
    {
        model: 'TiposCuentaList',
        proxy: 
        {
            timeout:9000000,
            type: 'ajax',
            url : url_lista_ctas_bancarias_empresa,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams:{opcionModulo:'DEPOSITOS'}
        }
    });
    storeCtasBancariasEmpresa.load();
    
    //Combo que muestra las cuentas bancarias de la empresa para las formas de pago de depositos
    combo_ctas_bancarias_empresa = new Ext.form.ComboBox
    ({
            id           : 'cmb_ctas_bancarias_empresa',
            name         : 'cmb_ctas_bancarias_empresa',
            fieldLabel   : false,
            anchor       : '100%',
            queryMode    : 'local',
            width        : 350,
            emptyText    : 'Seleccione cuenta bancaria empresa',
            store        : storeCtasBancariasEmpresa,
            displayField : 'descripcion_cuenta',
            valueField   : 'id_cuenta',
            listeners:
            {
                select:{fn:function(combo, value) {  }}
            }
    });
	
        var acumulado = new Ext.form.Display({
	  xtype: 'displayfield',
	  id: 'acumulado',
	  name: 'acumulado',
	  fieldLabel: false,
	  width: 100,
	  value: 0,
	  border: 1,
	      style: {
		  borderColor: '#B5B8C8',
		  borderStyle: 'solid'
	      }	  
	});
	
        var combo_oficinas = new Ext.form.ComboBox({
            id: 'cmb_oficinas',
            name: 'cmb_oficinas',
            fieldLabel: 'Oficina',
            anchor: '100%',
            queryMode:'local',
            width: 400,
            emptyText: 'Seleccione Oficina',
            store:storeOficinas,
            displayField: 'nombre_oficina',
            valueField: 'id_oficina',
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
            TFCreador = new Ext.form.TextField({
                id: 'creador',
                name: 'creador',
                labelAlign:'left',
                fieldLabel: 'Creado por',
                xtype: 'textfield',
                width: '160px'
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
                        timeout:900000,
		        type: 'ajax',
		        url : url_lista_formaspago,
		        reader: {
		            type: 'json',
		            root: 'formas'
                        }
                    }
            });	



    var baseExampleConfig = 
    {
        id          :'idformasmultiselect',
        name        :'idformasmultiselect',
        fieldLabel  : 'Formas de Pago',
        displayField: 'descripcion',
        valueField  : 'idformapago',
        width       : 325,
        labelWidth  : 130,
        emptyText   : 'Seleccione',
        store       : formaspago_store,
        mode        : 'local',
        listeners: 
        {
            change: function( combo, newValue, oldValue, eOpts )
            {
                if((newValue.toUpperCase().search('TRANSFERENCIA')>=0 && (newValue!='')) &&  
                  (newValue.toUpperCase().search('EFECTIVO')>=0 
                  || newValue.toUpperCase().search('CHEQUE')>=0 
                  || newValue.toUpperCase().search('DEPOSITO')>=0))
                {
                    alert('No se puede seleccionar Transferencia junto con Efectivo, Cheque o depositos');
                    combo.setValue(new Array());
                }                  
            }
        }
    };


    var formaspagomultiselect_cmb = Ext.create('Ext.ux.form.field.BoxSelect', baseExampleConfig);
    


                Ext.define('ListaDetalleModel', {
                    extend: 'Ext.data.Model',
                    fields: [{name:'id', type: 'int'},
                            {name:'numero', type: 'string'},
                            {name:'cliente', type: 'string'},
                            {name:'punto', type: 'string'},
                            {name:'valor', type: 'string'},
                            {name:'formaPago', type: 'string'},
                            {name:'comentario', type: 'string'},
                            {name:'fechaCreacion', type: 'string'},
                            {name:'usuarioCreacion', type: 'string'},
                            {name:'estado', type: 'string'},
                            {name:'oficina', type: 'string'},
                            {name:'linkVer', type: 'string'}
                            ]
                }); 


                store = Ext.create('Ext.data.JsonStore', {
                    model: 'ListaDetalleModel',
                            pageSize: itemsPerPage,
                    proxy: {
                        timeout:900000,
                        type: 'ajax',
                        url: url_grid,
                        reader: {
                            type: 'json',
                            root: 'pagos',
                            totalProperty: 'total'
                        },
                        extraParams:{fechaDesde:'',fechaHasta:'', banco:'', cuenta:'', idOficina:''},
                        simpleSortMode: true
                    },
                    listeners: {
                        beforeload: function(store){
				store.getProxy().extraParams.fechaDesde= Ext.getCmp('fechaDesde').getValue();
				store.getProxy().extraParams.fechaHasta= Ext.getCmp('fechaHasta').getValue();
                                store.getProxy().extraParams.creadopor= Ext.getCmp('creador').getValue();
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

                store.load({params: {start: 0, limit: itemsPerPage}});    



    var sm = new Ext.selection.CheckboxModel( {    
        checkOnly: true,
        listeners:
        {
            selectionchange: function(selectionModel, selected, options)
            {			  			      
                var records = listView.getSelectionModel().getSelection();
                result      = 0;
                Ext.each(records, function(record)
                {                          
                    result += record.get('valor') * 1;
                });
                result=roundNumber(result, 2);
                Ext.getCmp('acumulado').setValue(result);                            
            },
            beforeselect: function( item, fila, index, eOpts )
            {		                      
                var intDepositos    = 0;
                var intNotasCredito = 0;
                var records         = listView.getSelectionModel().getSelection();
                Ext.each(records, function(record)
                {   
                    if(record.get('formaPago').toUpperCase().search('TRANSFERENCIA')>=0)
                    {
                        intNotasCredito=intNotasCredito+1; 
                    }
                    if (record.get('formaPago').toUpperCase().search('EFECTIVO')>=0 
                    || record.get('formaPago').toUpperCase().search('CHEQUE')>=0 
                    || record.get('formaPago').toUpperCase().search('DEPOSITO')>=0)
                    {
                        intDepositos=intDepositos+1;
                    }                           
                });

                if(intNotasCredito==0 && intDepositos>0 && (fila.get('formaPago').toUpperCase().search('TRANSFERENCIA')>=0))
                {
                    alert('No se puede seleccionar Transferencias junto con Efectivo, Cheque o depositos');                                
                    return false;
                }
                else
                {
                    if(intNotasCredito>0 && intDepositos==0 && 
                    (fila.get('formaPago').toUpperCase().search('EFECTIVO')>=0 
                    || fila.get('formaPago').toUpperCase().search('CHEQUE')>=0 
                    || fila.get('formaPago').toUpperCase().search('DEPOSITO')>=0))
                    {
                        alert('No se puede seleccionar Transferencias junto con Efectivo, Cheque o depositos');                                    
                        return false;
                    }
                    else
                    {
                        return true;
                    }    
                }    
            }
        }
    });
		 
	    function roundNumber(num, dec) {
		var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
		return result;
	    }



    function depositarAlgunos()
    {
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
                if (Ext.getCmp('cmb_ctas_bancarias_empresa').getValue())
                { 
                    Ext.Msg.confirm('Alerta','Se marcaran como depositados los pagos seleccionados. Desea continuar?', 
                    function(btn)
                    {
                        if(btn=='yes')
                        {
                            Ext.Ajax.request({
                                url: url_marcar_depositado_ajax,
                                method: 'post',
                                timeout: 240000, //4 minutos de esperar respuesta para el ajax
                                params: 
                                { 
                                    param   : param, 
                                    idcuenta: Ext.getCmp('cmb_ctas_bancarias_empresa').getValue(), 
                                    cuenta  : Ext.getCmp('cmb_ctas_bancarias_empresa').rawValue
                                },
                                success: function(response)
                                {
                                    var text = response.responseText;
                                    
                                    Ext.Msg.alert("Atención", text);
                                    
                                    store.load();
                                },
                                failure: function(result)
                                {
                                    Ext.Msg.show({
                                        title:'Error',
                                        msg: 'Error: ' + result.statusText,
                                        buttons: Ext.Msg.OK,
                                        icon: Ext.Msg.ERROR
                                    });                                      
                                }
                            });
                        }
                    });
                }
                else
                {
                    Ext.Msg.show({
                        title:'Error',
                        msg: "Debe seleccionar la cuenta donde se realiza el deposito",
                        buttons: Ext.Msg.OK,
                        icon: Ext.Msg.WARNING
                    });                    
                }
            }
            else
            {
                Ext.Msg.show({
                    title:'Error',
                    msg: "Por lo menos uno de las registro se encuentra en estado ELIMINADO",
                    buttons: Ext.Msg.OK,
                    icon: Ext.Msg.WARNING
                });                  
            }
        }
        else
        {
            Ext.Msg.show({
                title:'Error',
                msg: "Seleccione por lo menos un registro de la lista",
                buttons: Ext.Msg.OK,
                icon: Ext.Msg.WARNING
            });             
        }
    }


    var listView = Ext.create('Ext.grid.Panel', 
    {
        width       : 1050,
        height      : 600,
        collapsible : false,
        id          : 'pagosNoDepositados',
        title       : '',
        selModel    : sm,
        dockedItems : 
        [{
            xtype : 'toolbar',
            dock  : 'top',
            align : '->',
            items : 
            [
                //tbfill -> alinea los items siguientes a la derecha
                combo_ctas_bancarias_empresa,
                acumulado,
                { xtype: 'tbfill' },
                {
                    iconCls  : 'icon_bank',
                    text     : 'Generar',
                    disabled : false,
                    itemId   : 'depositar',
                    scope    : this,
                    handler: function(){ depositarAlgunos();}
                }
            ]
        }],                    
        renderTo: Ext.get('lista_pagos'),
        // paging bar on the bottom
        bbar    : Ext.create('Ext.PagingToolbar', 
        {
            store       : store,
            displayInfo : true,
            displayMsg  : 'Mostrando {2} pagos',
            emptyMsg    : "No hay datos para mostrar"
        }),	
        store       : store,
        multiSelect : false,
        viewConfig  : 
        {
            emptyText: 'No hay datos para mostrar'
        },
        listeners:
        {
                itemdblclick: function( view, record, item, index, eventobj, obj )
                {
                    var position = view.getPositionByEvent(eventobj),
                    data         = record.data,
                    value        = data[this.columns[position.column].dataIndex];
                    Ext.Msg.show(
                    {
                        title   :'Copiar texto?',
                        msg     : "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                        buttons : Ext.Msg.OK,
                        icon    : Ext.Msg.INFORMATION
                    });
                },
                afterrender: function (grid,eOpts)
                {
                    if (strPrefijoEmpresa!='TN')
                    {
                        grid.columns[2].setVisible(false);
                    }
                },
                viewready: function (grid) 
                {
                    var view = grid.view;
                    // record the current cellIndex
                    grid.mon(view, {
                        uievent: function (type, view, cell, recordIndex, cellIndex, e) 
                        {
                            grid.cellIndex   = cellIndex;
                            grid.recordIndex = recordIndex;
                        }
                    });

                    grid.tip = Ext.create('Ext.tip.ToolTip', {
                        target     : view.el,
                        delegate   : '.x-grid-cell',
                        trackMouse : true,
                        renderTo   : Ext.getBody(),
                        listeners: 
                        {
                            beforeshow: function updateTipBody(tip) 
                            {
                                if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1) 
                                {
                                    header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                    tip.update(grid.getStore().getAt(grid.recordIndex).get(header.dataIndex));
                                }
                            }
                        }
                    });
                } 
        },
        columns: [
        {
            text      : 'Numero',
            width     : 100,
            dataIndex : 'numero'
        },{
            text      : 'Oficina',
            width     : 100,
            dataIndex : 'oficina'
        },{
            text      : 'Cliente',
            width     : 150,
            dataIndex : 'cliente'
        },{
            text      : 'Punto',
            width     : 110,
            dataIndex : 'punto'
        },{
            text      : 'Forma Pago',
            dataIndex : 'formaPago',
            align     : 'right',
            width     : 90			
        },{
            text      : 'Valor',
            dataIndex : 'valor',
            align     : 'right',
            width     : 60			
        },{
            text      : 'Comentario',
            dataIndex : 'comentario',
            align     : 'right',
            flex      : 130			
        },{
            text      : 'Fecha Creacion',
            dataIndex : 'fechaCreacion',
            align     : 'right',
            flex      : 120			
        },{
            text      : 'creado por',
            dataIndex : 'usuarioCreacion',
            align     : 'right',
            flex      : 120			
        },{
            text      : 'Estado',
            dataIndex : 'estado',
            align     : 'right',
            flex      : 80
        },{
            text     : 'Acc',
            width    : 40,
            renderer : renderAcciones
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
                width: 1050,
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
                                TFCreador
                                //combo_oficinas
                                                                
                ],	
                renderTo: 'filtro_pagos'
            }); 
      

	function Buscar(){
		Ext.getCmp('acumulado').setValue(0);
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
				store.load({params: {start: 0, limit: itemsPerPage}});
			}
		}
		else
		{
                    Ext.Msg.show({
                    title:'Error en Busqueda',
                    msg: 'Por Favor para realizar la busqueda debe ingresar Fecha Desde y Fecha Hasta',
                    buttons: Ext.Msg.OK,
                    animEl: 'elId',
                    icon: Ext.MessageBox.ERROR
                         });
		}	
	}
        
        function Limpiar(){   
            Ext.getCmp('fechaDesde').setValue('');
            Ext.getCmp('fechaHasta').setValue('');
            Ext.getCmp('idformasmultiselect').setValue('');
            Ext.getCmp('creador').setValue('');
        }


});
