# AGENTS.md

Guidance for AI coding agents working in **specifications-ITS-JSON**.

## What this repo is

`specifications-ITS-JSON` is the **document source** for the openEHR **ITS-JSON** (JSON Schemas) component. It is a *specification* repo, not software: the deliverables are the HTML specs at https://specifications.openehr.org/releases/ITS-JSON/. Sources are **AsciiDoc** prose; the committed `docs/*.html` files are build artefacts.

`manifest.json` is the source of truth for which documents exist, their `spec_status`, releases, and the `SPECITS` Jira roadmap. Published documents: `ITS-JSON`, `ITS-JSON-Overview`.

ITS-JSON publishes JSON Schema (draft-07) renditions of the openEHR models, one set per component release, under `components/<COMPONENT>/Release-X.Y.Z/`: currently RM (1.0.3, 1.0.4, 1.1.0), AM (1.4, 2.1.0, 2.2.0) and BASE (1.1.0). The schemas are derived from the RM, AM and BASE specifications, so a change there may need a new schema release here. The Apache 2.0 licence applies because these are implementation artefacts, not prose specifications. The JSON serialisation rules the schemas implement are described in the ITS-REST and RM specifications.

## Layout

- `docs/<document>/master.adoc` plus `masterNN-*.adoc` chapters (`master00` = amendment record); `manifest_vars.adoc` is generated from `manifest.json` on publish.
- `.asciidoctorconfig` — attributes for editor previews (`:component:`, `:imagesdir:`).

<!-- openehr-scaffold:begin plugin -->
## Use the `openehr-specs@openehr` plugin

The plugin carries the spec-authoring know-how — **prefer its skills/agents over ad-hoc edits.** Don't re-derive their workflows here. `.claude/settings.json` registers the `openehr` marketplace and enables the plugin; Claude Code asks you to trust the folder first. To install it by hand: `/plugin marketplace add openEHR/ai-plugins`, then `/plugin install openehr-specs@openehr`.

| Task | Use |
|------|-----|
| Create/edit a spec, chapter, `master.adoc`, or `manifest.json` | skill `openehr-specs:authoring` |
| Spec prose style — overviews, semantics, design rationale | skill `openehr-specs:content-patterns` |
| Amendment record (`master00-amendment_record.adoc`) | skill `openehr-specs:amendment-record` |
| Releases, CR/PR, lifecycle status, Jira workflow | skill `openehr-specs:governance` |
| Quality / convention review of a document | skill `openehr-specs:review` |
| Whole-document convention review (all chapters) | agent `openehr-specs:spec-reviewer` |
| Fact-check class/attribute/function names in prose | agent `openehr-specs:identifier-grounding` |
| Audit `{openehr_*}` attributes + `<<anchor>>` cross-refs | agent `openehr-specs:xref-auditor` |
| Local HTML preview of this component (you run it) | `/openehr-specs:publish ITS-JSON` |
| Check this repo against the standard file set | `/openehr-specs:scaffold` |
<!-- openehr-scaffold:end plugin -->

<!-- openehr-scaffold:begin build -->
## Build tool invocation

Docker is all you need to render the documents. All `specifications-XX` repos, including `specifications-AA_GLOBAL` (boilerplate, references), are cloned as siblings under one parent directory. Render from that parent directory:

```bash
# render HTML. The image is published from specifications-AA_GLOBAL; its entrypoint passes -q
# (package-qualified class files). Use `Release-X.Y.Z` instead of `development` for a release build.
docker run --rm -u $(id -u):$(id -g) -v "$PWD:/documents/" ghcr.io/openehr/asciidoctor development ITS-JSON
```

The build prints `generated <file>` and exits 0 even when includes are missing, so read the log: any `ERROR` or `include file not found` line means incomplete output. It also rewrites the tracked `docs/*.html` artefacts.
<!-- openehr-scaffold:end build -->

<!-- openehr-scaffold:begin conventions -->
## Conventions

### Commit messages

- **Format:** `Changes for <KEY> - <what changed>`, one line, for example `Changes for SPECITS-42 - fix typos in the overview chapter`. Say what changed in the specification, not which file.
- **Several tickets:** join the numbers, `Changes for SPECITS-49/50 - <what changed>`.
- **Every commit that has a Jira ticket carries its key.** Take it from, in order: the user, the branch name (`feat/SPECITS-42-<slug>`), or the Jira issue the task links to (the `atlassian-openehr` MCP server can look an issue up). Never invent or guess a key.
- **Which key:** `SPECITS` for this component (change requests), `SPECPR` for problem reports, `SPECPUB` for publishing and tooling issues, or the owning component's key (`SPECAM`, `SPECRM`, ...) when the change belongs there. See skill `openehr-specs:governance`.
- **No ticket:** write a plain summary without a key and say so in the pull request; do not use a placeholder key.
- Some history, mostly in other components, puts the key last, `<what changed> (SPECITS-42)`. It is understood, but use the leading form here.
- A change to published specification text also needs an amendment-record entry with the same ticket (skill `openehr-specs:amendment-record`).
- Do not stage regenerated `docs/*.html` or generated class tables together with source changes unless the task is to refresh them.

### Branches

- Branch from `master` as `feat/<KEY>-<slug>` or `fix/<KEY>-<slug>`, for example `feat/SPECITS-42-template-id`, and merge to `master` by pull request.
<!-- openehr-scaffold:end conventions -->
