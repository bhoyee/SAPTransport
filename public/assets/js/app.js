'use strict';

/* ===== Enable Bootstrap Popover (on element  ====== */
const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));

/* ==== Enable Bootstrap Alert ====== */
const alertList = document.querySelectorAll('.alert');
const alerts = [...alertList].map(element => new bootstrap.Alert(element));

/* ===== Responsive Sidepanel ====== */
const sidePanelToggler = document.getElementById('sidepanel-toggler');
const sidePanel = document.getElementById('app-sidepanel');
const sidePanelDrop = document.getElementById('sidepanel-drop');
const sidePanelClose = document.getElementById('sidepanel-close');

// Ensure elements exist before adding event listeners
if (sidePanelToggler && sidePanel && sidePanelDrop && sidePanelClose) {
    // Add event listeners for side panel
    window.addEventListener('load', function(){
        responsiveSidePanel();
    });

    window.addEventListener('resize', function(){
        responsiveSidePanel();
    });

    function responsiveSidePanel() {
        let w = window.innerWidth;
        if(w >= 1200) {
            // If larger
            sidePanel.classList.remove('sidepanel-hidden');
            sidePanel.classList.add('sidepanel-visible');
        } else {
            // If smaller
            sidePanel.classList.remove('sidepanel-visible');
            sidePanel.classList.add('sidepanel-hidden');
        }
    }

    // Toggle side panel visibility
    sidePanelToggler.addEventListener('click', () => {
        if (sidePanel.classList.contains('sidepanel-visible')) {
            console.log('visible');
            sidePanel.classList.remove('sidepanel-visible');
            sidePanel.classList.add('sidepanel-hidden');
        } else {
            console.log('hidden');
            sidePanel.classList.remove('sidepanel-hidden');
            sidePanel.classList.add('sidepanel-visible');
        }
    });

    // Close side panel when clicking the close button
    sidePanelClose.addEventListener('click', (e) => {
        e.preventDefault();
        sidePanelToggler.click();
    });

    // Close side panel when clicking outside the panel (drop area)
    sidePanelDrop.addEventListener('click', (e) => {
        sidePanelToggler.click();
    });
}

/* ====== Mobile search ======= */
const searchMobileTrigger = document.querySelector('.search-mobile-trigger');
const searchBox = document.querySelector('.app-search-box');

// Ensure mobile search elements exist before adding event listener
if (searchMobileTrigger && searchBox) {
    searchMobileTrigger.addEventListener('click', () => {
        searchBox.classList.toggle('is-visible');

        let searchMobileTriggerIcon = document.querySelector('.search-mobile-trigger-icon');

        if (searchMobileTriggerIcon.classList.contains('fa-magnifying-glass')) {
            searchMobileTriggerIcon.classList.remove('fa-magnifying-glass');
            searchMobileTriggerIcon.classList.add('fa-xmark');
        } else {
            searchMobileTriggerIcon.classList.remove('fa-xmark');
            searchMobileTriggerIcon.classList.add('fa-magnifying-glass');
        }
    });
}