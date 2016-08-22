<?php

class Empleado extends \Phalcon\Mvc\Model
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
    public $primer_nombre;

    /**
     *
     * @var string
     */
    public $segundo_nombre;

    /**
     *
     * @var string
     */
    public $primer_apellido;

    /**
     *
     * @var string
     */
    public $segundo_apellido;

    /**
     *
     * @var string
     */
    public $apellido_casada;

    /**
     *
     * @var string
     */
    public $genero;

    /**
     *
     * @var string
     */
    public $fecha_nacimiento;

    /**
     *
     * @var string
     */
    public $direccion;

    /**
     *
     * @var string
     */
    public $fecha_ingreso;

    /**
     *
     * @var string
     */
    public $sucursal;

    /**
     *
     * @var integer
     */
    public $estado;

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
    public $salario;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->hasMany('id', 'Cheques', 'revisadopor', array('alias' => 'Cheques'));
        $this->hasMany('id', 'Cheques', 'autorizadopor', array('alias' => 'Cheques'));
        $this->hasMany('id', 'Cuotas', 'empleado', array('alias' => 'Cuotas'));
        $this->hasMany('id', 'DeduccionesEmpleado', 'empleado', array('alias' => 'DeduccionesEmpleado'));
        $this->hasMany('id', 'Envios', 'empSaca', array('alias' => 'Envios'));
        $this->hasMany('id', 'Envios', 'empRecibe', array('alias' => 'Envios'));
        $this->hasMany('id', 'ItemXFactura', 'empleado', array('alias' => 'ItemXFactura'));
        $this->belongsTo('sucursal', 'Sucursal', 'id', array('alias' => 'Sucursal'));
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'empleado';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Empleado[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Empleado
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
