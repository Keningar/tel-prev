
function verInformacionHousing(data)
{
    $.ajax({
        type: "POST",
        url: url_getInformacionRacksDC,
        data:
            {
                'nombreCanton': data.nombreCanton
            },
        beforeSend: function()
        {
            Ext.get(document.body).mask('Cargando información de Filas y Racks');
        },
        complete: function()
        {
            Ext.get(document.body).unmask();
        },
        success: function(dataRack)
        {
            if(dataRack.length === 0)
            {
                Ext.Msg.show({
                    title: 'Alerta',
                    msg: 'No existe Información de Racks para el Data Center Requerido',
                    buttons: Ext.Msg.OK
                });
            }
            else
            {
                var contentHtmlIT = Ext.create('Ext.Component', {
                    html: '<div id="content-asignacion-rack" class="content-asignacion-rack"></div><br/>\n\
                           <div align="center"><table id="table-content-resumen-asignado" class="table-resumen-class"\n\
                                 width="80%">\n\
                                <tr>\n\
                                    <th>Fila</th>\n\
                                    <th>Nombre Rack</th>\n\
                                    <th>Reservados</th>\n\
                                </tr>\n\
                            </table></div>',
                    padding: 1,
                    layout: 'anchor',
                    style: {color: 'red', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
                });
            
                var formPanelResumenHousing = Ext.create('Ext.form.Panel', {
                    buttonAlign: 'center',
                    BodyPadding: 10,
                    width:800,
                    height:700,
                    bodyStyle: "background: white; padding: 5px; border: 0px none;",
                    frame: true,
                    items: 
                    [
                        contentHtmlIT
                    ],
                    buttons: [
                        {
                            text: 'Cerrar',
                            handler: function() {
                                winResumenReservaHousing.close();
                                winResumenReservaHousing.destroy();
                            }
                        }
                    ]});

                var winResumenReservaHousing = Ext.widget('window', {
                    id:'winResumenReservaHousing',
                    title: 'Información de Reserva de Espacio DATA CENTER',
                    layout: 'fit',
                    resizable: true,
                    modal: true,
                    closable: true,
                    width:'auto',
                    items: [formPanelResumenHousing]
                });
                                
                //Cargar la informacion reservada
                Ext.get("grid").mask('Consultando Datos de Espacio Reservado...');

                Ext.Ajax.request({
                    url: urlGetInformacionEspacioHousing,
                    method: 'post',
                    timeout: 400000,
                    params:
                        {
                            idServicioAlquiler: data.idServicio
                        },
                    success: function(response)
                    {
                        Ext.get("grid").unmask();

                        var json = Ext.JSON.decode(response.responseText);
                
                        winResumenReservaHousing.show();    
                        
                        //Renderizar información de rack de acuerdo a la fila seleccionada                
                        drawGrid($("#content-asignacion-rack"), 'mostrarResumen', dataRack, data.nombreCanton);
                        
                        $.each(json.encontrados, function(i, item) 
                        {
                            var html = "<tr style='color:black;'><td>" + item.nombreFila + "</td>";
                            html    += "<td>" + item.nombreRack + "</td>";
                            html    += "<td>" + item.reservados + "</td></tr>";
                            
                            $("#table-content-resumen-asignado").append(html);

                            $("#content-asignacion-rack").find("td").each(function()
                            {
                                var id = $(this).attr("id");

                                if(id === item.nombreFila)
                                {
                                    $(this).removeClass("fila-ocupada");
                                    $(this).addClass("fila-habilitada");
                                    
                                    $(this).bind("click",function()
                                    {
                                        //Se carga la informacion del Rack en cuestion
                                        $.ajax({
                                            type: "POST",
                                            url: urlGetUnidadesRack,
                                            data:
                                                {
                                                    'idRack': item.idRack
                                                },
                                            beforeSend: function()
                                            {
                                                Ext.get("winResumenReservaHousing").mask('Consulta Información del Rack');
                                            },
                                            complete: function()
                                            {
                                                Ext.get("winResumenReservaHousing").unmask();
                                            },
                                            success: function(data)
                                            {
                                                showRack(data,item);
                                            }
                                        });
                                    });
                                }
                            });
                        });
                    },
                    failure: function(result) {
                        Ext.MessageBox.show({
                            title: 'Error',
                            msg: result.responseText,
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.ERROR
                        });
                    }
                });
            }
        }
    });
}

function showRack(dataUnidadesRack,dataUnidadesRackReservadas)
{
    var contentHtmlRack = Ext.create('Ext.Component', {
        html: '<div id="panel-rack-resumen" align="center"></div>',
        padding: 1,
        layout: 'anchor'
    });
    
    var formPanelResumenRack = Ext.create('Ext.form.Panel', {
        buttonAlign: 'center',
        BodyPadding: 10,
        width: 250,
        height: 600,
        bodyStyle: "background: white; padding: 5px; border: 0px none;",
        frame: true,
        items:
            [
                contentHtmlRack
            ],
        buttons: [
            {
                text: 'Cerrar',
                handler: function() {
                    winResumenRack.close();
                    winResumenRack.destroy();
                }
            }
        ]});

    var winResumenRack = Ext.widget('window', {
        id: 'winResumenRack',
        title: 'Unidades de Racks Ocupadas',
        layout: 'fit',
        resizable: true,
        modal: true,
        closable: true,
        width: 'auto',
        items: [formPanelResumenRack]
    });
    
    var table = '<table id="table-rack" border=2 width="200px;">'+
		        '<tr>'+
			       '<td class="borde" ></td>'+
			       '<td height="5" align=center class="cabecera"><span class="titulo">'+dataUnidadesRackReservadas.nombreRack+'</span></td>'+
			       '<td class="borde" ></td>'+
		        '</tr>'+
		        '<tr>'+
			       '<td colspan="3" class="borde"></td>'+
		        '</tr>';
    //Se consulta los valores de inicio y fin para dibujar el rack
    var arrayData =dataUnidadesRack;       
    var json={};
    var i=0;
    var estado='';
    var clase  = '';
    switch(arrayData.rangoFinal) 
    {
        case '42':
		      
                    //Se grafica los rack ocupados y disponibles de acuerdo a la data mapeada y generada        
                    i=42;
                    while (i > 0) 
                    {
                        json   = getInformacionUdRack(i,dataUnidadesRack.arrayUnidadesRack);
                        clase  = '';

                        clase = "desocupado";        

                        $.each(dataUnidadesRackReservadas.unidades, function(i, item) 
                        {
                            if(item.idUdRack == json.id)
                            {
                                clase = "ocupado";
                                return false;
                            }
                        });

                            table += ' <tr>'+
                                    '<td width="10%" class="borde" style="height:10px;">'+i+'</td>'+
                                    '<td id="'+json.id+'" width="90%" class="'+clase+'" style="height:10px;" input-val="'+json.nombre+'"></td>'+
                                    '<td width="10%" class="borde" style="height:10px;">&nbsp;&nbsp;</td>'+
                                  '</tr>';	    	    	    
                            i--; 
                        }
                        break;
        case '1':
                    //Se grafica los rack ocupados y disponibles de acuerdo a la data mapeada y generada        
                    i=1;
                    while (i < 43)  
                    {
                        json   = getInformacionUdRack(i,dataUnidadesRack.arrayUnidadesRack);
                        clase  = '';

                        clase = "desocupado";        

                        $.each(dataUnidadesRackReservadas.unidades, function(i, item) 
                        {
                            if(item.idUdRack == json.id)
                            {
                                clase = "ocupado";
                                return false;
                            }
                        });

                            table += ' <tr>'+
                                    '<td width="10%" class="borde" style="height:10px;">'+i+'</td>'+
                                    '<td id="'+json.id+'" width="90%" class="'+clase+'" style="height:10px;" input-val="'+json.nombre+'"></td>'+
                                    '<td width="10%" class="borde" style="height:10px;">&nbsp;&nbsp;</td>'+
                                  '</tr>';	    	    	    
                            i++; 
                    }
                        break;
        default: 
                     //Si la marca del rack seleccionado no está parametrizada en admi_parámetro con rangos mostrará error
                     Ext.Msg.alert('Mensaje', "Rango final para marca de rack no definido");
                     return false;
                     break;
    }
	
	table += '<tr>'+
		        '<td colspan="3" class="borde" style="height: 8px;"></td>'+
		     '</tr>'+
		     '<tr>'+
		        '<td colspan="3" class="borde"></td>'+
		     '</tr>'+
		   '</table>';
       
     winResumenRack.show();
           
    $("#panel-rack-resumen").html(table);
    
   
}         
function consultarLoginCam(idPersonaRol,idProducto){
    var valor= false;
    Ext.Ajax.request({
        url: url_getCamarasEm,
        method: 'get',
        async: false,
        params: {idPersonaRol: idPersonaRol, idProducto: idProducto},
            success: function(response) {
                respuestaInt = Ext.JSON.decode(response.responseText);
                if(respuestaInt>0)
                {
                    valor=true
                }
            }
    });

   return valor;
}


function consultarInformacion(data, caracteristica)
{
    var valor = [];
    Ext.Ajax.request({
        url: url_getCaracteristicasServicio,
        method: 'get',
        async: false,
        params: {idServicio: data, estado: 'Todos'},
            success: function(response) {
                var json = Ext.JSON.decode(response.responseText);
                
                total=parseInt(json.total);
                
                for (var i=0; i < total; i++ )
                {
                    if(json.encontrados[i].descripcionCaracteristica == caracteristica)
                    {
                         valor["valor"] = json.encontrados[i].valor;
                         valor["descripcionCaracteristica"] = json.encontrados[i].descripcionCaracteristica;
                         valor["idServicioProdCaract"] = json.encontrados[i].idServicioProdCaract;
                         valor["estado"] = json.encontrados[i].estado;
                    }
                }
            }
    });

   return valor;

}

function consultarInformacionSerie(data)
{
    var total= 0;
    var existemac = false;
    Ext.Ajax.request({
        url: url_getCaracteristicasServicio,
        method: 'get',
        async: false,
        params: {idServicio: data, estado: 'Todos'},
            success: function(response) {
                var json = Ext.JSON.decode(response.responseText);
                
                total=parseInt(json.total);
                
                for (var i=0; i < total; i++ )
                {
                    if(json.encontrados[i].descripcionCaracteristica == 'SERIE_EQUIPO_PTZ')
                    {
                        existemac = true;
                    }
                }
            }
    });

   return existemac;

}

function verInformacionCarac(data)
{   
    var arraycarac = [];

    Ext.tip.QuickTipManager.init();
    Ext.Ajax.request({
        url: url_getCaracteristicasServicio,
        method: 'get',
        async: false,
        params: {idServicio: data.idServicio, estado: 'Todos'},
            success: function(response) {
                var json = Ext.JSON.decode(response.responseText);
               
                total=parseInt(json.total);
                
                for (var i=0; i < total; i++ )
                {
                    arraycarac[json.encontrados[i].descripcionCaracteristica] = json.encontrados[i].valor;
                }
            }
    });
   
       
    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 95,
            msgTarget: 'side',
            bodyStyle: 'padding:20px'
        },
        items: [
            {
                xtype: 'fieldset',
                title: 'Datos del Servicio',
                defaultType: 'textfield',
                defaults: {
                    width: 585
                },
                items: [
                    {
                        xtype: 'container',
                        layout: {
                            type: 'table',
                            columns: 2
                        },
                        items: [
                            {
                                xtype: 'textfield',
                                fieldLabel: 'Serie',
                                value: arraycarac["SERIE_EQUIPO_PTZ"],
                                readOnly: true,
                                fieldCls: 'details-disabled',
                                fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`,
                                width: '50%'
                            },

                            {
                                xtype: 'textfield',
                                fieldLabel: 'Mac',
                                value: arraycarac["MAC"],
                                readOnly: true,
                                fieldCls: 'details-disabled',
                                fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`,
                                width: '50%'
                            },
                            {
                                xtype: 'textfield',
                                fieldLabel: 'Id Cámara',
                                value: arraycarac["ID_CAMARA"],
                                readOnly: true,
                                fieldCls: 'details-disabled',
                                fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`,
                                width: '50%'
                            }
                        ]
                    }

                ]
            },            
            {
                xtype: 'fieldset',
                title: 'Datos del Ubicación',
                defaultType: 'textfield',
                defaults: {
                    width: 585
                },
                items: [
                    {
                        xtype: 'container',
                        autoWidth: true,
                        layout: {
                            type: 'table',
                            columns: 2
                        },
                        items: [
                                                        {
                                xtype: 'textfield',
                                fieldLabel: 'Cantón',
                                value: data.puntoCanton,
                                readOnly: true,
                                fieldCls: 'details-disabled',
                                fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`,
                                width: '50%'
                            },
                            {
                                xtype: 'textfield',
                                fieldLabel: 'Dirección',
                                value: data.puntoDireccion,
                                readOnly: true,
                                fieldCls: 'details-disabled',
                                fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`,
                                width: '50%'
                            },
                            {
                                xtype: 'textfield',
                                fieldLabel: 'Latitud',
                                value: data.puntoLatitud,
                                readOnly: true,
                                fieldCls: 'details-disabled',
                                fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`,
                                width: '50%'
                            },
                            {
                                xtype: 'textfield',
                                fieldLabel: 'Longitud',
                                value: data.puntoLongitud,
                                readOnly: true,
                                fieldCls: 'details-disabled',
                                fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`,
                                width: '50%'
                            },
                            {
                                xtype: 'textfield',
                                fieldLabel: 'Altura',
                                value: arraycarac["ALTURA_POSTE"],
                                readOnly: true,
                                fieldCls: 'details-disabled',
                                fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`,
                                width: '50%'
                            },
                            {
                                xtype: 'textfield',
                                fieldLabel: 'Tipo',
                                value: arraycarac["TIPO_POSTE"],
                                readOnly: true,
                                fieldCls: 'details-disabled',
                                fieldStyle: `background-color: ${objFieldStyle.backgroundColor}; background-image: ${objFieldStyle.backgrodunImage};`,
                                width: '50%'
                            }
                            
                        ]
                    }

                ]
            }
        ],
        buttons: [{
                text: 'Cerrar',
                handler: function() {
                    win.destroy();
                }
            }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Información técnica',
        modal: true,
        width: 630,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
    
}

function verInformacionServidoresAlquiler(data)
{        
    var storeServidores = new Ext.data.Store({
        pageSize: 14,
        total: 'total',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url:urlGetServidoresAlquiler,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            actionMethods: {
                create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
            },
            extraParams: {
                idServicio: data.idServicio
            }
        },
        fields:
            [
                {name: 'nombreRecurso', mapping: 'nombreRecurso'},
                {name: 'modelo', mapping: 'modelo'},
                {name: 'storage', mapping: 'storage'},
                {name: 'datastore', mapping: 'datastore'},
                {name: 'licenciamiento', mapping: 'licenciamiento'}
            ]
    });

    var gridAlquiler = Ext.create('Ext.grid.Panel', {
        width: 650,
        id:'gridAlquiler',
        height: 180,
        store: storeServidores,
        loadMask: true,
        frame: false,
        columns: [
            {
                id: 'nombreRecurso',
                header: 'Modelo',
                dataIndex: 'nombreRecurso',
                width: 200,
                sortable: true
            },
            {
                id: 'storage',
                header: 'Storage',
                dataIndex: 'storage',
                width: 80,
                renderer: function(val)
                {                    
                    return '<label style="color:green;font-weight: bold;">' + val + ' GB</label>';
                }
            },
            {
                id: 'datastore',
                header: 'DataStore',
                dataIndex: 'datastore',
                width: 100,
                renderer: function(val)
                {
                    return '<label style="color:green;font-weight: bold;">' + val + '</label>';
                }
            },
            {
                id: 'licenciamiento',
                header: 'Licenciamiento S.O.',
                dataIndex: 'licenciamiento',
                width: 230,
                renderer: function(val)
                {
                    return '<label style="color:green;font-weight: bold;">' + val + '</label>';
                }
            } 
        ]
    });        
    
    storeServidores.on('load', function(){
        //Your function here
        var grid = Ext.getCmp('gridAlquiler');
                
        var total = 0;
        
        for (var i = 0; i < grid.getStore().getCount(); i++)
        {                        
            total = total + parseInt(grid.getStore().getAt(i).data.storage);
        }
                
            $("#progressbar-storage").progressbar({
                max: total
            });

            $("#progressbar-storage").progressbar("option", "value", total);
            $("#progressbar-storage-label").text(total+" / "+total+"  (GB)");
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
                            '</td>'+
                      '</tr>'+
                    '</table>'+
                 '</div>',                
        style: {marginBottom: '15px', border: '0'}
    });

    var formPanelAlquiler = Ext.create('Ext.form.Panel', {
        buttonAlign: 'center',
        id:'formPanelAlquiler',
        BodyPadding: 10,        
        frame: true,
        items: [
            {
                xtype: 'fieldset',
                id:'recursos',
                title: '<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Información Recursos disponibles ( Storage )</b>',
                defaultType: 'textfield',
                layout: {                    
                    type: 'table',
                    columns: 3,
                    pack: 'center'
                },
                items: [
                    contentHtmlIT
                ]
            },
            //Factibilidad de storage para alquiler de servidores
            {
                xtype: 'fieldset',
                title: '<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Información Storage asignada a Servidores</b>',
                defaultType: 'textfield',
                layout: {                    
                    type: 'table',
                    columns: 3,
                    pack: 'center'
                },
                items: [
                    gridAlquiler
                ]
            }
        ],
        buttons: [                            
            {
                text: 'Cerrar',
                handler: function() {
                    winIngresoFactibilidad.close();
                    winIngresoFactibilidad.destroy();
                }
            }
        ]
    });

    winIngresoFactibilidad = Ext.widget('window', {
        title: 'Recursos asignados para Alquiler de Servidores',
        layout: 'fit',
        width:700,
        resizable: false,
        modal: true,
        closable: false,
        items: [formPanelAlquiler]
    });        

    winIngresoFactibilidad.show();
}

function verInformacionGeneralCuartoTi(idServicio)
{
    if(Ext.get("grid"))
    {
        Ext.get("grid").mask('Consultando Información del cuarto de Ti...');
    }
    else
    {
        Ext.MessageBox.wait("Consultando Información del cuarto de Ti...");
    }
    
    Ext.Ajax.request({
        url: urlGetInformacionGeneralCuartoTi,
        method: 'post',
        timeout: 400000,
        params   : 
        {
            idServicio  : idServicio
        },
        success: function(response)
        {
            if(Ext.get("grid"))
            {
                Ext.get("grid").unmask();
            }          
            else
            {
                Ext.MessageBox.hide();
            }

            var json = Ext.JSON.decode(response.responseText);
            
            winInformacionCuartoTi.show();    

            arrayPosicion        = json.arrayPosiciones;
            jsonFilasJaula       = json.arrayFilasJaulas;
            var ciudad           = json.ciudad;
            jsonFactibilidad     = Ext.JSON.decode(json.factibilidadHousing);//Factibilidad asignada al servicio

            json           = {};    
            json['posiciones'] = arrayPosicion;

            drawGrid($("#content-asignacion-rack"),'regularizacion',json,ciudad,jsonFactibilidad);
            
            $("#content-matriz-it").find("td").each(function()
            {
                var clase = $(this).attr("class");
                if(clase === 'fila-habilitada' || clase === 'fila-factible')
                {                                
                    $(this).bind("click",function(){
                        filaSeleccionada = $(this).attr("id");
                        if($(this).attr("class") === 'fila-habilitada')
                        {
                            $("#button-agregar").removeClass("ui-state-disabled").attr("disabled", false);
                            $("#input-nombre-rack").val("");
                            $("#input-descripcion-rack").val("");
                            $("#table-add-rack").hide();
                            $("#panel-agregar-rack").dialog("open");
                        }
                    });
                }
            });        

            $("#content-asignacion-rack").find("td").each(function(){
                var clase = $(this).attr("class")+"";
                if(clase.includes("fila-habilitada") || clase.includes("fila-factible"))
                {
                    $(this).bind("click",function()
                    {
                        verInformacionRackSimple($(this),json,'consulta');
                    });
                }
            });
            
        },
        failure: function(result) {
            Ext.MessageBox.show({
                title: 'Error',
                msg: result.responseText,
                buttons: Ext.MessageBox.OK,
                icon: Ext.MessageBox.ERROR
            });
        }
    });
                
    var contentHtmlIT = Ext.create('Ext.Component', {
        html: '<div id="content-asignacion-rack" class="content-asignacion-rack"></div><br/>'+
              '<table width="100%">'+
                '<tr>'+
                    '<td><b><i class="fa fa-map" aria-hidden="true"></i>&nbsp;Mapa de colores</b></td><td>&nbsp;</td>'+
                    '<td><i class="fa fa-square identificador-habil" aria-hidden="true"></i>'+
                         '&nbsp;Filas con Racks Seleccionables</td><td>&nbsp;</td>'+
                    '<td><i class="fa fa-square identificador-ocupado" aria-hidden="true"></i>'+
                         '&nbsp;Racks Ocupados ( Jaulas )</td><td>&nbsp;</td>'+
                    '<td><i class="fa fa-square identificador-no-habil" aria-hidden="true"></i>'+
                         '&nbsp;Filas no Habilitadas para Selecci&oacute;n o Desocupadas</td><td>&nbsp;</td>'+
                '</tr>'+
                '<tr>'+
                    '<td>&nbsp;</td>'+
                    '<td>&nbsp;</td>'+
                    '<td><i class="fa fa-square identificador-all" aria-hidden="true"></i>'+
                         '&nbsp;Racks con disponibilidad Completa</td><td>&nbsp;</td>'+
                    '<td><i class="fa fa-square identificador-midle" aria-hidden="true"></i>'+
                         '&nbsp;Racks con disponibilidad Media</td><td>&nbsp;</td>'+
                    '<td><i class="fa fa-square identificador-lleno" aria-hidden="true"></i>'+
                         '&nbsp;Racks sin disponibilidad</td><td>&nbsp;</td>'+
                '</tr>'+
                '<tr>'+
                    '<td>&nbsp;</td>'+
                    '<td>&nbsp;</td>'+
                    '<td><i class="fa fa-square identificador-factible" aria-hidden="true"></i>'+
                         '&nbsp;Racks con Factibilidad asignada</td><td>&nbsp;</td>'+                    
                '</tr>'+
            '</table>',       
        padding: 1,
        layout: 'anchor'
    });

    var formInformacionCuartoTi = Ext.create('Ext.form.Panel', {
        buttonAlign: 'center',
        BodyPadding: 10,
        width:1000,
        height:600,
        bodyStyle: "background: white; padding: 5px; border: 0px none;",
        frame: true,
        items: 
        [
            contentHtmlIT
        ],
        buttons: [
            {
                text: 'Cerrar',
                handler: function() 
                {
                    winInformacionCuartoTi.close();
                    winInformacionCuartoTi.destroy();
                    jsonFactibilidad = {};
                }
            }
        ]});
                
    var winInformacionCuartoTi = Ext.widget('window', {
        id:'winInformacionCuartoTi',
        title: '<b>Información General Cuarto Ti</b>',
        layout: 'fit',
        resizable: true,
        modal: true,
        closable: true,
        width:'auto',
        items: [formInformacionCuartoTi]
    });
}