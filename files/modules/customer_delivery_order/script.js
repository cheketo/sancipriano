if($(".delivery_date").length>0)
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

function setDatePicker()
{
	if($(".delivery_date").length>0)
	{
		$(".delivery_date").datepicker({
			autoclose:true,
			todayHighlight: true,
			language: 'es',
			startDate: '-0d',
			endDate: '+4m'
		});
	}
}
///////////////////////// CREATE/EDIT ////////////////////////////////////
$(function(){
	$("#BtnCreate,#BtnCreateNext").on("click",function(e){
		if(validate.validateFields('*'))
		{
		e.preventDefault();
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


///////////////////////////////////////////// DELIVERY //////////////////
$(document).ready(function(){
	calculateRowPrice();
	countItems();
	calculateTotalOrderPrice();
	calculateTotalOrderQuantity();
	saveItem();
	editItem();
	updateRowBackground();
	showCheckForm();
	cancelCheck();
	addCheck()
	
	setDatePicker();
	
	if($(".InputMask").length>0)
		$(".InputMask").inputmask();  //static mask
});

function calculateRowPrice()
{
	$(".calcable").keyup(function(){
		var element = $(this).attr("id").split("_");
		var id = element[1];
		
		var price = parseFloat($("#Price"+id).html());
		var quantity = parseFloat($("#quantity_"+id).val().replace(/_/g, "0"))
		if(price>0 && quantity>0)
			var total = price*quantity;
		else
			var total = 0.00;
		$("#item_number_"+id).attr("total",total);
		$("#item_number_"+id).html("$ "+total.formatMoney(2));	
		
		calculateTotalOrderQuantity();
	});
}

function calculateTotalOrderQuantity()
{
	var total = 0;
	$(".QuantityItem").each(function(){
		var val = parseFloat($(this).val().replace(/_/g, "0"));
		if(val>0)
			total = total + val;
	});
	
	$("#TotalQuantity").html(total);
}

function calculateTotalOrderPrice()
{
	var total = 0.00;
	$(".item_number").each(function(){
		if($(this).parent().parent().hasClass('SelectedItem'))
		{
			var val = parseFloat($(this).attr("total"));
			if(val>0)
				total = total + val;
		}
	});
	$("#total_price").val(total);
	$("#TotalPrice").html("$ "+total.formatMoney(2));
}

function countItems()
{
	$("#TotalItems").html($(".ItemRow").length);
}

function saveItem()
{
	$(".SaveItem").on("click",function(){
		var id = $(this).attr("item");
		if(validate.validateFields('item_form_'+id))
		{
			var quantity = $("#quantity_"+id).val().replace(/_/g, "0");
			if(quantity && parseFloat(quantity)>0)
			{
				$("#Quantity"+id).html(quantity);
				$("#SaveItem"+id+",#DeleteItem"+id+",#quantity_"+id).addClass('Hidden');
				$("#EditItem"+id+",#Quantity"+id).removeClass('Hidden');
				$("#item_"+id).next().addClass('Hidden');
				$("#item_row_"+id).removeClass('bg-gray');
				$("#item_row_"+id).removeClass('bg-gray-active');
				$("#item_row_"+id).addClass('bg-green');
				$("#item_row_"+id).addClass('SelectedItem');
				$("#selected_"+id).val('Y');
				 $("#quantity_"+id).val(quantity);
				updateRowBackground();
				calculateTotalOrderPrice();
			}else{
				notifyError("Debe ingresar una cantidad mayor a 0 para entregar un producto.");	
			}
		}
	});
}

function editItem()
{
	$(".EditItem").on("click",function(){
		var id = $(this).attr("item");
		$("#SaveItem"+id+",#DeleteItem"+id+",#quantity_"+id).removeClass('Hidden');
		$("#EditItem"+id+",#Quantity"+id).addClass('Hidden');
		$("#item_"+id).next().removeClass('Hidden');
		$("#item_row_"+id).removeClass('bg-green');
		$("#item_row_"+id).removeClass('bg-green-active');
		$("#item_row_"+id).removeClass('SelectedItem');
		$("#selected_"+id).val('');
		updateRowBackground();
		calculateTotalOrderPrice();
	});
}

function updateRowBackground()
{
	var bgClass1 = "bg-gray";
	var bgClass2 = "bg-green";
	$(".ItemRow").each(function(){
		if(!$(this).hasClass('bg-green') && !$(this).hasClass('bg-green-active'))
		{
			$(this).removeClass("bg-gray");
			$(this).removeClass("bg-gray-active");
			$(this).addClass(bgClass1);
		}else{
			$(this).removeClass("bg-green");
			$(this).removeClass("bg-green-active");
			$(this).addClass(bgClass2);
		}
		if(bgClass1 == "bg-gray")
			bgClass1 = "bg-gray-active";
		else
			bgClass1 = "bg-gray";
			
		if(bgClass2 == "bg-green")
			bgClass2 = "bg-green-active";
		else
			bgClass2 = "bg-green";
	});
}

///////////////////////////// DELIVERY ORDER ////////////////////////////////////////
$(function(){
	$("#BtnDelivery").on("click",function(e){
		e.preventDefault();
			alertify.confirm(utf8_decode('¿Desea despachar la orden?<br><b>No podr&aacute; modificar la cantidad de productos entregados ni los medios de pago una vez despachado.</b>'), function(e){
				if(e)
				{
					var process		= '../../library/processes/proc.common.php?object=CustomerDeliveryOrder';
					var target		= '../customer_delivery_order/list.php?msg='+ $("#action").val();
					
					var haveData	= function(returningData)
					{
						$("input,select").blur();
						if(returningData=="403")
						{
							notifyError("No es posible despachar este reparto. Ya fue despachado previamente.");
						}else{
							notifyError("Ha ocurrido un error durante el proceso de entrega.");
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
	});
});

$(function(){
	$("#BtnStart").on("click",function(e){
		e.preventDefault();
			alertify.confirm(utf8_decode('¿Desea empezar el reparto?<br><b>No podr&aacute; modificar las ordenes ni la merluza una vez empezado.</b>'), function(e){
				if(e)
				{
					var process		= '../../library/processes/proc.common.php?object=CustomerDelivery';
					var target		= '../customer_delivery_order/list.php?msg='+ $("#action").val();
					
					var haveData	= function(returningData)
					{
						$("input,select").blur();
						if(returningData=="403")
						{
							notifyError("No es posible empezar este reparto. Ya fue empezado previamente.");
						}else{
							notifyError("Ha ocurrido un error durante el proceso de iniciaci&oacute;n del reparto.");
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
	});
});


function showCheckForm()
{
	$("#ShowCheck").click(function(){
		$(this).addClass('Hidden');
		$("#AddCheck").removeClass('Hidden');
		$("#CancelCheck").removeClass('Hidden');
		$("#check_row").removeClass('Hidden');
	});
}

function cancelCheck()
{
	$("#CancelCheck").click(function(){
		$(this).addClass('Hidden');
		$("#AddCheck").addClass('Hidden');
		$("#check_row").addClass('Hidden');
		$("#ShowCheck").removeClass('Hidden');
		resetCheckForm();
	});
}

function resetCheckForm()
{
	$('.CheckForm').val('');
}

function addCheck()
{
	$("#AddCheck").on('click',function(e){
		e.stopPropagation();
		if(validate.validateFields('check_form'))
		{
			var check = parseInt($("#checks").val())+1;
			var amount = $("#check_amount").val();
			amount = amount.split('$');
			amount = amount[1];
			var from = $("#check_from").val();
			var number = $("#check_number").val();
			var bank = $("#check_bank").val();
			var date = $("#check_date").val();
			$("#CheckWrapper").append('<div class="col-xs-12 col-sm-6 col-md-4" id="check_'+check+'"><div class="box box-success"><div class="box-header with-border"><h4 class="box-title">Cheque N&deg; '+number+'</h4><input type="hidden" value="'+number+'" id="check_number_'+check+'" /><div class="box-tools pull-right"><i class="fa fa-times text-danger removeCheck" style="cursor:pointer;" check="'+check+'"></i></div></div><div class="box-body"><div class="row"><div class="col-xs-6">Monto:</div><div class="col-xs-6"><b>$ '+amount+'</b></div><input type="hidden" value="'+amount+'" id="check_amount_'+check+'" /></div><div class="row"><div class="col-xs-6">Banco:</div><div class="col-xs-6"><b>'+bank+'</b></div><input type="hidden" value="'+bank+'" id="check_bank_'+check+'" /></div><div class="row"><div class="col-xs-6">Emisor:</div><div class="col-xs-6"><b>'+from+'</b><input type="hidden" value="'+from+'" id="check_from_'+check+'" /></div></div><div class="row"><div class="col-xs-6">Vencimiento:</div><div class="col-xs-6"><b>'+date+'</b><input type="hidden" value="'+date+'" id="check_date_'+check+'" /></div></div></div></div></div>');
			$("#CancelCheck").click();
			$("#checks").val(check);
			removeCheck().stopImmediatePropagation();
		}
	});
}

function removeCheck()
{
	$(".removeCheck").on('click',function(e){
		e.preventDefault();
		e.stopImmediatePropagation();
		var check = $(this).attr('check');
		var number = $("#check_number_"+check).val();
		var amount = $("#check_amount_"+check).val();
		alertify.confirm(utf8_decode('¿Desea eliminar el cheque N° <b>'+number+'</b> (<b>$'+amount+'</b>)?'), function(e){
			if(e)
			{
				$("#check_"+check).remove();
				$("#checks").val(parseInt($("#checks").val())+1);
			}
		});
	});
}