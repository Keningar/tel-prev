    var arrayProdAdicionales = [];
    //Obtiene los productos adicionales
    $.ajax({
        url: urlGetProdAdicionalesCV,
        method: 'GET',
        success: function (data) {
             
            $.each(data.prodAdicionalesCV, function (id,registro) { 
                arrayProdAdicionales.push(registro.nombre);
            });
            
            console.log(arrayProdAdicionales);           
        },
        error: function () {
            alert('NO');
        }
    });
    



var botones = [
    //VER DATA TECNICA DE PRODUCTO NGFIREWALL
    {
        getClass: function(v, meta, rec) 
        {
            let button = 'button-grid-invisible';

            if(permisoShowDataTecnicaNGF)
            {
                if(rec.get('botones') === "SI")
                {
                    if(rec.get('flujo') ==='TN' && rec.get('estado') === 'Activo')
                    {
                        if(rec.get('nombreProducto') === 'SECURITY NG FIREWALL')
                        {
                            button = 'button-grid-showDataTecnicaNGF';
                        }
                    }
                }
            }

            return button;
        },
        tooltip: 'Ver Data Ténica',
        handler: function(grid, rowIndex, colIndex) {
            showDataTecnicaNGF(grid.getStore().getAt(rowIndex).data);
        }
    },
    //EDITAR DATA TECNICA DE PRODUCTO NGFIREWALL
    {
        getClass: function(v, meta, rec) 
        {
            let button = 'button-grid-invisible';

            if(permisoEditDataTecnicaNGF)
            {
                if(rec.get('botones') === "SI")
                {
                    if(rec.get('flujo') ==='TN' && rec.get('estado') === 'Activo')
                    {
                        if(rec.get('nombreProducto') === 'SECURITY NG FIREWALL')
                        {
                            button = 'button-grid-editDataTecnicaNGF';
                        }
                    }
                }
            }

            return button;
        },
        tooltip: 'Editar Data Ténica',
        handler: function(grid, rowIndex, colIndex) {
            editDataTecnicaNGF(grid.getStore().getAt(rowIndex).data);
        }
    },
    //VER INFORMACION TECNICA COMPLETA DEL CLIENTE
    {
        getClass: function(v, meta, rec) {
            if(rec.get('flujo')=='TTCO'){
                if(rec.get('descripcionProducto')=='INTERNET'){
                    var permiso = $("#ROLE_151-831");
                        var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          

                    if(!boolPermiso){ 
                        return 'button-grid-invisible';
                    }
                    else{
                        if (rec.get('estado') == "Activo" || rec.get('estado') == "Cancel" || 
                            rec.get('estado') == "Cancel-SinEje" || rec.get('estado') == "In-Corte"  || 
                            rec.get('estado') == "EnPruebas" || rec.get('estado') == "EnVerificacion" || 
                            rec.get('estado') == "In-Temp"  ) {
                                return 'button-grid-dataTecnicaCompleta';
                        }
                        else{
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
            if(rec.get('flujo')=='MD'){
                if(rec.get('descripcionProducto')=='INTERNET' || 
                rec.get('descripcionProducto') === 'INTERNET SMALL BUSINESS' || 
                rec.get('descripcionProducto') === 'TELCOHOME'){
                    permiso     = $("#ROLE_151-831");
                    boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          

                    if(!boolPermiso){ 
                        return 'button-grid-invisible';
                    }
                    else{
                        if (rec.get('estado') == "Activo" || rec.get('estado') == "Cancel" || 
                            rec.get('estado') == "In-Corte"  || 
                            rec.get('estado') == "EnPruebas" || rec.get('estado') == "EnVerificacion" || 
                            rec.get('estado') == "In-Temp"  ) {
                                return 'button-grid-dataTecnicaCompleta';
                        }
                        else{
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
            if(rec.get('flujo')=='TNP' && rec.get('descripcionProducto')=='INTERNET' && !Ext.isEmpty(rec.get('nombrePlan'))){
                var permisoTnp = $("#ROLE_151-831");
                var boolPermisoTnp = (typeof permisoTnp === 'undefined') ? false : (permisoTnp.val() == 1 ? true : false);          

                if(!boolPermisoTnp){ 
                    return 'button-grid-invisible';
                }
                else{
                    if (rec.get('estado') == "Activo" || rec.get('estado') == "Cancel" || 
                        rec.get('estado') == "In-Corte"  || 
                        rec.get('estado') == "EnPruebas" || rec.get('estado') == "EnVerificacion" || 
                        rec.get('estado') == "In-Temp"  ) {
                            return 'button-grid-dataTecnicaCompleta';
                    }
                    else{
                        return 'button-grid-invisible';
                    }
                }
            }
            if(rec.get('flujo')=='TNG' ){
                var permisoTng = $("#ROLE_151-831");
                var boolPermisoTng = (typeof permisoTng === 'undefined') ? false : (permisoTng.val() == 1 ? true : false);          

                if(!boolPermisoTng){ 
                    return 'button-grid-invisible';
                }
                else{
                    if (rec.get('estado') == "Activo" || rec.get('estado') == "Cancel" || 
                        rec.get('estado') == "In-Corte"  || 
                        rec.get('estado') == "EnPruebas" || rec.get('estado') == "EnVerificacion" || 
                        rec.get('estado') == "In-Temp"  ) {
                            return 'button-grid-dataTecnicaCompleta';
                    }
                    else{
                        return 'button-grid-invisible';
                    }
                }
            }
            if(rec.get('flujo')=='TN'){                 
                if(
                    rec.get('descripcionProducto')=='L3MPLS'           ||
                    rec.get('descripcionProducto')=='L3MPLS SDWAN'     ||
                    rec.get('descripcionProducto')=='INTMPLS'          ||
                    rec.get('descripcionProducto')=='INTERNET'         ||
                    rec.get('descripcionProducto')=='INTERNET SDWAN'   ||
                    rec.get('descripcionProducto')=='INTERNETDC'       ||
                    rec.get('descripcionProducto')=='INTERNET DC SDWAN'||
                    rec.get('descripcionProducto')=='DATOSDC'          ||
                    rec.get('descripcionProducto')=='DATOS DC SDWAN'  ||
                    rec.get('descripcionProducto')=='L2MPLS'           ||
                    rec.get('descripcionProducto')=='INTERNET WIFI'    ||
                    rec.get('descripcionProducto')=='TUNELIP'          ||
                    rec.get('descripcionProducto')=='CONCINTER'        ||
                    rec.get('descripcionProducto')=='DATOS FWA'        ||
                    rec.get('descripcionProducto')=='DATOS SAFECITY'   ||
                    rec.get('descripcionProducto')=='SAFECITYDATOS'    ||
                    rec.get('descripcionProducto')=='SAFECITYSWPOE'    ||
                    rec.get('descripcionProducto')=='SAFECITYWIFI'     ||
                    rec.get('descripcionProducto')=='SEG_VEHICULO'     ||
                    (rec.get('nombreProducto').includes('SEGURIDAD ELECTRONICA   VIDEO VIGILANCIA') == true &&
                    consultarLoginCam(rec.get('idPersonaEmpresaRol'),rec.get('idProducto')) == true && consultarInformacionSerie(rec.get('idServicio')) == true) ||
                    rec.get('descripcionProducto')=='SAFE ENTRY'       ||
                    (
                     (rec.get('nombreProducto').includes('POOL RECURSOS') && 
                      rec.get('tieneAlquilerServidores') ==='S')
                    )
                ){       
                    var permiso = $("#ROLE_151-831");
                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          

                    if(!boolPermiso){
                        return 'button-grid-invisible';
                    }
                    else{
                        if (   
                            rec.get('estado') == "Activo"         || 
                            rec.get('estado') == "Cancel"         || 
                            rec.get('estado') == "In-Corte"       || 
                            rec.get('estado') == "EnPruebas"      || 
                            rec.get('estado') == "EnVerificacion" || 
                            rec.get('estado') == "Asignada"       ||
                            rec.get('estado') == "AsignadoTarea" 
                           ) 
                        {
                            return 'button-grid-dataTecnicaCompleta';
                        }
                        else
                        {
                            return 'button-grid-invisible';
                        }
                    }
                } 
            }                  
        },
        tooltip: 'Ver Info Tecnica Completa',
        handler: function(grid, rowIndex, colIndex) {           
            if(grid.getStore().getAt(rowIndex).data.flujo=="TTCO"){
                if(grid.getStore().getAt(rowIndex).data.estado=="Activo" || 
                   grid.getStore().getAt(rowIndex).data.estado=="Cancel" || 
                   grid.getStore().getAt(rowIndex).data.estado=="Cancel-SinEje" || 
                   grid.getStore().getAt(rowIndex).data.estado=="In-Corte" || 
                   grid.getStore().getAt(rowIndex).data.estado=="EnPruebas" || 
                   grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion" || 
                   grid.getStore().getAt(rowIndex).data.estado=="In-Temp" ){
                   window.location = "../../tecnico/clientes/"+grid.getStore().getAt(rowIndex).data.idServicio+"/showServicio";
                }
            }
            if((grid.getStore().getAt(rowIndex).data.flujo=="MD" || 
               (grid.getStore().getAt(rowIndex).data.flujo == 'TNP' && 
               !Ext.isEmpty(grid.getStore().getAt(rowIndex).data.nombrePlan))) && (grid.getStore().getAt(rowIndex).data.estado=="Activo" || 
                   grid.getStore().getAt(rowIndex).data.estado=="Cancel" || 
                   grid.getStore().getAt(rowIndex).data.estado=="In-Corte" || 
                   grid.getStore().getAt(rowIndex).data.estado=="EnPruebas" || 
                   grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion" || 
                   grid.getStore().getAt(rowIndex).data.estado=="In-Temp" )){
                   window.location = "../../tecnico/clientes/"+grid.getStore().getAt(rowIndex).data.idServicio+"/showServicio";
            }
             if(grid.getStore().getAt(rowIndex).data.flujo == 'TNG' && (
                grid.getStore().getAt(rowIndex).data.estado=="Activo"  || 
                grid.getStore().getAt(rowIndex).data.estado=="Cancel" || 
                grid.getStore().getAt(rowIndex).data.estado=="In-Corte" || 
                grid.getStore().getAt(rowIndex).data.estado=="EnPruebas" || 
                grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion" || 
                grid.getStore().getAt(rowIndex).data.estado=="In-Temp" )){
                    verServicioTng(grid.getStore().getAt(rowIndex).data);
            }
            if(grid.getStore().getAt(rowIndex).data.flujo=="TN"){
                if(grid.getStore().getAt(rowIndex).data.estado=="Activo" || 
                   grid.getStore().getAt(rowIndex).data.estado=="Cancel" || 
                   grid.getStore().getAt(rowIndex).data.estado=="In-Corte" || 
                   grid.getStore().getAt(rowIndex).data.estado=="EnPruebas" || 
                   grid.getStore().getAt(rowIndex).data.estado=="AsignadoTarea" || 
                   grid.getStore().getAt(rowIndex).data.estado=="Asignada"
                ){               
                    if(grid.getStore().getAt(rowIndex).data.grupo.includes('DATACENTER'))
                    {                   
                        if(grid.getStore().getAt(rowIndex).data.descripcionProducto === 'HOSTING' && 
                           grid.getStore().getAt(rowIndex).data.tieneAlquilerServidores === 'S')
                        {
                            verInformacionServidoresAlquiler(grid.getStore().getAt(rowIndex).data);
                        }
                        if(grid.getStore().getAt(rowIndex).data.descripcionProducto === 'INTERNETDC' ||
                           grid.getStore().getAt(rowIndex).data.descripcionProducto === 'INTERNET DC SDWAN' ||
                           grid.getStore().getAt(rowIndex).data.descripcionProducto === 'DATOSDC' ||
                           grid.getStore().getAt(rowIndex).data.descripcionProducto === 'DATOS DC SDWAN' ||
                           grid.getStore().getAt(rowIndex).data.descripcionProducto === 'L2MPLS')
                        {
                            window.location = "../../tecnico/clientes/"+grid.getStore().getAt(rowIndex).data.idServicio+"/showServicio";
                        }
                    }
                    else if(grid.getStore().getAt(rowIndex).data.nombreProducto.includes('SEGURIDAD ELECTRONICA   VIDEO VIGILANCIA')){
                        verInformacionCarac(grid.getStore().getAt(rowIndex).data);
                    }
                    else
                    {
                        window.location = "../../tecnico/clientes/"+grid.getStore().getAt(rowIndex).data.idServicio+"/showServicio";
                    }                    
                }
                else if(grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion" &&
                        grid.getStore().getAt(rowIndex).data.descripcionProducto === 'DATOS SAFECITY'){
                            window.location = "../../tecnico/clientes/"+grid.getStore().getAt(rowIndex).data.idServicio+"/showServicio";
                }
            }
        }
    },

    //Flujo de soporte para Paramount y Noggin
    {
        getClass: function(v, meta, rec)
        {
            var permiso;
            if ("PARAMOUNT" === rec.get('descripcionProducto') || "NOGGIN" === rec.get('descripcionProducto'))
            {
                permiso = $("#ROLE_151-7697");
            }
            else if ("GTVPREMIUM" === rec.get('descripcionProducto'))
            {
                permiso = $("#ROLE_151-8419");
            }
            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

            if(boolPermiso)
            {
                return 'button-grid-formulario_soporte';
            }
            else
            {
                return 'button-grid-invisible';
            }
        },
        tooltip: 'Ingresar Formulario de Soporte',
        handler: function(grid, rowIndex, colIndex) {
            if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="GTVPREMIUM")
            {
                crearFormularioSoporteGtv(grid.getStore().getAt(rowIndex).data);
            }
            else
            {
                crearFormularioSoporte(grid.getStore().getAt(rowIndex).data);
            }
        }
    },
    //Flujo de soporte de segundo nivel para ECDF
    {
        getClass: function(v, meta, rec)
        {
            var permiso     = $("#ROLE_151-8357");
            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

            if(!boolPermiso)
            {
                return 'button-grid-invisible';
            }
            else
            {
                if ("ECDF" === rec.get('descripcionProducto'))
                {
                    let arrayEstadosPermitidos = ["Activo", "In-Corte", "Cancel"];
                    if(arrayEstadosPermitidos.find(a=>a === rec.get('estado')) !== undefined)
                    {
                      return 'button-grid-formulario_soporte';
                    }
                    else 
                    {
                      return 'button-grid-invisible';
                    }
                    
                }
                else
                {
                    return 'button-grid-invisible';
                }
            }
        },
        tooltip: 'Ingresar Formulario de Soporte',
        handler: function(grid, rowIndex, colIndex) {
            crearFormularioSoporteEcdf(grid.getStore().getAt(rowIndex).data);
        }
    },
    
    //CONSULTAR INFORMACION DE HOUSING ( CUARTO TI POR CIUDAD DEL USUARIO )
    {
        getClass: function(v, meta, rec) 
        {
            if(rec.get('nombreProducto').includes('Alquiler de Espacio') && rec.get('estado') !== 'Cancel')
            {
                return 'button-grid-verCuartoTi';
            }
        },
        tooltip: 'Consultar Cuarto TI',
        handler: function(grid, rowIndex, colIndex) 
        {
            verInformacionGeneralCuartoTi(grid.getStore().getAt(rowIndex).data.idServicio);
        }
    },
    
    //ADMINISTRACION DE MAQUINAS VIRTUALES
    {
        getClass: function(v, meta, rec) 
        {
            var permiso     = $("#ROLE_151-5577");
            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
            
            if(!boolPermiso)
            { 
                return 'button-grid-invisible';
            }
            else
            {   
                if(rec.get('estado') === "Asignada" || rec.get('estado') === "Activo" || rec.get('estado') === "In-Corte")
                {
                    var nombreProducto = rec.get('nombreProducto');
                    if(rec.get('descripcionProducto') === 'HOSTING' && 
                       nombreProducto.includes("POOL RECURSOS") && 
                       rec.get('tieneAlquilerServidores') === 'N')
                    {
                        return 'button-grid-adminstrarMaquinaVirtual';
                    }
                }
                
                return 'button-grid-invisible';
            }
                     
        },
        tooltip: 'Administración de Máquinas Virtuales',
        handler: function(grid, rowIndex, colIndex) 
        {
            administrarMaquinasVirtuales(grid.getStore().getAt(rowIndex).data);
        }
    },
    
    //REGULARIZA SERVICIO RADIO TN
    {
        getClass: function(v, meta, rec) 
        {
            var strClsBtnRegulaInfoRadio = 'button-grid-invisible';
            
            if(rolHerramientaRegulaServ && rec.get('prefijoEmpresa')==='TN' && rec.get('productoEsEnlace') === "SI" &&
               ( rec.get('estado') === "Activo" || rec.get('estado') === "EnPruebas" || rec.get('estado') === "In-Corte" ) && 
                 rec.get('informacionRadio') === "ERROR" && 
                 rec.get('ultimaMilla')=== 'Radio'
              )
            {
                strClsBtnRegulaInfoRadio =  'button-grid-regulaInfoRadio';  
            }
            return strClsBtnRegulaInfoRadio;
        },
        tooltip: 'Regularizar Información Servicio',
        handler: function(grid, rowIndex, colIndex) 
        {                        
            //Funcion para realizar cambio de ultima milla
            regularizacionServiciosRadioTn(grid.getStore().getAt(rowIndex).data);
        }
    },//FIN REGULARIZA SERVICIO RADIO TN
    //---------------------------------------------------
    //ADMINISTRACION DE BGP
    {
        getClass: function(v, meta, rec) 
        {
            var permiso     = $("#ROLE_151-5017");
            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          

            if(!boolPermiso)
            { 
                return 'button-grid-invisible';
            }
            else
            {
                if (rec.get('flujo') == "TN")
                {
                    if ( (rec.get('descripcionProducto') === "L3MPLS" || rec.get('descripcionProducto') === "INTMPLS" ||
                          rec.get('descripcionProducto') === "L3MPLS SDWAN" || rec.get('descripcionProducto') === "INTERNET SDWAN") && 
                        (rec.data.estado === "Activo" ||  rec.data.estado === "EnPruebas") && 
                         rec.data.poseeProtocoloBGP === "SI"
                       )
                    {
                        return 'button-grid-administracionEnrutamiento';
                    }
                    else
                    {
                        return 'button-grid-invisible';
                    }
                }
            }
                     
        },
        tooltip: 'Administración de Enrutamiento',
        handler: function(grid, rowIndex, colIndex) 
        {
            adminstrarEnrutamiento(grid.getStore().getAt(rowIndex).data);
        }
    },
    //PROTOCOLOS DE ENRUTAMIENTO
    //Se agrega boton a flujo INTMPLS siempre y cuando este no posea configuracion BGP asignada
    {
        getClass: function(v, meta, rec) 
        {
            if (rec.get('flujo') == "TN")
            {
                var validaEnlace = (rec.data.tipoEnlace !== null) ? rec.data.tipoEnlace.substring(0, 9):rec.data.tipoEnlace;
                if ( (rec.get('descripcionProducto') === "L3MPLS" || rec.get('descripcionProducto') === "L3MPLS SDWAN" ||  
                      rec.get('descripcionProducto') === "DATOS FWA" ||
                     (rec.get('descripcionProducto') === "INTMPLS" || rec.get('descripcionProducto') === "INTERNET SDWAN" && (
                      validaEnlace === 'PRINCIPAL' ||
                      rec.data.tipoEnlace === 'BACKUP' && rec.data.protocolo !== 'BGP' )
                      )) &&                    
                    (rec.data.estado === "Activo" ||  rec.data.estado === "Asignada" ||  rec.data.estado === "EnPruebas") &&
                    (rec.data.esServicioCamaraSafeCity != "S"))
                {
                    if(puedeEliminarProtocoloEnrutamiento)
                    {
                        return 'button-grid-protocolos';
                    }
                }
            }
            
            return 'button-grid-invisible';            
        },
        tooltip: 'Protocolos de Enrutamiento',
        handler: function(grid, rowIndex, colIndex) 
        {
            if (grid.getStore().getAt(rowIndex).data.flujo == "TN")
            {
                if(puedeEliminarProtocoloEnrutamiento)
                { 
                    verProtocolosEnrutamiento(grid.getStore().getAt(rowIndex).data);
                }
            }
        }
    },
    //--------------------------------------------------
    //  Cambio de Subredes para productos InterntMPLS
    //--------------------------------------------------
    {
        getClass: function(v, meta, rec) 
        {
            var permiso     = $("#ROLE_151-5137");
            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          

            if(!boolPermiso || rec.get('booleanTipoRedGpon'))
            { 
                return 'button-grid-invisible';
            }
            else
            {
                if (rec.get('flujo') == "TN")
                {
                    if ( ( rec.get('descripcionProducto') === "INTMPLS" || rec.get('descripcionProducto') === "INTERNET SDWAN" ) && 
                        (rec.data.estado === "Activo" ||  rec.data.estado === "EnPruebas")
                       )
                    {
                        return 'button-grid-cambioSubredes';
                    }
                    else
                    {
                        return 'button-grid-invisible';
                    }
                }
            }
                     
        },
        tooltip: 'Cambio de Subredes Públicas/Privadas',
        handler: function(grid, rowIndex, colIndex) 
        {
            cambiarSubredes(grid.getStore().getAt(rowIndex).data);
        }
    },
    //VER RUTAS
    {
        getClass: function(v, meta, rec) 
        {
            if (rec.get('flujo') == "TN" && rec.get('nombreProducto')!== 'CLEAR CHANNEL PUNTO A PUNTO')
            {
                var validaEnlace = (rec.data.tipoEnlace !== null) ? rec.data.tipoEnlace.substring(0, 9):rec.data.tipoEnlace;
                if (((rec.get('descripcionProducto') === "INTERNET" || 
                     rec.get('descripcionProducto') === "L3MPLS"   ||
                     rec.get('descripcionProducto') === "L3MPLS SDWAN"   ||
                     rec.get('descripcionProducto') === "INTERNET SDWAN"   ||
//                     rec.get('descripcionProducto') === "DATOSDC"  ||
//                     rec.get('descripcionProducto') === "INTERNETDC"  ||
                     rec.get('descripcionProducto') === "INTMPLS") && 
                     ((rec.data.estado === "Activo" || rec.data.estado === "EnPruebas") && rec.data.esServicioCamaraSafeCity != "S") &&
                     validaEnlace === 'PRINCIPAL'
                   ) || ( rec.data.tipoEnlace === 'BACKUP' && rec.get('descripcionProducto') === "L3MPLS"
                          && rec.get('esConcentrador') === "SI"  && rec.get('esNodoWifi') === "S")
                          ||((rec.get('descripcionProducto') === "INTERNET" || rec.get('descripcionProducto') === "INTMPLS"
                        || rec.get('descripcionProducto') === "L3MPLS" || rec.get('descripcionProducto') === "INTERNET SDWAN") 
                        && rec.data.tipoEnlace === 'BACKUP' &&
                        rec.get('permiteRutaEstaticaBuckup') === "S" && rec.data.estado === "Activo"))
                {
                    if(rolMostrarRutas){ 
                        return 'button-grid-rutas';
                    }
                }
            }
            
            return 'button-grid-invisible';            
        },
        tooltip: 'Ver rutas estaticas',
        handler: function(grid, rowIndex, colIndex) 
        {
            if (grid.getStore().getAt(rowIndex).data.flujo == "TN")
            {
                if(grid.getStore().getAt(rowIndex).data.descripcionProducto==="INTERNET")
                {
                    showRutas(grid.getStore().getAt(rowIndex).data, grid);
                }
                else if(grid.getStore().getAt(rowIndex).data.descripcionProducto==="L3MPLS" ||
                        grid.getStore().getAt(rowIndex).data.descripcionProducto==="L3MPLS SDWAN")
                {
                    showRutas(grid.getStore().getAt(rowIndex).data, grid);
                }
                else if(grid.getStore().getAt(rowIndex).data.descripcionProducto==="INTMPLS" ||
                    grid.getStore().getAt(rowIndex).data.descripcionProducto==="INTERNET SDWAN")
                {
                    showRutas(grid.getStore().getAt(rowIndex).data, grid);
                }
//                else if(grid.getStore().getAt(rowIndex).data.descripcionProducto==="DATOSDC" ||
//                        grid.getStore().getAt(rowIndex).data.descripcionProducto==="INTERNETDC")
//                {
//                    showRutas(grid.getStore().getAt(rowIndex).data, grid);
//                }
            }
        }
    },
    //RUTAS ESTATICAS
    {
        getClass: function(v, meta, rec) 
        {
            if (rec.get('flujo') == "TN" && rec.get('nombreProducto')!== 'CLEAR CHANNEL PUNTO A PUNTO')
            {
                var validaEnlace = (rec.data.tipoEnlace !== null) ? rec.data.tipoEnlace.substring(0, 9):rec.data.tipoEnlace;
                if (((rec.get('descripcionProducto') === "INTERNET" ||
                     rec.get('descripcionProducto') === "L3MPLS"   ||
                     rec.get('descripcionProducto') === "L3MPLS SDWAN"   ||
                     rec.get('descripcionProducto') === "INTERNET SDWAN"   ||
//                     rec.get('descripcionProducto') === "DATOSDC"  ||
//                     rec.get('descripcionProducto') === "INTERNETDC"  ||
                     rec.get('descripcionProducto') === "INTMPLS") && (rec.data.estado === "Activo" || rec.data.estado === "EnPruebas") &&
                     validaEnlace === 'PRINCIPAL' && rec.get('esServicioCamaraSafeCity') != "S"
                     ) || ( rec.data.tipoEnlace === 'BACKUP' && rec.get('descripcionProducto') === "L3MPLS"
                          && rec.get('esConcentrador') === "SI"  && rec.get('esNodoWifi') === "S")
                        ||((rec.get('descripcionProducto') === "INTERNET" || rec.get('descripcionProducto') === "INTMPLS"
                        || rec.get('descripcionProducto') === "L3MPLS" || rec.get('descripcionProducto') === "INTERNET SDWAN") 
                        && rec.data.tipoEnlace === 'BACKUP' &&
                        rec.get('permiteRutaEstaticaBuckup') === "S" && rec.data.estado === "Activo"))
                {
                    if(puedeCrearRutaEstatica){ 
                        return 'button-grid-ruta-estatica';
                    }
                }
            }
            
            return 'button-grid-invisible';            
        },
        tooltip: 'Crear ruta estatica',
        handler: function(grid, rowIndex, colIndex) 
        {
            if(grid.getStore().getAt(rowIndex).data.flujo==="TN")
            {
                if(grid.getStore().getAt(rowIndex).data.descripcionProducto==="INTERNET")
                {
                    crearRutaEstatica(grid.getStore().getAt(rowIndex).data,grid);
                }
                else if(grid.getStore().getAt(rowIndex).data.descripcionProducto==="L3MPLS" ||
                        grid.getStore().getAt(rowIndex).data.descripcionProducto==="L3MPLS SDWAN")
                {
                    crearRutaEstatica(grid.getStore().getAt(rowIndex).data,grid);
                }
                else if(grid.getStore().getAt(rowIndex).data.descripcionProducto==="INTMPLS" ||
                    grid.getStore().getAt(rowIndex).data.descripcionProducto==="INTERNET SDWAN")
                {
                    crearRutaEstatica(grid.getStore().getAt(rowIndex).data,grid);
                }
//                else if(grid.getStore().getAt(rowIndex).data.descripcionProducto==="DATOSDC" || 
//                        grid.getStore().getAt(rowIndex).data.descripcionProducto==="INTERNETDC")
//                {
//                    crearRutaEstatica(grid.getStore().getAt(rowIndex).data,grid);
//                }
            }
        }
    },
    //RUTAS AUTOMATICAS
    {
        getClass: function(v, meta, rec) 
        {
            if (rec.get('flujo') == "TN")
            {
                var validaEnlace = (rec.data.tipoEnlace !== null) ? rec.data.tipoEnlace.substring(0, 9):rec.data.tipoEnlace;
                if(puedeCrearRutaAutomaticas)
                {
                    if ((rec.get('descripcionProducto') === "INTMPLS" ||
                        rec.get('descripcionProducto') === "INTERNET SDWAN"
                        //rec.get('descripcionProducto') === "INTERNETDC"
                        )
                        && (rec.data.estado === "Activo" || rec.data.estado === "EnPruebas") &&
                        validaEnlace === 'PRINCIPAL')
                    {
                        return 'button-grid-ruta-automatica';
                    }
                    else
                    {
                        return 'button-grid-invisible';
                    }
                }
            }
        },
        tooltip: 'Crear ruta automatica',
        handler: function(grid, rowIndex, colIndex) 
        {
            if(grid.getStore().getAt(rowIndex).data.flujo==="TN")
            {
                if((grid.getStore().getAt(rowIndex).data.estado==="Activo" || grid.getStore().getAt(rowIndex).data.estado==="EnPruebas") && 
                   (grid.getStore().getAt(rowIndex).data.descripcionProducto==="INTMPLS" ||
                   grid.getStore().getAt(rowIndex).data.descripcionProducto==="INTERNET SDWAN"
                   // grid.getStore().getAt(rowIndex).data.descripcionProducto==="INTERNETDC"
                    ))
                {
                    crearRutaAutomatica(grid.getStore().getAt(rowIndex).data, grid);
                }
            }
        }
    },
    //CREAR CACTI
    {
        getClass: function(v, meta, rec) 
        {
            if (rec.get('flujo') == "TN")
            {
                if(puedeCrearCacti)
                {
                    if ((rec.get('productoEsEnlace') == "SI") && (rec.get('estado') == "Activo") && (rec.get('cacti') != "SI")
                        && !rec.get('grupo').includes('DATACENTER') && rec.get('nombreProducto') !== "Cableado Estructurado"
                        && !rec.get('booleanTipoRedGpon') && rec.get('esServicioCamaraSafeCity') != "S"
                        && rec.get('nombreProducto')!== 'CLEAR CHANNEL PUNTO A PUNTO')
                    {
                        return 'button-grid-cacti';
                    }
                    else
                    {
                        return 'button-grid-invisible';
                    }
                }
            }
        },
        tooltip: 'Crear Cacti',
        handler: function(grid, rowIndex, colIndex) 
        {
            if(grid.getStore().getAt(rowIndex).data.flujo=="TN")
            {
                if(grid.getStore().getAt(rowIndex).data.estado=="Activo" && grid.getStore().getAt(rowIndex).data.productoEsEnlace=="SI" && 
                   grid.getStore().getAt(rowIndex).data.cacti!="SI" && !grid.getStore().getAt(rowIndex).data.booleanTipoRedGpon)
                {
                    crearCacti(grid.getStore().getAt(rowIndex).data, grid);
                }
            }
        }
    },
    //Reenviar credenciales Telcograf
    {
        getClass: function(v, meta, rec) 
        {
            if (rec.get('flujo') === "TN" && 
                puedeReenviarCredenciales && 
                rec.get('strReenvioCredencialTg') === "SI" &&
                rec.get('estado') === "Activo"
                )
            {
                
                return 'button-grid-mail';
            }
            else
            {
                return 'button-grid-invisible';
            }
        },
        tooltip: 'Reenviar Credenciales Telcograf',
        handler: function(grid, rowIndex, colIndex) 
        {
            if(grid.getStore().getAt(rowIndex).data.flujo === "TN" &&
               grid.getStore().getAt(rowIndex).data.strReenvioCredencialTg === "SI" &&
               grid.getStore().getAt(rowIndex).data.estado === "Activo")
            {
                reenviarCredencialesTg(grid.getStore().getAt(rowIndex).data, grid);
            }
        }
    },
    //Reintento Monitoreo Telcograf
    {
        getClass: function(v, meta, rec) 
        {
            if (rec.get('flujo') === "TN" && 
                puedeReintentarMonitoreo && 
                rec.get('strReintentoCreacionTg') === "SI" &&
                rec.get('estado') === "Activo" && rec.get('esServicioCamaraSafeCity') != "S"
                && rec.get('nombreProducto')!== 'CLEAR CHANNEL PUNTO A PUNTO'
                )
            {
                
                return 'button-grid-reintentoMonitoreTg';
            }
            else
            {
                return 'button-grid-invisible';
            }
        },
        tooltip: 'Reintento Monitoreo Telcograf',
        handler: function(grid, rowIndex) 
        {
            if(grid.getStore().getAt(rowIndex).data.flujo === "TN" &&
               grid.getStore().getAt(rowIndex).data.strReintentoCreacionTg === "SI" &&
               grid.getStore().getAt(rowIndex).data.estado === "Activo")
            {
                reintentoMonitoreoTg(grid.getStore().getAt(rowIndex).data, grid);
            }
        }
    },
    //Crear Monitoreo Telcograf
    {
        getClass: function(v, meta, rec)
        {
            var icon = 'button-grid-invisible';
            if (rec.get('flujo') === "TN" && rec.get('estado') === "Activo"
                    && rec.get('strCrearMonitoreoTG') === "SI" && creaMonitoreoTelcograf && rec.get('nombreProducto') !== "Cableado Estructurado"
                    && rec.get('boolSecureCpe') !== 'S') 
            {
                if (rec.get('boolVisualizarBotonCorte') === 'N' )
                {
                    icon = 'button-grid-invisible';
                }
                else if(rec.get('nombreProducto') == 'CLEAR CHANNEL PUNTO A PUNTO'
                 &&  rec.get('aprovisioClearChannel') != 'NO')
                {
                    return 'button-grid-invisible';
                }
                else
                {
                    icon = 'button-grid-monitoreoTg';
                }
            }
            return icon;
        },
        tooltip: 'Crear Monitoreo Telcograf',
        handler: function(grid, rowIndex)
        {
            if(grid.getStore().getAt(rowIndex).data.flujo  === "TN" &&
                grid.getStore().getAt(rowIndex).data.strCrearMonitoreoTG === "SI" &&
               grid.getStore().getAt(rowIndex).data.estado === "Activo")
            {
                crearMonitoreoTg(grid.getStore().getAt(rowIndex).data);
            }
        }
    },
    //Reenvío de notificación y Cambio de Contraseña - Telcograf
    {
        getClass: function(v, meta, rec)  
        {
            if (rec.get('flujo') === "TN" && 
                puedeCambiarPass && 
                rec.get('strCambioPassTg') === "SI" &&
                rec.get('estado') === "Activo" && rec.get('esServicioCamaraSafeCity') != "S"
                && rec.get('nombreProducto')!== 'CLEAR CHANNEL PUNTO A PUNTO'
                )
            {
                
                return 'button-grid-PassWordTg';
            }
            else
            {
                return 'button-grid-invisible';
            }
        },
        tooltip: 'Reenvío de notificación y Cambio de Contraseña - Telcograf',
        handler: function(grid, rowIndex) 
        {
            if(grid.getStore().getAt(rowIndex).data.flujo === "TN" &&
               grid.getStore().getAt(rowIndex).data.strCambioPassTg === "SI" &&
               grid.getStore().getAt(rowIndex).data.estado === "Activo")
            {
                cambiarPassTg(grid.getStore().getAt(rowIndex).data, grid);
            }
        }
    },
    //Cambiar usuario - Telcograf
    {
        getClass: function(v, meta, rec) 
        {
            if (rec.get('flujo') === "TN" && 
                puedeCambiarPass && 
                rec.get('strCambioPassTg') === "SI" &&
                rec.get('estado') === "Activo" && rec.get('esServicioCamaraSafeCity') != "S"
                && rec.get('nombreProducto')!== 'CLEAR CHANNEL PUNTO A PUNTO'
                )
            {
                
                return 'button-grid-cambiarUsuaTelcoGraph';
            }
            else
            {
                return 'button-grid-invisible';
            }
        },
        tooltip: 'Cambio de usuario - Telcograf',
        handler: function(grid, rowIndex) 
        {
            if(grid.getStore().getAt(rowIndex).data.flujo === "TN" &&
               grid.getStore().getAt(rowIndex).data.strCambioPassTg === "SI" &&
               grid.getStore().getAt(rowIndex).data.estado === "Activo")
            {
                cambiarUsuarioTg(grid.getStore().getAt(rowIndex).data, grid);
            }
        }
    },
    //Restablecer datos Ldap - Telcograf
    {
        tooltip: 'Restablecer datos Ldap - Telcograf',
        getClass: function(v, meta, rec) {
            var icono = 'button-grid-invisible';
            if (rec.get('flujo')  === "TN"     && puedeRestablecerDatosLdapTg &&
                rec.get('estado') === "Activo" && rec.get('strCambioPassTg') === "SI"
                && rec.get('nombreProducto')!== 'CLEAR CHANNEL PUNTO A PUNTO') {
                icono = 'button-grid-verReversoSolicitudMigracion';
            }
            return icono;
        },
        handler: function(grid, rowIndex){
            var data = grid.getStore().getAt(rowIndex).data;
            if(data.flujo === "TN" && puedeRestablecerDatosLdapTg && data.estado === "Activo" && data.strCambioPassTg === "SI") {
                restablecerDatosLdapTg(data);
            }
        }
    },
    //Ver Información Técnica Telcograf
    {
        getClass: function(v, meta, rec)
        {
            if (rec.get('flujo') === "TN" && puedeVerInfoTelcoGraph  && rec.get('estado') === "Activo"
                && rec.get('strMostrarInfoTelcoGraph') === "SI" && rec.get('esServicioCamaraSafeCity') != "S"
                && rec.get('nombreProducto')!== 'CLEAR CHANNEL PUNTO A PUNTO') {
                return 'button-grid-infoTelcoGraph';
            } else {
                return 'button-grid-invisible';
            }
        },
        tooltip: 'Ver Información Técnica Telcograf',
        handler: function(grid, rowIndex)
        {
            if(grid.getStore().getAt(rowIndex).data.flujo === "TN" && grid.getStore().getAt(rowIndex).data.estado === "Activo"
                && grid.getStore().getAt(rowIndex).data.strMostrarInfoTelcoGraph === "SI") {
                verInfoTelcograf(grid.getStore().getAt(rowIndex).data, grid);
            }
        }
    },
    //CAMBIO DE CPE
    {
        tooltip: 'Cambio CPE',
        getClass: function(v, meta, rec) {

            const productosPermitidos = {
                "tn": ['INTMPLS','INTERNET SDWAN','L3MPLS', 'L3MPLS SDWAN', 'INTERNET', 'INTERNET WIFI', 'WIFI Alquiler Equipos',
                       'DATOS SAFECITY','SAFECITYDATOS','SAFECITYSWPOE','SAFECITYWIFI','SEG_VEHICULO', 'SAFE ENTRY']
            };
            if(rec.get('tieneSolicitudCambioCpe')!=null){
                if(rec.get('flujo')=='TTCO'){
                    var permiso = $("#ROLE_151-1107");
                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                    //alert(typeof permiso);
                    if(!boolPermiso){ 
                        return 'button-grid-invisible';
                    }
                    else{
                        if (rec.get('estado') == "Activo" && rec.get('descripcionProducto')=='INTERNET') {
                            return 'button-grid-cambioCpe';
                                // return 'button-grid-invisible';

                        }
                        else{
                            if (rec.get('estado') == "Activo" && rec.get('descripcionProducto')=='INTERNET') {
                                return 'button-grid-cambioCpe';
                            }
                            else{
                                return 'button-grid-invisible';
                            }
                        }
                    }
                }
                if(rec.get('flujo')=='MD'){
                    var permiso = $("#ROLE_151-1107");
                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                    //alert(typeof permiso);
                    if(!boolPermiso){ 
                        return 'button-grid-invisible';
                    }
                    else{
                        if (rec.get('estado') == "Activo" && 
                            (rec.get('descripcionProducto')=='INTERNET' || 
                             ((rec.get('descripcionProducto')=='SMARTWIFI' ||
                               rec.get('descripcionProducto')=='APWIFI'
                              )&& 
                              Ext.isEmpty(rec.get('nombrePlan'))
                             )
                             || rec.get('descripcionProducto') === 'INTERNET SMALL BUSINESS'
                             || rec.get('descripcionProducto') === 'TELCOHOME'
                             || rec.get('descripcionProducto') === 'EXTENDER_DUAL_BAND'
                             || rec.get('descripcionProducto') === 'WDB_Y_EDB'
                            )
                           ) {
                            return 'button-grid-cambioCpe';
                        }
                        else{
                            if (rec.get('estado') == "Activo" && rec.get('descripcionProducto')=='INTERNET') {
                                return 'button-grid-cambioCpe';
                            }
                            else{
                                return 'button-grid-invisible';
                            }
                        }
                    }
                }             
                if(rec.get('flujo')=='TN'){
                    this.items[22].tooltip = 'Cambio Elemento Cliente';
                    var permiso = $("#ROLE_151-1107");
                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

                    if(!boolPermiso){
                        return 'button-grid-invisible';
                    }
                    else{
                        if ( 
                            (rec.get('estado') == "Activo" || rec.get('estado') == "EnPruebas") && 
                            ((productosPermitidos.tn.includes(rec.get('descripcionProducto')) ||
                              productosPermitidos.tn.includes(rec.get('nombreProducto')) )
                            || rec.get('registroEquipo') === "S" )) {
                            return 'button-grid-cambioCpe';
                        }
                        else{
                            return 'button-grid-invisible';
                        }
                    }
                }
                if(rec.get('flujo')=='TNP'){
                    var permisoTnp = $("#ROLE_151-1107");
                    var boolPermisoTnp = (typeof permisoTnp === 'undefined') ? false : (permisoTnp.val() == 1 ? true : false);          
                    if(!boolPermisoTnp){ 
                        return 'button-grid-invisible';
                    }
                    else{
                        if (rec.get('estado') == "Activo" && 
                            (rec.get('descripcionProducto')=='INTERNET')
                           ) {
                            return 'button-grid-cambioCpe';
                        }
                        else{
                            if (rec.get('estado') == "Activo" && rec.get('descripcionProducto')=='INTERNET') {
                                return 'button-grid-cambioCpe';
                            }
                            else{
                                return 'button-grid-invisible';
                            }
                        }
                    }
                }   
            }

        },
        handler: function(grid, rowIndex, colIndex) {
            if(grid.getStore().getAt(rowIndex).data.flujo=="TTCO"){
                if(grid.getStore().getAt(rowIndex).data.tieneSolicitudCambioCpe!=null){
                    if(grid.getStore().getAt(rowIndex).data.estado=="Activo" && 
                       grid.getStore().getAt(rowIndex).data.descripcionProducto=="INTERNET"){
                        cambioElementoCliente(grid.getStore().getAt(rowIndex).data);
                    }
                }
            }
            if(grid.getStore().getAt(rowIndex).data.flujo=="MD"){
                if(grid.getStore().getAt(rowIndex).data.tieneSolicitudCambioCpe!=null){
                    cambioElementoCliente(grid.getStore().getAt(rowIndex).data);
                }
            }

            if(grid.getStore().getAt(rowIndex).data.flujo=="TN"){
                if(grid.getStore().getAt(rowIndex).data.descripcionProducto == "INTMPLS" ||
                   grid.getStore().getAt(rowIndex).data.descripcionProducto == "INTERNET SDWAN" ||
                   grid.getStore().getAt(rowIndex).data.descripcionProducto == "L3MPLS"  ||
                   grid.getStore().getAt(rowIndex).data.descripcionProducto == "L3MPLS SDWAN"  ||
                   grid.getStore().getAt(rowIndex).data.descripcionProducto == "INTERNET" ||
                   grid.getStore().getAt(rowIndex).data.descripcionProducto == "SEG_VEHICULO" ||
                   grid.getStore().getAt(rowIndex).data.descripcionProducto == 'SAFE ENTRY' ||
                   grid.getStore().getAt(rowIndex).data.registroEquipo === "S" )
                {
                    if(grid.getStore().getAt(rowIndex).data.tieneSolicitudCambioCpe!=null){
                        cambioElementoCliente(grid.getStore().getAt(rowIndex).data);
                    }
                }
                else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="INTERNET WIFI")
                {
                    if(grid.getStore().getAt(rowIndex).data.tieneSolicitudCambioCpe!=null){
                        cambioElementoWifi(grid.getStore().getAt(rowIndex).data);
                    }
                }
                else if (grid.getStore().getAt(rowIndex).data.nombreProducto == 'WIFI Alquiler Equipos' &&
                    grid.getStore().getAt(rowIndex).data.tieneSolicitudCambioCpe != null)
                {
                    if (!grid.getStore().getAt(rowIndex).data.loginAux)
                    {
                        Ext.MessageBox.show({
                            title: 'Atención',
                            msg: 'El servicio seleccionado no posee <b class="red-text">&#171;Login Auxiliar&#187;</b>, desea continuar?',
                            closable: false,
                            multiline: false,
                            icon: Ext.Msg.QUESTION,
                            buttons: Ext.Msg.YESNO,
                            buttonText: {yes: 'Si', no: 'No'},
                            fn: function (buttonValue) {
                                if (buttonValue === 'yes')
                                {
                                    cambioElementoWAE(grid.getStore().getAt(rowIndex).data);
                                }
                            }
                        });
                    } else
                    {
                        cambioElementoWAE(grid.getStore().getAt(rowIndex).data);
                    }
                }
            }
            
            if(grid.getStore().getAt(rowIndex).data.flujo=="TNP" && 
               grid.getStore().getAt(rowIndex).data.tieneSolicitudCambioCpe!=null)
            {
                cambioElementoCliente(grid.getStore().getAt(rowIndex).data);
            }
        }
    },
    //Actualizar Data Tecnica 
    {
        getClass: function(v, meta, rec) 
        {   

            if(rec.get('tieneSolicitudCambioCpe')!=null)
            //|| rec.get('usaUltimaMillaExistente') == "SI"
            {
                if(rec.get('flujo')=='TN'){
                    var permiso = $("#ROLE_151-8697");
                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
    
                    if(!boolPermiso){
                        return 'button-grid-invisible';
                    }
                    else{
                        if ( 
                            (rec.get('estado') == "Activo"  
                            )) {
                            return 'button-grid-cambioDataTecnicaCpe';
                        }
                        else{
                            return 'button-grid-invisible';
                        }
                    }
                }
            }   
        },
        tooltip: 'Actualizar Data Tecnica Cpe',
        handler: function(grid, rowIndex, colIndex)
        {    
            actualizaDataTecnicaCpe(grid.getStore().getAt(rowIndex).data);
        }
    },
    //Activacion de wifi
    {
        getClass: function(v, meta, rec) {

            if (rec.get('prefijoEmpresa') == 'TN') {
                var permiso = $("#ROLE_341-3957");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                //alert(typeof permiso);
                if (!boolPermiso) {
                    return 'button-grid-invisible';
                }
                else {
                    if (rec.get('estado') == "Asignada" && rec.get('descripcionProducto') == 'INTERNET WIFI') {
                        return 'button-grid-informacionTecnica';
                    }
                }
            }
        },
        tooltip: 'Activar Wifi',
        handler: function(grid, rowIndex, colIndex) {
            if (grid.getStore().getAt(rowIndex).data.prefijoEmpresa == "TN") {

                if (grid.getStore().getAt(rowIndex).data.estado == "Asignada" &&
                    grid.getStore().getAt(rowIndex).data.descripcionProducto == 'INTERNET WIFI') {
                    activacionWifi(grid.getStore().getAt(rowIndex).data);
                }
            }
        }
    },
    //cambio nodo wifi logico
    {
        getClass: function(v, meta, rec) {

            if (rec.get('prefijoEmpresa') == 'TN') {
                var permiso = $("#ROLE_341-4617");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                if (!boolPermiso) {
                    return 'button-grid-invisible';
                }
                else {
                    if (rec.get('estado') == "Activo" && rec.get('descripcionProducto') == 'INTERNET WIFI') {
                        return 'button-grid-cambiarEstado';
                    }
                }
            }
        },
        tooltip: 'Cambio Nodo Wifi Logico',
        handler: function(grid, rowIndex, colIndex) {
                    cambioNodoWifi(grid.getStore().getAt(rowIndex));
        }
    },  
    //cambio de elemento pasivo
    {
        getClass: function(v, meta, rec) {

            if (rec.get('prefijoEmpresa') == 'TN' && !rec.get('booleanTipoRedGpon')) {
                var permiso = $("#ROLE_17-5437");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                if (!boolPermiso ) {
                    return 'button-grid-invisible';
                }
                else {
                    if ((rec.get('estado') == "EnPruebas" || rec.get('estado') == "Activo" ) &&                         (
                         rec.get('descripcionProducto') == 'L3MPLS' ||
                         rec.get('descripcionProducto') == 'L3MPLS SDWAN' ||
                         rec.get('descripcionProducto') == 'INTERNET' ||
                         rec.get('descripcionProducto') == 'INTMPLS' ||
                         rec.get('descripcionProducto') == 'INTERNET SDWAN') && (rec.get('esServicioCamaraSafeCity') != "S")
                         && rec.get('nombreProducto')!== 'CLEAR CHANNEL PUNTO A PUNTO' ) {
                        return 'button-grid-cambiarEstado';
                    }
                    else
                    {
                        return 'button-grid-invisible';
                    }
                }
            }
        },
        tooltip: 'Cambio de elemento pasivo',
        handler: function(grid, rowIndex, colIndex) {
            ejecutaCambioElementoPasivo(grid.getStore().getAt(rowIndex).data);
        }
    },     
    //EDITAR INFORMACION TECNICA
    {
        getClass: function(v, meta, rec) {
            if(rec.get('flujo')=='TTCO'){
                if(rec.get('descripcionProducto')=='INTERNET'){
                    var permiso = $("#ROLE_151-830");
                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                    //alert(typeof permiso);
                    if(!boolPermiso){ 
                        return 'button-grid-invisible';
                    }
                    else{
                        if (rec.get('estado') == "Activo") {
                                return 'button-grid-edit';
                        }
                        else{
                            return 'button-grid-invisible';
                        }
                    }                                    
                }
            }
            if(rec.get('flujo')=='MD'){

            }
        },
        tooltip: 'Editar Info Tecnica',
        handler: function(grid, rowIndex, colIndex) {
            if(grid.getStore().getAt(rowIndex).data.flujo=="TTCO"){
                if(grid.getStore().getAt(rowIndex).data.estado=="Activo"){
                    editarInformactionTecnicaCompleta(grid.getStore().getAt(rowIndex).data);
                }
            }
            if(grid.getStore().getAt(rowIndex).data.flujo=="MD"){

            }
        }
    },
    //Agregar Mac address
    {
        getClass: function(v, meta, rec) {
            //var permiso = $("#ROLE_151-853");
            //var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          

            if ((rec.get('estado') == "Activo") && (rec.get('requiereMac')=="SI") && (rec.get('flujo')=='MD')){ 

                 if ((rec.get('descripcionProducto') == ("IP FIJA") && rec.get('botones')=='SI')){
                     return 'button-grid-agregarMac';}
                 else if ((rec.get('descripcionProducto') == ("INTERNET"))){ 
                     return 'button-grid-agregarMac';}
                 else{
                     return 'button-grid-invisible';                                         
                }
            }
             else {
                 return 'button-grid-invisible';       
            }
        },
        tooltip: 'Agregar Mac Address Wifi',
        handler: function(grid, rowIndex, colIndex) {
            agregarMacAddres(grid.getStore().getAt(rowIndex).data, grid);
        }
    },
    
    //Reenviar contraseña Fox Premium, Paramount, Noggin, Goltv y El canal del futbol
    {
        getClass: function(v, meta, rec) {
            if ("FOXPREMIUM" === rec.get('descripcionProducto'))
            {
                if(rolReenvioContraseniaFOX == 1 && rec.get('estado') == "Activo")
                {
                    return 'button-grid-mail';
                }
            }
            else if ("PARAMOUNT" === rec.get('descripcionProducto'))
            {
                if(rolReenvioContraseniaPARAMOUNT == 1 && rec.get('estado') == "Activo")
                {
                    return 'button-grid-mail';
                }
            }
            else if ("NOGGIN" === rec.get('descripcionProducto'))
            {
                if(rolReenvioContraseniaNOGGIN == 1 && rec.get('estado') == "Activo")
                {
                    return 'button-grid-mail';
                }
            }
            else if ("GTVPREMIUM" === rec.get('descripcionProducto'))
            {
                if(rolReenvioContraseniaGTV == 1 && rec.get('estado') == "Activo")
                {
                    return 'button-grid-mail';
                }
            }
            
            else if ("ECDF" === rec.get('descripcionProducto'))
            {
                let arrayEstadosPermitidos = ["Activo", "In-Corte", "Cancel"];
                if(rolReenvioContraseniaECDF == 1 && arrayEstadosPermitidos.find(a=>a === rec.get('estado')) !== undefined)
                {
                    return 'button-grid-mail';
                }
                else
                {
                    return 'button-grid-invisible';
                }
            }
            else
            {
                return 'button-grid-invisible';
            }
        },
        tooltip: 'Reenviar Contraseña',
        handler: function(grid, rowIndex, colIndex, v, meta, rec) {
            reenviarContrasenia(grid.getStore().getAt(rowIndex).data.idServicio, rec.get('descripcionProducto'));
        }
    },
    //Restablecer contraseña Fox Premium, Paramount , Noggin
    {
        getClass: function(v, meta, rec) {
            if ("FOXPREMIUM" === rec.get('descripcionProducto'))
            {
                if(rolRestablecerContraseniaFOX == 1 && rec.get('estado') == "Activo")
                {
                    return 'button-grid icon-password';
                }
            }
            else if ("PARAMOUNT" === rec.get('descripcionProducto'))
            {
                if(rolRestablecerContraseniaPARAMOUNT == 1 && rec.get('estado') == "Activo")
                {
                    return 'button-grid icon-password';
                }
            }
            else if ("NOGGIN" === rec.get('descripcionProducto'))
            {
                if(rolRestablecerContraseniaNOGGIN == 1 && rec.get('estado') == "Activo")
                {
                    return 'button-grid icon-password';
                }
            }
            else if ("ECDF" === rec.get('descripcionProducto'))
            {
                if(rolRestablecerContraseniaECDF == 1 && (rec.get('estado') == "Activo" || rec.get('estado') == "Cancel" 
                || rec.get('estado') == "In-Corte"))
                {
                    return 'button-grid icon-password';
                }
            }
            else if ("GTVPREMIUM" === rec.get('descripcionProducto'))
            {
                if(rolRestablecerContraseniaGTV == 1 && (rec.get('estado') == "Activo" || rec.get('estado') == "Cancel"))
                {
                    return 'button-grid icon-password';
                }
            }
            else if ("HBO-MAX" === rec.get('descripcionProducto'))
            {
                if(rolRestablecerContraseniaHBOMAX == 1 && rec.get("strTienePassword") == "SI" 
                  && (rec.get('estado') == "Activo" || rec.get('estado') == "Cancel" || rec.get('estado') == "In-Corte"))
                {
                    return 'button-grid icon-password';
                }
            }
            else
            {
                return 'button-grid-invisible';
            }
        },
        tooltip: 'Restablecer Contraseña',
        handler: function(grid, rowIndex, colIndex,v, meta, rec) {
            restablecerContrasenia(grid.getStore().getAt(rowIndex).data.idServicio, rec.get('descripcionProducto'));
        }
    },
    //Toolbox Fox Premium, Paramount , Noggin
    {
        getClass: function(v, meta, rec) {
            if ("FOXPREMIUM" === rec.get('descripcionProducto'))
            {
                if(rolClearCacheToolboxFOX == 1)
                {
                    return 'button-grid icon-fox';
                }
            }
            else if ("PARAMOUNT" === rec.get('descripcionProducto'))
            {
                if(rolClearCacheToolboxPARAMOUNT == 1)
                {
                    return 'button-grid icon-paramount';
                }
            }
            else if ("NOGGIN" === rec.get('descripcionProducto'))
            {
                if(rolClearCacheToolboxNOGGIN == 1)
                {
                    return 'button-grid icon-noggin';
                }
            }
            else
            {
                return 'button-grid-invisible';
            }
        },
        tooltip: 'Sincronizar Usuario',
        handler: function(grid, rowIndex, colIndex,v,meta, rec) {
            clearCacheToolbox(grid.getStore().getAt(rowIndex).data.idServicio, rec.get('descripcionProducto'));
        }
    },
    //Ingresar Correo para productos Paramount , Noggin
    {
        getClass: function(v, meta, rec) {
            if ("PARAMOUNT" === rec.get('descripcionProducto'))
            {
                if(rolCorreoPARAMOUNT == 1 && rec.get('estado') == "Activo")
                {
                    return 'button-grid icon-ingresarCorreo ';
                }
            }
            else if ("NOGGIN" === rec.get('descripcionProducto'))
            {
                if(rolCorreoNOGGIN == 1 && rec.get('estado') == "Activo")
                {
                    return 'button-grid icon-ingresarCorreo ';
                }
            }
            else
            {
                return 'button-grid-invisible';
            }
        },
        tooltip: 'Ingresar Contacto',
        handler: function(grid, rowIndex, colIndex,v,meta, rec) {
            ingresarContacto(grid.getStore().getAt(rowIndex).raw, rec.get('descripcionProducto'));
        }
    },
      //Ingresar Correo para activar ECDF
    {
        getClass: function(v, meta, rec) {
            if ("ECDF" === rec.get('descripcionProducto'))
            {
                if(rolCorreoECDF == 1 && (rec.get('estado') == "Pendiente") 
                && rec.get('boolServicioInternetActivo') === true && rec.get('strCorreoECDF') === null)
                {
                  return 'button-grid icon-ingresarCorreo ';
                }
            }
            else
            {
                return 'button-grid-invisible';
            }
        },
        tooltip: 'Ingresar Correo Electrónico',
        handler: function(grid, rowIndex, colIndex,v,meta, rec) {
            agregarCorreoElectronicoECDF(grid.getStore().getAt(rowIndex).raw, rec.get('descripcionProducto'));
        }
    },
    //Actualizar Correo para activar ECDF
    {
        getClass: function(v, meta, rec) {
            if ("ECDF" === rec.get('descripcionProducto'))
            {
                if(rolCorreoECDF == 1 && (rec.get('estado') == "Pendiente") 
                  && rec.get('boolServicioInternetActivo') === true && rec.get('strCorreoECDF') !== null)
                {
                  return 'button-grid-verCorreo ';
                }
            }
            else
            {
                return 'button-grid-invisible';
            }
        },
        tooltip: 'Actualizar Correo Electrónico',
        handler: function(grid, rowIndex, colIndex,v,meta, rec) {
            actualizarCorreoElectronicoECDF(grid.getStore().getAt(rowIndex).raw, rec.get('strCorreoECDF'));
        }
    },
    // Reenviar correo para crear contraseña a productos que no generan credenciales
    {
      getClass: function(v, meta, rec) {
          if ("HBO-MAX" === rec.get('descripcionProducto'))
          {
              if(rolRestablecerContraseniaHBOMAX == 1 && rec.get('estado') == "Activo" && rec.get("strTienePassword") != "SI")
              {
                return 'button-grid-mail ';
              }
          }
          else
          {
              return 'button-grid-invisible';
          }
      },
      tooltip: 'Reenviar correo para creación de contraseña',
      handler: function(grid, rowIndex, colIndex,v,meta, rec) {
        reenviarCorreoPassword(grid.getStore().getAt(rowIndex).raw);
      }
  },
    //CORTAR SERVICIO
    {
        getClass: function(v, meta, rec) {
            if(rec.get('prefijoEmpresa')=='TN')
            {
                var strProductoPaqHoras       =   rec.get('strValorProductoPaqHoras');
                var strProductoPaqHorasRec    =   rec.get('strValorProductoPaqHorasRec');
                var strDescripcionProducto    =   rec.get('nombreProducto');
                if ((strDescripcionProducto == strProductoPaqHoras ) || (strDescripcionProducto == strProductoPaqHorasRec ) )
                {
                    return 'button-grid-invisible';    
                }
            }
            if (rec.get('intServicioFTTxTN') !== null) {
                return 'button-grid-invisible';
            }
            if(rec.get('flujo')=='TTCO'){
                if(rec.get('descripcionProducto')=='INTERNET' || rec.get('descripcionProducto')=='CORREO' || 
                   rec.get('descripcionProducto')=='IP' || rec.get('descripcionProducto')=='IP PUBLICA' || 
                   rec.get('descripcionProducto')=='DOMINIO' || rec.get('descripcionProducto')=='HOSTING' || 
                   rec.get('descripcionProducto')=='SITIO WEB' || rec.get('descripcionProducto')=='SMTP AUTENTICADO' ||                   
                   rec.get('descripcionProducto')=='OTROS'){
                    var permiso = $("#ROLE_151-311");
                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                    //alert(typeof permiso);
                    if(!boolPermiso){ 
                        return 'button-grid-invisible';
                    }
                    else{
                        if ( (rec.get('estado') == "Activo" || rec.get('estado') == "Pre-cancelado") && 
                              rec.get('botones')=="SI") {
                            return 'button-grid-cortarCliente';
                        }
                        else{
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
            else if(rec.get('flujo')=='MD'){
                if(rec.get('descripcionProducto')=='INTERNET' || rec.get('descripcionProducto')=='CORREO' || 
                   rec.get('descripcionProducto')=='DOMINIO' || rec.get('descripcionProducto')=='HOSTING' || 
                   rec.get('descripcionProducto')=='SITIO WEB' || rec.get('descripcionProducto')=='SMTP AUTENTICADO' ||
                   (rec.get('descripcionProducto')=='OTROS' && 
                    !rec.get('nombreProducto').includes('I. PROTEGIDO') &&
                    !rec.get('nombreProducto').includes('I. PROTECCION')
                   ) || 
                   rec.get('descripcionProducto')=='ANTIVIRUS' ||
                   rec.get('descripcionProducto')=='EQUIPO PROTEGIDO' || rec.get('descripcionProducto')=='NETLIFECAM' ||
                   rec.get('descripcionProducto')=='NETWIFI' || rec.get('descripcionProducto')=='SMARTWIFI' ||
                   rec.get('descripcionProducto')==='CAMARA IP' || rec.get('descripcionProducto')==="24 HRS GR EXTRA" ||
                   rec.get('descripcionProducto')==="INTERNET SMALL BUSINESS" || rec.get('descripcionProducto')=='APWIFI' ||
                   rec.get('descripcionProducto')==="TELCOHOME"|| rec.get('descripcionProducto')=='NETLIFECAM OUTDOOR'){
                    var permiso = $("#ROLE_151-311");
                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                    //alert(typeof permiso);
                    if(!boolPermiso){ 
                        return 'button-grid-invisible';
                    }
                    else{
                        if ( (rec.get('estado') == "Activo" || rec.get('estado') == "Pre-cancelado") && 
                              rec.get('botones')=="SI") {
                            return 'button-grid-cortarCliente';
                        }
                        else{
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
            else if(rec.get('flujo')=='TNP'){
                if(rec.get('descripcionProducto')=='INTERNET' && !Ext.isEmpty(rec.get('nombrePlan'))){
                    var permisoTnp     = $("#ROLE_151-311");
                    var boolPermisoTnp = (typeof permisoTnp === 'undefined') ? false : (permisoTnp.val() == 1 ? true : false);          
                    if(!boolPermisoTnp){ 
                        return 'button-grid-invisible';
                    }
                    else{
                        if ( (rec.get('estado') == "Activo" || rec.get('estado') == "Pre-cancelado") && 
                              rec.get('botones')=="SI") {
                            return 'button-grid-cortarCliente';
                        }
                        else{
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
             else if(rec.get('flujo')=='TNG'){
                if((rec.get('descripcionProducto')=='INTERNET' && !Ext.isEmpty(rec.get('nombrePlan'))) ||
                        rec.get('descripcionProducto')=='OTROS' || rec.get('descripcionProducto')=='' ){
                    var permisoTng     = $("#ROLE_151-311");
                    var boolPermisoTng = (typeof permisoTng === 'undefined') ? false : (permisoTng.val() == 1 ? true : false);          
                    if(!boolPermisoTng){ 
                        return 'button-grid-invisible';
                    }
                    else{
                       if ( (rec.get('estado') == "Activo" || rec.get('estado') == "Pre-cancelado") && 
                              rec.get('botones')=="SI" ) {
                            return 'button-grid-cortarCliente';
                        }
                        else{
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
            else if(rec.get('prefijoEmpresa')=='TN' && !rec.get('booleanTipoRedGpon') && consultarLoginCam(rec.get('idPersonaEmpresaRol'),rec.get('idProducto')) == false)
            {
                var validaEnlace = (rec.get('tipoEnlace') !== null) ? rec.get('tipoEnlace').substring(0, 9):rec.get('tipoEnlace');
                if( validaEnlace === 'PRINCIPAL' && (
                    rec.get('descripcionProducto')=='L3MPLS'  ||
                    rec.get('descripcionProducto')=='L3MPLS SDWAN'  ||
                    rec.get('descripcionProducto')=='INTMPLS' || 
                    rec.get('descripcionProducto')=='INTERNET SDWAN' || 
                    rec.get('descripcionProducto')=='TUNELIP' || 
                    rec.get('descripcionProducto')=='NETWIFI' || 
                    rec.get('descripcionProducto')==='INTERNET'))
                {                    
                    if(!puedeCortarCliente)
                    { 
                        return 'button-grid-invisible';
                    }
                    else
                    {
                        if ((rec.get('estado') == "Activo") && rec.get('botones')=="SI" && !rec.get('grupo').includes('DATACENTER') &&
                            rec.get('esServicioCamaraSafeCity') != "S"
                            && rec.get('nombreProducto')!== 'CLEAR CHANNEL PUNTO A PUNTO') 
                        {
                            return 'button-grid-cortarCliente';
                        }
                        else
                        { 
                            return 'button-grid-invisible';
                        }
                    }
                }
                
                if( rec.get('descripcionProducto')=='OTROS' ) 
                {                    
                    if (rec.get('boolVisualizarBotonCorte') === 'N')
                    {
                        return 'button-grid-invisible';
                    }
                    if(!puedeCortarCliente)
                    { 
                        return 'button-grid-invisible';
                    }
                    else if(rec.get('nombreProducto') == 'CLEAR CHANNEL PUNTO A PUNTO'
                        &&  rec.get('aprovisioClearChannel') != 'NO')
                    {
                        return 'button-grid-invisible';
                    }
                    else
                    {
                        if ((rec.get('estado') === "Activo") && rec.get('botones')==="SI") 
                        {
                            return 'button-grid-cortarCliente';
                        }
                        else
                        { 
                            return 'button-grid-invisible';
                        }
                    }
                }                
                
                if(rec.get('descripcionProducto')=='INTERNET WIFI')
                {                    
                    if(!puedeCortarClienteWifi)
                    { 
                        return 'button-grid-invisible';
                    }
                    else
                    {
                        if ((rec.get('estado') == "Activo")) 
                        {
                            return 'button-grid-cortarCliente';
                        }
                        else
                        { 
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
        },
        tooltip: 'Cortar Cliente',
        handler: function(grid, rowIndex, colIndex) {
            if (grid.getStore().getAt(rowIndex).data.intServicioFTTxTN !== null) {
                return 'button-grid-invisible';
            }
            if(grid.getStore().getAt(rowIndex).data.flujo=="TTCO"){
                if( (grid.getStore().getAt(rowIndex).data.estado=="Activo" || 
                     grid.getStore().getAt(rowIndex).data.estado=="Pre-cancelado") && 
                     grid.getStore().getAt(rowIndex).data.botones=="SI"){
                    if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="INTERNET"){
                        cortarCliente(grid.getStore().getAt(rowIndex).data,'311');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="CORREO"){
                        cortarServicioCorreo(grid.getStore().getAt(rowIndex).data,'311');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="SMTP AUTENTICADO"){
                        cortarServicioCorreo(grid.getStore().getAt(rowIndex).data,'311');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="DOMINIO"){
                        cortarServicioDominio(grid.getStore().getAt(rowIndex).data,'311');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="HOSTING"){
                        cortarServicioDominio(grid.getStore().getAt(rowIndex).data,'311');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="SITIO WEB"){
                        cortarServicioDominio(grid.getStore().getAt(rowIndex).data,'311');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="IP"){
                        cortarServicioIpPublica(grid.getStore().getAt(rowIndex).data,'311');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="IP PUBLICA"){
                        cortarServicioIpPublica(grid.getStore().getAt(rowIndex).data,'311');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="OTROS"){
                        cortarServicioOtros(grid.getStore().getAt(rowIndex).data,'311');
                    }
                }
            }
            else if(grid.getStore().getAt(rowIndex).data.flujo=="MD"){
                if( (grid.getStore().getAt(rowIndex).data.estado=="Activo" || 
                     grid.getStore().getAt(rowIndex).data.estado=="Pre-cancelado") && 
                     grid.getStore().getAt(rowIndex).data.botones=="SI" ){
                    if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="INTERNET"){
                        cortarServicioMd(grid.getStore().getAt(rowIndex).data,'311');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="CORREO"){
                        cortarServicioCorreo(grid.getStore().getAt(rowIndex).data,'311');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="SMTP AUTENTICADO"){
                        cortarServicioCorreo(grid.getStore().getAt(rowIndex).data,'311');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="DOMINIO"){
                        cortarServicioDominio(grid.getStore().getAt(rowIndex).data,'311');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="HOSTING"){
                        cortarServicioDominio(grid.getStore().getAt(rowIndex).data,'311');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="SITIO WEB"){
                        cortarServicioDominio(grid.getStore().getAt(rowIndex).data,'311');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="IP"){
                        cortarServicioIpPublica(grid.getStore().getAt(rowIndex).data,'311');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="IP PUBLICA"){
                        cortarServicioIpPublica(grid.getStore().getAt(rowIndex).data,'311');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="OTROS" &&
                            !grid.getStore().getAt(rowIndex).data.includes('I. PROTEGIDO') &&
                            !grid.getStore().getAt(rowIndex).data.includes('I. PROTECCION')
                           ){
                        cortarServicioOtros(grid.getStore().getAt(rowIndex).data,'311');
                    }                    
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="ANTIVIRUS"){
                        cortarServicioAntivirus(grid.getStore().getAt(rowIndex).data,'311');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="EQUIPO PROTEGIDO"){
                        cortarServicioEquipoProtegido(grid.getStore().getAt(rowIndex).data,'311');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="NETLIFECAM"){
                        cortarServicioNetlifeCam(grid.getStore().getAt(rowIndex).data,'311');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="NETLIFECAM OUTDOOR"){
                        cortarServicioNetlifeCam(grid.getStore().getAt(rowIndex).data,'311');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="SMARTWIFI"){
                        cortarServicioSmartWifi(grid.getStore().getAt(rowIndex).data,'311');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="APWIFI"){
                        cortarServicioApWifi(grid.getStore().getAt(rowIndex).data,'311');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="NETWIFI"){
                        cortarServicioNetlifeWifi(grid.getStore().getAt(rowIndex).data,'311');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto==="CAMARA IP" 
                            || grid.getStore().getAt(rowIndex).data.descripcionProducto==="24 HRS GR EXTRA"){
                        cortarServicioNetlifeCamStoragePortal(grid.getStore().getAt(rowIndex).data,'311');
                    }
                    //Cortar el servicio de INTERNET SMALL BUSINESS - Fljo de MD. 
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto == "INTERNET SMALL BUSINESS"
                        || grid.getStore().getAt(rowIndex).data.descripcionProducto == "TELCOHOME")
                    {
                            cortarServicioMd(grid.getStore().getAt(rowIndex).data,'311');
                    }
                }
            }
            else if(grid.getStore().getAt(rowIndex).data.flujo=="TNP" && 
                    (grid.getStore().getAt(rowIndex).data.estado=="Activo" || 
                     grid.getStore().getAt(rowIndex).data.estado=="Pre-cancelado") && 
                    grid.getStore().getAt(rowIndex).data.botones=="SI"  &&
                    grid.getStore().getAt(rowIndex).data.descripcionProducto=="INTERNET" &&
                    !Ext.isEmpty(grid.getStore().getAt(rowIndex).data.nombrePlan)
                   ){
                cortarServicioMd(grid.getStore().getAt(rowIndex).data,'311');
            }
            else if(grid.getStore().getAt(rowIndex).data.esServicioCamaraVpnSafeCity === 'S' && 
                    grid.getStore().getAt(rowIndex).data.estado=="Activo"){
                cortarServicioOtros(grid.getStore().getAt(rowIndex).data,'311');
            }
             else if(grid.getStore().getAt(rowIndex).data.flujo=="TNG" && 
                    (grid.getStore().getAt(rowIndex).data.estado=="Activo" || 
                     grid.getStore().getAt(rowIndex).data.estado=="Pre-cancelado") && 
                    grid.getStore().getAt(rowIndex).data.botones=="SI"  &&
                    grid.getStore().getAt(rowIndex).data.descripcionProducto=="INTERNET" &&
                    !Ext.isEmpty(grid.getStore().getAt(rowIndex).data.nombrePlan ) ||
                     grid.getStore().getAt(rowIndex).data.descripcionProducto=="OTROS" ||
                     grid.getStore().getAt(rowIndex).data.descripcionProducto==""
                   ){
                cortarServicioTng(grid.getStore().getAt(rowIndex).data,'311');
            }
            else if(grid.getStore().getAt(rowIndex).data.prefijoEmpresa=="TN" && !grid.getStore().getAt(rowIndex).data.booleanTipoRedGpon)
            {
                if(grid.getStore().getAt(rowIndex).data.descripcionProducto=='INTERNET WIFI' )
                {
                    cortarWifi(grid.getStore().getAt(rowIndex).data,'311');                
                }
                                
                if(grid.getStore().getAt(rowIndex).data.descripcionProducto === "L3MPLS" ||
                   grid.getStore().getAt(rowIndex).data.descripcionProducto === "L3MPLS SDWAN")
                {
                    cortarServicioL3mpls(grid.getStore().getAt(rowIndex).data,'311');
                }
                if( grid.getStore().getAt(rowIndex).data.descripcionProducto==="INTERNET" ||
                    grid.getStore().getAt(rowIndex).data.descripcionProducto==="TUNELIP"
                  )
                {
                    cortarServicioInternetDedicado(grid.getStore().getAt(rowIndex).data,'311');
                }
                if(grid.getStore().getAt(rowIndex).data.descripcionProducto === "INTMPLS" ||
                    grid.getStore().getAt(rowIndex).data.descripcionProducto === "INTERNET SDWAN")
                {
                    cortarServicioIntMpls(grid.getStore().getAt(rowIndex).data,'311');
                }

                if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="NETWIFI")
                {
                    cortarServicioNetlifeWifi(grid.getStore().getAt(rowIndex).data,'311');
                }

                if(grid.getStore().getAt(rowIndex).data.descripcionProducto==="OTROS")
                {
                    cortarServicioOtros(grid.getStore().getAt(rowIndex).data,'311');
                }
            }
        }
    },
    
    //CORTAR TEMPORALMENTE EL SERVICIO
    {
        getClass: function(v, meta, rec) {
            if(rec.get('flujo')=='TTCO'){
                if(rec.get('descripcionProducto')=='INTERNET' || rec.get('descripcionProducto')=='CORREO' || 
                   rec.get('descripcionProducto')=='IP' || rec.get('descripcionProducto')=='IP PUBLICA' || 
                   rec.get('descripcionProducto')=='DOMINIO' || rec.get('descripcionProducto')=='HOSTING' || 
                   rec.get('descripcionProducto')=='SMTP AUTENTICADO' || rec.get('descripcionProducto')=='SITIO WEB'){
                    var permiso = $("#ROLE_151-311");
                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                    //alert(typeof permiso);
                    if(!boolPermiso){ 
                        return 'button-grid-invisible';
                    }
                    else{
                        if ( (rec.get('estado') == "Activo" || rec.get('estado') == "Pre-cancelado") && 
                              rec.get('botones')=="SI") {
                            return 'button-grid-suspensionTemporal';
                        }
                        else{
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
            if(rec.get('flujo')=='MD'){

            }

        },
        tooltip: 'Cortar Temporal Cliente',
        handler: function(grid, rowIndex, colIndex) {
            if(grid.getStore().getAt(rowIndex).data.flujo=="TTCO"){
                if( (grid.getStore().getAt(rowIndex).data.estado=="Activo" || 
                     grid.getStore().getAt(rowIndex).data.estado=="Pre-cancelado") && 
                     grid.getStore().getAt(rowIndex).data.botones=="SI"){
                    cortarTemporalCliente(grid.getStore().getAt(rowIndex).data);
                }
            }
            if(grid.getStore().getAt(rowIndex).data.flujo=="MD"){

            }                                    
        }
    },
    //MIGRACION DE VLAN
    {
        getClass: function(v, meta, rec)
        {
            if((rec.get('estadoSolMigraAnillo')=== null || rec.get('estadoSolMigraAnillo')==='Finalizada') &&
                (rec.get('estadoSolCambioUm') === "Finalizada" || rec.get('estadoSolCambioUm') === null) &&
                (rec.get('estadoSolMigracionVlan')=== null || rec.get('estadoSolMigracionVlan')==='Finalizada') &&
                rec.get('prefijoEmpresa')==='TN'  && rec.get('clienteMigracionVlan') === "S" &&
                rec.get('estado') === "Activo"  && (rec.get('descripcionProducto') === 'L3MPLS') && rec.get('anillo') === "0")
            {
                var permiso = $("#ROLE_151-6678");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                if(boolPermiso)
                {
                    return 'button-grid-migracionVlan';
                }
                else
                {
                    return 'button-grid-invisible';
                }
            }
            else
            {
                return 'button-grid-invisible';
            }
        },
        tooltip: 'Migración de VLAN',
        handler: function(grid, rowIndex, colIndex)
        {
            if (grid.getStore().getAt(rowIndex).data.flujo == "TN")
            {
                cambioMigracionAnillo(grid.getStore().getAt(rowIndex).data,"S");
            }
        }
    },
    //EJECUTAR MIGRACION DE ANILLO
    {
        getClass: function(v, meta, rec)
        {
            if(rec.get('prefijoEmpresa')==='TN' && rec.get('productoEsEnlace') === "SI" && rec.get('clienteMigracionVlan') === "S" &&
               rec.get('estado') === "Activo" && rec.get('estadoSolMigracionVlan')==='Asignada')
            {
                var permiso = $("#ROLE_151-6678");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                if(boolPermiso)
                {
                    return 'button-grid-informacionTecnica';
                }
                else
                {
                    return 'button-grid-invisible';
                }
            }
            else
            {
                return 'button-grid-invisible';
            }
        },
        tooltip: 'Ejecuta Migración de VLAN',
        handler: function(grid, rowIndex, colIndex)
        {
            if(grid.getStore().getAt(rowIndex).data.flujo==="TN")
            {
                if( grid.getStore().getAt(rowIndex).data.estado === "Activo" &&
                    grid.getStore().getAt(rowIndex).data.estadoSolMigracionVlan === "Asignada")
                {
                    if(grid.getStore().getAt(rowIndex).data.descripcionProducto === "L3MPLS")
                    {
                        ejecutaMigracionAnillo(grid.getStore().getAt(rowIndex).data,"S");
                    }
                }
            }
        }
    },
    //CANCELAR SERVICIO
    {
        getClass: function(v, meta, rec) {
            if(rec.get('prefijoEmpresa')=='TN')
            {
                var strProductoPaqHoras       =   rec.get('strValorProductoPaqHoras');
                var strProductoPaqHorasRec    =   rec.get('strValorProductoPaqHorasRec');
                var strDescripcionProducto    =   rec.get('nombreProducto');
                if ((strDescripcionProducto == strProductoPaqHoras ) || (strDescripcionProducto == strProductoPaqHorasRec ) )
                {
                    return 'button-grid-invisible';    
                }
            }
            if (rec.get('intServicioFTTxTN') !== null) {
                return 'button-grid-invisible';
            }
            if(rec.get('flujo')=='TTCO'){
                if(rec.get('descripcionProducto')=='INTERNET' || rec.get('descripcionProducto')=='CORREO' || 
                   rec.get('descripcionProducto')=='IP' || rec.get('descripcionProducto')=='SITIO WEB' || 
                   rec.get('descripcionProducto')=='SMTP AUTENTICADO' || rec.get('descripcionProducto')=='IP PUBLICA' || 
                   rec.get('descripcionProducto')=='DOMINIO' || rec.get('descripcionProducto')=='HOSTING' ||
                   rec.get('descripcionProducto')=='OTROS'){
                    var permiso = $("#ROLE_151-313");
                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                    //alert(typeof permiso);
                    if(!boolPermiso){ 
                        return 'button-grid-invisible';
                    }
                    else{
                        if (   (rec.get('estado') == "Activo" || 
                                rec.get('estado')=="In-Corte" || 
                                rec.get('estado')=="In-Corte-SinEje" || 
                                rec.get('estado')=="Pre-cancelado" || 
                                rec.get('estado')=="In-Temp" || 
                                rec.get('estado')=="In-Temp-SinEje") && 
                                rec.get('botones')=="SI" 
                            ) {
                            return 'button-grid-cancelarCliente';
                        }
                        else{ 
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
            else if(rec.get('flujo')=='MD'){
                if(rec.get('descripcionProducto')=='INTERNET' || rec.get('descripcionProducto')=='CORREO' || 
                   rec.get('descripcionProducto')=='SMTP AUTENTICADO' || rec.get('descripcionProducto')=='DOMINIO' || 
                   rec.get('descripcionProducto')=='HOSTING' || rec.get('descripcionProducto')=='ANTIVIRUS' || 
                   rec.get('descripcionProducto')=='EQUIPO PROTEGIDO' || rec.get('descripcionProducto')=='OTROS' || 
                   rec.get('descripcionProducto')=='NETLIFECAM' || rec.get('descripcionProducto')=='NETWIFI' ||
                   rec.get('descripcionProducto')=='SMARTWIFI' || rec.get('descripcionProducto')=='CAMARA IP' ||
                   rec.get('descripcionProducto')=='24 HRS GR EXTRA' || rec.get('descripcionProducto')==="INTERNET SMALL BUSINESS" ||
                   rec.get('descripcionProducto')==="FOXPREMIUM" || rec.get('descripcionProducto')==="PARAMOUNT" ||
                   rec.get('descripcionProducto')==="NOGGIN" || rec.get('descripcionProducto')=='APWIFI' ||
                   rec.get('descripcionProducto') === 'EXTENDER_DUAL_BAND' || rec.get('descripcionProducto')==="TELCOHOME" ||
                   rec.get('descripcionProducto') === 'WIFI_DUAL_BAND' || rec.get('descripcionProducto') === 'WDB_Y_EDB' ||
                   rec.get('descripcionProducto')=='ECDF' || rec.get('descripcionProducto') === 'GTVPREMIUM'|| 
                   rec.get('descripcionProducto')=='NETLIFECAM OUTDOOR' || rec.get('descripcionProducto') === 'HBO-MAX')
                   {
                    var permiso = $("#ROLE_151-313");
                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                    //alert(typeof permiso);
                    if(!boolPermiso || rec.get('descripcionProducto')=='INTERNET'){ 
                        return 'button-grid-invisible';
                    }
                    else{
                        if (   (rec.get('estado') == "Activo" || 
                                rec.get('estado')=="In-Corte" || 
                                rec.get('estado')=="In-Corte-SinEje" || 
                                rec.get('estado')=="Pre-cancelado" || 
                                rec.get('estado')=="In-Temp" || 
                                rec.get('estado')=="In-Temp-SinEje") && 
                                rec.get('botones')=="SI"
                            ) {
                            return 'button-grid-cancelarCliente';
                        }
                        else{ 
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
            else if(rec.get('flujo')=='TNP'){
                if(rec.get('descripcionProducto')=='INTERNET' && !Ext.isEmpty(rec.get('nombrePlan'))){
                    var permisoTnp = $("#ROLE_151-313");
                    var boolPermisoTnp = (typeof permisoTnp === 'undefined') ? false : (permisoTnp.val() == 1 ? true : false);          
                    if(!boolPermisoTnp){ 
                        return 'button-grid-invisible';
                    }
                    else{
                        if (   (rec.get('estado') == "Activo" || 
                                rec.get('estado')=="In-Corte" || 
                                rec.get('estado')=="In-Corte-SinEje" || 
                                rec.get('estado')=="Pre-cancelado" || 
                                rec.get('estado')=="In-Temp" || 
                                rec.get('estado')=="In-Temp-SinEje") && 
                                rec.get('botones')=="SI"
                            ) {
                            return 'button-grid-cancelarCliente';
                        }
                        else{ 
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
            else if(rec.get('flujo')=='TNG'){ 
                 if((rec.get('descripcionProducto')=='INTERNET' && !Ext.isEmpty(rec.get('nombrePlan'))) || 
                         rec.get('descripcionProducto')=='OTROS' || rec.get('descripcionProducto')=='' ){
                    var permisoTng = $("#ROLE_151-313");
                    var boolPermisoTng = (typeof permisoTng === 'undefined') ? false : (permisoTng.val() == 1 ? true : false);          
                    if(!boolPermisoTng){ 
                        return 'button-grid-invisible';
                    }
                    else{
                        if (   (rec.get('estado') == "Activo" || 
                                rec.get('estado')=="In-Corte" || 
                                rec.get('estado')=="In-Corte-SinEje" || 
                                rec.get('estado')=="Pre-cancelado" || 
                                rec.get('estado')=="In-Temp" || 
                                rec.get('estado')=="In-Temp-SinEje") && 
                                rec.get('botones')=="SI"
                            ) {
                            return 'button-grid-cancelarCliente';
                        }
                        else{ 
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
            else if(rec.get('prefijoEmpresa')=='TN' && !rec.get('booleanTipoRedGpon'))
            {
                if( rec.get('descripcionProducto')=='L3MPLS' ||
                    rec.get('descripcionProducto')=='L3MPLS SDWAN' ||
                    rec.get('descripcionProducto')=='INTMPLS' ||
                    rec.get('descripcionProducto')=='INTERNET SDWAN' ||
                    rec.get('descripcionProducto')=='INTERNET' || 
                    rec.get('descripcionProducto')=='TUNELIP' || 
                    rec.get('descripcionProducto')=='NETWIFI' ||
                    rec.get('descripcionProducto')=='CORREO' ||
                    rec.get('descripcionProducto')=='DOMINIO')                
                {
                    var permiso = $("#ROLE_151-313");
                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                    
                    if(!boolPermiso)
                    { 
                        return 'button-grid-invisible';
                    }
                    else
                    {
                        if ((rec.get('estado') == "Activo" || rec.get('estado')=="In-Corte") && rec.get('botones')=="SI" &&
                             !rec.get('grupo').includes('DATACENTER') && rec.get('esServicioCamaraSafeCity') != "S"
                             && rec.get('nombreProducto')!== 'CLEAR CHANNEL PUNTO A PUNTO') 
                        {
                            return 'button-grid-cancelarCliente';
                        }
                        else
                        { 
                            return 'button-grid-invisible';
                        }
                    }
                }
                
                if(rec.get('descripcionProducto')=='OTROS' ||rec.get('descripcionProducto')=='SAFE ENTRY' )            
                {                    
                    if (rec.get('boolVisualizarBotonCancelar') === 'N')
                    {
                        return 'button-grid-invisible';
                    }
                    if(!puedeCancelarCliente)
                    { 
                        return 'button-grid-invisible';
                    }
                    else if(rec.get('nombreProducto') == 'CLEAR CHANNEL PUNTO A PUNTO'
                        &&  rec.get('aprovisioClearChannel') != 'NO')
                    {
                        return 'button-grid-invisible';
                    }
                    else
                    {
                        if (rec.get('estado') === "Activo"  && rec.get('botones')==="SI" ) 
                        {
                            return 'button-grid-cancelarCliente';
                        }
                        else
                        { 
                            return 'button-grid-invisible';
                        }
                    }
                }

                if (rec.get('descripcionProducto') == 'INTERNET WIFI' ||
                    rec.get('nombreProducto') == 'WIFI Alquiler Equipos'
                )
                {
                    var permiso = $("#ROLE_341-3978");
                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                    
                    if(!boolPermiso)
                    { 
                        return 'button-grid-invisible';
                    }
                    else
                    {
                        if ((rec.get('estado') == "Activo" || rec.get('estado')=="In-Corte") && rec.get('botones')=="SI") 
                        {
                            return 'button-grid-cancelarCliente';
                        }
                        else
                        { 
                            return 'button-grid-invisible';
                        }
                    }
                }                

                if(rec.get('descripcionProducto')=='SEG_VEHICULO')            
                {
                    if (rec.get('boolVisualizarBotonCancelar') === 'N')
                    {
                        return 'button-grid-invisible';
                    }
                    if(!puedeCancelarCliente)
                    {
                        return 'button-grid-invisible';
                    }
                    else
                    {
                        if (rec.get('estado') === "Activo"  && rec.get('botones')==="SI" )
                        {
                            return 'button-grid-cancelarCliente';
                        }
                        else
                        {
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
            else{
                if(rec.get('descripcionProducto')=='OTROS'){
                    var permiso = $("#ROLE_151-313");
                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                    //alert(typeof permiso);
                    if(!boolPermiso){ 
                        return 'button-grid-invisible';
                    }
                    else
                    {
                        if ( rec.get('estado') == "Activo" && rec.get('botones')=="SI") {
                            return 'button-grid-cancelarCliente';
                        }
                        else{ 
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
        },
        tooltip: 'Cancelar Servicio',
        handler: function(grid, rowIndex, colIndex) {
            if(grid.getStore().getAt(rowIndex).data.flujo=="TTCO"){
                if( (   grid.getStore().getAt(rowIndex).data.estado=="Activo" || 
                        grid.getStore().getAt(rowIndex).data.estado=="In-Corte" || 
                        grid.getStore().getAt(rowIndex).data.estado=="In-Corte-SinEje" || 
                        grid.getStore().getAt(rowIndex).data.estado=="Pre-cancelado" || 
                        grid.getStore().getAt(rowIndex).data.estado=="In-Temp" || 
                        grid.getStore().getAt(rowIndex).data.estado=="In-Temp-SinEje") && 
                        grid.getStore().getAt(rowIndex).data.botones=="SI" 
                 ){
                    if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="INTERNET"){
                        cancelarCliente(grid.getStore().getAt(rowIndex).data,'313');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="CORREO"){
                        cancelarServicioCorreo(grid.getStore().getAt(rowIndex).data,'313');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="SMTP AUTENTICADO"){
                        cancelarServicioCorreo(grid.getStore().getAt(rowIndex).data,'313');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="DOMINIO"){
                        cancelarServicioDominio(grid.getStore().getAt(rowIndex).data,'313');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="HOSTING"){
                        cancelarServicioDominio(grid.getStore().getAt(rowIndex).data,'313');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="SITIO WEB"){
                        cancelarServicioDominio(grid.getStore().getAt(rowIndex).data,'313');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="IP"){
                        cancelarServicioIpPublica(grid.getStore().getAt(rowIndex).data,'313');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="IP PUBLICA"){
                        cancelarServicioIpPublica(grid.getStore().getAt(rowIndex).data,'313');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="OTROS"){
                        cancelarServicioOtros(grid.getStore().getAt(rowIndex).data,'313');
                    }

                }
            }
            else if(grid.getStore().getAt(rowIndex).data.flujo=="MD"){
                if( (   grid.getStore().getAt(rowIndex).data.estado=="Activo" || 
                        grid.getStore().getAt(rowIndex).data.estado=="In-Corte" || 
                        grid.getStore().getAt(rowIndex).data.estado=="In-Corte-SinEje" || 
                        grid.getStore().getAt(rowIndex).data.estado=="Pre-cancelado" || 
                        grid.getStore().getAt(rowIndex).data.estado=="In-Temp" || 
                        grid.getStore().getAt(rowIndex).data.estado=="In-Temp-SinEje") && 
                        grid.getStore().getAt(rowIndex).data.botones=="SI" 
                 ){
                    if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="INTERNET"){
                        cancelarServicioMd(grid.getStore().getAt(rowIndex).data,'313');
                    }
                    else if ( arrayProdAdicionales.includes( grid.getStore().getAt(rowIndex).data.descripcionProducto ) ) {
                        cancelacionAnticipada(grid.getStore().getAt(rowIndex).data);
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="CORREO"){
                        cancelarServicioCorreo(grid.getStore().getAt(rowIndex).data,'313');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="SMTP AUTENTICADO"){
                        cancelarServicioCorreo(grid.getStore().getAt(rowIndex).data,'313');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="DOMINIO"){
                        cancelarServicioDominio(grid.getStore().getAt(rowIndex).data,'313');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="HOSTING"){
                        cancelarServicioDominio(grid.getStore().getAt(rowIndex).data,'313');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="SITIO WEB"){
                        cancelarServicioDominio(grid.getStore().getAt(rowIndex).data,'313');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="ANTIVIRUS"){
                        cancelarServicioAntivirus(grid.getStore().getAt(rowIndex).data,'313');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="EQUIPO PROTEGIDO"){
                        cancelarServicioEquipoProtegido(grid.getStore().getAt(rowIndex).data,'313');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="OTROS"){
                        cancelarServicioOtros(grid.getStore().getAt(rowIndex).data,'313');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="FOXPREMIUM"){
                        cancelarServicioOtros(grid.getStore().getAt(rowIndex).data,'313');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="PARAMOUNT"){
                        cancelarServicioOtros(grid.getStore().getAt(rowIndex).data,'313');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="NOGGIN"){
                        cancelarServicioOtros(grid.getStore().getAt(rowIndex).data,'313');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="ECDF"){
                        cancelarServicioOtros(grid.getStore().getAt(rowIndex).data,'313');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="GTVPREMIUM"){
                        cancelarServicioOtros(grid.getStore().getAt(rowIndex).data,'313');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="NETLIFECAM OUTDOOR"){
                        cancelacionAnticipada(grid.getStore().getAt(rowIndex).data);
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="NETWIFI"){
                        cancelarServiceWifi(grid.getStore().getAt(rowIndex).data,'313');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="SMARTWIFI"){
                        cancelarServicioSmartWifi(grid.getStore().getAt(rowIndex).data,'313');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="APWIFI"){
                        cancelarServicioApWifi(grid.getStore().getAt(rowIndex).data,'313');
                    }
		            else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="CAMARA IP" ||
                            grid.getStore().getAt(rowIndex).data.descripcionProducto=="24 HRS GR EXTRA"){
                        cancelarServicioNetlifeCamStorage(grid.getStore().getAt(rowIndex).data,'313');
                        //Ext.Msg.alert('Mensaje ', 'Acción no disponible por el momento. ' +
                          //  'Por favor notifique a Sistemas!');
                    }
                    //Cancelar el servicio de INTERNET SMALL BUSINESS - Fljo de MD. 
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="INTERNET SMALL BUSINESS"
                            || grid.getStore().getAt(rowIndex).data.descripcionProducto=="TELCOHOME")
                    {
                            cancelarServicioMd(grid.getStore().getAt(rowIndex).data,'313');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto === "EXTENDER_DUAL_BAND"){
                        cancelarServicioExtenderDualBand(grid.getStore().getAt(rowIndex).data,'313');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto === "WIFI_DUAL_BAND"){
                        cancelarServicioWifiDualBand(grid.getStore().getAt(rowIndex).data,'313');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto === "WDB_Y_EDB"){
                        cancelarServicioWyAp(grid.getStore().getAt(rowIndex).data,'313');
                    }else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="HBO-MAX"){
                        cancelarServicioOtros(grid.getStore().getAt(rowIndex).data,'313');
                    }
                }
            }
            else if(grid.getStore().getAt(rowIndex).data.flujo=="TNP" &&
                    (grid.getStore().getAt(rowIndex).data.estado=="Activo" || 
                     grid.getStore().getAt(rowIndex).data.estado=="In-Corte" || 
                     grid.getStore().getAt(rowIndex).data.estado=="In-Corte-SinEje" || 
                     grid.getStore().getAt(rowIndex).data.estado=="Pre-cancelado" || 
                     grid.getStore().getAt(rowIndex).data.estado=="In-Temp" || 
                     grid.getStore().getAt(rowIndex).data.estado=="In-Temp-SinEje") && 
                     grid.getStore().getAt(rowIndex).data.botones=="SI" &&
                     grid.getStore().getAt(rowIndex).data.descripcionProducto=="INTERNET" &&
                     !Ext.isEmpty(grid.getStore().getAt(rowIndex).data.nombrePlan)
                   ){
                cancelarServicioMd(grid.getStore().getAt(rowIndex).data,'313');
            }
            else if(grid.getStore().getAt(rowIndex).data.flujo=="TNG" &&
                    (grid.getStore().getAt(rowIndex).data.estado=="Activo" || 
                     grid.getStore().getAt(rowIndex).data.estado=="In-Corte" || 
                     grid.getStore().getAt(rowIndex).data.estado=="In-Corte-SinEje" || 
                     grid.getStore().getAt(rowIndex).data.estado=="Pre-cancelado" || 
                     grid.getStore().getAt(rowIndex).data.estado=="In-Temp" || 
                     grid.getStore().getAt(rowIndex).data.estado=="In-Temp-SinEje") && 
                     grid.getStore().getAt(rowIndex).data.botones=="SI" 
                   ){ 
              cancelarServicioTng(grid.getStore().getAt(rowIndex).data,'313');
            }
            else if(grid.getStore().getAt(rowIndex).data.prefijoEmpresa=="TN" && !grid.getStore().getAt(rowIndex).data.booleanTipoRedGpon)
            {
                if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="L3MPLS" ||
                   grid.getStore().getAt(rowIndex).data.descripcionProducto=="L3MPLS SDWAN")
                {
                    cancelarServicioL3mpls(grid.getStore().getAt(rowIndex).data,'313');
                }
                if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="INTMPLS" ||
                    grid.getStore().getAt(rowIndex).data.descripcionProducto=="INTERNET SDWAN")
                {
                    cancelarServicioIntMpls(grid.getStore().getAt(rowIndex).data,'313');
                }
                if(grid.getStore().getAt(rowIndex).data.descripcionProducto=='INTERNET WIFI' )
                {
                   cancelarWifi(grid.getStore().getAt(rowIndex).data,'313');
                }
                if (grid.getStore().getAt(rowIndex).data.nombreProducto == 'WIFI Alquiler Equipos')
                {
                    if (!grid.getStore().getAt(rowIndex).data.loginAux)
                    {
                        Ext.Msg.alert('Atención', 
                        'El servicio seleccionado no posee <b class="red-text">&#171;Login Auxiliar&#187;</b>, notificar a sistemas.'
                        );
                    } else
                    {
                        cancelarWifi(grid.getStore().getAt(rowIndex).data, '313');
                    }
                }
                if( grid.getStore().getAt(rowIndex).data.descripcionProducto=="INTERNET" ||
                    grid.getStore().getAt(rowIndex).data.descripcionProducto=="TUNELIP"
                  )
                {
                    cancelarServicioInternetDedicado(grid.getStore().getAt(rowIndex).data,'313');
                }
                if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="CORREO")
                {
                    cancelarServicioCorreo(grid.getStore().getAt(rowIndex).data,'313');
                }
                else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="NETWIFI"){
                    cancelarServiceWifi(grid.getStore().getAt(rowIndex).data,'313');
                }
                if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="DOMINIO")
                {
                    cancelarServicioDominio(grid.getStore().getAt(rowIndex).data,'313');
                }
                else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="OTROS"
                        || grid.getStore().getAt(rowIndex).data.descripcionProducto=="SEG_VEHICULO"
                        || grid.getStore().getAt(rowIndex).data.descripcionProducto=="SAFE ENTRY"){
                    cancelarServicioOtros(grid.getStore().getAt(rowIndex).data,'313');
                }
            }
            else{                
                if(grid.getStore().getAt(rowIndex).data.descripcionProducto==="OTROS")
                {
                    cancelarServicioOtros(grid.getStore().getAt(rowIndex).data,'313');
                }
            }
        }
    },
    //MIGRACION DE SECURITY SECURE NG
    {
        getClass: function(v, meta, rec)
        {
            if (rec.get('flujo') == "TN" && rec.get('estado') === "Activo" && rec.get('boolVisualizaBotonNg') === "S")
            {
                if(!strPermisoMigraNg){ 
                    return 'button-grid-invisible';
                }
                else
                {
                    return 'button-grid-cambioCpe';
                }
            }
            return 'button-grid-invisible';
        },
        tooltip: 'Migración de Cpe',
        handler: function(grid, rowIndex, colIndex)
        {
            if (grid.getStore().getAt(rowIndex).data.flujo == "TN")
            {
                migracionCpeSecureNg(grid.getStore().getAt(rowIndex).data);
            }
        }
    },
    //RECONECTAR EL SERVICIO
    {
        getClass: function(v, meta, rec) {
            if(rec.get('flujo')=='TTCO'){
                if(rec.get('descripcionProducto')=='INTERNET' || rec.get('descripcionProducto')=='CORREO' || 
                   rec.get('descripcionProducto')=='IP' || rec.get('descripcionProducto')=='IP PUBLICA' || 
                   rec.get('descripcionProducto')=='DOMINIO' || rec.get('descripcionProducto')=='HOSTING' ||
                   rec.get('descripcionProducto')=='OTROS'){
                    var permiso = $("#ROLE_151-315");
                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                    //alert(typeof permiso);
                    if(!boolPermiso){ 
                        return 'button-grid-invisible';
                    }
                    else{
                        if (    (rec.get('estado') == "In-Corte" || 
                                rec.get('estado') == "In-Corte-SinEje" || 
                                rec.get('estado') == "In-Temp-SinEje" || 
                                rec.get('estado') == "In-Temp") &&
                                rec.get('botones')=="SI"
                           ) {
                            return 'button-grid-reconectarCliente';
                        }
                        else{
                            return 'button-grid-invisible';
                        }
                    }   
                }
            }
            else if(rec.get('flujo')=='MD'){
                if(rec.get('descripcionProducto')=='INTERNET' || rec.get('descripcionProducto')=='CORREO' || 
                   rec.get('descripcionProducto')=='IP' || rec.get('descripcionProducto')=='IP PUBLICA' || 
                   rec.get('descripcionProducto')=='DOMINIO' || rec.get('descripcionProducto')=='HOSTING' ||
                   rec.get('descripcionProducto')=='OTROS' || rec.get('descripcionProducto')=='ANTIVIRUS' ||
                   rec.get('descripcionProducto')=='EQUIPO PROTEGIDO' || rec.get('descripcionProducto')=='NETLIFECAM' ||
                   rec.get('descripcionProducto')=='NETWIFI' || rec.get('descripcionProducto')=='SMARTWIFI' ||
                   rec.get('descripcionProducto')==='CAMARA IP' || rec.get('descripcionProducto')==="24 HRS GR EXTRA" ||
                   rec.get('descripcionProducto')==="INTERNET SMALL BUSINESS" || rec.get('descripcionProducto')=='APWIFI' ||
                   rec.get('descripcionProducto')==="TELCOHOME" || rec.get('descripcionProducto')=='NETLIFECAM OUTDOOR'){
                    var permiso = $("#ROLE_151-315");
                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                    //alert(typeof permiso);
                    if(!boolPermiso){ 
                        return 'button-grid-invisible';
                    }
                    else{
                        if (    (rec.get('estado') == "In-Corte" || 
                                rec.get('estado') == "In-Corte-SinEje" || 
                                rec.get('estado') == "In-Temp-SinEje" || 
                                rec.get('estado') == "In-Temp") &&
                                rec.get('botones')=="SI" && rec.get('servicioInternetMDInCorte') == "N"
                           ) {
                            return 'button-grid-reconectarCliente';
                        }
                        else{
                            return 'button-grid-invisible';
                        }
                    }   
                }
            }
            else if(rec.get('flujo')=='TNP'){
                if(rec.get('descripcionProducto')=='INTERNET' && !Ext.isEmpty(rec.get('nombrePlan'))){
                    var permisoTnp = $("#ROLE_151-315");
                    var boolPermisoTnp = (typeof permisoTnp === 'undefined') ? false : (permisoTnp.val() == 1 ? true : false);          
                    if(!boolPermisoTnp){ 
                        return 'button-grid-invisible';
                    }
                    else{
                        if (    (rec.get('estado') == "In-Corte" || 
                                rec.get('estado') == "In-Corte-SinEje" || 
                                rec.get('estado') == "In-Temp-SinEje" || 
                                rec.get('estado') == "In-Temp") &&
                                rec.get('botones')=="SI"
                           ) {
                            return 'button-grid-reconectarCliente';
                        }
                        else{
                            return 'button-grid-invisible';
                        }
                    }   
                }
            }
            else if(rec.get('flujo')=='TNG'){
               
                if( (rec.get('descripcionProducto')=='INTERNET' && !Ext.isEmpty(rec.get('nombrePlan')) ) ||
                        rec.get('descripcionProducto')=='OTROS' || rec.get('descripcionProducto')=='' ){
                    var permisoTng = $("#ROLE_151-315");
                    var boolPermisoTng = (typeof permisoTng === 'undefined') ? false : (permisoTng.val() == 1 ? true : false);          
                    if(!boolPermisoTng){ 
                        return 'button-grid-invisible';
                    }
                    else{
                        if (    (rec.get('estado') == "In-Corte" || 
                                rec.get('estado') == "In-Corte-SinEje" || 
                                rec.get('estado') == "In-Temp-SinEje" || 
                                rec.get('estado') == "In-Temp") &&
                                rec.get('botones')=="SI"  )
                           {  
                            return 'button-grid-reconectarCliente';
                           }
                        else{
                            return 'button-grid-invisible';
                            }
                    }   
                }
            }
            else if(rec.get('prefijoEmpresa') === 'TN')
            {
                var validaEnlace = (rec.get('tipoEnlace') !== null) ? rec.get('tipoEnlace').substring(0, 9):rec.get('tipoEnlace');
                if( validaEnlace === 'PRINCIPAL' &&
                    (rec.get('descripcionProducto')==='L3MPLS' || rec.get('descripcionProducto')==='INTERNET' || 
                    rec.get('descripcionProducto') === 'INTMPLS' || rec.get('descripcionProducto')==='INTERNET SDWAN'
                    || rec.get('descripcionProducto')==='L3MPLS SDWAN' || rec.get('descripcionProducto')==='NETWIFI'))
                {
                    var permiso = $("#ROLE_151-315");
                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                    
                    if(!boolPermiso)
                    { 
                        return 'button-grid-invisible';
                    }
                    else
                    {
                        if ((rec.get('estado') === "In-Corte" && rec.get('botones') === "SI")) 
                        {
                            return 'button-grid-reconectarCliente';
                        }
                        else
                        { 
                            return 'button-grid-invisible';
                        }
                    }
                }
                if(rec.get('descripcionProducto')=='INTERNET WIFI')
                {
                    var permiso = $("#ROLE_341-3958");
                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                    
                    if(!boolPermiso)
                    { 
                        return 'button-grid-invisible';
                    }
                    else
                    {
                        if ((rec.get('estado') == "In-Corte" && rec.get('botones')=="SI")) 
                        {
                            return 'button-grid-reconectarCliente';
                        }
                        else
                        { 
                            return 'button-grid-invisible';
                        }
                    }
                }
                if (rec.get('descripcionProducto') == 'OTROS')
                {                    
                    if (rec.get('boolVisualizarBotonReactivacion') === 'N')
                    {
                        return 'button-grid-invisible';
                    }
                    
                    if (!puedeReconectarCliente)
                    {
                        return 'button-grid-invisible';
                    } 
                    else
                    {
                        if (rec.get('estado') === "In-Corte" && rec.get('botones') === "SI")
                        {
                            return 'button-grid-reconectarCliente';
                        } else
                        {
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
        },
        tooltip: 'Reconectar Cliente',
        handler: function(grid, rowIndex, colIndex) {
            if(grid.getStore().getAt(rowIndex).data.flujo=="TTCO"){
                if((grid.getStore().getAt(rowIndex).data.estado=="In-Corte" || 
                    grid.getStore().getAt(rowIndex).data.estado=="In-Corte-SinEje" || 
                    grid.getStore().getAt(rowIndex).data.estado=="In-Temp-SinEje" || 
                    grid.getStore().getAt(rowIndex).data.estado=="In-Temp") &&
                    grid.getStore().getAt(rowIndex).data.botones=="SI"){
                    if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="INTERNET"){
                        reconectarCliente(grid.getStore().getAt(rowIndex).data,'315');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="CORREO"){
                        reconectarServicioCorreo(grid.getStore().getAt(rowIndex).data,'315');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="DOMINIO"){
                        reconectarServicioDominio(grid.getStore().getAt(rowIndex).data,'315');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="IP"){
                        reconectarServicioIpPublica(grid.getStore().getAt(rowIndex).data,'315');
                    }      
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="OTROS"){
                        reconectarServicioOtros(grid.getStore().getAt(rowIndex).data,'315');
                    }
                }
            }
            else if(grid.getStore().getAt(rowIndex).data.flujo=="MD"){
                if((grid.getStore().getAt(rowIndex).data.estado=="In-Corte" || 
                    grid.getStore().getAt(rowIndex).data.estado=="In-Corte-SinEje" || 
                    grid.getStore().getAt(rowIndex).data.estado=="In-Temp-SinEje" || 
                    grid.getStore().getAt(rowIndex).data.estado=="In-Temp") &&
                    grid.getStore().getAt(rowIndex).data.botones=="SI"){
                    if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="INTERNET"){
                        reconectarCliente(grid.getStore().getAt(rowIndex).data,'315');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="CORREO"){
                        reconectarServicioCorreo(grid.getStore().getAt(rowIndex).data,'315');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="DOMINIO"){
                        reconectarServicioDominio(grid.getStore().getAt(rowIndex).data,'315');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="IP"){
                        reconectarServicioIpPublica(grid.getStore().getAt(rowIndex).data,'315');
                    }      
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="OTROS"){
                        reconectarServicioOtros(grid.getStore().getAt(rowIndex).data,'315');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="ANTIVIRUS"){
                        reconectarServicioAntivirus(grid.getStore().getAt(rowIndex).data,'315');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="EQUIPO PROTEGIDO"){
                        reconectarServicioEquipoProtegido(grid.getStore().getAt(rowIndex).data,'315');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="NETLIFECAM"){
                        reconectarServicioNetlifeCam(grid.getStore().getAt(rowIndex).data,'315');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="NETLIFECAM OUTDOOR"){
                        reconectarServicioNetlifeCam(grid.getStore().getAt(rowIndex).data,'315');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="NETWIFI"){
                        reconectarServicioNetlifeWifi(grid.getStore().getAt(rowIndex).data,'315');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="SMARTWIFI"){
                        reconectarServicioSmartWifi(grid.getStore().getAt(rowIndex).data,'315');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="APWIFI"){
                        reconectarServicioApWifi(grid.getStore().getAt(rowIndex).data,'315');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto==="CAMARA IP"
                            || grid.getStore().getAt(rowIndex).data.descripcionProducto==="24 HRS GR EXTRA"){
                        reconectarServicioNetlifeCamStoragePortal(grid.getStore().getAt(rowIndex).data,'315');
                    }
                    else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="INTERNET SMALL BUSINESS"
                            || grid.getStore().getAt(rowIndex).data.descripcionProducto=="TELCOHOME"){
                        reconectarCliente(grid.getStore().getAt(rowIndex).data,'315');
                    }
                }
            }
            else if(grid.getStore().getAt(rowIndex).data.flujo=="TNP" &&
                    (grid.getStore().getAt(rowIndex).data.estado=="In-Corte" || 
                     grid.getStore().getAt(rowIndex).data.estado=="In-Corte-SinEje" || 
                     grid.getStore().getAt(rowIndex).data.estado=="In-Temp-SinEje" || 
                     grid.getStore().getAt(rowIndex).data.estado=="In-Temp") &&
                     grid.getStore().getAt(rowIndex).data.botones=="SI" &&
                    grid.getStore().getAt(rowIndex).data.descripcionProducto=="INTERNET" &&
                    !Ext.isEmpty(grid.getStore().getAt(rowIndex).data.nombrePlan)
                   ){
                reconectarCliente(grid.getStore().getAt(rowIndex).data,'315');
            }
            else if(grid.getStore().getAt(rowIndex).data.flujo=="TNG"  &&
                    (grid.getStore().getAt(rowIndex).data.estado=="In-Corte" || 
                     grid.getStore().getAt(rowIndex).data.estado=="In-Corte-SinEje" || 
                     grid.getStore().getAt(rowIndex).data.estado=="In-Temp-SinEje" || 
                     grid.getStore().getAt(rowIndex).data.estado=="In-Temp") &&
                     grid.getStore().getAt(rowIndex).data.botones=="SI" 
                   ){ 
                reconectarClienteTng(grid.getStore().getAt(rowIndex).data,'315');
            }
            else if(grid.getStore().getAt(rowIndex).data.prefijoEmpresa==="TN")
            {
                if(grid.getStore().getAt(rowIndex).data.descripcionProducto==="L3MPLS" || 
                   grid.getStore().getAt(rowIndex).data.descripcionProducto==="L3MPLS SDWAN")
                {
                    reconectarServicioL3mpls(grid.getStore().getAt(rowIndex).data,'315');
                }                        
                if(grid.getStore().getAt(rowIndex).data.descripcionProducto==="INTERNET")
                {
                    reconectarServicioInternetDedicado(grid.getStore().getAt(rowIndex).data,'315');
                }
                if(grid.getStore().getAt(rowIndex).data.descripcionProducto === "INTMPLS" ||
                    grid.getStore().getAt(rowIndex).data.descripcionProducto === "INTERNET SDWAN")
                {
                    reconectarServicioIntMpls(grid.getStore().getAt(rowIndex).data,'315');
                }

                if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="NETWIFI")
                {
                    reconectarServicioNetlifeWifi(grid.getStore().getAt(rowIndex).data,'315');
                }

                if(grid.getStore().getAt(rowIndex).data.descripcionProducto=='INTERNET WIFI' && grid.getStore().getAt(rowIndex).data.estado=="In-Corte")
                {
                    reconectarWifi(grid.getStore().getAt(rowIndex).data,'315');
                }
                if (grid.getStore().getAt(rowIndex).data.descripcionProducto === "OTROS") {
                    reconectarServicioOtros(grid.getStore().getAt(rowIndex).data, '315');
                }
            }
        }
    },
    //RECONFIGURAR PUERTO
    {
        getClass: function(v, meta, rec) 
        {
            if(rec.get('prefijoEmpresa') === 'MD' && rec.get('ultimaMilla') === 'Fibra Optica')
            {
                var permiso = $("#ROLE_151-1557");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                if(!boolPermiso)
                {
                    return 'button-grid-invisible';
                }
                else
                {
                    if ((rec.get('estado') === "EnVerificacion" || rec.get('estado') === "EnPruebas" || rec.get('estado') === "Activo") && 
                        rec.get('descripcionProducto') === "INTERNET" && 
                        (rec.get('modeloElemento') === "EP-3116" || rec.get('modeloElemento') === "MA5608T")) 
                    {
                        return 'button-grid-reconfigurarPuerto';
                    }
                    else
                    {
                        return 'button-grid-invisible';
                    }
                }
            }
            else if(rec.get('prefijoEmpresa') === 'TN' && !rec.get('booleanTipoRedGpon'))
            {
                if((rec.get('descripcionProducto') === 'INTMPLS' || 
                   rec.get('descripcionProducto') === 'INTERNET SDWAN' ||
                   rec.get('descripcionProducto') === 'L3MPLS' ||
                   rec.get('descripcionProducto') === 'L3MPLS SDWAN' ||
                   rec.get('descripcionProducto') === 'TUNELIP' || 
                   rec.get('descripcionProducto') === 'INTERNET') && (rec.get('esServicioCamaraSafeCity') != "S"))
                {
                    var permiso = $("#ROLE_151-1557");
                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                    if(!boolPermiso)
                    {
                        return 'button-grid-invisible';
                    }
                    else
                    {
                        if ((rec.get('estado') == "EnPruebas" || rec.get('estado') == "Activo") && 
                            rec.get('botones') == "SI" && !rec.get('grupo').includes('DATACENTER')
                            && rec.get('nombreProducto')!== 'CLEAR CHANNEL PUNTO A PUNTO') 
                        {
                            return 'button-grid-reconfigurarPuerto';
                        }
                        else
                        {
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
        },
        tooltip: 'Reconfigurar Puerto',
        handler: function(grid, rowIndex) 
        {
            if(grid.getStore().getAt(rowIndex).data.prefijoEmpresa === "MD" &&
               grid.getStore().getAt(rowIndex).data.ultimaMilla === "Fibra Optica")
            {
                if((grid.getStore().getAt(rowIndex).data.estado === "EnVerificacion" || 
                    grid.getStore().getAt(rowIndex).data.estado === "EnPruebas" || 
                    grid.getStore().getAt(rowIndex).data.estado === "Activo") && 
                    grid.getStore().getAt(rowIndex).data.descripcionProducto === "INTERNET")
                {
                    reconfigurarPuertoMd(grid.getStore().getAt(rowIndex).data, grid);
                }
            }
            else if(grid.getStore().getAt(rowIndex).data.prefijoEmpresa === "TN" && !grid.getStore().getAt(rowIndex).data.booleanTipoRedGpon)
            {
                if(grid.getStore().getAt(rowIndex).data.descripcionProducto === "INTERNET" ||
                   grid.getStore().getAt(rowIndex).data.descripcionProducto === "INTMPLS" ||
                   grid.getStore().getAt(rowIndex).data.descripcionProducto === "INTERNET SDWAN" ||
                   grid.getStore().getAt(rowIndex).data.descripcionProducto === "TUNELIP" ||
                   grid.getStore().getAt(rowIndex).data.descripcionProducto === "L3MPLS" ||
                   grid.getStore().getAt(rowIndex).data.descripcionProducto === "L3MPLS SDWAN")
                {
                    reconfigurarServicioIntMpls(grid.getStore().getAt(rowIndex).data,'1557');
                }
            }
        }
    },
    //ASIGNAR IPV4 PÚBLICA
    {
        getClass: function(v, meta, rec) 
        {
            if(rec.get('prefijoEmpresa')=='MD' && rec.get('ultimaMilla')=='Fibra Optica' && rec.get('ipv4Publico')==null)
            {
                var permiso = $("#ROLE_151-5457");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                if(!boolPermiso)
                {
                    return 'button-grid-invisible';
                }
                else
                {
                    if ((rec.get('estado') == "EnVerificacion" || rec.get('estado') == "EnPruebas" || rec.get('estado') == "Activo") && 
                        rec.get('descripcionProducto') == "INTERNET" && 
                        (rec.get('modeloElemento') == "EP-3116" || rec.get('modeloElemento') == "MA5608T")) 
                    {
                        return 'button-grid-ipv4Publica';
                    }
                    else
                    {
                        return 'button-grid-invisible';
                    }
                }
            }
        },
        tooltip: 'Asignar Ipv4 Publica',
        handler: function(grid, rowIndex, colIndex) 
        {
            if(grid.getStore().getAt(rowIndex).data.prefijoEmpresa=="MD" &&
               grid.getStore().getAt(rowIndex).data.ultimaMilla=="Fibra Optica" &&
               grid.getStore().getAt(rowIndex).data.ipv4Publico==null)
            {
                if((grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion" || 
                    grid.getStore().getAt(rowIndex).data.estado=="EnPruebas" || 
                    grid.getStore().getAt(rowIndex).data.estado=="Activo") && 
                    grid.getStore().getAt(rowIndex).data.descripcionProducto=="INTERNET")
                {
                    asignarIpv4Publico(grid.getStore().getAt(rowIndex).data, grid);
                }
            }
        }
    },
    //CAMBIO DE ULTIMA MILLA ( TN )
    {
        getClass: function(v, meta, rec) 
        {
            if(rec.get('prefijoEmpresa')==='TN' && rec.get('productoEsEnlace') === "SI" && rec.get('estadoSolCambioUm') === "Asignada" &&
              ( rec.get('estado') === "Activo" || rec.get('estado') === "EnPruebas" ) 
              )
            {
                var permiso = $("#ROLE_151-3779");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                if(!boolPermiso)
                {
                    return 'button-grid-invisible';
                }
                else
                {                    
                    return 'button-grid-cambiarPuerto';                    
                }
            }
        },
        tooltip: 'Ejecutar Cambio de Ultima Milla',
        handler: function(grid, rowIndex, colIndex) 
        {                        
            //Funcion para realizar cambio de ultima milla
            ejecutaCambioUM(grid.getStore().getAt(rowIndex).data);
        }
    },//FIN CAMBIO DE ULTIMA MILLA 05-04-2016 - AS
    //MIGRACION A ANILLO - CAMBIO LOGICO
    {
        getClass: function(v, meta, rec) 
        {
            if( rec.get('prefijoEmpresa')==='TN' && !rec.get('booleanTipoRedGpon') && 
                rec.get('productoEsEnlace') === "SI" && 
                rec.get('estado') === "Activo"  && 
                (
                    (rec.get('estadoSolMigraAnillo')=== null || rec.get('estadoSolMigraAnillo')==='Finalizada') && 
                    (rec.get('estadoSolCambioUm') === "Finalizada" || rec.get('estadoSolCambioUm') === null)
                ) &&  
                (rec.get('descripcionProducto') === 'L3MPLS'  || rec.get('descripcionProducto') === 'INTERNET' 
                || rec.get('descripcionProducto') === 'L3MPLS SDWAN')
                && !rec.get('grupo').includes('DATACENTER') && (rec.get('esServicioCamaraSafeCity') != 'S')
              )
            {
                if(puedeCambiarUmProgramada)
                {
                    return 'button-grid-cambiarUmAnillo';
                }
                else
                {                    
                    return 'button-grid-invisible';
                }
            }
            else
            {                    
                return 'button-grid-invisible';
            }
        },
        tooltip: 'Migración a Anillo',
        handler: function(grid, rowIndex, colIndex) 
        {                        
            if (grid.getStore().getAt(rowIndex).data.flujo == "TN" && !grid.getStore().getAt(rowIndex).data.booleanTipoRedGpon)
            {
                if(grid.getStore().getAt(rowIndex).data.descripcionProducto==="INTERNET")
                {
                    showMigrarAnilloInternetMPLS(grid.getStore().getAt(rowIndex).data);
                }
                else
                {
                    cambioMigracionAnillo(grid.getStore().getAt(rowIndex).data,"N");
                }
            }                                               
        }
    },
    //REVERSO SOLICITUD DE MIGRACION DE ANILLO O VLAN
    {
        getClass: function(v, meta, rec)
        {
            var permiso = $("#ROLE_151-7277");
            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
            if( boolPermiso && rec.get('prefijoEmpresa')==='TN' && rec.get('productoEsEnlace') === "SI" && 
                rec.get('estado') === "Activo" && rec.get('estadoSolMigraAnillo')==='Asignada' )
            {
                return 'button-grid-verReversoSolicitudMigracion';
            }
            else
            {
                return 'button-grid-invisible';
            }
        },
        tooltip: 'Reversar Solicitud Migración a Anillo',
        handler: function(grid, rowIndex, colIndex)
        {
            //Funcion para reversar la solicitud de migración anillo o vlan
            reversarSolicitudMigracionAnillo(grid.getStore().getAt(rowIndex).data);
        }
    },
    //SOLICITUD DE CAMBIO UM
    {
        getClass: function(v, meta, rec) 
        {
            if( rec.get('prefijoEmpresa')==='TN' && 
                rec.get('productoEsEnlace') === "SI" && 
                ( rec.get('estado') === "Activo" || rec.get('estado') === "EnPruebas" ) && 
                (
                    (rec.get('estadoSolMigraAnillo')=== null || rec.get('estadoSolMigraAnillo')==='Finalizada') && 
                    (rec.get('estadoSolCambioUm') === "Finalizada" || rec.get('estadoSolCambioUm') === null)
                ) &&
                (
                    rec.get('descripcionProducto') === 'L3MPLS' ||
                    rec.get('descripcionProducto') === 'L3MPLS SDWAN' ||
                    rec.get('descripcionProducto') === 'INTERNET' || 
                    rec.get('descripcionProducto') === 'INTMPLS' ||
                    rec.get('descripcionProducto') === 'INTERNET SDWAN'
                )
                && !rec.get('grupo').includes('DATACENTER') && rec.get('esServicioCamaraSafeCity') != "S"
              )
            {
                if(puedeCambiarUmProgramada && !rec.get('booleanTipoRedGpon'))
                {
                    return 'button-grid-solicitudCambioUM';
                }
                else
                {                    
                    return 'button-grid-invisible';
                }
            }
        },
        tooltip: 'Solicitar Cambio de Ultima Milla',
        handler: function(grid, rowIndex, colIndex) 
        {                        
            //Funcion para solicitar cambio de ultima milla
            crearSolicitudCambioUM(grid.getStore().getAt(rowIndex).data);
        }
    },
    //REVERSO SOLICITUD DE CAMBIO UM
    {
        getClass: function(v, meta, rec)
        {
            var permiso = $("#ROLE_151-7257");
            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
            if( boolPermiso && rec.get('prefijoEmpresa')==='TN' && 
                ( rec.get('estadoSolCambioUm') === "AsignadoTarea" || rec.get('estadoSolCambioUm') === "Asignada" || 
                  rec.get('estadoSolCambioUm') === "FactibilidadEnProceso" )
              )
            {
                return 'button-grid-verReversoSolicitudMigracion';
            }
            else
            {
                return 'button-grid-invisible';
            }
        },
        tooltip: 'Reversar Solicitud Cambio de Ultima Milla',
        handler: function(grid, rowIndex, colIndex)
        {
            //Funcion para reversar la solicitud cambio de ultima milla
            reversarSolicitudCambioUM(grid.getStore().getAt(rowIndex).data);
        }
    },
    //EJECUTAR MIGRACION DE ANILLO
    {
        getClass: function(v, meta, rec) 
        {
            if(rec.get('prefijoEmpresa')==='TN' && rec.get('productoEsEnlace') === "SI" &&
               rec.get('estado') === "Activo" && rec.get('estadoSolMigraAnillo')==='Asignada')
            {
                if(puedeCambiarUmProgramada)
                {
                    return 'button-grid-informacionTecnica';
                }
                else
                {                    
                    return 'button-grid-invisible';
                }
            }
        },
        tooltip: 'Ejecuta Migracion Anillo',
        handler: function(grid, rowIndex, colIndex)
        {   
            if(grid.getStore().getAt(rowIndex).data.flujo==="TN")
            {
                if( grid.getStore().getAt(rowIndex).data.estado === "Activo" && 
                    grid.getStore().getAt(rowIndex).data.estadoSolMigraAnillo === "Asignada")
                {
                    if(grid.getStore().getAt(rowIndex).data.descripcionProducto === "L3MPLS" || 
                    grid.getStore().getAt(rowIndex).data.descripcionProducto === "L3MPLS SDWAN"){
                        ejecutaMigracionAnillo(grid.getStore().getAt(rowIndex).data,"N");
                    } else if(grid.getStore().getAt(rowIndex).data.descripcionProducto === "INTMPLS" ||
                        grid.getStore().getAt(rowIndex).data.descripcionProducto === "INTERNET SDWAN"){
                        showEjecutarMigrarAnilloInternetMPLS(grid.getStore().getAt(rowIndex).data);
                    }
                }
            }
        }
    },
    //AGREGAR SERVICIO A EDIFICIO PSEUDOPE
    {
        getClass: function(v, meta, rec) 
        {
            if(rec.get('prefijoEmpresa')==='TN' && 
               rec.get('seMigraAPseudoPe') === "SI" && 
               (rec.get('nombreProducto') === "L3MPLS" || rec.get('nombreProducto') === "L3MPLS SDWAN") &&
              ( rec.get('estado') === "Activo" || rec.get('estado') === "EnPruebas" ) 
              )
            {
                var permiso = $("#ROLE_151-5057");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                if(!boolPermiso)
                {
                    return 'button-grid-invisible';
                }
                else
                {                    
                    return 'button-grid-migrarPseudoPe';                    
                }
            }
        },
        tooltip: 'Agregar Servicio a Edificio PseudoPE',
        handler: function(grid, rowIndex, colIndex) 
        {                        
            //Funcion para realizar cambio de ultima milla
            migrarAPseudoPe(grid.getStore().getAt(rowIndex).data);
        }
    },
    
    //--------------------------------------------
    
    //CAMBIO DE MAC TN POR PUERTO
    {
        getClass: function(v, meta, rec) 
        {
            if(rec.get('prefijoEmpresa')==='TN' && rec.get('productoEsEnlace') === "SI" &&
               rec.get('estado') === "Activo" 
               && (!rec.get('grupo').includes('DATACENTER') && rec.get('descripcionProducto') !== "INTERNET SMALL BUSINESS"
               && rec.get('descripcionProducto') !== "TELCOHOME") && rec.get('nombreProducto') !== "Cableado Estructurado"
               && !rec.get('booleanTipoRedGpon') && rec.get('esServicioCamaraSafeCity') != "S")
            {
                var permiso = $("#ROLE_151-4697");                
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                if(!boolPermiso)
                {
                    return 'button-grid-invisible';
                }
                else
                {
                    return 'button-grid-verMacs';                  
                }                      
            }
        },
        tooltip: 'Actualizar MAC de Servicio',
        handler: function(grid, rowIndex, colIndex)
        {   
            realizarCambioMac(grid.getStore().getAt(rowIndex).data);
        }
    },
    
     //CONSULTA DE ENLACES DE CADA SERVICIO
    {
        getClass: function(v, meta, rec) 
        {
            if(rec.get('prefijoEmpresa')==='TN' && rec.get('productoEsEnlace') === "SI" &&
               rec.get('estado') === "Activo" 
               && (!rec.get('grupo').includes('DATACENTER') && rec.get('descripcionProducto') !== "INTERNET SMALL BUSINESS"
                    && rec.get('descripcionProducto') !== "TELCOHOME") && rec.get('nombreProducto') !== "Cableado Estructurado" &&
                    rec.get('esServicioCamaraSafeCity') !== "S")
            {
                var permiso = $("#ROLE_151-4717");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                if(!boolPermiso)
                {
                    return 'button-grid-invisible';
                }
                else
                {
                    return 'button-grid-consultarEnlaces';           
                }               
            }
        },
        tooltip: 'Consultar Enlaces del Servicio',
        handler: function(grid, rowIndex, colIndex)
        {   
            consultarEnlacesServicio(grid.getStore().getAt(rowIndex).data);
        }
    },
    
    //ACTIVAR IP FIJA
    {
        getClass: function(v, meta, rec) {
            if(rec.get('prefijoEmpresa')=='MD'){
                if(rec.get('descripcionProducto')=='IP'){
                    var permiso = $("#ROLE_151-1297");
                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                    if(!boolPermiso){
                        return 'button-grid-invisible';
                    }
                    else{
                        if ((rec.get('estado') == "Asignada") && rec.get('descripcionProducto') == "IP" && rec.get('botones')=='SI') {
                            return 'button-grid-informacionTecnica';
                        }
                        else{
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
            if (rec.get('prefijoEmpresa') === 'TN' || rec.get('prefijoEmpresa') === 'TNP') {
                var permiso = $("#ROLE_151-1297");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                if (!boolPermiso) {
                    return 'button-grid-invisible';
                } else {
                    if (rec.get('estado') === "Asignada" && rec.get('descripcionProducto') === 'IPSB') {
                        return 'button-grid-informacionTecnica';
                    } else {
                        return 'button-grid-invisible';
                    }
                }
            }
        },
        tooltip: 'Activar Ip(s) Fija(s)',
        handler: function(grid, rowIndex, colIndex) {
            if( grid.getStore().getAt(rowIndex).data.estado=="Asignada" 
                && ( (  grid.getStore().getAt(rowIndex).data.prefijoEmpresa === "MD" 
                        && grid.getStore().getAt(rowIndex).data.descripcionProducto === "IP")
                    || ( (grid.getStore().getAt(rowIndex).data.prefijoEmpresa === "TN" || 
                          grid.getStore().getAt(rowIndex).data.prefijoEmpresa === "TNP"
                         )
                        && grid.getStore().getAt(rowIndex).data.descripcionProducto === "IPSB")))
            {
                if(grid.getStore().getAt(rowIndex).data.descripcionProducto === "IPSB"
                   && grid.getStore().getAt(rowIndex).data.strEsVerificarSerRequerido === "S")
                {
                    Ext.Msg.alert('Validacion ',grid.getStore().getAt(rowIndex).data.strMensajeServicioRequerido);
                }
                else
                {
                    activarIpFijaMD(grid.getStore().getAt(rowIndex).data, grid);
                }
            }
        }
    },
    //CAMBIAR MAC IP FIJA
    {
        getClass: function(v, meta, rec) {
            if(rec.get('prefijoEmpresa')=='MD'){
                if(rec.get('descripcionProducto')=='IP'){
                    var permiso = $("#ROLE_151-1298");
                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                    if(!boolPermiso){
                        return 'button-grid-invisible';
                    }
                    else{
                        if ((rec.get('estado') == "Activo") && rec.get('descripcionProducto') == "IP" && rec.get('botones')=='SI' && rec.get('strEsIpWan')=='N') {
                            return 'button-grid-cambiarPuerto';
                        }
                        else{
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
            if (rec.get('prefijoEmpresa') === 'TN' || rec.get('prefijoEmpresa') === 'TNP') {
                var permiso = $("#ROLE_151-1298");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                if (!boolPermiso) {
                    return 'button-grid-invisible';
                } else {
                    if (rec.get('estado') === "Activo" && rec.get('descripcionProducto') === 'IPSB' && rec.get('botones') === 'SI') {
                        return 'button-grid-cambiarPuerto';
                    } else {
                        return 'button-grid-invisible';
                    }
                }
            }
        },
        tooltip: 'Cambiar Mac Ip(s) Fija(s)',
        handler: function(grid, rowIndex, colIndex) {
            if(grid.getStore().getAt(rowIndex).data.estado === "Activo"
                && ((grid.getStore().getAt(rowIndex).data.prefijoEmpresa === "MD" 
                    && grid.getStore().getAt(rowIndex).data.descripcionProducto === "IP")
                    || ((grid.getStore().getAt(rowIndex).data.prefijoEmpresa === "TN" ||
                         grid.getStore().getAt(rowIndex).data.prefijoEmpresa === "TNP"
                        )
                        && grid.getStore().getAt(rowIndex).data.descripcionProducto === "IPSB"))){
                cambiarMacIpFijaMD(grid.getStore().getAt(rowIndex).data, grid);
            }                                    
        }
    },
    //Cancelar IP FIJA
    {
        getClass: function(v, meta, rec) {
            if(rec.get('prefijoEmpresa')=='MD'){
                if(rec.get('descripcionProducto')=='IP'){
                    var permiso = $("#ROLE_151-1299");
                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                    if(!boolPermiso){
                        return 'button-grid-invisible';
                    }
                    else{
                        if ((rec.get('estado') == "Activo") && rec.get('descripcionProducto') == "IP" && rec.get('botones')=='SI') {
                            return 'button-grid-cancelarCliente';
                        }
                        else{
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
            if (rec.get('prefijoEmpresa') === 'TN' || rec.get('prefijoEmpresa') === 'TNP') {
                var permiso = $("#ROLE_151-1299");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                if (!boolPermiso) {
                    return 'button-grid-invisible';
                } else {
                    if (rec.get('estado') === "Activo" && rec.get('descripcionProducto') === "IPSB" && rec.get('botones') === 'SI') {
                        return 'button-grid-cancelarCliente';
                    } else {
                        return 'button-grid-invisible';
                    }
                }
            }
        },
        tooltip: 'Cancelar Ip(s) Fija(s)',
        handler: function(grid, rowIndex, colIndex) {
            if(grid.getStore().getAt(rowIndex).data.estado === "Activo"
                && ((grid.getStore().getAt(rowIndex).data.prefijoEmpresa === "MD" 
                    && grid.getStore().getAt(rowIndex).data.descripcionProducto === "IP")
                    || ((grid.getStore().getAt(rowIndex).data.prefijoEmpresa === "TN" ||
                         grid.getStore().getAt(rowIndex).data.prefijoEmpresa === "TNP"
                        )
                        && grid.getStore().getAt(rowIndex).data.descripcionProducto === "IPSB"))){
                cancelarIpFijaMD(grid.getStore().getAt(rowIndex).data, '313');
            }                                    
        }
    },
    //MIGRAR IP FIJA
    {
        getClass: function(v, meta, rec) {
            if(rec.get('prefijoEmpresa')=='MD'){
                if(rec.get('descripcionProducto')=='IP'){
                    var permiso = $("#ROLE_151-1107");
                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                    if(!boolPermiso){
                        return 'button-grid-invisible';
                    }
                    else{
                        if ((rec.get('estado') == "Activo") && rec.get('migrado')=="NO") {
                            return 'button-grid-informacionTecnica';
                        }
                        else{
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
        },
        tooltip: 'Migrar Ip Fija',
        handler: function(grid, rowIndex, colIndex) {
            if(grid.getStore().getAt(rowIndex).data.prefijoEmpresa=="MD"){
                if((grid.getStore().getAt(rowIndex).data.estado=="Activo") && 
                    grid.getStore().getAt(rowIndex).data.descripcionProducto=="IP" && 
                    grid.getStore().getAt(rowIndex).data.migrado=="NO"){
                    migrarIpHuawei(grid.getStore().getAt(rowIndex).data);
                }

            }                                    
        }
    },
    //MIGRACION HUAWEI
    {
        getClass: function(v, meta, rec) 
        { 
            if(rec.get('flujo')=='MD'){
                var permiso = $("#ROLE_151-1107");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);   
                if(!boolPermiso){ 
                    return 'button-grid-invisible';
                }
                else{
                    if (rec.get('estado') == "Activo" && 
                        (rec.get('descripcionProducto') == 'INTERNET' || rec.get('descripcionProducto') == 'INTERNET SMALL BUSINESS'
                        || rec.get('descripcionProducto') == 'TELCOHOME')
                         && rec.get('tieneSolicitudMigracion')!=null) {
                        return 'button-grid-verCircuitoVirtual';
                    }
                    else{
                        return 'button-grid-invisible';
                    }
                }
            }//if(rec.get('flujo')=='MD')
            else if(rec.get('flujo')=='TN'){
                permiso = $("#ROLE_151-846");
                boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                if(!boolPermiso){
                    return 'button-grid-invisible';
                }
                else if (rec.get('estado') == "Activo" && rec.get('strMigrarSwPoe') == "S"
                       && ( rec.get('booleanTipoRedGpon') || rec.get('esServicioCamaraVpnSafeCity') === 'S' ) ){
                    return 'button-grid-informacionTecnica';
                }
                else
                {
                    return 'button-grid-invisible';
                }
            }
        },
        tooltip: 'Migrar Servicio',
        handler: function(grid, rowIndex, colIndex) 
        {
            if(grid.getStore().getAt(rowIndex).data.flujo=="MD"){
                if(grid.getStore().getAt(rowIndex).data.tieneSolicitudMigracion!=null){
                    migracionHuawei(grid.getStore().getAt(rowIndex).data);
                }
            }
            else if(grid.getStore().getAt(rowIndex).data.flujo=="TN" && grid.getStore().getAt(rowIndex).data.estado=="Activo"
                    && (grid.getStore().getAt(rowIndex).data.booleanTipoRedGpon
                        || grid.getStore().getAt(rowIndex).data.esServicioCamaraVpnSafeCity === 'S')
                    && grid.getStore().getAt(rowIndex).data.strMigrarSwPoe == "S"){
                        activarServiciosSafeCityGpon(grid.getStore().getAt(rowIndex).data, grid);
            }
        }
    },
    //MIGRACION TUNEL IP A DATOS MPLS
    {
        getClass: function(v, meta, rec) 
        { 
            if(rec.get('flujo')=='TN'){
                var permiso = $("#ROLE_151-1107");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);   
                if(!boolPermiso){ 
                    return 'button-grid-invisible';
                }
                else{
                    if (rec.get('estado') == "Activo" && 
                        rec.get('descripcionProducto')=='TUNELIP') {
                        return 'button-grid-cambiarEstado';
                    }
                    else{
                        return 'button-grid-invisible';
                    }
                }
            }//if(rec.get('flujo')=='TN')
        },
        tooltip: 'Migrar Tunel Ip a Datos MPLS',
        handler: function(grid, rowIndex, colIndex) 
        {
            if(grid.getStore().getAt(rowIndex).data.flujo=="TN"){
                if(grid.getStore().getAt(rowIndex).data.descripcionProducto === "TUNELIP" && 
                   grid.getStore().getAt(rowIndex).data.estado === "Activo"){
                    migrarDataTunelAMpls(grid.getStore().getAt(rowIndex).data);
                }
            }
        }
    },
    //EJECUTAR MIGRACION TUNEL IP A DATOS MPLS
    {
        getClass: function(v, meta, rec) 
        { 
            if(rec.get('flujo')=='TN'){
                var permiso = $("#ROLE_151-1107");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);   
                if(!boolPermiso){ 
                    return 'button-grid-invisible';
                }
                else{
                    if (rec.get('estado') === "Activo" && 
                        (rec.get('descripcionProducto')==='L3MPLS' || rec.get('descripcionProducto')==='L3MPLS SDWAN') && 
                        rec.get('estadoSolMigracionTunel')==='Asignada') {
                        return 'button-grid-informacionTecnica';
                    }
                    else{
                        return 'button-grid-invisible';
                    }
                }
            }//if(rec.get('flujo')=='TN')
        },
        tooltip: 'Ejecutar Migracion Tunel Ip a L3MPLS',
        handler: function(grid, rowIndex, colIndex) 
        {
            if(grid.getStore().getAt(rowIndex).data.flujo==="TN"){
                if((grid.getStore().getAt(rowIndex).data.descripcionProducto === "L3MPLS" || 
                    grid.getStore().getAt(rowIndex).data.descripcionProducto === "L3MPLS SDWAN") && 
                   grid.getStore().getAt(rowIndex).data.estado === "Activo" && 
                   grid.getStore().getAt(rowIndex).data.estadoSolMigracionTunel === "Asignada"){
                    ejecutarMigracion(grid.getStore().getAt(rowIndex).data);
                }
            }
        }
    },
    //RESERVO SOLICITUD MIGRACION HUAWEI
    {
        getClass: function(v, meta, rec) 
        {
            if(rec.get('flujo')=='MD'){
                var permiso = $("#ROLE_151-2277");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);   
                if(!boolPermiso){ 
                    return 'button-grid-invisible';
                }
                else{
                    if (rec.get('estado') == "Activo" && 
                        rec.get('descripcionProducto')=='INTERNET' && 
                        rec.get('tieneSolicitudMigracion')!=null)
                    {
                        if (Ext.getCmp('txtLogin').value != "")
                        {
                            alert("Tiene una solicitud de migración pendiente de finalizar.");
                        }
                        return 'button-grid-verReversoSolicitudMigracion';
                    }
                    else{
                            return 'button-grid-invisible';
                    }
                }
            }
        },
        tooltip: 'Reversar Solicitud de migracion',
        handler: function(grid, rowIndex, colIndex) 
        {
            if(grid.getStore().getAt(rowIndex).data.flujo=="MD"){
                if(grid.getStore().getAt(rowIndex).data.tieneSolicitudMigracion!=null){
                    reversarSolicitudMigracion(grid.getStore().getAt(rowIndex).data);
                }
            }
        }
    },    
    //ingresar solo elemento cliente
    {
        getClass: function(v, meta, rec) 
        {
            
            if(rec.get('prefijoEmpresa')=='TN'){
                
                var permiso = $("#ROLE_341-4817");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);   
                if(!boolPermiso){ 
                    return 'button-grid-invisible';
                }
                else
                {
                    if ((rec.get('estado') == "Activo") &&  rec.get('descripcionProducto')=='INTERNET WIFI')
                    {
                         return 'button-grid-verInterface';
                    }
                    else{
                            return 'button-grid-invisible';
                    }
                }
            }
        },
        tooltip: 'Ingreso de elemento cliente',
        handler: function(grid, rowIndex, colIndex) 
        {
                ingresarElementoCliente(grid.getStore().getAt(rowIndex).data);
        }

    },
    /*Activación y Registro Elemento Alquiler*/
    {
        getClass: function(v, meta, rec)
        {
            if(rec.get('prefijoEmpresa')=='TN'){
                var permiso = $("#ROLE_151-6637");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                if(!boolPermiso){
                    return 'button-grid-invisible';
                }
                else
                {
                    if (typeof rec.get('objParametrosDet') !== 'undefined')
                    {                                                                                                                                                   
                        if (((rec.get('estado') == "Asignada") || (rec.get('estado') == "Pendiente")) &&
                            rec.get('descripcionProducto') == rec.get('objParametrosDet').descripcionProducto &&
                            rec.get('nombreProducto') == rec.get('objParametrosDet').nombreProducto &&
                            !rec.get('grupo').includes('DATACENTER'))
                        {
                            return 'button-grid-telcos wifi-router';
                        }
                        else{
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
        },
        tooltip: 'Activación y Registro Elemento Alquiler',
        handler: function(grid, rowIndex, colIndex)
        {
            ingresarElementoClienteAlquiler(grid.getStore().getAt(rowIndex).data);
        }
    },
    /*Ver data técnica de Wifi Alquiler de Equipos.*/
    {
        getClass: function(v, meta, rec)
        {
            if(rec.get('prefijoEmpresa')=='TN'){
                var permiso = $("#ROLE_151-6638");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                if(!boolPermiso){
                    return 'button-grid-invisible';
                }
                else
                {
                    if (rec.get('nombreProducto') == 'WIFI Alquiler Equipos' && rec.get('estado') == "Activo")
                    {
                        return 'button-grid-telcos detalle-tecnico ';
                    }
                    else{
                        return 'button-grid-invisible';
                    }
                }
            }
        },
        tooltip: 'Ver detalle Wifi Alquiler Equipos',
        handler: function(grid, rowIndex, colIndex)
        {
            verInformacionTecnicaWifiAlquiler(grid.getStore().getAt(rowIndex).data);
        }
    },
    {
        /*Activación y regitro de servicios sin flujo.*/
        getClass: function (v, meta, rec)
        {
            if (rec.get('prefijoEmpresa') == 'TN')
            {
                var permiso = $("#ROLE_151-846");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

                /*Definimos variables para verificar si el servicio padre simultaneo se encuentra activo.*/
                let servicioPadreSimultaneo = rec.get('servicioPadreSimultaneo');

                if (boolPermiso &&
                    (typeof rec.get('boolRequiereRegistro') !== 'undefined' && typeof rec.get('boolTieneFlujo') !== 'undefined'))
                {
                    if (rec.get('boolRequiereRegistro') && !rec.get('boolTieneFlujo') && rec.get('estado') === 'Pendiente'
                    && (servicioPadreSimultaneo == null || servicioPadreSimultaneo.estadoServicio === 'Activo'))
                    {
                        return 'button-grid-telcos register';
                    } else
                    {
                        return 'button-grid-invisible';
                    }
                    
                }
            }
            else if(rec.get('prefijoEmpresa') == 'MD' && rec.get('estado') === 'Asignada' && rec.get('productoPermitidoRegistroEle') == "S")
            {
                var permisoBtn = $("#ROLE_151-846");
                var boolPermisoBtn = (typeof permisoBtn === 'undefined') ? false : (permisoBtn.val() == 1 ? true : false);
                if(!boolPermisoBtn)
                {
                    return 'button-grid-invisible';
                }
                else
                {
                    return 'button-grid-telcos register';
                }
            }
            else
            {
                return 'button-grid-invisible';
            }
        
        },
        tooltip: 'Activación y Registro Elemento',
        handler: function(grid, rowIndex, colIndex)
        {
            if((grid.getStore().getAt(rowIndex).data.flujo=="MD") &&
                (grid.getStore().getAt(rowIndex).data.estado==="Asignada") && 
                ((grid.getStore().getAt(rowIndex).data.descripcionProducto==="NETLIFECAM") ||
                (grid.getStore().getAt(rowIndex).data.descripcionProducto==="NETLIFECAM OUTDOOR")) &&
                (grid.getStore().getAt(rowIndex).data.tipoOrden==="T"))
            {
                confirmarActElemTrasladado(grid.getStore().getAt(rowIndex).data);
            }
            else
            {
                confirmarRegistrarElemento(grid.getStore().getAt(rowIndex).data);
            }
        }
    },
    {
        getClass: function(v, meta, rec)
        {
            if(rec.get('prefijoEmpresa')=='TN'){
                
                var permiso = $("#ROLE_151-831");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

                if (boolPermiso &&
                    (typeof rec.get('boolRequiereRegistro') !== 'undefined' && typeof rec.get('boolTieneFlujo') !== 'undefined'))
                {
                    
                    if (rec.get('boolRequiereRegistro') && !rec.get('boolTieneFlujo') && rec.get('estado') === "Activo")
                    {
                        return 'button-grid-telcos detalle-tecnico-sim';
                    } else
                    {
                        return 'button-grid-invisible';
                    }

                }
            }
        },
        tooltip: 'Ver detalle tecnico',
        handler: function(grid, rowIndex, colIndex)
        {
            verInformacionTecnicaSimultanea(grid.getStore().getAt(rowIndex).data);
        }
    },
    //ACTIVAR PUERTO
    {
        getClass: function(v, meta, rec) {
            if(rec.get('flujo')=='TTCO'){
                if(rec.get('descripcionProducto')=='INTERNET'){
                    var permiso = $("#ROLE_151-846");
                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                    if(!boolPermiso){
                        return 'button-grid-invisible';
                    }
                    else{
                        if ((rec.get('estado') == "Asignada") && rec.get('descripcionProducto') == "INTERNET") {
                            return 'button-grid-informacionTecnica';
                        }
                        else{
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
            if(rec.get('flujo')=='MD'){
                if(rec.get('descripcionProducto') === 'INTERNET'  || rec.get('descripcionProducto') === "INTERNET SMALL BUSINESS"
                    || rec.get('descripcionProducto') === "TELCOHOME"){
                    var permiso = $("#ROLE_151-846");
                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                    if(!boolPermiso){
                        return 'button-grid-invisible';
                    }
                    else
                    {
                        // se aumenta el producto de cableado etehernet
                        if ((rec.get('estado') === "Asignada") &&
                            (rec.get('descripcionProducto') === "INTERNET" || rec.get('descripcionProducto') === "INTERNET SMALL BUSINESS"
                            || rec.get('descripcionProducto') === "TELCOHOME")) {
                            console.log('Solo Activar')
                            return 'button-grid-informacionTecnica';
                        }
                        else{
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
            if(rec.get('flujo')=='TN'){
           
                var permiso = $("#ROLE_151-846");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                if(!boolPermiso){
                    return 'button-grid-invisible';
                }
                else{
                    if (rec.get('estado') == "Asignada"
                        && rec.get('nombreProducto')!== 'CLEAR CHANNEL PUNTO A PUNTO') {
                        if( rec.get('descripcionProducto')=='L3MPLS' ||
                            rec.get('descripcionProducto')=='L3MPLS SDWAN' ||
                            rec.get('descripcionProducto')=='INTERNET' ||
                            rec.get('descripcionProducto')=='INTERNET SDWAN' ||
                            rec.get('descripcionProducto')=='INTERNETDC' ||
                            rec.get('descripcionProducto')=='INTERNET DC SDWAN' ||
                            rec.get('descripcionProducto')=='CONCINTER' ||
                            rec.get('descripcionProducto')=='DATOSDC' ||
                            rec.get('descripcionProducto')=='DATOS DC SDWAN' ||
                            rec.get('descripcionProducto')=='L2MPLS' ||
                            rec.get('descripcionProducto')=='INTMPLS' ||
                            rec.get('descripcionProducto')=='DATOS FWA' ||
                            rec.get('descripcionProducto')=='DATOS SAFECITY' ||
                            rec.get('descripcionProducto')=='SAFECITYDATOS' ||
                            rec.get('descripcionProducto')=='SAFECITYSWPOE' ||
                            rec.get('descripcionProducto')=='SAFECITYWIFI')
                        {
                            return 'button-grid-informacionTecnica';
                        }
                    }
                    else{
                        return 'button-grid-invisible';
                    }
                }
            }
            if(rec.get('flujo')=='TNP' && rec.get('descripcionProducto') === 'INTERNET' )
            {
                var permisoTnp = $("#ROLE_151-846");
                var boolPermisoTnp = (typeof permisoTnp === 'undefined') ? false : (permisoTnp.val() == 1 ? true : false);
                if(!boolPermisoTnp){
                    return 'button-grid-invisible';
                }
                else{
                    if ((rec.get('estado') === "Asignada") &&
                        rec.get('descripcionProducto') === "INTERNET") {
                        return 'button-grid-informacionTecnica';
                    }
                    else{
                        return 'button-grid-invisible';
                    }
                }
            }
        },
        tooltip: 'Activar Puerto',
        handler: function(grid, rowIndex, colIndex) {
            var marcaElemento = grid.getStore().getAt(rowIndex).data.marcaElemento;
            if(grid.getStore().getAt(rowIndex).data.flujo=="TTCO"){
                if((grid.getStore().getAt(rowIndex).data.estado=="Asignada") && 
                    grid.getStore().getAt(rowIndex).data.descripcionProducto=="INTERNET"){
                    activarCliente(grid.getStore().getAt(rowIndex).data, grid);
                }
            }
            // Se aumenta el producto de cableado ethernet
            if(grid.getStore().getAt(rowIndex).data.flujo=="MD"){
                if((grid.getStore().getAt(rowIndex).data.estado==="Asignada") && 
                    (grid.getStore().getAt(rowIndex).data.descripcionProducto==="INTERNET" 
                    || grid.getStore().getAt(rowIndex).data.descripcionProducto==="INTERNET SMALL BUSINESS"
                    || grid.getStore().getAt(rowIndex).data.descripcionProducto==="TELCOHOME")){ 
                    var tieneProgresoRuta       = grid.getStore().getAt(rowIndex).data.tieneProgresoRuta;
                    var tieneProgresoMateriales = grid.getStore().getAt(rowIndex).data.tieneProgresoMateriales;

                    if(tieneProgresoRuta === "NO" || tieneProgresoMateriales === "NO")
                    {
                        registroFibraMaterial(grid.getStore().getAt(rowIndex).data);
                    }
                    else
                    {
                        if(marcaElemento === "HUAWEI")
                        {
                            activarServicioHuaweiMD(grid.getStore().getAt(rowIndex).data, grid);
                        }
                        else if(marcaElemento === "TELLION")
                        {
                            activarServicioMD(grid.getStore().getAt(rowIndex).data, grid);
                        }
                        else if(marcaElemento === "ZTE")
                        {
                            activarServicioZteMD(grid.getStore().getAt(rowIndex).data);
                        }
                    }
                    
                }
            }
            if(grid.getStore().getAt(rowIndex).data.flujo=="TN")
            {
                //Verificar si se trata de activar servicio con factibilidad heredada
                var idServicioHeredado = grid.getStore().getAt(rowIndex).data.servicioHeredadoFact;
                var ultimaMilla        = grid.getStore().getAt(rowIndex).data.ultimaMilla;
                
                //Si trata de un servicio heredado se  muestra informacion de resumen de servicios a cancelar ( heredado ) y activar
                //el nuevo servicio
                if((!Ext.isEmpty(idServicioHeredado) && 
                   idServicioHeredado !==0 && 
                   grid.getStore().getAt(rowIndex).data.estado === "Asignada" &&
                   (grid.getStore().getAt(rowIndex).data.descripcionProducto === "L3MPLS" ||
                    grid.getStore().getAt(rowIndex).data.descripcionProducto === "L3MPLS SDWAN" ||
                    grid.getStore().getAt(rowIndex).data.descripcionProducto === "INTERNET")
                   ) || grid.getStore().getAt(rowIndex).data.descripcionProducto === "CONCINTER")
                {
                    mostrarDetalleActivar(grid.getStore().getAt(rowIndex).data);
                }
                else
                {
                    if((grid.getStore().getAt(rowIndex).data.estado=="Asignada") && 
                       (grid.getStore().getAt(rowIndex).data.descripcionProducto=="L3MPLS" ||
                        grid.getStore().getAt(rowIndex).data.descripcionProducto=="L3MPLS SDWAN")){
                        if(ultimaMilla === "FTTx" && ( grid.getStore().getAt(rowIndex).data.tieneProgresoRuta === "NO"
                           || grid.getStore().getAt(rowIndex).data.tieneProgresoMateriales === "NO" ) )
                        {
                            registroFibraMaterial(grid.getStore().getAt(rowIndex).data);
                        }
                        else
                        {
                            activarServicioL3MPLS(grid.getStore().getAt(rowIndex).data, grid);
                        }
                    }
                    else if((grid.getStore().getAt(rowIndex).data.estado=="Asignada") && 
                        grid.getStore().getAt(rowIndex).data.descripcionProducto=="INTERNET" ||
                        grid.getStore().getAt(rowIndex).data.descripcionProducto=="INTERNETDC" ||
                        grid.getStore().getAt(rowIndex).data.descripcionProducto=="INTERNET DC SDWAN" ||
                        grid.getStore().getAt(rowIndex).data.descripcionProducto=="DATOSDC" ||
                        grid.getStore().getAt(rowIndex).data.descripcionProducto=="DATOS DC SDWAN"){
                        if(ultimaMilla === "FTTx" && ( grid.getStore().getAt(rowIndex).data.tieneProgresoRuta === "NO"
                           || grid.getStore().getAt(rowIndex).data.tieneProgresoMateriales === "NO" ) )
                        {
                            registroFibraMaterial(grid.getStore().getAt(rowIndex).data);
                        }
                        else
                        {
                            activarServicioInternet(grid.getStore().getAt(rowIndex).data, grid);
                        }
                    }
                    else if((grid.getStore().getAt(rowIndex).data.estado=="Asignada") && 
                        (grid.getStore().getAt(rowIndex).data.descripcionProducto=="INTMPLS" ||
                        grid.getStore().getAt(rowIndex).data.descripcionProducto=="INTERNET SDWAN")){                    
                        if(ultimaMilla === "FTTx" && ( grid.getStore().getAt(rowIndex).data.tieneProgresoRuta === "NO"
                           || grid.getStore().getAt(rowIndex).data.tieneProgresoMateriales === "NO" ) )
                        {
                            registroFibraMaterial(grid.getStore().getAt(rowIndex).data);
                        }
                        else
                        {
                            activarServicioINTMPLS(grid.getStore().getAt(rowIndex).data, grid);
                        }
                    }
                    else if((grid.getStore().getAt(rowIndex).data.estado=="Asignada") && 
                        grid.getStore().getAt(rowIndex).data.descripcionProducto=="L2MPLS"){                    
                        mostrarDetalleL2mpls(grid.getStore().getAt(rowIndex).data, grid);
                    }
                    else if((grid.getStore().getAt(rowIndex).data.estado=="Asignada") &&
                        grid.getStore().getAt(rowIndex).data.descripcionProducto=="DATOS FWA"){
                        mostrarDetalleActivarFWA(grid.getStore().getAt(rowIndex).data, grid);
                    }
                    else if((grid.getStore().getAt(rowIndex).data.estado=="Asignada") &&
                        grid.getStore().getAt(rowIndex).data.descripcionProducto=="DATOS SAFECITY"){
                            if( grid.getStore().getAt(rowIndex).data.tieneProgresoRuta === "NO"
                                || grid.getStore().getAt(rowIndex).data.tieneProgresoMateriales === "NO" )
                            {
                                registroFibraMaterial(grid.getStore().getAt(rowIndex).data);
                            }
                            else
                            {
                                activarServicioHuaweiMD(grid.getStore().getAt(rowIndex).data, grid);
                            }
                    }
                    else if((grid.getStore().getAt(rowIndex).data.estado=="Asignada") &&
                        (grid.getStore().getAt(rowIndex).data.descripcionProducto=="SAFECITYDATOS"
                         || grid.getStore().getAt(rowIndex).data.descripcionProducto=="SAFECITYWIFI")){
                        activarServiciosSafeCityGpon(grid.getStore().getAt(rowIndex).data, grid);
                    }
                    else if((grid.getStore().getAt(rowIndex).data.estado=="Asignada") &&
                        grid.getStore().getAt(rowIndex).data.descripcionProducto=="SAFECITYSWPOE"){
                            if( grid.getStore().getAt(rowIndex).data.tieneProgresoMateriales === "NO" )
                            {
                                registroFibraMaterial(grid.getStore().getAt(rowIndex).data);
                            }
                            else
                            {
                                activarServicioSwPoeSafeCity(grid.getStore().getAt(rowIndex).data, grid);
                            }
                    }
                }
            }
            if(grid.getStore().getAt(rowIndex).data.flujo=="TNP" &&
               grid.getStore().getAt(rowIndex).data.estado==="Asignada" && 
               grid.getStore().getAt(rowIndex).data.descripcionProducto==="INTERNET")
            {
                if(marcaElemento === "HUAWEI")
                {
                    activarServicioHuaweiMD(grid.getStore().getAt(rowIndex).data, grid);
                }
                else if(marcaElemento === "TELLION")
                {
                    activarServicioMD(grid.getStore().getAt(rowIndex).data, grid);
                }
                else if(marcaElemento === "ZTE")
                {
                    activarServicioZteMD(grid.getStore().getAt(rowIndex).data);
                }
            }
        }
    },
    {
        getClass: function(v, meta, rec)
        {
            var permiso = $("#ROLE_151-6997");
            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

            if(!boolPermiso)
            {
                return 'button-grid-invisible';
            }
            else
            {
                if( (rec.get('estado') == "Asignada") && (((rec.get('descripcionProducto') == 'L3MPLS' ||
                     rec.get('descripcionProducto') == 'INTMPLS' || rec.get('descripcionProducto') == 'INTERNET'
                     || rec.get('descripcionProducto') == 'INTERNET SDWAN')
                     && (rec.get('ultimaMilla') == "Fibra Optica" || rec.get('ultimaMilla') == "Radio" ||
                     rec.get('ultimaMilla') == "UTP")) || (rec.get('productoPermitidoReversarOT') == "S") )
                     && rec.get('nombreProducto')!== 'CLEAR CHANNEL PUNTO A PUNTO'  )
                {
                    return 'button-grid-verReversoSolicitudMigracion';
                }
                else
                {
                    return 'button-grid-invisible';
                }
            }
        },
        tooltip: 'Reversar estado Orden Trabajo',
        handler: function(grid, rowIndex, colIndex)
        {
            reversarEstadoSolicitud(grid.getStore().getAt(rowIndex).data);
        }
    },
    //FACTIBILIDAD NODO WIFI PARA IPCCL2
    {
        getClass: function(v, meta, rec) {
            if(rec.get('flujo')=='TN'){
                var permiso = $("#ROLE_151-846");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                let arrayConcentradoresWifiActivos;

                /*Valido que la propiedad no sea null*/
                if (rec.get('arrayConcentradoresWifi'))
                {
                    const arrayConcentradoresWifi = rec.get('arrayConcentradoresWifi');
                    /*Valido que exista el elemento y sea igual o mayor a 2*/
                    if (arrayConcentradoresWifi.length >= 2)
                    {
                        /*Filtro el elemento en un nuevo arreglo, que contendra solo los que esten en estado activo*/
                        arrayConcentradoresWifiActivos = rec.get('arrayConcentradoresWifi').filter(function (el) {
                            return el.strEstado === 'Activo';
                        });
                    }
                }

                const intTipoEsquema = rec.get('tipoEsquema');

                if(!boolPermiso){
                    return 'button-grid-invisible';
                } else 
                {
                    if (arrayConcentradoresWifiActivos)
                    { /* Se validan condiciones especificas para que aparezca el botón en el grid técnico */
                        if (rec.get('estado') == "FactibilidadEnProceso" &&
                            rec.get('descripcionProducto') == 'INTERNET WIFI' &&
                            intTipoEsquema == 2
                            && arrayConcentradoresWifiActivos.length == 2
                        )
                        {
                            /* Valido que la solicitud este en estado de Prefactibilidad para poder presentar el botón. */
                            const strSolWifiEstado = rec.get('arraySolicitudWifi') ? rec.get('arraySolicitudWifi').strEstado : null;
                            if (strSolWifiEstado == 'PreFactibilidad') return 'button-grid-Check';
                        } else
                        {
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
        },
        tooltip: 'Asignar Factibilidad Nodo Wifi',
        handler: function(grid, rowIndex, colIndex) {
            var rec                 = store.getAt(rowIndex);
            showIngresoFactibilidadWifiL2(rec);
        }
    },
    //PROGRAMAR RADIO NODO WIFI Esquema 1
    {
        getClass: function(v, meta, rec) {
            if(rec.get('flujo')=='TN'){
                var permiso = $("#ROLE_151-6577");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

                if (!boolPermiso)
                {
                    return 'button-grid-invisible';
                } else
                {
                    if (rec.get('estado') == "PrePlanificada" && rec.get('arrayDatosNodoWifi'))
                    {
                        return 'button-grid-Time2';
                    } else
                    {
                        return 'button-grid-invisible';
                    }
                }
            }
        },
        tooltip: 'Programar Nodo Wifi - RADIO',
        handler: function(grid, rowIndex, colIndex) {
            var rec                 = store.getAt(rowIndex);
            showProgramarRadio(rec);
        }
    },
    //AGREGAR ARCHIVO DE INSPECCION RADIO
    {
        getClass: function(v, meta, rec) {
            if(rec.get('flujo')=='TN'){
                var permiso = $("#ROLE_151-6639");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                if (!boolPermiso)
                {
                    return 'button-grid-invisible';
                } else
                {
                    if (rec.get('nombreProducto') == 'WIFI Alquiler Equipos' &&
                    rec.get('estado') == 'FactibilidadEnProceso')
                    {
                        return 'button-grid-telcos inspection-upload';
                    } else
                    {
                        return 'button-grid-invisible';
                    }
                }
            }
        },
        tooltip: 'Subir Informe de Inspección',
        handler: function(grid, rowIndex, colIndex) {
            var rec                 = store.getAt(rowIndex);
            cargarArchivoInspeccion(rec.get('idServicio'), rec.get('login'));
        }
    },
    //VER ARCHIVO DE INSPECCION RADIO
    {
        getClass: function(v, meta, rec) {
            if(rec.get('flujo')=='TN'){
                var permiso = $("#ROLE_151-6640");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                if (!boolPermiso)
                {
                    return 'button-grid-invisible';
                } else
                {
                    if (rec.get('nombreProducto') == 'WIFI Alquiler Equipos')
                    {
                        return 'button-grid-telcos inspection-file';
                    } else
                    {
                        return 'button-grid-invisible';
                    }
                }
            }
        },
        tooltip: 'Ver Informe de Inspección',
        handler: function(grid, rowIndex, colIndex) {
            var rec                 = store.getAt(rowIndex);
            verArchivoInspeccion(rec.get('idServicio'));
        }
    },
    //CONFIRMAR SERVICIO
    {
        getClass: function(v, meta, rec) {
            if( (rec.get('flujo')=='TN') )
            {
                var strProductoPaqHoras       =   rec.get('strValorProductoPaqHoras');
                var strProductoPaqHorasRec    =   rec.get('strValorProductoPaqHorasRec');
                if ((rec.get('nombreProducto') == strProductoPaqHoras )|| (rec.get('nombreProducto') == strProductoPaqHorasRec ) )
                {
                    return 'button-grid-invisible';    
                }
            }
            if(rec.get('flujo')=='TTCO'){
                var permiso = $("#ROLE_151-847");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                
                if(!boolPermiso){ 
                    return 'button-grid-invisible';
                }
                else{
                    if(rec.get('botones')=="SI"){
                        if(rec.get('descripcionProducto')=='INTERNET'){
                            if(rec.get('estadoSolicitud')=='Finalizada'){
                                if (rec.get('estado') == "EnPruebas") {
                                    return 'button-grid-confirmarActivacion';
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                            }
                        }
                        if(rec.get('descripcionProducto')=='CORREO' || rec.get('descripcionProducto')=='SMTP AUTENTICADO'){
                            if (rec.get('estado') == "Pendiente") {
                                return 'button-grid-confirmarActivacion';
                            }
                            else{
                                return 'button-grid-invisible';
                            }
                        }
                        if(rec.get('descripcionProducto')=='DOMINIO' || rec.get('descripcionProducto')=='HOSTING' || 
                           rec.get('descripcionProducto')=='SITIO WEB'){
                            if (rec.get('estado') == "Pendiente") {
                                return 'button-grid-confirmarActivacion';
                            }
                            else{
                                return 'button-grid-invisible';
                            }
                        }
                        if(rec.get('descripcionProducto')=='IP'){
                            if (rec.get('estado') == "EnPruebas") {
                                return 'button-grid-confirmarActivacion';
                            }
                            else{
                                return 'button-grid-invisible';
                            }
                        }
                        if(rec.get('descripcionProducto')=='OTROS' || rec.get('descripcionProducto')=='ANTIVIRUS' ){
                            if (rec.get('estado') == "Pendiente") {
                                return 'button-grid-confirmarActivacion';
                            }
                            else{
                                return 'button-grid-invisible';
                            }
                        }
                        if(rec.get('descripcionProducto')=='EQUIPO PROTEGIDO'){
                            if (rec.get('estado') == "Pendiente" || rec.get('estado') == "AsignadoTarea") {
                                return 'button-grid-confirmarActivacion';
                            }
                            else{
                                return 'button-grid-invisible';
                            }
                        }
                        if(rec.get('descripcionProducto')=='NETLIFECAM'|| rec.get('descripcionProducto')=='NETLIFECAM OUTDOOR'){
                            if (rec.get('estado') == "Asignada") {
                                return 'button-grid-confirmarActivacion';
                            }
                            else{
                                return 'button-grid-invisible';
                            }
                        }
                    }

                }
            }
            if(rec.get('flujo')=='MD'){
                var permiso = $("#ROLE_151-847");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                //alert(typeof permiso);
                if(!boolPermiso){ 
                    return 'button-grid-invisible';
                }
                else{
                    var botonesPermitidos = rec.get('botones');
                    if (botonesPermitidos === "SI" 
                        && typeof rec.get('arrayPersonalizacionOpcionesGridTecnico') !== 'undefined'
                        && rec.get('arrayPersonalizacionOpcionesGridTecnico').hasOwnProperty('ROLE_151-847'))
                    {
                        var arrayPersonalizacionConfirmarServicio = rec.get('arrayPersonalizacionOpcionesGridTecnico')['ROLE_151-847'];
                        if (arrayPersonalizacionConfirmarServicio.includes(rec.get('descripcionProducto')))
                        {
                            botonesPermitidos = "SI";
                        }
                        else
                        {
                            botonesPermitidos = "NO";
                        }
                    }
                    if(botonesPermitidos=="SI"){
                        if(rec.get('descripcionProducto')=='INTERNET'){
                                if (rec.get('estado') == "EnPruebas" && rec.get('tieneEncuesta') == "TRUE" && rec.get('tieneActa') == "TRUE") {
                                    return 'button-grid-confirmarActivacion';
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                        }
                        if(rec.get('descripcionProducto')=='CORREO'){
                            if (rec.get('estado') == "Pendiente") {
                                return 'button-grid-confirmarActivacion';
                            }
                            else{
                                return 'button-grid-invisible';
                            }
                        }
                        if(rec.get('descripcionProducto')=='DOMINIO' || rec.get('descripcionProducto')=='HOSTING'){
                            if (rec.get('estado') == "Pendiente") {
                                return 'button-grid-confirmarActivacion';
                            }
                            else{
                                return 'button-grid-invisible';
                            }
                        }
                        if(rec.get('descripcionProducto')=='IP'){
                            if (rec.get('estado') == "EnPruebas") {
                                return 'button-grid-confirmarActivacion';
                            }
                            else{
                                return 'button-grid-invisible';
                            }
                        }
                        if(rec.get('descripcionProducto')=='ANTIVIRUS'){
                            if (rec.get('estado') == "Pendiente") {
                                return 'button-grid-confirmarActivacion';
                            }
                            else{
                                return 'button-grid-invisible';
                            }
                        }
                        if(rec.get('nombreProducto')==='CABLEADO ETHERNET')
                        {
                            if (rec.get('estado') == "Asignada") 
                            {
                                return 'button-grid-confirmarActivacion';
                            }
                            else
                            {
                                return 'button-grid-invisible';
                            }
                        }
                        if(rec.get('descripcionProducto')=='OTROS'){
                            if (rec.get('estado') == "Pendiente")
                            {   
                                if(rec.get('strNuevoAntivirus') === 'SI' || rec.get('strReintentoNuevoAntivirus') === 'SI')
                                {
                                    return 'button-grid-invisible';
                                }
                                else
                                {
                                    return 'button-grid-confirmarActivacion';
                                }
                            }
                            else
                            {
                                return 'button-grid-invisible';
                            }
                        }
                        if(rec.get('descripcionProducto')=='EQUIPO PROTEGIDO'){
                            if (rec.get('estado') == "Pendiente" || rec.get('estado') == "AsignadoTarea") {
                                return 'button-grid-confirmarActivacion';
                            }
                            else{
                                return 'button-grid-invisible';
                            }
                        }
                        if(rec.get('descripcionProducto')=='NETLIFECAM' || rec.get('descripcionProducto')=='NETLIFECAM OUTDOOR'){
                            if (rec.get('estado') == "Asignada" && rec.get('productoPermitidoRegistroEle') == "N") {
                                return 'button-grid-confirmarActivacion';
                            }
                            else{
                                return 'button-grid-invisible';
                            }
                        }
                        if(rec.get('descripcionProducto')==='SMARTWIFI' || 
                           rec.get('descripcionProducto')==='APWIFI'    ||
                           rec.get('descripcionProducto')==='NETHOME'   ||
                           rec.get('descripcionProducto')==='NETFIBER'  ||
                           rec.get('descripcionProducto') === 'EXTENDER_DUAL_BAND' ||
                           rec.get('descripcionProducto') === 'WIFI_DUAL_BAND')
                        {
                            if (rec.get('estado') == "Asignada" && rec.get('strTrasladarExtenderDB') === "NO") 
                            {
                                return 'button-grid-confirmarActivacion';
                            }
                            else
                            {
                                return 'button-grid-invisible';
                            }
                        }
                        if(rec.get('descripcionProducto')=='WDB_Y_EDB'){
                            if (rec.get('tieneSolicitudAgregarEquipo') !=null
                                && (rec.get('estado') == "PendienteAp" || rec.get('estado') == "Asignada")) {
                                return 'button-grid-confirmarActivacion';
                            }
                            else{
                                return 'button-grid-invisible';
                            }
                        }
                        // BOTONES DE ACTIVACION PARA SERVICIOS ADICIONALES CON LOGICA FOXPREMIUM
                        if('FOXPREMIUM' === rec.get('descripcionProducto') ||
                           'PARAMOUNT' === rec.get('descripcionProducto') ||
                           'NOGGIN' === rec.get('descripcionProducto') ||
                           'ECDF' === rec.get('descripcionProducto') ||
                           'GTVPREMIUM' === rec.get('descripcionProducto'))
                        {
                            if (rec.get('estado') == "Pendiente" && ('ECDF' != rec.get('descripcionProducto') || rec.get('strCorreoECDF')!== null)) 
                            {
                                return 'button-grid-confirmarActivacion';
                            }
                            else
                            {
                                return 'button-grid-invisible';
                            }
                        }
                        if(rec.get('descripcionProducto')==='CAMARA IP')
                        {
                            if (rec.get('estado') == "Asignada") 
                            {
                                return 'button-grid-camara';
                            }
                            else
                            {
                                return 'button-grid-invisible';
                            }
                        }
                        if(rec.get('descripcionProducto')==='50 SMS' )
                        {
                            if (rec.get('estado') == "Pendiente") 
                            {
                                return 'button-grid-sms';
                            }
                            else
                            {
                                return 'button-grid-invisible';
                            }
                        }
                        if(rec.get('descripcionProducto')==='24 HRS GR EXTRA')
                        {
                            if (rec.get('estado') == "Pendiente") 
                            {
                                return 'button-grid-grabacion';
                            }
                            else
                            {
                                return 'button-grid-invisible';
                            }
                        }
                        if(rec.get('descripcionProducto') === 'INTERNET SMALL BUSINESS' || rec.get('descripcionProducto') === 'TELCOHOME')
                        {
                            if (rec.get('estado') === "EnPruebas") 
                            {
                                return 'button-grid-confirmarActivacion';
                            }
                            else
                            {
                                return 'button-grid-invisible';
                            }
                            
                        }
                        if(rec.get('descripcionProducto')==='HBO-MAX')
                        {
                            if (rec.get('estado') == "Pendiente" && rec.get('boolServicioInternetActivo') === true) 
                            {
                                return 'button-grid-confirmarActivacion';
                            }
                            else
                            {
                                return 'button-grid-invisible';
                            }
                        }
                    }
                }
            }
            if(rec.get('flujo')=='TN')
            {
                var permiso = $("#ROLE_151-847");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                var boolClearChannel = (rec.data.boolIsClearChannel !== null) ? rec.data.boolIsClearChannel : false;
                var requiereTransporte = (rec.data.strClearChannelPuntoAPuntoTransporte!== null) ? rec.data.strClearChannelPuntoAPuntoTransporte:"";
                var validaEnlace = (rec.data.tipoEnlace !== null) ? rec.data.tipoEnlace.substring(0, 9):rec.data.tipoEnlace;
                
                if(!boolPermiso)
                { 
                    return 'button-grid-invisible';
                }
                else
                {
                    if(rec.get('botones')=="SI")
                    {
                        if (rec.get('boolValidaDptoActivar') === 'N')
                        {
                            return 'button-grid-invisible';
                        }
                        if (rec.get('boolPermiteVisualizarBoton') === 'N')
                        {
                            return 'button-grid-invisible';
                        }
                        if (rec.get('estado') === "EnPruebas") 
                        {
                            if( rec.get('descripcionProducto') === 'INTERNET' || 
                                rec.get('descripcionProducto') === 'L3MPLS' ||
                                rec.get('descripcionProducto') === 'L3MPLS SDWAN' ||
                                rec.get('descripcionProducto') === "INTMPLS" || 
                                rec.get('descripcionProducto') === "INTERNET SDWAN" || 
                                rec.get('descripcionProducto') === "INTERNETDC" ||
                                rec.get('descripcionProducto') === "INTERNET DC SDWAN" ||
                                rec.get('descripcionProducto') === "DATOSDC" ||
                                rec.get('descripcionProducto') === "DATOS DC SDWAN" ||
                                rec.get('descripcionProducto') === "L2MPLS" ||
                                rec.get('descripcionProducto') === "CONCINTER" ||
                                rec.get('descripcionProducto') === 'INTERNET WIFI' ||
                                rec.get('descripcionProducto') === 'SERVICIOS-CAMARA-SAFECITY')
                            {
                                return 'button-grid-confirmarActivacion';
                            }
                        }
                        
                        if (rec.get('estado') === "Pendiente" || rec.get('estado') === 'AsignadoTarea' ||
                            (rec.get('estado') === 'Asignada' && rec.get('esPreferenteSolucion') === 'N')  ||
                            rec.get('estado') === 'PreAsignacionInfoTecnica') 
                        {
                            if(rec.get('perteneceSolucion') === 'S')
                            {
                                if(
                                    (rec.get('esPreferenteSolucion') === 'S' && rec.get('estado') !== 'AsignadoTarea')
                                    || rec.get('seActivaServicioSolucion') === 'S')
                                {
                                    return 'button-grid-confirmarActivacion';
                                }
                                else
                                {
                                    return 'button-grid-invisible';
                                }
                            }
                            else
                            {                   
                                if( (rec.get('descripcionProducto') === 'OTROS' ||
                                    rec.get('descripcionProducto') === 'SMARTSPACE') &&
                                    rec.get('nombreProducto') !== 'WIFI Alquiler Equipos' &&
                                    !rec.get('boolRequiereRegistro') )
                                {
                                    //Consultamos si el producto es de instalación simultánea y si lo es el producto padre debe estar en Activo
                                    //para visualizar el botón de confirmación de servicio.
                                    let servicioPadreSimultaneo = rec.get('servicioPadreSimultaneo');

                                    if(rec.get('nombreProducto') === 'SECURITY NG FIREWALL')
                                    {

                                        if(permisoActivarServiciotNGF)
                                        {
                                            return 'button-grid-confirmarActivacion';
                                        }
                                        else
                                        {
                                            return 'button-grid-invisible';
                                        }
                                    }else if(rec.get('esServicioCamaraVpnSafeCity') === 'S')
                                    {
                                        var estadoServicio = rec.get('estado');
                                        if(estadoServicio === 'Asignada')
                                        {
                                            return 'button-grid-confirmarActivacion';
                                        }
                                        else
                                        {
                                            return 'button-grid-invisible';
                                        }
                                    }
                                    else if (typeof rec.get('boolRequiereRegistro') !== 'undefined' && typeof rec.get('boolTieneFlujo') !== 'undefined')
                                    {
                                        if (!rec.get('boolRequiereRegistro') && !rec.get('boolTieneFlujo') && (rec.get('estado') === 'AsignadoTarea'
                                            || rec.get('estado') === 'Asignada') && (servicioPadreSimultaneo == null 
                                            || servicioPadreSimultaneo.estadoServicio === 'Activo'))
                                        {
                                            return 'button-grid-confirmarActivacion';
                                        } 
                                        else
                                        {
                                            if (rec.get('estado') === 'Pendiente')
                                            {
                                                return 'button-grid-confirmarActivacion';
                                            }
                                            else
                                            {
                                                return 'button-grid-invisible';
                                            }
                                        }
                                    }
                                    else
                                    {
                                        if(rec.get('seActivaProducto') === 'S')
                                        {
                                            return 'button-grid-confirmarActivacion';
                                        }
                                        else
                                        {
                                            return 'button-grid-invisible';
                                        }
                                    }
                                }
                                else if(rec.get('descripcionProducto') === 'SEG_VEHICULO')
                                {
                                    if(rec.get('estado') === 'AsignadoTarea' || rec.get('estado') === 'Asignada')
                                    {
                                        return 'button-grid-confirmarActivacion';
                                    }
                                    else
                                    {
                                        return 'button-grid-invisible';
                                    }
                                }
                                
                                else if(rec.get('descripcionProducto') === 'SAFE ENTRY')
                                {
                                    if(rec.get('estado') === 'Pendiente')
                                    {
                                        return 'button-grid-confirmarActivacion';
                                    }
                                    else{
                                        return 'button-grid-invisible';
                                    }
                                }
                            }
                            if(boolClearChannel && rec.get('estado') === 'Asignada'
                            ||  validaEnlace === 'PRINCIPAL' && boolClearChannel 
                            && rec.get('estado') === 'Pendiente' && requiereTransporte === 'SI'){
                                return 'button-grid-confirmarActivacion';
                            }else{
                                return 'button-grid-invisible';
                            }
                        }
                        else
                        {
                            return 'button-grid-invisible';
                        }                        
                    }//if(rec.get('botones')=="SI")
                }//if(!boolPermiso)
            }//if(rec.get('flujo')=='TN')
            if(rec.get('flujo')=='TNP')
            {
                var permiso = $("#ROLE_151-847");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                //alert(typeof permiso);
                if(!boolPermiso)
                { 
                    return 'button-grid-invisible';
                }
                else
                {
                    if(rec.get('botones')=="SI")
                    {
                        if (rec.get('estado') === "EnPruebas") 
                        {
                            if( rec.get('descripcionProducto') === 'OTROS')
                            {
                                return 'button-grid-confirmarActivacion';
                            }
                        }
                        else
                        {
                            return 'button-grid-invisible';
                        }
                    }//if(rec.get('botones')=="SI")
                }
            }//if(rec.get('flujo')=='TNP')
            if(rec.get('flujo')=='TNG')
            {
               var permisoTng = $("#ROLE_151-847");
                var boolPermisoTng = (typeof permisoTng === 'undefined') ? false : (permisoTng.val() == 1 ? true : false);          
                if(!boolPermisoTng)
                { 
                    return 'button-grid-invisible';
                }
                else
                { 
                    if(rec.get('botones')=="SI")
                    {   
                        if (rec.get('estado') === "Pendiente" || rec.get('estado') === "Backlog") 
                        {
                            if( rec.get('descripcionProducto') === 'OTROS' || rec.get('descripcionProducto') === '')
                            {
                                return 'button-grid-confirmarActivacion';
                            }
                        }
                        else
                        {
                            return 'button-grid-invisible';
                        }
                    }//if(rec.get('botones')=="SI")
                }
             }//if(rec.get('flujo')=='TNG')
        },
        tooltip: 'Confirmar Servicio',
        handler: function(grid, rowIndex, colIndex) {
            var tieneProgresoMateriales = grid.getStore().getAt(rowIndex).data.tieneProgresoMateriales; 
            if(grid.getStore().getAt(rowIndex).data.flujo=="TTCO"){
                if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="INTERNET"){
                    if(grid.getStore().getAt(rowIndex).data.estado=="EnPruebas"){
                        confirmarActivacion(grid.getStore().getAt(rowIndex).data,'847');
                    }
                }
                else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="CORREO" || 
                       grid.getStore().getAt(rowIndex).data.descripcionProducto=="SMTP AUTENTICADO"){
                    if(grid.getStore().getAt(rowIndex).data.estado=="Pendiente"){
                        confirmarServicioCorreo(grid.getStore().getAt(rowIndex).data,'847');
                    }
                }
                else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="DOMINIO" || 
                        grid.getStore().getAt(rowIndex).data.descripcionProducto=="HOSTING" || 
                        grid.getStore().getAt(rowIndex).data.descripcionProducto=="SITIO WEB"){
                    if(grid.getStore().getAt(rowIndex).data.estado=="Pendiente"){
                        confirmarServicioDominio(grid.getStore().getAt(rowIndex).data,'847');
                    }
                }
                else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="IP"){
                    if(grid.getStore().getAt(rowIndex).data.estado=="EnPruebas"){
                        confirmarServicioIp(grid.getStore().getAt(rowIndex).data,'847');
                    }
                }
                else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="OTROS"){
                    if(grid.getStore().getAt(rowIndex).data.estado=="Pendiente"){
                        confirmarServicioOtros(grid.getStore().getAt(rowIndex).data,'847');
                    }
                }
            }
            if(grid.getStore().getAt(rowIndex).data.flujo=="MD"){
                if(grid.getStore().getAt(rowIndex).data.descripcionProducto === "INTERNET"
                    || grid.getStore().getAt(rowIndex).data.descripcionProducto === "INTERNET SMALL BUSINESS"
                    || grid.getStore().getAt(rowIndex).data.descripcionProducto === "TELCOHOME"){
                    if(grid.getStore().getAt(rowIndex).data.estado=="EnPruebas" ){
                        confirmarActivacionMD(grid.getStore().getAt(rowIndex).data,'847');
                    }
                }
                else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="CORREO" || 
                        grid.getStore().getAt(rowIndex).data.descripcionProducto=="SMTP AUTENTICADO"){
                    if(grid.getStore().getAt(rowIndex).data.estado=="Pendiente"){
                        confirmarServicioCorreo(grid.getStore().getAt(rowIndex).data,'847');
                    }
                }
                else if(grid.getStore().getAt(rowIndex).data.nombreProducto=="CABLEADO ETHERNET"){
                    if(grid.getStore().getAt(rowIndex).data.estado=="Asignada"){
                        confirmarServicioPlan(grid.getStore().getAt(rowIndex).data,'847');
                    }
                }
                else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="OTROS"){
                    if(grid.getStore().getAt(rowIndex).data.estado=="Pendiente"
                        && (grid.getStore().getAt(rowIndex).data.strNuevoAntivirus === "NO" 
                            && grid.getStore().getAt(rowIndex).data.strReintentoNuevoAntivirus === "NO")){
                        confirmarServicioOtros(grid.getStore().getAt(rowIndex).data,'847');
                    }
                }
                else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="ANTIVIRUS"){
                    if(grid.getStore().getAt(rowIndex).data.estado=="Pendiente"){
                        confirmarServicioAntivirus(grid.getStore().getAt(rowIndex).data,'847');
                    }
                }
                else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="EQUIPO PROTEGIDO"){
                    if(grid.getStore().getAt(rowIndex).data.estado=="Pendiente"){
                        confirmarServicioEquipoProtegido(grid.getStore().getAt(rowIndex).data,'847');
                    }
                }
                else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="NETLIFECAM"
                        || grid.getStore().getAt(rowIndex).data.descripcionProducto=="NETLIFECAM OUTDOOR"){
                    if(grid.getStore().getAt(rowIndex).data.estado=="Asignada")
                    {
                        confirmarServicioNetlifeCam(grid.getStore().getAt(rowIndex).data,'847'); 
                    }
                }
                else if("FOXPREMIUM" == grid.getStore().getAt(rowIndex).data.descripcionProducto ||
                        "PARAMOUNT" == grid.getStore().getAt(rowIndex).data.descripcionProducto  ||
                        "NOGGIN" == grid.getStore().getAt(rowIndex).data.descripcionProducto ||
                        "ECDF" == grid.getStore().getAt(rowIndex).data.descripcionProducto ||
                        "GTVPREMIUM" == grid.getStore().getAt(rowIndex).data.descripcionProducto)
                {
                    if("Pendiente" == grid.getStore().getAt(rowIndex).data.estado)
                    {
                        confirmarServicioOtros(grid.getStore().getAt(rowIndex).data,'847');
                    }
                }
                else if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="SMARTWIFI" ||
                        grid.getStore().getAt(rowIndex).data.descripcionProducto=="APWIFI"    ||
                        grid.getStore().getAt(rowIndex).data.descripcionProducto=="NETHOME"   ||
                        grid.getStore().getAt(rowIndex).data.descripcionProducto=="NETFIBER"  ||
                        grid.getStore().getAt(rowIndex).data.descripcionProducto === "EXTENDER_DUAL_BAND" ||
                        grid.getStore().getAt(rowIndex).data.descripcionProducto === "WIFI_DUAL_BAND")
                {
                    if(grid.getStore().getAt(rowIndex).data.estado=="Asignada")
                    {
                        if (!Ext.isEmpty(grid.getStore().getAt(rowIndex).data.idServicioRefIpFija) &&
                            grid.getStore().getAt(rowIndex).data.idServicioRefIpFija != 0)
                        {
                            if (grid.getStore().getAt(rowIndex).data.descripcionProducto=="SMARTWIFI")
                            {
                                confirmarServicioSmartWifi(grid.getStore().getAt(rowIndex).data,'847'); 
                            }
                            else if (grid.getStore().getAt(rowIndex).data.descripcionProducto=="APWIFI")
                            {
                                confirmarServicioApWifi(grid.getStore().getAt(rowIndex).data,'847');
                            }
                            else if (grid.getStore().getAt(rowIndex).data.descripcionProducto=="NETHOME")
                            {
                            confirmarServicioNetHome(grid.getStore().getAt(rowIndex).data,'847'); 
                            }
                            else if (grid.getStore().getAt(rowIndex).data.descripcionProducto=="NETFIBER")
                            {
                            confirmarServicioNetfiber(grid.getStore().getAt(rowIndex).data,'847'); 
                            }
                            else if(grid.getStore().getAt(rowIndex).data.descripcionProducto === "EXTENDER_DUAL_BAND")
                            {
                                confirmarServicioExtenderDualBand(grid.getStore().getAt(rowIndex).data,'847',"PRODUCTO");
                            }
                            else if(grid.getStore().getAt(rowIndex).data.descripcionProducto === "WIFI_DUAL_BAND")
                            {
                                confirmarServicioWifiDualBand(grid.getStore().getAt(rowIndex).data,'847',"PRODUCTO");
                            }
                        }
                        else
                        {
                            Ext.Msg.alert('Mensaje ', 'No se puede realizar la confirmación del servicio,'+
                                                      ' es obligatorio tener un servicio de internet activo!');
                        }
                    }
                }
                else if(grid.getStore().getAt(rowIndex).data.descripcionProducto==="WDB_Y_EDB")
                {
                    if(grid.getStore().getAt(rowIndex).data.tieneSolicitudAgregarEquipo != null
                       && (grid.getStore().getAt(rowIndex).data.estado=="PendienteAp" 
                            || grid.getStore().getAt(rowIndex).data.estado=="Asignada"))
                    {
                        gestionarServicioWyAp(grid.getStore().getAt(rowIndex).data,'847',"PRODUCTO");
                    }
                }
                else if(grid.getStore().getAt(rowIndex).data.descripcionProducto==="CAMARA IP")
                {
                    if(grid.getStore().getAt(rowIndex).data.estado=="Asignada")
                    {
                        confirmarServicioNetlifeCamStoragePortal(grid.getStore().getAt(rowIndex).data,'847'); 
                    }
                }
                else if(grid.getStore().getAt(rowIndex).data.descripcionProducto==="50 SMS")
                {
                    if(grid.getStore().getAt(rowIndex).data.estado=="Pendiente")
                    {
                        confirmarProdAdicionalNetlifeCamStoragePortal(grid.getStore().getAt(rowIndex).data,'847','sms'); 
                    }
                }
                else if (grid.getStore().getAt(rowIndex).data.descripcionProducto === "24 HRS GR EXTRA" 
                    && grid.getStore().getAt(rowIndex).data.estado == "Pendiente")
                {
                    confirmarProdAdicionalNetlifeCamStoragePortal(grid.getStore().getAt(rowIndex).data, '847', 'storage');
                }
                else if(grid.getStore().getAt(rowIndex).data.descripcionProducto==="HBO-MAX")
                {
                    if(grid.getStore().getAt(rowIndex).data.estado=="Pendiente")
                    {
                        activarSerivicioSinCredenciales(grid.getStore().getAt(rowIndex).data); 
                    }
                }
            }
            if(grid.getStore().getAt(rowIndex).data.flujo=="TN")
            {
                
                if (grid.getStore().getAt(rowIndex).data.boolValidaDptoActivar === 'S' && 
                    grid.getStore().getAt(rowIndex).data.boolPermiteVisualizarBoton === 'S')
                {  
                    if((grid.getStore().getAt(rowIndex).data.estado=="EnPruebas"))
                    {
                        if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="INTERNET" || 
                        grid.getStore().getAt(rowIndex).data.descripcionProducto=="L3MPLS" ||
                        grid.getStore().getAt(rowIndex).data.descripcionProducto=="L3MPLS SDWAN" ||
                        grid.getStore().getAt(rowIndex).data.descripcionProducto=="INTMPLS"|| 
                        grid.getStore().getAt(rowIndex).data.descripcionProducto=="INTERNET SDWAN"|| 
                        grid.getStore().getAt(rowIndex).data.descripcionProducto==="CONCINTER"|| 
                        grid.getStore().getAt(rowIndex).data.descripcionProducto== 'INTERNET WIFI'||
                        grid.getStore().getAt(rowIndex).data.descripcionProducto== 'SERVICIOS-CAMARA-SAFECITY'||
                        grid.getStore().getAt(rowIndex).data.descripcionProducto=== 'INTERNETDC' ||
                        grid.getStore().getAt(rowIndex).data.descripcionProducto=== 'INTERNET DC SDWAN' ||
                        grid.getStore().getAt(rowIndex).data.descripcionProducto=== 'DATOSDC'||
                        grid.getStore().getAt(rowIndex).data.descripcionProducto=== 'DATOS DC SDWAN' ||
                        grid.getStore().getAt(rowIndex).data.descripcionProducto=== 'L2MPLS')
                        {
                            confirmarServicioTn(grid.getStore().getAt(rowIndex).data,'847');
                        }
                    }
                    else if(grid.getStore().getAt(rowIndex).data.estado=="Pendiente" ||
                        grid.getStore().getAt(rowIndex).data.estado=="Asignada"  ||
                        grid.getStore().getAt(rowIndex).data.estado=="AsignadoTarea"  ||
                        grid.getStore().getAt(rowIndex).data.estado=="PreAsignacionInfoTecnica")
                    {   
                        if ( grid.getStore().getAt(rowIndex).data.nombreProducto ==='CLEAR CHANNEL PUNTO A PUNTO' && 
                            grid.getStore().getAt(rowIndex).data.descripcionProducto  === "INTERNET"  && 
                           (grid.getStore().getAt(rowIndex).data.estado=="Asignada" || 
                           (grid.getStore().getAt(rowIndex).data.estado=="Pendiente" ))
                        ){
                            confirmarServicioClearChannelPaP(grid.getStore().getAt(rowIndex).data,'847');
                        }
                        else if (grid.getStore().getAt(rowIndex).data.descripcionProducto=="SMARTSPACE")
                        {
                            confirmarServicioSmartSpaceTn(grid.getStore().getAt(rowIndex).data,'847');
                        }
                        else if(grid.getStore().getAt(rowIndex).data.esServicioCamaraVpnSafeCity === 'S')
                        {
                            activarServiciosSafeCityGpon(grid.getStore().getAt(rowIndex).data, grid);
                        }
                        else if(grid.getStore().getAt(rowIndex).data.descripcionProducto === "SEG_VEHICULO")
                        {
                            if (tieneProgresoMateriales === "NO")
                            {
                                registroFibraMaterial(grid.getStore().getAt(rowIndex).data);
                            }
                            else
                            {
                                confirmarServicioSegVehiculo(grid.getStore().getAt(rowIndex).data, grid);
                            }
                        }
                        else if(grid.getStore().getAt(rowIndex).data.descripcionProducto === "SAFE ENTRY")
                        {
                            if (tieneProgresoMateriales === "NO") 
                            {
                                registroFibraMaterial(grid.getStore().getAt(rowIndex).data);
                            }
                            else
                            {
                                confirmarServicioSafeEntry(grid.getStore().getAt(rowIndex).data, grid);
                            }
                        }
                        else if(grid.getStore().getAt(rowIndex).data.registroEquipo === "S")
                        {
                            if(grid.getStore().getAt(rowIndex).data.nombreProducto === "SECURITY NG FIREWALL"
                               && grid.getStore().getAt(rowIndex).data.strEsVerificarSerRequerido === "S")
                            {
                                Ext.Msg.alert('Validacion ',grid.getStore().getAt(rowIndex).data.strMensajeServicioRequerido);
                            }
                            else
                            {
                                confirmarServicioSeguridadLogica(grid.getStore().getAt(rowIndex).data,'847');
                            }
                        }
                        else if(grid.getStore().getAt(rowIndex).data.descripcionProducto      === "OTROS" ||
                                grid.getStore().getAt(rowIndex).data.seActivaServicioSolucion === 'S'
                            )
                        {
                            if (tieneProgresoMateriales === "NO")
                            {
                                registroFibraMaterial(grid.getStore().getAt(rowIndex).data);
                            }
                            else
                            {
                                confirmarServicioTn(grid.getStore().getAt(rowIndex).data,'847');
                            }
                        }
                    }
                }
            }//if(grid.getStore().getAt(rowIndex).data.flujo=="TN")
            if(grid.getStore().getAt(rowIndex).data.flujo=="TNP")
            {
                if((grid.getStore().getAt(rowIndex).data.estado=="EnPruebas"))
                {
                    if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="OTROS")
                    {
                        confirmarServicioTn(grid.getStore().getAt(rowIndex).data,'847');
                    }
                }
            }//if(grid.getStore().getAt(rowIndex).data.flujo=="TNP")
            if(grid.getStore().getAt(rowIndex).data.flujo === "TNG" && 
               (grid.getStore().getAt(rowIndex).data.estado === "Pendiente" ||
                grid.getStore().getAt(rowIndex).data.estado === "Backlog" )&&
               (grid.getStore().getAt(rowIndex).data.descripcionProducto=="OTROS" || 
               grid.getStore().getAt(rowIndex).data.descripcionProducto=="" ))
            { 
                     confirmarServicioTng(grid.getStore().getAt(rowIndex).data,'847');
                
            }//if(grid.getStore().getAt(rowIndex).data.flujo=="TNP")
        }
    },
    // Llamar al metodo de activacion en konibit
    
    {
        getClass: function(v, meta, rec) {
            
            if(rec.get('flujo')=='MD') {
                if(rec.get('descripcionProducto')=='OTROS' && 
                   (rec.get('nombreProducto')=='ECOMMERCE BASIC' ||
                    rec.get('nombreProducto')=='Netlife Assistance Pro') &&
                   rec.get('activoKonibit')=='NO')
                {
                    return 'button-grid-confirmarActivacion';
                    
                }
                else
                {
                    return 'button-grid-invisible';
                }
            }
        },
        tooltip: 'Activar producto en Konibit',
        handler: function(grid, rowIndex, colIndex) {
            
            if(grid.getStore().getAt(rowIndex).data.flujo=="MD" &&
               (grid.getStore().getAt(rowIndex).data.nombreProducto=="ECOMMERCE BASIC" ||
                grid.getStore().getAt(rowIndex).data.nombreProducto=="Netlife Assistance Pro") &&
                grid.getStore().getAt(rowIndex).data.activoKonibit=="NO"){
                confirmarServicioKonibit(grid.getStore().getAt(rowIndex).data,'847');
            }
        }
    },
    //Crear solicitud de cambio de equipo por soporte MD
    {
        getClass: function(v, meta, rec) {
            if(rec.get('flujo')=='MD'){
                if(puedecrearSolCambioEquipoSoporte){ 
                    if(rec.get('botones')=="SI"){
                        if(rec.get('descripcionProducto')=='INTERNET')
                        {
                            if (rec.get('estado') == "Activo" && 
                                rec.get('strTieneEquipoNuevo') == "NO" &&
                                rec.get('strTieneSolCambEquiSoporte') == "NO") 
                            {
                                return 'button-grid-CrearSolXSoporte';
                            }
                            else
                            {
                                return 'button-grid-invisible';
                            }
                        }
                    }
                }
                else{
                    return 'button-grid-invisible';
                }
            }

        },
        tooltip: 'Crear Solicitud Cambio Equipo por Soporte',
        handler: function(grid, rowIndex, colIndex) {
            if(grid.getStore().getAt(rowIndex).data.flujo=="MD" &&
               grid.getStore().getAt(rowIndex).data.descripcionProducto=="INTERNET" &&
               grid.getStore().getAt(rowIndex).data.estado=="Activo" &&
               grid.getStore().getAt(rowIndex).data.strTieneEquipoNuevo=="NO" &&
               grid.getStore().getAt(rowIndex).data.strTieneSolCambEquiSoporte=="NO"){
                generarSolCambioEquipoSoporte(grid.getStore().getAt(rowIndex).data,'6497');
            }
        }
    },
    //AGREGAR ELEMENTO WIFI
    {
        getClass: function(v, meta, rec) {
            if(rec.get('flujo')=='MD'){
                var permiso = $("#ROLE_151-847");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                
                if(!boolPermiso){ 
                    return 'button-grid-invisible';
                }
                else{
                    if(rec.get('botones')=="SI" &&
                       rec.get('descripcionProducto')=='INTERNET')
                    {
                        if (rec.get('estado') == "Activo" && 
                            rec.get('tieneSolicitudAgregarEquipo') !=null && 
                            rec.get('strEsSmartWifi') == "SI") 
                        {
                            return 'button-grid-ActivarSmartWiFi';
                        }
                        else{
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
        },
        tooltip: 'Agregar Elemento Smart Wifi',
        handler: function(grid, rowIndex, colIndex) {
            if(grid.getStore().getAt(rowIndex).data.flujo=="MD"){
                if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="INTERNET"){
                    if(grid.getStore().getAt(rowIndex).data.estado=="Activo" &&
                       grid.getStore().getAt(rowIndex).data.tieneSolicitudAgregarEquipo!=null &&
                       grid.getStore().getAt(rowIndex).data.strEsSmartWifi=="SI"){
                        agregarElementoSmartWifi(grid.getStore().getAt(rowIndex).data,'847');
                    }
                    }
               
            }
        }
    },
    //CAMBIAR ELEMENTO POR SOPORTE
    {
        getClass: function(v, meta, rec) {
            if(rec.get('flujo')=='MD'){
                var permiso = $("#ROLE_151-847");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                
                if(!boolPermiso){ 
                    return 'button-grid-invisible';
                }
                else{
                    if(rec.get('botones')=="SI" &&
                       rec.get('descripcionProducto')=='INTERNET')
                    {
                        if (rec.get('estado') == "Activo" && 
                            rec.get('strTieneEquipoNuevo') == "NO" &&
                            rec.get('strTieneSolCambEquiSoporte') == "SI" &&
                            rec.get('strEstadoSolCambEquiSoporte') == "Asignada"
                            ) 
                        {
                            return 'button-grid-ActivarSmartWiFi';
                        }
                        else
                        {
                            return 'button-grid-invisible';
                        }
                    }
                }
            }            
            if(rec.get('flujo')=='TN' && rec.get('estado') == "Activo" ){
                if(rec.get('nombreProducto').includes('SEGURIDAD ELECTRONICA   VIDEO VIGILANCIA') == true &&  
                consultarLoginCam(rec.get('idPersonaEmpresaRol'),rec.get('idProducto')) == true && consultarInformacionSerie(rec.get('idServicio')) == true){
                    return 'button-grid-CrearSolXSoporte';
                }
            }
        },
        tooltip: 'Cambio de equipo por Soporte',
        handler: function(grid, rowIndex, colIndex) {
            if(grid.getStore().getAt(rowIndex).data.flujo=="MD" &&
               grid.getStore().getAt(rowIndex).data.descripcionProducto=="INTERNET" &&
               grid.getStore().getAt(rowIndex).data.estado=="Activo" &&
               grid.getStore().getAt(rowIndex).data.strTieneEquipoNuevo == "NO" &&
               grid.getStore().getAt(rowIndex).data.strTieneSolCambEquiSoporte=="SI" &&
               grid.getStore().getAt(rowIndex).data.strEstadoSolCambEquiSoporte=="Asignada"){
                cambiarElementoPorSoporte(grid.getStore().getAt(rowIndex).data,'847');
            }else if(grid.getStore().getAt(rowIndex).data.flujo=="TN" &&
            grid.getStore().getAt(rowIndex).data.nombreProducto.includes('SEGURIDAD ELECTRONICA   VIDEO VIGILANCIA')){
                generarAlCambioEquipoSoporte(grid.getStore().getAt(rowIndex).data,
                consultarLoginCam(grid.getStore().getAt(rowIndex).data.idPersonaEmpresaRol,grid.getStore().getAt(rowIndex).data.idPersonaEmpresaRolidProducto));
            }
        }
    },
    //Reintento McAfee en plan y Reintento Kaspersky en el plan o como producto adicional 
    {
        getClass: function(v, meta, rec) {
            if(rec.get('flujo')=='MD'){
                var permiso = $("#ROLE_151-847");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                
                if(!boolPermiso){ 
                    return 'button-grid-invisible';
                }
                else if((rec.get('estado') == "Activo" || rec.get('estado') == "Pendiente")  
                    && rec.get('descripcionProducto') === 'OTROS' && rec.get('strReintentoNuevoAntivirus') == "SI"){
                    return 'button-grid-reintentoNuevoAntivirus';
                }
                else if(rec.get('estado') == "Activo" && rec.get('descripcionProducto') === 'OTROS' && rec.get('strPermiteReintentoMcAfee') == "SI"){
                    return 'button-grid-reintentoMcAfee';
                }
                else
                {
                    return 'button-grid-invisible';
                }
            }
        },
        tooltip: 'Reintentar Activación',
        handler: function(grid, rowIndex, colIndex) {
            if(grid.getStore().getAt(rowIndex).data.flujo=="MD"){
                if(grid.getStore().getAt(rowIndex).data.descripcionProducto=="OTROS")
                {
                    if(grid.getStore().getAt(rowIndex).data.strPermiteReintentoMcAfee === "SI"
                        && grid.getStore().getAt(rowIndex).data.estado === "Activo")
                    {
                        reintentarActivacionMcAfeeEnPlan(grid.getStore().getAt(rowIndex).data,'847');
                    }
                    if(grid.getStore().getAt(rowIndex).data.strReintentoNuevoAntivirus === "SI")
                    {
                        if(grid.getStore().getAt(rowIndex).data.estado === "Activo" || grid.getStore().getAt(rowIndex).data.estado === "Pendiente")
                        {
                            reintentarActivacionServicio(grid.getStore().getAt(rowIndex).data);
                        }
                    }
                }
            }
        }
    },
    //Reintento proceso validación de promocion
    {
        getClass: function(v, meta, rec) {
            if(rec.get('flujo')=='MD'){
                var permiso = $("#ROLE_151-847");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                
                if(!boolPermiso){
                    return 'button-grid-invisible';
                }
                else if(rec.get('estado') == "Activo" &&
                        rec.get('descripcionProducto') === 'INTERNET' &&
                        rec.get('strReintentoPromoBw') === "SI"){
                    return 'button-grid-reintentoPromocionBW';
                }
                else{
                    return 'button-grid-invisible';
                }
            }
        },
        tooltip: 'Reintentar Proceso Promoción BW',
        handler: function(grid, rowIndex, colIndex) {
            if(grid.getStore().getAt(rowIndex).data.flujo=="MD" &&
               grid.getStore().getAt(rowIndex).data.descripcionProducto=="INTERNET" &&
               grid.getStore().getAt(rowIndex).data.strReintentoPromoBw === "SI" &&
               grid.getStore().getAt(rowIndex).data.estado === "Activo"){
                reintentarPromoBw(grid.getStore().getAt(rowIndex).data,'847');
            }
        }
    },
    //Actualización de correo McAfee pendiente de activar
    {
        getClass: function(v, meta, rec) {
            if(rec.get('flujo')=='MD'){
                if(puedeActualizarCorreoMcAfee && rec.get('descripcionProducto') === 'OTROS'
                    && ((rec.get('estado') == "Activo"  && rec.get('strPermiteReintentoMcAfee') == "SI")
                         || ( (rec.get('estado') == "Pendiente" || rec.get('estado') == "Activo") && rec.get('strReintentoNuevoAntivirus') == "SI"))
                    && !Ext.isEmpty(rec.get('intIdCaractCorreoMcAfee'))){
                    return 'button-grid-verCorreo';
                }
                else
                {
                    return 'button-grid-invisible';
                }
            }
        },
        tooltip: 'Actualizar correo por Reintento',
        handler: function(grid, rowIndex, colIndex) {
            if(grid.getStore().getAt(rowIndex).data.flujo=="MD" && 
               grid.getStore().getAt(rowIndex).data.descripcionProducto=="OTROS" 
               && ((grid.getStore().getAt(rowIndex).data.estado === "Activo" 
                    && grid.getStore().getAt(rowIndex).data.strPermiteReintentoMcAfee === "SI")
                    || ((grid.getStore().getAt(rowIndex).data.estado === "Pendiente" || grid.getStore().getAt(rowIndex).data.estado === "Activo" )
                        &&  grid.getStore().getAt(rowIndex).data.strReintentoNuevoAntivirus === "SI"))
                        && !Ext.isEmpty(grid.getStore().getAt(rowIndex).data.intIdCaractCorreoMcAfee)
              )
            {
                actualizarCorreoMcAfeeEnPlanPorReintento(grid.getStore().getAt(rowIndex).data,'847');
            }
        }
    },
    //Actualización de correo McAfee Activo - esto suele pasar cuando el correo electrónico del cliente registrado en el telcos es incorrecto
    {
        getClass: function(v, meta, rec) {
            if(rec.get('flujo')=='MD' && permiteNuevoSuscriber)
            {
                if(rec.get( 'estado') == "Activo"  &&
                   rec.get('descripcionProducto') === 'OTROS' && (rec.get('strMcAfeeActivo') == "SI" || rec.get('strNuevoAntivirusActivo') == "SI")){
                    
                    return 'button-grid-ActulizarCorreo';
                }
                else 
                {
                    return 'button-grid-invisible';
                }
            }
            else
            {
                return 'button-grid-invisible';
            }
        },
        tooltip: 'Solicitar uno nuevo Suscriber Id',
        handler: function(grid, rowIndex, colIndex) {
            if(grid.getStore().getAt(rowIndex).data.flujo=="MD" && 
               grid.getStore().getAt(rowIndex).data.descripcionProducto=="OTROS" &&
               grid.getStore().getAt(rowIndex).data.estado === "Activo"
              )
            {
                if(grid.getStore().getAt(rowIndex).data.strNuevoAntivirusActivo === "SI")
                {
                    cambiarCorreoEnServicioActivo(grid.getStore().getAt(rowIndex).data);
                }
                else if(grid.getStore().getAt(rowIndex).data.strMcAfeeActivo === "NO")
                {
                    cambiarCorreoEnServicioActivo(grid.getStore().getAt(rowIndex).data,'847');
                }
            }
        }
    },

    //Actualización de correo
    {
        getClass: function(v, meta, rec) {
            if(rec.get('flujo')=='MD'){
                if(puedeActualizarCorreoMcAfee && rec.get('estado') == "Activo"  &&
                   rec.get('descripcionProducto') === 'OTROS' && (rec.get('strMcAfeeActivo') == "SI" || rec.get('strNuevoAntivirusActivo') == "SI")){
                    return 'button-grid-verCorreo';
                }
                else if(puedeActualizarCorreoMcAfee && rec.get('estado') == "Activo"  &&
                rec.get('descripcionProducto') === 'OTROS' && rec.get('nombreProducto') == "I. PROTEGIDO MULTI PAID"
                && (rec.get('strMcAfeeActivo') == "NO" || rec.get('strNuevoAntivirusActivo') == "NO"))
                {
                    return 'button-grid-verCorreo';
                }
                else
                {
                    return 'button-grid-invisible';
                }
            }
        },
        tooltip: 'Cambiar Correo Electrónico',
        handler: function(grid, rowIndex, colIndex) {
            if(grid.getStore().getAt(rowIndex).data.flujo=="MD" && 
               grid.getStore().getAt(rowIndex).data.descripcionProducto=="OTROS" &&
               grid.getStore().getAt(rowIndex).data.estado === "Activo"
              )
            {
                actualizacionCorreo(grid.getStore().getAt(rowIndex).data);
            }
        }
    },
    //Reenvío a correo del cliente
    {
        getClass: function(v, meta, rec) {
            if(rec.get('flujo')=='MD'){
                if(puedeReenviarCorreoKaspersky && rec.get('estado') == "Activo" && 
                   rec.get('descripcionProducto') === 'OTROS' && (rec.get('strNuevoAntivirusActivo') == "SI")){
                    return 'button-grid-reenvio';
                }
                else
                {
                    return 'button-grid-invisible';
                }
            }
        },
        tooltip: 'Reenvío correo',
        handler: function(grid, rowIndex, colIndex) {
            if(grid.getStore().getAt(rowIndex).data.flujo=="MD" && 
               grid.getStore().getAt(rowIndex).data.descripcionProducto=="OTROS" &&
               grid.getStore().getAt(rowIndex).data.estado === "Activo"
              )
            {
                if(grid.getStore().getAt(rowIndex).data.strNuevoAntivirusActivo === "SI")
                {
                    reenvioCorreoKasperskyEnPlanServicioActivo(grid.getStore().getAt(rowIndex).data,'847');
                }
            }
        }
    },
    //AGREGAR EQUIPOS DUAL BAND
    {
        getClass: function(v, meta, rec) {
            if(rec.get('flujo')=='MD'){
                var permiso = $("#ROLE_151-847");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                
                if(!boolPermiso){ 
                    return 'button-grid-invisible';
                }
                else{
                    if(rec.get('botones')=="SI"){
                        if(rec.get('descripcionProducto')=='INTERNET')
                        {
                            if (( (rec.get('estado') === "EnVerificacion" || rec.get('estado') === "Activo") && 
                                rec.get('tieneSolicitudAgregarEquipo') !=null && 
                                (rec.get('strCambioAWifiDualBand') === "SI" || rec.get('strAgregaExtenderDualBand') === "SI"
                                 || rec.get('strEsCambioOntPorSolAgregarEquipo') === "SI" ))
                                || (rec.get('estado') === "Activo" && rec.get('idSolicitudMigracionExtender') !=null))
                            {
                                return 'button-grid-ActivarSmartWiFi';
                            }
                            else
                            {
                                return 'button-grid-invisible';
                            }
                        }
                    }

                }
            }
        },
        tooltip: 'Agregar Equipos Dual Band',
        handler: function(grid, rowIndex, colIndex) {
            if(grid.getStore().getAt(rowIndex).data.flujo=="MD"){
                if(grid.getStore().getAt(rowIndex).data.descripcionProducto === "INTERNET"
                    && ((grid.getStore().getAt(rowIndex).data.estado === "EnVerificacion" || grid.getStore().getAt(rowIndex).data.estado === "Activo")
                       && grid.getStore().getAt(rowIndex).data.tieneSolicitudAgregarEquipo!=null))
                {
                    //Es una solicitud por cambio de ont por creación de extender
                    if(grid.getStore().getAt(rowIndex).data.strEsCambioOntPorSolAgregarEquipo === "SI")
                    {
                        cambioEquipoOntPorSolAgregarEquipo(grid.getStore().getAt(rowIndex).data);
                    }
                    //Solo se necesita agregar el equipo Extender Dual Band
                    else if(grid.getStore().getAt(rowIndex).data.strCambioAWifiDualBand === "NO"
                       && grid.getStore().getAt(rowIndex).data.strAgregaExtenderDualBand === "SI")
                    {
                        confirmarServicioExtenderDualBand(grid.getStore().getAt(rowIndex).data,'847',"PLAN");
                    }
                    else if(grid.getStore().getAt(rowIndex).data.strCambioAWifiDualBand === "SI")
                    {
                        cambioEquiposDualBand(grid.getStore().getAt(rowIndex).data);
                    }
                }
                else if(grid.getStore().getAt(rowIndex).data.descripcionProducto === "INTERNET"
                    && grid.getStore().getAt(rowIndex).data.estado === "Activo"
                    && grid.getStore().getAt(rowIndex).data.idSolicitudMigracionExtender != null)
                {
                    confirmarServicioExtenderDualBand(grid.getStore().getAt(rowIndex).data,'847',"MIGRACION");
                }
            }
        }
    },
    //TRASLADAR EXTENDER DUAL BAND
    {
        getClass: function(v, meta, rec) {
            if(rec.get('flujo')=='MD'){
                var permiso = $("#ROLE_151-847");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                
                if(!boolPermiso){ 
                    return 'button-grid-invisible';
                }
                else{
                    if(rec.get('botones')=="SI"){
                        if(rec.get('descripcionProducto')=='INTERNET')
                        {
                            if ( rec.get('estado') === "Activo" && 
                                 rec.get('tipoOrden') === "T"   && 
                                 rec.get('strTrasladarExtenderDB') === "SI") 
                            {
                                return 'button-grid-ActivarSmartWiFi';
                            }
                            else
                            {
                                return 'button-grid-invisible';
                            }
                        }
                        else if (rec.get('descripcionProducto')=='EXTENDER_DUAL_BAND')
                        {
                            if ( (rec.get('estado') === "Pendiente"
                                 || rec.get('estado') === "Asignada") && 
                                 rec.get('tipoOrden') === "T"   && 
                                 rec.get('strTrasladarExtenderDB') === "SI" &&
                                 Ext.isEmpty(rec.get('nombrePlan'))) 
                            {
                                return 'button-grid-ActivarSmartWiFi';
                            }
                            else
                            {
                                return 'button-grid-invisible';
                            }
                        }
                        else if (rec.get('descripcionProducto')=='WDB_Y_EDB')
                        {
                            if ( rec.get('estado') === "PendienteAp" && 
                                 rec.get('tipoOrden') === "T"   && 
                                 rec.get('strTrasladarExtenderDB') === "SI") 
                            {
                                return 'button-grid-ActivarSmartWiFi';
                            }
                            else
                            {
                                return 'button-grid-invisible';
                            }
                        }
                    }

                }
            }
        },
        tooltip: 'Trasladar Extender Dual Band',
        handler: function(grid, rowIndex, colIndex) {
            if(grid.getStore().getAt(rowIndex).data.flujo=="MD" &&
               ( 
                ( grid.getStore().getAt(rowIndex).data.descripcionProducto === "INTERNET" &&
                  grid.getStore().getAt(rowIndex).data.estado === "Activo" ) ||
                ( grid.getStore().getAt(rowIndex).data.descripcionProducto === "EXTENDER_DUAL_BAND" &&
                  (grid.getStore().getAt(rowIndex).data.estado === "Pendiente" || 
                   grid.getStore().getAt(rowIndex).data.estado === "Asignada")) || 
                  ( grid.getStore().getAt(rowIndex).data.descripcionProducto === "WDB_Y_EDB" &&
                  grid.getStore().getAt(rowIndex).data.estado === "PendienteAp")
               ) &&
               grid.getStore().getAt(rowIndex).data.tipoOrden === "T" &&
               grid.getStore().getAt(rowIndex).data.strTrasladarExtenderDB === "SI")
            {
                    
                trasladarExtenderDualBand(grid.getStore().getAt(rowIndex).data);
            }
        }
    },
    //SINCRONIZAR EXTENDER DUAL BAND
    {
        getClass: function(v, meta, rec) {
            if(rec.get('flujo')=='MD'){
                var permiso = $("#ROLE_151-847");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                
                if(!boolPermiso){ 
                    return 'button-grid-invisible';
                }
                else{
                    if(rec.get('botones')=="SI"){
                        if(rec.get('descripcionProducto')=='INTERNET')
                        {
                            if ( rec.get('estado') === "Activo" && 
                                 rec.get('strSincronizarExtenderDB') === "SI") 
                            {
                                return 'button-grid-ActivarSmartWiFi';
                            }
                            else
                            {
                                return 'button-grid-invisible';
                            }
                        }
                        else if (rec.get('descripcionProducto')=='EXTENDER_DUAL_BAND' || rec.get('descripcionProducto')=='WDB_Y_EDB')
                        {
                            if ( rec.get('estado') === "Activo" && 
                                 rec.get('strSincronizarExtenderDB') === "SI" &&
                                 Ext.isEmpty(rec.get('nombrePlan'))) 
                            {
                                return 'button-grid-ActivarSmartWiFi';
                            }
                            else
                            {
                                return 'button-grid-invisible';
                            }
                        }
                    }

                }
            }
        },
        tooltip: 'Sincronizar Extender Dual Band',
        handler: function(grid, rowIndex, colIndex) {
            if(grid.getStore().getAt(rowIndex).data.flujo=="MD" &&
               ( 
                ( grid.getStore().getAt(rowIndex).data.descripcionProducto === "INTERNET" &&
                  grid.getStore().getAt(rowIndex).data.estado === "Activo" ) ||
                ( (grid.getStore().getAt(rowIndex).data.descripcionProducto === "EXTENDER_DUAL_BAND"
                    || grid.getStore().getAt(rowIndex).data.descripcionProducto === "WDB_Y_EDB") &&
                  grid.getStore().getAt(rowIndex).data.estado === "Activo")
               ) &&
               grid.getStore().getAt(rowIndex).data.strSincronizarExtenderDB === "SI")
            {
                    
                sincronizarExtenderDualBand(grid.getStore().getAt(rowIndex).data);
            }
        }
    },
    //GRABAR PARAMETROS INICIALES Y ACTIVAR SERVICIO
    {
        getClass: function(v, meta, rec) {
            if(rec.get('flujo')=='TTCO'){
                var permiso = $("#ROLE_151-848");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                //alert(typeof permiso);
                if(!boolPermiso){ 
                    return 'button-grid-invisible';
                }
                else{   
                    if(rec.get('descripcionProducto')=='INTERNET'){
                        if (rec.get('estado') == "EnVerificacion" ) {
                            this.items[17].tooltip = 'Grabar Parametros Iniciales';
                            return 'button-grid-grabarHistorial';
                        }
                        else{
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
            if(rec.get('flujo')=='MD'){
                var permiso = $("#ROLE_151-848");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                //alert(typeof permiso);
                if(!boolPermiso){ 
                    return 'button-grid-invisible';
                }
                else{
                    if(rec.get('descripcionProducto')==='INTERNET' || rec.get('descripcionProducto')==='INTERNET SMALL BUSINESS'
                        || rec.get('descripcionProducto') === 'TELCOHOME'){
                        if (rec.get('estado') == "EnVerificacion" 
                            && (rec.get('strActivacionOrigen') === '' || rec.get('strActivacionOrigen') === "WEB")
                            && rec.get('strPermiteActivarServicio') === "SI") {
                            this.items[17].tooltip = 'Activar Servicio';
                            return 'button-grid-confirmarActivacion';
                        }
                        else{
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
            if(rec.get('flujo')=='TNP'){
                var permisoTnp = $("#ROLE_151-848");
                var boolPermisoTnp = (typeof permisoTnp === 'undefined') ? false : (permisoTnp.val() == 1 ? true : false);          
                if(!boolPermisoTnp){ 
                    return 'button-grid-invisible';
                }
                else{
                    if(rec.get('descripcionProducto')==='INTERNET'){
                        if (rec.get('estado') == "EnVerificacion" ) {
                            this.items[17].tooltip = 'Activar Servicio';
                            return 'button-grid-confirmarActivacion';
                        }
                        else{
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
        },
        handler: function(grid, rowIndex, colIndex) {
            if(grid.getStore().getAt(rowIndex).data.flujo=="TTCO"){
                if(grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion" ){
                    grabarHistorial(grid.getStore().getAt(rowIndex).data, grid);

                }
            }
            if(grid.getStore().getAt(rowIndex).data.flujo=="MD"){
                if(grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion" 
                    && (grid.getStore().getAt(rowIndex).data.strActivacionOrigen === ""
                        || grid.getStore().getAt(rowIndex).data.strActivacionOrigen === "WEB")
                    && grid.getStore().getAt(rowIndex).data.strPermiteActivarServicio === "SI"){
                    grabarHistorialMd(grid.getStore().getAt(rowIndex).data, grid,'847');

                }
            }
            if(grid.getStore().getAt(rowIndex).data.flujo=="TNP" &&
               grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion"){
                grabarHistorialMd(grid.getStore().getAt(rowIndex).data, grid,'847');
            }
        }
    },
    //VER PARAMETROS INICIALES
    {
        getClass: function(v, meta, rec) {
            if(rec.get('flujo')=='TTCO'){
                var permiso = $("#ROLE_151-849");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                //alert(typeof permiso);
                if(!boolPermiso){ 
                    return 'button-grid-invisible';
                }
                else{
                    if(rec.get('descripcionProducto')=='INTERNET'){
                        if ( (rec.get('estado') == "EnPruebas" || rec.get('estado') == "Activo" ) && rec.get('ultimaMilla')=="Cobre") {
                            return 'button-grid-verParametrosIniciales';
                        }
                        else{
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
            if(rec.get('flujo')=='MD'){
                var permiso = $("#ROLE_151-849");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                //alert(typeof permiso);
                if(!boolPermiso){ 
                    return 'button-grid-invisible';
                }
                else{
                    if(rec.get('descripcionProducto')=='INTERNET'){
                        if ( (rec.get('estado') == "EnPruebas" || rec.get('estado') == "Activo" ) && rec.get('ultimaMilla')=="Fibra Optica") {
                            return 'button-grid-verParametrosIniciales';
                        }
                        else{
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
        },
        tooltip: 'Ver Parametros Iniciales',
        handler: function(grid, rowIndex, colIndex) {
            if(grid.getStore().getAt(rowIndex).data.flujo=="TTCO"){
                if(grid.getStore().getAt(rowIndex).data.estado=="EnPruebas" || 
                   grid.getStore().getAt(rowIndex).data.estado=="Activo"){
                    verParametrosIniciales(grid.getStore().getAt(rowIndex).data, grid);
                }
            }
            if(grid.getStore().getAt(rowIndex).data.flujo=="MD"){
                if(grid.getStore().getAt(rowIndex).data.estado=="EnPruebas" || 
                   grid.getStore().getAt(rowIndex).data.estado=="Activo"){
                    verParametrosInicialesMd(grid.getStore().getAt(rowIndex).data, grid);
                }
            }
        }
    },
    //CAMBIO DE PLAN
    {
        getClass: function(v, meta, rec) {
            if (rec.get('intServicioFTTxTN') !== null) {
                return 'button-grid-invisible';
            }
            if(rec.get('flujo')=='TTCO'){
                if(rec.get('descripcionProducto')=='INTERNET'){
                    var permiso = $("#ROLE_151-829");
                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                    //alert(typeof permiso);
                    if(!boolPermiso){ 
                        return 'button-grid-invisible';
                    }
                    else{
                        if (rec.get('estado') == "Activo") {
                            return 'button-grid-cambioVelocidad';
                        }
                        else{
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
            if(rec.get('flujo')=='MD'){
                if(rec.get('descripcionProducto')=='INTERNET' || rec.get('descripcionProducto')==='INTERNET SMALL BUSINESS'
                    || rec.get('descripcionProducto')==='TELCOHOME'){
                    var permiso = $("#ROLE_151-829");
                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                    //alert(typeof permiso);
                    if(!boolPermiso){ 
                        return 'button-grid-invisible';
                    }
                    else{
                        if (rec.get('estado') == "Activo" && rec.get('boolVisualizarBotonCambioVelocidad') ==='S') {
                            return 'button-grid-cambioVelocidad';
                        }
                        else{
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
            if(rec.get('flujo')=='TNP' &&
               rec.get('descripcionProducto')=='INTERNET' && !Ext.isEmpty(rec.get('nombrePlan'))){
                var permisoTnp = $("#ROLE_151-829");
                var boolPermisoTnp = (typeof permisoTnp === 'undefined') ? false : (permisoTnp.val() == 1 ? true : false);
                if(!boolPermisoTnp){ 
                    return 'button-grid-invisible';
                }
                else{
                    if (rec.get('estado') == "Activo") {
                        return 'button-grid-cambioVelocidad';
                    }
                    else{
                        return 'button-grid-invisible';
                    }
                }
            }
        },
        tooltip: 'Cambio de Velocidad',
        handler: function(grid, rowIndex, colIndex) {
            if (grid.getStore().getAt(rowIndex).data.intServicioFTTxTN !== null) {
                return 'button-grid-invisible';
            }
            if(grid.getStore().getAt(rowIndex).data.flujo=="TTCO"){
                if(grid.getStore().getAt(rowIndex).data.estado=="Activo"){
                    cambioPlanCliente(grid.getStore().getAt(rowIndex).data);
                }
            }
            if(grid.getStore().getAt(rowIndex).data.flujo=="MD"){
                if(grid.getStore().getAt(rowIndex).data.estado=="Activo"){
                    if( grid.getStore().getAt(rowIndex).data.descripcionProducto === "INTERNET SMALL BUSINESS"
                        || grid.getStore().getAt(rowIndex).data.descripcionProducto === "TELCOHOME"){
                        cambioPlanClienteIsbTn(grid.getStore().getAt(rowIndex).data);
                    }
                    else
                    {
                        cambioPlanClienteMd(grid.getStore().getAt(rowIndex).data);
                    }
                }
            }
            if(grid.getStore().getAt(rowIndex).data.flujo=="TNP" &&
               grid.getStore().getAt(rowIndex).data.estado=="Activo"){
                cambioPlanClienteMd(grid.getStore().getAt(rowIndex).data);
            }
        }
    },
    //CAMBIAR PUERTO
    {
        getClass: function(v, meta, rec) {
            if(rec.get('flujo')=='TTCO'){
                if(rec.get('descripcionProducto')=='INTERNET'){
                    var permiso = $("#ROLE_151-832");
                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                    //alert(typeof permiso);
                    if(!boolPermiso){ 
                        return 'button-grid-invisible';
                    }
                    else{
                        if ( (rec.get('estado') == "Activo" || rec.get('estado') == "Asignada"  || rec.get('estado') == "EnVerificacion" || rec.get('estado') == "In-Corte") && (rec.get('tipoOrden')!='R') ) {
                            return 'button-grid-cambiarPuerto';
                        }
                        else if( rec.get('estado') == "Activo" && rec.get('tipoOrden')=='R'){
                            return 'button-grid-cambiarPuerto';
                        }
                        else{
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
            if(rec.get('flujo')=='MD'){
                if(rec.get('descripcionProducto')=='INTERNET'){
                    var permiso = $("#ROLE_151-832");
                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                    //alert(typeof permiso);
                    if(!boolPermiso){ 
                        return 'button-grid-invisible';
                    }
                    else{
                        if ( (rec.get('estado') == "Activo" || rec.get('estado') == "Asignada" 
                              || rec.get('estado') == "EnVerificacion" || rec.get('estado') == "In-Corte") && (rec.get('tipoOrden')!='R') ) {
                            return 'button-grid-cambiarPuerto';
                        }
                        else if( (rec.get('estado') == "Activo" ) && (rec.get('tipoOrden')=='R')){
                            return 'button-grid-cambiarPuerto';
                        }
                        else{
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
            if(rec.get('flujo')=='TNP' && 
               rec.get('descripcionProducto')=='INTERNET' && !Ext.isEmpty(rec.get('nombrePlan'))){
                var permisoTnp = $("#ROLE_151-832");
                var boolPermisoTnp = (typeof permisoTnp === 'undefined') ? false : (permisoTnp.val() == 1 ? true : false);          
                if(!boolPermisoTnp){ 
                    return 'button-grid-invisible';
                }
                else{
                    if ( (rec.get('estado') == "Activo" || rec.get('estado') == "Asignada" 
                          || rec.get('estado') == "EnVerificacion" || rec.get('estado') == "In-Corte") && (rec.get('tipoOrden')!='R') ) {
                        return 'button-grid-cambiarPuerto';
                    }
                    else if( (rec.get('estado') == "Activo" ) && (rec.get('tipoOrden')=='R')){
                        return 'button-grid-cambiarPuerto';
                    }
                    else{
                        return 'button-grid-invisible';
                    }
                }
            }
            if(rec.get('flujo')=='TN'){
                if(rec.get('descripcionProducto')=='SAFECITYDATOS' || rec.get('descripcionProducto')=='SAFECITYWIFI'
                   || rec.get('descripcionProducto')=='SAFECITYSWPOE'){
                    var permisoCp = $("#ROLE_151-832");
                    var boolPermisoCp = (typeof permisoCp === 'undefined') ? false : (permisoCp.val() == 1 ? true : false);
                    if(!boolPermisoCp){
                        return 'button-grid-invisible';
                    }
                    else{
                        if(rec.get('estado') == "Activo" || rec.get('estado') == "In-Corte"){
                            return 'button-grid-cambiarPuerto';
                        }
                        else{
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
        },
        tooltip: 'Cambiar Puerto',
        handler: function(grid, rowIndex, colIndex) {
            if(grid.getStore().getAt(rowIndex).data.flujo=="TTCO"){
                if( (grid.getStore().getAt(rowIndex).data.estado=="Activo" || 
                     grid.getStore().getAt(rowIndex).data.estado=="Asignada" || 
                     grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion" || 
                     grid.getStore().getAt(rowIndex).data.estado=="In-Corte") && 
                     (grid.getStore().getAt(rowIndex).data.tipoOrden!='R') ){
                    cambiarPuerto(grid.getStore().getAt(rowIndex).data, grid);
                }
                else if(grid.getStore().getAt(rowIndex).data.estado=="Activo" && 
                     (grid.getStore().getAt(rowIndex).data.tipoOrden=='R') ){
                    cambiarPuerto(grid.getStore().getAt(rowIndex).data, grid);
                }
            }
            if(grid.getStore().getAt(rowIndex).data.flujo=="MD"){
                if( (grid.getStore().getAt(rowIndex).data.estado=="Activo" || 
                     grid.getStore().getAt(rowIndex).data.estado=="Asignada" || 
                     grid.getStore().getAt(rowIndex).data.estado=="In-Corte") && 
                     (grid.getStore().getAt(rowIndex).data.tipoOrden!='R') ){
                    cambiarPuertoMd(grid.getStore().getAt(rowIndex).data, grid);
                }
                else if((grid.getStore().getAt(rowIndex).data.estado=="Activo") &&
                     (grid.getStore().getAt(rowIndex).data.tipoOrden=='R') ){
                    cambiarPuertoMd(grid.getStore().getAt(rowIndex).data, grid);
                }
            }
            if(grid.getStore().getAt(rowIndex).data.flujo=="TNP" &&
               (grid.getStore().getAt(rowIndex).data.estado=="Activo" || 
                grid.getStore().getAt(rowIndex).data.estado=="Asignada" || 
                grid.getStore().getAt(rowIndex).data.estado=="In-Corte") && 
               (grid.getStore().getAt(rowIndex).data.tipoOrden!='R') ){
                cambiarPuertoMd(grid.getStore().getAt(rowIndex).data, grid);
            }
            if(grid.getStore().getAt(rowIndex).data.flujo=="TN" && 
               (grid.getStore().getAt(rowIndex).data.estado=="Activo" ||
                grid.getStore().getAt(rowIndex).data.estado=="In-Corte") ){
                cambiarPuertoTN(grid.getStore().getAt(rowIndex).data, grid);
            }
        }
    },
    //ACTUALIZAR INDICE CLIENTE
    {
        getClass: function(v, meta, rec) 
        {
            if(rec.get('flujo')=='MD')
            {
                var permiso = $("#ROLE_151-1417");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                //alert(typeof permiso);
                if(!boolPermiso)
                { 
                    return 'button-grid-invisible';
                }
                else
                {
                    if(rec.get('descripcionProducto')=='INTERNET')
                    {
                        if ( rec.get('estado') == "Activo" || rec.get('estado') == "Cancel" ) 
                        {
                            return 'button-grid-actualizarIndiceCliente';
                        }
                        else
                        {
                            return 'button-grid-invisible';
                        }
                    }
                }
            }//if(rec.get('flujo')=='MD')
        },
        tooltip: 'Actualizar Indice Cliente',
        handler: function(grid, rowIndex, colIndex) 
        {
            if(grid.getStore().getAt(rowIndex).data.flujo=="MD")
            {
                if( (grid.getStore().getAt(rowIndex).data.estado=="Activo") || (grid.getStore().getAt(rowIndex).data.estado=="Cancel") )
                {
                    updateIndiceCliente(grid.getStore().getAt(rowIndex).data);
                }
            }
        }
    },
    //VER DOMINIOS
    {
        getClass: function(v, meta, rec) {
            var permiso = $("#ROLE_151-850");
            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
            //alert(typeof permiso);
            if(!boolPermiso){ 
                return 'button-grid-invisible';
            }
            else{
                if(rec.get('prefijoEmpresa')=='TN' && (rec.get('descripcionProducto')=='HOSTING' || rec.get('descripcionProducto')=='DOMINIO' )){
                    if ( rec.get('estado') != "Cancel" ) {
                        return 'button-grid-verDominio';
                    }
                    else{
                        return 'button-grid-invisible';
                    }
                }
            }
        },
        tooltip: 'Ver Dominios',
        handler: function(grid, rowIndex, colIndex) {
            if( (grid.getStore().getAt(rowIndex).data.estado!="Cancel") ){
                verInformacionDominio(grid.getStore().getAt(rowIndex).data, grid);
            }
        }
    },
    //AGREGAR DOMINIO
    {
        getClass: function(v, meta, rec) {            
            
            var permiso = $("#ROLE_151-851");
            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false); 
            if(!boolPermiso){ 
                return 'button-grid-invisible';
            }
            else{
                
                if( rec.get('descripcionProducto')=='HOSTING' || rec.get('descripcionProducto')=='DOMINIO'){                    
                    //if ( (rec.get('estado') == "PreAsignacionInfoTecnica") && (rec.get('cantidad') < rec.get('cantidadReal')))
                    if ( rec.get('prefijoEmpresa')=='MD' ){
                        if ( (rec.get('estado') != "Cancel" && (rec.get('cantidad') < rec.get('cantidadReal')))) {
                            return 'button-grid-agregarDominio';
                        }
                        else{
                            return 'button-grid-invisible';
                        }
                    }else if ( rec.get('prefijoEmpresa')=='TN' ){
                        if ( (rec.get('estado') == "PreAsignacionInfoTecnica") || (rec.get('estado') == "Activo") && (rec.get('cantidad') < rec.get('cantidadReal'))){
                            return 'button-grid-agregarDominio';
                        }
                        else{
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
        },
        tooltip: 'Agregar Dominio',
        handler: function(grid, rowIndex, colIndex) {
            if( (grid.getStore().getAt(rowIndex).data.estado!="Cancel" && (grid.getStore().getAt(rowIndex).data.cantidadReal!=0 && grid.getStore().getAt(rowIndex).data.cantidadReal>0)) ){
                agregarDominio(grid.getStore().getAt(rowIndex).data, grid);
            }
        }
    },
    //VER CORREOS
    {
        getClass: function(v, meta, rec) {
            var permiso = $("#ROLE_151-853");
            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
            //alert(typeof permiso);            
            if(!boolPermiso){ 
                return 'button-grid-invisible';
            }
            else{
                if(rec.get('descripcionProducto')=='CORREO' || rec.get('descripcionProducto')=='CORREOSEGURO'){
                    if ( rec.get('estado') != "Cancel" && rec.get('estado') != "Pendiente" ) {
                        return 'button-grid-verCorreo';
                    }
                    else{
                        return 'button-grid-invisible';
                    }
                }
            }
        },
        tooltip: 'Ver Correos',
        handler: function(grid, rowIndex, colIndex) {
            if( grid.getStore().getAt(rowIndex).data.estado!="Cancel" ){
                verInformacionCorreo(grid.getStore().getAt(rowIndex).data, grid);
            }
        }
    },
    //AGREGAR CORREOS 
    {
        getClass: function(v, meta, rec) {
            var permiso = $("#ROLE_151-854");
            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
            if(!boolPermiso){ 
                return 'button-grid-invisible';
            }
            else{
                if(rec.get('descripcionProducto')==='CORREO'){                    
                    //if ( rec.get('estado') != "Cancel" && (rec.get('cantidad')!==0 && rec.get('cantidad')>0)) {
                    if ( rec.get('prefijoEmpresa')=='MD' ){
                        if ( (rec.get('estado') != "Cancel" ) && (rec.get('cantidad') < rec.get('cantidadReal'))) {
                            return 'button-grid-agregarCorreo';
                        }
                        else{
                            return 'button-grid-invisible';
                        }
                    }
                    
                     if ( rec.get('prefijoEmpresa')=='TN' ){
                        if ( (rec.get('estado') == "PreAsignacionInfoTecnica") || (rec.get('estado') == "Activo") && (rec.get('cantidad') < rec.get('cantidadReal'))) {
                            return 'button-grid-agregarCorreo';
                        }
                        else{
                            return 'button-grid-invisible';
                        }
                    }
                }
            }
        },
        tooltip: 'Agregar Correos',
        handler: function(grid, rowIndex, colIndex) {
            //if( (grid.getStore().getAt(rowIndex).data.estado1="Cancel" && (grid.getStore().getAt(rowIndex).data.cantidad!=0 && grid.getStore().getAt(rowIndex).data.cantidad>0)) ){            
            if (grid.getStore().getAt(rowIndex).data.prefijoEmpresa == "MD"){    
                agregarCorreo(grid.getStore().getAt(rowIndex).data, grid);
            }
            if(grid.getStore().getAt(rowIndex).data.prefijoEmpresa=="TN"){
               agregarCorreoTn(grid.getStore().getAt(rowIndex).data, grid);
            }

            
        }
    },
    //AGREGAR CREDENCIALES NETLIFE WIFI
    {
        getClass: function(v, meta, rec) {
            if (!rolGenerarCredencialesNetlifeZone) {
                return 'button-grid-invisible';
            }
            else{
                if ( rec.get('prefijoEmpresa')==='MD' ){
                    if(rec.get('descripcionProducto')==='NETWIFI' && 
                        (rec.get('estado') === "PreAsignacionInfoTecnica"))
                    {
                        return 'button-grid-ActivarWiFi';
                    }
                    else
                    {
                        return 'button-grid-invisible';
                    }
                }
            }
        },
        tooltip: 'Activar Servicio Netlife Zone',
        handler: function(grid, rowIndex, colIndex) {
            if (grid.getStore().getAt(rowIndex).data.prefijoEmpresa === "MD")
            {    
                if(grid.getStore().getAt(rowIndex).data.descripcionProducto ==='NETWIFI' && 
                   grid.getStore().getAt(rowIndex).data.estado === "PreAsignacionInfoTecnica")
                {
                    agregarCredencialesWifi(grid.getStore().getAt(rowIndex).data, grid);
                }
            } 
        }
    },
    //Recuperar CREDENCIALES NETLIFE WIFI
    {
        getClass: function(v, meta, rec) {
            if (!rolRestablecerContrasenaNetlifeZone) {
                return 'button-grid-invisible';
            }
            else{
                if ( rec.get('prefijoEmpresa')=='MD' ){
                    if(rec.get('descripcionProducto')==='NETWIFI' && 
                        (rec.get('estado') === "Activo")){
                        return 'button-grid-RecuperarClaveWiFi';
                    }
                    else{
                        return 'button-grid-invisible';
                    }
                }
            }
        },
        tooltip: 'Recuperar Clave Servicio Netlife Zone',
        handler: function(grid, rowIndex, colIndex) {
            if (grid.getStore().getAt(rowIndex).data.prefijoEmpresa === "MD")
            {    
                if(grid.getStore().getAt(rowIndex).data.descripcionProducto ==='NETWIFI' && 
                   grid.getStore().getAt(rowIndex).data.estado === "Activo")
                {
                    resetearCredencialesWifi(grid.getStore().getAt(rowIndex).data);
                }
            } 
        }
    },
    //AGREGAR CREDENCIALES SSID_MOVIL
    {
        getClass: function(v, meta, rec) {

            var permiso = $("#ROLE_151-7838");
            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

            if(!boolPermiso){
                return 'button-grid-invisible';
            }
            else{
                if ( rec.get('prefijoEmpresa')==='TN' ){
                    if(rec.get('descripcionProducto')==='NETWIFI' &&
                        (rec.get('estado') === "PreAsignacionInfoTecnica"))
                    {
                        return 'button-grid-ActivarWiFi';
                    }
                    else
                    {
                        return 'button-grid-invisible';
                    }
                }
            }
        },
        tooltip: 'Activar Servicio SSID MOVIL',
        handler: function(grid, rowIndex, colIndex) {
            if (grid.getStore().getAt(rowIndex).data.prefijoEmpresa === "TN")
            {    
                if(grid.getStore().getAt(rowIndex).data.descripcionProducto ==='NETWIFI' && 
                   grid.getStore().getAt(rowIndex).data.estado === "PreAsignacionInfoTecnica")
                {
                    agregarCredencialesWifi(grid.getStore().getAt(rowIndex).data);
                }
            } 
        }
    },
    //Recuperar CREDENCIALES SSID_MOVIL
    {
        getClass: function(v, meta, rec) {
            var permiso = $("#ROLE_151-7857");
            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

            if(!boolPermiso){
                return 'button-grid-invisible';
            }
            else{
                if ( rec.get('prefijoEmpresa')=='TN' ){
                    if(rec.get('descripcionProducto')==='NETWIFI' && 
                        (rec.get('estado') === "Activo")){
                        return 'button-grid-RecuperarClaveWiFi';
                    }
                    else{
                        return 'button-grid-invisible';
                    }
                }
            }
        },
        tooltip: 'Recuperar Clave Servicio SSID MOVIL',
        handler: function(grid, rowIndex, colIndex) {
            if (grid.getStore().getAt(rowIndex).data.prefijoEmpresa === "TN")
            {    
                if(grid.getStore().getAt(rowIndex).data.descripcionProducto ==='NETWIFI' && 
                   grid.getStore().getAt(rowIndex).data.estado === "Activo")
                {
                    resetearCredencialesWifi(grid.getStore().getAt(rowIndex).data);
                }
            } 
        }
    },    
    //VER IP PUBLICA
    {
        getClass: function(v, meta, rec) {
            var permiso = $("#ROLE_151-835");
            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
            //alert(typeof permiso);
            if(!boolPermiso){ 
                return 'button-grid-invisible';
            }
            else{
                if(rec.get('descripcionProducto')=='IP' || rec.get('descripcionProducto') === 'IPSB'){
                //if ( rec.get('estado') == "Activo" ) {
                    return 'button-grid-verIpPublica';
                }
                else{
                    return 'button-grid-invisible';
                }
            }
        },
        tooltip: 'Ver Ip(s)',
        handler: function(grid, rowIndex, colIndex) {
            //if( (grid.getStore().getAt(rowIndex).data.estado=="Activo") ){
                verInformacionIpPublica(grid.getStore().getAt(rowIndex).data, grid);
            //}
        }
    },
    //CAMBIO DE LINEA PON
    {
        getClass: function(v, meta, rec) {
            var permiso = $("#ROLE_151-1517");
            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
            //alert(typeof permiso);
            if(!boolPermiso){ 
                return 'button-grid-invisible';
            }
            else{
                if((rec.get('idSolicitudLineaPom')!=null && rec.get('descripcionProducto')=='INTERNET' && (prefijoEmpresa=='MD' || prefijoEmpresa=='EN'))
                    || (rec.get('idSolicitudLineaPom')!=null && rec.get('descripcionProducto')==='INTERNET SMALL BUSINESS' && prefijoEmpresa==='TN')
                    || (rec.get('idSolicitudLineaPom')!=null && rec.get('descripcionProducto')==='DATOS SAFECITY' && prefijoEmpresa==='TN')
                    || (rec.get('idSolicitudLineaPom')!=null && rec.get('descripcionProducto')==='INTERNET SMALL BUSINESS' && prefijoEmpresa==='TNP')
                    || (rec.get('idSolicitudLineaPom')!=null && rec.get('descripcionProducto')==='TELCOHOME' && prefijoEmpresa==='TN')
                    || (rec.get('idSolicitudLineaPom')!=null && rec.get('descripcionProducto')==='INTERNET' && 
                        !Ext.isEmpty(rec.get('nombrePlan')) && prefijoEmpresa==='TNP')
                  )
                {//jv
                //if ( rec.get('estado') == "Activo" ) {
                    return 'button-grid-cambiarEstado';
                }
                else{
                    return 'button-grid-invisible';
                }
            }
        },
        tooltip: 'Cambio de Línea Pon',
        handler: function(grid, rowIndex, colIndex) {
            //if( (grid.getStore().getAt(rowIndex).data.estado=="Activo") ){
                cambioLineaPom(grid.getStore().getAt(rowIndex).data, grid);
            //}
        }
    },
    //AGREGAR IP PUBLICA
    {
        getClass: function(v, meta, rec) {
            if(rec.get('descripcionProducto')==='IP'){
                var permiso = $("#ROLE_151-836");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                //alert(typeof permiso);
                if(!boolPermiso){ 
                    return 'button-grid-invisible';
                }
                else{
                    if ( (rec.get('cantidad')!==0 && rec.get('cantidad')>0 && rec.get('flujo') == "TTCO")) {
                        return 'button-grid-agregarIpPublica';
                    }
                    else{
                        return 'button-grid-invisible';
                    }
                }
            }
        },
        tooltip: 'Agregar Ip Publica',
        handler: function(grid, rowIndex, colIndex) {
            if( ( (grid.getStore().getAt(rowIndex).data.cantidad!=0 && grid.getStore().getAt(rowIndex).data.cantidad>0)) ){
                agregarIpPublica(grid.getStore().getAt(rowIndex).data, grid);
            }
        }
    },  
    //Solicitar factibilidad
    {
        getClass: function(v, meta, rec) {
            var permiso = $("#ROLE_151-833");
            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
            if (boolPermiso && rec.get('prefijoEmpresa')  == 'TN' &&  (rec.get('estado') == 'Pendiente' || rec.get('estado') == 'Pre-servicio') 
                            && rec.get('strFlagActivSim') == 'S')                                                  
            {
                return 'button-grid-solicitarFactibilidad';
            }
            else
                return 'button-grid-invisible';
        },
        tooltip: 'Solicitar Factibilidad',
        handler: function(grid, rowIndex, colIndex) {
            solicitarFactibilidad(grid.getStore().getAt(rowIndex).data);
        }
    },
    //VER LOGS
    {
        getClass: function(v, meta, rec) {
            if (rec.get('intServicioFTTxTN') !== null) {
                return 'button-grid-invisible';
            }
            var permiso = $("#ROLE_151-833");
            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
            
            if (!boolPermiso) {
                return 'button-grid-invisible';
            }
            else
                return 'button-grid-logs';
        },
        tooltip: 'Ver Logs',
        handler: function(grid, rowIndex, colIndex) {
            verLogsServicio(grid.getStore().getAt(rowIndex).data);
        }
    },
    //Reenviar Información
    {
        getClass: function (v, meta, rec) {
            var permiso = $("#ROLE_151-4637");
            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

            if (!boolPermiso) {
                return 'button-grid-invisible';
            } 
            else
            {
                if (rec.get('estado') != 'Pendiente' && rec.get('esNetlifeCloud') == 'S') {
                    return 'button-grid-verMacs';
                } else {
                    return 'button-grid-invisible';
                }
            }
        },
        tooltip: 'Reenviar Información NetlifeCloud',
        handler: function (grid, rowIndex, colIndex) {
            reenviarInformacionOffice(grid.getStore().getAt(rowIndex).data);
        }
    },
    //CONSULTAR LDAP
    {
        getClass: function(v, meta, rec) {
            if (rec.get('intServicioFTTxTN')!== null) {
                return 'button-grid-invisible';
            }
            if(rec.get('flujo')=='MD')
            {
                if ((rec.get('descripcionProducto') === 'INTERNET' || rec.get('descripcionProducto')==='INTERNET SMALL BUSINESS'
                    || rec.get('descripcionProducto') === 'TELCOHOME' )
                    && rec.get('ldap') === "SI" ) 
                {
                    var permiso = $("#ROLE_151-2420");
                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                    if (!boolPermiso) {
                        return 'button-grid-invisible';
                    }
                    else
                        return 'button-grid-ver-ldap';
                }
            }
            if(rec.get('flujo')=='TNP' && 
               (rec.get('descripcionProducto') === 'INTERNET' && !Ext.isEmpty(rec.get('nombrePlan'))) &&
               rec.get('ldap') === "SI" )
            {
                var permisoTnp = $("#ROLE_151-2420");
                var boolPermisoTnp = (typeof permisoTnp === 'undefined') ? false : (permisoTnp.val() == 1 ? true : false);
                if (!boolPermisoTnp) {
                    return 'button-grid-invisible';
                }
                else
                {
                    return 'button-grid-ver-ldap';
                }
            }
        },
        tooltip: 'Consultar LDAP',
        handler: function(grid, rowIndex, colIndex) {
            verLdapServicio(grid.getStore().getAt(rowIndex).data);
        }
    },
    //CREAR CLIENTE EN LDAP
    {
        getClass: function(v, meta, rec) {
            if (rec.get('intServicioFTTxTN') !== null) {
                return 'button-grid-invisible';
            }
            if(rec.get('flujo')=='MD')
            {
                if ((rec.get('descripcionProducto') === 'INTERNET' || rec.get('descripcionProducto')==='INTERNET SMALL BUSINESS'
                    || rec.get('descripcionProducto')==='TELCOHOME')
                    && rec.get('ldap') === "SI" && rec.get('estado') === "Activo") 
                {
                    var permiso = $("#ROLE_151-2458");
                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                    if (!boolPermiso) {
                        return 'button-grid-invisible';
                    }
                    else
                    {
                        return 'button-grid-crearClienteLdap';
                    }
                }
            }
            if(rec.get('flujo')=='TNP' && 
               rec.get('descripcionProducto') === 'INTERNET' && 
               !Ext.isEmpty(rec.get('nombrePlan')) &&
               rec.get('ldap') === "SI" && 
               rec.get('estado') === "Activo")
            {
                var permisoTnp = $("#ROLE_151-2458");
                var boolPermisoTnp = (typeof permisoTnp === 'undefined') ? false : (permisoTnp.val() == 1 ? true : false);
                if (!boolPermisoTnp) {
                    return 'button-grid-invisible';
                }
                else
                    return 'button-grid-crearClienteLdap';
            }
        },
        tooltip: 'Crear cliente en LDAP',
        handler: function(grid, rowIndex, colIndex) {
            crearLdapServicio(grid.getStore().getAt(rowIndex).data);
        }
    },
    //RECONFIGURAR LDAP
    {
        getClass: function(v, meta, rec) {
            if (rec.get('intServicioFTTxTN')!== null) {
                return 'button-grid-invisible';
            }
            if(rec.get('flujo')=='MD')
            {
                if ((rec.get('descripcionProducto') === 'INTERNET' || rec.get('descripcionProducto')==='INTERNET SMALL BUSINESS'
                    || rec.get('descripcionProducto')==='TELCOHOME') 
                    && rec.get('ldap') === "SI" && rec.get('estado') === "Activo")
                {
                    var permiso = $("#ROLE_151-2421");
                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                    if (!boolPermiso) {
                        return 'button-grid-invisible';
                    }
                    else
                        return 'button-grid-reconfigurarLdap';
                }
            }
            if(rec.get('flujo')=='TNP' &&
               rec.get('descripcionProducto') === 'INTERNET' && 
               !Ext.isEmpty(rec.get('nombrePlan')) &&
               rec.get('ldap') === "SI" && 
               rec.get('estado') === "Activo")
            {
                var permisoTnp = $("#ROLE_151-2421");
                var boolPermisoTnp = (typeof permisoTnp === 'undefined') ? false : (permisoTnp.val() == 1 ? true : false);
                if (!boolPermisoTnp) {
                    return 'button-grid-invisible';
                }
                else
                {
                    return 'button-grid-reconfigurarLdap';
                }
            }
        },
            tooltip: 'Reconfigurar LDAP',
            handler: function(grid, rowIndex, colIndex) {
                reconfigurarLdapServicio(grid.getStore().getAt(rowIndex).data);
        }
    },
    //ELIMINAR CLIENTE LDAP
    {
        getClass: function(v, meta, rec) {
            if(rec.get('flujo')=='MD')
            {
                if ((rec.get('descripcionProducto') === 'INTERNET' || rec.get('descripcionProducto')==='INTERNET SMALL BUSINESS'
                    || rec.get('descripcionProducto')==='TELCOHOME')
                    && rec.get('modeloElemento') === "MA5608T" && rec.get('estado') === "Cancel") 
                {
                    var permiso = $("#ROLE_151-3277");
                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                    if (!boolPermiso) {
                        return 'button-grid-invisible';
                    }
                    else
                        return 'button-grid-delete';
                }
            }
            if(rec.get('flujo')=='TNP' &&
               rec.get('descripcionProducto') === 'INTERNET' && 
               !Ext.isEmpty(rec.get('nombrePlan')) &&
               rec.get('modeloElemento') === "MA5608T" &&
               rec.get('estado') === "Cancel")
            {
                var permisoTnp = $("#ROLE_151-3277");
                var boolPermisoTnp = (typeof permisoTnp === 'undefined') ? false : (permisoTnp.val() == 1 ? true : false);
                if (!boolPermisoTnp) {
                    return 'button-grid-invisible';
                }
                else
                {
                    return 'button-grid-delete';
                }
            }
        },
            tooltip: 'Eliminar LDAP Cliente',
            handler: function(grid, rowIndex, colIndex) {
                eliminarLdapServicio(grid.getStore().getAt(rowIndex).data);
        }
    },
    //VER PDF
    {
        getClass: function(v, meta, rec) {
            if(rec.get('descripcionProducto')=='INTERNET' && rec.get('flujo') == "TTCO"){
                var permiso = $("#ROLE_151-834");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                //alert(typeof permiso);
                if(!boolPermiso){ 
                    return 'button-grid-invisible';
                }
                else{
                    if (rec.get('estado') == "Activo" || rec.get('estado') == "EnPruebas" || rec.get('estado') == "Cancel" || rec.get('estado') == "Cancel-SinEje") {
                            return 'button-grid-pdf';
                    }
                    else{
                        return 'button-grid-invisible';
                    }
                }
            }
        },
        tooltip: 'Descargar Info Tecnica',
        handler: function(grid, rowIndex, colIndex) {
                if(grid.getStore().getAt(rowIndex).data.estado=="Activo" ||  grid.getStore().getAt(rowIndex).data.estado=="EnPruebas" || grid.getStore().getAt(rowIndex).data.estado=="Cancel" || grid.getStore().getAt(rowIndex).data.estado=="Cancel-SinEje"){
                    window.open("/tecnico/clientes/getDatosTecnicosPdf?idServicio="+grid.getStore().getAt(rowIndex).data.idServicio+"&cliente="+grid.getStore().getAt(rowIndex).data.nombreCompleto);
                }
        }
    },
    //FACTURACION CANCELACION VOLUNTARIA
    {
        getClass: function(v, meta, rec) {
            if((rec.get('prefijoEmpresa')==='MD' || rec.get('prefijoEmpresa')==='EN') && rec.get('descripcionProducto')==='INTERNET'){

                if (boolCancelacionVoluntaria && (rec.get('estado') === "Activo"|| rec.get('estado') === "In-Corte" 
                                              ||  rec.get('estado') === "In-Corte-SinEje" || rec.get('estado') === "Pre-cancelado"
                                              ||  rec.get('estado') === "In-Temp" || rec.get('estado') === "In-Temp-SinEje")) 
                {
                    return 'button-grid-Fact-Anticipada';
                }
                else
                {
                    return 'button-grid-invisible';
                }
                
            }
        },
        tooltip: 'Cancelaci\u00f3n Voluntaria',
        handler: function(grid, rowIndex) {
            cancelacionAnticipada(grid.getStore().getAt(rowIndex).data);
        }        
    },    
    //GENERA ACTA DE RECEPCION - TN
    {
        getClass: function(v, meta, rec) 
        {
            if (rec.get('prefijoEmpresa') == 'TN')
            {
                var permiso = $("#ROLE_151-3837");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);  
                if(!boolPermiso)
                { 
                    return 'button-grid-invisible';
                }
                else
                {
                    if(rec.get('estado') === "Activo") 
                    {
                        if(rec.get('grupo').includes('DATACENTER') 
                            || rec.get('descripcionProducto') === 'IPSB')
                        {
                            return 'button-grid-invisible';
                        }
                        else
                        {
                            if(rec.get('tieneProgresoActa') === 'NO'){
                                return 'button-grid-pdf';
                            }else{
                                return 'button-grid-invisible';
                            }
                        }
                    }
                    else
                    {
                        return 'button-grid-invisible';
                    }
                }
            }
        },
        tooltip: 'Generar Acta de Entrega-Recepción',
        handler: function(grid, rowIndex, colIndex) 
        {
            if (grid.getStore().getAt(rowIndex).data.prefijoEmpresa == 'TN')
            {
                if((grid.getStore().getAt(rowIndex).data.estado=="Activo") && grid.getStore().getAt(rowIndex).data.descripcionProducto !== "IPSB")
                  {
                      Ext.MessageBox.wait("Abriendo acta...");
                      window.location = "../../tecnico/clientes/" + grid.getStore().getAt(rowIndex).data.idServicio + "/acta";
                  }
            }
        }
    },
    
    
    /**********************************************************
     * 	
     *  @author:	arsuarez@telconet.ec
     *  @version:   V:14/08/2014
     *  @descripcion: Iconos de subida de Acta de recepcion
     *                y encuesta en caso de no haber sido creada			                
     * 
     ***********************************************************/	
    //SUBIDA DE ACTA DE RECEPCION
    {
        getClass: function(v, meta, rec)
        {
            var permiso = $("#ROLE_151-1657");

            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

            if (!boolPermiso)
            {
                return 'button-grid-invisible';
            }
            else
            {
                if (rec.get('prefijoEmpresa') == 'MD')
                {
                    if (rec.get('descripcionProducto') == 'INTERNET')
                    {
                        if((rec.get('estado') == "EnPruebas" && rec.get('tieneActa') == "FALSE" )|| 
                          (rec.get('estado') == "Activo" && rec.get('tieneEncuesta') == "FALSE" && rec.get('tieneActa') == "FALSE" ))
                        {
                            return 'button-grid-upload';
                        }                                            
                        else {
                            return 'button-grid-invisible';
                        }
                    }
                }
                else if (rec.get('prefijoEmpresa') == 'TN')
                {
                    if(((rec.get('estado') == "EnPruebas"      && rec.get('tieneActa') == "FALSE" )|| 
                       (rec.get('estado') == "Activo"         && rec.get('tieneActa') == "FALSE" ))
                       && ( rec.get('descripcionProducto') !== 'INTERNET SMALL BUSINESS'
                            && rec.get('descripcionProducto') !== 'IPSB'
                            && rec.get('descripcionProducto') !== 'TELCOHOME'
                            && rec.get('descripcionProducto') !== 'NETWIFI' && rec.get('boolSecureCpe') !== 'S') 
                            && (rec.get('esServicioCamaraSafeCity') !== 'S'
                            && consultarLoginCam(rec.get('idPersonaEmpresaRol'),rec.get('idProducto')) == false)
                      )
                    {
                        if(rec.get('grupo').includes('DATACENTER'))
                        {
                            return 'button-grid-invisible';
                        }
                        else if(rec.get('nombreProducto') == 'CLEAR CHANNEL PUNTO A PUNTO'
                           &&  rec.get('aprovisioClearChannel') != 'NO')
                        {
                            return 'button-grid-invisible';
                        }
                        else if ((rec.get('nombreProducto') == rec.get('strValorProductoPaqHoras') )
                        || (rec.get('nombreProducto') == rec.get('strValorProductoPaqHorasRec') ) )
                        {
                            return 'button-grid-invisible';    
                        }
                        else
                        {
                            return 'button-grid-upload';
                        }
                    }                                            
                    else 
                    {
                        return 'button-grid-invisible';
                    }
                    
                }                
            }
        },
        tooltip: 'Subir Acta Recepcion',
        handler: function(grid, rowIndex, colIndex) {
            if (grid.getStore().getAt(rowIndex).data.prefijoEmpresa == "MD" || grid.getStore().getAt(rowIndex).data.prefijoEmpresa == "EN")
            {
                if (grid.getStore().getAt(rowIndex).data.estado == "EnPruebas" || grid.getStore().getAt(rowIndex).data.estado == "Activo" )
                {
                    subirActaRecepcion(grid.getStore().getAt(rowIndex).data);
                }
            }
            else if (grid.getStore().getAt(rowIndex).data.prefijoEmpresa == "TN")
            {
                if (((grid.getStore().getAt(rowIndex).data.estado == "EnPruebas" && grid.getStore().getAt(rowIndex).data.tieneActa== "FALSE") || 
                    (grid.getStore().getAt(rowIndex).data.estado == "Activo" && grid.getStore().getAt(rowIndex).data.tieneActa == "FALSE" ))
                    && (grid.getStore().getAt(rowIndex).data.descripcionProducto !== "INTERNET SMALL BUSINESS"
                        && grid.getStore().getAt(rowIndex).data.descripcionProducto !== "IPSB"
                        && grid.getStore().getAt(rowIndex).data.descripcionProducto !== "TELCOHOME"
                        && grid.getStore().getAt(rowIndex).data.descripcionProducto !== 'NETWIFI')
                   )
                {
                    subirActaRecepcionTn(grid.getStore().getAt(rowIndex).data);
                }
            }            
        }
    },
    //REALIZAR ENCUESTA
    {
        getClass: function(v, meta, rec)
        {
            var permiso = $("#ROLE_151-1657");

            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

            if (!boolPermiso)
            {
                return 'button-grid-invisible';
            }
            else
            {
                if (rec.get('prefijoEmpresa') == 'MD' || rec.get('prefijoEmpresa') == 'EN')
                {
                    if (rec.get('descripcionProducto') == 'INTERNET')
                    {
                        if ((rec.get('estado') == "EnPruebas" && rec.get('tieneEncuesta') == "FALSE" && rec.get('tieneActa') == "TRUE" )|| 
                            rec.get('estado') == "Activo" && rec.get('tieneEncuesta') == "FALSE" && rec.get('tieneActa') == "TRUE")
                        {
                            return 'button-grid-encuesta';
                        }
                        else {
                            return 'button-grid-invisible';
                        }

                    }
                }
            }
        },
        tooltip: 'LLenar Encuesta Servicio',
        handler: function(grid, rowIndex, colIndex) {
            if (grid.getStore().getAt(rowIndex).data.prefijoEmpresa == "MD")
            {
                if (grid.getStore().getAt(rowIndex).data.estado == "EnPruebas" || grid.getStore().getAt(rowIndex).data.estado == "Activo") {

                    Ext.MessageBox.wait("Abriendo Encuesta...");
                    window.location = "../../tecnico/clientes/" + grid.getStore().getAt(rowIndex).data.idServicio + "/encuesta";
                }
            }
        }
    },
    
    /*
     * ********************************************
     * FUNCIONES PARA LINEAS NETVOICE
     * ********************************************
     */    
    //ACTIVAR
    {
        getClass: function(v, meta, rec) {
            var permiso = $("#ROLE_415-6045");
            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
            if (!boolPermiso) {
                return 'button-grid-invisible';
            }
            else 
            {
                if ((rec.get('estado') == "Asignada" || rec.get('estado') == "AsignadoTarea") && 
                    (rec.get('descripcionProducto') == 'TELEFONIA_NETVOICE'))  
                {
                    if(rec.get('prefijoEmpresa') == 'MD')
                    {
                        return 'button-grid-telefonia';
                    }
                    else
                    {
                        return 'button-grid-informacionTecnica';
                    }
                }
                else
                {
                    return 'button-grid-invisible';
                }
            }
        },
        tooltip: 'Activar Línea Telefónica',
        handler: function (grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
            if ((grid.getStore().getAt(rowIndex).data.estado == "Asignada" || grid.getStore().getAt(rowIndex).data.estado == "AsignadoTarea" )&&
                grid.getStore().getAt(rowIndex).data.descripcionProducto == "TELEFONIA_NETVOICE")
            {

                if (grid.getStore().getAt(rowIndex).data.prefijoEmpresa == 'TN')
                {
                   
                    if (grid.getStore().getAt(rowIndex).data.categoriaTelefonia == 'FIJA ANALOGA')
                    {                        
                        activarLineasAnalogicas(grid.getStore().getAt(rowIndex).data);
                    } else
                    {
                        activarLineasTelefonicas(grid.getStore().getAt(rowIndex).data);
                    }
                } else
                {
                    activarLineasTelefonicas(grid.getStore().getAt(rowIndex).data);
                }
            }
        }
    },
    {
        getClass: function (v, meta, rec) {
            var permiso = $("#ROLE_415-6044");
            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
            if (!boolPermiso) {
                return 'button-grid-invisible';
            } else
            {
                if ( (rec.get('descripcionProducto') == 'TELEFONIA_NETVOICE' && rec.get('estado') == 'Activo' 
                      && rec.get('categoriaTelefonia') == 'FIJA ANALOGA' && rec.get('tieneSolicitudCambioCpe'))) 
                {
                    return 'button-grid-crearBackup';

                } else
                {
                    return 'button-grid-invisible';
                }
            }
        },
        tooltip: 'Cambiar Equipo',
        handler: function (grid, rowIndex) {

            if (
                grid.getStore().getAt(rowIndex).data.descripcionProducto == "TELEFONIA_NETVOICE" &&
                grid.getStore().getAt(rowIndex).data.estado == "Activo" && 
                grid.getStore().getAt(rowIndex).data.tieneSolicitudCambioCpe )
            {                
                cambioElementoTelefonia(grid.getStore().getAt(rowIndex).data);          
            }
        }
    },    

    {
        getClass: function (v, meta, rec) {
            var permiso = $("#ROLE_374-5097");
            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
            //alert(typeof permiso);
            if (!boolPermiso) {
                return 'button-grid-invisible';
            } else
            {
                if ((rec.get('estado') == "Activo" || rec.get('estado') == "In-Corte") && rec.get('descripcionProducto') == 'TELEFONIA_NETVOICE' && rec.get('prefijoEmpresa') == 'TN') {
                    return 'button-grid-telefonia';
                } else
                {
                    return 'button-grid-invisible';
                }
            }
        },
        tooltip: 'Ver Líneas Telefónica',
        handler: function (grid, rowIndex) {
            
            if (grid.getStore().getAt(rowIndex).data.descripcionProducto == "TELEFONIA_NETVOICE")
            {
                consultarTelefonia(grid.getStore().getAt(rowIndex).data.idServicio, grid.getStore().getAt(rowIndex).data.estado);
            }
        }
    },
    
    /*
     * ********************************************
     * FUNCIONES PARA CONEXION CON ELEMENTOS
     * ********************************************
     */
    //VER SUBSCRIPTORES
    {
        getClass: function(v, meta, rec) {
            if (rec.get('estado') == "Asignada") {
                if(rec.get('modeloElemento')=="EP-3116"){
                    return 'button-grid-verContadores';
                }
            }
            else{
                return 'button-grid-invisible';
            }
        },
        tooltip: 'Ver Subscriptores',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
            if(grid.getStore().getAt(rowIndex).data.estado=="Asignada"){
                if(modelo=="EP-3116"){
                    verSubscribers(grid.getStore().getAt(rowIndex).data, "mostrarSubscriberOlt");
                }
                else{
                    alert("No existe Accion para este Elemento");
                }
            }
        }
    },
    //VER SUBSCRIPTORES CONECTADOS
    {
        getClass: function(v, meta, rec) {
            if ((rec.get('estado') == "EnVerificacion" || rec.get('estado') == "EnPruebas" || rec.get('estado') == "Activo" 
                || rec.get('estado') == "In-Corte") 
                && (rec.get('descripcionProducto') !== 'INTERNET SMALL BUSINESS' && rec.get('descripcionProducto') !== 'TELCOHOME')) {
                if(rec.get('modeloElemento')=="EP-3116"){
                    return 'button-grid-verInterfacesPorPuerto';
                }
            }
            else{
                return 'button-grid-invisible';
            }
        },
        tooltip: 'Ver Subscriptores Conectados',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
            if((grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion" || grid.getStore().getAt(rowIndex).data.estado=="EnPruebas" ||
               grid.getStore().getAt(rowIndex).data.estado=="Activo" || grid.getStore().getAt(rowIndex).data.estado=="In-Corte")
               && (grid.getStore().getAt(rowIndex).data.descripcionProducto !== "INTERNET SMALL BUSINESS"
                    && grid.getStore().getAt(rowIndex).data.descripcionProducto !== "TELCOHOME")){
                if(modelo=="EP-3116"){
                    verSubscribersConectados(grid.getStore().getAt(rowIndex).data, "mostrarSubscriberConectadosOlt");
                }
                else{
                    alert("No existe Accion para este Elemento");
                }
            }
        }
    },
    //VERIFICAR SERVICIOS
    {
        getClass: function(v, meta, rec) {
            if ((rec.get('estado') == "EnVerificacion" || rec.get('estado') == "EnPruebas" 
                || rec.get('estado') == "Activo" || rec.get('estado') == "In-Corte")
                && (rec.get('descripcionProducto') !== 'INTERNET SMALL BUSINESS' && rec.get('descripcionProducto') !== 'TELCOHOME')) {
                if(rec.get('modeloElemento')=="EP-3116"){
                    return 'button-grid-verVelocidadReal';
                }
            }
            else{
                return 'button-grid-invisible';
            }
        },
        tooltip: 'Verificar Servicio',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
            if((grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion" || grid.getStore().getAt(rowIndex).data.estado=="EnPruebas" ||
               grid.getStore().getAt(rowIndex).data.estado=="Activo" || grid.getStore().getAt(rowIndex).data.estado=="In-Corte")
               && (grid.getStore().getAt(rowIndex).data.descripcionProducto !== "INTERNET SMALL BUSINESS"
                    && grid.getStore().getAt(rowIndex).data.descripcionProducto !== "TELCOHOME")){
                if(modelo=="EP-3116"){
                    verificarServicios(grid.getStore().getAt(rowIndex).data, "verificarServicioOlt");
                }
                else{
                    alert("No existe Accion para este Elemento");
                }
            }
        }
    },
    //VER MACS CONECTADOS
    {
        getClass: function(v, meta, rec) {
            if ((rec.get('estado') == "EnVerificacion" || rec.get('estado') == "EnPruebas" || 
                rec.get('estado') == "Activo" || rec.get('estado') == "In-Corte")
                && (rec.get('descripcionProducto') !== 'INTERNET SMALL BUSINESS' && rec.get('descripcionProducto') !== 'TELCOHOME')) {
                if(rec.get('modeloElemento')=="EP-3116"){
                    return 'button-grid-verMacPuerto';
                }
            }
            else{
                return 'button-grid-invisible';
            }
        },
        tooltip: 'Ver Macs Conectadas del Cliente',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
            if((grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion" || 
               grid.getStore().getAt(rowIndex).data.estado=="EnPruebas" ||
               grid.getStore().getAt(rowIndex).data.estado=="Activo" || 
               grid.getStore().getAt(rowIndex).data.estado=="In-Corte")
               && (grid.getStore().getAt(rowIndex).data.descripcionProducto !== "INTERNET SMALL BUSINESS"
                    && grid.getStore().getAt(rowIndex).data.descripcionProducto !== "TELCOHOME")){
                if(modelo=="EP-3116"){
                    verMacsConectadas(grid.getStore().getAt(rowIndex).data, "mostrarMacsConectadasOlt");
                }
                else{
                    alert("No existe Accion para este Elemento");
                }
            }
        }
    },			
    //VER TABLA DE MACS POR PUERTO EN EL OLT
    {
        getClass: function(v, meta, rec) {
            if (rec.get('estado') == "Asignada") {
                if(rec.get('modeloElemento')=="EP-3116"){
                    return 'button-grid-verMacPuerto';
                }
            }
            else{
                return 'button-grid-invisible';
            }
        },
        tooltip: 'Ver Tabla de Macs Conectadas',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
            if(grid.getStore().getAt(rowIndex).data.estado=="Asignada"){
                if(modelo=="EP-3116"){
                    verScriptVariableOlt(grid.getStore().getAt(rowIndex).data, "mostrarTablaMacsConectadasOlt");
                }
                else{
                    alert("No existe Accion para este Elemento");
                }
            }
        }
    },	
    {
        getClass: function(v, meta, rec) {
            if (rec.get('estado') == "Activo" || rec.get('estado') == "EnVerificacion") {
                if(rec.get('modeloElemento')=="A2024" || rec.get('modeloElemento')=="A2048"){
                    return 'button-grid-verInterfacesPorPuerto';
                }
                if(rec.get('modeloElemento')=="R1AD24A" || rec.get('modeloElemento')=="R1AD48A"){
                    return 'button-grid-verInterfacesPorPuerto';
                }
                if(rec.get('modeloElemento')=="7224"){
                    return 'button-grid-verInterfacesPorPuerto';
                }
            }
            else{
                return 'button-grid-invisible';
            }
        },
        tooltip: 'Ver VCI y Velocidad',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
            if(grid.getStore().getAt(rowIndex).data.estado=="Activo"  || grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion"){
                if(modelo=="A2024" || modelo=="A2048"){
                    verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarConfiguracionInterfaceDslamA2024");
                }
                else if(modelo=="R1AD24A" || modelo=="R1AD48A"){
                    verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarConfiguracionInterfaceDslamR1");
                }
                else if(modelo=="7224"){
                    verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarConfiguracionInterfaceDslam7224");
                }
                else{
                    alert("No existe Accion para este Dslam");
                }
            }
        }
    },
    {
        getClass: function(v, meta, rec) {
            if (rec.get('estado') == "Activo" || rec.get('estado') == "EnVerificacion") {
                if(rec.get('modeloElemento')=="6524"){
                    return 'button-grid-verNombrePuerto';
                }
            }
            else{
                return 'button-grid-invisible';
            }

        },
        tooltip: 'Ver Nombre Puerto',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                if(grid.getStore().getAt(rowIndex).data.estado=="Activo"  || grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion"){
                    if(modelo=="6524"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarNombrePuertoDslam6524");
                    }
                    else{
                        alert("No existe Accion para este Dslam");
                    }
                }
            }
    },
    {
        getClass: function(v, meta, rec) {
            if (rec.get('estado') == "Activo" || rec.get('estado') == "EnVerificacion") {
                if(rec.get('modeloElemento')=="A2024" || rec.get('modeloElemento')=="A2048"){
                    return 'button-grid-verVelocidadReal';
                }
                if(rec.get('modeloElemento')=="R1AD24A" || rec.get('modeloElemento')=="R1AD48A"){
                    return 'button-grid-verVelocidadReal';
                }
                if(rec.get('modeloElemento')=="6524"){
                    return 'button-grid-verVelocidadReal';
                }
            }
            else{
                return 'button-grid-invisible';
            }

        },
        tooltip: 'Ver Velocidad Real',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                if(grid.getStore().getAt(rowIndex).data.estado=="Activo"  || grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion"){
                    if(modelo=="A2024" || modelo=="A2048"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarVelocidadRealDslamA2024");
                    }
                    else if(modelo=="6524"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarVelocidadRealDslam6524");
                    }
                    else if(modelo=="R1AD24A" || modelo=="R1AD48A"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarVelocidadRealDslamR1");
                    }
                    else{
                        alert("No existe Accion para este Dslam");
                    }
                }
            }
    },
    {
        getClass: function(v, meta, rec) {
            if (rec.get('estado') == "Activo" || rec.get('estado') == "EnVerificacion") {
                if(rec.get('modeloElemento')=="A2024" || rec.get('modeloElemento')=="A2048"){
                    return 'button-grid-verSenalLejos';
                }
            }
            else{
                return 'button-grid-invisible';
            }

        },
        tooltip: 'Ver Niveles de Señal Extremo Lejano',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                if(grid.getStore().getAt(rowIndex).data.estado=="Activo"  || grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion"){
                    if(modelo=="A2024" || modelo=="A2048"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarNivelesSenalExtremoLejanoDslamA2024");
                    }
                    else{
                        alert("No existe Accion para este Dslam");
                    }
                }
            }
    },
    {
        getClass: function(v, meta, rec) {
            if (rec.get('estado') == "Activo" || rec.get('estado') == "EnVerificacion") {
                if(rec.get('modeloElemento')=="A2024" || rec.get('modeloElemento')=="A2048"){
                    return 'button-grid-verSenalCerca';
                }
            }
            else{
                return 'button-grid-invisible';
            }

        },
        tooltip: 'Ver Niveles de Señal Extremo Cercano',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                if(grid.getStore().getAt(rowIndex).data.estado=="Activo"  || grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion"){
                    if(modelo=="A2024" || modelo=="A2048"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarNivelesSenalExtremoCercanoDslamA2024");
                    }
                    else{
                        alert("No existe Accion para este Dslam");
                    }
                }
            }
    },
    {
        getClass: function(v, meta, rec) {
            if (rec.get('estado') == "Activo" || rec.get('estado') == "EnVerificacion") {
                if(rec.get('modeloElemento')=="A2024" || rec.get('modeloElemento')=="A2048"){
                    return 'button-grid-verConfiguracionBridge';
                }
            }
            else{
                return 'button-grid-invisible';
            }

        },
        tooltip: 'Ver Configuracion Bridge',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                if(grid.getStore().getAt(rowIndex).data.estado=="Activo"  || grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion"){
                    if(modelo=="A2024" || modelo=="A2048"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarConfiguracionBridgeDslamA2024");
                    }
                    else{
                        alert("No existe Accion para este Dslam");
                    }
                }
            }
    },
    {
        getClass: function(v, meta, rec) {
            if (rec.get('estado') == "Activo" || rec.get('estado') == "EnVerificacion") {
                if(rec.get('modeloElemento')=="A2024" || rec.get('modeloElemento')=="A2048"){
                    return 'button-grid-verCircuitoVirtual';
                }
            }
            else{
                return 'button-grid-invisible';
            }

        },
        tooltip: 'Ver Circuito Virtual',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                if(grid.getStore().getAt(rowIndex).data.estado=="Activo"  || grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion"){
                    if(modelo=="A2024" || modelo=="A2048"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarCircuitoVirtualDslamA2024");
                    }
                    else{
                        alert("No existe Accion para este Dslam");
                    }
                }
            }
    },
    {
        getClass: function(v, meta, rec) {
            if (rec.get('estado') == "Activo" || rec.get('estado') == "EnVerificacion") {
                if(rec.get('modeloElemento')=="A2024" || rec.get('modeloElemento')=="A2048"){
                    return 'button-grid-verContadores';
                }
            }
            else{
                return 'button-grid-invisible';
            }

        },
        tooltip: 'Ver Contadores',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                if(grid.getStore().getAt(rowIndex).data.estado=="Activo"  || grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion"){
                    if(modelo=="A2024" || modelo=="A2048"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarContadoresDslamA2024");
                    }
                    else{
                        alert("No existe Accion para este Dslam");
                    }
                }
            }
    },
    {
        getClass: function(v, meta, rec) {
            if (rec.get('estado') == "Activo" || rec.get('estado') == "EnVerificacion") {
                if(rec.get('modeloElemento')=="A2024" || rec.get('modeloElemento')=="A2048"){
                    return 'button-grid-verRendimientoPuertoDiario';
                }
                if(rec.get('modeloElemento')=="MEA1" || rec.get('modeloElemento')=="MEA3"){
                    return 'button-grid-verRendimientoPuertoDiario';
                }
            }
            else{
                return 'button-grid-invisible';
            }

        },
        tooltip: 'Ver Desempeño Puerto - Diario',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                if(grid.getStore().getAt(rowIndex).data.estado=="Activo"  || grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion"){
                    if(modelo=="A2024" || modelo=="A2048"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarDesempenoPuertoDiarioDslamA2024");
                    }
                    else if(modelo=="MEA1" || modelo=="MEA3"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarDesempenoPuertoDiarioDslamMea");
                    }
                    else{
                        alert("No existe Accion para este Dslam");
                    }
                }
            }
    },
    {
        getClass: function(v, meta, rec) {
            if (rec.get('estado') == "Activo" || rec.get('estado') == "EnVerificacion") {
                if(rec.get('modeloElemento')=="A2024" || rec.get('modeloElemento')=="A2048"){
                    return 'button-grid-verRendimientoPuertoIntervalo';
                }
                if(rec.get('modeloElemento')=="MEA1" || rec.get('modeloElemento')=="MEA3"){
                    return 'button-grid-verRendimientoPuertoIntervalo';
                }
            }
            else{
                return 'button-grid-invisible';
            }

        },
        tooltip: 'Ver Desempeño Puerto - Intervalo',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                if(grid.getStore().getAt(rowIndex).data.estado=="Activo"  || grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion"){
                    if(modelo=="A2024" || modelo=="A2048"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarDesempenoPuertoIntervaloDslamA2024");
                    }
                    else if(modelo=="MEA1" || modelo=="MEA3"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarDesempenoPuertoIntervaloDslamMea");
                    }
                    else{
                        alert("No existe Accion para este Dslam");
                    }
                }
            }
    },
    {
        getClass: function(v, meta, rec) {
            if (rec.get('estado') == "Activo" || rec.get('estado') == "EnVerificacion") {
                if(rec.get('modeloElemento')=="7224"){
                    return 'button-grid-verMacPuerto';
                }
                if(rec.get('modeloElemento')=="6524"){
                    return 'button-grid-verMacPuerto';
                }
                if(rec.get('modeloElemento')=="R1AD24A" || rec.get('modeloElemento')=="R1AD48A"){
                    return 'button-grid-verMacPuerto';
                }
                if(rec.get('modeloElemento')=="MEA1" || rec.get('modeloElemento')=="MEA3"){
                    return 'button-grid-verMacPuerto';
                }
            }
            else{
                return 'button-grid-invisible';
            }

        },
        tooltip: 'Ver Macs del Puerto',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                if(grid.getStore().getAt(rowIndex).data.estado=="Activo"  || grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion"){
                    if(modelo=="7224"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarMacsPuertoDslam7224");
                    }
                    else if(modelo=="6524"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarMacsPuertoDslam6524");
                    }
                    else if(modelo=="R1AD24A" || modelo=="R1AD48A"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarMacsPuertosDslamR1");
                    }
                    else if(modelo=="MEA1" || modelo=="MEA3"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarMacPuertoDslamMea");
                    }
                    else{
                        alert("No existe Accion para este Dslam");
                    }
                }
            }
    },
    {
        getClass: function(v, meta, rec) {
            if (rec.get('estado') == "Activo" || rec.get('estado') == "EnVerificacion") {
                if(rec.get('modeloElemento')=="7224"){
                    return 'button-grid-verVelocidadSeteada';
                }
                if(rec.get('modeloElemento')=="6524"){
                    return 'button-grid-verVelocidadSeteada';
                }
            }
            else{
                return 'button-grid-invisible';
            }

        },
        tooltip: 'Ver Velocidad Seteada',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                if(grid.getStore().getAt(rowIndex).data.estado=="Activo"  || grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion"){
                    if(modelo=="7224"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarVelocidadSeteadaDslam7224");
                    }
                    else if(modelo=="6524"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarVelocidadSeteadaDslam6524");
                    }
                    else{
                        alert("No existe Accion para este Dslam");
                    }
                }
            }
    },
    {
        getClass: function(v, meta, rec) {
            if (rec.get('estado') == "Activo" || rec.get('estado') == "EnVerificacion") {
                if(rec.get('modeloElemento')=="7224"){
                    return 'button-grid-verMonitorearPuerto';
                }
                if(rec.get('modeloElemento')=="6524"){
                    return 'button-grid-verMonitorearPuerto';
                }
            }
            else{
                return 'button-grid-invisible';
            }

        },
        tooltip: 'Monitorear Puerto',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                if(grid.getStore().getAt(rowIndex).data.estado=="Activo"  || grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion"){
                    if(modelo=="7224"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarMonitorearPuertoDslam7224");
                    }
                    else if(modelo=="6524"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarMonitorearPuertoDslam6524");
                    }
                    else{
                        alert("No existe Accion para este Dslam");
                    }
                }
            }
    },
    {
        getClass: function(v, meta, rec) {
            if (rec.get('estado') == "Activo" || rec.get('estado') == "EnVerificacion") {
                if(rec.get('modeloElemento')=="7224"){
                    return 'button-grid-verMacPuerto';
                }
            }
            else{
                return 'button-grid-invisible';
            }

        },
        tooltip: 'Ver Parametros Linea',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                if(grid.getStore().getAt(rowIndex).data.estado=="Activo"  || grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion"){
                    if(modelo=="7224"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarParametrosLineaDslam7224");
                    }
                    else{
                        alert("No existe Accion para este Dslam");
                    }
                }
            }
    },
    {
        getClass: function(v, meta, rec) {
            if (rec.get('estado') == "Activo" || rec.get('estado') == "EnVerificacion") {
                if(rec.get('modeloElemento')=="7224"){
                    return 'button-grid-verMacPuerto';
                }
            }
            else{
                return 'button-grid-invisible';
            }

        },
        tooltip: 'Ver Codificacion Linea',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                if(grid.getStore().getAt(rowIndex).data.estado=="Activo"  || grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion"){
                    if(modelo=="7224"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarCodificacionLineaDslam7224");
                    }
                    else{
                        alert("No existe Accion para este Dslam");
                    }
                }
            }
    },
    {
        getClass: function(v, meta, rec) {
            if (rec.get('estado') == "Activo" || rec.get('estado') == "EnVerificacion") {
                if(rec.get('modeloElemento')=="6524"){
                    return 'button-grid-verMacPuerto';
                }
            }
            else{
                return 'button-grid-invisible';
            }

        },
        tooltip: 'Ver Crc',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                if(grid.getStore().getAt(rowIndex).data.estado=="Activo"  || grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion"){
                    if(modelo=="6524"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarCrcDslam6524");
                    }
                    else{
                        alert("No existe Accion para este Dslam");
                    }
                }
            }
    },
    {
        getClass: function(v, meta, rec) {
            if (rec.get('estado') == "Activo" || rec.get('estado') == "EnVerificacion") {
                if(rec.get('modeloElemento')=="6524"){
                    return 'button-grid-verMacPuerto';
                }
            }
            else{
                return 'button-grid-invisible';
            }

        },
        tooltip: 'Ver Senal Ruido',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                if(grid.getStore().getAt(rowIndex).data.estado=="Activo"  || grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion"){
                    if(modelo=="6524"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarSenalRuidoDslam6524");
                    }
                    else{
                        alert("No existe Accion para este Dslam");
                    }
                }
            }
    },
    {
        getClass: function(v, meta, rec) {
            if (rec.get('estado') == "Activo" || rec.get('estado') == "EnVerificacion") {
                if(rec.get('modeloElemento')=="6524"){
                    return 'button-grid-verMacPuerto';
                }
            }
            else{
                return 'button-grid-invisible';
            }

        },
        tooltip: 'Ver Atenuacion',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                if(grid.getStore().getAt(rowIndex).data.estado=="Activo"  || grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion"){
                    if(modelo=="6524"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarAtenuacionDslam6524");
                    }
                    else{
                        alert("No existe Accion para este Dslam");
                    }
                }
            }
    },
    {
        getClass: function(v, meta, rec) {
            if (rec.get('estado') == "Activo" || rec.get('estado') == "EnVerificacion") {
                if(rec.get('modeloElemento')=="6524"){
                    return 'button-grid-verMacPuerto';
                }
            }
            else{
                return 'button-grid-invisible';
            }

        },
        tooltip: 'Ver Codificacion',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                if(grid.getStore().getAt(rowIndex).data.estado=="Activo"  || grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion"){
                    if(modelo=="6524"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarCodificacionDslam6524");
                    }
                    else{
                        alert("No existe Accion para este Dslam");
                    }
                }
            }
    },
    {
        getClass: function(v, meta, rec) {
            if (rec.get('estado') == "Activo" || rec.get('estado') == "EnVerificacion") {
                if(rec.get('modeloElemento')=="6524"){
                    return 'button-grid-verMacPuerto';
                }
            }
            else{
                return 'button-grid-invisible';
            }

        },
        tooltip: 'Ver Restriccion Ip',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                if(grid.getStore().getAt(rowIndex).data.estado=="Activo"  || grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion"){
                    if(modelo=="6524"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarRestriccionIpDslam6524");
                    }
                    else{
                        alert("No existe Accion para este Dslam");
                    }
                }
            }
    },
    {
        getClass: function(v, meta, rec) {
            if (rec.get('estado') == "Activo" || rec.get('estado') == "EnVerificacion") {
                if(rec.get('modeloElemento')=="R1AD24A" || rec.get('modeloElemento')=="R1AD48A"){
                    return 'button-grid-verMacPuerto';
                }
            }
            else{
                return 'button-grid-invisible';
            }

        },
        tooltip: 'Monitorear Puerto Data I',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                if(grid.getStore().getAt(rowIndex).data.estado=="Activo"  || grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion"){
                    if(modelo=="R1AD24A" || modelo=="R1AD48A"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarMonitoreoPuertoDataIDslamR1");
                    }
                    else{
                        alert("No existe Accion para este Dslam");
                    }
                }
            }
    },
    {
        getClass: function(v, meta, rec) {
            if (rec.get('estado') == "Activo" || rec.get('estado') == "EnVerificacion") {
                if(rec.get('modeloElemento')=="R1AD24A" || rec.get('modeloElemento')=="R1AD48A"){
                    return 'button-grid-verMacPuerto';
                }
            }
            else{
                return 'button-grid-invisible';
            }

        },
        tooltip: 'Monitorear Puerto Data II',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                if(grid.getStore().getAt(rowIndex).data.estado=="Activo"  || grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion"){
                    if(modelo=="R1AD24A" || modelo=="R1AD48A"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarMonitoreoPuertoDataIIDslamR1");
                    }
                    else{
                        alert("No existe Accion para este Dslam");
                    }
                }
            }
    },
    {
        getClass: function(v, meta, rec) {
            if (rec.get('estado') == "Activo" || rec.get('estado') == "EnVerificacion") {
                if(rec.get('modeloElemento')=="R1AD24A" || rec.get('modeloElemento')=="R1AD48A"){
                    return 'button-grid-verMacPuerto';
                }
                else if(rec.get('modeloElemento')=="MEA1" || rec.get('modeloElemento')=="MEA3"){
                    return 'button-grid-verMacPuerto';
                }
            }
            else{
                return 'button-grid-invisible';
            }

        },
        tooltip: 'Ver Status Puerto',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                if(grid.getStore().getAt(rowIndex).data.estado=="Activo"  || grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion"){
                    if(modelo=="R1AD24A" || modelo=="R1AD48A"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarStatusPuertoDslamR1");
                    }
                    else if(modelo=="MEA1" || modelo=="MEA3"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarEstadoPuertoDslamMea");
                    }
                    else{
                        alert("No existe Accion para este Dslam");
                    }
                }
            }
    },
    {
        getClass: function(v, meta, rec) {
            if (rec.get('estado') == "Activo" || rec.get('estado') == "EnVerificacion") {
                if(rec.get('modeloElemento')=="MEA1" || rec.get('modeloElemento')=="MEA3"){
                    return 'button-grid-verMacPuerto';
                }
            }
            else{
                return 'button-grid-invisible';
            }

        },
        tooltip: 'Mostrar Configuracion Puerto',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
            if(grid.getStore().getAt(rowIndex).data.estado=="Activo"  || grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion"){
                if(modelo=="MEA1" || modelo=="MEA3"){
                    verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarConfiguracionPuertoDslamMea");
                }
                else{
                    alert("No existe Accion para este Dslam");
                }
            }
        }
    },
    {
        getClass: function(v, meta, rec) {
            if (rec.get('estado') == "Activo" || rec.get('estado') == "EnVerificacion") {
                if(rec.get('modeloElemento')=="MEA1" || rec.get('modeloElemento')=="MEA3"){
                    return 'button-grid-verMacPuerto';
                }
            }
            else{
                return 'button-grid-invisible';
            }

        },
        tooltip: 'Mostrar Errores Puerto',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
            if(grid.getStore().getAt(rowIndex).data.estado=="Activo"  || grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion"){
                if(modelo=="MEA1" || modelo=="MEA3"){
                    verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarErroresPuertoDslamMea");
                }
                else{
                    alert("No existe Accion para este Dslam");
                }
            }
        }
    },
    {
        getClass: function(v, meta, rec) {
            if (rec.get('estado') == "Activo" || rec.get('estado') == "EnVerificacion") {
                if(rec.get('modeloElemento')=="MEA1" || rec.get('modeloElemento')=="MEA3"){
                    return 'button-grid-verMacPuerto';
                }
            }
            else{
                return 'button-grid-invisible';
            }

        },
        tooltip: 'Mostrar Interface Puerto',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
            if(grid.getStore().getAt(rowIndex).data.estado=="Activo"  || grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion"){
                if(modelo=="MEA1" || modelo=="MEA3"){
                    verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarInterfacePuertoDslamMea");
                }
                else{
                    alert("No existe Accion para este Dslam");
                }
            }
        }
    },
    {
        getClass: function(v, meta, rec) {
            if (rec.get('estado') == "Activo" || rec.get('estado') == "EnVerificacion") {
                if(rec.get('modeloElemento')=="MEA1" || rec.get('modeloElemento')=="MEA3"){
                    return 'button-grid-verMacPuerto';
                }
            }
            else{
                return 'button-grid-invisible';
            }

        },
        tooltip: 'Mostrar Vci Puerto',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
            if(grid.getStore().getAt(rowIndex).data.estado=="Activo"  || grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion"){
                if(modelo=="MEA1" || modelo=="MEA3"){
                    verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarVciPuertoDslamMea");
                }
                else{
                    alert("No existe Accion para este Dslam");
                }
            }
        }
    },
    {
        getClass: function(v, meta, rec) {
            if (rec.get('estado') == "Activo" || rec.get('estado') == "EnVerificacion") {
                if(rec.get('modeloElemento')=="MEA1" || rec.get('modeloElemento')=="MEA3"){
                    return 'button-grid-verMacPuerto';
                }
            }
            else{
                return 'button-grid-invisible';
            }

        },
        tooltip: 'Mostrar Tiempo Actividad Puerto',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
            if(grid.getStore().getAt(rowIndex).data.estado=="Activo"  || grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion"){
                if(modelo=="MEA1" || modelo=="MEA3"){
                    verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarTiempoActividadPuertoDslamMea");
                }
                else{
                    alert("No existe Accion para este Dslam");
                }
            }
        }
    },
    {
        getClass: function(v, meta, rec) {
            if (rec.get('estado') == "Activo" || rec.get('estado') == "EnVerificacion") {
                if(rec.get('modeloElemento')=="R1AD24A" || rec.get('modeloElemento')=="R1AD48A"){
                    return 'button-grid-verMacPuerto';
                }
            }
            else{
                return 'button-grid-invisible';
            }

        },
        tooltip: 'Ver Codificacion',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                if(grid.getStore().getAt(rowIndex).data.estado=="Activo"  || grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion"){
                    if(modelo=="R1AD24A" || modelo=="R1AD48A"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "mostrarCodificacionDslamR1");
                    }
                    else{
                        alert("No existe Accion para este Dslam");
                    }
                }
            }
    },
    {
        getClass: function(v, meta, rec) {
            if (rec.get('estado') == "Activo" || rec.get('estado') == "EnVerificacion") {
                if(rec.get('modeloElemento')=="7224"){
                    return 'button-grid-verMacPuerto';
                }
                if(rec.get('modeloElemento')=="6524"){
                    return 'button-grid-verMacPuerto';
                }
                if(rec.get('modeloElemento')=="R1AD24A" || rec.get('modeloElemento')=="R1AD48A"){
                    return 'button-grid-verMacPuerto';
                }
                if(rec.get('modeloElemento')=="A2024" || rec.get('modeloElemento')=="A2048"){
                    return 'button-grid-verMacPuerto';
                }
                if(rec.get('modeloElemento')=="MEA1" || rec.get('modeloElemento')=="MEA3"){
                    return 'button-grid-verMacPuerto';
                }
            }
            else{
                return 'button-grid-invisible';
            }

        },
        tooltip: 'Resetear Puerto',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                if(grid.getStore().getAt(rowIndex).data.estado=="Activo"  || grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion"){
                    if(modelo=="7224"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "resetearPuertoDslam7224");
                    }
                    else if(modelo=="6524"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "resetearPuertoDslam6524");
                    }
                    else if(modelo=="R1AD24A" || modelo=="R1AD48A"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "resetearPuertoDslamR1");
                    }
                    else if(modelo=="A2024" || modelo=="A2048"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "resetearPuertoDslamA20");
                    }
                    else if(modelo=="MEA1" || modelo=="MEA3"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "resetearPuertoDslamMea");
                    }
                    else{
                        alert("No existe Accion para este Dslam");
                    }
                }
            }
    },
    {
        getClass: function(v, meta, rec) {
            if (rec.get('estado') == "Activo" || rec.get('estado') == "EnVerificacion") {
                if(rec.get('modeloElemento')=="7224"){
                    return 'button-grid-verMacPuerto';
                }
                if(rec.get('modeloElemento')=="6524"){
                    return 'button-grid-verMacPuerto';
                }
                if(rec.get('modeloElemento')=="R1AD24A" || rec.get('modeloElemento')=="R1AD48A"){
                    return 'button-grid-verMacPuerto';
                }
            }
            else{
                return 'button-grid-invisible';
            }

        },
        tooltip: 'Cambiar Codificacion',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                if(grid.getStore().getAt(rowIndex).data.estado=="Activo"  || grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion"){
                    if(modelo=="7224"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "cambiarCodificacionPuertoDslam7224");
                    }
                    else if(modelo=="6524"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "cambiarCodificacionPuertoDslam6524");
                    }
                    else if(modelo=="R1AD24A" || modelo=="R1AD48A"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "cambiarCodificacionPuertoDslamR1");
                    }
                    else{
                        alert("No existe Accion para este Dslam");
                    }
                }
            }
    },
    {
        getClass: function(v, meta, rec) {
            if (rec.get('estado') == "Activo" || rec.get('estado') == "EnVerificacion") {
                if(rec.get('modeloElemento')=="7224"){
                    return 'button-grid-verMacPuerto';
                }
                if(rec.get('modeloElemento')=="R1AD24A" || rec.get('modeloElemento')=="R1AD48A"){
                    return 'button-grid-verMacPuerto';
                }
                if(rec.get('modeloElemento')=="MEA1" || rec.get('modeloElemento')=="MEA3"){
                    return 'button-grid-verMacPuerto';
                }
            }
            else{
                return 'button-grid-invisible';
            }

        },
        tooltip: 'Limpiar Contadores',
        handler: function(grid, rowIndex, colIndex) {
            var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
                if(grid.getStore().getAt(rowIndex).data.estado=="Activo"  || grid.getStore().getAt(rowIndex).data.estado=="EnVerificacion"){
                    if(modelo=="7224"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "limpiarContadoresPuertoDslam7224");
                    }
                    else if(modelo=="R1AD24A" || modelo=="R1AD48A"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "limpiarContadoresPuertoDslamR1");
                    }
                    else if(modelo=="MEA1" || modelo=="MEA3"){
                        verScriptVariableDslam(grid.getStore().getAt(rowIndex).data, "limpiarContadoresPuertoDslamMea");
                    }
                    else{
                        alert("No existe Accion para este Dslam");
                    }
                }
            }
    },
    //Reenviar código temporal usuario portal netlifecam
    {
        getClass: function (v, meta, rec) {
            var permiso = $("#ROLE_151-847");
            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

            if (!boolPermiso) 
            {
                return 'button-grid-invisible';
            } 
            else if (rec.get('botones') == "SI" && rec.get('descripcionProducto')=='CAMARA IP' && rec.get('estado') == "Activo") 
            {
                return 'button-grid-mail';
            }
            else
            {
                return 'button-grid-invisible';
            }
        },
        tooltip: 'Reenviar Código Temporal',
        handler: function (grid, rowIndex, colIndex) {
            //reenviarCodigoTemporal(grid.getStore().getAt(rowIndex).data);
            Ext.Msg.alert('Mensaje ', 'Acción no disponible por el momento. ' +
                'Por favor notifique a Sistemas!');
        }
    },
    //SETEAR ESQUEMA PE-HSRP
    {
        getClass: function(v, meta, rec) {
            var validaEnlace = (rec.get('tipoEnlace') !== null) ? rec.get('tipoEnlace').substring(0, 9):rec.get('tipoEnlace');
            if ((rec.get('estado') === "AsignadoTarea") && (rec.get('descripcionProducto') === 'INTMPLS' || rec.get('descripcionProducto') === 'INTERNET'
                || rec.get('descripcionProducto') === 'L3MPLS' || rec.get('descripcionProducto') === 'L3MPLS SDWAN' 
                || rec.get('descripcionProducto') === 'INTERNET SDWAN') 
                && rec.get('prefijoEmpresa') === 'TN' && rec.get('configuracionPeHsrp') === 'S'
                && validaEnlace === 'PRINCIPAL' && rec.get('nombreProducto')!== 'CLEAR CHANNEL PUNTO A PUNTO')
            {
                    return 'button-grid-setearPeHsrp';
            }
            else
            {
                return 'button-grid-invisible';
            }
        },
        tooltip: 'Definir Esquema PE-HSRP',
        handler: function (grid, rowIndex, colIndex)
        {
            setearEsquemaPeHsrp(grid.getStore().getAt(rowIndex).data.idServicio,grid.getStore().getAt(rowIndex).data.productoId);
        }
    },
    //Seguimiento Servicios
    {
        getClass: function(v, meta, rec) 
        {
            if (rec.get('flujo') == "TN")
            {
                var validaEnlace = (rec.data.tipoEnlace !== null) ? rec.data.tipoEnlace.substring(0, 9):rec.data.tipoEnlace;
                if ( rec.get('nombreProducto') !== 'CLEAR CHANNEL PUNTO A PUNTO' &&
                    ((rec.get('descripcionProducto') === "INTERNET" ||
                     rec.get('descripcionProducto') === "L3MPLS"   ||
                     rec.get('descripcionProducto') === "L3MPLS SDWAN"   ||
                     rec.get('descripcionProducto') === "INTERNET SDWAN"   ||
                     rec.get('descripcionProducto') === "INTMPLS")  &&
                     validaEnlace === 'PRINCIPAL' ) 
                     || ( rec.get('nombreProducto') !== 'CLEAR CHANNEL PUNTO A PUNTO' &&
                         rec.data.tipoEnlace === 'BACKUP' && rec.get('descripcionProducto') === "L3MPLS"
                          && rec.get('esConcentrador') === "SI"  && rec.get('esNodoWifi') === "S")
                        || ( rec.get('nombreProducto') !== 'CLEAR CHANNEL PUNTO A PUNTO' &&
                        (rec.get('descripcionProducto') === "INTERNET" || rec.get('descripcionProducto') === "INTMPLS"
                        || rec.get('descripcionProducto') === "L3MPLS" || rec.get('descripcionProducto') === "INTERNET SDWAN") 
                        && rec.data.tipoEnlace === 'BACKUP' ))
                {
                        return 'button-grid-Tracing';
                }
            }
            
            return 'button-grid-invisible';            
        },
        tooltip: 'Seguimiento Servicio',
        handler: function(grid, rowIndex, colIndex) 
        {
            if(grid.getStore().getAt(rowIndex).data.flujo==="TN")
            {
                if(grid.getStore().getAt(rowIndex).data.descripcionProducto==="INTERNET" ||
                   grid.getStore().getAt(rowIndex).data.descripcionProducto==="L3MPLS" ||
                   grid.getStore().getAt(rowIndex).data.descripcionProducto==="INTMPLS" ||
                   grid.getStore().getAt(rowIndex).data.descripcionProducto==="INTERNET SDWAN" ||
                   grid.getStore().getAt(rowIndex).data.descripcionProducto==="L3MPLS SDWAN")
                {
                    seguimientoServicio(grid.getStore().getAt(rowIndex).data,grid);
                }
                
            }
        }
    },
    //Seguimiento Clear Channel
    {
        getClass: function(v, meta, rec) 
        {

            if (rec.get('flujo') == "TN")
            {
                var validaEnlace = (rec.data.tipoEnlace !== null) ? rec.data.tipoEnlace.substring(0, 9):rec.data.tipoEnlace;
                if ((rec.get('nombreProducto') === "CLEAR CHANNEL PUNTO A PUNTO")  && 
                    (rec.get('estado') === "Activo") && 
                     validaEnlace === 'PRINCIPAL'
                     )
                {
                        return 'button-grid-verParametrosIniciales';
                }
            }
            
            return 'button-grid-invisible';            
        },
        tooltip: 'Seguimiento Clear Channel',
        handler: function(grid, rowIndex, colIndex) 
        {
            if(grid.getStore().getAt(rowIndex).data.flujo==="TN")
            {
                showInfoclearChannel(grid.getStore().getAt(rowIndex).data,grid);
            }
        }
    },
    //Activar Servicio Clear Channel Punto a Punto
    {
        getClass: function(v, meta, rec) 
        {
            if (rec.get('flujo') == "TN")
            {
                var validaEnlace = (rec.data.tipoEnlace !== null) ? rec.data.tipoEnlace.substring(0, 9):rec.data.tipoEnlace;
                var requiereTransporte = (rec.data.strClearChannelPuntoAPuntoTransporte!== null) ? rec.data.strClearChannelPuntoAPuntoTransporte:"";
                var esClearChannel = (rec.data.boolIsClearChannel !== null) ? rec.data.boolIsClearChannel : false;
                if ( ( requiereTransporte ==="NO" &&
                       esClearChannel &&
                      (rec.get('estado') === "Pendiente") &&
                      (validaEnlace === 'PRINCIPAL')) || 
                     ((rec.get('descripcionProducto') === "INTERNET") &&
                    esClearChannel &&
                      (rec.get('strClearChannelPuntoAPuntoTransporte') === "NO") &&
                      (rec.get('estado') === "Pendiente") &&
                      (validaEnlace === 'BACKUP'))
                    ) 
                {
                        return 'button-grid-reintentoMonitoreTg';
                }
            }
            return 'button-grid-invisible';            
        },
        tooltip: 'Cambiar a AsignadoTarea',
        handler: function(grid, rowIndex, colIndex) 
        {
            if(grid.getStore().getAt(rowIndex).data.flujo==="TN")
            {
                if(grid.getStore().getAt(rowIndex).data.descripcionProducto==="INTERNET")
                {
                    var rec                 = store.getAt(rowIndex);
                    confirmarAsignacionTarea(rec.get('idServicio'), rec.get('login'));
                }
                
            }
        }
    },
    //Activar Seguridad Sdwan
    {
        getClass: function(v, meta, rec) 
        {
            if (rec.get('flujo') == "TN")
            {
                //var validaEnlace = (rec.data.tipoEnlace !== null) ? rec.data.tipoEnlace.substring(0, 9):rec.data.tipoEnlace;
                if (rec.get('descripcionProducto') === "SECSALES" && rec.get('estado') === "Pendiente" && rec.get('boolSecureCpe') === "N")
                {
                        return 'button-grid-seguridadSDWAN';
                }
            }
            
            return 'button-grid-invisible';            
        },
        tooltip: 'Activar Seguridad Sdwan',
        handler: function(grid, rowIndex, colIndex) 
        {
            if(grid.getStore().getAt(rowIndex).data.flujo==="TN")
            {
                if(grid.getStore().getAt(rowIndex).data.descripcionProducto==="SECSALES" )
                {
                    seguridadSdwan(grid.getStore().getAt(rowIndex).data);
                }
                
            }
        }
    },
    //Activar Secure Cpe
    {
        getClass: function(v, meta, rec) 
        {
            if (rec.get('flujo') == "TN")
            {
                if(!strPermisoActivar){ 
                    return 'button-grid-invisible';
                }
                else
                {
                    if (rec.get('descripcionProducto') === "SECSALES" && rec.get('estado') === "Pendiente" && rec.get('boolSecureCpe') === "S")
                    {
                            return 'button-grid-seguridadSDWAN';
                    }
                }
            }
            
            return 'button-grid-invisible';            
        },
        tooltip: 'Activar Secure Cpe',
        handler: function(grid, rowIndex, colIndex) 
        {
            if(grid.getStore().getAt(rowIndex).data.flujo==="TN")
            {
                if(grid.getStore().getAt(rowIndex).data.descripcionProducto==="SECSALES" )
                {
                    seguridadCpe(grid.getStore().getAt(rowIndex).data);
                }
                
            }
        }
    },
        //VER Correo Resumen Compra
        {
            getClass: function(v, meta, rec) {
                if (rec.get('boolResumenCompra'))
                {             
                    return 'button-grid-verCorreo';
               
                }
               return 'button-grid-invisible';                     
            },
            tooltip: 'Enviar Correo Resumen Compra',
            handler: function(grid, rowIndex, colIndex) {
                enviarCorreoResumenCompra(grid.getStore().getAt(rowIndex).data);
            }
        }
    ,
    //Consultar horas de soporte
    {
        getClass: function(v, meta, rec) {            
            if( (rec.get('nombreProducto')== rec.get('strValorProductoPaqHoras')) 
                &&  (rec.get('prefijoEmpresa')  == 'TN' )
                &&  (rec.get('estado') === "Activo")
                )
                 {
                    return 'button-grid-show';
            }
            return 'button-grid-invisible';
            
        },
        tooltip: 'Consultar horas de soporte',
        handler: function(grid, rowIndex, colIndex) {
            consultarHorasSoporte(grid.getStore().getAt(rowIndex).data);
        }
    },
    //Nos direcciona al registro de la tarea de Paquete de HORAS DE SOPORTE
    //   por tipo de soporte y por servicios/productos.
    {
       getClass: function(v, meta, rec) {
        //Si es producto es PAQUETE HORAS SOPORTE
           if( (rec.get('nombreProducto')== rec.get('strValorProductoPaqHoras')) 
                && (rec.get('prefijoEmpresa')  == 'TN' )
                &&  (rec.get('estado') === "Activo")
                )
                {
                  
                    return 'button-grid-paquete-hsop';  
          

                 
           }
           else
                return 'button-grid-invisible';
       },
       tooltip: 'Registrar Soporte al paquete de Horas de Soporte',
       handler: function(grid, rowIndex, colIndex) {
           paqueteHorasSoporte(grid.getStore().getAt(rowIndex).data);
       }
    }
    ,

    //Ver Detalle de Paquete de HORAS DE SOPORTE
    {
        getClass: function(v, meta, rec) {
        //Si es producto es PAQUETE HORAS SOPORTE
        if( ((rec.get('nombreProducto')== rec.get('strValorProductoPaqHoras'))  || (rec.get('nombreProducto')== rec.get('strValorProductoPaqHorasRec')) ) 
                &&  (rec.get('prefijoEmpresa')  == 'TN' )
                &&  (rec.get('estado') === "Activo")
                )
            {
               return 'button-grid-detalle-hsop';     
            }
        else
            return 'button-grid-invisible';
        },
        tooltip: 'Ver Detalle de Paquete de Horas de Soporte',
        handler: function(grid, rowIndex, colIndex) {
            verDetallePaqueteHorasSoporte(grid.getStore().getAt(rowIndex).data);
        }
    }
 
        ,
        //VER Correo Cambio de plan
        {
            getClass: function(v, meta, rec) {
                if (rec.get('boolCambioPlanCP') && rec.get('botones') === 'SI')
                {             
                    return 'button-grid-verCorreo';
               
                }
               return 'button-grid-invisible';                     
            },
            tooltip: 'Enviar Correo Cambio de plan',
            handler: function(grid, rowIndex, colIndex) {
                enviarCorreoCambioPlan(grid.getStore().getAt(rowIndex).data);
            }
        }
]


   /**
    * Funcion que sirve para subir acta de entrega recepcion para la empresa TN
    * 
    * @author      Edgar Holguin     <eholguin@telconet.ec>
    * @param       data        Informacion que fue cargada en el grid
    * @version     1.0     11-04-2015
    * 
    * */       
   function subirActaRecepcionTn(data){

         var conn = new Ext.data.Connection({
             listeners: {
             'beforerequest': {
                 fn: function (con, opt) {
                 Ext.get(document.body).mask('Procesando...');
                 },
                 scope: this
             },
             'requestcomplete': {
                 fn: function (con, res, opt) {
                 Ext.get(document.body).unmask();
                 },
                 scope: this
             },
             'requestexception': {
                 fn: function (con, res, opt) {
                 Ext.get(document.body).unmask();
                 },
                 scope: this
             }
             }
       }); 

        var formPanel = Ext.create('Ext.form.Panel', 
        {      
           width: 500,
           frame: true,        
           bodyPadding: '10 10 0',

           defaults: {
               anchor: '100%',
               allowBlank: false,
               msgTarget: 'side',
               labelWidth: 50
           },

           items: [{
               xtype: 'filefield',
               id: 'form-file',
               name: 'archivo',
               emptyText: 'Seleccione una Archivo',
               buttonText: 'Browse',
               buttonConfig: {
                   iconCls: 'upload-icon'
               }
           }],

           buttons: [{
               text: 'Subir',
               handler: function(){
                    var form = this.up('form').getForm();
                    if(form.isValid())
            {		   
                 form.submit({		    
                   url: '/soporte/gestion_documentos/fileUpload',
                   params :{
                     servicio: data.idServicio,
                     codigo:'ACT',
                   },
                   waitMsg: 'Procesando Archivo...',
                   success: function(fp, o) 
                   {   				  
                     Ext.Msg.alert("Mensaje", o.result.respuesta, function(btn){
                       if(btn=='ok')
                       {
                           win.destroy();					      				      
                       }				 	      				
                     });
                   },
                   failure: function(fp, o) {
                     Ext.Msg.alert("Alerta",o.result.respuesta);
                   }
               });
                   }
               }
           },{
               text: 'Cancelar',
               handler: function() {
               this.up('form').getForm().reset();
           store.load();		
               win.destroy();  

               }
           }]
       });

       var win = Ext.create('Ext.window.Window', {
           title: 'Subir Acta Recepcion',
           modal: true,
           width: 500,
           closable: true,
           layout: 'fit',
           items: [formPanel]
       }).show();
   }


function reversarEstadoSolicitud(data)
{
    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.get(document.body).mask('Loading...');
                },
                scope: this
            },
            'requestcomplete': {
                fn: function (con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function (con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    });


	btnguardar = Ext.create('Ext.Button', {
			text: 'Aceptar',
			cls: 'x-btn-rigth',
			handler: function()
            {
                var strObservacion = Ext.getCmp('observacionSerie').value;

				win.destroy();
				conn.request({
					method: 'POST',
					params :{
                        idServicio:          data.idServicio,
                        tipoEnlace:          data.tipoEnlace,
                        descripcionProducto: data.descripcionProducto,
                        productoId:          data.productoId,
                        observacion:         strObservacion,
					},
					url: urlReversarOrdenTrabajo,
					success: function(response){
                    var json = Ext.JSON.decode(response.responseText);

                    store.load();
                    Ext.Msg.alert('Alerta ',json.strMensaje);

					},
					failure: function(result) {
                        Ext.Msg.show({
                            title: 'Error',
                            msg: result.statusText,
                            buttons: Ext.Msg.OK,
                            icon: Ext.MessageBox.ERROR
                        });
					}
			});
			}
	});

	btncancelar = Ext.create('Ext.Button', {
                text: 'Cerrar',
                cls: 'x-btn-rigth',
                handler: function() {
                    win.destroy();
                }
        });


	formPanel = Ext.create('Ext.form.Panel', {
		bodyPadding: 5,
		waitMsgTarget: true,
		layout: 'column',
		fieldDefaults: {
			labelAlign: 'left',
			labelWidth: 150,
			msgTarget: 'side'
		},
		items:
		[
			{
				xtype: 'fieldset',
				title: 'Reversar de Asignada a AsignadoTarea',
				autoHeight: true,
				width: 475,
				items:
				[
					{
						xtype: 'displayfield',
						fieldLabel: 'Login Aux:',
						id: 'elemento_serie',
						name: 'elemento_serie',
						value: data.loginAux
					},
					{
						xtype: 'textarea',
						fieldLabel: 'Observación:',
						id: 'observacionSerie',
						name: 'observacionSerie',
						rows: 3,
						cols: 40,
					}
				]
			}
		]
	});

	win = Ext.create('Ext.window.Window', {
		title: "Reversar estado de la orden de trabajo",
		closable: false,
		modal: true,
		width: 500,
		height: 200,
		resizable: false,
		layout: 'fit',
		items: [formPanel],
		buttonAlign: 'center',
		buttons:[btnguardar,btncancelar]
	}).show();

}

/**
 * Documentación para el método 'setearEsquemaPeHsrp'.
 *
 * Método encargado de definir si una orden de servicio va utilizar el esquema de Pe-Hsrp
 *
 * @author Richard Cabrera <rcabrera@telconet.ec>
 * @version 1.0 19-03-2019
 */
function setearEsquemaPeHsrp(intIdServicio,intProducto)
{

    var connPeHsrp = new Ext.data.Connection
        ({
            listeners:
                {
                    'beforerequest':
                        {
                            fn: function()
                            {
                                Ext.MessageBox.show
                                    ({
                                        msg: 'Definiendo esquema PE-HSRP.',
                                        width: 300,
                                        wait: true,
                                        waitConfig: {interval: 200}
                                    });
                            },
                            scope: this
                        },
                    'requestcomplete':
                        {
                            fn: function()
                            {
                                Ext.MessageBox.hide();
                            },
                            scope: this
                        },
                    'requestexception':
                        {
                            fn: function()
                            {
                                Ext.MessageBox.hide();
                            },
                            scope: this
                        }
                }
        });

    strMensaje = '¿Está seguro que desea definir el esquema PE-HSRP en este servicio?';

    Ext.Msg.confirm('Alerta', strMensaje, function(btn)
    {
        if (btn === 'yes')
        {
            connPeHsrp.request
                (
                    {
                        url: urlSetearPeHsrp,
                        method: 'POST',
                        timeout: 60000,
                        params:
                            {
                                idServicio: intIdServicio,
                                idProducto: intProducto,
                            },
                        success: function(response)
                        {
                            var json = Ext.JSON.decode(response.responseText);
                            if (json.status == 'OK')
                            {
                                Ext.Msg.show(
                                    {
                                        title: 'Información',
                                        msg: 'Se configuró la Orden de Servicio para el esquema de PE-HSRP',
                                        buttons: Ext.Msg.OK,
                                        icon: Ext.MessageBox.INFO
                                    });
                                store.load({params: {start: 0, limit: 10}});
                            }
                            else
                            {
                                Ext.Msg.show(
                                    {
                                        title: 'Error',
                                        msg: response.responseText,
                                        buttons: Ext.Msg.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                            }
                        },
                        failure: function(result)
                        {
                            Ext.MessageBox.hide();
                            Ext.Msg.alert('Error', result.responseText);
                        }
                    }
                );
        }
    });
}

/**
 * Documentación para el método 'cargarArchivoInspeccion'.
 *
 * Método encargado de presentar pantalla para la carga de archivo de inspección.
 *
 * @author Pablo Pin <ppin@telconet.ec>
 * @version 1.0 16-08-2019 | Versión Inicial.
 *
 * @author Jonathan Mazon Sanchez <jmazon@telconet.ec>
 * @version 1.1 22-04-2021 - Se realiza cambio para mostar el archivo de inspección guardado en el NFS.
 * 
 */
function cargarArchivoInspeccion(idServicio, login)
{
    var id_servicio = idServicio;
    var strLogin    = login;
    var formPanel = Ext.create('Ext.form.Panel',
        {
            width: 500,
            frame: true,
            bodyPadding: '10 10 0',
            defaults: {
                anchor: '100%',
                allowBlank: false,
                msgTarget: 'side',
                labelWidth: 100
            },
            items: [
                {
                    xtype: 'filefield',
                    fieldLabel: '<b>Archivo Adjunto</b>',
                    id: 'form-file',
                    name: 'archivo',
                    emptyText: 'Seleccione un archivo',
                    buttonConfig: {
                        iconCls: 'fa fa-upload'
                    }
                }],
            buttons: [{
                text: 'Subir',
                handler: function() {
                    var form = this.up('form').getForm();
                    if (form.isValid())
                    {
                        form.submit({
                            url: strUrlcargarDocumento,
                            params: {
                                idServicio: id_servicio,
                                strLogin: strLogin,
                                strObservacion: 'Se cargó un documento de inspección',
                                strCambioEstado: 'Factible',
                                strNombreDocumento: 'Inspeccion Radio',
                                strEsInspeccionRadio: 'SI',
                                tipo: 'TECNICO',
                                strMensaje: 'Reporte Técnico de Inspección para Wifi Alquiler de Equipos',
                                data: JSON.stringify({app:"TelcosWeb", modulo:"Tecnico", submodulo:"InspeccionRadio"})
                            },
                            waitMsg: 'Procesando Archivo...',
                            success: function(fp, o)
                            {
                                Ext.Msg.alert("Mensaje", 'Se ha cargado el informe de inspección con éxito', function(btn) {
                                    if (btn == 'ok')
                                    {
                                        store.load({params: {start: 0, limit: 10}});
                                        win.destroy();
                                    }
                                });
                            },
                            failure: function(fp, o) {
                                Ext.Msg.alert("Alerta", o.result.respuesta);
                            }
                        });
                    }
                }
            }, {
                text: 'Cancelar',
                handler: function() {
                    win.destroy();
                }
            }]
        });

    var win = Ext.create('Ext.window.Window', {
        title: 'Cargar Informe Inspección',
        modal: true,
        width: 500,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

/**
 * Documentación para el método 'verDocumentoInspeccion'.
 *
 * Método encargado de presentar los archivos cargados.
 *
 * @author Pablo Pin <ppin@telconet.ec>
 * @version 1.0 16-08-2019 | Versión Inicial.
 *
 */
function verArchivoInspeccion(idServicio)
{
    var id_servicio = idServicio;
    var cantidadDocumentos = 1;
    var connDocumentos = new Ext.data.Connection
    ({
        listeners:
            {
                'beforerequest':
                    {
                        fn: function (con, opt)
                        {
                            Ext.MessageBox.show({
                                msg: 'Consultando documentos, Por favor espere!!',
                                progressText: 'Consultando...',
                                width: 300,
                                wait: true,
                                waitConfig: {interval: 200}
                            });
                        },
                        scope: this
                    },
                'requestcomplete':
                    {
                        fn: function (con, res, opt)
                        {
                            Ext.MessageBox.hide();
                        },
                        scope: this
                    },
                'requestexception':
                    {
                        fn: function (con, res, opt)
                        {
                            Ext.MessageBox.hide();
                        },
                        scope: this
                    }
            }
    });

    connDocumentos.request({
        url: '../../comercial/punto/verDocumentosEncontrados',
        method: 'post',
        params:{
            idServicio: id_servicio
        },
        success: function (response)
        {
            var text           = Ext.decode(response.responseText);
            cantidadDocumentos = text.total;

            if (cantidadDocumentos > 0)
            {
                var storeDocumentos = new Ext.data.Store
                ({
                    pageSize: 1000,
                    autoLoad: true,
                    proxy:
                        {
                            type: 'ajax',
                            url: '../../comercial/punto/verDocumentos',
                            reader:
                                {
                                    type: 'json',
                                    totalProperty: 'total',
                                    root: 'encontrados'
                                },
                            extraParams:
                                {
                                    idServicio: id_servicio,
                                    strNombreDocumento: 'Inspeccion Radio'
                                }
                        },
                    fields:
                        [
                            {name: 'ubicacionLogica', mapping: 'ubicacionLogica'},
                            {name: 'feCreacion', mapping: 'feCreacion'},
                            {name: 'linkVerDocumento', mapping: 'linkVerDocumento'},
                            {name: 'idDocumento', mapping: 'idDocumento'}
                        ]
                });

                Ext.define('Documentos',
                    {
                        extend: 'Ext.data.Model',
                        fields:
                            [
                                {name: 'ubicacionLogica', mapping: 'ubicacionLogica'},
                                {name: 'feCreacion', mapping: 'feCreacion'},
                                {name: 'linkVerDocumento', mapping: 'linkVerDocumento'},
                                {name: 'idDocumento', mapping: 'idDocumento'}
                            ]
                    });

                //grid de documentos
                gridDocumentos = Ext.create('Ext.grid.Panel',
                    {
                        id: 'gridMaterialesPunto',
                        store: storeDocumentos,
                        columnLines: true,
                        columns:
                            [
                                {
                                    header: 'Nombre Archivo',
                                    dataIndex: 'ubicacionLogica',
                                    width: 328
                                },
                                {
                                    header: 'Fecha de Carga',
                                    dataIndex: 'feCreacion',
                                    align: 'center',
                                    width: 100
                                },
                                {
                                    xtype: 'actioncolumn',
                                    header: 'Acciones',
                                    align: 'center',
                                    width: 70,
                                    items:
                                        [
                                            {
                                                iconCls: 'button-grid-show',
                                                tooltip: 'Ver Archivo Digital',
                                                handler: function (grid, rowIndex, colIndex)
                                                {
                                                    var rec = storeDocumentos.getAt(rowIndex);
                                                    verArchivoDigital(rec);
                                                }
                                            }
                                        ]
                                }
                            ],
                        viewConfig:
                            {
                                stripeRows: true,
                                enableTextSelection: true
                            },
                        frame: true,
                        height: 200
                    });

                function verArchivoDigital(rec)
                {
                    var idDocumento = rec.get('idDocumento');
                    window.location = '../../comercial/punto/descargaDocumentos' + '?idDocumento=' + idDocumento;
                }

                var formPanel = Ext.create('Ext.form.Panel',
                    {
                        width:700,
                        bodyPadding: 2,
                        waitMsgTarget: true,
                        fieldDefaults:
                            {
                                labelAlign: 'left',
                                labelWidth: 85,
                                msgTarget: 'side'
                            },
                        items:
                            [
                                {
                                    xtype: 'fieldset',
                                    title: '',
                                    defaultType: 'textfield',
                                    defaults:
                                        {
                                            width: 510
                                        },
                                    items:
                                        [
                                            gridDocumentos
                                        ]
                                }
                            ],
                        buttons:
                            [{
                                text: 'Cerrar',
                                handler: function ()
                                {
                                    win.destroy();
                                }
                            }]
                    });

                var win = Ext.create('Ext.window.Window',
                    {
                        title: 'Documentos Cargados',
                        modal: true,
                        width: 550,
                        closable: true,
                        layout: 'fit',
                        items: [formPanel]
                    }).show();

            }
            else
            {
                Ext.Msg.show({
                    title: 'Mensaje',
                    msg: 'El servicio seleccionado no posee archivos adjuntos.',
                    buttons: Ext.Msg.OK,
                    animEl: 'elId',
                });
            }

        },
        failure: function (result)
        {
            Ext.Msg.show({
                title: 'Error',
                msg: result.statusText,
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR
            });
        }
    });
}

/**
 *
 * Esta función muestra un modal que permite visualizar
 * la data técnica de un producto SECURITY NG FIREWALL
 *
 * @author Joel Muñoz<jrmunoz@telconet.ec>
 * @version 1.0 29-09-2022 | Versión Inicial.
 *
 */
function showDataTecnicaNGF(data)
{

    let tieneNubePublica = false;
    let esNGFirewall =  false;
    // data.nombreProducto = 'otro'
    // data.strNGFNubePublica = 'NINGUNO'


    if(data.strNGFNubePublica.length > 0 && data.strNGFNubePublica !== 'NINGUNO')
    {
        tieneNubePublica =  '1';
    }


    if(data.nombreProducto === 'SECURITY NG FIREWALL')
    {
        esNGFirewall =  '1';
    }

    var panelDataTecnica = Ext.create('Ext.form.Panel', {
        bodyPadding: 1,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side',
            bodyStyle: 'padding:20px'
        },
        layout: {
            type: 'table',
            // The total column count must be specified here
            columns: 1
        },
        defaults: {
            // applied to each contained panel
            bodyStyle: 'padding:20px'
        },
        items: [
            //informacion del servicio/producto
            {
                xtype: 'fieldset',
                defaultType: 'textfield',
                defaults: {
                    width: 350,
                    height: 200,
                },
                items: [
                    {
                        xtype: 'container',
                        layout: {
                            type: 'table',
                            columns: 1,
                            align: 'stretch'
                        },
                        items: [
                            // Campo Tipo plan
                            {
                                xtype: 'textfield',
                                id: 'strNGFPlan',
                                name: 'strNGFPlan',
                                fieldLabel: 'Tipo plan',
                                value: data.strNGFPlan,
                                width: 350,
                                readOnly: true
                            },
                            //-------------------------------


                            // Campo Modelo equipo
                            {
                                xtype: 'textfield',
                                id: 'strNGFModeloEquipo',
                                name: 'strNGFModeloEquipo',
                                fieldLabel: 'Modelo equipo',
                                width: 350,
                                value: data.strNGFModeloEquipo,
                                readOnly: true
                            },
                            //-------------------------------


  
                            // Campo Nube Publica
                            {
                                xtype: 'textfield',
                                id: 'strNGFNubePublica',
                                name: 'strNGFNubePublica',
                                width: 350,
                                fieldLabel: 'Nube pública',
                                value: data.strNGFNubePublica,
                                readOnly: true
                            },
                            //-------------------------------

                            // Campo Propiedad del equipo
                            {
                                xtype: 'textfield',
                                id: 'strNGFPropiedadEquipo',
                                name: 'strNGFPropiedadEquipo',
                                width: 350,
                                fieldLabel: 'Propiedad del equipo',
                                value: data.strNGFPropiedadEquipo,
                                readOnly: true,
                                hidden:  (esNGFirewall && tieneNubePublica)
                            },
                            //-------------------------------


                            // Campo Serie
                            {
                                xtype: 'textfield',
                                id: 'strNGFSerieEquipo',
                                name: 'strNGFSerieEquipo',
                                width: 350,
                                fieldLabel: 'Serie',
                                value: data.strNGFSerieEquipo,
                                readOnly: true,
                                hidden:  (esNGFirewall && tieneNubePublica)
                            },
                            //-------------------------------

                            // Campo Modelo
                            {
                                xtype: 'textfield',
                                id: 'strNGFNombreModeloElemento',
                                name: 'strNGFNombreModeloElemento',
                                width: 350,
                                fieldLabel: 'Modelo',
                                value: data.strNGFNombreModeloElemento,
                                readOnly: true,
                                hidden:  (esNGFirewall && tieneNubePublica)
                           },
                          //-------------------------------
                

                            // Campo Mac 
                            {
                                xtype: 'textfield',
                                id: 'strNGFMACEquipo',
                                name: 'strNGFMACEquipo',
                                width: 350,
                                fieldLabel: 'Mac',
                                value: data.strNGFMACEquipo,
                                readOnly: true,
                                hidden:  (esNGFirewall && tieneNubePublica)

                            },
                            //-------------------------------


                            // Campo Ip/FQDN 
                            {
                                xtype: 'textfield',
                                id: 'strNGFIpDns',
                                name: 'strNGFIpDns',
                                width: 350,
                                fieldLabel: 'Ip/FQDN',
                                value: data.strNGFIpDns,
                                readOnly: true,
                                hidden:  !(esNGFirewall && tieneNubePublica)

                            },
                            //-------------------------------


                            // Campo Puerto Administración
                            {
                                xtype: 'textfield',
                                id: 'strNGFPuertoAdministracionWeb',
                                name: 'strNGFPuertoAdministracionWeb',
                                width: 350,
                                fieldLabel: 'Puerto Administración<br>Web',
                                value: data.strNGFPuertoAdministracionWeb,
                                hidden:  !(esNGFirewall && tieneNubePublica),
                                readOnly: true
                            },
                            //-------------------------------


                            // Campo Serial 
                            {
                                xtype: 'textfield',
                                id: 'strNGFlicencia',
                                name: 'strNGFlicencia',
                                width: 350,
                                fieldLabel: 'Serial Licencia',
                                value: data.strNGFlicencia,
                                hidden:  !(esNGFirewall && tieneNubePublica),
                                readOnly: true
                            },
                            //-------------------------------
                            

                            
                        ]
                    }

                ]
            },//cierre de la informacion servicio/producto

        ],
        buttons: [{
            text: 'Aceptar',
            handler: function(){
                winDataTecnica.destroy();
            }
        }]
    });

    var winDataTecnica = Ext.create('Ext.window.Window', {
        title: 'Ver Data Técnica',
        modal: true,
        width: 380,
        closable: true,
        layout: 'fit',
        items: [panelDataTecnica]
    }).show();
}

/**
 *
 * Esta función muestra un modal que permite editar
 * la data técnica de un producto SECURITY NG FIREWALL
 *
 * @author Joel Muñoz<jrmunoz@telconet.ec>
 * @version 1.0 29-09-2022 | Versión Inicial.
 *
 */

function editDataTecnicaNGF(data)
{

    let tieneNubePublica = false;
    let esNGFirewall =  false;
    // data.nombreProducto = 'otro'
    // data.strNGFNubePublica = 'NINGUNO'

    if(data.strNGFNubePublica.length > 0 && data.strNGFNubePublica !== 'NINGUNO')
    {
        tieneNubePublica =  '1';
    }


    if(data.nombreProducto === 'SECURITY NG FIREWALL')
    {
        esNGFirewall =  '1';
    }

    var panelEditDataTecnicaFisica = Ext.create('Ext.form.Panel', {
        bodyPadding: 1,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side',
            bodyStyle: 'padding:20px'
        },
        layout: {
            type: 'table',
            // The total column count must be specified here
            columns: 1
        },
        defaults: {
            // applied to each contained panel
            bodyStyle: 'padding:20px'
        },
        items: [
            {
                xtype: 'fieldset',
                title: 'Datos del Equipo',
                defaultType: 'textfield',
                hidden: (esNGFirewall && tieneNubePublica),
                defaults: {
                    width: 400,
                    height: 195
                },
                items: [
                    {
                        xtype: 'container',
                        layout: {
                            type: 'table',
                            columns: 1,
                            align: 'stretch'
                        },
                        items: [
                            {
                                xtype: 'textfield',
                                fieldLabel: 'Nube pública',
                                id: 'strNGFNubePublica',
                                name: 'strNGFNubePublica',
                                displayField : 'valor1',
                                valueField: 'valor1',
                                width: 350,
                                readOnly: true,
                                value: data.strNGFNubePublica,
                            },

                            // Campo Servicio
                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                fieldLabel: 'Propiedad equipo',
                                id: 'propiedadEquipo',               
                                width: 350,
                                readOnly: true,
                            },
                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                name: 'serieEquipo',
                                width: 350,
                                id: 'serieEquipo',
                                fieldLabel: '* Serie',
                                readOnly: true,
                     
                            },
                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                id: 'descEquipo',
                                name: 'descEquipo',
                                width: 350,
                                fieldLabel: '* Descripción',
                                readOnly: true,

                            },
                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                id: 'modeloEquipo',
                                name: 'modeloEquipo',
                                width: 350,
                                fieldLabel: '* Modelo',
                                readOnly: true,

                            },
                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                id: 'macEquipo',
                                name: 'macEquipo',
                                width: 350,
                                fieldLabel: '* Mac',
                                readOnly: true,

                            },
                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                id: 'ipEquipo',
                                name: 'ipEquipo',
                                width: 350,
                                fieldLabel: '* Ip',
                                readOnly: true,

                            },
                            { width: '10%', border: false},
                            {
                                height: 10,
                                layout: 'form',
                                border: false,
                                items:
                                [
                                    {
                                        xtype: 'displayfield'
                                    }
                                ]
                            },
                            {
                                xtype: 'label',
                                forId: 'labelCamposObligatorios',
                                text: '* Campos obligatorios'
                            }
                        ]
                    }

                ]
            },
        ],
        buttons: [
            {
                text: 'Cancelar',
                handler: function(){
                    winEditDataTecnica.destroy();
                }
            }
        ]
    });



    var panelEditDataTecnica = Ext.create('Ext.form.Panel', {
        bodyPadding: 1,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side',
            bodyStyle: 'padding:20px'
        },
        layout: {
            type: 'table',
            // The total column count must be specified here
            columns: 1
        },
        defaults: {
            // applied to each contained panel
            bodyStyle: 'padding:20px'
        },
        items: [
            {
                xtype: 'fieldset',
                defaultType: 'textfield',
                defaults: {
                    width: 400,
                    height: 30,
                },
                items: [
                    {
                        xtype: 'container',
                        layout: {
                            type: 'table',
                            columns: 1,
                            align: 'stretch'
                        },
                        items: [
                            // Campo Nube Publica
                            {
                                xtype: 'textfield',
                                fieldLabel: 'Nube pública',
                                id: 'strNGFNubePublica',
                                name: 'strNGFNubePublica',
                                displayField : 'valor1',
                                valueField: 'valor1',
                                width: 350,
                                readOnly: true,
                                value: data.strNGFNubePublica,
                            },
                        ]
                    }

                ]
            },
            
            {
                xtype: 'fieldset',
                defaultType: 'textfield',
                title: 'Editar',
                defaults: {
                    width: 400,
                    height: 150,
                },
                items: [
                    {
                        xtype: 'container',
                        layout: {
                            type: 'table',
                            columns: 1,
                            align: 'stretch'
                        },
                        items: [
                            // Campo IPFQDN
                            {
                                xtype: 'textfield',
                                id: 'strNGFIpDns',
                                name: 'strNGFIpDns',
                                width: 350,
                                fieldLabel: '* Ip/FQDN',
                                value: data.strNGFIpDns,
                                readOnly: !(esNGFirewall && tieneNubePublica),
                                maxLength: 20,
                                maxLengthText: 'El máximo de caracteres permitidos para este campo es 20',
                                listeners: {
                                    change: function(){
                                        checkUpdateDataTecnicaNGF(data);
                                    }
                                }
                            },


                            // Campo ADMINISTRACION WEB
                            {
                                xtype: 'textfield',
                                id: 'strNGFPuertoAdministracionWeb',
                                name: 'strNGFPuertoAdministracionWeb',
                                width: 350,
                                fieldLabel: '* Puerto Administración<br>Web',
                                value: data.strNGFPuertoAdministracionWeb,
                                validator: function(v) {
                                    if((esNGFirewall && tieneNubePublica && permisoEditDataTecnicaNGF))
                                    {
                                        return  /^[0-9]+$/.test(v)? true : 'Se permiten solo números';
                                    }
                                    else
                                    {
                                        return true;
                                    }
                                },
                                readOnly: !(esNGFirewall && tieneNubePublica),
                                listeners: {
                                    change: function(){
                                        checkUpdateDataTecnicaNGF(data)
                                    }
                                }
                            },


                            // Campo Serial Licencia
                            {
                                xtype: 'textfield',
                                id: 'strNGFlicencia',
                                name: 'strNGFlicencia',
                                width: 350,
                                fieldLabel: '* Serial Licencia',
                                value: data.strNGFlicencia,
                                readOnly: !(esNGFirewall && tieneNubePublica),
                                listeners: {
                                    change: function(){
                                        checkUpdateDataTecnicaNGF(data)
                                    }
                                }
                            },
                            {
                                height: 10,
                                layout: 'form',
                                border: false,
                                items:
                                [
                                    {
                                        xtype: 'displayfield'
                                    }
                                ]
                            },
                            {
                                xtype: 'label',
                                forId: 'labelCamposObligatorios',
                                text: '* Campos obligatorios'
                            }
                            
                        ]
                    }

                ]
            },//cierre de la informacion servicio/producto

        ],
        buttons: [
            {
                id: 'btnEditDataNGF',
                disabled: true,
                hidden: !(esNGFirewall && tieneNubePublica),
                text: 'Actualizar',
                handler: function(){
                    let strNGFIpFqns = Ext.getCmp('strNGFIpDns').getValue().trim();
                    let strNGFPuertoAdministracionWeb = Ext.getCmp('strNGFPuertoAdministracionWeb').getValue().trim();
                    let strNGFlicencia = Ext.getCmp('strNGFlicencia').getValue().trim();


                    if(!strNGFIpFqns 
                        || !strNGFPuertoAdministracionWeb 
                        || !strNGFlicencia)  {
                        Ext.Msg.alert("Alerta","Por favor, ingrese los campos obligatorios", function(btn){
                            return false;
                        });
                    } else {
                        Ext.get(panelEditDataTecnica.getId()).mask('Actualizando Data Técnina...');

                        Ext.Ajax.request({
                            url: urlUpdateDataTecnicaNGF,
                            method: 'post',
                            timeout: 40000000000,
                            params: {
                                idServicio                 : data.idServicio,
                                idProducto                 : data.productoId,

                                strNGFIpFqns: (strNGFIpFqns !== data.strNGFIpDns.trim() ? strNGFIpFqns: ''),
                                strNGFPuertoAdministracionWeb: (strNGFPuertoAdministracionWeb !== data.strNGFPuertoAdministracionWeb.trim() 
                                ? strNGFPuertoAdministracionWeb: ''),
                                strNGFlicencia: (strNGFlicencia !== data.strNGFlicencia.trim() ? strNGFlicencia: '')
                            },
                            success: function(response) 
                            {
                                let objResponse  = Ext.decode(response.responseText);
                                Ext.get(panelEditDataTecnica.getId()).unmask();
                                
                                if(objResponse.strResultado === 'OK')
                                {
                                    Ext.Msg.alert('Mensaje ', objResponse.strMensaje, function(){
                                        winEditDataTecnica.destroy();
                                        store.load();
                                
                                    });  
                                }
                                else
                                {
                                    Ext.Msg.alert('Error ', objResponse.strMensaje, function(){
                                        winEditDataTecnica.destroy();
                                        store.load();
                                    });   
                                }
                            },
                            failure: function(responseError)
                            {
                                Ext.get(panelEditDataTecnica.getId()).unmask();

                                if(responseError.statusText)
                                {
                                    Ext.Msg.alert('Error ','Error: ' + responseError.statusText, function(){
                                    });  
                                }
                                else
                                {
                                    Ext.Msg.alert('Error ','Error: ' + 'Error no especificado en el servidor', function(){
                                    });
                                }
                            }
                        });
                    }
                },
            },
            {
                text: 'Cancelar',
                handler: function(){
                    winEditDataTecnica.destroy();
                }
            }
        ]
    });

    var winEditDataTecnica = Ext.create('Ext.window.Window', {
        title: 'Editar Data Técnica',
        modal: true,
        width: 390,
        closable: true,
        layout: 'fit',
        items: [(tieneNubePublica && esNGFirewall) ? panelEditDataTecnica : panelEditDataTecnicaFisica]
    }).show();
}

/**
 *
 * Esta función aplica una validación sobre un modal para activar
 * el botón de actualizar únicamente cuando se detecte que se haya
 * modificado algún campo.
 *
 * @author Joel Muñoz<jrmunoz@telconet.ec>
 * @version 1.0 29-09-2022 | Versión Inicial.
 * 
 * @author Joel Muñoz<jrmunoz@telconet.ec>
 * @version 1.1 03-09-2022 | Valida que si un campo de tipo numérico no es válido 
 * deshabilite un botón
 *
 */
function checkUpdateDataTecnicaNGF(data)
{

    let intCamposEditar = 0;

    let strNGFIpFqns = Ext.getCmp('strNGFIpDns').getValue().trim();
    let strNGFPuertoAdministracionWeb = Ext.getCmp('strNGFPuertoAdministracionWeb').getValue().trim();
    let strNGFlicencia = Ext.getCmp('strNGFlicencia').getValue().trim();


        if((strNGFIpFqns !== data.strNGFIpDns.trim()))
        {
            intCamposEditar++;
        }
    
        if((strNGFPuertoAdministracionWeb !== data.strNGFPuertoAdministracionWeb.trim()))
        {
            intCamposEditar++;
        }
    
        if((strNGFlicencia !== data.strNGFlicencia.trim()))
        {
            intCamposEditar++;
        }
    
        let booleanNGFPuertoAdministracionWebValido = /^[0-9]+$/.test(strNGFPuertoAdministracionWeb)
    
        if(intCamposEditar > 0 && booleanNGFPuertoAdministracionWebValido)
        {
            Ext.getCmp('btnEditDataNGF').enable()
        }
        else
        {
            Ext.getCmp('btnEditDataNGF').disable()
        }
}