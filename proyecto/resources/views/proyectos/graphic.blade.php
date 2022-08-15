@extends('layouts.modal')
@section('content')
<style>
    #chartdiv {
        width: 100%;
        height: 350px;
    }
</style>
<script src="https://cdn.amcharts.com/lib/4/core.js"></script>
<script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
<script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>
<div class="row p-10 m-10 bg-white rounded">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <h6 class="font-size-11 text-uppercase mb-4 uppercase">{{$proyecto->nombre}} cuadro de actividades</h6>
                <div class="text-right">
                    <button class="btn btn-success btn-sm mt-2 mr-2 b btn-tar" type="button">Tareas</button><button
                        class="btn btn-info btn-sm mt-2 btn-sub" type="button">Subtareas</button>
                </div>
            </div>
            <div id="chartdiv" class="pl-4"></div>
        </div>
</div>
<script type="text/javascript">
    var array_task = @json($pro, JSON_PRETTY_PRINT);

      am4core.useTheme(am4themes_animated);      
      var chart = am4core.create("chartdiv", am4charts.XYChart);
      chart.hiddenState.properties.opacity = 0; 
      
      chart.paddingRight = 150;
      chart.dateFormatter.inputDateFormat = "yyyy-MM-dd";
      
      var colorSet = new am4core.ColorSet();
      colorSet.saturation = 0.4;
      let colores = [];

        for (var i = 0; i < 30; i++) {
            for (var j = 0; j < 1; j=j+0.2) {
                colores[i] = colorSet.getIndex(i).brighten(j);
                console.log("i: " + i + " j: " + j);
            }
        }
        colores.sort(function() { return Math.random() - 0.5 });

        for (var i = 0; i < array_task.length; i++) {
            if(array_task[i]['color'] == 'subtarea'){
                colores[i]._value.a = '0.6';
                array_task[i]['color'] = colores[i];
            }else{
                array_task[i]['color'] = colores[i];
            }            
        }
      
      chart.data = array_task;
      
      var categoryAxis = chart.yAxes.push(new am4charts.CategoryAxis());
      categoryAxis.dataFields.category = "name";
      categoryAxis.renderer.grid.template.location = 0;
      categoryAxis.renderer.inversed = true;
      
      var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
      dateAxis.dateFormatter.dateFormat = "yyyy-MM-dd";
      dateAxis.renderer.minGridDistance = 30;
      dateAxis.renderer.grid.template.location = 0;
      dateAxis.baseInterval = { count: 15, timeUnit: "minute" };
      var today = new Date();
      var year = today.getFullYear()+1;
      dateAxis.max = new Date(year, 0, 1, 1, 0, 0, 0).getTime();
      dateAxis.strictMinMax = true;
      dateAxis.renderer.tooltipLocation = 0;
      
      var series1 = chart.series.push(new am4charts.ColumnSeries());
      series1.columns.template.width = am4core.percent(100);
      series1.columns.template.tooltipText = "{client} \n {fromDate}-{toDate}";
      
      series1.dataFields.openDateX = "fromDate";
      series1.dataFields.dateX = "toDate";
      series1.dataFields.categoryY = "name";
      series1.columns.template.propertyFields.fill = "color";
      series1.columns.template.strokeOpacity = 0;
      
      chart.scrollbarX = new am4core.Scrollbar();
      chart.scrollbarX.fill = am4core.color("#017acd");
      
      var label = categoryAxis.renderer.labels.template;
      label.wrap = true;
      label.width = 150;
      label.minHeight = 20;
      label.fontFamily = "Helvetica";
      label.fontSize = "12";
      label.align = "left";
      
      categoryAxis.dataFields.category.fontSize = "10";

      $(".btn-tar").click(function() {
        localStorage.setItem('modal-response', 'tareas');
        window.parent.closeModal();
    });

    $(".btn-sub").click(function() {
        localStorage.setItem('modal-response', 'subtareas');
        window.parent.closeModal();
    });

</script>

@endsection