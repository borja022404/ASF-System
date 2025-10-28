@extends('layouts.Admin.app')

@section('content')
    <div class="main-content">
        <div class="row">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h2 class="mb-4 text-center text-md-start">Dashboard Analytics</h2>
                <button id="downloadPdf" class="btn btn-danger">
                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> Download PDF
                </button>
            </div>
        </div>

        {{-- Risk and Status Charts --}}
        <div class="row g-4 mb-4 justify-content-center">
            <div class="col-lg-6 col-md-12">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light border-bottom">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-pie-chart-fill me-2"></i>Reports by Risk Level</h6>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center p-3">
                        <canvas id="riskChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-12">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light border-bottom">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-bar-chart-fill me-2"></i>Reports by Status</h6>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center p-3">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Trends Chart --}}
        <div class="row g-4">
            <div class="col-12">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light border-bottom">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-graph-up me-2"></i>Monthly Report Trends</h6>
                    </div>
                    <div class="card-body p-3">
                        <canvas id="trendsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden content for PDF generation --}}
    <div id="pdfContent" style="display: none;">
        <div style="padding: 20px; font-family: Arial, sans-serif;">
            {{-- PDF Header with Logo --}}
            <div style="text-align: center; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 3px solid #198754;">
                <img src="{{ asset('/images/vitarich-logo.png') }}" alt="Vitarich Logo"
                    style="height: 80px; margin-bottom: 15px;">
                <h1 style="color: #198754; margin: 10px 0; font-size: 28px; font-weight: bold;">ASF REPORT ANALYTICS</h1>
                <p
                    style="color: #666; margin: 5px 0; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
                    Smart ASF Disease Detection Monitoring System
                </p>
                <p style="color: #999; margin: 10px 0 0 0; font-size: 11px;">Generated on: <span id="reportDate"></span></p>
            </div>

            {{-- Monthly Reports by Risk Level Table --}}
            <h3 style="color: #333; margin-bottom: 15px; border-bottom: 2px solid #dc3545; padding-bottom: 5px;">
                Monthly Reports by Risk Level
            </h3>
            <table id="riskTable" style="width: 100%; border-collapse: collapse; margin-bottom: 40px;">
                <thead>
                    <tr style="background-color: #dc3545; color: white;">
                        <th style="border: 1px solid #ddd; padding: 12px; text-align: left;">Month</th>
                        <th style="border: 1px solid #ddd; padding: 12px; text-align: center;">Low Risk</th>
                        <th style="border: 1px solid #ddd; padding: 12px; text-align: center;">Medium Risk</th>
                        <th style="border: 1px solid #ddd; padding: 12px; text-align: center;">High Risk</th>
                        <th style="border: 1px solid #ddd; padding: 12px; text-align: center; font-weight: bold;">Total
                            Cases</th>
                    </tr>
                </thead>
                <tbody id="riskTableBody">
                    {{-- Data will be populated by JavaScript --}}
                </tbody>
                <tfoot>
                    <tr style="background-color: #f8f9fa; font-weight: bold;">
                        <td style="border: 1px solid #ddd; padding: 12px;">TOTAL</td>
                        <td id="totalLow" style="border: 1px solid #ddd; padding: 12px; text-align: center;"></td>
                        <td id="totalMedium" style="border: 1px solid #ddd; padding: 12px; text-align: center;"></td>
                        <td id="totalHigh" style="border: 1px solid #ddd; padding: 12px; text-align: center;"></td>
                        <td id="totalRisk"
                            style="border: 1px solid #ddd; padding: 12px; text-align: center; background-color: #dc3545; color: white;">
                        </td>
                    </tr>
                </tfoot>
            </table>

            {{-- Monthly Reports by Status Table --}}
            <h3 style="color: #333; margin-bottom: 15px; border-bottom: 2px solid #0d6efd; padding-bottom: 5px;">
                Monthly Reports by Status
            </h3>
            <table id="statusTable" style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <thead>
                    <tr style="background-color: #0d6efd; color: white;">
                        <th style="border: 1px solid #ddd; padding: 12px; text-align: left;">Month</th>
                        <th style="border: 1px solid #ddd; padding: 12px; text-align: center;">Submitted</th>
                        <th style="border: 1px solid #ddd; padding: 12px; text-align: center;">For Inspection</th>
                        <th style="border: 1px solid #ddd; padding: 12px; text-align: center;">Resolved</th>
                        <th style="border: 1px solid #ddd; padding: 12px; text-align: center; font-weight: bold;">Total
                            Cases</th>
                    </tr>
                </thead>
                <tbody id="statusTableBody">
                    {{-- Data will be populated by JavaScript --}}
                </tbody>
                <tfoot>
                    <tr style="background-color: #f8f9fa; font-weight: bold;">
                        <td style="border: 1px solid #ddd; padding: 12px;">TOTAL</td>
                        <td id="totalSubmitted" style="border: 1px solid #ddd; padding: 12px; text-align: center;"></td>
                        <td id="totalInspection" style="border: 1px solid #ddd; padding: 12px; text-align: center;"></td>
                        <td id="totalResolved" style="border: 1px solid #ddd; padding: 12px; text-align: center;"></td>
                        <td id="totalStatus"
                            style="border: 1px solid #ddd; padding: 12px; text-align: center; background-color: #0d6efd; color: white;">
                        </td>
                    </tr>
                </tfoot>
            </table>

            {{-- PDF Footer with Watermark --}}
            <div
                style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 2px solid #e9ecef; font-size: 10px; color: #666; font-style: italic;">
                <span
                    style="display: inline-block; width: 14px; height: 14px; background-color: #198754; border-radius: 50%; margin-right: 8px; vertical-align: middle;"></span>
                This report is generated by SMART ASF DISEASE DETECTION AND MONITORING SYSTEM
            </div>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {{-- Include required libraries for PDF --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Data from server-side
            const riskCounts = @json($riskCounts);
            const submitted = @json($submittedCount);
            const forInspection = @json($forInspectionCount);
            const resolved = @json($resolvedCount);
            const months = @json($months);
            const monthlyData = @json($monthlyCounts);
            const riskPercentages = @json($riskPercentages);


            // Monthly breakdown data (you'll need to pass this from controller)
            const monthlyRiskData = @json($monthlyRiskData ?? []);
            const monthlyStatusData = @json($monthlyStatusData ?? []);

            // Common chart options
            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: '#6c757d'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                        titleColor: '#fff',
                        bodyColor: '#fff'
                    }
                }
            };

            // Risk Chart
            // Risk Chart
            new Chart(document.getElementById('riskChart'), {
                type: 'doughnut',
                data: {
                    labels: [
                        `Low Risk (${riskPercentages.low}%)`,
                        `Medium Risk (${riskPercentages.medium}%)`,
                        `High Risk (${riskPercentages.high}%)`
                    ],
                    datasets: [{
                        data: [riskCounts.low, riskCounts.medium, riskCounts.high],
                        backgroundColor: ['#198754', '#ffc107', '#dc3545'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    ...commonOptions,
                    plugins: {
                        ...commonOptions.plugins,
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#6c757d',
                                font: { weight: 'bold' }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const index = context.dataIndex;
                                    const percentage = [riskPercentages.low, riskPercentages.medium, riskPercentages.high][index];
                                    return `${context.label.split('(')[0].trim()}: ${context.formattedValue} (${percentage}%)`;
                                }
                            }
                        },
                        datalabels: {
                            color: '#fff',
                            formatter: function (value, context) {
                                const index = context.dataIndex;
                                const percentage = [riskPercentages.low, riskPercentages.medium, riskPercentages.high][index];
                                return `${percentage}%`;
                            },
                            font: {
                                weight: 'bold',
                                size: 14
                            }
                        }
                    }
                }
            });


            // Status Chart
            new Chart(document.getElementById('statusChart'), {
                type: 'bar',
                data: {
                    labels: ['Submitted', 'For Inspection', 'Resolved'],
                    datasets: [{
                        label: 'Number of Reports',
                        data: [submitted, forInspection, resolved],
                        backgroundColor: ['#6c757d', '#0dcaf0', '#198754'],
                        borderRadius: 5
                    }]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                color: '#6c757d'
                            },
                            grid: {
                                color: '#e9ecef'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#6c757d'
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // Trends Chart
            new Chart(document.getElementById('trendsChart'), {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Reports Submitted',
                        data: monthlyData,
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                color: '#6c757d'
                            },
                            grid: {
                                color: '#e9ecef'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#6c757d'
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // Populate PDF tables with data
            function populateTables() {
                const riskTableBody = document.getElementById('riskTableBody');
                const statusTableBody = document.getElementById('statusTableBody');

                let totalLow = 0,
                    totalMedium = 0,
                    totalHigh = 0;
                let totalSubmitted = 0,
                    totalInspection = 0,
                    totalResolved = 0;

                // Populate Risk Level Table
                months.forEach((month, index) => {
                    const monthData = monthlyRiskData[index] || {
                        low: 0,
                        medium: 0,
                        high: 0
                    };
                    const monthTotal = monthData.low + monthData.medium + monthData.high;

                    totalLow += monthData.low;
                    totalMedium += monthData.medium;
                    totalHigh += monthData.high;

                    const row = `
                                <tr style="background-color: ${index % 2 === 0 ? '#ffffff' : '#f8f9fa'}">
                                    <td style="border: 1px solid #ddd; padding: 10px;">${month}</td>
                                    <td style="border: 1px solid #ddd; padding: 10px; text-align: center;">${monthData.low}</td>
                                    <td style="border: 1px solid #ddd; padding: 10px; text-align: center;">${monthData.medium}</td>
                                    <td style="border: 1px solid #ddd; padding: 10px; text-align: center;">${monthData.high}</td>
                                    <td style="border: 1px solid #ddd; padding: 10px; text-align: center; font-weight: bold;">${monthTotal}</td>
                                </tr>
                            `;
                    riskTableBody.innerHTML += row;
                });

                // Populate Status Table
                months.forEach((month, index) => {
                    const monthData = monthlyStatusData[index] || {
                        submitted: 0,
                        inspection: 0,
                        resolved: 0
                    };
                    const monthTotal = monthData.submitted + monthData.inspection + monthData.resolved;

                    totalSubmitted += monthData.submitted;
                    totalInspection += monthData.inspection;
                    totalResolved += monthData.resolved;

                    const row = `
                                <tr style="background-color: ${index % 2 === 0 ? '#ffffff' : '#f8f9fa'}">
                                    <td style="border: 1px solid #ddd; padding: 10px;">${month}</td>
                                    <td style="border: 1px solid #ddd; padding: 10px; text-align: center;">${monthData.submitted}</td>
                                    <td style="border: 1px solid #ddd; padding: 10px; text-align: center;">${monthData.inspection}</td>
                                    <td style="border: 1px solid #ddd; padding: 10px; text-align: center;">${monthData.resolved}</td>
                                    <td style="border: 1px solid #ddd; padding: 10px; text-align: center; font-weight: bold;">${monthTotal}</td>
                                </tr>
                            `;
                    statusTableBody.innerHTML += row;
                });

                // Update totals for Risk Level
                document.getElementById('totalLow').textContent = totalLow;
                document.getElementById('totalMedium').textContent = totalMedium;
                document.getElementById('totalHigh').textContent = totalHigh;
                document.getElementById('totalRisk').textContent = totalLow + totalMedium + totalHigh;

                // Update totals for Status
                document.getElementById('totalSubmitted').textContent = totalSubmitted;
                document.getElementById('totalInspection').textContent = totalInspection;
                document.getElementById('totalResolved').textContent = totalResolved;
                document.getElementById('totalStatus').textContent = totalSubmitted + totalInspection +
                    totalResolved;

                // Set report date
                document.getElementById('reportDate').textContent = new Date().toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }

            populateTables();
        });

        // PDF Download - Outside DOMContentLoaded to ensure it's accessible
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById("downloadPdf").addEventListener("click", function (e) {
                e.preventDefault();
                console.log('Download button clicked');

                // Show loading state
                const btn = this;
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Generating...';
                btn.disabled = true;

                // Make pdfContent visible temporarily
                const pdfContent = document.getElementById("pdfContent");
                console.log('PDF Content element:', pdfContent);

                pdfContent.style.display = 'block';
                pdfContent.style.position = 'absolute';
                pdfContent.style.left = '-9999px';
                pdfContent.style.top = '0';

                setTimeout(() => {
                    console.log('Starting html2canvas...');

                    html2canvas(pdfContent, {
                        scale: 2,
                        useCORS: true,
                        logging: true,
                        backgroundColor: '#ffffff',
                        windowWidth: 1200
                    }).then(canvas => {
                        console.log('Canvas created, generating PDF...');

                        const {
                            jsPDF
                        } = window.jspdf;
                        const pdf = new jsPDF("p", "mm", "a4");
                        const imgData = canvas.toDataURL("image/png");

                        const pdfWidth = pdf.internal.pageSize.getWidth();
                        const pdfHeight = pdf.internal.pageSize.getHeight();
                        const imgWidth = pdfWidth;
                        const imgHeight = (canvas.height * pdfWidth) / canvas.width;

                        let heightLeft = imgHeight;
                        let position = 0;

                        pdf.addImage(imgData, "PNG", 0, position, imgWidth, imgHeight);
                        heightLeft -= pdfHeight;

                        while (heightLeft > 0) {
                            position = heightLeft - imgHeight;
                            pdf.addPage();
                            pdf.addImage(imgData, "PNG", 0, position, imgWidth, imgHeight);
                            heightLeft -= pdfHeight;
                        }

                        const filename = "asf-report-analytics-" + new Date().toISOString()
                            .slice(0, 10) + ".pdf";
                        console.log('Saving PDF as:', filename);
                        pdf.save(filename);

                        // Hide pdfContent again
                        pdfContent.style.display = 'none';
                        pdfContent.style.position = '';
                        pdfContent.style.left = '';
                        pdfContent.style.top = '';

                        // Reset button
                        btn.innerHTML = originalText;
                        btn.disabled = false;

                        console.log('PDF generated successfully!');
                    }).catch(error => {
                        console.error('PDF generation error:', error);
                        alert('Error generating PDF: ' + error.message);

                        // Hide pdfContent and reset button
                        pdfContent.style.display = 'none';
                        pdfContent.style.position = '';
                        pdfContent.style.left = '';
                        pdfContent.style.top = '';
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    });
                }, 500);
            });
        });
    </script>
@endsection