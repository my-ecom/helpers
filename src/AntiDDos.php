<?php
namespace oangia;

class AntiDDos {
    function __construct($limit_time = 10, $limit_visit = 10, $file_dir = '') {
        #if (is_xhr()) return;
        $this->user_ip = getUserIP();
        $this->file = $file_dir . '/' . $this->user_ip . '.json';
        if (! file_exists($this->file)) {
            $this->setClient(['last_access' => time(), 'count' => 1]);
            return;
        }
        $client = $this->getClient();
        $last_access = time() - $client['last_access'];
        $count = $client['count'] + 1;

        if ($last_access > $limit_time) {
            $this->setClient(['last_access' => time(), 'count' => 1]);
        } else {
            if ($count >= $limit_visit) {
                $this->setClient(['last_access' => time(), 'count' => $count]);
                $this->ddosDetectedAction();
            } else {
                $this->getClient(['last_access' => time(), 'count' => $count]);
            }
        }
    }

    private function setClient($data) {
        file_put_contents($this->file, json_encode($data));
    }

    private function getClient() {
        return json_decode(file_get_contents($this->file), true);
    }

    public function ddosDetectedAction() {
        die('You\'re going too fast');
    }
}
