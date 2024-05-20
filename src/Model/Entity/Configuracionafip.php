<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Configuracionafip Entity
 *
 * @property int $id
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Puntodeventa $puntodeventa
 */
class Configuracionafip extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'cuitemisor' => true,
        'password' => true,
        'apertura' => true,
        'alias' => true,
        'key' => true,
        'cert' => true,
        'user' => true,
        'puntodeventa' => true,
        'created' => true,
        'modify' => true,
    ];
}
