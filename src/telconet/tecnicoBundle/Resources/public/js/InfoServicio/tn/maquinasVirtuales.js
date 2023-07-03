
var storageTotal      = 0;
var memoriaTotal      = 0;
var procesadorTotal   = 0;
var licenciaTotal     = 0;
var idVCenter         = 0;
var idHyperview       = 0;
var arrayInformacion  = [];
var arrayRecursoEliminados = [];
//Recursos existentes y configurados
var arrayRecursos     = [];
//Recursos existentes y configurados de Licencia
var arrayRecursosLic     = [];
//Va acumulando los recursos que son configurados dentro de las maquinas 
//virtuales para efecto de validacion
var arrayRecursosConf = [];
//Recursos de una maquina virtual previo a ser guardados
var arrayRecursoTmp   = [];

var arrayResumenGeneralRecursos = [];
//----------------------------------

var carpeta           = null;
var ciudad            = null;
var idServicio        = 0;
var login             = '';

function administrarMaquinasVirtuales(data)
{
    getLicencias();
    licenciaTotal     = 0;
    ciudad     = data.nombreCanton;
    idServicio = data.idServicio;
    login      = data.login;

    Ext.get(gridServicios.getId()).mask('Obteniendo Datos Generados de Hosting...');
    
        Ext.Ajax.request({
        url: urlGetInformacionGeneralHosting,
            method: 'post',
            params: 
            { 
            idServicio      : idServicio,
            tipoInformacion : 'GENERAL'
            },
            success: function(response)
            {

                var objJson = Ext.JSON.decode(response.responseText)[0];
                var objJsonLic = "" ;
                //Valor total de los recursos
                storageTotal    = objJson.storage;
                memoriaTotal    = objJson.memoria;
                procesadorTotal = objJson.procesador;

                idVCenter       = objJson.idVcenter;
                idHyperview     = objJson.idHyperview;
                datastore       = objJson.datastore;

                //Detalle de recursos
                arrayRecursos   = objJson.arrayDetalleRecursos;
                Ext.Ajax.request({
                    url: urlGetInformacionGeneralHosting,
                    method: 'post',
                    async : false,
                    params: 
                    { 
                        idServicio      : idServicio,
                        tipoInformacion : 'SISTEMA-OPERATIVO'
                    },
                    success: function(response)
                    {
                        Ext.get(gridServicios.getId()).unmask();
                        objJsonLic       =   Ext.JSON.decode(response.responseText);
                        arrayRecursosLic =   objJsonLic;    
                        arrayRecursos['arrayDetalleLicencia']=arrayRecursosLic;
                            $.each(arrayRecursosLic, function(key, value) {
                                licenciaTotal   =   licenciaTotal + parseInt(value.valor);
                            });  
                    }
                });
                var contentHtmlIT = Ext.create('Ext.Component', {
                html: '<div id="content-recursos">'+
                            '<table style="width:30%;left:10%;">'+
                              '<tr>'+
                                    '<td align="left"><label><b>Storage:</b></label></td>'+
                                    '<td>&nbsp;</td>'+
                                    '<td style="width:100%">'+
                                        '<div id="progressbar-storage" class="ui-progressbar" align="center">'+
                                            '<div id="progressbar-storage-label" class="progress-label"></div>'+
                                        '</div>'+
                                    '</td><td><a onclick="verDetalleRecurso(\'DISCO\',\'resumenContratado\'\);" style="cursor:pointer;"\n\
                                              title="Ver Resumen" class="ui-icon ui-icon-zoomin"></a></td>'+
                              '</tr>'+
                              '<tr><td><br></td></tr>'+
                              '<tr>'+
                                    '<td align="left"><label><b>Memoria</b></label></td>'+
                                    '<td>&nbsp;</td>'+
                                    '<td style="width:100%">'+
                                        '<div id="progressbar-memoria" class="ui-progressbar">'+
                                            '<div id="progressbar-memoria-label" class="progress-label"></div>'+
                                        '</div>'+
                                    '</td><td><a onclick="verDetalleRecurso(\'MEMORIA RAM\',\'resumenContratado\'\);" style="cursor:pointer;"\n\
                                              title="Ver Resumen" class="ui-icon ui-icon-zoomin"></a></td>'+
                              '</tr>'+
                              '<tr><td><br></td></tr>'+
                              '<tr>'+
                                    '<td align="left"><label><b>Procesador:</b></label></td>'+
                                    '<td>&nbsp;</td>'+
                                    '<td style="width:100%">'+
                                        '<div id="progressbar-procesador" class="ui-progressbar">'+
                                            '<div id="progressbar-procesador-label" class="progress-label"></div>'+
                                        '</div>'+
                                    '</td><td><a onclick="verDetalleRecurso(\'PROCESADOR\',\'resumenContratado\'\);" style="cursor:pointer;"\n\
                                              title="Ver Resumen" class="ui-icon ui-icon-zoomin"></a></td>'+
                              '</tr>'+
                              '<tr><td><br></td></tr>'+
                              '<tr>'+
                                    '<td align="left"><label><b>Licencia:</b></label></td>'+
                                    '<td>&nbsp;</td>'+
                                    '<td style="width:100%">'+
                                        '<div id="progressbar-licencia" class="ui-progressbar">'+
                                            '<div id="progressbar-licencia-label" class="progress-label"></div>'+
                                        '</div>'+
                                    '</td><td><a onclick="verDetalleRecurso(\'LICENCIA\',\'resumenContratado\'\);" style="cursor:pointer;"\n' +
                                              'title="Ver Resumen" class="ui-icon ui-icon-zoomin"></a></td>'+
                              '</tr>'+
                            '</table>'+
                         '</div>',
                    style: {marginBottom: '15px', border: '0'}
                });

                var contentHtmlMV = Ext.create('Ext.Component', {
                    html:'<div class="ui-widget ui-helper-clearfix">'+
                            '<ul id="contenetMV" class="contenetMV ui-helper-reset ui-helper-clearfix"></ul>'	+
                            '<div id="trash" align="center" ><i class="fa fa-trash fa-2x" aria-hidden="true"></i></div>' + 
                          '</div>'
                });

                var formPanelResumenHousing = Ext.create('Ext.form.Panel', {
                    buttonAlign: 'center',
                    BodyPadding: 10,
                    width: 620,
                    height: 525,
                    bodyStyle: "background: white; padding: 5px; border: 0px none;",
                    frame: true,
                    items:
                        [
                            {
                                xtype: 'fieldset',
                                title: '<b>Información de Servicio Contratado</b>',
                                hidden: true,
                                layout: {
                                    tdAttrs: {style: 'padding: 5px;'},
                                    type: 'table',
                                    columns: 1,
                                    pack: 'center'
                                },
                                items: [
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: '<i class="fa fa-connectdevelop" aria-hidden="true"></i>&nbsp;<b>HyperView</b>',
                                        value: objJson.hyperview,
                                        fieldStyle:'color:#3194C4;',
                                        width:400,
                                        readOnly: true
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: '<i class="fa fa-connectdevelop" aria-hidden="true"></i>&nbsp;<b>VCenter</b>',
                                        name: 'info_cliente',
                                        id: 'info_cliente',
                                        value: objJson.vcenter,
                                        fieldStyle:'color:#3194C4;',
                                        width:400,
                                        readOnly: true
                                    },
                                   {
                                        xtype: 'textfield',
                                        fieldLabel: '<i class="fa fa-stack-exchange" aria-hidden="true"></i>&nbsp;<b>Cluster</b>',
                                        name: 'info_login',
                                        id: 'info_login',
                                        value: objJson.cluster,
                                        width:400,
                                        readOnly: true
                                    }
                                        ]
                            },
                        //Recursos Disponibles
                            {
                                xtype: 'fieldset',
                                title: '<i class="fa fa-sliders" aria-hidden="true"></i>&nbsp;<b>Recursos Contratados Disponibles</b>',
                                defaults: { 
                                        height: 110,
                                        width:560
                                    },
                                layout: {
                                    type: 'table',
                                    columns: 1,
                                    pack: 'center'
                                },
                                items: 
                                [                            
                                    contentHtmlIT
                                ]
                            },
                            //Creacion de Maquinas Virtuales
                            {
                                xtype: 'fieldset',
                                title: '<b>Contenedor de Máquinas Virtuales</b>',
                                id   : 'contentMaquinasVirtuales',
                                defaults: { 
                                        height: 'auto',
                                        width:580
                                    },
                                layout: {
                                    type: 'table',
                                    columns: 1,
                                    pack: 'center'
                                },
                                items: 
                                [
                                    {
                                        xtype: 'toolbar',
                                        dock: 'top',
                                        align: '<-',
                                        config : {
                                            height : '2.2em'
                                        },
                                        items:
                                            [
                                                {
                                                    text: '<i class="fa fa-plus-square" aria-hidden="true"></i>&nbsp;Agregar Máquina Virtual',
                                                    scope: this,
                                                    handler: function() 
                                                    {
                                                        agregarNuevaMaquinaVirtual();
                                                    }
                                                }
                                           ]
                                    },
                                    {width: '10%', border: false},
                                    contentHtmlMV
                                ]
                            }
                        ],
                    buttons: [                   
                        {
                        text: '<i class="fa fa-floppy-o" aria-hidden="true"></i>&nbsp;Guardar Máquinas',
                        handler: function() 
                        {                            
                            var boolContinua = false;
                            
                            $.each(arrayInformacion, function(key, value) {
                                if(value.idMaquina === 0)
                                {
                                    boolContinua = true;
                                    return false;
                                }
                            });
                            
                            if(boolContinua)
                            {
                                var arrayInformacionNuevo = [];
                                Ext.get(winResumenReservaHousing.getId()).mask("Guardando las Máquinas Virtuales...");
                                //Guardar Informacion de maquinas virtuales
                                arrayInformacionNuevo = arrayInformacion.filter(elemento => elemento.esNuevo);
                                Ext.Ajax.request({
                                    url: urlGuardarMaquinasVirtuales,
                                    timeout: 600000,
                                    method: 'post',
                                    params: 
                                    { 
                                        idServicio      : data.idServicio,
                                        idVCenter       : idVCenter,
                                        data            : Ext.JSON.encode(arrayInformacionNuevo)
                                    },
                                    success: function(response)
                                    {
                                        Ext.get(winResumenReservaHousing.getId()).unmask();

                                        var objJson = Ext.JSON.decode(response.responseText);
                                        
                                        Ext.Msg.alert('Mensaje',objJson.strMensaje);
                                        
                                        inicializarVariablesGlobales();
                                        winResumenReservaHousing.close();
                                        winResumenReservaHousing.destroy();
                                    },
                                    failure: function(result)
                                    {
                                        Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                    }
                                });
                            }
                            else
                            {
                                Ext.Msg.alert('Alerta','No existen Máquinas Virtuales Nuevas a ser Guardadas');
                            }
                        }
                    },
                    {
                        text: '<i class="fa fa-window-close" aria-hidden="true"></i>&nbsp;Cerrar',
                        handler: function() 
                        {
                            winResumenReservaHousing.close();
                            winResumenReservaHousing.destroy();
                            inicializarVariablesGlobales();
                        }
                    }
                ]});

        var winResumenReservaHousing = Ext.widget('window', {
            id: 'winResumenReservaHousing',
            title: 'Creación de <b>Máquinas Virtuales</b>',
            layout: 'fit',
            resizable: true,
            modal: true,
            closable: true,
            width: 'auto',
            items: [formPanelResumenHousing]
        });

        arrayInformacion = [];
        winResumenReservaHousing.show();
        initProgressBar();

        ajaxConsultarMaquinasVirtuales();
    },
    failure: function(result)
    {
        Ext.Msg.alert('Error ','Error: ' + result.statusText);
    }
});
}

function initProgressBar()
{ 
    $( "#progressbar-storage" ).progressbar({	   
	    max:storageTotal
	  });
	  
	 $( "#progressbar-memoria" ).progressbar({	    
	    max:memoriaTotal
	  });
	  
	 $( "#progressbar-procesador" ).progressbar({	    
	    max:procesadorTotal
	  });
          
        $( "#progressbar-licencia" ).progressbar({	    
	    max:licenciaTotal
	  });
	  
	  
	 $( "#progressbar-storage" ).progressbar("option","value",storageTotal);
	 $( "#progressbar-memoria" ).progressbar("option","value",memoriaTotal);
	 $( "#progressbar-procesador" ).progressbar("option","value",procesadorTotal);
         $( "#progressbar-licencia" ).progressbar("option","value",licenciaTotal);
	 
	 $( "#progressbar-storage-label" ).text(storageTotal+" (GB)");
	 $( "#progressbar-memoria-label" ).text(memoriaTotal+" (GB)");
	 $( "#progressbar-procesador-label" ).text(procesadorTotal+" (Cores)");
         $( "#progressbar-licencia-label" ).text(licenciaTotal+" (Unidades)");
}

function initDroppable()
{   
    var $contenetMV = $("#contenetMV"), $trash = $("#trash");

    $("li", $contenetMV).draggable({
        cancel: "a.ui-icon", // clicking an icon won't initiate dragging
        revert: "invalid", // when not dropped, the item will revert back to its initial position
        containment: "document",
        helper: "clone",
        cursor: "move"
    });

    $trash.droppable({
        accept: "#contenetMV > li",
        classes:
            {
                "ui-droppable-active": "ui-state-highlight"
            },
        drop: function(event, ui)
        {
            eliminarMaquinaVirtual(ui.draggable);
        }
    });

    $("ul.contenetMV > li").on("click", function(event)
    {
        var $item = $(this),
            $target = $(event.target);

        if($target.is("a.ui-icon-trash"))
        {
            eliminarMaquinaVirtual($item);
        }

        return false;
    });

}
      
function deleteImage( $item  ) 
{
    $item.fadeOut(200, "linear", function()
    {        
        //recalcular capacidad contratada
        $(this).find("div").each(function() {

            var attrId = $(this).attr("id");
            if(attrId === 'resumen-storage')
            {
                storageTotal = parseInt(storageTotal) + parseInt($(this).text());
                actualizarTotal('storage', storageTotal);
            }
            else if(attrId === 'resumen-memoria')
            {
                memoriaTotal = parseInt(memoriaTotal) + parseInt($(this).text());
                actualizarTotal('memoria', memoriaTotal);
            }
            else if(attrId === 'resumen-procesador') {

                procesadorTotal = parseInt(procesadorTotal) + parseInt($(this).text());
                actualizarTotal('procesador', procesadorTotal);
            }else if(attrId === 'resumen-licencia') {

                licenciaTotal = parseInt(licenciaTotal) + parseInt($(this).text());
                actualizarTotal('licencia', licenciaTotal);
            }
        });

        $(this).find("h5").each(function() {
            var nombre = $(this).text();
            arrayInformacion = arrayInformacion.filter(function(elem) {
                return elem.nombre !== nombre;
            });
        });
        
        $(this).remove();                
    });
}

function actualizarTotal(tipo,cambio)
{
    var value = cambio;

    $("#progressbar-" + tipo).progressbar("option", "value", parseInt(value));

    var unidad = '(GB)';
    if(tipo === 'licencia')
    {
        unidad = '(Unidades)';
    }
    if(tipo === 'procesador')
    {
        unidad = '(Cores)';
    }
    $("#progressbar-" + tipo + "-label").text(value + " " + unidad);
}

function agregarBloqueHtmlMaquinaVirtual()
{
    var boolContinua = validarFormulario(false);

    if(boolContinua)
    {
        var $content   = $("#contenetMV");
        
        var nombre     = Ext.getCmp("txtNombreMaquina").getValue();
        var storage    = Ext.getCmp("txtStorage").getValue();
        var memoria    = Ext.getCmp("txtMemoria").getValue();
        var procesador = Ext.getCmp("txtProcesador").getValue();
        var licencia   = (Ext.getCmp("txtLicencia").getValue() == null ? 0 : Ext.getCmp("txtLicencia").getValue());
        var carpeta    = Ext.getCmp("txtNombreCarpeta").getValue();
        var tarjeta    = Ext.getCmp("txtTarjetaRed").getValue();
        var recursos   = Ext.getCmp("txtInfoRecursos").getValue();
    
        var html = getHtmlMaquinaVirtual(nombre,storage,memoria,procesador,licencia,'nuevo');
        $content.append(html);
    
        var storageCalculado    = parseInt(storageTotal)    - parseInt(storage);
        var memoriaCalculado    = parseInt(memoriaTotal)    - parseInt(memoria);
        var procesadorCalculado = parseInt(procesadorTotal) - parseInt(procesador);
        var licenciaCalculado   = parseInt(licenciaTotal)   - parseInt(licencia);
        
        storageTotal    = storageCalculado;
        memoriaTotal    = memoriaCalculado;
        procesadorTotal = procesadorCalculado;
        licenciaTotal   = licenciaCalculado;

        actualizarTotal('storage', storageTotal);
        actualizarTotal('memoria', memoriaTotal);
        actualizarTotal('procesador', procesadorTotal);
        actualizarTotal('licencia', licenciaTotal);

        var json = {};

        json['nombre']     = nombre;
        json['arrayRecursos']   = recursos;
        json['storage']    = storage;
        json['memoria']    = memoria;
        json['procesador'] = procesador;
        json['carpeta']    = carpeta;
        json['tarjeta']    = tarjeta;        
        json['idMaquina']  = 0;
        json['esNuevo']    = true;

        arrayInformacion.push(json);
        
        initDroppable();
        arrayRecursoTmp = [];//Se inicializa cuando ya se configura una maquina virtual
        return true;
    }
    else
    {
        return false;
    }
}

function getHtmlMaquinaVirtual(nombre,storage,memoria,procesador,licencia,tipo)
{
    var opcion = '';
    var clase  = '';
    
    if(tipo === 'existente')
    {
        opcion = '<a href="#" title="Editar Máquina" class="ui-icon ui-icon-pencil" onclick="editarMaquinaVirtual($(this));">Editar</a>' +
        '<a href="#" title="Factibilidad Rápida" class="ui-icon ui-icon-tag" onclick="factibilidadRapida($(this),ciudad);">Factibilidad Rápida</a>'  +
        '<a href="#" title="Solicitar recursos" class="ui-icon ui-icon-note" onclick="crearTareaAComercial($(this));">Solicitar recursos</a>';
        clase  = "li-mv-nueva";
    }
    else
    {
        opcion = '<a href="#" title="Consultar Máquina" class="ui-icon ui-icon-zoomin" onclick="consultarMaquinaVirtual($(this));">Ver Resumen</a>';
    }
    
    var html = '<li class="ui-widget-content ui-corner-tr li-mv ui-draggable ui-draggable-handle '+clase+' ">';
    html    += '<h5 class="ui-widget-header">' + nombre + '</h5>';
    html += '<div>' +
                '<table>' +
                    '<tr><td><input type="hidden" id="id_mv" value=""/></td></tr>'+
                    '<tr>' +
                         '<td class="lbl-resumen">Storage</td>' +
                         '<td>\n\
                              <div align="center" id="resumen-storage" class="content-resumen" style="background:#3194C4;">'+storage+' (GB)</div>\n\
                          </td>' +
                    '</tr>' +
                    '<tr><td><div style="height:5px;"></div></td></tr>'+
                    '<tr>' +
                            '<td class="lbl-resumen">Memoria</td>' +
                            '<td>\n\
                                <div align="center" id="resumen-memoria" class="content-resumen" style="background:#61A4D8;">'+memoria+' (GB)</div>\n\
                            </td>' +
                    '</tr>' +
                    '<tr><td><div style="height:5px;"></div></td></tr>'+
                    '<tr>' +
                            '<td class="lbl-resumen">Procesador</td>' +
                            '<td>\n\
                                <div align="center" id="resumen-procesador" class="content-resumen" style="background:#92CBC6;">'+procesador+' (Cores)</div>\n\
                            </td>' +
                    '</tr>' +
                    '<tr><td><div style="height:5px;"></div></td></tr>'+
                    '<tr>' +
                            '<td class="lbl-resumen">Licencia</td>' +
                            '<td>' + 
                                '<div align="center" id="resumen-licencia" class="content-resumen" style="background:#61A4D8;">'+licencia+' (Unidades)</div>'+
                            '</td>' +
                    '</tr>' +
                '</table>' +
            '</div><div style="height:5px;"></div>' +
            opcion +
            '<a href="#" title="Eliminar Máquina" class="ui-icon ui-icon-trash">Eliminar Máquina</a></li>';
        
    return html;
}



function agregarNuevaMaquinaVirtual()
{
    getAgregarEditarWindow('agregar',null);
}

function editarMaquinaVirtual($item)
{ 
    getAgregarEditarWindow('editar',$item);
}

function consultarMaquinaVirtual($item)
{
    $item = $item.parents('li');
    
    var jsonInfo               = {};       
    
    $item.find("h5").each(function() 
    {       
        var nombre = $(this).text();
        
        $.each(arrayInformacion, function(key, value) {
            if(value.nombre === nombre)
            {
                jsonInfo = value;
                return false;
            }
        });
    });
        
    var textoboton = '<i class="fa fa-window-close-o" aria-hidden="true"></i>&nbsp;Cerrar';    
        
    var formPanelDatosConsultarMV = Ext.create('Ext.form.Panel', {
            buttonAlign: 'center',
            BodyPadding: 10,
            id:'formPanelDatosConsultarMV',
            width: 450,
            height: 400,
            bodyStyle: "background: white; padding: 5px; border: 0px none;",
            frame: true,
            items:
                [                    
                    {
                        xtype: 'fieldset',
                        title: 'Datos de la Máquina Virtual',
                        layout: {
                            tdAttrs: {style: 'padding: 5px;'},
                            type: 'table',
                            columns: 5,
                            pack: 'center'
                        },
                        items: [ 
                            {width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                fieldLabel: '<b>* Nombre Máquina</b>',
                                name: 'txtNombreMaquinac',
                                id: 'txtNombreMaquinac',
                                width:350,
                                value: jsonInfo.nombre,
                                readOnly: true,
                                fieldStyle:'color:#3194C4;font-weight:bold;'
                            },                            
                            {width: '10%', border: false},
                            {width: '10%', border: false},
                            {width: '10%', border: false},
                            //-----------------------------------------------------
                            {width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                fieldLabel: '<b>* Carpeta</b>',
                                name: 'txtNombreCarpetac',
                                id: 'txtNombreCarpetac',
                                value: jsonInfo.carpeta,                                
                                width:350,
                                readOnly: true,
                                fieldStyle:'color:#3194C4;font-weight:bold;'
                            },                            
                            {width: '10%', border: false},
                            {width: '10%', border: false},
                            {width: '10%', border: false},
                            //-----------------------------------------------------
                            {width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                fieldLabel: '<b>* Tarjeta de Red</b>',
                                name: 'txtTarjetaRedc',
                                id: 'txtTarjetaRedc',
                                value: jsonInfo.tarjeta,
                                width:350,
                                readOnly: true,
                                fieldStyle:'color:#3194C4;font-weight:bold;'
                            },                            
                            {width: '10%', border: false},
                            {width: '10%', border: false},
                            {width: '10%', border: false},
                            //-----------------------------------------------------
                            {width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                fieldLabel: '<b>Sistema Operativo</b>',
                                name: 'txtSoc',
                                id: 'txtSoc',
                                value: jsonInfo.soNombre,
                                width:350,
                                readOnly: true,                                
                                fieldStyle:'color:#3194C4;font-weight:bold;'
                            },                            
                            {width: '10%', border: false},
                            {width: '10%', border: false},
                            {width: '10%', border: false},
                            //-----------------------------------------------------
                            {width: '10%', border: false},
                            {
                                xtype: 'numberfield',
                                fieldLabel: '<i class="fa fa-hashtag" aria-hidden="true"></i>&nbsp;<b>Storage</b>',
                                name: 'txtStoragec',
                                id: 'txtStoragec',
                                value: jsonInfo.storage,
                                fieldStyle:'color:#3194C4;font-weight:bold;',
                                allowDecimals:   false,
                                allowNegative:   false,
                                hideTrigger:true,
                                width:350,
                                readOnly:true
                            },                                                        
                            {width: '10%', border: false},
                            {width: '10%', border: false},//imgShowRecursos('DISCO'),
                            {width: '10%', border: false},
                            //-----------------------------------------------------
                            {width: '10%', border: false},
                            {
                                xtype: 'numberfield',
                                fieldLabel: '<i class="fa fa-hashtag" aria-hidden="true"></i>&nbsp;<b>Memoria</b>',
                                name: 'txtMemoriac',
                                id: 'txtMemoriac',
                                value: jsonInfo.memoria,
                                allowDecimals:   false,
                                allowNegative:   false,
                                hideTrigger:true,
                                width:350,
                                readOnly: true,
                                fieldStyle:'color:#3194C4;font-weight:bold;'
                            },                                                        
                            {width: '10%', border: false},
                            {width: '10%', border: false},//imgShowRecursos('MEMORIA RAM'),
                            {width: '10%', border: false},
                            //-----------------------------------------------------
                            {width: '10%', border: false},
                            {
                                xtype: 'numberfield',
                                fieldLabel: '<i class="fa fa-hashtag" aria-hidden="true"></i>&nbsp;<b>Procesadores</b>',
                                name: 'txtProcesadorc',
                                id: 'txtProcesadorc',
                                allowDecimals:   false,
                                allowNegative:   false,
                                hideTrigger:true,
                                value: jsonInfo.procesador,
                                width:350,
                                readOnly: true,
                                fieldStyle:'color:#3194C4;font-weight:bold;'
                            },                                                        
                            {width: '10%', border: false},
                            {width: '10%', border: false},//imgShowRecursos('PROCESADOR'),
                            {width: '10%', border: false}
                            //-----------------------------------------------------
                        ]
                }                              
            ],
            buttons: [
                {
                    text: textoboton,
                    handler: function() 
                    {
                        winConsultarMV.close();
                        winConsultarMV.destroy();      
                        formPanelDatosConsultarMV.destroy();
                    }
                }
            ]});

        var winConsultarMV = Ext.widget('window', {
            id: 'winConultarMV',
            title: 'Consultar Nueva Máquina Virtual',
            layout: 'fit',
            resizable: true,
            modal: true,
            closable: true,
            width: 'auto',
            items: [formPanelDatosConsultarMV]
        });
        
        winConsultarMV.show();
}

function mostrarMaquinasVirtuales(arrayJson)
{
    arrayResumenGeneralRecursos = [];

    $.each(arrayRecursos.arrayDetalleDisco, function(i, item)
    {
        var json = {};                
        json['idRecurso'] = item.idRecurso;
        json['tipo']      = 'DISCO';
        json['total']     = item.valor;
        json['disponible']= item.valor;
        json['usado']     = 0;
        json['nombreRecurso']     = item.nombreRecurso;
        arrayResumenGeneralRecursos.push(json);
    });

    $.each(arrayRecursos.arrayDetalleProcesador, function(i, item)
    {
        var json = {};
        json['idRecurso'] = item.idRecurso;
        json['tipo']      = 'PROCESADOR';
        json['total']     = item.valor;
        json['disponible']= item.valor;
        json['usado']     = 0;
        json['nombreRecurso']     = item.nombreRecurso;
        arrayResumenGeneralRecursos.push(json);
    });

    $.each(arrayRecursos.arrayDetalleMemoria, function(i, item)
    {
        var json = {};
        json['idRecurso'] = item.idRecurso;
        json['tipo']      = 'MEMORIA RAM';
        json['total']     = item.valor;
        json['disponible']= item.valor;
        json['usado']     = 0;
        json['nombreRecurso']     = item.nombreRecurso;
        arrayResumenGeneralRecursos.push(json);
    });

    $.each(arrayRecursos.arrayDetalleLicencia, function(i, item)
    {
        var json = {};
        json['idRecurso'] = item.idRecurso;
        json['tipo']      = 'LICENCIA';
        json['total']     = item.valor ? item.valor : 0;
        json['disponible']= item.valor ? item.valor : 0;
        json['usado']     = 0;
        json['nombreRecurso']     = item.nombreRecurso;
        arrayResumenGeneralRecursos.push(json);
    });

    var $content   = $("#contenetMV");
    
    $.each(arrayJson, function(key, value) 
    {    
        var arrayInfoGeneral = value.arrayInfoGeneral;
        
        //Calcular total de recursos
        var disco      = calcularTotalRecursosMaquinasVirtuales(value.arrayDetalleDisco);
        var memoria    = calcularTotalRecursosMaquinasVirtuales(value.arrayDetalleMemoria);
        var procesador = calcularTotalRecursosMaquinasVirtuales(value.arrayDetalleProcesador);
        var licencia   = 0;
        if(value.arrayDetalleLicencia.length !== 0){
            licencia   = calcularTotalRecursosMaquinasVirtuales(value.arrayDetalleLicencia);
        }

        var json           = {};
        json['nombre']     = arrayInfoGeneral.nombreElemento;
        json['storage']    = disco;
        json['memoria']    = memoria;
        json['procesador'] = procesador;
        json['licencia']   = licencia;
        json['so']         = arrayInfoGeneral.idSistemaOperativo;
        json['carpeta']    = arrayInfoGeneral.carpeta;
        json['tarjeta']    = arrayInfoGeneral.tarjetaRed;
        json['idMaquina']  = arrayInfoGeneral.idElemento;        
        json['soNombre']   = arrayInfoGeneral.sistemaOperativo;
        json['arrayRecursos'] = value;
        arrayInformacion.push(json);
        
        var html = getHtmlMaquinaVirtual(arrayInfoGeneral.nombreElemento,disco,memoria,procesador,licencia,'existente');
        
        var storageCalculado    = parseInt(storageTotal)    - parseInt(disco);
        var memoriaCalculado    = parseInt(memoriaTotal)    - parseInt(memoria);
        var procesadorCalculado = parseInt(procesadorTotal) - parseInt(procesador);
        var licenciaCalculado   = parseInt(licenciaTotal)   - parseInt(licencia);

        storageTotal    = storageCalculado;
        memoriaTotal    = memoriaCalculado;
        procesadorTotal = procesadorCalculado;
        licenciaTotal = licenciaCalculado;

        actualizarTotal('storage', storageTotal);
        actualizarTotal('memoria', memoriaTotal);
        actualizarTotal('procesador', procesadorTotal);
        actualizarTotal('licencia', licenciaTotal);
        
        $content.append(html);
        
        initDroppable();
    });
}

function eliminarMaquinaVirtual($item)
{
    var jsonInfo               = {};
    var boolEsMaquinaExistente = false;
    
    $item.find("h5").each(function() 
    {       
        var nombre = $(this).text();
        $.each(arrayInformacion, function(key, value) {
            if(value.nombre === nombre)
            {
                jsonInfo = value;
                return false;
            }
        });
    });
    
    //Se valida si la maquina virtual es nueva o ya fue creada previamente
    if(jsonInfo.idMaquina !== 0)
    {
        boolEsMaquinaExistente = true;
    }
    
    if(boolEsMaquinaExistente)
    {
        Ext.Msg.alert('Mensaje', "Seguro que desea eliminar la Máquina Virtual <b>"+jsonInfo.nombre+"</b>", function(btn) {
            if (btn == 'ok') 
            {
                //Request para realizar eliminacion de maquina virtual
                ajaxEditarEliminarMaquinaVirtual('eliminar',jsonInfo,null,$item);
            }
        });
    }
    else//Si aun no ha sido creada se elimina la seleccion en caliente
    {
        deleteImage($item);
        eliminarRecursosDeMaquinaVirtual(jsonInfo, 'nuevo');
    }
}

function ajaxConsultarMaquinasVirtuales()
{   
    Ext.get("contentMaquinasVirtuales").mask('Obteniendo Información de Máquinas Virtuales...');
    //Buscar Maquinas Virtuales
    Ext.Ajax.request({
            url: urlGetInformacionGeneralHosting,
            method: 'post',
            timeout:600000,
            params: 
            { 
                idServicio      : idServicio,
                tipoInformacion : 'MAQUINAS-VIRTUALES'
            },
            success: function(response)
            {
                Ext.get("contentMaquinasVirtuales").unmask();

                var objJson = Ext.JSON.decode(response.responseText);

                mostrarMaquinasVirtuales(objJson);
            },
            failure: function(result)
            {
                Ext.Msg.alert('Error ','Error: ' + result.statusText);
            }
        });
}

function ajaxEditarEliminarMaquinaVirtual(tipo,json,jsonActual,$item)
{    
    var msg     = '';
    var idPanel = '';
    
    if(tipo === 'editar')
    {
        msg     = 'Editando Información de Máquina Virtual...';
        idPanel = 'panelAgregarMv';
    }
    else
    {
        msg     = 'Eliminando Máquina Virtual...';
        idPanel = 'contentMaquinasVirtuales';
    }    
    
    Ext.get(idPanel).mask(msg);
    

    Ext.Ajax.request({
        url: urlActualizarMaquinasVirtuales,
            method: 'post',
            timeout: 600000,
            params: 
            { 
                idElemento  : json.idMaquina,
                tipoAccion  : tipo,
                data        : Ext.JSON.encode(json),
                idServicio  : idServicio,
                dataAnterior: Ext.JSON.encode(jsonActual),
                dataEliminados: Ext.JSON.encode(arrayRecursoEliminados)
            },
            success: function(response)
            {
                Ext.get(idPanel).unmask();

                var objJson = Ext.JSON.decode(response.responseText);

                if(objJson.strStatus === 'OK')
                {
                    if(tipo === 'editar')
                    {
                        limpiarContenedorMaquinasVirtuales();                                                   
                    }
                    else
                    {                        
                        deleteImage( $item  );
                        
                        //Eliminar recursos del listado generico de recursos a ser validados
                        eliminarRecursosDeMaquinaVirtual(json, 'existente');
                    }              
                    
                    Ext.Msg.alert('Mensaje', objJson.strMensaje, function(btn) {
                        if (btn == 'ok') 
                        {    
                            inicializarVariablesGlobales();
                            
                            if(tipo === 'editar')
                            {                                
                                Ext.getCmp('winCrearMV').close();                                
                                ajaxConsultarMaquinasVirtuales();
                            }
                            
                            return true;
                        }
                        else
                        {
                            return false;
                        }
                    });
                }
                else
                {
                    Ext.Msg.alert('Mensaje', objJson.strMensaje);
                    return false;
                }
            },
            failure: function(result)
            {
                Ext.Msg.alert('Error ','Error: ' + result.statusText);
            }
        });
}

function validarFormulario(isEdicion,jsonAnterior)
{ 
    var nombre     = '';
    var storage    = '';
    var memoria    = '';
    var procesador = '';
    var carpeta    = '';
    var tarjeta    = '';    
            
    nombre     = Ext.getCmp("txtNombreMaquina").getValue();
    storage    = Ext.getCmp("txtStorage").getValue();
    memoria    = Ext.getCmp("txtMemoria").getValue();
    procesador = Ext.getCmp("txtProcesador").getValue();
    carpeta    = Ext.getCmp("txtNombreCarpeta").getValue();
    tarjeta    = Ext.getCmp("txtTarjetaRed").getValue();
   
    if(storage <= 0)
    {
        Ext.Msg.alert('Alerta ', "Debe ingresar un valor para el <b>Disco</b> mayor a 0");        
        boolContinua = false;
    }
    else if(memoria <= 0)
    {
        Ext.Msg.alert('Alerta ', "Debe ingresar un valor para <b>Memoria Ram</b> mayor a 0");        
        boolContinua = false;
    }
    else if(procesador <= 0)
    {
        Ext.Msg.alert('Alerta ', "Debe ingresar una cantidad para <b>Procesador</b> mayor a 0");        
        boolContinua = false;
    }
    else
    {
        var storageCalculado    = 0;
        var memoriaCalculado    = 0;
        var procesadorCalculado = 0;

        if(isEdicion)//En la edicion agregamos el valor existente y luego restamos el valor nuevo para validar que no sobrepase la capacidad
        {
            storageCalculado    = parseInt(storageTotal)    + jsonAnterior.storage     - parseInt(storage);
            memoriaCalculado    = parseInt(memoriaTotal)    + jsonAnterior.memoria     - parseInt(memoria);
            procesadorCalculado = parseInt(procesadorTotal) + jsonAnterior.procesador  - parseInt(procesador);
        }
        else
        {
            storageCalculado    = parseInt(storageTotal)    - parseInt(storage);
            memoriaCalculado    = parseInt(memoriaTotal)    - parseInt(memoria);
            procesadorCalculado = parseInt(procesadorTotal) - parseInt(procesador);
        }        

        var boolContinua = true;
        //Validar nombres distintos        

        if(Ext.isEmpty(nombre))
        {
            Ext.Msg.alert('Alerta ', "Debe escribir el nombre de la nueva Máquina Virtual");        
            boolContinua = false;
        }
        else
        {
            if(!isEdicion)
            {
                $.each(arrayInformacion, function(key, value) {
                    if(value.nombre === nombre)
                    {
                        Ext.Msg.alert('Alerta ', "Ya existe una Máquina Virtual con el mismo nombre");            
                        boolContinua = false;
                        return false;
                    }
                });  
            }
        }       

        if(Ext.isEmpty(tarjeta))
        {
            Ext.Msg.alert('Alerta ', "Debe colocar el número de la Tarjeta de Red de la Máquina Virtual");        
            boolContinua = false;
        }

        if(Ext.isEmpty(carpeta))
        {
            Ext.Msg.alert('Alerta ', "Debe escoger el nombre de la carpeta de alojamiento de la Máquina Virtual");        
            boolContinua = false;
        }

        if(Ext.isEmpty(storage))
        {
            Ext.Msg.alert('Alerta ', "Debe ingresar el valor del Storage");        
            boolContinua = false;
        }
        else if(Ext.isEmpty(memoria))
        {
            Ext.Msg.alert('Alerta ', "Debe ingresar el valor de la Memoria");        
            boolContinua = false;
        }
        else if(Ext.isEmpty(procesador))
        {
            Ext.Msg.alert('Alerta ', "Debe ingresar el valor de la cantidad de Procesadores");        
            boolContinua = false;
        }
        else
        {
            if(storageCalculado < 0)
            {
                Ext.Msg.alert('Alerta ', "No puede asignar más Storage del contratado");        
                boolContinua = false;
            }
            else if(memoriaCalculado < 0)
            {
                Ext.Msg.alert('Alerta ', "No puede asignar mas Memoria de la contratada");        
                boolContinua = false;
            }
            else if(procesadorCalculado < 0)
            {
                Ext.Msg.alert('Alerta ', "No puede asignar mas Procesador del contratado");
                boolContinua = false;
            }
        }
    }
    
    return boolContinua;
}