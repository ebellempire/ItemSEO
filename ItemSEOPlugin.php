<?php
class ItemSEOPlugin extends Omeka_Plugin_AbstractPlugin
{
	
    const ELEMENT_SET_NAME = 'Item SEO';
    
    protected $_hooks = array(
        'initialize', 
        'install', 
        'uninstall', 
        'uninstall_message', 
        'config',
        'config_form',
        'public_head'
    );	
    
    protected $_filters = array(
        'admin_items_form_tabs'
    );
	
    public function hookInitialize()
    {
    }
    
    /**
    ** Install.
    */
    public function hookInstall()
    {
	    $this->_installOptions();
	    
        // Don't install if an element set with this name already exists.
        if ($this->_db->getTable('ElementSet')->findByName(self::ELEMENT_SET_NAME)) {
            throw new Omeka_Plugin_Installer_Exception(
                __('An element set by the name "%s" already exists. You must delete that element set to install this plugin.', self::ELEMENT_SET_NAME)
            );
        }
        
        $elementSetMetadata = array(
            'name' => self::ELEMENT_SET_NAME,
            'description' => __('Elements added by the Item SEO plugin to add various search engine optimizations to item records.')
            );
        $elements = array(
            array('name' => __('Canonical URL'), 
                  'description' => __('The URL where this content was originally published. Use only when duplicating content from another site, preferably with permission from the original author or publisher.')
            )
        );
        insert_element_set($elementSetMetadata, $elements);
    }

	/**
	** Config
	*/
	public function hookConfig()
    {
	    set_option('seo_delete_on_uninstall', $_POST['seo_delete_on_uninstall']);
    }

	/**
	** Config Form
	*/    
    public function hookConfigForm()
    {
	?>
	
		<h2><?php echo __('Settings'); ?></h2>
		<style>
			.helper{font-size:.85em;}
		</style>
		<fieldset id="settings">
		
			<div class="field">
			    <div class="two columns alpha">
			        <label for="id_items"><?php echo __('Delete Data on Uninstall'); ?></label>
			    </div>
		
			    <div class="inputs five columns omega">
			        <?php echo get_view()->formCheckbox('seo_delete_on_uninstall', true,
			array('checked'=>(boolean)get_option('seo_delete_on_uninstall'))); ?>
		
			        <p class="explanation"><?php echo __('If checked, uninstalling the plugin will delete the %s element set and all data stored in related fields.', self::ELEMENT_SET_NAME); ?></p>
			    </div>
			</div>
		
		</fieldset>	
	
	<?php	    
    }    
    
    /**
    ** Uninstall.
    */
    public function hookUninstall()
    {
        // Delete the element set.
        if(get_option('seo_delete_on_uninstall')){
	        $this->_db->getTable('ElementSet')->findByName(self::ELEMENT_SET_NAME)->delete();
        }
		
		$this->_uninstallOptions();

    }
    
    /**
    ** Appends a warning message to the uninstall confirmation page.
    */
    public function hookUninstallMessage()
    {
	    if(get_option('seo_delete_on_uninstall')){
        	echo '<p>' . __(
            'Warning: Uninstalling will permanently delete the "%s" element set. To uninstall without deleting the element set and its data, stop and update plugin settings before uninstalling.', self::ELEMENT_SET_NAME) . '</p>';
        }
    }	
    
    /*
    ** adds content to <head>
    */
    public function hookPublicHead($args){
        if(is_current_url('/items/show')){
            $record = $args['view']->item;
            if($canonical_url = metadata($record,array('Item SEO', 'Canonical URL'))){
                echo '
                <link rel="canonical" href="'.$canonical_url.'"/>
                ';   
            }
        }
    }
	
    /*
    ** only Super users can access the Item SEO tab
    */
	public function filterAdminItemsFormTabs($tabs, $args){
        if(current_user()->role !== 'super'){
            unset($tabs['Item SEO']);
        }
        return $tabs;
    }
}