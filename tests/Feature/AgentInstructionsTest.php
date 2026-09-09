<?php

declare(strict_types=1);

/*
 * `CLAUDE.md` is a byte-identical copy of `AGENTS.md`, not a pointer to it.
 *
 * Two agent runtimes read two different filenames, and a rule that reaches only
 * one of them is worse than no rule: the divergence is silent, and whichever
 * file the next agent happens to load decides how it behaves. Nothing else in
 * the suite reads either file, so without this the two drift the first time one
 * is edited alone — which is exactly how they were found.
 */

it('keeps CLAUDE.md byte-identical to AGENTS.md', function (): void {
    $agents = base_path('AGENTS.md');
    $claude = base_path('CLAUDE.md');

    expect($agents)->toBeReadableFile()
        ->and($claude)->toBeReadableFile();

    $agentsContents = file_get_contents($agents);
    $claudeContents = file_get_contents($claude);

    expect(md5($claudeContents))->toBe(
        md5($agentsContents),
        'AGENTS.md and CLAUDE.md have drifted. They are one document under two names: '
        .'copy AGENTS.md over CLAUDE.md in the same commit that changes it.'
    );
});

/*
 * Rules that have been silently dropped before, pinned so they cannot be again.
 *
 * These do not test agent behaviour — nothing here can. They test that the text an
 * agent is handed still says the thing, because both of these rules were lost by a
 * reasonable-sounding edit rather than by anyone disagreeing with them:
 *
 *  - the no-machine-authorship rule was moved out to a guide in v5.7.0 "now that this
 *    guidance has a dedicated home", after which a fork shipped eight commits carrying
 *    `Co-Authored-By:` trailers;
 *  - the skill-loading rule was phrased purely around *editing files*, so an agent
 *    opening a pull request concluded it did not apply and ran `gh pr create` without
 *    ever loading `managing-github`.
 *
 * A future edit may reword any of this freely. It may not remove the substance.
 */

it('keeps the skill-loading rule covering delivery, not only file edits', function (): void {
    $agents = file_get_contents(base_path('AGENTS.md'));

    // Acting, not only editing — the wording an agent opening a PR must not be able to
    // read itself out of.
    expect($agents)->toContain('before you act on it');
    expect($agents)->toContain('Acting on an area whose skill you never loaded is a');
    // Named at the delivery boundary, so there is nothing to interpret.
    expect($agents)->toContain('managing-github');
    expect($agents)->toContain('Opening a pull request');
});

it('keeps the no-machine-authorship rule in the always-loaded instructions', function (): void {
    $agents = file_get_contents(base_path('AGENTS.md'));

    expect($agents)->toContain('Never sign work as a machine');
    // The trailer is the case that actually ships; name it explicitly.
    expect($agents)->toContain('Co-Authored-By');
});

it('keeps AGENTS.md template-managed rather than fork-customised', function (): void {
    $agents = file_get_contents(base_path('AGENTS.md'));

    expect($agents)->toContain('A fork does not customise these rules');
});
