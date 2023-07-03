/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function getAnios(){
    return new Ext.data.Store({
        fields: ['id','descripcion'],
        data: [{'id':2018, 'descripcion': '2018'},
               {'id':2019, 'descripcion': '2019'},
               {'id':2020, 'descripcion': '2020'},
               {'id':2021, 'descripcion': '2021'},
               {'id':2022, 'descripcion': '2022'},
               {'id':2023, 'descripcion': '2023'},
               {'id':2024, 'descripcion': '2024'},
               {'id':2025, 'descripcion': '2025'}
              ]
    });
}
    
