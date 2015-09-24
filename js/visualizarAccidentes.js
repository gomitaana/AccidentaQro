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

	        var options = {
	        	'title':'Accidentes 2014',
	    		animation:{
					duration: 700,
					easing: 'out',
				},
				height: 400,
				width: jQuery('#tabchart1').width(),
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
	          data.removeRows(0, data.getNumberOfRows());
		      data.addRows(jsonData);
		      switch(id_chart) {
		      	case 1:
		      		drawChart(chart1);
		      		break;
		      	case 2:
		      		drawChart(chart2);
		      		break;
		      	case 3:
		      		drawChart(chart3);
		      		break;
		      	case 4:
		      		drawChart(chart4);
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
