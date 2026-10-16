# Debugging C# in Godot

Read [csharp.md](csharp.md) first for build identity, exceptions, dumps and determinism. This page
covers what Godot adds: an engine that swallows callback exceptions, reloads assemblies, and owns the
native object behind every node. Engine rules themselves belong to the engine's own guidance.

## Match the symptom to the first useful evidence

| Symptom | First evidence |
|---|---|
| The game runs but a node does nothing | Godot's stderr, read whole — a throw inside `_Ready`, `_Process` or a signal handler is logged and swallowed, not fatal |
| `ObjectDisposedException` or a null native pointer | Whether the `Node` was freed while a C# reference to it survived |
| Editor still runs old behaviour after a build | Whether the assembly was rebuilt **and** reloaded; the `.godot/mono/` cache |
| Engine-level crash with no managed frames | The OS crash report, symbolized, plus `--verbose` engine output |

## Read the exception Godot swallowed

An unhandled exception inside an engine callback does not stop the process. Godot catches it at the
marshalling boundary, prints it, and carries on to the next frame — so the visible symptom is "the
feature silently does nothing" and the cause is sitting in stderr, scrolled away.

```sh
# Run headless and keep the whole log. Never judge from the last few lines.
<godot-binary> --headless --path . 2>&1 | tee <log-file>
grep -nE "ERROR|Unhandled|Exception|   at " <log-file> | head -50
```

Then read the exception completely, inner exceptions included, as `csharp.md` describes.

## Prove the editor loaded the new assembly

| Symptom | Cause | Preferred replacement | Proof |
|---|---|---|---|
| Godot shows old behaviour after a successful build | The editor holds the previous assembly; hot reload is not total | Close the editor, build, reopen — or run from the CLI, which always loads fresh | The behaviour changes on a fresh process |
| A new class or `[Export]` is invisible in the editor | The assembly built but the editor has not reloaded, or the source generator did not run | Rebuild and reopen; check the generator's output under `obj/` | The member appears in the inspector |

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
the OS crash report and read the faulting thread before assuming the C# is at fault:

```sh
<godot-binary> --verbose --headless --path . 2>&1 | tail -80
```

## Attach the debugger the way Godot expects

Godot's C# debugging is an attach: the editor or CLI exposes the debugger and the IDE connects to
it. Launching the game from the IDE rather than from Godot is what makes breakpoints bind. Use
`dotnet-counters` and `dotnet-dump` against the Godot process id as `csharp.md` describes.

A simulation running inside Godot is where the determinism traps in `csharp.md` bite hardest: keep
RNG state inside the simulation state and serialized with the save, and use the simulation's own
tick rather than a wall clock.
