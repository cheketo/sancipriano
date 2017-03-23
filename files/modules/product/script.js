///////////////////////// CREATE/EDIT ////////////////////////////////////
$(function(){
	$("#BtnCreate,#BtnCreateNext").click(function(){
		if(validate.validateFields(''))
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

			confirmText += " el art&iacute;culo '"+utf8_encode($("#title").val())+"'";

			alertify.confirm(utf8_decode('¿Desea '+confirmText+' ?'), function(e){
				if(e)
				{
					var process		= '../../library/processes/proc.common.php?object=Product';
					if(BtnID=="BtnCreate")
					{
						var target		= 'list.php?element='+utf8_encode($('#title').val())+'&msg='+ $("#action").val();
					}else{
						var target		= 'new.php?element='+utf8_encode($('#title').val())+'&msg='+ $("#action").val();
					}
					var haveData	= function(returningData)
					{
						$("input,select").blur();
						notifyError("Ha ocurrido un error durante el proceso de "+procText+".");
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

	$("input").keypress(function(e){
		if(e.which==13){
			$("#BtnCreate").click();
		}
	});
});

function ShowCategoriesList(id)
{
    $('option[value="'+id+'"]').parent().parent().removeClass("Hidden");
    id = $('option[value="'+id+'"]').parent().parent().attr("category");
    if(id>0)
    {
        ShowCategoriesList(id);
    }
}

$(document).ready(function(){
    ////////////////////////// SET VALUES TO SELECT FIELDS ////////////
    if($('option[selected="selected"]').length>0)
    {
        var category = $('option[selected="selected"]');
        var categoryID = category.attr("value");
        var html = category.html();
        $("#category_selected").html(html);
        ShowCategoriesList(categoryID);
    }
    
    ///////////////////////// SELECT2 /////////////////////////////////
	if($('.selectTags').length>0)
	{
		$('.selectTags').select2({placeholder: {id: '',text: 'Seleccionar Marca'},allowClear: true});
		$('.selectTags').on("select2:select", function (e) { $("#brand").val(e.params.data.id); });
		$('.selectTags').on("select2:unselect", function (e) { $("#brand").val(''); });
	}
});
/////////// Show or Hide Icons On subtop //////////////////////
$(document).ready(function() {
    $('#viewlistbt').removeClass('Hidden');
    $('#newprod').removeClass('Hidden');
    $('#showitemfilters').removeClass('Hidden');

////////////////////// NUMBERS MASKS ////////////////////////////
    // $('#price,#price_fob,#price_dispatch').mask('00000000.00',{reverse: true});
    if($('#stock').length>0)
      $('#stock,#stock_min,#stock_max').mask('000000000000',{reverse: true});
    if($('.priceInput').length>0)
      $('.priceInput').inputmask();
});

///////// Select Product/Item ////////////////////////

$(function(){
    $(".category_selector").on('change',function(){
      var id = $(this).val();
      var html = $('option[value="'+id+'"]').html();
      var level = parseInt($(this).parent().attr('level'));
      var nextLevel = level+1;
      $("#category_selected").html(html);
      $("#category").val(id);
      
      if(nextLevel<=$("#maxlevel").val())
      {
        HideLevels(nextLevel);
        $("#CountinueBtn").addClass("Hidden");
      }
      if($("#category_"+id).parent().length>0)
        $("#category_"+id).parent().removeClass('Hidden');
      else
        $("#CountinueBtn").removeClass("Hidden");
    });
    
    $('#dispatch_data').on('click',function(){
      $('#dispatch_data').addClass('Hidden');
      $('.Dispatch').removeClass('Hidden');
    });
  
});

function HideLevels(level)
{
  $('li[level="'+level+'"]').addClass('Hidden');
  $('li[level="'+level+'"]').children('select').val(0);
  level++;
  if(level<=$("#maxlevel").val())
    HideLevels(level);
}
  




//////////////////// Character Counter ///////////////////////////
$('input, textarea').keyup(function() {
  var max = $(this).attr('maxLength');
  var curr = this.value.length;
  var percent = (curr/max) * 100;
  var indicator = $(this).parent().children('.indicator-wrapper').children('.indicator').first();

  // Shows characters left
  indicator.children('.current-length').html(max - curr);

  // Change colors
  if (percent > 10 && percent <= 50) { indicator.attr('class', 'indicator low'); }
  else if (percent > 50 && percent <= 70) { indicator.attr('class', 'indicator med'); }
  else if (percent > 70 && percent < 100) { indicator.attr('class', 'indicator high'); }
  else if (percent == 100) { indicator.attr('class', 'indicator full'); }
  else { indicator.attr('class', 'indicator empty'); }
  indicator.width(percent + '%');
});


/////////////////////// Categories ///////////////////////////


$(function(){
    $(".BackToCategory").on('click',function(){
      $('.ProductDetails').addClass('Hidden');
      $('.CategoryMain').removeClass('Hidden');
    });
    
    $('.SelectCategory').click(function(){
      $('.CategoryMain').addClass('Hidden');
      $('.ProductDetails').removeClass('Hidden');
    });
});


/////////////////////// LIST FUNCTIONS ///////////////////////////
$(function(){
  $("#selected_ids").on('change',function(){
      var ids = $(this).val();
      ids = ids.split(",");
      if(ids.length>1)
      {
        $("#MoreActions").removeClass("Hidden");
      }else{
        $("#MoreActions").addClass("Hidden");
        $("#MoreActionsBody").addClass("Hidden");
      }
  });
  
  
  $('#MoreActions').click(function(){
    $('#MoreActionsBody').toggleClass('Hidden');
  });
  
  $(".updatePrice").click(function(){
    var operation = $(this).attr("operation");
    var type = $(this).attr("price_type");
    var mode = $(this).attr("mode");
    var value;
    var ids = $("#selected_ids").val();
    if(mode=="#")
    {
      var mode_text="pesos";
    }else{
      var mode_text=mode;
    }
    if(operation=="add")
    {
      var operation_text = "aumentar";
    }else{
      var operation_text = "disminu&iacute;r";
    }
    if(type=="r")
    {
      var type_text = "minorista";
      value = $("#retail_price").val();
    }else{
      var type_text = "mayorista";
      value = $("#mayorist_price").val();
    }
    var confirmText = operation_text+" "+value+" "+mode_text+" el precio "+type_text+" de los productos seleccionados";
    alertify.confirm(utf8_decode('¿Desea '+confirmText+' ?'), function(e){
      
      var string      = 'ids='+ ids + '&action=update_price&object=Product&operation='+operation+'&mode='+mode+'&type='+type+'&value='+value;
      var finished = true;
      $.ajax({
          type: "POST",
          url: '../../library/processes/proc.common.php',
          data: string,
          cache: false,
          async: false,
          success: function(data){
              if(data)
              {
                finished = false;
                notifyError('Hubo un error al intentar cambiar los precios '+type_text);
                console.log(data);
              }else{
                notifySuccess('Precios '+type_text+' modificados correctamente');
              }
          }
      });
      if(finished)
      {
        $(".searchButton").click();
      }
    });
  });
  
});


