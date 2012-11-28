<?php
// Template Name: Donation
?>
<?php
if (!isset($_SESSION['volunteer']['steps'])) {
	$_SESSION['volunteer']['steps'] = array();
}


?>
<style>
	#step1, #step2, #step3, #step4, #step5 {
		display: none;
	}
</style>

<!--<pre><?php //var_dump($_SESSION['volunteer']); var_dump($_POST); ?></pre>-->
<link rel="stylesheet" type="text/css" media="all" href="<?php echo get_stylesheet_directory_uri(); ?>/styles/index.451.css" />
<link rel="stylesheet" type="text/css" media="all" href="<?php echo get_stylesheet_directory_uri(); ?>/styles/theme.css" />

<div id="main" class="clear" >
	<div id="leftcontent">
		<div class="section-head">
			<h1>Donate</h1>
		</div>

			<iframe id="wufooFormm7x3k1" height="1150px" allowtransparency="true" frameborder="0" scrolling="no" style="width:100%;border:none" src="https://uwba.wufoo.com/embed/m7x3k1/def/embedKey=m7x3k1938812&amp;referrer=http%3Awuslashwuslashwww.earnitkeepitsaveit.orgwuslashdonate">

				<!DOCTYPE html><html class="embed safari"><head>

				<title>
				Earn It! Keep It! Save It!
				</title>

				<!-- Meta Tags -->
				<meta charset="utf-8">
				<meta name="generator" content="Wufoo">
				<meta name="robots" content="noindex, noarchive">
				<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

				<link rel="canonical" href="https://uwba.wufoo.com/forms/m7x3k1/">

				<!-- CSS -->
				<link href="/stylesheets/public/forms/css/index.451.css" rel="stylesheet">
				<link href="/css/custom/6/theme.css" rel="stylesheet">
				<link href="https://uwba.org/wp/wp-content/themes/lifestyle/css/custom.css" rel="stylesheet">

				<!--[if lt IE 10]>
				<script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
				<![endif]-->

				</head>

				<body id="public" onorientationchange="window.scrollTo(0, 1)">



				<div id="container" class="ltr">
					<h1 id="logo">
					<a>United Way of the Bay Area</a>
				</h1>

				<form id="form2" name="form2" class="wufoo topLabel page1" autocomplete="off" enctype="multipart/form-data" method="post" novalidate="" action="https://uwba.wufoo.com/embed/m7x3k1/def/embedKey=m7x3k1938812&amp;referrer=http%3Awuslashwuslashwww.earnitkeepitsaveit.orgwuslashdonate#public">

				<header id="header" class="info">
				<h2>Earn It! Keep It! Save It!</h2>
				<div>Your generous gift will support the Earn It! Keep It! Save It! Free Tax Help program powered by United Way of the Bay Area. No goods or services were provided in exchange for this gift.</div>
				</header>

				<ul class="running">


				<li class="total">
				<output id="lola" style="margin-top: 7px; "><table id="run" border="0" cellspacing="0" cellpadding="0"><tfoot></tfoot><tbody><tr><td colspan="2"><b>Total</b><span>$0.00</span></td></tr></tbody></table></output>
				</li>


				<li id="fo2li121" class="notranslate      ">
				<label class="desc" id="title121" for="Field121">
				Name
				<span id="req_121" class="req">*</span>
				</label>
				<span>
				<input id="Field121" name="Field121" type="text" class="field text fn" value="" size="8" tabindex="1" onkeyup="handleInput(this);" onchange="handleInput(this);" required="">
				<label for="Field121">First</label>
				</span>
				<span>
				<input id="Field122" name="Field122" type="text" class="field text ln" value="" size="14" tabindex="2" onkeyup="handleInput(this);" onchange="handleInput(this);" required="">
				<label for="Field122">Last</label>
				</span>
				</li>



				<li id="fo2li9" class="notranslate      ">
				<label class="desc" id="title9" for="Field9">
				Email
				<span id="req_9" class="req">*</span>
				</label>
				<div>
				<input id="Field9" name="Field9" type="email" spellcheck="false" class="field text medium" value="" maxlength="255" tabindex="3" onkeyup="handleInput(this);" onchange="handleInput(this);" required=""> 
				</div>
				</li>



				<li id="fo2li1" class="notranslate      ">
				<label class="desc" id="title1" for="Field1">
				Organization Name (optional)
				</label>
				<div>
				<input id="Field1" name="Field1" type="text" class="field text large" value="" maxlength="255" tabindex="4" onkeyup="handleInput(this); " onchange="handleInput(this);">
				</div>
				</li>


				<li id="fo2li4" class="notranslate      ">
				<label class="desc" id="title4" for="Field4">
				Zip
				<span id="req_4" class="req">*</span>
				</label>
				<div>
				<input id="Field4" name="Field4" type="text" class="field text medium" value="" maxlength="255" tabindex="5" onkeyup="handleInput(this); " onchange="handleInput(this);" required="">
				</div>
				</li>


				<li id="fo2li8" class="phone notranslate      ">
				<label class="desc" id="title8" for="Field8">
				Phone Number
				</label>
				<span>
				<input id="Field8" name="Field8" type="tel" class="field text" value="" size="3" maxlength="3" tabindex="6" onkeyup="handleInput(this);" onchange="handleInput(this);">
				<label for="Field8">###</label>
				</span>
				<span class="symbol">-</span>
				<span>
				<input id="Field8-1" name="Field8-1" type="tel" class="field text" value="" size="3" maxlength="3" tabindex="7" onkeyup="handleInput(this);" onchange="handleInput(this);">
				<label for="Field8-1">###</label>
				</span>
				<span class="symbol">-</span>
				<span>
				 <input id="Field8-2" name="Field8-2" type="tel" class="field text" value="" size="4" maxlength="4" tabindex="8" onkeyup="handleInput(this);" onchange="handleInput(this);">
				<label for="Field8-2">####</label>
				</span>
				</li>


				<li id="fo2li127" class="notranslate section      ">
				<section>
				<h3 id="title127">
				Select a donation amount
				</h3>
				<div id="instruct127">Your donation goes straight to poverty-cutting programs</div>
				</section>
				</li>


				<li id="fo2li123" class="notranslate      ">
				<fieldset>
				<!--[if !IE | (gte IE 8)]-->
				<legend id="title123" class="desc">
				</legend>
				<!--[endif]-->
				<!--[if lt IE 8]>
				<label id="title123" class="desc">
				</label>
				<![endif]-->
				<div>
				<input id="radioDefault_123" name="Field123" type="hidden" value="">
				<span>
				<input id="Field123_0" name="Field123" type="radio" class="field radio" value="$50 provides a child with an after-school program for 1 week." tabindex="9" onchange="handleInput(this);" onmouseup="handleInput(this);">
				<label class="choice" for="Field123_0">
				$50 provides a child with an after-school program for 1 week.</label>
				</span>
				<span>
				<input id="Field123_1" name="Field123" type="radio" class="field radio" value="$100 helps pay for a career workshop for 20 youth." tabindex="10" onchange="handleInput(this);" onmouseup="handleInput(this);">
				<label class="choice" for="Field123_1">
				$100 helps pay for a career workshop for 20 youth.</label>
				</span>
				<span>
				<input id="Field123_2" name="Field123" type="radio" class="field radio" value="$250 provides 5 low income families with free tax preparation." tabindex="11" onchange="handleInput(this);" onmouseup="handleInput(this);">
				<label class="choice" for="Field123_2">
				$250 provides 5 low income families with free tax preparation.</label>
				</span>
				<span>
				<input id="Field123_3" name="Field123" type="radio" class="field radio" value="$500 enables credit counseling, workshops, and ongoing financial coaching for a struggling family." tabindex="12" onchange="handleInput(this);" onmouseup="handleInput(this);">
				<label class="choice" for="Field123_3">
				$500 enables credit counseling, workshops, and ongoing financial coaching for a struggling family.</label>
				</span>
				<span>
				<input id="Field123_4" name="Field123" type="radio" class="field radio" value="$1500 creates opportunity for vocational training to move someone into a self-sustaining job." tabindex="13" onchange="handleInput(this);" onmouseup="handleInput(this);">
				<label class="choice" for="Field123_4">
				$1500 creates opportunity for vocational training to move someone into a self-sustaining job.</label>
				</span>
				</div>
				</fieldset>
				</li>


				<li id="fo2li232" class="notranslate      ">
				<label class="desc" id="title232" for="Field232">
				Other
				</label>
				<span class="symbol">$</span>
				<span>
				<input id="Field232" name="Field232" type="text" class="field text currency nospin" value="" size="10" tabindex="14" onkeyup="handleInput(this);" onchange="handleInput(this);">
				<label for="Field232">Dollars</label>
				</span>
				<span class="symbol radix">.</span>
				<span class="cents">
				<input id="Field232-1" name="Field232-1" type="text" class="field text nospin" value="" size="2" maxlength="2" tabindex="15" onkeyup="handleInput(this);" onchange="handleInput(this);">
				<label for="Field232-1">Cents</label>
				</span>
				</li>

				<li id="fo2li235" class="notranslate section      ">
				<section>
				<h3 id="title235">
				</h3>
				<div id="instruct235">After clicking ��Continue�� you may enter your credit card information on the next screen. We take your privacy seriously and never sell your email address, ever.</div>
				</section>
				</li>


				<li id="fo2li237" class="notranslate hide     ">
				<label class="desc" id="title237" for="Field237">
				Comment1
				</label>
				<div>
				<input id="Field237" name="Field237" type="text" class="field text small" value="earnitkeepitsaveit" maxlength="255" tabindex="16" onkeyup="handleInput(this); " onchange="handleInput(this);">
				</div>
				</li>

				 


				<li class="buttons ">
				<div>
				<input type="hidden" name="currentPage" id="currentPage" value="Sn1HNtO7pfO5I2MeaqsxRqGEJLXmS5fW70wWuiLUPso=">

				<input id="saveForm" name="saveForm" class="btImg submit" type="image" src="https://uwba.org/wp/wp-content/uploads/continue-btn-donate.jpg" alt="Submit" onmousedown="doSubmitEvents();" tabindex="91">


				</div>
				</li>

				<li class="hide">
				<label for="comment">Do Not Fill This Out</label>
				<textarea name="comment" id="comment" rows="1" cols="1"></textarea>
				<input type="hidden" id="idstamp" name="idstamp" value="aiy7sz8F07Ou2d0YY2ys53ujFk5IouBwKeGub3TLTTU=">
				<input type="hidden" id="stats" name="stats" value="{&quot;errors&quot;:0,&quot;startTime&quot;:0,&quot;endTime&quot;:0,&quot;referer&quot;:&quot;http:\/\/www.earnitkeepitsaveit.org\/donate&quot;}">
				<input type="hidden" id="clickOrEnter" name="clickOrEnter" value="">
				</li>
				</ul>
				</form> 

				</div><!--container-->


<!-- JavaScript -->
<script src="/scripts/public/dynamic.451.js?language=english"></script>

<script>
__RULES = [];
__ENTRY = [];
__PRICES = {"ShowRunningTotal":true,"BasePrice":"0.00","Currency":"&#36;","Decimals":2,"BasePriceName":"Donation Amount","TotalText":"Total","MerchantFields":[{"Title":"","Typeof":"radio","ColumnId":"123","Price":"0","ChoicesText":"0","Choices":{"295":{"ColumnId":"123","ChoiceId":"295","Choice":"$50 provides a child with an after-school program for 1 week.","Price":"50","Score":1},"296":{"ColumnId":"123","ChoiceId":"296","Choice":"$100 helps pay for a career workshop for 20 youth.","Price":"100","Score":2},"297":{"ColumnId":"123","ChoiceId":"297","Choice":"$250 provides 5 low income families with free tax preparation.","Price":"250","Score":3},"298":{"ColumnId":"123","ChoiceId":"298","Choice":"$500 enables credit counseling, workshops, and ongoing financial coaching for a struggling family.","Price":"500","Score":4},"299":{"ColumnId":"123","ChoiceId":"299","Choice":"$1500 creates opportunity for vocational training to move someone into a self-sustaining job.","Price":"1500","Score":5}}},{"Title":"Other","Typeof":"money","ColumnId":"232","Price":"1","ChoicesText":"0"}],"BasePriceText":"Donation Amount"};

</script>



<script type="text/javascript">
__EMBEDKEY = "m7x3k1938812"
</script>
				</body>
			</html>

		</iframe>

<script src="<?php echo get_stylesheet_directory_uri(); ?>/scripts/dynamic.451.js?language=english"></script>


	</div>
</div>

	<?php get_sidebar(); ?>

