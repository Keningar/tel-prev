var objTxtLatitud = null;
var objTxtLatitudGrados = null;
var objTxtLatitudMinutos = null;
var objTxtLatitudDecimales = null;
var objCmbSeleccionLatitud = null;
var objTxtLatitudUbicacion = null;

var objTxtLongitud = null;
var objTxtLongitudGrados = null;
var objTxtLongitudMinutos = null;
var objTxtLongitudDecimales = null;
var objCmbSeleccionLongitud = null;
var objTxtLongitudUbicacion = null;

Ext.onReady(function()
{
    Ext.tip.QuickTipManager.init();

    var verPoste = function(grid, rowIndex, colIndex) {
        var rec = store.getAt(rowIndex);

        var formVerPoste = Ext.create('Ext.form.Panel', {
            id: 'formVerPoste',
            bodyStyle: 'padding: 20px 10px 0; background:#FFFFFF;',
            bodyPadding: 15,
            autoScroll: false,
            layout: {
                type: 'table',
                columns: 4,
                tableAttrs: {
                    style: {
                        width: '90%',
                        height: '90%'
                    }
                },
                tdAttrs: {
                    style: ' padding: 5px;',
                    align: 'left',
                    valign: 'middle'
                }
            },
            items: []
        });

        var objLblNombreElemento = Utils.objLabel();
        objLblNombreElemento.style = Utils.STYLE_BOLD;
        objLblNombreElemento.text = "Nombre Elemento:";
        var objLblValorNombreElemento = Utils.objLabel();
        objLblValorNombreElemento.text = rec.get('nombreElemento');

        var objLblEstado = Utils.objLabel();
        objLblEstado.style = Utils.STYLE_BOLD;
        objLblEstado.text = "Estado: ";
        var objLblValorEstado = Utils.objLabel();
        objLblValorEstado.text = rec.get('estado');

        var objLblDescElemento = Utils.objLabel();
        objLblDescElemento.style = Utils.STYLE_BOLD;
        objLblDescElemento.text = "Descripción: ";
        var objLblValorDescElemento = Utils.objLabel();
        objLblValorDescElemento.text = rec.get('descripcionElemento');

        var objLblTipo = Utils.objLabel();
        objLblTipo.style = Utils.STYLE_BOLD;
        objLblTipo.text = "Modelo: ";
        var objLblValorTipo = Utils.objLabel();
        objLblValorTipo.text = rec.get('tipoElemento');

        var objLblCanton = Utils.objLabel();
        objLblCanton.style = Utils.STYLE_BOLD;
        objLblCanton.text = "Cantón: ";
        var objLblValorCanton = Utils.objLabel();
        objLblValorCanton.text = rec.get('cantonNombre');

        var objLblDireccion = Utils.objLabel();
        objLblDireccion.style = Utils.STYLE_BOLD;
        objLblDireccion.text = "Dirección: ";
        var objLblValorDireccion = Utils.objLabel();
        objLblValorDireccion.text = rec.get('direccionUbicacion');

        var objLblPropietario = Utils.objLabel();
        objLblPropietario.style = Utils.STYLE_BOLD;
        objLblPropietario.text = "Propietario: ";
        var objLblValorPropietario = Utils.objLabel();
        objLblValorPropietario.text = rec.get('propietario');

        var objLblParroquia = Utils.objLabel();
        objLblParroquia.style = Utils.STYLE_BOLD;
        objLblParroquia.text = "Parroquia: ";
        var objLblValorParroquia = Utils.objLabel();
        objLblValorParroquia.text = rec.get('nombreParroquia');

        var objLblLatitud = Utils.objLabel();
        objLblLatitud.style = Utils.STYLE_BOLD;
        objLblLatitud.text = "Latitud: ";
        var objLblValorLatitud = Utils.objLabel();
        objLblValorLatitud.text = rec.get('latitudUbicacion');

        var objLblLongitud = Utils.objLabel();
        objLblLongitud.style = Utils.STYLE_BOLD;
        objLblLongitud.text = "Longitud: ";
        var objLblValorLongitud = Utils.objLabel();
        objLblValorLongitud.text = rec.get('longitudUbicacion');

        var objLblCosto = Utils.objLabel();
        objLblCosto.style = Utils.STYLE_BOLD;
        objLblCosto.text = "Costo: ";
        var objLblValorCosto = Utils.objLabel();
        objLblValorCosto.text = rec.get('costoElemento');

        var objLblAsnm = Utils.objLabel();
        objLblAsnm.style = Utils.STYLE_BOLD;
        objLblAsnm.text = "Altura sobre el nivel del mar: ";
        var objLblValorAsnm = Utils.objLabel();
        objLblValorAsnm.text = rec.get('alturaSnm');
        
        var objLblElementoContenedor = Utils.objLabel();
        objLblElementoContenedor.style = Utils.STYLE_BOLD;
        objLblElementoContenedor.text = "Elemento Contenedor: ";
        var objLblValorElementoContenedor = Utils.objLabel();
        objLblValorElementoContenedor.text = rec.get('elementoContenedor');
        
        var objLblTipoLugar = Utils.objLabel();
        objLblTipoLugar.style = Utils.STYLE_BOLD;
        objLblTipoLugar.text = "Tipo Lugar: ";
        var objLblValorTipoLugar = Utils.objLabel();
        objLblValorTipoLugar.text = rec.get('tipoLugar');
        
        var objLblNivel = Utils.objLabel();
        objLblNivel.style = Utils.STYLE_BOLD;
        objLblNivel.text = "Nivel: ";
        var objLblValorNivel = Utils.objLabel();
        objLblValorNivel.text = rec.get('nivel'); 
        
        var objLblUbicadoEn = Utils.objLabel();
        objLblUbicadoEn.style = Utils.STYLE_BOLD;
        objLblUbicadoEn.text = "Ubicado En: ";
        var objValorUbicadoEn = Utils.objLabel();
        objValorUbicadoEn.text = rec.get('ubicadoEn');          

        formVerPoste.add(objLblNombreElemento);
        formVerPoste.add(objLblValorNombreElemento);
        formVerPoste.add(objLblEstado);
        formVerPoste.add(objLblValorEstado);
        formVerPoste.add(objLblDescElemento);
        formVerPoste.add(objLblValorDescElemento);
        formVerPoste.add(objLblTipo);
        formVerPoste.add(objLblValorTipo);
        formVerPoste.add(objLblCanton);
        formVerPoste.add(objLblValorCanton);
        formVerPoste.add(objLblDireccion);
        formVerPoste.add(objLblValorDireccion);
        formVerPoste.add(objLblPropietario);
        formVerPoste.add(objLblValorPropietario);
        formVerPoste.add(objLblParroquia);
        formVerPoste.add(objLblValorParroquia);
        formVerPoste.add(objLblLatitud);
        formVerPoste.add(objLblValorLatitud);
        formVerPoste.add(objLblLongitud);
        formVerPoste.add(objLblValorLongitud);
        formVerPoste.add(objLblCosto);
        formVerPoste.add(objLblValorCosto);
        formVerPoste.add(objLblAsnm);
        formVerPoste.add(objLblValorAsnm);        
        formVerPoste.add(objLblTipoLugar);
        formVerPoste.add(objLblValorTipoLugar);
        formVerPoste.add(objLblElementoContenedor);
        formVerPoste.add(objLblValorElementoContenedor);  
        formVerPoste.add(objLblNivel);
        formVerPoste.add(objLblValorNivel);  
        formVerPoste.add(objLblUbicadoEn);
        formVerPoste.add(objValorUbicadoEn);        
        

        var storeHistorial = new Ext.data.Store({
            total: 'total',
            proxy: {
                type: 'ajax',
                url: url_getHistorialElementos,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            fields:
                [
                    {name: 'estado_elemento', mapping: 'estado_elemento'},
                    {name: 'fe_creacion', mapping: 'fe_creacion'},
                    {name: 'usr_creacion', mapping: 'usr_creacion'},
                    {name: 'observacion', mapping: 'observacion'}
                ]
        });

        storeHistorial.load({params: {
                idElemento: rec.get('idElemento')
            }});

        var formVerHistorialPoste = Ext.create('Ext.grid.Panel', {
            width: 930,
            height: 350,
            store: storeHistorial,
            loadMask: true,
            frame: false,
            bodyStyle: {
                background: '#fff'
            },
            collapsible: true,
            collapsed: false,
            title: 'Historial de Elemento',
            viewConfig: {enableTextSelection: true},
            columns: [
                {
                    id: 'estado_elemento',
                    header: 'Estado',
                    dataIndex: 'estado_elemento',
                    width: 100,
                    sortable: true
                },
                {
                    id: 'fe_creacion',
                    header: 'Fecha Creación',
                    dataIndex: 'fe_creacion',
                    width: 100,
                    sortable: true
                },
                {
                    id: 'usr_creacion',
                    header: 'Usuario Creación',
                    dataIndex: 'usr_creacion',
                    width: 150,
                    sortable: true
                },
                {
                    id: 'observacion',
                    header: 'Observación',
                    dataIndex: 'observacion',
                    width: 300,
                    sortable: true
                }
            ]
        });

        btnregresar = Ext.create('Ext.Button', {
            text: 'Cerrar',
            cls: 'x-btn-rigth',
            handler: function() {
                windowVerPoste.destroy();
            }
        });

        var windowVerPoste = Ext.widget('window', {
            title: 'Información de elemento ' + rec.get('nombreElemento'),
            id: 'windowVerPoste',
            height: 455,
            width: 900,
            modal: true,
            resizable: false,
            closeAction: 'destroy',
            items: [formVerPoste,
                formVerHistorialPoste],
            buttonAlign: 'center',
            buttons: [btnregresar]
        });
        windowVerPoste.show();
    };

    var btnVerPoste = Ext.create('Ext.button.Button', {
        text: 'Ver',
        scope: this,
        style: {
            marginTop: '10px'
        },
        getClass: function(v, meta, rec)
        {
            return 'button-grid-show';
        },
        tooltip: 'Ver',
        handler: verPoste
    });

    var editarPoste = function(grid, rowIndex, colIndex) {
        var rec = store.getAt(rowIndex);

        Ext.define('modelCanton', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'canton_id', type: 'int'},
                {name: 'nombreCanton', type: 'string'}
            ]
        });

        var storeCanton = Ext.create('Ext.data.Store', {
            id: 'storeIdCanton',
            model: 'modelCanton',
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url: urlGetCantonJurisdiccion,
                timeout: 600000,
                reader: {
                    type: 'json',
                    root: 'encontrados'
                },
                simpleSortMode: true
            }
        });

        Ext.define('modelParroquia', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'id_parroquia', type: 'int'},
                {name: 'nombre_parroquia', type: 'string'}
            ]
        });

        var storeParroquia = Ext.create('Ext.data.Store', {
            id: 'storeIdParroquia',
            model: 'modelParroquia',
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url: urlGetParroquiaCanton,
                timeout: 600000,
                reader: {
                    type: 'json',
                    root: 'encontrados'
                },
                simpleSortMode: true
            }
        });

        var objComboJurisdiccion = function() {

            Ext.define('modelJurisdiccion', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'idJurisdiccion', type: 'int'},
                    {name: 'nombreJurisdiccion', type: 'string'},
                    {name: 'estado', type: 'string'}
                ]
            });

            var storeJurisdiccion = Ext.create('Ext.data.Store', {
                id: 'storeIdJurisdiccion',
                model: 'modelJurisdiccion',
                autoLoad: true,
                proxy: {
                    type: 'ajax',
                    url: urlGetJurisdiccion,
                    timeout: 600000,
                    reader: {
                        type: 'json',
                        root: 'encontrados'
                    },
                    extraParams: {
                        estado: 'Eliminado'
                    },
                    simpleSortMode: true
                }
            });

            return Ext.create('Ext.form.ComboBox', {
                store: storeJurisdiccion,
                queryMode: 'local',
                displayField: 'nombreJurisdiccion',
                valueField: 'idJurisdiccion',
                listeners: {
                    select: function(records) {
                        objCmbCanton.clearValue();
                        objCmbParroquia.clearValue();
                        storeCanton.loadData([], false);
                        storeCanton.load({params: {
                                jurisdiccionId: objCmbJurisdiccion.getValue(),
                                estado: 'Activo'
                            }});
                    }
                }
            });
        };

        var objComboCanton = function() {
            return Ext.create('Ext.form.ComboBox', {
                store: storeCanton,
                queryMode: 'local',
                displayField: 'nombreCanton',
                valueField: 'canton_id',
                listeners: {
                    select: function(records) {
                        objCmbParroquia.clearValue();
                        storeParroquia.loadData([], false);
                        storeParroquia.load({params: {
                                cantonId: objCmbCanton.getValue()
                            }});

                    }
                }
            });
        };

        var objComboParroquia = function() {
            return Ext.create('Ext.form.ComboBox', {
                store: storeParroquia,
                queryMode: 'local',
                displayField: 'nombre_parroquia',
                valueField: 'id_parroquia'
            });
        };        
        
        var objComboNivel = function() {

                var myStoreNivel = Ext.create('Ext.data.Store',{
                    fields:['id','nombre'],
                    data:[
                        {id:'1',nombre:'NIVEL 1'},
                        {id:'2',nombre:'NIVEL 2'}
                    ]
                });

                return Ext.create('Ext.form.ComboBox', {
                    store:        myStoreNivel,
                    queryMode:    'local',
                    displayField: 'nombre',
                    valueField:   'id',
                });
        };
        
        var objChkFactibilidad = function () {

            return Ext.create('Ext.form.field.Checkbox', {
                xtype: 'checkbox',
                checked: false,
                listeners: {
                    change: function () {
                        if (objChkFactible.checked)
                        {
                            objCmbNivel.setValue("2");
                        }
                        else
                        {
                            objCmbNivel.setValue("");
                        }    
                    }
                }
            });
        };
        
        var objComboPropietario = function() {

            Ext.define('modelPropietario', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'id_propietario', type: 'int'},
                    {name: 'nombre_propietario', type: 'string'}
                ]
            });
            var storePropietario = Ext.create('Ext.data.Store', {
                id: 'storeIdPersonaEmpresaRol',
                model: 'modelPropietario',
                autoLoad: true,
                proxy: {
                    type: 'ajax',
                    url: url_getPropietarios,
                    timeout: 600000,
                    reader: {
                        type: 'json',
                        root: 'encontrados'
                    },
                    extraParams: {
                    },
                    simpleSortMode: true
                }
            });

            return Ext.create('Ext.form.ComboBox', {
                store: storePropietario,
                queryMode: 'local',
                displayField: 'nombre_propietario',
                valueField: 'id_propietario'
            });
        };
        
        var objComboTipoLugar = function() {

                var myStore = Ext.create('Ext.data.Store',{
                    fields:['id','nombre'],
                    data:[
                        {id:'AEREO',nombre:'AEREO'},
                        {id:'SOTERRADO',nombre:'SOTERRADO'}
                    ]
                });

                return Ext.create('Ext.form.ComboBox', {
                    store:        myStore,
                    queryMode:    'local',
                    displayField: 'nombre',
                    valueField:   'id',
                });
        };     
        
        var objComboUbicadoEn = function() {

                    var myStore = Ext.create('Ext.data.Store',{
                        fields:['id','nombre'],
                        data:[
                            {id:'EDIFICIO'      ,nombre:'EDIFICIO'},
                            {id:'PEDESTAL'      ,nombre:'PEDESTAL'},
                            {id:'POZO'          ,nombre:'POZO'},
                            {id:'POSTE'         ,nombre:'POSTE'},
                            {id:'URBANIZACION'  ,nombre:'URBANIZACION'},
                            {id:'CONJUNTO'      ,nombre:'CONJUNTO'}
                        ]
                    });

                    return Ext.create('Ext.form.ComboBox', {
                        store:        myStore,
                        queryMode:    'local',
                        displayField: 'nombre',
                        valueField:   'id',
                    });
            };    
        
        
            var storeElementoContenedor = new Ext.data.Store({
                id: 'storeElementoContenedor',
                pageSize: 100,
                proxy: {
                    type: 'ajax',
                    timeout: 400000,
                    url: urlGetElementoTipo,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    }
                },
                fields:
                    [
                        {name: 'id_elemento', mapping: 'id_elemento'},
                        {name: 'nombre_elemento', mapping: 'nombre_elemento'}
                    ]
            });           
            
            var objComboElementoContenedor = function() {

                    return Ext.create('Ext.form.ComboBox', {
                        store:        storeElementoContenedor,
                        queryMode:    'local',
                        displayField: 'nombre_elemento',
                        valueField:   'id_elemento',
                    });
            };                
        

        var objComboPuntosCardinales = function(strTipoCardinal) {

            var arrayNorteSur = [{"strCod": "NULL", "strNombre": "Seleccione.."},
                {"strCod": "ESTE", "strNombre": "Este"},
                {"strCod": "OESTE", "strNombre": "Oeste"}];
            if (!Ext.isEmpty(strTipoCardinal) && 'NS' === strTipoCardinal)
            {
                arrayNorteSur = [{"strCod": "NULL", "strNombre": "Seleccione.."}, {"strCod": "NORTE", "strNombre": "Norte"}, {"strCod": "SUR", "strNombre": "Sur"}];
            }
            var objStorePCadinales = Ext.create('Ext.data.Store', {
                fields: ['strCod', 'strNombre'],
                data: arrayNorteSur
            });

            return Ext.create('Ext.form.ComboBox', {
                store: objStorePCadinales,
                queryMode: 'local',
                displayField: 'strNombre',
                valueField: 'strCod'
            });
        };

        var objStoreTipo = Ext.create('Ext.data.Store', {
            id: 'storeIdTipo',
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: strUrlGetModelo,
                timeout: 600000,
                reader: {
                    type: 'json',
                    root: 'encontrados'
                },
                extraParams: {
                    idMarca: '',
                    tipoElemento: rec.get('tipoElemento')

                },
                simpleSortMode: true
            },
            fields: [
                {name: 'idModeloElemento', mapping: 'idModeloElemento'},
                {name: 'nombreModeloElemento', mapping: 'nombreModeloElemento'}
            ]
        });    

        var objComboTipo = function () {

            return Ext.create('Ext.form.ComboBox', {
                store:        objStoreTipo,
                queryMode:    'local',
                displayField: 'nombreModeloElemento',
                valueField:   'idModeloElemento',
            });
        };

        var objBotonMapa = function() {
            return Ext.create('Ext.Button', {
                listeners: {
                    click: function() {
                        muestraMapa();
                    }
                }
            });
        };

        formEditElemento = Ext.create('Ext.form.Panel', {
            id: 'formEditElemento',
            bodyStyle: 'padding: 20px 10px 0; background:#FFFFFF;',
            bodyPadding: 15,
            autoScroll: false,
            layout: {
                type: 'table',
                columns: 12,
                pack: 'center',
                tableAttrs: {
                    style: {
                        width: '90%',
                        height: '90%'
                    }
                },
                tdAttrs: {
                    align: 'left',
                    valign: 'middle'
                }
            },
            buttonAlign: 'center',
            buttons: [
                {
                    text: 'Guardar',
                    name: 'btnGuardar',
                    id: 'idBtnGuardar',
                    disabled: false,
                    handler: function() {
                        if (objChkFactible.checked)
                        {
                            objCmbNivel.enable();
                        }

                        var form = formEditElemento.getForm();
                        if (form.isValid())
                        {
                            var data = form.getValues();
                            Ext.get(document.body).mask('Editando datos...');
                            Ext.Ajax.request({
                                url: urlEditPoste,
                                method: 'POST',
                                params: data,
                                success: function(response) {
                                    Ext.get(document.body).unmask();
                                    var json = Ext.JSON.decode(response.responseText);
                                    Ext.Msg.alert('Mensaje', json.strMessageStatus);
                                    store.load();
                                    windowEditarElemento.destroy();
                                },
                                failure: function(result) {
                                    Ext.get(document.body).unmask();
                                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                }
                            });
                        }

                    }
                },
                {
                    text: 'Cancelar',
                    handler: function() {
                        windowEditarElemento.destroy();
                    }
                }]
        });

        var windowEditarElemento = Ext.widget('window', {
            title: 'Editar Elemento',
            id: 'windowEditarElemento',
            height: 500,
            width: 820,
            modal: true,
            resizable: false,
            closeAction: 'destroy',
            items: [formEditElemento],
            buttonAlign: 'center'
        });
        windowEditarElemento.show();
        
        var intWidth = 325;
        var objTxtNombreElemento = new Ext.form.TextField({
        style      : Utils.GREY_BOLD_COLOR,
        id         : 'objTxtNombreElemento',
        name       : 'objTxtNombreElemento',
        fieldLabel : '*Nombre',
        colspan    : 6,
        width      : intWidth,
        allowBlank : false,
        blankText  : 'Ingrese nombre por favor',
        enableKeyEvents: true,
            listeners:
            {
                keypress: function(me, e)
                {
                    validador(me, e);
                }
            }
        });
        objTxtNombreElemento.setValue(rec.get('nombreElemento'));

        var objCmbPropietario = objComboPropietario();
        objCmbPropietario.style = Utils.GREY_BOLD_COLOR;
        objCmbPropietario.id = 'objCmbPropietario';
        objCmbPropietario.name = 'objCmbPropietario';
        objCmbPropietario.fieldLabel = "*Propietario";
        objCmbPropietario.colspan = 6;
        objCmbPropietario.width = intWidth;
        objCmbPropietario.allowBlank = false;
        objCmbPropietario.blankText = 'Ingrese propietario por favor';
        objCmbPropietario.setValue(rec.get('propietario'));
        objCmbPropietario.setRawValue(rec.get('propietario'));

        var objTarDescripcionElemento = Utils.objTextArea();
        objTarDescripcionElemento.style = Utils.GREY_BOLD_COLOR;
        objTarDescripcionElemento.id = 'objTarDescripcionElemento';
        objTarDescripcionElemento.name = 'objTarDescripcionElemento';
        objTarDescripcionElemento.fieldLabel = "*Descripción";
        objTarDescripcionElemento.colspan = 6;
        objTarDescripcionElemento.width = intWidth;
        objTarDescripcionElemento.allowBlank = false;
        objTarDescripcionElemento.blankText = 'Ingrese descripción por favor';
        objTarDescripcionElemento.setValue(rec.get('descripcionElemento'));

        var objCmbTipoElemento = objComboTipo();
        objCmbTipoElemento.style = Utils.GREY_BOLD_COLOR;
        objCmbTipoElemento.id = 'objCmbTipoElemento';
        objCmbTipoElemento.name = 'objCmbTipoElemento';
        objCmbTipoElemento.fieldLabel = "*Modelo";
        objCmbTipoElemento.colspan = 6;
        objCmbTipoElemento.width = intWidth;
        objCmbTipoElemento.allowBlank = false;
        objCmbTipoElemento.blankText = 'Ingrese tipo por favor';
        objCmbTipoElemento.setValue(rec.get('modeloElementoId'));
        objCmbTipoElemento.setRawValue(rec.get('modelo_elemento'));

        var objCmbJurisdiccion = objComboJurisdiccion();
        objCmbJurisdiccion.style = Utils.GREY_BOLD_COLOR;
        objCmbJurisdiccion.id = 'objCmbJurisdiccion';
        objCmbJurisdiccion.name = 'objCmbJurisdiccion';
        objCmbJurisdiccion.fieldLabel = "*Jurisdicciones";
        objCmbJurisdiccion.colspan = 12;
        objCmbJurisdiccion.width = intWidth;
        objCmbJurisdiccion.allowBlank = false;
        objCmbJurisdiccion.blankText = 'Ingrese jurisdicción por favor';
        objCmbJurisdiccion.setValue(rec.get('jurisdiccionId'));
        objCmbJurisdiccion.setRawValue(rec.get('jurisdiccionNombre'));

        storeCanton.load({params: {
                jurisdiccionId: rec.get('jurisdiccionId'),
                estado: 'Activo'
            }});

        var objCmbCanton = objComboCanton();
        objCmbCanton.style = Utils.GREY_BOLD_COLOR;
        objCmbCanton.id = 'objCmbCanton';
        objCmbCanton.name = 'objCmbCanton';
        objCmbCanton.fieldLabel = "*Cantón";
        objCmbCanton.colspan = 6;
        objCmbCanton.width = intWidth;
        objCmbCanton.allowBlank = false;
        objCmbCanton.blankText = 'Ingrese cantón por favor';
        objCmbCanton.setValue(rec.get('cantonId'));
        objCmbCanton.setRawValue(rec.get('nombreCanton'));

        storeParroquia.load({params: {
                cantonId: rec.get('cantonId')
            }});

        var objTxtCosto = Utils.objText();
        objTxtCosto.style = Utils.GREY_BOLD_COLOR;
        objTxtCosto.id = 'objTxtCosto';
        objTxtCosto.name = 'objTxtCosto';
        objTxtCosto.fieldLabel = "*Costo";
        objTxtCosto.colspan = 6;
        objTxtCosto.width = intWidth;
        objTxtCosto.allowBlank = false;
        objTxtCosto.maskRe = /[\d\.]/;
        objTxtCosto.regex = /[0-9]+(\.[0-9][0-9]?)?/;
        objTxtCosto.blankText = 'Ingrese costo por favor';
        objTxtCosto.regexText = 'Costo - Ingrese solo numeros';
        objTxtCosto.setValue(rec.get('costoElemento'));


        var objCmbParroquia = objComboParroquia();
        objCmbParroquia.style = Utils.GREY_BOLD_COLOR;
        objCmbParroquia.id = 'objCmbParroquia';
        objCmbParroquia.name = 'objCmbParroquia';
        objCmbParroquia.fieldLabel = "*Parroquia";
        objCmbParroquia.colspan = 12;
        objCmbParroquia.width = intWidth;
        objCmbParroquia.allowBlank = false;
        objCmbParroquia.blankText = 'Ingrese parroquia por favor';
        objCmbParroquia.setValue(rec.get('parroquiaId'));
        objCmbParroquia.setRawValue(rec.get('nombreParroquia'));

        var objTxtIdParroquia = Utils.objText();
        objTxtIdParroquia.id = 'objTxtIdParroquia';
        objTxtIdParroquia.name = 'objTxtIdParroquia';
        objTxtIdParroquia.hidden = true;
        objTxtIdParroquia.setValue(rec.get('parroquiaId'));

        var objTxtIdElemento = Utils.objText();
        objTxtIdElemento.id = 'objTxtIdElemento';
        objTxtIdElemento.name = 'objTxtIdElemento';
        objTxtIdElemento.hidden = true;
        objTxtIdElemento.setValue(rec.get('idElemento'));

        var objTxtIdUbicacion = Utils.objText();
        objTxtIdUbicacion.id = 'objTxtIdUbicacion';
        objTxtIdUbicacion.name = 'objTxtIdUbicacion';
        objTxtIdUbicacion.hidden = true;
        objTxtIdUbicacion.setValue(rec.get('ubicacionId'));

        var objTxtDireccion = Utils.objText();
        objTxtDireccion.style = Utils.GREY_BOLD_COLOR;
        objTxtDireccion.id = 'objTxtDireccion';
        objTxtDireccion.name = 'objTxtDireccion';
        objTxtDireccion.fieldLabel = "*Dirección";
        objTxtDireccion.colspan = 6;
        objTxtDireccion.width = intWidth;
        objTxtDireccion.allowBlank = false;
        objTxtDireccion.blankText = 'Ingrese dirección por favor';
        objTxtDireccion.setValue(rec.get('direccionUbicacion'));

        var objTxtAltura = Utils.objText();
        objTxtAltura.style = Utils.GREY_BOLD_COLOR;
        objTxtAltura.id = 'objTxtAltura';
        objTxtAltura.name = 'objTxtAltura';
        objTxtAltura.fieldLabel = "*Altura Sobre Nivel Mar";
        objTxtAltura.colspan = 6;
        objTxtAltura.width = intWidth;
        objTxtAltura.allowBlank = false;
        objTxtAltura.maskRe = /[\d\.]/;
        objTxtAltura.regex = /[0-9]+(\.[0-9][0-9]?)?/;
        objTxtAltura.blankText = 'Ingrese altura sobre el nivel del mar por favor';
        objTxtAltura.regexText = 'Altura sobre el nivel del mar - Ingrese solo numeros';
        objTxtAltura.setValue(rec.get('alturaSnm'));
        
        
        var objCmbElementoContenedor        = objComboElementoContenedor();
        objCmbElementoContenedor.style      = Utils.GREY_BOLD_COLOR;
        objCmbElementoContenedor.id         = 'objCmbElementoContenedor';
        objCmbElementoContenedor.name       = 'objCmbElementoContenedor';
        objCmbElementoContenedor.fieldLabel = "Elemento Contenedor"; 
        objCmbElementoContenedor.colspan    = 6;
        objCmbElementoContenedor.width      = intWidth; 
        objCmbElementoContenedor.blankText  = 'Ingrese tipo por favor'; 
        objCmbElementoContenedor.queryMode  = ''; 
        objCmbElementoContenedor.queryMode  = 'remote'; 
        objCmbElementoContenedor.lazyRender = true; 
        objCmbElementoContenedor.loadingText= 'Buscando...'; 
        objCmbElementoContenedor.minChars   = 3; 
        objCmbElementoContenedor.setValue(rec.get('idElementoContenedor'));
        objCmbElementoContenedor.setRawValue(rec.get('elementoContenedor'));
        
        
        var objCmbTipoLugar             = objComboTipoLugar();
        objCmbTipoLugar.style           = Utils.GREY_BOLD_COLOR;
        objCmbTipoLugar.id              = 'objCmbTipoLugar';
        objCmbTipoLugar.name            = 'objCmbTipoLugar';
        objCmbTipoLugar.fieldLabel      = "*Tipo Lugar";        
        objCmbTipoLugar.colspan         = 6;
        objCmbTipoLugar.width           = intWidth; 
        objCmbTipoLugar.allowBlank      = false;
        objCmbTipoLugar.blankText       = 'Ingrese propietario por favor';
        objCmbTipoLugar.setValue(rec.get('tipoLugar'));
        objCmbTipoLugar.setRawValue(rec.get('tipoLugar')); 
        
        var objCmbNivel             = objComboNivel();
        objCmbNivel.style           = Utils.GREY_BOLD_COLOR;
        objCmbNivel.id              = 'objCmbNivel';
        objCmbNivel.name            = 'objCmbNivel';
        objCmbNivel.fieldLabel      = "*Nivel";        
        objCmbNivel.colspan         = 6;
        objCmbNivel.width           = intWidth; 
        objCmbNivel.allowBlank      = false;
        objCmbNivel.blankText       = 'Ingrese Informacion...';        
        objCmbNivel.setValue(rec.get('nivel'));
        
        var objChkFactible          = objChkFactibilidad();
        objChkFactible.style        = Utils.GREY_BOLD_COLOR;
        objChkFactible.id           = 'objChkFactible';
        objChkFactible.name         = 'objChkFactible';
        objChkFactible.fieldLabel   = "Factibilidad";        
        objChkFactible.colspan      = 6;
        objChkFactible.width        = intWidth;
        
        var objUbicadoEn             = objComboUbicadoEn();
        objUbicadoEn.style           = Utils.GREY_BOLD_COLOR;
        objUbicadoEn.id              = 'objUbicadoEn';
        objUbicadoEn.name            = 'objUbicadoEn';
        objUbicadoEn.fieldLabel      = "*Ubicado En";        
        objUbicadoEn.colspan         = 6;
        objUbicadoEn.width           = intWidth; 
        objUbicadoEn.allowBlank      = true;
        objUbicadoEn.blankText       = 'Ingrese tipo lugar por favor';            
        objUbicadoEn.setValue(rec.get('ubicadoEn'));

        var objLblLatitud = Utils.objLabel();
        objLblLatitud.style = Utils.GREY_BOLD_COLOR;
        objLblLatitud.text = 'Coordenadas Latitud';
        objTxtLatitud = Utils.objText();
        objTxtLatitud.id = 'objTxtLatitud';
        objTxtLatitud.name = 'objTxtLatitud';
        objTxtLatitud.width = 40;
        objTxtLatitud.maskRe = /[\d]/;
        objTxtLatitud.regex = /^(?:36[0]|3[0-5][0-9]|[12][0-9][0-9]|[1-9]?[0-9])?$/;
        objTxtLatitud.regexText = 'Grados - Ingrese solo numeros entre 0-360';

        var objLblLatitudGrados = Utils.objLabel();
        objLblLatitudGrados.style = Utils.GREY_BOLD_COLOR;
        objLblLatitudGrados.text = '°';
        objTxtLatitudGrados = Utils.objText();
        objTxtLatitudGrados.style = Utils.GREY_BOLD_COLOR;
        objTxtLatitudGrados.id = 'objTxtLatitudGrados';
        objTxtLatitudGrados.name = 'objTxtLatitudGrados';
        objTxtLatitudGrados.labelStyle = 'padding: 0px 0px;';
        objTxtLatitudGrados.width = 40;
        objTxtLatitudGrados.maskRe = /[\d]/;
        objTxtLatitudGrados.regex = /^[1-5]?[0-9]$/;
        objTxtLatitudGrados.regexText = 'Minutos - Ingrese solo numeros entre 0-59';

        var objLblLatitudMinutos = Utils.objLabel();
        objLblLatitudMinutos.style = Utils.GREY_BOLD_COLOR;
        objLblLatitudMinutos.text = "'";
        objTxtLatitudMinutos = Utils.objText();
        objTxtLatitudMinutos.style = Utils.GREY_BOLD_COLOR;
        objTxtLatitudMinutos.id = 'objTxtLatitudMinutos';
        objTxtLatitudMinutos.name = 'objTxtLatitudMinutos';
        objTxtLatitudMinutos.width = 40;
        objTxtLatitudMinutos.maskRe = /[\d]/;
        objTxtLatitudMinutos.regex = /^[1-5]?[0-9]$/;
        objTxtLatitudMinutos.regexText = 'Segundos - Ingrese solo numeros entre 0-59';

        var objLblLatitudDecimales = Utils.objLabel();
        objLblLatitudDecimales.style = Utils.GREY_BOLD_COLOR;
        objLblLatitudDecimales.text = '.';
        objTxtLatitudDecimales = Utils.objText();
        objTxtLatitudDecimales.id = 'objTxtLatitudDecimales';
        objTxtLatitudDecimales.name = 'objTxtLatitudDecimales';
        objTxtLatitudDecimales.width = 40;
        objTxtLatitudDecimales.maskRe = /[\d]/;
        objTxtLatitudDecimales.regex = /^\d{1,3}$/;
        objTxtLatitudDecimales.regexText = 'Décimas Segundos - Ingrese solo numeros entre 0-999';

        var objLblSeleccionLatitud = Utils.objLabel();
        objLblSeleccionLatitud.style = Utils.GREY_BOLD_COLOR;
        objLblSeleccionLatitud.text = '"';
        objCmbSeleccionLatitud = objComboPuntosCardinales('NS');
        objCmbSeleccionLatitud.setValue('NULL');
        objCmbSeleccionLatitud.id = 'objCmbSeleccionLatitud';
        objCmbSeleccionLatitud.name = 'objCmbSeleccionLatitud';
        objCmbSeleccionLatitud.width = 70;

        var objTxtAltura = Utils.objText();
        objTxtAltura.style = Utils.GREY_BOLD_COLOR;
        objTxtAltura.id = 'objTxtAltura';
        objTxtAltura.name = 'objTxtAltura';
        objTxtAltura.fieldLabel = "*Altura Sobre Nivel Mar";
        objTxtAltura.colspan = 6;
        objTxtAltura.width = intWidth;
        objTxtAltura.allowBlank = false;
        objTxtAltura.maskRe = /[\d\.]/;
        objTxtAltura.regex = /[0-9]+(\.[0-9][0-9]?)?/;
        objTxtAltura.blankText = 'Ingrese altura sobre el nivel del mar por favor';
        objTxtAltura.regexText = 'Altura sobre el nivel del mar - Ingrese solo numeros';
        objTxtAltura.setValue(rec.get('alturaSnm'));

        objTxtLatitudUbicacion = Utils.objText();
        objTxtLatitudUbicacion.style = Utils.GREY_BOLD_COLOR;
        objTxtLatitudUbicacion.id = 'objTxtLatitudUbicacion';
        objTxtLatitudUbicacion.name = 'objTxtLatitudUbicacion';
        objTxtLatitudUbicacion.fieldLabel = "*Latitud";
        objTxtLatitudUbicacion.colspan = 6;
        objTxtLatitudUbicacion.width = intWidth;
        objTxtLatitudUbicacion.allowBlank = false;
        objTxtLatitudUbicacion.maskRe = /[\d\.]/;
        objTxtLatitudUbicacion.regex = /[0-9]+(\.[0-9][0-9]?)?/;
        objTxtLatitudUbicacion.blankText = 'Ingrese altura sobre el nivel del mar por favor';
        objTxtLatitudUbicacion.regexText = 'Altura sobre el nivel del mar - Ingrese solo numeros';
        objTxtLatitudUbicacion.setValue(rec.get('latitudUbicacion'));
        
        


        var objContainerLatitud = Ext.create('Ext.container.Container', {
            colspan: 6,
            bodyStyle: 'margin: 1px 20px;',
            layout: {
                tdAttrs: {
                    style: 'padding: 1px 2px;'
                },
                type: 'table',
                columns: 12,
                pack: 'center'
            },
            items: [
                objLblLatitud,
                objTxtLatitud,
                objLblLatitudGrados,
                objTxtLatitudGrados,
                objLblLatitudMinutos,
                objTxtLatitudMinutos,
                objLblLatitudDecimales,
                objTxtLatitudDecimales,
                objLblSeleccionLatitud,
                objCmbSeleccionLatitud
            ]
        });

        var objLblLongitud = Utils.objLabel();
        objLblLongitud.style = Utils.GREY_BOLD_COLOR;
        objLblLongitud.text = 'Coordenadas Longitud';
        objTxtLongitud = Utils.objText();
        objTxtLongitud.id = 'objTxtLongitud';
        objTxtLongitud.name = 'objTxtLongitud';
        objTxtLongitud.width = 40;
        objTxtLongitud.maskRe = /[\d]/;
        objTxtLongitud.regex = /^(?:36[0]|3[0-5][0-9]|[12][0-9][0-9]|[1-9]?[0-9])?$/;
        objTxtLongitud.regexText = 'Grados - Ingrese solo numeros entre 0-360';

        var objLblLongitudGrados = Utils.objLabel();
        objLblLongitudGrados.style = Utils.GREY_BOLD_COLOR;
        objLblLongitudGrados.text = '°';
        objTxtLongitudGrados = Utils.objText();
        objTxtLongitudGrados.id = 'objTxtLongitudGrados';
        objTxtLongitudGrados.name = 'objTxtLongitudGrados';
        objTxtLongitudGrados.width = 40;
        objTxtLongitudGrados.maskRe = /[\d]/;
        objTxtLongitudGrados.regex = /^[1-5]?[0-9]$/;
        objTxtLongitudGrados.regexText = 'Minutos - Ingrese solo numeros entre 0-59';

        var objLblLongitudMinutos = Utils.objLabel();
        objLblLongitudMinutos.text = "'";
        objTxtLongitudMinutos = Utils.objText();
        objTxtLongitudMinutos.style = Utils.GREY_BOLD_COLOR;
        objTxtLongitudMinutos.id = 'objTxtLongitudMinutos';
        objTxtLongitudMinutos.name = 'objTxtLongitudMinutos';
        objTxtLongitudMinutos.width = 40;
        objTxtLongitudMinutos.maskRe = /[\d]/;
        objTxtLongitudMinutos.regex = /^[1-5]?[0-9]$/;
        objTxtLongitudMinutos.regexText = 'Segundos - Ingrese solo numeros entre 0-59';

        var objLblLongitudDecimales = Utils.objLabel();
        objLblLongitudDecimales.style = Utils.GREY_BOLD_COLOR;
        objLblLongitudDecimales.text = '.';
        objTxtLongitudDecimales = Utils.objText();
        objTxtLongitudDecimales.id = 'objTxtLongitudDecimales';
        objTxtLongitudDecimales.name = 'objTxtLongitudDecimales';
        objTxtLongitudDecimales.width = 40;
        objTxtLongitudDecimales.maskRe = /[\d]/;
        objTxtLongitudDecimales.regex = /^\d{1,3}$/;
        objTxtLongitudDecimales.regexText = 'Decimas Segundos - Ingrese solo numeros entre 0-999';

        var objLblSeleccionLongitud = Utils.objLabel();
        objLblSeleccionLongitud.style = Utils.GREY_BOLD_COLOR;
        objLblSeleccionLongitud.text = '"';
        objCmbSeleccionLongitud = objComboPuntosCardinales('EO');
        objCmbSeleccionLongitud.setValue('NULL');
        objCmbSeleccionLongitud.id = 'objCmbSeleccionLongitud';
        objCmbSeleccionLongitud.name = 'objCmbSeleccionLongitud';
        objCmbSeleccionLongitud.width = 70;

        objTxtLongitudUbicacion = Utils.objText();
        objTxtLongitudUbicacion.id = 'objTxtLongitudUbicacion';
        objTxtLongitudUbicacion.name = 'objTxtLongitudUbicacion';
        objTxtLongitudUbicacion.hidden = true;

        objTxtLongitudUbicacion = Utils.objText();
        objTxtLongitudUbicacion.style = Utils.GREY_BOLD_COLOR;
        objTxtLongitudUbicacion.id = 'objTxtLongitudUbicacion';
        objTxtLongitudUbicacion.name = 'objTxtLongitudUbicacion';
        objTxtLongitudUbicacion.fieldLabel = "*Longitud";
        objTxtLongitudUbicacion.colspan = 6;
        objTxtLongitudUbicacion.width = intWidth;
        objTxtLongitudUbicacion.allowBlank = false;
        objTxtLongitudUbicacion.maskRe = /[\d\.]/;
        objTxtLongitudUbicacion.regex = /[0-9]+(\.[0-9][0-9]?)?/;
        objTxtLongitudUbicacion.blankText = 'Ingrese altura sobre el nivel del mar por favor';
        objTxtLongitudUbicacion.regexText = 'Altura sobre el nivel del mar - Ingrese solo numeros';
        objTxtLongitudUbicacion.setValue(rec.get('longitudUbicacion'));

        var objBtnMapa = objBotonMapa();
        objBtnMapa.id = 'objBtnMapa';
        objBtnMapa.name = 'objBtnMapa';
        objBtnMapa.icon = iconMap;
        objBtnMapa.cls = 'button-grid-Gmaps';

        var objContainerLongitud = Ext.create('Ext.container.Container', {
            colspan: 6,
            bodyStyle: 'margin: 1px 20px;',
            layout: {
                tdAttrs: {
                    style: 'padding: 1px 2px;'
                },
                type: 'table',
                columns: 12,
                pack: 'center'
            },
            items: [
                objLblLongitud,
                objTxtLongitud,
                objLblLongitudGrados,
                objTxtLongitudGrados,
                objLblLongitudMinutos,
                objTxtLongitudMinutos,
                objLblLongitudDecimales,
                objTxtLongitudDecimales,
                objLblSeleccionLongitud,
                objCmbSeleccionLongitud,
                objBtnMapa
            ]
        });

        formEditElemento.add(objTxtNombreElemento);
        formEditElemento.add(objCmbPropietario);
        formEditElemento.add(objTarDescripcionElemento);
        formEditElemento.add(objCmbTipoElemento);
        formEditElemento.add(objCmbJurisdiccion);
        formEditElemento.add(objCmbCanton);
        formEditElemento.add(objTxtCosto);
        formEditElemento.add(objCmbParroquia);
        formEditElemento.add(objTxtDireccion);
        formEditElemento.add(objTxtAltura);
        formEditElemento.add(objCmbElementoContenedor);
        formEditElemento.add(objCmbTipoLugar);
        formEditElemento.add(objTxtLatitudUbicacion);
        formEditElemento.add(objTxtLongitudUbicacion);
        formEditElemento.add(objTxtIdParroquia);
        formEditElemento.add(objTxtIdElemento);
        formEditElemento.add(objCmbNivel);
        formEditElemento.add(objChkFactible);
        formEditElemento.add(objUbicadoEn);
        formEditElemento.add(objContainerLatitud);
        formEditElemento.add(objContainerLongitud);
        
        //validaciones
        formularioDinamico(rec.get('tipoElemento'), rec.get('modelo_elemento'));
                

        function formularioDinamico(strTipoElemento, strModeloElemento)
        {

            objTxtNombreElemento.disable();
            objTarDescripcionElemento.disable();
            objCmbTipoLugar.disable();
            objCmbJurisdiccion.disable();
            objCmbPropietario.disable();
            objCmbCanton.disable();
            objTxtCosto.disable();
            objCmbParroquia.disable();
            objCmbTipoElemento.disable();
            objTxtAltura.disable();
            objTxtDireccion.disable();
            objCmbElementoContenedor.disable();
            objContainerLatitud.disable();
            objContainerLongitud.disable();
            objCmbNivel.disable();
            objChkFactible.disable();
            objUbicadoEn.disable();

            if (strTipoElemento == 'CAJA DISPERSION')
            {
                objCmbTipoElemento.disable();
                formularioDinamicoModelo(strModeloElemento);
            }
            else
            {
                if(strTipoElemento ==  'MANGA' || strTipoElemento ==  'RESERVA')
                {
                    if (strTipoElemento ==  'MANGA')
                    {
                        if (objCmbNivel.getValue() !== null)
                        {
                            objChkFactible.setValue(true);
                        }
                        objChkFactible.enable();
                    }
                    objUbicadoEn.enable();
                }
                objTxtNombreElemento.enable();
                objTarDescripcionElemento.enable();
                objCmbTipoElemento.enable();
                objCmbJurisdiccion.enable();
                objCmbCanton.enable();
                objCmbParroquia.enable();
                objTxtDireccion.enable();
                objTxtAltura.enable();
                objContainerLatitud.enable();
                objContainerLongitud.enable();

                if (strTipoElemento == 'POZO')
                {
                    objCmbElementoContenedor.enable();
                    strTipoLugar = 'SOTERRADO';
                    objCmbPropietario.enable();

                    //elementos contenedores que se van a cargar segun el tipo de elemento
                    storeElementoContenedor.proxy.extraParams = {tipoElemento: 'EDIFICACION', estado: 'Activo'};
                    storeElementoContenedor.load({params: {}});
                }
                else if (strTipoElemento == 'POSTE')
                {
                    strTipoLugar = 'AEREO';
                    objCmbPropietario.enable();
                    objTxtCosto.enable();
                }
            }
        }
        
        function formularioDinamicoModelo(strModeloTipoElemento)
        {
            objTxtNombreElemento.disable();
            objTarDescripcionElemento.disable();
            objCmbTipoLugar.disable();
            objCmbJurisdiccion.disable();
            objCmbPropietario.disable();
            objCmbCanton.disable();
            objTxtCosto.disable();
            objCmbParroquia.disable();
            objTxtAltura.disable();
            objTxtDireccion.disable();
            objCmbElementoContenedor.disable();
            objContainerLatitud.disable();
            objContainerLongitud.disable();
            objCmbNivel.disable();

            objTxtNombreElemento.enable();
            objTarDescripcionElemento.enable();
            objCmbJurisdiccion.enable();
            objCmbCanton.enable();
            objCmbParroquia.enable();
            objTxtDireccion.enable();
            objTxtAltura.enable();
            objContainerLatitud.enable();
            objContainerLongitud.enable();
            objUbicadoEn.enable();

            var strModelo = strModeloTipoElemento.toUpperCase();

            if ((strModelo.indexOf('STANDARD') != -1) || (strModelo.indexOf('CDP') != -1) || (strModelo.indexOf('PEDESTAL') != -1)
                || (strModelo.indexOf('MINIPOSTE') != -1) || (strModelo.indexOf('LDD') != -1))
            {

                if ((strModelo.indexOf('STANDARD') != -1) || (strModelo.indexOf('CDP') != -1))
                {
                    strTipoLugar = 'AEREO';
                }

                if ((strModelo.indexOf('PEDESTAL') != -1) || (strModelo.indexOf('MINIPOSTE') != -1))
                {
                    strTipoLugar = 'SOTERRADO';
                }

                objCmbElementoContenedor.enable();
                if (strModelo.indexOf('STANDARD') != -1)
                {
                    objCmbNivel.enable();
                }
                if (strModelo.indexOf('LDD') != -1)
                {
                    objCmbTipoLugar.enable();
                }
                //objCmbTipoLugar.enable();
                //elementos contenedores que se van a cargar segun el tipo de elemento
                storeElementoContenedor.proxy.extraParams = {tipoElemento: 'EDIFICACION', estado: 'Activo'};
                storeElementoContenedor.load({params: {}});
            }

        }        

    };

    var btnEditarPoste = Ext.create('Ext.button.Button', {
        text: 'Editar',
        scope: this,
        style: {
            marginTop: '10px'
        },
        getClass: function(v, meta, rec)
        {
            var strEstado = rec.get('estado');
            if (strEstado !== 'Eliminado' && boolPermisoEditar)
            {
                return 'button-grid-edit';
            }
            return '';
        },
        tooltip: 'Editar Elemento',
        handler: editarPoste
    });

    var eliminarPoste = function(rec) {
        var intIdElemento = rec.get('idElemento');
        var strNombreElemento = rec.get('nombreElemento');
        var strEstado = rec.get('estado');
        Ext.Msg.confirm('Alerta', 'Se Eliminará : ' + strNombreElemento + ' . Desea continuar?', function(btn) {
            if (btn === 'yes') {
                if (strEstado !== 'Eliminado') {

                    Ext.get(document.body).mask('Eliminando Poste...');
                    Ext.Ajax.request({
                        url: urlDeletePoste,
                        method: 'post',
                        params: {
                            idElemento: intIdElemento
                        },
                        success: function(response)
                        {
                            Ext.get(document.body).unmask();
                            var json = Ext.JSON.decode(response.responseText);
                            Ext.Msg.alert('Mensaje', json.strMessageStatus);
                            store.load();
                        },
                        failure: function(result)
                        {
                            Ext.get(document.body).unmask();
                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                        }
                    });
                } else {
                    alert('Error - Poste (' + strNombreElemento + ') Solo se puede eliminar una solicitud en estado: ');
                }
            }
        });
    };

    var btnEliminarPoste = Ext.create('Ext.button.Button', {
        text: 'Eliminar',
        scope: this,
        style: {
            marginTop: '10px'
        },
        getClass: function(v, meta, rec)
        {
            var strEstado = rec.get('estado');
            if (strEstado !== 'Eliminado' && boolPermisoEliminar)
            {
                return 'button-grid-delete';
            }
            return '';
        },
        tooltip: 'Eliminar',
        handler: function(grid, rowIndex, colIndex) {
            var rec = store.getAt(rowIndex);
            eliminarPoste(rec);
        }
    });

    var storeCantones = new Ext.data.Store({
        total: 'total',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: url_getCantones,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'nombre_canton', mapping: 'nombre_canton'},
                {name: 'id_canton', mapping: 'id_canton'}
            ]
    });

    var storePropietarios = new Ext.data.Store({
        total: 'total',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: url_getPropietarios,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'nombre_propietario', mapping: 'nombre_propietario'},
                {name: 'id_propietario', mapping: 'id_propietario'}
            ]
    });

    var storeTipoElemento = new Ext.data.Store({
        total: 'total',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: urlTipoElementos,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'nombreTipoElemento', mapping: 'nombreTipoElemento'},
                {name: 'idTipoElemento', mapping: 'idTipoElemento'}
            ]
    });

    store = new Ext.data.Store({
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
            url: url_getEncontradosPostes,
            timeout: 800000,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                strCodigo: '',
                strPropietario: '',
                strCanton: '',
                strEstado: ''
            }
        },
        fields:
            [
                {name: 'idElemento', mapping: 'id_elemento'},
                {name: 'nombreElemento', mapping: 'nombre_elemento'},
                {name: 'descripcionElemento', mapping: 'descripcion_elemento'},
                {name: 'cantonNombre', mapping: 'nombre_canton'},
                {name: 'cantonId', mapping: 'id_canton'},
                {name: 'nombreRegion', mapping: 'region'},
                {name: 'jurisdiccionId', mapping: 'id_jurisdiccion'},
                {name: 'jurisdiccionNombre', mapping: 'nombre_jurisdiccion'},
                {name: 'estado', mapping: 'estado'},
                {name: 'longitudUbicacion', mapping: 'longitud_ubicacion'},
                {name: 'latitudUbicacion', mapping: 'latitud_ubicacion'},
                {name: 'direccionUbicacion', mapping: 'direccion_ubicacion'},
                {name: 'parroquiaId', mapping: 'id_parroquia'},
                {name: 'ubicacionId', mapping: 'id_ubicacion'},
                {name: 'nombreParroquia', mapping: 'nombre_parroquia'},
                {name: 'costoElemento', mapping: 'costo'},
                {name: 'modelo_elemento', mapping: 'modelo_elemento'},
                {name: 'modeloElementoId', mapping: 'id_modelo_elemento'},
                {name: 'tipoElementoId', mapping: 'id_tipo_elemento'},
                {name: 'tipoElemento', mapping: 'tipo_elemento'},
                {name: 'tipoLugar', mapping: 'tipo_elemento'},
                {name: 'alturaSnm', mapping: 'altura_snm'},
                {name: 'tipoLugar', mapping: 'tipoLugar'},
                {name: 'nivel', mapping: 'nivel'},
                {name: 'elementoContenedor', mapping: 'elementoContenedor'},
                {name: 'idElementoContenedor', mapping: 'idElementoContenedor'},
                {name: 'propietario', mapping: 'propietario'},
                {name: 'ubicadoEn', mapping: 'ubicadoEn'}
            ]
    });

    var pluginExpanded = true;

    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    });

    gridPostes = Ext.create('Ext.grid.Panel', {
        width: 930,
        height: 350,
        store: store,
        loadMask: true,
        dockedItems: [toolbar],
        frame: false,
        viewConfig: {enableTextSelection: true},
        columns: [
            {
                id: 'idElemento',
                header: 'idElemento',
                dataIndex: 'idElemento',
                hidden: true,
                hideable: false
            },
            {
                id: 'ipElemento',
                header: 'Elemento',
                xtype: 'templatecolumn',
                width: 290,
                tpl: '<span class="box-detalle">{nombreElemento}</span>\n\
                        <span class="bold">Jurisdicción:</span><span>{jurisdiccionNombre}</span></br>\n\
                        <span class="bold">Región:</span><span>{nombreRegion}</span></br>\n\
                        <span class="bold">Cantón:</span><span>{cantonNombre}</span></br>\n\\n\ '

            },
            {
                id: 'idModeloElemento',
                header: 'Modelo',
                dataIndex: 'modelo_elemento',
                width: 150,
                sortable: true
            },               
            {
                id: 'canton',
                header: 'Canton',
                dataIndex: 'cantonNombre',
                width: 150,
                sortable: true
            },
            {
                header: 'Estado',
                dataIndex: 'estado',
                width: 80,
                sortable: true
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 275,
                items: [
                    btnVerPoste,
                    {
                        getClass: function(v, meta, rec) {
                            return 'button-grid-Gmaps'
                        },
                        tooltip: 'Ver Mapa',
                        handler: function(grid, rowIndex, colIndex) {
                            var rec = store.getAt(rowIndex);
                            if(rec.get("latitudUbicacion")!=0 && rec.get("longitudUbicacion")!=0){
                                showVerMapa(rec.get("latitudUbicacion"),rec.get("longitudUbicacion"));
                            }
                            else
                                Ext.MessageBox.show({
                                   title: 'Error',
                                   msg: 'Las coordenadas son incorrectas',
                                   buttons: Ext.MessageBox.OK,
                                   icon: Ext.MessageBox.ERROR
                                });
                        }
                    },                    
                    btnEditarPoste,
                    btnEliminarPoste
                ]
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar', {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'grid'
    });

    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,
        border: false,
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 4,
            align: 'stretch'
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed: false,
        width: 930,
        title: 'Criterios de busqueda',
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function() {
                    buscar();
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function() {
                    limpiar();
                }
            }

        ],
        items: [
            {width: '5%', border: false},
            {
                xtype: 'combobox',
                id: 'sltTipoElemento',
                fieldLabel: 'Tipo elemento',
                displayField: 'nombreTipoElemento',
                valueField: 'idTipoElemento',
                loadingText: 'Buscando ...',
                store: storeTipoElemento,
                listClass: 'x-combo-list-small',
                queryMode: 'local',
                width: '40%'
            },
            {
                xtype: 'textfield',
                id: 'sltNombreElemento',
                name: 'sltNombreElemento',
                fieldLabel: 'Nombre elemento',
                value: '',
                width: '40%'
            },
            {width: '10%', border: false},
            {width: '5%', border: false},
            {
                xtype: 'combobox',
                id: 'sltCanton',
                fieldLabel: 'Cantón',
                displayField: 'nombre_canton',
                valueField: 'id_canton',
                loadingText: 'Buscando ...',
                store: storeCantones,
                listClass: 'x-combo-list-small',
                queryMode: 'local',
                width: '40%'
            },
            {
                xtype: 'combobox',
                fieldLabel: 'Estado',
                id: 'sltEstado',
                value: '',
                store: [
                    ['', 'Todos'],
                    ['Activo', 'Activo'],
                    ['Modificado', 'Modificado'],
                    ['Eliminado', 'Eliminado']
                ],
                width: '40%'
            },
            {width: '10%', border: false}
        ],
        renderTo: 'filtro'
    });

    if (!Ext.isEmpty(strNombreElemento))
    {
        store.getProxy().extraParams.intElemento = strNombreElemento;
        
        store.load();
    }
    
});

function buscar() {

    if (!Ext.getCmp('sltTipoElemento').value)
    {
        alert('Debe seleccionar el tipo de elemento.');
    }
    else
    {
        store.getProxy().extraParams.strCodigo = Ext.getCmp('sltNombreElemento').value;
        store.getProxy().extraParams.strCanton = Ext.getCmp('sltCanton').value;
        store.getProxy().extraParams.sltTipoElemento = Ext.getCmp('sltTipoElemento').value;
        store.getProxy().extraParams.strEstado = Ext.getCmp('sltEstado').value;
        store.getProxy().extraParams.intElemento = '';
        store.load();
    }

}

function limpiar() {
    Ext.getCmp('sltNombreElemento').value = "";
    Ext.getCmp('sltNombreElemento').setRawValue("");

    Ext.getCmp('sltCanton').value = "";
    Ext.getCmp('sltCanton').setRawValue("");

    Ext.getCmp('sltTipoElemento').value = "";
    Ext.getCmp('sltTipoElemento').setRawValue("");

    Ext.getCmp('sltEstado').value = "Todos";
    Ext.getCmp('sltEstado').setRawValue("Todos");

    store.load({params: {
            strCodigo: Ext.getCmp('sltNombreElemento').value,
            strCanton: Ext.getCmp('sltCanton').value,
            strPropietario: Ext.getCmp('sltTipoElemento').value,
            strEstado: Ext.getCmp('sltEstado').value
        }});
}

 
/************************************************************************ */
/************************** VER MAPA ************************************ */
/************************************************************************ */
function showVerMapa(latitud,longitud){
    winVerMapa="";

    if(latitud!=0 && longitud!=0)
    {
        if (!winVerMapa)
        {
            formPanelMapa = Ext.create('Ext.form.Panel', {
                BodyPadding: 10,
                frame: true,
                items: [
                    {
                        html: "<div id='map_canvas2' style='width:575px; height:450px'></div>"
                    }
                ]
            });

            winVerMapa = Ext.widget('window', {
                title: 'Mapa del Elemento',
                layout: 'fit',
                resizable: false,
                modal: true,
                closable: true,
                items: [formPanelMapa]
            });
        }

        winVerMapa.show();
        muestraMapas(latitud, longitud);
    }
    else
    {
        alert('Estas coordenadas son incorrectas!!')
    }
}

function muestraMapas(vlat,vlong){
    var mapa;
    var ciudad = "";
    var markerPto ;

    if((vlat)&&(vlong)){
        var latlng = new google.maps.LatLng(vlat,vlong);
        var myOptions = {
            zoom: 14,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }

        if(mapa){
            mapa.setCenter(latlng);
        }else{
            mapa = new google.maps.Map(document.getElementById("map_canvas2"), myOptions);
        }

        if(markerPto)
            markerPto.setMap(null);

        markerPto = new google.maps.Marker({
            position: latlng, 
            map: mapa
        });
        mapa.setZoom(17);

    }
} 

function validador(me,e)
{
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
    
    letras = "abcdefghijklmnopqrstuvwxyz0123456789";
    especiales = [8, 36, 35, 45, 47, 40, 41, 46];
    
    tecla_especial = false
    for (var i in especiales) {
        if (key == especiales[i]) {
            tecla_especial = true;
            break;
        }
    }

    if (letras.indexOf(tecla) == -1 && !tecla_especial)
    {
        e.stopEvent();
    }
    else
    {
        me.isValid();
    }
}
