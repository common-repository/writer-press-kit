/*
 * Javascript for Administration Screen
 *
 * @package WordPress
 * @subpackage Writer Press Kit Plugin
 * @author Jamel Cato
 * @since 1.0.0
 */

jQuery(document).ready(function($) {
	
$('.dashicons-arrow-up').hide();
$('#wpk-settings').find('.accordion-toggle').click(function() {
	
	/* Handle display of arrows */
	if($(this).next().is(':visible')) {
		$('.dashicons-arrow-up').hide();
		$('.dashicons-arrow-down').show();
	} else {
		$('.dashicons-arrow-up').not($(this).next()).hide();
		$(this).find('.dashicons-arrow-up:first').show();
		$('.dashicons-arrow-down').show();
		$(this).find('.dashicons-arrow-down:first').hide();
	}
	
	/* Handle display of content */
	$(this).next().slideToggle('fast');
	$(".accordion-content").not($(this).next()).slideUp('fast');
});

	
//----------
}); //end doc ready	