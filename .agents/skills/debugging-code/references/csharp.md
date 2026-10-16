# Debugging C#

Use these mechanics with the hypothesis loop in `SKILL.md`. The language's own rules belong to its
own guidance; this reference chooses the evidence and explains what each tool can mislead you about.
When Godot hosts the code, read [godot-csharp.md](godot-csharp.md) as well.

## Match the symptom to the first useful evidence

| Symptom | First evidence |
|---|---|
| Build or restore failure | The complete diagnostic, plus an MSBuild binary log of the same invocation |
| Managed exception at runtime | The full exception type, message, **and inner exceptions**, with the managed stack trace |
| A host still runs old behaviour after a build | Whether the assembly was rebuilt **and** reloaded |
| Hang or deadlock | All thread stacks from one stop, via `dotnet-dump` or a debugger attach |
| A latency hitch that is not CPU work | Allocation rate and GC counts over the hitch, from `dotnet-counters` |
| Two runs of the same seed diverge | Every unordered container and every float accumulation on the path |
| Test passes alone, fails in the suite | Shared static state and the test runner's parallel collections |

Pick the instrument that can distinguish the leading hypotheses, then reduce the failing input or
call path. Stepping line by line before that decision produces volume without testing a cause.

## Prove what was built, and that it was loaded

Two separate claims. C# projects fail both ways, and the second is the one that wastes hours.

```sh
# The definitive record of what MSBuild actually did. Open it with the MSBuild Structured Log
# Viewer, or replay it as text with `dotnet msbuild <log-file>.binlog -v:diag`.
dotnet build <solution> -bl:<log-file>.binlog

# What the compiler saw, when the diagnostic is not enough.
dotnet build <solution> -v:n

# Prove the restore graph is current rather than cached.
dotnet restore --force-evaluate
```

| Symptom | Cause | Preferred replacement | Proof |
|---|---|---|---|
| A source edit has no effect | Incremental build skipped the project, or the host loaded the previous assembly | Rebuild, then confirm the assembly's mtime moved | `ls -l` on the built `.dll` postdates the edit |
| `NU1605` or a mysterious version conflict | Two projects pulled different versions of one package | Pin the version in one place | The binlog shows one resolved version |
| Build is clean but the program will not start | The runtime cannot load the target framework | Read the loader error verbatim, not the host's line above it | A `dotnet --list-runtimes` that includes the required major version |

**Delete `bin/` and `obj/` only after recording what the stale build proved.** They are the evidence
that a stale build was the cause; removing them first turns a solved problem into an unreproducible
one.

## Read a managed exception completely

Record the exception type, the message, **every** `InnerException`, and the managed stack. A
`TargetInvocationException` or `TypeInitializationException` says only that something failed during
a call or a static initializer; its inner exception is the finding. Reporting the outer type alone
is reporting nothing.

Keep the whole log of the failing run, then search it; never judge from the last few lines:

```sh
<command> 2>&1 | tee <log-file>
grep -nE "ERROR|Unhandled|Exception|   at " <log-file> | head -50
```

## Attach a debugger, or take a dump

```sh
# Live counters: allocation rate, GC count and pause time, without changing the build.
dotnet-counters monitor --process-id <pid> --counters System.Runtime

# A hang: every thread's stack from one stop.
dotnet-dump collect --process-id <pid>
dotnet-dump analyze <dump-file>     # then: clrthreads, clrstack -a, dumpheap -stat
```

For a managed exception you cannot place, break where it is thrown, not where it is caught: set the
IDE's exception breakpoint on the specific type. A first-chance break on every exception is
unusable in a program that throws in ordinary control flow.

At each stop, record the stop reason, the thread, the frame, the arguments, the locals, and one
predicted next observation. Move the breakpoint to the last known-correct boundary instead of
collecting an unbounded step transcript.

**Evaluating an expression in the debugger can change the program.** A property getter is a method
call; watching an indexer or a lazy property may allocate or mutate. Prefer raw fields, and treat a
behaviour that only appears while observing as a finding about the observation.

## Intermittency, and the determinism traps that look like it

Hold code and inputs constant and control the sources of nondeterminism:

| Source | Why it diverges | What to use instead |
|---|---|---|
| `Dictionary<K,V>` / `HashSet<T>` enumeration | Order depends on hash codes and on insertion *and removal* history, and is explicitly not part of the contract | An ordered collection, or sort a key snapshot before iterating |
| `string` or object `GetHashCode()` | Randomized per process by default | Never hash-order anything the result depends on |
| `float`/`double` accumulation | Order of operations changes the low bits | Integer or fixed-point where bits must match |
| `DateTime.Now`, `Environment.TickCount` | Wall clock | An injected clock or the program's own tick |
| `Random` without a stored seed | Not reproducible, not saved | A seeded generator whose state is part of the saved state |
| `Parallel.For`, `AsParallel` | Completion order, and float reduction order | Keep the deterministic path single-threaded until measured need says otherwise |
| `[ThreadStatic]` or `static` mutable state | Survives between tests and dies across an assembly reload | Instance state |

Capture the seed, the order and the environment for every failure. A test that passes alone and
fails in the suite is a shared-static-state finding, not a flake; do not answer it with a retry.

## Correct the proven cause

Follow `SKILL.md`: the smallest failing proof first, then the narrowest change. Prove the fix the way
the project proves any behavior, and name the language rule the defect violated. Remove temporary
instrumentation and re-run the original reproduction — including, for a stale-build finding, from a
clean `bin/` and `obj/`.
