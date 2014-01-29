<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace SynergyCommon\Session\SaveHandler;


use SynergyCommon\Model\SessionModel;
use Zend\Session\SaveHandler\SaveHandlerInterface;

/**
 * DB Table Gateway session save handler
 */
class DoctrineSaveHandler
    implements SaveHandlerInterface
{
    /**
     * Session Save Path
     *
     * @var string
     */
    protected $sessionSavePath;

    /**
     * Session Name
     *
     * @var string
     */
    protected $sessionName;

    /**
     * Lifetime
     *
     * @var int
     */
    protected $lifetime;

    /**
     *
     * @var \SynergyCommon\Model\SessionModel
     */
    protected $model;


    /**
     * @param SessionModel $model
     * @param null         $lifetime
     */
    public function __construct(SessionModel $model, $lifetime = null)
    {
        $this->model    = $model;
        $this->lifetime = $lifetime;
    }

    /**
     * Open Session
     *
     * @param  string $savePath
     * @param  string $name
     *
     * @return bool
     */
    public function open($savePath, $name)
    {
        $this->sessionSavePath = $savePath;
        $this->sessionName     = $name;
        $this->lifetime        = $this->_getLIfeTime();

        return true;
    }

    /**
     * Close session
     *
     * @return bool
     */
    public function close()
    {
        return true;
    }

    /**
     * Read session data
     *
     * @param string $id
     *
     * @return string
     */
    public function read($id)
    {
        $data = '';

        /** @var $row \SynergyCommon\Member\Entity\Session */
        $row = $this->model->getRepository()->findOneBy(
            array(
                 'id'   => $id,
                 'name' => $this->sessionName
            )
        );

        if ($row) {
            if (($row->getModified() + $row->getLifetime()) > time()) {
                $data = $row->getData();
            } else {
                $this->destroy($id);
            }
        }

        return $data;
    }

    /**
     * Write session data
     *
     * @param string $id
     * @param string $data
     *
     * @return bool
     */
    public function write($id, $data)
    {
        $data = array(
            'modified' => time(),
            'data'     => (string)$data,
        );

        /** @var $row \SynergyCommon\Member\Entity\Session */
        $row = $this->model->getRepository()->findOneBy(
            array(
                 'id'   => $id,
                 'name' => $this->sessionName
            )
        );


        if (!$row) {
            $class = $this->model->getEntity();
            $row   = new $class();

            $data['lifetime'] = $this->lifetime;
            $data['id']       = $id;
            $data['name']     = $this->sessionName;
        }

        $row->exchangeArray($data);

        return $this->model->save($row) ? true : false;
    }

    /**
     * Destroy session
     *
     * @param  string $id
     *
     * @return bool
     */
    public function destroy($id)
    {
        return (bool)$this->model->remove($id);
    }

    /**
     * Garbage Collection
     *
     * @param int $maxlifetime
     *
     * @return bool
     */
    public function gc($maxlifetime)
    {
        return $this->model->collectGabage() ? true : false;
    }

    protected function _getLIfeTime()
    {
        if ($this->lifetime) {
            $this->lifetime = \ini_get('session.gc_maxlifetime');
        }

        return $this->lifetime;
    }
}
