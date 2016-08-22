<?php

class Envios extends \Phalcon\Mvc\Model
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
    public $santerior;

    /**
     *
     * @var string
     */
    public $snueva;

    /**
     *
     * @var string
     */
    public $fecha;

    /**
     *
     * @var string
     */
    public $item;

    /**
     *
     * @var string
     */
    public $cantidad;

    /**
     *
     * @var string
     */
    public $empSaca;

    /**
     *
     * @var string
     */
    public $empRecibe;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->belongsTo('santerior', 'Sucursal', 'id', array('alias' => 'Sucursal'));
        $this->belongsTo('snueva', 'Sucursal', 'id', array('alias' => 'Sucursal'));
        $this->belongsTo('item', 'Item', 'id', array('alias' => 'Item'));
        $this->belongsTo('empSaca', 'Empleado', 'id', array('alias' => 'Empleado'));
        $this->belongsTo('empRecibe', 'Empleado', 'id', array('alias' => 'Empleado'));
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'envios';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Envios[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Envios
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
