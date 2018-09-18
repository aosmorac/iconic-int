<?php

namespace AppBundle\Entity;

use AppBundle\Service\CacheService;
use AppBundle\Service\DatabaseService;

class Collection
{

    protected $database;
    protected $cache;
    protected $customers;
    protected $updated;
    protected $changesLog;
    protected $connectionError;


    /**
     * Collection constructor.
     * @param DatabaseService $database
     * @param CacheService $cache
     */
    public function __construct(DatabaseService $database, CacheService $cache)
    {
        $this->database = $database->getDatabase();
        $this->cache = $cache;
    }


    /**
     * Return the customers - db, cache or error db
     *
     * @return array
     */
    public function getCustomers() {
        if (empty($this->customers)) {
            $this->loadCustomers();
        }
        if ($this->connectionError != null) {
            return array("error"=>$this->connectionError, "customers"=>$this->getCustomersCache());
        }
        $this->getChangesLog();
        return $this->customers;
    }


    /**
     *
     * Save a customer or a customers set in db
     *
     * @param $customers
     * @param null $origin
     * @return bool
     */
    public function addCustomers( $customers, $origin = null ) {

        if (empty($customers)) {
            return false;
        }

        foreach ($customers as $customer) {
            $this->database->customers->insertOne($customer);
        }

        $this->database->log->insertOne((object)["action" => 'add', "origin" => $origin, "time" => date("YmdHis")]);

        return true;
    }


    /**
     *
     * Delete all customers
     *
     * @param null $origin
     * @return bool
     */
    public function deleteCustomers($origin = null) {
        $this->database->customers->drop();
        $this->cache->del('customers');
        $this->customers = null;
        $this->database->log->insertOne((object)["action" => 'delete', "origin" => $origin, "time" => date("YmdHis")]);
        return true;
    }



    /**
     *
     * Load customers, if cache is empty load info from db, if there are a change in db load from db, in other cases load cache
     *
     */
    public function loadCustomers() {
        if (!$this->getCustomersCache() || $this->areThereChanges()) {
            if (empty($this->customers) || $this->areThereChanges()) {
                try{
                    $customers = $this->database->customers->find();
                    $customers = iterator_to_array($customers);
                    $this->customers = $customers;
                    $this->setCustomersCache($customers);
                    $this->updateLog();
                    $this->connectionError = null;
                }
                catch(\Exception $e){
                    $this->customers = null;
                    $this->connectionError = $e->getMessage();
                }
            }
        } else {
            $this->customers = $this->getCustomersCache();
        }
    }


    /**
     * Set data in cache
     *
     * @param $customers
     */
    public function setCustomersCache( $customers ) {
        $this->cache->set('customers', $customers);
    }

    /**
     *
     * Get data from cache
     *
     * @return bool|mixed
     */
    public function getCustomersCache() {
        return $this->cache->get("customers");
    }


    /**
     * Get count of logs
     *
     * @return mixed
     */
    public function getChangesLog() {
        try{
            $log = $this->database->log->count();
            return $log;
        }
        catch(\Exception $e){
            return $this->changesLog;
        }
    }

    /**
     *
     * Set a new log in db
     *
     */
    public function updateLog() {
        if (empty($this->changesLog) || $this->changesLog != $this->getChangesLog() ) {
            $this->changesLog = $this->getChangesLog();
        }
    }


    /**
     *
     * Check if there are new changes in DB
     *
     * @return bool
     */
    public function areThereChanges() {
        if (empty($this->changesLog) || $this->changesLog != $this->getChangesLog() ) {
            return true;
        }
    }


}