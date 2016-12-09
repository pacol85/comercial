<?php

class CreditoXCliente extends \Phalcon\Mvc\Model
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
    public $fecha_adquisicion;

    /**
     *
     * @var string
     */
    public $monto;

    /**
     *
     * @var string
     */
    public $fecha_cancelacion;

    /**
     *
     * @var string
     */
    public $cliente;

    /**
     *
     * @var string
     */
    public $fiador;

    /**
     *
     * @var string
     */
    public $fsolicitud;

    /**
     *
     * @var string
     */
    public $cuotaBase;

    /**
     *
     * @var string
     */
    public $interes;

    /**
     *
     * @var integer
     */
    public $diaCorte;

    /**
     *
     * @var string
     */
    public $prima;

    /**
     *
     * @var string
     */
    public $primerPago;

    /**
     *
     * @var string
     */
    public $pariente;

    /**
     *
     * @var string
     */
    public $amigo;

    /**
     *
     * @var string
     */
    public $sucursal;

    /**
     *
     * @var string
     */
    public $cuenta;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->hasMany('id', 'Cuotas', 'credito', array('alias' => 'Cuotas'));
        $this->belongsTo('cliente', 'Cliente', 'id', array('alias' => 'Cliente'));
        $this->belongsTo('fiador', 'Fiador', 'id', array('alias' => 'Fiador'));
        $this->belongsTo('pariente', 'Referencia', 'id', array('alias' => 'Referencia'));
        $this->belongsTo('amigo', 'Referencia', 'id', array('alias' => 'Referencia'));
        $this->belongsTo('sucursal', 'Sucursal', 'id', array('alias' => 'Sucursal'));
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'credito_x_cliente';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return CreditoXCliente[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return CreditoXCliente
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
