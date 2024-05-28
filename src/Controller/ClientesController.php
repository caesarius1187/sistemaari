<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;
use Cake\Http\Response;

/**
 * Clientes Controller
 *
 * @property \App\Model\Table\ClientesTable $Clientes
 *
 * @method \App\Model\Entity\Cliente[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ClientesController extends AppController
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
        $clientes =$this->Clientes->find('all',['conditions'=>['Clientes.empresa_id'=>$empresaId]]);

        $this->set(compact('clientes'));
        
        $micliente = $this->Clientes->newEntity();
        $session = $this->request->getSession(); // less than 3.5
        $AuthUserRole = $session->read('Auth.User')['role'];
        $this->set(compact('micliente','AuthUserRole'));
    }

    /**
     * View method
     *
     * @param string|null $id Cliente id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        if($this->viewVars['userfiscal']){
            $this->Flash->error(__('Error al intentar abrir la vista. Por favor intentelo de nuevo mas tarde.'));
            return $this->redirect(['action' => 'index']);
        }
        $session = $this->request->getSession(); // less than 3.5
        $empresaId = $session->read('Auth.User')['empresa_id'];
        $cliente = $this->Clientes->get($id, [
            'contain' => ['Ventas','Pagos']
        ]);
        if($cliente->empresa_id != $empresaId){
            $this->Flash->error(__('Error al intentar abrir la vista. No existe cliente. Por favor intentelo de nuevo mas tarde.'));
            return $this->redirect(['action' => 'index']);
        }

        $this->set('cliente', $cliente);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $cliente = $this->Clientes->newEntity();
        if ($this->request->is('post')) {
            $this->autoRender = false;
            $clientesTable = TableRegistry::get('Clientes');
           // New entity with nested associations
            $entity = $clientesTable->newEntity($this->request->getData());
            $respuesta = "";
            $session = $this->request->getSession(); // less than 3.5
            $empresaId = $session->read('Auth.User')['empresa_id'];
            $entity->empresa_id = $empresaId;
            if ($clientesTable->save($entity)) {
                // The $article entity contains the id now
                $id = $entity->id;
                $respuesta .=  "El cliente ha sido guardado. ";
                $response = $this->response;
                $data=[
                    'result' => 'success',
                    'respuesta'=>$respuesta,
                    'cliente'=>$entity,
                ];
                $response = $response->withType('application/json')
                ->withStringBody(json_encode($data));
                echo json_encode($data);
                return $this->response;     
            }
            $respuesta = 'Error. El cliente NO ha sido guardado.';
            $response = $this->response;
            $data=['result' => 'fail',
                    'response'=>$respuesta,
                    'cliente'=>$entity];
            echo json_encode($data);
            $response = $response->withType('application/json')
                ->withStringBody(json_encode($data));
            return $this->response;        
        }
        $this->set(compact('cliente'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Cliente id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $cliente = $this->Clientes->get($id, [
            'contain' => []
        ]);
        $session = $this->request->getSession(); // less than 3.5
        $empresaId = $session->read('Auth.User')['empresa_id'];
        if($cliente->empresa_id != $empresaId){
            $this->Flash->error(__('Error al intentar abrir la vista. No existe cliente. Por favor intentelo de nuevo mas tarde.'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->request->is(['patch', 'post', 'put'])) {
            $cliente = $this->Clientes->patchEntity($cliente, $this->request->getData());
            if ($this->Clientes->save($cliente)) {
                $this->Flash->success(__('El cliente ha sido modificado.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('El cliente no pudo ser modificado. Por favor intente mas tarde.'));
        }
        $this->set(compact('cliente'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Cliente id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $cliente = $this->Clientes->get($id);
        $session = $this->request->getSession(); // less than 3.5
        $empresaId = $session->read('Auth.User')['empresa_id'];
        if($cliente->empresa_id != $empresaId){
            $this->Flash->error(__('Error al intentar eliminar. No existe cliente. Por favor intentelo de nuevo mas tarde.'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->Clientes->delete($cliente)) {
            $this->Flash->success(__('El cliente ha sido eliminado.'));
        } else {
            $this->Flash->error(__('El cliente no pudo ser elimindado. Por favor intente mas tarde.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
