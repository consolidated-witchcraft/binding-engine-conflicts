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
├── relationshipType: parent_of
├── subject: george-austen
└── object: jane-austen
```

and:

```text
RelationshipProjection
├── relationshipType: parent_of
├── subject: someone-else
└── object: jane-austen
```

A conflict rule may produce:

```text
Conflict
├── conflictType: field_value_conflict
├── field: subject
├── subjectKey:
│   relationship:parent_of:*:jane-austen
├── candidates:
│   ├── george-austen
│   └── someone-else
└── originatingProjections:
    ├── (...)
    └── (...)
```

Conflict structures preserve:
- competing semantic structures
- provenance
- semantic identity
- deterministic ordering

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
- originating projections
- originating assertions
- source document identifiers
- source revision identifiers
- vocabulary identifiers
- vocabulary versions
- source spans where available

Downstream systems should always be able to answer:

> “Which semantic structures conflicted, and where did they originate?”

---

## Deterministic Detection

Conflict detection is intentionally deterministic.

Given identical:
- projections
- vocabularies
- conflict rules
- library versions

the same conflicts should always be produced.

This is important for:
- reproducibility
- synchronization
- debugging
- provenance traceability

---

## No Hidden Resolution

This package intentionally avoids:
- canonical truth resolution
- semantic reconciliation
- automatic conflict suppression
- entity merging
- editorial prioritisation

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

# Planned Components

## Conflict Detector

Detects incompatible semantic structures from projection sets.

---

## Conflict Set

Immutable collection of semantic conflicts.

---

## Conflict Rules

Deterministic semantic rules defining incompatibility conditions.

Examples may include:
- competing chronology
- incompatible classifications
- conflicting relationships
- mutually exclusive semantic identity

---

## Conflict Types

Structured conflict representations for:
- field value conflicts
- chronology conflicts
- relationship conflicts
- semantic identity conflicts

---

# Philosophy

Binding Engine Conflicts preserves semantic disagreement rather than resolving it.

This distinction is important.

Different applications may resolve conflicts differently using:
- canonical source systems
- editorial review workflows
- manual conflict resolution
- trust-weighted systems

This package intentionally avoids imposing a universal truth model.

---

# Example Future Usage

```php
<?php

declare(strict_types=1);

use ConsolidatedWitchcraft\BindingEngine\Conflicts\ConflictDetector;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Rules\UniqueFieldConflictRule;

$detector = new ConflictDetector([
    new UniqueFieldConflictRule(
        projectionType: 'event',
        field: 'occurred-at',
    ),
]);

$conflictSet = $detector->detect($projectionSet);

foreach ($conflictSet->getConflicts() as $conflict) {
    var_dump($conflict);
}
```

---

# Important

The conflict detection layer assumes:
- parser validation has already succeeded
- vocabulary validation has already succeeded
- assertion extraction has already succeeded
- semantic projection has already succeeded

Malformed semantic structures should not be passed into the conflict layer.

---

# Related Packages

| Package                                                  | Responsibility                                 |
|----------------------------------------------------------|------------------------------------------------|
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
- provenance preservation
- deterministic behaviour
- explicit semantic modelling
- immutable structures
- application neutrality

Avoid:
- hidden reconciliation
- silent canonicalisation
- implicit mutation
- application-specific truth models

---

# License

Licensed under the GNU Affero General Public License v3.0 or later (`AGPL-3.0-or-later`).