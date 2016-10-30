<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Session;

use DateTime;
use Fixin\Base\Cookie\CookieManagerInterface;
use Fixin\Model\Repository\RepositoryInterface;
use Fixin\Resource\Prototype;
use Fixin\Support\Strings;

class SessionManager extends Prototype implements SessionManagerInterface {

    const
    DATA_REGENERATED = 'regenerated',
    THIS_REQUIRES = [
        self::OPTION_COOKIE_MANAGER => self::TYPE_INSTANCE,
        self::OPTION_COOKIE_NAME => self::TYPE_STRING,
        self::OPTION_REPOSITORY => self::TYPE_INSTANCE
    ],
    THIS_SETS_LAZY = [
        self::OPTION_COOKIE_MANAGER => CookieManagerInterface::class,
        self::OPTION_REPOSITORY => RepositoryInterface::class
    ];

    /**
     * @var SessionAreaInterface[]
     */
    protected $areas;

    /**
     * @var CookieManagerInterface|false|null
     */
    protected $cookieManager;

    /**
     * @var string
     */
    protected $cookieName = 'session';

    /**
     * @var SessionEntity
     */
    protected $entity;

    /**
     * @var integer
     */
    protected $lifetime = 0;

    /**
     * @var bool
     */
    protected $modified = false;

    /**
     * @var integer
     */
    protected $regenerationForwardTime = 1;

    /**
     * @var RepositoryInterface|false|null
     */
    protected $repository;

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @var bool
     */
    protected $started = false;

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Session\SessionManagerInterface::clear()
     */
    public function clear(): SessionManagerInterface {
        $this->start();

        $this->areas = [];
        $this->modified = true;

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Session\SessionManagerInterface::garbageCollection()
     */
    public function garbageCollection(int $lifetime): int {
        $request = $this->getRepository()->createRequest();
        $request->getWhere()->compare(SessionEntity::COLUMN_ACCESS_TIME, '<', new DateTime('+' . $lifetime . ' MINUTES'));

        return $request->delete();
    }

    /**
     * Generate session id
     *
     * @return string
     */
    protected function generateId(): string {
        return sha1(Strings::generateRandom(24) . uniqid('', true) . microtime(true));
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Session\SessionManagerInterface::getArea()
     */
    public function getArea(string $name): SessionAreaInterface {
        $this->start();

        // Existing area
        if (isset($this->areas[$name])) {
            return $this->areas[$name];
        }

        // New area
        $this->modified = true;

        return $this->areas[$name] = (new \Fixin\Base\Session\SessionArea());
    }

    /**
     * Get cookie manager instance
     *
     * @return CookieManagerInterface
     */
    protected function getCookieManager(): CookieManagerInterface {
        return $this->cookieManager ?: $this->loadLazyProperty(static::OPTION_COOKIE_MANAGER);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Session\SessionManagerInterface::getCookieName()
     */
    public function getCookieName(): string {
        return $this->cookieName;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Session\SessionManagerInterface::getLifetime()
     */
    public function getLifetime(): int {
        return $this->lifetime;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Session\SessionManagerInterface::getRegenerationForwardTime()
     */
    public function getRegenerationForwardTime(): int {
        return $this->regenerationForwardTime;
    }

    /**
     * Get repository instance
     *
     * @return RepositoryInterface
     */
    protected function getRepository(): RepositoryInterface {
        return $this->repository ?: $this->loadLazyProperty(static::OPTION_REPOSITORY);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Session\SessionManagerInterface::isModified()
     */
    public function isModified(): bool {
        $this->start();

        if ($this->modified) {
            return true;
        }

        foreach ($this->areas as $area) {
            if ($area->isModified()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Load entity
     *
     * @param SessionEntity $entity
     */
    protected function loadEntity(SessionEntity $entity) {
        $this->entity = $entity;
        $this->areas = $entity->getData();
        $this->sessionId = $entity->getSessionId();

        if ($this->lifetime) {
            $this->setupCookie();
        }

        $request = $this->getRepository()->createRequest();
        $request->getWhere()->compare(SessionEntity::COLUMN_SESSION_ID, '=', $this->sessionId);
        $request->update([SessionEntity::COLUMN_ACCESS_TIME => new DateTime()]);
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Session\SessionManagerInterface::regenerateId()
     */
    public function regenerateId(): SessionManagerInterface {
        $this->start();

        $this->sessionId = $this->generateId();
        $this->modified = true;

        if ($this->entity->isStored()) {
            $this->entity
            ->setData([static::DATA_REGENERATED => $this->sessionId])
            ->setAccessTime(new DateTime())
            ->save();

            $this->entity = $this->getRepository()->create();
        }

        $this->setupCookie();

        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Session\SessionManagerInterface::save()
     */
    public function save(): SessionManagerInterface {
        if ($this->started && $this->isModified()) {
            $this->entity
            ->setSessionId($this->sessionId)
            ->setData($this->areas)
            ->setAccessTime(new DateTime())
            ->save();

            $this->modified = false;

            foreach ($this->areas as $area) {
                $area->setModified(false);
            }
        }

        return $this;
    }

    /**
     * Set cookie name
     *
     * @param string $cookieName
     */
    protected function setCookieName(string $cookieName) {
        $this->cookieName = $cookieName;
    }

    /**
     * Set lifetime
     *
     * @param int $lifetime
     */
    protected function setLifetime(int $lifetime) {
        $this->lifetime = $lifetime;
    }

    /**
     * Set regeneration forward time
     *
     * @param int $regenerationForwardTime
     */
    protected function setRegenerationForwardTime(int $regenerationForwardTime) {
        $this->regenerationForwardTime = $regenerationForwardTime;
    }

    /**
     * Setup cookie
     */
    protected function setupCookie() {
        $this->getCookieManager()->set($this->cookieName, $this->sessionId)->setExpire($this->lifetime)->setPath('/');
    }

    /**
     * {@inheritDoc}
     * @see \Fixin\Base\Session\SessionManagerInterface::start()
     */
    public function start(): SessionManagerInterface {
        if (!$this->started) {
            $this->started = true;

            $sessionId = $this->getCookieManager()->getValue($this->cookieName);
            if ($sessionId && $this->startWith($sessionId)) {
                return $this;
            }

            // New session
            $this->areas = [];
            $this->entity = $this->getRepository()->create();
            $this->regenerateId();
        }

        return $this;
    }

    /**
     * Start with stored session id
     *
     * @param string $sessionId
     * @return bool
     */
    protected function startWith(string $sessionId): bool {
        $request = $this->getRepository()->createRequest();
        $where = $request->getWhere()->compare(SessionEntity::COLUMN_SESSION_ID, '=', $sessionId);

        if ($this->lifetime) {
            $where->compare(SessionEntity::COLUMN_ACCESS_TIME, '>=', new DateTime('+' . $this->lifetime . ' MINUTES'));
        }

        /** @var SessionEntity $entity */
        $entity = $request->fetchFirst();

        if (isset($entity)) {
            $data = $entity->getData();
            if (isset($data[static::DATA_REGENERATED])) {
                return ($entity->getAccessTime() >= new DateTime('-' . $this->regenerationForwardTime . ' MINUTES')) ? $this->startWith($data[static::DATA_REGENERATED]) : false;
            }

            $this->loadEntity($entity);

            return true;
        }

        return false;
    }
}