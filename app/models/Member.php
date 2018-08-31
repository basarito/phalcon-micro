<?php

class Member extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    public $member_id;

    /**
     *
     * @var string
     * @Column(type="string", length=50, nullable=false)
     */
    public $first_name;

    /**
     *
     * @var string
     * @Column(type="string", length=50, nullable=false)
     */
    public $last_name;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $date_of_birth;

    /**
     *
     * @var string
     * @Column(type="string", length=4, nullable=false)
     */
    public $class;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $company;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("alumni");
        $this->setSource("member");
        $this->belongsTo('company', '\Company', 'company_id', ['alias' => 'Company']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'member';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Member[]|Member|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Member|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
