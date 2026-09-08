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
