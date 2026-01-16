/* Mini Leave App (client-only)
 * Purpose: teach core concepts (roles, state machine, validation, “DB” writes)
 * Data is stored in localStorage under: leaveMiniApp:v1
 */

const STORAGE_KEY = "leaveMiniApp:v1";

function todayISO() {
  const d = new Date();
  const yyyy = d.getFullYear();
  const mm = String(d.getMonth() + 1).padStart(2, "0");
  const dd = String(d.getDate()).padStart(2, "0");
  return `${yyyy}-${mm}-${dd}`;
}

function addDaysISO(iso, days) {
  const d = new Date(iso + "T00:00:00");
  d.setDate(d.getDate() + days);
  const yyyy = d.getFullYear();
  const mm = String(d.getMonth() + 1).padStart(2, "0");
  const dd = String(d.getDate()).padStart(2, "0");
  return `${yyyy}-${mm}-${dd}`;
}

function toast(msg) {
  const el = document.getElementById("toast");
  el.textContent = msg;
  el.style.display = "block";
  clearTimeout(toast._t);
  toast._t = setTimeout(() => (el.style.display = "none"), 2200);
}

function loadState() {
  const raw = localStorage.getItem(STORAGE_KEY);
  if (raw) return JSON.parse(raw);
  return seedState();
}

function saveState(state) {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
}

function seedState() {
  const year = new Date().getFullYear();
  const state = {
    session: { userId: null },
    departments: [
      { id: 1, name: "Engineering", color: "#8b5cf6", dept_manager_id: 3 },
      { id: 2, name: "Marketing", color: "#22c55e", dept_manager_id: 4 },
    ],
    policies: [
      { id: 1, leave_type: "Annual Leave", annual_entitlement: 20 },
      { id: 2, leave_type: "Sick Leave", annual_entitlement: 10 },
    ],
    users: [
      { id: 1, name: "Alice Employee", email: "alice@demo", role: "employee", department_id: 1, manager_id: 3, password: "password" },
      { id: 2, name: "Bob Employee", email: "bob@demo", role: "employee", department_id: 2, manager_id: 4, password: "password" },
      { id: 3, name: "Maya Manager", email: "maya@demo", role: "dept_manager", department_id: 1, manager_id: null, password: "password" },
      { id: 4, name: "Mark Manager", email: "mark@demo", role: "dept_manager", department_id: 2, manager_id: null, password: "password" },
      { id: 5, name: "Helen HR", email: "hr@demo", role: "hr_admin", department_id: 1, manager_id: null, password: "password" },
    ],
    balances: [],
    requests: [],
    next: { requestId: 1 },
  };

  // Create balances based on policies (like HRController::storeEmployee does)
  for (const u of state.users) {
    if (u.role === "employee" || u.role === "dept_manager") {
      for (const p of state.policies) {
        state.balances.push({
          id: `${u.id}-${p.id}-${year}`,
          user_id: u.id,
          leave_type: p.leave_type,
          balance: p.annual_entitlement,
          used: 0,
          year,
        });
      }
    }
  }

  saveState(state);
  return state;
}

function getUser(state) {
  return state.users.find((u) => u.id === state.session.userId) || null;
}

function requireRole(state, role) {
  const u = getUser(state);
  return !!u && u.role === role;
}

function statusColor(status) {
  if (status === "pending") return "#1C96E1";
  if (status === "dept_manager_approved") return "#f59e0b";
  if (status === "hr_approved") return "#22c55e";
  if (status === "dept_manager_rejected" || status === "hr_rejected") return "#ef4444";
  return "#94a3b8";
}

function humanStatus(status) {
  switch (status) {
    case "pending": return "Pending";
    case "dept_manager_approved": return "Manager Approved";
    case "dept_manager_rejected": return "Manager Rejected";
    case "hr_approved": return "Approved";
    case "hr_rejected": return "Rejected";
    default: return status;
  }
}

function findDept(state, department_id) {
  return state.departments.find((d) => d.id === department_id) || null;
}

function getBalance(state, user_id, leave_type) {
  const year = new Date().getFullYear();
  return state.balances.find((b) => b.user_id === user_id && b.leave_type === leave_type && b.year === year) || null;
}

function availableDays(balance) {
  return Math.max(0, balance.balance - balance.used);
}

function workingDaysInclusive(startISO, endISO) {
  const start = new Date(startISO + "T00:00:00");
  const end = new Date(endISO + "T00:00:00");
  let days = 0;
  for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
    const dow = d.getDay(); // 0=Sun,6=Sat
    if (dow !== 0 && dow !== 6) days++;
  }
  return days;
}

function render(state, route) {
  const view = document.getElementById("view");
  const u = getUser(state);

  document.getElementById("who").textContent = u ? `${u.name} (${u.role})` : "Not logged in";

  if (route === "login") {
    view.innerHTML = `
      <div class="card">
        <h2>Login</h2>
        <p class="muted">Choose a demo account (password is <code>password</code>).</p>
        <div class="row">
          <button class="btn small" data-login="alice@demo">Login as Employee (Alice)</button>
          <button class="btn small" data-login="maya@demo">Login as Manager (Maya)</button>
          <button class="btn small" data-login="hr@demo">Login as HR</button>
        </div>
        <div class="hr"></div>
        <form id="loginForm" class="row" style="align-items:flex-end">
          <div class="field" style="flex:1; min-width:220px">
            <label>Email</label>
            <input name="email" placeholder="alice@demo" />
          </div>
          <div class="field" style="flex:1; min-width:220px">
            <label>Password</label>
            <input name="password" type="password" placeholder="password" />
          </div>
          <button class="btn primary" type="submit">Login</button>
        </form>
      </div>
    `;

    view.querySelectorAll("[data-login]").forEach((b) => {
      b.addEventListener("click", () => {
        const email = b.getAttribute("data-login");
        const user = state.users.find((x) => x.email === email);
        state.session.userId = user?.id ?? null;
        saveState(state);
        toast("Logged in");
        go(defaultRouteForRole(getUser(state)));
      });
    });

    view.querySelector("#loginForm").addEventListener("submit", (e) => {
      e.preventDefault();
      const fd = new FormData(e.target);
      const email = String(fd.get("email") || "").trim();
      const password = String(fd.get("password") || "");
      const user = state.users.find((x) => x.email === email && x.password === password);
      if (!user) return toast("Invalid credentials");
      state.session.userId = user.id;
      saveState(state);
      toast("Logged in");
      go(defaultRouteForRole(user));
    });
    return;
  }

  // “Middleware-like guard”
  if (!u) {
    view.innerHTML = `<div class="card"><h2>Not logged in</h2><p class="muted">Go to Login first.</p></div>`;
    return;
  }

  if (route === "employee") {
    if (!requireRole(state, "employee")) {
      view.innerHTML = `<div class="card"><h2>Forbidden</h2><p class="muted">This page is for employees.</p></div>`;
      return;
    }

    const myReqs = state.requests
      .filter((r) => r.employee_id === u.id)
      .sort((a, b) => b.id - a.id);

    const balRows = state.policies.map((p) => {
      const b = getBalance(state, u.id, p.leave_type);
      const avail = b ? availableDays(b) : 0;
      return `<tr><td>${p.leave_type}</td><td>${avail.toFixed(1)} / ${b?.balance ?? 0}</td></tr>`;
    }).join("");

    const reqRows = myReqs.map((r) => {
      const dept = findDept(state, u.department_id);
      const c = statusColor(r.status);
      return `
        <tr>
          <td><span class="badge"><span class="dot" style="background:${c}"></span>${humanStatus(r.status)}</span></td>
          <td>${r.leave_type}</td>
          <td>${r.start_date} → ${r.end_date}</td>
          <td>${r.number_of_days}</td>
          <td><span class="badge"><span class="dot" style="background:${dept?.color || "#94a3b8"}"></span>${dept?.name || "N/A"}</span></td>
        </tr>
      `;
    }).join("");

    view.innerHTML = `
      <div class="card">
        <h2>Employee Dashboard</h2>
        <p class="muted">Submit a leave request (creates a Request row with status <code>pending</code>).</p>
      </div>

      <div class="grid" style="display:grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top:14px">
        <div class="card">
          <h3>Leave balances (this year)</h3>
          <table class="table">
            <thead><tr><th>Type</th><th>Available / Total</th></tr></thead>
            <tbody>${balRows}</tbody>
          </table>
        </div>

        <div class="card">
          <h3>Submit request</h3>
          <form id="reqForm" class="stack">
            <div class="field">
              <label>Leave type</label>
              <select name="leave_type" required>
                ${state.policies.map((p) => `<option value="${p.leave_type}">${p.leave_type}</option>`).join("")}
              </select>
            </div>
            <div class="row">
              <div class="field" style="flex:1">
                <label>Start</label>
                <input name="start_date" type="date" value="${todayISO()}" required />
              </div>
              <div class="field" style="flex:1">
                <label>End</label>
                <input name="end_date" type="date" value="${todayISO()}" required />
              </div>
            </div>
            <div class="field">
              <label>Reason</label>
              <textarea name="reason" required placeholder="Why do you need leave?"></textarea>
            </div>
            <button class="btn primary" type="submit">Submit</button>
          </form>
        </div>
      </div>

      <div class="card" style="margin-top:14px">
        <h3>My requests</h3>
        <table class="table">
          <thead><tr><th>Status</th><th>Type</th><th>Dates</th><th>Days</th><th>Department</th></tr></thead>
          <tbody>${reqRows || `<tr><td colspan="5" class="muted">No requests yet.</td></tr>`}</tbody>
        </table>
      </div>
    `;

    view.querySelector("#reqForm").addEventListener("submit", (e) => {
      e.preventDefault();
      const fd = new FormData(e.target);
      const leave_type = String(fd.get("leave_type"));
      const start_date = String(fd.get("start_date"));
      const end_date = String(fd.get("end_date"));
      const reason = String(fd.get("reason"));

      if (end_date < start_date) return toast("End date must be after start date");

      const days = workingDaysInclusive(start_date, end_date);
      const bal = getBalance(state, u.id, leave_type);
      if (!bal) return toast("No balance for that leave type");
      if (availableDays(bal) < days) return toast(`Insufficient balance. Available: ${availableDays(bal).toFixed(1)} days`);

      state.requests.push({
        id: state.next.requestId++,
        employee_id: u.id,
        leave_type,
        start_date,
        end_date,
        reason,
        number_of_days: days,
        status: "pending",
        approved_by_dept_manager_id: null,
        approved_by_dept_at: null,
        approved_by_hr_id: null,
        approved_by_hr_at: null,
      });
      saveState(state);
      toast("Request submitted (pending)");
      render(state, "employee");
    });
    return;
  }

  if (route === "manager") {
    if (!requireRole(state, "dept_manager")) {
      view.innerHTML = `<div class="card"><h2>Forbidden</h2><p class="muted">This page is for managers.</p></div>`;
      return;
    }

    const myDeptEmployees = state.users.filter((x) => x.role === "employee" && x.department_id === u.department_id);
    const empIds = new Set(myDeptEmployees.map((x) => x.id));
    const pending = state.requests.filter((r) => empIds.has(r.employee_id) && r.status === "pending").sort((a, b) => b.id - a.id);

    view.innerHTML = `
      <div class="card">
        <h2>Manager Dashboard</h2>
        <p class="muted">Approve or reject pending requests for your department.</p>
      </div>
      <div class="card" style="margin-top:14px">
        <h3>Pending requests</h3>
        <table class="table">
          <thead><tr><th>Employee</th><th>Type</th><th>Dates</th><th>Days</th><th>Actions</th></tr></thead>
          <tbody>
            ${pending.map((r) => {
              const emp = state.users.find((x) => x.id === r.employee_id);
              return `
                <tr>
                  <td>${emp?.name || "Unknown"}</td>
                  <td>${r.leave_type}</td>
                  <td>${r.start_date} → ${r.end_date}</td>
                  <td>${r.number_of_days}</td>
                  <td>
                    <button class="btn small primary" data-approve="${r.id}">Approve</button>
                    <button class="btn small danger" data-reject="${r.id}">Reject</button>
                  </td>
                </tr>
              `;
            }).join("") || `<tr><td colspan="5" class="muted">No pending requests.</td></tr>`}
          </tbody>
        </table>
      </div>
    `;

    view.querySelectorAll("[data-approve]").forEach((b) => {
      b.addEventListener("click", () => {
        const id = Number(b.getAttribute("data-approve"));
        const r = state.requests.find((x) => x.id === id);
        if (!r || r.status !== "pending") return toast("Not approvable");
        r.status = "dept_manager_approved";
        r.approved_by_dept_manager_id = u.id;
        r.approved_by_dept_at = new Date().toISOString();
        saveState(state);
        toast("Approved by manager");
        render(state, "manager");
      });
    });

    view.querySelectorAll("[data-reject]").forEach((b) => {
      b.addEventListener("click", () => {
        const id = Number(b.getAttribute("data-reject"));
        const r = state.requests.find((x) => x.id === id);
        if (!r || r.status !== "pending") return toast("Not rejectable");
        r.status = "dept_manager_rejected";
        saveState(state);
        toast("Rejected by manager");
        render(state, "manager");
      });
    });

    return;
  }

  if (route === "hr") {
    if (!requireRole(state, "hr_admin")) {
      view.innerHTML = `<div class="card"><h2>Forbidden</h2><p class="muted">This page is for HR.</p></div>`;
      return;
    }

    const awaiting = state.requests.filter((r) => r.status === "dept_manager_approved").sort((a, b) => b.id - a.id);
    const approved = state.requests.filter((r) => r.status === "hr_approved");

    view.innerHTML = `
      <div class="card">
        <h2>HR Dashboard</h2>
        <p class="muted">Final approval gate. Approving deducts balance and the leave appears on the calendar.</p>
      </div>

      <div class="grid" style="display:grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top:14px">
        <div class="card">
          <h3>Awaiting HR approval</h3>
          <table class="table">
            <thead><tr><th>Employee</th><th>Type</th><th>Dates</th><th>Days</th><th>Actions</th></tr></thead>
            <tbody>
              ${awaiting.map((r) => {
                const emp = state.users.find((x) => x.id === r.employee_id);
                return `
                  <tr>
                    <td>${emp?.name || "Unknown"}</td>
                    <td>${r.leave_type}</td>
                    <td>${r.start_date} → ${r.end_date}</td>
                    <td>${r.number_of_days}</td>
                    <td>
                      <button class="btn small primary" data-hr-approve="${r.id}">Approve</button>
                      <button class="btn small danger" data-hr-reject="${r.id}">Reject</button>
                    </td>
                  </tr>
                `;
              }).join("") || `<tr><td colspan="5" class="muted">Nothing waiting.</td></tr>`}
            </tbody>
          </table>
        </div>

        <div class="card">
          <h3>Calendar (approved only)</h3>
          <div class="muted" style="margin-bottom:10px">Badges show who is on leave per day (dept color).</div>
          <div id="calendar" class="calendar"></div>
        </div>
      </div>
    `;

    view.querySelectorAll("[data-hr-approve]").forEach((b) => {
      b.addEventListener("click", () => {
        const id = Number(b.getAttribute("data-hr-approve"));
        const r = state.requests.find((x) => x.id === id);
        if (!r || r.status !== "dept_manager_approved") return toast("Not approvable");

        const bal = getBalance(state, r.employee_id, r.leave_type);
        if (!bal) return toast("Balance missing");
        if (availableDays(bal) < r.number_of_days) return toast("Insufficient balance");

        bal.used += r.number_of_days;
        r.status = "hr_approved";
        r.approved_by_hr_id = u.id;
        r.approved_by_hr_at = new Date().toISOString();
        saveState(state);
        toast("Approved by HR (balance deducted)");
        render(state, "hr");
      });
    });

    view.querySelectorAll("[data-hr-reject]").forEach((b) => {
      b.addEventListener("click", () => {
        const id = Number(b.getAttribute("data-hr-reject"));
        const r = state.requests.find((x) => x.id === id);
        if (!r || r.status !== "dept_manager_approved") return toast("Not rejectable");
        r.status = "hr_rejected";
        saveState(state);
        toast("Rejected by HR");
        render(state, "hr");
      });
    });

    // Calendar render: build 14-day grid starting today (teaching-friendly)
    const cal = view.querySelector("#calendar");
    const start = todayISO();
    const days = 14;
    const cells = [];
    for (let i = 0; i < days; i++) {
      const dayISO = addDaysISO(start, i);
      const items = approved.flatMap((r) => {
        if (dayISO < r.start_date || dayISO > r.end_date) return [];
        const emp = state.users.find((x) => x.id === r.employee_id);
        const dept = emp ? findDept(state, emp.department_id) : null;
        return [{
          label: emp?.name || "Unknown",
          color: dept?.color || "#94a3b8",
        }];
      });

      cells.push({ dayISO, items });
    }

    cal.innerHTML = cells.map((c) => `
      <div class="day">
        <div class="d">${c.dayISO}</div>
        ${c.items.map((it) => `<span class="pill" style="background:${it.color}">${it.label}</span>`).join("")}
      </div>
    `).join("");

    return;
  }

  view.innerHTML = `<div class="card"><h2>Unknown page</h2></div>`;
}

function defaultRouteForRole(user) {
  if (!user) return "login";
  if (user.role === "employee") return "employee";
  if (user.role === "dept_manager") return "manager";
  if (user.role === "hr_admin") return "hr";
  return "login";
}

function go(route) {
  location.hash = `#${route}`;
}

function boot() {
  let state = loadState();

  document.querySelectorAll("[data-nav]").forEach((b) => {
    b.addEventListener("click", () => go(b.getAttribute("data-nav")));
  });

  document.getElementById("reset").addEventListener("click", () => {
    localStorage.removeItem(STORAGE_KEY);
    state = seedState();
    toast("Demo data reset");
    go("login");
  });

  document.getElementById("logout").addEventListener("click", () => {
    state.session.userId = null;
    saveState(state);
    toast("Logged out");
    go("login");
  });

  window.addEventListener("hashchange", () => {
    state = loadState();
    const route = (location.hash || "#login").slice(1);
    render(state, route);
  });

  const initial = (location.hash || "").slice(1) || defaultRouteForRole(getUser(state));
  go(initial);
}

boot();

