<?php
// 2017-02-21 update: page title and breadcrumbs moved to page_functions.php
//////////////////////////////////////////////////
//////////////////////////////////////////////////
//////////////////////////////////////////////////
/*//////*/      session_start();        /*//////*/
//////////////////////////////////////////////////
//////////////////////////////////////////////////
//  now check the user is OK to view this page  //
/*//////// require ('page_access.php'); /*//////*/
//////////////////////////////////////////////////
//////////////////////////////////////////////////
//////////////////////////////////////////////////

header('Content-Type: text/html; charset=utf-8');
require ('page_functions.php');
include 'db_conn.php';

/* session check */
if (!isset($_SESSION['username'])) {
	$_SESSION['url'] = $_SERVER['REQUEST_URI'];
	header("Location: login.php"); // send them to the Login page.
}

// pull the header and template stuff:
pagehead();

?>
<!-- start page -->
					<div class="row">
						<div class="col-md-12">

			<!-- START THE FORM! -->
			 			  <form class="form-horizontal form-bordered" action="page_add_do.php" method="post">
			 
			 
							<section class="panel">
								<header class="panel-heading">
									<div class="panel-actions">
										<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
										<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
									</div>

									<h2 class="panel-title">Add A New Page</h2>
								</header>
								<div class="panel-body">

<!-- START PAGE EDIT FORM -->
									<div class="form-group">
								<div class="table-responsive">
									<table class="table table-bordered table-striped table-condensed mb-none">
  <tr>
    <th width="50%">FIELD</th>
    <th>VALUE</th>
  </tr>
  <tr>
    <td>Name</td>
    <td>
    	<input type="text" class="form-control" name="name_EN" value="">
    </td>
  </tr>
  <tr>
    <td>中文名</td>
    <td>
    	<input type="text" class="form-control" name="name_CN" value="中文名">
    </td>
  </tr>
  <tr>
    <td>Parent Page</td>
    <td>

		<select data-plugin-selectTwo class="form-control populate" name="parent_page_ID">
			<option value="0" selected="selected">None Selected / 无</option>
    <?php

					$get_P_pages_SQL = "SELECT * FROM `pages` ORDER BY `order`";
					// echo $get_P_pages_SQL;

					  $P_page_count = 0;

					  $result_get_P_pages = mysqli_query($con,$get_P_pages_SQL);
					  // while loop


					  while($row_get_P_pages = mysqli_fetch_array($result_get_P_pages)) {

							$P_page_ID 					= $row_get_P_pages['ID'];
							$P_page_name_EN 			= $row_get_P_pages['name_EN'];
							$P_page_name_CN 			= $row_get_P_pages['name_CN'];
							$P_page_parent_ID 			= $row_get_P_pages['parent_ID'];
							$P_page_dept_ID 			= $row_get_P_pages['dept_ID'];
							$P_page_main_menu 			= $row_get_P_pages['main_menu'];
							$P_page_footer_menu 		= $row_get_P_pages['footer_menu'];
							$P_page_filename 			= $row_get_P_pages['filename'];
							$P_page_created_by 			= $row_get_P_pages['created_by'];
							$P_page_date_created 		= $row_get_P_pages['date_created'];
							$P_page_status 				= $row_get_P_pages['status'];
							$P_page_privacy 			= $row_get_P_pages['privacy'];
							$P_page_min_user_level 		= $row_get_P_pages['min_user_level'];
							$P_page_order 				= $row_get_P_pages['order'];
							$P_page_icon 				= $row_get_P_pages['icon'];
							$P_page_og_locale 			= $row_get_P_pages['og_locale'];
							$P_page_og_type 			= $row_get_P_pages['og_type'];
							$P_page_og_desc 			= $row_get_P_pages['og_desc'];
							$P_page_og_section 			= $row_get_P_pages['og_section'];
							$P_page_side_bar_config 	= $row_get_P_pages['side_bar_config'];
							$P_page_lookup_table 		= $row_get_P_pages['lookup_table'];

						?>
							<option value="$this_page_parent_ID"><?php echo $P_page_ID; ?>: <?php echo $P_page_name_EN; if (($P_page_name_CN!='')&&($P_page_name_CN!='中文名')) { echo ' / ' . $P_page_name_CN; } ?> [ <?php echo $P_page_filename; ?> ]</option>
						<?php


						} // END LOOP
					  ?>
    	</select>
    </td>
  </tr>
  <tr>
    <td>Main Menu</td>
    <td>

    	<div class="switch switch-success"">
			<input type="checkbox" name="page_main_menu" data-plugin-ios-switch />
		</div>

    </td>
  </tr>
  <tr>
    <td>Filename</td>
    <td>
    <input type="text" class="form-control" name="filename" value="*.php">
    <br /><em>(changes must be manually reflected in the file name itself)</em></td>
  </tr>
  <tr>
    <td>Privacy</td>
    <td>

    <select class="form-control mb-md" name="privacy">
		<option value="PUBLIC">PUBLIC</option>
		<option value="PRIVATE" selected="selected">PRIVATE</option>
	</select>

	</td>
  </tr>
  <tr>
    <td>Minimum User Level</td>
    <td>
    <input type="text" class="form-control" name="min_user_level" value="<?php echo $this_page_min_user_level; ?>">
    <br /><em>(feature coming soon!)</em>
    </td>
  </tr>
  <tr>
    <td>Order</td>
    <td>
    <input type="text" class="form-control" name="page_order" value="<?php echo $this_page_order; ?>">
    <br /><em>(feature coming soon!)</em>
    </td>
  </tr>
  <tr>
    <td>Icon
    <br />
    <a href="http://fontawesome.io/icons/" target="_blank" title="We use the FONTAWESOME icons. See them all here">Reference</a>
    </td>
    <td>

    	<input type="text" class="form-control" id="icon" value="fa-" name="icon">
    	<br /><em>(MUST include the 'fa-' at the start)</em>

    </td>
  </tr>
  <tr>
    <td>OG Locale</td>
    <td>
    	<select data-plugin-selectTwo class="form-control populate" name="OG_locale">
	<option value="af_NA">Afrikaans (Namibia)</option>
	<option value="af_ZA">Afrikaans (South Africa)</option>
	<option value="af">Afrikaans</option>
	<option value="ak_GH">Akan (Ghana)</option>
	<option value="ak">Akan</option>
	<option value="sq_AL">Albanian (Albania)</option>
	<option value="sq">Albanian</option>
	<option value="am_ET">Amharic (Ethiopia)</option>
	<option value="am">Amharic</option>
	<option value="ar_DZ">Arabic (Algeria)</option>
	<option value="ar_BH">Arabic (Bahrain)</option>
	<option value="ar_EG">Arabic (Egypt)</option>
	<option value="ar_IQ">Arabic (Iraq)</option>
	<option value="ar_JO">Arabic (Jordan)</option>
	<option value="ar_KW">Arabic (Kuwait)</option>
	<option value="ar_LB">Arabic (Lebanon)</option>
	<option value="ar_LY">Arabic (Libya)</option>
	<option value="ar_MA">Arabic (Morocco)</option>
	<option value="ar_OM">Arabic (Oman)</option>
	<option value="ar_QA">Arabic (Qatar)</option>
	<option value="ar_SA">Arabic (Saudi Arabia)</option>
	<option value="ar_SD">Arabic (Sudan)</option>
	<option value="ar_SY">Arabic (Syria)</option>
	<option value="ar_TN">Arabic (Tunisia)</option>
	<option value="ar_AE">Arabic (United Arab Emirates)</option>
	<option value="ar_YE">Arabic (Yemen)</option>
	<option value="ar">Arabic</option>
	<option value="hy_AM">Armenian (Armenia)</option>
	<option value="hy">Armenian</option>
	<option value="as_IN">Assamese (India)</option>
	<option value="as">Assamese</option>
	<option value="asa_TZ">Asu (Tanzania)</option>
	<option value="asa">Asu</option>
	<option value="az_Cyrl">Azerbaijani (Cyrillic)</option>
	<option value="az_Cyrl_AZ">Azerbaijani (Cyrillic, Azerbaijan)</option>
	<option value="az_Latn">Azerbaijani (Latin)</option>
	<option value="az_Latn_AZ">Azerbaijani (Latin, Azerbaijan)</option>
	<option value="az">Azerbaijani</option>
	<option value="bm_ML">Bambara (Mali)</option>
	<option value="bm">Bambara</option>
	<option value="eu_ES">Basque (Spain)</option>
	<option value="eu">Basque</option>
	<option value="be_BY">Belarusian (Belarus)</option>
	<option value="be">Belarusian</option>
	<option value="bem_ZM">Bemba (Zambia)</option>
	<option value="bem">Bemba</option>
	<option value="bez_TZ">Bena (Tanzania)</option>
	<option value="bez">Bena</option>
	<option value="bn_BD">Bengali (Bangladesh)</option>
	<option value="bn_IN">Bengali (India)</option>
	<option value="bn">Bengali</option>
	<option value="bs_BA">Bosnian (Bosnia and Herzegovina)</option>
	<option value="bs">Bosnian</option>
	<option value="bg_BG">Bulgarian (Bulgaria)</option>
	<option value="bg">Bulgarian</option>
	<option value="my_MM">Burmese (Myanmar [Burma])</option>
	<option value="my">Burmese</option>
	<option value="ca_ES">Catalan (Spain)</option>
	<option value="ca">Catalan</option>
	<option value="tzm_Latn">Central Morocco Tamazight (Latin)</option>
	<option value="tzm_Latn_MA">Central Morocco Tamazight (Latin, Morocco)</option>
	<option value="tzm">Central Morocco Tamazight</option>
	<option value="chr_US">Cherokee (United States)</option>
	<option value="chr">Cherokee</option>
	<option value="cgg_UG">Chiga (Uganda)</option>
	<option value="cgg">Chiga</option>
	<option value="zh_Hans">Chinese (Simplified Han)</option>
	<option value="zh_Hans_CN">Chinese (Simplified Han, China)</option>
	<option value="zh_Hans_HK">Chinese (Simplified Han, Hong Kong SAR China)</option>
	<option value="zh_Hans_MO">Chinese (Simplified Han, Macau SAR China)</option>
	<option value="zh_Hans_SG">Chinese (Simplified Han, Singapore)</option>
	<option value="zh_Hant">Chinese (Traditional Han)</option>
	<option value="zh_Hant_HK">Chinese (Traditional Han, Hong Kong SAR China)</option>
	<option value="zh_Hant_MO">Chinese (Traditional Han, Macau SAR China)</option>
	<option value="zh_Hant_TW">Chinese (Traditional Han, Taiwan)</option>
	<option value="zh">Chinese</option>
	<option value="kw_GB">Cornish (United Kingdom)</option>
	<option value="kw">Cornish</option>
	<option value="hr_HR">Croatian (Croatia)</option>
	<option value="hr">Croatian</option>
	<option value="cs_CZ">Czech (Czech Republic)</option>
	<option value="cs">Czech</option>
	<option value="da_DK">Danish (Denmark)</option>
	<option value="da">Danish</option>
	<option value="nl_BE">Dutch (Belgium)</option>
	<option value="nl_NL">Dutch (Netherlands)</option>
	<option value="nl">Dutch</option>
	<option value="ebu_KE">Embu (Kenya)</option>
	<option value="ebu">Embu</option>
	<option value="en_AS">English (American Samoa)</option>
	<option value="en_AU">English (Australia)</option>
	<option value="en_BE">English (Belgium)</option>
	<option value="en_BZ">English (Belize)</option>
	<option value="en_BW">English (Botswana)</option>
	<option value="en_CA">English (Canada)</option>
	<option value="en_GU">English (Guam)</option>
	<option value="en_HK">English (Hong Kong SAR China)</option>
	<option value="en_IN">English (India)</option>
	<option value="en_IE">English (Ireland)</option>
	<option value="en_JM">English (Jamaica)</option>
	<option value="en_MT">English (Malta)</option>
	<option value="en_MH">English (Marshall Islands)</option>
	<option value="en_MU">English (Mauritius)</option>
	<option value="en_NA">English (Namibia)</option>
	<option value="en_NZ">English (New Zealand)</option>
	<option value="en_MP">English (Northern Mariana Islands)</option>
	<option value="en_PK">English (Pakistan)</option>
	<option value="en_PH">English (Philippines)</option>
	<option value="en_SG">English (Singapore)</option>
	<option value="en_ZA">English (South Africa)</option>
	<option value="en_TT">English (Trinidad and Tobago)</option>
	<option value="en_UM">English (U.S. Minor Outlying Islands)</option>
	<option value="en_VI">English (U.S. Virgin Islands)</option>
	<option value="en_GB">English (United Kingdom)</option>
	<option value="en_US" selected="selected">English (United States)</option>
	<option value="en_ZW">English (Zimbabwe)</option>
	<option value="en">English</option>
	<option value="eo">Esperanto</option>
	<option value="et_EE">Estonian (Estonia)</option>
	<option value="et">Estonian</option>
	<option value="ee_GH">Ewe (Ghana)</option>
	<option value="ee_TG">Ewe (Togo)</option>
	<option value="ee">Ewe</option>
	<option value="fo_FO">Faroese (Faroe Islands)</option>
	<option value="fo">Faroese</option>
	<option value="fil_PH">Filipino (Philippines)</option>
	<option value="fil">Filipino</option>
	<option value="fi_FI">Finnish (Finland)</option>
	<option value="fi">Finnish</option>
	<option value="fr_BE">French (Belgium)</option>
	<option value="fr_BJ">French (Benin)</option>
	<option value="fr_BF">French (Burkina Faso)</option>
	<option value="fr_BI">French (Burundi)</option>
	<option value="fr_CM">French (Cameroon)</option>
	<option value="fr_CA">French (Canada)</option>
	<option value="fr_CF">French (Central African Republic)</option>
	<option value="fr_TD">French (Chad)</option>
	<option value="fr_KM">French (Comoros)</option>
	<option value="fr_CG">French (Congo - Brazzaville)</option>
	<option value="fr_CD">French (Congo - Kinshasa)</option>
	<option value="fr_CI">French (Côte d’Ivoire)</option>
	<option value="fr_DJ">French (Djibouti)</option>
	<option value="fr_GQ">French (Equatorial Guinea)</option>
	<option value="fr_FR">French (France)</option>
	<option value="fr_GA">French (Gabon)</option>
	<option value="fr_GP">French (Guadeloupe)</option>
	<option value="fr_GN">French (Guinea)</option>
	<option value="fr_LU">French (Luxembourg)</option>
	<option value="fr_MG">French (Madagascar)</option>
	<option value="fr_ML">French (Mali)</option>
	<option value="fr_MQ">French (Martinique)</option>
	<option value="fr_MC">French (Monaco)</option>
	<option value="fr_NE">French (Niger)</option>
	<option value="fr_RW">French (Rwanda)</option>
	<option value="fr_RE">French (Réunion)</option>
	<option value="fr_BL">French (Saint Barthélemy)</option>
	<option value="fr_MF">French (Saint Martin)</option>
	<option value="fr_SN">French (Senegal)</option>
	<option value="fr_CH">French (Switzerland)</option>
	<option value="fr_TG">French (Togo)</option>
	<option value="fr">French</option>
	<option value="ff_SN">Fulah (Senegal)</option>
	<option value="ff">Fulah</option>
	<option value="gl_ES">Galician (Spain)</option>
	<option value="gl">Galician</option>
	<option value="lg_UG">Ganda (Uganda)</option>
	<option value="lg">Ganda</option>
	<option value="ka_GE">Georgian (Georgia)</option>
	<option value="ka">Georgian</option>
	<option value="de_AT">German (Austria)</option>
	<option value="de_BE">German (Belgium)</option>
	<option value="de_DE">German (Germany)</option>
	<option value="de_LI">German (Liechtenstein)</option>
	<option value="de_LU">German (Luxembourg)</option>
	<option value="de_CH">German (Switzerland)</option>
	<option value="de">German</option>
	<option value="el_CY">Greek (Cyprus)</option>
	<option value="el_GR">Greek (Greece)</option>
	<option value="el">Greek</option>
	<option value="gu_IN">Gujarati (India)</option>
	<option value="gu">Gujarati</option>
	<option value="guz_KE">Gusii (Kenya)</option>
	<option value="guz">Gusii</option>
	<option value="ha_Latn">Hausa (Latin)</option>
	<option value="ha_Latn_GH">Hausa (Latin, Ghana)</option>
	<option value="ha_Latn_NE">Hausa (Latin, Niger)</option>
	<option value="ha_Latn_NG">Hausa (Latin, Nigeria)</option>
	<option value="ha">Hausa</option>
	<option value="haw_US">Hawaiian (United States)</option>
	<option value="haw">Hawaiian</option>
	<option value="he_IL">Hebrew (Israel)</option>
	<option value="he">Hebrew</option>
	<option value="hi_IN">Hindi (India)</option>
	<option value="hi">Hindi</option>
	<option value="hu_HU">Hungarian (Hungary)</option>
	<option value="hu">Hungarian</option>
	<option value="is_IS">Icelandic (Iceland)</option>
	<option value="is">Icelandic</option>
	<option value="ig_NG">Igbo (Nigeria)</option>
	<option value="ig">Igbo</option>
	<option value="id_ID">Indonesian (Indonesia)</option>
	<option value="id">Indonesian</option>
	<option value="ga_IE">Irish (Ireland)</option>
	<option value="ga">Irish</option>
	<option value="it_IT">Italian (Italy)</option>
	<option value="it_CH">Italian (Switzerland)</option>
	<option value="it">Italian</option>
	<option value="ja_JP">Japanese (Japan)</option>
	<option value="ja">Japanese</option>
	<option value="kea_CV">Kabuverdianu (Cape Verde)</option>
	<option value="kea">Kabuverdianu</option>
	<option value="kab_DZ">Kabyle (Algeria)</option>
	<option value="kab">Kabyle</option>
	<option value="kl_GL">Kalaallisut (Greenland)</option>
	<option value="kl">Kalaallisut</option>
	<option value="kln_KE">Kalenjin (Kenya)</option>
	<option value="kln">Kalenjin</option>
	<option value="kam_KE">Kamba (Kenya)</option>
	<option value="kam">Kamba</option>
	<option value="kn_IN">Kannada (India)</option>
	<option value="kn">Kannada</option>
	<option value="kk_Cyrl">Kazakh (Cyrillic)</option>
	<option value="kk_Cyrl_KZ">Kazakh (Cyrillic, Kazakhstan)</option>
	<option value="kk">Kazakh</option>
	<option value="km_KH">Khmer (Cambodia)</option>
	<option value="km">Khmer</option>
	<option value="ki_KE">Kikuyu (Kenya)</option>
	<option value="ki">Kikuyu</option>
	<option value="rw_RW">Kinyarwanda (Rwanda)</option>
	<option value="rw">Kinyarwanda</option>
	<option value="kok_IN">Konkani (India)</option>
	<option value="kok">Konkani</option>
	<option value="ko_KR">Korean (South Korea)</option>
	<option value="ko">Korean</option>
	<option value="khq_ML">Koyra Chiini (Mali)</option>
	<option value="khq">Koyra Chiini</option>
	<option value="ses_ML">Koyraboro Senni (Mali)</option>
	<option value="ses">Koyraboro Senni</option>
	<option value="lag_TZ">Langi (Tanzania)</option>
	<option value="lag">Langi</option>
	<option value="lv_LV">Latvian (Latvia)</option>
	<option value="lv">Latvian</option>
	<option value="lt_LT">Lithuanian (Lithuania)</option>
	<option value="lt">Lithuanian</option>
	<option value="luo_KE">Luo (Kenya)</option>
	<option value="luo">Luo</option>
	<option value="luy_KE">Luyia (Kenya)</option>
	<option value="luy">Luyia</option>
	<option value="mk_MK">Macedonian (Macedonia)</option>
	<option value="mk">Macedonian</option>
	<option value="jmc_TZ">Machame (Tanzania)</option>
	<option value="jmc">Machame</option>
	<option value="kde_TZ">Makonde (Tanzania)</option>
	<option value="kde">Makonde</option>
	<option value="mg_MG">Malagasy (Madagascar)</option>
	<option value="mg">Malagasy</option>
	<option value="ms_BN">Malay (Brunei)</option>
	<option value="ms_MY">Malay (Malaysia)</option>
	<option value="ms">Malay</option>
	<option value="ml_IN">Malayalam (India)</option>
	<option value="ml">Malayalam</option>
	<option value="mt_MT">Maltese (Malta)</option>
	<option value="mt">Maltese</option>
	<option value="gv_GB">Manx (United Kingdom)</option>
	<option value="gv">Manx</option>
	<option value="mr_IN">Marathi (India)</option>
	<option value="mr">Marathi</option>
	<option value="mas_KE">Masai (Kenya)</option>
	<option value="mas_TZ">Masai (Tanzania)</option>
	<option value="mas">Masai</option>
	<option value="mer_KE">Meru (Kenya)</option>
	<option value="mer">Meru</option>
	<option value="mfe_MU">Morisyen (Mauritius)</option>
	<option value="mfe">Morisyen</option>
	<option value="naq_NA">Nama (Namibia)</option>
	<option value="naq">Nama</option>
	<option value="ne_IN">Nepali (India)</option>
	<option value="ne_NP">Nepali (Nepal)</option>
	<option value="ne">Nepali</option>
	<option value="nd_ZW">North Ndebele (Zimbabwe)</option>
	<option value="nd">North Ndebele</option>
	<option value="nb_NO">Norwegian Bokmål (Norway)</option>
	<option value="nb">Norwegian Bokmål</option>
	<option value="nn_NO">Norwegian Nynorsk (Norway)</option>
	<option value="nn">Norwegian Nynorsk</option>
	<option value="nyn_UG">Nyankole (Uganda)</option>
	<option value="nyn">Nyankole</option>
	<option value="or_IN">Oriya (India)</option>
	<option value="or">Oriya</option>
	<option value="om_ET">Oromo (Ethiopia)</option>
	<option value="om_KE">Oromo (Kenya)</option>
	<option value="om">Oromo</option>
	<option value="ps_AF">Pashto (Afghanistan)</option>
	<option value="ps">Pashto</option>
	<option value="fa_AF">Persian (Afghanistan)</option>
	<option value="fa_IR">Persian (Iran)</option>
	<option value="fa">Persian</option>
	<option value="pl_PL">Polish (Poland)</option>
	<option value="pl">Polish</option>
	<option value="pt_BR">Portuguese (Brazil)</option>
	<option value="pt_GW">Portuguese (Guinea-Bissau)</option>
	<option value="pt_MZ">Portuguese (Mozambique)</option>
	<option value="pt_PT">Portuguese (Portugal)</option>
	<option value="pt">Portuguese</option>
	<option value="pa_Arab">Punjabi (Arabic)</option>
	<option value="pa_Arab_PK">Punjabi (Arabic, Pakistan)</option>
	<option value="pa_Guru">Punjabi (Gurmukhi)</option>
	<option value="pa_Guru_IN">Punjabi (Gurmukhi, India)</option>
	<option value="pa">Punjabi</option>
	<option value="ro_MD">Romanian (Moldova)</option>
	<option value="ro_RO">Romanian (Romania)</option>
	<option value="ro">Romanian</option>
	<option value="rm_CH">Romansh (Switzerland)</option>
	<option value="rm">Romansh</option>
	<option value="rof_TZ">Rombo (Tanzania)</option>
	<option value="rof">Rombo</option>
	<option value="ru_MD">Russian (Moldova)</option>
	<option value="ru_RU">Russian (Russia)</option>
	<option value="ru_UA">Russian (Ukraine)</option>
	<option value="ru">Russian</option>
	<option value="rwk_TZ">Rwa (Tanzania)</option>
	<option value="rwk">Rwa</option>
	<option value="saq_KE">Samburu (Kenya)</option>
	<option value="saq">Samburu</option>
	<option value="sg_CF">Sango (Central African Republic)</option>
	<option value="sg">Sango</option>
	<option value="seh_MZ">Sena (Mozambique)</option>
	<option value="seh">Sena</option>
	<option value="sr_Cyrl">Serbian (Cyrillic)</option>
	<option value="sr_Cyrl_BA">Serbian (Cyrillic, Bosnia and Herzegovina)</option>
	<option value="sr_Cyrl_ME">Serbian (Cyrillic, Montenegro)</option>
	<option value="sr_Cyrl_RS">Serbian (Cyrillic, Serbia)</option>
	<option value="sr_Latn">Serbian (Latin)</option>
	<option value="sr_Latn_BA">Serbian (Latin, Bosnia and Herzegovina)</option>
	<option value="sr_Latn_ME">Serbian (Latin, Montenegro)</option>
	<option value="sr_Latn_RS">Serbian (Latin, Serbia)</option>
	<option value="sr">Serbian</option>
	<option value="sn_ZW">Shona (Zimbabwe)</option>
	<option value="sn">Shona</option>
	<option value="ii_CN">Sichuan Yi (China)</option>
	<option value="ii">Sichuan Yi</option>
	<option value="si_LK">Sinhala (Sri Lanka)</option>
	<option value="si">Sinhala</option>
	<option value="sk_SK">Slovak (Slovakia)</option>
	<option value="sk">Slovak</option>
	<option value="sl_SI">Slovenian (Slovenia)</option>
	<option value="sl">Slovenian</option>
	<option value="xog_UG">Soga (Uganda)</option>
	<option value="xog">Soga</option>
	<option value="so_DJ">Somali (Djibouti)</option>
	<option value="so_ET">Somali (Ethiopia)</option>
	<option value="so_KE">Somali (Kenya)</option>
	<option value="so_SO">Somali (Somalia)</option>
	<option value="so">Somali</option>
	<option value="es_AR">Spanish (Argentina)</option>
	<option value="es_BO">Spanish (Bolivia)</option>
	<option value="es_CL">Spanish (Chile)</option>
	<option value="es_CO">Spanish (Colombia)</option>
	<option value="es_CR">Spanish (Costa Rica)</option>
	<option value="es_DO">Spanish (Dominican Republic)</option>
	<option value="es_EC">Spanish (Ecuador)</option>
	<option value="es_SV">Spanish (El Salvador)</option>
	<option value="es_GQ">Spanish (Equatorial Guinea)</option>
	<option value="es_GT">Spanish (Guatemala)</option>
	<option value="es_HN">Spanish (Honduras)</option>
	<option value="es_419">Spanish (Latin America)</option>
	<option value="es_MX">Spanish (Mexico)</option>
	<option value="es_NI">Spanish (Nicaragua)</option>
	<option value="es_PA">Spanish (Panama)</option>
	<option value="es_PY">Spanish (Paraguay)</option>
	<option value="es_PE">Spanish (Peru)</option>
	<option value="es_PR">Spanish (Puerto Rico)</option>
	<option value="es_ES">Spanish (Spain)</option>
	<option value="es_US">Spanish (United States)</option>
	<option value="es_UY">Spanish (Uruguay)</option>
	<option value="es_VE">Spanish (Venezuela)</option>
	<option value="es">Spanish</option>
	<option value="sw_KE">Swahili (Kenya)</option>
	<option value="sw_TZ">Swahili (Tanzania)</option>
	<option value="sw">Swahili</option>
	<option value="sv_FI">Swedish (Finland)</option>
	<option value="sv_SE">Swedish (Sweden)</option>
	<option value="sv">Swedish</option>
	<option value="gsw_CH">Swiss German (Switzerland)</option>
	<option value="gsw">Swiss German</option>
	<option value="shi_Latn">Tachelhit (Latin)</option>
	<option value="shi_Latn_MA">Tachelhit (Latin, Morocco)</option>
	<option value="shi_Tfng">Tachelhit (Tifinagh)</option>
	<option value="shi_Tfng_MA">Tachelhit (Tifinagh, Morocco)</option>
	<option value="shi">Tachelhit</option>
	<option value="dav_KE">Taita (Kenya)</option>
	<option value="dav">Taita</option>
	<option value="ta_IN">Tamil (India)</option>
	<option value="ta_LK">Tamil (Sri Lanka)</option>
	<option value="ta">Tamil</option>
	<option value="te_IN">Telugu (India)</option>
	<option value="te">Telugu</option>
	<option value="teo_KE">Teso (Kenya)</option>
	<option value="teo_UG">Teso (Uganda)</option>
	<option value="teo">Teso</option>
	<option value="th_TH">Thai (Thailand)</option>
	<option value="th">Thai</option>
	<option value="bo_CN">Tibetan (China)</option>
	<option value="bo_IN">Tibetan (India)</option>
	<option value="bo">Tibetan</option>
	<option value="ti_ER">Tigrinya (Eritrea)</option>
	<option value="ti_ET">Tigrinya (Ethiopia)</option>
	<option value="ti">Tigrinya</option>
	<option value="to_TO">Tonga (Tonga)</option>
	<option value="to">Tonga</option>
	<option value="tr_TR">Turkish (Turkey)</option>
	<option value="tr">Turkish</option>
	<option value="uk_UA">Ukrainian (Ukraine)</option>
	<option value="uk">Ukrainian</option>
	<option value="ur_IN">Urdu (India)</option>
	<option value="ur_PK">Urdu (Pakistan)</option>
	<option value="ur">Urdu</option>
	<option value="uz_Arab">Uzbek (Arabic)</option>
	<option value="uz_Arab_AF">Uzbek (Arabic, Afghanistan)</option>
	<option value="uz_Cyrl">Uzbek (Cyrillic)</option>
	<option value="uz_Cyrl_UZ">Uzbek (Cyrillic, Uzbekistan)</option>
	<option value="uz_Latn">Uzbek (Latin)</option>
	<option value="uz_Latn_UZ">Uzbek (Latin, Uzbekistan)</option>
	<option value="uz">Uzbek</option>
	<option value="vi_VN">Vietnamese (Vietnam)</option>
	<option value="vi">Vietnamese</option>
	<option value="vun_TZ">Vunjo (Tanzania)</option>
	<option value="vun">Vunjo</option>
	<option value="cy_GB">Welsh (United Kingdom)</option>
	<option value="cy">Welsh</option>
	<option value="yo_NG">Yoruba (Nigeria)</option>
	<option value="yo">Yoruba</option>
	<option value="zu_ZA">Zulu (South Africa)</option>
	<option value="zu">Zulu</option>
	</select>
    </td>
  </tr>
  <tr>
    <td>OG Type</td>
    <td>
	<select data-plugin-selectTwo class="form-control populate" name="OG_type">


<optgroup label="Activities">


	<option value="activity">Activity</option>
	<option value="sport">Sport</option>
														</optgroup>
<optgroup label="Businesses">

	<option value="bar">Bar</option>
	<option value="company">Company</option>
	<option value="cafe">Café</option>
	<option value="hotel">Hotel</option>
	<option value="restaurant">Restaurant</option>
														</optgroup>
<optgroup label="Groups">

	<option value="cause">Cause</option>
	<option value="sports_league">Sports League</option>
	<option value="sports_team">Sports Team</option>
														</optgroup>
<optgroup label="Organizations">

	<option value="band">Band</option>
	<option value="government">Government</option>
	<option value="non_profit">Non-Profit</option>
	<option value="school">School</option>
	<option value="university">University</option>
														</optgroup>
<optgroup label="People">

	<option value="actor">Actor</option>
	<option value="athlete">Athlete</option>
	<option value="author">Author</option>
	<option value="director">Director</option>
	<option value="musician">Musician</option>
	<option value="politician">Politician</option>
	<option value="profile">Profile</option>
	<option value="public_figure">Public Figure</option>
														</optgroup>
<optgroup label="Places">

	<option value="city">City</option>
	<option value="country">Country</option>
	<option value="landmark">Landmark</option>
	<option value="state_province">State / Province</option>
														</optgroup>
<optgroup label="Products and Entertainment">

	<option value="album">Album</option>
	<option value="book">Book</option>
	<option value="drink">Drink</option>
	<option value="food">Food</option>
	<option value="game">Game</option>
	<option value="movie">Movie</option>
	<option value="product">Product</option>
	<option value="song">Song</option>
	<option value="tv_show">TV Show</option>
														</optgroup>
<optgroup label="Websites">

	<option value="article">Article</option>
	<option value="blog">Blog</option>
	<option value="website" selected="selected">Website</option>
														</optgroup>
													</select>



    </td>
  </tr>
  <tr>
    <td>OG Description</td>
    <td>
      <input type="text" class="form-control" value="Description for this page must be updated" name="OG_desc">
    </td>
  </tr>
  <tr>
    <td>OG Section</td>
    <td>
      <input type="text" class="form-control" value="epg" name="OG_section">
    </td>
  </tr>
  <tr>
    <td>Sidebar Config</td>
    <td>
      <input type="text" class="form-control" value="0" name="sidebar_config">
    </td>
  </tr>
  <tr>
    <td>Lookup Table</td>
    <td>
    
    <select class="form-control mb-md" name="lookup_table">
			<option value="" selected="selected">NONE</option>
    
    <?php 
    
    $table_lookup_result = mysqli_query($con,"show tables"); // run the query and assign the result to $result
	while($table_lookup_table = mysqli_fetch_array($table_lookup_result)) { // go through each row that was returned in $result
		    // print the table that was returned on that row.
		    $this_table_name = $table_lookup_table[0];
		
		?>
			<option value="<?php echo $this_table_name; ?>"><?php echo $this_table_name; ?></option>
		<?php
	}
	
    ?>

	</select>
    
    </td>
  </tr>
</table>
</div>

<!-- REMOVED SUB-PAGES SECTION FOR NEW PAGE -->

</div>
<input type="hidden" value="<?php echo $this_page_ID; ?>" name="page_ID" />
</form>
												</div>
												<?php /* 
												<div class="modal-footer">
													<a href="<?php echo $this_page_filename; ?>" type="button" class="mb-xs mt-xs mr-xs btn btn-success"><i class="fa fa-arrow-right"></i> Launch Page</a>
													<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
												</div>
												*/ 
												?>
												
												
												<footer class="panel-footer">
													<?php form_buttons('index', $record_id); ?>
						
												</footer>
												
<!-- END OF MODEL POP-UP CONTAINING PAGE INFO -->
<!-- now close the panel -->
								</div>
							</section>
							
						  </form>
						</div>
					</div> <!-- end row! -->

					<!-- end: page -->

<?php
// now close the page out:
pagefoot($page_id);

?>
