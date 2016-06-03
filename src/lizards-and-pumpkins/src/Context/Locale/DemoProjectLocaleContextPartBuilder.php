<?php

namespace LizardsAndPumpkins\Context\Locale;

use LizardsAndPumpkins\Context\ContextBuilder;
use LizardsAndPumpkins\Context\ContextPartBuilder;
use LizardsAndPumpkins\Context\Locale\Exception\UnableToDetermineContextLocaleException;
use LizardsAndPumpkins\Context\Website\RequestToWebsiteMap;
use LizardsAndPumpkins\Http\HttpRequest;

class DemoProjectLocaleContextPartBuilder implements ContextPartBuilder
{
    private $websiteCodeToLocaleMap = [
        'de' => 'de_DE',
        'en' => 'en_US',
        'fr' => 'fr_FR',
    ];

    /**
     * @var RequestToWebsiteMap
     */
    private $requestToWebsiteMap;

    public function __construct(RequestToWebsiteMap $requestToWebsiteMap)
    {
        $this->requestToWebsiteMap = $requestToWebsiteMap;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return Locale::CONTEXT_CODE;
    }

    /**
     * @param mixed[] $inputDataSet
     * @return string
     */
    public function getValue(array $inputDataSet)
    {
        if (isset($inputDataSet[Locale::CONTEXT_CODE])) {
            return (string) $inputDataSet[Locale::CONTEXT_CODE];
        }

        if (isset($inputDataSet[ContextBuilder::REQUEST])) {
            return $this->getLocaleFromRequest($inputDataSet[ContextBuilder::REQUEST]);
        }

        throw new UnableToDetermineContextLocaleException(
            'Unable to determine context locale as neither the locale nor the request are set in the input array.'
        );
    }

    /**
     * @param HttpRequest $request
     * @return string
     */
    private function getLocaleFromRequest(HttpRequest $request)
    {
        $websiteCode = (string) $this->requestToWebsiteMap->getWebsiteFromRequest($request);

        if (isset($this->websiteCodeToLocaleMap[$websiteCode])) {
            return $this->websiteCodeToLocaleMap[$websiteCode];
        }

        throw new UnableToDetermineContextLocaleException('Unable to determine locale from request.');
    }
}
