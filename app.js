// Change this to your Railway Node.js public URL once deployed!
const API_BASE_URL =
  "https://waste-management-system-production-0055.up.railway.app"; // Update with your actual Railway URL

let bins = [];
let notifiedBins = {};
try {
  const stored = JSON.parse(localStorage.getItem('ecosync_notified_bins'));
  // Backwards compatibility: update old arrays to timestamp objects automatically
  if (Array.isArray(stored)) {
    stored.forEach(id => notifiedBins[id] = Date.now());
  } else if (stored) {
    notifiedBins = stored;
  }
} catch (e) {}

let collectionTracking = {};
try {
  const storedTracking = JSON.parse(localStorage.getItem('ecosync_collection_tracking'));
  if (storedTracking) collectionTracking = storedTracking;
} catch (e) {}

let activeDashboardAlerts = {};
try {
  const storedAlerts = JSON.parse(localStorage.getItem('ecosync_active_alerts'));
  if (storedAlerts) activeDashboardAlerts = storedAlerts;
} catch (e) {}

// Function to fetch live data from our local server
async function fetchLiveData() {
  try {
    // Add a timestamp to the URL to completely prevent browser caching
    const timestamp = new Date().getTime();
    const response = await fetch(
      `${API_BASE_URL}/api/bins?t=${timestamp}`,
      { cache: "no-store" },
    );
    const data = await response.json();

    bins = data.map((bin) => ({
      ...bin,
      lastUpdated: new Date(bin.lastUpdated),
      fillLevel: Number(bin.fillLevel) // Ensure numeric comparison
    }));

    // Check and trigger email notifications for bins approaching full capacity (>= 80%)
    checkBinLevelsAndNotify(bins);

    // Track bin states and log collections automatically
    trackCollections(bins);

    // Check what page we are on by looking for the HTML elements, not the URL
    const dashboardContainer = document.getElementById("fillLevelOverview");
    const tableContainer = document.getElementById("binTableBody");
    const reportsContainer = document.getElementById("reports-page");

    if (dashboardContainer) {
      // Check if the bars are actually drawn
      const missingBars = bins
        .some((bin) => !document.getElementById(`bar-${bin.id}`));

      if (dashboardContainer.innerHTML.trim() === "" || missingBars) {
        initDashboard(); // Draw bars the first time
      } else {
        updateDashboardLive(); // Slide existing bars smoothly
      }
    }

    // Update the bin table page if we are on it
    if (tableContainer) {
      renderBinTable();
    }

    // Update the reports page dynamically if we are on it
    if (reportsContainer) {
      renderReports();
    }
  } catch (error) {
    console.error("Error fetching live data:", error);
  }
}

async function checkBinLevelsAndNotify(currentBins) {
  const freqValue = parseFloat(localStorage.getItem('ecosync_notif_freq_value') || "0");
  const freqUnit = localStorage.getItem('ecosync_notif_freq_unit') || "hours";
  const now = Date.now();
  
  // Read the latest state from localStorage to ensure multiple open tabs stay in sync
  try {
    const stored = JSON.parse(localStorage.getItem('ecosync_notified_bins'));
    if (stored && !Array.isArray(stored)) {
      notifiedBins = stored;
    }
  } catch (e) {}

  let freqMs = 0;
  if (freqUnit === 'seconds') freqMs = freqValue * 1000;
  else if (freqUnit === 'minutes') freqMs = freqValue * 60 * 1000;
  else freqMs = freqValue * 60 * 60 * 1000; // Default to hours

  for (let index = 0; index < currentBins.length; index++) {
    const bin = currentBins[index];
    let shouldNotify = false;
    
    if (bin.fillLevel >= 80) {
      if (!notifiedBins[bin.id]) {
        console.log(`[Alert] ${bin.id} reached ${bin.fillLevel}%. Triggering first email.`);
        shouldNotify = true;
      } else if (freqValue > 0) {
        const msSince = now - notifiedBins[bin.id];
        if (msSince >= freqMs) {
          console.log(`[Alert] ${bin.id} timer expired. Triggering repeat email.`);
          shouldNotify = true;
        }
      } else {
        console.log(`[Alert] ${bin.id} is full but already notified. Waiting to drop below 75% to reset.`);
      }
    }

    if (shouldNotify) {
      // Mark as notified with current timestamp and save
      notifiedBins[bin.id] = now;
      localStorage.setItem('ecosync_notified_bins', JSON.stringify(notifiedBins));
      
      // Retrieve the target email saved in settings, or use a default fallback
      const targetEmail = localStorage.getItem('ecosync_notif_email') || "admin@example.com";

      const payload = {
        email: targetEmail,
        subject: `EcoSync Alert: Bin ${bin.id} is at or above 80% capacity!`,
        message: `<p>Please note that the bin at <b>${bin.location}</b> has reached <b>${bin.fillLevel}%</b> capacity. As it is 80% or above, it needs collection soon.</p>`
      };

      // We add a stagger (delay) using the bin index to prevent Google Apps Script 
      // from silently dropping concurrent requests!
      setTimeout(() => {
        fetch('send_notification_api.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(result => {
          console.log(`Notification for ${bin.id}:`, result.message);
          if (result.success) {
            showNotification(`Email Sent: ${bin.id} is full!`, 'success');
          } else {
            showNotification(`Failed to send email for ${bin.id}: ${result.message}`, 'error');
          }
        })
        .catch(error => {
          console.error('Error sending alert:', error);
          showNotification(`Network error while sending email for ${bin.id}`, 'error');
        });
      }, index * 2500); // 2.5 seconds delay per bin in the list
      
    } else if (bin.fillLevel < 75 && notifiedBins[bin.id]) {
      // Reset the notification state immediately if the bin drops below 75%
      console.log(`[Alert] ${bin.id} dropped to ${bin.fillLevel}%. Notification lock reset!`);
      delete notifiedBins[bin.id];
      localStorage.setItem('ecosync_notified_bins', JSON.stringify(notifiedBins));
    }
  }
}

function showNotification(message, type = 'success') {
  // Create a container for notifications if it doesn't exist yet
  let container = document.getElementById('notification-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'notification-container';
    container.className = 'fixed top-5 right-5 z-50 flex flex-col gap-3 pointer-events-none';
    document.body.appendChild(container);
  }

  // Build the pop-up toast element
  const toast = document.createElement('div');
  const bgColor = type === 'success' ? 'bg-emerald-100 border-emerald-200 text-emerald-800' : 'bg-red-100 border-red-200 text-red-800';
  const icon = type === 'success' ? 'fa-check-circle text-emerald-600' : 'fa-exclamation-circle text-red-600';
  
  toast.className = `flex items-center gap-3 p-4 rounded-xl shadow-lg border ${bgColor} transform transition-all duration-500 translate-x-full opacity-0 pointer-events-auto max-w-sm`;
  toast.innerHTML = `<i class="fas ${icon} text-xl flex-shrink-0"></i><span class="font-semibold text-sm">${message}</span>`;

  container.appendChild(toast);

  // Slide it in smoothly, then remove it after 5 seconds
  requestAnimationFrame(() => toast.classList.remove('translate-x-full', 'opacity-0'));
  setTimeout(() => toast.classList.add('translate-x-full', 'opacity-0'), 4500);
  setTimeout(() => toast.remove(), 5000);
}

let collectionHistory = [];
try {
  const cachedHistory = JSON.parse(localStorage.getItem('ecosync_history_cache'));
  if (cachedHistory && Array.isArray(cachedHistory)) {
    collectionHistory = cachedHistory.map(record => ({
      ...record,
      date: new Date(record.date)
    }));
  }
} catch (e) {}

async function fetchCollectionHistory() {
  try {
    const response = await fetch('get_collection_api.php');
    const result = await response.json();
    if (result.success) {
      collectionHistory = result.data.map(record => ({
        id: record.id,
        date: new Date(record.date),
        binId: record.binId,
        location: record.location,
        actionTaken: record.actionTaken
      }));

      localStorage.setItem('ecosync_history_cache', JSON.stringify(collectionHistory));
      
      // Update the UI immediately after the database history is loaded
      if (document.getElementById("historyTableBody")) filterHistory();
      if (document.getElementById("reports-page")) renderReports();
      updateDashboardStats();
      updateNotificationDropdown();
    }
  } catch (error) {
    console.error("Error loading collection history:", error);
  }
}

function trackCollections(currentBins) {
  const now = Date.now();
  let trackingUpdated = false;

  currentBins.forEach(bin => {
    if (!collectionTracking[bin.id]) {
      collectionTracking[bin.id] = { wasFull: false, emptyTimestamp: null };
      trackingUpdated = true;
    }

    const tracking = collectionTracking[bin.id];

    // Track when a bin reaches 80% or higher
    if (bin.fillLevel >= 80) {
      if (!tracking.wasFull || tracking.emptyTimestamp !== null) {
        tracking.wasFull = true;
        tracking.emptyTimestamp = null;
        trackingUpdated = true;
      }
    } 
    // Track when a previously full bin drops to exactly 0%
    else if (bin.fillLevel === 0 && tracking.wasFull) {
      if (!tracking.emptyTimestamp) {
        tracking.emptyTimestamp = now;
        trackingUpdated = true;
        console.log(`[Collection] ${bin.id} hit 0%. Starting 1-minute timer...`);
      } else if (now - tracking.emptyTimestamp >= 60000) { // 60000 ms = 1 minute
        console.log(`[Collection] ${bin.id} has been at 0% for 1 minute. Marking as collected.`);
        recordCollection(bin);
        
        // Reset tracking for this bin
        tracking.wasFull = false;
        tracking.emptyTimestamp = null;
        trackingUpdated = true;
      }
    } 
    // If the bin level fluctuates above 0% before the 1 minute is up, cancel the timer
    else if (bin.fillLevel > 0 && tracking.emptyTimestamp) {
      tracking.emptyTimestamp = null;
      trackingUpdated = true;
      console.log(`[Collection] ${bin.id} fill level increased before timer finished. Timer reset.`);
    }
  });

  // Save tracking state so it survives page reloads
  if (trackingUpdated) {
    localStorage.setItem('ecosync_collection_tracking', JSON.stringify(collectionTracking));
  }
}

async function recordCollection(bin) {
  const payload = {
    binId: bin.id,
    location: bin.location,
    actionTaken: "Emptied"
  };

  try {
    const response = await fetch('add_collection_api.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    const result = await response.json();
    
    if (result.success) {
      const newRecord = {
        id: result.id,
        date: new Date(result.date),
        binId: bin.id,
        location: bin.location,
        actionTaken: "Emptied"
      };
      
      collectionHistory.unshift(newRecord);
      localStorage.setItem('ecosync_history_cache', JSON.stringify(collectionHistory));
      showNotification(`Bin ${bin.id} was emptied and marked as collected!`, 'success');
      
      if (document.getElementById("historyTableBody")) filterHistory();
      if (document.getElementById("reports-page")) renderReports();
      updateDashboardStats();
      updateNotificationDropdown();
    } else {
      showNotification(`Failed to save record: ${result.message}`, 'error');
    }
  } catch (error) {
    console.error("Error saving collection:", error);
  }
}

function formatDate(date) {
  const options = {
    month: "short",
    day: "numeric",
    year: "numeric",
    hour: "numeric",
    minute: "2-digit",
    hour12: true,
  };
  return date.toLocaleString("en-US", options);
}

function formatDateOnly(date) {
  const options = { month: "short", day: "numeric", year: "numeric" };
  return date.toLocaleString("en-US", options);
}

function formatTimeOnly(date) {
  const options = { hour: "numeric", minute: "2-digit", hour12: true };
  return date.toLocaleString("en-US", options);
}

// Initial draw of the dashboard
function initDashboard() {
  const fillLevelOverview = document.getElementById("fillLevelOverview");
  if (!fillLevelOverview) return;

  fillLevelOverview.innerHTML = "";

  bins.forEach((bin) => {
    const color =
      bin.fillLevel >= 90
        ? "bg-red-500"
        : bin.fillLevel >= 70
          ? "bg-yellow-500"
          : "bg-emerald-500";
    fillLevelOverview.innerHTML += `
            <div class="space-y-2">
                <div class="flex items-center justify-between text-sm">
                    <span class="font-medium text-gray-700">${bin.id}</span>
                    <span id="text-${bin.id}" class="text-gray-600 font-bold transition-all duration-300">${bin.fillLevel}%</span>
                </div> 
                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                    <div id="bar-${bin.id}" class="progress-bar h-full ${color} transition-all duration-1000 ease-out" style="width: ${bin.fillLevel}%"></div>
                </div>
                <p class="text-xs text-gray-500">${bin.location}</p>
            </div>
        `;
  });

  updateAlerts();
  updateDashboardStats();
}

function updateDashboardStats() {
  const statTotalBins = document.getElementById("statTotalBins");
  const statFullBins = document.getElementById("statFullBins");
  const statAvgFillLevel = document.getElementById("statAvgFillLevel");
  const statCollectedToday = document.getElementById("statCollectedToday");

  if (statTotalBins) statTotalBins.textContent = bins.length;
  if (statFullBins) statFullBins.textContent = bins.filter((b) => b.fillLevel >= 90).length;
  if (statAvgFillLevel) {
    const avg = bins.length > 0 ? bins.reduce((sum, b) => sum + b.fillLevel, 0) / bins.length : 0;
    statAvgFillLevel.textContent = `${Math.round(avg)}%`;
  }
  if (statCollectedToday) {
    const today = new Date().toDateString();
    statCollectedToday.textContent = collectionHistory.filter(
      (r) => r.date.toDateString() === today
    ).length;
  }
}

// This function smoothly animates the bar when new data comes in
function updateDashboardLive() {
  bins.forEach((bin) => {
    const bar = document.getElementById(`bar-${bin.id}`);
    const text = document.getElementById(`text-${bin.id}`);

    if (bar && text) {
      bar.style.width = `${bin.fillLevel}%`;
      text.textContent = `${bin.fillLevel}%`;

      // Swap colors dynamically if the bin crosses a threshold
      bar.className = `progress-bar h-full transition-all duration-1000 ease-out ${bin.fillLevel >= 90 ? "bg-red-500" : bin.fillLevel >= 70 ? "bg-yellow-500" : "bg-emerald-500"}`;
    }
  });

  updateAlerts();
  updateDashboardStats();
}

function updateAlerts() {
  const recentAlerts = document.getElementById("recentAlerts");
  if (!recentAlerts) return;

  const now = Date.now();
  let alertsUpdated = false;

  // 1. Add/Update current alerts and refresh their 1-minute expiration timer
  bins.forEach((bin) => {
    if (bin.fillLevel >= 70) {
      const type = bin.fillLevel >= 90 ? "Full" : "Warning";
      const existing = activeDashboardAlerts[bin.id];
      
      if (!existing || existing.fillLevel !== bin.fillLevel || existing.type !== type) {
        activeDashboardAlerts[bin.id] = {
          id: bin.id,
          type: type,
          fillLevel: bin.fillLevel,
          location: bin.location,
          lastUpdated: bin.lastUpdated,
          expiresAt: now + 60000 // 1 minute from now
        };
        alertsUpdated = true;
      } else {
        // Keep extending the 1-minute timer as long as the bin is still full/warning
        activeDashboardAlerts[bin.id].expiresAt = now + 60000;
        alertsUpdated = true;
      }
    }
  });

  // 2. Clean up expired alerts (bins that dropped below 70% and 1 minute has passed)
  Object.keys(activeDashboardAlerts).forEach((id) => {
    const currentBin = bins.find((b) => b.id === id);
    if (!currentBin || (currentBin.fillLevel < 70 && now >= activeDashboardAlerts[id].expiresAt)) {
      delete activeDashboardAlerts[id];
      alertsUpdated = true;
    }
  });

  if (alertsUpdated) {
    localStorage.setItem('ecosync_active_alerts', JSON.stringify(activeDashboardAlerts));
  }

  recentAlerts.innerHTML = "";

  const alertList = Object.values(activeDashboardAlerts);
  alertList.sort((a, b) => b.fillLevel - a.fillLevel); // Highest fill levels first

  const fullBins = alertList.filter((alert) => alert.type === "Full");
  const warningBins = alertList.filter((alert) => alert.type === "Warning").slice(0, 3);

  const recentAlertsTitle = document.getElementById("recentAlertsTitle");
  if (recentAlertsTitle) {
    recentAlertsTitle.textContent = `Recent Alerts (${fullBins.length + warningBins.length})`;
  }

  fullBins.forEach((alert) => {
    recentAlerts.innerHTML += `
            <div class="flex items-start gap-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="p-2 bg-red-100 rounded-lg flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-900 mb-1">${alert.id} - Full (${alert.fillLevel}%)</h3>
                    <p class="text-sm text-gray-600 mb-2">${alert.location}</p>
                    <p class="text-xs text-gray-500">Last updated: ${formatDate(new Date(alert.lastUpdated))}</p>
                </div>
            </div>
        `;
  });

  warningBins.forEach((alert) => {
    recentAlerts.innerHTML += `
            <div class="flex items-start gap-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="p-2 bg-yellow-100 rounded-lg flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-900 mb-1">${alert.id} - Warning (${alert.fillLevel}%)</h3>
                    <p class="text-sm text-gray-600 mb-2">${alert.location}</p>
                    <p class="text-xs text-gray-500">Last updated: ${formatDate(new Date(alert.lastUpdated))}</p>
                </div>
            </div>
        `;
  });

  updateNotificationDropdown();
}

function updateNotificationDropdown() {
  const notifList = document.getElementById("notificationList");
  const notifBadge = document.getElementById("notificationBadge");
  const notifCount = document.getElementById("notificationCount");
  
  if (!notifList) return;

  let notifications = [];

  // 1. Pull in active warning/full alerts
  Object.values(activeDashboardAlerts).forEach(alert => {
    notifications.push({
      id: `alert-${alert.id}`,
      title: `Bin ${alert.type}: ${alert.id}`,
      message: `${alert.location} is at ${alert.fillLevel}%.`,
      time: new Date(alert.lastUpdated),
      type: alert.type === 'Full' ? 'danger' : 'warning'
    });
  });

  // 2. Pull in the 10 most recent collections
  collectionHistory.slice(0, 10).forEach(record => {
    notifications.push({
      id: `col-${record.id}`,
      title: `Bin Collected: ${record.binId}`,
      message: `${record.location} was emptied.`,
      time: new Date(record.date),
      type: 'success'
    });
  });

  // Sort chronologically (newest first)
  notifications.sort((a, b) => b.time - a.time);

  if (notifications.length > 0) {
    notifList.innerHTML = '';
    notifications.forEach(n => {
      let icon, bgColor, textColor;
      if (n.type === 'danger') {
        icon = 'fa-exclamation-triangle'; bgColor = 'bg-red-100 dark:bg-red-500/20'; textColor = 'text-red-600 dark:text-red-400';
      } else if (n.type === 'warning') {
        icon = 'fa-exclamation-circle'; bgColor = 'bg-yellow-100 dark:bg-yellow-500/20'; textColor = 'text-yellow-600 dark:text-yellow-400';
      } else {
        icon = 'fa-check-circle'; bgColor = 'bg-emerald-100 dark:bg-emerald-500/20'; textColor = 'text-emerald-600 dark:text-emerald-400';
      }

      notifList.innerHTML += `
        <div class="p-4 border-b border-gray-100/50 dark:border-slate-700/50 hover:bg-gray-50/50 dark:hover:bg-slate-800/50 transition-colors flex gap-3 cursor-default">
            <div class="w-8 h-8 rounded-full ${bgColor} flex items-center justify-center flex-shrink-0 mt-0.5">
                <i class="fas ${icon} ${textColor} text-sm"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-900 dark:text-white">${n.title}</p>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">${n.message}</p>
                <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1.5 uppercase font-semibold tracking-wider">${formatDate(n.time)}</p>
            </div>
        </div>
      `;
    });
    if (notifBadge) notifBadge.classList.remove('hidden');
    if (notifCount) notifCount.textContent = `${notifications.length} New`;
  } else {
    notifList.innerHTML = `<div class="p-6 text-center text-sm text-gray-500 dark:text-gray-400">No new notifications</div>`;
    if (notifBadge) notifBadge.classList.add('hidden');
    if (notifCount) notifCount.textContent = `0 New`;
  }
}

function renderBinTable(filteredBins = bins) {
  const tbody = document.getElementById("binTableBody");
  if (!tbody) return;

  // Do not re-render if the user is currently editing a location to prevent interrupting their typing
  const activeEdit = tbody.querySelector('div[id^="location-edit-"]:not(.hidden)');
  if (activeEdit) return;

  tbody.innerHTML = "";
  filteredBins.forEach((bin) => {
    const statusColor =
      bin.status === "Full"
        ? "text-red-700 bg-red-50 border-red-200"
        : bin.status === "Warning"
          ? "text-yellow-700 bg-yellow-50 border-yellow-200"
          : "text-emerald-700 bg-emerald-50 border-emerald-200";
    const fillColor =
      bin.fillLevel >= 90
        ? "bg-red-500"
        : bin.fillLevel >= 70
          ? "bg-yellow-500"
          : "bg-emerald-500";
    const fillTextColor =
      bin.fillLevel >= 90
        ? "text-red-600"
        : bin.fillLevel >= 70
          ? "text-yellow-600"
          : "text-emerald-600";

    tbody.innerHTML += `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4"><span class="font-medium text-gray-900">${bin.id}</span></td>
                <td class="px-6 py-4">
                    <div id="location-display-${bin.id}" class="flex items-center justify-between text-gray-600 group">
                        <span class="truncate">${bin.location}</span>
                        <button onclick="toggleEditBinLocation('${bin.id}')" class="text-gray-400 hover:text-indigo-600 opacity-0 group-hover:opacity-100 transition-all focus:opacity-100 ml-2" title="Edit Location">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                    <div id="location-edit-${bin.id}" class="hidden flex items-center gap-2">
                        <input type="text" id="input-location-${bin.id}" value="${bin.location}" class="w-full px-2 py-1.5 text-sm bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-gray-900">
                        <button onclick="saveBinLocation('${bin.id}', '${bin.location}')" class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-full bg-emerald-100 text-emerald-600 hover:bg-emerald-200 transition-colors" title="Save">
                            <i class="fas fa-check text-xs"></i>
                        </button>
                        <button onclick="toggleEditBinLocation('${bin.id}')" class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-full bg-rose-100 text-rose-600 hover:bg-rose-200 transition-colors" title="Cancel">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex-1 max-w-xs">
                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden"> 
                                <div class="progress-bar h-full ${fillColor}" style="width: ${bin.fillLevel}%"></div>
                            </div>
                        </div>
                        <span class="font-semibold ${fillTextColor}">${bin.fillLevel}%</span>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium border ${statusColor}">${bin.status}</span>
                </td>
                <td class="px-6 py-4 text-gray-600 text-sm">${formatDate(bin.lastUpdated)}</td>
            </tr>
        `;
  });

  const binCount = document.getElementById("binCount");
  if (binCount)
    binCount.textContent = `Showing ${filteredBins.length} of ${bins.length} bins`;
}

function filterBins() {
  const binSearch = document.getElementById("binSearch");
  const statusFilter = document.getElementById("statusFilter");
  if (!binSearch || !statusFilter) return;

  const searchTerm = binSearch.value.toLowerCase();
  const status = statusFilter.value;

  const filtered = bins.filter((bin) => {
    const matchesSearch =
      bin.id.toLowerCase().includes(searchTerm) ||
      bin.location.toLowerCase().includes(searchTerm);
    const matchesStatus = status === "all" || bin.status === status;
    return matchesSearch && matchesStatus;
  });

  renderBinTable(filtered);
}

function renderHistoryTable(filteredHistory = collectionHistory) {
  const tbody = document.getElementById("historyTableBody");
  if (!tbody) return;

  tbody.innerHTML = "";
  filteredHistory.forEach((record) => {
    tbody.innerHTML += `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 text-gray-900">${formatDateOnly(record.date)}</td>
                <td class="px-6 py-4 text-gray-600">${formatTimeOnly(record.date)}</td>
                <td class="px-6 py-4"><span class="font-medium text-gray-900">${record.binId}</span></td>
                <td class="px-6 py-4 text-gray-600">${record.location}</td>
                <td class="px-6 py-4">
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">${record.actionTaken}</span>
                </td>
            </tr>
        `;
  });

  const historyCount = document.getElementById("historyCount");
  if (historyCount)
    historyCount.textContent = `Showing ${filteredHistory.length} of ${collectionHistory.length} records`;
}

function filterHistory() {
  const historySearch = document.getElementById("historySearch");
  const startDate = document.getElementById("startDate");
  const endDate = document.getElementById("endDate");
  const clearBtn = document.getElementById("clearDates");

  if (!historySearch || !startDate || !endDate) return;

  const searchTerm = historySearch.value.toLowerCase();
  const start = startDate.value;
  const end = endDate.value;

  if (clearBtn) {
    if (start || end) clearBtn.classList.remove("hidden");
    else clearBtn.classList.add("hidden");
  }

  const filtered = collectionHistory.filter((record) => {
    const matchesSearch =
      record.binId.toLowerCase().includes(searchTerm) ||
      record.location.toLowerCase().includes(searchTerm) ||
      record.actionTaken.toLowerCase().includes(searchTerm);

    let matchesDate = true;
    if (start && end) {
      const recordDate = record.date;
      matchesDate =
        recordDate >= new Date(start) && recordDate <= new Date(end);
    }
    return matchesSearch && matchesDate;
  });

  renderHistoryTable(filtered);
}

function renderReports() {
  // 1. Top Bins Table
  const topBinsBody = document.getElementById("topBinsTableBody");
  if (topBinsBody) {
    const counts = {};
    collectionHistory.forEach(r => {
      counts[r.binId] = (counts[r.binId] || 0) + 1;
    });
    
    const sortedBins = bins.map(b => ({
      id: b.id,
      location: b.location,
      collections: counts[b.id] || 0
    })).sort((a, b) => b.collections - a.collections);
    
    topBinsBody.innerHTML = "";
    sortedBins.forEach(b => {
      topBinsBody.innerHTML += `
        <tr>
          <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">${b.id}</td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${b.location}</td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${b.collections}</td>
        </tr>
      `;
    });
  }

  // 2. Fill Level Distribution Pie Chart
  const fillLevelCtx = document.getElementById('fillLevelChart');
  if (fillLevelCtx) {
    const dist = [0, 0, 0, 0];
    bins.forEach(b => {
      if (b.fillLevel <= 25) dist[0]++;
      else if (b.fillLevel <= 50) dist[1]++;
      else if (b.fillLevel <= 75) dist[2]++;
      else dist[3]++;
    });

    if (!window.fillLevelChartInstance) {
      window.fillLevelChartInstance = new Chart(fillLevelCtx, {
        type: 'pie',
        data: {
          labels: ['0-25%', '26-50%', '51-75%', '76-100%'],
          datasets: [{
            data: dist,
            backgroundColor: ['#34D399', '#60A5FA', '#FCD34D', '#EF4444'],
            borderColor: ['#059669', '#2563EB', '#FBBF24', '#DC2626'],
            borderWidth: 1
          }]
        },
        options: { responsive: true, plugins: { legend: { position: 'top' } } }
      });
    } else {
      window.fillLevelChartInstance.data.datasets[0].data = dist;
      window.fillLevelChartInstance.update();
    }
  }

  // 3. Collection Frequency Line Chart
  const freqCtx = document.getElementById('collectionFrequencyChart');
  if (freqCtx) {
    const mappedDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    const mappedFreq = [0, 0, 0, 0, 0, 0, 0];
    
    collectionHistory.forEach(r => {
      const dayIndex = new Date(r.date).getDay();
      const mappedIndex = dayIndex === 0 ? 6 : dayIndex - 1; // Start week on Monday
      mappedFreq[mappedIndex]++;
    });

    if (!window.collectionFreqChartInstance) {
      window.collectionFreqChartInstance = new Chart(freqCtx, {
        type: 'line',
        data: {
          labels: mappedDays,
          datasets: [{ label: 'Collections', data: mappedFreq, backgroundColor: '#10B981', borderColor: '#059669', borderWidth: 2, tension: 0.3, fill: true }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
      });
    } else {
      window.collectionFreqChartInstance.data.datasets[0].data = mappedFreq;
      window.collectionFreqChartInstance.update();
    }
  }
}

window.exportCSV = function () {
  const csv = [
    ["Date", "Time", "Bin ID", "Location", "Action Taken"],
    ...collectionHistory.map((record) => [
      formatDateOnly(record.date),
      formatTimeOnly(record.date),
      record.binId,
      record.location,
      record.actionTaken,
    ]),
  ]
    .map((row) => row.join(","))
    .join("\n");
  downloadCSV(csv, "collection-history.csv");
};

window.exportReportCSV = function () {
  const csv = [
    ["Bin ID", "Location", "Total Collections"],
    ...bins.map((bin) => {
      const count = collectionHistory.filter((r) => r.binId === bin.id).length;
      return [bin.id, bin.location, count.toString()];
    }),
  ]
    .map((row) => row.join(","))
    .join("\n");
  downloadCSV(csv, "waste-report.csv");
};

function downloadCSV(csv, filename) {
  const blob = new Blob([csv], { type: "text/csv" });
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = filename;
  a.click();
  window.URL.revokeObjectURL(url);
}

window.toggleEditBinLocation = function(id) {
  const displayDiv = document.getElementById(`location-display-${id}`);
  const editDiv = document.getElementById(`location-edit-${id}`);
  
  if (displayDiv && editDiv) {
    if (displayDiv.classList.contains('hidden')) {
      displayDiv.classList.remove('hidden');
      editDiv.classList.add('hidden');
    } else {
      displayDiv.classList.add('hidden');
      editDiv.classList.remove('hidden');
      const input = document.getElementById(`input-location-${id}`);
      if (input) input.focus();
    }
  }
};

window.saveBinLocation = async function(id, currentLocation) {
  const input = document.getElementById(`input-location-${id}`);
  if (!input) return;
  
  const newLocation = input.value.trim();
  
  if (newLocation !== "" && newLocation !== currentLocation) {
    try {
      const response = await fetch(`${API_BASE_URL}/api/edit-bin`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, location: newLocation })
      });
      if (response.ok) {
        showNotification(`Location for ${id} updated!`, 'success');
        toggleEditBinLocation(id); // Go back to display mode
        fetchLiveData(); // Refresh data immediately to reflect on screen
      } else {
        showNotification(`Failed to update location for ${id}`, 'error');
        toggleEditBinLocation(id);
      }
    } catch (error) {
      console.error("Error updating bin:", error);
      showNotification(`Network error while updating bin`, 'error');
      toggleEditBinLocation(id);
    }
  } else {
    // Revert if empty or unchanged
    toggleEditBinLocation(id);
  }
};

window.toggleSwitch = function (element) {
  element.classList.toggle("active");
};

window.saveSettings = function () {
  const freqValue = document.getElementById('notificationFrequencyValue');
  if (freqValue) localStorage.setItem('ecosync_notif_freq_value', freqValue.value);
  
  const freqUnit = document.getElementById('notificationFrequencyUnit');
  if (freqUnit) localStorage.setItem('ecosync_notif_freq_unit', freqUnit.value);
  
  const email = document.getElementById('notificationEmail');
  if (email) localStorage.setItem('ecosync_notif_email', email.value);

  alert("Settings saved successfully!");
};

function setupEventListeners() {
  const binSearch = document.getElementById("binSearch");
  if (binSearch) binSearch.addEventListener("input", filterBins);

  const statusFilter = document.getElementById("statusFilter");
  if (statusFilter) statusFilter.addEventListener("change", filterBins);

  const historySearch = document.getElementById("historySearch");
  if (historySearch) historySearch.addEventListener("input", filterHistory);

  const startDate = document.getElementById("startDate");
  if (startDate) startDate.addEventListener("change", filterHistory);

  const endDate = document.getElementById("endDate");
  if (endDate) endDate.addEventListener("change", filterHistory);

  const clearDates = document.getElementById("clearDates");
  if (clearDates) {
    clearDates.addEventListener("click", () => {
      if (startDate) startDate.value = "";
      if (endDate) endDate.value = "";
      filterHistory();
    });
  }
}

document.addEventListener("DOMContentLoaded", () => {
  // --- 1. SIDEBAR TOGGLE LOGIC ---
  const sidebar = document.getElementById("sidebar");
  const mainContent = document.getElementById("mainContent");
  const toggleBtn = document.getElementById("toggleSidebar");

  // Note: Initial state is now handled synchronously in the PHP files
  // to completely prevent the animation flash on page load.

  if (toggleBtn && sidebar) {
    toggleBtn.addEventListener("click", () => {
      const isNowCollapsed = sidebar.classList.toggle("collapsed");
      localStorage.setItem("ecosync_sidebar_collapsed", isNowCollapsed);
    });
  }

  // --- 2. UPDATE SIDEBAR NAME ---
  const session = localStorage.getItem("ecosync_session");
  if (session) {
    const user = JSON.parse(session);
    const nameElements = document.querySelectorAll(".nav-label p.font-bold");
    if (nameElements.length > 0) nameElements[0].textContent = user.name;
  }

  // --- NOTIFICATION BELL LOGIC ---
  const bellBtn = document.getElementById("notificationBellBtn");
  const notifDropdown = document.getElementById("notificationDropdown");

  if (bellBtn && notifDropdown) {
    bellBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      notifDropdown.classList.toggle("hidden");
    });

    document.addEventListener("click", (e) => {
      if (!notifDropdown.contains(e.target) && !bellBtn.contains(e.target)) {
        notifDropdown.classList.add("hidden");
      }
    });
  }

  // --- 3. STANDARD DASHBOARD LOGIC ---
  setupEventListeners();

  // Migrate old settings if they exist
  if (localStorage.getItem('ecosync_notif_freq') && !localStorage.getItem('ecosync_notif_freq_value')) {
    localStorage.setItem('ecosync_notif_freq_value', localStorage.getItem('ecosync_notif_freq'));
    localStorage.setItem('ecosync_notif_freq_unit', 'hours');
    localStorage.removeItem('ecosync_notif_freq');
  }

  // Load settings if on settings page
  const notifFreqValue = document.getElementById('notificationFrequencyValue');
  if (notifFreqValue) notifFreqValue.value = localStorage.getItem('ecosync_notif_freq_value') || "0";
  const notifFreqUnit = document.getElementById('notificationFrequencyUnit');
  if (notifFreqUnit) notifFreqUnit.value = localStorage.getItem('ecosync_notif_freq_unit') || "hours";
  
  const notifEmail = document.getElementById('notificationEmail');
  if (notifEmail) notifEmail.value = localStorage.getItem('ecosync_notif_email') || "";

  const path = window.location.pathname;
  const page = path.split("/").pop().split("?")[0];

  // Instantly render the cached history table so the page doesn't look empty while loading
  if (page === "collection-history.php") {
    filterHistory();
  }

  fetchCollectionHistory();
  fetchLiveData();
  setInterval(fetchLiveData, 2000);

  // --- 4. LOGOUT LOGIC ---
  const logoutBtn = document.getElementById("logoutBtn");
  if (logoutBtn) {
    logoutBtn.addEventListener("click", (e) => {
      e.preventDefault();
      localStorage.removeItem("ecosync_session");
      window.location.replace("login.php");
    });
  }
});
