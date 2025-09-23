// Dashboard specific JavaScript
document.addEventListener("DOMContentLoaded", function () {
  // Add some interactive animations
  const statCards = document.querySelectorAll(".stat-card");

  statCards.forEach((card) => {
    card.addEventListener("mouseenter", function () {
      this.style.transform = "translateY(-5px) scale(1.02)";
    });

    card.addEventListener("mouseleave", function () {
      this.style.transform = "translateY(0) scale(1)";
    });
  });

  // Auto-refresh data every 5 minutes
  setInterval(function () {
    // You can add AJAX calls here to refresh statistics
    console.log("Dashboard data refresh...");
  }, 300000);
});
