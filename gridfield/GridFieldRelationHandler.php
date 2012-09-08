<?php

abstract class GridFieldRelationHandler implements GridField_ColumnProvider, GridField_HTMLProvider, GridField_ActionProvider {
	protected $targetFragment;

	public function __construct($targetFragment = 'before') {
		$this->targetFragment = $targetFragment;
	}

	public function augmentColumns($gridField, &$columns) {
		$state = $gridField->State->GridFieldRelationHandler;
		if(!isset($state->RelationVal)) {
			$state->RelationVal = 0;
			$state->FirstTime = 1;
		} else {
			$state->FirstTime = 0;
		}
		if(!in_array('RelationSetter', $columns)) {
			array_unshift($columns, 'RelationSetter');
		}
	}

	public function getColumnsHandled($gridField) {
		return array('RelationSetter');
	}

	public function getColumnMetadata($gridField, $columnName) {
		if($columnName == 'RelationSetter') {
			return array('title' => '');
		}
		return array();
	}

	public function getColumnAttributes($gridField, $record, $columnName) {
		return array('class' => 'col-noedit');
	}

	public function getHTMLFragments($gridField) {
		Requirements::javascript(basename(dirname(__DIR__)) . '/javascript/GridFieldRelationHandler.js');
		$saveRelation = Object::create(
			'GridField_FormAction',
			$gridField,
			'relationhandler-saverel',
			_t('GridFieldRelationHandler.SAVE_RELATION', 'Save relation status'),
			'saveGridRelation',
			null
		);
		return array(
			$this->targetFragment => $saveRelation->Field()
		);
	}

	public function getActions($gridField) {
		return array('saveGridRelation');
	}

	public function handleAction(GridField $gridField, $actionName, $arguments, $data) {
		if(in_array($actionName, array_map('strtolower', $this->getActions($gridField)))) {
			return $this->$actionName($gridField, $arguments, $data);
		}
	}

	abstract protected function saveGridRelation(GridField $gridField, $arguments, $data);
}
