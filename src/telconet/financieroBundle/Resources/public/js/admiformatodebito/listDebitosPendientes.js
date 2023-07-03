Ext.require([
    '*',
    'Ext.tip.QuickTipManager',
        'Ext.window.MessageBox'
]);

var itemsPerPage       = 500;
var store              = '';
var bcotc_id           = '';
var area_id            = '';
var login_id           = '';
var tipo_asignacion    = '';
var pto_sucursal       = '';
var valor_base_imponib = 0;
var labelCodigoDebito  = 'Código Debito';
var idClienteSucursalSesion;
if (prefijoEmpresa=='TN')
{
    labelCodigoDebito='# Transferencia';
}    

Ext.onReady(function(){
    
    
	Ext.form.VTypes["porcentajeVtypeVal"] =/(^\d{1,3}\.\d{1,2}$)|(^\d{1,3}$)/;		
	Ext.form.VTypes["porcentajeVtype"]=function(v){       
		return Ext.form.VTypes["porcentajeVtypeVal"].test(v);
	}
	Ext.form.VTypes["porcentajeVtypeText"]="Puede ingresar hasta 3 enteros y al menos 1 decimal o puede ingresar hasta 3 enteros sin decimales";
	Ext.form.VTypes["porcentajeVtypeMask"]=/[\d\.]/;    
    
    Ext.override(Ext.data.proxy.Ajax, { timeout:120000 });            
    TFObservacion = new Ext.form.field.TextArea({
        xtype     : 'textareafield',
        name      : 'observacion',
        fieldLabel: 'Observacion Rechazo',
        cols     : 80,
        rows     : 1,
        maxLength: 200
    }); 
    TFNumeroCuenta = new Ext.form.TextField({
        id: 'numerocuenta',
        name: 'numerocuenta',
        labelAlign:'right',
        fieldLabel: 'Numero Cuenta/Tarj',
        xtype: 'textfield',
        width: '170px'
    });
    TFNumeroCedula = new Ext.form.TextField({
        id: 'numerocedula',
        name: 'numerocedula',
        labelAlign:'right',
        fieldLabel: 'Cedula/Ruc',
        xtype: 'textfield',
        width: '170px'
    });
    TFCodigoDebito = new Ext.form.TextField({
            id: 'codigodebito',
            name: 'codigodebito',
            labelAlign:'right',
            fieldLabel: labelCodigoDebito,
            xtype: 'textfield',
            width: '205px'
    });
    DTFechaProceso = new Ext.form.DateField({
            id: 'fechaProceso',
            name: 'fechaProceso',
            fieldLabel: 'Fecha Proceso',
            labelAlign : 'left',
            xtype: 'datefield',
            format: 'Y-m-d',
            width:215
    });

    TFCodigoDebitoAuto = new Ext.form.TextField({
            id: 'codigodebitoauto',
            name: 'codigodebitoauto',
            labelAlign:'right',
            fieldLabel: "Código Debito",
            xtype: 'textfield',
            width: '205px'
    });
    DTFechaProcesoAuto = new Ext.form.DateField({
            id: 'fechaProcesoAuto',
            name: 'fechaProcesoAuto',
            fieldLabel: 'Fecha Proceso',
            labelAlign : 'left',
            xtype: 'datefield',
            format: 'Y-m-d',
            width:215
    });

    TFPorcentajeComision = new Ext.form.TextField({
            id: 'porcentajeComision',
            name: 'porcentajeComision',
            labelAlign:'right',
            fieldLabel: '% Comision',
            xtype: 'textfield',
            width: 140,
            value:0,
            vtype: 'porcentajeVtype',
            listeners:{
                blur: function( textfield, The, eOpts)
                {
                    if(Ext.form.VTypes["porcentajeVtypeVal"].test(textfield.getValue())===false)
                    {
                        textfield.setValue(0);
                    } 
                    else
                    {
                        calcularValores(); 
                    }    
                }
            }
    });

    TFValorComision = new Ext.form.TextField({
            id: 'valor_comision',
            name: 'valor_comision',
            labelAlign:'right',
            fieldLabel: 'Comision',
            xtype: 'textfield',
            width: 150,
            value:0,
            readOnly:true
    });

    TFValorNeto = new Ext.form.TextField({
            id: 'valor_neto',
            name: 'valor_neto',
            labelAlign:'right',
            fieldLabel: 'Neto',
            xtype: 'textfield',
            width: 170,
            value:0,
            readOnly:true
    });

    TFValorRetencionFuente = new Ext.form.TextField({
            id: 'valor_retencion_fuente',
            name: 'valor_retencion_fuente',
            labelAlign:'right',
            fieldLabel: 'Fuente',
            xtype: 'textfield',
            width: 160,
            value:0,
            readOnly:true
    });
    
    TFValorRetencionIva = new Ext.form.TextField({
            id: 'valor_retencion_iva',
            name: 'valor_retencion_iva',
            labelAlign:'right',
            fieldLabel: 'Iva',
            xtype: 'textfield',
            width: 160,
            value:0,
            readOnly:true
    });
    
    TFValorTotal = new Ext.form.TextField({
            id: 'valor_total',
            name: 'valor_total',
            labelAlign:'right',
            fieldLabel: 'Total',
            xtype: 'textfield',
            width: 170,
            value:0,
            readOnly:true
    });
    
    //Caja de Texto para presentar el saldo de los débitos pendientes
    TFSaldoDebitosPendientes = new Ext.form.TextField({
            id: 'saldoDebitosPendientes',
            name: 'saldoDebitosPendientes',
            labelAlign:'right',
            fieldLabel: 'Total: $',
            xtype: 'textfield',
            width: '205px'
    });
    
    /**
    * Documentación para función 'saldoDebitosPendientes'. Obtiene el saldo total de los débitos pendientes y lo presenta en una caja de texto.
    * @author <hlozano@telconet.ec>
    * @since 07/02/2019
    * @version 1.0
    */   
    function saldoDebitosPendientes() {      
        $.ajax({
            url: strUrlSaldoDebitosPendientes,
            success: function(respuesta) {
                Ext.getCmp('saldoDebitosPendientes').setValue(respuesta.totalDebito);
            },
            error: function() {
                Ext.Msg.alert('Mensaje', 'No se ha podido obtener el saldo de débitos pendientes');
            }
        });       
    }saldoDebitosPendientes();
          
    //CREAMOS DATA STORE PARA EMPLEADOS
    Ext.define('modelEstado', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id_banco', type: 'int'},
            {name: 'descripcion_banco',  type: 'string'}                    
        ]
    });			
    var tipo_cuenta_store = Ext.create('Ext.data.Store', {
        autoLoad: false,
        model: "modelEstado",
        proxy: {
            type: 'ajax',
            url : url_lista_bco_tipo_cta,
            reader: {
                type: 'json',
                root: 'encontrados'
            }
        }
    });	
    var tipo_cuenta_cmb = new Ext.form.ComboBox({
        xtype: 'combobox',
        store: tipo_cuenta_store,
        labelAlign : 'right',
        id:'idtipocuenta',
        name: 'idtipocuenta',
        valueField:'id_banco',
        displayField:'descripcion_banco',
        fieldLabel: 'Banco',
        width: 325,
        triggerAction: 'all',
        selectOnFocus:true,
        lastQuery: '',
        mode: 'local',
        allowBlank: true,	

        listeners: {
            select:
            function(e) {
                bcotc_id = Ext.getCmp('idtipocuenta').getValue();
            },
            click: {
                element: 'el', //bind to the underlying el property on the panel
                fn: function(){ 
                    bcotc_id='';
                    tipo_cuenta_store.removeAll();
                    tipo_cuenta_store.load();
                }
            }			
        }
    });

    Ext.define('ListaDetalleModel', {
        extend: 'Ext.data.Model',
        fields: [{name:'id', type: 'int'},
                {name:'banco', type: 'string'},
                {name:'numerotarjetacuenta', type: 'string'},
                {name:'cliente', type: 'string'},
                {name:'cedula', type: 'string'},
                {name:'total', type: 'string'},
                {name:'fechaCreacion', type: 'string'},
                {name:'usuarioCreacion', type: 'string'},
                {name:'estado', type: 'string'},
                {name:'observacionRechazo', type: 'string'},
                {name:'linkVer', type: 'string'}
        ]
    }); 

     Ext.define('TiposCuentaList', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id_cuenta', type:'int'},
            {name:'descripcion_cuenta', type:'string'}
        ]
    });

    //store que recepta las cuentas bancarias de la empresa en sesion
    storeCtasBancariasEmpresa = Ext.create('Ext.data.Store',
    {
        model: 'TiposCuentaList',
        proxy: 
        {
            type: 'ajax',
            url : url_lista_ctas_bancarias_empresa,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
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
        width        : 500,
        fieldLabel: 'Banco',
        emptyText    : 'Seleccione cuenta bancaria empresa',
        store        : storeCtasBancariasEmpresa,
        displayField : 'descripcion_cuenta',
        valueField   : 'id_cuenta',
        listeners:
        {
        select:{fn:function(combo, value) { }}
        }
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
            extraParams:{fechaDesde:'',fechaHasta:'', banco:'',debitoGeneralId:''},
            simpleSortMode: true
        },
        listeners: {
            beforeload: function(store){  
                store.getProxy().extraParams.banco= Ext.getCmp('idtipocuenta').getValue();
                store.getProxy().extraParams.numerocuenta= Ext.getCmp('numerocuenta').getValue();
                store.getProxy().extraParams.numerocedula= Ext.getCmp('numerocedula').getValue();
                store.getProxy().extraParams.debitoGeneralId=debitoGenId;	
                Ext.getCmp('valor_total').setValue(0);
                Ext.getCmp('valor_neto').setValue(0);
            }
        },
        sortOnLoad : true,
        sorters : {
                property : 'banco',
                direction : 'ASC'
        }
    });

    store.load({params: {start: 0, limit: 500}});    


    var sm = new Ext.selection.CheckboxModel( {
       listeners:{          
            selectionchange: function(selectionModel, selected, options)
            {			  
                listView.getView().refresh();				      

                var records = listView.getSelectionModel().getSelection();										
                result  = 0;
                //alert(records);
                Ext.each(records, function(record){
                result += record.get('total') * 1;
                });                     
                Ext.getCmp('valor_total').setValue(roundNumber(result,2));
    
                calcularValores();
            }                     
       }
   });		

    var AutomaticPanel = Ext.create('Ext.panel.Panel', {
        collapsible : true,
        collapsed: true,
        width: 950,
        title: 'Generación Automatica',
        dockedItems: [{
            xtype: 'toolbar',
            dock: 'top',
            align: '->',
            items: [
                TFCodigoDebitoAuto,
                DTFechaProcesoAuto,
                { xtype: 'tbfill'
                },{
                    iconCls: 'icon_aprobar',
                    text: 'Generación Automatica',
                    disabled: false,
                    itemId: 'automatica',
                    scope: this,
                    handler: function(){procesar('AUTOMATICA')}
                }
            ]}],
        renderTo: 'panel_automatico'
    });

    var listView = Ext.create('Ext.grid.Panel', {
        width:950,
        height:400,
        collapsible:false,
        title: 'Generación Manual',
        selModel: sm,
        dockedItems: [{
            xtype: 'toolbar',
            dock: 'top',
            align: '->',
            items: [
                TFCodigoDebito,	
                DTFechaProceso,	
                TFSaldoDebitosPendientes,
                { xtype: 'tbfill' //tbfill -> alinea los items siguientes a la derecha
                },{
                    iconCls: 'icon_aprobar',
                    text: 'Generar Seleccionados',
                    disabled: false,
                    itemId: 'aprobar',
                    scope: this,
                    handler: function(){procesar('NORMAL')}
                },{
                    iconCls: 'icon_aprobar',
                    text: 'Marcar Rechazados',
                    disabled: false,
                    itemId: 'rechazar',
                    scope: this,
                    handler: function(){marcarComoRechazado()}
                }
            ]}],                      
        renderTo: Ext.get('lista_pagos'),
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
            width: 190,
            dataIndex: 'banco'
        },{
            text: 'Numero Cta/Tarj',
            width: 110,
            dataIndex: 'numerotarjetacuenta'
        },{
            text: 'Cliente',
            width: 200,
            dataIndex: 'cliente'
        },{
            text: 'Cedula/Ruc',
            width: 100,
            dataIndex: 'cedula'
        },{
            text: 'Total',
            dataIndex: 'total',
            align: 'right',
            width: 60			
        },{
            text: 'Fecha Creacion',
            dataIndex: 'fechaCreacion',
            align: 'right',
            flex: 85			
        },{
            text: 'Estado',
            dataIndex: 'estado',
            align: 'right',
            flex: 50
        }
        ]
    });            

            
    function renderAcciones(value, p, record) {
            var iconos='';
            iconos=iconos+'<b><a href="'+record.data.linkVer+'" onClick="" title="Ver" class="button-grid-show"></a></b>';
            if((record.data.tipo=='Anticipo') && (record.data.estado=='Pendiente'))
                iconos=iconos+'<b><a href="#" onClick="" title="Cruzar Anticipo" class="button-grid-cruzar"></a></b>';
            return Ext.String.format(
                iconos,
                value
            );
    }

    var observacionPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,
        border:true,
        //buttonAlign: 'center',
        bodyStyle: {
                    background: '#fff'
        },                     
        defaults: {
            bodyStyle: 'padding:10px'
        },
        width: 950,
        title: '',
        items: [
          TFObservacion
        ],	
        renderTo: 'panel_observacion'
    });


    if (prefijoEmpresa=='TN')
    {
        var opcionesPanel = Ext.create('Ext.panel.Panel', {
            bodyPadding: 7,
            border:true,
            layout:{
                type:'table',
                columns: 3,
                align: 'left'
            },        
            bodyStyle: {
                        background: '#fff'
            },                     
            defaults: {
                bodyStyle: 'padding:10px'
            },
            width: 950,
            title: '',
            items: [
                combo_ctas_bancarias_empresa            

            ],	
            renderTo: 'panel_opciones'
        });

        var opcionesValoresPanel = Ext.create('Ext.panel.Panel', {
            bodyPadding: 4,
            border:true,
            layout:{
                type:'table',
                columns: 4,
                align: 'left'
            },        
            bodyStyle: {
                        background: '#fff'
            },                     
            defaults: {
                bodyStyle: 'padding:4px'
            },
            width: 950,
            title: '',
            items: [
            {
                xtype      : 'fieldcontainer',
                fieldLabel : '',
                width: 300,
                bodyPadding:10,
                defaultType: 'checkboxfield',
                layout: {
                    type: 'hbox'
                },                
                items: [
                    {
                        boxLabel  : 'retencion iva ('+porcentajeRetencionIva+'%)',
                        name      : 'retencionIva',
                        inputValue: '1',
                        checked   : false,
                        id        : 'retencionIva', 
                        listeners:{
                            change: function(checkbox, newVal, oldVal)
                            {                                
                                calcularValores();
                            }    
                } 
                    }, {
                        boxLabel  : 'retencion fuente ('+porcentajeRetencionFte+'%)',
                        name      : 'retencionFuente',
                        inputValue: '2',
                        checked   : false,
                        id        : 'retencionFuente',
                        listeners:{
                            change: function(checkbox,newVal,oldVal)
                            {
                                calcularValores();
                            }
                        }
                    }
                ]   
            },                
                TFPorcentajeComision,
                TFValorComision,            
                TFValorNeto,
                {
                    xtype: 'hiddenfield',
                    name: 'hidden_field_1',
                    value: 'value from hidden field'
                },                
                TFValorRetencionFuente,
                TFValorRetencionIva,
                TFValorTotal

            ],	
            renderTo: 'panel_valores_opciones'
        });
        
    }

    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,  // Don't want content to crunch against the borders
        border:false,
        buttonAlign: 'center',
        layout:{
            type:'table',
            columns: 4,
            align: 'left'
        },
        bodyStyle: {
                    background: '#fff'
        },                     
        defaults: {
            bodyStyle: 'padding:10px'
        },
        collapsible : true,
        collapsed: true,
        width: 950,
        title: 'Criterios de busqueda',
        buttons: 
        [{
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
            tipo_cuenta_cmb,                               
            {html:"&nbsp;",border:false,width:50},
            TFNumeroCuenta,
            {html:"&nbsp;",border:false,width:50},
            TFNumeroCedula            
        ],	
        renderTo: 'filtro_pagos'
    }); 
      
	function roundNumber(num, dec) 
    {
		var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
		return result;
	}
        
    function Buscar(){
        store.load({params: {start: 0, limit:500}});
    }

    function Limpiar(){   
        Ext.getCmp('fechaDesde').setValue('');
        Ext.getCmp('fechaHasta').setValue('');
        Ext.getCmp('idtipocuenta').setValue('');
    }

    /**
    * Documentación para funcion 'marcarComoRechazado'.
    * marca como rechazados los debitos seleccionados
    * @author <amontero@telconet.ec>
    * @since 10/09/2015
    * @version 1.1
    * @return objeto para crear excel
    */
    function marcarComoRechazado()
    {
        var param = '';
        if(TFObservacion.getValue()!="")
        {
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
                    Ext.Msg.confirm('Alerta','Se marcaran como rechazados los debitos seleccionados. Desea continuar?', function(btn){
                        if(btn=='yes')
                        {
                            var loadMask = new Ext.LoadMask(Ext.getBody(), {msg: "Procesando ", height: 1200});     
                            loadMask.show();
                            Ext.Ajax.request(
                            {                                
                                url     : url_marchar_rechazo,
                                timeout : 500000,                            
                                method  : 'post',
                                params: 
                                { 
                                    param         : param, 
                                    motivoRechazo : TFObservacion.getValue(),
                                    nombreBanco   : nombreBanco,
                                    fechaGenerado : fechaGenerado 
                                },             
                                success: function(response)
                                {
                                    var text = response.responseText;                                  
                                    loadMask.hide();
                                    saldoDebitosPendientes();
                                    store.load();
                                    Ext.Msg.alert('Mensaje', text);                                      
                                },
                                failure: function(result)
                                {
                                    Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                    mon_loader.hide();
                                },
                                beforerequest : function()
                                {
                                    mon_loader.show();//activation de mon loader
                                }                
                            });
                        }
                    });
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
        else
        {
          alert('Debe ingresar el motivo de rechazo.');
        }
    }
    
    /**
    * Documentación para función 'existenPagosPendientes'.
    * Verifica si existen pagos pendientes en el grid(listdebitospendientes), correspondiente al debito que se esta procesando.
    * @author <hlozano@telconet.ec>
    * @since 04/02/2019
    * @version 1.1
    * @return boolean (true o false)
    */ 
    
    function existenPagosPendientes()
    {
        var existenRegistros = false;
        
        if(listView.getStore().getCount() > 1)
        {
            existenRegistros = true;
        }
        else if (listView.getStore().getCount() === 1)
        {
            listView.getStore().each(function(registro){
               var banco = registro.getData()['banco'];
               if(banco.length !== 0 || banco !== '' )
               {
                   existenRegistros = true;    
               }
            });
        }
        return existenRegistros;            
    }
    
    /**
    * Documentación para funcion 'procesar'.
    * procesa los debitos por pagos pendientes
    * @author <amontero@telconet.ec>
    * @since 06/01/2015
    * @version 1.1
    * 
    * @author <hlozano@telconet.ec>
    * @since 07/02/2019
    * @version 1.2 Se realizan validaciones para la generación automática de débitos de la empresa Telconet.
    * @return objeto para crear excel
    */ 
    function procesar(tipo)
    {
        var param = '';        
        var mensaje = '';
        var ingresar = false;
        var fechaProceso = "";
        var codigoDebito = "";
        var existenRegistros = existenPagosPendientes();
      
        if ((prefijoEmpresa=="TN" && TFCodigoDebito.getValue() != "" && DTFechaProceso.getValue() != "" 
             && Ext.getCmp('cmb_ctas_bancarias_empresa').getValue() && DTFechaProceso.getValue() != null && tipo === "NORMAL")
             ||(prefijoEmpresa=="MD" && TFCodigoDebito.getValue() != "" && DTFechaProceso.getValue() != ""
             && DTFechaProceso.getValue() != null && tipo === "NORMAL"))
        {
            ingresar = true;
            codigoDebito = TFCodigoDebito.getValue();
            fechaProceso = DTFechaProceso.getValue();
            mensaje = "de los debitos seleccionados";
        }
        else if((tipo === "AUTOMATICA" && existenRegistros === true && TFCodigoDebitoAuto.getValue() != "" && DTFechaProcesoAuto.getValue() != ""
                && DTFechaProcesoAuto.getValue() != null && prefijoEmpresa=="MD")
                ||(tipo === "AUTOMATICA" && existenRegistros === true && TFCodigoDebitoAuto.getValue() != "" && DTFechaProcesoAuto.getValue() != ""
                && DTFechaProcesoAuto.getValue() != null && Ext.getCmp('cmb_ctas_bancarias_empresa').getValue() && prefijoEmpresa=="TN" ))
        {
            codigoDebito = TFCodigoDebitoAuto.getValue();
            fechaProceso = DTFechaProcesoAuto.getValue();
            mensaje = "de forma automática";
            ingresar = true;
        }
        else if(tipo === "AUTOMATICA" && existenRegistros !== true)
        {
            Ext.Msg.alert('Mensaje', 'No existen registros para procesar');
        }     

        if(ingresar)
        {
            if (sm.getSelection().length > 0 || tipo === 'AUTOMATICA')
            {
                var estado = 0;
                
                if(tipo != 'AUTOMATICA')
                {
                    for (var i = 0; i < sm.getSelection().length; ++i)
                    {
                        param = param + sm.getSelection()[i].data.id;

                        if (sm.getSelection()[i].data.estado == 'Eliminado')
                        {
                            estado = estado + 1;
                        }
                        if (i < (sm.getSelection().length - 1))
                        {
                            param = param + '|';
                        }
                    }
                }

                if (estado == 0 && tipo=='AUTOMATICA')
                {
                    Ext.Msg.confirm('Alerta', 'Se crearán los pagos '+mensaje+'. Desea continuar?', function(btn)
                    {
                        if (btn == 'yes') 
                        {
                            var loadMask = new Ext.LoadMask(Ext.getBody(), {msg: "Procesando ", height: 900});
                            loadMask.show();
                            Ext.Ajax.request({
                                url: url_procesar,
                                timeout: 500000,
                                method: 'post',
                                params: 
                                {
                                    param             : param, 
                                    codigo            : codigoDebito,
                                    fechaProceso      : fechaProceso,
                                    nombreBanco       : nombreBanco,
                                    fechaGenerado     : fechaGenerado,
                                    valorComision     : Ext.getCmp('valor_comision').getValue(),
                                    valorRetencionIva : Ext.getCmp('valor_retencion_iva').getValue(),
                                    valorRetencionFte : Ext.getCmp('valor_retencion_fuente').getValue(), 
                                    porcentajeComision: Ext.getCmp('porcentajeComision').getValue(),
                                    valorNeto         : Ext.getCmp('valor_neto').getValue(),
                                    debitoGeneralId   : debitoGenId,
                                    cuentaContableId  : Ext.getCmp('cmb_ctas_bancarias_empresa').getValue(),
                                    tipoOperacion     : tipo

                                },
                                success: function(response) 
                                {
                                    var arrayRespuesta = response.responseText.split("|");
                                    store.load();
                                    loadMask.hide();                                    
                                    Ext.Msg.alert('Mensaje', arrayRespuesta[1], 
                                    function(btn){
                                        if (btn === 'ok')
                                        {
                                            window.location.href = strUrlListaDebitos;
                                        }
                                    });

                                },
                                failure: function(result)
                                {
                                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                    loadMask.hide();
                                }
                            });
                        }
                    });

                }else if (estado == 0 && tipo=='NORMAL')
                {
                     Ext.Msg.confirm('Alerta', 'Se crearán los pagos '+mensaje+'. Desea continuar?', function(btn)
                    {
                        if (btn == 'yes') 
                        {
                            var loadMask = new Ext.LoadMask(Ext.getBody(), {msg: "Procesando ", height: 900});
                            loadMask.show();
                            Ext.Ajax.request({
                                url: url_procesar,
                                timeout: 500000,
                                method: 'post',
                                params: 
                                {
                                    param             : param, 
                                    codigo            : codigoDebito,
                                    fechaProceso      : fechaProceso,
                                    nombreBanco       : nombreBanco,
                                    fechaGenerado     : fechaGenerado,
                                    valorComision     : Ext.getCmp('valor_comision').getValue(),
                                    valorRetencionIva : Ext.getCmp('valor_retencion_iva').getValue(),
                                    valorRetencionFte : Ext.getCmp('valor_retencion_fuente').getValue(), 
                                    porcentajeComision: Ext.getCmp('porcentajeComision').getValue(),
                                    valorNeto         : Ext.getCmp('valor_neto').getValue(),
                                    debitoGeneralId   : debitoGenId,
                                    cuentaContableId  : Ext.getCmp('cmb_ctas_bancarias_empresa').getValue(),
                                    tipoOperacion     : tipo

                                }
                                
                            });
                            store.load();
                            loadMask.hide();                               
                            Ext.Msg.alert('Mensaje', 'Estimado(a) usuario, al culminar el proceso automático se enviará el correo respectivo..', 
                            function(btn){
                                if (btn === 'ok')
                                {
                                    window.location.href = strUrlListaDebitos;
                                }
                            });
                        }
                    });
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
        else
        {
            if (prefijoEmpresa == "TN" && existenRegistros === true)
            {    
                alert('Debe ingresar el codigo de debito, la fecha de proceso y el banco para poder crear los pagos');
            }
            else if(prefijoEmpresa == "MD" && existenRegistros === true)
            {
                alert('Debe ingresar la fecha de proceso y el banco para poder crear los pagos');
            }    
        }
    }
    
function calcularValores()
{
    if (prefijoEmpresa=='TN')
    {
        Ext.getCmp('valor_retencion_iva').setValue(0);
        Ext.getCmp('valor_retencion_fuente').setValue(0);
        Ext.getCmp('valor_comision').setValue(0);    
        valor_base_imponib = roundNumber((Ext.getCmp('valor_total').getValue()*1) / 1.12, 2 );  

        Ext.getCmp('valor_comision').setValue( roundNumber((Ext.getCmp('valor_total').getValue()*1) * ((Ext.getCmp('porcentajeComision').getValue()*1)/100),2));

        if (Ext.getCmp('retencionIva').getValue()===true)
        {    
            Ext.getCmp('valor_retencion_iva').setValue(
                roundNumber((Ext.getCmp('valor_total').getValue()*1) * ((porcentajeRetencionIva * 1)/100),2 ));
        }

        if (Ext.getCmp('retencionFuente').getValue()===true)
        {    
            if(nombreBanco.toUpperCase().search("DINERS")<0)
            {    
                Ext.getCmp('valor_retencion_fuente').setValue(
                    roundNumber((valor_base_imponib - (Ext.getCmp('valor_comision').getValue()*1) ) * ((porcentajeRetencionFte * 1)/100),2 ));
            }
            else
            {
                Ext.getCmp('valor_retencion_fuente').setValue( roundNumber(valor_base_imponib * ((porcentajeRetencionFte * 1)/100),2 ));            
            }    
        }

        Ext.getCmp('valor_neto').setValue( 
            roundNumber( ((Ext.getCmp('valor_total').getValue()*1) - (Ext.getCmp('valor_comision').getValue()*1) - 
                (Ext.getCmp('valor_retencion_iva').getValue()*1) - (Ext.getCmp('valor_retencion_fuente').getValue()*1)),2) );     
    }
}

});
