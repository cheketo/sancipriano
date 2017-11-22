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

$(document).ready(function(){
	
	if($("#customer").val()!='')
	{
		getCustomerData();
	}
	
	if($("#order_type").length>0)
	{
		$("#order_type").on('change',function(){
			if($(this).val()=='N')
			{
				$("#DeliveryWrapper").removeClass('Hidden');
			}else{
				$("#DeliveryWrapper").addClass('Hidden');
				$('#delivery_man').val('');
			}
		});
	}
	
	if(get['error']=="status")
	{
		notifyError('La orden no puede ser editada ya que no se encuentra en estado pendiente.');
	}
	
	if(get['error']=="user")
	{
		notifyError('La orden que desea editar no existe.');
	}
	
	customerData();
	
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
		
		$('.chosenSelect').chosen();
	}
});

function setItemChosen(id)
{
	// $('#item_'+id).chosen();
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
		            	//console.log(data);
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
			            	calculateTotalOrderPrice();
		            	});
		            }else{
		            	notifyError('Hubo un error al calcular el precio del producto');
		                console.log('Sin información devuelta. Item='+ids);
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
	if($(".delivery_date").length>0)
	{
		if($("#type").val()=="N")
		{
			$(".delivery_date").datepicker({
				autoclose:true,
				todayHighlight: true,
				language: 'es',
				startDate: '-0d',
				endDate: '+4m'
			});
		}else{
			$(".delivery_date").datepicker({
				autoclose:true,
				todayHighlight: true,
				language: 'es',
				endDate: '+4m'
			});
		}
	}
}

function setADatePicker(element)
{
	if($(".delivery_date").length>0)
	{
		if($("#type").val()=="N")
		{
			$(".delivery_date").datepicker({
				autoclose:true,
				todayHighlight: true,
				language: 'es',
				startDate: '-0d',
				endDate: '+4m'
			});
		}else{
			$(".delivery_date").datepicker({
				autoclose:true,
				todayHighlight: true,
				language: 'es',
				endDate: '+4m'
			});
		}
	}
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
	                calculateTotalOrderPrice();
	                $('.chosenSelect').chosen();
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
		calculateTotalOrderPrice();
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
	$("#BtnCreate,#BtnCreateNext,#BtnPay").on("click",function(e){
		e.preventDefault();
		if(validate.validateFields('*'))
		{
			calculateTotalOrderPrice();
			var confirmText,procText;
			var BtnID = $(this).attr("id");
			if(get['id']>0)
			{
				confirmText = "modificar";
				procText = "modificaci&oacute;n"
			}else{
				confirmText = "crear";
				procText = "creaci&oacute;n"
			}
			if(BtnID=="BtnPay") confirmText += " y pagar";
			if($("#customer_name").val())var customer = $("#customer_name").val();
			else var customer = $("#customer option:selected").text();
			confirmText += " la orden de compra de <b>"+customer+'</b>';
			
			if($("#order_type").val()=='N' && !$("#delivery_man").val())
			{
				var status = 'P';
			}else{
				var status = 'A';
			}
			
			alertify.confirm('¿Desea '+confirmText+'?', function(e){
				if(e)
				{
					var process		= '../../library/processes/proc.common.php?object=CustomerOrder';
					if(BtnID=="BtnCreate")
					{
						if($("#status").val())
							var target		= 'list.php?type='+$("#order_type").val()+'&status='+$("#status").val()+'&msg='+ $("#action").val();
						else
							var target		= 'list.php?type='+$("#order_type").val()+'&status='+status+'&msg='+ $("#action").val();
					}else{
						if(BtnID=="BtnCreateNext")
							var target		= 'new.php?type='+$("#order_type").val()+'&msg='+ $("#action").val();
						else{
							var stateObj = { url: "list.php?status=A&type=Y" };
							window.history.pushState(stateObj, "Ordenes en Local",stateObj.url);
							var target		= 'payment.php?id='+get['id']+'&msg='+ $("#action").val();
						}
					}
					var haveData	= function(returningData)
					{
						$("input,select").blur();
						if(returningData=="403")
						{
							notifyError("No es posible editar esta orden. No se encuentra en el estado correcto.");
						}else{
							if(isNaN(returningData))
								notifyError("Ha ocurrido un error durante el proceso de "+procText+".");
							else{
								if(BtnID=="BtnPay")
								{
									// Change browser to emulate cancel button in payment page
									//var stateObj = { url: "list.php?status=A&type=Y" };
									//window.history.pushState(stateObj, "Ordenes en Local",stateObj.url);
									//console.log(window.history.state);
									document.location = 'payment.php?id='+returningData+'&msg='+ $("#action").val();
									//console.log('payment.php?id='+returningData+'&msg='+ $("#action").val());
								}else{
									document.location = target;
								}
								
								
							}
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

function customerData()
{
	$("#customer").change(function() {
		getCustomerData();
	});
}

function getCustomerData()
{
	var id = $("#customer").val();
	var process = '../../library/processes/proc.common.php';
	var string	= 'id='+ id +'&action=Customerdata&object=CustomerOrder';
	$.ajax({
		type: "POST",
		url: process,
		data: string,
		cache: false,
		async: false,
		success: function(data){
			if(data)
			{
				$("#CustomerData").html(data);
				//console.log(data);
			}
        }
    });
}



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
		var string	= 'selected='+selected+'&user='+ user +'&action=Associate&object=CustomerOrder';
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

















//////////////////////////// PAYMENT ///////////////////////////////////////////
$(document).ready(function(){
	calculateRowPricePayment();
	countItemsPayment();
	calculateTotalOrderPricePayment();
	calculateTotalOrderQuantityPayment();
	saveItemPayment();
	editItemPayment();
	updateRowBackgroundPayment();
	showCheckForm();
	cancelCheck();
	addCheck()
	
	if($(".InputMask").length>0)
		$(".InputMask").inputmask();  //static mask
});

function calculateRowPricePayment()
{
	$(".calcablePayment").keyup(function(){
		var element = $(this).attr("id").split("_");
		var id = element[1];
		
		var price = parseFloat($("#PricePayment"+id).html());
		var quantity = parseFloat($("#quantity_"+id).val().replace(/_/g, "0"))
		if(price>0 && quantity>0)
			var total = price*quantity;
		else
			var total = 0.00;
		$("#item_number_"+id).attr("total",total);
		$("#item_number_"+id).html("$ "+total.formatMoney(2));	
		
		calculateTotalOrderQuantityPayment();
	});
}

function calculateTotalOrderQuantityPayment()
{
	var total = 0;
	$(".QuantityItemPayment").each(function(){
		var val = parseFloat($(this).val().replace(/_/g, "0"));
		if(val>0)
			total = total + val;
	});
	
	$("#TotalQuantityPayment").html(total);
}

function calculateTotalOrderPricePayment()
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
	$("#TotalPricePayment").html("$ "+total.formatMoney(2));
}

function countItemsPayment()
{
	$("#TotalItemsPayment").html($(".ItemRow").length);
}

function saveItemPayment()
{
	$(".SaveItemPayment").on("click",function(){
		var id = $(this).attr("item");
		if(validate.validateFields('item_form_'+id))
		{
			var quantity = $("#quantity_"+id).val().replace(/_/g, "0");
			if(quantity && parseFloat(quantity)>0)
			{
				$("#QuantityPayment"+id).html(quantity);
				$("#SaveItemPayment"+id+",#DeleteItemPayment"+id+",#quantity_"+id).addClass('Hidden');
				$("#EditItemPayment"+id+",#QuantityPayment"+id).removeClass('Hidden');
				$("#item_"+id).next().addClass('Hidden');
				$("#item_row_"+id).removeClass('bg-gray');
				$("#item_row_"+id).removeClass('bg-gray-active');
				$("#item_row_"+id).addClass('bg-green');
				$("#item_row_"+id).addClass('SelectedItem');
				$("#selected_"+id).val('Y');
				$("#quantity_"+id).val(quantity);
				updateRowBackgroundPayment();
				calculateTotalOrderPricePayment();
			}else{
				notifyError("Debe ingresar una cantidad mayor a 0 para entregar un producto.");	
			}
		}
	});
}

function editItemPayment()
{
	$(".EditItemPayment").on("click",function(){
		var id = $(this).attr("item");
		$("#SaveItemPayment"+id+",#DeleteItemPayment"+id+",#quantity_"+id).removeClass('Hidden');
		$("#EditItemPayment"+id+",#QuantityPayment"+id).addClass('Hidden');
		$("#item_"+id).next().removeClass('Hidden');
		$("#item_row_"+id).removeClass('bg-green');
		$("#item_row_"+id).removeClass('bg-green-active');
		$("#item_row_"+id).removeClass('SelectedItem');
		$("#selected_"+id).val('');
		updateRowBackground();
		calculateTotalOrderPricePayment();
	});
}

function updateRowBackgroundPayment()
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

$(function(){
	$("#BtnPayment").on("click",function(e){
		e.preventDefault();
			alertify.confirm(utf8_decode('¿Desea pagar la orden?<br><b>No podr&aacute; modificar la cantidad de productos entregados ni los medios de pago una vez pagado.</b>'), function(e){
				if(e)
				{
					var process		= '../../library/processes/proc.common.php?object=CustomerOrder';
					var target		= '../customer_national_order/list.php?status=F&type=Y&msg='+ $("#action").val();
					
					var haveData	= function(returningData)
					{
						$("input,select").blur();
						if(returningData=="403")
						{
							notifyError("No es posible pagar esta orden. Ya fue pagada previamente.");
						}else{
							notifyError("Ha ocurrido un error durante el proceso de pago.");
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