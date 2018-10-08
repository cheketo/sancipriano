if($(".calendar_date").length>0)
{
	$.fn.datepicker.dates['es'] = {
	    days: ["Domingo", "Lunes", "Martes", "Miércoles", "Juves", "Viernes", "Sábado"],
	    daysShort: ["Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab"],
	    daysMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
	    months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
	    monthsShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
	    today: "Hoy",
	    clear: "Borrar",
	    format: "dd/mm/yyyy",
	    titleFormat: "MM yyyy", /* Leverages same syntax as 'format' */
	    weekStart: 1
	};
}

$(document).ready(function(){
	$(".calendar_date").datepicker({
		autoclose:true,
		todayHighlight: true,
		language: 'es'
	});
});


///////////////////////// CREATE/EDIT ////////////////////////////////////
$(function(){
	$("#BtnReport").on("click",function(){
		if(!$("#from").val() || !$("#to").val() || parseInt($("#from").val().split('/').reverse().join(""))<=parseInt($("#to").val().split('/').reverse().join("")))
		{
			var from = $("#from").val().split('/').reverse().join("-");
			var to = $("#to").val().split('/').reverse().join("-");
			document.location = 'report.php?from='+from+'&to='+to;
		}else{
			notifyError("Seleccione un int&eacute;rvalo v&aacute;lido.");
		}
	});
});