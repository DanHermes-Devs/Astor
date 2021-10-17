<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="nsbigbox<?php echo $ns_style; ?>">
	<div class="titlensbigbox<?php echo $ns_style; ?>">
		<h4><?php echo strtoupper($ns_full_name); ?> <?php _e('PREMIUM VERSION', $ns_text_domain); ?></h4>
	</div>
	<div class="contentnsbigbox">
		<p>	<?php _e('ALL FREE VERSION FEATURES and', $ns_text_domain); ?>:<br/><br/> 
			<?php _e(	'– Display rating inside single product<br/>
						 – Display rating inside shop<br/>
						 – Display rating inside shop<br>
						 – Display rating inside category and tag pages<br>
						 – Choose rating image<br>
						 – Choose color for rating image<br>
						 – All vote are saved inside a tab in Plugin Option in your backend<br>
						 – Reorder results page<br>', $ns_text_domain); ?></p>



		<a href="<?php echo $link_sidebar; ?>" class="linkBigBoxNS">
			<div class="buttonNsbigbox<?php echo $ns_style; ?>">
				<?php _e('UPGRADE', $ns_text_domain); ?>!
			</div>
		</a>
	</div>
</div>