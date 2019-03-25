<?php

require_once './templates.php';

$page = new Blacklist();

switch (filter_var(getenv('REQUEST_METHOD'))) {
    case "GET":
        $page->getData();
        break;
    case "POST":
        $page->setData();
        break;
    case "DELETE":
        $page->deleteData(file_get_contents('php://input'));
}

class Blacklist {

    private $db;
    private $name = 'BLACKLIST';
    private $fields = array('CPF');
    /* Codes collected from http://en.wikipedia.org/wiki/List_of_HTTP_status_codes */
    private $status = array(
        200 => 'HTTP/1.0 200 OK',
        400 => 'HTTP/1.0 400 Bad Request'
    );

    private function model($operation, $data = array()) {
        switch ($operation) {
            case "INSERT":
                $query = sprintf("INSERT INTO {$this->name} (CPF)"
                        . "            VALUES (%s)", $data);
                break;
            case "DELETE":
                $query = sprintf("DELETE FROM {$this->name}"
                        . "             WHERE CPF = '%s'", $data);
                break;
            case "SELECT":
                !apc_fetch($this->name) ? apc_store($this->name, 1) : apc_inc($this->name);
                $query = sprintf("SELECT * FROM {$this->name}"
                        . "               WHERE CPF = '%s'", $data);
                break;
            case "COUNT":
                $query = "SELECT COUNT(DISTINCT CPF) FROM {$this->name}";
                break;
        }
        return $this->db->query($query);
    }

    private function validateField($CPF) {
        if (!$CPF) {
            Template::renderJSON(array('message' => 'CPF is a required field'), $this->status[400]);
        } elseif (!$this->validateCPF($CPF)) {
            Template::renderJSON(array('message' => 'Invalid CPF value'), $this->status[400]);
        }
        return true;
    }

    private function validateCPF($data) {
        /* https://gist.github.com/rafael-neri/ab3e58803a08cb4def059fce4e3c0e40 */
        $cpf = preg_replace('/[^0-9]/is', '', $data);
        if (strlen($cpf) != 11) {
            return false;
        }
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf{$c} * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf{$c} != $d) {
                return false;
            }
        }
        return true;
    }

    private function getCPF($URL) {
        if (isset($URL['query'])) {
            $query = array();
            parse_str($URL['query'], $query);
            if (isset($query['cpf']) && $this->validateField($query['cpf'])) {
                $switch = $this->model('SELECT', $query['cpf'])->fetchArray();
                $valid = $switch ? '' : 'not';
                Template::renderJSON(array('message' => "CPF {$valid} located"),
                        $this->status[$switch ? 200 : 400]);
            }
        }
        return "CPF field is required";
    }

    public function __construct() {
        /* Fill the required fields */
        foreach ($this->fields as $field) {
            $fieldsQuery = !isset($fieldsQuery) ? array() : $fieldsQuery;
            array_push($fieldsQuery, "{$field} INTEGER");
        }
        $fieldsQuery = implode(', ', $fieldsQuery);
        /* Init DB */
        $this->db = new SQLite3("sqlite.db");
        $this->db->exec("CREATE TABLE IF NOT EXISTS {$this->name} ({$fieldsQuery})");
    }

    public function getData() {
        $data = array();
        $URL = parse_url(filter_var(getenv('REQUEST_URI')));
        if ($URL['path'] <> '/') {
            switch ($URL['path']) {
                case '/check':
                    $data['feedback'] = $this->getCPF($URL);
                    break;
                case '/status':
                    Template::renderJSON([
                        'SELECT_LOG' => apc_fetch($this->name),
                        'SERVER_UPTIME' => shell_exec('uptime -p'),
                        'BLACKLIST_TOTAL' => $this->model('COUNT')->fetchArray()[0]
                    ]);
                    break;
                default:
                    $data['feedback'] = "Unknown endpoint";
                    break;
            }
        }
        Template::renderHTML('index', $data);
    }

    public function setData() {
        $CPF = filter_input(INPUT_POST, 'cpf', FILTER_DEFAULT);
        Template::renderJSON(['message' => $this->handleData('INSERT', $CPF) ? "CPF inserted successfully" :
                    "Sorry, the CPF was not inserted"]);
    }

    public function deleteData($data) {
        $query = array();
        parse_str($data, $query);
        $switch = $this->model('SELECT', $query['cpf'])->fetchArray();
        if (!$switch) {
            Template::renderJSON(
                    ['message' => "CPF not located"],
                    $this->status[400]
            );
        }
        $execute = $this->handleData('DELETE', isset($query['cpf']) ? $query['cpf'] : '');
        Template::renderJSON(
                ['message' => $execute ?
                            'CPF deleted successfully' :
                            'Sorry, the CPF was not deleted']
        );
    }

    private function handleData($operation, $CPF) {
        if ($this->validateField($CPF)) {
            return $this->model($operation, $CPF);
        }
        return false;
    }

}
