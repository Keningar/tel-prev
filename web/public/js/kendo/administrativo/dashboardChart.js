               var internetUsers = [ {
                        "country": "Paises",
                        "year": "2005",
                        "eeuu": 67.96,
						"ecuador": 59,
						"uruguay": 70,
						"todos": 196.96
                    },{
                        "country": "Paises",
                        "year": "2006",
                        "eeuu": 69.96,
						"ecuador": 60,
						"uruguay": 40,
						"todos": 169.96
                    },{
                        "country": "Paises",
                        "year": "2007",
                        "eeuu": 70,
						"ecuador": 64,
						"uruguay": 80,
						"todos": 214
                    },{
                        "country": "Paises",
                        "year": "2008",
                        "eeuu": 110,
						"ecuador": 91,
						"uruguay": 87,
						"todos": 196.96
                    } ];
				var times =[ {
			    "family": "Pentium",
			    "model": "D 820",
			    "price": 105,
			    "performance": 100
				}, {
			    "family": "Pentium",
			    "model": "D 915",
			    "price": 120,
			    "performance": 102
				}, {
			    "family": "Pentium",
			    "model": "D 945",
			    "price": 160,
			    "performance": 118
				}, {
			    "family": "Pentium",
			    "model": "XE 965",
			    "price": 1000,
			    "performance": 137
				}, {
			    "family": "Core 2 Duo",
			    "model": "E6300",
			    "price": 185,
			    "performance": 134
				}, {
			    "family": "Core 2 Duo",
			    "model": "E6400",
			    "price": 210,
			    "performance": 143
				}, {
			    "family": "Core 2 Duo",
			    "model": "E6600",
			    "price": 305,
			    "performance": 163
				}, {
			    "family": "Core 2 Duo",
			    "model": "E6700",
			    "price": 530,
			    "performance": 177
				}];
                function createChart1(div) {
                    $(div).kendoChart({
                        theme: $(document).data("kendoSkin") || "default",
                        dataSource: {
                            data: internetUsers
                        },
                        title: {
                            text: "Internet Users"
                        },
                        legend: {
                            position: "bottom"
                        },
                        seriesDefaults: {
                            type: "bar",
                            labels: {
                                visible: true,
                                format: "{0}%"
                            }
                        },
                        series: [
						{
                            field: "eeuu",
                            name: "United States"
                        },
						{
                            field: "ecuador",
                            name: "Ecuador"
                        },
						{
                            field: "uruguay",
                            name: "Uruguay"
                        }],
                        valueAxis: {
                            labels: {
                                format: "{0}%"
                            }
                        },
                        categoryAxis: {
                            field: "year"
                        }
                    });
                }
                function createChart2(div) {
                    $(div).kendoChart({
                        theme: $(document).data("kendoSkin") || "default",
                        dataSource: {
                            data: internetUsers
                        },
                        title: {
                            text: "Usuarios de Internet"
                        },
                        legend: {
                            position: "bottom"
                        },
                        seriesDefaults: {
                            type: "pie",
                            labels: {
                                visible: true,
                                format: "{0}%"
                            }
                        },
                        series: [{
                            field: "todos",
                            categoryField: "year"
                        }],
                        valueAxis: {
                            labels: {
                                format: "{0}%"
                            }
                        },
                        categoryAxis: {
                            field: "year"
                        }
                    });
                }
                function createChart3(div) {
                    $("#chart3").kendoChart({
                        theme: $(document).data("kendoSkin") || "default",
                        title: {
                            text: "Charge current vs. charge time"
                        },
                        dataSource: {
                            /*transport: {
                                read: {
                                    url: content/dataviz/js/price-performance.json",
                                    dataType: "json"
                                }
                            },*/
							data: times,	
                            sort: {
                                field: "year",
                                dir: "asc"
                            }
                        },
                        legend: {
                            visible: false
                        },
                        seriesDefaults: {
                            type: "scatterLine"
                        },
                        series: [{
                            xField: "price",
                            yField: "performance"
                        }],
                        xAxis: {
                            max: 1000,
                            labels: {
                                format: "${0}"
                            },
                            title: {
                                text: "Performance Ratio"
                            }
                        },
                        yAxis: {
                            min: 80,
                            labels: {
                                format: "{0}%"
                            },
                            title: {
                                text: "Price"
                            }
                        },
                        tooltip: {
                            visible: true,
                            template: "#= '<b>$' + value.x + ' / ' + dataItem.family + ' ' + dataItem.model + ': ' + value.y + '%</b>' #"
                        }
                    });

                }				
                $(document).ready(function() {
                    setTimeout(function() {
                        // Initialize the chart with a delay to make sure
                        // the initial animation is visible
                        createChart1("#chart1");
						createChart2("#chart2");
						createChart3("#chart3");

                        $("#example").bind("kendo:skinChange", function(e) {
                            createChart1("#chart1");
							createChart2("#chart2");
							createChart3("#chart3");
                        });
                    }, 400);
                });
