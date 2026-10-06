<?php

declare(strict_types=1);

use Obelaw\SystemOne\Contracts\DecisionResponseInterface;
use Obelaw\SystemOne\Contracts\PrimitiveInterface;
use Obelaw\SystemOne\DecisionResponse;
use Obelaw\SystemOne\Primitives\Choice;
use Obelaw\SystemOne\Primitives\Noul;
use Obelaw\SystemOne\Primitives\Score;
use Obelaw\SystemOne\Tests\TestCase;

uses(TestCase::class);

describe('Primitives Serialization & Schemas', function () {
    it('serializes Noul to standard decision instruction schema', function () {
        $noul = Noul::make('Is this support ticket urgent?');

        expect($noul)->toBeInstanceOf(PrimitiveInterface::class)
            ->and($noul->getType())->toBe('noul')
            ->and($noul->getInstructions())->toBe('Is this support ticket urgent?')
            ->and($noul->toArray())->toBe([
                'type' => 'noul',
                'instructions' => 'Is this support ticket urgent?',
            ])
            ->and(json_encode($noul))->toBe(json_encode([
                'type' => 'noul',
                'instructions' => 'Is this support ticket urgent?',
            ]));
    });

    it('allows mutating Noul instructions fluently', function () {
        $noul = Noul::make('Initial instruction')
            ->instructions('Updated instruction');

        expect($noul->getInstructions())->toBe('Updated instruction')
            ->and($noul->toArray()['instructions'])->toBe('Updated instruction');
    });

    it('serializes Choice to instruction and list criteria schema', function () {
        $choice = Choice::make('Pick a priority level', ['low', 'medium', 'high']);

        expect($choice)->toBeInstanceOf(PrimitiveInterface::class)
            ->and($choice->getType())->toBe('choice')
            ->and($choice->getInstructions())->toBe('Pick a priority level')
            ->and($choice->toArray())->toBe([
                'type' => 'choice',
                'instructions' => 'Pick a priority level',
                'criteria' => ['low', 'medium', 'high'],
            ]);
    });

    it('serializes Choice with associative key-description criteria', function () {
        $choice = Choice::make('Route customer ticket', [
            'billing' => 'Invoices, refunds, and charges',
            'support' => 'Technical troubleshooting',
        ]);

        expect($choice->toArray())->toBe([
            'type' => 'choice',
            'instructions' => 'Route customer ticket',
            'criteria' => [
                'billing' => 'Invoices, refunds, and charges',
                'support' => 'Technical troubleshooting',
            ],
        ]);
    });

    it('allows configuring Choice criteria fluently', function () {
        $choice = Choice::make('Select department')
            ->criterion('finance', 'Financial inquiries')
            ->criterion('engineering', 'Engineering inquiries');

        expect($choice->getCriteria())->toBe([
            'finance' => 'Financial inquiries',
            'engineering' => 'Engineering inquiries',
        ]);
    });

    it('serializes Score to bounded numerical min and max schema', function () {
        $score = Score::range('Rate customer satisfaction from 1 to 5', 1, 5);

        expect($score)->toBeInstanceOf(PrimitiveInterface::class)
            ->and($score->getType())->toBe('score')
            ->and($score->getMin())->toBe(1)
            ->and($score->getMax())->toBe(5)
            ->and($score->toArray())->toBe([
                'type' => 'score',
                'instructions' => 'Rate customer satisfaction from 1 to 5',
                'min' => 1,
                'max' => 5,
            ]);
    });

    it('serializes Score with ordered rubric criteria array', function () {
        $score = Score::rubric('Assess severity level', [
            'No impact',
            'Minor',
            'Major',
            'Critical',
        ]);

        expect($score->toArray())->toBe([
            'type' => 'score',
            'instructions' => 'Assess severity level',
            'criteria' => [
                'No impact',
                'Minor',
                'Major',
                'Critical',
            ],
        ]);
    });

    it('allows mutating Score bounds and rubric criteria fluently', function () {
        $score = Score::make('Evaluation')
            ->min(10)
            ->max(100);

        expect($score->getMin())->toBe(10)
            ->and($score->getMax())->toBe(100)
            ->and($score->toArray())->toBe([
                'type' => 'score',
                'instructions' => 'Evaluation',
                'min' => 10,
                'max' => 100,
            ]);

        $score->criteria(['Poor', 'Fair', 'Good', 'Excellent']);

        expect($score->toArray())->toBe([
            'type' => 'score',
            'instructions' => 'Evaluation',
            'criteria' => ['Poor', 'Fair', 'Good', 'Excellent'],
        ]);
    });
});

describe('DecisionResponse & Response Accessors', function () {
    it('implements DecisionResponseInterface and correctly accesses values', function () {
        $answers = [
            'urgent' => [
                'value' => 0.88,
                'confidence' => 0.88,
            ],
            'department' => [
                'value' => 'engineering',
                'confidence' => 0.95,
                'probabilities' => [
                    'billing' => 0.05,
                    'engineering' => 0.95,
                ],
            ],
            'severity' => [
                'value' => 4.2,
                'confidence' => 0.82,
            ],
        ];

        $raw = ['result' => ['answers' => $answers], 'success' => true];
        $response = new DecisionResponse($answers, $raw);

        expect($response)->toBeInstanceOf(DecisionResponseInterface::class)
            ->and($response->answers())->toBe($answers)
            ->and($response->raw())->toBe($raw)
            ->and($response->value('urgent'))->toBe(0.88)
            ->and($response->confidence('urgent'))->toBe(0.88)
            ->and($response->isPositive('urgent'))->toBeTrue()
            ->and($response->isNegative('urgent'))->toBeFalse()
            ->and($response->isPositive('urgent', 0.90))->toBeFalse()
            ->and($response->value('department'))->toBe('engineering')
            ->and($response->confidence('department'))->toBe(0.95)
            ->and($response->probabilities('department'))->toBe([
                'billing' => 0.05,
                'engineering' => 0.95,
            ])
            ->and($response->value('severity'))->toBe(4.2)
            ->and($response->value('nonexistent'))->toBeNull();
    });
});
