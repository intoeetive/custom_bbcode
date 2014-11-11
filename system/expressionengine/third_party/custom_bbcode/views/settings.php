
<?=form_open('C=addons_extensions'.AMP.'M=save_extension_settings'.AMP.'file=custom_bbcode');?>

<?=lang('custom_bbcode_instructions')?>

<?php 
$this->table->set_template($cp_pad_table_template);
$this->table->set_heading(
    array('data' => lang('bbcode_tag'), 'style' => 'width:50%;'),
    lang('parsed_to_html')
);

foreach ($settings as $key => $val)
{
	if ($key == '__bbcode__')
    {
        $key = form_input('__bbcode__', '');
    }
    $this->table->add_row($key, $val);
}

echo $this->table->generate();

?>

<p><?=form_submit('submit', lang('submit'), 'class="submit"')?></p>
<?php $this->table->clear()?>
<?=form_close()?>