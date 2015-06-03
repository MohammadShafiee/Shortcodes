<?php
class ShortcodesShortcodes {
    
    public function testfunc($options = array(), $body = null, $shortcodeTag = null) {
        return '<h3 class="'.$options['class'].'">'.$body.', This Text Appended by Shortcode, Shortcode tag is: '.$shortcodeTag.'</h3>';
    }
    public function anothershortcode() {
        return 'Another Shortcode';
    }
}
