<div>
	<h2>Reco Widget</h2>
	L&auml;s och skriv rekommendationer om alla Sveriges f&ouml;retag. Med din och andras &aring;sikter hittar du de popul&auml;raste f&ouml;retagen inom ditt omr&aring;de.
	<p>För att använda denna widget så använder du shortcoden <strong>[reco-widget]</strong> i innehållet på sidan där du vill placera den</p>
	<form action="options.php" method="post">
		<?php settings_fields('reco_widget_options'); ?>
		<?php do_settings_sections('reco_widget_sections'); ?>
		<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
	</form>
</div>