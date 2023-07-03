Ext.onReady(function()
{
    dateFechaReprocesamientoContable = new Ext.form.DateField
    ({
        id: 'dateFechaReprocesamientoContable',
        fieldLabel: 'Fecha de Reproceso Contable',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:325,
        editable: false
    });
	
    storeGetTipoProcesoContable = new Ext.data.Store
    ({ 
        total: 'total',
        autoLoad: true,
        proxy:
        {
            type: 'ajax',
            url : strUrlGetTiposProcesoContable,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams:
            {
                strTipoOpcion: 'FACTURACION'
            }
        },
        fields:
        [
            {name:'valor2',      mapping:'valor2'},//Corresponde al código del tipo de reprocesamiento contable a realizar
            {name:'descripcion', mapping:'descripcion'}//Corresponde al nombre del tipo de reprocesamiento contable a realizar
        ]
    });
    
	
    /* ******************************************* */
    /* *********** FILTROS DE BUSQUEDA *********** */
    /* ******************************************* */
    var filterPanelFinanciero = Ext.create('Ext.panel.Panel',
    {
        bodyPadding: 7,
        buttonAlign: 'center',
        layout:
        {
            type:'table',
            columns: 3,
            align: 'left'
        },
        border: false,
        bodyStyle:
        {
            background: '#fff'
        },
        buttons:
        [
            {
                text: 'Reprocesar Información Contable',
                iconCls: "icon_search",
                handler: function()
                { 
                    reprocesarContabilidad();
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function()
                { 
                    limpiarFiltros();
                }
            }

        ],   
        width: 970,
        title: 'Criterios de Búsqueda',
        collapsible : true,
        collapsed: false,
        items: 
        [
            {
                xtype:'fieldset',
                width: 950,
                title: 'General',
                collapsible: false,
                layout:
                {
                    type:'table',
                    columns: 5,
                    align: 'left'
                },
                items:
                [
                    {html:"&nbsp;", border:false, width: 100},
                    {
                        xtype: 'combobox',
                        id: 'cmbTipoProcesoContable',
                        fieldLabel: 'Tipo de Proceso',
                        typeAhead: true,
                        triggerAction: 'all',
                        displayField:'descripcion',
                        valueField: 'valor2',
                        selectOnTab: true,
                        editable: false,
                        remote: 'local',
                        store: storeGetTipoProcesoContable,
                        width: 325
                    },
                    {html:"&nbsp;", border:false, width:100},
                    dateFechaReprocesamientoContable,
                    {html:"&nbsp;", border:false, width:100}
                ]
            }
        ],
        renderTo: 'filtroReprocesamiento'
    }); 
 
});


function reprocesarContabilidad()
{
    var strTipoReporteContabilidad       = Ext.getCmp('cmbTipoProcesoContable').getValue();
    var strFechaReprocesamientoContable  = "";

    if( Ext.isEmpty(strTipoReporteContabilidad) )
    {
        Ext.Msg.alert('Alerta','Debe seleccionar un tipo de proceso');
        return false;
    }
    
    if( !Ext.isEmpty(Ext.getCmp('dateFechaReprocesamientoContable').getValue()) )
    {
        strFechaReprocesamientoContable  = Ext.util.Format.date(Ext.getCmp('dateFechaReprocesamientoContable').getValue(), 'd-m-Y');
    }
    else
    {
        Ext.Msg.alert('Alerta','Debe seleccionar una fecha válida para realizar el reprocesamiento de la información');
        return false;
    }

    Ext.MessageBox.wait('Reprocesando la información contable. Favor espere..');
    Ext.Ajax.request
    ({
        timeout: 9000000,
        url: strUrlReprocesarFacturacion,
        params:
        {
            strTipoReporteContabilidad: strTipoReporteContabilidad,
            strFechaReprocesamientoContable: strFechaReprocesamientoContable,
            strTipoOpcion: 'FACTURACION'
        },
        method: 'get',
        success: function(response) 
        {
            var mensajeRespuesta = response.responseText;
            
            if( "OK" == mensajeRespuesta )
            {
                Ext.Msg.alert('Informativo', 'El reproceso de la información contable fue realizado con éxito.');
            }
            else
            {
                Ext.Msg.alert('Atención', response.responseText);
            }
        },
        failure: function(result)
        {
            Ext.Msg.alert('Error ', 'Error al reprocesar la información contable: ' + result.statusText);
        }
    });
}


function limpiarFiltros()
{
    $('#tr_error').css("display", "none");
    $('#busqueda_error').html("");
    
    Ext.getCmp('cmbTipoProcesoContable').value = "";
    Ext.getCmp('cmbTipoProcesoContable').setRawValue("");
    Ext.getCmp('dateFechaReprocesamientoContable').value = "";
    Ext.getCmp('dateFechaReprocesamientoContable').setRawValue("");
}
 
