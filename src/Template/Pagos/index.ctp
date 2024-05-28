<?php
use Cake\Routing\Router;
$this->Html->css([
    'AdminLTE./plugins/datatables/dataTables.bootstrap',
    'AdminLTE./plugins/select2/select2.min',
],
['block' => 'css']);

$this->Html->script([
    'AdminLTE./plugins/datatables/jquery.dataTables.min',
    'AdminLTE./plugins/datatables/dataTables.bootstrap.min',
    'AdminLTE./plugins/select2/select2.full.min',
],
['block' => 'script']);

$this->start('scriptBottom'); ?>
<script>
  $(function () {
    $("#tblMovimientos").DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        }
    });
  });
   $("#formAgregarPago #tipo").change(function(){
        if($(this).val()=='retiro'){
            $("#formAgregarPago #cliente-id").val('');
            $("#formAgregarPago #cliente-id").parent().hide();
        }else{
            $("#formAgregarPago #cliente-id").parent().show();
        }
    });
   $("#formAgregarCobro #tipo").change(function(){
        if($(this).val()=='deposito'){
            $("#formAgregarCobro #cliente-id").val('');
            $("#formAgregarCobro #cliente-id").parent().hide();
        }else{
            $("#formAgregarCobro #cliente-id").parent().show();
        }
    });
    $("#formAgregarPago #cliente-id").select2();
    $("#formAgregarCobro #cliente-id").select2();
</script>
<?php $this->end(); 

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Pago[]|\Cake\Collection\CollectionInterface $pagos
 */
?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Movimientos de Caja ::</h3> <?= __('Vendedor '.$AuthUserNombre) ?>
                    <h3 class="box-title">Punto de Venta ::</h3> <?php echo str_pad($micaja['puntodeventa']['numero'], 5, "0", STR_PAD_LEFT); ?></br>
                    <a class="btn btn-app" data-toggle="modal" data-target="#modal-success" style="margin: 0 0 -25px 0px;">
                                    <i class="fa fa-usd"></i> Nuevo Ingreso.
                                </a>
                    <a class="btn btn-app" data-toggle="modal" data-target="#modal-warning" style="margin: 0 0 -25px 0px;">
                                    <i class="fa fa-usd"></i> Nuevo Egreso.
                                </a>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="pagos index large-9 medium-8 columns content">
                        <table cellpadding="0" cellspacing="0" id="tblMovimientos">
                            <thead>
                                <tr>
                                    <th scope="col">Tipo</th>
                                    <th scope="col">Metodo</th>
                                    <th scope="col">Fecha</th>
                                    <th scope="col">Cliente / Provedor</th>
                                    <th scope="col">Importe</th>
                                    <th scope="col">Descripcion</th>
                                    <th scope="col" class="actions"><?= __('Acciones') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pagos as $pago): ?>
                                <tr>
                                    <td><?= h($pago->tipo) ?></td>
                                    <td><?= h($pago->metodo) ?></td>
                                    <td><?= date('d-m-Y', strtotime($pago->fecha)) ?></td>
                                    <td><?= $pago->has('cliente') ? $this->Html->link($pago->cliente->nombre, ['controller' => 'Clientes', 'action' => 'view', $pago->cliente->id]) : '' ?></td>
                                    <td> $<?= $this->Number->format($pago->importe) ?></td>
                                    <td><?= h($pago->descripcion) ?></td>
                                    <td class="actions">
                                        <?= $this->Html->link(__('Ver'), ['action' => 'view', $pago->id]) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>                       
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</section>
<!-- /.content -->
<div class="modal modal-success fade in" id="modal-success" style="display: none; padding-right: 20px;">
    <div class="modal-dialog">
        <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">Ã—</span></button>
                <h4 class="modal-title">Agregar Cobro</h4>
              </div>
              <div class="modal-body">
                <?= $this->Form->create($otropago,[
                    'id' => "formAgregarCobro", 
                    'class'=>'form-control-horizontal',
                    'url'=>[                                
                        'controller'=>'Pagos',
                        'action'=>'add',

                    ],
                    'type'=>'post',
                ]) ?>
                &nbsp;
                <?php
                    echo $this->Form->control('puntodeventa_id', ['value' => $micaja['puntodeventa']['id'], 'type' => 'hidden']);
                     $tz = '-0300';
                    $timestamp = time();
                    $dt = new DateTime("now", new DateTimeZone($tz)); //first argument "must" be a string
                    $dt->setTimestamp($timestamp); //adjust the object to correct timestamp
                    echo $this->Form->control('fecha', [
                                            'type'=>'text',
                                            'default'=>$dt->format('d-m-Y, H:i:s'),
                                            //'label' => false,
                                            'readonly' => 'readonly',
                                            'class'=>'form-control pull-right',
                                        ]);
                     echo $this->Form->control('user_id', [
                                            'value' => $AuthUserId, 
                                            'type' => 'hidden'
                                        ]);
                    echo $this->Form->control('tipo',[
                        'type'=>'select',
                        'options'=>['cobro'=>'Cobro','deposito'=>'Deposito'],
                        'empty'=>false                            
                    ]);
                    echo $this->Form->control('cliente_id', ['options' => $clientes, 'empty' => true, 'style'=>'width:164px']);
                    echo $this->Form->control('numero',['type'=>'hidden']);
                    echo $this->Form->control('descripcion');
                    echo $this->Form->control('importe');
                   
                   
                ?>
                <?= $this->Form->end() ?>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-outline" onclick='$("#formAgregarCobro").submit()'>Guardar Movimiento</button>
              </div>
        </div>
            <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->

</div>
<div class="modal modal-warning fade in" id="modal-warning" style="display: none; padding-right: 20px;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">Ã—</span></button>
                <h4 class="modal-title">Agregar Pago</h4>
            </div>
            <div class="modal-body">
                <?= $this->Form->create($mipago,[
                    'id' => "formAgregarPago", 
                    'class'=>'form-control-horizontal',
                    'url'=>[                                
                        'controller'=>'Pagos',
                        'action'=>'add',

                    ],
                    'type'=>'post',
                ]) ?>
                <?php
                    echo $this->Form->control('puntodeventa_id', ['value' => $micaja['puntodeventa']['id'], 'type' => 'hidden']);
                     $tz = '-0300';
                    $timestamp = time();
                    $dt = new DateTime("now", new DateTimeZone($tz)); //first argument "must" be a string
                    $dt->setTimestamp($timestamp); //adjust the object to correct timestamp
                    echo $this->Form->control('fecha', [
                                            'type'=>'text',
                                            'default'=>$dt->format('d-m-Y, H:i:s'),
                                            //'label' => false,
                                            'readonly' => 'readonly',
                                            'class'=>'form-control pull-right',
                                        ]);
                     echo $this->Form->control('user_id', [
                                            'value' => $AuthUserId, 
                                            'type' => 'hidden'
                                        ]);
                    echo $this->Form->control('tipo',[
                        'type'=>'select',
                        'options'=>['pago'=>'Pago','retiro'=>'Retiro'],
                        'empty'=>false                            
                    ]);
                    echo $this->Form->control('provedore_id', ['label'=>'Proveedor','options' => $provedores, 'empty' => true, 'style'=>'width:164px']);
                    echo $this->Form->control('numero',['type'=>'hidden']);
                    echo $this->Form->control('descripcion');
                    echo $this->Form->control('importe');
                   
                   
                ?>
                <?= $this->Form->end() ?>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-outline" onclick='$("#formAgregarPago").submit()'>Guardar Movimiento</button>
              </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
