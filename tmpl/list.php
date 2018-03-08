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
$doc->addStyleSheet("modules/mod_crowdfundingcategories/css/style.css");

$html2 = array();
if ($selectedCategory !== null and $subcategories !== null) {
    $html2[] = '<ul class="cf-modcategories-navcat">';

    foreach ($subcategories as $subcategory) {
        $numberHtml  = $showItemsNumber ? ' (0)':'';
        $itemsNumber = 0;
        if ($showItemsNumber and array_key_exists($subcategory->id, $numberOfItems)) {
            $numberHtml = ' ('.(int)$numberOfItems[$subcategory->id].')';
        }

        $active = '';
        if ((int)$subcategory->id === $activeItemId) {
            $active = 'cf-modcategories-navcat-active';
        }

        $html2[] = '<li class="cf-modcategories-navcat-li ' . $active .'">';
        $html2[] = '<a href="' .JRoute::_(CrowdfundingHelperRoute::getCategoryRoute($subcategory->slug)) .'">';
        $html2[] = htmlspecialchars($subcategory->title, ENT_COMPAT, 'UTF-8');
        $html2[] = $numberHtml;
        $html2[] = '</a>';
        $html2[] = '</li>';
    }

    $html2[] = '</ul>';
} ?>

<div class="cf-modcategories-list<?php echo $moduleclassSfx; ?>">
    <?php
    if ($categories !== null) {
        $html   = array();
        $html[] = '<ul class="cf-modcategories-navcat cf-modcategories-catnav-primary">';

        foreach ($categories as $category) {
            $active      = '';
            $numberHtml  = $showItemsNumber ? ' (0)' : '';
            $itemsNumber = 0;
            if ($showItemsNumber and array_key_exists($category->id, $numberOfItems)) {
                $numberHtml = ' (' . (int)$numberOfItems[$category->id] . ')';
            }

            $active = '';
            if ((int)$category->id === $activeItemId) {
                $active = ' cf-modcategories-navcat-active';
            }

            $html[] = '<li class="cf-modcategories-navcat-li ' . $active . '">';
            $html[] = '<a href="' . JRoute::_(CrowdfundingHelperRoute::getCategoryRoute($category->slug)) . '">' . htmlspecialchars($category->title, ENT_COMPAT, 'UTF-8') . $numberHtml . '</a>';
            $html[] = '</li>';
            if ((int)$category->id === (int)$openedItemId and count($html2) > 0) {
                $html[] = implode("\n", $html2);
            }
        }
        $html[] = '</ul>';

        echo implode("\n", $html);
    }
    ?>
</div>
