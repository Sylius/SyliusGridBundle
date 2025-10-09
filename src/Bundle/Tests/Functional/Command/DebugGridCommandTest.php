<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Functional\Command;

use App\BoardGameBlog\Infrastructure\Sylius\Grid\BoardGameGrid;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class DebugGridCommandTest extends KernelTestCase
{
    public function testListGridDefinitions(): void
    {
        $tester = new CommandTester((new Application(self::bootKernel(['environment' => 'test_grids_with_php_config'])))->find('sylius:debug:grid'));

        $tester->setInputs(['app_author']);
        $tester->execute([]);

        $display = $tester->getDisplay();

        $this->assertStringContainsString('Which grid do you want to debug?', $display);
        $this->assertStringContainsString(BoardGameGrid::class, $display);
        $this->assertStringContainsString('app_author', $display);
    }

    public function testDebugGridDefinition(): void
    {
        $tester = new CommandTester((new Application(self::bootKernel(['environment' => 'test_grids_with_php_config'])))->find('sylius:debug:grid'));

        $tester->execute([
            'grid' => 'app_author',
        ]);

        $display = $tester->getDisplay();

        $this->assertEquals(
            <<<TXT

            Definition of "app_author" Grid
            ===============================
            
             --------------------- ------------------------------------------------------------- 
              Option                Value                                                        
             --------------------- ------------------------------------------------------------- 
              code                  "app_author"                                                 
              driver                "doctrine/orm"                                               
              driverConfiguration   [                                                            
                                      "class" => "App\Entity\Author"                             
                                    ]                                                            
              provider              null                                                         
              sorting               [                                                            
                                      "name" => "asc"                                            
                                    ]                                                            
              limits                [                                                            
                                      10,                                                        
                                      5,                                                         
                                      15,                                                        
                                      100                                                        
                                    ]                                                            
              fields                [                                                            
                                      "id" => Sylius\Component\Grid\Definition\Field {           
                                        -name: "id"                                              
                                        -type: "callable"                                        
                                        -path: "id"                                              
                                        -label: "id"                                             
                                        -enabled: true                                           
                                        -sortable: "id"                                          
                                        -options: [                                              
                                          "callable" => [                                        
                                            "App\Helper\GridHelper",                             
                                            "addHashPrefix"                                      
                                          ],                                                     
                                          "htmlspecialchars" => true                             
                                        ]                                                        
                                        -position: 100                                           
                                      },                                                         
                                      "name" => Sylius\Component\Grid\Definition\Field {         
                                        -name: "name"                                            
                                        -type: "string"                                          
                                        -path: "name"                                            
                                        -label: "Name"                                           
                                        -enabled: true                                           
                                        -sortable: "name"                                        
                                        -options: []                                             
                                        -position: 100                                           
                                      },                                                         
                                      "nationality" => Sylius\Component\Grid\Definition\Field {  
                                        -name: "nationality"                                     
                                        -type: "callable"                                        
                                        -path: "nationality"                                     
                                        -label: "Nationality"                                    
                                        -enabled: true                                           
                                        -sortable: "nationality.name"                            
                                        -options: [                                              
                                          "service" => "App\Helper\GridHelper",                  
                                          "htmlspecialchars" => true                             
                                        ]                                                        
                                        -position: 100                                           
                                      }                                                          
                                    ]                                                            
              filters               [                                                            
                                      "name" => Sylius\Component\Grid\Definition\Filter {        
                                        -name: "name"                                            
                                        -type: "string"                                          
                                        -label: "name"                                           
                                        -enabled: true                                           
                                        -template: null                                          
                                        -options: []                                             
                                        -formOptions: []                                         
                                        -criteria: null                                          
                                        -position: 100                                           
                                      }                                                          
                                    ]                                                            
              actionGroups          []                                                           
             --------------------- ------------------------------------------------------------- 
            
            
            TXT
            ,
            $display,
        );
    }
}
