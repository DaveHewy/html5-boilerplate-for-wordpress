<?php 

/* Template Name: Pg TPL - Homepage */

get_header(); ?>

<!-- Homepage Slide -->
<section id="slide">
	<div class="slidebg">
		<hgroup>
			<h1>Solar Panel Systems</h1>
			<h2>Save Money Today!</h2>
			<p><strong>Enter your postcode to find out how much you could SAVE</strong></p>
			<form action="/roof-calculator/" method="post">
				<input type="text" placeholder="eg. cm9 6ys" name="postcode">
				<button class="orange">Get your Quote</button>
			</form>
			<p class="earnupto">
				Earn up to 16% per year from solar panels, through electricity savings and the Feed In Tariffs.
			</p>
		</hgroup>
	</div>
</section>
<!-- End homepage slide -->

<div id="homepage-content">

<!-- Two column homepage top section -->
<div id="col-1" class="column six">
	<img src="<?php bloginfo('stylesheet_directory'); ?>/images/arrows-down.png" class="arrows-down">
	<h3 class="light-blue text-center">A Long Term Investment</h3>
	<form action="/roof-calculator/" method="post">
		<input type="text" class="homepage-input" placeholder="Postcode" name="postcode">
		<div class="clearfix"></div>
		<button type="submit" class="button orange homepage-button">Calculate savings</button>
	</form>
	
	<section id="inflation-protected">
		<h3 class="navy small-line">Inflation protected income for 25 years</h3>
		<p>Join the <mark>solar revolution</mark> and start <mark>making money from your roof</mark>. Simply enter your postcode below and we will give you an instant estimate of the income you could generate.</p>
	</section>
	<section>
		<h4>Example 25 Year Solar Panel Income</h4>
		
		<table width="98%" class="example-install-table">
			<tr>
				<th width="25%">12 x Solar Panels</th>
				<th width="25%">Revenue per year</th>
				<th width="25%">Payback period</th>
				<th width="25%">Total Payback</th>
			</tr>
			<tr>
				<td class="text-center">£10,000</td>
				<td class="text-center">£900</td>
				<td class="text-center">7y 3m</td>
				<td class="text-center">£22,500</td>				
			</tr>
		</table>
		
	</section>
</div>

<div class="learn-more-arrow"></div>

<div id="col-2" class="column six">
	<h2 class="adv">Advantages Of<br>Solar Energy</h2>
	<section>
		<h3 class="env">Environment</h3>
		<ul class="tree-list">
			<li>A true renewable energy source</li>
			<li>Reduce cO2 emissions by up to 1 tonne</li>
		</ul>
	</section>
	<section>
		<h3 class="save">Save money</h3>
		<ul class="light-bulb">
			<li>Reduce energy bills by up to £200 p/a</li>
			<li>Save the environment</li>
		</ul>		
	</section>
	<section>
		<h3 class="make">Make money</h3>
		<ul class="pound-list">
			<li>Develop dependancy from the electricity grid</li>
			<li>Up to 10% tax free guaranteed return on investment</li>
		</ul>		
	</section>	
	
	<section id="calculate-savings">
		<div class="column push two">
			<img src="<?php bloginfo('stylesheet_directory'); ?>/images/arrow-blank.png" class="arrows-alt" alt="Solar panel more info">
		</div>
		<div class="column push three">
			<form action="/roof-calculator/" method="post">
				<input type="text" class="homepage-input small" placeholder="Postcode" name="postcode">
				<button class="button orange homepage-button small">Get your quote</button>
			</form>
		</div>
	</section>
</div>
<!-- End Two columns -->

<div class="clearfix"></div>

<!-- FAQ Section -->
<section id="faq">
	<div class="column ten">
		<h2>Frequently Asked Questions</h2>
	</div>
	<div class="column three">
		<a href="<?php bloginfo('url'); ?>/question/how-do-i-get-a-quote/" title="Learn how to get a solar panel quote" class="button orange">How do I<br>get a quote?</a>
	</div>
	<div class="column three push-1">
		<a href="<?php bloginfo('url'); ?>/question/how-much-could-i-earn/" title="How much could I earn" class="button green">How much<br>could I earn?</a>
	</div>
	<div class="column three push-2">
		<a href="<?php bloginfo('url'); ?>/question/is-my-roof-suitable-for-solar-panels/" title="Find out if your roof is solar panel suited" class="button teal">Is my roof suitable for solar panels?</a>
	</div>
</section>
<!-- End FAQ Section -->

<div class="clearfix"></div>

<!-- Solar Information Section -->
<section id="solarinfo">
	<div class="faq">
		<div class="column ten">
			<h2 class="navy">Solar Information</h2>
		</div>
		<div class="column four">
			<h3 class="no-margin">Solar PV</h3>
			<p>Solar PV technology is an extremely underused technology within the UK which can provide vast cost savings and environmental benefits to a domestic household. It also generates clean, renewable energy to be fed back into the Electricity Grid and generates extra income through government based incentives such as the <a href="#">Feed-in Tariff</a></p>
		</div>
		<div class="column four">
			<h3 class="no-margin">Solar Panels UK</h3>
			<p>Solar panels come in a variety of different types and technologies all suited to varying situations and requirements. The two main types of panel are differentiated by their output of either electricity (solar PV) or hot water (solar thermal).</p>
		</div>
		<div class="column four">
			<h3 class="no-margin">Solar Power</h3>
			<p>Solar power has been harnessed for many years but only in the last years has solar technology become economically viable for use in the domestic household. Solar thermal can provide hot water all-year round whilst solar PV can cut up to &pound;200 off your energy bills and provide a passive income via the Government's Feed-in Tariff.</p>
		</div>
	</div>
</section>
<!-- End Solar Information Section -->

</div>

<?php get_footer(); ?>