<?php
class ShortcodesActivation{

    public function beforeActivation(&$controller) {
        return true;
    }
    public function onActivation(Controller $controller) {
        $Block = ClassRegistry::init('Blocks.Block');
        $Block->create();
        $data = array(
            'Block' => array(
                'region_id' => '4',
                'title' => 'Shortcode Example',
                'alias' => 'shortcode_example',
                'body' => '[Shortcodes.testfunc class="cls1"]Hello World![/Shortcodes.testfunc]'.'<br /> '.'[Shortcodes.anothershortcode]',
                'show_title' => 1,
                'status' => 1,
            )
        );
        $Block->save($data);
        return true;
    }
    public function beforeDeactivation(&$controller) {
        return true;
    }
    public function onDeactivation(Controller $controller) {
        $Block = ClassRegistry::init('Blocks.Block');
        $block = $Block->findByAlias('shortcode_example', array('id'));
        $Block->id = $block['Block']['id'];
        $Block->delete();
        return true;
    }
}