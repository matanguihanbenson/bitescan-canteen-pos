import "./bootstrap";
import "laravel-datatables-vite";

(function () {
    var sidenavState = localStorage.getItem("sidenavState") || "collapsed";
    var sidenav = document.getElementById("mySidenav");

    if (sidenavState === "expanded") {
        sidenav.classList.add("expanded");
    } else {
        sidenav.classList.add("collapsed");
    }
})();

// Make toggleNav globally accessible
window.toggleNav = function() {
    var sidenav = document.getElementById("mySidenav");
    var mainContent = document.getElementById("mainContent");
    sidenav.classList.toggle("collapsed");
    sidenav.classList.toggle("expanded");
    if (sidenav.classList.contains("expanded")) {
        mainContent.style.marginLeft = "250px";
        mainContent.style.width = "calc(100% - 250px)";
        localStorage.setItem("sidenavState", "expanded");
    } else {
        mainContent.style.marginLeft = "80px";
        mainContent.style.width = "calc(100% - 80px)";
        localStorage.setItem("sidenavState", "collapsed");
    }
}

// Make toggleSubNav globally accessible
window.toggleSubNav = function(subNavId, element) {
    var subNav = document.getElementById(subNavId);
    var allSubNavs = document.querySelectorAll(".subnav");
    var allNavLinks = document.querySelectorAll(".sidenav-link");

    // Remove active class from all trigger links
    allNavLinks.forEach(function (link) {
        if (link !== element) {
            link.classList.remove("active");
        }
    });

    // Close other subnavs
    allSubNavs.forEach(function (nav) {
        if (nav !== subNav) {
            nav.classList.remove("expanded");
            nav.classList.add("collapsed");
        }
    });

    // Toggle current subnav
    subNav.classList.toggle("collapsed");
    subNav.classList.toggle("expanded");

    // Toggle active class on the trigger link
    element.classList.toggle("active");
}

// Add click handler for sidenav links
document.querySelectorAll(".sidenav-link").forEach(function (link) {
    link.addEventListener("click", function (e) {
        // Prevent subnav links from triggering this
        if (
            this.nextElementSibling &&
            this.nextElementSibling.classList.contains("subnav")
        ) {
            return;
        }

        // Remove active class from all links
        document.querySelectorAll(".sidenav-link").forEach(function (l) {
            l.classList.remove("active");
        });
        // Add active class to the clicked link
        this.classList.add("active");
    });
});

// Initialize Bootstrap tooltips
var tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
);
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});

// Restore sidenav state from local storage
document.addEventListener("DOMContentLoaded", function () {
    var sidenav = document.getElementById("mySidenav");
    var mainContent = document.getElementById("mainContent");
    var sidenavState = localStorage.getItem("sidenavState") || "collapsed";

    if (sidenavState === "expanded") {
        sidenav.classList.remove("collapsed");
        sidenav.classList.add("expanded");
        mainContent.style.marginLeft = "250px";
        mainContent.style.width = "calc(100% - 250px)";
    } else {
        sidenav.classList.remove("expanded");
        sidenav.classList.add("collapsed");
        mainContent.style.marginLeft = "80px";
        mainContent.style.width = "calc(100% - 80px)";
    }
});
