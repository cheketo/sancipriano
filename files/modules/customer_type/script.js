$(document).ready(function(){
	if($('.chosenSelect').length>0)
	{
		$('.chosenSelect').chosen();
	}
});
///////////////////////// CREATE/EDIT ////////////////////////////////////
$(function(){
	$("#BtnCreate").on("click",function(e){
		e.preventDefault();
		
		
		if(validate.validateFields('customer_type_form'))
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

			confirmText += " el tipo de cliente '"+$("#name").val()+"'";

			alertify.confirm(utf8_decode('¿Desea '+confirmText+' ?'), function(e){
				if(e)
				{
					var process		= '../../library/processes/proc.common.php?object=CustomerType';
					if(BtnID=="BtnCreate")
					{
						var target		= 'list.php?element='+$('#title').val()+'&msg='+ $("#action").val();
					}else{
						var target		= 'new.php?element='+$('#title').val()+'&msg='+ $("#action").val();
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

	// $("input").keypress(function(e){
	// 	if(e.which==13){
	// 		$("#BtnCreate").click();
	// 	}
	// });
});

///////////////////////// CREATE/EDIT FORM FUNCTIONS ////////////////////////////////////
///////////////////////// UPLOAD IMAGE ////////////////////////////////////
// $(function(){
// 	$("#image_upload").on("click",function(){
// 		$("#image").click();	
// 	});
	
// 	$("#image").change(function(){
// 		var process		= '../../library/processes/proc.common.php?action=newimage&object=Provider';
// 		var haveData	= function(returningData)
// 		{
// 			$('#newimage').val(returningData);
// 			$("#company_logo").attr("src",returningData);
// 			$('#company_logo').addClass('pulse').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
// 		      $(this).removeClass('pulse');
// 		    });
// 		}
// 		var noData		= function(){console.log("No data");}
// 		sumbitFields(process,haveData,noData);
// 	});
// 	ShowAgentForm();
// });
