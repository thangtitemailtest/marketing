<?php

namespace Google\AdsApi\AdManager\v202002;


/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
abstract class HasHtmlSnippetDynamicAllocationCreative extends \Google\AdsApi\AdManager\v202002\BaseDynamicAllocationCreative
{

    /**
     * @var string $codeSnippet
     */
    protected $codeSnippet = null;

    /**
     * @param int $advertiserId
     * @param int $id
     * @param string $name
     * @param \Google\AdsApi\AdManager\v202002\Size $size
     * @param string $previewUrl
     * @param string[] $policyViolations
     * @param string[] $policyLabels
     * @param \Google\AdsApi\AdManager\v202002\AppliedLabel[] $appliedLabels
     * @param \Google\AdsApi\AdManager\v202002\DateTime $lastModifiedDateTime
     * @param \Google\AdsApi\AdManager\v202002\BaseCustomFieldValue[] $customFieldValues
     * @param string $codeSnippet
     */
    public function __construct($advertiserId = null, $id = null, $name = null, $size = null, $previewUrl = null, array $policyViolations = null, array $policyLabels = null, array $appliedLabels = null, $lastModifiedDateTime = null, array $customFieldValues = null, $codeSnippet = null)
    {
      parent::__construct($advertiserId, $id, $name, $size, $previewUrl, $policyViolations, $policyLabels, $appliedLabels, $lastModifiedDateTime, $customFieldValues);
      $this->codeSnippet = $codeSnippet;
    }

    /**
     * @return string
     */
    public function getCodeSnippet()
    {
      return $this->codeSnippet;
    }

    /**
     * @param string $codeSnippet
     * @return \Google\AdsApi\AdManager\v202002\HasHtmlSnippetDynamicAllocationCreative
     */
    public function setCodeSnippet($codeSnippet)
    {
      $this->codeSnippet = $codeSnippet;
      return $this;
    }

}
