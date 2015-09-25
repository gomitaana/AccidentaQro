google.load('visualization', '1.0', {'packages':['corechart']});
google.setOnLoadCallback(init);

function init() {
	var jsonString = '[["Total",2490],["Fatal",13],["No fatal",250],["Solo da\u00f1os",2227]]';
	var jsonData = eval(jsonString);
	console.log(jsonString);

	var data = new google.visualization.DataTable();

	data.addColumn('string', 'Tipo');
	data.addColumn('number', 'Accidentes');

	data.addRows(jsonData);
	var w = jQuery('#tabchart1').width();
	var options = {
		'title':'Accidentes 2014',
		animation:{
			duration: 700,
			easing: 'out',
		},
		height: 400,
		width: w
	};
	var id_chart = 1;
	var chart1 = new google.visualization.ColumnChart(document.getElementById('chart1'));
	var chart2 = new google.visualization.PieChart(document.getElementById('chart2'));
	var chart3 = new google.visualization.LineChart(document.getElementById('chart3'));
	var chart4 = new google.visualization.BubbleChart(document.getElementById('chart4'));
	var button = document.getElementById('consultar');

	function drawChart(chart) {
	  button.disabled = true;
	  google.visualization.events.addListener(chart, 'ready',
		  function() {
			button.disabled = false;
		  });
	  chart.draw(data, options);
	}

	function updateChart() {
	  var jsonString = jQuery.ajax({
		  url: "http://accidentaqro.com/php/accidentes.php", //TODO: Cambiar a relativa en producción
		  dataType:"json",
		  async: false,
		  type: "POST",
		  data: { mes: jQuery( "#mes" ).val(), causa: jQuery( "#tipo" ).val(), sexo: jQuery( "#sexo" ).val(), 
				  alcohol: jQuery( "#alcohol" ).val(), cinturon: jQuery( "#cinturon" ).val(), consulta: id_chart },
		  statusCode: {
			500: function() {
			  alert("Error en servidor");
			}
		  },
		}).responseText;
	  var jsonData = eval(jsonString);
	  
	  data = new google.visualization.DataTable();
	  
	  var gender = [ 'Se fugo', 'Mujeres', 'Hombres', 'Total'];
	  if(id_chart == 4) {
		options = {
		'title':'Accidentes 2014',
		 vAxis: { logScale: true },
		hAxis: { 
		  gridlines: {
			count: gender.length,
			color : '#f7f7f7',
		  }
		},
		height: 400,
		width: w,
		};
	  } else {
		options = {
		  'title':'Accidentes 2014',
		  animation:{
			  duration: 700,
			  easing: 'out',
		  },
		  height: 400,
		  width: w
		};
	  }

	  switch(id_chart) {
		case 1:
			data.addColumn('string', 'Tipo');
			data.addColumn('number', 'Accidentes');		  
			data.addRows(jsonData);
			drawChart(chart1);
			break;
		case 2:
			data.addColumn('string', 'Tipo');
			data.addColumn('number', 'Accidentes');		  
			data.addRows(jsonData);
			drawChart(chart2);
			break;
		case 3:
			data.addColumn('string', 'Mes');
			data.addColumn('number', 'Accidentes');		  
			data.addRows(jsonData);
			drawChart(chart3);
			break;
		case 4:
			data.addColumn('string', 'Tipo');
			data.addColumn('number', 'Sexo');
			data.addColumn('number', 'Accidentes');
			data.addColumn('string', 'Descripcion');	
			data.addColumn('number', 'Accidentes');					
			data.addRows(jsonData);
			drawChart(chart4);
			
			for ( var i = 1; i <= gender.length; i ++ ){
			  $('#chart4 svg text[text-anchor="middle"]:contains("'+i+'")').text(function(j,t){
				if (t == i){
				if (i >= gender.length + 1 || i < 1){
					return " ";
				}
				return gender[i - 1];
				}
			  });
			}
			break;
	  }
	}

	button.onclick = function() {
		updateChart();
	}

	jQuery('.nav-tabs a').click(function() {
	  id_chart = jQuery(this).data("id");
	  updateChart();
	});

	drawChart(chart1);
  }
