Ext.require(["Ext.ux.grid.plugin.PagingSelectionPersistence"]);

Ext.onReady(function () {
  Ext.tip.QuickTipManager.init();

  Ext.define("ModelStore", {
    extend: "Ext.data.Model",
    fields: [
      {
        name: "idTag",
        mapping: "idTag"
      }, {
        name: "descripcionTag",
        mapping: "descripcionTag"
      }, {
        name: "observacionTag",
        mapping: "observacionTag"
      }, {
        name: "estado",
        mapping: "estado"
      }, {
        name: "action1",
        mapping: "action1"
      }, {
        name: "action3",
        mapping: "action3"
      }
    ],
    idProperty: "idTag"
  });

  store = new Ext.data.Store({
    pageSize: 10,
    model: "ModelStore",
    total: "total",
    proxy: {
      type: "ajax",
      timeout: 600000,
      url: getEncontrados,
      reader: {
        type: "json",
        totalProperty: "total",
        root: "encontrados"
      },
      extraParams: {
        nombre: "",
        codigo: "",
        estado: "Todos"
      }
    },
    autoLoad: true
  });

  var pluginExpanded = true;

  var toolbar = Ext.create("Ext.toolbar.Toolbar", {
    dock: "top",
    align: "->",
    items: [
      {
        iconCls: "icon_add",
        text: "Seleccionar Todos",
        itemId: "select",
        scope: this,
        handler: function () {
          Ext.getCmp("grid").getPlugin("pagingSelectionPersistence").selectAll();
        }
      }, {
        iconCls: "icon_limpiar",
        text: "Borrar Todos",
        itemId: "clear",
        scope: this,
        handler: function () {
          Ext.getCmp("grid").getPlugin("pagingSelectionPersistence").clearPersistedSelection();
        }
      }
    ]
  });

  grid = Ext.create("Ext.grid.Panel", {
    id: "grid",
    width: 850,
    height: 400,
    store: store,
    selModel: Ext.create("Ext.selection.CheckboxModel"),
    plugins: [
      {
        ptype: "pagingselectpersist"
      }
    ],
    viewConfig: {
      enableTextSelection: true,
      id: "gv",
      trackOver: true,
      stripeRows: true,
      loadMask: false
    },
    dockedItems: [toolbar],
    columns: [
      {
        id: "idTag",
        header: "idTag",
        dataIndex: "idTag",
        hidden: true,
        hideable: false
      }, {
        id: "descripcionTag",
        header: "Descripción",
        dataIndex: "descripcionTag",
        width: 220,
        sortable: true
      }, {
        id: "observacionTag",
        header: "Observación",
        dataIndex: "observacionTag",
        width: 220,
        sortable: true
      }, {
        header: "Estado",
        dataIndex: "estado",
        width: 100,
        sortable: true
      }, {
        xtype: "actioncolumn",
        header: "Acciones",
        width: 120,
        items: [
          {
            getClass: function (v, meta, rec) {
              this.items[0].tooltip = "Ver";

              return rec.get("action1");
            },
            tooltip: "Ver",
            handler: function (grid, rowIndex, colIndex) {
              var rec = store.getAt(rowIndex);
              window.location = rec.get("idTag") + "/show";
            }
          }, {
            getClass: function (v, meta, rec) {
              var permiso = $("#ROLE_278-2178");
              var boolPermiso = typeof permiso === "undefined"
                ? false
                : permiso.val() == 1
                  ? true
                  : false;
              if (!boolPermiso) {
                this.items[1].tooltip = "";
              } else {
                this.items[1].tooltip = "Eliminar";
              }
              return rec.get("action3");
            },
            handler: function (grid, rowIndex, colIndex) {
              var rec = store.getAt(rowIndex);

              var permiso = $("#ROLE_278-2178");
              var boolPermiso = typeof permiso === "undefined"
                ? false
                : permiso.val() == 1
                  ? true
                  : false;
              if (!boolPermiso) {
                Ext.Msg.alert("Error ", "No tiene permisos para realizar esta accion");
              } else {
                eliminar(rec);
              }
            }
          }
        ]
      }
    ],
    bbar: Ext.create("Ext.PagingToolbar", {
      store: store,
      displayInfo: true,
      displayMsg: "Mostrando {0} - {1} de {2}",
      emptyMsg: "No hay datos que mostrar."
    }),
    renderTo: "grid"
  });
  
  Ext.create("Ext.panel.Panel", {
    bodyPadding: 7, // Don't want content to crunch against the borders
    //bodyBorder: false,
    border: false,
    //border: '1,1,0,1',
    buttonAlign: "center",
    layout: {
      type: "table",
      columns: 5,
      align: "stretch"
    },
    bodyStyle: {
      background: "#fff"
    },
    collapsible: true,
    collapsed: true,
    width: 850,
    title: "Criterios de busqueda",
    buttons: [
      {
        text: "Buscar",
        iconCls: "icon_search",
        handler: function () {
          buscar();
        }
      }, {
        text: "Limpiar",
        iconCls: "icon_limpiar",
        handler: function () {
          limpiar();
        }
      }
    ],
    items: [
      {
        width: "10%",
        border: false
      }, {
        xtype: "textfield",
        id: "txtNombre",
        fieldLabel: "Nombre",
        value: "",
        width: "300px"
      }, {
        width: "30%",
        border: false
      }, {
        xtype: "combobox",
        fieldLabel: "Estado",
        id: "sltEstado",
        value: "Todos",
        store: [
          [
            "Todos", "Todos"
          ],
          [
            "Activo", "Activo"
          ],
          [
            "Modificado", "Modificado"
          ],
          [
            "Eliminado", "Eliminado"
          ]
        ],
        width: "30%"
      }, {
        width: "10%",
        border: false
      }, {
        width: "10%",
        border: false
      }, { //inicio
        xtype: "textfield",
        id: "txtCodigo",
        fieldLabel: "Codigo",
        value: "",
        width: "200px"
      }, {
        width: "20%",
        border: false
      }, { //medio
        width: "30%",
        border: false
      }, {
        width: "10%",
        border: false
      } //final
    ],
    renderTo: "filtro"
  });
});

function buscar() {
  store.getProxy().extraParams.codigo = Ext.getCmp("txtCodigo").value;
  store.getProxy().extraParams.nombre = Ext.getCmp("txtNombre").value;
  store.getProxy().extraParams.estado = Ext.getCmp("sltEstado").value;
  store.load();
}

function eliminar(rec) {
  var conn = new Ext.data.Connection({
    listeners: {
      beforerequest: {
        fn: function (con, opt) {
          Ext.get(document.body).mask("Eliminando tag...");
        },
        scope: this
      },
      requestcomplete: {
        fn: function (con, res, opt) {
          Ext.get(document.body).unmask();
        },
        scope: this
      },
      requestexception: {
        fn: function (con, res, opt) {
          Ext.get(document.body).unmask();
        },
        scope: this
      }
    }
  });
  storeServidores = new Ext.data.Store({
    total: "total",
    proxy: {
      type: "ajax",
      method: "post",
      url: url_getServidor,
      reader: {
        type: "json",
        totalProperty: "total",
        root: "encontrados"
      }
    },
    fields: [
      {
        name: "idElemento",
        mapping: "idElemento"
      }, {
        name: "nombreElemento",
        mapping: "nombreElemento"
      }
    ]
  });
  comboServidores = new Ext.form.ComboBox({
    id: "cmb_servidores",
    name: "cmb_servidores",
    fieldLabel: "Elemento",
    emptyText: "Seleccione",
    store: storeServidores,
    displayField: "nombreElemento",
    valueField: "idElemento",
    height: 30,
    width: 350,
    border: 0,
    margin: 0
  });
  btnguardar = Ext.create("Ext.Button", {
    text: "Aceptar",
    cls: "x-btn-rigth",
    handler: function () {
      if (Ext.getCmp("cmb_servidores").value != null && Ext.getCmp("cmb_servidores").value != "") {
        Ext.Msg.confirm("Alerta", "Se eliminara el registro. Desea continuar?", function (btn) {
          if (btn == "yes") {
            Ext.Ajax.request({
              url: deleteAjax,
              method: "post",
              params: {
                param: rec.get("idTag"),
                elemento: Ext.getCmp("cmb_servidores").value
              },
              success: function (response) {
                var text = response.responseText;
                Ext.Msg.alert("Informacion ", text, function (btn) {
                  if (btn == "ok") {
                    win.destroy();
                    store.load();
                  }
                });
              },
              failure: function (result) {
                Ext.Msg.alert("Error ", "Error: " + result.statusText);
              }
            });
          }
        });
      } else {
        Ext.Msg.alert("Advertencia ", "Debe Seleccionar un elemento");
      }
    }
  });

  btncancelar = Ext.create("Ext.Button", {
    text: "Cerrar",
    cls: "x-btn-rigth",
    handler: function () {
      win.destroy();
    }
  });

  formPanel = Ext.create("Ext.form.Panel", {
    bodyPadding: 5,
    waitMsgTarget: true,
    layout: "column",
    fieldDefaults: {
      labelAlign: "left",
      labelWidth: 150,
      msgTarget: "side"
    },
    items: [
      {
        xtype: "fieldset",
        title: "Seleccionar Elemento",
        autoHeight: true,
        width: 400,
        items: [comboServidores]
      }
    ]
  });

  win = Ext.create("Ext.window.Window", {
    title: "Asignar Elemento a Policy a eliminar",
    modal: true,
    width: 430,
    height: 150,
    resizable: false,
    layout: "fit",
    items: [formPanel],
    buttonAlign: "center",
    buttons: [btnguardar, btncancelar]
  }).show();
}

function limpiar() {
  Ext.getCmp("txtNombre").value = "";
  Ext.getCmp("txtNombre").setRawValue("");

  Ext.getCmp("txtCodigo").value = "";
  Ext.getCmp("txtCodigo").setRawValue("");

  Ext.getCmp("sltEstado").value = "Todos";
  Ext.getCmp("sltEstado").setRawValue("Todos");

  store.getProxy().extraParams.codigo = Ext.getCmp("txtCodigo").value;
  store.getProxy().extraParams.nombre = Ext.getCmp("txtNombre").value;
  store.getProxy().extraParams.estado = Ext.getCmp("sltEstado").value;
  store.load();
}

function eliminarAlgunos() {
  var param = "";
  var selection = grid.getPlugin("pagingSelectionPersistence").getPersistedSelection();

  if (selection.length > 0) {
    var estado = 0;
    for (var i = 0; i < selection.length; ++i) {
      param = param + selection[i].getId();

      if (i < selection.length - 1) {
        param = param + "|";
      }
    }

    if (estado == 0) {
      Ext.Msg.confirm("Alerta", "Se eliminaran los registros. Desea continuar?", function (btn) {
        if (btn == "yes") {
          Ext.Ajax.request({
            url: deleteAjax,
            method: "post",
            params: {
              param: param
            },
            success: function (response) {
              var text = response.responseText;
              store.load();
            },
            failure: function (result) {
              Ext.Msg.alert("Error ", "Error: " + result.statusText);
            }
          });
        }
      });
    } else {
      alert("Por lo menos uno de las registro se encuentra en estado ELIMINADO");
    }
  } else {
    alert("Seleccione por lo menos un registro de la lista");
  }
}
