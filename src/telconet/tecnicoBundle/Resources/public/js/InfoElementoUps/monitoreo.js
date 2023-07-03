$("[data-mask]").inputmask();

var arrayDireccion  = [];
var arrayLatitud    = [];
var arrayLongitud   = [];
var arrayCiudad     = [];
var arrayProvincia  = [];
var arrayNodo       = [];
var arrayIpUps      = [];
var arrayTipo       = [];
var arraySeveridad  = [];

Ext.require
([
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

var connEsperaAccion = new Ext.data.Connection
({
    listeners:
    {
        'beforerequest': 
        {
            fn: function (con, opt)
            {						
                Ext.MessageBox.show
                ({
                   msg: 'Cargando la información, por favor espere!!',
                   width:300,
                   wait:true,
                   waitConfig: {interval:200}
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

function limpiarBusqueda()
{
    document.getElementById('nombreNodo').value = '';
    document.getElementById('ipUps').value = '';
    Ext.getCmp('cmbMarca').setValue(null);
    Ext.getCmp('cmbMarca').setRawValue(null);
    Ext.getCmp('cmbRegion').setValue(null);
    Ext.getCmp('cmbRegion').setRawValue(null);
    Ext.getCmp('cmbProvincia').reset();
    Ext.getCmp('cmbProvincia').setDisabled(true);
    Ext.getCmp('cmbCiudad').reset();
    Ext.getCmp('cmbCiudad').setDisabled(true);
    Ext.getCmp('cmbEstado').setRawValue(null);
    Ext.getCmp('cmbSeveridad').setValue(null);
    Ext.getCmp('cmbSeveridad').setRawValue(null);
    
    intInicio = intValorInicial;
    intLimite = intValorLimite;
    
    buscarUps(intInicio, intLimite);
}


function buscarUps(inicio, limite)
{
    var boolContinuar = true;
    var strNombreNodo = document.getElementById('nombreNodo').value;
    var strIpsUps     = document.getElementById('ipUps').value;
    var strMarca      = Ext.getCmp('cmbMarca').getValue();
    var strRegion     = Ext.getCmp('cmbRegion').getValue();
    var strProvincia  = Ext.getCmp('cmbProvincia').getValue();
    var strCiudad     = Ext.getCmp('cmbCiudad').getValue();
    var strEstado     = Ext.getCmp('cmbEstado').getValue();
    var strSeveridad  = Ext.getCmp('cmbSeveridad').getValue();
    
    if( strEstado != null && strEstado != "" )
    {
        strEstado = strEstado.join();
    }
    
    if( strSeveridad != null && strSeveridad != "" )
    {
        strSeveridad = strSeveridad.join();
    }
    
    
    if( strIpsUps != '' && strIpsUps != null)
    {
        var ipTmp          = strIpsUps.replace(/_/g,"");
        var resultadoSplit = ipTmp.split(".");
        var incompleto     = false;

        strIpsUps = ipTmp;

        for (var i = 0; i < resultadoSplit.length; i++)
        {
            if( resultadoSplit[i] == "" || resultadoSplit[i] == null)
            {
                incompleto = true;
            }
        }


        if(incompleto)
        {        
            boolContinuar = false;

            Ext.MessageBox.hide();
            Ext.Msg.alert("Atención", "Debe ingresar una ip válida");
        }
    }
    
    if(boolContinuar)
    {
        connEsperaAccion.request
        ({
            url: strUrlBuscarDispositivos,
            method: 'post',
            dataType: 'json',
            timeout: 9000000,
            params:
            { 
                strNombreNodo: strNombreNodo,
                strIpsUps: strIpsUps,
                strMarca: strMarca,
                strRegion: strRegion,
                strProvincia: strProvincia,
                strCiudad: strCiudad,
                strEstado: strEstado,
                strSeveridad: strSeveridad,
                length: limite,
                start: inicio
            },
            success: function(result)
            {
                var objData               = Ext.JSON.decode(result.responseText);
                var arrayDispositivos     = objData.data;
                var objParametrosBusqueda = { 
                                                strNombreNodo: strNombreNodo,
                                                strIpsUps: strIpsUps,
                                                strMarca: strMarca,
                                                strRegion: strRegion,
                                                strProvincia: strProvincia,
                                                strCiudad: strCiudad,
                                                strEstado: strEstado,
                                                strSeveridad: strSeveridad
                                            };
                                            
                intInicio = objData.intInicio;
                intLimite = objData.intLimite;
                
                arrayDireccion = [];
                arrayLatitud   = [];
                arrayLongitud  = [];
                arrayCiudad    = [];
                arrayProvincia = [];
                arrayNodo      = [];
                arrayIpUps     = [];
                arrayTipo      = [];
                arraySeveridad = [];

                iniciarDatatable(intInicio, intLimite, objParametrosBusqueda);
                
                for(var i=0; i < arrayDispositivos.length ; i++)
                {
                    var arrayDispositivo = arrayDispositivos[i];
                    
                    arrayDireccion.push(arrayDispositivo['direccion']);
                    arrayLatitud.push(arrayDispositivo['latitud']);
                    arrayLongitud.push(arrayDispositivo['longitud']);
                    arrayCiudad.push(arrayDispositivo['ciudad']);
                    arrayProvincia.push(arrayDispositivo['provincia']);
                    arrayNodo.push(arrayDispositivo['nombreNodo']);
                    arrayIpUps.push(arrayDispositivo['ipUps']);
                    arrayTipo.push(arrayDispositivo['tipo']);
                    arraySeveridad.push(arrayDispositivo['severidad']);
                }
                
                if( arrayLatitud.length > 0)
                {
                    muestraMapa();
                }
            },
            failure: function(result)
            {
                Ext.Msg.alert('Error ','Error: ' + result.statusText);
            }
        });
    }
}

function exportarExcel()
{            
    var boolContinuar = true;
    var strNombreNodo = document.getElementById('nombreNodo').value;
    var strIpsUps     = document.getElementById('ipUps').value;
    var strMarca      = Ext.getCmp('cmbMarca').getValue();
    var strRegion     = Ext.getCmp('cmbRegion').getValue();
    var strProvincia  = Ext.getCmp('cmbProvincia').getValue();
    var strCiudad     = Ext.getCmp('cmbCiudad').getValue();
    var strEstado     = Ext.getCmp('cmbEstado').getValue();
    var strSeveridad  = Ext.getCmp('cmbSeveridad').getValue();
    
    if( strEstado != null && strEstado != "" )
    {
        strEstado= strEstado.join();
    }
    
    if( strSeveridad != null && strSeveridad != "" )
    {
        strSeveridad= strSeveridad.join();
    }
    
    
    if( strIpsUps != '' && strIpsUps != null)
    {
        var ipTmp          = strIpsUps.replace(/_/g,"");
        var resultadoSplit = ipTmp.split(".");
        var incompleto     = false;

        strIpsUps = ipTmp;

        for (var i = 0; i < resultadoSplit.length; i++)
        {
            if( resultadoSplit[i] == "" || resultadoSplit[i] == null)
            {
                incompleto = true;
            }
        }


        if(incompleto)
        {        
            boolContinuar = false;

            Ext.MessageBox.hide();
            Ext.Msg.alert("Atención", "Debe ingresar una ip válida");
        }
    }
    
    
    if(boolContinuar)
    {    
        document.getElementById('strNombreNodo').value = strNombreNodo;
        document.getElementById('strIpsUps').value     = strIpsUps;
        document.getElementById('strMarca').value      = strMarca;
        document.getElementById('strRegion').value     = strRegion;
        document.getElementById('strProvincia').value  = strProvincia;
        document.getElementById('strCiudad').value     = strCiudad;
        document.getElementById('strEstado').value     = strEstado;
        document.getElementById('strSeveridad').value  = strSeveridad;
        
        document.forms[0].submit();
    }
}


Ext.onReady(function()
{
    var storeMarcas = new Ext.data.Store
    ({
        total: 'total',
        autoLoad: true,
        proxy: 
        {
            type: 'ajax',
            url: strUrlGetMarcas,
            timeout: 400000,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams:
            {
                tipoElemento: 'UPS'
            }
        },
        fields:
        [
            {name: 'idMarcaElemento',     mapping: 'idMarcaElemento'},
            {name: 'nombreMarcaElemento', mapping: 'nombreMarcaElemento'}
        ]
    });
    
    var cmbMarcas = new Ext.form.ComboBox
    ({
        id: 'cmbMarca',
        name: 'cmbMarca',
        fieldLabel: false,
        editable: false,
        anchor: '100%',
        queryMode: 'local',
        width: '100%',
        emptyText: 'Seleccione Marca',
        store: storeMarcas,
        displayField: 'nombreMarcaElemento',
        valueField: 'nombreMarcaElemento',
        renderTo: 'divMarca',
        forceSelection: true
    });
    
    
    var storeRegiones = new Ext.data.Store
    ({
        total: 'total',
        autoLoad: true,
        proxy: 
        {
            type: 'ajax',
            url: strUrlGetRegiones,
            timeout: 400000,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams:
            {
                estado: 'Activo'
            }
        },
        fields:
        [
            {name: 'id_region',     mapping: 'id_region'},
            {name: 'nombre_region', mapping: 'nombre_region'}
        ]
    });
    
    var cmbRegiones = new Ext.form.ComboBox
    ({
        id: 'cmbRegion',
        name: 'cmbRegion',
        fieldLabel: false,
        editable: false,
        anchor: '100%',
        queryMode: 'local',
        width: '100%',
        emptyText: 'Seleccione Region',
        store: storeRegiones,
        displayField: 'nombre_region',
        valueField: 'nombre_region',
        renderTo: 'divRegion',
        forceSelection: true,
        listeners: 
        {
            select: 
            {
                fn: function(combo, value) 
                {
                    Ext.getCmp('cmbProvincia').reset();
                    Ext.getCmp('cmbProvincia').setDisabled(false);
                    
                    Ext.getCmp('cmbCiudad').reset();
                    Ext.getCmp('cmbCiudad').setDisabled(true);
                    
                    if( combo.getValue() != '' && combo.getValue() != null)
                    {
                        getProvincias(storeProvincias, combo.getValue());
                    }
                }
            }
        }
    });
    
    
    var storeProvincias = new Ext.data.Store
    ({
        total: 'total',
        autoLoad: false,
        proxy: 
        {
            type: 'ajax',
            url: strUrlGetProvincias,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
        [
            {name: 'id_provincia',     mapping: 'id_provincia'},
            {name: 'nombre_provincia', mapping: 'nombre_provincia'}
        ]
    });

    var cmbProvincias = new Ext.form.ComboBox
    ({
        id: 'cmbProvincia',
        name: 'cmbProvincia',
        fieldLabel: false,
        editable: false,
        disabled: true,
        anchor: '100%',
        queryMode: 'local',
        width: '100%',
        emptyText: 'Seleccione Provincia',
        store: storeProvincias,
        displayField: 'nombre_provincia',
        valueField: 'nombre_provincia',
        renderTo: 'divProvincia',
        forceSelection: true,
        listeners: 
        {
            select: 
            {
                fn: function(combo, value) 
                {
                    Ext.getCmp('cmbCiudad').reset();
                    Ext.getCmp('cmbCiudad').setDisabled(false);
                    
                    if( combo.getValue() != '' && combo.getValue() != null)
                    {
                        getCiudades(storeCiudades, combo.getValue());
                    }
                }
            }
        }
    });
    
    function getProvincias(store, regionId)
    {
        store.proxy.extraParams = {idRegion: regionId};
        store.load();
    }
    
    
    var storeCiudades = new Ext.data.Store
    ({
        total: 'total',
        autoLoad: false,
        proxy: 
        {
            type: 'ajax',
            url: strUrlGetCiudades,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
        [
            {name: 'id_canton',     mapping: 'id_canton'},
            {name: 'nombre_canton', mapping: 'nombre_canton'}
        ]
    });

    var cmbCiudades = new Ext.form.ComboBox
    ({
        id: 'cmbCiudad',
        name: 'cmbCiudad',
        fieldLabel: false,
        editable: false,
        disabled: true,
        anchor: '100%',
        queryMode: 'local',
        width: '100%',
        emptyText: 'Seleccione Ciudad',
        store: storeCiudades,
        displayField: 'nombre_canton',
        valueField: 'nombre_canton',
        renderTo: 'divCiudad'
    });
    
    function getCiudades(store, provinciaId)
    {
        store.proxy.extraParams = {idProvincia: provinciaId};
        store.load();
    }
    
    
    var storeEstados = new Ext.data.Store
    ({
        total: 'total',
        autoLoad: true,
        pageSize: 100,
        proxy: 
        {
            type: 'ajax',
            url: strUrlGetParametrosCab,
            timeout: 400000,
            reader: 
            {
                type: 'json',
                totalProperty: 'intTotalParametros',
                root: 'jsonAdmiParametroDetResult'
            },
            extraParams:
            {
                intIdParametroCab: intIdEstadosMonitoreo,
                strEstado: 'Activo'
            }
        },
        fields:
        [
            {name: 'strDescripcionDet', mapping: 'strDescripcionDet'}
        ]
    });
    
    var cmbEstados = new Ext.form.ComboBox
    ({
        id: 'cmbEstado',
        name: 'cmbEstado[]',
        fieldLabel: false,
        editable: false,
        multiSelect: true,
        anchor: '100%',
        queryMode: 'local',
        width: '100%',
        emptyText: 'Seleccione Estado',
        store: storeEstados,
        displayField: 'strDescripcionDet',
        valueField: 'strDescripcionDet',
        renderTo: 'divEstado',
        forceSelection: true
    });
    
    
    var storeSeveridad = new Ext.data.Store
    ({
        total: 'total',
        autoLoad: true,
        pageSize: 100,
        proxy: 
        {
            type: 'ajax',
            url: strUrlGetParametrosCab,
            timeout: 400000,
            reader: 
            {
                type: 'json',
                totalProperty: 'intTotalParametros',
                root: 'jsonAdmiParametroDetResult'
            },
            extraParams:
            {
                intIdParametroCab: intIdSeveridadMonitoreo,
                strEstado: 'Activo'
            }
        },
        fields:
        [
            {name: 'strDescripcionDet', mapping: 'strDescripcionDet'}
        ]
    });
    
    var cmbSeveridad = new Ext.form.ComboBox
    ({
        id: 'cmbSeveridad',
        name: 'cmbSeveridad[]',
        fieldLabel: false,
        editable: false,
        multiSelect: true,
        anchor: '100%',
        queryMode: 'local',
        width: '100%',
        emptyText: 'Seleccione Severidad',
        store: storeSeveridad,
        displayField: 'strDescripcionDet',
        valueField: 'strDescripcionDet',
        renderTo: 'divSeveridad',
        forceSelection: true
    });

    $('[data-toggle="tooltip"]').tooltip(); 

    buscarUps(intInicio, intLimite);
});

function muestraMapa()
{
    var infowindow;
    var mapa;
    var center = new google.maps.LatLng(-1.766963, -77.973673);

    var myOptions = 
    {
        zoom: 7,
        center: center,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    
    infowindow = new google.maps.InfoWindow
    ({
        content: "Cargando.."
    });

    mapa = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

    for (i = 0; i < arrayLatitud.length; i++) 
    {
        var latlng = new google.maps.LatLng(arrayLatitud[i], arrayLongitud[i]);

        var markerPto = new google.maps.Marker
        ({
            position: latlng, 
            map: mapa,
            title: arrayCiudad[i]+' - '+arrayProvincia[i],
            html: '<table class="table table-hover" style="margin-bottom: 0px; font-size: 11px;">'+
                    '<tr>'+
                        '<td>'+
                            '<dl class="dl-horizontal" style="margin-bottom: 0px;">'+
                                '<dt>Nodo</dt>'+
                                '<dd>'+arrayNodo[i]+'</dd>'+
                                '<dt>IP Ups</dt>'+
                                '<dd>'+arrayIpUps[i]+'</dd>'+
                                '<dt>Tipo</dt>'+
                                '<dd>'+arrayTipo[i]+'</dd>'+
                                '<dt>Ciudad - Provincia</dt>'+
                                '<dd>'+arrayCiudad[i]+' - '+arrayProvincia[i]+'</dd>'+
                                '<dt>Direccion</dt>'+
                                '<dd>'+arrayDireccion[i]+'</dd>'+
                                '<dt>Severidad</dt>'+
                                '<dd>'+
                                    '<span class="estado'+arraySeveridad[i]+'">'+
                                        arraySeveridad[i]+
                                    '</span>'+
                                '</dd>'+
                            '</dl>'+
                        '</td>'+
                    '</tr>'+
                '</table>'
        });
        
        google.maps.event.addListener(markerPto, 'click', function ()
        {
            infowindow.setContent(this.html);
            infowindow.open(mapa, this);
        });
    }
}


setInterval(function(){ buscarUps(intInicio, intLimite); }, 300000);

function ocultarMapa()
{
    $('#divAOcultar').toggle();

    //Primero se oculta o se hace visible el panel del mapa
    if( $('#divAOcultar').is(":visible") )
    {
        $('#informacion-dispositivos').removeClass("col-xs-11").addClass("col-xs-offset-0-5 col-xs-4 ");
        $('#divAOcultar').addClass("col-xs-6 well");
        muestraMapa();
    }
    else
    {
        $('#divAOcultar').removeClass("col-xs-6 well");
        $('#informacion-dispositivos').removeClass("col-xs-offset-0-5 col-xs-4").addClass("col-xs-11");
        $('#tableMonitoreo').DataTable().columns.adjust();
    }
}


function iniciarDatatable(inicio, limite, objParametrosBusqueda)
{
    $("#tableMonitoreo").DataTable
    ({
        "processing": true,
        "bLengthChange": false,
        "serverSide": true,
        "iDisplayLength": limite,
        "iDisplayStart": inicio,
        "bFilter": false,
        "scrollY": 435,
        "destroy": true,
        "dom": '<"top"iflp<"clear">>rt<"bottom"flp<"clear">>',
        "fnDrawCallback": function( oSettings )
        {
            intLimite = oSettings._iDisplayLength;
            intInicio = oSettings._iDisplayStart;
        },
        "ajax": 
        {
            "method": "post",
            "url": strUrlBuscarDispositivos,
            "dataType": "json",
            "data": objParametrosBusqueda
        },
        "createdRow": function ( row, data, index ) 
        {
            $('td', row).eq(4).addClass('estado'+data.severidad);
            $('td', row).eq(5).addClass('estado'+data.severidad);
        },
        columns: 
        [
            { data: 'nombreNodo' },
            { data: 'ipUps' },
            { data: 'generador' },
            { data: 'ciudad' },
            { data: 'severidad' },
            { data: 'descripcionAlerta' },
            { data: 'fechaModificacion' },
            { data: 'id' }
        ],
        "columnDefs": 
        [
            {
                "render": function (data, type, row) 
                {
                    var permiso      = $("#ROLE_326-3817");
                    var boolPermiso  = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                    var strAcciones  = '';
                    
                    if(boolPermiso)
                    { 
                        if(row.asignadoTarea == 'N')
                        {
                            strAcciones = strAcciones + '<button class="button-grid-administrarTareas" data-toggle="tooltip" data-placement="left" '+
                                                        'title="Asignar tarea" onclick="asignarTarea('+row.id+', '+row.idUps+')"></button>';
                        }
                    }
                    
                    permiso      = $("#ROLE_326-1147");
                    boolPermiso  = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                    
                    if(boolPermiso)
                    {
                        if(row.asignadoTarea == 'S')
                        {
                            strAcciones = strAcciones + '<button class="button-grid-show" data-toggle="tooltip" data-placement="bottom" ' +
                                                        'title="Ver Seguimiento Alerta" onclick="verSeguimientoTarea('+row.idDetalleTarea+')" >'+
                                                        '</button>';
                        }
                    }
                    
                    return strAcciones;
                    
                },
                "targets": 7
            }
        ]
    });
}


/* *********************************************************************** */
/* *************************** ASIGNAR TAREA ***************************** */
/* *********************************************************************** */
var winAsignarTarea;
var connGuardar = new Ext.data.Connection
({
    listeners: 
    {
        'beforerequest': 
        {
            fn: function (con, opt) 
            {
                Ext.MessageBox.show
                ({
                    msg: 'Guardando información...',
                    width:300,
                    wait:true,
                    waitConfig: {interval:200}
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


function presentarDepartamentosPorCiudad(id_canton, empresa) 
{
    storeDepartamentosCiudad.proxy.extraParams = { id_canton: id_canton, empresa: empresa };
    storeDepartamentosCiudad.load();
}


function presentarEmpleadosXDepartamentoCiudad(id_departamento, id_canton, empresa, valorIdDepartamento) 
{
    storeAsignaEmpleado.proxy.extraParams = { id_canton: id_canton, empresa: empresa, id_departamento: id_departamento, departamento_caso: valorIdDepartamento};
    storeAsignaEmpleado.load();
}


function asignarTarea(idAlertaMonitoreo, idUps)
{
    storeCiudades = new Ext.data.Store
    ({
        total: 'total',
        pageSize: 200,
        autoLoad: true,
        proxy: 
        {
            type: 'ajax',
            method: 'post',
            url: strUrlCiudadPorEmpresa,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: 
            {
                nombre: '',
                estado: 'Activo',
                empresa: intIdEmpresaSession
            }
        },
        fields:
        [
            {name: 'id_canton', mapping: 'id_canton'},
            {name: 'nombre_canton', mapping: 'nombre_canton'}
        ]
    });


    storeDepartamentosCiudad = new Ext.data.Store
    ({
        total: 'total',
        pageSize: 200,
        proxy: 
        {
            type: 'ajax',
            method: 'post',
            url: strUrlDepartamentoPorEmpresaCiudad,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams:
            {
                nombre: '',
                estado: 'Activo'
            }
        },
        fields:
            [
                {name: 'id_departamento', mapping: 'id_departamento'},
                {name: 'nombre_departamento', mapping: 'nombre_departamento'}
            ]
    });


    storeAsignaEmpleado = new Ext.data.Store
    ({
       total: 'total',        
       proxy: 
       {
           type: 'ajax',
           url: strUrlEmpleadoPorDepartamentoCiudad,
           reader: 
           {
               type: 'json',
               totalProperty: 'result.total',
               root: 'result.encontrados',
               metaProperty: 'myMetaData'
           },
           extraParams: 
           {
               nombre: ''
           }
       },
       fields:
       [
           {name: 'id_empleado', mapping: 'id_empleado'},
           {name: 'nombre_empleado', mapping: 'nombre_empleado'}
       ]
   });
    
   
    combo_empleados = new Ext.form.ComboBox
    ({
        id: 'comboEmpleados',
        name: 'comboEmpleados',
        fieldLabel: "Empleado",
        store: storeAsignaEmpleado,
        displayField: 'nombre_empleado',
        valueField: 'id_empleado',
        queryMode: "remote",
        emptyText: '',
        hidden: false,
        disabled: true,
        editable: false
    });
    
    
    btnguardar = Ext.create('Ext.Button', 
    {
		text: 'Asignar',
		cls: 'x-btn-rigth',
		handler: function() 
        {
            if( Ext.getCmp('comboDepartamento').getValue() != null && Ext.getCmp('comboCiudad').getValue()  != null )
            {
                if(Ext.getCmp('comboEmpleados').getValue())
                {
                    if(Ext.getCmp('observacion').getValue() != null && Ext.getCmp('observacion').getValue().trim() != '')
                    {
                        var strObservacion                = Ext.getCmp('observacion').value;
                        var intDepartamentoAsignado       = Ext.getCmp('comboDepartamento').value;
                        var strEmpleadoAsignado           = Ext.getCmp('comboEmpleados').value;
                        var strNombreDepartamentoAsignado = $('#comboDepartamento-inputEl').val();
                        var strNombreEmpleadoAsignado     = $('#comboEmpleados-inputEl').val();
                        var strFechaEjecucion             = Ext.getCmp('fechaEjecucion').value;
                        var strHoraEjecucion              = Ext.getCmp('horaEjecucion').value;
                    
                        connGuardar.request
                        ({
                            method: 'POST',
                            params:
                            {
                                intIdAlerta:                    idAlertaMonitoreo,
                                intIdElemento:                  idUps,
                                intIdTarea:                     intIdTarea,
                                strObservacion:                 strObservacion,
                                intDepartamentoAsignado:        intDepartamentoAsignado,
                                strEmpleadoAsignado:            strEmpleadoAsignado,
                                strNombreDepartamentoAsignado:  strNombreDepartamentoAsignado,
                                strNombreEmpleadoAsignado:      strNombreEmpleadoAsignado,
                                strFechaEjecucion:              strFechaEjecucion,
                                strHoraEjecucion:               strHoraEjecucion
                            },
                            url: strUrlAsignarTarea,
                            success: function(response)
                            {
                                winAsignarTarea.destroy();
                                
                                var json = Ext.JSON.decode(response.responseText);

                                if(!json.boolError)
                                {
                                    Ext.MessageBox.show
                                    ({
                                        title:'Información',
                                        msg: 'Se asigno la tarea correctamente.',
                                        buttons: Ext.Msg.OK,
                                        fn: function(btn)
                                        {
                                            if (btn == 'ok')
                                            {
                                                Ext.MessageBox.hide(); 
                                                buscarUps(intInicio, intLimite);
                                            }
                                        },
                                        closable : false
                                    });
                                }
                                else
                                {
                                    Ext.Msg.alert('Alerta ',json.strMensaje);
                                }
                            },
                            failure: function(response)
                            {
                                winAsignarTarea.destroy();
                                
                                var json = Ext.JSON.decode(response.responseText);
                                
                                Ext.Msg.alert('Alerta ',json.strMensaje);
                            }
                        });
                    }
                    else
                    {
                        Ext.Msg.alert('Alerta ', 'Por favor escriba una observación');
                    }
                }
                else
                {
                    Ext.Msg.alert('Alerta ', 'Por favor escoja un empleado');
                }
            } 
            else
            {
                Ext.Msg.alert('Alerta ','Campos incompletos, debe seleccionar Ciudad y Departamento');
            }
		}
    });
    
    btncancelar = Ext.create('Ext.Button', 
    {
		text: 'Cerrar',
		cls: 'x-btn-rigth',
		handler: function() 
        {
			winAsignarTarea.destroy();
		}
    });
    
    formPanel = Ext.create('Ext.form.Panel',
    {
		bodyPadding: 5,
		waitMsgTarget: true,
		height: 280,
		width: 300,
		layout: 'fit',
		fieldDefaults: 
        {
			labelAlign: 'left',
			msgTarget: 'side'
		},
		items: 
		[
			{
				xtype: 'fieldset',
				title: '',
				defaultType: 'textfield',
				items: 
				[
					{
						xtype: 'displayfield',
						fieldLabel: 'Tarea:',
						id: 'strNombreTarea',
						name: 'strNombreTarea',
						value: strNombreTarea
					},
					{
						xtype: 'displayfield',
						fieldLabel: 'Empresa:',
						id: 'strEmpresaSession',
						name: 'strEmpresaSession',
						value: strEmpresaSession
					}, 
					{
						xtype: 'combobox',
						fieldLabel: 'Ciudad',
						id: 'comboCiudad',
						name: 'comboCiudad',
						store: storeCiudades,
						displayField: 'nombre_canton',
						valueField: 'id_canton',
						queryMode: "remote",
						emptyText: '',
						listeners: 
                        {
							select: function(combo)
                            {															
								Ext.getCmp('comboDepartamento').reset();
								Ext.getCmp('comboEmpleados').reset();
																								
								Ext.getCmp('comboDepartamento').setDisabled(false);
								Ext.getCmp('comboEmpleados').setDisabled(true);
								
								presentarDepartamentosPorCiudad(combo.getValue(), intIdEmpresaSession);
							}
						},
						forceSelection: true,
                        editable: false
					}, 
					{
						xtype: 'combobox',
						fieldLabel: 'Departamento',
						id: 'comboDepartamento',
						name: 'comboDepartamento',
						store: storeDepartamentosCiudad,
						displayField: 'nombre_departamento',
						valueField: 'id_departamento',
						queryMode: "remote",
						emptyText: '',
						disabled: true,
						listeners: 
                        {
							select: function(combo)
                            {
                                Ext.getCmp('comboEmpleados').reset();
                                Ext.getCmp('comboEmpleados').value = "";
                                Ext.getCmp('comboEmpleados').setDisabled(false);                               
                                Ext.getCmp('comboEmpleados').setRawValue("");
                                canton  = Ext.getCmp('comboCiudad').getValue();
                                
                                presentarEmpleadosXDepartamentoCiudad(combo.getValue(), canton, intIdEmpresaSession);
							}
						},
						forceSelection: true,
                        editable: false
					},
                    combo_empleados,
					{
						xtype: 'datefield',
						fieldLabel: 'Fecha de Ejecucion:',
						id: 'fechaEjecucion',
						name:'fechaEjecucion',
						editable: false,
						format: 'Y-m-d',
						value: new Date() ,
						minValue: new Date()
					},
					{
						xtype: 'timefield',
						fieldLabel: 'Hora de Ejecucion:',
						format: 'H:i',
						id: 'horaEjecucion',
						name: 'horaEjecucion',
						minValue: '00:01',
						maxValue: '23:59',
						increment: 1,						
						editable: false,
						value: new Date() 
					},             
                    {
						xtype: 'textarea',
						fieldLabel: 'Observación:',
						id: 'observacion',
						name: 'observacion',
						rows: 4,
						cols: 50
                    }                         
                ]
            }
        ]
    });  
	
    winAsignarTarea = Ext.create('Ext.window.Window',
    {
        title: 'Asignar Tarea',
        modal: true,
        width: 420,
        height: 410,
        resizable: false,
        layout: 'fit',
        items: [formPanel],
        buttonAlign: 'center',
        buttons:[btnguardar, btncancelar]
    }).show(); 
}
/* *********************************************************************** */
/* ************************* FIN ASIGNAR TAREA *************************** */
/* *********************************************************************** */



/* *********************************************************************** */
/* *********************** VER SEGUIMIENTO TAREA ************************* */
/* *********************************************************************** */
function verSeguimientoTarea(intIdDetalle)
{
    btncancelar = Ext.create('Ext.Button',
    {
        text: 'Cerrar',
        cls: 'x-btn-rigth',
        handler: function() 
        {
            winSeguimientoTarea.destroy();													
        }
    });
    
	storeSeguimientoTarea = new Ext.data.Store
    ({ 
		total: 'total',
		autoLoad: true,
		proxy: 
        {
			type: 'ajax',
			url : strUrlVerSeguimientoTarea,
			reader: 
            {
				type: 'json',
				totalProperty: 'total',
				root: 'encontrados'
			},
			extraParams:
            {
				id_detalle: intIdDetalle			
			}
		},
		fields:
		[
		      {name:'id_detalle',   mapping:'id_detalle'},
		      {name:'observacion',  mapping:'observacion'},
		      {name:'departamento', mapping:'departamento'},
		      {name:'empleado',     mapping:'empleado'},
		      {name:'fecha',        mapping:'fecha'}					
		]
	});
    
	gridSeguimiento = Ext.create('Ext.grid.Panel', 
    {
		id:'gridSeguimiento',
		store: storeSeguimientoTarea,		
		columnLines: true,
		columns: 
        [
			{
                id: 'observacion',
                header: 'Observacion',
                dataIndex: 'observacion',
                width:400,
                sortable: true						 
			},
            {
                id: 'empleado',
                header: 'Ejecutante',
                dataIndex: 'empleado',
                width:80,
                sortable: true						 
			},
            {
                id: 'departamento',
                header: 'Departamento',
                dataIndex: 'departamento',
                width:100,
                sortable: true						 
			},
            {
                id: 'fecha',
                header: 'Fecha Observacion',
                dataIndex: 'fecha',
                width:112,
                sortable: true						 
			}
		],		
		width: 695,
		height: 300,
        viewConfig: 
        {
            stripeRows: true,
            enableTextSelection: true
        },
        listeners: 
        {
            viewready: function (grid)
            {
                var view = grid.view;

                grid.mon(view,
                {
                    uievent: function (type, view, cell, recordIndex, cellIndex, e)
                    {
                        grid.cellIndex   = cellIndex;
                        grid.recordIndex = recordIndex;
                    }
                });

                grid.tip = Ext.create('Ext.tip.ToolTip',
                {
                    target: view.el,
                    delegate: '.x-grid-cell',
                    trackMouse: true,
                    autoHide: false,
                    renderTo: Ext.getBody(),
                    listeners:
                    {
                        beforeshow: function(tip)
                        {
                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1)
                            {
                                header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                
                                if( header.dataIndex != null )
                                {
                                    var trigger         = tip.triggerElement,
                                        parent          = tip.triggerElement.parentElement,
                                        columnTitle     = view.getHeaderByCell(trigger).text,
                                        columnDataIndex = view.getHeaderByCell(trigger).dataIndex;

                                    if( view.getRecord(parent).get(columnDataIndex) != null )
                                    {
                                        var columnText      = view.getRecord(parent).get(columnDataIndex).toString();
                                        
                                        if (columnText)
                                        {
                                            tip.update(columnText);
                                        }
                                        else
                                        {
                                            return false;
                                        }
                                    }
                                    else
                                    {
                                        return false;
                                    }
                                }
                                else
                                {
                                    return false;
                                }
                            }     
                        }
                    }
                });
                
                grid.tip.on('show', function()
                {
                    var timeout;

                    grid.tip.getEl().on('mouseout', function()
                    {
                        timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                    });

                    grid.tip.getEl().on('mouseover', function(){window.clearTimeout(timeout);});

                    Ext.get(view.el).on('mouseover', function(){window.clearTimeout(timeout);});

                    Ext.get(view.el).on('mouseout', function()
                    {
                        timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                    });
                });
            }
        },
	});
    
	formPanelSeguimiento = Ext.create('Ext.form.Panel', 
    {
        bodyPadding: 5,
        waitMsgTarget: true,
        height: 300,
        width:700,
        layout: 'fit',
        fieldDefaults: 
        {
            labelAlign: 'left',
            msgTarget: 'side'
        },
        items: 
        [{
            xtype: 'fieldset',				
            defaultType: 'textfield',
            items:[ gridSeguimiento ]
        }]
    });
         
	winSeguimientoTarea = Ext.create('Ext.window.Window',
    {
        title: 'Seguimiento Tarea',
        modal: true,
        width: 750,
        height: 400,
        resizable: true,
        layout: 'fit',
        items: [formPanelSeguimiento],
        buttonAlign: 'center',
        buttons:[btncancelar]
	}).show();       
}
/* *********************************************************************** */
/* ********************* FIN VER SEGUIMIENTO TAREA *********************** */
/* *********************************************************************** */