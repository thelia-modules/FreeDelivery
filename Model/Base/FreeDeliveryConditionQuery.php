<?php

namespace FreeDelivery\Model\Base;

use \Exception;
use \PDO;
use FreeDelivery\Model\FreeDeliveryCondition as ChildFreeDeliveryCondition;
use FreeDelivery\Model\FreeDeliveryConditionQuery as ChildFreeDeliveryConditionQuery;
use FreeDelivery\Model\Map\FreeDeliveryConditionTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;
use Thelia\Model\Area;
use Thelia\Model\Module;

/**
 * Base class that represents a query for the 'free_delivery_condition' table.
 *
 *
 *
 * @method     ChildFreeDeliveryConditionQuery orderByAreaId($order = Criteria::ASC) Order by the area_id column
 * @method     ChildFreeDeliveryConditionQuery orderByModuleId($order = Criteria::ASC) Order by the module_id column
 * @method     ChildFreeDeliveryConditionQuery orderByAmount($order = Criteria::ASC) Order by the amount column
 *
 * @method     ChildFreeDeliveryConditionQuery groupByAreaId() Group by the area_id column
 * @method     ChildFreeDeliveryConditionQuery groupByModuleId() Group by the module_id column
 * @method     ChildFreeDeliveryConditionQuery groupByAmount() Group by the amount column
 *
 * @method     ChildFreeDeliveryConditionQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildFreeDeliveryConditionQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildFreeDeliveryConditionQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildFreeDeliveryConditionQuery leftJoinArea($relationAlias = null) Adds a LEFT JOIN clause to the query using the Area relation
 * @method     ChildFreeDeliveryConditionQuery rightJoinArea($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Area relation
 * @method     ChildFreeDeliveryConditionQuery innerJoinArea($relationAlias = null) Adds a INNER JOIN clause to the query using the Area relation
 *
 * @method     ChildFreeDeliveryConditionQuery leftJoinModule($relationAlias = null) Adds a LEFT JOIN clause to the query using the Module relation
 * @method     ChildFreeDeliveryConditionQuery rightJoinModule($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Module relation
 * @method     ChildFreeDeliveryConditionQuery innerJoinModule($relationAlias = null) Adds a INNER JOIN clause to the query using the Module relation
 *
 * @method     ChildFreeDeliveryCondition findOne(ConnectionInterface $con = null) Return the first ChildFreeDeliveryCondition matching the query
 * @method     ChildFreeDeliveryCondition findOneOrCreate(ConnectionInterface $con = null) Return the first ChildFreeDeliveryCondition matching the query, or a new ChildFreeDeliveryCondition object populated from the query conditions when no match is found
 *
 * @method     ChildFreeDeliveryCondition findOneByAreaId(int $area_id) Return the first ChildFreeDeliveryCondition filtered by the area_id column
 * @method     ChildFreeDeliveryCondition findOneByModuleId(int $module_id) Return the first ChildFreeDeliveryCondition filtered by the module_id column
 * @method     ChildFreeDeliveryCondition findOneByAmount(string $amount) Return the first ChildFreeDeliveryCondition filtered by the amount column
 *
 * @method     array findByAreaId(int $area_id) Return ChildFreeDeliveryCondition objects filtered by the area_id column
 * @method     array findByModuleId(int $module_id) Return ChildFreeDeliveryCondition objects filtered by the module_id column
 * @method     array findByAmount(string $amount) Return ChildFreeDeliveryCondition objects filtered by the amount column
 *
 */
abstract class FreeDeliveryConditionQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \FreeDelivery\Model\Base\FreeDeliveryConditionQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\FreeDelivery\\Model\\FreeDeliveryCondition', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildFreeDeliveryConditionQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildFreeDeliveryConditionQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \FreeDelivery\Model\FreeDeliveryConditionQuery) {
            return $criteria;
        }
        $query = new \FreeDelivery\Model\FreeDeliveryConditionQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj = $c->findPk(array(12, 34), $con);
     * </code>
     *
     * @param array[$area_id, $module_id] $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildFreeDeliveryCondition|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = FreeDeliveryConditionTableMap::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(FreeDeliveryConditionTableMap::DATABASE_NAME);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return   ChildFreeDeliveryCondition A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT AREA_ID, MODULE_ID, AMOUNT FROM free_delivery_condition WHERE AREA_ID = :p0 AND MODULE_ID = :p1';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
            $stmt->bindValue(':p1', $key[1], PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            $obj = new ChildFreeDeliveryCondition();
            $obj->hydrate($row);
            FreeDeliveryConditionTableMap::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildFreeDeliveryCondition|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(array(12, 56), array(832, 123), array(123, 456)), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return ChildFreeDeliveryConditionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(FreeDeliveryConditionTableMap::AREA_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(FreeDeliveryConditionTableMap::MODULE_ID, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildFreeDeliveryConditionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(FreeDeliveryConditionTableMap::AREA_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(FreeDeliveryConditionTableMap::MODULE_ID, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
    }

    /**
     * Filter the query on the area_id column
     *
     * Example usage:
     * <code>
     * $query->filterByAreaId(1234); // WHERE area_id = 1234
     * $query->filterByAreaId(array(12, 34)); // WHERE area_id IN (12, 34)
     * $query->filterByAreaId(array('min' => 12)); // WHERE area_id > 12
     * </code>
     *
     * @see       filterByArea()
     *
     * @param     mixed $areaId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildFreeDeliveryConditionQuery The current query, for fluid interface
     */
    public function filterByAreaId($areaId = null, $comparison = null)
    {
        if (is_array($areaId)) {
            $useMinMax = false;
            if (isset($areaId['min'])) {
                $this->addUsingAlias(FreeDeliveryConditionTableMap::AREA_ID, $areaId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($areaId['max'])) {
                $this->addUsingAlias(FreeDeliveryConditionTableMap::AREA_ID, $areaId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FreeDeliveryConditionTableMap::AREA_ID, $areaId, $comparison);
    }

    /**
     * Filter the query on the module_id column
     *
     * Example usage:
     * <code>
     * $query->filterByModuleId(1234); // WHERE module_id = 1234
     * $query->filterByModuleId(array(12, 34)); // WHERE module_id IN (12, 34)
     * $query->filterByModuleId(array('min' => 12)); // WHERE module_id > 12
     * </code>
     *
     * @see       filterByModule()
     *
     * @param     mixed $moduleId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildFreeDeliveryConditionQuery The current query, for fluid interface
     */
    public function filterByModuleId($moduleId = null, $comparison = null)
    {
        if (is_array($moduleId)) {
            $useMinMax = false;
            if (isset($moduleId['min'])) {
                $this->addUsingAlias(FreeDeliveryConditionTableMap::MODULE_ID, $moduleId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($moduleId['max'])) {
                $this->addUsingAlias(FreeDeliveryConditionTableMap::MODULE_ID, $moduleId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FreeDeliveryConditionTableMap::MODULE_ID, $moduleId, $comparison);
    }

    /**
     * Filter the query on the amount column
     *
     * Example usage:
     * <code>
     * $query->filterByAmount(1234); // WHERE amount = 1234
     * $query->filterByAmount(array(12, 34)); // WHERE amount IN (12, 34)
     * $query->filterByAmount(array('min' => 12)); // WHERE amount > 12
     * </code>
     *
     * @param     mixed $amount The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildFreeDeliveryConditionQuery The current query, for fluid interface
     */
    public function filterByAmount($amount = null, $comparison = null)
    {
        if (is_array($amount)) {
            $useMinMax = false;
            if (isset($amount['min'])) {
                $this->addUsingAlias(FreeDeliveryConditionTableMap::AMOUNT, $amount['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($amount['max'])) {
                $this->addUsingAlias(FreeDeliveryConditionTableMap::AMOUNT, $amount['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FreeDeliveryConditionTableMap::AMOUNT, $amount, $comparison);
    }

    /**
     * Filter the query by a related \Thelia\Model\Area object
     *
     * @param \Thelia\Model\Area|ObjectCollection $area The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildFreeDeliveryConditionQuery The current query, for fluid interface
     */
    public function filterByArea($area, $comparison = null)
    {
        if ($area instanceof \Thelia\Model\Area) {
            return $this
                ->addUsingAlias(FreeDeliveryConditionTableMap::AREA_ID, $area->getId(), $comparison);
        } elseif ($area instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(FreeDeliveryConditionTableMap::AREA_ID, $area->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByArea() only accepts arguments of type \Thelia\Model\Area or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Area relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildFreeDeliveryConditionQuery The current query, for fluid interface
     */
    public function joinArea($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Area');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Area');
        }

        return $this;
    }

    /**
     * Use the Area relation Area object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Thelia\Model\AreaQuery A secondary query class using the current class as primary query
     */
    public function useAreaQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinArea($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Area', '\Thelia\Model\AreaQuery');
    }

    /**
     * Filter the query by a related \Thelia\Model\Module object
     *
     * @param \Thelia\Model\Module|ObjectCollection $module The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildFreeDeliveryConditionQuery The current query, for fluid interface
     */
    public function filterByModule($module, $comparison = null)
    {
        if ($module instanceof \Thelia\Model\Module) {
            return $this
                ->addUsingAlias(FreeDeliveryConditionTableMap::MODULE_ID, $module->getId(), $comparison);
        } elseif ($module instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(FreeDeliveryConditionTableMap::MODULE_ID, $module->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByModule() only accepts arguments of type \Thelia\Model\Module or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Module relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildFreeDeliveryConditionQuery The current query, for fluid interface
     */
    public function joinModule($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Module');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Module');
        }

        return $this;
    }

    /**
     * Use the Module relation Module object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Thelia\Model\ModuleQuery A secondary query class using the current class as primary query
     */
    public function useModuleQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinModule($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Module', '\Thelia\Model\ModuleQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildFreeDeliveryCondition $freeDeliveryCondition Object to remove from the list of results
     *
     * @return ChildFreeDeliveryConditionQuery The current query, for fluid interface
     */
    public function prune($freeDeliveryCondition = null)
    {
        if ($freeDeliveryCondition) {
            $this->addCond('pruneCond0', $this->getAliasedColName(FreeDeliveryConditionTableMap::AREA_ID), $freeDeliveryCondition->getAreaId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(FreeDeliveryConditionTableMap::MODULE_ID), $freeDeliveryCondition->getModuleId(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

    /**
     * Deletes all rows from the free_delivery_condition table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(FreeDeliveryConditionTableMap::DATABASE_NAME);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            FreeDeliveryConditionTableMap::clearInstancePool();
            FreeDeliveryConditionTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildFreeDeliveryCondition or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildFreeDeliveryCondition object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
     public function delete(ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(FreeDeliveryConditionTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(FreeDeliveryConditionTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        FreeDeliveryConditionTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            FreeDeliveryConditionTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // FreeDeliveryConditionQuery
