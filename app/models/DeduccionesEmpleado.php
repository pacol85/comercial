<?php

class DeduccionesEmpleado extends \Phalcon\Mvc\Model
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
    public $deduccion;

    /**
     *
     * @var string
     */
    public $monto;

    /**
     *
     * @var string
     */
    public $descripcion;

    /**
     *
     * @var string
     */
    public $finicio;

    /**
     *
     * @var string
     */
    public $ffin;

    /**
     *
     * @var string
     */
    public $empleado;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->belongsTo('empleado', 'Empleado', 'id', array('alias' => 'Empleado'));
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'deducciones_empleado';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return DeduccionesEmpleado[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return DeduccionesEmpleado
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
