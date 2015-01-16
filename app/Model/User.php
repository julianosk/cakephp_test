<?php

// app/Model/User.php

App::uses('AppModel', 'Model');
App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');

class User extends AppModel {

    public $validate = array(
        'username' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'A username is required'
            )
        ),
        'password' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'A password is required'
            )
        ),
        'role' => array(
            'valid' => array(
                'rule' => array('inList', array('admin', 'author')),
                'message' => 'Please enter a valid role',
                'allowEmpty' => false
            )
        )
    );

    public function beforeSave($options = array()) {
        if (isset($this->data[$this->alias]['password'])) {
            $passwordHasher = new BlowfishPasswordHasher();
            $this->data[$this->alias]['password'] = $passwordHasher->hash(
                    $this->data[$this->alias]['password']
            );
        }
        return true;
    }
    
    public function isBlocked($username) {
        $user = $this->findByUsername($username);
        if ($user['User']['attempts'] > 1){
            $now = time();
            if ($now - strtotime($user['User']['last_attempt']) > 10){
                $this->updateAll(
                    array('User.attempts' => 0),
                    array('User.username' => $username)
                );
                return false;
            }
            return true;
        }
        return false;
    }
    
    public function last_attempt($username) {
        return $this->findByUsername($username)['User']['last_attempt'];
    }
    
    public function attempts($username) {
        return $this->findByUsername($username)['User']['attempts'];
    }
    
    public function fail($username) {
        $this->updateAll(
            array('User.attempts' => 'User.attempts + 1', 'User.last_attempt' => 'NOW()'),
            array('User.username' => $username)
        );
        return $this->findByUsername($username)['User']['attempts'];
    }
    
    
    public function reset_attempts($username) {
        $this->updateAll(
            array('User.attempts' => 0),
            array('User.username' => $username)
        );
    }

}
