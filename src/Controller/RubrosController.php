<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;
use Cake\Http\Response;
/**
 * Rubros Controller
 *
 * @property \App\Model\Table\RubrosTable $Rubros
 *
 * @method \App\Model\Entity\Rubro[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class RubrosController extends AppController
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
            'conditions'=>[
                'Rubros.empresa_id' => $empresaId
            ]
        ];
        $rubros = $this->Rubros->find('all',$conditions);
        $this->set(compact('rubros'));
    }

    /**
     * View method
     *
     * @param string|null $id Rubro id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
     public function resumen()
    {
        ini_set('memory_limit', '1024M');
        $fechaRubrosfin = date('d-m-Y');
        $fechaRubrosinicio = date('01-m-Y');
        if ($this->request->is('post')) {
                $fechaRubrosinicio = $this->request->getData()['fechainicio'];
                $fechaRubrosfin = $this->request->getData()['fechafin'];
        }
        $fechaconsultainicio = date('Y-m-d',strtotime($fechaRubrosinicio));
        $fechaconsultafin = date('Y-m-d',strtotime($fechaRubrosfin));
        
        $session = $this->request->getSession(); // less than 3.5
        $empresaId = $session->read('Auth.User')['empresa_id'];

        $conditions = [
            'contain' => [
                'Detalleventas'=>[
                    'Productos'=>[
                       'Rubros'
                    ]
                ]
            ],
            'conditions'=>[
                'Ventas.presupuesto' => 0,
                'Ventas.fecha >= "'.$fechaconsultainicio.'"',
                'Ventas.fecha <= "'.$fechaconsultafin.'"',
                'Ventas.empresa_id' => $empresaId,
            ]
        ];
        $ventasTable = TableRegistry::get('Ventas');
        $ventas = $ventasTable->find('all',$conditions)->toArray();

        $rubro = $this->Rubros->newEntity();
        $this->set(compact('fechaconsultainicio','fechaconsultafin','fechaRubrosinicio','fechaRubrosfin','rubro','ventas'));
    }
    public function view($id = null)
    {
        $rubro = $this->Rubros->get($id, [
            'contain' => ['Productos']
        ]);
        $session = $this->request->getSession(); // less than 3.5
        $empresaId = $session->read('Auth.User')['empresa_id'];
        if($rubro['empresa_id'] != $empresaId ){
            $this->Flash->error(__('El Rubro no existe.'));
            return $this->redirect(['action' => 'index']);
        }
        $this->set('rubro', $rubro);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $rubro = $this->Rubros->newEntity();
        $session = $this->request->getSession(); // less than 3.5
        $empresaId = $session->read('Auth.User')['empresa_id'];
        if ($this->request->is('post')) {
            $rubro = $this->Rubros->patchEntity($rubro, $this->request->getData());
            $rubro->empresa_id = $empresaId;
            if ($this->Rubros->save($rubro)) {
                $this->Flash->success(__('The rubro has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The rubro could not be saved. Please, try again.'));
        }
        $this->set(compact('rubro'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Rubro id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $rubro = $this->Rubros->get($id, [
            'contain' => []
        ]);
        $session = $this->request->getSession(); // less than 3.5
        $empresaId = $session->read('Auth.User')['empresa_id'];
        if($rubro['empresa_id'] != $empresaId ){
            $this->Flash->error(__('El Rubro no existe.'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->request->is(['patch', 'post', 'put'])) {
            $rubro = $this->Rubros->patchEntity($rubro, $this->request->getData());
            if ($this->Rubros->save($rubro)) {
                $this->Flash->success(__('The rubro has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The rubro could not be saved. Please, try again.'));
        }
        $this->set(compact('rubro'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Rubro id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $rubro = $this->Rubros->get($id);
        $session = $this->request->getSession(); // less than 3.5
        $empresaId = $session->read('Auth.User')['empresa_id'];
        if($rubro['empresa_id'] != $empresaId ){
            $this->Flash->error(__('El Rubro no existe.'));
            return $this->redirect(['action' => 'index']);
        }
        $this->loadModel('Productos');
        $productosTable = TableRegistry::get('Productos');
        $exists = $productosTable->exists([
            'OR'=>[
                'rubro_id' => $id
            ]
        ]);
        if($exists){
            $this->Flash->error(__('El rubro no se puede eliminar por que tiene productos ligados.'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->Rubros->delete($rubro)) {
            $this->Flash->success(__('El Rubro fue eliminado.'));
        } else {
            $this->Flash->error(__('El Rubro no se pudo eliminar. Por favor, intente de nuevo mas tarde.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
