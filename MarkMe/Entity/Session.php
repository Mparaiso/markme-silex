<?php

/**
 * @copyrights 2014 mparaiso <mparaiso@online.fr>
 * @All rights reserved
 */

namespace MarkMe\Entity;

/**
 * @Entity
 * @Table(name="sessions")
 */
class Session {

    /**
     * @Column(name="sess_id",type="string",  unique=true, nullable=false)
     * @Id
     * @var integer
     */
    private $id;

    /**
     * @Column(name="sess_data",type="text", nullable=false)
     * @var string
     */
    private $data;

    /**
     * @Column(name="sess_time",type="string",  nullable=false)
     * @var string
     */
    private $time;

    function getId() {
        return $this->id;
    }

    function setId($id) {
        $this->id = $id;
    }

    function getData() {
        return $this->data;
    }

    function setData($data) {
        $this->data = $data;
    }

    function getTime() {
        return $this->time;
    }

    function setTime($time) {
        $this->time = $time;
    }

}
