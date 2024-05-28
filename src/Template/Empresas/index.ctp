<?php
//QUERY para actualizar precios de prductos
//update productos set productos.precio = (1+(ganancia/100))*costo
//update productos set productos.preciopack = (1+(gananciapack/100))*costo
//UPDATE productos SET productos.codigo = REPLACE (productos.codigo, ' ', '')
//UPDATE productos SET productos.codigo = REPLACE (productos.codigo, ',', '')
//echo $this->Html->script('jquery.dataTables.js',array('inline'=>false));

use Cake\Routing\Router;
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Producto[]|\Cake\Collection\CollectionInterface $productos
 */
echo $this->Html->script('productos/index', ['block' => true]);
echo $this->Html->script('table2excel',array('inline'=>false));

$this->Html->css([
    'AdminLTE./plugins/datatables/dataTables.bootstrap',
  ],
  ['block' => 'css']);

$this->Html->script([
  'AdminLTE./plugins/datatables/jquery.dataTables.min',
  'AdminLTE./plugins/datatables/dataTables.bootstrap.min',
],
['block' => 'script']);
?>

<?php $this->start('scriptBottom'); ?>
<script>
  
</script>

<?php $this->end(); ?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
            <div class="box-header">
                    <h3 class="box-title">Mi Empresa</h3>
                </div>
                <div class="box-header">
                    <h3 class="box-title">Mi Empresa</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="productos index large-9 medium-8 columns content">
                    <table cellpadding="0" cellspacing="0" id="tblProductos" class="toExcelTable">
                        <thead>
                            <tr>
                                <th scope="col">Nombre</th>
                                <th scope="col" class="actions"><?= __('Act.') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($empresas as $empresa): ?>
                            <tr >
                                <td ><label style="width: 100px;" class="tdTruncated"><?= $empresa->nombre ?><label></td>                               
                                <td class="actions">
                                    <?php 
                                    echo $this->Html->link(__('Ver'), ['action' => 'view', $empresa->id]); 
                                    echo $this->Html->link(__('Editar'), ['action' => 'edit', $empresa->id], ['target' => '_blank']) ;
                                    echo $this->Form->postLink(__('Eliminar'), ['action' => 'delete', $empresa->id], ['confirm' => __('Are you sure you want to delete # {0}?', $empresa->id)]) ;
                                    ?>
                                </td>
                            </tr>
                            <?php 
                            endforeach; ?>
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

<?php
function unlockfields($context){

}
?>