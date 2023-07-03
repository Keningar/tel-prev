nombre            = document.getElementById("TipoCaso").value;
formulario        = document.getElementById("formulario").value;
var strValorRadio = $("input[name=radioTipoCaso]:checked").val();
if(nombre)
{
    document.getElementById('campoUno').style.display = '';
}
else
{
    document.getElementById('campoUno').style.display = 'none'; 
}
if(strValorRadio == "uno")
{
    if(formulario == "sintoma")
    {
        document.getElementById('telconet_schemabundle_admisintomatype_tipoCasoId').required = true;
    }
    else
    {
        document.getElementById('telconet_schemabundle_admihipotesistype_tipoCasoId').required = true;
    }
}
else
{
    if(formulario == "sintoma")
    {
        document.getElementById('telconet_schemabundle_admisintomatype_tipoCasoId').required = false;
    }
    else
    {
        document.getElementById('telconet_schemabundle_admihipotesistype_tipoCasoId').required = false;
    }
}
$("input[name=radioTipoCaso]:radio").change(function ()
{
    var strValorRadio = $("input[name=radioTipoCaso]:checked").val();
    
    if( strValorRadio == 'uno')
    {
        document.getElementById('campoUno').style.display = '';
        document.getElementById('editTipoCaso').value = "uno";
        if(formulario == "sintoma")
        {
            document.getElementById('telconet_schemabundle_admisintomatype_tipoCasoId').required = true;
        }
        else
        {
            document.getElementById('telconet_schemabundle_admihipotesistype_tipoCasoId').required = true;
        }
    }
    else
    {
        document.getElementById('campoUno').style.display = 'none';
        document.getElementById('editTipoCaso').value = "todos";
        if(formulario == "sintoma")
        {
            document.getElementById('telconet_schemabundle_admisintomatype_tipoCasoId').required = false;
        }
        else
        {
            document.getElementById('telconet_schemabundle_admihipotesistype_tipoCasoId').required = false;
        }
    }
});


function validarCaracteresEspeciales(e)
{
    var k = (document.all) ? e.keyCode : e.which;
    
    if (k==8 || k==0)
    {
        return true;
    }
    
    var patron = /[a-zA-Z0-9()/,-.%' ]/;
    var n = String.fromCharCode(k);
    
    return patron.test(n);
}


