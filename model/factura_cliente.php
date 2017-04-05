<?php

/**
 * Created by PhpStorm.
 * User: SaykoPC
 * Date: 04/04/2017
 * Time: 21:40
 */

require_once 'plugins/facturacion_base/model/core/factura_cliente.php';



class factura_cliente extends  FacturaScripts\model\factura_cliente
{

    public function new_codigo()
    {

        /// buscamos el numero inicial para la serie
        $num = 1;
        $serie0 = new \serie();
        $serie = $serie0->get($this->codserie);
        if($serie)
        {

            $num = $serie->numfactura;

        }



        /// buscamos un hueco o el siguiente numero disponible
        $encontrado = FALSE;
        $fecha = $this->fecha;
        $hora = $this->hora;
        $sql = "SELECT ".$this->db->sql_to_int('numero')." as numero,fecha,hora FROM ".$this->table_name
            ." WHERE codserie = ".$this->var2str($this->codserie)
            ." ORDER BY numero ASC;";

        $data = $this->db->select($sql);
        if($data)
        {
            foreach($data as $d)
            {
                if( intval($d['numero']) < $num )
                {
                    /**
                     * El numero de la factura es menor que el inicial.
                     * El usuario ha cambiado el numero inicial despuÃ©s de hacer
                     * facturas.
                     */
                }
                else if( intval($d['numero']) == $num )
                {
                    /// el numero es correcto, avanzamos
                    $num++;
                }
                else
                {
                    /// Hemos encontrado un hueco y debemos usar el nÃºmero y la fecha.
                    $encontrado = TRUE;
                    $fecha = Date('d-m-Y', strtotime($d['fecha']));
                    $hora = Date('H:i:s', strtotime($d['hora']));
                    break;
                }
            }
        }
        if($encontrado)
        {
            $this->numero = $num;
            $this->fecha = $fecha;
            $this->hora = $hora;
        }
        else
        {
            $this->numero = $num;

        }

        $this->codigo = $this->codserie.sprintf('%07s', $this->numero);

    }

}