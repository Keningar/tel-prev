function salirSistema(urlLogout){
    if(confirm("Esta seguro(a) de salir del Sistema ?")){
        window.location = urlLogout; 
    }
}
function busquedaLogin(login)
{
    if(login != "")
    {        
        var pathNameProyecto = "/";
        var pathRutaBusqueda = "inicio/busquedaAvanzada?LoginSearch="+login; 
        
        window.location = pathNameProyecto + pathRutaBusqueda;
        //window.location = "inicio/busquedaAvanzada?LoginSearch="+login; 
    }
}

function comboEmpresaLogeada(valor)
{
    Ext.MessageBox.wait("Cambiando de Empresa...");
    var valueTotal               = valor;    
    var valorIdEmpresa           = "";  
    var valorEmpresa             = "";    
    var valorIdOficina           = "";   
    var valorOficina             = "";    
    var valorIdDepartamento      = "";   
    var valorDepartamento        = "";  
    var valorIdPersonaEmpresaRol = "";
    var valorPrefijoEmpresa      = "";
    var intIdPais                = "";
    var strNombrePais            = "";
    var intIdRegion              = "";
    var strNombreRegion          = "";
    var intIdCanton              = "";
    var strNombreCanton          = "";
    var intIdProvincia           = "";
    var strNombreProvincia       = "";
    var strFacturaElectronico    = "";
    var strNombreEmpresa         = "";
    
    if(valor != "0" && valor != "")
    {
        var arrayValores = valor.split('@@');
        if(arrayValores && arrayValores.length > 3)
        {
            valorIdEmpresa           = arrayValores[0];
            valorEmpresa             = arrayValores[1];
            valorIdOficina           = arrayValores[2];
            valorOficina             = arrayValores[3];
            valorIdDepartamento      = arrayValores[4];
            valorDepartamento        = arrayValores[5];
            valorIdPersonaEmpresaRol = arrayValores[6];
	        valorPrefijoEmpresa      = arrayValores[7];
            intIdPais                = arrayValores[8];
            strNombrePais            = arrayValores[9];
            intIdRegion              = arrayValores[10];
            strNombreRegion          = arrayValores[11];
            intIdCanton              = arrayValores[12];
            strNombreCanton          = arrayValores[13];
            intIdProvincia           = arrayValores[14];
            strNombreProvincia       = arrayValores[15];
            strFacturaElectronico    = arrayValores[16];
            strNombreEmpresa         = arrayValores[17];
        }
    }
    
    $('#global_nombre_oficina').html(valorOficina);
    if(valorOficina=="")
    {
        $('#li_global_nombre_oficina').css("min-width","0px");
    }
    else
    {
        $('#li_global_nombre_oficina').css("min-width","80px");
    }
    
    var pathNameProyecto = "/";
    var pathRutaBusqueda = "inicio/guardarSesionEmpresaAjax";    
    $.ajax({
        type: "POST",
        url: pathNameProyecto + pathRutaBusqueda,
        data: 
        { 
            prefijoEmpresa: valorPrefijoEmpresa,
            IdEmpresa: valorIdEmpresa,
            nombreEmpresa: valorEmpresa, 
            IdOficina: valorIdOficina,
            nombreOficina: valorOficina, 
            IdDepartamento: valorIdDepartamento,
            nombreDepartamento: valorDepartamento,
            IdPersonaEmpresaRol: valorIdPersonaEmpresaRol,
            intIdPais: intIdPais,
            strNombrePais: strNombrePais,
            intIdRegion: intIdRegion,
            strNombreRegion: strNombreRegion,
            intIdCanton: intIdCanton,
            strNombreCanton: strNombreCanton,
            intIdProvincia: intIdProvincia,
            strNombreProvincia: strNombreProvincia,
            strFacturaElectronico: strFacturaElectronico,
            strNombreEmpresa: strNombreEmpresa
        }
    })
    .done(function( msg )
    {
          window.location = "/inicio";
    });
}

function destruirSesion()
{
      if(confirm("Esta seguro(a) de salir del Cliente ?")){
	var permiso = "{{ is_granted('ROLE_147-125') }}" ;
	var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);	
	
	if(boolPermiso)
	{ 		
	    var pathNameProyecto = "/";
	    var pathRutaBusqueda = "inicio/destruirSesionAjax";  		
		
	    Ext.Ajax.request({
		    url: pathNameProyecto + pathRutaBusqueda,
		    method: 'post',
		    success: function(response){
	            var text = response.responseText;
	            if(text == "La sesion del Punto Cliente ha sido eliminada")
	            {                              
	                Ext.Msg.alert('Mensaje', text, function(btn){
			    if(btn=='ok'){
				 Ext.MessageBox.wait("cargando...");
				 window.location = "/inicio";
			    }
			});
	            }
	            else{
	                Ext.Msg.alert('Error ', text);
	            }  
	        },
	        failure: function(result)
	        {
	            Ext.Msg.alert('Error ','Error: ' + result.statusText);
	        }
	    });
	}
	else
		Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
	
      }
}

function verErrorLog()
{
//       if(confirm("Esta seguro(a) de salir del Cliente ?")){
	/*var permiso = "{{ is_granted('ROLE_147-125') }}" ;
	var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);	
	
	if(boolPermiso)
	{ */	
	Ext.MessageBox.wait("Consultando Error Log...");
	    Ext.Ajax.request({
		    url: '/administracion/soporte/consultarErrorLog',
		    method: 'get',
		    success: function(response){
			  Ext.MessageBox.hide();
			  var text = response.responseText;
	          
			  Ext.MessageBox.show({
			    title: 'Error Log',
			    msg: text,
			    buttons: Ext.MessageBox.OK,
			    icon: Ext.MessageBox.INFO
			  });
	           
		    },
		    failure: function(result)
		    {
			Ext.MessageBox.hide();
			Ext.MessageBox.show({
			  title: 'Error',
			  msg: result.statusText,
			  buttons: Ext.MessageBox.OK,
			  icon: Ext.MessageBox.ERROR
			});
		    }
	    });
// 	}
// 	else
// 		Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
// 	
//       }
}

/**
 * Documentacion para funcion setPuntoSesionById
 * Función que permite setear el punto en sesión con el punto cuyo id es el enviado como parámetro.
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.0 12/04/2018
 * @param   int  intIdPunto Id del punto que estará en sesión
 */
function setPuntoSesionById(intIdPunto)
{
    if(confirm("Est\u00E1 seguro(a) de cambiar el punto en sesi\u00F3n ?"))
    {
        var permiso     = "{{ is_granted('ROLE_151-846') }}" ;
        var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
	
        if(boolPermiso)
        {       
            Ext.MessageBox.wait("Cargando...");										

            Ext.Ajax.request({
                url: '/search/ajaxSetPuntoSession',
                method: 'post',
                params: { 
                    idPunto: intIdPunto
                },
                success: function(response){
                    var strRespuesta = response.responseText;

                    if(strRespuesta === "OK")
                    {
                        window.location = "/comercial/punto/"+intIdPunto+"/Cliente/show"; 
                    }else{
                        Ext.MessageBox.hide();
                        Ext.Msg.alert('Error',strRespuesta); 
                    }
                },
                failure: function(result)
                {
                    Ext.MessageBox.hide();
                    Ext.Msg.alert('Error',result.responseText);
                }
            });       
        }
        else
        {
            Ext.Msg.alert('Error ','No tiene permisos para realizar esta acci\u00F3n');
        }	
    }
}

function obtieneCantidadCasosExtranet()
{
    if (document.getElementById("spanCasosExtranetSinTareas"))
    {
        Ext.Ajax.request({
            url: '/soporte/info_caso/getCantidadCasosExtranet',
            method: 'post',
            success: function(response){
                var strRespuesta = JSON.parse(response.responseText);
                if (strRespuesta.casosSinTareas !== undefined) 
                {
                    spanCasosExtranetSinTareas.innerHTML = strRespuesta.casosSinTareas;
                    spanCasosExtranetConTareas.innerHTML = strRespuesta.casosConTareas;
                }
            }
        }); 
    
        setTimeout('obtieneCantidadCasosExtranet()', (minutosConsultaCasosExtranet * 60000));
    }
    
}

window.onload = obtieneCantidadCasosExtranet;

function getCasosExtranet(strTipoConsulta)
{
    window.location.href = '/soporte/info_caso?strOrigen=E&strTipoConsulta='+strTipoConsulta;
}
