<x-app-layout>
    <div class="container mx-auto px-6 py-4">
        <h1 class="text-2xl font-bold text-center mb-6">📊 出席分析グラフ</h1>

        <div class="flex justify-end mb-4">
            <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded shadow">
                🖨 印刷
            </button>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <canvas id="attendanceChart" width="1000" height="400"></canvas>
        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const ctx = document.getElementById('attendanceChart').getContext('2d');
        const attendanceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($dates) !!},
                datasets: [
                    {
                        label: 'オンライン',
                        data: {!! json_encode($onlineCounts) !!},
                        backgroundColor: 'rgba(34, 197, 94, 0.7)', // green
                        borderColor: 'rgba(34, 197, 94, 1)',
                        borderWidth: 1,
                        stack: 'Stack 0'
                    },
                    {
                        label: '対面（午前）',
                        data: {!! json_encode($physicalMorningCounts) !!},
                        backgroundColor: 'rgba(59, 130, 246, 0.7)', // blue
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1,
                        stack: 'Stack 1'
                    },
                    {
                        label: '対面（午後）',
                        data: {!! json_encode($physicalAfternoonCounts) !!},
                        backgroundColor: 'rgba(251, 146, 60, 0.7)', // purple
                        borderColor: 'rgba(139, 92, 246, 1)',
                        borderWidth: 1,
                        stack: 'Stack 1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: '日別出席者数（午前・午後別 / 重複なし）'
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        title: {
                            display: true,
                            text: '日付'
                        }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: '人数'
                        },
                        stepSize: 1
                    }
                }
            }
        });
    </script>

    <style>
        @media print {
            @page {
                size: A4 landscape;
                margin: 20mm;
            }
            body {
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</x-app-layout>

<!-- Tailwind CDN（開発用） -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@tailwindcss/line-clamp@latest"></script>
