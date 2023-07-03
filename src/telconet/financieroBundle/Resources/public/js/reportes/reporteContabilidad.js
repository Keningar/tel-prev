Ext.onReady(function()
{
    dateFechaContabilizacionDesde = new Ext.form.DateField
    ({
        id: 'dateFechaContabilizacionDesde',
        fieldLabel: 'Fecha Contabilización Desde',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:325,
        editable: false
    });
    
    dateFechaContabilizacionHasta = new Ext.form.DateField
    ({
        id: 'dateFechaContabilizacionHasta',
        fieldLabel: 'Fecha Contabilización Hasta',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:325,
        editable: false
    });
	
    storeGetTipoReportesContabilidad = new Ext.data.Store
    ({ 
        total: 'total',
        autoLoad: true,
        proxy:
        {
            type: 'ajax',
            url : strUrlGetTiposReportesContabilidad,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
        [
            {name:'valor2',      mapping:'valor2'},//Corresponde al código del tipo de reporte para contabilidad
            {name:'descripcion', mapping:'descripcion'}//Corresponde al nombre del tipo de reporte para contabilidad
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
                text: 'Generar Excel',
                iconCls: "icon_search",
                handler: function()
                { 
                    generarReporteContabilidad();
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function()
                { 
                    limpiarReporteContabilidad();
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
                        id: 'cmbTipoReporteContabilidad',
                        fieldLabel: 'Tipo de Reporte',
                        typeAhead: true,
                        triggerAction: 'all',
                        displayField:'descripcion',
                        valueField: 'valor2',
                        selectOnTab: true,
                        editable: false,
                        remote: 'local',
                        store: storeGetTipoReportesContabilidad,
                        width: 325
                    },
                    {html:"&nbsp;", border:false, width:100},
                    {html:"&nbsp;", border:false, width:325},
                    {html:"&nbsp;", border:false, width:100},
                    {html:"&nbsp;", border:false, width:100},
                    {html:"&nbsp;", border:false, width:325},
                    {html:"&nbsp;", border:false, width:100},
                    {html:"&nbsp;", border:false, width:325},
                    {html:"&nbsp;", border:false, width:100},
                    {html:"&nbsp;", border:false, width:100},
                    dateFechaContabilizacionDesde,
                    {html:"&nbsp;", border:false, width:100},
                    dateFechaContabilizacionHasta,
                    {html:"&nbsp;", border:false, width:100}
                ]
            }
        ],
        renderTo: 'filtroBusquedaDocumentos'
    }); 
 
});


function generarReporteContabilidad()
{
    var strTipoReporteContabilidad    = Ext.getCmp('cmbTipoReporteContabilidad').getValue();
    var strFechaContabilizacionDesde  = "";
    var strFechaContabilizacionHasta  = "";
    var boolFechaContabilizacionDesde = false;
    var boolFechaContabilizacionHasta = false; 

    if( Ext.isEmpty(strTipoReporteContabilidad) )
    {
        Ext.Msg.alert('Alerta','Debe seleccionar un tipo de reporte de contabilidad a generar');
        return false;
    }
    
    if( !Ext.isEmpty(Ext.getCmp('dateFechaContabilizacionDesde').getValue()) )
    {
        strFechaContabilizacionDesde  = Ext.util.Format.date(Ext.getCmp('dateFechaContabilizacionDesde').getValue(), 'd-m-Y');
        boolFechaContabilizacionDesde = true;
    }

    if( !Ext.isEmpty(Ext.getCmp('dateFechaContabilizacionHasta').getValue()) )
    {
        strFechaContabilizacionHasta  = Ext.util.Format.date(Ext.getCmp('dateFechaContabilizacionHasta').getValue(), 'd-m-Y');
        boolFechaContabilizacionHasta = true;
    }


    if( boolFechaContabilizacionDesde && boolFechaContabilizacionHasta )
    {
        var rangoFechaContabilizacion = Utils.restaFechas(strFechaContabilizacionDesde, strFechaContabilizacionHasta);        

        if(rangoFechaContabilizacion > 31)
        {
            Ext.Msg.alert('Alerta ','Rango de fechas excede el limite permitido (31 dias) ');
            return false;            
        }
    }
    else
    {
        Ext.Msg.alert("Atención", "Debe elegir un rango de Fechas de Contabilización válido");
        return false;
    }

    Ext.MessageBox.wait('Generando Reporte. Favor espere..');
    Ext.Ajax.request
    ({
        timeout: 9000000,
        url: strUrlGenerarReporteContabilidad,
        params:
        {
            strTipoReporteContabilidad: strTipoReporteContabilidad,
            strFechaContabilizacionDesde: strFechaContabilizacionDesde,
            strFechaContabilizacionHasta: strFechaContabilizacionHasta
        },
        method: 'get',
        success: function(response) 
        {                 
            Ext.Msg.alert('Mensaje', response.responseText);
        },
        failure: function(result)
        {
            Ext.Msg.alert('Error ', 'Error al generar y enviar reporte: ' + result.statusText);
        }
    });
}


function limpiarReporteContabilidad()
{
    $('#tr_error').css("display", "none");
    $('#busqueda_error').html("");
    
    Ext.getCmp('cmbTipoReporteContabilidad').value="";
    Ext.getCmp('cmbTipoReporteContabilidad').setRawValue("");
    Ext.getCmp('dateFechaContabilizacionDesde').value="";
    Ext.getCmp('dateFechaContabilizacionDesde').setRawValue("");
    Ext.getCmp('dateFechaContabilizacionHasta').value="";
    Ext.getCmp('dateFechaContabilizacionHasta').setRawValue("");
}
 
