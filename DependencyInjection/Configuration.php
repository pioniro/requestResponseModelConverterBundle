<?php
declare(strict_types=1);

namespace Pioniro\RequestResponseModelConverterBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Alexey Fedorov <pioniro@yandex.ru>
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $tree = new TreeBuilder('rrm_converter');
        return $tree;
    }
}