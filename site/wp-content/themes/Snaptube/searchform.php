<?php

	// Vars
	global $vh_search_form_button, $vh_is_in_sidebar;

	if ($vh_search_form_button == 'searchButton') {
		$search_string = '';
	} elseif (get_search_query() == '') {
		$search_string = __('Search', 'vh');
	} else {
		$search_string = get_search_query();
	}

	if (empty($vh_search_form_button)) {
		$vh_search_form_button = 'submitButton';
	}

	$class      = 'footer_search_input';
	$form_class = ' gray-form';
	if ( $vh_is_in_sidebar === true ) {
		$class      = 'sb-search-input';
		$search_string = '';
		$form_class = '';
	} elseif ( is_search() && $vh_is_in_sidebar === 'content' ) {
		$class      = 'span5';
		$form_class = '';
	}
?>
<div class="search sb-search" id="sb-search">
	<form action="<?php echo home_url(); ?>" method="get" class="<?php echo $form_class; ?>">
		<input type="text" name="s" class="<?php echo $class; ?>" onclick="clearInput(this, '<?php _e('Search', 'vh'); ?>');" value="<?php echo $search_string; ?>" placeholder="<?php echo __('Search', 'vh'); ?>"/>
		<input type="submit" name="search" class="btn btn-primary sb-search-submit" value="<?php _e('Search', 'vh'); ?>" />
		<span class="sb-icon-search icon-search blue-button"></span>
	</form>
</div><!--end of search-->