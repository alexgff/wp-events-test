/**
 * WP Events Test Admin.
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
		init: function() {
			$( '#'+WPEventsTestAdmin.datePicker).datepicker({
			  dateFormat: "yy-mm-dd",
			  showOtherMonths: true,
			  selectOtherMonths: true
			});
		}
	};
	
	WPEventsTestAdmin = $.extend({}, WPEventsTestAdmin, api);
	WPEventsTestAdmin.init();
})(jQuery);