jQuery(document).ready(function(){
	var show_itemsOnPage = 8;
	//show_image('',1, show_itemsOnPage);
	jQuery('a.action_refresh').click(function(){ show_image('refresh',1, show_itemsOnPage); });
	jQuery('a#action_seach').click(function(){ ajax_search(); });
	jQuery('form#search-form').submit(function(){
		ajax_search();
		return false;
	})
	$.contextMenu({
        selector: '.thumbnail', 
        callback: function(key, options) {
            var m = "global: " + key;
            /*window.console && console.log(m) || alert(m); */
        },
        items: {
            "view": {
                name: "View", 
                icon: "edit", 
                // superseeds "global" callback
                callback: function(key, options) {
					jQuery(this).click();
                }
            },
            "delete": {name: "Delete", icon: "delete", callback: function(key, options) {
				if (confirm("Are you sure you want to delete image "+jQuery(this).attr('data-title')+"?")){                    
					delete_image(jQuery(this).attr('href'), jQuery(this));
				}
			}},
            "sep1": "---------",
            "quit": {name: "Hide Image", icon: "quit", callback: function(key, options) {
				jQuery(this).parent().parent().fadeOut();
			}}
        }
    });
})
/* FUNCTION */
function ajax_search(){
		var value = jQuery.trim(jQuery('form.search-form input[name=url]').val());
		var str_element = 'div.photo-gallery div div ul li.default_image';
		if(value != ''){
			$.ajax({  
				type: 'POST',  
				url: ajaxurl+ 'processdata.php',  
				data: {  
					action: 'search',
					keyword : value
				},  
				dataType: 'json',
				beforeSend: function() {
					jQuery('.search-form span._waiting').fadeIn();
					jQuery(str_element).fadeIn();
				},
				success: function(response, textStatus, XMLHttpRequest){ 
					jQuery('div.results').fadeIn();
					jQuery('form.search-form input[name=url]').val('');
					jQuery('.search-form span._waiting').fadeOut();
					
					if(response.status == 'true'){
						var html ='<li><span data-tooltip class="has-tip tip-top" title="'+response.name+'"><a data-title="'+response.name+'"  class="thumbnail" href="'+response.url+'"><img src="'+response.url+'"/></a></span></li>';
						jQuery(str_element).fadeOut().after(html);
					} else {
						jQuery(str_element).fadeOut();
					}
				},    
				error: function(MLHttpRequest, textStatus, errorThrown){  
		 
				}  
			});  
		} 
	}
	function show_image(type_action, _pageNumber, _itemsOnPage){
		$.ajax({  
				type: 'POST',  
				url: ajaxurl+ 'processdata.php',  
				data: {  
					action: 'show_images',
					keyword : '1',
					curentPage : _pageNumber,
					set_itemsOnPage : _itemsOnPage
				},  
				dataType: 'json',
				beforeSend: function() {
					jQuery('.search-form span._waiting').fadeIn();
				},
				success: function(response, textStatus, XMLHttpRequest){ 
					jQuery('.search-form span._waiting').fadeOut();
					if(response.status == 'true'){
						jQuery('div.results').fadeIn();
						jQuery('div.results div a').remove();
						var str_element = 'div.photo-gallery div div ul';
						if(type_action == 'refresh'){
							jQuery(str_element+ ' li').remove();
							jQuery(str_element).append('<li class="default_image" style="display:none;" ><a class="thumbnail" href="#default"></a></li>');
						}
						jQuery.each( response.data, function ( i, val ) {
							var src = val.dirname + '/' + val.basename;
							var html ='<li><span data-tooltip class="has-tip tip-top" title="'+val.filename+'"><a data-title="'+val.filename+'" class="thumbnail" href="'+src+'"><img src="'+src+'"/></a></span></li>';
							jQuery(str_element).append(html).fadeIn();
						});
						 $('#paginate-page').pagination({
							items: response.totalImage,
							itemsOnPage: _itemsOnPage,
							currentPage: _pageNumber,
							prevText : '&laquo;',
							nextText: '&raquo;',
							cssStyle: 'dark-theme',
							onPageClick: function(pageNumber, event) {
								show_image('refresh', pageNumber, _itemsOnPage);
							}
						});
					} 
				},    
				error: function(MLHttpRequest, textStatus, errorThrown){  
		 
				}  
			});  
			
	}
	
	function delete_image(str_url, element_obj){
		$.ajax({  
				type: 'POST',  
				url: ajaxurl+ 'processdata.php',  
				data: {  
					action: 'delete_image',
					src : str_url
				},  
				dataType: 'json',
				beforeSend: function() {
					jQuery('.search-form span._waiting').fadeIn();
				},
				success: function(response, textStatus, XMLHttpRequest){ 
					jQuery('.search-form span._waiting').fadeOut();
					if(response.status == 'true'){
						jQuery(element_obj).parent().parent().remove();
					} 
				},    
				error: function(MLHttpRequest, textStatus, errorThrown){  
		 
				}  
			});  
	}