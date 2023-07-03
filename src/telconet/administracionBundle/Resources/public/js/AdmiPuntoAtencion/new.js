$(document).ready(function(){
   
    
    $("#btnGuardar").click(function(){
       var strMensaje = confirm("¿Está seguro que desea Guardar un punto de atención?");
       
       if(strMensaje===true)
       {
          var strNombrePuntoAtencion = $("#strNombrePuntoAtencion").val();
          if(strNombrePuntoAtencion.length>100)
          {
              alert("Ha superado el máximo de caracteres permitidos: 100");
              return false;
          }
           
          $.ajax({
            url: urlGuardarPuntoAtencion,
            type: 'POST',
            data: {
                "strNombrePuntoAtencion": $("#strNombrePuntoAtencion").val()
            },
            success: function (response) {
                if(response.strStatus==="OK")
                {
                   alert(response.strMensaje);
                   $("#strNombrePuntoAtencion").val("");
                   document.location = "../punto_atencion";
                }
                else
                {
                    alert(response.strMensaje);
                }
                
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert("Se presento un error al guardar un punto de atención");
            }
            
          });
        
       }
       
       
        
        
    });
    
    
});