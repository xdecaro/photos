# SDD ledger — plan: docs/superpowers/plans/2026-09-26-photos-v0.1.0.md

Execution mode: Native on isolated branch `feature/photos-v0.1.0`.
Ruling: container cannot resolve github.com; GitHub Actions on the isolated branch is the execution/test environment.
Pre-flight: Task 2 consumes Task 1 package identity; Task 3 consumes Task 2 PhotoReference; Task 4 consumes Tasks 2–3 references/storage; Task 5 consumes Tasks 3–4 storage/service; Task 6 consumes Task 4 service + Core UI; Task 7 consumes Tasks 3 + 5; Task 8 consumes all previous tasks. No interface conflicts found against the approved spec.
