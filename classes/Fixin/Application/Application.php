<?php

namespace Fixin\Application;

use Fixin\Config\Config;
use Fixin\ResourceManager\ResourceManager;

class Application implements ApplicationInterface {

    /**
     * @var \Fixin\ResourceManager\ResourceManagerInterface
     */
    protected $resourceManager;

    /**
     * @param array $config
     */
    public function __construct(array $config) {
        // Resource Manager config
        $rmConfig = $config['resourceManager'];
        unset($config['resourceManager']);

        $rmClass = $rmConfig['class'] ?? '\Fixin\ResourceManager\ResourceManager';
        unset($rmConfig['class']);

        // Resoure Manager init
        $this->resourceManager =
        $rm = new $rmClass($rmConfig);
        $rm->set(ApplicationInterface::CONFIG_KEY, new Config($config));
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Application\ApplicationInterface::run()
     */
    public function run() {
    }
}