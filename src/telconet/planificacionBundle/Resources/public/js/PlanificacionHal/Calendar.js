
function showCalendario(idCuadrilla,idZona,fechaIni,fechaFin, strEsConsulta)
{
    Ext.get('tabs').mask('Cargando Planificacion...');
    
    //Mostrar la informacion de cuadrillas planififcadas tipo HAL
    $.ajax({
        type: "POST",
        url: urlConsultarAgendaHAL,
        dataType: "json",
        data   : 
        {
          'idCuadrilla' : idCuadrilla,
          'idZona'      : idZona,
          'fechaIni'    : fechaIni,
          'fechaFin'    : fechaFin,
          'strEsConsulta': strEsConsulta
        },
        success: function (response)
        {
            Ext.get('tabs').unmask();
            var arrayCalendar = [];
            
            $.each(response, function (i, item) 
            {
                var json = {};
                json['id']             = item.idCab;
                json['idCuadrilla']    = item.idCuadrilla;
                json['title']          = item.cuadrilla;
                json['start']          = getDate(new Date(item.feTrabajo))+"T"+item.horaInicio;
                json['end']            = getDate(new Date(item.feTrabajo))+"T"+item.horaFin;
                json['idIntervalo']    = item.idIntervalo;
                json['intervalo']      = item.intervalo;
                json['idZona']         = item.idZona;
                json['horaInicio']     = item.horaInicio;
                json['horaFin']        = item.horaFin;
                json['fechaActual']    = item.feTrabajo;
                json['zona']           = item.zona;
                json['estado']         = item.estado;
                json['zonaPrestada']   = item.zonaPrestada;
                json['idzonaPrestada'] = item.idzonaPrestada;
                json['actividad']      = item.actividad;

                if(item.estado === 'Liberado')
                {                                    
                    json['color']      = '#adad85';
                    json['textColor']  = 'black';
                }

                if(parseInt(item.tareasAbiertas) > 0){
                    json['color']      = '#088A29';
                }

                arrayCalendar.push(json);
            });
            $('#calendar').fullCalendar('destroy');
            $('#calendar').fullCalendar('render');
            $('#calendar').fullCalendar({
                locale   : 'es',
                editable : false,
                eventClick: function (calEvent, jsEvent, view)
                {
                    //Solo se mostrará información sobre planificaciones diarias activas
                    if(calEvent.estado === 'Activo')
                    {
                        //Obtener los valores para mostrar el detalle de la Cuadrilla dentro de la agenda
                        verDetallePlanficacion(calEvent);
                    }
                    else
                    {
                        var fechaActual  = getDate(new Date());
                        var fechaTrabajo = getDate(new Date(calEvent.fechaActual));
                        fechaActual      = parseInt(new Date(fechaActual).getTime());
                        fechaTrabajo     = parseInt(new Date(fechaTrabajo).getTime());

                        if (fechaTrabajo < fechaActual)
                        {
                            Ext.Msg.alert('Alerta', 'No se pueden Activar fechas de trabajo menor a la fecha actual..!!');
                            return;
                        }

                        var zonaPrestada = '';
                        if (!Ext.isEmpty(calEvent.zonaPrestada)) {
                            zonaPrestada = '<tr>'+
                                            '<td><b>Zona Prestada:</b></td>'+
                                            '<th><label style="color:blue">'+calEvent.zonaPrestada+'</label></th>'+
                                           '</tr>';
                        }

                        var html = '<table style="width: 270px">\n\
                                                    <tr>\n\
                                                       <td><b>Fecha de Trabajo:</b></td>\n\
                                                       <th>'+calEvent.fechaActual.split(" ")[0]+'</th>\n\
                                                    </tr>\n\
                                                    <tr>\n\
                                                       <td><b>Intervalo de Trabajo:</b></td>\n\
                                                       <th>'+calEvent.intervalo+'</th>\n\
                                                    <tr>\n\
                                                        <td><b>Cuadrilla:</b></td>\n\
                                                        <th>'+calEvent.title+'</th>\n\
                                                    </tr>\n\
                                                    <tr>\n\
                                                        <td><b>Zona:</b></td>\n\
                                                        <th>'+calEvent.zona+'</th>\n\
                                                    </tr>'+
                                        zonaPrestada+
                                        '<tr>'+
                                            '<td><b>Estado:</b></td>'+
                                            '<th><b style="color:green">'+calEvent.estado+'</b></th>'+
                                        '</tr>'+
                                   '</table><br />';

                        Ext.Msg.confirm('Alerta', html+'¿Desea Activar el día de trabajo?',function(btn)
                        {
                            if(btn == 'yes')
                            {
                                Ext.get('tabs').mask("Activando día de trabajo...");

                                Ext.Ajax.request
                                ({
                                    url     :  urlEliminarLiberarPlanif,
                                    method  : 'post',
                                    timeout :  60000,
                                    params:
                                    {
                                        accion     : 'activarDiaTrabajo',
                                        idCabecera :  calEvent.id
                                    },
                                    success: function (response)
                                    {
                                        Ext.get('tabs').unmask();

                                        var objJson = Ext.JSON.decode(response.responseText);

                                        if(objJson.status === 'OK')
                                        {
                                            Ext.Msg.alert('Mensaje', objJson.mensaje, function(btn)
                                            {
                                                if (btn == 'ok')
                                                {
                                                    showCalendario('','','','','');
                                                }
                                            });
                                        }
                                        else
                                        {
                                            Ext.Msg.alert('Alerta', objJson.mensaje);
                                        }
                                    },
                                    failure: function (result)
                                    {
                                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                    }
                                });
                            }
                        });
                    }
                },
                eventMouseover: function(calEvent, jsEvent, view) 
                {                    
                    if(view.name==='month')
                    {
                            var height       = 85;
                            var zonaPrestada = '';
                            if (!Ext.isEmpty(calEvent.zonaPrestada)) {
                                height       = 93;
                                zonaPrestada = '<tr>'+
                                                '<td><b>Zona Prestada:</b></td>'+
                                                '<td>&nbsp;</td>'+
                                                '<td><label style="color:blue">'+calEvent.zonaPrestada+'</label></td>'+
                                               '</tr>';
                            }
                            var htmlActividad = '';
                            if (calEvent.actividad != null && calEvent.actividad != 'null')
                            {
                                htmlActividad = '<tr>'+
                                                '<td><b>Actividad:</b></td>'+
                                                '<td>&nbsp;</td>'+
                                                '<td><label>'+calEvent.actividad+'</label></td>'+
                                                '</tr>';
                            }
                            var html = '<table>'+
                                        '<tr>'+                                  
                                          '<td><b>Cuadrilla:</b></td>'+
                                          '<td>&nbsp;</td>'+
                                          '<td><label>'+calEvent.title+'</label></td>'+
                                        '</tr>'+
                                        '<tr>'+
                                          '<td><b>Zona:</b></td>'+
                                          '<td>&nbsp;</td>'+
                                          '<td><label>'+calEvent.zona+'</label></td>'+
                                        '</tr>'+
                                        zonaPrestada+
                                        '<tr>'+
                                          '<td><b>Intervalo:</b></td>'+
                                          '<td>&nbsp;</td>'+
                                          '<td><label>'+calEvent.horaInicio+' - '+calEvent.horaFin+'</label></td>'+
                                        '</tr>'+
                                        htmlActividad+
                                        '<tr>'+
                                          '<td><b>Estado:</b></td>'+
                                          '<td>&nbsp;</td>'+
                                          '<td><b style="color:green">'+calEvent.estado+'</b></td>'+
                                        '</tr>'+
                                        '</table>';

                        var tooltip  = '<div class="tooltipevent"\n\
                                            style="padding:1%;width:auto;height:'+height+'px;background:#ccc;position:absolute;z-index:10001;">'
                                            + html + 
                                       '</div>';

                        var $tooltip = $(tooltip).appendTo('body');

                        $(this).mouseover(function(e) {
                            $(this).css('z-index', 10000);
                            $tooltip.fadeIn('500');
                            $tooltip.fadeTo('10', 1.9);
                        }).mousemove(function(e) {
                            $tooltip.css('top', e.pageY + 10);
                            $tooltip.css('left', e.pageX + 20);
                        });
                    }
                },
                eventMouseout: function(calEvent, jsEvent, view) 
                {
                    $(this).css('z-index', 8);
                    $('.tooltipevent').remove();
                },
                header:
                {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay,listWeek'
                },
                defaultDate: new Date(),
                navLinks: true, // can click day/week names to navigate views
                eventLimit: true, // allow "more" link when too many events
                events: arrayCalendar,
                timeFormat: 'H:mm'
            });
        }
    });
}

function verDetallePlanficacion(object)
{
    var idCabecera  = object.id;
    var cuadrilla   = object.title;
    var zona        = object.zona;
    var intervalo   = object.horaInicio+" - "+object.horaFin;
    var fechaActual = object.fechaActual;
    var idZona      = object.idZona;
    var idIntervalo = object.idIntervalo;
    var idCuadrilla = object.idCuadrilla;
    var actividad   = object.actividad;

    var panelDetalle = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,       
        id:'panelDetalle',
        border: false,        
        buttonAlign: 'center',
        defaults: {
            anchor: '100%',
            labelWidth: 100            
        },
        width: 750,
        buttons: [
            {
                text: '<i class="fa fa-window-close-o" aria-hidden="true"></i>&nbsp;Cerrar',
                handler: function() 
                {
                    winDetallePlanificacion.close();
                }
            }            
        ],
        items: 
        [
            {
                xtype: 'fieldset',                
                title: '&nbsp;<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Información Básica</b>',
                items: 
                [
                    {
                        xtype: 'fieldcontainer',
                        items: 
                        [     
                            {
                                xtype: 'hiddenfield',                                
                                id: 'hdZona',
                                value: idZona
                            },
                            {
                                xtype: 'hiddenfield',                                
                                id: 'hdIntervalo',
                                value: idIntervalo
                            },
                            {
                                xtype: 'textfield',
                                width: 300,
                                fieldLabel: '<b>Cuadrilla</b>',
                                value: cuadrilla,
                                id:'cuadrillaDetalle',
                                readOnly: true
                            },                            
                            {
                                xtype: 'textfield',
                                width: 300,
                                fieldLabel: '<b>Zona</b>',
                                value: zona,
                                readOnly: true
                            },
                            {
                                xtype      : 'textfield',
                                width      : 300,
                                fieldLabel : '<b>Zona Prestada</b>',
                                value      : object.zonaPrestada,
                                hidden     : (object.zonaPrestada !== null ? false : true),
                                readOnly   : true
                            },
                            {
                                xtype: 'textfield',
                                width: 300,
                                fieldLabel: '<b>Intervalo</b>',
                                value: intervalo,
                                id:'intervaloPlanificacion',
                                readOnly: true
                            },
                            {width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                width: 300,
                                fieldLabel: '<b>Fecha Actual</b>',
                                id:'fechaActual',
                                value: fechaActual.split(" ")[0],
                                readOnly: true
                            },
                            {
                                xtype: 'textfield',
                                width: 300,
                                fieldLabel: '<b>Actividad</b>',
                                id:'actividad',
                                value: actividad,
                                readOnly: true
                            },
                            {
                                xtype      : 'displayfield',
                                fieldLabel : '<b>Nota</b>',
                                id         : 'notaPlanificacion',
                                name       : 'notaPlanificacion',
                                value      : '<b style="color:blue;">La planificación podría cambiar,\n\
                                                                     debido a que HAL siempre busca la mejor opción.</b>'
                            },
                            {
                                xtype          : 'button',
                                text           : '<label style="color:red;"><i class="fa fa-minus-square" aria-hidden="true"></i></label>\n\
                                                 &nbsp;<b>Liberar Día</b>',
                                align: 'center',
                                listeners: 
                                {
                                    click: function ()
                                    {
                                        liberarDiaTrabajo(idCabecera,idCuadrilla);
                                    }
                                }
                            },
                            {
                                xtype          : 'button',
                                text           : '<label style="color:teal;"><i class="fa fa-cog" aria-hidden="true"></i></label>\n\
                                                 &nbsp;<b>Reprogramar Cuadrilla</b>',
                                align: 'center',
                                listeners: 
                                {
                                    click: function ()
                                    {
                                        verReprogramacionCuadrilla(object);
                                    }
                                }
                            },
                            {
                                xtype          : 'button',
                                text           : '<label style="color:indianred;"><i class="fa fa-map-marker" aria-hidden="true"></i></label>\n\
                                                 &nbsp;<b>Cambiar Zona</b>',
                                align: 'center',
                                listeners: 
                                {
                                    click: function ()
                                    {
                                        verReprogramacionZona(object);
                                    }
                                }
                            },
                            {
                                xtype          : 'button',
                                text           : '<label style="color:lightblue;"><i class="fa fa-cutlery" aria-hidden="true"></i></label>\n\
                                                 &nbsp;<b>Solicitar Alimentación</b>',
                                style          : 'margin-left:5px;',
                                align          : 'center',
                                listeners: 
                                {
                                    click: function ()
                                    {
                                        liberarPermisoEvento(idCabecera, idCuadrilla, 'alimentacion', 'Alimentación');
                                    }
                                }
                            },
                            {
                                xtype          : 'button',
                                text           : '<label style="color:red;"><i class="fa fa-power-off" aria-hidden="true"></i></label>\n\
                                                 &nbsp;<b>Solicitar Fin de Jornada</b>',
                                style          : 'margin-left:5px;',
                                align          : 'center',
                                listeners: 
                                {
                                    click: function ()
                                    {
                                        liberarPermisoEvento(idCabecera, idCuadrilla, 'finJornada', 'Fin de Jornada');
                                    }
                                }
                            }

                        ]
                    }                                        
                ]
            },
            
            //Resumen de la planificacion diaria
            {
                xtype: 'fieldset',
                title: '&nbsp;<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Detalle del Día Planificado</b>',
                layout: 
                    {
                        type: 'table',
                        columns: 5,
                        pack: 'center'
                    },
                items: 
                [
                    {
                        xtype: 'fieldcontainer',
                        items: 
                        [
                            getGridDetallePlanificacion(idCabecera,idCuadrilla)
                        ]
                    }                                        
                ]
            }
        ]        
    });
    
    // Store que obtiene la jornada laboral de una cuadrilla
    storeJornadaLaboral = new Ext.data.Store(
    {
        pageSize :  1000,
        total    : 'total',
        proxy:
        {
            type : 'ajax',
            url  : urlGetJornadaDeTrabajo,
            reader:
            {
                type          : 'json',
                totalProperty : 'total'
            }
        },
        fields:
        [
            {name: 'jornadaLaboral' , mapping: 'jornadaLaboral'}
        ],
        listeners:
        {
            load: function(sender, node, records)
            {
                if (storeJornadaLaboral.getCount() > 0)
                {
                    Ext.getCmp('intervaloPlanificacion').setValue(node[0].data.jornadaLaboral);
                    panelDetalle.refresh;
                }
            }
        }
    });

    var winDetallePlanificacion = Ext.create('Ext.window.Window',
    {
        id:'winDetallePlanificacion',
        title: 'Detalle de Planificación del día',
        modal: true,
        width: 700,
        height: 725,
        closable: false,
        layout: 'fit',
        items: [panelDetalle]
    }).show();
}

function verReprogramacionCuadrilla(object)
{
    var idCabecera  = object.id;
    var cuadrilla   = object.title;
    var idCuadrilla = object.idCuadrilla;
    var fechaActual = object.fechaActual;
    var horaInicio  = object.horaInicio;
    var horaFin     = object.horaFin;
    //Cargar las cuadrillas en el combo de busqueda dentro de la planificacion
    var storeCuadrillasRep = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        autoLoad:true,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: urlConsultarCuadrillasRep,
            reader: {
                type: 'json',
                root: 'encontrados'
            },
            extraParams: {
                fecha      : fechaActual,
                horaInicio : horaInicio,
                horaFin    : horaFin
            }
        },
        fields:
            [
                {name: 'id', mapping: 'id'},
                {name: 'nombre', mapping: 'nombre'}
            ]
    });

    var comboCuadrillasReprogramacion = Ext.create('Ext.form.ComboBox', {
        id           : 'comboCuadrillasReprogramacion',
        store        : storeCuadrillasRep,
        displayField : 'nombre',
        valueField   : 'id',
        fieldLabel   : 'Nueva Cuadrilla',
        width        : 340,
        labelWidth   : 100,
        emptyText    : 'Seleccione nueva Cuadrilla',
        disabled     : false,
        hidden       : true,
        listeners: {
            select: {fn: function(combo, value) {
                var objDatoCuadrilla = value[0].data;

                if (Ext.getCmp('valorTipoReprogramacion').value =='A_CUADRILLA' && objDatoCuadrilla != null )
                {
                    Ext.getCmp('btnGuardarReprogramacion').setDisabled(false);
                }
                }},
            change: {fn: function(combo, newValue, oldValue) {
                if (Ext.getCmp('valorTipoReprogramacion').value =='A_CUADRILLA' && (newValue == null || newValue == 'null' || newValue == ''))
                {
                    Ext.getCmp('btnGuardarReprogramacion').setDisabled(true);
                }
                }},
         }
    });

    var panelReprogramarPlanificacionCuadrilla = Ext.create('Ext.panel.Panel', {
        bodyPadding: 10,       
        id:'panelReprogramarCuadrillaPlanificacion',
        border: false,        
        //buttonAlign: 'center',
        defaults: {
            anchor: '100%',
            labelWidth: 100            
        },
        width: 400,
        buttons: [
            {
                text: '<i class="fa fa-window-close-o" aria-hidden="true"></i>&nbsp;Cerrar',
                handler: function() 
                {
                    winReprogramarPlanificacionCuadrilla.close();
                }
            },
            {
                xtype          : 'button',
                text           : '<label style="color:steelblue;"><i class="fa fa-save" aria-hidden="true"></i></label>\n\
                                 &nbsp;<b>Guardar</b>',
                align: 'center',
                id: 'btnGuardarReprogramacion',
                disabled: true,
                listeners: 
                {
                    click: function ()
                    {
                        var opcion = Ext.getCmp('valorTipoReprogramacion').value;
                        var idNuevaCuadrilla = comboCuadrillasReprogramacion.value;

                        if (idNuevaCuadrilla != null)
                        {
                            //Llamar proceso de reprogramar planificación
                            Ext.Ajax.request({
                                url: urlReprogramarPlanif,
                                method: 'post',
                                timeout: 60000,
                                params:
                                    {
                                        tipo             : 'CUADRILLA',
                                        opcion           : opcion,
                                        idCabecera       : idCabecera,
                                        idCuadrilla      : idCuadrilla,
                                        idNuevaCuadrilla : idNuevaCuadrilla
                                    },
                                success: function (response)
                                {
                                    Ext.get('panelReprogramarCuadrillaPlanificacion').unmask();
                
                                    var objJson = Ext.JSON.decode(response.responseText);

                                    if(objJson.status === 'Ok')
                                    {
                                        Ext.Msg.alert('Mensaje', objJson.mensaje, function(btn) {
                                            if (btn == 'ok')
                                            {
                                                if (Ext.getCmp('valorTipoReprogramacion').value =='CUALQUIER_CUADRILLA')
                                                {
                                                    winReprogramarPlanificacionCuadrilla.close();
                                                    Ext.getCmp('winDetallePlanificacion').close();
                                                    showCalendario('','','','','');
                                                }
                                                else
                                                {
                                                        var newIdCuadrilla = Ext.getCmp('comboCuadrillasReprogramacion').getValue();
                                                        var newCuadrilla   = Ext.getCmp('comboCuadrillasReprogramacion').getRawValue();
                                                        winReprogramarPlanificacionCuadrilla.close();
                                                        Ext.getCmp('winDetallePlanificacion').close();
                                                        showCalendario('','','','','');
                                                        var newObject = {
                                                                            id           : object.id, 
                                                                            title        : newCuadrilla, 
                                                                            zona         : object.zona, 
                                                                            horaInicio   : object.horaInicio,
                                                                            horaFin      : object.horaFin,
                                                                            fechaActual  : object.fechaActual,
                                                                            idZona       : object.idZona,
                                                                            idIntervalo  : object.idIntervalo,
                                                                            idCuadrilla  : newIdCuadrilla,
                                                                            actividad    : object.actividad,
                                                                            zonaPrestada : object.zonaPrestada 
                                                                        };
                                                        verDetallePlanficacion(newObject);    
                                                }
                                            }
                                        });
                                    }
                                    else
                                    {
                                        Ext.Msg.alert('Alerta', objJson.mensaje);
                                    }
                                },
                                failure: function (result)
                                {
                                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                }
                            });
                        }
                        else
                        {
                            Ext.Msg.alert('Error ', 'Por favor Seleccione nueva cuadrilla');
                            Ext.getCmp('comboCuadrillasReprogramacion').setValue('');
                            Ext.getCmp('comboCuadrillasReprogramacion').setRawValue('');
                            Ext.getCmp('btnGuardarReprogramacion').setDisabled(true);
                        }

                    }
                }
            }
        ],
        items: 
        [
            {
                xtype: 'fieldset',                
                title: '&nbsp;<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Información Básica</b>',
                items: 
                [
                    {
                        xtype: 'fieldcontainer',
                        items: 
                        [   
                            {
                                xtype: 'hiddenfield',                                
                                value: idCuadrilla,
                                id:'idCuadrillaReprog'
                            },
                            {
                                xtype: 'hiddenfield',
                                id:'valorTipoReprogramacion'
                            },  
                            {
                                xtype: 'textfield',
                                width: 300,
                                fieldLabel: '<b>Cuadrilla</b>',
                                value: cuadrilla,
                                id:'cuadrillaReprog',
                                readOnly: true
                            },
                            {
                                xtype: 'radiogroup',
                                fieldLabel: 'Reprogramar a',
                                // Arrange radio buttons into two columns, distributed vertically
                                columns: 1,
                                vertical: false,
                                items: [
                                    { boxLabel: 'Cualquier Cuadrilla', name: 'tipoReprogramacion', inputValue: 'CUALQUIER_CUADRILLA' },
                                    { boxLabel: 'A una Cuadrilla', name: 'tipoReprogramacion', inputValue: 'A_CUADRILLA'}
                                ],
                                listeners: {
                                    change: function(field, newValue, oldValue) {
                                        console.log("onchange optionbox");
                                        var value = newValue.tipoReprogramacion;
                                        /*if (Ext.isArray(value)) {
                                            return;
                                        }*/
                                        Ext.getCmp('valorTipoReprogramacion').setValue(value);
                                        if(value == 'CUALQUIER_CUADRILLA')
                                        {
                                            Ext.getCmp('comboCuadrillasReprogramacion').setValue("");
                                            Ext.getCmp('comboCuadrillasReprogramacion').getEl().toggle();
                                            Ext.getCmp('comboCuadrillasReprogramacion').getEl().hide();
                                            Ext.getCmp('btnGuardarReprogramacion').setDisabled(false);
                                        }
                                        else if(value=='A_CUADRILLA')
                                        {
                                            Ext.getCmp('comboCuadrillasReprogramacion').setValue("");
                                            Ext.getCmp('comboCuadrillasReprogramacion').getEl().toggle();
                                            Ext.getCmp('comboCuadrillasReprogramacion').getEl().show();
                                            Ext.getCmp('btnGuardarReprogramacion').setDisabled(true);
                                        }

                                    }
                                }
                            },
                            comboCuadrillasReprogramacion

                        ]
                    }
                ]
            }
        ]
    });


    var winReprogramarPlanificacionCuadrilla = Ext.create('Ext.window.Window',
    {
        id:'winReprogramarPlanificacion',
        title: 'Reprogramar Planificación',
        modal: true,
        width: 400,
        height: 250,
        closable: false,
        layout: 'fit',
        items: [panelReprogramarPlanificacionCuadrilla]
    }).show();
}



function verReprogramacionZona(object)
{
    var idCabecera  = object.id;
    var zona        = object.zona;
    var idZona      = object.idZona;
    var idCuadrilla = object.idCuadrilla;

    var comboZonasReprogramacion = Ext.create('Ext.form.ComboBox', {
        id           : 'comboZonasReprogramacion',
        store        : storeZonasFilter,
        displayField : 'nombreZona',
        valueField   : 'idZona',
        fieldLabel   : 'Nueva Zona',
        width        : 340,
        labelWidth   : 100,
        emptyText    : 'Seleccione nueva Zona',
        disabled     : false,
        hidden       : false,
        listeners: {
            select: {fn: function(combo, value) {
                    Ext.getCmp('btnGuardarReprogramacionZona').setDisabled(false);
                }}
         }
    });

    var panelReprogramarZonaPlanificacion = Ext.create('Ext.panel.Panel', {
        bodyPadding: 10,       
        id:'panelReprogramarZonaPlanificacion',
        border: false,        
        //buttonAlign: 'center',
        defaults: {
            anchor: '100%',
            labelWidth: 100            
        },
        width: 400,
        buttons: [
            {
                text: '<i class="fa fa-window-close-o" aria-hidden="true"></i>&nbsp;Cerrar',
                handler: function() 
                {
                    winReprogramarZonaPlanificacion.close();
                }
            },
            {
                xtype          : 'button',
                text           : '<label style="color:steelblue;"><i class="fa fa-save" aria-hidden="true"></i></label>\n\
                                 &nbsp;<b>Guardar</b>',
                align: 'center',
                id: 'btnGuardarReprogramacionZona',
                disabled: true,
                listeners: 
                {
                    click: function ()
                    {
                        var idNuevaZona = comboZonasReprogramacion.value;
                        if (idNuevaZona != null)
                        {
                            //Llamar proceso de reprogramar planificación
                            Ext.Ajax.request({
                                url: urlReprogramarPlanif,
                                method: 'post',
                                timeout: 60000,
                                params:
                                    {
                                        tipo        : 'ZONA',
                                        idCabecera  : idCabecera,
                                        idZona      : idZona,
                                        idNuevaZona : idNuevaZona,
                                        idCuadrilla : idCuadrilla
                                    },
                                success: function (response)
                                {
                                    Ext.get('panelReprogramarZonaPlanificacion').unmask();
                
                                    var objJson = Ext.JSON.decode(response.responseText);

                                    if(objJson.status === 'Ok')
                                    {
                                        Ext.Msg.alert('Mensaje', objJson.mensaje, function(btn) {
                                            if (btn == 'ok')
                                            {
                                                var newIdZona = Ext.getCmp('comboZonasReprogramacion').getValue();
                                                var newZona   = Ext.getCmp('comboZonasReprogramacion').getRawValue();
                                                winReprogramarZonaPlanificacion.close();
                                                Ext.getCmp('winDetallePlanificacion').close();
                                                showCalendario('','','','','');
                                                var newObject = {
                                                                    id           : object.id, 
                                                                    title        : object.title, 
                                                                    zona         : newZona, 
                                                                    horaInicio   : object.horaInicio,
                                                                    horaFin      : object.horaFin,
                                                                    fechaActual  : object.fechaActual,
                                                                    idZona       : newIdZona,
                                                                    idIntervalo  : object.idIntervalo,
                                                                    idCuadrilla  : object.idCuadrilla,
                                                                    actividad    : object.actividad,
                                                                    zonaPrestada : object.zonaPrestada 
                                                                };
                                                verDetallePlanficacion(newObject);
                                            }
                                        });
                                    }
                                    else
                                    {
                                        Ext.Msg.alert('Alerta', objJson.mensaje);
                                    }
                                },
                                failure: function (result)
                                {
                                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                }
                            });
                        }
                        else{
                            Ext.Msg.alert('Error ', 'Por favor Seleccione nueva zona');
                            Ext.getCmp('comboZonasReprogramacion').setValue('');
                            Ext.getCmp('comboZonasReprogramacion').setRawValue('');
                            Ext.getCmp('btnGuardarReprogramacionZona').setDisabled(true);

                        }
                    }
                }
            }
        ],
        items: 
        [
            {
                xtype: 'fieldset',                
                title: '&nbsp;<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Información Básica</b>',
                items: 
                [
                    {
                        xtype: 'fieldcontainer',
                        items: 
                        [   
                            {
                                xtype: 'hiddenfield',                                
                                value: idZona,
                                id:'idZonaReprog'
                            },
                            {
                                xtype: 'hiddenfield',
                                id:'valorTipoReprogramacion'
                            },  
                            {
                                xtype: 'textfield',
                                width: 300,
                                fieldLabel: '<b>Zona Actual</b>',
                                value: zona,
                                id:'zonaReprog',
                                readOnly: true
                            },
                            comboZonasReprogramacion

                        ]
                    }
                ]
            }
        ]
    });


    var winReprogramarZonaPlanificacion = Ext.create('Ext.window.Window',
    {
        id:'winReprogramarZonaPlanificacion',
        title: 'Cambiar Zona de Planificación',
        modal: true,
        width: 400,
        height: 250,
        closable: false,
        layout: 'fit',
        items: [panelReprogramarZonaPlanificacion]
    }).show();
}


function liberarDiaTrabajo(idCabecera,idCuadrilla)
{
    var grid = Ext.getCmp("gridDetalle");
    var cont = 0;
    
    for (var i = 0; i < grid.getStore().getCount(); i++)
    {
        var estadoTarea = grid.getStore().getAt(i).data.estadoTarea;
        
        if(!Ext.isEmpty(estadoTarea))
        {
            if(estadoTarea !== 'Finalizada' &&
               estadoTarea !== 'Anulada'    &&
               estadoTarea !== 'Rechazada'  &&
               estadoTarea !== 'Cancelada')
            {
                cont++;
            }
        }
    }

    if(cont > 0)
    {
        Ext.Msg.alert('<b>Alerta</b>', '<b>No se puede liberar el día de trabajo dado que aún existe<br/ >\n\
                                        al menos una tarea sin finalizar para la cuadrilla..!!</b>');
        return;
    }

    //Si no existen tareas sin finalizar se puede liberar el dia de camello
    Ext.Msg.confirm('Alerta', '¿Está seguro que desea liberar el día de trabajo?', function(btn) {
        if (btn == 'yes')
        {
            Ext.get('panelDetalle').mask("Liberando día de Trabajo...");

            Ext.Ajax.request({
                url: urlEliminarLiberarPlanif,
                method: 'post',
                timeout: 60000,
                params:
                    {
                        accion        : 'liberarDiaTrabajo',
                        idCabecera    : idCabecera,
                        idCuadrilla   : idCuadrilla
                    },
                success: function (response)
                {
                    Ext.get('panelDetalle').unmask();

                    var objJson = Ext.JSON.decode(response.responseText);

                    if(objJson.status === 'OK')
                    {
                        Ext.Msg.alert('Mensaje', objJson.mensaje, function(btn) {
                            if (btn == 'ok')
                            {
                                Ext.getCmp('winDetallePlanificacion').close();
                                showCalendario('','','','','');
                            }
                        });
                    }
                    else
                    {
                        Ext.Msg.alert('Alerta', objJson.mensaje);
                    }
                },
                failure: function (result)
                {
                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                }
            });
        }
    });
}

function liberarHorasTrabajo(idCabecera,idCuadrilla)
{
    var grid           = Ext.getCmp("gridDetalle");
    var arrayLiberar   = [];
    var arraySelection = grid.getSelectionModel().getSelection();
    
    var observaciones  = '';

    for(var i=0 ;  i < arraySelection.length ; ++i)
    {
        var estado      = arraySelection[i].data.estadoTarea;
        var boolLiberar = true;

        if(!Ext.isEmpty(estado))
        {
            if(estado !== 'Finalizada' &&
               estado !== 'Anulada'    &&
               estado !== 'Rechazada'  &&
               estado !== 'Cancelada')
            {
                observaciones += "<br/><i class='fa fa-long-arrow-right' aria-hidden='true'></i>&nbsp;";
                observaciones += "<b>Intervalo:</b> &nbsp;"+arraySelection[i].data.horaInicio+" - "+arraySelection[i].data.horaFin;
                boolLiberar    = false;
            }
        }

        if(boolLiberar)
        {
            var json = {};
            json['idDetalle'] = arraySelection[i].data.idDet;
            arrayLiberar.push(json);
        }
    }    
    
    if(!Ext.isEmpty(observaciones))
    {
        Ext.Msg.confirm('Alerta ', 'Las siguientes horas no serán liberadas por tener tareas pendientes de finalizar: ' + observaciones +
                                 '<br/><b>¿Desea Continuear?</b>', function(btn) {
            if (btn == 'yes')
            {
                if (!Ext.isEmpty(arrayLiberar))
                {
                    ajaxLiberarHoras(arrayLiberar,idCabecera,idCuadrilla);
                }
            }
        });
    }
    else
    {
        Ext.Msg.confirm('Alerta ', '¿Está seguro que desea liberar las horas de trabajo seleccionadas?', function(btn) {
            if (btn == 'yes')
            {
                ajaxLiberarHoras(arrayLiberar,idCabecera,idCuadrilla);
            }
        });
    }
}

function ajaxLiberarHoras(arrayLiberar,idCabecera,idCuadrilla)
{
    Ext.get('gridDetalle').mask("Liberando Horas de Trabajo...");
    
    Ext.Ajax.request({
        url: urlEliminarLiberarPlanif,
        method: 'post',
        timeout: 60000,
        params:
            {
                accion      : 'liberarHoraTrabajo',
                detalles    : Ext.JSON.encode(arrayLiberar),
                idCabecera  : idCabecera,
                idCuadrilla : idCuadrilla
            },
        success: function (response)
        {
            Ext.get('gridDetalle').unmask();

            var objJson = Ext.JSON.decode(response.responseText);

            if(objJson.status === 'OK')
            {                                
                Ext.Msg.alert('Mensaje', objJson.mensaje, function(btn) {
                    if (btn == 'ok') 
                    {
                        storeDetallePlanificacion.load();
                        showCalendario('','','','','');
                        storeJornadaLaboral.removeAll();
                        storeJornadaLaboral.getProxy().extraParams.idCab      = idCabecera;
                        storeJornadaLaboral.getProxy().extraParams.estadoCab  = 'Activo';
                        storeJornadaLaboral.getProxy().extraParams.estadoDet  = 'Activo';
                        storeJornadaLaboral.load();
                    }
                });
            }
            else
            {
                Ext.Msg.alert('Alerta', objJson.mensaje);
            }
        },
        failure: function (result)
        {
            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
        }
    });
}

function liberarPermisoEvento(idCabecera, idCuadrilla, opcion, msgAlert)
{
    Ext.Msg.confirm('Alerta', '¿Está seguro que desea solicitar ' + msgAlert + '?', function(btn) {
        if (btn == 'yes')
        {
            Ext.get('panelDetalle').mask('Solicitando ' + msgAlert + '...');

            Ext.Ajax.request({
                url: urlLiberarPermisoEvento,
                method: 'post',
                timeout: 180000,
                params:
                    {
                        opcion        : opcion,
                        idCabecera    : idCabecera,
                        idCuadrilla   : idCuadrilla
                    },
                success: function (response)
                {
                    Ext.get('panelDetalle').unmask();

                    var objJson = Ext.JSON.decode(response.responseText);

                    if(objJson.status === 'OK')
                    {
                        Ext.Msg.alert('Mensaje', objJson.mensaje, function(btn) {
                            
                        });
                    }
                    else
                    {
                        Ext.Msg.alert('Alerta', objJson.mensaje);
                    }
                },
                failure: function (result)
                {
                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                }
            });
        }
    });
}