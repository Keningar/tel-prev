
$(document).ready(function () {

     
    $(".spinner_subirArchivo").hide();
    /**
     * Valida Campos requeridos 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 25-01-2021
     */
    var forms = document.getElementsByClassName('formSubirXml');
    Array.prototype.filter.call(forms, function (form) {
        form.addEventListener('submit', function (event) {
            if (form.checkValidity() === false)
            {
                event.preventDefault();
                event.stopPropagation();
                form.classList.add('was-validated');
                $('#modalMensajes .modal-body').html("Por favor llenar campos requeridos.");
                $('#modalMensajes').modal({show: true});
            } else
            {
                form.classList.add('was-validated');
                if (!validaArchivo())
                {
                    $('#modalMensajes .modal-body').html("Extensión de archivo no válida .Favor verificar que sea formato xml");
                    $('#modalMensajes').modal({show: true});
                } else
                {
                    subirXml();
                }
            }
        }, false);
    });
    
    var formsRpt = document.getElementsByClassName('formSubirRpt');
    Array.prototype.filter.call(formsRpt, function (form) {
        form.addEventListener('submit', function (event) {
            if (form.checkValidity() === false)
            {
                event.preventDefault();
                event.stopPropagation();
                form.classList.add('was-validated');
                $('#modalMensajes .modal-body').html("Por favor llenar campos requeridos.");
                $('#modalMensajes').modal({show: true});
            } else
            {
                form.classList.add('was-validated');
                if (!validaArchivoRpt())
                {
                    $('#modalMensajes .modal-body').html("Extensión de archivo no válida .Favor verificar que sea formato xlsx");
                    $('#modalMensajes').modal({show: true});
                } else
                {
                    subirReporte();
                }
            }
        }, false);
    });    
    
  
    function validaArchivo() 
    {
        var boolRespuesta = false;        
        
        $("input[name='xml_retencion[]']").each(function(){
            var filesObj = $(this).val();
            var ext = filesObj.substring(filesObj.lastIndexOf("."));

            if(ext.toUpperCase() != ".XML")
            {
                boolRespuesta = false;
            }
            else
            {
                boolRespuesta = true;
            }
            
            if (boolRespuesta == false)
            {
                alert("La extensión "+ext+" no es un formato válido.");
            }            
        });
        return boolRespuesta;
    }
    
    function validaArchivoRpt() 
    {
        var strNombreArchivo = $("#rpt_retencion").val();
        var strExtension     = strNombreArchivo.replace(/^.*\./, '');

        if (strNombreArchivo !== null && strExtension === 'xlsx')
        {
            return true;
        }
        return false;
    }

    $("#btnLimpiarForm").click(function () {
        limpiarFormulario();
    });

    function limpiarFormulario() 
    {
        $('#xml_retencion').val("");
    }

    /**
     * Función para subir archivo xml de la retención.
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 25-01-2021
     */
    function subirXml()
    {
        var arrayResponse = null;
        $(".spinner_subirArchivo").show();

        $.ajax({

            data: new FormData(document.getElementById("formSubirXml")),
            contentType: false,
            cache: false,
            processData: false,
            url:  strUrlSubirArchivo,
            type: 'post',           
            success: function (response) {
                $(".spinner_subirArchivo").hide();
                $("#subirArchivo").removeAttr("disabled");
                arrayResponse = response.split("*");
                var strRespuesta    = '';
                var strRespuestaErr = '';
                var cabHtml = '<table class="table"><thead><tr><th class="col-sm-6">Archivo</th><th class="col-sm-6">Detalle</th></tr></thead><tbody>';
              
                if (arrayResponse[1] === "OK")
                {
                    strRespuesta = arrayResponse[0].replace(/[/]/g, '</tbody></table>');
                    strRespuesta = strRespuesta.replace(/[¡]/g, '<tr>');                    
                    strRespuesta = strRespuesta.replace(/[{]/g, '<td>');
                    strRespuesta = strRespuesta.replace(/[}]/g, '</td>');
                    strRespuesta = strRespuesta.replace(/[!]/g, '</tr>');                  
                } 
                else
                {
                    strRespuestaErr = arrayResponse[0].replace(/[/]/g, '</tbody></table>');
                    strRespuestaErr = strRespuestaErr.replace(/[¡]/g, '<tr>');                    
                    strRespuestaErr = strRespuestaErr.replace(/[{]/g, '<td>');
                    strRespuestaErr = strRespuestaErr.replace(/[}]/g, '</td>');
                    strRespuestaErr = strRespuestaErr.replace(/[!]/g, '</tr>');                       
                }
                $('#modalMensajes .modal-body').html(cabHtml+strRespuesta+strRespuestaErr);
                $('#modalMensajes').modal({show: true});
                limpiarFormulario();
                $('#modalMensajes').on('hidden.bs.modal', function () {
                  window.location.href = strUrlIndex;
                })                
            },
            failure: function (response) {
                arrayResponse = response.split("*");
                $(".spinner_subirArchivo").hide();
                $("#subirArchivo").removeAttr("disabled");
                $('#modalMensajes .modal-body').html("Error al cargar archivo xml. Favor verificar.");
                $('#modalMensajes').modal({show: true});            
            }
        });
    }
    
     /**
     * Función para subir reporte de retenciones.
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 07-06-2021
     */
    function subirReporte()
    {
        $(".spinner_subirArchivo").show();
        $.ajax({

            data: new FormData(document.getElementById("formSubirRpt")),
            contentType: false,
            cache: false,
            processData: false,
            url:  strUrlSubirReporte,
            type: 'post',           
            success: function (response) {
                $(".spinner_subirArchivo").hide();
                $("#subirArchivo").removeAttr("disabled");
                
                if (response === "OK")
                {                
                    $('#modalMensajes .modal-body').html('Se procesó con éxito el reporte de retenciones.');
                    $('#modalMensajes').modal({show: true});
                    limpiarFormulario();
                    $('#modalMensajes').on('hidden.bs.modal', function () {
                      window.location.href = strUrlIndex;
                    })                    
                } 
                else
                {                
                    $('#modalMensajes .modal-body').html(response);
                    $('#modalMensajes').modal({show: true});
                }
            },
            failure: function (response) {
                $(".spinner_subirArchivo").hide();
                $("#subirArchivo").removeAttr("disabled");
                $('#modalMensajes .modal-body').html("Error al cargar archivo xls. Favor verificar.");
                $('#modalMensajes').modal({show: true});            
            }
        });
    }   
    
    
    
});
