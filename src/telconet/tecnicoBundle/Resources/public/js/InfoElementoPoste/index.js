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

Ext.onReady(function () 
{
    Ext.tip.QuickTipManager.init();
    
    var verPoste = function(grid, rowIndex, colIndex){
        var rec = store.getAt(rowIndex);
    
        var formVerPoste = Ext.create('Ext.form.Panel', {
            id:          'formVerPoste',
            bodyStyle:   'padding: 20px 10px 0; background:#FFFFFF;',
            bodyPadding: 15,
            autoScroll:  false,
                layout: {
                    type:    'table',
                    columns: 4,
                    tableAttrs: {
                        style: {
                            width:  '90%',
                            height: '90%'
                        }
                    },
                    tdAttrs: {
                        style: ' padding: 5px;',
                        align:  'left',
                        valign: 'middle'
                    }
                },
                items: []
        });         
        
        var objLblNombreElemento       = Utils.objLabel();
        objLblNombreElemento.style     = Utils.STYLE_BOLD;
        objLblNombreElemento.text      = "Nombre Elemento:";        
        var objLblValorNombreElemento  = Utils.objLabel();
        objLblValorNombreElemento.text = rec.get('nombreElemento');
        
        var objLblEstado               = Utils.objLabel();
        objLblEstado.style             = Utils.STYLE_BOLD;
        objLblEstado.text              = "Estado: ";        
        var objLblValorEstado          = Utils.objLabel();
        objLblValorEstado.text         = rec.get('estado');
        
        var objLblDescElemento         = Utils.objLabel();
        objLblDescElemento.style       = Utils.STYLE_BOLD;
        objLblDescElemento.text        = "Descripción: ";        
        var objLblValorDescElemento    = Utils.objLabel();
        objLblValorDescElemento.text   = rec.get('descripcionElemento');
        
        var objLblTipo                 = Utils.objLabel();
        objLblTipo.style               = Utils.STYLE_BOLD;
        objLblTipo.text                = "Tipo: ";        
        var objLblValorTipo            = Utils.objLabel();
        objLblValorTipo.text           = rec.get('tipoElemento');
        
        var objLblCanton               = Utils.objLabel();
        objLblCanton.style             = Utils.STYLE_BOLD;
        objLblCanton.text              = "Cantón: ";        
        var objLblValorCanton          = Utils.objLabel();
        objLblValorCanton.text         = rec.get('cantonNombre');
        
        var objLblDireccion            = Utils.objLabel();
        objLblDireccion.style          = Utils.STYLE_BOLD;
        objLblDireccion.text           = "Dirección: ";        
        var objLblValorDireccion       = Utils.objLabel();
        objLblValorDireccion.text      = rec.get('direccionUbicacion');
        
        var objLblPropietario          = Utils.objLabel();
        objLblPropietario.style        = Utils.STYLE_BOLD;
        objLblPropietario.text         = "Propietario: ";        
        var objLblValorPropietario     = Utils.objLabel();
        objLblValorPropietario.text    = rec.get('nombrePropietario');
        
        var objLblParroquia            = Utils.objLabel();
        objLblParroquia.style          = Utils.STYLE_BOLD;
        objLblParroquia.text           = "Parroquia: ";        
        var objLblValorParroquia       = Utils.objLabel();
        objLblValorParroquia.text      = rec.get('nombreParroquia');
        
        var objLblLatitud              = Utils.objLabel();
        objLblLatitud.style            = Utils.STYLE_BOLD;
        objLblLatitud.text             = "Latitud: ";        
        var objLblValorLatitud         = Utils.objLabel();
        objLblValorLatitud.text        = rec.get('latitudUbicacion');
        
        var objLblLongitud             = Utils.objLabel();
        objLblLongitud.style           = Utils.STYLE_BOLD;
        objLblLongitud.text            = "Longitud: ";        
        var objLblValorLongitud        = Utils.objLabel();
        objLblValorLongitud.text       = rec.get('longitudUbicacion');
        
        var objLblCosto                = Utils.objLabel();
        objLblCosto.style              = Utils.STYLE_BOLD;
        objLblCosto.text               = "Costo: ";        
        var objLblValorCosto           = Utils.objLabel();
        objLblValorCosto.text          = rec.get('costoElemento');
        
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
        
        
        var storeHistorial = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url:  url_getHistorialElementos,
            reader: {
                type:          'json',
                totalProperty: 'total',
                root:          'encontrados'
            }
        },
        fields:
            [
                {name: 'estado_elemento', mapping: 'estado_elemento'},
                {name: 'fe_creacion',     mapping: 'fe_creacion'},
                {name: 'usr_creacion',    mapping: 'usr_creacion'},
                {name: 'observacion',     mapping: 'observacion'}
            ]
        });
        
        storeHistorial.load({params: {
            idElemento: rec.get('idElemento')        
        }});
        
        var formVerHistorialPoste = Ext.create('Ext.grid.Panel', {
            width:    930,
            height:   350,
            store:    storeHistorial,
            loadMask: true,
            frame:    false,
            bodyStyle: {
                background: '#fff'
            },
            collapsible: true,
            collapsed:   false,
            title:       'Historial de Elemento',
            viewConfig:  {enableTextSelection: true},
            columns: [
                {
                    id:        'estado_elemento',
                    header:    'Estado',
                    dataIndex: 'estado_elemento',
                    width:     100,
                    sortable:  true
                },
                {
                    id:        'fe_creacion',
                    header:    'Fecha Creación',
                    dataIndex: 'fe_creacion',
                    width:     100,
                    sortable:  true
                },
                {
                    id:        'usr_creacion',
                    header:    'Usuario Creación',
                    dataIndex: 'usr_creacion',
                    width:     150,
                    sortable:  true
                },
                {
                    id:        'observacion',
                    header:    'Observación',
                    dataIndex: 'observacion',
                    width:     300,
                    sortable:  true
                }
            ]
        });
        
        btnregresar = Ext.create('Ext.Button', {
        text: 'Cerrar',
        cls:  'x-btn-rigth',
            handler: function() {
                windowVerPoste.destroy();
            }
        });
        
        var windowVerPoste = Ext.widget('window', {
            title:       'Información de Poste ' + rec.get('nombreElemento'),
            id:          'windowVerPoste',
            height:      455,
            width:       900,
            modal:       true,
            resizable:   false,
            closeAction: 'destroy',
            items:       [formVerPoste,
                         formVerHistorialPoste],
            buttonAlign: 'center',
            buttons:     [btnregresar]                
        });
        windowVerPoste.show();
    };
    
    var verElementoContenido = function(grid, rowIndex, colIndex) {
        var rec                    = store.getAt(rowIndex);
        var storeElementoContenido = new Ext.data.Store({            
                total: 'total',
                autoLoad:true,
                proxy:
                    {                    
                        type: 'ajax',
                        url:  url_verContenidos,
                        reader:
                        {
                            type:          'json',
                            totalProperty: 'total',
                            root:          'encontrados'
                        },
                        extraParams:
                        {   
                            idNodo:          rec.get('idElemento'),
                            strTipoElemento: 'POSTE'
                        }
                    },
                fields:
                    [
                        {name:'idElemento',       mapping: 'idElemento'},
                        {name:'idModeloElemento', mapping: 'idModeloElemento'},
                        {name:'nombreElemento',   mapping: 'nombreElemento'},
                        {name:'modeloElemento',   mapping: 'modeloElemento'},
                        {name:'tipoElemento',     mapping: 'tipoElemento'},
                        {name:'estado',           mapping: 'estado'}                   
                    ]
            });

        Ext.define('elementoContenidoModelo', {
            extend: 'Ext.data.Model',
            fields: [
                {name:'id',               mapping: 'id'},
                {name:'idElemento',       mapping: 'idElemento'},
                {name:'idModeloElemento', mapping:'idModeloElemento'},
                {name:'nombreElemento',   mapping: 'nombreElemento'},
                {name:'modeloElemento',   mapping: 'modeloElemento'},
                {name:'tipoElemento',     mapping: 'tipoElemento'},
                {name:'estado',           mapping: 'estado'},
                {name:'nuevo',            mapping: 'nuevo'}
            ]
        });
        
        var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 1,
            listeners: {
                edit: function(){                
                    gridElementoContenido.getView().refresh();
                }
            }
        });
        
        var selEspacioModelo = Ext.create('Ext.selection.CheckboxModel', {
            listeners: {
                selectionchange: function(sm, selections) 
                {   
                    gridElementoContenido.down('#btnQuitar').setDisabled(selections.length === 0);
                    
                }
            }
        });
    
        var toolbarElementosContenidos = Ext.create('Ext.toolbar.Toolbar', {
            dock: 'top',
            align: '->',
            items: 
               [{xtype: 'tbfill'},
                    {
                        xtype:   'button',
                        id:      "btnAgregar",
                        iconCls: "icon_anadir",
                        text:    'Agregar',
                        scope:    this,
                        handler: function ()  {
                            var r = Ext.create('elementoContenidoModelo', {
                                idElemento:         '0',
                                idModeloElemento:   '0',
                                nombreElemento:     '',
                                modeloElemento:     '',
                                idTipoElemento:     '',
                                nombreTipoElemento: '',
                                estado:             '',
                                id:                 '0',
                                nuevo:              '1'
                            });
                           
                            var intCountGridElementos = gridElementoContenido.getStore().getCount();
                            if (intCountGridElementos === 0)
                            {
                                storeElementoContenido.insert(0, r);
                                cellEditing.startEditByPosition({row: 0, column: 1});
                            }
                            if (intCountGridElementos >= 1)
                            {
                                if ('' === gridElementoContenido.getStore().getAt(0).data.modeloElemento.trim() ||
                                    '' === gridElementoContenido.getStore().getAt(0).data.estado.trim())
                                {
                                    Ext.Msg.alert('Alerta!', 'Debe ingresar la información del elemento.');
                                    cellEditing.startEditByPosition({row: 0, column: 1});
                                }
                                else
                                { 
                                    storeElementoContenido.insert(0, r);
                                    cellEditing.startEditByPosition({row: 0, column: 1});
                                }
                            }
                           
                        }  
                    },
                    {
                        xtype:    'button',
                        id:       "btnQuitar",
                        iconCls:  "icon_remover",
                        text:     'Quitar',
                        scope:    this,
                        disabled: true,
                        handler: function (){
                            var intCountGridElementos = gridElementoContenido.getStore().getCount();
                            if (intCountGridElementos !== 0)
                                {
                                    if (selEspacioModelo.getSelection().length > 0)
                                        {
                                            var arraySeleccionados = new Array();
                                            var arraySeleccionados1 = new Array();
                                            var arraySeleccionados2 = new Array();
                                            var strJsonElementosEliminarB = '';

                                            var intX = 0;
                                            var intY = 0;
                                            for (var i = 0; i < selEspacioModelo.getSelection().length; i++) 
                                            {  
                                                if(selEspacioModelo.getSelection()[i].data.nuevo !== '1')
                                                {
                                                   arraySeleccionados[intX] = selEspacioModelo.getSelection()[i].data.idElemento;
                                                   arraySeleccionados1[intX] = selEspacioModelo.getSelection()[i];
                                                   intX++;
                                                }
                                                else
                                                {
                                                    arraySeleccionados2[intY] = selEspacioModelo.getSelection()[i];
                                                    intY++;
                                                }
                                            }
                                            if (arraySeleccionados2!== null || arraySeleccionados2.lenght >0)
                                            {
                                                gridElementoContenido.getStore().remove(arraySeleccionados2);
                                            }
                                            
                                            if (arraySeleccionados!== null && arraySeleccionados.length >0 )
                                            {
                                                strJsonElementosEliminarB = Ext.JSON.encode(arraySeleccionados);
                                                Ext.get(document.body).mask('Guardando datos...');
                                                Ext.Ajax.request({
                                                    url :    urlEliminaRelacionElementoSave,
                                                    method : 'POST',
                                                    params :
                                                    {
                                                        idElementoA:  rec.get('idElemento'),   
                                                        strElemntosB: strJsonElementosEliminarB
                                                    },
                                                    success:function(response)
                                                    {
                                                        Ext.get(document.body).unmask();
                                                        var json = Ext.JSON.decode(response.responseText);
                                                        Ext.Msg.alert('Mensaje', json.strMessageStatus);
                                                        gridElementoContenido.getStore().remove(arraySeleccionados1);
                                                    },
                                                    failure:function(result)
                                                    {
                                                        Ext.get(document.body).unmask();
                                                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                                    }
                                                });

                                            }
                                        }
//                                    }
                                }
                        }
                    }
                ]
        });
        
        var storeBuscaTipoElemento = new Ext.data.Store({
        total:    'total',
        pageSize : '',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: url_getTipoElemento,
            reader: {
                type:          'json',
                totalProperty: 'total',
                root:          'encontrados'
            },
            extraParams:
            {  
                nombre: 'CAJA DISPERSION',
                estado: 'Activo'
            }
        },
        fields:
            [
                {name: 'idTipoElemento'     , mapping: 'idTipoElemento'},
                {name: 'nombreTipoElemento' , mapping: 'nombreTipoElemento'}
            ]
        });
        //storeBuscaTipoElemento.load();
        
        var storeBuscaElemento = new Ext.data.Store({
        total:     'total',
        pageSize : '',
        proxy: {
            type: 'ajax',
            url:  url_getElementoB,
            reader: {
                type:          'json',
                totalProperty: 'total',
                root:          'resultado'
            }
        },
        fields:
            [
                {name: 'idElemento'     , mapping: 'id_elemento'},
                {name: 'nombreElemento' , mapping: 'nombre_elemento'},
                {name: 'modeloElemento' , mapping: 'nombre_modelo_elemento'},
                {name: 'estadoElemento' , mapping: 'estado'}
            ]
        });
        
        gridElementoContenido = Ext.create('Ext.grid.Panel',
        {
            width:     '100%',
            height:     300,
            store:       storeElementoContenido,
            loadMask:    true,
            frame:       false,   
            dockedItems: [toolbarElementosContenidos],
            plugins:     [cellEditing],
            selModel:    selEspacioModelo,       
            viewConfig: {
                stripeRows: true
            },
            columns:
                [   
                    {
                        header:    'nuevo',
                        id:        'nuevo',
                        dataIndex: 'nuevo',
                        hidden:    true,
                        hideable:  false                        
                    },
                    {
                        header:    'idElementoA',
                        id:        'id',
                        dataIndex: 'id',
                        hidden:    true,
                        hideable:  false,
                        value:     rec.get('idElemento'),
                        
                    },
                    {
                        header:    'idTipoElemento',
                        id:        'idTipoElemento',
                        dataIndex: 'idTipoElemento',
                        hidden:    true,
                        hideable:  false
                    },
                    {
                        header:    'Tipo Elemento',
                        id:        'tipoElemento',
                        dataIndex: 'tipoElemento',
                        flex:      1,
                        sortable:  true,
                        editor: 
                        { 
                            id:            'objCmbBuscaTipoElemento',
                            xtype:         'combobox',
                            typeAhead:     true,
                            displayField:  'nombreTipoElemento',
                            valueField:    'idTipoElemento',
                            queryMode:     "local",
                            selectOnFocus: true,
                            allowBlank:    false,
                            store:         storeBuscaTipoElemento,
                            listClass:     'x-combo-list-small',
                            listeners: {
                                select: function(combo, record, index) 
                                {
                                    storeBuscaElemento.load({params: {
                                        tipoElementoId: combo.value,
                                        estado:         'Activo',
                                        canton:         rec.get('cantonId')
                                    }});
                                }
                            }
                        },
                        renderer: function(value, metadata, record, rowIndex, colIndex, store) 
                        {   
                            var tipoElementoIdTmp = value;
                            for (var i = 0; i < storeBuscaTipoElemento.data.items.length; i++)
                            {   
                                if (storeBuscaTipoElemento.data.items[i].data.idTipoElemento === tipoElementoIdTmp)
                                {   
                                    record.data.idTipoElemento = storeBuscaTipoElemento.data.items[i].data.idTipoElemento;
                                    return storeBuscaTipoElemento.data.items[i].data.nombreTipoElemento;
                                }
                            }                                
                            return value;
                        }
                    },
                    {
                        header:    'idElementoB',
                        id:        'idElementoB',
                        dataIndex: 'idElemento',
                        hidden:    true
                    },
                    {
                        header:    'Nombre Elemento',
                        dataIndex: 'nombreElemento',
                        flex:      1,
                        sortable:  true, 
                        editor: 
                        {
                            id:            'objCmbBuscaNombreElemento',
                            xtype:         'combobox',
                            typeAhead:     true,
                            displayField:  'nombreElemento',
                            valueField:    'idElemento',
                            queryMode:     "local",
                            triggerAction: 'all',
                            editable:      true,
                            selectOnFocus: true,
                            loadingText:   'Buscando ...',
                            hideTrigger:   false,
                            allowBlank:    false,
                            store:         storeBuscaElemento,
                            lazyRender:    true,
                            listClass:     'x-combo-list-small',
                            listeners: {
                                select: function(combo) {
                                    var r = Ext.create('elementoContenidoModelo', {
                                        idElemento:         combo.value,
                                        idModeloElemento:   0,
                                        nombreElemento:     combo.rawValue,
                                        modeloElemento:     '',
                                        idTipoElemento:     0,
                                        nombreTipoElemento: '',
                                        estado:             '',
                                        id:                 0
                                    });
                                    if(existeRecordElemento(r, gridElementoContenido))
                                    {  
                                        Ext.Msg.alert("Advertencia","Ya ingreso información de "+ r.raw.nombreElemento);
                                        eliminarSeleccionTipoElemento(gridElementoContenido);
                                    }
                                    else
                                    { 
                                        var record   = combo.findRecord(combo.valueField || combo.displayField, combo.value);
                                        var selModel = gridElementoContenido.getSelectionModel();
                                        selModel.getSelection()[0].set('idElementoB',    record.get('idElemento'));
                                        selModel.getSelection()[0].set('nombreElemento', record.get('nombreElemento'));
                                        selModel.getSelection()[0].set('modeloElemento', record.get('modeloElemento'));
                                        selModel.getSelection()[0].set('estado',         record.get('estadoElemento'));
                                    }
                                }
                            }
                        },
                        renderer: function(value, metadata, record, rowIndex, colIndex, store) 
                        {   
                            var elementoIdTmp = value;

                            for (var i = 0; i < storeBuscaElemento.data.items.length; i++)
                            {   
                                if (storeBuscaElemento.data.items[i].data.idElemento === elementoIdTmp)
                                {   
                                    record.data.idElemento = storeBuscaElemento.data.items[i].data.idElemento;
                                    return storeBuscaElemento.data.items[i].data.nombreElemento;
                                }
                            }                                
                            return value;
                        }
                    },
                    {
                        header:    'Modelo',
                        id    :    'modeloElemento',
                        dataIndex: 'modeloElemento',
                        width:     '20%',
                        sortable:  true
                    },
                    {
                        header:    'Estado',
                        id:        'estado',
                        dataIndex: 'estado',
                        width:     '10%',
                        sortable:  true
                    }
                ]
        });
    
        function existeRecordElemento(myRecord, grid)
            {    
                var existe = false;        

                var num = grid.getStore().getCount();    
     
                for (var i = 0; i < num; i++)
                {
                    var idElemento = grid.getStore().getAt(i).data.idElemento;   
              
                    if (idElemento === myRecord.raw.idElemento)
                    {
                        existe = true;
                        break;
                    }
                }
                return existe;
            }

        function eliminarSeleccionTipoElemento(datosSelect)
        {
        for (var i = 0; i < datosSelect.getSelectionModel().getCount(); i++)
        {
            datosSelect.getStore().remove(datosSelect.getSelectionModel().getSelection()[i]);
        }
    }
    
        formRelacionElemento = Ext.create('Ext.form.Panel', {
            bodyPadding:   5,
            waitMsgTarget: true,
            layout:        'column',
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 150,
                msgTarget: 'side'
            },
            items:
                [
                    {
                        xtype:      'fieldset',
                        autoHeight: true,
                        width:      700,
                        items:
                            [gridElementoContenido]
                    }
                ],
            buttonAlign: 'center',
            buttons: 
                [
                    {
                        text:     'Guardar',
                        name:     'btnGuardar',
                        align:    'center',
                        buttonAlign: 'center',
                        id:       'idBtnGuardar',
                        disabled:  false,
                        handler:   function () 
                        {
                            var intCountGridElementos = gridElementoContenido.getStore().getCount();
                            var strJsonElementosB =     '';
                            var arrElementosB =         [];

                            if (intCountGridElementos !== 0)
                            {   
                                if ('' === gridElementoContenido.getStore().getAt(0).data.modeloElemento.trim() ||
                                    '' === gridElementoContenido.getStore().getAt(0).data.estado.trim())
                                {
                                    Ext.Msg.alert('Alerta!', 'Registro en blanco, debe ingresar la información del elemento.');
                                    return;
                                }
                                else
                                {
                                    for (var i=0; i<intCountGridElementos ; i++)
                                    { 
                                        var intIdElementoB = gridElementoContenido.getStore().getAt(i).data.idElementoB;

                                        if (intIdElementoB !== null &&  intIdElementoB > 0 )
                                        {
                                            arrElementosB[i]= gridElementoContenido.getStore().getAt(i).data.idElementoB;                    
                                        }
                                    }
                                    if (arrElementosB === null || arrElementosB.length ===0 )
                                    {
                                        Ext.Msg.alert('Alerta ', 'Error: Debe ingresar la información del elemento.' );
                                        return;
                                    }

                                    strJsonElementosB = Ext.JSON.encode(arrElementosB);
                                    Ext.get(document.body).mask('Guardando datos...');
                                    Ext.Ajax.request({
                                        url :    urlElementoRelacionadoSave,
                                        method : 'POST',
                                        params :
                                        {
                                            idElementoA:  rec.get('idElemento'),   
                                            strElemntosB: strJsonElementosB
                                        },
                                        success:function(response)
                                        {
                                            Ext.get(document.body).unmask();
                                            var json = Ext.JSON.decode(response.responseText);
                                            Ext.Msg.alert('Mensaje', json.strMessageStatus);
                                            store.load();
                                            winElementoContenido.destroy();
                                        },
                                        failure:function(result)
                                        {
                                            Ext.get(document.body).unmask();
                                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                        }
                                    });
                                }
                            }
                        }
                    },
                    {
                        text:        'Cancelar',
                        buttonAlign: 'center',
                        listeners: {
                            click: function() 
                            {
                                winElementoContenido.destroy();
                            }
                        }   
                    }   
                ]
        });        
    
        winElementoContenido = Ext.create('Ext.window.Window', {
            title:     'Elementos Contenidos en POSTE',
            modal:     true,
            width:     725,        
            resizable: false,
            layout:    'fit',
            items:     [formRelacionElemento]
        }).show();
    };
            
    var btnVerPoste = Ext.create('Ext.button.Button', {
            text:  'Ver',
            scope: this,
            style: {
                marginTop: '10px'
            },
            getClass: function(v, meta, rec)
            {
                if(boolPermisoAdminPoste)
                {
                    return 'button-grid-show';
                }
                return '';
            },
            tooltip: 'Ver Poste',
            handler: verPoste
    });
    
    var btnVerElementosC = Ext.create('Ext.button.Button', {
            text: 'Ver',
            scope: this,
            style: {
                marginTop: '10px'
            },
            getClass: function(v, meta, rec)
            {
                var strEstado = rec.get('estado');
                if (strEstado !== 'Eliminado' && boolPermisoAdminPoste) 
                {  
                    return 'button-grid-Tuerca';
                }
                return '';
            },
            tooltip: 'Elemento Contenido',
            handler: verElementoContenido
    });
       
    var editarPoste = function(grid, rowIndex, colIndex){
        var rec = store.getAt(rowIndex);
        
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
                store: storeJurisdiccion,
                queryMode:    'local',
                displayField: 'nombreJurisdiccion',
                valueField:   'idJurisdiccion',
                listeners: {
                    select: function(records) {
                        objCmbCanton.clearValue();
                        objCmbParroquia.clearValue();
                        storeCanton.loadData([],false);
                        storeCanton.load({params: {
                            jurisdiccionId: objCmbJurisdiccion.getValue(),
                            estado        : 'Activo'
                        }});
                    }
                }
            });
        };

        var objComboCanton = function() {
            return Ext.create('Ext.form.ComboBox', {
                store:        storeCanton,
                queryMode:    'local',
                displayField: 'nombreCanton',
                valueField:   'canton_id',
                listeners: {
                    select: function(records) {
                        objCmbParroquia.clearValue(); 
                        storeParroquia.loadData([],false);
                        storeParroquia.load({params: {
                            cantonId: objCmbCanton.getValue()      
                        }});

                    }
                }
            });
        };

        var objComboParroquia = function() {
            return Ext.create('Ext.form.ComboBox', {
                store:        storeParroquia,
                queryMode:    'local',
                displayField: 'nombre_parroquia',
                valueField:   'id_parroquia'
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
                    id:      'storeIdPersonaEmpresaRol',
                    model:   'modelPropietario',
                    autoLoad: true,
                    proxy: {
                        type:    'ajax',
                        url:     url_getPropietarios,
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
                    queryMode:    'local',
                    displayField: 'nombre_propietario',
                    valueField:   'id_propietario'
                });
        };

        var objComboPuntosCardinales = function (strTipoCardinal) {

            var arrayNorteSur = [{"strCod": "NULL",  "strNombre": "Seleccione.."}, 
                                 {"strCod": "ESTE",  "strNombre": "Este"}, 
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
                valueField:   'strCod'
            });
        };

        var objComboTipo = function () {

            Ext.define('modelTipo', {
                    extend: 'Ext.data.Model',
                    fields: [
                        {name: 'idModeloElemento',     type: 'int'},
                        {name: 'nombreModeloElemento', type: 'string'}
                    ]
                });
                var objStoreTipo= Ext.create('Ext.data.Store', {
                    id: 'storeIdTipo',
                    model: 'modelTipo',
                    autoLoad: true,
                    proxy: {
                        type:    'ajax',
                        url:     strUrlGetModelo,
                        timeout: 600000,
                        reader: {
                            type: 'json',
                            root: 'encontrados'
                        },
                        extraParams: {
                            idMarca:       '',
                            tipoElemento: 'POSTE'

                        },
                        simpleSortMode: true
                    }
                });
            return Ext.create('Ext.form.ComboBox', {
                store:        objStoreTipo,
                queryMode:    'local',
                displayField: 'nombreModeloElemento',
                valueField:   'idModeloElemento'
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

        formEditElemento = Ext.create('Ext.form.Panel', {
            id:          'formEditElemento',
            bodyStyle:   'padding: 20px 10px 0; background:#FFFFFF;',
            bodyPadding: 15,
            autoScroll:  false,
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
                text:     'Guardar Poste',
                name:     'btnGuardar',
                id:       'idBtnGuardar',
                disabled: false,
                handler: function () {
                    var form = formEditElemento.getForm();
                    if (form.isValid())
                    {  
                        var data = form.getValues();
                        Ext.get(document.body).mask('Editando datos...');
                        Ext.Ajax.request({
                            url :    urlEditPoste,
                            method : 'POST',
                            params : data, 
                            success:function(response){                                 
                                Ext.get(document.body).unmask();
                                var json = Ext.JSON.decode(response.responseText);
                                Ext.Msg.alert('Mensaje', json.strMessageStatus);
                                store.load();
                                windowEditarElemento.destroy();
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
                    windowEditarElemento.destroy();
                }
            }]
        });
        
        var windowEditarElemento = Ext.widget('window', {
            title:       'Editar Poste',
            id:          'windowEditarElemento',
            height:      400,
            width:       820,
            modal:       true,
            resizable:   false,
            closeAction: 'destroy',
            items:       [formEditElemento],
            buttonAlign: 'center'
        });
        windowEditarElemento.show();

        var intWidth                    = 325;
        var objTxtNombreElemento        = Utils.objText();
        objTxtNombreElemento.style      = Utils.GREY_BOLD_COLOR;
        objTxtNombreElemento.id         = 'objTxtNombreElemento';
        objTxtNombreElemento.name       = 'objTxtNombreElemento';
        objTxtNombreElemento.fieldLabel = "*Nombre"; 
        objTxtNombreElemento.colspan    = 6;
        objTxtNombreElemento.width      = intWidth;
        objTxtNombreElemento.allowBlank = false;
        objTxtNombreElemento.blankText  = 'Ingrese nombre por favor';
        objTxtNombreElemento.setValue(rec.get('nombreElemento'));

        var objCmbPropietario           = objComboPropietario();
        objCmbPropietario.style         = Utils.GREY_BOLD_COLOR;
        objCmbPropietario.id            = 'objCmbPropietario';
        objCmbPropietario.name          = 'objCmbPropietario';
        objCmbPropietario.fieldLabel    = "*Propietario";        
        objCmbPropietario.colspan       = 6;
        objCmbPropietario.width         = intWidth; 
        objCmbPropietario.allowBlank    = false;
        objCmbPropietario.blankText     = 'Ingrese propietario por favor';
        objCmbPropietario.setValue(rec.get('idPropietario'));
        objCmbPropietario.setRawValue(rec.get('nombrePropietario'));

        var objTarDescripcionElemento        = Utils.objTextArea();
        objTarDescripcionElemento.style      = Utils.GREY_BOLD_COLOR;
        objTarDescripcionElemento.id         = 'objTarDescripcionElemento';
        objTarDescripcionElemento.name       = 'objTarDescripcionElemento';
        objTarDescripcionElemento.fieldLabel = "*Descripción"; 
        objTarDescripcionElemento.colspan    = 6;
        objTarDescripcionElemento.width      = intWidth;
        objTarDescripcionElemento.allowBlank = false;
        objTarDescripcionElemento.blankText  = 'Ingrese descripción por favor';
        objTarDescripcionElemento.setValue(rec.get('descripcionElemento'));

        var objCmbTipoElemento        = objComboTipo();
        objCmbTipoElemento.style      = Utils.GREY_BOLD_COLOR;
        objCmbTipoElemento.id         = 'objCmbTipoElemento';
        objCmbTipoElemento.name       = 'objCmbTipoElemento';
        objCmbTipoElemento.fieldLabel = "*Tipo"; 
        objCmbTipoElemento.colspan    = 6;
        objCmbTipoElemento.width      = intWidth; 
        objCmbTipoElemento.allowBlank = false;
        objCmbTipoElemento.blankText  = 'Ingrese tipo por favor';
        objCmbTipoElemento.setValue(rec.get('tipoElementoId'));
        objCmbTipoElemento.setRawValue(rec.get('tipoElemento'));
        
        var objCmbJurisdiccion        = objComboJurisdiccion();
        objCmbJurisdiccion.style      = Utils.GREY_BOLD_COLOR;
        objCmbJurisdiccion.id         = 'objCmbJurisdiccion';
        objCmbJurisdiccion.name       = 'objCmbJurisdiccion';
        objCmbJurisdiccion.fieldLabel = "*Jurisdicciones";
        objCmbJurisdiccion.colspan    = 12;
        objCmbJurisdiccion.width      = intWidth; 
        objCmbJurisdiccion.allowBlank = false;
        objCmbJurisdiccion.blankText  = 'Ingrese jurisdicción por favor';
        objCmbJurisdiccion.setValue(rec.get('jurisdiccionId'));
        objCmbJurisdiccion.setRawValue(rec.get('jurisdiccionNombre'));
        
        storeCanton.load({params: {
            jurisdiccionId: rec.get('jurisdiccionId'),
            estado        : 'Activo'
        }});

        var objCmbCanton        = objComboCanton();
        objCmbCanton.style      = Utils.GREY_BOLD_COLOR;
        objCmbCanton.id         = 'objCmbCanton';
        objCmbCanton.name       = 'objCmbCanton';
        objCmbCanton.fieldLabel = "*Cantón"; 
        objCmbCanton.colspan    = 6;
        objCmbCanton.width      = intWidth; 
        objCmbCanton.allowBlank = false;
        objCmbCanton.blankText  = 'Ingrese cantón por favor';
        objCmbCanton.setValue(rec.get('cantonId'));
        objCmbCanton.setRawValue(rec.get('nombreCanton'));
        
        storeParroquia.load({params: {
            cantonId: rec.get('cantonId')
        }});      
    
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
        objTxtCosto.setValue(rec.get('costoElemento'));
    

        var objCmbParroquia        = objComboParroquia();
        objCmbParroquia.style      = Utils.GREY_BOLD_COLOR;
        objCmbParroquia.id         = 'objCmbParroquia';
        objCmbParroquia.name       = 'objCmbParroquia';
        objCmbParroquia.fieldLabel = "*Parroquia"; 
        objCmbParroquia.colspan    = 12;
        objCmbParroquia.width      = intWidth; 
        objCmbParroquia.allowBlank = false;
        objCmbParroquia.blankText  = 'Ingrese parroquia por favor';
        objCmbParroquia.setValue(rec.get('parroquiaId'));
        objCmbParroquia.setRawValue(rec.get('nombreParroquia'));
      
        var objTxtIdParroquia        = Utils.objText();
        objTxtIdParroquia.id         = 'objTxtIdParroquia';
        objTxtIdParroquia.name       = 'objTxtIdParroquia';
        objTxtIdParroquia.hidden     = true;
        objTxtIdParroquia.setValue(rec.get('parroquiaId'));
        
        var objTxtIdElemento        = Utils.objText();
        objTxtIdElemento.id         = 'objTxtIdElemento';
        objTxtIdElemento.name       = 'objTxtIdElemento';
        objTxtIdElemento.hidden     = true;
        objTxtIdElemento.setValue(rec.get('idElemento'));
        
        var objTxtIdUbicacion        = Utils.objText();
        objTxtIdUbicacion.id         = 'objTxtIdUbicacion';
        objTxtIdUbicacion.name       = 'objTxtIdUbicacion';
        objTxtIdUbicacion.hidden     = true;
        objTxtIdUbicacion.setValue(rec.get('ubicacionId'));
   
        var objTxtDireccion        = Utils.objText();
        objTxtDireccion.style      = Utils.GREY_BOLD_COLOR;
        objTxtDireccion.id         = 'objTxtDireccion';
        objTxtDireccion.name       = 'objTxtDireccion';
        objTxtDireccion.fieldLabel = "*Dirección"; 
        objTxtDireccion.colspan    = 6;
        objTxtDireccion.width      = intWidth; 
        objTxtDireccion.allowBlank = false;
        objTxtDireccion.blankText  = 'Ingrese dirección por favor';
        objTxtDireccion.setValue(rec.get('direccionUbicacion'));



        var objTxtAltura        = Utils.objText();
        objTxtAltura.style      = Utils.GREY_BOLD_COLOR;
        objTxtAltura.id         = 'objTxtAltura';
        objTxtAltura.name       = 'objTxtAltura';
        objTxtAltura.fieldLabel = "*Altura Sobre Nivel Mar"; 
        objTxtAltura.colspan    = 6;
        objTxtAltura.width      = intWidth; 
        objTxtAltura.allowBlank = false;
        objTxtAltura.maskRe     = /[\d\.]/;
        objTxtAltura.regex      = /[0-9]+(\.[0-9][0-9]?)?/;
        objTxtAltura.blankText  = 'Ingrese altura sobre el nivel del mar por favor';
        objTxtAltura.regexText  = 'Altura sobre el nivel del mar - Ingrese solo numeros';
        objTxtAltura.setValue(rec.get('alturaSnm'));

        var objLblLatitud        = Utils.objLabel();
        objLblLatitud.style      = Utils.GREY_BOLD_COLOR;
        objLblLatitud.text       = 'Coordenadas Latitud';
        objTxtLatitud            = Utils.objText();
        objTxtLatitud.id         = 'objTxtLatitud';
        objTxtLatitud.name       = 'objTxtLatitud';
        objTxtLatitud.width      = 40; 
        objTxtLatitud.maskRe     = /[\d]/;
        objTxtLatitud.regex      = /^(?:36[0]|3[0-5][0-9]|[12][0-9][0-9]|[1-9]?[0-9])?$/;
        objTxtLatitud.regexText  = 'Grados - Ingrese solo numeros entre 0-360';

        var objLblLatitudGrados         = Utils.objLabel();
        objLblLatitudGrados.style       = Utils.GREY_BOLD_COLOR;
        objLblLatitudGrados.text        = '°';
        objTxtLatitudGrados             = Utils.objText();
        objTxtLatitudGrados.style       =  Utils.GREY_BOLD_COLOR;
        objTxtLatitudGrados.id          = 'objTxtLatitudGrados';
        objTxtLatitudGrados.name        = 'objTxtLatitudGrados';
        objTxtLatitudGrados.labelStyle  = 'padding: 0px 0px;';
        objTxtLatitudGrados.width       = 40; 
        objTxtLatitudGrados.maskRe      = /[\d]/;
        objTxtLatitudGrados.regex       = /^[1-5]?[0-9]$/;
        objTxtLatitudGrados.regexText   = 'Minutos - Ingrese solo numeros entre 0-59';

        var objLblLatitudMinutos        = Utils.objLabel();
        objLblLatitudMinutos.style      = Utils.GREY_BOLD_COLOR;
        objLblLatitudMinutos.text       = "'";
        objTxtLatitudMinutos            = Utils.objText();
        objTxtLatitudMinutos.style      =  Utils.GREY_BOLD_COLOR;
        objTxtLatitudMinutos.id         = 'objTxtLatitudMinutos';
        objTxtLatitudMinutos.name       = 'objTxtLatitudMinutos';
        objTxtLatitudMinutos.width      = 40; 
        objTxtLatitudMinutos.maskRe     = /[\d]/;
        objTxtLatitudMinutos.regex      = /^[1-5]?[0-9]$/;
        objTxtLatitudMinutos.regexText  = 'Segundos - Ingrese solo numeros entre 0-59';

        var objLblLatitudDecimales        = Utils.objLabel();
        objLblLatitudDecimales.style      = Utils.GREY_BOLD_COLOR;
        objLblLatitudDecimales.text       = '.';
        objTxtLatitudDecimales            = Utils.objText();
        objTxtLatitudDecimales.id         = 'objTxtLatitudDecimales';
        objTxtLatitudDecimales.name       = 'objTxtLatitudDecimales';
        objTxtLatitudDecimales.width      = 40;     
        objTxtLatitudDecimales.maskRe     =  /[\d]/;
        objTxtLatitudDecimales.regex      =  /^\d{1,3}$/;
        objTxtLatitudDecimales.regexText  = 'Décimas Segundos - Ingrese solo numeros entre 0-999';

        var objLblSeleccionLatitud        = Utils.objLabel();
        objLblSeleccionLatitud.style      = Utils.GREY_BOLD_COLOR;
        objLblSeleccionLatitud.text       = '"';
        objCmbSeleccionLatitud            = objComboPuntosCardinales('NS');
        objCmbSeleccionLatitud.setValue('NULL');
        objCmbSeleccionLatitud.id         = 'objCmbSeleccionLatitud';
        objCmbSeleccionLatitud.name       = 'objCmbSeleccionLatitud';
        objCmbSeleccionLatitud.width      = 70;      

        var objTxtAltura        = Utils.objText();
        objTxtAltura.style      = Utils.GREY_BOLD_COLOR;
        objTxtAltura.id         = 'objTxtAltura';
        objTxtAltura.name       = 'objTxtAltura';
        objTxtAltura.fieldLabel = "*Altura Sobre Nivel Mar"; 
        objTxtAltura.colspan    = 6;
        objTxtAltura.width      = intWidth; 
        objTxtAltura.allowBlank = false;
        objTxtAltura.maskRe     = /[\d\.]/;
        objTxtAltura.regex      = /[0-9]+(\.[0-9][0-9]?)?/;
        objTxtAltura.blankText  = 'Ingrese altura sobre el nivel del mar por favor';
        objTxtAltura.regexText  = 'Altura sobre el nivel del mar - Ingrese solo numeros';
        objTxtAltura.setValue(rec.get('alturaSnm'));
        
        objTxtLatitudUbicacion            = Utils.objText();
        objTxtLatitudUbicacion.style      = Utils.GREY_BOLD_COLOR;
        objTxtLatitudUbicacion.id         = 'objTxtLatitudUbicacion';
        objTxtLatitudUbicacion.name       = 'objTxtLatitudUbicacion';
        objTxtLatitudUbicacion.fieldLabel = "*Latitud"; 
        objTxtLatitudUbicacion.colspan    = 6;
        objTxtLatitudUbicacion.width      = intWidth; 
        objTxtLatitudUbicacion.allowBlank = false;
        objTxtLatitudUbicacion.maskRe     = /[\d\.]/;
        objTxtLatitudUbicacion.regex      = /[0-9]+(\.[0-9][0-9]?)?/;
        objTxtLatitudUbicacion.blankText  = 'Ingrese altura sobre el nivel del mar por favor';
        objTxtLatitudUbicacion.regexText  = 'Altura sobre el nivel del mar - Ingrese solo numeros';
        objTxtLatitudUbicacion.setValue(rec.get('latitudUbicacion'));


        var objContainerLatitud   = Ext.create('Ext.container.Container', {
                    colspan: 6,
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
                            objCmbSeleccionLatitud
                        ]
                });

        var objLblLongitud        = Utils.objLabel();
        objLblLongitud.style      = Utils.GREY_BOLD_COLOR;
        objLblLongitud.text       = 'Coordenadas Longitud';
        objTxtLongitud            = Utils.objText();
        objTxtLongitud.id         = 'objTxtLongitud';
        objTxtLongitud.name       = 'objTxtLongitud';
        objTxtLongitud.width      = 40; 
        objTxtLongitud.maskRe     = /[\d]/;
        objTxtLongitud.regex      = /^(?:36[0]|3[0-5][0-9]|[12][0-9][0-9]|[1-9]?[0-9])?$/;
        objTxtLongitud.regexText  = 'Grados - Ingrese solo numeros entre 0-360';

        var objLblLongitudGrados        = Utils.objLabel();
        objLblLongitudGrados.style      = Utils.GREY_BOLD_COLOR;
        objLblLongitudGrados.text       = '°';
        objTxtLongitudGrados            = Utils.objText();
        objTxtLongitudGrados.id         = 'objTxtLongitudGrados';
        objTxtLongitudGrados.name       = 'objTxtLongitudGrados';
        objTxtLongitudGrados.width      = 40; 
        objTxtLongitudGrados.maskRe     = /[\d]/;
        objTxtLongitudGrados.regex      = /^[1-5]?[0-9]$/;
        objTxtLongitudGrados.regexText  = 'Minutos - Ingrese solo numeros entre 0-59';

        var objLblLongitudMinutos        = Utils.objLabel();
        objLblLongitudMinutos.text       = "'";
        objTxtLongitudMinutos            = Utils.objText();
        objTxtLongitudMinutos.style      =  Utils.GREY_BOLD_COLOR;
        objTxtLongitudMinutos.id         = 'objTxtLongitudMinutos';
        objTxtLongitudMinutos.name       = 'objTxtLongitudMinutos';
        objTxtLongitudMinutos.width      = 40; 
        objTxtLongitudMinutos.maskRe     = /[\d]/;
        objTxtLongitudMinutos.regex      = /^[1-5]?[0-9]$/;
        objTxtLongitudMinutos.regexText  = 'Segundos - Ingrese solo numeros entre 0-59';

        var objLblLongitudDecimales        = Utils.objLabel();
        objLblLongitudDecimales.style      = Utils.GREY_BOLD_COLOR;
        objLblLongitudDecimales.text       = '.';
        objTxtLongitudDecimales            = Utils.objText();
        objTxtLongitudDecimales.id         = 'objTxtLongitudDecimales';
        objTxtLongitudDecimales.name       = 'objTxtLongitudDecimales';
        objTxtLongitudDecimales.width      = 40; 
        objTxtLongitudDecimales.maskRe     =  /[\d]/;
        objTxtLongitudDecimales.regex      =  /^\d{1,3}$/;
        objTxtLongitudDecimales.regexText  = 'Decimas Segundos - Ingrese solo numeros entre 0-999';

        var objLblSeleccionLongitud        = Utils.objLabel();
        objLblSeleccionLongitud.style      = Utils.GREY_BOLD_COLOR;
        objLblSeleccionLongitud.text       = '"';
        objCmbSeleccionLongitud            = objComboPuntosCardinales('EO');
        objCmbSeleccionLongitud.setValue('NULL');
        objCmbSeleccionLongitud.id         = 'objCmbSeleccionLongitud';
        objCmbSeleccionLongitud.name       = 'objCmbSeleccionLongitud';
        objCmbSeleccionLongitud.width      = 70; 

        objTxtLongitudUbicacion            = Utils.objText();
        objTxtLongitudUbicacion.id         = 'objTxtLongitudUbicacion';
        objTxtLongitudUbicacion.name       = 'objTxtLongitudUbicacion';
        objTxtLongitudUbicacion.hidden     = true;
        
        objTxtLongitudUbicacion            = Utils.objText();
        objTxtLongitudUbicacion.style      = Utils.GREY_BOLD_COLOR;
        objTxtLongitudUbicacion.id         = 'objTxtLongitudUbicacion';
        objTxtLongitudUbicacion.name       = 'objTxtLongitudUbicacion';
        objTxtLongitudUbicacion.fieldLabel = "*Longitud"; 
        objTxtLongitudUbicacion.colspan    = 6;
        objTxtLongitudUbicacion.width      = intWidth; 
        objTxtLongitudUbicacion.allowBlank = false;
        objTxtLongitudUbicacion.maskRe     = /[\d\.]/;
        objTxtLongitudUbicacion.regex      = /[0-9]+(\.[0-9][0-9]?)?/;
        objTxtLongitudUbicacion.blankText  = 'Ingrese altura sobre el nivel del mar por favor';
        objTxtLongitudUbicacion.regexText  = 'Altura sobre el nivel del mar - Ingrese solo numeros';
        objTxtLongitudUbicacion.setValue(rec.get('longitudUbicacion'));
        
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
        formEditElemento.add(objTxtLatitudUbicacion);
        formEditElemento.add(objTxtLongitudUbicacion);
        formEditElemento.add(objTxtIdParroquia);
        formEditElemento.add(objTxtIdElemento);
        formEditElemento.add(objTxtIdUbicacion);
        formEditElemento.add(objContainerLatitud);
        formEditElemento.add(objContainerLongitud);
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
                if (strEstado !== 'Eliminado' && boolPermisoAdminPoste) 
                {  
                    return 'button-grid-edit';
                }
                return '';
            },
            tooltip: 'Editar Elemento',
            handler: editarPoste
    });
    
    var eliminarPoste = function (rec) {
        var intIdElemento     = rec.get('idElemento');
        var strNombreElemento = rec.get('nombreElemento');
        var strEstado         = rec.get('estado');
        Ext.Msg.confirm('Alerta', 'Se Eliminará el Poste con código: ' + strNombreElemento + ' . Desea continuar?', function (btn) {
            if (btn === 'yes') {
                if (strEstado !== 'Eliminado') {      
                    
                    Ext.get(document.body).mask('Eliminando Poste...');
                    Ext.Ajax.request({
                        url:    urlDeletePoste,
                        method: 'post',
                        params: {
                            idElemento: intIdElemento
                        },
                        success:function(response)
                        {
                            Ext.get(document.body).unmask();
                            var json = Ext.JSON.decode(response.responseText);
                            Ext.Msg.alert('Mensaje', json.strMessageStatus);
                            store.load();
                        },
                        failure: function (result)
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
                if (strEstado !== 'Eliminado' && boolPermisoAdminPoste) 
                {  
                    return 'button-grid-delete';
                }
                return '';
            },
            tooltip: 'Eliminar Poste',
            handler: function(grid, rowIndex, colIndex) {
                var rec = store.getAt(rowIndex);                                
                eliminarPoste(rec);
            }
    });
    
    var storeCantones = new Ext.data.Store({
        total:    'total',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url:  url_getCantones,
            reader: {
                type:          'json',
                totalProperty: 'total',
                root:          'encontrados'
            }
        },
        fields:
            [
                {name: 'nombre_canton', mapping: 'nombre_canton'},
                {name: 'id_canton',     mapping: 'id_canton'}
            ]
    });
    
    var storePropietarios = new Ext.data.Store({
        total:    'total',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: url_getPropietarios,
            reader: {
                type:          'json',
                totalProperty: 'total',
                root:          'encontrados'
            }
        },
        fields:
            [
                {name: 'nombre_propietario', mapping: 'nombre_propietario'},
                {name: 'id_propietario',     mapping: 'id_propietario'}
            ]
    });

    store = new Ext.data.Store({
        pageSize: 10,
        total:    'total',
        proxy: {
            type:    'ajax',
            url:     url_getEncontradosPostes,
            timeout: 800000,
            reader: {
                type:          'json',
                totalProperty: 'total',
                root:          'encontrados'
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
                {name: 'idElemento',          mapping: 'id_elemento'},
                {name: 'nombreElemento',      mapping: 'nombre_elemento'},
                {name: 'descripcionElemento', mapping: 'descripcion_elemento'},
                {name: 'cantonNombre',        mapping: 'nombre_canton'},
                {name: 'cantonId',            mapping: 'id_canton'},
                {name: 'nombreRegion',        mapping: 'region'},
                {name: 'jurisdiccionId',      mapping: 'id_jurisdiccion'},
                {name: 'jurisdiccionNombre',  mapping: 'nombre_jurisdiccion'},
                {name: 'estado',              mapping: 'estado'},
                {name: 'longitudUbicacion',   mapping: 'longitud_ubicacion'},
                {name: 'latitudUbicacion',    mapping: 'latitud_ubicacion'},
                {name: 'direccionUbicacion',  mapping: 'direccion_ubicacion'},
                {name: 'parroquiaId',         mapping: 'id_parroquia'},
                {name: 'ubicacionId',         mapping: 'id_ubicacion'},
                {name: 'nombreParroquia',     mapping: 'nombre_parroquia'},
                {name: 'nombrePropietario',   mapping: 'razon_social'},
                {name: 'idPropietario',       mapping: 'id_persona_rol'},
                {name: 'costoElemento',       mapping: 'costo'},
                {name: 'tipoElemento',        mapping: 'modelo_elemento'},
                {name: 'tipoElementoId',      mapping: 'id_modelo_elemento'},
                {name: 'alturaSnm',           mapping: 'altura_snm'}
                
            ]
    });

    var pluginExpanded = true;

    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    });
   
    gridPostes = Ext.create('Ext.grid.Panel', {
        width:       930,
        height:      350,
        store:       store,
        loadMask:    true,
        dockedItems: [toolbar],
        frame:       false,
        viewConfig:  {enableTextSelection: true},
        columns: [
            {
                id:        'idElemento',
                header:    'idElemento',
                dataIndex: 'idElemento',
                hidden:    true,
                hideable:  false
            },
            {
                id:     'ipElemento',
                header: 'Poste',
                xtype:  'templatecolumn',
                width:  290,
                tpl:    '<span class="box-detalle">{nombreElemento}</span>\n\
                        <span class="bold">Jurisdicción:</span><span>{jurisdiccionNombre}</span></br>\n\
                        <span class="bold">Región:</span><span>{nombreRegion}</span></br>\n\
                        <span class="bold">Cantón:</span><span>{cantonNombre}</span></br>\n\\n\ '

            },
            {
                id:        'propietario',
                header:    'Propietario',
                dataIndex: 'nombrePropietario',
                width:     300,
                sortable:  true
            },
            {
                header:    'Estado',
                dataIndex: 'estado',
                width:     80,
                sortable:  true
            },
            {
                xtype:  'actioncolumn',
                header: 'Acciones',
                width:  275,
                items:  [
                            btnVerPoste,
                            btnVerElementosC,
                            btnEditarPoste,
                            btnEliminarPoste
                ]
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar', {
            store:       store,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg:   "No hay datos que mostrar."
        }),
        renderTo: 'grid'
    });
    
    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,
        border:      false,
        buttonAlign: 'center',
        layout: {
            type:    'table',
            columns: 4,
            align:   'stretch'
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed:   false,
        width:       930,
        title:       'Criterios de busqueda',
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function () {
                    buscar();
                }
            },
            {
                text:    'Limpiar',
                iconCls: "icon_limpiar",
                handler: function () {
                    limpiar();
                }
            }

        ],
        items: [
            {width: '5%', border: false},
            {
                xtype:      'textfield',
                id:         'txtCodigo',
                fieldLabel: 'Código',
                value:      '',
                width:      '40%'
            },            
            {
                xtype:        'combobox',
                id:           'sltPropietario',
                fieldLabel:   'Propietario',
                displayField: 'nombre_propietario',
                valueField:   'id_propietario',
                loadingText:  'Buscando ...',
                store:        storePropietarios,
                listClass:    'x-combo-list-small',
                queryMode:    'local',
                width:        '40%'
            },
            {width: '10%', border: false},
            {width: '5%',  border: false},
            {
                xtype:         'combobox',
                id:            'sltCanton',
                fieldLabel:    'Cantón',
                displayField:  'nombre_canton',
                valueField:    'id_canton',
                loadingText:   'Buscando ...',
                store:         storeCantones,
                listClass:     'x-combo-list-small',
                queryMode:     'local',
                width:         '40%'
            },
            {
                xtype:      'combobox',
                fieldLabel: 'Estado',
                id:         'sltEstado',
                value:      '',
                store:      [
                                ['',           'Todos'],
                                ['Activo',     'Activo'],
                                ['Modificado', 'Modificado'],
                                ['Eliminado',  'Eliminado']
                ],
                width: '40%'
            },
            {width: '10%', border: false}
        ],
        renderTo: 'filtro'
    });
    
    if (!Ext.isEmpty(strNombreElemento))
    {
        store.getProxy().extraParams.strCodigo = strNombreElemento;
        store.load();
    }
});

function buscar() {
    store.getProxy().extraParams.strCodigo      = Ext.getCmp('txtCodigo').value;
    store.getProxy().extraParams.strCanton      = Ext.getCmp('sltCanton').value;
    store.getProxy().extraParams.strPropietario = Ext.getCmp('sltPropietario').value;
    store.getProxy().extraParams.strEstado      = Ext.getCmp('sltEstado').value;
    store.load();

}

function limpiar() {
    Ext.getCmp('txtCodigo').value = "";
    Ext.getCmp('txtCodigo').setRawValue("");

    Ext.getCmp('sltCanton').value = "";
    Ext.getCmp('sltCanton').setRawValue("");

    Ext.getCmp('sltPropietario').value = "";
    Ext.getCmp('sltPropietario').setRawValue("");

    Ext.getCmp('sltEstado').value = "Todos";
    Ext.getCmp('sltEstado').setRawValue("Todos");
    
    store.load({params: {
        strCodigo:      Ext.getCmp('txtCodigo').value,
        strCanton:      Ext.getCmp('sltCanton').value,
        strPropietario: Ext.getCmp('sltPropietario').value,
        strEstado:      Ext.getCmp('sltEstado').value
    }});
}
