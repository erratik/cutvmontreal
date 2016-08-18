jQuery(document).ready(function($) {
	
	function fes_validate_email(email){
		var emailReg = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
		var valid = emailReg.test(email);
		if(!valid) return false;
		else return true;
	}
	
	
	
	$('body').on('click','.fes_submit_again a',function(e){			
		e.preventDefault();
		window.location.reload();
	
	});
	
	$('.fes_form_wrap').each(function(){
		var form = $(this);
		var btn = $('.fes_submit_video' , form );
		var response = $('.fes_response' , form );
		var url = btn.attr('url');
		var captcha = $('#captcha' , form );
		var hash = '';
		
		
		
		if( typeof captcha.attr('value') !== 'undefined' ){
			captcha.realperson({length: 5});
			setTimeout( function(){
				//console.log( captcha.realperson('getHash') );
				$('.realperson-challenge').trigger('click').fadeOut();
				//console.log( captcha.realperson('getHash') );
			} , 1000);
			
		}
		
		btn.click(function(e){
			e.preventDefault();
			var btn = $(this);
			if( btn.hasClass('isLoading') ) return false;
			btn.addClass('isLoading');
			
			
			
			var userid = $('#submitter_id' , form).attr('value') ;
			var category = $('#submitter_category' , form).attr('value') ;
			var email = $('#submitter_email' , form).attr('value') ;
			var name = $('#submitter_email' , form).attr('value') ;
			var videoid = $('#video_id' , form).attr('value') ;
			var video_service = $('#video_service' , form).attr('value') ;

			if( typeof captcha.attr('value') !== 'undefined' ){
				captcha.realperson({length: 5});
				var captcha_value = captcha.attr('value') ;
				var captcha_hash = captcha.realperson('getHash');
			}else{
				var captcha_value = btn.attr('token') ;
				var captcha_hash = btn.attr('token');
			}
			
			
			
			var form_data = form.serialize();
			if( !name || !videoid || !email || !fes_validate_email(email) ) {
				//console.log('CORRECT YOUR ENTRY');
				$('.fes_error_response' , form ).fadeIn().html(' Please correct your entry ! ');
				btn.removeClass('isLoading');
				return false;
			}
			response.html('Loading ...').fadeIn();
			$.ajax({             
				type: 'POST',
				url: url,
				data: {
					userid : userid ,
					name : name ,
					category : category ,
					email : email ,
					videoid : videoid ,
                    video_service : video_service ,
					captcha_value : captcha_value ,
					captcha_hash : captcha_hash ,
				},
				success: function(data) {
					$json = wpvr_get_json( data );
					btn.removeClass('isLoading');
					if( $json.status == 0 ) 
						response.html( $json.msg ).removeClass('ok').removeClass('ko').addClass('ko').fadeIn();
					else 
						form.html( $json.msg ) ;
				},            
				error: function (xhr, ajaxOptions, thrownError) {              
					alert(thrownError);            
				}         
			});
			
			
			
		});
		
		
	});
	
	
	
});


	
	
	
	