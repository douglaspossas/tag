<?php

namespace AppBundle\Entity;
use JsonSerializable;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Cow implements JsonSerializable
{
    public $avg_life = 20;
    public $custo_pasto_kg = 0.20;

    protected $pastoDia = 0;
    protected $pastoMes = 0;
    protected $pastoAno = 0;

    public function __construct($o = null){
        if ( !is_null($o) ){
            $this->id = $o->id;
            $this->weight = $o->weight;
            $this->price = $o->price;
            $this->age = $o->age;

            $this->pastoDia = ($this->weight / 100) * 3;
            $this->pastoMes = $this->getPastoDia() * 30;
            $this->pastoAno = $this->getPastoDia() * 365;
        }
    }
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $id;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $weight;

    public function expectativaVida(){
        return $this->avg_life - $this->age;
    }

    public function setWeight($v)
    {
        $this->weight = $v;
    }

    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $age;

    public function setAge($v)
    {
        $this->age = $v;
    }

    public function getAge()
    {
        return $this->age;
    }

    /**
     * @var decimal
     *
     * @ORM\Column(type="decimal", precision=12, scale=2)
     */
    protected $price;

    public function setPrice($v)
    {
        $this->price = $v;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getPastoDia()
    {
        return $this->pastoDia;
    }

    public function getPastoMes()
    {
        return $this->pastoMes;
    }

    public function getPastoAno()
    {
        return $this->pastoAno;
    }

    public function getAnnualCost()
    {
        //(VALOR_PAGO / (20 - IDADE DA VACA)) + ( (PESO / 100 * 3 * DIAS * 0,20 ) * (20 - IDADE DA VACA) )
        //{{ ((cow.weight/100) * 3 * 365) | number_format(2, ',', '.') }} KG / R$ {{ ( (cow.price / (20 - cow.age)) + ( ( cow.weight / 100 * 3 * 365 * 0.20) * ( 20 - cow.age ) ) ) | number_format(2, ',', '.')}}
       
        return $this->getPastoAno() * $this->custo_pasto_kg;
    }

    public function custoMedioAnual()
    {
        return (($this->price / (20 - $this->age)) + ($this->getAnnualCost()));
    }

    public function jsonSerialize()
    {
        return array(
            'id' => $this->id,
            'weight' => $this->weight,
            'age' => $this->age,
            'price'=> $this->price,
            'annual_cost' => $this->getAnnualCost(),
            'pasto_ano' => $this->getPastoAno(),
            'custo_medio' => $this->custoMedioAnual()
        );
    }
}