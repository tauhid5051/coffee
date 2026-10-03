# Coffee Application - Agent Instructions

## Project
Legacy PHP application running on PHP 7.4.

## Before changing code
1. Inspect existing implementation before creating new patterns.
2. Trace the complete request flow before modifying behavior.
3. Search for all callers/usages of functions, methods, variables,
   routes, database fields, and views being changed.
4. Do not modify unrelated files.

## Compatibility
- PHP 7.4 compatibility is mandatory.
- Do not introduce PHP 8+ syntax.
- Preserve existing framework conventions.
- Preserve existing database conventions.

## Before completing a task
1. Review git diff.
2. Check for accidental unrelated changes.
3. Run relevant tests/checks.
4. Report exactly which files were changed and why.

## Safety
- Never modify vendor/.
- Never modify .git/.
- Do not make database-destructive changes without explicit approval.
- Do not delete existing functionality merely because it appears unused.


For every product, does the current stock equal the opening stock + every valid stock-in movement − every valid stock-out movement, with edits/deletes/reversals applied exactly once?