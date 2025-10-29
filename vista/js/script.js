// FinanceU - Student Financial Management App
// JavaScript Application Logic

// Application State
let appState = {
  currentPage: "landing",
  currentSection: "dashboard",
  user: null,
  transactions: [],
  goals: [],
  reminders: [],
  currentEditingId: null,
  currentMonth: new Date().getMonth(),
  currentYear: new Date().getFullYear(),
};

// Utility Functions
const formatCurrency = (amount) => {
  return new Intl.NumberFormat("es-CO", {
    style: "currency",
    currency: "COP",
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(amount || 0);
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString("es-CO", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

const escapeHtml = (unsafe) => {
  if (!unsafe) return '';
  return String(unsafe)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
};

const generateId = () => {
  return Date.now().toString(36) + Math.random().toString(36).substr(2);
};

const saveToStorage = () => {
  localStorage.setItem(
    "financeu_data",
    JSON.stringify({
      user: appState.user,
      transactions: appState.transactions,
      goals: appState.goals,
      reminders: appState.reminders,
    })
  );
};

const loadFromStorage = () => {
  const data = localStorage.getItem("financeu_data");
  if (data) {
    const parsed = JSON.parse(data);
    appState.user = parsed.user || null;
    appState.transactions = parsed.transactions || [];
    appState.goals = parsed.goals || [];
    appState.reminders = parsed.reminders || [];
  }
};

// Toast Notification System
const showToast = (title, message, type = "info") => {
  const container = document.getElementById("toast-container");
  if (!container) return;

  const toast = document.createElement("div");
  toast.className = `toast ${type}`;

  const icons = {
    success: "fas fa-check-circle",
    error: "fas fa-exclamation-circle",
    info: "fas fa-info-circle",
  };

  toast.innerHTML = `
    <div class="toast-icon"><i class="${icons[type]}"></i></div>
    <div class="toast-content">
      <div class="toast-title">${title}</div>
      <div class="toast-message">${message}</div>
    </div>
    <button class="toast-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
  `;

  container.appendChild(toast);
  setTimeout(() => {
    if (toast.parentElement) toast.remove();
  }, 5000);
};

// Page Navigation
const showPage = (pageName) => {
  document
    .querySelectorAll(".page")
    .forEach((page) => page.classList.remove("active"));
  const target = document.getElementById(pageName);
  if (target) {
    target.classList.add("active");
    appState.currentPage = pageName;
  }
};

const showLanding = () => showPage("landing");
const showLogin = () => showPage("login");
const showRegister = () => showPage("register");

// Authentication Functions
const handleLogin = (email, password) => {
  if (email && password) {
    const data = localStorage.getItem("financeu_data");
    if (data) {
      const parsed = JSON.parse(data);
      if (parsed.user && parsed.user.email === email) {
        appState.user = parsed.user;
        appState.transactions = parsed.transactions || [];
        appState.goals = parsed.goals || [];
        appState.reminders = parsed.reminders || [];
        showMainApp();
        showToast("¡Bienvenido!", "Has iniciado sesión correctamente", "success");
        return true;
      }
    }
  }
  return false;
};

const handleRegister = (userData) => {
  appState.user = {
    firstName: userData.firstName,
    lastName: userData.lastName,
    email: userData.email,
    university: userData.university,
    studyProgram: userData.studyProgram,
  };
  showMainApp();
  showToast("¡Cuenta creada!", "Tu cuenta ha sido creada exitosamente", "success");
  return true;
};

const showMainApp = () => {
  showPage("main-app");
  updateUserInfo();
  updateDashboard();
  saveToStorage();
};

const logout = () => {
  appState.user = null;
  appState.transactions = [];
  appState.goals = [];
  appState.reminders = [];
  localStorage.removeItem("financeu_data");
  showLanding();
  showToast("Sesión cerrada", "Has cerrado sesión correctamente", "info");
};

// Section Navigation (SPA opcional)
const showSection = (sectionName) => {
  document
    .querySelectorAll(".nav-link")
    .forEach((link) => link.classList.remove("active"));
  const activeLink = document.querySelector(`[data-section="${sectionName}"]`);
  if (activeLink) activeLink.classList.add("active");

  document
    .querySelectorAll(".content-section")
    .forEach((section) => section.classList.remove("active"));
  const target = document.getElementById(`${sectionName}-section`);
  if (target) target.classList.add("active");

  appState.currentSection = sectionName;

  switch (sectionName) {
    case "dashboard":
      updateDashboard();
      break;
    case "transactions":
      updateTransactionsList();
      break;
    case "goals":
      updateGoalsList();
      break;
    case "analysis":
      updateAnalysis();
      break;
    case "calendar":
      updateCalendar();
      break;
    case "profile":
      updateProfile();
      break;
  }
};

// Sidebar Toggle
const toggleSidebar = () => {
  const sb = document.querySelector(".sidebar");
  if (sb) sb.classList.toggle("active");
};

// User Info Update
const updateUserInfo = () => {
  if (!appState.user) return;
  const nameEl = document.getElementById("user-name");
  const emailEl = document.getElementById("user-email");
  if (nameEl)
    nameEl.textContent = `${appState.user.firstName} ${appState.user.lastName}`;
  if (emailEl) emailEl.textContent = appState.user.email;
};

// Dashboard Functions
const updateDashboard = () => {
  hydrateDashboardFromServer();
  updateStats();
  updateExpensesChart();
  updateRecentTransactions();
  updateGoalsProgress();
  updateTrendChart();
};

const updateStats = () => {
  const incEl = document.getElementById("monthly-income");
  const expEl = document.getElementById("monthly-expenses");
  const balEl = document.getElementById("total-balance");
  const savEl = document.getElementById("total-savings");
  if (!incEl || !expEl || !balEl || !savEl) return;

  const currentMonth = new Date().getMonth();
  const currentYear = new Date().getFullYear();

  const monthlyTransactions = appState.transactions.filter((t) => {
    const d = new Date(t.date);
    return d.getMonth() === currentMonth && d.getFullYear() === currentYear;
  });

  const monthlyIncome = monthlyTransactions
    .filter((t) => t.type === "income")
    .reduce((s, t) => s + t.amount, 0);
  const monthlyExpenses = monthlyTransactions
    .filter((t) => t.type === "expense")
    .reduce((s, t) => s + t.amount, 0);
  const totalBalance = appState.transactions.reduce(
    (s, t) => s + (t.type === "income" ? t.amount : -t.amount),
    0
  );

  // FIX: totalSavings definido (preferir backend; si no, sumar progreso local de metas)
  let totalSavings = 0;
  if (window.dashboardData?.totals?.ahorro != null) {
    totalSavings = Number(window.dashboardData.totals.ahorro);
  } else {
    totalSavings = appState.goals.reduce((sum, g) => sum + (g.currentAmount || 0), 0);
  }

  incEl.textContent = formatCurrency(monthlyIncome);
  expEl.textContent = formatCurrency(monthlyExpenses);
  balEl.textContent = formatCurrency(totalBalance);
  savEl.textContent = formatCurrency(totalSavings);
};

const updateExpensesChart = () => {
  const canvas = document.getElementById("expenses-chart");
  if (!canvas) return;
  const ctx = canvas.getContext("2d");

  ctx.clearRect(0, 0, canvas.width, canvas.height);

  const expenses = appState.transactions.filter((t) => t.type === "expense");
  const totals = {};
  expenses.forEach((e) => {
    totals[e.category] = (totals[e.category] || 0) + e.amount;
  });

  const categories = Object.keys(totals);
  const amounts = Object.values(totals);

  if (categories.length > 0) {
    new Chart(ctx, {
      type: "doughnut",
      data: {
        labels: categories.map((cat) => getCategoryName(cat)),
        datasets: [
          {
            data: amounts,
            backgroundColor: [
              "#ef4444",
              "#f59e0b",
              "#10b981",
              "#3b82f6",
              "#8b5cf6",
              "#ec4899",
              "#06b6d4",
              "#84cc16",
            ],
          },
        ],
      },
      options: {
        responsive: true,
        plugins: { legend: { position: "bottom" } },
      },
    });
  }
};

const updateRecentTransactions = () => {
  const container = document.getElementById("recent-transactions");
  if (!container) return;

  const recent = [...appState.transactions]
    .sort((a, b) => new Date(b.date) - new Date(a.date))
    .slice(0, 5);
  if (recent.length === 0) {
    container.innerHTML =
      '<p class="text-center">No hay transacciones recientes</p>';
    return;
  }

  container.innerHTML = recent
    .map(
      (t) => `
    <div class="transaction-item" style="display:flex;justify-content:space-between;align-items:center;padding:.75rem 0;border-bottom:1px solid var(--border);">
      <div>
        <div style="font-weight:var(--font-weight-medium);">${t.description}</div>
        <div style="font-size:.875rem;color:var(--muted-foreground);">${formatDate(
          t.date
        )} • ${getCategoryName(t.category)}</div>
      </div>
      <div class="transaction-amount ${t.type}" style="font-weight:var(--font-weight-medium);">
        ${t.type === "income" ? "+" : "-"}${formatCurrency(t.amount)}
      </div>
    </div>
  `
    )
    .join("");
};

const updateGoalsProgress = () => {
  const container = document.getElementById("goals-progress");
  if (!container) return;

  if (!appState.goals || appState.goals.length === 0) {
    container.innerHTML = '<p class="text-center">No hay metas creadas</p>';
    return;
  }

  container.innerHTML = appState.goals
    .slice(0, 3)
    .map((goal) => {
      const pct = Math.min((goal.currentAmount / goal.targetAmount) * 100, 100);
      return `
      <div style="margin-bottom:1rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.5rem;">
          <span style="font-weight:var(--font-weight-medium);">${goal.title}</span>
          <span style="font-size:.875rem;color:var(--muted-foreground);">${pct.toFixed(0)}%</span>
        </div>
        <div class="progress-bar"><div class="progress-fill" style="width:${pct}%;"></div></div>
      </div>
    `;
    })
    .join("");
};

const updateTrendChart = () => {
  const canvas = document.getElementById("trend-chart");
  if (!canvas) return;
  const ctx = canvas.getContext("2d");

  ctx.clearRect(0, 0, canvas.width, canvas.height);

  const monthsData = [];
  const currentDate = new Date();
  for (let i = 5; i >= 0; i--) {
    const date = new Date(
      currentDate.getFullYear(),
      currentDate.getMonth() - i,
      1
    );
    const monthTx = appState.transactions.filter((t) => {
      const d = new Date(t.date);
      return (
        d.getMonth() === date.getMonth() &&
        d.getFullYear() === date.getFullYear()
      );
    });
    const income = monthTx
      .filter((t) => t.type === "income")
      .reduce((s, t) => s + t.amount, 0);
    const expenses = monthTx
      .filter((t) => t.type === "expense")
      .reduce((s, t) => s + t.amount, 0);
    monthsData.push({
      month: date.toLocaleDateString("es-CO", { month: "short" }),
      income,
      expenses,
    });
  }

  new Chart(ctx, {
    type: "line",
    data: {
      labels: monthsData.map((m) => m.month),
      datasets: [
        {
          label: "Ingresos",
          data: monthsData.map((m) => m.income),
          borderColor: "#10b981",
          backgroundColor: "rgba(16,185,129,.1)",
          tension: 0.4,
        },
        {
          label: "Gastos",
          data: monthsData.map((m) => m.expenses),
          borderColor: "#ef4444",
          backgroundColor: "rgba(239,68,68,.1)",
          tension: 0.4,
        },
      ],
    },
    options: {
      responsive: true,
      scales: {
        y: { beginAtZero: true, ticks: { callback: (v) => formatCurrency(v) } },
      },
      plugins: { legend: { position: "bottom" } },
    },
  });
};

// Transaction Functions
const updateTransactionsList = () => {
  const tbody = document.getElementById("transactions-tbody");
  if (!tbody) return;

  const typeSel = document.getElementById("transaction-type-filter");
  const catSel = document.getElementById("transaction-category-filter");
  const dateInp = document.getElementById("transaction-date-filter");

  const typeFilter = typeSel ? typeSel.value : "all";
  const categoryFilter = catSel ? catSel.value : "all";
  const dateFilter = dateInp ? dateInp.value : "";

  let filtered = [...appState.transactions];

  // map 1/2 -> income/expense si vienen como IDs numéricos
  let mappedType = typeFilter;
  if (typeFilter === "1") mappedType = "income";
  if (typeFilter === "2") mappedType = "expense";

  if (mappedType !== "all")
    filtered = filtered.filter((t) => t.type === mappedType);
  if (categoryFilter !== "all")
    filtered = filtered.filter(
      (t) => String(t.category) === String(categoryFilter)
    );
  if (dateFilter) filtered = filtered.filter((t) => t.date === dateFilter);

  filtered.sort((a, b) => new Date(b.date) - new Date(a.date));

  if (filtered.length === 0) {
    tbody.innerHTML =
      '<tr><td colspan="6" class="text-center">No se encontraron transacciones</td></tr>';
    return;
  }

  tbody.innerHTML = filtered
    .map(
      (t) => `
    <tr>
      <td>${formatDate(t.date)}</td>
      <td>${t.description}</td>
      <td>${getCategoryName(t.category)}</td>
      <td><span class="transaction-type ${t.type}">${t.type === "income" ? "Ingreso" : "Gasto"}</span></td>
      <td><span class="transaction-amount ${t.type}">${t.type === "income" ? "+" : "-"}${formatCurrency(t.amount)}</span></td>
      <td>
        <div class="transaction-actions">
          <button class="btn-icon" onclick="editTransaction('${t.id}')"><i class="fas fa-edit"></i></button>
          <button class="btn-icon danger" onclick="deleteTransaction('${t.id}')"><i class="fas fa-trash"></i></button>
        </div>
      </td>
    </tr>
  `
    )
    .join("");
};

const openTransactionModal = (transactionId = null) => {
  const modal = document.getElementById("transaction-modal");
  const form = document.getElementById("transaction-form");
  const title = document.getElementById("transaction-modal-title");

  // Si no hay modal (form inline), al menos setea fecha y enfoca
  if (!form) {
    const date = document.getElementById("transaction-date");
    if (date) date.value = new Date().toISOString().split("T")[0];
    const desc = document.getElementById("transaction-description");
    if (desc) desc.focus();
    return;
  }

  form.reset();
  appState.currentEditingId = transactionId;

  if (transactionId) {
    const t = appState.transactions.find((x) => x.id === transactionId);
    if (t) {
      if (title) title.textContent = "Editar Transacción";
      const typeSel = document.getElementById("transaction-type");
      if (typeSel) typeSel.value = t.type === "income" ? "1" : "2";
      document.getElementById("transaction-description").value = t.description;
      document.getElementById("transaction-amount").value = t.amount;
      document.getElementById("transaction-category").value = t.category;
      document.getElementById("transaction-date").value = t.date;
    }
  } else {
    if (title) title.textContent = "Nueva Transacción";
    const date = document.getElementById("transaction-date");
    if (date) date.value = new Date().toISOString().split("T")[0];
  }

  if (modal) modal.classList.add("active");
};

const closeTransactionModal = () => {
  const modal = document.getElementById("transaction-modal");
  if (modal) modal.classList.remove("active");
  appState.currentEditingId = null;
};

const saveTransaction = (formData) => {
  const transactionData = {
    type: formData.type, // 'income' | 'expense'
    description: formData.description,
    amount: parseInt(formData.amount),
    category: formData.category, // puede ser id numérico en string
    date: formData.date,
  };

  if (appState.currentEditingId) {
    const idx = appState.transactions.findIndex(
      (t) => t.id === appState.currentEditingId
    );
    if (idx !== -1) {
      appState.transactions[idx] = {
        ...appState.transactions[idx],
        ...transactionData,
      };
      showToast("Transacción actualizada", "La transacción ha sido actualizada correctamente", "success");
    }
  } else {
    const newTransaction = { id: generateId(), ...transactionData };
    appState.transactions.push(newTransaction);
    showToast("Transacción creada", "La transacción ha sido registrada correctamente", "success");
  }

  updateTransactionsList();
  updateDashboard();
  saveToStorage();
  closeTransactionModal();
};

const editTransaction = (transactionId) => openTransactionModal(transactionId);

const deleteTransaction = (transactionId) => {
  if (confirm("¿Estás seguro de que quieres eliminar esta transacción?")) {
    appState.transactions = appState.transactions.filter(
      (t) => t.id !== transactionId
    );
    updateTransactionsList();
    updateDashboard();
    saveToStorage();
    showToast("Transacción eliminada", "La transacción ha sido eliminada correctamente", "info");
  }
};

const clearFilters = () => {
  const typeEl = document.getElementById("transaction-type-filter");
  const catEl = document.getElementById("transaction-category-filter");
  const dateEl = document.getElementById("transaction-date-filter");
  if (typeEl) typeEl.value = "all";
  if (catEl) catEl.value = "all";
  if (dateEl) dateEl.value = "";
  updateTransactionsList();
};

function toCOP(n) {
  return new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    maximumFractionDigits: 0
  }).format(n || 0);
}

function hydrateDashboardFromServer() {
  if (!window.dashboardData) return;

  // Tarjetas superiores
  const incEl = document.getElementById('monthly-income');
  const expEl = document.getElementById('monthly-expenses');
  const balEl = document.getElementById('total-balance');
  const savEl = document.getElementById('total-savings');

  if (incEl && expEl && balEl && savEl) {
    const t = window.dashboardData.totals || {};
    incEl.textContent = toCOP(t.ingresos || 0);
    expEl.textContent = toCOP(t.gastos || 0);
    balEl.textContent = toCOP(t.balance || 0);
    savEl.textContent = toCOP(t.ahorro || 0);
  }

  // Gastos por categoría (Chart.js doughnut)
  (function drawExpensesByCategory() {
    const canvas = document.getElementById('expenses-chart');
    if (!canvas || !window.dashboardData.expensesByCategory) return;
    const ctx = canvas.getContext('2d');
    if (canvas._chart) { canvas._chart.destroy(); canvas._chart = null; }

    const cats = window.dashboardData.expensesByCategory;
    if (!cats.length) { ctx.clearRect(0, 0, canvas.width, canvas.height); return; }

    const labels = cats.map(c => getCategoryName(String(c.id_categoria)));
    const data = cats.map(c => Number(c.total || 0));

    canvas._chart = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels, datasets: [{
          data,
          backgroundColor: ['#ef4444', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16']
        }]
      },
      options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });
  })();

  // Transacciones recientes
  (function drawRecent() {
    const container = document.getElementById('recent-transactions');
    if (!container) return;
    const rec = window.dashboardData.recentTransactions || [];
    if (!rec.length) { container.innerHTML = '<p class="text-center">No hay transacciones recientes</p>'; return; }
    container.innerHTML = rec.map(t => {
      const type = (Number(t.idtipo_transaccion) === 1) ? 'income' : 'expense';
      const sign = (type === 'income') ? '+' : '-';
      return `
        <div class="transaction-item" style="display:flex;justify-content:space-between;align-items:center;padding:.75rem 0;border-bottom:1px solid var(--border);">
          <div>
            <div style="font-weight:var(--font-weight-medium);">${t.descripcion}</div>
            <div style="font-size:.875rem;color:var(--muted-foreground);">${formatDate(t.fecha)} • ${getCategoryName(String(t.idCategoriaTransaccion))}</div>
          </div>
          <div class="transaction-amount ${type}" style="font-weight:var(--font-weight-medium);">${sign}${toCOP(t.monto)}</div>
        </div>`;
    }).join('');
  })();

  // Progreso de metas (sin progreso en BD: mostramos 0% y datos básicos)
  (function drawGoals() {
    const container = document.getElementById('goals-progress');
    if (!container) return;
    const goals = window.dashboardData.goals || [];
    if (!goals.length) { container.innerHTML = '<p class="text-center">No hay metas creadas</p>'; return; }
    container.innerHTML = goals.slice(0, 3).map(g => {
      const pct = 0; // no hay progreso en BD
      return `
        <div style="margin-bottom:1rem;">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.5rem;">
            <span style="font-weight:var(--font-weight-medium);">${g.titulo_meta}</span>
            <span style="font-size:.875rem;color:var(--muted-foreground);">${pct}%</span>
          </div>
          <div class="progress-bar"><div class="progress-fill" style="width:${pct}%;"></div></div>
          <div style="display:flex;justify-content:space-between;font-size:.875rem;color:var(--muted-foreground);margin-top:.25rem;">
            <span>Meta: ${toCOP(g.monto_objetivo)}</span>
            <span>Vence: ${formatDate(g.fecha_limite)}</span>
          </div>
        </div>`;
    }).join('');
  })();
}

// --- GLOBAL: hidrata metas desde el backend ---
function hydrateGoalsFromServer() {
  if (!window.goalsData || !Array.isArray(window.goalsData)) return;

  appState.goals = window.goalsData.map(g => ({
    id: String(g.id_meta),
    title: g.titulo_meta,
    targetAmount: Number(g.monto_objetivo || 0),
    currentAmount: Number(g.monto_actual || 0), // si no hay en BD, quedará 0
    deadline: g.fecha_limite,
    description: g.descripcion || ''
  }));

  updateGoalsList();
}

// Goal Functions
const updateGoalsList = () => {
  const container = document.getElementById("goals-grid");
  if (!container) return;

  if (!appState.goals || appState.goals.length === 0) {
    container.innerHTML = `
      <div class="goals-empty">
        <h4>No tienes metas financieras creadas</h4>
        <p>Define tus objetivos y haz seguimiento de tu progreso</p>
        <button class="btn-primary" onclick="openGoalModal()"><i class="fas fa-plus"></i> Crear Meta</button>
      </div>
    `;
    return;
  }

  container.innerHTML = appState.goals
    .map((goal) => {
      const percentage = Math.min(
        (goal.currentAmount / goal.targetAmount) * 100,
        100
      );
      const daysLeft = Math.ceil(
        (new Date(goal.deadline) - new Date()) / (1000 * 60 * 60 * 24)
      );
      return `
      <div class="goal-card">
        <div class="goal-header">
          <h3 class="goal-title">${goal.title}</h3>
          <div class="goal-deadline">${daysLeft > 0 ? `${daysLeft} días restantes` : "Vencida"} • ${formatDate(goal.deadline)}</div>
        </div>
        <div class="goal-amount">
          <span class="goal-current">${formatCurrency(goal.currentAmount || 0)}</span>
          <span class="goal-target">de ${formatCurrency(goal.targetAmount)}</span>
        </div>
        <div class="progress-bar"><div class="progress-fill" style="width:${percentage}%;"></div></div>
        <div class="goal-actions">
          <button class="btn-primary" onclick="openAddProgressModal('${goal.id}')"><i class="fas fa-plus"></i> Progreso</button>
          <button class="btn-secondary" onclick="editGoal('${goal.id}')"><i class="fas fa-edit"></i></button>
          <button class="btn-danger" onclick="deleteGoal('${goal.id}')"><i class="fas fa-trash"></i></button>
        </div>
      </div>
    `;
    })
    .join("");
};

const openGoalModal = (goalId = null) => {
  const modal = document.getElementById("goal-modal");
  const form = document.getElementById("goal-form");
  const title = document.getElementById("goal-modal-title");
  if (!form) return;

  form.reset();
  appState.currentEditingId = goalId;

  if (goalId) {
    const goal = appState.goals.find((g) => g.id === goalId);
    if (goal) {
      if (title) title.textContent = "Editar Meta";
      document.getElementById("goal-title").value = goal.title;
      document.getElementById("goal-amount").value = goal.targetAmount;
      document.getElementById("goal-deadline").value = goal.deadline;
      document.getElementById("goal-description").value = goal.description || "";
    }
  } else {
    if (title) title.textContent = "Nueva Meta";
    const nextYear = new Date();
    nextYear.setFullYear(nextYear.getFullYear() + 1);
    document.getElementById("goal-deadline").value = nextYear.toISOString().split("T")[0];
  }

  if (modal) modal.classList.add("active");
};

const closeGoalModal = () => {
  const modal = document.getElementById("goal-modal");
  if (modal) modal.classList.remove("active");
  appState.currentEditingId = null;
};

const saveGoal = (formData) => {
  const goalData = {
    title: formData.title,
    targetAmount: parseInt(formData.targetAmount),
    deadline: formData.deadline,
    description: formData.description,
  };

  if (appState.currentEditingId) {
    const index = appState.goals.findIndex((g) => g.id === appState.currentEditingId);
    if (index !== -1) {
      appState.goals[index] = { ...appState.goals[index], ...goalData };
      showToast("Meta actualizada", "La meta ha sido actualizada correctamente", "success");
    }
  } else {
    const newGoal = { id: generateId(), currentAmount: 0, ...goalData };
    appState.goals.push(newGoal);
    showToast("Meta creada", "La meta ha sido creada correctamente", "success");
  }

  updateGoalsList();
  updateDashboard();
  saveToStorage();
  closeGoalModal();
};

const editGoal = (goalId) => openGoalModal(goalId);

const deleteGoal = (goalId) => {
  if (!confirm("¿Estás seguro de que quieres eliminar esta meta?")) return;

  // Llamada al backend para eliminar la meta
  fetch(`index.php?action=eliminarMeta&id=${encodeURIComponent(goalId)}`, {
    method: 'GET',
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  })
    .then(resp => resp.json())
    .then(data => {
      if (data && data.success) {
        appState.goals = appState.goals.filter((g) => g.id !== goalId);
        updateGoalsList();
        updateDashboard();
        saveToStorage();
        showToast("Meta eliminada", "La meta ha sido eliminada correctamente", "info");
      } else {
        showToast("Error", (data && data.message) ? data.message : "No se pudo eliminar la meta", "error");
      }
    })
    .catch(err => {
      console.error('Error eliminar meta:', err);
      showToast("Error", "No se pudo conectar con el servidor", "error");
    });
};

const openAddProgressModal = (goalId) => {
  const modal = document.getElementById("add-progress-modal");
  const form = document.getElementById("add-progress-form");
  if (!form) return;
  form.reset();
  appState.currentEditingId = goalId;

  // populate min/max and preview fields based on the selected goal
  const goal = appState.goals.find((g) => g.id === goalId);
  const amountInput = document.getElementById('progress-amount');
  const currentLabel = document.getElementById('add-progress-current');
  const targetLabel = document.getElementById('add-progress-target');
  const previewFill = document.getElementById('add-progress-preview-fill');
  const percentLabel = document.getElementById('add-progress-percent');

  if (goal) {
    const cur = Number(goal.currentAmount || 0);
    const tgt = Number(goal.targetAmount || 0);
    // user requested min = 0 and max = monto de la meta
    amountInput.setAttribute('min', 0);
    amountInput.setAttribute('max', tgt);
    amountInput.setAttribute('placeholder', `Máx ${tgt}`);

    currentLabel.textContent = `Actual: ${formatCurrency(cur)}`;
    targetLabel.textContent = `Meta: ${formatCurrency(tgt)}`;

    const pct = Math.min((cur / Math.max(tgt, 1)) * 100, 100);
    previewFill.style.width = pct + '%';
    percentLabel.textContent = `${pct.toFixed(1)}% completado`;

    // live preview when user types an amount
    const updatePreview = () => {
      const v = Number(newAmountInput.value || 0);
      const newTotal = cur + v;
      const newPct = Math.min((newTotal / Math.max(tgt, 1)) * 100, 100);
      previewFill.style.width = newPct + '%';
      percentLabel.textContent = `${newPct.toFixed(1)}% completado`;
    };

    // remove previous listeners by replacing input node, then attach
    const newAmountInput = amountInput.cloneNode(true);
    amountInput.parentNode.replaceChild(newAmountInput, amountInput);
    newAmountInput.addEventListener('input', updatePreview);
  }

  if (modal) modal.classList.add("active");
};

const closeAddProgressModal = () => {
  const modal = document.getElementById("add-progress-modal");
  if (modal) modal.classList.remove("active");
  appState.currentEditingId = null;
};

const addProgressToGoal = (amount) => {
  const idx = appState.goals.findIndex((g) => g.id === appState.currentEditingId);
  if (idx === -1) return;

  const goal = appState.goals[idx];
  const parsedAmount = Number(amount || 0);
  if (isNaN(parsedAmount) || parsedAmount <= 0) {
    showToast('Error', 'Ingrese un monto válido', 'error');
    return;
  }

  // Try to persist on server; if it fails, fallback to local-only update
  (async () => {
    try {
      const formData = new FormData();
      formData.append('id_meta', goal.id);
      formData.append('monto', parsedAmount);

      const res = await fetch('index.php?action=registrarProgreso', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });

      if (res.ok) {
        const data = await res.json().catch(() => null);
        if (data && data.success) {
          // update local state with canonical value returned by server
          appState.goals[idx].currentAmount = Number(data.new_amount || data.new_amount === 0 ? data.new_amount : appState.goals[idx].currentAmount + parsedAmount);
          // add a local transaction record for UI/history
          appState.transactions.push({
            id: generateId(),
            type: 'expense',
            description: `Progreso en meta: ${goal.title}`,
            amount: parsedAmount,
            category: 'savings',
            date: new Date().toISOString().split('T')[0]
          });

          updateGoalsList();
          updateTransactionsList();
          updateDashboard();
          saveToStorage();
          closeAddProgressModal();
          showToast('Progreso agregado', `Se agregaron ${formatCurrency(parsedAmount)} a la meta`, 'success');
          return;
        }
      }

      // fallback: local update
      appState.goals[idx].currentAmount = (appState.goals[idx].currentAmount || 0) + parsedAmount;
      appState.transactions.push({
        id: generateId(),
        type: 'expense',
        description: `Progreso en meta: ${goal.title}`,
        amount: parsedAmount,
        category: 'savings',
        date: new Date().toISOString().split('T')[0]
      });
      updateGoalsList();
      updateTransactionsList();
      updateDashboard();
      saveToStorage();
      closeAddProgressModal();
      showToast('Progreso agregado (local)', `Se agregaron ${formatCurrency(parsedAmount)} a la meta (no se guardó en servidor)`, 'info');
    } catch (err) {
      console.error('Error registrar progreso:', err);
      // fallback local
      appState.goals[idx].currentAmount = (appState.goals[idx].currentAmount || 0) + parsedAmount;
      appState.transactions.push({
        id: generateId(),
        type: 'expense',
        description: `Progreso en meta: ${goal.title}`,
        amount: parsedAmount,
        category: 'savings',
        date: new Date().toISOString().split('T')[0]
      });
      updateGoalsList();
      updateTransactionsList();
      updateDashboard();
      saveToStorage();
      closeAddProgressModal();
      showToast('Progreso agregado (local)', `Se agregaron ${formatCurrency(parsedAmount)} a la meta (error de red)`, 'info');
    }
  })();
};

function hydrateWeeklyFromServer() {
  if (!window.weeklySummary) return;
  const inc = document.getElementById('weekly-income');
  const exp = document.getElementById('weekly-expenses');
  const bal = document.getElementById('weekly-balance');
  if (!inc || !exp || !bal) return;

  const toCOP = (n) => new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(n || 0);

  inc.textContent = toCOP(parseFloat(window.weeklySummary.ingresos_totales || 0));
  exp.textContent = toCOP(parseFloat(window.weeklySummary.gastos_totales || 0));
  const balance = parseFloat(window.weeklySummary.balance || 0);
  bal.textContent = toCOP(balance);
  bal.className = `stat-value ${balance >= 0 ? 'income' : 'expense'}`;
}

const updateAnalysis = () => {
  hydrateWeeklyFromServer();
  updateDailyExpensesChart();
  updateTopCategories();
  updateComparison();
};

const updateWeeklyStats = () => {
  if (window.weeklySummary) return;

  const inc = document.getElementById('weekly-income');
  const exp = document.getElementById('weekly-expenses');
  const bal = document.getElementById('weekly-balance');
  if (!inc || !exp || !bal) return;

  const now = new Date();
  const weekStart = new Date(now.setDate(now.getDate() - now.getDay()));
  const weekEnd = new Date(weekStart); weekEnd.setDate(weekEnd.getDate() + 6);

  const weekly = appState.transactions.filter(t => {
    const d = new Date(t.date);
    return d >= weekStart && d <= weekEnd;
    });

  const weeklyIncome = weekly.filter(t => t.type === 'income').reduce((s, t) => s + t.amount, 0);
  const weeklyExpenses = weekly.filter(t => t.type === 'expense').reduce((s, t) => s + t.amount, 0);
  const weeklyBalance = weeklyIncome - weeklyExpenses;

  const toCOP = n => new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(n || 0);

  inc.textContent = toCOP(weeklyIncome);
  exp.textContent = toCOP(weeklyExpenses);
  bal.textContent = toCOP(weeklyBalance);
  bal.className = `stat-value ${weeklyBalance >= 0 ? 'income' : 'expense'}`;
};

// Helper: rango de semana entregado por el backend (monday..sunday)
function getServerWeekRange() {
  if (!window.weeklySummary || !window.weeklySummary.semana_inicio || !window.weeklySummary.semana_fin) return null;
  return {
    start: new Date(window.weeklySummary.semana_inicio + 'T00:00:00'),
    end: new Date(window.weeklySummary.semana_fin + 'T00:00:00')
  };
}

const updateDailyExpensesChart = () => {
  const canvas = document.getElementById('daily-expenses-chart');
  if (!canvas) return;
  const ctx = canvas.getContext('2d');

  if (canvas._chart) {
    canvas._chart.destroy();
    canvas._chart = null;
  }

  const range = getServerWeekRange();
  if (!range) {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    return;
  }

  let labels = [];
  let data = [];

  if (Array.isArray(window.dailyExpenses) && window.dailyExpenses.length) {
    const map = {};
    window.dailyExpenses.forEach(d => { map[d.fecha] = Number(d.gastos || 0); });

    const cursor = new Date(range.start);
    while (cursor <= range.end) {
      const iso = cursor.toISOString().split('T')[0];
      labels.push(cursor.toLocaleDateString('es-CO', { weekday: 'short' }));
      data.push(map[iso] ?? 0);
      cursor.setDate(cursor.getDate() + 1);
    }
  } else {
    const cursor = new Date(range.start);
    while (cursor <= range.end) {
      const iso = cursor.toISOString().split('T')[0];
      const expenses = appState.transactions
        .filter(t => t.type === 'expense' && t.date === iso)
        .reduce((s, t) => s + Number(t.amount || 0), 0);

      labels.push(cursor.toLocaleDateString('es-CO', { weekday: 'short' }));
      data.push(expenses);
      cursor.setDate(cursor.getDate() + 1);
    }
  }

  canvas._chart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Gastos Diarios',
        data,
        backgroundColor: '#ef4444',
        borderColor: '#dc2626',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          ticks: { callback: v => formatCurrency(v) }
        }
      },
      plugins: { legend: { display: false } }
    }
  });
};

const updateTopCategories = () => {
  const container = document.getElementById('top-categories');
  if (!container) return;

  if (Array.isArray(window.topCategories) && window.topCategories.length) {
    container.innerHTML = window.topCategories.map(row => `
      <div class="category-item">
        <span class="category-name">${getCategoryName(String(row.categoria))}</span>
        <span class="category-amount">${formatCurrency(Number(row.total || 0))}</span>
      </div>
    `).join('');
    return;
  }

  const m = new Date().getMonth();
  const y = new Date().getFullYear();

  const monthlyExpenses = appState.transactions.filter(t => {
    if (t.type !== 'expense') return false;
    const d = new Date(t.date);
    return d.getMonth() === m && d.getFullYear() === y;
  });

  const totals = {};
  monthlyExpenses.forEach(e => {
    const key = String(e.category);
    totals[key] = (totals[key] || 0) + Number(e.amount || 0);
  });

  const entries = Object.entries(totals)
    .sort(([, a], [, b]) => b - a)
    .slice(0, 5);

  if (entries.length === 0) {
    container.innerHTML = '<p class="text-center">No hay gastos este mes</p>';
    return;
  }

  container.innerHTML = entries.map(([cat, amount]) => `
    <div class="category-item">
      <span class="category-name">${getCategoryName(cat)}</span>
      <span class="category-amount">${formatCurrency(amount)}</span>
    </div>
  `).join('');
};

const updateComparison = () => {
  const container = document.getElementById('comparison-chart');
  if (!container) return;

  let prev = 0, cur = 0;

  if (window.weekCompare) {
    prev = Number(window.weekCompare.previa || 0);
    cur = Number(window.weekCompare.actual || 0);
  } else if (Array.isArray(window.weeklySeries) && window.weeklySeries.length) {
    cur = Number(window.weeklySeries[0]?.gastos_totales || 0);
    prev = Number(window.weeklySeries[1]?.gastos_totales || 0);
  }

  const diff = cur - prev;
  const pct = prev > 0 ? (diff / prev) * 100 : 0;

  container.innerHTML = `
    <div style="text-align:center;">
      <div style="font-size:2rem;font-weight:var(--font-weight-medium);margin-bottom:1rem;">
        <span style="color:${diff >= 0 ? 'var(--destructive)' : 'var(--chart-4)'};">
          ${diff >= 0 ? '+' : ''}${formatCurrency(diff)}
        </span>
      </div>
      <div style="color:var(--muted-foreground);">
        ${Math.abs(pct).toFixed(1)}% ${diff >= 0 ? 'más' : 'menos'} que la semana anterior
      </div>
      <div style="margin-top:1rem;display:grid;grid-template-columns:1fr 1fr;gap:1rem;text-align:center;">
        <div>
          <div style="font-size:.875rem;color:var(--muted-foreground);">Semana Anterior</div>
          <div style="font-size:1.25rem;font-weight:var(--font-weight-medium);">${formatCurrency(prev)}</div>
        </div>
        <div>
          <div style="font-size:.875rem;color:var(--muted-foreground);">Semana Actual</div>
          <div style="font-size:1.25rem;font-weight:var(--font-weight-medium);">${formatCurrency(cur)}</div>
        </div>
      </div>
    </div>
  `;
};

// Calendar Functions
const updateCalendar = () => {
  updateCalendarDisplay();
  updateRemindersList();
};

const updateCalendarDisplay = () => {
  const calendar = document.getElementById("calendar");
  const monthSpan = document.getElementById("current-month");
  if (!calendar || !monthSpan) return;

  const firstDay = new Date(appState.currentYear, appState.currentMonth, 1);
  const startDate = new Date(firstDay);
  startDate.setDate(startDate.getDate() - firstDay.getDay());

  monthSpan.textContent = new Date(
    appState.currentYear,
    appState.currentMonth,
    1
  ).toLocaleDateString("es-CO", { month: "long", year: "numeric" });

  const daysOfWeek = ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"];
  let html = daysOfWeek
    .map(
      (day) =>
        `<div class="calendar-day" style="font-weight:var(--font-weight-medium);background-color:var(--muted);">${day}</div>`
    )
    .join("");

  const today = new Date();
  const currentDate = new Date(startDate);

  for (let i = 0; i < 42; i++) {
    const isCurrentMonth = currentDate.getMonth() === appState.currentMonth;
    const isToday = currentDate.toDateString() === today.toDateString();
    const dateString = currentDate.toISOString().split("T")[0];
    const hasReminder = appState.reminders.some((r) => r.date === dateString);

    let classes = "calendar-day";
    if (!isCurrentMonth) classes += " other-month";
    if (isToday) classes += " today";
    if (hasReminder) classes += " has-reminder";

    html += `<div class="${classes}">${currentDate.getDate()}</div>`;
    currentDate.setDate(currentDate.getDate() + 1);
  }

  calendar.innerHTML = html;
};

const updateRemindersList = () => {
  const container = document.getElementById("reminders-list");
  if (!container) return;

  if (container.dataset && container.dataset.serverRendered === '1') {
    return;
  }

  const today = new Date();
  const in30 = new Date();
  in30.setDate(today.getDate() + 30);

  const upcoming = appState.reminders
    .filter((r) => {
      const d = new Date(r.date);
      return d >= today && d <= in30;
    })
    .sort((a, b) => new Date(a.date) - new Date(b.date));

  if (upcoming.length === 0) {
    container.innerHTML = '<p class="text-center">No hay recordatorios próximos</p>';
    return;
  }

  container.innerHTML = upcoming
    .map((r) => {
      const daysUntil = Math.ceil((new Date(r.date) - new Date()) / (1000 * 60 * 60 * 24));
      return `
      <div class="reminder-item">
        <div class="reminder-title">${r.title}</div>
        <div class="reminder-date">
          ${formatDate(r.date)} ${
            daysUntil === 0 ? "(Hoy)" :
            daysUntil === 1 ? "(Mañana)" : `(${daysUntil} días)`
          }
        </div>
        ${r.amount ? `<div class="reminder-amount">${formatCurrency(r.amount)}</div>` : ""}
        <div style="margin-top:.5rem;">
          <button class="btn-secondary" onclick="editReminder('${r.id}')" style="padding:.25rem .5rem;font-size:.875rem;"><i class="fas fa-edit"></i></button>
          <button class="btn-danger" onclick="deleteReminder('${r.id}')" style="padding:.25rem .5rem;font-size:.875rem;"><i class="fas fa-trash"></i></button>
        </div>
      </div>
    `;
    })
    .join("");
};

const previousMonth = () => {
  appState.currentMonth--;
  if (appState.currentMonth < 0) {
    appState.currentMonth = 11;
    appState.currentYear--;
  }
  updateCalendarDisplay();
};

const nextMonth = () => {
  appState.currentMonth++;
  if (appState.currentMonth > 11) {
    appState.currentMonth = 0;
    appState.currentYear++;
  }
  updateCalendarDisplay();
};

const openReminderModal = (reminderId = null) => {
  const modal = document.getElementById("reminder-modal");
  const form = document.getElementById("reminder-form");
  const title = document.getElementById("reminder-modal-title");
  if (!form) return;

  form.reset();
  appState.currentEditingId = reminderId;

  if (reminderId) {
    const r = appState.reminders.find((x) => x.id === reminderId);
    if (r) {
      if (title) title.textContent = "Editar Recordatorio";
      document.getElementById("reminder-title").value = r.title;
      document.getElementById("reminder-amount").value = r.amount || "";
      document.getElementById("reminder-date").value = r.date;
      document.getElementById("reminder-type").value = r.type;
      document.getElementById("reminder-recurring").value = r.recurring;
      document.getElementById("reminder-description").value = r.description || "";
    }
  } else {
    if (title) title.textContent = "Nuevo Recordatorio";
    const d = document.getElementById("reminder-date");
    if (d) d.value = new Date().toISOString().split("T")[0];
  }

  if (modal) modal.classList.add("active");
};

const closeReminderModal = () => {
  const modal = document.getElementById("reminder-modal");
  if (modal) modal.classList.remove("active");
  appState.currentEditingId = null;
};

const saveReminder = (formData) => {
  const reminderData = {
    title: formData.title,
    amount: formData.amount ? parseInt(formData.amount) : null,
    date: formData.date,
    type: formData.type,
    recurring: formData.recurring,
    description: formData.description,
  };

  if (appState.currentEditingId) {
    const index = appState.reminders.findIndex((r) => r.id === appState.currentEditingId);
    if (index !== -1) {
      appState.reminders[index] = { ...appState.reminders[index], ...reminderData };
      showToast("Recordatorio actualizado", "El recordatorio ha sido actualizado correctamente", "success");
    }
  } else {
    const newReminder = { id: generateId(), ...reminderData };
    appState.reminders.push(newReminder);
    showToast("Recordatorio creado", "El recordatorio ha sido creado correctamente", "success");
  }

  updateCalendar();
  saveToStorage();
  closeReminderModal();
};

const editReminder = (reminderId) => openReminderModal(reminderId);

const deleteReminder = (reminderId) => {
  if (confirm("¿Estás seguro de que quieres eliminar este recordatorio?")) {
    appState.reminders = appState.reminders.filter((r) => r.id !== reminderId);
    updateCalendar();
    saveToStorage();
    showToast("Recordatorio eliminado", "El recordatorio ha sido eliminado correctamente", "info");
  }
};

// --- Server-side edit/delete helpers ---
const editServerReminder = (id) => {
  const el = document.querySelector(`.reminder-item[data-id="${id}"]`);
  if (!el) return;
  const title = el.dataset.title || '';
  const amount = el.dataset.amount || '';
  const date = el.dataset.date || '';
  const type = el.dataset.type || '';
  const recurring = el.dataset.recurring || '';
  const description = el.dataset.description || '';

  openReminderModal();
  document.getElementById('reminder-title').value = title;
  document.getElementById('reminder-amount').value = amount;
  document.getElementById('reminder-date').value = date;
  document.getElementById('reminder-type').value = type;
  document.getElementById('reminder-recurring').value = recurring;
  document.getElementById('reminder-description').value = description;

  appState.currentEditingServerId = id;
  appState.currentEditingId = null;
  const titleEl = document.getElementById('reminder-modal-title');
  if (titleEl) titleEl.textContent = 'Editar Recordatorio';
};

const deleteServerReminder = async (id) => {
  if (!confirm('¿Eliminar este recordatorio?')) return;
  try {
    const target = '/FinanceU-/vista/RecordatorioVista.php?action=eliminar';
    const res = await fetch(target, {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ id: id })
    });
    const data = await res.json().catch(() => null);
    if (res.ok && data && data.success) {
      showToast('Recordatorio eliminado', 'El recordatorio se eliminó correctamente', 'success');
      const el = document.querySelector(`.reminder-item[data-id="${id}"]`);
      if (el) el.remove();
      await refreshRemindersFromServer();
    } else {
      showToast('Error', (data && data.message) ? data.message : 'No se pudo eliminar', 'error');
    }
  } catch (err) {
    console.error(err);
    showToast('Error', 'No se pudo conectar con el servidor', 'error');
  }
};

const refreshRemindersFromServer = async () => {
  try {
    const endpoint = '/FinanceU-/vista/RecordatorioVista.php?action=getProximos';
    const res = await fetch(endpoint, { credentials: 'same-origin' });
    if (!res.ok) return;
    const arr = await res.json().catch(() => null);
    if (!Array.isArray(arr)) return;

    appState.reminders = arr.map((r) => ({
      id: r.id_recordatorio?.toString() || (r.id_recordatorio ?? ''),
      title: r.titulo || r.title || '',
      amount: r.monto !== undefined ? Number(r.monto) : null,
      date: r.fecha || '',
      type: r.idtipo_recordatorio || r.type || '',
      recurring: r.idrecurrente || r.recurring || '',
      description: r.descripcion || r.description || ''
    }));

    updateCalendarDisplay();

    const container = document.getElementById('reminders-list');
    if (container) {
      const itemsHtml = arr
        .map((r) => {
          const title = r.titulo || '';
          const montoVal = r.monto !== undefined ? Number(r.monto) : 0;
          const monto = montoVal > 0 ? `\$ ${montoVal.toLocaleString('es-CO')}` : '';
          const fechaRaw = r.fecha || '';
          const today = new Date();
          const d = new Date(fechaRaw + 'T00:00:00');
          const diffDays = Math.ceil((d - new Date(today.getFullYear(), today.getMonth(), today.getDate())) / (1000 * 60 * 60 * 24));
          let diasTxt = '';
          if (diffDays === 0) diasTxt = '(Hoy)';
          else if (diffDays === 1) diasTxt = '(Mañana)';
          else if (diffDays < 0) diasTxt = `(${Math.abs(diffDays)} días ago)`;
          else diasTxt = `(${diffDays} días)`;

          const months = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
          const fr = d.getDate() + ' de ' + months[d.getMonth()] + ' de ' + d.getFullYear();

          return `
              <div class="reminder-item" data-id="${r.id_recordatorio}">
                <div class="reminder-title">${escapeHtml(title)}</div>
                <div class="reminder-date">${fr} ${diasTxt}</div>
                ${monto ? `<div class="reminder-amount">${monto}</div>` : ''}
                <div style="margin-top:.5rem;">
                  <button class="btn-secondary" onclick="editServerReminder(${r.id_recordatorio})" style="padding:.25rem .5rem;font-size:.875rem;"><i class="fas fa-edit"></i></button>
                  <button class="btn-danger" onclick="deleteServerReminder(${r.id_recordatorio})" style="padding:.25rem .5rem;font-size:.875rem;"><i class="fas fa-trash"></i></button>
                </div>
              </div>
            `;
        })
        .join('');
      container.innerHTML = itemsHtml || '<p class="text-center">No hay recordatorios próximos</p>';
      container.dataset.serverRendered = '1';
    }
  } catch (e) {
    console.error('Error refrescando recordatorios:', e);
  }
};

// Profile Functions
const updateProfile = () => {
  const f = (id) => document.getElementById(id);

  if (appState.user) {
    const fn = f('profile-firstname'); if (fn) fn.value = appState.user.firstName || '';
    const ln = f('profile-lastname'); if (ln) ln.value = appState.user.lastName || '';
    const em = f('profile-email'); if (em) em.value = appState.user.email || '';
    const un = f('profile-university'); if (un) un.value = appState.user.university || '';
    const sp = f('profile-program'); if (sp) sp.value = appState.user.studyProgram || '';
  }

  const tt = f('total-transactions');
  const tg = f('total-goals');
  const cd = f('consecutive-days');

  if (window.usageStats) {
    if (tt) tt.textContent = window.usageStats.transacciones_registradas ?? 0;
    if (tg) tg.textContent = window.usageStats.metas_establecidas ?? 0;
    if (cd) cd.textContent = window.usageStats.dias_consecutivos ?? 0;
  } else {
    if (tt) tt.textContent = appState.transactions.length;
    if (tg) tg.textContent = appState.goals.length;
    if (cd) cd.textContent = calculateConsecutiveDays();
  }
};

const calculateConsecutiveDays = () => {
  if (appState.transactions.length === 0) return 0;
  const dates = [...new Set(appState.transactions.map((t) => t.date))].sort();
  const today = new Date().toISOString().split("T")[0];
  let consecutiveDays = 0;
  const currentDate = new Date(today);
  while (dates.includes(currentDate.toISOString().split("T")[0])) {
    consecutiveDays++;
    currentDate.setDate(currentDate.getDate() - 1);
  }
  return consecutiveDays;
};

const saveProfile = (formData) => {
  appState.user = {
    ...appState.user,
    firstName: formData.firstName,
    lastName: formData.lastName,
    email: formData.email,
    university: formData.university,
    studyProgram: formData.studyProgram,
  };
  updateUserInfo();
  saveToStorage();
  showToast("Perfil actualizado", "Tu perfil ha sido actualizado correctamente", "success");
};

// Utility helpers
const getCategoryName = (category) => {
  const categories = {
    1: "Alimentación",
    2: "Transporte",
    3: "Educación",
    4: "Entretenimiento",
    5: "Salud",
    6: "Compras",
    7: "Otros",
    food: "Alimentación",
    transport: "Transporte",
    education: "Educación",
    entertainment: "Entretenimiento",
    health: "Salud",
    shopping: "Compras",
    salary: "Salario",
    scholarship: "Beca",
    family: "Familia",
    freelance: "Freelance",
    savings: "Ahorros",
    other: "Otros",
  };
  return categories[category] || category;
};

const getIconClass = (icon) => {
  const icons = {
    "piggy-bank": "piggy-bank",
    "graduation-cap": "graduation-cap",
    plane: "plane",
    heart: "heart",
    car: "car",
    home: "home",
    gift: "gift",
    star: "star",
  };
  return icons[icon] || "piggy-bank";
};

// ----- Stubs para evitar ReferenceError si aún no tienes estos modales -----
function openAddMoneyModal() { /* opcional: implementar modal de agregar dinero */ }
function closeAddMoneyModal() { /* opcional */ }

// Event Listeners
document.addEventListener("DOMContentLoaded", () => {
  const main = document.getElementById('main-app');
  if (main) main.classList.add('active');
  hydrateWeeklyFromServer();
  loadFromStorage();

  // Hero chart (opcional)
  setTimeout(() => {
    const heroCanvas = document.getElementById("hero-chart");
    if (heroCanvas) {
      const ctx = heroCanvas.getContext("2d");
      new Chart(ctx, {
        type: "line",
        data: {
          labels: ["Ene", "Feb", "Mar", "Abr", "May", "Jun"],
          datasets: [
            {
              data: [800000, 950000, 1100000, 1050000, 1200000, 1250000],
              borderColor: "rgba(255,255,255,0.8)",
              backgroundColor: "rgba(255,255,255,0.1)",
              tension: 0.4,
              borderWidth: 2,
              pointBackgroundColor: "white",
              pointBorderColor: "rgba(255,255,255,0.8)",
              pointBorderWidth: 2,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: { x: { display: false }, y: { display: false } },
          interaction: { intersect: false },
        },
      });
    }
  }, 100);

  if (document.getElementById('profile-section') && typeof updateProfile === 'function') {
    updateProfile();
  }

  const prevBtn = document.getElementById("prev-month");
  if (prevBtn) prevBtn.addEventListener("click", previousMonth);
  const nextBtn = document.getElementById("next-month");
  if (nextBtn) nextBtn.addEventListener("click", nextMonth);

  const loginForm = document.getElementById("login-form");
  if (loginForm) {
    loginForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const fd = new FormData(e.target);
      const ok = handleLogin(fd.get("email"), fd.get("password"));
      const err = document.getElementById("login-error");
      if (err) {
        if (ok) err.classList.remove("show");
        else {
          err.textContent = "Credenciales incorrectas. Intenta nuevamente o regístrate.";
          err.classList.add("show");
        }
      }
    });
  }

  const registerForm = document.getElementById("register-form");
  if (registerForm) {
    registerForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const fd = new FormData(e.target);
      handleRegister({
        firstName: fd.get("firstName"),
        lastName: fd.get("lastName"),
        email: fd.get("email"),
        password: fd.get("password"),
        university: fd.get("university"),
        studyProgram: fd.get("studyProgram"),
      });
    });
  }

  const txForm = document.getElementById("transaction-form");
  if (txForm) {
    const hasAction = txForm.getAttribute("action");
    if (!hasAction) {
      txForm.addEventListener("submit", (e) => {
        e.preventDefault();
        const fd = new FormData(txForm);
        const type = fd.get("id_tipo") === "1" ? "income" : "expense";
        saveTransaction({
          type,
          description: fd.get("descripcion"),
          amount: fd.get("monto"),
          category: fd.get("id_categoria"),
          date: fd.get("fecha"),
        });
      });
    }
  }

  // --- GOAL FORM ---
  const goalForm = document.getElementById('goal-form');
  if (goalForm) {
    const hasAction = goalForm.getAttribute('action'); // "index.php?action=crearMeta"
    if (!hasAction) {
      goalForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const fd = new FormData(goalForm);
        saveGoal({
          title: fd.get('titulo_meta'),
          targetAmount: fd.get('monto_objetivo'),
          deadline: fd.get('fecha_limite'),
          description: fd.get('descripcion')
        });
      });
    }
  }

  [
    "reminder-form",
    "add-money-form",
    "add-progress-form",
    "profile-form",
  ].forEach((id) => {
    const f = document.getElementById(id);
    if (!f) return;
    f.addEventListener("submit", (e) => {
      e.preventDefault();
      const fd = new FormData(f);
      if (id === "reminder-form") {
        (async () => {
          try {
            const isEditServer = !!appState.currentEditingServerId;
            const action = isEditServer ? 'actualizar' : 'crear';
            const endpoint = '/FinanceU-/vista/RecordatorioVista.php?action=' + action;

            if (isEditServer) fd.append('id', appState.currentEditingServerId);

            const res = await fetch(endpoint, { method: 'POST', body: fd, credentials: 'same-origin' });
            const data = await res.json().catch(() => null);
            if (res.ok && data && data.success) {
              showToast(isEditServer ? 'Recordatorio actualizado' : 'Recordatorio creado',
                        isEditServer ? 'El recordatorio se actualizó en la base de datos' : 'El recordatorio se guardó correctamente',
                        'success');
              appState.currentEditingServerId = null;
              closeReminderModal();
              await refreshRemindersFromServer();
            } else {
              const msg = (data && data.message) ? data.message : 'Error al guardar el recordatorio';
              showToast('Error', msg, 'error');
            }
          } catch (err) {
            console.error(err);
            showToast('Error', 'No se pudo conectar con el servidor', 'error');
          }
        })();
      }
      if (id === "add-progress-form") addProgressToGoal(fd.get("amount"));
      if (id === "profile-form")
        saveProfile({
          firstName: fd.get("firstName"),
          lastName: fd.get("lastName"),
          email: fd.get("email"),
          university: fd.get("university"),
          studyProgram: fd.get("studyProgram"),
        });
    });
  });

  const typeFilterEl = document.getElementById("transaction-type-filter");
  if (typeFilterEl) typeFilterEl.addEventListener("change", updateTransactionsList);
  const catFilterEl = document.getElementById("transaction-category-filter");
  if (catFilterEl) catFilterEl.addEventListener("change", updateTransactionsList);
  const dateFilterEl = document.getElementById("transaction-date-filter");
  if (dateFilterEl) dateFilterEl.addEventListener("change", updateTransactionsList);

  document.querySelectorAll(".modal").forEach((modal) => {
    modal.addEventListener("click", (e) => {
      if (e.target === modal) modal.classList.remove("active");
    });
  });

  const goalsGrid = document.getElementById("goals-grid");
  if (goalsGrid) {
    if (typeof hydrateGoalsFromServer === "function") hydrateGoalsFromServer();
    if (typeof updateGoalsList === "function") updateGoalsList();
  }

  const calendarEl = document.getElementById("calendar");
  if (calendarEl && typeof updateCalendar === "function") {
    updateCalendar();
    const prev = document.getElementById("prev-month");
    if (prev) prev.addEventListener("click", previousMonth);
    const next = document.getElementById("next-month");
    if (next) next.addEventListener("click", nextMonth);
  }

  document.addEventListener("click", (e) => {
    const link = e.target.closest("a.nav-link");
    if (!link) return;
    if (link.getAttribute("data-spa") === "true") {
      e.preventDefault();
      const section = link.getAttribute("data-section");
      if (section) showSection(section);
    }
  });
});

// Expose functions to global scope for HTML onclick handlers
window.showLanding = showLanding;
window.showLogin = showLogin;
window.showRegister = showRegister;
window.logout = logout;
window.toggleSidebar = toggleSidebar;
window.openTransactionModal = openTransactionModal;
window.closeTransactionModal = closeTransactionModal;
window.editTransaction = editTransaction;
window.deleteTransaction = deleteTransaction;
window.clearFilters = clearFilters;
window.openAddMoneyModal = openAddMoneyModal;
window.closeAddMoneyModal = closeAddMoneyModal;
window.openGoalModal = openGoalModal;
window.closeGoalModal = closeGoalModal;
window.editGoal = editGoal;
window.deleteGoal = deleteGoal;
window.openAddProgressModal = openAddProgressModal;
window.closeAddProgressModal = closeAddProgressModal;
window.openReminderModal = openReminderModal;
window.closeReminderModal = closeReminderModal;
window.editReminder = editReminder;
window.deleteReminder = deleteReminder;
