<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ComprobantesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ComprobantesTable Test Case
 */
class ComprobantesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\ComprobantesTable
     */
    public $Comprobantes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.comprobantes',
        'app.ventas'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Comprobantes') ? [] : ['className' => ComprobantesTable::class];
        $this->Comprobantes = TableRegistry::getTableLocator()->get('Comprobantes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Comprobantes);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
