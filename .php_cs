<?php
$finder = PhpCsFixer\Finder::create()
            ->in('src')
            ->in('tests')
;
return PhpCsFixer\Config::create()
    ->setRules(array(
         '@PSR2' => true,
         'array_syntax' => ['syntax' => 'short'],
         'no_unused_imports' => true,
         'blank_line_after_opening_tag' => true,
    ))
    ->setFinder($finder)
;
