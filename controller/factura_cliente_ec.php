<?php

/**
 * Created by PhpStorm.
 * User: SaykoPC
 * Date: 04/04/2017
 * Time: 21:52
 */
require_model('factura_cliente.php');
require_model('articulo.php');



class factura_cliente_ec extends  fs_controller
{

public $factura;

    public function __construct()
    {
        parent::__construct(__CLASS__, 'Factura de cliente', 'ventas', FALSE,  FALSE);
    }

    protected function private_core()
    {

        $this->factura = FALSE;
        if( isset($_REQUEST['id']) )
        {
            $fc = new factura_cliente();
            $this->factura = $fc->get($_REQUEST['id']);

        }

        if( isset($_GET['anular']) )
        {
            $this->anular_factura();
        }


    }

    private function anular_factura()
    {
        $fc = new factura_cliente();
        $this->factura = $fc->get($_GET['anular']);

        if($this->factura)
        {

            /// Ponemos valores en ceros de la factura para informes
            $this->factura->anulada = TRUE;
            $this->factura->rent_iva = NULL;
            $this->factura->rent_fuente = NULL;
            $this->factura->rent_fuente_por = 0.00;
            $this->factura->rent_iva_por = 0.00;
            $this->factura->totaliva = 0.00;
            $this->factura->totaleuros = 0.00;
            $this->factura->neto =  0.00 ;
            $this->factura->total =  0.00 ;
            $this->factura->fecha_anulada = $this->today();
            $this->factura->hora_anulada = $this->hour();
            $this->factura->save();


            $linea_iva0 = new linea_iva_factura_cliente();

            foreach($this->factura->get_lineas_iva() as $linea_iva)
            {

                if($linea_iva0){

                    $linea_iva->iva = 0 ;
                    $linea_iva->neto = 0 ;
                    $linea_iva->recargo = 0;
                    $linea_iva->totaliva = 0;
                    $linea_iva->totallinea = 0;
                    $linea_iva->totalrecargo = 0;
                    $linea_iva->save();

                }


            }


            /// Restauramos el stock
            $art0 = new articulo();


            foreach($this->factura->get_lineas() as $linea)

            {
                if( is_null($linea->idalbaran) )
                {
                    $articulo = $art0->get($linea->referencia);



                    if($articulo)
                    {

                        $articulo->sum_stock($this->factura->codalmacen, $linea->cantidad);


                        //$linea->cantidad = 0;
                        $linea->pvpsindto = 0.00;
                        $linea->pvpunitario = 0.00;
                        $linea->pvptotal = 0.00;
                        $linea->save();

                    }
                }
            }

            $this->new_message("Factura de venta ".$this->factura->codigo." anulada correctamente.", TRUE);
            $this->clean_last_changes();
            header('Location:index.php?page=ventas_factura&id='.$this->factura->idfactura);


        }else
          {
            $this->new_error_msg("Factura no encontrada.");
          }

    }

    public function url()
    {
        if($this->factura)
        {
            return parent::url().'&id='.$this->factura->idfactura;
        }
        else
        {
            return parent::url();
        }
    }

}