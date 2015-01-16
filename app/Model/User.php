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
//        return $user['User']['blocked'];
        if ($user['User']['blocked']){
            $now = time();
            if ($now - strtotime($user['User']['last_attempt']) > 10){
                $this->updateAll(
                    array('User.blocked' => false, 'User.attempts' => 0),
                    array('User.username' => $username)
                );
                return false;
            }
            return true;
        }
        return false;
    }
    
    public function diff($username) {
        $user = $this->findByUsername($username);
        if ($user['User']['blocked']){
            $now = time();
            return $now - strtotime($user['User']['last_attempt']);
        }
        return 0;
    }
    
    public function fail($username) {
        $this->updateAll(
            array('User.attempts' => 'User.attempts + 1'),
            array('User.username' => $username)
        );
        if ($this->findByUsername($username)['User']['attempts'] > 1) {
            $date = date("Y-m-d H:i:s");
            $this->updateAll(
                array('User.blocked' => true, 'User.last_attempt' => 'NOW()'),
                array('User.username' => $username)
            );
        }        
    }
    
    
    public function success($username) {
        $this->updateAll(
            array('User.blocked' => false, 'User.attempts' => 0),
            array('User.username' => $username)
        );
    }

}
