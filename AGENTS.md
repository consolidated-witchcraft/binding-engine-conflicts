# AGENTS.md — BindingEngine Conflicts Library

## README.md

The README.md contains valuable information about the structure and intent of this repository and should be consulted, read and followed.

---

# Purpose

This repository contains the semantic conflict detection layer for the Consolidated Witchcraft BindingEngine ecosystem.

The responsibility of this package is:

- detecting incompatible semantic structures
- preserving competing semantic claims
- exposing semantic disagreement explicitly
- preserving provenance through conflict analysis
- supporting downstream resolution workflows

This package MUST NOT:
- silently resolve conflicts
- determine canonical truth
- discard conflicting structures
- mutate originating semantic structures
- perform hidden inference
- perform graph persistence
- erase provenance chains

This package exists to expose semantic disagreement — not to decide truth.

---

# Architectural Principles

## Conflicts Represent Disagreement

Conflicts represent:

> “These semantic structures are incompatible under a given rule.”

They do NOT represent:

> “This semantic structure is false.”

Multiple conflicting structures may coexist indefinitely.

Conflict resolution belongs to downstream systems.

---

## Provenance Is Mandatory

Conflict structures MUST preserve provenance information.

This includes:
- originating projections
- originating assertions
- source document identifiers
- source revision identifiers
- vocabulary identifiers
- vocabulary versions
- source spans where available

Downstream systems MUST be able to determine:
- which semantic structures conflicted
- where those structures originated
- which rule detected the conflict

Loss of provenance is considered a serious architectural failure.

---

## Conflict Detection Is Deterministic

Conflict detection MUST be deterministic.

Given identical:
- projections
- vocabularies
- conflict rules
- library versions

the same conflicts should always be produced.

Avoid:
- hidden heuristics
- probabilistic behaviour
- implicit prioritisation
- order-dependent mutation

Determinism is foundational to:
- reproducibility
- debugging
- synchronization
- provenance traceability

---

## Conflict Detection Does Not Resolve Conflicts

This package detects:
- incompatible semantic structures

It does NOT:
- choose winners
- canonicalise structures
- merge entities
- reconcile semantic disagreement
- discard conflicting claims

Resolution belongs to downstream systems such as:
- editorial review workflows
- canonical source systems
- manual conflict resolution systems
- trust-weighted reification systems

---

## Immutable Data Structures

Conflict structures SHOULD be immutable.

Prefer:
- readonly classes
- value objects
- explicit constructor validation

Avoid:
- setters
- mutable collections
- hidden internal state

Mutation introduces ambiguity into provenance and semantic analysis.

---

## Explicitness Over Implicit Behaviour

This package prioritises:
- explicit semantic rules
- deterministic conflict modelling
- transparent provenance
- traceable semantic analysis

Avoid:
- magical reconciliation
- hidden canonicalisation
- implicit semantic assumptions
- silent data mutation

If semantic resolution is required, it belongs downstream.

---

# Repository Standards

## PHP Standards

- `declare(strict_types=1);` is mandatory
- PHPStan MUST pass at maximum configured level
- `treatPhpDocTypesAsCertain: true` is enforced
- All public APIs MUST be fully typed
- Array shapes MUST be documented where appropriate
- Prefer immutable value objects over associative arrays

---

## Exceptions

Exceptions MUST:
- be domain-specific
- carry meaningful contextual information
- preserve previous exceptions

Never throw:
- `\Exception`
- `\RuntimeException`
- `\Throwable`

except at application boundaries.

---

## Testing Standards

All behaviour MUST be covered by tests.

Tests SHOULD:
- validate deterministic conflict detection
- validate provenance preservation
- validate successful construction paths
- validate failure paths
- validate edge cases

Tests MUST:
- assert exact exception types
- assert exact conflict structures where stable
- avoid hidden coupling between test cases

Boundary tests are required for:
- projection identity handling
- provenance preservation
- conflicting candidate structures
- deterministic ordering
- invalid conflict construction

---

## Provenance Handling

Provenance is first-class system data.

Conflict structures MUST preserve:
- originating projections
- originating assertions
- semantic identity
- vocabulary context
- source traceability

Downstream systems should always be able to answer:

> “Which semantic structures conflicted, and where did they originate?”

---

## Conflict Rules

Conflict rules SHOULD:
- remain narrowly scoped
- remain deterministic
- expose explicit semantic reasoning
- avoid application-specific assumptions where possible

Prefer:
- small focused rule objects
- explicit rule naming
- composable detection systems

Avoid:
- giant monolithic conflict engines
- hidden semantic mutation
- rule side-effects

---

## Conflict Detection vs Inference

Keep conflict detection and inference strictly separated.

This repository detects:
- incompatible semantic structures

It does NOT:
- derive implied structures
- generate hidden semantic relationships
- infer missing entities
- construct semantic graphs

Inference belongs to dedicated inference systems.

---

## Conflict Detection vs Reification

This repository exposes semantic disagreement.

It does NOT:
- determine canonical truth
- resolve semantic ambiguity
- apply editorial judgement
- perform trust-weighted resolution

Different downstream systems may resolve conflicts differently.

This package intentionally avoids imposing:
- universal truth models
- canonical resolution strategies
- application-specific semantics

---

## Preferred Design Style

Prefer:
- composition over inheritance
- immutable value objects
- small focused services
- deterministic transforms
- explicit constructor validation

Avoid:
- service locators
- hidden global state
- reflection-heavy behaviour
- runtime mutation
- implicit magic resolution

---

## Commit Standards

Commits MUST:
- pass the full test suite
- pass PHPStan
- preserve deterministic behaviour
- preserve provenance guarantees
- maintain backwards compatibility unless intentionally breaking

Do not commit:
- failing tests
- partially implemented conflict rules
- hidden reconciliation behaviour
- debugging artefacts
- dead code

---

## Long-Term Direction

This package is intended to become:
- deterministic
- provenance-safe
- infrastructure-grade
- semantically explicit
- application-neutral

Optimise for:
- correctness
- traceability
- semantic clarity
- maintainability

over:
- hidden abstraction
- magical behaviour
- convenience-driven shortcuts
- premature optimisation

---

## Coding Standards

Coding standards are contained within the `./codingstandards/` subdirectory and MUST be followed.