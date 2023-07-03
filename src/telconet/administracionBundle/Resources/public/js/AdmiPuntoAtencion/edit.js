$(document).ready(function(){
   
    $("#btnEditar").click(function(){
       var strMensaje = confirm("¿Está seguro que desea editar el punto de atención?");
       
       if(strMensaje===true)
       {
           
          var strNombrePuntoAtencion = $("#strNombrePuntoAtencion").val();
          if(strNombrePuntoAtencion.length>100)
          {
              alert("Ha superado el máximo de caracteres permitidos: 100");
              return false;
          }
           
          $.ajax({
            url: urlEditarPuntoAtencion,
            type: 'POST',
            data: {
                "intIdPuntoAtencion": $("#intIdPuntoAtencion").val(),
                "strNombrePuntoAtencion": $("#strNombrePuntoAtencion").val()
            },
            success: function (response) {
                console.log(response.strStatus)
                if(response.strStatus==="OK")
                {
                    alert(response.strMensaje);
                    document.location = "../";
                    
                }
                else
                {
                    alert(response.strMensaje);
                }
                
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert("Se presento un error al editar un punto de atención");
            }
            
          });
        
       }
       
       
        
        
    });
    
    
});


