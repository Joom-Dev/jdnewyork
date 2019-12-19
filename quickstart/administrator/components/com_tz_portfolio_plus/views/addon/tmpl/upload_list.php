<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2018 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

$dataServer = $this -> state -> get('list.dataserver');

?>
<div class="tpContainer">
    <button type="button" data-toggle="collapse" data-target="#tpp-addon__upload"
            class="btn btn-success pull-left float-left hasTooltip" title="<?php echo JText::_('JTOOLBAR_UPLOAD');
            ?>"><span class="icon-upload"></span> <?php echo JText::_('JTOOLBAR_UPLOAD'); ?></button>
    <?php
    if($dataServer) {
        if (!COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE) {
            JHtml::_('formbehavior.chosen', 'select');
        } else {
            JHtml::_('formbehavior.chosen', 'select[multiple]');
        }
        // Search tools bar
        echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
    }
    ?>
</div>

<div class="tpp-extension__upload-form <?php echo $this -> state -> get('list.dataserver')?'collapse':''; ?>" id="tpp-addon__upload">
    <fieldset>
        <legend><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_UPLOAD_AND_INSTALL_ADDON');?></legend>
        <div class="form-horizontal">
            <div class="control-group">
                <div class="control-label"><?php echo $this -> form -> getLabel('install_package');?></div>
                <div class="controls"><?php echo $this -> form -> getInput('install_package');?></div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <button class="btn btn-primary btn-small" type="button" onclick="Joomla.submitbutton('addon.install')">
                        <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_UPLOAD_AND_INSTALL');?></button>
                </div>
            </div>
        </div>
    </fieldset>
</div>
<?php
if(empty($this -> itemsServer)){
    if($dataServer) {
        ?>
<div class="alert alert-warning alert-no-items">
    <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
</div>
        <?php
    }
}else{
    $itemsServer   = $this -> itemsServer;

    $loading    = '<span class="loading"><span class="tps tp-sync-alt text-update tp-spin"></span> '
        .JText::_('COM_TZ_PORTFOLIO_PLUS_INSTALLING').'</span>';
    $installed  = '<span class="installed"><span class="tps tp-check"></span> '
        .JText::_('COM_TZ_PORTFOLIO_PLUS_INSTALLED').'</span>';

    $doc    = JFactory::getDocument();
    $doc -> addScriptDeclaration('
        (function($){
            "use strict";
            $(document).ready(function(){
                $(".tpp-extension__list").on("click", ".install-now", function(e){
                    e.preventDefault();
                    var $this   = $(this),
                        href    = $this.attr("href"),
                        loading = $(\''.$loading.'\');
                    if(!$this.hasClass("installing")) {
                        $this.html(loading).addClass("installing");
                        $.ajax({
                            url: "index.php?option=com_tz_portfolio_plus&view=addon&task=addon.ajax_install",
                            method: "POST",
                            data: {
                                view: "addon",
                                pProduceUrl: href
                                , "'.JSession::getFormToken().'": 1
    
                            }
                        }).done(function (res) {                            
                            if(res.success){
                                $this.addClass("disabled").html(\''.$installed.'\');
                            }
        
                            // Always redirect that can show message queue from session
                            if (res.data.redirect) {
                                location.href = res.data.redirect;
                            } else {
                                location.href = "index.php?option=com_tz_portfolio_plus&view=addon&layout=upload";
                            }
                        });
                    }
                });
            });
        })(jQuery);
    ');
    ?>
<div class="tpp-extension__list tpp-extension__flexbox">
    <?php
    foreach ($itemsServer as $i => $item) {
        $detailUrl  = $item -> link;
        if(strpos($detailUrl,'?')){
            $detailUrl  .= '&tmpl=component';
        }else{
            $detailUrl  .= '?tmpl=component';
        }

        $addon      = null;
        $version    = $item -> installedVersion;
        ?>
    <div class="tpp-extension__col">
        <div class="tpp-extension__item">
            <div class="top">
                <h3 class="title">
                    <a data-toggle="modal" href="#tpp-addon__modal-detail-<?php echo $i; ?>">
                        <?php echo $item -> title; ?>
                        <?php if(isset($item -> imageUrl) && $item -> imageUrl){ ?>
                            <img src="<?php echo $item -> imageUrl;?>" alt="<?php echo $item -> title; ?>">
                        <?php } ?>
                    </a>
                </h3>
                <div class="action-links">
                    <ul class="pl-0">
                        <?php
                        $addOnButton    = null;
                        if($item -> pProduce && $item -> pProduce -> pCommercial == true && !$item -> pProduce -> pHasPurchased) {
                            $addOnButton    = 'buynow';
                        }else{
                            $addOnButton = 'install';
                        }

                        if($version && $item -> pProduce){
                            if(!$item -> pProduce ->  pVersion || ($item -> pProduce -> pVersion
                                    && version_compare($version, $item -> pProduce -> pVersion, '>='))){
                                $addOnButton    = 'installed';
                            }elseif($item -> pProduce -> pVersion && version_compare($version, $item -> pProduce -> pVersion, '<')){
                                $addOnButton    = 'update';
                            }
                        }
                        ?>
                        <?php
                        switch ($addOnButton){
                            default:
                            case 'install':
                        ?>
                        <li>
                            <a href="<?php echo $item -> pProduce -> pProduceUrl;
                            ?>" class="install-now btn btn-outline-secondary"><span class="tps tp-download"></span> <?php
                                    echo JText::_('COM_TZ_PORTFOLIO_PLUS_INSTALL_NOW'); ?></a>
                        </li>
                            <?php
                                break;
                            case 'update':
                            ?>
                        <li>
                            <a href="<?php echo $item -> pProduce ->  pProduceUrl;
                            ?>" class="install-now btn btn-outline-secondary"><span class="tps tp-sync-alt text-update"></span> <?php
                                echo JText::_('COM_TZ_PORTFOLIO_PLUS_UPDATE_NOW'); ?></a>
                        </li>
                            <?php
                                break;
                            case 'buynow':
                            ?>
                        <li>
                            <a href="<?php echo $item -> pProduce ->  pProduceUrl?$item -> pProduce ->  pProduceUrl:$item -> link;
                            ?>" target="_blank" class="btn btn-outline-secondary"><span class="tps tp-shopping-cart"></span> <?php
                                echo JText::_('COM_TZ_PORTFOLIO_PLUS_BUY_NOW'); ?></a>
                        </li>
                                <?php
                                break;
                            case 'installed':
                                ?>
                                <li><button type="button" class="btn btn-outline-success disabled"><?php echo $installed; ?></li>
                        <?php
                                break;
                        }?>
                        <?php if(isset($item -> liveDemoUrl) && $item -> liveDemoUrl){ ?>
                            <li>
                                <a target="_blank" class="btn btn-success js-tpp-live-demo" href="<?php
                                echo $item -> liveDemoUrl; ?>"><i class="tpr tp-eye"></i> <?php
                                    echo JText::_('COM_TZ_PORTFOLIO_PLUS_LIVE_DEMO');?></a>
                            </li>
                        <?php } ?>
                        <li>
                            <a data-toggle="modal" href="#tpp-addon__modal-detail-<?php echo $i; ?>"><?php
                                echo JText::_('COM_TZ_PORTFOLIO_PLUS_MORE_DETAIL');?></a>
                        </li>
                    </ul>
                </div>
                <div class="desc">
                    <?php echo $item -> introtext; ?>
                    <p class="author">
                        <?php
                        $author = '<strong>'.$item -> author.'</strong>';
                        echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_BY', $author);
                        ?>
                    </p>
                </div>
                <?php
                echo JHtml::_(
                    'bootstrap.renderModal',
                    'tpp-addon__modal-detail-'.$i,
                    array(
                        'url'        => $detailUrl,
                        'title'      => $item -> title,
                        'width'      => '400px',
                        'height'     => '800px',
                        'modalWidth' => '70',
                        'bodyHeight' => '70',
                        'closeButton' => true,
                        'footer'      => '<a class="btn" data-dismiss="modal" aria-hidden="true">' . JText::_('JCANCEL') . '</a>',
                    )
                );
                ?>
            </div>
            <div class="bottom">
                <ul class="unstyled list-unstyled pull-left float-left">
                    <li><?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_LATEST_VERSION', '') ?><span><?php
                            echo $item -> pProduce -> pVersion?$item -> pProduce ->  pVersion:JText::_('COM_TZ_PORTFOLIO_PLUS_NA');
                            ?></span>
                    </li>
                    <li><?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_INSTALLED_VERSION', ''); ?><span><?php
                            echo $item -> installedVersion?$item -> installedVersion:JText::_('COM_TZ_PORTFOLIO_PLUS_NA');
                            ?></span>
                    </li>
                </ul>
                <ul class="unstyled list-unstyled pull-right float-right text-right">
                    <li><?php
                        $updated = '<span>'.JHtml::_('date', $item -> modified, JText::_('DATE_FORMAT_LC4')).'</span>';
                        echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_LAST_UPDATED', $updated);
                        ?></li>
                </ul>
            </div>
        </div>
    </div>
    <?php }
    ?>
</div>
    <?php echo $this -> paginationServer -> getListFooter();?>
<?php
}