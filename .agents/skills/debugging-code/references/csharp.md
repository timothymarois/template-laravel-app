# Debugging C# and Godot

Use these mechanics with the hypothesis loop in `SKILL.md`. The language's own rules and the
engine's belong to their own guidance; this reference chooses the evidence and explains what each
tool can mislead you about.

## Match the symptom to the first useful evidence

| Symptom | First evidence |
|---|---|
| Build or restore failure | The complete diagnostic, plus an MSBuild binary log of the same invocation |
| Managed exception at runtime | The full exception type, message, **and inner exceptions**, with the managed stack trace |
| The game runs but a node does nothing | Godot's stderr, read whole — a throw inside `_Ready`, `_Process` or a signal handler is logged and swallowed, not fatal |
| `ObjectDisposedException` or a null native pointer | Whether the `Node` was freed while a C# reference to it survived |
| Editor still runs old behaviour after a build | Whether the assembly was rebuilt **and** reloaded; the `.godot/mono/` cache |
| Engine-level crash with no managed frames | The macOS crash report, symbolized, plus `--verbose` engine output |
| Hang or deadlock | All thread stacks from one stop, via `dotnet-dump` or a debugger attach |
| A frame hitch that is not CPU work | Allocation rate and GC counts over the hitch, from `dotnet-counters` |
| Two runs of the same seed diverge | Every unordered container and every float accumulation on the path |
| Test passes alone, fails in the suite | Shared static state and xUnit's parallel collections |

Pick the instrument that can distinguish the leading hypotheses, then reduce the failing input or
call path. Stepping line by line before that decision produces volume without testing a cause.

## Prove what was built, and that it was loaded

Two separate claims. C# projects fail both ways, and the second is the one that wastes hours.

```sh
# The definitive record of what MSBuild actually did. Open with the MSBuild Structured Log Viewer,
# or `dotnet build -bl:x.binlog` then `dotnet msbuild x.binlog -v:diag` to read it as text.
dotnet build <solution> -bl:/tmp/build.binlog

# What the compiler saw, when the diagnostic is not enough.
dotnet build <solution> -v:n

# Prove the restore graph is current rather than cached.
dotnet restore --force-evaluate
```

| Symptom | Cause | Preferred replacement | Proof |
|---|---|---|---|
| A source edit has no effect | Incremental build skipped the project, or the editor loaded the previous assembly | Rebuild, then confirm the assembly's mtime moved | `ls -l` on the built `.dll` postdates the edit |
| Godot shows old behaviour after a successful build | The editor holds the previous assembly; hot reload is not total | Close the editor, build, reopen — or run from the CLI, which always loads fresh | The behaviour changes on a fresh process |
| A new class or `[Export]` is invisible in the editor | The assembly built but the editor has not reloaded, or the generator did not run | Rebuild and reopen; check the source generator's output under `obj/` | The member appears in the inspector |
| `NU1605` or a mysterious version conflict | Two projects pulled different versions of one package | Pin the version in one place | The binlog shows one resolved version |
| Build is clean but the game will not start | The runtime cannot load the target framework | Read the loader error verbatim, not the Godot line above it | A `dotnet --list-runtimes` that includes the required major version |

**Delete `bin/` and `obj/` only after recording what the stale build proved.** They are the evidence
that a stale build was the cause; removing them first turns a solved problem into an unreproducible
one.

## Read a managed exception completely

An unhandled exception inside an engine callback does not stop the process. Godot catches it at the
marshalling boundary, prints it, and carries on to the next frame — so the visible symptom is "the
feature silently does nothing" and the cause is sitting in stderr, scrolled away.

```sh
# Run headless and keep the whole log. Never judge from the last few lines.
/Applications/Godot_mono.app/Contents/MacOS/Godot --headless --path . 2>&1 | tee /tmp/run.log
grep -nE "ERROR|Unhandled|Exception|  at " /tmp/run.log | head -50
```

Record the exception type, the message, **every** `InnerException`, and the managed stack. A
`TargetInvocationException` or `TypeInitializationException` says only that something failed during
a call or a static initializer; its inner exception is the finding. Reporting the outer type alone
is reporting nothing.

## Suspect the native lifetime before the C# one

The most common Godot C# defect has no C# cause. A `Node` is a C# object wrapping a native object,
and `QueueFree()` destroys the native one while the C# reference lives on until the GC collects it.

```csharp
// Bad: the reference is non-null and the object behind it is gone.
_panel.QueueFree();
_panel.Visible = false;          // ObjectDisposedException, or a native null dereference

// Good: ask the engine, not the reference.
if (GodotObject.IsInstanceValid(_panel))
    _panel.Visible = false;
```

The same boundary explains three more symptoms: a signal handler that stops firing after an assembly
reload (the delegate was bound to an assembly that no longer exists); a `static` field that resets
for no visible reason (the editor reloaded the assembly and static state does not survive it); and a
lambda connected to a signal that keeps its captured target alive longer than the scene.

When the stack has no managed frames at all, the fault is in the engine or in marshalling. Symbolize
the crash report and read the faulting thread before assuming the C# is at fault:

```sh
ls ~/Library/Logs/DiagnosticReports/ | grep -i godot | tail -3
/Applications/Godot_mono.app/Contents/MacOS/Godot --verbose --headless --path . 2>&1 | tail -80
```

## Attach a debugger, or take a dump

```sh
# Live counters: allocation rate, GC count and pause time, without changing the build.
dotnet-counters monitor --process-id $(pgrep -n Godot) --counters System.Runtime

# A hang: every thread's stack from one stop.
dotnet-dump collect --process-id $(pgrep -n Godot)
dotnet-dump analyze core.<pid>      # then: clrthreads, clrstack -a, dumpheap -stat

# A managed exception you cannot place: break where it is thrown, not where it is caught.
#   In Rider or VS Code, set the exception breakpoint on the specific type. A first-chance
#   break on every exception is unusable in an engine that throws in ordinary control flow.
```

Godot's own C# debugging is an attach: the editor or CLI exposes the debugger and the IDE connects
to it. Launching the game from the IDE rather than from Godot is what makes breakpoints bind.

At each stop, record the stop reason, the thread, the frame, the arguments, the locals, and one
predicted next observation. Move the breakpoint to the last known-correct boundary instead of
collecting an unbounded step transcript.

**Evaluating an expression in the debugger can change the program.** A property getter is a method
call; watching `Silo.Cells[i]` may allocate or mutate. Prefer raw fields, and treat a behaviour that
only appears while observing as a finding about the observation.

## Intermittency, and the determinism traps that look like it

Hold code and inputs constant and control the sources of nondeterminism. In a simulation the list is
short and specific, and every entry has produced a real defect:

| Source | Why it diverges | What to use instead |
|---|---|---|
| `Dictionary<K,V>` / `HashSet<T>` enumeration | Order depends on hash codes and on insertion *and removal* history, and is explicitly not part of the contract | An ordered collection, or sort a key snapshot before iterating |
| `string` or object `GetHashCode()` | Randomized per process by default | Never hash-order anything the simulation reads |
| `float`/`double` accumulation over a tick | Order of operations changes the low bits | Integer or fixed-point where bits must match |
| `DateTime.Now`, `Environment.TickCount` | Wall clock | The simulation's own tick |
| `Random` without a stored seed | Not reproducible, not saved | RNG state inside the simulation state, serialized with the save |
| `Parallel.For`, `AsParallel` | Completion order, and float reduction order | Keep the simulation single-threaded until measured need says otherwise |
| `[ThreadStatic]` or `static` mutable state | Survives between tests, dies across an assembly reload | Instance state on the simulation |

Capture the seed, the tick count, the order and the environment for every failure. A test that passes
alone and fails in the suite is a shared-static-state finding, not a flake; do not answer it with a
retry.

## Correct the proven cause

Follow `SKILL.md`: the smallest failing proof first, then the narrowest change. Prove the fix the way
the project proves any behavior, and name the language or engine rule the defect violated. Remove
temporary instrumentation and re-run the original reproduction — including, for a stale-build
finding, from a clean `bin/` and `obj/`.
