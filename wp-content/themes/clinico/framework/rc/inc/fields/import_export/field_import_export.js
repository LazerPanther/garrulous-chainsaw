(function($) {
	"use strict";

function handleFileSelect(e) {
	var files = e.target.files; // FileList object
	var reader = new FileReader();
	reader.onload = function(event) {
		var content = event.target.result;
		document.getElementById('import-code-value').value = content;
		document.getElementById('redux-import-code-button').click();
	};
	reader.readAsText(files[0]);
}

document.getElementById('reduxbackupjson').addEventListener('change', handleFileSelect, false);

	$(document).ready(function() {
		$('#redux-import').click(function(e) {
			if ($('#import-code-value').val() === "" && $('#import-link-value').val() === "") {
				e.preventDefault();
				return false;
			}
		});

		$('#redux-import-code-button').click(function() {
			if ($('#redux-import-link-wrapper').is(':visible')) {
				$('#redux-import-link-wrapper').hide();
				$('#import-link-value').val('');
			}
			$('#redux-import-code-wrapper').fadeIn('fast');
		});

		$('#redux-import-file-button').click(function() {
			$("#reduxbackupjson").trigger('click');
		});

		$('#redux-import-link-button').click(function() {
			if ($('#redux-import-code-wrapper').is(':visible')) {
				$('#redux-import-code-wrapper').hide();
				$('#import-code-value').val('');
			}
			$('#redux-import-link-wrapper').fadeIn('fast');
		});

		$('#redux-export-code-copy').click(function() {
			if ($('#redux-export-link-value').is(':visible')) {
				$('#redux-export-link-value').hide();
			}
			$('#redux-export-code').fadeIn('fast');
		});

		$('#redux-export-link').click(function() {
			if ($('#redux-export-code').is(':visible')) {
				$('#redux-export-code').hide();
			}
			$('#redux-export-link-value').fadeIn('fast');
		});

	});
})(jQuery);
