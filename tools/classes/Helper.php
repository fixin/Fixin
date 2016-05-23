<?php
/**
 * Fixin Framework
 *
 * Class, interface, and trait lister
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */
namespace Classes;

class Helper {

    /**
     * @var array
     */
    public $namespaces = [];

    /**
     * @var array
     */
    protected $shortNameResolve = [];

    /**
     * @param string $topDir
     */
    public function __construct(string $topDir) {
        // Include all PHP files under classes/
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator("$topDir/classes"));
        foreach ($iterator as $item) {
            if ($item->isFile() && strtolower($item->getExtension()) === 'php') {
                include_once $item;
            }
        }

        $this->processElements();
    }

    /**
     * @param string $name
     * @return string
     */
    public function classShortName(string $name): string {
        return substr(strrchr($name, '\\'), 1);
    }

    /**
     * @param string $name
     * @return string
     */
    public function classLink(string $name): string {
        $name = ltrim($name, '\\');

        $name = $this->shortNameResolve[$name] ?? $name;

        return strncmp($name, 'Fixin\\', 6)
        ? htmlspecialchars($name)
        : '<a href="#' . htmlspecialchars($name) . '">' . htmlspecialchars($this->classShortName($name)) . '</a>';
    }

    /**
     * @param mixed $reflection
     * @return string[]
     */
    public function commentParameters($reflection) {
        $parameters = [];

        preg_match_all('/^\s*\*\s*@param\s+([^\s]+)\s+\$([^\s]+)$/m', $reflection->getDocComment(), $matches);

        foreach ($matches[1] as $index => $type) {
            $parameters[$matches[2][$index]] = '<span class="FromComment">' . implode('|', array_map([$this, 'classLink'], (explode('|', $type)))) . '</span>';
        }

        return $parameters;
    }

    /**
     * @param mixed $reflection
     * @return string
     */
    public function commentReturnType($reflection) {
        return $this->commentTypeFetch($reflection, 'return');
    }

    /**
     * @param mixed $reflection
     * @return string
     */
    public function commentText($reflection) {
        if (preg_match_all('/^\s*\*\s*([^@\s*].+)$/m', $reflection->getDocComment(), $matches)) {
            if ($matches[1][0] === '{@inheritDoc}' && ($parent = $reflection->getPrototype())) {
                return '<span class="Inherited">' . $this->commentText($parent) . '</span>';
            }
        }

        return nl2br(htmlspecialchars(implode("\n", $matches[1])));
    }

    /**
     * @param mixed $reflection
     * @param string $name
     * @return string
     */
    protected function commentTypeFetch($reflection, string $name): string {
        if (preg_match_all('(@' . $name . '\s+([^\s]+))', $reflection->getDocComment(), $matches)) {
            return '<span class="FromComment">' . implode('|', array_map([$this, 'classLink'], (explode('|', $matches[1][0])))) . '</span>';
        }

        return '';
    }

    /**
     * @param mixed $reflection
     * @return string
     */
    public function commentVar($reflection) {
        return $this->commentTypeFetch($reflection, 'var');
    }

    /**
     * @return string
     */
    public function evenStyle() {
        static $rowEvenState = 0;

        $rowEvenState = 1 - $rowEvenState;
        return $rowEvenState === 1 ? 'Even' : 'Odd';
    }

    /**
     * @param array $reflections
     * @return array
     */
    public function orderedReflectionList(array $reflections): array {
        $list = [];

        foreach ($reflections as $reflection) {
            $list[$reflection->getName()] = $reflection;
        }

        ksort($list);

        return $list;
    }

    /**
     * Process declared elements
     */
    protected function processElements() {
        foreach (array_merge(get_declared_classes(), get_declared_interfaces(), get_declared_traits()) as $name) {
            $reflection = new \ReflectionClass($name);
            if ($reflection->isInternal() || $name === 'Classes\Helper') {
                continue;
            }

            $namespace = substr($name, 0, strrpos($name, '\\'));
            $shortName = $this->classShortName($name);

            $this->namespaces[$namespace][$shortName] = $reflection;
            $this->shortNameResolve[$shortName] = $name;
        }

        ksort($this->namespaces);
    }

    /**
     * @param mixed $reflection
     * @return string
     */
    public function reflectionLink($reflection): string {
        $name = $reflection->getName();

        return strncmp($name, 'Fixin\\', 6)
        ? '\\' . htmlspecialchars($name)
        : '<a href="#' . htmlspecialchars($name) . '">' . htmlspecialchars($reflection->getShortName()) . '</a>';
    }
}