<?php
    
namespace Mithos\Network;

use Mithos\Core\Config;

class Mail extends \PHPMailer {

    public function __construct() {
        $this->isSMTP();

        $this->Host = Config::get('smtp.host');
        $this->SMTPAuth = true;
        $this->Username = Config::get('smtp.username');
        $this->Password = Config::get('smtp.password');
        $this->Port = Config::get('smtp.port');

        if (Config::get('smtp.secure')) {
            $this->SMTPSecure = 'ssl';
        }

        if (is_array(Config::get('smtp.from'))) {
            foreach (Config::get('smtp.from') as $key => $value) {
                $this->From = $value;
                $this->FromName = $key;
            }
        } else {
            $this->From = Config::get('smtp.from');
        }

        $this->isHTML(true);

        parent::__construct();
    }
    
    public function addAddress($email, $name = '') {
        parent::addAddress($email, $name);
        return $this;
    }
    
    public function setMessage($message) {
        $this->Body = $message;
        return $this;
    }
    
    public function getMessage() {
        return $this->Body;
    }
    
    public function setSubject($subject) {
        $this->Subject = $subject;
        return $this;
    }
    
    public function getSubject() {
        return $this->Subject;
    }
    
    public function send($message = null) {
        if ($message !== null) {
            $this->setMessage($message);
        }
        return parent::send();
    }
    
    public function setMessageFromTemplate($file, $data) {
        $smarty = new \Smarty();
        $vars = array(
            'template_dir' => TEMPLATES_PATH . 'mail/',
            'compile_dir' => CACHE_PATH . 'smarty/',
            'cache_dir' => CACHE_PATH
        );

        foreach ($vars as $name => $value) {
            $smarty->{$name} = $value;
        }

        foreach ($data as $key => $value) {
            $smarty->assign($key, $value);
        }
        $smarty->assign('content', $smarty->fetch($file . '.html'));
        $this->Body = $smarty->fetch('layout.html');
        return $this;
    }

    public function getError() {
        return $this->ErrorInfo;
    }
    
}