jQuery(function(){
		jQuery("#odb_category").chosen();
		jQuery("#service").chosen();
		jQuery("#emp_name").chosen();
		
		jQuery("#odb_category").change(function($) {
			var $ =jQuery;
			var odb_category = $(this).val();
			$("#getEmployee select").trigger("chosen:updated");

			// alert(odb_category);
			$.ajax({
					type: 'POST',
					dataType: 'json',
					url: ajaxurl,
					data: { 
							'action': 'odb_get_service_by_category', //calls wp_ajax_nopriv_ajaxlogin
							'odb_category': odb_category,
						},
				  
						
						 success: function(data){
						
						if(data){
						var dataLength = data.value.length;
						var odb_service = '';
						for (i = 0; i < dataLength; i++) {
							odb_service+='<option value="'+data.value[i].ID+'">'+data.value[i].title+'</option>';
						}

						$('#service').html(odb_service);
						$(".odb_services").trigger("chosen:updated");
			
				   }

			   }
					
			});
			
			// alert(odb_category);
			$.ajax({
					type: 'POST',
					dataType: 'json',
					url: ajaxurl,
					data: { 
							'action': 'odb_get_employee'
						},
				  
						
						 success: function(data){
						
						if(data){
						var dataLength = data.value.length;
						var emp_name = '';
						for (i = 0; i < dataLength; i++) {
							emp_name+='<option value="'+data.value[i].ID+'">'+data.value[i].user_nicename+'</option>';
						}

						$('#emp_name').html(emp_name);
						$(".emp_names").trigger("chosen:updated");
			
				   }

			   }
					
			});
		});
		
		
	});
	
	
    jQuery(document).ready(function(){
        jQuery(".add-row").click(function(){
            var service_id = jQuery("#service").val();
			var service = jQuery("#service option:selected").text();
            var number_services = jQuery("#number_services").val();
            var emp_name_id = jQuery("#emp_name").val();
			var emp_name = jQuery("#emp_name option:selected").text();
            //var emp_name = jQuery("#emp_name").val();
            var markup = "<tr><td><input type='checkbox' name='record'></td><td>" + service + "<input type='hidden' name='service_id[]' value='" + service_id + "'><input type='hidden' name='service_name[]' value='" + service + "'></td><td>" + number_services + "<input type='hidden' name='number_service[]' value='" + number_services + "'></td><td>" + emp_name + "<input type='hidden' name='empname[]' value='" + emp_name_id + "'></td></tr>";
            jQuery("table#add_staff tbody").append(markup);
        });
        
        // Find and remove selected table rows
        jQuery(".delete-row").click(function(){
            jQuery("table#add_staff tbody").find('input[name="record"]').each(function(){
                if(jQuery(this).is(":checked")){
                    jQuery(this).parents("tr").remove();
                }
            });
        });
    });    
	
    jQuery(document).ready(function(){
		jQuery( "#role" ).change(function() {
			var role = jQuery("#role").val()
			if( role == "customer"){
				jQuery('.custom_fields_client').show();
			} else {
				jQuery('.custom_fields_client').hide();				
			}
		});
	});
		