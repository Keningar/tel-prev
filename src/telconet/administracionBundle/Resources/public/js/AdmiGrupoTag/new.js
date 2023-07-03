Ext.onReady(function () {
  Ext.tip.QuickTipManager.init();

  $("#inputGuardar").click(function () {
    guardarGrupoTags();
  });

  Ext.define("modelTipoScope", {
    extend: "Ext.data.Model",
    fields: [
      {
        name: "strIdTipoScope",
        type: "string"
      }, {
        name: "strTipoScope",
        type: "string"
      }
    ]
  });

  //Store Tipo Scope
  var storeTipoScope = Ext.create("Ext.data.Store", {
    autoLoad: true,
    model: "modelTipoScope",
    proxy: {
      type: "ajax",
      url: urlGetTipoScope,
      reader: {
        type: "json",
        root: "jsonTipoScope"
      }
    }
  });

  //ley 1
  storeTags = new Ext.data.Store({
    pageSize: 1000,
    proxy: {
      type: "ajax",
      url: urlGetTags,
      reader: {
        type: "json",
        totalProperty: "intTotalTags",
        root: "jsonTags"
      }
    },
    fields: [
      {
        name: "intIdTag",
        mapping: "intIdTag"
      }, {
        name: "strDescripcionTag",
        mapping: "strDescripcionTag"
      }
    ]
  });

  //ley 5
  var selectionTags = Ext.create("Ext.selection.CheckboxModel", {
    listeners: {
      selectionchange: function (sm, selections) {
        gridTags.down("#removeButton").setDisabled(selections.length == 0);
      }
    }
  });

  //Combo de tipo de scope
  Ext.create("Ext.form.ComboBox", {
    id: "cmbTipoScope",
    fieldLabel: 'Tipo Scope:',
    store: storeTipoScope,
    displayField: "strTipoScope",
    valueField: "strIdTipoScope",
    height: 30,
    width: 425,
    queryMode: "local",
    emptyText: "",
    renderTo: "comboTipoScope"
  });

  //Model Blanck para el store de Tags  ley2
  Ext.define("modelTagsBlanck", {
    extend: "Ext.data.Model",
    fields: [
      {
        name: "intIdTagBlanck",
        mapping: "intIdTagBlanck"
      }, {
        name: "strDescripcionTagBlanck",
        mappgin: "strDescripcionTagBlanck"
      }
    ]
  });

  //Store Blanck para el store de Tags ley 3
  storeTagsBlanck = Ext.create("Ext.data.Store", {
    autoDestroy: true,
    autoLoad: false,
    model: "modelTagsBlanck",
    proxy: {
      type: "ajax",
      url: "gridTags",
      reader: {
        type: "json",
        totalProperty: "total",
        root: "bufferHilo"
      }
    }
  });

  var cellEditing = Ext.create("Ext.grid.plugin.CellEditing", {
    clicksToEdit: 2,
    listeners: {
      edit: function () {
        gridTags.getView().refresh();
      }
    }
  });

  //Grid tags
  gridTags = Ext.create("Ext.grid.Panel", {
    id: "gridTags",
    store: storeTagsBlanck,
    columnLines: true,
    columns: [
      {
        id: "intIdTagBlanck",
        header: "intIdTagBlanck",
        dataIndex: "intIdTagBlanck",
        hidden: true,
        hideable: false
      }, {
        id: "idGridTags",
        header: "Tags",
        dataIndex: "strDescripcionTagBlanck",
        width: 210,
        sortable: true,
        renderer: function (value, metadata, record, rowIndex, colIndex, store) {
          if (typeof record.data.strDescripcionTagBlanck == "number") {
            record.data.intIdTagBlanck = record.data.strDescripcionTagBlanck;
            for (var i = 0; i < storeTags.data.items.length; i++) {
              if (storeTags.data.items[i].data.intIdTag == record.data.intIdTagBlanck) {
                record.data.strDescripcionTagBlanck = storeTags.data.items[i].data.strDescripcionTag;
                break;
              }
            }
          }
          return record.data.strDescripcionTagBlanck;
        },
        editor: {
          id: "cboTags",
          xtype: "combobox",
          typeAhead: true,
          displayField: "strDescripcionTag",
          valueField: "intIdTag",
          triggerAction: "all",
          selectOnFocus: true,
          loadingText: "Buscando ...",
          hideTrigger: false,
          store: storeTags,
          lazyRender: true,
          listClass: "x-combo-list-small",
          listeners: {
            select: function (combo) {
              var r = Ext.create("modelTagsBlanck", {
                intIdTagBlanck: combo.getValue(),
                strDescripcionTagBlanck: combo.lastSelectionText
              });
              if (!existeRecordTag(r, gridTags)) {
                Ext.get("cboTags").dom.value = "";
                if (r.get("intIdTagBlanck") != "null") {
                  Ext.get("cboTags").dom.value = r.get("strDescripcionTagBlanck");
                  this.collapse();
                }
              } else {
                Ext.Msg.alert("Error ", "Ya existe");
                eliminarSeleccion(gridTags);
              }
            }
          } //listeners
        } //editor
      }
    ],
    selModel: selectionTags,
    viewConfig: {
      stripeRows: true
    },
    // inline buttons
    dockedItems: [
      {
        xtype: "toolbar",
        items: [
          {
            itemId: "removeButton",
            text: "Eliminar",
            tooltip: "Elimina el tag seleccionado",
            iconCls: "remove",
            disabled: true,
            handler: function () {
              eliminarSeleccion(gridTags);
            }
          },
          "-", {
            text: "Agregar",
            tooltip: "Agrega un tag a la lista",
            iconCls: "add",
            handler: function () {
              var r = Ext.create("modelTagsBlanck", {
                intIdTagBlanck: "",
                strDescripcionTagBlanck: ""
              });
              if (!existeRecordTag(r, gridTags)) {
                storeTagsBlanck.insert(0, r);
                cellEditing.startEditByPosition({row: 0, column: 1});
              } else {
                Ext.Msg.alert("Error ", "Ya existe un registro");
              }
            }
          }
        ]
      }
    ],
    width: 248,
    height: 200,
    frame: true,
    fieldLabel: 'Grupos Tags:',
    title: "Agregar Tags",
    style: "margin:0 auto;margin-top:20px; margin-left:50%;",
    plugins: [cellEditing],
    renderTo: "gridGrupoTag"
  });
});

function guardarGrupoTags() {
    //Validador
    var intTipoScope = Ext.getCmp("cmbTipoScope").getValue();
    var tags = obtenerInformacionGrid();

    if (Ext.isEmpty(intTipoScope)) {
        Ext.Msg.alert("Error ", "Debe ingresar el tipo de Scope");
        return false;
    } else if (!tags) {
        Ext.Msg.alert("Error ", "Debe agregar al menos un TAG para crear el grupo");
        return false;
    } else {
        Ext.MessageBox.show({
            msg: "Guardando...",
            title: "Procesando",
            progressText: "Guardando.",
            progress: true,
            closable: false,
            width: 300,
            wait: true,
            waitConfig: {
                interval: 200
            }
        });

        Ext.Ajax.request({
            url: urlCrearGrupoTag,
            method: "POST",
            timeout: 60000,
            params: {
                intTipoScope: intTipoScope,
                jsonTagsScope: tags
            },
            success: function (response) {
                var objRespuesta = Ext.decode(response.responseText);

                if (objRespuesta.status === "OK") {
                    Ext.Msg.alert("Informacion", objRespuesta.mensaje, function (btn) {
                        if (btn == "ok") {
                            //Redirect
                            window.location = objRespuesta.intTipoScope + "/show";
                            Ext.MessageBox.show({
                                msg: "Redireccionando...",
                                title: "Espere por favor",
                                progressText: "Redireccionando.",
                                progress: true,
                                closable: false,
                                width: 300,
                                wait: true,
                                waitConfig: {
                                    interval: 200
                                }
                            });
                        }
                    });
                } else {
                    Ext.Msg.alert("Error", objRespuesta.mensaje);
                }
            },
            failure: function (result) {
                Ext.Msg.alert("Error ", "Error: " + result.statusText);
            }
        });
    }
}

function obtenerInformacionGrid() {
  var array = new Object();
  var grid = gridTags;
  array["total"] = grid.getStore().getCount();
  array["data"] = new Array();

  arrayData = Array();
  if (grid.getStore().getCount() !== 0) {
    for (var i = 0; i < grid.getStore().getCount(); i++) {
      strTags = grid.getStore().getAt(i).data.intIdTagBlanck;
      if (strTags === "") {
        Ext.Msg.alert("Advertencia", "No puede ingresar tags vacios");
        return false;
      } else {
        arrayData.push(grid.getStore().getAt(i).data);
      }
    }
    array["data"] = arrayData;
    return Ext.JSON.encode(array);
  } else {
    Ext.Msg.alert("Advertencia", "No ha ingresado tags");
    return false;
  }
}

function eliminarSeleccion(datosSelect) {
  while(datosSelect.getSelectionModel().getCount() > 0){
    for (var i = 0; i < datosSelect.getSelectionModel().getCount(); i++) {
      datosSelect.getStore().remove(datosSelect.getSelectionModel().getSelection()[i]);
    }
  }
}

function existeRecordTag(myRecord, grid) {
  var existe = false;
  var num = grid.getStore().getCount();

  for (var i = 0; i < num; i++) {
    var intIdTagBlanck = grid.getStore().getAt(i).get("intIdTagBlanck");
    if (intIdTagBlanck === myRecord.get("intIdTagBlanck")) {
      existe = true;
      break;
    }
  }
  return existe;
}