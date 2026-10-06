# Obelaw SystemOne

[![Pest Tests](https://img.shields.io/badge/Pest-Passing-brightgreen)](https://pestphp.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue)](https://php.net)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

**SystemOne** is the modular Core Decision Engine for the Obelaw AI ecosystem. It replaces open-ended conversational generation with typed decision primitives (`Noul`, `Choice`, `Score`) evaluated against arbitrary context states.

## Primitives

- **`Noul`**: Binary / Bernoulli likelihood questions:
  ```php
  Noul::make('Is this payment transaction fraudulent?')
  ```
- **`Choice`**: Categorical decisions with associative or list criteria:
  ```php
  Choice::make('Which department should handle this request?', [
      'billing' => 'Invoices, refunds, and pricing',
      'support' => 'Technical troubleshooting',
      'sales' => 'Plan upgrades and enterprise licenses',
  ])
  ```
- **`Score`**: Bounded numerical ratings or ordered rubric scales:
  ```php
  Score::rubric('Rate ticket urgency', ['Low', 'Medium', 'High', 'Critical'])
  Score::range('Score satisfaction from 1 to 10', 1, 10)
  ```

## Fluent API Builder

```php
use Obelaw\SystemOne\Facades\SystemOne;
use Obelaw\SystemOne\Primitives\Noul;
use Obelaw\SystemOne\Primitives\Choice;

$response = SystemOne::state('Customer account logged in from 3 distinct IP addresses in 5 minutes.')
    ->ask('is_anomaly', Noul::make('Is this login pattern suspicious?'))
    ->ask('action', Choice::make('Recommended security mitigation', [
        'allow' => 'Allow login silently',
        'challenge' => 'Send MFA push notification',
        'block' => 'Block session immediately',
    ]))
    ->run();

$isAnomaly = $response->value('is_anomaly'); // e.g. 0.96
$action = $response->value('action');         // e.g. "challenge"
$confidence = $response->confidence('action'); // e.g. 0.94
```
