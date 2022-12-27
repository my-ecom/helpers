<?php
namespace oangia\web\entities;
use oangia\web\database\Database;

class Model {
    private $table;
    private $select = '*';
    private $orderBy = ['id', 'desc'];
    private $where = ['id', '!=', ''];
    private $limit = 0;
    private $offset = 0;

    private function __construct($table) {
        $this->table = $table;
    }

    public static function table($table) {
        return new Model($table);
    }

    public function select($select) {
        if (is_array($select)) {
            $this->select = implode(',', $select);
        } else {
            $this->select = $select;
        }
        return $this;
    }

    public function where($field, $equation, $value = null) {
        if ($value === null) {
            $this->where = [$field, '==', $equation];
        } else {
            $this->where = [$field, $equation, $value];
        }
        return $this;
    }

    public function orderBy($field, $sort = 'asc') {
        $this->orderBy = [$field, $sort];
        return $this;
    }

    public function limit($limit) {
        $this->limit = $limit;
        return $this;
    }

    public function offset($offset) {
        $this->offset = $offset;
        return $this;
    }

    public function queryBuilder() {
        $sql = 'SELECT ' . $this->select . ' FROM ' . $this->table;
        $sql .= ' WHERE ' . $this->where[0] . $this->where[1] . '"' . $this->where[2]. '"';
        $sql .= ' ORDER BY ' . $this->orderBy[0] . ' ' . $this->orderBy[1];
        if ($this->limit > 0) {
            $sql .= ' LIMIT ' . $this->limit;
        }
        return $sql;
    }

    public function get() {
        $sql = $this->queryBuilder();
        return $this->query($sql);
    }

    public function first() {
        $this->limit = 1;
        $sql = $this->queryBuilder();
        $result = $this->query($sql);
        if (! $result) {
            return null;
        }
        return $result[0];
    }

    public static function getProductBySlug($table, $slug) {
        $sql = 'select * from ' . $table . ' where slug="' . $slug .'" limit 1';
        return Model::query($sql);
    }

    public static function getHomeProducts($table, $limit) {
        $sql =  'select * from ' . $table . ' order by id DESC limit ' . $limit;
        return Model::query($sql);
    }

    /*public static function where() {

    }*/

    protected static $sql = '';
    protected $addDomain = true;

    /*public function __construct($data = []) {
        if (! empty($data)) {
            $this->setData($data);
        }
        if ($this->addDomain) {
            $this->table = str_replace('.', '_', $_SERVER['HTTP_HOST']) . '_' . $this->table;
        }
    }*/

    #public static function test() {
    #  return get_called_class();
    #}

  /*  public static function where($field, $equation, $value = false) {
        $class = get_called_class();
        $model = new $class;
        $model::$sql = 'SELECT * FROM ' . $model->table . ' WHERE ';
        if (! $value) {
            $model::$sql .= $field . '=' . (is_numeric($equation)?$equation:('"' . $equation .'"'));
        } else {
            $model::$sql .= $field . $equation . (is_numeric($equation)?$equation:('"' . $equation .'"'));
        }
        return $model;
    }

    public function limit($limit) {
        self::$sql .= ' LIMIT ' . $limit;
        return $this;
    }

    public function orderBy($field, $sort = 'asc') {
        self::$sql .= ' ORDER BY ' . $field . ' ' . $sort;
        return $this;
    }

    public function get() {
        $results = $this->query(self::$sql);
        $class = get_called_class();
        $entities = [];
        foreach ($results as $item) {
            $entities[] = new $class($item);
        }
        return $entities;
    }*/

    public function all() {
        $sql = 'SELECT * FROM ' . $this->table;
        return $this->query($sql);
    }

    public static function fromCache($slug) {
        $class = get_called_class();
        $entity = new $class;
        $cache_file = APP_PATH . '/public/cache/' . $entity->table . '/' . $slug . '.json';
        if (file_exists($cache_file)) {
            $data = json_decode(file_get_contents($cache_file), true);
            $entity->setData($data);
            return $entity;
        } else {
            $entity = $class::find($slug, 'slug');
            $entity->saveCache();
            return $entity;
        }
    }

    protected function saveCache() {
        $data = [];
        foreach ($this->fillable as $field) {
            $data[$field] = isset($this->{$field})?$this->{$field}:null;
        }
        save_file(APP_PATH . '/public/cache/' . $this->table . '/' . $this->slug . '.json', json_encode($data));
    }


    public static function find($value, $field = 'id') {
        $class = get_called_class();
        $entity = new $class;
        if (!is_numeric($value)) {
            $value = '"' . $value . '"';
        }
        $sql = 'SELECT * FROM ' . $entity->table . ' WHERE ' . $field . '='. $value .' LIMIT 1';
        $result = $entity->query($sql);
        if (! $result) {
            return null;
        }
        $entity->setData($result[0]);
        return $entity;
    }

    protected function setData($data) {
        foreach ($this->fillable as $field) {
            $this->{$field} = isset($data[$field])?$data[$field]:null;
        }
    }

    public static function create($table, $data) {
        $keys = [];
        $values = [];
        foreach ($data as $key => $value) {
            $keys[] = $key;
            if (is_numeric($value)) {
                $values[] = $value;
            } else {
                $values[] = '"' . str_replace('"', '\"', $value) . '"';
            }
        }
        $sql = 'INSERT INTO ' . $table . '(' . implode(',', $keys) . ') VALUES(' . implode(',', $values). ')';
        return Model::query($sql);
    }

    public function toArray() {
        $result = [];
        foreach ($this->fields as $field) {
            $result[$field] = isset($this->{$field})?$this->{$field}:null;
        }
        return $result;
    }

    public static function update($id, $data, $field = 'id') {
        $values = [];
        foreach ($data as $key => $value) {
            if (is_numeric($value)) {
                $values[] = $key . '=' . $value;
            } else {
                $values[] = $key . '=' . '"' . str_replace('"', '\"', $value) . '"';
            }
        }
        $sql = 'UPDATE ' . $this->table . ' SET ' . implode(',', $values) . ' WHERE ' . $field . '=' . $id;
        return $this->query($sql);
    }

    public static function delete($value, $field = 'id') {
        if (!is_numeric($value)) {
            $value = '"' . $value . '"';
        }
        $sql = 'DELETE FROM ' . $this->table . ' WHERE ' . $field . '='. $value;
        return $this->query($sql);
    }

    public static function query($sql, $fetch = 'assoc') {
        return Database::query($sql, $fetch);
    }

    private static function getInstance($data = []) {
        $class = get_called_class();
        return new $class($data);
    }
}
