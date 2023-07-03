
jQuery(document).ready(function() {
    var fechasPublicacionHastaList    = jQuery('#fechasPublicacionHasta-fields-list');
    var newWidgetFecha=fechasPublicacionHastaList.attr('data-prototype');
    newWidgetFecha=newWidgetFecha.replace('__name__','0');
    newWidgetFecha=newWidgetFecha.replace('__name__','0');
    newWidgetFecha=newWidgetFecha.replace('__name__','0');
    newWidgetFecha=newWidgetFecha.replace('__name__','0');
    newWidgetFecha=newWidgetFecha.replace('__name__','0');
    newWidgetFecha=newWidgetFecha.replace('__name__','0');
    newWidgetFecha=newWidgetFecha.replace('__name__','0');

    var newLi = jQuery('<li></li>').html(newWidgetFecha);
    newLi.appendTo(jQuery('#fechasPublicacionHasta-fields-list'));

    jQuery('#agregar_imagen').click(function() {           
        var imagenesList = jQuery('#imagenes-fields-list');
        var tiposList    = jQuery('#tipos-fields-list'); 
        var fechasPublicacionHastaList    = jQuery('#fechasPublicacionHasta-fields-list');

        var newWidget = imagenesList.attr('data-prototype');
        var newWidgetTipo = tiposList.attr('data-prototype');
        var newWidgetFecha=fechasPublicacionHastaList.attr('data-prototype');
        var name='__name__';
        newWidget = newWidget.replace(name, imagenesCount);            
        newWidgetTipo = newWidgetTipo.replace(name, tiposCount);
        newWidget = newWidget.replace(name, imagenesCount);            
        newWidgetTipo = newWidgetTipo.replace(name, tiposCount);

        newWidgetFecha=newWidgetFecha.replace(name, fechasPublicacionHastaCount);
        newWidgetFecha=newWidgetFecha.replace(name, fechasPublicacionHastaCount);
        newWidgetFecha=newWidgetFecha.replace(name, fechasPublicacionHastaCount);
        newWidgetFecha=newWidgetFecha.replace(name, fechasPublicacionHastaCount);
        newWidgetFecha=newWidgetFecha.replace(name, fechasPublicacionHastaCount);
        newWidgetFecha=newWidgetFecha.replace(name, fechasPublicacionHastaCount);
        newWidgetFecha=newWidgetFecha.replace(name, fechasPublicacionHastaCount);

        imagenesCount++;
        tiposCount++;
        fechasPublicacionHastaCount++;
        // crea un nuevo elemento lista y lo añade a la lista
        var newLi = jQuery('<li></li>').html(newWidget);
        newLi.appendTo(jQuery('#imagenes-fields-list'));

        var newLi = jQuery('<li></li>').html(newWidgetTipo);             
        newLi.appendTo(jQuery('#tipos-fields-list'));

        var newLi = jQuery('<li></li>').html(newWidgetFecha);
        newLi.appendTo(jQuery('#fechasPublicacionHasta-fields-list'));

        return false;
    });
});

function validarDocumentosObligatoriosTransporte()
{
    var tiposDocs = document.getElementById("imagenes-fields-list").getElementsByTagName("li");
    var idMedioTransporte = document.getElementById("idMedioTransporte").value;
   
    //+1 por la imagen por defecto que tiene
    var numImagenesPorSubir=tiposDocs.length+1;
    var strIdTiposDocsPorSubir="";
    var strFechasHastaDocsPorSubir="";
    var contArchivosVacíos=0;
    for (i = 0; i < numImagenesPorSubir; i++)
    {
        if((document.getElementById("infodocumentotype_imagenes_"+i).value)!="")
        {
            var year=document.getElementById("infodocumentotype_fechasPublicacionHasta_"+i+"_year").value;
            var month=document.getElementById("infodocumentotype_fechasPublicacionHasta_"+i+"_month").value;
            var day=document.getElementById("infodocumentotype_fechasPublicacionHasta_"+i+"_day").value;

            if(year=="Anio") year="";
            if(month=="Mes") month="";
            if(day=="Dia") day="";
            var fecha=""+year+"-"+month+"-"+day;
            if(i== (numImagenesPorSubir-1))
            {
                strIdTiposDocsPorSubir=strIdTiposDocsPorSubir+document.getElementById("infodocumentotype_tipos_"+i).value;
                strFechasHastaDocsPorSubir=strFechasHastaDocsPorSubir+fecha;
            }
            else
            {
                strIdTiposDocsPorSubir=strIdTiposDocsPorSubir+document.getElementById("infodocumentotype_tipos_"+i).value+"/";
                strFechasHastaDocsPorSubir=strFechasHastaDocsPorSubir+fecha+"/";
            }
        }
        else
        {
            contArchivosVacíos++;
        }
    }
    
    if(contArchivosVacíos==numImagenesPorSubir)
    {
         Ext.Msg.alert('Alerta', 'No se han seleccionado archivos');
    }
    else 
    {
        if(contArchivosVacíos>0)
        {
            Ext.Msg.confirm('Alerta','Hay archivos sin seleccionar. Desea subir los archivos restantes?', function(btn)
            {
                if(btn=='yes')
                {
                    enviarAGuardarArchivos(strIdTiposDocsPorSubir,idMedioTransporte,strFechasHastaDocsPorSubir);
                }
            });
        }
        else
        {
            enviarAGuardarArchivos(strIdTiposDocsPorSubir,idMedioTransporte,strFechasHastaDocsPorSubir);
        }
    }

}

function enviarAGuardarArchivos(strIdTiposDocsPorSubir,idMedioTransporte,strFechasHastaDocsPorSubir)
{
    Ext.MessageBox.wait("Verificando Archivos...", 'Por favor espere'); 
    Ext.Ajax.request(
    {
            url: urlValidarDocumentosObligatorios,
            method: 'post',
            params:
            {
                strIdsTiposDocsASubir: strIdTiposDocsPorSubir,
                idMedioTransporte  : idMedioTransporte,
                strFechasHastaDocsPorSubir: strFechasHastaDocsPorSubir
            },
            success: function(parResponse)
            {
                Ext.MessageBox.hide();
                var response = parResponse.responseText;
                var parsedJSON = eval('(' + response + ')');
                if (parsedJSON.msg == '')
                {
                    $('#diverrorident').attr('style', 'display:none');
                    $('#diverrorident').html('');
                    Ext.MessageBox.wait("Grabando Datos...", 'Por favor espere'); 
                    document.getElementById("form-Imgs").submit();
                }
                else
                {
                    $('#diverrorident').attr('style', '');
                    $('#diverrorident').html(parsedJSON.msg);

                }
            },
            failure: function(result)
            {
                Ext.MessageBox.hide();
                Ext.Msg.show({
                    title: 'Error',
                    msg: result.statusText,
                    buttons: Ext.Msg.OK,
                    icon: Ext.MessageBox.ERROR
                });
            }
    });
}
    
