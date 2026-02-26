/**
 * Serial Validator - Admin Dashboard Scripts
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        
        // Initialize chart if canvas exists
        if ($('#sv-verification-chart').length) {
            initChart();
        }
        
        // Period toggle buttons
        $('.sv-period-btn').on('click', function() {
            const period = $(this).data('period');
            
            $('.sv-period-btn').removeClass('active');
            $(this).addClass('active');
            
            updateStats(period);
        });
    });
    
    /**
     * Initialize Chart.js chart
     */
    function initChart() {
        const canvas = document.getElementById('sv-verification-chart');
        const labels = JSON.parse(canvas.getAttribute('data-labels'));
        const values = JSON.parse(canvas.getAttribute('data-values'));
        
        const ctx = canvas.getContext('2d');
        
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Verifications',
                    data: values,
                    borderColor: '#2271b1',
                    backgroundColor: 'rgba(34, 113, 177, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });
        
        // Store chart instance for later updates
        canvas.chartInstance = chart;
    }
    
    /**
     * Update statistics based on period
     */
    function updateStats(period) {
        const $container = $('.sv-stats-cards');
        const stats7 = JSON.parse($container.attr('data-stats-7'));
        const stats30 = JSON.parse($container.attr('data-stats-30'));
        
        const stats = period === 7 ? stats7 : stats30;
        
        // Update stat values with animation
        $('.sv-stat-total .sv-stat-value').text(stats.total);
        $('.sv-stat-valid .sv-stat-value').text(stats.valid);
        $('.sv-stat-invalid .sv-stat-value').text(stats.invalid);
        $('.sv-stat-used .sv-stat-value').text(stats.used);
        $('.sv-stat-blocked .sv-stat-value').text(stats.blocked);
        
        // Animate the values
        $('.sv-stat-card').each(function() {
            $(this).addClass('sv-stat-updated');
            setTimeout(() => {
                $(this).removeClass('sv-stat-updated');
            }, 300);
        });
    }

})(jQuery);
