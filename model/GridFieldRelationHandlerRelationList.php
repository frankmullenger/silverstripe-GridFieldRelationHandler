<?php

class GridFieldManyRelationHandler_HasManyList extends HasManyList {
	public function __construct() {

	}

	public function getForeignKey(HasManyList $on) {
		return $on->foreignKey;
	}

	public function getForeignIDFilter(RelationList $on) {
		return $on->foreignIDFilter();
	}
}

class GridFieldManyRelationHandler_ManyManyList extends ManyManyList {
	public function __construct() {

	}

	public function getJoinTable(ManyManyList $on) {
		return $on->joinTable;
	}

	public function getLocalKey(ManyManyList $on) {
		return $on->localKey;
	}

	public function getForeignKey(ManyManyList $on) {
		return $on->foreignKey;
	}

	public function getForeignIDFilter(RelationList $on) {
		return $on->foreignIDFilter();
	}
}
