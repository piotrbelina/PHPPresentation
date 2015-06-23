<?php
/**
 * This file is part of PHPPowerPoint - A pure PHP library for reading and writing
 * presentations documents.
 *
 * PHPPowerPoint is free software distributed under the terms of the GNU Lesser
 * General Public License version 3 as published by the Free Software Foundation.
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code. For the full list of
 * contributors, visit https://github.com/PHPOffice/PHPPowerPoint/contributors.
 *
 * @copyright   2009-2014 PHPPowerPoint contributors
 * @license     http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 * @link        https://github.com/PHPOffice/PHPPowerPoint
 */

namespace PhpOffice\PhpPowerpoint\Tests\Writer\ODPresentation;

use PhpOffice\PhpPowerpoint\PhpPowerpoint;
use PhpOffice\PhpPowerpoint\Shape\Chart\Series;
use PhpOffice\PhpPowerpoint\Shape\Chart\Type\Bar3D;
use PhpOffice\PhpPowerpoint\Shape\Chart\Type\Line;
use PhpOffice\PhpPowerpoint\Shape\Chart\Type\Pie;
use PhpOffice\PhpPowerpoint\Shape\Chart\Type\Pie3D;
use PhpOffice\PhpPowerpoint\Shape\Chart\Type\Scatter;
use PhpOffice\PhpPowerpoint\Style\Color;
use PhpOffice\PhpPowerpoint\Style\Fill;
use PhpOffice\PhpPowerpoint\Writer\ODPresentation;
use PhpOffice\PhpPowerpoint\Tests\TestHelperDOCX;

/**
 * Test class for PhpOffice\PhpPowerpoint\Writer\ODPresentation\Manifest
 *
 * @coversDefaultClass PhpOffice\PhpPowerpoint\Writer\ODPresentation\Manifest
 */
class ChartsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Executed before each method of the class
     */
    public function tearDown()
    {
        TestHelperDOCX::clear();
    }
    
    /**
     * @expectedException \Exception
     * @expectedExceptionMessage The chart type provided could not be rendered.
     */
    public function testNoChart()
    {
        $oStub = $this->getMockForAbstractClass('PhpOffice\PhpPowerpoint\Shape\Chart\Type\AbstractType');
        
        $phpPowerPoint = new PhpPowerpoint();
        $oSlide = $phpPowerPoint->getActiveSlide();
        $oChart = $oSlide->createChartShape();
        $oChart->getPlotArea()->setType($oStub);
        
        TestHelperDOCX::getDocument($phpPowerPoint, 'ODPresentation');
    }

    public function testChartBar3D()
    {
        $oSeries = new Series('Series', array('Jan' => 1, 'Feb' => 5, 'Mar' => 2));
        $oSeries->setShowSeriesName(true);
        $oSeries->getDataPointFill(0)->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FF4672A8'));
        $oSeries->getDataPointFill(1)->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FFAB4744'));
        $oSeries->getDataPointFill(2)->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FF8AA64F'));

        $oBar3D = new Bar3D();
        $oBar3D->addSeries($oSeries);
        
        $phpPowerPoint = new PhpPowerpoint();
        $oSlide = $phpPowerPoint->getActiveSlide();
        $oChart = $oSlide->createChartShape();
        $oChart->getPlotArea()->setType($oBar3D);
        
        $pres = TestHelperDOCX::getDocument($phpPowerPoint, 'ODPresentation');
        
        $element = '/office:document-content/office:body/office:chart/chart:chart';
        $this->assertTrue($pres->elementExists($element, 'Object 1/content.xml'));
        $this->assertEquals('chart:bar', $pres->getElementAttribute($element, 'chart:class', 'Object 1/content.xml'));
        
        $element = '/office:document-content/office:body/office:chart/chart:chart/chart:plot-area/chart:series/chart:data-point';
        $this->assertTrue($pres->elementExists($element, 'Object 1/content.xml'));
        
        $element = '/office:document-content/office:automatic-styles/style:style[@style:name=\'stylePlotArea\']/style:chart-properties';
        $this->assertTrue($pres->elementExists($element, 'Object 1/content.xml'));
        $this->assertEquals('false', $pres->getElementAttribute($element, 'chart:vertical', 'Object 1/content.xml'));
        $this->assertTrue($pres->attributeElementExists($element, 'chart:three-dimensional', 'Object 1/content.xml'));
        $this->assertEquals('true', $pres->getElementAttribute($element, 'chart:three-dimensional', 'Object 1/content.xml'));
        $this->assertTrue($pres->attributeElementExists($element, 'chart:right-angled-axes', 'Object 1/content.xml'));
        $this->assertEquals('true', $pres->getElementAttribute($element, 'chart:right-angled-axes', 'Object 1/content.xml'));
    }

    public function testChartBar3DHorizontal()
    {
        $oSeries = new Series('Series', array('Jan' => 1, 'Feb' => 5, 'Mar' => 2));
        $oSeries->setShowSeriesName(true);
        $oSeries->getDataPointFill(0)->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FF4672A8'));
        $oSeries->getDataPointFill(1)->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FFAB4744'));
        $oSeries->getDataPointFill(2)->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FF8AA64F'));

        $oBar3D = new Bar3D();
        $oBar3D->setBarDirection(Bar3D::DIRECTION_HORIZONTAL);
        $oBar3D->addSeries($oSeries);
        
        $phpPowerPoint = new PhpPowerpoint();
        $oSlide = $phpPowerPoint->getActiveSlide();
        $oChart = $oSlide->createChartShape();
        $oChart->getPlotArea()->setType($oBar3D);
        
        $pres = TestHelperDOCX::getDocument($phpPowerPoint, 'ODPresentation');
        
        $element = '/office:document-content/office:body/office:chart/chart:chart';
        $this->assertTrue($pres->elementExists($element, 'Object 1/content.xml'));
        $this->assertEquals('chart:bar', $pres->getElementAttribute($element, 'chart:class', 'Object 1/content.xml'));
        
        $element = '/office:document-content/office:automatic-styles/style:style[@style:name=\'stylePlotArea\']/style:chart-properties';
        $this->assertTrue($pres->elementExists($element, 'Object 1/content.xml'));
        $this->assertEquals('true', $pres->getElementAttribute($element, 'chart:vertical', 'Object 1/content.xml'));
        $this->assertTrue($pres->attributeElementExists($element, 'chart:three-dimensional', 'Object 1/content.xml'));
        $this->assertEquals('true', $pres->getElementAttribute($element, 'chart:three-dimensional', 'Object 1/content.xml'));
        $this->assertTrue($pres->attributeElementExists($element, 'chart:right-angled-axes', 'Object 1/content.xml'));
        $this->assertEquals('true', $pres->getElementAttribute($element, 'chart:right-angled-axes', 'Object 1/content.xml'));
    }
    
    public function testChartLine()
    {
        $oSeries = new Series('Series', array('Jan' => 1, 'Feb' => 5, 'Mar' => 2));
        $oSeries->setShowSeriesName(true);
    
        $oLine = new Line();
        $oLine->addSeries($oSeries);
    
        $phpPowerPoint = new PhpPowerpoint();
        $oSlide = $phpPowerPoint->getActiveSlide();
        $oChart = $oSlide->createChartShape();
        $oChart->getPlotArea()->setType($oLine);
    
        $pres = TestHelperDOCX::getDocument($phpPowerPoint, 'ODPresentation');
    
        $element = '/office:document-content/office:body/office:chart/chart:chart';
        $this->assertTrue($pres->elementExists($element, 'Object 1/content.xml'));
        $this->assertEquals('chart:line', $pres->getElementAttribute($element, 'chart:class', 'Object 1/content.xml'));
        $element = '/office:document-content/office:automatic-styles/style:style[@style:name=\'styleAxisX\']/style:chart-properties';
        $this->assertTrue($pres->elementExists($element, 'Object 1/content.xml'));
        $this->assertEquals('false', $pres->getElementAttribute($element, 'chart:tick-marks-major-inner', 'Object 1/content.xml'));
        $this->assertEquals('false', $pres->getElementAttribute($element, 'chart:tick-marks-major-outer', 'Object 1/content.xml'));
        $element = '/office:document-content/office:automatic-styles/style:style[@style:name=\'styleAxisX\']/style:graphic-properties';
        $this->assertTrue($pres->elementExists($element, 'Object 1/content.xml'));
        $this->assertEquals('0.026cm', $pres->getElementAttribute($element, 'svg:stroke-width', 'Object 1/content.xml'));
        $this->assertEquals('#878787', $pres->getElementAttribute($element, 'svg:stroke-color', 'Object 1/content.xml'));
        $element = '/office:document-content/office:automatic-styles/style:style[@style:name=\'styleAxisY\']/style:chart-properties';
        $this->assertTrue($pres->elementExists($element, 'Object 1/content.xml'));
        $this->assertEquals('false', $pres->getElementAttribute($element, 'chart:tick-marks-major-inner', 'Object 1/content.xml'));
        $this->assertEquals('false', $pres->getElementAttribute($element, 'chart:tick-marks-major-outer', 'Object 1/content.xml'));
        $element = '/office:document-content/office:automatic-styles/style:style[@style:name=\'styleAxisY\']/style:graphic-properties';
        $this->assertTrue($pres->elementExists($element, 'Object 1/content.xml'));
        $this->assertEquals('0.026cm', $pres->getElementAttribute($element, 'svg:stroke-width', 'Object 1/content.xml'));
        $this->assertEquals('#878787', $pres->getElementAttribute($element, 'svg:stroke-color', 'Object 1/content.xml'));
    }
    
    public function testChartPie()
    {
        $oSeries = new Series('Series', array('Jan' => 1, 'Feb' => 5, 'Mar' => 2));
        $oSeries->setShowSeriesName(true);
        $oSeries->getDataPointFill(0)->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FF4672A8'));
        $oSeries->getDataPointFill(1)->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FFAB4744'));
        $oSeries->getDataPointFill(2)->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FF8AA64F'));
    
        $oPie = new Pie();
        $oPie->addSeries($oSeries);
    
        $phpPowerPoint = new PhpPowerpoint();
        $oSlide = $phpPowerPoint->getActiveSlide();
        $oChart = $oSlide->createChartShape();
        $oChart->getPlotArea()->setType($oPie);
    
        $pres = TestHelperDOCX::getDocument($phpPowerPoint, 'ODPresentation');
    
        $element = '/office:document-content/office:body/office:chart/chart:chart';
        $this->assertTrue($pres->elementExists($element, 'Object 1/content.xml'));
        $this->assertEquals('chart:circle', $pres->getElementAttribute($element, 'chart:class', 'Object 1/content.xml'));
        
        $element = '/office:document-content/office:body/office:chart/chart:chart/chart:plot-area/chart:series/chart:data-point';
        $this->assertTrue($pres->elementExists($element, 'Object 1/content.xml'));
        
        $element = '/office:document-content/office:automatic-styles/style:style[@style:name=\'styleAxisX\']/style:chart-properties';
        $this->assertTrue($pres->elementExists($element, 'Object 1/content.xml'));
        $this->assertEquals('true', $pres->getElementAttribute($element, 'chart:reverse-direction', 'Object 1/content.xml'));
        
        $element = '/office:document-content/office:automatic-styles/style:style[@style:name=\'styleAxisY\']/style:chart-properties';
        $this->assertTrue($pres->elementExists($element, 'Object 1/content.xml'));
        $this->assertEquals('true', $pres->getElementAttribute($element, 'chart:reverse-direction', 'Object 1/content.xml'));
    }

    public function testChartPie3D()
    {
        $oSeries = new Series('Series', array('Jan' => 1, 'Feb' => 5, 'Mar' => 2));
        $oSeries->setShowSeriesName(true);
        $oSeries->getDataPointFill(0)->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FF4672A8'));
        $oSeries->getDataPointFill(1)->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FFAB4744'));
        $oSeries->getDataPointFill(2)->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FF8AA64F'));

        $oPie3D = new Pie3D();
        $oPie3D->addSeries($oSeries);

        $phpPowerPoint = new PhpPowerpoint();
        $oSlide = $phpPowerPoint->getActiveSlide();
        $oChart = $oSlide->createChartShape();
        $oChart->getPlotArea()->setType($oPie3D);

        $pres = TestHelperDOCX::getDocument($phpPowerPoint, 'ODPresentation');

        $element = '/office:document-content/office:body/office:chart/chart:chart';
        $this->assertTrue($pres->elementExists($element, 'Object 1/content.xml'));
        $this->assertEquals('chart:circle', $pres->getElementAttribute($element, 'chart:class', 'Object 1/content.xml'));

        $element = '/office:document-content/office:body/office:chart/chart:chart/chart:plot-area/chart:series/chart:data-point';
        $this->assertTrue($pres->elementExists($element, 'Object 1/content.xml'));

        $element = '/office:document-content/office:automatic-styles/style:style[@style:name=\'styleAxisX\']/style:chart-properties';
        $this->assertTrue($pres->elementExists($element, 'Object 1/content.xml'));
        $this->assertEquals('true', $pres->getElementAttribute($element, 'chart:reverse-direction', 'Object 1/content.xml'));

        $element = '/office:document-content/office:automatic-styles/style:style[@style:name=\'styleAxisY\']/style:chart-properties';
        $this->assertTrue($pres->elementExists($element, 'Object 1/content.xml'));
        $this->assertEquals('true', $pres->getElementAttribute($element, 'chart:reverse-direction', 'Object 1/content.xml'));
    }
    
    public function testChartPie3DExplosion()
    {
        $value = rand(0, 100);
        
        $oSeries = new Series('Series', array('Jan' => 1, 'Feb' => 5, 'Mar' => 2));
        $oSeries->setShowSeriesName(true);
    
        $oPie3D = new Pie3D();
        $oPie3D->setExplosion($value);
        $oPie3D->addSeries($oSeries);
    
        $phpPowerPoint = new PhpPowerpoint();
        $oSlide = $phpPowerPoint->getActiveSlide();
        $oChart = $oSlide->createChartShape();
        $oChart->getPlotArea()->setType($oPie3D);
    
        $pres = TestHelperDOCX::getDocument($phpPowerPoint, 'ODPresentation');
    
        $element = '/office:document-content/office:automatic-styles/style:style[@style:name=\'styleSeries0\'][@style:family=\'chart\']/style:chart-properties';
        $this->assertTrue($pres->elementExists($element, 'Object 1/content.xml'));
        $this->assertEquals($value, $pres->getElementAttribute($element, 'chart:pie-offset', 'Object 1/content.xml'));
    }
    
    public function testChartScatter()
    {
        $oSeries = new Series('Series', array('Jan' => 1, 'Feb' => 5, 'Mar' => 2));
        $oSeries->setShowSeriesName(true);
    
        $oScatter = new Scatter();
        $oScatter->addSeries($oSeries);
    
        $phpPowerPoint = new PhpPowerpoint();
        $oSlide = $phpPowerPoint->getActiveSlide();
        $oChart = $oSlide->createChartShape();
        $oChart->getPlotArea()->setType($oScatter);
    
        $pres = TestHelperDOCX::getDocument($phpPowerPoint, 'ODPresentation');
    
        $element = '/office:document-content/office:body/office:chart/chart:chart';
        $this->assertTrue($pres->elementExists($element, 'Object 1/content.xml'));
        $this->assertEquals('chart:scatter', $pres->getElementAttribute($element, 'chart:class', 'Object 1/content.xml'));
    }
    
    public function testLegend()
    {
        $oSeries = new Series('Series', array('Jan' => 1, 'Feb' => 5, 'Mar' => 2));
        $oSeries->setShowSeriesName(true);
        
        $oLine = new Line();
        $oLine->addSeries($oSeries);
        
        $phpPowerPoint = new PhpPowerpoint();
        $oSlide = $phpPowerPoint->getActiveSlide();
        $oChart = $oSlide->createChartShape();
        $oChart->getPlotArea()->setType($oLine);
        
        $pres = TestHelperDOCX::getDocument($phpPowerPoint, 'ODPresentation');
    
        $element = '/office:document-content/office:body/office:chart/chart:chart/chart:legend';
        $this->assertTrue($pres->elementExists($element, 'Object 1/content.xml'));
        $element = '/office:document-content/office:body/office:chart/chart:chart/table:table/table:table-header-rows';
        $this->assertTrue($pres->elementExists($element, 'Object 1/content.xml'));
        $element = '/office:document-content/office:body/office:chart/chart:chart/table:table/table:table-header-rows/table:table-row';
        $this->assertTrue($pres->elementExists($element, 'Object 1/content.xml'));
        $element = '/office:document-content/office:body/office:chart/chart:chart/table:table/table:table-header-rows/table:table-row/table:table-cell';
        $this->assertTrue($pres->elementExists($element, 'Object 1/content.xml'));
        $element = '/office:document-content/office:body/office:chart/chart:chart/table:table/table:table-header-rows/table:table-row/table:table-cell[@office:value-type=\'string\']';
        $this->assertTrue($pres->elementExists($element, 'Object 1/content.xml'));
    }
}
