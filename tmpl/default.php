<?php
/**
 * @package      Crowdfunding
 * @subpackage   Modules
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2013 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */
 
// no direct access
defined('_JEXEC') or die;
$doc = JFactory::getDocument();
$doc->addStyleSheet('modules/mod_crowdfundingcategories/css/style.css');
?>
<div class="cf-modcategories<?php echo $moduleclassSfx; ?>">
    <?php if (is_array($categories) and count($categories) > 0) { ?>
        <div class="row">
        <?php foreach ($categories as $item) {
            $item->title = htmlentities($item->title, ENT_QUOTES, 'UTF-8');
            ?>
            <div class="col-md-<?php echo $itemSpan; ?>">
                <div class="thumbnail cf-category">
                   <?php if ($params->get('display_images', Prism\Constants::YES)) {?>
                    <a href="<?php echo JRoute::_(CrowdfundingHelperRoute::getCategoryRoute($item->slug)); ?>">
                        <?php if ($item->params->get('image')) { ?>
                            <img src="<?php echo $item->params->get('image'); ?>" alt="<?php echo $item->title; ?>" />
                        <?php } else { ?>
                            <img src="<?php echo 'media/com_crowdfunding/images/no_image.png'; ?>" alt="<?php echo $item->title; ?>" width="200" height="200" />
                        <?php } ?>
                    </a>
                    <?php } ?>
                    <div class="caption height-150px absolute-bottom">
                        <h3>
                            <a href="<?php echo JRoute::_(CrowdfundingHelperRoute::getCategoryRoute($item->slug)); ?>">
                                <?php echo $item->title; ?>
                            </a>
                            <?php
                            if ($params->get('display_projects_number', 0)) {
                                $number = (!isset($numberOfItems[$item->id])) ? 0 : $numberOfItems[$item->id];
                                echo '( '.$number. ' )';
                            } ?>
                        </h3>
                        <?php if ($params->get('display_description', 1)) { ?>
                            <p><?php echo JHtmlString::truncate($item->description, $descriptionLength, true, false); ?></p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
        </div>
    <?php } ?>
</div>
