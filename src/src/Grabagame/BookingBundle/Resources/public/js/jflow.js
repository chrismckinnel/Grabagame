$(document).ready(function(){
	/*
	Here we create a content slider using the jquery jFlow plugin.
	Instructions are in the HELP file and also included in HTML comments below.
	Link to the jFlow plugin can be found in help file.
	*/
	$("#hidden-controller").jFlow({
		slides: "#header-slides",
		controller: ".jFlowControl", // must be class, use . sign
		slideWrapper : "#jFlowSlide", // must be id, use # sign
		selectedWrapper: "jFlowSelected",  // text, no sign
		width: "500px",
		height: "200px",
		duration: 400,
		prev: ".slides-arrow-left", // must be class, use . sign
		next: ".slides-arrow-right" // must be class, use . sign
	});
	// We do this so that on long loading pages (eg: google maps) the content doesn't overflow/flicker down
	// over the content.
	$('#header-slides-holder').css('visibility','visible');
});
