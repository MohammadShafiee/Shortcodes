<?php
App::uses('Component', 'Controller');
App::uses('ClassShortCode', 'Shortcodes.Lib');

class ShortcodesComponent extends Component{

    public $controller = null;

    public function startup(Controller $controller) {
        $this->controller = $controller;
    }
    //------------------------------------------------------------
    public function beforeRender(Controller $controller) {
        if(isset($this->controller->request->params['admin']) && $this->controller->request->params['admin'])
            return;
        if(isset($this->controller->viewVars['nodes'])){
            $nodes = &$this->controller->viewVars['nodes'];
            foreach ($nodes as &$node) {
                $shortCodeObj = new ClassShortCode();
                $node['Node']['excerpt'] = $shortCodeObj->parseString($node['Node']['excerpt']);
                $node['Node']['body'] = $shortCodeObj->parseString($node['Node']['body']);
            }
        }

    }
}