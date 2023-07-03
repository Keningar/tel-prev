Ext.require
([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);

var itemsPerPage                 = 100;
var intHeightGrid                = 350;
var store                        = '';
var estado_id                    = '';
var area_id                      = '';
var login_id                     = '';
var tipo_asignacion              = '';
var pto_sucursal                 = '';
var idClienteSucursalSesion;


Ext.onReady(function()
{
    
            
                $('#strCheckNuevoGrupo').val("");
                $('#strComboGrupo').val("");
                $('#strComboBanco').val("");
                $('#strComboTipoCuenta').val("");
                $('#strTextGrupo').val("");
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
                Ext.define('TiposCuentaList', {
                    extend: 'Ext.data.Model',
                    fields: [
                        {name:'id_cuenta', type:'int'},
                        {name:'descripcion_cuenta', type:'string'}
                    ]
                });
                storeTipoCuenta = Ext.create('Ext.data.Store', {
                        model: 'TiposCuentaList',
                        proxy: {
                            type: 'ajax',
                            url : strUrlObtieneComboTipoCuenta,
                            reader: {
                                type: 'json',
                                totalProperty: 'total',
                                root: 'encontrados'
                            }
                        }
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
                            nombre:'',
                            start: 0, 
                            limit: 100

                        },
                        simpleSortMode: true
                    },
                    autoLoad: true
                });
              
            
                storeComboGrupo = Ext.create('Ext.data.JsonStore',
                {
                    model: 'ListaDetalleModel',
                    pageSize: itemsPerPage,
                    proxy: 
                    {
                        type: 'ajax',
                        url: strUrlObtieneComboGrupo,
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
                            nombre:'',
                            start: 0,
                             limit: 100
                        },
                        simpleSortMode: true
                    },
                    autoLoad: true
                });            
            
                Ext.define('ModelStore', {
                    extend: 'Ext.data.Model',
                    fields:
                    [				
                        {name:'id_banco', mapping:'id_banco'},
                        {name:'descripcion_banco', mapping:'descripcion_banco'}               
                    ],
                    idProperty: 'id_banco'
                });

                
                storeComboBanco = new Ext.data.Store({ 
                    pageSize: 10,
                    model: 'ModelStore',
                    total: 'total',
                    proxy: {
                        type: 'ajax',
                        timeout: 600000,
                        url : strUrlObtieneComboBancos,
                        reader: {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        },
                        extraParams: {
                            nombre: '',
                            estado: 'Activo',
                            start: 0, 
                            limit: 1000
                        }
                    },
                    autoLoad: true
                });
                                

                
                
                
                
                
                var listView = Ext.create('Ext.grid.Panel', 
                {
                    width:891,
                    height: intHeightGrid,
                    collapsible:false,
                    title: '',
                    id:'panelDebitos',
                    bbar: Ext.create('Ext.PagingToolbar', 
                    {
                        store: store,
                        displayInfo: true,
                        displayMsg: 'Mostrando grupos {0} - {1} of {2}',
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
                            text: 'Grupo',
                            width: 250,
                            dataIndex: 'banco'
                        },
                        {
                            text: 'Bancos',
                            width: 700,
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
                    width: 650,
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
                        columns: 3,
                        align: 'left',
                        tableAttrs: {
                            style: {
                                width: '100%',
                                height: '100%'
                            }
                        },
                    },
                    items: [{
                    
                        xtype: 'checkbox',
                        fieldLabel : 'Nuevo Grupo',
                        id : 'checkNuevoGrupo',
                        name : 'checkNuevoGrupo',
                        width: 300,
                        checked: false,
                        hidden: false,
                        listeners:{
                            afterRender: function() {
                                    
                                    
                                },
                            change :function (checkNuevoGrupo) {
                                var fecha3 = Ext.getCmp('textGrupo');
                                if (checkNuevoGrupo.value) {

                                    fecha3.show();
                                } else {
                                    
                                    fecha3.setValue();
                                    fecha3.hide();
                                    
                                }
            
                                $('#strCheckNuevoGrupo').val(checkNuevoGrupo.getValue());
                            }
                        }
            
                    }]
                });
                panel.render('div1');

                var panel1 = Ext.create('Ext.form.Panel', {
                    width: 950,
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
                        columns: 3,
                        align: 'left',
                        tableAttrs: {
                            style: {
                                width: '100%',
                                height: '100%'
                            }
                        }
                    },
                    items: [{
                        xtype: 'combobox',
                        id:'comboGrupo',
                        name: 'comboGrupo',
                        width: 250,
                        fieldLabel: 'Grupo Base',
                        displayField: 'banco',
                        valueField: 'id',
                        store:storeComboGrupo,
                        listeners: {
                            afterRender: function() {
                            },
                            change:function (comboGrupo) {
                                $('#strComboGrupo').val(comboGrupo.getValue());
                            }
                        } 
                    },{
                        xtype: 'combobox',
                        id:'comboBanco',
                        name: 'comboBanco',
                        width: 300,
                        fieldLabel: 'Banco',
                        displayField: 'descripcion_banco',
                        valueField: 'id_banco',
                        store:storeComboBanco,
                        listeners: {
                            select:{fn:function(comboBanco, value) {
                                Ext.getCmp('comboTipoCuenta').reset();  
                                $('#strComboBanco').val(comboBanco.getValue());
                                //Ext.getCmp('cmb_accion').reset();  
                                storeTipoCuenta.proxy.extraParams = {id_banco: comboBanco.getValue()};
                                storeTipoCuenta.load({params: {}});
            
                            }}
                        }
                    },{
                        xtype: 'combobox',
                        id:'comboTipoCuenta',
                        name: 'comboTipoCuenta',
                        width: 250,
                        fieldLabel: 'Tipo Cuenta',
                        displayField: 'descripcion_cuenta',
                        valueField: 'id_cuenta',
                        store:storeTipoCuenta,
                        listeners: {
                            select:{fn:function(comboBanco, value) {
                                $('#strComboTipoCuenta').val(comboBanco.getValue());
                            }}

                        }
                    }]
                });
                panel1.render('div2');

                var panel2 = Ext.create('Ext.form.Panel', {
                    width: 650,
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
                        }
                    },
                    items: [ {
                        xtype: 'textfield',
                        id:'textGrupo',
                        name: 'textGrupo',
                        width: 250,
                        fieldLabel: 'Grupo Nuevo',
                        listeners: {
                            afterRender: function() {
                                var checkNuevoGrupo = Ext.getCmp('checkNuevoGrupo');
                                if (!checkNuevoGrupo.value)
                                    this.hide();
                            },
                            change:function (textGrupo) {
                                $('#strTextGrupo').val(textGrupo.getValue());
                            }
                        } 
                    ,}]
                });
                panel2.render('div3');
            
                
                
                
            
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
                
               
                
                
                
                
                            
                            
                var tabsFiltros = Ext.create('Ext.tab.Panel',
                {
                    id: 'tab_panel',
                    width: 1000,
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
                            title: 'Grupo Debitos Generales',
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
                    items:[tabsFiltros]
                });
                
                objFilterPanel.render('lista');
                
            
    
});






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
 * 
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 06-05-2021 - Se agrega logica para agregar bancos a un grupo existente
 */
function procesar()
{
    var param               = '';
    var boolContinuar       = true;
    var comboGrupo     = $('#strComboGrupo').val();
    var comboBanco     = $('#strComboBanco').val();
    var checkGrupoNuevo     = (Ext.isEmpty($('#strCheckNuevoGrupo').val())?false:$('#strCheckNuevoGrupo').val());
    var comboTipoCuenta     = $('#strComboTipoCuenta').val();
    var strTextGrupo     = $('#strTextGrupo').val();
    var mensaje = '';
    var mensaje1 ='debe seleccionar un Grupo Base para continuar';
    var mensaje2 ='debe seleccionar un Banco para continuar';
    var mensaje3 ='debe seleccionar un Tipo Cuenta para continuar';
    var mensaje4 ='debe escribir el nombre del Grupo Nuevo para continuar';

    if (checkGrupoNuevo == false) {
        if (Ext.isEmpty(comboGrupo)) {
            mensaje = mensaje1;
        } else if(Ext.isEmpty(comboBanco)) {
            mensaje = mensaje2;
        } else if(Ext.isEmpty(comboTipoCuenta)) {
            mensaje = mensaje3;
        } 
        console.log('grupo existente'); 
    } else {
        if (Ext.isEmpty(comboGrupo)) {
            mensaje = mensaje1;
        } else if(Ext.isEmpty(comboBanco)) {
            mensaje = mensaje2;
        } else if(Ext.isEmpty(comboTipoCuenta)) {
            mensaje = mensaje3;
        } else if(Ext.isEmpty(strTextGrupo)) {
            mensaje = mensaje4;
        } 
        console.log('grupo nuevo');  
    }
    
    if(! Ext.isEmpty(mensaje) )
        {
            Ext.Msg.alert(mensaje);
            boolContinuar = false;
        }
    
    if (boolContinuar) {
        Ext.Msg.confirm('Alerta','va a proceder a registrar el Banco seleccionado dentro del Grupo Base elegido, ¿ Esta seguro de continuar?', function(btn)
            {
                if(btn=='yes')
                {
                    procesarDebitoAceptado(comboGrupo, comboBanco, comboTipoCuenta, strTextGrupo, checkGrupoNuevo);
                }
            });
    } 
    
    
}



function procesarDebitoAceptado(comboGrupo, comboBanco, comboTipoCuenta, strTextGrupo, checkGrupoNuevo)
{
    Ext.MessageBox.wait("Procesando débitos...");
    
    $('#strComboGrupo').val(comboGrupo);
    $('#strComboBanco').val(comboBanco);
    $('#strCheckNuevoGrupo').val(checkGrupoNuevo);
    $('#strComboTipoCuenta').val(comboTipoCuenta);
    $('#strTextGrupo').val(strTextGrupo);

    

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
