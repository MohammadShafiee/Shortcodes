<?php
class ClassShortCode{

    private $__instance = null;

    public function parseString($content = '', &$instance = null){
        $pattern = $this->getShortcodeRegex();
        $this->__instance = $instance;
        return preg_replace_callback( "/$pattern/s", array($this, '__doShortcodeTag'), $content );
    }
    //------------------------------------------------------------
    /**
     * Retrieve the shortcode regular expression for searching.
     *
     * The regular expression combines the shortcode tags in the regular expression
     * in a regex class.
     *
     * The regular expression contains 6 different sub matches to help with parsing.
     *
     * 1 - An extra [ to allow for escaping shortcodes with double [[]]
     * 2 - The shortcode name
     * 3 - The shortcode argument list
     * 4 - The self closing /
     * 5 - The content of a shortcode when it wraps some content.
     * 6 - An extra ] to allow for escaping shortcodes with double [[]]
     *
     * @return string The shortcode search regular expression
     */
    public function getShortcodeRegex() {

//        $tagnames = array_keys($this->__shortcodeTags);
//        $tagregexp = join( '|', array_map('preg_quote', $tagnames) );

        // WARNING! Do not change this regex without changing __doShortcodeTag() and stripShortcodeTag()
        return
            '\\['                              // Opening bracket
            . '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
            . "([\\w]+\\.[\\w]+)"                // 2: Shortcode name Example: pluginname.action
            . '(?![\\w-])'                       // Not followed by word character or hyphen
            . '('                                // 3: Unroll the loop: Inside the opening shortcode tag
            .     '[^\\]\\/]*'                   // Not a closing bracket or forward slash
            .     '(?:'
            .         '\\/(?!\\])'               // A forward slash not followed by a closing bracket
            .         '[^\\]\\/]*'               // Not a closing bracket or forward slash
            .     ')*?'
            . ')'
            . '(?:'
            .     '(\\/)'                        // 4: Self closing tag ...
            .     '\\]'                          // ... and closing bracket
            . '|'
            .     '\\]'                          // Closing bracket
            .     '(?:'
            .         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
            .             '[^\\[]*+'             // Not an opening bracket
            .             '(?:'
            .                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
            .                 '[^\\[]*+'         // Not an opening bracket
            .             ')*+'
            .         ')'
            .         '\\[\\/\\2\\]'             // Closing shortcode tag
            .     ')?'
            . ')'
            . '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]]
    }
    //------------------------------------------------------------
    /**
     * Regular Expression callable for doShortcode() for calling shortcode hook.
     * @see getShortcodeRegex for details of the match array contents.
     *
     * @access private
     * @uses $shortcodeTags
     *
     * @param array $m Regular expression match array
     * @return mixed False on failure.
     */
    private function __doShortcodeTag( $m) {

        // allow [[foo]] syntax for escaping a tag
        if ( $m[1] == '[' && $m[6] == ']' ) {
            return substr($m[0], 1, -1);
        }

        $tag = $m[2];
        if(strpos($tag, '.') === false)
            return false;

        //-----------------------------
        // load plugin shortcodes class
        list($plugin, $action) = explode('.', $tag);
        $shortcodeClass = $plugin.'Shortcodes';
        $shortcodeFile = CakePlugin::path($plugin).'Shortcodes'.DS.$shortcodeClass.'.php';
        if(!file_exists($shortcodeFile)){
            return $m[0];
        }
        include_once $shortcodeFile;
        $shortcodeInstance = new $shortcodeClass();
        if(!method_exists($shortcodeInstance, $action)){
            return $m[0];
        }
        //-----------------------------

        $attr = $this->shortcodeParseAtts( $m[3] );

        if ( isset( $m[5] ) ) {
            // enclosing tag - extra parameter
            return $m[1] . call_user_func( array($shortcodeInstance, $action), $attr, $m[5], $tag, $this->__instance ) . $m[6];
        } else {
            // self-closing tag
            return $m[1] . call_user_func( array($shortcodeInstance, $action), $attr, null,  $tag, $this->__instance ) . $m[6];
        }
    }
    //------------------------------------------------------------
    /**
     * Retrieve all attributes from the shortcodes tag.
     *
     * The attributes list has the attribute name as the key and the value of the
     * attribute as the value in the key/value pair. This allows for easier
     * retrieval of the attributes, since all attributes have to be known.
     *
     * @param string $text
     * @return array List of attributes and their value.
     */
    public function shortcodeParseAtts($text) {
        $atts = array();
        $pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
        $text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);
        if ( preg_match_all($pattern, $text, $match, PREG_SET_ORDER) ) {
            foreach ($match as $m) {
                if (!empty($m[1]))
                    $atts[strtolower($m[1])] = stripcslashes($m[2]);
                elseif (!empty($m[3]))
                    $atts[strtolower($m[3])] = stripcslashes($m[4]);
                elseif (!empty($m[5]))
                    $atts[strtolower($m[5])] = stripcslashes($m[6]);
                elseif (isset($m[7]) and strlen($m[7]))
                    $atts[] = stripcslashes($m[7]);
                elseif (isset($m[8]))
                    $atts[] = stripcslashes($m[8]);
            }
        } else {
            $atts = ltrim($text);
        }
        return $atts;
    }
    //------------------------------------------------------------
    /**
     * Remove all shortcode tags from the given content.
     *
     * @param string $content Content to remove shortcode tags.
     * @return string Content without shortcode tags.
     */
    public function stripShortcodes( $content ) {
        $pattern = $this->getShortcodeRegex();
        return preg_replace_callback( "/$pattern/s", array($this, '__stripShortcodeTag'), $content );
    }
    //------------------------------------------------------------
    private function __stripShortcodeTag( $m ) {
        // allow [[foo]] syntax for escaping a tag
        if ( $m[1] == '[' && $m[6] == ']' ) {
            return substr($m[0], 1, -1);
        }

        return $m[1] . $m[6];
    }
}