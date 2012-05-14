<?php
	
/**
 * @package WordPress
 * @subpackage Marble Solar
 * Template Name: Pg TPL - Roof Calculator
**/

get_header();

$assumptions = mss_get_all_settings();
$installationWidth = $assumptions['default_system_width'];
$installationHeight = $assumptions['default_system_height'];

/* Check to see if Postcode has been passed then clean it up and get Longitude and Latitude */
if(isset($_POST['submitpostcode']) || isset($_REQUEST['postcode'])){
	$postcode = $_REQUEST['postcode'];
	
	if(mss_isPostcode($_REQUEST['postcode'])){
				
		$trimPostcode = preg_replace("/\s+/","",$_REQUEST['postcode']);
		$longAndLat=file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$trimPostcode.',uk&sensor=false');
		$output = json_decode($longAndLat);
		
		if($output->status == "OK"){
		
			$defaultLatitude = $output->results[0]->geometry->location->lat;
			$defaultLongitude = $output->results[0]->geometry->location->lng;
		
		} else {
			$errors = "<li>You must enter a valid UK postcode.</li>";
		}
	} else {
		$errors = "<li>You must enter a valid UK postcode.</li>";
	}
}
?>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript" src="<?=bloginfo('template_url')?>/js/geo.js"></script>
<script type="text/javascript" src="<?=bloginfo('template_url')?>/js/calculatearea.js"></script>
<script type="text/javascript" src="<?=bloginfo('template_url')?>/js/polygon.js"></script>
<link rel="stylesheet" type="text/css" href="<?php bloginfo( 'template_url' ); ?>/css/polygonmap.css" />

<script type="text/javascript">
/*
function goToByScroll(id){
	jQuery('html,body').animate({
		scrollTop: jQuery("#"+id).offset().top
	},'slow');
}
*/

jQuery(document).ready(function() {
	//goToByScroll("findpropertyheader");
		
	jQuery(".estimatesubmit").attr("disabled", true);

	jQuery("#skiplink").click(function (e) {
		e.preventDefault();
		$("#skipstep").modal({
			opacity:80
		});		
	 });

});

jQuery(function(){
	//Set the default Map when no Postcode is defined
	<?php if(!empty($_REQUEST['postcode'])){?>
	//Zoom in and pinpoint the location based on Postcode given
		var thePoint=new google.maps.LatLng(<?=$defaultLatitude?>, <?=$defaultLongitude?>);
		var myOptions = {
			zoom: 19,
			center: thePoint,
			draggableCursor: 'crosshair',
			mapTypeId: google.maps.MapTypeId.SATELLITE
		}	
		map = new google.maps.Map(document.getElementById('main-map'), myOptions);
		var creator = new PolygonCreator(map);
	<?php } ?>
		 
	
	//Allow all points to be reset from the map
	jQuery('#reset').click(function(){ 
		creator.destroy();
		creator=null;
		
		map = new google.maps.Map(document.getElementById('main-map'), myOptions);
	
		creator=new PolygonCreator(map);
		jQuery("#roofheight").text("N/A");
		jQuery("#rooflength").text("N/A");
		jQuery("#roofarea").text("");
		jQuery("#roofangle").text("N/A");
		jQuery("#panelcalculator").remove();
		jQuery("#hiddenheight").remove();
		jQuery("#hiddenlength").remove();
		jQuery(".estimatesubmit").attr("disabled", true);

		jQuery("#calculatorvisual_1").show();
		jQuery("#calculatorvisual_2").hide();
		jQuery("#calculatorvisual_3").hide();
		jQuery("#calculatorstep_2").addClass("stepcomplete");
		jQuery("#calculatorstep_1").removeClass("stepawaiting stepcomplete");
		jQuery("#calculatorstep_2").removeClass("stepcomplete");
		jQuery("#calculatorstep_3").removeClass("stepcomplete");
		jQuery("#calculatorstep_2").addClass("stepawaiting");
		jQuery("#calculatorstep_3").addClass("stepawaiting");		

	});
});

</script>

<div class="container">

<?php if(!$_REQUEST['postcode'] || $errors) { ?>

<?php ds_output_stepper(1); ?>

<?php if($errors) { message_box($errors); } ?>

<section id="find-property">
	<div class="column twelve">
		<form method="post">
			<div class="roof_postcode">
				<div class="findproperty">
					<h2>Find Your Property</h2>
					<p class="larger">Get started using our tool by first finding your property.</p>

					<p>Simply enter your postcode and click search to begin.</p>
				</div>
				<div class="roofpostcodecontainer">
					<div class="searchinput">
						<input tabindex="1" type="text" class="alt_homepage_input_black" maxlength="8" name="postcode" value="<?=$postcode?>">
					</div>		
					<div class="searchbutton">
						<input type="submit" value="Search" tabindex="2" class="searchsubmit" name="submitpostcode">
					</div>		
				</div>
			</div>
		</form>
	</div>
</section>


<div class="row">
	<img src="http://www.discoversolar.co.uk/wp-content/themes/discoversolar/assets/images/content/default_map.jpg" width="960px">
</div>


<?php } else { ?>

<?php ds_output_stepper(2); ?>

<p class="larger">We have centred our map on <strong><?=strtoupper($postcode)?></strong> <a href="<?=bloginfo('url')?>/roof-calculator">(edit)</a>. Follow the instructions below to estimate the size of your roof. If you make a mistake at any time click the reset tool button. When you’re ready to proceed click the estimate income button. If you already know the size of your roof click <a id="skiplink" href="#">here</a> to skip this step.
</p>
<section id="how-to-use-roof-calc">
	<h3>How to use the roof calculator</h3>
	<div class="overflow">
		<div class="calculatorvisual">
			<div class="calc_visual_1" id="calculatorvisual_1"><img src="<?=bloginfo('template_url')?>/images/map_move.jpg"></div>
			<div class="calc_visual_2" id="calculatorvisual_2"><img src="<?=bloginfo('template_url')?>/images/calculator_instructions.gif"></div>
			<div class="calc_visual_3" id="calculatorvisual_3"><img src="<?=bloginfo('template_url')?>/images/angle_instructions.gif"></div>
		</div>
		<div class="calculatorticks">
			<div class="tick1"></div>
			<div class="tick2"></div>
			<div class="tick3"></div>
		</div>
		<div class="calculatorinstructions">
			<div class="calculatorstep" id="calculatorstep_1">Click and drag the map below to centre on your roof, then zoom in as necessary</div>
			<div class="calculatorstep stepawaiting" id="calculatorstep_2">Click on the four corners of your roof and then close the box by clicking on the first corner again</div>
			<div class="calculatorstep stepawaiting" id="calculatorstep_3">Drag the compass pin so the green line points in the direction your roof is facing</div>
		</div>
	</div>
</div>
<div class="row">
	<div id="main-map"></div>
</div>
<form method="post" action="<?=bloginfo('url')?>/results">
	<div class="container calc-info">
		<div class="column four"><span class="grey">Roof Area:</span> <strong><span id="roofheight">N/A</span> x <span id="rooflength">N/A</span> <span id="roofarea"></span></strong></div>
		<div class="column four"><span class="grey">Direction:</span> <strong id="roofangle">N/A</strong></div>
		<div class="column two" style="width:125px;"><input id="reset" type="button" value="Reset Tool" tabindex="1" class="resetsubmit"></div>
		<div class="column two" id="buttons"><input name="get_results" value="Estimate Income" type="submit" class="estimatesubmit"/><input type="hidden" name="postcode" value="<?=$postcode?>"><input type="hidden" id="polygonpoints" name="polygonpoints" value=""></div>
	</div>
</form>

<div id="skipstep">
	<h2 class="black">Roof Dimensions</h2>
	<p>If you already know the size of your roof enter the dimensions below.</p>

	<div class="roofdimensionscontainer">
		<form method="post" action="<?=bloginfo('url')?>/results">
			<input type="text" name="width" class="skip_step_dimension_input" value="<?=$installationWidth?>"><div class="left paddthex">&nbsp;x&nbsp;</div><input type="text" name="height" class="skip_step_dimension_input" value="<?=$installationHeight?>">
			<select name="dimensionType" class="dimensionSelect">
				<option value="m" <?php if($dimensionType == "m" || empty($dimensionType)){ ?>selected="selected"<?php } ?>>Metres</option>
				<option value="f" <?php if($dimensionType == "f"){ ?>selected="selected"<?php } ?>>Feet</option>
				<option value="y" <?php if($dimensionType == "y"){ ?>selected="selected"<?php } ?>>Yards</option>
				<option value="cm" <?php if($dimensionType == "cm"){ ?>selected="selected"<?php } ?>>CM</option>
				<option value="mm" <?php if($dimensionType == "mm"){ ?>selected="selected"<?php } ?>>MM</option>
			</select>
			<input type="submit" value="Calculate" tabindex="2" class="center calculatewhite margintop20" name="get_results">
			<input type="hidden" name="postcode" value="<?=$postcode?>">
		</form>
	</div>
</div>
<?php } ?>
</div>

</div>

<!-- Google Code for Income Estimator Conversion Page -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 1015361646;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "rjuRCOKdngIQ7uCU5AM";
var google_conversion_value = 0;
/* ]]> */
</script>
<script type="text/javascript" src="http://www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="http://www.googleadservices.com/pagead/conversion/1015361646/?label=rjuRCOKdngIQ7uCU5AM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>

<?php get_footer(); ?>