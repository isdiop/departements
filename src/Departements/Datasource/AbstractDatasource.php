<?php

namespace Departements\Datasource;

use PhpCollection\Map;

abstract class AbstractDatasource implements DatasourceInterface
{
    protected $regions;
    protected $departements;
    protected $index;

    public function __construct() {
        $this->regions      = new Map();
        $this->departements = new Map();
        $this->communes     = new Map();
        $this->index = array(
            'departements' => array(),
            'regions'      => array(),
            'communes'     => array()
        );
    }

    protected function slugify($value) {
        $value = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $value));
        return preg_replace("/[^a-z0-9]/", "", $value);
    }

    protected function addToIndex($type, $value, $key) {
        $this->index[$type][$this->slugify($value)] = $key;
    }

    public function findAllDepartements($sortByValue = false) {
        if (!$sortByValue) {
            return $this->departements;
        }

        return $this->sortByValue($this->departements);
    }

    public function findAllRegions($sortByValue = false) {
        if (!$sortByValue) {
            return $this->regions;
        }

        return $this->sortByValue($this->regions);
    }

    public function findAllCommunes() {
        return $this->communes;
    }

    public function findDepartementByCode($departementCode) {
        return $this->departements->get($departementCode)->getOrElse(null);
    }

    public function findDepartementByName($departementName) {
        $departementName = $this->slugify($departementName);

        if(!isset($this->index['departements'][$departementName])) {
            return null;
        }

        $departementCode = $this->index['departements'][$departementName];

        return $this->departements->get($departementCode)->get();
    }

    public function findRegionByName($regionName) {
        $regionName = $this->slugify($regionName);

        if(!isset($this->index['regions'][$regionName])) {
            return null;
        }

        $regionCode = $this->index['regions'][$regionName];

        return $this->regions->get($regionCode)->get();
    }

    public function findRegionByCode($regionCode) {
        return $this->regions->get($regionCode)->getOrElse(null);
    }

    protected function sortByValue($collection) {
        $collator = new \Collator('fr_FR');

        $items = iterator_to_array($collection);
        $collator->asort($items);

        return new Map($items);
    }
}

