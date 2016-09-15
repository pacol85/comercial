<?php

class Usuario extends \Phalcon\Mvc\Model
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
    public $usuario;

    /**
     *
     * @var string
     */
    public $clave;

    /**
     *
     * @var string
     */
    public $rol_id;

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
    public $fclave;
    
    /**
     *
     * @var integer
     */
    public $estado;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->hasMany('id', 'ClavesUsuario', 'usuario', array('alias' => 'ClavesUsuario'));
        $this->belongsTo('rol_id', 'Roles', 'id', array('alias' => 'Roles'));
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'usuario';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Usuario[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Usuario
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
