function Cliente() {
    this.AJAX = 'ajax';
    this.JSON = 'json';
    this.booleanAutoLoadStore = true;    
    this.intTimeOut           = 99999;    
           
    this.objStoreOficinas = function(objScope) {
        this.modelOficina = Ext.define('modelOficinasByEmpresa', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'intIdObj', type: 'int'},
                {name: 'strDescripcionObj', type: 'string'}
            ]
        });

        return new Ext.create('Ext.data.Store', {
            id: objScope.id,
            model: this.modelOficina,
            autoLoad: this.booleanAutoLoadStore,
            proxy: {
                type: this.AJAX,
                url: urlGetOficinasByEmpresa,
                timeout: this.intTimeOut,
                reader: {
                    type: this.JSON,
                    root: 'registros'
                },
                extraParams: objScope.extraParams,
                simpleSortMode: true
            }
        });
    };
    
    this.objStorePlanes = function(objScope) {
        this.modelPlan = Ext.define('modelPlanesByEmpresa', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'intIdObj', type: 'int'},
                {name: 'strDescripcionObj', type: 'string'}
            ]
        });

        return new Ext.create('Ext.data.Store', {
            id: objScope.id,
            model: this.modelPlan,
            autoLoad: this.booleanAutoLoadStore,
            proxy: {
                type: this.AJAX,
                url: urlGetPlanesByEmpresa,
                timeout: this.intTimeOut,
                reader: {
                    type: this.JSON,
                    root: 'registros'
                },
                extraParams: objScope.extraParams,
                simpleSortMode: true
            }
        });
    };    
    
    this.objComboMultiSelectDatos = function(objScope,strLabel) {
        Ext.define('cboSelectedCountDatos', {
            alias: 'plugin.selectedDatos',
            init: function(cboSelectedCountDatos) {
                cboSelectedCountDatos.on({
                    select: function(me, objRecords) {
                        intNumeroRegistros = objRecords.length;
                        storeCboSelectedCountDatos = cboSelectedCountDatos.getStore();
                        boolDiffRowCbo = objRecords.length !== storeCboSelectedCountDatos.count;
                        boolNewAll = false;
                        boolSelectedAll = false;
                        objNewRecords = [];
                        Ext.each(objRecords, function(obj, i, objRecordsItself) {
                            if (objRecords[i].data.intIdObj === 0) {
                                boolSelectedAll = true;
                                if (!cboSelectedCountDatos.boolCboSelectedAll) {
                                    intNumeroRegistros = storeCboSelectedCountDatos.getCount();
                                    cboSelectedCountDatos.select(storeCboSelectedCountDatos.getRange());
                                    cboSelectedCountDatos.boolCboSelectedAll = true;
                                    boolNewAll = true;
                                }
                            } else {
                                if (boolDiffRowCbo && !boolNewAll)
                                    objNewRecords.push(objRecords[i]);
                            }
                        });
                        if (cboSelectedCountDatos.boolCboSelectedAll && !boolSelectedAll) {
                            cboSelectedCountDatos.clearValue();
                            cboSelectedCountDatos.boolCboSelectedAll = false;
                        } else if (boolDiffRowCbo && !boolNewAll) {
                            cboSelectedCountDatos.select(objNewRecords);
                            cboSelectedCountDatos.boolCboSelectedAll = false;
                        }
                    }
                });
            }
        });

        return new Ext.create('Ext.form.ComboBox', {
            disabled: false,
            multiSelect: true,
            plugins: ['selectedDatos'],
            id: objScope.strIdObj,
            fieldLabel: strLabel,
            store: objScope.objStore,
            queryMode: 'local',
            editable: false,
            displayField: 'strDescripcionObj',
            valueField: 'intIdObj',
            width: objScope.intWidth,
            displayTpl: '<tpl for="."> { strDescripcionObj } <tpl if="xindex < xcount">, </tpl> </tpl>',
            listConfig: {
                itemTpl: '{strDescripcionObj} <div class="uncheckedChkbox"></div>'
            }
        });
    };    

}