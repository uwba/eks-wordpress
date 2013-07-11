<?php
// Template Name: Home
?>

<style>#content {background-image: none;} #content-mid {background-image: none;} #content-inner {background-image:none;padding-left:15px;padding-right:15px;position:relative;}</style>
<div id="before-content-area" class="clear" style="overflow:hidden;">
	<?php if ( ! dynamic_sidebar( 'Before Content Area' ) ) : ?>
		<!--Widgetized 'Before Content Area' for the home page-->
	<?php endif ?>
</div>
<!--<div id="outer">
	<div id="inner">
		<ul id="slides">
			<li>
				<img src="bottles.jpg" width="938" height="255" alt="" />
			</li>
			<li>
				<img src="barrels.jpg" width="938" height="400" alt="" />
			</li>
			<li>
				<img src="rack.jpg" width="600" height="400" alt="" />
			</li>
			<li>
				<img src="cellar.jpg" width="600" height="400" alt="" />
			</li>
		</ul>
	</div>
</div> -->

<div id="top-content-area" class="clear"  style="overflow:hidden;">
	<?php if ( ! dynamic_sidebar( 'Top Content Area' ) ) : ?>
		<!--Widgetized 'Top Content Area' for the home page-->
	<?php endif ?>
</div>
<div id="top-content-buttons" class="clear"  style="overflow:hidden;">
	<?php if ( ! dynamic_sidebar( 'Top Content Buttons' ) ) : ?>
		<!--Widgetized 'Top Content Buttons' for the home page-->
	<?php endif ?>
</div>
<div id="center-content-area" class="clear" >
	<div id="leftcontent">
		<?php if ( ! dynamic_sidebar( 'Left Content Area' ) ) : ?>
		  <!--Widgetized 'Left Content Area' for the home page-->
		<?php endif ?>
	</div>
	<div id="rightcontent">
		<?php if ( ! dynamic_sidebar( 'Right Content Area' ) ) : ?>
		  <!--Widgetized 'Right Content Area' for the home page-->
		<?php endif ?>
	</div>
</div>
<!--<div class="clear">
	<div id="aftercontent">
		<?php //if ( ! dynamic_sidebar( 'After Content Area' ) ) : ?>-->
		  <!--Widgetized 'After Content Area' for the home page-->
		<!-- <?php //endif ?>
	</div>
</div>--><!-- /#top content area -->
<div class="clear">
	<div id="beforefooter">
		<?php if ( ! dynamic_sidebar( 'Before Footer Area' ) ) : ?>
		  <!--Widgetized 'Before Content Area' for the home page-->
		<?php endif ?>
	</div>
</div><!-- /#before footer area -->

