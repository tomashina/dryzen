/* REPLACE SUMMERNOTE IN OC2.2 WITH CKEDITOR BY CL!CKER */

//function replace_summernote_22() {
$(document).ready(function() {
	//setTimeout(function() {
	//console.log($('.summernote, [data-toggle=\'summernote\']'));
	$('.summernote, [data-toggle=\'summernote\']').each(function(idx, holder) {
		var ck_element = this;

		$(ck_element).summernote({
			disableDragAndDrop: true,
			// height: 300
		});
		//console.log($(ck_element).summernote('code'));
	});
	//}, 10);
});
//}