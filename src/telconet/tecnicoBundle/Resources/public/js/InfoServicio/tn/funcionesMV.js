
//Variables para editar Recursos
var boolCambioDisco = false;
var boolCambioProcesador= false;
var boolCambioMemoria = false;
var boolCambioLicencia = false;
var arrayValidaLicencias        =   [];
var arrayParametrosLicencias    =   [];

function imgAgregarRecursos(tipo,nombreMaquina)
{
    var recurso = '(GB)';
    if(tipo === 'PROCESADOR')
    {
        recurso = '(Cores)';
    }
    if(tipo === 'LICENCIA')
    {
        recurso = '(Unidades)';
    }
    var selector = Ext.create('Ext.Component', {
        html: recurso+'&nbsp;<i class="fa fa-plus-square" aria-hidden="true" style="cursor:pointer;" \n\
              onclick="showAgregarRecursos(\'' + tipo + '\',\''+ nombreMaquina +'\');"></i>',
        padding: 1,
        id:'img_'+tipo,
        layout: 'anchor',
        style: {color: 'black', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
    });
    
    return selector;
}

function imgShowRecursos(tipo)
{        
    var selector = Ext.create('Ext.Component', {
        html: '<a onclick="verDetalleRecurso(\'' + tipo + '\',\'resumenPrevio\'\);" style="cursor:pointer;"\n\
               title="Ver Resumen" class="ui-icon ui-icon-zoomin"></a>',
        padding: 1,
        layout: 'anchor',
        style: {color: 'black', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
    });
    
    return selector;
}

function verDetalleRecurso(tipo, accion)
{
    var array = [];
    var unidad= '';
    
    Ext.define('detalleRecursos', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'nombreRecurso', type: 'string'},                        
            {name: 'valor',         type: 'string'}
        ]
    }); 
                
    var storeDetalleRecursos = new Ext.data.Store({
        pageSize: 5,
        autoDestroy: true,
        model: 'detalleRecursos',
        proxy: {
            type: 'memory'
        }
    });
    
    //Peticion ajax para obtener del detalle de recursos contratados
    
    //Cuando se crea una maquina virtual previo a ser guardada
    if(accion === 'resumenPrevio')
    {
        var nombreActual = Ext.getCmp("txtNombreMaquinac").getValue();
        
        $.each(arrayInformacion, function(i, item) 
        {
            if(item.nombre === nombreActual)
            {
                if(item.arrayRecursos.length !== 0)
                {
                    var arrayRecursos = Ext.JSON.decode(item.arrayRecursos);

                    $.each(arrayRecursos, function(i, item1)
                    {                    
                        if(item1.tipo === tipo)
                        {
                            var json = {};
                            json['nombreRecurso'] = item1.caracteristica;
                            json['valor']         = item1.valor;
                            array.push(json);
                        }
                    });
                }
            }
        });
    }
    else//muestra informacion de recursos contratados ( pool )
    {
        switch(tipo)
        {
            case 'DISCO':
                array  = arrayRecursos.arrayDetalleDisco;
                unidad = '(GB)';
                break;

            case 'PROCESADOR':
                array  = arrayRecursos.arrayDetalleProcesador;
                unidad = '(Cores)';
                break;      
            
            case 'LICENCIA':
                array  = arrayRecursos.arrayDetalleLicencia;
                unidad = '(Unidades)';
                break;   

            default :
                array  = arrayRecursos.arrayDetalleMemoria;
                unidad = '(GB)';
                break;
        }
    }
    
    $.each(array,function(i , item)
    {
        var recordParamDet = Ext.create('detalleRecursos', {
            nombreRecurso: item.nombreRecurso,                        
            valor        : item.valor
        });

        storeDetalleRecursos.insert(i, recordParamDet);
    });
        
    var gridDetalleRecursos = Ext.create('Ext.grid.Panel', {
        width: 480,
        id:'gridDetalleRecursos',
        height: 120,
        store: storeDetalleRecursos,
        loadMask: true,
        frame: false,
        columns: [
            {
                id: 'nombreRecurso',
                header: '<b>Recurso Contratado</b>',
                dataIndex: 'nombreRecurso',
                width: 300
            },
            {
                id: 'valor',
                header: '<b>Cantidad Contratada</b>',
                dataIndex: 'valor',
                width: 130,
                align:'center',
                renderer: function(val)
                {                    
                    return '<label style="color:#4D793E;"><b>'+val+' '+unidad+'</b></label>';
                }
            }
        ]
    });  
        
    var formPanelDatosConsultarMV = Ext.create('Ext.form.Panel', {
        buttonAlign: 'center',
        BodyPadding: 10,
        width: 530,
        height: 200,
        bodyStyle: "background: white; padding: 5px; border: 0px none;",
        frame: true,
        items:
            [                    
                {
                    xtype: 'fieldset',
                    layout: {
                        tdAttrs: {style: 'padding: 5px;'},
                        type: 'table',
                        columns: 1,
                        pack: 'center'
                    },
                    items: 
                    [                           
                        gridDetalleRecursos
                    ]
            }                              
        ],
        buttons: [
            {
                text: '<i class="fa fa-window-close-o" aria-hidden="true"></i>&nbsp;Cerrar',
                handler: function() 
                {
                    winDetalleRecursos.close();
                    winDetalleRecursos.destroy();                        
                }
            }
        ]});

    var winDetalleRecursos = Ext.widget('window', {
        id: 'winDetalleRecursos',
        title: 'Detalle de Recursos contratado de <b>'+tipo+'</b>',
        layout: 'fit',
        resizable: true,
        modal: true,
        closable: true,
        width: 'auto',
        items: [formPanelDatosConsultarMV]
    });

    winDetalleRecursos.show();
}


function showAgregarRecursos(tipo,nombreMaquina)
{ 
    var array           = [];
    var boolEsDisco     = true;
    
    //Variable que indica cuando se cambia el recurso en el grid
    var boolEsEdicion   = false;    
    var boolEditarMv    = false;
    var idRecursoAnt    = 0;    
    var recursoAnt      = 0;
    var componente      = '';
    storeTipoRecurso    = [];
    arrayRecursosGuardados = [];
    
    if(!Ext.isEmpty(nombreMaquina) && nombreMaquina !== 'undefined')
    {
        boolEditarMv = true;
    }
    
    //Cargar la informacion de la maquina virtual ya guardada
    if(boolEditarMv)
    {
        $.each(arrayInformacion, function(key, value) 
        {            
            if(value.nombre === nombreMaquina)
            {
                if(tipo === 'DISCO')
                {
                    arrayRecursosGuardados = value.arrayRecursos.arrayDetalleDisco;
                }
                else if(tipo === 'PROCESADOR')
                {
                    arrayRecursosGuardados = value.arrayRecursos.arrayDetalleProcesador;
                }
                else if(tipo === 'LICENCIA')
                {
                    arrayRecursosGuardados = value.arrayRecursos.arrayDetalleLicencia;
                }
                else
                {
                    arrayRecursosGuardados = value.arrayRecursos.arrayDetalleMemoria;
                }

                return false;                         
            }
        });
    }    
    //Se cargan los tipo de recursos con sus respectivos valores para poder
    //seleccionarlos en la creacion o edicion de maquinas virtuales
    switch(tipo)
    {       
        case 'DISCO':
            array      = arrayRecursos.arrayDetalleDisco;
            componente = 'txtStorage';
            break;
        
        case 'MEMORIA RAM':
            array       = arrayRecursos.arrayDetalleMemoria;
            boolEsDisco = false;
            componente  = 'txtMemoria';
            break;
        
        case 'LICENCIA':
            array       = arrayRecursos.arrayDetalleLicencia;
            boolEsDisco = false;
            componente  = 'txtLicencia';
            break;
            
        default ://PROCESADOR
            array = arrayRecursos.arrayDetalleProcesador;
            boolEsDisco = false;
            componente  = 'txtProcesador';
            break;
    }
    
    //recursos segun el tipo ( se mostraran como opcion en el editor )
    storeTipoRecurso = getStoreCaracteristicas(array);
    rowEditingRecursos = Ext.create('Ext.grid.plugin.RowEditing', {
        saveBtnText: '<i class="fa fa-check-square"></i>',
        cancelBtnText: '<i class="fa fa-eraser"></i>',
        clicksToMoveEditor: 1,
        autoCancel: false,
        listeners: {
            canceledit: function(editor, e, eOpts) 
            {
                arrayRecursosConf = eliminarRegistroDeArray(arrayRecursosConf,e.record.data.idRecurso);
                arrayRecursoTmp   = eliminarRegistroDeArray(arrayRecursoTmp,e.record.data.idRecurso); 
                                
                arrayRecursoEliminados.push(e.record.data.idRecurso);
                
                 $.each(arrayResumenGeneralRecursos, function(i, item1)
                {
                    if(item1.idRecurso  === e.record.data.idRecurso)
                    {
                         item1.disponible  =  item1.disponible +  parseInt(e.record.data['asignar']);
                         item1.usado  = item1.usado - parseInt(e.record.data['asignar']);
                    }
                });  
                e.store.remove(e.record);
                if(!boolEsEdicion)
                {
                    //Se agrega los recursos configurados para una maquina virtual
                    Ext.getCmp("txtInfoRecursos").setValue(Ext.JSON.encode(arrayRecursoTmp));
                }

                var totalRecurso = 0;
                
                for (var i = 0; i < gridRecursos.getStore().getCount(); i++)
                {
                    var asignar   = parseInt(gridRecursos.getStore().getAt(i).data.asignar);

                    totalRecurso += asignar;
                }
                
                //Se muestra el valor total de recursos escogidos ( Gb o Cores )
                Ext.getCmp(componente).setValue(totalRecurso);
            },
            beforeedit:function(editor, e, eOpts) 
            {      
                idRecursoAnt = e.record.data.idRecurso;                
                recursoAnt   = e.record.data.asignar;
                if (recursoAnt == "")
                {
                    recursoAnt = 0;
                }
                if(!Ext.isEmpty(idRecursoAnt) && idRecursoAnt !== 0)
                {
                    boolEsEdicion = true;
                }                
            },
            afteredit: function(editor, e, eOpts) 
            {
                var intCountGridDetalle = Ext.getCmp('gridRecursos').getStore().getCount();
                var selectionModel      = Ext.getCmp('gridRecursos').getSelectionModel();
                selectionModel.select(0);
                
                var idRecurso = e.record.data.caracteristica;
                
                if (intCountGridDetalle > 0)
                {
                    if ( Ext.isEmpty(idRecurso))
                    {
                        Ext.Msg.alert('Error', 'Debe escoger los valores para asignar el recurso');
                        rowEditingRecursos.cancelEdit();
                        selectionModel.select(0);
                        rowEditingRecursos.startEdit(0, 0);
                        return false;
                    }
                    else
                    {
                        var boolContinuar = true;                        
                        //Validar que el recurso ya no exista configurado en la misma maquina virtual o en una nueva
                        $.each(arrayRecursoTmp, function(i, item)
                        {
                            if(parseInt(item.idRecurso) === parseInt(idRecurso))
                            {
                                Ext.Msg.alert('Alerta', 'Recurso de <b>'+item.caracteristica+'</b> ya fue utilizado en la Máquina');
                                boolContinuar = false;
                                return false;
                            }
                        });
                        
                        //Si paso validacion
                        if(boolContinuar)
                        {                                                      
                            //Generar nueva maquina virtual
                            //Si existe cambio de caracteristica
                            if(boolEsEdicion && idRecursoAnt !== 0)
                            {                       
                                if(Ext.isNumber(parseInt(e.record.data.idRecurso)) && 
                                   Ext.isNumber(parseInt(idRecursoAnt)))
                                {
                                     if(parseInt(e.record.data.idRecurso) !== parseInt(idRecursoAnt) ||
                                        parseInt(e.record.data.asignar) !== parseInt(recursoAnt))
                                     {                                                                                
                                         arrayRecursosConf = eliminarRegistroDeArray(arrayRecursosConf,idRecursoAnt);
                                         arrayRecursoTmp   = eliminarRegistroDeArray(arrayRecursoTmp,idRecursoAnt);
                                     }
                                }
                                if(parseInt(e.record.data.idRecurso) !== parseInt(idRecursoAnt)  ||
                                   (Ext.isNumeric(parseInt(e.record.data.caracteristica)) && 
                                   parseInt(e.record.data.caracteristica) !== parseInt(idRecursoAnt))
                                  )
                                {
                                    $.each(arrayResumenGeneralRecursos, function(i, item1)
                                    {
                                        if(item1.idRecurso === idRecursoAnt)
                                        {
                                            arrayResumenGeneralRecursos[i].disponible = parseInt(item1.disponible) + parseInt(e.record.data.asignar);
                                            arrayResumenGeneralRecursos[i].usado      = 0;
                                            e.record.set('disponible',item1.disponible);
                                            e.record.set('asignar',0);
                                        }
                                    });
                                }
                            }
                            
                            $.each(array, function(i, item)
                            {
                                if(parseInt(item.idRecurso) === parseInt(idRecurso) || 
                                   parseInt(item.idRecurso) === parseInt(e.record.data.idRecurso))
                                {                                    
                                    //Asignar el valor disponible calculado con cada redistribucion de recursos asignados por tipo
                                    var disponibleRecurso = 0;
                                    var asignar           = 0;
                                    
                                    $.each(arrayResumenGeneralRecursos, function(i, item1)
                                    {
                                        if(item1.idRecurso === item.idRecurso )
                                        {                        
                                            disponibleRecurso = parseInt(item1.disponible);
                                            return false;
                                        }
                                    });
                                    
                                    //Si no ha sido asignado
                                    if(!Ext.isEmpty(e.record.data.asignar))
                                    {
                                        asignar = e.record.data.asignar;
                                        
                                        if(!boolEditarMv)
                                        {
                                            if(parseInt(asignar) === 0)
                                            { 
                                                disponibleRecurso = parseInt(disponibleRecurso) + parseInt(recursoAnt);
                                            }
                                            else
                                            {
                                                disponibleRecurso = parseInt(disponibleRecurso) - parseInt(asignar);
                                            }
                                        }
                                        else
                                        {
                                            if(parseInt(asignar) === 0)
                                            {                        
                                                disponibleRecurso = parseInt(disponibleRecurso) + parseInt(recursoAnt);
                                            }
                                            else
                                            { 
                                                disponibleRecurso = (parseInt(disponibleRecurso) + parseInt(recursoAnt)) - parseInt(asignar);
                                            }
                                        }
                                        
                                        if(disponibleRecurso < 0)
                                        {
                                            Ext.Msg.alert('Alerta', 'No puede seleccionar más recursos del ya contratado.');
                                            boolContinuar = false;
                                            
                                            //Si los recursos son nuevos se borra el registro para volver a seleccionarlos
                                            if(!boolEditarMv)
                                            {
                                                e.store.remove(e.record);
                                            }
                                            else//si ya existeb los permanece como estaban para continuar el proceso
                                            {
                                                //Poner el recurso asignado anterior en caso de excepcion
                                                e.record.set("asignar", parseInt(recursoAnt));
                                            }
                                            
                                            return false;
                                        }
                                        //Si no existe cambio de asignacion no agrega ningun registro nuevo
                                        if(parseInt(e.record.data.asignar) === parseInt(recursoAnt))
                                        {
                                            //No existen cambios de valor del recurso
                                            boolContinuar = false;
                                        }
                                    }                                                                        
                                    
                                    if(boolContinuar)
                                    {
                                        if(boolEditarMv)
                                        {
                                            e.record.set("idDetalle", e.record.data.idDetalle);
                                        }
                                        else
                                        {
                                            e.record.set("idDetalle", item.idDetalle);
                                        }
                                        
                                        e.record.set("idRecurso",          item.idRecurso);
                                        e.record.set("caracteristica",     item.nombreRecurso);
                                        e.record.set("valor",              item.valor);
                                        e.record.set("disponible",         parseInt(disponibleRecurso));
                                        e.record.set("asignar",            parseInt(asignar));
                                        e.record.set("datastore",          item.valorCaracteristica);
                                        
                                        var json                = {};
                                        json['tipo']            = tipo;
                                        json['idRecurso']       = item.idRecurso;
                                        json['caracteristica']  = item.nombreRecurso;
                                        json['valor']           = item.valor;
                                        json['disponible']      = parseInt(disponibleRecurso);
                                        json['asignar']         = parseInt(asignar);
                                        json['datastore']       = item.valorCaracteristica;   
                                        json['idDetalle']       = 0;
                                        json['maquina']         = nombreMaquina;
                                        
                                        if(boolEditarMv)
                                        {
                                            json['idDetalle']  = e.record.data.idDetalle;
                                        }
                                        
                                        arrayRecursosConf.push(json);
                                        arrayRecursoTmp.push(json);
                                        
                                        var boolRecursoNoExiste = true
                                        $.each(arrayResumenGeneralRecursos, function(i, item1)
                                        {
                                            if(item1.idRecurso === item.idRecurso )
                                            {
                                                arrayResumenGeneralRecursos[i].disponible = parseInt(disponibleRecurso);
                                             arrayResumenGeneralRecursos[i].usado      = parseInt(asignar);
                                            }
                                        });
                                        
                                        
                                        return false;
                                    }
                                }                                
                            });
                            
                            if(boolEditarMv)
                            {
                                if(tipo === 'DISCO')
                                {
                                    boolCambioDisco = true;
                                }
                                else if(tipo === 'PROCESADOR')
                                {
                                    boolCambioProcesador = true;
                                }
                                else if(tipo === 'LICENCIA')
                                {
                                    boolCambioLicencia = true;
                                }
                                else
                                {
                                    boolCambioMemoria = true;
                                }
                            }
                        }
                        else
                        {
                            if(!boolEsEdicion)
                            {
                                e.store.remove(e.record);
                            }
                            else
                            {
                                var value = storeTipoRecurso.findRecord("id", idRecursoAnt).get('value');
                                e.record.set('idRecurso',idRecurso);
                                e.record.set('caracteristica',value);  
                            }
                        }
                    }
                }
            }
        }
    });
    
    Ext.define('recursosModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idRecurso',      type: 'integer'},
            {name: 'caracteristica', type: 'string'},
            {name: 'valor',          type: 'string'},
            {name: 'disponible',     type: 'string'},
            {name: 'asignar',        type: 'string'},
            {name: 'datastore',      type: 'string'},
            {name: 'idDetalle',      type: 'integer'}
        ]
    });    
      
    storeRecursosCaracteristicas = Ext.create('Ext.data.Store', {
        pageSize: 5,
        autoDestroy: true,
        model: 'recursosModel',
        proxy: {
            type: 'memory'
        }
    });
    
    var toolbar = Ext.create('Ext.toolbar.Toolbar', {
		dock: 'top',
		align: '->',
        id:'tlbAgregar',
		items   : 
		[ 
                    {
                        iconCls: 'icon_add',
                        text: 'Agregar Recurso',
                        id: 'btnAgregarRecurso',
                        scope: this,
                        handler: function()
                        {                   
                            rowEditingRecursos.cancelEdit();

                            var recordParamDet = Ext.create('recursosModel', {
                                    idRecurso       : '',
                                    caracteristica  : '',
                                    valor           : '',
                                    disponible      : '',
                                    asignar         : '',
                                    datastore       : '',
                                    idDetalle       : ''
                                });

                            storeRecursosCaracteristicas.insert(0, recordParamDet);
                            rowEditingRecursos.startEdit(0, 0);
                        }
                    }
		]
	});
    
    var infoRecursos = Ext.getCmp("txtInfoRecursos").getValue();
    
    //Si ya existe
    if(!Ext.isEmpty(infoRecursos))
    {        
        var arrayRecursosConfigurados = Ext.JSON.decode(infoRecursos);
        storeRecursosCaracteristicas.clearData();
        
        $.each(arrayRecursosConfigurados, function(i, item)
        {
            if(item.tipo === tipo)
            {
                rowEditingRecursos.cancelEdit();
                
                var disponibleRecurso = 0;
                var asignar           = 0;
                
                //Obtener la cantidadad de recurso disponible por tipo y caracteristica
                $.each(arrayResumenGeneralRecursos, function(i, item1)
                {
                    if(item1.idRecurso === item.idRecurso )
                    {
                        disponibleRecurso = parseInt(item1.disponible);
                        asignar           = item1.usado;
                    }
                });
                
                var recordParamDet = Ext.create('recursosModel', {
                        idRecurso       : item.idRecurso,
                        caracteristica  : item.caracteristica,
                        valor           : item.valor,
                        disponible      : parseInt(disponibleRecurso),
                        asignar         : asignar,
                        datastore       : !Ext.isEmpty(item.datastore)?item.datastore:'',
                        idDetalle       : item.idDetalle
                    });
                    
                storeRecursosCaracteristicas.insert(0, recordParamDet);
            }
        });
    }
    
    gridRecursos = Ext.create('Ext.grid.Panel',{
        width: 600,        
        collapsible: false,        
        layout:'fit',
        dockedItems: [ toolbar ],
        store: storeRecursosCaracteristicas,
        plugins: [rowEditingRecursos],
        id: 'gridRecursos',
        height: 150,        
        columns: 
        [
            {
                id: 'idDetalle',
                dataIndex: 'idDetalle',
                hidden: true,
                hideable: false
            },
            {
                id: 'idRecurso',
                dataIndex: 'idRecurso',
                hidden: true,
                hideable: false
            },
            {
                header: "<b>Recurso</b>",
                dataIndex:'caracteristica',
                width: 250,
                align: 'left',                
                editor: new Ext.form.field.ComboBox({
                    typeAhead: true,
                    id: 'cmbCaracteristica',
                    name: 'cmbCaracteristica',
                    valueField: 'id',
                    displayField: 'value',
                    store: storeTipoRecurso,
                    editable: false
                }),
                renderer: function(value, id,id2)
                { 
                    if(!Ext.isEmpty(value))
                    {
                        if(storeTipoRecurso.findRecord("id", value))
                        {
                            return storeTipoRecurso.findRecord("id", value).get('value');
                            
                        }

                        if(storeTipoRecurso.findRecord("value", value))
                        {
                            return value;
                        }
                        
                        if(storeTipoRecurso.findRecord("id", id2.data.idRecurso))
                        {
                            return storeTipoRecurso.findRecord("id", id2.data.idRecurso).data.value;
                        }
                    }
                    else
                    {
                        return value;
                    }
                }
            },
            {
                header: "<b>Pool Total</b>",
                width: 80,
                dataIndex:'valor',
                id:'valor',
                align:'center',
                renderer: function(value)
                {          
                    return '<b style="color:green;">'+value+'</b>';
                }
            },
            {
                header: "<b>Disponible</b>",
                width: 80,
                dataIndex:'disponible',
                id:'disponible',
                align:'center',
                renderer: function(value)
                {  
                    return '<b style="color:blue;">'+value+'</b>';
                }
            },
            {
                header: "<b>A Asignar</b>",
                width: 80,
                dataIndex:'asignar',
                id:'asignar',
                align:'center',
                editor: new Ext.form.field.Number({
                    id: 'txtAsignacion',
                    name: 'txtAsignacion',
                    hideTrigger:true,
                    allowNegative: false,
                    minValue: 0
                }),
                renderer: function(value)
                {                    
                    return '<b>'+value+'</b>';
                }
            },
            {
                header: "<b>Datastore</b>",
                dataIndex:'datastore',      
                id:'datastore',
                width: 100,
                align:'center',
                hidden:!boolEsDisco
            }
        ]
    });

    var formPanelEditar = Ext.create('Ext.form.Panel', {
        buttonAlign: 'center',
        width:'auto',
        height:'auto',
        layout: {
            type: 'table',
            columns: 2
        },
        frame: true,
        items: 
        [
            {
                xtype: 'panel',
                width:600,
                height:150,
                defaults: { 
                    height: 150
                },
                items: 
                [
                    gridRecursos
                ]
            }
        ],
        buttons: [
            {
                text: '<i class="fa fa-plus-square" aria-hidden="true"></i>&nbsp;<b>Agregar Recurso</b>',
                handler: function() 
                {
                    if(gridRecursos.getStore().getCount()>0)
                    {
                        var totalRecurso  = 0;
                        var boolContinuar = true;
                        
                        for (var i = 0; i < gridRecursos.getStore().getCount(); i++)
                        {
                            if(parseInt(gridRecursos.getStore().getAt(i).data.asignar) === 0)
                            {
                                boolContinuar = false;//si existe alguna asignacion de recursos en 0 no continua con el flujo
                            }
                            
                            if(!boolContinuar)
                            {
                                break;
                            }
                        }
                        
                        if(boolContinuar)
                        {
                            //Agrear recursos a la caracteristica
                            for (var i = 0; i < gridRecursos.getStore().getCount(); i++)
                            {
                                var asignar   = parseInt(gridRecursos.getStore().getAt(i).data.asignar);
                                
                                totalRecurso += asignar;
                            }
                            
                            //Se agrega los recursos configurados para una maquina virtual
                            $.each(arrayRecursoTmp, function(i, item)
                            {
                                if(item.tipo === 'LICENCIA' && ((item.caracteristica.substring(0,3)) === '<b>'))
                                {   
                                        item.caracteristica =   item.caracteristica.substring(item.caracteristica.indexOf("</b> ")+5, item.caracteristica.length);
                                }
                            });
                            Ext.getCmp("txtInfoRecursos").setValue(Ext.JSON.encode(arrayRecursoTmp));
                            
                            //Se muestra el valor total de recursos escogidos ( Gb o Cores )
                            Ext.getCmp(componente).setValue(totalRecurso);
                            boolEsEdicion = false;

                            storeTipoRecurso = [];//Se limpia el store para que pueda ser cargado por otro tipo de recurso
                            winAgregarRecursos.close();
                            winAgregarRecursos.destroy();        
                        }
                        else
                        {
                            Ext.Msg.alert('Alerta', 'Debe asignar valores mayores a 0 de los recursos \n\
                                                     escogidos <b>ó</b> ya no existen recursos disponibles en el Pool para asignar');
                        }
                    }
                    else
                    {
                        Ext.Msg.alert('Alerta', 'Debe ser escogido al menos un recurso para continuar');
                    }
                }
            },
            {
                text: '<b>Cerrar</b>',
                handler: function() {                    
                    winAgregarRecursos.close();
                    winAgregarRecursos.destroy();                    
                    storeTipoRecurso = [];//Se limpia el store para que pueda ser cargado por otro tipo de recurso                                        
                }
            }
        ]});

    var winAgregarRecursos = Ext.widget('window', {
            id: 'winAgregarRecursos',
            title: 'Administrar Recursos',
            layout: 'fit',
            resizable: true,
            modal: true,
            closable: true,
            width: 'auto',
            items: [formPanelEditar]
        });
        
    if(boolEditarMv)
    {        
        switch(tipo)
        {
            case 'DISCO':
                if(boolCambioDisco)cargarInformacionRecursosActualizada(tipo);
                else cargarInformacionRecursosGuardada();                
                break;

            case 'MEMORIA RAM':
                if(boolCambioMemoria)cargarInformacionRecursosActualizada(tipo);
                else cargarInformacionRecursosGuardada(); 
                break;
                
            case 'LICENCIA':
                if(boolCambioLicencia)cargarInformacionRecursosActualizada('LICENCIA');
                else cargarInformacionRecursosGuardada(); 
                break;

            default ://PROCESADOR
                if(boolCambioProcesador)cargarInformacionRecursosActualizada(tipo);
                else cargarInformacionRecursosGuardada(); 
                break;
        }
    }

    winAgregarRecursos.show();
}

//Funcion qe cargara la informacion de recursos guardados para una maquina virtual, cuando el usuario realiza algun cambio
//ya no carga esta informacion y carga las combinaciones que decida escoger en la edicion
function cargarInformacionRecursosGuardada()
{    
    storeRecursosCaracteristicas.clearData();
    $.each(arrayRecursosGuardados, function(i, item)
    {     
        var disponibilidad = 0;
        
        $.each(arrayResumenGeneralRecursos, function(i, itemRGR)
        {   
            if(itemRGR.idRecurso === item.idRecurso )
            {                
                disponibilidad = itemRGR.disponible;
            }
        });
        
        var json                = {};
        json['tipo']            = item.tipo;
        json['idRecurso']       = item.idRecurso;
        json['caracteristica']  = item.nombreRecurso;
        json['valor']           = item.valor;
        json['disponible']      = disponibilidad;
        json['asignar']         = parseInt(item.usado);
        json['datastore']       = item.valorCaracteristica;   
        json['idDetalle']       = item.idDetalle;
        if(arrayRecursoTmp.length === 0)
        {           
            arrayRecursoTmp.push(json);
        }
        else
        {
            var boolRecursoNoExiste = true;
            //Validar que no exista para agregar uno diferente
            $.each(arrayRecursoTmp, function(i, item1)
            {                
                if(item1.idRecurso === item.idRecurso)
                {
                    boolRecursoNoExiste = false;
                    return false;
                }
            });   
            
            if(boolRecursoNoExiste)
            {
                arrayRecursoTmp.push(json);
            }
        }
        
        rowEditingRecursos.cancelEdit();

        var recordParamDet = Ext.create('recursosModel', {
                idRecurso       : item.idRecurso,
                caracteristica  : item.nombreRecurso,
                valor           : item.valor,
                disponible      : parseInt(disponibilidad),
                asignar         : item.usado,
                datastore       : item.valorCaracteristica,
                idDetalle       : item.idDetalle
            });
        storeRecursosCaracteristicas.insert(0, recordParamDet);
    });   
}

//Funcion que sirve para cargar el json de recursos que esta siendo actualizada en la edicion
//cargara las nuevas selecciones en caso que el usuario decida realizar alguna
function cargarInformacionRecursosActualizada(tipo)
{
    storeRecursosCaracteristicas.clearData();    
    $.each(arrayRecursoTmp, function(i, item)
    {
        if(tipo === item.tipo)
        {            
            rowEditingRecursos.cancelEdit();
            
            var recordParamDet = Ext.create('recursosModel', {
                    idRecurso       : item.idRecurso,
                    caracteristica  : item.caracteristica,
                    valor           : item.valor,
                    disponible      : item.disponible,
                    asignar         : item.asignar,
                    datastore       : item.datastore,
                    idDetalle       : item.idDetalle
                });
            storeRecursosCaracteristicas.insert(0, recordParamDet);
        }
    });
}
function getAgregarEditarWindow(tipo, $item)
{   
    var boolEsNuevo      = true;
    var jsonInfo         = {};
    arrayRecursoTmp      = [];
    
    if(tipo === 'editar')
    {
        boolEsNuevo = false;
        $item       = $item.parents('li');
        
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
        
        var totalDisco      = 0;
        var totalProcesador = 0;
        var totalMemoria    = 0;
        var totalLicencia   = 0;
        
        //calcular recursos configurados
        $.each(jsonInfo.arrayRecursos.arrayDetalleDisco, function(i, item){            
            totalDisco = totalDisco + parseInt(item.usado);
        });
        
        $.each(jsonInfo.arrayRecursos.arrayDetalleProcesador, function(i, item){
            totalProcesador = totalProcesador + parseInt(item.usado);
        });
        
        $.each(jsonInfo.arrayRecursos.arrayDetalleMemoria, function(i, item){
            totalMemoria = totalMemoria + parseInt(item.usado);
        });

        $.each(jsonInfo.arrayRecursos.arrayDetalleLicencia, function(i, item){
            totalLicencia = totalLicencia + parseInt(item.usado);
        });
    }

    var iniHtmlCamposRequeridosAdd = '<p style="text-align: right; color:red; font-weight: bold; border: 0 !important;">\n\
                                           <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>&nbsp;* Campos requeridos</p>';
    var CamposRequeridosAdd = Ext.create('Ext.Component', {
        html: iniHtmlCamposRequeridosAdd,
        padding: 1,
        layout: 'anchor',
        style: {color: 'red', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
    });
    
    var htmlDivisor = Ext.create('Ext.Component', {
        html: '<div class="secHead"><label style="text-align:left;">\n\
               <b><i class="fa fa-tags" aria-hidden="true"></i>&nbsp;</b><label>Administración de Recursos</label></div>',
        padding: 1,
        layout: 'anchor'
    });
        
    var storeSO = new Ext.data.Store({
        pageSize: 10,
        total: 'total',
        autoLoad:true,
        proxy: {
            timeout: 3000000,
            type: 'ajax',
            url: urlGetInformacionGeneralHosting,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                idServicio        : idServicio,
                tipoInformacion   : 'SISTEMA-OPERATIVO'
            }
        },
        fields:
            [
                {name: 'idServicio',  mapping: 'idServicio'},
                {name: 'descripcion', mapping: 'descripcion'}
            ]
    });
    
    var formCrearMV = Ext.create('Ext.form.Panel', {
            buttonAlign: 'center',
            id:'panelAgregarMv',
            BodyPadding: 10,
            width: 750,
            height: 400,
            bodyStyle: "background: white; padding: 5px; border: 0px none;",
            frame: true,
            items:
                [
                    CamposRequeridosAdd,
                    {
                        xtype: 'fieldset',
                        id   : 'resumenCrearMv',
                        title: 'Datos Adicionales para creación de Máquina Virtual',
                        layout: {
                            tdAttrs: {style: 'padding: 5px;'},
                            type: 'table',
                            columns: 5,
                            pack: 'center'
                        },
                        items: [                            
                            {
                                xtype:'hidden',                                
                                name: 'txtIdMaquinaVirtual',
                                id:   'txtIdMaquinaVirtual',
                                value:boolEsNuevo?'':jsonInfo.idMaquina
                            },
                            {width: '10%', border: false},
                            {width: '10%', border: false},
                            {width: '10%', border: false},
                            {width: '10%', border: false},
                            //----------------------------------------------
                            {width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                fieldLabel: '<b>* Nombre Máquina</b>',
                                name: 'txtNombreMaquina',
                                id: 'txtNombreMaquina',
                                width:300,
                                value: boolEsNuevo?'':jsonInfo.nombre,
                                readOnly: false
                            },
                            {width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                fieldLabel: '<b>* Carpeta</b>',
                                name: 'txtNombreCarpeta',
                                id: 'txtNombreCarpeta',
                                value: boolEsNuevo?login:jsonInfo.carpeta,
                                emptyText:login,
                                width:300,
                                readOnly: false
                            },                            
                            {width: '10%', border: false},
                            //------------------------------------------------
                            {width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                fieldLabel: '<b>* Tarjeta de Red</b>',
                                name: 'txtTarjetaRed',
                                id: 'txtTarjetaRed',
                                value: boolEsNuevo?'':jsonInfo.tarjeta,
                                width:300,
                                readOnly: false
                            },
                            {width: '10%', border: false},    
                            {width: '10%', border: false},                                
                            {width: '10%', border: false},
                            //------------------------------------------------      
                            {width: '10%', border: false},
                            htmlDivisor,                                        
                            {width: '10%', border: false},
                            {width: '10%', border: false},
                            {width: '10%', border: false},
                            //---------------------------------------------
                            {width: '10%', border: false},
                            {
                                xtype: 'numberfield',
                                fieldLabel: '<b>Disco</b>',
                                name: 'txtStorage',
                                id: 'txtStorage',
                                fieldStyle: 'color:green;font-weight:bold;',
                                allowDecimals:   false,
                                allowNegative:   false,
                                hideTrigger:true,
                                width:300,
                                value:boolEsNuevo?'':totalDisco,
                                readOnly: true
                            },
                            imgAgregarRecursos('DISCO',jsonInfo.nombre),
                            {width: '10%', border: false},
                            {width: '10%', border: false},
                            //--------------------------------------------                            
                            {width: '10%', border: false},                            
                            {
                                xtype: 'numberfield',
                                fieldLabel: '<b>Memoria</b>',
                                name: 'txtMemoria',
                                id: 'txtMemoria',
                                fieldStyle: 'color:green;font-weight:bold;',
                                allowDecimals:   false,
                                allowNegative:   false,
                                hideTrigger:true,
                                value:boolEsNuevo?'':totalMemoria,
                                width:300,
                                readOnly: true
                            },
                            imgAgregarRecursos('MEMORIA RAM',jsonInfo.nombre),
                            {width: '10%', border: false},
                            {width: '10%', border: false},
                            //----------------------------------------------
                            {width: '10%', border: false},
                            {
                                xtype: 'numberfield',
                                fieldLabel: '<b>Procesadores</b>',
                                name: 'txtProcesador',
                                id: 'txtProcesador',                                
                                allowDecimals:   false,
                                allowNegative:   false,
                                value:boolEsNuevo?'':totalProcesador,
                                hideTrigger:true,
                                fieldStyle: 'color:green;font-weight:bold;',
                                width:300,
                                readOnly: true
                            },
                            imgAgregarRecursos('PROCESADOR',jsonInfo.nombre),
                            {width: '10%', border: false},
                            {width: '10%', border: false}, 
                           //---------------------------------------------------
                           {width: '10%', border: false},
                            {
                                xtype: 'numberfield',
                                fieldLabel: '<b>Licencia</b>',
                                name: 'txtLicencia',
                                id: 'txtLicencia',                           
                                allowDecimals:   false,
                                allowNegative:   false,
                                value:boolEsNuevo?'':totalLicencia,
                                hideTrigger:true,
                                fieldStyle: 'color:green;font-weight:bold;',
                                width:300,
                                readOnly: true
                            },
                            imgAgregarRecursos('LICENCIA',jsonInfo.nombre),
                            {width: '10%', border: false},
                            {width: '10%', border: false}, 
                           //---------------------------------------------------
                            {
                                xtype:'hidden',                                
                                name: 'txtInfoRecursos',
                                id:   'txtInfoRecursos',
                                value:''
                            },
                            {width: '10%', border: false},
                            {width: '10%', border: false},
                            {width: '10%', border: false},
                            {width: '10%', border: false},
                        ]
                }                              
            ],
            buttons: [
                {
                    text: '<i class="fa fa-plus-square" aria-hidden="true"></i>&nbsp;'+(boolEsNuevo?'Agregar':'Editar'),
                    handler: function() 
                    {
                        var infoRecursos = Ext.getCmp("txtInfoRecursos").getValue();
                        //Verificamos si hay recursos configurados para la  MV
                        if(!Ext.isEmpty(infoRecursos))
                        {
                            var arrayLicenciaCant   =   [];
                            var boolEsElPrimero     =   true;
                            var boolSeDebeIngresar  =   false;
                            var arrayRecursosConfigurados = Ext.JSON.decode(infoRecursos);
                            
                            
                            $.each(arrayRecursosConfigurados, function(i, item)
                            {
                                boolSeDebeIngresar  =   false;
                                var arrayNombreLic = [] ;
                                if(item.tipo === 'LICENCIA' || item.tipo === 'SISTEMA_OPERATIVO')
                                {   
                                        if(boolEsElPrimero)
                                        {
                                            arrayNombreLic          =   item.caracteristica.split("@", 2);
                                            var json                =   {};
                                            json['tipo']            =   arrayNombreLic[0]; 
                                            json['nombreLic']       =   arrayNombreLic[1];
                                            json['cantidad']        =   1;
                                            arrayLicenciaCant.push(json);
                                            boolEsElPrimero         =   false;
                                        }
                                        else
                                        {
                                            var jsonLicNueva        =   {};
                                            $.each(arrayLicenciaCant, function(i2, item2)
                                            {
                                                arrayNombreLic       =   item.caracteristica.split("@", 2);
                                                if(item2.nombreLic == arrayNombreLic[1])
                                                {
                                                    item2.cantidad   =   item2.cantidad+1;
                                                    boolSeDebeIngresar  =   false;
                                                    return false;
                                                }
                                                else
                                                {
                                                    jsonLicNueva        =   {};
                                                    jsonLicNueva['tipo']        =   arrayNombreLic[0]; 
                                                    jsonLicNueva['nombreLic']   =   arrayNombreLic[1];
                                                    jsonLicNueva['cantidad']    =   1;
                                                    boolSeDebeIngresar  =   true;
                                                }
                                            });
                                            if(boolSeDebeIngresar){
                                                arrayLicenciaCant.push(jsonLicNueva);
                                            }
                                        }

                                }
                            });
                            //Validación de licencia
                            var srtMensajeAlerta = "";
                            
                            $.each(arrayLicenciaCant, function(i, item)
                            {
                                if((item.tipo.substring(0,3)) === '<b>')
                                {
                                    arrayValidaLicencias =    arrayParametrosLicencias[item.tipo.substring(item.tipo.indexOf("</b> ")+5
                                                            , item.tipo.length)];
                                }else{
                                    arrayValidaLicencias    =   arrayParametrosLicencias[item.tipo];
                                }
                                var jsonLicencia         = {};
                                jsonLicencia = validarLicencia(parseInt(Ext.getCmp("txtProcesador").getValue()), item.nombreLic);
                                if(item.cantidad < jsonLicencia['numeroLicencia'] )
                                {
                                    srtMensajeAlerta = srtMensajeAlerta + 'Licencia: ' + item.nombreLic + ' <b>requiere '+ jsonLicencia['numeroLicencia']  + ' licencias</b> \n';
                                }
                                    //Cuando se necesita Small Instance
                                
                                if(jsonLicencia['esRHLargeInstance'] && numCore < jsonLicencia['numeroCoreReq'])
                                {
                                      srtMensajeAlerta = srtMensajeAlerta + 'Requiere Licencia  Small Instance \n';
                                }else if (jsonLicencia['esRHSmallInstance'] && numCore > jsonLicencia['numeroCoreReq'])
                                {
                                   srtMensajeAlerta = srtMensajeAlerta + 'Requiere Licencia  Large Instance \n'; 
                                }

                            });
                            if(srtMensajeAlerta !== ""){
                                Ext.Msg.alert('Alerta', srtMensajeAlerta, function(btn) {
                                    if (btn == 'ok') 
                                    {    
                                        return true;
                                    }
                                });
                            }
                        }
                        
                        if(boolEsNuevo)
                        {
                            //Crear Maquina Virtual
                            var boolrespuesta = agregarBloqueHtmlMaquinaVirtual();

                            if(boolrespuesta)
                            {
                                winCrearMV.close();
                                winCrearMV.destroy();
                            }
                        }
                        else
                        {
                            var json = {};
                            
                            json['nombre']     = Ext.getCmp("txtNombreMaquina").getValue();;
                            json['storage']    = Ext.getCmp("txtStorage").getValue();;
                            json['memoria']    = Ext.getCmp("txtMemoria").getValue();
                            json['procesador'] = Ext.getCmp("txtProcesador").getValue();
                            json['licencia']   = Ext.getCmp("txtLicencia").getValue();
                            json['carpeta']    = Ext.getCmp("txtNombreCarpeta").getValue();
                            json['tarjeta']    = Ext.getCmp("txtTarjetaRed").getValue();                            
                            json['idMaquina']  = jsonInfo.idMaquina;                            
                            json['soNombre']   = Ext.getCmp("txtLicencia").getValue();
                            
                            $.each(arrayRecursoTmp, function(i,item)
                            {
                                if(item.tipo === 'LICENCIA')
                                {    
                                    item.tipo='SISTEMA_OPERATIVO'; 
                                }
                            });
                            json['recursos']   = Ext.JSON.encode(arrayRecursoTmp);
                            
                            //Llamado ajax para editar maquinas virtuales
                            ajaxEditarEliminarMaquinaVirtual('editar',json,jsonInfo,$item);
                        }
                        
                    }
                },
                {
                    text: '<i class="fa fa-window-close-o" aria-hidden="true"></i>&nbsp;Cerrar',
                    handler: function() 
                    {                       
                        winCrearMV.close();
                        winCrearMV.destroy();
                    }
                }
            ]});

    var winCrearMV = Ext.widget('window', {
        id: 'winCrearMV',
        title: boolEsNuevo?'Creación':'Edición'+' de Máquina Virtual',
        layout: 'fit',
        resizable: true,
        modal: true,
        closable: true,
        width: 'auto',
        items: [formCrearMV]
    });
    
    winCrearMV.show();       
}


function eliminarRegistroDeArray(array, registro)
{
    array = array.filter(function(elem)
    {
        return parseInt(elem.idRecurso) !== parseInt(registro);
    });
    
    return array;
}

function eliminarRecursosDeMaquinaVirtual(json, accion)
{
    if(accion === 'nuevo')
    {
        var arrayRecursos = Ext.JSON.decode(json.arrayRecursos);
        
        $.each(arrayRecursos, function(i,item)
        {
            $.each(arrayResumenGeneralRecursos, function(i, item1)
                {
                    if(item1.idRecurso  === item.idRecurso)
                    {
                         item1.disponible  =  parseInt(item1.disponible) + parseInt(item.asignar);
                    }
                });
            arrayRecursosConf = eliminarRegistroDeArray(arrayRecursosConf,item.idRecurso);
        });
    }
    else 
    {
        var arrayRecursosDisco      = json.arrayRecursos.arrayDetalleDisco;
        var arrayRecursosMemoria    = json.arrayRecursos.arrayDetalleMemoria;
        var arrayRecursosProcesador = json.arrayRecursos.arrayDetalleProcesador;
        var arrayRecursosLicencia   = json.arrayRecursos.arrayDetalleLicencia;

        $.each(arrayRecursosDisco, function ( i,item)
        {
            $.each(arrayResumenGeneralRecursos, function(i, item1)
                {
                    if(item1.idRecurso  === item.idRecurso)
                    {
                         item1.disponible  =  parseInt(item1.disponible) + parseInt(item.asignar);
                    }
                });
            arrayRecursosConf = eliminarRegistroDeArray(arrayRecursosConf,item.idRecurso);
        });

        $.each(arrayRecursosMemoria, function ( i,item)
        {
            $.each(arrayResumenGeneralRecursos, function(i, item1)
                {
                    if(item1.idRecurso  === item.idRecurso)
                    {
                         item1.disponible  =  parseInt(item1.disponible) + parseInt(item.asignar);
                    }
                });
            arrayRecursosConf = eliminarRegistroDeArray(arrayRecursosConf,item.idRecurso);
        });

        $.each(arrayRecursosProcesador, function ( i,item)
        {
            $.each(arrayResumenGeneralRecursos, function(i, item1)
                {
                    if(item1.idRecurso  === item.idRecurso)
                    {
                         item1.disponible  =  parseInt(item1.disponible) + parseInt(item.asignar);
                    }
                });
            arrayRecursosConf = eliminarRegistroDeArray(arrayRecursosConf,item.idRecurso);
        });
        
        $.each(arrayRecursosLicencia, function ( i,item)
        {
            $.each(arrayResumenGeneralRecursos, function(i, item1)
                {
                    if(item1.idRecurso  === item.idRecurso)
                    {
                         item1.disponible  =  parseInt(item1.disponible) + parseInt(item.usado);
                    }
                });
            arrayRecursosConf = eliminarRegistroDeArray(arrayRecursosConf,item.idRecurso);
        });
    }
}

function getStoreCaracteristicas(arrayOriginal)
{
    var array = [];
    
    $.each(arrayOriginal, function(i, item) 
    {
        var json      = {};
        json['id']    = item.idRecurso;
        json['value'] = item.nombreRecurso;
        array.push(json);        
    });
    
    var store = new Ext.data.Store({
        fields: ['id','value'],
        data: array
    });
    return store;
}

function calcularTotalRecursosMaquinasVirtuales(array)
{
    var totalRecurso = 0;
    
    $.each(array, function(i , item)
    {
        totalRecurso = parseInt(totalRecurso) + parseInt(item.usado);
        
        var json                = {};
        json['tipo']            = item.tipo;
        json['idRecurso']       = item.idRecurso;
        json['caracteristica']  = item.nombreRecurso;
        json['valor']           = item.valor;
        json['asignar']         = item.usado;
        json['datastore']       = item.valorCaracteristica;   
        json['idDetalle']       = item.idDetalle;
        
        //Recalcular la cantidad de recursos en funcion de lo asignado
        var disponibilidad = parseInt(item.valor);
        

        $.each(arrayResumenGeneralRecursos, function(i, item1)
        {
            if(item1.idRecurso === item.idRecurso )
            {   
                if(parseInt(item1.disponible) === parseInt(item1.total))
                {
                    disponibilidad = disponibilidad - parseInt(item.usado);
                }
                else
                {
                    disponibilidad = parseInt(item1.disponible) - parseInt(item.usado);
                }
            }
        });

        $.each(arrayResumenGeneralRecursos, function(i, item1)
        {
            if(item1.idRecurso === item.idRecurso )
            {
                arrayResumenGeneralRecursos[i].disponible = parseInt(disponibilidad);
            }
        });
        
        json['disponible']       = parseInt(disponibilidad);


        //Cargar el array de Recursos configurados
        arrayRecursosConf.push(json);
    });      
    
    return totalRecurso;
}

function inicializarVariablesGlobales()
{
    boolCambioDisco      = false;
    boolCambioMemoria    = false;
    boolCambioProcesador = false;
    boolCambioLicencia   = false;
    arrayRecursoTmp      = [];
    arrayRecursosConf    = [];
}

function limpiarContenedorMaquinasVirtuales()
{
    var arrayNombresExistentes = [];//nombres de mv que son existentes
                        
    $.each(arrayInformacion, function(key, value) 
    {
        if(value.idMaquina !== 0)
        {
             storageTotal    = storageTotal    + parseInt(value.storage);
             memoriaTotal    = memoriaTotal    + parseInt(value.memoria);
             procesadorTotal = procesadorTotal + parseInt(value.procesador);
             licenciaTotal   = licenciaTotal   + parseInt(value.licencia);

             arrayNombresExistentes.push(value.nombre);
        }                            
    }); 

    if(arrayNombresExistentes.length > 0)
    {
        var content = $("#contenetMV");
        $.each(arrayNombresExistentes, function(key, value) 
        {                                
            content.find("h5").each(function()
            {
                var texto = $(this).text();                                
                if(value === texto)
                {
                    $item = $(this).parents('li');
                    $item.remove();
                }
            });
        });
    }

    arrayInformacion = arrayInformacion.filter(function(elem) 
    {                            
        return elem.idMaquina === 0;
    });  
}
