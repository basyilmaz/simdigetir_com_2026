---
name: project-tracking
description: Use this skill when the user asks for project status, sprint tracking, completion percentages, missing items, or execution order. It provides a strict checklist-driven workflow for planning and reporting progress.
---

# Project Tracking Skill

Use this skill for sprint-level execution and reporting.

## Workflow

1. Identify active roadmap source of truth.
2. Extract sprint goals, acceptance criteria, and technical backlog.
3. Classify each item:
- `done`
- `partial`
- `missing`
- `blocked`
4. Convert `partial` and `missing` items into implementation tasks with concrete file targets.
5. Execute tasks in dependency order.
6. After each implementation step:
- run focused tests
- run full quality gate when milestone is complete
7. Publish a concise sprint status report:
- completion percent
- done/partial/missing list
- next critical steps

## Status Rules

- Do not mark as `done` unless behavior is verified by tests or deterministic code inspection.
- `partial` must include exact missing part.
- `blocked` must include blocking dependency and proposed workaround.

## Reporting Template

- Sprint: `Sprint N`
- Completion: `%`
- Done:
- Partial:
- Missing:
- Risks:
- Next actions:

