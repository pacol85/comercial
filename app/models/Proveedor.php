<?php

class Proveedor extends \Phalcon\Mvc\Model
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
    public $documento;

    /**
     *
     * @var string
     */
    public $direccion;

    /**
     *
     * @var string
     */
    public $documento2;

    /**
     *
     * @var string
     */
    public $telefono;

    /**
     *
     * @var string
     */
    public $tipo;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->hasMany('id', 'Cheques', 'proveedor', array('alias' => 'Cheques'));
        $this->hasMany('id', 'Contactos', 'proveedor', array('alias' => 'Contactos'));
        $this->hasMany('id', 'OrdenCompra', 'proveedor', array('alias' => 'OrdenCompra'));
        $this->belongsTo('tipo', 'TipoProveedor', 'id', array('alias' => 'TipoProveedor'));
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'proveedor';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Proveedor[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Proveedor
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
