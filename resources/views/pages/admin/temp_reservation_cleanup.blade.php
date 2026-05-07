<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>TEMP — Reservation Cleanup</title>
<style>
  body { font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; max-width: 960px; margin: 32px auto; padding: 0 16px; color: #1d2433; }
  .banner { background:#fff3cd; border:1px solid #ffe69c; padding:12px 16px; border-radius:6px; margin-bottom:16px; }
  .err { background:#f8d7da; border:1px solid #f1aeb5; padding:12px 16px; border-radius:6px; margin-bottom:16px; }
  .card { border:1px solid #d8dde6; border-radius:8px; padding:16px; margin-bottom:16px; }
  pre { background:#0b1020; color:#dde6ff; padding:14px; border-radius:6px; max-height:600px; overflow:auto; font-size:13px; }
  button { padding:8px 14px; border-radius:6px; border:1px solid #888; cursor:pointer; }
  .btn-secondary { background:#e9ecef; }
  .btn-danger { background:#dc3545; color:#fff; border-color:#b02a37; }
  input[type=text] { padding:7px 10px; border:1px solid #aab; border-radius:6px; }
  form { display:inline-block; margin-right:8px; }
  code { background:#f1f3f7; padding:1px 5px; border-radius:3px; }
  h1 { font-size:20px; }
  p.muted { color:#5a6373; }
</style>
</head>
<body>
  <div class="banner">
    <strong>TEMPORARY DEBUG PAGE — PUBLIC (no auth).</strong>
    Remove the controller, view and routes after the one-off cleanup is complete.
  </div>

  <h1>Manufacturing — stale daily_schedule reservation cleanup</h1>
  <p class="muted">
    Runs <code>manufacturing:cleanup-stale-schedule-reservations</code>.
    Use Dry-run first; confirm the per-product totals; back up the
    <code>inventory_reservations</code> table via the hosting database tool; then Apply.
  </p>

  @if (!empty($error))
    <div class="err">{{ $error }}</div>
  @endif

  <div class="card">
    <form method="POST" action="{{ url('/admin/temp/reservation-cleanup') }}">
      @csrf
      <input type="hidden" name="apply" value="0">
      <button type="submit" class="btn-secondary">Dry-run</button>
    </form>

    <form method="POST" action="{{ url('/admin/temp/reservation-cleanup') }}"
          onsubmit="return confirm('Apply changes to inventory_reservations? This sets matching rows to status=consumed.');">
      @csrf
      <input type="hidden" name="apply" value="1">
      <input type="text" name="confirm" placeholder="Type APPLY" autocomplete="off">
      <button type="submit" class="btn-danger">Apply</button>
    </form>
  </div>

  @if (!is_null($output))
    <div class="card">
      <div><strong>Last run:</strong> {{ $mode }}</div>
      <pre>{{ $output }}</pre>
    </div>
  @endif
</body>
</html>
