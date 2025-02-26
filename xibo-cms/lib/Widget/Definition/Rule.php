<?php


namespace Xibo\Widget\Definition;

/**
 * A rule to apply to a property
 * @SWG\Definition()
 */
class Rule implements \JsonSerializable
{
    public $onSave = true;

    public $onStatus = true;

    /** @var Test[] */
    public $tests;

    public function addRuleTest(Test $test): Rule
    {
        $this->tests[] = $test;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'onSave' => $this->onSave,
            'onStatus' => $this->onStatus,
            'tests' => $this->tests,
        ];
    }
}
