Ext.require
([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);

var itemsPerPage                 = 100;
var intHeightGrid                = 435;
var store                        = '';
var estado_id                    = '';
var area_id                      = '';
var login_id                     = '';
var tipo_asignacion              = '';
var pto_sucursal                 = '';
var cmbImpuestos                 = null;
var grupoDebitoDetSelectionModel = null;
var cmbFormatosDebitar           = null;
var idClienteSucursalSesion;
//parametro para identificar flujo debito planificado
var strFlujoPlanificado;

Ext.onReady(function()
{
    
    dateFechaActivacionDesde = new Ext.form.DateField
    ({
        id: 'idFechaActivacionDesde',
        fieldLabel: 'Fecha Desde',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:360,
        value: '',
        editable: false
    }); 
    
    dateFechaActivacionHasta = new Ext.form.DateField
    ({
        id: 'idFechaActivacionHasta',
        fieldLabel: 'Fecha Hasta',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:360,
        padding: '0 50 0 30',
        value: '',
        editable: false
    });

    
    Ext.Ajax.request
    ({
        url: strUrlGetParametroFlujoPlanificado,
        method: 'get',
        datatype: 'json',
        timeout: 9000000,
        reader: 
            {
                type: 'json',
                root: 'encontrados'
            },
        params:
        { 
            strNombreParametro: "DEBITOS_PLANIFICADOS"
        },
        success: function(response)
        {
                strFlujoPlanificado = response.responseText;
                    /**
                     * COMBO CUENTAS BANCARIAS EMPRESA
                    */
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
                    autoLoad: boolCargaCtaBanc,
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
                    fieldLabel   : '*Cuenta Empresa:',
                    emptyText    : 'Seleccione cuenta bancaria empresa',
                    store        : storeCtasBancariasEmpresa,
                    displayField : 'descripcion_cuenta',
                    valueField   : 'id_cuenta',
                    listeners:
                    {
                    select:{fn:function(combo, value) {
                        $('#respuestadebitoextratype_CuentaId').val(combo.getValue());
                        }}
                    }
                });
            
            
                if (boolCargaCtaBanc===true){
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
                        title: 'Cuentas bancarias',
                        width : 790,
                        renderTo: 'panel_CuentaEmpresa',
                        items: [
                            combo_ctas_bancarias_empresa            
            
                        ],	
                        });
                }
            
                /**
                * FIN COMBO CUENTAS BANCARIAS EMPRESA
                */
            
            
                
                Ext.define('ListaDetalleModel', 
                {
                    extend: 'Ext.data.Model',
                    fields: 
                    [
                        {name:'id',                 type: 'int'},
                        {name:'banco',              type: 'string'},
                        {name:'tipoCuentaTarjeta',  type: 'string'}
                    ]
                }); 
            
            
                store = Ext.create('Ext.data.JsonStore',
                {
                    model: 'ListaDetalleModel',
                    pageSize: itemsPerPage,
                    proxy: 
                    {
                        type: 'ajax',
                        url: strUrlGrid,
                        timeout: 9000000,
                        reader:
                        {
                            type: 'json',
                            root: 'detalles'
                        },
                        extraParams:
                        {
                            fechaDesde:'',
                            fechaHasta:'', 
                            estado:'',
                            nombre:''
                        },
                        simpleSortMode: true
                    }
                });
            
                                store.load({params: {start: 0, limit: 100}});    
            
            
            
                            sm = new Ext.selection.CheckboxModel( {
                                listeners:{
                                    selectionchange: function(selectionModel, selected, options){
                                        arregloSeleccionados= new Array();
            
                                        Ext.each(selected, function(record){
                                        });			
            
                                    }
                                }                            
                            });
            
                            //Modelo Oficina
                            Ext.define('modelOficinas', {
                                extend: 'Ext.data.Model',
                                fields: [
                                    {name: 'intIdOficina', type: 'int'},
                                    {name: 'strNombreOficina', type: 'string'}
                                ]
                            });
                            var oficinaStore = Ext.create('Ext.data.Store', {
                                autoLoad: true,
                                model: "modelOficinas",
                                proxy: {
                                    type: 'ajax',
                                    url : url_oficinas,
                                    timeout: 9000000,
                                    reader: {
                                        type: 'json',
                                        root: 'objDatos'
                                    }
                                },
                                listeners: {
                                    load: function () 
                                    {
                                        this.add(Ext.create('modelOficinas', {
                                            intIdOficina:0,
                                            strNombreOficina:'TODOS'
                                        }));
                                    }
                                }                    
                            });	
                            
                //Modelo Impuestos
                Ext.define('modelImpuestos', 
                {
                    extend: 'Ext.data.Model',
                    fields: 
                    [
                        {name: 'intIdImpuesto',          type: 'int'},
                        {name: 'strDescripcionImpuesto', type: 'string'}
                    ]
                });
                
                var impuestosStore = Ext.create('Ext.data.Store', 
                {
                    autoLoad: true,
                    model: "modelImpuestos",
                    proxy: 
                    {
                        type: 'ajax',
                        url : strUrlGetImpuestos,
                        timeout: 9000000,
                        reader: 
                        {
                            type: 'json',
                            root: 'encontrados'
                        }
                    }                   
                });	
            
                //Combo Impuestos
                cmbImpuestos = new Ext.form.ComboBox
                ({
                    xtype: 'combobox',
                    store: impuestosStore,
                    labelAlign: 'left',
                    name: 'cmbImpuesto',
                    id: 'cmbImpuesto',
                    valueField: 'intIdImpuesto',
                    displayField: 'strDescripcionImpuesto',
                    fieldLabel: 'Impuesto',
                    width: 200,
                    triggerAction: 'all',
                    queryMode: 'local',
                    allowBlank: true,
                });
                                        
                //Combo Oficina
                cbxOficina = new Ext.form.ComboBox
                ({
                    xtype: 'combobox',
                    store: oficinaStore,
                    labelAlign: 'left',
                    name: 'cbxOficina',
                    id: 'idCbxOficina',
                    valueField: 'intIdOficina',
                    displayField: 'strNombreOficina',
                    fieldLabel: 'Oficina',
                    width: 400,
                    triggerAction: 'all',
                    queryMode: 'local',
                    allowBlank: true,
                    renderTo: 'divOficina'
                });
                            
                cbxOficina.setValue(0);  
            
                /**
                 * ComboBox Ciclos de Facturación
                 */
                booleanAplicaCiclosFac = false;
                if (strAplicaCiclosFacturacion === 'S') {
                    booleanAplicaCiclosFac = true;
                    var storeCiclos = new Ext.data.Store({
                        pageSize: 10,
                        total: 'intTotal',
                        proxy: {
                            type: 'ajax',
                            url: strUrlObtieneCiclos,
                            reader: {
                                type: 'json',
                                totalProperty: 'intTotal',
                                root: 'arrayRegistros'
                            }
                        },
                        fields:
                            [
                                {name: 'intIdCiclo', mapping: 'intIdCiclo'},
                                {name: 'strNombreCiclo', mapping: 'strNombreCiclo'}
                            ],
                        autoLoad: true,
                        sorters: {
                            property: 'intIdCiclo',
                            direction: 'ASC'
                        }
                    });
            
                    var cmbCicloFacturacion = Ext.create('Ext.form.ComboBox', {
                        xtype: 'combo',
                        fieldLabel: 'Ciclo Facturacio&#769;n',
                        emptyText: 'Seleccione...',
                        id: 'cmbCicloFacturacionId',
                        name: 'cmbCicloFacturacion',
                        displayField: 'strNombreCiclo',
                        valueField: 'intIdCiclo',
                        labelStyle: 'text-align:left;',
                        multiSelect: false,
                        width: 400,
                        queryMode: 'local',
                        store: storeCiclos,
                        hidden: !booleanAplicaCiclosFac,
                        renderTo: 'divCiclos',
                        listeners: {
                            select: function (combo, records) {
                                $("#intCicloId").val(combo.lastValue);
                            },
                        }
                    });
                }
            
                
                /**
                 * Función que obtiene los Tipos de Escenarios para la generación de los débitos.
                 */
                $('#strEscenarioDebito').val("");
                $('#strFiltroEscenario').val("");
                var storeEscenarios = new Ext.data.Store({
                    pageSize: 10,
                    total: 'intTotal',
                    proxy: {
                        type: 'ajax',
                        url: strUrlObtieneEscenarios,
                        reader: {
                            type: 'json',
                            totalProperty: 'intTotal',
                            root: 'arrayRegistros'
                        }
                    },
                    fields:
                        [
                            {name: 'intIdEscenario', mapping: 'intIdEscenario'},
                            {name: 'strNombreEscenario', mapping: 'strNombreEscenario'},
                            {name: 'strValorEscenario', mapping: 'strValorEscenario'}
                        ],
                    autoLoad: true,
                    sorters: {
                        property: 'intIdCiclo',
                        direction: 'ASC'
                    }
                });
            
                Ext.create('Ext.form.ComboBox',
                {
                    xtype:        'combo',
                    fieldLabel:   'Escenarios',
                    emptyText:    'Seleccione...',
                    id:           'cmbEscenariosDeb',
                    name:         'cmbEscenariosDeb',
                    displayField: 'strNombreEscenario',
                    valueField:   'strValorEscenario',
                    labelStyle:   'text-align:left;',
                    multiSelect:  false,
                    width:        400,
                    queryMode:    'local',
                    editable:     false,
                    store:        storeEscenarios,
                    renderTo:     'divEscenarios',
                    listeners: {
                        'change': function(data) {
                            $('#divFiltroESCENARIO_1').hide();
                            $('#divFiltroESCENARIO_2').hide();
                            $('#divFiltroESCENARIO_3').hide();
                            $('#divFiltro'+data.getValue()).show();   
                            
                            $('#strEscenarioDebito').val("");
                            $('#strFiltroEscenario').val("");
                            
                            $('#cmbCuotaEscenario3Deb-inputEl').val("");
                            $('#cmbMontoEscenario2Deb-inputEl').val("");
                            $('#fechaEscenario1-inputEl').val("");
                            fechaFiltroEscenario1.setValue("");
                            
                            $('#strEscenarioDebito').val(data.getValue());
                                            
                            if(data.getValue()=='ESCENARIO_BASE')
                            {
                                $('#strFiltroEscenario').val("SIN_FILTRO");
                            }                        
                        }
                    }
                });
                
                
                /**
                 * Función que obtiene las cuotas NDI del Escenario3(Generación de Debitos con Filtro de Cuotas NDI), 
                 * para la generación de los débitos.
                 */
                $('#divFiltroESCENARIO_3').hide();
                var storeCuotasEscenario3 = new Ext.data.Store({
                    pageSize: 10,
                    total: 'intTotal',
                    proxy: {
                        type: 'ajax',
                        url: strUrlObtieneCuotasEsc3,
                        reader: {
                            type: 'json',
                            totalProperty: 'intTotal',
                            root: 'arrayRegistros'
                        }
                    },
                    fields:
                        [
                            {name: 'intIdCuota', mapping: 'intIdCuota'},
                            {name: 'strValorCuota', mapping: 'strValorCuota'}
                        ],
                    autoLoad: true,
                    sorters: {
                        property: 'intIdCiclo',
                        direction: 'ASC'
                    }
                }); 
            
                Ext.create('Ext.form.ComboBox',
                {
                    xtype:        'combo',
                    fieldLabel:   'Cantidad de Cuotas por Cliente',
                    emptyText:    'Seleccione...',
                    id:           'cmbCuotaEscenario3Deb',
                    name:         'cmbCuotaEscenario3Deb',
                    displayField: 'strValorCuota',
                    valueField:   'strValorCuota',
                    labelStyle:   'text-align:left;',
                    multiSelect:  false,
                    width:        400,
                    queryMode:    'local',
                    editable:     false,
                    store:        storeCuotasEscenario3,
                    renderTo:    'divFiltroESCENARIO_3',
                    listeners: {
                        'change': function(data) {
                            $('#strFiltroEscenario').val(data.getValue()); 
                        }
                    }
                });
                
                /**
                 * Función que obtiene los montos del Escenario2(Generación de Debitos con Filtro de Montos), 
                 * para la generación de los débitos.
                 */
                $('#divFiltroESCENARIO_2').hide();
                var storeMontosEscenario2 = new Ext.data.Store({
                    pageSize: 10,
                    total: 'intTotal',
                    proxy: {
                        type: 'ajax',
                        url: strUrlObtieneMontosEsc2,
                        reader: {
                            type: 'json',
                            totalProperty: 'intTotal',
                            root: 'arrayRegistros'
                        }
                    },
                    fields:
                        [
                            {name: 'intIdMonto', mapping: 'intIdMonto'},
                            {name: 'strValorMonto', mapping: 'strValorMonto'}
                        ],
                    autoLoad: true,
                    sorters: {
                        property: 'intIdCiclo',
                        direction: 'ASC'
                    }
                });
            
                Ext.create('Ext.form.ComboBox',
                {
                    xtype:        'combo',
                    fieldLabel:   'Monto $',
                    emptyText:    'Seleccione...',
                    id:           'cmbMontoEscenario2Deb',
                    name:         'cmbMontoEscenario2Deb',
                    displayField: 'strValorMonto',
                    valueField:   'strValorMonto',
                    labelStyle:   'text-align:left;',
                    multiSelect:  false,
                    width:        400,
                    queryMode:    'local',
                    editable:     false,
                    store:        storeMontosEscenario2,
                    renderTo:    'divFiltroESCENARIO_2',
                    listeners: {
                        'change': function(data) {
                            $('#strFiltroEscenario').val(data.getValue()); 
                        }
                    }
                });
            
                /**
                 * Función que presenta el filtro de fecha del Escenario1(Generación de Debitos con Filtro de Fecha),
                 * para la generación de los débitos.
                 */
                $('#divFiltroESCENARIO_1').hide();
                Ext.define('Ext.form.field.Month', {
                    extend: 'Ext.form.field.Date',
                    alias: 'widget.monthfield',
                    requires: ['Ext.picker.Month'],
                    alternateClassName: ['Ext.form.MonthField', 'Ext.form.Month'],
                    selectMonth:new Date(),
                    createPicker: function() {
                        var me = this;
                        return Ext.create('Ext.picker.Month', {
                            pickerField: me,
                            ownerCt: me.ownerCt,
                            renderTo: document.body,
                            floating: true,
                            hidden: true,
                            focusOnShow: true,             
                            width:250,
                            listeners: {
                                select: {
                                    scope: me,
                                    fn: me.onSelect
                                },
                                monthdblclick: {
                                    scope: me,
                                    fn: me.onOKClick
                                },
                                yeardblclick: {
                                    scope: me,
                                    fn: me.onOKClick
                                },
                                OkClick: {
                                    scope: me,
                                    fn: me.onOKClick
                                },
                                CancelClick: {
                                    scope: me,
                                    fn: me.onCancelClick
                                }
                            },
                            keyNavConfig: {
                                esc: function() {
                                    me.collapse();
                                }
                            }
                        });
                    },
                    onCancelClick: function() {
                        var me = this;
                        me.selectMonth = null;
                        me.collapse();
                    },
                    onOKClick: function() {
                        var me = this;    
                        if(me.selectMonth == "" || me.selectMonth == null) {
                            me.setValue(new Date);
                            me.fireEvent('select', me, me.selectMonth);
                        }else{
                            me.setValue(me.selectMonth);
                            me.fireEvent('select', me, me.selectMonth);
                        }
                        $('#strFiltroEscenario').val($('#fechaEscenario1-inputEl').val()); 
                        me.selectMonth = null;
                        me.collapse();
                    },
                    onSelect: function(m, d) {
                        var me = this;
                        me.selectMonth = new Date((d[0] + 1) + '/1/' + d[1]);
                    }
                });
                
                var fechaFiltroEscenario1 = Ext.create('Ext.form.field.Month', {
                    id: 'fechaEscenario1',
                    fieldLabel: 'Fecha Filtro',
                    labelAlign: 'left',
                    xtype: 'datefield',
                    format: 'm/Y',
                    width: 400,
                    editable: false,
                    renderTo: 'divFiltroESCENARIO_1'
                });
                
                
                Ext.define("Ext.locale.es.picker.Month", {
                    override: "Ext.picker.Month"
                });
                
                if (Ext.Date) {
                    Ext.Date.monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio",
                                        "Agosto","Septiembre", "Octubre", "Noviembre", "Diciembre"];
            
                    Ext.Date.getShortMonthName = function (month) {
                        return Ext.Date.monthNames[month].substring(0, 3);
                    };
                }
                
                var listView = Ext.create('Ext.grid.Panel', 
                {
                    width:800,
                    height: intHeightGrid,
                    collapsible:false,
                    title: '',
                    id:'panelDebitos',
                    selModel: sm,
                    bbar: Ext.create('Ext.PagingToolbar', 
                    {
                        store: store,
                        displayInfo: true,
                        displayMsg: 'Mostrando clientes {0} - {1} of {2}',
                        emptyMsg: "No hay datos para mostrar"
                    }),	
                    store: store,
                    multiSelect: false,
                    viewConfig: 
                    {
                        emptyText: 'No hay datos para mostrar'
                    },
                    columns: 
                    [
                        new Ext.grid.RowNumberer(),  
                        {
                            text: 'Banco',
                            width: 250,
                            dataIndex: 'banco'
                        },
                        {
                            text: 'Contenido',
                            width: 485,
                            dataIndex: 'tipoCuentaTarjeta'
                        }                        
                    ],
                    listeners:
                    {
                        viewready: function(grid)
                        {
                            var view = grid.view;
            
                            grid.mon(view,
                            {
                                uievent: function(type, view, cell, recordIndex, cellIndex, e)
                                {
                                    grid.cellIndex = cellIndex;
                                    grid.recordIndex = recordIndex;
                                }
                            });
            
                            grid.tip = Ext.create('Ext.tip.ToolTip',
                            {
                                target: view.el,
                                delegate: '.x-grid-cell',
                                trackMouse: true,
                                autoHide: false,
                                renderTo: Ext.getBody(),
                                listeners:
                                {
                                    beforeshow: function(tip)
                                    {
                                        if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1)
                                        {
                                            header = grid.headerCt.getGridColumns()[grid.cellIndex];
            
                                            if (header.dataIndex != null)
                                            {
                                                var trigger         = tip.triggerElement,
                                                    parent          = tip.triggerElement.parentElement,
                                                    columnDataIndex = view.getHeaderByCell(trigger).dataIndex;
            
                                                if (view.getRecord(parent).get(columnDataIndex) != null)
                                                {
                                                    var columnText = view.getRecord(parent).get(columnDataIndex).toString();
            
                                                    if (columnText)
                                                    {
                                                        tip.update(columnText);
                                                    }
                                                    else
                                                    {
                                                        return false;
                                                    }
                                                }
                                                else
                                                {
                                                    return false;
                                                }
                                            }
                                            else
                                            {
                                                return false;
                                            }
                                        }
                                    }
                                }
                            });
            
                            grid.tip.on('show', function()
                            {
                                var timeout;
            
                                grid.tip.getEl().on('mouseout', function()
                                {
                                    timeout = window.setTimeout(function()
                                    {
                                        grid.tip.hide();
                                    }, 500);
                                });
            
                                grid.tip.getEl().on('mouseover', function()
                                {
                                    window.clearTimeout(timeout);
                                });
            
                                Ext.get(view.el).on('mouseover', function()
                                {
                                    window.clearTimeout(timeout);
                                });
            
                                Ext.get(view.el).on('mouseout', function()
                                {
                                    timeout = window.setTimeout(function()
                                    {
                                        grid.tip.hide();
                                    }, 500);
                                });
                            });
                        }
                    }   
                });
                //se agrega panel de planificado que incluye el check Generacion Planificada y Fecha Planificada
                $('#strDatePlanificado').val("");
                var panel = Ext.create('Ext.form.Panel', {
                    width: 500,
                    border:0,
                    fieldDefaults: {
                        labelAlign: 'left',
                        labelWidth: 90,
                        anchor: '100%',
            
                    columnWidth:0.5
                    ,layout:"anchor"
                    ,border:0
                    },
                    layout: {
                        type: 'table',
                        columns: 2,
                        align: 'left',
                        tableAttrs: {
                            style: {
                                width: '100%',
                                height: '100%'
                            }
                        },
                        tdAttrs: {
                            align: 'center',
                            valign: 'middle'
                        }
                    },
                    items: [{
                    
                        xtype: 'checkbox',
                        fieldLabel : 'Generación Planificada',
                        id : 'checkPlanificado',
                        name : 'checkPlanificado',
                        width: 500,
                        checked: false,
                        hidden: false,
                        listeners:{
                            afterRender: function() {
                                    
                                    if (getParametroDebitoPlanificado(strFlujoPlanificado,'FlujoDebitoPlanificado') == 'NO')
                                        this.hide();
                                },
                            change :function (checkPlanificado) {
                                var fecha = Ext.getCmp('datePlanificado');
                                var grid1 = Ext.getCmp('panelDebitos');
                                var grid2 = Ext.getCmp('panelTarjetas');
                                if (checkPlanificado.value) {
                                    checkPlanificado.setWidth(200);
                                    grid1.getSelectionModel().selectAll();
                                    grid2.getSelectionModel().selectAll();
                                    fecha.show();

                                } else {
                                    grid1.getSelectionModel().deselectAll();
                                    grid2.getSelectionModel().deselectAll();
                                    fecha.setValue();
                                    fecha.hide();
                                    checkPlanificado.setWidth(500);
                                    
                                }
            
                                $('#strCheckPlanificado').val(checkPlanificado.getValue());
                            }
                        }
            
                    }, {
                        xtype: 'datefield',
                        id:'datePlanificado',
                        name: 'datePlanificado',
                        width: 300,
                        fieldLabel: 'Fecha Planificada',
                        listeners: {
                            afterRender: function() {
                                var checkPlanificado = Ext.getCmp('checkPlanificado');
                                if (!checkPlanificado.value)
                                    this.hide();
                            },
                            change:function (datePlanificado) {
                                $('#strDatePlanificado').val(datePlanificado.getValue());
                            }
                        }
                    }]
                });
                panel.render('divPlanificado');
            
                //Flujo de generación débito para opciones nuevas
                if (strFlujoGeneracionDebito === 'SI') 
                { 
                    //Opción Subir archivo excel de clientes a excluir
                    $('#subirArchivo').val("");
                    $("#divArchivoClientes").append('<label >Clientes a Excluir:</label>');
                    $("#divArchivoClientes").append('<input id="subirArchivo" name="subirArchivo" type="file" style="margin-left: 10px" />');
                    $("#divArchivoClientes").append('<button id="btnLimpiar" style="margin-left: 50px"> Limpiar archivo </button>');
                    
                    $("#btnLimpiar").click(function () {
                        $('#subirArchivo').val("");
                    });
                    
                    //Opción estados de servicios
                    var storeEstadosServicios = new Ext.data.Store({
                        pageSize: 10,
                        total: 'intTotal',
                        proxy: {
                            type: 'ajax',
                            url: strUrlEstadosServicio,
                            extraParams: {'strDescParam': 'ESTADOS_SERVICIOS_DEBITOS'},
                            reader: {
                                type: 'json',
                                totalProperty: 'intTotal',
                                root: 'arrayRegistros'
                            }
                        },
                        fields:
                            [
                                {name: 'intId', mapping: 'intId'},
                                {name: 'strValor', mapping: 'strValor'} 
                            ],
                        autoLoad: true,
                        sorters: {
                            property: 'intId',
                            direction: 'ASC'
                        }
                    });
                    
                    Ext.create('Ext.form.ComboBox', {
                        xtype: 'combo',
                        fieldLabel: 'Estados OS:',
                        emptyText: 'Seleccione...',
                        id: 'cmbEstadosServicioId',
                        name: 'cmbEstadosServicio',
                        displayField: 'strValor',
                        valueField: 'strValor',
                        labelStyle: 'text-align:left;',
                        multiSelect: true,
                        editable: false,
                        width: 400,
                        queryMode: 'local',
                        store: storeEstadosServicios,
                        renderTo: 'divEstadosServicio',
                        listeners: {
                            change: function (combo, records) {
                                $("#strEstadosServicio").val(combo.lastValue);
                                
                                //Se valida para eliminar los demás valores y setear valor "Todos" cuando éste sea seleccionado 
                                if( $("#strEstadosServicio").val() !== "" && ( $("#strEstadosServicio").val().indexOf("Todos") ) >=0 )
                                {
                                    combo.setValue("Todos");
                                    $("#strEstadosServicio").val(combo.lastValue);
                                }
                            },
                        }
                    });
                    
                    //Opción FechaS de activación
                    $('#strFechaActDesde').val("");
                    $('#strFechaActHasta').val("");
                    Ext.create('Ext.form.Panel', {
                        width: 1000,
                        border:0,
                        fieldDefaults: {
                            labelAlign: 'left',
                            labelWidth: 100,
                            anchor: '100%',
                            columnWidth:0.5,
                            layout:"anchor",
                            border:0
                        },
                        layout: {
                            type: 'table',
                            columns: 3,
                            align: 'left',
                            tableAttrs: {
                                style: {
                                    width: '70%',
                                    height: '100%'
                                }
                            },
                            tdAttrs: {
                                valign: 'middle',
                            } 
                        },
                        items: [ 
                            {
                                xtype: 'label',
                                forId: 'fieldId',
                                text: 'Fechas Activación:',
                                padding: '0 40 0 0',
                                width: 300,
                            }, 
                            dateFechaActivacionDesde,
                            dateFechaActivacionHasta
                            ],
                        renderTo: 'divFechasActivacion'
                    });
                    
                    //Opción Motivos de rechazo
                    var storeMotivosRechazos = new Ext.data.Store({
                        pageSize: 10,
                        total: 'intTotal',
                        proxy: {
                            type: 'ajax',
                            url: strUrlMotivosRechazos,
                            extraParams: {'strDescParam': 'MOTIVO_RECHAZO_HOMOLOGADO'},
                            reader: {
                                type: 'json',
                                totalProperty: 'intTotal',
                                root: 'arrayRegistros'
                            }
                        },
                        fields:
                            [
                                {name: 'intId', mapping: 'intId'},
                                {name: 'strValor', mapping: 'strValor'} 
                            ],
                        autoLoad: true,
                        sorters: {
                            property: 'intId',
                            direction: 'ASC'
                        }
                    });
                    
                    Ext.create('Ext.form.ComboBox', {
                        xtype: 'comboMotivos',
                        fieldLabel: 'Excluir Motivos Rechazos:',
                        emptyText: 'Seleccione...',
                        id: 'cmbMotivosRechazosId',
                        name: 'cmbMotivosRechazos',
                        displayField: 'strValor',
                        valueField: 'strValor',
                        labelStyle: 'text-align:left;',
                        multiSelect: true,
                        editable: false,
                        width: 400,
                        queryMode: 'local',
                        store: storeMotivosRechazos,
                        renderTo: 'divMotivosRechazo',
                        listeners: {
                            change: function (comboMotivos, records) {
                                $("#strMotivosRechazo").val(comboMotivos.lastValue);
                            },
                        }
                    });
 
                } //fin strFlujoGeneracionDebito
                
                /**
                 * SE CREA VARIABLE QUE SE RECUPERA PARA FUNCIONALIDAD DE COMBO FORMATOS
                 * @author Ivan Romero <icromero@telconet.ec>
                 * @version 1.1 22-03-2021 - intIdGrupoCab sirve para recuperar los valores de grupo detalles
                 * @since 1.0
                 */
                Ext.define('modelInformacionCombosDebitar', 
                {
                    extend: 'Ext.data.Model',
                    fields: 
                    [
                        {name: 'intId',          type: 'integer'},
                        {name: 'intIdGrupoCab',          type: 'integer'},
                        {name: 'strDescripcion', type: 'string'}
                    ]
                });
            
                
                /**
                 * COMBO TARJETAS A DEBITAR
                 */
                var tarjetasDebitarStore = Ext.create('Ext.data.Store', 
                {
                    autoLoad: true,
                    model: "modelInformacionCombosDebitar",
                    proxy: 
                    {
                        type: 'ajax',
                        url : strUrlGetInformacionCombosDebitos,
                        timeout: 9000000,
                        reader: 
                        {
                            type: 'json',
                            root: 'encontrados'
                        },
                        extraParams:
                        {
                            strNombreParametro: "TARJETAS_DEBITO"
                        }
                    }                   
                });	
                /**
                 * SETEO EL PRIMER REGISTRO DEL STORE DE FORMATOS A DEBITAR
                 * @author Ivan Romero <icromero@telconet.ec>
                 * @version 1.1 22-03-2021 - se quita el seteo de del grupo detalles, ya que se hara en formatosDevitarStore.load
                 * @since 1.0
                 */
                var cmbTarjetasDebitar = new Ext.form.ComboBox
                ({
                    xtype: 'combobox',
                    store: tarjetasDebitarStore,
                    labelAlign: 'left',
                    name: 'cmbTarjetaDebitar',
                    id: 'cmbTarjetaDebitar',
                    valueField: 'intId',
                    displayField: 'strDescripcion',
                    fieldLabel: 'Tarjetas',
                    width: 150,
                    triggerAction: 'all',
                    queryMode: 'local',
                    allowBlank: false,
                    required: true,
                    labelWidth: '50px',
                    editable: false,
                    listeners:
                    {
                        select: function(combo)
                        {
                            formatosDebitarStore.loadData([],false);
                            formatosDebitarStore.getProxy().extraParams.intValor = combo.getValue();
                            formatosDebitarStore.load();
                        }
                    }
                });
                /**
                 * FIN COMBO TARJETAS A DEBITAR
                 */
                
                
                /**
                 * COMBO FORMATOS CON EL CUAL DEBITAR
                 */
                var formatosDebitarStore = Ext.create('Ext.data.Store', 
                {
                    model: "modelInformacionCombosDebitar",
                    proxy: 
                    {
                        type: 'ajax',
                        url : strUrlGetInformacionCombosDebitos,
                        timeout: 9000000,
                        reader: 
                        {
                            type: 'json',
                            root: 'encontrados'
                        },
                        extraParams:
                        {
                            strNombreParametro: "FORMATOS_DEBITOS"
                        }
                    }                   
                });	
            
                /**
                 * SETEO EL PRIMER REGISTRO DEL STORE DE FORMATOS A DEBITAR
                 * @author Ivan Romero <icromero@telconet.ec>
                 * @version 1.1 22-03-2021 - se actualiza Seteo del grupo detalles para  se cargue despues de seleccionar el valor de combo formatos con el campo intIdGrupoCab
                 * @since 1.0
                 */
                cmbFormatosDebitar = new Ext.form.ComboBox
                ({
                    xtype: 'combobox',
                    store: formatosDebitarStore,
                    labelAlign: 'left',
                    name: 'cmbFormatoDebitar',
                    id: 'cmbFormatoDebitar',
                    valueField: 'intId',
                    displayField: 'strDescripcion',
                    fieldLabel: 'Formatos',
                    width: 220,
                    triggerAction: 'all',
                    queryMode: 'local',
                    allowBlank: false,
                    required: true,
                    labelWidth: '60px',
                    editable: false,
                    listeners:
                    {
                        select: function(combo)
                        {
                            storeGrupoDebitosDet.loadData([],false);
                            storeGrupoDebitosDet.getProxy().extraParams.intIdGrupoCab = combo.valueModels[0].data.intIdGrupoCab ;
                            storeGrupoDebitosDet.load();
                            
                        }
                    }
                });
                /**
                 * FIN COMBO FORMATOS CON EL CUAL DEBITAR
                 */
                
                
            
                Ext.define('grupoDebitosDetModel', 
                {
                    extend: 'Ext.data.Model',
                    fields: 
                    [
                        {name:'intIdGrupoCab',  type: 'int'},
                        {name:'intIdGrupoDet',  type: 'int'},
                        {name:'strDescripcion', type: 'string'}
                    ]
                });
                
                grupoDebitoDetSelectionModel = new Ext.selection.CheckboxModel();
                
                var storeGrupoDebitosDet = Ext.create('Ext.data.JsonStore',
                {
                    model: 'grupoDebitosDetModel',
                    pageSize: itemsPerPage,
                    proxy: 
                    {
                        type: 'ajax',
                        url: strUrlGetGrupoDebitosDet,
                        reader:
                        {
                            type: 'json',
                            root: 'encontrados'
                        },
                        simpleSortMode: true
                    }
                });
                
                
                var gridPanelTarjetas = Ext.create('Ext.grid.Panel', 
                {
                    width:800,
                    height: intHeightGrid,
                    collapsible:false,
                    title: '',
                    id:'panelTarjetas',
                    selModel: grupoDebitoDetSelectionModel,
                    dockedItems: 
                    [{
                        xtype: 'toolbar',
                        dock: 'top',
                        align: '->',
                        items:
                        [ 
                            cmbTarjetasDebitar, 
                            {html: "&nbsp;", border: false, width: 50},
                            cmbFormatosDebitar
                        ]
                    }],
                    bbar: Ext.create('Ext.PagingToolbar', 
                    {
                        store: storeGrupoDebitosDet,
                        displayInfo: true,
                        displayMsg: 'Mostrando tarjetas {0} - {1} of {2}',
                        emptyMsg: "No hay datos para mostrar"
                    }),	
                    store: storeGrupoDebitosDet,
                    multiSelect: false,
                    viewConfig: 
                    {
                        emptyText: 'No hay datos para mostrar'
                    },
                    columns: 
                    [
                        new Ext.grid.RowNumberer(),
                        {
                        id: 'intIdGrupoCab',
                        header: 'intIdGrupoCab',
                        dataIndex: 'intIdGrupoCab',
                        hidden: true,
                        hideable: false
                        },
                        {
                        id: 'intIdGrupoDet',
                        header: 'intIdGrupoDet',
                        dataIndex: 'intIdGrupoDet',
                        hidden: true,
                        hideable: false
                        },
                        {
                            text: 'Tarjeta',
                            width: 735,
                            dataIndex: 'strDescripcion'
                        }                       
                    ],
                    listeners:
                    {
                        viewready: function(grid)
                        {
                            var view = grid.view;
            
                            grid.mon(view,
                            {
                                uievent: function(type, view, cell, recordIndex, cellIndex, e)
                                {
                                    grid.cellIndex = cellIndex;
                                    grid.recordIndex = recordIndex;
                                }
                            });
            
                            grid.tip = Ext.create('Ext.tip.ToolTip',
                            {
                                target: view.el,
                                delegate: '.x-grid-cell',
                                trackMouse: true,
                                autoHide: false,
                                renderTo: Ext.getBody(),
                                listeners:
                                {
                                    beforeshow: function(tip)
                                    {
                                        if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1)
                                        {
                                            header = grid.headerCt.getGridColumns()[grid.cellIndex];
            
                                            if (header.dataIndex != null)
                                            {
                                                var trigger         = tip.triggerElement,
                                                    parent          = tip.triggerElement.parentElement,
                                                    columnDataIndex = view.getHeaderByCell(trigger).dataIndex;
            
                                                if (view.getRecord(parent).get(columnDataIndex) != null)
                                                {
                                                    var columnText = view.getRecord(parent).get(columnDataIndex).toString();
            
                                                    if (columnText)
                                                    {
                                                        tip.update(columnText);
                                                    }
                                                    else
                                                    {
                                                        return false;
                                                    }
                                                }
                                                else
                                                {
                                                    return false;
                                                }
                                            }
                                            else
                                            {
                                                return false;
                                            }
                                        }
                                    }
                                }
                            });
            
                            grid.tip.on('show', function()
                            {
                                var timeout;
            
                                grid.tip.getEl().on('mouseout', function()
                                {
                                    timeout = window.setTimeout(function()
                                    {
                                        grid.tip.hide();
                                    }, 500);
                                });
            
                                grid.tip.getEl().on('mouseover', function()
                                {
                                    window.clearTimeout(timeout);
                                });
            
                                Ext.get(view.el).on('mouseover', function()
                                {
                                    window.clearTimeout(timeout);
                                });
            
                                Ext.get(view.el).on('mouseout', function()
                                {
                                    timeout = window.setTimeout(function()
                                    {
                                        grid.tip.hide();
                                    }, 500);
                                });
                            });
                        }
                    }   
                });
                            
                            
                var tabsFiltros = Ext.create('Ext.tab.Panel',
                {
                    id: 'tab_panel',
                    width: 800,
                    columns: 3,
                    autoScroll: true,
                    activeTab: 0,
                    colspan: 5,
                    defaults: {autoHeight: true},
                    plain: true,
                    deferredRender: false,
                    hideMode: 'offsets',
                    frame: false,
                    buttonAlign: 'center',
                    items:
                    [
                        {
                            contentEl: 'fieldsTabDebitosGenerales',
                            title: 'Debitos Generales',
                            id: 'idTabDebitoGeneral',
                            layout:
                            {
                                type: 'table',
                                columns: 3,
                                align: 'left'
                            },
                            items:[ listView ],
                            closable: false,
                            listeners: 
                            {
                                activate: function(selModel, Cmp)
                                {
                                    strTabActivo = "debitosNormales";
                                }
                            }
                        },
                        {
                            contentEl: 'fieldsTabDebitosVisaMastercard',
                            title: 'Visa/MasterCard',
                            id: 'idTabVisaMastercard',
                            hidden: boolHiddenTab,
                            layout:
                            {
                                type: 'table',
                                columns: 3,
                                align: 'left'
                            },
                            items:[ gridPanelTarjetas ],
                            closable: false,
                            listeners: 
                            {
                                activate: function(selModel, Cmp)
                                {
                                    strTabActivo = "debitosEspeciales";
                                }
                            }
                        }
                    ]
                });
            
                var objFilterPanel = Ext.create('Ext.form.Panel',
                {
                    bodyPadding: 7,
                    border: false,
                    bodyStyle:
                    {
                        background: '#fff'
                    },
                    collapsible: false,
                    collapsed: false,
                    title: '',
                    width: 900,
                    items:[tabsFiltros],
                    renderTo: 'lista'
                });
                
                
                
            
                /**
                 * SETEO EL PRIMER REGISTRO DEL STORE DE TARJETAS A DEBITAR Y CARGO EL STORE CON LOS DETALLES DEL GRUPO DE ESE DEBITO SETEADO
                 * @author Ivan Romero <icromero@telconet.ec>
                 * @version 1.1 17-03-2021 - se actualiza Seteo del grupo detalles para que no se cargue con la accion de seleccionar combo tarjetas
                 * @since 1.0
                 */
                tarjetasDebitarStore.on('load',function(store)
                {
                    cmbTarjetasDebitar.setValue(store.getAt(0).get('intId'));
            
                    formatosDebitarStore.loadData([],false);
                    formatosDebitarStore.getProxy().extraParams.intValor = cmbTarjetasDebitar.getValue();
                    formatosDebitarStore.load();
                });
                
                
                
                /**
                 * SETEO EL PRIMER REGISTRO DEL STORE DE FORMATOS A DEBITAR
                 * @author Ivan Romero <icromero@telconet.ec>
                 * @version 1.1 22-03-2021 - se actualiza Seteo del grupo detalles para  se cargue despues de seleccionar el valor de combo formatos
                 * @since 1.0
                 */
                formatosDebitarStore.on('load',function(store)
                {
                    cmbFormatosDebitar.setValue(store.getAt(0).get('intId'));
                    storeGrupoDebitosDet.loadData([],false);
                    storeGrupoDebitosDet.getProxy().extraParams.intIdGrupoCab = store.getAt(0).get('intIdGrupoCab');
                    storeGrupoDebitosDet.load();
                });
        },
        failure: function(result)
        {
            Ext.Msg.alert('Error',result.responseText); 
        }
    });
    
});


jQuery(document).ready(function ()
{
    jQuery('#agregar_archivo').click(function ()
    {
        intArchivos++;
        var strNuevoElemento = "<td><input type='file' required='required' id='respuestadebitoextratype_" + 
            strIdDebito + "_" +intArchivos + "' " +
            "name='respuestadebitoextratype[" + strIdDebito  + "][" + intArchivos + "]' size='40'></td>"
        + "<td><button class='button-grid-eliminar' id='gridrespuestadebitoextratype_" + 
            strIdDebito + "_" +intArchivos + "' onclick='eliminarElemento(\"respuestadebitoextratype_" + 
            strIdDebito + "_" +intArchivos + "\");'></td>";
        var newLi = jQuery('<li></li>').html(strNuevoElemento);
        newLi.appendTo(jQuery('#listaArchivos'));
        return false;
    });
});

function eliminarElemento(strId)
{
    var objElement = document.getElementById(strId);
    objElement.parentNode.removeChild(objElement);
    var objElement = document.getElementById("grid" + strId);
    objElement.parentNode.removeChild(objElement);
    return false;
}

//se obtiene el mensaje parametrizados para las validaciones
function getMensajeDebito(strMensajesDebitos,strDescripcion)
{       var json = Ext.JSON.decode(strMensajesDebitos);
        var mensaje ='null';
        json.encontrados.forEach(element => {
            if(element.strDescripcion === strDescripcion){
                mensaje = element.strMensaje;
            }
        });
        return mensaje;
}
//se obtiene el mensaje parametrizados para las validaciones
function getParametroDebitoPlanificado(strMensajesDebitos,strDescripcion)
{       var json = Ext.JSON.decode(strMensajesDebitos);
        var mensaje ='null';
        json.encontrados.forEach(element => {
            if(element.strDescripcion === strDescripcion){
                mensaje = element.strValor;
            }
        });
        return mensaje;
}



/**
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.1 28-11-2017 - Se agrega la validación del ciclo de facturación si se tiene el parámetro habilitado.
 * @since 1.0
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.2 06-05-2021 - Se agrega logica para la generacion de debitos planificados
 * 
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @version 1.3 06-09-2022 - Se agrega lógica para el flujo de opciones en la generacion de débitos. Las opciones se presentan
 *                           si el flujo cumple con la variable strFlujoGeneracionDebito por la empresa.
 *                           Opciones: subir arhcivo excel clientes, estados de servicio, fechas activación, motivos de rechazo.
 *                           Para las opciones de subir archivo, fecha de activación se realizan validaciones y mostrará mensaje
 *                           de alerta en caso de cumplir con alguna reestricción.
 *                           
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @version 1.4 06-01-2023 - Se corrige la forma de obtener y enviar el valor de las fechas de activación.                      
 */
function procesar()
{
    var param               = '';
    var oficina             = cbxOficina.getValue();
    var boolContinuar       = true;
    var selectionModel      = null;
    var intIdGrupoDebitoCab = 0;
    var intIdFormato        = 0;
    var escenarioDebito     = $('#strEscenarioDebito').val();
    var filtroEscenario     = $('#strFiltroEscenario').val();
    var boolPlanificado     = (Ext.isEmpty($('#strCheckPlanificado').val())?false:$('#strCheckPlanificado').val());
    var datePlanificado     = new Date($('#strDatePlanificado').val());
    var dateActual = new Date();
    var mensajesDebitos = null;
    
    //Flujo Opciones: archivo excel clientes,  estados Servicios, Fecha activación, motivos rechazo, 
    /* Variable booleana 'boolValidaOpcion' encargada permitir o no el flujo de generar, para los casos de haber 
       seleccionado opción archivo excel, fechas de activación por el cumplimiento de las validaciones. */
    var boolValidaOpcion   = false;
    var strMsjOpciones     = "";
    var strMsjValidaOpcion = "";
    
    if (strFlujoGeneracionDebito === 'SI') 
    { 
        var strMsjEstadosServicio = "";
        var strMsjFeActivacion    = "";
        var strMsjMotivoRechazo   = "";
        var strMsjArchivoClientes = "";
        var fechaActDesdeTemp     = Ext.getCmp('idFechaActivacionDesde').value;
        var fechaActHastaTemp     = Ext.getCmp('idFechaActivacionHasta').value;
        
        //**Opción Subir archivo excel de clientes a excluir**
        if( !boolValidaOpcion &&  $("#subirArchivo").val() )
        {
            var file             = $("#subirArchivo")[0].files[0];
            var strNombreArchivo = file.name;
            var strExtension     = strNombreArchivo.split(".").pop();

            if(strExtension.toUpperCase() === 'XLS' || strExtension.toUpperCase() === 'XLSX')
            {
                //Se llama a función para convertir el archivo a base64
                conversionArchivoBase64(file);
                $("#strNombreArchivoCl").val(strNombreArchivo);
                strMsjArchivoClientes = getValorMsjParamDebito('MENSAJE_ARCHIVO_CLIENTES').replace('%archivo%',strNombreArchivo);
            }
            else
            {
                boolValidaOpcion   = true;
                strMsjValidaOpcion = getValorMsjParamDebito('MENSAJE_EXT_ARCHIVO');
            }
        } 
        
        //**Opción estados OS**
        if($("#strEstadosServicio").val() != "" )
        {
            strMsjEstadosServicio = getValorMsjParamDebito('MENSAJE_ESTADOS_OS').replace('%estadosOS%',$("#strEstadosServicio").val());
        }
        
        //**Opción Fechas de activación**
        $('#strFechaActDesde').val("");
        $('#strFechaActHasta').val("");
        
        if( !boolValidaOpcion && ( fechaActDesdeTemp !== null || fechaActHastaTemp !== null ) ) 
        {
            //Se llama a la función para validar las fechas.
            var strMsjFechaActivacion = validaFechasActivacion(fechaActDesdeTemp,fechaActHastaTemp);
            if( strMsjFechaActivacion === "OK" )
            {
                var fechaActDesde = Ext.Date.format(new Date(fechaActDesdeTemp),'Y-m-d');
                var fechaActHasta = Ext.Date.format(new Date(fechaActHastaTemp),'Y-m-d');
                
                $('#strFechaActDesde').val(fechaActDesde.split('-').reverse().join('-'));
                $('#strFechaActHasta').val(fechaActHasta.split('-').reverse().join('-'));
        
                strMsjFeActivacion = getValorMsjParamDebito('MENSAJE_FECHA_ACTIVACION').replace('%fechaDesde%',fechaActDesde);
                strMsjFeActivacion = strMsjFeActivacion.replace('%fechaHasta%',fechaActHasta);
            }
            else
            {
                boolValidaOpcion   = true;
                strMsjValidaOpcion = strMsjFechaActivacion;
            }
        }
        
        //**Opción Motivos de Rechazo**
        if($('#strMotivosRechazo').val() !== "")
        {
            strMsjMotivoRechazo = getValorMsjParamDebito('MENSAJE_MOTIVO_RECHAZO_HOMOLOGADO').replace('%motivoRechazo%',
                                                                                                   $("#strMotivosRechazo").val());
            strMsjMotivoRechazo = splitMensaje(strMsjMotivoRechazo);  
        } 
        
        var strMensajes = strMsjArchivoClientes + strMsjEstadosServicio + strMsjFeActivacion + strMsjMotivoRechazo;
        
        //Variable que contiene el mensaje final de las opciones seleccionadas.
        strMsjOpciones = strMensajes !== "" ? '<br><br> Opciones seleccionadas: <br>' +strMensajes : "";
        
    } //Fin flujo opciones
    
    
    //obtiene mensajes para las validaciones del proceso de Debitos Planificados
    Ext.Ajax.request
            ({
                url: strUrlGetMensajesDebitos,
                method: 'get',
                datatype: 'json',
                timeout: 9000000,
                reader: 
                    {
                        type: 'json',
                        root: 'encontrados'
                    },
                params:
                { 
                    strNombreParametro: "DEBITOS_PLANIFICADOS"
                },
                success: function(response)
                {
                    mensajesDebitos = response.responseText;

                    if(strTabActivo == "debitosEspeciales")
                    {
                        selectionModel = grupoDebitoDetSelectionModel.getSelection();
                        intIdFormato   = cmbFormatosDebitar.getValue();
                        if( Ext.isEmpty(intIdFormato) )
                        {
                            Ext.Msg.alert('Atención','Debe seleccionar un formato');
                            boolContinuar = false;
                        }
                    }
                    else
                    {
                        selectionModel = sm.getSelection();
                    }
                
                    if( selectionModel.length > 0 )
                    {
                        for(var i=0 ;  i < selectionModel.length ; ++i)
                        {
                            if(strTabActivo == "debitosEspeciales")
                            {
                                param = param + selectionModel[i].data.intIdGrupoDet;
                                
                                //Se setea con igual puesto que todos los detalles de los debitos pertenecen a la misma cabecera
                                intIdGrupoDebitoCab = selectionModel[i].data.intIdGrupoCab;
                            }
                            else
                            {
                                param = param + selectionModel[i].data.id;
                            }
                            
                            if(i < (selectionModel.length -1))
                            {
                                param = param + '|';
                            }
                        }
                    }//( selectionModel.length > 0 )
                    else
                    {
                        boolContinuar = false;
                        
                        Ext.Msg.alert('Atención','Seleccione por lo menos un registro de la lista');
                    }//( selectionModel.length < 0 )
                        
                        
                        if (boolContinuar && booleanAplicaCiclosFac && $('#intCicloId').val() === '') {
                            Ext.Msg.alert('Atencio&#769;n', 'Debe seleccionar el ciclo al que pertenece el de&#769;bito.');
                            boolContinuar = false;
                        }
                        
                        if ((boolContinuar && escenarioDebito == '' && strPrefijoEmpresa == 'MD') || 
                            (escenarioDebito != 'ESCENARIO_BASE' && filtroEscenario == '' && strPrefijoEmpresa == 'MD')) {
                            console.log(boolContinuar+":"+escenarioDebito+":"+strPrefijoEmpresa+":"+filtroEscenario+":");
                            Ext.Msg.alert('Atencio&#769;n', 'Debe seleccionar un escenario y su filtro para generar el de&#769;bito.');
                            boolContinuar = false;
                        }
                        
                        if ((boolContinuar && escenarioDebito == '' && strPrefijoEmpresa == 'TN')) {
                            Ext.Msg.alert('Atencio&#769;n', 'Debe seleccionar un escenario para generar el de&#769;bito.');
                            boolContinuar = false;
                        }
                
                        if(boolContinuar && boolPlanificado && Ext.isEmpty($('#strDatePlanificado').val()) && getParametroDebitoPlanificado(strFlujoPlanificado,'FlujoDebitoPlanificado') == 'SI'){
                            Ext.Msg.alert('Atencio&#769;n', getMensajeDebito(mensajesDebitos,'MensajeFechaVacia'));
                            boolContinuar = false;
                        }
                        
                        if(boolContinuar && boolPlanificado  && !Ext.isEmpty($('#strDatePlanificado').val()) &&(dateActual> datePlanificado)&& getParametroDebitoPlanificado(strFlujoPlanificado,'FlujoDebitoPlanificado') == 'SI'){
                            Ext.Msg.alert('Atencio&#769;n', getMensajeDebito(mensajesDebitos,'MensajeFechaMenor'));
                            boolContinuar = false;
                        }
                
                        if(boolContinuar && strFlujoGeneracionDebito === 'SI' && boolValidaOpcion)
                        {
                            Ext.Msg.alert('Atencio&#769;n', strMsjValidaOpcion); 
                            boolContinuar = false; 
                        }
                    
                    if( boolContinuar )
                    {
                        if( strPrefijoEmpresa == "MD" )
                        {
                            Ext.MessageBox.wait("Validando débitos...");
                
                            Ext.Ajax.request
                            ({
                                url: strUrlValidadorDebitoExistente,
                                method: 'get',
                                datatype: 'json',
                                timeout: 9000000,
                                params:
                                { 
                                    strTabActivo: strTabActivo,
                                    strDebitos: param,
                                    intIdGrupoDebitoCab: intIdGrupoDebitoCab,
                                    intCicloId: $('#intCicloId').val()
                                },
                                success: function(response)
                                {
                                    Ext.MessageBox.hide();
                                    var strRespuesta = response.responseText;
                
                                    if( 'OK' == strRespuesta)
                                    {   
                                        Ext.Msg.confirm('Alerta',boolPlanificado ? getMensajeDebito(mensajesDebitos,'MensajePlanificado').replace('%FECHA%',Ext.Date.format(datePlanificado,'m/d/Y'))+strMsjOpciones:getMensajeDebito(mensajesDebitos,'MensajeNoPlanificado')+strMsjOpciones, function(btn)
                                        {
                                            if(btn=='yes')
                                            {   $('#strDatePlanificado').val(Ext.Date.format(datePlanificado,'Y-m-d h:i:s'));
                                                procesarDebitoAceptado(intIdGrupoDebitoCab, param, intIdFormato, oficina);
                                            }
                                        });
                                    }
                                    else
                                    {
                                        //Se concatena variable de mensaje 'strMsjOpciones' con las opciones seleccionadas en caso de cumplir las condiciones.
                                        strRespuesta = strRespuesta+strMsjOpciones;
                                        Ext.Msg.confirm('Alerta', strRespuesta, function(btn)
                                        {
                                            if(btn=='yes')
                                            {   $('#strDatePlanificado').val(Ext.Date.format(datePlanificado,'Y-m-d h:i:s'));
                                                procesarDebitoAceptado(intIdGrupoDebitoCab, param, intIdFormato, oficina);
                                            }
                                        });
                                    }
                                },
                                failure: function(result)
                                {
                                    Ext.MessageBox.hide();
                                    Ext.Msg.alert('Error',result.responseText); 
                                }
                            });
                        }//( strPrefijoEmpresa == "MD" )
                        else
                        {
                            //Se concatena variable mensaje con las opciones en caso de cumplir las condiciones para TN.
                            Ext.Msg.confirm('Alerta',getMensajeDebito(mensajesDebitos,'MensajeNoPlanificado')+strMsjOpciones, function(btn)
                            {
                                if(btn=='yes')
                                {
                                    procesarDebitoAceptado(intIdGrupoDebitoCab, param, intIdFormato, oficina);
                                }
                            });
                        }//( strPrefijoEmpresa == "TN" )
                    }//( boolContinuar )


                },
                failure: function(result)
                {
                    Ext.Msg.alert('Error',result.responseText); 
                }
            });
    
    
}


function procesarDebitoAceptado(intIdGrupoDebitoCab, strParam, intIdFormato, intIdOficina)
{
    Ext.MessageBox.wait("Procesando débitos...");
    
    if(strTabActivo == "debitosEspeciales")
    {
        $('#debitos').val('');
        $('#intIdGrupoDebitoCab').val(intIdGrupoDebitoCab);
        $('#strIdsGrupoDebitoDet').val(strParam);
        $('#intIdFormato').val(intIdFormato);
    }
    else
    {
        $('#debitos').val(strParam);
    }

    $('#oficinaId').val(intIdOficina); 
    $('#strTabActivo').val(strTabActivo); 

    document.forms[0].submit();
}

function cargaMensajeEspera() {
    if  ($('#respuestadebitoextratype_CuentaId').val() == "0")
    {
        Ext.Msg.alert('Error', 'Debe escoger cuenta bancaria de empresa');
        return false;
    }

    Ext.MessageBox.wait("Procesando el archivo de respuesta...");
};

    /**
    * Función encargada de realizar las validaciones de las fechas seleccionadas de activación para el flujo de débitos.
    * Devuelve como retorno el mensaje parametrizado en caso de cumplir con alguna condición.
    * 
    * @author Alex Arreaga <atarreaga@telconet.ec>
    * @version 1.0 08-09-2022
    * @since 1.0
    * 
    * @author Alex Arreaga <atarreaga@telconet.ec>
    * @version 1.1 06-01-2023 - Se corrige la forma de obtener y validar los valores de las fechas de activación.
    */
    function validaFechasActivacion(fechaActDesdeTemp,fechaActHastaTemp) {
        
        var strFechaActivacionDesde  = fechaActDesdeTemp !== null ? Ext.Date.format(new Date(fechaActDesdeTemp),'Y-m-d') : "";
        var strFechaActivacionHasta  = fechaActHastaTemp !== null ? Ext.Date.format(new Date(fechaActHastaTemp),'Y-m-d') : "";
        var strMensajeFechasAct      = "";
        var strDetParamMsjAct        = "";

        if(strFechaActivacionDesde !== "" && strFechaActivacionHasta === "" )
        {
            strDetParamMsjAct = 'MENSAJE_FECHA_DESDE_HASTA';
        } 

        if(strFechaActivacionDesde === "" && strFechaActivacionHasta !== "" )
        {
            strDetParamMsjAct = 'MENSAJE_FECHA_DESDE_HASTA';
        } 

        if(strFechaActivacionDesde !== "" && strFechaActivacionHasta !== "" )
        {
            if(strFechaActivacionDesde > strFechaActivacionHasta)
            {
                strDetParamMsjAct = 'MENSAJE_FECHA_DESDE_MENOR';
            }

            if(strFechaActivacionHasta > Ext.Date.format(new Date,'Y-m-d'))
            {
                strDetParamMsjAct = 'MENSAJE_FECHA_HASTA_MENOR_ACTUAL';
            }
        } 

        strMensajeFechasAct = strDetParamMsjAct !== "" ? getValorMsjParamDebito(strDetParamMsjAct) : "OK";
        
        return strMensajeFechasAct;
    };

    /**
    * Función encargada de obtener valor parametrizado para flujo de las opciones de débitos.
    * 
    * @author Alex Arreaga <atarreaga@telconet.ec>
    * @version 1.0 08-09-2022
    * @since 1.0
    */
    function getValorMsjParamDebito(strDescripcionParam) {
        var strNombreParamCab = 'PARAM_GENERACION_DEBITOS';
        var strValor = "";

        $.ajax({
            url: stUrlGetValorMsjParamDebito,
            method: 'get',
            async: false,
            data: {'strNombreParamCab': strNombreParamCab, 'strDescripcionParam':strDescripcionParam},
            success: function (data) {
                strValor = data.strValor;
            },
            error: function () {
                Ext.Msg.alert('Alert', 'Error: No se pudo obtener el valor parametrizado. '
                                       +'Consulte con el Administrador del Sistema'); 
            }
        });

        return strValor;
    };
    
    /**
    * Función encargada de realizar split al mensaje para ordenamiento.
    * 
    * @author Alex Arreaga <atarreaga@telconet.ec>
    * @version 1.0 09-09-2022
    * @since 1.0
    */
    function splitMensaje(strMensaje) {
        var arrayDeCadenas = strMensaje.split(',');
        var strCadena      = "";

        for (var intCount=0; intCount < arrayDeCadenas.length; intCount++) 
        {
            strCadena = strCadena + arrayDeCadenas[intCount] + " <br> ";
        }
   
        return strCadena;
    };
    
    /**
    * Función encargada de convertir el archivo de clientes a base64.
    * 
    * @author Alex Arreaga <atarreaga@telconet.ec>
    * @version 1.0 13-09-2022
    * @since 1.0
    */
    function conversionArchivoBase64(file)
    {
        // Encode the file using the FileReader API
        const reader = new FileReader();

        reader.onloadend = () => {
            // Use a regex to remove data url part
            const base64String = reader.result
                                       .replace('data:', '')
                                       .replace(/^.+,/, '');
                               
            $("#strArchivoClientes").val(base64String); 
        };
        reader.readAsDataURL(file);
    }