<?php
declare(strict_types=1);

namespace test\labo86\temple_core;

use labo86\temple_core\TemplateFiller;
use Exception;
use PHPUnit\Framework\TestCase;

class TemplateFiller2Test extends TestCase
{

    /**
     * @var false|string
     */
    private $base_folder;

    public function setUp() : void {
        $this->base_folder = tempnam(__DIR__, 'demo_phar');

        unlink($this->base_folder);
        mkdir($this->base_folder);
    }

    public function tearDown() : void {
        exec(sprintf('rm -rf %s', escapeshellarg($this->base_folder)));
    }



    public function fillTemplatePathProvider()
    {
        return [
            ["nested/.hidden"],
            [".hidden"],
            ["something"]
        ];
    }

    /**
     * @dataProvider fillTemplatePathProvider
     * @param string $file
     * @throws Exception
     */
    public function testFillTemplatePath(string $file)
    {
        chdir($this->base_folder);
        $folder = $this->base_folder . '/input/' . dirname($file);
        mkdir($folder, 0777, true);
        file_put_contents('input/' . $file, "content");

        $template = new TemplateFiller(['tpl_company_tpl' => "company", 'tpl_project_tpl' => "project"]);
        $template->fillTemplate( 'input',  'output');
        $this->assertFileEqualsString( "content", 'output/' . $file);
    }

    /**
     * @dataProvider fillTemplatePathProvider
     * @param string $file
     * @throws Exception
     */
    public function testFillTemplatePathReplace(string $file)
    {
        chdir($this->base_folder);
        $folder = $this->base_folder . '/input/' . dirname($file);
        mkdir($folder, 0777, true);
        file_put_contents('input/' . $file, "tpl_company_tpl_tpl_project_tpl");

        $template = new TemplateFiller(['tpl_company_tpl' => "company", 'tpl_project_tpl' => "project"]);
        $template->fillTemplate( 'input',  'output');
        $this->assertFileEqualsString( "company_project", 'output/' . $file);
    }

    public function assertFileEqualsString(string $expected, string $filename, string $message = "") {
        $this->assertFileExists( $filename, $message);
        $this->assertEquals($expected, file_get_contents($filename), $message);
    }


}
