<?php

/**
 * Module Factory class.
 *
 * @package    Webshr\Theme
 * @since  1.0.0
 */

namespace Webshr\Core\Modules;

use Webshr\Core\Modules\Contracts\Module as Module_interface;

class Module_Factory
{
    /**
     * @var Module_Interface The module instance.
     */
    protected $module;
    /**
     * Create Module instance.
     *
     * @param  string  $class  Module class name
     * @return Module_Interface
     * @throws \Exception
     */
    public function create(string $class): Module_Interface
    {
        if (! class_exists($class) || ! is_subclass_of($class, Module_Interface::class)) {
            throw new \Exception("Module class {$class} must exist and implement Module_Interface.");
        }

        return new $class();
    }
}
