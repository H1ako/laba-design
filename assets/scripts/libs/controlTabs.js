const tabsControlEls = document.querySelectorAll("[data-tabs-control]");
const tabsContentEls = document.querySelectorAll("[data-tabs-id]");

function initControlTabs(onChange = function () {}) {
  const tabsControlEls = document.querySelectorAll("[data-tabs-control]");
  const tabsContentEls = document.querySelectorAll("[data-tabs-id]");

  tabsControlEls.forEach((tabsControlEl) => {
    tabsControlEl.addEventListener("change", (e) => {
      tabsControlUpdateState(e.target);
      onChange(e.target);
    });
  });
  
  setDefaultTabs(tabsContentEls, onChange);
}

function setDefaultTabs(contentEls, onChange) {
  contentEls.forEach((tabsContentEl) => {
    const controlId = tabsContentEl.dataset.tabsId;
    var currentTab = tabsContentEl.dataset.tabsState;

    if (localStorage.getItem(`tabState_${controlId}`)) {
      currentTab = localStorage.getItem(`tabState_${controlId}`);
    }

    const tabsControlEl = document.querySelector(
      `[data-tabs-control="${controlId}"][value="${currentTab}"]`
    );
    if (!tabsControlEl) return;

    tabsControlEl.checked = true;
    tabsContentEl.dataset.tabsState = currentTab;
    onChange(tabsControlEl);
  });
}

function tabsControlUpdateState(tabsControlEl) {
  const newTab = tabsControlEl.value;
  const controlId = tabsControlEl.dataset.tabsControl;

  const tabsContentEl = document.querySelector(`[data-tabs-id="${controlId}"]`);
  if (!tabsContentEl) return;

  localStorage.setItem(`tabState_${controlId}`, newTab);
  tabsContentEl.dataset.tabsState = newTab;
}
