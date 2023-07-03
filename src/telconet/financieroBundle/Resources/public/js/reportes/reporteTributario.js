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
	
    /* ******************************************* */
    /* *********** FILTROS DE BUSQUEDA *********** */
    /* ******************************************* */
    
     Ext.create('Ext.panel.Panel',
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
                    generarReporteTributario();
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function()
                { 
                    limpiarReporteTributario();
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


function generarReporteTributario()
{
    var strFechaCreacionDesde  = "";
    var strFechaCreacionHasta  = "";
    var boolFechaCreacionDesde = false;
    var boolFechaCreacionHasta = false; 

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
    console.log(strFechaCreacionDesde);
    console.log(strFechaCreacionHasta);

    if( boolFechaCreacionDesde && boolFechaCreacionHasta )
    {
        var rangoFechaReporte = Utils.restaFechas(strFechaCreacionDesde, strFechaCreacionHasta);        

        if(rangoFechaReporte > 31)
        {
            Ext.Msg.alert('Alerta ','Rango de fechas excede el límite permitido (31 días) ');
            return false;            
        }
        
        if(strFechaCreacionDesde > strFechaCreacionHasta)
        {
            Ext.Msg.alert('Alerta ','Por Favor para realizar la búsqueda Fecha Creación Desde debe ser fecha menor a Fecha Creación Hasta.');
            return false; 
        }
    }
    else
    {
        Ext.Msg.alert("Atención", "Debe elegir un rango de Fechas de Creación válido");
        return false;
    }

    Ext.MessageBox.wait('Generando Reporte. Favor espere..');
    Ext.Ajax.request({
        timeout: 9000000,
        url: strUrlGenerarReporteTributario,
        params: {
            strFechaReporteDesde: strFechaCreacionDesde,
            strFechaReporteHasta: strFechaCreacionHasta
        },
        method: 'get',
        success: function(response) {                 
            Ext.Msg.alert('Mensaje', response.responseText);
        },
        failure: function(result) {
            Ext.Msg.alert('Error ', 'Error al generar y enviar reporte: ' + result.statusText);
        }
    });
}

function limpiarReporteTributario()
{
    $('#tr_error').css("display", "none");
    $('#busqueda_error').html("");
    
    Ext.getCmp('dateFechaCreacionDesde').value="";
    Ext.getCmp('dateFechaCreacionDesde').setRawValue("");
    Ext.getCmp('dateFechaCreacionHasta').value="";
    Ext.getCmp('dateFechaCreacionHasta').setRawValue("");
}
 
