<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Core\Model\App\Area;

/**
 * Class CachePlugin
 * Should add design exceptions o identifier for built-in cache
 *
 * @package Magento\Core\Model\App\Area
 */
class CacheIdentifierPlugin
{
    /**
     * Constructor
     *
     * @param DesignExceptions                $designExceptions
     * @param \Magento\App\RequestInterface   $request
     * @param \Magento\PageCache\Model\Config $config
     */
    public function __construct(
        \Magento\Core\Model\App\Area\DesignExceptions $designExceptions,
        \Magento\App\RequestInterface $request,
        \Magento\PageCache\Model\Config $config
    ) {
        $this->designExceptions = $designExceptions;
        $this->request = $request;
        $this->config = $config;
    }

    /**
     * Adds a theme key to identifier for a built-in cache if user-agent theme rule is actual
     *
     * @param \Magento\App\PageCache\Identifier $identifier
     * @param string $result
     * @return string
     */
    public function afterGetValue(\Magento\App\PageCache\Identifier $identifier, $result)
    {
        if ($this->config->getType() == \Magento\PageCache\Model\Config::BUILT_IN && $this->config->isEnabled()) {
            $ruleDesignException = $this->designExceptions->getThemeForUserAgent($this->request);
            if ($ruleDesignException !== false) {
                return $ruleDesignException . $result;
            }
        }
        return $result;
    }
}
