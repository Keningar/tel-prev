Ext.require([
  "Ext.form.*",
  "Ext.grid.*",
  "Ext.data.*",
  "Ext.dd.*",
  //'extjs.SlidingPager'
]);
//array que contiene el titulo de las ventanas de mensajes
var strObjTags;
var arrayTituloMensajeBox = [];
arrayTituloMensajeBox["100"] = "Información";
arrayTituloMensajeBox["001"] = "Error";
arrayTituloMensajeBox["000"] = "Alerta";
var arrayIdsOltsSelecccionadosGrid = {};
var cantidadMaxOltsSeleccionados = 20;
var permiteEventosCheck = true;
var connGrabandoDatos = new Ext.data.Connection({
  listeners: {
    beforerequest: {
      fn: function (con, opt) {
        Ext.MessageBox.show({
          msg: "Grabando los datos, Por favor espere!!",
          progressText: "Saving...",
          width: 300,
          wait: true,
          waitConfig: { interval: 200 },
        });
      },
      scope: this,
    },
    requestcomplete: {
      fn: function (con, res, opt) {
        Ext.MessageBox.hide();
      },
      scope: this,
    },
    requestexception: {
      fn: function (con, res, opt) {
        Ext.MessageBox.hide();
      },
      scope: this,
    },
  },
});

var connConsultandoDatosIps = new Ext.data.Connection({
  listeners: {
    beforerequest: {
      fn: function (con, opt) {
        Ext.MessageBox.show({
          msg: "Consultando los datos de Ips, Por favor espere!!",
          progressText: "Saving...",
          width: 300,
          wait: true,
          waitConfig: { interval: 200 },
        });
      },
      scope: this,
    },
    requestcomplete: {
      fn: function (con, res, opt) {
        Ext.MessageBox.hide();
      },
      scope: this,
    },
    requestexception: {
      fn: function (con, res, opt) {
        Ext.MessageBox.hide();
      },
      scope: this,
    },
  },
});

var connEjecutandoOperacion = new Ext.data.Connection({
  listeners: {
    beforerequest: {
      fn: function (con, opt) {
        Ext.MessageBox.show({
          msg: "Ejecutando operación, Por favor espere!!",
          progressText: "Ejecutando...",
          width: 300,
          wait: true,
          waitConfig: { interval: 200 },
        });
      },
      scope: this,
    },
    requestcomplete: {
      fn: function (con, res, opt) {
        Ext.MessageBox.hide();
      },
      scope: this,
    },
    requestexception: {
      fn: function (con, res, opt) {
        Ext.MessageBox.hide();
      },
      scope: this,
    },
  },
});

Ext.onReady(function () {
  Ext.tip.QuickTipManager.init();

  Ext.define("estados", {
    extend: "Ext.data.Model",
    fields: [
      { name: "opcion", type: "string" },
      { name: "valor", type: "string" },
    ],
  });

  comboEstados = new Ext.data.Store({
    model: "estados",
    data: [
      { opcion: "Libre", valor: "not connect" },
      { opcion: "Ocupado", valor: "connected" },
      { opcion: "Dañado", valor: "err-disabled" },
      { opcion: "Inactivo", valor: "disabled" },
      { opcion: "Reservado", valor: "reserved" },
      { opcion: "Factible", valor: "Factible" },
    ],
  });

  var storeMarcas = new Ext.data.Store({
    total: "total",
    proxy: {
      type: "ajax",
      url: "../../../administracion/tecnico/admi_marca_elemento/getMarcasElementosTipo",
      extraParams: {
        tipoElemento: "OLT",
      },
      reader: {
        type: "json",
        totalProperty: "total",
        root: "encontrados",
      },
    },
    fields: [
      { name: "nombreMarcaElemento", mapping: "nombreMarcaElemento" },
      { name: "idMarcaElemento", mapping: "idMarcaElemento" },
    ],
  });

  storeModelos = new Ext.data.Store({
    total: "total",
    proxy: {
      type: "ajax",
      url: "../../../administracion/tecnico/admi_modelo_elemento/getModelosElementosPorMarca",
      extraParams: {
        idMarca: "",
        tipoElemento: "OLT",
      },
      reader: {
        type: "json",
        totalProperty: "total",
        root: "encontrados",
      },
    },
    fields: [
      { name: "nombreModeloElemento", mapping: "nombreModeloElemento" },
      { name: "idModeloElemento", mapping: "idModeloElemento" },
    ],
  });

  var storeCantones = new Ext.data.Store({
    total: "total",
    proxy: {
      type: "ajax",
      url: "../../../administracion/general/admi_canton/getCantones",
      reader: {
        type: "json",
        totalProperty: "total",
        root: "encontrados",
      },
    },
    fields: [
      { name: "nombre_canton", mapping: "nombre_canton" },
      { name: "id_canton", mapping: "id_canton" },
    ],
  });

  var storeJurisdicciones = new Ext.data.Store({
    total: "total",
    proxy: {
      type: "ajax",
      url: "../../../administracion/tecnico/admi_jurisdiccion/getJurisdicciones",
      reader: {
        type: "json",
        totalProperty: "total",
        root: "encontrados",
      },
    },
    fields: [
      { name: "nombreJurisdiccion", mapping: "nombreJurisdiccion" },
      { name: "idJurisdiccion", mapping: "idJurisdiccion" },
    ],
  });

  var storeNodo = new Ext.data.Store({
    total: "total",
    autoLoad: false,
    proxy: {
      timeout: 400000,
      type: "ajax",
      url: "../../elemento/nodo/getEncontradosNodo",
      extraParams: {
        nombreElemento: "",
        modeloElemento: "",
        marcaElemento: "",
        canton: "",
        jurisdiccion: "",
        esMultiplataforma: "SI",
        estado: "Todos",
      },
      reader: {
        type: "json",
        totalProperty: "total",
        root: "encontrados",
      },
      limitParam: undefined,
      startParam: undefined,
    },
    fields: [
      { name: "idElemento", mapping: "idElemento" },
      { name: "nombreElemento", mapping: "nombreElemento" },
    ],
  });

  store = new Ext.data.Store({
    pageSize: 10,
    total: "total",
    proxy: {
      type: "ajax",
      timeout: 900000,
      url: "getEncontradosOlt",
      reader: {
        type: "json",
        totalProperty: "total",
        root: "encontrados",
      },
      extraParams: {
        nombreElemento: "",
        ipElemento: "",
        marcaElemento: "",
        modeloElemento: "",
        canton: "",
        jurisdiccion: "",
        popElemento: "",
        estado: "Todos",
        booleanMultiplataforma: true,
      },
    },
    fields: [
      { name: "idElemento", mapping: "idElemento" },
      { name: "nombreElemento", mapping: "nombreElemento" },
      { name: "nombreElementoNodo", mapping: "nombreElementoNodo" },
      { name: "tipoElemento", mapping: "tipoElemento" },
      { name: "ipElemento", mapping: "ipElemento" },
      { name: "cantonNombre", mapping: "cantonNombre" },
      { name: "jurisdiccionNombre", mapping: "jurisdiccionNombre" },
      { name: "switchTelconet", mapping: "switchTelconet" },
      { name: "puertoSwitch", mapping: "puertoSwitch" },
      { name: "marcaElemento", mapping: "marcaElemento" },
      { name: "modeloElemento", mapping: "modeloElemento" },
      { name: "idTipoElemento", mapping: "idTipoElemento" },
      { name: "estado", mapping: "estado" },
      { name: "action1", mapping: "action1" },
      { name: "action2", mapping: "action2" },
      { name: "action3", mapping: "action3" },
      { name: "action5", mapping: "action5" },
      { name: "botonOperatividad", mapping: "botonOperatividad" },
      { name: "strAprovisionamientoIp", mapping: "strAprovisionamientoIp" },
      { name: "botonMigracion", mapping: "botonMigracion" },
      { name: "botonCambioPlanMasivo", mapping: "botonCambioPlanMasivo" },
      { name: "strmigracionHuaweiZte", mapping: "strmigracionHuaweiZte" },
      { name: "strOltMultiplataforma", mapping: "strOltMultiplataforma" },
      {
        name: "strEstadoSolMultiplataforma",
        mapping: "strEstadoSolMultiplataforma",
      },
      { name: "idEmpresa", mapping: "idEmpresa" },
    ],
  });

  var pluginExpanded = true;

  sm = Ext.create("Ext.selection.CheckboxModel", {
    checkOnly: true,
    onHeaderClick: function (headerCt, header, e) {
      if (header.isCheckerHd) {
        e.stopEvent();
        var isChecked = header.el.hasCls(
          Ext.baseCSSPrefix + "grid-hd-checker-on"
        );
        if (isChecked) {
          this.deselectAll(true);
        } else {
          this.selectAll(true);
          var view = this.views[0];
          var store = view.getStore();
          var model = view.getSelectionModel();
          var registrosNuevosSeleccionados = [];
          var registrosYaSeleccionados = [];
          var numOltsSeleccionados = Object.keys(
            arrayIdsOltsSelecccionadosGrid
          ).length;
          store.queryBy(function (record) {
            //Se almacena todos los olts que ya están seleccionados previamente al dar click en el checkbox de la cabecera
            if (
              record.get("estado") !== "Eliminado" &&
              arrayIdsOltsSelecccionadosGrid[record.get("idElemento")] == true
            ) {
              registrosYaSeleccionados.push(record);
            }
          });
          var seleccionPermitida = true;
          permiteEventosCheck = false;
          store.queryBy(function (record) {
            if (seleccionPermitida) {
              if (
                record.get("estado") !== "Eliminado" &&
                arrayIdsOltsSelecccionadosGrid[record.get("idElemento")] != true
              ) {
                if (numOltsSeleccionados + 1 <= cantidadMaxOltsSeleccionados) {
                  registrosNuevosSeleccionados.push(record);
                  numOltsSeleccionados++;
                } else {
                  seleccionPermitida = false;
                  registrosNuevosSeleccionados = [];
                }
              }
            }
          });
          Array.prototype.push.apply(
            registrosYaSeleccionados,
            registrosNuevosSeleccionados
          );
          model.select(registrosYaSeleccionados);
          if (!seleccionPermitida) {
            Ext.Msg.alert(
              "Alerta",
              "No se permite seleccionar más de " +
                cantidadMaxOltsSeleccionados +
                " olts"
            );
          } else {
            for (
              var contRegistrosNuevos = 0;
              contRegistrosNuevos < registrosNuevosSeleccionados.length;
              contRegistrosNuevos++
            ) {
              var recordNuevo =
                registrosNuevosSeleccionados[contRegistrosNuevos];
              arrayIdsOltsSelecccionadosGrid[
                recordNuevo.data.idElemento
              ] = true;
            }
            Ext.getCmp("contadorOltsSeleccionados").setValue(
              Object.keys(arrayIdsOltsSelecccionadosGrid).length
            );
            Ext.getCmp("contadorOltsSeleccionados").setRawValue(
              Object.keys(arrayIdsOltsSelecccionadosGrid).length
            );
            Ext.getCmp("contadorOltsSeleccionados").show();
          }
        }
      }
    },
    restoreSelection: function () {
      this.store.each(function (i) {
        if (arrayIdsOltsSelecccionadosGrid[i.data.idElemento] == true) {
          this.select(i, true, true);
        }
      }, this);
      this.page = this.store.currentPage;
      Ext.getCmp("contadorOltsSeleccionados").setValue(
        Object.keys(arrayIdsOltsSelecccionadosGrid).length
      );
      Ext.getCmp("contadorOltsSeleccionados").setRawValue(
        Object.keys(arrayIdsOltsSelecccionadosGrid).length
      );
    },
    listeners: {
      select: function (selectionModel, record, index, eOpts) {
        if (record.data.estado == "Eliminado") {
          this.deselect(index);
          Ext.Msg.alert(
            "Alerta",
            "No se permite seleccionar Olts en estado Eliminado " +
              "para generar la migración a Kaspersky de clientes con Internet Protegido"
          );
        } else if (
          Object.keys(arrayIdsOltsSelecccionadosGrid).length ==
            cantidadMaxOltsSeleccionados &&
          permiteEventosCheck
        ) {
          this.deselect(index);
          Ext.Msg.alert(
            "Alerta",
            "No se permite seleccionar más de " +
              cantidadMaxOltsSeleccionados +
              " olts"
          );
        }
      },
      selectionchange: function (selectionModel, selectedRecords, options) {
        //No cambiar la selección en el cambio de página
        if (
          selectedRecords.length == 0 &&
          this.store.loading == true &&
          this.store.currentPage != this.page
        ) {
          return;
        }

        if (permiteEventosCheck) {
          //Eliminar selección anterior de la página actual
          this.store.each(function (i) {
            delete arrayIdsOltsSelecccionadosGrid[i.data.idElemento];
          }, this);

          //Seleccionar los registros
          Ext.each(
            selectedRecords,
            function (i) {
              if (
                i.data.estado != "Eliminado" &&
                Object.keys(arrayIdsOltsSelecccionadosGrid).length <
                  cantidadMaxOltsSeleccionados
              ) {
                arrayIdsOltsSelecccionadosGrid[i.data.idElemento] = true;
              }
            },
            this
          );
          Ext.getCmp("contadorOltsSeleccionados").setValue(
            Object.keys(arrayIdsOltsSelecccionadosGrid).length
          );
          Ext.getCmp("contadorOltsSeleccionados").setRawValue(
            Object.keys(arrayIdsOltsSelecccionadosGrid).length
          );
          Ext.getCmp("contadorOltsSeleccionados").show();
        }
        permiteEventosCheck = true;
      },
      buffer: 5,
    },
  });

  grid = Ext.create("Ext.grid.Panel", {
    width: "100%",
    height: 350,
    store: store,
    loadMask: true,
    frame: false,
    selModel: sm,
    viewConfig: { enableTextSelection: true },
    iconCls: "icon-grid",
    dockedItems: [
      {
        xtype: "toolbar",
        dock: "top",
        align: "->",
        hidden: idEmpresa === '33' ? true : false,
        items: [
          { xtype: "tbfill" },
          {
            text: "Subir Policy/Scopes Masivo",
            cls: "button-docked-items-custom",
            itemId: "subir",
            scope: this,
            handler: function () {
              var permiso = $("#ROLE_227-7977");
              var boolPermiso =
                typeof permiso === "undefined"
                  ? false
                  : permiso.val() == 1
                  ? true
                  : false;
              if (!boolPermiso) {
                Ext.Msg.alert(
                  "Error ",
                  "No tiene permisos para realizar esta accion"
                );
              } else {
                subir();
              }
            },
          },
          {
            text: "Generar Migración a Kaspersky",
            cls: "button-docked-items-custom",
            itemId: "migrar",
            scope: this,
            handler: function () {
              var permiso = $("#ROLE_227-6837");
              var boolPermiso =
                typeof permiso === "undefined"
                  ? false
                  : permiso.val() == 1
                  ? true
                  : false;
              if (!boolPermiso) {
                Ext.Msg.alert(
                  "Error ",
                  "No tiene permisos para realizar esta accion"
                );
              } else {
                migrar();
              }
            },
          },
          {
            xtype: "textfield",
            id: "contadorOltsSeleccionados",
            name: "contadorOltsSeleccionados",
            value: "0",
            hidden: true,
            readOnly: true,
            width: 25,
            listeners: {
              render: function (c) {
                c.inputEl.setStyle("text-align", "center");
                Ext.QuickTips.register({
                  target: c.getEl(),
                  text: "# de Olts seleccionados",
                  enabled: true,
                  showDelay: 20,
                  trackMouse: true,
                  autoShow: true,
                });
              },
            },
          },
        ],
      },
    ],
    columns: [
      {
        id: "idElemento",
        header: "idElemento",
        dataIndex: "idElemento",
        hidden: true,
        hideable: false,
      },
      {
        id: "ipElemento",
        header: "Olt",
        xtype: "templatecolumn",
        width: "25%",
        tpl: '<span class="box-detalle">{nombreElemento}</span>\n\
                        <span class="bold">Ip:</span><span>{ipElemento}</span></br>\n\
                        <span class="bold">Jurisdiccion:</span><span>{jurisdiccionNombre}</span></br>\n\
                        <span class="bold">Canton:</span><span>{cantonNombre}</span></br>\n\\n\
                        <span class="bold">{tipoElemento}</span><span>{nombreElementoNodo}</span></br>\n\
                        <span class="bold">Tecnologia destino:</span><span>{strmigracionHuaweiZte}</span></br>\n\
                        <tpl if="switchTelconet!=\'N/A\'">\n\
                            <!--<span class="bold">Switch:</span>{switchTelconet}</br>--> \n\
                            <!--<span class="bold">Puerto:</span>{puertoSwitch}-->\n\
                        </tpl>',
      },
      {
        header: "Marca",
        dataIndex: "marcaElemento",
        width: "6%",
        sortable: true,
      },
      {
        header: "Modelo",
        dataIndex: "modeloElemento",
        width: "6%",
        sortable: true,
      },
      {
        header: "Estado",
        dataIndex: "estado",
        width: "6%",
        sortable: true,
      },
      {
        header: "Olt Multiplataforma",
        dataIndex: "strOltMultiplataforma",
        width: "11%",
        sortable: true,
      },
      {
        header: "Estado Multiplataforma",
        dataIndex: "strEstadoSolMultiplataforma",
        width: "13%",
        sortable: true,
      },
      {
        xtype: "actioncolumn",
        header: "Acciones",
        width: "45%",
        items: [
          //VER OLT
          {
            getClass: function (v, meta, rec) {
              if (idEmpresa != rec.get("idEmpresa")) {
                return "button-grid-invisible";
              } else {
                return "button-grid-show";
              }
            },
            tooltip: "Ver",
            handler: function (grid, rowIndex, colIndex) {
              var rec = store.getAt(rowIndex);
              window.location = "" + rec.get("idElemento") + "/showOlt";
            },
          },
          //EDITAR OLT
          {
            getClass: function (v, meta, rec) {
              var permiso = $("#ROLE_227-4");
              var boolPermiso =
                typeof permiso === "undefined"
                  ? false
                  : permiso.val() == 1
                  ? true
                  : false;
              if (!boolPermiso || idEmpresa != rec.get("idEmpresa")) {
                return "button-grid-invisible";
              } else {
                if (rec.get("action2") == "button-grid-invisible")
                  this.items[1].tooltip = "";
                else this.items[1].tooltip = "Editar";
              }

              return rec.get("action2");
            },
            handler: function (grid, rowIndex, colIndex) {
              var rec = store.getAt(rowIndex);
              if (rec.get("action2") != "button-grid-invisible")
                window.location = "" + rec.get("idElemento") + "/editOlt";
            },
          },
          //ELIMINAR OLT
          {
            getClass: function (v, meta, rec) {
              var permiso = $("#ROLE_227-8");
              var boolPermiso =
                typeof permiso === "undefined"
                  ? false
                  : permiso.val() == 1
                  ? true
                  : false;
              //alert(typeof permiso);
              if (!boolPermiso || idEmpresa != rec.get("idEmpresa")) {
                return "button-grid-invisible";
              } else {
                if (rec.get("action3") == "button-grid-invisible")
                  this.items[2].tooltip = "";
                else this.items[2].tooltip = "Eliminar";
              }

              return rec.get("action3");
            },
            handler: function (grid, rowIndex, colIndex) {
              var rec = store.getAt(rowIndex);
              if (rec.get("action3") != "button-grid-invisible")
                Ext.Msg.confirm(
                  "Alerta",
                  "Se eliminara el registro. Desea continuar?",
                  function (btn) {
                    if (btn == "yes") {
                      Ext.Ajax.request({
                        url: "" + rec.get("idElemento") + "/deleteOlt",
                        method: "post",
                        params: { param: rec.get("idElemento") },
                        success: function (response) {
                          var text = response.responseText;
                          if (text == "SERVICIOS ACTIVOS") {
                            Ext.Msg.alert(
                              "Mensaje",
                              "NO SE PUEDE ELIMINAR EL ELEMENTO PORQUE AUN EXISTEN <BR> SERVICIOS ACTIVOS, FAVOR REVISAR!",
                              function (btn) {
                                if (btn == "ok") {
                                  store.load();
                                }
                              }
                            );
                          } else if (text == "ELEMENTOS ENLAZADOS") {
                            Ext.Msg.alert(
                              "Mensaje",
                              "NO SE PUEDE ELIMINAR EL ELEMENTO PORQUE AUN EXISTEN" +
                                " <BR> ENLACES DE ELEMENTOS ACTIVOS, FAVOR REVISAR!",
                              function (btn) {
                                if (btn == "ok") {
                                  store.load();
                                }
                              }
                            );
                          } else {
                            store.load();
                          }
                        },
                        failure: function (result) {
                          Ext.Msg.alert(
                            "Error ",
                            "Error: " + result.statusText
                          );
                        },
                      });
                    }
                  }
                );
            },
          },
          //VER SUBSCRIBERS
          {
            getClass: function (v, meta, rec) {
              if (
                rec.get("estado") != "Eliminado" &&
                idEmpresa == rec.get("idEmpresa")
              ) {
                if (rec.get("modeloElemento") == "EP-3116") {
                  return "button-grid-verDslam";
                }
              } else {
                return "button-grid-invisible";
              }
            },
            tooltip: "Ver Subscribers",
            handler: function (grid, rowIndex, colIndex) {
              var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
              if (grid.getStore().getAt(rowIndex).data.estado != "Eliminado") {
                if (modelo == "EP-3116") {
                  verScriptDslam(
                    grid.getStore().getAt(rowIndex).data,
                    "mostrarSubscriberOlt"
                  );
                } else {
                  alert("No existe Accion para este Elemento");
                }
              }
            },
          },
          //ACTUALIZAR POOL IP
          {
            getClass: function (v, meta, rec) {
              if (
                rec.get("estado") != "Eliminado" &&
                idEmpresa == rec.get("idEmpresa")
              ) {
                if (rec.get("modeloElemento") == "EP-3116") {
                  var permiso = $("#ROLE_227-1877");
                  var boolPermiso =
                    typeof permiso === "undefined"
                      ? false
                      : permiso.val() == 1
                      ? true
                      : false;
                  if (!boolPermiso) {
                    return "button-grid-invisible";
                  } else {
                    return "button-grid-edit-perfiles";
                  }
                }
              } else {
                return "button-grid-invisible";
              }
            },
            tooltip: "Actualizar Pool Ip",
            handler: function (grid, rowIndex, colIndex) {
              var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
              if (grid.getStore().getAt(rowIndex).data.estado != "Eliminado") {
                if (modelo == "EP-3116") {
                  actualizarPoolIp(grid.getStore().getAt(rowIndex).data);
                } else {
                  alert("No existe Accion para este Elemento");
                }
              }
            },
          },
          //VER DETALLE ELEMENTO
          {
            getClass: function (v, meta, rec) {
              if (idEmpresa != rec.get("idEmpresa")) {
                return "button-grid-invisible";
              } else {
                return "button-grid-editarPerfil";
              }
            },
            tooltip: "Ver detalle elemento",
            handler: function (grid, rowIndex, colIndex) {
              var objElemento = store.getAt(rowIndex);
              showDetalleElemento(objElemento);
            },
          },
          //CORREGIR DOBLE LINEA PON
          {
            getClass: function (v, meta, rec) {
              if (
                rec.get("estado") != "Eliminado" &&
                idEmpresa == rec.get("idEmpresa")
              ) {
                if (rec.get("modeloElemento") == "EP-3116") {
                  return "button-grid-verInterfacesPorPuerto";
                }
              } else {
                return "button-grid-invisible";
              }
            },
            tooltip: "Eliminar Doble Linea Pon",
            handler: function (grid, rowIndex, colIndex) {
              var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
              if (grid.getStore().getAt(rowIndex).data.estado != "Eliminado") {
                if (modelo == "EP-3116") {
                  eliminarDobleLineaPon(
                    grid.getStore().getAt(rowIndex).data,
                    "eliminarDobleLineaPon"
                  );
                } else {
                  alert("No existe Accion para este Elemento");
                }
              }
            },
          },
          //ELIMINAR IP FIJA
          {
            getClass: function (v, meta, rec) {
              if (
                rec.get("estado") != "Eliminado" &&
                idEmpresa == rec.get("idEmpresa")
              ) {
                if (rec.get("modeloElemento") == "EP-3116") {
                  var permiso = $("#ROLE_227-837");
                  var boolPermiso =
                    typeof permiso === "undefined"
                      ? false
                      : permiso.val() == 1
                      ? true
                      : false;
                  if (!boolPermiso) {
                    return "button-grid-invisible";
                  } else {
                    return "button-grid-eliminarIpPublica";
                  }
                }
              } else {
                return "button-grid-invisible";
              }
            },
            tooltip: "Eliminar Ip Fija",
            handler: function (grid, rowIndex, colIndex) {
              var modelo = grid.getStore().getAt(rowIndex).data.modeloElemento;
              if (grid.getStore().getAt(rowIndex).data.estado != "Eliminado") {
                if (modelo == "EP-3116") {
                  eliminarIpFija(
                    grid.getStore().getAt(rowIndex).data,
                    "desconfigurarIpFija"
                  );
                } else {
                  alert("No existe Accion para este Elemento");
                }
              }
            },
          },
          //ADMINISTRAR POOL DE IPS
          {
            getClass: function (v, meta, rec) {
              if (
                "EP-3116" == rec.get("modeloElemento") &&
                idEmpresa == rec.get("idEmpresa")
              ) {
                var permiso = $("#ROLE_151-1127");
                let _boolPermiso = false;
                _boolPermiso = typeof permiso === "undefined"
                    ? false
                    : permiso.val() == 1
                    ? true
                    : false;
                _boolPermiso = true;
                if (!_boolPermiso) {
                  return "button-grid-invisible";
                } else {
                  return "button-grid-editarDireccion";
                }
              }
            },
            tooltip: "Pool de IP",
            handler: function (grid, rowIndex, colIndex) {
              var row = store.getAt(rowIndex);
              showPoolIP(row);
            },
          },
          //ADMINISTRAR SCOPES
          {
            getClass: function (v, meta, rec) {
              var strBtnScope = "";
              var permiso = $("#ROLE_227-2237");
              var boolPermiso =
                typeof permiso === "undefined"
                  ? false
                  : permiso.val() == 1
                  ? true
                  : false;
              if (!boolPermiso || idEmpresa != rec.get("idEmpresa")) {
                strBtnScope = "button-grid-invisible";
              } else {
                strBtnScope = "button-grid-administrarScopes";
              }
              return strBtnScope;
            },
            tooltip: "Scopes",
            handler: function (grid, rowIndex, colIndex) {
              var objElemento = store.getAt(rowIndex);
              showScopes(objElemento);
            },
          },
          //RESERVAR IPS
          {
            getClass: function (v, meta, rec) {
              var permiso = $("#ROLE_227-2537");
              var boolPermiso =
                typeof permiso === "undefined"
                  ? false
                  : permiso.val() == 1
                  ? true
                  : false;
              if (
                "CNR" == rec.get("strAprovisionamientoIp") &&
                "EP-3116" === rec.get("modeloElemento") &&
                idEmpresa == rec.get("idEmpresa")
              ) {
                if (!boolPermiso) {
                  return "button-grid-invisible";
                } else {
                  if (rec.get("action5") == "button-grid-invisible")
                    this.items[9].tooltip = "";
                  else this.items[9].tooltip = "Reservar Ips";
                }
              } else {
                return "button-grid-invisible";
              }
              return rec.get("action5");
            },
            tooltip: "Reserva IPS Migración",
            handler: function (grid, rowIndex, colIndex) {
              var rec = store.getAt(rowIndex);
              if (rec.get("action5") != "button-grid-invisible")
                reservarIps(grid.getStore().getAt(rowIndex).data);
            },
          },
          //ACTUALIZAR CARACTERISTICAS PARA OLT HUAWEI (LINE-PROFILE, SERVICE-PROFILE, GEMPORTS, TRAFFIC-TABLE)
          {
            getClass: function (v, meta, rec) {
              var strBtnScope = "";
              var permiso = $("#ROLE_227-2457");
              var boolPermiso =
                typeof permiso === "undefined"
                  ? false
                  : permiso.val() == 1
                  ? true
                  : false;
              if (!boolPermiso) {
                strBtnScope = "button-grid-invisible";
              } else {
                if (
                  strEmpresaSesion != "TNP" &&
                  idEmpresa == rec.get("idEmpresa")
                ) {
                  strBtnScope = "button-grid-edit-perfiles";
                } else {
                  strBtnScope = "button-grid-invisible";
                }
              }
              return strBtnScope;
            },
            tooltip: "Actualizar Caracteristicas",
            handler: function (grid, rowIndex, colIndex) {
              var objElemento = store.getAt(rowIndex);
              actualizarCaracteristicas(grid.getStore().getAt(rowIndex).data);
            },
          },
          //DEJAR SIN OPERATIVIDAD EL OLT
          {
            getClass: function (v, meta, rec) {
              strBtnOperativo = "button-grid-invisible";
              if (
                rec.get("botonOperatividad") != "Eliminado" &&
                idEmpresa == rec.get("idEmpresa")
              ) {
                var permiso = $("#ROLE_227-2777");
                var boolPermiso =
                  typeof permiso === "undefined"
                    ? false
                    : permiso.val() == 1
                    ? true
                    : false;
                if (!boolPermiso) {
                  strBtnOperativo = "button-grid-invisible";
                } else {
                  if (strEmpresaSesion != "TNP") {
                    strBtnOperativo = "button-grid-cancelarCliente";
                  } else {
                    strBtnOperativo = "button-grid-invisible";
                  }
                }
              }

              return strBtnOperativo;
            },
            tooltip: "Dejar sin Operatividad",
            handler: function (grid, rowIndex, colIndex) {
              var objElemento = store.getAt(rowIndex);
              if (objElemento.get("botonOperatividad") == "SI") {
                Ext.Msg.confirm(
                  "Alerta",
                  "Se dejará al Olt sin Operativdad. Desea continuar?",
                  function (btn) {
                    if (btn == "yes") {
                      connGrabandoDatos.request({
                        timeout: 900000,
                        url: url_QuitarOperatividad,
                        method: "post",
                        params: {
                          idElemento: grid.getStore().getAt(rowIndex).data
                            .idElemento,
                        },
                        success: function (response) {
                          var text = response.responseText;

                          if (text == "PROBLEMAS TRANSACCION") {
                            Ext.Msg.alert(
                              "Error ",
                              "Existieron problemas al realizar la transaccion, " +
                                "favor notificar a sistemas"
                            );
                          } else {
                            Ext.Msg.alert(
                              "Mensaje",
                              "Operación realizada con exito",
                              function (btn) {
                                if (btn == "ok") {
                                  store.load();
                                }
                              }
                            );
                          }
                        },
                        failure: function (result) {
                          Ext.Msg.alert(
                            "Error ",
                            "Error: " + result.statusText
                          );
                        },
                      });
                    }
                  }
                );
              } else {
                Ext.Msg.alert(
                  "Mensaje ",
                  "Este elemento ya se encuentra marcado SIN OPERATIVIDAD."
                );
              }
            },
          },
          //ASIGNAR SCOPES
          {
            getClass: function (v, meta, rec) {
              var strBtnScope = "";
              var permiso = $("#ROLE_227-2237");
              var boolPermiso =
                typeof permiso === "undefined"
                  ? false
                  : permiso.val() == 1
                  ? true
                  : false;
              if (!boolPermiso) {
                strBtnScope = "button-grid-invisible";
              } else {
                if (
                  strEmpresaSesion != "TNP" &&
                  idEmpresa == rec.get("idEmpresa")
                ) {
                  strBtnScope = "button-grid-cambioCpe";
                } else {
                  strBtnScope = "button-grid-invisible";
                }
              }
              return strBtnScope;
            },
            tooltip: "AsignarScopes",
            handler: function (grid, rowIndex, colIndex) {
              var objElemento = store.getAt(rowIndex);
              showAsignarScopes(objElemento);
            },
          },
          //ADMINISTRAR TARJETAS
          {
            getClass: function (v, meta, rec) {
              strBtnOperativo = "button-grid-invisible";
              const arrayMarcas = ["HUAWEI", "ZTE"];
              if (
                arrayMarcas.indexOf(rec.get("marcaElemento")) != -1 &&
                idEmpresa == rec.get("idEmpresa")
              ) {
                var permiso = $("#ROLE_227-6");
                var boolPermiso =
                  typeof permiso === "undefined"
                    ? false
                    : permiso.val() == 1
                    ? true
                    : false;
                if (!boolPermiso) {
                  strBtnOperativo = "button-grid-invisible";
                } else {
                  if (strEmpresaSesion != "TNP") {
                    strBtnOperativo = "button-grid-administraTarjeta";
                  } else {
                    strBtnOperativo = "button-grid-invisible";
                  }
                }
              }
              return strBtnOperativo;
            },
            tooltip: "Administrar Tarjetas",
            handler: function (grid, rowIndex, colIndex) {
              var objElemento = store.getAt(rowIndex);
              administrarTarjetas(grid.getStore().getAt(rowIndex).data);
            },
          },
          //Olt Aprovisiona con CNR
          {
            getClass: function (v, meta, rec) {
              var strBtnScope = "";
              if (
                "CNR" !== rec.get("strAprovisionamientoIp") &&
                "EP-3116" == rec.get("modeloElemento") &&
                idEmpresa == rec.get("idEmpresa")
              ) {
                var permiso = $("#ROLE_227-3177");
                var boolPermiso =
                  typeof permiso === "undefined"
                    ? false
                    : permiso.val() == 1
                    ? true
                    : false;
                if (!boolPermiso) {
                  strBtnScope = "button-grid-invisible";
                } else {
                  strBtnScope = "button-grid-Check";
                }
              }
              return strBtnScope;
            },
            tooltip: "Olt Aprovisiona con CNR",
            handler: function (grid, rowIndex, colIndex) {
              actualizarAprovisionamientoOlt(
                grid.getStore().getAt(rowIndex).data,
                "CNR",
                "APROVISIONAMIENTO_IP"
              );
            },
          },
          //Configurar Ip CNR TELLION manual
          {
            getClass: function (v, meta, rec) {
              var strBtnScope = "button-grid-invisible";
              var permiso = $("#ROLE_227-3197");
              var boolPermiso =
                typeof permiso === "undefined"
                  ? false
                  : permiso.val() == 1
                  ? true
                  : false;
              if (boolPermiso && idEmpresa == rec.get("idEmpresa")) {
                if (
                  "CNR" !== rec.get("strAprovisionamientoIp") &&
                  "EP-3116" === rec.get("modeloElemento")
                ) {
                  strBtnScope = "button-grid-agregarIpPublica";
                }
              }
              return strBtnScope;
            },
            tooltip: "Configura Ip CNR TELLION manual",
            handler: function (grid, rowIndex, colIndex) {
              configurarIpCnrTellionManual(
                grid.getStore().getAt(rowIndex).data
              );
            },
          },
          //Cambio de plan masivo
          {
            getClass: function (v, meta, rec) {
              var strBtnCambioPlanMasivo = rec.get("botonCambioPlanMasivo");

              if (
                (strEmpresaSesion == "TNP") || 
                (idEmpresa != rec.get("idEmpresa") && strEmpresaSesion != "EN")
              ) {
                strBtnCambioPlanMasivo = "button-grid-invisible";
              }

              return strBtnCambioPlanMasivo;
            },
            tooltip: "Iniciar Cambio de Plan Masivo",
            handler: function (grid, rowIndex, colIndex) {
              var strBtnCambioPlanMasivo = grid.getStore().getAt(rowIndex)
                .data.botonCambioPlanMasivo;

              if (strBtnCambioPlanMasivo == "button-grid-btnMigracion") {
                activarMigracionOlt(
                  grid.getStore().getAt(rowIndex).data.idElemento
                );
              }
            },
          },
        ],
      },
    ],
    bbar: Ext.create("Ext.PagingToolbar", {
      store: store,
      displayInfo: true,
      displayMsg: "Mostrando {0} - {1} de {2}",
      emptyMsg: "No hay datos que mostrar.",
    }),
    renderTo: "grid",
  });
  store.on(
    "load",
    grid.getSelectionModel().restoreSelection,
    grid.getSelectionModel()
  );
  
  let _filterPanel = Ext.create("Ext.panel.Panel", {
    bodyPadding: 7, // Don't want content to crunch against the borders
    border: false,
    buttonAlign: "center",
    layout: {
      type: "table",
      columns: 5,
      align: "stretch",
    },
    bodyStyle: {
      background: "#fff",
    },
    collapsible: true,
    collapsed: false,
    width: 930,
    title: "Criterios de busqueda",
    buttons: [
      {
        text: "Buscar",
        iconCls: "icon_search",
        handler: function () {
          buscar();
        },
      },
      {
        text: "Limpiar",
        iconCls: "icon_limpiar",
        handler: function () {
          limpiar();
        },
      },
    ],
    items: [
      { width: "10%", border: false },
      {
        xtype: "textfield",
        id: "txtNombre",
        fieldLabel: "Nombre",
        value: "",
        width: "200px",
      },
      { width: "20%", border: false },
      {
        xtype: "textfield",
        id: "txtIp",
        fieldLabel: "Ip",
        value: "",
        width: "200px",
      },
      { width: "10%", border: false },
      //-------------------------------------

      { width: "10%", border: false }, //inicio
      {
        id: "sltMarca",
        fieldLabel: "Marca",
        xtype: "combobox",
        store: storeMarcas,
        displayField: "nombreMarcaElemento",
        valueField: "idMarcaElemento",
        loadingText: "Buscando ...",
        queryMode: "local",
        listClass: "x-combo-list-small",
        listeners: {
          select: function (combo) {
            cargarModelos(combo.getValue());
          },
        }, //cierre listener
        width: "30%",
      },
      { width: "20%", border: false }, //medio
      {
        xtype: "combobox",
        id: "sltModelo",
        fieldLabel: "Modelo",
        store: storeModelos,
        displayField: "nombreModeloElemento",
        valueField: "idModeloElemento",
        loadingText: "Buscando ...",
        listClass: "x-combo-list-small",
        queryMode: "local",
        width: "30%",
      },
      { width: "10%", border: false }, //final

      //-------------------------------------

      { width: "10%", border: false }, //inicio
      {
        xtype: "combobox",
        id: "sltCanton",
        fieldLabel: "Canton",
        displayField: "nombre_canton",
        valueField: "id_canton",
        loadingText: "Buscando ...",
        store: storeCantones,
        listClass: "x-combo-list-small",
        queryMode: "local",
        width: "30%",
      },
      { width: "20%", border: false }, //medio
      {
        xtype: "combobox",
        id: "sltJurisdiccion",
        fieldLabel: "Jurisidiccion",
        store: storeJurisdicciones,
        displayField: "nombreJurisdiccion",
        valueField: "idJurisdiccion",
        loadingText: "Buscando ...",
        listClass: "x-combo-list-small",
        queryMode: "local",
        width: "30%",
      },
      { width: "10%", border: false }, //final

      //-------------------------------------

      { width: "10%", border: false }, //inicio
      {
        xtype: "combobox",
        fieldLabel: "Estado",
        id: "sltEstado",
        value: "Todos",
        store: [
          ["Todos", "Todos"],
          ["Activo", "Activo"],
          ["Modificado", "Modificado"],
          ["Eliminado", "Eliminado"],
        ],
        width: "30%",
      },
      { width: "20%", border: false }, //medio
      {
        xtype: "combobox",
        id: "sltNodo",
        fieldLabel: "Nodo",
        store: storeNodo,
        displayField: "nombreElemento",
        valueField: "idElemento",
        loadingText: "Buscando ...",
        listClass: "x-combo-list-small",
        queryMode: "remote",
        width: "30%",
      },
      { width: "10%", border: false }, //final

      //-------------------------------------

      { width: "10%", border: false }, //inicio
      {
        xtype: "combobox",
        fieldLabel: "Olt Multiplataforma",
        id: "sltMultiplataforma",
        value: "Todos",
        store: [
          ["Todos", "Todos"],
          ["SI", "SI"],
          ["NO", "NO"],
        ],
        width: "30%",
      },
      { width: "20%", border: false }, //medio
      {
        xtype: "combobox",
        fieldLabel: "Estado Multiplataforma",
        id: "sltEstadoMultiplataforma",
        value: "Todos",
        store: [
          ["Todos", "Todos"],
          ["Pendiente", "Pendiente"],
          ["Asignado", "Asignado"],
          ["Configurado", "Configurado"],
          ["Finalizada", "Finalizada"],
        ],
        width: "30%",
      },
      { width: "10%", border: false }, //final
    ],
    renderTo: "filtro",
  });

  store.load({
    callback: function () {
      storeMarcas.load({
        // store loading is asynchronous, use a load listener or callback to handle results
        callback: function () {
          storeModelos.load({
            callback: function () {
              storeCantones.load({
                callback: function () {
                  storeJurisdicciones.load({});
                },
              });
            },
          });
        },
      });
    },
  });
});

function cargarModelos(idParam) {
  storeModelos.proxy.extraParams = {
    idMarca: idParam,
    tipoElemento: "OLT",
    limite: 100,
  };
  storeModelos.load({ params: {} });
}

function buscar() {
  permiteEventosCheck = true;
  store.loadData([], false);
  store.currentPage = 1;
  store.getProxy().extraParams.nombreElemento = Ext.getCmp("txtNombre").value;
  store.getProxy().extraParams.ipElemento = Ext.getCmp("txtIp").value;
  store.getProxy().extraParams.marcaElemento = Ext.getCmp("sltMarca").value;
  store.getProxy().extraParams.modeloElemento = Ext.getCmp("sltModelo").value;
  store.getProxy().extraParams.canton = Ext.getCmp("sltCanton").value;
  store.getProxy().extraParams.jurisdiccion =
    Ext.getCmp("sltJurisdiccion").value;
  store.getProxy().extraParams.nodoElemento = Ext.getCmp("sltNodo").value;
  store.getProxy().extraParams.multiplataforma =
    Ext.getCmp("sltMultiplataforma").value;
  store.getProxy().extraParams.estadoMultiplataforma = Ext.getCmp(
    "sltEstadoMultiplataforma"
  ).value;
  store.getProxy().extraParams.booleanMultiplataforma = true;
  store.getProxy().extraParams.estado = Ext.getCmp("sltEstado").value;
  store.load();
}

function limpiar() {
  arrayIdsOltsSelecccionadosGrid = {};
  permiteEventosCheck = true;
  Ext.getCmp("txtNombre").value = "";
  Ext.getCmp("txtNombre").setRawValue("");

  Ext.getCmp("txtIp").value = "";
  Ext.getCmp("txtIp").setRawValue("");

  Ext.getCmp("sltMarca").value = "";
  Ext.getCmp("sltMarca").setRawValue("");

  Ext.getCmp("sltModelo").value = "";
  Ext.getCmp("sltModelo").setRawValue("");

  Ext.getCmp("sltCanton").value = "";
  Ext.getCmp("sltCanton").setRawValue("");

  Ext.getCmp("sltJurisdiccion").value = "";
  Ext.getCmp("sltJurisdiccion").setRawValue("");

  Ext.getCmp("sltNodo").value = "";
  Ext.getCmp("sltNodo").setRawValue("");

  Ext.getCmp("sltMultiplataforma").value = "Todos";
  Ext.getCmp("sltMultiplataforma").setRawValue("Todos");

  Ext.getCmp("sltEstadoMultiplataforma").value = "Todos";
  Ext.getCmp("sltEstadoMultiplataforma").setRawValue("Todos");

  Ext.getCmp("sltEstado").value = "Todos";
  Ext.getCmp("sltEstado").setRawValue("Todos");
  store.load({
    params: {
      nombreElemento: Ext.getCmp("txtNombre").value,
      ipElemento: Ext.getCmp("txtIp").value,
      marcaElemento: Ext.getCmp("sltMarca").value,
      modeloElemento: Ext.getCmp("sltModelo").value,
      canton: Ext.getCmp("sltCanton").value,
      jurisdiccion: Ext.getCmp("sltJurisdiccion").value,
      nodoElemento: Ext.getCmp("sltNodo").value,
      multiplataforma: Ext.getCmp("sltMultiplataforma").value,
      estadoMultiplataforma: Ext.getCmp("sltEstadoMultiplataforma").value,
      estado: Ext.getCmp("sltEstado").value,
      booleanMultiplataforma: true,
    },
  });
}

function eliminarAlgunos() {
  Ext.get(grid.getId()).mask("Eliminando Elementos...");
  var param = "";
  if (sm.getSelection().length > 0) {
    var estado = 0;
    for (var i = 0; i < sm.getSelection().length; ++i) {
      param = param + sm.getSelection()[i].data.idElemento;

      if (sm.getSelection()[i].data.estado == "Eliminado") {
        estado = estado + 1;
      }
      if (i < sm.getSelection().length - 1) {
        param = param + "|";
      }
    }
    if (estado == 0) {
      Ext.Msg.confirm(
        "Alerta",
        "Se eliminaran los registros. Desea continuar?",
        function (btn) {
          if (btn == "yes") {
            Ext.Ajax.request({
              url: "dslam/deleteAjaxNodo",
              method: "post",
              params: { param: param },
              success: function (response) {
                var text = response.responseText;

                if (text == "OK") {
                  Ext.Msg.alert(
                    "Mensaje",
                    "Se eliminaron los Elementos!",
                    function (btn) {
                      if (btn == "ok") {
                        Ext.get(grid.getId()).unmask();
                        store.load();
                      }
                    }
                  );
                } else if (text == "SERVICIOS ACTIVOS") {
                  Ext.Msg.alert(
                    "Mensaje",
                    "Uno o mas de los elementos aun posee servicios activos, <br> Favor revisar!",
                    function (btn) {
                      if (btn == "ok") {
                        Ext.get(grid.getId()).unmask();
                        store.load();
                      }
                    }
                  );
                }
              },
              failure: function (result) {
                Ext.Msg.alert("Error ", "Error: " + result.statusText);
              },
            });
          }
        }
      );
    } else {
      alert(
        "Por lo menos uno de las registro se encuentra en estado ELIMINADO"
      );
    }
  } else {
    alert("Seleccione por lo menos un registro de la lista");
  }
}

/**
 * Funcion que sirve para enviar al controlador una peticion
 * para que descargue las caracteristicas del olt
 *
 * @author Francisco Adum <fadum@telconet.ec>
 * @version 1.0 2-04-2015
 * */
function actualizarCaracteristicas(data) {
  Ext.Msg.alert(
    "Mensaje",
    "Esta seguro que desea Actualizar las Caracteristicas del OLT?",
    function (btn) {
      if (btn == "ok") {
        Ext.get(grid.getId()).mask("Actualizando Caracteristicas...");
        Ext.Ajax.request({
          url: actualizarCaractAjax,
          method: "post",
          timeout: 2000000,
          params: {
            idElemento: data.idElemento,
          },
          success: function (response) {
            Ext.Msg.alert(
              "Mensaje",
              "Resultados de la ejecucion: <br>" + response.responseText,
              function (btn) {
                if (btn == "ok") {
                  Ext.get(grid.getId()).unmask();
                }
              }
            );
          },
          failure: function (result) {
            Ext.Msg.alert("Error ", "Error: " + result.statusText);
          },
        });
      }
    }
  );
}

function actualizarPoolIp(data) {
  Ext.Msg.alert(
    "Mensaje",
    "Esta seguro que desea Actualizar los Pools de Ip?",
    function (btn) {
      if (btn == "ok") {
        Ext.get(grid.getId()).mask("Actualizando Pools...");
        Ext.Ajax.request({
          url: actualizarPoolIpAjax,
          method: "post",
          timeout: 3000000,
          params: {
            idElemento: data.idElemento,
          },
          success: function (response) {
            if (response.responseText == "OK") {
              Ext.Msg.alert(
                "Mensaje",
                "Se Actualizaron los Pools Ip!",
                function (btn) {
                  if (btn == "ok") {
                    Ext.get(grid.getId()).unmask();
                  }
                }
              );
            }
          },
          failure: function (result) {
            Ext.Msg.alert("Error ", "Error: " + result.statusText);
          },
        });
      }
    }
  );
}

function administrarPuertos(data) {
  Ext.define("estados", {
    extend: "Ext.data.Model",
    fields: [
      { name: "opcion", type: "string" },
      { name: "valor", type: "string" },
    ],
  });

  comboEstados = new Ext.data.Store({
    model: "estados",
    data: [
      { opcion: "Activo", valor: "not connect" },
      { opcion: "Online", valor: "connected" },
      { opcion: "Dañado", valor: "err-disabled" },
      { opcion: "Inactivo", valor: "disabled" },
    ],
  });

  var comboInterfaces = new Ext.data.Store({
    total: "total",
    autoLoad: true,
    proxy: {
      type: "ajax",
      url: "../../interface_elemento/getInterfacesElemento",
      extraParams: { idElemento: data.idElemento },
      reader: {
        type: "json",
        totalProperty: "total",
        root: "encontrados",
      },
    },
    fields: [
      { name: "idInterfaceElemento", mapping: "idInterfaceElemento" },
      { name: "nombreInterfaceElemento", mapping: "nombreInterfaceElemento" },
      { name: "estado", mapping: "estado" },
    ],
  });

  var cellEditing = Ext.create("Ext.grid.plugin.CellEditing", {
    clicksToEdit: 2,
    listeners: {
      edit: function () {
        // refresh summaries
        gridAdministracionPuertos.getView().refresh();
      },
    },
  });

  gridAdministracionPuertos = Ext.create("Ext.grid.Panel", {
    id: "gridAdministracionPuertos",
    store: comboInterfaces,
    columnLines: true,
    columns: [
      {
        id: "idInterfaceElemento",
        header: "idInterfaceElemento",
        dataIndex: "idInterfaceElemento",
        hidden: true,
        hideable: false,
      },
      {
        id: "nombreInterfaceElemento",
        header: "Interface Elemento",
        dataIndex: "nombreInterfaceElemento",
        width: 220,
        hidden: false,
        hideable: false,
      },
      {
        id: "estado",
        header: "Estado",
        dataIndex: "estado",
        width: 220,
        sortable: true,
        renderer: function (
          value,
          metadata,
          record,
          rowIndex,
          colIndex,
          store
        ) {
          for (var i = 0; i < comboEstados.data.items.length; i++) {
            if (comboEstados.data.items[i].data.valor == record.data.estado) {
              console.log(comboEstados.data.items[i].data.valor);
              console.log(comboInterfaces.data.items[i].data.valor);
              if (comboEstados.data.items[i].data.valor == "not connect") {
                record.data.estado = "Activo";
                break;
              } else if (comboEstados.data.items[i].data.valor == "connected") {
                record.data.estado = "Online";
                break;
              } else if (
                comboEstados.data.items[i].data.valor == "err-disabled"
              ) {
                record.data.estado = "Dañado";
                break;
              } else if (comboEstados.data.items[i].data.valor == "disabled") {
                record.data.estado = "Inactivo";
                break;
              }
            }
          }

          return record.data.estado;
        },
        editor: {
          xtype: "combobox",
          displayField: "opcion",
          valueField: "valor",
          loadingText: "Buscando ...",
          store: comboEstados,
          listClass: "x-combo-list-small",
          queryMode: "local",
        },
      },
    ],
    viewConfig: {
      stripeRows: true,
    },
    width: 500,
    height: 350,
    frame: true,
    plugins: [cellEditing],
  });

  var formPanel = Ext.create("Ext.form.Panel", {
    bodyPadding: 2,
    waitMsgTarget: true,
    fieldDefaults: {
      labelAlign: "left",
      labelWidth: 85,
      msgTarget: "side",
      bodyStyle: "padding:20px",
    },
    layout: {
      type: "table",
      // The total column count must be specified here
      columns: 2,
    },
    defaults: {
      // applied to each contained panel
      bodyStyle: "padding:20px",
    },
    items: [
      //hidden json
      {
        xtype: "hidden",
        id: "jsonInterfaces",
        name: "jsonInterfaces",
        fieldLabel: "Dslam",
        displayField: "",
        value: "",
        readOnly: true,
        width: "30%",
      }, //cierre hidden

      {
        xtype: "fieldset",
        title: "Puertos",
        defaultType: "textfield",
        defaults: {
          width: 590,
          height: 200,
        },
        items: [gridAdministracionPuertos],
      }, //cierre interfaces cpe
    ], //cierre items
    buttons: [
      {
        text: "Actualizar",
        formBind: true,
        handler: function () {
          if (true) {
            obtenerInterfaces();
            var interfaces = Ext.getCmp("jsonInterfaces").getRawValue();
            Ext.get(formPanel.getId()).mask(
              "Guardando datos y Ejecutando Scripts de Comprobacion!"
            );
            Ext.Ajax.request({
              url: "administrarPuertos",
              method: "post",
              timeout: 10000,
              params: {
                idElemento: data.idElemento,
                interfaces: interfaces,
              },
              success: function (response) {
                Ext.get(formPanel.getId()).unmask();
                if (response.responseText == "OK") {
                  Ext.Msg.alert(
                    "Mensaje",
                    "Se Actualizaron los puertos",
                    function (btn) {
                      if (btn == "ok") {
                        store.load();
                        win.destroy();
                      }
                    }
                  );
                } else {
                  Ext.Msg.alert(
                    "Mensaje ",
                    "No se pudo Actualizar los puertos del Elemento!"
                  );
                }
              },
              failure: function (result) {
                Ext.get(formPanel.getId()).unmask();
                Ext.Msg.alert("Error ", "Error: " + result.statusText);
              },
            });
          }
        },
      },
      {
        text: "Cancelar",
        handler: function () {
          win.destroy();
        },
      },
    ],
  });

  var win = Ext.create("Ext.window.Window", {
    title: "Administracion de Puertos",
    modal: true,
    width: 600,
    closable: true,
    layout: "fit",
    items: [formPanel],
  }).show();
}

function configurarIpCnrTellionManual(objRecord) {
  rowEditingConfiguraIpCNRManual = Ext.create("Ext.grid.plugin.RowEditing", {
    saveBtnText: "Guardar",
    cancelBtnText: "Cancelar",
    errorsText: "Errores",
    dirtyText: "Debe guardar o cancelar sus cambios",
    clicksToMoveEditor: 1,
    autoCancel: false,
    listeners: {
      canceledit: function (editor, e, eOpts) {
        e.store.remove(e.record);
      },
      afteredit: function (roweditor, changes, record, rowIndex) {
        var intCountGridConfiguraIpCNRManual = Ext.getCmp(
          "gridConfiguraIpCNRManual"
        )
          .getStore()
          .getCount();
        var selectionModel = Ext.getCmp(
          "gridConfiguraIpCNRManual"
        ).getSelectionModel();
        //Selecciona la fila 0 en el grid gridConfiguraIpCNRManual
        selectionModel.select(0);

        if (
          false === validaIp(changes.newValues.strIpConfigurar.trim()) ||
          false === validaMac(changes.newValues.strMacConfigurar.trim())
        ) {
          Ext.Msg.alert("Error", "La ip o mac no tienen el formato correcto");
          //Empieza la edición en fila 0 en el grid gridConfiguraIpCNRManual
          rowEditingConfiguraIpCNRManual.startEdit(0, 0);
          return false;
        }
        if (intCountGridConfiguraIpCNRManual > 0) {
          //Valida que no se agregue a otra fila si la que se está editando actualmente tiene los campos vacios
          if (
            "" ===
              Ext.getCmp("gridConfiguraIpCNRManual")
                .getStore()
                .getAt(0)
                .data.strIpConfigurar.trim() &&
            "" ===
              Ext.getCmp("gridConfiguraIpCNRManual")
                .getStore()
                .getAt(0)
                .data.strMacConfigurar.trim()
          ) {
            Ext.Msg.alert(
              "Error",
              "Debes ingresar una ip y su mac para agregar una nueva"
            );
            //Cancela la edición de la fila en el grid gridConfiguraIpCNRManual
            rowEditingConfiguraIpCNRManual.cancelEdit();
            //Selecciona la fila 0 en el grid gridConfiguraIpCNRManual
            selectionModel.select(0);
            //Empieza la edición en fila 0 en el grid gridConfiguraIpCNRManual
            rowEditingConfiguraIpCNRManual.startEdit(0, 0);
          }
        }
        //Itera el grid gridConfiguraIpCNRManual para verificar que el nuevo valor no esté ingresado.
        for (var i = 1; i < intCountGridConfiguraIpCNRManual; i++) {
          //Si los nuevos valores son iguales a alguno anteriormente ingresado muestra un mensaje y sale del loop.
          if (
            Ext.getCmp("gridConfiguraIpCNRManual")
              .getStore()
              .getAt(i)
              .get("strIpConfigurar") === changes.newValues.strIpConfigurar ||
            Ext.getCmp("gridConfiguraIpCNRManual")
              .getStore()
              .getAt(i)
              .get("strMacConfigurar") === changes.newValues.strMacConfigurar
          ) {
            Ext.Msg.alert("Error", "La ip y mac ya han sido ingresadas.");
            //Empieza la edición en fila 0 en el grid gridConfiguraIpCNRManual
            rowEditingConfiguraIpCNRManual.startEdit(0, 0);
            break;
          }
        }

        Ext.MessageBox.show({
          msg: "Verificando que la Ip este reservada...",
          title: "Verificación",
          progressText: "Verificando que la Ip este reservada.",
          progress: true,
          closable: false,
          width: 300,
          wait: true,
          waitConfig: { interval: 200 },
        });

        Ext.Ajax.request({
          url: urlVerificaIpReservada,
          method: "POST",
          timeout: 800000,
          params: {
            strIp: changes.newValues.strIpConfigurar,
            strValidarMismoElemento: "SI",
            strEstadoIp: "Reservada",
            strNombreElemento: objRecord.nombreElemento,
          },
          success: function (response) {
            var text = Ext.decode(response.responseText);

            //Permite que sigan ingresando o editen la ip que se esta ingresando cuando la no existe o no se ha reservado la ip
            if ("100" !== text.strStatus) {
              Ext.MessageBox.confirm(
                arrayTituloMensajeBox[text.strStatus],
                text.strMessageStatus + " Editar registro?",
                function (btn) {
                  if (btn === "yes") {
                    rowEditingConfiguraIpCNRManual.startEdit(0, 0);
                  }
                }
              );
            } else {
              Ext.Msg.alert("Informacion", "La ip si se encuentra reservada.");
            }
          },
        });
      },
    },
  });

  //Define un modelo para el store storeIpConfigurarCnrTellion
  Ext.define("modelIpConfigurarCnrTellion", {
    extend: "Ext.data.Model",
    fields: [
      { name: "strIpConfigurar", type: "string" },
      { name: "strMacConfigurar", type: "string" },
    ],
  });

  //Crea un store estático para el grid gridConfiguraIpCNRManual que se encuentra como item en el formulario formCreaParametrosCab
  storeIpConfigurarCnrTellion = Ext.create("Ext.data.Store", {
    pageSize: 5,
    autoDestroy: true,
    model: "modelIpConfigurarCnrTellion",
    proxy: {
      type: "memory",
    },
  });

  /**Crea el boton para agregar una fila al grid gridConfiguraIpCNRManual
   * boton usado en el toolbar toolbarCreaParamDet
   */
  btnRegistroIpConfigurar = Ext.create("Ext.button.Button", {
    text: "Agregar Ip y Mac",
    width: 160,
    iconCls: "button-grid-crearSolicitud-without-border",
    handler: function () {
      rowEditingConfiguraIpCNRManual.cancelEdit();

      //Crea una nueva fila en el grid gridConfiguraIpCNRManual con el model definido modelIpConfigurarCnrTellion
      var recordParamDet = Ext.create("modelIpConfigurarCnrTellion", {
        strIpConfigurar: "",
        strMacConfigurar: "",
      });
      //Inserta la fila en el store del grid gridConfiguraIpCNRManual
      storeIpConfigurarCnrTellion.insert(0, recordParamDet);
      //Habilita la edición de la fila del grid gridConfiguraIpCNRManual
      rowEditingConfiguraIpCNRManual.startEdit(0, 0);
      //Valida que el grid tenga filas creadas
      if (Ext.getCmp("gridConfiguraIpCNRManual").getStore().getCount() > 1) {
        //Valida que no se agregue a otra fila si la que se está editando actualmente tiene los campos vacios
        if (
          "" ===
            Ext.getCmp("gridConfiguraIpCNRManual")
              .getStore()
              .getAt(1)
              .data.strIpConfigurar.trim() &&
          "" ===
            Ext.getCmp("gridConfiguraIpCNRManual")
              .getStore()
              .getAt(1)
              .data.strMacConfigurar.trim()
        ) {
          Ext.Msg.alert(
            "Error",
            "Debes ingresar la descripcion y al menos un valor para agregar un nuevo parametro."
          );
          var selectionModel = Ext.getCmp(
            "gridConfiguraIpCNRManual"
          ).getSelectionModel();
          //Cancela la edición de la fila en el grid gridConfiguraIpCNRManual
          rowEditingConfiguraIpCNRManual.cancelEdit();
          //Remueve la fila en el grid gridConfiguraIpCNRManual
          storeIpConfigurarCnrTellion.remove(selectionModel.getSelection());
          //Selecciona la fila 0 en el grid gridConfiguraIpCNRManual
          selectionModel.select(0);
          //Empieza la edición en fila 0 en el grid gridConfiguraIpCNRManual
          rowEditingConfiguraIpCNRManual.startEdit(0, 0);
        }
      }
    },
  });

  /*Crea boton para eliminar una fila de store storeIpConfigurarCnrTellion del grid gridConfiguraIpCNRManual,
   * boton usado en el toolbar toolbarCreaParamDet*/
  btnDeleteRegistroIpConfigurar = Ext.create("Ext.button.Button", {
    text: "Eliminar Ip y Mac",
    width: 130,
    iconCls: "button-grid-quitarFacturacionElectronica-without-border",
    handler: function () {
      var gridConfiguraIpCNRManual = Ext.getCmp("gridConfiguraIpCNRManual");
      var selectionModel = gridConfiguraIpCNRManual.getSelectionModel();
      //Cancela la edición de la fila en el grid gridConfiguraIpCNRManual
      rowEditingConfiguraIpCNRManual.cancelEdit();
      //Remueve la fila en el grid gridConfiguraIpCNRManual
      storeIpConfigurarCnrTellion.remove(selectionModel.getSelection());
      //Selecciona la fila 0 del store storeIpConfigurarCnrTellion cuando este tenga registros.
      if (storeIpConfigurarCnrTellion.getCount() > 0) {
        selectionModel.select(0);
      }
    },
  });

  //Crea el toolbar para el grid gridConfiguraIpCNRManual
  toolbarCreaRegistroIpConfigurar = Ext.create("Ext.toolbar.Toolbar", {
    dock: "top",
    align: "->",
    items: [
      { xtype: "tbfill" },
      btnRegistroIpConfigurar,
      btnDeleteRegistroIpConfigurar,
    ],
  });

  windowsConfiguraIpManualCnrTellion = "";

  formConfiguraIpManualCnrTellion = Ext.create("Ext.form.Panel", {
    height: 330,
    width: 340,
    bodyPadding: 10,
    layout: {
      tdAttrs: { style: "padding: 5px;" },
      type: "table",
      columns: 1,
      pack: "center",
    },
    items: [
      {
        xtype: "displayfield",
        fieldLabel: "Olt",
        id: "strNombreElemento",
        labelStyle: "font-weight:bold;",
        value: objRecord.nombreElemento,
        textAlign: "left",
      },
      {
        xtype: "grid",
        store: storeIpConfigurarCnrTellion,
        plugins: [rowEditingConfiguraIpCNRManual],
        dockedItems: [toolbarCreaRegistroIpConfigurar],
        id: "gridConfiguraIpCNRManual",
        height: 210,
        width: 303,
        columns: [
          {
            header: "Ip",
            dataIndex: "strIpConfigurar",
            width: 150,
            editor: {
              allowBlank: false,
              blankText: "Ip Campo Obligatorio",
              regex:
                /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/,
            },
          },
          {
            header: "Mac",
            dataIndex: "strMacConfigurar",
            width: 150,
            editor: {
              allowBlank: false,
              blankText: "Mac Campo Obligatorio",
              regex: /^([0-9a-f]{2}([:-]|$)){6}$|([0-9a-f]{4}([.]|$)){3}$/,
            },
          },
        ],
      },
    ],
    buttonAlign: "center",
    buttons: [
      {
        text: "Enviar a configurar",
        name: "btnGuardar",
        id: "idBtnGuardar",
        disabled: false,
        handler: function () {
          var arrayConfiguraIpCNRManual = new Object();
          jsonConfiguraIpCNRManual = "";
          var arrayGridCreaParametrosDet = Ext.getCmp(
            "gridConfiguraIpCNRManual"
          );
          arrayConfiguraIpCNRManual["inTotal"] = arrayGridCreaParametrosDet
            .getStore()
            .getCount();
          arrayConfiguraIpCNRManual["arrayData"] = new Array();
          arrayConfiguraIpCNRManualData = Array();

          //Valida que el grid gridConfiguraIpCNRManual tenga datos
          if (arrayGridCreaParametrosDet.getStore().getCount() !== 0) {
            //Itera el grid gridConfiguraIpCNRManual y realiza un push en la variable arrayConfiguraIpCNRManualData
            for (
              var intCounterStore = 0;
              intCounterStore <
              arrayGridCreaParametrosDet.getStore().getCount();
              intCounterStore++
            ) {
              arrayConfiguraIpCNRManualData.push(
                arrayGridCreaParametrosDet.getStore().getAt(intCounterStore)
                  .data
              );
            }

            //Setea arrayConfiguraIpCNRManualData en arrayConfiguraIpCNRManual['arrayData']
            arrayConfiguraIpCNRManual["arrayData"] =
              arrayConfiguraIpCNRManualData;

            //Realiza encode a arrayConfiguraIpCNRManual para ser enviada por el request al controlador
            jsonConfiguraIpCNRManual = Ext.JSON.encode(
              arrayConfiguraIpCNRManual
            );

            Ext.MessageBox.show({
              msg: "Guardando...",
              title: "Procesando",
              progressText: "Guardando.",
              progress: true,
              closable: false,
              width: 300,
              wait: true,
              waitConfig: { interval: 200 },
            });

            Ext.Ajax.request({
              url: urlConfigurarIpManual,
              method: "POST",
              timeout: 800000,
              params: {
                intIdElemento: objRecord.idElemento,
                jsonConfiguraIpCNRManual: jsonConfiguraIpCNRManual,
              },
              success: function (response) {
                var text = Ext.decode(response.responseText);
                //Valida que el estatus de respuesta sea 100 para destruir la ventana y resetear el formulario.
                if ("100" === text.strStatus) {
                  formConfiguraIpManualCnrTellion.getForm().reset();
                  formConfiguraIpManualCnrTellion.destroy();
                  windowsConfiguraIpManualCnrTellion.destroy();
                }
                Ext.Msg.alert(
                  arrayTituloMensajeBox[text.strStatus],
                  text.strMessageStatus
                );
              },
              failure: function (result) {
                Ext.Msg.alert("Error ", result.statusText);
              },
            });
          } else {
            Ext.Msg.alert(
              arrayTituloMensajeBox["001"],
              "Debe ingresar al menos una Ip con su respectiva Mac."
            );
          }
        },
      },
      {
        text: "Cancelar",
        handler: function () {
          this.up("form").getForm().reset();
          this.up("window").destroy();
        },
      },
    ],
  });

  panelConfiguraIpManualCnrTellion = new Ext.Panel({
    width: "100%",
    height: "100%",
    items: [formConfiguraIpManualCnrTellion],
  });

  windowsConfiguraIpManualCnrTellion = Ext.widget("window", {
    title: "Configura Ip CNR - TELLION",
    height: 364,
    width: 345,
    modal: true,
    resizable: false,
    items: [panelConfiguraIpManualCnrTellion],
  }).show();
}

function actualizarAprovisionamientoOlt(
  objRecord,
  strValorCaracteristica,
  strBusqueda
) {
  windowsActualizaAprovisionamiento = "";

  formActualizaAprovisionamiento = Ext.create("Ext.form.Panel", {
    height: 147,
    width: 260,
    bodyPadding: 10,
    layout: {
      tdAttrs: { style: "padding: 5px;" },
      type: "table",
      columns: 1,
      pack: "center",
    },
    items: [
      {
        xtype: "displayfield",
        fieldLabel: "Olt",
        name: "strNombreElemento",
        labelStyle: "font-weight:bold;",
        value: objRecord.nombreElemento,
        textAlign: "left",
      },
      {
        xtype: "displayfield",
        fieldLabel: "Caracteristica",
        id: "strDetalleValorCaracteristica",
        labelStyle: "font-weight:bold;",
        value: objRecord.strAprovisionamientoIp,
        textAlign: "left",
      },
      {
        xtype: "displayfield",
        fieldLabel: "Caracteristica",
        id: "strDetalleValorCaracteristicaNuevo",
        labelStyle: "font-weight:bold;",
        value: strValorCaracteristica,
        textAlign: "left",
      },
    ],
    buttonAlign: "center",
    buttons: [
      {
        text: "Actualizar Aprovisionamiento",
        name: "btnGuardar",
        id: "idBtnGuardar",
        disabled: false,
        handler: function () {
          Ext.Msg.alert("Error ", "Actualizo");

          Ext.MessageBox.show({
            msg: "Guardando...",
            title: "Procesando",
            progressText: "Guardando.",
            progress: true,
            closable: false,
            width: 300,
            wait: true,
            waitConfig: { interval: 200 },
          });

          Ext.Ajax.request({
            url: urlActualizaCaractOLT,
            method: "POST",
            timeout: 60000,
            params: {
              intIdElemento: objRecord.idElemento,
              strDetalleValorCaracteristica: strValorCaracteristica,
              strDetalleNombreBusqueda: strBusqueda,
            },
            success: function (response) {
              var text = Ext.decode(response.responseText);
              //Valida que el estatus de respuesta sea 100 para destruir la ventana y resetear el formulario.
              if ("100" === text.strStatus) {
                formActualizaAprovisionamiento.getForm().reset();
                formActualizaAprovisionamiento.destroy();
                windowsActualizaAprovisionamiento.destroy();
                store.load();
              }
              Ext.Msg.alert(
                arrayTituloMensajeBox[text.strStatus],
                text.strMessageStatus
              );
            },
            failure: function (result) {
              Ext.Msg.alert("Error ", result.statusText);
            },
          });
        },
      },
      {
        text: "Cancelar",
        handler: function () {
          this.up("form").getForm().reset();
          this.up("window").destroy();
        },
      },
    ],
  });

  panelActualizaAprovisionamiento = new Ext.Panel({
    width: "100%",
    height: "100%",
    items: [formActualizaAprovisionamiento],
  });

  windowsActualizaAprovisionamiento = Ext.widget("window", {
    title: "Actualiza aprovisionamiento",
    height: 180,
    width: 270,
    modal: true,
    resizable: false,
    items: [panelActualizaAprovisionamiento],
  }).show();
}

function obtenerInterfaces() {
  var array_relaciones = new Object();
  array_relaciones["total"] = gridAdministracionPuertos.getStore().getCount();
  array_relaciones["interfaces"] = new Array();
  var array_data = new Array();
  for (var i = 0; i < gridAdministracionPuertos.getStore().getCount(); i++) {
    array_data.push(gridAdministracionPuertos.getStore().getAt(i).data);
  }
  array_relaciones["interfaces"] = array_data;
  Ext.getCmp("jsonInterfaces").setValue(Ext.JSON.encode(array_relaciones));
}

function eliminarDobleLineaPon(data, action) {
  var comboInterfaces = new Ext.data.Store({
    total: "total",
    autoLoad: true,
    proxy: {
      type: "ajax",
      url: "../../interface_elemento/getInterfacesElemento",
      extraParams: { idElemento: data.idElemento },
      reader: {
        type: "json",
        totalProperty: "total",
        root: "encontrados",
      },
    },
    fields: [
      { name: "idInterfaceElemento", mapping: "idInterfaceElemento" },
      { name: "nombreInterfaceElemento", mapping: "nombreInterfaceElemento" },
    ],
  });

  var formPanel = Ext.create("Ext.form.Panel", {
    bodyPadding: 2,
    waitMsgTarget: true,
    fieldDefaults: {
      labelAlign: "left",
      labelWidth: 85,
      msgTarget: "side",
    },
    layout: {
      type: "table",
      // The total column count must be specified here
      columns: 2,
    },
    items: [
      {
        xtype: "fieldset",
        title: "Ver Interface",
        defaultType: "textfield",
        defaults: {
          width: 600,
        },
        items: [
          {
            xtype: "container",
            layout: {
              type: "table",
              columns: 5,
              align: "stretch",
            },
            items: [
              { width: "10%", border: false },
              {
                xtype: "combo",
                id: "comboInterfaces",
                name: "comboInterfaces",
                store: comboInterfaces,
                fieldLabel: "Interfaces",
                displayField: "nombreInterfaceElemento",
                valueField: "nombreInterfaceElemento",
                queryMode: "local",
                width: "25%",
              },
              { width: "10%", border: false },
              {
                xtype: "textfield",
                id: "indice",
                name: "indice",
                fieldLabel: "Indice Cliente",
                displayField: "",
                valueField: "",
                width: "25%",
              },
              { width: "10%", border: false },
            ],
          },
        ],
      },
    ],
    buttons: [
      {
        text: "Ejecutar",
        formBind: true,
        handler: function () {
          if (true) {
            Ext.get(grid.getId()).mask("Loading...");
            Ext.Ajax.request({
              url: action,
              method: "post",
              waitMsg: "Esperando Respuesta del Elemento",
              timeout: 400000,
              params: {
                modelo: data.modeloElemento,
                idElemento: data.idElemento,
                interfaceElemento: Ext.getCmp("comboInterfaces").value,
                indiceCliente: Ext.getCmp("indice").value,
              },
              success: function (response) {
                var respuesta = response.responseText;

                if (respuesta == "ERROR, NO EXISTE RELACION TAREA - ACCION") {
                  Ext.Msg.alert(
                    "Error ",
                    "No Existe la Relacion Tarea - Accion"
                  );
                  Ext.get(grid.getId()).unmask();
                } else if (
                  respuesta.indexOf(
                    "El host no es alcanzable a nivel de red"
                  ) != -1
                ) {
                  Ext.Msg.alert(
                    "Error ",
                    "No se puede Conectar al Dslam <br>Favor revisar con el Departamento Tecnico"
                  );
                  Ext.get(grid.getId()).unmask();
                } else {
                  Ext.Msg.alert("MENSAJE ", "Se elimino la linea pon indicada");
                  Ext.get(grid.getId()).unmask();
                } //cierre else
              },
              failure: function (result) {
                Ext.Msg.alert("Error ", "Error: " + result.statusText);
              },
            });
            win.destroy();
          }
        },
      },
      {
        text: "Cancelar",
        handler: function () {
          win.destroy();
        },
      },
    ],
  });

  var win = Ext.create("Ext.window.Window", {
    title: "Eliminar Linea Pon",
    modal: true,
    width: 650,
    closable: true,
    layout: "fit",
    items: [formPanel],
  }).show();
}

/*
 * funcion que realiza la eliminacion de una ip fija
 * por medio de ajax request.
 */
function eliminarIpFija(data, action) {
  //store que obtiene los pools del olt
  var comboPerfilconPool = new Ext.data.Store({
    total: "total",
    autoLoad: true,
    proxy: {
      type: "ajax",
      url: mostrarPoolsPorOlt,
      extraParams: { idElemento: data.idElemento },
      reader: {
        type: "json",
        totalProperty: "total",
        root: "encontrados",
      },
    },
    fields: [
      { name: "idPool", mapping: "idPool" },
      { name: "pool", mapping: "pool" },
      { name: "idPerfil", mapping: "idPerfil" },
      { name: "perfil", mapping: "perfil" },
    ],
  });

  //creacion del panel
  var formPanel = Ext.create("Ext.form.Panel", {
    bodyPadding: 2,
    waitMsgTarget: true,
    fieldDefaults: {
      labelAlign: "left",
      labelWidth: 85,
      msgTarget: "side",
    },
    layout: {
      type: "table",
      // The total column count must be specified here
      columns: 2,
    },
    items: [
      {
        xtype: "fieldset",
        title: "Datos",
        defaultType: "textfield",
        defaults: {
          width: 600,
        },
        items: [
          {
            xtype: "container",
            layout: {
              type: "table",
              columns: 5,
              align: "stretch",
            },
            items: [
              { width: "10%", border: false },
              {
                xtype: "combo",
                id: "comboPerfilconPool",
                name: "comboPerfilconPool",
                store: comboPerfilconPool,
                fieldLabel: "Perfil",
                displayField: "perfil",
                valueField: "pool",
                queryMode: "local",
                width: "25%",
                listeners: {
                  select: function (combo) {
                    Ext.getCmp("pool").setValue = combo.getValue();
                    Ext.getCmp("pool").setRawValue(combo.getValue());
                  },
                },
              },
              { width: "10%", border: false },
              {
                xtype: "textfield",
                id: "pool",
                name: "pool",
                fieldLabel: "Pool",
                displayField: "",
                valueField: "",
                width: "25%",
                readOnly: true,
              },
              { width: "10%", border: false },
              { width: "10%", border: false },
              {
                xtype: "textfield",
                id: "ip",
                name: "ip",
                fieldLabel: "Ip",
                displayField: "",
                valueField: "",
                width: "25%",
              },
              { width: "10%", border: false },
              {
                xtype: "textfield",
                id: "mac",
                name: "mac",
                fieldLabel: "Mac",
                displayField: "",
                valueField: "",
                width: "25%",
              },
              { width: "10%", border: false },
            ],
          },
        ],
      },
    ], //end items
    //creacion de botones
    buttons: [
      {
        text: "Ejecutar",
        formBind: true,
        handler: function () {
          //validacion para mac ip fija
          var macIpFija = Ext.getCmp("mac").getValue();
          var regex = /^([0-9a-f]{2}([:-]|$)){6}$|([0-9a-f]{4}([.]|$)){3}$/i;
          macIpFija = macIpFija.replace(/\s/g, "");
          if (macIpFija == "" || !macIpFija.match(regex)) {
            Ext.Msg.alert(
              "Error",
              "Formato de Mac Incorrecta, favor ingrese con el formato (aaaa.bbbb.cccc)"
            );
            return;
          }

          //validacion para ip fija
          var ip = Ext.getCmp("ip").getValue();
          var regexIp =
            /^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/;
          ip = ip.replace(/\s/g, "");
          if (ip == "" || !ip.match(regexIp)) {
            Ext.Msg.alert(
              "Error",
              "Formato de Ip Incorrecta, favor ingrese con el formato (192.168.10.10)"
            );
            return;
          }

          if (true) {
            Ext.get(grid.getId()).mask("Loading...");
            Ext.Ajax.request({
              url: action,
              method: "post",
              waitMsg: "Esperando Respuesta del Elemento",
              timeout: 400000,
              params: {
                modelo: data.modeloElemento,
                idElemento: data.idElemento,
                pool: Ext.getCmp("pool").getValue(),
                ip: ip,
                mac: macIpFija,
              },
              success: function (response) {
                var respuesta = response.responseText;

                if (respuesta == "OK") {
                  Ext.Msg.alert("MENSAJE ", "Se elimino la ip!");
                  Ext.get(grid.getId()).unmask();
                } else {
                  Ext.Msg.alert("MENSAJE ", respuesta);
                  Ext.get(grid.getId()).unmask();
                }
                win.destroy();
              }, //end succes
              failure: function (result) {
                Ext.Msg.alert("Error ", "Error: " + result.statusText);
                if (result.statusText == "Forbidden") {
                  Ext.get(grid.getId()).unmask();
                }
              }, //end failure
            }); //end ajax request
          } //end if  
        }, //end handle
      },
      {
        text: "Cancelar",
        handler: function () {
          win.destroy();
        },
      },
    ], //end buttons
  });

  //creacion de la ventana
  var win = Ext.create("Ext.window.Window", {
    title: "Eliminar Ip Fija",
    modal: true,
    width: 650,
    closable: true,
    layout: "fit",
    items: [formPanel],
  }).show();
}

//ejecuta los scripts sin variable para los olt
function verScriptDslam(data, action) {
  console.log(data.modeloElemento);
  console.log(action);
  Ext.get(grid.getId()).mask("Loading...");
  Ext.Ajax.request({
    url: action,
    method: "post",
    waitMsg: "Esperando Respuesta del Dslam",
    timeout: 400000,
    params: { modelo: data.modeloElemento, idElemento: data.idElemento },
    success: function (response) {
      var variable = response.responseText.split("&");
      var resp = variable[0];
      var script = variable[1];

      if (script == "NO EXISTE RELACION TAREA - ACCION") {
        Ext.Msg.alert("Error ", "No Existe la Relacion Tarea - Accion");
        Ext.get(grid.getId()).unmask();
      } else {
        var ejecucion = Ext.JSON.decode(resp);
        Ext.get(grid.getId()).unmask();
        var formPanel = Ext.create("Ext.form.Panel", {
          bodyPadding: 2,
          waitMsgTarget: true,
          fieldDefaults: {
            labelAlign: "left",
            labelWidth: 85,
            msgTarget: "side",
          },
          items: [
            {
              xtype: "fieldset",
              title: "Script",
              defaultType: "textfield",
              defaults: {
                width: 550,
                height: 70,
              },
              items: [
                {
                  xtype: "container",
                  layout: {
                    type: "hbox",
                    pack: "left",
                  },
                  items: [
                    {
                      xtype: "textareafield",
                      id: "script",
                      name: "script",
                      fieldLabel: "Script",
                      value: script,
                      cols: 75,
                      rows: 3,
                      anchor: "100%",
                      readOnly: true,
                    },
                  ],
                },
              ],
            },
            ,
            {
              xtype: "fieldset",
              title: "Configuracion",
              defaultType: "textfield",
              defaults: {
                width: 550,
                height: 325,
              },
              items: [
                {
                  xtype: "container",
                  layout: {
                    type: "hbox",
                    pack: "left",
                  },
                  items: [
                    {
                      xtype: "textareafield",
                      id: "mensaje",
                      name: "mensaje",
                      fieldLabel: "Configuracion",
                      value: ejecucion.mensaje,
                      cols: 75,
                      rows: 19,
                      anchor: "100%",
                      readOnly: true,
                    },
                  ],
                },
              ],
            },
          ],
          buttons: [
            {
              text: "Cerrar",
              formBind: true,
              handler: function () {
                win.destroy();
              },
            },
          ],
        });

        var win = Ext.create("Ext.window.Window", {
          title: "Ver Configuracion",
          modal: true,
          width: 630,
          height: 550,
          closable: true,
          layout: "fit",
          items: [formPanel],
        }).show();
      } //cierre else
    },
    failure: function (result) {
      Ext.Msg.alert("Error ", "Error: " + result.statusText);
    },
  });
}

//ejecuta los script con variable (puerto) para los olt
function verScriptVariableDslam(data, action) {
  var comboInterfaces = new Ext.data.Store({
    total: "total",
    autoLoad: true,
    proxy: {
      type: "ajax",
      url: "../../interface_elemento/getInterfacesElemento",
      extraParams: { idElemento: data.idElemento },
      reader: {
        type: "json",
        totalProperty: "total",
        root: "encontrados",
      },
    },
    fields: [
      { name: "idInterfaceElemento", mapping: "idInterfaceElemento" },
      { name: "nombreInterfaceElemento", mapping: "nombreInterfaceElemento" },
    ],
  });

  var str = -1;

  str = action.search("cambiarCodificacionPuerto");
  let _formPanel = null;
  if (str != -1) {
    Ext.define("codificacion", {
      extend: "Ext.data.Model",
      fields: [
        { name: "opcion", type: "string" },
        { name: "valor", type: "string" },
      ],
    });

    var mod1 = 0;
    var mod2 = 0;
    var mod3 = 0;

    mod1 = action.search("6524");
    mod2 = action.search("7224");
    mod3 = action.search("R1");

    console.log(mod1);
    console.log(mod2);
    console.log(mod3);

    if (mod1 != -1) {
      //6524
      comboCodificacion = new Ext.data.Store({
        model: "codificacion",
        data: [
          { opcion: "G.DMT ONLY MODE", valor: "0" },
          { opcion: "G.LITE ONLY MODE", valor: "2" },
          { opcion: "T1.413 ONLY MODE", valor: "1" },
          { opcion: "AUTO SENSING MODE", valor: "3" },
        ],
      });
    } else if (mod2 != -1) {
      //7224
      comboCodificacion = new Ext.data.Store({
        model: "codificacion",
        data: [
          { opcion: "ADSL2", valor: "adsl2" },
          { opcion: "ADSL2+", valor: "adsl2+" },
          { opcion: "DMT", valor: "dmt" },
          { opcion: "MULTIMODE", valor: "multimode" },
        ],
      });
    } else if (mod3 != -1) {
      //R1
      comboCodificacion = new Ext.data.Store({
        model: "codificacion",
        data: [
          { opcion: "ADSL.BIS", valor: "adsl.bis" },
          { opcion: "ADSL.BIS.PLUS", valor: "adsl.bis.plus" },
          { opcion: "G.DMT", valor: "g.dmt" },
          { opcion: "AUTO", valor: "auto" },
        ],
      });
    }

    _formPanel = Ext.create("Ext.form.Panel", {
      bodyPadding: 2,
      waitMsgTarget: true,
      fieldDefaults: {
        labelAlign: "left",
        labelWidth: 85,
        msgTarget: "side",
      },
      items: [
        {
          xtype: "fieldset",
          title: "Ver Interface",
          defaultType: "textfield",
          defaults: {
            width: 650,
          },
          items: [
            {
              xtype: "container",
              layout: {
                type: "hbox",
                pack: "left",
              },
              items: [
                {
                  xtype: "combo",
                  id: "comboInterfaces",
                  name: "comboInterfaces",
                  store: comboInterfaces,
                  fieldLabel: "Interfaces",
                  displayField: "nombreInterfaceElemento",
                  valueField: "nombreInterfaceElemento",
                  queryMode: "local",
                },
                {
                  xtype: "combo",
                  id: "comboCodificacion",
                  name: "comboCodificacion",
                  store: comboCodificacion,
                  fieldLabel: "Codificacion",
                  displayField: "opcion",
                  valueField: "valor",
                  queryMode: "local",
                },
              ],
            },
          ],
        },
      ],
      buttons: [
        {
          text: "Ejecutar",
          formBind: true,
          handler: function () {
            if (true) {
              Ext.get(grid.getId()).mask("Loading...");
              Ext.Ajax.request({
                url: action,
                method: "post",
                waitMsg: "Esperando Respuesta del Dslam",
                timeout: 400000,
                params: {
                  modelo: data.modeloElemento,
                  idElemento: data.idElemento,
                  interfaceElemento: Ext.getCmp("comboInterfaces").value,
                  codificacion: Ext.getCmp("comboCodificacion").value,
                },
                success: function (response) {
                  var variable = response.responseText.split("&");
                  var resp = variable[0];
                  var script = variable[1];

                  if (script == "NO EXISTE RELACION TAREA - ACCION") {
                    Ext.Msg.alert(
                      "Error ",
                      "No Existe la Relacion Tarea - Accion"
                    );
                    Ext.get(grid.getId()).unmask();
                  } else if (
                    response.responseText.indexOf(
                      "El host no es alcanzable a nivel de red"
                    ) != -1
                  ) {
                    Ext.Msg.alert(
                      "Error ",
                      "No se puede Conectar al Dslam <br>Favor revisar con el Departamento Tecnico"
                    );
                    Ext.get(grid.getId()).unmask();
                  } else {
                    var ejecucion = Ext.JSON.decode(resp);
                    Ext.get(grid.getId()).unmask();

                    var formPanel1 = Ext.create("Ext.form.Panel", {
                      bodyPadding: 2,
                      waitMsgTarget: true,
                      fieldDefaults: {
                        labelAlign: "left",
                        labelWidth: 85,
                        msgTarget: "side",
                      },
                      items: [
                        {
                          xtype: "fieldset",
                          title: "Script",
                          defaultType: "textfield",
                          defaults: {
                            width: 550,
                            height: 70,
                          },
                          items: [
                            {
                              xtype: "container",
                              layout: {
                                type: "hbox",
                                pack: "left",
                              },
                              items: [
                                {
                                  xtype: "textareafield",
                                  id: "script",
                                  name: "script",
                                  fieldLabel: "Script",
                                  value: script,
                                  cols: 75,
                                  rows: 3,
                                  anchor: "100%",
                                  readOnly: true,
                                },
                              ],
                            },
                          ],
                        },
                        ,
                        {
                          xtype: "fieldset",
                          title: "Configuracion",
                          defaultType: "textfield",
                          defaults: {
                            width: 550,
                            height: 325,
                          },
                          items: [
                            {
                              xtype: "container",
                              layout: {
                                type: "hbox",
                                pack: "left",
                              },
                              items: [
                                {
                                  xtype: "textareafield",
                                  id: "mensaje",
                                  name: "mensaje",
                                  fieldLabel: "Configuracion",
                                  value: ejecucion.mensaje,
                                  cols: 75,
                                  rows: 19,
                                  anchor: "100%",
                                  readOnly: true,
                                },
                              ],
                            },
                          ],
                        },
                      ],
                      buttons: [
                        {
                          text: "Cerrar",
                          formBind: true,
                          handler: function () {
                            win.destroy();
                          },
                        },
                      ],
                    });

                    var win = Ext.create("Ext.window.Window", {
                      title: "Ver Configuracion Interface",
                      modal: true,
                      width: 630,
                      height: 550,
                      closable: false,
                      layout: "fit",
                      items: [formPanel1],
                    }).show();
                  } //cierre else
                },
                failure: function (result) {
                  Ext.Msg.alert("Error ", "Error: " + result.statusText);
                },
              });
              win.destroy();
            }
          },
        },
        {
          text: "Cancelar",
          handler: function () {
            win.destroy();
          },
        },
      ],
    });

    var win = Ext.create("Ext.window.Window", {
      title: "Ver Configuracion Interface",
      modal: true,
      width: 550,
      closable: false,
      layout: "fit",
      items: [_formPanel],
    }).show();
  } else {
    _formPanel = Ext.create("Ext.form.Panel", {
      bodyPadding: 2,
      waitMsgTarget: true,
      fieldDefaults: {
        labelAlign: "left",
        labelWidth: 85,
        msgTarget: "side",
      },
      items: [
        {
          xtype: "fieldset",
          title: "Ver Interface",
          defaultType: "textfield",
          defaults: {
            width: 650,
          },
          items: [
            {
              xtype: "container",
              layout: {
                type: "hbox",
                pack: "left",
              },
              items: [
                {
                  xtype: "combo",
                  id: "comboInterfaces",
                  name: "comboInterfaces",
                  store: comboInterfaces,
                  fieldLabel: "Interfaces",
                  displayField: "nombreInterfaceElemento",
                  valueField: "nombreInterfaceElemento",
                  queryMode: "local",
                },
              ],
            },
          ],
        },
      ],
      buttons: [
        {
          text: "Ejecutar",
          formBind: true,
          handler: function () {
            if (true) {
              Ext.get(grid.getId()).mask("Loading...");
              Ext.Ajax.request({
                url: action,
                method: "post",
                waitMsg: "Esperando Respuesta del Dslam",
                timeout: 400000,
                params: {
                  modelo: data.modeloElemento,
                  idElemento: data.idElemento,
                  interfaceElemento: Ext.getCmp("comboInterfaces").value,
                  codificacion: "",
                },
                success: function (response) {
                  var variable = response.responseText.split("&");
                  var resp = variable[0];
                  var script = variable[1];

                  if (script == "NO EXISTE RELACION TAREA - ACCION") {
                    Ext.Msg.alert(
                      "Error ",
                      "No Existe la Relacion Tarea - Accion"
                    );
                    Ext.get(grid.getId()).unmask();
                  } else if (
                    response.responseText.indexOf(
                      "El host no es alcanzable a nivel de red"
                    ) != -1
                  ) {
                    Ext.Msg.alert(
                      "Error ",
                      "No se puede Conectar al Dslam <br>Favor revisar con el Departamento Tecnico"
                    );
                    Ext.get(grid.getId()).unmask();
                  } else {
                    var ejecucion = Ext.JSON.decode(resp);
                    Ext.get(grid.getId()).unmask();

                    var formPanel1 = Ext.create("Ext.form.Panel", {
                      bodyPadding: 2,
                      waitMsgTarget: true,
                      fieldDefaults: {
                        labelAlign: "left",
                        labelWidth: 85,
                        msgTarget: "side",
                      },
                      items: [
                        {
                          xtype: "fieldset",
                          title: "Script",
                          defaultType: "textfield",
                          defaults: {
                            width: 550,
                            height: 70,
                          },
                          items: [
                            {
                              xtype: "container",
                              layout: {
                                type: "hbox",
                                pack: "left",
                              },
                              items: [
                                {
                                  xtype: "textareafield",
                                  id: "script",
                                  name: "script",
                                  fieldLabel: "Script",
                                  value: script,
                                  cols: 75,
                                  rows: 3,
                                  anchor: "100%",
                                  readOnly: true,
                                },
                              ],
                            },
                          ],
                        },
                        ,
                        {
                          xtype: "fieldset",
                          title: "Configuracion",
                          defaultType: "textfield",
                          defaults: {
                            width: 550,
                            height: 325,
                          },
                          items: [
                            {
                              xtype: "container",
                              layout: {
                                type: "hbox",
                                pack: "left",
                              },
                              items: [
                                {
                                  xtype: "textareafield",
                                  id: "mensaje",
                                  name: "mensaje",
                                  fieldLabel: "Configuracion",
                                  value: ejecucion.mensaje,
                                  cols: 75,
                                  rows: 19,
                                  anchor: "100%",
                                  readOnly: true,
                                },
                              ],
                            },
                          ],
                        },
                      ],
                      buttons: [
                        {
                          text: "Cerrar",
                          formBind: true,
                          handler: function () {
                            win.destroy();
                          },
                        },
                      ],
                    });

                    var win = Ext.create("Ext.window.Window", {
                      title: "Ver Configuracion Interface",
                      modal: true,
                      width: 630,
                      height: 550,
                      closable: false,
                      layout: "fit",
                      items: [formPanel1],
                    }).show();
                  } //cierre else
                },
                failure: function (result) {
                  Ext.Msg.alert("Error ", "Error: " + result.statusText);
                },
              });
              win.destroy();
            } else {
              Ext.Msg.alert(
                "Failed",
                "Favor Revise los campos",
                function (btn) {
                  if (btn == "ok") {
                    Ext.Msg.alert("Error ", "Error");
                  }
                }
              );
            }
          },
        },
        {
          text: "Cancelar",
          handler: function () {
            win.destroy();
          },
        },
      ],
    });
  }
}

function administrarScopes(objElemento) {
  Ext.define("modelTipoScope", {
    extend: "Ext.data.Model",
    fields: [
      { name: "strIdTipoScope", type: "string" },
      { name: "strTipoScope", type: "string" },
    ],
  });

  storeServidores = new Ext.data.Store({
    total: "total",
    proxy: {
      type: "ajax",
      method: "post",
      url: urlElementoServidor,
      extraParams: {
        tipoElemento: "CNR",
      },
      reader: {
        type: "json",
        totalProperty: "total",
        root: "encontrados",
      },
    },
    fields: [
      { name: "idElemento", mapping: "idElemento" },
      { name: "nombreElemento", mapping: "nombreElemento" },
    ],
    autoLoad: true,
  });

  //Store Tipo Scope
  storeTipoScope = Ext.create("Ext.data.Store", {
    autoLoad: true,
    model: "modelTipoScope",
    proxy: {
      type: "ajax",
      url: urlGetTipoScope,
      reader: {
        type: "json",
        root: "jsonTipoScope",
      },
    },
  });

  //Modelo Policy
  Ext.define("modelPolicy", {
    extend: "Ext.data.Model",
    fields: [
      { name: "intIdPolicy", type: "integer" },
      { name: "strPolicy", type: "string" },
    ],
  });

  //Store Policy
  storePolicy = Ext.create("Ext.data.Store", {
    autoLoad: true,
    model: "modelPolicy",
    proxy: {
      type: "ajax",
      url: urlGetPolicy,
      reader: {
        type: "json",
        root: "jsonPolicy",
      },
    },
  });

  //Modelo Scope
  Ext.define("modelScope", {
    extend: "Ext.data.Model",
    fields: [{ name: "intNumeroScope", type: "integer" }],
  });

  //Store Scope
  storeScope = Ext.create("Ext.data.Store", {
    autoLoad: true,
    model: "modelScope",
    proxy: {
      type: "ajax",
      url: urlGetNumerosScope,
      reader: {
        type: "json",
        root: "jsonNumerosScope",
      },
    },
  });

  storeTags = new Ext.data.Store({
    pageSize: 1000,
    proxy: {
      type: "ajax",
      url: urlGetTags,
      reader: {
        type: "json",
        totalProperty: "intTotalTags",
        root: "jsonTags",
      },
    },
    fields: [
      { name: "intIdTag", mapping: "intIdTag" },
      { name: "strDescripcionTag", mapping: "strDescripcionTag" },
    ],
  });

  //Model Blanck para el store de Tags
  Ext.define("modelTagsBlanck", {
    extend: "Ext.data.Model",
    fields: [
      { name: "intIdTagBlanck", mapping: "intIdTagBlanck" },
      { name: "strDescripcionTagBlanck", mappgin: "strDescripcionTagBlanck" },
    ],
  });

  //Model Mascara Permitida
  Ext.define("modelMascaraPermitida", {
    extend: "Ext.data.Model",
    fields: [{ name: "intMascaraPermitida", mapping: "intMascaraPermitida" }],
  });

  //Store Mascara Permitida
  storeMascaraPermitida = Ext.create("Ext.data.Store", {
    autoLoad: true,
    model: "modelMascaraPermitida",
    proxy: {
      type: "ajax",
      url: urlGetMascaraPermitidaAjax,
      reader: {
        type: "json",
        root: "jsonMascaraPermitida",
      },
    },
  });

  Ext.define("modelEjecutar", {
    extend: "Ext.data.Model",
    fields: [{ name: "strEjecutar", type: "string" }],
  });

  storeEjecutar = new Ext.data.Store({
    model: "modelEjecutar",
    data: [{ strEjecutar: "Si" }, { strEjecutar: "No" }],
  });

  formCreaScopesAdmin = Ext.create("Ext.form.Panel", {
    title: "Creacion Scopes",
    bodyStyle: "padding:5px 10px 0",
    buttonAlign: "center",
    width: 670,
    height: 320,
    modal: true,
    floating: true,
    closable: true,
    layout: {
      type: "table",
      columns: 4,
      tableAttrs: {
        style: {
          width: "100%",
        },
      },
    },
    fieldDefaults: {
      labelAlign: "left",
    },
    items: [
      {
        colspan: 2,
        xtype: "textfield",
        fieldLabel: "Nombre",
        id: "idIntNombre",
        readOnly: true,
        disabled: true,
        value: "S-" + objElemento.get("nombreElemento"),
      },
      {
        colspan: 2,
        xtype: "combo",
        fieldLabel: "Tipo Scope",
        id: "idCbxTipoScope",
        name: "cbxTipoScope",
        queryMode: "local",
        store: storeTipoScope,
        displayField: "strTipoScope",
        valueField: "strIdTipoScope",
        listeners: {
          select: function (f, r, i) {
            var intNumeroScope = "";
            if (null !== Ext.getCmp("idCbxScope").getValue()) {
              intNumeroScope = "-" + Ext.getCmp("idCbxScope").getValue();
            }
            if (null !== Ext.getCmp("idCbxTipoScope").getValue()) {
              if ("F" === Ext.getCmp("idCbxTipoScope").getValue()) {
                Ext.getCmp("intIdIpInicio").setValue("");
                //                                Ext.getCmp('intIdIpInicio').disable();
                Ext.getCmp("intIdIpFin").setValue("");
                //                                Ext.getCmp('intIdIpFin').disable();
                if (
                  Ext.getCmp("intIdSubred_").getValue() !== "" &&
                  Ext.getCmp("idCbxMascaraPermitida").getValue() !== null
                ) {
                  Ext.Ajax.request({
                    url: urlGetRangoIpsAjax,
                    method: "POST",
                    timeout: 60000,
                    params: {
                      strSubred_: Ext.getCmp("intIdSubred_").getValue(),
                      intMascaraPermitida: Ext.getCmp(
                        "idCbxMascaraPermitida"
                      ).getValue(),
                    },
                    success: function (response) {
                      var text = Ext.decode(response.responseText);
                      if (text.strMesanjeError === "") {
                        if (Ext.getCmp("idCbxTipoScope").getValue() === "F") {
                          Ext.getCmp("intIdIpInicio").setValue(
                            text.strIpInicio
                          );
                          Ext.getCmp("intIdIpFin").setValue(text.strIpFin);
                        }
                        Ext.getCmp("intIdMask").setValue(text.strMask);
                      } else {
                        Ext.Msg.alert(
                          "Error ",
                          "Error: " + text.strMesanjeError
                        );
                      }
                    },
                    failure: function (result) {
                      Ext.Msg.alert("Error ", "Error: " + result.statusText);
                    },
                  });
                }
              } else {
                Ext.getCmp("intIdIpInicio").setValue("");
                Ext.getCmp("intIdIpInicio").enable();
                Ext.getCmp("intIdIpFin").setValue("");
                Ext.getCmp("intIdIpFin").enable();
              }
            }

            if ("P" === Ext.getCmp("idCbxTipoScope").getValue()) {
              Ext.getCmp("idIntNombre").setValue(
                "SF" +
                  Ext.getCmp("idCbxTipoScope").getValue() +
                  "-" +
                  objElemento.get("nombreElemento") +
                  intNumeroScope
              );
            } else {
              Ext.getCmp("idIntNombre").setValue(
                "S" +
                  Ext.getCmp("idCbxTipoScope").getValue() +
                  "-" +
                  objElemento.get("nombreElemento") +
                  intNumeroScope
              );
            }
            obtenerTagsPorScope(Ext.getCmp("idCbxTipoScope").getValue());
          },
        },
      },
      {
        colspan: 2,
        xtype: "textfield",
        fieldLabel: "Subred",
        id: "intIdSubred_",
      },
      {
        xtype: "combo",
        fieldLabel: "Mask",
        id: "idCbxMascaraPermitida",
        name: "cbxMascaraPermitida",
        width: 150,
        store: storeMascaraPermitida,
        displayField: "intMascaraPermitida",
        valueField: "intMascaraPermitida",
        editable: false,
        listeners: {
          select: function (f, r, i) {
            if (Ext.getCmp("intIdSubred_").getValue() === "") {
              Ext.getCmp("idCbxMascaraPermitida").setValue("");
              Ext.Msg.alert("Error", "El campo Subred no debe estar vacio.");
            } else {
              boolSubredMP = validaIp(Ext.getCmp("intIdSubred_").getValue());
              if (false === boolSubredMP) {
                Ext.getCmp("intIdSubred_").setValue("");
                Ext.getCmp("idCbxMascaraPermitida").setValue("");
                Ext.Msg.alert(
                  "Error",
                  Ext.getCmp("intIdSubred_").getValue() +
                    ". No es una ip valida"
                );
              } else {
                if (Ext.getCmp("idCbxTipoScope").getValue() !== "") {
                  Ext.Ajax.request({
                    url: urlGetRangoIpsAjax,
                    method: "POST",
                    timeout: 60000,
                    params: {
                      strSubred_: Ext.getCmp("intIdSubred_").getValue(),
                      intMascaraPermitida: Ext.getCmp(
                        "idCbxMascaraPermitida"
                      ).getValue(),
                    },
                    success: function (response) {
                      var text = Ext.decode(response.responseText);
                      if (text.strMesanjeError === "") {
                        if (Ext.getCmp("idCbxTipoScope").getValue() === "F") {
                          Ext.getCmp("intIdIpInicio").setValue(
                            text.strIpInicio
                          );
                          Ext.getCmp("intIdIpFin").setValue(text.strIpFin);
                        }
                        Ext.getCmp("intIdMask").setValue(text.strMask);
                      } else {
                        Ext.Msg.alert(
                          "Error ",
                          "Error: " + text.strMesanjeError
                        );
                      }
                    },
                    failure: function (result) {
                      Ext.Msg.alert("Error ", "Error: " + result.statusText);
                    },
                  });
                }
              }
            }
          },
        },
      },
      {
        xtype: "textfield",
        fieldLabel: "",
        id: "intIdMask",
        width: 100,
        disabled: true,
      },
      {
        colspan: 4,
        xtype: "combo",
        fieldLabel: "Policy",
        id: "idCbxpolicy",
        name: "cbxPolicy",
        queryMode: "local",
        store: storePolicy,
        valueField: "intIdPolicy",
        displayField: "strPolicy",
      },
      {
        colspan: 2,
        xtype: "textfield",
        fieldLabel: "Ip Inicio",
        id: "intIdIpInicio",
      },
      {
        colspan: 2,
        xtype: "textfield",
        fieldLabel: "Ip Fin",
        id: "intIdIpFin",
      },
      {
        colspan: 2,
        xtype: "combo",
        fieldLabel: "Scope",
        id: "idCbxScope",
        name: "cbxScope",
        queryMode: "local",
        store: storeScope,
        displayField: "intNumeroScope",
        valueField: "intNumeroScope",
        listeners: {
          select: function (f, r, i) {
            if (Ext.getCmp("idCbxScope").getValue() > 1) {
              Ext.getCmp("intIdSubred").setValue("");
              Ext.getCmp("intIdSubred").enable();
            } else {
              Ext.getCmp("intIdSubred").setValue("");
              Ext.getCmp("intIdSubred").disable();
            }
            var strTipoScope = "S-";

            if (Ext.getCmp("idCbxTipoScope").getValue() !== null) {
              if ("P" === Ext.getCmp("idCbxTipoScope").getValue()) {
                strTipoScope =
                  "SF" + Ext.getCmp("idCbxTipoScope").getValue() + "-";
              } else {
                strTipoScope =
                  "S" + Ext.getCmp("idCbxTipoScope").getValue() + "-";
              }
            }
            Ext.getCmp("idIntNombre").setValue(
              strTipoScope +
                "" +
                objElemento.get("nombreElemento") +
                "-" +
                Ext.getCmp("idCbxScope").getValue()
            );
          },
        },
      },
      {
        colspan: 2,
        xtype: "textfield",
        fieldLabel: "Subred 0.0.0.0/0",
        id: "intIdSubred",
      },
      {
        colspan: 4,
        xtype: "combo",
        fieldLabel: "Seleccione Servidor",
        id: "idCbxServidores",
        name: "idCbxServidores",
        emptyText: "Ninguno...",
        queryMode: "local",
        store: storeServidores,
        displayField: "nombreElemento",
        valueField: "idElemento",
      },
    ],
    buttons: [
      {
        text: "Guardar Scope",
        name: "btnGuardar",
        id: "idBtnGuardar",
        disabled: false,
        handler: function () {
          var boolSubred_;
          var boolSubred = true;
          var boolMask;
          var boolIpInicio = true;
          var boolIpFin = true;
          var intCountError = 0;
          var strIp = "";

          if (
            Ext.getCmp("intIdSubred_").getValue() !== "" &&
            Ext.getCmp("idCbxMascaraPermitida").getValue() !== "" &&
            Ext.getCmp("idCbxTipoScope").getValue() !== "" &&
            Ext.getCmp("idCbxpolicy").getValue() !== "" &&
            Ext.getCmp("idCbxScope").getValue() !== ""
          ) {
            //if: 1.0
            if (
              (Ext.getCmp("idCbxScope").getValue() === 1 &&
                Ext.getCmp("intIdSubred").getValue() === "") ||
              (Ext.getCmp("idCbxScope").getValue() > 1 &&
                Ext.getCmp("intIdSubred").getValue() !== "")
            ) {
              //if: 1.1
              if (
                false !==
                  obtenerTagsPorScope(
                    Ext.getCmp("idCbxTipoScope").getValue()
                  ) ||
                null !==
                  obtenerTagsPorScope(Ext.getCmp("idCbxTipoScope").getValue())
              ) {
                //if: 1.2

                if (
                  ("D" === Ext.getCmp("idCbxTipoScope").getValue() &&
                    Ext.getCmp("intIdIpInicio").getValue() !== "" &&
                    Ext.getCmp("intIdIpFin").getValue() !== "") ||
                  ("F" === Ext.getCmp("idCbxTipoScope").getValue() &&
                    Ext.getCmp("intIdIpInicio").getValue() !== "" &&
                    Ext.getCmp("intIdIpFin").getValue() !== "") ||
                  ("S" === Ext.getCmp("idCbxTipoScope").getValue() &&
                    Ext.getCmp("intIdIpInicio").getValue() !== "" &&
                    Ext.getCmp("intIdIpFin").getValue() !== "") ||
                  ("P" === Ext.getCmp("idCbxTipoScope").getValue() &&
                    Ext.getCmp("intIdIpInicio").getValue() !== "" &&
                    Ext.getCmp("intIdIpFin").getValue() !== "") ||
                  ("SCGN" === Ext.getCmp("idCbxTipoScope").getValue() &&
                    Ext.getCmp("intIdIpInicio").getValue() !== "" &&
                    Ext.getCmp("intIdIpFin").getValue() !== "") ||
                  ("SFP" === Ext.getCmp("idCbxTipoScope").getValue() &&
                    Ext.getCmp("intIdIpInicio").getValue() !== "" &&
                    Ext.getCmp("intIdIpFin").getValue() !== "")
                ) {
                  boolSubred_ = validaIp(Ext.getCmp("intIdSubred_").getValue());
                  if (false === boolSubred_) {
                    strIp = Ext.getCmp("intIdSubred_").getValue();
                    intCountError++;
                  }

                  if (
                    "D" === Ext.getCmp("idCbxTipoScope").getValue() ||
                    "S" === Ext.getCmp("idCbxTipoScope").getValue() ||
                    "F" === Ext.getCmp("idCbxTipoScope").getValue() ||
                    "SCGN" === Ext.getCmp("idCbxTipoScope").getValue() ||
                    "SFP" === Ext.getCmp("idCbxTipoScope").getValue()
                  ) {
                    boolIpInicio = validaIp(
                      Ext.getCmp("intIdIpInicio").getValue()
                    );
                    if (false === boolIpInicio) {
                      strIp =
                        strIp + ", " + Ext.getCmp("intIdIpInicio").getValue();
                      intCountError++;
                    }

                    boolIpFin = validaIp(Ext.getCmp("intIdIpFin").getValue());
                    if (false === boolIpFin) {
                      strIp =
                        strIp + ", " + Ext.getCmp("intIdIpFin").getValue();
                      intCountError++;
                    }
                  }

                  if (
                    Ext.getCmp("idCbxScope").getValue() > 1 &&
                    Ext.getCmp("intIdSubred").getValue() !== ""
                  ) {
                    boolSubred = validaIpSubred(
                      Ext.getCmp("intIdSubred").getValue()
                    );
                    if (false === boolSubred) {
                      strIp =
                        strIp + ", " + Ext.getCmp("intIdSubred").getValue();
                      intCountError++;
                    }
                  }

                  if (
                    false === boolSubred_ ||
                    false === boolIpInicio ||
                    false === boolIpFin ||
                    false === boolSubred
                  ) {
                    if (intCountError > 1) {
                      var strSearchComa = strIp.substr(0, 1);
                      console.log(strSearchComa);
                      if (strSearchComa === ",") {
                        strIp = strIp.substr(1, strIp.Length);
                      }

                      strIp = strIp + " no son IP's validas";
                    } else {
                      var strSearchComa = strIp.substr(0, 1);
                      console.log(strSearchComa);
                      if (strSearchComa === ",") {
                        strIp = strIp.substr(1, strIp.Length);
                      }
                      strIp = strIp.substr(1) + " no es una IP valida";
                    }
                    Ext.Msg.alert("Error ", strIp);
                  } else {
                    Ext.MessageBox.show({
                      msg: "Guardando...",
                      title: "Procesando",
                      progressText: "Guardando.",
                      progress: true,
                      closable: false,
                      width: 300,
                      wait: true,
                      waitConfig: { interval: 200 },
                    });
                    Ext.Ajax.request({
                      url: urlCrearScope,
                      method: "POST",
                      timeout: 60000,
                      params: {
                        jsonTagsScope: strObjTags,
                        strNombreScope: Ext.getCmp("idIntNombre").getValue(),
                        strTipoScope: Ext.getCmp("idCbxTipoScope").getValue(),
                        strSubred_: Ext.getCmp("intIdSubred_").getValue(),
                        strSubred: Ext.getCmp("intIdSubred").getValue(),
                        intMascaraPermitida: Ext.getCmp(
                          "idCbxMascaraPermitida"
                        ).getValue(),
                        strMask: Ext.getCmp("intIdMask").getValue(),
                        intIdPolicy: Ext.getCmp("idCbxpolicy").getValue(),
                        strIpInicio: Ext.getCmp("intIdIpInicio").getValue(),
                        strIpFin: Ext.getCmp("intIdIpFin").getValue(),
                        intNumeroScope: Ext.getCmp("idCbxScope").getValue(),
                        intIdElemento: objElemento.get("idElemento"),
                        intIdElementoServidor:
                          Ext.getCmp("idCbxServidores").getValue(),
                      },
                      success: function (response) {
                        var text = Ext.decode(response.responseText);

                        Ext.Msg.alert("Informational", text.messageStatus);
                        formCreaScopesAdmin.getForm().reset();
                        formCreaScopesAdmin.destroy();
                        winScopes.destroy();
                      },
                      failure: function (result) {
                        Ext.Msg.alert("Error ", "Error: " + result.statusText);
                      },
                    });
                  }
                } else {
                  Ext.Msg.alert(
                    "Error ",
                    "Debe ingresar una Ip de inicio y fin"
                  );
                } //if: 1.3 Validacion Tipo Scope
              } else {
                //Validacion Tags
                Ext.Msg.alert("Error ", "Debe ingresar al menos un tag");
              } //if: 1.2 Validacion Tags
            } else {
              //Validacion Numero Scope vs Subred
              Ext.Msg.alert("Error ", "Debe ingresar una subred");
            } //if: 1.1 Validacion Numero Scope vs Subred
          } else {
            //Validacion campos vacios
            Ext.Msg.alert("Error ", "Debe ingresar todos los campos");
          } //if: 1.0 Validacion campos vacios
        }, //handler
      },
      {
        text: "Cancelar",
        handler: function () {
          formCreaScopesAdmin.getForm().reset();
          formCreaScopesAdmin.destroy();
        },
      },
    ],
  });
  formCreaScopesAdmin.show();
}

function showAsignarScopes(objElemento) {
  var txtNombreElemento = Ext.create("Ext.form.Text", {
    id: "txtNombreElemento",
    name: "txtNombreElemento",
    fieldLabel: "Elemento OLT",
    labelAlign: "left",
    disabled: true,
  });

  var txtIdElemento = "";

  var txtNombreScope = Ext.create("Ext.form.Text", {
    id: "txtNombreScope",
    name: "txtNombreScope",
    fieldLabel: "Nombre Scope",
    labelAlign: "right",
    padding: 5,
  });

  var formAsignarScopes = Ext.create("Ext.form.Panel", {
    height: 80,
    width: "100%",
    bodyPadding: 10,
    layout: {
      tdAttrs: { style: "padding: 2px;" },
      type: "table",
      columns: 3,
      pack: "center",
    },
    items: [
      txtNombreElemento,
      {
        iconCls: "button-grid-show",
        xtype: "button",
        width: 35,
        handler: function () {
          storeFindOLT = new Ext.data.Store({
            pageSize: 5,
            total: "total",
            proxy: {
              type: "ajax",
              url: "getEncontradosOlt",
              reader: {
                type: "json",
                totalProperty: "total",
                root: "encontrados",
              },
            },
            fields: [
              { name: "idElemento", mapping: "idElemento" },
              { name: "nombreElemento", mapping: "nombreElemento" },
              { name: "ipElemento", mapping: "ipElemento" },
              { name: "marcaElemento", mapping: "marcaElemento" },
              { name: "modeloElemento", mapping: "modeloElemento" },
            ],
          });

          gridOLT = Ext.create("Ext.grid.Panel", {
            title: "Seleccione el OLT",
            store: storeFindOLT,
            columns: [
              {
                header: "Olt",
                dataIndex: "idElemento",
                xtype: "templatecolumn",
                width: 240,
                tpl: '<span class="box-detalle">{nombreElemento}</span>\n\
                                               <span class="bold">Ip:</span><span>{ipElemento}</span></br>',
              },
              { header: "Marca", dataIndex: "marcaElemento", width: 136 },
              { header: "Modelo", dataIndex: "modeloElemento", width: 124 },
              {
                xtype: "actioncolumn",
                header: "Seleccionar",
                width: 120,
                items: [
                  {
                    getClass: function (v, meta, rec) {
                      return "button-grid-aprobar";
                    },
                    tooltip: "Seleccionar",
                    handler: function (grid, rowIndex, colIndex) {
                      var recStore = storeFindOLT.getAt(rowIndex);
                      Ext.getCmp("txtNombreElemento").setValue(
                        recStore.get("nombreElemento")
                      );
                      txtIdElemento = recStore.get('idElemento');
                      windowsBuscarOLT.close();
                    },
                  },
                ],
              },
            ],
            height: 300,
            width: "100%",
            bbar: Ext.create("Ext.PagingToolbar", {
              store: storeFindOLT,
              displayInfo: true,
              displayMsg: "Mostrando {0} - {1} de {2}",
              emptyMsg: "No hay datos que mostrar.",
            }),
          });

          var txtNombreOlt = Ext.create("Ext.form.Text", {
            id: "txtNombreOlt",
            name: "txtNombreOlt",
            fieldLabel: "Nombre OLT",
            width: 250,
          });

          var formFindOLT = Ext.create("Ext.form.Panel", {
            height: 100,
            width: "100%",
            bodyPadding: 15,
            layout: {
              tdAttrs: { style: "padding: 10px;" },
              type: "table",
              columns: 2,
              pack: "center",
            },
            buttonAlign: "center",
            items: [
              txtNombreOlt,
              {
                xtype: "combobox",
                fieldLabel: "Estado",
                id: "strEstado",
                value: "Activo",
                store: [
                  ["Activo", "Activo"],
                  ["Modificado", "Modificado"],
                  ["Eliminado", "Eliminado"],
                ],
              },
            ],
            buttons: [
              {
                text: "Buscar",
                iconCls: "icon_search",
                handler: function () {
                  if ("" !== Ext.getCmp("txtNombreOlt").getValue()) {
                    storeFindOLT.getProxy().extraParams.nombreElemento =
                      Ext.getCmp("txtNombreOlt").getValue();
                    storeFindOLT.getProxy().extraParams.estado =
                      Ext.getCmp("strEstado").getValue();
                    storeFindOLT.load();
                  } else {
                    Ext.Msg.alert(
                      "Alerta!",
                      "Debe ingresar un nombre de OLT para buscar."
                    );
                  }
                },
              },
              {
                text: "Limpiar",
                iconCls: "icon_limpiar",
                handler: function () {
                  Ext.getCmp("txtNombreOlt").setValue("");
                  Ext.getCmp("strEstado").value = null;
                  Ext.getCmp("strEstado").setRawValue(null);
                },
              },
            ],
          });

          var panelOLT = new Ext.Panel({
            width: "100%",
            items: [
              formFindOLT,
              { html: "", border: false, width: "100%", height: 25 },
              gridOLT,
            ],
          });

          windowsBuscarOLT = Ext.widget("window", {
            title: "Buscar OLT's",
            width: 638,
            height: 460,
            layout: "fit",
            modal: true,
            items: [panelOLT],
          }).show();
        },
      },
      txtNombreScope,
    ],
    buttonAlign: "center",
    buttons: [
      {
        text: "Buscar",
        iconCls: "icon_search",
        handler: function () {
          if ("" !== Ext.getCmp("txtNombreElemento").getValue()) {
            storeScopesOLT.getProxy().extraParams.intIdElemento =
              txtIdElemento;
            storeScopesOLT.getProxy().extraParams.strNombreScope =
              Ext.getCmp("txtNombreScope").getValue();
            storeScopesOLT.getProxy().extraParams.strEstado = "Activo";
            storeScopesOLT.load();
          } else {
            Ext.Msg.alert("Alerta!", "Debe buscar un nombre de OLT.");
          }
        },
      },
      {
        text: "Limpiar",
        iconCls: "icon_limpiar",
        handler: function () {
          Ext.getCmp("txtNombreElemento").setValue("");
          Ext.getCmp("txtNombreScope").setValue("");
          txtIdElemento = "";
        },
      },
    ],
  });

  Ext.define("ListaScopesByOLT", {
    extend: "Ext.data.Model",
    fields: [
      { name: "intIdDetalleElemento", type: "int" },
      { name: "strNombreScope", type: "string" },
      { name: "strSubred", type: "string" },
      { name: "strMascara", type: "string" },
      { name: "strIpInicial", type: "string" },
      { name: "strIpFinal", type: "string" },
      { name: "strEstado", type: "string" },
      { name: "intIdSubred", type: "int" },
    ],
  });

  storeScopesOLT = Ext.create("Ext.data.Store", {
    pageSize: 500,
    model: "ListaScopesByOLT",
    proxy: {
      type: "ajax",
      url: urlGetStoreScopesOLT,
      timeout: 90000,
      reader: {
        type: "json",
        root: "jsonScopesOLT",
        totalProperty: "intTotal",
      },
      simpleSortMode: true,
    },
    listeners: {
      load: function (storeScopesOLT) {},
    },
  });

  gridAsignarScopes = Ext.create("Ext.grid.Panel", {
    title: "Asignar Scope a OLT",
    store: storeScopesOLT,
    id: "gridScopesOLT",
    columns: [
      { header: "Scope ", dataIndex: "strNombreScope", width: 150 },
      { header: "Subred", dataIndex: "strSubred", width: 120 },
      { header: "Mascara", dataIndex: "strMascara", width: 120 },
      { header: "Ip Inicial", dataIndex: "strIpInicial", width: 120 },
      { header: "Ip Final", dataIndex: "strIpFinal", width: 120 },
      { header: "Estado", dataIndex: "strEstado", width: 50 },
      {
        xtype: "actioncolumn",
        header: "Seleccionar",
        width: 80,
        items: [
          {
            getClass: function (v, meta, rec) {
              return "button-grid-cambioCpe";
            },
            tooltip: "Asignar Scope a OLT",
            handler: function (grid, rowIndex, colIndex) {
              var recStore = storeScopesOLT.getAt(rowIndex);
              Ext.MessageBox.show({
                title: "Alerta",
                msg:
                  "Esta seguro de asignar el: <br> Scope: [" +
                  recStore.get("strNombreScope") +
                  "] del OLT: [" +
                  Ext.getCmp("txtNombreElemento").getValue() +
                  "] <br> Al OLT: [" +
                  objElemento.get("nombreElemento") +
                  "] ?",
                buttons: Ext.MessageBox.OKCANCEL,
                icon: Ext.MessageBox.INFO,
                fn: function (btn) {
                  if (btn == "ok") {
                    Ext.MessageBox.show({
                      msg: "Asignando...",
                      title: "Asiganando Scope a OLT",
                      progressText: "Asignando.",
                      progress: true,
                      closable: false,
                      width: 300,
                      wait: true,
                      waitConfig: { interval: 200 },
                    });
                    Ext.Ajax.request({
                      url: urlAsignaScope,
                      method: "POST",
                      timeout: 60000,
                      params: {
                        intIdSubred: recStore.get("intIdSubred"),
                        intIdElementoOLT: objElemento.get("idElemento"),
                      },
                      success: function (response) {
                        var text = Ext.decode(response.responseText);
                        Ext.Msg.alert("Informational", text.strMessageStatus);
                        windowsAsignarScope.close();
                      },
                      failure: function (result) {
                        Ext.Msg.alert("Error ", "Error: " + result.statusText);
                      },
                    });
                  } else {
                    return;
                  }
                },
              });
            },
          },
        ],
      },
    ],
    height: 380,
    width: "100%",
    bbar: Ext.create("Ext.PagingToolbar", {
      store: storeScopesOLT,
      displayInfo: true,
      displayMsg: "Mostrando {0} - {1} de {2}",
      emptyMsg: "No hay datos que mostrar.",
    }),
  });

  var panelAsignarScopes = new Ext.Panel({
    width: "100%",
    height: 300,
    items: [formAsignarScopes, gridAsignarScopes],
  });

  windowsAsignarScope = Ext.widget("window", {
    title: "Asignación de Scopes",
    height: 500,
    width: 778,
    layout: "fit",
    modal: true,
    items: [panelAsignarScopes],
  }).show();
}

function showScopes(objElemento) {
  winScopes = "";
  if (!winScopes) {
    Ext.define("modelGridScope", {
      extend: "Ext.data.Model",
      fields: [
        { name: "intIdSubred", type: "integer" },
        { name: "strSubred", type: "string" },
        { name: "strNombreScope", type: "string" },
        { name: "strMascara", type: "string" },
        { name: "strIpInicial", type: "string" },
        { name: "strIpFinal", type: "string" },
        { name: "strPolicy", type: "string" },
        { name: "strEstado", type: "string" },
      ],
    });

    storeGridScope = Ext.create("Ext.data.JsonStore", {
      model: "modelGridScope",
      pageSize: 10,
      autoLoad: true,
      proxy: {
        type: "ajax",
        timeout: 900000,
        url: urlGetStoreScopesOLT,
        reader: {
          type: "json",
          root: "jsonScopesOLT",
          totalProperty: "intTotal",
        },
        simpleSortMode: false,
      },
      listeners: {
        beforeload: function (storeGridScope) {
          storeGridScope.getProxy().extraParams.intIdElemento =
            objElemento.get("idElemento");
        },
      },
    });

    storeServidores = new Ext.data.Store({
      total: "total",
      proxy: {
        type: "ajax",
        method: "post",
        url: urlElementoServidor,
        extraParams: {
          tipoElemento: "CNR",
        },
        reader: {
          type: "json",
          totalProperty: "total",
          root: "encontrados",
        },
      },
      fields: [
        { name: "idElemento", mapping: "idElemento" },
        { name: "nombreElemento", mapping: "nombreElemento" },
      ],
      autoLoad: true,
    });

    toolBarCreaScope = Ext.create("Ext.toolbar.Toolbar", {
      dock: "top",
      align: "->",
      items: [
        { xtype: "tbfill" },
        {
          text: "Crear Nuevo Scope",
          scope: this,
          handler: function () {
            administrarScopes(objElemento);
          },
        },
      ],
    });

    gridScope = Ext.create("Ext.grid.Panel", {
      title: "Listado de Scopes",
      id: "gridScope",
      store: storeGridScope,
      loadMask: true,
      dockedItems: [toolBarCreaScope],
      columns: [
        {
          id: "intIdSubred",
          header: "Subred",
          dataIndex: "intIdSubred",
          hidden: true,
          hideable: false,
        },
        { header: "Nombre Scope", dataIndex: "strNombreScope", width: 120 },
        { header: "Subred ", dataIndex: "strSubred", width: 120 },
        { header: "Mascara", dataIndex: "strMascara", width: 120 },
        { header: "Ip Inicial", dataIndex: "strIpInicial", width: 120 },
        { header: "Ip Final", dataIndex: "strIpFinal", width: 120 },
        { header: "Policy", dataIndex: "strPolicy", width: 120 },
        { header: "Estado", dataIndex: "strEstado", width: 100 },
        {
          xtype: "actioncolumn",
          header: "Accion",
          width: 100,
          items: [
            {
              getClass: function (v, meta, rec) {
                var permiso = $("#ROLE_227-2237");
                var boolPermiso =
                  typeof permiso === "undefined"
                    ? false
                    : permiso.val() == 1
                    ? true
                    : false;
                var strBtnScope = "";
                if (!boolPermiso) {
                  strBtnScope = "button-grid-invisible";
                } else {
                  if ("Eliminado" !== rec.data.strEstado) {
                    strBtnScope = "button-grid-delete";
                  }
                }
                return strBtnScope;
              },
              tooltip: "Eliminar",
              handler: function (grid, rowIndex, colIndex) {
                var row = storeGridScope.getAt(rowIndex);
                var strEjecutar;
                formEliminaScope = Ext.create("Ext.form.Panel", {
                  title: "Elimar Scope",
                  bodyStyle: "padding:5px 10px 0",
                  buttonAlign: "center",
                  modal: true,
                  width: 350,
                  height: 150,
                  floating: true,
                  closable: true,
                  layout: {
                    type: "table",
                    columns: 1,
                    tableAttrs: {
                      style: {
                        width: "100%",
                      },
                    },
                  },
                  fieldDefaults: {
                    labelAlign: "left",
                  },
                  items: [
                    {
                      xtype: "combo",
                      fieldLabel: "Seleccione Servidor",
                      id: "idCbxServidor",
                      name: "cbxServidor",
                      emptyText: "Ninguno...",
                      queryMode: "local",
                      store: storeServidores,
                      displayField: "nombreElemento",
                      valueField: "idElemento",
                      width: 300,
                    },
                  ],
                  buttons: [
                    {
                      text: "Eliminar Scope",
                      name: "btnGuardar",
                      id: "idBtnGuardar",
                      disabled: false,
                      handler: function () {
                        Ext.Ajax.request({
                          url: urlEliminarScope,
                          method: "POST",
                          timeout: 60000,
                          params: {
                            intIdSubred: row.get("intIdSubred"),
                            intServidores:
                              Ext.getCmp("idCbxServidor").getValue(),
                          },
                          success: function (response) {
                            var text = Ext.decode(response.responseText);
                            Ext.Msg.alert("Informational", text.messageStatus);
                            formEliminaScope.getForm().reset();
                            formEliminaScope.destroy();
                            winScopes.destroy();
                          },
                          failure: function (result) {
                            Ext.Msg.alert(
                              "Error ",
                              "Error: " + result.statusText
                            );
                          },
                        });
                      }, //handler
                    },
                    {
                      text: "Cancelar",
                      handler: function () {
                        formEliminaScope.getForm().reset();
                        formEliminaScope.destroy();
                      },
                    },
                  ],
                });
                formEliminaScope.show();
              },
            },
            {
              //Editar Ip's Scopes
              getClass: function (v, meta, rec) {
                var permiso = $("#ROLE_227-2237");
                var boolPermiso =
                  typeof permiso === "undefined"
                    ? false
                    : permiso.val() == 1
                    ? true
                    : false;
                var strBtnScope = "";
                if (!boolPermiso) {
                  strBtnScope = "button-grid-invisible";
                } else {
                  if ("Eliminado" !== rec.data.strEstado) {
                    strBtnScope = "button-grid-edit";
                  }
                }
                return strBtnScope;
              },
              tooltip: "Editar",
              handler: function (grid, rowIndex, colIndex) {
                var row = storeGridScope.getAt(rowIndex);

                var cbxProducto = new Ext.form.ComboBox({
                  xtype: "combo",
                  fieldLabel: "Seleccione Servidor",
                  id: "idCbxServidorIp",
                  name: "cbxServidorIp",
                  emptyText: "Ninguno...",
                  queryMode: "local",
                  store: storeServidores,
                  displayField: "nombreElemento",
                  valueField: "idElemento",
                  width: 300,
                });

                Ext.Ajax.request({
                  url: urlGetTipoScopeAjax,
                  method: "POST",
                  timeout: 60000,
                  params: {
                    intIdSubred: row.get("intIdSubred"),
                  },
                  success: function (response) {
                    var text = Ext.decode(response.responseText);
                    console.log(text.strTipoScope);
                    if ("Fija" === text.strTipoScope) {
                      Ext.getCmp("idCbxServidorIp").setDisabled(true);
                    } else {
                      Ext.getCmp("idCbxServidorIp").setDisabled(false);
                    }
                  },
                });

                formEditarIpScope = Ext.create("Ext.form.Panel", {
                  id: "formEditarScope",
                  title: "Editar Ip's del Scope " + row.get("strNombreScope"),
                  bodyStyle: "padding:5px 10px 0",
                  buttonAlign: "center",
                  modal: true,
                  width: 350,
                  height: 150,
                  floating: true,
                  closable: true,
                  layout: {
                    type: "table",
                    columns: 1,
                    tableAttrs: {
                      style: {
                        width: "100%",
                      },
                    },
                  },
                  fieldDefaults: {
                    labelAlign: "left",
                  },
                  items: [
                    {
                      xtype: "textfield",
                      fieldLabel: "Ip Inicial",
                      id: "intIpInicialEditIpScope",
                      width: 300,
                      value: row.get("strIpInicial"),
                    },
                    {
                      xtype: "textfield",
                      fieldLabel: "Ip  Final",
                      id: "intIpFinEditIpScope",
                      width: 300,
                      value: row.get("strIpFinal"),
                    },
                    cbxProducto,
                  ],
                  buttons: [
                    {
                      text: "Actualizar Rango Ip",
                      name: "btnGuardar",
                      id: "idBtnGuardar",
                      disabled: false,
                      handler: function () {
                        if (
                          true ===
                            validaIp(
                              Ext.getCmp("intIpInicialEditIpScope").getValue()
                            ) &&
                          true ===
                            validaIp(
                              Ext.getCmp("intIpFinEditIpScope").getValue()
                            )
                        ) {
                          Ext.MessageBox.show({
                            msg: "Actualizando rangos...",
                            title: "Procesando",
                            progressText: "Guardando.",
                            progress: true,
                            closable: false,
                            width: 300,
                            wait: true,
                            waitConfig: { interval: 200 },
                          });
                          Ext.Ajax.request({
                            url: urlActualizaIpScopes,
                            method: "POST",
                            timeout: 60000,
                            params: {
                              intIdSubred: row.get("intIdSubred"),
                              strNombreScope: row.get("strNombreScope"),
                              intServidores:
                                Ext.getCmp("idCbxServidorIp").getValue(),
                              strIpInicial: Ext.getCmp(
                                "intIpInicialEditIpScope"
                              ).getValue(),
                              strIpFinal: Ext.getCmp(
                                "intIpFinEditIpScope"
                              ).getValue(),
                            },
                            success: function (response) {
                              var text = Ext.decode(response.responseText);
                              Ext.Msg.alert("Informational", text.strMensaje);
                              formEditarIpScope.getForm().reset();
                              formEditarIpScope.destroy();
                              storeGridScope.load();
                            },
                            failure: function (result) {
                              Ext.Msg.alert(
                                "Error ",
                                "Error: " + result.statusText
                              );
                            },
                          });
                        } else {
                          Ext.Msg.alert(
                            "Error ",
                            "La Ip Inicial o la Ip Final no son correctas."
                          );
                        }
                      }, //handler
                    },
                    {
                      text: "Cancelar",
                      handler: function () {
                        formEditarIpScope.getForm().reset();
                        formEditarIpScope.destroy();
                      },
                    },
                  ],
                });
                formEditarIpScope.show();
              },
            },
          ],
        },
      ],
      height: 405,
      width: 922,
      bbar: Ext.create("Ext.PagingToolbar", {
        store: storeGridScope,
        displayInfo: true,
        displayMsg: "Mostrando {0} - {1} de {2}",
        emptyMsg: "No hay datos que mostrar.",
      }),
    });

    winScopes = new Ext.Window({
      title: "Crear Scopes",
      id: "winScopes",
      width: 954,
      height: 458,
      modal: true,
      bodyStyle: "background-color:#fff;padding: 10px",
      items: gridScope,
      resizable: false,
      draggable: false,
    });
  }
  winScopes.show();
}

function showDetalleElemento(objElemento) {
  storeTiposDetalles = new Ext.data.Store({
    pageSize: 100,
    listeners: {
      load: function () {},
    },
    proxy: {
      timeout: 400000,
      type: "ajax",
      url: urlTiposDetallesElemento,
      extraParams: {
        idElemento: objElemento.get("idElemento"),
        tipoDetalle: this.detalle_elemento,
      },
      reader: {
        type: "json",
        totalProperty: "total",
        root: "encontrados",
      },
    },
    fields: [{ name: "DETALLE_ELEMENTO", mapping: "DETALLE_ELEMENTO" }],
    autoLoad: true,
  });

  DTFechaDesdeCreacion = new Ext.form.DateField({
    id: "fechaDesdeCreacion",
    name: "fechaDesdeCreacion",
    fieldLabel: "Creado Desde",
    labelAlign: "left",
    xtype: "datefield",
    format: "Y-m-d",
    width: "100%",
    editable: false,
    //anchor : '65%',
    //layout: 'anchor'
  });
  DTFechaHastaCreacion = new Ext.form.DateField({
    id: "fechaHastaCreacion",
    name: "fechaHastaCreacion",
    fieldLabel: "Creado Hasta",
    labelAlign: "left",
    xtype: "datefield",
    format: "Y-m-d",
    width: "100%",
    editable: false,
    //anchor : '65%',
    //layout: 'anchor'
  });

  var formDetallesElemento = Ext.create("Ext.form.Panel", {
    height: 150,
    width: "100%",
    bodyPadding: 10,
    layout: {
      tdAttrs: { style: "padding: 2px;" },
      type: "table",
      columns: 2,
      pack: "center",
    },
    items: [
      {
        xtype: "combobox",
        id: "nombreDetalle",
        fieldLabel: "Nombre",
        store: storeTiposDetalles,
        displayField: "DETALLE_ELEMENTO",
        valueField: "DETALLE_ELEMENTO",
        loadingText: "Buscando ...",
        listClass: "x-combo-list-small",
        queryMode: "local",
        width: "100%",
      },
      DTFechaDesdeCreacion,
      {
        xtype: "textfield",
        id: "valorDetalle",
        fieldLabel: "Valor",
        value: "",
        width: "100%",
      },
      DTFechaHastaCreacion,
      {
        xtype: "textfield",
        id: "descripcion",
        fieldLabel: "Descripcion",
        value: "",
        width: "100%",
      },
    ],
    buttonAlign: "center",
    buttons: [
      {
        text: "Buscar",
        iconCls: "icon_search",
        handler: function () {
          var boolError = false;
          if (
            Ext.getCmp("fechaDesdeCreacion").getValue() != null &&
            Ext.getCmp("fechaHastaCreacion").getValue() != null
          ) {
            if (
              Ext.getCmp("fechaDesdeCreacion").getValue() >
              Ext.getCmp("fechaHastaCreacion").getValue()
            ) {
              boolError = true;

              Ext.Msg.show({
                title: "Error en Busqueda",
                msg: "Por Favor para realizar la busqueda Fecha Desde debe ser fecha menor a Fecha Hasta.",
                buttons: Ext.Msg.OK,
                animEl: "elId",
                icon: Ext.MessageBox.ERROR,
              });
            }
          }

          if (!boolError) {
            if (
              "" != Ext.getCmp("nombreDetalle").getRawValue() ||
              "" != Ext.getCmp("valorDetalle").getRawValue() ||
              "" != Ext.getCmp("descripcion").getRawValue() ||
              null != Ext.getCmp("fechaDesdeCreacion").getValue() ||
              null != Ext.getCmp("fechaHastaCreacion").getValue()
            ) {
              storeDetallesElemento.getProxy().extraParams.fechaDesde =
                Ext.getCmp("fechaDesdeCreacion").value;
              storeDetallesElemento.getProxy().extraParams.fechaHasta =
                Ext.getCmp("fechaHastaCreacion").value;
              storeDetallesElemento.getProxy().extraParams.nombreDetalle =
                Ext.getCmp("nombreDetalle").value;
              storeDetallesElemento.getProxy().extraParams.valorDetalle =
                Ext.getCmp("valorDetalle").value;
              storeDetallesElemento.getProxy().extraParams.descripcion =
                Ext.getCmp("descripcion").value;
              storeDetallesElemento.getProxy().extraParams.idElemento =
                objElemento.get("idElemento");
              storeDetallesElemento.load();
            } else {
              Ext.Msg.alert(
                "Alerta!",
                "Debe ingresar algun parametro de busqueda."
              );
            }
          }
        },
      },
      {
        text: "Limpiar",
        iconCls: "icon_limpiar",
        handler: function () {
          Ext.getCmp("nombreDetalle").setValue("");
          Ext.getCmp("valorDetalle").setValue("");
          Ext.getCmp("descripcion").setValue("");
          Ext.getCmp("fechaDesdeCreacion").setValue("");
          Ext.getCmp("fechaHastaCreacion").setValue("");
        },
      },
    ],
  });

  storeDetallesElemento = new Ext.data.Store({
    pageSize: 100,
    listeners: {
      load: function () {},
    },
    proxy: {
      type: "ajax",
      url: urlDetallesElemento,
      extraParams: {
        idElemento: objElemento.get("idElemento"),
      },
      reader: {
        type: "json",
        totalProperty: "total",
        root: "encontrados",
      },
    },
    fields: [
      { name: "DETALLE_ELEMENTO", mapping: "DETALLE_ELEMENTO" },
      { name: "DETALLE_VALOR", mapping: "DETALLE_VALOR" },
      { name: "DETALLE_DESCRIPCION", mapping: "DETALLE_DESCRIPCION" },
      { name: "DETALLE_FECREACION", mapping: "DETALLE_FECREACION" },
      { name: "DETALLE_USRCREACION", mapping: "DETALLE_USRCREACION" },
    ],
  });

  gridDetallesElemento = Ext.create("Ext.grid.Panel", {
    title: "Registros",
    store: storeDetallesElemento,
    id: "gridDetallesElemento",
    columns: [
      { header: "Nombre ", dataIndex: "DETALLE_ELEMENTO", width: 178 },
      { header: "Valor", dataIndex: "DETALLE_VALOR", width: 150 },
      { header: "Descripcion", dataIndex: "DETALLE_DESCRIPCION", width: 150 },
      { header: "Fecha Creacion", dataIndex: "DETALLE_FECREACION", width: 150 },
      {
        header: "Usuario Creacion",
        dataIndex: "DETALLE_USRCREACION",
        width: 150,
      },
    ],
    height: 380,
    width: "100%",
    bbar: Ext.create("Ext.PagingToolbar", {
      store: storeDetallesElemento,
      displayInfo: true,
      displayMsg: "Mostrando {0} - {1} de {2}",
      emptyMsg: "No hay datos que mostrar.",
    }),
  });

  var panelDetallesElemento = new Ext.Panel({
    width: "100%",
    height: 300,
    items: [formDetallesElemento, gridDetallesElemento],
  });

  windowsDetallesElemento = Ext.widget("window", {
    title: "Detalle de Elemento",
    height: 500,
    width: 778,
    layout: "fit",
    modal: true,
    items: [panelDetallesElemento],
  }).show();
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
    console.log(array);
    return Ext.JSON.encode(array);
  } else {
    Ext.Msg.alert("Advertencia", "No ha ingresado tags");
    return false;
  }
}

function obtenerTagsPorScope(tipoScope) {
  if (
    tipoScope !== "" ||
    null !== tipoScope ||
    undefined !== tipoScope ||
    "null" !== tipoScope
  ) {
    $.ajax({
      url: urlGetTagsByTipoScope,
      method: "GET",
      dataType: "json",
      data: {
        tipoScope: tipoScope,
      },
      success: function (response) {
        var obj = response;

        if (obj["total"] === 0) {
          Ext.Msg.alert(
            "Advertencia",
            "No hay tags relacionados a ese Tipo Scope."
          );
          return false;
        } else {
          strObjTags = Ext.JSON.encode(obj);
          return strObjTags;
        }
      },
      failure: function () {
        Ext.Msg.alert("Error");
        return false;
      },
    });
  } else {
    console.log("si pasa");
    Ext.Msg.alert("Advertencia", "Se debe seleccionar un Tipo Scope.");
    return false;
  }
}

function validaIp(strIp) {
  var boolCorrecto = false;
  if (
    /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(
      strIp
    )
  ) {
    boolCorrecto = true;
  }
  return boolCorrecto;
}

function validaIpSubred(strSubred) {
  var boolCorrecto = false;
  if (
    /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\/(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(
      strSubred
    )
  ) {
    boolCorrecto = true;
  }
  return boolCorrecto;
}

function validaMac(strMac) {
  var boolCorrecto = true;
  var regex = /^([0-9a-f]{2}([:-]|$)){6}$|([0-9a-f]{4}([.]|$)){3}$/i;
  strMac = strMac.replace(/\s/g, "");
  if ("" === strMac || !strMac.match(regex)) {
    boolCorrecto = false;
  }
  return boolCorrecto;
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

function eliminarSeleccion(datosSelect) {
  for (var i = 0; i < datosSelect.getSelectionModel().getCount(); i++) {
    datosSelect
      .getStore()
      .remove(datosSelect.getSelectionModel().getSelection()[i]);
  }
}
/* dms 13/12/2013 */
function showPoolIP(elemento) {
  var perfilesDropDownStore = new Ext.data.Store({
    autoload: false,
    proxy: {
      type: "ajax",
      url: elemento.get("idElemento") + "/getPerfiles",
      reader: {
        type: "json",
        root: "perfiles",
      },
    },
    storeId: "perfilesDropDown",
    idProperty: "idPerfil",
    fields: [
      { name: "idPerfil", mapping: "idPerfil" },
      { name: "nombrePerfil", mapping: "nombrePerfil" },
    ],
  });

  var paquetesDropDownStore = new Ext.data.Store({
    autoload: true,
    proxy: {
      type: "ajax",
      url: "getPaquetes",
      reader: {
        type: "json",
        root: "paquetes",
      },
    },
    storeId: "paquetesDropDown",
    idProperty: "idPaquetes",
    fields: [
      { name: "idPaquete", mapping: "idPaquete" },
      { name: "nombrePaquete", mapping: "nombrePaquete" },
    ],
  });

  Ext.apply(Ext.form.field.VTypes, {
    ipI: function (v) {
      return /^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/.test(v);
    },
    ipIText: "Formato incorrecto en la IP inicial",
    ipIMask: /[\d\.]/i,
    ipF: function (v) {
      return /^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/.test(v);
    },
    ipFText: "Formato incorrecto en la IP final",
    ipFMask: /[\d\.]/i,
  });

  Ext.define("pool", {
    extend: "Ext.data.Model",
    fields: ["idPaquete", "idPerfil", "ipInicial", "ipFinal", "notificacion"],
  });

  var rowEditing = Ext.create("Ext.grid.plugin.RowEditing", {
    saveBtnText: "Guardar",
    cancelBtnText: "Cancelar",
    errorsText: "Errores",
    dirtyText: "Debe guardar o cancelar sus cambios",
    clicksToMoveEditor: 2,
    autoCancel: false,
  });

  var nombreElemento = elemento.get("nombreElemento");
  var idElemento = elemento.get("idElemento");

  var store = new Ext.data.Store({
    id: "idPoolStore",
    total: "total",
    pageSize: 10,
    autoLoad: false,
    proxy: {
      type: "ajax",
      url: "showPoolIP/" + elemento.get("idElemento"),
      reader: {
        type: "json",
        totalProperty: "total",
        root: "pools",
      },
    },
    fields: [
      { name: "id", mapping: "id" },
      { name: "idPaquete", mapping: "idPaquete" },
      { name: "idPerfil", mapping: "idPerfil" },
      { name: "ipInicial", mapping: "ipInicial" },
      { name: "ipFinal", mapping: "ipFinal" },
      { name: "notificacion", mapping: "notificacion" },
      { name: "disAction", mapping: "disAction" },
    ],
  });

  paquetesDropDownStore.load({
    callback: function (records, operation, success) {
      perfilesDropDownStore.load({
        callback: function (records, operation, success) {
          store.load({
            callback: function (records, operation, success) {
              Ext.MessageBox.hide();
            },
          });
        },
      });
    },
  });

  var gridPool = Ext.create("Ext.grid.Panel", {
    id: "gridPool",
    store: store,
    plugins: [rowEditing],
    timeout: 60000,
    dockedItems: [
      {
        xtype: "toolbar",
        dock: "top",
        align: "->",
        items: [
          { xtype: "tbfill" },
          {
            iconCls: "icon_add",
            text: "Crear",
            itemId: "crearPool",
            scope: this,
            handler: function () {
              rowEditing.cancelEdit();
              var r = Ext.ModelManager.create(
                {
                  perfil: "",
                  ipInicial: "0.0.0.0",
                  ipFinal: "0.0.0.0",
                  notificacion: 1,
                },
                "pool"
              );
              store.insert(0, r);
              rowEditing.startEdit(0, 0);
            },
          },
        ],
      },
    ],
    columns: [
      {
        id: "id",
        header: "id",
        dataIndex: "id",
        hidden: true,
        hideable: false,
      },
      {
        header: "Paquete",
        dataIndex: "idPaquete",
        width: 120,
        editor: {
          xtype: "combobox",
          store: paquetesDropDownStore,
          displayField: "nombrePaquete",
          valueField: "idPaquete",
          allowBlank: false,
          blankText: "Campo Obligatorio.",
        },
        renderer: function (value) {
          if (value != 0 && value != "") {
            if (
              !Ext.isEmpty(paquetesDropDownStore.findRecord("idPaquete", value))
            )
              return paquetesDropDownStore
                .findRecord("idPaquete", value)
                .get("nombrePaquete");
            else return "Plan no existe.";
          } else return "";
        },
      },
      {
        header: "Perfil",
        dataIndex: "idPerfil",
        width: 120,
        sortable: true,
        editor: {
          xtype: "combobox",
          store: perfilesDropDownStore,
          displayField: "nombrePerfil",
          valueField: "idPerfil",
          allowBlank: false,
          blankText: "Campo Obligatorio.",
        },
        renderer: function (value) {
          if (value != 0 && value != "") {
            if (perfilesDropDownStore.findRecord("idPerfil", value) != null)
              return perfilesDropDownStore
                .findRecord("idPerfil", value)
                .get("nombrePerfil");
            else return "Perfil no existe.";
          } else return "";
        },
      },
      {
        header: "IP inicial",
        dataIndex: "ipInicial",
        width: 120,
        sortable: true,
        editor: {
          xtype: "textfield",
          allowBlank: false,
          blankText: "Campo Obligatorio.",
        },
      },
      {
        header: "IP final",
        dataIndex: "ipFinal",
        width: 120,
        sortable: true,
        editor: {
          xtype: "textfield",
          allowBlank: false,
          blankText: "Campo Obligatorio.",
        },
      },
      {
        header: "Notificación",
        dataIndex: "notificacion",
        width: 75,
        sortable: false,
        editor: {
          xtype: "numberfield",
          allowBlank: false,
          blankText: "Campo Obligatorio.",
          minValue: 0,
        },
      },
      {
        xtype: "actioncolumn",
        header: "Acciones",
        width: 75,
        sortable: false,
        items: [
          {
            getClass: function (v, meta, rec) {
              return "button-grid-delete";
            },
            tooltip: "Eliminar",
            handler: function (grid, rowIndex, colIndex) {
              var rec = grid.getStore().getAt(rowIndex);
              Ext.MessageBox.confirm(
                "Borrar Pool",
                "¿Esta seguro de eliminar el pool? \n IP inicial: " +
                  rec.get("ipInicial") +
                  "\nIP final: " +
                  rec.get("ipFinal"),
                function (btn) {
                  if (btn === "yes") {
                    Ext.MessageBox.show({
                      msg: "Eliminando Pool ...",
                      title: "Procesando",
                      progressText: "Eliminando datos.",
                      progress: true,
                      closable: false,
                      width: 300,
                      wait: true,
                      waitConfig: { interval: 200 },
                    });
                    Ext.Ajax.request({
                      url: rec.get("id") + "/deletePool",
                      method: "POST",
                      success: function () {
                        Ext.MessageBox.hide();
                        Ext.getCmp("gridPool").getStore().load();
                        Ext.Msg.alert("Eliminar Pool", "Pool de ip eliminado.");
                      },
                      failure: function () {
                        Ext.getCmp("gridPool").getStore().load();
                        Ext.MessageBox.hide();
                      },
                    });
                  } else {
                    Ext.Msg.alert("No se eliminó el pool.");
                  }
                }
              );
            },
          },
        ],
      },
    ],
    bbar: Ext.create("Ext.PagingToolbar", {
      store: store,
      displayInfo: true,
      displayMsg: "Mostrando {0} - {1} de {2}",
      emptyMsg: "No hay datos que mostrar.",
    }),
  });

  rowEditing.on({
    scope: this,
    validateedit: function (plugin, edit) {
      var ipInicial = edit.newValues.ipInicial;
      var ipFinal = edit.newValues.ipFinal;
      var octIpIni = ipInicial.split(".");
      var octIpFin = ipFinal.split(".");
      var msg = "";

      if (
        /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(
          ipInicial
        )
      ) 
      {
        msg = " ";
      } else {
        msg = "IP inicial " + ipInicial + " no es válida.";
      }

      if (
        /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(
          ipFinal
        )
      ) 
      {
        msg = "";
      } else {
        msg += "<br/>IP final " + ipFinal + " no es válida.";
      }

      if (msg !== "") {
        Ext.MessageBox.show({
          title: "Error",
          msg: msg,
          width: 300,
          icon: Ext.MessageBox.ERROR,
        });
        return false;
      }

      if (
        octIpIni[0] !== octIpFin[0] ||
        octIpIni[1] !== octIpFin[1] ||
        octIpIni[2] !== octIpFin[2]
      ) {
        Ext.MessageBox.show({
          title: "Error",
          msg: "Los 3 primeros octetos deben coincidir.",
          width: 300,
          icon: Ext.MessageBox.ERROR,
        });
        return false;
      } else {
        if (parseInt(octIpIni[3]) > parseInt(octIpFin[3])) {
          Ext.MessageBox.show({
            title: "Error",
            msg: "La IP inicial debe ser menor o igual a la IP final.",
            width: 300,
            icon: Ext.MessageBox.ERROR,
          });
          return false;
        }
      }

      return true;
    },
    afteredit: function (roweditor, changes, record, rowIndex) {
      Ext.MessageBox.show({
        msg: "Validando...",
        title: "Procesando",
        progressText: "Validando pool.",
        progress: true,
        closable: false,
        width: 300,
        wait: true,
        waitConfig: { interval: 200 },
      });
      var idSubred = changes.newValues.id;
      if (changes.newValues.id === "") {
        idSubred = 0;
      }
      Ext.Ajax.request({
        url:
          idSubred +
          "/" +
          changes.newValues.ipInicial +
          "/" +
          changes.newValues.ipFinal +
          "/validaCruceIp",
        method: record.phantom ? "POST" : "PUT",
        params: changes.newValues,
        success: function (response, request) {
          var obj = Ext.decode(response.responseText);
          Ext.MessageBox.hide();

          if (obj.cruce) {
            Ext.MessageBox.show({
              title: "Error",
              msg: obj.olt,
              width: 300,
              icon: Ext.MessageBox.ERROR,
            });
            return false;
          } else {
            Ext.MessageBox.show({
              msg: "Guardando pool ...",
              title: "Procesando",
              progressText: "Guardando datos.",
              progress: true,
              closable: false,
              width: 300,
              wait: true,
              waitConfig: { interval: 200 },
            });

            Ext.Ajax.request({
              url: idElemento + "/updatePool",
              method: record.phantom ? "POST" : "PUT",
              params: changes.newValues,
              success: function () {
                Ext.MessageBox.hide();
                Ext.MessageBox.show({
                  modal: true,
                  title: "Información",
                  msg: "Actualizado correctamente",
                  width: 300,
                  icon: Ext.MessageBox.INFO,
                });
                Ext.getCmp("gridPool").getStore().load();
              },
              failure: function () {
                Ext.MessageBox.hide();
              },
            });
          }
        },
        failure: function () {
          Ext.MessageBox.hide();
        },
      });
    },
  });

  var pop = Ext.create("Ext.window.Window", {
    title: nombreElemento,
    height: 400,
    width: 670,
    modal: true,
    layout: {
      type: "fit",
      align: "stretch",
      pack: "start",
    },
    floating: true,
    shadow: true,
    shadowOffset: 20,
    items: [gridPool],
  });

  pop.on("show", function (win) {
    Ext.MessageBox.show({
      msg: "Obteniendo pools ...",
      title: "Procesando",
      progressText: "obteniendo datos.",
      progress: true,
      closable: false,
      width: 300,
      wait: true,
      waitConfig: { interval: 200 },
    });
  });

  pop.show();
}

function reservarIps(data) {
  storeElementosA = new Ext.data.Store({
    pageSize: 100,
    autoLoad: true,
    proxy: {
      type: "ajax",
      timeout: 800000,
      url: url_elementosOlt,
      extraParams: {
        idServicio: "",
        nombreElemento: this.nombreElemento,
        tipoElemento: data.idTipoElemento,
      },
      reader: {
        type: "json",
        totalProperty: "total",
        root: "encontrados",
      },
    },
    fields: [
      { name: "idElemento", mapping: "idElemento" },
      { name: "nombreElemento", mapping: "nombreElemento" },
      { name: "ipElemento", mapping: "ip" },
    ],
  });

  comboElementos = new Ext.form.ComboBox({
    allowBlank: false,
    id: "cmb_elementoA",
    name: "cmb_elementoA",
    fieldLabel: false,
    anchor: "100%",
    queryMode: "remote",
    width: 300,
    emptyText: "Seleccione Elemento",
    store: storeElementosA,
    displayField: "nombreElemento",
    valueField: "idElemento",
  });

  var formPanel = Ext.create("Ext.form.Panel", {
    bodyPadding: 2,
    waitMsgTarget: true,
    fieldDefaults: {
      labelAlign: "left",
      labelWidth: 85,
      msgTarget: "side",
      bodyStyle: "padding:20px",
    },
    layout: {
      type: "table",
      // The total column count must be specified here
      columns: 1,
    },
    defaults: {
      // applied to each contained panel
      bodyStyle: "padding:20px",
    },
    items: [
      {
        xtype: "fieldset",
        title: "Cantidad Ips Necesitadas",
        defaultType: "textfield",
        defaults: {
          width: 400,
          height: 20,
        },
        items: [
          {
            xtype: "textfield",
            id: "txtIpCant",
            fieldLabel: "N°",
            readOnly: true,
            align: "center",
            width: "15",
          },
        ], //cierre del container table
      },
      //elemento
      {
        xtype: "fieldset",
        title: "Seleccionar OLT HW a obtener Ips",
        defaultType: "textfield",
        defaults: {
          width: 400,
          height: 20,
        },
        items: [
          comboElementos,

          //---------------------------------------
        ], //cierre del container table
      }, //cierre informacion ont
    ], //cierre items
    buttons: [
      {
        text: "Reservar",
        formBind: true,
        handler: function () {
          if (true && Ext.getCmp("cmb_elementoA").value != null) {
            Ext.Msg.confirm(
              "Alerta",
              "Se reservaran Ips para los servicios a Migrar. Desea continuar?",
              function (btn) {
                if (btn == "yes") {
                  connGrabandoDatos.request({
                    timeout: 900000,
                    url: url_ReservarIps,
                    method: "post",
                    params: {
                      idElemento: data.idElemento,
                      idElementoIp: Ext.getCmp("cmb_elementoA").getValue(),
                      strMarcaElemento: data.marcaElemento,
                    },
                    success: function (response) {
                      var objData = Ext.JSON.decode(response.responseText);
                      var strStatus = objData.status;
                      var strMensaje = objData.mensaje;
                      if (strStatus == "OK") {
                        Ext.Msg.alert("Mensaje", strMensaje);
                        store.load();
                        win.destroy();
                      } else {
                        Ext.Msg.alert("Error ", strMensaje);
                        win.destroy();
                      }
                    },
                    failure: function (result) {
                      Ext.Msg.alert("Error ", "Error: " + result.statusText);
                    },
                  });
                }
              }
            );
          }
        },
      },
      {
        text: "Cancelar",
        handler: function () {
          win.destroy();
        },
      },
    ],
  });

  var win = Ext.create("Ext.window.Window", {
    title: "Reserva Ips",
    modal: true,
    width: 350,
    closable: true,
    layout: "fit",
    items: [formPanel],
  }).show();

  connConsultandoDatosIps.request({
    timeout: 900000,
    url: url_ContarIpsOlt,
    method: "post",
    params: {
      idElemento: data.idElemento,
      strMarcaElemento: data.marcaElemento,
    },
    success: function (response) {
      var text = response.responseText;

      if (text == "PROBLEMAS CONTAR") {
        Ext.Msg.alert(
          "Error ",
          "Existieron problemas al realizar la consulta de información, " +
            "favor notificar a sistemas"
        );
        win.destroy();
      } else {
        Ext.getCmp("txtIpCant").setValue(text);
      }
    },
    failure: function (result) {
      Ext.Msg.alert("Error ", "Error: " + result.statusText);
    },
  });
}

function administrarTarjetas(data) {
  var idElemento = data.idElemento;
  Ext.define("estados", {
    extend: "Ext.data.Model",
    fields: [
      { name: "opcion", type: "string" },
      { name: "valor", type: "string" },
    ],
  });

  comboEstados = new Ext.data.Store({
    model: "estados",
    data: [
      { opcion: "Libre", valor: "not connect" },
      { opcion: "Ocupado", valor: "connected" },
      { opcion: "Dañado", valor: "err-disabled" },
      { opcion: "Inactivo", valor: "disabled" },
      { opcion: "Reservado", valor: "reserved" },
      { opcion: "Factible", valor: "Factible" },
    ],
  });

  var comboInterfaces = new Ext.data.Store({
    autoLoad: true,
    proxy: {
      type: "ajax",
      url: url_getInterfacesOlt,
      extraParams: { idElemento: data.idElemento },
      reader: {
        type: "json",
        root: "encontrados",
      },
    },
    fields: [
      { name: "idOlt", mapping: "idOlt" },
      { name: "nombreTarjeta", mapping: "nombreTarjeta" },
      { name: "cantidadPuertos", mapping: "cantidadPuertos" },
      { name: "cantidadConectados", mapping: "cantidadConectados" },
      { name: "cantidadLibres", mapping: "cantidadLibres" },
      { name: "permiteAcciones", mapping: "permiteAcciones" },
    ],
  });

  var permiso = $("#ROLE_227-2877");
  var boolPermiso =
    typeof permiso === "undefined" ? false : permiso.val() == 1 ? true : false;
    var botonAgregarTarjeta = "";
  if (!boolPermiso) {
    botonAgregarTarjeta = null;
  } else {
    botonAgregarTarjeta = Ext.create("Ext.Button", {
      text: "Agregar Tarjeta",
      formBind: true,
      width: "300",
      handler: function () {
        agregarTarjeta(data);
      },
    });
  }

  gridAdministracionPuertos = Ext.create("Ext.grid.Panel", {
    id: "gridAdministracionPuertos",
    store: comboInterfaces,
    columnLines: true,
    columns: [
      {
        id: "idOlt",
        header: "idOlt",
        dataIndex: "idOlt",
        hidden: true,
        hideable: false,
      },
      {
        id: "nombreTarjeta",
        header: "Nombre Tarjeta",
        dataIndex: "nombreTarjeta",
        width: 150,
        hidden: false,
        hideable: false,
      },
      {
        id: "cantidadPuertos",
        header: "Cantidad Puertos",
        dataIndex: "cantidadPuertos",
        width: 125,
        hidden: false,
        hideable: false,
      },
      {
        id: "cantidadConectados",
        header: "Cantidad Conectados",
        dataIndex: "cantidadConectados",
        width: 125,
        hidden: false,
        hideable: false,
      },
      {
        id: "cantidadLibres",
        header: "Cantidad Libres",
        dataIndex: "cantidadLibres",
        width: 105,
        hidden: false,
        hideable: false,
      },
      {
        header: "Acciones",
        width: 143,
        xtype: "actioncolumn",
        items: [
          //VER DETALLE
          {
            getClass: function (v, meta, rec) {
              strBtnOperativo = "button-grid-invisible";
              var permiso = $("#ROLE_227-6");
              var boolPermiso =
                typeof permiso === "undefined"
                  ? false
                  : permiso.val() == 1
                  ? true
                  : false;
              if (!boolPermiso) {
                strBtnOperativo = "button-grid-invisible";
              } else {
                strBtnOperativo = "button-grid-verDetalle";
              }
              return strBtnOperativo;
            },
            tooltip: "Ver Detalle",
            handler: function (grid, rowIndex, colIndex) {
              var objElemento = store.getAt(rowIndex);
              administrarPuertosTarjeta(grid.getStore().getAt(rowIndex).data);
            },
          },
          //AGREGAR 8 PUERTOS
          {
            getClass: function (v, meta, rec) {
              strBtnOperativo = "button-grid-invisible";
              if (rec.data.permiteAcciones == "NO") {
                var permiso = $("#ROLE_227-2877");
                var boolPermiso =
                  typeof permiso === "undefined"
                    ? false
                    : permiso.val() == 1
                    ? true
                    : false;
                if (!boolPermiso) {
                  strBtnOperativo = "button-grid-invisible";
                } else {
                  strBtnOperativo = "button-grid-direccionUp";
                }
              } else {
                strBtnOperativo = "button-grid-invisible";
              }

              return strBtnOperativo;
            },
            tooltip: "Agregar Puertos",
            handler: function (grid, rowIndex, colIndex) {
              var objElemento = store.getAt(rowIndex);
              Ext.Msg.confirm(
                "Alerta",
                "Se agregaran 8 puertos adicionales a la tarjeta. Desea continuar?",
                function (btn) {
                  if (btn == "yes") {
                    Ext.MessageBox.show({
                      msg: "Guardando...",
                      title: "Procesando",
                      progressText: "Guardando.",
                      progress: true,
                      closable: false,
                      width: 300,
                      wait: true,
                      waitConfig: { interval: 200 },
                    });

                    Ext.Ajax.request({
                      timeout: 900000,
                      url: url_agregarPuertosTarjeta,
                      method: "post",
                      params: {
                        idElemento: grid.getStore().getAt(rowIndex).data.idOlt,
                        nombreTarjeta: grid.getStore().getAt(rowIndex).data
                          .nombreTarjeta,
                      },
                      success: function (response) {
                        var text = response.responseText;

                        if (text == "PROBLEMAS TRANSACCION") {
                          Ext.Msg.alert(
                            "Error ",
                            "Existieron problemas al realizar la transaccion, " +
                              "favor notificar a sistemas"
                          );
                        } else {
                          Ext.Msg.alert("Mensaje ", text);
                          comboInterfaces.load();
                        }
                      },
                      failure: function (result) {
                        Ext.Msg.alert("Error ", "Error: " + result.statusText);
                      },
                    });
                  }
                }
              );
            },
          },
          //REDUCIR A 8 PUERTOS
          {
            getClass: function (v, meta, rec) {
              strBtnOperativo = "button-grid-invisible";
              if (rec.data.permiteAcciones == "SI") {
                var permiso = $("#ROLE_227-2877");
                var boolPermiso =
                  typeof permiso === "undefined"
                    ? false
                    : permiso.val() == 1
                    ? true
                    : false;
                if (!boolPermiso) {
                  strBtnOperativo = "button-grid-invisible";
                } else {
                  strBtnOperativo = "button-grid-direccionDown";
                }
              } else {
                strBtnOperativo = "button-grid-invisible";
              }
              return strBtnOperativo;
            },
            tooltip: "Reducir Puertos",
            handler: function (grid, rowIndex, colIndex) {
              var objElemento = store.getAt(rowIndex);
              Ext.Msg.confirm(
                "Alerta",
                "Se reduciran 8 puertos a la tarjeta. Desea continuar?",
                function (btn) {
                  if (btn == "yes") {
                    Ext.MessageBox.show({
                      msg: "Guardando...",
                      title: "Procesando",
                      progressText: "Guardando.",
                      progress: true,
                      closable: false,
                      width: 300,
                      wait: true,
                      waitConfig: { interval: 200 },
                    });

                    Ext.Ajax.request({
                      timeout: 900000,
                      url: url_reducirPuertosTarjeta,
                      method: "post",
                      params: {
                        idElemento: grid.getStore().getAt(rowIndex).data.idOlt,
                        nombreTarjeta: grid.getStore().getAt(rowIndex).data
                          .nombreTarjeta,
                        tipoOperacion: "reducir",
                      },
                      success: function (response) {
                        var text = response.responseText;

                        if (text == "PROBLEMAS TRANSACCION") {
                          Ext.Msg.alert(
                            "Error ",
                            "Existieron problemas al realizar la transaccion, " +
                              "favor notificar a sistemas"
                          );
                        } else {
                          Ext.Msg.alert("Mensaje ", text);
                          comboInterfaces.load();
                        }
                      },
                      failure: function (result) {
                        Ext.Msg.alert("Error ", "Error: " + result.statusText);
                      },
                    });
                  }
                }
              );
            },
          },
          //ELIMINAR TARJETA
          {
            getClass: function (v, meta, rec) {
              strBtnOperativo = "button-grid-invisible";
              var permiso = $("#ROLE_227-2877");
              var boolPermiso =
                typeof permiso === "undefined"
                  ? false
                  : permiso.val() == 1
                  ? true
                  : false;
              if (!boolPermiso) {
                strBtnOperativo = "button-grid-invisible";
              } else {
                strBtnOperativo = "button-grid-eliminar";
              }
              return strBtnOperativo;
            },
            tooltip: "Eliminar Tarjeta",
            handler: function (grid, rowIndex, colIndex) {
              var objElemento = store.getAt(rowIndex);
              Ext.Msg.confirm(
                "Alerta",
                "Se eliminara la tarjeta seleccionada. Desea continuar?",
                function (btn) {
                  if (btn == "yes") {
                    Ext.MessageBox.show({
                      msg: "Guardando...",
                      title: "Procesando",
                      progressText: "Guardando.",
                      progress: true,
                      closable: false,
                      width: 300,
                      wait: true,
                      waitConfig: { interval: 200 },
                    });

                    Ext.Ajax.request({
                      timeout: 900000,
                      url: url_reducirPuertosTarjeta,
                      method: "post",
                      params: {
                        idElemento: grid.getStore().getAt(rowIndex).data.idOlt,
                        nombreTarjeta: grid.getStore().getAt(rowIndex).data
                          .nombreTarjeta,
                        tipoOperacion: "eliminar",
                      },
                      success: function (response) {
                        var text = response.responseText;

                        if (text == "PROBLEMAS TRANSACCION") {
                          Ext.Msg.alert(
                            "Error ",
                            "Existieron problemas al realizar la transaccion, " +
                              "favor notificar a sistemas"
                          );
                        } else {
                          Ext.Msg.alert("Mensaje ", text);
                          comboInterfaces.load();
                        }
                      },
                      failure: function (result) {
                        Ext.Msg.alert("Error ", "Error: " + result.statusText);
                      },
                    });
                  }
                }
              );
            },
          },
        ],
      },
    ],
    viewConfig: {
      stripeRows: true,
      enableTextSelection: true,
    },
    width: 660,
    height: 95,
    frame: true,
  });

  var formPanel = Ext.create("Ext.form.Panel", {
    bodyPadding: 2,
    waitMsgTarget: true,
    fieldDefaults: {
      labelAlign: "left",
      labelWidth: 85,
      msgTarget: "side",
      bodyStyle: "padding:20px",
    },
    layout: {
      type: "table",
      // The total column count must be specified here
      columns: 1,
    },
    defaults: {
      // applied to each contained panel
      bodyStyle: "padding:20px",
    },
    items: [
      //elemento
      {
        xtype: "fieldset",
        title: "Informacion del Elemento",
        defaultType: "textfield",
        defaults: {
          width: 600,
          height: 22,
        },
        items: [
          {
            xtype: "container",
            layout: {
              type: "table",
              columns: 2,
              align: "stretch",
            },
            items: [
              {
                xtype: "textfield",
                id: "elemento",
                name: "elemento",
                fieldLabel: "Elemento",
                displayField: data.nombreElemento,
                value: data.nombreElemento,
                readOnly: true,
                width: "500",
              },
              //---------------------------------------
            ],
          },
        ], //cierre del fieldset
      }, //cierre informacion ont
      botonAgregarTarjeta,
      {
        xtype: "fieldset",
        title: "Puertos",
        defaultType: "textfield",
        defaults: {
          width: 500,
          height: 200,
        },
        items: [gridAdministracionPuertos],
      }, //cierre interfaces cpe
    ], //cierre items
  });
  let win = Ext.widget("window", {
    title: "Administracion de Tarjetas",
    modal: true,
    width: 700,
    closable: true,
    layout: "fit",
    items: [formPanel],
  });
  win.show();
  function agregarTarjeta(objElemento) {
  let idElemento = objElemento.idElemento;
  let storeTarjetaOlt = new Ext.data.Store({
    pageSize: 100,
    listeners: {
      load: function () {},
    },
    proxy: {
      timeout: 400000,
      type: "ajax",
      url: url_getPuertoTarjetaOlt,
      extraParams: {
        marcaElemento: objElemento.marcaElemento,
        modeloElemento: objElemento.modeloElemento,
        tipoConsulta: "TARJETA"
      },
      reader: {
        type: "json",
        totalProperty: "total",
        root: "encontrados",
      },
    },
    fields: [{ name: "tarjetaOlt", mapping: "tarjetaOlt" }],
    autoLoad: true,
  });
  let storePuertoOlt = new Ext.data.Store({
    pageSize: 100,
    listeners: {
      load: function () {},
    },
    proxy: {
      timeout: 400000,
      type: "ajax",
      url: url_getPuertoTarjetaOlt,
      extraParams: {
        marcaElemento: objElemento.marcaElemento,
        modeloElemento: objElemento.modeloElemento,
        tipoConsulta: "PUERTO"
      },
      reader: {
        type: "json",
        totalProperty: "total",
        root: "encontrados",
      },
    },
    fields: [{ name: "puertoOlt", mapping: "puertoOlt" }],
    autoLoad: true,
  });
    var formPanelAgregar = Ext.create("Ext.form.Panel", {
      bodyPadding: 2,
      waitMsgTarget: true,
      fieldDefaults: {
        labelAlign: "center",
        labelWidth: 85,
        msgTarget: "side",
        bodyStyle: "padding:20px",
      },
      buttonAlign: "center",
      layout: {
        type: "table",
        // The total column count must be specified here
        columns: 1,
      },
      defaults: {
        // applied to each contained panel
        bodyStyle: "padding:20px",
      },
      items: [
        {
          xtype: "fieldset",
          defaultType: "textfield",
          defaults: {
            width: 250,
            height: 250,
          },
          items: [
            {
              queryMode: "local",
              id: "comboTarjeta",
              editable: false,
              xtype: "combobox",
              fieldLabel: "* Tarjeta",
              selectOnTab: true,
              width: 250,
              height: 30,
              store: storeTarjetaOlt,
              displayField: "tarjetaOlt",
              valueField: "tarjetaOlt",
              loadingText: "Buscando ...",
              listClass: "x-combo-list-small",
            },
            {
              queryMode: "local",
              id: "comboPuertos",
              editable: false,
              xtype: "combobox",
              fieldLabel: "* Cantidad Puertos",
              selectOnTab: true,
              width: 250,
              height: 30,
              store: storePuertoOlt,
              displayField: "puertoOlt",
              valueField: "puertoOlt",
              loadingText: "Buscando ...",
              listClass: "x-combo-list-small",
            },
          ],
        }, //cierre interfaces cpe
      ], //cierre items
      buttons: [
        {
          xtype: "button",
          text: "Crear",
          align: "center",
          formBind: true,
          width: "250",
          height: 30,
          handler: function () {
            if (
              null == Ext.getCmp("comboTarjeta").getValue() ||
              null == Ext.getCmp("comboPuertos").getValue()
            ) {
              alert("Ingrese los parametros solicitados.");
            } else {
              Ext.MessageBox.show({
                msg: "Guardando...",
                title: "Procesando",
                progressText: "Guardando.",
                progress: true,
                closable: false,
                width: 300,
                wait: true,
                waitConfig: { interval: 200 },
              });

              Ext.Ajax.request({
                timeout: 900000,
                url: url_agregarTarjeta,
                method: "post",
                params: {
                  idElemento: idElemento,
                  nombreTarjeta: Ext.getCmp("comboTarjeta").getValue(),
                  cantidadPuertos: Ext.getCmp("comboPuertos").getValue(),
                },
                success: function (response) {
                  var text = response.responseText;

                  if (text == "PROBLEMAS TRANSACCION") {
                    Ext.Msg.alert(
                      "Error ",
                      "Existieron problemas al realizar la transaccion, " +
                        "favor notificar a sistemas"
                    );
                  } else {
                    if (text == "Tarjeta agregada exitosamente!") {
                      Ext.Msg.alert("Mensaje ", text);
                      winAgregarTarjeta.destroy();
                      comboInterfaces.load();
                    } else {
                      Ext.Msg.alert("Mensaje ", text);
                    }
                  }
                },
                failure: function (result) {
                  Ext.Msg.alert("Error ", "Error: " + result.statusText);
                },
              });
            }
          },
        },
      ],
    });

    var winAgregarTarjeta = Ext.create("Ext.window.Window", {
      title: "Agregar Tarjeta",
      modal: true,
      width: 290,
      closable: true,
      layout: "fit",
      items: [formPanelAgregar],
    }).show();
  }

  function administrarPuertosTarjeta(data) {
    var comboInterfacesOltTarjeta = new Ext.data.Store({
      total: "total",
      autoLoad: true,
      proxy: {
        type: "ajax",
        url: url_getInterfacesTarjeta,
        extraParams: {
          idElemento: data.idOlt,
          nombreTarjeta: data.nombreTarjeta,
        },
        reader: {
          type: "json",
          totalProperty: "total",
          root: "encontrados",
        },
      },
      fields: [
        { name: "nombreInterfaceElemento", mapping: "nombreInterfaceElemento" },
        { name: "estado", mapping: "estado" },
      ],
    });

    gridAdministracionPuertosTarjeta = Ext.create("Ext.grid.Panel", {
      id: "gridAdministracionPuertosDetalle",
      store: comboInterfacesOltTarjeta,
      columnLines: true,
      columns: [
        {
          id: "nombreInterfaceElemento",
          header: "Interface Elemento",
          dataIndex: "nombreInterfaceElemento",
          width: 138,
          hidden: false,
          hideable: false,
        },
        {
          id: "estado",
          header: "Estado",
          dataIndex: "estado",
          width: 100,
          sortable: true,
          renderer: function (
            value,
            metadata,
            record,
            rowIndex,
            colIndex,
            store
          ) {
            for (var i = 0; i < comboEstados.data.items.length; i++) {
              if (comboEstados.data.items[i].data.valor == record.data.estado) {
                if (comboEstados.data.items[i].data.valor == "not connect") {
                  record.data.estado = "Libre";
                  break;
                } else if (
                  comboEstados.data.items[i].data.valor == "connected"
                ) {
                  record.data.estado = "Ocupado";
                  break;
                } else if (
                  comboEstados.data.items[i].data.valor == "err-disabled"
                ) {
                  record.data.estado = "Dañado";
                  break;
                } else if (
                  comboEstados.data.items[i].data.valor == "disabled"
                ) {
                  record.data.estado = "Inactivo";
                  break;
                } else if (
                  comboEstados.data.items[i].data.valor == "Factible"
                ) {
                  record.data.estado = "Factible";
                  break;
                } else if (
                  comboEstados.data.items[i].data.valor == "reserved"
                ) {
                  record.data.estado = "Reservado";
                  break;
                }
              }
            }

            return record.data.estado;
          },
        },
      ],
      viewConfig: {
        stripeRows: true,
        enableTextSelection: true,
      },
      width: 250,
      height: 250,
      frame: true,
    });
    
    let _formPanelPuertos = Ext.create("Ext.form.Panel", {
      bodyPadding: 2,
      waitMsgTarget: true,
      fieldDefaults: {
        labelAlign: "left",
        labelWidth: 85,
        msgTarget: "side",
        bodyStyle: "padding:20px",
      },
      layout: {
        type: "table",
        // The total column count must be specified here
        columns: 1,
      },
      defaults: {
        // applied to each contained panel
        bodyStyle: "padding:20px",
      },
      items: [
        {
          xtype: "fieldset",
          defaultType: "textfield",
          defaults: {
            width: 250,
            height: 250,
          },
          items: [gridAdministracionPuertosTarjeta],
        }, //cierre interfaces cpe
      ], //cierre items
    });

    var winPuertosTarjeta = Ext.create("Ext.window.Window", {
      title: "Puertos Tarjeta Olt",
      modal: true,
      width: 290,
      closable: false,
      layout: "fit",
      items: [gridAdministracionPuertosTarjeta],
      buttons: [
        {
          xtype: "button",
          text: "Cerrar",
          align: "center",
          formBind: true,
          width: "250",
          height: 30,
          handler: function () {
            winPuertosTarjeta.destroy();
          },
        },
      ],
    }).show();
  }
}

function asignarValorPorDefecto(){
  return null;
}

function guardarDetalleMigracion(intIdElemento, marcaElemento) {
  Ext.MessageBox.wait("Iniciando migración...");

  if (marcaElemento === "TELLION") {
    activarMigracionOlt(intIdElemento);
  } else {
    Ext.Ajax.request({
      url: strUrlEliminarDetalles,
      method: "post",
      timeout: 1000000,
      params: {
        idElemento: intIdElemento,
      },
      success: function (response) {
        var text = response.responseText;

        if ("OK" == text) {
          actualizarCaracteristicasElementos(intIdElemento);
        } else {
          Ext.MessageBox.hide();
          Ext.Msg.alert("Error", text);
        }
      },
      failure: function (result) {
        Ext.MessageBox.hide();
        Ext.Msg.alert("Error", result.responseText);
      },
    });
  }
}

function activarMigracionOlt(intIdElemento) {
  Ext.MessageBox.show({
    msg: "Guardando datos...",
    title: "Procesando",
    progressText: "Mensaje",
    progress: true,
    closable: false,
    width: 300,
    wait: true,
    waitConfig: { interval: 200 },
  });
  Ext.Ajax.request({
    url: strUrlIniciarMigracion,
    method: "post",
    params: {
      intIdElemento: intIdElemento,
    },
    success: function (response) {
      Ext.MessageBox.hide();
      var objData = Ext.JSON.decode(response.responseText);
      var strStatus = objData.status;
      var strMensaje = objData.mensaje;
      if (strStatus == "OK") {
        store.load();
        Ext.Msg.alert(
          "Mensaje",
          "El cambio de plan masivo en el OLT ha iniciado"
        );
      } else {
        Ext.Msg.alert("Error ", strMensaje);
      }
    },
    failure: function (result) {
      Ext.MessageBox.hide();
      Ext.Msg.alert("Error", result.responseText);
    },
  });
}

function actualizarCaracteristicasElementos(intIdElemento) {
  Ext.Ajax.request({
    url: actualizarCaractAjax,
    method: "post",
    timeout: 1000000,
    params: {
      idElemento: intIdElemento,
    },
    success: function (response) {
      var text = response.responseText;
      var posicion = text.search("Se grabaron los Traffic Table");

      if (posicion > 0) {
        activarMigracionOlt(intIdElemento);
      } else {
        Ext.MessageBox.hide();
        Ext.Msg.alert("Error", text);
      }
    },
    failure: function (result) {
      Ext.MessageBox.hide();
      Ext.Msg.alert("Error", result.responseText);
    },
  });
}

function migrar() {
  var strIdElemento = "";
  for (var idElementoSeleccionado in arrayIdsOltsSelecccionadosGrid) {
    strIdElemento = strIdElemento + idElementoSeleccionado + "|";
  }
  if (strIdElemento !== "") {
    var storeOlt = new Ext.data.Store({
      pageSize: 28,
      total: "total",
      autoLoad: true,
      proxy: {
        timeout: 600000,
        type: "ajax",
        url: urlOltMigraMcafee,
        reader: {
          type: "json",
          totalProperty: Object.keys(arrayIdsOltsSelecccionadosGrid).length,
          root: "encontrados",
        },
        extraParams: {
          arrayElemento: strIdElemento,
        },
      },
      fields: [
        { name: "idElemento", mapping: "idElemento" },
        { name: "nombreElemento", mapping: "nombreElemento" },
        { name: "cantidad", mapping: "cantidad" },
        { name: "arrayElemento", mapping: "arrayElemento" },
      ],
    });

    gridOlt = Ext.create("Ext.grid.Panel", {
      id: "gridOlt",
      width: "100%",
      height: 425,
      store: storeOlt,
      loadMask: true,
      frame: false,
      columns: [
        {
          header: "Código",
          dataIndex: "idElemento",
          width: "20%",
          sortable: true,
          name: "id",
          id: "id",
        },
        {
          header: "Olt",
          dataIndex: "nombreElemento",
          width: "50%",
          sortable: true,
          name: "nombre",
          id: "nombre",
        },
        {
          header: "Servicios",
          dataIndex: "cantidad",
          width: "16%",
          sortable: true,
          name: "cantidad",
          id: "cantidad",
        },
        {
          header: "Acciones",
          xtype: "actioncolumn",
          width: "14%",
          sortable: true,
          items: [
            {
              tooltip: "Quitar",
              getClass: function () {
                return "button-grid-deleteRecord";
              },
              handler: function (grid, rowIndex, colIndex) {
                storeOlt.removeAt(rowIndex);
              },
            },
          ],
        },
      ],
      bbar: Ext.create("Ext.PagingToolbar", {
        store: storeOlt,
        displayInfo: true,
        displayMsg: "Mostrando {0} - {1} de {2}",
        emptyMsg: "No hay datos que mostrar.",
      }),
    });

    var formPanel = Ext.create("Ext.form.Panel", {
      bodyPadding: 5,
      waitMsgTarget: true,
      fieldDefaults: {
        labelAlign: "left",
        labelWidth: 85,
        msgTarget: "side",
      },

      items: [
        {
          xtype: "fieldset",
          autoHeight: true,
          width: 480,
          height: 450,
          items: [gridOlt],
        },
      ],

      buttons: [
        {
          text: "Migrar",
          handler: function (grid, rowIndex, colIndex) {
            var strElemento = "";
            var arrayOlt = storeOlt.data.items;
            if (arrayOlt.length > 0) {
              Ext.Msg.confirm(
                "Alerta",
                "¿Está seguro de generar registros para la migración masiva?",
                function (btnCarga) {
                  if (btnCarga == "yes") {
                    for (var k = 0; k < arrayOlt.length; ++k) {
                      if (arrayOlt[k].data.cantidad > 0) {
                        strElemento =
                          strElemento + arrayOlt[k].data.idElemento + "|";
                      }
                    }

                    var conn = new Ext.data.Connection({
                      listeners: {
                        beforerequest: {
                          fn: function (con, opt) {
                            Ext.get(document.body).mask("Grabando datos...");
                          },
                          scope: this,
                        },
                        requestcomplete: {
                          fn: function (con, res, opt) {
                            Ext.get(document.body).unmask();
                          },
                          scope: this,
                        },
                        requestexception: {
                          fn: function (con, res, opt) {
                            Ext.get(document.body).unmask();
                          },
                          scope: this,
                        },
                      },
                    });

                    conn.request({
                      method: "POST",
                      params: {
                        arrayListado: strElemento,
                      },
                      url: urlGrabaMigracion,
                      success: function (response) {
                        var text = response.responseText;
                        if (text == "OK") {
                          Ext.Msg.alert(
                            "Mensaje",
                            "Registro de migración masiva generada con éxito"
                          );
                          arrayIdsOltsSelecccionadosGrid = {};
                          winMigracion.destroy();
                          limpiar();
                        } else {
                          Ext.Msg.alert("Mensaje", text);
                        }
                      },
                      failure: function (result) {
                        Ext.Msg.alert("Error ", "Error: " + result.statusText);
                      },
                    });
                  }
                }
              );
            } else {
              Ext.Msg.alert(
                "Atención",
                "No existen olts en el listado para realizar la migración"
              );
            }
          },
        },
        {
          text: "Salir",
          handler: function () {
            winMigracion.destroy();
          },
        },
      ],
    });

    winMigracion = Ext.create("Ext.window.Window", {
      title: "Listado de Olts a migrar",
      modal: true,
      width: 510,
      height: 525,
      closable: true,
      layout: "fit",
      items: [formPanel],
    }).show();
  } else {
    Ext.Msg.alert("Error", "No se ha seleccionado ningún olt");
  }
}

function listarOltsInicioCambioPlanMasivo() {
  storeOltsCpm = new Ext.data.Store({
    pageSize: 20,
    total: "intTotal",
    autoLoad: true,
    proxy: {
      type: "ajax",
      method: "post",
      url: strUrlGetOltsInicioCpm,
      reader: {
        type: "json",
        totalProperty: "intTotal",
        root: "arrayResultado",
      },
    },
    fields: [
      { name: "idOlt", mapping: "idOlt" },
      { name: "nombreOlt", mapping: "nombreOlt" },
      { name: "estadoOlt", mapping: "estadoOlt" },
      { name: "numLogines", mapping: "numLogines" },
    ],
  });

  gridOltsCpm = Ext.create("Ext.grid.Panel", {
    id: "gridOltsCpm",
    width: "100%",
    height: 400,
    store: storeOltsCpm,
    loadMask: true,
    frame: false,
    columns: [
      {
        id: "idOlt",
        header: "idOlt",
        dataIndex: "idOlt",
        hidden: true,
        hideable: false,
      },
      {
        header: "Olt",
        dataIndex: "nombreOlt",
        width: "50%",
        sortable: true,
        name: "nombreOlt",
        id: "nombreOlt",
      },
      {
        header: "Estado del Olt",
        dataIndex: "estadoOlt",
        width: "30%",
        sortable: true,
        name: "estadoOlt",
        id: "estadoOlt",
      },
      {
        header: "# Logines",
        dataIndex: "numLogines",
        width: "16%",
        sortable: true,
        name: "numLogines",
        id: "numLogines",
      },
    ],
    bbar: Ext.create("Ext.PagingToolbar", {
      store: storeOltsCpm,
      displayInfo: true,
      displayMsg: "Mostrando {0} - {1} de {2}",
      emptyMsg: "No hay datos que mostrar.",
    }),
  });

  var formPanelOltsCpm = Ext.create("Ext.form.Panel", {
    bodyPadding: 5,
    waitMsgTarget: true,
    fieldDefaults: {
      labelAlign: "left",
      labelWidth: 85,
      msgTarget: "side",
    },

    items: [
      {
        xtype: "fieldset",
        autoHeight: true,
        width: 450,
        height: 425,
        items: [gridOltsCpm],
      },
    ],

    buttons: [
      {
        text: "Ejecutar Cambio de Plan Masivo",
        handler: function (grid, rowIndex, colIndex) {
          var arrayRegistrosOlts = storeOltsCpm.data.items;
          if (arrayRegistrosOlts.length > 0) {
            Ext.Msg.confirm(
              "Alerta",
              "¿Está seguro de iniciar el Cambio de Plan Masivo de todos los Olts del listado?",
              function (btn) {
                if (btn == "yes") {
                  connGrabandoDatos.request({
                    method: "POST",
                    url: strUrlEjecutarCpmOlts,
                    success: function (response) {
                      var objData = Ext.JSON.decode(response.responseText);
                      var strStatus = objData.status;
                      var strMensaje = objData.mensaje;
                      if (strStatus == "OK") {
                        Ext.Msg.alert("Mensaje", strMensaje);
                        store.load();
                      } else {
                        Ext.Msg.alert("Error ", strMensaje);
                      }
                      winOltsCpm.destroy();
                    },
                    failure: function (result) {
                      Ext.Msg.alert("Error ", "Error: " + result.statusText);
                    },
                  });
                }
              }
            );
          } else {
            Ext.Msg.alert(
              "Atención",
              "No existen olts en el listado para ejecutar el cambio de plan masivo"
            );
          }
        },
      },
      {
        text: "Salir",
        handler: function () {
          winOltsCpm.destroy();
        },
      },
    ],
  });

  winOltsCpm = Ext.create("Ext.window.Window", {
    title: "Listado de Olts para ejecución de Cambio de plan masivo",
    modal: true,
    width: 480,
    height: 500,
    closable: true,
    layout: "fit",
    items: [formPanelOltsCpm],
  }).show();
}

function subir() {
  var formPanel = Ext.widget("form", {
    width: 400,
    bodyPadding: 10,
    items: [
      {
        xtype: "filefield",
        name: "archivoScope",
        id: "archivoScope",
        fieldLabel: "Archivo a cargar(*):",
        labelWidth: 120,
        anchor: "100%",
        buttonText: "Seleccionar Archivo...",
      },
    ],
    buttons: [
      {
        text: "Guardar",
        handler: function () {
          var form = this.up("form").getForm();
          //Se valida mini formulario para ingreso de elementos masivos
          var archivoScopeComp = Ext.getCmp("archivoScope").value;

          if (!archivoScopeComp) {
            Ext.Msg.alert("Advertencia", "Debe seleccionar el archivo a subir");
          } else {
            var archivoFinal = archivoScopeComp.toLowerCase();
            var ext = getFileExt(archivoFinal);
            if (ext == "csv") {
              form.submit({
                url: url_ingresoMasivo,
                waitMsg: "Subiendo Archivo...",
                success: function (response) {
                  Ext.Msg.alert("Mensaje ", "Archivo subido exitosamente");
                  win.destroy();
                },
                failure: function (rec, op) {
                  var json = Ext.JSON.decode(op.response.responseText);
                  Ext.Msg.alert("Alerta ", json.mensaje);
                },
              });
            } else {
              Ext.Msg.alert(
                "Advertencia",
                "Solo se aceptan archivos con extensión .csv"
              );
              Ext.getCmp("archivoScope").value = "";
              Ext.getCmp("archivoScope").setRawValue("");
            }
          }
        },
      },
      {
        text: "Salir",
        handler: function () {
          win.destroy();
        },
      },
    ],
  });

  var win = Ext.create("Ext.window.Window", {
    title: "Ingreso Masivo de Policy/Scopes",
    modal: true,
    width: 600,
    closable: true,
    layout: "fit",
    items: [formPanel],
  }).show();
}

// Obtener extensión
function getFileExt(sPTF, bDot) {
  if (!bDot) {
    bDot = false;
  }
  return sPTF.substr(sPTF.lastIndexOf(".") + (!bDot ? 1 : 0));
}

function listarOltsMultiplataforma() {
  var permiso = $("#ROLE_227-8018");
  var boolPermisoAdd =
    typeof permiso === "undefined" ? false : permiso.val() == 1 ? true : false;
  storeOltMultiplataforma = Ext.create("Ext.data.Store", {
    id: "storeOltMultiplataforma",
    model: "ListModelOltMultiplataforma",
    autoLoad: true,
    pageSize: 5000,
    total: "total",
    proxy: {
      type: "ajax",
      url: urlOltMultiplataforma,
      actionMethods: "POST",
      timeout: 3000000,
      reader: {
        type: "json",
        totalProperty: "total",
        root: "registros",
      },
    },
    fields: [
      { name: "idSolicitud", mapping: "idSolicitud" },
      { name: "nombreOlt", mapping: "nombreOlt" },
      { name: "nombreNodo", mapping: "nombreNodo" },
      { name: "nombreAgregador", mapping: "nombreAgregador" },
      { name: "nombrePe", mapping: "nombrePe" },
      { name: "dirIpv6", mapping: "dirIpv6" },
      { name: "estadoSolicitud", mapping: "estadoSolicitud" },
    ],
  });
  filterPanelOltMultiplataforma = Ext.create("Ext.panel.Panel", {
    id: "filterPanelOltMultiplataforma",
    bodyPadding: 7,
    border: false,
    buttonAlign: "center",
    layout: {
      type: "table",
      columns: 3,
      align: "stretch",
    },
    bodyStyle: {
      background: "#fff",
    },
    collapsible: false,
    collapsed: false,
    width: "100%",
    height: "100%",
    title: "Criterios de busqueda",
    buttons: [
      {
        text: "Buscar",
        iconCls: "icon_search",
        handler: function () {
          buscarOltMultiplataforma();
        },
      },
      {
        text: "Limpiar",
        iconCls: "icon_limpiar",
        handler: function () {
          limpiarOltMultiplataforma();
        },
      },
    ],
    items: [
      {
        xtype: "textfield",
        id: "txtNombreOltMulti",
        fieldLabel: "Nombre Olt",
        value: "",
        matchFieldWidth: true,
        width: "90%",
      },
      { width: "10%", border: false },
      {
        xtype: "textfield",
        id: "txtNombreNodoMulti",
        fieldLabel: "Nombre Nodo",
        value: "",
        width: "90%",
      },
      //------
      {
        xtype: "textfield",
        id: "txtNombreAgregadorMulti",
        fieldLabel: "Nombre Agregador",
        value: "",
        width: "90%",
      },
      { width: "10%", border: false },
      {
        xtype: "textfield",
        id: "txtNombrePeMulti",
        fieldLabel: "Nombre PE",
        value: "",
        width: "90%",
      },
      //------
      {
        xtype: "textfield",
        id: "txtIpv6Multi",
        fieldLabel: "Ipv6",
        value: "",
        width: "90%",
      },
      { width: "10%", border: false },
      { width: "10%", border: false },
    ],
  });
  gridOltsMultiplataforma = Ext.create("Ext.grid.Panel", {
    id: "gridOltsMultiplataforma",
    title: "Lista de Olt Multiplataforma",
    width: "100%",
    height: 400,
    store: storeOltMultiplataforma,
    loadMask: true,
    frame: false,
    autoScroll: true,
    iconCls: "icon-grid",
    columns: [
      {
        id: "idSolicitud",
        header: "idSolicitud",
        dataIndex: "idSolicitud",
        hidden: true,
        hideable: false,
      },
      {
        header: "Olt",
        dataIndex: "nombreOlt",
        width: "19%",
        sortable: true,
      },
      {
        header: "Nodo",
        dataIndex: "nombreNodo",
        width: "12%",
        sortable: true,
      },
      {
        header: "Agregador",
        dataIndex: "nombreAgregador",
        width: "12%",
        sortable: true,
      },
      {
        header: "PE",
        dataIndex: "nombrePe",
        width: "14%",
        sortable: true,
      },
      {
        header: "IPv6",
        dataIndex: "dirIpv6",
        width: "19%",
        sortable: true,
      },
      {
        header: "Estado Solicitud",
        dataIndex: "estadoSolicitud",
        width: "14%",
        sortable: true,
      },
      {
        xtype: "actioncolumn",
        header: "Acciones",
        width: "10%",
        items: [
          {
            tooltip: "Ver Recursos Asignados",
            getClass: function (v, meta, rec) {
              return "button-grid-show";
            },
            handler: function (grid, rowIndex) {
              var rec = grid.getStore().getAt(rowIndex);
              viewDataOltMultiplataforma(rec.get("idSolicitud"));
            },
          },
          {
            tooltip: "Asignar Recursos",
            getClass: function (v, meta, rec) {
              strClass = "button-grid-invisible";
              var permiso = $("#ROLE_227-8037");
              var boolPermiso =
                typeof permiso === "undefined"
                  ? false
                  : permiso.val() == 1
                  ? true
                  : false;
              if (boolPermiso && rec.get("estadoSolicitud") == "Pendiente") {
                strClass = "button-grid-informacionTecnica";
              }
              return strClass;
            },
            handler: function (grid, rowIndex) {
              var rec = grid.getStore().getAt(rowIndex);
              panelAsignarOltMultiplataforma(
                rec.get("idSolicitud"),
                rec.get("nombreOlt")
              );
            },
          },
          {
            tooltip: "Configurar",
            getClass: function (v, meta, rec) {
              strClass = "button-grid-invisible";
              var permiso = $("#ROLE_227-8038");
              var boolPermiso =
                typeof permiso === "undefined"
                  ? false
                  : permiso.val() == 1
                  ? true
                  : false;
              if (boolPermiso && rec.get("estadoSolicitud") == "Asignado") {
                strClass = "button-grid-Tuerca-2";
              }
              return strClass;
            },
            handler: function (grid, rowIndex) {
              var rec = grid.getStore().getAt(rowIndex);
              configurarOltMultiplataforma(
                rec.get("idSolicitud"),
                rec.get("nombreOlt")
              );
            },
          },
          {
            tooltip: "Reversar Solicitud Multiplataforma",
            getClass: function (v, meta, rec) {
              strClass = "button-grid-invisible";
              var permiso = $("#ROLE_227-8019");
              var boolPermiso =
                typeof permiso === "undefined"
                  ? false
                  : permiso.val() == 1
                  ? true
                  : false;
              if (boolPermiso) {
                strClass = "button-reversar";
              }
              return strClass;
            },
            handler: function (grid, rowIndex) {
              var rec = grid.getStore().getAt(rowIndex);
              reversarOltMultiplataforma(
                rec.get("idSolicitud"),
                rec.get("nombreOlt")
              );
            },
          },
        ],
      },
    ],
    selModel: {
      checkOnly: false,
      injectCheckbox: "first",
      mode: "SIMPLE",
    },
  });
  gridOltsMultiplataforma.getStore().sort("nombreOlt", "ASC");
  formPanelMultiplataforma = Ext.create("Ext.form.Panel", {
    id: "formDatosOltMultiplataforma",
    bodyPadding: 2,
    waitMsgTarget: true,
    fieldDefaults: {
      labelAlign: "left",
      labelWidth: 200,
      msgTarget: "side",
    },
    items: [filterPanelOltMultiplataforma, gridOltsMultiplataforma],
    buttons: [
      {
        iconCls: "icon_add",
        text: "Nueva Solicitud",
        cls: "button-text-green x-btn-item-medium",
        hidden: !boolPermisoAdd,
        handler: function () {
          panelAddOltsMultiplataforma();
        },
      },
      {
        iconCls: "icon_cerrar",
        text: "Cerrar",
        cls: "x-btn-item-medium",
        handler: function () {
          winOltMultiplataforma.destroy();
        },
      },
    ],
  });
  winOltMultiplataforma = Ext.create("Ext.window.Window", {
    id: "winOltMultiplataforma",
    title: "OLTs Multiplataforma",
    modal: true,
    width: 1000,
    closable: false,
    layout: "fit",
    resizable: false,
    bodyStyle: {
      background: "#fff",
    },
    items: [formPanelMultiplataforma],
  }).show();
}
function panelAddOltsMultiplataforma() {
  storeCbxOltMulti = Ext.create("Ext.data.Store", {
    id: "storeCbxOltMulti",
    total: "total",
    autoLoad: false,
    proxy: {
      type: "ajax",
      actionMethods: "POST",
      timeout: 3000000,
      url: urlGetOltNodoMultiplataforma,
      reader: {
        type: "json",
        totalProperty: "total",
        root: "registros",
      },
      extraParams: {
        tipo: "OLT",
        estadoNot: "Eliminado",
      },
    },
    fields: [
      { name: "id", mapping: "id" },
      { name: "nombre", mapping: "nombre" },
    ],
  });
  storeCbxNodoMulti = Ext.create("Ext.data.Store", {
    id: "storeCbxNodoMulti",
    total: "total",
    autoLoad: false,
    proxy: {
      type: "ajax",
      actionMethods: "POST",
      timeout: 3000000,
      url: urlGetOltNodoMultiplataforma,
      reader: {
        type: "json",
        totalProperty: "total",
        root: "registros",
      },
      extraParams: {
        tipo: "NODO",
        strIdOlt: "",
        estado: "Activo",
      },
    },
    fields: [
      { name: "id", mapping: "id" },
      { name: "nombre", mapping: "nombre" },
      { name: "nombreAgregador", mapping: "nombreAgregador" },
    ],
  });
  addPanelOltMultiplataforma = Ext.create("Ext.panel.Panel", {
    id: "addPanelOltMultiplataforma",
    bodyPadding: 7,
    border: false,
    buttonAlign: "center",
    layout: {
      type: "table",
      columns: 1,
      align: "stretch",
    },
    bodyStyle: {
      background: "#fff",
    },
    width: "100%",
    height: "20%",
    buttons: [
      {
        text: "Guardar",
        iconCls: "iconSave",
        handler: function () {
          var strNombreOlt = Ext.getCmp("cbxOltMulti").getRawValue();
          var strIdOlt = Ext.getCmp("cbxOltMulti").value;
          var strIdNodo = Ext.getCmp("cbxNodoMulti").value;
          var strIpv6 = Ext.getCmp("inputIpv6Multi").value;
          guardarOltMultiplataforma(strNombreOlt, strIdOlt, strIdNodo, strIpv6);
        },
      },
      {
        text: "Cancelar",
        iconCls: "icon_cerrar",
        handler: function () {
          winAddOltMultiplataforma.destroy();
        },
      },
    ],
    items: [
      {
        xtype: "combobox",
        store: storeCbxOltMulti,
        labelAlign: "left",
        name: "cbxOltMulti",
        id: "cbxOltMulti",
        valueField: "id",
        displayField: "nombre",
        fieldLabel: "Nombre Olt",
        loadingText: "Buscando ...",
        width: "100%",
        emptyText: "Digite el Olt para la busqueda",
        allowBlank: false,
        matchFieldWidth: true,
        fieldStyle: {
          width: "90%!important",
        },
        queryMode: "remote",
        listeners: {
          select: function (combo, record, index) {
            storeCbxNodoMulti.getProxy().extraParams.strIdOlt =
              combo.getValue();
            storeCbxNodoMulti.load({
              callback: function () {
                if (Ext.getCmp("cbxNodoMulti").getStore().getCount() > 0) {
                  Ext.getCmp("cbxNodoMulti").select(
                    Ext.getCmp("cbxNodoMulti").getStore().getAt(0)
                  );
                } else {
                  Ext.Msg.show({
                    title: "Informaci\xf3n",
                    msg:
                      Ext.getCmp("cbxOltMulti").getRawValue() +
                      " está ubicado en un nodo que no es Multiplataforma. " +
                      "Debe escoger otro Olt o solicitar a Sistemas para el ingreso del nodo a Multiplataforma.",
                    buttons: Ext.Msg.OK,
                    icon: Ext.MessageBox.INFO,
                  });
                }
              },
            });
          },
          change: function (combo) {
            Ext.getCmp("cbxNodoMulti").setValue("");
            Ext.getCmp("cbxNodoMulti").setRawValue("");
            Ext.getCmp("inputAgregadorMulti").setValue("");
          },
          scope: this,
        },
      },
      {
        xtype: "combobox",
        store: storeCbxNodoMulti,
        labelAlign: "left",
        name: "cbxNodoMulti",
        id: "cbxNodoMulti",
        valueField: "id",
        displayField: "nombre",
        fieldLabel: "Nombre Nodo",
        loadingText: "Buscando ...",
        width: "100%",
        emptyText: "",
        allowBlank: false,
        matchFieldWidth: true,
        readOnly: true,
        fieldStyle: {
          width: "90%",
        },
        queryMode: "remote",
        listeners: {
          change: function (combo) {
            if (Ext.getCmp("cbxNodoMulti").getStore().getCount() > 0) {
              Ext.getCmp("inputAgregadorMulti").setValue(
                Ext.getCmp("cbxNodoMulti")
                  .getStore()
                  .getAt(0)
                  .get("nombreAgregador")
              );
            }
          },
          scope: this,
        },
      },
      {
        xtype: "textfield",
        id: "inputAgregadorMulti",
        fieldLabel: "Nombre Agregador",
        readOnly: true,
        value: "",
        emptyText: "",
        fieldStyle: {
          width: "100%",
        },
        width: "100%",
      },
      {
        xtype: "textfield",
        id: "inputIpv6Multi",
        fieldLabel: "Ipv6",
        value: "",
        emptyText: "Digite la Ipv6",
        fieldStyle: {
          width: "100%",
        },
        width: "100%",
      },
    ],
  });
  formPanelAddMultiplataforma = Ext.create("Ext.form.Panel", {
    id: "formPanelAddMultiplataforma",
    bodyPadding: 2,
    waitMsgTarget: true,
    items: [addPanelOltMultiplataforma],
  });
  winAddOltMultiplataforma = Ext.create("Ext.window.Window", {
    id: "winAddOltMultiplataforma",
    title: "Nueva Solicitud Olt Multiplataforma",
    modal: true,
    width: 500,
    closable: true,
    layout: "fit",
    resizable: false,
    bodyStyle: {
      background: "#fff",
    },
    items: [formPanelAddMultiplataforma],
  }).show();
}
function panelAsignarOltMultiplataforma(strIdSolicitud, strNombreOlt) {
  storePuertosPeMulti = Ext.create("Ext.data.Store", {
    id: "storePuertosPeMulti",
    total: "total",
    autoLoad: false,
    proxy: {
      type: "ajax",
      actionMethods: "POST",
      timeout: 3000000,
      url: urlGetPuertosPeMultiplataforma,
      reader: {
        type: "json",
        totalProperty: "total",
        root: "registros",
        messageProperty: "error",
      },
      extraParams: {
        strIdSolicitud: strIdSolicitud,
      },
    },
    fields: [
      { name: "id", mapping: "id" },
      { name: "name", mapping: "name" },
    ],
  });
  gridPuertosPeMulti = Ext.create("Ext.grid.Panel", {
    id: "gridPuertosPeMulti",
    width: "100%",
    height: 200,
    store: storePuertosPeMulti,
    loadMask: true,
    frame: false,
    iconCls: "icon-grid",
    columns: [
      {
        id: "interface",
        header: "interface",
        dataIndex: "id",
        hidden: true,
        hideable: false,
      },
      {
        header: "Interfaces",
        dataIndex: "name",
        width: "92%",
        sortable: true,
      },
    ],
    selModel: {
      checkOnly: false,
      injectCheckbox: "first",
      mode: "SIMPLE",
    },
    selType: "checkboxmodel",
  });
  formPanelAsignarMultiplataforma = Ext.create("Ext.form.Panel", {
    id: "formPanelAsignarMultiplataforma",
    bodyPadding: 2,
    waitMsgTarget: true,
    items: [gridPuertosPeMulti],
    buttons: [
      {
        text: "Asignar Recursos",
        iconCls: "icon_informacionTecnica",
        handler: function () {
          var xRowSelMod = Ext.getCmp("gridPuertosPeMulti")
            .getSelectionModel()
            .getSelection();
          if (xRowSelMod.length > 0) {
            var arrayInterfaces = [];
            xRowSelMod.map((item) => {
              arrayInterfaces.push(item.get("id"));
            });
            asignarRecursosOltMultiplataforma(
              strIdSolicitud,
              strNombreOlt,
              arrayInterfaces
            );
          } else {
            Ext.Msg.show({
              title: "Informaci\xf3n",
              msg: "No ha seleccionado ninguna interface.",
              buttons: Ext.Msg.OK,
              icon: Ext.MessageBox.INFO,
            });
          }
        },
      },
      {
        text: "Cancelar",
        iconCls: "icon_cerrar",
        handler: function () {
          winAsignarOltMultiplataforma.destroy();
        },
      },
    ],
  });
  winAsignarOltMultiplataforma = Ext.create("Ext.window.Window", {
    id: "winAsignarOltMultiplataforma",
    title: "Asignar Recursos Red Olt Multiplataforma",
    modal: true,
    width: 400,
    closable: true,
    layout: "fit",
    resizable: false,
    bodyStyle: {
      background: "#fff",
    },
    items: [formPanelAsignarMultiplataforma],
  }).show();
  storePuertosPeMulti.load({
    callback: function (records, operation, success) {
      if (typeof operation.resultSet.message !== "undefined") {
        var message = operation.resultSet.message;
        if (message.length > 0) {
          Ext.Msg.show({
            title: "Error",
            msg: message,
            buttons: Ext.Msg.OK,
            icon: Ext.MessageBox.ERROR,
          });
        }
      }
    },
  });
}
function buscarOltMultiplataforma() {
  storeOltMultiplataforma.getProxy().extraParams = {};
  storeOltMultiplataforma.getProxy().extraParams.strNombreOlt =
    Ext.getCmp("txtNombreOltMulti").value;
  storeOltMultiplataforma.getProxy().extraParams.strNombreNodo =
    Ext.getCmp("txtNombreNodoMulti").value;
  storeOltMultiplataforma.getProxy().extraParams.strNombreAgregador =
    Ext.getCmp("txtNombreAgregadorMulti").value;
  storeOltMultiplataforma.getProxy().extraParams.strNombrePe =
    Ext.getCmp("txtNombrePeMulti").value;
  storeOltMultiplataforma.getProxy().extraParams.strIpv6 =
    Ext.getCmp("txtIpv6Multi").value;
  storeOltMultiplataforma.getProxy().timeout = 300000;
  storeOltMultiplataforma.load();
}
function limpiarOltMultiplataforma() {
  Ext.getCmp("txtNombreOltMulti").setValue("");
  Ext.getCmp("txtNombreNodoMulti").setValue("");
  Ext.getCmp("txtNombrePeMulti").setValue("");
  Ext.getCmp("txtIpv6Multi").setValue("");
  Ext.getCmp("txtNombreAgregadorMulti").setValue("");
  storeOltMultiplataforma.getProxy().extraParams = {};
  storeOltMultiplataforma.getProxy().timeout = 300000;
  storeOltMultiplataforma.load();
}
function guardarOltMultiplataforma(strNombreOlt, strIdOlt, strIdNodo, strIpv6) {
  if (
    strIdOlt !== null &&
    strIdOlt.length > 0 &&
    strIdNodo !== null &&
    strIdNodo.length > 0 &&
    strIpv6.length > 0
  ) {
    Ext.Msg.confirm(
      "Alerta",
      "Está seguro de ingresar la solicitud olt multiplataforma para el " +
        strNombreOlt +
        "?",
      function (btn) {
        if (btn == "yes") {
          connGrabandoDatosGpon.request({
            url: urlAsignarOltMultiplataforma,
            method: "post",
            timeout: 3000000,
            params: {
              strIdOlt: strIdOlt,
              strIdNodo: strIdNodo,
              strIpv6: strIpv6,
            },
            success: function (response) {
              var result = Ext.decode(response.responseText);
              if (result.status == "OK") {
                Ext.Msg.show({
                  title: "Informaci\xf3n",
                  msg: result.mensaje,
                  buttons: Ext.Msg.OK,
                  icon: Ext.MessageBox.INFO,
                });
                limpiarOltMultiplataforma();
              } else {
                Ext.Msg.show({
                  title: "Error",
                  msg: result.mensaje,
                  buttons: Ext.Msg.OK,
                  icon: Ext.MessageBox.ERROR,
                });
              }
              winAddOltMultiplataforma.destroy();
            },
            failure: function (response) {
              Ext.Msg.show({
                title: "Error",
                msg: "Error: " + response.statusText,
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR,
              });
              winAddOltMultiplataforma.destroy();
            },
          });
        } else {
          winAddOltMultiplataforma.destroy();
        }
      }
    );
  } else if (strIdOlt === null || strIdOlt.length < 1) {
    Ext.Msg.show({
      title: "Informaci\xf3n",
      msg: "No se ha seleccionado el Olt.",
      buttons: Ext.Msg.OK,
      icon: Ext.MessageBox.INFO,
    });
  } else if (strIdNodo === null || strIdNodo.length < 1) {
    Ext.Msg.show({
      title: "Informaci\xf3n",
      msg: "No se ha seleccionado el Nodo.",
      buttons: Ext.Msg.OK,
      icon: Ext.MessageBox.INFO,
    });
  } else if (strIpv6.length < 1) {
    Ext.Msg.show({
      title: "Informaci\xf3n",
      msg: "No se ha ingresado la Ipv6.",
      buttons: Ext.Msg.OK,
      icon: Ext.MessageBox.INFO,
    });
  }
}
function asignarRecursosOltMultiplataforma(
  strIdSolicitud,
  strNombreOlt,
  arrayInterfaces
) {
  if (arrayInterfaces.length > 0) {
    Ext.Msg.confirm(
      "Alerta",
      "Está seguro de asignar los recursos de red al " +
        strNombreOlt +
        " para multiplataforma?",
      function (btn) {
        if (btn == "yes") {
          connGrabandoDatosGpon.request({
            url: urlRecursosOltMultiplataforma,
            method: "post",
            timeout: 3000000,
            params: {
              strIdSolicitud: strIdSolicitud,
              arrayInterfaces: Ext.encode(arrayInterfaces),
            },
            success: function (response) {
              var result = Ext.decode(response.responseText);
              if (result.status == "OK") {
                Ext.Msg.show({
                  title: "Informaci\xf3n",
                  msg: result.mensaje,
                  buttons: Ext.Msg.OK,
                  icon: Ext.MessageBox.INFO,
                });
                limpiarOltMultiplataforma();
              } else {
                Ext.Msg.show({
                  title: "Error",
                  msg: result.mensaje,
                  buttons: Ext.Msg.OK,
                  icon: Ext.MessageBox.ERROR,
                });
              }
              winAsignarOltMultiplataforma.destroy();
            },
            failure: function (response) {
              Ext.Msg.show({
                title: "Error",
                msg: "Error: " + response.statusText,
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR,
              });
              winAsignarOltMultiplataforma.destroy();
            },
          });
        } else {
          winAsignarOltMultiplataforma.destroy();
        }
      }
    );
  } else if (arrayInterfaces.length < 1) {
    Ext.Msg.show({
      title: "Informaci\xf3n",
      msg: "No ha seleccionado ninguna interface.",
      buttons: Ext.Msg.OK,
      icon: Ext.MessageBox.INFO,
    });
  }
}
function configurarOltMultiplataforma(strIdSolicitud, strNombreOlt) {
  Ext.Msg.confirm(
    "Alerta",
    "Está seguro de configurar el " + strNombreOlt + " para multiplataforma?",
    function (btn) {
      if (btn == "yes") {
        connGrabandoDatosGpon.request({
          url: urlConfigurarOltMultiplataforma,
          method: "post",
          timeout: 3000000,
          params: {
            strIdSolicitud: strIdSolicitud,
          },
          success: function (response) {
            var result = Ext.decode(response.responseText);
            if (result.status == "OK") {
              Ext.Msg.show({
                title: "Informaci\xf3n",
                msg: result.mensaje,
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.INFO,
              });
              limpiarOltMultiplataforma();
            } else {
              Ext.Msg.show({
                title: "Error",
                msg: result.mensaje,
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR,
              });
            }
          },
          failure: function (response) {
            Ext.Msg.show({
              title: "Error",
              msg: "Error: " + response.statusText,
              buttons: Ext.Msg.OK,
              icon: Ext.MessageBox.ERROR,
            });
          },
        });
      }
    }
  );
}
function reversarOltMultiplataforma(strIdSolicitud, strNombreOlt) {
  Ext.Msg.confirm(
    "Alerta",
    "Está seguro de anular la solicitud de Olt Multiplataforma para el " +
      strNombreOlt +
      "?",
    function (btn) {
      if (btn == "yes") {
        connGrabandoDatosGpon.request({
          url: urlReversarOltMultiplataforma,
          method: "post",
          timeout: 3000000,
          params: {
            strIdSolicitud: strIdSolicitud,
          },
          success: function (response) {
            var result = Ext.decode(response.responseText);
            if (result.status == "OK") {
              Ext.Msg.show({
                title: "Informaci\xf3n",
                msg: result.mensaje,
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.INFO,
              });
              limpiarOltMultiplataforma();
            } else {
              Ext.Msg.show({
                title: "Error",
                msg: result.mensaje,
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR,
              });
            }
          },
          failure: function (response) {
            Ext.Msg.show({
              title: "Error",
              msg: "Error: " + response.statusText,
              buttons: Ext.Msg.OK,
              icon: Ext.MessageBox.ERROR,
            });
          },
        });
      }
    }
  );
}
function viewDataOltMultiplataforma(strIdSolicitud) {
  storeDatosOltMultiplataforma = Ext.create("Ext.data.Store", {
    id: "storeDatosOltMultiplataforma",
    model: "ListModelDatosOltMultiplataforma",
    autoLoad: true,
    pageSize: 100,
    total: "total",
    proxy: {
      type: "ajax",
      url: urlGetDatosOltMultiplataforma,
      actionMethods: "POST",
      timeout: 3000000,
      reader: {
        type: "json",
        totalProperty: "total",
        root: "registros",
      },
      extraParams: {
        strIdSolicitud: strIdSolicitud,
      },
    },
    fields: [{ name: "historial", mapping: "historial" }],
  });
  gridDatosOltsMultiplataforma = Ext.create("Ext.grid.Panel", {
    id: "gridDatosOltsMultiplataforma",
    width: "100%",
    height: 300,
    store: storeDatosOltMultiplataforma,
    loadMask: true,
    frame: false,
    autoScroll: true,
    iconCls: "icon-grid",
    columns: [
      {
        header: "Historial",
        dataIndex: "historial",
        width: "100%",
        sortable: true,
      },
    ],
    selModel: {
      checkOnly: false,
      injectCheckbox: "first",
      mode: "SIMPLE",
    },
  });
  formPanelViewMultiplataforma = Ext.create("Ext.form.Panel", {
    id: "formPanelViewMultiplataforma",
    bodyPadding: 2,
    waitMsgTarget: true,
    buttons: [
      {
        text: "Cerrar",
        iconCls: "icon_cerrar",
        handler: function () {
          winViewOltMultiplataforma.destroy();
        },
      },
    ],
    items: [gridDatosOltsMultiplataforma],
  });
  winViewOltMultiplataforma = Ext.create("Ext.window.Window", {
    id: "winViewOltMultiplataforma",
    title: "Ver Recursos Olt Multiplataforma",
    modal: true,
    width: 400,
    closable: false,
    layout: "fit",
    resizable: false,
    bodyStyle: {
      background: "#fff",
    },
    items: [formPanelViewMultiplataforma],
  }).show();
}
var connGrabandoDatosGpon = new Ext.data.Connection({
  listeners: {
    beforerequest: {
      fn: function (con, opt) {
        Ext.MessageBox.show({
          msg: "Grabando los datos, Por favor espere!!",
          progressText: "Saving...",
          width: 300,
          wait: true,
          waitConfig: { interval: 200 },
        });
      },
      scope: this,
    },
  },
});

function abrirModalMacDesconfigurarIp() {
  storeMacEliminarIp = Ext.create("Ext.data.Store", {
    id: "storeMacEliminarIp",
    model: "ListModelMacEliminarIp",
    autoLoad: false,
    pageSize: 5000,
    total: "total",
    proxy: {
      type: "ajax",
      url: urlBuscarMacEliminarIp,
      actionMethods: "POST",
      timeout: 3000000,
      reader: {
        type: "json",
        totalProperty: "total",
        root: "registros",
      },
    },
    fields: [
      { name: "idIp", mapping: "idIp" },
      { name: "nombreCliente", mapping: "nombreCliente" },
      { name: "identificacionCliente", mapping: "identificacionCliente" },
      { name: "servicioId", mapping: "servicioId" },
      { name: "estadoServicio", mapping: "estadoServicio" },
      { name: "estadosServicios", mapping: "estadosServicios" },
      { name: "macOnt", mapping: "macOnt" },
      { name: "login", mapping: "login" },
      { name: "nombreOnt", mapping: "nombreOnt" },
      { name: "ipOnt", mapping: "ipOnt" },
      { name: "estadoIp", mapping: "estadoIp" },
      { name: "serialOnt", mapping: "serialOnt" },
      { name: "scope", mapping: "scope" },
      { name: "permiso", mapping: "permiso" },
      { name: "estadoSPC", mapping: "estadoSPC" },
    ],
  });
  filterPanelMacEliminarIp = Ext.create("Ext.panel.Panel", {
    id: "filterPanelMacEliminarIp",
    bodyPadding: 7,
    border: false,
    buttonAlign: "center",
    layout: {
      type: "hbox",
      align: "middle",
      pack: "center",
    },
    bodyStyle: {
      background: "#fff",
      textAlign: "center",
    },
    collapsible: false,
    collapsed: false,
    width: "100%",
    height: "100%",
    title: "Criterios de busqueda",
    items: [
      {
        xtype: "label",
        text: "MAC ONT:",
        width: "auto",
        margin: "0 10px",
      },
      {
        xtype: "textfield",
        id: "txtMAc1",
        value: "",
        fieldStyle: "text-align: center; font-weight: bold;",
        width: "50px",
        enforceMaxLength: "true",
        maxLength: 4,
      },
      {
        xtype: "textfield",
        id: "txtMAc2",
        value: "",
        width: "50px",
        fieldStyle: "text-align: center; font-weight: bold;",
        margin: "0 10px",
        enforceMaxLength: "true",
        maxLength: 4,
      },
      {
        xtype: "textfield",
        id: "txtMAc3",
        value: "",
        fieldStyle: "text-align: center; font-weight: bold;",
        width: "50px",
        enforceMaxLength: "true",
        maxLength: 4,
      },
      {
        xtype: "button",
        text: "Buscar",
        iconCls: "icon_search",
        margin: "0 10px",
        handler: function () {
          buscarMacEliminarIp();
        },
      },
      {
        xtype: "button",
        text: "Limpiar",
        iconCls: "icon_limpiar",
        handler: function () {
          limpiarMacEliminarIp();
        },
      },
    ],
  });
  gridMacEliminarIp = Ext.create("Ext.grid.Panel", {
    id: "gridMacEliminarIp",
    title: "Lista de IP",
    width: "100%",
    height: 250,
    store: storeMacEliminarIp,
    loadMask: true,
    frame: false,
    autoScroll: true,

    columns: [
      {
        id: "idIp",
        header: "idIp",
        dataIndex: "idIp",
        hidden: true,
        hideable: false,
      },
      {
        id: "nombreCliente",
        header: "nombreCliente",
        dataIndex: "nombreCliente",
        hidden: true,
        hideable: false,
      },
      {
        id: "identificacionCliente",
        header: "identificacionCliente",
        dataIndex: "identificacionCliente",
        hidden: true,
        hideable: false,
      },
      {
        id: "servicioId",
        header: "servicioId",
        dataIndex: "servicioId",
        hidden: true,
        hideable: false,
      },
      {
        id: "estadosServicios",
        header: "estadosServicios",
        dataIndex: "estadosServicios",
        hidden: true,
        hideable: false,
      },
      {
        id: "scope",
        header: "scope",
        dataIndex: "scope",
        hidden: true,
        hideable: false,
      },
      
      {
        id: "macOnt",
        header: "macOnt",
        dataIndex: "macOnt",
        hidden: true,
        hideable: false,
      },
      {
        id: "permiso",
        header: "permiso",
        dataIndex: "permiso",
        hidden: true,
        hideable: false,
      },
      {
        header: "Ip",
        dataIndex: "ipOnt",
        width: "21%",
      },
      {
        header: "Login",
        dataIndex: "login",
        width: "23%",
      },
      {
        header: "Estado Servicio",
        dataIndex: "estadoServicio",
        width: "15%",
      },
      {
        header: "Estado SPC",
        dataIndex: "estadoSPC",
        width: "15%",
      },
      {
        header: "Estado IP",
        dataIndex: "estadoIp",
        width: "15%",
        sortable: true,
        renderer : function(value, meta) {
          if(value !== "Activo") {
              meta.style = "color:red;";
          }
          return value;
      }
      },
      {
        id: "serialOnt",
        header: "serialOnt",
        dataIndex: "serialOnt",
        hidden: true,
        hideable: false,
      },
      {
        xtype: "actioncolumn",
        header: "Acciones",
        width: "10%",
        items: [
          {
            tooltip: "Procesar",
            getClass: function (v, meta, rec) {
              strClass = "button-grid-invisible";
              var boolPermiso = rec.get("permiso");
              let estados = rec.get("estadosServicios");
              if (boolPermiso && estados.find(a=>a === rec.get("estadoServicio")) !== undefined) {
                strClass = "button-grid-Tuerca-2";
              }
              return strClass;
            },
            handler: function (grid, rowIndex) {
              var rec = grid.getStore().getAt(rowIndex);
              macEliminarIp(rec);
            },
          },
        ],
      },
    ],
    selModel: {
      checkOnly: false,
      injectCheckbox: "first",
      mode: "SIMPLE",
    },
  });
  formPanelMacEliminarIp = Ext.create("Ext.form.Panel", {
    id: "formDatosOltMultiplataforma",
    bodyPadding: 2,
    waitMsgTarget: true,
    fieldDefaults: {
      labelAlign: "left",
      labelWidth: 200,
      msgTarget: "side",
    },
    items: [filterPanelMacEliminarIp, gridMacEliminarIp],
    buttons: [
      {
        iconCls: "icon_cerrar",
        text: "Cerrar",
        cls: "x-btn-item-medium",
        handler: function () {
          winMacEliminarIp.destroy();
        },
      },
    ],
  });
  Ext.getCmp("txtMAc1").focus(false, 200);
  winMacEliminarIp = Ext.create("Ext.window.Window", {
    id: "winMacEliminarIp",
    title: "Desconfigurar IP CNR",
    modal: true,
    width: 700,
    closable: false,
    layout: "fit",
    resizable: false,
    bodyStyle: {
      background: "#fff",
    },
    items: [formPanelMacEliminarIp],
  }).show();
}

function buscarMacEliminarIp() {
  storeMacEliminarIp.getProxy().extraParams = {};
  let strMac1 = Ext.getCmp("txtMAc1").value,
    strMac2 = Ext.getCmp("txtMAc2").value,
    strMac3 = Ext.getCmp("txtMAc3").value;

  if(strMac1.length === 0 || strMac2.length === 0 || strMac3.length === 0) {
    Ext.Msg.show({
      title: "Error",
      msg: "Debes llenar todos los campos",
      buttons: Ext.Msg.OK,
      icon: Ext.MessageBox.ERROR,
    });
    return false;
  }

  storeMacEliminarIp.getProxy().extraParams.strMac = `${strMac1}.${strMac2}.${strMac3}`;
  storeMacEliminarIp.getProxy().timeout = 300000;
  storeMacEliminarIp.load();
}

function limpiarMacEliminarIp() {
  Ext.getCmp("txtMAc1").setValue("");
  Ext.getCmp("txtMAc2").setValue("");
  Ext.getCmp("txtMAc3").setValue("");
  storeMacEliminarIp.getProxy().extraParams = {};
  storeMacEliminarIp.getProxy().timeout = 300000;
  storeMacEliminarIp.clearData();
  storeMacEliminarIp.removeAll();
  Ext.getCmp("txtMAc1").focus(false, 200);
}

function macEliminarIp(objData) {
  let strIpOnt                = objData.get("ipOnt"),
    strNombreCliente          = objData.get("nombreCliente"),
    strIdentificacionCliente  = objData.get("identificacionCliente"),
    strLogin                  = objData.get("login"),
    strMac                    = objData.get("macOnt"),
    strSerialOnt              = objData.get("serialOnt"),
    strEstadoServicio         = objData.get("estadoServicio"),
    strScope                  = objData.get("scope"),
    intIdIp                   = objData.get("idIp"),
    intServicioId             = objData.get("servicioId");
  let mensaje = ` <p><b>CLIENTE:      </b><span>${strNombreCliente}</span></p>
                  <p><b>IDENFICACIÓN: </b><span>${strIdentificacionCliente}</span></p>
                  <p><b>LOGIN:        </b><span>${strLogin}</span></p>
                  <p><b>MAC ONT:      </b><span>${strMac}</span></p>
                  <p><b>SCOPE:        </b><span>${strScope}</span></p>
                  <br />
                  ¿Está seguro de desconfigurar la ip <b style='color:red;'>${strIpOnt}</b>?`;

  Ext.Msg.confirm(
    "Alerta",
    mensaje,
    function (btn) {
      if (btn == "yes") {
        let parametros = {
          nombre_cliente: strNombreCliente,
          login: strLogin,
          identificacion: strIdentificacionCliente,
          serial_ont: strSerialOnt,
          mac_ont: strMac,
          ip_ont: strIpOnt,
          estado_servicio: strEstadoServicio,
          escope: strScope,
          idIp: intIdIp,
          servicioId: intServicioId
        };
        connGrabandoDatosGpon.request({
          url: urlMacEliminarIp,
          method: "post",
          timeout: 3000000,
          params: parametros,
          success: function (response) {
            var result = Ext.decode(response.responseText);
            if (result.status == "OK") {
              Ext.Msg.show({
                title: "Informaci\xf3n",
                msg: result.mensaje,
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.INFO,
              });
              buscarMacEliminarIp();
            } else {
              Ext.Msg.show({
                title: "Error",
                msg: result.mensaje,
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR,
              });
            }
          },
          failure: function (response) {
            Ext.Msg.show({
              title: "Error",
              msg: "Error: " + response.statusText,
              buttons: Ext.Msg.OK,
              icon: Ext.MessageBox.ERROR,
            });
          },
        });
      }
    }
  );
}