<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;
use Cake\Http\Response;

/**
 * Provedores Controller
 *
 * @property \App\Model\Table\ProvedoresTable $Provedores
 *
 * @method \App\Model\Entity\Provedore[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ProvedoresController extends AppController
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
        $provedores =$this->Provedores->find('all',['conditions'=>['Provedores.empresa_id'=>$empresaId]]);
        $this->set(compact('provedores'));
        
        $miprovedore = $this->Provedores->newEntity();
        $session = $this->request->getSession(); // less than 3.5
        $AuthUserRole = $session->read('Auth.User')['role'];
        $this->set(compact('miprovedore','AuthUserRole'));
    }

    /**
     * View method
     *
     * @param string|null $id Provedore id.
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
        $provedore = $this->Provedores->get($id, [
            'contain' => ['Pagos','Compras']
        ]);
        if($provedore->empresa_id != $empresaId){
            $this->Flash->error(__('Error al intentar abrir la vista. No existe provedore. Por favor intentelo de nuevo mas tarde.'));
            return $this->redirect(['action' => 'index']);
        }

        $this->set('provedore', $provedore);
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $provedore = $this->Provedores->newEntity();
        if ($this->request->is('post')) {
            $this->autoRender = false;
            $provedoresTable = TableRegistry::get('Provedores');
           // New entity with nested associations
            $entity = $provedoresTable->newEntity($this->request->getData());
            $respuesta = "";
            $session = $this->request->getSession(); // less than 3.5
            $empresaId = $session->read('Auth.User')['empresa_id'];
            $entity->empresa_id = $empresaId;
            if ($provedoresTable->save($entity)) {
                // The $article entity contains the id now
                $id = $entity->id;
                $respuesta .=  "El provedore ha sido guardado. ";
                $response = $this->response;
                $data=[
                    'result' => 'success',
                    'respuesta'=>$respuesta,
                    'provedore'=>$entity,
                ];
                $response = $response->withType('application/json')
                ->withStringBody(json_encode($data));
                echo json_encode($data);
                return $this->response;     
            }
            $respuesta = 'Error. El provedore NO ha sido guardado.';
            $response = $this->response;
            $data=['result' => 'fail',
                    'response'=>$respuesta,
                    'provedore'=>$entity];
            echo json_encode($data);
            $response = $response->withType('application/json')
                ->withStringBody(json_encode($data));
            return $this->response;        
        }
        $this->set(compact('provedore'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Provedore id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $provedore = $this->Provedores->get($id, [
            'contain' => []
        ]);
        $session = $this->request->getSession(); // less than 3.5
        $empresaId = $session->read('Auth.User')['empresa_id'];
        if($provedore->empresa_id != $empresaId){
            $this->Flash->error(__('Error al intentar abrir la vista. No existe provedore. Por favor intentelo de nuevo mas tarde.'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->request->is(['patch', 'post', 'put'])) {
            $provedore = $this->Provedores->patchEntity($provedore, $this->request->getData());
            if ($this->Provedores->save($provedore)) {
                $this->Flash->success(__('El provedore ha sido modificado.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('El provedore no pudo ser modificado. Por favor intente mas tarde.'));
        }
        $this->set(compact('provedore'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Provedore id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $provedore = $this->Provedores->get($id);
        $session = $this->request->getSession(); // less than 3.5
        $empresaId = $session->read('Auth.User')['empresa_id'];
        if($provedore->empresa_id != $empresaId){
            $this->Flash->error(__('Error al intentar eliminar. No existe provedore. Por favor intentelo de nuevo mas tarde.'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->Provedores->delete($provedore)) {
            $this->Flash->success(__('El provedore ha sido eliminado.'));
        } else {
            $this->Flash->error(__('El provedore no pudo ser elimindado. Por favor intente mas tarde.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
