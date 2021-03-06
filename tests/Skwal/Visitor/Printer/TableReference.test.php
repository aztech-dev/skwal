<?php
namespace Aztech\Skwal\Tests\Visitor\Printer
{

    use Aztech\Skwal\Table;
    use Aztech\Skwal\Visitor\Printer\TableReference;
				
    class TableReferenceTest extends \PHPUnit_Framework_TestCase
    {

        public function testVisitDispatchesCallToVisitable()
        {
            $visitor = new \Aztech\Skwal\Visitor\Printer\TableReference();

            $reference = $this->getMock('\Aztech\Skwal\CorrelatableReference');

            $reference->expects($this->once())
                ->method('acceptTableVisitor')
                ->with($this->equalTo($visitor));

            $visitor->visit($reference);
        }

        public function testVisitTableGeneratesCorrectString()
        {
            $table = new Table('dummy', 'alias');
            $visitor = new TableReference();

            $this->assertEquals('dummy AS alias', $visitor->printTableStatement($table));
        }

        public function testVisitQueryGeneratesCorrectString()
        {
            $query = $this->getMock('\Aztech\Skwal\Query\Select');
            $query->expects($this->any())
                ->method('getCorrelationName')
                ->will($this->returnValue('correlation'));

            $queryVisitor = $this->getMock('\Aztech\Skwal\Visitor\Printer\Query');
            $queryVisitor->expects($this->any())
                ->method($this->anything())
                ->will($this->returnValue('nested query'));

            $visitor = new TableReference();
            $visitor->setQueryVisitor($queryVisitor);

            $visitor->visitQuery($query);

            $this->assertEquals('(nested query) AS correlation', $visitor->getLastStatement());
        }
    }
}