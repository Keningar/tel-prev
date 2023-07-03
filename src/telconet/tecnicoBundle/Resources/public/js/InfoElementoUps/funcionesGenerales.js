function convertirTextoEnMayusculas(idTexto)
{
    var strTexto      = document.getElementById(idTexto).value;
    var strMayusculas = strTexto.toUpperCase(); 
    
    document.getElementById(idTexto).value = strMayusculas;
}

function validarCaracteresEspeciales(e)
{
    var k = (document.all) ? e.keyCode : e.which;
    
    if (k==8 || k==0)
    {
        return true;
    }
    
    var patron = /[a-zA-Z0-9-]/;
    var n = String.fromCharCode(k);
    
    return patron.test(n);
}


function validarSoloLetras(e)
{
    var k = (document.all) ? e.keyCode : e.which;
    
    if (k==8 || k==0)
    {
        return true;
    }
    
    var patron = /[a-zA-Z]/;
    var n = String.fromCharCode(k);
    
    return patron.test(n);
}


function validarTiempoAcceso(e)
{
    var charCode = (e.which) ? e.which : e.keyCode;

    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
    {
        return false;
    }

    return true;
}


function validarSoloNumeros(e)
{
    var k = (document.all) ? e.keyCode : e.which;
    
    if (k==8 || k==0)
    {
        return true;
    }
    
    var patron = /[0-9]/;
    var n = String.fromCharCode(k);
    
    return patron.test(n);
}
