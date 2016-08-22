<?php

class Factura extends \Phalcon\Mvc\Model
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
    public $cliente;

    /**
     *
     * @var string
     */
    public $fecha;

    /**
     *
     * @var string
     */
    public $estado;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->hasMany('id', 'ItemXFactura', 'factura', array('alias' => 'ItemXFactura'));
        $this->belongsTo('cliente', 'Cliente', 'id', array('alias' => 'Cliente'));
        $this->belongsTo('estado', 'EstadoFactura', 'id', array('alias' => 'EstadoFactura'));
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'factura';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Factura[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Factura
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
