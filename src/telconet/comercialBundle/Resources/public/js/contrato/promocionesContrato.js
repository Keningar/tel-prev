$(document).ready(function () {   

    $.ajax({
    url: url_grid,
    method: 'GET',
    success: function (data) {

        var intContador;
        var strDivContainerPlan    = '';
        var strDivContainerProd    = '';
        var strDivContainerGeneral = '';
        var strDivContainerResumen = '';
        var strDivResumenPlan      = '';
        var strDivResumenProd      = '';

        for (intContador = 0; intContador < data.servicios.length; intContador++) 
        {

            var intIdServicio      = data.servicios[intContador].idServicio;
            var strTipoServicio    = data.servicios[intContador].tipo;
            var strNombreServicio  = data.servicios[intContador].descripcionProducto;
            var intIdProducto      = data.servicios[intContador].idProducto;
            var strEstado          = data.servicios[intContador].estado;
            var strTipoAdendum     = data.servicios[intContador].strTipoAdendum;
            var strCodigoMens      = data.servicios[intContador].strCodigoMens;
            var strCodigoIns       = data.servicios[intContador].strCodigoIns;
            var strCodigoBw        = data.servicios[intContador].strCodigoBw;

            if (strTipoServicio === 'plan' && strEstado !== 'Eliminado' && strEstado !== 'Rechazada' && strEstado!=='Anulado' && strEstado !== 'Cancelado' && ( ($('#nombrePantalla').val() === 'Adendum' && strTipoAdendum == null) || $('#nombrePantalla').val() === 'Contrato'))
            {
                strDivContainerPlan+=   
                "<div class='container shadow-sm p-4 mb-4 bg-white'>"+
                    "<div class='form-group row'>"+
                        "<div class='col-md-12'>"+
                            "<div class='accordion container-fluid' id='accordionExample1'> "+
                                "<div class='card' style='border-bottom:1px solid rgba(0,0,0,.125)'>"+
                                    "<div class='card-header' id='promocionMix'>"+
                                        "<h2 class='mb-0'>"+
                                            "<button class='btn btn-link' type='button' data-toggle='collapse' data-target='#Plan"+intIdServicio+"' aria-expanded='false' "+
                                            "aria-controls='Plan"+intIdServicio+"'> Detalles Plan Internet - "+ strNombreServicio +" <i class='fa fa-angle-down'></i>"+
                                            "</button>"+
                                        "</h2>"+
                                    "</div>"+
                                    "<div id='Plan"+intIdServicio+"' class='collapse' aria-labelledby='promocionMix' data-parent='#accordionExample'>"+
                                        "<div class='card-body'>"+
                                            "<div id='contenedor_ver_sector' class='form-group row scrollbar'>   "+
                                                "<div class='form-group col-md-12'>"+
                                                    "<div class='row-md-4' >";
                                                    if ($('#nombrePantalla').val()==='Contrato')
                                                    {
                                  strDivContainerPlan+= "<div class='form-group row-md-12'>"+
                                                            "<div class='custom-control custom-checkbox'>"+
                                                                "<input type='checkbox' onclick='limpiarValoresMix(this);' class='custom-control-input' id='checkedMix' >"+
                                                                "<label class='custom-control-label' for='checkedMix'>Promoción Mix</label>"+
                                                            "</div>"+
                                                        "</div>";
                                                    } 
                                  strDivContainerPlan+= "<div class='form-group row-md-12'>"+
                                                            "<label for='observacion_inactivar' class='col-sm-4 col-form-label'>Código Mensualidad:</label>  "+
                                                            "<div class='col-sm-12'>";
                                                            if ($('#nombrePantalla').val()==='Contrato')
                                                            {
                                           strDivContainerPlan+="<input type='text' class='form-control input-sm' id='codigoMens_"+intContador+"' name='codigoMens[MENS]["+intIdServicio+"][]' onChange='validaDatosIngresados(this)' placeholder='Código Mensualidad'>";
                                                            }
                                                            if ($('#nombrePantalla').val()==='Adendum')
                                                            {
                                           strDivContainerPlan+="<input type='text' class='form-control input-sm' id='codigoMens_"+intContador+"' name='codigoMens[MENS]["+intIdServicio+"][]' onChange='validaDatosIngresados(this)' placeholder='Código Mensualidad' value ='"+strCodigoMens+"' disabled>";
                                                            }
                                           strDivContainerPlan+="<input type='hidden' id='codigoMensVal_"+intContador+"' name='codigoEval' value='V'> "+
                                                                "<input type='hidden' id='codigoCheckMix' name='codigoCheckMix' value=''> "+
                                                                "<input type='hidden' id='codigoServMix' name='codigoServMix' value=''> "+
                                                                "<input type='hidden' id='idTipoPromoMix' name='idTipoPromoMix' value=''> "+
                                                                "<input type='hidden' id='codigoMixVal' name='codigoMixVal' value=''> "+
                                                                "<input type='hidden' id='validado' name='validado' value=''> "+
                                                                "<input type='hidden' id='mostrarResumen' name='mostrarResumen' value=''> "+
                                                                "<div id='divSuccesMens_"+intContador+"'>"+
                                                                "</div>"+
                                                            "</div>"+
                                                        "</div>"+
                                                        "<input type='hidden' id='intIdServicio_"+intContador+"' name='idServicio' value='"+intIdServicio+"'> "+
                                                        "<input type='hidden' id='intIdProducto_"+intContador+"' name='intIdProducto' value='"+intIdProducto+"'> "+
                                                        "<input type='hidden' id='strTipoProducto_"+intContador+"' name='strTipoProducto' value='"+strTipoServicio+"'> "+
                                                        "<div class='form-group row-md-12'>"+
                                                            "<label for='observacion_inactivar' class='col-sm-4 col-form-label'>Código Instalación:</label>  "+
                                                            "<div class='col-sm-12'>";
                                                            if ($('#nombrePantalla').val()==='Contrato')
                                                            {
                                           strDivContainerPlan+="<input type='text' class='form-control input-sm' id='codigoIns_"+intContador+"' name='codigoIns[INS]["+intIdServicio+"][]' onChange='validaDatosIngresados(this)' placeholder='Código Instalación'>";
                                                            }
                                                            if ($('#nombrePantalla').val()==='Adendum')
                                                            {
                                           strDivContainerPlan+="<input type='text' class='form-control input-sm' id='codigoIns_"+intContador+"' name='codigoIns[INS]["+intIdServicio+"][]' onChange='validaDatosIngresados(this)' placeholder='Código Instalación' value ='"+strCodigoIns+"' disabled>";
                                                            }
                                           strDivContainerPlan+="<input type='hidden' id='codigoInsVal_"+intContador+"' name='codigoInsVal' value='V'> "+
                                                                "<div  id='divSuccesIns_"+intContador+"'>"+
                                                                "</div>"+
                                                            "</div>"+
                                                        "</div>"+
                                                        "<div class='form-group row-md-12'>"+
                                                            "<label for='observacion_inactivar' class='col-sm-4 col-form-label'>Código Ancho de Banda:</label>    "+
                                                            "<div class='col-sm-12'>  ";
                                                            if ($('#nombrePantalla').val()==='Contrato')
                                                            {
                                           strDivContainerPlan+="<input type='text' class='form-control input-sm' id='codigoBw_"+intContador+"' name='codigoBw[BW]["+intIdServicio+"][]' onChange='validaDatosIngresados(this)' placeholder='Código Ancho de Banda'>";
                                                            }
                                                            if ($('#nombrePantalla').val()==='Adendum')
                                                            {
                                           strDivContainerPlan+="<input type='text' class='form-control input-sm' id='codigoBw_"+intContador+"' name='codigoBw[BW]["+intIdServicio+"][]' onChange='validaDatosIngresados(this)' placeholder='Código Ancho de Banda' value ='"+strCodigoBw+"' disabled>";
                                                            }
                                           strDivContainerPlan+="<input type='hidden' id='codigoBwVal_"+intContador+"' name='codigoBwVal' value='V'> "+
                                                                "<div id='divSuccesBw_"+intContador+"'>"+
                                                                "</div>"+
                                                            "</div>"+
                                                        "</div>"+
                                                    "</div>"+
                                                "</div>"+
                                            "</div>"+
                                        "</div>"+
                                    "</div>"+
                                "</div>"+
                            "</div>"+
                        "</div>"+
                    "</div>"+
                "</div>";

                strDivResumenPlan += 
                "<ul>"+
                "<li><b>Plan Internet "+ strNombreServicio +"</b></li>"+
                "</ul> "+
                "<div id='divResumen_"+intContador+"' >"+
                    "<p id='resumenInicial_"+intContador+"' > No se registraron códigos promocionales."+
                    "</p>"+
                    "<p id='resumenMens_"+intContador+"' >"+
                    "</p>"+
                    "<p id='resumenIns_"+intContador+"' >"+
                    "</p>"+
                    "<p id='resumenBw_"+intContador+"' >"+
                    "</p>"+
                "</div>";
            }
            else if (strTipoServicio === 'producto'  && strEstado !== 'Eliminado'  && strEstado !== 'Rechazada' && strEstado!=='Anulado' && strEstado !== 'Cancelado' && ( ($('#nombrePantalla').val() === 'Adendum' && strTipoAdendum == null) || $('#nombrePantalla').val() === 'Contrato'))
            {
                // datos de los producctos
                 strNombreServicio = data.servicios[intContador].nombreProducto;
        
                strDivContainerProd+=
                "<div class='container shadow-sm p-4 mb-4 bg-white'>"+
                    "<div class='form-group row'>"+
                        "<div class='col-md-12'>"+
                            "<div class='accordion container-fluid' id='accordionExample1'> "+
                                "<div class='card' style='border-bottom:1px solid rgba(0,0,0,.125)'>"+
                                    "<div class='card-header' id='promocionMix'>"+
                                        "<h2 class='mb-0'>"+
                                            "<button class='btn btn-link' type='button' data-toggle='collapse' data-target='#Producto"+intIdServicio+"' aria-expanded='false' "+
                                            "aria-controls='Producto"+intIdServicio+"'> Detalles Producto - "+ strNombreServicio +" <i class='fa fa-angle-down'></i>"+
                                            "</button>"+
                                        "</h2>"+
                                    "</div>"+
                                    "<div id='Producto"+intIdServicio+"' class='collapse' aria-labelledby='promocionMix' data-parent='#accordionExample'>"+
                                        "<div class='card-body'>"+
                                            "<div id='contenedor_ver_sector' class='form-group row scrollbar'>   "+
                                                "<div class='form-group col-md-12'>"+
                                                    "<div class='row-md-4' >"+
                                                        "<div class='form-group row-md-12'>"+
                                                            "<label for='observacion_inactivar' class='col-sm-4 col-form-label'>Código Mensualidad:</label>  "+
                                                            "<div class='col-sm-12'>";
                                                            if ($('#nombrePantalla').val()==='Contrato')
                                                            {
                                           strDivContainerProd+="<input type='text' class='form-control input-sm' id='codigoMens_"+intContador+"' name='codigoMens[MENS]["+intIdServicio+"][]' onChange='validaDatosIngresados(this)' placeholder='Código Mensualidad'>";
                                                            }
                                                            if ($('#nombrePantalla').val()==='Adendum')
                                                            {
                                           strDivContainerProd+="<input type='text' class='form-control input-sm' id='codigoMens_"+intContador+"' name='codigoMens[MENS]["+intIdServicio+"][]' onChange='validaDatosIngresados(this)' placeholder='Código Mensualidad' value ='"+strCodigoMens+"' disabled>";
                                                            }
                                           strDivContainerProd+="<input type='hidden' id='codigoMensVal_"+intContador+"' name='codigoEval' value='V'> "+
                                                                "<input type='hidden' id='validadoP' name='validadoP' value=''> "+
                                                                "<div id='divSuccesMens_"+intContador+"'>"+
                                                                "</div>"+
                                                            "</div>"+
                                                        "</div>"+
                                                        "<input type='hidden' id='intIdServicio_"+intContador+"' name='idServicio' value='"+intIdServicio+"'> "+
                                                        "<input type='hidden' id='intIdProducto_"+intContador+"' name='intIdProducto' value='"+intIdProducto+"'> "+
                                                        "<input type='hidden' id='strTipoProducto_"+intContador+"' name='strTipoProducto' value='"+strTipoServicio+"'> "+
                                                        "<div class='form-group row-md-12'>"+
                                                            "<label for='observacion_inactivar' class='col-sm-4 col-form-label'>Código Instalación:</label>  "+
                                                            "<div class='col-sm-12'>";
                                                            if ($('#nombrePantalla').val()==='Contrato')
                                                            {
                                           strDivContainerProd+="<input type='text' class='form-control input-sm' id='codigoIns_"+intContador+"' name='codigoIns[INS]["+intIdServicio+"][]' onChange='validaDatosIngresados(this)' placeholder='Código Instalación'>";
                                                            }
                                                            if ($('#nombrePantalla').val()==='Adendum')
                                                            {
                                           strDivContainerProd+="<input type='text' class='form-control input-sm' id='codigoIns_"+intContador+"' name='codigoIns[INS]["+intIdServicio+"][]' onChange='validaDatosIngresados(this)' placeholder='Código Instalación' value ='"+strCodigoIns+"' disabled>";
                                                            }
                                           strDivContainerProd+="<input type='hidden' id='codigoInsVal_"+intContador+"' name='codigoInsVal' value='V'> "+
                                                                "<div id='divSuccesIns_"+intContador+"'>"+
                                                                "</div>"+
                                                            "</div>"+
                                                        "</div>"+
                                                    "</div>"+
                                                "</div>  "+
                                            "</div>"+
                                        "</div>"+
                                    "</div>"+
                                "</div>"+
                            "</div>"+
                        "</div>"+
                    "</div>"+
                "</div>";

                strDivResumenProd +=
                "<ul>"+
                "<li><b>Producto "+ strNombreServicio +"</b></li>"+
                "</ul>"+
                "<div id='divResumen_"+intContador+"' >"+
                    "<p id='resumenInicial_"+intContador+"' > No se registraron códigos promocionales."+
                    "</p>"+
                    "<p id='resumenMens_"+intContador+"' >"+
                    "</p> "+
                    "<p id='resumenIns_"+intContador+"' >"+
                    "</p> "+
                    "<p id='resumenBw_"+intContador+"' >"+
                    "</p>"+
                "</div>";
           }
         }
         strDivContainerGeneral=strDivContainerPlan + strDivContainerProd;
         strDivContainerResumen=strDivResumenPlan + strDivResumenProd;
        $("#divContainer").html(strDivContainerGeneral);
        $("#divResumen").html(strDivContainerResumen);

     },
     error: function () {
         $('#modalMensajes .modal-body').html("No se pudieron cargar los Motivos. Por favor consulte con el Administrador");
         $('#modalMensajes').modal({show: true});
     }
     });

     $(".spinner_btnValidarCodigoPromocion").hide();
});


  /**
  * Realiza la llamada a la función Ajax que valida los códigos promocionales por Mensualidad y Ancho de Banda.
  *    
  * @author Katherine Yager <kyager@telconet.ec>
  * @version 1.0 24-11-2020
  * @since 1.0
  */
        
    function validaCodigosPromo()
    {
        var promMens              = '';
        var promMensId            = '';
        var intIdServicio         = '';
        var intIdProducto         = '';
        var strTipoProducto       = '';
        var intIdPunto            = '';
        var intIdUltimaMilla      = '';
        var strGrupoPromo         = '';
        var strTipoPromo          = '';
        var strCheckMix           = 'N';
        var strMensaje            = '';
        var strServicios          = '';
        var strComa               = '';
        var strFormaPago          = $('#infocontratotype_formaPagoId').val();
        var strFormaPagoNombre    = $('#infocontratotype_formaPagoId option:selected').text();
        var strbancoTipoCuentaId  = '';
        var strStringFormaPago    = '';
        var strEsContrato         = 'S';
        var strServiciosMix       = 'N';
        var promMensMix           = '';
        var strResumeHtml         = '';
        var strResumeHtmlResult   = '';
        var strIdResumen          = '';
        
         $('#codigoMensMix').val(strCheckMix);
         $("#validado").val(''); 
         $("#validadoP").val(''); 
        
        if(strFormaPago=='3')
        {
            strbancoTipoCuentaId = $('#infocontratoformapagotype_bancoTipoCuentaId').val();
            strStringFormaPago='Tipo:'+strFormaPagoNombre+'|Valor:'+strbancoTipoCuentaId+'|';
        }
        else
        {
            strStringFormaPago='Tipo:'+strFormaPagoNombre+'|Valor:'+strFormaPago+'|';
        }

        if ($('#checkedMix').is(':checked')) 
        {
            strCheckMix='S';
           
        }
        else
        {
            strCheckMix='N';
            
        }
        
       strBandRemove='';
        $(".spinner_btnValidarCodigoPromocion").show();
        $('input[name^="codigoMens"]').each(function() {
          
             promMens           = $(this).val(); 
             promMensId         = $(this).attr('id');
             var intPos         = promMensId.slice(11);
             var arrayPos       = promMensId.split('_');
             var strNombreCod   = arrayPos[0];
           
             intIdServicio       = $("#intIdServicio_"+intPos).val();
             intIdProducto       = $("#intIdProducto_"+intPos).val();
             strTipoProducto     = $("#strTipoProducto_"+intPos).val();
               
            if (strBandRemove=='')
            {
               $("#resumenMens_"+intPos).remove();

            }
              $("#divResumen_"+intPos).append("<p  id='resumenMens_"+intPos+"' > </p> ");
             
            if(typeof intIdServicio!== "undefined")
            {
                strServicios+=strComa+intIdServicio;
                strComa          = ','; 
            } 
             
            if (strTipoProducto=='producto')
            {
                strTipoPromo='PROM_MPRO';
            }
            else if (strTipoProducto=='plan')
            {
                 strTipoPromo='PROM_MPLA';
                 
                 if (strCheckMix=='S' && promMens=='' )
                 {

                     $("#"+strNombreCod+"Val_"+intPos).val('Mix');
                     strMensaje='Seleccionó Promoción Mix, debe ingresar el código Mix.';
                     $('#modalMensajes .modal-body').html(strMensaje);
                     $('#mensaje_validaciones').removeClass('campo-oculto').html(strMensaje);  
                     $('#modalMensajes').modal({show: true});
                    
                 }
                
            }    
            
            if(strTipoPromo=='PROM_MPLA' || strTipoPromo=='PROM_MPRO')
            {
                strGrupoPromo='PROM_MENS';
            } 

            if(strCheckMix=='S')
            {
                strTipoPromo      = 'PROM_MIX';
                strGrupoPromoMix  = 'PROM_MENS';
                strCodigoPromoMix =  promMens;
                intIdProductoMix  =  intIdProducto;
                intPosMix         =  intPos;
                strNombreCodMix   =  strNombreCod;
                intIdServicioMix   =  intIdServicio;
            }
      
            if(promMens!='')
            {
                strCodigoPromo=promMens;
            }
           
            if (strCodigoPromo!='')
            {
                var parametros = {
               "strGrupoPromocion": strGrupoPromo,
               "strTipoPromocion" : strTipoPromo,
               "strCodigo"        : strCodigoPromo,
               "intIdServicio"    : intIdServicio,
               "intIdPunto"       : intIdPunto,
               "intIdPlan"        : intIdProducto,
               "intIdProducto"    : intIdProducto,
               "intIdUltimaMilla" : intIdUltimaMilla,
               "strTipoProceso"   : 'NUEVO',
               "strFormaPago"     : strStringFormaPago,
               "strEsContrato"    : strEsContrato
               };
               
               strServiciosMix =  validaCodigoPromociones(parametros,intPos,strGrupoPromo,strNombreCod);
               
               strResumeHtml = strServiciosMix.strResumeHtml;
            
               $('#codigoMens_'+intPos).attr('name',"codigoMens[MENS]["+intIdServicio+"]["+strServiciosMix.strIdTipoPromocion+"-"+strServiciosMix.strNombrePromocion+"]");
            
               $('[name="codigoMens"]').attr('value', strServiciosMix.strIdTipoPromocion);
                     
                  
                if (strServiciosMix.strServiciosMix !== null && strServiciosMix.strServiciosMix !== '' && strCheckMix=='S')
                {
                    strIdResumen='resumenMens';
                    promMensMix=promMens; 
                    promMensMixMensaje=strServiciosMix.strMensaje; 
                    $('#codigoServMix').val(strServiciosMix.strServiciosMix);
                    $('#idTipoPromoMix').val(strServiciosMix.strIdTipoPromocion+"-"+strServiciosMix.strNombrePromocion);
                    $('#codigoCheckMix').val(strCheckMix);
                    $('#codigoMixVal').val(promMensMix);
                }
              
            }
            else
            {
                $("#messageIdMens_"+intPos ).remove();
            }
            
                if(promMensMix!='')
                {
                    
                    $("#"+promMensId).val(promMensMix);
                    $("#messageIdMens_"+intPos).remove();
                    $("#divSuccesMens_"+intPos).removeClass("text-danger");
                    $("#divSuccesMens_"+intPos).addClass("text-success");
                    $("#"+strNombreCod+"Val_"+intPos).val('S'); 
                    $("#divSuccesMens_"+intPos).append( "<p id='messageIdMens_"+intPos+"'>"+promMensMixMensaje+"</p>" );
                    $("#"+strIdResumen+"_"+intPos).append(strResumeHtml);
                   
                   
                }  
                if (strCheckMix=='S')
                {
                      strBandRemove='';
                      $("#resumenInicial_"+intPos).remove();
                }
              
        });
        
        strResumeHtml='';
        strBandRemove='';
        $('input[name^="codigoIns"]').each(function() {
              
             promIns            = $(this).val();
             promInsId          = $(this).attr('id');
             var intPos         = promInsId.slice(10);
             var arrayPos       = promInsId.split('_');
             var strNombreCod   = arrayPos[0];
             intIdServicio      =  $("#intIdServicio_"+intPos).val();
             intIdProducto      =  $("#intIdProducto_"+intPos).val();
             strTipoProducto    =  $("#strTipoProducto_"+intPos).val();
             
            if (strBandRemove=='')
            {
               $("#resumenIns_"+intPos).remove();
               $("#divResumen_"+intPos).append("<p  id='resumenIns_"+intPos+"' > </p> ");
               strBandRemove='S';
            }

            if(typeof intIdServicio!== "undefined")
            {
                strServicios+=strComa+intIdServicio;
                strComa          = ','; 
            }          

            strTipoPromo='PROM_INS';
        
            strGrupoPromo='PROM_INS';
            
            if(promIns!='')
            {
                strCodigoPromo=promIns;
            }
            
            if (strCodigoPromo!='')
            {
                var parametros = {
                  "strGrupoPromocion": strGrupoPromo,
                  "strTipoPromocion" : strTipoPromo,
                  "strCodigo"        : strCodigoPromo,
                  "intIdServicio"    : intIdServicio,
                  "intIdPunto"       : intIdPunto,
                  "intIdPlan"        : intIdProducto,
                  "intIdProducto"    : intIdProducto,
                  "intIdUltimaMilla" : intIdUltimaMilla,
                  "strTipoProceso"   : 'NUEVO',
                  "strFormaPago"     : strStringFormaPago,
                  "strEsContrato"    : strEsContrato
               };
                
                   strResumeHtmlResult = validaCodigoPromociones(parametros,intPos,strGrupoPromo,strNombreCod);
                    
                   strResumeHtml += strResumeHtmlResult.strResumeHtml;
                   $('#codigoIns_'+intPos).attr('name',"codigoIns[INS]["+intIdServicio+"]["+strResumeHtmlResult.strIdTipoPromocion+"-"+strResumeHtmlResult.strNombrePromocion+"]");
                   
                  
            }
            else
            {
                $("#messageIdIns_"+intPos ).remove();
            }
            
        });
            
        strResumeHtml='';
        strBandRemove='';
        
        $('input[name^="codigoBw"]').each(function() {
              
             promBw             = $(this).val();
             promBwId           = $(this).attr('id');
             var intPos         = promBwId.slice(9);
             var arrayPos       = promBwId.split('_');
             var strNombreCod   = arrayPos[0];
             intIdServicio       =  $("#intIdServicio_"+intPos).val();
             intIdProducto       =  $("#intIdProducto_"+intPos).val();
             strTipoProducto     =  $("#strTipoProducto_"+intPos).val();
             
             if (strBandRemove=='')
             {
               $("#resumenBw_"+intPos).remove();
               $("#divResumen_"+intPos).append("<p  id='resumenBw_"+intPos+"' > </p> ");
               strBandRemove='S';
             }
             
             if(typeof intIdServicio!== "undefined")
             {
                 strServicios+=strComa+intIdServicio;
                 strComa          = ','; 
             } 
       
            strTipoPromo='PROM_BW';
        
            strGrupoPromo='PROM_BW';
            
            if(promBw!='')
            {
                strCodigoPromo=promBw;
            }
           
            if (strCodigoPromo!='')
            {
                var parametros = {
                  "strGrupoPromocion": strGrupoPromo,
                  "strTipoPromocion" : strTipoPromo,
                  "strCodigo"        : strCodigoPromo,
                  "intIdServicio"    : intIdServicio,
                  "intIdPunto"       : intIdPunto,
                  "intIdPlan"        : intIdProducto,
                  "intIdProducto"    : intIdProducto,
                  "intIdUltimaMilla" : intIdUltimaMilla,
                  "strTipoProceso"   : 'NUEVO',
                  "strFormaPago"     : strStringFormaPago,
                  "strEsContrato"    : strEsContrato
               };
               
                 strResumeHtmlResult = validaCodigoPromociones(parametros,intPos,strGrupoPromo,strNombreCod);
                 strResumeHtml += strResumeHtmlResult.strResumeHtml;
                 $('#codigoBw_'+intPos).attr('name',"codigoBw[BW]["+intIdServicio+"]["+strResumeHtmlResult.strIdTipoPromocion+"-"+strResumeHtmlResult.strNombrePromocion+"]");
                   
            }
            else
            {
                $("#messageIdBw_"+intPos ).remove();
            }
           
        });    
         $(".spinner_btnValidarCodigoPromocion").hide();

    }

/**
  * Realiza la llamada a la función Ajax que valida los códigos promocionales por Mensualidad y Ancho de Banda.
  *    
  * @author Katherine Yager <kyager@telconet.ec>
  * @version 1.0 24-11-2020
  * @since 1.0
  */
 function validaCodigoPromociones(parametros,intPos,strGrupoPromo,strNombreCod)
 {

  var codigoPromo             =  parametros['strCodigo'];
  var strMensajeMix           =  '';
  var strResumeHtml           =  '';
  var strResumeHtmlMix        =  '';
  var strIdTipoPromocionRet   =  '';
  var strNombrePromocionRet   =  '' ;
  var strCheckMix             =  '' ;

  var strServiciosMix = 'N';
  
         if ($('#checkedMix').is(':checked')) 
        {
            strCheckMix='S';
           
        }
        else
        {
            strCheckMix='N';
            
        }
        
        $.ajax({
           data: parametros,
           url: urlCodigoPromocion,
           type: 'post',
           async: false,
           success: function (response) {
               var strAplica          = response.strAplica;
               var strMensaje         = response.strMensaje;
                   strServiciosMix    = response.strServiciosMix;
               var strIdTipoPromocion = response.strIdTipoPromocion;
               var strNombrePromocion = response.strNombrePromocion;               
   
                if (strServiciosMix!='' && strCheckMix=='S')
                {
                    strServiciosMix2 = strServiciosMix;
                }
                else
                {
                    strServiciosMix2 = 'N';
                }
               strServiciosMix = strServiciosMix2;
               strIdTipoPromocionRet=strIdTipoPromocion;
               strNombrePromocionRet=strNombrePromocion;
               if (strAplica=='N')
               {                        
                   if (strGrupoPromo=='PROM_MENS')
                   {
                        $("#messageIdMens_"+intPos ).remove();  
                        $("#codigoMens_"+intPos).val('');
                        if ($("#"+strNombreCod+"Val_"+intPos).val()!='Mix')
                        {
                           $("#"+strNombreCod+"Val_"+intPos).val('V'); 
                        }
                        
                        $("#divSuccesMens_"+intPos).removeClass("text-success");
                        $("#divSuccesMens_"+intPos).addClass("text-danger");
                        $("#divSuccesMens_"+intPos).append( "<p id='messageIdMens_"+intPos+"'>"+strMensaje+' Código ingresado: ' +codigoPromo+ "</p>" );
                   }
                   else if (strGrupoPromo=='PROM_INS')
                   {
                        $("#messageIdIns_"+intPos ).remove();
                        $("#codigoIns_"+intPos).val('');
                        $("#"+strNombreCod+"Val_"+intPos).val('V'); 
                        $("#divSuccesIns_"+intPos).removeClass("text-success");
                         
                        $("#divSuccesIns_"+intPos).addClass("text-danger");
                        $("#divSuccesIns_"+intPos).append( "<p id='messageIdIns_"+intPos+"'>"+strMensaje+' Código ingresado: ' +codigoPromo+"</p>" );
                 
                   }
                   else if (strGrupoPromo=='PROM_BW')
                   {
                        $("#messageIdBw_"+intPos ).remove();
                        $("#codigoBw_"+intPos).val('');
                        $("#"+strNombreCod+"Val_"+intPos).val('V'); 
                        $("#divSuccesBw_"+intPos).removeClass("text-success");
                        
                        $("#divSuccesBw_"+intPos).addClass("text-danger");
                        $("#divSuccesBw_"+intPos).append( "<p id='messageIdBw_"+intPos+"'>"+strMensaje+' Código ingresado: ' +codigoPromo+"</p>" );
                   }
                  
               }
               else
               {
                   $("#validado").val('S'); 
                   $("#validadoP").val('S'); 
                   $("#resumenInicial_"+intPos).remove();
                   if (strGrupoPromo=='PROM_MENS')
                   {
                       strMensajeMix=strMensaje;
                       $("#messageIdMens_"+intPos).remove();
                       $("#divSuccesMens_"+intPos).removeClass("text-danger");
                       $("#divSuccesMens_"+intPos).addClass("text-success");
                       $("#"+strNombreCod+"Val_"+intPos).val('S'); 
                       $("#divSuccesMens_"+intPos).append( "<p id='messageIdMens_"+intPos+"'>"+strMensaje+"</p>" );
                       
                            if ($('#checkedMix').is(':checked')) 
                            {
                                strCheckMix='S';

                            }
      
           
                       strResumeHtml = resumenCodigosPromocionales(codigoPromo, response.strNombrePromocion,strGrupoPromo,intPos,strCheckMix);
                     
                   }
                   else if (strGrupoPromo=='PROM_INS')
                   {
                       $("#messageIdIns_"+intPos).remove();
                       $("#divSuccesIns_"+intPos).removeClass("text-danger");
                       $("#divSuccesIns_"+intPos).addClass("text-success");
                       $("#"+strNombreCod+"Val_"+intPos).val('S'); 
                       $("#divSuccesIns_"+intPos).append( "<p  id='messageIdIns_"+intPos+"'>"+strMensaje+"</p>" );
                       strResumeHtml = resumenCodigosPromocionales(codigoPromo, response.strNombrePromocion,strGrupoPromo,intPos,'');
                   }
                   else if (strGrupoPromo=='PROM_BW')
                   {
                       $("#messageIdBw_"+intPos).remove();
                       $("#divSuccesBw_"+intPos).removeClass("text-danger");
                       $("#divSuccesBw_"+intPos).addClass("text-success");
                       $("#"+strNombreCod+"Val_"+intPos).val('S'); 
                       $("#divSuccesBw_"+intPos).append( "<p  id='messageIdBw_"+intPos+"'>"+strMensaje+"</p>" );
                       strResumeHtml = resumenCodigosPromocionales(codigoPromo, response.strNombrePromocion,strGrupoPromo,intPos,'');
                   }
                   
               }
               
           },
           failure: function (response) {
               $('#modalMensajes .modal-body').html('Existe un error: ' + response);
               $('#modalMensajes').modal({show: true});
           }
       });
       
        var arrayResult     = {
                               "strServiciosMix"   : strServiciosMix,
                               "strMensaje"        : strMensajeMix,
                               "strResumeHtml"     : strResumeHtml,
                               "strIdTipoPromocion": strIdTipoPromocionRet,
                               "strNombrePromocion":strNombrePromocionRet,
                               "strResumeHtmlMix"  :strResumeHtmlMix}; 
             
    return  arrayResult;
 }
 
 /**
  * Valida si los campos de lso códigos tienen información para validación previa y disable de botón Guardar
  *    
  * @author Katherine Yager <kyager@telconet.ec>
  * @version 1.0 25-11-2020
  * @since 1.0
  */
 function validaDatosIngresados(codigo)
 {
     var intIdCodigoPromo    =  $(codigo).attr('id');
     var arrayPos            = intIdCodigoPromo.split('_');
     var strNombreCod        = arrayPos[0];
     var strPosCod           = arrayPos[1];
     
     $("#"+strNombreCod+"Val_"+strPosCod).val('N');
     
     if ($("#"+strNombreCod+"_"+strPosCod).val()=='')
     {
          $("#"+strNombreCod+"Val_"+strPosCod).val('V');
     }

 }
 
  /**
  * Realiza el resemen de todos los códigos promocionales que aplican a los servicos.
  *    
  * @author Katherine Yager <kyager@telconet.ec>
  * @version 1.0 07-12-2020
  * @since 1.0
  */
 function resumenCodigosPromocionales(strCodigoPromo,strNombrePromocion,strTipoPromo,intPos , strMix)
 {
     var strTipoPromoNombre='';
     var strIdResumen='';
     if(strTipoPromo=='PROM_MENS')
     {
     
         strTipoPromoNombre='Mensualidad';
         strIdResumen='resumenMens';
     }
     else if (strTipoPromo=='PROM_INS')
     {
         strTipoPromoNombre='Instalación';
         strIdResumen='resumenIns';
     }
     else if (strTipoPromo=='PROM_BW')
     {
         strTipoPromoNombre='Ancho de Banda';
         strIdResumen='resumenBw';
     }
     
     var strResumeHtml=
             "<b> <span style='font-size: 20px; color: Dodgerblue;'>"+
             "<i class='fa fa-check-circle' aria-hidden='true'></i> "+
             "</span></b> Código " +strTipoPromoNombre+ " "+strCodigoPromo+ ","+
             "cliente aplicará a promoción "+strNombrePromocion+". </br> ";

    
        if(strMix=='N' || strMix=='' )
        {  
            $("#"+strIdResumen+"_"+intPos).append(strResumeHtml);
        }
       

    
     return strResumeHtml;
 }
 
 
 /**
  * Limpia los valores cuando se deschequea la opción de Mix.
  *    
  * @author Katherine Yager <kyager@telconet.ec>
  * @version 1.0 11-12-2020
  * @since 1.0
  */
 function limpiarValoresMix()
 {

    if (!$('#checkedMix').is(':checked')) 
    {
        $('input[name^="codigoMens"]').each(function() {

         promMens           = $(this).val(); 
         promMensId         = $(this).attr('id');

         $("#"+promMensId).val('');
         });
    }
    
 }
 