<div class="gestionproductos form">
<?php echo $this->Form->create('Gestionproducto'); ?>
	<fieldset>
		<legend><?php echo __('Edit Gestionproducto'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('cliente_id');
		echo $this->Form->input('nombre');
		echo $this->Form->input('precio');
		echo $this->Form->input('stockminimo');
		echo $this->Form->input('stockactual');
		echo $this->Form->input('unidadmedida');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Gestionproducto.id')), array(), __('Are you sure you want to delete # %s?', $this->Form->value('Gestionproducto.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Gestionproductos'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Clientes'), array('controller' => 'clientes', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Cliente'), array('controller' => 'clientes', 'action' => 'add')); ?> </li>
	</ul>
</div>
