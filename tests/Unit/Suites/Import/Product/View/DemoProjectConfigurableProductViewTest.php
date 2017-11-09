<?php

declare(strict_types=1);

namespace LizardsAndPumpkins\Import\Product\View;

use LizardsAndPumpkins\Import\ImageStorage\Image;
use LizardsAndPumpkins\Import\Product\Composite\ConfigurableProduct;
use LizardsAndPumpkins\Import\Product\ProductAttribute;
use LizardsAndPumpkins\Import\Product\ProductAttributeList;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LizardsAndPumpkins\Import\Product\View\DemoProjectConfigurableProductView
 */
class DemoProjectConfigurableProductViewTest extends TestCase
{
    /**
     * @var ConfigurableProduct|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockProduct;

    /**
     * @var DemoProjectConfigurableProductView
     */
    private $productView;

    /**
     * @var ProductViewLocator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stubProductViewLocator;

    /**
     * @var ProductImageFileLocator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stubProductImageFileLocator;
    
    protected function setUp()
    {
        $this->stubProductViewLocator = $this->createMock(ProductViewLocator::class);
        $this->mockProduct = $this->createMock(ConfigurableProduct::class);
        $this->stubProductImageFileLocator = $this->createMock(ProductImageFileLocator::class);
        $this->stubProductImageFileLocator->method('getPlaceholder')->willReturn($this->createMock(Image::class));
        $this->stubProductImageFileLocator->method('getVariantCodes')->willReturn(['large']);

        $this->productView = new DemoProjectConfigurableProductView(
            $this->stubProductViewLocator,
            $this->mockProduct,
            $this->stubProductImageFileLocator
        );
    }

    public function testProductViewInterfaceIsImplemented()
    {
        $this->assertInstanceOf(ProductView::class, $this->productView);
    }

    public function testItExtendsTheAbstractConfigurableProductView()
    {
        $this->assertInstanceOf(AbstractConfigurableProductView::class, $this->productView);
    }

    public function testOriginalProductIsReturned()
    {
        $this->assertSame($this->mockProduct, $this->productView->getOriginalProduct());
    }

    public function testGettingFirstValueOfBackordersAttributeReturnsEmptyString()
    {
        $testAttributeCode = 'backorders';
        $testAttributeValue = 'true';

        $attribute = new ProductAttribute($testAttributeCode, $testAttributeValue, []);
        $attributeList = new ProductAttributeList($attribute);
        $this->mockProduct->method('getAttributes')->willReturn($attributeList);

        $this->assertSame('', $this->productView->getFirstValueOfAttribute($testAttributeCode));
    }

    public function testGettingAllValuesOfBackordersAttributeReturnsEmptyArray()
    {
        $testAttributeCode = 'backorders';
        $testAttributeValue = true;

        $attribute = new ProductAttribute($testAttributeCode, $testAttributeValue, []);
        $attributeList = new ProductAttributeList($attribute);
        $this->mockProduct->method('getAttributes')->willReturn($attributeList);

        $this->assertSame([], $this->productView->getAllValuesOfAttribute($testAttributeCode));
    }

    public function testProductViewAttributeListDoesNotHaveBackorders()
    {
        $testAttributeCode = 'backorders';
        $testAttributeValue = true;

        $attribute = new ProductAttribute($testAttributeCode, $testAttributeValue, []);
        $attributeList = new ProductAttributeList($attribute);
        $this->mockProduct->method('getAttributes')->willReturn($attributeList);

        $this->assertFalse($this->productView->hasAttribute($testAttributeCode));
    }

    public function testFilteredProductAttributeListIsReturned()
    {
        $nonPriceAttribute = new ProductAttribute('foo', 'bar', []);
        $priceAttribute = new ProductAttribute('price', 1000, []);
        $specialPriceAttribute = new ProductAttribute('special_price', 900, []);
        $backordersAttribute = new ProductAttribute('backorders', true, []);

        $attributeList = new ProductAttributeList(
            $nonPriceAttribute,
            $priceAttribute,
            $specialPriceAttribute,
            $backordersAttribute
        );

        $this->mockProduct->method('getAttributes')->willReturn($attributeList);

        $result = $this->productView->getAttributes();

        $this->assertCount(1, $result);
        $this->assertContains($nonPriceAttribute, $result->getAllAttributes());
    }
}
