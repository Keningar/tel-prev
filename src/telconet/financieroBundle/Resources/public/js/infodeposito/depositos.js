Ext.require([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox',
    'Ext.ux.form.field.BoxSelect'    
]);

var itemsPerPage = 30;
var storeDepositos='';
var estado_id='';
var area_id='';
var login_id='';
var tipo_asignacion='';
var pto_sucursal='';
var idClienteSucursalSesion;
var winDetalle;



function showProcesar(id_deposito,valor, banco, cuenta) {
    winDetalle="";
    if(!winDetalle){		
        var form = Ext.widget('form', {
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
            url: url_procesar,
            items: [
            {
                xtype: 'textfield',
                fieldLabel: 'Valor',
                labelAlign : 'left',
                name: 'valor',
                value:valor,
                readOnly:true,
                width:100,
                anchor: '100%'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Banco',
                labelAlign : 'left',
                name: 'banco',
                value: banco,
                readOnly:true,
                width:100,
                anchor: '100%'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Cuenta',
                labelAlign : 'left',
                name: 'cuenta',
                value: cuenta,
                readOnly:true,
                width:100,
                anchor: '100%'
            },
            {
                id: 'fechaprocesa',
                name: 'fechaprocesa',
                fieldLabel: 'Fecha procesa',
                labelAlign : 'left',
                xtype: 'datefield',
                format: 'Y-m-d',
                width:100,
                anchor: '100%',
                allowBlank: false
            },
            {
                xtype: 'hiddenfield',
                name: 'iddeposito',
                name: 'iddeposito',
                value: id_deposito
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Comprobante',
                labelAlign : 'left',
                name: 'comprobante',
                width:100,
                anchor: '100%',
                allowBlank: false,
                enforceMaxLength : true,
                maxLength: 18
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
                handler: function() 
                {
                    var form1        = this.up('form').getForm();
                    var strFeProceso = Ext.Date.format(Ext.getCmp('fechaprocesa').getValue(),'Y-m-d');
                    
                    if (form1.isValid())
                    {
                        if( !Ext.isEmpty(strFeProceso) )
                        {
                            Ext.MessageBox.wait('Validando fecha del depósito a procesar...');
                            
                            Ext.Ajax.request
                            ({
                                timeout: 9000000,
                                url: strUrlValidarFechaPago,
                                params:
                                {
                                    strFechaValidar: strFeProceso,
                                    strParametroValidar: 'PROCESAR_DEPOSITO'
                                },
                                method: 'get',
                                success: function(response) 
                                {
                                    var mensajeRespuesta = response.responseText;

                                    if( "S" == mensajeRespuesta )
                                    {
                                        form1.submit
                                        ({
                                            waitMsg: "Procesando",
                                            success: function(form1, action)
                                            {	
                                                Ext.Msg.alert('Success', 'Los datos fueron ingresados con exito');	
                                                form1.reset();
                                                if (storeDepositos)
                                                {
                                                    storeDepositos.load();
                                                }
                                            },
                                            failure: function(form1, action)
                                            {
                                                Ext.Msg.alert('Error', 'Error al ingresar los datos, por favor comunicarse con el departamento de '+ 
                                                                       'Sistemas');
                                            }
                                        });
                                        
                                        winDetalle.hide();
                                    }
                                    else
                                    {
                                        Ext.Msg.alert('Atención', mensajeRespuesta);
                                    }
                                },
                                failure: function(result)
                                {
                                    Ext.Msg.alert('Error ', 'Error al validar la fecha ingresada al procesar el depósito.');
                                }
                            });
                        }
                        else
                        {
                            Ext.Msg.alert('Atención', 'Debe ingresar una fecha para procesar el depósito');
                        }
                   }
                }	
            }]
        });

        winDetalle = Ext.widget('window', {
            title: 'Procesar Deposito',
            closeAction: 'hide',
            width: 350,
            height: 300,
            minHeight: 200,
            layout: 'fit',
            resizable: true,
            modal: true,
            items: form
        });

    }

    winDetalle.show();

}






function editardeposito(id_deposito,valor, banco, cuenta,comprobante,fechaProcesa) {
    winDetalleDeposito="";
    
    var fechaProcesado=fechaProcesa;
    if(fechaProcesado==""){
        fechaProcesado=new Date();
    }
    if(!winDetalleDeposito) {		

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
            url: url_editarDeposito,
            items: [
            {
                xtype: 'textfield',
                fieldLabel: 'Valor',
                labelAlign : 'left',
                name: 'valor',
                value:valor,
                readOnly:true,
                width:100,
                anchor: '100%'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Banco',
                labelAlign : 'left',
                name: 'banco',
                value: banco,
                readOnly:true,
                width:100,
                anchor: '100%'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Cuenta',
                labelAlign : 'left',
                name: 'cuenta',
                value: cuenta,
                readOnly:true,
                width:100,
                anchor: '100%'
            },
            {
                name: 'fechaProcesa',
                fieldLabel: 'Fecha Deposito',
                labelAlign : 'left',
                xtype: 'datefield',
                format: 'Y-m-d',
                width:100,
                anchor: '100%',
                value: fechaProcesado,
                allowBlank: false
            },
            {
                xtype: 'hiddenfield',
                name: 'iddeposito',
                value: id_deposito
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Numero Comprobante',
                labelAlign : 'left',
                name: 'comprobanteDeposito',
                width:100,
                anchor: '100%',
                value: comprobante,
                allowBlank: false
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
                            if (storeDepositos){
                                    storeDepositos.load();
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

        winDetalleDeposito = Ext.widget('window', {
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

    winDetalleDeposito.show();

}


function anularDeposito(id){
    //alert (id);
    Ext.Msg.confirm('Alerta','Se anulara el deposito. Desea continuar?', function(btn){
        if(btn=='yes'){
            Ext.Ajax.request({
                url: url_anular,
                params: { iddeposito : id},                     
                method: 'get',                     
                success: function(response){
                   var text = response.responseText;
                   Ext.Msg.alert("Mensaje","Se anulo el deposito con exito");
                   storeDepositos.load();
                },
                failure: function(result)
                {
                  Ext.Msg.alert('Error ','Error: ' + result.statusText);
                }
            });
        }
    });
} 


Ext.onReady(function(){     
          
    Ext.override(Ext.data.proxy.Ajax,{timeout:900000}); 
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
            timeout:900000,
            type: 'ajax',
            url : url_lista_bancos,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        }
    });
    storeBancos.load({params: {es_tarjeta: 'N'}});

    Ext.define('CuentasList', {
    extend: 'Ext.data.Model',
    fields: [
        {name:'id_cuenta', type:'int'},
        {name:'descripcion_cuenta', type:'string'}
    ]
    });
    storeCuentas = Ext.create('Ext.data.Store', {
        model: 'CuentasList',
        autoLoad: false,
        proxy: {
            timeout:900000,
            type: 'ajax',
            url : url_lista_cuentas,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
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
                storeCuentas.proxy.extraParams = {id_banco: combo.getValue()};
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
        //store:storeBancos,
        displayField: 'descripcion_cuenta',
        valueField: 'id_cuenta',
        listeners:{
            select:{fn:function(combo, value) {
            }}
        }
    });
    TipoFecha = new Ext.form.RadioGroup(
        {
            xtype      : 'fieldcontainer',
            defaultType: 'radiofield',
             width: '250px',
            defaults: {
                flex: 1
            },
            layout: 'hbox',
            items: [
                {
                    boxLabel  : 'Fecha Procesa',
                    name      : 'tipoFecha',
                    inputValue: 'p',
                    id        : 'radio_feprocesa',
                    checked   : true,                    
                    /*listeners:{                    
                    change:
                        function(radio1, newValue, oldValue, eOpts) {
                            if (radio1.checked){
                                TFPorcentaje.setVisible(true);
                            }
                            else
                            {
                                TFPorcentaje.setVisible(false);
                                TFPorcentaje.setValue('');
                            }
                        }
                    }*/
                }, {
                    boxLabel  : 'Fecha Creacion',
                    name      : 'tipoFecha',
                    inputValue: 'c',
                    id        : 'radio_fecreacion',
                    /*listeners:{                    
                        change:
                        function(radio2, newValue, oldValue, eOpts) {
                            if (radio2.checked){
                                TFPrecio.setVisible(true);
                            }
                            else
                            {
                                TFPrecio.setVisible(false);
                                TFPrecio.setValue('');
                            }
                        }
                    }*/
                }
            ]
        }        
    );    
    //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		
    DTFechaDesde = new Ext.form.DateField({
        id: 'fechaDesde',
        fieldLabel: 'Desde',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:250
    });
    DTFechaHasta = new Ext.form.DateField({
        id: 'fechaHasta',
        fieldLabel: 'Hasta',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:250
    });
    TFNumeroComprobante = new Ext.form.TextField({
        id: 'numeroComprobante',
        name: 'numeroComprobante',
        labelAlign:'left',
        fieldLabel: 'Comprobante',
        xtype: 'textfield',
        width: '170px'
    });            

    Ext.define('ListaDetalleModel', {
        extend: 'Ext.data.Model',
        fields: 
        [
            {name:'id',               type: 'int'},
            {name:'valor',            type: 'float'},
            {name:'realizadoPor',     type: 'string'},
            {name:'nombreBanco',      type: 'string'},
            {name:'banco',            type: 'int'},
            {name:'cuenta',           type: 'string'},
            {name:'comprobante',      type: 'string'},
            {name:'fechaProcesa',     type: 'string'},
            {name:'fechaCreacion',    type: 'string'},
            {name:'usuarioCreacion',  type: 'string'},
            {name:'estado',           type: 'string'},
            {name:'oficina',          type: 'string'},            
            {name:'linkVer',          type: 'string'},
            {name:'linkEditar',       type: 'string'},
            {name:'linkPagos',        type: 'string'},
            {name:'strPuedeProcesar', type: 'string'}
        ]
    }); 


    storeDepositos = Ext.create('Ext.data.JsonStore', {
        model: 'ListaDetalleModel',
        pageSize: itemsPerPage,
        proxy: {
            timeout:900000,
            type: 'ajax',
            url: url_grid,
            reader: {
                type: 'json',
                root: 'depositos',
                totalProperty: 'total'
            },
            extraParams:{fechaDesde:'',fechaHasta:'',numeroComprobante:'',tipoFecha:''},
            simpleSortMode: true
        },
        listeners: {
            beforeload: function(store){
                    store.getProxy().extraParams.fechaDesde= Ext.getCmp('fechaDesde').getValue();
                    store.getProxy().extraParams.fechaHasta= Ext.getCmp('fechaHasta').getValue();  
                   if (Ext.getCmp('radio_fecreacion').checked)
                    {  
                        store.getProxy().extraParams.tipoFecha= "c";  
                    }                    
                   if (Ext.getCmp('radio_feprocesa').checked)
                    {  
                        store.getProxy().extraParams.tipoFecha= "p";  
                    }
                    store.getProxy().extraParams.numeroComprobante=Ext.getCmp('numeroComprobante').getValue();
                    
            },
            load: function(store){
                store.each(function(record) {
                    //idClienteSucursalSesion = record.data.idClienteSucursalSesion;
                });
            }
        }
    });

    storeDepositos.load({params: {start: 0, limit: 30}});    

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




    var listView = Ext.create('Ext.grid.Panel', 
    {
        width       : 900,
        height      : 275,
        collapsible : false,
        title       : '',
        dockedItems : 
        [{
            xtype : 'toolbar',
            dock  : 'top',
            align : '->',
            items : []
        }],                    
        renderTo : Ext.get('lista_pagos'),
        // paging bar on the bottom
        bbar     : Ext.create('Ext.PagingToolbar', 
        {
            store       : storeDepositos,
            displayInfo : true,
            displayMsg  : 'Mostrando depositos {0} - {1} de {2}',
            emptyMsg    : "No hay datos para mostrar"
        }),	
        store       : storeDepositos,
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
                    Ext.Msg.show({
                        title   : 'Copiar texto?',
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
        columns: [new Ext.grid.RowNumberer(),
        {
            text      : 'Fecha Creacion',
            dataIndex : 'fechaCreacion',
            align     : 'right',
            flex      : 80			
        },{
            text      : 'Oficina',
            dataIndex : 'oficina',
            align     : 'right',
            flex      : 80			
        },{
            text      : 'Realizado por',
            dataIndex : 'realizadoPor',
            align     : 'right',
            flex      : 60			
        },{
            text      : 'Banco',
            dataIndex : 'nombreBanco',
            align     : 'right',
            flex      : 100			
        },{
            text      : 'Cuenta',
            dataIndex : 'cuenta',
            align     : 'right',
            flex      : 60			
        },{
            text      : 'Fecha Procesa',
            dataIndex : 'fechaProcesa',
            align     : 'right',
            flex      : 70			
        },{
            text      : 'Valor',
            dataIndex : 'valor',
            align     : 'right',
            width     : 60			
        },{
            text      : 'Comprobante',
            dataIndex : 'comprobante',
            align     : 'right',
            flex      : 60			
        },{
            text      : 'Estado',
            dataIndex : 'estado',
            align     : 'right',
            flex      : 60			
        },{
            text     : 'Acciones',
            width    : 90,
            renderer : renderAcciones
        }

        ]
    });            

            
    function renderAcciones(value, p, record) 
    {
        var iconos           = '';
        var strPuedeProcesar = record.data.strPuedeProcesar;
        
        if (record.data.estado=='Pendiente')
        {
            if( puedeVerProcesarDeposito && strPuedeProcesar == 'S' )
            {
                iconos=iconos+'<b><a href="#" onClick="showProcesar('+record.data.id+','+record.data.valor+',\''+
                    record.data.nombreBanco+'\',\''+record.data.cuenta+'\')" title="Procesar Deposito" class="button-grid-process"></a></b>';
            }
            if (puedeAnularDeposito)
            {    
            
                iconos=
                    iconos+'<b><a href="#" onClick="anularDeposito('+record.data.id+')" title="Anular Deposito" class="button-grid-delete"></a></b>';
            }    
        }
        
        if( record.data.estado == 'Procesado' && strContabiliza == 'N' )
        {
            var fecha=record.data.fechaProcesa;
            var fecha0=fecha.substring(0,11);
            var fecha2=fecha0.split("\/");
            var fechaProcesa=fecha2[2].trim()+'-'+fecha2[1].trim()+'-'+fecha2[0].trim();
            var comprobante=record.data.comprobante;
            var permiso = $("#ROLE_86-1066");
            var boolPermiso1 = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);	
            
            if(boolPermiso1)
            {
                iconos=
                    iconos+'<b><a href="#" onClick="editardeposito('+
                    record.data.id+','+record.data.valor+',\''+record.data.nombreBanco+'\','+
                    record.data.cuenta+', \''+comprobante+'\', \''+fechaProcesa+'\')" title="Editar Deposito" class="button-grid-edit"></a></b>'; 
            }
        }
        
        if(puedeVerExcelPagosDeposito)
        {
            iconos=iconos+'<b><a href="'+record.data.linkPagos+'" onClick="" title="Excel Pagos" class="button-grid-excel-green"></a></b>';
        }            
        
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
        width: 900,
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
            TipoFecha,
            {html:"&nbsp;",border:false,width:50},
            {html:"&nbsp;",border:false,width:50},
            {html:"&nbsp;",border:false,width:50},
            {html:"&nbsp;",border:false,width:50},
            DTFechaDesde,  
            {html:"&nbsp;",border:false,width:50},
            DTFechaHasta,
            {html:"&nbsp;",border:false,width:50},
            TFNumeroComprobante,

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
                storeDepositos.load({params: {start: 0, limit: 30}});
            }
        }
        else
        {
            storeDepositos.load({params: {start: 0, limit: 30}});
        }	
    }
        
    function Limpiar(){   
        Ext.getCmp('fechaDesde').setValue('');
        Ext.getCmp('fechaHasta').setValue('');
        Ext.getCmp('numeroComprobante').setValue('');
    }

});
