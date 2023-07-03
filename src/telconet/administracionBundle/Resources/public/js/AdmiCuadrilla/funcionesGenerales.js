$("input[name=tipoCuadrilla]:radio").change(function () 
{
    var strValorRadio = $("input[name=tipoCuadrilla]:checked").val();
    
    if( strValorRadio == 'zona')
    {
        document.getElementById('campozona').style.display = '';
        document.getElementById('campotarea').style.display = 'none';
        document.getElementById("zonaId").required = true;
        document.getElementById("tareaId").required = false;
    }
    else
    {
        document.getElementById('campozona').style.display = 'none';
        document.getElementById('campotarea').style.display = '';
        document.getElementById("zonaId").required = false;
        document.getElementById("tareaId").required = true;
    }
});


function validarCaracteresEspeciales(e)
{
    var k = (document.all) ? e.keyCode : e.which;
    
    if (k==8 || k==0)
    {
        return true;
    }
    
    var patron = /[a-zA-Z0-9 ]/;
    var n = String.fromCharCode(k);
    
    return patron.test(n);
}


