<?php

namespace Oro\Bundle\EntityBundle\Tests\Unit\Grid;

use Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration;
use Oro\Bundle\EntityBundle\Grid\ExtendExtension;
use Oro\Bundle\EntityConfigBundle\Config\Config;
use Oro\Bundle\EntityConfigBundle\Config\Id\FieldConfigId;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\DataGridBundle\Datasource\Orm\OrmDatasource;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;

/**
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 */
class ExtendExtensionTest extends \PHPUnit_Framework_TestCase
{
    const ENTITY_CLASS = 'Test\Entity';
    const ENTITY_NAME  = 'Test:Entity';

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $configManager;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $datagridConfig;

    /** @var ExtendExtension */
    protected $extension;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $entityConfigProvider;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $extendConfigProvider;

    protected function setUp()
    {
        $this->configManager = $this->getMockBuilder('Oro\Bundle\EntityConfigBundle\Config\ConfigManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->entityConfigProvider = $this->getConfigProviderMock();
        $this->extendConfigProvider = $this->getConfigProviderMock();

        $this->extension = new ExtendExtension($this->configManager);
    }

    public function testIsApplicable()
    {
        $this->assertFalse(
            $this->extension->isApplicable(DatagridConfiguration::create([]))
        );
        $this->assertTrue(
            $this->extension->isApplicable(DatagridConfiguration::create(['extended_entity_name' => 'entity']))
        );
    }

    public function testGetPriority()
    {
        $this->assertEquals(
            250,
            $this->extension->getPriority()
        );
    }

    public function testProcessConfigsForString()
    {
        $entityName = self::ENTITY_NAME;
        $fieldName  = 'testField';
        $fieldLabel = 'test.field.label';
        $fieldType  = 'string';

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->setExpectationForGetExtendedEntityNameByConfig(
            $em,
            $entityName,
            self::ENTITY_CLASS
        );
        $this->setExpectationForGetDynamicFields(self::ENTITY_CLASS, $fieldName, $fieldType);

        $entityFieldConfig = new Config(new FieldConfigId('entity', self::ENTITY_CLASS, $fieldName, $fieldType));
        $entityFieldConfig->set('label', $fieldLabel);
        $this->entityConfigProvider->expects($this->once())
            ->method('getConfig')
            ->with(self::ENTITY_CLASS, $fieldName)
            ->will($this->returnValue($entityFieldConfig));

        $config = DatagridConfiguration::create(['extended_entity_name' => $entityName]);
        $this->extension->processConfigs($config);
        $this->assertEquals(
            [
                'extended_entity_name' => $entityName,
                'columns'              => [
                    $fieldName => [
                        'label'         => $fieldLabel,
                        'frontend_type' => 'string'
                    ]
                ],
                'sorters'              => [
                    'columns' => [
                        $fieldName => [
                            'data_name' => $fieldName
                        ]
                    ]
                ],
                'filters'              => [
                    'columns' => [
                        $fieldName => [
                            'type'      => 'string',
                            'data_name' => $fieldName,
                            'enabled'   => false,
                            'options'   => []
                        ]
                    ]
                ],
            ],
            $config->toArray()
        );
    }

    /**
     * @dataProvider intTypeProvider
     */
    public function testProcessConfigsForInt($fieldType)
    {
        $entityName = self::ENTITY_NAME;
        $fieldName  = 'testField';
        $fieldLabel = 'test.field.label';

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->setExpectationForGetExtendedEntityNameByConfig(
            $em,
            $entityName,
            self::ENTITY_CLASS
        );
        $this->setExpectationForGetDynamicFields(self::ENTITY_CLASS, $fieldName, $fieldType);

        $entityFieldConfig = new Config(new FieldConfigId('entity', self::ENTITY_CLASS, $fieldName, $fieldType));
        $entityFieldConfig->set('label', $fieldLabel);
        $this->entityConfigProvider->expects($this->once())
            ->method('getConfig')
            ->with(self::ENTITY_CLASS, $fieldName)
            ->will($this->returnValue($entityFieldConfig));

        $config = DatagridConfiguration::create(['extended_entity_name' => $entityName]);
        $this->extension->processConfigs($config);
        $this->assertEquals(
            [
                'extended_entity_name' => $entityName,
                'columns'              => [
                    $fieldName => [
                        'label'         => $fieldLabel,
                        'frontend_type' => 'integer'
                    ]
                ],
                'sorters'              => [
                    'columns' => [
                        $fieldName => [
                            'data_name' => $fieldName
                        ]
                    ]
                ],
                'filters'              => [
                    'columns' => [
                        $fieldName => [
                            'type'      => 'number',
                            'data_name' => $fieldName,
                            'enabled'   => false,
                            'options'   => []
                        ]
                    ]
                ],
            ],
            $config->toArray()
        );
    }

    public function intTypeProvider()
    {
        return [
            ['integer'],
            ['smallint'],
            ['bigint'],
        ];
    }

    /**
     * @dataProvider floatTypeProvider
     */
    public function testProcessConfigsForFloat($fieldType)
    {
        $entityName = self::ENTITY_NAME;
        $fieldName  = 'testField';
        $fieldLabel = 'test.field.label';

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->setExpectationForGetExtendedEntityNameByConfig(
            $em,
            $entityName,
            self::ENTITY_CLASS
        );
        $this->setExpectationForGetDynamicFields(self::ENTITY_CLASS, $fieldName, $fieldType);

        $entityFieldConfig = new Config(new FieldConfigId('entity', self::ENTITY_CLASS, $fieldName, $fieldType));
        $entityFieldConfig->set('label', $fieldLabel);
        $this->entityConfigProvider->expects($this->once())
            ->method('getConfig')
            ->with(self::ENTITY_CLASS, $fieldName)
            ->will($this->returnValue($entityFieldConfig));

        $config = DatagridConfiguration::create(['extended_entity_name' => $entityName]);
        $this->extension->processConfigs($config);
        $this->assertEquals(
            [
                'extended_entity_name' => $entityName,
                'columns'              => [
                    $fieldName => [
                        'label'         => $fieldLabel,
                        'frontend_type' => 'decimal'
                    ]
                ],
                'sorters'              => [
                    'columns' => [
                        $fieldName => [
                            'data_name' => $fieldName
                        ]
                    ]
                ],
                'filters'              => [
                    'columns' => [
                        $fieldName => [
                            'type'      => 'number',
                            'data_name' => $fieldName,
                            'enabled'   => false,
                            'options'   => [
                                'data_type' => 'data_decimal'
                            ]
                        ]
                    ]
                ],
            ],
            $config->toArray()
        );
    }

    public function floatTypeProvider()
    {
        return [
            ['float'],
            ['decimal'],
        ];
    }

    public function testProcessConfigsForBoolean()
    {
        $entityName = self::ENTITY_NAME;
        $fieldName  = 'testField';
        $fieldLabel = 'test.field.label';
        $fieldType  = 'boolean';

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->setExpectationForGetExtendedEntityNameByConfig(
            $em,
            $entityName,
            self::ENTITY_CLASS
        );
        $this->setExpectationForGetDynamicFields(self::ENTITY_CLASS, $fieldName, $fieldType);

        $entityFieldConfig = new Config(new FieldConfigId('entity', self::ENTITY_CLASS, $fieldName, $fieldType));
        $entityFieldConfig->set('label', $fieldLabel);
        $this->entityConfigProvider->expects($this->once())
            ->method('getConfig')
            ->with(self::ENTITY_CLASS, $fieldName)
            ->will($this->returnValue($entityFieldConfig));

        $config = DatagridConfiguration::create(['extended_entity_name' => $entityName]);
        $this->extension->processConfigs($config);
        $this->assertEquals(
            [
                'extended_entity_name' => $entityName,
                'columns'              => [
                    $fieldName => [
                        'label'         => $fieldLabel,
                        'frontend_type' => 'boolean'
                    ]
                ],
                'sorters'              => [
                    'columns' => [
                        $fieldName => [
                            'data_name' => $fieldName
                        ]
                    ]
                ],
                'filters'              => [
                    'columns' => [
                        $fieldName => [
                            'data_name' => $fieldName,
                            'enabled'   => false,
                            'options'   => []
                        ]
                    ]
                ],
            ],
            $config->toArray()
        );
    }

    public function testProcessConfigsForDate()
    {
        $entityName = self::ENTITY_NAME;
        $fieldName  = 'testField';
        $fieldLabel = 'test.field.label';
        $fieldType  = 'date';

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->setExpectationForGetExtendedEntityNameByConfig(
            $em,
            $entityName,
            self::ENTITY_CLASS
        );
        $this->setExpectationForGetDynamicFields(self::ENTITY_CLASS, $fieldName, $fieldType);

        $entityFieldConfig = new Config(new FieldConfigId('entity', self::ENTITY_CLASS, $fieldName, $fieldType));
        $entityFieldConfig->set('label', $fieldLabel);
        $this->entityConfigProvider->expects($this->once())
            ->method('getConfig')
            ->with(self::ENTITY_CLASS, $fieldName)
            ->will($this->returnValue($entityFieldConfig));

        $config = DatagridConfiguration::create(['extended_entity_name' => $entityName]);
        $this->extension->processConfigs($config);
        $this->assertEquals(
            [
                'extended_entity_name' => $entityName,
                'columns'              => [
                    $fieldName => [
                        'label'         => $fieldLabel,
                        'frontend_type' => 'date'
                    ]
                ],
                'sorters'              => [
                    'columns' => [
                        $fieldName => [
                            'data_name' => $fieldName
                        ]
                    ]
                ],
                'filters'              => [
                    'columns' => [
                        $fieldName => [
                            'data_name' => $fieldName,
                            'enabled'   => false,
                            'options'   => []
                        ]
                    ]
                ],
            ],
            $config->toArray()
        );
    }

    public function testProcessConfigsForDateTime()
    {
        $entityName = self::ENTITY_NAME;
        $fieldName  = 'testField';
        $fieldLabel = 'test.field.label';
        $fieldType  = 'datetime';

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->setExpectationForGetExtendedEntityNameByConfig(
            $em,
            $entityName,
            self::ENTITY_CLASS
        );
        $this->setExpectationForGetDynamicFields(self::ENTITY_CLASS, $fieldName, $fieldType);

        $entityFieldConfig = new Config(new FieldConfigId('entity', self::ENTITY_CLASS, $fieldName, $fieldType));
        $entityFieldConfig->set('label', $fieldLabel);
        $this->entityConfigProvider->expects($this->once())
            ->method('getConfig')
            ->with(self::ENTITY_CLASS, $fieldName)
            ->will($this->returnValue($entityFieldConfig));

        $config = DatagridConfiguration::create(['extended_entity_name' => $entityName]);
        $this->extension->processConfigs($config);
        $this->assertEquals(
            [
                'extended_entity_name' => $entityName,
                'columns'              => [
                    $fieldName => [
                        'label'         => $fieldLabel,
                        'frontend_type' => 'datetime'
                    ]
                ],
                'sorters'              => [
                    'columns' => [
                        $fieldName => [
                            'data_name' => $fieldName
                        ]
                    ]
                ],
                'filters'              => [
                    'columns' => [
                        $fieldName => [
                            'data_name' => $fieldName,
                            'enabled'   => false,
                            'options'   => []
                        ]
                    ]
                ],
            ],
            $config->toArray()
        );
    }

    public function testProcessConfigsForMoney()
    {
        $entityName = self::ENTITY_NAME;
        $fieldName  = 'testField';
        $fieldLabel = 'test.field.label';
        $fieldType  = 'money';

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->setExpectationForGetExtendedEntityNameByConfig(
            $em,
            $entityName,
            self::ENTITY_CLASS
        );
        $this->setExpectationForGetDynamicFields(self::ENTITY_CLASS, $fieldName, $fieldType);

        $entityFieldConfig = new Config(new FieldConfigId('entity', self::ENTITY_CLASS, $fieldName, $fieldType));
        $entityFieldConfig->set('label', $fieldLabel);
        $this->entityConfigProvider->expects($this->once())
            ->method('getConfig')
            ->with(self::ENTITY_CLASS, $fieldName)
            ->will($this->returnValue($entityFieldConfig));

        $config = DatagridConfiguration::create(['extended_entity_name' => $entityName]);
        $this->extension->processConfigs($config);
        $this->assertEquals(
            [
                'extended_entity_name' => $entityName,
                'columns'              => [
                    $fieldName => [
                        'label'         => $fieldLabel,
                        'frontend_type' => 'currency'
                    ]
                ],
                'sorters'              => [
                    'columns' => [
                        $fieldName => [
                            'data_name' => $fieldName
                        ]
                    ]
                ],
                'filters'              => [
                    'columns' => [
                        $fieldName => [
                            'type'      => 'number',
                            'data_name' => $fieldName,
                            'enabled'   => false,
                            'options'   => []
                        ]
                    ]
                ],
            ],
            $config->toArray()
        );
    }

    public function testProcessConfigsForPercent()
    {
        $entityName = self::ENTITY_NAME;
        $fieldName  = 'testField';
        $fieldLabel = 'test.field.label';
        $fieldType  = 'percent';

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->setExpectationForGetExtendedEntityNameByConfig(
            $em,
            $entityName,
            self::ENTITY_CLASS
        );
        $this->setExpectationForGetDynamicFields(self::ENTITY_CLASS, $fieldName, $fieldType);

        $entityFieldConfig = new Config(new FieldConfigId('entity', self::ENTITY_CLASS, $fieldName, $fieldType));
        $entityFieldConfig->set('label', $fieldLabel);
        $this->entityConfigProvider->expects($this->once())
            ->method('getConfig')
            ->with(self::ENTITY_CLASS, $fieldName)
            ->will($this->returnValue($entityFieldConfig));

        $config = DatagridConfiguration::create(['extended_entity_name' => $entityName]);
        $this->extension->processConfigs($config);
        $this->assertEquals(
            [
                'extended_entity_name' => $entityName,
                'columns'              => [
                    $fieldName => [
                        'label'         => $fieldLabel,
                        'frontend_type' => 'percent'
                    ]
                ],
                'sorters'              => [
                    'columns' => [
                        $fieldName => [
                            'data_name' => $fieldName
                        ]
                    ]
                ],
                'filters'              => [
                    'columns' => [
                        $fieldName => [
                            'type'      => 'percent',
                            'data_name' => $fieldName,
                            'enabled'   => false,
                            'options'   => []
                        ]
                    ]
                ],
            ],
            $config->toArray()
        );
    }

    public function testProcessConfigsForEnum()
    {
        $entityName = self::ENTITY_NAME;
        $fieldName  = 'testField';
        $fieldLabel = 'test.field.label';
        $fieldType  = 'enum';

        $targetEntity = 'Test\TargetEntity';

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->setExpectationForGetExtendedEntityNameByConfig(
            $em,
            $entityName,
            self::ENTITY_CLASS
        );
        $this->setExpectationForGetDynamicFields(self::ENTITY_CLASS, $fieldName, $fieldType);

        $entityFieldConfig = new Config(new FieldConfigId('entity', self::ENTITY_CLASS, $fieldName, $fieldType));
        $entityFieldConfig->set('label', $fieldLabel);
        $this->entityConfigProvider->expects($this->once())
            ->method('getConfig')
            ->with(self::ENTITY_CLASS, $fieldName)
            ->will($this->returnValue($entityFieldConfig));

        $extendFieldConfig = new Config(new FieldConfigId('extend', self::ENTITY_CLASS, $fieldName, $fieldType));
        $extendFieldConfig->set('target_entity', $targetEntity);
        $this->extendConfigProvider->expects($this->once())
            ->method('getConfig')
            ->with(self::ENTITY_CLASS, $fieldName)
            ->will($this->returnValue($extendFieldConfig));

        $config = DatagridConfiguration::create(['extended_entity_name' => $entityName]);
        $this->extension->processConfigs($config);
        $this->assertEquals(
            [
                'extended_entity_name' => $entityName,
                'columns'              => [
                    $fieldName => [
                        'label'         => $fieldLabel,
                        'frontend_type' => 'string'
                    ]
                ],
                'sorters'              => [
                    'columns' => [
                        $fieldName => [
                            'data_name' => $fieldName
                        ]
                    ]
                ],
                'filters'              => [
                    'columns' => [
                        $fieldName => [
                            'type'       => 'enum',
                            'null_value' => ':empty:',
                            'data_name'  => $fieldName,
                            'enabled'    => false,
                            'options'    => [
                                'class' => $targetEntity
                            ]
                        ]
                    ]
                ],
            ],
            $config->toArray()
        );
    }

    public function testProcessConfigsForMultiEnum()
    {
        $entityName = self::ENTITY_NAME;
        $fieldName  = 'testField';
        $fieldLabel = 'test.field.label';
        $fieldType  = 'multiEnum';

        $targetEntity = 'Test\TargetEntity';
        $twigTemplate = 'OroEntityExtendBundle:Datagrid:Property/multiEnum.html.twig';

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->setExpectationForGetExtendedEntityNameByConfig(
            $em,
            $entityName,
            self::ENTITY_CLASS
        );
        $this->setExpectationForGetDynamicFields(self::ENTITY_CLASS, $fieldName, $fieldType);

        $entityFieldConfig = new Config(new FieldConfigId('entity', self::ENTITY_CLASS, $fieldName, $fieldType));
        $entityFieldConfig->set('label', $fieldLabel);
        $this->entityConfigProvider->expects($this->once())
            ->method('getConfig')
            ->with(self::ENTITY_CLASS, $fieldName)
            ->will($this->returnValue($entityFieldConfig));

        $extendFieldConfig = new Config(new FieldConfigId('extend', self::ENTITY_CLASS, $fieldName, $fieldType));
        $extendFieldConfig->set('target_entity', $targetEntity);
        $this->extendConfigProvider->expects($this->once())
            ->method('getConfig')
            ->with(self::ENTITY_CLASS, $fieldName)
            ->will($this->returnValue($extendFieldConfig));

        $config = DatagridConfiguration::create(['extended_entity_name' => $entityName]);
        $this->extension->processConfigs($config);
        $this->assertEquals(
            [
                'extended_entity_name' => $entityName,
                'columns'              => [
                    $fieldName => [
                        'label'         => $fieldLabel,
                        'frontend_type' => 'html',
                        'template'      => $twigTemplate,
                        'type'          => 'twig',
                        'context'       => [
                            'entity_class' => $targetEntity
                        ]
                    ]
                ],
                'sorters'              => [
                    'columns' => [
                        $fieldName => [
                            'data_name' => $fieldName
                        ]
                    ]
                ],
                'filters'              => [
                    'columns' => [
                        $fieldName => [
                            'type'       => 'multi_enum',
                            'null_value' => ':empty:',
                            'data_name'  => $fieldName,
                            'enabled'    => false,
                            'options'    => [
                                'class' => $targetEntity
                            ]
                        ]
                    ]
                ],
            ],
            $config->toArray()
        );
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Class 'fake class' does not exist
     */
    public function testVisitDatasourceThrowAnException()
    {
        $entityName = 'fake class';

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $em->expects($this->once())
            ->method('getClassMetadata')
            ->with($entityName)
            ->will($this->throwException(new \Exception('Class \'' . $entityName . '\' does not exist')));

        $this->configManager->expects($this->once())
            ->method('getEntityManager')
            ->will($this->returnValue($em));

        $datasource = new OrmDatasource($em, $this->getEventDispatcherMock());

        $this->extension->visitDatasource(
            DatagridConfiguration::create(['extended_entity_name' => $entityName]),
            $datasource
        );
    }

    public function testVisitDatasourceNoFields()
    {
        $entityName = self::ENTITY_NAME;

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->setExpectationForGetExtendedEntityNameByConfig(
            $em,
            $entityName,
            self::ENTITY_CLASS
        );

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $qb->expects($this->never())
            ->method('getDQLPart');

        $datasource = new OrmDatasource($em, $this->getEventDispatcherMock());
        $datasource->setQueryBuilder($qb);

        $config = DatagridConfiguration::create(['extended_entity_name' => $entityName]);
        $this->extension->visitDatasource($config, $datasource);
    }

    public function testVisitDatasourceForNotConfigurableEntity()
    {
        $entityName = self::ENTITY_NAME;

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->setExpectationForGetExtendedEntityNameByConfig(
            $em,
            $entityName,
            self::ENTITY_CLASS
        );

        $this->configManager->expects($this->once())
            ->method('hasConfig')
            ->with(self::ENTITY_CLASS)
            ->will($this->returnValue(false));

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $qb->expects($this->never())
            ->method('getDQLPart');

        $datasource = new OrmDatasource($em, $this->getEventDispatcherMock());
        $datasource->setQueryBuilder($qb);

        $config = DatagridConfiguration::create(['extended_entity_name' => $entityName]);
        $this->extension->visitDatasource($config, $datasource);
    }

    public function testVisitDatasource()
    {
        $entityName = self::ENTITY_NAME;
        $fieldName  = 'testField';
        $fieldType  = 'string';
        $alias      = 'c';

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->setExpectationForGetExtendedEntityNameByConfig(
            $em,
            $entityName,
            self::ENTITY_CLASS,
            2
        );
        $this->setExpectationForGetDynamicFields(self::ENTITY_CLASS, $fieldName, $fieldType);

        $from = $this->getMockBuilder('Doctrine\ORM\Query\Expr\From')
            ->disableOriginalConstructor()
            ->getMock();
        $from->expects($this->once())
            ->method('getAlias')
            ->will($this->returnValue($alias));
        $from->expects($this->once())
            ->method('getFrom')
            ->will($this->returnValue($entityName));

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $qb->expects($this->once())
            ->method('getDQLPart')
            ->with('from')
            ->will($this->returnValue([$from]));
        $qb->expects($this->once())
            ->method('addSelect')
            ->with($alias . '.' . $fieldName)
            ->will($this->returnSelf());

        $datasource = new OrmDatasource($em, $this->getEventDispatcherMock());
        $datasource->setQueryBuilder($qb);

        $config = DatagridConfiguration::create(['extended_entity_name' => $entityName]);
        $this->extension->visitDatasource($config, $datasource);
        $this->assertEquals(
            [
                'extended_entity_name' => $entityName,
                'columns'              => [
                    $fieldName => [
                        'data_name' => $fieldName
                    ]
                ],
                'sorters'              => [
                    'columns' => [
                        $fieldName => [
                            'data_name' => $alias . '.' . $fieldName
                        ]
                    ]
                ],
                'filters'              => [
                    'columns' => [
                        $fieldName => [
                            'data_name' => $alias . '.' . $fieldName
                        ]
                    ]
                ],
            ],
            $config->toArray()
        );
    }

    public function testVisitDatasourceForEnum()
    {
        $entityName = self::ENTITY_NAME;
        $fieldName  = 'testField';
        $fieldType  = 'enum';
        $alias      = 'c';

        $targetFieldName = 'testRel';
        $relAlias        = 'auto_rel_1';

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->setExpectationForGetExtendedEntityNameByConfig(
            $em,
            $entityName,
            self::ENTITY_CLASS,
            2
        );
        $this->setExpectationForGetDynamicFields(self::ENTITY_CLASS, $fieldName, $fieldType);

        $extendFieldConfig = new Config(new FieldConfigId('extend', self::ENTITY_CLASS, $fieldName, $fieldType));
        $extendFieldConfig->set('target_field', $targetFieldName);
        $this->extendConfigProvider->expects($this->once())
            ->method('getConfig')
            ->with(self::ENTITY_CLASS, $fieldName)
            ->will($this->returnValue($extendFieldConfig));

        $from = $this->getMockBuilder('Doctrine\ORM\Query\Expr\From')
            ->disableOriginalConstructor()
            ->getMock();
        $from->expects($this->once())
            ->method('getAlias')
            ->will($this->returnValue($alias));
        $from->expects($this->once())
            ->method('getFrom')
            ->will($this->returnValue($entityName));

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $qb->expects($this->once())
            ->method('getDQLPart')
            ->with('from')
            ->will($this->returnValue([$from]));
        $qb->expects($this->once())
            ->method('leftJoin')
            ->with($alias . '.' . $fieldName, $relAlias)
            ->will($this->returnSelf());
        $qb->expects($this->once())
            ->method('addSelect')
            ->with($relAlias . '.' . $targetFieldName . ' as ' . $fieldName)
            ->will($this->returnSelf());

        $datasource = new OrmDatasource($em, $this->getEventDispatcherMock());
        $datasource->setQueryBuilder($qb);

        $config = DatagridConfiguration::create(['extended_entity_name' => $entityName]);
        $this->extension->visitDatasource($config, $datasource);
        $this->assertEquals(
            [
                'extended_entity_name' => $entityName,
                'columns'              => [
                    $fieldName => [
                        'data_name' => $fieldName
                    ]
                ],
                'sorters'              => [
                    'columns' => [
                        $fieldName => [
                            'data_name' => $relAlias . '.' . $targetFieldName
                        ]
                    ]
                ],
                'filters'              => [
                    'columns' => [
                        $fieldName => [
                            'data_name' => $alias . '.' . $fieldName
                        ]
                    ]
                ],
            ],
            $config->toArray()
        );
    }

    public function testVisitDatasourceForMultiEnum()
    {
        $entityName = self::ENTITY_NAME;
        $fieldName  = 'testField';
        $fieldType  = 'multiEnum';
        $alias      = 'c';

        $snapshotFieldName = ExtendHelper::getMultipleEnumSnapshotFieldName($fieldName);

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->setExpectationForGetExtendedEntityNameByConfig(
            $em,
            $entityName,
            self::ENTITY_CLASS,
            2
        );
        $this->setExpectationForGetDynamicFields(self::ENTITY_CLASS, $fieldName, $fieldType);

        $from = $this->getMockBuilder('Doctrine\ORM\Query\Expr\From')
            ->disableOriginalConstructor()
            ->getMock();
        $from->expects($this->once())
            ->method('getAlias')
            ->will($this->returnValue($alias));
        $from->expects($this->once())
            ->method('getFrom')
            ->will($this->returnValue($entityName));

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $qb->expects($this->once())
            ->method('getDQLPart')
            ->with('from')
            ->will($this->returnValue([$from]));
        $qb->expects($this->once())
            ->method('addSelect')
            ->with($alias . '.' . $snapshotFieldName)
            ->will($this->returnSelf());

        $datasource = new OrmDatasource($em, $this->getEventDispatcherMock());
        $datasource->setQueryBuilder($qb);

        $config = DatagridConfiguration::create(['extended_entity_name' => $entityName]);
        $this->extension->visitDatasource($config, $datasource);
        $this->assertEquals(
            [
                'extended_entity_name' => $entityName,
                'columns'              => [
                    $fieldName => [
                        'data_name' => $snapshotFieldName
                    ]
                ],
                'sorters'              => [
                    'columns' => [
                        $fieldName => [
                            'data_name' => $alias . '.' . $snapshotFieldName
                        ]
                    ]
                ],
                'filters'              => [
                    'columns' => [
                        $fieldName => [
                            'data_name' => $alias . '.' . $fieldName
                        ]
                    ]
                ],
            ],
            $config->toArray()
        );
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getEventDispatcherMock()
    {
        return $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getConfigProviderMock()
    {
        return $this->getMockBuilder('Oro\Bundle\EntityConfigBundle\Provider\ConfigProvider')
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function setExpectationForGetExtendedEntityNameByConfig(
        \PHPUnit_Framework_MockObject_MockObject $em,
        $entityName,
        $resultEntityName,
        $count = 1
    ) {
        $metadata = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->getMock();
        $metadata->expects($this->exactly($count))
            ->method('getName')
            ->will($this->returnValue($resultEntityName));
        $em->expects($this->exactly($count))
            ->method('getClassMetadata')
            ->with($entityName)
            ->will($this->returnValue($metadata));
        $this->configManager->expects($this->exactly($count))
            ->method('getEntityManager')
            ->will($this->returnValue($em));
    }

    protected function setExpectationForGetDynamicFields($className, $fieldName, $fieldType)
    {
        $fieldId = new FieldConfigId('entity', $className, $fieldName, $fieldType);

        $extendConfig = new Config(new FieldConfigId('extend', $className, $fieldName, $fieldType));
        $extendConfig->set('owner', ExtendScope::OWNER_CUSTOM);
        $extendConfig->set('state', ExtendScope::STATE_ACTIVE);
        $extendConfig->set('is_deleted', false);

        $datagridConfig = new Config(new FieldConfigId('datagrid', $className, $fieldName, $fieldType));
        $datagridConfig->set('is_visible', true);

        $this->configManager->expects($this->once())
            ->method('hasConfig')
            ->with($className)
            ->will($this->returnValue(true));

        $datagridConfigProvider = $this->getConfigProviderMock();
        $this->configManager->expects($this->any())
            ->method('getProvider')
            ->will(
                $this->returnValueMap(
                    [
                        ['entity', $this->entityConfigProvider],
                        ['extend', $this->extendConfigProvider],
                        ['datagrid', $datagridConfigProvider],
                    ]
                )
            );

        $this->entityConfigProvider->expects($this->once())
            ->method('getIds')
            ->with($className)
            ->will($this->returnValue([$fieldId]));
        $this->extendConfigProvider->expects($this->once())
            ->method('getConfigById')
            ->with($this->identicalTo($fieldId))
            ->will($this->returnValue($extendConfig));
        $datagridConfigProvider->expects($this->once())
            ->method('getConfigById')
            ->with($this->identicalTo($fieldId))
            ->will($this->returnValue($datagridConfig));
    }
}
