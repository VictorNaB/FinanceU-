// FinanceU - Student Financial Management App
// JavaScript Application Logic

// Application State
let appState = {
    currentPage: 'landing',
    currentSection: 'dashboard',
    user: null,
    transactions: [],
    pockets: [],
    goals: [],
    reminders: [],
    currentEditingId: null,
    currentMonth: new Date().getMonth(),
    currentYear: new Date().getFullYear()
};

// Utility Functions
const formatCurrency = (amount) => {
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-CO', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const generateId = () => {
    return Date.now().toString(36) + Math.random().toString(36).substr(2);
};

const saveToStorage = () => {
    localStorage.setItem('financeu_data', JSON.stringify({
        user: appState.user,
        transactions: appState.transactions,
        pockets: appState.pockets,
        goals: appState.goals,
        reminders: appState.reminders
    }));
};

const loadFromStorage = () => {
    const data = localStorage.getItem('financeu_data');
    if (data) {
        const parsed = JSON.parse(data);
        appState.user = parsed.user;
        appState.transactions = parsed.transactions || [];
        appState.pockets = parsed.pockets || [];
        appState.goals = parsed.goals || [];
        appState.reminders = parsed.reminders || [];
    }
};

// Toast Notification System
const showToast = (title, message, type = 'info') => {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;

    const icons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-circle',
        info: 'fas fa-info-circle'
    };

    toast.innerHTML = `
            <div class="toast-icon">
                <i class="${icons[type]}"></i>
            </div>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;

    container.appendChild(toast);

    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 5000);
};

// Page Navigation
const showPage = (pageName) => {
    document.querySelectorAll('.page').forEach(page => {
        page.classList.remove('active');
    });
    const target = document.getElementById(pageName);
    if (target) {
        target.classList.add('active');
        appState.currentPage = pageName;
    }
};

const showLanding = () => showPage('landing');
const showLogin = () => showPage('login');
const showRegister = () => showPage('register');

// Authentication Functions

const handleLogin = (email, password) => {
    // For demo purposes, accept any non-empty credentials
    if (email && password) {
        // Check if user exists in localStorage
        const data = localStorage.getItem('financeu_data');
        if (data) {
            const parsed = JSON.parse(data);
            if (parsed.user && parsed.user.email === email) {
                appState.user = parsed.user;
                appState.transactions = parsed.transactions || [];
                appState.pockets = parsed.pockets || [];
                appState.goals = parsed.goals || [];
                appState.reminders = parsed.reminders || [];

                showMainApp();
                showToast('¡Bienvenido!', 'Has iniciado sesión correctamente', 'success');
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
        studyProgram: userData.studyProgram
    };

    showMainApp();
    showToast('¡Cuenta creada!', 'Tu cuenta ha sido creada exitosamente', 'success');
    return true;
};

const showMainApp = () => {
    showPage('main-app');
    updateUserInfo();
    updateDashboard();
    saveToStorage();
};

const logout = () => {
    appState.user = null;
    appState.transactions = [];
    appState.pockets = [];
    appState.goals = [];
    appState.reminders = [];
    localStorage.removeItem('financeu_data');
    showLanding();
    showToast('Sesión cerrada', 'Has cerrado sesión correctamente', 'info');
};

// Section Navigation
const showSection = (sectionName) => {
    // Update navigation
    document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
    });
    document.querySelector(`[data-section="${sectionName}"]`).classList.add('active');

    // Update content
    document.querySelectorAll('.content-section').forEach(section => {
        section.classList.remove('active');
    });
    document.getElementById(`${sectionName}-section`).classList.add('active');

    appState.currentSection = sectionName;

    // Load section-specific data
    switch (sectionName) {
        case 'dashboard':
            updateDashboard();
            break;
        case 'transactions':
            updateTransactionsList();
            break;
        case 'pockets':
            updatePocketsList();
            break;
        case 'goals':
            updateGoalsList();
            break;
        case 'analysis':
            updateAnalysis();
            break;
        case 'calendar':
            updateCalendar();
            break;
        case 'profile':
            updateProfile();
            break;
    }
};

// Sidebar Toggle
const toggleSidebar = () => {
    document.querySelector('.sidebar').classList.toggle('active');
};

// User Info Update
const updateUserInfo = () => {
    if (appState.user) {
        const nameEl = document.getElementById('user-name');
        const emailEl = document.getElementById('user-email');

        if (nameEl) {
            nameEl.textContent = `${appState.user.firstName} ${appState.user.lastName}`;
        }

        if (emailEl) {
            emailEl.textContent = appState.user.email;
        }
    }
};

// Dashboard Functions
const updateDashboard = () => {
    updateStats();
    updateExpensesChart();
    updateRecentTransactions();
    updateGoalsProgress();
    updateTrendChart();
};

const updateStats = () => {
    const currentMonth = new Date().getMonth();
    const currentYear = new Date().getFullYear();

    const monthlyTransactions = appState.transactions.filter(t => {
        const transactionDate = new Date(t.date);
        return transactionDate.getMonth() === currentMonth &&
            transactionDate.getFullYear() === currentYear;
    });

    const monthlyIncome = monthlyTransactions
        .filter(t => t.type === 'income')
        .reduce((sum, t) => sum + t.amount, 0);

    const monthlyExpenses = monthlyTransactions
        .filter(t => t.type === 'expense')
        .reduce((sum, t) => sum + t.amount, 0);

    const totalBalance = appState.transactions
        .reduce((sum, t) => sum + (t.type === 'income' ? t.amount : -t.amount), 0);

    const totalSavings = appState.pockets
        .reduce((sum, p) => sum + p.current, 0);

    document.getElementById('monthly-income').textContent = formatCurrency(monthlyIncome);
    document.getElementById('monthly-expenses').textContent = formatCurrency(monthlyExpenses);
    document.getElementById('total-balance').textContent = formatCurrency(totalBalance);
    document.getElementById('total-savings').textContent = formatCurrency(totalSavings);
};

const updateExpensesChart = () => {
    const canvas = document.getElementById('expenses-chart');
    const ctx = canvas.getContext('2d');

    // Clear existing chart
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // Get expense data by category
    const expenses = appState.transactions.filter(t => t.type === 'expense');
    const categoryTotals = {};

    expenses.forEach(expense => {
        categoryTotals[expense.category] = (categoryTotals[expense.category] || 0) + expense.amount;
    });

    const categories = Object.keys(categoryTotals);
    const amounts = Object.values(categoryTotals);

    if (categories.length > 0) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: categories.map(cat => getCategoryName(cat)),
                datasets: [{
                    data: amounts,
                    backgroundColor: [
                        '#ef4444', '#f59e0b', '#10b981', '#3b82f6',
                        '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
};

const updateRecentTransactions = () => {
    const container = document.getElementById('recent-transactions');
    const recentTransactions = appState.transactions
        .sort((a, b) => new Date(b.date) - new Date(a.date))
        .slice(0, 5);

    if (recentTransactions.length === 0) {
        container.innerHTML = '<p class="text-center">No hay transacciones recientes</p>';
        return;
    }

    container.innerHTML = recentTransactions.map(transaction => `
            <div class="transaction-item" style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid var(--border);">
                <div>
                    <div style="font-weight: var(--font-weight-medium);">${transaction.description}</div>
                    <div style="font-size: 0.875rem; color: var(--muted-foreground);">
                        ${formatDate(transaction.date)} • ${getCategoryName(transaction.category)}
                    </div>
                </div>
                <div class="transaction-amount ${transaction.type}" style="font-weight: var(--font-weight-medium);">
                    ${transaction.type === 'income' ? '+' : '-'}${formatCurrency(transaction.amount)}
                </div>
            </div>
        `).join('');
};

const updateGoalsProgress = () => {
    const container = document.getElementById('goals-progress');

    if (appState.goals.length === 0) {
        container.innerHTML = '<p class="text-center">No hay metas creadas</p>';
        return;
    }

    container.innerHTML = appState.goals.slice(0, 3).map(goal => {
        const percentage = Math.min((goal.currentAmount / goal.targetAmount) * 100, 100);
        return `
                <div style="margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <span style="font-weight: var(--font-weight-medium);">${goal.title}</span>
                        <span style="font-size: 0.875rem; color: var(--muted-foreground);">${percentage.toFixed(0)}%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: ${percentage}%;"></div>
                    </div>
                </div>
            `;
    }).join('');
};

const updateTrendChart = () => {
    const canvas = document.getElementById('trend-chart');
    const ctx = canvas.getContext('2d');

    // Clear existing chart
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // Get last 6 months data
    const monthsData = [];
    const currentDate = new Date();

    for (let i = 5; i >= 0; i--) {
        const date = new Date(currentDate.getFullYear(), currentDate.getMonth() - i, 1);
        const monthTransactions = appState.transactions.filter(t => {
            const transactionDate = new Date(t.date);
            return transactionDate.getMonth() === date.getMonth() &&
                transactionDate.getFullYear() === date.getFullYear();
        });

        const income = monthTransactions
            .filter(t => t.type === 'income')
            .reduce((sum, t) => sum + t.amount, 0);

        const expenses = monthTransactions
            .filter(t => t.type === 'expense')
            .reduce((sum, t) => sum + t.amount, 0);

        monthsData.push({
            month: date.toLocaleDateString('es-CO', { month: 'short' }),
            income,
            expenses
        });
    }

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: monthsData.map(m => m.month),
            datasets: [{
                label: 'Ingresos',
                data: monthsData.map(m => m.income),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4
            }, {
                label: 'Gastos',
                data: monthsData.map(m => m.expenses),
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return formatCurrency(value);
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
};

// Transaction Functions
const updateTransactionsList = () => {
    const tbody = document.getElementById('transactions-tbody');
    let filteredTransactions = [...appState.transactions];

    // Apply filters
    const typeFilter = document.getElementById('transaction-type-filter').value;
    const categoryFilter = document.getElementById('transaction-category-filter').value;
    const dateFilter = document.getElementById('transaction-date-filter').value;

    if (typeFilter !== 'all') {
        filteredTransactions = filteredTransactions.filter(t => t.type === typeFilter);
    }

    if (categoryFilter !== 'all') {
        filteredTransactions = filteredTransactions.filter(t => t.category === categoryFilter);
    }

    if (dateFilter) {
        filteredTransactions = filteredTransactions.filter(t => t.date === dateFilter);
    }

    // Sort by date (newest first)
    filteredTransactions.sort((a, b) => new Date(b.date) - new Date(a.date));

    if (filteredTransactions.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No se encontraron transacciones</td></tr>';
        return;
    }

    tbody.innerHTML = filteredTransactions.map(transaction => `
            <tr>
                <td>${formatDate(transaction.date)}</td>
                <td>${transaction.description}</td>
                <td>${getCategoryName(transaction.category)}</td>
                <td>
                    <span class="transaction-type ${transaction.type}">
                        ${transaction.type === 'income' ? 'Ingreso' : 'Gasto'}
                    </span>
                </td>
                <td>
                    <span class="transaction-amount ${transaction.type}">
                        ${transaction.type === 'income' ? '+' : '-'}${formatCurrency(transaction.amount)}
                    </span>
                </td>
                <td>
                    <div class="transaction-actions">
                        <button class="btn-icon" onclick="editTransaction('${transaction.id}')">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-icon danger" onclick="deleteTransaction('${transaction.id}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
};

const openTransactionModal = (transactionId = null) => {
    const modal = document.getElementById('transaction-modal');
    const form = document.getElementById('transaction-form');
    const title = document.getElementById('transaction-modal-title');

    form.reset();
    appState.currentEditingId = transactionId;

    if (transactionId) {
        const transaction = appState.transactions.find(t => t.id === transactionId);
        if (transaction) {
            title.textContent = 'Editar Transacción';
            document.getElementById('transaction-type').value = transaction.type;
            document.getElementById('transaction-description').value = transaction.description;
            document.getElementById('transaction-amount').value = transaction.amount;
            document.getElementById('transaction-category').value = transaction.category;
            document.getElementById('transaction-date').value = transaction.date;
        }
    } else {
        title.textContent = 'Nueva Transacción';
        document.getElementById('transaction-date').value = new Date().toISOString().split('T')[0];
    }

    modal.classList.add('active');
};

const closeTransactionModal = () => {
    document.getElementById('transaction-modal').classList.remove('active');
    appState.currentEditingId = null;
};

const saveTransaction = (formData) => {
    const transactionData = {
        type: formData.type,
        description: formData.description,
        amount: parseInt(formData.amount),
        category: formData.category,
        date: formData.date
    };

    if (appState.currentEditingId) {
        const index = appState.transactions.findIndex(t => t.id === appState.currentEditingId);
        if (index !== -1) {
            appState.transactions[index] = { ...appState.transactions[index], ...transactionData };
            showToast('Transacción actualizada', 'La transacción ha sido actualizada correctamente', 'success');
        }
    } else {
        const newTransaction = {
            id: generateId(),
            ...transactionData
        };
        appState.transactions.push(newTransaction);
        showToast('Transacción creada', 'La transacción ha sido registrada correctamente', 'success');
    }

    updateTransactionsList();
    updateDashboard();
    saveToStorage();
    closeTransactionModal();
};

const editTransaction = (transactionId) => {
    openTransactionModal(transactionId);
};

const deleteTransaction = (transactionId) => {
    if (confirm('¿Estás seguro de que quieres eliminar esta transacción?')) {
        appState.transactions = appState.transactions.filter(t => t.id !== transactionId);
        updateTransactionsList();
        updateDashboard();
        saveToStorage();
        showToast('Transacción eliminada', 'La transacción ha sido eliminada correctamente', 'info');
    }
};

const clearFilters = () => {
    document.getElementById('transaction-type-filter').value = 'all';
    document.getElementById('transaction-category-filter').value = 'all';
    document.getElementById('transaction-date-filter').value = '';
    updateTransactionsList();
};

// Pocket Functions
const updatePocketsList = () => {
    const container = document.getElementById('pockets-grid');

    if (appState.pockets.length === 0) {
        container.innerHTML = `
                <div style="grid-column: 1 / -1; text-align: center; padding: 2rem;">
                    <i class="fas fa-piggy-bank" style="font-size: 3rem; color: var(--muted-foreground); margin-bottom: 1rem;"></i>
                    <p>No tienes bolsillos de ahorro creados</p>
                    <p style="color: var(--muted-foreground); margin-bottom: 1rem;">Crea tu primer bolsillo para empezar a ahorrar</p>
                    <button class="btn-primary" onclick="openPocketModal()">
                        <i class="fas fa-plus"></i>
                        Crear Bolsillo
                    </button>
                </div>
            `;
        return;
    }

    container.innerHTML = appState.pockets.map(pocket => {
        const percentage = Math.min((pocket.current / pocket.target) * 100, 100);
        return `
                <div class="pocket-card">
                    <div class="pocket-header">
                        <div class="pocket-icon color-${pocket.color}">
                            <i class="fas fa-${getIconClass(pocket.icon)}"></i>
                        </div>
                        <div class="pocket-info">
                            <h3>${pocket.name}</h3>
                            <div class="pocket-target">Meta: ${formatCurrency(pocket.target)}</div>
                        </div>
                    </div>
                    
                    <div class="pocket-progress">
                        <div class="pocket-amount">${formatCurrency(pocket.current)}</div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: ${percentage}%;"></div>
                        </div>
                        <div class="progress-percentage">${percentage.toFixed(1)}% completado</div>
                    </div>
                    
                    <div class="pocket-actions">
                        <button class="btn-primary" onclick="openAddMoneyModal('${pocket.id}')">
                            <i class="fas fa-plus"></i>
                            Agregar
                        </button>
                        <button class="btn-secondary" onclick="editPocket('${pocket.id}')">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-danger" onclick="deletePocket('${pocket.id}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
    }).join('');
};

const openPocketModal = (pocketId = null) => {
    const modal = document.getElementById('pocket-modal');
    const form = document.getElementById('pocket-form');
    const title = document.getElementById('pocket-modal-title');

    form.reset();
    appState.currentEditingId = pocketId;

    if (pocketId) {
        const pocket = appState.pockets.find(p => p.id === pocketId);
        if (pocket) {
            title.textContent = 'Editar Bolsillo';
            document.getElementById('pocket-name').value = pocket.name;
            document.getElementById('pocket-target').value = pocket.target;
            document.getElementById('pocket-icon').value = pocket.icon;
            document.getElementById('pocket-color').value = pocket.color;
        }
    } else {
        title.textContent = 'Nuevo Bolsillo';
    }

    modal.classList.add('active');
};

const closePocketModal = () => {
    document.getElementById('pocket-modal').classList.remove('active');
    appState.currentEditingId = null;
};

const savePocket = (formData) => {
    const pocketData = {
        name: formData.name,
        target: parseInt(formData.target),
        icon: formData.icon,
        color: formData.color
    };

    if (appState.currentEditingId) {
        const index = appState.pockets.findIndex(p => p.id === appState.currentEditingId);
        if (index !== -1) {
            appState.pockets[index] = { ...appState.pockets[index], ...pocketData };
            showToast('Bolsillo actualizado', 'El bolsillo ha sido actualizado correctamente', 'success');
        }
    } else {
        const newPocket = {
            id: generateId(),
            current: 0,
            ...pocketData
        };
        appState.pockets.push(newPocket);
        showToast('Bolsillo creado', 'El bolsillo ha sido creado correctamente', 'success');
    }

    updatePocketsList();
    updateDashboard();
    saveToStorage();
    closePocketModal();
};

const editPocket = (pocketId) => {
    openPocketModal(pocketId);
};

const deletePocket = (pocketId) => {
    if (confirm('¿Estás seguro de que quieres eliminar este bolsillo?')) {
        appState.pockets = appState.pockets.filter(p => p.id !== pocketId);
        updatePocketsList();
        updateDashboard();
        saveToStorage();
        showToast('Bolsillo eliminado', 'El bolsillo ha sido eliminado correctamente', 'info');
    }
};

const openAddMoneyModal = (pocketId) => {
    const modal = document.getElementById('add-money-modal');
    const form = document.getElementById('add-money-form');

    form.reset();
    appState.currentEditingId = pocketId;
    modal.classList.add('active');
};

const closeAddMoneyModal = () => {
    document.getElementById('add-money-modal').classList.remove('active');
    appState.currentEditingId = null;
};

const addMoneyToPocket = (amount) => {
    const pocketIndex = appState.pockets.findIndex(p => p.id === appState.currentEditingId);
    if (pocketIndex !== -1) {
        appState.pockets[pocketIndex].current += parseInt(amount);

        // Also create a transaction
        const pocket = appState.pockets[pocketIndex];
        const newTransaction = {
            id: generateId(),
            type: 'expense',
            description: `Ahorro en ${pocket.name}`,
            amount: parseInt(amount),
            category: 'savings',
            date: new Date().toISOString().split('T')[0]
        };
        appState.transactions.push(newTransaction);

        updatePocketsList();
        updateTransactionsList();
        updateDashboard();
        saveToStorage();
        closeAddMoneyModal();
        showToast('Dinero agregado', `Se agregaron ${formatCurrency(amount)} al bolsillo`, 'success');
    }
};

// Goal Functions
const updateGoalsList = () => {
  const container = document.getElementById('goals-grid');
  if (!container) return;

  // Sin metas → empty state centrado (como en tu imagen)
  if (!appState.goals || appState.goals.length === 0) {
    container.innerHTML = `
      <div class="goals-empty">
        <h4>No tienes metas financieras creadas</h4>
        <p>Define tus objetivos y haz seguimiento de tu progreso</p>
        <button class="btn-primary" onclick="openGoalModal()">
          <i class="fas fa-plus"></i> Crear Meta
        </button>
      </div>
    `;
    return;
  }

  // Con metas → tarjetas
  container.innerHTML = appState.goals.map(goal => {
    const percentage = Math.min((goal.currentAmount / goal.targetAmount) * 100, 100);
    const daysLeft = Math.ceil((new Date(goal.deadline) - new Date()) / (1000 * 60 * 60 * 24));
    return `
      <div class="goal-card">
        <div class="goal-header">
          <h3 class="goal-title">${goal.title}</h3>
          <div class="goal-deadline">
            ${daysLeft > 0 ? `${daysLeft} días restantes` : 'Vencida'} • ${formatDate(goal.deadline)}
          </div>
        </div>

        <div class="goal-amount">
          <span class="goal-current">${formatCurrency(goal.currentAmount || 0)}</span>
          <span class="goal-target">de ${formatCurrency(goal.targetAmount)}</span>
        </div>

        <div class="progress-bar">
          <div class="progress-fill" style="width:${percentage}%;"></div>
        </div>

        <div class="goal-actions">
          <button class="btn-primary" onclick="openAddProgressModal('${goal.id}')">
            <i class="fas fa-plus"></i> Progreso
          </button>
          <button class="btn-secondary" onclick="editGoal('${goal.id}')">
            <i class="fas fa-edit"></i>
          </button>
          <button class="btn-danger" onclick="deleteGoal('${goal.id}')">
            <i class="fas fa-trash"></i>
          </button>
        </div>
      </div>
    `;
  }).join('');
};

const openGoalModal = (goalId = null) => {
    const modal = document.getElementById('goal-modal');
    const form = document.getElementById('goal-form');
    const title = document.getElementById('goal-modal-title');

    form.reset();
    appState.currentEditingId = goalId;

    if (goalId) {
        const goal = appState.goals.find(g => g.id === goalId);
        if (goal) {
            title.textContent = 'Editar Meta';
            document.getElementById('goal-title').value = goal.title;
            document.getElementById('goal-amount').value = goal.targetAmount;
            document.getElementById('goal-deadline').value = goal.deadline;
            document.getElementById('goal-description').value = goal.description || '';
        }
    } else {
        title.textContent = 'Nueva Meta';
        // Set default deadline to 1 year from now
        const nextYear = new Date();
        nextYear.setFullYear(nextYear.getFullYear() + 1);
        document.getElementById('goal-deadline').value = nextYear.toISOString().split('T')[0];
    }

    modal.classList.add('active');
};

const closeGoalModal = () => {
    document.getElementById('goal-modal').classList.remove('active');
    appState.currentEditingId = null;
};

const saveGoal = (formData) => {
    const goalData = {
        title: formData.title,
        targetAmount: parseInt(formData.targetAmount),
        deadline: formData.deadline,
        description: formData.description
    };

    if (appState.currentEditingId) {
        const index = appState.goals.findIndex(g => g.id === appState.currentEditingId);
        if (index !== -1) {
            appState.goals[index] = { ...appState.goals[index], ...goalData };
            showToast('Meta actualizada', 'La meta ha sido actualizada correctamente', 'success');
        }
    } else {
        const newGoal = {
            id: generateId(),
            currentAmount: 0,
            ...goalData
        };
        appState.goals.push(newGoal);
        showToast('Meta creada', 'La meta ha sido creada correctamente', 'success');
    }

    updateGoalsList();
    updateDashboard();
    saveToStorage();
    closeGoalModal();
};

const editGoal = (goalId) => {
    openGoalModal(goalId);
};

const deleteGoal = (goalId) => {
    if (confirm('¿Estás seguro de que quieres eliminar esta meta?')) {
        appState.goals = appState.goals.filter(g => g.id !== goalId);
        updateGoalsList();
        updateDashboard();
        saveToStorage();
        showToast('Meta eliminada', 'La meta ha sido eliminada correctamente', 'info');
    }
};

const openAddProgressModal = (goalId) => {
    const modal = document.getElementById('add-progress-modal');
    const form = document.getElementById('add-progress-form');

    form.reset();
    appState.currentEditingId = goalId;
    modal.classList.add('active');
};

const closeAddProgressModal = () => {
    document.getElementById('add-progress-modal').classList.remove('active');
    appState.currentEditingId = null;
};

const addProgressToGoal = (amount) => {
    const goalIndex = appState.goals.findIndex(g => g.id === appState.currentEditingId);
    if (goalIndex !== -1) {
        appState.goals[goalIndex].currentAmount += parseInt(amount);

        // Also create a transaction
        const goal = appState.goals[goalIndex];
        const newTransaction = {
            id: generateId(),
            type: 'expense',
            description: `Progreso en meta: ${goal.title}`,
            amount: parseInt(amount),
            category: 'savings',
            date: new Date().toISOString().split('T')[0]
        };
        appState.transactions.push(newTransaction);

        updateGoalsList();
        updateTransactionsList();
        updateDashboard();
        saveToStorage();
        closeAddProgressModal();
        showToast('Progreso agregado', `Se agregaron ${formatCurrency(amount)} a la meta`, 'success');
    }
};

// Analysis Functions
const updateAnalysis = () => {
    updateWeeklyStats();
    updateDailyExpensesChart();
    updateTopCategories();
    updateComparison();
};

const updateWeeklyStats = () => {
    const now = new Date();
    const weekStart = new Date(now.setDate(now.getDate() - now.getDay()));
    const weekEnd = new Date(weekStart);
    weekEnd.setDate(weekEnd.getDate() + 6);

    const weeklyTransactions = appState.transactions.filter(t => {
        const transactionDate = new Date(t.date);
        return transactionDate >= weekStart && transactionDate <= weekEnd;
    });

    const weeklyIncome = weeklyTransactions
        .filter(t => t.type === 'income')
        .reduce((sum, t) => sum + t.amount, 0);

    const weeklyExpenses = weeklyTransactions
        .filter(t => t.type === 'expense')
        .reduce((sum, t) => sum + t.amount, 0);

    const weeklyBalance = weeklyIncome - weeklyExpenses;

    document.getElementById('weekly-income').textContent = formatCurrency(weeklyIncome);
    document.getElementById('weekly-expenses').textContent = formatCurrency(weeklyExpenses);
    document.getElementById('weekly-balance').textContent = formatCurrency(weeklyBalance);
    document.getElementById('weekly-balance').className = `stat-value ${weeklyBalance >= 0 ? 'income' : 'expense'}`;
};

// Fuerza la navegación de los links con URL real, aunque otro listener haga preventDefault
document.querySelectorAll('a.nav-link').forEach(a => {
  a.addEventListener('click', function (e) {
    const href = this.getAttribute('href');
    if (href && href !== '#') {
      // Evita cualquier SPA y navega sí o sí
      window.location.href = href;
    }
  }, true); // <-- en captura, se ejecuta antes que otros listeners en burbujeo
});


const updateDailyExpensesChart = () => {
    const canvas = document.getElementById('daily-expenses-chart');
    const ctx = canvas.getContext('2d');

    // Clear existing chart
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // Get last 7 days data
    const dailyData = [];
    const today = new Date();

    for (let i = 6; i >= 0; i--) {
        const date = new Date(today);
        date.setDate(date.getDate() - i);

        const dayExpenses = appState.transactions
            .filter(t => t.type === 'expense' && t.date === date.toISOString().split('T')[0])
            .reduce((sum, t) => sum + t.amount, 0);

        dailyData.push({
            day: date.toLocaleDateString('es-CO', { weekday: 'short' }),
            expenses: dayExpenses
        });
    }

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: dailyData.map(d => d.day),
            datasets: [{
                label: 'Gastos Diarios',
                data: dailyData.map(d => d.expenses),
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
                    ticks: {
                        callback: function (value) {
                            return formatCurrency(value);
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
};

const updateTopCategories = () => {
    const container = document.getElementById('top-categories');

    // Get expense data by category for current month
    const currentMonth = new Date().getMonth();
    const currentYear = new Date().getFullYear();

    const monthlyExpenses = appState.transactions.filter(t => {
        const transactionDate = new Date(t.date);
        return t.type === 'expense' &&
            transactionDate.getMonth() === currentMonth &&
            transactionDate.getFullYear() === currentYear;
    });

    const categoryTotals = {};
    monthlyExpenses.forEach(expense => {
        categoryTotals[expense.category] = (categoryTotals[expense.category] || 0) + expense.amount;
    });

    const sortedCategories = Object.entries(categoryTotals)
        .sort(([, a], [, b]) => b - a)
        .slice(0, 5);

    if (sortedCategories.length === 0) {
        container.innerHTML = '<p class="text-center">No hay gastos este mes</p>';
        return;
    }

    container.innerHTML = sortedCategories.map(([category, amount]) => `
            <div class="category-item">
                <span class="category-name">${getCategoryName(category)}</span>
                <span class="category-amount">${formatCurrency(amount)}</span>
            </div>
        `).join('');
};

const updateComparison = () => {
    const container = document.getElementById('comparison-chart');

    // Get current week and previous week data
    const now = new Date();
    const currentWeekStart = new Date(now.setDate(now.getDate() - now.getDay()));
    const currentWeekEnd = new Date(currentWeekStart);
    currentWeekEnd.setDate(currentWeekEnd.getDate() + 6);

    const previousWeekStart = new Date(currentWeekStart);
    previousWeekStart.setDate(previousWeekStart.getDate() - 7);
    const previousWeekEnd = new Date(previousWeekStart);
    previousWeekEnd.setDate(previousWeekEnd.getDate() + 6);

    const currentWeekExpenses = appState.transactions
        .filter(t => {
            const transactionDate = new Date(t.date);
            return t.type === 'expense' &&
                transactionDate >= currentWeekStart &&
                transactionDate <= currentWeekEnd;
        })
        .reduce((sum, t) => sum + t.amount, 0);

    const previousWeekExpenses = appState.transactions
        .filter(t => {
            const transactionDate = new Date(t.date);
            return t.type === 'expense' &&
                transactionDate >= previousWeekStart &&
                transactionDate <= previousWeekEnd;
        })
        .reduce((sum, t) => sum + t.amount, 0);

    const difference = currentWeekExpenses - previousWeekExpenses;
    const percentageChange = previousWeekExpenses > 0 ?
        ((difference / previousWeekExpenses) * 100) : 0;

    container.innerHTML = `
            <div style="text-align: center;">
                <div style="font-size: 2rem; font-weight: var(--font-weight-medium); margin-bottom: 1rem;">
                    <span style="color: ${difference >= 0 ? 'var(--destructive)' : 'var(--chart-4)'};">
                        ${difference >= 0 ? '+' : ''}${formatCurrency(difference)}
                    </span>
                </div>
                <div style="color: var(--muted-foreground);">
                    ${Math.abs(percentageChange).toFixed(1)}% 
                    ${difference >= 0 ? 'más' : 'menos'} que la semana anterior
                </div>
                <div style="margin-top: 1rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; text-align: center;">
                    <div>
                        <div style="font-size: 0.875rem; color: var(--muted-foreground);">Semana Anterior</div>
                        <div style="font-size: 1.25rem; font-weight: var(--font-weight-medium);">
                            ${formatCurrency(previousWeekExpenses)}
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 0.875rem; color: var(--muted-foreground);">Semana Actual</div>
                        <div style="font-size: 1.25rem; font-weight: var(--font-weight-medium);">
                            ${formatCurrency(currentWeekExpenses)}
                        </div>
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
    const calendar = document.getElementById('calendar');
    const monthSpan = document.getElementById('current-month');

    const firstDay = new Date(appState.currentYear, appState.currentMonth, 1);
    const lastDay = new Date(appState.currentYear, appState.currentMonth + 1, 0);
    const startDate = new Date(firstDay);
    startDate.setDate(startDate.getDate() - firstDay.getDay());

    monthSpan.textContent = new Date(appState.currentYear, appState.currentMonth, 1)
        .toLocaleDateString('es-CO', { month: 'long', year: 'numeric' });

    // Days of week header
    const daysOfWeek = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
    let calendarHTML = daysOfWeek.map(day =>
        `<div class="calendar-day" style="font-weight: var(--font-weight-medium); background-color: var(--muted);">${day}</div>`
    ).join('');

    // Calendar days
    const today = new Date();
    const currentDate = new Date(startDate);

    for (let i = 0; i < 42; i++) { // 6 weeks * 7 days
        const isCurrentMonth = currentDate.getMonth() === appState.currentMonth;
        const isToday = currentDate.toDateString() === today.toDateString();
        const dateString = currentDate.toISOString().split('T')[0];
        const hasReminder = appState.reminders.some(r => r.date === dateString);

        let classes = 'calendar-day';
        if (!isCurrentMonth) classes += ' other-month';
        if (isToday) classes += ' today';
        if (hasReminder) classes += ' has-reminder';

        calendarHTML += `<div class="${classes}">${currentDate.getDate()}</div>`;
        currentDate.setDate(currentDate.getDate() + 1);
    }

    calendar.innerHTML = calendarHTML;
};

const updateRemindersList = () => {
    const container = document.getElementById('reminders-list');

    // Get upcoming reminders (next 30 days)
    const today = new Date();
    const thirtyDaysFromNow = new Date();
    thirtyDaysFromNow.setDate(today.getDate() + 30);

    const upcomingReminders = appState.reminders
        .filter(r => {
            const reminderDate = new Date(r.date);
            return reminderDate >= today && reminderDate <= thirtyDaysFromNow;
        })
        .sort((a, b) => new Date(a.date) - new Date(b.date));

    if (upcomingReminders.length === 0) {
        container.innerHTML = '<p class="text-center">No hay recordatorios próximos</p>';
        return;
    }

    container.innerHTML = upcomingReminders.map(reminder => {
        const daysUntil = Math.ceil((new Date(reminder.date) - new Date()) / (1000 * 60 * 60 * 24));
        return `
                <div class="reminder-item">
                    <div class="reminder-title">${reminder.title}</div>
                    <div class="reminder-date">
                        ${formatDate(reminder.date)}
                        ${daysUntil === 0 ? ' (Hoy)' : daysUntil === 1 ? ' (Mañana)' : ` (${daysUntil} días)`}
                    </div>
                    ${reminder.amount ? `<div class="reminder-amount">${formatCurrency(reminder.amount)}</div>` : ''}
                    <div style="margin-top: 0.5rem;">
                        <button class="btn-secondary" onclick="editReminder('${reminder.id}')" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-danger" onclick="deleteReminder('${reminder.id}')" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
    }).join('');
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
    const modal = document.getElementById('reminder-modal');
    const form = document.getElementById('reminder-form');
    const title = document.getElementById('reminder-modal-title');

    form.reset();
    appState.currentEditingId = reminderId;

    if (reminderId) {
        const reminder = appState.reminders.find(r => r.id === reminderId);
        if (reminder) {
            title.textContent = 'Editar Recordatorio';
            document.getElementById('reminder-title').value = reminder.title;
            document.getElementById('reminder-amount').value = reminder.amount || '';
            document.getElementById('reminder-date').value = reminder.date;
            document.getElementById('reminder-type').value = reminder.type;
            document.getElementById('reminder-recurring').value = reminder.recurring;
            document.getElementById('reminder-description').value = reminder.description || '';
        }
    } else {
        title.textContent = 'Nuevo Recordatorio';
        document.getElementById('reminder-date').value = new Date().toISOString().split('T')[0];
    }

    modal.classList.add('active');
};

const closeReminderModal = () => {
    document.getElementById('reminder-modal').classList.remove('active');
    appState.currentEditingId = null;
};

const saveReminder = (formData) => {
    const reminderData = {
        title: formData.title,
        amount: formData.amount ? parseInt(formData.amount) : null,
        date: formData.date,
        type: formData.type,
        recurring: formData.recurring,
        description: formData.description
    };

    if (appState.currentEditingId) {
        const index = appState.reminders.findIndex(r => r.id === appState.currentEditingId);
        if (index !== -1) {
            appState.reminders[index] = { ...appState.reminders[index], ...reminderData };
            showToast('Recordatorio actualizado', 'El recordatorio ha sido actualizado correctamente', 'success');
        }
    } else {
        const newReminder = {
            id: generateId(),
            ...reminderData
        };
        appState.reminders.push(newReminder);
        showToast('Recordatorio creado', 'El recordatorio ha sido creado correctamente', 'success');
    }

    updateCalendar();
    saveToStorage();
    closeReminderModal();
};

const editReminder = (reminderId) => {
    openReminderModal(reminderId);
};

const deleteReminder = (reminderId) => {
    if (confirm('¿Estás seguro de que quieres eliminar este recordatorio?')) {
        appState.reminders = appState.reminders.filter(r => r.id !== reminderId);
        updateCalendar();
        saveToStorage();
        showToast('Recordatorio eliminado', 'El recordatorio ha sido eliminado correctamente', 'info');
    }
};

// Profile Functions
const updateProfile = () => {
    if (appState.user) {
        document.getElementById('profile-firstname').value = appState.user.firstName;
        document.getElementById('profile-lastname').value = appState.user.lastName;
        document.getElementById('profile-email').value = appState.user.email;
        document.getElementById('profile-university').value = appState.user.university;
        document.getElementById('profile-program').value = appState.user.studyProgram;
    }

    // Update usage statistics
    document.getElementById('total-transactions').textContent = appState.transactions.length;
    document.getElementById('total-pockets').textContent = appState.pockets.length;
    document.getElementById('total-goals').textContent = appState.goals.length;
    document.getElementById('consecutive-days').textContent = calculateConsecutiveDays();
};

const calculateConsecutiveDays = () => {
    if (appState.transactions.length === 0) return 0;

    const dates = [...new Set(appState.transactions.map(t => t.date))].sort();
    const today = new Date().toISOString().split('T')[0];
    let consecutiveDays = 0;
    const currentDate = new Date(today);

    while (dates.includes(currentDate.toISOString().split('T')[0])) {
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
        studyProgram: formData.studyProgram
    };

    updateUserInfo();
    saveToStorage();
    showToast('Perfil actualizado', 'Tu perfil ha sido actualizado correctamente', 'success');
};

// Utility helper functions
const getCategoryName = (category) => {
    const categories = {
        food: 'Alimentación',
        transport: 'Transporte',
        education: 'Educación',
        entertainment: 'Entretenimiento',
        health: 'Salud',
        shopping: 'Compras',
        salary: 'Salario',
        scholarship: 'Beca',
        family: 'Familia',
        freelance: 'Freelance',
        savings: 'Ahorros',
        other: 'Otros'
    };
    return categories[category] || category;
};

const getIconClass = (icon) => {
    const icons = {
        'piggy-bank': 'piggy-bank',
        'graduation-cap': 'graduation-cap',
        'plane': 'plane',
        'heart': 'heart',
        'car': 'car',
        'home': 'home',
        'gift': 'gift',
        'star': 'star'
    };
    return icons[icon] || 'piggy-bank';
};

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    // Load data from localStorage
    loadFromStorage();

    // Check if user is already logged in
    if (appState.user) {
        showMainApp();
    } else {
        showLanding();
    }

    // Create hero chart
    setTimeout(() => {
        const heroCanvas = document.getElementById('hero-chart');
        if (heroCanvas) {
            const ctx = heroCanvas.getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
                    datasets: [{
                        data: [800000, 950000, 1100000, 1050000, 1200000, 1250000],
                        borderColor: 'rgba(255, 255, 255, 0.8)',
                        backgroundColor: 'rgba(255, 255, 255, 0.1)',
                        tension: 0.4,
                        borderWidth: 2,
                        pointBackgroundColor: 'white',
                        pointBorderColor: 'rgba(255, 255, 255, 0.8)',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: { display: false },
                        y: { display: false }
                    },
                    interaction: { intersect: false }
                }
            });
        }
    }, 100);

   

    // Calendar navigation
    const prevBtn = document.getElementById('prev-month');
    if (prevBtn) {
        prevBtn.addEventListener('click', previousMonth);
    }

    const nextBtn = document.getElementById('next-month');
    if (nextBtn) {
        nextBtn.addEventListener('click', nextMonth);
    }

    // Form submissions
    document.getElementById('login-form').addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const email = formData.get('email');
        const password = formData.get('password');

        if (handleLogin(email, password)) {
            document.getElementById('login-error').classList.remove('show');
        } else {
            const errorDiv = document.getElementById('login-error');
            errorDiv.textContent = 'Credenciales incorrectas. Intenta nuevamente o regístrate.';
            errorDiv.classList.add('show');
        }
    });

    document.getElementById('register-form').addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const userData = {
            firstName: formData.get('firstName'),
            lastName: formData.get('lastName'),
            email: formData.get('email'),
            password: formData.get('password'),
            university: formData.get('university'),
            studyProgram: formData.get('studyProgram')
        };

        handleRegister(userData);
    });

    document.getElementById('transaction-form').addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const transactionData = {
            type: formData.get('type'),
            description: formData.get('description'),
            amount: formData.get('amount'),
            category: formData.get('category'),
            date: formData.get('date')
        };

        saveTransaction(transactionData);
    });

    document.getElementById('pocket-form').addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const pocketData = {
            name: formData.get('name'),
            target: formData.get('target'),
            icon: formData.get('icon'),
            color: formData.get('color')
        };

        savePocket(pocketData);
    });

    document.getElementById('goal-form').addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const goalData = {
            title: formData.get('title'),
            targetAmount: formData.get('targetAmount'),
            deadline: formData.get('deadline'),
            description: formData.get('description')
        };

        saveGoal(goalData);
    });

    document.getElementById('reminder-form').addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const reminderData = {
            title: formData.get('title'),
            amount: formData.get('amount'),
            date: formData.get('date'),
            type: formData.get('type'),
            recurring: formData.get('recurring'),
            description: formData.get('description')
        };

        saveReminder(reminderData);
    });

    document.getElementById('add-money-form').addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const amount = formData.get('amount');

        addMoneyToPocket(amount);
    });

    document.getElementById('add-progress-form').addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const amount = formData.get('amount');

        addProgressToGoal(amount);
    });

    document.getElementById('profile-form').addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const profileData = {
            firstName: formData.get('firstName'),
            lastName: formData.get('lastName'),
            email: formData.get('email'),
            university: formData.get('university'),
            studyProgram: formData.get('studyProgram')
        };

        saveProfile(profileData);
    });

    // Filter listeners
    document.getElementById('transaction-type-filter').addEventListener('change', updateTransactionsList);
    document.getElementById('transaction-category-filter').addEventListener('change', updateTransactionsList);
    document.getElementById('transaction-date-filter').addEventListener('change', updateTransactionsList);

    // Modal close listeners
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('active');
            }
        });
    });

    const goalsGrid = document.getElementById('goals-grid');
  if (goalsGrid && typeof updateGoalsList === 'function') {
    updateGoalsList();
  }

   const calendarEl = document.getElementById('calendar');
  if (calendarEl && typeof updateCalendar === 'function') {
    updateCalendar();

    const prevBtn = document.getElementById('prev-month');
    if (prevBtn) prevBtn.addEventListener('click', previousMonth);

    const nextBtn = document.getElementById('next-month');
    if (nextBtn) nextBtn.addEventListener('click', nextMonth);
  }

  document.addEventListener('click', (e) => {
  const link = e.target.closest('a.nav-link');
  if (!link) return;

  // Solo bloquea si quieres SPA explícitamente
  if (link.getAttribute('data-spa') === 'true') {
    e.preventDefault();
    const section = link.getAttribute('data-section');
    if (section) showSection(section);
  }
  // Si no tiene data-spa="true", deja navegar normal
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
window.openPocketModal = openPocketModal;
window.closePocketModal = closePocketModal;
window.editPocket = editPocket;
window.deletePocket = deletePocket;
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
