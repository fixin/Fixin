<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\ResourceManager;

use Closure;
use Fixin\Base\Configurable\ConfigurableInterface;
use Fixin\Base\Exception\InvalidParameterException;
use Fixin\ResourceManager\AbstractFactory\AbstractFactoryInterface;
use Fixin\ResourceManager\Factory\FactoryInterface;
use Fixin\Support\PrototypeInterface;

class ResourceManager implements ResourceManagerInterface, ConfigurableInterface {

    const ABSTRACT_FACTORIES_KEY = 'abstractFactories';
    const CLASS_KEY = 'class';
    const DEFINITIONS_KEY = 'definitions';

    /**
     * Abstract factories
     *
     * @var array
     */
    protected $abstractFactories = [];

    /**
     * Definitions
     *
     * @var array
     */
    protected $definitions = [];

    /**
     * Allocated resources
     *
     * @var array
     */
    protected $resources = [];

    /**
     * @param array $config
     */
    public function __construct(array $config = []) {
        $this->configure($config);
    }

    /**
     * Add abstract factory
     *
     * @param string|object $abstractFactory
     * @return self
     */
    public function addAbstractFactory($abstractFactory) {
        $this->setupAbstractFactories([$abstractFactory]);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\ResourceManager\ResourceManagerInterface::clonePrototype()
     */
    public function clonePrototype(string $name) {
        $arr = $this->resources[$name] ?? $this->produceResource($name);

        if (isset($arr[1])) {
            return clone $arr[1];
        }

        // Not found
        throw new Exception\ResourceNotFoundException((isset($arr[0]) ? 'Resource registered' : 'Prototype is not registered') . " with name '$name'");
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Configurable\ConfigurableInterface::configure()
     */
    public function configure(array $config) {
        // Abstract factories
        if (isset($config[static::ABSTRACT_FACTORIES_KEY])) {
            $this->setupAbstractFactories($config[static::ABSTRACT_FACTORIES_KEY]);
        }

        // Inject options
        if (isset($config[static::DEFINITIONS_KEY])) {
            $values = $config[static::DEFINITIONS_KEY];

            if ($names = array_intersect_key($values, $this->definitions)) {
                throw new Exception\OverrideNotAllowedException("Definition already defined for '" . implode("', '", array_keys($names)) . "'");
            }

            $this->definitions = $values + $this->definitions;
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Support\ContainerInterface::get($name)
     */
    public function get(string $name) {
        $arr = $this->resources[$name] ?? $this->produceResource($name);

        if (isset($arr[0])) {
            return $arr[0];
        }

        // Not found
        throw new Exception\ResourceNotFoundException((isset($arr[1]) ? 'Prototype registered' : 'Resource is not registered') . " with name '$name'");
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Support\ContainerInterface::has($name)
     */
    public function has(string $name): bool {
        // Made or defined
        if (isset($this->resources[$name]) || isset($this->definitions[$name])) {
            return true;
        }

        // Abstract factories
        foreach ($this->abstractFactories as $abstractFactory) {
            if ($abstractFactory->canProduce($this, $name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Preprocess definition
     *
     * @param mixed $definition
     * @return mixed
     */
    protected function preprocessDefinition($definition) {
        // Resolve class name
        if (is_string($definition) && class_exists($definition)) {
            return new $definition($this);
        }
        // Resolve class array
        elseif (isset($definition[static::CLASS_KEY]) && class_exists($class = $definition[static::CLASS_KEY])) {
            unset($definition[static::CLASS_KEY]);

            return new $class($this, $definition);
        }

        return $definition;
    }

    /**
     * Produce resource
     *
     * @param string $name
     * @return object
     */
    protected function produceResource(string $name) {
        $instance = null;

        if (isset($this->definitions[$name])) {
            $instance = $this->produceResourceFromDefinition($name);
        }

        // Abstract factories
        foreach ($this->abstractFactories as $abstractFactory) {
            if ($abstractFactory->canProduce($this, $name)) {
                $instance = $abstractFactory->produce($this, $name);

                break;
            }
        }

        if (isset($instance)) {
            $this->resources[$name] =
            $instance = [$instance instanceof PrototypeInterface => $instance];
        }

        return $instance;
    }

    /**
     * Produce resource from definition
     *
     * @param string $name
     * @return object
     */
    protected function produceResourceFromDefinition(string $name) {
        $definition = $this->preprocessDefinition($this->definitions[$name]);

        // Non-factory object
        if (is_object($definition) && !$definition instanceof FactoryInterface && !$definition instanceof Closure) {
            return $definition;
        }

        if (is_callable($definition)) {
            return $definition($this, $name);
        }

        throw new Exception\ResourceFaultException("Invalid definition registered for name '$name'");
    }

    /**
     * Set definition
     *
     * @param string $name
     * @param mixed $definition
     * @return self
     */
    public function setDefinition(string $name, $definition) {
        $this->configure([static::DEFINITIONS_KEY => [$name => $definition]]);

        return $this;
    }

    /**
     * Set resource
     *
     * @param string $name
     * @param object $resource
     * @throws InvalidParameterException
     * @return self
     */
    public function setResource(string $name, $resource) {
        if (!is_object($resource)) {
            throw new InvalidParameterException('Resource must be an object.');
        }

        if (isset($this->resources[$name])) {
            throw new Exception\OverrideNotAllowedException("Resource already defined for '$name'");
        }

        $this->resources[$name][$resource instanceof PrototypeInterface] = $resource;

        return $this;
    }

    /**
     * Set abstract factories
     *
     * @param array $abstractFactories
     * @throws InvalidParameterException
     */
    protected function setupAbstractFactories(array $abstractFactories) {
        foreach ($abstractFactories as $abstractFactory) {
            $abstractFactory = $this->preprocessDefinition($abstractFactory);

            if ($abstractFactory instanceof AbstractFactoryInterface) {
                $this->abstractFactories[] = $abstractFactory;

                continue;
            }

            // Fault
            if (is_string($abstractFactory)) {
                throw new InvalidParameterException('Invalid abstract factory: ' . $abstractFactory);
            }
            elseif (is_array($abstractFactory)) {
                throw new InvalidParameterException('Invalid abstract factory array data');
            }

            throw new InvalidParameterException('Invalid type for abstract factory: ' . gettype($abstractFactory));
        }
    }
}