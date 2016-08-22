<?php

class OrdenCompra extends \Phalcon\Mvc\Model
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
    public $item;

    /**
     *
     * @var string
     */
    public $proveedor;

    /**
     *
     * @var string
     */
    public $cantidad;

    /**
     *
     * @var string
     */
    public $fecha_compra;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->belongsTo('item', 'Item', 'id', array('alias' => 'Item'));
        $this->belongsTo('proveedor', 'Proveedor', 'id', array('alias' => 'Proveedor'));
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'orden_compra';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return OrdenCompra[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return OrdenCompra
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
