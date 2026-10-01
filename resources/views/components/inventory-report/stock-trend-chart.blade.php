<div class="card shadow">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold">Stock Level Trends</h6>
        <div class="btn-group period-selector">
            <button type="button" class="btn btn-sm btn-outline-secondary" data-period="daily">Daily</button>
            <button type="button" class="btn btn-sm btn-outline-secondary active" data-period="weekly">Weekly</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" data-period="monthly">Monthly</button>
        </div>
    </div>
    <div class="card-body">
        <div style="height: 300px;" id="stockTrendChartContainer">
            <div class="text-center py-3 d-none" id="stockTrendLoading">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only"></span>
                </div>
            </div>
            <div class="alert alert-danger d-none" id="stockTrendError">
                <p class="mb-0">Error loading stock trend data</p>
                <button class="btn btn-sm btn-outline-danger mt-2" onclick="loadStockTrendData()">Retry</button>
            </div>
            <canvas id="stockTrendChart"></canvas>
        </div>
    </div>
</div>
