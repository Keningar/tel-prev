            Ext.require([
                '*',
                'Ext.tip.QuickTipManager',
                'Ext.window.MessageBox',
                'Ext.ux.form.field.BoxSelect'    
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
                 
   
       //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		
            DTFechaDesde = new Ext.form.DateField({
                    id: 'fechaDesde',
                    fieldLabel: 'Fecha Doc',
                    labelAlign : 'left',
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    width:220,
                    margin:0,
                    padding:0,
                    border:0,
                    value: new Date() ,
                    listeners: {
                     change: function (t,n,o) {
                      //alert("holaaa");
                        Ext.getCmp('fechaHasta').setValue(Ext.getCmp('fechaDesde').getValue());
                     }
                  }
            });
            DTFechaHasta = new Ext.form.DateField({
                    id: 'fechaHasta',
                    fieldLabel: 'Hasta',
                    labelAlign : 'left',
                    xtype: 'datefield',
                    format: 'Y-m-d',
                    width:220,
                    margin:0,
                    padding:0,
                    border:0,
                    hidden:true,
                    value:new Date() ,
                     listeners: {
                     change: function (t,n,o) {
                      //alert("holaaa");
                        Ext.getCmp('fechaDesde').setValue(Ext.getCmp('fechaHasta').getValue());
                     }
                  }
            });


        
storeFormaPago= new Ext.data.Store({ 
   total: 'total',
        proxy: {
            type: 'ajax',
            url : url_lista_formaspago,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'registros'
            },
            extraParams: {
                    nombre: '',
                    estado: 'Activo'
                }
        },
                
        fields:
              [
                {name:'id', mapping:'id'},
                {name:'descripcion', mapping:'descripcion'}
              ]
     
    });
//se comenta porque el item cambió de tipo
/*comboFPago = Ext.create('Ext.form.ComboBox', {
        id:'formapago',
        name:'formapago',
        fieldLabel: 'Forma Pago:',
        store: storeFormaPago,
        displayField: 'descripcion',
      valueField: 'descripcion',
        // valueField: 'descripcion',
        height:30,
        width: 280,
        border:0,
        margin:0,
        padding:0,
		queryMode: "remote",
		emptyText: ''
           
     
    });
    */

    var baseExampleConfig = 
    {
        id           : 'formapago',
        name         : 'formapago',
        fieldLabel   : 'Forma Pago:',
        store        : storeFormaPago,
        displayField : 'descripcion',
        valueField   : 'descripcion',
        width        : 325,
        border       : 0,
        margin       : 0,
        padding      : 0,
        queryMode    : "remote",
        emptyText    : ''
    };


    var comboFPago = Ext.create('Ext.ux.form.field.BoxSelect', baseExampleConfig);
 
  ////////////////////////////Oficinas////////////////////////////////////

    Ext.define('modelOficinas', 
    {
        extend: 'Ext.data.Model',
        fields: 
        [
            {name: 'intIdOficina', type: 'int'},
            {name: 'strNombreOficina', type: 'string'}
        ]
    });
    
    var storeOficina = Ext.create('Ext.data.Store', 
    {
        autoLoad : true,
        model    : "modelOficinas",
        proxy: 
        {
            type : 'ajax',
            url  : url_lista_oficinas,
            reader: 
            {
                    type: 'json',
                    root: 'objDatos'
            }
        }                    
    });	

    comboOficina = Ext.create('Ext.form.ComboBox', 
    {
        id           : 'oficina',
        name         : 'oficina',
        fieldLabel   : 'Oficina',
        store        : storeOficina,
        displayField : 'strNombreOficina',
        valueField   : 'intIdOficina',
        height       : 30,
        width        : 325,
        border       : 0,
        margin       : 0,
         padding     : 0,
        queryMode    : "remote",
        emptyText    : ''
    });
  

//////////////////////////////////////////////////////////////////


    Ext.define('ListaDetalleModel', {
        extend: 'Ext.data.Model',
        fields: [{name:'id', type: 'int'},
                {name:'numeroPago', type: 'string'},
                {name:'empl', type: 'string'},
                {name:'fechaCreacion', type: 'string'},
                {name:'login', type: 'string'},
                {name:'cliente', type: 'string'},
                {name:'numReferencia', type: 'string'},
                {name:'formaPago', type: 'string'},
                {name:'nombreOficina', type: 'string'},
                {name:'valor', type: 'string'},
                {name:'linkVer', type: 'string'}


                ]
    }); 

 store = Ext.create('Ext.data.JsonStore', {
                    model: 'ListaDetalleModel',
                     pageSize: itemsPerPage,
                    proxy: {
                        type: 'ajax',
                          method: 'get',
                         timeout: 700000,
                        url: url_grid,
                        reader: {
                            type: 'json',
                            root: 'pagos',
                            totalProperty: 'total'
                        },
                       
                        extraParams:{fechaDesde:'',fechaHasta:''},
                        simpleSortMode: true
                    },
                    listeners: {
                        beforeload: function(store){
                            
                              
				store.getProxy().extraParams.fechaDesde= Ext.getCmp('fechaDesde').getValue();
				store.getProxy().extraParams.fechaHasta= Ext.getCmp('fechaHasta').getValue();
                                
                              //store.getProxy().extraParams.formapago= Ext.getCmp('idformasmultiselect').lastValue; 
                               store.getProxy().extraParams.formapago= Ext.getCmp('formapago').lastValue;  //envio de esta forma porque es necesario para el tipo de item
                                //store.getProxy().extraParams.formapago= comboFPago.getValue();
                              store.getProxy().extraParams.oficina= comboOficina.getValue();
                                
                        },
                        load: function(store){
                            store.each(function(record) {
                                //idClienteSucursalSesion = record.data.idClienteSucursalSesion;
                            });
                        }
                    }
  });

store.load({params: {start: 0, limit: 100}});    
             


////////////////////////////////////////


            function generarPDF(){
             
              var feDesde   = Ext.Date.format(Ext.getCmp('fechaDesde').getValue(), 'Y-m-d');
              var feHasta   = Ext.Date.format(Ext.getCmp('fechaHasta').getValue(), 'Y-m-d');
              var formaPago=Ext.getCmp('formapago').getValue();
               var oficina=Ext.getCmp('oficina').getValue();
                
                if(formaPago === null){
                    formaPago='';
                }
                
                if(oficina === null){
                    oficina='';
                }
             
              if(feDesde!="" && feHasta!=""){
                 
                window.open("pdfCierreCaja?fedesde="+feDesde+"&feHasta="+feHasta+"&formaPago="+formaPago+"&oficina="+oficina,'_blank');
                  
              
              }else{
                   Ext.Msg.alert('Informacion','Debe escoger la fecha de busqueda para generar la descarga');
              }
              
               
         }
            
            
            
            function generarPDFResumen(){
             
                 var feDesde   = Ext.Date.format(Ext.getCmp('fechaDesde').getValue(), 'Y-m-d');
                 var feHasta   = Ext.Date.format(Ext.getCmp('fechaHasta').getValue(), 'Y-m-d');
                 var oficina=Ext.getCmp('oficina').getValue();
                
                if(oficina === null){
                    oficina='';
                }
                //alert(oficina);
              
             // window.open(feDesde +"/"+feHasta +"/"+"pdfResumenCierreCaja",'_blank'); // asi para el routing
              window.open("pdfResumenCierreCaja?fedesde="+feDesde+"&feHasta="+feHasta+"&oficina="+oficina,'_blank');
               
             
                           
            }
            
            
             function generarRptPorEmpleado()
             {            
                var feDesde  = Ext.Date.format(Ext.getCmp('fechaDesde').getValue(), 'Y-m-d');
                var feHasta  = Ext.Date.format(Ext.getCmp('fechaHasta').getValue(), 'Y-m-d');
                var formaPago=Ext.getCmp('formapago').getValue();
                var oficina=Ext.getCmp('oficina').getValue();
                
                if(oficina === null)
                {
                    oficina='';
                }
                window.open("getRptCierreCajaXEmpleado?fedesde="+feDesde+"&feHasta="+feHasta+"&oficina="+oficina+"&formaPago="+formaPago,'_blank');             
            } 
            
            function generarRptPorPapeleta()
            {           
                var feDesde   = Ext.Date.format(Ext.getCmp('fechaDesde').getValue(), 'Y-m-d');
                var feHasta   = Ext.Date.format(Ext.getCmp('fechaHasta').getValue(), 'Y-m-d');
                var formaPago = Ext.getCmp('formapago').getValue();
                var oficina   = Ext.getCmp('oficina').getValue();
                
                if(oficina === null)
                {
                    oficina='';
                }
                window.open("getRptCierreCajaXPapeleta?fedesde="+feDesde+"&feHasta="+feHasta+"&oficina="+oficina+"&formaPago="+formaPago,'_blank');             
            }             


            var listView = Ext.create('Ext.grid.Panel', 
            {
                id          : 'listView',
                width       : 950,
                height      : 400,
                margin      : 0,
                padding     : 0,
                collapsible : false,
                title       : 'Listado de pagos',                   
                renderTo    : Ext.get('lista_pagos'),
                // paging bar on the bottom
                bbar        : Ext.create('Ext.PagingToolbar', 
                {
                    store       : store,
                    displayInfo : true,
                    displayMsg  : 'Mostrando pagos {0} - {1} of {2}',
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
                columns: 
                [
                    new Ext.grid.RowNumberer(),    
                    {
                        text      : 'id PagoDet',
                        width     : 10,
                        dataIndex : 'id',
                        hidden    : true
                    },     
                    {
                        text      : 'Num. Doc',
                        width     : 100,
                        dataIndex : 'numeroPago'
                    },
                    {
                        text      : 'Empleado',
                        width     : 110,
                        dataIndex : 'empl'
                    },
                    {
                        text      : 'Fecha Creacion',
                        width     : 100,
                        dataIndex : 'fechaCreacion',
                        align     : 'right'			
                    }, 
                    {
                        text      : 'login',
                        width     : 80,
                        dataIndex : 'login',
                         align    : 'left'
                    },
                    {
                        text      : 'Cliente',
                        width     : 100,
                        dataIndex : 'cliente'
                    }, 
                    {
                        text      : 'Num. Ref',
                        width     : 80,
                        dataIndex : 'numReferencia'
                    },
                    {
                        text      : 'Forma Pago',
                        dataIndex : 'formaPago',
                        align     : 'left',
                        width     : 80			
                    },
                    {
                        text      : 'Oficina',
                        dataIndex : 'nombreOficina',
                        align     : 'left',
                        width     : 180			
                    },
                    {
                        text      : 'Valor',
                        dataIndex : 'valor',
                        align     : 'right',
                        width     : 60			
                    }
                ]
            });            

            
            function renderAcciones(value, p, record) {
              
              
                    var iconos='';
                   
                    iconos=iconos+'<b><a href="#" onClick="" title="Ver" class="button-grid-show"></a></b>';
                   
                return Ext.String.format(
                                    iconos,
                        value
                    );
            }
            



            var filterPanel = Ext.create('Ext.panel.Panel', {
                bodyPadding: 7,  // Don't want content to crunch against the borders
                margin:0,
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
                  //  bodyStyle: 'padding:10px'
                    bodyStyle: 'padding:0px'
                },
                collapsible : true,
                collapsed: false,
                width: 950,
                title: 'Criterios de busqueda',
                buttons: [
                        {
                            text: 'Buscar',
                            id: 'buscar',
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
                    
                    
                    {
                         xtype: 'fieldset',
                         title: '',
                      
                        hideBorders: true,
                        border:false,
                       
                        id: 'fieldsetTemp',
                            items: [
                              
                                DTFechaDesde,
                                {html:"&nbsp",border:false,width:50},
                               
                                comboOficina,
                                {html:"&nbsp",border:false,width:50}, 
                                 comboFPago,
                                {html:"&nbsp",border:false,width:50} 
                       
                              
                           ]
                       } ,
                       {
                         xtype: 'fieldset',
                         title: '',
                           //x: 10,
                          //y: 140,
                         x: 60,
                          y: 10,
                         // width: 780,
                         // height: 200,
                        width:200,
                        height: 130,
                        layout: 'absolute',
                        hideBorders: true,
                        border:false,
                       
                        id: 'selectCriteriaFieldSet',
                            items: [
                              
                               {
                                   xtype: 'radio',
                                 //  x: 350,
                                  // y: 40,
                                   x: 0,
                                   y: 0,
                                   boxLabel: 'Detallado',
                                   name: 'radio_detallado',
                                   inputValue: 'detallado',
                                   id: 'radio_detallado',
                                   checked: true,
                                        //text: 'Smaller Size',
                                        handler: function() {
                                            var radio1 = Ext.getCmp('radio_detallado');
                                            var radio2 = Ext.getCmp('radio_resumido');
                                            var radio3 = Ext.getCmp('radio_cajero');
                                            var radio4 = Ext.getCmp('radio_papeleta');

                                            if (radio1.getValue()) 
                                            {
                                                radio2.setValue(false);
                                                radio3.setValue(false);
                                                radio4.setValue(false);
                                                Ext.getCmp('listView').setVisible(true);
                                                comboFPago.setVisible(true);
                                                Ext.getCmp('buscar').setVisible(true);      
                                                Ext.getCmp('buttonDetalle').setVisible(true);
                                                Ext.getCmp('buttonResumen').setVisible(false);
                                                Ext.getCmp('buttonCajero').setVisible(false);
                                                Ext.getCmp('buttonPapeleta').setVisible(false);
                                                return;
                                            }

                                        }
                               },
                               {
                                   xtype: 'radio',
                                   x: 0,
                                   y: 20,
                                   boxLabel: 'Resumido',
                                   name: 'radio_resumido',
                                   inputValue: 'resumido',
                                   id: 'radio_resumido',
                                   handler: function() {
                                            var radio1 = Ext.getCmp('radio_detallado');
                                            var radio2 = Ext.getCmp('radio_resumido');
                                            var radio3 = Ext.getCmp('radio_cajero');
                                            var radio4 = Ext.getCmp('radio_papeleta');

                                           if (radio2.getValue()) 
                                           {
                                                radio1.setValue(false);
                                                radio3.setValue(false);
                                                radio4.setValue(false);
                                                Ext.getCmp('listView').setVisible(false);                                               
                                                comboFPago.setVisible(false);
                                                Ext.getCmp('buscar').setVisible(false);
                                                Ext.getCmp('buttonDetalle').setVisible(false);
                                                Ext.getCmp('buttonResumen').setVisible(true);
                                                Ext.getCmp('buttonCajero').setVisible(false);
                                                Ext.getCmp('buttonPapeleta').setVisible(false);
                                                
                                                return;
                                            }
                                         
                                            
                                        }
                               },
                               {
                                   xtype: 'radio',
                                   x: 0,
                                   y: 40,
                                   boxLabel: 'Agrupado por Empleado',
                                   name: 'radio_cajero',
                                   inputValue: 'cajero',
                                   id: 'radio_cajero',
                                   handler: function() {
                                            var radio1 = Ext.getCmp('radio_detallado');
                                            var radio2 = Ext.getCmp('radio_resumido');
                                            var radio3 = Ext.getCmp('radio_cajero');
                                            var radio4 = Ext.getCmp('radio_papeleta');
                                         
                                      
                                           if (radio3.getValue()) 
                                           {
                                                radio1.setValue(false);
                                                radio2.setValue(false);
                                                radio4.setValue(false);
                                                Ext.getCmp('listView').setVisible(true);
                                                comboFPago.setVisible(true);
                                                Ext.getCmp('buscar').setVisible(true);  
                                                Ext.getCmp('buttonDetalle').setVisible(false);
                                                Ext.getCmp('buttonResumen').setVisible(false);
                                                Ext.getCmp('buttonCajero').setVisible(true);
                                                Ext.getCmp('buttonPapeleta').setVisible(false);
                                                return;
                                            }
                                        }
                               }, 
                               {
                                   xtype: 'radio',
                                   x: 0,
                                   y: 60,
                                   boxLabel: 'Agrupado por Papeleta',
                                   name: 'radio_papeleta',
                                   inputValue: 'papeleta',
                                   id: 'radio_papeleta',
                                   handler: function() {
                                            var radio1 = Ext.getCmp('radio_detallado');
                                            var radio2 = Ext.getCmp('radio_resumido');
                                            var radio3 = Ext.getCmp('radio_cajero');
                                            var radio4 = Ext.getCmp('radio_papeleta');
                                         
                                      
                                           if (radio4.getValue()) 
                                           {
                                                radio1.setValue(false);
                                                radio2.setValue(false);
                                                radio3.setValue(false);
                                                Ext.getCmp('listView').setVisible(true);
                                                comboFPago.setVisible(true);
                                                Ext.getCmp('buscar').setVisible(true);  
                                                Ext.getCmp('buttonDetalle').setVisible(false);
                                                Ext.getCmp('buttonResumen').setVisible(false);
                                                Ext.getCmp('buttonCajero').setVisible(false);
                                                Ext.getCmp('buttonPapeleta').setVisible(true);
                                                return;
                                            }
                                        }
                               },                                
                               {
                                   xtype: 'button',
                                   id: 'buttonResumen',       
                                   x: 0,
                                   y: 85,
                                   width:95,
                                   text: 'Resumen pdf',
                                   name: 'buttonResumen',
                                  handler: function (){
                                   generarPDFResumen();
                                  }
                                  
                                   
                                       
                               },
                                 {
                                   xtype: 'button',
                                   id: 'buttonCajero',       
                                   x: 0,
                                   y: 85,
                                   width:95,
                                   text: 'Por Emp. pdf',
                                   name: 'buttonCajero',
                                  handler: function (){
                                   generarRptPorEmpleado();
                                  }
                               }, 
                               {
                                   xtype: 'button',
                                   id: 'buttonPapeleta',       
                                   x: 0,
                                   y: 85,
                                   width:95,
                                   text: 'Por Papeleta. pdf',
                                   name: 'buttonPapeleta',
                                  handler: function (){
                                   generarRptPorPapeleta();
                                  }
                               },                                
                                 {
                                   xtype: 'button',
                                   id: 'buttonDetalle',       
                                   x: 0,
                                   y: 85,
                                   width:95,
                                   text: 'Detalle pdf',
                                   name: 'buttonDetalle',
                                  handler: function (){
                                   generarPDF();
                                  }
                               }   
                           ]
                       } 
                       
                         
                                                         
                ],
                
                    
              
                renderTo: 'filtro_pagos'
              
                
            }); 
            
  
      

	function Buscar(){
           var button = Ext.getCmp('buscar');
          // alert( Ext.getCmp('formapago').getValue());
           validarFechas();
			
	}
        function validarFechas(){
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
                           return false;
			}
			else
			{
				store.load({params: {start: 0, limit: 100}});
                                return true;
			}
                        
		}
		else
		{
                    store.load({params: {start: 0, limit: 100}});
                    return false;
		}
        }
        
        function Limpiar(){   
            //Ext.getCmp('fechaDesde').setValue('');
            //Ext.getCmp('fechaHasta').setValue('');
            Ext.getCmp('fechaDesde').setValue(new Date());
            Ext.getCmp('fechaHasta').setValue(new Date());
            
             Ext.getCmp('formapago').setValue('');
             Ext.getCmp('oficina').setValue('');
        }
       


});
