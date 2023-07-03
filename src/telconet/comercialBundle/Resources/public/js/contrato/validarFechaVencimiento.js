var meses=["Seleccione","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
$(document).ready(function(){
    var intAnio = $("#infocontratoformapagotype_anioVencimiento").val();
    var TodayDate = new Date();
    var mesDate = TodayDate.getMonth()+1;
    
    $("#infocontratoformapagotype_anioVencimiento").find('option:eq(0)').prop('disabled', false);
    if(TodayDate.getFullYear() == intAnio) //Cuando recien se hace GET
    { //Debe borrar los meses sobrantes
        
        $("#infocontratoformapagotype_mesVencimiento").find("option").each(function() {
            if ( Number($(this).val()) < mesDate+1 ) {
                $(this).remove();
            }
        });
        //$("#infocontratoformapagotype_mesVencimiento").prepend(new Option(meses[0],""));
    }

    $("#infocontratoformapagotype_anioVencimiento").change(function(){ //Cuando cambio debo borrar si es el año en curso
        var intAnio = $("#infocontratoformapagotype_anioVencimiento").val();
        if(TodayDate.getFullYear() == intAnio)
        {
            
            $("#infocontratoformapagotype_mesVencimiento").find('option').each(function() {
                if ( Number($(this).val()) < mesDate+1 ) {
                    $(this).remove();
                }
            });
            //$("#infocontratoformapagotype_mesVencimiento").prepend(new Option(meses[0],""));
        }
        else{ //
            var i;
            var mesEscojido = $("#infocontratoformapagotype_mesVencimiento").val();
            $("#infocontratoformapagotype_mesVencimiento").empty();
            $("#infocontratoformapagotype_mesVencimiento").append(new Option(meses[0],""));
            for(i=1;i<=12;i++)
            {     
                $("#infocontratoformapagotype_mesVencimiento").append(new Option(meses[i],i));
            }
            //console.log("Poniendo mes que estaba escojido");
            $("#infocontratoformapagotype_mesVencimiento").val(mesEscojido);
        }
        
    });
});