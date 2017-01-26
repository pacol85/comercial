<?php

class Fiador extends \Phalcon\Mvc\Model
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
    public $nombre;

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
     * @var integer
     */
    public $alquila;

    /**
     *
     * @var string
     */
    public $propietario;

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
    public $depto;

    /**
     *
     * @var string
     */
    public $jefe;

    /**
     *
     * @var string
     */
    public $pagador;

    /**
     *
     * @var string
     */
    public $cargo;

    /**
     *
     * @var string
     */
    public $sueldo;

    /**
     *
     * @var string
     */
    public $desde;

    /**
     *
     * @var string
     */
    public $telofic;

    /**
     *
     * @var string
     */
    public $dirtrab;

    /**
     *
     * @var string
     */
    public $dui;

    /**
     *
     * @var string
     */
    public $expedicion;

    /**
     *
     * @var string
     */
    public $fexpedicion;

    /**
     *
     * @var string
     */
    public $conyugue;

    /**
     *
     * @var string
     */
    public $ctrabajo;

    /**
     *
     * @var string
     */
    public $cdepto;

    /**
     *
     * @var string
     */
    public $ccargo;

    /**
     *
     * @var string
     */
    public $cdesde;

    /**
     *
     * @var string
     */
    public $ctelefono;

    /**
     *
     * @var string
     */
    public $cdirtrab;

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
    public $cliente;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->hasMany('id', 'CreditoXCliente', 'fiador', array('alias' => 'CreditoXCliente'));
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'fiador';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Fiador[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Fiador
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
