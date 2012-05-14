/* Author: David Heward
*/

demo={
	
	init: function(){
		if(jQuery("#skiplink").length>0){
		jQuery("#skiplink").click(function (e) {
			e.preventDefault();
			$("#skipstep").modal({
				opacity: 80
			});		
		 });
		}		
	}
	
}

$(document).ready(function(){
	demo.init();
});























