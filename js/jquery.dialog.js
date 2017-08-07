/**
 * @detail
 * Additional function to handle content
 * http://zourbuth.com/
 */

(function ($) {
	// Tabs function
	$('ul.nav-tabs li').live("click", function(){
		var liIndex = $(this).index();
		var content = $(this).parent("ul").next().children("li").eq(liIndex);
		$(this).addClass('active').siblings("li").removeClass('active');
		$(content).show().addClass('active').siblings().hide().removeClass('active');
		$(this).parent("ul").find("input").val(0);
		$('input', this).val(1);
	});
	
	$(".gw-add-widget a").live('click', function(){
		var par = $(this).parents(".gw-container");
		$.post( ajaxurl, { action: doughnut.action, nonce : doughnut.nonce }, function(data){
			$(par).append(data).fadeIn(1000);
		}); 
		$(".gw-container").gwSortable();
		return false;		
	});

	$(".gw-delete").live('click', function(){
		$(this).parents('.gw-sortable').fadeTo(300, 0.00, function(){
            $(this).slideUp(500, function() {
                $(this).remove();
             });
         });
	});
	

	$.fn.gwSortable = function(){
		$(this).each(function(){
			$(this).sortable({
				items: '.gw-sortable', 
				placeholder: 'placeholder', 
				revert: true,
				start: function(event, ui) {
					$(".placeholder").width(ui.item.width());
					$(".placeholder").height(ui.item.height());
				}
			});
		});		
	}
	
	$.fn.totalAddImages = function(){
		$(this).click(function() {
			var imagesibling = $(this).siblings('img'),
			inputsibling = $(this).siblings('input'),
			buttonsibling = $(this).siblings('a');
			tb_show('Select Image/Icon Title', 'media-upload.php?post_id=0&type=image&TB_iframe=true');	
			window.send_to_editor = function(html) {
				var imgurl = $('img',html).attr('src');
				if ( imgurl === undefined || typeof( imgurl ) == "undefined" ) imgurl = $(html).attr('src');		
				imagesibling.attr("src", imgurl).slideDown();
				inputsibling.val(imgurl);
				buttonsibling.addClass("showRemove").removeClass("hideRemove");
				tb_remove();
			};
			return false;
		});
	}
	
	
	$.fn.totalRemoveImages = function(){
		$(this).click(function() {
			$(this).next().val('');
			$(this).siblings('img').slideUp();
			$(this).removeClass('show-remove').addClass('hide-remove');
			$(this).fadeOut();
			return false;
		});
	}
})(jQuery);