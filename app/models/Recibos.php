<?php

class Recibos extends \Phalcon\Mvc\Model
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
    public $numero;

    /**
     *
     * @var string
     */
    public $cuota;

    /**
     *
     * @var string
     */
    public $fpago;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->belongsTo('cuota', 'Cuotas', 'id', array('alias' => 'Cuotas'));
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'recibos';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Recibos[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Recibos
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
