<?php
App::uses('CakeEventListener', 'Event');
App::uses('ClassShortCode', 'Shortcodes.Lib');

class ShortcodesEventHandler extends Object implements CakeEventListener{
    
    public function implementedEvents() {
        return array(
            'Helper.Layout.beforeFilter' => array(
                'callable' => 'onBeforeBlocksFilter',
            )
        );
    }
    public function onBeforeBlocksFilter($event){
        $View = $event->subject;
        $data = $event->data;
        $shortCodeObj = new ClassShortCode();
        $data['content'] = $shortCodeObj->parseString($data['content'], $View);
    }
}
