/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var valorAsignacion   = "empleado";   
var cuadrillaAsignada = "S";
var seleccionaHal     = false;
var nIntentos         = 0;
var tipoHal;
var isFirstLoad = true;
function obtenerTareas()
{
  var array = new Object();
  array['total'] =  gridTareas.getStore().getCount();
  array['tareas'] = new Array();
  var array_data = new Array();
  for(var i=0; i < gridTareas.getStore().getCount(); i++)
  {
  	array_data.push(gridTareas.getStore().getAt(i).data);
  }
  array['tareas'] = array_data;    
  
  return Ext.JSON.encode(array);
}

function obtenerMateriales()
{
  var array = new Object();
  array['total'] =  gridMaterialTareas.getStore().getCount();
  array['materiales'] = new Array();
  var array_data = new Array();
  for(var i=0; i < gridMaterialTareas.getStore().getCount(); i++)
  {
  	array_data.push(gridMaterialTareas.getStore().getAt(i).data);
  }
  array['materiales'] = array_data;
  return Ext.JSON.encode(array);
}

function validarTareas()
{
	//RONALD .------ VALIDAR AQUI QUE SE HAYA ESCOGIDO UN HIPOTESIS ANTERIOR... ANTES DE CREAR OTRO..
	var storeValida = Ext.getCmp("gridTareas").getStore();
	
	if(storeValida.getCount() > 0)
	{
		var boolSigue_vacio = true;
		var boolSigue_igual = true;
		
		for(var i = 0; i < storeValida.getCount(); i++)
		{
			var id_asignado = storeValida.getAt(i).data.id_asignado;
			var id_tarea = storeValida.getAt(i).data.id_tarea;
			var tipo = storeValida.getAt(i).data.nombreTipoElemento;
			var idTipo = storeValida.getAt(i).data.idTipo;
							
			/*Se Comenta provisionalmente para que puedan asignar tareas y hasta que se revise la informacion tecnica migrada
			if(tipo==""){
				Ext.Msg.alert("Alerta","Debe escoger el tipo.");
				return false;
			}	
			if(idTipo==""){
				Ext.Msg.alert("Alerta","Debe escoger el Elemento/Tramo.");
				return false;
			}
			*/
			if(id_asignado==""){
				Ext.Msg.alert("Alerta","Debe asignar la tarea por lo menos a un departamento.");
				return false;
			}	
					  
		} 
		
		return true;						
	}
	else
	{
		return false;
	}
}

function agregarTarea(id_caso, numero, fecha, hora, version_inicial, esDepartamento, elementoAfectado, empresa) {

    var flagBoolAsignado = $('#flagBoolAsignado').val();
    var flagAsignado = $('#flagAsignado').val();

    var arrayUltimaMilla = elementoAfectado.split(":");

    winTareas = "";
    var formPanel = "";

    if (winSintomas)
    {
        cierraVentanaByIden(winSintomas);
        winSintomas = "";
    }

    if (winHipotesis)
    {
        cierraVentanaByIden(winHipotesis);
        winHipotesis = "";
    }

    if (winTareas)
    {
        cierraVentanaByIden(winTareas);
        winTareas = "";
    }

    if (!winTareas)
    {
        var conn = new Ext.data.Connection({
            listeners: {
                'beforerequest': {
                    fn: function(con, opt) {
                        Ext.MessageBox.show({
                                        msg: 'Asignando Tarea',
                                        progressText: 'Cargando...',
                                        width: 300,
                                        wait: true,
                                        waitConfig: {interval: 800}
                                    });                        
                    },
                    scope: this
                },
                'requestcomplete': {
                    fn: function(con, res, opt) {
                        Ext.MessageBox.hide();                        
                    },
                    scope: this
                },
                'requestexception': {
                    fn: function(con, res, opt) {
                        Ext.MessageBox.hide();                        
                    },
                    scope: this
                }
            }
        });
        
        btnguardar = Ext.create('Ext.Button', {
            id: 'btnguardarCaso',
            text: 'Guardar',
            cls: 'x-btn-rigth',
            handler: function() {
                if(Ext.getCmp('searchTarea_cmp').getValue() == null || Ext.getCmp('searchTarea_cmp').getValue() == "")
                {
                    Ext.Msg.alert('Alerta ', "Por Favor seleccionar una tarea a asignar");
                }
                else
                {
                    var valorBool = validarTareas();


                    if (valorBool)
                    {
                        json_tareas = obtenerTareas();

                        conn.request({
                            method : 'post',
                            timeout: 200000,
                            params: {
                                id_caso: id_caso,
                                tareas: json_tareas
                            },
                            url: '../actualizarTareas',
                            success: function(response) 
                            {
                                cierraVentanaByIden(winTareas);
                                var json    = Ext.JSON.decode(response.responseText);
                                var mensaje = "";
                                var html    = "";
                                var indice  = 0;

                                html = html + "<table>";
                                Ext.each(json.mensaje, function(ob){
                                    indice = indice + 1;
                                    html = html + "<tr><td>"+indice+".</td>";
                                    if (ob.mensaje.toUpperCase() === "Tarea no creada".toUpperCase()){
                                       html = html + "<td style='padding-left:4px'><b>"+ob.mensaje+":</b></td>";
                                    } else {
                                       html = html + "<td style='padding-left:4px'>"+ob.mensaje+":</td>";
                                    }
                                    html = html + "<td style='padding-left:5px'>"+ob.tarea+"</td>";
                                    html = html + "</tr>";
                                });
                                html = html + "</table>";

                                if (json.mensaje != null && json.mensaje != " " && json.mensaje != ""){
                                    mensaje = html;
                                } else {
                                    mensaje = "Se actualizaron las tareas.";
                                }

                                Ext.Msg.alert('Mensaje', mensaje, function(btn) {
                                    if (btn == 'ok') {
                                        document.location.reload(true);
                                    }
                                });
                            },
                            failure: function(result)
                            {
                                cierraVentanaByIden(winTareas);
                                Ext.MessageBox.show({
                                    title   : 'Error',
                                    msg     : result.statusText,
                                    buttons : Ext.MessageBox.OK,
                                    icon    : Ext.MessageBox.ERROR
                                });
                            }
                        });
                    }
                }
            }
        });
        
        btncancelar = Ext.create('Ext.Button', {
            text: 'Cerrar',
            cls: 'x-btn-rigth',
            handler: function() {
                cierraVentanaByIden(winTareas);
            }
        });

        storeHipotesis = new Ext.data.Store({
            pageSize: 1000,
            total: 'total',
            proxy: {
                type: 'ajax',
                url: '../getHipotesisXCaso',
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    band: 'tarea',
                    id: id_caso,
                    nombre: '',
                    estado: 'Activos'
                }
            },
            fields:
                [
                    {name: 'id_sintoma', mapping: 'id_sintoma'},
                    {name: 'nombre_sintoma', mapping: 'nombre_sintoma'},
                    {name: 'id_hipotesis', mapping: 'id_hipotesis'},
                    {name: 'nombre_hipotesis', mapping: 'nombre_hipotesis'},
                    {name: 'asunto_asignacionCaso', mapping: 'asunto_asignacionCaso'},
                    {name: 'departamento_asignacionCaso', mapping: 'departamento_asignacionCaso'},
                    {name: 'nombreDepartamento_asignacionCaso', mapping: 'nombreDepartamento_asignacionCaso'},
                    {name: 'empleado_asignacionCaso', mapping: 'empleado_asignacionCaso'},
                    {name: 'observacion_asignacionCaso', mapping: 'observacion_asignacionCaso'},
                    {name: 'nombre_asignacionCaso', mapping: 'nombre_asignacionCaso'},
                    {name: 'origen', mapping: 'origen'}
                ],
            autoLoad: true,
            listeners: {
                beforeload: function(sender, options)
                {
                    Ext.MessageBox.show({
                        msg: 'Cargando los datos, Por favor espere!!',
                        progressText: 'Saving...',
                        width: 300,
                        wait: true,
                        waitConfig: {interval: 200}
                    });

                    winTareas = "";
                    formPanel = "";
                },
                load: function(sender, node, records) {
                    Ext.each(records, function(record, index) {
                        if (storeHipotesis.getCount() > 0) {
                            selModelHipotesis = Ext.create('Ext.selection.CheckboxModel', {
                                listeners: {
                                    selectionchange: function(sm, selections) {
                                    }
                                }
                            });

                            gridHipotesis = Ext.create('Ext.grid.Panel', {
                                id: 'gridHipotesis',
                                store: storeHipotesis,
                                viewConfig: {enableTextSelection: true, stripeRows: true},
                                columnLines: true,
                                columns:
                                    [
                                        {
                                            id: 'id_sintoma',
                                            header: 'SintomaId',
                                            dataIndex: 'id_sintoma',
                                            hidden: true,
                                            hideable: false
                                        }, {
                                            id: 'nombre_sintoma',
                                            header: 'Sintoma',
                                            dataIndex: 'nombre_sintoma',
                                            width: 300
                                        }, {
                                            id: 'id_hipotesis',
                                            header: 'id_hipotesis',
                                            dataIndex: 'id_hipotesis',
                                            hidden: true,
                                            hideable: false
                                        }, {
                                            id: 'nombre_hipotesis',
                                            header: 'Hipotesis',
                                            dataIndex: 'nombre_hipotesis',
                                            width: 300,
                                            hideable: false
                                        },
                                        {
                                            id: 'asunto_asignacionCaso',
                                            dataIndex: 'asunto_asignacionCaso',
                                            hidden: true,
                                            hideable: false
                                        },
                                        {
                                            id: 'departamento_asignacionCaso',
                                            dataIndex: 'departamento_asignacionCaso',
                                            hidden: true,
                                            hideable: false
                                        },
                                        {
                                            id: 'nombreDepartamento_asignacionCaso',
                                            dataIndex: 'nombreDepartamento_asignacionCaso',
                                            hidden: true,
                                            hideable: false
                                        },
                                        {
                                            id: 'empleado_asignacionCaso',
                                            dataIndex: 'empleado_asignacionCaso',
                                            hidden: true,
                                            hideable: false
                                        },
                                        {
                                            id: 'observacion_asignacionCaso',
                                            dataIndex: 'observacion_asignacionCaso',
                                            hidden: true,
                                            hideable: false
                                        },
                                        {
                                            id: 'nombre_asignacionCaso',
                                            dataIndex: 'nombre_asignacionCaso',
                                            header: 'Asignado Caso',
                                            width: 220
                                        },
                                        {
                                            id: 'origen',
                                            dataIndex: 'origen',
                                            hidden: true,
                                            hideable: false
                                        }
                                    ],
                                selModel: selModelHipotesis,
                                width: 900,
                                height: 200,
                                frame: true,
                                title: 'Informacion de Hipotesis'
                            });
                            /*******************************************************/

                            comboTiposElementosStore = new Ext.data.Store({
                                pageSize: 10000,
                                total: 'total',
                                proxy: {
                                    type: 'ajax',
                                    url: '../getTiposElementos',
                                    reader: {
                                        type: 'json',
                                        totalProperty: 'total',
                                        root: 'encontrados'
                                    },
                                    extraParams: {
                                        nombre: '',
                                        estado: 'Activo',
                                        caso: id_caso
                                    }
                                },
                                fields:
                                    [
                                        {name: 'idTipoElemento', mapping: 'idTipoElemento'},
                                        {name: 'nombreTipoElemento', mapping: 'nombreTipoElemento'}
                                    ]
                            });


                            /*******************************************************/

                            comboTareaStore = new Ext.data.Store({
                                pageSize: 200,
                                total: 'total',
                                proxy: {
                                    type: 'ajax',
                                    url: '../../../administracion/soporte/admi_tarea/grid',
                                    reader: {
                                        type: 'json',
                                        totalProperty: 'total',
                                        root: 'encontrados'
                                    },
                                    extraParams: {
                                        nombre: '',
                                        estado: 'Activo',
                                        visible: 'SI',
                                        caso: id_caso
                                    }
                                },
                                fields:
                                    [
                                        {name: 'id_tarea', mapping: 'id_tarea'},
                                        {name: 'nombre_tarea', mapping: 'nombre_tarea'}
                                    ]
                            });

                            comboTarea = Ext.create('Ext.form.ComboBox', {
                                id: 'comboTarea',
                                store: comboTareaStore,
                                displayField: 'nombre_tarea',
                                valueField: 'id_tarea',
                                height: 30,
                                border: 0,
                                margin: 0,
                                fieldLabel: false,
                                queryMode: "remote",
                                emptyText: ''
                            });

                            comboTipoStore = new Ext.data.Store({
                                pageSize: 10000,
                                total: 'total',
                                proxy: {
                                    type: 'ajax',
                                    url: '../getTipos',
                                    reader: {
                                        type: 'json',
                                        totalProperty: 'total',
                                        root: 'encontrados'
                                    },
                                    extraParams: {
                                        nombre: '',
                                        estado: 'Activo',
                                        caso: id_caso
                                    }
                                },
                                fields:
                                    [
                                        {name: 'idTipo', mapping: 'idTipo'},
                                        {name: 'nombreTipo', mapping: 'nombreTipo'}
                                    ]
                            });

                            comboElementoStore = new Ext.data.Store({
                                pageSize: 10000,
                                total: 'total',
                                proxy: {
                                    type: 'ajax',
                                    url: '../getElementos',
                                    reader: {
                                        type: 'json',
                                        totalProperty: 'total',
                                        root: 'encontrados'
                                    },
                                    extraParams: {
                                        nombre: '',
                                        estado: 'Activo'
                                    }
                                },
                                fields:
                                    [
                                        {name: 'idElemento', mapping: 'idElemento'},
                                        {name: 'nombreElemento', mapping: 'nombreElemento'}
                                    ]
                            });

                            comboElemento = Ext.create('Ext.form.ComboBox', {
                                id: 'comboElemento',
                                store: comboElementoStore,
                                displayField: 'nombreElemento',
                                valueField: 'idElemento',
                                height: 30,
                                border: 0,
                                margin: 0,
                                fieldLabel: false,
                                queryMode: "remote",
                                emptyText: ''
                            });

                            Ext.define('Tarea', {
                                extend: 'Ext.data.Model',
                                fields: [
                                    {name: 'id_sintomaTarea', type: 'string'},
                                    {name: 'nombre_sintomaTarea', type: 'string'},
                                    {name: 'id_hipotesisTarea', type: 'string'},
                                    {name: 'nombre_hipotesisTarea', type: 'string'},
                                    {name: 'id_tarea', type: 'string'},
                                    {name: 'nombre_tarea', type: 'string'},
                                    {name: 'idTipoElemento', mapping: 'idTipoElemento'},
                                    {name: 'nombreTipoElemento', mapping: 'nombreTipoElemento'},
                                    {name: 'idTipo', type: 'string'},
                                    {name: 'nombreTipo', type: 'string'},
                                    {name: 'id_asignado', type: 'string'},
                                    {name: 'id_refAsignado', type: 'string'},
                                    {name: 'id_personaEmpresaRol', type: 'string'},
                                    {name: 'observacion', type: 'string'},
                                    {name: 'asunto', type: 'string'},
                                    {name: 'tar_origen', type: 'string'},
                                    {name: 'tar_id_empleado', type: 'string'},
                                    {name: 'tar_id_departamento', type: 'string'},
                                    {name: 'edit_hal', type: 'string'}
                                ]
                            });

                            storeTareas = new Ext.data.Store({
                                pageSize: 1000,
                                total: 'total',
                                proxy: {
                                    type: 'ajax',
                                    url: '../getTareasXCaso',
                                    reader: {
                                        type: 'json',
                                        totalProperty: 'total',
                                        root: 'encontrados'
                                    },
                                    extraParams: {
                                        id: id_caso,
                                        nombre: '',
                                        estado: 'Activos'
                                    }
                                },
                                fields:
                                    [
                                        {name: 'id_sintomaTarea', mapping: 'id_sintomaTarea'},
                                        {name: 'nombre_sintomaTarea', mapping: 'nombre_sintomaTarea'},
                                        {name: 'id_hipotesisTarea', mapping: 'id_hipotesisTarea'},
                                        {name: 'nombre_hipotesisTarea', mapping: 'nombre_hipotesisTarea'},
                                        {name: 'id_tarea', mapping: 'id_tarea'},
                                        {name: 'nombre_tarea', mapping: 'nombre_tarea'},                                        
                                        {name: 'idTipoElemento', mapping: 'idTipoElemento'},
                                        {name: 'nombreTipoElemento', mapping: 'nombreTipoElemento'},                                        
                                        {name: 'idTipo', mapping: 'idTipo'},
                                        {name: 'nombreTipo', mapping: 'nombreTipo'},
                                        {name: 'id_asignado', mapping: 'id_asignado'},
                                        {name: 'id_refAsignado', mapping: 'id_refAsignado'},
                                        {name: 'id_personaEmpresaRol', mapping: 'id_personaEmpresaRol'},
                                        {name: 'tar_origen', mapping: 'tar_origen'},
                                        {name: 'tar_id_empleado', mapping: 'tar_id_empleado'},
                                        {name: 'tar_id_departamento', mapping: 'tar_id_departamento'}
                                    ]
                            });

                            cellEditingTareas = Ext.create('Ext.grid.plugin.CellEditing', {
                                clicksToEdit: 1,
                                listeners: {
                                    beforeedit: function(editor, e){
                                        var allowed = true;
                                        var editHal = e.grid.store.getAt(e.rowIdx).data.edit_hal;
                                        if(editHal == 'NO') {
                                            allowed = false;
                                            Ext.Msg.alert('Alert','Registro bloqueado por Hal..!!');
                                        }
                                        return allowed;
                                    },
                                    edit: function() {
                                        gridTareas.getView().refresh();
                                    }
                                }
                            });

                            selModelTareas = Ext.create('Ext.selection.CheckboxModel', {
                                checkOnly: true,
                                listeners: {
                                    selectionchange: function(sm, selections) {
                                        gridTareas.down('#removeButton').setDisabled(selections.length === 0);
                                    }
                                }
                            });


                            combo_tarea = new Ext.form.ComboBox({
                                id: 'searchTarea_cmp',
                                name: 'searchTarea_cmp',
                                displayField: 'nombre_tarea',
                                valueField: 'id_tarea',
                                store: comboTareaStore,
                                loadingText: 'Buscando ...',
                                disabled: true,
                                fieldLabel: false,
                                queryMode: "remote",
                                emptyText: '',
                                listClass: 'x-combo-list-small'
                            });

                            combo_tipo = new Ext.form.ComboBox({
                                id: 'searchTipo_cmp',
                                name: 'searchTipo_cmp',
                                displayField: 'nombreTipoElemento',
                                valueField: 'idTipoElemento',
                                store: comboTiposElementosStore,
                                loadingText: 'Buscando ...', 														
                                disabled: true,
                                fieldLabel: false,
                                queryMode: "remote",
                                listClass: 'x-combo-list-small',
                                listeners: {
                                    select: function(combo) {
                                        comboTipoStore.proxy.extraParams = {tipo: combo.getValue(), caso: id_caso};
                                    }
                                }
                            });

                            combo_elemento = new Ext.form.ComboBox({
                                id: 'searchElemento_cmp',
                                name: 'searchElemento_cmp',
                                displayField: 'nombreTipo',
                                valueField: 'idTipo',
                                store: comboTipoStore,
                                triggerAction: 'all',
                                minChars: 3,
                                loadingText: 'Buscando ...',
                                disabled: true,
                                fieldLabel: false,
                                queryMode: "remote",
                                emptyText: '',
                                listClass: 'x-combo-list-small'
                            });
                            
                            
                            var conn = new Ext.data.Connection({
                                listeners: {
                                    'beforerequest': {
                                        fn: function (con, opt) {
                                            Ext.get(document.body).mask('Obteniendo Fecha y Hora...');
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


                            gridTareas = Ext.create('Ext.grid.Panel', {
                                id: 'gridTareas',
                                store: storeTareas,
                                viewConfig: {enableTextSelection: true, stripeRows: true},
                                columnLines: true,
                                columns:
                                    [{
                                            id: 'id_sintomaTarea',
                                            header: 'SintomaId',
                                            dataIndex: 'id_sintomaTarea',
                                            hidden: true,
                                            hideable: false
                                        }, {
                                            id: 'nombre_sintomaTarea',
                                            header: 'Sintoma',
                                            dataIndex: 'nombre_sintomaTarea',
                                            width: 120
                                        },
                                        {
                                            id: 'id_hipotesisTarea',
                                            header: 'HipotesisId',
                                            dataIndex: 'id_hipotesisTarea',
                                            hidden: true,
                                            hideable: false
                                        }, {
                                            id: 'nombre_hipotesisTarea',
                                            header: 'Hipotesis',
                                            dataIndex: 'nombre_hipotesisTarea',
                                            width: 130,
                                            sortable: true
                                        },
                                        {
                                            id: 'id_tarea',
                                            header: 'HipotesisId',
                                            dataIndex: 'id_tarea',
                                            hidden: true,
                                            hideable: false
                                        }, {
                                            id: 'nombre_tarea',
                                            header: 'Tarea',
                                            dataIndex: 'nombre_tarea',
                                            width: 180,
                                            sortable: true,
                                            renderer: function(value, metadata, record, rowIndex, colIndex, store) {
                                                var dataOrigen = record.data.tar_origen;
                                                if (dataOrigen == 'Nuevo')
                                                {
                                                    combo_tarea.setDisabled(false);
                                                }
                                                record.data.id_tarea_seleccionada = record.data.id_tarea;  
                                                record.data.id_tarea = record.data.nombre_tarea;

                                                for (var i = 0; i < comboTareaStore.data.items.length; i++)
                                                {
                                                    if (comboTareaStore.data.items[i].data.id_tarea == record.data.id_tarea)
                                                    {
                                                        gridTareas.getStore().getAt(rowIndex).data.id_sintoma = record.data.id_tarea;

                                                        record.data.id_tarea = comboTareaStore.data.items[i].data.id_tarea;
                                                        record.data.nombre_tarea = comboTareaStore.data.items[i].data.nombre_tarea;
                                                        break;
                                                    }
                                                }
                                                return record.data.nombre_tarea;
                                            },
                                            editor: combo_tarea
                                        },
                                        {
                                            id: 'idTipoElemento',
                                            header: 'idTipoElemento',
                                            dataIndex: 'idTipoElemento',
                                            hidden: true,
                                            hideable: false
                                        },
                                        {
                                            id: 'nombreTipoElemento',
                                            header: 'Tipo',
                                            dataIndex: 'nombreTipoElemento',
                                            width: 120,
                                            sortable: true,
                                            renderer: function(value, metadata, record, rowIndex, colIndex, store) {
                                                var dataOrigen = record.data.tar_origen;
                                                if (dataOrigen == 'Nuevo')
                                                {
                                                    combo_tipo.setDisabled(false);
                                                }

                                                record.data.idTipoElemento = record.data.nombreTipoElemento;

                                                for (var i = 0; i < comboTiposElementosStore.data.items.length; i++)
                                                {
                                                    if (comboTiposElementosStore.data.items[i].data.idTipoElemento == record.data.nombreTipoElemento)
                                                    {
                                                        gridTareas.getStore().getAt(rowIndex).data.idTipoElemento = record.data.idTipoElemento;
                                                        gridTareas.getStore().getAt(rowIndex).data.nombreTipoElemento = record.data.nombreTipoElemento;

                                                        record.data.idTipoElemento = comboTiposElementosStore.data.items[i].data.idTipoElemento;
                                                        record.data.nombreTipoElemento = comboTiposElementosStore.data.items[i].data.nombreTipoElemento;
                                                        break;
                                                    }
                                                }
                                                return record.data.nombreTipoElemento;

                                            },
                                            editor: combo_tipo
                                        },
                                        {
                                            id: 'idTipo',
                                            header: 'idTipo',
                                            dataIndex: 'idTipo',
                                            hidden: true,
                                            hideable: false
                                        }, {
                                            id: 'nombreTipo',
                                            header: 'Elemento/Tramo',
                                            dataIndex: 'nombreTipo',
                                            width: 180,
                                            sortable: true,
                                            renderer: function(value, metadata, record, rowIndex, colIndex, store) {
                                                var dataOrigen = record.data.tar_origen;
                                                if (dataOrigen == 'Nuevo')
                                                {
                                                    combo_elemento.setDisabled(false);
                                                }
                                               
                                                for (var i = 0; i < comboTipoStore.data.items.length; i++)
                                                {
                                                    if (comboTipoStore.data.items[i].data.idTipo == record.data.nombreTipo)
                                                    {
                                                        gridTareas.getStore().getAt(rowIndex).data.idTipo = comboTipoStore.data.items[i].data.idTipo;
                                                        gridTareas.getStore().getAt(rowIndex).data.nombreTipo = comboTipoStore.data.items[i].data.nombreTipo;
                                                        record.data.idTipo = comboTipoStore.data.items[i].data.idTipo;
                                                        record.data.nombreTipo = comboTipoStore.data.items[i].data.nombreTipo;
                                                        break;
                                                    }
                                                }
                                                return record.data.nombreTipo;
                                            },
                                            editor: combo_elemento
                                        },
                                        {
                                            header: 'Acciones',
                                            xtype: 'actioncolumn',
                                            width: 80,
                                            sortable: false,
                                            items: [{
                                                    getClass: function(v, meta, rec)
                                                    {
                                                        var cssName = "icon-invisible";
                                                        if (rec.get('tar_origen') == 'Nuevo' && rec.get('edit_hal') == 'SI')
                                                        {
                                                            cssName = "button-grid-asignarResponsable";
                                                        }

                                                        if (cssName == "icon-invisible")
                                                            this.items[0].tooltip = '';
                                                        else
                                                            this.items[0].tooltip = 'Asignar Tarea';

                                                        return cssName;
                                                    },
                                                    tooltip: 'Asignar Tarea',
                                                    handler: function(grid, rowIndex, colIndex) 
                                                    {
                                                        if (Ext.getCmp('searchTarea_cmp').getValue() == null || Ext.getCmp('searchTarea_cmp').getValue() == "")
                                                        {
                                                            Ext.Msg.alert('Alerta ', "Por Favor seleccionar una tarea a asignar");
                                                        } else
                                                        {
                                                            conn.request({
                                                                method: 'POST',
                                                                url: url_obtenerFechaServer,
                                                                success: function (response) {
                                                                    var json = Ext.JSON.decode(response.responseText);

                                                                    if (json.success) {
                                                                        agregarAsignacionXTarea(id_caso, grid.getStore().getAt(rowIndex), json.fechaActual, json.horaActual, empresa);
                                                                    }
                                                                    else {
                                                                        Ext.Msg.alert('Alerta ', json.error);
                                                                    }
                                                                }
                                                            });
                                                        }
                                                    }
                                                }]
                                        },
                                        {
                                            id: 'id_asignado',
                                            header: 'id_asignado',
                                            dataIndex: 'id_asignado',
                                            hidden: true,
                                            hideable: false
                                        },
                                        {
                                            id: 'id_refAsignado',
                                            header: 'id_refAsignado',
                                            dataIndex: 'id_refAsignado',
                                            hidden: true,
                                            hideable: false
                                        },
                                        {
                                            id: 'id_personaEmpresaRol',
                                            header: 'id_personaEmpresaRol',
                                            dataIndex: 'id_personaEmpresaRol',
                                            hidden: true,
                                            hideable: false
                                        },
                                        {
                                            id: 'observacion',
                                            header: 'observacion',
                                            dataIndex: 'observacion',
                                            hidden: true,
                                            hideable: false
                                        },
                                        {
                                            id: 'asunto',
                                            header: 'asunto',
                                            dataIndex: 'asunto',
                                            hidden: true,
                                            hideable: false
                                        },
                                        {
                                            id: 'tar_origen',
                                            dataIndex: 'tar_origen',
                                            hidden: true,
                                            hideable: false
                                        },
                                        {
                                            id: 'tar_id_empleado',
                                            dataIndex: 'tar_id_empleado',
                                            hidden: true,
                                            hideable: false
                                        },
                                        {
                                            id: 'tar_id_departamento',
                                            dataIndex: 'tar_id_departamento',
                                            hidden: true,
                                            hideable: false
                                        }
                                    ],
                                dockedItems: [{
                                        xtype: 'toolbar',
                                        items: [{
                                                itemId: 'removeButton',
                                                text: 'Eliminar',
                                                tooltip: 'Elimina el item seleccionado',
                                                disabled: true,
                                                handler: function() {
                                                    eliminarSeleccion(gridTareas, 'gridTareas', selModelTareas);
                                                }
                                            }, '-', {
                                                itemId: 'addButton',
                                                text: 'Agregar',
                                                tooltip: 'Agrega un item a la lista',                                                
                                                handler: function() {

                                                    if ((flagBoolAsignado && flagAsignado) || empresa == "TN")
                                                    {

                                                        if (selModelHipotesis.getSelection().length > 0)
                                                        {
                                                            if (selModelHipotesis.getSelection().length > 1) {
                                                                Ext.Msg.alert('Alerta', 'Solo debe seleccionar una hipotesis a la vez.');
                                                                return;
                                                            }

                                                            for (var i = 0; i < selModelHipotesis.getSelection().length; ++i)
                                                            {
                                                                sintoma_id = selModelHipotesis.getSelection()[i].data.id_sintoma;
                                                                sintoma_nombre = selModelHipotesis.getSelection()[i].data.nombre_sintoma;
                                                                hipotesis_id = selModelHipotesis.getSelection()[i].data.id_hipotesis;
                                                                hipotesis_nombre = selModelHipotesis.getSelection()[i].data.nombre_hipotesis;
                                                                id_empleado = selModelHipotesis.getSelection()[i].data.empleado_asignacionCaso;
                                                                id_departamento = selModelHipotesis.getSelection()[i].data.departamento_asignacionCaso;
                                                                nombre_departamento = selModelHipotesis.getSelection()[i].data.nombreDepartamento_asignacionCaso;
                                                            }
                                                            if (esDepartamento || empresa == "TN")
                                                            {

                                                                var r = Ext.create('Tarea', {
                                                                    id_sintomaTarea: sintoma_id,
                                                                    nombre_sintomaTarea: sintoma_nombre,
                                                                    id_hipotesisTarea: hipotesis_id,
                                                                    nombre_hipotesisTarea: hipotesis_nombre,
                                                                    id_tarea: '',
                                                                    nombre_tarea: '',
                                                                    idTipoElemento: arrayUltimaMilla[1],
                                                                    nombreTipoElemento: arrayUltimaMilla[0],
                                                                    idTipo: arrayUltimaMilla[3],
                                                                    nombreTipo: arrayUltimaMilla[2],
                                                                    id_asignado: '',
                                                                    id_refAsignado: '',
                                                                    id_personaEmpresaRol: '',
                                                                    observacion: '',
                                                                    asunto: '',
                                                                    tar_origen: 'Nuevo',
                                                                    tar_id_empleado: id_empleado,
                                                                    tar_id_departamento: id_departamento,
                                                                    edit_hal: 'SI'
                                                                });
                                                                storeTareas.insert(0, r);

                                                            }
                                                            else
                                                            {
                                                                Ext.Msg.alert('Alerta ', "Esta hipotesis esta asignada a otro departamento, no puede ingresar tareas.");
                                                            }
                                                        } else {
                                                            Ext.Msg.alert('Alerta', 'Debe escoger una hipotesis para ingresar las tareas correspondientes.');
                                                            return;
                                                        }

                                                    }
                                                    else
                                                    {
                                                        Ext.Msg.alert('Alerta ', "No tiene permisos para crear Tareas, porque el caso fue asignado a otra persona");
                                                    }

                                                }
                                            }]
                                    }],
                                selModel: selModelTareas,
                                width: 900,
                                height: 200,
                                frame: true,
                                plugins: [cellEditingTareas],
                                title: 'Ingresar Informacion de Tareas'
                            });

                            formPanel = Ext.create('Ext.form.Panel', {
                                id:"formPanelCaso",
                                bodyPadding: 5,
                                waitMsgTarget: true,
                                height: 200,
                                layout: 'fit',
                                fieldDefaults: {
                                    labelAlign: 'left',
                                    labelWidth: 140,
                                    msgTarget: 'side'
                                },
                                items: [{
                                        xtype: 'fieldset',
                                        title: 'Información del Caso',
                                        defaultType: 'textfield',
                                        items: [
                                            {
                                                xtype: 'displayfield',
                                                fieldLabel: 'Caso:',
                                                id: 'numero_casoSintoma',
                                                name: 'numero_casoSintoma',
                                                value: numero
                                            },
                                            {
                                                xtype: 'displayfield',
                                                fieldLabel: 'Fecha apertura:',
                                                id: 'fechacaso',
                                                name: 'fechaCaso',
                                                value: fecha + " " + hora
                                            },
                                            {
                                                xtype: 'textarea',
                                                fieldLabel: 'Version Inicial:',
                                                id: 'version_inicialSintoma',
                                                name: 'version_inicialSintoma',
                                                rows: 3,
                                                cols: 100,
                                                readOnly: true,
                                                value: version_inicial
                                            },
                                            gridHipotesis,
                                            gridTareas
                                        ]
                                    }]
                            });

                            winTareas = Ext.create('Ext.window.Window', {
                                title: 'Agregar Tareas',
                                modal: true,
                                width: 960,
                                height: 620,
                                resizable: false,
                                layout: 'fit',
                                closabled: false,
                                items: [formPanel],
                                buttonAlign: 'center',
                                buttons: [btnguardar, btncancelar]
                            }).show();
                        } else {
                            Ext.Msg.alert("Alerta", "Debe ingresar por lo menos una hipotesis para ingresar tareas al caso.");
                        }

                        Ext.MessageBox.hide();
                    }, this);
                }
            }
        });

    }
}

function presentarCiudades(empresa){
      
    storeCiudades.proxy.extraParams = { empresa:empresa};
    storeCiudades.load();
  
  
}


function presentarDepartamentosPorCiudad(id_canton , empresa){
  
    storeDepartamentosCiudad.proxy.extraParams = { id_canton:id_canton,empresa:empresa};
    storeDepartamentosCiudad.load();
  
}


function presentarEmpleadosXDepartamentoCiudad(id_departamento, id_canton, empresa, valorIdDepartamento , esJefe){
  
    storeEmpleados.proxy.extraParams = { id_canton:id_canton,empresa:empresa,id_departamento:id_departamento,departamento_caso:valorIdDepartamento, es_jefe:esJefe};
    storeEmpleados.load();
  
}


function presentarCuadrillasXDepartamento(id_departamento){
    
    storeCuadrillas.proxy.extraParams = { departamento:id_departamento,estado: 'Eliminado',origenD: 'Departamento'};
    storeCuadrillas.load();
  
}

function presentarContratistas(){

    storeContratista.proxy.extraParams = { rol:'Empresa Externa' };
    storeContratista.load();

}

function agregarAsignacionXTarea(id_caso,tarea, fechaActual , horaActual, empresa)
{

	var globalComboEmpresa      = $('#globalEmpresaEscogida').val();
    var arrayValores            = globalComboEmpresa.split('@@');
    var valorIdDepartamento     = '';
    var idTareaSeleccionada = tarea.data.id_tarea_seleccionada;
    var ocultarFormularioContactoCliente = false;
 
    if (arrayValores && arrayValores.length > 3)
    {
        valorIdDepartamento = arrayValores[4];
    }

    storeEmpleados = new Ext.data.Store({
        total: 'total',
        pageSize: 1000,
        proxy: {
            type: 'ajax',
            url: url_empleadosDepartamento,
            reader: {
                type: 'json',
                totalProperty: 'result.total',
                root: 'result.encontrados',
                metaProperty: 'myMetaData'
            },
            extraParams: {
                nombre: ''
            }
        },
        fields:
            [
                {name: 'id_empleado', mapping: 'id_empleado'},
                {name: 'nombre_empleado', mapping: 'nombre_empleado'}
            ]
    });


    var storeEmpresas = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_empresaPorSistema,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                app: 'TELCOS'                    
            }
        },
        fields:
            [
                {name: 'opcion', mapping: 'nombre_empresa'},
                {name: 'valor', mapping: 'prefijo'}
            ]
    });
    /*****************************************************************************/

    storeCiudades = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: '../getCiudadesPorEmpresa',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
                estado: 'Activo'
            }
        },
        fields:
            [
                {name: 'id_canton', mapping: 'id_canton'},
                {name: 'nombre_canton', mapping: 'nombre_canton'}
            ]
    });
    storePrefijoProvincias = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: '../getPrefijosTelefono',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'codigo', mapping: 'codigo'}
            ]
    });

    storeDepartamentosCiudad = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: '../getDepartamentosPorEmpresaYCiudad',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
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

    FieldsDatosContacto = new Ext.form.FieldSet(
        {
            xtype: 'fieldset',
            title: '<i class="fa fa-user" aria-hidden="true"></i>&nbsp;<b style="color:black";>Registro de Contacto del Cliente</b>&nbsp;',
            width: 500,
            id: 'fieldsDatosContacto',
            border: '1 1 1 0',
            cls: 'fieldsHeaderDatosContacto',
            padding: 0,
            items:
                [
                    {
                        xtype: 'textfield',
                        hidden: ocultarFormularioContactoCliente,
                        id: 'nombreClienteARecibir',
                        fieldLabel: 'Nombre y Apellido',
                        name: 'nombreCliente',
                        width: 'auto',
                        maxLength: 50,
                        enforceMaxLength : true,
                        allowBlank: true,
                        validator: function (v) 
                        {
                            if (!/^[A-Za-zÀ-ÿ\\u00f1\\u00d1\s]+$/.test(v) && v.length > 0)
                            {
                                return 'Solo se permiten caracteres de texto';
                            } else
                            {
                                return true;
                            }
                        }
                    },
                    {
                        xtype: 'textfield',
                        hidden: ocultarFormularioContactoCliente,
                        id: 'telefonoClienteARecibir',
                        fieldLabel: 'Celular',
                        name: 'telefonoCliente',
                        width: 'auto',
                        allowBlank: true,
                        //minLength:10,
                        maxLength: 10,
                        enforceMaxLength: 10,
                        validator: function (v)
                        {
                            if (!/^[0-9]+$/.test(v) && v.length > 0)
                            {
                                return 'Se permiten solo números';
                            } else if (v.length > 0 && v[0] != 0)
                            {
                                return 'El número de celular debe empezar con 0, ejemplo: 098XXXXXXX'
                            } else if (v.length > 0 && v.length < 10)
                            {
                                return 'Se debe ingresar 10 digitos'
                            } else
                            {
                                return true;
                            }
                        }
                    },
                    {
                        xtype: 'textfield',
                        hidden: ocultarFormularioContactoCliente,
                        id: 'cargoClienteARecibir',
                        fieldLabel: 'Cargo/Área',
                        name: 'cargoCliente',
                        maxLength: 50,
                        enforceMaxLength : true,
                        width: 'auto',
                        allowBlank: true,
                        validator: function (v) 
                        {
                            if (!/^[A-Za-z0-9À-ÿ\\u00f1\\u00d1\s]+$/.test(v) && v.length > 0)
                            {
                                return 'Solo se permiten caracteres alfanuméricos';
                            } else
                            {
                                return true;
                            }
                        }
                    },
                    {
                        xtype: 'textfield',
                        hidden: ocultarFormularioContactoCliente,
                        id: 'correoClienteARecibir',
                        fieldLabel: 'Correo',
                        maxLength: 50,
                        enforceMaxLength : true,
                        name: 'correoCliente',
                        width: 'auto',
                        validator: function (v)
                        {
                            if (!Ext.form.VTypes.email(v) && v.length > 0)
                            {
                                return 'Debe ingresar un correo electrónico válido';
                            } else
                            {
                                return true;
                            }
                        },
                        allowBlank: true
                    },
                    {
                        xtype: 'fieldcontainer',
                        fieldLabel: 'Convencional',
                        labelWidth: 200,
                        layout: 'hbox',
                        items: [
                            {
                                xtype: 'combobox',
                                id: 'prefijoNumeroClienteARecibir',
                                name: 'prefijoNumeroClienteARecibir',
                                store: storePrefijoProvincias,
                                displayField: 'codigo',
                                valueField: 'codigo',
                                queryMode: "remote",
                                emptyText: '',
                                width: 40,
                                listeners:
                                {
                                    select: function (combo)
                                    {
                                        console.log(combo.getValue());
                                        if (combo.getValue() != null)
                                        {
                                            Ext.getCmp('prefijoNumeroClienteARecibir').setActiveError(false)
                                        }
                                    }
                                },
                                forceSelection: true
                            },
                            {
                                xtype: 'textfield',
                                hidden: ocultarFormularioContactoCliente,
                                id: 'convencionalClienteARecibir',
                                name: 'convencionalCliente',
                                width: 110,
                                allowBlank: true,
                                validator: function (v)
                                {
                                    if (Ext.getCmp('prefijoNumeroClienteARecibir').getValue() == null && v.length > 0)
                                    {
                                        Ext.getCmp('prefijoNumeroClienteARecibir').setActiveError('Seleccionar código de área')
                                        return 'Debe seleccionar un código de área';
                                    } else if (!/^[0-9]+$/.test(v) && v.length > 0)
                                    {
                                        return 'Se permiten solo números';
                                    } else if (v.length > 0 && v[0] != 2)
                                    {
                                        return 'El número convencional debe empezar con 2, ejemplo: 29XXXXX'
                                    } else if (v.length > 0 && v.length < 7)
                                    {
                                        return 'Se debe ingresar 7 digitos'
                                    } else
                                    {
                                        return true;
                                    }
                                },
                                maxLength: 7,
                                enforceMaxLength: 7,
                            }]
                    }
                ]
    });

    FieldsDatosContactoHal = new Ext.form.FieldSet(
        {
            xtype: 'fieldset',
            title: '<i class="fa fa-user" aria-hidden="true"></i>&nbsp;<b style="color:black";>Registro de Contacto del Cliente</b>&nbsp;',
            //width: 600,
            id: 'fieldsDatosContactoHal',
            border: '1 1 1 0',
            cls: 'fieldsHeaderDatosContacto',
            padding: 0,
            items:
                [
                    {
                        xtype: 'textfield',
                        hidden: ocultarFormularioContactoCliente,
                        id: 'nombreClienteARecibirHal',
                        fieldLabel: 'Nombre y Apellido',
                        name: 'nombreCliente',
                        width: 'auto',
                        maxLength: 50,
                        enforceMaxLength : true,
                        allowBlank: true,
                        validator: function (v) 
                        {
                            if (!/^[A-Za-zÀ-ÿ\\u00f1\\u00d1\s]+$/.test(v) && v.length > 0)
                            {
                                return 'Solo se permiten caracteres de texto';
                            } else
                            {
                                return true;
                            }
                        }
                    },
                    {
                        xtype: 'textfield',
                        hidden: ocultarFormularioContactoCliente,
                        id: 'telefonoClienteARecibirHal',
                        fieldLabel: 'Celular',
                        name: 'telefonoCliente',
                        width: 'auto',
                        allowBlank: true,
                        //minLength:10,
                        maxLength: 10,
                        enforceMaxLength: 10,
                        validator: function (v)
                        {
                            if (!/^[0-9]+$/.test(v) && v.length > 0)
                            {
                                return 'Se permiten solo números';
                            } else if (v.length > 0 && v[0] != 0)
                            {
                                return 'El número de celular debe empezar con 0, ejemplo: 098XXXXXXX'
                            } else if (v.length > 0 && v.length < 10)
                            {
                                return 'Se debe ingresar 10 digitos'
                            } else {
                                return true;
                            }
                        }
                    },
                    {
                        xtype: 'textfield',
                        hidden: ocultarFormularioContactoCliente,
                        id: 'cargoClienteARecibirHal',
                        fieldLabel: 'Cargo/Área',
                        name: 'cargoCliente',
                        width: 'auto',
                        maxLength: 50,
                        enforceMaxLength : true,
                        allowBlank: true,
                        validator: function (v) 
                        {
                            if (!/^[A-Za-z0-9À-ÿ\\u00f1\\u00d1\s]+$/.test(v) && v.length > 0)
                            {
                                return 'Solo se permiten caracteres alfanuméricos';
                            } else
                            {
                                return true;
                            }
                        }
                    },
                    {
                        xtype: 'textfield',
                        hidden: ocultarFormularioContactoCliente,
                        id: 'correoClienteARecibirHal',
                        fieldLabel: 'Correo',
                        maxLength: 50,
                        enforceMaxLength : true,
                        name: 'correoCliente',
                        width: 'auto',
                        validator: function (v)
                        {
                            if (!Ext.form.VTypes.email(v) && v.length > 0)
                            {
                                return 'Debe ingresar un correo electrónico válido';
                            } else
                            {
                                return true;
                            }
                        },
                        allowBlank: true
                    },
                    {
                        xtype: 'fieldcontainer',
                        fieldLabel: 'Convencional',
                        labelWidth: 200,
                        layout: 'hbox',
                        items: [
                            {
                                xtype: 'combobox',
                                id: 'prefijoNumeroClienteARecibirHal',
                                name: 'prefijoNumeroClienteARecibir',
                                store: storePrefijoProvincias,
                                displayField: 'codigo',
                                valueField: 'codigo',
                                queryMode: "remote",
                                emptyText: '',
                                width: 40,
                                listeners:
                                {
                                    select: function (combo)
                                    {
                                        console.log(combo.getValue());
                                        if (combo.getValue() != null) 
                                        {
                                            Ext.getCmp('prefijoNumeroClienteARecibirHal').setActiveError(false)
                                        }
                                    }
                                },
                                forceSelection: true
                            },
                            {
                                xtype: 'textfield',
                                hidden: ocultarFormularioContactoCliente,
                                id: 'convencionalClienteARecibirHal',
                                name: 'convencionalCliente',
                                width: 110,
                                allowBlank: true,
                                validator: function (v)
                                {
                                    if (Ext.getCmp('prefijoNumeroClienteARecibirHal').getValue() == null && v.length > 0)
                                    {
                                        Ext.getCmp('prefijoNumeroClienteARecibirHal').setActiveError('Seleccionar código de área')
                                        return 'Debe seleccionar un código de área';
                                    } else if (!/^[0-9]+$/.test(v) && v.length > 0)
                                    {
                                        return 'Se permiten solo números';
                                    } else if (v.length > 0 && v[0] != 2)
                                    {
                                        return 'El número convencional debe empezar con 2, ejemplo: 29XXXXX'
                                    } else if (v.length > 0 && v.length < 7)
                                    {
                                        return 'Se debe ingresar 7 digitos'
                                    } else {
                                        return true;
                                    }
                                },
                                maxLength: 7,
                                enforceMaxLength: 7,
                            }]
                    }
                ]
    });
    storeCuadrillas = new Ext.data.Store({ 
        total: 'total',
        pageSize: 9999,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_integrantesCuadrilla,
            reader: {
                type: 'json',
                totalProperty: 'result.total',
                root: 'result.encontrados',
                metaProperty: 'myMetaData' 
            },
            extraParams: {
                estado: 'Eliminado'
            }
        },
        fields:
              [
                {name:'idCuadrilla', mapping:'idCuadrilla'},
                {name:'nombre', mapping:'nombre'}
              ] ,
        listeners: {
	 
            load: function(store) { 
                if(store.proxy.extraParams.origenD == "Departamento")
                {                
                    document.getElementById('radio_e').disabled  = false;
                    document.getElementById('radio_c').disabled  = false;
                    document.getElementById('radio_co').disabled = false;
                    document.getElementById('radio_e').checked   = false;
                    document.getElementById('radio_c').checked   = false;
                    document.getElementById('radio_co').checked  = false;
                    Ext.getCmp('comboCuadrilla').setDisabled(true);
                    Ext.getCmp('comboEmpleado').setDisabled(true);
                    Ext.getCmp('comboContratista').setDisabled(true);
                    
                    storeCuadrillas.proxy.extraParams.origenD = '';
                }
            }
	 
        }               
	}); 
    
    storeContratista = new Ext.data.Store({
        total: 'total',
        pageSize: 9999,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_empresasExternas,
            reader: {
                type: 'json',
                totalProperty: 'result.total',
                root: 'result.encontrados',
                metaProperty: 'myMetaData'
            },
            extraParams: {
                rol : 'Empresa Externa'
            }
        },
        fields:
              [
                {name:'id_empresa_externa', mapping:'id_empresa_externa'},
                {name:'nombre_empresa_externa', mapping:'nombre_empresa_externa'}
              ]
	});
    
	var iniHtml =   '<div align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n\
                      &nbsp;<input type="radio" onchange="setearCombo(1);" value="empleado" name="radioCuadrilla" id="radio_e" \n\
                      disabled>&nbsp;Empleado&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" onchange="setearCombo(2);" value="cuadrilla" \n\
                      name="radioCuadrilla" id="radio_c" disabled>&nbsp;Cuadrilla&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" \n\
                      onchange="setearCombo(3);" value="contratista" name="radioCuadrilla" id="radio_co" disabled>&nbsp;Contratista</div>';
					   
    RadiosTiposResponsable =  Ext.create('Ext.Component', {
       html: iniHtml,    
       width: 600,
       padding: 10,
       style: { color: '#000000' }});
 
    
    combo_empleados = new Ext.form.ComboBox({
        id: 'comboEmpleado',
        name: 'comboEmpleado',
        fieldLabel: "Empleado",
        store: storeEmpleados,
        displayField: 'nombre_empleado',
        valueField: 'id_empleado',
        queryMode: "remote",
        emptyText: '',
        disabled: true,
        listeners: {
            select: function(){							
                Ext.getCmp('comboCuadrilla').value = "";
                Ext.getCmp('comboCuadrilla').setRawValue("");                                
            }
        }          
    });
    
    /* Panel principal para Agregar Tareas */
    formPanel = Ext.create('Ext.form.Panel', {
                title: "Manual",
		bodyPadding: 5,
		waitMsgTarget: true,
		fieldDefaults: {
			labelAlign: 'left',
			labelWidth: 200,                                
			msgTarget: 'side'
		},
		items: 
		[
			{
				xtype: 'fieldset',
				defaults: {
					width: 500
				},
                items: 
				[
					{
						xtype: 'combobox',
						fieldLabel: 'Empresa:',
						id: 'comboEmpresa',
						name: 'comboEmpresa',
						store: storeEmpresas,
						displayField: 'opcion',
						valueField: 'valor',
						queryMode: "remote",
						emptyText: '' ,
						listeners: {
							select: function(combo){							
							  
								Ext.getCmp('comboCiudad').reset();									
								Ext.getCmp('comboDepartamento').reset();
								Ext.getCmp('comboEmpleado').reset();
																
								Ext.getCmp('comboCiudad').setDisabled(false);								
								Ext.getCmp('comboDepartamento').setDisabled(true);
								Ext.getCmp('comboEmpleado').setDisabled(true);
								
								presentarCiudades(combo.getValue());
                                validarPresentarCamposContactoCliente(combo.getValue(),idTareaSeleccionada);
							}
						},
						forceSelection: true
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
						emptyText: '' ,
						disabled: true,
						listeners: {
							select: function(combo){															
								Ext.getCmp('comboDepartamento').reset();
								Ext.getCmp('comboEmpleado').reset();
																								
								Ext.getCmp('comboDepartamento').setDisabled(false);
								Ext.getCmp('comboEmpleado').setDisabled(true);
								
								empresa = Ext.getCmp('comboEmpresa').getValue();
								
								presentarDepartamentosPorCiudad(combo.getValue(),empresa);
							}
						},
						forceSelection: true
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
						minChars: 3,
						emptyText: '',
						disabled: true,
						listeners: {
                            afterRender: function(combo) {
                                if(typeof strPrefijoEmpresaSession !== 'undefined' && strPrefijoEmpresaSession.trim())
                                {
                                    storeEmpresas.load(function() {
                                        isFirstLoad = true;
                                        Ext.getCmp('comboEmpresa').setValue(strPrefijoEmpresaSession);
                                        storeCiudades.proxy.extraParams = { empresa:strPrefijoEmpresaSession };
                                        storeCiudades.load(function() {
                                            Ext.getCmp('comboCiudad').setDisabled(false);
                                            if(typeof strIdCantonUsrSession !== 'undefined' && strIdCantonUsrSession.trim())
                                            {
                                                Ext.getCmp('comboCiudad').setValue(Number(strIdCantonUsrSession));
                                                storeDepartamentosCiudad.proxy.extraParams = { id_canton:   strIdCantonUsrSession,
                                                                                               empresa  :   strPrefijoEmpresaSession};
                                                storeDepartamentosCiudad.load(function() {
                                                    Ext.getCmp('comboDepartamento').setDisabled(false);
                                                    combo.setValue(Number(strIdDepartamentoUsrSession));
                                                    presentarEmpleadosXDepartamentoCiudad(strIdDepartamentoUsrSession, 
                                                                                          strIdCantonUsrSession, 
                                                                                          strPrefijoEmpresaSession, 
                                                                                          valorIdDepartamento,
                                                                                          'no');
                                                    presentarCuadrillasXDepartamento(strIdDepartamentoUsrSession);
                                                    presentarContratistas();
                                                    elWinAgregarAsignacionTarea.unmask();
                                                });
                                            }
                                            else
                                            {
                                                elWinAgregarAsignacionTarea.unmask();
                                            }
                                        });
                                        validarPresentarCamposContactoCliente(strPrefijoEmpresaSession,idTareaSeleccionada);
                                    });
                                }
                                else
                                {
                                    elWinAgregarAsignacionTarea.unmask();
                                }
                            },
							select: function(combo){							
								
								
                                Ext.getCmp('comboEmpleado').reset();     
                                Ext.getCmp('comboEmpleado').setDisabled(true);
                                Ext.getCmp('comboCuadrilla').value = "";
                                Ext.getCmp('comboCuadrilla').setRawValue("");
                                Ext.getCmp('comboCuadrilla').setDisabled(true);
                                Ext.getCmp('comboContratista').value = "";
                                Ext.getCmp('comboContratista').setRawValue("");
                                Ext.getCmp('comboContratista').setDisabled(true);
                                empresa = Ext.getCmp('comboEmpresa').getValue();
                                canton  = Ext.getCmp('comboCiudad').getValue();                              
                                presentarEmpleadosXDepartamentoCiudad(combo.getValue(), canton, empresa, valorIdDepartamento,'no');
                                presentarCuadrillasXDepartamento(Ext.getCmp('comboDepartamento').getValue());
                                presentarContratistas();
                            }
                        },
                        forceSelection: true
					}, 
                    RadiosTiposResponsable,                       
                    combo_empleados,	
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Cuadrilla',
                        id: 'comboCuadrilla',
                        name: 'comboCuadrilla',
                        store: storeCuadrillas,
                        displayField: 'nombre',
                        valueField: 'idCuadrilla',
                        queryMode: "remote",
                        emptyText: '',
                        disabled: true,
                        listeners: {
                            select: function(combo){							
                                Ext.getCmp('comboEmpleado').value = "";
                                Ext.getCmp('comboEmpleado').setRawValue("");   
                                validarTabletPorCuadrilla(combo.getValue());
                            }
                        }                          

                    },
                    {
                        xtype       : 'combobox',
                        fieldLabel  : 'Contratista',
                        id          : 'comboContratista',
                        name        : 'comboContratista',
                        store       : storeContratista,
                        displayField: 'nombre_empresa_externa',
                        valueField  : 'id_empresa_externa',
                        queryMode   : "remote",
                        emptyText   : '',
                        disabled    : true,
                        listeners: {
                            select: function(){
                                Ext.getCmp('comboEmpleado').value = "";
                                Ext.getCmp('comboEmpleado').setRawValue("");
                                Ext.getCmp('comboCuadrilla').value = "";
                                Ext.getCmp('comboCuadrilla').setRawValue("");
                            }
                        }

                    },
                    {
						xtype: 'datefield',
						fieldLabel: 'Fecha de Ejecucion:',
						id: 'fecha_ejecucion',
						name:'fecha_ejecucion',
						editable: false,
						format: 'Y-m-d',
						value:fechaActual,
						minValue: fechaActual
					},
					{
						xtype: 'timefield',
						fieldLabel: 'Hora de Ejecucion:',
						format: 'H:i',
						id: 'hora_ejecucion',
						name: 'hora_ejecucion',
						minValue: '00:01',
						maxValue: '23:59',
						increment: 1,						
						editable: true,
						value:horaActual
					},
                    {
                      xtype: 'textarea',
                      id: 'observacionAsignacion',
                      fieldLabel: 'Observacion',
                      name: 'observacion',
                      rows: 3,
                      allowBlank: false
                    },
					{
                      xtype: 'textfield',
                      id: 'asuntoAsignacion',
                      fieldLabel: 'Asunto del correo',
                      name: 'asunto',
                      width: 'auto',
                      allowBlank: true
                    },
                    FieldsDatosContacto
				]
			}
		],
		buttons: 
		[
			{
                text: 'Guardar',
                formBind: true,
                handler: function(){
                    
                    if(Ext.getCmp('comboEmpresa').getValue() != null && Ext.getCmp('comboDepartamento').getValue() != null && 
                        Ext.getCmp('comboCiudad').getValue()  != null )
                     {
                                        
                        if(Ext.getCmp('comboEmpleado') && Ext.getCmp('comboEmpleado').value ||
                          (Ext.getCmp('comboCuadrilla') && Ext.getCmp('comboCuadrilla').value && valorAsignacion == "cuadrilla") ||
                          (Ext.getCmp('comboContratista') && Ext.getCmp('comboContratista').value && valorAsignacion == "contratista"))
                        {		

                           if(valorAsignacion == "empleado")
                           {  
                               var comboEmpleado               = Ext.getCmp('comboEmpleado').value;
                               var valoresComboEmpleado        = comboEmpleado.split("@@"); 
                               var idEmpleado                  = valoresComboEmpleado[0];
                               var idPersonaEmpresaRol         = valoresComboEmpleado[1];                            
                               tarea.data.id_asignado          = valorIdDepartamento;
                               tarea.data.id_refAsignado       = idEmpleado;
                               tarea.data.id_personaEmpresaRol = idPersonaEmpresaRol;	
                               tarea.data.tipo_asignado        = "EMPLEADO";  

                           }
                           else if(valorAsignacion == "cuadrilla"){
                               var idCuadrilla             = Ext.getCmp('comboCuadrilla').value;
                               tarea.data.id_refAsignado   = "0"; 
                               tarea.data.id_asignado      = idCuadrilla;
                               tarea.data.nombre_asignado  = Ext.getCmp('comboCuadrilla').getRawValue();   
                               tarea.data.tipo_asignado    = "CUADRILLA";                            
                           }
                           else{
                                var idContratista          = Ext.getCmp('comboContratista').value;
                                tarea.data.id_refAsignado  = "0";
                                tarea.data.id_asignado     = idContratista;
                                tarea.data.nombre_asignado = Ext.getCmp('comboContratista').getRawValue();   
                                tarea.data.tipo_asignado   = "EMPRESAEXTERNA";
                           }

                           tarea.data.observacion = Ext.getCmp('observacionAsignacion').value;
                           tarea.data.asunto = Ext.getCmp('asuntoAsignacion').value;
                           tarea.data.fechaEjecucion = Ext.getCmp('fecha_ejecucion').value;
                           tarea.data.horaEjecucion = Ext.getCmp('hora_ejecucion').value;
                           var valPrefijo = Ext.getCmp('prefijoNumeroClienteARecibir').value != null ? Ext.getCmp('prefijoNumeroClienteARecibir').value : "";
                            var valNombreClienteARecibir = Ext.getCmp('nombreClienteARecibir').value;
                            var valTelefonoClienteARecibir = Ext.getCmp('telefonoClienteARecibir').value;
                            var valCargoClienteARecibir = Ext.getCmp('cargoClienteARecibir').value;
                            var valCorreoClienteARecibir = Ext.getCmp('correoClienteARecibir').value;
                            var valConvencionalClienteARecibir = Ext.getCmp('convencionalClienteARecibir').getValue() != ''
                            ? valPrefijo + Ext.getCmp('convencionalClienteARecibir').getValue()
                            : '';

                            if (valNombreClienteARecibir != "" || valTelefonoClienteARecibir != "" || valCargoClienteARecibir != "" ||
                                valCorreoClienteARecibir != "" || valConvencionalClienteARecibir != "")
                            {
                                tarea.data.existenDatosContactoCliente = true;
                                tarea.data.nombreClienteARecibir = valNombreClienteARecibir;
                                tarea.data.telefonoClienteARecibir = valTelefonoClienteARecibir;
                                tarea.data.cargoClienteARecibir = valCargoClienteARecibir;
                                tarea.data.correoClienteARecibir = valCorreoClienteARecibir;
                                tarea.data.convencionalClienteARecibir = valConvencionalClienteARecibir;
                            } else {
                                tarea.data.existenDatosContactoCliente = false;
                            }

                           winAgregarAsignacionTarea.destroy();
                       }
                       else
                       {
                            Ext.Msg.alert('Alerta ', 'Por favor escoja un empleado, cuadrilla o contratista');
                       }
                                        
                    } 
                    else
                    {
                        Ext.Msg.alert('Alerta ','Campos incompletos, debe seleccionar Empresa,Ciudad y Departamento');
                    }                      
                    
                }
            },
			{
                text: 'Cancelar',
                handler: function(){
                    tarea.data.existenDatosContactoCliente = false;
                    tarea.data.nombreClienteARecibir = '';
                    tarea.data.telefonoClienteARecibir = '';
                    tarea.data.cargoClienteARecibir = '';
                    tarea.data.correoClienteARecibir = '';
                    tarea.data.convencionalClienteARecibir = '';
                    winAgregarAsignacionTarea.destroy();
                }
            }
		]
	});

    /* ============================================= */
    /* Variables para el envio de sugerencia */
    var idAdmiTarea = "'"+tarea.data.id_tarea+"'";

    var radbuttonHal = '<div align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n\
                        &nbsp;<input type="radio" onchange="opcionesHal(1,'+idAdmiTarea+','+id_caso+','+tarea.data.id_hipotesisTarea+');\n\
                        " value="halDice" name="radioCuadrilla" id="radio_a">&nbsp;\n\
                        Mejor Opci&oacuten&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" \n\
                        onchange="opcionesHal(2,'+idAdmiTarea+','+id_caso+','+tarea.data.id_hipotesisTarea+');" value="halSugiere" \n\
                        name="radioCuadrilla" id="radio_b">&nbsp;Sugerencias&nbsp;&nbsp;&nbsp;&nbsp;</div>';

    var radioButtonAA = '<div align="left" id="divAtenderAntes" style="display:none;">\n\
                            <label><b>¿De existir disponibilidad, el cliente desea ser atendido antes\n\
                                       de la fecha acordada?</b></label>&nbsp;&nbsp;&nbsp;\n\
                            <input type="checkbox" id="cboxAtenderAntes" name="cboxAtenderAntes" >\n\
                        </div>';

    /* Componente para los radio button */
    radioAtenderAntes = Ext.create('Ext.Component',
    {
       html    : radioButtonAA,
       width   : 600,
       padding : 10,
       style   : { color: '#000000' }
    });

    /* Componente para los radio button */
    radiosTiposHal =  Ext.create('Ext.Component',
    {
       html    : radbuttonHal,
       width   : 600,
       padding : 10,
       style   : { color: '#000000' }
    });

    FieldNotificacionHal = new Ext.form.field.Display(
    {
         xtype : 'displayfield',
         id    : 'notificacionHal',
         name  : 'notificacionHal'
    });

    /* Store que obtiene las sugerencias de hal */
    storeIntervalosHal = new Ext.data.Store(
    {
        pageSize : 1000,
        total    : 'total',
        async    : false,
        proxy:
        {
            type : 'ajax',
            url  : url_getIntervalosHal,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'intervalos'
            }
        },
        fields:
        [
            {name: 'idSugerencia'      , mapping: 'idSugerencia'},
            {name: 'fecha'             , mapping: 'fecha'},
            {name: 'horaIni'           , mapping: 'horaIni'},
            {name: 'fechaTexto'        , mapping: 'fechaTexto'},
            {name: 'segTiempoVigencia' , mapping: 'segTiempoVigencia'},
            {name: 'fechaVigencia'     , mapping: 'fechaVigencia'},
            {name: 'horaVigencia'      , mapping: 'horaVigencia'}
        ],
        listeners:
        {
            load: function(sender, node, records)
            {
                if (tipoHal === 2) {
                    Ext.getCmp('nueva_sugerencia').setDisabled(false);
                }

                var boolExiste = (typeof sender.getProxy().getReader().rawData === 'undefined') ? false :
                                 (typeof sender.getProxy().getReader().rawData.mensaje === 'undefined') ? false : true;

                if (boolExiste) {
                    var mensaje = sender.getProxy().getReader().rawData.mensaje;
                    if (mensaje !== null || mensaje !== '') {
                        Ext.getCmp('notificacionHal').setValue(mensaje);
                    } else {
                        Ext.getCmp('notificacionHal').setValue(null);
                    }
                } else {
                    var mensaje = '<b style="color:red";>Error interno, Comunique a Sistemas..!!</b>';
                    Ext.getCmp('notificacionHal').setValue(mensaje);
                }
                formPanelHal.refresh;
            }
        }
    });

    /* Model para la seleccion de las sugerencias  */
    selModelIntervalos = Ext.create('Ext.selection.CheckboxModel',
    {
        mode: 'SINGLE'
    });

    /* Componente para fecha Sugerida por el cliente */
    FieldFechaSugerida = new Ext.form.field.Display(
    {
        xtype      : 'displayfield',
        fieldLabel : 'Fecha Solicitada',
        width      :  90,
        padding    : '6px'
    });

    /* Componente para fecha Sugerida por el cliente */
    DTFechaSugerida = new Ext.form.DateField(
    {
        id       : 'fecha_sugerida',
        name     : 'fecha_sugerida',
        xtype    : 'datefield',
        format   : 'Y-m-d',
        editable : false,
        minValue : fechaActual,
        width    : 120
    });

    /* Componente para la hora Sugerida por el cliente */
    FieldHoraSugerida = new Ext.form.field.Display(
    {
        xtype      : 'displayfield',
        fieldLabel : 'Hora',
        width      : 32,
        padding    : '3px'
    });

    TMHoraSugerida = new Ext.form.TimeField(
    {
        xtype     : 'timefield',
        format    : 'H:i',
        id        : 'hora_sugerida',
        name      : 'hora_sugerida',
        minValue  : '00:00',
        maxValue  : '23:59',
        increment : 15,
        editable  : false,
        width     : 75
    });

    /* Grid de intervalos */
    gridIntervalos = Ext.create('Ext.grid.Panel',
    {
        width       : 650,
        height      : 240,
        collapsible : false,
        title       : 'Sugerencias',
        id          : 'gridIntervalos',
        selModel    : selModelIntervalos,
        store       : storeIntervalosHal,
        loadMask    : true,
        frame       : true,
        forceFit    : true,
        autoRender  : true,
        enableColumnResize :false,
        listeners:{
            itemdblclick: function( view, record, item, index, eventobj, obj ){
                var position = view.getPositionByEvent(eventobj),
                data = record.data,
                value = data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title:'Copiar texto?',
                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    buttons: Ext.Msg.OK,
                    icon: Ext.Msg.INFORMATION
                });
            },
            viewready: function (grid) {
                var view = grid.view;
                grid.mon(view, {
                    uievent: function (type, view, cell, recordIndex, cellIndex, e) {
                        grid.cellIndex = cellIndex;
                        grid.recordIndex = recordIndex;
                    }
                });

                grid.tip = Ext.create('Ext.tip.ToolTip', {
                    target: view.el,
                    delegate: '.x-grid-cell',
                    trackMouse: true,
                    renderTo: Ext.getBody(),
                    listeners: {
                        beforeshow: function updateTipBody(tip) {
                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1) {
                                header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                tip.update(grid.getStore().getAt(grid.recordIndex).get(header.dataIndex));
                            }
                        }
                    }
                });
            }
        },
        dockedItems: [
        {
            xtype : 'toolbar',
            dock  : 'top',
            align : '->',
            items : [
                FieldFechaSugerida,
                DTFechaSugerida,
                '-',
                FieldHoraSugerida,
                TMHoraSugerida,
                { xtype: 'tbfill' },
                {
                    text     : 'Nueva Sugerencia',
                    iconCls  : 'icon_aprobar',
                    disabled : true,
                    itemId   : 'automatica',
                    scope    : this,
                    id       : 'nueva_sugerencia',
                    name     : 'nueva_sugerencia',
                    handler: function()
                    {
                        Ext.getCmp('nueva_sugerencia').setDisabled(true);
                        nIntentos = nIntentos + 1;
                        storeIntervalosHal.getProxy().extraParams.idCaso        = id_caso;
                        storeIntervalosHal.getProxy().extraParams.idHipotesis   = tarea.data.id_hipotesisTarea;
                        storeIntervalosHal.getProxy().extraParams.idAdmiTarea   = tarea.data.id_tarea;
                        storeIntervalosHal.getProxy().extraParams.nIntentos     = nIntentos;
                        storeIntervalosHal.getProxy().extraParams.fechaSugerida = Ext.getCmp('fecha_sugerida').value;
                        storeIntervalosHal.getProxy().extraParams.horaSugerida  = Ext.getCmp('hora_sugerida').value;
                        storeIntervalosHal.getProxy().extraParams.tipoHal       = tipoHal;
                        storeIntervalosHal.load();
                    }
                }
            ]}],
        viewConfig:
        {
            enableTextSelection: true,
            stripeRows: true,
            emptyText: 'Sin datos para mostrar, Por favor leer la Notificación HAL'
        },
        columnLines: true,
        columns:
            [
                {
                    id: 'id_Sugerencia',
                    header: "id_Sugerencia",
                    dataIndex: 'idSugerencia',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'fecha_disponible',
                    header: 'Fecha Disponible',
                    dataIndex: 'fecha',
                    width: 90
                },
                {
                    id: 'horaIni_disponible',
                    header: 'Hora Inicio',
                    dataIndex: 'horaIni',
                    width: 60
                },
                {
                    id: 'fechaTexto',
                    header: 'Mensaje',
                    dataIndex: 'fechaTexto',
                    width: 310
                },
                {
                    id: 'tiempo_reserva',
                    header: 'Reserva (Seg)',
                    dataIndex: 'segTiempoVigencia',
                    width: 80
                },
                {
                    id: 'hora_fin_reserva',
                    header: 'Hora Fin Reserva',
                    dataIndex: 'horaVigencia',
                    width: 130,
                    hidden: true
                },
                {
                    id: 'fecha_reserva',
                    dataIndex: 'fechaVigencia',
                    hidden: true,
                    hideable: false
                }
            ]
    });

    /* Inavilitamos el gid */
    gridIntervalos.setVisible(false);

    // Componente que muestra la tarea seleccionada por el usuario
    tareaSeleccionada = new Ext.form.field.Display({
        xtype      : 'displayfield',
        fieldLabel : '<b>Tarea Seleccionada</b>',
        id         : 'tareaSeleccionada',
        name       : 'tareaSeleccionada',
        value      : (tarea.data.nombre_tarea != null && tarea.data.nombre_tarea != "") ? tarea.data.nombre_tarea : 'No ha seleccionado la tarea'
    });

    /* Grid hal dice */
    gridHalDice = Ext.create('Ext.grid.Panel',
    {
        title: 'Sugerencia de Hal',
        id: 'gridHalDice',
        width: 650,
        height: 100,
        autoRender:true,
        enableColumnResize :false,
        store: storeIntervalosHal,
        listeners:{
            itemdblclick: function( view, record, item, index, eventobj, obj ){
                var position = view.getPositionByEvent(eventobj),
                data = record.data,
                value = data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title:'Copiar texto?',
                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    buttons: Ext.Msg.OK,
                    icon: Ext.Msg.INFORMATION
                });
            },
            viewready: function (grid) {
                var view = grid.view;
                grid.mon(view, {
                    uievent: function (type, view, cell, recordIndex, cellIndex, e) {
                        grid.cellIndex = cellIndex;
                        grid.recordIndex = recordIndex;
                    }
                });
                grid.tip = Ext.create('Ext.tip.ToolTip', {
                    target: view.el,
                    delegate: '.x-grid-cell',
                    trackMouse: true,
                    renderTo: Ext.getBody(),
                    listeners: {
                        beforeshow: function updateTipBody(tip) {
                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1) {
                                header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                tip.update(grid.getStore().getAt(grid.recordIndex).get(header.dataIndex));
                            }
                        }
                    }
                });
            }
        },
        viewConfig:
        {
            enableTextSelection: true,
            stripeRows: true,
            emptyText: 'Sin datos para mostrar, Por favor leer la Notificación HAL'
        },
        loadMask: true,
        frame:true,
        forceFit:true,
        columns:
            [
                {
                    id: 'id_Sugerencia_hal_dice',
                    header: "id_Sugerencia",
                    dataIndex: 'idSugerencia',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'fecha_disponible_hal_dice',
                    header: 'Fecha Disponible',
                    dataIndex: 'fecha',
                    width: 90
                },
                {
                    id: 'horaIni_disponible_hal_dice',
                    header: 'Hora Inicio',
                    dataIndex: 'horaIni',
                    width: 60
                },
                {
                    id: 'fechaTexto_hal_dice',
                    header: 'Mensaje',
                    dataIndex: 'fechaTexto',
                    width: 310
                },
                {
                    id: 'tiempo_reserva_hal_dice',
                    header: 'Reserva (Seg)',
                    dataIndex: 'segTiempoVigencia',
                    width: 80
                },
                {
                    id: 'hora_fin_reserva_hal_dice',
                    header: 'Hora Fin Reserva',
                    dataIndex: 'horaVigencia',
                    width: 130,
                    hidden: true
                },
                {
                    id: 'fecha_reserva_hal_dice',
                    dataIndex: 'fechaVigencia',
                    hidden: true,
                    hideable: false
                }
            ]
    });

    gridHalDice.setVisible(false);

    /* Panel principal para la comunicacion con hal */
    formPanelHal = Ext.create('Ext.form.Panel',
    {
        title: "HAL",
        bodyPadding: 5,
        waitMsgTarget: true,
        fieldDefaults:
        {
                labelAlign: 'left',
                labelWidth: 200,
                msgTarget: 'side'
        },
        items:
        [
            {
                xtype : 'fieldset',
                title : '&nbsp;<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b style="color:blue";>Notificación HAL</b>',
                items :
                [
                    FieldNotificacionHal
                ]
            },
            {
                xtype : 'fieldset',
                title : '&nbsp;<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Asignación de Tareas HAL</b>',
                items :
                [
                   tareaSeleccionada,
                   radiosTiposHal,
                   gridHalDice,
                   gridIntervalos,
                   radioAtenderAntes
                ]
            },
            empresa == 'TN' ? FieldsDatosContactoHal : {}
        ],
        buttons:
        [
            {
                text: 'Guardar',
                formBind: true,
                handler: function()
                {
                    if (tarea.data.id_tarea == null || tarea.data.id_tarea == "" || tarea.data.id_tarea == " ")
                    {
                        Ext.Msg.alert("Alerta","Por Favor seleccionar una tarea...!!");
                        return;
                    }

                    var atenderAntes = "N";

                    if(document.getElementById('cboxAtenderAntes').checked)
                    {
                        atenderAntes = "S";
                    }

                    if (!seleccionaHal)
                    {
                        Ext.Msg.alert("Alerta","Debe escoger una opción de Hal...!!");
                        return;
                    }

                    if (tipoHal == 1)
                    {
                        if (gridHalDice.getStore().data.items.length < 1)
                        {
                            Ext.Msg.alert("Alerta","No se obtuvieron sugerencias de hal...!!");
                            return;
                        }

                        for (var i = 0; i < gridHalDice.getStore().data.items.length; ++i)
                        {
                            tarea.data.idSugerencia   = gridHalDice.getStore().data.items[i].data.idSugerencia;
                            tarea.data.fechaEjecucion = gridHalDice.getStore().data.items[i].data.fecha;
                            tarea.data.horaEjecucion  = gridHalDice.getStore().data.items[i].data.horaIni;
                            tarea.data.fechaVigencia  = gridHalDice.getStore().data.items[i].data.fechaVigencia;
                        }
                    }
                    else
                    {
                       if (selModelIntervalos.getSelection().length < 1)
                       {
                           Ext.Msg.alert("Alerta","Debe escoger una fecha...!!");
                           return;
                       }

                       for (var i = 0; i < selModelIntervalos.getSelection().length; ++i)
                       {
                           tarea.data.idSugerencia   = selModelIntervalos.getSelection()[i].data.idSugerencia;
                           tarea.data.fechaEjecucion = selModelIntervalos.getSelection()[i].data.fecha;
                           tarea.data.horaEjecucion  = selModelIntervalos.getSelection()[i].data.horaIni;
                           tarea.data.fechaVigencia  = selModelIntervalos.getSelection()[i].data.fechaVigencia;
                       }
                    }

                    tarea.data.id_asignado    = 0.1;
                    tarea.data.tipo_operacion = "AUTOMATICA";
                    tarea.data.tipo_hal       = tipoHal;
                    tarea.data.atenderAntes   = atenderAntes;

                    Ext.MessageBox.wait("Verificando datos...");
                    Ext.Ajax.request(
                    {
                        url    :  url_confirmarReservaHal,
                        method : 'post',
                        params :
                        {
                            idCaso        : id_caso,
                            idHipotesis   : tarea.data.id_hipotesisTarea,
                            idAdmiTarea   : tarea.data.id_tarea,
                            idSugerencia  : tarea.data.idSugerencia,
                            fechaVigencia : tarea.data.fechaVigencia
                        },
                        success: function(response)
                        {
                            var responseJson = Ext.JSON.decode(response.responseText);

                            if (responseJson.success)
                            {
                                // Obtenemos la informacion importante del response del controlador
                                tarea.data.segTiempoVigencia   = responseJson.segTiempoVigencia;
                                tarea.data.fechaTiempoVigencia = responseJson.fechaTiempoVigencia;
                                tarea.data.horaTiempoVigencia  = responseJson.horaTiempoVigencia;
                                tarea.data.edit_hal            = 'NO';
                                gridTareas.getView().refresh();
                                Ext.Msg.alert('Mensaje', responseJson.mensaje, function(btn)
                                {
                                    if (btn == 'ok')
                                    {
                                        winAgregarAsignacionTarea.destroy();
                                    }
                                });
                            }
                            else
                            {
                                Ext.Msg.alert('Alert', responseJson.mensaje);

                                if (responseJson.noDisponible)
                                {
                                    eliminarSeleccionHal(selModelIntervalos,gridHalDice,tipoHal);
                                }
                            }
                        },
                        failure: function(rec, op)
                        {
                            var responseJson = Ext.JSON.decode(op.response.responseText);
                            Ext.Msg.alert('Alerta ', responseJson.mensaje);
                            return;
                        }
                    });
                }
            },
            {
                text: 'Cancelar',
                handler: function()
                {
                    tarea.data.existenDatosContactoCliente = false;
                    tarea.data.nombreClienteARecibir = '';
                    tarea.data.telefonoClienteARecibir = '';
                    tarea.data.cargoClienteARecibir = '';
                    tarea.data.correoClienteARecibir = '';
                    tarea.data.convencionalClienteARecibir = '';
                    nIntentos     = 0;
                    seleccionaHal = false;
                    Ext.getCmp('fecha_sugerida').setValue(null);
                    Ext.getCmp('hora_sugerida').setValue(null);
                    winAgregarAsignacionTarea.destroy();
                }
            }
        ]
    });

    var tabs = new Ext.TabPanel({
        xtype     :'tabpanel',
        activeTab : 0,
        autoScroll: false,
        layoutOnTabChange: true,
        items: [formPanel,formPanelHal]
    });

    winAgregarAsignacionTarea = Ext.create('Ext.window.Window',
    {
        title: 'Asignar Tarea',
        modal: true,
        closable: false,
        width: 700,
        layout: 'fit',
        items: (boolPermisoAsignarTareaHal ? [tabs] : [formPanel])
    }).show();

    elWinAgregarAsignacionTarea = winAgregarAsignacionTarea.getEl();
    elWinAgregarAsignacionTarea.mask('Cargando...');
}



function administrarTareas(id_caso,numero,fecha,hora,version_inicial){
    
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
            text: 'Guardar',
            cls: 'x-btn-rigth',
            handler: function() {
                json_tareas = obtenerTareas();
                
                conn.request({
                    method: 'POST',
                    params :{
                        id_caso: id_caso,
                        tareas: json_tareas
                    },
                    url: '../actualizarTareas',
                    success: function(response){
                        Ext.Msg.alert('Mensaje','Se actualizaron las tareas.', function(btn){
                            if(btn=='ok'){
								winAdministrarTareas.destroy();									
								document.location.reload(true);
                            }
                        });
                    },
                    failure: function(rec, op) {
                        var json = Ext.JSON.decode(op.response.responseText);
                        Ext.Msg.alert('Alerta ',json.mensaje);
                    }
            });
            }
    });
    btncancelar = Ext.create('Ext.Button', {
            text: 'Cerrar',
            cls: 'x-btn-rigth',
            handler: function() {
                winAdministrarTareas.destroy();									
				document.location.reload(true);
            }
    });
    
				
	Ext.define('Tarea', {
		extend: 'Ext.data.Model',
		fields: [
			{name:'id_sintomaTarea', type:'string'},
			{name:'nombre_sintomaTarea', type:'string'},
			{name:'id_hipotesisTarea', type:'string'},
			{name:'nombre_hipotesisTarea', type:'string'},
			{name:'id_tarea', type:'string'},
			{name:'nombre_tarea', type:'string'},			
			{name:'idTipoElemento', type:'idTipoElemento'},
			{name:'nombreTipoElemento', type:'nombreTipoElemento'},
			{name:'fechaEjecucion', mapping:'fechaEjecucion'},
			{name:'horaEjecucion', mapping:'horaEjecucion'},
			{name:'idTipo', type:'string'},
			{name:'nombreTipo', type:'string'},
			{name:'id_asignado', type:'string'},
			{name:'id_refAsignado', type:'string'},
			{name:'observacion', type:'string'},
			{name:'tareasAbiertas', mapping:'tareasAbiertas'},
			{name:'estado', type:'string'},
			{name:'action1', type:'string'},
			{name:'action2', type:'string'}

		]
	});
	storeTareas = new Ext.data.Store({ 
		pageSize: 1000,
		total: 'total',
		autoLoad: true,
		proxy: {
			type: 'ajax',
			url : '../getTareasXCaso',
			reader: {
				type: 'json',
				totalProperty: 'total',
				root: 'encontrados'
			},
			extraParams: {
				id: id_caso,
				nombre: '',
				estado: 'Activos'
			}
		},
		fields:
				  [
                    {name:'id_sintomaTarea', mapping:'id_sintomaTarea'},
                    {name:'nombre_sintomaTarea', mapping:'nombre_sintomaTarea'},
                    {name:'id_hipotesisTarea', mapping:'id_hipotesisTarea'},
                    {name:'nombre_hipotesisTarea', mapping:'nombre_hipotesisTarea'},
                    {name:'id_tarea', mapping:'id_tarea'},
                    {name:'mostrarCoordenadas', mapping:'mostrarCoordenadas'},
                    {name:'clientes', mapping:'clientes'},
                    {name:'nombre_tarea', mapping:'nombre_tarea'},
                    {name:'idTipoElemento', mapping:'idTipoElemento'},
                    {name:'nombreTipoElemento', mapping:'nombreTipoElemento'},				  
                    {name:'fechaEjecucion', mapping:'fechaEjecucion'},
                    {name:'horaEjecucion', mapping:'horaEjecucion'},				  
                    {name:'fechaEjecucionTotal', mapping:'fechaEjecucionTotal'},
                    {name:'idTipo', mapping:'idTipo'},
                    {name:'nombreTipo', mapping:'nombreTipo'},
                    {name:'id_asignado', mapping:'id_asignado'},
                    {name:'id_refAsignado', mapping:'id_refAsignado'},
                    {name:'tipoAsignado', mapping: 'tipoAsignado'},    
                    {name:'id_cuadrilla', mapping: 'id_cuadrilla'},                    
                    {name:'observacion', mapping:'observacion'},
                    {name:'casoPerteneceTN', mapping:'casoPerteneceTN'},
                    {name:'tareasManga', mapping:'tareasManga'},
                    {name:'tareasAbiertas', mapping:'tareasAbiertas'},
                    {name:'numero_tareas', mapping:'numero_tareas'},
                    {name:'id_caso', mapping:'id_caso'},
                    {name:'estado', mapping:'estado'},
                    {name:'action0', mapping:'action0'},
                    {name:'action1', mapping:'action1'},
                    {name:'action2', mapping:'action2'},
                    {name:'action3', mapping:'action3'},
                    {name:'action4', mapping:'action4'},
                    {name:'action5', mapping:'action5'},
                    {name:'action8', mapping:'action8'},
                    {name:'tareaEsHal', mapping:'tareaEsHal'},
                    {name:'esHal', mapping:'esHal'},
                    {name:'atenderAntes', mapping:'atenderAntes'}
				  ]
	});
	cellEditingTareas = Ext.create('Ext.grid.plugin.CellEditing', {
		clicksToEdit: 1,
		listeners: {
			edit: function(){
				gridTareas.getView().refresh();
			}
		}
	});
	selModelTareas = Ext.create('Ext.selection.CheckboxModel', {
	   listeners: {
			selectionchange: function(sm, selections) {

			}
		}
	})
	gridTareas = Ext.create('Ext.grid.Panel', {
		id:'gridTareas',
		store: storeTareas,
		viewConfig: { enableTextSelection: true, stripeRows:true }, 
		columnLines: true,
		columns: [{
			id: 'id_sintomaTarea',
			header: 'SintomaId',
			dataIndex: 'id_sintomaTarea',
			hidden: true,
			hideable: false
		}, {
			id: 'nombre_sintomaTarea',
			header: 'Sintoma',
			dataIndex: 'nombre_sintomaTarea',
			width: 180
		},
		{   
			id: 'id_hipotesisTarea',
			header: 'HipotesisId',
			dataIndex: 'id_hipotesisTarea',
			hidden: true,
			hideable: false
		}, {
			id: 'nombre_hipotesisTarea',
			header: 'Hipotesis',
			dataIndex: 'nombre_hipotesisTarea',
			width: 180,
			sortable: true
		},
		{   
			id: 'id_tarea',
			header: 'HipotesisId',
			dataIndex: 'id_tarea',
			hidden: true,
			hideable: false
		}, {
			id: 'nombre_tarea',
			header: 'Tarea',
			dataIndex: 'nombre_tarea',
			width: 180,
			sortable: true
		},
		{   
			id: 'idTipoElemento',
			header: 'idTipoElemento',
			dataIndex: 'idTipoElemento',
			hidden: true,
			hideable: false
		},
		{   
			id: 'nombreTipoElemento',
			header: 'Tipo',
			dataIndex: 'nombreTipoElemento',
			width: 50,
			sortable: true
		},
		{
			id: 'nombreTipo',
			header: 'Elemento/Tramo',
			dataIndex: 'nombreTipo',
			width: 110,
			sortable: true
		},
		{
			id: 'fechaEjecucionTotal',
			header: 'Fecha Ejecucion',
			dataIndex: 'fechaEjecucionTotal',
			width: 100,
			sortable: true
		},	
		{   
			id: 'idTipo',
			header: 'idTipo',
			dataIndex: 'idTipo',
			hidden: true,
			hideable: false
		},{
			id: 'estado',
			header: 'Estado',
			dataIndex: 'estado',
			width: 70,
			sortable: true
		},{
                        id        : 'esHal',
                        dataIndex : 'esHal',
                        header    : 'Es Hal',
                        width     :  70,
                        sortable  :  true,
                        hideable  :  false
                },{
                        id        : 'atenderAntes',
                        dataIndex : 'atenderAntes',
                        header    : 'Atender Antes',
                        width     :  100,
                        sortable  :  true,
                        hideable  :  false
                },{
                        id        : 'tareaEsHal',
                        dataIndex : 'tareaEsHal',
                        hidden    :  true,
                        hideable  :  false
                },{
			header: 'Acciones',
			xtype: 'actioncolumn',
			width:140,
			sortable: false,
			items: 
			[
				{
					getClass: function(v, meta, rec) {
						var permiso = '{{ is_granted("ROLE_78-50") }}';
						var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);							
						if(!boolPermiso){ rec.data.action0 = "icon-invisible"; }
							
						if (rec.get('action0') == "icon-invisible") 
							this.items[0].tooltip = '';
						else 
							this.items[0].tooltip = 'Ver Asignado';
						
						return rec.get('action0');
					},
					handler: function(grid, rowIndex, colIndex) {
						var rec = storeTareas.getAt(rowIndex);
							
						var permiso = '{{ is_granted("ROLE_78-50") }}';
						var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
						if(!boolPermiso){ rec.data.action0 = "icon-invisible"; }
							
						if(rec.get('action0')!="icon-invisible")
							verAsignadoTarea(id_caso,numero,grid.getStore().getAt(rowIndex).data, 'AdminTareas'); 
						else
							Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
					}
				},
				{
					getClass: function(v, meta, rec) {
						var permiso = '{{ is_granted("ROLE_78-157") }}';
						var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);							
						if(!boolPermiso){ rec.data.action1 = "icon-invisible"; }
							
						if (rec.get('action1') == "icon-invisible") 
							this.items[1].tooltip = '';
						else 
							this.items[1].tooltip = 'Ingresar Seguimiento';
						
						return rec.get('action1');
					},
					handler: function(grid, rowIndex, colIndex) {
						var rec = storeTareas.getAt(rowIndex);
							
						var permiso = '{{ is_granted("ROLE_78-157") }}';
						var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
						if(!boolPermiso){ rec.data.action1 = "icon-invisible"; }
							
						if(rec.get('action1')!="icon-invisible")
							agregarSeguimiento(id_caso,numero,grid.getStore().getAt(rowIndex).data); 
						else
							Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
					}
				},
                        {
                            getClass: function(v, meta, rec) {
                                var permiso = '{{ is_granted("ROLE_78-38") }}';
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
                                if (!boolPermiso) {
                                    rec.data.action2 = "icon-invisible";
                                }

                                if (rec.get('action2') == "icon-invisible")
                                    this.items[2].tooltip = '';
                                else
                                    this.items[2].tooltip = 'Finalizar Tarea';

                                return rec.get('action2');
                            },
                            handler: function(grid, rowIndex, colIndex) 
                            {
                                var rec = storeTareas.getAt(rowIndex);

                                var permiso = '{{ is_granted("ROLE_78-38") }}';
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
                                
                                if (!boolPermiso) 
                                {
                                    rec.data.action2 = "icon-invisible";
                                }

                                if (rec.get('action2') !== "icon-invisible")
                                {
                                    //Obtener la fecha y hora del servidor por cada instante en que requieran finalizar la tarea                                        
                                    conn.request({
                                        method: 'POST',
                                        url: url_obtenerFechaServer,
                                        success: function(response)
                                        {
                                            var json = Ext.JSON.decode(response.responseText);                                                                                        

                                            if (json.success)
                                            {                                                
                                                var fechaFinArray = json.fechaActual.split("-");
                                                var fechaActual   = fechaFinArray[2]+"-"+fechaFinArray[1]+"-"+fechaFinArray[0];
                                                
                                                rs = validarFechaTareaReprogramada(grid.getStore().getAt(rowIndex).data.fechaEjecucion, 
                                                                                   grid.getStore().getAt(rowIndex).data.horaEjecucion,
                                                                                   fechaActual, json.horaActual);

                                                if (rs !== -1)
                                                {
                                                    finalizarTarea(id_caso, numero, grid.getStore().getAt(rowIndex).data, fechaActual, 
                                                    json.horaActual,rec.data.tipoAsignado,rec.data.id_cuadrilla);
                                                }
                                            }
                                            else
                                            {
                                                Ext.Msg.alert('Alerta ', json.error);
                                            }
                                        }
                                    });
                                }
                                else
                                {
                                    Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                                }
                            }
                        },
                        {
					getClass: function(v, meta, rec) {
						var permiso = '{{ is_granted("ROLE_78-156") }}';
						var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);							
						if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }
							
						if (rec.get('action3') == "icon-invisible") 
							this.items[3].tooltip = '';
						else
                        {
                            if (rec.get('action8') == "button-grid-rechazarTarea")
                            {
                                this.items[3].tooltip = 'Aceptar/Anular Tarea';
                            }
                            else
                            {
                                this.items[3].tooltip = 'Aceptar/Rechazar Tarea';
                            }
                        }
						return rec.get('action3');
					},
					handler: function(grid, rowIndex, colIndex) {
						var rec = storeTareas.getAt(rowIndex);
							
						var permiso = '{{ is_granted("ROLE_78-156") }}';
						var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
						if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }
							
						if(rec.get('action3')!="icon-invisible")
							aceptarRechazarTarea(grid.getStore().getAt(rowIndex).data, id_caso); 
						else
							Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
					}
				},
				//REPROGRAMACION EN TAREAS CASOS
				{
					getClass: function(v, meta, rec) 
                    {
						var permiso = '{{ is_granted("ROLE_78-156") }}';
						var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);							
					
                        if(!boolPermiso){ rec.data.action3 = "icon-invisible"; }
							
						if (rec.get('action4') == "icon-invisible") 
							this.items[4].tooltip = '';
						else 
							this.items[4].tooltip = 'Reprogramar Tarea';
						
						return rec.get('action4');
					},
					handler: function(grid, rowIndex, colIndex) 
                    {
						var rec = storeTareas.getAt(rowIndex);
							
						var permiso = '{{ is_granted("ROLE_78-156") }}';
						var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
					
                        if(!boolPermiso){ rec.data.action4 = "icon-invisible"; }
							
						if(rec.get('action4')!=="icon-invisible")
                        {
							conn.request({
                                method: 'POST',                                                                                                                                                                          
                                url:url_obtenerFechaServer,
                                success: function(response) 
                                {
                                    var json = Ext.JSON.decode(response.responseText);                                                                                                

                                    if (json.success)
                                    {
                                        reprogramarTarea(rec.data.id_sintomaTarea, rec.data, json.fechaActual, json.horaActual); 
                                    }
                                    else
                                    {
                                        Ext.Msg.alert('Alerta ', json.error);
                                    }
                                }
                            });  
							
                        }
                        else
                        {
							Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
                        }
					}
				},
				{
					getClass: function(v, meta, rec) {
						var permiso = '{{ is_granted("ROLE_78-156") }}';
						var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);							
						if(!boolPermiso){ rec.data.action5 = "icon-invisible"; }
							
						if (rec.get('action5') == "icon-invisible") 
							this.items[5].tooltip = '';
						else 
							this.items[5].tooltip = 'Cancelar Tareas';
						
						return rec.get('action5');
					},
					handler: function(grid, rowIndex, colIndex) {
						var rec = storeTareas.getAt(rowIndex);
							
						var permiso = '{{ is_granted("ROLE_78-156") }}';
						var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
						if(!boolPermiso){ rec.data.action5 = "icon-invisible"; }
							
						if(rec.get('action5')!="icon-invisible")							
							cancelarTarea(rec.data.id_sintomaTarea, rec.data,'cancelada'); 							
						else
							Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
					}
				},
			]
		},
		{   
			id: 'id_asignado',
			header: 'id_asignado',
			dataIndex: 'id_asignado',
			hidden: true,
			hideable: false
		},
		{   
			id: 'id_refAsignado',
			header: 'id_refAsignado',
			dataIndex: 'id_refAsignado',
			hidden: true,
			hideable: false
		},
		{   
			id: 'observacion',
			header: 'observacion',
			dataIndex: 'observacion',
			hidden: true,
			hideable: false
		},
		{   
			id: 'asunto',
			header: 'asunto',
			dataIndex: 'asunto',
			hidden: true,
			hideable: false
		}],
		selModel: selModelTareas,
		width: 1010,
		height: 370,
		frame: true,
		plugins: [cellEditingTareas],
		title: 'Tareas Agregadas'
	});
	formPanel = Ext.create('Ext.form.Panel', {
			bodyPadding: 5,
			waitMsgTarget: true,
			height: 200,
			layout: 'fit',
			fieldDefaults: {
				labelAlign: 'left',
				labelWidth: 140,
				msgTarget: 'side'
			},

			items: [{
				xtype: 'fieldset',
				title: 'Información del Caso',
				defaultType: 'textfield',
				items: [
					{
						xtype: 'displayfield',
						fieldLabel: 'Caso:',
						id: 'numero_casoSintoma',
						name: 'numero_casoSintoma',
						value: numero
					},
					{
						xtype: 'displayfield',
						fieldLabel: 'Fecha apertura:',
						id: 'fechacaso',
						name: 'fechaCaso',
						value: fecha+" "+hora
					},
					{
						xtype: 'textarea',
						fieldLabel: 'Version Inicial:',
						id: 'version_inicialSintoma',
						name: 'version_inicialSintoma',
						rows: 3,
						cols: 100,
						readOnly: true,
						value: version_inicial
					},
					gridTareas
				]
			}]
		 });
	winAdministrarTareas = Ext.create('Ext.window.Window', {
			title: 'Administrar Tareas',
			modal: true,
			width: 1060,
			height: 620,
			resizable: false,
			layout: 'fit',
			items: [formPanel],
			buttonAlign: 'center',
			buttons:[btncancelar]
	}).show(); 

}

function verSeguimientoTarea(data){
  
  
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
   
    btncancelar = Ext.create('Ext.Button', {
            text: 'Cerrar',
            cls: 'x-btn-rigth',
            handler: function() {
		      winSeguimientoTarea.destroy();													
            }
    });
    
	storeSeguimientoTarea = new Ext.data.Store({ 
		//pageSize: 1000,
		total: 'total',
		autoLoad: true,
		proxy: {
			type: 'ajax',
			url : '../verSeguimientoTarea',
			reader: {
				type: 'json',
				totalProperty: 'total',
				root: 'encontrados'
			},
			extraParams: {
				id_detalle: data.id				
			}
		},
		fields:
		[
		      {name:'id_detalle', mapping:'id_detalle'},
		      {name:'observacion', mapping:'observacion'},
		      {name:'departamento', mapping:'departamento'},
		      {name:'empleado', mapping:'empleado'},
		      {name:'fecha', mapping:'fecha'}					
		]
	});
	gridSeguimiento = Ext.create('Ext.grid.Panel', {
		id:'gridSeguimiento',
		store: storeSeguimientoTarea,		
		columnLines: true,
		columns: [
			{
			      id: 'observacion',
			      header: 'Observación',
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
			      header: 'Fecha Observación',
			      dataIndex: 'fecha',
			      width:120,
			      sortable: true						 
			}
		],		
		width: 700,
		height: 300,
		listeners:{
                            itemdblclick: function( view, record, item, index, eventobj, obj ){
                                var position = view.getPositionByEvent(eventobj),
                                data = record.data,
                                value = data[this.columns[position.column].dataIndex];
                                Ext.Msg.show({
                                    title:'Copiar texto?',
                                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                                    buttons: Ext.Msg.OK,
                                    icon: Ext.Msg.INFORMATION
                                });
                            },
                            viewready: function (grid) {
                                var view = grid.view;

                                // record the current cellIndex
                                grid.mon(view, {
                                    uievent: function (type, view, cell, recordIndex, cellIndex, e) {
                                        grid.cellIndex = cellIndex;
                                        grid.recordIndex = recordIndex;
                                    }
                                });

                                grid.tip = Ext.create('Ext.tip.ToolTip', {
                                    target: view.el,
                                    delegate: '.x-grid-cell',
                                    trackMouse: true,
                                    renderTo: Ext.getBody(),
                                    listeners: {
                                        beforeshow: function updateTipBody(tip) {
                                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1) {
                                                header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                                tip.update(grid.getStore().getAt(grid.recordIndex).get(header.dataIndex));
                                            }
                                        }
                                    }
                                });

                            }                                    
                    }
	});
	formPanelSeguimiento = Ext.create('Ext.form.Panel', {
			bodyPadding: 5,
			waitMsgTarget: true,
			height: 300,
			width:700,
			layout: 'fit',
			fieldDefaults: {
				labelAlign: 'left',
				//labelWidth: 140,
				msgTarget: 'side'
			},

			items: [{
				xtype: 'fieldset',				
				defaultType: 'textfield',
				items: [					
					gridSeguimiento
				]
			}]
		 });
	winSeguimientoTarea = Ext.create('Ext.window.Window', {
			title: 'Seguimiento Tareas',
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

function verAsignadoTarea(id_caso,numero,data, origen){
    winAsignadoTarea = "";
	var formPanel2 = "";
	
    if (winAsignadoTarea)
    {
		cierraVentanaByIden(winAsignadoTarea);
		winAsignadoTarea = "";
	}
	
    if (!winAsignadoTarea)
    {     
	    btncancelar2 = Ext.create('Ext.Button', {
	            text: 'Cerrar',
	            cls: 'x-btn-rigth',
	            handler: function() {
					cierraVentanaByIden(winAsignadoTarea);
	            }
	    });
		
		var id_detalle = '';
		if(origen == 'global') id_detalle = data.id;
		else id_detalle = data.id_sintomaTarea;

	    storeAsignadoTarea = new Ext.data.Store({ 
	        total: 'total',
	        autoLoad:true,
	        proxy: {
	            type: 'ajax',
	            url : '../getTareaAsignado',
	            reader: {
	                type: 'json',
	                totalProperty: 'total',
	                root: 'encontrados'
	            },
	            extraParams: {
	                id_detalle: id_detalle
	            }
	        },
	        fields:
	              [
	                {name:'oficina', mapping:'oficina'},
	                {name:'area', mapping:'area'},
	                {name:'departamento', mapping:'departamento'},
	                {name:'empleado', mapping:'empleado'},
	                {name:'tipoAsignado', mapping:'tipoAsignado'}                    
	              ],
	        listeners: {
				beforeload: function(sender, options )
				{
					Ext.MessageBox.show({
					   msg: 'Cargando los datos, Por favor espere!!',
					   progressText: 'Saving...',
					   width:300,
					   wait:true,
					   waitConfig: {interval:200}
					});
				   
					winAsignadoTarea = "";
					formPanel2 = "";
				},
	            load: function(sender, node, records) {
	                //console.log(storeAsignadoTarea.data.items[0].data.oficina);
	                formPanel2 = Ext.create('Ext.form.Panel', {
	                    bodyPadding: 5,
	                    waitMsgTarget: true,
	                    height: 200,
	                    width: 500,
	                    layout: 'fit',
	                    fieldDefaults: {
	                        labelAlign: 'left',
	                        labelWidth: 140,
	                        msgTarget: 'side'
	                    },

	                    items: [{
	                        xtype: 'fieldset',
	                        title: 'Información de Asignacion',
	                        defaultType: 'textfield',
	                        items: [
	                            {
	                                xtype: 'displayfield',
	                                fieldLabel: 'Oficina:',
	                                id: 'tareaOficina',
	                                name: 'tareaOficina',
	                                value: storeAsignadoTarea.data.items[0].data.oficina
	                            },{
	                                xtype: 'displayfield',
	                                fieldLabel: 'Area:',
	                                id: 'tareaArea',
	                                name: 'tareaArea',
	                                value: storeAsignadoTarea.data.items[0].data.area
	                            },{
	                                xtype: 'displayfield',
	                                fieldLabel: 'Departamento:',
	                                id: 'tareaDepartamento',
	                                name: 'tareaDepartamento',
	                                value: storeAsignadoTarea.data.items[0].data.departamento
	                            },
                                {
                                    xtype: 'displayfield',
                                    fieldLabel: storeAsignadoTarea.data.items[0].data.tipoAsignado,
                                    name: 'tareaEmpleado',
                                    value: storeAsignadoTarea.data.items[0].data.empleado
                                }                              
	                        ]
	                    }]
					});
					
		            winAsignadoTarea = Ext.create('Ext.window.Window', {
		                    title: 'Ver asignado de la tarea',
		                    modal: true,
		                    width: 660,
		                    height: 240,
		                    resizable: false,
		                    layout: 'fit',
							closabled: false,
		                    items: [formPanel2],
		                    buttonAlign: 'center',
		                    buttons:[btncancelar2]
		            }).show(); 
					
					Ext.MessageBox.hide();
	            }
	        }
	    });
    }
}

function agregarSeguimiento(id_caso,numero,data){
    
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
    
    if(data.id)id_detalle = data.id;
    else id_detalle = data.id_sintomaTarea;
    
    btnguardar2 = Ext.create('Ext.Button', {
            text: 'Guardar',
            cls: 'x-btn-rigth',
            handler: function() {
              //  json_tareas = obtenerTareas();
                var valorSeguimiento = Ext.getCmp('seguimiento').value;
                winSeguimiento.destroy();
                conn.request({
                    method: 'POST',
                    params :{
                        id_caso: id_caso,
                        id_detalle: id_detalle,
                        seguimiento: valorSeguimiento
                    },
                    url: '../ingresarSeguimiento',
                    success: function(response){
                        Ext.Msg.alert('Mensaje','Se ingreso el seguimiento.', function(btn){
                            if(btn=='ok'){
                            }
                        });
                    },
                    failure: function(rec, op) {
                        var json = Ext.JSON.decode(op.response.responseText);
                        Ext.Msg.alert('Alerta ',json.mensaje);
                    }
            });
            }
    });
    btncancelar2 = Ext.create('Ext.Button', {
            text: 'Cerrar',
            cls: 'x-btn-rigth',
            handler: function() {
                winSeguimiento.destroy();
            }
    });
    
                        
            
    formPanel2 = Ext.create('Ext.form.Panel', {
            bodyPadding: 5,
            waitMsgTarget: true,
            height: 200,
            width: 500,
            layout: 'fit',
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 140,
                msgTarget: 'side'
            },

            items: [{
                xtype: 'fieldset',
                title: 'Información',
                defaultType: 'textfield',
                items: [
                    {
                        xtype: 'displayfield',
                        fieldLabel: 'Caso:',
                        id: 'seguimientoCaso',
                        name: 'seguimientoCaso',
                        value: numero
                    },{
                        xtype: 'displayfield',
                        fieldLabel: 'Tarea:',
                        id: 'tareaCaso',
                        name: 'tareaCaso',
                        value: data.nombre_tarea
                    },{
                        xtype: 'displayfield',
                        fieldLabel: 'Elemento/Tramo:',
                        id: 'elemento/tramo',
                        name: 'elemento/tramo',
                        value: data.nombreTipo
                    },
                    {
                        xtype: 'textarea',
                        fieldLabel: 'Seguimiento:',
                        id: 'seguimiento',
                        name: 'seguimiento',
                        rows: 7,
                        cols: 70
                    }
                ]
            }]
         });
    winSeguimiento = Ext.create('Ext.window.Window', {
            title: 'Ingresar Seguimiento',
            modal: true,
            width: 660,
            height: 340,
            resizable: false,
            layout: 'fit',
            items: [formPanel2],
            buttonAlign: 'center',
            buttons:[btnguardar2,btncancelar2]
    }).show(); 
    
}

function validarTareasMateriales()
{
	//RONALD .------ VALIDAR AQUI QUE SE HAYA ESCOGIDO UN HIPOTESIS ANTERIOR... ANTES DE CREAR OTRO..
	var storeValida = Ext.getCmp("gridMaterialTareas").getStore();
	var boolSigue = false;
	var boolSigue2 = false;
	
	if(storeValida.getCount() > 0)
	{
		var boolSigue_vacio = true;
		var boolSigue_igual = true;
		
		for(var i = 0; i < storeValida.getCount(); i++)
		{
			var id_material = storeValida.getAt(i).data.id_material;
			var nombre_material = storeValida.getAt(i).data.nombre_material;
			var cod_material = storeValida.getAt(i).data.cod_material;
			
			if(id_material != "" && nombre_material != ""){ /*NADA*/  }
			else {  boolSigue_vacio = false; }

			if(i>0)
			{
				for(var j = 0; j < i; j++)
				{
					if(i != j)
					{
						var id_material_valida = storeValida.getAt(j).data.id_material;
						var nombre_material_valida = storeValida.getAt(j).data.nombre_material;
						var cod_material_valida = storeValida.getAt(j).data.cod_material;
						
						if(id_material_valida == id_material && nombre_material == nombre_material_valida)
						{
							boolSigue_igual = false;	
						}
					}
				}
			}
		} 
		
		if(boolSigue_vacio) { boolSigue = true; }	
		if(boolSigue_igual) { boolSigue2 = true; }						
	}
	else
	{
		boolSigue = true;
		boolSigue2 = true;
	}
	
	if(boolSigue && boolSigue2)
	{
		return true;
	}
	else if(!boolSigue)
	{
		Ext.Msg.alert('Alerta ', "Debe escoger un material del combo, antes de solicitar un nuevo material");
		return false;
	}
	else if(!boolSigue2)
	{
		Ext.Msg.alert('Alerta ', "No puede ingresar el mismo material! Debe modificar el registro repetido, antes de solicitar un nuevo material");
		return false;
	}
	else
	{
		Ext.Msg.alert('Alerta ', "Debe completar datos de los materiales a ingresar, antes de solicitar un nuevo material");
		return false;
	}	
}


function finalizarTarea(id_caso,numero,data,fechaActual,horaActual,tipoAsignado,idCuadrilla)
{
    isCuadrilla = false;
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
    comboTareaStore = new Ext.data.Store({
        pageSize: 200,
        total: 'total',
        proxy: {
            type: 'ajax',            
            url:url_gridTarea,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
                estado: 'Activo',
                visible: 'SI',
                caso: id_caso
            }
        },
        fields:
            [
                {name: 'id_tarea', mapping: 'id_tarea'},
                {name: 'nombre_tarea', mapping: 'nombre_tarea'}
            ]
    });

    comboTarea = Ext.create('Ext.form.ComboBox', {
        id: 'comboTarea',
        store: comboTareaStore,
        displayField: 'nombre_tarea',
        valueField: 'nombre_tarea',
        height: 30,
        border: 0,
        margin: 0,
        fieldLabel: 'Tarea Final ',
        queryMode: "remote",
        emptyText: ''
    });
    Ext.getCmp('comboTarea').setRawValue(data.nombre_tarea);
    btnguardar2 = Ext.create('Ext.Button', {
		text: 'Guardar',
		cls: 'x-btn-rigth',
		handler: function() {	
            
            var valorBool=false;
            if (!data.casoPerteneceTN)
            {
                valorBool = validarTareasMateriales();
            }
            else
            {
                valorBool = true;
            }            

			if(valorBool)
			{
                if (tipoAsignado === 'CUADRILLA')
                {
                    Ext.Msg.confirm('Confirmacion', 'Esta seguro que desea cerrar la tarea con los integrantes de esta cuadrilla ?, caso \n\
                                    contrario notificar para que se actualice los integrantes', function(id) {
                    if (id === 'yes')
                    {                                                    
                        guardarFinalizarTarea(id_caso,data);
                    } 
                    else
                    {
                        winFinalizarTarea.destroy();
                        winAdministrarTareas.destroy();
                    }
                    }, this);

                }
                else
                {
                        guardarFinalizarTarea(id_caso,data);                    
                }                
			}	
		}
    });
    btncancelar2 = Ext.create('Ext.Button', {
            text: 'Cerrar',
            cls: 'x-btn-rigth',
            handler: function() {
                winFinalizarTarea.destroy();                
            }
    });
    
    if (!data.casoPerteneceTN)
    {                        
        Ext.define('Material', {
            extend: 'Ext.data.Model',
            fields: 
            [
                {name:'id_detalle', type:'string'},
                {name:'id_tarea', type:'string'},
                {name:'id_material', type:'string'},
                {name:'cod_material', type:'string'},
                {name:'nombre_tarea', type:'string'},
                {name:'nombre_material', type:'string'},
                {name:'costo', type:'string'},
                {name:'precio_venta_material', type:'string'},
                {name:'cant_nf', type:'string'},
                {name:'cant_f', type:'string'},
                {name:'cant', type:'string'},
                {name:'valor', type:'string'},
                {name:'fin_origen', type:'string'}

            ]
        });

        storeMaterialTareas = new Ext.data.Store({ 
            pageSize: 1000,
            total: 'total',
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url : '../getMaterialesByTarea',
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    nombre: '',
                    id_detalle: data.id_sintomaTarea
                }
            },
            fields:
            [
                {name:'id_detalle', mapping:'id_material'},
                {name:'id_tarea', mapping:'id_tarea'},
                {name:'id_material', mapping:'id_material'},
                {name:'cod_material', mapping:'cod_material'},
                {name:'nombre_tarea', mapping:'nombre_tarea'},
                {name:'nombre_material', mapping:'nombre_material'},
                {name:'costo', mapping:'costo'},
                {name:'precio_venta_material', mapping:'precio_venta_material'},
                {name:'cant_nf', mapping:'cant_nf'},
                {name:'cant_f', mapping:'cant_f'},
                {name:'cant', mapping:'cant'},
                {name:'valor', mapping:'valor'},
                {name:'fin_origen', mapping:'fin_origen'}
            ]
        });

        cellEditingMaterialTareas = Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 1,
            listeners: {
                edit: function(){
                    gridMaterialTareas.getView().refresh();
                }
            }
        });

        selModelMaterialTareas = Ext.create('Ext.selection.CheckboxModel', {
            checkOnly: true,
            listeners: {
                 selectionchange: function(sm, selections) {
                    gridMaterialTareas.down('#removeButton').setDisabled(selections.length == 0);
                 }
             }
        })

        comboMaterialStore = new Ext.data.Store({ 
            total: 'total',
            proxy: {
                type: 'ajax',
                url : '../getMateriales',
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                        nombre: '',
                        estado: 'Activo'
                    }
            },

            fields:
                  [
                    {name:'id_material', mapping:'id_material'},
                    {name:'cod_material', mapping:'cod_material'},
                    {name:'nombre_material', mapping:'nombre_material'},
                    {name:'costo_material', mapping:'costo_material'},
                    {name:'cant_material', mapping:'cant_material'}
                  ]
        });

        comboMaterial = Ext.create('Ext.form.ComboBox', {
            id:'comboMaterial',
            store: comboMaterialStore,
            displayField: 'nombre_material',
            valueField: 'id_material',
            height:30,
            border:0,
            margin:0,
            fieldLabel: false,	
            queryMode: "remote",
            emptyText: ''
        });

        gridMaterialTareas = Ext.create('Ext.grid.Panel', {
            id:'gridMaterialTareas',
            store: storeMaterialTareas,
            viewConfig: { enableTextSelection: true, stripeRows:true }, 
            columnLines: true,
            columns: [
                {
                    id: 'id_material',
                    header: 'id_material',
                    dataIndex: 'id_material',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'precio_venta_material',
                    header: 'precio_venta_material',
                    dataIndex: 'precio_venta_material',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'fin_origen',
                    header: 'fin_origen',
                    dataIndex: 'fin_origen',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'cod_material',
                    header: 'Codido Material',
                    dataIndex: 'cod_material',
                    width: 100
                },
                {
                    id: 'nombre_material',
                    header: 'Nombre Material',
                    dataIndex: 'nombre_material',
                    width: 260,
                    sortable: true,
                    renderer: function (value, metadata, record, rowIndex, colIndex, store){
                        record.data.id_material = record.data.nombre_material;
                        for (var i = 0;i< comboMaterialStore.data.items.length;i++)
                        {
                            if (comboMaterialStore.data.items[i].data.id_material== record.data.id_material)
                            {
                                gridMaterialTareas.getStore().getAt(rowIndex).data.costo = comboMaterialStore.data.items[i].data.costo_material;
                                gridMaterialTareas.getStore().getAt(rowIndex).data.cant = comboMaterialStore.data.items[i].data.cant_material;
                                gridMaterialTareas.getStore().getAt(rowIndex).data.id_material=comboMaterialStore.data.items[i].data.id_material;

                                record.data.id_material = comboMaterialStore.data.items[i].data.id_material;
                                record.data.cod_material = comboMaterialStore.data.items[i].data.cod_material;
                                record.data.nombre_material = comboMaterialStore.data.items[i].data.nombre_material;
                                break;
                            }
                        }

                        //record.commit();
                        //gridMaterialTareas.getView().refresh();

                        return record.data.nombre_material;
                    },
                    editor: {
                        id:'searchMaterial_cmp',
                        xtype: 'combobox',
                        displayField:'nombre_material',
                        valueField: 'id_material',
                        loadingText: 'Buscando ...',
                        store: comboMaterialStore,
                        fieldLabel: false,	
                        queryMode: "remote",
                        emptyText: '',
                        listClass: 'x-combo-list-small'
                    }
                },
                {   
                    id: 'costo',
                    header: 'Costo',
                    width: 60,
                    dataIndex: 'costo',
                    hideable: false
                }, {
                    id: 'cant_nf',
                    header: 'Cant. no Fact.',
                    dataIndex: 'cant_nf',
                    width: 90,
                    sortable: true,
                    editor: {
                        xtype: 'numberfield'
                    }
        //            renderer: function (value, metadata, record, rowIndex, colIndex, store){
        //                alert(gridMaterialTareas.getStore().getAt(rowIndex).data.cant+"-"+record.data.cant_nf);
        //                if(gridMaterialTareas.getStore().getAt(rowIndex).data.cant<record.data.cant_nf){
        //                    Ext.Msg.alert("Alerta","La cantidad ingresada es mayor que la cantidad maxima permitida.");
        //                    return;
        //                }
        //                    
        //                
        //                return record.data.cant_nf;
        //            }
                },
                {   
                    id: 'cant_f',
                    header: 'Cant. Fact.',
                    dataIndex: 'cant_f',
                    width: 80,
                    hideable: false,
                    editor: {
                        xtype: 'numberfield'
                    },
                    renderer: function (value, metadata, record, rowIndex, colIndex, store){
                        gridMaterialTareas.getStore().getAt(rowIndex).data.valor = gridMaterialTareas.getStore().getAt(rowIndex).data.costo*gridMaterialTareas.getStore().getAt(rowIndex).data.cant_f;
        //                if(gridMaterialTareas.getStore().getAt(rowIndex).data.cant==""){
        //                    Ext.Msg.alert("Alerta","Debe escoger primero un material.");
        //                    return;
        //                }
                        return record.data.cant_f;
                    }
                },
                {   
                    id: 'cant',
                    header: 'Cantidad Max. Fact. ',
                    dataIndex: 'cant',            
                    width: 120,
                    hideable: false
                }, {
                    id: 'valor',
                    header: 'Valor',
                    dataIndex: 'valor',
                    width: 70,
                    sortable: true
                }
            ],
            selModel: selModelMaterialTareas,
            dockedItems: [{
                xtype: 'toolbar',
                items: [
                    {
                        itemId: 'removeButton',
                        text:'Eliminar',
                        tooltip:'Elimina el item seleccionado',
                        disabled: true,
                        handler : function(){eliminarSeleccion(gridMaterialTareas, 'gridMaterialTareas', selModelMaterialTareas);}
                    }, '-', 
                    {
                        text:'Agregar',
                        tooltip:'Agrega un item a la lista',
                        handler : function(){
                            var boolValida = validarTareasMateriales();
                            if(boolValida)
                            {
                                // Create a model instance
                                var r = Ext.create('Material', {
                                    id_detalle: '',
                                    id_tarea: '',
                                    id_material: '',
                                    cod_material: '',
                                    nombre_tarea: '',
                                    nombre_material: '',
                                    costo: '',
                                    precio_venta_material: '',
                                    cant_nf: 0,
                                    cant_f: 0,
                                    cant: '',
                                    valor: '',
                                    fin_origen: 'Nuevo'
                                });
                                storeMaterialTareas.insert(0, r);
                            }
                        }
                    }
                ]
            }],
            width: 840,
            height: 350,
            frame: false,
            plugins: [cellEditingMaterialTareas],
            title: 'Materiales'
        });      
    
    
    }
    else
    {
        gridMaterialTareas=null;
    }
    

    gridCuadrilla = null;
    if (tipoAsignado === 'CUADRILLA')
    {
    isCuadrilla = true;
        //Grid Asignados de Cuadrilla
        storeCuadrilla = new Ext.data.Store({
        pageSize: 10,
            total: 'total',
            proxy: {
            type: 'ajax',
                url : url_getMiembrosCuadrilla,
                reader: {
                type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                idCuadrilla: idCuadrilla,
                }
            },
            fields:
            [
            {name: 'id_persona_rol', mapping: 'id_persona_rol'},
            {name: 'id_persona', mapping: 'id_persona'},
            {name: 'nombre', mapping: 'nombre'},
            ],
            autoLoad: true
        });
        gridCuadrilla = Ext.create('Ext.grid.Panel', {
            width: 450,
            height: 170,
            title:'Miembros de Cuadrilla',
            store: storeCuadrilla,
            viewConfig: {enableTextSelection: true, emptyText: 'No hay datos para mostrar'},
                loadMask: true,
                frame: false,
                columns:
                [
                {
                id: 'id_persona',
                    header: 'id_persona',
                    dataIndex: 'id_persona',
                    hidden: true,
                    hideable: false
                },
                {
                id: 'id_persona_rol',
                    header: 'id_persona_rol',
                    dataIndex: 'id_persona_rol',
                    hidden: true,
                    hideable: false
                },
                {
                id: 'nombre',
                    header: 'Nombre Tecnico',
                    dataIndex: 'nombre',
                    width: 440,
                    sortable: true
                }
                ],
                bbar: Ext.create('Ext.PagingToolbar', {
                store: storeCuadrilla,
                    displayInfo: true,
                    displayMsg: 'Mostrando {0} - {1} de {2}',
                    emptyMsg: "No hay datos que mostrar."
                })
            });
        }    
    
     
	
    var keyArray = new Array();

    fieldsetCoordenadasTotal     = null;
    fieldsetCoordenadasManga1    = null;
    fieldsetCoordenadasManga2    = null;
    fieldsetCoordenadasIncidente = null;

     btnCoordenadasIncidente = Ext.create('Ext.button.Button', {
         iconCls: 'button-grid-Gmaps',
         itemId: 'ing_coordenadasIncidente',
         text: ' ',
         scope: this,
         handler: function(){ muestraMapa(3);}
     });
     btnCoordenadasManga1 = Ext.create('Ext.button.Button', {
         iconCls: 'button-grid-Gmaps',
         itemId: 'ing_coordenadasManga1',
         text: ' ',
         scope: this,
         handler: function(){ muestraMapa(1);}
     });
     btnCoordenadasManga2 = Ext.create('Ext.button.Button', {
         iconCls: 'button-grid-Gmaps',
         itemId: 'ing_coordenadasManga2',
         text: ' ',
         scope: this,
         handler: function(){ muestraMapa(2);}
     });

    if (data.casoPerteneceTN && data.mostrarCoordenadas == "S")
    {
        widthCoordenadas = 300;
        if(data.tareasManga == "S")
        {
            widthCoordenadas = "100%";
            fieldsetCoordenadasManga1 = new Ext.form.FieldSet(
            {
                xtype: 'fieldset',
                title: 'Manga 1',
                width: 230,
                items:
                [
                    {
                        layout: 'table',
                        border: false,
                        items:
                        [
                            {
                                width: 180,
                                layout: 'form',
                                border: false,
                                items:
                                [
                                    {
                                        xtype: 'displayfield'
                                    }
                                ]
                            },
                            btnCoordenadasManga1
                        ]
                    },
                    {
                        width: 230,
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
                    xtype: 'textfield',
                        fieldLabel: '* Longitud:',
                        maskRe: /[0-9-.]/,
                        id: 'text_longitudManga1',
                        name: 'text_longitudManga1',
                        width: 200,
                        value: '',
                        readOnly: false
                    },
                    {
                    xtype: 'textfield',
                        fieldLabel: '* Latitud:',
                        maskRe: /[0-9-.]/,
                        id: 'text_latitudManga1',
                        name: 'text_latitudManga1',
                        width: 200,
                        value: '',
                        readOnly: false
                    },

                ]
            });

            fieldsetCoordenadasManga2 = new Ext.form.FieldSet(
            {
                xtype: 'fieldset',
                title: 'Manga 2',
                width: 230,
                items:
                [
                    {
                        layout: 'table',
                        border: false,
                        items:
                        [
                            {
                                width: 180,
                                layout: 'form',
                                border: false,
                                items:
                                [
                                    {
                                        xtype: 'displayfield'
                                    }
                                ]
                            },
                            btnCoordenadasManga2
                        ]
                    },
                    {
                        width: 230,
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
                    xtype: 'textfield',
                        fieldLabel: '* Longitud:',
                        maskRe: /[0-9-.]/,
                        id: 'text_longitudManga2',
                        name: 'text_longitudManga2',
                        width: 200,
                        value: '',
                        readOnly: false
                    },
                    {
                    xtype: 'textfield',
                        fieldLabel: '* Latitud:',
                        maskRe: /[0-9-.]/,
                        id: 'text_latitudManga2',
                        name: 'text_latitudManga2',
                        width: 200,
                        value: '',
                        readOnly: false
                    },

                ]
            });
        }

        fieldsetCoordenadasIncidente = new Ext.form.FieldSet(
        {
            xtype: 'fieldset',
            title: 'Incidente',
            width: 230,
            items:
            [
                {
                    layout: 'table',
                    border: false,
                    items:
                    [
                        {
                            width: 180,
                            layout: 'form',
                            border: false,
                            items:
                            [
                                {
                                    xtype: 'displayfield'
                                }
                            ]
                        },
                        btnCoordenadasIncidente
                    ]
                },
                {
                    width: 230,
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
                xtype: 'textfield',
                    fieldLabel: '* Longitud:',
                    maskRe: /[0-9-.]/,
                    id: 'text_longitudI',
                    name: 'text_longitudI',
                    width: 200,
                    value: '',
                    readOnly: false
                },
                {
                xtype: 'textfield',
                    fieldLabel: '* Latitud:',
                    maskRe: /[0-9-.]/,
                    id: 'text_latitudI',
                    name: 'text_latitudI',
                    width: 200,
                    value: '',
                    readOnly: false
                },

            ]
        });

        fieldsetCoordenadasTotal = new Ext.form.FieldSet(
        {
            xtype: 'fieldset',
            title: 'Seleccionar Coordenadas',
            width: widthCoordenadas,
            items:
            [
                {
                    layout: 'table',
                    border: false,
                    items:
                    [
                        fieldsetCoordenadasManga1,
                        fieldsetCoordenadasManga2,
                    ]
                },
                fieldsetCoordenadasIncidente
            ]
        });
    }

    formPanel2 = Ext.create('Ext.form.Panel', {
		bodyPadding: 5,
		waitMsgTarget: true,
		height: 750,
		width: 900,
		layout: 'fit',
		fieldDefaults: {
			labelAlign: 'left',
			width: 300,
			msgTarget: 'side'
		},

		items: [{
			xtype: 'fieldset',
			title: 'Información',
			defaultType: 'textfield',
			items: [
				{
					xtype: 'displayfield',
					fieldLabel: 'Caso:',
					id: 'seguimientoCaso',
					name: 'seguimientoCaso',
					value: numero
				},{
					xtype: 'displayfield',
					fieldLabel: 'Tarea Inicial:',
					id: 'tareaCaso',
					name: 'tareaCaso',
					value: data.nombre_tarea
				},comboTarea,{
					xtype: 'displayfield',
					fieldLabel: 'Elemento/Tramo:',
					id: 'elemento/tramo',
					name: 'elemento/tramo',
					value: data.nombreTipo
				},
				{
					xtype: 'textfield',
					fieldLabel: 'Fecha de Inicio:',
					id: 'fechaInicial',
					name: 'fechaInicial',
					value: data.fechaEjecucion,
					readOnly:true
				},
				{
					xtype: 'textfield',
					fieldLabel: 'Hora Inicial Tarea:',
					id: 'horaInicial',
					name: 'horaInicial',
					value: data.horaEjecucion,
					readOnly:true
				},
                    {
                        fieldLabel: 'Fecha de Cierre:',
                        xtype: 'textfield',
                        id: 'fe_cierre_value',
                        name: 'fe_cierre_value',
                        format: 'Y-m-d',
                        editable: false,
                        readOnly: true,
                        value: fechaActual,
                        listeners: {
                            select: {
                                fn: function(e) 
                                {
                                    date = e.getValue();
                                    
                                    total = getTiempoTotal(fecha, hora, date, Ext.getCmp('ho_cierre_value').value, 'fecha');

                                    if (total !== -1) 
                                    {
                                        Ext.getCmp('tiempoTotal').setValue(total);                                        
                                    } 
                                    else 
                                    {
                                        Ext.getCmp('tiempoTotal').setValue(getTiempoTotal(fecha, hora, fechaActual,horaActual, ''));                                        
                                        Ext.getCmp('fe_cierre_value').setValue(date);
                                    }
                                }
                            }
                        }
                    },
                    {
                        fieldLabel: 'Hora de Cierre:',
                        xtype: 'textfield',
                        format: 'H:i',
                        id: 'ho_cierre_value',
                        name: 'ho_cierre_value',
                        value: horaActual,
                        editable: false,
                        increment: 1,
                        readOnly: true,
                        listeners: {
                            select: {
                                fn: function(e) 
                                {
                                    date = e.getValue();

                                    total = getTiempoTotal(fecha, hora, Ext.getCmp('fe_cierre_value').value, date, 'hora');

                                    if (total !== -1) 
                                    {
                                        Ext.getCmp('tiempoTotal').setValue(total);                                        
                                    } 
                                    else 
                                    {
                                        Ext.getCmp('tiempoTotal').setValue(getTiempoTotal(fecha, hora, fechaActual, horaActual, ''));                                        
                                        Ext.getCmp('ho_cierre_value').setValue(date);
                                    }
                                }
                            }
                        }
                    },
				{
					xtype: 'textfield',
					fieldLabel: 'Tiempo Total Tarea (minutos):',
					id: 'tiempoTotal',
					name: 'tiempoTotal',
					value: getTiempoTotal(data.fechaEjecucion,data.horaEjecucion,fechaActual,horaActual),
					readOnly:true				
				},
				{
					xtype: 'textarea',
					fieldLabel: 'Obsevacion:',
					id: 'observacionFinalizarTarea',
					name: 'observacionFinalizarTarea',
                    maxLength: 500,
                    enforceMaxLength:true,
					rows: 3,
					cols: 250
				},
				{
					xtype      : 'fieldcontainer',
					fieldLabel : 'Es Solucion',
					defaultType: 'radiofield',
					defaults: {
						flex: 1
					},
                    hidden: data.casoPerteneceTN ? true : false ,
					layout: 'hbox',
					items: [
						{
							boxLabel  : 'Si',
							name      : 'esSolucion',
							inputValue: 'S',
							id        : 'radio1'
						}, {
							boxLabel  : 'No',
							name      : 'esSolucion',
							inputValue: 'N',
							id        : 'radio2'
						}
					]
				},fieldsetCoordenadasTotal,gridCuadrilla,gridMaterialTareas
			]
		}]
	 });

	Ext.getCmp('radio1').setValue(true);   
	
    if(!data.casoPerteneceTN)
    {
        width = 900;
        if (isCuadrilla) 
        {
            height = 880 ;
        } 
        else
        {
            height = 680;
        }
    }
    else
    {
        if(data.tareasManga == "S")
        {
            width = 540;
        }
        else
        {
            width = 500;
        }
        if (data.tareasManga == "N" && data.mostrarCoordenadas == "S" && isCuadrilla)
        {
            height = 855 ;
        }
        else if(data.tareasManga == "S" && data.mostrarCoordenadas == "S" && isCuadrilla)
        {
            height = 940 ;
        }
        else if(isCuadrilla)
        {
            height = 650 ;
        }
        else if(data.mostrarCoordenadas == "S")
        {
            if(data.tareasManga == "S")
            {
                height = 750 ;
            }
            else
            {
                height = 650 ;
            }
        }
        else
        {
            height = 450;
        }
    }
    
    winFinalizarTarea = Ext.create('Ext.window.Window', {
		title: 'Finalizar Tarea',
		modal: true,
		width: width,
		height: height,
		resizable: false,
		layout: 'fit',
		items: [formPanel2],
		buttonAlign: 'center',
		buttons:[btnguardar2,btncancelar2]
    }).show(); 
    
}

function validarPresentarCamposContactoCliente(empresa, idTareaSeleccionada)
{      
    Ext.getCmp('nombreClienteARecibir').reset();
    Ext.getCmp('telefonoClienteARecibir').reset();
    Ext.getCmp('cargoClienteARecibir').reset();
    Ext.getCmp('correoClienteARecibir').reset();
    Ext.getCmp('convencionalClienteARecibir').reset();
    Ext.getCmp('prefijoNumeroClienteARecibir').reset();
    currentHeight = winAgregarAsignacionTarea.getHeight();
    heightFieldsContactClient = 168;
    if(empresa=='TN')
    {              
        Ext.getCmp('fieldsDatosContacto').getEl().show();
        if(!isFirstLoad)
        {
            winAgregarAsignacionTarea.setHeight(currentHeight + heightFieldsContactClient);
        }
    }else
    {
        Ext.getCmp('fieldsDatosContacto').getEl().hide();
        winAgregarAsignacionTarea.setHeight(currentHeight - heightFieldsContactClient);
    }  
    isFirstLoad = false;
}

function validarTabletPorCuadrilla(idCuadrilla)
{        
    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.get(document.body).mask('Cargando...');
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
    
    
    conn.request({
        url: url_tabletPorCuadrilla,
        method: 'post',
        params: 
            { 
                cuadrillaId : idCuadrilla
            },
        success: function(response){			
            var text = Ext.decode(response.responseText);            
            if(text.existeTablet == "S")
            {
                cuadrillaAsignada = "S";
            }
            else
            {
                Ext.Msg.alert("Alerta","La cuadrilla "+text.nombreCuadrilla+" no posee tablet asignada. Realice la asignación de tablet correspondiente o \n\
                                        seleccione otra cuadrilla.");
                cuadrillaAsignada = "N";
                Ext.getCmp('comboCuadrilla').setValue("");
            }
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


function guardarFinalizarTarea(id_caso,data)
{
    if(data.casoPerteneceTN && data.mostrarCoordenadas == "S" && data.tareasManga == "S" && ( Ext.getCmp('text_longitudManga1').getValue() == ""
        || Ext.getCmp('text_latitudManga1').getValue() == "" || Ext.getCmp('text_latitudManga2').getValue() == ""
        || Ext.getCmp('text_latitudManga2').getValue() == "" || Ext.getCmp('text_latitudI').getValue() == ""
        || Ext.getCmp('text_latitudI').getValue() == ""))
    {
        alert("Por favor llenar los campos obligatorios");
    }
    else if(data.casoPerteneceTN && data.mostrarCoordenadas == "S" && data.tareasManga == "N" && (Ext.getCmp('text_latitudI').getValue() == ""
        || Ext.getCmp('text_latitudI').getValue() == ""))
    {
        alert("Por favor llenar los campos obligatorios");
    }
    else
    {
        var conn = new Ext.data.Connection({
        listeners: {
        'beforerequest': {
        fn: function (con, opt) {
        Ext.get(document.body).mask('Finalizando Tarea...');
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

        var radio1              = Ext.getCmp('radio1').getValue();
        var tiempoTotal         = Ext.getCmp('tiempoTotal').getValue();
        var fe_cierre_value     = Ext.getCmp('fe_cierre_value').getValue();
        var ho_cierre_value     = Ext.getCmp('ho_cierre_value').getValue();
        var observacion         = Ext.getCmp('observacionFinalizarTarea').getValue();
        var comboTarea          = Ext.getCmp('comboTarea').getValue();
        var longitudIncidente   = "";
        var latitudIncidente    = "";
        var longitudManga1      = "";
        var latitudManga1       = "";
        var longitudManga2      = "";
        var latitudManga2       = "";
        if(data.casoPerteneceTN && data.mostrarCoordenadas == "S")
        {
            longitudIncidente   = Ext.getCmp('text_longitudI').getValue();
            latitudIncidente    = Ext.getCmp('text_latitudI').getValue();

            if(data.tareasManga == "S")
            {
                longitudManga1   = Ext.getCmp('text_longitudManga1').getValue();
                latitudManga1    = Ext.getCmp('text_latitudManga1').getValue();
                longitudManga2   = Ext.getCmp('text_longitudManga2').getValue();
                latitudManga2    = Ext.getCmp('text_latitudManga2').getValue();
            }
        }
        if(data.casoPerteneceTN)
        {
            radio1=false;
            json_materiales=null;
        }
        else
        {
            json_materiales = obtenerMateriales();
        }

        winFinalizarTarea.destroy();
        conn.request({
            method: 'POST',
            params :{
                id_caso: id_caso,
                id_detalle: data.id_sintomaTarea,
                es_solucion: radio1,
                materiales: json_materiales,
                tiempo_total:tiempoTotal,
                tiempo_cierre:fe_cierre_value,
                hora_cierre:ho_cierre_value,
                tiempo_ejecucion:data.fechaEjecucion,
                hora_ejecucion:data.horaEjecucion,
                observacion:observacion,
                tarea : comboTarea,
                clientes : data.clientes,
                tarea_final : "S",
                longitud: longitudIncidente,
                latitud: latitudIncidente,
                longitudManga1: longitudManga1,
                latitudManga1: latitudManga1,
                longitudManga2: longitudManga2,
                latitudManga2: latitudManga2
            },
            url: url_finalizarTarea,
            success: function(response){
                var json = Ext.JSON.decode(response.responseText);
                if(json.success)
                {
                    Ext.Msg.alert('Mensaje','Se finalizó la tarea.', function(btn){
                        if(btn=='ok')
                        {
                            winAdministrarTareas.destroy();

                            if (json.tareasAbiertas === 0 && json.tareasSolucionadas > 0 && !data.casoPerteneceTN)
                            {

                                obtenerDatosCasosCierre(data.id_caso, conn, false);
                            }
                            else
                            {
                                document.location.reload(true);
                            }

                        }
                    });
                }
                else
                {
                    Ext.Msg.alert('Alerta ',json.mensaje);
                }
            },
            failure: function(rec, op) {
                var json = Ext.JSON.decode(op.response.responseText);
                Ext.Msg.alert('Alerta ',json.mensaje);
            }
        });
    }
}


function aceptarRechazarTarea(data, id_caso){
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
		handler: function() {
			var calendario_observacion = Ext.getCmp('calendario_observacion').value;
			win.destroy();
			conn.request({
				method: 'POST',
				params :{
					id: data.id_sintomaTarea,
					observacion: calendario_observacion,
					bandera: 'Aceptada'
				},
				url: '../administrarTareaAsignadaGrid',
				success: function(response){
					Ext.Msg.alert('Mensaje','Se actualizo los datos.', function(btn){
						if(btn=='ok'){
							storeTareas.load();
						}
					});
				},
				failure: function(rec, op) {
					var json = Ext.JSON.decode(op.response.responseText);
					Ext.Msg.alert('Alerta ',json.mensaje);
				}
			});
		}
	});
	btnrechazar = Ext.create('Ext.Button', {
        text:  (data.action8 == 'button-grid-rechazarTarea') ? 'Anular' : 'Rechazar',
        cls: 'x-btn-rigth',
		handler: function() {
			conn.request({
				method: 'POST',
                params :
                {
                        id:             data.id_sintomaTarea,
                        observacion:    Ext.getCmp('calendario_observacion').value,
                        bandera:        (data.action8 == 'button-grid-rechazarTarea') ?  'Anulada' : 'Rechazada',
                        nombreTarea:    data.nombre_tarea,
                        id_caso:        data.id_caso
                },
				url: '../administrarTareaAsignadaGrid',
				success: function(response){
					Ext.Msg.alert('Mensaje','Se actualizo los datos.', function(btn){
						if(btn=='ok'){
							win.destroy();
							storeTareas.load();
						}
					});
				},
				failure: function(rec, op) {
					var json = Ext.JSON.decode(op.response.responseText);
					Ext.Msg.alert('Alerta ',json.mensaje);
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
	
	
	
    var string_html  = "<table width='100%' border='0' >";
    string_html += "    <tr>";
    string_html += "        <td colspan='6'>";
    string_html += "                <tr style='height:380px'>";
    string_html += "                    <td colspan='4'><div id='criterios_aceptar'></div></td>";
    string_html += "                    <td colspan='4'><div id='afectados_aceptar'></div></td>";
    string_html += "                </tr>";
    string_html += "            </table>";
    string_html += "        </td>";
    string_html += "    </tr>";
    string_html += "</table>";
	
	DivsCriteriosAfectados =  Ext.create('Ext.Component', {
		html: string_html,
		padding: 1,
		layout: 'anchor',
		style: { border: '0' }
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
				title: 'Información de Afectados',                    
                autoHeight: true,
				width: 1030,
				items: 
				[
					DivsCriteriosAfectados
				]
			},	
			{
				xtype: 'fieldset',
				title: 'Información de la tarea',                       
                autoHeight: true,
				width: 1030,
				items: 
				[
					{
						xtype: 'displayfield',
						fieldLabel: 'Tarea:',
						id: 'calendario_tarea',
						name: 'calendario_tarea',
						value: data.nombre_tarea

					},
					{
						xtype: 'textarea',
						fieldLabel: 'Obsevacion:',
						id: 'calendario_observacion',
						name: 'calendario_observacion',
						rows: 3,
						cols: 120
					}
				]
			}
		]
	});
				
	win = Ext.create('Ext.window.Window', {
		title: (data.action8 == 'button-grid-rechazarTarea') ? 'Aceptar / Anular Tarea Asignada' : 'Aceptar / Rechazar Tarea Asignada',
		modal: true,
		width: 1060,
		height: 650,
		resizable: false,
		layout: 'fit',
		items: [formPanel],
		buttonAlign: 'center',
		buttons:[btnguardar,btnrechazar,btncancelar]
	}).show();

	////////////////Grid  Criterios////////////////  
    storeCriterios_aceptar = new Ext.data.JsonStore(
    {
        total: 'total',
        pageSize: 400,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url : '../'+id_caso+'/getCriterios',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
			extraParams: {
				todos: 'YES'
			}
        },
        fields:
        [
            {name:'tipo', mapping:'tipo'},
            {name:'nombre', mapping:'nombre'},
			{name:'id_criterio_afectado', mapping:'id_criterio_afectado'},
			{name:'caso_id', mapping:'caso_id'},
			{name:'criterio', mapping:'criterio'},
			{name:'opcion', mapping:'opcion'}
        ]                
    });
    gridCriterios_aceptar = Ext.create('Ext.grid.Panel', {
        title:'Criterios de Seleccion',
        width: 500,
        height: 380,
        autoRender:true,
        enableColumnResize :false,
        id:'gridCriterios_aceptar',
        store: storeCriterios_aceptar,
		viewConfig: { enableTextSelection: true }, 
        loadMask: true,
        frame:true,
        forceFit:true,
        columns:
		[
			{
			  id: 'aceptar_id_criterio_afectado',
			  header: 'id_criterio_afectado',
			  dataIndex: 'id_criterio_afectado',
			  hidden: true,
			  hideable: false
			},
			{
			  id: 'aceptar_caso_id',
			  header: 'caso_id',
			  dataIndex: 'caso_id',
			  hidden: true,
			  sortable: true
			},
			{
			  id: 'aceptar_tipo_criterio',
			  header: 'Tipo',
			  dataIndex: 'tipo',
			  width:60,
			  hideable: false
			},
			{
			  id: 'aceptar_nombre_tipo_criterio',
			  header: 'Nombre',
			  dataIndex: 'nombre',
			  width:80,
			  hideable: false,
			  sortable: true
			},
			{
			  id: 'aceptar_criterio',
			  header: 'Criterio',
			  dataIndex: 'criterio',
			  width: 70,
			  hideable: false
			},
			{
			  id: 'aceptar_opcion',
			  header: 'Opcion',
			  dataIndex: 'opcion',
			  width: 260,
			  sortable: true
			}
		],
		renderTo: 'criterios_aceptar'
    });
    
	////////////////Grid  Afectados////////////////  
    storeAfectados_aceptar = new Ext.data.JsonStore(
    {
        autoLoad: true,
        pageSize: 4000,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : '../'+id_caso+'/getAfectados',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
			extraParams: {
				todos: 'YES'
			}
        },
        fields:
        [
            {name:'tipo', mapping:'tipo'},
			{name:'nombre', mapping:'nombre'},
            {name:'id', mapping:'id'},
            {name:'id_afectado', mapping:'id_afectado'},
            {name:'id_criterio', mapping:'id_criterio'},
            {name:'caso_id_afectado', mapping:'caso_id_afectado'},
            {name:'nombre_afectado', mapping:'nombre_afectado'},
            {name:'descripcion_afectado', mapping:'descripcion_afectado'}
        ]                
    });
    gridAfectados_aceptar = Ext.create('Ext.grid.Panel', {
        title:'Equipos Afectados',
        width: 500,
        height: 380,
        sortableColumns:false,
        store: storeAfectados_aceptar,
		viewConfig: { enableTextSelection: true }, 
        id:'gridAfectados_aceptar',
        enableColumnResize :false,
        loadMask: true,
        frame:true,
        forceFit:true,
		columns: [
			Ext.create('Ext.grid.RowNumberer'),
			{
			  id: 'aceptar_id',
			  header: 'id',
			  dataIndex: 'id',
			  hidden: true,
			  hideable: false
			},
			 {
			  id: 'aceptar_id_afectado',
			  header: 'id_afectado',
			  dataIndex: 'id_afectado',
			  hidden: true,
			  hideable: false
			},
			{
			  id: 'aceptar_id_criterio',
			  header: 'id_criterio',
			  dataIndex: 'id_criterio',
			  hidden: true,
			  hideable: false
			},
			{
			  id: 'aceptar_caso_id_afectado',
			  header: 'caso_id_afectado',
			  dataIndex: 'caso_id_afectado',
			  hidden: true,
			  hideable: false 
			},
			{
			  id: 'aceptar_tipo_afectado',
			  header: 'Tipo',
			  dataIndex: 'tipo',
			  width:65,
			  hideable: false
			},
			{
			  id: 'aceptar_nombre_tipo_afectado',
			  header: 'Nombre',
			  dataIndex: 'nombre',
			  width:85,
			  hideable: false,
			  sortable: true
			},
			{
			  id: 'aceptar_nombre_afectado',
			  header: 'Parte Afectada',
			  dataIndex: 'nombre_afectado',
			  width:210,
			  sortable: true
			},
			{
			  id: 'aceptar_descripcion_afectado',
			  header: 'Descripcion',
			  dataIndex: 'descripcion_afectado',
			  width:145,
			  sortable: true
			}
		],    
        renderTo: 'afectados_aceptar'
    });
   	
}


var winReprogramarTarea;

function reprogramarTarea(id_detalle, data , fechaActual , horaActual){
    
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
    
    
     var storeMotivos = Ext.create('Ext.data.Store', {
      
		fields: ['opcion', 'valor'],
		data: 
		[{
		    "opcion": "Cliente Solicita Reprogramar",
		    "valor": "C"
		    }, {
		    "opcion": "Tecnico Solicita Reprogramar",
		    "valor": "T"
		    }		   
		]
 	    });

    
    
      comboMotivoStore = new Ext.data.Store({ 
	        total: 'total',
	        proxy: {
	            type: 'ajax',
	            url : '../getMotivosReprogramacion',
	            reader: {
	                type: 'json',
	                totalProperty: 'total',
	                root: 'encontrados'
	            },
// 	            extraParams: {
// 	                    nombre: '',
// 	                    estado: 'Activo'
// 	                }
	        },
	        
	        fields:
	              [
	                {name:'id_motivo', mapping:'id_motivo'},
	                {name:'nombre_motivo', mapping:'nombre_motivo'}
	              ]
	    });
    
     comboMotivo = Ext.create('Ext.form.ComboBox', {
	        id:'comboMotivo',
	        store: comboMotivoStore,
		fieldLabel: 'Motivo :',
	        displayField: 'nombre_motivo',
	        valueField: 'id_motivo',
	        height:30,
	        border:0,		
	        margin:0,		
		queryMode: "remote",
		emptyText: ''
	    });
    
    
    
    btnguardar2 = Ext.create('Ext.Button', {
		text: 'Guardar',
		cls: 'x-btn-rigth',
		handler: function() {		
		  		  
			
			if(Ext.getCmp('comboMotivo').value=="" || Ext.getCmp('comboMotivo').value==null )
				  Ext.Msg.alert('Alerta ',"Debe elegir un motivo");
			else{
		  
				var valorBool = true;//validarTareasMateriales();
				if(valorBool)
				{
					var fe_ejecucion_value = Ext.getCmp('fe_ejecucion_value').value;
					var ho_ejecucion_value = Ext.getCmp('ho_ejecucion_value').value;
					var observacion = Ext.getCmp('observacion').value;
					var motivo = Ext.getCmp('comboMotivo').value;
					winReprogramarTarea.destroy();
					conn.request({
						method: 'POST',
						params :{
							id_detalle: id_detalle,
							fe_ejecucion: fe_ejecucion_value,
							ho_ejecucion: ho_ejecucion_value,
							observacion: observacion,
							motivo:motivo
						},
						url: '../reprogramarTarea',
						success: function(response){
							var json = Ext.JSON.decode(response.responseText);
							if(json.success)
							{
								Ext.Msg.alert('Mensaje','Se reprogramo la tarea.', function(btn){
									if(btn=='ok'){									
										//storeTareas.load();
										winAdministrarTareas.destroy();
										document.location.reload(true);
									}
								});
							}
							else
							{
								Ext.Msg.alert('Alerta ',json.mensaje);
							}
						},
						failure: function(rec, op) {
							var json = Ext.JSON.decode(op.response.responseText);
							Ext.Msg.alert('Alerta ',json.mensaje);
						}
					});
				}
			
			}
		}
    });
    btncancelar2 = Ext.create('Ext.Button', {
		text: 'Cerrar',
		cls: 'x-btn-rigth',
		handler: function() {
			winReprogramarTarea.destroy();
		}
    });
    
    formPanel2 = Ext.create('Ext.form.Panel', {
		bodyPadding: 5,
		waitMsgTarget: true,
		height: 280,
		width: 500,
		layout: 'fit',
		fieldDefaults: {
			labelAlign: 'left',
			msgTarget: 'side'
		},
		items: 
		[
			{
				xtype: 'fieldset',
				title: 'Información',
				defaultType: 'textfield',
				items: 
				[
					{
						xtype: 'displayfield',
						fieldLabel: 'Tarea:',
						id: 'tareaCaso',
						name: 'tareaCaso',
						value: data.nombre_tarea
					},
					{
						xtype: 'datefield',
						fieldLabel: 'Fecha de Ejecucion:',
						id: 'fe_ejecucion_value',
						name:'fe_ejecucion_value',
						editable: false,
						format: 'Y-m-d',
						value:fechaActual,
						minValue: fechaActual  // limited to the current date or prior
					},
					{
						xtype: 'timefield',
						fieldLabel: 'Hora de Ejecucion:',
						format: 'H:i',
						id: 'ho_ejecucion_value',
						name: 'ho_ejecucion_value',
						minValue: '00:01',
						maxValue: '23:59',
						increment: 1,
						editable: true,
						value:horaActual
					},
					//comboMotivo,
					{
						xtype: 'combobox',
						fieldLabel: 'Motivo:',
						id: 'comboMotivo',
						name: 'comboMotivo',
						store: storeMotivos,
						displayField: 'opcion',
						valueField: 'valor',
						queryMode: "remote",
						emptyText: '',
					},
					{
						xtype: 'textarea',
						fieldLabel: 'Observacion:',
						id: 'observacion',
						name: 'observacion',
						rows: 5,
						cols: 160
					}
				]
			}
		]
	 });  
	
    winReprogramarTarea = Ext.create('Ext.window.Window', {
		title: 'Reprogramar Tarea',
		modal: true,
		width: 500,
		height: 350,
		resizable: false,
		layout: 'fit',
		items: [formPanel2],
		buttonAlign: 'center',
		buttons:[btnguardar2,btncancelar2]
    }).show(); 
}
var winCancelarTarea;

function cancelarTarea(id_detalle, data , tipo){
  
    
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
    btnguardar2 = Ext.create('Ext.Button', {
		text: 'Guardar',
		cls: 'x-btn-rigth',
		handler: function() {		
			var valorBool = true;//validarTareasMateriales();
			if(valorBool)
			{
				//json_materiales = obtenerMateriales();
				var canObservacion = Ext.getCmp('observacion').value;
				winCancelarTarea.destroy();
				conn.request({
					method: 'POST',
					params :{
						id_detalle: id_detalle,
						observacion: canObservacion,
						tipo : tipo,
						id_caso: data.id_caso
					},
					url: '/soporte/tareas/cancelarTarea',
					success: function(response){
						var json = Ext.JSON.decode(response.responseText);
						if(json.success)
						{
							if(tipo=='rechazada')mensaje = 'Se rechazo la tarea.';
							else mensaje = 'Se cancelo la tarea.';
							Ext.Msg.alert('Mensaje',mensaje, function(btn){
								if(btn=='ok'){
									winAdministrarTareas.destroy();

                                    if(json.tareasAbiertas === 0 && !data.casoPerteneceTN)
                                    {
                                        obtenerDatosCasosCierre(data.id_caso,conn,true);
                                    }
                                    else
                                    {
                                        document.location.reload(true);
                                    }
								}
							});
						}
						else
						{
							Ext.Msg.alert('Alerta ',json.mensaje);
						}
					},
					failure: function(rec, op) {
						var json = Ext.JSON.decode(op.response.responseText);
						Ext.Msg.alert('Alerta ',json.mensaje);
					}
				});
			}	
		}
    });
    btncancelar2 = Ext.create('Ext.Button', {
		text: 'Cerrar',
		cls: 'x-btn-rigth',
		handler: function() {
			winCancelarTarea.destroy();
		}
    });
    
    formPanel2 = Ext.create('Ext.form.Panel', {
		bodyPadding: 5,
		waitMsgTarget: true,
		height: 250,
		width: 500,
		layout: 'fit',
		fieldDefaults: {
			labelAlign: 'left',
			msgTarget: 'side'
		},
		items: 
		[
			{
				xtype: 'fieldset',
				title: 'Información',
				defaultType: 'textfield',
				items: 
				[
					{
						xtype: 'displayfield',
						fieldLabel: 'Tarea:',
						id: 'tareaCaso',
						name: 'tareaCaso',
						value: data.nombre_tarea
					},
					{
						xtype: 'textarea',
						fieldLabel: 'Obsevacion:',
						id: 'observacion',
						name: 'observacion',
						rows: 5,
						cols: 160
					}
				]
			}
		]
	 });  
	
    winCancelarTarea = Ext.create('Ext.window.Window', {
		title: 'Cancelar Tarea',
		modal: true,
		width: 500,
		height: 280,
		resizable: false,
		layout: 'fit',
		items: [formPanel2],
		buttonAlign: 'center',
		buttons:[btnguardar2,btncancelar2]
    }).show(); 
}


function setearCombo(tipo)
{
    if(tipo == "1")    
    {       
        cuadrillaAsignada = "S";
        var myData_message = storeEmpleados.getProxy().getReader().jsonData.myMetaData.message;
        var myData_boolSuccess = storeEmpleados.getProxy().getReader().jsonData.myMetaData.boolSuccess;

        if (myData_boolSuccess != "1")
        {
            Ext.Msg.alert('Mensaje ', myData_message);
            Ext.getCmp('comboEmpleado').setDisabled(true); 
            Ext.getCmp('comboCuadrilla').setDisabled(true);
            Ext.getCmp('comboCuadrilla').setValue("");
            Ext.getCmp('comboContratista').setDisabled(true);
            Ext.getCmp('comboContratista').setValue("");
        }
        else
        {
            if (storeEmpleados.getCount() <= 1 && myData_boolSuccess != "1") {
                Ext.Msg.alert('Mensaje ', "No existen empleados asignados para este departamento.");
                Ext.getCmp('comboEmpleado').setDisabled(true);  
                Ext.getCmp('comboCuadrilla').setDisabled(true); 
                Ext.getCmp('comboCuadrilla').setValue("");
                Ext.getCmp('comboContratista').setDisabled(true);
                Ext.getCmp('comboContratista').setValue("");
            }
            else
            {
                Ext.getCmp('comboEmpleado').setDisabled(false);
                Ext.getCmp('comboCuadrilla').setDisabled(true); 
                Ext.getCmp('comboCuadrilla').setValue("");
                Ext.getCmp('comboContratista').setDisabled(true);
                Ext.getCmp('comboContratista').setValue("");
                valorAsignacion = "empleado";  
            }
            
        }
    }
    else if (tipo == "2")
    {     
        var myData_message = storeCuadrillas.getProxy().getReader().jsonData.myMetaData.message;
        var myData_boolSuccess = storeCuadrillas.getProxy().getReader().jsonData.myMetaData.boolSuccess;                 

        if (myData_boolSuccess != "1")
        {
            Ext.Msg.alert('Alerta ', myData_message);
            Ext.getCmp('comboCuadrilla').setDisabled(true);
            Ext.getCmp('comboEmpleado').setDisabled(true);
            Ext.getCmp('comboEmpleado').setValue("");
            Ext.getCmp('comboContratista').setDisabled(true);
        }
        else
        {                
            Ext.getCmp('comboCuadrilla').setDisabled(false);
            Ext.getCmp('comboEmpleado').setDisabled(true);
            Ext.getCmp('comboEmpleado').setValue("");
            Ext.getCmp('comboContratista').setDisabled(true);
            Ext.getCmp('comboContratista').setValue("");
            valorAsignacion = "cuadrilla";
        }
    }
    else
    {
        cuadrillaAsignada = "S";
        var myData_message = storeContratista.getProxy().getReader().jsonData.myMetaData.message;
        var myData_boolSuccess = storeContratista.getProxy().getReader().jsonData.myMetaData.boolSuccess;

        if (myData_boolSuccess != "1")
        {
            Ext.Msg.alert('Alerta ', myData_message);
            Ext.getCmp('comboCuadrilla').setDisabled(true);
            Ext.getCmp('comboCuadrilla').setValue("");
            Ext.getCmp('comboEmpleado').setDisabled(true);
            Ext.getCmp('comboEmpleado').setValue("");
            Ext.getCmp('comboContratista').setDisabled(true);
        }
        else
        {
            Ext.getCmp('comboCuadrilla').setDisabled(true);
            Ext.getCmp('comboCuadrilla').setValue("");
            Ext.getCmp('comboEmpleado').setDisabled(true);
            Ext.getCmp('comboEmpleado').setValue("");
            Ext.getCmp('comboContratista').setDisabled(false);
            valorAsignacion = "contratista";
        }
    }
}								

function opcionesHal(tipo,tarea,caso,hipotesis)
{
    if(tarea == null || tarea == "")
    {
        Ext.Msg.alert('Alerta ', "Por Favor seleccionar una tarea...!!");
        return;
    }

    if (tipo == 1)
    {
        storeIntervalosHal.getProxy().extraParams.nIntentos = 1;
        gridIntervalos.setVisible(false);
        gridHalDice.setVisible(true);
        formPanelHal.doLayout();
    }
    else if (tipo == 2)
    {
        nIntentos = nIntentos + 1;
        storeIntervalosHal.getProxy().extraParams.nIntentos = nIntentos;
        gridIntervalos.setVisible(true);
        gridHalDice.setVisible(false);
        formPanelHal.doLayout();
    }

    document.getElementById('divAtenderAntes').style.display = 'block';
    Ext.getCmp('nueva_sugerencia').setDisabled(true);
    tipoHal       = tipo;
    seleccionaHal = true;
    storeIntervalosHal.removeAll();
    storeIntervalosHal.getProxy().extraParams.idCaso        = caso;
    storeIntervalosHal.getProxy().extraParams.idHipotesis   = hipotesis;
    storeIntervalosHal.getProxy().extraParams.idAdmiTarea   = tarea;
    storeIntervalosHal.getProxy().extraParams.fechaSugerida = null;
    storeIntervalosHal.getProxy().extraParams.horaSugerida  = null;
    storeIntervalosHal.getProxy().extraParams.tipoHal       = tipoHal;
    storeIntervalosHal.load();
    Ext.getCmp('fecha_sugerida').setValue(null);
    Ext.getCmp('hora_sugerida').setValue(null);
}

function eliminarSeleccionHal(selModelIntervalos,gridHalDice,tipoHal)
{
    if (tipoHal == 2)
    {
        for (var i = 0; i < selModelIntervalos.getSelection().length; ++i)
        {
            selModelIntervalos.getStore().remove(selModelIntervalos.getSelection()[i]);
        }
    }
    else
    {
        for (var i = 0; i < gridHalDice.getStore().data.items.length; ++i)
        {
            gridHalDice.getStore().remove(gridHalDice.getStore().data.items[i]);
        }
    }
}