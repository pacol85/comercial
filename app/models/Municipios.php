<?php

class Municipios extends \Phalcon\Mvc\Model
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
    public $descripcion;

    /**
     *
     * @var string
     */
    public $departamento;

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
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->hasMany('id', 'Cliente', 'municipio', array('alias' => 'Cliente'));
        $this->belongsTo('departamento', 'Departamentos', 'id', array('alias' => 'Departamentos'));
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'municipios';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Municipios[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Municipios
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
