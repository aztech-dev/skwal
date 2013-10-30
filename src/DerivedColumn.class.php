<?php
namespace Skwal
{

    class DerivedColumn implements AliasExpression
    {

        private $columnName;

        private $alias;

        private $correlatedParent;

        public function __construct($columnName, $alias = '')
        {
            $this->columnName = $columnName;
            $this->alias = $alias;
        }

        public function getValue()
        {
            return $this->columnName;
        }

        public function getAlias()
        {
            return $this->alias;
        }

        public function setTable(CorrelatableReference $reference = null)
        {
            // Not using cloning to avoid unnecessary cloning of the attached
            // correlated reference.
            $clone = new self($this->columnName, $this->alias);

            $clone->correlatedParent = $reference;

            return $clone;
        }

        public function __clone()
        {
            $this->correlatedParent = clone $this->correlatedParent;
        }

        /**
         * (non-PHPdoc)
         *
         * @see Skwal_AliasExpression::accept()
         */
        public function acceptExpressionVisitor(\Skwal\Visitor\Expression $visitor)
        {
            return $visitor->visitColumn($this);
        }
    }
}