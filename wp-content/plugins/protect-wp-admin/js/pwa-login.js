jQuery(document).ready(function(){
	
	let u = pwaawp_object.u;
	
	let su = pwaawp_object.s+'/'+u.substr(3);
	
	let b = pwaawp_object.b;
	let l = pwaawp_object.l; 
	let c = pwaawp_object.c; 
	
	if( l != '') {
	 jQuery("#login h1 a").css('background', 'url(' + l + ')').css('background-repeat', 'no-repeat').css('background-size', 'contain');
	}	
	
	if( b != '') {
	 jQuery("body.login-action-login,html,.login .button-primary").css('background-color',b);
	}	
	
	if( c != '') {
	 jQuery(".login #backtoblog a, .login #nav a").css('color',c);
	}
	
	jQuery("#login #login_error a").attr("href",su+'/lostpassword');
	jQuery("body.login-action-resetpass p.reset-pass a").attr("href",su);
	var formId= jQuery("#login form").attr("id");
if(formId=="loginform"){
	jQuery("#"+formId).attr("action",su);
	}else if("lostpasswordform"==formId){
			jQuery("#"+formId).attr("action",su+'/lostpassword');
			jQuery("#"+formId+" input:hidden[name=redirect_to]").val(su+'/?checkemail=confirm');
		}else if("registerform"==formId){
			jQuery("#"+formId).attr("action",su+'/register');
			}
		else
			{
				//silent
				}			
        jQuery("#nav a").each(function(){
            var linkText = jQuery(this).attr("href").match(/[^/]*(?=(\/)?$)/)[0];
            if(linkText=="wp-login.php"){jQuery(this).attr("href",su);}
			else if(linkText=="wp-login.php?action=register"){jQuery(this).attr("href",su+'/register');}else if(linkText=="wp-login.php?action=lostpassword"){jQuery(this).attr("href",su+'/lostpassword');}else { 
				//silent
				}	
        });
	});