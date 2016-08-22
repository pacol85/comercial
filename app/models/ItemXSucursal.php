<?php

class ItemXSucursal extends \Phalcon\Mvc\Model
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
    public $sucursal;

    /**
     *
     * @var string
     */
    public $cantidad;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->belongsTo('item', 'Item', 'id', array('alias' => 'Item'));
        $this->belongsTo('sucursal', 'Sucursal', 'id', array('alias' => 'Sucursal'));
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'item_x_sucursal';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ItemXSucursal[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ItemXSucursal
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
