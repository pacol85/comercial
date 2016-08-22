<?php

class Cheques extends \Phalcon\Mvc\Model
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
    public $monto;

    /**
     *
     * @var string
     */
    public $fecha;

    /**
     *
     * @var string
     */
    public $concepto;

    /**
     *
     * @var string
     */
    public $proveedor;

    /**
     *
     * @var string
     */
    public $banco;

    /**
     *
     * @var string
     */
    public $recibidopor;

    /**
     *
     * @var string
     */
    public $revisadopor;

    /**
     *
     * @var string
     */
    public $autorizadopor;

    /**
     *
     * @var string
     */
    public $frecibido;

    /**
     *
     * @var string
     */
    public $frevisado;

    /**
     *
     * @var string
     */
    public $fautorizado;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->belongsTo('proveedor', 'Proveedor', 'id', array('alias' => 'Proveedor'));
        $this->belongsTo('banco', 'Bancos', 'id', array('alias' => 'Bancos'));
        $this->belongsTo('revisadopor', 'Empleado', 'id', array('alias' => 'Empleado'));
        $this->belongsTo('autorizadopor', 'Empleado', 'id', array('alias' => 'Empleado'));
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'cheques';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Cheques[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Cheques
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
