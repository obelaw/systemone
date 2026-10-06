<?php

declare(strict_types=1);

use Obelaw\SystemOne\Contracts\DecisionResponseInterface;
use Obelaw\SystemOne\Contracts\DriverInterface;
use Obelaw\SystemOne\DecisionRequest;
use Obelaw\SystemOne\DecisionResponse;
use Obelaw\SystemOne\Facades\SystemOne;
use Obelaw\SystemOne\Primitives\Choice;
use Obelaw\SystemOne\Primitives\Noul;
use Obelaw\SystemOne\Primitives\Score;
use Obelaw\SystemOne\SystemOneManager;
use Obelaw\SystemOne\Tests\TestCase;

uses(TestCase::class);

describe('DecisionRequest Fluent API & Builder', function () {
    it('fluently chains state, model, questions, and serializes payload accurately', function () {
        $request = (new DecisionRequest())
            ->state('Payment gateway returned error code 504')
            ->model('custom-model-v1')
            ->ask('is_critical', Noul::make('Is this error critical?'))
            ->ask('escalation', Choice::make('Who should receive this?', [
                'devops' => 'Infrastructure team',
                'backend' => 'Payment core engineers',
            ]))
            ->ask('impact_score', Score::range('Impact score 1 to 5', 1, 5));

        $payload = $request->toPayload();

        expect($payload)->toHaveKeys(['state', 'questions'])
            ->and($payload['state'])->toBe('Payment gateway returned error code 504')
            ->and($payload['questions'])->toHaveKeys(['is_critical', 'escalation', 'impact_score'])
            ->and($payload['questions']['is_critical'])->toBe([
                'type' => 'noul',
                'instructions' => 'Is this error critical?',
            ])
            ->and($payload['questions']['escalation'])->toBe([
                'type' => 'choice',
                'instructions' => 'Who should receive this?',
                'criteria' => [
                    'devops' => 'Infrastructure team',
                    'backend' => 'Payment core engineers',
                ],
            ])
            ->and($payload['questions']['impact_score'])->toBe([
                'type' => 'score',
                'instructions' => 'Impact score 1 to 5',
                'min' => 1,
                'max' => 5,
            ]);

        expect($request->getModel())->toBe('custom-model-v1');
    });

    it('supports bulk questions registration', function () {
        $request = (new DecisionRequest())
            ->state('Context text')
            ->questions([
                'q1' => Noul::make('Question 1'),
                'q2' => Noul::make('Question 2'),
            ]);

        expect($request->getQuestions())->toHaveCount(2)
            ->and($request->getQuestions())->toHaveKeys(['q1', 'q2']);
    });

    it('executes decision using an injected DriverInterface instance', function () {
        $mockDriver = new class implements DriverInterface {
            public function decide(DecisionRequest $request): DecisionResponseInterface
            {
                return new DecisionResponse([
                    'is_fraud' => ['value' => 0.99, 'confidence' => 0.99],
                ], ['status' => 'mocked']);
            }

            public function run(DecisionRequest $request): DecisionResponseInterface
            {
                return $this->decide($request);
            }
        };

        $response = (new DecisionRequest())
            ->state('Transaction of $10,000 from unusual IP')
            ->ask('is_fraud', Noul::make('Is transaction fraudulent?'))
            ->run($mockDriver);

        expect($response->value('is_fraud'))->toBe(0.99)
            ->and($response->isPositive('is_fraud'))->toBeTrue()
            ->and($response->raw()['status'])->toBe('mocked');
    });

    it('supports driver resolution through SystemOneManager', function () {
        $manager = new SystemOneManager(app());

        $mockDriver = new class implements DriverInterface {
            public function decide(DecisionRequest $request): DecisionResponseInterface
            {
                return new DecisionResponse([
                    'processed_by' => ['value' => 'custom_driver'],
                ]);
            }

            public function run(DecisionRequest $request): DecisionResponseInterface
            {
                return $this->decide($request);
            }
        };

        $manager->extend('mock', fn() => $mockDriver);

        $request = (new DecisionRequest($manager))
            ->state('Some state')
            ->driver('mock');

        $response = $request->run();

        expect($response->value('processed_by'))->toBe('custom_driver');
    });

    it('works fluently through SystemOne facade and helper methods', function () {
        SystemOne::extend('test_driver', function () {
            return new class implements DriverInterface {
                public function decide(DecisionRequest $request): DecisionResponseInterface
                {
                    return new DecisionResponse([
                        'ready' => ['value' => 1.0],
                    ]);
                }

                public function run(DecisionRequest $request): DecisionResponseInterface
                {
                    return $this->decide($request);
                }
            };
        });

        $response = SystemOne::state('System ready check')
            ->driver('test_driver')
            ->ask('ready', Noul::make('Is ready?'))
            ->run();

        expect($response->value('ready'))->toBe(1.0);
    });
});
