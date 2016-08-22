<?php

class MenuXRol extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var string
     */
    public $menu;

    /**
     *
     * @var string
     */
    public $rol;

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
        $this->belongsTo('menu', 'Menu', 'id', array('alias' => 'Menu'));
        $this->belongsTo('rol', 'Roles', 'id', array('alias' => 'Roles'));
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'menu_x_rol';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return MenuXRol[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return MenuXRol
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
