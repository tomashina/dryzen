<?php
namespace OCM\Traits\Back\Controller;
trait Common {
    private function validate() {
        if (!$this->user->hasPermission('modify', $this->ext_path)) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
    public function install() {
        if ($this->tables) {
            $this->load->model($this->ext_path);
            $this->{$this->ext_key}->addDBTables();
        }
        $this->ocm->util->addEvents($this->events);
        if (method_exists($this, 'onInstall')) {
            $this->onInstall($save);
        }
    }
    public function uninstall() {
        $this->ocm->util->removeDBTables($this->tables);
        $this->ocm->util->deleteEvents();
        if (method_exists($this, 'onUninstall')) {
            $this->onUninstall($save);
        }
    }
    public function upgrade() {
        $update_status = $this->ocm->util->isDBBUpdateAvail($this->tables, $this->events);
        if ($update_status['db']) {
            $this->{$this->ext_key}->addDBTables();
            $this->ocm->util->safeDBColumnAdd($this->tables);
        }
        if ($update_status['event']) {
            $this->ocm->util->addEvents($this->events);
        }
    }
    /* m@nu@l k#y ver1f1c@ti0n */
    public function awpdz() {
        if (isset($this->request->get['_key']) && $this->request->get['_key']) {
            $this->ocm->wpd($this->request->get['_key']);
            $this->response->redirect($this->ocm->url->getExtensionURL());
        }
    }
}