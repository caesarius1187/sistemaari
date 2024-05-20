<?php
use Cake\Routing\Router;
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Compra[]|\Cake\Collection\CollectionInterface $compras
 */
$this->Html->css([
    'AdminLTE./plugins/datepicker/datepicker3',
    'AdminLTE./plugins/datatables/dataTables.bootstrap',
  ],
  ['block' => 'css']);

$this->Html->script([
    'AdminLTE./plugins/datatables/jquery.dataTables.min',
    'AdminLTE./plugins/datepicker/bootstrap-datepicker',
    'AdminLTE./plugins/datatables/dataTables.bootstrap.min',
],
['block' => 'script']);
?>

<?php $this->start('scriptBottom'); ?>
<script>
  $(function () {
    $("#tblCompras").DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        }
    });
  });
  $('.datepicker').datepicker({
      autoclose: true,
      format: 'dd-mm-yyyy'
    });
</script>
<?php $this->end(); ?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <?php
                    echo $this->Form->button(
                        'Agregar', 
                        array(
                            'onclick' => "window.location.href='".Router::url(
                                array(
                                    'controller' => 'compras',
                                    'action' => 'add')
                             )."'",
                            'class'=>'btn btn-block btn-primary btn-flat btn-add',                            
                        )
                    );
                    echo $this->Form->create($compra,[
                            'id' => "formListarCompra", 
                            'class'=>'form-control-horizontal',
                            ]) ?>
                    <fieldset>
                        <legend>
                            <?= __('Compras desde el dia '.date('d-m-Y',strtotime($fechaComprasDesde))) ?> 
                            <?= __('hasta el dia '.date('d-m-Y',strtotime($fechaComprasHasta))) ?> 
                            <?php if(count($micaja)>0){
                                ?>
                                <h3 class="box-title">Punto de Compra ::</h3> 
                                <?php echo str_pad($micaja['puntodeventa']['numero'], 5, "0", STR_PAD_LEFT); 
                            }
                            ?>
                        </legend>
                        <?php
                             echo $this->Form->control('fecha', [
                                    'label'=>'Desde',
                                    'type'=>'text',
                                    'default'=>date('d-m-Y'),
                                    //'label' => false,
                                    'empty' => true,
                                    'class'=>'form-control pull-right datepicker',
                                    'templates' => [
                                        'inputContainer' => '
                                            <div class="input-group date">
                                                {{content}}
                                            </div>'
                                    ],
                                ]);
                             echo $this->Form->control('fechahasta', [
                                    'label'=>'Hasta',
                                    'type'=>'text',
                                    'default'=>date('d-m-Y'),
                                    //'label' => false,
                                    'empty' => true,
                                    'class'=>'form-control pull-right datepicker',
                                    'templates' => [
                                        'inputContainer' => '
                                            <div class="input-group date">
                                                {{content}}
                                            </div>'
                                    ],
                                ]);

                        ?>
                         <?= $this->Form->button(__('ver otra fecha'),[
                            'style'=>'vertical-align: bottom;'
                          ]) ?>
                    </fieldset>
                    <?= $this->Form->end() ?>    
                   
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="compras index large-9 medium-8 columns content">
                    <table cellpadding="10" cellspacing="10" id="tblCompras">
                        <thead>
                            <tr>
                                <th scope="col">fecha</th>
                                <th scope="col" style="min-width:120px">Provedor</th>
                                <th scope="col">N Factura</th>
                                <th scope="col">neto</th>
                                <th scope="col">total</th>
                                <th scope="col" class="actions"><?= __('Actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($compras as $compra): ?>
                            <tr>
                                <td><?= h(date('d-m-Y',strtotime($compra->fecha))) ?></td>
                                <td><?= $compra->has('cliente') ? $this->Html->link($compra->cliente->nombre, ['controller' => 'Clientes', 'action' => 'view', $compra->cliente->id]) : '' ?></td>
                                <td><?php 
                                    $numeroPDV = str_pad($compra->puntodeventa->numero, 5, "0", STR_PAD_LEFT)."-".$compra->numero;
                                    echo $this->Html->link( $numeroPDV, ['controller' => 'Puntodeventas', 'action' => 'view', $compra->puntodeventa->id]);
                                    ?></td>
                                <td><?= $this->Number->format($compra->neto) ?></td>
                                <td><?= $this->Number->format($compra->total) ?></td>
                                <td class="actions">
                                    <?php 
                                    $iconoPrint = '<i class="fa fa-print"></i>';
                                    echo $this->Html->link($iconoPrint, [
                                            'action' => 'view', $compra->id
                                        ],
                                        [
                                            'escape' => false,
                                        ]  
                                    ); 
                                    $iconoTrash = '<i class="fa fa-trash"></i>';
                                    echo $this->Form->postLink(
                                        $iconoTrash,
                                            ['action' => 'delete', $compra->id,], 
                                            [
                                                'confirm' => __('Are you sure you want to delete # {0}?', $compra->id),
                                                'escape' => false,
                                            ],                                                
                                            ['class' =>'btn btn-app-select',]
                                        ) ?>
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

