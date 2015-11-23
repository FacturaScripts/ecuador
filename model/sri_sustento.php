<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class sri_sustento extends fs_model {

    public $codsustento;
    public $descripcion;

    public function __construct($f = FALSE) {
        parent::__construct('sri_sustento', 'plugins/ecuador/');
        if ($f) {
            $this->codsustento = $f['codsustento'];
            $this->descripcion = $f['descripcion'];
        } else {
            $this->codsustento = NULL;
            $this->descripcion = '';
        }
    }

    protected function install() {
        $this->clean_cache();
        return "INSERT INTO " . $this->table_name . " (codsustento,descripcion) VALUES ('01','Crédito Tributario para declaración de IVA');";
    }

    private function clean_cache() {
        $this->cache->delete('m_sri_sustento_all');
    }

    public function url() {
        if (is_null($this->codsustento)) {
            return 'index.php?page=contabilidad_sri_sustentos';
        } else
            return 'index.php?page=contabilidad_sri_sustentos#' . $this->codsustento;
    }

    public function delete() {
        $this->clean_cache();
        $sql = "DELETE FROM " . $this->table_name . " WHERE codsustento = " . $this->var2str($this->codsustento) . ";";

        return $this->db->exec($sql);
    }

    public function exists() {
        
    }
    
    public function get_codigo_descripcion() {
        return $this->codsustento." ".$this->descripcion;
    }

    public function test() {
        $status = FALSE;

        $this->codsustento = trim($this->codsustento);
        $this->descripcion = $this->no_html($this->descripcion);

        if (strlen($this->codsustento) < 2 OR strlen($this->codsustento) > 2) {
            $this->new_error_msg("Código no válido. Deben ser entre 2 carateres.");
        } else if (strlen($this->descripcion) < 1 OR strlen($this->descripcion) > 100) {
            $this->new_error_msg("Descripción no válida.");
        } else
            $status = TRUE;

        return $status;
    }

    public function save() {
        if ($this->test()) {
            $this->clean_cache();

            if ($this->exists()) {
                $sql = "UPDATE " . $this->table_name . " SET descripcion = " . $this->var2str($this->descripcion) .
                        " WHERE codsustento = " . $this->var2str($this->codsustento) . ";";
            } else {
                $sql = "INSERT INTO " . $this->table_name . " (codsustento,descripcion) VALUES " .
                        "(" . $this->var2str($this->codsustento) .
                        "," . $this->var2str($this->descripcion) .");";
            }

            return $this->db->exec($sql);
        } else
            return FALSE;
    }

    public function all() {
        $sri_sustentolist = $this->cache->get_array('m_sri_sustento_all');
        if (!$sri_sustentolist) {
            $sri_sustentos = $this->db->select("SELECT * FROM " . $this->table_name . " ORDER BY codsustento ASC;");
            if ($sri_sustentos) {
                foreach ($sri_sustentos as $s)
                    $sri_sustentolist[] = new sri_sustento($s);
            }
            $this->cache->set('m_sri_sustento_all', $sri_sustentolist);
        }

        return $sri_sustentolist;
    }
   public function get($cod)
   {
      $sri_sustento = $this->db->select("SELECT * FROM ".$this->table_name." WHERE codsustento = ".$this->var2str($cod).";");
      if($sri_sustento)
      {
         return new sri_sustento($sri_sustento[0]);
      }
      else
         return FALSE;
   }
   
}
