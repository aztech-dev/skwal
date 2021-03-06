<?php
namespace Aztech\Skwal\Tests\Condition
{

    use Aztech\Skwal\Condition\AbstractPredicate;

    /**
     * @author thibaud
     *
     */
    class AbstractPredicateTest extends \PHPUnit_Framework_TestCase
    {

        protected $predicate;

        protected function setUp()
        {
            $this->predicate = $this->getMockForAbstractClass('\Aztech\Skwal\Condition\AbstractPredicate');
        }

        /**
         * @testdox Calling bAnd(...) returns new AndPredicate with original and bound predicates.
         */
        public function testAndBindingReturnsPredicateWithBothConditionsAndCorrectOperator()
        {
            $other = $this->getMock('\Aztech\Skwal\Condition\Predicate');

            $andPredicate = $this->predicate->bAnd($other);

            $this->assertInstanceOf('\Aztech\Skwal\Condition\AndPredicate', $andPredicate);
            $this->assertSame($andPredicate->getFirstPredicate(), $this->predicate);
            $this->assertSame($andPredicate->getSecondPredicate(), $other);
        }

        /**
         * @testdox Calling bOr(...) returns new OrPredicate with original and bound predicates.
         */
        public function testOrBindingReturnsPredicateWithBothConditionsAndCorrectOperator()
        {
            $other = $this->getMock('\Aztech\Skwal\Condition\Predicate');

            $andPredicate = $this->predicate->bOr($other);

            $this->assertInstanceOf('\Aztech\Skwal\Condition\OrPredicate', $andPredicate);
            $this->assertSame($andPredicate->getFirstPredicate(), $this->predicate);
            $this->assertSame($andPredicate->getSecondPredicate(), $other);
        }
    }
}