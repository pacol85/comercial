<?php

class Cliente extends \Phalcon\Mvc\Model
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
    public $fexpedicion;

    /**
     *
     * @var string
     */
    public $lugarExpedicion;

    /**
     *
     * @var string
     */
    public $direccion;

    /**
     *
     * @var string
     */
    public $municipio;

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
    public $jefe;

    /**
     *
     * @var string
     */
    public $sueldo;

    /**
     *
     * @var string
     */
    public $telOficina;

    /**
     *
     * @var string
     */
    public $estado;

    /**
     *
     * @var string
     */
    public $dui;

    /**
     *
     * @var string
     */
    public $nit;

    /**
     *
     * @var string
     */
    public $telcasa;

    /**
     *
     * @var string
     */
    public $celular;

    /**
     *
     * @var string
     */
    public $foto;
    
    /**
     *
     * @var string
     */
    public $pagador;

    /**
     *
     * @var string
     */
    public $fcreacion;
    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->hasMany('id', 'CreditoXCliente', 'cliente', array('alias' => 'CreditoXCliente'));
        $this->hasMany('id', 'Factura', 'cliente', array('alias' => 'Factura'));
        $this->hasMany('id', 'Referencia', 'cliente', array('alias' => 'Referencia'));
        $this->belongsTo('municipio', 'Municipios', 'id', array('alias' => 'Municipios'));
        $this->belongsTo('estado', 'EstadoCliente', 'id', array('alias' => 'EstadoCliente'));
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'cliente';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Cliente[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Cliente
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
