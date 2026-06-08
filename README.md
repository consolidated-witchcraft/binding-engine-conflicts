# Binding Engine Conflicts

A deterministic, provenance-aware semantic conflict detection layer for the Consolidated Witchcraft BindingEngine ecosystem.

Binding Engine Conflicts detects incompatible semantic structures while preserving competing semantic claims and their provenance.

## Purpose

The Binding Engine parser answers:

> “What syntactically exists in this document?”

The vocabulary layer answers:

> “Is this binding semantically valid under this vocabulary?”

Binding Assertions answers:

> “What claims does this document make?”

Binding Projection answers:

> “What semantic structures do those claims describe?”

Binding Conflicts answers:

> “Which semantic structures are incompatible?”

This package acts as the bridge between semantic projection and downstream resolution workflows.

---

# Status

Early development.

The API should be considered unstable until `1.0.0`.

---

# Installation

```bash
composer require consolidated-witchcraft/binding-engine-conflicts
```

---

# Conceptual Overview

Given competing semantic projections:

```text
RelationshipProjection
├── relationshipType: born_in
├── subject: jane-austen
└── object: steventon-hampshire
```

and:

```text
RelationshipProjection
├── relationshipType: born_in
├── subject: jane-austen
└── object: bath
```

A configured relationship cardinality rule may produce:

```text
Conflict
├── conflictType: relationship_conflict
├── field: object
├── subjectKey:
│   relationship:born_in:subject:jane-austen
├── candidates:
│   ├── steventon-hampshire
│   └── bath
└── originatingProjections:
    ├── (...)
    └── (...)
```

The conflict indicates that a relationship configured as:

```text
unique_object_per_subject
```

has produced multiple competing values for the same subject.

Conflict structures preserve:

* competing semantic structures
* provenance
* semantic identity
* deterministic ordering

Conflicts expose semantic disagreement without resolving it.

---

# Core Philosophy

Conflicts represent:

> “These semantic structures are incompatible under a given rule.”

They do NOT represent:

> “This semantic structure is false.”

Multiple conflicting structures may coexist indefinitely.

Conflict resolution belongs to downstream systems.

---

# Design Goals

## Provenance First

Conflicts preserve:

* originating projections
* originating assertions
* source document identifiers
* source revision identifiers
* vocabulary identifiers
* vocabulary versions
* source spans where available

Downstream systems should always be able to answer:

> “Which semantic structures conflicted, and where did they originate?”

---

## Deterministic Detection

Conflict detection is intentionally deterministic.

Given identical:

* projections
* vocabularies
* conflict rules
* library versions

the same conflicts should always be produced.

This is important for:

* reproducibility
* synchronization
* debugging
* provenance traceability

---

## No Hidden Resolution

This package intentionally avoids:

* canonical truth resolution
* semantic reconciliation
* automatic conflict suppression
* entity merging
* editorial prioritisation

Conflict detection exposes disagreement explicitly rather than deciding winners.

---

# Example Workflow

```text
Markdown Document
↓
Parser
↓
Vocabulary Validator
↓
Assertions
↓
Projection
↓
Conflict Detection
↓
Conflict Set
↓
Resolution / Editorial Review / Reification
```

---

# Current Components

## Conflict

Immutable representation of semantic disagreement.

## Conflict Candidate

Represents one competing semantic value involved in a conflict.

## Conflict Set

Immutable collection of conflicts.

## Conflict Detector

Coordinates one or more conflict rules and produces a conflict set.

## Conflict Rules

Deterministic semantic rules which identify incompatible structures.

Current built-in rules:

* RelationshipCardinalityConflictRule

## Rule Definitions

Rule definitions construct configured rules from external configuration.

Current built-in definitions:

* RelationshipCardinalityConflictRuleDefinition

## JSON Rule Loader

Loads conflict rule configuration from JSON documents and produces configured conflict rules.

The loader is extensible through user-supplied implementations of:

```php
ConflictRuleDefinitionInterface
```

allowing applications to provide custom rule types without modifying the library.

---

# Extensibility

Applications may provide their own conflict rules by implementing:

```php
ConflictRuleInterface
```

Applications may provide custom rule configuration formats by implementing:

```php
ConflictRuleDefinitionInterface
```

This allows downstream systems to define domain-specific conflict behaviour without modifying the library itself.

---

# Philosophy

Binding Engine Conflicts preserves semantic disagreement rather than resolving it.

This distinction is important.

Different applications may resolve conflicts differently using:

* canonical source systems
* editorial review workflows
* manual conflict resolution
* trust-weighted systems

This package intentionally avoids imposing a universal truth model.

---

# Usage

```php
<?php

declare(strict_types=1);

use ConsolidatedWitchcraft\BindingEngine\Conflicts\ConflictDetector;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\RuleLoading\JsonConflictRuleLoader;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Rules\RelationshipCardinalityConflictRuleDefinition;

$loader = new JsonConflictRuleLoader([
    new RelationshipCardinalityConflictRuleDefinition(),
]);

$rules = $loader->load(
    file_get_contents(__DIR__ . '/conflict-rules.json'),
);

$detector = new ConflictDetector($rules);

$conflictSet = $detector->detect($projectionSet);

foreach ($conflictSet->getConflicts() as $conflict) {
    var_dump($conflict);
}
```

Example rule configuration:

```json
{
  "rules": [
    {
      "type": "relationship_cardinality",
      "relationshipType": "born_in",
      "direction": "unique_object_per_subject",
      "severity": "manual_resolution_required"
    }
  ]
}
```

---

# Important

The conflict detection layer assumes:

* parser validation has already succeeded
* vocabulary validation has already succeeded
* assertion extraction has already succeeded
* semantic projection has already succeeded

Malformed semantic structures should not be passed into the conflict layer.

---

# Related Packages

| Package                                                  | Responsibility                                 |
| -------------------------------------------------------- | ---------------------------------------------- |
| consolidated-witchcraft/binding-engine-parser            | Parses binding syntax into AST structures      |
| consolidated-witchcraft/binding-engine-vocabulary        | Defines semantic vocabulary rules              |
| consolidated-witchcraft/binding-engine-vocabulary-loader | Loads vocabularies from JSON definitions       |
| consolidated-witchcraft/binding-engine-assertions        | Extracts provenance-aware semantic assertions  |
| consolidated-witchcraft/binding-engine-projection        | Projects assertions into semantic structures   |
| consolidated-witchcraft/binding-engine-conflicts         | Detects semantic conflicts between projections |

---

# Development

## Quality Standards

Commits should not be made without running:

```bash
composer test
composer stan
```

PHPStan is configured with:

```neon
treatPhpDocTypesAsCertain: true
```

Docblocks are therefore considered part of the public contract and must remain accurate.

---

# Design Principles

This package prioritises:

* provenance preservation
* deterministic behaviour
* explicit semantic modelling
* immutable structures
* application neutrality

Avoid:

* hidden reconciliation
* silent canonicalisation
* implicit mutation
* application-specific truth models

---

# License

Licensed under the GNU Affero General Public License v3.0 or later (`AGPL-3.0-or-later`).
