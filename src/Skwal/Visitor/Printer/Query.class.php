<?php
namespace Skwal\Visitor\Printer
{

    class Query implements \Skwal\Visitor\Query
    {

        /**
         *
         * @var \SplStack
         */
        private $queryStack = null;

        /**
         *
         * @var \Skwal\Visitor\Printer\Expression
         */
        private $expressionVisitor;

        /**
         *
         * @var \Skwal\Visitor\Printer\Correlatable
         */
        private $correlationVisitor;

        /**
         *
         * @var \Skwal\Visitor\Printer\Predicate
         */
        private $predicateVisitor;

        public function __construct()
        {
            $this->queryStack = new \SplStack();
            $this->expressionVisitor = $this->getExpressionVisitor();
            $this->correlationVisitor = $this->getCorrelationVisitor();
            $this->predicateVisitor  = $this->getPredicateVisitor();
        }

        /**
         *
         * @return \Skwal\Visitor\Printer\Expression
         */
        private function getExpressionVisitor()
        {
            $visitor = new Expression();

            return $visitor;
        }

        /**
         *
         * @return \Skwal\Visitor\Printer\Table
         */
        private function getCorrelationVisitor()
        {
            $visitor = new Table();

            $visitor->setQueryVisitor($this);

            return $visitor;
        }

        private function getPredicateVisitor()
        {
            $visitor = new Predicate();

            return $visitor;
        }

        public function getQueryCommand(\Skwal\Query $query)
        {
            $this->visit($query);

            return $this->queryStack->pop();
        }

        public function visit(\Skwal\Query $query)
        {
            $query->acceptQueryVisitor($this);
        }

        public function visitExpression(\Skwal\AliasExpression $expression)
        {
            $this->expressionVisitor->visit($expression);
        }

        public function visitCorrelatedReference(\Skwal\CorrelatableReference $reference)
        {
            $this->correlationVisitor->visit($reference);
        }

        public function visitSelect(\Skwal\SelectQuery $query)
        {
            $this->expressionVisitor->useAliases(true);

            $command = 'SELECT';
            $fromNames = array();

            foreach ($query->getColumns() as $column) {
                $fromNames[] = $this->expressionVisitor->printExpression($column);
            }

            $command .= ' ' . implode(', ', $fromNames);
            $command .= ' FROM ' . $this->correlationVisitor->getFromStatement($query);

            if ($query->getCondition() != null) {
                $command .= ' WHERE ' . $this->predicateVisitor->getPredicateStatement($query->getCondition());
            }

            $this->queryStack->push($command);
        }

        public function visitUpdate()
        {
            throw new RuntimeException('Not implemented');
        }

        public function visitDelete()
        {
            throw new RuntimeException('Not implemented');
        }

        public function visitInsert()
        {
            throw new RuntimeException('Not implemented');
        }
    }
}