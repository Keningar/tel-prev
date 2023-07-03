var objTxtLatitud          = null;
var objTxtLatitudGrados    = null;
var objTxtLatitudMinutos   = null;
var objTxtLatitudDecimales = null;
var objCmbSeleccionLatitud = null;
var objTxtLatitudUbicacion = null;

var objTxtLongitud          = null;
var objTxtLongitudGrados    = null;
var objTxtLongitudMinutos   = null;
var objTxtLongitudDecimales = null;
var objCmbSeleccionLongitud = null;
var objTxtLongitudUbicacion = null;

var strTipoLugar            = '';
//pozo      -> soterrado
//poste     -> aereo
//caja dispersion
//standard  -> aereo
//CDP       -> aereo
//Pedestal  -> soterrado
//miniposte -> soterrado
//ldd       -> aereo o soterrado


Ext.onReady(function () {
    
    
    //validaciones que se cargan segun el tipo de elemento
    
    var objTipoElemento = function() {

            Ext.define('modelStoreTipoElemento', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'idTipoElemento',     type: 'int'},
                    {name: 'nombreTipoElemento', type: 'string'}
                ]
            });
            var storeTipoElemento= Ext.create('Ext.data.Store', {
                id:       'idStoreTipoElemento',
                model:    'modelStoreTipoElemento',
                autoLoad: true,
                proxy: {
                    type:    'ajax',
                    url:     urlTipoElementos,
                    timeout: 600000,
                    reader: {
                        type: 'json',
                        root: 'encontrados'
                    },
                    simpleSortMode: true
                }
            });

            return Ext.create('Ext.form.ComboBox', {
                store: storeTipoElemento,
                queryMode: 'local',
                displayField: 'nombreTipoElemento',
                valueField: 'idTipoElemento',
                listeners: {
                    select: function(records) {
                        strTipoLugar = '';
                        formularioDinamico (objCmbTipoElementoRuta.getRawValue());
                        objStoreTipo.proxy.extraParams = {tipoElemento: objCmbTipoElementoRuta.getRawValue()};                   
                        objStoreTipo.load({params: {}});                       
                        

                    },
                }
        });
    };       
    
    Ext.define('modelCanton', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'canton_id',    type: 'int'},
            {name: 'nombreCanton', type: 'string'}
        ]
    });
    var storeCanton = Ext.create('Ext.data.Store', {
        id:       'storeIdCanton',
        model:    'modelCanton',
        autoLoad: false,
        proxy: {
            type:    'ajax',
            url:     urlGetCantonJurisdiccion,
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
            {name: 'id_parroquia',     type: 'int'},
            {name: 'nombre_parroquia', type: 'string'}
        ]
    });
    var storeParroquia = Ext.create('Ext.data.Store', {
        id:       'storeIdParroquia',
        model:    'modelParroquia',
        autoLoad: false,
        proxy: {
            type:    'ajax',
            url:     urlGetParroquiaCanton,
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
                {name: 'idJurisdiccion',     type: 'int'},
                {name: 'nombreJurisdiccion', type: 'string'},
                {name: 'estado',             type: 'string'}
            ]
        });
        var storeJurisdiccion = Ext.create('Ext.data.Store', {
            id:       'storeIdJurisdiccion',
            model:    'modelJurisdiccion',
            autoLoad: true,
                proxy: {
                type:    'ajax',
                url:     urlGetJurisdiccion,
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
            store:        storeJurisdiccion,
            queryMode:    'local',
            displayField: 'nombreJurisdiccion',
            valueField:   'idJurisdiccion',
            listeners: {
                select: function(records) {
                     storeCanton.load({params: {
                        jurisdiccionId: objCmbJurisdiccion.getValue(),
                        estado        : 'Activo'
                    }});
                },
                change: function() { objCmbCanton.clearValue(); 
                                     objCmbParroquia.clearValue(); 
                                   }
            },
        });
    };

    var objComboCanton = function() {
        return Ext.create('Ext.form.ComboBox', {
            store: storeCanton,
            queryMode:    'local',
            displayField: 'nombreCanton',
            valueField:   'canton_id',
            listeners: {
                select: function(records) {
                    storeParroquia.load({params: {
                        cantonId: objCmbCanton.getValue()      
                    }});
                },
                change: function() { objCmbParroquia.clearValue(); }
            },
        });
    };
    
    var objComboParroquia = function() {
        return Ext.create('Ext.form.ComboBox', {
            store:        storeParroquia,
            queryMode:    'local',
            displayField: 'nombre_parroquia',
            valueField:   'id_parroquia',
        });
    };
    
    var objComboPropietario = function() {

            Ext.define('modelPropietario', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'id_propietario',     type: 'int'},
                    {name: 'nombre_propietario', type: 'string'}
                ]
            });
            var storePropietario= Ext.create('Ext.data.Store', {
                id:       'storeIdPersonaEmpresaRol',
                model:    'modelPropietario',
                autoLoad: true,
                proxy: {
                    type:    'ajax',
                    url:     strUrlGetPropietarios,
                    timeout: 600000,
                    reader: {
                        type: 'json',
                        root: 'encontrados'
                    },
                    simpleSortMode: true
                }
            });

            return Ext.create('Ext.form.ComboBox', {
                store:        storePropietario,
                queryMode:    'local',
                displayField: 'nombre_propietario',
                valueField:   'id_propietario',
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
                queryMode:    'remote',
                displayField: 'nombre_elemento',
                valueField:   'id_elemento',
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
                valueField:   'id'
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
    
    var objComboPuntosCardinales = function (strTipoCardinal) {
        
        var arrayNorteSur = [{"strCod": "NULL", "strNombre": "Seleccione.."}, 
                             {"strCod": "ESTE", "strNombre": "Este"}, 
                             {"strCod": "OESTE", "strNombre": "Oeste"}];
        if(!Ext.isEmpty(strTipoCardinal) && 'NS' === strTipoCardinal)
        {
            arrayNorteSur = [{"strCod": "NULL", "strNombre": "Seleccione.."}, {"strCod": "NORTE", "strNombre": "Norte"}, {"strCod": "SUR", "strNombre": "Sur"}];
        }
        var objStorePCadinales = Ext.create('Ext.data.Store', {
            fields: ['strCod', 'strNombre'],
            data:   arrayNorteSur
        });

        return Ext.create('Ext.form.ComboBox', {
            store:        objStorePCadinales,
            queryMode:    'local',
            displayField: 'strNombre',
            valueField:   'strCod',
        });
    };
    
    var objStoreTipo = Ext.create('Ext.data.Store', {
        id: 'storeIdTipo',
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
                tipoElemento: ''

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
            listeners: {
                    select: function(combo, record, index) {

                        if(objCmbTipoElementoRuta.getRawValue() == 'CAJA DISPERSION')
                        {
                           strTipoLugar = '';
                           formularioDinamicoModelo (combo.rawValue);
                        }
             
                        

                    },
                }
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
    
    var objBotonMapa = function (){
        return Ext.create('Ext.Button', {
            listeners: {
                click: function() {
                    muestraMapa();
                }
            }
        });
    };
    
    formCreaParametrosDet = Ext.create('Ext.form.Panel', {
        bodyStyle:   'padding: 20px 10px 0; background:#FFFFFF;',
        bodyPadding: 15,
        width:       930,
        title:       'Nuevo Elemento Pasivo',
        renderTo:    'Div_New_Poste',
        layout: {
            type:    'table',
            columns: 12,
            pack:    'center',
            tableAttrs: {
                style: {
                    width:  '90%',
                    height: '90%'
                }
            },
            tdAttrs: {
                align:  'left',
                valign: 'middle'
            }
        },
        buttonAlign: 'center',
        buttons: [
            {
                text:     'Guardar',
                name:     'btnGuardar',
                id:       'idBtnGuardar',
                disabled: false,
                handler:  function () {
                    
                    if(strTipoLugar != '')
                    {
                        objCmbTipoLugar.enable();
                        objCmbTipoLugar.setValue(strTipoLugar);
                    }
                    
                    if (objChkFactible.checked)
                    {
                        objCmbNivel.enable();
                    }
                                            
                    var form = formCreaParametrosDet.getForm();
                    if (form.isValid())
                    {  
                         if(!validarCoordenadasPorPaisMapa())
                         {
                            return false;
                         }
                        
                        var data = form.getValues();
                        Ext.get(document.body).mask('Guardando datos...');
                        Ext.Ajax.request({
                            url :    urlSaveElementoPasivo,
                            method : 'POST',
                            params : data,                           
                            success:function(response){                                 
                                Ext.get(document.body).unmask();
                                var json = Ext.JSON.decode(response.responseText);
                                Ext.Msg.alert('Mensaje', json.strMessageStatus);
                                if (json.strStatus == 'OK')
                                {
                                    window.location.href= strUrlIndex;
                                }
                            },
                            failure:function(result){
                                Ext.get(document.body).unmask();
                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText); 
                            }
                        });
                    }
                }
            },
            {
                text: 'Cancelar',
                handler: function () {
                    this.up('form').getForm().reset();
                }
            }]
    });
    
    var intWidth                    = 325;
    
    
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
        
    var objCmbPropietario           = objComboPropietario();
    objCmbPropietario.style         = Utils.GREY_BOLD_COLOR;
    objCmbPropietario.id            = 'objCmbPropietario';
    objCmbPropietario.name          = 'objCmbPropietario';
    objCmbPropietario.fieldLabel    = "*Propietario";        
    objCmbPropietario.colspan       = 6;
    objCmbPropietario.width         = intWidth; 
    objCmbPropietario.allowBlank    = false;
    objCmbPropietario.blankText     = 'Ingrese propietario por favor';
    
    var objCmbTipoLugar             = objComboTipoLugar();
    objCmbTipoLugar.style           = Utils.GREY_BOLD_COLOR;
    objCmbTipoLugar.id              = 'objCmbTipoLugar';
    objCmbTipoLugar.name            = 'objCmbTipoLugar';
    objCmbTipoLugar.fieldLabel      = "*Tipo Lugar";        
    objCmbTipoLugar.colspan         = 6;
    objCmbTipoLugar.width           = intWidth; 
    objCmbTipoLugar.allowBlank      = false;
    objCmbTipoLugar.blankText       = 'Ingrese tipo lugar por favor';
    
    var objUbicadoEn             = objComboUbicadoEn();
    objUbicadoEn.style           = Utils.GREY_BOLD_COLOR;
    objUbicadoEn.id              = 'objUbicadoEn';
    objUbicadoEn.name            = 'objUbicadoEn';
    objUbicadoEn.fieldLabel      = "*Ubicado En";        
    objUbicadoEn.colspan         = 6;
    objUbicadoEn.width           = intWidth; 
    objUbicadoEn.allowBlank      = true;
    objUbicadoEn.blankText       = 'Ingrese tipo lugar por favor';    
    
    var objCmbNivel             = objComboNivel();
    objCmbNivel.style           = Utils.GREY_BOLD_COLOR;
    objCmbNivel.id              = 'objCmbNivel';
    objCmbNivel.name            = 'objCmbNivel';
    objCmbNivel.fieldLabel      = "*Nivel";        
    objCmbNivel.colspan         = 12;
    objCmbNivel.width           = intWidth; 
    objCmbNivel.allowBlank      = false;
    objCmbNivel.blankText       = 'Ingrese tipo lugar por favor';
    
    var objChkFactible          = objChkFactibilidad();
    objChkFactible.style        = Utils.GREY_BOLD_COLOR;
    objChkFactible.id           = 'objChkFactible';
    objChkFactible.name         = 'objChkFactible';
    objChkFactible.fieldLabel   = "Factibilidad";        
    objChkFactible.colspan      = 12;
    objChkFactible.width        = intWidth; 
        
    var objTarDescripcionElemento        = Utils.objTextArea();
    objTarDescripcionElemento.style      = Utils.GREY_BOLD_COLOR;
    objTarDescripcionElemento.id         = 'objTarDescripcionElemento';
    objTarDescripcionElemento.name       = 'objTarDescripcionElemento';
    objTarDescripcionElemento.fieldLabel = "Descripción"; 
    objTarDescripcionElemento.colspan    = 6;
    objTarDescripcionElemento.width      = intWidth;
    objTarDescripcionElemento.blankText  = 'Ingrese descripción por favor';

    var objCmbTipoElemento        = objComboTipo();
    objCmbTipoElemento.style      = Utils.GREY_BOLD_COLOR;
    objCmbTipoElemento.id         = 'objCmbTipoElemento';
    objCmbTipoElemento.name       = 'objCmbTipoElemento';
    objCmbTipoElemento.fieldLabel = "*Modelo"; 
    objCmbTipoElemento.colspan    = 6;
    objCmbTipoElemento.width      = intWidth; 
    objCmbTipoElemento.allowBlank = false;
    objCmbTipoElemento.blankText  = 'Ingrese tipo por favor';
    
    var objCmbElementoContenedor        = objComboElementoContenedor();
    objCmbElementoContenedor.style      = Utils.GREY_BOLD_COLOR;
    objCmbElementoContenedor.id         = 'objCmbElementoContenedor';
    objCmbElementoContenedor.name       = 'objCmbElementoContenedor';
    objCmbElementoContenedor.fieldLabel = "Elemento Contenedor"; 
    objCmbElementoContenedor.colspan    = 6;
    objCmbElementoContenedor.width      = intWidth; 
    objCmbElementoContenedor.blankText  = 'Ingrese tipo por favor'; 
    objCmbElementoContenedor.queryMode  = 'remote'; 
    objCmbElementoContenedor.lazyRender = true; 
    objCmbElementoContenedor.loadingText= 'Buscando...'; 
    objCmbElementoContenedor.minChars   = 3; 

    var objCmbJurisdiccion        = objComboJurisdiccion();
    objCmbJurisdiccion.style      = Utils.GREY_BOLD_COLOR;
    objCmbJurisdiccion.id         = 'objCmbJurisdiccion';
    objCmbJurisdiccion.name       = 'objCmbJurisdiccion';
    objCmbJurisdiccion.fieldLabel = "*Jurisdicción";
    objCmbJurisdiccion.colspan    = 6;
    objCmbJurisdiccion.width      = intWidth; 
    objCmbJurisdiccion.allowBlank = false;
    objCmbJurisdiccion.blankText  = 'Ingrese jurisdicción por favor';
    
    var objCmbTipoElementoRuta        = objTipoElemento();
    objCmbTipoElementoRuta.style      = Utils.GREY_BOLD_COLOR;
    objCmbTipoElementoRuta.id         = 'idTipoElemento';
    objCmbTipoElementoRuta.name       = 'idTipoElemento';
    objCmbTipoElementoRuta.fieldLabel = "*Tipo Elemento";
    objCmbTipoElementoRuta.colspan    = 6;
    objCmbTipoElementoRuta.width      = intWidth; 
    objCmbTipoElementoRuta.allowBlank = false;
    objCmbTipoElementoRuta.blankText  = 'Seleccion el tipo de elemento por favor';        
    
    var objCmbCanton        = objComboCanton();
    objCmbCanton.style      = Utils.GREY_BOLD_COLOR;
    objCmbCanton.id         = 'objCmbCanton';
    objCmbCanton.name       = 'objCmbCanton';
    objCmbCanton.fieldLabel = "*Cantón"; 
    objCmbCanton.colspan    = 6;
    objCmbCanton.width      = intWidth; 
    objCmbCanton.allowBlank = false;
    objCmbCanton.blankText  = 'Ingrese cantón por favor';
    
    var objTxtCosto        = Utils.objText();
    objTxtCosto.style      = Utils.GREY_BOLD_COLOR;
    objTxtCosto.id         = 'objTxtCosto';
    objTxtCosto.name       = 'objTxtCosto';
    objTxtCosto.fieldLabel = "*Costo"; 
    objTxtCosto.colspan    = 6;
    objTxtCosto.width      = intWidth; 
    objTxtCosto.allowBlank = false;
    objTxtCosto.maskRe     = /[\d\.]/;
    objTxtCosto.regex      = /[0-9]+(\.[0-9][0-9]?)?/;
    objTxtCosto.blankText  = 'Ingrese costo por favor';
    objTxtCosto.regexText  = 'Costo - Ingrese solo numeros';

    var objCmbParroquia        = objComboParroquia();
    objCmbParroquia.style      = Utils.GREY_BOLD_COLOR;
    objCmbParroquia.id         = 'objCmbParroquia';
    objCmbParroquia.name       = 'objCmbParroquia';
    objCmbParroquia.fieldLabel = "*Parroquia"; 
    objCmbParroquia.colspan    = 6;
    objCmbParroquia.width      = intWidth; 
    objCmbParroquia.allowBlank = false;
    objCmbParroquia.blankText  = 'Ingrese parroquia por favor';
     
    var objTxtDireccion        = Utils.objText();
    objTxtDireccion.style      = Utils.GREY_BOLD_COLOR;
    objTxtDireccion.id         = 'objTxtDireccion';
    objTxtDireccion.name       = 'objTxtDireccion';
    objTxtDireccion.fieldLabel = "*Dirección"; 
    objTxtDireccion.colspan    = 6;
    objTxtDireccion.width      = intWidth; 
    objTxtDireccion.allowBlank = false;
    objTxtDireccion.blankText  = 'Ingrese dirección por favor';
    
    var objTxtAltura        = Utils.objText();
    objTxtAltura.style      = Utils.GREY_BOLD_COLOR;
    objTxtAltura.id         = 'objTxtAltura';
    objTxtAltura.name       = 'objTxtAltura';
    objTxtAltura.fieldLabel = "*Altura Sobre Nivel Mar 5"; 
    objTxtAltura.colspan    = 6;
    objTxtAltura.width      = intWidth; 
    objTxtAltura.allowBlank = false;
    objTxtAltura.maskRe     = /[\d\.]/;
    objTxtAltura.regex      = /[0-9]+(\.[0-9][0-9]?)?/;
    objTxtAltura.blankText  = 'Ingrese altura sobre el nivel del mar por favor';
    objTxtAltura.regexText  = 'Altura sobre el nivel del mar - Ingrese solo numeros';
    
    var objLblLatitud        = Utils.objLabel();
    objLblLatitud.text       = '*Coordenadas Latitud';
    objTxtLatitud            = Utils.objText();
    objLblLatitud.style      = Utils.GREY_BOLD_COLOR;
    objTxtLatitud.id         = 'objTxtLatitud';
    objTxtLatitud.name       = 'objTxtLatitud';
    objTxtLatitud.width      = 40; 
    objTxtLatitud.allowBlank = false;
    objTxtLatitud.maskRe     = /[\d]/;
    objTxtLatitud.regex      = /^(?:36[0]|3[0-5][0-9]|[12][0-9][0-9]|[1-9]?[0-9])?$/;
    objTxtLatitud.blankText  = 'Ingrese los grados por favor';
    objTxtLatitud.regexText  = 'Grados - Ingrese solo numeros entre 0-360';

    var objLblLatitudGrados         = Utils.objLabel();
    objLblLatitudGrados.text        = '°';
    objTxtLatitudGrados             = Utils.objText();
    objLblLatitudGrados.style       = Utils.GREY_BOLD_COLOR;
    objTxtLatitudGrados.id          = 'objTxtLatitudGrados';
    objTxtLatitudGrados.name        = 'objTxtLatitudGrados';
    objTxtLatitudGrados.labelStyle  = 'padding: 0px 0px;';
    objTxtLatitudGrados.width       = 40; 
    objTxtLatitudGrados.allowBlank  = false;
    objTxtLatitudGrados.maskRe      = /[\d]/;
    objTxtLatitudGrados.regex       = /^[1-5]?[0-9]$/;
    objTxtLatitudGrados.blankText   = 'Ingrese los minutos por favor';
    objTxtLatitudGrados.regexText   = 'Minutos - Ingrese solo numeros entre 0-59';
    
    
    var objLblLatitudMinutos        = Utils.objLabel();
    objLblLatitudMinutos.text       = "'";
    objTxtLatitudMinutos            = Utils.objText();
    objLblLatitudMinutos.style      = Utils.GREY_BOLD_COLOR;
    objTxtLatitudMinutos.id         = 'objTxtLatitudMinutos';
    objTxtLatitudMinutos.name       = 'objTxtLatitudMinutos';
    objTxtLatitudMinutos.width      = 40; 
    objTxtLatitudMinutos.allowBlank = false;
    objTxtLatitudMinutos.maskRe     = /[\d]/;
    objTxtLatitudMinutos.regex      = /^[1-5]?[0-9]$/;
    objTxtLatitudMinutos.blankText  = 'Ingrese los segundos por favor';
    objTxtLatitudMinutos.regexText  = 'Segundos - Ingrese solo numeros entre 0-59';

    var objLblLatitudDecimales        = Utils.objLabel();
    objLblLatitudDecimales.text       = '.';
    objTxtLatitudDecimales            = Utils.objText();
    objLblLatitudDecimales.style      = Utils.GREY_BOLD_COLOR;
    objTxtLatitudDecimales.id         = 'objTxtLatitudDecimales';
    objTxtLatitudDecimales.name       = 'objTxtLatitudDecimales';
    objTxtLatitudDecimales.width      = 40;      
    objTxtLatitudDecimales.allowBlank = false;
    objTxtLatitudDecimales.maskRe     =  /[\d]/;
    objTxtLatitudDecimales.regex      =  /^\d{1,3}$/;
    objTxtLatitudDecimales.blankText  = 'Ingrese las décimas de segundos por favor';
    objTxtLatitudDecimales.regexText  = 'Décimas Segundos - Ingrese solo numeros entre 0-999';
    
    var objLblSeleccionLatitud        = Utils.objLabel();
    objLblSeleccionLatitud.text       = '"';
    objCmbSeleccionLatitud            = objComboPuntosCardinales('NS');
    objCmbSeleccionLatitud.setValue('NULL');
    objLblSeleccionLatitud.style      = Utils.GREY_BOLD_COLOR;
    objCmbSeleccionLatitud.id         = 'objCmbSeleccionLatitud';
    objCmbSeleccionLatitud.name       = 'objCmbSeleccionLatitud';
    objCmbSeleccionLatitud.width      = 70;      
    objCmbSeleccionLatitud.allowBlank = false;
    
    objTxtLatitudUbicacion            = Utils.objText();
    objTxtLatitudUbicacion.id         = 'objTxtLatitudUbicacion';
    objTxtLatitudUbicacion.name       = 'objTxtLatitudUbicacion';
    objTxtLatitudUbicacion.hidden     = true;
    
    var objContainerLatitud   = Ext.create('Ext.container.Container', {
                colspan:    6,
                bodyStyle: 'margin: 1px 20px;',
                layout: {
                    tdAttrs: {
                        style: 'padding: 1px 2px;'
                    },
                    type:    'table',
                    columns: 12,
                    pack:    'center'
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
                        objCmbSeleccionLatitud,
                        objTxtLatitudUbicacion
                    ]
            });
            
    var objLblLongitud        = Utils.objLabel();
    objLblLongitud.text       = '*Coordenadas Longitud';
    objLblLongitud.style      = Utils.GREY_BOLD_COLOR;
    objTxtLongitud            = Utils.objText();
    objTxtLongitud.id         = 'objTxtLongitud';
    objTxtLongitud.name       = 'objTxtLongitud';
    objTxtLongitud.width      = 40; 
    objTxtLongitud.allowBlank = false;
    objTxtLongitud.maskRe     = /[\d]/;
    objTxtLongitud.regex      = /^(?:36[0]|3[0-5][0-9]|[12][0-9][0-9]|[1-9]?[0-9])?$/;
    objTxtLongitud.blankText  = 'Ingrese los grados por favor';
    objTxtLongitud.regexText  = 'Grados - Ingrese solo numeros entre 0-360';

    var objLblLongitudGrados        = Utils.objLabel();
    objLblLongitudGrados.text       = '°';
    objTxtLongitudGrados            = Utils.objText();
    objLblLongitudGrados.style      = Utils.GREY_BOLD_COLOR;
    objTxtLongitudGrados.id         = 'objTxtLongitudGrados';
    objTxtLongitudGrados.name       = 'objTxtLongitudGrados';
    objTxtLongitudGrados.width      = 40; 
    objTxtLongitudGrados.allowBlank = false;
    objTxtLongitudGrados.maskRe     = /[\d]/;
    objTxtLongitudGrados.regex      = /^[1-5]?[0-9]$/;
    objTxtLongitudGrados.blankText  = 'Ingrese los minutos por favor';
    objTxtLongitudGrados.regexText  = 'Minutos - Ingrese solo numeros entre 0-59';
     
    var objLblLongitudMinutos        = Utils.objLabel();
    objLblLongitudMinutos.text       = "'";
    objTxtLongitudMinutos            = Utils.objText();
    objLblLongitudMinutos.style      = Utils.GREY_BOLD_COLOR;
    objTxtLongitudMinutos.id         = 'objTxtLongitudMinutos';
    objTxtLongitudMinutos.name       = 'objTxtLongitudMinutos';
    objTxtLongitudMinutos.width      = 40; 
    objTxtLongitudMinutos.allowBlank = false;
    objTxtLongitudMinutos.maskRe     = /[\d]/;
    objTxtLongitudMinutos.regex      = /^[1-5]?[0-9]$/;
    objTxtLongitudMinutos.blankText  = 'Ingrese los segundos por favor';
    objTxtLongitudMinutos.regexText  = 'Segundos - Ingrese solo numeros entre 0-59';

    var objLblLongitudDecimales        = Utils.objLabel();
    objLblLongitudDecimales.text       = '.';
    objTxtLongitudDecimales            = Utils.objText();
    objLblLongitudDecimales.style      = Utils.GREY_BOLD_COLOR;
    objTxtLongitudDecimales.id         = 'objTxtLongitudDecimales';
    objTxtLongitudDecimales.name       = 'objTxtLongitudDecimales';
    objTxtLongitudDecimales.width      = 40; 
    objTxtLongitudDecimales.allowBlank = false;
    objTxtLongitudDecimales.maskRe     =  /[\d]/;
    objTxtLongitudDecimales.regex      =  /^\d{1,3}$/;
    objTxtLongitudDecimales.blankText  = 'Ingrese las décimas de segundos por favor';
    objTxtLongitudDecimales.regexText  = 'Décimas Segundos - Ingrese solo numeros entre 0-999';

    var objLblSeleccionLongitud        = Utils.objLabel();
    objLblSeleccionLongitud.text       = '"';
    objCmbSeleccionLongitud            = objComboPuntosCardinales('EO');
    objCmbSeleccionLongitud.setValue('NULL');
    objLblSeleccionLongitud.style      = Utils.GREY_BOLD_COLOR;
    objCmbSeleccionLongitud.id         = 'objCmbSeleccionLongitud';
    objCmbSeleccionLongitud.name       = 'objCmbSeleccionLongitud';
    objCmbSeleccionLongitud.width      = 70; 
    objCmbSeleccionLongitud.allowBlank = false;
    
    objTxtLongitudUbicacion            = Utils.objText();
    objTxtLongitudUbicacion.id         = 'objTxtLongitudUbicacion';
    objTxtLongitudUbicacion.name       = 'objTxtLongitudUbicacion';
    objTxtLongitudUbicacion.hidden     = true;
    
    var objBtnMapa        = objBotonMapa();
    objBtnMapa.id         = 'objBtnMapa';
    objBtnMapa.name       = 'objBtnMapa';
    objBtnMapa.icon       = iconMap;
    objBtnMapa.cls        = 'button-grid-Gmaps';
    
    var objContainerLongitud = Ext.create('Ext.container.Container', {
                colspan:   6,
                bodyStyle: 'margin: 1px 20px;',
                layout: {
                    tdAttrs: {
                        style: 'padding: 1px 2px;'
                    },
                    type:    'table',
                    columns: 12,
                    pack:    'center'
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
                        objTxtLongitudUbicacion,
                        objBtnMapa
                    ]
            });   
        
        formCreaParametrosDet.add(objCmbTipoElementoRuta);
        formCreaParametrosDet.add(objCmbTipoElemento);  
        formCreaParametrosDet.add(objTxtNombreElemento);            
        formCreaParametrosDet.add(objTarDescripcionElemento);        
        formCreaParametrosDet.add(objCmbJurisdiccion);        
        formCreaParametrosDet.add(objCmbCanton);
        formCreaParametrosDet.add(objCmbParroquia);
        formCreaParametrosDet.add(objTxtDireccion);
        formCreaParametrosDet.add(objTxtAltura);
        formCreaParametrosDet.add(objCmbElementoContenedor);
        formCreaParametrosDet.add(objTxtCosto);
        formCreaParametrosDet.add(objCmbPropietario);      
        formCreaParametrosDet.add(objCmbTipoLugar);  
        formCreaParametrosDet.add(objUbicadoEn);
        formCreaParametrosDet.add(objCmbNivel);
        formCreaParametrosDet.add(objChkFactible);
        formCreaParametrosDet.add(objContainerLatitud);
        formCreaParametrosDet.add(objContainerLongitud);
                
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
        objCmbTipoElemento.disable();
        objTxtAltura.disable();
        objTxtDireccion.disable();
        objCmbElementoContenedor.disable();
        objContainerLatitud.disable();
        objContainerLongitud.disable();
        objCmbNivel.disable(); 
                
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
            if(strModelo.indexOf('STANDARD') != -1)
            {
                objCmbNivel.enable();
            }
            if(strModelo.indexOf('LDD') != -1)
            {
                objCmbTipoLugar.enable();
            }            
            //objCmbTipoLugar.enable();
            //elementos contenedores que se van a cargar segun el tipo de elemento
            storeElementoContenedor.proxy.extraParams = {tipoElemento: 'EDIFICACION' , estado : 'Activo'};                        
            storeElementoContenedor.load({params: {}});            
        }        
        
    }

    function formularioDinamico (strTipoElemento)
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
        objChkFactible.setValue(false);
                
        if(strTipoElemento ==  'CAJA DISPERSION' )
        {
            objCmbNivel.setValue("");
            objCmbTipoElemento.enable();
        }
        else
        {   
            if(strTipoElemento ==  'MANGA' || strTipoElemento ==  'RESERVA' )
            {
                if (strTipoElemento ==  'MANGA')
                {
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
            objCmbTipoLugar.setValue("");
            objCmbPropietario.setValue("");
            objCmbTipoElemento.setValue("");
            objTxtCosto.setValue("");
            objCmbElementoContenedor.setValue("");
            objCmbNivel.setValue("");
                    

            if (strTipoElemento == 'POZO')
            {
                objCmbElementoContenedor.enable();
                strTipoLugar = 'SOTERRADO';
                objCmbPropietario.enable();
            }
            else if(strTipoElemento == 'POSTE')
            {
                strTipoLugar = 'AEREO';
                objCmbPropietario.enable();
                objTxtCosto.enable();
            }
        }
}
    
});

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


