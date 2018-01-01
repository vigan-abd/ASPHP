(function( factory ) {
	if ( typeof define === "function" && define.amd ) {
		define( ["jquery", "../jquery.validate"], factory );
	} else if (typeof module === "object" && module.exports) {
		module.exports = factory( require( "jquery" ) );
	} else {
		factory( jQuery );
	}
}(function( $ ) {

/*
 * Translated default messages for the jQuery validation plugin.
 * Locale: AL (Albanian; Shqip)
 */
$.extend( $.validator.messages, {
	required: "Fusha duhet te plotesohet.",
	remote: "Ju lutemi rregullone fushen.",
	email: "Ju lutemi jepni email adres valide.",
	url: "Ju lutemi jepni web adrese (URL) valide.",
	date: "Ju lutemi jepni date valide .",
	dateISO: "Ju lutemi jepni date valide (Formati ISO).",
	number: "Ju lutemi jepni vlere numerike.",
	digits: "Ju lutemi jepni vlere numerike.",
	creditcard: "Ju lutemi jepni kredit kartele valide.",
	equalTo: "Ju lutemi jepni perseri vleren.",
	extension: "Ju lutemi jepni ekstension valid.",
	maxlength: $.validator.format( "Numri maksimal i karaktereve te lejuara eshte {0}." ),
	minlength: $.validator.format("Numri minimal i karaktereve te lejuara eshte {0}."),
	rangelength: $.validator.format( "Numri i karaktereve duhet te jete ne mes {0} dhe {1}." ),
	range: $.validator.format( "Ju lutemi jepni vlere ne mes te rangut {0}-{1}." ),
	max: $.validator.format( "Vlera maksimale e lejuar eshte {0}." ),
	min: $.validator.format("Vlera minimale e lejuar eshte {0}."),
	require_from_group: "Ju lutemi plotesone te pakten {0} prej fushave.",
	integer: "Ju lutemi jepni vlere numerike (pa pike)",
	ipv4: "Ju lutemi jepni IP v4 adrese valide.", 
	ipv6: "Ju lutemi jepni IP v6 adrese valide.", 
	lettersonly: "Ju lutemi jepni vetem shkronja.", 
	letterswithbasicpunc: "Ju lutemi jepni vetem shkronja dhe shenja te pikesimit.", 
	regex: "Ju lutemi shiqoni input e juaj edhe nje here." 
} );

}));