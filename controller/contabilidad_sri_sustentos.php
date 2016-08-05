<?php
/*
 * This file is part of FacturaSctipts
 * Copyright (C) 2013-2015  Carlos Garcia Gomez  neorazorx@gmail.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_model('sri_sustento.php');

class contabilidad_sri_sustentos extends fs_controller
{
   public $allow_delete;
   public $sri_sustento;
   
   public function __construct()
   {
      parent::__construct(__CLASS__, 'SRI Sustento Tributario', 'contabilidad', FALSE, TRUE);
   }
   
   protected function private_core()
   {
      /// ¿El usuario tiene permiso para eliminar en esta página?
      $this->allow_delete = $this->user->allow_delete_on(__CLASS__);
      
      $this->sri_sustento = new sri_sustento();
      
      $fsvar = new fs_var();

      if( isset($_POST['codsustento']) )
      {
         $sri_sustento = $this->sri_sustento->get($_POST['codsustento']);
         if( !$sri_sustento )
         {
            $sri_sustento = new sri_sustento();
            $sri_sustento->codsustento = $_POST['codsustento'];
         }
         $sri_sustento->descripcion = $_POST['descripcion'];
                 
         if( $sri_sustento->save() )
         {
            $this->new_message("Sustento tributario ".$sri_sustento->codsustento." guardado correctamente");
         }
         else
            $this->new_error_msg("¡Imposible guardar el registro!");
      }
      else if( isset($_GET['delete']) )
      {
         if(!$this->user->admin)
         {
            $this->new_error_msg('Sólo un administrador puede eliminar sustentos.');
         }
         else
         {
            $sri_sustento = $this->sri_sustento->get($_GET['delete']);
            if($sri_sustento)
            {
               if( $sri_sustento->delete() )
               {
                  $this->new_message('Sustento tributario eliminado correctamente.');
               }
               else
                  $this->new_error_msg("¡Imposible eliminar el registro!");
            }
            else
               $this->new_error_msg("Codigo de sustento no encontrado.");
         }
      }
   }
}
