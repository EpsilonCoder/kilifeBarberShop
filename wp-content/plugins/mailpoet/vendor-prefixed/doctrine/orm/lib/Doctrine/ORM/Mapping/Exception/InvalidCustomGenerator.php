<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping\Exception;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Exception\ORMException;
use LogicException;
use function sprintf;
use function var_export;
final class InvalidCustomGenerator extends ORMException
{
 public static function onClassNotConfigured() : self
 {
 return new self('Cannot instantiate custom generator, no class has been defined');
 }
 public static function onMissingClass(array $definition) : self
 {
 return new self(sprintf('Cannot instantiate custom generator : %s', var_export($definition, \true)));
 }
}
