<?php

class Cuotas extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var string
     */
    public $id;

    /**
     *
     * @var string
     */
    public $monto;

    /**
     *
     * @var string
     */
    public $fechaPago;

    /**
     *
     * @var string
     */
    public $pormenores;

    /**
     *
     * @var string
     */
    public $nota;

    /**
     *
     * @var string
     */
    public $empleado;

    /**
     *
     * @var string
     */
    public $credito;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->belongsTo('empleado', 'Empleado', 'id', array('alias' => 'Empleado'));
        $this->belongsTo('credito', 'CreditoXCliente', 'id', array('alias' => 'CreditoXCliente'));
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'cuotas';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Cuotas[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Cuotas
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
