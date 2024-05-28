<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;
use Cake\Http\Response;
/**
 * Empresas Controller
 *
 * @property \App\Model\Table\EmpresasTable $Empresas
 *
 * @method \App\Model\Entity\Empresa[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class EmpresasController extends AppController
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
            'conditions'=>['Empresas.id'=>$empresaId]
        ];
        $empresas = $this->Empresas->find('all',$conditions);
        $this->set(compact('empresas'));
    }

    /**
     * View method
     *
     * @param string|null $id Empresa id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    
    public function view($id = null)
    {
        $empresa = $this->Empresas->get($id, [
            'contain' => ['Productos']
        ]);

        $this->set('empresa', $empresa);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $empresa = $this->Empresas->newEntity();
        if ($this->request->is('post')) {
            $empresa = $this->Empresas->patchEntity($empresa, $this->request->getData());
            if ($this->Empresas->save($empresa)) {
                $this->Flash->success(__('The empresa has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The empresa could not be saved. Please, try again.'));
        }
        $this->set(compact('empresa'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Empresa id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $empresa = $this->Empresas->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $empresa = $this->Empresas->patchEntity($empresa, $this->request->getData());
            if ($this->Empresas->save($empresa)) {
                $this->Flash->success(__('The empresa has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The empresa could not be saved. Please, try again.'));
        }
        $this->set(compact('empresa'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Empresa id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $empresa = $this->Empresas->get($id);
        $this->loadModel('Productos');
        $productosTable = TableRegistry::get('Productos');
        $entity = $productosTable->newEntity($this->request->getData());
            $respuesta = "";
            //no guardemos un codigo de producto repetido
            $exists = $productosTable->exists([
                'OR'=>[
                    'empresa_id' => $id
                ]
            ]);
            if($exists){
                $this->Flash->error(__('El empresa no se puede eliminar por que tiene productos ligados.'));
                return $this->redirect(['action' => 'index']);
            }
        if ($this->Empresas->delete($empresa)) {
            $this->Flash->success(__('The empresa has been deleted.'));
        } else {
            $this->Flash->error(__('The empresa could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
