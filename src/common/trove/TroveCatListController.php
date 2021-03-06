<?php
/**
 * Copyright (c) Enalean, 2016. All Rights Reserved.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Tuleap\Trove;

use HTTPRequest;
use TroveCatDao;
use TroveCatFactory;

class TroveCatListController
{
    /**
     * @var TroveCatDao
     */
    private $trove_cat_dao;
    /**
     * @var TroveCatFactory
     */
    private $trove_cat_factory;
    /**
     * @var TroveCatHierarchyRetriever
     */
    private $trove_cat_retriever;

    public function __construct(
        TroveCatDao $trove_cat_dao,
        TroveCatFactory $trove_cat_factory,
        TroveCatHierarchyRetriever $trove_cat_retriever
    ) {
        $this->trove_cat_dao       = $trove_cat_dao;
        $this->trove_cat_factory   = $trove_cat_factory;
        $this->trove_cat_retriever = $trove_cat_retriever;
    }

    public function add(HTTPRequest $request)
    {
        $trove_category = $this->formatTroveCategoriesFromRequest($request);
        $this->trove_cat_dao->add(
            $trove_category['shortname'],
            $trove_category['fullname'],
            $trove_category['description'],
            $trove_category['parent'],
            $trove_category['display'],
            $trove_category['mandatory'],
            $trove_category['trove_cat_id'],
            $trove_category['fullpath'],
            $trove_category['fullpath_ids']
        );
    }

    private function isMandatory($newroot, $mandatory)
    {
        if ($newroot !== 0) {
            $mandatory = 0;
        }

        return $mandatory;
    }

    private function isANewRootChild($id, $display)
    {
        $list_of_top_level_category_ids = array_keys($this->trove_cat_factory->getMandatoryParentCategoriesUnderRoot());

        if (! in_array($id, $list_of_top_level_category_ids)) {
            $display = 0;
        }

        return $display;
    }

    public function update(HTTPRequest $request)
    {
        $current_trove_category = $this->formatTroveCategoriesFromRequest($request);

        $last_parent             = array();
        $already_seen            = array();
        $trove_category_children = array();
        $last_parent_ids         = array();
        $this->trove_cat_retriever->retrieveChildren(
            $current_trove_category['trove_cat_id'],
            $last_parent,
            $already_seen,
            $trove_category_children,
            $last_parent_ids
        );

        $this->trove_cat_dao->startTransaction();

        if ($current_trove_category['parent'] ===  $current_trove_category['trove_cat_id']) {
            $this->trove_cat_dao->rollBack();
            throw new CannotMoveFatherInChildException();
        }

        $this->trove_cat_dao->updateTroveCat(
            $current_trove_category['shortname'],
            $current_trove_category['fullname'],
            $current_trove_category['description'],
            $current_trove_category['parent'],
            $current_trove_category['display'],
            $current_trove_category['mandatory'],
            $current_trove_category['display'],
            $current_trove_category['trove_cat_id'],
            $current_trove_category['fullpath'],
            $current_trove_category['fullpath_ids']
        );

        foreach ($trove_category_children as $child) {
            if ($current_trove_category['parent'] === $child['trove_cat_id']) {
                $this->trove_cat_dao->rollBack();
                throw new CannotMoveFatherInChildException();
            }
            $this->trove_cat_dao->updateTroveCat(
                $child['shortname'],
                $child['fullname'],
                $child['description'],
                $child['parent'],
                $child['display_during_project_creation'],
                $child['is_top_level_id'],
                $child['display_during_project_creation'],
                $child['trove_cat_id'],
                $current_trove_category['fullpath'] . ' :: ' . $child['hierarchy'],
                $current_trove_category['fullpath'] . ' :: ' . $child['hierarchy_ids']
            );
        }

        $this->trove_cat_dao->Commit();
    }

    private function formatTroveCategoriesFromRequest(HTTPRequest $request)
    {
        if (! $request->get('fullname')) {
            throw new TroveCatMissingFullNameException();
        }

        if (! $request->get('shortname')) {
            throw new TroveCatMissingShortNameException();
        }

        $display = $this->isANewRootChild(
            $request->get('parent'),
            $request->get('display-at-project-creation')
        );

        $trove_cat_list  = array();
        $already_seen    = array();
        $last_parent     = array();
        $last_parent_ids = array();
        $this->trove_cat_retriever->retrieveFathers(
            $request->get('parent'),
            $last_parent,
            $already_seen,
            $trove_cat_list,
            $last_parent_ids
        );

        $trove_categories = array(
            'shortname'    => $request->get('shortname'),
            'fullname'     => $request->get('fullname'),
            'description'  => $request->get('description'),
            'parent'       => $request->get('parent'),
            'display'      => $display,
            'mandatory'    => $this->isMandatory($display, $request->get('is-mandatory')),
            'trove_cat_id' => $request->get('id'),
            'fullpath'     => (isset($trove_cat_list['hierarchy'])) ? $trove_cat_list['hierarchy'] : '',
            'fullpath_ids' => (isset($trove_cat_list['hierarchy'])) ? $trove_cat_list['hierarchy_id'] : ''
        );

        return $trove_categories;
    }

    private function isRoot($parent)
    {
        return (int) $parent === 0;
    }

    public function delete(HTTPRequest $request)
    {
        if ($this->isRoot($request->get('parent'))) {
            throw new TroveCatParentIsRootException();
        }

        $trove_cat_id            = $request->get('trove_cat_id');
        $last_parent             = array();
        $already_seen            = array();
        $trove_category_children = array();
        $hierarchy_ids           = array();

        $this->trove_cat_retriever->retrieveChildren(
            $trove_cat_id,
            $last_parent,
            $already_seen,
            $trove_category_children,
            $hierarchy_ids
        );

        $this->trove_cat_dao->startTransaction();

        foreach ($trove_category_children as $child) {
            $this->trove_cat_dao->delete($child['trove_cat_id']);
        }

        $this->trove_cat_dao->delete($request->get('trove_cat_id'));

        $this->trove_cat_dao->commit();
    }
}
