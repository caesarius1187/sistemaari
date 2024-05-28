<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\Time;

/**
 * Cajas Controller
 *
 * @property \App\Model\Table\CajasTable $Cajas
 *
 * @method \App\Model\Entity\Caja[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CajasController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $micaja = [];
        $cajaAbierta = false;
        $conditions=[];
        if(!empty($this->viewVars['cajasabiertas'])){
            foreach ($this->viewVars['cajasabiertas'] as $kc => $caja) {
                $micaja = $caja;
                $cajaAbierta = true;
                $conditions = [
                    'Cajas.puntodeventa_id'=>$micaja['puntodeventa']['numero']
                ];
            }
        }
        $session = $this->request->getSession(); // less than 3.5
        $empresaId = $session->read('Auth.User')['empresa_id'];
        $cajas = $this->Cajas->find('all', [
                'contain'=>['Puntodeventas','Users'],
                'conditions' => [ 'Cajas.empresa_id' => $empresaId]
            ]);
        $this->set(compact('cajas'));
        $session = $this->request->getSession(); // less than 3.5
        // $session = $this->request->getSession(); // 3.5 or more
        $AuthUserId = $session->read('Auth.User')['id'];
        $AuthUserNombre = $session->read('Auth.User')['first_name']." ".$session->read('Auth.User')['last_name'];
        $this->set(compact('AuthUserId','AuthUserNombre','micaja'));

    }

    /**
     * View method
     *
     * @param string|null $id Caja id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        //TODO: Controlar que no vea cajas de otros usuarios
        $caja = $this->Cajas->get($id, [
            'contain' => ['Users', 'Puntodeventas']
        ]);

        $this->set('caja', $caja);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $caja = $this->Cajas->newEntity();
        $session = $this->request->getSession(); // less than 3.5
        $empresaId = $session->read('Auth.User')['empresa_id'];

        if ($this->request->is('post')) {
            $cajasabiertas = $this->Cajas->find('all', [
                'contain'=>['Puntodeventas'],
                'conditions' => [
                    'DATE(Cajas.apertura) <= NOW()',
                    "Cajas.cierre IS NULL",
                    "Cajas.empresa_id"=>$empresaId
                ]
            ]);
            foreach ($cajasabiertas as $kca => $cajaabierta) {
                if($cajaabierta['puntodeventa_id']== $this->request->getData()['puntodeventa_id']){
                    //significa que esta caja ya esta abierta por otro usuario
                    $this->Flash->error(__('Esta caja ya esta abierta por otro usuario, por favor intente de nuevo mas tarde.'));
                    return $this->redirect(['action' => 'add']);
                }
            }
            $caja = $this->Cajas->patchEntity($caja, $this->request->getData());            
            $now = Time::parse('now');
            $caja['apertura'] =  $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $caja['empresa_id'] =  $empresaId;
            if ($this->Cajas->save($caja)) {
                $this->Flash->success(__('Se ha abierto la caja con exito.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo abrir la caja, por favor intente de nuevo mas tarde.'));
        }
        $users = $this->Cajas->Users->find('list', [
            'limit' => 200,
            'keyField' => 'id',
            'valueField' => 'first_name'
        ]);
        $puntodeventas = $this->Cajas->Puntodeventas->find('list', ['conditions' => ['Puntodeventas.empresa_id' => $empresaId]]);
        $this->set(compact('caja', 'users', 'puntodeventas'));
        $AuthUserId = $session->read('Auth.User')['id'];
        $AuthUserNombre = $session->read('Auth.User')['first_name']." ".$session->read('Auth.User')['last_name'];

        $this->set(compact('AuthUserId','AuthUserNombre'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Caja id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function cerrar($id = null)
    {
        //TODO: Controlar que no vea cajas de otros usuarios
        $this->loadModel('Pagos');
        $this->loadModel('Extracciones');
        $session = $this->request->getSession(); // less than 3.5
        $AuthUserId = $session->read('Auth.User')['id'];
        $AuthUserNombre = $session->read('Auth.User')['first_name']." ".$session->read('Auth.User')['last_name'];

        $this->set(compact('AuthUserId','AuthUserNombre'));
        $caja = $this->Cajas->get($id, [
            'contain' => ['Users']
        ]);
        $empresaId = $session->read('Auth.User')['empresa_id'];
        if($caja['empresa_id'] != $empresaId ){
            $this->Flash->error(__('La Caja no existe.'));
            return $this->redirect(['action' => 'index']);
        }
        $cajaCerrada = false;
        if(!is_null($caja['cierre'])){
            $cajaCerrada = true;
        }
        if ($this->request->is(['patch', 'post', 'put'])) {
            $caja = $this->Cajas->patchEntity($caja, $this->request->getData());     
            $caja['apertura'] = Time::parse($caja['apertura'])->i18nFormat('yyyy-MM-dd HH:mm:ss');       
            $caja['cierre'] = Time::now()->i18nFormat('yyyy-MM-dd HH:mm:ss');       
            if ($this->Cajas->save($caja)) {
                $this->Flash->success(__('La Caja ha sido cerrada.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo cerrar la caja por favor intente de nuevo mas tarde.'));
        }
        $this->set(compact('caja'));
        //treamos Pagos

        $conditions = [
            'contain' => ['Clientes'],
            'conditions'=>[
                'Pagos.puntodeventa_id'=>$caja['puntodeventa_id'],
                'Pagos.created >= '=>$caja['apertura']                            
            ],
            'order'=>['Pagos.created asc']
        ];
        if($cajaCerrada){
            $conditions['conditions']['Pagos.created <= ']= $caja['cierre'];
        }
        $pagos = $this->Pagos->find('all',$conditions);

        $this->set(compact('pagos'));
        //Extracciones
        $conditions = [
            'contain' => [],
            'conditions'=>[
                'Extracciones.puntodeventa_id'=>$AuthUserId,
                'Extracciones.created >= '=>$caja['apertura']                            
            ],
            'order'=>['Extracciones.created asc']
        ];
        if($cajaCerrada){
            $conditions['conditions']['Extracciones.created <= ']= $caja['cierre'];
        }
        $extracciones = $this->Extracciones->find('all',$conditions);

        $this->set(compact('extracciones'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Caja id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $caja = $this->Cajas->get($id);
        $session = $this->request->getSession(); // less than 3.5
        $empresaId = $session->read('Auth.User')['empresa_id'];
        if($caja['empresa_id'] != $empresaId ){
            $this->Flash->error(__('La Caja no existe.'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->Cajas->delete($caja)) {
            $this->Flash->success(__('The caja has been deleted.'));
        } else {
            $this->Flash->error(__('The caja could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
