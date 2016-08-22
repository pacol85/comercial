<?php

use Phalcon\Mvc\Model\Validator\Email as Email;

class Sucursal extends \Phalcon\Mvc\Model
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
    public $email;

    /**
     *
     * @var string
     */
    public $fax;

    /**
     *
     * @var string
     */
    public $empresa;

    /**
     * Validations and business logic
     *
     * @return boolean
     */
    public function validation()
    {
        $this->validate(
            new Email(
                array(
                    'field'    => 'email',
                    'required' => true,
                )
            )
        );

        if ($this->validationHasFailed() == true) {
            return false;
        }

        return true;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->hasMany('id', 'Empleado', 'sucursal', array('alias' => 'Empleado'));
        $this->hasMany('id', 'Empresa', 'matriz', array('alias' => 'Empresa'));
        $this->hasMany('id', 'Envios', 'santerior', array('alias' => 'Envios'));
        $this->hasMany('id', 'Envios', 'snueva', array('alias' => 'Envios'));
        $this->hasMany('id', 'ItemXSucursal', 'sucursal', array('alias' => 'ItemXSucursal'));
        $this->belongsTo('empresa', 'Empresa', 'id', array('alias' => 'Empresa'));
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'sucursal';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Sucursal[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Sucursal
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
