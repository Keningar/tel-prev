Ext.require([
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

Ext.onReady(function()
{
    Ext.tip.QuickTipManager.init();

    var sizeResulSuperIzquierdo = 1204;

    var sizeLogins = 150;
    var sizeServic = 80;
    var sizeDirecc = 350;
    var sizePlands = 180;
    var sizeJurisd = 180;
    var sizeSector = 150;
    var sizeClient = 280;
    var sizeIdentf = 110;
    var sizeAsesor = 280;
    var sizeUsuari = 100;
    var sizeSuperv = 280;
    var sizeCanalV = 110;
    var sizePuntoV = 190;
    var sizeFechAp = 90;
    var sizeFechCr = 90;
    var sizeFechAc = 90;
    var sizePrecio = 90;

    var boolExpandio = false;

    var sizeAltoVentasJefatura = 600;

    var sizeColPadding = 5;
    var sizeToolbarPad = 20;
    var tooltip = '<div class="tooltipStyle">Recargar Resultados por Supervisor</div>';
    var tooltip2 = '<div class="tooltipStyle">Exporta Ventas de la Jefatura</div>';
    var summSt = '<span style="color:white; font-weight:bolder; font-size:12; ';
    var paddingL = 'padding-left:' + sizeColPadding + 'px;';
    var paddingR = 'padding-right:' + sizeColPadding + 'px;';
    var signo = '<div style="float:left; width: 4px; padding-left: 8px; color:{0}; font-weight:bolder; font-size:12">$</div>';

    var activoVentasJefetura = false;
    var activoVentasJefetura2 = false;

    $("#msgVentasJefatura").click(function()
    {
        $("#msgVentasJefatura").hide(400);
        activoVentasJefetura = false;
    });
    $("#msgVentasJefatura2").click(function()
    {
        $("#msgVentasJefatura2").hide(400);
        activoVentasJefetura2 = false;
    });
    
/* INI - ESTADO SERVICIO */
    var storeEstadosServicios = new Ext.data.Store
        ({ 
            total: 'total',
            proxy: 
            {
                timeout: 400000,                
                type: 'ajax',
                url : url_GetEstadosServicios ,
                reader: 
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            fields:
            [
                {name:'estado_servicio', mapping:'estado_servicio'}
            ],
            listeners: 
            {
                load: function(store, records)
                {
                     store.insert(0, [{ estado_servicio: '&nbsp;' }]);
                 
                     Ext.getCmp('cmbEstadoServicio').queryMode = 'local';
                }      
            }
        });
        
     var combo_estadoServicio = new Ext.form.ComboBox( {
                    xtype: 'combobox',
                    id: 'cmbEstadoServicio',
                    name: 'cmbEstadoServicio',
                    fieldLabel: 'Estado del Servicio',
                    labelWidth: 100,
                    labelAlign : 'right',
                    emptyText: "Seleccione",
                    width:262,
                    triggerAction: 'all',
                    displayField:'estado_servicio',
                    valueField: 'estado_servicio',
                    selectOnTab: true,
                    store: storeEstadosServicios,              
                    lazyRender: true,
                    queryMode: "remote",
                    listClass: 'x-combo-list-small',
                    listeners:
                    {
                        select:
                        {
                            fn:function(comp, record, index)
                            {
                                if (comp.getValue() === "" || comp.getValue() === "&nbsp;")
                                {
                                    comp.setValue(null);
                                }
                            }
                        }
                     }
                });   
/* FIN - ESTADO SERVICIO */

/* INI - PLANES */         
Ext.define('ListaDetalleModel', {
				extend: 'Ext.data.Model',
				fields: [
                    {name:'IdPlan', type: 'string'},
                    {name:'Nombre', type: 'string'},
                ]
			}); 
            

var storePlanes = Ext.create('Ext.data.Store', {
                model: 'ListaDetalleModel',
                autoLoad: false,
                proxy: {
                    type: 'ajax',
                    timeout: 90000,
                    url: url_store_grid,
                    pageParam: false,
                    startParam: false,
                    limitParam: false,
                    timeout: 9000000,
                    params: { fechaDesde: '',fechaHasta: ''},
                    reader: {
                        type: 'json',
						root: 'arreglo',
						totalProperty: 'total'
                    }
                },  
            });
            
var combo_planes = new Ext.form.ComboBox({
                xtype: 'combobox',
                store: storePlanes,
                labelAlign: 'left',
                emptyText: 'Escriba y Seleccione Plan',
                id: 'IdPlan',
                name: 'IdPlan',
                valueField: 'IdPlan',
                displayField: 'Nombre',
                fieldLabel: 'Planes',
                width: 270,
                allowBlank: true,
                listeners: {
                    change: {fn: function(combo, newValue, oldValue) {
                            nombrePlan = combo.getRawValue();
                            storePlanes.proxy.extraParams = {strNombrePlan: nombrePlan};
                            storePlanes.load();
                        }}
                }
            });
   /* FIN - PLANES */         

   /* INICIO - CLIENTE */
    Ext.define('ClienteList', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'idcliente', type: 'int'},
                    {name: 'descripcion', type: 'string'}
                ]
            });

            var storeClientes = Ext.create('Ext.data.Store', {
                model: 'ClienteList',
                autoLoad: false,
                proxy: {
                    type: 'ajax',
                    timeout: 90000,
                    url: url_lista_clientes_por_roles,
                    reader: {
                        type: 'json',
                        root: 'clientes'
                    }
                }

            });

            var combo_clientes = new Ext.form.ComboBox({
                xtype: 'combobox',
                store: storeClientes,
                labelAlign: 'left',
                emptyText: 'Escriba y Seleccione Cliente',
                id: 'idcliente',
                name: 'idcliente',
                valueField: 'idcliente',
                displayField: 'descripcion',
                fieldLabel: 'Clientes',
                width: 300,
                allowBlank: true
            });
            
   /* FIN - CLIENTE */
   
    /* INI - LOGIN */
            Ext.define('modelListPuntos', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'id_pto_cliente', type: 'string'},
                    {name: 'descripcion_pto', type: 'string'}
                ]
            });

            var listaPtos_store = Ext.create('Ext.data.Store', {
                autoLoad: false,
                model: "modelListPuntos",
                proxy: {
                    type: 'ajax',
                    url: url_lista_ptos,
                    timeout: 9000000,
                    reader: {
                        type: 'json',
                        root: 'listado'
                    }
                },
                listeners: {
                    load: function(store) {
                        Ext.ComponentQuery.query('combobox[name=idcliente]')[0].reset();
                    }
                }		
            });

            var listaPtos_cmb = new Ext.form.ComboBox({
                xtype: 'combobox',
                name: 'idpunto',
                id: 'idpunto',
                valueField: 'id_pto_cliente',
                store: listaPtos_store,
                labelAlign: 'left',
                displayField: 'descripcion_pto',
                fieldLabel: 'Login',
                width: 325,
                triggerAction: 'all',
                mode: 'local',
                allowBlank: false,
                emptyText: 'Ingrese al menos los 4 primeros caracteres...',
                listeners: {
                    select: {fn: function(combo, value) {
                            storeClientes.proxy.extraParams = {idpunto: combo.getValue()};
                            Ext.ComponentQuery.query('combobox[name=idcliente]')[0].reset();
                            Ext.ComponentQuery.query('combobox[name=idcliente]')[0].setDisabled(false);
                            storeClientes.load();
                        }},
                    change: {fn: function(combo, newValue, oldValue) {
                            Ext.ComponentQuery.query('combobox[name=idcliente]')[0].reset();
                        }}
                }
            });  
    /* FIN - LOGIN */

    /* INI - FECHA APROBACION */ 
    fechaAprobacion = Ext.create('Ext.toolbar.Toolbar',
        {
            items:
                [
                    Ext.define('Ext.form.field.Month',
                        {
                            extend: 'Ext.form.field.Date',
                            alias: 'widget.monthfield',
                            id: 'dateFechaAprobacion',
                            name: 'dateFechaAprobacion',
                            format: 'F, Y',
                            labelWidth: 110,
                            width: 200,
                            style: 'margin-top: 0px; margin-left: 0px;',
                            fieldLabel: 'Mes de Aprobacion',
                            maxValue: new Date(),
                            editable: false,
                            requires: ['Ext.picker.Month'],
                            alternateClassName: ['Ext.form.MonthField', 'Ext.form.Month'],
                            selectMonth: null,
                            handler: function(button)
                            {
                                var gridpanel = button.up('gridpanel');
                                var gridview = gridpanel.getView();
                                gridview.emptyText = '<div class="x-grid-empty">Test</div>';
                                gridview.refresh();
                            },
                            createPicker: function()
                            {
                                var me = this;
                                var format = Ext.String.format;
                                return Ext.create('Ext.picker.Month',
                                    {
                                        pickerField: me,
                                        ownerCt: me.ownerCt,
                                        renderTo: document.body,
                                        floating: true,
                                        hidden: true,
                                        focusOnShow: true,
                                        minDate: me.minValue,
                                        maxDate: me.maxValue,
                                        disabledDatesRE: me.disabledDatesRE,
                                        disabledDatesText: me.disabledDatesText,
                                        disabledDays: me.disabledDays,
                                        disabledDaysText: me.disabledDaysText,
                                        format: me.format,
                                        showToday: me.showToday,
                                        startDay: me.startDay,
                                        minText: format(me.minText, me.formatDate(me.minValue)),
                                        maxText: format(me.maxText, me.formatDate(me.maxValue)),
                                        listeners:
                                            {
                                                select:
                                                    {
                                                        scope: me,
                                                        fn: me.onSelect
                                                    },
                                                monthdblclick:
                                                    {
                                                        scope: me,
                                                        fn: me.onOKClick
                                                    },
                                                yeardblclick:
                                                    {
                                                        scope: me,
                                                        fn: me.onOKClick
                                                    },
                                                OkClick:
                                                    {
                                                        scope: me,
                                                        fn: me.onOKClick
                                                    },
                                                CancelClick:
                                                    {
                                                        scope: me,
                                                        fn: me.onCancelClick
                                                    }
                                            },
                                        keyNavConfig:
                                            {
                                                esc: function()
                                                {
                                                    me.collapse();
                                                }
                                            }
                                    });
                            },
                            onCancelClick: function()
                            {
                                var me = this;
                                me.selectMonth = null;
                                me.collapse();
                            },
                            onOKClick: function()
                            {
                                var me = this;
                                var titulo = '';
                                if (me.selectMonth)
                                {
                                    me.setValue(me.selectMonth);
                                    me.fireEvent('select', me, me.selectMonth);
                                    dataStoreVentasJefatura.getProxy().extraParams.idPunto = Ext.getCmp('idpunto').getValue();
                                    dataStoreVentasJefatura.getProxy().extraParams.idCliente = Ext.getCmp('idcliente').getValue();
                                    dataStoreVentasJefatura.getProxy().extraParams.direccionPunto = Ext.getCmp('direccion_punto').value;
                                    dataStoreVentasJefatura.getProxy().extraParams.idPlan = Ext.getCmp('IdPlan').getValue();
                                    dataStoreVentasJefatura.getProxy().extraParams.idPtoCobertura = Ext.getCmp('idptocobertura').getValue();
                                    dataStoreVentasJefatura.getProxy().extraParams.idSector = Ext.getCmp('idsector').getValue();
                                    dataStoreVentasJefatura.getProxy().extraParams.EstadoServicio = Ext.getCmp('cmbEstadoServicio').getRawValue();
                                    dataStoreVentasJefatura.getProxy().extraParams.mesCreacionPunto = Ext.getCmp('dateFechaCreacionPunto').value;
                                    dataStoreVentasJefatura.getProxy().extraParams.mesAprobacion = Ext.getCmp('dateFechaAprobacion').value;
                                    dataStoreVentasJefatura.getProxy().extraParams.mes = Ext.getCmp('dateVentasJefatura').value;
                                    dataStoreVentasJefatura.getProxy().extraParams.supervisor = Ext.getCmp('cbxSupervisores2').value;
                                    dataStoreVentasJefatura.getProxy().extraParams.asesor = Ext.getCmp('cbxAsesores2').value;
                                    dataStoreVentasJefatura.getProxy().extraParams.canal = Ext.getCmp('cbxCanales').value;
                                    dataStoreVentasJefatura.getProxy().extraParams.puntoVenta = Ext.getCmp('cbxPuntosVenta').value;
                                    dataStoreVentasJefatura.getProxy().extraParams.supervisorDesc = Ext.getCmp('cbxSupervisores2').getRawValue();
                                    dataStoreVentasJefatura.getProxy().extraParams.asesorDesc = Ext.getCmp('cbxAsesores2').getRawValue();
                                    dataStoreVentasJefatura.getProxy().extraParams.canalDesc = Ext.getCmp('cbxCanales').getRawValue();
                                    dataStoreVentasJefatura.getProxy().extraParams.puntoVentaDesc = Ext.getCmp('cbxPuntosVenta').getRawValue();
                                    dataStoreVentasJefatura.currentPage = 1;
                                    dataStoreVentasJefatura.load();
                                    titulo = 'REPORTE DE VENTAS JEFATURA DE ';
                                    titulo += Ext.getCmp('dateVentasJefatura').getRawValue().toUpperCase();
                                    gridVentasJefatura.setTitle(titulo);
                                }
                                me.collapse();
                            },
                            onSelect: function(m, d)
                            {
                                var me = this;
                                me.selectMonth = new Date((d[0] + 1) + '/1/' + d[1]);
                            }
                        }
                    )
                ]
        });
    /* FIN - FECHA APROBACION */
    
    /* INI - FECHA CREACION PUNTO */
    fechaCreacionPunto = Ext.create('Ext.toolbar.Toolbar',
        {
            items:
                [
                    Ext.define('Ext.form.field.Month',
                        {
                            extend: 'Ext.form.field.Date',
                            alias: 'widget.monthfield',
                            id: 'dateFechaCreacionPunto',
                            name: 'dateFechaCreacionPunto',
                            format: 'F, Y',
                            labelWidth: 110,
                            width: 200,
                            style: 'margin-top: 0px; margin-left: 0px;',
                            fieldLabel: 'Mes de Creacion del Punto',
                            maxValue: new Date(),
                            editable: false,
                            requires: ['Ext.picker.Month'],
                            alternateClassName: ['Ext.form.MonthField', 'Ext.form.Month'],
                            selectMonth: null,
                            handler: function(button)
                            {
                                var gridpanel = button.up('gridpanel');
                                var gridview = gridpanel.getView();
                                gridview.emptyText = '<div class="x-grid-empty">Test</div>';
                                gridview.refresh();
                            },
                            createPicker: function()
                            {
                                var me = this;
                                var format = Ext.String.format;
                                return Ext.create('Ext.picker.Month',
                                    {
                                        pickerField: me,
                                        ownerCt: me.ownerCt,
                                        renderTo: document.body,
                                        floating: true,
                                        hidden: true,
                                        focusOnShow: true,
                                        minDate: me.minValue,
                                        maxDate: me.maxValue,
                                        disabledDatesRE: me.disabledDatesRE,
                                        disabledDatesText: me.disabledDatesText,
                                        disabledDays: me.disabledDays,
                                        disabledDaysText: me.disabledDaysText,
                                        format: me.format,
                                        showToday: me.showToday,
                                        startDay: me.startDay,
                                        minText: format(me.minText, me.formatDate(me.minValue)),
                                        maxText: format(me.maxText, me.formatDate(me.maxValue)),
                                        listeners:
                                            {
                                                select:
                                                    {
                                                        scope: me,
                                                        fn: me.onSelect
                                                    },
                                                monthdblclick:
                                                    {
                                                        scope: me,
                                                        fn: me.onOKClick
                                                    },
                                                yeardblclick:
                                                    {
                                                        scope: me,
                                                        fn: me.onOKClick
                                                    },
                                                OkClick:
                                                    {
                                                        scope: me,
                                                        fn: me.onOKClick
                                                    },
                                                CancelClick:
                                                    {
                                                        scope: me,
                                                        fn: me.onCancelClick
                                                    }
                                            },
                                        keyNavConfig:
                                            {
                                                esc: function()
                                                {
                                                    me.collapse();
                                                }
                                            }
                                    });
                            },
                            onCancelClick: function()
                            {
                                var me = this;
                                me.selectMonth = null;
                                me.collapse();
                            },
                            onOKClick: function()
                            {
                                var me = this;
                                var titulo = '';
                                if (me.selectMonth)
                                {
                                    me.setValue(me.selectMonth);
                                    me.fireEvent('select', me, me.selectMonth);
                                    dataStoreVentasJefatura.getProxy().extraParams.idPunto = Ext.getCmp('idpunto').getValue();
                                    dataStoreVentasJefatura.getProxy().extraParams.idCliente = Ext.getCmp('idcliente').getValue();
                                    dataStoreVentasJefatura.getProxy().extraParams.direccionPunto = Ext.getCmp('direccion_punto').value;
                                    dataStoreVentasJefatura.getProxy().extraParams.idPlan = Ext.getCmp('IdPlan').getValue();
                                    dataStoreVentasJefatura.getProxy().extraParams.idPtoCobertura = Ext.getCmp('idptocobertura').getValue();
                                    dataStoreVentasJefatura.getProxy().extraParams.idSector = Ext.getCmp('idsector').getValue();
                                    dataStoreVentasJefatura.getProxy().extraParams.EstadoServicio = Ext.getCmp('cmbEstadoServicio').getRawValue();
                                    dataStoreVentasJefatura.getProxy().extraParams.mesCreacionPunto = Ext.getCmp('dateFechaCreacionPunto').value;
                                    dataStoreVentasJefatura.getProxy().extraParams.mesAprobacion = Ext.getCmp('dateFechaAprobacion').value;
                                    dataStoreVentasJefatura.getProxy().extraParams.mes = Ext.getCmp('dateVentasJefatura').value;
                                    dataStoreVentasJefatura.getProxy().extraParams.supervisor = Ext.getCmp('cbxSupervisores2').value;
                                    dataStoreVentasJefatura.getProxy().extraParams.asesor = Ext.getCmp('cbxAsesores2').value;
                                    dataStoreVentasJefatura.getProxy().extraParams.canal = Ext.getCmp('cbxCanales').value;
                                    dataStoreVentasJefatura.getProxy().extraParams.puntoVenta = Ext.getCmp('cbxPuntosVenta').value;
                                    dataStoreVentasJefatura.getProxy().extraParams.supervisorDesc = Ext.getCmp('cbxSupervisores2').getRawValue();
                                    dataStoreVentasJefatura.getProxy().extraParams.asesorDesc = Ext.getCmp('cbxAsesores2').getRawValue();
                                    dataStoreVentasJefatura.getProxy().extraParams.canalDesc = Ext.getCmp('cbxCanales').getRawValue();
                                    dataStoreVentasJefatura.getProxy().extraParams.puntoVentaDesc = Ext.getCmp('cbxPuntosVenta').getRawValue();
                                    dataStoreVentasJefatura.currentPage = 1;
                                    dataStoreVentasJefatura.load();
                                    titulo = 'REPORTE DE VENTAS JEFATURA DE ';
                                    titulo += Ext.getCmp('dateVentasJefatura').getRawValue().toUpperCase();
                                    gridVentasJefatura.setTitle(titulo);
                                }
                                me.collapse();
                            },
                            onSelect: function(m, d)
                            {
                                var me = this;
                                me.selectMonth = new Date((d[0] + 1) + '/1/' + d[1]);
                            }
                        }
                    )
                ]
        });
    /* FIN - FECHA CREACION PUNTO */

    /* INI - PTOS DE COBERTURA*/
     Ext.define('ListModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id', type:'int'},
            {name:'nombre', type:'string'}
        ]
    });
    
    storePtosCobertura = Ext.create('Ext.data.Store',
        {
            id: 'storePtosCobertura',
            autoLoad: true,
            model: 'ListModel',
            proxy:
                {
                    type: 'ajax',
                    timeout: 90000,
                    url: url_puntoscobertura,
                    reader:
                        {
                            type: 'json',
                            root: 'jurisdicciones'
                        }
                }
        });
        
        
     combo_ptoscobertura = new Ext.form.ComboBox(
        {
            xtype: 'combobox',
            id: 'idptocobertura',
            valueField: 'id',
            displayField: 'nombre',
            name: 'idptocobertura',
            store: storePtosCobertura,
            labelAlign: 'left',
            fieldLabel: 'Jurisdiccion',
            width: 325,
            allowBlank: false,
            emptyText: 'Escriba y Seleccione Pto Cobertura'
        });   
    /* FIN - PTOS DE COBERTURA*/
    
    /* INI - SECTOR */
    storeSectores = Ext.create('Ext.data.Store',
        {
            autoLoad: false,
            model: "ListModel",
            proxy:
                {
                    type: 'ajax',
                    url: url_lista_sectores,
                    reader:
                        {
                            type: 'json',
                            root: 'sectores'
                        }
                }
        });
        
    combo_sector = new Ext.form.ComboBox({
                xtype: 'combobox',
                store: storeSectores,
                labelAlign : 'left',
                id: 'idsector',        
                name: 'idsector',
                valueField:'id',
                displayField:'nombre',
                fieldLabel: 'Sector',
                width: 300,
                triggerAction: 'all',
                mode: 'local',
                allowBlank: false,	
                emptyText: 'Seleccione Sector'
            });    
    /* FIN - SECTOR */
    
    Ext.define('ModelStoreSupervisores',
        {
            extend: 'Ext.data.Model',
            fields:
                [
                    {name: 'id_persona_sup', mapping: 'id_persona_sup', type: 'string'},
                    {name: 'nombre_sup', mapping: 'nombre_sup', type: 'string'}
                ]
        });

    Ext.define('ModelStoreAsesores',
        {
            extend: 'Ext.data.Model',
            fields:
                [
                    {name: 'login_asesor', mapping: 'login_asesor', type: 'string'},
                    {name: 'nombre_ase', mapping: 'nombre_ase', type: 'string'}
                ]
        });

    Ext.define('ListModelCanal',
        {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'canal', type: 'string'},
                {name: 'descripcion', Metype: 'string'}
            ]
        });

    Ext.define('ListModelPuntoVenta',
        {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'punto_venta', type: 'string'},
                {name: 'descripcion', type: 'string'}
            ]
        });

    Ext.define('ModelStoreVentasJefatura',
        {
            extend: 'Ext.data.Model',
            fields:
                [
                    {name: 'login', mapping: 'login', type: 'string'},
                    {name: 'servicio', mapping: 'servicio', type: 'string'},
                    {name: 'direccion', mapping: 'direccion', type: 'string'},
                    {name: 'nombre_servicio', mapping: 'nombre_servicio', type: 'string'},
                    {name: 'jurisdiccion_', mapping: 'jurisdiccion_', type: 'string'},
                    {name: 'empresa', mapping: 'empresa', type: 'string'},
                    {name: 'sector', mapping: 'sector', type: 'string'},
                    {name: 'cliente', mapping: 'cliente', type: 'string'},
                    {name: 'identificacion', mapping: 'identificacion', type: 'string'},
                    {name: 'vendedor', mapping: 'vendedor', type: 'string'},
                    {name: 'usuario', mapping: 'usuario', type: 'string'},
                    {name: 'supervisor', mapping: 'supervisor', type: 'string'},
                    {name: 'canal', mapping: 'canal', type: 'string'},
                    {name: 'punto_venta', mapping: 'punto_venta', type: 'string'},
                    {name: 'aprobacion', mapping: 'aprobacion', type: 'string'},
                    {name: 'creacion', mapping: 'creacion', type: 'string'},
                    {name: 'activacion', mapping: 'activacion', type: 'string'},
                    {name: 'precio', mapping: 'precio', type: 'float'}
                ]
        });

    dataStoreSupervisores2 = new Ext.data.Store(
        {
            autoLoad: false,
            model: 'ModelStoreSupervisores',
            proxy:
                {type: 'ajax',
                    timeout: 600000,
                    url: urlCargarSupervisores,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'supervisores'
                        }
                }
        });

    dataStoreAsesores2 = new Ext.data.Store(
        {
            model: 'ModelStoreAsesores',
            proxy:
                {
                    type: 'ajax',
                    timeout: 600000,
                    url: urlCargarAsesores,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'asesores'
                        }
                }
        });

    dataStoreVentasJefatura = new Ext.data.Store(
        {
            model: 'ModelStoreVentasJefatura',
            proxy:
                {
                    type: 'ajax',
                    timeout: 600000,
                    url: urlGridVentasJefatura,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'ventas_jefatura'
                        }
                }
        });

    storeCanales = Ext.create('Ext.data.Store',
        {
            model: 'ListModelCanal',
            autoLoad: false,
            proxy:
                {
                    type: 'ajax',
                    url: urlCanales,
                    reader:
                        {
                            type: 'json',
                            root: 'canales'
                        }
                }
        });

    storePuntosVenta = Ext.create('Ext.data.Store',
        {
            model: 'ListModelPuntoVenta',
            autoLoad: false,
            proxy:
                {
                    type: 'ajax',
                    url: urlPuntosVenta,
                    reader: {
                        type: 'json',
                        root: 'puntos_venta'
                    }
                }
        });

    fechaVentasJefatura = Ext.create('Ext.toolbar.Toolbar',
        {
            items:
                [
                    Ext.define('Ext.form.field.Month',
                        {
                            extend: 'Ext.form.field.Date',
                            alias: 'widget.monthfield',
                            id: 'dateVentasJefatura',
                            name: 'dateVentasJefatura',
                            format: 'F, Y',
                            labelWidth: 100,
                            width: 200,
                            style: 'margin-top: 0px; margin-left: 0px;',
                            fieldLabel: 'Mes Activación',
                            maxValue: new Date(),
                            editable: false,
                            requires: ['Ext.picker.Month'],
                            alternateClassName: ['Ext.form.MonthField', 'Ext.form.Month'],
                            selectMonth: null,
                            handler: function(button)
                            {
                                var gridpanel = button.up('gridpanel');
                                var gridview = gridpanel.getView();
                                gridview.emptyText = '<div class="x-grid-empty">Test</div>';
                                gridview.refresh();
                            },
                            createPicker: function()
                            {
                                var me = this;
                                var format = Ext.String.format;
                                return Ext.create('Ext.picker.Month',
                                    {
                                        pickerField: me,
                                        ownerCt: me.ownerCt,
                                        renderTo: document.body,
                                        floating: true,
                                        hidden: true,
                                        focusOnShow: true,
                                        minDate: me.minValue,
                                        maxDate: me.maxValue,
                                        disabledDatesRE: me.disabledDatesRE,
                                        disabledDatesText: me.disabledDatesText,
                                        disabledDays: me.disabledDays,
                                        disabledDaysText: me.disabledDaysText,
                                        format: me.format,
                                        showToday: me.showToday,
                                        startDay: me.startDay,
                                        minText: format(me.minText, me.formatDate(me.minValue)),
                                        maxText: format(me.maxText, me.formatDate(me.maxValue)),
                                        listeners:
                                            {
                                                select:
                                                    {
                                                        scope: me,
                                                        fn: me.onSelect
                                                    },
                                                monthdblclick:
                                                    {
                                                        scope: me,
                                                        fn: me.onOKClick
                                                    },
                                                yeardblclick:
                                                    {
                                                        scope: me,
                                                        fn: me.onOKClick
                                                    },
                                                OkClick:
                                                    {
                                                        scope: me,
                                                        fn: me.onOKClick
                                                    },
                                                CancelClick:
                                                    {
                                                        scope: me,
                                                        fn: me.onCancelClick
                                                    }
                                            },
                                        keyNavConfig:
                                            {
                                                esc: function()
                                                {
                                                    me.collapse();
                                                }
                                            }
                                    });
                            },
                            onCancelClick: function()
                            {
                                var me = this;
                                me.selectMonth = null;
                                me.collapse();
                            },
                            onOKClick: function()
                            {
                                var me = this;
                                var titulo = '';
                                if (me.selectMonth)
                                {
                                    me.setValue(me.selectMonth);
                                    me.fireEvent('select', me, me.selectMonth);
                                    dataStoreVentasJefatura.getProxy().extraParams.idPunto = Ext.getCmp('idpunto').getValue();
                                    dataStoreVentasJefatura.getProxy().extraParams.idCliente = Ext.getCmp('idcliente').getValue();
                                    dataStoreVentasJefatura.getProxy().extraParams.direccionPunto = Ext.getCmp('direccion_punto').value;
                                    dataStoreVentasJefatura.getProxy().extraParams.idPlan = Ext.getCmp('IdPlan').getValue();
                                    dataStoreVentasJefatura.getProxy().extraParams.idPtoCobertura = Ext.getCmp('idptocobertura').getValue();
                                    dataStoreVentasJefatura.getProxy().extraParams.idSector = Ext.getCmp('idsector').getValue();
                                    dataStoreVentasJefatura.getProxy().extraParams.EstadoServicio = Ext.getCmp('cmbEstadoServicio').getRawValue();
                                    dataStoreVentasJefatura.getProxy().extraParams.mesCreacionPunto = Ext.getCmp('dateFechaCreacionPunto').value;
                                    dataStoreVentasJefatura.getProxy().extraParams.mesAprobacion = Ext.getCmp('dateFechaAprobacion').value;
                                    dataStoreVentasJefatura.getProxy().extraParams.mes = Ext.getCmp('dateVentasJefatura').value;
                                    dataStoreVentasJefatura.getProxy().extraParams.supervisor = Ext.getCmp('cbxSupervisores2').value;
                                    dataStoreVentasJefatura.getProxy().extraParams.asesor = Ext.getCmp('cbxAsesores2').value;
                                    dataStoreVentasJefatura.getProxy().extraParams.canal = Ext.getCmp('cbxCanales').value;
                                    dataStoreVentasJefatura.getProxy().extraParams.puntoVenta = Ext.getCmp('cbxPuntosVenta').value;
                                    dataStoreVentasJefatura.getProxy().extraParams.supervisorDesc = Ext.getCmp('cbxSupervisores2').getRawValue();
                                    dataStoreVentasJefatura.getProxy().extraParams.asesorDesc = Ext.getCmp('cbxAsesores2').getRawValue();
                                    dataStoreVentasJefatura.getProxy().extraParams.canalDesc = Ext.getCmp('cbxCanales').getRawValue();
                                    dataStoreVentasJefatura.getProxy().extraParams.puntoVentaDesc = Ext.getCmp('cbxPuntosVenta').getRawValue();
                                    dataStoreVentasJefatura.currentPage = 1;
                                    dataStoreVentasJefatura.load();
                                    titulo = 'REPORTE DE VENTAS JEFATURA DE ';
                                    titulo += Ext.getCmp('dateVentasJefatura').getRawValue().toUpperCase();
                                    gridVentasJefatura.setTitle(titulo);
                                }
                                me.collapse();
                            },
                            onSelect: function(m, d)
                            {
                                var me = this;
                                me.selectMonth = new Date((d[0] + 1) + '/1/' + d[1]);
                            }
                        }
                    )
                ]
        });

    cbxSupervisores2 = Ext.create('Ext.toolbar.Toolbar',
        {
            items:
                [
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Supervisor',
                        id: 'cbxSupervisores2',
                        value: 'Todos',
                        labelWidth: '6',
                        store: dataStoreSupervisores2,
                        displayField: 'nombre_sup',
                        valueField: 'id_persona_sup',
                        width: 240,
                        triggerAction: 'all',
                        queryMode: 'local',
                        allowBlank: true,
                        editable: false,
                        matchFieldWidth: false,
                        listeners:
                            {
                                select: function()
                                {
                                    //Consulta los Asesores por el supervisor seleccionado
                                    Ext.getCmp('cbxAsesores2').value = 'Todos';
                                    Ext.getCmp('cbxAsesores2').setRawValue('Todos');
                                    dataStoreAsesores2.getProxy().extraParams.supervisor = Ext.getCmp('cbxSupervisores2').value;
                                    dataStoreAsesores2.currentPage = 1;
                                    dataStoreAsesores2.load();
                                }
                            },
                        listConfig:
                            {
                                listeners:
                                    {
                                        beforeshow: function(picker)
                                        {
                                            picker.minWidth = picker.up('combobox').getSize().width;
                                        }
                                    }
                            }
                    }
                ]
        });

    cbxAsesores2 = Ext.create('Ext.toolbar.Toolbar',
        {
            items:
                [
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Asesor',
                        id: 'cbxAsesores2',
                        value: 'Todos',
                        labelWidth: '4',
                        store: dataStoreAsesores2,
                        displayField: 'nombre_ase',
                        valueField: 'login_asesor',
                        width: 240,
                        allowBlank: true,
                        editable: false,
                        matchFieldWidth: false,
                        listConfig:
                            {
                                listeners:
                                    {
                                        beforeshow: function(picker)
                                        {
                                            picker.minWidth = picker.up('combobox').getSize().width;
                                        }
                                    }
                            }
                    }
                ]
        });

    cbxServicios = Ext.create('Ext.toolbar.Toolbar',
        {
            items:
                [
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Servicio',
                        id: 'cbxServicios',
                        value: 'Todos',
                        labelWidth: '7',
                        store:
                            [
                                ['Todos', 'Todos'],
                                ['Activo', 'Activo'],
                                ['Asignada', 'Asignada'],
                                ['AsignadoTarea', 'Asignado Tarea'],
                                ['Detenido', 'Detenido'],
                                ['EnPruebas', 'En Pruebas'],
                                ['EnVerificacion', 'En Verificacion'],
                                ['Inactivo', 'Inactivo'],
                                ['Preplanificada', 'Pre-planificada'],
                                ['Planificada', 'Planificada'],
                                ['Replanificada', 'Re-planificada']
                            ],
                        displayField: 'nombre_sup',
                        valueField: 'id_persona_sup',
                        style: 'margin-top: 0px; margin-left: ' + sizeToolbarPad + 'px;',
                        width: 250,
                        triggerAction: 'all',
                        queryMode: 'local',
                        allowBlank: true,
                        editable: false,
                        matchFieldWidth: false
                    }
                ]
        });

    cbxCanales = Ext.create('Ext.toolbar.Toolbar',
        {
            items:
                [
                    {
                        xtype: 'combobox',
                        store: storeCanales,
                        labelAlign: 'left',
                        id: 'cbxCanales',
                        valueField: 'canal',
                        displayField: 'descripcion',
                        fieldLabel: 'Canal',
                        labelWidth: '5',
                        style: 'cursor:pointer',
                        width: 150,
                        value: '%',
                        allowBlank: false,
                        editable: false,
                        matchFieldWidth: false,
                        listeners:
                            {
                                select: function()
                                {
                                    //Consulta los Puntos de venta por el canal seleccionado
                                    Ext.getCmp('cbxPuntosVenta').value = '%';
                                    Ext.getCmp('cbxPuntosVenta').setRawValue('Todos');
                                    storePuntosVenta.getProxy().extraParams.canal = Ext.getCmp('cbxCanales').value;
                                    storePuntosVenta.currentPage = 1;
                                    storePuntosVenta.load();
                                }
                            },
                        listConfig:
                            {
                                listeners:
                                    {
                                        beforeshow: function(picker)
                                        {
                                            picker.minWidth = picker.up('combobox').getSize().width;
                                        }
                                    }
                            }
                    }
                ]
        });

    cbxPuntosVenta = Ext.create('Ext.toolbar.Toolbar',
        {
            items:
                [
                    {
                        xtype: 'combobox',
                        store: storePuntosVenta,
                        labelAlign: 'left',
                        id: 'cbxPuntosVenta',
                        valueField: 'punto_venta',
                        displayField: 'descripcion',
                        fieldLabel: 'Punto de Venta',
                        labelStyle: 'white-space: nowrap;',
                        labelWidth: '8',
                        width: 230,
                        value: '%',
                        allowBlank: false,
                        editable: false,
                        matchFieldWidth: false,
                        listConfig:
                            {
                                listeners:
                                    {
                                        beforeshow: function(picker)
                                        {
                                            picker.minWidth = picker.up('combobox').getSize().width;
                                        }
                                    }
                            }
                    }
                ]
        });

    btnRefresh =
        {
            xtype: 'button',
            id: 'refreshVentasJefatura',
            iconCls: 'iconReloadDataStore',
            handler: function()
            {
                fecha                 = Ext.getCmp('dateVentasJefatura').value;
                fechaCreacionPunto    = Ext.getCmp('dateFechaCreacionPunto').value;
                fechaAprobacion       = Ext.getCmp('dateFechaAprobacion').value;
                
                if (((typeof fecha === 'undefined') || fecha == null) && 
                    ((typeof fechaCreacionPunto === 'undefined') || fechaCreacionPunto == null)  &&
                    ((typeof fechaAprobacion === 'undefined') || fechaAprobacion == null ))
                {
                    if (!activoVentasJefetura)
                    {
                        setTimeout(function()
                        {
                            activoVentasJefetura = true;
                            $('#msgVentasJefatura').show(100);
                        }, 0);
                        setTimeout(function()
                        {
                            $('#msgVentasJefatura').hide(400);
                            setTimeout(function()
                            {
                                activoVentasJefetura = false;
                            }, 400);
                        }, 3000); // Tiempo que espera antes de ejecutar el código interno
                    }
                }
                else
                {
                    dataStoreVentasJefatura.getProxy().extraParams.idPunto = Ext.getCmp('idpunto').getValue();
                    dataStoreVentasJefatura.getProxy().extraParams.idCliente = Ext.getCmp('idcliente').getValue();
                    dataStoreVentasJefatura.getProxy().extraParams.direccionPunto = Ext.getCmp('direccion_punto').value;
                    dataStoreVentasJefatura.getProxy().extraParams.idPlan = Ext.getCmp('IdPlan').getValue();
                    dataStoreVentasJefatura.getProxy().extraParams.idPtoCobertura = Ext.getCmp('idptocobertura').getValue();
                    dataStoreVentasJefatura.getProxy().extraParams.idSector = Ext.getCmp('idsector').getValue();
                    dataStoreVentasJefatura.getProxy().extraParams.EstadoServicio = Ext.getCmp('cmbEstadoServicio').getRawValue();
                    dataStoreVentasJefatura.getProxy().extraParams.mesCreacionPunto = Ext.getCmp('dateFechaCreacionPunto').value;
                    dataStoreVentasJefatura.getProxy().extraParams.mesAprobacion = Ext.getCmp('dateFechaAprobacion').value;
                    dataStoreVentasJefatura.getProxy().extraParams.mes = Ext.getCmp('dateVentasJefatura').value;
                    dataStoreVentasJefatura.getProxy().extraParams.supervisor = Ext.getCmp('cbxSupervisores2').value;
                    dataStoreVentasJefatura.getProxy().extraParams.asesor = Ext.getCmp('cbxAsesores2').value;
                    dataStoreVentasJefatura.getProxy().extraParams.canal = Ext.getCmp('cbxCanales').value;
                    dataStoreVentasJefatura.getProxy().extraParams.puntoVenta = Ext.getCmp('cbxPuntosVenta').value;
                    dataStoreVentasJefatura.getProxy().extraParams.supervisorDesc = Ext.getCmp('cbxSupervisores2').getRawValue();
                    dataStoreVentasJefatura.getProxy().extraParams.asesorDesc = Ext.getCmp('cbxAsesores2').getRawValue();
                    dataStoreVentasJefatura.getProxy().extraParams.canalDesc = Ext.getCmp('cbxCanales').getRawValue();
                    dataStoreVentasJefatura.getProxy().extraParams.puntoVentaDesc = Ext.getCmp('cbxPuntosVenta').getRawValue();
                    dataStoreVentasJefatura.currentPage = 1;
                    dataStoreVentasJefatura.load();
                }
            },
            listeners:
                {
                    afterrender: function()
                    {
                        Ext.create('Ext.tip.ToolTip',
                            {
                                target: 'refreshVentasJefatura',
                                html: tooltip,
                                anchor: 'top'
                            });
                    }
                }
        };

    btnExportar =
        {
            xtype: 'button',
            id: 'exportarVentasJefatura',
            iconCls: 'icon_exportar',
            handler: function()
            {
                var permiso = $("#ROLE_312-3446");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

                if (boolPermiso)
                {
                    if (dataStoreVentasJefatura.data.getCount() == 0)
                    {
                        if (!activoVentasJefetura2)
                        {
                            setTimeout(function()
                            {
                                activoVentasJefetura2 = true;
                                $('#msgVentasJefatura2').show(100);
                            }, 0);
                            setTimeout(function()
                            {
                                $('#msgVentasJefatura2').hide(400);
                                setTimeout(function()
                                {
                                    activoVentasJefetura2 = false;
                                }, 400);
                            }, 3000); // Tiempo que espera antes de ejecutar el código interno
                        }
                    }
                    else
                    {
                        document.forms[4].submit();
                    }
                }
                else
                {
                    Ext.Msg.show({
                        title: 'Error',
                        msg: 'No tiene permiso para realizar esta acción.',
                        buttons: Ext.Msg.OK,
                        icon: Ext.MessageBox.ERROR
                    });
                }
            },
            listeners:
                {
                    afterrender: function()
                    {
                        Ext.create('Ext.tip.ToolTip',
                            {
                                target: 'exportarVentasJefatura',
                                html: tooltip2,
                                anchor: 'top'
                            });
                    }
                }
        };

    var strRender = Ext.String.format('<br><div style="padding-left:{0}px; font-size:12px">{1}</div><br>', sizeColPadding, '{0}');

    gridVentasJefatura = Ext.create('Ext.grid.Panel',
        {
            id: 'gridVentasJefatura',
            width: sizeResulSuperIzquierdo,
            height: sizeAltoVentasJefatura,
            store: dataStoreVentasJefatura,
            loadMask: true,
            renderTo: 'ReporteVentasJefatura',
            iconCls: 'global_grid',
            cls: 'panelBar1 custom-grid extra-alt',
            title: 'REPORTE DE VENTAS JEFATURA',
            style: 'color:#1496DB',
            collapsible: true,
            collapsed: true,
            columnLines: true,
            features:
                [
                    {
                        ftype: 'summary',
                        dock: 'bottom'
                    }
                ],
            dockedItems:
                [
                    {
                        xtype: 'toolbar',
                        dock: 'top',
                        layout:
                            {
                                pack: 'start',
                                type: 'hbox'
                            },
                        items:
                            [
                                fechaVentasJefatura,
                                {xtype: 'tbspacer'}, cbxCanales,
                                {xtype: 'tbspacer'}, cbxPuntosVenta,
                                {xtype: 'tbspacer'}, cbxSupervisores2,
                                {xtype: 'tbspacer'}, cbxAsesores2,
                                {xtype: 'tbspacer'}, {xtype: 'tbseparator'},
                                {xtype: 'tbspacer'}, btnRefresh,
                                {xtype: 'tbspacer'}, {xtype: 'tbseparator'},
                                {xtype: 'tbspacer'}, btnExportar
                            ]
                    },
                    {
                        xtype: 'toolbar',
                        dock: 'top',
                        layout:
                            {
                                pack: 'start',
                                type: 'hbox'
                            },
                        items:
                            [
                                 listaPtos_cmb,
                                 {xtype: 'tbspacer'},combo_clientes,
                                 {xtype: 'tbspacer'}, 
                                 {
                                    xtype: 'textfield',
                                    id: 'direccion_punto',
                                    name: 'direccion_punto',
                                    fieldLabel: 'Direccion',
                                    labelAlign: 'left',
                                    value: ''
                                 },
                                 combo_planes
                            ]
                    },
                    {
                        xtype: 'toolbar',
                        dock: 'top',
                        layout:
                            {
                                pack: 'start',
                                type: 'hbox'
                            },
                        items:
                            [
                                combo_ptoscobertura,
                                {xtype: 'tbspacer'},combo_sector,
                                {xtype: 'tbspacer'},combo_estadoServicio
                            ]
                    },
                    {
                        xtype: 'toolbar',
                        dock: 'top',
                        layout:
                            {
                                pack: 'start',
                                type: 'hbox'
                            },
                        items:
                            [
                                fechaAprobacion,
                                {xtype: 'tbspacer'},fechaCreacionPunto
                                
                                 
                            ]
                    }
                ],
            viewConfig:
                {
                    enableTextSelection: true,
                    loadingText: '<b>Cargando Ventas de Jefatura, Por favor espere',
                    emptyText: '',
                    deferEmptyText: true
                },
            columns:
                [
                    {
                        id: 'login_',
                        header: 'Login',
                        dataIndex: 'login',
                        style: 'font-weight:bold; padding-left:' + sizeColPadding + 'px',
                        width: sizeLogins,
                        sortable: true,
                        summaryType: 'count',
                        summaryRenderer: function(value)
                        {
                            return Ext.String.format('{0}{1}{2}TOTAL({3})<span>', summSt, paddingL, '">', value);
                        },
                        renderer: function(value)
                        {
                            return Ext.String.format(strRender, value);
                        }
                    },
                    {
                        id: 'servicio',
                        header: 'Servicio',
                        dataIndex: 'servicio',
                        style: 'font-weight:bold; padding-left:' + sizeColPadding + 'px',
                        width: sizeServic,
                        sortable: true,
                        renderer: function(value)
                        {
                            return Ext.String.format(strRender, value);
                        }
                    },
                    {
                        id: 'direccion',
                        header: 'Dirección',
                        dataIndex: 'direccion',
                        style: 'font-weight:bold; padding-left:' + sizeColPadding + 'px',
                        width: sizeDirecc,
                        sortable: true,
                        renderer: function(value)
                        {
                            return Ext.String.format(strRender, value);
                        }
                    },
                    {
                        id: 'nombre_servicio',
                        header: 'Plan',
                        dataIndex: 'nombre_servicio',
                        style: 'font-weight:bold; padding-left:' + (sizeColPadding - 5) + 'px;',
                        width: sizePlands,
                        sortable: true,
                        renderer: function(value)
                        {
                            return Ext.String.format(strRender, value);
                        }
                    },
                    {
                        id: 'jurisdiccion_',
                        header: 'Jurisdicción',
                        dataIndex: 'jurisdiccion_',
                        style: 'font-weight:bold; padding-left:' + (sizeColPadding - 5) + 'px;',
                        width: sizeJurisd,
                        sortable: true,
                        renderer: function(value)
                        {
                            return Ext.String.format(strRender, value);
                        }
                    },
                    {
                        id: 'sector',
                        header: 'Sector',
                        dataIndex: 'sector',
                        style: 'font-weight:bold; padding-left:' + (sizeColPadding - 5) + 'px;',
                        width: sizeSector,
                        sortable: true,
                        renderer: function(value)
                        {
                            return Ext.String.format(strRender, value);
                        }
                    },
                    {
                        id: 'cliente',
                        header: 'Cliente',
                        dataIndex: 'cliente',
                        style: 'font-weight:bold; padding-left:' + (sizeColPadding - 5) + 'px;',
                        width: sizeClient,
                        sortable: true,
                        renderer: function(value)
                        {
                            return Ext.String.format(strRender, value);
                        }
                    },
                    {
                        id: 'empresa',
                        header: 'Empresa',
                        dataIndex: 'empresa',
                        style: 'font-weight:bold; padding-left:' + (sizeColPadding - 5) + 'px;',
                        width: sizeClient,
                        sortable: true,
                        renderer: function(value)
                        {
                            return Ext.String.format(strRender, value);
                        }
                    },
                    {
                        id: 'identificacion',
                        header: 'Identificación',
                        dataIndex: 'identificacion',
                        style: 'font-weight:bold; padding-left:' + (sizeColPadding - 5) + 'px;',
                        width: sizeIdentf,
                        sortable: true,
                        renderer: function(value)
                        {
                            return Ext.String.format(strRender, value);
                        }
                    },
                    {
                        id: 'vendedor',
                        header: 'Asesor',
                        dataIndex: 'vendedor',
                        style: 'font-weight:bold; padding-left:' + (sizeColPadding - 5) + 'px;',
                        width: sizeAsesor,
                        sortable: true,
                        renderer: function(value)
                        {
                            return Ext.String.format(strRender, value);
                        }
                    },
                    {
                        id: 'usuario',
                        header: 'Usuario',
                        dataIndex: 'usuario',
                        style: 'font-weight:bold; padding-left:' + (sizeColPadding - 5) + 'px;',
                        width: sizeUsuari,
                        sortable: true,
                        renderer: function(value)
                        {
                            return Ext.String.format(strRender, value);
                        }
                    },
                    {
                        id: 'supervisor_',
                        header: 'Supervisor',
                        dataIndex: 'supervisor',
                        style: 'font-weight:bold; padding-left:' + (sizeColPadding - 5) + 'px;',
                        width: sizeSuperv,
                        sortable: true,
                        renderer: function(value)
                        {
                            return Ext.String.format(strRender, value);
                        }
                    },
                    {
                        id: 'canal',
                        header: 'Canal',
                        dataIndex: 'canal',
                        style: 'font-weight:bold; padding-left:' + (sizeColPadding - 5) + 'px;',
                        width: sizeCanalV,
                        sortable: true,
                        renderer: function(value)
                        {
                            return Ext.String.format(strRender, value);
                        }
                    },
                    {
                        id: 'punto_venta',
                        header: 'Punto de Venta',
                        dataIndex: 'punto_venta',
                        style: 'font-weight:bold; padding-left:' + (sizeColPadding - 5) + 'px;',
                        width: sizePuntoV,
                        sortable: true,
                        renderer: function(value)
                        {
                            return Ext.String.format(strRender, value);
                        }
                    },
                    {
                        id: 'aprobacion',
                        header: 'Aprobación',
                        dataIndex: 'aprobacion',
                        style: 'font-weight:bold; padding-left:' + (sizeColPadding - 5) + 'px;',
                        width: sizeFechAp,
                        sortable: true,
                        renderer: function(value)
                        {
                            return Ext.String.format(strRender, value);
                        }
                    },
                    {
                        id: 'creacion',
                        header: 'Creación',
                        dataIndex: 'creacion',
                        style: 'font-weight:bold; padding-left:' + (sizeColPadding - 5) + 'px;',
                        width: sizeFechCr,
                        sortable: true,
                        renderer: function(value)
                        {
                            return Ext.String.format(strRender, value);
                        }
                    },
                    {
                        id: 'activacion',
                        header: 'Activación',
                        dataIndex: 'activacion',
                        style: 'font-weight:bold; padding-left:' + (sizeColPadding - 5) + 'px;',
                        width: sizeFechAc,
                        sortable: true,
                        renderer: function(value)
                        {
                            return Ext.String.format(strRender, value);
                        }
                    },
                    {
                        id: 'precio_venta',
                        header: 'Precio Venta',
                        dataIndex: 'precio',
                        style: 'font-weight:bold; padding-right:' + sizeColPadding + 'px;',
                        width: sizePrecio,
                        align: 'right',
                        sortable: true,
                        summaryType: 'sum',
                        summaryRenderer: function(value)
                        {
                            mySigno = Ext.String.format(signo, 'white');
                            return Ext.String.format('{0}{1}{2}{3}{4}<span>', mySigno, summSt, paddingR, '">', value.toFixed(2));
                        },
                        renderer: function(value)
                        {
                            div = '<div style="padding-right:' + sizeColPadding + 'px; font-size:12px">';
                            return '<br>' + Ext.String.format(signo, 'black') + div + value.toFixed(2) + '</div><br>';
                        }
                    }
                ],
            listeners:
                {
                    expand: function()
                    {
                        if (!boolExpandio)
                        {
                            storeCanales.load();
                            storePuntosVenta.load();
                            dataStoreSupervisores2.load();
                        }
                        boolExpandio = true;
                    }
                }
        });
});
