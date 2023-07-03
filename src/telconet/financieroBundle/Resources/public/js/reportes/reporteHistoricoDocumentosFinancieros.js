Ext.onReady(function()
{
    dateFechaCreacionDesde = new Ext.form.DateField
    ({
        id: 'dateFechaCreacionDesde',
        fieldLabel: 'Fecha Creación Desde',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:325,
        editable: false
    });
    
    dateFechaCreacionHasta = new Ext.form.DateField
    ({
        id: 'dateFechaCreacionHasta',
        fieldLabel: 'Fecha Creación Hasta',
        labelAlign : 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width:325,
        editable: false
    });
	
    storeGetTipoReportesHistoricoDocumentosFinancieros = new Ext.data.Store
    ({ 
        total: 'total',
        autoLoad: true,
        proxy:
        {
            type: 'ajax',
            url : strUrlGetTiposReportesHistoricoDocumentosFinancieros,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
        [
            {name:'valor2',      mapping:'valor2'},//Corresponde al código del tipo de reporte
            {name:'descripcion', mapping:'descripcion'}//Corresponde al nombre del tipo de reporte
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
                    generarReporteHistoricoDocumentosFinancieros();
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function()
                { 
                    limpiarReporteHistoricoDocumentosFinancieros();
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
                        id: 'cmbTipoReporte',
                        fieldLabel: 'Tipo de Reporte',
                        typeAhead: true,
                        triggerAction: 'all',
                        displayField:'descripcion',
                        valueField: 'valor2',
                        selectOnTab: true,
                        editable: false,
                        remote: 'local',
                        store: storeGetTipoReportesHistoricoDocumentosFinancieros,
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
                    dateFechaCreacionDesde,
                    {html:"&nbsp;", border:false, width:100},
                    dateFechaCreacionHasta,
                    {html:"&nbsp;", border:false, width:100}
                ]
            }
        ],
        renderTo: 'filtroBusquedaDocumentos'
    }); 
 
});


function generarReporteHistoricoDocumentosFinancieros()
{
    var strTipoReporte         = Ext.getCmp('cmbTipoReporte').getValue();
    var strFechaCreacionDesde  = "";
    var strFechaCreacionHasta  = "";
    var boolFechaCreacionDesde = false;
    var boolFechaCreacionHasta = false; 

    if( Ext.isEmpty(strTipoReporte) )
    {
        Ext.Msg.alert('Alerta','Debe seleccionar un tipo de reporte a generar');
        return false;
    }
    
    if( !Ext.isEmpty(Ext.getCmp('dateFechaCreacionDesde').getValue()) )
    {
        strFechaCreacionDesde  = Ext.util.Format.date(Ext.getCmp('dateFechaCreacionDesde').getValue(), 'd-m-Y');
        boolFechaCreacionDesde = true;
    }

    if( !Ext.isEmpty(Ext.getCmp('dateFechaCreacionHasta').getValue()) )
    {
        strFechaCreacionHasta  = Ext.util.Format.date(Ext.getCmp('dateFechaCreacionHasta').getValue(), 'd-m-Y');
        boolFechaCreacionHasta = true;
    }


    if( boolFechaCreacionDesde && boolFechaCreacionHasta )
    {
        var rangoFechaContabilizacion = Utils.restaFechas(strFechaCreacionDesde, strFechaCreacionHasta);        

        if(rangoFechaContabilizacion > 31)
        {
            Ext.Msg.alert('Alerta ','Rango de fechas excede el limite permitido (31 dias) ');
            return false;            
        }
    }
    else
    {
        Ext.Msg.alert("Atención", "Debe elegir un rango de Fechas de Creación válido");
        return false;
    }

    Ext.MessageBox.wait('Generando Reporte. Favor espere..');
    Ext.Ajax.request
    ({
        timeout: 9000000,
        url: strUrlGenerarReporteHistoricoDocumentoFinanciero,
        params:
        {
            strTipoReporteContabilidad: strTipoReporte,
            strFechaContabilizacionDesde: strFechaCreacionDesde,
            strFechaContabilizacionHasta: strFechaCreacionHasta
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


function limpiarReporteHistoricoDocumentosFinancieros()
{
    $('#tr_error').css("display", "none");
    $('#busqueda_error').html("");
    
    Ext.getCmp('cmbTipoReporte').value="";
    Ext.getCmp('cmbTipoReporte').setRawValue("");
    Ext.getCmp('dateFechaCreacionDesde').value="";
    Ext.getCmp('dateFechaCreacionDesde').setRawValue("");
    Ext.getCmp('dateFechaCreacionHasta').value="";
    Ext.getCmp('dateFechaCreacionHasta').setRawValue("");
}
 
