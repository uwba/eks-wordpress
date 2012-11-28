/**
 * custom style radio & checkbox
 * 
 * author page: http://shuric.dp.ua
 *
 * Copyright (c) 2011 Shuric A. Cornery
 * Licensed under the MIT License: http://www.opensource.org/licenses/mit-license.php
 *
 * version 0.1
 * 04/08/2011
 *
 * .my-checkbox {
 *	   height: 22px; width: 21px; margin: 0 0 3px; padding: 0 0 0 0; cursor: pointer; text-align:left;
 *	   background: url(/images/my-checkbox.png) no-repeat;
 * }
 *
 * <div class="my-checkbox"><input type="checkbox" name="" checked="checked"/></div>
 * 
 * $(document).ready(function() {$('.my-checkbox').cbStyle({h:22});});	
 * 
 */


(function($) {                                          
	$.fn.cbStyle = function(o) {
	
		var methods = {
			draw : function() { 
				t = $(this).data('type');
				
				var objCB	=	$(this).children().get(0);
				
				$(objCB).css('display','none');
				$(this).data('type',$(objCB).attr("type"));
				$(this).data('checked',$(objCB).is(':checked'));
				$(this).data('disabled',$(objCB).attr("disabled"));
				
				if ($(this).data('disabled')) {
					if($(this).data("checked"))	{
						$(this).css("backgroundPosition","center -"+(options.h*5)+"px");
					} else {
						$(this).css("backgroundPosition","center -"+(options.h*2)+"px");
					}	
					$(this).css('cursor','default');
				} else {
					if($(this).data("checked"))	{
						$(this).css("backgroundPosition","center -"+(options.h*3)+"px");
					} else {
						$(this).css("backgroundPosition","center 0");
					}	
				}
				
				return (t == undefined);
			}
		};	
		
		var options = jQuery.extend({
			h:0,
			onChange : function(state, id_cb) {
//				console.log(id_cb);
			}
		}, o);
		
		return this.each( function(){
			if (options.h==0) {
				options.h = parseInt($(this).css('height'));
			}
		
			fl_new = methods['draw'].apply(this);
			if (fl_new) {
			
				if (!$(this).data('disabled')) {
			
					$(this).bind('mouseover', function() {
						if($(this).data("checked")) {
							$(this).css({backgroundPosition:"center -"+(options.h*4)+"px"});
						} else {
							$(this).css({backgroundPosition:"center -"+(options.h*1)+"px"});
						}
					}); 
					
					$(this).bind('mouseout', function() {
						if($(this).data("checked")) {
							$(this).css({backgroundPosition:"center -"+(options.h*3)+"px"});
						} else {
							$(this).css({backgroundPosition:"center -"+(options.h*0)+"px"});
						}
					}); 
					
					$(this).bind('mouseup', function() {
						var id_cb = $(this).children().get(0);
						if($(this).data("checked")) {
							if ($(this).data('type')=='checkbox') {
								$(id_cb).attr("checked",false);
								$(this).data('checked',false).css({backgroundPosition:"center 0"});
							}	
						} else {
							$(id_cb).attr("checked",true);
							$(this).data('checked',true).css({backgroundPosition:"center -"+(options.h*3)+"px"});
						}	
					

						if($(this).data('type')=='radio' && $(this).data("checked"))	{
							$.each($("input[name='"+$(id_cb).attr("name")+"']"),function() {
								if(id_cb!=this) {
									$(this).attr("checked",false);
									$(this).parent().data("checked",false).css({backgroundPosition:"center 0"});
								}
							});
						}
						
						options.onChange.call(this, $(this).data("checked"), id_cb);
						
					}); 
				
				}
				
			}
			
		});
	}
	
})(jQuery);