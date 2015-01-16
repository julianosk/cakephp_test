<?php

// app/Controller/UsersController.php
App::uses('AppController', 'Controller');

class UsersController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        // Allow users to register and logout.
        $this->Auth->allow('add', 'logout');
    }

    public function login() {
        if ($this->request->is('post')) {
            $username = $this->data['User']['username'];
            $attempts = $this->User->attempts($username);
            $blocked = false;
            if ($attempts > 2) {
                $blocked = true;
                $now = time();
                if ($now - strtotime($this->User->last_attempt($username)) > 10) {
                    $this->User->reset_attempts($username);
                    $blocked = false;
                } else {
                    $this->User->fail($username);
                    $this->Session->setFlash(__($username . ' blocked, try again after 10 seconds'));                    
                }
            }
            if (!$blocked) {
                if ($this->Auth->login()) {
                    $this->User->reset_attempts($username);
                    return $this->redirect($this->Auth->redirectUrl());
                }
                $attempts = $this->User->fail($username);
                if ($attempts > 2) {
                    $this->Session->setFlash(__($username . ' blocked, try again after 10 seconds'));
                } else {
                    $this->Session->setFlash(__('Invalid username or password, try again'));
                }
            }
        }
    }

    public function logout() {
        return $this->redirect($this->Auth->logout());
    }

    public function index() {
        $this->User->recursive = 0;
        $this->set('users', $this->paginate());
    }

    public function view($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        $this->set('user', $this->User->read(null, $id));
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->User->create();
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('The user has been saved'));
                return $this->redirect(array(
                            'controller' => 'posts',
                            'action' => 'index',)
                );
//                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(
                    __('The user could not be saved. Please, try again.')
            );
        }
    }

    public function edit($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('The user has been saved'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(
                    __('The user could not be saved. Please, try again.')
            );
        } else {
            $this->request->data = $this->User->read(null, $id);
            unset($this->request->data['User']['password']);
        }
    }

    public function delete($id = null) {
        // Prior to 2.5 use
        // $this->request->onlyAllow('post');

        $this->request->allowMethod('post');

        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->User->delete()) {
            $this->Session->setFlash(__('User deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('User was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }

}
