var Guestbook = {
	addSmiley : function(smiley, fieldId) {
		var field = document.getElementById(fieldId);
		var doc_content = field.value + smiley;
		field.value = doc_content;
		field.focus();
	}
};

$('#guestbookFormPanel').on('click', function() {
	console.log("test");
})