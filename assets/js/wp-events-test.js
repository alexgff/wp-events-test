/**
 * WP Events Test
 * Interface JS functions
 *
 * @since 1.0.0
 *
 * @package WP Events Test.
 */
/*jslint browser: true*/
/*global jQuery, console*/
(function($) {
    "use strict";
	var api = {
		init: function(args) {
			api.attachListener();
		},
		attachListener: function() {
			$(document).on('click', '.wp-events-test-nav', function(){
				var order = {};
				order['action'] = 'get-calendar';
				order['get-month'] = $(this).data('get-month');
				api.ajax(order);
			});
		},
		beforeSend: function(order) {
			$('.wp-events-test-spinner').css({'display':'block'});
		},
		ajax: function(order) {
			$.ajax({type:'POST', url:WPEventsTest.ajaxurl, beforeSend: api.beforeSend(order), data:{action:WPEventsTest.process_ajax, order:order}, dataType:'json'})
			.done(function(response) {
				if ( response.success  ) {
					$('.wp-events-test-calendar_wrap').html(response.data.calendar);
				}	
			})
			.fail(function (error){})
			.always(function (jqXHR, status){});		
		},	

	};
	
	WPEventsTest = $.extend({}, WPEventsTest, api);
	WPEventsTest.init();
})(jQuery);