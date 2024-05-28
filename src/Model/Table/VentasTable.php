<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use App\Controller\AppController;
use Cake\Core\App;
use Cake\Event\Event;
use Cake\Http\Response;
use Afip;
//use Afipsdk\Afip;
/**
 * Ventas Model
 *
 * @property \App\Model\Table\ClientesTable|\Cake\ORM\Association\BelongsTo $Clientes
 * @property \App\Model\Table\PuntodeventasTable|\Cake\ORM\Association\BelongsTo $Puntodeventas
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\Venta get($primaryKey, $options = [])
 * @method \App\Model\Entity\Venta newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Venta[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Venta|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Venta|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Venta patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Venta[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Venta findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class VentasTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->addBehavior('Timestamp');

        $this->setTable('ventas');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Clientes', [
            'foreignKey' => 'cliente_id'
        ]);
        $this->belongsTo('Puntodeventas', [
            'foreignKey' => 'puntodeventa_id'
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id'
        ]);
        $this->belongsTo('Empresas', [
            'foreignKey' => 'empresa_id'
        ]);
        $this->hasMany('Detalleventas', [
            'foreignKey' => 'venta_id'
        ]);
        $this->hasMany('Pagos', [
            'foreignKey' => 'venta_id'
        ]);
        $this->hasMany('Tributos', [
            'foreignKey' => 'venta_id'
        ]);
        $this->hasMany('Alicuotas', [
            'foreignKey' => 'venta_id'
        ]);
        $this->belongsTo('Comprobantes', [
            'foreignKey' => 'comprobante_id'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        /*$validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->boolean('presupuesto')
            ->allowEmpty('presupuesto');

        $validator
            ->dateTime('fecha')
            ->allowEmpty('fecha');

        $validator
            ->numeric('neto')
            ->allowEmpty('neto');

        $validator
            ->numeric('porcentajedescuento')
            ->allowEmpty('porcentajedescuento');

        $validator
            ->numeric('importedescuento')
            ->allowEmpty('importedescuento');

        $validator
            ->numeric('iva')
            ->allowEmpty('iva');

        $validator
            ->numeric('total')
            ->allowEmpty('total');*/

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['cliente_id'], 'Clientes'));
        $rules->add($rules->existsIn(['puntodeventa_id'], 'Puntodeventas'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }
    public function beforeSave($event, $entity, $options) {
        //debug($entity);
        $entity->fecha = date('Y-m-d H:i:s', strtotime($entity->fecha));
    }
    /*PHP FOR AFIP CONECTIONS*/
    
    public function afipConect($isProduction){
        //App::import('Vendor', 'Afip/Afip');
        require_once(ROOT. DS  . 'vendor' . DS  . 'Afip' . DS . 'Afip.php');
        //Conecciones de la barrica
        if($isProduction){
            $afip = new Afip([
                'CUIT' => '30717926966', //Distribuidora King Pack
                //'CUIT' => '203865340336', //maxi
                'cert' => 'PrivadaMaxiProd_35f1b78096cb9fb4.crt',
                'key' => 'privadaMaxiProd',
                'passphrase'=>'privadaMaxiProd',
                'production'=>true
            ]);   
        }else{
            //20386530336
            //30717926966
            $afip = new Afip([
                'CUIT' => '203865340336',
                'cert' => 'certificadoMaxiTest.crt',
                'key' => 'privadaMaxi',
                'passphrase'=>'privadaMaxi',
                'production'=>false
            ]);   
        }
        return $afip;
    }
    public function afipget($afipClass,$funcionAFIP=null,$puntoDeVenta=null,$tipoFactura=null,$numero=null){
        //$this->loadModel('Puntosdeventa');
        

        $tipos = [];
        switch ($funcionAFIP) {
            case 'GetLastVoucher':
                $tipos = $afipClass->ElectronicBilling->GetLastVoucher($puntoDeVenta,$tipoFactura);
                break;
            case 'GetVoucherTypes':
                $tipos = $afipClass->ElectronicBilling->GetVoucherTypes();
                break;
            case 'GetDocumentTypes':
                $tipos = $afipClass->ElectronicBilling->GetDocumentTypes();
                break;
            case 'GetCurrenciesTypes':
                $tipos = $afipClass->ElectronicBilling->GetCurrenciesTypes();
                break;
            case 'GetTaxTypes':
                $tipos = $afipClass->ElectronicBilling->GetTaxTypes();
                break;
            case 'GetAliquotTypes':
                $tipos = $afipClass->ElectronicBilling->GetAliquotTypes();
                break;
            case 'GetOptionsTypes':
                $tipos = $afipClass->ElectronicBilling->GetOptionsTypes();
                break;
            case 'GetVoucherInfo':
                $tipos = $afipClass->ElectronicBilling->GetVoucherInfo($numero, $puntoDeVenta, $tipoFactura);
                break;
            case 'GetPointOfSales':
                $tipos = $afipClass->ElectronicBilling->GetPointOfSales();
                break;    
            default:
                break;
        }
        $response['respuesta'] = [$tipos];
        return $response;
    }
    /*FIN AFÏP*/
}
