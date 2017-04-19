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
	
	if(get['error']=="status")
	{
		notifyError('La orden no puede ser editada ya que no se encuentra en estado pendiente.');
	}
	
	if(get['error']=="user")
	{
		notifyError('La orden que desea editar no existe.');
	}
	
	addOrderItem();
	saveItem();
	calculateRowPrice();
	editItem();
	changeDates();
	countItems();
	calculateTotalOrderPrice();
	calculateTotalOrderQuantity();
	
	setDatePicker();
	priceImputMask(1);
	
	if($('.chosenSelect').length>0)
	{
		$(".itemSelect").each(function(){
			var item = $(this).attr('item');
			setItemChosen(item);
		});
		
		if($('#customer').length>0)
		{
			$('#customer').chosen();
			recalculateItemPrice();
		}
		
		if($('#user_id').length>0)
		{
			$('#user_id').chosen();
		}
		
	}
});

function setItemChosen(id)
{
	$('#item_'+id).chosen();
	$('#item_'+id).on('change',function(){
		getProductsPrices($('#item_'+id).val(),id);
	});
}

function getProductsPrices(values,ids)
{
	var customer = $("#customer").val();
	var process = '../../library/processes/proc.common.php';
	var string	= 'items='+ values +'&customer='+customer+'&action=Getitemprices&object=CustomerOrder';
	if(values.length>0 && parseInt(customer)>0)
	{
		if(ids)
		{
			ids = ids +'';
			$.ajax({
		        type: "POST",
		        url: process,
		        data: string,
		        success: function(data){
		            if(data)
		            {
		            	console.log(data);
		            	var prices = data.split(",");
		            	var items = ids.split(",");
		            	var decimal;
		            	prices.forEach(function(price,index){
		            		decimal = price.substr(price.indexOf("."));
			            	if(decimal.length==1)
			            	{
			            		price = price + ".00";
			            	}
			            	if(decimal.length==2)
			            	{
			            		price = price + "0";
			            	}
			            	$("#price_"+items[index]).val(price);
			            	$("#Price"+items[index]).html("$ "+price);
		            	});
		            }else{
		            	notifyError('Hubo un error al calcular el precio del producto');
		                console.log('Sin información devuelta. Item='+id);
		            }
		        }
		    });
		}
	}
}

function recalculateItemPrice()
{
	$('#customer').change(function(e){
		e.stopImmediatePropagation();
		var ids="";
		var values = "";
		var first = true;
		$(".itemSelect").each(function(){
			var id = $(this).attr("item");
			var value = $("#item_"+id).val();
			if(value)
			{
				if(first)
				{
					ids = id;
					values = value;
					first = false;
				}else{
					ids = ids+","+id;
					values = values + ","+value;
				}
			}
		});
		getProductsPrices(values,ids);
	});
	
}

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

function priceImputMask(id)
{
	if($("#price_"+id).length>0)
		$("#price_"+id).inputmask();  //static mask
		
	if($("#quantity_"+id).length>0)
		$("#quantity_"+id).inputmask();  //static mask
	
	$("#price_"+id).change(function(){
		var decimal = $(this).val().split(".");
		if(decimal[1]=="__")
		{
			$("#price_"+id).val(decimal[0]+".00");
		}
	});	
}

//////////////////////////// ORDER ITEMS //////////////////////////////////
function addOrderItem()
{
	$("#add_order_item").on('click',function(e){
		e.stopImmediatePropagation();
		var id		= parseInt($("#items").val())+1;
		var process = '../../library/processes/proc.common.php';
		var string	= 'item='+ id +'&action=addorderitem&object=CustomerOrder';
		$.ajax({
	        type: "POST",
	        url: process,
	        data: string,
	        cache: false,
	        success: function(data){
	            if(data)
	            {
	                //$("#ItemWrapper").append(data);
	                //$("#item_row_"+$("#items").val()).after(data);
	                $(".ItemRow:last-child").after(data);
	                $("#items").val(id);
	                setItemChosen(id);
	                saveItem();
	                editItem();
	                deleteItem();
	                setADatePicker("#date_"+id);
	                validateDivChange();
	                countItems();
	                calculateRowPrice();
	                priceImputMask(id);
	                updateRowBackground();
	                recalculateItemPrice();
	            }else{
	                console.log('Sin información devuelta. Item='+id);
	            }
	        }
	    });
		
	});
}

function saveItem()
{
	$(".SaveItem").on("click",function(){
		var id = $(this).attr("item");
		if(validate.validateFields('item_form_'+id))
		{
			var item_id = $("#item_"+id).val();
			var item = $("#item_"+id).children('option[value="'+item_id+'"]').html();
			var price = $("#price_"+id).val();
			var quantity = $("#quantity_"+id).val();
			var delivery = $("#date_"+id).val();
			$("#Item"+id).html(item);
			$("#Price"+id).html("$ "+price);
			$("#Quantity"+id).html(quantity);
			$("#Date"+id).html(delivery);
			$("#SaveItem"+id+",.ItemField"+id).addClass('Hidden');
			$("#EditItem"+id+",.ItemText"+id).removeClass('Hidden');
			$("#item_"+id).next().addClass('Hidden');
		}
	});
}

function editItem()
{
	$(".EditItem").on("click",function(){
		var id = $(this).attr("item");
		$("#SaveItem"+id+",.ItemField"+id).removeClass('Hidden');
		$("#EditItem"+id+",.ItemText"+id).addClass('Hidden');
		$("#item_"+id).next().removeClass('Hidden');
	});
}

function deleteItem()
{
	$(".DeleteItem").click(function(){
		var id = $(this).attr("item");
		$("#item_row_"+id).remove();
		countItems();
		calculateTotalOrderPrice();
		calculateTotalOrderQuantity();
		updateRowBackground();
	});
	
}

function updateRowBackground()
{
	var bgClass = "bg-gray";
	$(".ItemRow").each(function(){
		$(this).removeClass("bg-gray");
		$(this).removeClass("bg-gray-active");
		$(this).addClass(bgClass);
		if(bgClass == "bg-gray")
			bgClass = "bg-gray-active";
		else
			bgClass = "bg-gray";
	});
}

function countItems()
{
	$("#TotalItems").html($(".ItemRow").length);
}

function calculateRowPrice()
{
	$(".calcable").change(function(){
		var element = $(this).attr("id").split("_");
		var id = element[1];
		
		var price = parseFloat($("#price_"+id).val());
		var quantity = parseInt($("#quantity_"+id).val())
		if(price>0 && quantity>0)
			var total = price*quantity;
		else
			var total = 0.00;
		$("#item_number_"+id).attr("total",total);
		$("#item_number_"+id).html("$ "+total.formatMoney(2));	
		
		calculateTotalOrderPrice();
		calculateTotalOrderQuantity();
	});
}

function calculateTotalOrderQuantity()
{
	var total = 0;
	$(".QuantityItem").each(function(){
		var val = parseInt($(this).val());
		if(val>0)
			total = total + val;
	});
	
	$("#TotalQuantity").html(total);
}

function calculateTotalOrderPrice()
{
	var total = 0.00;
	$(".item_number").each(function(){
		var val = parseFloat($(this).attr("total"));
		if(val>0)
			total = total + val;
	});
	$("#total_price").val(total);
	$("#TotalPrice").html("$ "+total.formatMoney(2));
}

function changeDates()
{
	$("#ChangeDates").click(function(){
		var date = $("#change_date").val();
		alertify.confirm(utf8_decode('¿Desea aplicar la fecha '+date+' a todos los art&iacute;culos ?'), function(e){
		if(e)
		{
			$(".delivery_date").each(function(){
				$(this).val(date);
			});
			
			$(".OrderDate").each(function(){
				$(this).html(date);
			});
		}
		});
	});
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

			confirmText += " la orden de compra";

			alertify.confirm(utf8_decode('¿Desea '+confirmText+' ?'), function(e){
				if(e)
				{
					var process		= '../../library/processes/proc.common.php?object=CustomerOrder';
					if(BtnID=="BtnCreate")
					{
						var target		= 'list.php?msg='+ $("#action").val();
					}else{
						var target		= 'new.php?msg='+ $("#action").val();
					}
					var haveData	= function(returningData)
					{
						$("input,select").blur();
						if(returningData=="403")
						{
							notifyError("No es posible editar esta orden. No se encuentra en el estado correcto.");
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

	// $("input").keypress(function(e){
	// 	if(e.which==13){
	// 		if($("#BtnCreate").is(":disabled"))
	// 		{
	// 			$("#agent_new").click();
	// 		}else{
	// 			$("#BtnCreate").click();
	// 		}
	// 	}
	// });
});



///////////////////////////// LIST ACTIONS /////////////////////////////////
$(document).ready(function(){
	activateModal();
	
	$("#selected_ids").on('change',function(){
		var ids = $(this).val();
		ids = ids.split(",");
		if(ids.length>1)
		{
			$("#Associate").removeClass("Hidden");
		}else{
			$("#Associate").addClass("Hidden");
		}
	});
	  
	/// SHOW MODAL
	$('#Associate').click(function(){
		$("#associateModal").show();
	});
	
	/// HIDE MODAL
	$('.close,#associate_user').click(function(){
		$("#associateModal").hide();	
	});
	
	$('#associate_user').click(function(){
		var finished = true;
		var user	= $('#user_id').val();
		var selected = $("#selected_ids").val();
		var process = '../../library/processes/proc.common.php';
		var string	= 'selected='+selected+'&user='+ user +'&action=associate&object=CustomerOrder';
		$.ajax({
	        type: "POST",
	        url: process,
	        data: string,
	        cache: false,
        	async: false,
	        success: function(data){
	            if(data)
	            {
	            	finished = false;
	                console.log(data);
	                notifyError('Ha ocurrido un error. Por favor, intente nuevamente.');
	            }else{
	            	
	                // var ids = $('#selected_ids').val().split(',');
	                // $('#UnselectAll').click();
	                // ids.forEach(removeRow);
	            }
	        }
	    });
	    if(finished)
		{
			$(".searchButton").click();
		}
	});
	
	
});

function removeRow(id)
{
	// unselectRow(id);
	$('#row_'+id).remove();
	$('#grid_'+id).remove();
}

function activateModal()
{
	$('.associateElement').on('click',function(e){
		e.preventDefault();
		e.stopPropagation();
		$('#associateModal').show();
	});
}