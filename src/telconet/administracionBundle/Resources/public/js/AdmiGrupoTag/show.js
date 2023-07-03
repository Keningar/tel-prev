Ext.onReady(function() { 
    Ext.tip.QuickTipManager.init();
//Model Blanck para el store de Tags  ley2
    Ext.define('modeloGrupoTags', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'descripcion', mappgin: 'descripcion'},
            {name: 'observacion', mappgin: 'observacion'},
            {name: 'fe_creacion', mappgin: 'fe_creacion'},
            {name: 'usr_creacion', mappgin: 'usr_creacion'}
        ]
    });

    //Store Blanck para el store de Tags ley 3
    storeTagsBlanck = Ext.create('Ext.data.Store', {
        autoDestroy: true,
        autoLoad: true,
        model: 'modeloGrupoTags',
        proxy: {
            type: 'ajax',
            url: urlGetGrupoTag,
            reader: {
                type: 'json',
            },
            extraParams:
            {
                tipoScope: tipoScope
            }
        }
    });

    //grid
    gridTags = Ext.create("Ext.grid.Panel", {
        id: "gridTags",
        store: storeTagsBlanck,
        columnLines: true,
        columns: [
          {
            id: "descripcion",
            header: "Descripción",
            dataIndex: "descripcion",            
            width: 130,
            hidden: false,
            hideable: false
          },{
            id: "observacion",
            header: "Observación",
            dataIndex: "observacion",
            width: 200,
            hidden: false,
            hideable: false
          },{
            id: "fe_creacion",
            header: "Fecha Creación",
            dataIndex: "fe_creacion",
            width: 130,
            hidden: false,
            hideable: false
          },{
            id: "usr_creacion",
            header: "Usuario Creación",
            dataIndex: "usr_creacion",
            width: 130,
            hidden: false,
            hideable: false
          }
        ],
        width: 603,
        height: 300,
        frame: true,
        title: "Tags Asociados",
        renderTo: "grupoTags"
      });
});