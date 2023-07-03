/**
 * Funcion que sirve para cargar grids (ip, configuracion olt)
 * 
 * @author Francisco Adum <fadum@telconet.ec>
 * @version 1.0 23-04-2015
 * @since 1.1
 */
Ext.onReady(function(){
    
    Ext.tip.QuickTipManager.init(); 
    
    var Url = location.href; 
    UrlUrl = Url.replace(/.*\?(.*?)/,"$1");  
    Variables = Url.split ("/");
    var n = Variables.length;
    var id = Variables[n-2];
    
    if(!booleanSegVehiculo && !booleanSafeEntry )
    {
        var storeIps = new Ext.data.Store({ 
                total: 'total',
                autoLoad:true,
                proxy: {
                    type: 'ajax',
                    url : verIps,
                    timeout: 3000000,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    },
                    extraParams: {
                        idServicio: id
                    }
                },
                fields:
                    [
                        {name:'idIp',       mapping:'idIp'   },
                        {name:'ip',         mapping:'ip'     },
                        {name:'mascara',    mapping:'mascara'},
                        {name:'gateway',    mapping:'gateway'},
                        {name:'tipo',       mapping:'tipo'   },
                        {name:'estado',     mapping:'estado' },
                        {name:'mac',        mapping:'mac'    },
                        {name:'subred',     mapping:'subred' },
                        {name:'scope',      mapping:'scope'  },
                        {name:'descripcion',mapping:'descripcion'},
                        {name:'strStyle',   mapping:'strStyle'}
                    ]
            });   
    }
    
    if(prefijoEmpresa == "TN" && !booleanTipoRedGpon && !booleanSegVehiculo && !booleanSafeEntry)
    {   
        var storeConcentradorExtremo = new Ext.data.Store({ 
            total: 'total',
            autoLoad:true,
            proxy: {
                type: 'ajax',
                url : verConcentradorExtr,
                timeout: 3000000,
                reader: {
                    type:           'json',
                    root:           'encontrados'
                },
                extraParams: {
                    idServicio: id
                }
            },
            fields:
                  [
                    {name:'servicioId', mapping:'servicioId'},
                    {name:'login',      mapping:'login'     },
                    {name:'loginAux',   mapping:'loginAux'  },
                    {name:'ip',         mapping:'ip'        },
                    {name:'capacidad1', mapping:'capacidad1'},
                    {name:'capacidad2', mapping:'capacidad2'},
                    {name:'estado',     mapping:'estado'    },
                    {name:'codigoUM',   mapping:'codigoUM'  },
                    {name:'tipo',       mapping:'tipo'      }
                  ]
        });

        gridConcentradorExtremos = Ext.create('Ext.grid.Panel', {
            id:'gridConcentradorExtremos',
            store: storeConcentradorExtremo,
            columnLines: true,
            columns: [
            {
                id: 'tipoEnlace',
                header: 'Tipo',
                dataIndex: 'tipo',
                width: 120,
                sortable: true
            }, 
            {
                id: 'codigoUM',
                header: 'UM',
                dataIndex: 'codigoUM',
                width: 40,
                sortable: true
            },
            {
                id: 'login',
                header: 'Login Punto',
                dataIndex: 'login',
                width: 200,
                sortable: true
            },
            {
                id: 'loginAux',
                header: 'Login Aux',
                dataIndex: 'loginAux',
                width: 200,
                sortable: true
            }, 
            {
                id: 'ipEnlace',
                header: 'Ip',
                dataIndex: 'ip',
                width: 100,
                sortable: true
            },
            {
                id: 'capacidad1',
                header: 'Capacidad1',
                dataIndex: 'capacidad1',
                width: 70,
                sortable: true
            },
            {
                id: 'capacidad2',
                header: 'Capacidad2',
                dataIndex: 'capacidad2',
                width: 70,
                sortable: true
            },
            {
                id: 'estadoServicio',
                header: 'Estado Servicio',
                dataIndex: 'estado',
                width: 130,
                sortable: true
            }],        
            viewConfig:{
                stripeRows:true,
                 enableTextSelection: true
            },
            width: 930,
            height: 400,
            frame: true,
            title: 'Concentrador - Extremos',
            renderTo: 'gridConcentradorExtremos'
        });
    }
        
    var boolEsSatelital = false;
    
    if(ultimaMilla === 'SATELITAL')
    {
        boolEsSatelital = true;
    }
    
    if(esDataCenter === 'SI')
    {
        boolEsSatelital = true;
    }
    
    if(!booleanSegVehiculo && !booleanSafeEntry)
    {
        gridIps = Ext.create('Ext.grid.Panel', {
            id:'gridIps',
            store: storeIps,
            columnLines: true,
            columns: [Ext.create('Ext.grid.RowNumberer'),
            {
                id: 'mac',
                header: 'Mac',
                dataIndex: 'mac',
                width: 120,
                sortable: true,
                hidden:boolEsSatelital
            },
            {
                id: 'descripcion',
                header: 'Descripcion Subred',
                dataIndex: 'descripcion',
                width: 150,
                sortable: true,
                hidden:!boolEsSatelital
            },        
            {
                id: 'ip',
                header: 'Ip',
                dataIndex: 'ip',
                width: 150,
                sortable: true,
                renderer: function (val) 
                {
                    if(boolEsSatelital)
                    {
                        return '<label style="color:green;font-weight: bold;">' + val + '</label>';
                    }
                    else
                    {
                        return val;
                    }
                }
            }, 
            {
                id: 'mascara',
                header: 'Mascara',
                dataIndex: 'mascara',
                width: 100,
                sortable: true
            },
            {
                id: 'gateway',
                header: 'Gateway',
                dataIndex: 'gateway',
                width: 100,
                sortable: true
            },
            {
                id: 'tipo',
                header: 'Tipo',
                dataIndex: 'tipo',
                width: 80,
                sortable: true
            }, 
            {
                id: 'estado',
                header: 'Estado',
                dataIndex: 'estado',
                width: 100,
                sortable: true
            }],        
            viewConfig:{
                stripeRows:true,
                enableTextSelection: true,
                getRowClass: function(record, index) {                            
                    if (record.get("strStyle") === "SI") 
                    {
                        return "x-grid-row-principal";
                    } 
                }
            },
            width: 850,
            height: 200,
            frame: true,
            title: 'Ips',
            renderTo: 'gridIps'
        });
    }
    
    if(prefijoEmpresa == "TN" && !booleanSegVehiculo && !booleanSafeEntry)
    {
        
        gridIps.headerCt.insert(
                                5,
                                {
                                    id: 'subred',
                                    header: 'Subred',
                                    dataIndex: 'subred',
                                    width: 160,
                                    sortable: true
                                }
                            );
    }
    if(prefijoEmpresa == "MD" && !booleanSegVehiculo && !booleanSafeEntry)
    {
        
        gridIps.headerCt.insert(
                                5,
                                {
                                    id: 'scope',
                                    header: 'Scope/Pool',
                                    dataIndex: 'scope',
                                    width: 160,
                                    sortable: true
                                }
                            );
    }
    
    //AJAX PARA CONFIGURACION DE EQUIPO
    if(!booleanSegVehiculo && !booleanSafeEntry)
    {
        Ext.Ajax.request({ 
            url: verConfiguracionOlt,
            method: 'post',
            timeout: 400000,
            params: { 
                idServicio: id
            },
            success: function(response){
                var datos = response.responseText;
                
                formPanel = Ext.create('Ext.form.Panel', {
                    bodyPadding: 2,
                    waitMsgTarget: true,
                    fieldDefaults: {
                        labelAlign: 'left',
                        labelWidth: 85,
                        msgTarget: 'side',
                        bodyStyle: 'padding:20px'
                    },
                    layout: {
                        type: 'table',
                        columns: 1
                    },
                    defaults: {
                        bodyStyle: 'padding:20px'
                    },

                    items: [
                        //Configuracion Dslam
                        {
                            xtype: 'fieldset',
                            title: 'Ver Configuracion Servicio',
                            defaultType: 'textfield',
                            defaults: {
                                width: 810,
                                height: 500
                            },
                            items: [
                                
                                {
                                xtype : 'panel',
                                html : datos,
                                autoScroll: true,
                                layout: 'fit'
                                }
                            ],
                            colspan: 2
                        }//cierre 

                    ],//cierre items
                    width: 850,
                    height: 550,
                    frame: true,
                    renderTo: 'configuracion'
                });
            }//cierre response
        });  
    }
    
    //-------------------------------------------------------------------------------
    
});
