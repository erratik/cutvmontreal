<?php
$output = $el_class = $width = '';
extract(shortcode_atts(array(
    'el_class' => '',
    'width' => '1/1'
), $atts));

$el_class = $this->getExtraClass($el_class);
// $width = str_replace('/', '_', wpb_translateColumnWidthToSpan($width));
$width = wpb_translateColumnWidthToSpan($width);

if ( isset( $this->atts['width'] ) && ( $this->atts['width'] == '6/8' || $this->atts['width'] == '2/8' ) ) {
    $width .= ' vc_'.str_replace('/', '_', $this->atts['width']);
}

$el_class .= ' wpb_column column_container';

$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $width.$el_class, $this->settings['base']);
$output .= "\n\t".'<div class="'.$css_class.'">';
$output .= "\n\t\t".'<div class="wpb_wrapper">';
$output .= "\n\t\t\t".wpb_js_remove_wpautop($content);
$output .= "\n\t\t".'</div> '.$this->endBlockComment('.wpb_wrapper');
$output .= "\n\t".'</div> '.$this->endBlockComment($el_class) . "\n";

echo $output;