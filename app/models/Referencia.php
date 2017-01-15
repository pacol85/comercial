<?php

class Referencia extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var string
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $pariente;

    /**
     *
     * @var string
     */
    public $parentesco;

    /**
     *
     * @var string
     */
    public $direccion;

    /**
     *
     * @var string
     */
    public $telefono;

    /**
     *
     * @var string
     */
    public $trabajo;

    /**
     *
     * @var string
     */
    public $areaTrab;

    /**
     *
     * @var string
     */
    public $cargo;

    /**
     *
     * @var string
     */
    public $fdesde;

    /**
     *
     * @var string
     */
    public $telOficina;

    /**
     *
     * @var string
     */
    public $cliente;

    /**
     *
     * @var integer
     */
    public $validez;

    /**
     *
     * @var string
     */
    public $fcreacion;

    /**
     *
     * @var string
     */
    public $fmod;

    /**
     *
     * @var string
     */
    public $nombre;

    /**
     *
     * @var string
     */
    public $sueldo;

    /**
     *
     * @var string
     */
    public $jefe;

    /**
     *
     * @var string
     */
    public $referencias;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->hasMany('id', 'CreditoXCliente', 'pariente', array('alias' => 'CreditoXCliente'));
        $this->hasMany('id', 'CreditoXCliente', 'amigo', array('alias' => 'CreditoXCliente'));
        $this->belongsTo('cliente', 'Cliente', 'id', array('alias' => 'Cliente'));
        $this->belongsTo('parentesco', 'Parentesco', 'id', array('alias' => 'Parentesco'));
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'referencia';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Referencia[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Referencia
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
