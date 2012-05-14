<?php
	
/**
 * @package WordPress
 * @subpackage Marble Solar
 * Template Name: Pg TPL - Results
*/

/*
 * Check for logged in user and check submitted postcode matches
 *
 * @since v1.1 (Discover branch)
 */

if(function_exists('mss_userCheck'))
	mss_userCheck();

/* Set the defaults before they submit */
$assumptions = mss_get_all_settings();

$installationWidth = $assumptions['default_system_width'];
$installationHeight = $assumptions['default_system_height'];
$electricityRate = $assumptions['daytime_electricity_rate'];
$energyUsage = $assumptions['use_in_home'];
$years = $assumptions['payback_period'];
$inflation = $assumptions['inflation'];
$rpi = $assumptions['rpi'];
$roofAngle = $assumptions['roof_angle'];
$overShading = $assumptions['deafult_over_shading'];
$degradeRate = $assumptions['panel_degrade_rate'];
$largeInstallation = 0;
$exportDefault = 1;
$sapCheck = 1;

setlocale(LC_MONETARY, 'en_GB');

if(isset($_POST['get_results'])){
	if(mss_isPostcode($_POST['postcode'])){
				
		if(is_numeric($_POST['quoteid'])){
			$table = $wpdb->prefix."quotes";
			$prevQuote = $wpdb->get_row("SELECT * from $table where id='{$_POST['quoteid']}'", ARRAY_A);
						
			$errors = 0;
			$dimensionsArray = array('m','y','f','cm','mm');
			if(!in_array($_POST['dimensionType'],$dimensionsArray)){
				$errors++;
				$error.= "<li>You must select a valid roof dimension type.</li>";
				$_POST['dimensionType'] = $prevQuote['dimensiontype'];			
			}
			if(!is_numeric($_POST['height'])){
				$errors++;
				$error.= "<li>You must enter a valid roof height.</li>";
				$oldHeight = explode("-",$prevQuote['roofdimensions']);
				$finalHeight = mss_convertMeasurement($oldHeight[0],$_POST['dimensionType']);
				$_POST['height'] = $finalHeight;
			}
			if(!is_numeric($_POST['width'])){
				$errors++;
				$error.= "<li>You must enter a valid roof width.</li>";
				$oldWidth = explode("-",$prevQuote['roofdimensions']);
				$finalWidth = mss_convertMeasurement($oldWidth[1],$_POST['dimensionType']);
				$_POST['width'] = $finalWidth;
			}
			$mmDimensions = mss_convertDimensions($_POST['height'],$_POST['width'],$_POST['dimensionType']);
			if($mmDimensions['height'] < 2000 && $mmDimensions['width'] < 2000){
				$errors++;
				$error.= "<li>Your roof size is too small to have Solar Panels fitted.</li>";
				$dimensionsFull = explode("-",$prevQuote['roofdimensions']);
				$finalHeight = mss_convertMeasurement($dimensionsFull[0],$prevQuote['dimensiontype']);
				$finalWidth = mss_convertMeasurement($dimensionsFull[1],$prevQuote['dimensiontype']);
				$_POST['width'] = $finalWidth;
				$_POST['height'] = $finalHeight;
			}
			$roofDirectionArray = array('n','ne','nw','e','w','se','sw','s');
			if(!in_array($_POST['roofFaces'],$roofDirectionArray)){
				$errors++;
				$error.= "<li>You must select a valid roof facing direction.</li>";
				$_POST['roofFaces'] = round($prevQuote['rooffaces']);
			}			
			if(!is_numeric($_POST['roofAngle']) || $_POST['roofAngle'] < 0 || $_POST['roofAngle'] > 90){
				$errors++;
				$error.= "<li>You must enter a valid roof angle between 0&deg; and 90&deg;.</li>";
				$_POST['roofAngle'] = round($prevQuote['roofangle']);
			}
			if(!is_numeric($_POST['sapData']) && ($_POST['sapData'] != 1 || $_POST['sapData'] != 2)){
				$errors++;
				$error.= "<li>You must select which data set you wish to use.</li>";
				$_POST['sapData'] = $prevQuote['datatype'];
			}
			if(!is_numeric($_POST['electricyRate'])){
				$errors++;
				$error.= "<li>You must enter a valid electricity rate.</li>";
				$_POST['electricyRate'] = $prevQuote['energyrate'];
			}
			if(!is_numeric($_POST['degradeRate'])){
				$errors++;
				$error.= "<li>You must enter a valid rate of panel degradation.</li>";
				$_POST['degradeRate'] = $prevQuote['degradeRate'];
			}
			if($_POST['exportType'] == 2 && !is_numeric($_POST['exportPrice'])){
				$errors++;
				$error.= "<li>You must enter a valid export price.</li>";
				$_POST['exportPrice'] = $prevQuote['exportprice'];
			}
			if(!is_numeric($_POST['energyToUse']) || $_POST['energyToUse'] < 0 || $_POST['energyToUse'] > 100){
				$errors++;
				$error.= "<li>You must enter a valid energy usage between 0% and 100%.</li>";
				$_POST['energyToUse'] = round($prevQuote['energyusage']);
			}
			if(!is_numeric($_POST['benefitPeriod']) || $_POST['benefitPeriod'] < 11 || $_POST['benefitPeriod'] > 25){
				$errors++;
				$error.= "<li>You must enter a valid benefit period between 10 and 25 years.</li>";
				$_POST['benefitPeriod'] = $prevQuote['years'];
			}
			if(!is_numeric($_POST['rpi'])){
				$errors++;
				$error.= "<li>You must enter a valid RPI percentage.</li>";
				$_POST['rpi'] = $prevQuote['rpi'];
			}
			if(!is_numeric($_POST['priceInflation'])){
				$errors++;
				$error.= "<li>You must enter a valid price inflation percentage.</li>";
				$_POST['priceInflation'] = round($prevQuote['inflation']);
			}
			if(!is_numeric($_POST['overShading'])){
				$errors++;
				$error.= "<li>Over shading must be a value between 1 and 10.</li>";
				$_POST['overShading'] = round($prevQuote['overshading']);
			}
		}
		
		/* Gets longitude and latitude of given Postcode */
		$trimPostcode = preg_replace("/\s+/","",$_POST['postcode']);
		$longAndLat=file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$trimPostcode.',uk&sensor=false');
		$output = json_decode($longAndLat);
		
		if($output->status != "OK"){
			$message = "<li>You must enter a valid UK postcode.</li>";
		}
		
		if($_POST['givenlat'] && $_POST['givenlong']){
			$givenLat = $_POST['givenlat'];
			$givenLong = $_POST['givenlong'];
			
			$defaultLatitude = $givenLat;
			$defaultLongitude = $givenLong;
		} else {
			$defaultLatitude = $output->results[0]->geometry->location->lat;
			$defaultLongitude = $output->results[0]->geometry->location->lng;
		}
				
		$address1 = $output->results[0]->address_components[1]->long_name;
		$address2 = $output->results[0]->address_components[2]->long_name;
		$address3 = $output->results[0]->address_components[3]->long_name;
		
		if($address3 == "United Kingdom"){ $address3 = ""; }
					
		/* Takes all the inputs or takes default values from the database */
		$post_code = $_POST['postcode'];
		$longitude = isset($_POST['longitude']) && !empty($_POST['longitude']) ? round($_POST['longitude']) : round($defaultLongitude);
		$latitude = isset($_POST['latitude']) && !empty($_POST['latitude']) ? round($_POST['latitude']) : round($defaultLatitude);
		$fullLongitude = isset($_POST['longitude']) ? $_POST['longitude'] : $defaultLongitude;
		$fullLatitude = isset($_POST['latitude']) ? $_POST['latitude'] : $defaultLatitude;		
		$installationHeight = isset($_POST['height']) && $_POST['height'] != "" ? $_POST['height'] : $assumptions['default_system_height'];
		$installationWidth = isset($_POST['width']) && $_POST['width'] != "" ? $_POST['width'] : $assumptions['default_system_width'];
		$dimensionType = isset($_POST['dimensionType']) ? $_POST['dimensionType'] : $assumptions['default_system_measurement'];
		$energyUsage = isset($_POST['energyToUse']) ? $_POST['energyToUse'] : $assumptions['use_in_home'];
		$electricityRate = isset($_POST['electricyRate']) ? $_POST['electricyRate'] : $assumptions['daytime_electricity_rate'];
		$years = isset($_POST['benefitPeriod']) ? $_POST['benefitPeriod'] : $assumptions['payback_period'];
		$rpi = isset($_POST['rpi']) ? $_POST['rpi'] : $assumptions['rpi'];
		$inflation = isset($_POST['priceInflation']) ? $_POST['priceInflation'] : $assumptions['inflation'];
		$roofAngle = isset($_POST['roofAngle']) ? $_POST['roofAngle'] : $assumptions['roof_angle'];
		$roofFaces = isset($_POST['roofFaces']) ? $_POST['roofFaces'] : $assumptions['default_roof_direction'];
		$largeInstallation = isset($_POST['largeInstallation']) ? $_POST['largeInstallation'] : 0;
		$exportType = isset($_POST['exportType']) ? $_POST['exportType'] : 1;
		$overShading = isset($_POST['overShading']) ? $_POST['overShading'] : 1;
		$roofDirection = mss_calc_RoofDirection($roofFaces);
		$sapData = isset($_POST['sapData']) ? $_POST['sapData'] : 1;
		if($sapData == 1){ $sapCheck = 1;$sapData = true; } elseif($sapData == 2){ $sapCheck = 2;$sapData = false; }
		$degradeRate = isset($_POST['degradeRate']) ? $_POST['degradeRate'] : $assumptions['panel_degrade_rate'];
		$polygonpoints = $_POST['polygonpoints'];
		
		if($exportType == 2 && !empty($_POST['exportPrice'])){
			$exportPrice = $_POST['exportPrice'];
			$exportDefault = 2;
			$exportField = $exportPrice;
		} else {
			$_POST['exportPrice'] = "";
			$exportPrice = $assumptions['export_tariff'];
			$exportDefault = 1;
			$exportField = "";
		}
	
		/* Works out basic information given inputs */
		$roofDimensions = mss_convertDimensions($installationHeight,$installationWidth,$dimensionType);
		$roofArea = mss_convertArea($roofDimensions);
		$averagePower = mss_averagePower($longitude,$latitude,$roofArea,$roofAngle,$roofFaces,$sapData);
		if(!$sapData){
			$nasaEstimate = mss_nasaAverage($longitude,$latitude);
		}
										
		/* Stores settings into an array */
		$allSettings = array();
		$allSettings['longitude']=$longitude;
		$allSettings['latitude']=$latitude;
		$allSettings['fullLongitude']=$fullLongitude;
		$allSettings['fullLatitude']=$fullLatitude;
		$allSettings['postcode']=$post_code;
		$allSettings['roofdimensions']=$roofDimensions;
		$allSettings['roofarea']=$roofArea;
		$allSettings['averagepower']=$averagePower;
		$allSettings['nasaestimate']=$nasaEstimate;
		$allSettings['largeinstallation']=$largeInstallation;
		$allSettings['dimensiontype']=$dimensionType;
		$allSettings['polygonpoints']=$polygonpoints;

		/* Stores default settings into an array */
		$defaultSettings = array();
		$defaultSettings['years']=$years;
		$defaultSettings['rpi']=$rpi;
		$defaultSettings['inflation']=$inflation;
		$defaultSettings['exportprice']=$exportPrice;
		$defaultSettings['energyusage']=$energyUsage;
		$defaultSettings['energyrate']=$electricityRate;
		$defaultSettings['rooffaces']=$roofFaces;
		$defaultSettings['roofangle']=$roofAngle;
		$defaultSettings['overshading']=mss_sapShading($overShading);
		$defaultSettings['solarradiation']=mss_sapAverage($roofAngle,$roofFaces);
		$defaultSettings['overshading_value']=$overShading;
		$defaultSettings['degraderate']=$degradeRate;
		
		
		/* Returns all panels in an array */
		$allPanels = mss_getAllPanels();

		/* Loop round each panel producing the cost and information over first year */
		foreach($allPanels as $k => $v){
			$panelDimensions = mss_panelDimensions($v['settings']['model_dimensions']);
			$thePanels = mss_panelCalculator($panelDimensions,$roofDimensions);
														
			/* Check at least one panel fits in the given dimensions else exclude it */
			if($thePanels['landscape']['panels'] != 0 || $thePanels['portrait']['panels'] != 0){
				$panelCosts = mss_panelCosts($v,$thePanels,$allSettings,$nasaEstimate,$largeInstallation,$roofFaces,$roofAngle,$sapData,$overShading);
				$allPanels[$k]['panels']=$panelCosts;
			}
		}
				
		/* First Year Amounts */
		$firstYear[] = mss_firstYear($allPanels,$exportPrice,$energyUsage,$electricityRate);

		/* Produce overall profit for each of the panel in the array */
		$overallProfit = mss_overallProfit($allPanels,$defaultSettings);
		
		/* Insert the information into the database under the quote number */
		$quoteID = mss_insertPanels($allPanels,$allSettings,$firstYear,$overallProfit,$defaultSettings,$sapData);
		
		/*** Start of displaying Results ***/
		
		/* Get Optimum Landscape */
		$optimumLandscape = mss_getOptimumLandscape($quoteID);
				
		/* Get Optimum Portrait */
		$optimumPortrait = mss_getOptimumPortrait($quoteID);
		
		$countCapped = mss_countCapped($optimumLandscape,$optimumPortrait);
				
		$optimumLandscape = mss_formatOutput($optimumLandscape);
		$optimumPortrait = mss_formatOutput($optimumPortrait);
				
		$formattedBothOptimums = mss_produceBothOptimumOutput($optimumLandscape,$optimumPortrait);
		if(!empty($overallProfit)){
			$outputBothOptimums = mss_outputBothOptimumOutput($optimumLandscape,$optimumPortrait);
		}
		
		// Generate an auth code and update the quote row.
		$auth_code = mss_generate_auth_code();
		mss_update_auth_code($quoteID,$auth_code);
		
		//Send email to Marble Solar
		mss_send_quote_email($quoteID,$post_code,$auth_code);

	} else {
		$message.= "<li>You must enter a valid UK postcode.</li>";
	}
}

if(isset($_GET['quoteID']) && is_numeric($_GET['quoteID'])){
		
	$quoteID = $_GET['quoteID'];
	$auth_code = $_REQUEST['auth'];
	
	$table = mss_table("quotes");
	$quoteDetails = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %s",array($quoteID)), ARRAY_A);
	
	if($quoteDetails){
		
		// Validate the auth code.
		if(strlen($auth_code)==20){
		if(urldecode($auth_code) == $quoteDetails['auth_code']){
	
		$validGet = 1;
	
		/* Gets longitude and latitude of given Postcode */
		$trimPostcode = preg_replace("/\s+/","",$quoteDetails['postcode']);
		$longAndLat=file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$trimPostcode.'&sensor=false');
		$output = json_decode($longAndLat);
		
		$defaultLatitude = $output->results[0]->geometry->location->lat;
		$defaultLongitude = $output->results[0]->geometry->location->lng;
		$address1 = $output->results[0]->address_components[1]->long_name;
		$address2 = $output->results[0]->address_components[2]->long_name;
		$address3 = $output->results[0]->address_components[3]->long_name;
		
		if($address3 == "United Kingdom"){ $address3 = ""; }

		$largeInstallation = $quoteDetails['dno'];
		$sapData = $quoteDetails['datatype'];
		if($sapData == 1){ $sapCheck = 1;$sapData = true; } elseif($sapData == 2 || $sapData == 0){ $sapCheck = 2;$sapData = false; }
		$roofFaces = $quoteDetails['rooffaces'];
		$roofAngle = $quoteDetails['roofangle'];
		$exportPrice = $quoteDetails['exportprice'];
		$energyUsage = $quoteDetails['energyusage'];
		$electricityRate = $quoteDetails['energyrate'];
		$dimensionType = $quoteDetails['dimensiontype'];
		$overShading = $quoteDetails['overshading_value'];
		$roofDirection = mss_calc_RoofDirection($quoteDetails['rooffaces']);
		$post_code = $quoteDetails['postcode'];
		$years = $quoteDetails['years'];
		$rpi = $quoteDetails['rpi'];
		$inflation = $quoteDetails['inflation'];
		$degradeRate = $quoteDetails['degraderate'];

		if($exportPrice != $assumptions['export_tariff']){
			$exportDefault = 2;
			$exportField = $exportPrice;
		}
				
		/* Stores default settings into an array */
		$defaultSettings = array();
		$defaultSettings['years']=$quoteDetails['years'];
		$defaultSettings['rpi']=$quoteDetails['rpi'];
		$defaultSettings['inflation']=$quoteDetails['inflation'];
		$defaultSettings['exportprice']=$quoteDetails['exportprice'];
		$defaultSettings['energyusage']=$quoteDetails['energyusage'];
		$defaultSettings['energyrate']=$quoteDetails['electricityrate'];
		$defaultSettings['rooffaces']=$quoteDetails['rooffaces'];
		$defaultSettings['roofangle']=$quoteDetails['roofangle'];
		$defaultSettings['overshading']=mss_sapShading($quoteDetails['overshading_value']);
		$defaultSettings['solarradiation']=mss_sapAverage($roofAngle,$roofFaces);
		$defaultSettings['degraderate']=$quoteDetails['degraderate'];
		
		/* Stores settings into an array */
		$allSettings = array();
		$allSettings['longitude']=$quoteDetails['longitude'];
		$allSettings['latitude']=$quoteDetails['latitude'];
		$allSettings['fullLongitude']=$quoteDetails['fulllongitude'];
		$allSettings['fullLatitude']=$quoteDetails['fulllatitude'];
		$allSettings['postcode']=$quoteDetails['postcode'];
		$allSettings['roofarea']=$quoteDetails['roofarea'];
		$allSettings['largeinstallation']=$largeInstallation;
		$allSettings['dimensiontype']=$quoteDetails['dimensiontype'];
		
		$roofSize = explode('-',$quoteDetails['roofdimensions']);
		$installationHeight = mss_convertMeasurement($roofSize[0],$quoteDetails['dimensiontype']);
		$installationWidth = mss_convertMeasurement($roofSize[1],$quoteDetails['dimensiontype']);
		
		/* Works out basic information given inputs */
		$roofDimensions = mss_convertDimensions($roofSize[0],$roofSize[1],'mm');
		$roofArea = mss_convertArea($roofDimensions);
		$averagePower = mss_averagePower($quoteDetails['longitude'],$quoteDetails['latitude'],$quoteDetails['roofarea'],$quoteDetails['roofangle'],$quoteDetails['rooffaces'], $quoteDetails['datatype']);
		if(!$quoteDetails['datatype']){
			$nasaEstimate = mss_nasaAverage($quoteDetails['longitude'],$quoteDetails['latitude']);
		}
	
		$allSettings['roofdimensions']=$roofDimensions;
		$allSettings['averagepower']=$averagePower;
		$allSettings['nasaestimate']=$nasaEstimate;
		
		/* Returns all panels in an array */
		$allPanels = mss_getAllPanels();
							
		/* Loop round each panel producing the cost and information over first year */
		foreach($allPanels as $k => $v){
			$panelDimensions = mss_panelDimensions($v['settings']['model_dimensions']);
			$thePanels = mss_panelCalculator($panelDimensions,$roofDimensions);
									
			/* Check at least one panel fits in the given dimensions else exclude it */
			if($thePanels['landscape']['panels'] != 0 || $thePanels['portrait']['panels'] != 0){
				$panelCosts = mss_panelCosts($v,$thePanels,$allSettings,$nasaEstimate,$largeInstallation,$roofFaces,$roofAngle,$sapData,$overShading);
				$allPanels[$k]['panels']=$panelCosts;
			}
		}
	
		/* Produce overall profit for each of the panel in the array */
		$overallProfit = mss_overallProfit($allPanels,$defaultSettings);
								
		/*** Start of displaying Results ***/
		
		/* Get Optimum Landscape */
		$optimumLandscape = mss_getOptimumLandscape($quoteID);
				
		/* Get Optimum Portrait */
		$optimumPortrait = mss_getOptimumPortrait($quoteID);
		
		$countCapped = mss_countCapped($optimumLandscape,$optimumPortrait);
				
		$optimumLandscape = mss_formatOutput($optimumLandscape);
		$optimumPortrait = mss_formatOutput($optimumPortrait);
				
		$formattedBothOptimums = mss_produceBothOptimumOutput($optimumLandscape,$optimumPortrait);
		if(!empty($overallProfit)){
			$outputBothOptimums = mss_outputBothOptimumOutput($optimumLandscape,$optimumPortrait);
		}
			
		
		}else{
			wp_redirect(get_bloginfo('url'));exit;
		} // End of validation 
		
		}else{
			wp_redirect(get_bloginfo('url'));exit;
		} // End of auth code length test
		
	}
}

$api = mss_get_plugin_setting('google_api_key');

get_header();
?>
<div class="page-content">

<?php if(!$_POST['postcode'] && !$validGet || $message) { ?>
<?php ds_output_stepper(1); ?>

<div class="container">
	<div class="column twelve">		
		<h2 class="status-title">Help us find your property</h2>
		<p class="larger margintop15">Enter your postcode below to calculate how much you could make from installing Solar Panels on your roof.<br>Unsure about the size of your roof? use our <a href="roof-calculator">Roof Calculator</a>.</p>
		<?php if($message) { message_box($message); } ?>
		<div class="yourpostcode">
			<div class="houseimage house_alt"><img src="<?=bloginfo('stylesheet_directory')?>/images/your_house.jpg"></div>
			<form method="post">
				<div class="postcodeinfo">
					<div class="row padbottom5">
						<div class="span3"><h3>Post Code:</h3></div>
						<div class="span4"><input type="text" class="postcode_input" maxlength="8" name="postcode" value=""></div>
					</div>
					<div class="row">
						<div class="span3"><h3>Roof Dimensions:</h3></div>
						<div class="span5"><input type="text" name="width" class="dimension_input" value="<?=$installationWidth?>"><div class="left padtop5">&nbsp;x&nbsp;</div><input type="text" name="height" class="dimension_input" value="<?=$installationHeight?>">
						<select name="dimensionType" class="resultsdimensionSelect">
							<option value="m" <?php if($dimensionType == "m" || empty($dimensionType)){ ?>selected="selected"<?php } ?>>Metres</option>
							<option value="f" <?php if($dimensionType == "f"){ ?>selected="selected"<?php } ?>>Feet</option>
							<option value="y" <?php if($dimensionType == "y"){ ?>selected="selected"<?php } ?>>Yards</option>
							<option value="cm" <?php if($dimensionType == "cm"){ ?>selected="selected"<?php } ?>>CM</option>
							<option value="mm" <?php if($dimensionType == "mm"){ ?>selected="selected"<?php } ?>>MM</option>
						</select></div>
					</div>
				
						<input type="submit" value="Calculate" tabindex="2" class="calculatesubmit" name="get_results">
					
				</div>
			</form>
		</div>
	</div>
	<div class="column four push-1">
		<input class="large" style="display:none;" id="postcode" name="postcode" size="30" type="text" value="<?=$_POST['postcode']?>">
		<div id="map"></div>
	</div>		
</div>


<?php } else { ?>
		
<?php ds_output_stepper(3,$post_code); ?>

	<div class="container">
	<div class="column seven">	
<!--
		<div class="askForDetails">
			<h1>Get your Solar Panel Quote...</h1>
			
			<div class="overflow">
				<div id="themessage"></div>
				<div class="left_getquote">
					<p>In order to ensure your quote is accurate please fill out the required information below.</p>
					<form id="quoteform">
						<div id="getaquoteform">
						<div class="form-row">
							<label class="float">Title: *</label>
							<select name="salutation" id="title" class="dropdown">
								<option value="Mr">Mr</option>
								<option value="Mrs">Mrs</option>
								<option value="Miss">Miss</option>
								<option value="Ms">Ms</option>
								<option value="Dr">Dr</option>
							</select>						
						</div>
						<div class="form-row">	
							<label class="float">First name: *</label>
							<input id="fname" type="text" name="firstName">
						</div>
						<div class="form-row">	
							<label class="float">Last name: *</label>
							<input type="text" name="lastName">
						</div>
						<div class="form-row">	
							<label class="float">Email: *</label>
							<input type="text" name="emailAddress">
						</div>
						<div class="form-row" id="housesection">
							<label class="float">House no. or name: *</label>
							<input type="text" id="house" name="houseno">
						</div>
						<div class="form-row" id="housesection">
							<label class="float">Address: *</label>
							<input type="text" id="address_1" name="firstLine">
						</div>
						<div class="form-row" id="housesection">
							<label class="float">&nbsp;</label>
							<input type="text" id="address_2" name="secondLine">
						</div>
						<div class="form-row" id="housesection">
							<label class="float">Town:  *</label>
							<input type="text" id="address_3" name="town">
						</div>
						<div class="form-row" id="housesection">
							<label class="float">County: *</label>
							<input type="text" id="address_4" name="county">
						</div>
						<div class="form-row" id="postcodesection">
							<label class="float">Postcode: *</label>
							<input type="text" id="postcode" name="postcode" value="<?=$post_code?>">
						</div>
						<div class="form-row">
							<label class="phoneLabel float">Phone: *</label>
							<input type="text" name="telephone">
						</div>
						<div class="form-row">
							<input type="submit" id="submit" name="getquote" value="Get a Quote">
							<input type="hidden" name="quoteID" value="<?=$quoteID?>">
						</div>
						<div class="requiredfields">* denotes required field</div>
						</div>
					</form>
				</div>
				<div class="right_getquote">
					<h3 class="solar_icon">About my Solar Panel quote</h3>
					<ul class="solar_advantages">
						<li><strong>Easy to use</strong>, accurate and customisable allowing you to configure your installation to suit you.</li>
						<li>All our quotes are <strong>100% free</strong>!</li>
						<li>See the breakdown of costs and revenue over your chosen duration (e.g. 25 years) to see your <strong>return on investment</strong>.</li>
					</ul>			


					<h3 class="house_icon">Why use Discover Solar?</h3>
					<ul class="solar_advantages">
						<li>Discover Solar is the smarter way to find trustworthy <strong>solar installers</strong> operating in your area.</li>
						<li>It's <strong>free and easy</strong> - tell us what you need and we'll find up to 3 approved solar specialists on your behalf. No-fee, no-obligation and no-hassle.</li>
						<li>Only <strong>MCS certified companies</strong> are permitted to quote for Solar PV enquiries to ensure you can benefit from the Government’s Feed-in Tariff.</li>
						<li>We regularly check ratings and reviews of registered companies to ensure you get <strong>only the best</strong>.</li>
					</ul>			
				</div>
				</div>
			</div>
-->
		
		<?php if($_POST['homepage'] != 1) { ?>
				
			<p class="larger margintop15">We have calculated that your roof is <strong><?=$installationWidth?><?=strtoupper($dimensionType)?> x <?=$installationHeight?><?=strtoupper($dimensionType)?></strong>, and this it is facing <strong><?=$roofDirection?></strong> with a <strong><?=round($roofAngle)?>&deg;</strong> pitch. We have estimated your income based on <a class="underlined sapguidelines" href="#">SAP 2005 guidelines</a>, to help you compare us with other solar companies.</p>
			<?php } else { ?>
			<p class="larger margintop15">We have estimated that your roof is <strong><?=$installationWidth?><?=strtoupper($dimensionType)?> x <?=$installationHeight?><?=strtoupper($dimensionType)?></strong>, and this it is facing <strong><?=$roofDirection?></strong> with a <strong><?=round($roofAngle)?>&deg;</strong> pitch. For a more accurate roof calculation please use our <a href="roof-calculator">Roof Calculator</a>. The estimates made are based on <a class="underlined sapguidelines" href="#">SAP 2005 guidelines</a>.</p>		
			<?php } ?>		
			
			<div class="houseinfo-wrapper">		
			
			<?php if($errors > 0) { message_box($error); } ?>		
			
				<div class="houseimage"><img src="<?=bloginfo('template_url')?>/images/your_house.jpg"></div>
				<div class="houseinfo">
					<div class="spanner"></div>	<a class="underlined changeAssumptions" href="#">Edit</a> 
					<h3>Your House</h3>
					<div class="house-info-left">
						<p class="nomarg"><strong>Roof size:</strong> <?=$installationWidth?><?=strtoupper($dimensionType)?> x <?=$installationHeight?><?=strtoupper($dimensionType)?></p>
						<p class="nomarg"><strong>Roof direction:</strong> <?=$roofDirection?></p>
					</div>
					
					<div class="house-info-right">
						<p class="nomarg"><strong>Pitch:</strong> <?=round($roofAngle)?>&deg;</p>
						<p class="nomarg"><strong>Location:</strong> <?=$address1?>, <?=$address2?></p>					
					</div>
					<?php if($countCapped != 0) { ?>
						<div class="clearfix"></div>
						<div class="dno-warning-wrapper">
							<div class="house-info-dno-warning">
								<img src="<?=bloginfo('template_url')?>/images/content/warning.jpg">
							</div>
							<div class="house-info-dno">
								<p class="smaller nomarg">We have used a 3.68kW system in this estimate, as this is the largest system you can install without requiring prior permission from the electricity grid district network operator (DNO). If you would like to see a larger system, please <a class="underlined changeAssumptions nofloat" href="#">edit our assumptions</a>.</p>
							</div>
						</div>
					<?php } ?>	
				</div>
			</div>
			
		
		<div id="sapguidelinesdiv">
			<h2 class="black">SAP 2005 Guidelines</h2>
			<p>The Standard Assessment Procedure (SAP) is adopted by Government as the UK methodology for calculating the energy performance of dwellings. Ratings are not affected by the geographical location, so that a given dwelling has the same rating in all
parts of the UK.</p>

			<p><strong>SAP Calculation:</strong> <span class="sapcalc">0.8 x Kilo Watt Peak x Solar Radiation x Over shading = kWh/year</span></p>
			<p><strong>Example SAP Calculation:</strong> <span class="sapcalc">0.8 x 4 x 1042 x 1 = 3,334 kWh/year</span></p>

			<p>The table below shows a table indicating the annual solar radiation per kWh/m&#178;</p>
			
			<table class="saptable">
				<tr>
					<th rowspan="2">Tilt of collector</th>
					<th colspan="5" class="center">Orientation of collector</th>
				</tr>
				<tr>
					<th class="center">South</th>
					<th class="center">SE/SW</th>
					<th class="center">E/W</th>
					<th class="center">NE/NW</th>
					<th class="center">North</th>
				</tr>
				<tr>
					<td>Horizontal</td>
					<td colspan="5" class="center">933</td>
				</tr>
				<tr>
					<td>30&deg;</td>
					<td>1042</td>
					<td>997</td>
					<td>886</td>
					<td>762</td>
					<td>709</td>
				</tr>
				<tr>
					<td>45&deg;</td>
					<td>1023</td>
					<td>968</td>
					<td>829</td>
					<td>666</td>
					<td>621</td>
				</tr>
				<tr>
					<td>60&deg;</td>
					<td>960</td>
					<td>900</td>
					<td>753</td>
					<td>580</td>
					<td>485</td>
				</tr>
				<tr>
					<td>Vertical</td>
					<td>724</td>
					<td>684</td>
					<td>565</td>
					<td>427</td>
					<td>360</td>
				</tr>
			</table>

			<table class="saptable">
				<tr>
					<th>Overshading</th>
					<th>% of sky blocked by obstacles</th>
					<th>Overshading factor</th>
				</tr>
				<tr>
					<td>Heavy</td>
					<td>> 80%</td>
					<td>0.5</td>
				</tr>
				<tr>
					<td>Significant</td>
					<td>> 60% - 80%</td>
					<td>0.65</td>
				</tr>
				<tr>
					<td>Modest</td>
					<td>20% - 60%</td>
					<td>0.8</td>
				</tr>
				<tr>
					<td>None or very little</td>
					<td>< 20%</td>
					<td>1</td>
				</tr>
			</table>
		</div>
		
				
		<div class="assumptions">
			<h2 class="black">Edit Details</h2>
			<p class="larger margintop5">If you wish to make changes to your house or the assumptions we have made please edit them below then re-calculate your estimated income.</p>
			<form method="post" action="<?=bloginfo('url')?>/results">
				<table class="nobottommargin">
					<tr>
						<td colspan="2"><h5 class="black">Your House</h5></td>
					</tr>
					<tr>
						<td colspan="2">Post code <input class="mini capitalised" name="postcode" type="text" value="<?=$post_code?>"></td>
					</tr>
					<tr>
						<td colspan="2">Roof faces <select name="roofFaces" id="normalSelect">
						<option value="s" <?php if($roofFaces == "s" || empty($roofFaces)){?>selected="selected"<?php } ?>>South</option>
						<option value="sw" <?php if($roofFaces == "sw"){?>selected="selected"<?php } ?>>South West</option>
						<option value="se" <?php if($roofFaces == "se"){?>selected="selected"<?php } ?>>South East</option>
						<option value="w" <?php if($roofFaces == "w"){?>selected="selected"<?php } ?>>West</option>
						<option value="e" <?php if($roofFaces == "e"){?>selected="selected"<?php } ?>>East</option>
						<option value="nw" <?php if($roofFaces == "nw"){?>selected="selected"<?php } ?>>North West</option>
						<option value="ne" <?php if($roofFaces == "ne"){?>selected="selected"<?php } ?>>North East</option>		
						<option value="n" <?php if($roofFaces == "n"){?>selected="selected"<?php } ?>>North</option>
					</select></td></tr>
				<tr><td colspan="2">Roof dimensions <input type="text" name="width" class="mini" value="<?=$installationWidth?>"> x <input type="text" name="height" class="mini" value="<?=$installationHeight?>"><select name="dimensionType" id="normalSelect">
						<option value="m" <?php if($dimensionType == "m" || empty($dimensionType)){ ?>selected="selected"<?php } ?>>Metres</option>
						<option value="f" <?php if($dimensionType == "f"){ ?>selected="selected"<?php } ?>>Feet</option>
						<option value="y" <?php if($dimensionType == "y"){ ?>selected="selected"<?php } ?>>Yards</option>
						<option value="cm" <?php if($dimensionType == "cm"){ ?>selected="selected"<?php } ?>>CM</option>
						<option value="mm" <?php if($dimensionType == "mm"){ ?>selected="selected"<?php } ?>>MM</option>
					</select></td>
					</tr>
					<tr>
						<td colspan="2">The roof angle is <input class="mini" name="roofAngle" type="text" value="<?=round($roofAngle)?>">&deg;</td>
					</tr>
					<tr>
						<td colspan="2">Over shading: <input class="mini" name="overShading" type="text" value="<?=$overShading?>"> (Mark between 1 - 10, 1 being no shading, 10 being totally shaded)</td>
					</tr>
					<tr>
						<td colspan="2">Data Set: SAP Data <input type="radio" value="1" <?php if($sapCheck == 1) {?>checked="checked"<?php } ?> name="sapData"> NASA Data <input type="radio" value="2" <?php if($sapCheck == 2) {?>checked="checked"<?php } ?> name="sapData"></td>
					</tr>
				</table>
				<table class="assumptionbutton nobottommargin">
					<tr><td></td><td><span class="right"><input type="submit" class="searchsubmit_assumptions" value="Calculate" name="get_results"></span></td></tr>
				</table>
				<table class="nobottommargin">
					<tr>
						<td colspan="2"><h5 class="black">Assumptions</h5></td>
					</tr>
					<tr>
						<td colspan="2">My daytime electricity rate &pound;&nbsp;<input class="mini" name="electricyRate" type="text" value="<?=$electricityRate?>"> per kWh (inc VAT)</td>
					</tr>
					<tr>
						<td><input type="radio" value="1" name="exportType" <?php if($exportDefault == 1){?>checked="checked"<?php } ?>> Use Standard Export Tariff (<?=round($assumptions['export_tariff'],1)?>p/kWh)</td>
						<td><input type="radio" value="2" name="exportType" <?php if($exportDefault == 2){?>checked="checked"<?php } ?>> Energy supplier has agreed to buy at <input class="mini" name="exportPrice" type="text" value="<?=$exportField?>">p/kWh</td>
					</tr>
					<tr>
						<td colspan="2"><input class="mini" name="energyToUse" type="text" value="<?=round($energyUsage)?>">% of the energy generated will be used in my home</td>
					</tr>
					<tr>
						<td colspan="2">I want to assess the benefits over a <input class="mini" name="benefitPeriod" type="text" value="<?=$years?>"> year period</td>
					</tr>
					<tr>
						<td colspan="2">Rate at which panels degrade <input class="mini" name="degradeRate" type="text" value="<?=$degradeRate?>">% per year</td>
					</tr>
					<tr>
						<td colspan="2">Assume RPI of <input class="mini" name="rpi" type="text" value="<?=$rpi?>">% and annual energy price inflation of <input class="mini" name="priceInflation" type="text" value="<?=$inflation?>">%</td>
					</tr>
					<tr>
						<td colspan="2">Quote for larger installations ( >3.68 kW ) that require DNO pre-approval? Yes <input type="radio" value="1" name="largeInstallation" <?php if($largeInstallation == 1) {?>checked="checked"<?php } ?>> No <input type="radio" value="0" name="largeInstallation" <?php if($largeInstallation == 0) {?>checked="checked"<?php } ?>></td>
					</tr>
				</table>		
				<table class="assumptionbutton">
					<tr><td></td><td><input type="submit" class="searchsubmit_assumptions" value="Calculate" name="get_results"><input type="hidden" name="quoteid" value="<?=$quoteID?>"></td></tr>
				</table>
			</form>
		</div>
	</div>
	<div class="column four push-1">
		<input class="large" style="display:none;" id="postcode" name="postcode" size="30" type="text" value="<?=$trimPostcode?>">
		<input style="display:none;" name="givenlat" id="givenlat" type="text" value="<?=$givenLat?>">
		<input style="display:none;" name="givenlong" id="givenlong" type="text" value="<?=$givenLong?>">
		<div id="map"></div>
	</div>
</div>

<div class="clearfix"></div>

<?=$formattedBothOptimums?>
<?=$outputBothOptimums?>
<?php } ?>

</div>

<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?=$api->setting_value?>" type="text/javascript"></script>
<script src="http://www.google.com/uds/api?file=uds.js&amp;v=1.0&amp;key=<?=$api->setting_value?>" type="text/javascript"></script> 
<script src="<?=bloginfo("template_url")?>/js/gmap.js" type="text/javascript"></script>
<script type="text/javascript">
		
	

	function showCalc(elementid) {
		var theElement = document.getElementById(elementid);
		if (theElement.style.display == '') {
			theElement.style.display = 'none';
		} else {
			theElement.style.display = '';
		}
	}
		
	function initiateMap(){
		doAll(jQuery("#postcode").val(),jQuery("#givenlat").val(),jQuery("#givenlong").val());
	}
		
	if(jQuery("#postcode").val()!=""){
		addLoadEvent(mapLoad);
		addUnLoadEvent(GUnload);
	}

	jQuery(document).ready(function() {

		jQuery('#quoteform').submit(function() {
			//jQuery(this).preventDefault();
			
			var quoteData = jQuery(this).serialize();
						
			jQuery.ajax({
				url: "<?php bloginfo('template_url')?>/includes/ajax/createuser.php",
				type: "POST",
				data: "data="+quoteData,
				dataType: "json",
				success: function(ret){		
							
					jQuery("#themessage").html('');
					
					if(ret.error == 1){
						jQuery("<div />").attr({"class" : "xtrcon_message xtrcon_fail"}).html(ret.msg).appendTo("#themessage");
					}else if(ret.error == 0){
						jQuery.modal.close();
					}
					
				}
			});
			
			return false;
		
		});

		jQuery(".orientationradio").change(function(){
			var clicked = jQuery(this).attr('value');
			var panelid = jQuery(this).attr('id');
			var realid = panelid.split('[');
			var thepanelid = realid[1].split(']');
	
			if(clicked == 1){
				var notclicked = 2;
			} else {
				var notclicked = 1;
			}
			jQuery("#system-"+notclicked).hide();
			jQuery("#system-"+clicked).show();
			jQuery("#chosenSystem").val(thepanelid[0]);
			jQuery(".calcsContainer").hide();
		});
		
		<?php if($_POST && !is_user_logged_in()) { ?>
/*
		jQuery(".askForDetails").modal({
			opacity:86,
			overlayClose:true,
			close: false
		});
*/
		<?php } ?>
			
		jQuery('.displayCalcsL').click(function(e) {
			e.preventDefault();

			jQuery(".calcsContainerL").modal({
				opacity:50,
				overlayClose:true
			});

		});		

		jQuery('.displayCalcsP').click(function(e) {
			e.preventDefault();

			jQuery(".calcsContainerP").modal({
				opacity:50,
				overlayClose:true
			});

		});		
			
		jQuery(".changeAssumptions").click(function (e) {
			e.preventDefault();
		
			jQuery(".assumptions").modal({
				opacity:50,
				overlayClose:true
			});
 		 });

		jQuery(".sapguidelines").click(function () {
		
			jQuery("#sapguidelinesdiv").modal({
				opacity:50,
				overlayClose:true
			});
 		 });

		jQuery(".breakdown").click(function (e) {
			e.preventDefault();
			
			var id = jQuery(this).attr("id");
			
			jQuery("#"+id+"summary").modal({
				opacity:50,
				overlayClose:true
			});
 		 });
	});
</script>

<?php get_footer(); ?>