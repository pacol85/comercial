<?php

class ItemXFactura extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var string
     */
    public $item;

    /**
     *
     * @var string
     */
    public $factura;

    /**
     *
     * @var string
     */
    public $cantidad;

    /**
     *
     * @var string
     */
    public $precio;

    /**
     *
     * @var string
     */
    public $empleado;

    /**
     *
     * @var string
     */
    public $comision;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->belongsTo('item', 'Item', 'id', array('alias' => 'Item'));
        $this->belongsTo('factura', 'Factura', 'id', array('alias' => 'Factura'));
        $this->belongsTo('empleado', 'Empleado', 'id', array('alias' => 'Empleado'));
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'item_x_factura';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ItemXFactura[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ItemXFactura
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
