if($('.delivery_date').length>0)
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
$(document).ready(function(){
	
		if($("#PendingOrdersList").length>0)
		{
			var group = $("#PendingOrdersList").sortable({
			  group: 'simple_with_animation',
			  onDrop: function ($item, container, _super) {
				    $('#orders').val(
				      group.sortable("serialize").get());
				    _super($item, container);
				    console.log($('#orders').val());
			  },
			  serialize: function (parent, children, isContainer) {
			    return isContainer ? children.join() : parent.attr("cli");
			  }
			});
			
			group = $("#OrdersList").sortable({group: 'simple_with_animation'});
			
			fixHeight();
			
			if(get['error']=="status")
			{
				notifyError('El reparto no puede ser editado ya que no se encuentra en estado pendiente.');
			}
			
			if(get['error']=="user")
			{
				notifyError('El reparto que desea editar no existe.');
			}
			
			showOrdersList();
			setDatePicker();
			
			if($("#id").val())
			{
				$('#delivery_date').change();
			}
		
	
		if($('#delivery_man').length>0)
		{
			$('#delivery_man').chosen();
		}
	}
});

function setDatePicker()
{
	$(".delivery_date").datepicker({
		autoclose:true,
		todayHighlight: true,
		language: 'es'
	});
}

function setADatePicker(element)
{
	$(element).datepicker({
		autoclose:true,
		todayHighlight: true,
		language: 'es'
	});
}

//////////////////////////// ORDER ITEMS //////////////////////////////////
function showOrdersList()
{
	$('#delivery_date').on("change",function(e){
		e.stopImmediatePropagation();
		var id				= $("#id").val();
		if(!$("#id").val())
			id=0;
		var delivery_date	= $("#delivery_date").val();
		var process 		= '../../library/processes/proc.common.php';
		var string			= 'id='+id+'&date='+ delivery_date +'&action=showorders&object=CustomerDelivery';
		$.ajax({
	        type: "POST",
	        url: process,
	        data: string,
	        cache: false,
	        success: function(data){
	            if(data)
	            {
	            	console.log(data);
	            	var lists = data.split("--///--");
	                $("#OrdersList").html(lists[0]);
	                $("#PendingOrdersList").html(lists[1]);
	                $(".jquerySorteable").sortable("refresh");
	                fixHeight();
	            }else{
	                console.log('Sin información devuelta.');
	            }
	        }
	    });
	});
}

// function syncDragList()
// {
// 	var id				= $("#id").val();
// 	if(!$("#id").val())
// 		id=0;
// 	var delivery_date	= $("#delivery_date").val();
// 	var process 		= '../../library/processes/proc.common.php';
// 	var string			= 'id='+id+'&date='+ delivery_date +'&action=showorderslist&object=CustomerDelivery';
// 	$.ajax({
//         type: "POST",
//         url: process,
//         data: string,
//         cache: false,
//         success: function(data){
//             if(data)
//             {
//                 $("#PendingOrdersList").html(data);
//                 $(".jquerySorteable").sortable("refresh");
//             }else{
//                 console.log('Sin información devuelta.');
//             }
//         }
//     });
// }

function fixHeight()
{
	// var height = parseInt($("#PendingOrdersList").css('height'))+40;
	// $("#PendingListContainer").css('height',height+'px');
	
	$("#ListContainer").css('height',(parseInt($("#OrdersList").css('height'))+80)+'px');
	$("#PendingListContainer").css('height',(parseInt($("#PendingOrdersList").css('height'))+40)+'px');
}

///////////////////////// CREATE/EDIT ////////////////////////////////////
$(function(){
	$("#BtnCreate,#BtnCreateNext").on("click",function(e){
		e.preventDefault();
		if(validate.validateFields('*'))
		{
			var BtnID = $(this).attr("id")
			if(get['id']>0)
			{
				confirmText = "modificar";
				procText = "modificaci&oacute;n"
			}else{
				confirmText = "crear";
				procText = "creaci&oacute;n"
			}

			confirmText += " el reparto";

			alertify.confirm(utf8_decode('¿Desea '+confirmText+' ?'), function(e){
				if(e)
				{
					var process		= '../../library/processes/proc.common.php?object=CustomerDelivery';
					if(BtnID=="BtnCreate")
					{
						var delivery_date = "";
						if(get['delivery_date'])
						{
							delivery_date = '&delivery_date=today';
						}
						var target		= 'list.php?msg='+ $("#action").val() + delivery_date;
					}else{
						var target		= 'new.php?msg='+ $("#action").val();
					}
					var haveData	= function(returningData)
					{
						$("input,select").blur();
						if(returningData=="403")
						{
							notifyError("No es posible editar este reparto. No se encuentra en el estado correcto.");
						}else{
							notifyError("Ha ocurrido un error durante el proceso de "+procText+".");
						}
						console.log(returningData);
					}
					var noData		= function()
					{
						document.location = target;
					}
					sumbitFields(process,haveData,noData);
				}
			});
		}
	});
});
// ///////////////////////////// START DELIVERY ////////////////////////////////////////
// $(function(){
// 	$("#BtnStart").on("click",function(e){
// 		e.preventDefault();
		
			

// 			alertify.confirm(utf8_decode('¿Desea empezar el reparto?<br><b>No podr&aacute; modificar la cantidad de merluza una vez empezado.</b>'), function(e){
// 				if(e)
// 				{
// 					var process		= '../../library/processes/proc.common.php?object=CustomerDelivery';
// 					var target		= '../customer_delivery_order/list.php?msg='+ $("#action").val();
					
// 					var haveData	= function(returningData)
// 					{
// 						$("input,select").blur();
// 						if(returningData=="403")
// 						{
// 							notifyError("No es posible empezar este reparto. Ya fue empezado previamente.");
// 						}else{
// 							notifyError("Ha ocurrido un error durante el proceso de "+procText+".");
// 						}
// 						console.log(returningData);
// 					}
// 					var noData		= function()
// 					{
// 						document.location = target;
// 					}
// 					sumbitFields(process,haveData,noData);
// 				}
// 			});
// 	});
// });