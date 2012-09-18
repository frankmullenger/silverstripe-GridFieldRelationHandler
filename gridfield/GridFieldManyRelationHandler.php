<?php

class GridFieldManyRelationHandler extends GridFieldRelationHandler implements GridField_DataManipulator {
	protected $cheatList;
	protected $cheatManyList;

	public function __construct($useToggle = true, $segement = 'before') {
		parent::__construct($useToggle, $segement);
		$this->cheatList = new GridFieldManyRelationHandler_HasManyList;
		$this->cheatManyList = new GridFieldManyRelationHandler_ManyManyList;
	}

	public function getColumnContent($gridField, $record, $columnName) {
		$list = $gridField->getList();
		if(!$list instanceof RelationList) {
			user_error('GridFieldManyRelationHandler requires the GridField to have a RelationList. Got a ' . get_class($list) . ' instead.', E_USER_WARNING);
		}

		$state = $gridField->State->GridFieldRelationHandler;
		$checked = in_array($record->ID, $state->RelationVal->toArray());
		$field = array('Checked' => $checked, 'Value' => $record->ID, 'Name' => $this->relationName($gridField));
		if($list instanceof HasManyList) {
			$key = $record->{$this->cheatList->getForeignKey($list)};
			if($key && !$checked) {
				$field['Disabled'] = true;
			}
		}
		$field = new ArrayData($field);
		return $field->renderWith('GridFieldManyRelationHandlerItem');
	}

	public function getManipulatedData(GridField $gridField, SS_List $list) {
		if(!$list instanceof RelationList) {
			user_error('GridFieldManyRelationHandler requires the GridField to have a RelationList. Got a ' . get_class($list) . ' instead.', E_USER_WARNING);
		}

		$state = $gridField->State->GridFieldRelationHandler;
		if($state->FirstTime) {
			$state->RelationVal = array_values($list->getIdList()) ?: array();
		}
		if(!$state->ShowingRelation && $this->useToggle) {
			return $list;
		}

		$query = clone $list->dataQuery();
		try { 
			$query->removeFilterOn($this->cheatList->getForeignIDFilter($list));
		} catch(InvalidArgumentException $e) { /* NOP */ }
		$orgList = $list;
		$list = new DataList($list->dataClass());
		$list = $list->setDataQuery($query);
		if($orgList instanceof ManyManyList) {
			$joinTable = $this->cheatManyList->getJoinTable($orgList);
			$baseClass = ClassInfo::baseDataClass($list->dataClass());
			$localKey = $this->cheatManyList->getLocalKey($orgList);
			$query->leftJoin($joinTable, "\"$joinTable\".\"$localKey\" = \"$baseClass\".\"ID\"");
			$list = $list->setDataQuery($query);
		}
		return $list;
	}

	protected function relationName($gridField) {
		return $gridField->getName() . get_class($gridField->getList());
	}

	protected function cancelGridRelation(GridField $gridField, $arguments, $data) {
		parent::cancelGridRelation($gridField, $arguments, $data);

		$state = $gridField->State->GridFieldRelationHandler;
		$state->RelationVal = array_values($gridField->getList()->getIdList()) ?: array();
	}

	protected function saveGridRelation(GridField $gridField, $arguments, $data) {
		$state = $gridField->State->GridFieldRelationHandler;
		$gridField->getList()->setByIdList($state->RelationVal->toArray());
		parent::saveGridRelation($gridField, $arguments, $data);
	}
}
