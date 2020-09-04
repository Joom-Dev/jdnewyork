<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2015 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die('Restricted access');

JLoader::register('TZ_Portfolio_PlusHelperAddon_Datas', COM_TZ_PORTFOLIO_PLUS_ADMIN_HELPERS_PATH
    .DIRECTORY_SEPARATOR.'addon_datas.php');

class TZ_Portfolio_PlusViewExtension extends JViewLegacy
{
    protected $item;
    protected $addonItem;
    protected $state;
    protected $addonItems;
    protected $return_link;
    protected $itemsServer;
    protected $paginationServer;
    public    $filterForm;

    public function display($tpl = null)
    {
        $this->state                = $this->get('State');
        $this->item                 = $this->get('Item');
        $this -> return_link        = $this -> get('ReturnLink');

        if($this -> getLayout() == 'manager') {
            $this->addonItem = $this->get('AddonItem');
        }

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        if($this -> getLayout() == 'upload') {
            $this -> document -> addScript(TZ_Portfolio_PlusUri::base(true, true).'/js/libs.min.js',
                array('version' => 'auto'));
            $this -> filterForm   = $this -> get('FilterForm');
			
            TZ_Portfolio_PlusHelper::addSubmenu('extension');
            $this->sidebar = JHtmlSidebar::render();
        }

        $this->addToolbar();

        parent::display($tpl); // TODO: Change the autogenerated stub
    }

    protected function addToolbar(){

        $user		= JFactory::getUser();
        $userId		= $user->get('id');

        $canDo = JHelperContent::getActions('com_tz_portfolio_plus');
        $checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);

        JToolBarHelper::title(JText::_('COM_TZ_PORTFOLIO_PLUS_EXTENSIONS_MANAGEMENT'),'cube');

        if($this -> getLayout() == 'edit'){
            // If not checked out, can save the item.
            if (!$checkedOut) {
                if ($canDo->get('core.edit')) {
                    JToolBarHelper::apply($this -> getName().'.apply');
                    JToolBarHelper::save($this -> getName().'.save');
                }
            }
        }

        if($user->authorise('core.admin', 'com_tz_portfolio_plus')
            || $user->authorise('core.options', 'com_tz_portfolio_plus')){
            JToolBarHelper::preferences('com_tz_portfolio_plus');
        }

        JToolBarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER',false,
            'https://www.tzportfolio.com/document/add-ons/28-installation.html?tmpl=component');

        TZ_Portfolio_PlusToolbarHelper::customHelp('https://www.youtube.com/channel/UCrLN8LMXTyTahwDKzQ-YOqg/videos'
            ,'COM_TZ_PORTFOLIO_PLUS_VIDEO_TUTORIALS', 'youtube', 'youtube');

        if($this -> getLayout() == 'upload') {
            JToolbarHelper::link('javascript:', JText::_('COM_TZ_PORTFOLIO_PLUS_INTRO_GUIDE'), 'support');
        }
    }
}