<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Puntodeventas Controller
 *
 * @property \App\Model\Table\PuntodeventasTable $Puntodeventas
 *
 * @method \App\Model\Entity\Puntodeventa[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PuntodeventasController extends AppController
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
        $conditions = [
            'Puntodeventas.empresa_id' => $empresaId
        ];
        $puntodeventas = $this->Puntodeventas->find('all',$conditions);
        $this->set(compact('puntodeventas'));
    }

    /**
     * View method
     *
     * @param string|null $id Puntodeventa id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $session = $this->request->getSession(); // less than 3.5
        $empresaId = $session->read('Auth.User')['empresa_id'];
        $puntodeventa = $this->Puntodeventas->get($id, [
            'contain' => ['Ventas'],
            'conditions' => ['Puntodeventas.empresa_id' => $empresaId]
        ]);
        if($puntodeventa['empresa_id'] != $empresaId ){
            $this->Flash->error(__('El Punto de venta no existe.'));
            return $this->redirect(['action' => 'index']);
        }

        $this->set('puntodeventa', $puntodeventa);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $puntodeventa = $this->Puntodeventas->newEntity();
        if ($this->request->is('post')) {
            $puntodeventa = $this->Puntodeventas->patchEntity($puntodeventa, $this->request->getData());
            $session = $this->request->getSession(); // less than 3.5
            $empresaId = $session->read('Auth.User')['empresa_id'];
            $puntodeventa->empresa_id = $empresaId;
            if ($this->Puntodeventas->save($puntodeventa)) {
                $this->Flash->success(__('The puntodeventa has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The puntodeventa could not be saved. Please, try again.'));
        }
        $this->set(compact('puntodeventa'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Puntodeventa id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $puntodeventa = $this->Puntodeventas->get($id, [
            'contain' => []
        ]);
        $session = $this->request->getSession(); // less than 3.5
        $empresaId = $session->read('Auth.User')['empresa_id'];
        if($puntodeventa['empresa_id'] != $empresaId ){
            $this->Flash->error(__('El Punto de venta no existe.'));
            return $this->redirect(['action' => 'index']);
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $puntodeventa = $this->Puntodeventas->patchEntity($puntodeventa, $this->request->getData());
            if ($this->Puntodeventas->save($puntodeventa)) {
                $this->Flash->success(__('The puntodeventa has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The puntodeventa could not be saved. Please, try again.'));
        }
        $this->set(compact('puntodeventa'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Puntodeventa id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $puntodeventa = $this->Puntodeventas->get($id);
        $session = $this->request->getSession(); // less than 3.5
        $empresaId = $session->read('Auth.User')['empresa_id'];
        if($puntodeventa['empresa_id'] != $empresaId ){
            $this->Flash->error(__('El Punto de venta no existe.'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->Puntodeventas->delete($puntodeventa)) {
            $this->Flash->success(__('The puntodeventa has been deleted.'));
        } else {
            $this->Flash->error(__('The puntodeventa could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
