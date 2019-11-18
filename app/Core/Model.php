<?php

namespace App\Core;

use App\Core\Db;

class Model
{
    /**
     * @var Db
     */
    public $db;

    /**
     * @var table
     */
    private $table;

    /**
     * @var array
     */
    private $tpl;

    /**
     * Db constructor.
     */
    public function __construct()
    {
        // Iniciando conexao com o banco
        $this->db = (new Db())->conn;
        $this->table = strtolower(explode("\\", get_class($this))[2])."s";

        // Init template
        $this->tpl['INSERT'] = "INSERT INTO %table% (%fields%) VALUES (%values%)";
        $this->tpl['SELECT'] = "SELECT %fields% FROM %table% WHERE %where%";
    }

    /**
     * Select regs
     *
     * @return array
     */
    public function find(): array
    {
        return $this->sanitizeExecFind("SELECT");
    }


    /**
     * Save regs
     *
     * @return void
     */
    public function save(): void
    {

        if (count($this->fillable)) {
            if (isset($this->id) && is_int($this->id)) {
                $this->sanitizeExecSave("UPDATE");
            } else {
                $this->sanitizeExecSave("INSERT");
            }
        }
    }

    /**
     * Escapa caracteres e executa sql
     *
     * @param string $action
     * @return array
     */
    private function sanitizeExecFind(string $action): array
    {

        $fields = [];
        $where = [];
        $values = [];

        foreach ($this->fillable as $field) {
            $fields[] = $field;

            if (isset($this->{$field})) {
                $where[] = "$field = ?";
                $values[] = $this->{$field};
            }
        }

        
        $fields = implode(",", $fields);

        $tpl = $this->tpl[$action];
        $tpl = str_replace("%table%", $this->table, $tpl);
        $tpl = str_replace("%fields%", $fields, $tpl);

        if (!count($where)) {
            $tpl = explode("WHERE", $tpl);
            $tpl = $tpl[0];
        } else {
            $where = implode(" AND ", $where);
            $tpl = str_replace("%where%", $where, $tpl);
        }
        
        
        
        $sql = $this->db->prepare($tpl);

        $pass = 1;
        foreach ($this->fillable as $field) {
            if (isset($this->{$field})) {
                $sql->bindParam($pass, $this->{$field});
                $pass++;
            }
        }



        $sql->execute();

        // $sql->debugDumpParams();

        return $sql->fetchAll();
    }



    /**
     * Escapa caracteres e executa sql
     *
     * @param string $action
     * @return void
     */
    private function sanitizeExecSave(string $action): void
    {

        $fields = [];
        $values = [];

        foreach ($this->fillable as $field) {
            if (isset($this->{$field})) {
                if ($field == "id") {
                    continue;
                }
                
                $fields[] = $field;
                $values[] = "?";
            }
        }

        $fields = implode(",", $fields);
        $values = implode(",", $values);

        $tpl = $this->tpl[$action];
        $tpl = str_replace("%table%", $this->table, $tpl);
        $tpl = str_replace("%fields%", $fields, $tpl);
        $tpl = str_replace("%values%", $values, $tpl);

        
        $sql = $this->db->prepare($tpl);

        $pass = 1;
        foreach ($this->fillable as $field) {
            if (isset($this->{$field})) {
                if ($field == "id") {
                    continue;
                }
                $sql->bindParam($pass, $this->{$field});
                $pass++;
            }
        }

        $sql->execute();

        // $sql->debugDumpParams();
    }
}
