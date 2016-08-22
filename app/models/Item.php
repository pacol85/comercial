<?php

class Item extends \Phalcon\Mvc\Model
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
    public $marca;

    /**
     *
     * @var string
     */
    public $modelo;

    /**
     *
     * @var string
     */
    public $descripcion;

    /**
     *
     * @var string
     */
    public $valor;

    /**
     *
     * @var string
     */
    public $impuesto;

    /**
     *
     * @var string
     */
    public $total;

    /**
     *
     * @var string
     */
    public $minimo;

    /**
     *
     * @var string
     */
    public $codigo;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->hasMany('id', 'Envios', 'item', array('alias' => 'Envios'));
        $this->hasMany('id', 'ItemXFactura', 'item', array('alias' => 'ItemXFactura'));
        $this->hasMany('id', 'ItemXSucursal', 'item', array('alias' => 'ItemXSucursal'));
        $this->hasMany('id', 'OrdenCompra', 'item', array('alias' => 'OrdenCompra'));
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'item';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Item[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Item
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
