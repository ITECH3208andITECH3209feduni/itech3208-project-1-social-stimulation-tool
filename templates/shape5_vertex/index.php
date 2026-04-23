<?php
require('vertex/cms_core_functions.php');
s5_restricted_access_call();
/*
-----------------------------------------
Vertex - January 2012 - Shape 5 Club Design
-----------------------------------------
Site:      shape5.com
Email:     contact@shape5.com
@license:  GPL
@copyright (C) 2012 Shape 5 LLC

*/

?>
<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml" <?php s5_language_call(); ?>>
<head>
<?php s5_head_call(); ?>
<?php
require("vertex/parameters.php");
require("vertex/general_functions.php");
require("vertex/module_calcs.php");
require("vertex/includes/vertex_includes_header.php");
?>

<?php if ($browser == "ie7" || $browser == "ie8") { ?>
<link href="<?php echo $s5_directory_path ?>/css/IECSS3.css" rel="stylesheet" type="text/css"  />
<?php } ?>

<?php if ($browser == "ie9") { ?>
<link href="<?php echo $s5_directory_path ?>/css/IE9.css" rel="stylesheet" type="text/css"  />
<?php } ?>

<?php if(($s5_fonts_highlight != "Arial") && ($s5_fonts_highlight != "Helvetica") && ($s5_fonts_highlight != "Sans-Serif")) { ?>
<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=<?php echo $s5_fonts_highlight ?>" />
<?php } ?>

<style type="text/css"> 
body {font-family: '<?php echo $s5_fonts;?>',Helvetica,Arial,Sans-Serif ;
background:#<?php echo $s5_page_bg ?>;
} 

#s5_search input, #s5_menu_wrap, .s5_mod_h3, #subMenusContainer, h2 {
font-family: <?php echo $s5_fonts_highlight ?>;
}

<?php if ($s5_uppercase == "yes") { ?>
#s5_menu_wrap, .s5_mod_h3 {
text-transform:uppercase;
}
<?php } ?>


<?php if ($s5_small_menu == "yes") { ?>
#s5_menu_outer_wrap {
height:40px;
overflow:hidden;
}
.S5_parent_subtext {
display:none;
}
#s5_nav li {
height:37px;
}
#s5_search input {
margin-top:0px;
}
<?php } ?>

#s5_header_area_inner2, .module_round_box, .module_round_box-dark, #s5_component_wrap, #s5_footer_area_inner2 {
-webkit-box-shadow: 0 0px 8px #<?php echo $s5_shadow_color ?>;
-moz-box-shadow: 0 0px 8px #<?php echo $s5_shadow_color ?>;
box-shadow: 0 0px 8px #<?php echo $s5_shadow_color ?>; 
}

a, .module_round_box .s5_h3_first, .module_round_box-none .s5_h3_first, .module_round_box ul.menu .current a, h2, h4, #s5_md_outer_wrap h3 {
color:#<?php echo $s5_highlight ?>;
}

#s5_nav li.active a, #s5_nav li.mainMenuParentBtnFocused a, #s5_nav li:hover a, .btn-link {
color:#<?php echo $s5_highlight ?>;
}

.btn-primary, .button, li.pagenav-next, li.pagenav-prev, .validate, .dropdown-menu li > a:hover, .dropdown-menu li > a:focus, .dropdown-submenu:hover > a, .dropdown-menu .active > a, .dropdown-menu .active > a:hover, .nav-list > .active > a, .nav-list > .active > a:hover, .nav-pills > .active > a, .nav-pills > .active > a:hover, .btn-group.open .btn-primary.dropdown-toggle, .pager a {
background:#<?php echo $s5_highlight ?>;
}

#subMenusContainer div.s5_sub_wrap ul, #subMenusContainer div.s5_sub_wrap_rtl ul, #subMenusContainer div.s5_sub_wrap_lower ul, #subMenusContainer div.s5_sub_wrap_lower_rtl ul {
border-bottom:solid 3px #<?php echo $s5_highlight ?>;
}

<?php if (($s5_register == "") && ($s5_login  == "") && ($s5_pos_breadcrumb == "unpublished") && ($s5_font_resizer == "no") && ($s5_linked_in  == "") && ($s5_rss  == "") && ($s5_twitter  == "") && ($s5_facebook  == "") && ($s5_google  == "")) { ?>
#s5_menu_wrap {
-moz-border-radius:0px 0px 6px 6px;
-webkit-border-radius:0px 0px 6px 6px;
border-radius:0px 0px 6px 6px;
}
<?php } ?>

<?php if ($browser == "ie7" || $browser == "ie8" || $browser == "ie9") { ?>
.s5_lr_tab_inner {writing-mode: bt-rl;filter: flipV flipH;}
<?php } ?>

<?php if ($browser == "ie7" || $browser == "ie8") { ?>
#s5_header_area_inner2, #s5_header_wrap, #s5_search input, .module_round_box, .module_round_box-dark, #s5_component_wrap, #s5_footer_area_inner2, .padded, .padded:hover {
behavior: url(<?php echo $s5_directory_path ?>/css/PIE.htc);
position:relative;
}
<?php } ?>

<?php if($s5_thirdparty == "enabled") { ?>
/* k2 stuff */
div.itemHeader h2.itemTitle, div.catItemHeader h3.catItemTitle, h3.userItemTitle a, #comments-form p, #comments-report-form p, #comments-form span, #comments-form .counter, #comments .comment-author, #comments .author-homepage,
#comments-form p, #comments-form #comments-form-buttons, #comments-form #comments-form-error, #comments-form #comments-form-captcha-holder {font-family: '<?php echo $s5_fonts;?>',Helvetica,Arial,Sans-Serif ;} 
<?php } ?>	
.s5_wrap{width:<?php echo $s5_body_width; echo $s5_fixed_fluid ?>;}	
</style>
</head>

<body id="s5_body">

<div id="s5_scrolltotop"></div>

<!-- Top Vertex Calls -->
<?php require("vertex/includes/vertex_includes_top.php"); ?>

<!-- Body Padding Div Used For Responsive Spacing -->		
<div id="s5_body_padding">

	<!-- Header -->			
		<header id="s5_header_area1">		
		<div id="s5_header_area2">	
		<div id="s5_header_area_inner" class="s5_wrap">		
		<div id="s5_header_area_inner2">	
			<div id="s5_header_wrap">
			
				<?php if($s5_pos_logo == "published") { ?>
					<div id="s5_logo_module" style="height:<?php echo $s5_logo_height ?>px;width:<?php echo $s5_logo_width ?>px">
						<?php s5_module_call('logo','notitle'); ?>
					</div>	
				<?php } else { ?>
					<img alt="logo"  style="height:<?php echo $s5_logo_height ?>px;width:<?php echo $s5_logo_width ?>px" src="<?php echo $s5_directory_path ?>/images/s5_logo.png" id="s5_logo" onclick="window.document.location.href='<?php echo $LiveSiteUrl ?>'" />
				<?php } ?>
				
				<?php if($s5_pos_banner == "published") { ?>
					<div id="s5_banner" style="padding-left:<?php echo $s5_logo_width + 10 ?>px">
						<?php s5_module_call('banner','raw'); ?>
					</div>
				<?php } ?>
				
				<div style="clear:both; height:0px"></div>			
			</div>
			
			<?php if ($s5_show_menu == "show" || $s5_pos_search == "published") { ?>
				<nav id="s5_menu_wrap">
					<?php if ($s5_show_menu == "show") { ?>
						<?php include("vertex/s5flex_menu/default.php"); ?>
					<?php } ?>
					<?php if($s5_pos_search == "published") { ?>
						<div id="s5_search">
							<?php s5_module_call('search','notitle'); ?>
						</div>
					<?php } ?>
					<div style="clear:both; height:0px"></div>
				</nav>
			<?php } ?>
			
			<?php if (($s5_register  != "") || ($s5_login  != "") || ($s5_pos_breadcrumb == "published") || ($s5_font_resizer == "yes") || ($s5_linked_in  != "") || ($s5_rss  != "") || ($s5_twitter  != "") || ($s5_facebook  != "") || ($s5_google  != "")) { ?>
				<div id="s5_breadcrumb_fonts_wrap">
					<?php if ($s5_pos_breadcrumb == "published") { ?>
						<div id="s5_breadcrumb_wrap">
							<?php s5_module_call('breadcrumb','notitle'); ?>
						</div>
					<?php } ?>
					
					<?php if (($s5_font_resizer == "yes") || ($s5_register  != "") || ($s5_login  != "") || ($s5_linked_in  != "") || ($s5_rss  != "") || ($s5_twitter  != "") || ($s5_facebook  != "") || ($s5_google  != "")) { ?>
						<div id="s5_social_wrap1">
							<div id="s5_social_wrap_inner">
							
								<?php if ($s5_facebook  != "") { ?>
									<div id="s5_facebook" onclick="window.open('<?php echo $s5_facebook; ?>')"></div>
								<?php } ?>	
								<?php if ($s5_google  != "") { ?>
									<div id="s5_google" onclick="window.open('<?php echo $s5_google; ?>')"></div>
								<?php } ?>	
								<?php if ($s5_twitter  != "") { ?>
									<div id="s5_twitter" onclick="window.open('<?php echo $s5_twitter; ?>')"></div>
								<?php } ?>
								<?php if ($s5_linked_in  != "") { ?>
									<div id="s5_linked_in" onclick="window.open('<?php echo $s5_linked_in; ?>')"></div>
								<?php } ?>
								<?php if ($s5_rss  != "") { ?>
									<div id="s5_rss" onclick="window.open('<?php echo $s5_rss; ?>')"></div>
								<?php } ?>	
								
								<?php if (($s5_login  != "") || ($s5_register  != "")) { ?>	
									<div id="s5_loginreg">	
											<div id="s5_logregtm">
											
												<?php if ($s5_login  != "") { ?>
													<a href="<?php echo JRoute::_('index.php?option=com_users&view=login'); ?>" id="s5_login">
														<?php if ($s5_user_id) { echo $s5_loginout; } else { echo $s5_login; } ?>
													</a>
												<?php } ?>
											
												<?php if ($s5_user_id) { } else if ($s5_register != "") {?>
													<a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>" id="s5_register">
														<?php echo $s5_register ?>
													</a>
												<?php } ?>

											</div>
									
									</div>
								<?php } ?>
								
								<?php if($s5_font_resizer == "yes") { ?>
									<div id="fontControls"></div>
								<?php } ?>
							
							</div>
						</div>
					<?php } ?>
				
				<div style="clear:both;height:0px"></div>
				</div>
			
			<?php } ?>
			
		</div>
		</div>
		</div>
		</header>
	<!-- End Header -->	
	
		
	
	<!-- Top Row1 -->	
		<?php if ($s5_pos_top_row1_1 == "published" || $s5_pos_top_row1_2 == "published" || $s5_pos_top_row1_3 == "published" || $s5_pos_top_row1_4 == "published" || $s5_pos_top_row1_5 == "published" || $s5_pos_top_row1_6 == "published") { ?>
			<section id="s5_top_row1_area1">
			<div id="s5_top_row1_area2">
			<div id="s5_top_row1_area_inner" class="s5_wrap">

				<div id="s5_top_row1_wrap">
					<div id="s5_top_row1">
					<div id="s5_top_row1_inner">
					
						<?php if ($s5_pos_top_row1_1 == "published") { ?>
							<div id="s5_pos_top_row1_1" class="s5_float_left" style="width:<?php echo $s5_pos_top_row1_1_width ?>%">
								<?php s5_module_call('top_row1_1','round_box'); ?>
							</div>
						<?php } ?>
						
						<?php if ($s5_pos_top_row1_2 == "published") { ?>
							<div id="s5_pos_top_row1_2" class="s5_float_left" style="width:<?php echo $s5_pos_top_row1_2_width ?>%">
								<?php s5_module_call('top_row1_2','round_box'); ?>
							</div>
						<?php } ?>
						
						<?php if ($s5_pos_top_row1_3 == "published") { ?>
							<div id="s5_pos_top_row1_3" class="s5_float_left" style="width:<?php echo $s5_pos_top_row1_3_width ?>%">
								<?php s5_module_call('top_row1_3','round_box'); ?>
							</div>
						<?php } ?>
						
						<?php if ($s5_pos_top_row1_4 == "published") { ?>
							<div id="s5_pos_top_row1_4" class="s5_float_left" style="width:<?php echo $s5_pos_top_row1_4_width ?>%">
								<?php s5_module_call('top_row1_4','round_box'); ?>
							</div>
						<?php } ?>
						
						<?php if ($s5_pos_top_row1_5 == "published") { ?>
							<div id="s5_pos_top_row1_5" class="s5_float_left" style="width:<?php echo $s5_pos_top_row1_5_width ?>%">
								<?php s5_module_call('top_row1_5','round_box'); ?>
							</div>
						<?php } ?>
						
						<?php if ($s5_pos_top_row1_6 == "published") { ?>
							<div id="s5_pos_top_row1_6" class="s5_float_left" style="width:<?php echo $s5_pos_top_row1_6_width ?>%">
								<?php s5_module_call('top_row1_6','round_box'); ?>
							</div>
						<?php } ?>
						
						<div style="clear:both; height:0px"></div>

					</div>
					</div>
				</div>

		</div>
		</div>
		</section>
		<?php } ?>
	<!-- End Top Row1 -->	
		
		
		
	<!-- Top Row2 -->	
		<?php if ($s5_pos_top_row2_1 == "published" || $s5_pos_top_row2_2 == "published" || $s5_pos_top_row2_3 == "published" || $s5_pos_top_row2_4 == "published" || $s5_pos_top_row2_5 == "published" || $s5_pos_top_row2_6 == "published") { ?>
		<section id="s5_top_row2_area1">
		<div id="s5_top_row2_area2">
		<div id="s5_top_row2_area_inner" class="s5_wrap">			
		
			<div id="s5_top_row2_wrap">
				<div id="s5_top_row2">
				<div id="s5_top_row2_inner">					
					<?php if ($s5_pos_top_row2_1 == "published") { ?>
						<div id="s5_pos_top_row2_1" class="s5_float_left" style="width:<?php echo $s5_pos_top_row2_1_width ?>%">
							<?php s5_module_call('top_row2_1','round_box'); ?>
						</div>
					<?php } ?>
					
					<?php if ($s5_pos_top_row2_2 == "published") { ?>
						<div id="s5_pos_top_row2_2" class="s5_float_left" style="width:<?php echo $s5_pos_top_row2_2_width ?>%">
							<?php s5_module_call('top_row2_2','round_box'); ?>
						</div>
					<?php } ?>
					
					<?php if ($s5_pos_top_row2_3 == "published") { ?>
						<div id="s5_pos_top_row2_3" class="s5_float_left" style="width:<?php echo $s5_pos_top_row2_3_width ?>%">
							<?php s5_module_call('top_row2_3','round_box'); ?>
						</div>
					<?php } ?>
					
					<?php if ($s5_pos_top_row2_4 == "published") { ?>
						<div id="s5_pos_top_row2_4" class="s5_float_left" style="width:<?php echo $s5_pos_top_row2_4_width ?>%">
							<?php s5_module_call('top_row2_4','round_box'); ?>
						</div>
					<?php } ?>
					
					<?php if ($s5_pos_top_row2_5 == "published") { ?>
						<div id="s5_pos_top_row2_5" class="s5_float_left" style="width:<?php echo $s5_pos_top_row2_5_width ?>%">
							<?php s5_module_call('top_row2_5','round_box'); ?>
						</div>
					<?php } ?>
					
					<?php if ($s5_pos_top_row2_6 == "published") { ?>
						<div id="s5_pos_top_row2_6" class="s5_float_left" style="width:<?php echo $s5_pos_top_row2_6_width ?>%">
							<?php s5_module_call('top_row2_6','round_box'); ?>
						</div>
					<?php } ?>						
					<div style="clear:both; height:0px"></div>
				</div>
				</div>	
			</div>	
				
		</div>
		</div>
		</section>
		<?php } ?>
	<!-- End Top Row2 -->
	
	
	
	<!-- Top Row3 -->	
		<?php if ($s5_pos_top_row3_1 == "published" || $s5_pos_top_row3_2 == "published" || $s5_pos_top_row3_3 == "published" || $s5_pos_top_row3_4 == "published" || $s5_pos_top_row3_5 == "published" || $s5_pos_top_row3_6 == "published") { ?>
		<section id="s5_top_row3_area1">	
		<div id="s5_top_row3_area2">
		<div id="s5_top_row3_area_inner" class="s5_wrap">
		
			<div id="s5_top_row3_wrap">
				<div id="s5_top_row3">
				<div id="s5_top_row3_inner">
				
					<?php if ($s5_pos_top_row3_1 == "published") { ?>
						<div id="s5_pos_top_row3_1" class="s5_float_left" style="width:<?php echo $s5_pos_top_row3_1_width ?>%">
							<?php s5_module_call('top_row3_1','round_box'); ?>
						</div>
					<?php } ?>
					
					<?php if ($s5_pos_top_row3_2 == "published") { ?>
						<div id="s5_pos_top_row3_2" class="s5_float_left" style="width:<?php echo $s5_pos_top_row3_2_width ?>%">
							<?php s5_module_call('top_row3_2','round_box'); ?>
						</div>
					<?php } ?>
					
					<?php if ($s5_pos_top_row3_3 == "published") { ?>
						<div id="s5_pos_top_row3_3" class="s5_float_left" style="width:<?php echo $s5_pos_top_row3_3_width ?>%">
							<?php s5_module_call('top_row3_3','round_box'); ?>
						</div>
					<?php } ?>
					
					<?php if ($s5_pos_top_row3_4 == "published") { ?>
						<div id="s5_pos_top_row3_4" class="s5_float_left" style="width:<?php echo $s5_pos_top_row3_4_width ?>%">
							<?php s5_module_call('top_row3_4','round_box'); ?>
						</div>
					<?php } ?>
					
					<?php if ($s5_pos_top_row3_5 == "published") { ?>
						<div id="s5_pos_top_row3_5" class="s5_float_left" style="width:<?php echo $s5_pos_top_row3_5_width ?>%">
							<?php s5_module_call('top_row3_5','round_box'); ?>
						</div>
					<?php } ?>
					
					<?php if ($s5_pos_top_row3_6 == "published") { ?>
						<div id="s5_pos_top_row3_6" class="s5_float_left" style="width:<?php echo $s5_pos_top_row3_6_width ?>%">
							<?php s5_module_call('top_row3_6','round_box'); ?>
						</div>
					<?php } ?>						
					<div style="clear:both; height:0px"></div>

				</div>
				</div>
			</div>

		</div>
		</div>
		</section>
		<?php } ?>
	<!-- End Top Row3 -->	
		
		
		
	<!-- Center area -->	
		<?php if ($s5_show_component == "yes" || $s5_pos_left_top == "published" || $s5_pos_left == "published" || $s5_pos_left_inset == "published" || $s5_pos_left_bottom == "published" || $s5_pos_right_top == "published" || $s5_pos_right == "published" || $s5_pos_right_inset == "published" || $s5_pos_right_bottom == "published" || $s5_pos_middle_top_1 == "published" || $s5_pos_middle_top_2 == "published" || $s5_pos_middle_top_3 == "published" || $s5_pos_middle_top_4 == "published" || $s5_pos_middle_top_5 == "published" || $s5_pos_middle_top_6 == "published" || $s5_pos_above_body_1 == "published" || $s5_pos_above_body_2 == "published" || $s5_pos_above_body_3 == "published" || $s5_pos_above_body_4 == "published" || $s5_pos_above_body_5 == "published" || $s5_pos_above_body_6 == "published" || $s5_pos_middle_bottom_1 == "published" || $s5_pos_middle_bottom_2 == "published" || $s5_pos_middle_bottom_3 == "published" || $s5_pos_middle_bottom_4 == "published" || $s5_pos_middle_bottom_5 == "published" || $s5_pos_middle_bottom_6 == "published" || $s5_pos_below_body_1 == "published" || $s5_pos_below_body_2 == "published" || $s5_pos_below_body_3 == "published" || $s5_pos_below_body_4 == "published" || $s5_pos_below_body_5 == "published" || $s5_pos_below_body_6 == "published" || $s5_pos_above_columns_1 == "published" ||  $s5_pos_above_columns_2 == "published" ||  $s5_pos_above_columns_3 == "published" ||  $s5_pos_above_columns_4 == "published" ||  $s5_pos_above_columns_5 == "published" ||  $s5_pos_above_columns_6 == "published" ||  $s5_pos_below_columns_1 == "published" ||  $s5_pos_below_columns_2 == "published" ||  $s5_pos_below_columns_3 == "published" ||  $s5_pos_below_columns_4 == "published" ||  $s5_pos_below_columns_5 == "published" ||  $s5_pos_below_columns_6 == "published") { ?>
		<section id="s5_center_area1">
		<div id="s5_center_area2">
		<div id="s5_center_area_inner" class="s5_wrap">
		
		<!-- Above Columns Wrap -->	
			<?php if ($s5_pos_above_columns_1 == "published" || $s5_pos_above_columns_2 == "published" || $s5_pos_above_columns_3 == "published" || $s5_pos_above_columns_4 == "published" || $s5_pos_above_columns_5 == "published" || $s5_pos_above_columns_6 == "published") { ?>
			<div id="s5_above_columns_wrap1">	
			<div id="s5_above_columns_wrap2">
			<div id="s5_above_columns_inner" class="s5_wrap">

					
						<?php if ($s5_pos_above_columns_1 == "published") { ?>
							<div id="s5_above_columns_1" class="s5_float_left" style="width:<?php echo $s5_pos_above_columns_1_width ?>%">
								<?php s5_module_call('above_columns_1','round_box'); ?>
							</div>
						<?php } ?>
						
						<?php if ($s5_pos_above_columns_2 == "published") { ?>
							<div id="s5_above_columns_2" class="s5_float_left" style="width:<?php echo $s5_pos_above_columns_2_width ?>%">
								<?php s5_module_call('above_columns_2','round_box'); ?>
							</div>
						<?php } ?>
						
						<?php if ($s5_pos_above_columns_3 == "published") { ?>
							<div id="s5_above_columns_3" class="s5_float_left" style="width:<?php echo $s5_pos_above_columns_3_width ?>%">
								<?php s5_module_call('above_columns_3','round_box'); ?>
							</div>
						<?php } ?>
						
						<?php if ($s5_pos_above_columns_4 == "published") { ?>
							<div id="s5_above_columns_4" class="s5_float_left" style="width:<?php echo $s5_pos_above_columns_4_width ?>%">
								<?php s5_module_call('above_columns_4','round_box'); ?>
							</div>
						<?php } ?>
						
						<?php if ($s5_pos_above_columns_5 == "published") { ?>
							<div id="s5_above_columns_5" class="s5_float_left" style="width:<?php echo $s5_pos_above_columns_5_width ?>%">
								<?php s5_module_call('above_columns_5','round_box'); ?>
							</div>
						<?php } ?>
						
						<?php if ($s5_pos_above_columns_6 == "published") { ?>
							<div id="s5_above_columns_6" class="s5_float_left" style="width:<?php echo $s5_pos_above_columns_6_width ?>%">
								<?php s5_module_call('above_columns_6','round_box'); ?>
							</div>
						<?php } ?>						
						<div style="clear:both; height:0px"></div>

			</div>
			</div>
			</div>
			<?php } ?>
		<!-- End Above Columns Wrap -->			
				
			<!-- Columns wrap, contains left, right and center columns -->	
			<div id="s5_columns_wrap">
			<div id="s5_columns_wrap_inner">
				
				<div id="s5_center_column_wrap">
				<div id="s5_center_column_wrap_inner" style="margin-left:<?php echo $s5_center_column_margin_left ?>px; margin-right:<?php echo $s5_center_column_margin_right ?>px;">
					
					<?php if ($s5_pos_middle_top_1 == "published" || $s5_pos_middle_top_2 == "published" || $s5_pos_middle_top_3 == "published" || $s5_pos_middle_top_4 == "published" || $s5_pos_middle_top_5 == "published" || $s5_pos_middle_top_6 == "published") { ?>
					
						<div id="s5_middle_top_wrap">
							
							<div id="s5_middle_top">
							<div id="s5_middle_top_inner">
							
								<?php if ($s5_pos_middle_top_1 == "published") { ?>
									<div id="s5_pos_middle_top_1" class="s5_float_left" style="width:<?php echo $s5_pos_middle_top_1_width ?>%">
										<?php s5_module_call('middle_top_1','round_box'); ?>
									</div>
								<?php } ?>
								
								<?php if ($s5_pos_middle_top_2 == "published") { ?>
									<div id="s5_pos_middle_top_2" class="s5_float_left" style="width:<?php echo $s5_pos_middle_top_2_width ?>%">
										<?php s5_module_call('middle_top_2','round_box'); ?>
									</div>
								<?php } ?>
								
								<?php if ($s5_pos_middle_top_3 == "published") { ?>
									<div id="s5_pos_middle_top_3" class="s5_float_left" style="width:<?php echo $s5_pos_middle_top_3_width ?>%">
										<?php s5_module_call('middle_top_3','round_box'); ?>
									</div>
								<?php } ?>
								
								<?php if ($s5_pos_middle_top_4 == "published") { ?>
									<div id="s5_pos_middle_top_4" class="s5_float_left" style="width:<?php echo $s5_pos_middle_top_4_width ?>%">
										<?php s5_module_call('middle_top_4','round_box'); ?>
									</div>
								<?php } ?>
								
								<?php if ($s5_pos_middle_top_5 == "published") { ?>
									<div id="s5_pos_middle_top_5" class="s5_float_left" style="width:<?php echo $s5_pos_middle_top_5_width ?>%">
										<?php s5_module_call('middle_top_5','round_box'); ?>
									</div>
								<?php } ?>
								
								<?php if ($s5_pos_middle_top_6 == "published") { ?>
									<div id="s5_pos_middle_top_6" class="s5_float_left" style="width:<?php echo $s5_pos_middle_top_6_width ?>%">
										<?php s5_module_call('middle_top_6','round_box'); ?>
									</div>
								<?php } ?>						
								<div style="clear:both; height:0px"></div>

							</div>
							</div>
						
						</div>

					<?php } ?>
					
					<?php if ($s5_show_component == "yes" || $s5_pos_above_body_1 == "published" || $s5_pos_above_body_2 == "published" || $s5_pos_above_body_3 == "published" || $s5_pos_above_body_4 == "published" || $s5_pos_above_body_5 == "published" || $s5_pos_above_body_6 == "published" || $s5_pos_below_body_1 == "published" || $s5_pos_below_body_2 == "published" || $s5_pos_below_body_3 == "published" || $s5_pos_below_body_4 == "published" || $s5_pos_below_body_5 == "published" || $s5_pos_below_body_6 == "published") { ?>
						
						<div id="s5_component_wrap">
						<div id="s5_component_wrap_inner">
						
							<?php if ($s5_pos_above_body_1 == "published" || $s5_pos_above_body_2 == "published" || $s5_pos_above_body_3 == "published" || $s5_pos_above_body_4 == "published" || $s5_pos_above_body_5 == "published" || $s5_pos_above_body_6 == "published") { ?>
						
								<div id="s5_above_body_wrap">
									
									<div id="s5_above_body">
									<div id="s5_above_body_inner">
									
										<?php if ($s5_pos_above_body_1 == "published") { ?>
											<div id="s5_pos_above_body_1" class="s5_float_left" style="width:<?php echo $s5_pos_above_body_1_width ?>%">
												<?php s5_module_call('above_body_1','fourdivs'); ?>
											</div>
										<?php } ?>
										
										<?php if ($s5_pos_above_body_2 == "published") { ?>
											<div id="s5_pos_above_body_2" class="s5_float_left" style="width:<?php echo $s5_pos_above_body_2_width ?>%">
												<?php s5_module_call('above_body_2','fourdivs'); ?>
											</div>
										<?php } ?>
										
										<?php if ($s5_pos_above_body_3 == "published") { ?>
											<div id="s5_pos_above_body_3" class="s5_float_left" style="width:<?php echo $s5_pos_above_body_3_width ?>%">
												<?php s5_module_call('above_body_3','fourdivs'); ?>
											</div>
										<?php } ?>
										
										<?php if ($s5_pos_above_body_4 == "published") { ?>
											<div id="s5_pos_above_body_4" class="s5_float_left" style="width:<?php echo $s5_pos_above_body_4_width ?>%">
												<?php s5_module_call('above_body_4','fourdivs'); ?>
											</div>
										<?php } ?>
										
										<?php if ($s5_pos_above_body_5 == "published") { ?>
											<div id="s5_pos_above_body_5" class="s5_float_left" style="width:<?php echo $s5_pos_above_body_5_width ?>%">
												<?php s5_module_call('above_body_5','fourdivs'); ?>
											</div>
										<?php } ?>
										
										<?php if ($s5_pos_above_body_6 == "published") { ?>
											<div id="s5_pos_above_body_6" class="s5_float_left" style="width:<?php echo $s5_pos_above_body_6_width ?>%">
												<?php s5_module_call('above_body_6','fourdivs'); ?>
											</div>
										<?php } ?>						
										<div style="clear:both; height:0px"></div>

									</div>
									</div>
								
								</div>

							<?php } ?>
									
							<?php if ($s5_show_component == "yes") { ?>
							<main>
								<?php s5_component_call(); ?>
								<div style="clear:both;height:0px"></div>
							</main>
							<?php } ?>
							
							<?php if ($s5_pos_below_body_1 == "published" || $s5_pos_below_body_2 == "published" || $s5_pos_below_body_3 == "published" || $s5_pos_below_body_4 == "published" || $s5_pos_below_body_5 == "published" || $s5_pos_below_body_6 == "published") { ?>
						
								<div id="s5_below_body_wrap">			
								
									<div id="s5_below_body">
									<div id="s5_below_body_inner">
									
										<?php if ($s5_pos_below_body_1 == "published") { ?>
											<div id="s5_pos_below_body_1" class="s5_float_left" style="width:<?php echo $s5_pos_below_body_1_width ?>%">
												<?php s5_module_call('below_body_1','fourdivs'); ?>
											</div>
										<?php } ?>
										
										<?php if ($s5_pos_below_body_2 == "published") { ?>
											<div id="s5_pos_below_body_2" class="s5_float_left" style="width:<?php echo $s5_pos_below_body_2_width ?>%">
												<?php s5_module_call('below_body_2','fourdivs'); ?>
											</div>
										<?php } ?>
										
										<?php if ($s5_pos_below_body_3 == "published") { ?>
											<div id="s5_pos_below_body_3" class="s5_float_left" style="width:<?php echo $s5_pos_below_body_3_width ?>%">
												<?php s5_module_call('below_body_3','fourdivs'); ?>
											</div>
										<?php } ?>
										
										<?php if ($s5_pos_below_body_4 == "published") { ?>
											<div id="s5_pos_below_body_4" class="s5_float_left" style="width:<?php echo $s5_pos_below_body_4_width ?>%">
												<?php s5_module_call('below_body_4','fourdivs'); ?>
											</div>
										<?php } ?>
										
										<?php if ($s5_pos_below_body_5 == "published") { ?>
											<div id="s5_pos_below_body_5" class="s5_float_left" style="width:<?php echo $s5_pos_below_body_5_width ?>%">
												<?php s5_module_call('below_body_5','fourdivs'); ?>
											</div>
										<?php } ?>
										
										<?php if ($s5_pos_below_body_6 == "published") { ?>
											<div id="s5_pos_below_body_6" class="s5_float_left" style="width:<?php echo $s5_pos_below_body_6_width ?>%">
												<?php s5_module_call('below_body_6','fourdivs'); ?>
											</div>
										<?php } ?>						
										<div style="clear:both; height:0px"></div>

									</div>
									</div>
								</div>

							<?php } ?>
							
						</div>
						</div>
						
					<?php } ?>
					
					<?php if ($s5_pos_middle_bottom_1 == "published" || $s5_pos_middle_bottom_2 == "published" || $s5_pos_middle_bottom_3 == "published" || $s5_pos_middle_bottom_4 == "published" || $s5_pos_middle_bottom_5 == "published" || $s5_pos_middle_bottom_6 == "published") { ?>
					
						<div id="s5_middle_bottom_wrap">
							
							<div id="s5_middle_bottom">
							<div id="s5_middle_bottom_inner">
							
								<?php if ($s5_pos_middle_bottom_1 == "published") { ?>
									<div id="s5_pos_middle_bottom_1" class="s5_float_left" style="width:<?php echo $s5_pos_middle_bottom_1_width ?>%">
										<?php s5_module_call('middle_bottom_1','round_box'); ?>
									</div>
								<?php } ?>
								
								<?php if ($s5_pos_middle_bottom_2 == "published") { ?>
									<div id="s5_pos_middle_bottom_2" class="s5_float_left" style="width:<?php echo $s5_pos_middle_bottom_2_width ?>%">
										<?php s5_module_call('middle_bottom_2','round_box'); ?>
									</div>
								<?php } ?>
								
								<?php if ($s5_pos_middle_bottom_3 == "published") { ?>
									<div id="s5_pos_middle_bottom_3" class="s5_float_left" style="width:<?php echo $s5_pos_middle_bottom_3_width ?>%">
										<?php s5_module_call('middle_bottom_3','round_box'); ?>
									</div>
								<?php } ?>
								
								<?php if ($s5_pos_middle_bottom_4 == "published") { ?>
									<div id="s5_pos_middle_bottom_4" class="s5_float_left" style="width:<?php echo $s5_pos_middle_bottom_4_width ?>%">
										<?php s5_module_call('middle_bottom_4','round_box'); ?>
									</div>
								<?php } ?>
								
								<?php if ($s5_pos_middle_bottom_5 == "published") { ?>
									<div id="s5_pos_middle_bottom_5" class="s5_float_left" style="width:<?php echo $s5_pos_middle_bottom_5_width ?>%">
										<?php s5_module_call('middle_bottom_5','round_box'); ?>
									</div>
								<?php } ?>
								
								<?php if ($s5_pos_middle_bottom_6 == "published") { ?>
									<div id="s5_pos_middle_bottom_6" class="s5_float_left" style="width:<?php echo $s5_pos_middle_bottom_6_width ?>%">
										<?php s5_module_call('middle_bottom_6','round_box'); ?>
									</div>
								<?php } ?>						
								<div style="clear:both; height:0px"></div>

							</div>
							</div>
						
						</div>

					<?php } ?>
					
				</div>
				</div>
				<!-- Left column -->	
				<?php if($s5_pos_left == "published" || $s5_pos_left_inset == "published" || $s5_pos_left_top == "published" || $s5_pos_left_bottom == "published") { ?>
					<aside id="s5_left_column_wrap" class="s5_float_left" style="width:<?php echo $s5_left_column_width ?>px">
					<div id="s5_left_column_wrap_inner">
						<?php if($s5_pos_left_top == "published") { ?>
							<div id="s5_left_top_wrap" class="s5_float_left" style="width:<?php echo $s5_left_column_width ?>px">
								<?php s5_module_call('left_top','round_box'); ?>
							</div>
						<?php } ?>
						<?php if($s5_pos_left == "published") { ?>
							<div id="s5_left_wrap" class="s5_float_left" style="width:<?php echo $s5_left_width ?>px">
								<?php s5_module_call('left','round_box'); ?>
							</div>
						<?php } ?>
						<?php if($s5_pos_left_inset == "published") { ?>
							<div id="s5_left_inset_wrap" class="s5_float_left" style="width:<?php echo $s5_left_inset_width ?>px">
								<?php s5_module_call('left_inset','round_box'); ?>
							</div>
						<?php } ?>
						<?php if($s5_pos_left_bottom == "published") { ?>
							<div id="s5_left_bottom_wrap" class="s5_float_left" style="width:<?php echo $s5_left_column_width ?>px">
								<?php s5_module_call('left_bottom','round_box'); ?>
							</div>
						<?php } ?>
					</div>
					</aside>
				<?php } ?>
				<!-- End Left column -->	
				<!-- Right column -->	
				<?php if($s5_pos_right == "published" || $s5_pos_right_inset == "published" || $s5_pos_right_top == "published" || $s5_pos_right_bottom == "published") { ?>
					<aside id="s5_right_column_wrap" class="s5_float_left" style="width:<?php echo $s5_right_column_width ?>px; margin-left:-<?php echo $s5_right_column_width + $s5_left_column_width ?>px">
					<div id="s5_right_column_wrap_inner">
						<?php if($s5_pos_right_top == "published") { ?>
							<div id="s5_right_top_wrap" class="s5_float_left" style="width:<?php echo $s5_right_column_width ?>px">
								<?php s5_module_call('right_top','round_box'); ?>
							</div>
						<?php } ?>
						<?php if($s5_pos_right_inset == "published") { ?>
							<div id="s5_right_inset_wrap" class="s5_float_left" style="width:<?php echo $s5_right_inset_width ?>px">
								<?php s5_module_call('right_inset','round_box'); ?>
							</div>
						<?php } ?>
						<?php if($s5_pos_right == "published") { ?>
							<div id="s5_right_wrap" class="s5_float_left" style="width:<?php echo $s5_right_width ?>px">
								<?php s5_module_call('right','round_box'); ?>
							</div>
						<?php } ?>
						<?php if($s5_pos_right_bottom == "published") { ?>
							<div id="s5_right_bottom_wrap" class="s5_float_left" style="width:<?php echo $s5_right_column_width ?>px">
								<?php s5_module_call('right_bottom','round_box'); ?>
							</div>
						<?php } ?>
					</div>
					</aside>
				<?php } ?>
				<!-- End Right column -->	
			</div>
			</div>
			<!-- End columns wrap -->	
			
		<!-- Below Columns Wrap -->	
			<?php if ($s5_pos_below_columns_1 == "published" || $s5_pos_below_columns_2 == "published" || $s5_pos_below_columns_3 == "published" || $s5_pos_below_columns_4 == "published" || $s5_pos_below_columns_5 == "published" || $s5_pos_below_columns_6 == "published") { ?>
			<div id="s5_below_columns_wrap1">	
			<div id="s5_below_columns_wrap2">
			<div id="s5_below_columns_inner" class="s5_wrap">

					
						<?php if ($s5_pos_below_columns_1 == "published") { ?>
							<div id="s5_below_columns_1" class="s5_float_left" style="width:<?php echo $s5_pos_below_columns_1_width ?>%">
								<?php s5_module_call('below_columns_1','round_box'); ?>
							</div>
						<?php } ?>
						
						<?php if ($s5_pos_below_columns_2 == "published") { ?>
							<div id="s5_below_columns_2" class="s5_float_left" style="width:<?php echo $s5_pos_below_columns_2_width ?>%">
								<?php s5_module_call('below_columns_2','round_box'); ?>
							</div>
						<?php } ?>
						
						<?php if ($s5_pos_below_columns_3 == "published") { ?>
							<div id="s5_below_columns_3" class="s5_float_left" style="width:<?php echo $s5_pos_below_columns_3_width ?>%">
								<?php s5_module_call('below_columns_3','round_box'); ?>
							</div>
						<?php } ?>
						
						<?php if ($s5_pos_below_columns_4 == "published") { ?>
							<div id="s5_below_columns_4" class="s5_float_left" style="width:<?php echo $s5_pos_below_columns_4_width ?>%">
								<?php s5_module_call('below_columns_4','round_box'); ?>
							</div>
						<?php } ?>
						
						<?php if ($s5_pos_below_columns_5 == "published") { ?>
							<div id="s5_below_columns_5" class="s5_float_left" style="width:<?php echo $s5_pos_below_columns_5_width ?>%">
								<?php s5_module_call('below_columns_5','round_box'); ?>
							</div>
						<?php } ?>
						
						<?php if ($s5_pos_below_columns_6 == "published") { ?>
							<div id="s5_below_columns_6" class="s5_float_left" style="width:<?php echo $s5_pos_below_columns_6_width ?>%">
								<?php s5_module_call('below_columns_6','round_box'); ?>
							</div>
						<?php } ?>						
						<div style="clear:both; height:0px"></div>

			</div>
			</div>
			</div>
			<?php } ?>
		<!-- End Below Columns Wrap -->				
			
			
		</div>
		</div>
		</section>
		<?php } ?>
	<!-- End Center area -->	
	
	
	<!-- Bottom Row1 -->


		<?php if ($s5_pos_bottom_row1_1 == "published" || $s5_pos_bottom_row1_2 == "published" || $s5_pos_bottom_row1_3 == "published" || $s5_pos_bottom_row1_4 == "published" || $s5_pos_bottom_row1_5 == "published" || $s5_pos_bottom_row1_6 == "published") { ?>
			<section id="s5_bottom_row1_area1">
			<div id="s5_bottom_row1_area2">
			<div id="s5_bottom_row1_area_inner" class="s5_wrap">

				<div id="s5_bottom_row1_wrap">
					<div id="s5_bottom_row1">
					<div id="s5_bottom_row1_inner">
					
						<?php if ($s5_pos_bottom_row1_1 == "published") { ?>
							<div id="s5_pos_bottom_row1_1" class="s5_float_left" style="width:<?php echo $s5_pos_bottom_row1_1_width ?>%">
								<?php s5_module_call('bottom_row1_1','round_box'); ?>
							</div>
						<?php } ?>
						
						<?php if ($s5_pos_bottom_row1_2 == "published") { ?>
							<div id="s5_pos_bottom_row1_2" class="s5_float_left" style="width:<?php echo $s5_pos_bottom_row1_2_width ?>%">
								<?php s5_module_call('bottom_row1_2','round_box'); ?>
							</div>
						<?php } ?>
						
						<?php if ($s5_pos_bottom_row1_3 == "published") { ?>
							<div id="s5_pos_bottom_row1_3" class="s5_float_left" style="width:<?php echo $s5_pos_bottom_row1_3_width ?>%">
								<?php s5_module_call('bottom_row1_3','round_box'); ?>
							</div>
						<?php } ?>
						
						<?php if ($s5_pos_bottom_row1_4 == "published") { ?>
							<div id="s5_pos_bottom_row1_4" class="s5_float_left" style="width:<?php echo $s5_pos_bottom_row1_4_width ?>%">
								<?php s5_module_call('bottom_row1_4','round_box'); ?>
							</div>
						<?php } ?>
						
						<?php if ($s5_pos_bottom_row1_5 == "published") { ?>
							<div id="s5_pos_bottom_row1_5" class="s5_float_left" style="width:<?php echo $s5_pos_bottom_row1_5_width ?>%">
								<?php s5_module_call('bottom_row1_5','round_box'); ?>
							</div>
						<?php } ?>
						
						<?php if ($s5_pos_bottom_row1_6 == "published") { ?>
							<div id="s5_pos_bottom_row1_6" class="s5_float_left" style="width:<?php echo $s5_pos_bottom_row1_6_width ?>%">
								<?php s5_module_call('bottom_row1_6','round_box'); ?>
							</div>
						<?php } ?>
						
						<div style="clear:both; height:0px"></div>

					</div>
					</div>
				</div>

		</div>
		</div>
		</section>
		<?php } ?>
	<!-- End Bottom Row1 -->	
		
		
	<!-- Bottom Row2 -->	
		<?php if ($s5_pos_bottom_row2_1 == "published" || $s5_pos_bottom_row2_2 == "published" || $s5_pos_bottom_row2_3 == "published" || $s5_pos_bottom_row2_4 == "published" || $s5_pos_bottom_row2_5 == "published" || $s5_pos_bottom_row2_6 == "published") { ?>
		<section id="s5_bottom_row2_area1">
		<div id="s5_bottom_row2_area2">
		<div id="s5_bottom_row2_area_inner" class="s5_wrap">			
		
			<div id="s5_bottom_row2_wrap">
				<div id="s5_bottom_row2">
				<div id="s5_bottom_row2_inner">					
					<?php if ($s5_pos_bottom_row2_1 == "published") { ?>
						<div id="s5_pos_bottom_row2_1" class="s5_float_left" style="width:<?php echo $s5_pos_bottom_row2_1_width ?>%">
							<?php s5_module_call('bottom_row2_1','round_box'); ?>
						</div>
					<?php } ?>
					
					<?php if ($s5_pos_bottom_row2_2 == "published") { ?>
						<div id="s5_pos_bottom_row2_2" class="s5_float_left" style="width:<?php echo $s5_pos_bottom_row2_2_width ?>%">
							<?php s5_module_call('bottom_row2_2','round_box'); ?>
						</div>
					<?php } ?>
					
					<?php if ($s5_pos_bottom_row2_3 == "published") { ?>
						<div id="s5_pos_bottom_row2_3" class="s5_float_left" style="width:<?php echo $s5_pos_bottom_row2_3_width ?>%">
							<?php s5_module_call('bottom_row2_3','round_box'); ?>
						</div>
					<?php } ?>
					
					<?php if ($s5_pos_bottom_row2_4 == "published") { ?>
						<div id="s5_pos_bottom_row2_4" class="s5_float_left" style="width:<?php echo $s5_pos_bottom_row2_4_width ?>%">
							<?php s5_module_call('bottom_row2_4','round_box'); ?>
						</div>
					<?php } ?>
					
					<?php if ($s5_pos_bottom_row2_5 == "published") { ?>
						<div id="s5_pos_bottom_row2_5" class="s5_float_left" style="width:<?php echo $s5_pos_bottom_row2_5_width ?>%">
							<?php s5_module_call('bottom_row2_5','round_box'); ?>
						</div>
					<?php } ?>
					
					<?php if ($s5_pos_bottom_row2_6 == "published") { ?>
						<div id="s5_pos_bottom_row2_6" class="s5_float_left" style="width:<?php echo $s5_pos_bottom_row2_6_width ?>%">
							<?php s5_module_call('bottom_row2_6','round_box'); ?>
						</div>
					<?php } ?>						
					<div style="clear:both; height:0px"></div>
				</div>
				</div>	
			</div>	
				
		</div>
		</div>
		</section>
		<?php } ?>
	<!-- End Bottom Row2 -->
	
	
	
	<!-- Bottom Row3 -->	
		<?php if ($s5_pos_bottom_row3_1 == "published" || $s5_pos_bottom_row3_2 == "published" || $s5_pos_bottom_row3_3 == "published" || $s5_pos_bottom_row3_4 == "published" || $s5_pos_bottom_row3_5 == "published" || $s5_pos_bottom_row3_6 == "published") { ?>
		<section id="s5_bottom_row3_area1">	
		<div id="s5_bottom_row3_area2">
		<div id="s5_bottom_row3_area_inner" class="s5_wrap">
		
			<div id="s5_bottom_row3_wrap">
				<div id="s5_bottom_row3">
				<div id="s5_bottom_row3_inner">
				
					<?php if ($s5_pos_bottom_row3_1 == "published") { ?>
						<div id="s5_pos_bottom_row3_1" class="s5_float_left" style="width:<?php echo $s5_pos_bottom_row3_1_width ?>%">
							<?php s5_module_call('bottom_row3_1','round_box'); ?>
						</div>
					<?php } ?>
					
					<?php if ($s5_pos_bottom_row3_2 == "published") { ?>
						<div id="s5_pos_bottom_row3_2" class="s5_float_left" style="width:<?php echo $s5_pos_bottom_row3_2_width ?>%">
							<?php s5_module_call('bottom_row3_2','round_box'); ?>
						</div>
					<?php } ?>
					
					<?php if ($s5_pos_bottom_row3_3 == "published") { ?>
						<div id="s5_pos_bottom_row3_3" class="s5_float_left" style="width:<?php echo $s5_pos_bottom_row3_3_width ?>%">
							<?php s5_module_call('bottom_row3_3','round_box'); ?>
						</div>
					<?php } ?>
					
					<?php if ($s5_pos_bottom_row3_4 == "published") { ?>
						<div id="s5_pos_bottom_row3_4" class="s5_float_left" style="width:<?php echo $s5_pos_bottom_row3_4_width ?>%">
							<?php s5_module_call('bottom_row3_4','round_box'); ?>
						</div>
					<?php } ?>
					
					<?php if ($s5_pos_bottom_row3_5 == "published") { ?>
						<div id="s5_pos_bottom_row3_5" class="s5_float_left" style="width:<?php echo $s5_pos_bottom_row3_5_width ?>%">
							<?php s5_module_call('bottom_row3_5','round_box'); ?>
						</div>
					<?php } ?>
					
					<?php if ($s5_pos_bottom_row3_6 == "published") { ?>
						<div id="s5_pos_bottom_row3_6" class="s5_float_left" style="width:<?php echo $s5_pos_bottom_row3_6_width ?>%">
							<?php s5_module_call('bottom_row3_6','round_box'); ?>
						</div>
					<?php } ?>						
					<div style="clear:both; height:0px"></div>

				</div>
				</div>
			</div>

		</div>
		</div>
		</section>
		<?php } ?>
	<!-- End Bottom Row3 -->
	
	
	<!-- Footer Area -->
		<footer id="s5_footer_area1">
		<div id="s5_footer_area2">
		<div id="s5_footer_area_inner" class="s5_wrap">
		<div id="s5_footer_area_inner2">
		
			<?php if($s5_pos_footer == "published") { ?>
				<div id="s5_footer_module">
					<?php s5_module_call('footer','notitle'); ?>
				</div>	
			<?php } else { ?>
				<div id="s5_footer">
					<?php require("vertex/footer.php"); ?>
				</div>
			<?php } ?>
			
			<?php if($s5_pos_bottom_menu) { ?>
				<div id="s5_bottom_menu_wrap">
					<?php s5_module_call('bottom_menu','notitle'); ?>
				</div>	
			<?php } ?>
			<div style="clear:both; height:0px"></div>
			
		</div>
		</div>
		</div>
		</footer>
	<!-- End Footer Area -->
	
	<!-- Bottom Vertex Calls -->
	<?php require("vertex/includes/vertex_includes_bottom.php"); ?>
	
  <a title="FEDUNI_TEPPS" href="https://federation.edu.au/schools/school-of-engineering-information-technology-and-physical-sciences/research/computational-science-and-mathematics/ciao/research-groups/technologies-for-empowering-people-for-participation-in-society-tepps" target="blank" id="s5_shape5_logo"></a>
	
	<?php s5_module_call('debug','fourdivs'); ?>

<!-- End Body Padding -->
</div>

</body>
</html>
