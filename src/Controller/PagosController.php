<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\Time;

/**
 * Pagos Controller
 *
 * @property \App\Model\Table\PagosTable $Pagos
 *
 * @method \App\Model\Entity\Pago[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PagosController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $session = $this->request->getSession(); // less than 3.5
        $empresaId = $session->read('Auth.User')['empresa_id'];
        $clientes = $this->Pagos->Clientes->find('list', ['conditions'=>['Clientes.empresa_id'=>$empresaId]]);
        $provedores = $this->Pagos->Provedores->find('list', ['conditions'=>['Provedores.empresa_id'=>$empresaId]]);
        $micaja = [];
        $cajaAbierta = false;
        if(!empty($this->viewVars['cajasabiertas'])){
            foreach ($this->viewVars['cajasabiertas'] as $kc => $caja) {
                $micaja = $caja;
                $cajaAbierta = true;
            }
        }
        if(!$cajaAbierta){
            $this->Flash->error(__('Error. Para agregar una venta primero debe abrir una caja en un punto de venta.'));
            return $this->redirect(['action' => 'index']);
        }
        $this->set('micaja',$micaja);
        $this->set('clientes',$clientes);
        $this->set('provedores',$provedores);

        $conditions = [
            'contain' => ['Clientes','Provedores'],
            'conditions'=>[
                'Pagos.puntodeventa_id'=>$micaja['puntodeventa_id'],
                'Pagos.created >= '=>$micaja['apertura'],
                'Pagos.empresa_id' => $empresaId                            
            ],
            'order'=>['Pagos.created asc']
        ];
        $pagos = $this->Pagos->find('all',$conditions);

        $this->set(compact('pagos'));

        $AuthUserId = $session->read('Auth.User')['id'];
        $AuthUserNombre = $session->read('Auth.User')['first_name']." ".$session->read('Auth.User')['last_name'];

        $this->set(compact('AuthUserId','AuthUserNombre','ultimaventa','ultimopago'));
        $mipago = $this->Pagos->newEntity();
        $this->set('mipago', $mipago);
        $otropago = $this->Pagos->newEntity();
        $this->set('otropago', $otropago);
    }

    /**
     * View method
     *
     * @param string|null $id Pago id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $session = $this->request->getSession(); // less than 3.5
        $empresaId = $session->read('Auth.User')['empresa_id'];
        $pago = $this->Pagos->get($id, [
            'contain' => ['Clientes','Provedores','Users']
        ]);
        if($pago['empresa_id'] != $empresaId ){
            $this->Flash->error(__('El Pago no existe.'));
            return $this->redirect(['action' => 'index']);
        }
        $this->set('pago', $pago);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $pago = $this->Pagos->newEntity();

        if ($this->request->is('post')) {
            $micaja = [];
            $cajaAbierta = false;
            if(!empty($this->viewVars['cajasabiertas'])){
                foreach ($this->viewVars['cajasabiertas'] as $kc => $caja) {
                    $micaja = $caja;
                    $cajaAbierta = true;
                }
            }
            if(!$cajaAbierta){
                $this->Flash->error(__('Error. Para agregar un Pago primero debe abrir una caja en un punto de venta.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->set('micaja',$micaja);
            $miPago = $this->request->getData();
            $session = $this->request->getSession(); // less than 3.5
            $empresaId = $session->read('Auth.User')['empresa_id'];
           
            $toppago = $this->Pagos->find('all',[
                 'fields' => ['ultimopago' => 'MAX(Pagos.numero)'],
                 'conditions'=>[
                      'Pagos.puntodeventa_id'=> $micaja['puntodeventa']['numero'],
                      'Pagos.empresa_id' => $empresaId
                 ]
            ]); 
            $ultimopago = iterator_to_array($toppago);
            $miPago['numero'] = $ultimopago[0]['ultimopago']*1+1;
            $pago = $this->Pagos->patchEntity($pago, $miPago);
            $now = Time::parse($pago['fecha']);
            $pago['fecha'] =  $now->i18nFormat('yyyy-MM-dd HH:mm:ss');
            $pago['empresa_id'] = $empresaId;
            if ($this->Pagos->save($pago)) {
                $this->Flash->success(__('El pago ha sido Guardado.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('No se pudo registrar el pago , por favor intente de nuevo mas tarde'));
        }
        $clientes = $this->Pagos->Clientes->find('list', ['limit' => 200]);
        $puntosdeventas = $this->Pagos->Puntodeventas->find('list', ['limit' => 200]);
        $this->set(compact('pago', 'clientes','puntosdeventas'));
        $session = $this->request->getSession(); // less than 3.5
        // $session = $this->request->getSession(); // 3.5 or more
        $AuthUserId = $session->read('Auth.User')['id'];
        $AuthUserNombre = $session->read('Auth.User')['first_name']." ".$session->read('Auth.User')['last_name'];
        $this->set(compact('AuthUserId','AuthUserNombre'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Pago id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $pago = $this->Pagos->get($id, [
            'contain' => []
        ]);
        $session = $this->request->getSession(); // less than 3.5
        $empresaId = $session->read('Auth.User')['empresa_id'];
        if($pago['empresa_id'] != $empresaId ){
            $this->Flash->error(__('El Pago no existe.'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->request->is(['patch', 'post', 'put'])) {
            $pago = $this->Pagos->patchEntity($pago, $this->request->getData());
            if ($this->Pagos->save($pago)) {
                $this->Flash->success(__('The pago has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The pago could not be saved. Please, try again.'));
        }
        $clientes = $this->Pagos->Clientes->find('list', ['limit' => 200]);
        $this->set(compact('pago', 'clientes'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Pago id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $pago = $this->Pagos->get($id);
        $session = $this->request->getSession(); // less than 3.5
        $empresaId = $session->read('Auth.User')['empresa_id'];
        if($pago['empresa_id'] != $empresaId ){
            $this->Flash->error(__('El Pago no existe.'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->Pagos->delete($pago)) {
            $this->Flash->success(__('The pago has been deleted.'));
        } else {
            $this->Flash->error(__('The pago could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
