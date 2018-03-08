<?php
/**
 * @package      Crowdfunding
 * @subpackage   Modules
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('Prism.init');
jimport('Crowdfunding.init');

$moduleclassSfx = htmlspecialchars($params->get('moduleclass_sfx'));

switch ($params->get('order_by', Crowdfunding\Constants::ORDER_BY_CREATED_DATE)) {
    case 1: // Title
        $orderBy = 'a.title';
        break;

    case 2: // Created date
        $orderBy = 'a.created_time';
        break;

    default: // Ordering
        $orderBy = 'a.lft';
        break;
}

$app    = JFactory::getApplication();
$option = $app->input->getCmd('option');
$view   = $app->input->getCmd('view');

// Get parent category ID.
$parentId = 0;
if (strcmp('com_crowdfunding', $option) === 0 and strcmp('category', $view) === 0) {
    $parentId = (int)$app->input->getUint('id');
}
$parentId = $parentId ?: Prism\Constants::CATEGORY_ROOT;

$categories = null;
$subcategories = null;
$selectedCategory = null;

$layout   = $params->get('layout', 'default');

// Layout List
if (strcmp($layout, 'list')) {
    $selectedCategory = null;
    $subcategories    = null;
    $activeItemId     = 0;
    $openedItemId     = 0;

    $items = new Crowdfunding\Category\Joomla\Categories();
    if ($parentId === Prism\Constants::CATEGORY_ROOT) {
        $root       = $items->get();
        $categories = $root->getChildren();
    } else {
        $activeItemId            = $parentId;
        $openedItemId            = $parentId;
        $selectedCategory        = $items->get($parentId);

        // If there is no item, do not continue.
        if (!$selectedCategory) {
            return;
        }

        $selectedCategoryParent  = $selectedCategory->getParent();
        /** @var JCategoryNode $selectedCategoryParent */

        if ($selectedCategoryParent !== null && $selectedCategoryParent->hasChildren()) {
            $categories          = $selectedCategoryParent->getChildren();

            if ($selectedCategory->hasChildren()) {
                $subcategories   = $selectedCategory->getChildren();
            } else {
                $activeItemId    = (int)$selectedCategory->id;
                $openedItemId    = (int)$selectedCategoryParent->id;
                $subcategories   = $selectedCategoryParent->getChildren();

                $selectedCategoryParent  = $selectedCategoryParent->getParent();
                if ($selectedCategoryParent !== null) {
                    $categories   = $selectedCategoryParent->getChildren();
                }
            }
        }
    }
}

// Layout Default
if (strcmp('_:default', $layout) === 0) {
    $items = new Crowdfunding\Category\Joomla\Categories();
    if ($parentId === Prism\Constants::CATEGORY_ROOT) {
        $root       = $items->get();
        $categories = $root->getChildren();
    } else {
        $selectedCategory  = $items->get($parentId);

        // If there is no item, do not continue.
        if (!$selectedCategory) {
            return;
        }

        $categories        = $selectedCategory->getChildren();
    }

    if (is_array($categories) && count($categories) > 0) {
        $descriptionLength = $params->get('description_length', 128);

        $numberInRow = $params->get('items_in_row', 3);
        $itemSpan    = empty($numberInRow) ? 3 : round(12 / $numberInRow);

        $helperBus  = new Prism\Helper\HelperBus($categories);
        $helperBus->addCommand(new Crowdfunding\Helper\PrepareParamsHelper());
        $helperBus->handle();
    }
}

// Get number of items.
$numberOfItems   = array();
$showItemsNumber = $params->get('display_projects_number', Prism\Constants::NO);
if ($showItemsNumber) {
    $ids  = array();
    $ids2 = array();
    if (is_array($categories)) {
        $ids = Prism\Utilities\ArrayHelper::getIds($categories);
    }

    if (is_array($subcategories)) {
        $ids2 = Prism\Utilities\ArrayHelper::getIds($subcategories);
    }

    if ($selectedCategory !== null) {
        $ids[] = (int)$selectedCategory->id;
    }

    $ids = array_merge($ids, $ids2);
    $ids = array_filter(array_unique($ids));

    if (count($ids) > 0) {
        $statistics    = new Crowdfunding\Statistics\Basic(\JFactory::getDbo());
        $numberOfItems = $statistics->countCategoryItems($ids, array('state' => Prism\Constants::PUBLISHED, 'approved' => Prism\Constants::APPROVED));
    }
}

// Sort categories.
usort($categories, function ($left, $right) {
    return strnatcmp($left->title, $right->title);
});

require JModuleHelper::getLayoutPath('mod_crowdfundingcategories', $params->get('layout', 'default'));
