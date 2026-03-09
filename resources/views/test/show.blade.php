<!doctype html>
<html lang="en">
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
 <title>Tes Psikotest</title>
 <style>
 body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; background:#f7fafc; margin:0; padding:20px; }
 .container { max-width:800px; margin:0 auto; }
 .card { background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:24px; }
 h1 { font-size:18px; color:#0f172a; margin:0 0 20px 0; }
 .info { background:#dbeafe; color:#0c4a6e; padding:12px; border-radius:6px; margin-bottom:20px; font-size:13px; }
 .error-alert { background:#fee2e2; color:#991b1b; padding:12px; border-radius:6px; margin-bottom:20px; font-size:13px; border-left:3px solid #dc2626; }
 form { display:flex; flex-direction:column; }
 .question-section { margin-top:24px; padding-top:24px; border-top:1px solid #e2e8f0; }
 .question-text { background:#f8fafc; border-left:3px solid #003e6f; padding:16px; margin-bottom:16px; color:#0f172a; font-size:14px; }
 .options { margin-bottom:24px; }
 .option { margin-bottom:12px; }
 .option label { display:flex; align-items:flex-start; margin:0; cursor:pointer; }
 .option input[type=radio] { margin-right:8px; cursor:pointer; margin-top:3px; flex-shrink:0; }
 .option label span { cursor:pointer; }
 .option-img { max-width:200px; max-height:140px; border-radius:6px; border:1px solid #e2e8f0; margin-top:6px; display:block; }
 .text-input { width:100%; padding:10px 12px; border:1px solid #cbd5e1; border-radius:6px; font-size:14px; box-sizing:border-box; }
 .text-input:focus { outline:none; border-color:#003e6f; box-shadow:0 0 0 3px rgba(0,62,111,0.1); }
 .btn-submit { background:#003e6f; color:#fff; border:none; padding:14px 20px; border-radius:6px; font-size:15px; font-weight:600; cursor:pointer; margin-top:20px; transition:background 0.2s; }
 .btn-submit:hover { background:#002a4f; }
 .btn-submit:disabled { background:#94a3b8; cursor:not-allowed; }
 .question-counter { font-size:12px; color:#64748b; margin-bottom:6px; }
 .progress-bar { background:#e2e8f0; border-radius:4px; height:6px; margin-bottom:20px; overflow:hidden; }
 .progress-fill { background:#003e6f; height:100%; border-radius:4px; transition:width 0.3s; }
 .timer-bar { background:#003e6f; color:#fff; padding:12px 20px; border-radius:8px; display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; position:sticky; top:10px; z-index:100; box-shadow:0 2px 8px rgba(0,0,0,0.15); }
 .timer-bar .timer-label { font-size:13px; opacity:0.9; }
 .timer-bar .timer-clock { font-size:22px; font-weight:700; font-variant-numeric:tabular-nums; letter-spacing:1px; }
 .timer-bar.warning { background:#dc2626; animation: pulse 1s infinite; }
 @keyframes pulse { 0%,100%{opacity:1;} 50%{opacity:0.85;} }
 .copyright { text-align:center; margin-top:24px; font-size:12px; color:#64748b; }

 /* Sub-test cards */
 .subtest-cards { display:grid; grid-template-columns:1fr; gap:16px; margin:20px 0; }
 .subtest-card { background:#fff; border:2px solid #e2e8f0; border-radius:12px; padding:20px; cursor:pointer; transition:all 0.2s; position:relative; overflow:hidden; }
 .subtest-card:hover { border-color:#003e6f; box-shadow:0 4px 12px rgba(0,62,111,0.1); transform:translateY(-2px); }
 .subtest-card.completed { border-color:#10b981; background:#f0fdf4; }
 .subtest-card.active { border-color:#003e6f; background:#eff6ff; }
 .subtest-card .st-order { font-size:28px; font-weight:800; color:#cbd5e1; position:absolute; top:12px; right:16px; }
 .subtest-card.completed .st-order { color:#10b981; }
 .subtest-card .st-title { font-size:16px; font-weight:600; color:#0f172a; margin-bottom:6px; }
 .subtest-card .st-desc { font-size:12px; color:#64748b; margin-bottom:10px; line-height:1.5; }
 .subtest-card .st-meta { display:flex; gap:12px; font-size:11px; color:#94a3b8; }
 .subtest-card .st-meta span { display:flex; align-items:center; gap:4px; }
 .subtest-card .st-status { display:inline-block; font-size:10px; padding:3px 10px; border-radius:12px; font-weight:600; margin-top:8px; }
 .st-status.pending { background:#f1f5f9; color:#64748b; }
 .st-status.done { background:#d1fae5; color:#065f46; }
 .st-status.in-progress { background:#dbeafe; color:#1e40af; }

 /* Example questions screen */
 .example-screen { display:none; }
 .example-screen.show { display:block; }
 .example-header { background:linear-gradient(135deg, #f59e0b, #d97706); color:#fff; padding:20px; border-radius:12px; margin-bottom:20px; }
 .example-header h2 { margin:0; font-size:18px; }
 .example-header p { margin:6px 0 0; opacity:0.9; font-size:13px; }
 .example-question { background:#fffbeb; border:1px solid #fde68a; border-radius:8px; padding:16px; margin-bottom:14px; }
 .example-question .eq-label { font-size:10px; font-weight:700; color:#92400e; text-transform:uppercase; letter-spacing:1px; margin-bottom:8px; }
 .example-answer { background:#d1fae5; border:1px solid #6ee7b7; padding:10px 14px; border-radius:6px; margin-top:10px; font-size:13px; color:#065f46; }
 .btn-start-test { background:#003e6f; color:#fff; border:none; padding:14px 24px; border-radius:8px; font-size:15px; font-weight:600; cursor:pointer; width:100%; margin-top:16px; }
 .btn-start-test:hover { background:#002a4f; }
 .btn-back-overview { background:#64748b; color:#fff; border:none; padding:10px 16px; border-radius:6px; font-size:13px; cursor:pointer; margin-bottom:16px; }
 .btn-back-overview:hover { background:#475569; }

 /* Test screen per subtest */
 .subtest-test-screen { display:none; }
 .subtest-test-screen.show { display:block; }
 .subtest-test-header { background:#003e6f; color:#fff; padding:14px 20px; border-radius:8px; margin-bottom:16px; display:flex; justify-content:space-between; align-items:center; position:sticky; top:10px; z-index:90; box-shadow:0 2px 8px rgba(0,0,0,0.15); }
 .subtest-test-header h3 { margin:0; font-size:15px; }
 .st-timer { font-size:18px; font-weight:700; font-variant-numeric:tabular-nums; letter-spacing:1px; }
 .subtest-test-header.st-warning { background:#dc2626; animation: pulse 1s infinite; }
 .btn-finish-subtest { background:#10b981; color:#fff; border:none; padding:12px 20px; border-radius:6px; font-size:14px; font-weight:600; cursor:pointer; width:100%; margin-top:16px; }
 .btn-finish-subtest:hover { background:#059669; }

 /* Overview screen */
 .overview-screen { display:block; }
 .overview-screen.hidden { display:none; }

 /* Anti-cheat */
 body { -webkit-user-select:none; -moz-user-select:none; -ms-user-select:none; user-select:none; -webkit-touch-callout:none; touch-action:pan-y; }
 .text-input, textarea { -webkit-user-select:text; -moz-user-select:text; user-select:text; -webkit-touch-callout:default; }
 * { -webkit-tap-highlight-color: transparent; }
 #anti-cheat-warning {
 display:none; position:fixed; top:0; left:0; width:100%; height:100%; z-index:9999;
 background:rgba(0,0,0,0.85); justify-content:center; align-items:center;
 }
 #anti-cheat-warning.show { display:flex; }
 #anti-cheat-warning .acw-box {
 background:#fff; border-radius:12px; padding:32px; max-width:420px; width:90%; text-align:center;
 box-shadow:0 20px 60px rgba(0,0,0,0.3); animation:acw-pop 0.3s ease;
 }
 @keyframes acw-pop { from{transform:scale(0.8);opacity:0} to{transform:scale(1);opacity:1} }
 #anti-cheat-warning .acw-icon { font-size:48px; margin-bottom:12px; }
 #anti-cheat-warning .acw-title { font-size:18px; font-weight:700; color:#dc2626; margin-bottom:8px; }
 #anti-cheat-warning .acw-msg { font-size:13px; color:#475569; line-height:1.6; margin-bottom:16px; }
 #anti-cheat-warning .acw-count { font-size:12px; color:#991b1b; font-weight:600; margin-bottom:16px; background:#fee2e2; padding:8px 12px; border-radius:6px; }
 #anti-cheat-warning .acw-btn { background:#003e6f; color:#fff; border:none; padding:10px 24px; border-radius:6px; font-size:14px; font-weight:600; cursor:pointer; }
 #anti-cheat-warning .acw-btn:hover { background:#002a4f; }
 .violation-badge { background:#dc2626; color:#fff; font-size:11px; padding:4px 10px; border-radius:20px; margin-left:8px; font-weight:600; display:none; }
 .violation-badge.show { display:inline-block; }

 /* Screen-capture content protection overlay */
 .screen-protect {
 position:fixed; top:0; left:0; width:100%; height:100%; z-index:9998;
 background:#fff; pointer-events:none; display:none;
 }
 .screen-protect.active {
 display:block; pointer-events:all;
 }
 /* Screenshot blocked notification */
 .ss-blocked-toast {
 position:fixed; top:20px; left:50%; transform:translateX(-50%);
 background:#dc2626; color:#fff; padding:10px 24px; border-radius:8px;
 font-size:13px; font-weight:600; z-index:10000; display:none;
 box-shadow:0 4px 12px rgba(0,0,0,0.3); animation:acw-pop 0.3s ease;
 }
 .ss-blocked-toast.show { display:block; }
 /* Confirmation modal styles */
 .confirm-modal-backdrop { position:fixed; inset:0; background:rgba(0,0,0,0.5); display:none; align-items:center; justify-content:center; z-index:11000; }
 .confirm-modal-backdrop.show { display:flex; }
 .confirm-modal { background:#fff; border-radius:10px; padding:18px; width:92%; max-width:460px; box-shadow:0 20px 60px rgba(0,0,0,0.25); }
 .confirm-modal h3 { margin:0 0 8px 0; font-size:18px; color:#0f172a; }
 .confirm-modal p { margin:0 0 16px 0; color:#475569; font-size:14px; }
 .confirm-actions { display:flex; gap:10px; justify-content:flex-end; }
 .confirm-btn { padding:10px 14px; border-radius:8px; font-weight:600; cursor:pointer; border:none; }
 .confirm-cancel { background:#e5e7eb; color:#0f172a; }
 .confirm-yes { background:#003e6f; color:#fff; }

 /* Mobile responsive for test page */
 @media (max-width: 768px) {
  body { padding: 10px; }
  .container { max-width: 100%; }
  .card { padding: 16px; }
  h1 { font-size: 16px; margin-bottom: 14px; }
  .info { padding: 10px; font-size: 12px; line-height: 1.7; }
  .timer-bar { flex-direction: column; gap: 4px; padding: 10px 14px; text-align: center; }
  .timer-bar .timer-clock { font-size: 20px; }
  .subtest-test-header { padding: 10px 14px; flex-direction: column; align-items: flex-start; gap: 6px; }
  .subtest-card { padding: 14px; }
  .subtest-card .st-title { font-size: 14px; }
  .subtest-card .st-order { font-size: 22px; }
  .question-text { font-size: 13px; padding: 12px; }
  .option label { gap: 8px; }
  .option-img { max-width: 160px; max-height: 100px; }
  .btn-submit { padding: 13px; font-size: 15px; }
  .btn-finish-subtest { padding: 11px 16px; font-size: 14px; }
  .btn-back-overview { font-size: 12px; padding: 8px 12px; }
  .example-header { padding: 14px; }
  .example-header h2 { font-size: 15px; }
  .example-question { padding: 12px; }
  .confirm-modal { width: 92%; padding: 14px; }
 }
 @media (max-width: 480px) {
  body { padding: 6px; }
  .card { padding: 12px; }
  h1 { font-size: 15px; }
  .timer-bar .timer-clock { font-size: 18px; }
  .subtest-test-header h3 { font-size: 12px; }
  .st-timer { font-size: 14px; }
  .subtest-card { padding: 10px; }
  .subtest-card .st-title { font-size: 13px; }
  .question-text { font-size: 12px; padding: 10px; }
  .example-header { padding: 10px; }
  .example-header h2 { font-size: 14px; }
 }

 /* ===== KRAEPELIN CALCULATOR TEST STYLES ===== */
 .kraepelin-screen { display:none; }
 .kraepelin-screen.show { display:block; }
 .kraepelin-header { background:linear-gradient(135deg, #7c3aed, #5b21b6); color:#fff; padding:14px 20px; border-radius:10px; margin-bottom:16px; display:flex; justify-content:space-between; align-items:center; position:sticky; top:10px; z-index:90; box-shadow:0 2px 8px rgba(0,0,0,0.15); }
 .kraepelin-header h3 { margin:0; font-size:14px; }
 .kraepelin-header .kp-progress { font-size:11px; opacity:0.9; }
 .kraepelin-header .kp-timer { font-size:22px; font-weight:700; font-variant-numeric:tabular-nums; }
 .kraepelin-header.kp-warning { background:#dc2626; animation:pulse 1s infinite; }
 .kraepelin-info { background:#ede9fe; color:#5b21b6; padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:13px; text-align:center; }
 .kraepelin-waiting { text-align:center; padding:40px 20px; }
 .kraepelin-waiting h2 { font-size:20px; color:#5b21b6; margin-bottom:10px; }
 .kraepelin-waiting p { color:#64748b; font-size:14px; }
 .btn-start-kraepelin { background:#7c3aed; color:#fff; border:none; padding:14px 24px; border-radius:8px; font-size:15px; font-weight:600; cursor:pointer; width:100%; margin-top:16px; }
 .btn-start-kraepelin:hover { background:#6d28d9; }
 /* Calculator UI */
 .kp-calc { max-width:360px; margin:0 auto; background:#1e1b2e; border-radius:16px; padding:20px; box-shadow:0 8px 32px rgba(0,0,0,0.25); }
 .kp-calc-display { background:#0f0d1a; border-radius:12px; padding:20px; margin-bottom:16px; text-align:center; }
 .kp-calc-pair-label { font-size:11px; color:#a78bfa; margin-bottom:4px; letter-spacing:1px; text-transform:uppercase; }
 .kp-calc-digits { display:flex; justify-content:center; align-items:center; gap:12px; }
 .kp-calc-digit { font-size:52px; font-weight:800; color:#fff; font-variant-numeric:tabular-nums; width:64px; height:72px; display:flex; align-items:center; justify-content:center; background:#2d2845; border-radius:10px; }
 .kp-calc-plus { font-size:28px; color:#a78bfa; font-weight:700; }
 .kp-calc-answer { margin-top:12px; }
 .kp-calc-answer-display { font-size:42px; font-weight:800; color:#7c3aed; height:56px; display:flex; align-items:center; justify-content:center; background:#2d2845; border-radius:10px; min-width:64px; margin:0 auto; width:80px; letter-spacing:2px; }
 .kp-calc-answer-display.has-val { color:#34d399; }
 .kp-calc-answer-label { font-size:10px; color:#64748b; margin-top:4px; }
 .kp-numpad { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; margin-bottom:12px; }
 .kp-numpad-btn { background:#2d2845; color:#fff; border:none; font-size:24px; font-weight:700; padding:16px 0; border-radius:10px; cursor:pointer; transition:all 0.1s; user-select:none; -webkit-user-select:none; touch-action:manipulation; }
 .kp-numpad-btn:hover { background:#3d3660; }
 .kp-numpad-btn:active, .kp-numpad-btn.pressed { background:#7c3aed; transform:scale(0.95); }
 .kp-numpad-btn.zero { grid-column:span 2; }
 .kp-numpad-btn.del { background:#4a1d3d; color:#f87171; }
 .kp-numpad-btn.del:hover { background:#6b2150; }
 .kp-calc-counter { display:flex; justify-content:space-between; align-items:center; padding:8px 4px; }
 .kp-calc-counter .kp-pair-num { font-size:12px; color:#a78bfa; font-weight:600; }
 .kp-calc-counter .kp-pair-track { display:flex; gap:3px; }
 .kp-calc-counter .kp-dot { width:6px; height:6px; border-radius:50%; background:#2d2845; }
 .kp-calc-counter .kp-dot.done { background:#34d399; }
 .kp-calc-counter .kp-dot.current { background:#7c3aed; }
 @media (max-width: 480px) {
  .kp-calc { max-width:100%; padding:14px; }
  .kp-calc-digit { font-size:40px; width:52px; height:58px; }
  .kp-calc-answer-display { font-size:34px; height:48px; width:64px; }
  .kp-numpad-btn { font-size:20px; padding:14px 0; }
  .kraepelin-header { padding:10px 14px; flex-direction:column; gap:4px; }
 }

 /* ===== DISC PERSONALITY TEST STYLES ===== */
 .disc-screen { display:none; }
 .disc-screen.show { display:block; }
 .disc-header { background:linear-gradient(135deg, #0d9488, #0f766e); color:#fff; padding:14px 20px; border-radius:10px; margin-bottom:16px; display:flex; justify-content:space-between; align-items:center; position:sticky; top:10px; z-index:90; box-shadow:0 2px 8px rgba(0,0,0,0.15); }
 .disc-header h3 { margin:0; font-size:14px; }
 .disc-header .disc-progress { font-size:11px; opacity:0.9; }
 .disc-header .disc-group-num { font-size:18px; font-weight:700; }
 .disc-info { background:#e0f2f1; color:#004d40; padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:13px; text-align:center; }
 .disc-waiting { text-align:center; padding:40px 20px; }
 .disc-waiting h2 { font-size:20px; color:#0d9488; margin-bottom:10px; }
 .disc-waiting p { color:#64748b; font-size:14px; max-width:500px; margin:0 auto; }
 .btn-start-disc { background:#0d9488; color:#fff; border:none; padding:14px 24px; border-radius:8px; font-size:15px; font-weight:600; cursor:pointer; width:100%; margin-top:16px; max-width:360px; }
 .btn-start-disc:hover { background:#0f766e; }
 .disc-card-container { max-width:500px; margin:0 auto; }
 .disc-group-label { text-align:center; font-size:13px; color:#64748b; margin-bottom:12px; font-weight:600; }
 .disc-instruction { text-align:center; font-size:12px; color:#0f766e; margin-bottom:16px; background:#e0f2f1; padding:8px 12px; border-radius:6px; }
 .disc-statement { position:relative; background:#fff; border:2px solid #e2e8f0; border-radius:10px; padding:16px 18px; margin-bottom:10px; cursor:pointer; transition:all 0.2s; user-select:none; -webkit-user-select:none; font-size:14px; color:#1e293b; display:flex; align-items:center; gap:12px; }
 .disc-statement:hover { border-color:#94a3b8; background:#f8fafc; }
 .disc-statement.selected-most { border-color:#059669; background:#ecfdf5; }
 .disc-statement.selected-least { border-color:#dc2626; background:#fef2f2; }
 .disc-statement .disc-badge { display:none; font-size:10px; font-weight:700; padding:3px 8px; border-radius:6px; white-space:nowrap; flex-shrink:0; }
 .disc-statement.selected-most .disc-badge.badge-most { display:inline-block; background:#059669; color:#fff; }
 .disc-statement.selected-least .disc-badge.badge-least { display:inline-block; background:#dc2626; color:#fff; }
 .disc-statement .disc-num { width:28px; height:28px; border-radius:50%; background:#f1f5f9; color:#64748b; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; flex-shrink:0; }
 .disc-statement.selected-most .disc-num { background:#059669; color:#fff; }
 .disc-statement.selected-least .disc-num { background:#dc2626; color:#fff; }
 .disc-statement .disc-text { flex:1; }
 .disc-nav { display:flex; gap:10px; margin-top:20px; max-width:500px; margin-left:auto; margin-right:auto; }
 .disc-nav button { flex:1; padding:12px; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer; transition:all 0.2s; }
 .disc-nav .disc-prev { background:#f1f5f9; color:#334155; }
 .disc-nav .disc-prev:hover { background:#e2e8f0; }
 .disc-nav .disc-prev:disabled { opacity:0.4; cursor:not-allowed; }
 .disc-nav .disc-next { background:#0d9488; color:#fff; }
 .disc-nav .disc-next:hover { background:#0f766e; }
 .disc-nav .disc-next:disabled { opacity:0.4; cursor:not-allowed; }
 .disc-progress-bar { max-width:500px; margin:0 auto 16px; height:6px; background:#e2e8f0; border-radius:3px; overflow:hidden; }
 .disc-progress-fill { height:100%; background:linear-gradient(90deg, #0d9488, #14b8a6); border-radius:3px; transition:width 0.3s; }
 .disc-legend { display:flex; justify-content:center; gap:16px; margin-bottom:12px; font-size:11px; color:#64748b; }
 .disc-legend span { display:flex; align-items:center; gap:4px; }
 .disc-legend .dot-most { width:10px; height:10px; border-radius:50%; background:#059669; }
 .disc-legend .dot-least { width:10px; height:10px; border-radius:50%; background:#dc2626; }
 @media (max-width: 480px) {
  .disc-card-container { max-width:100%; }
  .disc-statement { padding:12px 14px; font-size:13px; }
  .disc-header { padding:10px 14px; flex-direction:column; gap:4px; }
 }

 /* ===== PAPIKOSTIK PERSONALITY TEST STYLES ===== */
 .papi-screen { display:none; }
 .papi-screen.show { display:block; }
 .papi-header { background:linear-gradient(135deg, #7c3aed, #6d28d9); color:#fff; padding:14px 20px; border-radius:10px; margin-bottom:16px; display:flex; justify-content:space-between; align-items:center; position:sticky; top:10px; z-index:90; box-shadow:0 2px 8px rgba(0,0,0,0.15); }
 .papi-header h3 { margin:0; font-size:14px; }
 .papi-header .papi-progress { font-size:11px; opacity:0.9; }
 .papi-header .papi-num { font-size:18px; font-weight:700; }
 .papi-info { background:#ede9fe; color:#5b21b6; padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:13px; text-align:center; }
 .papi-waiting { text-align:center; padding:40px 20px; }
 .papi-waiting h2 { font-size:20px; color:#7c3aed; margin-bottom:10px; }
 .papi-waiting p { color:#64748b; font-size:14px; max-width:500px; margin:0 auto; }
 .btn-start-papi { background:#7c3aed; color:#fff; border:none; padding:14px 24px; border-radius:8px; font-size:15px; font-weight:600; cursor:pointer; width:100%; margin-top:16px; max-width:360px; }
 .btn-start-papi:hover { background:#6d28d9; }
 .papi-card-container { max-width:500px; margin:0 auto; }
 .papi-pair-label { text-align:center; font-size:13px; color:#64748b; margin-bottom:12px; font-weight:600; }
 .papi-instruction { text-align:center; font-size:12px; color:#6d28d9; margin-bottom:16px; background:#ede9fe; padding:8px 12px; border-radius:6px; }
 .papi-choice { position:relative; background:#fff; border:2px solid #e2e8f0; border-radius:10px; padding:16px 18px; margin-bottom:10px; cursor:pointer; transition:all 0.2s; user-select:none; -webkit-user-select:none; font-size:14px; color:#1e293b; display:flex; align-items:center; gap:12px; }
 .papi-choice:hover { border-color:#a78bfa; background:#faf5ff; }
 .papi-choice.selected { border-color:#7c3aed; background:#ede9fe; }
 .papi-choice .papi-letter { width:32px; height:32px; border-radius:50%; background:#f1f5f9; color:#64748b; display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:700; flex-shrink:0; }
 .papi-choice.selected .papi-letter { background:#7c3aed; color:#fff; }
 .papi-choice .papi-text { flex:1; }
 .papi-nav { display:flex; gap:10px; margin-top:20px; max-width:500px; margin-left:auto; margin-right:auto; }
 .papi-nav button { flex:1; padding:12px; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer; transition:all 0.2s; }
 .papi-nav .papi-prev { background:#f1f5f9; color:#334155; }
 .papi-nav .papi-prev:hover { background:#e2e8f0; }
 .papi-nav .papi-prev:disabled { opacity:0.4; cursor:not-allowed; }
 .papi-nav .papi-next { background:#7c3aed; color:#fff; }
 .papi-nav .papi-next:hover { background:#6d28d9; }
 .papi-nav .papi-next:disabled { opacity:0.4; cursor:not-allowed; }
 .papi-progress-bar { max-width:500px; margin:0 auto 16px; height:6px; background:#e2e8f0; border-radius:3px; overflow:hidden; }
 .papi-progress-fill { height:100%; background:linear-gradient(90deg, #7c3aed, #a78bfa); border-radius:3px; transition:width 0.3s; }
 @media (max-width: 480px) {
  .papi-card-container { max-width:100%; }
  .papi-choice { padding:12px 14px; font-size:13px; }
  .papi-header { padding:10px 14px; flex-direction:column; gap:4px; }
 }
 </style>
 <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
</head>
<body>
 <!-- Screen capture protection overlay -->
 <div class="screen-protect" id="screenProtect"></div>
 <!-- Screenshot blocked toast -->
 <div class="ss-blocked-toast" id="ssBlockedToast">&#x26D4; Screenshot terdeteksi! Tindakan ini dicatat sebagai pelanggaran.</div>

<!-- Confirmation modal -->
<div class="confirm-modal-backdrop" id="confirmModal">
	<div class="confirm-modal" role="dialog" aria-modal="true" aria-labelledby="confirmModalTitle">
		<h3 id="confirmModalTitle">Konfirmasi Pengiriman</h3>
		<p id="confirmModalMessage">Anda yakin ingin mengirim jawaban?</p>
		<div class="confirm-actions">
			<button type="button" class="confirm-btn confirm-cancel" id="confirmCancelBtn">Batal</button>
			<button type="button" class="confirm-btn confirm-yes" id="confirmYesBtn">Kirim</button>
		</div>
	</div>
</div>

 <div class="container">
 <div class="card">
 <h1>{{ $bank->title }}</h1>
 <div class="info" style="line-height:1.8;">
 Peserta: <strong>{{ $response->participant_name }}</strong><br class="mobile-br">
 NIK: {{ $response->nik }}<br class="mobile-br">
 Dept: {{ $response->department }} &middot; Jabatan: {{ $response->position }}<br class="mobile-br">
 Email: {{ $response->participant_email ?? '-' }} &middot; Telp: {{ $response->phone ?? '-' }}
 @if($bank->duration_minutes)
 <br class="mobile-br"> Waktu: {{ $bank->duration_minutes }} menit
 @endif
 </div>

 @if($remainingSeconds !== null)
 <div class="timer-bar" id="timerBar">
 <div>
 <div class="timer-label"> Sisa Waktu Pengerjaan</div>
 </div>
 <div style="display:flex;align-items:center;gap:8px;">
 <div class="timer-clock" id="timerClock">--:--:--</div>
 <span class="violation-badge" id="violationBadge"></span>
 </div>
 </div>
 @endif

 {{-- Anti-cheat notice --}}
 <div style="background:#fef3c7;color:#92400e;padding:10px 14px;border-radius:6px;margin-bottom:16px;font-size:12px;border-left:3px solid #f59e0b;">
 <strong>Mode Ujian Aktif</strong> — Dilarang berpindah tab, menekan tombol home, berpindah aplikasi, copy/paste, atau meninggalkan halaman. Pelanggaran akan dicatat dan ujian dihentikan otomatis setelah 3x pelanggaran.
 </div>

 @if(session('error'))
 <div class="error-alert"> {{ session('error') }}</div>
 @endif

 @if($errors->any())
 <div class="error-alert">
 <strong>Perhatian:</strong>
 <ul style="margin:6px 0 0 0; padding-left:20px;">
 @foreach($errors->all() as $error)
 <li>{{ $error }}</li>
 @endforeach
 </ul>
 </div>
 @endif

 {{-- ============================================ --}}
 {{-- SUB-TEST MODE --}}
 {{-- ============================================ --}}
 @if($hasSubTests)

 <form method="POST" action="{{ route('test.submit', $response->token) }}" id="testForm">
 @csrf

 {{-- OVERVIEW SCREEN: Sub-test cards --}}
 <div id="overviewScreen" class="overview-screen">
 <div style="text-align:center;margin-bottom:16px;">
 <p style="font-size:13px;color:#64748b;margin:0;">Kerjakan seluruh sub-test di bawah ini. Klik kartu untuk memulai.</p>
 </div>

 <div class="progress-bar">
 <div class="progress-fill" id="progressFill" style="width:0%"></div>
 </div>

 <div class="subtest-cards">
 @foreach($subTests as $stIdx => $subTest)
 <div class="subtest-card" id="stCard{{ $subTest->id }}" onclick="openSubTest({{ $subTest->id }})">
 <div class="st-order">{{ $stIdx + 1 }}</div>
 <div class="st-title">{{ $subTest->title }}</div>
 @if($subTest->description)
 <div class="st-desc">{{ Str::limit($subTest->description, 100) }}</div>
 @endif
 <div class="st-meta">
 @if($subTest->type === 'kraepelin')
 @php $kc = $subTest->kraepelin_config; @endphp
 <span style="background:#ede9fe;color:#5b21b6;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:600;">Kraepelin</span>
 @if($kc)
 <span>{{ $kc['columns_count'] }} kolom</span>
 <span>~{{ round(array_sum($kc['column_durations']) / 60) }} menit</span>
 @endif
 @elseif($subTest->type === 'disc')
 @php $dc = $subTest->disc_config; @endphp
 <span style="background:#e0f2f1;color:#0d9488;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:600;">DISC</span>
 @if($dc)
 <span>{{ $dc['question_count'] }} grup</span>
 <span>~10-15 menit</span>
 @endif
 @elseif($subTest->type === 'papikostik')
 @php $pc = $subTest->papikostik_config; @endphp
 <span style="background:#ede9fe;color:#7c3aed;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:600;">PAPIKOSTIK</span>
 @if($pc)
 <span>{{ $pc['question_count'] }} pasang</span>
 <span>~15-20 menit</span>
 @endif
 @else
 <span> {{ $subTest->questions->count() }} soal</span>
 @if($subTest->duration_minutes)
 <span> {{ $subTest->duration_minutes }} menit</span>
 @endif
 @if($subTest->exampleQuestions->count() > 0)
 <span> {{ $subTest->exampleQuestions->count() }} contoh</span>
 @endif
 @endif
 </div>
 <div class="st-status pending" id="stStatus{{ $subTest->id }}">Belum Dikerjakan</div>
 </div>
 @endforeach
 </div>

 <button type="submit" class="btn-submit" id="submitBtn" style="margin-top:16px;">Selesaikan Semua Tes</button>
 </div>

 {{-- PER-SUBTEST SCREENS --}}
 @foreach($subTests as $subTest)

 @if($subTest->type === 'kraepelin')
 {{-- KRAEPELIN TEST SCREEN --}}
 @php $kc = $subTest->kraepelin_config ?? []; @endphp
 <div class="kraepelin-screen" id="kraepelinScreen{{ $subTest->id }}">
 <button type="button" class="btn-back-overview" onclick="backToOverview()">← Kembali ke Daftar Sub-Test</button>

 <div class="kraepelin-info">
 <strong>Tes Kraepelin — {{ $subTest->title }}</strong><br>
 Jumlahkan setiap pasangan angka yang berdekatan, lalu masukkan <strong>angka satuan</strong> hasilnya menggunakan tombol angka.<br>
 Contoh: 7 + 8 = 15 → tekan <strong>5</strong>. Kolom berganti otomatis saat waktu habis.
 @if($subTest->description)
 <div style="margin-top:8px;font-style:italic;">{{ $subTest->description }}</div>
 @endif
 </div>

 <div class="kraepelin-waiting" id="kraepelinWaiting{{ $subTest->id }}">
 <h2>Siap Memulai?</h2>
 <p>Tes ini terdiri dari {{ $kc['columns_count'] ?? 0 }} kolom angka.<br>Setiap kolom memiliki batas waktu berbeda. Kerjakan secepat dan seteliti mungkin.</p>
 <button type="button" class="btn-start-kraepelin" onclick="startKraepelin({{ $subTest->id }})">
 Mulai Tes Kraepelin →
 </button>
 </div>

 <div id="kraepelinActive{{ $subTest->id }}" style="display:none;">
 <div class="kraepelin-header" id="kpHeader{{ $subTest->id }}">
 <div>
 <h3>{{ $subTest->title }}</h3>
 <div class="kp-progress" id="kpProgress{{ $subTest->id }}">Kolom 1/{{ $kc['columns_count'] ?? 0 }} — Pasangan 1</div>
 </div>
 <div class="kp-timer" id="kpTimer{{ $subTest->id }}">--</div>
 </div>
 <div id="kpContainer{{ $subTest->id }}"></div>
 </div>

 {{-- Hidden field to store kraepelin JSON data --}}
 <input type="hidden" name="kraepelin_data[{{ $subTest->id }}]" id="kraepelinData{{ $subTest->id }}" value="">
 </div>

 <script>
 (function(){
  window['kraepelinConfig_{{ $subTest->id }}'] = @json($kc);
 })();
 </script>
 @elseif($subTest->type === 'disc')
 {{-- DISC PERSONALITY TEST SCREEN --}}
 @php $dc = $subTest->disc_config ?? []; @endphp
 <div class="disc-screen" id="discScreen{{ $subTest->id }}">
 <button type="button" class="btn-back-overview" onclick="backToOverview()">← Kembali ke Daftar Sub-Test</button>

 <div class="disc-info">
 <strong>Tes DISC — {{ $subTest->title }}</strong><br>
 Untuk setiap grup, pilih pernyataan yang <strong>paling menggambarkan diri Anda</strong> dan yang <strong>paling tidak menggambarkan diri Anda</strong>.
 @if($subTest->description)
 <div style="margin-top:8px;font-style:italic;">{{ $subTest->description }}</div>
 @endif
 </div>

 <div class="disc-waiting" id="discWaiting{{ $subTest->id }}">
 <h2>Tes Kepribadian DISC</h2>
 <p>Tes ini terdiri dari {{ $dc['question_count'] ?? 24 }} grup pernyataan.<br>Setiap grup memiliki 4 pernyataan. Pilih yang <strong>paling sesuai</strong> dan <strong>paling tidak sesuai</strong> dengan diri Anda.<br>Tidak ada jawaban benar atau salah. Jawablah dengan jujur.</p>
 <button type="button" class="btn-start-disc" onclick="startDisc({{ $subTest->id }})">
 Mulai Tes DISC →
 </button>
 </div>

 <div id="discActive{{ $subTest->id }}" style="display:none;">
 <div class="disc-header" id="discHeader{{ $subTest->id }}">
 <div>
 <h3>{{ $subTest->title }}</h3>
 <div class="disc-progress" id="discProgress{{ $subTest->id }}">Grup 1/{{ $dc['question_count'] ?? 24 }}</div>
 </div>
 <div class="disc-group-num" id="discGroupNum{{ $subTest->id }}">1</div>
 </div>
 <div class="disc-progress-bar"><div class="disc-progress-fill" id="discProgressFill{{ $subTest->id }}" style="width:0%"></div></div>
 <div class="disc-legend">
 <span><span class="dot-most"></span> Paling Sesuai</span>
 <span><span class="dot-least"></span> Paling Tidak Sesuai</span>
 </div>
 <div class="disc-card-container" id="discContainer{{ $subTest->id }}"></div>
 <div class="disc-nav">
 <button type="button" class="disc-prev" id="discPrev{{ $subTest->id }}" onclick="prevDiscGroup({{ $subTest->id }})" disabled>← Sebelumnya</button>
 <button type="button" class="disc-next" id="discNext{{ $subTest->id }}" onclick="nextDiscGroup({{ $subTest->id }})">Selanjutnya →</button>
 </div>
 </div>

 <input type="hidden" name="disc_data[{{ $subTest->id }}]" id="discData{{ $subTest->id }}" value="">
 </div>

 <script>
 (function(){
  window['discConfig_{{ $subTest->id }}'] = @json($dc);
 })();
 </script>
 @elseif($subTest->type === 'papikostik')
 {{-- PAPIKOSTIK PERSONALITY TEST SCREEN --}}
 @php $pc = $subTest->papikostik_config ?? []; @endphp
 <div class="papi-screen" id="papiScreen{{ $subTest->id }}">
 <button type="button" class="btn-back-overview" onclick="backToOverview()">← Kembali ke Daftar Sub-Test</button>

 <div class="papi-info">
 <strong>Tes PAPIKOSTIK — {{ $subTest->title }}</strong><br>
 Untuk setiap pasangan, pilih pernyataan yang <strong>paling menggambarkan diri Anda</strong>.
 @if($subTest->description)
 <div style="margin-top:8px;font-style:italic;">{{ $subTest->description }}</div>
 @endif
 </div>

 <div class="papi-waiting" id="papiWaiting{{ $subTest->id }}">
 <h2>Tes Kepribadian PAPIKOSTIK</h2>
 <p>Tes ini terdiri dari {{ $pc['question_count'] ?? 90 }} pasang pernyataan.<br>Pilih salah satu pernyataan (A atau B) yang <strong>paling menggambarkan diri Anda</strong>.<br>Tidak ada jawaban benar atau salah. Jawablah dengan jujur.</p>
 <button type="button" class="btn-start-papi" onclick="startPapi({{ $subTest->id }})">
 Mulai Tes PAPIKOSTIK →
 </button>
 </div>

 <div id="papiActive{{ $subTest->id }}" style="display:none;">
 <div class="papi-header" id="papiHeader{{ $subTest->id }}">
 <div>
 <h3>{{ $subTest->title }}</h3>
 <div class="papi-progress" id="papiProgress{{ $subTest->id }}">Soal 1/{{ $pc['question_count'] ?? 90 }}</div>
 </div>
 <div class="papi-num" id="papiNum{{ $subTest->id }}">1</div>
 </div>
 <div class="papi-progress-bar"><div class="papi-progress-fill" id="papiProgressFill{{ $subTest->id }}" style="width:0%"></div></div>
 <div class="papi-card-container" id="papiContainer{{ $subTest->id }}"></div>
 <div class="papi-nav">
 <button type="button" class="papi-prev" id="papiPrev{{ $subTest->id }}" onclick="prevPapi({{ $subTest->id }})" disabled>← Sebelumnya</button>
 <button type="button" class="papi-next" id="papiNext{{ $subTest->id }}" onclick="nextPapi({{ $subTest->id }})">Selanjutnya →</button>
 </div>
 </div>

 <input type="hidden" name="papikostik_data[{{ $subTest->id }}]" id="papiData{{ $subTest->id }}" value="">
 </div>

 <script>
 (function(){
  window['papiConfig_{{ $subTest->id }}'] = @json($pc);
 })();
 </script>
 @else
 {{-- NORMAL SUB-TEST: Example Questions Screen --}}
 @if($subTest->exampleQuestions->count() > 0)
 <div class="example-screen" id="exampleScreen{{ $subTest->id }}">
 <button type="button" class="btn-back-overview" onclick="backToOverview()">← Kembali</button>
 <div class="example-header">
 <h2> Contoh Soal — {{ $subTest->title }}</h2>
 <p>Pelajari contoh soal berikut sebelum mengerjakan tes. Contoh soal ini <strong>tidak dinilai</strong>.</p>
 @if($subTest->description)
 <p style="margin-top:8px;font-style:italic;">{{ $subTest->description }}</p>
 @endif
 </div>

 @foreach($subTest->exampleQuestions as $eIdx => $eq)
 <div class="example-question">
 <div class="eq-label">Contoh {{ $eIdx + 1 }}</div>
 <div class="question-text" style="border-left-color:#f59e0b;">{{ $eq->question }}</div>

@if($eq->image_data)
<div style="margin:12px 0; text-align:center;">
<img src="{{ route('questions.image', $eq) }}" alt="Gambar contoh" style="max-width:100%; max-height:300px; border-radius:6px;">
</div>
@elseif($eq->image)
@php
	$img = $eq->image;
	if (filter_var($img, FILTER_VALIDATE_URL)) {
		$imgUrl = $img;
	} elseif (\Illuminate\Support\Str::startsWith($img, 'storage/')) {
		$imgUrl = asset($img);
	} else {
		$imgUrl = asset('storage/' . ltrim($img, '/'));
	}
@endphp
<div style="margin:12px 0; text-align:center;">
<img src="{{ $imgUrl }}" alt="Gambar contoh" style="max-width:100%; max-height:300px; border-radius:6px;">
</div>
@endif

 @if($eq->audio)
 <div style="margin:12px 0;">
 <audio controls style="width:100%; max-width:400px;">
 <source src="{{ asset('storage/' . $eq->audio) }}" type="audio/mpeg">
 </audio>
 </div>
 @endif

 @if($eq->type === 'multiple_choice')
 <div style="font-size:13px;">
 @foreach(['A' => $eq->option_a, 'B' => $eq->option_b, 'C' => $eq->option_c, 'D' => $eq->option_d, 'E' => $eq->option_e, 'F' => $eq->option_f] as $k => $opt)
 @if($opt)
 @php $optImgField = 'option_' . strtolower($k) . '_image'; @endphp
 <div class="option" style="padding:6px 0;">
 <strong>{{ $k }}.</strong> {{ $opt }}
 @if($eq->correct_answer === $k) <span style="color:#10b981;font-weight:600;"> ✓ Benar</span> @endif
 @if($eq->$optImgField)
 @php
 $optImg = $eq->$optImgField;
 if (filter_var($optImg, FILTER_VALIDATE_URL)) {
 $optImgUrl = $optImg;
 } elseif (\Illuminate\Support\Str::startsWith($optImg, 'storage/')) {
 $optImgUrl = asset($optImg);
 } else {
 $optImgUrl = asset('storage/' . ltrim($optImg, '/'));
 }
 @endphp
 <div style="margin-top:4px;"><img src="{{ $optImgUrl }}" alt="Opsi {{ $k }}" style="max-width:200px;max-height:120px;border-radius:4px;border:1px solid #e2e8f0;"></div>
 @endif
 </div>
 @endif
 @endforeach
 </div>
 <div class="example-answer"> Jawaban yang benar: <strong>{{ $eq->correct_answer }}</strong></div>
 @elseif($eq->type === 'text')
 <div class="example-answer"> Jawaban yang benar: <strong>{{ $eq->correct_answer_text }}</strong></div>
 @elseif($eq->type === 'survey')
 <div style="font-size:13px;">
 @php $sLabels=['A','B','C','D','E','F']; $sFields=['option_a','option_b','option_c','option_d','option_e','option_f']; @endphp
 @for($si=0; $si<($eq->option_count??2); $si++)
 @if($eq->{$sFields[$si]})
 <div class="option" style="padding:6px 0;"><strong>{{ $sLabels[$si] }}.</strong> {{ $eq->{$sFields[$si]} }}</div>
 @endif
 @endfor
 </div>
 @endif
 </div>
 @endforeach

 <button type="button" class="btn-start-test" onclick="startSubTestQuestions({{ $subTest->id }})">
 Mulai Tes {{ $subTest->title }} →
 </button>
 </div>
 @endif

 {{-- Real Test Questions Screen --}}
 <div class="subtest-test-screen" id="testScreen{{ $subTest->id }}">
 <button type="button" class="btn-back-overview" onclick="backToOverview()">← Kembali ke Daftar Sub-Test</button>
 <div class="subtest-test-header" id="stHeader{{ $subTest->id }}">
 <div>
 <h3 style="margin:0;">{{ $subTest->title }}</h3>
 <span style="font-size:11px;opacity:0.8;">{{ $subTest->questions->count() }} soal</span>
 </div>
 @if($subTest->duration_minutes)
 <div class="st-timer" id="stTimer{{ $subTest->id }}" data-duration="{{ $subTest->duration_minutes }}">{{ sprintf('%02d', $subTest->duration_minutes) }}:00</div>
 @endif
 </div>

 @if($subTest->description)
 <div style="background:#f1f5f9;padding:10px 14px;border-radius:6px;margin-bottom:16px;font-size:12px;color:#475569;">
 {{ $subTest->description }}
 </div>
 @endif

 <div class="question-section" style="border-top:none;margin-top:0;padding-top:0;">
 @foreach($subTest->questions as $qIdx => $question)
 <div class="question-block" data-question="{{ $question->id }}" data-subtest="{{ $subTest->id }}">
 <div class="question-counter">Pertanyaan {{ $qIdx + 1 }} dari {{ $subTest->questions->count() }}</div>
 <div class="question-text">{!! nl2br(e($question->question)) !!}</div>

@if($question->image_data)
<div style="margin:12px 0; text-align:center;">
<img src="{{ route('questions.image', $question) }}" alt="Gambar soal" style="max-width:100%; max-height:400px; border-radius:6px;">
</div>
@elseif($question->image)
@php
	$img = $question->image;
	if (filter_var($img, FILTER_VALIDATE_URL)) {
		$imgUrl = $img;
	} elseif (\Illuminate\Support\Str::startsWith($img, 'storage/')) {
		$imgUrl = asset($img);
	} else {
		$imgUrl = asset('storage/' . ltrim($img, '/'));
	}
@endphp
<div style="margin:12px 0; text-align:center;">
<img src="{{ $imgUrl }}" alt="Gambar soal" style="max-width:100%; max-height:400px; border-radius:6px;">
</div>
@endif

 @if($question->audio)
 <div style="margin:12px 0;">
 <audio controls style="width:100%; max-width:400px;">
 <source src="{{ asset('storage/' . $question->audio) }}" type="audio/mpeg">
 Browser Anda tidak mendukung audio player.
 </audio>
 </div>
 @endif

 @if($question->type === 'text')
 <div style="margin-bottom:24px;">
 <input type="text" name="answers[{{ $question->id }}]" class="text-input answer-input" placeholder="Masukkan jawaban Anda...">
 </div>
 @elseif($question->type === 'narrative')
 <div style="margin-bottom:24px;">
 <textarea name="answers[{{ $question->id }}]" class="text-input answer-input" rows="5" placeholder="Tulis jawaban narasi Anda..." style="resize:vertical; min-height:100px;"></textarea>
 </div>
 @elseif($question->type === 'survey')
 <div class="options">
 @php
 $surveyLabels = ['A','B','C','D','E','F'];
 $surveyFields = ['option_a','option_b','option_c','option_d','option_e','option_f'];
 $optCount = $question->option_count ?? 2;
 @endphp
 @for($si = 0; $si < $optCount; $si++)
 @if($question->{$surveyFields[$si]})
 <div class="option">
 <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
 <input type="radio" name="answers[{{ $question->id }}]" value="{{ $surveyLabels[$si] }}" class="answer-input" style="width:16px; height:16px; accent-color:#003e6f;">
 <span>{{ $question->{$surveyFields[$si]} }}</span>
 </label>
 </div>
 @endif
 @endfor
 </div>
 @else
 <div class="options">
 @foreach(['A' => $question->option_a, 'B' => $question->option_b, 'C' => $question->option_c, 'D' => $question->option_d, 'E' => $question->option_e, 'F' => $question->option_f] as $key => $option)
 @if($option)
 @php $optImgField = 'option_' . strtolower($key) . '_image'; @endphp
 <div class="option">
 <label>
 <input type="radio" name="answers[{{ $question->id }}]" value="{{ $key }}" class="answer-input">
 <span>
 <strong>{{ $key }}.</strong> {{ $option }}
@if($question->$optImgField)
	@php
		$optImg = $question->$optImgField;
		if (filter_var($optImg, FILTER_VALIDATE_URL)) {
			$optImgUrl = $optImg;
		} elseif (\Illuminate\Support\Str::startsWith($optImg, 'storage/')) {
			$optImgUrl = asset($optImg);
		} else {
			$optImgUrl = asset('storage/' . ltrim($optImg, '/'));
		}
	@endphp
	<img src="{{ $optImgUrl }}" alt="Opsi {{ $key }}" class="option-img">
@endif
 </span>
 </label>
 </div>
 @endif
 @endforeach
 </div>
 @endif
 </div>
 @endforeach
 </div>

 <button type="button" class="btn-finish-subtest" onclick="finishSubTest({{ $subTest->id }})">
 Selesai — Kembali ke Daftar Sub-Test
 </button>
 </div>
 @endif
 @endforeach
 </form>

 {{-- ============================================ --}}
 {{-- LEGACY MODE (no sub-tests) --}}
 {{-- ============================================ --}}
 @else
 @if($questions->count() > 0)
 <div class="progress-bar">
 <div class="progress-fill" id="progressFill" style="width:0%"></div>
 </div>

 <form method="POST" action="{{ route('test.submit', $response->token) }}" id="testForm">
 @csrf

 <div class="question-section">
 @foreach($questions as $index => $question)
 <div class="question-block" data-question="{{ $question->id }}">
 <div class="question-counter">Pertanyaan {{ $index + 1 }} dari {{ $questions->count() }}</div>
 <div class="question-text">{!! nl2br(e($question->question)) !!}</div>

@if($question->image_data)
<div style="margin:12px 0; text-align:center;">
<img src="{{ route('questions.image', $question) }}" alt="Gambar soal" style="max-width:100%; max-height:400px; border-radius:6px;">
</div>
@elseif($question->image)
<div style="margin:12px 0; text-align:center;">
<img src="{{ asset('storage/' . $question->image) }}" alt="Gambar soal" style="max-width:100%; max-height:400px; border-radius:6px;">
</div>
@endif

 @if($question->audio)
 <div style="margin:12px 0;">
 <audio controls style="width:100%; max-width:400px;">
 <source src="{{ asset('storage/' . $question->audio) }}" type="audio/mpeg">
 Browser Anda tidak mendukung audio player.
 </audio>
 </div>
 @endif

 @if($question->type === 'text')
 <div style="margin-bottom:24px;">
 <input type="text" name="answers[{{ $question->id }}]" class="text-input answer-input" placeholder="Masukkan jawaban Anda..." required>
 </div>
 @elseif($question->type === 'narrative')
 <div style="margin-bottom:24px;">
 <textarea name="answers[{{ $question->id }}]" class="text-input answer-input" rows="5" placeholder="Tulis jawaban narasi Anda..." style="resize:vertical; min-height:100px;"></textarea>
 </div>
 @elseif($question->type === 'survey')
 <div class="options">
 @php
 $surveyLabels = ['A','B','C','D','E','F'];
 $surveyFields = ['option_a','option_b','option_c','option_d','option_e','option_f'];
 $optCount = $question->option_count ?? 2;
 @endphp
 @for($si = 0; $si < $optCount; $si++)
 @if($question->{$surveyFields[$si]})
 <div class="option">
 <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
 <input type="radio" name="answers[{{ $question->id }}]" value="{{ $surveyLabels[$si] }}" class="answer-input" style="width:16px; height:16px; accent-color:#003e6f;">
 <span>{{ $question->{$surveyFields[$si]} }}</span>
 </label>
 </div>
 @endif
 @endfor
 </div>
 @else
 <div class="options">
 @foreach(['A' => $question->option_a, 'B' => $question->option_b, 'C' => $question->option_c, 'D' => $question->option_d, 'E' => $question->option_e, 'F' => $question->option_f] as $key => $option)
 @if($option)
 @php $optImgField = 'option_' . strtolower($key) . '_image'; @endphp
 <div class="option">
 <label>
 <input type="radio" name="answers[{{ $question->id }}]" value="{{ $key }}" class="answer-input" required>
 <span>
 <strong>{{ $key }}.</strong> {{ $option }}
 @if($question->$optImgField)
 <img src="{{ asset('storage/' . $question->$optImgField) }}" alt="Opsi {{ $key }}" class="option-img">
 @endif
 </span>
 </label>
 </div>
 @endif
 @endforeach
 </div>
 @endif
 </div>
 @endforeach
 </div>

 <button type="submit" class="btn-submit" id="submitBtn">Selesaikan Tes</button>
 </form>
 @else
 <p style="color:#94a3b8; text-align:center; padding:40px;">Bank soal masih kosong. Hubungi administrator.</p>
 @endif
 @endif

 <div class="copyright">copyright &copy;2026 Shindengen HR Internal Team</div>
 </div>
 </div>

 {{-- Anti-Cheat Warning Overlay --}}
 <div id="anti-cheat-warning">
 <div class="acw-box">
 <div class="acw-icon"></div>
 <div class="acw-title">Peringatan Anti-Cheat!</div>
 <div class="acw-msg" id="acw-msg">Anda terdeteksi meninggalkan halaman ujian. Tindakan ini tercatat oleh sistem.</div>
 <div class="acw-count" id="acw-count"></div>
 <button class="acw-btn" id="acw-btn" onclick="dismissWarning()">Kembali ke Ujian</button>
 </div>
 </div>

 <script>
 // === PROGRESS TRACKING ===
 @if($hasSubTests)
 var subTestIds = @json($subTests->pluck('id'));
 var subTestQuestionCounts = @json($subTests->mapWithKeys(function($st) { return [$st->id => $st->questions->count()]; }));
var totalQuestions = Object.values(subTestQuestionCounts).reduce(function(a,b){return a+b;},0);
var completedSubTests = new Set();
var answeredSet = new Set();

// Storage keys namespaced by test token
function _answersKey() { return 'answers_' + TEST_TOKEN_GLOBAL; }
function _completedKey() { return 'completed_subtests_' + TEST_TOKEN_GLOBAL; }
function _violationsKey() { return 'violations_' + TEST_TOKEN_GLOBAL; }

// Load persisted state (answers, completed subtests, violations)
function loadSavedState() {
	try {
		var a = localStorage.getItem(_answersKey());
		if (a) {
			var map = JSON.parse(a);
			Object.keys(map).forEach(function(qid){
				var val = map[qid];
				var input = document.querySelector('.question-block[data-question="' + qid + '"] .answer-input');
				if (input) {
					if (input.type === 'radio') {
						var radio = document.querySelector('.question-block[data-question="' + qid + '"] .answer-input[value="' + val + '"]');
						if (radio) radio.checked = true;
					} else {
						input.value = val;
					}
					if (val !== null && String(val).trim() !== '') answeredSet.add(qid);
				}
			});
		}

		var comp = localStorage.getItem(_completedKey());
		if (comp) {
			var arr = JSON.parse(comp);
			arr.forEach(function(sid){ completedSubTests.add(sid); var card = document.getElementById('stCard' + sid); if (card) card.classList.add('completed'); var st = document.getElementById('stStatus' + sid); if (st) { st.className='st-status done'; st.textContent=' Selesai'; } });
		}

		var vio = localStorage.getItem(_violationsKey());
		if (vio) {
			var obj = JSON.parse(vio);
			if (obj) {
				violationCount = obj.count || 0;
				violationLog = obj.log || [];
				var badge = document.getElementById('violationBadge'); if (badge && violationCount>0) { badge.textContent = ' ' + violationCount + '/'+MAX_VIOLATIONS; badge.classList.add('show'); }
			}
		}
	} catch (e) { console.error('loadSavedState error', e); }
}

function saveAnswersToStorage(qid, val) {
	try {
		var a = {};
		var raw = localStorage.getItem(_answersKey());
		if (raw) a = JSON.parse(raw);
		a[qid] = val;
		localStorage.setItem(_answersKey(), JSON.stringify(a));
	} catch (e) {}
}

function saveCompletedSubtests() {
	try {
		var arr = Array.from(completedSubTests);
		localStorage.setItem(_completedKey(), JSON.stringify(arr));
	} catch (e) {}
}

function saveViolationsToStorage() {
	try {
		var obj = { count: violationCount, log: violationLog };
		localStorage.setItem(_violationsKey(), JSON.stringify(obj));
	} catch (e) {}
}

// Periodic autosave to ensure state persists
setInterval(function(){ try { /* touch storage to ensure it's present */ localStorage.getItem(_answersKey()); } catch(e){} }, 5000);

// Load persisted answers early
document.addEventListener('DOMContentLoaded', function(){ loadSavedState(); updateProgress(); });
 @else
 var totalQuestions = {{ $questions->count() }};
 var answeredSet = new Set();
 @endif

 var progressFill = document.getElementById('progressFill');

// Global token for this test (used for localStorage keys)
var TEST_TOKEN_GLOBAL = '{{ $response->token }}';

 function updateProgress() {
 if (totalQuestions === 0) return;
 var pct = Math.round((answeredSet.size / totalQuestions) * 100);
 if (progressFill) progressFill.style.width = pct + '%';
 }

 document.querySelectorAll('.answer-input').forEach(function(input) {
 var event = input.type === 'radio' ? 'change' : 'input';
	input.addEventListener(event, function() {
	var questionBlock = this.closest('.question-block');
	var questionId = questionBlock.dataset.question;
	if (this.value.trim()) {
		answeredSet.add(questionId);
		// save per-change
		saveAnswersToStorage(questionId, this.value);
	} else {
		answeredSet.delete(questionId);
		saveAnswersToStorage(questionId, '');
	}
	updateProgress();
	});
 });

 @if($hasSubTests)
 // === SUB-TEST NAVIGATION ===
 function hideAllScreens() {
 document.getElementById('overviewScreen').classList.add('hidden');
 document.querySelectorAll('.example-screen').forEach(function(el) { el.classList.remove('show'); });
 document.querySelectorAll('.subtest-test-screen').forEach(function(el) { el.classList.remove('show'); });
 document.querySelectorAll('.kraepelin-screen').forEach(function(el) { el.classList.remove('show'); });
 document.querySelectorAll('.disc-screen').forEach(function(el) { el.classList.remove('show'); });
 document.querySelectorAll('.papi-screen').forEach(function(el) { el.classList.remove('show'); });
 }

 function backToOverview() {
 pauseAllSubTestTimers();
 hideAllScreens();
 document.getElementById('overviewScreen').classList.remove('hidden');
 window.scrollTo(0, 0);
 }

 function openSubTest(stId) {
     // Check if this is a papikostik sub-test
     var papiScreen = document.getElementById('papiScreen' + stId);
     if (papiScreen) {
         hideAllScreens();
         papiScreen.classList.add('show');
         window.scrollTo(0, 0);
         return;
     }

     // Check if this is a disc sub-test
     var discScreen = document.getElementById('discScreen' + stId);
     if (discScreen) {
         hideAllScreens();
         discScreen.classList.add('show');
         window.scrollTo(0, 0);
         return;
     }

     // Check if this is a kraepelin sub-test
     var kraepelinScreen = document.getElementById('kraepelinScreen' + stId);
     if (kraepelinScreen) {
         hideAllScreens();
         kraepelinScreen.classList.add('show');
         window.scrollTo(0, 0);
         return;
     }

     // If there is an example screen for this subtest, show it first.
     var exampleScreen = document.getElementById('exampleScreen' + stId);
     if (exampleScreen) {
         hideAllScreens();
         exampleScreen.classList.add('show');
         window.scrollTo(0, 0);
         return;
     }

     // Otherwise, directly start the questions for the subtest.
     startSubTestQuestions(stId);
 }

// Ensure back buttons are hidden when subtest starts
function startSubTestQuestions(stId) {
	hideAllScreens();
	var testScreen = document.getElementById('testScreen' + stId);
	if (testScreen) {
		testScreen.classList.add('show');
		// hide back buttons to prevent going back while inside subtest
		var backBtns = testScreen.querySelectorAll('.btn-back-overview');
		backBtns.forEach(function(b){ b.style.display = 'none'; });
	}
	window.scrollTo(0, 0);
	// start timer and block navigation while inside subtest
	startSubTestTimer(stId);
	_blockNavigationDuringSubtest(stId);
}

// When entering the actual subtest questions, hide any back buttons
function _hideBackButtonsInSubTest(stId) {
	var testScreen = document.getElementById('testScreen' + stId);
	if (!testScreen) return;
	var backBtns = testScreen.querySelectorAll('.btn-back-overview');
	backBtns.forEach(function(b){ b.style.display = 'none'; });
}

// Navigation block helpers (prevent browser back while inside a subtest)
var _navBlockPopStateHandler = null;
var _navBeforeUnloadHandler = null;
function _blockNavigationDuringSubtest(stId) {
	try {
		history.pushState({subtest: stId}, '');
		_navBlockPopStateHandler = function(e) {
			// Re-push state and warn user
			history.pushState({subtest: stId}, '');
			alert('Tidak dapat kembali saat mengerjakan subtest. Silakan selesaikan subtest terlebih dahulu.');
		};
		window.addEventListener('popstate', _navBlockPopStateHandler);
		_navBeforeUnloadHandler = function(e) {
			e.preventDefault();
			e.returnValue = '';
			return '';
		};
		window.addEventListener('beforeunload', _navBeforeUnloadHandler);
	} catch (e) {}
}

function _unblockNavigationDuringSubtest() {
	try {
		if (_navBlockPopStateHandler) { window.removeEventListener('popstate', _navBlockPopStateHandler); _navBlockPopStateHandler = null; }
		if (_navBeforeUnloadHandler) { window.removeEventListener('beforeunload', _navBeforeUnloadHandler); _navBeforeUnloadHandler = null; }
		try { history.back(); } catch (e) {}
	} catch (e) {}
}

 // === SUB-TEST TIMERS ===
 var subTestTimers = {};
 var subTestRemaining = {};
var TEST_TOKEN_GLOBAL = '{{ $response->token }}';

function _subtestStorageKey(stId) {
	return 'subtest_end_' + TEST_TOKEN_GLOBAL + '_' + stId;
}

function _clearAllSubtestKeys() {
	try {
		for (var i = localStorage.length - 1; i >= 0; i--) {
			var k = localStorage.key(i);
			if (k && k.indexOf('subtest_end_' + TEST_TOKEN_GLOBAL + '_') === 0) {
				localStorage.removeItem(k);
			}
		}
	} catch (e) {}
}

 function startSubTestTimer(stId) {
 var timerEl = document.getElementById('stTimer' + stId);
 if (!timerEl) return;
 // Only initialize remaining/end time on first open
 var durationMin = parseInt(timerEl.getAttribute('data-duration')) || 0;
 if (durationMin <= 0) return;
 var key = _subtestStorageKey(stId);
 var stored = null;
 try { stored = localStorage.getItem(key); } catch (e) { stored = null; }
 var endTs = null;
 if (stored) {
	 endTs = parseInt(stored, 10);
 } else {
	 endTs = Date.now() + (durationMin * 60 * 1000);
	 try { localStorage.setItem(key, String(endTs)); } catch (e) {}
 }
 // Clear any existing interval for this sub-test
 if (subTestTimers[stId]) clearInterval(subTestTimers[stId]);

 var headerEl = document.getElementById('stHeader' + stId);

 function tickSt() {
 var s = Math.max(0, Math.round((endTs - Date.now()) / 1000));
 var m = Math.floor(s / 60);
 var sec = s % 60;
 timerEl.textContent = String(m).padStart(2,'0') + ':' + String(sec).padStart(2,'0');
 if (s <= 60 && headerEl) headerEl.classList.add('st-warning');
 if (s <= 0) {
	 clearInterval(subTestTimers[stId]);
	 subTestTimers[stId] = null;
	 timerEl.textContent = 'WAKTU HABIS';
	 try { localStorage.removeItem(key); } catch (e) {}
	 finishSubTest(stId);
 }
 }

 subTestTimers[stId] = setInterval(tickSt, 1000);
 }

 function pauseSubTestTimer(stId) {
 if (subTestTimers[stId]) {
 clearInterval(subTestTimers[stId]);
 subTestTimers[stId] = null;
 }
 }

 function pauseAllSubTestTimers() {
 for (var id in subTestTimers) {
 if (subTestTimers[id]) {
 clearInterval(subTestTimers[id]);
 subTestTimers[id] = null;
 }
 }
 }

 function finishSubTest(stId) {
 pauseSubTestTimer(stId);
	// mark completed locally
	completedSubTests.add(stId);
	try { saveCompletedSubtests(); } catch (e) {}

	// collect answers for this subtest (include empty answers) and attach as hidden inputs
	try {
		var form = document.getElementById('testForm');
		if (form) {
			// remove any previous hidden markers for this subtest
			var prev = form.querySelectorAll('input[data-subtest-id="' + stId + '"]');
			prev.forEach(function(n){ n.remove(); });

			var qblocks = document.querySelectorAll('.question-block[data-subtest="' + stId + '"]');
			qblocks.forEach(function(qb){
				var qid = qb.dataset.question;
				var input = qb.querySelector('.answer-input');
				var val = '';
				if (input) {
					if (input.type === 'radio') {
						var sel = qb.querySelector('.answer-input:checked');
						val = sel ? sel.value : '';
					} else {
						val = input.value || '';
					}
				} else {
					// multiple inputs (e.g., radio group) - try to find checked
					var sel2 = qb.querySelector('.answer-input:checked');
					val = sel2 ? sel2.value : '';
				}
				var h = document.createElement('input');
				h.type = 'hidden';
				h.name = 'answers[' + qid + ']';
				h.value = val;
				h.setAttribute('data-subtest-id', stId);
				form.appendChild(h);
			});

			// add marker that this subtest was finished
			var mh = document.createElement('input');
			mh.type = 'hidden'; mh.name = 'subtest_completed[' + stId + ']'; mh.value = '1'; mh.setAttribute('data-subtest-id', stId);
			form.appendChild(mh);
		}
	} catch (e) {
		console.error('finishSubTest attach error', e);
	}

	// Update card UI
	var card = document.getElementById('stCard' + stId);
	if (card) { card.classList.remove('active'); card.classList.add('completed'); }
	var status = document.getElementById('stStatus' + stId);
	if (status) { status.className = 'st-status done'; status.textContent = ' Selesai'; }
 // Update card meta with remaining time
 var timerEl = document.getElementById('stTimer' + stId);
 if (timerEl && typeof subTestRemaining[stId] !== 'undefined' && subTestRemaining[stId] <= 0) {
 // Mark as timed out on card
 var metaEl = card ? card.querySelector('.st-meta') : null;
 if (metaEl) {
 var timeoutSpan = document.createElement('span');
 timeoutSpan.style.color = '#dc2626';
 timeoutSpan.style.fontWeight = '600';
 timeoutSpan.textContent = 'Waktu habis';
 metaEl.appendChild(timeoutSpan);
 }
	 }

	 try { localStorage.removeItem(_subtestStorageKey(stId)); } catch (e) {}
	 _unblockNavigationDuringSubtest();
	 backToOverview();
 }

 // === KRAEPELIN CALCULATOR TEST ENGINE ===
 var kraepelinState = {};

 function startKraepelin(stId) {
  var config = window['kraepelinConfig_' + stId];
  if (!config || !config.digits) return;

  var state = {
   stId: stId,
   config: config,
   currentCol: 0,
   currentPair: 0,
   totalCols: config.columns_count,
   colAnswers: [],
   colCorrect: [],
   results: [],
   timer: null
  };
  kraepelinState[stId] = state;

  document.getElementById('kraepelinWaiting' + stId).style.display = 'none';
  document.getElementById('kraepelinActive' + stId).style.display = 'block';
  _blockNavigationDuringSubtest(stId);
  startKraepelinColumn(stId);
 }

 function startKraepelinColumn(stId) {
  var state = kraepelinState[stId];
  if (!state) return;
  if (state.currentCol >= state.totalCols) { finishKraepelin(stId); return; }

  state.currentPair = 0;
  state.colAnswers = [];
  state.colCorrect = [];

  var digits = state.config.digits[state.currentCol];
  state.totalPairs = digits.length - 1;

  renderKraepelinPair(stId);

  // Start column timer
  var duration = state.config.column_durations[state.currentCol];
  var timerEl = document.getElementById('kpTimer' + stId);
  var headerEl = document.getElementById('kpHeader' + stId);
  var remaining = duration;
  timerEl.textContent = remaining;
  headerEl.classList.remove('kp-warning');

  if (state.timer) clearInterval(state.timer);
  state.timer = setInterval(function(){
   remaining--;
   timerEl.textContent = remaining;
   if (remaining <= 5) headerEl.classList.add('kp-warning');
   if (remaining <= 0) {
    clearInterval(state.timer);
    state.timer = null;
    // Fill remaining pairs as unanswered
    while (state.colAnswers.length < state.totalPairs) {
     state.colAnswers.push(null);
     var pi = state.colAnswers.length - 1;
     var d = state.config.digits[state.currentCol];
     state.colCorrect.push((d[pi] + d[pi + 1]) % 10);
    }
    collectKraepelinColumn(stId);
    state.currentCol++;
    if (state.currentCol < state.totalCols) {
     startKraepelinColumn(stId);
    } else {
     finishKraepelin(stId);
    }
   }
  }, 1000);
 }

 function renderKraepelinPair(stId) {
  var state = kraepelinState[stId];
  var col = state.currentCol;
  var pair = state.currentPair;
  var digits = state.config.digits[col];

  // Update progress text
  document.getElementById('kpProgress' + stId).textContent =
   'Kolom ' + (col + 1) + '/' + state.totalCols + ' — Pasangan ' + (pair + 1) + '/' + state.totalPairs;

  var container = document.getElementById('kpContainer' + stId);
  var d1 = digits[pair];
  var d2 = digits[pair + 1];

  // Build progress dots (show max 20 recent)
  var dotsHtml = '';
  var showStart = Math.max(0, pair - 18);
  for (var di = showStart; di <= Math.min(pair, state.totalPairs - 1); di++) {
   var cls = di < pair ? 'kp-dot done' : 'kp-dot current';
   dotsHtml += '<span class="' + cls + '"></span>';
  }

  container.innerHTML =
   '<div class="kp-calc">' +
    '<div class="kp-calc-counter">' +
     '<span class="kp-pair-num">' + (pair + 1) + ' / ' + state.totalPairs + '</span>' +
     '<span class="kp-pair-track">' + dotsHtml + '</span>' +
    '</div>' +
    '<div class="kp-calc-display">' +
     '<div class="kp-calc-pair-label">Jumlahkan angka satuan</div>' +
     '<div class="kp-calc-digits">' +
      '<div class="kp-calc-digit">' + d1 + '</div>' +
      '<div class="kp-calc-plus">+</div>' +
      '<div class="kp-calc-digit">' + d2 + '</div>' +
     '</div>' +
     '<div class="kp-calc-answer">' +
      '<div class="kp-calc-answer-display" id="kpAnswerDisp' + stId + '">_</div>' +
      '<div class="kp-calc-answer-label">Jawaban Anda</div>' +
     '</div>' +
    '</div>' +
    '<div class="kp-numpad" id="kpNumpad' + stId + '">' +
     '<button type="button" class="kp-numpad-btn" data-val="1">1</button>' +
     '<button type="button" class="kp-numpad-btn" data-val="2">2</button>' +
     '<button type="button" class="kp-numpad-btn" data-val="3">3</button>' +
     '<button type="button" class="kp-numpad-btn" data-val="4">4</button>' +
     '<button type="button" class="kp-numpad-btn" data-val="5">5</button>' +
     '<button type="button" class="kp-numpad-btn" data-val="6">6</button>' +
     '<button type="button" class="kp-numpad-btn" data-val="7">7</button>' +
     '<button type="button" class="kp-numpad-btn" data-val="8">8</button>' +
     '<button type="button" class="kp-numpad-btn" data-val="9">9</button>' +
     '<button type="button" class="kp-numpad-btn zero" data-val="0">0</button>' +
     '<button type="button" class="kp-numpad-btn del" data-val="del">⌫</button>' +
    '</div>' +
   '</div>';

  // Attach numpad event listeners
  var numpad = document.getElementById('kpNumpad' + stId);
  numpad.querySelectorAll('.kp-numpad-btn').forEach(function(btn) {
   btn.addEventListener('click', function() {
    kpNumpadPress(stId, this.getAttribute('data-val'));
    this.classList.add('pressed');
    var b = this;
    setTimeout(function(){ b.classList.remove('pressed'); }, 120);
   });
  });

  // Keyboard support
  if (!state._keyHandler) {
   state._keyHandler = function(e) {
    if (e.key >= '0' && e.key <= '9') {
     kpNumpadPress(stId, e.key);
    } else if (e.key === 'Backspace') {
     kpNumpadPress(stId, 'del');
     e.preventDefault();
    }
   };
   document.addEventListener('keydown', state._keyHandler);
  }
 }

 function kpNumpadPress(stId, val) {
  var state = kraepelinState[stId];
  if (!state) return;
  var disp = document.getElementById('kpAnswerDisp' + stId);
  if (!disp) return;

  if (val === 'del') {
   disp.textContent = '_';
   disp.classList.remove('has-val');
   return;
  }

  // Show the digit
  disp.textContent = val;
  disp.classList.add('has-val');

  // Record answer and auto-advance after brief delay
  var digits = state.config.digits[state.currentCol];
  var expected = (digits[state.currentPair] + digits[state.currentPair + 1]) % 10;
  state.colAnswers.push(parseInt(val, 10));
  state.colCorrect.push(expected);

  setTimeout(function() {
   state.currentPair++;
   if (state.currentPair >= state.totalPairs) {
    // Column complete early - collect and move on
    clearInterval(state.timer);
    state.timer = null;
    collectKraepelinColumn(stId);
    state.currentCol++;
    if (state.currentCol < state.totalCols) {
     startKraepelinColumn(stId);
    } else {
     finishKraepelin(stId);
    }
   } else {
    renderKraepelinPair(stId);
   }
  }, 150);
 }

 function collectKraepelinColumn(stId) {
  var state = kraepelinState[stId];
  var correctCount = 0;
  var attempted = 0;
  for (var i = 0; i < state.colAnswers.length; i++) {
   if (state.colAnswers[i] !== null) {
    attempted++;
    if (state.colAnswers[i] === state.colCorrect[i]) correctCount++;
   }
  }
  state.results.push({
   col: state.currentCol + 1,
   answers: state.colAnswers,
   correct: state.colCorrect,
   correct_count: correctCount,
   attempted: attempted,
   duration: state.config.column_durations[state.currentCol]
  });
 }

 function finishKraepelin(stId) {
  var state = kraepelinState[stId];
  if (!state) return;
  if (state.timer) { clearInterval(state.timer); state.timer = null; }
  if (state._keyHandler) {
   document.removeEventListener('keydown', state._keyHandler);
   state._keyHandler = null;
  }

  // Store results as JSON in hidden field
  var dataField = document.getElementById('kraepelinData' + stId);
  if (dataField) {
   dataField.value = JSON.stringify(state.results);
  }

  // Mark subtest completed
  completedSubTests.add(stId);
  try { saveCompletedSubtests(); } catch(e){}

  // Add subtest completed marker to form
  var form = document.getElementById('testForm');
  if (form) {
   var mh = document.createElement('input');
   mh.type = 'hidden'; mh.name = 'subtest_completed[' + stId + ']'; mh.value = '1'; mh.setAttribute('data-subtest-id', stId);
   form.appendChild(mh);
  }

  // Update card UI
  var card = document.getElementById('stCard' + stId);
  if (card) { card.classList.remove('active'); card.classList.add('completed'); }
  var status = document.getElementById('stStatus' + stId);
  if (status) { status.className = 'st-status done'; status.textContent = 'Selesai'; }

  _unblockNavigationDuringSubtest();
  backToOverview();
 }

 // ===== DISC PERSONALITY TEST ENGINE =====
 var discState = {};

 function startDisc(stId) {
  var config = window['discConfig_' + stId];
  if (!config || !config.questions) return;

  var state = {
   stId: stId,
   config: config,
   currentGroup: 0,
   totalGroups: config.question_count,
   answers: [] // [{group, most, least}]
  };
  // Initialize answers array
  for (var i = 0; i < state.totalGroups; i++) {
   state.answers.push({ group: i + 1, most: null, least: null });
  }
  discState[stId] = state;

  document.getElementById('discWaiting' + stId).style.display = 'none';
  document.getElementById('discActive' + stId).style.display = 'block';
  _blockNavigationDuringSubtest(stId);
  renderDiscGroup(stId);
 }

 function renderDiscGroup(stId) {
  var state = discState[stId];
  if (!state) return;
  var g = state.currentGroup;
  var question = state.config.questions[g];
  var answer = state.answers[g];

  // Update header
  document.getElementById('discProgress' + stId).textContent = 'Grup ' + (g + 1) + '/' + state.totalGroups;
  document.getElementById('discGroupNum' + stId).textContent = (g + 1);

  // Update progress bar
  var pct = Math.round((g / state.totalGroups) * 100);
  document.getElementById('discProgressFill' + stId).style.width = pct + '%';

  // Build statement cards
  var container = document.getElementById('discContainer' + stId);
  var html = '<div class="disc-group-label">Grup ' + (g + 1) + ' dari ' + state.totalGroups + '</div>';
  html += '<div class="disc-instruction">Klik sekali untuk <strong>Paling Sesuai</strong>, klik lagi untuk <strong>Paling Tidak Sesuai</strong>, klik ketiga untuk membatalkan.</div>';

  for (var i = 0; i < question.statements.length; i++) {
   var st = question.statements[i];
   var cls = 'disc-statement';
   if (answer.most === st.trait) cls += ' selected-most';
   if (answer.least === st.trait) cls += ' selected-least';

   html += '<div class="' + cls + '" data-trait="' + st.trait + '" onclick="selectDiscStatement(' + stId + ', \'' + st.trait + '\')">';
   html += '<div class="disc-num">' + (i + 1) + '</div>';
   html += '<div class="disc-text">' + st.text + '</div>';
   html += '<span class="disc-badge badge-most">PALING SESUAI</span>';
   html += '<span class="disc-badge badge-least">PALING TIDAK SESUAI</span>';
   html += '</div>';
  }
  container.innerHTML = html;

  // Update nav buttons
  var prevBtn = document.getElementById('discPrev' + stId);
  var nextBtn = document.getElementById('discNext' + stId);
  prevBtn.disabled = (g === 0);

  if (g === state.totalGroups - 1) {
   nextBtn.textContent = 'Selesaikan Tes DISC';
   nextBtn.style.background = '#059669';
  } else {
   nextBtn.textContent = 'Selanjutnya \u2192';
   nextBtn.style.background = '#0d9488';
  }

  // Enable/disable next based on selection
  updateDiscNextBtn(stId);
 }

 function selectDiscStatement(stId, trait) {
  var state = discState[stId];
  if (!state) return;
  var answer = state.answers[state.currentGroup];

  if (answer.most === trait) {
   // clicking same as most -> move to least (if not already least)
   answer.most = null;
   // do not auto-assign least, just deselect most
  } else if (answer.least === trait) {
   // clicking same as least -> deselect least
   answer.least = null;
  } else if (answer.most === null) {
   // no most selected -> set as most
   answer.most = trait;
  } else if (answer.least === null) {
   // most is set, no least -> set as least
   answer.least = trait;
  } else {
   // both set, clicking a third one -> replace least
   answer.least = trait;
  }

  renderDiscGroup(stId);
 }

 function updateDiscNextBtn(stId) {
  var state = discState[stId];
  var answer = state.answers[state.currentGroup];
  var nextBtn = document.getElementById('discNext' + stId);
  nextBtn.disabled = !(answer.most && answer.least);
 }

 function nextDiscGroup(stId) {
  var state = discState[stId];
  if (!state) return;
  var answer = state.answers[state.currentGroup];
  if (!answer.most || !answer.least) return;

  if (state.currentGroup >= state.totalGroups - 1) {
   finishDisc(stId);
   return;
  }

  state.currentGroup++;
  renderDiscGroup(stId);
  window.scrollTo(0, 0);
 }

 function prevDiscGroup(stId) {
  var state = discState[stId];
  if (!state || state.currentGroup <= 0) return;
  state.currentGroup--;
  renderDiscGroup(stId);
  window.scrollTo(0, 0);
 }

 function finishDisc(stId) {
  var state = discState[stId];
  if (!state) return;

  // Calculate scores
  var scores = {
   D: { most: 0, least: 0 },
   I: { most: 0, least: 0 },
   S: { most: 0, least: 0 },
   C: { most: 0, least: 0 }
  };

  for (var i = 0; i < state.answers.length; i++) {
   var a = state.answers[i];
   if (a.most && scores[a.most]) scores[a.most].most++;
   if (a.least && scores[a.least]) scores[a.least].least++;
  }

  // Determine profile type (highest most score)
  var traits = ['D', 'I', 'S', 'C'];
  var maxMost = 0;
  var profileType = '';
  for (var t = 0; t < traits.length; t++) {
   if (scores[traits[t]].most > maxMost) {
    maxMost = scores[traits[t]].most;
    profileType = traits[t];
   }
  }
  // Add secondary (second highest)
  var secondMax = 0;
  var secondType = '';
  for (var t = 0; t < traits.length; t++) {
   if (traits[t] !== profileType && scores[traits[t]].most > secondMax) {
    secondMax = scores[traits[t]].most;
    secondType = traits[t];
   }
  }
  if (secondMax > 0) profileType += secondType;

  var result = {
   answers: state.answers,
   scores: scores,
   profile_type: profileType
  };

  // Store in hidden field
  var dataField = document.getElementById('discData' + stId);
  if (dataField) {
   dataField.value = JSON.stringify(result);
  }

  // Mark subtest completed
  completedSubTests.add(stId);
  try { saveCompletedSubtests(); } catch(e){}

  // Add subtest completed marker
  var form = document.getElementById('testForm');
  if (form) {
   var mh = document.createElement('input');
   mh.type = 'hidden'; mh.name = 'subtest_completed[' + stId + ']'; mh.value = '1'; mh.setAttribute('data-subtest-id', stId);
   form.appendChild(mh);
  }

  // Update card UI
  var card = document.getElementById('stCard' + stId);
  if (card) { card.classList.remove('active'); card.classList.add('completed'); }
  var status = document.getElementById('stStatus' + stId);
  if (status) { status.className = 'st-status done'; status.textContent = 'Selesai'; }

  _unblockNavigationDuringSubtest();
  backToOverview();
 }

 // ===== PAPIKOSTIK PERSONALITY TEST ENGINE =====
 var papiState = {};

 function startPapi(stId) {
  var config = window['papiConfig_' + stId];
  if (!config || !config.questions) return;

  var state = {
   stId: stId,
   config: config,
   currentQ: 0,
   totalQ: config.question_count,
   answers: [] // [{number, choice: 'a'|'b'|null}]
  };
  for (var i = 0; i < state.totalQ; i++) {
   state.answers.push({ number: i + 1, choice: null });
  }
  papiState[stId] = state;

  document.getElementById('papiWaiting' + stId).style.display = 'none';
  document.getElementById('papiActive' + stId).style.display = 'block';
  _blockNavigationDuringSubtest(stId);
  renderPapiQ(stId);
 }

 function renderPapiQ(stId) {
  var state = papiState[stId];
  if (!state) return;
  var q = state.currentQ;
  var question = state.config.questions[q];
  var answer = state.answers[q];

  document.getElementById('papiProgress' + stId).textContent = 'Soal ' + (q + 1) + '/' + state.totalQ;
  document.getElementById('papiNum' + stId).textContent = (q + 1);

  var pct = Math.round((q / state.totalQ) * 100);
  document.getElementById('papiProgressFill' + stId).style.width = pct + '%';

  var container = document.getElementById('papiContainer' + stId);
  var html = '<div class="papi-pair-label">Soal ' + (q + 1) + ' dari ' + state.totalQ + '</div>';
  html += '<div class="papi-instruction">Pilih pernyataan yang <strong>paling menggambarkan diri Anda</strong>.</div>';

  var clsA = 'papi-choice' + (answer.choice === 'a' ? ' selected' : '');
  var clsB = 'papi-choice' + (answer.choice === 'b' ? ' selected' : '');

  html += '<div class="' + clsA + '" onclick="selectPapi(' + stId + ', \'a\')">';
  html += '<div class="papi-letter">A</div>';
  html += '<div class="papi-text">' + question.a.text + '</div>';
  html += '</div>';

  html += '<div class="' + clsB + '" onclick="selectPapi(' + stId + ', \'b\')">';
  html += '<div class="papi-letter">B</div>';
  html += '<div class="papi-text">' + question.b.text + '</div>';
  html += '</div>';

  container.innerHTML = html;

  var prevBtn = document.getElementById('papiPrev' + stId);
  var nextBtn = document.getElementById('papiNext' + stId);
  prevBtn.disabled = (q === 0);

  if (q === state.totalQ - 1) {
   nextBtn.textContent = 'Selesaikan Tes PAPIKOSTIK';
   nextBtn.style.background = '#059669';
  } else {
   nextBtn.textContent = 'Selanjutnya \u2192';
   nextBtn.style.background = '#7c3aed';
  }

  nextBtn.disabled = !answer.choice;
 }

 function selectPapi(stId, choice) {
  var state = papiState[stId];
  if (!state) return;
  var answer = state.answers[state.currentQ];
  answer.choice = (answer.choice === choice) ? null : choice;
  renderPapiQ(stId);
 }

 function nextPapi(stId) {
  var state = papiState[stId];
  if (!state) return;
  var answer = state.answers[state.currentQ];
  if (!answer.choice) return;

  if (state.currentQ >= state.totalQ - 1) {
   finishPapi(stId);
   return;
  }

  state.currentQ++;
  renderPapiQ(stId);
  window.scrollTo(0, 0);
 }

 function prevPapi(stId) {
  var state = papiState[stId];
  if (!state || state.currentQ <= 0) return;
  state.currentQ--;
  renderPapiQ(stId);
  window.scrollTo(0, 0);
 }

 function finishPapi(stId) {
  var state = papiState[stId];
  if (!state) return;

  var dims = state.config.dimensions;
  var scores = {};
  for (var d = 0; d < dims.length; d++) {
   scores[dims[d]] = 0;
  }

  for (var i = 0; i < state.answers.length; i++) {
   var a = state.answers[i];
   var q = state.config.questions[i];
   if (a.choice === 'a' && q.a.dim) {
    scores[q.a.dim] = (scores[q.a.dim] || 0) + 1;
   } else if (a.choice === 'b' && q.b.dim) {
    scores[q.b.dim] = (scores[q.b.dim] || 0) + 1;
   }
  }

  var result = {
   answers: state.answers,
   scores: scores
  };

  var dataField = document.getElementById('papiData' + stId);
  if (dataField) {
   dataField.value = JSON.stringify(result);
  }

  completedSubTests.add(stId);
  try { saveCompletedSubtests(); } catch(e){}

  var form = document.getElementById('testForm');
  if (form) {
   var mh = document.createElement('input');
   mh.type = 'hidden'; mh.name = 'subtest_completed[' + stId + ']'; mh.value = '1'; mh.setAttribute('data-subtest-id', stId);
   form.appendChild(mh);
  }

  var card = document.getElementById('stCard' + stId);
  if (card) { card.classList.remove('active'); card.classList.add('completed'); }
  var status = document.getElementById('stStatus' + stId);
  if (status) { status.className = 'st-status done'; status.textContent = 'Selesai'; }

  _unblockNavigationDuringSubtest();
  backToOverview();
 }

 @endif

 // === ANTI-CHEAT SYSTEM ===
 var MAX_VIOLATIONS = 3;
 var violationCount = 0;
 var violationLog = [];
 var acWarning = document.getElementById('anti-cheat-warning');
 var acMsg = document.getElementById('acw-msg');
 var acCount = document.getElementById('acw-count');
 var acBtn = document.getElementById('acw-btn');

 function showViolation(reason) {
 violationCount++;
 violationLog.push({
 type: reason,
 time: new Date().toISOString(),
 count: violationCount
 });
	// persist violations so refresh does not reset them
	try { saveViolationsToStorage(); } catch (e) {}
 var remaining = MAX_VIOLATIONS - violationCount;

 if (violationCount >= MAX_VIOLATIONS) {
 acMsg.textContent = reason + ' Batas pelanggaran tercapai. Ujian akan dikirim otomatis.';
 acCount.textContent = ' Pelanggaran: ' + violationCount + '/' + MAX_VIOLATIONS + ' — UJIAN DIHENTIKAN';
 acBtn.style.display = 'none';
 acWarning.classList.add('show');
 setTimeout(function() { forceSubmit('Anti-cheat: batas pelanggaran tercapai (' + violationCount + 'x)'); }, 2000);
 return;
 }

 acMsg.textContent = reason + ' Jika Anda melakukan ini ' + remaining + ' kali lagi, ujian akan otomatis dikirim.';
 acCount.textContent = ' Pelanggaran: ' + violationCount + '/' + MAX_VIOLATIONS;
 acWarning.classList.add('show');

 var badge = document.getElementById('violationBadge');
 if (badge) { badge.textContent = ' ' + violationCount + '/' + MAX_VIOLATIONS; badge.classList.add('show'); }
 }

 function dismissWarning() {
 acWarning.classList.remove('show');
 }

 function forceSubmit(reason) {
 isSubmitting = true;
 isAutoSubmit = true;
 var btn = document.getElementById('submitBtn');
 if (btn) { btn.disabled = true; btn.textContent = ' Ujian dihentikan...'; }
 appendViolationData(reason);
 setTimeout(function() { document.getElementById('testForm').submit(); }, 500);
 }

 function appendViolationData(note) {
 var form = document.getElementById('testForm');
 var fields = {
 'violation_count': violationCount,
 'violation_log': JSON.stringify(violationLog),
 'anti_cheat_note': note || ''
 };
 for (var key in fields) {
 var existing = form.querySelector('input[name="' + key + '"]');
 if (existing) existing.remove();
 var input = document.createElement('input');
 input.type = 'hidden'; input.name = key; input.value = fields[key];
 form.appendChild(input);
 }
 }

 var lastViolationTime = 0;
 var isAutoSubmit = false;
 var isSubmitting = false;
// timestamp of last detected screenshot (used to avoid double-counting blur/visibility events)
window.__lastScreenshotAt = 0;
 function triggerViolation(reason) {
 if (isSubmitting) return;
 var now = Date.now();
 if (now - lastViolationTime < 1000) return;
 lastViolationTime = now;
 showViolation(reason);
 }
 document.addEventListener('visibilitychange', function() {
 if (document.hidden) {
	 // ignore visibility changes that immediately follow a detected screenshot
	 if (window.__lastScreenshotAt && (Date.now() - window.__lastScreenshotAt) < 2500) return;
	 triggerViolation('Anda terdeteksi berpindah tab atau meninggalkan halaman ujian.');
 }
 });
 window.addEventListener('blur', function() {
 // ignore blur events that immediately follow a detected screenshot (snipping tool may cause blur)
 if (window.__lastScreenshotAt && (Date.now() - window.__lastScreenshotAt) < 2500) return;
 triggerViolation('Anda terdeteksi meninggalkan jendela ujian.');
 });

 document.addEventListener('copy', function(e) { e.preventDefault(); showViolation('Copy tidak diizinkan selama ujian berlangsung.'); });
 document.addEventListener('cut', function(e) { e.preventDefault(); showViolation('Cut tidak diizinkan selama ujian berlangsung.'); });
 document.addEventListener('paste', function(e) { e.preventDefault(); showViolation('Paste tidak diizinkan selama ujian berlangsung.'); });
 document.addEventListener('contextmenu', function(e) { e.preventDefault(); });
 document.addEventListener('keydown', function(e) {
 if (e.ctrlKey && (e.key === 'c' || e.key === 'C' || e.key === 'v' || e.key === 'V' || e.key === 'x' || e.key === 'X' || e.key === 'u' || e.key === 'U' || e.key === 'a' || e.key === 'A')) {
 if ((e.key === 'a' || e.key === 'A') && (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA')) return;
 e.preventDefault();
 }
 if (e.key === 'F12') e.preventDefault();
 if (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'i' || e.key === 'J' || e.key === 'j' || e.key === 'C' || e.key === 'c')) e.preventDefault();
 });
 document.addEventListener('dragstart', function(e) { e.preventDefault(); });

 // === MOBILE-SPECIFIC ANTI-CHEAT ===
 window.addEventListener('pagehide', function() {
 triggerViolation('Anda terdeteksi meninggalkan halaman ujian (home/app switch).');
 });
 window.addEventListener('pageshow', function(e) {
 if (e.persisted) {
 triggerViolation('Anda terdeteksi kembali dari aplikasi lain.');
 }
 });
 var longPressTimer = null;
 document.addEventListener('touchstart', function(e) {
 longPressTimer = setTimeout(function() {
 if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
 e.preventDefault();
 }
 }, 500);
 }, { passive: false });
 document.addEventListener('touchend', function() { clearTimeout(longPressTimer); });
 document.addEventListener('touchmove', function() { clearTimeout(longPressTimer); });
 document.addEventListener('touchstart', function(e) {
 if (e.touches.length > 1) { e.preventDefault(); }
 }, { passive: false });
 var lastWidth = window.innerWidth;
 var lastHeight = window.innerHeight;
 window.addEventListener('resize', function() {
 var w = window.innerWidth, h = window.innerHeight;
 if (w === lastWidth && Math.abs(h - lastHeight) > 100) {
 lastHeight = h;
 return;
 }
 lastWidth = w; lastHeight = h;
 });

// Manual submit with confirmation (custom modal)
document.getElementById('testForm').addEventListener('submit', function(e) {
	if (isAutoSubmit) return;
	// prevent immediate submit to control flow
	e.preventDefault();
	if (isSubmitting) return;
	isSubmitting = true;

	var unanswered = totalQuestions - answeredSet.size;

	// If there are unanswered questions, allow immediate submit (user requested behavior)
	if (unanswered > 0) {
		appendViolationData(violationCount > 0 ? 'Peserta submit manual dengan ' + violationCount + ' pelanggaran' : '');
		var btn = document.getElementById('submitBtn');
		if (btn) { btn.disabled = true; btn.textContent = 'Mengirim...'; }
		// submit form (hidden inputs for finished subtests are attached by finishSubTest)
		document.getElementById('testForm').submit();
		return;
	}

	// No unanswered questions -> show confirmation modal as safeguard
	var modal = document.getElementById('confirmModal');
	var msgEl = document.getElementById('confirmModalMessage');
	msgEl.textContent = 'Yakin ingin mengirimkan jawaban? Anda tidak dapat mengubah jawaban setelah ini.';
	modal.classList.add('show');

	var onCancel = function() {
		modal.classList.remove('show');
		isSubmitting = false;
		cleanup();
	};

	var onYes = function() {
		modal.classList.remove('show');
		// Attach violation data and submit
		appendViolationData(violationCount > 0 ? 'Peserta submit manual dengan ' + violationCount + ' pelanggaran' : '');
		var btn = document.getElementById('submitBtn');
		if (btn) { btn.disabled = true; btn.textContent = 'Mengirim...'; }
		cleanup();
		document.getElementById('testForm').submit();
	};

	function cleanup() {
		document.getElementById('confirmCancelBtn').removeEventListener('click', onCancel);
		document.getElementById('confirmYesBtn').removeEventListener('click', onYes);
	}

	document.getElementById('confirmCancelBtn').addEventListener('click', onCancel);
	document.getElementById('confirmYesBtn').addEventListener('click', onYes);
});

 // === TIMER ===
 @if($remainingSeconds !== null)
 (function() {
var remaining = {{ (int) $remainingSeconds }};
var timerClock = document.getElementById('timerClock');
var timerBar = document.getElementById('timerBar');
var TEST_TOKEN = '{{ $response->token }}';
var TEST_END_KEY = 'test_end_' + TEST_TOKEN;
// use stored end time if present so timer continues across reloads/background
var _testEnd = null;
var storedEnd = null;
try { storedEnd = localStorage.getItem(TEST_END_KEY); } catch (e) { storedEnd = null; }
if (storedEnd) {
	_testEnd = parseInt(storedEnd, 10);
} else {
	_testEnd = Date.now() + (remaining * 1000);
	try { localStorage.setItem(TEST_END_KEY, String(_testEnd)); } catch (e) {}
}

 function formatTime(s) {
 if (s < 0) s = 0;
 var h = Math.floor(s / 3600);
 var m = Math.floor((s % 3600) / 60);
 var sec = s % 60;
 return (h > 0 ? String(h).padStart(2,'0') + ':' : '') +
 String(m).padStart(2,'0') + ':' +
 String(sec).padStart(2,'0');
 }

 function tick() {
 var remaining = Math.max(0, Math.round((_testEnd - Date.now()) / 1000));
 timerClock.textContent = formatTime(remaining);
 if (remaining <= 300 && !timerBar.classList.contains('warning')) timerBar.classList.add('warning');
 if (remaining <= 0) {
	 clearInterval(timerInterval);
	 timerClock.textContent = '00:00';
	 try { localStorage.removeItem(TEST_END_KEY); } catch (e) {}
	 autoSubmitForm();
	 return;
 }
 }

 function autoSubmitForm() {
 isAutoSubmit = true;
 var btn = document.getElementById('submitBtn');
 btn.disabled = true;
 btn.textContent = ' Waktu Habis! Mengirim otomatis...';
 timerClock.textContent = 'WAKTU HABIS';
 @if($hasSubTests)
 // Go back to overview before submitting so form is visible
 backToOverview();
 @endif
 setTimeout(function() {
 document.getElementById('testForm').submit();
 }, 1000);
 }

 timerClock.textContent = formatTime(remaining);
 if (remaining <= 300) timerBar.classList.add('warning');
 if (remaining <= 0) {
 autoSubmitForm();
 } else {
 var timerInterval = setInterval(tick, 1000);
 }

 window.addEventListener('beforeunload', function(e) {
 if (remaining <= 0) {
 clearInterval(timerInterval);
 timerClock.textContent = '00:00';
 try { localStorage.removeItem(TEST_END_KEY); } catch (e) {}
 autoSubmitForm();
 return;
 }
 @endif

 // === ANTI-SCREENSHOT SYSTEM ===
 (function() {
 var screenProtect = document.getElementById('screenProtect');
 var ssToast = document.getElementById('ssBlockedToast');
 var ssToastTimer = null;

 // Show toast notification
 function showSSToast() {
 if (ssToastTimer) clearTimeout(ssToastTimer);
 ssToast.classList.add('show');
 ssToastTimer = setTimeout(function() {
 ssToast.classList.remove('show');
 }, 3000);
 }

 // Blank the screen to ruin screenshot — stays white for 2 seconds
 function flashProtect() {
 screenProtect.classList.add('active');
 // Also try to overwrite clipboard with blank
 if (navigator.clipboard && navigator.clipboard.writeText) {
 navigator.clipboard.writeText(' ').catch(function() {});
 }
 setTimeout(function() {
 screenProtect.classList.remove('active');
 }, 2000);
 }

 // Handle screenshot attempt
 function onScreenshotAttempt(method) {
 flashProtect();
 showSSToast();
 // mark time so blur/visibility handlers ignore the immediate follow-up
 window.__lastScreenshotAt = Date.now();
 triggerViolation('Screenshot terdeteksi (' + method + '). Screenshot tidak diizinkan selama ujian.');
 }

 // 1. Block PrintScreen key (keyup catches it more reliably)
 document.addEventListener('keyup', function(e) {
 if (e.key === 'PrintScreen') {
 e.preventDefault();
 onScreenshotAttempt('PrintScreen');
 // Try to clear clipboard
 if (navigator.clipboard && navigator.clipboard.writeText) {
 navigator.clipboard.writeText('').catch(function() {});
 }
 }
 });

 // 2. Block PrintScreen on keydown too
 document.addEventListener('keydown', function(e) {
 if (e.key === 'PrintScreen') {
 e.preventDefault();
 return false;
 }
 // Windows Snipping Tool: Win+Shift+S
 if ((e.metaKey || e.key === 'Meta') && e.shiftKey && (e.key === 's' || e.key === 'S')) {
 e.preventDefault();
 onScreenshotAttempt('Snipping Tool');
 return false;
 }
 // Mac screenshots: Cmd+Shift+3, Cmd+Shift+4, Cmd+Shift+5
 if (e.metaKey && e.shiftKey && (e.key === '3' || e.key === '4' || e.key === '5')) {
 e.preventDefault();
 onScreenshotAttempt('Mac Screenshot');
 return false;
 }
 // Alt+PrintScreen (Windows active window screenshot)
 if (e.altKey && e.key === 'PrintScreen') {
 e.preventDefault();
 onScreenshotAttempt('Alt+PrintScreen');
 return false;
 }
 // Ctrl+PrintScreen
 if (e.ctrlKey && e.key === 'PrintScreen') {
 e.preventDefault();
 onScreenshotAttempt('Ctrl+PrintScreen');
 return false;
 }
 });

 // 3. Monitor clipboard for image content (detects screenshots that bypass key events)
 if (navigator.clipboard && navigator.clipboard.read) {
 var clipboardCheckInterval = setInterval(function() {
 try {
 navigator.permissions.query({ name: 'clipboard-read' }).then(function(result) {
 if (result.state === 'granted') {
 navigator.clipboard.read().then(function(items) {
 for (var i = 0; i < items.length; i++) {
 var types = items[i].types;
 for (var j = 0; j < types.length; j++) {
 if (types[j].indexOf('image') !== -1) {
 navigator.clipboard.writeText('').catch(function() {});
 onScreenshotAttempt('Clipboard Image');
 }
 }
 }
 }).catch(function() {});
 }
 }).catch(function() {});
 } catch(ex) {}
 }, 3000);
 }

 // 4. Detect screen sharing / screen capture API
 if (navigator.mediaDevices && navigator.mediaDevices.getDisplayMedia) {
 var origGetDisplayMedia = navigator.mediaDevices.getDisplayMedia.bind(navigator.mediaDevices);
 navigator.mediaDevices.getDisplayMedia = function() {
 onScreenshotAttempt('Screen Sharing');
 return Promise.reject(new Error('Screen capture blocked during exam'));
 };
 }

 // 5. CSS-based protection: hide content on print
 var printStyle = document.createElement('style');
 printStyle.textContent = '@media print { body { display:none !important; } body::after { content:"Screenshot/Print tidak diizinkan selama ujian"; display:block; font-size:24px; text-align:center; padding:100px; color:#dc2626; } }';
 document.head.appendChild(printStyle);

 // 6. Block Ctrl+P (print)
 document.addEventListener('keydown', function(e) {
 if ((e.ctrlKey || e.metaKey) && (e.key === 'p' || e.key === 'P')) {
 e.preventDefault();
 onScreenshotAttempt('Print');
 return false;
 }
 });
 })();

 </script>
</body>
</html>
