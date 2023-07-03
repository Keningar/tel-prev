Ext.onReady(function () {
  Ext.tip.QuickTipManager.init();

  Ext.define("ModelStore", {
    extend: "Ext.data.Model",
    fields: [
      {
        name: "scope",
        mapping: "scope"
      }, {
        name: "prefixscope",
        mapping: "prefixscope"
      }, {
        name: "counttags",
        mapping: "counttags"
      }, {
        name: "estado",
        mapping: "estado"
      }, {
        name: "action1",
        mapping: "action1"
      }, {
        name: "action2",
        mapping: "action2"
      }, {
        name: "action3",
        mapping: "action3"
      }
    ]
  });

  store = new Ext.data.Store({
    model: "ModelStore",
    autoDestroy: true,
    autoLoad: true,
    proxy: {
      type: "ajax",
      url: getEncontrados,
      reader: {
        type: "json"
      },
      extraParams: {
        estado: "Activo"
      }
    }
  });

  grid = Ext.create("Ext.grid.Panel", {
    id: "grid",
    title: "Grupo Tags Asociados por Tipo Scope",
    width: 455,
    columnLines: true,
    height: 250,
    frame: true,
    store: store,
    columns: [
      {
        id: "scope",
        header: "Tipo Scope",
        dataIndex: "scope",
        width: 220,
        sortable: true
      }, {
        id: "prefixscope",
        header: "Prefijo Scope",
        dataIndex: "prefixscope",
        width: 90,
        sortable: true,
        hidden: true,
        hideable: true
      }, {
        id: "counttags",
        header: "#Tags",
        dataIndex: "counttags",
        width: 90,
        sortable: true,
        hidden: true,
        hideable: true
      }, {
        header: "estado",
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
              window.location = rec.get("prefixscope") + "/show";

            }
          },{
            getClass: function (v, meta, rec) {
              this.items[1].tooltip = "Editar";
              return rec.get("action2");
            },
            tooltip: "Editar",
            handler: function(grid, rowIndex, colIndex) {
              var rec = store.getAt(rowIndex);
              window.location = rec.get("prefixscope") + "/edit"              
            }
          }, {
            getClass: function (v, meta, rec) {
              var permiso = $("#ROLE_461-1");
              var boolPermiso = typeof permiso === "undefined"
                ? false
                : permiso.val() == 1
                  ? true
                  : false;
              if (!boolPermiso) {
                this.items[2].tooltip = "";
              } else {
                this.items[2].tooltip = "Eliminar";
              }
              return rec.get("action3");
            },
            handler: function (grid, rowIndex, colIndex) {
              var rec = store.getAt(rowIndex);

              var permiso = $("#ROLE_461-1");
              var boolPermiso = typeof permiso === "undefined"
                ? false
                : permiso.val() == 1
                  ? true
                  : false;
              if (!boolPermiso) {
                Ext.Msg.alert("Error ", "No tiene permisos para realizar esta accion");
              } else {
                //eliminar(rec);
                Ext.Msg.confirm("Alerta", "Se eliminara el registro. Desea continuar?", function (btn) {
                  if (btn == "yes") {
                    Ext.Ajax.request({
                      url: "deleteAjax",
                      method: "post",
                      params: {
                        param: rec.get("scope")
                      },
                      success: function (response) {
                        var msgText = response.responseText;
                        console.log(msgText);
                        store.load();
                      },
                      failure: function (result) {
                        Ext.Msg.alert("Error ", "Error: " + result.statusText);
                      }
                    });
                  }
                });
              }
            }
          }
        ]
      }
    ],
    renderTo: "grid"
  });
});
